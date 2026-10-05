<?php
ob_start(); // capture any stray output
ini_set('display_errors', 0); // hide from output
ini_set('log_errors', 1);
ini_set('error_log', '/Applications/XAMPP/xamppfiles/htdocs/dre_homes_new/ajax/debug_blog.log');
error_reporting(E_ALL);
include "../db/config.php";

$page = (int)$_POST['page_id'];

$count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_blog WHERE is_active = 1");
$count_row    = mysqli_fetch_assoc($count_result);
$count        = $count_row['total'];

$per_page   = 6;
$no_of_page = ceil($count / $per_page);
$start      = ($page - 1) * $per_page;

$query_sql = "SELECT * FROM tbl_blog WHERE is_active = 1 ORDER BY blog_posted_on DESC LIMIT $start, $per_page";
$query     = mysqli_query($conn, $query_sql);

error_log("PAGE: $page | START: $start | COUNT: $count | PAGES: $no_of_page");

$data = '';
if ($count > 0) {
    $rownum = 0;
    while ($rows = mysqli_fetch_array($query)) {
        $rownum++;
        error_log("ROW $rownum: id=" . $rows['blog_id'] . " | date=" . $rows['blog_posted_on'] . " | slug=" . $rows['blog_slug']);

        $heading = !empty($rows['blog_heading'])     ? truncateHeading($rows['blog_heading'])       : 'Untitled';
        error_log("ROW $rownum: heading OK");

        $descp   = !empty($rows['blog_short_descp']) ? truncateParagraph($rows['blog_short_descp']) : '';
        error_log("ROW $rownum: descp OK");

        $slug    = $rows['blog_slug']          ?? '#';
        $thumb   = $rows['blog_thumb_img']     ?? '';
        $alt     = $rows['blog_thumb_img_alt'] ?? '';
        $timestamp = strtotime($rows['blog_posted_on']);
        $date    = ($timestamp && $timestamp > 0) ? date('M d, Y', $timestamp) : '';
        error_log("ROW $rownum: all vars OK");

        $data .= '
        <div class="post-item">
            <div class="post-item_wrap">
                <div class="post-item_media">
                    <a href="' . BASE_URL . 'our-blogs/' . $slug . '">
                        <img src="' . BASE_URL_IMG . 'blog/thumb/' . $thumb . '" alt="' . $alt . '">
                    </a>
                </div>
                <div class="post-item_content">
                    <div class="post-card-details">
                        <ul>
                            <li><i class="fa-light fa-calendar-days"></i><span>' . $date . '</span></li>
                        </ul>
                    </div>
                    <h3><a href="' . BASE_URL . 'our-blogs/' . $slug . '">' . $heading . '</a></h3>
                    <p>' . $descp . '&hellip;</p>
                    <a href="' . BASE_URL . 'our-blogs/' . $slug . '" class="post-card_link">View Details <i class="fa-solid fa-caret-right"></i></a>
                </div>
            </div>
        </div>';
        error_log("ROW $rownum: HTML built OK");
    }
    error_log("LOOP DONE: $rownum rows processed");
} else {
    $data = '<h2>No record found</h2>';
}

$ends_count   = 1;
$middle_count = 1;
$dots   = false;
$paging = '';

if ($page > 1) {
    $paging .= '<a href="javascript:void(0)" data-id="' . ($page - 1) . '" class="prevposts-link paging_blog"><i class="fa fa-caret-left"></i></a>';
}

for ($i = 1; $i <= $no_of_page; $i++) {
    if ($i == $page) {
        $paging .= '<a class="page-numbers current current-page">' . $i . '</a>';
        $dots = false;
    } elseif (
        $i <= $ends_count ||
        ($i >= $page - $middle_count && $i <= $page + $middle_count) ||
        $i > $no_of_page - $ends_count
    ) {
        $paging .= '<a class="page-numbers paging_blog" data-id="' . $i . '">' . $i . '</a>';
        $dots = false;
    } elseif (!$dots) {
        $paging .= '<a class="page-numbers">&hellip;</a>';
        $dots = true;
    }
}

if ($page < $no_of_page) {
    $paging .= '<a href="javascript:void(0)" data-id="' . ($page + 1) . '" class="nextposts-link paging_blog"><i class="fa fa-caret-right"></i></a>';
}

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