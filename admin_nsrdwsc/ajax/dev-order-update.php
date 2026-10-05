<?php

include "../db/config.php";

header('Content-Type: application/json');

$input = trim($_POST['input'] ?? '');
$devId = trim($_POST['devId'] ?? '');

if (empty($devId)) {
    echo json_encode([
        'success' => false,
        'message' => 'Developer ID is required'
    ]);
    exit;
}

$devId = (int)$devId;

if ($input === '') {

    // Save NULL when input is blank
    $sql = "UPDATE tbl_developer
            SET dev_order_no = NULL
            WHERE dev_id = $devId";

} else {

    // Allow only numeric values
    $input = (int)$input;

    $sql = "UPDATE tbl_developer
            SET dev_order_no = $input
            WHERE dev_id = $devId";
}

$query = mysqli_query($conn, $sql);

if ($query) {

    echo json_encode([
        'success' => true,
        'message' => 'Developer order number updated successfully'
    ]);

} else {

    echo json_encode([
        'success' => false,
        'message' => mysqli_error($conn)
    ]);
}

mysqli_close($conn);

?>