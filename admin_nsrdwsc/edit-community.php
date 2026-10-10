<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

// Nothing below runs unless someone is signed in.
if (empty($_SESSION['email_id'])) {
  exit;
}

$logger = new Logger($conn);

if (empty($_SESSION['admin_csrf'])) {
  $_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
}

// This file is used for both adding and editing: this copy changes a community (add-community.php adds one).
$editing = true;

function commf_e($text)
{
  return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function commf_query($conn, $sql, array $params = [])
{
  $stmt = mysqli_prepare($conn, $sql);
  if (!$stmt) {
    error_log('Community form query failed: ' . mysqli_error($conn));
    return false;
  }
  if ($params) {
    $types = '';
    foreach ($params as $param) {
      $types .= is_int($param) ? 'i' : 's';
    }
    mysqli_stmt_bind_param($stmt, $types, ...$params);
  }
  if (!mysqli_stmt_execute($stmt)) {
    error_log('Community form query failed: ' . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    return false;
  }
  $result = mysqli_stmt_get_result($stmt);
  mysqli_stmt_close($stmt);
  if ($result === false) {
    return true;
  }
  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }
  return $rows;
}

// "Dubai Marina" -> "dubai-marina"
function commf_slug($text)
{
  $text = strtolower(trim((string) $text));
  $text = preg_replace('/[^a-z0-9]+/', '-', $text);
  return trim($text, '-');
}

// Pictures are checked by extension and by their real content, and are stored under a
// name made here, never the name they arrived with.
const COMMF_IMAGE = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
const COMMF_IMAGE_MAX = 5 * 1024 * 1024;

// Checks one uploaded picture. Returns [extension, error]; extension is '' when no file was chosen.
function commf_check_upload($file, $label)
{
  if (!is_array($file) || is_array($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE || (string) $file['name'] === '') {
    return ['', ''];
  }
  if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > COMMF_IMAGE_MAX) {
    return ['', "$label is too large (maximum 5 MB)."];
  }
  if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
    return ['', "$label could not be uploaded. Please try again."];
  }
  $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $real_extension = array_search($finfo->file($file['tmp_name']), ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'], true);
  if (!isset(COMMF_IMAGE[$extension]) || $real_extension === false) {
    return ['', "$label must be one of: JPG, PNG, GIF, WEBP."];
  }
  return [$real_extension, ''];
}

// Shows the picture a field already has.
function commf_current($folder, $name)
{
  $name = trim((string) $name);
  if ($name === '') {
    return;
  }
  $url = BASE_URL . 'assets/media/community/' . $folder . rawurlencode($name);
  echo '<div class="dre-current"><a href="' . commf_e($url) . '" target="_blank" rel="noopener"><img src="' . commf_e($url) . '" alt="" loading="lazy"></a><span>Current picture. Choose a new one to replace it.</span></div>';
}

// Pictures: field => [folder inside assets/media/community/, name prefix, label]
$image_fields = [
  'comm_thumb_img' => ['thumb/', 'comm_thumb_img', 'Card image'],
  'comm_inner_img' => ['inner/', 'comm_inner_img', 'Inner image'],
  'comm_inner_thumb_img' => ['inner-thumb/', 'comm_inner_thumb_img', 'Inner thumbnail'],
  'comm_loc_main_img' => ['location/', 'comm_loc_main_img', 'Location image'],
  'comm_home_img' => ['home/', 'comm_home_img', 'Homepage card image'],
];
// Text fields: name => longest length kept (0 = no limit).
$text_fields = [
  'comm_heading' => 255, 'comm_location' => 255, 'comm_thumb_img_alt' => 255, 'comm_inner_thumb_img_alt' => 255, 'comm_long_descp' => 0,
  'comm_address' => 255, 'comm_phoneno' => 255, 'comm_email' => 255, 'comm_loc_main_heading' => 255, 'comm_loc_main_descp' => 0,
  'comm_map_heading' => 255, 'comm_map_iframe' => 0, 'comm_posted_on' => 20, 'comm_slug' => 200, 'meta_title' => 255, 'meta_keyword' => 255,
  'meta_description' => 0, 'meta_tags' => 0, 'comm_home_card_content' => 0,
];
// Icons for nearby places: Font Awesome name => label.
$nearby_icons = [
  'fa-landmark' => 'Landmark', 'fa-plane' => 'Airport', 'fa-plane-departure' => 'Airport (departure)', 'fa-city' => 'City centre', 'fa-building' => 'Business district',
  'fa-water' => 'Waterfront', 'fa-umbrella-beach' => 'Beach', 'fa-shopping-bag' => 'Shopping', 'fa-golf-ball' => 'Golf', 'fa-futbol' => 'Sports',
  'fa-tree' => 'Park', 'fa-bicycle' => 'Cycling', 'fa-hotel' => 'Hotel', 'fa-utensils' => 'Dining', 'fa-hospital' => 'Hospital', 'fa-school' => 'School',
  'fa-university' => 'University', 'fa-mosque' => 'Mosque', 'fa-train' => 'Train', 'fa-subway' => 'Metro', 'fa-bus' => 'Bus', 'fa-car' => 'Car',
  'fa-road' => 'Main road', 'fa-ship' => 'Marina', 'fa-archway' => 'Gate', 'fa-globe' => 'International', 'fa-home' => 'Home', 'fa-map-marker-alt' => 'Other place',
];
// The icon name inside a saved value such as "fa fa-landmark" or "fa-solid fa-location-dot".
function commf_icon_name($value)
{
  preg_match_all('/fa-[a-z0-9-]+/', (string) $value, $found);
  foreach ($found[0] as $name) {
    if (!in_array($name, ['fa-solid', 'fa-regular', 'fa-brands', 'fa-light'], true)) {
      return $name;
    }
  }
  return '';
}

$id = 0;
$details = null;
if ($editing) {
  $id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
  $details = $id > 0 ? (commf_query($conn, "SELECT * FROM tbl_community WHERE comm_id = ?", [$id]) ?: []) : [];
  $details = $details ? $details[0] : null;
}
$can_show_form = !$editing || $details;

$errors = [];
$old = ['is_active' => '1', 'comm_showonhomepage' => '', 'comm_featured' => '', 'comm_posted_on' => date('Y-m-d')];
foreach ($text_fields as $field => $max) {
  $old += [$field => ''];
}
$project_count = 0;
$highlight_count = 0;
$saved_nearby = [];   // comm_map_id => row, as saved
$nearby_rows = [];    // rows shown in the form: [id, icon name, place, distance]
$highlight_rows = []; // rows shown in the add form: [heading, text]
if ($details) {
  // The form starts from what is saved.
  foreach ($text_fields as $field => $max) {
    $old[$field] = (string) ($details[$field] ?? '');
  }
  $posted_time = strtotime((string) $details['comm_posted_on']);
  $old['comm_posted_on'] = $posted_time ? date('Y-m-d', $posted_time) : '';
  foreach (['is_active', 'comm_showonhomepage', 'comm_featured'] as $flag) {
    $old[$flag] = $details[$flag] === '1' ? '1' : '';
  }
  $counted = commf_query($conn, "SELECT COUNT(*) AS n FROM tbl_proj WHERE prj_comm_id = ?", [$id]);
  $project_count = $counted ? (int) $counted[0]['n'] : 0;
  $counted = commf_query($conn, "SELECT COUNT(*) AS n FROM tbl_community_loc WHERE comm_id = ?", [$id]);
  $highlight_count = $counted ? (int) $counted[0]['n'] : 0;
  foreach (commf_query($conn, "SELECT comm_map_id, comm_map_fa_icon, comm_map_heading, comm_map_distance FROM tbl_community_map WHERE comm_id = ? ORDER BY comm_map_id", [$id]) ?: [] as $row) {
    $saved_nearby[(int) $row['comm_map_id']] = $row;
    $nearby_rows[] = [(int) $row['comm_map_id'], commf_icon_name($row['comm_map_fa_icon']), (string) $row['comm_map_heading'], (string) $row['comm_map_distance']];
  }
}

if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_community'])) {

  // ---------------------------------------------------------------
  // 1. Read the form
  // ---------------------------------------------------------------
  $in = [];
  foreach ($text_fields as $field => $max) {
    $value = isset($_POST[$field]) && is_string($_POST[$field]) ? trim($_POST[$field]) : '';
    $in[$field] = $max > 0 ? mb_substr($value, 0, $max) : $value;
  }
  $old = $in;
  foreach (['is_active', 'comm_showonhomepage', 'comm_featured'] as $flag) {
    $old[$flag] = isset($_POST[$flag]) ? '1' : '';
  }

  // Nearby places: every row that still has a name. A row with an id is a saved one.
  $nearby_rows = [];
  foreach ((array) ($_POST['nearby_name'] ?? []) as $i => $name) {
    $name = is_string($name) ? mb_substr(trim($name), 0, 255) : '';
    $row_id = (int) ($_POST['nearby_id'][$i] ?? 0);
    $icon = is_string($_POST['nearby_icon'][$i] ?? null) ? $_POST['nearby_icon'][$i] : '';
    $distance = is_string($_POST['nearby_distance'][$i] ?? null) ? mb_substr(trim($_POST['nearby_distance'][$i]), 0, 255) : '';
    if ($name !== '' && count($nearby_rows) < 40) {
      $nearby_rows[] = [isset($saved_nearby[$row_id]) ? $row_id : 0, preg_match('/^fa-[a-z0-9-]+$/', $icon) ? $icon : 'fa-map-marker-alt', $name, $distance];
    }
  }
  // Location highlights can be typed while adding; afterwards they have their own page.
  $highlight_rows = [];
  if (!$editing) {
    foreach ((array) ($_POST['highlight_heading'] ?? []) as $i => $heading) {
      $heading = is_string($heading) ? mb_substr(trim($heading), 0, 255) : '';
      $text = is_string($_POST['highlight_text'][$i] ?? null) ? trim($_POST['highlight_text'][$i]) : '';
      if ($heading !== '' && count($highlight_rows) < 20) {
        $highlight_rows[] = [$heading, $text];
      }
    }
  }

  // ---------------------------------------------------------------
  // 2. Check it. Nothing is saved and no file is kept until everything is right.
  // ---------------------------------------------------------------
  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  foreach (['comm_heading' => 'Community name', 'comm_location' => 'Location'] as $field => $label) {
    if ($in[$field] === '') {
      $errors[] = "$label is needed.";
    }
  }
  if ($in['comm_email'] !== '' && !filter_var($in['comm_email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'The email address does not look right.';
  }
  $posted_time = strtotime($in['comm_posted_on']);
  if ($in['comm_posted_on'] === '' || $posted_time === false) {
    $errors[] = 'Choose the "posted on" date.';
  }

  // Page address: a saved one is left exactly as it is unless it was changed here, because
  // changing it changes the page's link. A new one is made from the name when left empty.
  $saved_slug = $details ? (string) $details['comm_slug'] : '';
  if ($saved_slug !== '' && $in['comm_slug'] === $saved_slug) {
    $slug = $saved_slug;
  } else {
    $slug_was_typed = $in['comm_slug'] !== '';
    $slug = commf_slug($slug_was_typed ? $in['comm_slug'] : ($saved_slug !== '' ? $saved_slug : $in['comm_heading']));
    if ($slug === '' && $in['comm_heading'] !== '') {
      $errors[] = 'The page address (slug) needs letters or numbers.';
    } elseif ($slug !== '') {
      $taken = commf_query($conn, "SELECT comm_id FROM tbl_community WHERE comm_slug = ? AND comm_id <> ? LIMIT 1", [$slug, $id]);
      if ($taken && ($slug_was_typed || $editing)) {
        $errors[] = 'Another community already uses the page address "' . $slug . '". Choose a different one.';
      } elseif ($taken) {
        for ($n = 2; $n < 50 && $taken; $n++) {
          $taken = commf_query($conn, "SELECT comm_id FROM tbl_community WHERE comm_slug = ? LIMIT 1", [$slug . '-' . $n]);
          if (!$taken) {
            $slug = $slug . '-' . $n;
          }
        }
      }
    }
  }

  $to_move = [];      // [temporary file, final path]
  $replaced = [];     // saved files that a new upload replaces
  $stored = [];       // field => stored file name
  foreach ($image_fields as $field => $image) {
    $stored[$field] = $details ? (string) $details[$field] : '';
    list($extension, $error) = commf_check_upload($_FILES[$field] ?? null, $image[2]);
    if ($error !== '') {
      $errors[] = $error;
    } elseif ($extension !== '') {
      $folder = 'assets/media/community/' . $image[0];
      if (trim($stored[$field]) !== '') {
        $replaced[] = $folder . basename($stored[$field]);
      }
      $stored[$field] = $image[1] . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
      $to_move[] = [$_FILES[$field]['tmp_name'], $folder . $stored[$field]];
    }
  }
  if (trim($stored['comm_thumb_img']) === '' && !$errors) {
    $errors[] = 'Add the card image.';
  }

  // ---------------------------------------------------------------
  // 3. Save
  // ---------------------------------------------------------------
  if (!$errors) {
    foreach ($to_move as $move) {
      if (!is_dir(dirname($move[1]))) {
        mkdir(dirname($move[1]), 0755, true);
      }
      if (!move_uploaded_file($move[0], $move[1])) {
        $errors[] = 'A file could not be stored on the server. Nothing was saved.';
        break;
      }
    }
  }

  if (!$errors) {
    $data = [
      'comm_thumb_img' => $stored['comm_thumb_img'],
      'comm_thumb_img_alt' => $in['comm_thumb_img_alt'],
      'comm_heading' => $in['comm_heading'],
      'comm_location' => $in['comm_location'],
      'comm_inner_img' => $stored['comm_inner_img'],
      'comm_inner_thumb_img' => $stored['comm_inner_thumb_img'],
      'comm_inner_thumb_img_alt' => $in['comm_inner_thumb_img_alt'],
      'comm_long_descp' => $in['comm_long_descp'],
      'comm_address' => $in['comm_address'],
      'comm_phoneno' => $in['comm_phoneno'],
      'comm_email' => $in['comm_email'],
      'comm_loc_main_heading' => $in['comm_loc_main_heading'],
      'comm_loc_main_descp' => $in['comm_loc_main_descp'],
      'comm_loc_main_img' => $stored['comm_loc_main_img'],
      'comm_map_heading' => $in['comm_map_heading'],
      'comm_map_iframe' => $in['comm_map_iframe'],
      'comm_posted_on' => date('Y-m-d', $posted_time),
      'comm_slug' => $slug,
      'meta_title' => $in['meta_title'],
      'meta_keyword' => $in['meta_keyword'],
      'meta_description' => $in['meta_description'],
      'meta_tags' => $in['meta_tags'],
      'comm_showonhomepage' => $old['comm_showonhomepage'] === '1' ? '1' : '2',
      'comm_home_img' => $stored['comm_home_img'],
      'comm_home_card_content' => $in['comm_home_card_content'],
      'comm_featured' => $old['comm_featured'] === '1' ? '1' : '2',
      'is_active' => $old['is_active'] === '1' ? '1' : '2',
    ];

    if ($editing) {
      $old_data = $logger->getOldRecordData('tbl_community', $id);
      $assignments = [];
      foreach (array_keys($data) as $column) {
        $assignments[] = "$column = ?";
      }
      // The app copy is out of date after any change, so it is marked for the next push.
      $saved = commf_query($conn, 'UPDATE tbl_community SET ' . implode(', ', $assignments) . ', app_sync_status = 0, tstp = NOW() WHERE comm_id = ?', array_merge(array_values($data), [$id]));
      $community_id = $id;
    } else {
      $saved = commf_query($conn, 'INSERT INTO tbl_community (' . implode(', ', array_keys($data)) . ', app_sync_status, app_sync_date) VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ', 0, NOW())', array_values($data));
      $community_id = $saved ? (int) mysqli_insert_id($conn) : 0;
    }

    if (!$saved) {
      foreach ($to_move as $move) {
        if (is_file($move[1])) {
          @unlink($move[1]);
        }
      }
      $errors[] = 'The community could not be saved. Please try again.';
    } else {
      foreach ($replaced as $path) {
        if (is_file($path)) {
          @unlink($path);
        }
      }

      // Nearby places: change the rows that changed, add the new ones, remove the ones taken out.
      $kept = [];
      foreach ($nearby_rows as $row) {
        if ($row[0] > 0) {
          $kept[$row[0]] = true;
          $before = $saved_nearby[$row[0]];
          $icon_changed = commf_icon_name($before['comm_map_fa_icon']) !== $row[1];
          if ($icon_changed || $before['comm_map_heading'] !== $row[2] || $before['comm_map_distance'] !== $row[3]) {
            // An icon that was not changed keeps the exact value it was saved with.
            commf_query($conn, "UPDATE tbl_community_map SET comm_map_fa_icon = ?, comm_map_heading = ?, comm_map_distance = ? WHERE comm_map_id = ? AND comm_id = ?", [$icon_changed ? 'fas ' . $row[1] : $before['comm_map_fa_icon'], $row[2], $row[3], $row[0], $community_id]);
          }
        } else {
          commf_query($conn, "INSERT INTO tbl_community_map (comm_id, comm_map_fa_icon, comm_map_heading, comm_map_distance, is_active, tstp) VALUES (?, ?, ?, ?, '1', NOW())", [$community_id, 'fas ' . $row[1], $row[2], $row[3]]);
        }
      }
      foreach (array_keys($saved_nearby) as $row_id) {
        if (!isset($kept[$row_id])) {
          commf_query($conn, "DELETE FROM tbl_community_map WHERE comm_map_id = ? AND comm_id = ?", [$row_id, $community_id]);
        }
      }
      foreach ($highlight_rows as $row) {
        // Plain text becomes paragraphs; text that already has tags is kept as typed.
        $text = strpos($row[1], '<') === false && $row[1] !== '' ? '<p>' . nl2br(htmlspecialchars($row[1], ENT_QUOTES, 'UTF-8')) . '</p>' : $row[1];
        commf_query($conn, "INSERT INTO tbl_community_loc (comm_id, comm_loc_heading, comm_loc_description, is_active, tstp) VALUES (?, ?, ?, '1', NOW())", [$community_id, $row[0], $text]);
      }

      if ($editing) {
        $logger->log('UPDATE', 'tbl_community', $id, $old_data, $logger->getOldRecordData('tbl_community', $id), 'Community Profile Updated : ' . $in['comm_heading']);
      } else {
        $logger->log('INSERT', 'tbl_community', $community_id, null, $logger->getOldRecordData('tbl_community', $community_id), 'New Community added: ' . $in['comm_heading']);
      }
      header('Location: ' . BASE_URL . (isset($_POST['save_and_stay']) ? 'edit-community.php?edit=' . base64_encode($community_id) . '&saved=1' : 'manage-community.php?done=' . ($editing ? 'saved' : 'added')));
      exit;
    }
  }
}

$just_saved = $editing && $details && isset($_GET['saved']) && !$errors;
$val = function ($field) use (&$old) {
  return commf_e($old[$field] ?? '');
};
?>
<!--begin::Body-->

<body id="kt_body"
  class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
  <!--begin::Main-->
  <!--begin::Header Mobile-->
  <div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
    <!--begin::Logo-->
    <a href="index.html">
      <img alt="Logo" src="<?= BASE_URL ?>assets/media/logos/logo-light.png" />
    </a>
    <!--end::Logo-->
    <!--begin::Toolbar-->
    <div class="d-flex align-items-center">
      <!--begin::Aside Mobile Toggle-->
      <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
        <span></span>
      </button>
      <!--end::Aside Mobile Toggle-->
      <!--begin::Header Menu Mobile Toggle-->
      <button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle">
        <span></span>
      </button>
      <!--end::Header Menu Mobile Toggle-->
      <!--begin::Topbar Mobile Toggle-->
      <button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle">
        <span class="svg-icon svg-icon-xl">
          <!--begin::Svg Icon | path:<?= BASE_URL ?>assets/media/svg/icons/General/User.svg-->
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
            viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <polygon points="0 0 24 0 24 24 0 24" />
              <path
                d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                fill="#000000" fill-rule="nonzero" opacity="0.3" />
              <path
                d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                fill="#000000" fill-rule="nonzero" />
            </g>
          </svg>
          <!--end::Svg Icon-->
        </span>
      </button>
      <!--end::Topbar Mobile Toggle-->
    </div>
    <!--end::Toolbar-->
  </div>
  <!--end::Header Mobile-->
  <div class="d-flex flex-column flex-root">
    <!--begin::Page-->
    <div class="d-flex flex-row flex-column-fluid page">
      <!--begin::Aside-->
      <?php include('includes/sidebar.php'); ?>
      <!--end::Aside-->
      <!--begin::Wrapper-->
      <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
        <!--begin::Header-->
        <div id="kt_header" class="header header-fixed">
          <!--begin::Container-->
          <div class="container-fluid d-flex align-items-stretch justify-content-between">
            <!--begin::Header Menu Wrapper-->
            <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
              <!--begin::Header Menu-->
              <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">

              </div>
              <!--end::Header Menu-->
            </div>
            <!--end::Header Menu Wrapper-->
            <!--begin::Topbar-->
            <div class="topbar">

              <!--begin::User-->
              <div class="topbar-item">
                <div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2"
                  id="kt_quick_user_toggle">
                  <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                  <span
                    class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= $_SESSION['username'] ?></span>
                  <span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
                    <span
                      class="symbol-label font-size-h5 font-weight-bold"><?= substr($_SESSION['username'], 0, 1) ?></span>
                  </span>
                </div>
              </div>
              <!--end::User-->
            </div>
            <!--end::Topbar-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::Header-->
        <!--begin::Content-->
        <div class="content d-flex flex-column flex-column-fluid dre-dash-content" id="kt_content">

          <!--begin::Entry-->
          <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">

              <!--begin::Community form-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-developers.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-edit.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-communities.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-communities">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1><?= $editing ? 'Edit community' : 'Add community' ?></h1>
                    <?php if ($editing): ?>
                      <p><?= $details ? commf_e($details['comm_heading']) . ' &middot; ' . number_format($project_count) . ($project_count === 1 ? ' project' : ' projects') : 'This community could not be found.' ?></p>
                    <?php else: ?>
                      <p>Fields marked <b>*</b> are needed. Everything else can be filled in later from Edit.</p>
                    <?php endif; ?>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL ?>manage-community.php"><i class="bi bi-arrow-left"></i> Back to communities</a>
                    <?php if ($details): ?>
                      <a href="<?= commf_e(BASE_URL_HOME . 'community/' . $details['comm_slug']) ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View on the website</a>
                    <?php endif; ?>
                  </div>
                </div>

                <?php if (!$can_show_form): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This community could not be found.</strong> It may have been deleted. <a href="<?= BASE_URL ?>manage-community.php">Back to all communities</a></div>
                  </div>
                <?php elseif ($just_saved): ?>
                  <div class="dre-form-alert dre-form-ok" role="status" id="commfSaved">
                    <i class="bi bi-check-circle"></i>
                    <div><strong>The community was saved.</strong> <a href="<?= BASE_URL ?>manage-community.php">All communities</a></div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="commfErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>The community was not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= commf_e($error) ?></li>
                        <?php endforeach; ?>
                      </ul>
                      <em>Pictures have to be chosen again: a browser does not keep them after an error.</em>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($can_show_form): ?>
                <div class="dre-form-layout">

                  <!-- Jump to a part of the form -->
                  <nav class="dre-form-nav" aria-label="Parts of the form">
                    <a href="#sec-basics"><i class="bi bi-card-heading"></i> Basics</a>
                    <a href="#sec-images"><i class="bi bi-images"></i> Pictures</a>
                    <a href="#sec-about"><i class="bi bi-text-paragraph"></i> About</a>
                    <a href="#sec-location"><i class="bi bi-stars"></i> Location</a>
                    <a href="#sec-nearby"><i class="bi bi-signpost-2"></i> Nearby places</a>
                    <a href="#sec-contact"><i class="bi bi-telephone"></i> Contact</a>
                    <a href="#sec-seo"><i class="bi bi-search"></i> SEO</a>
                    <a href="#sec-publish"><i class="bi bi-send"></i> Publishing</a>
                  </nav>

                  <!--begin::Form-->
                  <form action="<?= BASE_URL ?><?= $editing ? 'edit-community.php?edit=' . base64_encode($id) : 'add-community.php' ?>" enctype="multipart/form-data" method="post" id="community-form" class="dre-form">
                    <input type="hidden" name="csrf" value="<?= commf_e($_SESSION['admin_csrf']) ?>">
                    <input type="hidden" name="save_community" value="1">

                    <!-- Basics -->
                    <section class="dre-glass dre-form-section" id="sec-basics">
                      <h2><i class="bi bi-card-heading"></i> Basics</h2>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Community name <b>*</b></span>
                          <input type="text" name="comm_heading" id="commfName" value="<?= $val('comm_heading') ?>" maxlength="255" autocomplete="off" placeholder="Dubai Marina" required>
                          <small>Shown as the title of the community's page and on project pages.</small>
                        </label>
                        <label class="dre-field">
                          <span>Location <b>*</b></span>
                          <input type="text" name="comm_location" value="<?= $val('comm_location') ?>" maxlength="255" autocomplete="off" placeholder="Dubai" required>
                          <small>The city or emirate, shown on community cards.</small>
                        </label>
                      </div>
                    </section>

                    <!-- Pictures -->
                    <section class="dre-glass dre-form-section" id="sec-images">
                      <h2><i class="bi bi-images"></i> Pictures</h2>
                      <p class="dre-form-note">JPG, PNG, GIF or WEBP, up to 5 MB each.</p>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Card image <b>*</b></span>
                          <input type="file" name="comm_thumb_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="commfThumbPreview"<?= $details && trim((string) $details['comm_thumb_img']) !== '' ? '' : ' required' ?>>
                          <small>The picture on community cards and lists.</small>
                          <div class="dre-previews" id="commfThumbPreview"></div>
                          <?php if ($details) { commf_current('thumb/', $details['comm_thumb_img']); } ?>
                        </label>
                        <label class="dre-field">
                          <span>Card image alt text</span>
                          <input type="text" name="comm_thumb_img_alt" value="<?= $val('comm_thumb_img_alt') ?>" maxlength="255" autocomplete="off" placeholder="Dubai Marina skyline">
                          <small>Read out by screen readers and used by search engines.</small>
                        </label>
                      </div>
                      <label class="dre-field">
                        <span>Inner image</span>
                        <input type="file" name="comm_inner_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="commfInnerPreview">
                        <small>The large banner at the top of the community's page.</small>
                        <div class="dre-previews" id="commfInnerPreview"></div>
                        <?php if ($details) { commf_current('inner/', $details['comm_inner_img']); } ?>
                      </label>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Inner thumbnail</span>
                          <input type="file" name="comm_inner_thumb_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="commfInnerThumbPreview">
                          <small>The picture next to the description.</small>
                          <div class="dre-previews" id="commfInnerThumbPreview"></div>
                          <?php if ($details) { commf_current('inner-thumb/', $details['comm_inner_thumb_img']); } ?>
                        </label>
                        <label class="dre-field">
                          <span>Inner thumbnail alt text</span>
                          <input type="text" name="comm_inner_thumb_img_alt" value="<?= $val('comm_inner_thumb_img_alt') ?>" maxlength="255" autocomplete="off">
                        </label>
                      </div>
                    </section>

                    <!-- About -->
                    <section class="dre-glass dre-form-section" id="sec-about">
                      <h2><i class="bi bi-text-paragraph"></i> About the community</h2>
                      <div class="dre-field">
                        <label for="kt-tinymce-4"><span>Description</span></label>
                        <textarea name="comm_long_descp" class="tinymce-editor" id="kt-tinymce-4" rows="10"><?= $val('comm_long_descp') ?></textarea>
                      </div>
                    </section>

                    <!-- Location -->
                    <section class="dre-glass dre-form-section" id="sec-location">
                      <h2><i class="bi bi-stars"></i> Location</h2>
                      <label class="dre-field">
                        <span>Section heading</span>
                        <input type="text" name="comm_loc_main_heading" value="<?= $val('comm_loc_main_heading') ?>" maxlength="255" autocomplete="off" placeholder="Life in Dubai Marina">
                      </label>
                      <div class="dre-field">
                        <label for="kt-tinymce-5"><span>Section text</span></label>
                        <textarea name="comm_loc_main_descp" class="tinymce-editor" id="kt-tinymce-5" rows="8"><?= $val('comm_loc_main_descp') ?></textarea>
                      </div>
                      <label class="dre-field">
                        <span>Section image</span>
                        <input type="file" name="comm_loc_main_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="commfLocPreview">
                        <div class="dre-previews" id="commfLocPreview"></div>
                        <?php if ($details) { commf_current('location/', $details['comm_loc_main_img']); } ?>
                      </label>

                      <h3>Location highlights</h3>
                      <?php if ($editing): ?>
                        <p class="dre-form-note">Short points such as &ldquo;Luxury Living&rdquo; or &ldquo;Iconic Landmarks&rdquo;, each with its own text. They are edited on their own page.</p>
                        <div class="dre-form-grid dre-cols-2">
                          <a class="dre-list-link" href="<?= BASE_URL ?>manage-comm-loc.php?comm_id=<?= (int) $id ?>" target="_blank" rel="noopener"><i class="bi bi-stars"></i><span><strong>Location highlights</strong><small><?= $highlight_count ?> <?= $highlight_count === 1 ? 'highlight' : 'highlights' ?></small></span></a>
                        </div>
                      <?php else: ?>
                        <p class="dre-form-note">Short points such as &ldquo;Luxury Living&rdquo; or &ldquo;Iconic Landmarks&rdquo;, each with its own text. More can be added after saving.</p>
                        <div data-repeat="highlight">
                          <?php foreach ($highlight_rows ?: [['', '']] as $row): ?>
                            <div class="dre-repeat-row dre-highlight-row">
                              <input type="text" name="highlight_heading[]" value="<?= commf_e($row[0]) ?>" maxlength="255" placeholder="Heading" aria-label="Highlight heading">
                              <textarea name="highlight_text[]" rows="2" placeholder="Text" aria-label="Highlight text"><?= commf_e($row[1]) ?></textarea>
                              <button type="button" class="dre-icon-btn dre-repeat-remove" title="Remove this highlight" aria-label="Remove this highlight"><i class="bi bi-x-lg"></i></button>
                            </div>
                          <?php endforeach; ?>
                        </div>
                        <button type="button" class="dre-btn dre-repeat-add" data-target="highlight"><i class="bi bi-plus-lg"></i> Add a highlight</button>
                      <?php endif; ?>
                    </section>

                    <!-- Nearby places and map -->
                    <section class="dre-glass dre-form-section" id="sec-nearby">
                      <h2><i class="bi bi-signpost-2"></i> Nearby places and map</h2>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Section heading</span>
                          <input type="text" name="comm_map_heading" value="<?= $val('comm_map_heading') ?>" maxlength="255" autocomplete="off" placeholder="What is nearby">
                        </label>
                        <label class="dre-field">
                          <span>Map</span>
                          <textarea name="comm_map_iframe" rows="2" placeholder="Google Maps embed code or link"><?= $val('comm_map_iframe') ?></textarea>
                        </label>
                      </div>
                      <h3>Nearby places</h3>
                      <p class="dre-form-note">One row per place, with how far it is. Rows are saved together with the rest of the form. <span id="commfNearbyCount"></span></p>
                      <div data-repeat="nearby" id="commfNearby">
                        <?php foreach ($nearby_rows ?: [[0, 'fa-landmark', '', '']] as $row):
                          $row_icons = $nearby_icons + ($row[1] !== '' ? [$row[1] => ucwords(str_replace('-', ' ', substr($row[1], 3)))] : []);
                        ?>
                          <div class="dre-repeat-row dre-nearby-row">
                            <i class="fas <?= commf_e($row[1]) ?>" aria-hidden="true"></i>
                            <select name="nearby_icon[]" class="dre-nearby-icon" aria-label="Kind of place">
                              <?php foreach ($row_icons as $icon => $label): ?>
                                <option value="<?= commf_e($icon) ?>" <?= $row[1] === $icon ? 'selected' : '' ?>><?= commf_e($label) ?></option>
                              <?php endforeach; ?>
                            </select>
                            <input type="text" name="nearby_name[]" value="<?= commf_e($row[2]) ?>" maxlength="255" placeholder="Place, e.g. Dubai Mall" aria-label="Place">
                            <input type="text" name="nearby_distance[]" value="<?= commf_e($row[3]) ?>" maxlength="255" placeholder="15 Mins" aria-label="Distance">
                            <input type="hidden" name="nearby_id[]" value="<?= (int) $row[0] ?>">
                            <button type="button" class="dre-icon-btn dre-repeat-remove" title="Remove this place" aria-label="Remove this place"><i class="bi bi-x-lg"></i></button>
                          </div>
                        <?php endforeach; ?>
                      </div>
                      <button type="button" class="dre-btn dre-repeat-add" data-target="nearby"><i class="bi bi-plus-lg"></i> Add a place</button>
                    </section>

                    <!-- Contact -->
                    <section class="dre-glass dre-form-section" id="sec-contact">
                      <h2><i class="bi bi-telephone"></i> Contact</h2>
                      <label class="dre-field">
                        <span>Address</span>
                        <input type="text" name="comm_address" value="<?= $val('comm_address') ?>" maxlength="255" autocomplete="off">
                      </label>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Phone number</span>
                          <input type="text" name="comm_phoneno" value="<?= $val('comm_phoneno') ?>" maxlength="255" inputmode="tel" autocomplete="off">
                        </label>
                        <label class="dre-field">
                          <span>Email</span>
                          <input type="email" name="comm_email" value="<?= $val('comm_email') ?>" maxlength="255" autocomplete="off">
                        </label>
                      </div>
                    </section>

                    <!-- SEO -->
                    <section class="dre-glass dre-form-section" id="sec-seo">
                      <h2><i class="bi bi-search"></i> SEO</h2>
                      <label class="dre-field">
                        <span>Page address (slug)</span>
                        <input type="text" name="comm_slug" id="commfSlug" value="<?= $val('comm_slug') ?>" maxlength="200" autocomplete="off" placeholder="made from the community name">
                        <small>The page will be at <code id="commfUrl"><?= commf_e(BASE_URL_HOME) ?>community/</code><?= $editing ? ' Changing it breaks links to the old address.' : '' ?></small>
                      </label>
                      <label class="dre-field">
                        <span>Meta title</span>
                        <input type="text" name="meta_title" value="<?= $val('meta_title') ?>" maxlength="255" autocomplete="off" data-count="60">
                        <small class="dre-count"></small>
                      </label>
                      <label class="dre-field">
                        <span>Meta description</span>
                        <textarea name="meta_description" id="commfMetaDesc" rows="3" data-count="160"><?= $val('meta_description') ?></textarea>
                        <small class="dre-count"></small>
                      </label>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Meta keywords</span>
                          <input type="text" name="meta_keyword" value="<?= $val('meta_keyword') ?>" maxlength="255" autocomplete="off">
                        </label>
                        <label class="dre-field">
                          <span>Meta tags</span>
                          <textarea name="meta_tags" rows="2"><?= $val('meta_tags') ?></textarea>
                        </label>
                      </div>
                    </section>

                    <!-- Publishing -->
                    <section class="dre-glass dre-form-section" id="sec-publish">
                      <h2><i class="bi bi-send"></i> Publishing</h2>
                      <div class="dre-form-grid dre-cols-3">
                        <label class="dre-field">
                          <span>Posted on <b>*</b></span>
                          <input type="date" name="comm_posted_on" value="<?= $val('comm_posted_on') ?>" required>
                        </label>
                        <label class="dre-toggle">
                          <input type="checkbox" name="is_active" value="1" <?= ($old['is_active'] ?? '') === '1' ? 'checked' : '' ?>>
                          <i></i>
                          <span><strong>Live on the website</strong><small>Off = saved but hidden.</small></span>
                        </label>
                        <label class="dre-toggle">
                          <input type="checkbox" name="comm_featured" value="1" <?= ($old['comm_featured'] ?? '') === '1' ? 'checked' : '' ?>>
                          <i></i>
                          <span><strong>Featured</strong><small>Can be picked as a featured community on project pages.</small></span>
                        </label>
                      </div>
                      <label class="dre-toggle">
                        <input type="checkbox" name="comm_showonhomepage" value="1" id="commfHome" <?= ($old['comm_showonhomepage'] ?? '') === '1' ? 'checked' : '' ?>>
                        <i></i>
                        <span><strong>Show on the homepage</strong><small>Adds a card for this community to the homepage.</small></span>
                      </label>
                      <div class="dre-form-grid dre-cols-2" id="commfHomeFields">
                        <label class="dre-field">
                          <span>Homepage card image</span>
                          <input type="file" name="comm_home_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="commfHomePreview">
                          <div class="dre-previews" id="commfHomePreview"></div>
                          <?php if ($details) { commf_current('home/', $details['comm_home_img']); } ?>
                        </label>
                        <label class="dre-field">
                          <span>Homepage card text</span>
                          <textarea name="comm_home_card_content" rows="3" data-count="160" placeholder="A sentence or two for the homepage card."><?= $val('comm_home_card_content') ?></textarea>
                          <small class="dre-count"></small>
                        </label>
                      </div>
                    </section>

                    <!-- Always in view: how complete the page is, and the save buttons -->
                    <div class="dre-form-bar">
                      <div class="dre-form-progress" title="Details that make a community page complete">
                        <div><strong id="commfDone">0</strong> of <span id="commfOutOf">7</span> key details filled</div>
                        <div class="dre-track"><i id="commfMeter" style="width: 0%"></i></div>
                        <small id="commfMissing"></small>
                      </div>
                      <div class="dre-form-bar-buttons">
                        <button type="submit" name="save_and_stay" value="1" class="dre-btn">Save and keep editing</button>
                        <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Save community' ?></button>
                      </div>
                    </div>
                  </form>
                  <!--end::Form-->
                </div>

                <!-- Empty rows the "Add" buttons copy -->
                <template id="tpl-nearby">
                  <div class="dre-repeat-row dre-nearby-row">
                    <i class="fas fa-landmark" aria-hidden="true"></i>
                    <select name="nearby_icon[]" class="dre-nearby-icon" aria-label="Kind of place">
                      <?php foreach ($nearby_icons as $icon => $label): ?>
                        <option value="<?= commf_e($icon) ?>"><?= commf_e($label) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <input type="text" name="nearby_name[]" maxlength="255" placeholder="Place, e.g. Dubai Mall" aria-label="Place">
                    <input type="text" name="nearby_distance[]" maxlength="255" placeholder="15 Mins" aria-label="Distance">
                    <input type="hidden" name="nearby_id[]" value="0">
                    <button type="button" class="dre-icon-btn dre-repeat-remove" title="Remove this place" aria-label="Remove this place"><i class="bi bi-x-lg"></i></button>
                  </div>
                </template>
                <template id="tpl-highlight">
                  <div class="dre-repeat-row dre-highlight-row">
                    <input type="text" name="highlight_heading[]" maxlength="255" placeholder="Heading" aria-label="Highlight heading">
                    <textarea name="highlight_text[]" rows="2" placeholder="Text" aria-label="Highlight text"></textarea>
                    <button type="button" class="dre-icon-btn dre-repeat-remove" title="Remove this highlight" aria-label="Remove this highlight"><i class="bi bi-x-lg"></i></button>
                  </div>
                </template>
                <?php endif; ?>
              </div>
              <!--end::Community form-->

            </div>
            <!--end::Container-->
          </div>
          <!--end::Entry-->
        </div>
        <!--end::Content-->

        <?php
        include('includes/footer-inner.php');
        ?>

        <script>
          // Community form: helpers that make it quicker to fill in. The server checks everything again on save.
          (function () {
            var form = document.getElementById('community-form');
            if (!form) {
              return;
            }
            var siteUrl = <?= json_encode(BASE_URL_HOME) ?>;
            var has = <?= json_encode(['thumb' => $details && trim((string) $details['comm_thumb_img']) !== '', 'inner' => $details && trim((string) $details['comm_inner_img']) !== '', 'highlights' => $highlight_count]) ?>;
            var dirty = false;

            // --- Page address: follows the name until someone types their own ---
            var nameInput = document.getElementById('commfName');
            var slugInput = document.getElementById('commfSlug');
            var urlPreview = document.getElementById('commfUrl');
            function slugify(text) {
              return text.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            }
            function showUrl() {
              // A saved address is shown as it is; a new one is made from what is typed.
              urlPreview.textContent = siteUrl + 'community/' + (slugInput.value !== '' ? (slugInput.value === slugInput.defaultValue ? slugInput.value : slugify(slugInput.value)) : slugify(nameInput.value));
            }
            nameInput.addEventListener('input', showUrl);
            slugInput.addEventListener('input', showUrl);
            showUrl();

            // --- Homepage card fields only when the homepage switch is on ---
            var homeSwitch = document.getElementById('commfHome');
            var homeFields = document.getElementById('commfHomeFields');
            function toggleHome() { homeFields.style.display = homeSwitch.checked ? '' : 'none'; }
            homeSwitch.addEventListener('change', toggleHome);
            toggleHome();

            // --- Rows that can be added and removed (nearby places, highlights) ---
            function nearbyCount() {
              var filled = Array.prototype.filter.call(form.querySelectorAll('[name="nearby_name[]"]'), function (input) { return input.value.trim() !== ''; }).length;
              document.getElementById('commfNearbyCount').textContent = filled === 0 ? 'No places yet.' : filled + (filled === 1 ? ' place.' : ' places.');
              return filled;
            }
            document.querySelectorAll('.dre-repeat-add').forEach(function (button) {
              button.addEventListener('click', function () {
                var key = button.getAttribute('data-target');
                var row = document.getElementById('tpl-' + key).content.firstElementChild.cloneNode(true);
                form.querySelector('[data-repeat="' + key + '"]').appendChild(row);
                var first = row.querySelector('input[type="text"]');
                if (first) { first.focus(); }
              });
            });
            form.addEventListener('click', function (event) {
              var remove = event.target.closest('.dre-repeat-remove');
              if (!remove) {
                return;
              }
              var row = remove.closest('.dre-repeat-row');
              if (row.parentNode.querySelectorAll('.dre-repeat-row').length > 1) {
                row.remove();
              } else {
                // The last row is emptied instead, so there is always one to type in.
                row.querySelectorAll('input[type="text"], textarea').forEach(function (field) { field.value = ''; });
                var id = row.querySelector('[name="nearby_id[]"]');
                if (id) { id.value = '0'; }
              }
              dirty = true;
              progress();
            });
            form.addEventListener('change', function (event) {
              if (event.target.classList.contains('dre-nearby-icon')) {
                event.target.parentNode.querySelector('i').className = 'fas ' + event.target.value;
              }
            });

            // --- Picked pictures: a small preview, and a warning straight away when one cannot be used ---
            form.addEventListener('change', function (event) {
              var input = event.target;
              if (input.type !== 'file') {
                return;
              }
              var box = document.getElementById(input.getAttribute('data-preview'));
              var bad = '';
              if (box) { box.textContent = ''; }
              Array.prototype.forEach.call(input.files, function (file) {
                if (file.type.indexOf('image/') !== 0) {
                  bad = '"' + file.name + '" is not an image.';
                } else if (file.size > 5 * 1024 * 1024) {
                  bad = '"' + file.name + '" is larger than 5 MB.';
                } else if (box) {
                  var img = document.createElement('img');
                  img.src = URL.createObjectURL(file);
                  img.alt = '';
                  box.appendChild(img);
                }
              });
              input.setCustomValidity(bad);
              input.classList.toggle('dre-invalid', bad !== '');
              if (bad !== '') { input.reportValidity(); }
            });

            // --- Character counts ---
            form.querySelectorAll('[data-count]').forEach(function (field) {
              var target = parseInt(field.getAttribute('data-count'), 10);
              var note = field.parentNode.querySelector('.dre-count');
              function count() {
                var length = field.value.length;
                note.textContent = length + ' characters' + (length === 0 ? ' - aim for about ' + target + '.' : (length > target ? ' - about ' + target + ' reads best.' : '.'));
                note.classList.toggle('dre-warn-text', length > target);
              }
              field.addEventListener('input', count);
              count();
            });

            // --- How complete the page is ---
            function editorText(id) {
              var editor = window.tinymce && window.tinymce.get(id);
              var html = editor ? editor.getContent() : document.getElementById(id).value;
              return html.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
            }
            function fileChosen(name, saved) {
              return form.querySelector('[name="' + name + '"]').files.length > 0 || saved;
            }
            function progress() {
              var typedHighlights = Array.prototype.filter.call(form.querySelectorAll('[name="highlight_heading[]"]'), function (input) { return input.value.trim() !== ''; }).length;
              var checks = [
                ['name', nameInput.value.trim() !== ''],
                ['card image', fileChosen('comm_thumb_img', has.thumb)],
                ['inner image', fileChosen('comm_inner_img', has.inner)],
                ['description', editorText('kt-tinymce-4') !== ''],
                ['location highlights', has.highlights > 0 || typedHighlights > 0],
                ['nearby places', nearbyCount() > 0],
                ['meta description', document.getElementById('commfMetaDesc').value.trim() !== '']
              ];
              var done = checks.filter(function (check) { return check[1]; }).length;
              document.getElementById('commfDone').textContent = done;
              document.getElementById('commfOutOf').textContent = checks.length;
              document.getElementById('commfMeter').style.width = Math.round(done * 100 / checks.length) + '%';
              var missing = checks.filter(function (check) { return !check[1]; }).map(function (check) { return check[0]; });
              document.getElementById('commfMissing').textContent = missing.length ? 'Still empty: ' + missing.join(', ') : 'All key details are filled in.';
            }
            form.addEventListener('input', function () { dirty = true; progress(); });
            form.addEventListener('change', function () { dirty = true; progress(); });
            progress();
            // The editors load after this script, so look again once they are ready.
            window.addEventListener('load', function () {
              setTimeout(function () {
                progress();
                if (window.tinymce) {
                  window.tinymce.editors.forEach(function (editor) {
                    editor.on('change keyup', function () { dirty = true; progress(); });
                  });
                }
              }, 1200);
            });

            // --- Jump links: highlight the part in view ---
            var links = Array.prototype.slice.call(document.querySelectorAll('.dre-form-nav a'));
            var sections = Array.prototype.slice.call(form.querySelectorAll('.dre-form-section'));
            var navWaiting = false;
            function markNav() {
              navWaiting = false;
              var line = window.innerHeight / 3;
              var current = sections[0];
              sections.forEach(function (section) {
                if (section.getBoundingClientRect().top <= line) { current = section; }
              });
              links.forEach(function (link) {
                link.classList.toggle('is-on', link.getAttribute('href') === '#' + current.id);
              });
            }
            window.addEventListener('scroll', function () {
              if (!navWaiting) { navWaiting = true; window.requestAnimationFrame(markNav); }
            }, { passive: true });
            markNav();

            // --- Saving ---
            form.addEventListener('submit', function () {
              if (window.tinymce) { window.tinymce.triggerSave(); }
              dirty = false;
              form.querySelectorAll('button[type="submit"]').forEach(function (button) {
                // Disabled a moment later so the clicked button's own value is still sent.
                setTimeout(function () { button.disabled = true; }, 0);
              });
            });
            form.addEventListener('invalid', function (event) { event.target.classList.add('dre-invalid'); }, true);
            form.addEventListener('input', function (event) { event.target.classList.remove('dre-invalid'); });
            window.addEventListener('beforeunload', function (event) {
              if (dirty) { event.preventDefault(); event.returnValue = ''; }
            });

            if (document.getElementById('commfSaved') && window.history.replaceState) {
              // Reloading the page should not show "saved" again.
              window.history.replaceState(null, '', window.location.href.replace(/&saved=1/, ''));
            }
            var errorBox = document.getElementById('commfErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
