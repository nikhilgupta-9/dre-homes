<?php
include('db/config.php');

#$proj_detail = select_project_detail(str_replace('/', '', $_GET['detail']));
$type = 8;
$proj_detail = select_project_detail(str_replace('/', '', $_GET['detail']), $type);
if (empty($proj_detail)) {
    header('Location:' . BASE_URL . 'rent-property');
    exit();
}

$meta_tags = $proj_detail;
include('common/header.php');

$dev_detail = null;

if (!empty($proj_detail['prj_dev_id']) && is_numeric($proj_detail['prj_dev_id'])) {
    $dev_detail = select_row('tbl_developer', 'dev_id=' . (int) $proj_detail['prj_dev_id']);
}

$images = [];
if (!empty($proj_detail) && isset($proj_detail['prj_id'])) {
    $query = select_query('tbl_proj_gallery', 'proj_id = "' . $proj_detail['prj_id'] . '" AND is_active = 1');
    while ($row = mysqli_fetch_array($query)) {
        $images[] = $row;
    }
}
?>
<style>
    .gallery-layout {
        display: flex;
    }

    .left-gallery {
        height: 500px;
        /* Any height you want */
        display: grid;
        grid-template-rows: 1fr 1fr;
        gap: 12px;
    }

    .gallery-img {
        position: relative;
        overflow: hidden;
        border-radius: 18px;
    }

    .gallery-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .right-gallery {
        height: 500px;
        /* SAME as left-gallery */
        overflow: hidden;
        border-radius: 18px;
    }

    .right-gallery img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .gallery-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, .45);
        display: flex;
        justify-content: center;
        align-items: center;
        color: #fff;
        font-size: 20px;

    }

    @media(max-width:991px) {

        .gallery-layout {
            display: block;
        }

        .left-gallery,
        .right-gallery {
            height: auto;
        }

        .gallery-img,
        .right-gallery {
            aspect-ratio: 16/10;
        }

        .right-gallery {
            margin-bottom: 12px;
        }

        .left-col {
            order: 2;
        }

        .right-col {
            order: 1;
        }
    }
</style>

<!--warpper-->
<div class="box-head-bg"></div>
<div class="wrapper">
    <div class="content">
        <div class="container">
            <div class="breadcrumbs-list bl_flat">
                <a href="<?= BASE_URL ?>">Home</a>
                <a href="<?= BASE_URL ?>rent-property">Rent Property</a>
                <span><?= $proj_detail['proj_name'] ?></span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
        </div>
        <!--section-->
        <div class="section hero-section">
            <div class="boxed-content">


                <!--boxed-content-item-->
                <div class="boxed-content-item">
                    <div class="container-fluid">

                        <?php if (!empty($images)): ?>
                            <?php $total = count($images); ?>

                            <div class="row gallery-layout">

                                <!-- RIGHT LARGE IMAGE -->
                                <div class="col-lg-8 px-1 right-col">
                                    <div class="right-gallery">

                                        <?php if (isset($images[0])): ?>
                                            <a data-fancybox="dre-gallery"
                                                href="<?= BASE_URL_IMG ?>project/gallery/<?= $images[0]['gallery_img'] ?>"
                                                class="dre-gallery-item">

                                                <img src="<?= BASE_URL_IMG ?>project/gallery/<?= $images[0]['gallery_img'] ?>"
                                                    alt="<?= htmlspecialchars($proj_detail['proj_name']) ?>">
                                            </a>
                                        <?php endif; ?>

                                    </div>
                                </div>

                                <!-- LEFT SMALL IMAGES -->
                                <div class="col-lg-4 px-1 left-col">
                                    <div class="left-gallery">

                                        <?php if (isset($images[1])): ?>
                                            <div class="gallery-img">
                                                <a data-fancybox="dre-gallery"
                                                    href="<?= BASE_URL_IMG ?>project/gallery/<?= $images[1]['gallery_img'] ?>"
                                                    class="dre-gallery-item">

                                                    <img src="<?= BASE_URL_IMG ?>project/gallery/<?= $images[1]['gallery_img'] ?>"
                                                        alt="<?= htmlspecialchars($proj_detail['proj_name']) ?>">
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($images[2])): ?>
                                            <div class="gallery-img">
                                                <div class="grid-item-holder <?= ($total > 3) ? 'grid-item-holder-ovr' : '' ?>">
                                                    <a data-fancybox="dre-gallery"
                                                        href="<?= BASE_URL_IMG . 'project/gallery/' . $images[2]['gallery_img'] ?>"
                                                        class="dre-gallery-item dre-gallery-more">
                                                        <img src="<?= BASE_URL_IMG . 'project/gallery/' . $images[2]['gallery_img'] ?>"
                                                            class="dre-gallery-img" />

                                                        <?php if ($total > 3): ?>
                                                            <span class="dre-gallery-overlay">
                                                                +<?= ($total - 3) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </a>

                                                </div>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>

                            </div>

                            <!-- Hidden images for Fancybox -->
                            <?php for ($i = 3; $i < $total; $i++): ?>
                                <a data-fancybox="dre-gallery"
                                    href="<?= BASE_URL_IMG ?>project/gallery/<?= $images[$i]['gallery_img'] ?>"
                                    data-thumb="<?= BASE_URL_IMG ?>project/gallery/<?= $images[$i]['gallery_img'] ?>"
                                    style="display:none;"></a>
                            <?php endfor; ?>

                        <?php else: ?>

                            <div class="text-center py-5">
                                No gallery images available.
                            </div>

                        <?php endif; ?>

                    </div>
                </div>
                <!--boxed-content-item end-->


            </div>
        </div>
        <!--section-end-->
        <div class="container">
            <div class="main-content">
                <div class="boxed-container">
                    <div class="row buy-property-box">
                        <div class="col-12 buy-property-title text-left">
                            <h1><?= $proj_detail['proj_name'] ?></h1>
                            <?php if (!empty($proj_detail['proj_sub_heading'])): ?>
                                <h2><?= htmlspecialchars($proj_detail['proj_sub_heading']) ?></h2>
                            <?php endif; ?>
                            <h4><strong>Starting From:</strong>
                                <span class="pshp_item">
                                    <?php if (is_numeric($proj_detail["proj_price"]) && $proj_detail["proj_price"] != 0): ?>
                                        <div class="price-row"
                                            style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                                            <!-- Price Output -->
                                            <div class="geodir-category-content_price  currency-price"
                                                data-price="<?= $proj_detail["proj_price"] ?>" data-original-currency="AED"
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

                                    <?php if (!empty($proj_detail["proj_price_type"])): ?>
                                        <span class="price-type">
                                            / <?= htmlspecialchars($proj_detail["proj_price_type"]) ?>
                                        </span>
                                    <?php endif; ?>

                            </h4>


                        </div>
                        <div class="col-xl-8">
                            <!--boxed-container-->
                            <div class="scroll-content-wrap">

                                <!--ps-facts-wrapper-->
                                <div class="ps-facts-wrapper">

                                    <!--ps-facts-item-->
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


                                    <!--ps-facts-item-->
                                    <?php if (!empty($proj_detail['proj_shower'])): ?>
                                        <div class="ps-facts-item">
                                            <h4>Bathroom</h4>
                                            <h5><?= htmlspecialchars($proj_detail['proj_shower']) ?></h5>
                                            <i class="fa-light fa-bath"></i>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($proj_detail['proj_area'])): ?>
                                        <div class="ps-facts-item">
                                            <h4>Area</h4>
                                            <h5><?= htmlspecialchars($proj_detail['proj_area']) ?> sq ft</h5>
                                            <i class="fa-light fa-chart-area"></i>
                                        </div>
                                    <?php endif; ?>

                                    <!--ps-facts-item end-->
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

                                    </div>
                                    <!--boxed-content-item end-->
                                </div>
                                <!--boxed-content end-->
                                <!--boxed-content-->

                                <!--boxed-content-->
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
                                                                <a href="javascript:void(0)">
                                                                    <i
                                                                        class="fa <?= htmlspecialchars($row['featfac_faicon']) ?> me-2"></i>
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
                                <?php if (!empty($proj_detail['proj_map'])): ?>
                                    <div class="boxed-content">
                                        <!--boxed-content-title-->
                                        <div class="boxed-content-title">
                                            <h3>Property Location</h3>
                                        </div>
                                        <!--boxed-content-title end-->
                                        <!--boxed-content-item-->
                                        <div class="boxed-content-item">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="map-container mapC_vis2">
                                                        <iframe src="<?= $proj_detail['proj_map'] ?>" width="100%"
                                                            height="450" style="border:0;" allowfullscreen="" loading="lazy"
                                                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--boxed-content-item end-->
                                    </div>
                                <?php endif; ?>
                                <!--boxed-content end-->
                                <!--boxed-content-->
                                <?php if (!empty($proj_detail['proj_qr_img']) || !empty($proj_detail['proj_qr'])): ?>
                                    <div class="boxed-content">
                                        <!--boxed-content-item-->
                                        <div class="boxed-content-item qr-box">
                                            <div class="row text-left justify-content-center align-items-center ">
                                                <?php if (!empty($proj_detail['proj_qr_img'])): ?>
                                                    <div class="col-sm-3 py-2 ">
                                                        <a
                                                            href="<?= !empty($proj_detail['proj_qr_link']) ? $proj_detail['proj_qr_link'] : 'javascript:void(0)' ?>">
                                                            <img src="<?= BASE_URL_IMG ?>project/qr/<?= htmlspecialchars($proj_detail['proj_qr_img']) ?>"
                                                                alt="QR Code" width="100%">
                                                        </a>
                                                    </div>

                                                <?php endif; ?>
                                                <?php if (!empty($proj_detail['proj_qr'])): ?>
                                                    <div class="col-sm-9 py-1 editor-cont">
                                                        <?= $proj_detail['proj_qr'] ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <!--boxed-content-item end-->
                                    </div>
                                <?php endif; ?>
                                <!--boxed-content end-->


                                <!-- MORTGAGE CALCULATOR SCRIPT -->
                                <script>
                                    function estimatePayment() {
                                        const purchasePrice = parseFloat(document.getElementById("purchase_price").value) || 0;
                                        const downPaymentPercent = parseFloat(document.getElementById("down_payment").value) || 0;
                                        const loanTermYears = parseFloat(document.getElementById("loan_term").value) || 0;
                                        const annualInterestRate = parseFloat(document.getElementById("interest_rate").value) || 0;

                                        // Calculate down payment and loan amount
                                        const downPaymentAmount = purchasePrice * (downPaymentPercent / 100);
                                        const loanAmount = purchasePrice - downPaymentAmount;

                                        // Calculate monthly interest and payments
                                        const monthlyInterestRate = (annualInterestRate / 100) / 12;
                                        const numberOfPayments = loanTermYears * 12;

                                        let monthlyPayment = 0;
                                        if (monthlyInterestRate === 0) {
                                            monthlyPayment = loanAmount / numberOfPayments;
                                        } else {
                                            monthlyPayment = loanAmount * (monthlyInterestRate * Math.pow(1 + monthlyInterestRate, numberOfPayments)) /
                                                (Math.pow(1 + monthlyInterestRate, numberOfPayments) - 1);
                                        }

                                        // Format output
                                        const formatAED = num => "" + num.toLocaleString('en-AE', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });

                                        // Display results
                                        document.getElementById("down_payment_value").innerText = formatAED(downPaymentAmount);
                                        document.getElementById("monthly_payment_value").innerText = formatAED(monthlyPayment);
                                        document.getElementById("load_amount_value").innerText = formatAED(loanAmount);
                                    }
                                </script>
                                <!--boxed-content end-->



                                <!--boxed-content-->
                                <div class="boxed-content p-manage-box">
                                    <div class="boxed-content-item">
                                        <div class="row text-left">
                                            <div class="col-12">
                                                <p class="m-head"><i class="fa-solid fa-screwdriver-wrench pe-2"></i>
                                                    Home
                                                    Repairs Made Easy</p>
                                                <p>We take care of everything — quick, reliable, and hassle-free. <a
                                                        href="<?= BASE_URL ?>property-management.php">Reach out
                                                        now!</a></p>
                                            </div>
                                        </div>
                                    </div>
                                    <!--boxed-content-item end-->
                                </div>
                                <!--boxed-content end-->
                            </div>
                        </div>
                        <div class="col-xl-4">
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
                                                    <div class="custom-form" id="single_cf">
                                                        <?php include_once "common/sidebare-form.php"; ?>
                                                        <!--<div class="log-separator py-3"><span>or</span></div>-->

                                                    </div>
                                                    <!--<div class="log-separator py-3"><span>or</span></div>-->

                                                </div>
                                                <!--<div class="property-contacts-wrap buy-p-wrap">-->
                                                <!--    <div class="property-contacts-item sh-links">-->
                                                <!--        <div-->
                                                <!--            class="property-contacts_profile d-flex align-items-center">-->
                                                <!--            <div class=" py-1">-->
                                                <!--                <a href="javascript:void(0)" class="property-contacts_profile_link">-->
                                                <!--                        <img src="<?= BASE_URL ?>images/avatar/2.jpg" alt="">-->
                                                <!--                </a>-->
                                                <!--            </div>-->
                                                <!--            <div class="text-left ps-2 buy-p-wrap-h py-1">-->
                                                <!--                <h3>Dummy Name</h3>-->
                                                <!--                <h6>Sales Consultant</h6>-->
                                                <!--            </div>-->
                                                <!--            <div class="list-contact-icon ms-auto py-1">-->
                                                <!--                <a href="tel:+18475555555" target="_blank" class=""-->
                                                <!--                    data-microtip-position="right"><img-->
                                                <!--                        src="<?= BASE_URL ?>images/c-icon/call-icon.png"-->
                                                <!--                        alt="Call Icon" width="100%" title="Call">-->
                                                <!--                </a>-->
                                                <!--                <a href="https://wa.me/971527543243" target="_blank"-->
                                                <!--                    class="" data-microtip-position="right"><img-->
                                                <!--                        src="<?= BASE_URL ?>images/c-icon/whatsapp-icon.png"-->
                                                <!--                        alt="Whatsapp Icon" width="100%"-->
                                                <!--                        title="Whatsapp">-->
                                                <!--                </a>-->
                                                <!--            </div>-->
                                                <!--        </div>-->

                                                <!--    </div>-->

                                                <!--</div>-->
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
                $prop_query = select_query("tbl_proj", "prj_id!=" . $proj_detail['prj_id'] . " and prj_type_id=8 and is_active=1 order by proj_name");
                $proj_count = mysqli_num_rows($prop_query);
                if ($proj_count > 0): ?>


                    <div class="boxed-container">
                        <div class="boxed-content-title bcst_ca">
                            <h3>More Like This</h3>
                        </div>
                        <div class="single-carousel-wrap">
                            <div class="single-carousel equal-elc">
                                <div class="swiper-container">
                                    <div class="swiper-wrapper">
                                        <?php while ($row = mysqli_fetch_array($prop_query)):
                                            $proj_dev = null;

                                            if (!empty($row["prj_dev_id"]) && is_numeric($row["prj_dev_id"])) {
                                                $proj_dev = select_row("tbl_developer", "dev_id=" . (int) $row["prj_dev_id"]);
                                            }
                                            $prj_thumb_arr = explode(",", $row["prj_thumb_img"]);
                                            $prj_thumb_img = count($prj_thumb_arr) > 1 ? $prj_thumb_arr[0] : $row["prj_thumb_img"];
                                            ?>
                                            <div class="swiper-slide">
                                                <div class="testi-item">
                                                    <div class="listing-item">
                                                        <div class="geodir-category-listing">
                                                            <div class="geodir-category-img">
                                                                <a href="<?= BASE_URL ?>rent-property/<?= $row["proj_slug"] ?>"
                                                                    class="geodir-category-img_item">
                                                                    <div class="sm-prop-thumb">
                                                                        <img src="<?= BASE_URL_IMG ?>project/thumb/<?= $prj_thumb_img ?>"
                                                                            alt="">
                                                                    </div>
                                                                    <div class="overlay"></div>
                                                                </a>
                                                                <div class="geodir-category-location">
                                                                    <a href="javascript:void(0)" class="map-item">
                                                                        <i class="fas fa-map-marker-alt"></i>
                                                                        <?= $row['proj_location'] ?>
                                                                    </a>
                                                                </div>

                                                            </div>

                                                            <div class="geodir-category-content prop-gcc eq-ns">
                                                                <h3><a
                                                                        href="<?= BASE_URL ?>rent-property/<?= $row["proj_slug"] ?>"><?= $row['proj_name'] ?></a>
                                                                </h3>

                                                                <?php if (is_numeric($row["proj_price"]) && $row["proj_price"] != 0): ?>
                                                                    <div class="price-row"
                                                                        style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                                                                        <!-- Price Output -->
                                                                        <div class="geodir-category-content_price  currency-price"
                                                                            data-price="<?= $row["proj_price"] ?>"
                                                                            data-original-currency="AED"
                                                                            data-original-price="<?= $row["proj_price"] ?>">
                                                                            <img src="<?= BASE_URL ?>images/symbol/currency-symbol.png"
                                                                                alt="Currency"
                                                                                style="height: 14px; vertical-align: middle;">
                                                                            AED <?= number_format($row["proj_price"]) ?>
                                                                        </div>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="geodir-category-content_price ">
                                                                        Call Us
                                                                    </div>
                                                                <?php endif; ?>



                                                                <div class="geodir-category-content-details">
                                                                    <ul>
                                                                        <?php if (!empty($row["proj_bedroom"])): ?>
                                                                            <li><i
                                                                                    class="fa-light fa-bed"></i><span><?= htmlspecialchars(str_replace(',', ', ', $row["proj_bedroom"])) ?></span>
                                                                            </li>
                                                                        <?php endif; ?>
                                                                        <?php if (!empty($row["proj_shower"])): ?>
                                                                            <li><i
                                                                                    class="fa-light fa-bath"></i><span><?= htmlspecialchars($row["proj_shower"]) ?></span>
                                                                            </li>
                                                                        <?php endif; ?>
                                                                        <?php if (!empty($row["proj_area"])): ?>
                                                                            <li><i class="fa-light fa-chart-area"></i><span><?= htmlspecialchars($row["proj_area"]) ?>
                                                                                    sq ft</span></li>
                                                                        <?php endif; ?>
                                                                    </ul>
                                                                </div>
                                                            </div>

                                                            <div class="geodir-category-footer list-footer eq-ns-f">
                                                                <a href="<?= BASE_URL ?>rent-property/<?= $row["proj_slug"] ?>"
                                                                    class="gcf-company post-card_link-2">View<i
                                                                        class="fa-solid fa-caret-right"></i></a>
                                                                <div class="clearfix"></div>
                                                                <div class="list-contact-icon">
                                                                    <a href="tel:<?= $header_details['header_contact_number'] ?>"
                                                                        target="_blank" class="pcl_btn">
                                                                        <img src="<?= BASE_URL ?>images/c-icon/call-icon.png"
                                                                            alt="Call Icon" width="100%" title="Call">
                                                                    </a>
                                                                    <a href="https://wa.me/<?= $header_details['header_contact_number'] ?>"
                                                                        target="_blank" class="pcl_btn">
                                                                        <img src="<?= BASE_URL ?>images/c-icon/whatsapp-icon.png"
                                                                            alt="Whatsapp Icon" width="100%" title="Whatsapp">
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                                <div class="ss-carousel-button ss-carousel-button-prev"><i class="fas fa-caret-left"></i>
                                </div>
                                <div class="ss-carousel-button ss-carousel-button-next"><i class="fas fa-caret-right"></i>
                                </div>

                            </div>
                        </div>


                    </div>

                <?php endif; ?>
            </div>
            <!--main-content end-->
            <div class="to_top-btn-wrap">
                <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i>
                </div>
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