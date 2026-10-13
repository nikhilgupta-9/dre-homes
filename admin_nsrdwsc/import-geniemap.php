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

function set_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function set_query($conn, $sql, array $params = [])
{
	try {
		$stmt = mysqli_prepare($conn, $sql);
		if (!$stmt) {
			error_log('Settings query failed: ' . mysqli_error($conn));
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
			error_log('Settings query failed: ' . mysqli_stmt_error($stmt));
			mysqli_stmt_close($stmt);
			return false;
		}
		$result = mysqli_stmt_get_result($stmt);
		mysqli_stmt_close($stmt);
	} catch (mysqli_sql_exception $error) {
		// This server reports database errors as exceptions.
		error_log('Settings query failed: ' . $error->getMessage());
		return false;
	}
	if ($result === false) {
		return true;
	}
	$rows = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}

require_once __DIR__ . '/../config/load.php';
require_once __DIR__ . '/includes/GenieMap.php';

$self = BASE_URL . 'import-geniemap.php';
$genie = new GenieMap(secret('geniemap_api_token', ''));
$has_table = (bool) set_query($conn, "SHOW TABLES LIKE 'tbl_geniemap_import'");
$ready = $genie->isConfigured() && $has_table;
// Small copies of the pictures are used in the list when this server can make them.
$small_pictures = GenieMap::canMakeThumbs();
const GM_PER_PAGE = 24;

// ---------------------------------------------------------------
// Turning GenieMap's values into the way this website writes them
// ---------------------------------------------------------------
// "Dubai Hills Estate" and "dubai-hills estate " are the same name.
function gm_key($name, array $drop = [])
{
	$name = ' ' . preg_replace('/[^a-z0-9]+/', ' ', strtolower((string) $name)) . ' ';
	foreach ($drop as $word) {
		$name = str_replace(' ' . $word . ' ', ' ', $name);
	}
	return trim(preg_replace('/\s+/', ' ', $name));
}
// Finds our record with the same name as GenieMap's: [id => name] in, id out (0 = none).
function gm_match($name, array $ours, array $drop = [])
{
	$wanted = gm_key($name, $drop);
	if ($wanted === '') {
		return 0;
	}
	foreach ($ours as $id => $our_name) {
		if (gm_key($our_name, $drop) === $wanted) {
			return (int) $id;
		}
	}
	// Fallback: match without standalone/trailing numbers (e.g. "Warsan 4" -> "Warsan" matches "Al Warsan")
	$wanted_nodigits = trim(preg_replace('/\b\d+\b/', '', $wanted));
	if ($wanted_nodigits !== '') {
		foreach ($ours as $id => $our_name) {
			$our_key = gm_key($our_name, $drop);
			$our_nodigits = trim(preg_replace('/\b\d+\b/', '', $our_key));
			if ($our_nodigits === $wanted_nodigits) {
				return (int) $id;
			}
		}
	}
	return 0;
}
// "2028-01-01T00:00:00Z" -> "Handover: Q1 2028"
function gm_handover($iso)
{
	$time = strtotime((string) $iso);
	return $time && $time > 86400 ? 'Handover: Q' . (int) ceil((int) date('n', $time) / 3) . ' ' . date('Y', $time) : '';
}
// ["Studio", "1 BR", "3 BR"] -> [['STD', '1', '3'], 'STD, 1 - 3']
function gm_bedrooms(array $layouts)
{
	$studio = false;
	$numbers = [];
	foreach ($layouts as $layout) {
		if (stripos($layout, 'studio') !== false) {
			$studio = true;
		} elseif (preg_match('/^\s*(\d)(?:\.\d)?\s*(BR|bed)/i', $layout, $match) && (int) $match[1] >= 1) {
			$numbers[(int) $match[1]] = true;
		}
	}
	ksort($numbers);
	$numbers = array_keys($numbers);
	$range = !$numbers ? '' : (count($numbers) === 1 ? (string) $numbers[0] : $numbers[0] . ' - ' . end($numbers));
	return [array_merge($studio ? ['STD'] : [], array_map('strval', $numbers)), trim(($studio ? 'STD' : '') . ($studio && $range !== '' ? ', ' : '') . $range)];
}
// ["Apartment", "Villa"] -> "Apartments & Villas"
function gm_type_text(array $types)
{
	$plural = array_values(array_unique(array_map(function ($type) { return preg_match('/s$/i', $type) ? $type : $type . 's'; }, $types)));
	if (count($plural) <= 1) {
		return (string) ($plural[0] ?? '');
	}
	$last = array_pop($plural);
	return implode(', ', $plural) . ' & ' . $last;
}
function gm_money($amount)
{
	return $amount > 0 ? 'AED ' . number_format((float) $amount) : 'Price on request';
}
function gm_slug($text)
{
	return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(trim((string) $text))), '-');
}
// Plain text with line breaks -> paragraphs
function gm_paragraphs($text)
{
	$html = '';
	foreach (preg_split('/\R+/', trim((string) $text)) as $line) {
		if (trim($line) !== '') {
			$html .= '<p>' . htmlspecialchars(trim($line), ENT_QUOTES, 'UTF-8') . '</p>';
		}
	}
	return $html;
}
// The short line shown on project cards: "60/40 Payment Plan" when the plan ends with a share on completion.
function gm_plan_title(array $plan)
{
	$items = array_values($plan['items'] ?? []);
	$last = $items ? end($items) : null;
	if ($last && preg_match('/^(\d+)\s*%$/', trim((string) ($last['name'] ?? '')), $match) && preg_match('/completion|handover/i', (string) ($last['description'] ?? '')) && (int) $match[1] > 0 && (int) $match[1] < 100) {
		return (100 - (int) $match[1]) . '/' . (int) $match[1] . ' Payment Plan';
	}
	$name = trim((string) ($plan['name'] ?? ''));
	return $name !== '' && !preg_match('/^payment plan$/i', $name) ? $name : 'Easy Payment Plan';
}
// Our amenity for a GenieMap amenity, by what its name says.
function gm_amenity_ids(array $amenities, array $ours)
{
	$words = ['gym' => 'gym|fitness', 'kids playground' => 'kid|play ?(area|ground)|children', 'park' => 'park|garden', 'nursery' => 'nursery|daycare', 'retail outlets' => 'retail|shop|mall|supermarket', 'school' => 'school', 'swimming pool' => 'pool', 'fine dining' => 'restaurant|dining|cafe'];
	$ids = [];
	foreach ($ours as $id => $name) {
		$pattern = $words[strtolower(trim($name))] ?? null;
		foreach ($pattern ? $amenities : [] as $amenity) {
			if (preg_match('/\b(' . $pattern . ')/i', (string) ($amenity['name'] ?? ''))) {
				$ids[] = (int) $id;
				break;
			}
		}
	}
	return $ids;
}

// Our own lists, to match against
$our_developers = [];
foreach (set_query($conn, 'SELECT dev_id, dev_heading FROM tbl_developer ORDER BY dev_heading') ?: [] as $row) {
	$our_developers[(int) $row['dev_id']] = trim((string) $row['dev_heading']);
}
$our_communities = [];
foreach (set_query($conn, 'SELECT comm_id, comm_heading FROM tbl_community ORDER BY comm_heading') ?: [] as $row) {
	$our_communities[(int) $row['comm_id']] = trim((string) $row['comm_heading']);
}
$our_types = [];
foreach (set_query($conn, "SELECT prj_type_id, proj_type_name FROM tbl_proj_type WHERE prj_type_id IN (1, 2, 3, 4) ORDER BY prj_type_id") ?: [] as $row) {
	$our_types[(int) $row['prj_type_id']] = (string) $row['proj_type_name'];
}
$developer_noise = ['properties', 'property', 'developers', 'developer', 'developments', 'development', 'real', 'estate', 'group', 'holding', 'holdings', 'llc', 'the'];
$community_noise = ['al', 'the', 'city', 'area', 'community', 'phase', 'estate', 'estates'];

// Projects that were imported before: GenieMap id => our project
$imported = [];
foreach ($has_table ? (set_query($conn, 'SELECT g.genie_id, g.prj_id, g.imported_at, p.proj_name, p.is_active FROM tbl_geniemap_import g JOIN tbl_proj p ON p.prj_id = g.prj_id') ?: []) : [] as $row) {
	$imported[(int) $row['genie_id']] = $row;
}

// ---------------------------------------------------------------
// Actions
// ---------------------------------------------------------------
$errors = [];
$view_id = (int) ($_POST['genie_id'] ?? ($_GET['view'] ?? 0));
$post = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
$token_ok = $post && is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);

if ($post && isset($_POST['refresh_list'])) {
	if ($token_ok && $ready) {
		@set_time_limit(300);
		$genie->projects(true);
	}
	header('Location: ' . $self . '?done=' . ($token_ok ? 'refreshed' : 'expired'));
	exit;
}

$detail = null;
if ($ready && $view_id > 0) {
	$detail = $genie->project($view_id);
	if ($detail === null) {
		$errors[] = $genie->error !== '' ? $genie->error : 'That project could not be read from GenieMap.';
	}
}

// What the import form holds (also when a save was refused)
$form = ['proj_name' => '', 'prj_dev_id' => 0, 'prj_comm_id' => 0, 'prj_type_id' => 1, 'main' => 0, 'pictures' => [], 'documents' => []];
// What a GenieMap document can be saved as on the project: column => label
$document_roles = ['proj_brochure' => 'Brochure', 'proj_floorplan' => 'Floor plan', 'proj_payplan' => 'Payment plan'];
const GM_MAX_PICTURES = 30;
if ($detail) {
	$own_name = GenieMap::cleanName($detail['name'] ?? '');
	$district = trim((string) ($detail['district']['name'] ?? ''));
	$form['proj_name'] = $own_name . ($district !== '' && stripos($own_name, $district) === false ? ' at ' . $district : '');
	$form['prj_dev_id'] = gm_match($detail['developer']['name'] ?? '', $our_developers, $developer_noise);
	$form['prj_comm_id'] = gm_match($district, $our_communities, $community_noise);
	// To begin with: the first picture is the main one and the next ten go to the gallery.
	$form['pictures'] = range(0, min(10, max(0, count($detail['images'] ?? []) - 1)));
}

if ($post && isset($_POST['import_project']) && $detail) {
	$form['proj_name'] = mb_substr(trim(is_string($_POST['proj_name'] ?? null) ? $_POST['proj_name'] : ''), 0, 255);
	$form['prj_dev_id'] = (int) ($_POST['prj_dev_id'] ?? 0);
	$form['prj_comm_id'] = (int) ($_POST['prj_comm_id'] ?? 0);
	$form['prj_type_id'] = (int) ($_POST['prj_type_id'] ?? 0);
	$images = array_values(array_filter($detail['images'] ?? [], 'is_string'));
	$documents = array_values(array_filter($detail['documents'] ?? [], 'is_string'));
	// The ticked pictures, by their place in GenieMap's list; the main one is always among them.
	$form['main'] = (int) ($_POST['main'] ?? 0);
	$form['pictures'] = [];
	foreach (is_array($_POST['pictures'] ?? null) ? $_POST['pictures'] : [] as $index) {
		if (is_string($index) && ctype_digit($index) && isset($images[(int) $index])) {
			$form['pictures'][(int) $index] = (int) $index;
		}
	}
	$form['pictures'] = array_values($form['pictures']);
	sort($form['pictures']);
	// What each document is imported as: place in GenieMap's list => column of the project
	$form['documents'] = [];
	foreach (is_array($_POST['documents'] ?? null) ? $_POST['documents'] : [] as $index => $role) {
		if (is_string($role) && $role !== '' && isset($documents[(int) $index])) {
			$form['documents'][(int) $index] = $role;
		}
	}

	if (!$token_ok) {
		$errors[] = 'The form expired. Please check the choices and import again.';
	}
	if (isset($imported[$view_id])) {
		$errors[] = 'This project was already imported.';
	}
	if ($form['proj_name'] === '') {
		$errors[] = 'The project needs a name.';
	}
	if (!isset($our_developers[$form['prj_dev_id']])) {
		$errors[] = 'Choose the developer. If it is not in the list, add the developer first.';
	}
	if ($form['prj_comm_id'] !== 0 && !isset($our_communities[$form['prj_comm_id']])) {
		$errors[] = 'Choose a community from the list, or none.';
	}
	if (!isset($our_types[$form['prj_type_id']])) {
		$errors[] = 'Choose what kind of project this is.';
	}
	if (!$images) {
		$errors[] = 'GenieMap has no picture for this project, and a project here needs at least one. Add it by hand instead.';
	} elseif (!$form['pictures']) {
		$errors[] = 'Tick at least one picture: a project needs a main picture.';
	} elseif (count($form['pictures']) > GM_MAX_PICTURES + 1) {
		$errors[] = 'Up to ' . (GM_MAX_PICTURES + 1) . ' pictures can be imported at once. More can be added on the project form later.';
	}
	if ($form['pictures'] && !in_array($form['main'], $form['pictures'], true)) {
		// The main picture was unticked: the first ticked one takes its place.
		$form['main'] = $form['pictures'][0];
	}
	foreach ($form['documents'] as $role) {
		if (!isset($document_roles[$role])) {
			$errors[] = 'Choose what each document is from the list.';
			break;
		}
	}
	if (count($form['documents']) !== count(array_unique($form['documents']))) {
		$errors[] = 'Two documents were given the same use. A project holds one brochure, one floor plan and one payment plan file.';
	}

	$stored = [];       // files written during this import, removed again if it fails
	if (!$errors) {
		@set_time_limit(300);
		$media = 'assets/media/project/';
		$others = array_values(array_diff($form['pictures'], [$form['main']]));
		$thumb = GenieMap::downloadImage($images[$form['main']], $media . 'thumb/', 'thumb_img');
		if ($thumb === '') {
			$errors[] = 'The main picture could not be copied from GenieMap. Nothing was imported; please try again.';
		} else {
			$stored[] = $media . 'thumb/' . $thumb;
			// The banner of the project page: the next ticked picture, or the main one again.
			$inner = GenieMap::downloadImage($images[$others[0] ?? $form['main']], $media . 'inner-image/', 'proj_inner_img');
			if ($inner !== '') {
				$stored[] = $media . 'inner-image/' . $inner;
			}
			$gallery = [];
			foreach ($others as $index) {
				$name = GenieMap::downloadImage($images[$index], $media . 'gallery/', 'gallery_image');
				if ($name !== '') {
					$gallery[] = $name;
					$stored[] = $media . 'gallery/' . $name;
				}
			}
			// The documents that were given a use
			$files = [];
			foreach ($form['documents'] as $index => $role) {
				$name = GenieMap::downloadPdf($documents[$index], $media . 'pdf/', $role);
				if ($name === '') {
					$errors[] = 'The ' . strtolower($document_roles[$role]) . ' could not be copied from GenieMap (it has to be a PDF of up to 40 MB). Nothing was imported; set it to "Do not import" and try again.';
					break;
				}
				$files[$role] = $name;
				$stored[] = $media . 'pdf/' . $name;
			}
		}
	}

	if (!$errors) {
		$own_name = GenieMap::cleanName($detail['name'] ?? '');
		$district = trim((string) ($detail['district']['name'] ?? ''));
		$emirate = trim((string) ($detail['district']['emirate']['name'] ?? ''));
		$layouts = array_values(array_filter(array_map(function ($layout) { return (string) ($layout['name'] ?? ''); }, $detail['unitLayouts'] ?? [])));
		$types = array_values(array_filter(array_map(function ($type) { return (string) ($type['name'] ?? ''); }, $detail['unitTypes'] ?? [])));
		list($bedrooms, $bedrooms_on_card) = gm_bedrooms($layouts);
		$price = (float) ($detail['unitPriceRange']['from'] ?? 0);
		$area_from = (float) ($detail['unitAreaRange']['from'] ?? 0);
		$area_to = (float) ($detail['unitAreaRange']['to'] ?? 0);
		$size = $area_from > 0 ? number_format($area_from) . ($area_to > $area_from ? ' - ' . number_format($area_to) : '') . ' Sq Ft' : '';
		$description = trim((string) ($detail['description'] ?? ''));
		$plans = array_values(array_filter($detail['paymentPlans'] ?? [], 'is_array'));
		$lat = trim((string) ($detail['point']['lat'] ?? ''));
		$lon = trim((string) ($detail['point']['lon'] ?? ''));
		$has_point = is_numeric($lat) && is_numeric($lon);

		// Our unit types and amenities that match GenieMap's
		$inventory_ids = [];
		foreach (set_query($conn, 'SELECT * FROM tbl_inventry_type') ?: [] as $row) {
			$row = array_values($row);
			foreach ($types as $type) {
				// "Apartments" on GenieMap is our "Apartment".
				if (rtrim(gm_key($type), 's') === rtrim(gm_key($row[1]), 's')) {
					$inventory_ids[] = (int) $row[0];
				}
			}
		}
		$our_amenities = [];
		foreach (set_query($conn, "SELECT featfac_id, featfac_name FROM tbl_proj_featfac WHERE is_active = '1'") ?: [] as $row) {
			$our_amenities[(int) $row['featfac_id']] = (string) $row['featfac_name'];
		}
		$amenity_names = array_values(array_filter(array_map(function ($amenity) { return trim((string) ($amenity['name'] ?? '')); }, $detail['amenities'] ?? [])));
		$amenity_html = $amenity_names ? '<ul><li>' . implode('</li><li>', array_map(function ($name) { return htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); }, $amenity_names)) . '</li></ul>' : '';

		// A page address nobody else uses
		$slug = gm_slug($form['proj_name']);
		$slug = $slug !== '' ? mb_substr($slug, 0, 190) : 'project-' . $view_id;
		for ($n = 2, $base = $slug; set_query($conn, 'SELECT prj_id FROM tbl_proj WHERE proj_slug = ? LIMIT 1', [$slug]); $n++) {
			$slug = $base . '-' . $n;
		}

		// Extra specifications & details from GenieMap
		$ownership = trim((string) ($detail['ownershipType'] ?? ''));
		$finishes = array_values(array_filter(array_map(function ($f) {
			return is_array($f) ? trim((string) ($f['name'] ?? '')) : (is_string($f) ? trim($f) : '');
		}, $detail['finishes'] ?? [])));
		$finishes_text = implode(', ', $finishes);
		$service_charge = !empty($detail['serviceCharge']) ? (is_numeric($detail['serviceCharge']) ? number_format((float) $detail['serviceCharge']) : (string) $detail['serviceCharge']) : '';
		$noc = !empty($detail['noc']) ? trim((string) $detail['noc']) : '';

		// Map project status
		$genie_status = strtolower(trim((string) ($detail['status'] ?? '')));
		$status_map = [
			'available' => 'Available',
			'launch' => 'New Launch',
			'sold_out' => 'Sold Out',
		];
		$proj_status_val = $status_map[$genie_status] ?? (ucfirst($genie_status) ?: 'Available');

		// Developer logo from tbl_developer
		$dev_img = '';
		if ($form['prj_dev_id'] > 0) {
			$dev_row = set_query($conn, 'SELECT dev_img FROM tbl_developer WHERE dev_id = ? LIMIT 1', [$form['prj_dev_id']]);
			if ($dev_row && !empty($dev_row[0]['dev_img'])) {
				$dev_img = (string) $dev_row[0]['dev_img'];
			}
		}

		// Structured specifications block appended to overview
		$spec_items = [];
		if ($ownership !== '') {
			$spec_items[] = '<li><strong>Ownership:</strong> ' . htmlspecialchars(ucfirst($ownership), ENT_QUOTES, 'UTF-8') . '</li>';
		}
		if ($finishes_text !== '') {
			$spec_items[] = '<li><strong>Furnishing / Finishes:</strong> ' . htmlspecialchars($finishes_text, ENT_QUOTES, 'UTF-8') . '</li>';
		}
		if ($service_charge !== '') {
			$spec_items[] = '<li><strong>Service Charge:</strong> AED ' . htmlspecialchars($service_charge, ENT_QUOTES, 'UTF-8') . ' / sq ft / year</li>';
		}
		if ($noc !== '') {
			$spec_items[] = '<li><strong>NOC:</strong> ' . htmlspecialchars($noc, ENT_QUOTES, 'UTF-8') . '</li>';
		}
		$overview_content = gm_paragraphs($description);
		if ($spec_items) {
			$overview_content .= "\n" . '<div class="project-specifications"><h4><i class="fa fa-info-circle"></i> Key Project Specifications</h4><ul class="dre-spec-list">' . implode('', $spec_items) . '</ul></div>';
		}

		// Quick highlight cards (up to 5)
		$features = [];
		if ($ownership !== '') {
			$features[] = ['icon' => 'fa-award', 'text' => ucfirst($ownership) . ' Ownership'];
		}
		if ($finishes_text !== '') {
			$features[] = ['icon' => 'fa-couch', 'text' => $finishes_text];
		}
		if ($service_charge !== '') {
			$features[] = ['icon' => 'fa-coins', 'text' => 'Service Charge: AED ' . $service_charge . '/sqft'];
		}
		$handover_text = gm_handover($detail['handover'] ?? '');
		if ($handover_text !== '') {
			$features[] = ['icon' => 'fa-calendar-alt', 'text' => $handover_text];
		}
		if ($proj_status_val !== '') {
			$features[] = ['icon' => 'fa-check-circle', 'text' => 'Status: ' . $proj_status_val];
		}

		// OpenStreetMap embed for location map (Google output=embed is deprecated / returns 404)
		$map_embed = '';
		if ($has_point) {
			$lat_f = (float) $lat;
			$lon_f = (float) $lon;
			$bbox = ($lon_f - 0.008) . '%2C' . ($lat_f - 0.005) . '%2C' . ($lon_f + 0.008) . '%2C' . ($lat_f + 0.005);
			$map_embed = '<iframe width="100%" height="450" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.openstreetmap.org/export/embed.html?bbox=' . $bbox . '&amp;layer=mapnik&amp;marker=' . $lat_f . '%2C' . $lon_f . '" style="border: 0;"></iframe>';
		}

		$data = [
			'prj_type_id' => $form['prj_type_id'],
			'prj_dev_id' => $form['prj_dev_id'],
			'prj_comm_id' => $form['prj_comm_id'] ?: null,
			'proj_name' => $form['proj_name'],
			'proj_name_on_card' => mb_substr($own_name, 0, 255),
			'proj_sub_heading' => mb_substr($own_name, 0, 255),
			'prj_thumb_img' => $thumb,
			'proj_price' => $price > 0 ? (string) (int) round($price) : '0',
			'proj_price_type' => '',
			'proj_pplan_cont' => mb_substr(gm_plan_title($plans[0] ?? []), 0, 255),
			'proj_handover' => gm_handover($detail['handover'] ?? ''),
			'proj_days' => '',
			'proj_location' => mb_substr($district, 0, 255),
			'proj_loc_img' => '',
			'proj_shower' => '',
			'proj_area' => '',
			'proj_bedroom' => implode(',', $bedrooms),
			'proj_bedroom_on_card' => $bedrooms_on_card,
			'proj_email' => '',
			'proj_whatsaapno' => '',
			'proj_mobileno' => '',
			'proj_inner_img' => $inner,
			'proj_dev_img' => $dev_img,
			'proj_dev_img_alt' => $dev_img !== '' ? mb_substr($our_developers[$form['prj_dev_id']] ?? '', 0, 255) : '',
			'proj_size' => $size,
			'proj_inventory_type' => mb_substr(gm_type_text($types), 0, 255),
			'proj_inventory_type_filter' => implode(',', array_unique($inventory_ids)),
			'proj_start_price' => $price > 0 ? (string) (int) round($price) : '',
			'proj_overview' => $overview_content,
			'proj_brochure' => $files['proj_brochure'] ?? '',
			'proj_payplan' => $files['proj_payplan'] ?? '',
			'proj_floorplan' => $files['proj_floorplan'] ?? '',
			'proj_fea_fac_cont' => $amenity_html,
			'proj_fea_fac_opt' => implode(',', gm_amenity_ids($detail['amenities'] ?? [], $our_amenities)),
			'proj_loc_cont' => '',
			'proj_loc_img_alt_tag' => '',
			'proj_video' => '',
			'proj_masterplan' => '',
			'proj_mastrpln_img' => '',
			'proj_mastrpln_img_alt_tag' => '',
			'proj_slug' => $slug,
			'proj_map' => $map_embed,
			'meta_title' => mb_substr($form['proj_name'], 0, 255),
			'meta_keyword' => '',
			'meta_description' => $description !== '' ? rtrim(mb_substr(preg_replace('/\s+/', ' ', $description), 0, 157)) . (mb_strlen($description) > 157 ? '...' : '') : '',
			'meta_tags' => '',
			'proj_comm_feat' => '',
			'proj_showonhomepage' => '2',
			'proj_markfeat' => '2',
			'proj_posted_on' => date('Y-m-d'),
			// Always saved hidden: a person reads it through and publishes it.
			'is_active' => '2',
			'latitude' => $has_point ? $lat : null,
			'longitude' => $has_point ? $lon : null,
			'emirate' => $emirate !== '' ? mb_substr($emirate, 0, 100) : null,
			'property_status' => $proj_status_val,
			'proj_status' => $proj_status_val,
			'proj_feature_icon_1' => $features[0]['icon'] ?? '',
			'proj_feature_text_1' => mb_substr($features[0]['text'] ?? '', 0, 255),
			'proj_feature_icon_2' => $features[1]['icon'] ?? '',
			'proj_feature_text_2' => mb_substr($features[1]['text'] ?? '', 0, 255),
			'proj_feature_icon_3' => $features[2]['icon'] ?? '',
			'proj_feature_text_3' => mb_substr($features[2]['text'] ?? '', 0, 255),
			'proj_feature_icon_4' => $features[3]['icon'] ?? '',
			'proj_feature_text_4' => mb_substr($features[3]['text'] ?? '', 0, 255),
			'proj_feature_icon_5' => $features[4]['icon'] ?? '',
			'proj_feature_text_5' => mb_substr($features[4]['text'] ?? '', 0, 255),
		];

		$now = date('Y-m-d H:i:s');
		$ok = true;
		$proj_id = 0;
		mysqli_begin_transaction($conn);
		$columns = [];
		$values = [];
		foreach ($data as $column => $value) {
			if ($value !== null) {
				$columns[] = $column;
				$values[] = $value;
			}
		}
		$ok = (bool) set_query($conn, 'INSERT INTO tbl_proj (' . implode(', ', $columns) . ') VALUES (' . implode(', ', array_fill(0, count($columns), '?')) . ')', $values);
		if ($ok) {
			$proj_id = (int) mysqli_insert_id($conn);
			$alt = mb_substr(implode(', ', array_filter([$own_name, $district, $emirate])), 0, 255);
			foreach ($gallery as $name) {
				$ok = $ok && set_query($conn, "INSERT INTO tbl_proj_gallery (proj_id, gallery_img, gallery_img_alt, is_active, tstp) VALUES (?, ?, ?, '1', ?)", [$proj_id, $name, $alt, $now]);
			}
			foreach (array_slice($plans[0]['items'] ?? [], 0, 24) as $item) {
				$item_name = trim((string) ($item['name'] ?? ''));
				$item_text = trim((string) ($item['description'] ?? ''));
				// GenieMap puts the share in the name ("20%") and the moment in the description ("On booking").
				$is_share = (bool) preg_match('/^\d+(\.\d+)?\s*%$/', $item_name);
				$heading = $is_share ? ($item_text !== '' ? $item_text : 'Payment') : $item_name;
				$share = $is_share ? $item_name : (preg_match('/\d+(\.\d+)?\s*%/', $item_text, $match) ? $match[0] : '');
				if ($heading !== '') {
					$ok = $ok && set_query($conn, "INSERT INTO tbl_proj_payplan (proj_id, payplan_heading, payplan_percentage, payplan_status, is_active, tstp) VALUES (?, ?, ?, '', '1', ?)", [$proj_id, mb_substr($heading, 0, 255), mb_substr($share, 0, 255), $now]);
				}
			}
			$raw_json = json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			$ok = $ok && set_query($conn, 'INSERT INTO tbl_geniemap_import (prj_id, genie_id, genie_name, imported_by, imported_at, raw_data) VALUES (?, ?, ?, ?, ?, ?)', [$proj_id, $view_id, mb_substr((string) ($detail['name'] ?? ''), 0, 255), (string) $_SESSION['email_id'], $now, $raw_json]);
		}
		if ($ok) {
			mysqli_commit($conn);
			$logger->log('INSERT', 'tbl_proj', $proj_id, null, $logger->getOldRecordData('tbl_proj', $proj_id), 'Project imported from GenieMap: ' . $form['proj_name']);
			header('Location: ' . $self . '?imported=' . $proj_id);
			exit;
		}
		mysqli_rollback($conn);
		$errors[] = 'The project could not be saved. Nothing was imported; please try again.';
	}
	foreach ($errors ? $stored : [] as $file) {
		if (is_file($file)) {
			@unlink($file);
		}
	}
}

// ---------------------------------------------------------------
// The list
// ---------------------------------------------------------------
$f_search = trim(is_string($_GET['q'] ?? null) ? $_GET['q'] : '');
$f_emirate = is_string($_GET['emirate'] ?? null) ? $_GET['emirate'] : '';
$f_status = is_string($_GET['status'] ?? null) ? $_GET['status'] : '';
$f_new = !empty($_GET['new']) ? '1' : '';
$f_sort = is_string($_GET['sort'] ?? null) ? $_GET['sort'] : 'name';
$sorts = ['name' => 'Name A to Z', 'newest' => 'Newest on GenieMap', 'price' => 'Price, lowest first', 'handover' => 'Handover, soonest first'];
$statuses = ['available' => 'On sale', 'launch' => 'Launching', 'sold_out' => 'Sold out'];
if (!isset($sorts[$f_sort])) {
	$f_sort = 'name';
}
$page_no = max(1, (int) ($_GET['p'] ?? 1));

$list = null;
$emirates = [];
$total_all = 0;
if ($ready && !$detail) {
	@set_time_limit(300);
	$list = $genie->projects();
	if ($list === null) {
		$errors[] = $genie->error !== '' ? $genie->error : 'The project list could not be read from GenieMap.';
		$list = [];
	}
	$total_all = count($list);
	foreach ($list as $project) {
		if ($project['emirate'] !== '') {
			$emirates[$project['emirate']] = ($emirates[$project['emirate']] ?? 0) + 1;
		}
	}
	arsort($emirates);
	$needle = gm_key($f_search);
	$list = array_values(array_filter($list, function ($project) use ($needle, $f_emirate, $f_status, $f_new, $imported) {
		if ($needle !== '' && strpos(gm_key($project['name'] . ' ' . $project['developer'] . ' ' . $project['district']), $needle) === false) {
			return false;
		}
		if ($f_emirate !== '' && $project['emirate'] !== $f_emirate) {
			return false;
		}
		if ($f_status !== '' && $project['status'] !== $f_status) {
			return false;
		}
		return !($f_new !== '' && isset($imported[$project['id']]));
	}));
	usort($list, function ($a, $b) use ($f_sort) {
		if ($f_sort === 'newest') {
			return $b['id'] <=> $a['id'];
		}
		if ($f_sort === 'price') {
			return [$a['price_from'] <= 0, $a['price_from']] <=> [$b['price_from'] <= 0, $b['price_from']];
		}
		if ($f_sort === 'handover') {
			return [$a['handover'] === '', $a['handover']] <=> [$b['handover'] === '', $b['handover']];
		}
		return strcasecmp(GenieMap::cleanName($a['name']), GenieMap::cleanName($b['name']));
	});
}
// On a project's own page the number in the header comes from the kept list, when there is one.
if ($ready && $detail && $genie->listAge() > 0) {
	$total_all = count($genie->projects() ?: []);
}
$total = $list !== null ? count($list) : 0;
$pages = max(1, (int) ceil($total / GM_PER_PAGE));
$page_no = min($page_no, $pages);
$shown = $list !== null ? array_slice($list, ($page_no - 1) * GM_PER_PAGE, GM_PER_PAGE) : [];
$filters_on = $f_search !== '' || $f_emirate !== '' || $f_status !== '' || $f_new !== '';
function gm_url(array $change = [])
{
	global $self, $f_search, $f_emirate, $f_status, $f_new, $f_sort, $page_no;
	$query = array_filter(array_merge(['q' => $f_search, 'emirate' => $f_emirate, 'status' => $f_status, 'new' => $f_new, 'sort' => $f_sort === 'name' ? '' : $f_sort, 'p' => $page_no > 1 ? $page_no : ''], $change), function ($value) { return $value !== '' && $value !== null; });
	return $self . ($query ? '?' . http_build_query($query) : '');
}

$just_imported = null;
if (isset($_GET['imported'])) {
	$rows = set_query($conn, 'SELECT prj_id, proj_name FROM tbl_proj WHERE prj_id = ?', [(int) $_GET['imported']]) ?: [];
	$just_imported = $rows ? $rows[0] : null;
}
$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : '';
$notice = ['refreshed' => 'The list was read from GenieMap again.', 'expired' => 'The page expired. Nothing was changed; please try again.'][$done] ?? '';
$list_age = $genie->listAge();

// A project of ours with the same name, so nobody imports a duplicate by accident
$twin = null;
if ($detail && !isset($imported[$view_id])) {
	$rows = set_query($conn, 'SELECT prj_id, proj_name FROM tbl_proj WHERE proj_name LIKE ? ORDER BY prj_id DESC LIMIT 1', ['%' . addcslashes(GenieMap::cleanName($detail['name'] ?? ''), '%_\\') . '%']) ?: [];
	$twin = $rows ? $rows[0] : null;
}
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
					<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
						height="24px" viewBox="0 0 24 24" version="1.1">
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
									<span
										class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
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
							<!--begin::Import from GenieMap-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-geniemap.css?v=3" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-genie">

								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1><span class="dre-genie-mark"><i class="bi bi-geo-alt-fill"></i></span> Import from GenieMap</h1>
										<p>Bring a project from GenieMap into this website with its pictures, prices, payment plan and amenities. It is saved hidden, so you can read it through before it goes live.</p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= number_format($total_all) ?></strong><span>On GenieMap</span></div>
										<div><strong><?= number_format(count($imported)) ?></strong><span>Imported</span></div>
										<div><strong><?= number_format(count(array_filter($imported, function ($row) { return $row['is_active'] !== '1'; }))) ?></strong><span>Waiting for review</span></div>
									</div>
									<div class="dre-actions">
										<?php if ($detail): ?>
											<a class="dre-primary" href="<?= set_e($self) ?>"><i class="bi bi-arrow-left"></i> Back to the GenieMap list</a>
										<?php endif; ?>
										<a href="<?= BASE_URL ?>manage-project.php?source=geniemap"><i class="bi bi-buildings"></i> Imported projects</a>
										<a href="https://geniemap.net/map" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right"></i> Open GenieMap</a>
									</div>
								</div>

								<?php if (!$genie->isConfigured()): ?>
									<div class="dre-leads-notice dre-genie-bad" role="alert"><i class="bi bi-key"></i> The GenieMap API key is not set. Add <code>geniemap_api_token</code> to the secrets file of the website.</div>
								<?php elseif (!$has_table): ?>
									<div class="dre-leads-notice dre-genie-bad" role="alert"><i class="bi bi-database-exclamation"></i> One database table is missing. Run <code>db/migrations/005_create_tbl_geniemap_import.sql</code> once, then reload this page.</div>
								<?php endif; ?>

								<?php if ($ready && GenieMap::storageDir() === ''): ?>
									<div class="dre-leads-notice dre-genie-bad" role="alert"><i class="bi bi-hourglass-split"></i> This page is slow because the project list cannot be kept on the server: every visit reads all of it from GenieMap again. Make the folder <code>ai/storage/geniemap</code> writable for the web server to fix it.</div>
								<?php endif; ?>

								<?php if ($just_imported): ?>
									<div class="dre-leads-notice dre-genie-ok" role="status">
										<i class="bi bi-check-circle"></i> <strong><?= set_e($just_imported['proj_name']) ?></strong> was imported and saved hidden.
										<a class="dre-btn dre-btn-main" href="<?= BASE_URL ?>edit-project.php?edit=<?= base64_encode((string) $just_imported['prj_id']) ?>"><i class="bi bi-pencil"></i> Review and publish</a>
									</div>
								<?php elseif ($notice !== ''): ?>
									<div class="dre-leads-notice" role="status"><i class="bi bi-info-circle"></i> <?= set_e($notice) ?></div>
								<?php endif; ?>
								<?php if ($errors): ?>
									<div class="dre-leads-notice dre-genie-bad" role="alert">
										<i class="bi bi-exclamation-triangle"></i>
										<?php foreach ($errors as $error): ?><span><?= set_e($error) ?></span> <?php endforeach; ?>
									</div>
								<?php endif; ?>

								<?php if ($detail):
									$own_name = GenieMap::cleanName($detail['name'] ?? '');
									$images = array_values(array_filter($detail['images'] ?? [], 'is_string'));
									$layouts = array_values(array_filter(array_map(function ($layout) { return (string) ($layout['name'] ?? ''); }, $detail['unitLayouts'] ?? [])));
									$types = array_values(array_filter(array_map(function ($type) { return (string) ($type['name'] ?? ''); }, $detail['unitTypes'] ?? [])));
									list(, $bedroom_text) = gm_bedrooms($layouts);
									$plans = array_values(array_filter($detail['paymentPlans'] ?? [], 'is_array'));
									$already = $imported[$view_id] ?? null;
								?>
									<!-- One project: what GenieMap has, and what will be created -->
									<div class="dre-genie-detail">
										<div class="dre-genie-main">
											<section class="dre-glass dre-genie-box">
												<div class="dre-genie-title">
													<div>
														<h2><?= set_e($own_name) ?></h2>
														<p><i class="bi bi-building"></i> <?= set_e($detail['developer']['name'] ?? '') ?> &nbsp; <i class="bi bi-geo-alt"></i> <?= set_e(implode(', ', array_filter([$detail['district']['name'] ?? '', $detail['district']['emirate']['name'] ?? '']))) ?></p>
													</div>
													<span class="dre-pill <?= ($detail['status'] ?? '') === 'sold_out' ? 'dre-pill-bad' : 'dre-pill-good' ?>"><?= set_e($statuses[$detail['status'] ?? ''] ?? ($detail['status'] ?? '')) ?></span>
												</div>
												<?php if ($images && $already): ?>
													<div class="dre-genie-photos">
														<?php foreach (array_slice($images, 0, 9) as $index => $url): ?>
															<a href="<?= set_e($url) ?>" target="_blank" rel="noopener noreferrer"<?= $index === 0 ? ' class="is-main"' : '' ?>><img src="<?= set_e($url) ?>" alt="" loading="lazy" referrerpolicy="no-referrer"></a>
														<?php endforeach; ?>
													</div>
												<?php elseif ($images): ?>
													<div class="dre-genie-pickbar">
														<strong id="geniePickCount"></strong>
														<span>Tick the pictures to import. The star marks the main picture of the project.</span>
														<button type="button" class="dre-btn" id="geniePickAll">Tick all</button>
														<button type="button" class="dre-btn" id="geniePickNone">Untick all</button>
													</div>
													<div class="dre-genie-pick" id="geniePick" data-max="<?= GM_MAX_PICTURES + 1 ?>">
														<?php foreach ($images as $index => $url): ?>
															<div class="dre-genie-pic">
																<img src="<?= set_e($url) ?>" alt="Picture <?= $index + 1 ?>" loading="lazy" decoding="async" referrerpolicy="no-referrer">
																<label class="dre-genie-tick" title="Import this picture">
																	<input type="checkbox" name="pictures[]" value="<?= $index ?>" form="genieImport" <?= in_array($index, $form['pictures'], true) ? 'checked' : '' ?> aria-label="Import picture <?= $index + 1 ?>">
																	<i class="bi bi-check-lg"></i>
																</label>
																<label class="dre-genie-star" title="Use as the main picture">
																	<input type="radio" name="main" value="<?= $index ?>" form="genieImport" <?= $form['main'] === $index ? 'checked' : '' ?> aria-label="Use picture <?= $index + 1 ?> as the main picture">
																	<i class="bi bi-star-fill"></i> <span>Main</span>
																</label>
																<a href="<?= set_e($url) ?>" target="_blank" rel="noopener noreferrer" class="dre-genie-zoom" title="Open the full picture" aria-label="Open picture <?= $index + 1 ?> in full size"><i class="bi bi-arrows-fullscreen"></i></a>
															</div>
														<?php endforeach; ?>
													</div>
													<p class="dre-genie-note">The main picture is shown on the project's card. The next ticked one becomes the banner of the project page, and the rest go to its gallery. The files are copied to this website.</p>
												<?php else: ?>
													<p class="dre-genie-note dre-genie-warn"><i class="bi bi-exclamation-triangle"></i> GenieMap has no pictures for this project.</p>
												<?php endif; ?>
												<?php
												$preview_finishes = array_values(array_filter(array_map(function ($f) {
													return is_array($f) ? trim((string) ($f['name'] ?? '')) : (is_string($f) ? trim($f) : '');
												}, $detail['finishes'] ?? [])));
												$preview_finishes_text = implode(', ', $preview_finishes);
												$preview_sc = !empty($detail['serviceCharge']) ? (is_numeric($detail['serviceCharge']) ? number_format((float) $detail['serviceCharge']) : (string) $detail['serviceCharge']) : '';
												$preview_noc = !empty($detail['noc']) ? trim((string) $detail['noc']) : '';
												?>
												<dl class="dre-genie-facts">
													<div><dt>Starting price</dt><dd><?= set_e(gm_money((float) ($detail['unitPriceRange']['from'] ?? 0))) ?></dd></div>
													<div><dt>Handover</dt><dd><?= set_e(str_replace('Handover: ', '', gm_handover($detail['handover'] ?? '')) ?: 'Not given') ?></dd></div>
													<div><dt>Bedrooms</dt><dd><?= set_e($bedroom_text !== '' ? $bedroom_text : 'Not given') ?></dd></div>
													<div><dt>Unit types</dt><dd><?= set_e(gm_type_text($types) ?: 'Not given') ?></dd></div>
													<div><dt>Size</dt><dd><?= (float) ($detail['unitAreaRange']['from'] ?? 0) > 0 ? number_format((float) $detail['unitAreaRange']['from']) . ' – ' . number_format((float) ($detail['unitAreaRange']['to'] ?? 0)) . ' sq ft' : 'Not given' ?></dd></div>
													<div><dt>Ownership</dt><dd><?= set_e(ucfirst((string) ($detail['ownershipType'] ?? '')) ?: 'Not given') ?></dd></div>
													<div><dt>Furnishing</dt><dd><?= set_e($preview_finishes_text ?: 'Not given') ?></dd></div>
													<div><dt>Service charge</dt><dd><?= set_e($preview_sc !== '' ? 'AED ' . $preview_sc . ' / sq ft / yr' : 'Not given') ?></dd></div>
													<?php if ($preview_noc !== ''): ?>
														<div><dt>NOC</dt><dd><?= set_e($preview_noc) ?></dd></div>
													<?php endif; ?>
												</dl>
											</section>

											<section class="dre-glass dre-genie-box">
												<h3><i class="bi bi-text-paragraph"></i> Description</h3>
												<?php if (trim((string) ($detail['description'] ?? '')) !== ''): ?>
													<div class="dre-genie-text"><?= gm_paragraphs($detail['description']) ?></div>
													<p class="dre-genie-note"><i class="bi bi-stars"></i> This is GenieMap's own wording, and other websites may show the same text. After importing, use "Make this text clearer" on the project form to give it your own voice.</p>
												<?php else: ?>
													<p class="dre-genie-note dre-genie-warn">GenieMap has no description for this project.</p>
												<?php endif; ?>
											</section>

											<?php $documents = array_values(array_filter($detail['documents'] ?? [], 'is_string')); ?>
											<section class="dre-glass dre-genie-box">
												<h3><i class="bi bi-file-earmark-pdf"></i> Brochure and documents</h3>
												<?php if (!$documents): ?>
													<p class="dre-genie-note">GenieMap has no brochure or other document for this project. One can be uploaded on the project form after importing.</p>
												<?php else: ?>
													<div class="dre-genie-docs">
														<?php foreach ($documents as $index => $url): ?>
															<div>
																<i class="bi bi-file-earmark-pdf"></i>
																<span><strong>Document <?= $index + 1 ?></strong><small><?= set_e(strtoupper(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION)) ?: 'File') ?> on GenieMap</small></span>
																<a class="dre-btn" href="<?= set_e($url) ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-eye"></i> Open</a>
																<?php if (!$already): ?>
																	<select name="documents[<?= $index ?>]" form="genieImport" aria-label="What document <?= $index + 1 ?> is imported as">
																		<option value="">Do not import</option>
																		<?php foreach ($document_roles as $role => $label): ?>
																			<option value="<?= $role ?>" <?= ($form['documents'][$index] ?? '') === $role ? 'selected' : '' ?>>Import as: <?= set_e($label) ?></option>
																		<?php endforeach; ?>
																	</select>
																<?php endif; ?>
															</div>
														<?php endforeach; ?>
													</div>
													<?php if (!$already): ?>
														<p class="dre-genie-note">GenieMap does not say which document is which, so open each one and choose what it is. A chosen document is copied to this website and shown on the project page as its brochure, floor plan or payment plan.</p>
													<?php endif; ?>
												<?php endif; ?>
											</section>

											<?php if ($plans): ?>
												<section class="dre-glass dre-genie-box">
													<h3><i class="bi bi-cash-coin"></i> Payment plans</h3>
													<div class="dre-genie-plans">
														<?php foreach ($plans as $index => $plan): ?>
															<div class="<?= $index === 0 ? 'is-used' : '' ?>">
																<strong><?= set_e($plan['name'] ?? 'Payment plan') ?></strong>
																<?php if ($index === 0): ?><span class="dre-pill dre-pill-good">This one is imported</span><?php endif; ?>
																<ul>
																	<?php foreach ($plan['items'] ?? [] as $item): ?>
																		<li><b><?= set_e($item['name'] ?? '') ?></b> <?= set_e($item['description'] ?? '') ?></li>
																	<?php endforeach; ?>
																</ul>
															</div>
														<?php endforeach; ?>
													</div>
												</section>
											<?php endif; ?>

											<?php if (!empty($detail['amenities'])): ?>
												<section class="dre-glass dre-genie-box">
													<h3><i class="bi bi-stars"></i> Amenities</h3>
													<div class="dre-genie-chips">
														<?php foreach ($detail['amenities'] as $amenity): ?><span><?= set_e($amenity['name'] ?? '') ?></span><?php endforeach; ?>
													</div>
												</section>
											<?php endif; ?>
										</div>

										<aside class="dre-genie-side">
											<?php if ($already): ?>
												<div class="dre-glass dre-genie-box dre-genie-done">
													<span class="dre-genie-badge"><i class="bi bi-geo-alt-fill"></i> Imported from GenieMap</span>
													<h3><?= set_e($already['proj_name']) ?></h3>
													<p>Imported on <?= date('d M Y', strtotime($already['imported_at'])) ?>. It is <?= $already['is_active'] === '1' ? 'live on the website' : 'still hidden' ?>.</p>
													<a class="dre-btn dre-btn-main" href="<?= BASE_URL ?>edit-project.php?edit=<?= base64_encode((string) $already['prj_id']) ?>"><i class="bi bi-pencil"></i> Open the project</a>
												</div>
											<?php else: ?>
												<form method="post" action="<?= set_e($self) ?>?view=<?= $view_id ?>" class="dre-glass dre-genie-box dre-genie-form" id="genieImport">
													<input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
													<input type="hidden" name="genie_id" value="<?= $view_id ?>">
													<input type="hidden" name="import_project" value="1">
													<h3><i class="bi bi-box-arrow-in-down"></i> Import this project</h3>
													<?php if ($twin): ?>
														<p class="dre-genie-note dre-genie-warn"><i class="bi bi-exclamation-triangle"></i> A project with this name is already here: <a href="<?= BASE_URL ?>edit-project.php?edit=<?= base64_encode((string) $twin['prj_id']) ?>" target="_blank" rel="noopener"><?= set_e($twin['proj_name']) ?></a>. Check it is not the same one.</p>
													<?php endif; ?>
													<label>
														<span>Project name *</span>
														<input type="text" name="proj_name" value="<?= set_e($form['proj_name']) ?>" maxlength="255" required>
													</label>
													<label>
														<span>Developer *</span>
														<select name="prj_dev_id" required>
															<option value="">Choose the developer</option>
															<?php foreach ($our_developers as $id => $name): ?>
																<option value="<?= $id ?>" <?= $form['prj_dev_id'] === $id ? 'selected' : '' ?>><?= set_e($name) ?></option>
															<?php endforeach; ?>
														</select>
														<small><?= $form['prj_dev_id'] ? 'Matched to GenieMap\'s "' . set_e($detail['developer']['name'] ?? '') . '".' : 'GenieMap calls it "' . set_e($detail['developer']['name'] ?? '') . '". No developer here has that name: choose one, or <a href="' . BASE_URL . 'add-developer.php" target="_blank" rel="noopener">add it</a> first.' ?></small>
													</label>
													<label>
														<span>Community</span>
														<select name="prj_comm_id">
															<option value="0">None</option>
															<?php foreach ($our_communities as $id => $name): ?>
																<option value="<?= $id ?>" <?= $form['prj_comm_id'] === $id ? 'selected' : '' ?>><?= set_e($name) ?></option>
															<?php endforeach; ?>
														</select>
														<small><?= $form['prj_comm_id'] ? 'Matched to GenieMap\'s "' . set_e($detail['district']['name'] ?? '') . '".' : 'GenieMap\'s district is "' . set_e($detail['district']['name'] ?? '') . '". Choose a community if one fits.' ?></small>
													</label>
													<label>
														<span>Kind of project *</span>
														<select name="prj_type_id" required>
															<?php foreach ($our_types as $id => $name): ?>
																<option value="<?= $id ?>" <?= $form['prj_type_id'] === $id ? 'selected' : '' ?>><?= set_e($name) ?></option>
															<?php endforeach; ?>
														</select>
													</label>
													<div class="dre-genie-chosen" id="genieChosen" aria-live="polite"></div>
													<ul class="dre-genie-will">
														<li><i class="bi bi-eye-slash"></i> Saved <strong>hidden</strong>; nothing appears on the website until you publish it.</li>
														<li><i class="bi bi-geo-alt-fill"></i> Marked with the badge "Imported from GenieMap".</li>
														<li><i class="bi bi-check2-circle"></i> Extra details (Furnishing, Ownership, Service Charge) imported into specs & overview.</li>
														<li><i class="bi bi-pencil"></i> Location text, master plan and FAQs are not on GenieMap: add them on the project form.</li>
													</ul>
													<button type="submit" class="dre-btn dre-btn-main" id="genieImportButton"><i class="bi bi-box-arrow-in-down"></i> Import as a hidden project</button>
												</form>
											<?php endif; ?>
										</aside>
									</div>

								<?php elseif ($ready): ?>
									<!-- Search and filters -->
									<form method="get" action="<?= set_e($self) ?>" class="dre-glass dre-leads-filter dre-proj-filter">
										<label class="dre-leads-search">
											<span>Search</span>
											<i class="bi bi-search"></i>
											<input type="search" name="q" value="<?= set_e($f_search) ?>" placeholder="Project, developer or area" autocomplete="off">
										</label>
										<label>
											<span>Emirate</span>
											<select name="emirate">
												<option value="">Any</option>
												<?php foreach ($emirates as $name => $count): ?>
													<option value="<?= set_e($name) ?>" <?= $f_emirate === $name ? 'selected' : '' ?>><?= set_e($name) ?> (<?= number_format($count) ?>)</option>
												<?php endforeach; ?>
											</select>
										</label>
										<label>
											<span>Sales status</span>
											<select name="status">
												<option value="">Any</option>
												<?php foreach ($statuses as $key => $label): ?>
													<option value="<?= $key ?>" <?= $f_status === $key ? 'selected' : '' ?>><?= set_e($label) ?></option>
												<?php endforeach; ?>
											</select>
										</label>
										<label>
											<span>Sort by</span>
											<select name="sort">
												<?php foreach ($sorts as $key => $label): ?>
													<option value="<?= $key ?>" <?= $f_sort === $key ? 'selected' : '' ?>><?= set_e($label) ?></option>
												<?php endforeach; ?>
											</select>
										</label>
										<label class="dre-proj-check"><input type="checkbox" name="new" value="1" <?= $f_new !== '' ? 'checked' : '' ?>> Not imported yet only</label>
										<div class="dre-leads-filter-buttons">
											<button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-funnel"></i> Apply</button>
											<?php if ($filters_on || $f_sort !== 'name'): ?><a class="dre-btn" href="<?= set_e($self) ?>">Clear</a><?php endif; ?>
										</div>
									</form>

									<div class="dre-glass dre-leads-list">
										<div class="dre-h">
											<h3>
												<?= number_format($total) ?> <?= $total === 1 ? 'project' : 'projects' ?>
												<small><?= $total > 0 ? 'Showing ' . number_format(($page_no - 1) * GM_PER_PAGE + 1) . '–' . number_format(($page_no - 1) * GM_PER_PAGE + count($shown)) . ($filters_on ? ' · filtered' : '') : 'Nothing matches' . ($filters_on ? ' these filters' : '') ?></small>
											</h3>
											<form method="post" action="<?= set_e($self) ?>" class="dre-genie-refresh">
												<input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
												<span><?= $list_age ? 'List read from GenieMap ' . ($list_age > time() - 90 ? 'just now' : date('d M, H:i', $list_age)) : '' ?></span>
												<button type="submit" name="refresh_list" value="1" class="dre-btn" title="Takes about ten seconds"><i class="bi bi-arrow-clockwise"></i> Read again</button>
											</form>
										</div>
										<?php if (!$shown): ?>
											<div class="dre-empty"><i class="bi bi-geo-alt" style="font-size: 2rem;"></i><br>No projects to show.<?php if ($filters_on): ?> <a href="<?= set_e($self) ?>">Show all projects</a><?php endif; ?></div>
										<?php else: ?>
											<div class="dre-genie-grid">
												<?php foreach ($shown as $project):
													$done_row = $imported[$project['id']] ?? null;
													list(, $bedroom_text) = gm_bedrooms($project['layouts']);
												?>
													<article class="dre-genie-card<?= $done_row ? ' is-imported' : '' ?>">
														<a class="dre-genie-thumb" href="<?= set_e($self) ?>?view=<?= (int) $project['id'] ?>">
															<?php if ($project['image'] !== ''): ?><img src="<?= set_e($small_pictures ? BASE_URL . 'ajax/geniemap-thumb.php?id=' . (int) $project['id'] : $project['image']) ?>" alt="" loading="lazy" decoding="async" referrerpolicy="no-referrer" onerror="this.remove()"><?php endif; ?>
															<i class="bi bi-building"></i>
															<span class="dre-pill <?= $project['status'] === 'sold_out' ? 'dre-pill-bad' : ($project['status'] === 'launch' ? 'dre-pill-ai' : 'dre-pill-good') ?>"><?= set_e($statuses[$project['status']] ?? $project['status']) ?></span>
															<?php if ($done_row): ?><span class="dre-genie-badge"><i class="bi bi-check2-circle"></i> Imported</span><?php endif; ?>
														</a>
														<div class="dre-genie-body">
															<a class="dre-genie-name" href="<?= set_e($self) ?>?view=<?= (int) $project['id'] ?>"><?= set_e(GenieMap::cleanName($project['name'])) ?></a>
															<span><i class="bi bi-building"></i> <?= set_e($project['developer']) ?></span>
															<span><i class="bi bi-geo-alt"></i> <?= set_e(implode(', ', array_filter([$project['district'], $project['emirate']]))) ?></span>
															<div class="dre-genie-meta">
																<strong><?= set_e(gm_money($project['price_from'])) ?></strong>
																<em><?= set_e(implode(' · ', array_filter([$bedroom_text !== '' ? $bedroom_text . ' BR' : '', str_replace('Handover: ', '', gm_handover($project['handover']))]))) ?></em>
															</div>
														</div>
														<?php if ($done_row): ?>
															<a class="dre-btn" href="<?= BASE_URL ?>edit-project.php?edit=<?= base64_encode((string) $done_row['prj_id']) ?>"><i class="bi bi-pencil"></i> Open our project<?= $done_row['is_active'] === '1' ? '' : ' (hidden)' ?></a>
														<?php else: ?>
															<a class="dre-btn dre-btn-main" href="<?= set_e($self) ?>?view=<?= (int) $project['id'] ?>"><i class="bi bi-box-arrow-in-down"></i> Preview and import</a>
														<?php endif; ?>
													</article>
												<?php endforeach; ?>
											</div>
											<?php if ($pages > 1): ?>
												<nav class="dre-genie-pages" aria-label="Pages">
													<?php if ($page_no > 1): ?><a class="dre-btn" href="<?= set_e(gm_url(['p' => $page_no - 1 > 1 ? $page_no - 1 : ''])) ?>"><i class="bi bi-chevron-left"></i> Previous</a><?php endif; ?>
													<span>Page <?= $page_no ?> of <?= number_format($pages) ?></span>
													<?php if ($page_no < $pages): ?><a class="dre-btn" href="<?= set_e(gm_url(['p' => $page_no + 1])) ?>">Next <i class="bi bi-chevron-right"></i></a><?php endif; ?>
												</nav>
											<?php endif; ?>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>

							<script>
								// Importing copies pictures and can take a little while: say so, and stop a second click.
								(function () {
									var form = document.getElementById('genieImport');
									var pick = document.getElementById('geniePick');
									if (form && pick) {
										// Choosing the pictures: ticks, the main one, and a running count.
										var boxes = Array.prototype.slice.call(pick.querySelectorAll('input[type="checkbox"]'));
										var stars = Array.prototype.slice.call(pick.querySelectorAll('input[type="radio"]'));
										var max = Number(pick.getAttribute('data-max'));
										var documents = Array.prototype.slice.call(document.querySelectorAll('select[name^="documents["]'));
										var update = function () {
											var ticked = boxes.filter(function (box) { return box.checked; });
											// The main picture is always one of the ticked ones.
											var main = stars.filter(function (star) { return star.checked; })[0];
											if (ticked.length && (!main || !boxes[stars.indexOf(main)].checked)) {
												stars[boxes.indexOf(ticked[0])].checked = true;
											}
											boxes.forEach(function (box, index) {
												var card = box.closest('.dre-genie-pic');
												card.classList.toggle('is-on', box.checked);
												card.classList.toggle('is-main', box.checked && stars[index].checked);
											});
											var files = documents.filter(function (select) { return select.value !== ''; }).length;
											var over = ticked.length > max;
											document.getElementById('geniePickCount').textContent = ticked.length + ' of ' + boxes.length + ' ticked';
											var chosen = document.getElementById('genieChosen');
											chosen.className = 'dre-genie-chosen' + (ticked.length === 0 || over ? ' is-bad' : '');
											chosen.innerHTML = '';
											var line = document.createElement('span');
											line.textContent = ticked.length === 0 ? 'No picture is ticked. Tick at least one.' : (over ? 'Too many pictures: ' + max + ' at most.' : ticked.length + (ticked.length === 1 ? ' picture' : ' pictures') + (files ? ' and ' + files + (files === 1 ? ' document' : ' documents') : '') + ' will be copied.');
											chosen.appendChild(line);
											document.getElementById('genieImportButton').disabled = ticked.length === 0 || over;
										};
										pick.addEventListener('change', function (event) {
											// Choosing a picture as the main one ticks it too.
											if (event.target.type === 'radio') {
												boxes[stars.indexOf(event.target)].checked = true;
											}
											update();
										});
										documents.forEach(function (select) {
											select.addEventListener('change', function () {
												// One brochure, one floor plan, one payment plan: a use moves to the document chosen last.
												documents.forEach(function (other) {
													if (other !== select && select.value !== '' && other.value === select.value) { other.value = ''; }
												});
												update();
											});
										});
										document.getElementById('geniePickAll').addEventListener('click', function () {
											boxes.forEach(function (box, index) { box.checked = index < max; });
											update();
										});
										document.getElementById('geniePickNone').addEventListener('click', function () {
											boxes.forEach(function (box) { box.checked = false; });
											update();
										});
										update();
									}
									if (form) {
										form.addEventListener('submit', function () {
											var button = document.getElementById('genieImportButton');
											window.setTimeout(function () { button.disabled = true; }, 0);
											button.innerHTML = '<i class="bi bi-hourglass-split"></i> Importing: copying the files…';
										});
									}
									if (window.history.replaceState && /[?&](done|imported)=/.test(window.location.search)) {
										window.history.replaceState(null, '', window.location.pathname);
									}
								})();
							</script>
							<!--end::Import from GenieMap-->


						</div>
						<!--end::Container-->
					</div>
					<!--end::Entry-->
				</div>
				<!--end::Content-->

				<?php
				include('includes/footer-inner.php');
				?>


<?php
// Don't add extra closing tags
?>