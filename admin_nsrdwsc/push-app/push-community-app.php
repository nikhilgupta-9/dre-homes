<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../db/config.php'); // Website DB
include('app-db-conn.php'); // App DB connection

if(isset($_POST['push_to_app'])){
    
    if(empty($_POST['community_ids'])){
        die("Please select community");
    }
    
    $successCount = 0;
    $failedIds = [];
    
    foreach($_POST['community_ids'] as $id){
        $id = intval($id);
        
        // Get website community data
        $query = mysqli_query($conn, "
            SELECT * 
            FROM tbl_community
            WHERE comm_id = $id
        ");
        
        if(mysqli_num_rows($query) == 0){
            $failedIds[] = $id;
            continue;
        }
        
        $data = mysqli_fetch_assoc($query);
        
        // Check existing record in app database
        $check = mysqli_query(
    $app_conn,
    "SELECT comm_id FROM tbl_community WHERE comm_id = {$id}"
);
        
        // Use prepared statements for INSERT/UPDATE
        if(mysqli_num_rows($check) > 0){
            // UPDATE
            $stmt = mysqli_prepare($app_conn, "
                UPDATE tbl_community SET
                    comm_img = ?,
                    comm_heading = ?,
                    comm_long_descp = ?,
                    comm_slug = ?,
                    comm_showonhomepage = ?,
                    comm_featured = ?,
                    is_active = ?,
                    tstp = ?
                WHERE comm_id = ?
            ");
            
            $comm_img = BASE_URL . "assets/media/community/thumb/" . $data['comm_thumb_img'];
            
            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssi",
                $comm_img,
                $data['comm_heading'],
                $data['comm_long_descp'],
                $data['comm_slug'],
                $data['comm_showonhomepage'],
                $data['comm_featured'],
                $data['is_active'],
                $data['tstp'],
                $data['comm_id']
            );
        } else {
            // INSERT
            $stmt = mysqli_prepare($app_conn, "
                INSERT INTO tbl_community (
                    comm_id,
                    comm_img,
                    comm_img_alt,
                    comm_name,
                    comm_heading,
                    comm_long_descp,
                    comm_posted_on,
                    comm_slug,
                    meta_title,
                    meta_keyword,
                    meta_description,
                    comm_showonhomepage,
                    comm_featured,
                    is_active,
                    tstp
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )
            ");
            
            $comm_img = BASE_URL . "assets/media/community/thumb/" . $data['comm_thumb_img'];
            $comm_name = $data['comm_thumb_img_alt'];
            
            mysqli_stmt_bind_param(
                $stmt,
                "issssssssssssss",
                $data['comm_id'],
                $comm_img,
                $comm_name,
                $comm_name,
                $data['comm_heading'],
                $data['comm_long_descp'],
                $data['comm_posted_on'],
                $data['comm_slug'],
                $data['meta_title'],
                $data['meta_keyword'],
                $data['meta_description'],
                $data['comm_showonhomepage'],
                $data['comm_featured'],
                $data['is_active'],
                $data['tstp']
            );
        }
        
        $insert = mysqli_stmt_execute($stmt);
        
        if($insert){
            $successCount++;
            
            // Update website sync status
            $updateStmt = mysqli_prepare($conn, "
                UPDATE tbl_community
                SET 
                    app_sync_status = 1,
                    app_sync_date = NOW()
                WHERE comm_id = ?
            ");
            mysqli_stmt_bind_param($updateStmt, "i", $id);
            mysqli_stmt_execute($updateStmt);
            mysqli_stmt_close($updateStmt);
        } else {
            $failedIds[] = $id;
            echo "Sync failed for ID: $id<br>";
            echo "Error: " . mysqli_stmt_error($stmt) . "<br>";
        }
        
        mysqli_stmt_close($stmt);
    }
    
    if(empty($failedIds)){
        header("Location: " . BASE_URL . "manage-community.php?msg=push_success");
    } else {
        header("Location: " . BASE_URL . "manage-community.php?msg=partial_success&failed=" . implode(',', $failedIds));
    }
    exit;
}
?>