<?php
include('db/config.php');
$detail = trim((string) ($_GET['detail'] ?? ''));
$detail = mysqli_real_escape_string($conn, str_replace('/', '', $detail));
$dev_detail = $detail !== '' ? select_row('tbl_developer', "dev_slug='$detail' and is_active='1'") : [];
if (!$dev_detail) {
    http_response_code(404);
    include('404.php');
    exit;
}
$meta_tags = $dev_detail;
include('common/header.php');

?>
<!--warpper-->
<div class="box-head-bg"></div>
<div class="wrapper">
    <!--content-->
    <div class="content">
        <!--<div class="container">-->
        <!--    <div class="dd-hero-img" style='background-image:url(<?= BASE_URL_IMG ?>developer/inner-image/<?= $dev_detail['dev_innr_img'] ?>)'></div>-->
        <!--</div>-->
        <!--container-->
        <div class="container">
            <!--breadcrumbs-list-->
            <div class="breadcrumbs-list bl_flat">
                <a href="<?= BASE_URL ?>">Home</a><a
                    href="<?= BASE_URL ?>developer">Developers</a><span><?= $dev_detail['dev_heading'] ?></span>
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
                                        <div class="boxed-content-title seo-chg">
                                            <h1>About <?= $dev_detail['dev_heading'] ?></h1>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="text-block post-single_tb">
                                                <div class="text-block-container editor-cont">
                                                    <?= $dev_detail['dev_long_descp'] ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
<?php if (!empty($dev_detail['choose_us_img']) || !empty($dev_detail['choose_us_desc'])): ?>
<div class="dd-boxed-container">
    <div class="scroll-content-wrap">
        <div class="row dev-dd">
            <div class="col-12 mt-3">
                <div class="post-container">
                    <div class="boxed-content">
                        <div class="boxed-content-title">
                            <h3>Why Choose <?= $dev_detail['dev_heading'] ?>?</h3>
                        </div>
                        <div class="boxed-content-item">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-md-6">
                                    <?php if (!empty($dev_detail['choose_us_img'])): ?>
                                    <img src="<?= BASE_URL_IMG ?>developer/choose-us/<?= $dev_detail['choose_us_img'] ?>"
                                        alt="Developer Property">
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <?php if (!empty($dev_detail['choose_us_desc'])): ?>
                                    <div class="text-block post-single_tb">
                                        <div class="text-block-container">
                                            <p><?= $dev_detail['choose_us_desc'] ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php
                                    $choose_us_query = select_query('tbl_dev_chooseus', 'dev_id="' . $dev_detail['dev_id'] . '" and is_active=1 ORDER BY choose_us_id desc');
                                    if (mysqli_num_rows($choose_us_query) > 0):
                                    ?>
                                    <div class="text-block post-single_tb">
                                        <div class="text-block-container">
                                            <div class="mt-3" id="drop1">
                                                <div class="accordion">
                                                    <?php while ($row = mysqli_fetch_array($choose_us_query)): ?>
                                                    <a class="toggle" href="#"><?= $row['choose_us_head'] ?><i
                                                            class="fa-solid fa-caret-down"></i></a>
                                                    <div class="accordion-inner">
                                                        <p><?= $row['choose_us_descp'] ?></p>
                                                    </div>
                                                    <?php endwhile; ?>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div> <!-- .boxed-content-item -->
                    </div> <!-- .boxed-content -->
                </div> <!-- .post-container -->
            </div> <!-- .col-12 -->
        </div> <!-- .row -->
    </div> <!-- .scroll-content-wrap -->
    <div class="limit-box"></div>
</div> <!-- .dd-boxed-container -->
<?php endif; ?>
                    </div>
                    <div class="limit-box"></div>
                </div>


                <?php
$prj_query = select_query(
    'tbl_proj',
    'prj_dev_id="' . $dev_detail['dev_id'] . '" AND is_active=1 AND prj_type_id IN (1) ORDER BY proj_posted_on DESC'
);
                $count = mysqli_num_rows($prj_query);
                if ($count > 0) {
                    ?>

                    <div class="boxed-container">
                        <div class="boxed-content-title bcst_ca">
                            <h3>Latest Projects by <?= $dev_detail['dev_heading'] ?></h3>
                        </div>
                        <div class="single-carousel-wrap">
                            <div class="single-carousel equal-elc">
                                <div class="swiper-container">
                                    <div class="swiper-wrapper">



                                        <?php
                                        $key = 0;
                                        while ($row = mysqli_fetch_array($prj_query)) {
                                            $prj_thumb_arr = explode(',', $row['prj_thumb_img']);

                                            if (count($prj_thumb_arr) > 1) {
                                                $prj_thumb_img = $prj_thumb_arr[0];
                                            } else {
                                                $prj_thumb_img = $row['prj_thumb_img'];
                                            }
                                            ?>

                                            <div class="swiper-slide">
                                                <div class="listing-item">
                                                    <div class="geodir-category-listing">
                                                        <div class="geodir-category-img">
                                                            <a href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>" class="geodir-category-img_item">
                                                                <div class="off-plan-thumb">
                                                                    <img src="<?= BASE_URL_IMG ?>project/thumb/<?= $prj_thumb_img ?>" alt="">
                                                                </div>
                                                                <div class="overlay"></div>
                                                            </a>
                                                            <div class="geodir-category-location">
                                                                <a href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>" class="map-item"><i
                                                                        class="fas fa-map-marker-alt"></i>
                                                                    <?= $row["proj_location"] ?></a>
                                                            </div>
                                                            <ul class="list-single-opt_header_cat off-n">
                                                                <?php if (!empty($row['proj_handover'])): ?>
                                                                    <li>
                                                                        <a href="javascript:void(0)" class="cat-opt">
                                                                            <?= $row['proj_handover'] ?></a>
                                                                    </li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                        <div class="geodir-category-content prop-gcc off-n">

                                                            <?php if (!empty($row['proj_name_on_card'])) { ?>
                                                                <h3>
                                                                    <a href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>">
                                                                        <?= $row['proj_name_on_card'] ?>
                                                                    </a>
                                                                </h3>
                                                            <?php } else { ?>
                                                                <h3>
                                                                    <a href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>">
                                                                        <?= $row['proj_name'] ?>
                                                                    </a>
                                                                </h3>
                                                            <?php } ?>
                                                            <h5>
                                                                <span class="off-n-sp1">By</span>
                                                                <span class="off-n-sp2"><?= getDeveloper($row["prj_dev_id"]) ?></span>
                                                            </h5>

                                                        </div>

                                                        <div class="geodir-category-footer list-footer">
                                                            <div class="d-flex">
                                                            <?php
                                                            $bedroom1 = '';

                                                            if (!empty($row['proj_bedroom_on_card'])) {
                                                                $bedroom1 = htmlspecialchars($row['proj_bedroom_on_card']);
                                                            } else {
                                                                $bedrooms = explode(",", $row["proj_bedroom"]);
                                                                $bedrooms = array_map('trim', $bedrooms);
                                                                $bedrooms = array_filter($bedrooms);

                                                                if (!empty($bedrooms)) {
                                                                    $first = reset($bedrooms);
                                                                    $last  = end($bedrooms);

                                                                    if ($first == $last) {
                                                                        $bedroom1 = htmlspecialchars($first);
                                                                    } else {
                                                                        $bedroom1 = htmlspecialchars($first) . ' - ' . htmlspecialchars($last);
                                                                    }
                                                                }
                                                            }

                                                            // ✅ print only once
                                                            if (!empty($bedroom1)) {
                                                                echo '
                                                                <div class="off-p-f1">
                                                                    <div class="off-f-h">Bedroom:</div>
                                                                    <div class="off-f-b">
                                                                        ' . $bedroom1 . '
                                                                    </div>
                                                                </div>';
                                                            }
                                                            ?>
                                                            <div class="off-p-f2 ms-2">
                                                                <?php if (is_numeric($row["proj_price"]) && $row["proj_price"] != 0): ?>
                                                                    <div class="price-row" style="display: flex; align-items: flex-start; flex-direction: column; gap: 0; font-size:1em;">
                                                                        <div class="text-muted off-f-h">Starting Price: </div>
                                                                        <!-- Price Output -->
                                                                        <div class="geodir-category-content_price text-muted      currency-price off-f-b " "
                                                            data-price=" <?= $row["proj_price"] ?>"
                                                                            data-original-currency="AED"
                                                                            data-original-price="<?= $row["proj_price"] ?>">
                                                                            <img src="<?= BASE_URL ?>images/symbol/currency-symbol.png" alt="Currency"
                                                                                style="height: 16px; vertical-align: middle;">
                                                                            AED <?= number_format($row["proj_price"]) ?>
                                                                        </div>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="geodir-category-content_price text-muted " style="font-size:1em; flex-direction: column; align-items: flex-start;">
                                                                        <div class="text-muted off-f-h">Starting Price:</div>
                                                                        <div class="off-f-b " ">Call Us</div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            </div>
                                            <div class=" clearfix"></div>
                                                                        <?php
                                                                        $phone = '+971527543243' . $row["proj_whatsaapno"];

                                                                        $imageUrl = BASE_URL_IMG . "project/thumb/" . $prj_thumb_img;

                                                                        $message = "Hi, I'm interested in " . $row["proj_name"] . ".\n\n"
                                                                            . "Property Image:\n" . $imageUrl . "\n\n"
                                                                            . "Link:\n" . BASE_URL . "property/" . $row["proj_slug"];

                                                                        $encodedMessage = urlencode($message);
                                                                        ?>

                                                                        <div class="list-contact-icon off-p-f3">
                                                                            <a href="https://wa.me/<?= $phone ?>?text=<?= $encodedMessage ?>" target="_blank"
                                                                                class="pcl_btn">

                                                                                <img src="<?= BASE_URL ?>images/wa-chat-logo-w.png"
                                                                                    alt="Whatsapp Icon" width="100%" title="Whatsapp">
                                                                                <span class="d-none d-md-inline">WHATSAPP</span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                        </div>
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