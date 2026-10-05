<?php
session_start();
include('../db/config.php');
include('../includes/Logger.php');

// Set JSON response header
header('Content-Type: application/json');

// Initialize response array
$response = array(
    'success' => false,
    'message' => ''
);

// Check if required parameters are sent
if (isset($_POST['image_type']) && isset($_POST['header_id']) && isset($_POST['id'])) {
    
    $image_type = $_POST['image_type'];
    $header_id = intval($_POST['header_id']);
    $id = intval($_POST['id']);
    
    // Validate image type
    $allowed_types = array('greate_place_logo', 'header_logo', 'favicon_logo');
    if (!in_array($image_type, $allowed_types)) {
        $response['message'] = 'Invalid image type specified.';
        echo json_encode($response);
        exit;
    }
    
    // Get current image details
    $query = mysqli_query($conn, "SELECT $image_type FROM tbl_header_info WHERE header_info_id = $header_id");
    if (!$query || mysqli_num_rows($query) == 0) {
        $response['message'] = 'Record not found.';
        echo json_encode($response);
        exit;
    }
    
    $row = mysqli_fetch_assoc($query);
    $current_image = $row[$image_type];
    
    // Check if image exists
    if (empty($current_image)) {
        $response['message'] = 'No image to remove.';
        echo json_encode($response);
        exit;
    }
    
    // Begin transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Update database - set image to empty
        $update_query = mysqli_query($conn, "UPDATE tbl_header_info SET $image_type = '' WHERE header_info_id = $header_id");
        
        if ($update_query) {
            // Delete the physical file if it exists
            $file_path = '../assets/media/header/' . $current_image;
            if (file_exists($file_path)) {
                if (unlink($file_path)) {
                    $response['message'] = 'Image file deleted successfully.';
                } else {
                    // File deletion failed but database is updated
                    $response['message'] = 'Image removed from database but file could not be deleted.';
                }
            } else {
                $response['message'] = 'Image removed successfully (file not found on server).';
            }
            
            // Log the action
            $logger = new Logger($conn);
            $logger->log(
                'UPDATE',
                'tbl_header_info',
                $header_id,
                array($image_type => $current_image),
                array($image_type => ''),
                "Image '$image_type' removed by AJAX"
            );
            
            // Commit transaction
            mysqli_commit($conn);
            
            $response['success'] = true;
            $response['message'] = 'Image removed successfully!';
        } else {
            throw new Exception('Database update failed: ' . mysqli_error($conn));
        }
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
} else {
    $response['message'] = 'Missing required parameters.';
}

// Return JSON response
echo json_encode($response);
exit;
?>