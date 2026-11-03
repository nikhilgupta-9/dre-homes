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
$fileName = "About-content-" . date('Ymd') . ".xls"; 
 
// Column names 
$fields = array('SR.N.','TITLE','SUB TITLE','DESCRIPTION','STATUS' ,'CREATED'); 
 
// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n"; 
 
// Get records from the database 
$query = mysqli_query($conn,"select * from tbl_abt_content order by abt_content_id desc"); 
$totl=mysqli_num_rows($query);
if($totl > 0){ 
    // Output each row of the data 
    $i=0; 
    while ($row=mysqli_fetch_assoc($query))
    { 
        $i++;
        $rowData = array($i,$row['abt_content_title'],$row['abt_content_subtitle'],strip_tags($row['abt_content_descp']),($row['is_active']==1 ? 'Active' : 'Deactive'),$row['tstp']); 
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