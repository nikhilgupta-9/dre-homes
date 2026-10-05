<?php
include('db/config.php');
$meta_tags = select_row('tbl_tags', 'id=23 and is_active=1');
include('common/header.php');
$page = 1;
$sql = select_query('tbl_news', 'is_active=1 ');
$count = mysqli_num_rows($sql);
$per_page = 6;
$no_of_page = ceil($count / $per_page);
$start = ($page - 1) * $per_page;
$news_content = select_row('tbl_company_news_content', 'is_active=1');
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
                                <h1><?= $news_content['company_news_cont_heading'] ?></h1>

                            </div>
                        </div>
                    </div>

                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                        <div class="bg"
                            data-bg="<?= BASE_URL_IMG ?>company-news/<?= $news_content['company_news_cont_img'] ?>"
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
                <a href="<?= BASE_URL ?>">Home</a> <span>Company News</span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
            <!--breadcrumbs-list end-->
            <!--main-content-->
            <div class="main-content">
                <!--container-->
                <div class="container">
                    <div class="boxed-container dev-search-box">
                        <div class="show-mob-filter"><i class="far fa-search"></i> Search Particular News</div>
                        <!-- list-searh-input-wrap-->
                        <div class="list-searh-input-wrap box_list-searh-input-wrap lws_mobile lsw_mb-btn">
                            <div class="close_mob-filter cmf"><i class="fa-regular fa-xmark"></i></div>
                            <div class="list-searh-input-wrap-title_wrap">
                                <div class="list-searh-input-wrap-title"><span><i class="far fa-search"></i>Search
                                        Particular News</span></div>
                            </div>
                            <div class="custom-form">
                                <div class="row">
                                    <!-- listsearch-input-item -->
                                    <div class="col-lg-8">
                                        <div class="cs-intputwrap">
                                            <label for="" class="sr-only">Company News Search</label>
                                            <i class="fa-light fa-search"></i>
                                            <input type="text" placeholder="Type your keyword..."
                                                name="comp_news_search" value="">
                                        </div>
                                    </div>
                                    <!-- listsearch-input-item -->
                                    <!-- listsearch-input-item -->
                                    <div class="col-lg-4">
                                        <button onclick="" class="commentssubmit commentssubmit_fw">Search</button>
                                    </div>
                                    <!-- listsearch-input-item -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--boxed-container-->
                    <?php
                    $featured_news = select_row('tbl_news', "is_active=1 AND news_markspot=1 ORDER BY news_posted_on DESC");

                    if (!empty($featured_news)): ?>
                        <div class="boxed-container">
                            <div class="boxed-content">
                                <div class="spotlight-box">
                                    <div class="row">
                                        <div class="col-lg-6 p-0">
                                            <div class="post-item_media h-100">
                                                <img src="<?= BASE_URL_IMG ?>company-news/thumb/<?= $featured_news['news_thumb_img'] ?>"
                                                    alt="<?= htmlspecialchars($featured_news['news_heading']) ?>">
                                                <ul class="post_header_cat">
                                                    <li><a href="<?= BASE_URL ?>company-news/<?= $featured_news['news_slug'] ?>"
                                                            class="cat-opt">In Spotlight</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 p-0">
                                            <div class="post-item_content">
                                                <div class="post-card-details mb-2">
                                                    <ul class="mb-0">
                                                        <li><i class="fa-regular fa-calendar-days"></i>
                                                            <span><?= date('F d, Y', strtotime($featured_news['news_posted_on'])) ?></span>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <h3><a
                                                        href="<?= BASE_URL ?>company-news/<?= $featured_news['news_slug'] ?>"><?= $featured_news['news_heading'] ?></a>
                                                </h3>
                                                <p><?= $featured_news['news_short_descp'] ?>...</p>
                                                <a href="<?= BASE_URL ?>company-news/<?= $featured_news['news_slug'] ?>"
                                                    class="post-card_link">View Details →</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <!--boxed-container end-->

                </div>
                <!--container end-->
                <!--boxed-container-->
                <div class="boxed-container">
                    <div class="row company_news_pos">
                        <div class="col-lg-8">
                            <div class="post-container">
                                <div class="post-items company-news-items company_news_data">
                                    <?php
                                    $service = select_query('tbl_news', "is_active=1 ORDER BY news_posted_on desc LIMIT $start,$per_page");
                                    if ($count > 0) {

                                        while ($row = mysqli_fetch_array($service)) {
                                            ?>
                                            <div class="post-item">
                                                <div class="post-item_wrap">
                                                    <div class="post-item_media">
                                                        <a href="<?= BASE_URL ?>company-news/<?= $row['news_slug'] ?>">
                                                            <img src="<?= BASE_URL_IMG ?>company-news/thumb/<?= $row['news_thumb_img'] ?>"
                                                                alt="">
                                                        </a>
                                                    </div>
                                                    <div class="post-item_content">
                                                        <div class="post-card-details">
                                                            <ul>
                                                                <li><i class="fa-light fa-calendar-days"></i><span>
                                                                        <?= date('M d,Y', strtotime($row['news_posted_on'])) ?>
                                                                    </span></li>
                                                            </ul>
                                                        </div>
                                                        <h3><a
                                                                href="<?= BASE_URL ?>company-news/<?= $row['news_slug'] ?>"><?= $row['news_heading'] ?></a>
                                                        </h3>
                                                        <p><?= $row['news_short_descp'] ?>&hellip;</p>

                                                        <a href="<?= BASE_URL ?>company-news/<?= $row['news_slug'] ?>"
                                                            class="post-card_link">View Details <i
                                                                class="fa-solid fa-caret-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php }
                                    } else { ?>
                                        <h2>No record found</h2>

                                    <?php } ?>

                                </div>
                                <div class="pagination-wrap">
                                    <div class="pagination">
                                        <?php include('pagination/paging_company_news.php'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 fixed-social">
                            <!--boxed-container-->
                            <div class="sb-container ">
                                <!--boxed-content-->
                                <div class="boxed-content mb-3">
                                    <div class="post-banner-widget">
                                        <div class="bg-wrap fs-wrapper bg-parallax-wrap-gradien">
                                            <div class="bg  " data-bg="images/bg/about-box.jpg"></div>
                                        </div>
                                        <div class="post-banner-widget_content">
                                            <h5>Got the drive? Let’s build success together at DRE!</h5>
                                            <a href="https://drehomes.com/careers/">Join The Team</a>
                                        </div>
                                    </div>
                                </div>
                                <!--boxed-content-item end-->
                                <!--boxed-content-->
                                <div class="boxed-content">
                                    <!--boxed-content-title-->
                                    <div class="boxed-content-title">
                                        <h3>Follow Us</h3>
                                    </div>
                                    <!--boxed-content-title end-->
                                    <!--boxed-content-item-->
                                    <div class="boxed-content-item bc-item_smal_pad">
                                        <div class="social-widget">
                                            <a href="https://www.facebook.com/drehomesrealestateuae/" target="_blank"
                                                class="mt-1"><i class="fa-brands fa-facebook-f"></i></a>
                                            <a href="https://www.instagram.com/drehomes_realestate/" target="_blank"
                                                class="mt-1"><i class="fa-brands fa-instagram"></i></a>
                                            <a href="https://www.youtube.com/@DrehomesRE" target="_blank"
                                                class="mt-1"><i class="fa-brands fa-youtube"></i></a>
                                            <a href="https://www.linkedin.com/company/drehomes-real-estate-brokers/mycompany/verification/"
                                                target="_blank" class="mt-1"><i class="fa-brands fa-linkedin"></i></a>
                                            <a href="https://www.tiktok.com/@drehomesrealestate" target="_blank"
                                                class="mt-1"><i class="fa-brands fa-tiktok"></i></a>

                                        </div>
                                    </div>
                                    <!--boxed-content-item end-->
                                </div>
                                <!--boxed-content end-->
                            </div>
                        </div>
                    </div>
                    <div class="limit-box"></div>
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
    <?php include 'common/footer.php'; ?>