<?php 
include "../db/config.php";
$name=$_POST['name'];
$page=1;

 $sql=select_query('tbl_developer','dev_heading LIKE "%'.$name.'%" and is_active=1 ');
 $count=mysqli_num_rows($sql); 
 $per_page=8;
 $no_of_page=ceil($count/$per_page);
 $start=($page-1)*$per_page;
 $query=mysqli_query($conn,"select * from tbl_developer where dev_heading LIKE '%".$name."%' and is_active=1  order by dev_posted_on desc LIMIT $start,$per_page");
 $i=0;

if($count>0)
{
  while($rows=mysqli_fetch_array($query))
   {
    
$data.='<div class="listing-item dev-list">
                                <div class="geodir-category-listing">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="geodir-category-img d-flex justify-content-center align-items-center">
                                                <a href="'.BASE_URL.'developer/'.$rows['dev_slug'].'" class="geodir-category-img_item">
                                                <img src="'.BASE_URL_IMG.'developer/'.$rows['dev_img'].'" alt="'.$rows['dev_img_alt'].'">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="geodir-category-content">
                                                <h3><a href="'.BASE_URL.'developer/'.$rows['dev_slug'].'">'.$rows['dev_heading'].'</a></h3>
                                                <p> '.$rows['dev_short_descp'].'
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>';

$i++;
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
      $paging.='<a href="javascript:void(0)" data-id='.($page-1).'  class="prevposts-link paging_developer"><i class="fa fa-caret-left"></i></a>';
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
            $paging.='<a class="page-numbers paging_developer" data-id="'.$i.'" >'.$i.'</a>';
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

      $paging.='<a href="javascript:void(0)" data-id='.($page+1).' class="nextposts-link paging_developer"><i class="fa fa-caret-right"></i></a>';
     }

    
     $paging.="</ul>";


$attr=array('record'=>$data,'paging'=>$paging,'count'=>$count);

echo json_encode($attr);?>

