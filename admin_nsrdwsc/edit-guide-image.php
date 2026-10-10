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

const GUIDE_FOLDER = 'assets/media/guide/';

function guide_e($text)
{
  return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function guide_query($conn, $sql, array $params = [])
{
  $stmt = mysqli_prepare($conn, $sql);
  if (!$stmt) {
    error_log('Guide query failed: ' . mysqli_error($conn));
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
    error_log('Guide query failed: ' . mysqli_stmt_error($stmt));
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

// Each guide page of the website is its own file that reads one guide by its number.
// This finds them: guide id => address of its page, for example 15 => "buyer-guide".
function guide_pages()
{
  $pages = [];
  foreach (glob(dirname(__DIR__) . '/*guide*.php') ?: [] as $file) {
    if (preg_match('/tbl_guide\W+guide_id\s*=\s*(\d+)/', (string) file_get_contents($file, false, null, 0, 4000), $match)) {
      $pages[(int) $match[1]] = basename($file, '.php');
    }
  }
  return $pages;
}

// This file is used for both adding and editing a step: this copy changes one (add-guide-image.php adds one).
$editing = true;

const GUIDE_IMAGE = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
const GUIDE_IMAGE_MAX = 5 * 1024 * 1024;

// Adding: ?guide_id=<guide>. Editing: ?edit=<step> (the old "&id=<guide>" part is not needed).
$step_id = $editing ? (int) ($_GET['edit'] ?? 0) : 0;
$details = $editing && $step_id > 0 ? (guide_query($conn, "SELECT * FROM tbl_guide_img WHERE guide_img_id = ?", [$step_id]) ?: []) : [];
$details = $details ? $details[0] : null;
$guide_id = $editing ? ($details ? (int) $details['guide_id'] : 0) : (int) ($_GET['guide_id'] ?? 0);
$guide = $guide_id > 0 ? (guide_query($conn, "SELECT guide_id, guide_heading FROM tbl_guide WHERE guide_id = ?", [$guide_id]) ?: []) : [];
$guide = $guide ? $guide[0] : null;
$can_show_form = $guide && (!$editing || $details);
$back_url = BASE_URL . ($guide ? 'edit-guide.php?edit=' . base64_encode($guide_id) . '#sec-steps' : 'manage-guide.php');

$errors = [];
$old = ['head' => '', 'text' => '', 'is_active' => '1'];
if ($details) {
  $old = ['head' => (string) $details['guide_head'], 'text' => (string) $details['guide_short_descp'], 'is_active' => $details['is_active'] === '1' ? '1' : ''];
}
$saved_image = $details ? trim((string) $details['guide_image']) : '';

if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_step'])) {
  $old['head'] = isset($_POST['head']) && is_string($_POST['head']) ? mb_substr(trim($_POST['head']), 0, 255) : '';
  $old['text'] = isset($_POST['text']) && is_string($_POST['text']) ? trim($_POST['text']) : '';
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';

  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if ($old['head'] === '') {
    $errors[] = 'Heading is needed.';
  }

  // The picture keeps its saved file unless a new one is chosen. A new one is checked by
  // extension and by its real content, and stored under a name made here.
  $image = $saved_image;
  $new_file = '';
  $file = $_FILES['image'] ?? null;
  if (is_array($file) && !is_array($file['name']) && $file['error'] !== UPLOAD_ERR_NO_FILE && (string) $file['name'] !== '') {
    $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > GUIDE_IMAGE_MAX) {
      $errors[] = 'The picture is too large (maximum 5 MB).';
    } elseif ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
      $errors[] = 'The picture could not be uploaded. Please try again.';
    } elseif (!isset(GUIDE_IMAGE[$extension]) || ($real = array_search($finfo->file($file['tmp_name']), ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'], true)) === false) {
      $errors[] = 'The picture must be one of: JPG, PNG, GIF, WEBP.';
    } else {
      $new_file = 'guide_image_' . bin2hex(random_bytes(6)) . '.' . $real;
      $image = $new_file;
    }
  }
  if ($image === '' && !$errors) {
    $errors[] = 'Add a picture for this step.';
  }
  if (!$errors && $new_file !== '') {
    if (!is_dir(GUIDE_FOLDER)) {
      mkdir(GUIDE_FOLDER, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], GUIDE_FOLDER . $new_file)) {
      $errors[] = 'The picture could not be stored on the server. Nothing was saved.';
    }
  }

  if (!$errors) {
    if ($editing) {
      $old_data = $logger->getOldRecordData('tbl_guide_img', $step_id);
      $saved = guide_query($conn, "UPDATE tbl_guide_img SET guide_image = ?, guide_head = ?, guide_short_descp = ?, is_active = ?, tstp = NOW() WHERE guide_img_id = ?", [$image, $old['head'], $old['text'], $old['is_active'] === '1' ? '1' : '2', $step_id]);
      $record_id = $step_id;
    } else {
      $saved = guide_query($conn, "INSERT INTO tbl_guide_img (guide_id, guide_image, guide_head, guide_short_descp, is_active, tstp) VALUES (?, ?, ?, ?, ?, NOW())", [$guide_id, $image, $old['head'], $old['text'], $old['is_active'] === '1' ? '1' : '2']);
      $record_id = $saved ? (int) mysqli_insert_id($conn) : 0;
    }
    if (!$saved) {
      if ($new_file !== '' && is_file(GUIDE_FOLDER . $new_file)) {
        @unlink(GUIDE_FOLDER . $new_file);
      }
      $errors[] = 'The step could not be saved. Please try again.';
    } else {
      if ($new_file !== '' && $saved_image !== '' && is_file(GUIDE_FOLDER . basename($saved_image))) {
        @unlink(GUIDE_FOLDER . basename($saved_image));
      }
      if ($editing) {
        $logger->log('UPDATE', 'tbl_guide_img', $step_id, $old_data, $logger->getOldRecordData('tbl_guide_img', $step_id), 'Guide step updated: ' . $old['head']);
      } else {
        $logger->log('INSERT', 'tbl_guide_img', $record_id, null, $logger->getOldRecordData('tbl_guide_img', $record_id), 'Guide step added: ' . $old['head']);
      }
      $another = !$editing && isset($_POST['save_and_add']);
      header('Location: ' . BASE_URL . ($another ? 'add-guide-image.php?guide_id=' . $guide_id . '&added=1' : 'edit-guide.php?edit=' . base64_encode($guide_id) . '&step=' . ($editing ? 'saved' : 'added') . '#sec-steps'));
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
              <!--begin::Guide step form-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-articles.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-guides.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-article-form dre-guide-form">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1><?= $editing ? 'Edit step' : 'Add a step' ?></h1>
                    <p><?= $guide ? 'Guide: ' . guide_e($guide['guide_heading']) : 'This step could not be found.' ?></p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= guide_e($back_url) ?>"><i class="bi bi-arrow-left"></i> Back to the guide</a>
                  </div>
                </div>

                <?php if (!$can_show_form): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This <?= $editing ? 'step' : 'guide' ?> could not be found.</strong> It may have been deleted. <a href="<?= BASE_URL ?>manage-guide.php">Back to guides</a></div>
                  </div>
                <?php elseif (isset($_GET['added']) && !$errors): ?>
                  <div class="dre-form-alert dre-form-ok" role="status">
                    <i class="bi bi-check-circle"></i>
                    <div><strong>The step was added.</strong> Add the next one below, or <a href="<?= guide_e($back_url) ?>">go back to the guide</a>.</div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="stepErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>Not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= guide_e($error) ?></li>
                        <?php endforeach; ?>
                      </ul>
                      <em>The picture has to be chosen again: a browser does not keep it after an error.</em>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($can_show_form): ?>
                <form action="<?= BASE_URL ?><?= $editing ? 'edit-guide-image.php?edit=' . (int) $step_id : 'add-guide-image.php?guide_id=' . (int) $guide_id ?>" enctype="multipart/form-data" method="post" id="step-form" class="dre-form">
                  <input type="hidden" name="csrf" value="<?= guide_e($_SESSION['admin_csrf']) ?>">
                  <input type="hidden" name="save_step" value="1">

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-list-ol"></i> The step</h2>
                    <div class="dre-form-grid dre-cols-2">
                      <label class="dre-field">
                        <span>Heading <b>*</b></span>
                        <input type="text" name="head" value="<?= guide_e($old['head']) ?>" maxlength="255" autocomplete="off" placeholder="WHO CAN BUY?" required>
                      </label>
                      <label class="dre-field">
                        <span>Picture <b>*</b></span>
                        <input type="file" name="image" id="stepImage" accept="image/jpeg,image/png,image/gif,image/webp"<?= $saved_image !== '' ? '' : ' required' ?>>
                        <small>A small icon-like picture. JPG, PNG, GIF or WEBP, up to 5 MB.<?= $saved_image !== '' ? ' Leave empty to keep the current one.' : '' ?></small>
                        <div class="dre-guide-current" id="stepCurrent"<?= $saved_image === '' ? ' hidden' : '' ?>>
                          <img src="<?= $saved_image !== '' ? BASE_URL . GUIDE_FOLDER . guide_e(rawurlencode($saved_image)) : '' ?>" alt="" id="stepPreview">
                          <span id="stepCurrentNote">Current picture.</span>
                        </div>
                      </label>
                    </div>
                    <div class="dre-field">
                      <label for="kt-tinymce-2"><span>Text</span></label>
                      <textarea name="text" class="tinymce-editor" id="kt-tinymce-2" rows="10"><?= guide_e($old['text']) ?></textarea>
                    </div>
                    <label class="dre-toggle">
                      <input type="checkbox" name="is_active" value="1" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
                      <i></i>
                      <span><strong>Show this step</strong><small>Off = saved but left out of the guide.</small></span>
                    </label>
                  </section>

                  <div class="dre-form-bar">
                    <div class="dre-form-progress">
                      <div id="stepState"><?= $editing ? 'No unsaved changes' : 'Not saved yet' ?></div>
                    </div>
                    <div class="dre-form-bar-buttons">
                      <?php if (!$editing): ?>
                        <button type="submit" name="save_and_add" value="1" class="dre-btn">Save and add another</button>
                      <?php endif; ?>
                      <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Save step' ?></button>
                    </div>
                  </div>
                </form>
                <?php endif; ?>
              </div>
              <!--end::Guide step form-->

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
          // Guide step form: a preview of the picked picture and a warning before leaving unsaved changes.
          (function () {
            var form = document.getElementById('step-form');
            if (!form) {
              return;
            }
            var dirty = false;
            var state = document.getElementById('stepState');
            function changed() {
              dirty = true;
              state.textContent = 'Unsaved changes';
              state.classList.add('dre-warn-text');
            }
            form.addEventListener('input', function (event) { event.target.classList.remove('dre-invalid'); changed(); });
            form.addEventListener('change', changed);
            window.addEventListener('load', function () {
              setTimeout(function () {
                var editor = window.tinymce && window.tinymce.get('kt-tinymce-2');
                if (editor) { editor.on('change keyup', changed); }
              }, 1200);
            });

            var image = document.getElementById('stepImage');
            var current = document.getElementById('stepCurrent');
            var preview = document.getElementById('stepPreview');
            var note = document.getElementById('stepCurrentNote');
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
            var errorBox = document.getElementById('stepErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
