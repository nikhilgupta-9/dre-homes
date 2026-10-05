<?php
include('db/config.php');
$meta_tags=select_row('tbl_tags','id=21 and is_active=1');
include('common/header.php');
$page=1;
$row=select_row('tbl_golden_visa_content','is_active=1 ');
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
                                    <h1><?=$row['gold_visa_cont_title']?></h1>
                                </div>
                            </div>
                        </div>
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="<?=BASE_URL_IMG?>golden-visa/<?=$row['gold_visa_cont_img']?>"
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
                    <a href="<?=BASE_URL?>">Home</a><span>Golden Visa</span>
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
                                                <h3><?=$row['gold_visa_cont_heading']?></h3>
                                                <p><?=$row['gold_visa_cont_sub_heading']?></p>
                                            </div>
                                            <!--boxed-content-item-->
                                            <div class="boxed-content-item">
                                                <div class="row ">
                                                    <div class="col-md-3 text-left mb-3">
                                                        <div class="fw_car_title visa-icon">
                                                            <h3>Claim Your Visa</h3>
                                                            <i class="fas fa-id-card"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <div class="custom-form"><form class="visa-form" action="" method="post" id="golden-visa-frm">
    <div class="row">
        <!-- Left Side Inputs -->
        <div class="col-sm-6">
            <div class="cs-intputwrap mb-3">
                <i class="fa-light fa-user"></i>
                <input name="name" type="text" placeholder="Your name" value="">
            </div>
            <div class="cs-intputwrap mb-3">
                <input name="number" type="text" placeholder="Your Phone" id="comm_dev_phone" value="">
            </div>
            <div class="cs-intputwrap mb-3">
                <i class="fa-light fa-envelope"></i>
                <input type="text" name="email_id" id="email" placeholder="Email Address *" value="">
            </div>
        </div>

        <!-- Right Side Message Box -->
        <div class="col-sm-6 gv-msg">
            <div class="cs-intputwrap mb-3">
                <i class="fa-light fa-comment"></i>
                <textarea name="message" placeholder="Message" rows="6" class="form-control"></textarea>
            </div>
        </div>

        <!-- CAPTCHA Section -->
        <div class="col-md-4 mb-3">
            <img id="captcha_visa" src="<?=BASE_URL?>captcha/captcha-visa.php" alt="CAPTCHA" style="width: 100%; max-width: 190px;">
        </div>
        <div class="col-md-4 mb-3">
            <input type="text" id="captcha_visa_text" name="captcha_visa_text" class="form-control" placeholder="Enter Captcha">
        </div>
        <div class="col-md-4 mb-3">
            <button type="button" class="commentssubmit commentssubmit_fw" onclick="captcha_visa_refresh()">Regenerate Captcha</button>
        </div>

        <!-- Hidden Phone Field and Submit -->
        <div class="col-12 mt-4">
            <input type="hidden" name="golden_visa_phone_hidden">
            <button type="submit" name="golden_visa_submit" class="commentssubmit commentssubmit_fw">Submit</button>
        </div>
    </div>
</form>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--boxed-content-item end-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="limit-box"></div>
                    </div>
                    <!--boxed-container end-->
                    <!-- why choose dubai -->
                    <div class="container">
                        <div class="boxed-container">
                            <div class="boxed-content ">
                                <div class="about-wrap boxed-content-item ch-dubai-box">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="about-title ab-hero">
                                                <h2>REASONS TO CALL DUBAI HOME</h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row services-wrapper">
                                        <div class="col-lg-12">
                                            <div class="services-opions services-opions-grid">
                                                <ul>
                                                    <?php
            $query=select_query('tbl_services','is_active=1 order by serv_id desc');
            while($row=mysqli_fetch_array($query))
            {
            ?>
                                                    <li>
<i class='<?=$row['serv_fa_icon']?>'>       </i>
<h4><?=$row['serv_heading']?></h4>
                                                        <?=$row['serv_descp']?>
                                                    </li>
                                                                 <?php }?>

                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- why choose dubai-->
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