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
$fileName = "Project-" . date('Ymd') . ".xls"; 
 
// Column names 
$fields = array('SR.N.','PROJECT NAME','TYPE','DEVELOPER','PRICE','DAYS','LOCATION','BEDROOM','EMAIL ID','WHATSAPP NUMBER','MOBILE NUMBER','STATUS','CREATED'); 
 
// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n"; 
 
// Get records from the database 
$query = mysqli_query($conn,"select tp.*,td.dev_heading as proj_dev_name,ty.proj_type_name as proj_type from tbl_proj as tp 
            LEFT JOIN tbl_developer as td ON tp.prj_dev_id=td.dev_id
            LEFT JOIN tbl_proj_type as ty ON tp.prj_type_id=ty.prj_type_id order by tp.prj_id desc"); 
$totl=mysqli_num_rows($query);
if($totl > 0){ 
    // Output each row of the data 
    $i=0; 
    while ($row=mysqli_fetch_assoc($query))
    { 
        $i++;
        $rowData = array($i, $row['proj_name'],$row['proj_type'],$row['proj_dev_name'],$row['proj_price'],$row['proj_days'],$row['proj_location'],$row['proj_bedroom'],$row['proj_email'],$row['proj_whatsaapno'],$row['proj_mobileno'],($row['is_active']==1 ? 'Active' : 'Deactive'),$row['tstp']); 
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