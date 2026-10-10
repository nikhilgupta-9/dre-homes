<?php 
include('db/config.php');

$detail = $_GET['detail'];

// Detect route type from URL (scalable approach)
$request_uri = $_SERVER['REQUEST_URI'];
$isManagement = (strpos($request_uri, '/management/') !== false);

// Set breadcrumb label dynamically
$sectionLabel = $isManagement ? 'Management' : 'Team';

$meta_tags = select_row('tbl_team', "team_slug='$detail' and is_active=1");

include('common/header.php');

$our_team = select_row('tbl_team_det', "is_active='1'");
$team_detail = select_row('tbl_team', "team_slug='".$detail."' and is_active='1'");

// Redirect if not active or not found
if (empty($team_detail) || $team_detail['is_active'] != 1) {
    header("Location: " . BASE_URL . "our-agents");
    exit();
}
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
                                <!-- optional heading -->
                            </div>
                        </div>
                    </div>

                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                        <div class="bg" data-bg="<?=BASE_URL_IMG?>team/<?=$our_team['team_img']?>" 
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
                <a href="<?=BASE_URL?>">Home</a>

                <a href="<?=BASE_URL?>our-team">
                    <?=$sectionLabel?>
                </a>

                <span><?=$team_detail['team_inner_heading']?></span>

                <div class="breadcrumbs-list_dec">
                    <i class="fa-thin fa-arrow-up"></i>
                </div>
            </div>
            <!--breadcrumbs-list end-->

            <!--main-content-->
            <div class="main-content ms_vir_height">

                <div class="boxed-container agent-box">
                    <div class="row">

                        <!-- left profile -->
                        <div class="col-md-4">
                            <div class="boxed-content ">
                                <div class="agent-preofile-wrap">

                                    <div class="agent-preofile-content">
                                        <img src="<?=BASE_URL_IMG?>team/<?=$team_detail['team_img']?>" 
                                             alt="<?=$team_detail['team_img_alt']?>" width="100%">
                                    </div>

                                    <?php
                                    if (!empty($team_detail['team_linkedinlink']) || !empty($team_detail['team_instagramlink'])) {
                                    ?>
                                        <div class="agent-preofile-footer">
                                            <div class="agent-preofile-footer_title">Follow:</div>

                                            <div class="agent-preofile-footer-social">
                                                <?php if (!empty($team_detail['team_linkedinlink'])) { ?>
                                                    <a href="<?= htmlspecialchars($team_detail['team_linkedinlink']) ?>" target="_blank">
                                                        <i class="fa-brands fa-linkedin"></i>
                                                    </a>
                                                <?php } ?>

                                                <?php if (!empty($team_detail['team_instagramlink'])) { ?>
                                                    <a href="<?= htmlspecialchars($team_detail['team_instagramlink']) ?>" target="_blank">
                                                        <i class="fa-brands fa-instagram"></i>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                </div>
                            </div>
                        </div>

                        <!-- right content -->
                        <div class="col-md-8">

                            <div class="list-main-wrap-header box-list-header" style="margin-top: 0">

                                <div class="list-main-wrap-title agent-title py-2">

                                    <h1><?= htmlspecialchars($team_detail['team_name']) ?></h1>
                                    <h3><?= htmlspecialchars($team_detail['team_desg']) ?></h3>

                                    <?php if (!empty($team_detail['team_orn'])): ?>
                                        <p>BRN: <?= htmlspecialchars($team_detail['team_orn']) ?></p>
                                    <?php endif; ?>

                                    <div class="agent-preofile-footer agent-preofile-footer-contact">
                                        <div class="agent-preofile-footer-social">

                                            <?php if (!empty($team_detail['team_mobile'])): ?>
                                                <a href="tel:<?= htmlspecialchars($team_detail['team_mobile']) ?>">
                                                    <i class="fa-solid fa-phone"></i> Call
                                                </a>
                                            <?php endif; ?>

                                            <?php if (!empty($team_detail['team_emailid'])): ?>
                                                <a href="mailto:<?= htmlspecialchars($team_detail['team_emailid']) ?>">
                                                    <i class="fa-regular fa-envelope"></i> Email
                                                </a>
                                            <?php endif; ?>

                                            <?php if (!empty($team_detail['team_whtsapp'])): ?>
                                                <a href="https://wa.me/<?= htmlspecialchars($team_detail['team_whtsapp']) ?>" target="_blank">
                                                    <i class="fa-brands fa-whatsapp"></i> WhatsApp
                                                </a>
                                            <?php endif; ?>

                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="listing-item-container fw-listing-item agent-text">
                                <?=$team_detail['team_cont']?>
                            </div>

                        </div>

                    </div>

                    <div class="limit-box"></div>
                </div>

            </div>
            <!--main-content end-->

            <div class="to_top-btn-wrap">
                <div class="to-top to-top_btn">
                    <span>Back to top</span> 
                    <i class="fa-solid fa-arrow-up"></i>
                </div>
            </div>

        </div>
        <!-- container end-->

    </div>
    <!--content end-->

<?php include 'common/footer.php'; ?>