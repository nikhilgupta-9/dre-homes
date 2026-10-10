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

const TAG_FOLDER = 'assets/media/meta/';
const TAG_TITLE_MAX = 70;
const TAG_DESC_MIN = 70;
const TAG_DESC_MAX = 160;

$id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
$details = $id > 0 ? (set_query($conn, "SELECT t.*, p.page_name, p.page_slug FROM tbl_tags t LEFT JOIN tbl_pages p ON p.id = t.page_id WHERE t.id = ?", [$id]) ?: []) : [];
$details = $details ? $details[0] : null;

$errors = [];
$old = ['title' => '', 'description' => '', 'keyword' => '', 'is_active' => ''];
if ($details) {
  $old = ['title' => (string) $details['meta_title'], 'description' => (string) $details['meta_description'], 'keyword' => (string) $details['meta_keyword'], 'is_active' => $details['is_active'] === '1' ? '1' : ''];
}
$saved_image = $details ? trim((string) $details['meta_og_img']) : '';

if ($details && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_tags'])) {
  $old['title'] = isset($_POST['title']) && is_string($_POST['title']) ? mb_substr(trim($_POST['title']), 0, 255) : '';
  $old['description'] = isset($_POST['description']) && is_string($_POST['description']) ? trim($_POST['description']) : '';
  $old['keyword'] = isset($_POST['keyword']) && is_string($_POST['keyword']) ? mb_substr(trim($_POST['keyword']), 0, 255) : '';
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';

  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if ($old['title'] === '') {
    $errors[] = 'Meta title is needed.';
  }
  if ($old['description'] === '') {
    $errors[] = 'Meta description is needed.';
  }
  list($extension, $error) = set_check_upload($_FILES['image'] ?? null, 'The sharing picture');
  $new_file = '';
  if ($error !== '') {
    $errors[] = $error;
  } elseif ($extension !== '') {
    $new_file = 'meta_og_img_' . bin2hex(random_bytes(6)) . '.' . $extension;
  }
  if (!$errors && $new_file !== '') {
    if (!is_dir(TAG_FOLDER)) {
      mkdir(TAG_FOLDER, 0755, true);
    }
    if (!move_uploaded_file($_FILES['image']['tmp_name'], TAG_FOLDER . $new_file)) {
      $errors[] = 'The picture could not be stored on the server. Nothing was saved.';
    }
  }

  if (!$errors) {
    $old_data = $logger->getOldRecordData('tbl_tags', $id);
    // The page these tags belong to is not changed here.
    $saved = set_query($conn, "UPDATE tbl_tags SET meta_title = ?, meta_keyword = ?, meta_description = ?, meta_og_img = ?, is_active = ?, tstp = NOW() WHERE id = ?", [$old['title'], $old['keyword'], $old['description'], $new_file !== '' ? $new_file : $saved_image, $old['is_active'] === '1' ? '1' : '2', $id]);
    if (!$saved) {
      if ($new_file !== '' && is_file(TAG_FOLDER . $new_file)) {
        @unlink(TAG_FOLDER . $new_file);
      }
      $errors[] = 'The changes could not be saved. Please try again.';
    } else {
      if ($new_file !== '' && $saved_image !== '' && is_file(TAG_FOLDER . basename($saved_image))) {
        @unlink(TAG_FOLDER . basename($saved_image));
      }
      $logger->log('UPDATE', 'tbl_tags', $id, $old_data, $logger->getOldRecordData('tbl_tags', $id), 'SEO tags updated: ' . trim((string) $details['page_name']));
      header('Location: ' . BASE_URL . 'manage-tags.php?done=saved');
      exit;
    }
  }
}

$page_name = $details ? trim((string) $details['page_name']) : '';
$page_url = $details ? BASE_URL_HOME . $details['page_slug'] : '';
$image_url = $saved_image !== '' ? BASE_URL . TAG_FOLDER . rawurlencode($saved_image) : '';
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
              <!--begin::SEO tags form-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-articles.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-content.css?v=2" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-settings.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-ai-assist.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-article-form dre-content-page dre-settings-form">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1>SEO meta tags</h1>
                    <p><?= $details ? 'Page: ' . set_e($page_name) . ' &middot; /' . set_e($details['page_slug']) : 'These tags could not be found.' ?></p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL ?>manage-tags.php"><i class="bi bi-arrow-left"></i> Back to all pages</a>
                    <?php if ($details): ?>
                      <a href="<?= set_e($page_url) ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View the page</a>
                    <?php endif; ?>
                  </div>
                </div>

                <?php if (!$details): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>These tags could not be found.</strong> <a href="<?= BASE_URL ?>manage-tags.php">Back to all pages</a></div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="setErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>Not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= set_e($error) ?></li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($details): ?>
                <div class="dre-content-layout">
                  <form action="<?= BASE_URL ?>edit-tags.php?edit=<?= base64_encode($id) ?>" enctype="multipart/form-data" method="post" id="settings-form" class="dre-form">
                    <input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
                    <input type="hidden" name="save_tags" value="1">

                    <section class="dre-glass dre-form-section">
                      <h2><i class="bi bi-search"></i> For search engines</h2>
                      <label class="dre-field">
                        <span>Meta title <b>*</b></span>
                        <input type="text" name="title" id="setTitle" value="<?= set_e($old['title']) ?>" maxlength="255" autocomplete="off" required>
                        <small id="setTitleNote"></small>
                        <button type="button" class="dre-ai-btn" data-ai-task="meta_title" data-ai-target="#setTitle" data-ai-title="#setPageName" data-ai-text="#setDesc,#setKeyword" data-ai-page="A main page of a Dubai real estate brokerage website"><i class="bi bi-stars"></i> Suggest a title</button>
                      </label>
                      <label class="dre-field">
                        <span>Meta description <b>*</b></span>
                        <textarea name="description" id="setDesc" rows="4" required><?= set_e($old['description']) ?></textarea>
                        <small id="setDescNote"></small>
                        <button type="button" class="dre-ai-btn" data-ai-task="meta_description" data-ai-target="#setDesc" data-ai-title="#setPageName" data-ai-text="#setTitle,#setKeyword" data-ai-page="A main page of a Dubai real estate brokerage website"><i class="bi bi-stars"></i> Suggest a description</button>
                      </label>
                      <label class="dre-field">
                        <span>Meta keywords</span>
                        <input type="text" name="keyword" id="setKeyword" value="<?= set_e($old['keyword']) ?>" maxlength="255" autocomplete="off">
                        <input type="hidden" id="setPageName" value="<?= set_e($page_name) ?>">
                        <small>Separated by commas. Search engines give these little weight today.</small>
                      </label>
                    </section>

                    <section class="dre-glass dre-form-section">
                      <h2><i class="bi bi-share"></i> When the page is shared</h2>
                      <label class="dre-field">
                        <span>Sharing picture</span>
                        <input type="file" name="image" id="setImage" accept="image/jpeg,image/png,image/webp,image/gif">
                        <small>Shown when the link is posted on WhatsApp, Facebook or LinkedIn. JPG, PNG, WEBP or GIF, up to 5 MB; 1200 &times; 630 fits best.<?= $saved_image !== '' ? ' Leave empty to keep the current one.' : '' ?></small>
                      </label>
                    </section>

                    <section class="dre-glass dre-form-section">
                      <h2><i class="bi bi-send"></i> Publishing</h2>
                      <label class="dre-toggle">
                        <input type="checkbox" name="is_active" value="1" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
                        <i></i>
                        <span><strong>Use these tags on the website</strong><small>Off = the page has no title, description or sharing picture of its own.</small></span>
                      </label>
                    </section>

                    <div class="dre-form-bar">
                      <div class="dre-form-progress"><div id="setState">No unsaved changes</div></div>
                      <div class="dre-form-bar-buttons">
                        <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> Save changes</button>
                      </div>
                    </div>
                  </form>

                  <aside class="dre-content-side" aria-label="Preview">
                    <div class="dre-glass dre-preview-card">
                      <span class="dre-preview-label"><i class="bi bi-eye"></i> In a search result</span>
                      <div class="dre-search-preview">
                        <span><?= set_e($page_url) ?></span>
                        <strong id="setPreviewTitle"></strong>
                        <p id="setPreviewDesc"></p>
                      </div>
                      <small>A rough preview. Search engines may choose their own text.</small>
                    </div>
                    <div class="dre-glass dre-preview-card">
                      <span class="dre-preview-label"><i class="bi bi-eye"></i> When shared</span>
                      <div class="dre-share-preview">
                        <div id="setShareImage"<?= $image_url !== '' ? ' style="background-image: url(\'' . set_e($image_url) . '\')"' : '' ?>></div>
                        <div>
                          <span><?= set_e(parse_url(BASE_URL_HOME, PHP_URL_HOST)) ?></span>
                          <strong id="setShareTitle"></strong>
                          <p id="setShareDesc"></p>
                        </div>
                      </div>
                      <small id="setShareNote"><?= $image_url !== '' ? 'Current sharing picture.' : 'No sharing picture yet.' ?></small>
                    </div>
                  </aside>
                </div>
                <?php endif; ?>
              </div>
              <!--end::SEO tags form-->

            </div>
            <!--end::Container-->
          </div>
          <!--end::Entry-->
        </div>
        <!--end::Content-->

        <?php
        include('includes/footer-inner.php');
        ?>

        <?php if ($details): ?>
          <script>
            // Where the "Suggest with AI" buttons send their requests. The key stays on the server.
            window.DRE_AI_ASSIST = <?= json_encode(['endpoint' => BASE_URL . 'ajax/ai-assist.php', 'csrf' => $_SESSION['admin_csrf']], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
          </script>
          <script src="<?= BASE_URL ?>assets/js/dre-ai-assist.js?v=1"></script>
        <?php endif; ?>
        <script>
          // SEO tags form: live previews, length advice, and a warning before leaving unsaved changes.
          (function () {
            var form = document.getElementById('settings-form');
            if (!form) {
              return;
            }
            var limits = { title: <?= TAG_TITLE_MAX ?>, descMin: <?= TAG_DESC_MIN ?>, descMax: <?= TAG_DESC_MAX ?> };
            var dirty = false;
            var state = document.getElementById('setState');
            var title = document.getElementById('setTitle');
            var desc = document.getElementById('setDesc');
            function cut(text, length) {
              return text.length > length ? text.slice(0, length - 1).trim() + '...' : text;
            }
            function advise(note, length, text, warn) {
              note.textContent = length + ' characters. ' + text;
              note.classList.toggle('dre-warn-text', warn);
            }
            function refresh() {
              var t = title.value.trim();
              var d = desc.value.trim();
              advise(document.getElementById('setTitleNote'), t.length, t.length > limits.title ? 'Longer than about ' + limits.title + ': the end may be cut in search results.' : 'Up to about ' + limits.title + ' shows in full.', t.length > limits.title);
              advise(document.getElementById('setDescNote'), d.length, d.length < limits.descMin ? 'Short: aim for ' + limits.descMin + ' to ' + limits.descMax + ', a sentence or two about the page.' : (d.length > limits.descMax ? 'Longer than ' + limits.descMax + ': the end will be cut.' : 'A good length.'), d.length < limits.descMin || d.length > limits.descMax);
              document.getElementById('setPreviewTitle').textContent = cut(t || 'Meta title', limits.title);
              document.getElementById('setPreviewDesc').textContent = cut(d || 'Meta description', limits.descMax);
              document.getElementById('setShareTitle').textContent = cut(t || 'Meta title', 90);
              document.getElementById('setShareDesc').textContent = cut(d || 'Meta description', 110);
            }
            function changed() {
              dirty = true;
              state.textContent = 'Unsaved changes';
              state.classList.add('dre-warn-text');
            }
            form.addEventListener('input', function (event) { event.target.classList.remove('dre-invalid'); changed(); refresh(); });
            form.addEventListener('change', changed);
            refresh();

            var image = document.getElementById('setImage');
            var share = document.getElementById('setShareImage');
            var note = document.getElementById('setShareNote');
            var savedBackground = share.style.backgroundImage;
            var savedNote = note.textContent;
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
                share.style.backgroundImage = 'url("' + URL.createObjectURL(file) + '")';
                note.textContent = 'New sharing picture, not saved yet.';
              } else {
                share.style.backgroundImage = savedBackground;
                note.textContent = savedNote;
              }
            });

            form.addEventListener('invalid', function (event) { event.target.classList.add('dre-invalid'); }, true);
            form.addEventListener('submit', function () { dirty = false; });
            window.addEventListener('beforeunload', function (event) {
              if (dirty) { event.preventDefault(); event.returnValue = ''; }
            });
            var errorBox = document.getElementById('setErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
