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

$ids = isset($_POST['ids']) ? $_POST['ids'] : [];

if (empty($ids) || !is_array($ids)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No logs selected for deletion'
    ]);
    exit;
}

// Sanitize IDs
$ids = array_map('intval', $ids);
$ids_list = implode(',', $ids);

// First get the log details for logging purpose
$get_query = "SELECT login_log_id, user_email, login_time FROM tbl_login_logs WHERE login_log_id IN ($ids_list)";
$result = mysqli_query($conn, $get_query);
$deleted_logs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $deleted_logs[] = $row;
}

if (empty($deleted_logs)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No valid logs found to delete'
    ]);
    exit;
}

// Delete the logs
$delete_query = "DELETE FROM tbl_login_logs WHERE login_log_id IN ($ids_list)";

if (mysqli_query($conn, $delete_query)) {
    $affected = mysqli_affected_rows($conn);
    
    if ($affected > 0) {
        // Log the bulk deletion action in activity logs
        $username = mysqli_real_escape_string($conn, $_SESSION['username']);
        $delete_time = date('Y-m-d H:i:s');
        $user_ip = $_SERVER['REMOTE_ADDR'];
        
        // Create summary of deleted logs
        $deleted_summary = [];
        foreach ($deleted_logs as $log) {
            $deleted_summary[] = "ID: {$log['login_log_id']} - User: {$log['user_email']} - Time: {$log['login_time']}";
        }
        $summary = implode("; ", $deleted_summary);
        
        $log_query = "INSERT INTO tbl_activity_logs 
                      (username, user_type, action_type, table_name, action_description, user_ip, log_time) 
                      VALUES (
                          '$username', 
                          'ADMIN', 
                          'DELETE', 
                          'tbl_login_logs', 
                          'Bulk deleted $affected login log(s): $summary', 
                          '$user_ip', 
                          '$delete_time'
                      )";
        mysqli_query($conn, $log_query);
        
        echo json_encode([
            'status' => 'success',
            'message' => "$affected log entries deleted successfully"
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No records found to delete'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to delete logs: ' . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>