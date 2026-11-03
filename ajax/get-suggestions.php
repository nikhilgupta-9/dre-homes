<?php

include "../db/config.php";

$type = $_POST['type'];
$keyword = $_POST['keyword'] ?? "";
$filter1 = $_POST['filter1'];
$filter2 = $_POST['filter2'];

$output = '';


if ($type == 'propertyType') {

    $query = mysqli_query(
        $conn,
        "SELECT DISTINCT proj_inventory_type
         FROM tbl_proj
         WHERE prj_type_id = $keyword
         AND proj_inventory_type IS NOT NULL
         AND proj_inventory_type != ''
         ORDER BY proj_inventory_type ASC"
    );

    $output = '<option value="all">All Types</option>';

    while ($row = mysqli_fetch_assoc($query)) {

        $inventory_type = trim($row['proj_inventory_type']);

        $output .= '<option value="' . htmlspecialchars($inventory_type) . '">'
            . htmlspecialchars(ucwords($inventory_type))
            . '</option>';
    }
}

if ($type == 'location') {

    if ($keyword != '') {

        $query = mysqli_query($conn, "
                    SELECT DISTINCT proj_location
                    FROM tbl_proj
                    WHERE prj_type_id='$filter1'
                    AND proj_location IS NOT NULL
                    AND TRIM(proj_location) != ''
                    AND proj_location LIKE '%$keyword%'
                    ORDER BY proj_location
                    LIMIT 10
                ");

    } else {

        $query = mysqli_query($conn, "
                    SELECT DISTINCT proj_location
                    FROM tbl_proj
                    WHERE prj_type_id='$filter1'
                    AND proj_location IS NOT NULL
                    AND TRIM(proj_location) != ''
                    ORDER BY proj_location
                    LIMIT 15
                ");

    }

    while ($row = mysqli_fetch_assoc($query)) {

        $output .= '
            <div class="suggestion-item">
                ' . $row['proj_location'] . '
            </div>
        ';
    }

}

if ($type == 'property') {

    if ($keyword != '') {

        $query = mysqli_query($conn, "
            SELECT DISTINCT proj_name
            FROM tbl_proj
            WHERE prj_type_id='$filter1'
            AND proj_name LIKE '%$keyword%'
            AND `proj_location` = '$filter2'
            ORDER BY proj_name
            LIMIT 10
        ");

    } else {

        $query = mysqli_query($conn, "
            SELECT DISTINCT proj_name
            FROM tbl_proj
            WHERE prj_type_id='$filter1' AND `proj_location` = '$filter2'
            ORDER BY proj_name
            LIMIT 10
        ");

    }

    while ($row = mysqli_fetch_assoc($query)) {

        $output .= '
            <div class="suggestion-item">
                ' . $row['proj_name'] . '
            </div>
        ';
    }
}

echo $output;

?>