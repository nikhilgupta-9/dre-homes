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

// This file is used for both adding and editing: this copy adds a testimonial (edit-testimonial.php changes one).
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

$id = 0;
$details = null;
if ($editing) {
  $id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
  $details = $id > 0 ? (simf_query($conn, "SELECT * FROM tbl_testimonial WHERE test_id = ?", [$id]) ?: []) : [];
  $details = $details ? $details[0] : null;
}
$can_show_form = !$editing || $details;

$errors = [];
$old = ['name' => '', 'rating' => '5', 'text' => '', 'posted_on' => date('Y-m-d'), 'is_active' => '1'];
if ($details) {
  $posted_time = strtotime((string) $details['test_posted_on']);
  $old = [
    'name' => (string) $details['test_client_name'], 'rating' => (string) $details['test_rating'], 'text' => (string) $details['test_descp'],
    'posted_on' => $posted_time ? date('Y-m-d', $posted_time) : '', 'is_active' => $details['is_active'] === '1' ? '1' : '',
  ];
}

if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_testimonial'])) {
  $old['name'] = isset($_POST['name']) && is_string($_POST['name']) ? mb_substr(trim($_POST['name']), 0, 255) : '';
  $old['rating'] = isset($_POST['rating']) && is_string($_POST['rating']) ? trim($_POST['rating']) : '';
  $old['text'] = isset($_POST['text']) && is_string($_POST['text']) ? trim($_POST['text']) : '';
  $old['posted_on'] = isset($_POST['posted_on']) && is_string($_POST['posted_on']) ? trim($_POST['posted_on']) : '';
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';

  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if ($old['name'] === '') {
    $errors[] = 'Client name is needed.';
  }
  if (!preg_match('/^[0-9](\.[0-9])?$/', $old['rating']) || (float) $old['rating'] < 1 || (float) $old['rating'] > 5) {
    $errors[] = 'Rating must be a number from 1 to 5, for example 5 or 4.5.';
  }
  if (simf_plain($old['text']) === '') {
    $errors[] = 'Write what the client said.';
  }
  $posted_time = strtotime($old['posted_on']);
  if ($old['posted_on'] === '' || $posted_time === false) {
    $errors[] = 'Choose the date.';
  }

  if (!$errors) {
    // "5.0" is stored as "5", the way the existing ratings are written.
    $rating = rtrim(rtrim(number_format((float) $old['rating'], 1, '.', ''), '0'), '.');
    $values = [$old['name'], $rating, $old['text'], date('Y-m-d', $posted_time), $old['is_active'] === '1' ? '1' : '2'];
    if ($editing) {
      $old_data = $logger->getOldRecordData('tbl_testimonial', $id);
      $saved = simf_query($conn, "UPDATE tbl_testimonial SET test_client_name = ?, test_rating = ?, test_descp = ?, test_posted_on = ?, is_active = ?, tstp = NOW() WHERE test_id = ?", array_merge($values, [$id]));
      $record_id = $id;
    } else {
      $saved = simf_query($conn, "INSERT INTO tbl_testimonial (test_client_name, test_rating, test_descp, test_posted_on, is_active, tstp) VALUES (?, ?, ?, ?, ?, NOW())", $values);
      $record_id = $saved ? (int) mysqli_insert_id($conn) : 0;
    }
    if (!$saved) {
      $errors[] = 'The testimonial could not be saved. Please try again.';
    } else {
      if ($editing) {
        $logger->log('UPDATE', 'tbl_testimonial', $id, $old_data, $logger->getOldRecordData('tbl_testimonial', $id), 'Testimonial Updated: ' . $old['name']);
      } else {
        $logger->log('INSERT', 'tbl_testimonial', $record_id, null, $logger->getOldRecordData('tbl_testimonial', $record_id), 'New Testimonial added: ' . $old['name']);
      }
      $another = !$editing && isset($_POST['save_and_add']);
      header('Location: ' . BASE_URL . ($another ? 'add-testimonial.php?added=1' : 'manage-testimonial.php?done=' . ($editing ? 'saved' : 'added')));
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
              <!--begin::Testimonial form-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-simple.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-simple-form">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1><?= $editing ? 'Edit testimonial' : 'Add testimonial' ?></h1>
                    <p><?= $editing ? ($details ? simf_e($details['test_client_name']) : 'This testimonial could not be found.') : 'Fields marked <b>*</b> are needed.' ?></p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL ?>manage-testimonial.php"><i class="bi bi-arrow-left"></i> Back to testimonials</a>
                  </div>
                </div>

                <?php if (!$can_show_form): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This testimonial could not be found.</strong> It may have been deleted. <a href="<?= BASE_URL ?>manage-testimonial.php">Back to testimonials</a></div>
                  </div>
                <?php elseif (isset($_GET['added']) && !$errors): ?>
                  <div class="dre-form-alert dre-form-ok" role="status">
                    <i class="bi bi-check-circle"></i>
                    <div><strong>The testimonial was added.</strong> Add the next one below, or <a href="<?= BASE_URL ?>manage-testimonial.php">go back to the list</a>.</div>
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
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($can_show_form): ?>
                <form action="<?= BASE_URL ?><?= $editing ? 'edit-testimonial.php?edit=' . base64_encode($id) : 'add-testimonial.php' ?>" method="post" id="simple-form" class="dre-form">
                  <input type="hidden" name="csrf" value="<?= simf_e($_SESSION['admin_csrf']) ?>">
                  <input type="hidden" name="save_testimonial" value="1">

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-chat-quote"></i> The testimonial</h2>
                    <div class="dre-form-grid dre-cols-2">
                      <label class="dre-field">
                        <span>Client name <b>*</b></span>
                        <input type="text" name="name" id="simfName" value="<?= simf_e($old['name']) ?>" maxlength="255" autocomplete="off" required>
                      </label>
                      <div class="dre-field">
                        <label for="simfRating"><span>Rating <b>*</b></span></label>
                        <div class="dre-rating-pick">
                          <input type="number" name="rating" id="simfRating" value="<?= simf_e($old['rating']) ?>" min="1" max="5" step="0.1" inputmode="decimal" required>
                          <span class="dre-sim-stars" aria-hidden="true"><i id="simfStars" style="width: 0%"></i></span>
                        </div>
                        <small>From 1 to 5. Half and tenth steps are fine, for example 4.5.</small>
                      </div>
                    </div>
                    <div class="dre-field">
                      <label for="kt-tinymce-4"><span>What the client said <b>*</b></span></label>
                      <textarea name="text" class="tinymce-editor" id="kt-tinymce-4" rows="10"><?= simf_e($old['text']) ?></textarea>
                      <small>The website shows this as plain text: bold, colours and links are left out there.</small>
                    </div>
                    <div class="dre-field">
                      <span>How the card reads on the website</span>
                      <div class="dre-quote-preview">
                        <span class="dre-sim-stars" aria-hidden="true"><i id="simfCardStars" style="width: 0%"></i></span>
                        <p id="simfCardText"></p>
                        <strong id="simfCardName"></strong>
                        <small id="simfCardDate"></small>
                      </div>
                      <small id="simfCardNote"></small>
                    </div>
                  </section>

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-send"></i> Publishing</h2>
                    <div class="dre-form-grid dre-cols-2">
                      <label class="dre-field">
                        <span>Date <b>*</b></span>
                        <input type="date" name="posted_on" id="simfDate" value="<?= simf_e($old['posted_on']) ?>" required>
                        <small>Shown on the card. The newest date comes first on the website.</small>
                      </label>
                      <label class="dre-toggle">
                        <input type="checkbox" name="is_active" value="1" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
                        <i></i>
                        <span><strong>Live on the website</strong><small>Off = saved but hidden.</small></span>
                      </label>
                    </div>
                  </section>

                  <div class="dre-form-bar">
                    <div class="dre-form-progress">
                      <div id="simfState"><?= $editing ? 'No unsaved changes' : 'Not saved yet' ?></div>
                    </div>
                    <div class="dre-form-bar-buttons">
                      <?php if (!$editing): ?>
                        <button type="submit" name="save_and_add" value="1" class="dre-btn">Save and add another</button>
                      <?php endif; ?>
                      <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Save testimonial' ?></button>
                    </div>
                  </div>
                </form>
                <?php endif; ?>
              </div>
              <!--end::Testimonial form-->

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
          // Testimonial form: stars and a preview of the card, and a warning before leaving unsaved changes.
          (function () {
            var form = document.getElementById('simple-form');
            if (!form) {
              return;
            }
            var dirty = false;
            var state = document.getElementById('simfState');
            var rating = document.getElementById('simfRating');
            function editorText() {
              var editor = window.tinymce && window.tinymce.get('kt-tinymce-4');
              var html = editor ? editor.getContent() : document.getElementById('kt-tinymce-4').value;
              return html.replace(/<[^>]*>/g, ' ').replace(/&nbsp;/g, ' ').replace(/&amp;/g, '&').replace(/\s+/g, ' ').trim();
            }
            function preview() {
              var value = parseFloat(rating.value);
              var width = isNaN(value) ? 0 : Math.max(0, Math.min(5, value)) * 20;
              document.getElementById('simfStars').style.width = width + '%';
              document.getElementById('simfCardStars').style.width = width + '%';
              var text = editorText();
              // The website cuts the card text at 300 characters and offers "Read more".
              document.getElementById('simfCardText').textContent = text === '' ? 'The text appears here.' : (text.length > 300 ? text.slice(0, 300) + '...' : text);
              document.getElementById('simfCardNote').textContent = text.length > 300 ? text.length + ' characters: the card shows the first 300, then "Read more".' : (text === '' ? '' : text.length + ' characters: shown in full on the card.');
              document.getElementById('simfCardName').textContent = document.getElementById('simfName').value.trim() || 'Client name';
              var date = document.getElementById('simfDate').value;
              var parsed = date ? new Date(date + 'T00:00:00') : null;
              document.getElementById('simfCardDate').textContent = parsed && !isNaN(parsed) ? parsed.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '';
            }
            function changed() {
              dirty = true;
              state.textContent = 'Unsaved changes';
              state.classList.add('dre-warn-text');
            }
            form.addEventListener('input', function (event) { event.target.classList.remove('dre-invalid'); changed(); preview(); });
            form.addEventListener('change', function () { changed(); preview(); });
            preview();
            // The editor loads after this script, so connect to it once it is ready.
            window.addEventListener('load', function () {
              setTimeout(function () {
                preview();
                var editor = window.tinymce && window.tinymce.get('kt-tinymce-4');
                if (editor) { editor.on('change keyup', function () { changed(); preview(); }); }
              }, 1200);
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
