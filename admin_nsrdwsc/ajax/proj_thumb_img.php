<?php

include('../db/config.php');

$del_id=$_POST['del_id'];
$del_name=$_POST['del_name'];

$prj_details=mysqli_fetch_array(mysqli_query($conn,"select * from tbl_proj where prj_id='$del_id'"));

$prj_thumb_img_arr=explode(',',$prj_details['prj_thumb_img']);

if (($key = array_search("$del_name", $prj_thumb_img_arr)) !== false) {
unlink("../assets/media/project/thumb/$prj_thumb_img_arr[$key]");
unset($prj_thumb_img_arr[$key]);
}


$prj_thumb_img_str=implode(',', $prj_thumb_img_arr);


/*update record in tbl_proj starts*/


$query=mysqli_query($conn,"update tbl_proj set prj_thumb_img='$prj_thumb_img_str' where prj_id='$del_id'");
if($query)
{
   $attr=array('status'=>'success');
}
else
{
	$attr=array('status'=>'error');
}
/*update record in tbl_proj ends*/

echo json_encode($attr);

