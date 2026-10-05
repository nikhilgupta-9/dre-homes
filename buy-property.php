<?php
// Report all errors
error_reporting(E_ALL);

// Display errors on the screen
ini_set('display_errors', 1);


include('db/config.php');
$meta_tags = select_row("tbl_tags", "id=19 and is_active=1");
include('common/header.php');
$page = 1;
$searchRaw = ''; // or some default value

if (!empty($_GET)) {
    $conditions = [];

    $conditions[] = "prj_type_id = 7";

    if (!empty($_GET["search_offplan"])) {
        $searchRaw = urldecode($_GET["search_offplan"]);
        $searchTerm = mysqli_real_escape_string($conn, $searchRaw);
        $conditions[] = "proj_name LIKE '%$searchTerm%'";
    }

    if (!empty($_GET["dev_id"])) {
        $devId = (int) $_GET["dev_id"];
        $conditions[] = "prj_dev_id = $devId";
    }

    if (!empty($_GET["proj_bedroom"])) {
        $bedroomsArr = [];

        if (is_array($_GET["proj_bedroom"])) {
            $bedroomsArr = $_GET["proj_bedroom"];
        } else {
            $bedroomsRaw = urldecode($_GET["proj_bedroom"]);
            $bedroomsArr = explode(",", $bedroomsRaw);
        }

        $bedroomsClean = array_map(function ($b) use ($conn) {
            return mysqli_real_escape_string($conn, trim($b));
        }, $bedroomsArr);

        $bedroomConditions = [];
        foreach ($bedroomsClean as $bed) {
            $bedroomConditions[] = "proj_bedroom LIKE '%$bed%'";
        }

        if (!empty($bedroomConditions)) {
            $conditions[] = "(" . implode(" OR ", $bedroomConditions) . ")";
        }
    }
    if (!empty($_GET["property_type"])) {

        $propertyType = mysqli_real_escape_string(
            $conn,
            $_GET["property_type"]
        );

        $conditions[] = "proj_inventory_type = '$propertyType'";
    }

    if (!empty($_GET["location"])) {

        $location = $_GET["location"];

        $conditions[] = "proj_location LIKE '%$location%'";
    }
    // --- Price Filter ---
    if (!empty($_GET["price_min"]) && is_numeric($_GET["price_min"])) {
        $priceMin = (int) $_GET["price_min"];
        $conditions[] = "proj_price >= $priceMin";
    }

    if (
        !empty($_GET["price_max"]) &&
        is_numeric($_GET["price_max"]) &&
        $_GET["price_max"] !== "show_all"
    ) {
        $priceMax = (int) $_GET["price_max"];
        $conditions[] = "proj_price <= $priceMax";
    }

    // --- Amenities Filter ---
    if (!empty($_GET["amenities"]) && is_array($_GET["amenities"])) {
        $amenityConditions = [];
        $selectedAmenityIds = array_map("intval", $_GET["amenities"]);
        foreach ($selectedAmenityIds as $aid) {
            $amenityConditions[] = "FIND_IN_SET($aid, proj_fea_fac_opt)";
        }

        if (!empty($amenityConditions)) {
            $conditions[] = "(" . implode(" AND ", $amenityConditions) . ")";
        }
    }

    $whereClause = implode(" AND ", $conditions);
    $whereClause .= " AND is_active = 1";

    $sql = select_query("tbl_proj", $whereClause);
} else {
    $sql = select_query("tbl_proj", "prj_type_id = 7 AND is_active = 1");
}

$count = mysqli_num_rows($sql);
$per_page = 10;
$no_of_page = ceil($count / $per_page);
$start = ($page - 1) * $per_page;
$buyprop_content = select_row("tbl_buyproperty_content", "is_active=1");
?>
<style>
    .gallery-layout {
        display: flex;
    }

    .left-gallery {
        height: 250px;
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
        height: 250px;
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

    
    @media only screen and (max-width: 1068px) {
    .left-col {
        display: none;
    }
    .br-list .gallery-layout {
            min-height: 327px;
        }
        .br-list .right-col {
            margin: auto ;
            padding: 0 20px !important;
        }
    }

    @media(max-width:991px) {
        .br-list .right-col {
        margin: auto;
        padding: 0 5px !important;
        }

        .br-list .gallery-layout {
            display: flex;
            padding:10px;
        }

        .left-gallery,
        .right-gallery {
            height: 250px;
        }

        .br-list .gallery-img,
        .br-list .right-gallery {
            aspect-ratio: unset;
        }

        .left-col {
            order: 2;
        }

        .right-col {
            order: 1;
        }
    }
    @media(max-width:768px){
        .left-gallery,
        .right-gallery {
            height: auto;
        }
    }
</style>
<!--warpper-->
<div class="wrapper">
    <!--content-->
    <div class="content search-pgs">
        <!--section-->
        <div class="section hero-section hero-section_sin">
            <div class="hero-section-wrap">
                <div class="hero-section-wrap-item">
                    <div class="container">
                        <div class="hero-section-container">
                            <div class="hero-section-title">
                                <h1><?= $buyprop_content[
                                    "buyproperty_cont_heading"
                                ] ?></h1>

                            </div>
                        </div>
                    </div>

                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                        <div class="bg" data-bg="<?= BASE_URL_IMG ?>project/<?= $buyprop_content[
                              "buyproperty_cont_img"
                          ] ?>" data-scrollax="properties: { translateY: '30%' }">
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
                <a href="<?= BASE_URL ?>">Home</a><a href="javascript:void(0)">Properties</a><span>Buy Property</span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
            <?php
            $prop_query = select_query("tbl_proj", "prj_type_id=7 and proj_markfeat=1 and is_active=1 order by proj_name");
            $proj_count = mysqli_num_rows($prop_query);

            if ($proj_count > 0 && empty($_GET)): ?>
                <!-- exclusive listing carousel starts -->
                <div class="testimonilas-carousel-wrap">
                    <div class="testimonilas-carousel exc-listing-carousel equal-elc">
                        <div class="exc-carousel">
                            <h4>Exclusive Listing</h4>
                        </div>

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
                                                        <a href="<?= BASE_URL ?>buy-property/<?= $row["proj_slug"] ?>"
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
                                                                href="<?= BASE_URL ?>buy-property/<?= $row["proj_slug"] ?>"><?= $row['proj_name'] ?></a>
                                                        </h3>

                                                        <?php if (is_numeric($row["proj_price"]) && $row["proj_price"] != 0): ?>
                                                            <div class="price-row"
                                                                style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                                                                <!-- Price Output -->
                                                                <div class="geodir-category-content_price  currency-price"
                                                                    data-price="<?= $row["proj_price"] ?>"
                                                                    data-original-currency="AED"
                                                                    data-original-price="<?= $row["proj_price"] ?>"
                                                                    style="width: 100%;">
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
                                                                            sq ft</span> </li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <div class="geodir-category-footer list-footer eq-ns-f">
                                                        <a href="<?= BASE_URL ?>buy-property/<?= $row["proj_slug"] ?>"
                                                            class="gcf-company post-card_link-2">View<i
                                                                class="fa-solid fa-caret-right"></i></a>
                                                        <div class="clearfix"></div>
                                                        <div class="list-contact-icon">
                                                            <a href="tel:<?= $header_details['header_contact_number'] ?>"
                                                                target="_blank" class="pcl_btn">
                                                                <img src="images/c-icon/call-icon.png" alt="Call Icon"
                                                                    width="100%" title="Call">
                                                            </a>
                                                            <a href="https://wa.me/<?= $header_details['header_contact_number'] ?>"
                                                                target="_blank" class="pcl_btn">
                                                                <img src="images/c-icon/whatsapp-icon.png" alt="Whatsapp Icon"
                                                                    width="100%" title="Whatsapp">
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

                        <div class="tc-button tc-button-next"><i class="fas fa-caret-right"></i></div>
                        <div class="tc-button tc-button-prev"><i class="fas fa-caret-left"></i></div>
                    </div>
                </div>
                <!-- exclusive listing carousel ends -->
            <?php endif; ?>

            <div class="boxed-container space-r">
                <div class="show-mob-filter"><i class="far fa-sliders-h"></i> Search Filters</div>
                <!-- list-searh-input-wrap-->
                <form action="" method="get">
                    <div class="list-searh-input-wrap box_list-searh-input-wrap lws_mobile lsw_mb-btn">
                        <div class="close_mob-filter cmf"><i class="fa-regular fa-xmark"></i></div>
                        <div class="list-searh-input-wrap-title_wrap">
                            <div class="list-searh-input-wrap-title"><i class="far fa-sliders-h"></i><span>Search
                                    Filters</span></div>

                        </div>
                        <div class="custom-form">
                            <div class="row">
                                <!-- listsearch-input-item -->
                                <div class="col-lg-4">
                                    <div class="cs-intputwrap">
                                        <label for="" class="sr-only">Your Query</label>
                                        <i class="fa-light fa-search"></i>
                                        <div class="autocomplete-container">
                                            <div class="search-wrapper">
                                                <input type="text" name="search_offplan" class="search-input"
                                                    placeholder="Search Property..." autocomplete="off" <?php if (isset($_GET['search_offplan'])): ?>
                                                        value="<?= htmlspecialchars($_GET['search_offplan']) ?>" <?php endif; ?>>

                                                <span class="clear-btn">&times;</span>
                                            </div>
                                            <?php
                                            $prop_query = select_query("tbl_proj", "prj_type_id=7 and is_active=1 order by prj_id desc");
                                            $proj_count = mysqli_num_rows($prop_query);
                                            if ($proj_count > 0): ?>
                                                <ul class="suggestions">
                                                    <?php while ($row = mysqli_fetch_array($prop_query)): ?>
                                                        <li><?= htmlspecialchars($row["proj_name"]) ?></li>
                                                    <?php endwhile; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- listsearch-input-item -->

                                <!-- listsearch-input-item -->
                                <div class="col-lg-4">
                                    <div class="cs-intputwrap">
                                        <label for="" class="sr-only">Developer</label>
                                        <i class="fa-light fa-home"></i>
                                        <div class="autocomplete-container">
                                            <div class="search-wrapper">
                                                <input type="text" name="dev_name" class="search-input"
                                                    placeholder="Developer" autocomplete="off" <?php if (isset($_GET['dev_name'])): ?>
                                                        value="<?= htmlspecialchars($_GET['dev_name']) ?>" <?php endif; ?>>
                                                <input type="hidden" name="dev_id" id="dev_id"
                                                    value="<?= isset($_GET['dev_id']) ? (int) $_GET['dev_id'] : 0 ?>">
                                                <span class="clear-btn">&times;</span>
                                            </div>
                                            <?php
                                            $dev_query = select_query("tbl_developer", "is_active=1 ORDER BY dev_heading");
                                            if (mysqli_num_rows($dev_query) > 0): ?>
                                                <ul class="suggestions">
                                                    <?php while ($row = mysqli_fetch_array($dev_query)): ?>
                                                        <li data-dev-id="<?= (int) $row["dev_id"] ?>">
                                                            <?= htmlspecialchars($row["dev_heading"]) ?>
                                                        </li>
                                                    <?php endwhile; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- listsearch-input-item -->



                                <?php
                                // Bedrooms logic unchanged, assuming $conn is your mysqli connection
                                $sql = "SELECT DISTINCT proj_bedroom FROM tbl_proj WHERE proj_bedroom IS NOT NULL AND proj_bedroom != ''";
                                $result = mysqli_query($conn, $sql);

                                $bedroom_options = [];

                                while ($row = mysqli_fetch_assoc($result)) {
                                    $values = explode(",", $row["proj_bedroom"]);
                                    foreach ($values as $val) {
                                        $val = trim($val);
                                        // Keep only integers
                                        if ($val !== "" && ctype_digit($val) && !in_array($val, $bedroom_options, true)) {
                                            $bedroom_options[] = $val;
                                        }
                                    }
                                }

                                // Sort numerically
                                sort($bedroom_options, SORT_NUMERIC);

                                $selected_bedrooms_string = "";
                                if (isset($_GET["proj_bedroom"])) {
                                    if (is_array($_GET["proj_bedroom"])) {
                                        $selected_bedrooms_string = implode(",", $_GET["proj_bedroom"]);
                                    } else {
                                        $selected_bedrooms_string = $_GET["proj_bedroom"];
                                    }
                                }
                                $selected_bedrooms = array_filter(array_map("trim", explode(",", $selected_bedrooms_string)));
                                ?>

                                <div class="col-lg-4">
                                    <div class="cs-intputwrap">
                                        <label for="proj_bedroom" class="sr-only">Choose Bedroom</label>
                                        <i class="fa-light fa-bed"></i>
                                        <select name="proj_bedroom[]" data-placeholder="Project Bedroom"
                                            class="chosen-select on-radius no-search-select" multiple>
                                            <?php foreach ($bedroom_options as $option): ?>
                                                <?php $isSelected = in_array($option, $selected_bedrooms, true) ? "selected" : ""; ?>
                                                <option value="<?= htmlspecialchars($option) ?>" <?= $isSelected ?>>
                                                    <?= htmlspecialchars($option) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <?php
                                // Prices array and formatting function unchanged
                                
                                $prices = [
                                    500000,
                                    750000,
                                    1000000,
                                    2000000,
                                    3000000,
                                    4000000,
                                    5000000,
                                    6000000,
                                    7000000,
                                    8000000,
                                    9000000,
                                    10000000,
                                    15000000,
                                    25000000,
                                    35000000,
                                    45000000,
                                ];

                                function formatPrice($price)
                                {
                                    return "AED " . number_format($price);
                                }

                                // Check if GET values exist and sanitize
                                $selectedMin = isset($_GET["price_min"]) ? $_GET["price_min"] : "";
                                $selectedMax = isset($_GET["price_max"]) ? $_GET["price_max"] : "";
                                ?>

                                <!-- Min Price -->
                                <div class="col-lg-3">
                                    <div class="cs-intputwrap">
                                        <label for="price_min" class="sr-only">Set Min Price</label>
                                        <i class="fa-light fa-tag"></i>
                                        <select name="price_min" data-placeholder="Price Min"
                                            class="chosen-select on-radius no-search-select" id="price_min">
                                            <option value="show_all" <?= $selectedMin === "show_all" ? "selected" : "" ?>>
                                                Min</option>
                                            <?php foreach ($prices as $price): ?>
                                                <option value="<?= htmlspecialchars($price) ?>" <?= ($selectedMin !== "" && $selectedMin == $price) ? "selected" : "" ?>>
                                                    <?= formatPrice($price) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Max Price -->
                                <div class="col-lg-3">
                                    <div class="cs-intputwrap">
                                        <label for="price_max" class="sr-only">Set Max Price</label>
                                        <i class="fa-light fa-tag"></i>
                                        <select name="price_max" data-placeholder="Price Max"
                                            class="chosen-select on-radius no-search-select" id="price_max">
                                            <option value="show_all" <?= $selectedMax === "show_all" ? "selected" : "" ?>>
                                                Max</option>
                                            <?php foreach ($prices as $price): ?>
                                                <option value="<?= htmlspecialchars($price) ?>" <?= ($selectedMax !== "" && $selectedMax == $price) ? "selected" : "" ?>>
                                                    <?= formatPrice($price) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>



                                <!-- Amenities -->
                                <div class="col-lg-3">
                                    <div class="hidden-listing_search_wrap">
                                        <div class="more_search-btn">More Options <i class="fa-regular fa-plus"></i>
                                        </div>
                                        <div class="hidden-listing-filter">
                                            <?php
                                            $amenities_query = mysqli_query($conn, "SELECT featfac_id, featfac_name FROM tbl_proj_featfac WHERE is_active = 1 ORDER BY featfac_name");
                                            $selected_amenities = isset($_GET["amenities"]) ? (array) $_GET["amenities"] : [];
                                            ?>
                                            <div class="hidden-listing-item">
                                                <div class="filter-tags-title">Amenities</div>
                                                <div class="filter-tags">
                                                    <ul class="no-list-style">
                                                        <?php while ($amenity = mysqli_fetch_assoc($amenities_query)):
                                                            $id = (int) $amenity["featfac_id"];
                                                            $name = htmlspecialchars($amenity["featfac_name"]);
                                                            $isChecked = in_array($id, $selected_amenities) ? "checked" : "";
                                                            ?>
                                                            <li>
                                                                <input class="amenity-checkbox" id="amenity-<?= $id ?>"
                                                                    type="checkbox" name="amenities[]" value="<?= $id ?>"
                                                                    <?= $isChecked ?>>
                                                                <label for="amenity-<?= $id ?>"><?= $name ?></label>
                                                            </li>
                                                        <?php endwhile; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <!-- Search Button -->
                                <div class="col-lg-3">
                                    <button type="submit" name="submit"
                                        class="commentssubmit commentssubmit_fw">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>

            <!--<div class="small-head">-->
            <!--    <p>Properties for rent in Dubai (378)</p>-->
            <!--</div>-->




            <!--breadcrumbs-list end-->
            <!--main-content-->
            <div class="main-content">
                <!--boxed-container-->
                <div class="boxed-container">
                    <!---add-filter----->
                    <div class="row">
                        <div class="col-lg-12 br-space buy_prop_pos">
                            <!-- list-main-wrap-header-->
                            <div class="list-main-wrap-header box-list-header" style="margin-top: 0">
                                <!-- list-main-wrap-title-->
                                <div class="list-main-wrap-title">
                                    <h2>Results : <span><?= $searchRaw ?></span><strong><?= $count ?></strong></h2>
                                </div>
                                <!-- list-main-wrap-title end-->

                            </div>
                            <!-- list-main-wrap-header end-->
                            <!--listing-item-container-->
                            <div class="listing-item-container fw-listing-item buy-listing  buy_prop_data">
                                <!-- 1st listing-item -->
                                <?php
                                if (!isset($whereClause)) {
                                    $conditions = [];
                                    $conditions[] = "prj_type_id = 7";
                                    $conditions[] = "is_active = 1";

                                    if (!empty($_GET["search_offplan"])) {
                                        $searchRaw = urldecode($_GET["search_offplan"]);
                                        $searchTerm = mysqli_real_escape_string($conn, $searchRaw);
                                        $conditions[] = "proj_name LIKE '%$searchTerm%'";
                                    }

                                    if (!empty($_GET["dev_id"])) {
                                        $devId = (int) $_GET["dev_id"];
                                        $conditions[] = "prj_dev_id = $devId";
                                    }

                                    if (!empty($_GET["proj_bedroom"])) {
                                        $bedroomsArr = [];

                                        if (is_array($_GET["proj_bedroom"])) {
                                            $bedroomsArr = $_GET["proj_bedroom"];
                                        } else {
                                            $bedroomsRaw = urldecode($_GET["proj_bedroom"]);
                                            $bedroomsArr = explode(",", $bedroomsRaw);
                                        }

                                        $bedroomsClean = array_map(function ($b) use ($conn) {
                                            return mysqli_real_escape_string($conn, trim($b));
                                        }, $bedroomsArr);

                                        $bedroomConditions = [];
                                        foreach ($bedroomsClean as $bed) {
                                            $bedroomConditions[] = "proj_bedroom LIKE '%$bed%'";
                                        }

                                        if (!empty($bedroomConditions)) {
                                            $conditions[] = "(" . implode(" OR ", $bedroomConditions) . ")";
                                        }
                                    }

                                    if (!empty($_GET["price_min"]) && is_numeric($_GET["price_min"])) {
                                        $conditions[] = "proj_price >= " . (int) $_GET["price_min"];
                                    }

                                    if (!empty($_GET["price_max"]) && is_numeric($_GET["price_max"]) && $_GET["price_max"] !== "show_all") {
                                        $conditions[] = "proj_price <= " . (int) $_GET["price_max"];
                                    }

                                    if (!empty($_GET["amenities"]) && is_array($_GET["amenities"])) {
                                        $amenityConditions = [];
                                        $selectedAmenityIds = array_map("intval", $_GET["amenities"]);
                                        foreach ($selectedAmenityIds as $aid) {
                                            $amenityConditions[] = "FIND_IN_SET($aid, proj_fea_fac_opt)";
                                        }

                                        if (!empty($amenityConditions)) {
                                            $conditions[] = "(" . implode(" AND ", $amenityConditions) . ")";
                                        }
                                    }

                                    $whereClause = implode(" AND ", $conditions);
                                }

                                // Now use it in the dynamic listing query
                                $finalQuery = $whereClause . " ORDER BY prj_id desc LIMIT $start, $per_page";
                                $dynamic_listings = select_query("tbl_proj", $finalQuery);


                                if (mysqli_num_rows($dynamic_listings) > 0):
                                    while ($row = mysqli_fetch_array($dynamic_listings)):

                                        $proj_dev = null;

                                        if (!empty($row["prj_dev_id"]) && is_numeric($row["prj_dev_id"])) {
                                            $proj_dev = select_row("tbl_developer", "dev_id=" . (int) $row["prj_dev_id"]);
                                        }
                                        $prj_thumb_arr = explode(",", $row["prj_thumb_img"]);
                                        
                                        // Clean and filter empty values
                                        $prj_thumb_arr = array_filter($prj_thumb_arr, function ($val) {
                                            return !empty(trim($val));
                                        });

                                        // Re-index the array
                                        $prj_thumb_arr = array_values($prj_thumb_arr);

                                        // Set fallback images
                                        $main_thumb = isset($prj_thumb_arr[0]) ? $prj_thumb_arr[0] : 'default.jpg';
                                        $thumb1 = isset($prj_thumb_arr[1]) ? $prj_thumb_arr[1] : $main_thumb;
                                        $thumb2 = isset($prj_thumb_arr[2]) ? $prj_thumb_arr[2] : $main_thumb;
                                        ?>
                                        <div class="listing-item br-list">
                                            <div class="geodir-category-listing">
                                                <div class="geodir-category-img">
                                                    <div class="boxed-content">
                                                        <div class="boxed-content-item pd-rem">
                                                            <div class="row gallery-layout">
                                                                <!-- RIGHT SIDE -->
                                                                <div class="col-lg-8 px-1 right-col">
                                                                    <div class="right-gallery">
                                                                        <a data-fancybox="dre-gallery-<?= $row['prj_id'] ?>"
                                                                            href="<?= BASE_URL_IMG ?>project/thumb/<?= $main_thumb ?>"
                                                                            class="dre-gallery-item">
                                                                            <img src="<?=BASE_URL_IMG . "project/thumb/" . $main_thumb?>"
                                                                                class="dre-gallery-img" />
                                                                        </a>
                                                                    </div>
                                                                </div>

                                                                <!-- LEFT SIDE -->
                                                                <div class="col-lg-4 px-1 left-col">
                                                                    <div class="left-gallery">

                                                                        <div class="gallery-img">
                                                                            <a data-fancybox="dre-gallery-<?= $row['prj_id'] ?>"
                                                                                href="<?= BASE_URL_IMG ?>project/thumb/<?= $thumb1 ?>"
                                                                                class="dre-gallery-item">
                                                                                <img src="<?= BASE_URL_IMG ?>project/thumb/<?= $thumb1 ?>"
                                                                                    class="dre-gallery-img" />
                                                                            </a>
                                                                        </div>

                                                                        <div class="gallery-img">
                                                                            <a data-fancybox="dre-gallery-<?= $row['prj_id'] ?>"
                                                                                href="<?= BASE_URL_IMG ?>project/thumb/<?= $thumb2 ?>"
                                                                                class="dre-gallery-item">
                                                                                <img src="<?= BASE_URL_IMG ?>project/thumb/<?= $thumb2 ?>"
                                                                                    class="dre-gallery-img" />
                                                                            </a>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="geodir-category-content">
                                                    <h3><a
                                                            href="<?= BASE_URL ?>buy-property/<?= $row["proj_slug"] ?>"><?= $row['proj_name'] ?></a>
                                                    </h3>

                                                    <div class="geodir-category-location mb-2" style="text-align: left">
                                                        <a href="javascript:void(0)" class="map-item color-b">
                                                            <i class="fas fa-map-marker-alt"></i> <?= $row['proj_location'] ?>
                                                        </a>
                                                    </div>

                                                    <?php if (is_numeric($row["proj_price"]) && $row["proj_price"] != 0): ?>
                                                        <div class="price-row"
                                                            style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                                                            <!-- Price Output -->
                                                            <div class="geodir-category-content_price  currency-price"
                                                                data-price="<?= $row["proj_price"] ?>" data-original-currency="AED"
                                                                data-original-price="<?= $row["proj_price"] ?>"
                                                                style="width: 100%;">
                                                                <img src="<?= BASE_URL ?>images/symbol/currency-symbol.png"
                                                                    alt="Currency" style="height: 14px; vertical-align: middle;">
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
                                                                <li>
                                                                    <i class="fa-light fa-bed"></i>
                                                                    <span><?= htmlspecialchars(str_replace(',', ', ', $row["proj_bedroom"])) ?></span>
                                                                </li>
                                                            <?php endif; ?>

                                                            <?php if (!empty($row["proj_shower"])): ?>
                                                                <li>
                                                                    <i class="fa-light fa-bath"></i>
                                                                    <span><?= htmlspecialchars($row["proj_shower"]) ?></span>
                                                                </li>
                                                            <?php endif; ?>

                                                            <?php if (!empty($row["proj_area"])): ?>
                                                                <li>
                                                                    <i class="fa-light fa-chart-area"></i>
                                                                    <span><?= htmlspecialchars($row["proj_area"]) ?> sq ft</span>
                                                                </li>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="geodir-category-footer p-list-footer">
                                                    <a href="<?= BASE_URL ?>buy-property/<?= $row["proj_slug"] ?>"
                                                        class="gcf-company post-card_link-2">View<i
                                                            class="fa-solid fa-caret-right"></i></a>
                                                    <div class="clearfix"></div>
                                                    <div class="list-contact-icon">
                                                        <a href="tel:<?= $header_details['header_contact_number'] ?>"
                                                            target="_blank" class="pcl_btn">
                                                            <img src="images/c-icon/call-icon.png" alt="Call Icon" width="100%"
                                                                title="Call">
                                                        </a>
                                                        <a href="https://wa.me/<?= $header_details['header_contact_number'] ?>"
                                                            target="_blank" class="pcl_btn">
                                                            <img src="images/c-icon/whatsapp-icon.png" alt="Whatsapp Icon"
                                                                width="100%" title="Whatsapp">
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    endwhile;
                                endif;
                                ?>



                            </div>
                            <!--listing-item-container end-->
                            <!-- pagination-->
                            <div class="pagination-wrap">
                                <div class="pagination">
                                    <?php include "pagination/paging_buy_property.php"; ?>
                                </div>
                            </div>
                            <!-- pagination end-->
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
