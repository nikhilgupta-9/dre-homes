<?php
include('db/config.php');
$meta_tags = select_row('tbl_tags', 'id=38 and is_active=1');
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
                                <h1>Terms & Conditions</h1>
                            </div>
                        </div>
                    </div>
                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                        <div class="bg" data-bg="images/bg/privacy-policy-bg.jpg"
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
                <a href="<?= BASE_URL ?>">Home</a><span>Terms & Conditions</span>
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
                                            <h3>Terms & Conditions</h3>
                                        </div>
                                        <div class="boxed-content-item">

                                            <div class="text-block post-single_tb">

                                                <div class="text-block-container">

                                                    <div class="privacy-box mt-3">

                                                        <div class="my-4">
                                                            <p>
                                                                Please be advised that accessing or using this website implies the user's consent to abide by the following terms and conditions:

                                                                </p>
                                                                <p><b>Intellectual Property</b></p>
                                                                <p>All content, images, logos, property listings, and other materials on this website are the property of DRE Homes Real Estate Broker LLC, respective project developers, or their licensors. These materials are protected under applicable intellectual property laws, and users are strictly prohibited from copying, altering, distributing, or utilizing any content without obtaining prior written permission.</p>

                                                                <p><b>Property Information</b></p>
                                                                <p>The content of this website, including property details, prices, payment plans, and availability, is subject to change at any time without prior notice. The Company shall not be held responsible for any inaccuracies, omissions, or decisions made based on the information provided.</p>

                                                                <p><b>User Obligations</b></p>
                                                                <p>Users are required to provide accurate and current information when submitting inquiries or requests via the website. Users must refrain from engaging in unlawful or harmful activities, including hacking, spamming, unauthorized access, or transmitting malicious software.</p>

                                                                <p><b>Privacy and Data Collection</b></p>
                                                                <p>The Company may collect, store, and use your personal information in accordance with its Privacy Policy. We may also use third-party services or tracking technologies such as cookies and analytics tools to understand usage behavior and improve our services.</p>

                                                                <p><b>Disclaimer of Liability</b></p>
                                                                <p>The Company disclaims liability for any damages, losses, or claims arising from the use of the website or reliance on the provided information. This includes, but is not limited to, financial losses, investment decisions, property-related matters, or any direct or indirect damages.</p>

                                                                <p><b>Links to Third-Party Websites</b></p>
                                                                <p>This website may contain links to external websites or resources. We do not endorse or assume responsibility for the content, privacy practices, or availability of such third-party websites.
</p>

                                                                <p><b>Modifications to the Terms</b></p>
                                                                <p>The Company may update or modify these Terms and Conditions at any time without prior notice. Continued use of the website constitutes acceptance of the revised terms.</p>
                                                                

                                                                <p style="font-size:18px; margin-top:15px;"><b>Governing Law & Dispute Resolution</b></p>
                                                                <p><b>Governing Law</b></p>
                                                                <p>Your access to and use of this Website, together with these Terms, shall be subject to and construed in accordance with the applicable laws of the Emirate of Dubai and the federal laws of the United Arab Emirates. This applies to any dispute or claim regardless of whether it arises under contract or otherwise.</p>
                                                                
                                                                <p><b>Jurisdiction</b></p>
                                                                <p>All disputes relating to the Website or these Terms shall be submitted to the competent courts of Dubai, United Arab Emirates, which shall have primary jurisdiction. The Company also reserves the right to commence legal proceedings in any other jurisdiction where a user resides or where a violation of these Terms has occurred. By using this Website, you agree to submit to such jurisdiction and waive any objection to the venue or legal authority of those courts.</p>
                                                        </div>

                                                    </div>

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