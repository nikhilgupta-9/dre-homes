<?php
include('db/config.php');
$det=$_GET['detail'];
$meta_tags=select_row('tbl_service_section','serv_sec_slug="'.$det.'" and is_active=1');
include('common/header.php');
$row=select_row('tbl_service_section','serv_sec_slug="'.$det.'" and is_active=1');

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
                                    <h1>Services</h1>
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="<?=BASE_URL?>images/bg/2.jpg" data-scrollax="properties: { translateY: '30%' }">
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
                    <a href="<?=BASE_URL?>">Home</a><span><?=$row['serv_sec_name']?></span>
                    <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
                </div>
                <!--breadcrumbs-list end-->
                <!--main-content-->
                <div class="main-content">
                    <!--boxed-container-->
                    <div class="boxed-container">
                        <div class="scroll-content-wrap">
                            
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="post-container">
                                        <div class="boxed-content">
                                            <div class="boxed-content-title">
                                                <h3><?=$row['serv_sec_name']?></h3>
                                            </div>
                                            <div class="boxed-content-item">
                                                
                                                <div class="text-block post-single_tb">
                                                    
                                                    <div class="text-block-container editor-cont">
                                                        <?=$row['serv_sec_content']?>
                                                       
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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