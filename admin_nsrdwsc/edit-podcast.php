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

// This file is used for both adding and editing: this copy changes an episode (add-podcast.php adds one).
$editing = true;

function podf_e($text)
{
  return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function podf_query($conn, $sql, array $params = [])
{
  $stmt = mysqli_prepare($conn, $sql);
  if (!$stmt) {
    error_log('Podcast form query failed: ' . mysqli_error($conn));
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
    error_log('Podcast form query failed: ' . mysqli_stmt_error($stmt));
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

// The 11-character video id inside a YouTube address, or '' when it is not one.
function podf_youtube_id($url)
{
  $url = trim((string) $url);
  if (preg_match('~^https?://(?:www\.|m\.)?youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)([A-Za-z0-9_-]{11})~', $url, $match) || preg_match('~^https?://youtu\.be/([A-Za-z0-9_-]{11})~', $url, $match)) {
    return $match[1];
  }
  return '';
}

// "256M" -> bytes
function podf_bytes($setting)
{
  $number = (float) $setting;
  switch (strtoupper(substr(trim((string) $setting), -1))) {
    case 'G': return (int) ($number * 1073741824);
    case 'M': return (int) ($number * 1048576);
    case 'K': return (int) ($number * 1024);
  }
  return (int) $number;
}

const PODF_THUMB_FOLDER = 'assets/media/podcast/thumb/';
const PODF_VIDEO_FOLDER = 'assets/media/podcast/video/';
const PODF_IMAGE = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
const PODF_IMAGE_MAX = 5 * 1024 * 1024;
const PODF_VIDEO = ['mp4' => 'video/mp4', 'webm' => 'video/webm', 'mov' => 'video/quicktime'];
// The largest video this server accepts in one upload, with a little room for the rest of the form.
$video_max = (int) (min(podf_bytes(ini_get('upload_max_filesize')), podf_bytes(ini_get('post_max_size'))) * 0.95);
$video_max_text = $video_max >= 1073741824 ? round($video_max / 1073741824, 1) . ' GB' : round($video_max / 1048576) . ' MB';

$id = 0;
$details = null;
if ($editing) {
  $id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
  $details = $id > 0 ? (podf_query($conn, "SELECT * FROM tbl_podcast WHERE podcast_id = ?", [$id]) ?: []) : [];
  $details = $details ? $details[0] : null;
} elseif (isset($_GET['id']) && (int) $_GET['id'] > 0) {
  // The old add page also edited an episode through ?id=. Those links now go to the edit page.
  header('Location: ' . BASE_URL . 'edit-podcast.php?edit=' . base64_encode((int) $_GET['id']));
  exit;
}
$can_show_form = !$editing || $details;

$errors = [];
$next_position = podf_query($conn, "SELECT COALESCE(MAX(order_no), 0) + 1 AS n FROM tbl_podcast");
$old = ['title' => '', 'descp' => '', 'youtube_link' => '', 'order_no' => (string) ($next_position ? (int) $next_position[0]['n'] : 1), 'is_main' => '', 'is_active' => '1'];
if ($details) {
  $old = [
    'title' => (string) $details['podcast_title'], 'descp' => (string) $details['podcast_descp'], 'youtube_link' => (string) $details['youtube_link'],
    'order_no' => (string) (int) $details['order_no'], 'is_main' => (int) $details['is_main'] === 1 ? '1' : '', 'is_active' => (int) $details['is_active'] === 1 ? '1' : '',
  ];
}
$saved_thumb = $details ? trim((string) $details['podcast_thumb']) : '';
$saved_video = $details ? trim((string) $details['video_file']) : '';

// A form larger than the server allows arrives empty. Say so instead of showing a blank form.
if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
  $errors[] = 'The upload was larger than this server accepts (' . $video_max_text . '). Use a smaller video file, or use a YouTube link instead.';
}

if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_episode'])) {

  // ---------------------------------------------------------------
  // 1. Read the form
  // ---------------------------------------------------------------
  $old['title'] = isset($_POST['title']) && is_string($_POST['title']) ? mb_substr(trim($_POST['title']), 0, 255) : '';
  $old['descp'] = isset($_POST['descp']) && is_string($_POST['descp']) ? trim($_POST['descp']) : '';
  $old['youtube_link'] = isset($_POST['youtube_link']) && is_string($_POST['youtube_link']) ? mb_substr(trim($_POST['youtube_link']), 0, 255) : '';
  $old['order_no'] = isset($_POST['order_no']) && is_string($_POST['order_no']) ? trim($_POST['order_no']) : '';
  $old['is_main'] = isset($_POST['is_main']) ? '1' : '';
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';
  $remove_video = $saved_video !== '' && isset($_POST['remove_video']);

  // ---------------------------------------------------------------
  // 2. Check it. Nothing is saved and no file is kept until everything is right.
  // ---------------------------------------------------------------
  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if ($old['title'] === '') {
    $errors[] = 'Title is needed.';
  }
  if ($old['youtube_link'] !== '' && podf_youtube_id($old['youtube_link']) === '') {
    $errors[] = 'The YouTube link does not look like a video address. Copy it from the address bar of the video (it has "watch?v=" in it) or from Share.';
  }
  if ($old['order_no'] === '' || !ctype_digit($old['order_no']) || (int) $old['order_no'] < 1 || (int) $old['order_no'] > 9999) {
    $errors[] = 'Position must be a number, 1 or higher.';
  }

  $to_move = [];      // [temporary file, final path]
  $replaced = [];     // saved files that are replaced or removed
  $thumb = $saved_thumb;
  $video = $remove_video ? '' : $saved_video;
  $finfo = new finfo(FILEINFO_MIME_TYPE);

  $file = $_FILES['thumb'] ?? null;
  if (is_array($file) && !is_array($file['name']) && $file['error'] !== UPLOAD_ERR_NO_FILE && (string) $file['name'] !== '') {
    $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > PODF_IMAGE_MAX) {
      $errors[] = 'The thumbnail is too large (maximum 5 MB).';
    } elseif ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
      $errors[] = 'The thumbnail could not be uploaded. Please try again.';
    } elseif (!isset(PODF_IMAGE[$extension]) || ($real = array_search($finfo->file($file['tmp_name']), ['jpg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'], true)) === false) {
      $errors[] = 'The thumbnail must be one of: JPG, PNG, WEBP.';
    } else {
      if ($saved_thumb !== '') {
        $replaced[] = PODF_THUMB_FOLDER . basename($saved_thumb);
      }
      $thumb = 'thumb_' . bin2hex(random_bytes(6)) . '.' . $real;
      $to_move[] = [$file['tmp_name'], PODF_THUMB_FOLDER . $thumb];
    }
  }
  if ($thumb === '' && !$errors) {
    $errors[] = 'Add a thumbnail picture.';
  }

  $file = $_FILES['video'] ?? null;
  if (is_array($file) && !is_array($file['name']) && $file['error'] !== UPLOAD_ERR_NO_FILE && (string) $file['name'] !== '') {
    $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > $video_max) {
      $errors[] = 'The video is too large (maximum ' . $video_max_text . '). Use a YouTube link for long episodes.';
    } elseif ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
      $errors[] = 'The video could not be uploaded. Please try again.';
    } elseif (!isset(PODF_VIDEO[$extension]) || !in_array($finfo->file($file['tmp_name']), PODF_VIDEO, true)) {
      $errors[] = 'The video must be an MP4, WEBM or MOV file.';
    } else {
      if ($saved_video !== '') {
        $replaced[] = PODF_VIDEO_FOLDER . basename($saved_video);
      }
      $video = 'vid_' . bin2hex(random_bytes(6)) . '.' . $extension;
      $to_move[] = [$file['tmp_name'], PODF_VIDEO_FOLDER . $video];
    }
  } elseif ($remove_video) {
    $replaced[] = PODF_VIDEO_FOLDER . basename($saved_video);
  }
  if ($video === '' && $old['youtube_link'] === '' && !$errors) {
    $errors[] = 'Add a YouTube link or upload a video, so there is something to play.';
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
    $values = [$old['title'], $old['descp'], $thumb, $old['youtube_link'], $video, (int) $old['order_no'], $old['is_main'] === '1' ? 1 : 0, $old['is_active'] === '1' ? 1 : 0];
    if ($editing) {
      $old_data = $logger->getOldRecordData('tbl_podcast', $id);
      $saved = podf_query($conn, "UPDATE tbl_podcast SET podcast_title = ?, podcast_descp = ?, podcast_thumb = ?, youtube_link = ?, video_file = ?, order_no = ?, is_main = ?, is_active = ?, updated_on = NOW() WHERE podcast_id = ?", array_merge($values, [$id]));
      $record_id = $id;
    } else {
      $saved = podf_query($conn, "INSERT INTO tbl_podcast (podcast_title, podcast_descp, podcast_thumb, youtube_link, video_file, order_no, is_main, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", $values);
      $record_id = $saved ? (int) mysqli_insert_id($conn) : 0;
    }

    if (!$saved) {
      foreach ($to_move as $move) {
        if (is_file($move[1])) {
          @unlink($move[1]);
        }
      }
      $errors[] = 'The episode could not be saved. Please try again.';
    } else {
      foreach ($replaced as $path) {
        if (is_file($path)) {
          @unlink($path);
        }
      }
      if ($old['is_main'] === '1') {
        // One main episode at a time.
        podf_query($conn, "UPDATE tbl_podcast SET is_main = 0 WHERE podcast_id <> ?", [$record_id]);
      }
      if ($editing) {
        $logger->log('UPDATE', 'tbl_podcast', $id, $old_data, $logger->getOldRecordData('tbl_podcast', $id), 'Podcast episode updated: ' . $old['title']);
      } else {
        $logger->log('INSERT', 'tbl_podcast', $record_id, null, $logger->getOldRecordData('tbl_podcast', $record_id), 'Podcast episode added: ' . $old['title']);
      }
      header('Location: ' . BASE_URL . 'manage-podcast.php?done=' . ($editing ? 'saved' : 'added'));
      exit;
    }
  }
}

$val = function ($field) use (&$old) {
  return podf_e($old[$field] ?? '');
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

              <!--begin::Podcast form-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-articles.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-podcast.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-article-form dre-podcast-form">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1><?= $editing ? 'Edit episode' : 'Add episode' ?></h1>
                    <p><?= $editing ? ($details ? podf_e($details['podcast_title']) : 'This episode could not be found.') : 'Fields marked <b>*</b> are needed.' ?></p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL ?>manage-podcast.php"><i class="bi bi-arrow-left"></i> Back to episodes</a>
                  </div>
                </div>

                <?php if (!$can_show_form): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This episode could not be found.</strong> It may have been deleted. <a href="<?= BASE_URL ?>manage-podcast.php">Back to episodes</a></div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="podfErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>Not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= podf_e($error) ?></li>
                        <?php endforeach; ?>
                      </ul>
                      <em>Files have to be chosen again: a browser does not keep them after an error.</em>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($can_show_form): ?>
                <form action="<?= BASE_URL ?><?= $editing ? 'edit-podcast.php?edit=' . base64_encode($id) : 'add-podcast.php' ?>" enctype="multipart/form-data" method="post" id="podcast-form" class="dre-form">
                  <input type="hidden" name="csrf" value="<?= podf_e($_SESSION['admin_csrf']) ?>">
                  <input type="hidden" name="save_episode" value="1">

                  <!-- About the episode -->
                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-mic"></i> About the episode</h2>
                    <label class="dre-field">
                      <span>Title <b>*</b></span>
                      <input type="text" name="title" value="<?= $val('title') ?>" maxlength="255" autocomplete="off" required>
                    </label>
                    <label class="dre-field">
                      <span>Description</span>
                      <textarea name="descp" rows="4" data-count="300" placeholder="Who is on the episode and what it is about."><?= $val('descp') ?></textarea>
                      <small class="dre-count"></small>
                    </label>
                    <label class="dre-field">
                      <span>Thumbnail <b>*</b></span>
                      <input type="file" name="thumb" id="podfThumb" accept="image/jpeg,image/png,image/webp"<?= $saved_thumb !== '' ? '' : ' required' ?>>
                      <small>JPG, PNG or WEBP, up to 5 MB. A wide picture (16:9) fits the player.<?= $saved_thumb !== '' ? ' Leave empty to keep the current one.' : '' ?></small>
                    </label>
                  </section>

                  <!-- What plays -->
                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-play-btn"></i> What plays</h2>
                    <p class="dre-form-note">Give a YouTube link, upload a video, or both. When both are there the website plays the uploaded video.</p>
                    <div class="dre-form-grid dre-cols-2">
                      <div>
                        <label class="dre-field">
                          <span>YouTube link</span>
                          <input type="text" name="youtube_link" id="podfYoutube" value="<?= $val('youtube_link') ?>" maxlength="255" autocomplete="off" inputmode="url" placeholder="https://www.youtube.com/watch?v=...">
                          <small id="podfYoutubeNote">Copy it from the address bar of the video.</small>
                        </label>
                        <label class="dre-field">
                          <span>Video file</span>
                          <input type="file" name="video" id="podfVideo" accept="video/mp4,video/webm,video/quicktime">
                          <small>MP4, WEBM or MOV, up to <?= podf_e($video_max_text) ?>. For long episodes a YouTube link loads faster.</small>
                        </label>
                        <?php if ($saved_video !== ''): ?>
                          <div class="dre-pod-video-current">
                            <a href="<?= BASE_URL . PODF_VIDEO_FOLDER . podf_e(rawurlencode($saved_video)) ?>" target="_blank" rel="noopener"><i class="bi bi-film"></i> Open the current video</a>
                            <label><input type="checkbox" name="remove_video" value="1"> Remove it</label>
                          </div>
                        <?php endif; ?>
                      </div>
                      <div class="dre-field">
                        <span>How the player looks</span>
                        <div class="dre-pod-player<?= $saved_thumb === '' ? ' is-empty' : '' ?>" id="podfPlayer"<?= $saved_thumb !== '' ? ' style="background-image: url(\'' . podf_e(BASE_URL . PODF_THUMB_FOLDER . rawurlencode($saved_thumb)) . '\')"' : '' ?>><span>The thumbnail appears here.</span></div>
                        <small id="podfPlayerNote"></small>
                      </div>
                    </div>
                  </section>

                  <!-- Publishing -->
                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-send"></i> Publishing</h2>
                    <div class="dre-form-grid dre-cols-3">
                      <label class="dre-field">
                        <span>Position <b>*</b></span>
                        <input type="number" name="order_no" value="<?= $val('order_no') ?>" min="1" max="9999" inputmode="numeric" required>
                        <small>1 comes first in the list of episodes.</small>
                      </label>
                      <label class="dre-toggle">
                        <input type="checkbox" name="is_active" value="1" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
                        <i></i>
                        <span><strong>Live on the website</strong><small>Off = saved but hidden.</small></span>
                      </label>
                      <label class="dre-toggle">
                        <input type="checkbox" name="is_main" value="1" <?= $old['is_main'] === '1' ? 'checked' : '' ?>>
                        <i></i>
                        <span><strong>Main episode</strong><small>The large player at the top of the page. Turning this on takes it off the others.</small></span>
                      </label>
                    </div>
                  </section>

                  <div class="dre-form-bar">
                    <div class="dre-form-progress">
                      <div id="podfState"><?= $editing ? 'No unsaved changes' : 'Not saved yet' ?></div>
                    </div>
                    <div class="dre-form-bar-buttons">
                      <button type="submit" class="dre-btn dre-btn-main" id="podfSave"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Save episode' ?></button>
                    </div>
                  </div>
                </form>
                <?php endif; ?>
              </div>
              <!--end::Podcast form-->

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
          // Podcast form: a preview of the player, quick checks of the link and files, and a warning before leaving unsaved changes.
          (function () {
            var form = document.getElementById('podcast-form');
            if (!form) {
              return;
            }
            var videoMax = <?= (int) $video_max ?>;
            var videoMaxText = <?= json_encode($video_max_text) ?>;
            var hasVideo = <?= json_encode($saved_video !== '') ?>;
            var dirty = false;
            var state = document.getElementById('podfState');
            var youtube = document.getElementById('podfYoutube');
            var youtubeNote = document.getElementById('podfYoutubeNote');
            var youtubeNoteText = youtubeNote.textContent;
            var thumb = document.getElementById('podfThumb');
            var video = document.getElementById('podfVideo');
            var player = document.getElementById('podfPlayer');
            var playerNote = document.getElementById('podfPlayerNote');
            var savedBackground = player.style.backgroundImage;

            function youtubeId(url) {
              var match = /^https?:\/\/(?:www\.|m\.)?youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/|live\/)([A-Za-z0-9_-]{11})/.exec(url) || /^https?:\/\/youtu\.be\/([A-Za-z0-9_-]{11})/.exec(url);
              return match ? match[1] : '';
            }
            function refresh() {
              var link = youtube.value.trim();
              var id = youtubeId(link);
              var wrongLink = link !== '' && id === '';
              youtubeNote.textContent = wrongLink ? 'This does not look like a YouTube video address.' : (id !== '' ? 'YouTube video found.' : youtubeNoteText);
              youtubeNote.classList.toggle('dre-warn-text', wrongLink);
              youtube.classList.toggle('dre-invalid', wrongLink);

              var removing = form.querySelector('[name="remove_video"]');
              var uploaded = video.files.length > 0 || (hasVideo && !(removing && removing.checked));
              playerNote.textContent = uploaded ? 'The website will play the uploaded video.' : (id !== '' ? 'The website will play the YouTube video.' : 'Nothing to play yet: add a YouTube link or a video.');
              playerNote.classList.toggle('dre-warn-text', !uploaded && id === '');
            }
            function changed() {
              dirty = true;
              state.textContent = 'Unsaved changes';
              state.classList.add('dre-warn-text');
            }
            form.addEventListener('input', function () { changed(); refresh(); });
            form.addEventListener('change', function () { changed(); refresh(); });

            // A picked thumbnail is shown in the player straight away, or refused with the reason.
            thumb.addEventListener('change', function () {
              var file = thumb.files[0];
              var bad = '';
              if (file && ['image/jpeg', 'image/png', 'image/webp'].indexOf(file.type) === -1) {
                bad = '"' + file.name + '" is not a JPG, PNG or WEBP picture.';
              } else if (file && file.size > 5 * 1024 * 1024) {
                bad = '"' + file.name + '" is larger than 5 MB.';
              }
              thumb.setCustomValidity(bad);
              thumb.classList.toggle('dre-invalid', bad !== '');
              if (bad !== '') { thumb.reportValidity(); }
              if (file && bad === '') {
                player.style.backgroundImage = 'url("' + URL.createObjectURL(file) + '")';
                player.classList.remove('is-empty');
              } else {
                player.style.backgroundImage = savedBackground;
                player.classList.toggle('is-empty', savedBackground === '');
              }
            });
            video.addEventListener('change', function () {
              var file = video.files[0];
              var bad = '';
              if (file && ['video/mp4', 'video/webm', 'video/quicktime'].indexOf(file.type) === -1) {
                bad = '"' + file.name + '" is not an MP4, WEBM or MOV video.';
              } else if (file && file.size > videoMax) {
                bad = '"' + file.name + '" is larger than ' + videoMaxText + '. Use a YouTube link instead.';
              }
              video.setCustomValidity(bad);
              video.classList.toggle('dre-invalid', bad !== '');
              if (bad !== '') { video.reportValidity(); }
            });

            form.querySelectorAll('[data-count]').forEach(function (field) {
              var target = parseInt(field.getAttribute('data-count'), 10);
              var note = field.parentNode.querySelector('.dre-count');
              function count() {
                note.textContent = field.value.length + ' characters' + (field.value.length > target ? ' - about ' + target + ' reads best.' : '.');
                note.classList.toggle('dre-warn-text', field.value.length > target);
              }
              field.addEventListener('input', count);
              count();
            });

            form.addEventListener('invalid', function (event) { event.target.classList.add('dre-invalid'); }, true);
            form.addEventListener('submit', function () {
              dirty = false;
              var button = document.getElementById('podfSave');
              if (video.files.length > 0) { button.textContent = 'Uploading the video...'; }
              // Disabled a moment later so the form is still sent.
              setTimeout(function () { button.disabled = true; }, 0);
            });
            window.addEventListener('beforeunload', function (event) {
              if (dirty) { event.preventDefault(); event.returnValue = ''; }
            });
            refresh();

            var errorBox = document.getElementById('podfErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
