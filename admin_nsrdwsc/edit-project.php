<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');
include('includes/seo-check.php');

// Nothing below runs unless someone is signed in.
if (empty($_SESSION['email_id'])) {
  exit;
}

$logger = new Logger($conn);

if (empty($_SESSION['admin_csrf'])) {
  $_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
}

function addp_e($text)
{
  return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function addp_query($conn, $sql, array $params = [])
{
  $stmt = mysqli_prepare($conn, $sql);
  if (!$stmt) {
    error_log('Add project query failed: ' . mysqli_error($conn));
    return false;
  }
  if ($params) {
    $types = '';
    foreach ($params as $param) {
      $types .= is_int($param) ? 'i' : 's';
    }
    mysqli_stmt_bind_param($stmt, $types, ...$params);
  }
  try {
    $executed = mysqli_stmt_execute($stmt);
  } catch (mysqli_sql_exception $error) {
    // This server reports database errors as exceptions; the form shows its own message instead of a blank page.
    error_log('Add project query failed: ' . $error->getMessage());
    $executed = false;
  }
  if (!$executed) {
    error_log('Add project query failed: ' . mysqli_stmt_error($stmt));
    mysqli_stmt_close($stmt);
    return false;
  }
  $result = mysqli_stmt_get_result($stmt);
  mysqli_stmt_close($stmt);
  if ($result === false) {
    return true;
  }
  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }
  return $rows;
}

// "Marina Gate 2 – Dubai" -> "marina-gate-2-dubai"
function addp_slug($text)
{
  $text = strtolower(trim((string) $text));
  $text = preg_replace('/[^a-z0-9]+/', '-', $text);
  return trim($text, '-');
}

// What each upload field accepts. Files are checked by extension and by their real
// content, and are stored under a name made here, never the name they arrived with.
const ADDP_IMAGE = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
const ADDP_DOCUMENT = ['pdf' => 'application/pdf'] + ADDP_IMAGE;
const ADDP_IMAGE_MAX = 5 * 1024 * 1024;
const ADDP_DOCUMENT_MAX = 30 * 1024 * 1024;

// Checks one uploaded file. Returns [extension, error]; extension is '' when no file was chosen.
function addp_check_upload($name, $tmp_name, $error, $size, array $allowed, $max_bytes, $label)
{
  if ($error === UPLOAD_ERR_NO_FILE || (string) $name === '') {
    return ['', ''];
  }
  if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE || $size > $max_bytes) {
    return ['', "$label is too large (maximum " . round($max_bytes / 1048576) . ' MB).'];
  }
  if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmp_name)) {
    return ['', "$label could not be uploaded. Please try again."];
  }
  $extension = strtolower(pathinfo((string) $name, PATHINFO_EXTENSION));
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($tmp_name);
  // The name must end in an allowed extension and the content must be an allowed type.
  // A picture saved with the "wrong" picture extension (a PNG named .jpg) is fine: it is
  // stored with the extension that matches what it really is.
  $real_extension = array_search($mime, ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp', 'pdf' => 'application/pdf'], true);
  $same_family = ($extension === 'pdf') === ($real_extension === 'pdf');
  if (!isset($allowed[$extension]) || $real_extension === false || !isset($allowed[$real_extension]) || !$same_family) {
    return ['', "$label must be one of: " . strtoupper(implode(', ', array_unique(array_keys($allowed)))) . '.'];
  }
  return [$real_extension, ''];
}

// Shows the file a field already has, with a way to open or remove it.
// $remove is [class, id attribute, type attribute, name attribute, type] for the remove button, or null.
function editp_current($folder, $name, $is_image, $remove = null)
{
  global $id;
  $name = trim((string) $name);
  if ($name === '') {
    return;
  }
  $url = BASE_URL . 'assets/media/project/' . $folder . '/' . rawurlencode($name);
  echo '<div class="dre-current">';
  if ($is_image) {
    echo '<a href="' . addp_e($url) . '" target="_blank" rel="noopener"><img src="' . addp_e($url) . '" alt="" loading="lazy"></a>';
  } else {
    echo '<a class="dre-file-link" href="' . addp_e($url) . '" target="_blank" rel="noopener"><i class="bi bi-file-earmark-text"></i> Open the current file</a>';
  }
  echo '<span>Current file. Choose a new one to replace it.</span>';
  if ($remove) {
    echo '<button type="button" class="dre-btn dre-current-remove ' . $remove[0] . '" ' . $remove[1] . '="' . (int) $id . '" ' . $remove[2] . '="' . addp_e($remove[4]) . '" ' . $remove[3] . '="' . addp_e($name) . '"><i class="bi bi-trash3"></i> Remove</button>';
  }
  echo '</div>';
}

// Font Awesome icons that can be shown next to an extra detail in the app.
$FEATURE_ICON_LIST = [
  'fa-home' => 'Home', 'fa-building' => 'Building', 'fa-city' => 'City',
  'fa-warehouse' => 'Warehouse', 'fa-store' => 'Store', 'fa-store-alt' => 'Retail',
  'fa-hotel' => 'Hotel', 'fa-landmark' => 'Landmark', 'fa-industry' => 'Industry',
  'fa-door-open' => 'Door Open', 'fa-door-closed' => 'Door Closed', 'fa-window-maximize' => 'Window',
  'fa-ruler-combined' => 'Area / Size', 'fa-expand-arrows-alt' => 'Expand', 'fa-compress-arrows-alt' => 'Compress',
  'fa-bed' => 'Bedroom', 'fa-couch' => 'Living Room', 'fa-bath' => 'Bathroom',
  'fa-swimming-pool' => 'Swimming Pool', 'fa-dumbbell' => 'Gym', 'fa-spa' => 'Spa',
  'fa-hot-tub' => 'Hot Tub', 'fa-umbrella-beach' => 'Beach', 'fa-tree' => 'Garden',
  'fa-leaf' => 'Landscaping', 'fa-seedling' => 'Green Area', 'fa-campground' => 'Camping',
  'fa-hiking' => 'Jogging Track', 'fa-futbol' => 'Football', 'fa-basketball-ball' => 'Basketball',
  'fa-volleyball-ball' => 'Volleyball', 'fa-table-tennis' => 'Table Tennis', 'fa-child' => 'Kids Play Area',
  'fa-baby' => 'Nursery', 'fa-dog' => 'Pet Friendly', 'fa-paw' => 'Pet Area',
  'fa-concierge-bell' => 'Concierge', 'fa-utensils' => 'Restaurant', 'fa-coffee' => 'Cafe',
  'fa-glass-cheers' => 'Lounge / Bar', 'fa-shopping-cart' => 'Supermarket', 'fa-shopping-bag' => 'Shopping',
  'fa-wifi' => 'Wi-Fi', 'fa-tv' => 'TV / Cable', 'fa-phone-alt' => 'Phone Line',
  'fa-mobile-alt' => 'Mobile', 'fa-laptop' => 'Business Center', 'fa-plug' => 'Power Backup',
  'fa-bolt' => 'Electricity', 'fa-lightbulb' => 'Lighting', 'fa-fan' => 'Ventilation',
  'fa-thermometer-half' => 'Central AC', 'fa-snowflake' => 'Air Conditioning', 'fa-sun' => 'Sunny',
  'fa-cloud' => 'Weather', 'fa-wind' => 'Wind', 'fa-tint' => 'Water Supply',
  'fa-water' => 'Waterfront', 'fa-solar-panel' => 'Solar Power', 'fa-recycle' => 'Waste Disposal',
  'fa-shield-alt' => 'Security', 'fa-lock' => 'Gated Community', 'fa-key' => 'Access Control',
  'fa-video' => 'CCTV', 'fa-fire-extinguisher' => 'Fire Safety', 'fa-first-aid' => 'First Aid',
  'fa-wheelchair' => 'Accessible', 'fa-users' => 'Community Hall', 'fa-user-friends' => 'Family Area',
  'fa-handshake' => 'Deal / Agreement', 'fa-briefcase' => 'Business',
  'fa-parking' => 'Parking', 'fa-car' => 'Car', 'fa-car-side' => 'Covered Parking',
  'fa-bus' => 'Bus Access', 'fa-subway' => 'Metro Access', 'fa-train' => 'Train Access',
  'fa-plane' => 'Airport Access', 'fa-ship' => 'Marina', 'fa-bicycle' => 'Cycling Track',
  'fa-road' => 'Road Access', 'fa-traffic-light' => 'Traffic', 'fa-gas-pump' => 'Fuel Station',
  'fa-map-marker-alt' => 'Location', 'fa-map-marked-alt' => 'Prime Location', 'fa-compass' => 'Direction',
  'fa-globe' => 'International', 'fa-clock' => '24/7 Service', 'fa-calendar-alt' => 'Booking',
  'fa-dollar-sign' => 'Price', 'fa-hand-holding-usd' => 'Financing', 'fa-coins' => 'Investment',
  'fa-credit-card' => 'Payment Plan', 'fa-chart-line' => 'ROI', 'fa-percentage' => 'Discount',
  'fa-check-circle' => 'Verified', 'fa-star' => 'Featured', 'fa-gem' => 'Luxury',
  'fa-crown' => 'Premium', 'fa-award' => 'Award Winning', 'fa-trophy' => 'Best in Class',
  'fa-tools' => 'Maintenance', 'fa-hammer' => 'Under Construction', 'fa-paint-roller' => 'Finishing',
  'fa-mountain' => 'Mountain View', 'fa-hospital' => 'Hospital Nearby', 'fa-school' => 'School Nearby',
  'fa-church' => 'Place of Worship', 'fa-university' => 'University Nearby',
];

$id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
$details = $id > 0 ? (addp_query($conn, "SELECT * FROM tbl_proj WHERE prj_id = ?", [$id]) ?: []) : [];
$details = $details ? $details[0] : null;

$project_types = addp_query($conn, "SELECT prj_type_id, proj_type_name, proj_type_slug FROM tbl_proj_type WHERE (is_active = '1' AND prj_type_id NOT IN (7, 8)) OR prj_type_id = ? ORDER BY proj_type_name", [(int) ($details['prj_type_id'] ?? 0)]) ?: [];
$developers = addp_query($conn, "SELECT dev_id, dev_heading FROM tbl_developer WHERE is_active = '1' OR dev_id = ? ORDER BY dev_heading", [(int) ($details['prj_dev_id'] ?? 0)]) ?: [];
$communities = addp_query($conn, "SELECT comm_id, comm_heading FROM tbl_community WHERE is_active = '1' OR comm_id = ? ORDER BY comm_heading", [(int) ($details['prj_comm_id'] ?? 0)]) ?: [];
$inventory_types = addp_query($conn, "SELECT id, inventry_name FROM tbl_inventry_type ORDER BY inventry_name") ?: [];
$amenities = addp_query($conn, "SELECT featfac_id, featfac_name FROM tbl_proj_featfac WHERE is_active = '1' ORDER BY featfac_name") ?: [];
$featured_communities = addp_query($conn, "SELECT comm_id, comm_heading FROM tbl_community WHERE comm_featured = '1' AND is_active = '1' ORDER BY comm_heading") ?: [];
$bedroom_options = ['STD' => 'Studio', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9'];
$type_url_prefix = [1 => 'property/', 2 => 'ready-to-move-in/', 3 => 'international-properties/', 4 => 'property/', 7 => 'buy-property/', 8 => 'rent-property/'];

// Text fields: name => longest length kept (0 = no limit, for the editors).
$text_fields = [
  'proj_name' => 255, 'proj_name_on_card' => 255, 'proj_sub_heading' => 255, 'proj_price' => 255, 'proj_pplan_cont' => 255,
  'proj_handover' => 255, 'proj_days' => 255, 'proj_location' => 255, 'proj_bedroom_on_card' => 255, 'proj_area' => 255, 'proj_shower' => 255,
  'proj_email' => 255, 'proj_whatsaapno' => 255, 'proj_mobileno' => 255, 'proj_dev_img_alt' => 255, 'proj_size' => 255,
  'proj_inventory_type' => 255, 'proj_start_price' => 255, 'proj_overview' => 0, 'proj_fea_fac_cont' => 0, 'proj_loc_cont' => 0,
  'proj_loc_img_alt_tag' => 255, 'proj_video' => 255, 'proj_masterplan' => 0, 'proj_mastrpln_img_alt_tag' => 255, 'proj_qr' => 0,
  'proj_qr_link' => 255, 'proj_slug' => 200, 'proj_map' => 0, 'meta_title' => 255, 'meta_keyword' => 255, 'meta_description' => 0,
  'meta_tags' => 0, 'proj_posted_on' => 20, 'home_card_order_no' => 6, 'longitude' => 255, 'latitude' => 255,
  // Details used by the mobile app
  'homepage_status' => 20, 'balcony' => 50, 'suite_area' => 100, 'common_area' => 100, 'balcony_area' => 100, 'property_type' => 100,
  'floor_number' => 50, 'property_number' => 50, 'property_status' => 50, 'emirate' => 100, 'loan_interest' => 50, 'emi_15_year' => 50,
  'emi_20_year' => 50, 'emi_25_year' => 50, 'property_id' => 50, 'proj_status' => 50, 'prop_user_id' => 11, 'doc_id' => 11,
  'proj_feature_text_1' => 255, 'proj_feature_text_2' => 255, 'proj_feature_text_3' => 255, 'proj_feature_text_4' => 255, 'proj_feature_text_5' => 255,
  'proj_feature_icon_5' => 255,
];
// Labels for the "Other details" part: field => label.
$app_fields = [
  'property_type' => 'Property type', 'property_status' => 'Property status', 'proj_status' => 'Project status', 'emirate' => 'Emirate',
  'floor_number' => 'Floor number', 'property_number' => 'Property number', 'property_id' => 'Property ID', 'homepage_status' => 'Homepage status',
  'balcony' => 'Balcony', 'balcony_area' => 'Balcony area', 'suite_area' => 'Suite area', 'common_area' => 'Common area',
  'loan_interest' => 'Loan interest', 'emi_15_year' => 'EMI (15 years)', 'emi_20_year' => 'EMI (20 years)', 'emi_25_year' => 'EMI (25 years)',
  'prop_user_id' => 'Submitted by user ID', 'doc_id' => 'Document ID',
];
// What the app may show for this project: switch => [label, field whose value it shows].
$app_toggles = [
  'proj_show_down_payment' => ['Down payment', 'proj_pplan_cont'], 'proj_show_payment_plan' => ['Payment plan', 'proj_pplan_cont'],
  'proj_show_handover' => ['Handover', 'proj_handover'], 'proj_show_area' => ['Area', 'proj_area'],
  'proj_show_bedroom' => ['Bedrooms', 'proj_bedroom'], 'proj_show_shower' => ['Bathrooms', 'proj_shower'],
  'proj_show_size' => ['Size', 'proj_size'], 'proj_show_balcony' => ['Balcony', 'balcony'],
];

$errors = [];
$old = [];
$old_lists = ['proj_bedroom' => [], 'proj_price_type' => [], 'proj_fea_fac_opt' => [], 'proj_comm_feat' => [], 'inventory' => [], 'payplan' => [], 'faq' => []];
$icon_class = [1 => '', 2 => '', 3 => '', 4 => ''];   // icon picked for extra details 1 to 4, as "fa-home"
$list_counts = ['gallery' => 0, 'payplan' => 0, 'faq' => 0];

if ($details) {
  // The form starts from what is saved.
  $split = function ($text) {
    return array_values(array_filter(array_map('trim', explode(',', (string) $text)), 'strlen'));
  };
  foreach ($text_fields as $field => $max) {
    $old[$field] = (string) ($details[$field] ?? '');
  }
  $posted_time = strtotime((string) $details['proj_posted_on']);
  $old['proj_posted_on'] = $posted_time ? date('Y-m-d', $posted_time) : '';
  $old['home_card_order_no'] = (int) $details['home_card_order_no'] > 0 ? (string) (int) $details['home_card_order_no'] : '';
  $old['prj_type_id'] = (int) $details['prj_type_id'];
  $old['prj_dev_id'] = (int) $details['prj_dev_id'];
  $old['prj_comm_id'] = (int) $details['prj_comm_id'];
  foreach (array_merge(['proj_showonhomepage', 'proj_markfeat', 'is_active'], array_keys($app_toggles)) as $flag) {
    $old[$flag] = (string) $details[$flag] === '1' ? '1' : '';
  }
  $old_lists['proj_bedroom'] = $split($details['proj_bedroom']);
  $old_lists['proj_price_type'] = $split($details['proj_price_type']);
  $old_lists['proj_fea_fac_opt'] = $split($details['proj_fea_fac_opt']);
  $old_lists['proj_comm_feat'] = $split($details['proj_comm_feat']);
  $old_lists['inventory'] = $split($details['proj_inventory_type_filter']);
  foreach ($icon_class as $n => $unused) {
    if (preg_match('/fa-[a-z0-9-]+/', (string) $details["proj_feature_icon_$n"], $match)) {
      $icon_class[$n] = $match[0];
      // An icon saved earlier that is not in the list stays selectable.
      $FEATURE_ICON_LIST += [$match[0] => $match[0]];
    }
  }
  foreach (['gallery' => 'tbl_proj_gallery', 'payplan' => 'tbl_proj_payplan', 'faq' => 'tbl_proj_faq'] as $key => $table) {
    $counted = addp_query($conn, "SELECT COUNT(*) AS n FROM $table WHERE proj_id = ?", [$id]);
    $list_counts[$key] = $counted ? (int) $counted[0]['n'] : 0;
  }
}

if ($details && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['update_project'])) {

  // ---------------------------------------------------------------
  // 1. Read the form
  // ---------------------------------------------------------------
  $in = [];
  foreach ($text_fields as $field => $max) {
    $value = isset($_POST[$field]) && is_string($_POST[$field]) ? trim($_POST[$field]) : '';
    $in[$field] = $max > 0 ? mb_substr($value, 0, $max) : $value;
  }
  $old = $in;
  $old['prj_type_id'] = (int) ($_POST['prj_type_id'] ?? 0);
  $old['prj_dev_id'] = (int) ($_POST['prj_dev_id'] ?? 0);
  $old['prj_comm_id'] = (int) ($_POST['prj_comm_id'] ?? 0);
  foreach (array_merge(['proj_showonhomepage', 'proj_markfeat', 'is_active'], array_keys($app_toggles)) as $flag) {
    $old[$flag] = isset($_POST[$flag]) ? '1' : '';
  }
  foreach ($icon_class as $n => $unused) {
    $posted_icon = is_string($_POST["proj_feature_icon_$n"] ?? null) ? $_POST["proj_feature_icon_$n"] : '';
    $icon_class[$n] = isset($FEATURE_ICON_LIST[$posted_icon]) ? $posted_icon : '';
  }

  $pick = function ($name, array $allowed) {
    $values = [];
    foreach ((array) ($_POST[$name] ?? []) as $value) {
      if (is_scalar($value) && in_array((string) $value, $allowed, true)) {
        $values[] = (string) $value;
      }
    }
    return array_values(array_unique($values));
  };
  $bedrooms = $pick('proj_bedroom', array_map('strval', array_keys($bedroom_options)));
  $price_types = $pick('proj_price_type', ['year', 'month']);
  $amenity_ids = $pick('proj_fea_fac_opt', array_map('strval', array_column($amenities, 'featfac_id')));
  $featured_ids = $pick('proj_comm_feat', array_map('strval', array_column($featured_communities, 'comm_id')));
  $inventory_ids = array_values(array_intersect(
    array_filter(array_map('trim', explode(',', is_string($_POST['proj_inventory_type_filter'] ?? null) ? $_POST['proj_inventory_type_filter'] : ''))),
    array_map('strval', array_column($inventory_types, 'id'))
  ));
  $old_lists = ['proj_bedroom' => $bedrooms, 'proj_price_type' => $price_types, 'proj_fea_fac_opt' => $amenity_ids, 'proj_comm_feat' => $featured_ids, 'inventory' => $inventory_ids, 'payplan' => [], 'faq' => []];

  // ---------------------------------------------------------------
  // 2. Check it. Nothing is saved and no file is kept until everything is right.
  // ---------------------------------------------------------------
  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if (!in_array($old['prj_type_id'], array_map('intval', array_column($project_types, 'prj_type_id')), true)) {
    $errors[] = 'Choose a project type.';
  }
  if ($old['prj_dev_id'] !== 0 && !in_array($old['prj_dev_id'], array_map('intval', array_column($developers, 'dev_id')), true)) {
    $errors[] = 'Choose a developer from the list.';
  }
  if ($old['prj_comm_id'] !== 0 && !in_array($old['prj_comm_id'], array_map('intval', array_column($communities, 'comm_id')), true)) {
    $errors[] = 'Choose a community from the list.';
  }
  foreach (['proj_name' => 'Project name', 'proj_location' => 'Location'] as $field => $label) {
    if ($in[$field] === '') {
      $errors[] = "$label is needed.";
    }
  }
  if ($in['proj_email'] !== '' && !filter_var($in['proj_email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'The project email address does not look right.';
  }
  if (strlen(implode(',', $inventory_ids)) > 55) {
    $errors[] = 'Too many inventory types are ticked. Please choose fewer.';
  }
  foreach (['prop_user_id' => 'Submitted by user ID', 'doc_id' => 'Document ID'] as $field => $label) {
    if ($in[$field] !== '' && !ctype_digit($in[$field])) {
      $errors[] = "$label must be a number.";
    }
  }
  $posted_time = strtotime($in['proj_posted_on']);
  if ($in['proj_posted_on'] === '' || $posted_time === false) {
    $errors[] = 'Choose the "posted on" date.';
  }

  // Page address: left exactly as it is unless it was changed here, because changing it changes the page's link.
  $saved_slug = (string) $details['proj_slug'];
  if ($in['proj_slug'] === $saved_slug && $saved_slug !== '') {
    $slug = $saved_slug;
  } else {
    $slug = addp_slug($in['proj_slug'] !== '' ? $in['proj_slug'] : ($saved_slug !== '' ? $saved_slug : $in['proj_name']));
    if ($slug === '' && $in['proj_name'] !== '') {
      $errors[] = 'The page address (slug) needs letters or numbers.';
    } elseif ($slug !== '' && addp_query($conn, "SELECT prj_id FROM tbl_proj WHERE proj_slug = ? AND prj_id <> ? LIMIT 1", [$slug, $id])) {
      $errors[] = 'Another project already uses the page address "' . $slug . '". Choose a different one.';
    }
  }

  // Uploads: [field, folder, name prefix, allowed types, size limit, label]. A field without a new file keeps its saved one.
  $single_uploads = [
    ['proj_inner_img', 'assets/media/project/inner-image/', 'proj_inner_img', ADDP_IMAGE, ADDP_IMAGE_MAX, 'Inner image'],
    ['proj_dev_img', 'assets/media/project/dev/', 'proj_dev_img', ADDP_IMAGE, ADDP_IMAGE_MAX, 'Developer image'],
    ['proj_brochure', 'assets/media/project/pdf/', 'proj_brochure', ADDP_DOCUMENT, ADDP_DOCUMENT_MAX, 'Brochure'],
    ['proj_payplan', 'assets/media/project/pdf/', 'proj_payplan', ADDP_DOCUMENT, ADDP_DOCUMENT_MAX, 'Payment plan file'],
    ['proj_floorplan', 'assets/media/project/pdf/', 'proj_floorplan', ADDP_DOCUMENT, ADDP_DOCUMENT_MAX, 'Floor plan'],
    ['proj_loc_img', 'assets/media/project/location/', 'proj_loc_img', ADDP_IMAGE, ADDP_IMAGE_MAX, 'Location image'],
    ['proj_mastrpln_img', 'assets/media/project/masterplan/', 'proj_mastrpln_img', ADDP_IMAGE, ADDP_IMAGE_MAX, 'Master plan image'],
    ['proj_qr_img', 'assets/media/project/qr/', 'proj_qr_img', ADDP_IMAGE, ADDP_IMAGE_MAX, 'QR image'],
    ['home_card_img', 'assets/media/project/home-card-img/', 'home_card', ADDP_IMAGE, ADDP_IMAGE_MAX, 'Home page card image'],
  ];
  $to_move = [];      // [temporary file, final path]
  $replaced = [];     // paths of saved files that a new upload replaces
  $stored = [];       // field => stored file name
  foreach ($single_uploads as $upload) {
    $stored[$upload[0]] = (string) $details[$upload[0]];
    $file = $_FILES[$upload[0]] ?? null;
    if (!$file || is_array($file['name'])) {
      continue;
    }
    list($extension, $error) = addp_check_upload($file['name'], $file['tmp_name'], $file['error'], $file['size'], $upload[3], $upload[4], $upload[5]);
    if ($error !== '') {
      $errors[] = $error;
    } elseif ($extension !== '') {
      if (trim($stored[$upload[0]]) !== '') {
        $replaced[] = $upload[1] . basename($stored[$upload[0]]);
      }
      $stored[$upload[0]] = $upload[2] . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
      $to_move[] = [$file['tmp_name'], $upload[1] . $stored[$upload[0]]];
    }
  }

  // New thumbnails are added after the saved ones.
  $thumb_names = array_values(array_filter(array_map('trim', explode(',', (string) $details['prj_thumb_img'])), 'strlen'));
  $thumb_problem = false;
  $thumbs = $_FILES['prj_thumb_img'] ?? null;
  if ($thumbs && is_array($thumbs['name'])) {
    foreach ($thumbs['name'] as $i => $name) {
      list($extension, $error) = addp_check_upload($name, $thumbs['tmp_name'][$i], $thumbs['error'][$i], $thumbs['size'][$i], ADDP_IMAGE, ADDP_IMAGE_MAX, 'Thumbnail image ' . ($i + 1));
      if ($error !== '') {
        $errors[] = $error;
        $thumb_problem = true;
      } elseif ($extension !== '') {
        $thumb_names[] = 'thumb_img_' . bin2hex(random_bytes(5)) . '.' . $extension;
        $to_move[] = [$thumbs['tmp_name'][$i], 'assets/media/project/thumb/' . end($thumb_names)];
      }
    }
  }
  if (!$thumb_names && !$thumb_problem) {
    $errors[] = 'Add at least one thumbnail image.';
  }

  // ---------------------------------------------------------------
  // 3. Save
  // ---------------------------------------------------------------
  if (!$errors) {
    foreach ($to_move as $move) {
      if (!is_dir(dirname($move[1]))) {
        mkdir(dirname($move[1]), 0755, true);
      }
      if (!move_uploaded_file($move[0], $move[1])) {
        $errors[] = 'A file could not be stored on the server. Nothing was saved.';
        break;
      }
    }
  }

  if (!$errors) {
    $data = [
      'prj_type_id' => $old['prj_type_id'],
      'prj_dev_id' => $old['prj_dev_id'] ?: null,
      // No community is saved as empty, not as 0: the database only accepts a real community here.
      'prj_comm_id' => $old['prj_comm_id'] ?: null,
      'proj_name' => $in['proj_name'],
      'proj_name_on_card' => $in['proj_name_on_card'],
      'proj_sub_heading' => $in['proj_sub_heading'],
      'prj_thumb_img' => implode(',', $thumb_names),
      'proj_price' => $in['proj_price'] !== '' ? $in['proj_price'] : '0',
      'proj_price_type' => implode(',', $price_types),
      'proj_pplan_cont' => $in['proj_pplan_cont'],
      'proj_handover' => $in['proj_handover'],
      'proj_days' => $in['proj_days'],
      'proj_location' => $in['proj_location'],
      'proj_loc_img' => $stored['proj_loc_img'],
      'proj_shower' => $in['proj_shower'],
      'proj_area' => $in['proj_area'],
      'proj_bedroom' => implode(',', $bedrooms),
      'proj_bedroom_on_card' => $in['proj_bedroom_on_card'],
      'proj_email' => $in['proj_email'],
      'proj_whatsaapno' => $in['proj_whatsaapno'],
      'proj_mobileno' => $in['proj_mobileno'],
      'proj_inner_img' => $stored['proj_inner_img'],
      'proj_dev_img' => $stored['proj_dev_img'],
      'proj_dev_img_alt' => $in['proj_dev_img_alt'],
      'proj_size' => $in['proj_size'],
      'proj_inventory_type' => $in['proj_inventory_type'],
      'proj_inventory_type_filter' => implode(',', $inventory_ids),
      'proj_start_price' => $in['proj_start_price'],
      'proj_overview' => $in['proj_overview'],
      'proj_brochure' => $stored['proj_brochure'],
      'proj_payplan' => $stored['proj_payplan'],
      'proj_floorplan' => $stored['proj_floorplan'],
      'proj_fea_fac_cont' => $in['proj_fea_fac_cont'],
      'proj_fea_fac_opt' => implode(',', $amenity_ids),
      'proj_loc_cont' => $in['proj_loc_cont'],
      'proj_loc_img_alt_tag' => $in['proj_loc_img_alt_tag'],
      'proj_video' => $in['proj_video'],
      'proj_masterplan' => $in['proj_masterplan'],
      'proj_mastrpln_img' => $stored['proj_mastrpln_img'],
      'proj_mastrpln_img_alt_tag' => $in['proj_mastrpln_img_alt_tag'],
      'proj_qr' => $in['proj_qr'],
      'proj_qr_link' => $in['proj_qr_link'],
      'proj_qr_img' => $stored['proj_qr_img'],
      'proj_slug' => $slug,
      'proj_map' => $in['proj_map'],
      'meta_title' => $in['meta_title'],
      'meta_keyword' => $in['meta_keyword'],
      'meta_description' => $in['meta_description'],
      'meta_tags' => $in['meta_tags'],
      'proj_comm_feat' => implode(',', $featured_ids),
      'proj_showonhomepage' => $old['proj_showonhomepage'] === '1' ? '1' : '2',
      'home_card_order_no' => (int) $in['home_card_order_no'],
      'home_card_img' => $stored['home_card_img'],
      'proj_markfeat' => $old['proj_markfeat'] === '1' ? '1' : '2',
      'proj_posted_on' => date('Y-m-d', $posted_time),
      'is_active' => $old['is_active'] === '1' ? '1' : '2',
      'longitude' => $in['longitude'],
      'latitude' => $in['latitude'],
      'prop_user_id' => $in['prop_user_id'] !== '' ? (int) $in['prop_user_id'] : null,
      'doc_id' => $in['doc_id'] !== '' ? (int) $in['doc_id'] : null,
      'proj_feature_icon_5' => $in['proj_feature_icon_5'],
    ];
    foreach (array_keys($app_fields) as $field) {
      if ($field !== 'prop_user_id' && $field !== 'doc_id') {
        $data[$field] = $in[$field];
      }
    }
    foreach (array_keys($app_toggles) as $flag) {
      $data[$flag] = $old[$flag] === '1' ? '1' : '2';
    }
    for ($n = 1; $n <= 5; $n++) {
      $data["proj_feature_text_$n"] = $in["proj_feature_text_$n"];
      if ($n <= 4) {
        $data["proj_feature_icon_$n"] = $icon_class[$n] !== '' ? '<i class="fas ' . $icon_class[$n] . '"></i>' : '';
      }
    }

    $old_data = $logger->getOldRecordData('tbl_proj', $id);
    $assignments = [];
    foreach (array_keys($data) as $column) {
      $assignments[] = "$column = ?";
    }
    // The app copy is out of date after any change, so it is marked for the next push.
    $saved = addp_query(
      $conn,
      'UPDATE tbl_proj SET ' . implode(', ', $assignments) . ', app_sync_status = 0, app_sync_date = NOW() WHERE prj_id = ?',
      array_merge(array_values($data), [$id])
    );

    if (!$saved) {
      foreach ($to_move as $move) {
        if (is_file($move[1])) {
          @unlink($move[1]);
        }
      }
      $errors[] = 'The changes could not be saved. Please try again.';
    } else {
      foreach ($replaced as $path) {
        if (is_file($path)) {
          @unlink($path);
        }
      }
      seo_check_save($conn, $id);
      $logger->log('UPDATE', 'tbl_proj', $id, $old_data, $logger->getOldRecordData('tbl_proj', $id), 'Project updated');
      header('Location: ' . BASE_URL . 'edit-project.php?edit=' . base64_encode($id) . '&saved=1');
      exit;
    }
  }
}

$just_saved = $details && isset($_GET['saved']);
$view_url = $details ? BASE_URL_HOME . ($type_url_prefix[(int) $details['prj_type_id']] ?? 'property/') . $details['proj_slug'] : '';

// Helpers for refilling the form after an error
$val = function ($field) use (&$old) {
  return addp_e($old[$field] ?? '');
};
$picked = function ($list, $value) use (&$old_lists) {
  return in_array((string) $value, $old_lists[$list], true);
};
?>

<!--begin::Body-->

<body id="kt_body"
  class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
  <!--begin::Main-->
  <!--begin::Header Mobile-->
  <div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
    <!--begin::Logo-->
    <a href="index.html">
      <img alt="Logo" src="<?= BASE_URL ?>assets/media/logos/logo-light.png" />
    </a>
    <!--end::Logo-->
    <!--begin::Toolbar-->
    <div class="d-flex align-items-center">
      <!--begin::Aside Mobile Toggle-->
      <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
        <span></span>
      </button>
      <!--end::Aside Mobile Toggle-->
      <!--begin::Header Menu Mobile Toggle-->
      <button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle">
        <span></span>
      </button>
      <!--end::Header Menu Mobile Toggle-->
      <!--begin::Topbar Mobile Toggle-->
      <button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle">
        <span class="svg-icon svg-icon-xl">
          <!--begin::Svg Icon | path:<?= BASE_URL ?>assets/media/svg/icons/General/User.svg-->
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
            viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <polygon points="0 0 24 0 24 24 0 24" />
              <path
                d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                fill="#000000" fill-rule="nonzero" opacity="0.3" />
              <path
                d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                fill="#000000" fill-rule="nonzero" />
            </g>
          </svg>
          <!--end::Svg Icon-->
        </span>
      </button>
      <!--end::Topbar Mobile Toggle-->
    </div>
    <!--end::Toolbar-->
  </div>
  <!--end::Header Mobile-->
  <div class="d-flex flex-column flex-root">
    <!--begin::Page-->
    <div class="d-flex flex-row flex-column-fluid page">
      <!--begin::Aside-->
      <?php include('includes/sidebar.php'); ?>
      <!--end::Aside-->
      <!--begin::Wrapper-->
      <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
        <!--begin::Header-->
        <div id="kt_header" class="header header-fixed">
          <!--begin::Container-->
          <div class="container-fluid d-flex align-items-stretch justify-content-between">
            <!--begin::Header Menu Wrapper-->
            <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
              <!--begin::Header Menu-->
              <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">

              </div>
              <!--end::Header Menu-->
            </div>
            <!--end::Header Menu Wrapper-->
            <!--begin::Topbar-->
            <div class="topbar">

              <!--begin::User-->
              <div class="topbar-item">
                <div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2"
                  id="kt_quick_user_toggle">
                  <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                  <span
                    class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= $_SESSION['username'] ?></span>
                  <span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
                    <span
                      class="symbol-label font-size-h5 font-weight-bold"><?= substr($_SESSION['username'], 0, 1) ?></span>
                  </span>
                </div>
              </div>
              <!--end::User-->
            </div>
            <!--end::Topbar-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::Header-->
        <!--begin::Content-->
        <div class="content d-flex flex-column flex-column-fluid dre-dash-content" id="kt_content">

          <!--begin::Entry-->
          <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">

              <!--begin::Add project-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-edit.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-seo-check.css?v=2" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-geniemap.css?v=3" rel="stylesheet" type="text/css" />
              <?php
              // Where the project came from, when it was imported from GenieMap.
              $genie_source = $details && addp_query($conn, "SHOW TABLES LIKE 'tbl_geniemap_import'") ? (addp_query($conn, 'SELECT genie_name, imported_by, imported_at FROM tbl_geniemap_import WHERE prj_id = ?', [(int) $details['prj_id']]) ?: []) : [];
              $genie_source = $genie_source ? $genie_source[0] : null;
              ?>

              <div class="dre-dash dre-form-page">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1>Edit project</h1>
                    <p><?= $details ? addp_e($details['proj_name']) : 'This project could not be found.' ?></p>
                    <?php if ($genie_source): ?>
                      <p class="dre-genie-source"><span class="dre-pill dre-pill-genie"><i class="bi bi-geo-alt-fill"></i> Imported from GenieMap</span> on <?= date('d M Y', strtotime($genie_source['imported_at'])) ?> by <?= addp_e($genie_source['imported_by']) ?><?= $details['is_active'] === '1' ? '' : '. It is still hidden: read it through, then switch it to live.' ?></p>
                    <?php endif; ?>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL ?>manage-project.php"><i class="bi bi-arrow-left"></i> Back to projects</a>
                    <?php if ($details): ?>
                      <a href="<?= addp_e($view_url) ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View on the website</a>
                    <?php endif; ?>
                  </div>
                </div>

                <?php if (!$details): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div><strong>This project could not be found.</strong> It may have been deleted. <a href="<?= BASE_URL ?>manage-project.php">Back to all projects</a></div>
                  </div>
                <?php elseif ($just_saved && !$errors): ?>
                  <div class="dre-form-alert dre-form-ok" role="status" id="editpSaved">
                    <i class="bi bi-check-circle"></i>
                    <div>
                      <strong>The changes were saved.</strong>
                      <a href="<?= addp_e($view_url) ?>" target="_blank" rel="noopener">View on the website</a> &middot;
                      <a href="<?= BASE_URL ?>manage-project.php">All projects</a>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="addpErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>The changes were not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= addp_e($error) ?></li>
                        <?php endforeach; ?>
                      </ul>
                      <em>Files have to be chosen again: a browser does not keep them after an error.</em>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($details): ?>
                <div class="dre-form-layout">

                  <!-- Jump to a part of the form -->
                  <nav class="dre-form-nav" aria-label="Parts of the form">
                    <a href="#sec-basics"><i class="bi bi-card-heading"></i> Basics</a>
                    <a href="#sec-price"><i class="bi bi-cash-coin"></i> Price and units</a>
                    <a href="#sec-media"><i class="bi bi-images"></i> Images and files</a>
                    <a href="#sec-about"><i class="bi bi-text-paragraph"></i> Description</a>
                    <a href="#sec-lists"><i class="bi bi-collection"></i> Gallery, plan, questions</a>
                    <a href="#sec-location"><i class="bi bi-geo-alt"></i> Location</a>
                    <a href="#sec-extra"><i class="bi bi-map"></i> Master plan and QR</a>
                    <a href="#sec-seo"><i class="bi bi-search"></i> SEO</a>
                    <a href="#sec-publish"><i class="bi bi-send"></i> Publishing</a>
                    <a href="#sec-app"><i class="bi bi-phone"></i> Mobile app</a>
                  </nav>

                  <!--begin::Form-->
                  <form action="<?= BASE_URL ?>edit-project.php?edit=<?= base64_encode($id) ?>" enctype="multipart/form-data" method="post" id="add-project" class="dre-form" data-has-thumb="<?= trim((string) $details['prj_thumb_img']) !== '' ? '1' : '' ?>" data-has-brochure="<?= trim((string) $details['proj_brochure']) !== '' ? '1' : '' ?>">
                    <input type="hidden" name="csrf" value="<?= addp_e($_SESSION['admin_csrf']) ?>">
                    <input type="hidden" name="update_project" value="1">

                    <!-- Basics -->
                    <section class="dre-glass dre-form-section" id="sec-basics">
                      <h2><i class="bi bi-card-heading"></i> Basics</h2>
                      <div class="dre-form-grid dre-cols-3">
                        <label class="dre-field">
                          <span>Project type <b>*</b></span>
                          <select name="prj_type_id" id="addpType" required>
                            <?php foreach ($project_types as $type): ?>
                              <option value="<?= (int) $type['prj_type_id'] ?>" data-prefix="<?= addp_e($type_url_prefix[(int) $type['prj_type_id']] ?? 'property/') ?>" <?= (int) ($old['prj_type_id'] ?? 0) === (int) $type['prj_type_id'] ? 'selected' : '' ?>><?= addp_e($type['proj_type_name']) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </label>
                        <label class="dre-field">
                          <span>Developer</span>
                          <select name="prj_dev_id">
                            <option value="">No developer</option>
                            <?php foreach ($developers as $developer): ?>
                              <option value="<?= (int) $developer['dev_id'] ?>" <?= (int) ($old['prj_dev_id'] ?? 0) === (int) $developer['dev_id'] ? 'selected' : '' ?>><?= addp_e($developer['dev_heading']) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </label>
                        <label class="dre-field">
                          <span>Community</span>
                          <select name="prj_comm_id">
                            <option value="">No community</option>
                            <?php foreach ($communities as $community): ?>
                              <option value="<?= (int) $community['comm_id'] ?>" <?= (int) ($old['prj_comm_id'] ?? 0) === (int) $community['comm_id'] ? 'selected' : '' ?>><?= addp_e($community['comm_heading']) ?></option>
                            <?php endforeach; ?>
                          </select>
                        </label>
                      </div>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Project name <b>*</b></span>
                          <input type="text" name="proj_name" id="addpName" value="<?= $val('proj_name') ?>" maxlength="255" autocomplete="off" placeholder="Palm Beach Towers 3 at Palm Jumeirah" required>
                          <small>Shown as the title of the project page.</small>
                        </label>
                        <label class="dre-field">
                          <span>Name on cards</span>
                          <input type="text" name="proj_name_on_card" value="<?= $val('proj_name_on_card') ?>" maxlength="255" autocomplete="off" placeholder="Palm Beach Towers 3">
                          <small>A shorter name for listing cards. Empty = the project name is used.</small>
                        </label>
                      </div>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Sub heading</span>
                          <input type="text" name="proj_sub_heading" value="<?= $val('proj_sub_heading') ?>" maxlength="255" autocomplete="off" placeholder="Beachfront apartments by Nakheel">
                        </label>
                        <label class="dre-field">
                          <span>Location <b>*</b></span>
                          <input type="text" name="proj_location" value="<?= $val('proj_location') ?>" maxlength="255" autocomplete="off" placeholder="Palm Jumeirah, Dubai" required>
                        </label>
                      </div>
                    </section>

                    <!-- Price and units -->
                    <section class="dre-glass dre-form-section" id="sec-price">
                      <h2><i class="bi bi-cash-coin"></i> Price and units</h2>
                      <div class="dre-form-grid dre-cols-3">
                        <label class="dre-field">
                          <span>Price (AED)</span>
                          <input type="text" name="proj_price" id="addpPrice" value="<?= $val('proj_price') ?>" maxlength="255" inputmode="numeric" autocomplete="off" placeholder="1250000">
                          <small id="addpPriceHint">Numbers only, for example 1250000. Empty or 0 shows &ldquo;Call Us&rdquo;.</small>
                        </label>
                        <label class="dre-field">
                          <span>Start price text</span>
                          <input type="text" name="proj_start_price" value="<?= $val('proj_start_price') ?>" maxlength="255" autocomplete="off" placeholder="1.25 Million">
                          <small>How the starting price is written on the page.</small>
                        </label>
                        <div class="dre-field">
                          <span>Price is</span>
                          <div class="dre-chips">
                            <label><input type="checkbox" name="proj_price_type[]" value="year" <?= $picked('proj_price_type', 'year') ? 'checked' : '' ?>><i>Per year</i></label>
                            <label><input type="checkbox" name="proj_price_type[]" value="month" <?= $picked('proj_price_type', 'month') ? 'checked' : '' ?>><i>Per month</i></label>
                          </div>
                          <small>Leave both off for a sale price.</small>
                        </div>
                      </div>
                      <div class="dre-field">
                        <span>Bedrooms</span>
                        <div class="dre-chips" id="addpBedrooms">
                          <?php foreach ($bedroom_options as $value => $label): ?>
                            <label><input type="checkbox" name="proj_bedroom[]" value="<?= addp_e($value) ?>" <?= $picked('proj_bedroom', $value) ? 'checked' : '' ?>><i><?= addp_e($label) ?></i></label>
                          <?php endforeach; ?>
                        </div>
                        <small>Tick every size the project offers. Website filters use this.</small>
                      </div>
                      <div class="dre-form-grid dre-cols-3">
                        <label class="dre-field">
                          <span>Bedrooms on cards</span>
                          <input type="text" name="proj_bedroom_on_card" value="<?= $val('proj_bedroom_on_card') ?>" maxlength="255" autocomplete="off" placeholder="1 - 3">
                          <small>Empty = made from the ticks above.</small>
                        </label>
                        <label class="dre-field">
                          <span>Size</span>
                          <input type="text" name="proj_size" id="addpSize" value="<?= $val('proj_size') ?>" maxlength="255" autocomplete="off" placeholder="1,145 sq ft">
                        </label>
                        <label class="dre-field">
                          <span>Area</span>
                          <input type="text" name="proj_area" value="<?= $val('proj_area') ?>" maxlength="255" autocomplete="off" placeholder="1145">
                        </label>
                      </div>
                      <div class="dre-form-grid dre-cols-3">
                        <label class="dre-field">
                          <span>Handover</span>
                          <input type="text" name="proj_handover" id="addpHandover" value="<?= $val('proj_handover') ?>" maxlength="255" autocomplete="off" placeholder="Handover: Q4 2027">
                          <small>Write a quarter and year, or &ldquo;Completed&rdquo;.</small>
                        </label>
                        <label class="dre-field">
                          <span>Days</span>
                          <input type="text" name="proj_days" value="<?= $val('proj_days') ?>" maxlength="255" autocomplete="off">
                        </label>
                        <label class="dre-field">
                          <span>Property type text</span>
                          <input type="text" name="proj_inventory_type" value="<?= $val('proj_inventory_type') ?>" maxlength="255" autocomplete="off" placeholder="Apartments &amp; Penthouses">
                          <small>Shown on the page, for example &ldquo;1 to 3-BDR Apartments&rdquo;.</small>
                        </label>
                      </div>
                      <div class="dre-form-grid dre-cols-3">
                        <label class="dre-field">
                          <span>Bathrooms</span>
                          <input type="text" name="proj_shower" value="<?= $val('proj_shower') ?>" maxlength="255" autocomplete="off" placeholder="2">
                        </label>
                        <label class="dre-field">
                          <span>Latitude</span>
                          <input type="text" name="latitude" value="<?= $val('latitude') ?>" maxlength="255" inputmode="decimal" autocomplete="off" placeholder="25.0805">
                        </label>
                        <label class="dre-field">
                          <span>Longitude</span>
                          <input type="text" name="longitude" value="<?= $val('longitude') ?>" maxlength="255" inputmode="decimal" autocomplete="off" placeholder="55.1403">
                        </label>
                      </div>
                      <div class="dre-field">
                        <span>Property type filter</span>
                        <input type="hidden" name="proj_inventory_type_filter" id="proj_inventory_type_hidden" value="<?= addp_e(implode(',', $old_lists['inventory'])) ?>">
                        <div class="dre-chips">
                          <?php foreach ($inventory_types as $type): ?>
                            <label><input type="checkbox" class="inventory-checkbox" value="<?= (int) $type['id'] ?>" <?= $picked('inventory', $type['id']) ? 'checked' : '' ?>><i><?= addp_e($type['inventry_name']) ?></i></label>
                          <?php endforeach; ?>
                        </div>
                        <small>Used by the &ldquo;All Types&rdquo; filter on the website.</small>
                      </div>
                      <label class="dre-field">
                        <span>Payment plan summary</span>
                        <textarea name="proj_pplan_cont" rows="2" maxlength="255" placeholder="60/40 Payment Plan"><?= $val('proj_pplan_cont') ?></textarea>
                        <small>One line. The steps go in &ldquo;Payment plan&rdquo; below.</small>
                      </label>
                    </section>

                    <!-- Images and files -->
                    <section class="dre-glass dre-form-section" id="sec-media">
                      <h2><i class="bi bi-images"></i> Images and files</h2>
                      <p class="dre-form-note">Images: JPG, PNG, GIF or WEBP, up to 5 MB each. Documents: PDF or an image, up to 30 MB.</p>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Thumbnail images <b>*</b></span>
                          <input type="file" name="prj_thumb_img[]" id="addpThumbs" accept="image/jpeg,image/png,image/gif,image/webp" multiple data-preview="addpThumbPreview">
                          <small>New images are added after the saved ones. The first one is the listing card image.</small>
                          <div class="dre-previews" id="addpThumbPreview"></div>
                          <div class="dre-current-thumbs">
                            <?php foreach (array_filter(array_map('trim', explode(',', (string) $details['prj_thumb_img'])), 'strlen') as $thumb): ?>
                              <span>
                                <img src="<?= BASE_URL ?>assets/media/project/thumb/<?= addp_e(rawurlencode($thumb)) ?>" alt="" loading="lazy">
                                <button type="button" class="del_thumb_img" del-id="<?= (int) $id ?>" del-name="<?= addp_e($thumb) ?>" title="Delete this image" aria-label="Delete this thumbnail image"><i class="bi bi-x-lg"></i></button>
                              </span>
                            <?php endforeach; ?>
                          </div>
                        </label>
                        <label class="dre-field">
                          <span>Inner image</span>
                          <input type="file" name="proj_inner_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="addpInnerPreview">
                          <small>The large image at the top of the project page.</small>
                          <div class="dre-previews" id="addpInnerPreview"></div>
                          <?php editp_current('inner-image', $details['proj_inner_img'], true, null); ?>
                        </label>
                      </div>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Developer image</span>
                          <input type="file" name="proj_dev_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="addpDevPreview">
                          <div class="dre-previews" id="addpDevPreview"></div>
                          <?php editp_current('dev', $details['proj_dev_img'], true, null); ?>
                        </label>
                        <label class="dre-field">
                          <span>Developer image alt text</span>
                          <input type="text" name="proj_dev_img_alt" value="<?= $val('proj_dev_img_alt') ?>" maxlength="255" autocomplete="off" placeholder="Nakheel logo">
                        </label>
                      </div>

                      <h3>Documents</h3>
                      <div class="dre-form-grid dre-cols-3">
                        <label class="dre-field">
                          <span>Brochure</span>
                          <input type="file" name="proj_brochure" id="addpBrochure" accept="application/pdf,image/jpeg,image/png,image/webp" data-file-link>
                          <?php editp_current('pdf', $details['proj_brochure'], false, ['pdf_remove', 'pdf-id', 'pdf-type', 'pdf-name', 'brochure']); ?>
                        </label>
                        <label class="dre-field">
                          <span>Payment plan file</span>
                          <input type="file" name="proj_payplan" accept="application/pdf,image/jpeg,image/png,image/webp" data-file-link>
                          <?php editp_current('pdf', $details['proj_payplan'], false, ['pdf_remove', 'pdf-id', 'pdf-type', 'pdf-name', 'payplan']); ?>
                        </label>
                        <label class="dre-field">
                          <span>Floor plan</span>
                          <input type="file" name="proj_floorplan" accept="application/pdf,image/jpeg,image/png,image/webp" data-file-link>
                          <?php editp_current('pdf', $details['proj_floorplan'], false, ['pdf_remove', 'pdf-id', 'pdf-type', 'pdf-name', 'floorplan']); ?>
                        </label>
                      </div>
                    </section>

                    <!-- Description -->
                    <section class="dre-glass dre-form-section" id="sec-about">
                      <h2><i class="bi bi-text-paragraph"></i> Description</h2>
                      <div class="dre-field">
                        <label for="kt-tinymce-2"><span>Overview</span></label>
                        <textarea name="proj_overview" class="tinymce-editor" id="kt-tinymce-2" rows="10"><?= $val('proj_overview') ?></textarea>
                      </div>
                      <div class="dre-field">
                        <label for="kt-tinymce-5"><span>Features and facilities</span></label>
                        <textarea name="proj_fea_fac_cont" class="tinymce-editor" id="kt-tinymce-5" rows="10"><?= $val('proj_fea_fac_cont') ?></textarea>
                      </div>
                      <div class="dre-field">
                        <span>Amenities</span>
                        <div class="dre-chips">
                          <?php foreach ($amenities as $amenity): ?>
                            <label><input type="checkbox" name="proj_fea_fac_opt[]" value="<?= (int) $amenity['featfac_id'] ?>" <?= $picked('proj_fea_fac_opt', $amenity['featfac_id']) ? 'checked' : '' ?>><i><?= addp_e($amenity['featfac_name']) ?></i></label>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    </section>

                    <!-- Lists kept on their own pages -->
                    <section class="dre-glass dre-form-section" id="sec-lists">
                      <h2><i class="bi bi-collection"></i> Gallery, payment plan and questions</h2>
                      <p class="dre-form-note">These lists are edited on their own pages. Save your changes here first: the links open in a new tab. <span id="addpPlanTotal" hidden></span></p>
                      <div class="dre-form-grid dre-cols-3">
                        <a class="dre-list-link" href="<?= BASE_URL ?>manage-gallery.php?proj_id=<?= (int) $id ?>" target="_blank" rel="noopener"><i class="bi bi-images"></i><span><strong>Gallery</strong><small><?= $list_counts['gallery'] ?> <?= $list_counts['gallery'] === 1 ? 'image' : 'images' ?></small></span></a>
                        <a class="dre-list-link" href="<?= BASE_URL ?>manage-payplan.php?proj_id=<?= (int) $id ?>" target="_blank" rel="noopener"><i class="bi bi-calendar-check"></i><span><strong>Payment plan steps</strong><small><?= $list_counts['payplan'] ?> <?= $list_counts['payplan'] === 1 ? 'step' : 'steps' ?></small></span></a>
                        <a class="dre-list-link" href="<?= BASE_URL ?>manage-faq.php?proj_id=<?= (int) $id ?>" target="_blank" rel="noopener"><i class="bi bi-question-circle"></i><span><strong>Questions and answers</strong><small><?= $list_counts['faq'] ?> <?= $list_counts['faq'] === 1 ? 'question' : 'questions' ?></small></span></a>
                      </div>
                    </section>

                    <!-- Location -->
                    <section class="dre-glass dre-form-section" id="sec-location">
                      <h2><i class="bi bi-geo-alt"></i> Location</h2>
                      <div class="dre-field">
                        <label for="kt-tinymce-6"><span>About the location</span></label>
                        <textarea name="proj_loc_cont" class="tinymce-editor" id="kt-tinymce-6" rows="10"><?= $val('proj_loc_cont') ?></textarea>
                      </div>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Location image</span>
                          <input type="file" name="proj_loc_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="addpLocPreview">
                          <div class="dre-previews" id="addpLocPreview"></div>
                          <?php editp_current('location', $details['proj_loc_img'], true, ['img_remove', 'img-id', 'img-type', 'img-name', 'location']); ?>
                        </label>
                        <label class="dre-field">
                          <span>Location image alt text</span>
                          <input type="text" name="proj_loc_img_alt_tag" value="<?= $val('proj_loc_img_alt_tag') ?>" maxlength="255" autocomplete="off">
                        </label>
                      </div>
                      <label class="dre-field">
                        <span>Map</span>
                        <input type="text" name="proj_map" value="<?= $val('proj_map') ?>" autocomplete="off" placeholder="Google Maps embed link">
                      </label>
                      <?php if ($featured_communities): ?>
                        <div class="dre-field">
                          <span>Featured communities</span>
                          <div class="dre-chips">
                            <?php foreach ($featured_communities as $community): ?>
                              <label><input type="checkbox" name="proj_comm_feat[]" value="<?= (int) $community['comm_id'] ?>" <?= $picked('proj_comm_feat', $community['comm_id']) ? 'checked' : '' ?>><i><?= addp_e($community['comm_heading']) ?></i></label>
                            <?php endforeach; ?>
                          </div>
                        </div>
                      <?php endif; ?>
                    </section>

                    <!-- Master plan, video, QR, contact -->
                    <section class="dre-glass dre-form-section" id="sec-extra">
                      <h2><i class="bi bi-map"></i> Master plan, video and QR</h2>
                      <label class="dre-field">
                        <span>Video link</span>
                        <input type="text" name="proj_video" value="<?= $val('proj_video') ?>" maxlength="255" autocomplete="off" placeholder="https://www.youtube.com/watch?v=...">
                      </label>
                      <div class="dre-field">
                        <label for="kt-tinymce-7"><span>Master plan</span></label>
                        <textarea name="proj_masterplan" class="tinymce-editor" id="kt-tinymce-7" rows="10"><?= $val('proj_masterplan') ?></textarea>
                      </div>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Master plan image</span>
                          <input type="file" name="proj_mastrpln_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="addpMasterPreview">
                          <div class="dre-previews" id="addpMasterPreview"></div>
                          <?php editp_current('masterplan', $details['proj_mastrpln_img'], true, ['img_remove', 'img-id', 'img-type', 'img-name', 'masterplan']); ?>
                        </label>
                        <label class="dre-field">
                          <span>Master plan image alt text</span>
                          <input type="text" name="proj_mastrpln_img_alt_tag" value="<?= $val('proj_mastrpln_img_alt_tag') ?>" maxlength="255" autocomplete="off">
                        </label>
                      </div>
                      <div class="dre-field">
                        <label for="kt-tinymce-4"><span>QR code text</span></label>
                        <textarea name="proj_qr" class="tinymce-editor" id="kt-tinymce-4" rows="10"><?= $val('proj_qr') ?></textarea>
                      </div>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>QR link</span>
                          <input type="text" name="proj_qr_link" value="<?= $val('proj_qr_link') ?>" maxlength="255" autocomplete="off">
                        </label>
                        <label class="dre-field">
                          <span>QR image</span>
                          <input type="file" name="proj_qr_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="addpQrPreview">
                          <div class="dre-previews" id="addpQrPreview"></div>
                          <?php editp_current('qr', $details['proj_qr_img'], true, ['img_remove', 'img-id', 'img-type', 'img-name', 'qr']); ?>
                        </label>
                      </div>
                      <h3>Contact for this project</h3>
                      <div class="dre-form-grid dre-cols-3">
                        <label class="dre-field">
                          <span>Email</span>
                          <input type="email" name="proj_email" value="<?= $val('proj_email') ?>" maxlength="255" autocomplete="off">
                        </label>
                        <label class="dre-field">
                          <span>WhatsApp number</span>
                          <input type="text" name="proj_whatsaapno" value="<?= $val('proj_whatsaapno') ?>" maxlength="255" inputmode="tel" autocomplete="off">
                        </label>
                        <label class="dre-field">
                          <span>Mobile number</span>
                          <input type="text" name="proj_mobileno" value="<?= $val('proj_mobileno') ?>" maxlength="255" inputmode="tel" autocomplete="off">
                        </label>
                      </div>
                    </section>

                    <!-- SEO -->
                    <section class="dre-glass dre-form-section" id="sec-seo">
                      <h2><i class="bi bi-search"></i> SEO</h2>
                      <label class="dre-field">
                        <span>Page address (slug)</span>
                        <input type="text" name="proj_slug" id="addpSlug" value="<?= $val('proj_slug') ?>" maxlength="200" autocomplete="off" placeholder="made from the project name">
                        <small>The page will be at <code id="addpUrl"><?= addp_e(BASE_URL_HOME) ?>property/</code></small>
                      </label>
                      <label class="dre-field">
                        <span>Meta title</span>
                        <input type="text" name="meta_title" value="<?= $val('meta_title') ?>" maxlength="255" autocomplete="off" data-count="60">
                        <small class="dre-count"></small>
                      </label>
                      <label class="dre-field">
                        <span>Meta description</span>
                        <textarea name="meta_description" id="addpMetaDesc" rows="3" data-count="160"><?= $val('meta_description') ?></textarea>
                        <small class="dre-count"></small>
                      </label>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Meta keywords</span>
                          <input type="text" name="meta_keyword" value="<?= $val('meta_keyword') ?>" maxlength="255" autocomplete="off">
                        </label>
                        <label class="dre-field">
                          <span>Meta tags</span>
                          <textarea name="meta_tags" rows="2"><?= $val('meta_tags') ?></textarea>
                        </label>
                      </div>
                      <?php seo_check_panel($conn, ['prj_id' => $id, 'focus' => isset($_POST['seo_focus']) && is_string($_POST['seo_focus']) ? $_POST['seo_focus'] : null, 'url' => BASE_URL_HOME . rtrim($type_url_prefix[(int) $details['prj_type_id']] ?? 'property/', '/'), 'site' => 'DRE Homes']); ?>
                    </section>

                    <!-- Publishing -->
                    <section class="dre-glass dre-form-section" id="sec-publish">
                      <h2><i class="bi bi-send"></i> Publishing</h2>
                      <div class="dre-form-grid dre-cols-3">
                        <label class="dre-field">
                          <span>Posted on <b>*</b></span>
                          <input type="date" name="proj_posted_on" value="<?= $val('proj_posted_on') ?>" required>
                        </label>
                        <label class="dre-toggle">
                          <input type="checkbox" name="is_active" value="1" id="isActive" <?= ($old['is_active'] ?? '') === '1' ? 'checked' : '' ?>>
                          <i></i>
                          <span><strong>Live on the website</strong><small>Off = saved but hidden.</small></span>
                        </label>
                        <label class="dre-toggle">
                          <input type="checkbox" name="proj_markfeat" value="1" id="markFeatured" <?= ($old['proj_markfeat'] ?? '') === '1' ? 'checked' : '' ?>>
                          <i></i>
                          <span><strong>Featured</strong><small>Marks the project as featured.</small></span>
                        </label>
                      </div>
                      <label class="dre-toggle">
                        <input type="checkbox" name="proj_showonhomepage" value="1" id="showHomepage" <?= ($old['proj_showonhomepage'] ?? '') === '1' ? 'checked' : '' ?>>
                        <i></i>
                        <span><strong>Show on the homepage</strong><small>Adds a card for this project to the homepage.</small></span>
                      </label>
                      <div class="dre-form-grid dre-cols-2" id="homeOrderNo">
                        <label class="dre-field">
                          <span>Homepage card position</span>
                          <input type="number" name="home_card_order_no" value="<?= $val('home_card_order_no') ?>" min="0" max="999" inputmode="numeric" placeholder="1">
                          <small>1 comes first.</small>
                        </label>
                        <label class="dre-field">
                          <span>Homepage card image</span>
                          <input type="file" name="home_card_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="addpHomePreview">
                          <div class="dre-previews" id="addpHomePreview"></div>
                          <?php editp_current('home-card-img', $details['home_card_img'], true, null); ?>
                        </label>
                      </div>
                    </section>

                    <!-- Mobile app -->
                    <section class="dre-glass dre-form-section" id="sec-app">
                      <h2><i class="bi bi-phone"></i> Mobile app</h2>
                      <p class="dre-form-note">What the app shows for this project. After saving, the project is marked &ldquo;Not in the app&rdquo; until it is pushed again.</p>
                      <h3>Details the app may show</h3>
                      <div class="dre-form-grid dre-cols-2">
                        <?php foreach ($app_toggles as $flag => $toggle): $shown_value = trim((string) ($old[$toggle[1]] ?? ($toggle[1] === 'proj_bedroom' ? implode(', ', $old_lists['proj_bedroom']) : ''))); ?>
                          <label class="dre-toggle">
                            <input type="checkbox" name="<?= $flag ?>" value="1" <?= ($old[$flag] ?? '') === '1' ? 'checked' : '' ?>>
                            <i></i>
                            <span><strong><?= addp_e($toggle[0]) ?></strong><small><?= $shown_value !== '' ? addp_e(mb_strimwidth($shown_value, 0, 60, '...')) : 'Empty: nothing to show yet' ?></small></span>
                          </label>
                        <?php endforeach; ?>
                      </div>
                      <h3>Extra details</h3>
                      <p class="dre-form-note">Up to five short points with an icon, for example &ldquo;5 min to Metro&rdquo;.</p>
                      <?php for ($n = 1; $n <= 5; $n++): ?>
                        <div class="dre-form-grid dre-cols-2 dre-app-extra">
                          <?php if ($n <= 4): ?>
                            <label class="dre-field">
                              <span>Icon <?= $n ?></span>
                              <span class="dre-icon-pick">
                                <i class="fas <?= addp_e($icon_class[$n]) ?>" aria-hidden="true"></i>
                                <select name="proj_feature_icon_<?= $n ?>" class="dre-icon-select">
                                  <option value="">No icon</option>
                                  <?php foreach ($FEATURE_ICON_LIST as $class => $label): ?>
                                    <option value="<?= addp_e($class) ?>" <?= $icon_class[$n] === $class ? 'selected' : '' ?>><?= addp_e($label) ?></option>
                                  <?php endforeach; ?>
                                </select>
                              </span>
                            </label>
                          <?php else: ?>
                            <label class="dre-field">
                              <span>Icon 5 (your own tag)</span>
                              <input type="text" name="proj_feature_icon_5" value="<?= $val('proj_feature_icon_5') ?>" maxlength="255" autocomplete="off" placeholder='&lt;i class="fas fa-swimming-pool"&gt;&lt;/i&gt;'>
                            </label>
                          <?php endif; ?>
                          <label class="dre-field">
                            <span>Detail <?= $n ?></span>
                            <input type="text" name="proj_feature_text_<?= $n ?>" value="<?= $val('proj_feature_text_' . $n) ?>" maxlength="255" autocomplete="off" placeholder="5 min to Metro">
                          </label>
                        </div>
                      <?php endfor; ?>
                      <h3>Other details</h3>
                      <div class="dre-form-grid dre-cols-3">
                        <?php foreach ($app_fields as $field => $label): ?>
                          <label class="dre-field">
                            <span><?= addp_e($label) ?></span>
                            <input type="text" name="<?= $field ?>" value="<?= $val($field) ?>" maxlength="<?= (int) $text_fields[$field] ?>" autocomplete="off"<?= $field === 'prop_user_id' || $field === 'doc_id' ? ' inputmode="numeric"' : '' ?>>
                          </label>
                        <?php endforeach; ?>
                      </div>
                    </section>

                    <!-- Always in view: how complete the listing is, and the save buttons -->
                    <div class="dre-form-bar">
                      <div class="dre-form-progress" title="Details that make a listing easy to find and compare">
                        <div><strong id="addpDone">0</strong> of <span id="addpOutOf">8</span> key details filled</div>
                        <div class="dre-track"><i id="addpMeter" style="width: 0%"></i></div>
                        <small id="addpMissing"></small>
                      </div>
                      <div class="dre-seo-lights" title="SEO check: click to see what can be better">
                        <a href="#dreSeo" data-seo-light="seo"><i></i> SEO <em>Not checked</em></a>
                        <a href="#dreSeo" data-seo-light="readability"><i></i> Readability <em>Not checked</em></a>
                      </div>
                      <div class="dre-form-bar-buttons">
                        <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> Save changes</button>
                      </div>
                    </div>
                  </form>
                  <!--end::Form-->
                </div>
                <?php endif; ?>
              </div>

              <!--end::Add project-->

            </div>
            <!--end::Container-->
          </div>
          <!--end::Entry-->
        </div>
        <!--end::Content-->

        <?php
        include('includes/footer-inner.php');
        ?>

        <script src="<?= BASE_URL ?>assets/js/dre-seo-check.js?v=1"></script>
        <script>
          // Add project form: helpers that make it quicker to fill in. The server checks everything again on save.
          (function () {
            var form = document.getElementById('add-project');
            if (!form) { return; }
            if (!form) {
              return;
            }
            var siteUrl = <?= json_encode(BASE_URL_HOME) ?>;
            var byName = function (name) { return form.querySelector('[name="' + name + '"]'); };
            var dirty = false;

            // --- Page address: follows the project name until someone types their own ---
            var nameInput = document.getElementById('addpName');
            var slugInput = document.getElementById('addpSlug');
            var typeSelect = document.getElementById('addpType');
            var urlPreview = document.getElementById('addpUrl');
            var slugTyped = slugInput.value !== '';
            function slugify(text) {
              return text.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            }
            function showUrl() {
              var option = typeSelect.options[typeSelect.selectedIndex];
              var prefix = (option && option.getAttribute('data-prefix')) || 'property/';
              urlPreview.textContent = siteUrl + prefix + (slugInput.value !== '' ? slugify(slugInput.value) : slugify(nameInput.value));
            }
            nameInput.addEventListener('input', showUrl);
            typeSelect.addEventListener('change', showUrl);
            slugInput.addEventListener('input', function () { slugTyped = slugInput.value !== ''; showUrl(); });
            slugInput.addEventListener('blur', function () { if (slugTyped) { slugInput.value = slugify(slugInput.value); showUrl(); } });
            showUrl();

            // --- Price: say what the website will show ---
            var priceInput = document.getElementById('addpPrice');
            var priceHint = document.getElementById('addpPriceHint');
            var priceHintText = priceHint.textContent;
            function checkPrice() {
              var value = priceInput.value.trim();
              priceHint.classList.remove('dre-warn-text');
              if (value === '' || value === '0') {
                priceHint.textContent = priceHintText;
              } else if (/^\d+$/.test(value)) {
                priceHint.textContent = 'Shown as AED ' + Number(value).toLocaleString('en-US') + '.';
              } else {
                priceHint.textContent = 'Use digits only (no commas, "AED" or "M"), otherwise price filters cannot use it.';
                priceHint.classList.add('dre-warn-text');
              }
            }
            priceInput.addEventListener('input', checkPrice);
            checkPrice();

            // --- Property type filter: the ticks are stored as one comma-separated field ---
            var inventoryHidden = document.getElementById('proj_inventory_type_hidden');
            function syncInventory() {
              inventoryHidden.value = Array.prototype.map.call(form.querySelectorAll('.inventory-checkbox:checked'), function (box) { return box.value; }).join(',');
            }
            form.querySelectorAll('.inventory-checkbox').forEach(function (box) { box.addEventListener('change', syncInventory); });
            syncInventory();

            // --- Homepage card fields only when the homepage switch is on ---
            var homeSwitch = document.getElementById('showHomepage');
            var homeFields = document.getElementById('homeOrderNo');
            function toggleHome() { homeFields.style.display = homeSwitch.checked ? '' : 'none'; }
            homeSwitch.addEventListener('change', toggleHome);
            toggleHome();

            // --- Rows that can be added and removed ---
            function planTotal() {
              var total = 0;
              var any = false;
              form.querySelectorAll('.addp-percent').forEach(function (input) {
                var match = /(\d+(?:\.\d+)?)/.exec(input.value);
                if (match) { total += parseFloat(match[1]); any = true; }
              });
              var note = document.getElementById('addpPlanTotal');
              note.textContent = any ? 'The steps add up to ' + (Math.round(total * 10) / 10) + '%.' : '';
              note.style.color = any && Math.abs(total - 100) > 0.01 ? '#a8701a' : '';
              note.style.fontWeight = any && Math.abs(total - 100) > 0.01 ? '600' : '';
            }
            document.querySelectorAll('.dre-repeat-add').forEach(function (button) {
              button.addEventListener('click', function () {
                var key = button.getAttribute('data-target');
                var holder = form.querySelector('[data-repeat="' + key + '"]');
                var row = document.getElementById('tpl-' + key).content.firstElementChild.cloneNode(true);
                holder.appendChild(row);
                var first = row.querySelector('input, textarea');
                if (first) { first.focus(); }
              });
            });
            form.addEventListener('click', function (event) {
              var remove = event.target.closest('.dre-repeat-remove');
              if (!remove) {
                return;
              }
              var row = remove.closest('.dre-repeat-row');
              var holder = row.parentNode;
              if (holder.querySelectorAll('.dre-repeat-row').length > 1) {
                row.remove();
              } else {
                row.querySelectorAll('input, textarea').forEach(function (field) { field.value = ''; });
              }
              planTotal();
              progress();
            });
            form.addEventListener('input', function (event) {
              if (event.target.classList.contains('addp-percent')) { planTotal(); }
            });
            planTotal();

            // --- Picked files: small previews for images, an "open" link for documents ---
            form.addEventListener('change', function (event) {
              var input = event.target;
              if (input.type !== 'file') {
                return;
              }
              var previewId = input.getAttribute('data-preview');
              if (previewId) {
                var box = document.getElementById(previewId);
                box.textContent = '';
                Array.prototype.slice.call(input.files, 0, 8).forEach(function (file) {
                  if (file.type.indexOf('image/') === 0) {
                    var img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.alt = '';
                    box.appendChild(img);
                  }
                });
              }
              if (input.hasAttribute('data-file-link')) {
                var old = input.parentNode.querySelector('.dre-file-link');
                if (old) { old.remove(); }
                if (input.files[0]) {
                  var link = document.createElement('a');
                  link.className = 'dre-file-link';
                  link.href = URL.createObjectURL(input.files[0]);
                  link.target = '_blank';
                  link.rel = 'noopener';
                  link.textContent = 'Open ' + input.files[0].name;
                  input.parentNode.appendChild(link);
                }
              }
              // Say so straight away when a file is too big or of the wrong kind.
              var isDoc = input.hasAttribute('data-file-link');
              var limit = (isDoc ? 30 : 5) * 1024 * 1024;
              var bad = '';
              Array.prototype.forEach.call(input.files, function (file) {
                var okType = file.type.indexOf('image/') === 0 || (isDoc && file.type === 'application/pdf');
                if (!okType) { bad = '"' + file.name + '" is not ' + (isDoc ? 'a PDF or an image.' : 'an image.'); }
                else if (file.size > limit) { bad = '"' + file.name + '" is larger than ' + (isDoc ? 30 : 5) + ' MB.'; }
              });
              input.setCustomValidity(bad);
              input.classList.toggle('dre-invalid', bad !== '');
              if (bad !== '') { input.reportValidity(); }
            });

            // --- Character counts for the search-result title and description ---
            form.querySelectorAll('[data-count]').forEach(function (field) {
              var target = parseInt(field.getAttribute('data-count'), 10);
              var note = field.parentNode.querySelector('.dre-count');
              function count() {
                var length = field.value.length;
                note.textContent = length + ' characters' + (length === 0 ? ' - aim for about ' + target + '.' : (length > target ? ' - search engines usually show about ' + target + '.' : '.'));
                note.classList.toggle('dre-warn-text', length > target);
              }
              field.addEventListener('input', count);
              count();
            });

            // --- How complete the listing is ---
            function editorText(id) {
              var editor = window.tinymce && window.tinymce.get(id);
              var html = editor ? editor.getContent() : document.getElementById(id).value;
              return html.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
            }
            function progress() {
              var checks = [
                ['price', /^\d+$/.test(priceInput.value.trim()) && priceInput.value.trim() !== '0'],
                ['bedrooms', form.querySelectorAll('#addpBedrooms input:checked').length > 0],
                ['size', document.getElementById('addpSize').value.trim() !== ''],
                ['handover', document.getElementById('addpHandover').value.trim() !== ''],
                ['thumbnail', document.getElementById('addpThumbs').files.length > 0 || form.getAttribute('data-has-thumb') === '1'],
                ['overview', editorText('kt-tinymce-2') !== ''],
                ['meta description', document.getElementById('addpMetaDesc').value.trim() !== ''],
                ['brochure', document.getElementById('addpBrochure').files.length > 0 || form.getAttribute('data-has-brochure') === '1']
              ];
              var done = checks.filter(function (check) { return check[1]; }).length;
              document.getElementById('addpDone').textContent = done;
              document.getElementById('addpOutOf').textContent = checks.length;
              document.getElementById('addpMeter').style.width = Math.round(done * 100 / checks.length) + '%';
              var missing = checks.filter(function (check) { return !check[1]; }).map(function (check) { return check[0]; });
              document.getElementById('addpMissing').textContent = missing.length ? 'Still empty: ' + missing.join(', ') : 'All key details are filled in.';
            }
            form.addEventListener('input', function () { dirty = true; progress(); });
            form.addEventListener('change', function () { dirty = true; progress(); });
            progress();
            // The editors load after this script, so look again once they are ready.
            window.addEventListener('load', function () {
              setTimeout(function () {
                progress();
                if (window.tinymce) {
                  window.tinymce.editors.forEach(function (editor) {
                    editor.on('change keyup', function () { dirty = true; progress(); });
                  });
                }
              }, 1200);
            });

            // --- App icons: show the picked icon next to its list ---
            form.querySelectorAll('.dre-icon-select').forEach(function (select) {
              select.addEventListener('change', function () {
                select.parentNode.querySelector('i').className = 'fas ' + select.value;
              });
            });

            // --- Jump links: highlight the part in view ---
            var links = Array.prototype.slice.call(document.querySelectorAll('.dre-form-nav a'));
            var sections = Array.prototype.slice.call(form.querySelectorAll('.dre-form-section'));
            var navWaiting = false;
            function markNav() {
              navWaiting = false;
              // The part in view is the last one whose top has passed the upper third of the screen.
              var line = window.innerHeight / 3;
              var current = sections[0];
              sections.forEach(function (section) {
                if (section.getBoundingClientRect().top <= line) { current = section; }
              });
              links.forEach(function (link) {
                var on = link.getAttribute('href') === '#' + current.id;
                if (on && !link.classList.contains('is-on') && link.parentNode.scrollWidth > link.parentNode.clientWidth) {
                  // On small screens the links are one scrolling row: keep the current one visible.
                  link.parentNode.scrollLeft = link.offsetLeft - 12;
                }
                link.classList.toggle('is-on', on);
              });
            }
            window.addEventListener('scroll', function () {
              if (!navWaiting) { navWaiting = true; window.requestAnimationFrame(markNav); }
            }, { passive: true });
            markNav();

            // --- Saving ---
            form.addEventListener('submit', function () {
              syncInventory();
              if (window.tinymce) { window.tinymce.triggerSave(); }
              dirty = false;
              form.querySelectorAll('button[type="submit"]').forEach(function (button) {
                // Disabled a moment later so the clicked button's own value is still sent.
                setTimeout(function () { button.disabled = true; }, 0);
              });
            });
            // A required field that is empty: mark it, the browser scrolls to it and says why.
            form.addEventListener('invalid', function (event) { event.target.classList.add('dre-invalid'); }, true);
            form.addEventListener('input', function (event) { event.target.classList.remove('dre-invalid'); });
            window.addEventListener('beforeunload', function (event) {
              if (dirty) { event.preventDefault(); event.returnValue = ''; }
            });

            var savedBox = document.getElementById('editpSaved');
            if (savedBox && window.history.replaceState) {
              // Reloading the page should not show "saved" again.
              window.history.replaceState(null, '', window.location.href.replace(/&saved=1/, ''));
            }
            var errorBox = document.getElementById('addpErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
