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

// The header and footer settings share one form layout. This copy edits one of them.
// Fields: [form name, column, label, kind (text, email, tel, url, textarea), longest length, help, icon for links]
// Pictures: [form name, column, label, .ico allowed, can be removed, help]
$form = [
  'title' => 'Header', 'blurb' => 'The logo, contact details and social links used across the website.',
  'table' => 'tbl_header_info', 'id_column' => 'header_info_id', 'folder' => 'assets/media/header/',
  'file' => 'edit-header-info.php', 'log' => 'Header information updated',
  'off_note' => 'Off = the website loses its logo, contact details and social links. Keep it on.',
  'sections' => [
    ['Logos', 'bi-image', 'pictures' => [
      ['header_logo', 'header_logo', 'Website logo', false, false, 'Shown at the top of every page.'],
      ['favicon_logo', 'favicon_logo', 'Favicon', true, false, 'The small icon in the browser tab. A square PNG or ICO.'],
      ['greate_place_logo', 'greate_place_logo', '"Great Place to Work" badge', false, true, 'Shown beside the logo. Remove it to hide the badge.'],
    ], 'fields' => [
      ['header_logo_alt', 'header_logo_alt', 'Logo description (alt text)', 'text', 255, 'Read out by screen readers.', ''],
    ]],
    ['Contact', 'bi-telephone', 'fields' => [
      ['header_contact_number', 'header_contact_number', 'Phone number', 'tel', 255, 'With the country code, for example +971527543243.', ''],
      ['header_email_id', 'header_email_id', 'Email', 'email', 255, '', ''],
    ]],
    ['Social links', 'bi-share', 'links' => true, 'fields' => [
      ['header_fb_link', 'header_fb_link', 'Facebook', 'url', 255, '', 'fa-brands fa-facebook-f'],
      ['header_instagram_link', 'header_instagram_link', 'Instagram', 'url', 255, '', 'fa-brands fa-instagram'],
      ['header_linkedin_link', 'header_linkedin_link', 'LinkedIn', 'url', 255, '', 'fa-brands fa-linkedin-in'],
      ['header_youtube_link', 'header_youtube_link', 'YouTube', 'url', 255, '', 'fa-brands fa-youtube'],
      ['header_spotify_link', 'header_spotify_link', 'TikTok', 'url', 255, 'The website shows this link with the TikTok icon.', 'fa-brands fa-tiktok'],
      ['header_twitter_link', 'header_twitter_link', 'X (Twitter)', 'url', 255, '', 'fa-brands fa-x-twitter'],
    ]],
  ],
];

$id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
$details = $id > 0 ? (set_query($conn, "SELECT * FROM {$form['table']} WHERE {$form['id_column']} = ?", [$id]) ?: []) : [];
$details = $details ? $details[0] : null;

// Every field and picture of the form, in one list each.
$fields = [];
$pictures = [];
foreach ($form['sections'] as $section) {
  foreach ($section['fields'] ?? [] as $field) {
    $fields[$field[0]] = $field;
  }
  foreach ($section['pictures'] ?? [] as $picture) {
    $pictures[$picture[0]] = $picture;
  }
}

$errors = [];
$old = ['is_active' => $details && $details['is_active'] === '1' ? '1' : ''];
foreach ($fields as $name => $field) {
  $old[$name] = $details ? (string) $details[$field[1]] : '';
}

if ($details && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_info'])) {
  foreach ($fields as $name => $field) {
    $value = isset($_POST[$name]) && is_string($_POST[$name]) ? trim($_POST[$name]) : '';
    $old[$name] = $field[4] > 0 ? mb_substr($value, 0, $field[4]) : $value;
  }
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';

  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  foreach ($fields as $name => $field) {
    if ($old[$name] === '') {
      continue;
    }
    if ($field[3] === 'email' && !filter_var($old[$name], FILTER_VALIDATE_EMAIL)) {
      $errors[] = $field[2] . ': this does not look like an email address.';
    } elseif ($field[3] === 'url' && (!filter_var($old[$name], FILTER_VALIDATE_URL) || !preg_match('~^https?://~i', $old[$name]))) {
      $errors[] = $field[2] . ': a link has to start with https://';
    } elseif ($field[3] === 'tel' && !preg_match('/^[0-9+() .\-]{3,}$/', $old[$name])) {
      $errors[] = $field[2] . ': use digits, spaces and + only.';
    }
  }

  // A picture keeps its saved file unless a new one is chosen or it is removed.
  $to_move = [];      // [temporary file, final path]
  $replaced = [];     // saved files that are replaced or removed
  $stored = [];       // column => file name
  foreach ($pictures as $name => $picture) {
    $saved = (string) $details[$picture[1]];
    $stored[$picture[1]] = $saved;
    list($extension, $error) = set_check_upload($_FILES[$name] ?? null, $picture[2], $picture[3]);
    if ($error !== '') {
      $errors[] = $error;
    } elseif ($extension !== '') {
      if (trim($saved) !== '') {
        $replaced[] = $form['folder'] . basename($saved);
      }
      $stored[$picture[1]] = $picture[1] . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
      $to_move[] = [$_FILES[$name]['tmp_name'], $form['folder'] . $stored[$picture[1]]];
    } elseif ($picture[4] && isset($_POST['remove_' . $name]) && trim($saved) !== '') {
      $replaced[] = $form['folder'] . basename($saved);
      $stored[$picture[1]] = '';
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
    $data = $stored;
    foreach ($fields as $name => $field) {
      $data[$field[1]] = $old[$name];
    }
    $data['is_active'] = $old['is_active'] === '1' ? '1' : '2';
    $assignments = [];
    foreach (array_keys($data) as $column) {
      $assignments[] = "$column = ?";
    }
    $old_data = $logger->getOldRecordData($form['table'], $id);
    $saved = set_query($conn, "UPDATE {$form['table']} SET " . implode(', ', $assignments) . ", tstp = NOW() WHERE {$form['id_column']} = ?", array_merge(array_values($data), [$id]));
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
      $logger->log('UPDATE', $form['table'], $id, $old_data, $logger->getOldRecordData($form['table'], $id), $form['log']);
      header('Location: ' . BASE_URL . $form['file'] . '?edit=' . base64_encode($id) . '&saved=1');
      exit;
    }
  }
}

$just_saved = $details && isset($_GET['saved']) && !$errors;
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
              <!--begin::Settings form-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-settings.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-settings-form">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1><?= set_e($form['title']) ?> information</h1>
                    <p><?= set_e($form['blurb']) ?></p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= set_e(BASE_URL_HOME) ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View the website</a>
                  </div>
                </div>

                <?php if (!$details): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>These settings could not be found.</strong> Open them from the menu: Settings, then Header &amp; Footer.</div>
                  </div>
                <?php elseif ($just_saved): ?>
                  <div class="dre-form-alert dre-form-ok" role="status" id="setSaved">
                    <i class="bi bi-check-circle"></i>
                    <div><strong>The changes were saved.</strong> They show on every page of the website straight away.</div>
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
                      <em>Pictures have to be chosen again: a browser does not keep them after an error.</em>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($details): ?>
                <form action="<?= BASE_URL . $form['file'] ?>?edit=<?= base64_encode($id) ?>" enctype="multipart/form-data" method="post" id="settings-form" class="dre-form">
                  <input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
                  <input type="hidden" name="save_info" value="1">

                  <?php foreach ($form['sections'] as $section): ?>
                    <section class="dre-glass dre-form-section">
                      <h2><i class="bi <?= set_e($section[1]) ?>"></i> <?= set_e($section[0]) ?></h2>

                      <?php if (!empty($section['pictures'])): ?>
                        <div class="dre-form-grid dre-cols-<?= count($section['pictures']) >= 3 ? 3 : 2 ?>">
                          <?php foreach ($section['pictures'] as $picture):
                            $saved_name = trim((string) $details[$picture[1]]);
                            $accept = 'image/jpeg,image/png,image/webp,image/gif' . ($picture[3] ? ',image/x-icon,image/vnd.microsoft.icon,.ico' : '');
                          ?>
                            <div class="dre-field">
                              <label for="set_<?= set_e($picture[0]) ?>"><span><?= set_e($picture[2]) ?></span></label>
                              <input type="file" name="<?= set_e($picture[0]) ?>" id="set_<?= set_e($picture[0]) ?>" accept="<?= $accept ?>" class="dre-set-picture" data-icon="<?= $picture[3] ? '1' : '' ?>">
                              <small><?= set_e($picture[5]) ?> <?= $picture[3] ? 'PNG, JPG, WEBP, GIF or ICO' : 'PNG, JPG, WEBP or GIF' ?>, up to 5 MB.</small>
                              <div class="dre-current"<?= $saved_name === '' ? ' hidden' : '' ?>>
                                <img src="<?= $saved_name !== '' ? BASE_URL . $form['folder'] . set_e(rawurlencode($saved_name)) : '' ?>" alt="" data-saved="<?= $saved_name !== '' ? BASE_URL . $form['folder'] . set_e(rawurlencode($saved_name)) : '' ?>">
                                <span><?= $saved_name !== '' ? 'Current picture.' : '' ?></span>
                                <?php if ($picture[4] && $saved_name !== ''): ?>
                                  <label><input type="checkbox" name="remove_<?= set_e($picture[0]) ?>" value="1"> Remove it</label>
                                <?php endif; ?>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      <?php endif; ?>

                      <?php if (!empty($section['links'])): ?>
                        <p class="dre-form-note">Paste the full address of each profile. Leave a field empty when there is no profile.</p>
                      <?php endif; ?>
                      <?php
                      $short = array_values(array_filter($section['fields'] ?? [], function ($field) { return $field[3] !== 'textarea'; }));
                      $long = array_values(array_filter($section['fields'] ?? [], function ($field) { return $field[3] === 'textarea'; }));
                      ?>
                      <?php if ($short): ?>
                        <div class="dre-form-grid dre-cols-<?= count($short) === 1 ? 1 : 2 ?>">
                          <?php foreach ($short as $field): ?>
                            <label class="dre-field">
                              <span><?= set_e($field[2]) ?></span>
                              <?php if ($field[6] !== ''): ?><span class="dre-link-row"><i class="<?= set_e($field[6]) ?>" aria-hidden="true"></i><?php endif; ?>
                              <input type="<?= $field[3] === 'email' ? 'email' : 'text' ?>" name="<?= set_e($field[0]) ?>" value="<?= set_e($old[$field[0]]) ?>" maxlength="<?= (int) $field[4] ?>" autocomplete="off"<?= $field[3] === 'url' ? ' inputmode="url" placeholder="https://" class="dre-set-url"' : ($field[3] === 'tel' ? ' inputmode="tel"' : '') ?>>
                              <?php if ($field[6] !== ''): ?></span><?php endif; ?>
                              <?php if ($field[5] !== ''): ?><small><?= set_e($field[5]) ?></small><?php endif; ?>
                            </label>
                          <?php endforeach; ?>
                        </div>
                      <?php endif; ?>
                      <?php foreach ($long as $field): ?>
                        <label class="dre-field">
                          <span><?= set_e($field[2]) ?></span>
                          <textarea name="<?= set_e($field[0]) ?>" rows="4"><?= set_e($old[$field[0]]) ?></textarea>
                          <?php if ($field[5] !== ''): ?><small><?= set_e($field[5]) ?></small><?php endif; ?>
                        </label>
                      <?php endforeach; ?>
                    </section>
                  <?php endforeach; ?>

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-send"></i> Publishing</h2>
                    <label class="dre-toggle">
                      <input type="checkbox" name="is_active" value="1" id="setActive" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
                      <i></i>
                      <span><strong>Use these settings on the website</strong><small><?= set_e($form['off_note']) ?></small></span>
                    </label>
                  </section>

                  <div class="dre-form-bar">
                    <div class="dre-form-progress"><div id="setState">No unsaved changes</div></div>
                    <div class="dre-form-bar-buttons">
                      <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> Save changes</button>
                    </div>
                  </div>
                </form>
                <?php endif; ?>
              </div>
              <!--end::Settings form-->

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
          // Settings form: previews of picked pictures, quick checks of links, and a warning before leaving unsaved changes.
          (function () {
            var form = document.getElementById('settings-form');
            if (!form) {
              return;
            }
            var dirty = false;
            var state = document.getElementById('setState');
            function changed() {
              dirty = true;
              state.textContent = 'Unsaved changes';
              state.classList.add('dre-warn-text');
            }
            form.addEventListener('input', function (event) {
              var input = event.target;
              if (input.classList.contains('dre-set-url')) {
                // A link that does not start with http is marked straight away.
                input.classList.toggle('dre-invalid', input.value.trim() !== '' && !/^https?:\/\/\S+$/i.test(input.value.trim()));
              } else {
                input.classList.remove('dre-invalid');
              }
              changed();
            });
            form.addEventListener('change', changed);

            form.querySelectorAll('.dre-set-picture').forEach(function (input) {
              var box = input.parentNode.querySelector('.dre-current');
              var img = box.querySelector('img');
              var note = box.querySelector('span');
              var savedNote = note.textContent;
              input.addEventListener('change', function () {
                var file = input.files[0];
                var types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'].concat(input.getAttribute('data-icon') ? ['image/x-icon', 'image/vnd.microsoft.icon'] : []);
                var bad = '';
                if (file && types.indexOf(file.type) === -1) {
                  bad = '"' + file.name + '" is not a picture this field accepts.';
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

            var active = document.getElementById('setActive');
            form.addEventListener('invalid', function (event) { event.target.classList.add('dre-invalid'); }, true);
            form.addEventListener('submit', function (event) {
              if (!active.checked && active.defaultChecked && !window.confirm('These settings are switched off. The website will lose them on every page. Save anyway?')) {
                event.preventDefault();
                return;
              }
              dirty = false;
            });
            window.addEventListener('beforeunload', function (event) {
              if (dirty) { event.preventDefault(); event.returnValue = ''; }
            });
            if (document.getElementById('setSaved') && window.history.replaceState) {
              // Reloading the page should not show "saved" again.
              window.history.replaceState(null, '', window.location.href.replace(/&saved=1/, ''));
            }
            var errorBox = document.getElementById('setErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
