<?php 
include "../db/config.php";
$page = isset($_POST['page_id']) ? (int)$_POST['page_id'] : 1;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$name_safe = mysqli_real_escape_string($conn, $name);

// Filter condition
$where = "is_active=1";
if (!empty($name_safe)) {
    $where .= " AND news_heading LIKE '%$name_safe%'";
}

// Total count query
$sql = select_query('tbl_news', $where);
$count = mysqli_num_rows($sql); 

$per_page = 6;
$no_of_page = ceil($count / $per_page);
$start = ($page - 1) * $per_page;

// Fetch data with limit and filter
$query = mysqli_query($conn, "SELECT * FROM tbl_news WHERE $where ORDER BY news_posted_on DESC LIMIT $start, $per_page");

$data = '';
if ($count > 0) {
    while ($rows = mysqli_fetch_array($query)) {
        $data .= '
        <div class="post-item">
            <div class="post-item_wrap">
                <div class="post-item_media">
                    <a href="'.BASE_URL.'company-news/'.$rows['news_slug'].'">
                        <img src="'.BASE_URL_IMG.'company-news/thumb/'.$rows['news_thumb_img'].'" alt="">
                    </a>
                </div>
                <div class="post-item_content">
                    <div class="post-card-details">
                        <ul>
                            <li><i class="fa-light fa-calendar-days"></i>
                                <span>'.date('M d,Y', strtotime($rows['news_posted_on'])).'</span>
                            </li>
                        </ul>
                    </div>
                    <h3><a href="'.BASE_URL.'company-news/'.$rows['news_slug'].'">'.$rows['news_heading'].'</a></h3>
                    <p>'.$rows['news_short_descp'].'&hellip;</p>
                    <a href="'.BASE_URL.'company-news/'.$rows['news_slug'].'" class="post-card_link">
                        View Details <i class="fa-solid fa-caret-right"></i>
                    </a>
                </div>
            </div>
        </div>';
    }
} else {
    $data .= '<h2>No record found</h2>';
}

// Pagination HTML
$paging = '';
$ends_count = 1; 
$middle_count = 1;
$dots = true;
$prev_next = true;

if ($prev_next && $page > 1) {
    $paging .= '<a href="javascript:void(0)" data-id="'.($page-1).'" class="prevposts-link paging_company_news"><i class="fa fa-caret-left"></i></a>';
}

for ($i = 1; $i <= $no_of_page; $i++) {
    if ($i == $page) {
        $paging .= '<a class="page-numbers current current-page">'.$i.'</a>';
        $dots = true;
    } else {
        if ($i <= $ends_count || ($i >= $page - $middle_count && $i <= $page + $middle_count) || $i > $no_of_page - $ends_count) {
            $paging .= '<a class="page-numbers paging_company_news" data-id="'.$i.'">'.$i.'</a>';
            $dots = true;
        } elseif ($dots) {
            $paging .= '<a class="page-numbers">&hellip;</a>';
            $dots = false;
        }
    }
}

if ($prev_next && $page < $no_of_page) {
    $paging .= '<a href="javascript:void(0)" data-id="'.($page+1).'" class="nextposts-link paging_company_news"><i class="fa fa-caret-right"></i></a>';
}

$attr = array('record' => $data, 'paging' => $paging);
echo json_encode($attr);
?>
