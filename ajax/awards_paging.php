<?php 
include "../db/config.php";
$page=$_POST['page_id'];

 $sql=select_query('tbl_awards','is_active=1 ');
 $count=mysqli_num_rows($sql); 
 $per_page=6;
 $no_of_page=ceil($count/$per_page);
 $start=($page-1)*$per_page;
 $query=mysqli_query($conn,"select * from tbl_awards where is_active=1  order by award_id desc LIMIT $start,$per_page");
$i=0;
  while($rows=mysqli_fetch_array($query))
   {


               
$data.='<div class="listing-item award-list">
                                        <div class="geodir-category-listing">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="geodir-category-img">
                                                            <div class="">
                                                                <img src="'.BASE_URL_IMG.'awards/'.$rows['award_img'].'" alt="'.$rows['award_img_alt'].'">
                                                            </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="geodir-category-content">
                                                        <h3>'.$rows['award_heading'].'</h3>
                                                        '.$rows['award_descp'].'
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';

$i++;
}

     $ends_count = 1; 
     $middle_count = 1;
     $dots = true;
     $prev_next=true;
     $paging.='';
     if($prev_next && $page && 1 < $page)
     { 
      $paging.='<a href="javascript:void(0)" data-id='.($page-1).'  class="prevposts-link paging_awards"><i class="fa fa-caret-left"></i></a>';
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
            $paging.='<a class="page-numbers paging_awards" data-id="'.$i.'" >'.$i.'</a>';
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

      $paging.='<a href="javascript:void(0)" data-id='.($page+1).' class="nextposts-link paging_awards"><i class="fa fa-caret-right"></i></a>';
     }

    
     $paging.="</ul>";


$attr=array('record'=>$data,'paging'=>$paging);

echo json_encode($attr);?>
