<?php
include "../db/config.php";

$page = !empty($_POST['page_id']) ? (int)$_POST['page_id'] : 1;
$dev_id = isset($_POST['dev_id']) ? $_POST['dev_id'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$bedroomsArr = isset($_POST['proj_bedroom']) ? $_POST['proj_bedroom'] : [];
$priceMin = isset($_POST['price_min']) ? $_POST['price_min'] : '';
$priceMax = isset($_POST['price_max']) ? $_POST['price_max'] : '';
$amenitiesArr = isset($_POST['amenities']) ? $_POST['amenities'] : [];

$conditions = [];
$conditions[] = "prj_type_id = 3";
$conditions[] = "is_active = 1";

$header_details = select_row('tbl_header_info', 'is_active=1');

// Name search
if (!empty($name)) {
    $nameClean = mysqli_real_escape_string($conn, urldecode($name));
    $conditions[] = "proj_name LIKE '%$nameClean%'";
}

// Bedrooms filter
if (!empty($bedroomsArr)) {
    if (!is_array($bedroomsArr)) {
        $bedroomsArr = explode(",", urldecode($bedroomsArr));
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

// Developer filter
if (!empty($dev_id) && is_numeric($dev_id)) {
    $conditions[] = "prj_dev_id = " . (int)$dev_id;
}

// Price filter
if (!empty($priceMin) && is_numeric($priceMin)) {
    $conditions[] = "proj_price >= " . (int)$priceMin;
}
if (!empty($priceMax) && is_numeric($priceMax) && $priceMax !== "show_all") {
    $conditions[] = "proj_price <= " . (int)$priceMax;
}

// Amenities filter
if (!empty($amenitiesArr) && is_array($amenitiesArr)) {
    $amenityConditions = [];
    $selectedAmenityIds = array_map('intval', $amenitiesArr);
    foreach ($selectedAmenityIds as $aid) {
        $amenityConditions[] = "FIND_IN_SET($aid, proj_fea_fac_opt)";
    }
    if (!empty($amenityConditions)) {
        $conditions[] = "(" . implode(" AND ", $amenityConditions) . ")";
    }
}

$whereClause = implode(" AND ", $conditions);

// Count total
$countSql = select_query("tbl_proj", $whereClause);
$count = mysqli_num_rows($countSql);

$per_page = 9;
$no_of_page = ceil($count / $per_page);
$start = ($page - 1) * $per_page;

$result = select_query("tbl_proj", "$whereClause ORDER BY prj_id desc LIMIT $start, $per_page");

$data = '';

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $proj_dev = select_row("tbl_developer", "dev_id=" . $row["prj_dev_id"]);
        $prj_thumb_arr = explode(",", $row["prj_thumb_img"]);

        ob_start(); ?>
        
        <div class="listing-item re-int-list">
            <div class="geodir-category-listing">
                <div class="geodir-category-img">
                    <a href="<?= BASE_URL ?>ready-to-move-in/<?= $row["proj_slug"] ?>">
                        <div class="boxed-content">
                            <div class="boxed-content-item">
                                <div class="gisp grid-small-pad list-single-gallery three-coulms lightgallery">
                                    <?php if (!empty($prj_thumb_arr[0])): ?>
                                        <div class="">
                                            <div class="grid-item-holder hovzoom">
                                                <img src="<?= BASE_URL_IMG ?>project/thumb/<?= $prj_thumb_arr[0] ?>" alt="">
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="geodir-category-content">
                    <h3><a href="<?= BASE_URL ?>ready-to-move-in/<?= $row["proj_slug"] ?>"><?= $row['proj_name'] ?></a></h3>

                    <div class="geodir-category-location mb-2" style="text-align: left">
                        <a href="javascript:void(0)" class="map-item color-b">
                            <i class="fas fa-map-marker-alt"></i> <?= $row['proj_location'] ?>
                        </a>
                    </div>

                      <!-- ✅ Updated Price Block Starts -->
                    <?php if (is_numeric($row["proj_price"]) && $row["proj_price"] != 0): ?>
                        <div class="price-row" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
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
                    <!-- ✅ Updated Price Block Ends -->

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
                                    <span><?= htmlspecialchars($row["proj_area"]) ?> Sqft</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="geodir-category-footer p-list-footer">
                    <a href="<?= BASE_URL ?>ready-to-move-in/<?= $row["proj_slug"] ?>" class="gcf-company post-card_link-2">
                        View<i class="fa-solid fa-caret-right"></i>
                    </a>
                    <div class="clearfix"></div>
                    <div class="list-contact-icon">
                        <a href="tel:<?=$header_details['header_contact_number']?>" target="_blank" class="pcl_btn">
                            <img src="images/c-icon/call-icon.png" alt="Call Icon" width="100%" title="Call">
                        </a>
                        <a href="https://wa.me/<?=$header_details['header_contact_number']?>" target="_blank" class="pcl_btn">
                            <img src="images/c-icon/whatsapp-icon.png" alt="Whatsapp Icon" width="100%" title="Whatsapp">
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $data .= ob_get_clean();
    }
} else {
    $data = '<h2>No record found</h2>';
}

// Pagination
$paging = '';
$ends_count = 1;
$middle_count = 1;
$dots = true;

if ($page > 1) {
    $paging .= '<a href="javascript:void(0)" data-id="' . ($page - 1) . '" class="prevposts-link paging_international_property"><i class="fa fa-caret-left"></i></a>';
}

for ($i = 1; $i <= $no_of_page; $i++) {
    if ($i == $page) {
        $paging .= '<a class="page-numbers current current-page">' . $i . '</a>';
        $dots = true;
    } elseif (
        $i <= $ends_count ||
        ($i >= $page - $middle_count && $i <= $page + $middle_count) ||
        $i > $no_of_page - $ends_count
    ) {
        $paging .= '<a class="page-numbers paging_international_property" data-id="' . $i . '">' . $i . '</a>';
        $dots = true;
    } elseif ($dots) {
        $paging .= '<a class="page-numbers">&hellip;</a>';
        $dots = false;
    }
}

if ($page < $no_of_page) {
    $paging .= '<a href="javascript:void(0)" data-id="' . ($page + 1) . '" class="nextposts-link paging_international_property"><i class="fa fa-caret-right"></i></a>';
}

echo json_encode([
    'record' => $data,
    'paging' => $paging,
    'count' => $count,
]);
?>
