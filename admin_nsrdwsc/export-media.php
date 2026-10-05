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
$fileName = "Developer-" . date('Ymd') . ".xls"; 
 
// Column names 
$fields = array('SR.N.','IMAGE','IMAGE ALT TAG','HEADING','SHORT DESCRIPTION','LONG DESCRIPTION','ADDRESS','PHONE NO','EMAIL ID','POSTED ON','META TITLE','META KEYWORD','META DESCRIPTION','META TAGS','STATUS' ,'CREATED'); 
 
// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n"; 
 
// Get records from the database 
$query = mysqli_query($conn,"select * from tbl_developer order by dev_id desc"); 
$totl=mysqli_num_rows($query);
if($totl > 0){ 
    // Output each row of the data 
    $i=0; 
    while ($row=mysqli_fetch_assoc($query))
    { 
        $i++;
        $rowData = array($i,BASE_URL.'assets/media/developer/'.$row['dev_img'],$row['dev_img_alt'],$row['dev_heading'],$row['dev_short_descp'],strip_tags($row['dev_long_descp']),$row['dev_address'],$row['dev_phoneno'],$row['dev_email'],$row['dev_posted_on'],$row['meta_title'],$row['meta_keyword'],$row['meta_description'],$row['meta_tags'],($row['is_active']==1 ? 'Active' : 'Deactive'),$row['tstp']); 
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