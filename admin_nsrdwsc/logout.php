<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
include('db/config.php');
include('includes/Logger.php');

$logger = new Logger($conn);

// Log the logout
$email = $_SESSION['email_id'] ?? null;
$result = $logger->logLogout($email);

// Clear all session variables
session_destroy();

if ($result) {
    header("Location: ".BASE_URL."?msg=logged_out");
} else {
    header("Location: ".BASE_URL."msg=error");
}
exit();
?>