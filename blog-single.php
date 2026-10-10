<?php 
include('db/config.php');
$detail=$_GET['detail'];
$meta_tags=select_row('tbl_blog',"blog_slug='$detail' and is_active=1");
include('common/header.php');
$page=1;
$sql=select_query('tbl_blog',"blog_slug!='$detail' and is_active='1' ");
$count=mysqli_num_rows($sql);
$per_page=3;
$no_of_page=ceil($count/$per_page);
$start=($page-1)*$per_page;
$test_content=select_row('tbl_testimonial_content','is_active=1');
$blog_detail=select_row('tbl_blog',"blog_slug='$detail' and is_active='1'");
$nonencoded_url="https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$encoded_url = urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
?>
        
    <!--warpper-->
    <div class="box-head-bg"></div>
    <div class="wrapper">
        <!--content-->
        <div class="content">
            <!--container-->
            <div class="container">
                <!--breadcrumbs-list-->
                <div class="breadcrumbs-list bl_flat">
                    <a href="<?=BASE_URL?>">Home</a><a href="<?=BASE_URL?>our-blogs">Blog</a>
                    <!--<span><?=$blog_detail['blog_heading']?></span>-->
                    <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
                </div>
                <!--breadcrumbs-list end-->
                <!--main-content-->
                <div class="main-content">
                    <!--boxed-container-->
                    <div class="boxed-container">
                        <div class="scroll-content-wrap">

                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="post-container">
                                        <div class="boxed-content">
                                            <div class="boxed-content-title seo-chg">
                                                <h1><?=$blog_detail['blog_heading']?></h1>
                                            </div>
                                            <div class="boxed-content-item">
                                                <!-- blog media -->
                                                <div class="blog-media">
                                                <div class="lightgallery">
                                                                    <div class="hov_zoom"><img
                                                                            src="<?=BASE_URL_IMG?>blog/inner/<?=$blog_detail['blog_inner_img']?>" alt=""><a
                                                                            href="<?=BASE_URL_IMG?>blog/inner/<?=$blog_detail['blog_inner_img']?>"
                                                                            class="box-media-zoom   popup-image"><i
                                                                                class="fal fa-search"></i></a></div>
                                                                    
                                                                </div>
                                                </div>
                                                <div class="text-block post-single_tb">
                                                    <div class="post-card-details" style="margin-bottom: 20px;">
                                                        <ul>
                                                            <li><i class="fa-light fa-calendar-days"></i><span> <?=date('M d, Y',strtotime($blog_detail['blog_posted_on']))?></span></li>
                                                          
                                                        </ul>
                                            
                                                    </div>
                                                    <div class="text-block-container editor-cont">
                                                                                        <?=$blog_detail['blog_long_descp']?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-4 fixed-social">
                                    <!--boxed-container-->
                                    <div class="sb-container">

                                        <!--boxed-content-->
                                        <div class="boxed-content">
                                            <!--boxed-content-title-->
                                            <div class="boxed-content-title">
                                                <h3>Recent Posts</h3>
                                            </div>
                                            <!--boxed-content-title end-->
                                            <!--boxed-content-item-->
                                            <div class="boxed-content-item bc-item_smal_pad">
                                                <div class="recent-post-widget">
                                                    <ul>
                                                            <?php
                            $service=select_query('tbl_blog',"blog_slug!='$detail' and is_active=1 order by blog_posted_on desc LIMIT 3");
                            while($row=mysqli_fetch_array($service))
                            {
                            ?> 
                                                        <li>
                                                            <div class="recent-post-img"><a href="<?=BASE_URL?>our-blogs/<?=$row['blog_slug']?>"><img
                                                                        src="<?=BASE_URL_IMG?>blog/thumb/<?=$row['blog_thumb_img']?>" alt="<?=$row['blog_thumb_img_alt']?>"></a></div>
                                                            <div class="recent-post-content">
                                                                <h4><a href="<?=BASE_URL?>our-blogs/<?=$row['blog_slug']?>"><?=$row['blog_heading']?></a></h4>
                                                                <div class="recent-post-opt">
                                                                    <span class="post-date"><?=date('M d, Y',strtotime($row['blog_posted_on']))?>
</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                          <?php }?>             
                                                    </ul>
                                                </div>
                                            </div>
                                            <!--boxed-content-item end-->
                                        </div>
                                        <!--boxed-content end-->

                                   
                                    </div>
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