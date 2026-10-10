<?php
error_reporting(0);
header('Content-Type: application/json');
include('../db/config.php');
session_start();

if (!isset($_SESSION['username']) || $_SESSION['login_type'] != 1) {
    echo json_encode(['error - get-log-details.php:8' => 'Unauthorized']);
    exit;
}

$log_id = isset($_POST['log_id']) ? intval($_POST['log_id']) : 0;

if ($log_id <= 0) {
    echo json_encode(['error - get-log-details.php:15' => 'Invalid log ID']);
    exit;
}

$query = "SELECT * FROM tbl_activity_logs WHERE log_id = $log_id";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode($row);
} else {
    echo json_encode(['error - get-log-details.php:25' => 'Log not found']);
}
?>