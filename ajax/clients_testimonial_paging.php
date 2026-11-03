<?php 
include "../db/config.php";
$page=$_POST['page_id'];

 $sql=select_query('tbl_testimonial','is_active=1 ');
 $count=mysqli_num_rows($sql); 
 $per_page=6;
 $no_of_page=ceil($count/$per_page);
 $start=($page-1)*$per_page;
 $query=mysqli_query($conn,"select * from tbl_testimonial where is_active=1  order by test_posted_on desc LIMIT $start,$per_page");
 $j=1;
  while($rows=mysqli_fetch_array($query))
   {


              

          $data .= '<div class="col-md-6 my-3">
    <div class="swiper-slide">
        <div class="testi-item">
            <div class="testimonilas-text">
                <div class="testi-header">
                    <h3>' . $rows['test_client_name'] . '</h3>
                </div>
                <div class="testimonilas-text-item text-left">
                    <div class="testimonilas-text-item-wrap">
                        <div class="testimonial-snippet">
                            ' . substr(strip_tags($rows['test_descp']), 0, 300) . '...
                            <a href="javascript:void(0);"
                               class="pop-read-more"
                               data-fulltext="' . htmlspecialchars(strip_tags($rows['test_descp']), ENT_QUOTES) . '">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testi-footer">
                <span class="testi-number">' . date('d M,Y', strtotime($rows['test_posted_on'])) . '</span>
            </div>
        </div>
    </div>
</div>';


$j++;}

 $ends_count = 1; 
     $middle_count = 1;
     $dots = true;
     $prev_next=true;
     $paging.='';
     if($prev_next && $page && 1 < $page)
     { 
      $paging.='<a href="javascript:void(0)" data-id='.($page-1).'  class="prevposts-link paging_clients_testimonial"><i class="fa fa-caret-left"></i></a>';
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
            $paging.='<a class="page-numbers paging_clients_testimonial" data-id="'.$i.'" >'.$i.'</a>';
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

      $paging.='<a href="javascript:void(0)" data-id='.($page+1).' class="nextposts-link paging_clients_testimonial"><i class="fa fa-caret-right"></i></a>';
     }

    
     $paging.="</ul>";


$attr=array('record'=>$data,'paging'=>$paging);

echo json_encode($attr);?>
