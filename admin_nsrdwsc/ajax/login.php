<?php
session_start(); // Make sure session is started at the beginning
sleep(1);
include('../db/config.php');
include('../includes/Logger.php');

$logger = new Logger($conn);

$login_email = prepStr($_POST['login_email']);
$login_password = prepStr($_POST['login_password']);

$query = query("tbl_login", "login_email='$login_email' and login_password='" . md5($login_password) . "' and is_active=1");
$tot = total_record($query);

$query2 = query("tbl_login", "login_email='$login_email' and login_password='" . md5($login_password) . "'");
$tot2 = total_record($query2);

$details = query_detail("tbl_login", "login_email='$login_email' and login_password='" . md5($login_password) . "'");

if ($tot == 1) {
    $_SESSION['user_id'] = $details['login_id'];
    $_SESSION['username'] = $details['username'];
    $_SESSION['email_id'] = $details['login_email'];
    $_SESSION['login_type'] = $details['login_type'];
    
    // success log - this will now store session_id and login_time
    $logger->logLogin($login_email, 'success');
    
    $attr = ['status' => 'success', 'msg' => 'Login Successful'];
} else if ($tot2 == 1) {
    // suspended
    $logger->logLogin($login_email, 'failed', 'Account Suspended');
    $attr = ['status' => 'error', 'msg' => 'Account Suspended by Sub Admin'];
} else {
    // wrong credentials
    $logger->logLogin($login_email, 'failed', 'Invalid Email or Password');
    $attr = ['status' => 'error', 'msg' => 'Incorrect Email ID or Password'];
}

echo json_encode($attr);
?>