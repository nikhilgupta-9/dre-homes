<?php
include('db/config.php');
include('common/header.php');
?>

<!--wrapper-->
<div class="wrapper">
    <!--content-->
    <div class="content">
        <!--hero section-->
        <div class="section hero-section hero-section_sin">
            <div class="hero-section-wrap">
                <div class="hero-section-wrap-item">
                    <div class="container">
                        <div class="hero-section-container">
                            <div class="hero-section-title">
                                <!--<h2>404 - Page Not Found</h2>-->
                            </div>
                        </div>
                    </div>

                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                        <div class="bg" data-bg="images/page-not-found-3.jpg"
                             data-scrollax="properties: { translateY: '30%' }">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--hero section end-->

        <!--breadcrumbs-->
        <div class="container">
            <div class="breadcrumbs-list bl_flat">
                <a href="<?=BASE_URL?>">Home</a><span>404</span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
        </div>
        <!--breadcrumbs end-->

        <!--main content-->
        <div class="main-content ms_vir_height">
            <div class="container">
                <div class="page-nf">
                    <h2 style="font-size: 64px; margin-bottom: 20px;"><i class="fa fa-meh-o" aria-hidden="true"></i>Oops!</h2>
                    <p><i class="fa fa-meh-o"></i>It might have been moved or deleted, or maybe the URL was typed incorrectly.</p>
                   
                </div>
            </div>
        </div>
        <!--main content end-->
    </div>
    <!--content end-->
</div>
<!--wrapper end-->

<?php include 'common/footer.php'; ?>
