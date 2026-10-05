<?php
include('../db/config.php');




$img_id=prepStr($_POST['img_id']);
$img_type=prepStr($_POST['img_type']);
$img_name=prepStr($_POST['img_name']);


if($img_type=='location')
{
    $query=mysqli_query($conn,"update tbl_proj set proj_loc_img='' where prj_id=$img_id");
    unlink('../assets/media/project/location/'.$img_name.'');
    
}
else if($img_type=='qr')
{
    $query=mysqli_query($conn,"update tbl_proj set proj_qr_img='' where prj_id=$img_id");
    unlink('../assets/media/project/qr/'.$img_name.'');
    
}
else
{
   $query=mysqli_query($conn,"update tbl_proj set proj_mastrpln_img='' where prj_id=$img_id");   
   unlink('../assets/media/project/masterplan/'.$img_name.'');
}

$attr=array('status'=>'success');

echo json_encode($attr);
