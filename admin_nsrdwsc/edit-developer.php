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

// This file is used for both adding and editing: this copy changes a developer (add-developer.php adds one).
$editing = true;

function devf_e($text)
{
  return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function devf_query($conn, $sql, array $params = [])
{
  $stmt = mysqli_prepare($conn, $sql);
  if (!$stmt) {
    error_log('Developer form query failed: ' . mysqli_error($conn));
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
    error_log('Developer form query failed: ' . mysqli_stmt_error($stmt));
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

// "Emaar Properties" -> "emaar-properties"
function devf_slug($text)
{
  $text = strtolower(trim((string) $text));
  $text = preg_replace('/[^a-z0-9]+/', '-', $text);
  return trim($text, '-');
}

// Pictures are checked by extension and by their real content, and are stored under a
// name made here, never the name they arrived with.
const DEVF_IMAGE = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
const DEVF_IMAGE_MAX = 5 * 1024 * 1024;

// Checks one uploaded picture. Returns [extension, error]; extension is '' when no file was chosen.
function devf_check_upload($file, $label)
{
  if (!is_array($file) || is_array($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE || (string) $file['name'] === '') {
    return ['', ''];
  }
  if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > DEVF_IMAGE_MAX) {
    return ['', "$label is too large (maximum 5 MB)."];
  }
  if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
    return ['', "$label could not be uploaded. Please try again."];
  }
  $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $real_extension = array_search($finfo->file($file['tmp_name']), ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'], true);
  if (!isset(DEVF_IMAGE[$extension]) || $real_extension === false) {
    return ['', "$label must be one of: JPG, PNG, GIF, WEBP."];
  }
  return [$real_extension, ''];
}

// Shows the picture a field already has.
function devf_current($folder, $name)
{
  $name = trim((string) $name);
  if ($name === '') {
    return;
  }
  $url = BASE_URL . 'assets/media/developer/' . $folder . rawurlencode($name);
  echo '<div class="dre-current"><a href="' . devf_e($url) . '" target="_blank" rel="noopener"><img src="' . devf_e($url) . '" alt="" loading="lazy"></a><span>Current picture. Choose a new one to replace it.</span></div>';
}

// Pictures: field => [folder inside assets/media/developer/, name prefix, label]
$image_fields = [
  'dev_img' => ['', 'dev_img', 'Logo'],
  'dev_homp_img' => ['homepage-image/', 'dev_homp_img', 'Homepage image'],
  'dev_innr_img' => ['inner-image/', 'dev_innr_img', 'Inner image'],
  'choose_us_img' => ['choose-us/', 'choose_us_img', '"Why choose us" image'],
];
// Text fields: name => longest length kept (0 = no limit).
$text_fields = [
  'dev_heading' => 255, 'dev_img_alt' => 255, 'dev_homp_img_alt' => 255, 'dev_short_descp' => 0, 'dev_long_descp' => 0,
  'dev_address' => 255, 'dev_phoneno' => 255, 'dev_email' => 255, 'choose_us_desc' => 0, 'dev_posted_on' => 20, 'dev_slug' => 200,
  'meta_title' => 255, 'meta_keyword' => 255, 'meta_description' => 0, 'meta_tags' => 0, 'dev_order_no' => 6,
];

$id = 0;
$details = null;
if ($editing) {
  $id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
  $details = $id > 0 ? (devf_query($conn, "SELECT * FROM tbl_developer WHERE dev_id = ?", [$id]) ?: []) : [];
  $details = $details ? $details[0] : null;
}
$can_show_form = !$editing || $details;

$errors = [];
$old = ['is_active' => '1', 'dev_showonhomepage' => '', 'dev_posted_on' => date('Y-m-d')];
foreach ($text_fields as $field => $max) {
  $old += [$field => ''];
}
$project_count = 0;
if ($details) {
  // The form starts from what is saved.
  foreach ($text_fields as $field => $max) {
    $old[$field] = (string) ($details[$field] ?? '');
  }
  $posted_time = strtotime((string) $details['dev_posted_on']);
  $old['dev_posted_on'] = $posted_time ? date('Y-m-d', $posted_time) : '';
  $old['dev_order_no'] = (int) $details['dev_order_no'] > 0 ? (string) (int) $details['dev_order_no'] : '';
  $old['is_active'] = $details['is_active'] === '1' ? '1' : '';
  $old['dev_showonhomepage'] = $details['dev_showonhomepage'] === '1' ? '1' : '';
  $counted = devf_query($conn, "SELECT COUNT(*) AS n FROM tbl_proj WHERE prj_dev_id = ?", [$id]);
  $project_count = $counted ? (int) $counted[0]['n'] : 0;
}

if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_developer'])) {

  // ---------------------------------------------------------------
  // 1. Read the form
  // ---------------------------------------------------------------
  $in = [];
  foreach ($text_fields as $field => $max) {
    $value = isset($_POST[$field]) && is_string($_POST[$field]) ? trim($_POST[$field]) : '';
    $in[$field] = $max > 0 ? mb_substr($value, 0, $max) : $value;
  }
  $old = $in;
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';
  $old['dev_showonhomepage'] = isset($_POST['dev_showonhomepage']) ? '1' : '';

  // ---------------------------------------------------------------
  // 2. Check it. Nothing is saved and no file is kept until everything is right.
  // ---------------------------------------------------------------
  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if ($in['dev_heading'] === '') {
    $errors[] = 'Developer name is needed.';
  }
  if ($in['dev_email'] !== '' && !filter_var($in['dev_email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'The email address does not look right.';
  }
  if ($in['dev_order_no'] !== '' && (!ctype_digit($in['dev_order_no']) || (int) $in['dev_order_no'] < 1)) {
    $errors[] = 'Homepage position must be a number, 1 or higher.';
  }
  $posted_time = strtotime($in['dev_posted_on']);
  if ($in['dev_posted_on'] === '' || $posted_time === false) {
    $errors[] = 'Choose the "posted on" date.';
  }

  // Page address: a saved one is left exactly as it is unless it was changed here, because
  // changing it changes the page's link. A new one is made from the name when left empty.
  $saved_slug = $details ? (string) $details['dev_slug'] : '';
  if ($saved_slug !== '' && $in['dev_slug'] === $saved_slug) {
    $slug = $saved_slug;
  } else {
    $slug_was_typed = $in['dev_slug'] !== '';
    $slug = devf_slug($slug_was_typed ? $in['dev_slug'] : ($saved_slug !== '' ? $saved_slug : $in['dev_heading']));
    if ($slug === '' && $in['dev_heading'] !== '') {
      $errors[] = 'The page address (slug) needs letters or numbers.';
    } elseif ($slug !== '') {
      $taken = devf_query($conn, "SELECT dev_id FROM tbl_developer WHERE dev_slug = ? AND dev_id <> ? LIMIT 1", [$slug, $id]);
      if ($taken && ($slug_was_typed || $editing)) {
        $errors[] = 'Another developer already uses the page address "' . $slug . '". Choose a different one.';
      } elseif ($taken) {
        for ($n = 2; $n < 50 && $taken; $n++) {
          $taken = devf_query($conn, "SELECT dev_id FROM tbl_developer WHERE dev_slug = ? LIMIT 1", [$slug . '-' . $n]);
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
    list($extension, $error) = devf_check_upload($_FILES[$field] ?? null, $image[2]);
    if ($error !== '') {
      $errors[] = $error;
    } elseif ($extension !== '') {
      $folder = 'assets/media/developer/' . $image[0];
      if (trim($stored[$field]) !== '') {
        $replaced[] = $folder . basename($stored[$field]);
      }
      $stored[$field] = $image[1] . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
      $to_move[] = [$_FILES[$field]['tmp_name'], $folder . $stored[$field]];
    }
  }
  if (trim($stored['dev_img']) === '' && !$errors) {
    $errors[] = 'Add the developer\'s logo.';
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
      'dev_img' => $stored['dev_img'],
      'dev_img_alt' => $in['dev_img_alt'],
      'dev_homp_img' => $stored['dev_homp_img'],
      'dev_homp_img_alt' => $in['dev_homp_img_alt'],
      'dev_innr_img' => $stored['dev_innr_img'],
      'dev_heading' => $in['dev_heading'],
      'dev_short_descp' => $in['dev_short_descp'],
      'dev_long_descp' => $in['dev_long_descp'],
      'dev_address' => $in['dev_address'],
      'dev_phoneno' => $in['dev_phoneno'],
      'dev_email' => $in['dev_email'],
      'choose_us_img' => $stored['choose_us_img'],
      'choose_us_desc' => $in['choose_us_desc'],
      'dev_posted_on' => date('Y-m-d', $posted_time),
      'dev_slug' => $slug,
      'meta_title' => $in['meta_title'],
      'meta_keyword' => $in['meta_keyword'],
      'meta_description' => $in['meta_description'],
      'meta_tags' => $in['meta_tags'],
      'dev_showonhomepage' => $old['dev_showonhomepage'] === '1' ? '1' : '2',
      'dev_order_no' => $in['dev_order_no'] !== '' ? (int) $in['dev_order_no'] : null,
      'is_active' => $old['is_active'] === '1' ? '1' : '2',
    ];

    if ($editing) {
      $old_data = $logger->getOldRecordData('tbl_developer', $id);
      $assignments = [];
      foreach (array_keys($data) as $column) {
        $assignments[] = "$column = ?";
      }
      // The app copy is out of date after any change, so it is marked for the next push.
      $saved = devf_query($conn, 'UPDATE tbl_developer SET ' . implode(', ', $assignments) . ', app_sync_status = 0 WHERE dev_id = ?', array_merge(array_values($data), [$id]));
    } else {
      $saved = devf_query($conn, 'INSERT INTO tbl_developer (' . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ')', array_values($data));
    }

    if (!$saved) {
      foreach ($to_move as $move) {
        if (is_file($move[1])) {
          @unlink($move[1]);
        }
      }
      $errors[] = 'The developer could not be saved. Please try again.';
    } elseif ($editing) {
      foreach ($replaced as $path) {
        if (is_file($path)) {
          @unlink($path);
        }
      }
      $logger->log('UPDATE', 'tbl_developer', $id, $old_data, $logger->getOldRecordData('tbl_developer', $id), 'Developer Profile Updated : ' . $in['dev_heading']);
      header('Location: ' . BASE_URL . (isset($_POST['save_and_stay']) ? 'edit-developer.php?edit=' . base64_encode($id) . '&saved=1' : 'manage-developer.php?done=saved'));
      exit;
    } else {
      $new_id = (int) mysqli_insert_id($conn);
      $logger->log('INSERT', 'tbl_developer', $new_id, null, $logger->getOldRecordData('tbl_developer', $new_id), 'New Developer added: ' . $in['dev_heading']);
      header('Location: ' . BASE_URL . (isset($_POST['save_and_stay']) ? 'edit-developer.php?edit=' . base64_encode($new_id) . '&saved=1' : 'manage-developer.php?done=added'));
      exit;
    }
  }
}

$just_saved = $editing && $details && isset($_GET['saved']) && !$errors;
$val = function ($field) use (&$old) {
  return devf_e($old[$field] ?? '');
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

              <!--begin::Developer form-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-developers.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1><?= $editing ? 'Edit developer' : 'Add developer' ?></h1>
                    <?php if ($editing): ?>
                      <p><?= $details ? devf_e($details['dev_heading']) . ' &middot; ' . number_format($project_count) . ($project_count === 1 ? ' project' : ' projects') : 'This developer could not be found.' ?></p>
                    <?php else: ?>
                      <p>Fields marked <b>*</b> are needed. Everything else can be filled in later from Edit.</p>
                    <?php endif; ?>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL ?>manage-developer.php"><i class="bi bi-arrow-left"></i> Back to developers</a>
                    <?php if ($details): ?>
                      <a href="<?= devf_e(BASE_URL_HOME . 'developer/' . $details['dev_slug']) ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View on the website</a>
                    <?php endif; ?>
                  </div>
                </div>

                <?php if (!$can_show_form): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This developer could not be found.</strong> It may have been deleted. <a href="<?= BASE_URL ?>manage-developer.php">Back to all developers</a></div>
                  </div>
                <?php elseif ($just_saved): ?>
                  <div class="dre-form-alert dre-form-ok" role="status" id="devfSaved">
                    <i class="bi bi-check-circle"></i>
                    <div><strong>The developer was saved.</strong> <a href="<?= BASE_URL ?>manage-developer.php">All developers</a></div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="devfErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>The developer was not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= devf_e($error) ?></li>
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
                    <a href="#sec-why"><i class="bi bi-hand-thumbs-up"></i> Why choose us</a>
                    <a href="#sec-contact"><i class="bi bi-telephone"></i> Contact</a>
                    <a href="#sec-seo"><i class="bi bi-search"></i> SEO</a>
                    <a href="#sec-publish"><i class="bi bi-send"></i> Publishing</a>
                  </nav>

                  <!--begin::Form-->
                  <form action="<?= BASE_URL ?><?= $editing ? 'edit-developer.php?edit=' . base64_encode($id) : 'add-developer.php' ?>" enctype="multipart/form-data" method="post" id="developer-form" class="dre-form">
                    <input type="hidden" name="csrf" value="<?= devf_e($_SESSION['admin_csrf']) ?>">
                    <input type="hidden" name="save_developer" value="1">

                    <!-- Basics -->
                    <section class="dre-glass dre-form-section" id="sec-basics">
                      <h2><i class="bi bi-card-heading"></i> Basics</h2>
                      <label class="dre-field">
                        <span>Developer name <b>*</b></span>
                        <input type="text" name="dev_heading" id="devfName" value="<?= $val('dev_heading') ?>" maxlength="255" autocomplete="off" placeholder="Emaar Properties" required>
                        <small>Shown as the title of the developer's page and on project pages.</small>
                      </label>
                      <label class="dre-field">
                        <span>Short description</span>
                        <textarea name="dev_short_descp" id="devfShort" rows="3" data-count="200" placeholder="One or two sentences about the developer."><?= $val('dev_short_descp') ?></textarea>
                        <small class="dre-count"></small>
                      </label>
                    </section>

                    <!-- Pictures -->
                    <section class="dre-glass dre-form-section" id="sec-images">
                      <h2><i class="bi bi-images"></i> Pictures</h2>
                      <p class="dre-form-note">JPG, PNG, GIF or WEBP, up to 5 MB each.</p>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Logo <b>*</b></span>
                          <input type="file" name="dev_img" id="devfLogo" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="devfLogoPreview"<?= $details && trim((string) $details['dev_img']) !== '' ? '' : ' required' ?>>
                          <div class="dre-previews" id="devfLogoPreview"></div>
                          <?php if ($details) { devf_current('', $details['dev_img']); } ?>
                        </label>
                        <label class="dre-field">
                          <span>Logo alt text</span>
                          <input type="text" name="dev_img_alt" value="<?= $val('dev_img_alt') ?>" maxlength="255" autocomplete="off" placeholder="Emaar Properties logo">
                          <small>Read out by screen readers and used by search engines.</small>
                        </label>
                      </div>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Homepage image</span>
                          <input type="file" name="dev_homp_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="devfHomePreview">
                          <small>Used in the developers strip on the homepage.</small>
                          <div class="dre-previews" id="devfHomePreview"></div>
                          <?php if ($details) { devf_current('homepage-image/', $details['dev_homp_img']); } ?>
                        </label>
                        <label class="dre-field">
                          <span>Homepage image alt text</span>
                          <input type="text" name="dev_homp_img_alt" value="<?= $val('dev_homp_img_alt') ?>" maxlength="255" autocomplete="off">
                        </label>
                      </div>
                      <label class="dre-field">
                        <span>Inner image</span>
                        <input type="file" name="dev_innr_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="devfInnerPreview">
                        <small>The large image at the top of the developer's page.</small>
                        <div class="dre-previews" id="devfInnerPreview"></div>
                        <?php if ($details) { devf_current('inner-image/', $details['dev_innr_img']); } ?>
                      </label>
                    </section>

                    <!-- About -->
                    <section class="dre-glass dre-form-section" id="sec-about">
                      <h2><i class="bi bi-text-paragraph"></i> About the developer</h2>
                      <div class="dre-field">
                        <label for="kt-tinymce-4"><span>Long description</span></label>
                        <textarea name="dev_long_descp" class="tinymce-editor" id="kt-tinymce-4" rows="10"><?= $val('dev_long_descp') ?></textarea>
                      </div>
                    </section>

                    <!-- Why choose us -->
                    <section class="dre-glass dre-form-section" id="sec-why">
                      <h2><i class="bi bi-hand-thumbs-up"></i> Why choose us</h2>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Image</span>
                          <input type="file" name="choose_us_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="devfWhyPreview">
                          <div class="dre-previews" id="devfWhyPreview"></div>
                          <?php if ($details) { devf_current('choose-us/', $details['choose_us_img']); } ?>
                        </label>
                        <label class="dre-field">
                          <span>Text</span>
                          <textarea name="choose_us_desc" rows="5" placeholder="What makes this developer a good choice."><?= $val('choose_us_desc') ?></textarea>
                        </label>
                      </div>
                      <?php if ($details): ?>
                        <p class="dre-form-note">The list of points under this text is edited on its own page: <a href="<?= BASE_URL ?>manage-chooseus.php?dev_id=<?= (int) $id ?>" target="_blank" rel="noopener">&ldquo;Why choose us&rdquo; points</a>.</p>
                      <?php else: ?>
                        <p class="dre-form-note">The list of points under this text can be added after the developer is saved.</p>
                      <?php endif; ?>
                    </section>

                    <!-- Contact -->
                    <section class="dre-glass dre-form-section" id="sec-contact">
                      <h2><i class="bi bi-telephone"></i> Contact</h2>
                      <label class="dre-field">
                        <span>Address</span>
                        <input type="text" name="dev_address" value="<?= $val('dev_address') ?>" maxlength="255" autocomplete="off">
                      </label>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Phone number</span>
                          <input type="text" name="dev_phoneno" value="<?= $val('dev_phoneno') ?>" maxlength="255" inputmode="tel" autocomplete="off">
                        </label>
                        <label class="dre-field">
                          <span>Email</span>
                          <input type="email" name="dev_email" value="<?= $val('dev_email') ?>" maxlength="255" autocomplete="off">
                        </label>
                      </div>
                    </section>

                    <!-- SEO -->
                    <section class="dre-glass dre-form-section" id="sec-seo">
                      <h2><i class="bi bi-search"></i> SEO</h2>
                      <label class="dre-field">
                        <span>Page address (slug)</span>
                        <input type="text" name="dev_slug" id="devfSlug" value="<?= $val('dev_slug') ?>" maxlength="200" autocomplete="off" placeholder="made from the developer name">
                        <small>The page will be at <code id="devfUrl"><?= devf_e(BASE_URL_HOME) ?>developer/</code><?= $editing ? ' Changing it breaks links to the old address.' : '' ?></small>
                      </label>
                      <label class="dre-field">
                        <span>Meta title</span>
                        <input type="text" name="meta_title" value="<?= $val('meta_title') ?>" maxlength="255" autocomplete="off" data-count="60">
                        <small class="dre-count"></small>
                      </label>
                      <label class="dre-field">
                        <span>Meta description</span>
                        <textarea name="meta_description" id="devfMetaDesc" rows="3" data-count="160"><?= $val('meta_description') ?></textarea>
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
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Posted on <b>*</b></span>
                          <input type="date" name="dev_posted_on" value="<?= $val('dev_posted_on') ?>" required>
                        </label>
                        <label class="dre-toggle">
                          <input type="checkbox" name="is_active" value="1" <?= ($old['is_active'] ?? '') === '1' ? 'checked' : '' ?>>
                          <i></i>
                          <span><strong>Live on the website</strong><small>Off = saved but hidden.</small></span>
                        </label>
                      </div>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-toggle">
                          <input type="checkbox" name="dev_showonhomepage" value="1" id="devfHome" <?= ($old['dev_showonhomepage'] ?? '') === '1' ? 'checked' : '' ?>>
                          <i></i>
                          <span><strong>Show on the homepage</strong><small>Adds this developer to the homepage strip.</small></span>
                        </label>
                        <label class="dre-field" id="devfOrderField">
                          <span>Homepage position</span>
                          <input type="number" name="dev_order_no" value="<?= $val('dev_order_no') ?>" min="1" max="999" inputmode="numeric" placeholder="1">
                          <small>1 comes first.</small>
                        </label>
                      </div>
                    </section>

                    <!-- Always in view: how complete the profile is, and the save buttons -->
                    <div class="dre-form-bar">
                      <div class="dre-form-progress" title="Details that make a developer page complete">
                        <div><strong id="devfDone">0</strong> of <span id="devfOutOf">6</span> key details filled</div>
                        <div class="dre-track"><i id="devfMeter" style="width: 0%"></i></div>
                        <small id="devfMissing"></small>
                      </div>
                      <div class="dre-form-bar-buttons">
                        <button type="submit" name="save_and_stay" value="1" class="dre-btn">Save and keep editing</button>
                        <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Save developer' ?></button>
                      </div>
                    </div>
                  </form>
                  <!--end::Form-->
                </div>
                <?php endif; ?>
              </div>
              <!--end::Developer form-->

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
          // Developer form: helpers that make it quicker to fill in. The server checks everything again on save.
          (function () {
            var form = document.getElementById('developer-form');
            if (!form) {
              return;
            }
            var siteUrl = <?= json_encode(BASE_URL_HOME) ?>;
            var has = <?= json_encode(['logo' => $details && trim((string) $details['dev_img']) !== '', 'inner' => $details && trim((string) $details['dev_innr_img']) !== '']) ?>;
            var dirty = false;

            // --- Page address: follows the name until someone types their own ---
            var nameInput = document.getElementById('devfName');
            var slugInput = document.getElementById('devfSlug');
            var urlPreview = document.getElementById('devfUrl');
            function slugify(text) {
              return text.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            }
            function showUrl() {
              // A saved address is shown as it is; a new one is made from what is typed.
              urlPreview.textContent = siteUrl + 'developer/' + (slugInput.value !== '' ? (slugInput.value === slugInput.defaultValue ? slugInput.value : slugify(slugInput.value)) : slugify(nameInput.value));
            }
            nameInput.addEventListener('input', showUrl);
            slugInput.addEventListener('input', showUrl);
            showUrl();

            // --- Homepage position only when the homepage switch is on ---
            var homeSwitch = document.getElementById('devfHome');
            var orderField = document.getElementById('devfOrderField');
            function toggleHome() { orderField.style.display = homeSwitch.checked ? '' : 'none'; }
            homeSwitch.addEventListener('change', toggleHome);
            toggleHome();

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

            // --- How complete the profile is ---
            function editorText(id) {
              var editor = window.tinymce && window.tinymce.get(id);
              var html = editor ? editor.getContent() : document.getElementById(id).value;
              return html.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
            }
            function fileChosen(name, saved) {
              return form.querySelector('[name="' + name + '"]').files.length > 0 || saved;
            }
            function progress() {
              var checks = [
                ['name', nameInput.value.trim() !== ''],
                ['logo', fileChosen('dev_img', has.logo)],
                ['inner image', fileChosen('dev_innr_img', has.inner)],
                ['short description', document.getElementById('devfShort').value.trim() !== ''],
                ['long description', editorText('kt-tinymce-4') !== ''],
                ['meta description', document.getElementById('devfMetaDesc').value.trim() !== '']
              ];
              var done = checks.filter(function (check) { return check[1]; }).length;
              document.getElementById('devfDone').textContent = done;
              document.getElementById('devfOutOf').textContent = checks.length;
              document.getElementById('devfMeter').style.width = Math.round(done * 100 / checks.length) + '%';
              var missing = checks.filter(function (check) { return !check[1]; }).map(function (check) { return check[0]; });
              document.getElementById('devfMissing').textContent = missing.length ? 'Still empty: ' + missing.join(', ') : 'All key details are filled in.';
            }
            form.addEventListener('input', function () { dirty = true; progress(); });
            form.addEventListener('change', function () { dirty = true; progress(); });
            progress();
            // The editor loads after this script, so look again once it is ready.
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

            if (document.getElementById('devfSaved') && window.history.replaceState) {
              // Reloading the page should not show "saved" again.
              window.history.replaceState(null, '', window.location.href.replace(/&saved=1/, ''));
            }
            var errorBox = document.getElementById('devfErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
