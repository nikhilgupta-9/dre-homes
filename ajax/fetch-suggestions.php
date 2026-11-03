<?php 
include "../db/config.php";
$prj_type_id = $_POST['prj_type_id'];


$query = "SELECT proj_name FROM tbl_proj WHERE is_active = 1 AND prj_type_id = $prj_type_id ORDER BY proj_name";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li>" . htmlspecialchars($row['proj_name']) . "</li>";
    }
} else {
    echo "<li>No suggestions found</li>";
}