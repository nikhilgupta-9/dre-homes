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

// Blog posts and company news are the same kind of record. This copy works on one of them:
// its table, the prefix of its columns, and where it shows on the website.
$kind = [
  'one' => 'news item', 'many' => 'news items', 'title' => 'Company news',
  'table' => 'tbl_news', 'p' => 'news',
  'folder' => 'assets/media/company-news/', 'public_path' => 'company-news/',
  'list_file' => 'manage-company-news.php', 'add_file' => 'add-company-news.php', 'edit_file' => 'edit-company-news.php',
  'export_file' => 'export-news.php', 'content_file' => 'edit-company-news-content.php',
  'spotlight' => true, 'blurb' => 'Announcements on the Company News page of the website.',
];
$p = $kind['p'];
$t = $kind['table'];
// This file is used for both adding and editing: this copy changes one (add-company-news.php adds one).
$editing = true;

function artf_e($text)
{
  return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function artf_query($conn, $sql, array $params = [])
{
  $stmt = mysqli_prepare($conn, $sql);
  if (!$stmt) {
    error_log('Article form query failed: ' . mysqli_error($conn));
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
    error_log('Article form query failed: ' . mysqli_stmt_error($stmt));
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

// "Why Invest in Dubai?" -> "why-invest-in-dubai"
function artf_slug($text)
{
  $text = strtolower(trim((string) $text));
  $text = preg_replace('/[^a-z0-9]+/', '-', $text);
  return trim($text, '-');
}

// Pictures are checked by extension and by their real content, and are stored under a
// name made here, never the name they arrived with.
const ARTF_IMAGE = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
const ARTF_IMAGE_MAX = 5 * 1024 * 1024;

// Checks one uploaded picture. Returns [extension, error]; extension is '' when no file was chosen.
function artf_check_upload($file, $label)
{
  if (!is_array($file) || is_array($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE || (string) $file['name'] === '') {
    return ['', ''];
  }
  if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > ARTF_IMAGE_MAX) {
    return ['', "$label is too large (maximum 5 MB)."];
  }
  if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
    return ['', "$label could not be uploaded. Please try again."];
  }
  $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $real_extension = array_search($finfo->file($file['tmp_name']), ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'], true);
  if (!isset(ARTF_IMAGE[$extension]) || $real_extension === false) {
    return ['', "$label must be one of: JPG, PNG, GIF, WEBP."];
  }
  return [$real_extension, ''];
}

// Shows the picture a field already has.
function artf_current($url)
{
  echo '<div class="dre-current"><a href="' . artf_e($url) . '" target="_blank" rel="noopener"><img src="' . artf_e($url) . '" alt="" loading="lazy"></a><span>Current picture. Choose a new one to replace it.</span></div>';
}

// Pictures: form field => [column, folder inside the media folder, label]
$image_fields = [
  'thumb_img' => ["{$p}_thumb_img", 'thumb/', 'Card image'],
  'inner_img' => ["{$p}_inner_img", 'inner/', 'Inner image'],
];
// Text fields: form field => [column, longest length kept (0 = no limit)]
$text_fields = [
  'heading' => ["{$p}_heading", 255], 'short_descp' => ["{$p}_short_descp", 0], 'long_descp' => ["{$p}_long_descp", 0],
  'thumb_img_alt' => ["{$p}_thumb_img_alt", 255], 'inner_img_alt' => ["{$p}_inner_img_alt", 255],
  'slug' => ["{$p}_slug", 200], 'posted_on' => ["{$p}_posted_on", 20],
  'meta_title' => ['meta_title', 255], 'meta_keyword' => ['meta_keyword', 255], 'meta_description' => ['meta_description', 0], 'meta_tags' => ['meta_tags', 0],
];

$id = 0;
$details = null;
if ($editing) {
  $id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
  $details = $id > 0 ? (artf_query($conn, "SELECT * FROM $t WHERE {$p}_id = ?", [$id]) ?: []) : [];
  $details = $details ? $details[0] : null;
}
$can_show_form = !$editing || $details;

$errors = [];
$old = ['is_active' => '1', 'spotlight' => ''];
foreach ($text_fields as $field => $spec) {
  $old[$field] = '';
}
$old['posted_on'] = date('Y-m-d');
if ($details) {
  // The form starts from what is saved.
  foreach ($text_fields as $field => $spec) {
    $old[$field] = (string) ($details[$spec[0]] ?? '');
  }
  $posted_time = strtotime((string) $details["{$p}_posted_on"]);
  $old['posted_on'] = $posted_time ? date('Y-m-d', $posted_time) : '';
  $old['is_active'] = $details['is_active'] === '1' ? '1' : '';
  $old['spotlight'] = $kind['spotlight'] && $details['news_markspot'] === '1' ? '1' : '';
}

if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_article'])) {

  // ---------------------------------------------------------------
  // 1. Read the form
  // ---------------------------------------------------------------
  $in = [];
  foreach ($text_fields as $field => $spec) {
    $value = isset($_POST[$field]) && is_string($_POST[$field]) ? trim($_POST[$field]) : '';
    $in[$field] = $spec[1] > 0 ? mb_substr($value, 0, $spec[1]) : $value;
  }
  $old = $in;
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';
  $old['spotlight'] = $kind['spotlight'] && isset($_POST['spotlight']) ? '1' : '';

  // ---------------------------------------------------------------
  // 2. Check it. Nothing is saved and no file is kept until everything is right.
  // ---------------------------------------------------------------
  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if ($in['heading'] === '') {
    $errors[] = 'Title is needed.';
  }
  // A new record needs its text. Pictures and videos count; empty paragraphs do not.
  $plain_text = trim(str_replace("\xC2\xA0", ' ', html_entity_decode(strip_tags($in['long_descp'], '<img><iframe><video>'), ENT_QUOTES, 'UTF-8')));
  if (!$editing && $plain_text === '') {
    $errors[] = 'The text is empty. Write the ' . $kind['one'] . ' before saving.';
  }
  $posted_time = strtotime($in['posted_on']);
  if ($in['posted_on'] === '' || $posted_time === false) {
    $errors[] = 'Choose the date.';
  }

  // Page address: a saved one is left exactly as it is unless it was changed here, because
  // changing it changes the page's link. A new one is made from the title when left empty.
  $saved_slug = $details ? (string) $details["{$p}_slug"] : '';
  if ($saved_slug !== '' && $in['slug'] === $saved_slug) {
    $slug = $saved_slug;
  } else {
    $slug_was_typed = $in['slug'] !== '';
    $slug = artf_slug($slug_was_typed ? $in['slug'] : ($saved_slug !== '' ? $saved_slug : $in['heading']));
    if ($slug === '' && $in['heading'] !== '') {
      $errors[] = 'The page address (slug) needs letters or numbers.';
    } elseif ($slug !== '') {
      $taken = artf_query($conn, "SELECT {$p}_id FROM $t WHERE {$p}_slug = ? AND {$p}_id <> ? LIMIT 1", [$slug, $id]);
      if ($taken && ($slug_was_typed || $editing)) {
        $errors[] = 'Another ' . $kind['one'] . ' already uses the page address "' . $slug . '". Choose a different one.';
      } elseif ($taken) {
        for ($n = 2; $n < 50 && $taken; $n++) {
          $taken = artf_query($conn, "SELECT {$p}_id FROM $t WHERE {$p}_slug = ? LIMIT 1", [$slug . '-' . $n]);
          if (!$taken) {
            $slug = $slug . '-' . $n;
          }
        }
      }
    }
  }

  $to_move = [];      // [temporary file, final path]
  $replaced = [];     // saved files that a new upload replaces
  $stored = [];       // form field => stored file name
  foreach ($image_fields as $field => $image) {
    $stored[$field] = $details ? (string) $details[$image[0]] : '';
    list($extension, $error) = artf_check_upload($_FILES[$field] ?? null, $image[2]);
    if ($error !== '') {
      $errors[] = $error;
    } elseif ($extension !== '') {
      $folder = $kind['folder'] . $image[1];
      if (trim($stored[$field]) !== '') {
        $replaced[] = $folder . basename($stored[$field]);
      }
      $stored[$field] = $image[0] . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
      $to_move[] = [$_FILES[$field]['tmp_name'], $folder . $stored[$field]];
    }
  }
  if (trim($stored['thumb_img']) === '' && !$errors) {
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
    $data = [];
    foreach ($text_fields as $field => $spec) {
      $data[$spec[0]] = $in[$field];
    }
    $data["{$p}_slug"] = $slug;
    $data["{$p}_posted_on"] = date('Y-m-d', $posted_time);
    foreach ($image_fields as $field => $image) {
      $data[$image[0]] = $stored[$field];
    }
    $data['is_active'] = $old['is_active'] === '1' ? '1' : '2';
    if ($kind['spotlight']) {
      $data['news_markspot'] = $old['spotlight'] === '1' ? '1' : '2';
    }

    if ($editing) {
      $old_data = $logger->getOldRecordData($t, $id);
      $assignments = [];
      foreach (array_keys($data) as $column) {
        $assignments[] = "$column = ?";
      }
      $saved = artf_query($conn, "UPDATE $t SET " . implode(', ', $assignments) . ", tstp = NOW() WHERE {$p}_id = ?", array_merge(array_values($data), [$id]));
      $record_id = $id;
    } else {
      $saved = artf_query($conn, "INSERT INTO $t (" . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ')', array_values($data));
      $record_id = $saved ? (int) mysqli_insert_id($conn) : 0;
    }

    if (!$saved) {
      foreach ($to_move as $move) {
        if (is_file($move[1])) {
          @unlink($move[1]);
        }
      }
      $errors[] = 'The ' . $kind['one'] . ' could not be saved. Please try again.';
    } else {
      foreach ($replaced as $path) {
        if (is_file($path)) {
          @unlink($path);
        }
      }
      if ($editing) {
        $logger->log('UPDATE', $t, $id, $old_data, $logger->getOldRecordData($t, $id), $kind['title'] . ' updated: ' . $in['heading']);
      } else {
        $logger->log('INSERT', $t, $record_id, null, $logger->getOldRecordData($t, $record_id), $kind['title'] . ' added: ' . $in['heading']);
      }
      header('Location: ' . BASE_URL . (isset($_POST['save_and_stay']) ? $kind['edit_file'] . '?edit=' . base64_encode($record_id) . '&saved=1' : $kind['list_file'] . '?done=' . ($editing ? 'saved' : 'added')));
      exit;
    }
  }
}

$just_saved = $editing && $details && isset($_GET['saved']) && !$errors;
$public_base = BASE_URL_HOME . $kind['public_path'];
$val = function ($field) use (&$old) {
  return artf_e($old[$field] ?? '');
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

              <!--begin::Article form-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-articles.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-article-form">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1><?= $editing ? 'Edit ' : 'Add ' ?><?= artf_e($kind['one']) ?></h1>
                    <?php if ($editing): ?>
                      <p><?= $details ? artf_e($details["{$p}_heading"]) : 'This ' . artf_e($kind['one']) . ' could not be found.' ?></p>
                    <?php else: ?>
                      <p>Fields marked <b>*</b> are needed. Everything else can be filled in later from Edit.</p>
                    <?php endif; ?>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL . $kind['list_file'] ?>"><i class="bi bi-arrow-left"></i> Back to <?= artf_e($kind['many']) ?></a>
                    <?php if ($details): ?>
                      <a href="<?= artf_e($public_base . $details["{$p}_slug"]) ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View on the website</a>
                    <?php endif; ?>
                  </div>
                </div>

                <?php if (!$can_show_form): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This <?= artf_e($kind['one']) ?> could not be found.</strong> It may have been deleted. <a href="<?= BASE_URL . $kind['list_file'] ?>">Back to the list</a></div>
                  </div>
                <?php elseif ($just_saved): ?>
                  <div class="dre-form-alert dre-form-ok" role="status" id="artfSaved">
                    <i class="bi bi-check-circle"></i>
                    <div><strong>Saved.</strong> <a href="<?= BASE_URL . $kind['list_file'] ?>">Back to the list</a></div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="artfErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>Not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= artf_e($error) ?></li>
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
                    <a href="#sec-basics"><i class="bi bi-card-heading"></i> Title and summary</a>
                    <a href="#sec-images"><i class="bi bi-images"></i> Pictures</a>
                    <a href="#sec-text"><i class="bi bi-text-paragraph"></i> Text</a>
                    <a href="#sec-seo"><i class="bi bi-search"></i> SEO</a>
                    <a href="#sec-publish"><i class="bi bi-send"></i> Publishing</a>
                  </nav>

                  <!--begin::Form-->
                  <form action="<?= BASE_URL ?><?= $editing ? $kind['edit_file'] . '?edit=' . base64_encode($id) : $kind['add_file'] ?>" enctype="multipart/form-data" method="post" id="article-form" class="dre-form">
                    <input type="hidden" name="csrf" value="<?= artf_e($_SESSION['admin_csrf']) ?>">
                    <input type="hidden" name="save_article" value="1">

                    <!-- Title and summary -->
                    <section class="dre-glass dre-form-section" id="sec-basics">
                      <h2><i class="bi bi-card-heading"></i> Title and summary</h2>
                      <label class="dre-field">
                        <span>Title <b>*</b></span>
                        <input type="text" name="heading" id="artfTitle" value="<?= $val('heading') ?>" maxlength="255" autocomplete="off" data-count="70" required>
                        <small class="dre-count"></small>
                      </label>
                      <label class="dre-field">
                        <span>Short description</span>
                        <textarea name="short_descp" id="artfShort" rows="3" data-count="200" placeholder="Two or three sentences shown on the card in the list."><?= $val('short_descp') ?></textarea>
                        <small class="dre-count"></small>
                      </label>
                    </section>

                    <!-- Pictures -->
                    <section class="dre-glass dre-form-section" id="sec-images">
                      <h2><i class="bi bi-images"></i> Pictures</h2>
                      <p class="dre-form-note">JPG, PNG, GIF or WEBP, up to 5 MB each.</p>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Card image <b>*</b></span>
                          <input type="file" name="thumb_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="artfThumbPreview"<?= $details && trim((string) $details["{$p}_thumb_img"]) !== '' ? '' : ' required' ?>>
                          <small>The picture on the card in the list.</small>
                          <div class="dre-previews" id="artfThumbPreview"></div>
                          <?php if ($details && trim((string) $details["{$p}_thumb_img"]) !== '') { artf_current(BASE_URL . $kind['folder'] . 'thumb/' . rawurlencode($details["{$p}_thumb_img"])); } ?>
                        </label>
                        <label class="dre-field">
                          <span>Card image alt text</span>
                          <input type="text" name="thumb_img_alt" value="<?= $val('thumb_img_alt') ?>" maxlength="255" autocomplete="off">
                          <small>Describes the picture for screen readers and search engines.</small>
                        </label>
                      </div>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Inner image</span>
                          <input type="file" name="inner_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="artfInnerPreview">
                          <small>The large picture at the top of the page.</small>
                          <div class="dre-previews" id="artfInnerPreview"></div>
                          <?php if ($details && trim((string) $details["{$p}_inner_img"]) !== '') { artf_current(BASE_URL . $kind['folder'] . 'inner/' . rawurlencode($details["{$p}_inner_img"])); } ?>
                        </label>
                        <label class="dre-field">
                          <span>Inner image alt text</span>
                          <input type="text" name="inner_img_alt" value="<?= $val('inner_img_alt') ?>" maxlength="255" autocomplete="off">
                        </label>
                      </div>
                    </section>

                    <!-- Text -->
                    <section class="dre-glass dre-form-section" id="sec-text">
                      <h2><i class="bi bi-text-paragraph"></i> Text</h2>
                      <div class="dre-field">
                        <label for="kt-tinymce-4"><span>Full text<?= $editing ? '' : ' <b>*</b>' ?></span></label>
                        <textarea name="long_descp" class="tinymce-editor" id="kt-tinymce-4" rows="16"><?= $val('long_descp') ?></textarea>
                      </div>
                      <p class="dre-article-meter" id="artfWords"></p>
                    </section>

                    <!-- SEO -->
                    <section class="dre-glass dre-form-section" id="sec-seo">
                      <h2><i class="bi bi-search"></i> SEO</h2>
                      <label class="dre-field">
                        <span>Page address (slug)</span>
                        <input type="text" name="slug" id="artfSlug" value="<?= $val('slug') ?>" maxlength="200" autocomplete="off" placeholder="made from the title">
                        <small><?= $editing ? 'Changing it breaks links to the old address.' : 'Leave empty to make it from the title.' ?></small>
                      </label>
                      <label class="dre-field">
                        <span>Meta title</span>
                        <input type="text" name="meta_title" id="artfMetaTitle" value="<?= $val('meta_title') ?>" maxlength="255" autocomplete="off" data-count="60">
                        <small class="dre-count"></small>
                      </label>
                      <label class="dre-field">
                        <span>Meta description</span>
                        <textarea name="meta_description" id="artfMetaDesc" rows="3" data-count="160"><?= $val('meta_description') ?></textarea>
                        <small class="dre-count"></small>
                      </label>
                      <div class="dre-field">
                        <span>How it may look in a search result</span>
                        <div class="dre-search-preview" aria-live="off">
                          <span id="artfPreviewUrl"></span>
                          <strong id="artfPreviewTitle"></strong>
                          <p id="artfPreviewDesc"></p>
                        </div>
                        <small>A rough preview. Search engines may choose their own text.</small>
                      </div>
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
                      <div class="dre-form-grid dre-cols-<?= $kind['spotlight'] ? '3' : '2' ?>">
                        <label class="dre-field">
                          <span>Date <b>*</b></span>
                          <input type="date" name="posted_on" value="<?= $val('posted_on') ?>" required>
                          <small>Shown on the page. The list on the website is ordered by this date.</small>
                        </label>
                        <label class="dre-toggle">
                          <input type="checkbox" name="is_active" value="1" <?= ($old['is_active'] ?? '') === '1' ? 'checked' : '' ?>>
                          <i></i>
                          <span><strong>Live on the website</strong><small>Off = saved as a draft, not shown.</small></span>
                        </label>
                        <?php if ($kind['spotlight']): ?>
                          <label class="dre-toggle">
                            <input type="checkbox" name="spotlight" value="1" <?= ($old['spotlight'] ?? '') === '1' ? 'checked' : '' ?>>
                            <i></i>
                            <span><strong>Spotlight</strong><small>The website shows one spotlight item: the live one with the latest date.</small></span>
                          </label>
                        <?php endif; ?>
                      </div>
                    </section>

                    <!-- Always in view: how complete it is, and the save buttons -->
                    <div class="dre-form-bar">
                      <div class="dre-form-progress" title="Details that make a page complete">
                        <div><strong id="artfDone">0</strong> of <span id="artfOutOf">6</span> key details filled</div>
                        <div class="dre-track"><i id="artfMeter" style="width: 0%"></i></div>
                        <small id="artfMissing"></small>
                      </div>
                      <div class="dre-form-bar-buttons">
                        <button type="submit" name="save_and_stay" value="1" class="dre-btn">Save and keep editing</button>
                        <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Save ' . artf_e($kind['one']) ?></button>
                      </div>
                    </div>
                  </form>
                  <!--end::Form-->
                </div>
                <?php endif; ?>
              </div>
              <!--end::Article form-->

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
          // Article form: helpers that make it quicker to fill in. The server checks everything again on save.
          (function () {
            var form = document.getElementById('article-form');
            if (!form) {
              return;
            }
            var publicBase = <?= json_encode($public_base) ?>;
            var has = <?= json_encode(['thumb' => $details && trim((string) $details["{$p}_thumb_img"]) !== '', 'inner' => $details && trim((string) $details["{$p}_inner_img"]) !== '']) ?>;
            var dirty = false;

            var titleInput = document.getElementById('artfTitle');
            var shortInput = document.getElementById('artfShort');
            var slugInput = document.getElementById('artfSlug');
            var metaTitle = document.getElementById('artfMetaTitle');
            var metaDesc = document.getElementById('artfMetaDesc');
            function slugify(text) {
              return text.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            }
            function cut(text, length) {
              return text.length > length ? text.slice(0, length - 1).trim() + '...' : text;
            }
            function editorText() {
              var editor = window.tinymce && window.tinymce.get('kt-tinymce-4');
              var html = editor ? editor.getContent() : document.getElementById('kt-tinymce-4').value;
              return html.replace(/<[^>]*>/g, ' ').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
            }

            // --- Search result preview: uses what is typed, and falls back the way the page does ---
            function preview() {
              // A saved address is shown as it is; a new one is made from what is typed.
              var slug = slugInput.value !== '' ? (slugInput.value === slugInput.defaultValue ? slugInput.value : slugify(slugInput.value)) : slugify(titleInput.value);
              document.getElementById('artfPreviewUrl').textContent = publicBase + slug;
              document.getElementById('artfPreviewTitle').textContent = cut(metaTitle.value.trim() || titleInput.value.trim() || 'Title', 62);
              document.getElementById('artfPreviewDesc').textContent = cut(metaDesc.value.trim() || shortInput.value.trim() || 'The meta description appears here.', 160);
            }

            // --- How long the text is ---
            function words() {
              var text = editorText();
              var count = text === '' ? 0 : text.split(' ').length;
              document.getElementById('artfWords').textContent = count === 0 ? 'No text yet.' : count.toLocaleString('en-US') + (count === 1 ? ' word' : ' words') + ', about ' + Math.max(1, Math.round(count / 200)) + ' min to read.';
              return count;
            }

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

            // --- How complete it is ---
            function fileChosen(name, saved) {
              return form.querySelector('[name="' + name + '"]').files.length > 0 || saved;
            }
            function progress() {
              var checks = [
                ['title', titleInput.value.trim() !== ''],
                ['short description', shortInput.value.trim() !== ''],
                ['card image', fileChosen('thumb_img', has.thumb)],
                ['inner image', fileChosen('inner_img', has.inner)],
                ['text', words() > 0],
                ['meta description', metaDesc.value.trim() !== '']
              ];
              var done = checks.filter(function (check) { return check[1]; }).length;
              document.getElementById('artfDone').textContent = done;
              document.getElementById('artfOutOf').textContent = checks.length;
              document.getElementById('artfMeter').style.width = Math.round(done * 100 / checks.length) + '%';
              var missing = checks.filter(function (check) { return !check[1]; }).map(function (check) { return check[0]; });
              document.getElementById('artfMissing').textContent = missing.length ? 'Still empty: ' + missing.join(', ') : 'All key details are filled in.';
            }
            function refresh() { preview(); progress(); }
            form.addEventListener('input', function (event) {
              event.target.classList.remove('dre-invalid');
              dirty = true;
              refresh();
            });
            form.addEventListener('change', function () { dirty = true; refresh(); });
            refresh();
            // The editor loads after this script, so look again once it is ready.
            window.addEventListener('load', function () {
              setTimeout(function () {
                refresh();
                var editor = window.tinymce && window.tinymce.get('kt-tinymce-4');
                if (editor) {
                  editor.on('change keyup', function () { dirty = true; refresh(); });
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
            window.addEventListener('beforeunload', function (event) {
              if (dirty) { event.preventDefault(); event.returnValue = ''; }
            });

            if (document.getElementById('artfSaved') && window.history.replaceState) {
              // Reloading the page should not show "saved" again.
              window.history.replaceState(null, '', window.location.href.replace(/&saved=1/, ''));
            }
            var errorBox = document.getElementById('artfErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
