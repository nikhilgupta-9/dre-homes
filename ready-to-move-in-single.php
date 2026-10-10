<?php
include('db/config.php');

$type=2;
$slug = str_replace('/', '', $_GET['detail']);
$proj_detail = select_project_detail($slug, $type);

if (empty($proj_detail)) {
    header('Location:' . BASE_URL . 'ready-to-move-in');
    exit;  // Stop further execution immediately after redirect
}

$meta_tags = $proj_detail;
$dev_detail = select_row('tbl_developer', 'dev_id=' . $proj_detail['prj_dev_id']);

include('common/header.php');
?>

<!--warpper-->
<div class="box-head-bg"></div>
<div class="wrapper">
    <div class="content">
        <div class="container">
            <div class="breadcrumbs-list bl_flat">
                <a href="<?= BASE_URL ?>">Home</a><a href="<?= BASE_URL ?>ready-to-move-in">Ready To Move In</a><span><?= $proj_detail['proj_name'] ?></span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
        </div>
        <!--single-carousle-container-->
        <div class="fw-carousel-container">
            <div class="fw-carousel-wrap ">
                <!-- fw-carousel  -->
                <div class="fw-carousel lightgallery">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <?php
                            $query = select_query('tbl_proj_gallery', 'proj_id = "' . $proj_detail['prj_id'] . '" AND is_active = 1');
                            while ($row = mysqli_fetch_array($query)) {
                                ?>
                                <div class="swiper-slide hov_zoom">
                                    <a href="<?= BASE_URL_IMG ?>project/gallery/<?= $row['gallery_img'] ?>" class="popup-image">
                                    <img src="<?= BASE_URL_IMG ?>project/gallery/<?= $row['gallery_img'] ?>"
                                    alt="<?= $row['gallery_img_alt'] ?>">
                                    </a>
                                    
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- fw-carousel end -->
            </div>
            <!-- fw-carousel-wrap end -->
            <div class="fw-carousel-button-prev slider-button"><i class="fa-solid fa-caret-left"></i></div>
            <div class="fw-carousel-button-next slider-button"><i class="fa-solid fa-caret-right"></i></div>
            <div class="fwc-controls_wrap">
                <div class="solid-pagination_btns fwc-pagination"></div>
            </div>
        </div>
        <!--single-carousle-container-->
        <div class="container">
            <div class="main-content">
                <div class="boxed-container">
                    <div class="row">
                        <div class="col-lg-8">
                            <!--boxed-container-->
                            <div class="scroll-content-wrap">
                                <div class="list-single-opt_header hsc_flat_bci">
                                    <div class="hero-section_categories">
                                        <a href="javascript:void(0)"><?= $proj_detail['proj_inventory_type'] ?></a>

                                    </div>

                                </div>
                                <!--boxed-content-->
                                <div class="boxed-content">
                                    <!--boxed-content-item-->
                                    <div class="boxed-content-item pd-title">
                                        <div class="hero-section-title_container hsc_flat">
                                            <div class="hero-section-title proj-head">
                                                <h1><?= $proj_detail['proj_name'] ?></h1>
                                               <h4><strong>Starting From:</strong>
        <span class="pshp_item">
               <?php if (is_numeric($proj_detail["proj_price"]) && $proj_detail["proj_price"] != 0): ?>
    <div class="price-row" style="display: flex; align-items: center; gap: 10px; ">
        <!-- Price Output -->
        <div class="geodir-category-content_price  currency-price"
             data-price="<?= $proj_detail["proj_price"] ?>"
             data-original-currency="AED"
             data-original-price="<?= $proj_detail["proj_price"] ?>">
            <img src="<?= BASE_URL ?>images/symbol/currency-symbol.png" alt="Currency"
                 style="height: 14px; vertical-align: middle;">
            AED <?= number_format($proj_detail["proj_price"]) ?>
        </div>
    </div>
<?php else: ?>
    <div class="geodir-category-content_price ">
        Call Us
    </div>
<?php endif; ?>

    </h4>


                                                <div
                                                    class="property-single-header-price property-dev-box d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span>Developer:</span> <a
                                                            href="<?= BASE_URL ?>developer/<?= $dev_detail['dev_slug'] ?>"><?= $dev_detail['dev_heading'] ?></a>
                                                    </div>
                                                    <div><a href="<?= BASE_URL ?>developer/<?= $dev_detail['dev_slug'] ?>"
                                                            class="post-card_link ms-auto">View Details <i
                                                                class="fa-solid fa-caret-right"></i></a></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--boxed-content-item end-->
                                </div>
                                <!--boxed-content end-->
                                <!--ps-facts-wrapper-->
                                <div class="ps-facts-wrapper">
                                    <!-- Location -->
                                    <?php if (!empty($proj_detail['proj_location'])): ?>
                                        <div class="ps-facts-item">
                                            <h4>Location</h4>
                                            <h5><?= htmlspecialchars($proj_detail['proj_location']) ?></h5>
                                            <i class="fa-light fa-location-dot"></i>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Bedroom -->
<?php if (!empty($proj_detail['proj_bedroom'])): ?>
    <div class="ps-facts-item">
        <h4>Bedroom</h4>
        <h5>
            <?php
                $items = array_filter(array_map('trim', explode(',', $proj_detail['proj_bedroom'])), 'strlen');

                $text = [];
                $numbers = [];

                foreach ($items as $item) {
                    if (is_numeric($item)) {
                        $numbers[] = $item;
                    } else {
                        $text[] = $item;
                    }
                }

                // Optional: sort numbers ascending
                sort($numbers, SORT_NUMERIC);

                echo htmlspecialchars(implode(', ', array_merge($text, $numbers)));
            ?>
        </h5>
        <i class="fa-light fa-bed"></i>
    </div>
<?php endif; ?>

                            
                            
                                    <!-- Payment plan -->
                                    <?php if (!empty($proj_detail['proj_pplan_cont'])): ?>
                                        <div class="ps-facts-item">
                                            <h4>Payment Plan</h4>
                                            <h5><?= htmlspecialchars($proj_detail['proj_pplan_cont']) ?></h5>
                                            <i class="fa-solid fa-credit-card"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <!--ps-facts-wrapper end-->
                                <!--boxed-content-->
                                <div class="boxed-content">
                                    <!--boxed-content-title-->
                                    <div class="boxed-content-title">
                                        <h3>About this Property</h3>
                                    </div>
                                    <!--boxed-content-title end-->

                                    <!--boxed-content-item-->
                                    <div class="boxed-content-item editor-cont">
                                        <?= $proj_detail['proj_overview'] ?>
                                        <div class="pp-single-opt-wrap">
                                            <div class="pp-single-opt-links">
                                               <?php if (!empty($proj_detail['proj_brochure']) || !empty($proj_detail['proj_payplan']) || !empty($proj_detail['proj_floorplan'])): ?>
    <ul>
        <?php if (!empty($proj_detail['proj_brochure'])): ?>
            <li class="modal-open">
                <a data-url="<?= $proj_detail['proj_brochure'] ?>">
                    <i class="fa-light fa-file-pdf"></i> Brochure
                </a>
            </li>
        <?php endif; ?>

        <?php if (!empty($proj_detail['proj_payplan'])): ?>
            <li class="modal-open">
                <a data-url="<?= $proj_detail['proj_payplan'] ?>">
                    <i class="fa-light fa-file-pdf"></i> Payment Plan
                </a>
            </li>
        <?php endif; ?>

        <?php if (!empty($proj_detail['proj_floorplan'])): ?>
            <li class="modal-open">
                <a data-url="<?= $proj_detail['proj_floorplan'] ?>">
                    <i class="fa-light fa-file-pdf"></i> Floor Plan
                </a>
            </li>
        <?php endif; ?>
    </ul>
<?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                    <!--boxed-content-item end-->
                                </div>

                                <?php
                                $payplan_query = select_query('tbl_proj_payplan', 'proj_id=' . $proj_detail['prj_id'] . ' and is_active=1');
                                $payplan_tot = mysqli_num_rows($payplan_query);
                                if ($payplan_tot > 0) {

                                    ?>


                                    <div class="boxed-content">
                                        <!--boxed-content-title-->
                                        <div class="boxed-content-title">
                                            <h3> Payment Plan</h3>
                                        </div>
                                        <!--boxed-content-title end-->
                                        <!--boxed-content-item-->
                                        <div class="boxed-content-item property-pp">
                                            <div class="process-wrap">
                                                <ul>

                                                    <?php
                                                    while ($row = mysqli_fetch_array($payplan_query)) {
                                                        ?>

                                                        <li>
                                                            <div class="process-item ">
                                                                <h4 class="process-title text-left"><?= $row['payplan_heading'] ?>
                                                                </h4>
                                                                <span
                                                                    class="process-count"><?= $row['payplan_percentage'] ?></span>
                                                            </div>

                                                            <span class="pr-dec"></span>
                                                        </li>

                                                    <?php } ?>

                                                </ul>
                                            </div>

                                        </div>
                                        <!--boxed-content-item end-->
                                    </div>

                                <?php } ?>


                                <div class="boxed-content">
                                    <!--boxed-content-title-->
                                    <div class="boxed-content-title">
                                        <h3> Property Amenities</h3>
                                    </div>
                                    <!--boxed-content-title end-->
                                    <!--boxed-content-item-->
                                    <div class="boxed-content-item">
                                        <div class="pp-single-opt">
                                            <div class="pp-single-features ">
                                          <ul>
    <?php
    $feat_fac_query = select_query('tbl_proj_featfac', 'is_active=1');
    $feat_fac_arr = explode(',', $proj_detail['proj_fea_fac_opt']);
    while ($row = mysqli_fetch_array($feat_fac_query)) {
        if (in_array($row['featfac_id'], $feat_fac_arr)) {
            ?>
            <li>
                <a href="#">
                    <i class="fa <?= htmlspecialchars($row['featfac_faicon']) ?> me-2"></i>
                    <?= htmlspecialchars($row['featfac_name']) ?>
                </a>
            </li>
        <?php }
    } ?>
</ul>


                                            </div>
                                        </div>
                                    </div>
                                    <!--boxed-content-item end-->
                                </div>

                                <!--boxed-content-->
                                <div class="boxed-content">
                                    <!--boxed-content-title-->
                                    <div class="boxed-content-title">
                                        <h3>Property Location </h3>
                                    </div>
                                    <!--boxed-content-title end-->
                                    <!--boxed-content-item-->
                                    <div class="boxed-content-item">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <?php if (!empty($proj_detail['proj_map'])) { ?>

                                                    <iframe src="<?= $proj_detail['proj_map'] ?>" width="100%" height="450"
                                                        style="border:0;" allowfullscreen="" loading="lazy"
                                                        referrerpolicy="no-referrer-when-downgrade"></iframe> <?php } ?>


                                            </div>
                                        </div>
                                    </div>
                                    <!--boxed-content-item end-->
                                </div>
                                <!--boxed-content end-->
                                <!--boxed-content-->
                                <?php if ($proj_detail['proj_masterplan'] != '') { ?>

                                    <div class="boxed-content">
                                        <!--boxed-content-title-->
                                        <div class="boxed-content-title">
                                            <h3>Property Masterplan</h3>
                                        </div>
                                        <!--boxed-content-title end-->

                                        <!--boxed-content-item-->
                                        <div class="boxed-content-item">

                                            <div class="">
                                                <?= $proj_detail['proj_masterplan'] ?>
                                            </div>
                                        </div>
                                        <!--boxed-content-item end-->
                                    </div>

                                <?php } ?>



                            </div>
                        </div>
                        <div class="col-lg-4">
                            <!--boxed-container-->
                            <div class="sb-container">
                                <!--boxed-content-->
                                <div class="fixed-form-wrap">
                                    <div class="fixed-form">
                                        <div class="boxed-content">

                                            <!--boxed-content-item-->
                                            <div class="boxed-content-item p-4">
                                                <p class="form-heading">Fill the form. And book a viewing</p>
                                                <div class="custom-form" id="single_cf">
                                                    <?php include_once "common/sidebare-form.php";?>
                                                    <!--<div class="log-separator py-3"><span>or</span></div>-->

                                                </div>
                                                <!-- <div class="property-contacts-wrap buy-p-wrap">
                                                    <div class="property-contacts-item sh-links">
                                                        <div
                                                            class="property-contacts_profile d-flex align-items-center">
                                                            <div class=" py-1">
                                                                <a href="javascript:void(0)"
                                                                    class="property-contacts_profile_link">
                                                                    <img src="images/avatar/2.jpg" alt="">
                                                                </a>
                                                            </div>
                                                            <div class="text-left ps-2 buy-p-wrap-h py-1">
                                                                <h3>Dummy Name</h3>
                                                                <h6>Sales Consultant</h6>
                                                            </div>
                                                            <div class="list-contact-icon ms-auto py-1">
                                                                <a href="tel:+18475555555" target="_blank" class=""
                                                                    data-microtip-position="right"><img
                                                                        src="images/c-icon/call-icon.png"
                                                                        alt="Call Icon" width="100%" title="Call">
                                                                </a>
                                                                <a href="https://wa.me/971527543243" target="_blank"
                                                                    class="" data-microtip-position="right"><img
                                                                        src="images/c-icon/whatsapp-icon.png"
                                                                        alt="Whatsapp Icon" width="100%"
                                                                        title="Whatsapp">
                                                                </a>
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div> -->
                                            </div>
                                            <!--boxed-content-item end-->
                                        </div>
                                    </div>
                                </div>
                                <!--boxed-content end-->
                            </div>
                        </div>
                    </div>
                    <div class="limit-box"></div>
                </div>
                <?php
                // Similar properties worked out by ai/cron/build_similar.php, with the old list as fallback.
                require_once __DIR__ . '/ai/SimilarProjects.php';
                $prj_query = SimilarProjects::forPage($proj_detail['prj_id'], 2);
                if (!$prj_query) {
                    $prj_query = db_query("SELECT * FROM tbl_proj WHERE prj_dev_id = ? AND proj_slug != ? AND is_active = 1 ORDER BY proj_posted_on DESC", [(int) $proj_detail['prj_dev_id'], (string) $proj_detail['proj_slug']]);
                }
                if(mysqli_num_rows($prj_query) > 0){
                ?>
                <div class="boxed-container proj-sim-box">
                    <div class="boxed-content-title bcst_ca">
                        <h3>More Like This</h3>
                    </div>
                    <div class="single-carousel-wrap">
                        <div class="single-carousel equal-elc">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">

                                    <?php
                                    $key = 0;
                                    while ($row = mysqli_fetch_array($prj_query)) {
                                        $proj_dev = select_row(
                                            "tbl_developer",
                                            "dev_id=" . $row["prj_dev_id"] . ""
                                        );
                                        $prj_thumb_arr = explode(',', $row['prj_thumb_img']);

                                        if (count($prj_thumb_arr) > 1) {
                                            $prj_thumb_img = $prj_thumb_arr[0];
                                        } else {
                                            $prj_thumb_img = $row['prj_thumb_img'];
                                        }
                                        ?>

                                        <div class="swiper-slide">
                                            <!-- listing-item -->
                                            <div class="listing-item">
                                                <div class="geodir-category-listing">
                                                    <div class="geodir-category-img">
                                                        <a href="<?= BASE_URL ?>property/<?= $row['proj_slug'] ?>"
                                                            class="geodir-category-img_item">
                                                            <div class="off-plan-thumb">
                                                                <img src="<?= BASE_URL_IMG ?>project/thumb/<?= $prj_thumb_img ?>"
                                                                    alt="">
                                                            </div>
                                                            <div class="overlay"></div>
                                                        </a>
                                                        <div class="geodir-category-location">
                                                            <a href="<?= BASE_URL ?>property/<?= $row['proj_slug'] ?>"
                                                                class="map-item"><i class="fas fa-map-marker-alt"></i>
                                                                <?= $row['proj_location'] ?></a>
                                                        </div>
                                                        <ul class="list-single-opt_header_cat">
                                                                <li><a href="javascript:void(0)" class="cat-opt">
                                                                        <?= $row['proj_pplan_cont'] ?></a>
                                                                </li>
                                                        </ul>
                                                    </div>
                                                    <div class="geodir-category-content prop-gcc">
                                                        <h3><a
                                                                href="<?= BASE_URL ?>property/<?= $row['proj_slug'] ?>"><?= $row['proj_name'] ?></a>
                                                        </h3>
                                                    <?php if (is_numeric($row["proj_price"]) && $row["proj_price"] != 0): ?>
                                                        <div class="price-row" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                                                            <!-- Price Output -->
                                                            <div class="geodir-category-content_price text-muted currency-price"
                                                                data-price="<?= $row["proj_price"] ?>"
                                                                data-original-currency="AED"
                                                                data-original-price="<?= $row["proj_price"] ?>">
                                                                <img src="<?= BASE_URL ?>images/symbol/currency-symbol.png" alt="Currency"
                                                                    style="height: 14px; vertical-align: middle;">
                                                                AED <?= number_format($row["proj_price"]) ?>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="geodir-category-content_price text-muted">
                                                            Call Us
                                                        </div>
                                                    <?php endif; ?>

                                                    </div>
                                                    <div class="geodir-category-footer list-footer">
                                                        <!--<a href="<?= BASE_URL ?>property/<?= $row['proj_slug'] ?>"-->
                                                        <!--    class="gcf-company">-->
                                                        <!--    <img src="<?= BASE_URL_IMG ?>project/dev/<?= $row['proj_dev_img'] ?>"-->
                                                        <!--        alt=""></a>-->
                                                        <a href="<?= BASE_URL ?>property/<?= $row['proj_slug'] ?>" class="gcf-company post-card_link-2">View<i class="fa-solid fa-caret-right"></i></a>
                                                        <div class="clearfix"></div>
                                                        <div class="list-contact-icon">
                                                            <a href="tel:<?= $row['proj_mobileno'] ?>" target="_blank"
                                                                class=" pcl_btn" data-microtip-position="right"><img
                                                                    src="<?= BASE_URL ?>images/c-icon/call-icon.png"
                                                                    alt="Call Icon" width="100%" title="Call"></a>
                                                            <a href="https://wa.me/<?= $row['proj_whatsaapno'] ?>"
                                                                target="_blank" class=" pcl_btn"
                                                                data-microtip-position="right"><img
                                                                    src="<?= BASE_URL ?>images/c-icon/whatsapp-icon.png"
                                                                    alt="Whatsapp Icon" width="100%" title="Whatsapp"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!-- listing-item end-->
                                        </div>

                                        <?php $key++;
                                    } ?>
                                </div>
                            </div>
                            <div class="ss-carousel-button ss-carousel-button-prev"><i
                                        class="fas fa-caret-left"></i>
                            </div>
                            <div class="ss-carousel-button ss-carousel-button-next"><i
                                        class="fas fa-caret-right"></i>
                            </div>
                        </div>
                    </div>

                </div>
                <?php } ?>
            </div>

        </div>
        <!-- container end-->
    </div>
    <!--content  end-->
    
    <!--register form -->
<div class="main-register-container">
    <div class="main-register_box">
        <div class="main-register-holder">
            <div class="main-register-wrap ">
                <div class="main-register_bg">
                    <div class="mr_title">
                        <h4>Welcome to Drehomes</h4>
                        <h5>Premium Real Estate Agency</h5>
                    </div>
                    <div class="main-register_contacts-wrap">
                        <h4>Have a questions?</h4>
                        <a href="<?= BASE_URL ?>contact-us.php"> Get in Touch</a>
                        <div class="svg-corner svg-corner_white" style="bottom:0;left:  -39px;"></div>
                        <div class="svg-corner svg-corner_white"
                            style="bottom:0;right:  -39px;transform: rotate(90deg)"></div>
                    </div>
                    <div class="main-register_bg-dec"></div>
                </div>
                <div class="main-register tabs-act fl-wrap">
                  <ul class="tabs-menu">
    <?php if (!empty($proj_detail['proj_brochure'])): ?>
        <li class="current">
            <a href="#tab-1"><i class="fa-light fa-download"></i>Brochure</a>
        </li>
    <?php endif; ?>

    <?php if (!empty($proj_detail['proj_payplan'])): ?>
        <li>
            <a href="#tab-2"><i class="fa-light fa-download"></i>Payment Plan</a>
        </li>
    <?php endif; ?>

    <?php if (!empty($proj_detail['proj_floorplan'])): ?>
        <li>
            <a href="#tab-3"><i class="fa-light fa-download"></i>Floor Plan</a>
        </li>
    <?php endif; ?>
</ul>

                    
                    <!--tabs -->
<div id="tabs-container">
    <div class="tab">
        <!-- Tab 1: Brochure -->
        <div id="tab-1" class="tab-content first-tab">
            <div class="custom-form">
                <form method="post" name="brochureform" id="brochure-frm">
                    <div class="cs-intputwrap">
                        <i class="fa-light fa-user"></i>
                        <input type="text" placeholder="Name" name="brochure_name" required>
                    </div>
                    <div class="cs-intputwrap">
                        <i class="fa-light fa-envelope"></i>
                        <input type="email" placeholder="Email Address" name="brochure_email" required>
                    </div>
                    <div class="cs-intputwrap">
                        <input type="text" placeholder="Phone Number" id="brochure_phone" name="brochure_phone" required>
                    </div>
                    <input type="hidden" name="download_type" value="Brochure">
                    <input type="hidden" name="page_url" value="<?= current_url() ?>">
                    <input type="hidden" name="brochure_number_hidden" value="">
                    <input type="hidden" name="brochure_url" value="<?= $proj_detail['proj_brochure'] ?>">
                    <button type="submit" class="commentssubmit" data-url="<?= $proj_detail['proj_brochure'] ?>" name="brochure_submit">Download Brochure</button>
                </form>
            </div>
        </div>

        <!-- Tab 2: Payment Plan -->
        <div id="tab-2" class="tab-content">
            <div class="custom-form">
                <form method="post" name="payplanform" id="payplan-frm">
                    <div class="cs-intputwrap">
                        <i class="fa-light fa-user"></i>
                        <input type="text" placeholder="Name" name="payplan_name" >
                    </div>
                    <div class="cs-intputwrap">
                        <i class="fa-light fa-envelope"></i>
                        <input type="email" placeholder="Email Address" name="payplan_email" >
                    </div>
                    <div class="cs-intputwrap">
                        <input type="text" placeholder="Phone Number" id="payplan_phone" name="payplan_phone" >
                    </div>
                    <input type="hidden" name="download_type" value="Pay Plan">
                    <input type="hidden" name="page_url" value="<?= current_url() ?>">
                    <input type="hidden" name="payplan_number_hidden" value="">
                    <input type="hidden" name="payplan_url" value="<?= $proj_detail['proj_payplan'] ?>">
                    <button type="submit" class="commentssubmit" data-url="<?= $proj_detail['proj_payplan'] ?>" name="payplan_submit">Download Payplan</button>
                </form>
            </div>
        </div>

        <!-- Tab 3: Floor Plan -->
<!-- Tab 3: Floor Plan -->
<div id="tab-3" class="tab-content">
    <div class="custom-form">
        <form method="post" name="floorplanform" id="floorplan-frm">
            <div class="cs-intputwrap">
                <i class="fa-light fa-user"></i>
                <input type="text" placeholder="Name" name="floorplan_name">
            </div>
            <div class="cs-intputwrap">
                <i class="fa-light fa-envelope"></i>
                <input type="email" placeholder="Email Address" name="floorplan_email">
            </div>
            <div class="cs-intputwrap">
                <input type="text" placeholder="Phone Number" id="floorplan_phone" name="floorplan_phone">
            </div>
            <input type="hidden" name="download_type" value="Floor Plan">
            <input type="hidden" name="page_url" value="<?= current_url() ?>">
            <input type="hidden" name="floorplan_number_hidden" value="">
            <input type="hidden" name="floorplan_url" value="<?= $proj_detail['proj_floorplan'] ?>">
            <button type="submit" class="commentssubmit" data-url="<?= $proj_detail['proj_floorplan'] ?>" name="floorplan_submit">
                <span>Download Floor Plan</span>
            </button>
        </form>
    </div>
</div>

        <button type="button" class="commentssubmit close-modal close-reg-form">Close</button>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
    <div class="body-overlay fs-wrapper reg-overlay close-reg-form"></div>
</div>
<!--register form end -->

    <?php include 'common/footer.php'; ?>