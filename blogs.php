<?php
include('db/config.php');
$meta_tags=select_row('tbl_tags','id=13 and is_active=1');
include('common/header.php');
$page=1;
$sql=select_query('tbl_blog','is_active=1 ');
$count=mysqli_num_rows($sql);
$per_page=6;
$no_of_page=ceil($count/$per_page);
$start=($page-1)*$per_page;
$blog_content=select_row('tbl_blog_content','is_active=1');
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
                                    <h1><?=$blog_content['blog_cont_heading']?></h1>
                                </div>
                            </div>
                        </div>
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="<?=BASE_URL_IMG?>blog/<?=$blog_content['blog_cont_img']?>"
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
                    <a href="<?=BASE_URL?>">Home</a> <span>Blogs</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
                </div>
                <!--breadcrumbs-list end-->
                <!--main-content-->
                <div class="main-content">
                    <!--boxed-container-->
                    <div class="boxed-container">
                        <div class="row blog_pos">
                           <div class="col-lg-8">
                                <div class="post-container">
                                    <div class="post-items blog-items blog_data">
    <?php
                            $blog=select_query('tbl_blog',"is_active=1 ORDER BY blog_posted_on desc LIMIT $start,$per_page");
                            if($count>0)
                            {

                            while($row=mysqli_fetch_array($blog))
                            {
                            ?>  
                                                                    <div class="post-item">
                                            <div class="post-item_wrap">
                                                <div class="post-item_media">
                                                    <a href="<?=BASE_URL?>our-blogs/<?=$row['blog_slug']?>">
                                                        <img src="<?=BASE_URL_IMG?>blog/thumb/<?=$row['blog_thumb_img']?>" alt="<?=$row['blog_thumb_img_alt']?>">
                                                    </a>
                                                </div>
                                                <div class="post-item_content">
                                                    <div class="post-card-details">
                                                        <ul>
                                                            <li><i class="fa-light fa-calendar-days"></i><span>                                                 <?=date('M d, Y',strtotime($row['blog_posted_on']))?>
</span></li>
                                                        </ul>
                                                    </div>
                                                    <h3><a href="<?=BASE_URL?>our-blogs/<?=$row['blog_slug']?>"><?= $row['blog_heading'] ?></a></h3>
                                                    <p><?=$row['blog_short_descp']?>&hellip;</p>

                                                    <a href="<?=BASE_URL?>our-blogs/<?=$row['blog_slug']?>" class="post-card_link">View Details <i
                                                            class="fa-solid fa-caret-right"></i></a>
                                                </div>
                                            </div>
                                        </div>

                                            <?php }}else{?>
                                <h2>No record found</h2>

                            <?php } ?>
                                        
                                    </div>
                                    <div class="pagination-wrap">
                    <div class="pagination">
                    <?php include('pagination/paging_blog.php');?>
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
                                                <div class="bg  " data-bg="<?=BASE_URL?>images/bg/golden-visa-cards.png"></div>
                                            </div>
                                            <div class="post-banner-widget_content">
                                                <h5>How Property Gets You a Golden Visa</h5>
                                                <a href="<?=BASE_URL?>golden-visa">Find Out</a>
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
                                            <a href="<?=$header_details['header_fb_link']?>" target="_blank" class="mt-1"><i class="fa-brands fa-facebook-f"></i></a>
                                <a href="<?=$header_details['header_instagram_link']?>" target="_blank" class="mt-1"><i class="fa-brands fa-instagram"></i></a>
                                <a href="<?=$header_details['header_youtube_link']?>" target="_blank" class="mt-1"><i class="fa-brands fa-youtube"></i></a>
                                <a href="<?=$header_details['header_linkedin_link']?>" target="_blank" class="mt-1"><i class="fa-brands fa-linkedin"></i></a>
                                <a href="<?=$header_details['header_spotify_link']?>" target="_blank" class="mt-1"><i class="fa-brands fa-tiktok"></i></a>
                                
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