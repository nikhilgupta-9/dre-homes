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
$fileName = "Blog-" . date('Ymd') . ".xls"; 
 
// Column names 
$fields = array('SR.N.','THUMB IMAGE','THUMB IMAGE ALT TAG','HEADING','SHORT DESCRIPTION','INNER IMAGE','INNER IMAGE ALT TAG','LONG DESCRIPTION','SLUG','META TITLE','META KEYWORD','META DESCRIPTION','META TAGS','POSTED ON','STATUS' ,'CREATED'); 
 
// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n"; 
 
// Get records from the database 
$query = mysqli_query($conn,"select * from tbl_blog order by blog_id desc"); 
$totl=mysqli_num_rows($query);
if($totl > 0){ 
    // Output each row of the data 
    $i=0; 
    while ($row=mysqli_fetch_assoc($query))
    { 
        $i++;
        $rowData = array($i,BASE_URL.'assets/media/blog/thumb/'.$row['blog_thumb_img'],$row['blog_thumb_img_alt'],$row['blog_heading'],$row['blog_short_descp'],BASE_URL.'assets/media/blog/inner/'.$row['blog_inner_img'],$row['blog_inner_img_alt'],strip_tags($row['blog_long_descp']),$row['blog_slug'],$row['meta_title'],$row['meta_keyword'],$row['meta_description'],$row['meta_tags'],$row['blog_posted_on'],($row['is_active']==1 ? 'Active' : 'Deactive'),$row['tstp']); 
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