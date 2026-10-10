<?php 
include('db/config.php');
$meta_tags = select_row('tbl_tags','id=11 and is_active=1');
include('common/header.php');

// Get parameters
$page = isset($_GET['page_id']) && is_numeric($_GET['page_id']) ? (int)$_GET['page_id'] : 1;
$search_name = isset($_GET['name']) ? trim($_GET['name']) : '';

// Build WHERE clause
$where_clause = "is_active=1";
if ($search_name !== '') {
    $safe_name = mysqli_real_escape_string($conn, $search_name);
    $where_clause .= " AND dev_heading LIKE '%$safe_name%'";
}

$sql = select_query('tbl_developer', $where_clause);
$count = mysqli_num_rows($sql);

$per_page = 8;
$no_of_page = ceil($count / $per_page);
$start = ($page - 1) * $per_page;

// Developer content
$dev_content = select_row('tbl_developer_content','is_active=1');
$nonencoded_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>

    <!--warpper-->
    <div class="wrapper">
        <!--content-->
        <div class="content">
            <!--section-->
            <div class="section hero-section hero-section_sin">
                <div class="hero-section-wrap">
                    <div class="hero-section-wrap-item">
                        <div class="container">
                            <div class="hero-section-container">
                                <div class="hero-section-title">
                                    <h1>Developers</h1>
                                </div>
                            </div>
                        </div>
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="<?=BASE_URL_IMG?>developer/<?=$dev_content['dev_cont_img']?>"
                                data-scrollax="properties: { translateY: '30%' }"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!--section-end-->
            <!--container-->
            <div class="container">
                <!--breadcrumbs-list-->
                <div class="breadcrumbs-list bl_flat">
                    <a href="<?=BASE_URL?>">Home</a><span>Developers</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
                </div>
                <!--breadcrumbs-list end-->
                <!--main-content-->
                <div class="main-content">
                    <div class="boxed-container dev-search-box dsb-box">
                        
                        <!-- list-searh-input-wrap-->
                        <div class="list-searh-input-wrap box_list-searh-input-wrap lws_mobile lsw_mb-btn">
                            <form autocomplete="off">
                                <div class="custom-form">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="autocomplete cs-intputwrap">
                                                <i class="fa-light fa-search"></i>
                                                <input id="myInput" type="text" name="search_dev" placeholder="Search Developer" value="<?=$_GET['name']?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--boxed-container-->
                    <div class="boxed-container dev-pg developer_pos">
                        <!--listing-item-container-->
                        <div class="listing-item-container two-columns-grid fw-listing-item fw-listing-item2 mt-5 developer_data">
                           
                            <?php
                        $dev=select_query('tbl_developer',"is_active=1 order by dev_posted_on  desc LIMIT $start,$per_page");
                        if($count>0)
                        {
                        while($row=mysqli_fetch_array($dev))
                        {
                        ?> 

                            <div class="listing-item dev-list">
                                <div class="geodir-category-listing">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="geodir-category-img d-flex justify-content-center align-items-center">
                                                <a href="<?=BASE_URL?>developer/<?=$row['dev_slug']?>" class="geodir-category-img_item">
                                                <img src="<?=BASE_URL_IMG?>developer/<?=$row['dev_img']?>" alt="<?=$row['dev_img_alt']?>">
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="geodir-category-content">
                                                <h3><a href="<?=BASE_URL?>developer/<?=$row['dev_slug']?>"><?=$row['dev_heading']?></a></h3>
                                                <p> <?=$row['dev_short_descp']?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php }}else{?>
                        <h2>No record found</h2>
                        <?php } ?>
                           

                        </div>
                        <!--listing-item-container end-->
                       
                          <div class="pagination-wrap">
                    <div class="pagination">
                    <?php include('pagination/paging_developer.php');?>
                    </div>
                    </div>
                      
                    </div>
                    <!--boxed-container end-->
                </div>
                <!--main-content end-->
                <div class="to_top-btn-wrap">
                    <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                    <div class="svg-corner svg-corner_white" style="top:0;left:  -40px; transform: rotate(-90deg)">
                    </div>
                    <div class="svg-corner svg-corner_white" style="top:0;right: -40px; transform: rotate(-180deg)">
                    </div>
                </div>
            </div>
            <!-- container end-->
        </div>
        <!--content  end-->
        <!--main-footer-->
        <?php include 'common/footer.php'; ?>