<?php 
include "../db/config.php";
$page=$_POST['page_id'];
$name=$_POST['name'];


 $sql=select_query('tbl_community','comm_heading LIKE "%'.$name.'%" and comm_featured=2 and is_active=1 ');
 $count=mysqli_num_rows($sql); 
 $per_page=16;
 $no_of_page=ceil($count/$per_page);
 $start=($page-1)*$per_page;
  $query=mysqli_query($conn,"select * from tbl_community where comm_heading LIKE '%".$name."%' and comm_featured=2 and is_active=1  order by comm_posted_on  desc LIMIT $start,$per_page");

$i=0;
  while($rows=mysqli_fetch_array($query))
   {


               
$data.='<div class="comm-list-item">
                                          
                                            <div class="listing-item">
                                                <div class="geodir-category-listing">
                                                    <div class="geodir-category-img">
                                                        <a href="'.BASE_URL.'community/'.$rows['comm_slug'].'" class="geodir-category-img_item">
                                                         <div class="comm-img-box">
                                                         <img src="'.BASE_URL_IMG.'community/thumb/'.$rows['comm_thumb_img'].'" alt="'.$rows['comm_thumb_img_alt'].'">
                                                         </div>
                                                         <div class="overlay"></div>
                                                        </a>
                                                    </div>
                                                    <div class="geodir-category-content">
                                                        <h3><a href="'.BASE_URL.'community/'.$rows['comm_slug'].'">'.$rows['comm_heading'].'</a></h3>
                                                        <div class="geodir-category-content_price text-muted">'.$rows['comm_location'].'
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                        </div>';

$i++;
}

     $ends_count = 1; 
     $middle_count = 1;
     $dots = true;
     $prev_next=true;
     $paging.='';
     if($prev_next && $page && 1 < $page)
     { 
      $paging.='<a href="javascript:void(0)" data-id='.($page-1).'  class="prevposts-link paging_community"><i class="fa fa-caret-left"></i></a>';
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
            $paging.='<a class="page-numbers paging_community" data-id="'.$i.'" >'.$i.'</a>';
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

      $paging.='<a href="javascript:void(0)" data-id='.($page+1).' class="nextposts-link paging_community"><i class="fa fa-caret-right"></i></a>';
     }

    
     $paging.="</ul>";


$attr=array('record'=>$data,'paging'=>$paging);

echo json_encode($attr);?>
