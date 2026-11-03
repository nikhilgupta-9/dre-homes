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

function commc_e($text)
{
  return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function commc_query($conn, $sql, array $params = [])
{
  $stmt = mysqli_prepare($conn, $sql);
  if (!$stmt) {
    error_log('Community content query failed: ' . mysqli_error($conn));
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
    error_log('Community content query failed: ' . mysqli_stmt_error($stmt));
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

const COMMC_IMAGE = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
const COMMC_IMAGE_MAX = 5 * 1024 * 1024;

// Checks one uploaded picture. Returns [extension, error]; extension is '' when no file was chosen.
function commc_check_upload($file, $label)
{
  if (!is_array($file) || is_array($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE || (string) $file['name'] === '') {
    return ['', ''];
  }
  if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > COMMC_IMAGE_MAX) {
    return ['', "$label is too large (maximum 5 MB)."];
  }
  if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
    return ['', "$label could not be uploaded. Please try again."];
  }
  $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $real_extension = array_search($finfo->file($file['tmp_name']), ['jpg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'], true);
  if (!isset(COMMC_IMAGE[$extension]) || $real_extension === false) {
    return ['', "$label must be one of: JPG, PNG, WEBP."];
  }
  return [$real_extension, ''];
}

// Pictures: field => [folder, name prefix, label]
$image_fields = [
  'comm_cont_img' => ['assets/media/community/', 'comm_cont_img', 'The banner image'],
  'comm_img_home' => ['assets/media/community/home-bg/', 'home_bg', 'The homepage background'],
];
// Text fields: name => longest length kept (0 = no limit).
$text_fields = ['comm_cont_heading' => 255, 'comm_cont_map' => 0, 'comm_header_home' => 255, 'comm_content_home' => 255];

$id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
$details = $id > 0 ? (commc_query($conn, "SELECT * FROM tbl_community_content WHERE comm_cont_id = ?", [$id]) ?: []) : [];
$details = $details ? $details[0] : null;

$errors = [];
$old = ['comm_cont_heading' => '', 'comm_cont_map' => '', 'comm_header_home' => '', 'comm_content_home' => '', 'is_active' => ''];
if ($details) {
  foreach ($text_fields as $field => $max) {
    $old[$field] = (string) ($details[$field] ?? '');
  }
  $old['is_active'] = $details['is_active'] === '1' ? '1' : '';
}

if ($details && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['update_community_content'])) {
  foreach ($text_fields as $field => $max) {
    $value = isset($_POST[$field]) && is_string($_POST[$field]) ? trim($_POST[$field]) : '';
    $old[$field] = $max > 0 ? mb_substr($value, 0, $max) : $value;
  }
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';

  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if ($old['comm_cont_heading'] === '') {
    $errors[] = 'Page heading is needed.';
  }

  // A picture keeps its saved file unless a new one is chosen. A new one is checked by
  // extension and by its real content, and stored under a name made here.
  $to_move = [];      // [temporary file, final path]
  $replaced = [];     // saved files that a new upload replaces
  $stored = [];       // field => stored file name
  foreach ($image_fields as $field => $image) {
    $stored[$field] = (string) $details[$field];
    list($extension, $error) = commc_check_upload($_FILES[$field] ?? null, $image[2]);
    if ($error !== '') {
      $errors[] = $error;
    } elseif ($extension !== '') {
      if (trim($stored[$field]) !== '') {
        $replaced[] = $image[0] . basename($stored[$field]);
      }
      $stored[$field] = $image[1] . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
      $to_move[] = [$_FILES[$field]['tmp_name'], $image[0] . $stored[$field]];
    }
  }

  if (!$errors) {
    foreach ($to_move as $move) {
      if (!is_dir(dirname($move[1]))) {
        mkdir(dirname($move[1]), 0755, true);
      }
      if (!move_uploaded_file($move[0], $move[1])) {
        $errors[] = 'A picture could not be stored on the server. Nothing was saved.';
        break;
      }
    }
  }

  if (!$errors) {
    $old_data = $logger->getOldRecordData('tbl_community_content', $id);
    $saved = commc_query(
      $conn,
      "UPDATE tbl_community_content SET comm_cont_img = ?, comm_cont_heading = ?, comm_cont_map = ?, comm_header_home = ?, comm_content_home = ?, comm_img_home = ?, is_active = ?, tstp = NOW() WHERE comm_cont_id = ?",
      [$stored['comm_cont_img'], $old['comm_cont_heading'], $old['comm_cont_map'], $old['comm_header_home'], $old['comm_content_home'], $stored['comm_img_home'], $old['is_active'] === '1' ? '1' : '2', $id]
    );
    if (!$saved) {
      foreach ($to_move as $move) {
        if (is_file($move[1])) {
          @unlink($move[1]);
        }
      }
      $errors[] = 'The changes could not be saved. Please try again.';
    } else {
      foreach ($replaced as $path) {
        if (is_file($path)) {
          @unlink($path);
        }
      }
      $logger->log('UPDATE', 'tbl_community_content', $id, $old_data, $logger->getOldRecordData('tbl_community_content', $id), 'Communities page content updated');
      header('Location: ' . BASE_URL . 'edit-community-content.php?edit=' . base64_encode($id) . '&saved=1');
      exit;
    }
  }
}

$just_saved = $details && isset($_GET['saved']) && !$errors;
$banner_url = $details && trim((string) $details['comm_cont_img']) !== '' ? BASE_URL . 'assets/media/community/' . rawurlencode($details['comm_cont_img']) : '';
$home_url = $details && trim((string) $details['comm_img_home']) !== '' ? BASE_URL . 'assets/media/community/home-bg/' . rawurlencode($details['comm_img_home']) : '';
$live_communities = commc_query($conn, "SELECT COUNT(*) AS n FROM tbl_community WHERE is_active = '1'");
$live_communities = $live_communities ? (int) $live_communities[0]['n'] : 0;
$home_communities = commc_query($conn, "SELECT COUNT(*) AS n FROM tbl_community WHERE is_active = '1' AND comm_showonhomepage = '1'");
$home_communities = $home_communities ? (int) $home_communities[0]['n'] : 0;
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

              <!--begin::Communities page content-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-content.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-content-page">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1>Communities page content</h1>
                    <p>The banner and heading of the Communities page, and the communities block on the homepage.</p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL ?>manage-community.php"><i class="bi bi-geo-alt"></i> All communities</a>
                    <a href="<?= commc_e(BASE_URL_HOME) ?>community" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View the page</a>
                  </div>
                </div>

                <?php if (!$details): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This content could not be found.</strong> Open it from the menu: Communities, then Page Content.</div>
                  </div>
                <?php elseif ($just_saved): ?>
                  <div class="dre-form-alert dre-form-ok" role="status" id="commcSaved">
                    <i class="bi bi-check-circle"></i>
                    <div><strong>The changes were saved.</strong> <a href="<?= commc_e(BASE_URL_HOME) ?>community" target="_blank" rel="noopener">View the page</a></div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="commcErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>The changes were not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= commc_e($error) ?></li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($details): ?>
                <div class="dre-content-layout">

                  <!--begin::Form-->
                  <form action="<?= BASE_URL ?>edit-community-content.php?edit=<?= base64_encode($id) ?>" enctype="multipart/form-data" method="post" id="community-content-form" class="dre-form">
                    <input type="hidden" name="csrf" value="<?= commc_e($_SESSION['admin_csrf']) ?>">
                    <input type="hidden" name="update_community_content" value="1">

                    <!-- Communities page -->
                    <section class="dre-glass dre-form-section">
                      <h2><i class="bi bi-window"></i> Communities page</h2>
                      <label class="dre-field">
                        <span>Page heading <b>*</b></span>
                        <input type="text" name="comm_cont_heading" id="commcHeading" value="<?= commc_e($old['comm_cont_heading']) ?>" maxlength="255" autocomplete="off" placeholder="Find Community" required>
                        <small>The title over the banner.</small>
                      </label>
                      <label class="dre-field">
                        <span>Banner image</span>
                        <input type="file" name="comm_cont_img" id="commcImage" accept="image/jpeg,image/png,image/webp">
                        <small>JPG, PNG or WEBP, up to 5 MB. A wide picture works best (about 1920 &times; 600). Leave empty to keep the current one.</small>
                      </label>
                      <label class="dre-field">
                        <span>Map link</span>
                        <textarea name="comm_cont_map" id="commcMap" rows="2" placeholder="https://www.google.com/maps/embed?pb=..."><?= commc_e($old['comm_cont_map']) ?></textarea>
                        <small id="commcMapNote">The Google Maps embed link for the communities map.</small>
                      </label>
                    </section>

                    <!-- Homepage block -->
                    <section class="dre-glass dre-form-section">
                      <h2><i class="bi bi-house-heart"></i> Homepage block</h2>
                      <label class="dre-field">
                        <span>Heading</span>
                        <input type="text" name="comm_header_home" id="commcHomeHeading" value="<?= commc_e($old['comm_header_home']) ?>" maxlength="255" autocomplete="off" placeholder="Explore Signature Neighborhoods">
                        <small>Shown beside the communities carousel on the homepage.</small>
                      </label>
                      <label class="dre-field">
                        <span>Text</span>
                        <textarea name="comm_content_home" id="commcHomeContent" rows="3" maxlength="255" data-count="255" placeholder="A sentence or two about the communities."><?= commc_e($old['comm_content_home']) ?></textarea>
                        <small class="dre-count" id="commcHomeCount"></small>
                      </label>
                      <label class="dre-field">
                        <span>Background image</span>
                        <input type="file" name="comm_img_home" id="commcHomeImage" accept="image/jpeg,image/png,image/webp">
                        <small>JPG, PNG or WEBP, up to 5 MB. Leave empty to keep the current one.</small>
                      </label>
                    </section>

                    <!-- Publishing -->
                    <section class="dre-glass dre-form-section">
                      <h2><i class="bi bi-send"></i> Publishing</h2>
                      <label class="dre-toggle">
                        <input type="checkbox" name="is_active" value="1" id="commcActive" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
                        <i></i>
                        <span><strong>Use this content on the website</strong><small>Off = the Communities page loses its banner and heading, and the homepage block falls back to its built-in text. Keep it on unless you are replacing this content.</small></span>
                      </label>
                    </section>

                    <div class="dre-form-bar">
                      <div class="dre-form-progress">
                        <div id="commcState">No unsaved changes</div>
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
                      <span class="dre-preview-label"><i class="bi bi-eye"></i> Communities page banner</span>
                      <div class="dre-preview-banner" id="commcBanner"<?= $banner_url !== '' ? ' style="background-image: url(\'' . commc_e($banner_url) . '\')"' : '' ?>>
                        <div class="dre-preview-glass">
                          <strong id="commcBannerTitle"><?= commc_e($old['comm_cont_heading']) ?></strong>
                          <span>Home &rsaquo; Communities</span>
                        </div>
                      </div>
                      <small id="commcBannerNote"><?= $banner_url !== '' ? 'Current banner.' : 'No banner picture yet.' ?> <?= number_format($live_communities) ?> live <?= $live_communities === 1 ? 'community is' : 'communities are' ?> listed on this page.</small>
                    </div>
                    <div class="dre-glass dre-preview-card">
                      <span class="dre-preview-label"><i class="bi bi-eye"></i> Homepage block</span>
                      <div class="dre-preview-banner dre-preview-home-bg" id="commcHomeBanner"<?= $home_url !== '' ? ' style="background-image: url(\'' . commc_e($home_url) . '\')"' : '' ?>>
                        <div class="dre-preview-glass dre-preview-glass-light">
                          <strong id="commcHomeTitle"><?= commc_e($old['comm_header_home']) ?></strong>
                          <span id="commcHomeText"></span>
                        </div>
                      </div>
                      <small id="commcHomeNote"><?= $home_url !== '' ? 'Current background.' : 'No background picture yet.' ?> A rough preview. <?= number_format($home_communities) ?> <?= $home_communities === 1 ? 'community is' : 'communities are' ?> set to show on the homepage.</small>
                    </div>
                  </aside>
                </div>
                <?php endif; ?>
              </div>
              <!--end::Communities page content-->

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
          // Communities page content: a live preview next to the form, and a warning before leaving unsaved changes.
          (function () {
            var form = document.getElementById('community-content-form');
            if (!form) {
              return;
            }
            var dirty = false;
            var state = document.getElementById('commcState');
            function changed() {
              dirty = true;
              state.textContent = 'Unsaved changes';
              state.classList.add('dre-warn-text');
            }

            var heading = document.getElementById('commcHeading');
            var homeHeading = document.getElementById('commcHomeHeading');
            var homeContent = document.getElementById('commcHomeContent');
            var mapInput = document.getElementById('commcMap');
            var mapNote = document.getElementById('commcMapNote');
            var mapNoteText = mapNote.textContent;
            function preview() {
              document.getElementById('commcBannerTitle').textContent = heading.value.trim() || 'Page heading';
              document.getElementById('commcHomeTitle').textContent = homeHeading.value.trim() || 'Homepage heading';
              document.getElementById('commcHomeText').textContent = homeContent.value.trim() || 'Homepage text';
              document.getElementById('commcHomeCount').textContent = homeContent.value.length + ' of 255 characters.';
              // The map field is meant for the link, not the whole <iframe> code.
              var map = mapInput.value.trim();
              var looksWrong = map !== '' && !/^https:\/\/[^\s<>"]+$/.test(map);
              mapNote.textContent = looksWrong ? 'This does not look like a link. Paste only the address from the embed code (it starts with https://).' : mapNoteText;
              mapNote.classList.toggle('dre-warn-text', looksWrong);
            }
            form.addEventListener('input', function (event) {
              event.target.classList.remove('dre-invalid');
              changed();
              preview();
            });
            form.addEventListener('change', changed);
            preview();

            // A newly picked picture is shown straight away, or refused with the reason.
            function watchPicture(inputId, bannerId, noteId, what) {
              var input = document.getElementById(inputId);
              var banner = document.getElementById(bannerId);
              var note = document.getElementById(noteId);
              var savedBackground = banner.style.backgroundImage;
              var savedNote = note.textContent;
              input.addEventListener('change', function () {
                var file = input.files[0];
                var bad = '';
                if (file && ['image/jpeg', 'image/png', 'image/webp'].indexOf(file.type) === -1) {
                  bad = '"' + file.name + '" is not a JPG, PNG or WEBP picture.';
                } else if (file && file.size > 5 * 1024 * 1024) {
                  bad = '"' + file.name + '" is larger than 5 MB.';
                }
                input.setCustomValidity(bad);
                input.classList.toggle('dre-invalid', bad !== '');
                if (bad !== '') {
                  input.reportValidity();
                }
                if (file && bad === '') {
                  banner.style.backgroundImage = 'url("' + URL.createObjectURL(file) + '")';
                  note.textContent = 'New ' + what + ', not saved yet.';
                } else {
                  banner.style.backgroundImage = savedBackground;
                  note.textContent = savedNote;
                }
              });
            }
            watchPicture('commcImage', 'commcBanner', 'commcBannerNote', 'banner');
            watchPicture('commcHomeImage', 'commcHomeBanner', 'commcHomeNote', 'background');

            form.addEventListener('invalid', function (event) { event.target.classList.add('dre-invalid'); }, true);
            form.addEventListener('submit', function () {
              dirty = false;
            });
            window.addEventListener('beforeunload', function (event) {
              if (dirty) { event.preventDefault(); event.returnValue = ''; }
            });

            if (document.getElementById('commcSaved') && window.history.replaceState) {
              // Reloading the page should not show "saved" again.
              window.history.replaceState(null, '', window.location.href.replace(/&saved=1/, ''));
            }
            var errorBox = document.getElementById('commcErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
