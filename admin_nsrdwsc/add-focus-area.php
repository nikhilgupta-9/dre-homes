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

// This file is used for both adding and editing: this copy adds a focus area (edit-focus-area.php changes one).
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

// Icons that suit the focus areas: Font Awesome name => label.
$focus_icons = [
  'fa-wallet' => 'Personal wealth', 'fa-hand-holding-dollar' => 'Rewards', 'fa-chart-line' => 'Growth', 'fa-people-group' => 'Team',
  'fa-user-shield' => 'Protection', 'fa-hand-holding-medical' => 'Care', 'fa-heart-pulse' => 'Health', 'fa-star' => 'Recognition',
  'fa-graduation-cap' => 'Training', 'fa-book-open' => 'Learning', 'fa-scale-balanced' => 'Equality', 'fa-handshake' => 'Partnership',
  'fa-leaf' => 'Sustainability', 'fa-globe' => 'Community', 'fa-lightbulb' => 'Ideas', 'fa-trophy' => 'Achievement',
  'fa-briefcase' => 'Career', 'fa-clock' => 'Flexible time', 'fa-file-invoice-dollar' => 'Pay', 'fa-award' => 'Awards',
];

$id = 0;
$details = null;
if ($editing) {
  $id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
  $details = $id > 0 ? (simf_query($conn, "SELECT * FROM tbl_focus_area WHERE focus_area_id = ?", [$id]) ?: []) : [];
  $details = $details ? $details[0] : null;
}
$can_show_form = !$editing || $details;

$errors = [];
$old = ['heading' => '', 'icon' => 'fal fa-star', 'is_active' => '1'];
if ($details) {
  $old = ['heading' => (string) $details['focus_area_heading'], 'icon' => (string) $details['focus_fa_icon'], 'is_active' => $details['is_active'] === '1' ? '1' : ''];
}

if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_focus'])) {
  $old['heading'] = isset($_POST['heading']) && is_string($_POST['heading']) ? mb_substr(trim($_POST['heading']), 0, 255) : '';
  $old['icon'] = isset($_POST['icon']) && is_string($_POST['icon']) ? mb_substr(trim(preg_replace('/\s+/', ' ', $_POST['icon'])), 0, 255) : '';
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';

  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if ($old['heading'] === '') {
    $errors[] = 'Name is needed.';
  }
  // The icon is a list of class names, for example "fal fa-star". Nothing else is accepted.
  if (!preg_match('/^[a-z0-9-]+( [a-z0-9-]+){0,3}$/', $old['icon']) || !preg_match('/\bfa-[a-z0-9-]+$/', $old['icon'])) {
    $errors[] = 'Choose an icon, or type its class names, for example "fal fa-star".';
  }

  if (!$errors) {
    $values = [$old['icon'], $old['heading'], $old['is_active'] === '1' ? '1' : '2'];
    if ($editing) {
      $old_data = $logger->getOldRecordData('tbl_focus_area', $id);
      $saved = simf_query($conn, "UPDATE tbl_focus_area SET focus_fa_icon = ?, focus_area_heading = ?, is_active = ?, tstp = NOW() WHERE focus_area_id = ?", array_merge($values, [$id]));
      $record_id = $id;
    } else {
      $saved = simf_query($conn, "INSERT INTO tbl_focus_area (focus_fa_icon, focus_area_heading, is_active, tstp) VALUES (?, ?, ?, NOW())", $values);
      $record_id = $saved ? (int) mysqli_insert_id($conn) : 0;
    }
    if (!$saved) {
      $errors[] = 'The focus area could not be saved. Please try again.';
    } else {
      $logger->log($editing ? 'UPDATE' : 'INSERT', 'tbl_focus_area', $record_id, $editing ? $old_data : null, $logger->getOldRecordData('tbl_focus_area', $record_id), ($editing ? 'Focus area updated: ' : 'Focus area added: ') . $old['heading']);
      header('Location: ' . BASE_URL . 'manage-focus-area.php?done=' . ($editing ? 'saved' : 'added'));
      exit;
    }
  }
}
// The icon name inside what is saved: "fal fa-star" -> "fa-star".
$icon_name = preg_match('/\bfa-(?!light|regular|thin|duotone|solid|brands)[a-z0-9-]+/', $old['icon'], $match) ? $match[0] : '';
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
              <!--begin::Focus area form-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-careers.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1><?= $editing ? 'Edit focus area' : 'Add focus area' ?></h1>
                    <p><?= $editing ? ($details ? simf_e($details['focus_area_heading']) : 'This focus area could not be found.') : 'One of the "employee key focus areas" on the Careers page.' ?></p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL ?>manage-focus-area.php"><i class="bi bi-arrow-left"></i> Back to focus areas</a>
                  </div>
                </div>

                <?php if (!$can_show_form): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This focus area could not be found.</strong> <a href="<?= BASE_URL ?>manage-focus-area.php">Back to focus areas</a></div>
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
                <form action="<?= BASE_URL ?><?= $editing ? 'edit-focus-area.php?edit=' . base64_encode($id) : 'add-focus-area.php' ?>" method="post" id="simple-form" class="dre-form">
                  <input type="hidden" name="csrf" value="<?= simf_e($_SESSION['admin_csrf']) ?>">
                  <input type="hidden" name="save_focus" value="1">

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-bullseye"></i> The focus area</h2>
                    <div class="dre-form-grid dre-cols-2">
                      <label class="dre-field">
                        <span>Name <b>*</b></span>
                        <input type="text" name="heading" id="simfHeading" value="<?= simf_e($old['heading']) ?>" maxlength="255" autocomplete="off" placeholder="Training &amp; Development" required>
                      </label>
                      <div class="dre-field">
                        <span>How it looks</span>
                        <div class="dre-icon-big"><i class="fas <?= simf_e($icon_name) ?>" id="simfIconPreview" aria-hidden="true"></i><strong id="simfNamePreview"></strong></div>
                      </div>
                    </div>
                    <div class="dre-field">
                      <span>Icon <b>*</b></span>
                      <div class="dre-icon-choice" role="group" aria-label="Icons to choose from">
                        <?php foreach ($focus_icons as $icon => $label): ?>
                          <button type="button" data-icon="<?= simf_e($icon) ?>" title="<?= simf_e($label) ?>" aria-label="<?= simf_e($label) ?>" class="<?= $icon_name === $icon ? 'is-on' : '' ?>" aria-pressed="<?= $icon_name === $icon ? 'true' : 'false' ?>"><i class="fas <?= simf_e($icon) ?>"></i></button>
                        <?php endforeach; ?>
                      </div>
                    </div>
                    <label class="dre-field">
                      <span>Icon class names</span>
                      <input type="text" name="icon" id="simfIcon" value="<?= simf_e($old['icon']) ?>" maxlength="255" autocomplete="off" spellcheck="false" placeholder="fal fa-star" required>
                      <small>Filled in when you pick an icon above. The website draws it in its light style (&ldquo;fal&rdquo;); here it is shown in the solid style. Any other Font Awesome icon name can be typed.</small>
                    </label>
                  </section>

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-send"></i> Publishing</h2>
                    <label class="dre-toggle">
                      <input type="checkbox" name="is_active" value="1" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
                      <i></i>
                      <span><strong>Shown on the Careers page</strong><small>Off = saved but hidden.</small></span>
                    </label>
                  </section>

                  <div class="dre-form-bar">
                    <div class="dre-form-progress"><div id="simfState"><?= $editing ? 'No unsaved changes' : 'Not saved yet' ?></div></div>
                    <div class="dre-form-bar-buttons">
                      <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Save focus area' ?></button>
                    </div>
                  </div>
                </form>
                <?php endif; ?>
              </div>
              <!--end::Focus area form-->

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
          // Focus area form: pick an icon, see how it looks, and a warning before leaving unsaved changes.
          (function () {
            var form = document.getElementById('simple-form');
            if (!form) {
              return;
            }
            var dirty = false;
            var state = document.getElementById('simfState');
            var icon = document.getElementById('simfIcon');
            var heading = document.getElementById('simfHeading');
            var buttons = Array.prototype.slice.call(form.querySelectorAll('.dre-icon-choice button'));
            function iconName(value) {
              var match = /(?:^|\s)(fa-(?!light|regular|thin|duotone|solid|brands)[a-z0-9-]+)/.exec(value);
              return match ? match[1] : '';
            }
            function preview() {
              var name = iconName(icon.value.trim());
              document.getElementById('simfIconPreview').className = 'fas ' + name;
              document.getElementById('simfNamePreview').textContent = heading.value.trim() || 'Name';
              buttons.forEach(function (button) {
                var on = button.getAttribute('data-icon') === name;
                button.classList.toggle('is-on', on);
                button.setAttribute('aria-pressed', on ? 'true' : 'false');
              });
            }
            function changed() {
              dirty = true;
              state.textContent = 'Unsaved changes';
              state.classList.add('dre-warn-text');
              preview();
            }
            buttons.forEach(function (button) {
              button.addEventListener('click', function () {
                // Keep the style word that is already there ("fal"), and swap the icon name.
                var style = /^(fa[a-z]?|fa-(?:light|regular|thin|duotone|solid))\s/.exec(icon.value.trim());
                icon.value = (style ? style[1] : 'fal') + ' ' + button.getAttribute('data-icon');
                icon.classList.remove('dre-invalid');
                changed();
              });
            });
            form.addEventListener('input', function (event) { event.target.classList.remove('dre-invalid'); changed(); });
            form.addEventListener('change', changed);
            preview();
            form.addEventListener('invalid', function (event) { event.target.classList.add('dre-invalid'); }, true);
            form.addEventListener('submit', function () { dirty = false; });
            window.addEventListener('beforeunload', function (event) {
              if (dirty) { event.preventDefault(); event.returnValue = ''; }
            });
            var errorBox = document.getElementById('simfErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
