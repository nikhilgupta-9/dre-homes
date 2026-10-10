<?php
include "db/config.php";
$meta_tags = select_row("tbl_tags", "id=8 and is_active=1");
include "common/header.php";
$page = 1;
if (!empty($_GET)) {
    $conditions = [];

    $conditions[] = "prj_type_id = 1";

    if (!empty($_GET["search_offplan"])) {
        $searchRaw = urldecode($_GET["search_offplan"]);
        $searchTerm = mysqli_real_escape_string($conn, $searchRaw);
        $conditions[] = "proj_name LIKE '%$searchTerm%'";
    }

    if (!empty($_GET["dev_id"])) {
        $devId = (int) $_GET["dev_id"];
        $conditions[] = "prj_dev_id = $devId";
    }

    if (!empty($_GET["comm_id"])) {
        $commRaw = urldecode($_GET["comm_id"]);
        $commParts = explode(",", $commRaw);
        $commIds = array_map("intval", array_filter($commParts));

        if (!empty($commIds)) {
            $commConditions = [];
            foreach ($commIds as $cid) {
                $commConditions[] = "FIND_IN_SET($cid, proj_comm_feat)";
            }
            $conditions[] = '(' . implode(' OR ', $commConditions) . ')';
        }
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
            // $bedroomConditions[] = "proj_bedroom LIKE '%$bed%'";
            $bed = trim($bed);
            $bedroomConditions[] = "FIND_IN_SET(UPPER('$bed'), UPPER(REPLACE(proj_bedroom, ' ', '')))";
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

        $location =$_GET["location"];

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
    $sql = select_query("tbl_proj", "prj_type_id = 1 AND is_active = 1");
}

$count = mysqli_num_rows($sql);
$per_page = 9;
$no_of_page = ceil($count / $per_page);
$start = ($page - 1) * $per_page;
$offplan_content = select_row("tbl_offplan_content", "is_active=1");
?>

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
                                <h1><?= $offplan_content["offplan_cont_heading"] ?></h1>
                            </div>
                        </div>
                    </div>

                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                        <div class="bg" data-bg="<?= BASE_URL_IMG ?>project/<?= $offplan_content["offplan_cont_img"] ?>"
                            data-scrollax="properties: { translateY: '30%' }"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--section-end-->
        <!--container-->
        <div class="container">
            <!--breadcrumbs-list-->
            <div class="breadcrumbs-list bl_flat">
                <a href="<?= BASE_URL ?>">Home</a><span>Off-Plan Properties</span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
            <!--breadcrumbs-list end-->
            <!--main-content-->
            <div class="main-content off-plan-box">
                <!--boxed-container-->
                <div class="boxed-container">
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
                                                        placeholder="Search Property..." autocomplete="off"
                                                        <?php if (isset($_GET['search_offplan'])): ?>
                                                        value="<?= htmlspecialchars($_GET['search_offplan']) ?>"
                                                        <?php endif; ?>>

                                                    <span class="clear-btn">&times;</span>
                                                </div>
                                                <?php
                                                $prop_query = select_query("tbl_proj", "prj_type_id=1 and is_active=1 order by proj_name");
                                                $proj_count = mysqli_num_rows($prop_query);
                                                if ($proj_count > 0): ?>

                                                    <ul class="suggestions">
                                                        <?php while ($row = mysqli_fetch_assoc($prop_query)): ?>
                                                            <?php
                                                            if (!empty($row['proj_name_on_card'])):
                                                            ?>
                                                                <li><?= htmlspecialchars($row["proj_name_on_card"]) ?></li>
                                                            <?php
                                                            else:
                                                            ?>
                                                                <li><?= htmlspecialchars($row["proj_name"]) ?></li>
                                                        <?php
                                                            endif;
                                                        endwhile;
                                                        ?>
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
                                                    <input type="text" name="dev_name" class="search-input" placeholder="Developer" autocomplete="off"
                                                        <?php if (isset($_GET['dev_name'])): ?>
                                                        value="<?= htmlspecialchars($_GET['dev_name']) ?>"
                                                        <?php endif; ?>>
                                                    <input type="hidden" name="dev_id" id="dev_id" value="<?= isset($_GET['dev_id']) ? (int)$_GET['dev_id'] : 0 ?>">
                                                    <span class="clear-btn">&times;</span>
                                                </div>
                                                <?php
                                                $dev_query = select_query("tbl_developer", "is_active=1 ORDER BY dev_heading");
                                                if (mysqli_num_rows($dev_query) > 0): ?>
                                                    <ul class="suggestions">
                                                        <?php while ($row = mysqli_fetch_array($dev_query)): ?>
                                                            <li data-dev-id="<?= (int)$row["dev_id"] ?>"><?= htmlspecialchars($row["dev_heading"]) ?></li>
                                                        <?php endwhile; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- listsearch-input-item -->

                                    <?php
                                    // Bedrooms logic unchanged, assuming $conn is your mysqli connection
                                    $sql = "SELECT DISTINCT proj_bedroom FROM tbl_proj WHERE proj_bedroom IS NOT NULL AND proj_bedroom != '' AND prj_type_id = 1";
                                    $result = mysqli_query($conn, $sql);

                                    $bedroom_options = [];
                                    $string_values = [];
                                    $numeric_values = [];

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $values = explode(",", $row["proj_bedroom"]);

                                        foreach ($values as $val) {
                                            $val = trim($val);
                                            if ($val === "") continue;

                                            if (is_numeric($val)) {
                                                // numeric (includes 1, 1.5, 3.5)
                                                if (!in_array($val, $numeric_values, true)) {
                                                    $numeric_values[] = $val;
                                                }
                                            } else {
                                                // string (STD, RK, Studio)
                                                if (!in_array($val, $string_values, true)) {
                                                    $string_values[] = $val;
                                                }
                                            }
                                        }
                                    }

                                    // Sort numerically
                                    sort($numeric_values, SORT_NUMERIC); // 1,2,3,4
                                    sort($string_values, SORT_NATURAL | SORT_FLAG_CASE); // BHK, RK, Studio

                                    $bedroom_options = array_merge($string_values, $numeric_values);

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
                                            <select name="proj_bedroom[]"  data-placeholder="Project Bedroom" class=" on-radius no-search-select" id="proj_bedroom" style="width:100%;" multiple>
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
                                            <select name="price_min" data-placeholder="Price Min" class="chosen-select on-radius no-search-select" id="price_min">
                                                <option value="show_all" <?= $selectedMin === "show_all" ? "selected" : "" ?>>Min</option>
                                                <?php foreach ($prices as $price): ?>
                                                    <option value="<?= htmlspecialchars($price) ?>"
                                                        <?= ($selectedMin !== "" && $selectedMin == $price) ? "selected" : "" ?>>
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
                                            <select name="price_max" data-placeholder="Price Max" class="chosen-select on-radius no-search-select" id="price_max">
                                                <option value="show_all" <?= $selectedMax === "show_all" ? "selected" : "" ?>>Max</option>
                                                <?php foreach ($prices as $price): ?>
                                                    <option value="<?= htmlspecialchars($price) ?>"
                                                        <?= ($selectedMax !== "" && $selectedMax == $price) ? "selected" : "" ?>>
                                                        <?= formatPrice($price) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>



                                    <!-- Amenities -->
                                    <div class="col-lg-3">
                                        <div class="hidden-listing_search_wrap">
                                            <div class="more_search-btn">More Options <i class="fa-regular fa-plus"></i></div>
                                            <div class="hidden-listing-filter">
                                                <?php
                                                $amenities_query = mysqli_query($conn, "SELECT featfac_id, featfac_name FROM tbl_proj_featfac WHERE is_active = 1 ORDER BY featfac_name");
                                                $selected_amenities = isset($_GET["amenities"]) ? (array)$_GET["amenities"] : [];
                                                ?>
                                                <div class="hidden-listing-item">
                                                    <div class="filter-tags-title">Amenities</div>
                                                    <div class="filter-tags">
                                                        <ul class="no-list-style">
                                                            <?php while ($amenity = mysqli_fetch_assoc($amenities_query)):
                                                                $id = (int)$amenity["featfac_id"];
                                                                $name = htmlspecialchars($amenity["featfac_name"]);
                                                                $isChecked = in_array($id, $selected_amenities) ? "checked" : "";
                                                            ?>
                                                                <li>
                                                                    <input class="amenity-checkbox" id="amenity-<?= $id ?>" type="checkbox" name="amenities[]" value="<?= $id ?>" <?= $isChecked ?>>
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
                                        <input type="hidden" id="comm_id" name="comm_id" value="<?= isset($_GET['comm_id']) ? htmlspecialchars($_GET['comm_id']) : '' ?>">
                                        <button type="submit" name="submit" class="commentssubmit commentssubmit_fw">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                    <!-- list-searh-input-wrap end-->
                    <div class="mob-filter-overlay cmf fs-wrapper"></div>
                    <!-- list-main-wrap-header-->
                    <div class="list-main-wrap-header box-list-header offplan_pos">
                        <!-- list-main-wrap-title-->
                        <div class="list-main-wrap-title mt-3">
                            <h2>
                                Results :
                                <span class='search_offplan_name'>
                                    <?= htmlspecialchars(
                                        !empty($_GET["search_offplan"]) ? $_GET["search_offplan"] : "All"
                                    ) ?>
                                </span>
                                <strong class="search_offplan_count"><?= $count ?></strong>
                            </h2>

                        </div>
                        <!-- list-main-wrap-title end-->

                    </div>
                    <!-- list-main-wrap-header end-->
                    <!--listing-item-container-->
                    <div class="listing-item-container three-columns-grid off-box offplan_data">

                        <?php
                        if (!empty($_GET)) {
                            $comm = select_query(
                                "tbl_proj",
                                "$whereClause and is_active=1 order by proj_posted_on  desc LIMIT $start,$per_page"
                            );
                        } else {
                            $comm = select_query(
                                "tbl_proj",
                                "prj_type_id=1 and is_active=1 order by proj_posted_on  desc LIMIT $start,$per_page"
                            );
                        }

                        if ($count > 0) {
                            $key = 0;
                            while ($row = mysqli_fetch_array($comm)) {

                                $proj_dev = select_row(
                                    "tbl_developer",
                                    "dev_id=" . $row["prj_dev_id"] . ""
                                );
                                $prj_thumb_arr = explode(",", $row["prj_thumb_img"]);

                                if (count($prj_thumb_arr) > 1) {
                                    $prj_thumb_img = $prj_thumb_arr[0];
                                } else {
                                    $prj_thumb_img = $row["prj_thumb_img"];
                                }
                        ?>
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

                                                            . "Link:" . BASE_URL . "property/" . $row["proj_slug"];

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
                                    <?php $key++;
                                }
                            } else {
                                    ?>
                                    <div class="col-md-12 d-flex align-items-center"></div>
                                    <!-- <h2>No record found</h2> -->
                                    <img src="<?= BASE_URL ?>images/no-property-found.jpeg" alt="No Property Found" style="max-width:100%;">
                                <?php
                            }
                                ?>

                                    </div>
                                    <!--listing-item-container end-->
                                    <div class="pagination-wrap">
                                        <div class="pagination">
                                            <?php include "pagination/paging_offplan.php"; ?>
                                        </div>
                                    </div>
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


            <?php include "common/footer.php"; ?>