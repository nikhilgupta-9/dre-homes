<?php 
include "../db/config.php";

$page = $_POST['page_id'];
$name = $_POST['name'];

$sql = select_query('tbl_blog','is_active=1 ');
$count = mysqli_num_rows($sql); 
$per_page = 6;
$no_of_page = ceil($count / $per_page);
$start = ($page - 1) * $per_page;

// The query you are running
if($page==3)
{
$query_sql = "SELECT * FROM tbl_blog WHERE is_active=1 ORDER BY blog_posted_on DESC LIMIT 5,$per_page";
    
}
else
{
    $query_sql = "SELECT * FROM tbl_blog WHERE is_active=1 ORDER BY blog_posted_on DESC LIMIT $start,$per_page";

}
$query = mysqli_query($conn, $query_sql);

$data = '';

if($count > 0) {
    while($rows = mysqli_fetch_array($query)) {
        $data .= '
        <div class="post-item">
            <div class="post-item_wrap">
                <div class="post-item_media">
                    <a href="'.BASE_URL.'our-blogs/'.$rows['blog_slug'].'">
                        <img src="'.BASE_URL_IMG.'blog/thumb/'.$rows['blog_thumb_img'].'" alt="'.$rows['blog_thumb_img_alt'].'">
                    </a>
                </div>
                <div class="post-item_content">
                    <div class="post-card-details">
                        <ul>
                            <li><i class="fa-light fa-calendar-days"></i><span>'.date('M d,Y',strtotime($rows['blog_posted_on'])).'</span></li>
                        </ul>
                    </div>
                    <h3><a href="'.BASE_URL.'our-blogs/'.$rows['blog_slug'].'">'.truncateHeading($rows['blog_heading']).'</a></h3>
                    <p>'.truncateParagraph($rows['blog_short_descp']).'&hellip;</p>
                    <a href="'.BASE_URL.'our-blogs/'.$rows['blog_slug'].'" class="post-card_link">View Details <i class="fa-solid fa-caret-right"></i></a>
                </div>
            </div>
        </div>';
    }
} else {
    $data .= '<h2>No record found</h2>';
}

$ends_count = 1; 
$middle_count = 1;
$dots = true;
$prev_next = true;
$paging = '';

if($prev_next && $page && 1 < $page) { 
    $paging .= '<a href="javascript:void(0)" data-id='.($page-1).'  class="prevposts-link paging_blog"><i class="fa fa-caret-left"></i></a>';
}

for ($i = 1; $i <= $no_of_page; $i++) {
    if ($i == $page) {
        $paging .= '<a class="page-numbers current current-page">'.$i.'</a>';
        $dots = true;
    } else {
        if ($i <= $ends_count || ($page && $i >= $page - $middle_count && $i <= $page + $middle_count) || $i > $no_of_page - $ends_count) {         
            $paging .= '<a class="page-numbers paging_blog" data-id="'.$i.'" >'.$i.'</a>';
            $dots = true;
        } elseif ($dots) {
            $paging .= '<a class="page-numbers">&hellip;</a>';
            $dots = false;
        }
    }
}

if ($prev_next && $page && ($page < $no_of_page || -1 == $no_of_page)) { 
    $paging .= '<a href="javascript:void(0)" data-id='.($page+1).' class="nextposts-link paging_blog"><i class="fa fa-caret-right"></i></a>';
}

$paging .= "</ul>";

$attr = array(
    'record' => $data,
    'paging' => $paging,
    'query' => $query_sql  // <-- Added this line to include the SQL query
);

ob_end_clean(); // discard any stray output before json
// Debug json_encode failure
$json = json_encode([
    'record' => $data,
    'paging' => $paging,
    'query'  => $query_sql
]);

error_log("JSON encode error: " . json_last_error() . " | " . json_last_error_msg());

if ($json === false) {
    // Clean the data and retry
    $data   = mb_convert_encoding($data,   'UTF-8', 'UTF-8');
    $paging = mb_convert_encoding($paging, 'UTF-8', 'UTF-8');
    $json   = json_encode([
        'record' => $data,
        'paging' => $paging,
        'query'  => $query_sql
    ], JSON_INVALID_UTF8_SUBSTITUTE);
}

echo $json;
?>
