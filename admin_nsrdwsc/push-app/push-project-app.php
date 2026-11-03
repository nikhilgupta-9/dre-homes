<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../db/config.php'); // Website DB
include('app-db-conn.php'); // App DB connection

if(isset($_POST['push_to_app'])){

    if(empty($_POST['project_ids'])){
        die("Please select project");
    }

    $successCount = 0;
    $failedIds = [];

    // Columns present with matching names on both the website and app tbl_proj tables.
    $columns = [
        'prj_dev_id', 'prj_comm_id', 'proj_name', 'proj_sub_heading', 'prj_thumb_img',
        'proj_price', 'proj_pplan_cont', 'proj_handover', 'proj_days', 'proj_location', 'proj_location_long', 'proj_location_latit', 'proj_loc_img',
        'proj_bedroom', 'proj_shower', 'proj_area', 'proj_email', 'proj_whatsaapno',
        'proj_mobileno', 'proj_inner_img', 'proj_dev_img', 'proj_dev_img_alt', 'proj_size',
        'proj_inventory_type', 'proj_start_price', 'proj_overview', 'proj_brochure', 'proj_payplan',
        'proj_floorplan', 'proj_fea_fac_cont', 'proj_fea_fac_opt', 'proj_loc_cont', 'proj_loc_img_alt_tag',
        'proj_video', 'proj_masterplan', 'proj_mastrpln_img', 'proj_mastrpln_img_alt_tag', 'proj_qr',
        'proj_qr_link', 'proj_slug', 'proj_map', 'meta_title',
        'meta_keyword', 'meta_description', 'meta_tags', 'proj_comm_feat', 'proj_showonhomepage',
        'proj_markfeat', 'proj_posted_on', 'is_active', 'tstp', 'proj_price_type',
        'proj_show_down_payment', 'proj_show_payment_plan', 'proj_show_handover', 'proj_show_area',
        'proj_show_bedroom', 'proj_show_shower', 'proj_show_size', 'proj_show_balcony',
        'proj_feature_icon_1', 'proj_feature_text_1', 'proj_feature_icon_2', 'proj_feature_text_2',
        'proj_feature_icon_3', 'proj_feature_text_3', 'proj_feature_icon_4', 'proj_feature_text_4',
        'proj_feature_icon_5', 'proj_feature_text_5',
        'prop_user_id', 'doc_id', 'homepage_status', 'balcony', 'suite_area', 'common_area',
        'balcony_area', 'property_type', 'floor_number', 'property_number', 'property_status',
        'emirate', 'loan_interest', 'emi_15_year', 'emi_20_year', 'emi_25_year', 'property_id',
        'proj_status'
    ];

    foreach($_POST['project_ids'] as $id){
        $id = intval($id);

        // Get website project data (prj_type_id 1,2,3,4 = Off Plan / Ready to Move In / International / Commercial, as defense-in-depth)
        $query = mysqli_query($conn, "
            SELECT *
            FROM tbl_proj
            WHERE prj_id = $id AND prj_type_id IN (1, 2, 3, 4)
        ");

        if(mysqli_num_rows($query) == 0){
            $failedIds[] = $id;
            continue;
        }

        $data = mysqli_fetch_assoc($query);
        $prj_type_id = intval($data['prj_type_id']);

        // prj_thumb_img stores a comma-separated list of filenames; only the first is used.
        $thumbImages = array_values(array_filter(array_map('trim', explode(',', $data['prj_thumb_img']))));
        $data['prj_thumb_img'] = $thumbImages[0] ?? '';

        // Push images/documents as full URLs instead of bare filenames
        $imagePaths = [
            'prj_thumb_img'     => 'assets/media/project/thumb/',
            'proj_inner_img'    => 'assets/media/project/inner-image/',
            'proj_dev_img'      => 'assets/media/project/dev/',
            'proj_loc_img'      => 'assets/media/project/location/',
            'proj_mastrpln_img' => 'assets/media/project/masterplan/',
            'proj_brochure'     => 'assets/media/project/pdf/',
            'proj_payplan'      => 'assets/media/project/pdf/',
            'proj_floorplan'    => 'assets/media/project/pdf/',
        ];

        foreach($imagePaths as $col => $path){
            if(!empty($data[$col])){
                $data[$col] = BASE_URL . $path . $data[$col];
            }
        }

        // NOT NULL on the app DB but nullable on the website DB
        $prj_dev_id = ($data['prj_dev_id'] === null) ? 0 : intval($data['prj_dev_id']);
        $prj_comm_id = ($data['prj_comm_id'] === null) ? 0 : intval($data['prj_comm_id']);
        $data['prj_dev_id'] = $prj_dev_id;
        $data['prj_comm_id'] = $prj_comm_id;
        $data['proj_location_long'] = $data['longitude'];
        $data['proj_location_latit'] = $data['latitude'];

        // Check existing record in app database
        $check = mysqli_query($app_conn, "SELECT prj_id FROM tbl_proj WHERE prj_id = {$id}");

        $values = [];
        foreach($columns as $col){
            $values[] = $data[$col];
        }
        $types = 'ii' . str_repeat('s', count($columns) - 2);

        if(mysqli_num_rows($check) > 0){
            // UPDATE
            $setClause = implode(', ', array_map(fn($col) => "$col = ?", $columns));
            $stmt = mysqli_prepare($app_conn, "
                UPDATE tbl_proj SET $setClause
                WHERE prj_id = ?
            ");

            $updateValues = $values;
            $updateValues[] = $id;
            $updateTypes = $types . 'i';

            mysqli_stmt_bind_param($stmt, $updateTypes, ...$updateValues);
        } else {
            // INSERT
            $insertColumns = array_merge(['prj_id', 'prj_type_id'], $columns);
            $placeholders = implode(', ', array_fill(0, count($insertColumns), '?'));
            $stmt = mysqli_prepare($app_conn, "
                INSERT INTO tbl_proj (" . implode(', ', $insertColumns) . ")
                VALUES ($placeholders)
            ");

            $insertValues = array_merge([$id, $prj_type_id], $values);
            $insertTypes = 'ii' . $types;

            mysqli_stmt_bind_param($stmt, $insertTypes, ...$insertValues);
        }

        $insert = mysqli_stmt_execute($stmt);

        if($insert){
            $successCount++;

            // Replace the app-side gallery with the website's project gallery (tbl_proj_gallery)
            mysqli_query($app_conn, "DELETE FROM tbl_proj_gallery WHERE proj_id = {$id}");

            $galleryQuery = mysqli_query($conn, "
                SELECT gallery_img, gallery_img_alt, is_active
                FROM tbl_proj_gallery
                WHERE proj_id = {$id}
            ");

            if(mysqli_num_rows($galleryQuery) > 0){
                $galleryStmt = mysqli_prepare($app_conn, "
                    INSERT INTO tbl_proj_gallery (proj_id, gallery_img, gallery_img_alt, is_active, tstp)
                    VALUES (?, ?, ?, ?, NOW())
                ");

                while($galleryRow = mysqli_fetch_assoc($galleryQuery)){
                    $galleryImg = BASE_URL . 'assets/media/project/gallery/' . $galleryRow['gallery_img'];
                    $galleryImgAlt = $galleryRow['gallery_img_alt'];
                    $galleryIsActive = $galleryRow['is_active'];
                    mysqli_stmt_bind_param($galleryStmt, "isss", $id, $galleryImg, $galleryImgAlt, $galleryIsActive);
                    mysqli_stmt_execute($galleryStmt);
                }

                mysqli_stmt_close($galleryStmt);
            }

            // Update website sync status
            $updateStmt = mysqli_prepare($conn, "
                UPDATE tbl_proj
                SET
                    app_sync_status = 1,
                    app_sync_date = NOW()
                WHERE prj_id = ?
            ");
            mysqli_stmt_bind_param($updateStmt, "i", $id);
            mysqli_stmt_execute($updateStmt);
            mysqli_stmt_close($updateStmt);
        } else {
            $failedIds[] = $id;
            error_log("Sync failed for Project ID: ".$id." - Error: ".mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);
    }

    if(empty($failedIds)){
        header("Location: " . BASE_URL . "manage-project.php?msg=push_success");
    } else {
        header("Location: " . BASE_URL . "manage-project.php?msg=partial_success&failed=" . implode(',', $failedIds));
    }
    exit;
} else {
    header("Location: " . BASE_URL . "manage-project.php");
    exit;
}
?>