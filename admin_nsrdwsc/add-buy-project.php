<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

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
    error_log('Add buy property query failed: ' . mysqli_error($conn));
    return false;
  }
  if ($params) {
    $types = '';
    foreach ($params as $param) {
      $types .= is_int($param) ? 'i' : 's';
    }
    mysqli_stmt_bind_param($stmt, $types, ...$params);
  }
  if (!mysqli_stmt_execute($stmt)) {
    error_log('Add buy property query failed: ' . mysqli_stmt_error($stmt));
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

// What a file really is, told from its first bytes: "image/jpeg", "image/png", "image/gif",
// "image/webp" or "application/pdf". Anything else comes back as "" and is refused.
// PHP's fileinfo extension does this when the server has it. The live server has it switched
// off, and there asking fileinfo stops the page, so the bytes are read here instead.
function addp_file_kind($path)
{
  if (class_exists('finfo')) {
    return (string) (new finfo(FILEINFO_MIME_TYPE))->file($path);
  }
  $handle = is_file($path) ? @fopen($path, 'rb') : false;
  if (!$handle) {
    return '';
  }
  $start = (string) fread($handle, 16);
  fclose($handle);
  if (strncmp($start, "\xFF\xD8\xFF", 3) === 0) {
    return 'image/jpeg';
  }
  if (strncmp($start, "\x89PNG\r\n\x1A\n", 8) === 0) {
    return 'image/png';
  }
  if (strncmp($start, 'GIF87a', 6) === 0 || strncmp($start, 'GIF89a', 6) === 0) {
    return 'image/gif';
  }
  if (strncmp($start, 'RIFF', 4) === 0 && substr($start, 8, 4) === 'WEBP') {
    return 'image/webp';
  }
  if (strncmp($start, '%PDF-', 5) === 0) {
    return 'application/pdf';
  }
  return '';
}

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
  $mime = addp_file_kind($tmp_name);
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

$project_types = addp_query($conn, "SELECT prj_type_id, proj_type_name, proj_type_slug FROM tbl_proj_type WHERE is_active = '1' AND prj_type_id = 7") ?: [];
$developers = addp_query($conn, "SELECT dev_id, dev_heading FROM tbl_developer WHERE is_active = '1' ORDER BY dev_heading") ?: [];
$communities = addp_query($conn, "SELECT comm_id, comm_heading FROM tbl_community WHERE is_active = '1' ORDER BY comm_heading") ?: [];
$inventory_types = addp_query($conn, "SELECT id, inventry_name FROM tbl_inventry_type ORDER BY inventry_name") ?: [];
$amenities = addp_query($conn, "SELECT featfac_id, featfac_name FROM tbl_proj_featfac WHERE is_active = '1' ORDER BY featfac_name") ?: [];
$featured_communities = addp_query($conn, "SELECT comm_id, comm_heading FROM tbl_community WHERE comm_featured = '1' AND is_active = '1' ORDER BY comm_heading") ?: [];
$bedroom_options = ['STD' => 'Studio', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9'];
$type_url_prefix = [7 => 'buy-property/'];

// Text fields: name => longest length kept (0 = no limit, for the editors).
$text_fields = [
  'proj_name' => 255, 'proj_name_on_card' => 255, 'proj_sub_heading' => 255, 'proj_price' => 255, 'proj_pplan_cont' => 255,
  'proj_handover' => 255, 'proj_shower' => 255, 'proj_days' => 255, 'proj_location' => 255, 'proj_bedroom_on_card' => 255, 'proj_area' => 255,
  'proj_email' => 255, 'proj_whatsaapno' => 255, 'proj_mobileno' => 255, 'proj_dev_img_alt' => 255, 'proj_size' => 255,
  'proj_inventory_type' => 255, 'proj_start_price' => 255, 'proj_overview' => 0, 'proj_fea_fac_cont' => 0, 'proj_loc_cont' => 0,
  'proj_loc_img_alt_tag' => 255, 'proj_video' => 255, 'proj_masterplan' => 0, 'proj_mastrpln_img_alt_tag' => 255, 'proj_qr' => 0,
  'proj_qr_link' => 255, 'proj_slug' => 200, 'proj_map' => 0, 'meta_title' => 255, 'meta_keyword' => 255, 'meta_description' => 0,
  'meta_tags' => 0, 'proj_posted_on' => 20, 'home_card_order_no' => 6,
];

$errors = [];
$old = ['is_active' => '1', 'proj_posted_on' => date('Y-m-d'), 'prj_type_id' => 7];
$old_lists = ['proj_bedroom' => [], 'proj_price_type' => [], 'proj_fea_fac_opt' => [], 'proj_comm_feat' => [], 'inventory' => [], 'payplan' => [], 'faq' => []];

// What the server accepts in one save: all files and fields together, and how many files.
// Past the size limit PHP hands over an empty form, so the page checks this before sending (see the script below).
function addp_ini_bytes($value)
{
  $value = trim((string) $value);
  $number = (float) $value;
  $unit = strtolower(substr($value, -1));
  return (int) ($number * (['k' => 1024, 'm' => 1048576, 'g' => 1073741824][$unit] ?? 1));
}
$save_limit = addp_ini_bytes(ini_get('post_max_size'));
$file_count_limit = (int) ini_get('max_file_uploads');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && !$_POST && !$_FILES && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
  // Too much was sent at once: nothing arrived, so the form below is empty again.
  $errors[] = 'The files were too large together (' . round((int) $_SERVER['CONTENT_LENGTH'] / 1048576, 1) . ' MB). The server accepts up to ' . round($save_limit / 1048576) . ' MB in one save, so nothing arrived and the form has to be filled in again. Add fewer or smaller files: the rest of the gallery can be added after saving, from the property\'s gallery.';
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['add_project'])) {

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
  $old['proj_showonhomepage'] = isset($_POST['proj_showonhomepage']) ? '1' : '';
  $old['proj_markfeat'] = isset($_POST['proj_markfeat']) ? '1' : '';
  $old['is_active'] = isset($_POST['is_active']) ? '1' : '';

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

  $payplan_rows = [];
  foreach ((array) ($_POST['payplan_heading'] ?? []) as $i => $heading) {
    $heading = is_string($heading) ? trim($heading) : '';
    $percentage = is_string($_POST['payplan_percentage'][$i] ?? null) ? trim($_POST['payplan_percentage'][$i]) : '';
    $status = is_string($_POST['payplan_status'][$i] ?? null) ? trim($_POST['payplan_status'][$i]) : '';
    if ($heading !== '') {
      $payplan_rows[] = [mb_substr($heading, 0, 255), mb_substr($percentage, 0, 255), mb_substr($status, 0, 255)];
    }
  }
  $faq_rows = [];
  foreach ((array) ($_POST['faq_heading'] ?? []) as $i => $question) {
    $question = is_string($question) ? trim($question) : '';
    $answer = is_string($_POST['faq_descp'][$i] ?? null) ? trim($_POST['faq_descp'][$i]) : '';
    if ($question !== '') {
      $faq_rows[] = [mb_substr($question, 0, 255), $answer];
    }
  }
  $old_lists = ['proj_bedroom' => $bedrooms, 'proj_price_type' => $price_types, 'proj_fea_fac_opt' => $amenity_ids, 'proj_comm_feat' => $featured_ids, 'inventory' => $inventory_ids, 'payplan' => $payplan_rows, 'faq' => $faq_rows];

  // ---------------------------------------------------------------
  // 2. Check it. Nothing is saved and no file is kept until everything is right.
  // ---------------------------------------------------------------
  if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
    $errors[] = 'The form expired. Please check your entries and save again.';
  }
  if (!in_array($old['prj_type_id'], array_map('intval', array_column($project_types, 'prj_type_id')), true)) {
    $errors[] = 'The listing type is missing. Please reload the page.';
  }
  if ($old['prj_dev_id'] !== 0 && !in_array($old['prj_dev_id'], array_map('intval', array_column($developers, 'dev_id')), true)) {
    $errors[] = 'Choose a developer from the list.';
  }
  if ($old['prj_comm_id'] !== 0 && !in_array($old['prj_comm_id'], array_map('intval', array_column($communities, 'comm_id')), true)) {
    $errors[] = 'Choose a community from the list.';
  }
  foreach (['proj_name' => 'Property name', 'proj_location' => 'Location'] as $field => $label) {
    if ($in[$field] === '') {
      $errors[] = "$label is needed.";
    }
  }
  if ($in['proj_email'] !== '' && !filter_var($in['proj_email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'The contact email address does not look right.';
  }
  if (strlen(implode(',', $inventory_ids)) > 55) {
    $errors[] = 'Too many inventory types are ticked. Please choose fewer.';
  }
  $posted_time = strtotime($in['proj_posted_on']);
  if ($in['proj_posted_on'] === '' || $posted_time === false) {
    $errors[] = 'Choose the "posted on" date.';
  }

  // Page address: made from the name when left empty, and never the same as another project's.
  $slug_was_typed = $in['proj_slug'] !== '';
  $slug = addp_slug($slug_was_typed ? $in['proj_slug'] : $in['proj_name']);
  if ($slug === '' && $in['proj_name'] !== '') {
    $errors[] = 'The page address (slug) needs letters or numbers.';
  } elseif ($slug !== '') {
    $taken = addp_query($conn, "SELECT prj_id FROM tbl_proj WHERE proj_slug = ? LIMIT 1", [$slug]);
    if ($taken && $slug_was_typed) {
      $errors[] = 'Another listing already uses the page address "' . $slug . '". Choose a different one.';
    } elseif ($taken) {
      for ($n = 2; $n < 50 && $taken; $n++) {
        $taken = addp_query($conn, "SELECT prj_id FROM tbl_proj WHERE proj_slug = ? LIMIT 1", [$slug . '-' . $n]);
        if (!$taken) {
          $slug = $slug . '-' . $n;
        }
      }
    }
  }

  // Uploads: [field, folder, name prefix, allowed types, size limit, label]
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
  $stored = [];       // field => stored file name
  foreach ($single_uploads as $upload) {
    $stored[$upload[0]] = '';
    $file = $_FILES[$upload[0]] ?? null;
    if (!$file || is_array($file['name'])) {
      continue;
    }
    list($extension, $error) = addp_check_upload($file['name'], $file['tmp_name'], $file['error'], $file['size'], $upload[3], $upload[4], $upload[5]);
    if ($error !== '') {
      $errors[] = $error;
    } elseif ($extension !== '') {
      $stored[$upload[0]] = $upload[2] . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
      $to_move[] = [$file['tmp_name'], $upload[1] . $stored[$upload[0]]];
    }
  }

  $thumb_names = [];
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
  } elseif (count($thumb_names) > 8) {
    $errors[] = 'Up to 8 thumbnail images can be added. Put the rest in the gallery.';
  }

  $gallery_rows = [];  // [stored name, alt text]
  $gallery = $_FILES['gallery_image'] ?? null;
  if ($gallery && is_array($gallery['name'])) {
    foreach ($gallery['name'] as $i => $name) {
      list($extension, $error) = addp_check_upload($name, $gallery['tmp_name'][$i], $gallery['error'][$i], $gallery['size'][$i], ADDP_IMAGE, ADDP_IMAGE_MAX, 'Gallery image ' . ($i + 1));
      if ($error !== '') {
        $errors[] = $error;
      } elseif ($extension !== '') {
        $stored_name = 'gallery_image_' . bin2hex(random_bytes(6)) . '.' . $extension;
        $alt = is_string($_POST['gallery_image_alt'][$i] ?? null) ? mb_substr(trim($_POST['gallery_image_alt'][$i]), 0, 255) : '';
        $gallery_rows[] = [$stored_name, $alt];
        $to_move[] = [$gallery['tmp_name'][$i], 'assets/media/project/gallery/' . $stored_name];
      }
    }
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
    ];

    $saved = addp_query(
      $conn,
      'INSERT INTO tbl_proj (' . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ')',
      array_values($data)
    );

    if (!$saved) {
      foreach ($to_move as $move) {
        if (is_file($move[1])) {
          @unlink($move[1]);
        }
      }
      $errors[] = 'The property could not be saved. Please try again.';
    } else {
      $proj_id = (int) mysqli_insert_id($conn);
      $now = date('Y-m-d H:i:s');

      foreach ($gallery_rows as $row) {
        addp_query($conn, "INSERT INTO tbl_proj_gallery (proj_id, gallery_img, gallery_img_alt, is_active, tstp) VALUES (?, ?, ?, '1', ?)", [$proj_id, $row[0], $row[1], $now]);
      }
      foreach ($payplan_rows as $row) {
        addp_query($conn, "INSERT INTO tbl_proj_payplan (proj_id, payplan_heading, payplan_percentage, payplan_status, is_active, tstp) VALUES (?, ?, ?, ?, '1', ?)", [$proj_id, $row[0], $row[1], $row[2], $now]);
      }
      foreach ($faq_rows as $row) {
        addp_query($conn, "INSERT INTO tbl_proj_faq (proj_id, faq_heading, faq_descp, is_active, tstp) VALUES (?, ?, ?, '1', ?)", [$proj_id, $row[0], $row[1], $now]);
      }

      $logger->log('INSERT', 'tbl_proj', $proj_id, null, $logger->getOldRecordData('tbl_proj', $proj_id), 'New buy property added: ' . $in['proj_name']);

      $another = isset($_POST['save_and_add']);
      header('Location: ' . BASE_URL . ($another ? 'add-buy-project.php?added=' . $proj_id : 'manage-buy-project.php?done=added'));
      exit;
    }
  }
}

$just_added = [];
if (isset($_GET['added']) && (int) $_GET['added'] > 0) {
  $just_added = addp_query($conn, "SELECT prj_id, prj_type_id, proj_name, proj_slug FROM tbl_proj WHERE prj_id = ?", [(int) $_GET['added']]) ?: [];
}

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

              <div class="dre-dash dre-form-page">

                <div class="dre-glass dre-glass-dark dre-hero">
                  <div>
                    <h1>Add buy property</h1>
                    <p>Fields marked <b>*</b> are needed. Everything else can be filled in later from Edit.</p>
                  </div>
                  <div class="dre-actions">
                    <a href="<?= BASE_URL ?>manage-buy-project.php"><i class="bi bi-arrow-left"></i> Back to buy properties</a>
                  </div>
                </div>

                <?php if ($just_added): $added = $just_added[0]; ?>
                  <div class="dre-form-alert dre-form-ok" role="status">
                    <i class="bi bi-check-circle"></i>
                    <div>
                      <strong>&ldquo;<?= addp_e($added['proj_name']) ?>&rdquo; was added.</strong>
                      <a href="<?= BASE_URL ?>edit-buy-project.php?edit=<?= base64_encode($added['prj_id']) ?>">Edit it</a> &middot;
                      <a href="<?= addp_e(BASE_URL_HOME . ($type_url_prefix[(int) $added['prj_type_id']] ?? 'buy-property/') . $added['proj_slug']) ?>" target="_blank" rel="noopener">View on the website</a> &middot;
                      <a href="<?= BASE_URL ?>manage-buy-project.php">All buy properties</a>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($errors): ?>
                  <div class="dre-form-alert dre-form-bad" role="alert" id="addpErrors" tabindex="-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    <div>
                      <strong>The property was not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
                      <ul>
                        <?php foreach ($errors as $error): ?>
                          <li><?= addp_e($error) ?></li>
                        <?php endforeach; ?>
                      </ul>
                      <em>Files have to be chosen again: a browser does not keep them after an error.</em>
                    </div>
                  </div>
                <?php endif; ?>

                <div class="dre-form-layout">

                  <!-- Jump to a part of the form -->
                  <nav class="dre-form-nav" aria-label="Parts of the form">
                    <a href="#sec-basics"><i class="bi bi-card-heading"></i> Basics</a>
                    <a href="#sec-price"><i class="bi bi-cash-coin"></i> Price and units</a>
                    <a href="#sec-media"><i class="bi bi-images"></i> Images and files</a>
                    <a href="#sec-about"><i class="bi bi-text-paragraph"></i> Description</a>
                    <a href="#sec-plan"><i class="bi bi-calendar-check"></i> Payment plan</a>
                    <a href="#sec-faq"><i class="bi bi-question-circle"></i> Questions</a>
                    <a href="#sec-location"><i class="bi bi-geo-alt"></i> Location</a>
                    <a href="#sec-extra"><i class="bi bi-map"></i> Master plan and QR</a>
                    <a href="#sec-seo"><i class="bi bi-search"></i> SEO</a>
                    <a href="#sec-publish"><i class="bi bi-send"></i> Publishing</a>
                  </nav>

                  <!--begin::Form-->
                  <form action="<?= BASE_URL ?>add-buy-project.php" enctype="multipart/form-data" method="post" id="add-project" class="dre-form">
                    <input type="hidden" name="csrf" value="<?= addp_e($_SESSION['admin_csrf']) ?>">
                    <input type="hidden" name="add_project" value="1">

                    <!-- Basics -->
                    <section class="dre-glass dre-form-section" id="sec-basics">
                      <h2><i class="bi bi-card-heading"></i> Basics</h2>
                      <div class="dre-form-grid dre-cols-3">
                        <label class="dre-field">
                          <span>Listing type</span>
                          <select name="prj_type_id" id="addpType" required>
                            <?php foreach ($project_types as $type): ?>
                              <option value="<?= (int) $type['prj_type_id'] ?>" data-prefix="<?= addp_e($type_url_prefix[(int) $type['prj_type_id']] ?? 'buy-property/') ?>" <?= (int) ($old['prj_type_id'] ?? 0) === (int) $type['prj_type_id'] ? 'selected' : '' ?>><?= addp_e($type['proj_type_name']) ?></option>
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
                          <span>Property name <b>*</b></span>
                          <input type="text" name="proj_name" id="addpName" value="<?= $val('proj_name') ?>" maxlength="255" autocomplete="off" placeholder="3 Bedroom Villa in DAMAC Hills 2" required>
                          <small>Shown as the title of the property page.</small>
                        </label>
                        <label class="dre-field">
                          <span>Name on cards</span>
                          <input type="text" name="proj_name_on_card" value="<?= $val('proj_name_on_card') ?>" maxlength="255" autocomplete="off" placeholder="3 Bedroom Villa">
                          <small>A shorter name for listing cards. Empty = the property name is used.</small>
                        </label>
                      </div>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Sub heading</span>
                          <input type="text" name="proj_sub_heading" value="<?= $val('proj_sub_heading') ?>" maxlength="255" autocomplete="off" placeholder="Corner villa with garden">
                        </label>
                        <label class="dre-field">
                          <span>Location <b>*</b></span>
                          <input type="text" name="proj_location" value="<?= $val('proj_location') ?>" maxlength="255" autocomplete="off" placeholder="DAMAC Hills 2, Dubai" required>
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
                        <small>Tick the number of bedrooms. Website filters use this.</small>
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
                          <span>Bathrooms</span>
                          <input type="text" name="proj_shower" id="addpBathrooms" value="<?= $val('proj_shower') ?>" maxlength="255" inputmode="numeric" autocomplete="off" placeholder="2">
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
                        <textarea name="proj_pplan_cont" rows="2" maxlength="255" placeholder="Cash or mortgage"><?= $val('proj_pplan_cont') ?></textarea>
                        <small>One line. The steps go in &ldquo;Payment plan&rdquo; below.</small>
                      </label>
                    </section>

                    <!-- Images and files -->
                    <section class="dre-glass dre-form-section" id="sec-media">
                      <h2><i class="bi bi-images"></i> Images and files</h2>
                      <p class="dre-form-note">Images: JPG, PNG, GIF or WEBP, up to 5 MB each. Documents: PDF or an image, up to 30 MB.<?php if ($save_limit > 0): ?> All files of one save together: up to <?= round($save_limit / 1048576) ?> MB<?= $file_count_limit > 0 ? ' and ' . $file_count_limit . ' files' : '' ?>.<?php endif; ?> <strong id="addpFileTotal"></strong></p>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Thumbnail images <b>*</b></span>
                          <input type="file" name="prj_thumb_img[]" id="addpThumbs" accept="image/jpeg,image/png,image/gif,image/webp" multiple required data-preview="addpThumbPreview">
                          <small>The first one is the listing card image. Up to 8.</small>
                          <div class="dre-previews" id="addpThumbPreview"></div>
                        </label>
                        <label class="dre-field">
                          <span>Inner image</span>
                          <input type="file" name="proj_inner_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="addpInnerPreview">
                          <small>The large image at the top of the property page.</small>
                          <div class="dre-previews" id="addpInnerPreview"></div>
                        </label>
                      </div>
                      <div class="dre-form-grid dre-cols-2">
                        <label class="dre-field">
                          <span>Developer image</span>
                          <input type="file" name="proj_dev_img" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="addpDevPreview">
                          <div class="dre-previews" id="addpDevPreview"></div>
                        </label>
                        <label class="dre-field">
                          <span>Developer image alt text</span>
                          <input type="text" name="proj_dev_img_alt" value="<?= $val('proj_dev_img_alt') ?>" maxlength="255" autocomplete="off" placeholder="Nakheel logo">
                        </label>
                      </div>

                      <h3>Gallery</h3>
                      <div class="dre-repeat" data-repeat="gallery">
                        <div class="dre-repeat-row dre-repeat-gallery">
                          <input type="file" name="gallery_image[]" accept="image/jpeg,image/png,image/gif,image/webp" aria-label="Gallery image">
                          <input type="text" name="gallery_image_alt[]" maxlength="255" placeholder="Describe the picture (alt text)" aria-label="Gallery image alt text">
                          <button type="button" class="dre-icon-btn dre-repeat-remove" title="Remove this row" aria-label="Remove this gallery row"><i class="bi bi-x-lg"></i></button>
                        </div>
                      </div>
                      <button type="button" class="dre-btn dre-repeat-add" data-target="gallery"><i class="bi bi-plus-lg"></i> Add another image</button>

                      <h3>Documents</h3>
                      <div class="dre-form-grid dre-cols-3">
                        <label class="dre-field">
                          <span>Brochure</span>
                          <input type="file" name="proj_brochure" id="addpBrochure" accept="application/pdf,image/jpeg,image/png,image/webp" data-file-link>
                        </label>
                        <label class="dre-field">
                          <span>Payment plan file</span>
                          <input type="file" name="proj_payplan" accept="application/pdf,image/jpeg,image/png,image/webp" data-file-link>
                        </label>
                        <label class="dre-field">
                          <span>Floor plan</span>
                          <input type="file" name="proj_floorplan" accept="application/pdf,image/jpeg,image/png,image/webp" data-file-link>
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

                    <!-- Payment plan -->
                    <section class="dre-glass dre-form-section" id="sec-plan">
                      <h2><i class="bi bi-calendar-check"></i> Payment plan</h2>
                      <p class="dre-form-note">One row per step, for example &ldquo;Down Payment&rdquo; and &ldquo;20%&rdquo;. <span id="addpPlanTotal"></span></p>
                      <div class="dre-repeat" data-repeat="payplan">
                        <?php foreach ($old_lists['payplan'] ?: [['', '', '']] as $row): ?>
                          <div class="dre-repeat-row dre-repeat-plan">
                            <input type="text" name="payplan_heading[]" value="<?= addp_e($row[0]) ?>" maxlength="255" placeholder="Step, e.g. Down Payment" aria-label="Payment step">
                            <input type="text" name="payplan_percentage[]" value="<?= addp_e($row[1]) ?>" maxlength="255" placeholder="20%" aria-label="Percentage" class="addp-percent">
                            <input type="text" name="payplan_status[]" value="<?= addp_e($row[2]) ?>" maxlength="255" placeholder="When, e.g. On booking" aria-label="When it is due">
                            <button type="button" class="dre-icon-btn dre-repeat-remove" title="Remove this step" aria-label="Remove this payment step"><i class="bi bi-x-lg"></i></button>
                          </div>
                        <?php endforeach; ?>
                      </div>
                      <button type="button" class="dre-btn dre-repeat-add" data-target="payplan"><i class="bi bi-plus-lg"></i> Add a step</button>
                    </section>

                    <!-- Questions and answers -->
                    <section class="dre-glass dre-form-section" id="sec-faq">
                      <h2><i class="bi bi-question-circle"></i> Questions and answers</h2>
                      <div class="dre-repeat" data-repeat="faq">
                        <?php foreach ($old_lists['faq'] ?: [['', '']] as $row): ?>
                          <div class="dre-repeat-row dre-repeat-faq">
                            <input type="text" name="faq_heading[]" value="<?= addp_e($row[0]) ?>" maxlength="255" placeholder="Question" aria-label="Question">
                            <textarea name="faq_descp[]" rows="2" placeholder="Answer" aria-label="Answer"><?= addp_e($row[1]) ?></textarea>
                            <button type="button" class="dre-icon-btn dre-repeat-remove" title="Remove this question" aria-label="Remove this question"><i class="bi bi-x-lg"></i></button>
                          </div>
                        <?php endforeach; ?>
                      </div>
                      <button type="button" class="dre-btn dre-repeat-add" data-target="faq"><i class="bi bi-plus-lg"></i> Add a question</button>
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
                        </label>
                      </div>
                      <h3>Contact for this property</h3>
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
                        <input type="text" name="proj_slug" id="addpSlug" value="<?= $val('proj_slug') ?>" maxlength="200" autocomplete="off" placeholder="made from the property name">
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
                          <span><strong>Featured</strong><small>Marks the property as featured.</small></span>
                        </label>
                      </div>
                      <label class="dre-toggle">
                        <input type="checkbox" name="proj_showonhomepage" value="1" id="showHomepage" <?= ($old['proj_showonhomepage'] ?? '') === '1' ? 'checked' : '' ?>>
                        <i></i>
                        <span><strong>Show on the homepage</strong><small>Adds a card for this property to the homepage.</small></span>
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
                        </label>
                      </div>
                    </section>

                    <!-- Always in view: how complete the listing is, and the save buttons -->
                    <div class="dre-form-bar">
                      <div class="dre-form-progress" title="Details that make a listing easy to find and compare">
                        <div><strong id="addpDone">0</strong> of <span id="addpOutOf">8</span> key details filled</div>
                        <div class="dre-track"><i id="addpMeter" style="width: 0%"></i></div>
                        <small id="addpMissing"></small>
                      </div>
                      <div class="dre-form-alert dre-form-bad" role="alert" id="addpFileProblem" tabindex="-1" style="display: none; flex-basis: 100%; margin: 0;">
                        <i class="bi bi-exclamation-triangle"></i>
                        <div><strong>Not saved yet: the files cannot be sent like this.</strong><ul></ul><em>Nothing you typed is lost. Change the files and press Save again.</em></div>
                      </div>
                      <div class="dre-form-bar-buttons">
                        <button type="submit" name="save_and_add" value="1" class="dre-btn">Save and add another</button>
                        <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> Save property</button>
                      </div>
                    </div>
                  </form>
                  <!--end::Form-->
                </div>
              </div>

              <!-- Empty rows the "Add" buttons copy -->
              <template id="tpl-gallery">
                <div class="dre-repeat-row dre-repeat-gallery">
                  <input type="file" name="gallery_image[]" accept="image/jpeg,image/png,image/gif,image/webp" aria-label="Gallery image">
                  <input type="text" name="gallery_image_alt[]" maxlength="255" placeholder="Describe the picture (alt text)" aria-label="Gallery image alt text">
                  <button type="button" class="dre-icon-btn dre-repeat-remove" title="Remove this row" aria-label="Remove this gallery row"><i class="bi bi-x-lg"></i></button>
                </div>
              </template>
              <template id="tpl-payplan">
                <div class="dre-repeat-row dre-repeat-plan">
                  <input type="text" name="payplan_heading[]" maxlength="255" placeholder="Step, e.g. During Construction" aria-label="Payment step">
                  <input type="text" name="payplan_percentage[]" maxlength="255" placeholder="40%" aria-label="Percentage" class="addp-percent">
                  <input type="text" name="payplan_status[]" maxlength="255" placeholder="When it is due" aria-label="When it is due">
                  <button type="button" class="dre-icon-btn dre-repeat-remove" title="Remove this step" aria-label="Remove this payment step"><i class="bi bi-x-lg"></i></button>
                </div>
              </template>
              <template id="tpl-faq">
                <div class="dre-repeat-row dre-repeat-faq">
                  <input type="text" name="faq_heading[]" maxlength="255" placeholder="Question" aria-label="Question">
                  <textarea name="faq_descp[]" rows="2" placeholder="Answer" aria-label="Answer"></textarea>
                  <button type="button" class="dre-icon-btn dre-repeat-remove" title="Remove this question" aria-label="Remove this question"><i class="bi bi-x-lg"></i></button>
                </div>
              </template>
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

        <script>
          // Add project form: helpers that make it quicker to fill in. The server checks everything again on save.
          (function () {
            var form = document.getElementById('add-project');
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
                ['bathrooms', document.getElementById('addpBathrooms').value.trim() !== ''],
                ['thumbnail', document.getElementById('addpThumbs').files.length > 0],
                ['overview', editorText('kt-tinymce-2') !== ''],
                ['meta description', document.getElementById('addpMetaDesc').value.trim() !== ''],
                ['brochure', document.getElementById('addpBrochure').files.length > 0]
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

            // --- Files: checked before sending, because the server drops the whole form when they are too large together ---
            var saveLimit = <?= (int) $save_limit ?>;
            var fileCountLimit = <?= (int) $file_count_limit ?>;
            var fileTotal = document.getElementById('addpFileTotal');
            var fileProblem = document.getElementById('addpFileProblem');
            function megabytes(bytes) {
              return (bytes / 1048576).toFixed(1) + ' MB';
            }
            // Returns what is wrong with the chosen files together (an empty list when they can be sent).
            // One file being too large is checked where it is chosen.
            function fileProblems() {
              var problems = [];
              var total = 0;
              var count = 0;
              form.querySelectorAll('input[type="file"]').forEach(function (input) {
                Array.prototype.forEach.call(input.files, function (file) {
                  total += file.size;
                  count++;
                });
              });
              // A little room is kept for the text of the form.
              var room = saveLimit > 0 ? saveLimit - 1048576 : 0;
              if (room > 0 && total > room) {
                problems.push('All files together are ' + megabytes(total) + '. One save takes up to ' + megabytes(room) + '. Take some out: gallery images can be added after saving, from the property\'s gallery.');
              }
              if (fileCountLimit > 0 && count > fileCountLimit) {
                problems.push(count + ' files are chosen. One save takes up to ' + fileCountLimit + ' files: add the rest of the gallery after saving.');
              }
              if (fileTotal) {
                fileTotal.textContent = count === 0 ? '' : 'Chosen now: ' + count + (count === 1 ? ' file, ' : ' files, ') + megabytes(total) + '.';
                fileTotal.classList.toggle('dre-warn-text', problems.length > 0);
              }
              return problems;
            }
            function showFileProblems(problems) {
              var list = fileProblem.querySelector('ul');
              list.textContent = '';
              problems.forEach(function (problem) {
                var item = document.createElement('li');
                item.textContent = problem;
                list.appendChild(item);
              });
              fileProblem.style.display = problems.length ? 'flex' : 'none';
            }
            form.addEventListener('change', function (event) {
              if (event.target.type === 'file') {
                var problems = fileProblems();
                if (fileProblem.style.display !== 'none') { showFileProblems(problems); }
              }
            });

            // --- Saving ---
            form.addEventListener('submit', function (event) {
              var problems = fileProblems();
              showFileProblems(problems);
              if (problems.length) {
                event.preventDefault();
                fileProblem.focus();
                fileProblem.scrollIntoView({ block: 'center' });
                return;
              }
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

            var errorBox = document.getElementById('addpErrors');
            if (errorBox) { errorBox.focus(); errorBox.scrollIntoView({ block: 'start' }); }
          })();
        </script>
