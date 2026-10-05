<?php
include "../db/config.php";

$page = !empty($_POST['page_id']) ? (int)$_POST['page_id'] : 1;
$dev_id = isset($_POST['dev_id']) ? $_POST['dev_id'] : '';
$comm_id = isset($_POST['comm_id']) ? $_POST['comm_id'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$bedroomsArr = isset($_POST['proj_bedroom']) ? $_POST['proj_bedroom'] : [];
$priceMin = isset($_POST['price_min']) ? $_POST['price_min'] : '';
$priceMax = isset($_POST['price_max']) ? $_POST['price_max'] : '';
$amenitiesArr = isset($_POST['amenities']) ? $_POST['amenities'] : [];

$conditions = [];
$conditions[] = "prj_type_id = 1";
$conditions[] = "is_active = 1";

// Name search
if (!empty($name)) {
    $nameClean = mysqli_real_escape_string($conn, urldecode($name));
    $conditions[] = "proj_name LIKE '%$nameClean%'";
}

// Bedrooms filter
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
        if ($bed === '') continue;

        $bedroomConditions[] = "FIND_IN_SET(UPPER('$bed'), UPPER(REPLACE(proj_bedroom, ' ', '')))";
    }

    if (!empty($bedroomConditions)) {
        $conditions[] = "(" . implode(" OR ", $bedroomConditions) . ")";
    }
}

if (!empty($dev_id) && is_numeric($dev_id)) {
    $conditions[] = "prj_dev_id= " . (int)$dev_id;
}

if (!empty($comm_id)) {
    $commRaw = urldecode($comm_id);
    $commParts = explode(",", $commRaw);
    $commIds = array_map("intval", array_filter($commParts));

    if (!empty($commIds)) {
        $commConditions = [];
        foreach ($commIds as $cid) {
            $commConditions[] = "FIND_IN_SET($cid, proj_comm_feat)";
        }
        $conditions[] = "(" . implode(" OR ", $commConditions) . ")";
    }
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

// Count total matching records
$countSql = select_query('tbl_proj', $whereClause);
$count = mysqli_num_rows($countSql);

$per_page = 9;
$no_of_page = ceil($count / $per_page);
$start = ($page - 1) * $per_page;

// Fetch paginated results
$offplan_query = select_query('tbl_proj', "$whereClause ORDER BY proj_posted_on DESC LIMIT $start, $per_page");

$data = '';
if ($count > 0) {
    while ($rows = mysqli_fetch_array($offplan_query)) {

        $proj_dev = select_row('tbl_developer', 'dev_id=' . $rows['prj_dev_id']);
        $prj_thumb_arr = explode(',', $rows['prj_thumb_img']);

        $prj_thumb_img = count($prj_thumb_arr) > 1 ? $prj_thumb_arr[0] : $rows['prj_thumb_img'];

        // ✅ FIX: decide name first
        $proj_name = !empty($rows['proj_name_on_card'])
            ? $rows['proj_name_on_card']
            : $rows['proj_name'];

        $phone = '+971527543243' . $rows["proj_whatsaapno"];

        $imageUrl = BASE_URL_IMG . "project/thumb/" . $prj_thumb_img;

        $message = "Hi, I'm interested in " . $rows["proj_name"] . ".\n\n"
                    . "Property Image:\n" . $imageUrl . "\n\n"
                    . "Link:\n" . BASE_URL . "property/" . $rows["proj_slug"];

        $encodedMessage = urlencode($message);


        $bedroom1 = '';
        $bedroom2 = '';

        $bedroom_card = $rows['proj_bedroom_on_card'];
        $bedrooms = explode(",", $rows["proj_bedroom"]);
        $bedrooms = array_map('trim', $bedrooms);
        $bedrooms = array_filter($bedrooms);

        if(!empty($bedroom_card)){
            $bedroom1 = '
                <div class="off-p-f1">
                    <div class="off-f-h">Bedroom:</div>
                    <div class="off-f-b">
                        ' . $bedroom_card . '
                    </div>
                </div>
            ';
        }else {
            $first = reset($bedrooms);
            $last  = end($bedrooms);

            if ($first == $last) {
                $bedroom2 = htmlspecialchars($first);
            } else {
                $bedroom2 = htmlspecialchars($first) . ' - ' . htmlspecialchars($last);
            }
            if(!empty($bedroom2)){
            $bedroom1 = '
                <div class="off-p-f1">
                    <div class="off-f-h">Bedroom:</div>
                    <div class="off-f-b">
                        ' . $bedroom2 . '
                    </div>
                </div>
            ';
            }
        }

        // ✅ price block
        if (is_numeric($rows['proj_price']) && $rows['proj_price'] != 0) {
            $price_html = '
                <div class="price-row" style="display: flex; align-items: flex-start; flex-direction: column; gap: 0; font-size:1em;">
                    <div class="text-muted off-f-h">Starting Price: </div>
                    <div class="geodir-category-content_price text-muted currency-price off-f-b ms-1"
                        data-price="' . $rows["proj_price"] . '"
                        data-original-currency="AED"
                        data-original-price="' . $rows["proj_price"] . '">
                        <img src="' . BASE_URL . 'images/symbol/currency-symbol.png" alt="Currency"
                            style="height: 16px; vertical-align: middle;">
                        AED ' . number_format($rows["proj_price"]) . '
                    </div>
                </div>';
        } else {
            $price_html = '
            <div class="geodir-category-content_price text-muted " style="font-size:1em; flex-direction: column; align-items: flex-start;">
                <div class="text-muted off-f-h">Starting Price:</div>
                <div class="off-f-b ms-1" ">Call Us</div>
            </div>
            ';
        }

        // ✅ FINAL HTML
        $data .= '
        <div class="listing-item">
            <div class="geodir-category-listing">
                <div class="geodir-category-img">
                    <a href="' . BASE_URL . 'property/' . $rows['proj_slug'] . '" class="geodir-category-img_item">
                        <div class="off-plan-thumb">
                            <img src="' . BASE_URL_IMG . 'project/thumb/' . $prj_thumb_img . '" alt="">
                        </div>
                        <div class="overlay"></div>
                    </a>
                    <div class="geodir-category-location">
                        <a href="' . BASE_URL . 'property/' . $rows['proj_slug'] . '" class="map-item">
                            <i class="fas fa-map-marker-alt"></i> ' . $rows['proj_location'] . '
                        </a>
                    </div>

                    <ul class="list-single-opt_header_cat off-n">
                        ' . (!empty($rows['proj_handover']) ? '
                            <li>
                                <a href="javascript:void(0)" class="cat-opt">
                                    ' . htmlspecialchars($rows['proj_handover']) . '
                                </a>
                            </li>
                        ' : '') . '
                    </ul>
                 
                </div>

                <div class="geodir-category-content prop-gcc off-n">
                    <h3>
                        <a href="' . BASE_URL . 'property/' . $rows['proj_slug'] . '">
                            ' . htmlspecialchars($proj_name) . '
                        </a>
                    </h3>

                    <h5>
                        <span class="off-n-sp1">By</span> 
                        <span class="off-n-sp2">'.getDeveloper($rows["prj_dev_id"]).'</span>
                    </h5>
                </div>

                <div class="geodir-category-footer list-footer">
                    
                         '. $bedroom1.'
                        

                    <div class="off-p-f2">
                     ' . $price_html . '
                    </div>

                    <div class="clearfix"></div>

                    <div class="list-contact-icon off-p-f3">
                        <a href="https://wa.me/'.$phone.'?text='. $encodedMessage.'" target="_blank" 
                                                    class="pcl_btn">
                                                    
                        <img src="'.BASE_URL .'images/wa-chat-logo-w.png" alt="Whatsapp Icon" width="100%" title="Whatsapp">
                            <span class="d-none d-md-inline">WHATSAPP</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>';
    }
} else {
    $data = '<h2>No record found</h2>';
}


// Pagination
$ends_count = 1;
$middle_count = 1;
$dots = true;
$prev_next = true;
$paging = '';

if ($prev_next && $page && 1 < $page) {
    $paging .= '<a href="javascript:void(0)" data-id="' . ($page - 1) . '" class="prevposts-link paging_offplan"><i class="fa fa-caret-left"></i></a>';
}

for ($i = 1; $i <= $no_of_page; $i++) {
    if ($i == $page) {
        $paging .= '<a class="page-numbers current current-page">' . $i . '</a>';
        $dots = true;
    } else {
        if ($i <= $ends_count || ($page && $i >= $page - $middle_count && $i <= $page + $middle_count) || $i > $no_of_page - $ends_count) {
            $paging .= '<a class="page-numbers paging_offplan" data-id="' . $i . '">' . $i . '</a>';
            $dots = true;
        } elseif ($dots) {
            $paging .= '<a class="page-numbers">&hellip;</a>';
            $dots = false;
        }
    }
}

if ($prev_next && $page && ($page < $no_of_page || -1 == $no_of_page)) {
    $paging .= '<a href="javascript:void(0)" data-id="' . ($page + 1) . '" class="nextposts-link paging_offplan"><i class="fa fa-caret-right"></i></a>';
}

$paging .= "</ul>";

$attr = array('record' => $data, 'paging' => $paging, 'count' => $count);
echo json_encode($attr);
