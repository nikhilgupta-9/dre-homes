<?php
include('db/config.php');
$meta_tags = select_row('tbl_tags', 'id=36 and is_active=1');
include('common/header.php');
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
                                <h1>From Desert Mirage to Global Icon</h1>
                            </div>
                        </div>
                    </div>

                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">

                        <div class="bg" data-bg="<?= BASE_URL ?>images/bg/team-page-bg.jpg"
                            data-scrollax="properties: { translateY: '30%' }">
                        </div>
                        <!--<img src="<?= BASE_URL ?>images/bg/2.jpg" class="img-fluid w-100" alt="Hero Background">-->
                    </div>

                </div>
            </div>
        </div>
        <!--section-end-->

        <!--container-->
        <div class="container">
            <!--breadcrumbs-list-->
            <div class="breadcrumbs-list bl_flat">
                <a href="<?= BASE_URL ?>">Home</a><span>Dubai Evolution</span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
            <!--breadcrumbs-list end-->

            <!--main-content-->
            <div class="main-content">
                <!--boxed-container-->
                <div class="boxed-container">
                    <div class="scroll-content-wrap">
                        <div class="post-container">
                            <div class="boxed-content">
                                <div class="boxed-content-item">
                                    <div class="text-block-container">
                                        <div class="row guide-head">
                                            <div class="col-12 mt-5">

                                                <div class="">
                                                    <!-- <div class="banner-widget-wrap">
                                                            <div class="bg-wrap bg-parallax-wrap-gradien fs-wrapper">
                                                                <div class="bg"
                                                                    data-bg="<?= BASE_URL ?>images/home-sec/dre-brand-video-n.jpg">
                                                                </div>
                                                            </div>
                                                            <div class="banner-widget_content">
                                                                <div class="video-box-btn" id="html5-videos"
                                                                    data-html="#video3"><i class="fas fa-play"></i>
                                                                </div>

                                                            </div>
                                                            <div style="display:none;" id="video3" class=""
                                                                data-videolink="https://youtu.be/4jXkVmUTeRE?si=rLBbpPYuW1b_Gu-o">
                                                                <video class="lg-video-object lg-html5" controls
                                                                    preload="none">
                                                                    <source src="/" type="video/mp4">
                                                                </video>
                                                            </div>
                                                        </div> -->
                                                    <div class="y-video-thumb">
                                                        <a href="javascript:void(0)"
                                                            
                                                            class="d-flex justify-content-center align-items-center"><img
                                                                src="<?= BASE_URL ?>images/home-sec/dubai-evolution.jpg"
                                                                alt=""></a>
                                                        <div class="banner-widget_content">
                                                            <div class="video-box-btn" id="" data-html="#video3"><a
                                                                    href="javascript:void(0)"
                                                                    ><i class="fas fa-play"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>


                                            <div class="col-12 mt-5">
                                                <!-- <h3>Dre - 17+ Years of Excellence</h3> -->
                                                <p>Once a quiet desert region, Dubai has rapidly grown into a shining example of modern development and innovation. Guided by visionary leaders, the city transformed its landscape by building world-famous landmarks like the Burj Khalifa and Palm Jumeirah. Dubai’s economy expanded beyond oil, becoming a global hub for business, tourism, and technology, attracting millions of visitors and investors every year.</p>

                                                <p>Today, Dubai is celebrated for its luxurious real estate, diverse multicultural community, and strong focus on sustainability through green projects and renewable energy. The city embraces innovation, making it a unique and welcoming place for people from all over the world. Dubai’s incredible journey from desert sands to global icon shows how determination and smart planning can create a truly extraordinary city.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
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