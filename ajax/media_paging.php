<?php 
include "../db/config.php";
$page=$_POST['page_id'];
$name=$_POST['name'];

 $sql=select_query('tbl_media','is_active=1 ');
 $count=mysqli_num_rows($sql); 
 $per_page=6;
 $no_of_page=ceil($count/$per_page);
 $start=($page-1)*$per_page;
 $query=mysqli_query($conn,"select * from tbl_media where  is_active=1  order by media_id  desc LIMIT $start,$per_page");
if($count>0)
{
  while($rows=mysqli_fetch_array($query))
   {


              $data.='<div class="gallery-item">
              <div class="grid-item-holder">

           
           <a href="'.BASE_URL_IMG.'media-image/'.$rows['media_img'].'" class=" popup-image"><img src="'.BASE_URL_IMG.'media-image/'.$rows['media_img'].'" alt="'.$rows['media_img_alt_tag'].'"></a>
          </div>
          </div> ';


}


}else{

  $data.='<h2>No record found</h2>';
}

     $ends_count = 1; 
     $middle_count = 1;
     $dots = true;
     $prev_next=true;
     $paging.='';
     if($prev_next && $page && 1 < $page)
     { 
      $paging.='<a href="javascript:void(0)" data-id='.($page-1).'  class="prevposts-link paging_media"><i class="fa fa-caret-left"></i></a>';
      }


     for ($i = 1; $i <= $no_of_page; $i++) 
     {
      if ($i == $page) 
      {
        $paging.='<a class="page-numbers current current-page">'.$i.'</a>';
        $dots = true;
      } 
      else
      {
      if ($i <= $ends_count || ($page && $i >= $page - $middle_count && 
        $i <= $page + $middle_count) || $i > $no_of_page - $ends_count)
          {         
            $paging.='<a class="page-numbers paging_media" data-id="'.$i.'" >'.$i.'</a>';
                      $dots = true;

           } 
           elseif ($dots) 
           {
          $paging.='<a class="page-numbers">&hellip;</a>';
          $dots = false;
          }
      }
     }


     if ($prev_next && $page && ($page < $no_of_page || -1 == $no_of_page))
      { 

      $paging.='<a href="javascript:void(0)" data-id='.($page+1).' class="nextposts-link paging_media"><i class="fa fa-caret-right"></i></a>';
     }

    
     $paging.="</ul>";


$attr=array('record'=>$data,'paging'=>$paging);

echo json_encode($attr);?>

