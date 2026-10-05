<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

include('../db/config.php');
session_start();

if (!isset($_SESSION['username']) || empty($_SESSION['username']) || $_SESSION['login_type'] != 1) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$log_id = isset($_POST['log_id']) ? intval($_POST['log_id']) : 0;

if ($log_id <= 0) {
    echo json_encode(['error' => 'Invalid log ID']);
    exit;
}

$query = "SELECT * FROM tbl_activity_logs WHERE log_id = $log_id";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Log not found']);
}

mysqli_close($conn);
?>