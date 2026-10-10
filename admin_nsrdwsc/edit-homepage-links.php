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

function set_e($text)
{
  return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function set_query($conn, $sql, array $params = [])
{
  try {
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
      error_log('Settings query failed: ' . mysqli_error($conn));
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
      error_log('Settings query failed: ' . mysqli_stmt_error($stmt));
      mysqli_stmt_close($stmt);
      return false;
    }
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
  } catch (mysqli_sql_exception $error) {
    // This server reports database errors as exceptions.
    error_log('Settings query failed: ' . $error->getMessage());
    return false;
  }
  if ($result === false) {
    return true;
  }
  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }
  return $rows;
}

// Picture types by what the file really contains: real type => extension it is stored with.
const SET_IMAGE_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
const SET_ICON_TYPES = ['image/vnd.microsoft.icon' => 'ico', 'image/x-icon' => 'ico'];
const SET_IMAGE_MAX = 5 * 1024 * 1024;

// Checks one uploaded picture by extension and by its real content.
// Returns [extension to store it with, error]; the extension is '' when no file was chosen.
function set_check_upload($file, $label, $allow_icon = false)
{
  if (!is_array($file) || is_array($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE || (string) $file['name'] === '') {
    return ['', ''];
  }
  if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > SET_IMAGE_MAX) {
    return ['', "$label is too large (maximum 5 MB)."];
  }
  if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
    return ['', "$label could not be uploaded. Please try again."];
  }
  $types = SET_IMAGE_TYPES + ($allow_icon ? SET_ICON_TYPES : []);
  $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $real = $finfo->file($file['tmp_name']);
  if (!in_array($extension === 'jpeg' ? 'jpg' : $extension, $types, true) || !isset($types[$real])) {
    return ['', "$label must be one of: " . strtoupper(implode(', ', array_unique(array_values($types)))) . '.'];
  }
  return [$types[$real], ''];
}

// What this page works on: its table, its columns and where it shows on the website.
$entity = [
  'one' => 'homepage link',
  'many' => 'homepage links',
  'title' => 'Homepage links',
  'blurb' => 'Named links to pages of the website, such as "Residences by Sobha Realty".',
  'icon' => 'bi-link-45deg',
  'table' => 'tbl_homepage_links',
  'id' => 'homepage_link_id',
  'heading' => 'homepage_link_name',
  'public_path' => '',
  'public_slug' => '',
  'page_label' => 'Home page: a link to a property listing page',
  'active_label' => 'Marked as live',
  'active_off' => 'Off = saved but hidden.',
  'site_note' => 'No page of the website shows these links at the moment. They are kept here in case the home page uses them again.',
  'profile' => '',
  'list_file' => 'manage-homepage-links.php', 'add_file' => 'add-homepage-links.php', 'edit_file' => 'edit-homepage-links.php',
  'head_label' => 'Link', 'summary' => 'homepage_link_url', 'thumb' => '', 'folder' => '', 'icon_col' => '',
  'search' => ['homepage_link_name', 'homepage_link_url'],
  'missing' => [],
  'in_use_sql' => "", 'in_use_note' => '',
  'content_file' => '',
  'links' => [],
  'sections' => [['The link', 'bi-link-45deg', '']],
  'fields' => [
  ['name' => 'name', 'col' => 'homepage_link_name', 'label' => 'Text of the link', 'type' => 'text', 'required' => true, 'placeholder' => 'Residences by Sobha Realty', 'ai' => 'heading'],
  ['name' => 'url', 'col' => 'homepage_link_url', 'label' => 'Where it goes', 'type' => 'link', 'required' => true, 'placeholder' => 'developer/sobha-realty', 'help' => 'A path on this website, for example "developer/sobha-realty", or a full address starting with https://'],
  ],
];

// This file is one of a set made from the same form: this copy changes one (add-homepage-links.php adds one).
$mode = 'edit';   // 'add', 'edit', or 'single' for a page that has exactly one record
$editing = $mode !== 'add';
$t = $entity['table'];
$id_col = $entity['id'];

// "Mortgage Advisory" -> "mortgage-advisory"
function ent_slug($text)
{
  return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(trim((string) $text))), '-');
}
// Text with its tags and empty spaces taken out, to tell whether an editor field really has words in it.
function ent_plain($html)
{
  return trim(preg_replace('/\s+/', ' ', str_replace("\xC2\xA0", ' ', html_entity_decode(strip_tags((string) $html), ENT_QUOTES, 'UTF-8'))));
}

// Checks one uploaded video by extension and by its real content. Returns [extension, error].
const ENT_VIDEO_TYPES = ['video/mp4' => 'mp4', 'video/webm' => 'webm'];
const ENT_VIDEO_MAX = 30 * 1024 * 1024;
function ent_check_video($file, $label)
{
  if (!is_array($file) || is_array($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE || (string) $file['name'] === '') {
    return ['', ''];
  }
  if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > ENT_VIDEO_MAX) {
    return ['', "$label is too large (maximum 30 MB)."];
  }
  if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
    return ['', "$label could not be uploaded. Please try again."];
  }
  $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $real = $finfo->file($file['tmp_name']);
  if (!in_array($extension, ENT_VIDEO_TYPES, true) || !isset(ENT_VIDEO_TYPES[$real])) {
    return ['', "$label must be an MP4 or WEBM video."];
  }
  return [ENT_VIDEO_TYPES[$real], ''];
}

// Rich-text fields use the editors this panel already starts on these ids, in this order.
$editor_ids = ['kt-tinymce-4', 'kt-tinymce-2', 'kt-tinymce-5', 'kt-tinymce-6', 'kt-tinymce-7'];
$dom_ids = [];      // field name => id of its form control
$fields = [];       // field name => its settings
foreach ($entity['fields'] as $field) {
  $field += ['max' => 255, 'required' => false, 'help' => '', 'placeholder' => '', 'ai' => '', 'section' => $entity['sections'][0][0], 'rows' => 3, 'count' => 0, 'folder' => '', 'options_sql' => '', 'from' => '', 'alt' => '', 'empty' => null, 'choices' => ''];
  $fields[$field['name']] = $field;
  $dom_ids[$field['name']] = $field['type'] === 'rich' ? array_shift($editor_ids) : 'f_' . $field['name'];
}

$id = 0;
$details = null;
if ($editing) {
  $id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
  $details = $id > 0 ? (set_query($conn, "SELECT * FROM $t WHERE $id_col = ?", [$id]) ?: []) : [];
  $details = $details ? $details[0] : null;
}
$can_show_form = !$editing || $details;

// A page can ask for the two-column layout with a summary card: 'profile' => 'field:Label|field:Label'
// lists the fields the card ticks off as they are filled in.
$profile = [];
foreach (array_filter(explode('|', (string) ($entity['profile'] ?? ''))) as $pair) {
  list($profile_field, $profile_label) = explode(':', $pair, 2);
  $profile[$profile_field] = $profile_label;
}

// Choices of the drop-down fields: field name => [value => label]
$options = [];
foreach ($fields as $name => $field) {
  if ($field['type'] === 'choice') {
    $options[$name] = [];
    foreach (explode('|', $field['choices']) as $pair) {
      list($value, $label) = explode(':', $pair, 2);
      $options[$name][$value] = $label;
    }
  }
  if ($field['type'] === 'select') {
    $options[$name] = [];
    foreach (set_query($conn, $field['options_sql']) ?: [] as $row) {
      $row = array_values($row);
      $options[$name][(string) $row[0]] = (string) $row[1];
    }
  }
}

$errors = [];
$old = ['is_active' => $details ? ($details['is_active'] === '1' ? '1' : '') : '1'];
foreach ($fields as $name => $field) {
  if (in_array($field['type'], ['image', 'video'], true)) {
    continue;
  }
  $old[$name] = $details ? (string) $details[$field['col']] : '';
  if ($field['type'] === 'toggle') {
    $old[$name] = $details && (string) $details[$field['col']] === '1' ? '1' : '';
  }
  if ($field['type'] === 'date') {
    // "1970-01-01" is what an empty date was saved as.
    $time = $details ? strtotime((string) $details[$field['col']]) : time();
    $old[$name] = $time && $time > 86400 ? date('Y-m-d', $time) : ($details ? '' : date('Y-m-d'));
  }
}

if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_entity'])) {

  // ---------------------------------------------------------------
  // 1. Read the form
  // ---------------------------------------------------------------
  foreach ($fields as $name => $field) {
    if (in_array($field['type'], ['image', 'video'], true)) {
      continue;
    }
    $value = isset($_POST[$name]) && is_string($_POST[$name]) ? trim($_POST[$name]) : '';
    if ($field['type'] === 'toggle') {
      $value = isset($_POST[$name]) ? '1' : '';
    }
    $old[$name] = in_array($field['type'], ['rich', 'textarea'], true) && $field['max'] === 0 ? $value : mb_substr($value, 0, $field['max'] > 0 ? $field['max'] : 255);
    if ($field['type'] === 'textarea' && $field['max'] > 0) {
      $old[$name] = mb_substr($value, 0, $field['max']);
    }
  }
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';

  // ---------------------------------------------------------------
  // 2. Check it. Nothing is saved and no file is kept until everything is right.
  // ---------------------------------------------------------------
  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  $data = [];
  foreach ($fields as $name => $field) {
    if (in_array($field['type'], ['image', 'video'], true)) {
      continue;
    }
    $value = $old[$name];
    $empty = $field['type'] === 'rich' ? ent_plain($value) === '' && stripos($value, '<img') === false : $value === '';
    if ($field['required'] && $empty && $field['type'] !== 'slug') {
      $errors[] = $field['label'] . ' is needed.';
    }
    if ($field['type'] === 'date') {
      $time = $value !== '' ? strtotime($value) : false;
      if ($value !== '' && $time === false) {
        $errors[] = $field['label'] . ': choose a date.';
      }
      $value = $time ? date('Y-m-d', $time) : date('Y-m-d');
    } elseif ($field['type'] === 'select') {
      if ($value !== '' && !isset($options[$name][$value])) {
        $errors[] = $field['label'] . ': choose one from the list.';
      }
      $value = $value === '' ? null : (ctype_digit($value) ? (int) $value : $value);
    } elseif ($field['type'] === 'choice') {
      if (!isset($options[$name][$value])) {
        $errors[] = $field['label'] . ': choose one from the list.';
      }
    } elseif ($field['type'] === 'toggle') {
      $value = $value === '1' ? '1' : '2';
    } elseif ($field['type'] === 'number') {
      if ($value !== '' && !preg_match('/^\d{1,9}$/', $value)) {
        $errors[] = $field['label'] . ': type a whole number.';
      }
      $value = $value === '' ? $field['empty'] : (int) $value;
    } elseif ($field['type'] === 'link') {
      // A full web address, or a path on this website such as "developer/emaar".
      if ($value !== '' && (preg_match('/[\s<>"\']/', $value) || preg_match('/^(?!https?:)[a-z][a-z0-9+.-]*:/i', $value))) {
        $errors[] = $field['label'] . ': type a web address without spaces, starting with https:// or a path on this website.';
      }
    } elseif ($field['type'] === 'email') {
      if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
        $errors[] = $field['label'] . ': type a full email address.';
      }
    } elseif ($field['type'] === 'icon') {
      // A list of class names, for example "fal fa-star". Nothing else is accepted.
      if ($value !== '' && (!preg_match('/^[a-z0-9-]+( [a-z0-9-]+){0,3}$/', $value) || !preg_match('/\bfa-[a-z0-9-]+$/', $value))) {
        $errors[] = $field['label'] . ': type the class names of an icon, for example "fal fa-star".';
      }
    } elseif ($field['type'] === 'slug') {
      // A saved address is left exactly as it is unless it was changed here, because changing
      // it changes the page's link. A new one is made from the name when left empty.
      $saved = $details ? (string) $details[$field['col']] : '';
      if (!($saved !== '' && $value === $saved)) {
        $typed = $value !== '';
        $value = ent_slug($typed ? $value : ($saved !== '' ? $saved : ($old[$field['from']] ?? '')));
        if ($value === '' && ($old[$field['from']] ?? '') !== '') {
          $errors[] = $field['label'] . ' needs letters or numbers.';
        } elseif ($value !== '' && set_query($conn, "SELECT $id_col FROM $t WHERE TRIM({$field['col']}) = ? AND $id_col <> ? AND is_active = '1' LIMIT 1", [$value, $id])) {
          $errors[] = 'Another live ' . $entity['one'] . ' already uses the page address "' . $value . '". Choose a different one, or hide the other one first.';
        }
      }
    }
    $data[$field['col']] = $value;
  }

  $to_move = [];      // [temporary file, final path]
  $replaced = [];     // saved files that a new upload replaces
  foreach ($fields as $name => $field) {
    if (!in_array($field['type'], ['image', 'video'], true)) {
      continue;
    }
    $saved = $details ? (string) $details[$field['col']] : '';
    $data[$field['col']] = $saved;
    list($extension, $error) = $field['type'] === 'video' ? ent_check_video($_FILES[$name] ?? null, $field['label']) : set_check_upload($_FILES[$name] ?? null, $field['label']);
    if ($error !== '') {
      $errors[] = $error;
    } elseif ($extension !== '') {
      if (trim($saved) !== '') {
        $replaced[] = $field['folder'] . basename($saved);
      }
      $data[$field['col']] = $field['col'] . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
      $to_move[] = [$_FILES[$name]['tmp_name'], $field['folder'] . $data[$field['col']]];
    } elseif ($field['required'] && trim($saved) === '') {
      $errors[] = $field['label'] . ' is needed.';
    }
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
    $data['is_active'] = $old['is_active'] === '1' ? '1' : '2';
    if ($editing) {
      $old_data = $logger->getOldRecordData($t, $id);
      $assignments = [];
      foreach (array_keys($data) as $column) {
        $assignments[] = "$column = ?";
      }
      $saved = set_query($conn, "UPDATE $t SET " . implode(', ', $assignments) . ", tstp = NOW() WHERE $id_col = ?", array_merge(array_values($data), [$id]));
      $record_id = $id;
    } else {
      $saved = set_query($conn, "INSERT INTO $t (" . implode(', ', array_keys($data)) . ', tstp) VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ', NOW())', array_values($data));
      $record_id = $saved ? (int) mysqli_insert_id($conn) : 0;
    }

    if (!$saved) {
      foreach ($to_move as $move) {
        if (is_file($move[1])) {
          @unlink($move[1]);
        }
      }
      $errors[] = 'This could not be saved. Please try again.';
    } else {
      foreach ($replaced as $path) {
        if (is_file($path)) {
          @unlink($path);
        }
      }
      $logger->log($editing ? 'UPDATE' : 'INSERT', $t, $record_id, $editing ? $old_data : null, $logger->getOldRecordData($t, $record_id), $entity['title'] . ($editing ? ' updated' : ' added') . ($entity['heading'] !== '' ? ': ' . ($data[$entity['heading']] ?? '') : ''));
      if ($mode === 'single') {
        header('Location: ' . BASE_URL . $entity['edit_file'] . '?edit=' . base64_encode($id) . '&saved=1');
      } else {
        header('Location: ' . BASE_URL . $entity['list_file'] . '?done=' . ($editing ? 'saved' : 'added'));
      }
      exit;
    }
  }
}

$just_saved = $mode === 'single' && $details && isset($_GET['saved']) && !$errors;
$public_url = $entity['public_path'] !== '' ? BASE_URL_HOME . $entity['public_path'] : '';
// Fields the AI helper reads to write a suggestion: the name field, and every longer text.
$ai_title = '';
$ai_texts = [];
foreach ($fields as $name => $field) {
  if ($field['col'] === $entity['heading'] || ($ai_title === '' && $entity['heading'] === '' && $field['type'] === 'text')) {
    $ai_title = '#' . $dom_ids[$name];
  }
  if (in_array($field['type'], ['rich', 'textarea'], true) && $field['ai'] !== 'meta_description' && $field['ai'] !== 'keywords') {
    $ai_texts[] = '#' . $dom_ids[$name];
  }
}
$ai_labels = ['meta_title' => 'Suggest a title', 'meta_description' => 'Suggest a description', 'short_description' => 'Suggest a short description', 'keywords' => 'Suggest keywords', 'alt_text' => 'Suggest a description', 'heading' => 'Suggest a heading', 'improve' => 'Make this text clearer'];
$uses_ai = (bool) array_filter(array_column($fields, 'ai'));
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
              <!--begin::Form-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-settings.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-careers.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-ai-assist.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-webpages.css?v=3" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-settings-form">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1><?= $mode === 'single' ? set_e($entity['title']) : ($editing ? 'Edit ' : 'Add ') . set_e($entity['one']) ?></h1>
                    <p><?= $mode === 'single' ? set_e($entity['blurb']) : ($editing ? ($details ? set_e($entity['heading'] !== '' ? $details[$entity['heading']] : $entity['title']) : 'This ' . set_e($entity['one']) . ' could not be found.') : 'Fields marked <b>*</b> are needed.') ?></p>
                  </div>
                  <div class="dre-actions">
                    <?php if ($mode !== 'single'): ?>
                      <a href="<?= BASE_URL . $entity['list_file'] ?>"><i class="bi bi-arrow-left"></i> Back to <?= set_e($entity['many']) ?></a>
                    <?php endif; ?>
                    <?php foreach ($entity['links'] ?? [] as $link): ?>
                      <a href="<?= BASE_URL . $link[1] ?>"><i class="bi <?= set_e($link[2]) ?>"></i> <?= set_e($link[0]) ?></a>
                    <?php endforeach; ?>
                    <?php if ($public_url !== '' && ($mode === 'single' || $details)): ?>
                      <a href="<?= set_e($public_url . (!empty($entity['public_slug']) && $details ? trim((string) $details[$entity['public_slug']]) : '')) ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View the page</a>
                    <?php endif; ?>
                  </div>
                </div>

                <?php if ($entity['site_note'] !== ''): ?>
                  <div class="dre-form-alert dre-form-ok dre-site-note" role="note">
                    <i class="bi bi-eye-slash"></i>
                    <div><?= set_e($entity['site_note']) ?></div>
                  </div>
                <?php endif; ?>

                <?php if (!$can_show_form): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This <?= set_e($mode === 'single' ? 'content' : $entity['one']) ?> could not be found.</strong> <?php if ($mode !== 'single'): ?><a href="<?= BASE_URL . $entity['list_file'] ?>">Back to <?= set_e($entity['many']) ?></a><?php else: ?>Open it from the menu.<?php endif; ?></div>
                  </div>
                <?php elseif ($just_saved): ?>
                  <div class="dre-form-alert dre-form-ok" role="status" id="entSaved">
                    <i class="bi bi-check-circle"></i>
                    <div><strong>The changes were saved.</strong></div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="entErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>Not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= set_e($error) ?></li>
                        <?php endforeach; ?>
                      </ul>
                      <em>Pictures have to be chosen again: a browser does not keep them after an error.</em>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($can_show_form): ?>
                <form action="<?= BASE_URL ?><?= $editing ? $entity['edit_file'] . '?edit=' . base64_encode($id) : $entity['add_file'] ?>" enctype="multipart/form-data" method="post" id="entity-form" class="dre-form">
                  <input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
                  <input type="hidden" name="save_entity" value="1">
                  <?php if ($profile): ?><div class="dre-profile-layout"><div class="dre-profile-main"><?php endif; ?>

                  <?php foreach ($entity['sections'] as $section):
                    $section_fields = array_filter($fields, function ($field) use ($section) { return $field['section'] === $section[0]; });
                    if (!$section_fields) {
                      continue;
                    }
                  ?>
                    <section class="dre-glass dre-form-section" id="sec-<?= set_e(ent_slug($section[0])) ?>">
                      <h2><i class="bi <?= set_e($section[1]) ?>"></i> <?= set_e($section[0]) ?></h2>
                      <?php if (!empty($section[2])): ?><p class="dre-form-note"><?= set_e($section[2]) ?></p><?php endif; ?>
                      <?php foreach ($section_fields as $name => $field):
                        $dom_id = $dom_ids[$name];
                        $star = $field['required'] ? ' <b>*</b>' : '';
                        $ai_button = $field['ai'] !== '' ? '<button type="button" class="dre-ai-btn" data-ai-task="' . set_e($field['ai']) . '" data-ai-target="#' . set_e($dom_id) . '" data-ai-title="' . set_e($ai_title) . '" data-ai-text="' . set_e(implode(',', $field['ai'] === 'improve' ? [] : $ai_texts)) . '" data-ai-page="' . set_e($entity['page_label']) . '"><i class="bi bi-stars"></i> ' . set_e($ai_labels[$field['ai']] ?? 'Suggest with AI') . '</button>' : '';
                      ?>
                        <?php if ($profile): ?><div class="dre-f dre-f-<?= set_e($field['type']) ?><?= !empty($field['wide']) ? ' dre-f-wide' : '' ?>" data-f="<?= set_e($name) ?>"><?php endif; ?>
                        <?php if ($field['type'] === 'rich'): ?>
                          <div class="dre-field">
                            <label for="<?= set_e($dom_id) ?>"><span><?= set_e($field['label']) ?><?= $star ?></span></label>
                            <textarea name="<?= set_e($name) ?>" class="tinymce-editor" id="<?= set_e($dom_id) ?>" rows="12"><?= set_e($old[$name]) ?></textarea>
                            <?php if ($field['help'] !== ''): ?><small><?= set_e($field['help']) ?></small><?php endif; ?>
                            <?= $ai_button ?>
                          </div>
                        <?php elseif ($field['type'] === 'textarea'): ?>
                          <div class="dre-field">
                            <label for="<?= set_e($dom_id) ?>"><span><?= set_e($field['label']) ?><?= $star ?></span></label>
                            <textarea name="<?= set_e($name) ?>" id="<?= set_e($dom_id) ?>" rows="<?= (int) $field['rows'] ?>"<?= $field['max'] > 0 ? ' maxlength="' . (int) $field['max'] . '"' : '' ?><?= $field['count'] > 0 ? ' data-count="' . (int) $field['count'] . '"' : '' ?> placeholder="<?= set_e($field['placeholder']) ?>"<?= $field['required'] ? ' required' : '' ?>><?= set_e($old[$name]) ?></textarea>
                            <?php if ($field['count'] > 0): ?><small class="dre-count"></small><?php endif; ?>
                            <?php if ($field['help'] !== ''): ?><small><?= set_e($field['help']) ?></small><?php endif; ?>
                            <?= $ai_button ?>
                          </div>
                        <?php elseif ($field['type'] === 'image'):
                          $saved_name = $details ? trim((string) $details[$field['col']]) : '';
                          $saved_url = $saved_name !== '' ? BASE_URL . $field['folder'] . rawurlencode($saved_name) : '';
                        ?>
                          <div class="dre-field">
                            <label for="<?= set_e($dom_id) ?>"><span><?= set_e($field['label']) ?><?= $star ?></span></label>
                            <input type="file" name="<?= set_e($name) ?>" id="<?= set_e($dom_id) ?>" accept="image/jpeg,image/png,image/webp,image/gif" class="dre-set-picture"<?= $field['required'] && $saved_name === '' ? ' required' : '' ?>>
                            <small><?= set_e($field['help']) ?> JPG, PNG, WEBP or GIF, up to 5 MB.<?= $saved_name !== '' ? ' Leave empty to keep the current one.' : '' ?></small>
                            <div class="dre-current"<?= $saved_name === '' ? ' hidden' : '' ?>>
                              <img src="<?= set_e($saved_url) ?>" alt="" data-saved="<?= set_e($saved_url) ?>">
                              <span><?= $saved_name !== '' ? 'Current picture.' : '' ?></span>
                            </div>
                          </div>
                        <?php elseif ($field['type'] === 'video'):
                          $saved_name = $details ? trim((string) $details[$field['col']]) : '';
                          $saved_url = $saved_name !== '' ? BASE_URL . $field['folder'] . rawurlencode($saved_name) : '';
                        ?>
                          <div class="dre-field">
                            <label for="<?= set_e($dom_id) ?>"><span><?= set_e($field['label']) ?><?= $star ?></span></label>
                            <input type="file" name="<?= set_e($name) ?>" id="<?= set_e($dom_id) ?>" accept="video/mp4,video/webm">
                            <small><?= set_e($field['help']) ?> MP4 or WEBM, up to 30 MB.<?= $saved_name !== '' ? ' Leave empty to keep the current one.' : '' ?></small>
                            <?php if ($saved_name !== ''): ?>
                              <div class="dre-current dre-current-video">
                                <video src="<?= set_e($saved_url) ?>" controls preload="none" muted></video>
                                <span>Current video.</span>
                              </div>
                            <?php endif; ?>
                          </div>
                        <?php elseif ($field['type'] === 'toggle'): ?>
                          <label class="dre-toggle dre-field-toggle">
                            <input type="checkbox" name="<?= set_e($name) ?>" value="1" id="<?= set_e($dom_id) ?>" <?= $old[$name] === '1' ? 'checked' : '' ?>>
                            <i></i>
                            <span><strong><?= set_e($field['label']) ?></strong><small><?= set_e($field['help']) ?></small></span>
                          </label>
                        <?php elseif ($field['type'] === 'select' || $field['type'] === 'choice'): ?>
                          <label class="dre-field">
                            <span><?= set_e($field['label']) ?><?= $star ?></span>
                            <select name="<?= set_e($name) ?>" id="<?= set_e($dom_id) ?>"<?= $field['required'] ? ' required' : '' ?>>
                              <option value="">Choose one</option>
                              <?php foreach ($options[$name] as $value => $label): ?>
                                <option value="<?= set_e($value) ?>" <?= (string) $old[$name] === (string) $value ? 'selected' : '' ?>><?= set_e($label) ?></option>
                              <?php endforeach; ?>
                            </select>
                            <?php if ($field['help'] !== ''): ?><small><?= set_e($field['help']) ?></small><?php endif; ?>
                          </label>
                        <?php elseif ($field['type'] === 'icon'):
                          $icon_name = preg_match('/\bfa-(?!light|regular|thin|duotone|solid|brands)[a-z0-9-]+/', $old[$name], $match) ? $match[0] : '';
                        ?>
                          <div class="dre-field">
                            <label for="<?= set_e($dom_id) ?>"><span><?= set_e($field['label']) ?><?= $star ?></span></label>
                            <span class="dre-link-row"><i class="fas <?= set_e($icon_name) ?>" aria-hidden="true" data-icon-preview></i>
                              <input type="text" name="<?= set_e($name) ?>" id="<?= set_e($dom_id) ?>" value="<?= set_e($old[$name]) ?>" maxlength="255" autocomplete="off" spellcheck="false" placeholder="fal fa-star" class="dre-ent-icon"<?= $field['required'] ? ' required' : '' ?>>
                            </span>
                            <small>The class names of a Font Awesome icon, for example &ldquo;fal fa-wallet&rdquo;. The website draws it in its light style; here it is shown in the solid style.</small>
                          </div>
                        <?php else: ?>
                          <div class="dre-field">
                            <label for="<?= set_e($dom_id) ?>"><span><?= set_e($field['label']) ?><?= $star ?></span></label>
                            <input type="<?= $field['type'] === 'date' ? 'date' : ($field['type'] === 'number' ? 'number' : 'text') ?>"<?= $field['type'] === 'number' ? ' min="0" step="1" inputmode="numeric"' : '' ?> name="<?= set_e($name) ?>" id="<?= set_e($dom_id) ?>" value="<?= set_e($old[$name]) ?>"<?= $field['type'] === 'date' ? '' : ' maxlength="' . (int) $field['max'] . '" autocomplete="off" placeholder="' . set_e($field['placeholder']) . '"' ?><?= $field['count'] > 0 ? ' data-count="' . (int) $field['count'] . '"' : '' ?><?= $field['type'] === 'slug' ? ' data-slug-from="#' . set_e($dom_ids[$field['from']] ?? '') . '" spellcheck="false"' : '' ?><?= $field['required'] && $field['type'] !== 'slug' ? ' required' : '' ?>>
                            <?php if ($field['count'] > 0): ?><small class="dre-count"></small><?php endif; ?>
                            <?php if ($field['type'] === 'slug'): ?>
                              <small>The page will be at <code data-slug-preview="<?= set_e($public_url) ?>"><?= set_e($public_url) ?></code><?= $editing ? ' Changing it breaks links to the old address.' : ' Leave empty to make it from the name.' ?></small>
                            <?php elseif ($field['help'] !== ''): ?>
                              <small><?= set_e($field['help']) ?></small>
                            <?php endif; ?>
                            <?= $ai_button ?>
                          </div>
                        <?php endif; ?>
                        <?php if ($profile): ?></div><?php endif; ?>
                      <?php endforeach; ?>
                    </section>
                  <?php endforeach; ?>

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-send"></i> Publishing</h2>
                    <label class="dre-toggle">
                      <input type="checkbox" name="is_active" value="1" id="entActive" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
                      <i></i>
                      <span><strong><?= set_e($entity['active_label']) ?></strong><small><?= set_e($entity['active_off']) ?></small></span>
                    </label>
                  </section>

                  <?php if ($profile):
                    $photo_name = isset($fields['image']) && $details ? trim((string) $details[$fields['image']['col']]) : '';
                    $photo_url = $photo_name !== '' ? BASE_URL . $fields['image']['folder'] . rawurlencode($photo_name) : '';
                    // A page with a banner shows it as the cover of the card.
                    $cover_name = isset($fields['bg_image']) && $details ? trim((string) $details[$fields['bg_image']['col']]) : '';
                    $cover_url = $cover_name !== '' ? BASE_URL . $fields['bg_image']['folder'] . rawurlencode($cover_name) : '';
                  ?>
                    </div>
                    <aside class="dre-profile-side">
                      <div class="dre-glass dre-profile-card<?= isset($fields['bg_image']) ? ' has-cover' : '' ?>" id="profCard" data-fallback="<?= set_e($mode === 'single' ? $entity['title'] : 'No name yet') ?>">
                        <?php if (isset($fields['bg_image'])): ?>
                          <div class="dre-profile-cover" id="profCover"<?= $cover_url !== '' ? ' style="background-image: url(\'' . set_e($cover_url) . '\')"' : '' ?>></div>
                        <?php endif; ?>
                        <div class="dre-profile-photo" id="profPhoto"><?php if ($photo_url !== ''): ?><img src="<?= set_e($photo_url) ?>" alt=""><?php else: ?><i class="bi bi-person"></i><?php endif; ?></div>
                        <strong id="profName"></strong>
                        <span id="profDesg"></span>
                        <span class="dre-pill" id="profLive"></span>
                        <div class="dre-profile-meter" role="img" aria-label="How complete the profile is"><div id="profBar"></div></div>
                        <p id="profCount"></p>
                        <ul class="dre-profile-checks">
                          <?php foreach ($profile as $profile_field => $profile_label): ?>
                            <li data-check="<?= set_e($profile_field) ?>"><i class="bi bi-circle"></i> <a href="#<?= set_e($dom_ids[$profile_field] ?? '') ?>"><?= set_e($profile_label) ?></a></li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                      <nav class="dre-glass dre-profile-nav" aria-label="Sections of this form">
                        <?php foreach ($entity['sections'] as $section): ?>
                          <a href="#sec-<?= set_e(ent_slug($section[0])) ?>"><i class="bi <?= set_e($section[1]) ?>"></i> <?= set_e($section[0]) ?></a>
                        <?php endforeach; ?>
                      </nav>
                    </aside>
                    </div>
                  <?php endif; ?>

                  <div class="dre-form-bar">
                    <div class="dre-form-progress"><div id="entState"><?= $editing ? 'No unsaved changes' : 'Not saved yet' ?></div></div>
                    <div class="dre-form-bar-buttons">
                      <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Save ' . set_e($entity['one']) ?></button>
                    </div>
                  </div>
                </form>
                <?php endif; ?>
              </div>
              <!--end::Form-->

            </div>
            <!--end::Container-->
          </div>
          <!--end::Entry-->
        </div>
        <!--end::Content-->

        <?php
        include('includes/footer-inner.php');
        ?>

        <?php if ($uses_ai && $can_show_form): ?>
          <script>
            // Where the "Suggest with AI" buttons send their requests. The key stays on the server.
            window.DRE_AI_ASSIST = <?= json_encode(['endpoint' => BASE_URL . 'ajax/ai-assist.php', 'csrf' => $_SESSION['admin_csrf']], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
          </script>
          <script src="<?= BASE_URL ?>assets/js/dre-ai-assist.js?v=1"></script>
        <?php endif; ?>
        <script>
          // Form helpers: character counts, the page address, picture previews, and a warning before leaving unsaved changes.
          (function () {
            var form = document.getElementById('entity-form');
            if (!form) {
              return;
            }
            var single = <?= json_encode($mode === 'single') ?>;
            var dirty = false;
            var state = document.getElementById('entState');
            function changed() {
              dirty = true;
              state.textContent = 'Unsaved changes';
              state.classList.add('dre-warn-text');
            }

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

            // The page address follows the name until someone types their own.
            form.querySelectorAll('[data-slug-from]').forEach(function (slug) {
              var source = document.querySelector(slug.getAttribute('data-slug-from'));
              var preview = slug.parentNode.querySelector('[data-slug-preview]');
              function slugify(text) {
                return text.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
              }
              function show() {
                // A saved address is shown as it is; a new one is made from what is typed.
                var value = slug.value !== '' ? (slug.value === slug.defaultValue ? slug.value : slugify(slug.value)) : (source ? slugify(source.value) : '');
                preview.textContent = preview.getAttribute('data-slug-preview') + value;
              }
              slug.addEventListener('input', show);
              if (source) { source.addEventListener('input', show); }
              show();
            });

            form.querySelectorAll('.dre-ent-icon').forEach(function (input) {
              var preview = input.parentNode.querySelector('[data-icon-preview]');
              input.addEventListener('input', function () {
                var match = /(?:^|\s)(fa-(?!light|regular|thin|duotone|solid|brands)[a-z0-9-]+)/.exec(input.value.trim());
                preview.className = 'fas ' + (match ? match[1] : '');
              });
            });

            form.querySelectorAll('.dre-set-picture').forEach(function (input) {
              var box = input.parentNode.querySelector('.dre-current');
              var img = box.querySelector('img');
              var note = box.querySelector('span');
              var savedNote = note.textContent;
              input.addEventListener('change', function () {
                var file = input.files[0];
                var bad = '';
                if (file && ['image/jpeg', 'image/png', 'image/webp', 'image/gif'].indexOf(file.type) === -1) {
                  bad = '"' + file.name + '" is not a JPG, PNG, WEBP or GIF picture.';
                } else if (file && file.size > 5 * 1024 * 1024) {
                  bad = '"' + file.name + '" is larger than 5 MB.';
                }
                input.setCustomValidity(bad);
                input.classList.toggle('dre-invalid', bad !== '');
                if (bad !== '') { input.reportValidity(); }
                if (file && bad === '') {
                  img.src = URL.createObjectURL(file);
                  note.textContent = 'New picture, not saved yet.';
                  box.hidden = false;
                } else {
                  img.src = img.getAttribute('data-saved');
                  note.textContent = savedNote;
                  box.hidden = img.getAttribute('data-saved') === '';
                }
              });
            });

            form.addEventListener('input', function (event) { event.target.classList.remove('dre-invalid'); changed(); });
            form.addEventListener('change', changed);
            window.addEventListener('load', function () {
              setTimeout(function () {
                if (window.tinymce) {
                  window.tinymce.editors.forEach(function (editor) { editor.on('change keyup', changed); });
                }
              }, 1200);
            });
            var active = document.getElementById('entActive');
            form.addEventListener('invalid', function (event) { event.target.classList.add('dre-invalid'); }, true);
            form.addEventListener('submit', function (event) {
              if (single && !active.checked && active.defaultChecked && !window.confirm('This content is switched off. The page will lose it. Save anyway?')) {
                event.preventDefault();
                return;
              }
              if (window.tinymce) { window.tinymce.triggerSave(); }
              dirty = false;
            });
            window.addEventListener('beforeunload', function (event) {
              if (dirty) { event.preventDefault(); event.returnValue = ''; }
            });
            if (document.getElementById('entSaved') && window.history.replaceState) {
              // Reloading the page should not show "saved" again.
              window.history.replaceState(null, '', window.location.href.replace(/&saved=1/, ''));
            }
            // The summary card of the two-column layout: photo, name, and what is still empty.
            var card = document.getElementById('profCard');
            if (card) {
              var value = function (name) {
                var field = form.querySelector('[name="' + name + '"]');
                if (!field) { return ''; }
                if (field.type === 'file') {
                  return field.files.length || field.closest('.dre-field').querySelector('.dre-current:not([hidden])') ? '1' : '';
                }
                if (window.tinymce && field.id && window.tinymce.get(field.id)) {
                  return window.tinymce.get(field.id).getContent({ format: 'text' }).trim();
                }
                return field.value.replace(/<[^>]*>/g, ' ').trim();
              };
              var checks = card.querySelectorAll('[data-check]');
              var refresh = function () {
                document.getElementById('profName').textContent = value('name') || card.getAttribute('data-fallback');
                document.getElementById('profDesg').textContent = value('desg');
                var live = document.getElementById('profLive');
                live.textContent = active.checked ? 'Shown on the website' : 'Hidden';
                live.className = 'dre-pill ' + (active.checked ? 'dre-pill-good' : 'dre-pill-bad');
                var filled = 0;
                checks.forEach(function (item) {
                  var ok = value(item.getAttribute('data-check')) !== '';
                  filled += ok ? 1 : 0;
                  item.classList.toggle('is-done', ok);
                  item.querySelector('i').className = 'bi ' + (ok ? 'bi-check-circle-fill' : 'bi-circle');
                });
                document.getElementById('profBar').style.width = Math.round(filled / checks.length * 100) + '%';
                document.getElementById('profCount').textContent = filled === checks.length ? 'Everything is filled in.' : filled + ' of ' + checks.length + ' filled in. Click one to go to it.';
              };
              form.addEventListener('input', refresh);
              form.addEventListener('change', refresh);
              window.setInterval(refresh, 1500);
              refresh();
              var photoInput = form.querySelector('[name="image"]');
              if (photoInput) {
                photoInput.addEventListener('change', function () {
                  if (!photoInput.files.length) { return; }
                  var image = document.createElement('img');
                  image.alt = '';
                  image.src = URL.createObjectURL(photoInput.files[0]);
                  var holder = document.getElementById('profPhoto');
                  holder.textContent = '';
                  holder.appendChild(image);
                });
              }
              var coverInput = form.querySelector('[name="bg_image"]');
              var cover = document.getElementById('profCover');
              if (coverInput && cover) {
                coverInput.addEventListener('change', function () {
                  if (coverInput.files.length) {
                    cover.style.backgroundImage = 'url("' + URL.createObjectURL(coverInput.files[0]) + '")';
                  }
                });
              }
              card.querySelectorAll('[data-check] a').forEach(function (link) {
                link.addEventListener('click', function (event) {
                  var target = document.getElementById(link.getAttribute('href').slice(1));
                  if (!target) { return; }
                  event.preventDefault();
                  target.closest('.dre-f').scrollIntoView({ behavior: 'smooth', block: 'center' });
                  if (window.tinymce && window.tinymce.get(target.id)) { window.tinymce.get(target.id).focus(); } else { target.focus({ preventScroll: true }); }
                });
              });
            }
            var errorBox = document.getElementById('entErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
