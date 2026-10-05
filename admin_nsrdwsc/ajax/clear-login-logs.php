<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
include('../db/config.php');
session_start();

// Check admin authentication
if (!isset($_SESSION['username']) || empty($_SESSION['username']) || $_SESSION['login_type'] != 1) {
    echo json_encode(['status - clear-login-logs.php:10' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status - clear-login-logs.php:15' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$days = isset($_POST['days']) ? $_POST['days'] : 'all';

if ($days === 'all') {
    $query = "DELETE FROM tbl_login_logs";
    $message = 'All login logs have been cleared successfully';
} else {
    $days = intval($days);
    $query = "DELETE FROM tbl_login_logs WHERE login_time < DATE_SUB(NOW(), INTERVAL $days DAY)";
    $message = "Login logs older than $days days have been cleared successfully";
}

if (mysqli_query($conn, $query)) {
    $affected = mysqli_affected_rows($conn);
    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'deleted_count' => $affected
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to clear logs: ' . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>