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

// Users are managed by a super admin only.
$is_super_admin = (int) ($_SESSION['login_type'] ?? 0) === 1;
$my_id = (int) ($_SESSION['user_id'] ?? 0);
$user_roles = ['1' => 'Super admin', '2' => 'Sub admin'];

// This file is used for both adding and editing: this copy adds a user (edit-user.php changes one).
$editing = false;
const USER_PASSWORD_MIN = 8;

$id = 0;
$details = null;
if ($editing && $is_super_admin) {
  $id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
  $details = $id > 0 ? (set_query($conn, "SELECT login_id, username, login_email, login_type, is_active, biometric_allowed, tstp FROM tbl_login WHERE login_id = ?", [$id]) ?: []) : [];
  $details = $details ? $details[0] : null;
}
$can_show_form = $is_super_admin && (!$editing || $details);
$is_me = $editing && $id === $my_id;

$errors = [];
$old = ['username' => '', 'email' => '', 'is_active' => '1', 'biometric' => ''];
if ($details) {
  $old = ['username' => (string) $details['username'], 'email' => (string) $details['login_email'], 'is_active' => $details['is_active'] === '1' ? '1' : '', 'biometric' => (int) $details['biometric_allowed'] === 1 ? '1' : ''];
}
// Devices on which this user has set a fingerprint up (on their own profile page).
$device_count = $details ? count(set_query($conn, "SELECT passkey_id FROM tbl_login_passkeys WHERE login_id = ?", [$id]) ?: []) : 0;

if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_user'])) {
  $old['username'] = isset($_POST['username']) && is_string($_POST['username']) ? mb_substr(trim($_POST['username']), 0, 255) : '';
  $old['email'] = isset($_POST['login_email']) && is_string($_POST['login_email']) ? mb_substr(trim($_POST['login_email']), 0, 255) : '';
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';
  $old['biometric'] = isset($_POST['biometric_allowed']) ? '1' : '';
  $remove_devices = $editing && isset($_POST['remove_devices']);
  // The password is never shown again or put back into the form.
  $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
  $password_again = isset($_POST['password_again']) && is_string($_POST['password_again']) ? $_POST['password_again'] : '';

  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if ($old['username'] === '') {
    $errors[] = 'Name is needed.';
  }
  if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Give a real email address: it is what the user signs in with.';
  } elseif (set_query($conn, "SELECT login_id FROM tbl_login WHERE login_email = ? AND login_id <> ? LIMIT 1", [$old['email'], $id])) {
    $errors[] = 'Another user already signs in with this email address.';
  }
  if (!$editing || $password !== '') {
    if (mb_strlen($password) < USER_PASSWORD_MIN) {
      $errors[] = 'The password needs at least ' . USER_PASSWORD_MIN . ' characters.';
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
      $errors[] = 'The password needs at least one letter and one number.';
    } elseif ($password !== $password_again) {
      $errors[] = 'The two passwords are not the same.';
    }
  }
  if ($editing && $old['is_active'] !== '1') {
    if ($is_me) {
      $errors[] = 'You cannot stop your own account from signing in.';
    } elseif ($details['login_type'] === '1' && count(set_query($conn, "SELECT login_id FROM tbl_login WHERE login_type = '1' AND is_active = '1' AND login_id <> ?", [$id]) ?: []) === 0) {
      $errors[] = 'This is the only super admin who can sign in, so it has to stay allowed.';
    }
  }

  if (!$errors) {
    // Stored the way the sign-in page checks it.
    $password_hash = $password !== '' ? md5(prepStr($password)) : '';
    if ($editing) {
      $sql = "UPDATE tbl_login SET username = ?, login_email = ?, is_active = ?, biometric_allowed = ?, tstp = NOW()" . ($password_hash !== '' ? ", login_password = ?" : '') . " WHERE login_id = ?";
      $values = [$old['username'], $old['email'], $old['is_active'] === '1' ? '1' : '2', $old['biometric'] === '1' ? 1 : 0];
      if ($password_hash !== '') {
        $values[] = $password_hash;
      }
      $values[] = $id;
      $saved = set_query($conn, $sql, $values);
      $record_id = $id;
    } else {
      // New users are sub admins.
      $saved = set_query($conn, "INSERT INTO tbl_login (username, login_email, login_password, login_type, is_active, biometric_allowed, tstp) VALUES (?, ?, ?, '2', ?, ?, NOW())", [$old['username'], $old['email'], $password_hash, $old['is_active'] === '1' ? '1' : '2', $old['biometric'] === '1' ? 1 : 0]);
      $record_id = $saved ? (int) mysqli_insert_id($conn) : 0;
    }
    if (!$saved) {
      $errors[] = 'The user could not be saved. Please try again.';
    } else {
      // The log says what changed, never the password itself.
      $changes = ['username' => $old['username'], 'login_email' => $old['email'], 'is_active' => $old['is_active'] === '1' ? '1' : '2', 'biometric_allowed' => $old['biometric'] === '1' ? '1' : '0'];
      // "Remove the fingerprints" forgets every device: the user has to set them up again.
      if ($remove_devices && $device_count > 0) {
        set_query($conn, "DELETE FROM tbl_login_passkeys WHERE login_id = ?", [$id]);
        $changes['fingerprint_devices'] = 'removed ' . $device_count;
      }
      if ($password_hash !== '') {
        $changes['login_password'] = $editing ? 'changed' : 'set';
      }
      $before = $editing ? ['username' => $details['username'], 'login_email' => $details['login_email'], 'is_active' => $details['is_active'], 'biometric_allowed' => (string) (int) $details['biometric_allowed']] : null;
      $logger->log($editing ? 'UPDATE' : 'INSERT', 'tbl_login', $record_id, $before, $changes, ($editing ? 'User updated: ' : 'User added: ') . $old['username']);
      if ($is_me) {
        $_SESSION['username'] = $old['username'];
        $_SESSION['email_id'] = $old['email'];
      }
      header('Location: ' . BASE_URL . 'manage-user.php?done=' . ($editing ? 'saved' : 'added'));
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
              <!--begin::User form-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-settings.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-form-page dre-settings-form dre-user-form">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1><?= $editing ? 'Edit user' : 'Add user' ?></h1>
                    <p><?= $editing ? ($details ? set_e($details['username']) . ' &middot; ' . set_e($user_roles[$details['login_type']] ?? 'Sub admin') . ($is_me ? ' &middot; this is you' : '') : 'This user could not be shown.') : 'A new user is a sub admin: they can work on content pages, not on users or logs.' ?></p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL ?>manage-user.php"><i class="bi bi-arrow-left"></i> Back to users</a>
                  </div>
                </div>

                <?php if (!$is_super_admin): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-shield-lock"></i>
                    <div><strong>Only a super admin can add or change users.</strong></div>
                  </div>
                <?php elseif (!$can_show_form): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This user could not be found.</strong> <a href="<?= BASE_URL ?>manage-user.php">Back to users</a></div>
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
                      <em>The password has to be typed again.</em>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($can_show_form): ?>
                <form action="<?= BASE_URL ?><?= $editing ? 'edit-user.php?edit=' . base64_encode($id) : 'add-user.php' ?>" method="post" id="settings-form" class="dre-form" autocomplete="off">
                  <input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
                  <input type="hidden" name="save_user" value="1">

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-person"></i> The user</h2>
                    <div class="dre-form-grid dre-cols-2">
                      <label class="dre-field">
                        <span>Name <b>*</b></span>
                        <input type="text" name="username" value="<?= set_e($old['username']) ?>" maxlength="255" autocomplete="off" required>
                        <small>Shown at the top of the admin panel and in the logs.</small>
                      </label>
                      <label class="dre-field">
                        <span>Email <b>*</b></span>
                        <input type="email" name="login_email" value="<?= set_e($old['email']) ?>" maxlength="255" autocomplete="off" required>
                        <small>The user signs in with this address.</small>
                      </label>
                    </div>
                  </section>

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-key"></i> <?= $editing ? 'Change the password' : 'Password' ?></h2>
                    <p class="dre-form-note"><?= $editing ? 'Leave both fields empty to keep the current password. ' : '' ?>At least <?= USER_PASSWORD_MIN ?> characters, with a letter and a number. Passwords are never shown again after saving, so pass it on to the user now.</p>
                    <div class="dre-form-grid dre-cols-2">
                      <label class="dre-field">
                        <span><?= $editing ? 'New password' : 'Password <b>*</b>' ?></span>
                        <input type="password" name="password" id="setPassword" autocomplete="new-password" minlength="<?= USER_PASSWORD_MIN ?>"<?= $editing ? '' : ' required' ?>>
                        <small id="setPasswordNote"></small>
                      </label>
                      <label class="dre-field">
                        <span>The same password again<?= $editing ? '' : ' <b>*</b>' ?></span>
                        <input type="password" name="password_again" id="setPasswordAgain" autocomplete="new-password"<?= $editing ? '' : ' required' ?>>
                        <small id="setPasswordAgainNote"></small>
                      </label>
                    </div>
                    <div class="dre-form-bar-buttons">
                      <button type="button" class="dre-btn" id="setPasswordShow" aria-pressed="false"><i class="bi bi-eye"></i> Show what is typed</button>
                      <button type="button" class="dre-btn" id="setPasswordMake"><i class="bi bi-shuffle"></i> Make one for me</button>
                    </div>
                  </section>

                  <section class="dre-glass dre-form-section">
                    <h2><i class="bi bi-door-open"></i> Sign-in</h2>
                    <label class="dre-toggle">
                      <input type="checkbox" name="is_active" value="1" <?= $old['is_active'] === '1' ? 'checked' : '' ?><?= $is_me ? ' onclick="return false" aria-disabled="true"' : '' ?>>
                      <i></i>
                      <span><strong>Allowed to sign in</strong><small><?= $is_me ? 'This is your own account, so it stays on.' : 'Off = the account is kept but the user cannot sign in.' ?></small></span>
                    </label>
                    <label class="dre-toggle">
                      <input type="checkbox" name="biometric_allowed" value="1" <?= $old['biometric'] === '1' ? 'checked' : '' ?>>
                      <i></i>
                      <span><strong>Allowed to sign in with a fingerprint or face</strong><small>On = the user can set it up on their own device (My Profile) and then sign in without the password, with Touch ID, Face ID or Windows Hello. Off = password only, also on devices that were already set up.</small></span>
                    </label>
                    <?php if ($editing): ?>
                      <p class="dre-form-note" style="margin: 14px 0 0;">
                        <i class="bi bi-fingerprint"></i>
                        <?= $device_count === 0 ? 'This user has not set it up on any device yet.' : 'Set up on ' . $device_count . ($device_count === 1 ? ' device.' : ' devices.') ?>
                      </p>
                      <?php if ($device_count > 0): ?>
                        <label class="dre-toggle">
                          <input type="checkbox" name="remove_devices" value="1">
                          <i></i>
                          <span><strong>Remove the set-up devices when saving</strong><small>For a lost or replaced device: every device is forgotten and the user has to set it up again.</small></span>
                        </label>
                      <?php endif; ?>
                    <?php endif; ?>
                  </section>

                  <div class="dre-form-bar">
                    <div class="dre-form-progress"><div id="setState"><?= $editing ? 'No unsaved changes' : 'Not saved yet' ?></div></div>
                    <div class="dre-form-bar-buttons">
                      <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Add user' ?></button>
                    </div>
                  </div>
                </form>
                <?php endif; ?>
              </div>
              <!--end::User form-->

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
          // User form: password help (show, make one, do the two match) and a warning before leaving unsaved changes.
          (function () {
            var form = document.getElementById('settings-form');
            if (!form) {
              return;
            }
            var minLength = <?= USER_PASSWORD_MIN ?>;
            var dirty = false;
            var state = document.getElementById('setState');
            var password = document.getElementById('setPassword');
            var again = document.getElementById('setPasswordAgain');
            var note = document.getElementById('setPasswordNote');
            var againNote = document.getElementById('setPasswordAgainNote');
            function check() {
              var value = password.value;
              var problem = '';
              if (value !== '') {
                if (value.length < minLength) {
                  problem = 'Too short: ' + value.length + ' of ' + minLength + ' characters.';
                } else if (!/[A-Za-z]/.test(value) || !/[0-9]/.test(value)) {
                  problem = 'Add at least one letter and one number.';
                }
              }
              note.textContent = value === '' ? '' : (problem || 'This password is fine.');
              note.classList.toggle('dre-warn-text', problem !== '');
              var mismatch = again.value !== '' && again.value !== value;
              againNote.textContent = again.value === '' ? '' : (mismatch ? 'Not the same as the first one.' : 'The two match.');
              againNote.classList.toggle('dre-warn-text', mismatch);
              again.setCustomValidity(mismatch ? 'The two passwords are not the same.' : '');
            }
            form.addEventListener('input', function (event) {
              event.target.classList.remove('dre-invalid');
              dirty = true;
              state.textContent = 'Unsaved changes';
              state.classList.add('dre-warn-text');
              check();
            });
            document.getElementById('setPasswordShow').addEventListener('click', function () {
              var showing = password.type === 'text';
              password.type = again.type = showing ? 'password' : 'text';
              this.setAttribute('aria-pressed', showing ? 'false' : 'true');
              this.lastChild.textContent = showing ? ' Show what is typed' : ' Hide what is typed';
            });
            document.getElementById('setPasswordMake').addEventListener('click', function () {
              // Letters and numbers that are easy to read aloud (no 0/O, 1/l/I).
              var letters = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
              var digits = '23456789';
              var all = letters + digits;
              var random = new Uint32Array(14);
              window.crypto.getRandomValues(random);
              var made = letters[random[0] % letters.length] + digits[random[1] % digits.length];
              for (var i = 2; i < random.length; i++) {
                made += all[random[i] % all.length];
              }
              password.value = again.value = made;
              password.type = again.type = 'text';
              document.getElementById('setPasswordShow').setAttribute('aria-pressed', 'true');
              document.getElementById('setPasswordShow').lastChild.textContent = ' Hide what is typed';
              dirty = true;
              state.textContent = 'Unsaved changes';
              state.classList.add('dre-warn-text');
              check();
              password.focus();
              password.select();
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
