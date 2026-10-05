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
$fileName = "News-" . date('Ymd') . ".xls"; 
 
// Column names 
$fields = array('SR.N.','THUMB IMAGE','THUMB IMAGE ALT TAG','HEADING','SHORT DESCRIPTION','INNER IMAGE','INNER IMAGE ALT TAG','LONG DESCRIPTION','SLUG','META TITLE','META KEYWORD','META DESCRIPTION','META TAGS','POSTED ON','STATUS' ,'CREATED'); 
 
// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n"; 
 
// Get records from the database 
$query = mysqli_query($conn,"select * from tbl_news order by news_id desc"); 
$totl=mysqli_num_rows($query);
if($totl > 0){ 
    // Output each row of the data 
    $i=0; 
    while ($row=mysqli_fetch_assoc($query))
    { 
        $i++;
        $rowData = array($i,BASE_URL.'assets/media/news/thumb/'.$row['news_thumb_img'],$row['news_thumb_img_alt'],$row['news_heading'],$row['news_short_descp'],BASE_URL.'assets/media/news/inner/'.$row['news_inner_img'],$row['news_inner_img_alt'],strip_tags($row['news_long_descp']),$row['news_slug'],$row['meta_title'],$row['meta_keyword'],$row['meta_description'],$row['meta_tags'],$row['news_posted_on'],($row['is_active']==1 ? 'Active' : 'Deactive'),$row['tstp']); 
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