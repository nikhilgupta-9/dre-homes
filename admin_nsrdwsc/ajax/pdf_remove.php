<?php
include('../db/config.php');

$pdf_id=prepStr($_POST['pdf_id']);
$pdf_type=prepStr($_POST['pdf_type']);
$pdf_name=prepStr($_POST['pdf_name']);


if($pdf_type=='brochure')
{
    $query=mysqli_query($conn,"update tbl_proj set proj_brochure='' where prj_id=$pdf_id");
    unlink('../assets/media/project/pdf/'.$pdf_name.'');
    
}
else if($pdf_type=='payplan')
{
    $query=mysqli_query($conn,"update tbl_proj set proj_payplan='' where prj_id=$pdf_id");
    unlink('../assets/media/project/pdf/'.$pdf_name.'');
}
else
{
   $query=mysqli_query($conn,"update tbl_proj set proj_floorplan='' where prj_id=$pdf_id");   
   unlink('../assets/media/project/pdf/'.$pdf_name.'');
}

$attr=array('status'=>'success');

echo json_encode($attr);
