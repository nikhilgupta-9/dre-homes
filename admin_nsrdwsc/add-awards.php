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

// This file is used for both adding and editing: this copy adds an award (edit-awards.php changes one).
$editing = false;

function simf_e($text)
{
  return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function simf_query($conn, $sql, array $params = [])
{
  $stmt = mysqli_prepare($conn, $sql);
  if (!$stmt) {
    error_log('Form query failed: ' . mysqli_error($conn));
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
    error_log('Form query failed: ' . mysqli_stmt_error($stmt));
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

// Text with its tags and empty spaces taken out, to tell whether an editor field really has words in it.
function simf_plain($html)
{
  return trim(preg_replace('/\s+/', ' ', str_replace("\xC2\xA0", ' ', html_entity_decode(strip_tags((string) $html), ENT_QUOTES, 'UTF-8'))));
}

const AWARD_FOLDER = 'assets/media/awards/';
const AWARD_IMAGE = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
const AWARD_IMAGE_MAX = 5 * 1024 * 1024;

$id = 0;
$details = null;
if ($editing) {
  $id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
  $details = $id > 0 ? (simf_query($conn, "SELECT * FROM tbl_awards WHERE award_id = ?", [$id]) ?: []) : [];
  $details = $details ? $details[0] : null;
}
$can_show_form = !$editing || $details;

$errors = [];
$old = ['heading' => '', 'img_alt' => '', 'short' => '', 'text' => '', 'postedby' => date('d-m-Y'), 'added_on' => date('Y-m-d'), 'home' => '', 'is_active' => '1'];
if ($details) {
  $added_time = strtotime((string) $details['tstp']);
  $old = [
    'heading' => (string) $details['award_heading'], 'img_alt' => (string) $details['award_img_alt'], 'short' => (string) $details['award_short_descp'],
    'text' => (string) $details['award_descp'], 'postedby' => (string) $details['award_postedby'], 'added_on' => $added_time > 0 ? date('Y-m-d', $added_time) : '',
    'home' => $details['award_showonhomepage'] === '1' ? '1' : '', 'is_active' => $details['is_active'] === '1' ? '1' : '',
  ];
}
$saved_image = $details ? trim((string) $details['award_img']) : '';

if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_award'])) {
  foreach (['heading' => 255, 'img_alt' => 255, 'short' => 0, 'text' => 0, 'postedby' => 255, 'added_on' => 20] as $field => $max) {
    $value = isset($_POST[$field]) && is_string($_POST[$field]) ? trim($_POST[$field]) : '';
    $old[$field] = $max > 0 ? mb_substr($value, 0, $max) : $value;
  }
  $old['home'] = isset($_POST['home']) ? '1' : '';
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';

  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if ($old['heading'] === '') {
    $errors[] = 'Award name is needed.';
  }
  if (simf_plain($old['text']) === '' && !$editing) {
    $errors[] = 'Write the text about the award.';
  }
  $added_time = $editing ? strtotime($old['added_on']) : time();
  if ($editing && ($old['added_on'] === '' || $added_time === false)) {
    $errors[] = 'Choose the "added on" date.';
  }

  // The picture keeps its saved file unless a new one is chosen. A new one is checked by
  // extension and by its real content, and stored under a name made here.
  $image = $saved_image;
  $new_file = '';
  $file = $_FILES['image'] ?? null;
  if (is_array($file) && !is_array($file['name']) && $file['error'] !== UPLOAD_ERR_NO_FILE && (string) $file['name'] !== '') {
    $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > AWARD_IMAGE_MAX) {
      $errors[] = 'The picture is too large (maximum 5 MB).';
    } elseif ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
      $errors[] = 'The picture could not be uploaded. Please try again.';
    } elseif (!isset(AWARD_IMAGE[$extension]) || ($real = array_search($finfo->file($file['tmp_name']), ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'], true)) === false) {
      $errors[] = 'The picture must be one of: JPG, PNG, GIF, WEBP.';
    } else {
      $new_file = 'award_img_' . bin2hex(random_bytes(6)) . '.' . $real;
      $image = $new_file;
    }
  }
  if ($image === '' && !$errors) {
    $errors[] = 'Add a picture of the award.';
  }
  if (!$errors && $new_file !== '') {
    if (!is_dir(AWARD_FOLDER)) {
      mkdir(AWARD_FOLDER, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], AWARD_FOLDER . $new_file)) {
      $errors[] = 'The picture could not be stored on the server. Nothing was saved.';
    }
  }

  if (!$errors) {
    $values = [$image, $old['img_alt'], $old['heading'], $old['short'], $old['text'], $old['postedby'], $old['home'] === '1' ? '1' : '2', $old['is_active'] === '1' ? '1' : '2'];
    if ($editing) {
      $old_data = $logger->getOldRecordData('tbl_awards', $id);
      // The "added on" date keeps the time of day it already had.
      $added_at = date('Y-m-d', $added_time) . ' ' . (strtotime((string) $details['tstp']) > 0 ? date('H:i:s', strtotime($details['tstp'])) : '00:00:00');
      $saved = simf_query($conn, "UPDATE tbl_awards SET award_img = ?, award_img_alt = ?, award_heading = ?, award_short_descp = ?, award_descp = ?, award_postedby = ?, award_showonhomepage = ?, is_active = ?, tstp = ? WHERE award_id = ?", array_merge($values, [$added_at, $id]));
      $record_id = $id;
    } else {
      $saved = simf_query($conn, "INSERT INTO tbl_awards (award_img, award_img_alt, award_heading, award_short_descp, award_descp, award_postedby, award_showonhomepage, is_active, tstp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())", $values);
      $record_id = $saved ? (int) mysqli_insert_id($conn) : 0;
    }
    if (!$saved) {
      if ($new_file !== '' && is_file(AWARD_FOLDER . $new_file)) {
        @unlink(AWARD_FOLDER . $new_file);
      }
      $errors[] = 'The award could not be saved. Please try again.';
    } else {
      if ($new_file !== '' && $saved_image !== '' && is_file(AWARD_FOLDER . basename($saved_image))) {
        @unlink(AWARD_FOLDER . basename($saved_image));
      }
      if ($editing) {
        $logger->log('UPDATE', 'tbl_awards', $id, $old_data, $logger->getOldRecordData('tbl_awards', $id), 'Award Updated: ' . $old['heading']);
      } else {
        $logger->log('INSERT', 'tbl_awards', $record_id, null, $logger->getOldRecordData('tbl_awards', $record_id), 'New Award added: ' . $old['heading']);
      }
      header('Location: ' . BASE_URL . 'manage-awards.php?done=' . ($editing ? 'saved' : 'added'));
      exit;
    }
  }
}
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
              <!--begin::Award form-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-articles.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-simple.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-article-form dre-simple-form">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1><?= $editing ? 'Edit award' : 'Add award' ?></h1>
                    <p><?= $editing ? ($details ? simf_e($details['award_heading']) : 'This award could not be found.') : 'Fields marked <b>*</b> are needed.' ?></p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL ?>manage-awards.php"><i class="bi bi-arrow-left"></i> Back to awards</a>
                  </div>
                </div>

                <?php if (!$can_show_form): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This award could not be found.</strong> It may have been deleted. <a href="<?= BASE_URL ?>manage-awards.php">Back to awards</a></div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="simfErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>Not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= simf_e($error) ?></li>
                        <?php endforeach; ?>
                      </ul>
                      <em>The picture has to be chosen again: a browser does not keep it after an error.</em>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($can_show_form): ?>
                <form action="<?= BASE_URL ?><?= $editing ? 'edit-awards.php?edit=' . base64_encode($id) : 'add-awards.php' ?>" enctype="multipart/form-data" method="post" id="simple-form" class="dre-form">
                  <input type="hidden" name="csrf" value="<?= simf_e($_SESSION['admin_csrf']) ?>">
                  <input type="hidden" name="save_award" value="1">

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-trophy"></i> The award</h2>
                    <label class="dre-field">
                      <span>Award name <b>*</b></span>
                      <input type="text" name="heading" value="<?= simf_e($old['heading']) ?>" maxlength="255" autocomplete="off" required>
                      <small>The heading next to the picture on the Awards page.</small>
                    </label>
                    <div class="dre-form-grid dre-cols-2">
                      <label class="dre-field">
                        <span>Picture <b>*</b></span>
                        <input type="file" name="image" id="simfImage" accept="image/jpeg,image/png,image/gif,image/webp"<?= $saved_image !== '' ? '' : ' required' ?>>
                        <small>JPG, PNG, GIF or WEBP, up to 5 MB.<?= $saved_image !== '' ? ' Leave empty to keep the current one.' : '' ?></small>
                        <div class="dre-current" id="simfCurrent"<?= $saved_image === '' ? ' hidden' : '' ?>>
                          <img src="<?= $saved_image !== '' ? BASE_URL . AWARD_FOLDER . simf_e(rawurlencode($saved_image)) : '' ?>" alt="" id="simfPreview">
                          <span id="simfCurrentNote">Current picture.</span>
                        </div>
                      </label>
                      <label class="dre-field">
                        <span>Picture description (alt text)</span>
                        <input type="text" name="img_alt" value="<?= simf_e($old['img_alt']) ?>" maxlength="255" autocomplete="off" placeholder="DRE Homes team receiving the award on stage">
                        <small>Read out by screen readers and used by search engines.</small>
                      </label>
                    </div>
                    <label class="dre-field">
                      <span>Date shown</span>
                      <input type="text" name="postedby" value="<?= simf_e($old['postedby']) ?>" maxlength="255" autocomplete="off" placeholder="20-08-2026">
                      <small>Kept as written, day first: 20-08-2026.</small>
                    </label>
                  </section>

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-text-paragraph"></i> Text</h2>
                    <div class="dre-field">
                      <label for="kt-tinymce-4"><span>Full text<?= $editing ? '' : ' <b>*</b>' ?></span></label>
                      <textarea name="text" class="tinymce-editor" id="kt-tinymce-4" rows="12"><?= simf_e($old['text']) ?></textarea>
                      <small>This is the text shown on the Awards page.</small>
                    </div>
                    <div class="dre-field">
                      <label for="kt-tinymce-3"><span>Short text</span></label>
                      <textarea name="short" class="tinymce-editor" id="kt-tinymce-3" rows="5"><?= simf_e($old['short']) ?></textarea>
                      <small>A shorter version, used where there is less room.</small>
                    </div>
                  </section>

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-send"></i> Publishing</h2>
                    <div class="dre-form-grid dre-cols-3">
                      <label class="dre-toggle">
                        <input type="checkbox" name="is_active" value="1" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
                        <i></i>
                        <span><strong>Live on the website</strong><small>Off = saved but hidden.</small></span>
                      </label>
                      <label class="dre-toggle">
                        <input type="checkbox" name="home" value="1" <?= $old['home'] === '1' ? 'checked' : '' ?>>
                        <i></i>
                        <span><strong>Show on the homepage</strong><small>Adds this award to the homepage awards.</small></span>
                      </label>
                      <?php if ($editing): ?>
                        <label class="dre-field">
                          <span>Added on <b>*</b></span>
                          <input type="date" name="added_on" value="<?= simf_e($old['added_on']) ?>" required>
                          <small>For the record. The Awards page lists the newest added award first.</small>
                        </label>
                      <?php endif; ?>
                    </div>
                  </section>

                  <div class="dre-form-bar">
                    <div class="dre-form-progress">
                      <div id="simfState"><?= $editing ? 'No unsaved changes' : 'Not saved yet' ?></div>
                    </div>
                    <div class="dre-form-bar-buttons">
                      <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Save award' ?></button>
                    </div>
                  </div>
                </form>
                <?php endif; ?>
              </div>
              <!--end::Award form-->

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
          // Award form: a preview of the picked picture and a warning before leaving unsaved changes.
          (function () {
            var form = document.getElementById('simple-form');
            if (!form) {
              return;
            }
            var dirty = false;
            var state = document.getElementById('simfState');
            function changed() {
              dirty = true;
              state.textContent = 'Unsaved changes';
              state.classList.add('dre-warn-text');
            }
            form.addEventListener('input', function (event) { event.target.classList.remove('dre-invalid'); changed(); });
            form.addEventListener('change', changed);
            window.addEventListener('load', function () {
              setTimeout(function () {
                if (window.tinymce) {
                  window.tinymce.editors.forEach(function (editor) { editor.on('change keyup', changed); });
                }
              }, 1200);
            });

            var image = document.getElementById('simfImage');
            var current = document.getElementById('simfCurrent');
            var preview = document.getElementById('simfPreview');
            var note = document.getElementById('simfCurrentNote');
            var savedSrc = preview.getAttribute('src');
            image.addEventListener('change', function () {
              var file = image.files[0];
              var bad = '';
              if (file && file.type.indexOf('image/') !== 0) {
                bad = '"' + file.name + '" is not an image.';
              } else if (file && file.size > 5 * 1024 * 1024) {
                bad = '"' + file.name + '" is larger than 5 MB.';
              }
              image.setCustomValidity(bad);
              image.classList.toggle('dre-invalid', bad !== '');
              if (bad !== '') { image.reportValidity(); }
              if (file && bad === '') {
                preview.src = URL.createObjectURL(file);
                note.textContent = 'New picture, not saved yet.';
                current.hidden = false;
              } else {
                preview.src = savedSrc;
                note.textContent = 'Current picture.';
                current.hidden = savedSrc === '';
              }
            });

            form.addEventListener('invalid', function (event) { event.target.classList.add('dre-invalid'); }, true);
            form.addEventListener('submit', function () {
              if (window.tinymce) { window.tinymce.triggerSave(); }
              dirty = false;
            });
            window.addEventListener('beforeunload', function (event) {
              if (dirty) { event.preventDefault(); event.returnValue = ''; }
            });
            var errorBox = document.getElementById('simfErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
