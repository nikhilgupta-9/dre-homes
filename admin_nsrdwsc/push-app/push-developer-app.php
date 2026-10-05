<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../db/config.php'); // Website DB
include('app-db-conn.php'); // App DB connection

if(isset($_POST['push_to_app'])){

    if(empty($_POST['developer_ids'])){
        die("Please select developer");
    }

    $success_count = 0;
    $failed_count = 0;

    foreach($_POST['developer_ids'] as $id){
        $id = intval($id);

        // Get website developer data
        $query = mysqli_query($conn,"
            SELECT * 
            FROM tbl_developer
            WHERE dev_id = $id
        ");

        if(mysqli_num_rows($query) == 0){
            $failed_count++;
            continue;
        }

        $data = mysqli_fetch_assoc($query);

        // Escape data for app database
        $dev_img = mysqli_real_escape_string(
            $app_conn,
            BASE_URL .'developers/'. $data['dev_img']
        );

        $dev_homp_img = mysqli_real_escape_string(
            $app_conn,
            $data['dev_homp_img']
        );

        $dev_innr_img = mysqli_real_escape_string(
            $app_conn,
            $data['dev_innr_img']
        );

        $choose_us_img = mysqli_real_escape_string(
            $app_conn,
            $data['choose_us_img']
        );

        $dev_heading = mysqli_real_escape_string(
            $app_conn,
            $data['dev_heading']
        );

        $dev_short_descp = mysqli_real_escape_string(
            $app_conn,
            $data['dev_short_descp']
        );

        $dev_long_descp = mysqli_real_escape_string(
            $app_conn,
            $data['dev_long_descp']
        );

        $dev_address = mysqli_real_escape_string(
            $app_conn,
            $data['dev_address']
        );

        $dev_phoneno = mysqli_real_escape_string(
            $app_conn,
            $data['dev_phoneno']
        );

        $dev_email = mysqli_real_escape_string(
            $app_conn,
            $data['dev_email']
        );

        $choose_us_desc = mysqli_real_escape_string(
            $app_conn,
            $data['choose_us_desc']
        );

        $dev_slug = mysqli_real_escape_string(
            $app_conn,
            $data['dev_slug']
        );

        $meta_title = mysqli_real_escape_string(
            $app_conn,
            $data['meta_title']
        );

        $meta_keyword = mysqli_real_escape_string(
            $app_conn,
            $data['meta_keyword']
        );

        $meta_description = mysqli_real_escape_string(
            $app_conn,
            $data['meta_description']
        );

        $meta_tags = mysqli_real_escape_string(
            $app_conn,
            $data['meta_tags']
        );

        $dev_order_no = ($data['dev_order_no'] === null || $data['dev_order_no'] === '')
            ? 'NULL'
            : intval($data['dev_order_no']);

        // Check existing record in app database
        $check = mysqli_query($app_conn,"
            SELECT dev_id 
            FROM tbl_developer 
            WHERE dev_id = '".$data['dev_id']."'
        ");

        if(mysqli_num_rows($check) > 0){
            // Update existing data
            $insert = mysqli_query($app_conn,"
                UPDATE tbl_developer SET
                    dev_img = '$dev_img',
                    dev_img_alt = '".mysqli_real_escape_string($app_conn, $data['dev_img_alt'])."',
                    dev_homp_img = '$dev_homp_img',
                    dev_homp_img_alt = '".mysqli_real_escape_string($app_conn, $data['dev_homp_img_alt'])."',
                    dev_innr_img = '$dev_innr_img',
                    dev_heading = '$dev_heading',
                    dev_short_descp = '$dev_short_descp',
                    dev_long_descp = '$dev_long_descp',
                    dev_address = '$dev_address',
                    dev_phoneno = '$dev_phoneno',
                    dev_email = '$dev_email',
                    choose_us_img = '$choose_us_img',
                    choose_us_desc = '$choose_us_desc',
                    dev_posted_on = '".$data['dev_posted_on']."',
                    dev_slug = '$dev_slug',
                    meta_title = '$meta_title',
                    meta_keyword = '$meta_keyword',
                    meta_description = '$meta_description',
                    meta_tags = '$meta_tags',
                    dev_showonhomepage = '".$data['dev_showonhomepage']."',
                    dev_order_no = $dev_order_no,
                    is_active = '".$data['is_active']."',
                    tstp = NOW()
                WHERE dev_id = '".$data['dev_id']."'
            ");
        } else {
            // Insert new data
            $insert = mysqli_query($app_conn,"
                INSERT INTO tbl_developer
                (
                    dev_id,
                    dev_img,
                    dev_img_alt,
                    dev_homp_img,
                    dev_homp_img_alt,
                    dev_innr_img,
                    dev_heading,
                    dev_short_descp,
                    dev_long_descp,
                    dev_address,
                    dev_phoneno,
                    dev_email,
                    choose_us_img,
                    choose_us_desc,
                    dev_posted_on,
                    dev_slug,
                    meta_title,
                    meta_keyword,
                    meta_description,
                    meta_tags,
                    dev_showonhomepage,
                    dev_order_no,
                    is_active,
                    tstp
                )
                VALUES
                (
                    '".$data['dev_id']."',
                    '$dev_img',
                    '". $data['dev_img_alt']."',
                    '$dev_homp_img',
                    '".mysqli_real_escape_string($app_conn, $data['dev_homp_img_alt'])."',
                    '$dev_innr_img',
                    '$dev_heading',
                    '$dev_short_descp',
                    '$dev_long_descp',
                    '$dev_address',
                    '$dev_phoneno',
                    '$dev_email',
                    '$choose_us_img',
                    '$choose_us_desc',
                    '".$data['dev_posted_on']."',
                    '$dev_slug',
                    '$meta_title',
                    '$meta_keyword',
                    '$meta_description',
                    '$meta_tags',
                    '".$data['dev_showonhomepage']."',
                    $dev_order_no,
                    '".$data['is_active']."',
                    NOW()
                )
            ");
        }

        if($insert){
            // Update website sync status
            mysqli_query($conn,"
                UPDATE tbl_developer
                SET 
                    app_sync_status = 1,
                    app_sync_date = NOW()
                WHERE dev_id = $id
            ");
            $success_count++;
        } else {
            $failed_count++;
            error_log("Sync failed for Developer ID: ".$id." - Error: ".mysqli_error($app_conn));
        }
    }

    // For AJAX response
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        if($success_count > 0 && $failed_count == 0){
            echo json_encode([
                'status' => 'success',
                'message' => "Successfully pushed {$success_count} developer(s) to the app.",
                'data' => ['total' => $success_count]
            ]);
        } elseif($success_count > 0 && $failed_count > 0){
            echo json_encode([
                'status' => 'partial',
                'message' => "Pushed {$success_count} developer(s) but failed for {$failed_count} developer(s).",
                'data' => ['total' => $success_count, 'failed' => $failed_count]
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => "Failed to push developer(s).",
                'data' => ['failed' => $failed_count]
            ]);
        }
        exit;
    }

    // For regular form submission
    if($success_count > 0 && $failed_count == 0){
        header("Location: ".BASE_URL."manage-developer.php?msg=push_success");
    } elseif($success_count > 0 && $failed_count > 0){
        header("Location: ".BASE_URL."manage-developer.php?msg=partial_success");
    } else {
        header("Location: ".BASE_URL."manage-developer.php?msg=push_failed");
    }
    exit;
} else {
    header("Location: ".BASE_URL."manage-developer.php");
    exit;
}
?>