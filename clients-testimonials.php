<?php 
include('db/config.php');
$meta_tags=select_row('tbl_tags','id=16 and is_active=1');
include('common/header.php');

$page=1;
$sql=select_query('tbl_testimonial','is_active=1 ');
$count=mysqli_num_rows($sql);
$per_page=6;
$no_of_page=ceil($count/$per_page);
$start=($page-1)*$per_page;
$test_content=select_row('tbl_testimonial_content','is_active=1');
$test_content=select_row('tbl_testimonial_content','is_active=1');
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
                                    <h1> <?=$test_content['test_heading']?></h1>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="<?=BASE_URL_IMG?>testimonial/<?=$test_content['test_img']?>"
                                data-scrollax="properties: { translateY: '30%' }">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--section-end-->
            <!--container-->
            <div class="container">
                <!--breadcrumbs-list-->
                <div class="breadcrumbs-list bl_flat">
                    <a href="<?=BASE_URL?>">Home</a><span>Client Testimonial</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
                </div>
                <!--breadcrumbs-list end-->
                <!--main-content-->
                <div class="main-content ms_vir_height">
                    <!--boxed-container-->
                    <div class="">
                        <div class="row clients_testimonial_pos">
                            <!-- boxed-content -->
                            <div class="col-12">
                                <div class="boxed-content">
                                    <div class="boxed-content-title">
                                        <h3>Rated 5 Stars by Our Happy Clients</h3>
                                        <p class="pe-5">Every review is a story of trust, satisfaction, and
                                            success—explore what our clients have to say.</p>
                                    </div>
                                    
                                    <div class="boxed-content-item ">
                                        <div class="row clients_testimonial_data">
                                            <?php
                            $service=select_query('tbl_testimonial',"is_active=1 order by test_posted_on desc LIMIT $start,$per_page");
                            $j=1;
                            while($row=mysqli_fetch_array($service))
                            {
                            ?>  
                                            <div class="col-md-6 my-3">
                                                <div class="swiper-slide">
                                                    <div class="testi-item">
                                                        <div class="testimonilas-text">
                                                            <div class="testi-header">
                                                                <h3><?=$row['test_client_name']?></h3>
                                                            </div>
                                                            <div class="testimonilas-text-item text-left">
                                                                <div class="testimonilas-text-item-wrap">
                                                                    <div class="testimonial-snippet">
                                                                                                <?=substr(strip_tags($row['test_descp']),0,300).'...'?>
                                                                                                
                                                                                               <a href="javascript:void(0);"
   class="pop-read-more"
   data-fulltext="<?= strip_tags($row['test_descp']) ?>">Read More</a>


                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="testi-footer">
                                                            <span class="testi-number"><?=date('d M,Y',strtotime($row['test_posted_on']))?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                                        <?php $j++;}?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- boxed-content end-->

                            <div class="pagination-wrap">
                    <div class="pagination">
                    <?php include('pagination/paging_clients_testimonial.php');?>
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