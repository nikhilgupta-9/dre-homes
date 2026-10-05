<?php
include('db/config.php');
$meta_tags=select_row('tbl_tags','id=26 and is_active=1');
include('common/header.php');
$ceo_details=select_row('tbl_msg_from_ceo','is_active=1');
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
                                    <h1><?=$ceo_details['ceo_heading']?></h1>
                                </div>
                            </div>
                        </div>
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="<?=BASE_URL_IMG?>message/<?=$ceo_details['ceo_bg_img']?>"
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
                    <a href="<?=BASE_URL?>">Home</a><span><?=$ceo_details['ceo_heading']?></span>
                    <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
                </div>
                <!--breadcrumbs-list end-->
                <!--main-content-->
                <div class="main-content ms_vir_height mg-box">
                    <!--boxed-container-->
                    <div class="boxed-container agent-box">
                        <div class="row">
                            <!-- boxed-content -->
                            <div class="col-lg-4 col-md-6">
                                <div class="boxed-content btf_init">
                                    <div class="agent-preofile-wrap">
                                        <div class="agent-preofile-content">
                                            <div class="mg-img-badge">
                                                <img src="<?=BASE_URL_IMG?>message/<?=$ceo_details['ceo_img']?>" alt="<?=$ceo_details['ceo_img_alt_tag']?>" width="100%">

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- boxed-content end-->
                            <div class="col-lg-8 col-md-6">
                                <!-- list-main-wrap-header-->
                                <div class="list-main-wrap-header box-list-header" style="margin-top: 0">
                                    <!-- list-main-wrap-title-->
                                    <div class="list-main-wrap-title agent-title py-2">
                                        <h2><?=$ceo_details['ceo_name']?></h2>
                                        <h3><?=$ceo_details['ceo_desg']?></h3>
                                    </div>
                                    <!-- list-main-wrap-title end-->
                                    <div class="agent-preofile-footer">
                                        <div class="agent-preofile-footer_title">Follow: </div>
                                        <div class="agent-preofile-footer-social">
                                            <!--<a href="<?=$ceo_details['ceo_fb_link']?>" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>-->
                                            <a href="<?=$ceo_details['ceo_insta_link']?>" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                                            <a href="<?=$ceo_details['ceo_linkdn_link']?>" target="_blank"><i class="fa-brands fa-linkedin"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <!--listing-item-container-->
                                <div class="listing-item-container fw-listing-item agent-text">
                                                                          <p><?=$ceo_details['ceo_content']?></p>

                                </div>
                                <!--listing-item-container end-->
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