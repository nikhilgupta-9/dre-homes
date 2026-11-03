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

// The five listing pages have the same kind of content. This copy edits one of them:
// its table, the names of its columns, and where it shows on the website.
$page = [
  'title' => 'International',
  'table' => 'tbl_international_content',
  'id_column' => 'intl_cont_id',
  'image_column' => 'intl_cont_img',
  'heading_column' => 'intl_cont_heading',
  'text_column' => '',
  'file' => 'edit-international-content.php',
  'public_path' => 'international-properties',
  'project_type' => 3,
  'post_flag' => 'update_intl_content',
];
// All five, for the tabs at the top: file => label.
$listing_pages = [
  'edit-offplan-content.php' => 'Off-plan',
  'edit-buyproperty-content.php' => 'Buy',
  'edit-rentproperty-content.php' => 'Rent',
  'edit-readytomovein-content.php' => 'Ready to move in',
  'edit-international-content.php' => 'International',
];

function listc_e($text)
{
  return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function listc_query($conn, $sql, array $params = [])
{
  $stmt = mysqli_prepare($conn, $sql);
  if (!$stmt) {
    error_log('Listing content query failed: ' . mysqli_error($conn));
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
    error_log('Listing content query failed: ' . mysqli_stmt_error($stmt));
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

const LISTC_IMAGE = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
const LISTC_IMAGE_MAX = 5 * 1024 * 1024;
const LISTC_IMAGE_FOLDER = 'assets/media/project/';

$id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
$details = $id > 0 ? (listc_query($conn, "SELECT * FROM {$page['table']} WHERE {$page['id_column']} = ?", [$id]) ?: []) : [];
$details = $details ? $details[0] : null;

$errors = [];
$old = ['heading' => '', 'text' => '', 'is_active' => ''];
if ($details) {
  $old = [
    'heading' => (string) $details[$page['heading_column']],
    'text' => $page['text_column'] !== '' ? (string) $details[$page['text_column']] : '',
    'is_active' => $details['is_active'] === '1' ? '1' : '',
  ];
}

if ($details && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST[$page['post_flag']])) {
  $old['heading'] = isset($_POST['heading']) && is_string($_POST['heading']) ? mb_substr(trim($_POST['heading']), 0, 255) : '';
  $old['text'] = $page['text_column'] !== '' && isset($_POST['text']) && is_string($_POST['text']) ? trim($_POST['text']) : '';
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';

  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if ($old['heading'] === '') {
    $errors[] = 'Page heading is needed.';
  }

  // The banner keeps its saved picture unless a new one is chosen. A new one is checked by
  // extension and by its real content, and stored under a name made here.
  $stored = (string) $details[$page['image_column']];
  $new_file = '';
  $file = $_FILES['banner'] ?? null;
  if (is_array($file) && !is_array($file['name']) && $file['error'] !== UPLOAD_ERR_NO_FILE && (string) $file['name'] !== '') {
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > LISTC_IMAGE_MAX) {
      $errors[] = 'The banner image is too large (maximum 5 MB).';
    } elseif ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
      $errors[] = 'The banner image could not be uploaded. Please try again.';
    } else {
      $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
      $finfo = new finfo(FILEINFO_MIME_TYPE);
      $real_extension = array_search($finfo->file($file['tmp_name']), ['jpg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'], true);
      if (!isset(LISTC_IMAGE[$extension]) || $real_extension === false) {
        $errors[] = 'The banner image must be one of: JPG, PNG, WEBP.';
      } else {
        $new_file = $page['image_column'] . '_' . bin2hex(random_bytes(6)) . '.' . $real_extension;
      }
    }
  }

  if (!$errors && $new_file !== '' && !move_uploaded_file($file['tmp_name'], LISTC_IMAGE_FOLDER . $new_file)) {
    $errors[] = 'The banner image could not be stored on the server. Nothing was saved.';
  }

  if (!$errors) {
    $old_data = $logger->getOldRecordData($page['table'], $id);
    $assignments = "{$page['image_column']} = ?, {$page['heading_column']} = ?, is_active = ?, tstp = NOW()";
    $values = [$new_file !== '' ? $new_file : $stored, $old['heading'], $old['is_active'] === '1' ? '1' : '2'];
    if ($page['text_column'] !== '') {
      $assignments .= ", {$page['text_column']} = ?";
      $values[] = $old['text'];
    }
    $values[] = $id;
    $saved = listc_query($conn, "UPDATE {$page['table']} SET $assignments WHERE {$page['id_column']} = ?", $values);
    if (!$saved) {
      if ($new_file !== '' && is_file(LISTC_IMAGE_FOLDER . $new_file)) {
        @unlink(LISTC_IMAGE_FOLDER . $new_file);
      }
      $errors[] = 'The changes could not be saved. Please try again.';
    } else {
      $old_name = basename(trim($stored));
      if ($new_file !== '' && $old_name !== '' && is_file(LISTC_IMAGE_FOLDER . $old_name)) {
        @unlink(LISTC_IMAGE_FOLDER . $old_name);
      }
      $logger->log('UPDATE', $page['table'], $id, $old_data, $logger->getOldRecordData($page['table'], $id), $page['title'] . ' page content updated');
      header('Location: ' . BASE_URL . $page['file'] . '?edit=' . base64_encode($id) . '&saved=1');
      exit;
    }
  }
}

$just_saved = $details && isset($_GET['saved']) && !$errors;
$banner_url = $details && trim((string) $details[$page['image_column']]) !== '' ? BASE_URL . LISTC_IMAGE_FOLDER . rawurlencode($details[$page['image_column']]) : '';
$public_url = BASE_URL_HOME . $page['public_path'];
$counted = listc_query($conn, "SELECT COUNT(*) AS n FROM tbl_proj WHERE prj_type_id = ? AND is_active = '1'", [$page['project_type']]);
$live_listings = $counted ? (int) $counted[0]['n'] : 0;
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
              <!--begin::Listing page content-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-content.css?v=2" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-leads dre-form-page dre-content-page">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1><?= listc_e($page['title']) ?> page content</h1>
                    <p>The banner<?= $page['text_column'] !== '' ? ', heading and intro text' : ' and heading' ?> at the top of the <?= listc_e($page['title']) ?> listing page.</p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= listc_e($public_url) ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View the page</a>
                  </div>
                </div>

                <!-- The other listing pages -->
                <nav class="dre-leads-tabs dre-content-tabs" aria-label="Listing pages">
                  <?php foreach ($listing_pages as $file => $label): ?>
                    <a href="<?= BASE_URL . $file ?>?edit=<?= base64_encode(1) ?>" class="<?= $file === $page['file'] ? 'is-on' : '' ?>" <?= $file === $page['file'] ? 'aria-current="page"' : '' ?>><?= listc_e($label) ?></a>
                  <?php endforeach; ?>
                </nav>

                <?php if (!$details): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This content could not be found.</strong> Open it from the menu: Listing Page Content.</div>
                  </div>
                <?php elseif ($just_saved): ?>
                  <div class="dre-form-alert dre-form-ok" role="status" id="listcSaved">
                    <i class="bi bi-check-circle"></i>
                    <div><strong>The changes were saved.</strong> <a href="<?= listc_e($public_url) ?>" target="_blank" rel="noopener">View the page</a></div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="listcErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>The changes were not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= listc_e($error) ?></li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($details): ?>
                <div class="dre-content-layout">

                  <!--begin::Form-->
                  <form action="<?= BASE_URL . $page['file'] ?>?edit=<?= base64_encode($id) ?>" enctype="multipart/form-data" method="post" id="listing-content-form" class="dre-form">
                    <input type="hidden" name="csrf" value="<?= listc_e($_SESSION['admin_csrf']) ?>">
                    <input type="hidden" name="<?= listc_e($page['post_flag']) ?>" value="1">

                    <section class="dre-glass dre-form-section">
                      <h2><i class="bi bi-window"></i> Top of the page</h2>
                      <label class="dre-field">
                        <span>Page heading <b>*</b></span>
                        <input type="text" name="heading" id="listcHeading" value="<?= listc_e($old['heading']) ?>" maxlength="255" autocomplete="off" required>
                        <small>The title over the banner.</small>
                      </label>
                      <label class="dre-field">
                        <span>Banner image</span>
                        <input type="file" name="banner" id="listcImage" accept="image/jpeg,image/png,image/webp">
                        <small>JPG, PNG or WEBP, up to 5 MB. A wide picture works best (about 1920 &times; 600). Leave empty to keep the current one.</small>
                      </label>
                      <?php if ($page['text_column'] !== ''): ?>
                        <div class="dre-field">
                          <label for="kt-tinymce-2"><span>Intro text</span></label>
                          <textarea name="text" class="tinymce-editor" id="kt-tinymce-2" rows="8"><?= listc_e($old['text']) ?></textarea>
                        </div>
                      <?php endif; ?>
                    </section>

                    <section class="dre-glass dre-form-section">
                      <h2><i class="bi bi-send"></i> Publishing</h2>
                      <label class="dre-toggle">
                        <input type="checkbox" name="is_active" value="1" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
                        <i></i>
                        <span><strong>Use this content on the website</strong><small>Off = the <?= listc_e($page['title']) ?> page loses its banner and heading. Keep it on unless you are replacing this content.</small></span>
                      </label>
                    </section>

                    <div class="dre-form-bar">
                      <div class="dre-form-progress">
                        <div id="listcState">No unsaved changes</div>
                      </div>
                      <div class="dre-form-bar-buttons">
                        <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> Save changes</button>
                      </div>
                    </div>
                  </form>
                  <!--end::Form-->

                  <!-- How it will look -->
                  <aside class="dre-content-side" aria-label="Preview">
                    <div class="dre-glass dre-preview-card">
                      <span class="dre-preview-label"><i class="bi bi-eye"></i> Page banner</span>
                      <div class="dre-preview-banner" id="listcBanner"<?= $banner_url !== '' ? ' style="background-image: url(\'' . listc_e($banner_url) . '\')"' : '' ?>>
                        <div class="dre-preview-glass">
                          <strong id="listcBannerTitle"><?= listc_e($old['heading']) ?></strong>
                          <span>Home &rsaquo; <?= listc_e($page['title']) ?></span>
                        </div>
                      </div>
                      <small id="listcBannerNote"><?= $banner_url !== '' ? 'Current banner.' : 'No banner picture yet.' ?></small>
                      <?php if ($page['text_column'] !== ''): ?>
                        <p class="dre-preview-text" id="listcText"></p>
                      <?php endif; ?>
                    </div>
                    <div class="dre-glass dre-preview-card dre-preview-facts">
                      <span class="dre-preview-label"><i class="bi bi-info-circle"></i> This page</span>
                      <strong><?= number_format($live_listings) ?></strong>
                      <span>live <?= $live_listings === 1 ? 'listing' : 'listings' ?> shown under this banner</span>
                      <small>Last changed <?= !empty($details['tstp']) && strtotime($details['tstp']) > 0 ? date('d M Y', strtotime($details['tstp'])) : 'not known' ?>.</small>
                    </div>
                  </aside>
                </div>
                <?php endif; ?>
              </div>
              <!--end::Listing page content-->

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
          // Listing page content: a live preview next to the form, and a warning before leaving unsaved changes.
          (function () {
            var form = document.getElementById('listing-content-form');
            if (!form) {
              return;
            }
            var dirty = false;
            var state = document.getElementById('listcState');
            function changed() {
              dirty = true;
              state.textContent = 'Unsaved changes';
              state.classList.add('dre-warn-text');
            }

            var heading = document.getElementById('listcHeading');
            var textPreview = document.getElementById('listcText');
            function plainText(html) {
              var holder = document.createElement('div');
              holder.innerHTML = html.replace(/<(script|style)[\s\S]*?<\/\1>/gi, '');
              return (holder.textContent || '').replace(/\s+/g, ' ').trim();
            }
            function preview() {
              document.getElementById('listcBannerTitle').textContent = heading.value.trim() || 'Page heading';
              if (textPreview) {
                var editor = window.tinymce && window.tinymce.get('kt-tinymce-2');
                var text = plainText(editor ? editor.getContent() : document.getElementById('kt-tinymce-2').value);
                textPreview.textContent = text.length > 240 ? text.slice(0, 240) + '...' : (text || 'No intro text.');
              }
            }
            form.addEventListener('input', function (event) {
              event.target.classList.remove('dre-invalid');
              changed();
              preview();
            });
            form.addEventListener('change', changed);
            preview();
            // The editor loads after this script, so connect to it once it is ready.
            window.addEventListener('load', function () {
              setTimeout(function () {
                preview();
                var editor = window.tinymce && window.tinymce.get('kt-tinymce-2');
                if (editor) {
                  editor.on('change keyup', function () { changed(); preview(); });
                }
              }, 1200);
            });

            // A newly picked banner is shown straight away, or refused with the reason.
            var image = document.getElementById('listcImage');
            var banner = document.getElementById('listcBanner');
            var note = document.getElementById('listcBannerNote');
            var savedBackground = banner.style.backgroundImage;
            var savedNote = note.textContent;
            image.addEventListener('change', function () {
              var file = image.files[0];
              var bad = '';
              if (file && ['image/jpeg', 'image/png', 'image/webp'].indexOf(file.type) === -1) {
                bad = '"' + file.name + '" is not a JPG, PNG or WEBP picture.';
              } else if (file && file.size > 5 * 1024 * 1024) {
                bad = '"' + file.name + '" is larger than 5 MB.';
              }
              image.setCustomValidity(bad);
              image.classList.toggle('dre-invalid', bad !== '');
              if (bad !== '') {
                image.reportValidity();
              }
              if (file && bad === '') {
                banner.style.backgroundImage = 'url("' + URL.createObjectURL(file) + '")';
                note.textContent = 'New banner, not saved yet.';
              } else {
                banner.style.backgroundImage = savedBackground;
                note.textContent = savedNote;
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

            if (document.getElementById('listcSaved') && window.history.replaceState) {
              // Reloading the page should not show "saved" again.
              window.history.replaceState(null, '', window.location.href.replace(/&saved=1/, ''));
            }
            var errorBox = document.getElementById('listcErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
