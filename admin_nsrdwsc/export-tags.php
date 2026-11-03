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
$fileName = "Tags-" . date('Ymd') . ".xls"; 
 
// Column names 
$fields = array('SR.N.','PAGE SLUG','META TITLE','META KEYWORD','META DESCRIPTION','META TAGS','STATUS','CREATED'); 
 
// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n"; 
 
// Get records from the database 
$query = mysqli_query($conn,"select t.*,p.page_name,p.page_slug from tbl_tags as t LEFT JOIN tbl_pages as p ON t.page_id=p.id order by p.page_name asc"); 
$totl=mysqli_num_rows($query);
if($totl > 0){ 
    // Output each row of the data 
    $i=0; 
    while ($row=mysqli_fetch_assoc($query))
    { 
        $i++;
        $rowData = array($i,$row['page_slug'],$row['meta_title'],$row['meta_keyword'],strip_tags($row['meta_description']),$row['meta_tags'],($row['is_active']==1 ? 'Active' : 'Inactive'),$row['tstp']); 
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