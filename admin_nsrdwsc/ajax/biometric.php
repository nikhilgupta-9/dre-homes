<?php
// ---------------------------------------------------------------
// Fingerprint sign-in: everything the browser asks the server for.
//
//   action=login_options     sign-in page: a one-time value for the device to sign
//   action=login_verify      sign-in page: check the device's answer and sign the user in
//   action=register_options  profile page: start setting a fingerprint up   (signed in)
//   action=register_verify   profile page: store the device's public key    (signed in)
//   action=remove            profile page: forget one of the user's devices (signed in)
//
// A fingerprint only works for a user that a super admin allowed
// (tbl_login.biometric_allowed = 1) and whose account may sign in.
// ---------------------------------------------------------------
include('../db/config.php');
include('../includes/Logger.php');
include('../includes/WebAuthn.php');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

const BIOMETRIC_MAX_DEVICES = 5;

function bio_send($status, $msg, array $more = [])
{
    echo json_encode(['status' => $status, 'msg' => $msg] + $more);
    exit;
}

// Runs a prepared query and returns its rows (or true for a write, false when it failed).
function bio_query($conn, $sql, array $params = [])
{
    try {
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            error_log('Fingerprint query failed: ' . mysqli_error($conn));
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
            error_log('Fingerprint query failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    } catch (mysqli_sql_exception $error) {
        error_log('Fingerprint query failed: ' . $error->getMessage());
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

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    bio_send('error', 'Not available.');
}
$action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';
$answer = json_decode(is_string($_POST['data'] ?? null) ? $_POST['data'] : '', true);
$answer = is_array($answer) ? $answer : [];

// ----- Sign-in page (nobody is signed in yet) -----

if ($action === 'login_options') {
    // No email is asked for: the device shows the accounts it holds a key for.
    bio_send('success', '', ['options' => ['challenge' => WebAuthn::newChallenge('sign-in'), 'rpId' => WebAuthn::siteId()]]);
}

if ($action === 'login_verify') {
    sleep(1);
    $credential_id = is_string($answer['id'] ?? null) ? $answer['id'] : '';
    $rows = $credential_id !== '' ? bio_query($conn, "SELECT p.passkey_id, p.public_key, p.sign_count, l.login_id, l.username, l.login_email, l.login_type, l.is_active, l.biometric_allowed FROM tbl_login_passkeys p JOIN tbl_login l ON l.login_id = p.login_id WHERE p.credential_id = ? LIMIT 1", [$credential_id]) : [];
    $row = $rows ? $rows[0] : null;
    $email = $row ? $row['login_email'] : 'fingerprint (unknown device)';
    $reason = '';
    $message = 'This device is not set up for the admin panel. Sign in with your password.';
    try {
        if (!$row) {
            // The one-time value is used up even when the device is unknown.
            unset($_SESSION['webauthn_challenge']);
            $reason = 'Fingerprint: unknown device';
        } else {
            $count = WebAuthn::readSignIn($answer, $row['public_key'], (int) $row['sign_count']);
            if ($row['is_active'] !== '1') {
                $reason = 'Account Suspended';
                $message = 'Account Suspended by Sub Admin';
            } elseif ((int) $row['biometric_allowed'] !== 1) {
                $reason = 'Fingerprint: not allowed by admin';
                $message = 'Fingerprint and face sign-in is switched off for your account. Sign in with your password or ask a super admin.';
            }
        }
    } catch (RuntimeException $error) {
        $reason = 'Fingerprint: ' . $error->getMessage();
        $message = $error->getMessage();
    }
    if ($reason !== '') {
        (new Logger($conn))->logLogin($email, 'failed', mb_substr($reason, 0, 250));
        bio_send('error', $message);
    }

    bio_query($conn, "UPDATE tbl_login_passkeys SET sign_count = ?, last_used_at = NOW() WHERE passkey_id = ?", [$count, (int) $row['passkey_id']]);
    // A new session for the signed-in user, filled the same way as the password sign-in.
    session_regenerate_id(true);
    $_SESSION['user_id'] = $row['login_id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['email_id'] = $row['login_email'];
    $_SESSION['login_type'] = $row['login_type'];
    (new Logger($conn))->logLogin($row['login_email'], 'success');
    bio_send('success', 'Login Successful');
}

// ----- Profile page (the signed-in user's own devices) -----

$my_id = (int) ($_SESSION['user_id'] ?? 0);
if (empty($_SESSION['email_id']) || $my_id < 1) {
    bio_send('error', 'Please sign in again.');
}
if (!is_string($_POST['csrf'] ?? null) || empty($_SESSION['admin_csrf']) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    bio_send('error', 'The page expired. Reload it and try again.');
}
$me = bio_query($conn, "SELECT login_id, username, login_email, is_active, biometric_allowed FROM tbl_login WHERE login_id = ? LIMIT 1", [$my_id]);
$me = $me ? $me[0] : null;
if (!$me || $me['is_active'] !== '1') {
    bio_send('error', 'Please sign in again.');
}
$logger = new Logger($conn);

if ($action === 'remove') {
    $passkey_id = (int) ($_POST['passkey_id'] ?? 0);
    $mine = bio_query($conn, "SELECT passkey_id, device_name FROM tbl_login_passkeys WHERE passkey_id = ? AND login_id = ? LIMIT 1", [$passkey_id, $my_id]);
    if (!$mine) {
        bio_send('error', 'This device was already removed.');
    }
    bio_query($conn, "DELETE FROM tbl_login_passkeys WHERE passkey_id = ? AND login_id = ?", [$passkey_id, $my_id]);
    $logger->log('DELETE', 'tbl_login_passkeys', $passkey_id, ['device_name' => $mine[0]['device_name']], null, 'Fingerprint sign-in removed: ' . $me['username'] . ' (' . $mine[0]['device_name'] . ')');
    bio_send('success', 'Removed. This device can no longer sign in without the password.');
}

// Setting a fingerprint up needs the super admin's permission first.
if ((int) $me['biometric_allowed'] !== 1) {
    bio_send('error', 'Fingerprint and face sign-in is not allowed for your account yet. Ask a super admin to switch it on.');
}
$devices = bio_query($conn, "SELECT credential_id FROM tbl_login_passkeys WHERE login_id = ?", [$my_id]) ?: [];

if ($action === 'register_options') {
    if (count($devices) >= BIOMETRIC_MAX_DEVICES) {
        bio_send('error', 'You already have ' . BIOMETRIC_MAX_DEVICES . ' devices. Remove one first.');
    }
    bio_send('success', '', ['options' => [
        'challenge' => WebAuthn::newChallenge('register'),
        'rp' => ['id' => WebAuthn::siteId(), 'name' => 'DRE Homes Admin'],
        // The device stores this next to the key: a number for the account, never the password.
        'user' => ['id' => WebAuthn::encode('dre-admin-' . $my_id), 'name' => $me['login_email'], 'displayName' => $me['username']],
        'exclude' => array_column($devices, 'credential_id'),
    ]]);
}

if ($action === 'register_verify') {
    try {
        $device = WebAuthn::readRegistration($answer);
    } catch (RuntimeException $error) {
        bio_send('error', $error->getMessage());
    }
    if (count($devices) >= BIOMETRIC_MAX_DEVICES) {
        bio_send('error', 'You already have ' . BIOMETRIC_MAX_DEVICES . ' devices. Remove one first.');
    }
    if (bio_query($conn, "SELECT passkey_id FROM tbl_login_passkeys WHERE credential_id = ? LIMIT 1", [$device['credential_id']])) {
        bio_send('error', 'This device is already set up.');
    }
    $name = is_string($_POST['device_name'] ?? null) ? trim(preg_replace('/\s+/', ' ', $_POST['device_name'])) : '';
    $name = $name !== '' ? mb_substr($name, 0, 100) : 'This device';
    $saved = bio_query($conn, "INSERT INTO tbl_login_passkeys (login_id, credential_id, public_key, sign_count, device_name, created_at) VALUES (?, ?, ?, ?, ?, NOW())", [$my_id, $device['credential_id'], $device['public_key'], (int) $device['sign_count'], $name]);
    if (!$saved) {
        bio_send('error', 'The device could not be saved. Please try again.');
    }
    $passkey_id = (int) mysqli_insert_id($conn);
    // The log names the device, never the key.
    $logger->log('INSERT', 'tbl_login_passkeys', $passkey_id, null, ['device_name' => $name], 'Fingerprint sign-in set up: ' . $me['username'] . ' (' . $name . ')');
    bio_send('success', 'Done. You can now sign in on this device without the password.');
}

bio_send('error', 'Not available.');
