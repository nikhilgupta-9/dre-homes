<?php
include('db/config.php');
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
                                <h1>Get In Touch</h1>

                            </div>
                        </div>
                    </div>

                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                        <div class="bg" data-bg="images/bg/team-page-bg.jpg" data-scrollax="properties: { translateY: '30%' }">
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
                <a href="javascript:void(0)">Home</a><span>Contact Us</span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
            <!--breadcrumbs-list end-->
            <!--main-content-->
            <div class="main-content ms_vir_height">
                <!--boxed-container-->
                <div class="boxed-container">
                    <!-- contacts-cards-wrap  -->
                    <div class="contacts-cards-wrap">
                        <div class="row contacts-row">
                            <!-- contacts-card-item -->
                            <div class="col-lg-4 my-2">
                                <div class="contacts-card-item">
                                    <i class="fa-regular fa-location-dot"></i>
                                    <span>Visit Us</span>
                                    <p>Drop by, we’d love to meet you.</p>
                                    <a href="https://maps.app.goo.gl/zHNjkBSkwTs6xGsp8"><b>Headquarters:</b> Park
                                        Heights Square 2 </a>
                                    <a href="https://maps.app.goo.gl/42FBpCmPsNUR667V6"><b>Branch:</b> Town Square
                                    </a>
                                </div>
                            </div>
                            <!-- contacts-card-item end-->
                            <!-- contacts-card-item -->
                            <div class="col-lg-4 my-2">
                                <div class="contacts-card-item">
                                    <i class="fa-regular fa-phone-rotary"></i>
                                    <span>Give Us a Ring</span>
                                    <p>Pick up the phone, we’ve got you.</p>
                                    <a href="tel:800 37373"> Toll Free:800 37373</a>
                                    <a href="tel:+971 52 754 3243"> Hotline :+971 52 754 3243</a>
                                </div>
                            </div>
                            <!-- contacts-card-item end-->
                            <!-- contacts-card-item -->
                            <div class="col-lg-4 my-2">
                                <div class="contacts-card-item">
                                    <i class="fa-regular fa-mailbox"></i>
                                    <span>Write to Us</span>
                                    <p>Our inbox is always open for you — whether it’s a question, or an idea, we’d love to hear from you.</p>
                                    <a href="mailto:sales@drehomes.com">sales@drehomes.com</a>
                                </div>
                            </div>
                            <!-- contacts-card-item end-->
                        </div>
                    </div>
                    <!-- contacts-cards-wrap end   -->
                    <div class="row">
                        <!-- contacts-opt-wrap -->
                        <div class="col-lg-7">
                            <div class="contacts-opt-wrap">
                                <div class="contact-wh_title">Working Hours</div>
                                <div class="contact-wh">
                                    <div class="contact-wh-item">
                                        Monday - Saturday <strong> 09:00am - 6:00pm</strong></div>
                                    <div class="contact-wh-item">Sunday:<strong> Closed</strong></div>
                                </div>
                            </div>
                        </div>
                        <!-- contacts-opt-wrap end-->
                        <!-- contacts-opt-wrap -->
                        <div class="col-lg-5">
                            <div class="contacts-opt-wrap">
                                <div class="contact-social">
                                    <span class="cs-title">Find us on: </span>
                                    <div class="contact-social-container">
                                        <a href="<?= $header_details['header_fb_link'] ?>" target="_blank"
                                            class="mt-1"><i class="fa-brands fa-facebook-f"></i></a>
                                        <a href="<?= $header_details['header_instagram_link'] ?>" target="_blank"
                                            class="mt-1"><i class="fa-brands fa-instagram"></i></a>
                                        <a href="<?= $header_details['header_youtube_link'] ?>" target="_blank"
                                            class="mt-1"><i class="fa-brands fa-youtube"></i></a>
                                        <a href="<?= $header_details['header_linkedin_link'] ?>" target="_blank"
                                            class="mt-1"><i class="fa-brands fa-linkedin"></i></a>
                                        <a href="<?= $header_details['header_spotify_link'] ?>" target="_blank"
                                            class="mt-1"><i class="fa-brands fa-tiktok"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- contacts-card-item end-->
                    </div>
                    <div class="contacts-form-wrap">
                        <div class="row">
                            <!-- contacts-opt-wrap -->
                            <div class="col-lg-6">
                                <div class="boxed-content">
                                    <div class="boxed-content-title">
                                        <h3>Drop Your Query</h3>
                                    </div>
                                    <div class="boxed-content-item">
                                        <div class="comment-form custom-form contactform-wrap">
                                            <div class="custom-form" id="">
                                                <form method="post" name="contact-property-form" id="">
                                                    <div class="row">
                                                        <!-- Left Side Inputs -->
                                                        <div class="col-sm-6">
                                                            <div class="cs-intputwrap mb-3">
                                                                <i class="fa-light fa-user"></i>
                                                                <input name="name" type="text" placeholder="Your name"
                                                                    value="">
                                                            </div>
                                                            <div class="cs-intputwrap mb-3">
                                                                <input name="number" type="text"
                                                                    placeholder="Your Phone" id=""
                                                                    value="">
                                                            </div>
                                                            <div class="cs-intputwrap mb-3">
                                                                <i class="fa-light fa-envelope"></i>
                                                                <input type="text" name="email_id" id=""
                                                                    placeholder="Email Address *" value="">
                                                            </div>
                                                        </div>

                                                        <!-- Right Side Message Box -->
                                                        <div class="col-sm-6 gv-msg">
                                                            <div class="cs-intputwrap mb-3">
                                                                <i class="fa-light fa-comment"></i>
                                                                <textarea name="message" placeholder="Message" rows="6"
                                                                    class="form-control"></textarea>
                                                            </div>
                                                        </div>

                                                        <!-- CAPTCHA Section -->
                                                        <div class="col-md-4 mb-3">
                                                            <img id="captcha_visa"
                                                                src="<?= BASE_URL ?>captcha/captcha-visa.php"
                                                                alt="CAPTCHA" style="width: 100%; max-width: 190px;">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <input type="text" id="captcha_visa_text"
                                                                name="captcha_visa_text" class="form-control"
                                                                placeholder="Enter Captcha">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <button type="button"
                                                                class="commentssubmit commentssubmit_fw"
                                                                onclick="captcha_visa_refresh()">Regenerate
                                                                Captcha</button>
                                                        </div>

                                                        <!-- Hidden Phone Field and Submit -->
                                                        <div class="col-12 mt-4">
                                                            <input type="hidden" name="golden_visa_phone_hidden">
                                                            <button type="submit" name="golden_visa_submit"
                                                                class="commentssubmit commentssubmit_fw">Submit</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <!--<div class="log-separator py-3"><span>or</span></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- contacts-opt-wrap end-->
                            <!-- contacts-opt-wrap -->
                            <div class="col-lg-6">
                                <div class="map-container">
                                    <iframe
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3546.8034601615327!2d55.245715!3d25.113394299999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f69d069d937b1%3A0xe9a4d6fc4b56d9a0!2sDRE%20(Homes%20Real%20Estate)!5e1!3m2!1sen!2sin!4v1757570559029!5m2!1sen!2sin"
                                        width="100%" height="550" style="border:0;" allowfullscreen="" loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                            </div>
                            <!-- contacts-card-item end-->
                        </div>
                    </div>
                </div>
                <!--boxed-container end-->
            </div>
            <!--main-content end-->
            <div class="to_top-btn-wrap">
                <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                <div class="svg-corner svg-corner_white" style="top:0;left:  -40px; transform: rotate(-90deg)"></div>
                <div class="svg-corner svg-corner_white" style="top:0;right: -40px; transform: rotate(-180deg)"></div>
            </div>
        </div>
        <!-- container end-->
    </div>
    <!--content  end-->
    <?php include 'common/footer.php'; ?>