<?php
include "../db/config.php";

$input = $_POST['input'] ?? '';
$devId = $_POST['devId'] ?? '';

$output = '';

if (isset($input) && isset($devId)) {
    
    // Escape values to prevent SQL injection
    $input = mysqli_real_escape_string($conn, $input);
    $devId = mysqli_real_escape_string($conn, $devId);
    
    $query = mysqli_query(
        $conn,
        "UPDATE `tbl_developer` SET `dev_order_no`='$input' WHERE `dev_id` = '$devId'"
    );
    
    if ($query) {
        if (mysqli_affected_rows($conn) > 0) {
            $output = json_encode(['success' => true, 'message' => 'Developer order number updated successfully']);
        } else {
            $output = json_encode(['success' => false, 'message' => 'No changes made. Developer ID may not exist or value is same']);
        }
    } else {
        $output = json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
    
} else {
    $output = json_encode(['success' => false, 'message' => 'Missing required parameters: input or devId']);
}

echo $output;

// Close connection
mysqli_close($conn);
?>