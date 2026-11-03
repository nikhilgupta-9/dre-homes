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

// This file is used for both adding and editing: this copy adds a guide (edit-guide.php changes one).
$editing = false;
$guide_pages = guide_pages();

$id = 0;
$details = null;
if ($editing) {
  $id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
  $details = $id > 0 ? (guide_query($conn, "SELECT * FROM tbl_guide WHERE guide_id = ?", [$id]) ?: []) : [];
  $details = $details ? $details[0] : null;
}
$can_show_form = !$editing || $details;
$page_name = $details ? ($guide_pages[$id] ?? '') : '';

// Text fields: form field => [column, longest length kept (0 = no limit)]
$text_fields = [
  'heading' => ['guide_heading', 255], 'intro' => ['guide_short_description', 0], 'slug' => ['guide_slug', 200],
  'meta_title' => ['meta_title', 255], 'meta_keyword' => ['meta_keyword', 255], 'meta_description' => ['meta_description', 0], 'meta_tags' => ['meta_tags', 0],
];
$errors = [];
$step_notice = '';
$old = ['is_active' => '1'];
foreach ($text_fields as $field => $spec) {
  $old[$field] = $details ? (string) $details[$spec[0]] : '';
}
if ($details) {
  $old['is_active'] = $details['is_active'] === '1' ? '1' : '';
}

// ---------------------------------------------------------------
// Steps of this guide: show, hide or delete one (POST only, with a token)
// ---------------------------------------------------------------
if ($details && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['step_action'])) {
  $token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
  $action = is_string($_POST['step_action']) ? $_POST['step_action'] : '';
  $step_id = (int) ($_POST['step_id'] ?? 0);
  $step = $token_ok && $step_id > 0 ? guide_query($conn, "SELECT * FROM tbl_guide_img WHERE guide_img_id = ? AND guide_id = ?", [$step_id, $id]) : [];
  $done = '';
  if ($step && ($action === 'show' || $action === 'hide')) {
    guide_query($conn, "UPDATE tbl_guide_img SET is_active = ? WHERE guide_img_id = ?", [$action === 'show' ? '1' : '2', $step_id]);
    $logger->log('UPDATE', 'tbl_guide_img', $step_id, ['is_active' => $step[0]['is_active']], ['is_active' => $action === 'show' ? '1' : '2'], 'Guide step ' . ($action === 'show' ? 'shown: ' : 'hidden: ') . $step[0]['guide_head']);
    $done = $action === 'show' ? 'shown' : 'hidden';
  } elseif ($step && $action === 'delete') {
    $old_data = $logger->getOldRecordData('tbl_guide_img', $step_id);
    $logger->log('DELETE', 'tbl_guide_img', $step_id, $old_data, null, 'Guide step deleted: ' . $step[0]['guide_head']);
    $logger->archiveDeletedRecord('tbl_guide_img', $step_id, $old_data);
    if (guide_query($conn, "DELETE FROM tbl_guide_img WHERE guide_img_id = ?", [$step_id])) {
      $name = basename(trim((string) $step[0]['guide_image']));
      if ($name !== '' && is_file(GUIDE_FOLDER . $name)) {
        @unlink(GUIDE_FOLDER . $name);
      }
    }
    $done = 'deleted';
  }
  if ($done !== '') {
    header('Location: ' . BASE_URL . 'edit-guide.php?edit=' . base64_encode($id) . '&step=' . $done . '#sec-steps');
    exit;
  }
  $step_notice = 'Nothing was changed. Please reload the page and try again.';
}

// ---------------------------------------------------------------
// The guide itself
// ---------------------------------------------------------------
if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_guide'])) {
  $in = [];
  foreach ($text_fields as $field => $spec) {
    $value = isset($_POST[$field]) && is_string($_POST[$field]) ? trim($_POST[$field]) : '';
    $in[$field] = $spec[1] > 0 ? mb_substr($value, 0, $spec[1]) : $value;
  }
  $old = $in;
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';

  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if ($in['heading'] === '') {
    $errors[] = 'Guide name is needed.';
  }
  // The short name is kept as it is unless it was changed; a new one is made from the guide name.
  $saved_slug = $details ? (string) $details['guide_slug'] : '';
  $slug = $saved_slug !== '' && $in['slug'] === $saved_slug ? $saved_slug : trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($in['slug'] !== '' ? $in['slug'] : $in['heading'])), '-');

  if (!$errors) {
    $data = [];
    foreach ($text_fields as $field => $spec) {
      $data[$spec[0]] = $in[$field];
    }
    $data['guide_slug'] = $slug;
    $data['is_active'] = $old['is_active'] === '1' ? '1' : '2';
    if ($editing) {
      $old_data = $logger->getOldRecordData('tbl_guide', $id);
      $assignments = [];
      foreach (array_keys($data) as $column) {
        $assignments[] = "$column = ?";
      }
      $saved = guide_query($conn, 'UPDATE tbl_guide SET ' . implode(', ', $assignments) . ', tstp = NOW() WHERE guide_id = ?', array_merge(array_values($data), [$id]));
      $record_id = $id;
    } else {
      $saved = guide_query($conn, 'INSERT INTO tbl_guide (' . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ')', array_values($data));
      $record_id = $saved ? (int) mysqli_insert_id($conn) : 0;
    }
    if (!$saved) {
      $errors[] = 'The guide could not be saved. Please try again.';
    } else {
      if ($editing) {
        $logger->log('UPDATE', 'tbl_guide', $id, $old_data, $logger->getOldRecordData('tbl_guide', $id), 'Guide updated: ' . $in['heading']);
      } else {
        $logger->log('INSERT', 'tbl_guide', $record_id, null, $logger->getOldRecordData('tbl_guide', $record_id), 'Guide added: ' . $in['heading']);
      }
      // A new guide goes straight to its edit page, where its steps are added.
      header('Location: ' . BASE_URL . 'edit-guide.php?edit=' . base64_encode($record_id) . '&saved=' . ($editing ? '1' : 'new'));
      exit;
    }
  }
}

$steps = $details ? (guide_query($conn, "SELECT * FROM tbl_guide_img WHERE guide_id = ? ORDER BY guide_img_id", [$id]) ?: []) : [];
$just_saved = $details && isset($_GET['saved']) && !$errors;
$step_done = is_string($_GET['step'] ?? null) ? $_GET['step'] : '';
$step_notice = ['added' => 'The step was added.', 'saved' => 'The step was saved.', 'shown' => 'The step is now shown.', 'hidden' => 'The step is now hidden.', 'deleted' => 'The step was deleted. A copy of the record is kept in the delete log.'][$step_done] ?? $step_notice;
$val = function ($field) use (&$old) {
  return guide_e($old[$field] ?? '');
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
              <!--begin::Guide form-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-articles.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-guides.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-article-form dre-guide-form">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1><?= $editing ? 'Edit guide' : 'Add guide' ?></h1>
                    <p><?= $editing ? ($details ? guide_e($details['guide_heading']) . ' &middot; ' . count($steps) . (count($steps) === 1 ? ' step' : ' steps') : 'This guide could not be found.') : 'Save the guide first, then add its steps.' ?></p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL ?>manage-guide.php"><i class="bi bi-arrow-left"></i> Back to guides</a>
                    <?php if ($page_name !== ''): ?>
                      <a href="<?= guide_e(BASE_URL_HOME . $page_name) ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View on the website</a>
                    <?php endif; ?>
                  </div>
                </div>

                <?php if (!$can_show_form): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This guide could not be found.</strong> It may have been deleted. <a href="<?= BASE_URL ?>manage-guide.php">Back to guides</a></div>
                  </div>
                <?php elseif ($just_saved): ?>
                  <div class="dre-form-alert dre-form-ok" role="status" id="guideSaved">
                    <i class="bi bi-check-circle"></i>
                    <div><strong><?= ($_GET['saved'] ?? '') === 'new' ? 'The guide was added. Now add its steps below.' : 'The changes were saved.' ?></strong></div>
                  </div>
                <?php endif; ?>
                <?php if (!$editing || ($details && $page_name === '')): ?>
                  <div class="dre-form-alert dre-form-bad" role="status">
                    <i class="bi bi-info-circle"></i>
                    <div><strong>A <?= $editing ? '' : 'new ' ?>guide <?= $editing ? 'without a page' : '' ?> does not appear on the website by itself.</strong> Each guide page (Buyer, Seller, Tenant, Landlord) is a page of its own that shows one guide. A developer has to add a page for <?= $editing ? 'this one' : 'a new guide' ?>.</div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="guideErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>Not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= guide_e($error) ?></li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($can_show_form): ?>
                <form action="<?= BASE_URL ?><?= $editing ? 'edit-guide.php?edit=' . base64_encode($id) : 'add-guide.php' ?>" method="post" id="guide-form" class="dre-form">
                  <input type="hidden" name="csrf" value="<?= guide_e($_SESSION['admin_csrf']) ?>">
                  <input type="hidden" name="save_guide" value="1">

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-compass"></i> The guide</h2>
                    <label class="dre-field">
                      <span>Guide name <b>*</b></span>
                      <input type="text" name="heading" value="<?= $val('heading') ?>" maxlength="255" autocomplete="off" placeholder="BUYER GUIDE" required>
                      <small>The heading at the top of the guide.</small>
                    </label>
                    <label class="dre-field">
                      <span>Introduction</span>
                      <textarea name="intro" rows="5" data-count="350" placeholder="A short paragraph shown under the heading, before the steps."><?= $val('intro') ?></textarea>
                      <small class="dre-count"></small>
                    </label>
                  </section>

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-search"></i> SEO</h2>
                    <label class="dre-field">
                      <span>Meta title</span>
                      <input type="text" name="meta_title" value="<?= $val('meta_title') ?>" maxlength="255" autocomplete="off" data-count="60">
                      <small class="dre-count"></small>
                    </label>
                    <label class="dre-field">
                      <span>Meta description</span>
                      <textarea name="meta_description" rows="3" data-count="160"><?= $val('meta_description') ?></textarea>
                      <small class="dre-count"></small>
                    </label>
                    <div class="dre-form-grid dre-cols-2">
                      <label class="dre-field">
                        <span>Meta keywords</span>
                        <input type="text" name="meta_keyword" value="<?= $val('meta_keyword') ?>" maxlength="255" autocomplete="off">
                      </label>
                      <label class="dre-field">
                        <span>Meta tags</span>
                        <textarea name="meta_tags" rows="2"><?= $val('meta_tags') ?></textarea>
                      </label>
                    </div>
                    <label class="dre-field">
                      <span>Short name (slug)</span>
                      <input type="text" name="slug" value="<?= $val('slug') ?>" maxlength="200" autocomplete="off" placeholder="made from the guide name">
                      <small>Kept for reference. The address of a guide page is set by the page itself, not by this field.</small>
                    </label>
                  </section>

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-send"></i> Publishing</h2>
                    <label class="dre-toggle">
                      <input type="checkbox" name="is_active" value="1" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
                      <i></i>
                      <span><strong>Live on the website</strong><small>Off = the guide page shows no heading, introduction or search details.</small></span>
                    </label>
                  </section>

                  <div class="dre-form-bar">
                    <div class="dre-form-progress">
                      <div id="guideState"><?= $editing ? 'No unsaved changes' : 'Not saved yet' ?></div>
                    </div>
                    <div class="dre-form-bar-buttons">
                      <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Save and add steps' ?></button>
                    </div>
                  </div>
                </form>

                <?php if ($details): ?>
                  <!-- The steps of this guide -->
                  <section class="dre-glass dre-form-section dre-guide-steps-box" id="sec-steps">
                    <h2><i class="bi bi-list-ol"></i> Steps</h2>
                    <p class="dre-form-note">The parts of the guide, each with a small picture, a heading and its text. The website shows them in this order.</p>
                    <?php if ($step_notice !== ''): ?>
                      <div class="dre-form-alert dre-form-ok" role="status"><i class="bi bi-info-circle"></i><div><?= guide_e($step_notice) ?></div></div>
                    <?php endif; ?>
                    <?php if (!$steps): ?>
                      <p class="dre-form-note"><strong>No steps yet.</strong> The guide has nothing to read until one is added.</p>
                    <?php else: ?>
                      <ol class="dre-guide-steps">
                        <?php foreach ($steps as $step):
                          $step_id = (int) $step['guide_img_id'];
                          $step_live = $step['is_active'] === '1';
                          $step_text = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $step['guide_short_descp']), ENT_QUOTES, 'UTF-8')));
                          $step_edit = BASE_URL . 'edit-guide-image.php?edit=' . $step_id . '&id=' . (int) $id;
                          $step_image = trim((string) $step['guide_image']);
                        ?>
                          <li class="dre-guide-step<?= $step_live ? '' : ' is-hidden' ?>">
                            <span class="dre-guide-step-icon"><?php if ($step_image !== ''): ?><img src="<?= BASE_URL . GUIDE_FOLDER . guide_e(rawurlencode($step_image)) ?>" alt="" loading="lazy" onerror="this.remove()"><?php else: ?><i class="bi bi-image"></i><?php endif; ?></span>
                            <div>
                              <strong><a href="<?= guide_e($step_edit) ?>"><?= trim((string) $step['guide_head']) !== '' ? guide_e($step['guide_head']) : 'Step without a heading' ?></a><?= $step_live ? '' : ' <em class="dre-pill dre-pill-bad">Hidden</em>' ?></strong>
                              <span><?= $step_text !== '' ? guide_e(mb_strimwidth($step_text, 0, 150, '...')) : 'No text' ?></span>
                            </div>
                            <div class="dre-guide-step-actions">
                              <a class="dre-icon-btn" href="<?= guide_e($step_edit) ?>" title="Edit" aria-label="Edit step <?= guide_e($step['guide_head']) ?>"><i class="bi bi-pencil"></i></a>
                              <form method="post" action="<?= BASE_URL ?>edit-guide.php?edit=<?= base64_encode($id) ?>" class="dre-step-form" data-name="<?= guide_e($step['guide_head']) ?>">
                                <input type="hidden" name="csrf" value="<?= guide_e($_SESSION['admin_csrf']) ?>">
                                <input type="hidden" name="step_id" value="<?= $step_id ?>">
                                <button type="submit" name="step_action" value="<?= $step_live ? 'hide' : 'show' ?>" class="dre-icon-btn" title="<?= $step_live ? 'Shown. Click to hide.' : 'Hidden. Click to show.' ?>" aria-label="<?= $step_live ? 'Hide' : 'Show' ?> step <?= guide_e($step['guide_head']) ?>"><i class="bi <?= $step_live ? 'bi-eye' : 'bi-eye-slash' ?>"></i></button>
                                <button type="submit" name="step_action" value="delete" class="dre-icon-btn" title="Delete" aria-label="Delete step <?= guide_e($step['guide_head']) ?>"><i class="bi bi-trash3"></i></button>
                              </form>
                            </div>
                          </li>
                        <?php endforeach; ?>
                      </ol>
                    <?php endif; ?>
                    <a class="dre-btn dre-btn-main" href="<?= BASE_URL ?>add-guide-image.php?guide_id=<?= (int) $id ?>"><i class="bi bi-plus-lg"></i> Add a step</a>
                  </section>
                <?php endif; ?>
                <?php endif; ?>
              </div>
              <!--end::Guide form-->

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
          // Guide form: character counts, a warning before leaving unsaved changes, and a question before deleting a step.
          (function () {
            var form = document.getElementById('guide-form');
            if (!form) {
              return;
            }
            var dirty = false;
            var state = document.getElementById('guideState');
            form.querySelectorAll('[data-count]').forEach(function (field) {
              var target = parseInt(field.getAttribute('data-count'), 10);
              var note = field.parentNode.querySelector('.dre-count');
              function count() {
                var length = field.value.length;
                note.textContent = length + ' characters' + (length === 0 ? ' - aim for about ' + target + '.' : (length > target ? ' - about ' + target + ' reads best.' : '.'));
                note.classList.toggle('dre-warn-text', length > target);
              }
              field.addEventListener('input', count);
              count();
            });
            form.addEventListener('input', function (event) {
              event.target.classList.remove('dre-invalid');
              dirty = true;
              state.textContent = 'Unsaved changes';
              state.classList.add('dre-warn-text');
            });
            form.addEventListener('invalid', function (event) { event.target.classList.add('dre-invalid'); }, true);
            form.addEventListener('submit', function () { dirty = false; });
            window.addEventListener('beforeunload', function (event) {
              if (dirty) { event.preventDefault(); event.returnValue = ''; }
            });
            document.querySelectorAll('.dre-step-form').forEach(function (stepForm) {
              stepForm.addEventListener('submit', function (event) {
                var button = event.submitter;
                if (button && button.value === 'delete' && !window.confirm('Delete the step "' + stepForm.getAttribute('data-name') + '"? Its picture is removed too. A copy of the record is kept in the delete log.')) {
                  event.preventDefault();
                }
              });
            });
            if (document.getElementById('guideSaved') && window.history.replaceState) {
              // Reloading the page should not show "saved" again.
              window.history.replaceState(null, '', window.location.href.replace(/&saved=[a-z0-9]+/, '').replace(/&step=[a-z]+/, ''));
            }
            var errorBox = document.getElementById('guideErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
