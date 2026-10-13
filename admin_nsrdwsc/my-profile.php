<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

// Nothing below runs unless someone is signed in.
if (empty($_SESSION['email_id'])) {
  exit;
}

if (empty($_SESSION['admin_csrf'])) {
  $_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
}

function prof_e($text)
{
  return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows.
function prof_rows($conn, $sql, array $params)
{
  $rows = [];
  try {
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
      mysqli_stmt_bind_param($stmt, str_repeat('i', count($params)), ...$params);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      while ($result && ($row = mysqli_fetch_assoc($result))) {
        $rows[] = $row;
      }
      mysqli_stmt_close($stmt);
    }
  } catch (mysqli_sql_exception $error) {
    error_log('Profile query failed: ' . $error->getMessage());
  }
  return $rows;
}

// The signed-in user's own account and the devices they set a fingerprint up on.
$my_id = (int) ($_SESSION['user_id'] ?? 0);
$me = prof_rows($conn, "SELECT login_id, username, login_email, login_type, biometric_allowed, tstp FROM tbl_login WHERE login_id = ?", [$my_id]);
$me = $me ? $me[0] : null;
$is_super_admin = $me && $me['login_type'] === '1';

// A super admin gives the permission, also to themselves: one button here instead of a trip to Users.
if ($is_super_admin && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['allow_myself'])
  && is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
  if ((int) $me['biometric_allowed'] !== 1) {
    prof_rows($conn, "UPDATE tbl_login SET biometric_allowed = 1 WHERE login_id = ?", [$my_id]);
    (new Logger($conn))->log('UPDATE', 'tbl_login', $my_id, ['biometric_allowed' => '0'], ['biometric_allowed' => '1'], 'Fingerprint and face sign-in allowed for: ' . $me['username']);
  }
  header('Location: ' . BASE_URL . 'my-profile.php#fingerprint');
  exit;
}
$allowed = $me && (int) $me['biometric_allowed'] === 1;
$devices = $me ? prof_rows($conn, "SELECT passkey_id, device_name, created_at, last_used_at FROM tbl_login_passkeys WHERE login_id = ? ORDER BY created_at DESC", [$my_id]) : [];
$user_roles = ['1' => 'Super admin', '2' => 'Sub admin'];
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
              <!--begin::Profile-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=2" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-settings.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-profile.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-settings-form dre-profile">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1>My profile</h1>
                    <p><?= $me ? prof_e($me['username']) . ' &middot; ' . prof_e($user_roles[$me['login_type']] ?? 'Sub admin') : 'Your account could not be shown.' ?></p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL ?>dashboard.php"><i class="bi bi-arrow-left"></i> Back to dashboard</a>
                  </div>
                </div>

                <?php if (!$me): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>Your account could not be found.</strong> Please sign out and sign in again.</div>
                  </div>
                <?php else: ?>

                <section class="dre-glass dre-form-section">
                  <h2><i class="bi bi-person"></i> Account</h2>
                  <dl class="dre-profile-facts">
                    <div><dt>Name</dt><dd><?= prof_e($me['username']) ?></dd></div>
                    <div><dt>Email (you sign in with this)</dt><dd><?= prof_e($me['login_email']) ?></dd></div>
                    <div><dt>Role</dt><dd><?= prof_e($user_roles[$me['login_type']] ?? 'Sub admin') ?></dd></div>
                  </dl>
                  <p class="dre-form-note" style="margin: 16px 0 0;">
                    <?php if ($is_super_admin): ?>
                      Name, email and password are changed under <a href="<?= BASE_URL ?>edit-user.php?edit=<?= prof_e(base64_encode($my_id)) ?>">Users &rsaquo; Edit user</a>.
                    <?php else: ?>
                      To change your name, email or password, ask a super admin.
                    <?php endif; ?>
                  </p>
                </section>

                <section class="dre-glass dre-form-section" id="fingerprint">
                  <h2><i class="bi bi-fingerprint"></i> Fingerprint and face sign-in</h2>

                  <?php if (!$allowed): ?>
                    <div class="dre-form-alert dre-form-bad" role="status">
                      <i class="bi bi-shield-lock"></i>
                      <div>
                        <strong>Not switched on for your account yet.</strong>
                        <?php if ($is_super_admin): ?>
                          As a super admin you can switch it on for your own account here. For other users it is switched on under Users &rsaquo; Edit user.
                          <form method="post" action="<?= BASE_URL ?>my-profile.php" style="margin-top: 10px;">
                            <input type="hidden" name="csrf" value="<?= prof_e($_SESSION['admin_csrf']) ?>">
                            <button type="submit" name="allow_myself" value="1" class="dre-btn dre-btn-main" id="bioAllowMe"><i class="bi bi-toggle-on"></i> Switch it on for my account</button>
                          </form>
                        <?php else: ?>
                          A super admin has to allow it first (Users &rsaquo; Edit user &rsaquo; &ldquo;Allowed to sign in with a fingerprint or face&rdquo;). After that you can set it up here.
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php else: ?>
                    <p class="dre-form-note">Set it up once on each device you use. After that, the sign-in page on that device lets you in with its own check instead of the password: Touch ID on a MacBook, Face ID on an iPhone or iPad, face or fingerprint with Windows Hello. Your fingerprint stays on your device: the admin panel only stores a key that the device made.</p>
                  <?php endif; ?>

                  <div class="dre-form-alert" id="bioMessage" role="status" hidden>
                    <i class="bi bi-info-circle"></i>
                    <div></div>
                  </div>

                  <?php if ($devices): ?>
                    <h3>Devices that are set up</h3>
                    <ul class="dre-list dre-profile-devices">
                      <?php foreach ($devices as $device): ?>
                        <li>
                          <span class="dre-avatar"><i class="bi bi-laptop"></i></span>
                          <div class="dre-list-main">
                            <strong><?= prof_e($device['device_name'] !== '' ? $device['device_name'] : 'Device') ?></strong>
                            <span>Set up <?= prof_e(date('d M Y', strtotime($device['created_at']))) ?> &middot; <?= $device['last_used_at'] ? 'last used ' . prof_e(date('d M Y, H:i', strtotime($device['last_used_at']))) : 'not used yet' ?></span>
                          </div>
                          <div class="dre-list-side">
                            <button type="button" class="dre-btn" data-remove="<?= (int) $device['passkey_id'] ?>" data-name="<?= prof_e($device['device_name']) ?>"><i class="bi bi-trash"></i> Remove</button>
                          </div>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                    <?php if (!$allowed): ?>
                      <p class="dre-form-note" style="margin: 12px 0 0;">These devices cannot sign in while fingerprint and face sign-in is switched off for your account.</p>
                    <?php endif; ?>
                  <?php endif; ?>

                  <?php if ($allowed): ?>
                    <div id="bioSetUp" hidden>
                      <h3><?= $devices ? 'Add this device' : 'Set this device up' ?></h3>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Name for this device</span>
                          <input type="text" id="bioName" maxlength="100" autocomplete="off">
                          <small>Only so you can tell your devices apart in the list above.</small>
                        </label>
                      </div>
                      <div class="dre-form-bar-buttons" style="margin-top: 14px;">
                        <button type="button" class="dre-btn dre-btn-main" id="bioAdd"><i class="bi bi-fingerprint"></i> <span>Set up on this device</span></button>
                      </div>
                    </div>
                    <div class="dre-form-alert" id="bioUnsupported" role="status" hidden>
                      <i class="bi bi-info-circle"></i>
                      <div><strong>This browser or device cannot do a fingerprint or face check.</strong> Open this page on a device that has one (a MacBook with Touch ID, an iPhone or iPad with Face ID, a laptop with Windows Hello) in an up-to-date Chrome, Edge or Safari, on the secure (https) address of the admin panel.</div>
                    </div>
                  <?php endif; ?>
                </section>

                <?php endif; ?>
              </div>
              <!--end::Profile-->

            </div>
            <!--end::Container-->
          </div>
          <!--end::Entry-->
        </div>
        <!--end::Content-->

        <?php
        include('includes/footer-inner.php');
        ?>

        <script src="<?= BASE_URL ?>assets/js/dre-biometric.js?v=3"></script>
        <script>
          // Profile page: set a fingerprint up on this device, or remove a device.
          (function () {
            var csrf = <?= json_encode($_SESSION['admin_csrf']) ?>;
            var message = document.getElementById('bioMessage');
            var setUp = document.getElementById('bioSetUp');
            var unsupported = document.getElementById('bioUnsupported');
            var nameField = document.getElementById('bioName');
            var addButton = document.getElementById('bioAdd');
            if (!message) {
              return;
            }
            function say(text, good) {
              message.hidden = false;
              message.classList.toggle('dre-form-ok', good);
              message.classList.toggle('dre-form-bad', !good);
              message.firstElementChild.className = good ? 'bi bi-check-circle' : 'bi bi-exclamation-triangle';
              message.lastElementChild.textContent = text;
              message.scrollIntoView({ block: 'nearest' });
            }
            // After a change the page is loaded again so the list of devices is right.
            function done(answer) {
              if (answer.status === 'success') {
                try { sessionStorage.setItem('dreBioDone', answer.msg); } catch (error) {}
                window.location.reload();
              } else {
                say(answer.msg, false);
              }
              return answer;
            }
            try {
              var last = sessionStorage.getItem('dreBioDone');
              if (last) {
                sessionStorage.removeItem('dreBioDone');
                say(last, true);
              }
            } catch (error) {}

            if (setUp) {
              // A first guess for the device name, from what the browser says about itself.
              var agent = navigator.userAgent;
              var machine = /Macintosh/.test(agent) ? 'Mac' : (/Windows/.test(agent) ? 'Windows laptop' : (/Android/.test(agent) ? 'Android phone' : (/iPhone|iPad/.test(agent) ? 'iPhone / iPad' : 'Laptop')));
              var browser = /Edg\//.test(agent) ? 'Edge' : (/Chrome\//.test(agent) ? 'Chrome' : (/Firefox\//.test(agent) ? 'Firefox' : (/Safari\//.test(agent) ? 'Safari' : '')));
              nameField.value = machine + (browser ? ' (' + browser + ')' : '');
              var method = DreBiometric.method();
              addButton.querySelector('i').className = 'bi ' + method.icon;
              addButton.querySelector('span').textContent = 'Set up ' + method.name + ' on this device';
              DreBiometric.available().then(function (yes) {
                setUp.hidden = !yes;
                unsupported.hidden = yes;
              });
              addButton.addEventListener('click', function () {
                addButton.disabled = true;
                DreBiometric.setUp(csrf, nameField.value.trim()).then(done).then(function () { addButton.disabled = false; });
              });
            }
            document.querySelectorAll('[data-remove]').forEach(function (button) {
              button.addEventListener('click', function () {
                if (!window.confirm('Remove "' + (button.getAttribute('data-name') || 'this device') + '"? It will no longer sign in without the password.')) {
                  return;
                }
                button.disabled = true;
                DreBiometric.remove(csrf, button.getAttribute('data-remove')).then(done).then(function () { button.disabled = false; });
              });
            });
          })();
        </script>
