<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

include('../db/config.php');
session_start();

// Check admin authentication
if (!isset($_SESSION['username']) || empty($_SESSION['username']) || $_SESSION['login_type'] != 1) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

$log_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($log_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid log ID'
    ]);
    exit;
}

// First get the log details for logging purpose
$get_query = "SELECT user_email, login_status, login_time FROM tbl_login_logs WHERE login_log_id = $log_id";
$result = mysqli_query($conn, $get_query);
$log_data = mysqli_fetch_assoc($result);

if (!$log_data) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Log not found'
    ]);
    exit;
}

// Delete the log
$delete_query = "DELETE FROM tbl_login_logs WHERE login_log_id = $log_id";

if (mysqli_query($conn, $delete_query)) {
    $affected = mysqli_affected_rows($conn);
    
    if ($affected > 0) {
        // Log the deletion action in activity logs
        $username = mysqli_real_escape_string($conn, $_SESSION['username']);
        $user_email = mysqli_real_escape_string($conn, $log_data['user_email']);
        $log_time = $log_data['login_time'];
        $delete_time = date('Y-m-d H:i:s');
        $user_ip = $_SERVER['REMOTE_ADDR'];
        
        $log_query = "INSERT INTO tbl_activity_logs 
                      (username, user_type, action_type, table_name, record_id, action_description, user_ip, log_time) 
                      VALUES (
                          '$username', 
                          'ADMIN', 
                          'DELETE', 
                          'tbl_login_logs', 
                          $log_id, 
                          'Deleted login log for user: $user_email at login time: $log_time', 
                          '$user_ip', 
                          '$delete_time'
                      )";
        mysqli_query($conn, $log_query);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Log entry deleted successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No record found to delete'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to delete: ' . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>