<?php

include "db/config.php";
include "lock2.php";

// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 
 
// Excel file name for download 
$fileName = "Community-" . date('Ymd') . ".xls"; 
 
// Column names 
$fields = array('SR.N.','THUMB IMAGE','THUMB IMAGE ALT TAG','HEADING','SHORT DESCRIPTION','INNER IMAGE','INNER THUMB IMAGE','INNER THUMB IMAGE ALT TAG','LONG DESCRIPTION','ADDRESS','PHONE NO','EMAIL ID','POSTED ON','META TITLE','META KEYWORD','META DESCRIPTION','META TAGS','STATUS' ,'CREATED'); 
 
// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n"; 
 
// Get records from the database 
$query = mysqli_query($conn,"select * from tbl_community order by comm_id desc"); 
$totl=mysqli_num_rows($query);
if($totl > 0){ 
    // Output each row of the data 
    $i=0; 
    while ($row=mysqli_fetch_assoc($query))
    { 
        $i++;
        $rowData = array($i,BASE_URL.'assets/media/community/thumb/'.$row['comm_thumb_img'],$row['comm_thumb_img_alt'],$row['comm_heading'],$row['comm_short_descp'],BASE_URL.'assets/media/community/inner/'.$row['comm_inner_img'],BASE_URL.'assets/media/community/inner-thumb/'.$row['comm_inner_thumb_img'],$row['comm_inner_thumb_img_alt'],strip_tags($row['comm_long_descp']),$row['comm_address'],$row['comm_phoneno'],$row['comm_email'],$row['comm_posted_on'],$row['meta_title'],$row['meta_keyword'],$row['meta_description'],$row['meta_tags'],($row['is_active']==1 ? 'Active' : 'Deactive'),$row['tstp']); 
        array_walk($rowData, 'filterData'); 
        $excelData .= implode("\t", array_values($rowData)) . "\n"; 
    } 
}else{ 
    $excelData .= 'No records found...'. "\n"; 
     
} 
 
// Headers for download 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
header("Content-Type: application/vnd.ms-excel"); 
 
// Render excel data 
echo $excelData; 
 
exit;

?>