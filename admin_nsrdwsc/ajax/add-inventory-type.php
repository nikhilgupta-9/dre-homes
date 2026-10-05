<?php

include('../db/config.php');

if(isset($_POST['inventry_name']))
{
    $inventry_name = mysqli_real_escape_string(
        $conn,
        trim($_POST['inventry_name'])
    );

    // Check duplicate
    $check = mysqli_query(
        $conn,
        "SELECT id FROM tbl_inventry_type
         WHERE inventry_name='$inventry_name'"
    );

    if(mysqli_num_rows($check) > 0)
    {
        echo json_encode([
            'status' => 'error',
            'message' => 'Inventory Type already exists'
        ]);
        exit;
    }

    $insert = mysqli_query(
        $conn,
        "INSERT INTO tbl_inventry_type
        (inventry_name, created_at, updated_at)
        VALUES
        (
            '$inventry_name',
            NOW(),
            NOW()
        )"
    );

    if($insert)
    {
        $id = mysqli_insert_id($conn);

        echo json_encode([
            'status' => 'success',
            'id' => $id,
            'name' => $inventry_name
        ]);
    }
    else
    {
        echo json_encode([
            'status' => 'error',
            'message' => 'Insert failed'
        ]);
    }
}