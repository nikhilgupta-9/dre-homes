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

const CAREER_FOLDER = 'assets/media/career/';
const CAREER_VIDEO_FOLDER = 'assets/media/career/video/';
const CAREER_VIDEO = ['mp4' => 'video/mp4', 'webm' => 'video/webm', 'mov' => 'video/quicktime'];

// "256M" -> bytes
function career_bytes($setting)
{
  $number = (float) $setting;
  switch (strtoupper(substr(trim((string) $setting), -1))) {
    case 'G': return (int) ($number * 1073741824);
    case 'M': return (int) ($number * 1048576);
    case 'K': return (int) ($number * 1024);
  }
  return (int) $number;
}
// The largest video this server accepts in one upload, with a little room for the rest of the form.
$video_max = (int) (min(career_bytes(ini_get('upload_max_filesize')), career_bytes(ini_get('post_max_size'))) * 0.95);
$video_max_text = $video_max >= 1073741824 ? round($video_max / 1073741824, 1) . ' GB' : round($video_max / 1048576) . ' MB';

$id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
$details = $id > 0 ? (set_query($conn, "SELECT * FROM tbl_career_content WHERE career_cont_id = ?", [$id]) ?: []) : [];
$details = $details ? $details[0] : null;

// Text fields: form name => [column, longest length kept (0 = no limit)]
$fields = [
  'descp' => ['career_cont_descp', 0], 'award_heading' => ['career_award_heading', 255],
  'keyfocus_heading' => ['career_keyfocus_heading', 255], 'resume_heading' => ['career_resume_heading', 255],
];
$errors = [];
$old = ['is_active' => $details && $details['is_active'] === '1' ? '1' : ''];
foreach ($fields as $name => $spec) {
  $old[$name] = $details ? (string) $details[$spec[0]] : '';
}
$saved_image = $details ? trim((string) $details['career_cont_img']) : '';
$saved_video = $details ? trim((string) $details['career_cont_video']) : '';

// A form larger than the server allows arrives empty. Say so instead of showing a blank form.
if ($details && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
  $errors[] = 'The upload was larger than this server accepts (' . $video_max_text . '). Use a smaller video file.';
}

if ($details && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_career_content'])) {
  foreach ($fields as $name => $spec) {
    $value = isset($_POST[$name]) && is_string($_POST[$name]) ? trim($_POST[$name]) : '';
    $old[$name] = $spec[1] > 0 ? mb_substr($value, 0, $spec[1]) : $value;
  }
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';
  $remove_video = $saved_video !== '' && isset($_POST['remove_video']);

  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }

  $to_move = [];      // [temporary file, final path]
  $replaced = [];     // saved files that are replaced or removed
  $image = $saved_image;
  $video = $remove_video ? '' : $saved_video;

  list($extension, $error) = set_check_upload($_FILES['image'] ?? null, 'The banner image');
  if ($error !== '') {
    $errors[] = $error;
  } elseif ($extension !== '') {
    if ($saved_image !== '') {
      $replaced[] = CAREER_FOLDER . basename($saved_image);
    }
    $image = 'career_cont_img_' . bin2hex(random_bytes(6)) . '.' . $extension;
    $to_move[] = [$_FILES['image']['tmp_name'], CAREER_FOLDER . $image];
  }

  $file = $_FILES['video'] ?? null;
  if (is_array($file) && !is_array($file['name']) && $file['error'] !== UPLOAD_ERR_NO_FILE && (string) $file['name'] !== '') {
    $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > $video_max) {
      $errors[] = 'The video is too large (maximum ' . $video_max_text . ').';
    } elseif ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
      $errors[] = 'The video could not be uploaded. Please try again.';
    } elseif (!isset(CAREER_VIDEO[$extension]) || !in_array($finfo->file($file['tmp_name']), CAREER_VIDEO, true)) {
      $errors[] = 'The video must be an MP4, WEBM or MOV file.';
    } else {
      if ($saved_video !== '') {
        $replaced[] = CAREER_VIDEO_FOLDER . basename($saved_video);
      }
      $video = 'career_cont_video_' . bin2hex(random_bytes(6)) . '.' . $extension;
      $to_move[] = [$file['tmp_name'], CAREER_VIDEO_FOLDER . $video];
    }
  } elseif ($remove_video) {
    $replaced[] = CAREER_VIDEO_FOLDER . basename($saved_video);
  }

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
    $old_data = $logger->getOldRecordData('tbl_career_content', $id);
    $saved = set_query(
      $conn,
      "UPDATE tbl_career_content SET career_cont_img = ?, career_cont_descp = ?, career_award_heading = ?, career_cont_video = ?, career_keyfocus_heading = ?, career_resume_heading = ?, is_active = ?, tstp = NOW() WHERE career_cont_id = ?",
      [$image, $old['descp'], $old['award_heading'], $video, $old['keyfocus_heading'], $old['resume_heading'], $old['is_active'] === '1' ? '1' : '2', $id]
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
      $logger->log('UPDATE', 'tbl_career_content', $id, $old_data, $logger->getOldRecordData('tbl_career_content', $id), 'Careers page content updated');
      header('Location: ' . BASE_URL . 'edit-career-content.php?edit=' . base64_encode($id) . '&saved=1');
      exit;
    }
  }
}

$just_saved = $details && isset($_GET['saved']) && !$errors;
$banner_url = $saved_image !== '' ? BASE_URL . CAREER_FOLDER . rawurlencode($saved_image) : '';
$video_url = $saved_video !== '' ? BASE_URL . CAREER_VIDEO_FOLDER . rawurlencode($saved_video) : '';
// What the rest of the Careers page is made of: [number, label, page]
$parts = [];
foreach ([['tbl_position', 'open positions', 'manage-position.php', 'bi-person-workspace'], ['tbl_career_awards', 'award logos', 'manage-career-awards.php', 'bi-trophy'], ['tbl_focus_area', 'focus areas', 'manage-focus-area.php', 'bi-bullseye'], ['tbl_career_gallery', 'gallery pictures', 'manage-career-gallery.php', 'bi-images']] as $part) {
  $counted = set_query($conn, "SELECT COUNT(*) AS n FROM {$part[0]} WHERE is_active = '1'");
  $parts[] = [$counted ? (int) $counted[0]['n'] : 0, $part[1], $part[2], $part[3]];
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
              <!--begin::Careers page content-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-content.css?v=2" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-edit.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-podcast.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-content-page">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1>Careers page content</h1>
                    <p>The banner, video and section headings of the Careers page.</p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= set_e(BASE_URL_HOME) ?>careers" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View the page</a>
                  </div>
                </div>

                <?php if (!$details): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This content could not be found.</strong> Open it from the menu: Careers, then Careers Page, then Page Content.</div>
                  </div>
                <?php elseif ($just_saved): ?>
                  <div class="dre-form-alert dre-form-ok" role="status" id="careerSaved">
                    <i class="bi bi-check-circle"></i>
                    <div><strong>The changes were saved.</strong> <a href="<?= set_e(BASE_URL_HOME) ?>careers" target="_blank" rel="noopener">View the page</a></div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="careerErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>Not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= set_e($error) ?></li>
                        <?php endforeach; ?>
                      </ul>
                      <em>Files have to be chosen again: a browser does not keep them after an error.</em>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($details): ?>
                <div class="dre-content-layout">
                  <form action="<?= BASE_URL ?>edit-career-content.php?edit=<?= base64_encode($id) ?>" enctype="multipart/form-data" method="post" id="career-content-form" class="dre-form">
                    <input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
                    <input type="hidden" name="save_career_content" value="1">

                    <section class="dre-glass dre-form-section">
                      <h2><i class="bi bi-window"></i> Top of the page</h2>
                      <label class="dre-field">
                        <span>Banner image</span>
                        <input type="file" name="image" id="careerImage" accept="image/jpeg,image/png,image/webp,image/gif">
                        <small>JPG, PNG, WEBP or GIF, up to 5 MB. A wide picture works best (about 1920 &times; 600). Leave empty to keep the current one.</small>
                      </label>
                      <div class="dre-field">
                        <label for="kt-tinymce-2"><span>Introduction</span></label>
                        <textarea name="descp" class="tinymce-editor" id="kt-tinymce-2" rows="10"><?= set_e($old['descp']) ?></textarea>
                        <small>Kept for the page, but the Careers page does not show this text at the moment.</small>
                      </div>
                    </section>

                    <section class="dre-glass dre-form-section">
                      <h2><i class="bi bi-camera-reels"></i> Video</h2>
                      <label class="dre-field">
                        <span>Video file</span>
                        <input type="file" name="video" id="careerVideo" accept="video/mp4,video/webm,video/quicktime">
                        <small>MP4, WEBM or MOV, up to <?= set_e($video_max_text) ?>. MP4 plays in every browser. Leave empty to keep the current one.</small>
                      </label>
                      <?php if ($saved_video !== ''): ?>
                        <div class="dre-pod-video-current">
                          <a href="<?= set_e($video_url) ?>" target="_blank" rel="noopener"><i class="bi bi-film"></i> Open the current video</a>
                          <label><input type="checkbox" name="remove_video" value="1"> Remove it</label>
                        </div>
                      <?php endif; ?>
                    </section>

                    <section class="dre-glass dre-form-section">
                      <h2><i class="bi bi-type-h2"></i> Section headings</h2>
                      <p class="dre-form-note">The titles above each part of the page. What is listed under them is edited on its own page.</p>
                      <label class="dre-field">
                        <span>Above the award logos</span>
                        <input type="text" name="award_heading" value="<?= set_e($old['award_heading']) ?>" maxlength="255" autocomplete="off" placeholder="Award Winning Platinum Partner With">
                      </label>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Above the focus areas</span>
                          <input type="text" name="keyfocus_heading" value="<?= set_e($old['keyfocus_heading']) ?>" maxlength="255" autocomplete="off" placeholder="OUR EMPLOYEE KEY FOCUS AREAS">
                        </label>
                        <label class="dre-field">
                          <span>Above the application form</span>
                          <input type="text" name="resume_heading" value="<?= set_e($old['resume_heading']) ?>" maxlength="255" autocomplete="off" placeholder="Send Us Your Resume">
                          <small>Kept for the page, but the form on the Careers page uses its own fixed title at the moment.</small>
                        </label>
                      </div>
                    </section>

                    <section class="dre-glass dre-form-section">
                      <h2><i class="bi bi-send"></i> Publishing</h2>
                      <label class="dre-toggle">
                        <input type="checkbox" name="is_active" value="1" id="careerActive" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
                        <i></i>
                        <span><strong>Use this content on the website</strong><small>Off = the Careers page loses its banner, video and headings. Keep it on.</small></span>
                      </label>
                    </section>

                    <div class="dre-form-bar">
                      <div class="dre-form-progress"><div id="careerState">No unsaved changes</div></div>
                      <div class="dre-form-bar-buttons">
                        <button type="submit" class="dre-btn dre-btn-main" id="careerSave"><i class="bi bi-check2"></i> Save changes</button>
                      </div>
                    </div>
                  </form>

                  <aside class="dre-content-side" aria-label="Preview">
                    <div class="dre-glass dre-preview-card">
                      <span class="dre-preview-label"><i class="bi bi-eye"></i> Page banner</span>
                      <div class="dre-preview-banner" id="careerBanner"<?= $banner_url !== '' ? ' style="background-image: url(\'' . set_e($banner_url) . '\')"' : '' ?>>
                        <div class="dre-preview-glass"><strong>Careers</strong><span>Home &rsaquo; Careers</span></div>
                      </div>
                      <small id="careerBannerNote"><?= $banner_url !== '' ? 'Current banner.' : 'No banner picture yet.' ?></small>
                    </div>
                    <div class="dre-glass dre-preview-card">
                      <span class="dre-preview-label"><i class="bi bi-camera-reels"></i> Video</span>
                      <?php if ($video_url !== ''): ?>
                        <video src="<?= set_e($video_url) ?>" controls preload="metadata" class="dre-career-video" id="careerVideoPreview"></video>
                        <small id="careerVideoNote">Current video.</small>
                      <?php else: ?>
                        <video controls preload="metadata" class="dre-career-video" id="careerVideoPreview" hidden></video>
                        <small id="careerVideoNote">No video yet.</small>
                      <?php endif; ?>
                    </div>
                    <div class="dre-glass dre-preview-card">
                      <span class="dre-preview-label"><i class="bi bi-diagram-3"></i> The rest of the page</span>
                      <div class="dre-career-parts">
                        <?php foreach ($parts as $part): ?>
                          <a href="<?= BASE_URL . $part[2] ?>"><i class="bi <?= set_e($part[3]) ?>"></i> <strong><?= number_format($part[0]) ?></strong> <?= set_e($part[1]) ?></a>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </aside>
                </div>
                <?php endif; ?>
              </div>
              <!--end::Careers page content-->

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
          // Careers page content: previews of the picked banner and video, and a warning before leaving unsaved changes.
          (function () {
            var form = document.getElementById('career-content-form');
            if (!form) {
              return;
            }
            var videoMax = <?= (int) $video_max ?>;
            var videoMaxText = <?= json_encode($video_max_text) ?>;
            var dirty = false;
            var state = document.getElementById('careerState');
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

            var image = document.getElementById('careerImage');
            var banner = document.getElementById('careerBanner');
            var bannerNote = document.getElementById('careerBannerNote');
            var savedBackground = banner.style.backgroundImage;
            var savedBannerNote = bannerNote.textContent;
            image.addEventListener('change', function () {
              var file = image.files[0];
              var bad = '';
              if (file && ['image/jpeg', 'image/png', 'image/webp', 'image/gif'].indexOf(file.type) === -1) {
                bad = '"' + file.name + '" is not a JPG, PNG, WEBP or GIF picture.';
              } else if (file && file.size > 5 * 1024 * 1024) {
                bad = '"' + file.name + '" is larger than 5 MB.';
              }
              image.setCustomValidity(bad);
              image.classList.toggle('dre-invalid', bad !== '');
              if (bad !== '') { image.reportValidity(); }
              if (file && bad === '') {
                banner.style.backgroundImage = 'url("' + URL.createObjectURL(file) + '")';
                bannerNote.textContent = 'New banner, not saved yet.';
              } else {
                banner.style.backgroundImage = savedBackground;
                bannerNote.textContent = savedBannerNote;
              }
            });

            var video = document.getElementById('careerVideo');
            var player = document.getElementById('careerVideoPreview');
            var videoNote = document.getElementById('careerVideoNote');
            var savedVideo = player.getAttribute('src') || '';
            var savedVideoNote = videoNote.textContent;
            video.addEventListener('change', function () {
              var file = video.files[0];
              var bad = '';
              if (file && ['video/mp4', 'video/webm', 'video/quicktime'].indexOf(file.type) === -1) {
                bad = '"' + file.name + '" is not an MP4, WEBM or MOV video.';
              } else if (file && file.size > videoMax) {
                bad = '"' + file.name + '" is larger than ' + videoMaxText + '.';
              }
              video.setCustomValidity(bad);
              video.classList.toggle('dre-invalid', bad !== '');
              if (bad !== '') { video.reportValidity(); }
              if (file && bad === '') {
                player.src = URL.createObjectURL(file);
                player.hidden = false;
                videoNote.textContent = 'New video, not saved yet.';
              } else {
                if (savedVideo !== '') { player.src = savedVideo; } else { player.removeAttribute('src'); player.hidden = true; }
                videoNote.textContent = savedVideoNote;
              }
            });

            var active = document.getElementById('careerActive');
            form.addEventListener('invalid', function (event) { event.target.classList.add('dre-invalid'); }, true);
            form.addEventListener('submit', function (event) {
              if (!active.checked && active.defaultChecked && !window.confirm('This content is switched off. The Careers page will lose its banner, video and headings. Save anyway?')) {
                event.preventDefault();
                return;
              }
              if (window.tinymce) { window.tinymce.triggerSave(); }
              dirty = false;
              var button = document.getElementById('careerSave');
              if (video.files.length > 0) { button.textContent = 'Uploading the video...'; }
              // Disabled a moment later so the form is still sent.
              setTimeout(function () { button.disabled = true; }, 0);
            });
            window.addEventListener('beforeunload', function (event) {
              if (dirty) { event.preventDefault(); event.returnValue = ''; }
            });
            if (document.getElementById('careerSaved') && window.history.replaceState) {
              // Reloading the page should not show "saved" again.
              window.history.replaceState(null, '', window.location.href.replace(/&saved=1/, ''));
            }
            var errorBox = document.getElementById('careerErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
