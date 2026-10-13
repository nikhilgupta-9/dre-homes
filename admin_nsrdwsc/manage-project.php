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
$date = date('Y-m-d H:i:s');

if (empty($_SESSION['admin_csrf'])) {
	$_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
}

// This page lists these project types (buy and rent listings have their own pages).
$proj_types = [1 => 'Off Plan', 2 => 'Ready to Move In', 3 => 'International', 4 => 'Commercial'];
$proj_url_prefix = [1 => 'property/', 2 => 'ready-to-move-in/', 3 => 'international-properties/', 4 => 'property/'];
$proj_missing = ['price' => 'Price', 'bedrooms' => 'Bedrooms', 'image' => 'Thumbnail image', 'meta' => 'Meta description'];
$proj_sorts = ['newest' => 'Latest first', 'added' => 'Newest ID first', 'name' => 'Name A-Z', 'price_low' => 'Price: low to high', 'price_high' => 'Price: high to low'];

// ---------------------------------------------------------------
// Filters (all optional, all from the address bar)
// ---------------------------------------------------------------
$f_type = isset($_GET['type']) && isset($proj_types[(int) $_GET['type']]) ? (int) $_GET['type'] : 0;
$f_status = in_array($_GET['status'] ?? '', ['active', 'inactive'], true) ? $_GET['status'] : '';
$f_home = (($_GET['home'] ?? '') === '1' || ($_GET['homepage_filter'] ?? '') === '1') ? '1' : '';
$f_sync = in_array($_GET['sync'] ?? '', ['pushed', 'pending'], true) ? $_GET['sync'] : '';
$f_source = ($_GET['source'] ?? '') === 'geniemap' ? 'geniemap' : '';
$f_seo = in_array($_GET['seo'] ?? '', ['good', 'ok', 'bad', 'none'], true) ? $_GET['seo'] : '';
$f_dev = max(0, (int) ($_GET['dev'] ?? 0));
$f_comm = max(0, (int) ($_GET['comm'] ?? 0));
$f_missing = isset($_GET['missing']) && is_string($_GET['missing']) && isset($proj_missing[$_GET['missing']]) ? $_GET['missing'] : '';
$f_sort = isset($_GET['sort']) && is_string($_GET['sort']) && isset($proj_sorts[$_GET['sort']]) ? $_GET['sort'] : 'newest';
$f_search = isset($_GET['q']) && is_string($_GET['q']) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$f_per_page = (int) ($_GET['per'] ?? 25);
if (!in_array($f_per_page, [25, 50, 100], true)) {
	$f_per_page = 25;
}
$f_page = max(1, (int) ($_GET['page'] ?? 1));

// Address of this page with the current filters, with some of them changed.
function proj_url(array $change = [])
{
	global $f_seo, $f_source, $f_type, $f_status, $f_home, $f_sync, $f_dev, $f_comm, $f_missing, $f_sort, $f_search, $f_per_page, $f_page;
	$params = array_merge([
		'type' => $f_type ?: '', 'status' => $f_status, 'home' => $f_home, 'sync' => $f_sync, 'source' => $f_source, 'seo' => $f_seo, 'dev' => $f_dev ?: '', 'comm' => $f_comm ?: '',
		'missing' => $f_missing, 'sort' => $f_sort === 'newest' ? '' : $f_sort, 'q' => $f_search,
		'per' => $f_per_page === 25 ? '' : $f_per_page, 'page' => $f_page === 1 ? '' : $f_page,
	], $change);
	$params = array_filter($params, function ($value) {
		return $value !== '' && $value !== null;
	});
	return BASE_URL . 'manage-project.php' . ($params ? '?' . http_build_query($params) : '');
}

function proj_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function proj_query($conn, $sql, array $params = [])
{
	$stmt = mysqli_prepare($conn, $sql);
	if (!$stmt) {
		error_log('Project query failed: ' . mysqli_error($conn));
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
		error_log('Project query failed: ' . mysqli_stmt_error($stmt));
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

// Switches one project on or off the website and records the change.
function proj_set_active($conn, $logger, $id, $active)
{
	$old = proj_query($conn, "SELECT prj_id, proj_name, is_active FROM tbl_proj WHERE prj_id = ? AND prj_type_id IN (1, 2, 3, 4)", [$id]);
	if (!$old) {
		return false;
	}
	$value = $active ? '1' : '2';
	if ($old[0]['is_active'] === $value) {
		return true;
	}
	if (!proj_query($conn, "UPDATE tbl_proj SET is_active = ? WHERE prj_id = ?", [$value, $id])) {
		return false;
	}
	$logger->log('UPDATE', 'tbl_proj', $id, ['is_active' => $old[0]['is_active']], ['is_active' => $value], ($active ? 'Project shown on website: ' : 'Project hidden from website: ') . $old[0]['proj_name']);
	return true;
}

// ---------------------------------------------------------------
// Changes (POST only, with a token)
// ---------------------------------------------------------------
$notice = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['proj_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$action = is_string($_POST['proj_action']) ? $_POST['proj_action'] : '';
	$done = '';

	if ($token_ok && ($action === 'show' || $action === 'hide')) {
		// One project, or every ticked project.
		$ids = isset($_POST['proj_id']) ? [(int) $_POST['proj_id']] : array_map('intval', (array) ($_POST['project_ids'] ?? []));
		$ids = array_slice(array_unique(array_filter($ids)), 0, 200);
		$changed = 0;
		foreach ($ids as $id) {
			$changed += proj_set_active($conn, $logger, $id, $action === 'show') ? 1 : 0;
		}
		$done = $changed > 0 ? ($action === 'show' ? 'shown' : 'hidden') . '-' . $changed : 'none';
	} elseif ($token_ok && $action === 'delete') {
		$id = (int) ($_POST['proj_id'] ?? 0);
		$project = $id > 0 ? proj_query($conn, "SELECT * FROM tbl_proj WHERE prj_id = ? AND prj_type_id IN (1, 2, 3, 4)", [$id]) : [];
		if ($project) {
			$project = $project[0];
			// Files are removed only when a name is stored and the file is really there.
			$files = [];
			foreach (proj_query($conn, "SELECT gallery_img FROM tbl_proj_gallery WHERE proj_id = ?", [$id]) ?: [] as $gallery) {
				$files[] = ['assets/media/project/gallery/', $gallery['gallery_img']];
			}
			$files[] = ['assets/media/project/inner-image/', $project['proj_inner_img']];
			foreach (['proj_brochure', 'proj_payplan', 'proj_floorplan'] as $column) {
				$files[] = ['assets/media/project/pdf/', $project[$column]];
			}
			foreach ($files as $file) {
				$name = basename(trim((string) $file[1]));
				if ($name !== '' && is_file($file[0] . $name)) {
					@unlink($file[0] . $name);
				}
			}

			$old_data = $logger->getOldRecordData('tbl_proj', $id);
			$logger->log('DELETE', 'tbl_proj', $id, $old_data, null, 'Delete Properties: ' . $id);
			$logger->archiveDeletedRecord('tbl_proj', $id, $old_data);
			proj_query($conn, "DELETE FROM tbl_proj WHERE prj_id = ?", [$id]);
			$done = 'deleted';
		} else {
			$done = 'none';
		}
	}

	if ($done !== '') {
		header('Location: ' . proj_url(['done' => $done]));
		exit;
	}
	$notice = 'Nothing was changed. Please reload the page and try again.';
}

// ---------------------------------------------------------------
// The list
// ---------------------------------------------------------------
$has_facts = (bool) proj_query($conn, "SHOW TABLES LIKE 'tbl_ai_proj_facts'");
$legacy_price_sql = "IF(tp.proj_price REGEXP '^ *[0-9]+ *$' AND tp.proj_price + 0 > 0, tp.proj_price + 0, NULL)";
$price_sql = $has_facts ? "COALESCE(f.price_aed, $legacy_price_sql)" : $legacy_price_sql;
$from_sql = "FROM tbl_proj AS tp
	LEFT JOIN tbl_developer AS td ON tp.prj_dev_id = td.dev_id
	LEFT JOIN tbl_community AS tc ON tp.prj_comm_id = tc.comm_id"
	. ($has_facts ? " LEFT JOIN tbl_ai_proj_facts AS f ON f.prj_id = tp.prj_id" : '');
// Projects imported from GenieMap carry a badge (the table exists once its migration has been run).
$has_genie = (bool) proj_query($conn, "SHOW TABLES LIKE 'tbl_geniemap_import'");
$from_sql .= $has_genie ? ' LEFT JOIN tbl_geniemap_import AS gm ON gm.prj_id = tp.prj_id' : '';
$genie_sql = $has_genie ? 'gm.genie_id' : 'NULL';
// The SEO check of the project form keeps a score per project (once its migration has been run).
$has_seo = (bool) proj_query($conn, "SHOW TABLES LIKE 'tbl_proj_seo'");
$from_sql .= $has_seo ? ' LEFT JOIN tbl_proj_seo AS seo ON seo.prj_id = tp.prj_id' : '';
$seo_sql = $has_seo ? 'seo.seo_score' : 'NULL';
$seo_levels = ['good' => ['Good', "$seo_sql > 70"], 'ok' => ['OK', "$seo_sql BETWEEN 41 AND 70"], 'bad' => ['Needs improvement', "$seo_sql <= 40"], 'none' => ['Not checked yet', "$seo_sql IS NULL"]];

$missing_sql = [
	'price' => "$price_sql IS NULL",
	'bedrooms' => "(tp.proj_bedroom IS NULL OR TRIM(tp.proj_bedroom) = '')",
	'image' => "(tp.prj_thumb_img IS NULL OR TRIM(tp.prj_thumb_img) = '')",
	'meta' => "(tp.meta_description IS NULL OR TRIM(tp.meta_description) = '')",
];

// Every filter except the type, so the type tabs can show their own counts.
$where = ['tp.prj_type_id IN (1, 2, 3, 4)'];
$params = [];
if ($f_search !== '') {
	$like = '%' . addcslashes($f_search, '%_\\') . '%';
	$where[] = '(tp.proj_name LIKE ? OR tp.proj_location LIKE ? OR tp.proj_slug LIKE ? OR td.dev_heading LIKE ? OR tc.comm_heading LIKE ?)';
	array_push($params, $like, $like, $like, $like, $like);
}
if ($f_status !== '') {
	$where[] = $f_status === 'active' ? "tp.is_active = '1'" : "tp.is_active <> '1'";
}
if ($f_home !== '') {
	$where[] = "tp.proj_showonhomepage = '1'";
}
if ($f_source !== '' && $has_genie) {
	$where[] = 'gm.genie_id IS NOT NULL';
}
if ($f_seo !== '' && $has_seo) {
	$where[] = $seo_levels[$f_seo][1];
}
if ($f_sync !== '') {
	$where[] = $f_sync === 'pushed' ? 'tp.app_sync_status = 1' : '(tp.app_sync_status IS NULL OR tp.app_sync_status <> 1)';
}
if ($f_dev > 0) {
	$where[] = 'tp.prj_dev_id = ?';
	$params[] = $f_dev;
}
if ($f_comm > 0) {
	$where[] = 'tp.prj_comm_id = ?';
	$params[] = $f_comm;
}
if ($f_missing !== '') {
	$where[] = $missing_sql[$f_missing];
}
$where_sql = 'WHERE ' . implode(' AND ', $where);

$type_counts = [];
foreach (proj_query($conn, "SELECT tp.prj_type_id, COUNT(*) AS n $from_sql $where_sql GROUP BY tp.prj_type_id", $params) ?: [] as $row) {
	$type_counts[(int) $row['prj_type_id']] = (int) $row['n'];
}
$count_all = array_sum($type_counts);
// How many GenieMap projects there are in all, for the quick filter beside the tabs.
$genie_total = $has_genie ? (int) ((proj_query($conn, 'SELECT COUNT(*) AS n FROM tbl_geniemap_import gm JOIN tbl_proj tp ON tp.prj_id = gm.prj_id WHERE tp.prj_type_id IN (1, 2, 3, 4)') ?: [['n' => 0]])[0]['n']) : 0;
$total = $f_type ? ($type_counts[$f_type] ?? 0) : $count_all;

$list_where_sql = $where_sql . ($f_type ? ' AND tp.prj_type_id = ' . $f_type : '');
$order_sql = [
	'newest' => 'tp.tstp DESC, tp.prj_id DESC',
	'added' => 'tp.prj_id DESC',
	'name' => 'tp.proj_name ASC',
	'price_low' => "$price_sql IS NULL, $price_sql ASC",
	'price_high' => "$price_sql IS NULL, $price_sql DESC",
][$f_sort];

$pages = max(1, (int) ceil($total / $f_per_page));
$f_page = min($f_page, $pages);
$offset = ($f_page - 1) * $f_per_page;

$projects = proj_query(
	$conn,
	"SELECT tp.prj_id, tp.prj_type_id, tp.proj_name, tp.proj_location, tp.proj_slug, tp.proj_bedroom, tp.proj_price, tp.prj_thumb_img,
		tp.meta_description, tp.is_active, tp.proj_showonhomepage, tp.app_sync_status, tp.app_sync_date, tp.tstp,
		td.dev_heading AS proj_dev_name, tc.comm_heading AS proj_comm_name, $price_sql AS price_number, $genie_sql AS genie_id, $seo_sql AS seo_score
	$from_sql $list_where_sql ORDER BY $order_sql LIMIT ?, ?",
	array_merge($params, [$offset, $f_per_page])
) ?: [];

$stat = proj_query($conn, "SELECT COUNT(*) AS total, SUM(is_active = '1') AS active, SUM(is_active <> '1') AS hidden, SUM(proj_showonhomepage = '1') AS home, SUM(app_sync_status = 1) AS pushed FROM tbl_proj WHERE prj_type_id IN (1, 2, 3, 4)");
$stat = $stat ? $stat[0] : ['total' => 0, 'active' => 0, 'hidden' => 0, 'home' => 0, 'pushed' => 0];

$developer_options = proj_query($conn, "SELECT d.dev_id, d.dev_heading FROM tbl_developer d WHERE d.dev_id IN (SELECT prj_dev_id FROM tbl_proj WHERE prj_type_id IN (1, 2, 3, 4)) ORDER BY d.dev_heading") ?: [];
$community_options = proj_query($conn, "SELECT c.comm_id, c.comm_heading FROM tbl_community c WHERE c.comm_id IN (SELECT prj_comm_id FROM tbl_proj WHERE prj_type_id IN (1, 2, 3, 4)) ORDER BY c.comm_heading") ?: [];

$filters_on = $f_search !== '' || $f_status !== '' || $f_home !== '' || $f_sync !== '' || $f_source !== '' || $f_seo !== '' || $f_dev > 0 || $f_comm > 0 || $f_missing !== '';

// Messages after an action
$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : '';
if ($done === 'added') {
	$notice = 'The project was added.';
} elseif ($done === 'deleted') {
	$notice = 'The project was deleted. A copy is kept in the delete log.';
} elseif (preg_match('/^(shown|hidden)-(\d+)$/', $done, $match)) {
	$notice = $match[2] . ((int) $match[2] === 1 ? ' project is' : ' projects are') . ' now ' . ($match[1] === 'shown' ? 'live on the website.' : 'hidden from the website.');
} elseif ($done === 'none') {
	$notice = 'Nothing was changed.';
}
$push_message = is_string($_GET['msg'] ?? null) ? $_GET['msg'] : '';
if ($push_message === 'push_success') {
	$notice = 'The selected projects were pushed to the app.';
} elseif ($push_message === 'partial_success') {
	$notice = 'Some projects could not be pushed to the app. Failed IDs: ' . preg_replace('/[^0-9,]/', '', (string) ($_GET['failed'] ?? ''));
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

							<!--begin::Projects-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-geniemap.css?v=3" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-seo-check.css?v=2" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects">

								<!-- Title, numbers, main actions -->
								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1>Projects</h1>
										<p>Off-plan, ready to move in, international and commercial listings.</p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= number_format((int) $stat['active']) ?></strong><span>Live</span></div>
										<div><strong><?= number_format((int) $stat['hidden']) ?></strong><span>Hidden</span></div>
										<div><strong><?= number_format((int) $stat['home']) ?></strong><span>On homepage</span></div>
										<div><strong><?= number_format((int) $stat['pushed']) ?></strong><span>In the app</span></div>
									</div>
									<div class="dre-actions">
										<a class="dre-primary" href="<?= BASE_URL ?>add-project.php"><i class="bi bi-plus-lg"></i> Add project</a>
										<a href="<?= BASE_URL ?>import-geniemap.php"><i class="bi bi-geo-alt-fill"></i> Import from GenieMap</a>
										<a href="<?= BASE_URL ?>export-project.php"><i class="bi bi-download"></i> Export XLS</a>
									</div>
								</div>

								<?php if ($notice !== ''): ?>
									<div class="dre-leads-notice" role="status" id="projNotice"><i class="bi bi-info-circle"></i> <?= proj_e($notice) ?></div>
								<?php endif; ?>

								<!-- Kind of listing -->
								<nav class="dre-leads-tabs" aria-label="Kind of listing">
									<a href="<?= proj_e(proj_url(['type' => '', 'page' => ''])) ?>" class="<?= $f_type === 0 ? 'is-on' : '' ?>"><i class="bi bi-collection"></i> All <b><?= number_format($count_all) ?></b></a>
									<?php foreach ($proj_types as $type_id => $type_name): ?>
										<a href="<?= proj_e(proj_url(['type' => $type_id, 'page' => ''])) ?>" class="<?= $f_type === $type_id ? 'is-on' : '' ?>"><?= proj_e($type_name) ?> <b><?= number_format($type_counts[$type_id] ?? 0) ?></b></a>
									<?php endforeach; ?>
									<?php if ($has_genie): ?>
										<a href="<?= proj_e(proj_url(['source' => $f_source !== '' ? '' : 'geniemap', 'page' => ''])) ?>" class="dre-genie-tab<?= $f_source !== '' ? ' is-on' : '' ?>" title="<?= $f_source !== '' ? 'Show projects from every source again' : 'Show only the projects imported from GenieMap' ?>"><i class="bi bi-geo-alt-fill"></i> GenieMap <b><?= number_format($genie_total) ?></b><?php if ($f_source !== ''): ?> <i class="bi bi-x-lg"></i><?php endif; ?></a>
									<?php endif; ?>
								</nav>

								<!-- Search and filters -->
								<form method="get" action="<?= BASE_URL ?>manage-project.php" class="dre-glass dre-leads-filter dre-proj-filter">
									<?php if ($f_type): ?><input type="hidden" name="type" value="<?= $f_type ?>"><?php endif; ?>
									<label class="dre-leads-search">
										<span>Search</span>
										<i class="bi bi-search"></i>
										<input type="search" name="q" value="<?= proj_e($f_search) ?>" placeholder="Project, location, developer or community" autocomplete="off">
									</label>
									<label>
										<span>Website status</span>
										<select name="status">
											<option value="">Any</option>
											<option value="active" <?= $f_status === 'active' ? 'selected' : '' ?>>Live</option>
											<option value="inactive" <?= $f_status === 'inactive' ? 'selected' : '' ?>>Hidden</option>
										</select>
									</label>
									<label>
										<span>Developer</span>
										<select name="dev">
											<option value="">Any</option>
											<?php foreach ($developer_options as $option): ?>
												<option value="<?= (int) $option['dev_id'] ?>" <?= $f_dev === (int) $option['dev_id'] ? 'selected' : '' ?>><?= proj_e($option['dev_heading']) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span>Community</span>
										<select name="comm">
											<option value="">Any</option>
											<?php foreach ($community_options as $option): ?>
												<option value="<?= (int) $option['comm_id'] ?>" <?= $f_comm === (int) $option['comm_id'] ? 'selected' : '' ?>><?= proj_e($option['comm_heading']) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span>Missing</span>
										<select name="missing">
											<option value="">Nothing selected</option>
											<?php foreach ($proj_missing as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_missing === $key ? 'selected' : '' ?>><?= proj_e($label) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span>App</span>
										<select name="sync">
											<option value="">Any</option>
											<option value="pending" <?= $f_sync === 'pending' ? 'selected' : '' ?>>Not in the app</option>
											<option value="pushed" <?= $f_sync === 'pushed' ? 'selected' : '' ?>>In the app</option>
										</select>
									</label>
									<?php if ($has_seo): ?>
										<label>
											<span>SEO check</span>
											<select name="seo">
												<option value="">Any</option>
												<?php foreach ($seo_levels as $key => $level): ?>
													<option value="<?= $key ?>" <?= $f_seo === $key ? 'selected' : '' ?>><?= proj_e($level[0]) ?></option>
												<?php endforeach; ?>
											</select>
										</label>
									<?php endif; ?>
									<?php if ($has_genie): ?>
										<label>
											<span>Source</span>
											<select name="source">
												<option value="">Any</option>
												<option value="geniemap" <?= $f_source !== '' ? 'selected' : '' ?>>Imported from GenieMap</option>
											</select>
										</label>
									<?php endif; ?>
									<label>
										<span>Sort by</span>
										<select name="sort">
											<?php foreach ($proj_sorts as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_sort === $key ? 'selected' : '' ?>><?= proj_e($label) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span>Per page</span>
										<select name="per">
											<?php foreach ([25, 50, 100] as $option): ?>
												<option value="<?= $option ?>" <?= $f_per_page === $option ? 'selected' : '' ?>><?= $option ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label class="dre-proj-check">
										<input type="checkbox" name="home" value="1" <?= $f_home !== '' ? 'checked' : '' ?>> On the homepage only
									</label>
									<div class="dre-leads-filter-buttons">
										<button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-funnel"></i> Apply</button>
										<?php if ($filters_on || $f_sort !== 'newest'): ?>
											<a class="dre-btn" href="<?= proj_e(proj_url(['q' => '', 'status' => '', 'home' => '', 'sync' => '', 'dev' => '', 'comm' => '', 'missing' => '', 'sort' => '', 'page' => ''])) ?>">Clear</a>
										<?php endif; ?>
									</div>
								</form>

								<!-- The form the ticked projects belong to. Its buttons choose what happens. -->
								<form method="post" action="<?= proj_e(proj_url()) ?>" id="projBulkForm">
									<input type="hidden" name="csrf" value="<?= proj_e($_SESSION['admin_csrf']) ?>">
								</form>

								<!-- The list -->
								<div class="dre-glass dre-leads-list">
									<div class="dre-h">
										<h3>
											<?= number_format($total) ?> <?= $total === 1 ? 'project' : 'projects' ?><?= $f_type ? ' &middot; ' . proj_e($proj_types[$f_type]) : '' ?>
											<small>
												<?php if ($total > 0): ?>
													Showing <?= number_format($offset + 1) ?>&ndash;<?= number_format($offset + count($projects)) ?><?= $filters_on ? ' &middot; filtered' : '' ?>
												<?php else: ?>
													Nothing matches<?= $filters_on ? ' these filters' : '' ?>
												<?php endif; ?>
											</small>
										</h3>
										<?php if ($projects): ?>
											<label class="dre-proj-all"><input type="checkbox" id="projSelectAll"> Select all on this page</label>
										<?php endif; ?>
									</div>

									<?php if (!$projects): ?>
										<div class="dre-empty">
											<i class="bi bi-buildings" style="font-size: 2rem;"></i><br>
											No projects to show.
											<?php if ($filters_on || $f_type): ?>
												<a href="<?= BASE_URL ?>manage-project.php">Show all projects</a>
											<?php endif; ?>
										</div>
									<?php else: ?>
										<div class="dre-proj-table" role="table" aria-label="Projects">
											<div class="dre-proj-row dre-proj-head" role="row">
												<div role="columnheader"><span class="sr-only">Select</span></div>
												<div role="columnheader">Project</div>
												<div role="columnheader">Developer and community</div>
												<div role="columnheader">Price and bedrooms</div>
												<div role="columnheader">Status</div>
												<div role="columnheader"><span class="sr-only">Actions</span></div>
											</div>
											<?php foreach ($projects as $row):
												$id = (int) $row['prj_id'];
												$type_id = (int) $row['prj_type_id'];
												$is_live = $row['is_active'] === '1';
												$thumb = trim(explode(',', (string) $row['prj_thumb_img'])[0]);
												$view_url = BASE_URL_HOME . ($proj_url_prefix[$type_id] ?? 'property/') . $row['proj_slug'];
												$missing = [];
												if ($row['price_number'] === null) {
													$missing[] = 'price';
												}
												if (trim((string) $row['proj_bedroom']) === '') {
													$missing[] = 'bedrooms';
												}
												if ($thumb === '') {
													$missing[] = 'image';
												}
												if (trim((string) $row['meta_description']) === '') {
													$missing[] = 'meta description';
												}
												$bedrooms = str_ireplace('STD', 'Studio', implode(', ', array_filter(array_map('trim', explode(',', (string) $row['proj_bedroom'])), 'strlen')));
											?>
												<div class="dre-proj-row<?= $is_live ? '' : ' is-hidden' ?>" role="row">
													<div class="dre-proj-pick" role="cell">
														<input type="checkbox" name="project_ids[]" value="<?= $id ?>" form="projBulkForm" class="dre-proj-box" aria-label="Select <?= proj_e($row['proj_name']) ?>">
													</div>
													<div class="dre-proj-main" role="cell">
														<a class="dre-proj-thumb" href="<?= BASE_URL ?>edit-project.php?edit=<?= base64_encode($id) ?>" tabindex="-1" aria-hidden="true">
															<?php if ($thumb !== ''): ?>
																<img src="<?= BASE_URL ?>assets/media/project/thumb/<?= proj_e(rawurlencode($thumb)) ?>" alt="" loading="lazy" onerror="this.remove()">
															<?php endif; ?>
															<i class="bi bi-building"></i>
														</a>
														<div>
															<a class="dre-proj-name" href="<?= BASE_URL ?>edit-project.php?edit=<?= base64_encode($id) ?>"><?= proj_e($row['proj_name']) ?></a>
															<span class="dre-proj-sub"><i class="bi bi-geo-alt"></i> <?= trim((string) $row['proj_location']) !== '' ? proj_e($row['proj_location']) : 'No location' ?></span>
															<div class="dre-proj-pills">
																<span class="dre-pill"><?= proj_e($proj_types[$type_id] ?? 'Other') ?></span>
																<?php if ($row['proj_showonhomepage'] === '1'): ?><span class="dre-pill dre-pill-ai"><i class="bi bi-house-heart"></i> Homepage</span><?php endif; ?>
																<?php if ($has_seo): $seo_level = $row['seo_score'] === null ? 'none' : ((int) $row['seo_score'] > 70 ? 'good' : ((int) $row['seo_score'] > 40 ? 'ok' : 'bad')); ?><span class="dre-seo-pill is-<?= $seo_level ?>" title="SEO check of the project form<?= $row['seo_score'] === null ? ': open the project and save it once to get a score' : ' (score ' . (int) $row['seo_score'] . ' of 100)' ?>"><i></i> SEO: <?= proj_e($seo_levels[$seo_level][0]) ?></span><?php endif; ?>
																<?php if (!empty($row['genie_id'])): ?><span class="dre-pill dre-pill-genie" title="This project was imported from GenieMap"><i class="bi bi-geo-alt-fill"></i> Imported from GenieMap</span><?php endif; ?>
															</div>
														</div>
													</div>
													<div class="dre-proj-where" role="cell">
														<span><i class="bi bi-building"></i> <?= trim((string) $row['proj_dev_name']) !== '' ? proj_e($row['proj_dev_name']) : '<em>No developer</em>' ?></span>
														<span><i class="bi bi-map"></i> <?= trim((string) $row['proj_comm_name']) !== '' ? proj_e($row['proj_comm_name']) : '<em>No community</em>' ?></span>
													</div>
													<div class="dre-proj-facts" role="cell">
														<strong><?= $row['price_number'] !== null ? 'AED ' . number_format((float) $row['price_number']) : 'Price on request' ?></strong>
														<span><?= $bedrooms !== '' ? proj_e($bedrooms) . ' bed' : 'Bedrooms not set' ?></span>
														<?php if ($missing): ?>
															<span class="dre-proj-missing" title="Details that are not filled in"><i class="bi bi-exclamation-triangle"></i> Missing: <?= proj_e(implode(', ', $missing)) ?></span>
														<?php endif; ?>
													</div>
													<div class="dre-proj-status" role="cell">
														<form method="post" action="<?= proj_e(proj_url()) ?>">
															<input type="hidden" name="csrf" value="<?= proj_e($_SESSION['admin_csrf']) ?>">
															<input type="hidden" name="proj_id" value="<?= $id ?>">
															<button type="submit" name="proj_action" value="<?= $is_live ? 'hide' : 'show' ?>" class="dre-switch<?= $is_live ? ' is-on' : '' ?>" role="switch" aria-checked="<?= $is_live ? 'true' : 'false' ?>" title="<?= $is_live ? 'Live on the website. Click to hide.' : 'Hidden from the website. Click to show.' ?>">
																<i></i><span><?= $is_live ? 'Live' : 'Hidden' ?></span>
															</button>
														</form>
														<span class="dre-proj-sync">
															<?php if ((int) $row['app_sync_status'] === 1): ?>
																<i class="bi bi-phone"></i> In the app<?= $row['app_sync_date'] ? ' &middot; ' . date('d M Y', strtotime($row['app_sync_date'])) : '' ?>
															<?php else: ?>
																<i class="bi bi-phone"></i> Not in the app
															<?php endif; ?>
														</span>
													</div>
													<div class="dre-proj-actions" role="cell">
														<a class="dre-icon-btn" href="<?= BASE_URL ?>edit-project.php?edit=<?= base64_encode($id) ?>" title="Edit" aria-label="Edit <?= proj_e($row['proj_name']) ?>"><i class="bi bi-pencil"></i></a>
														<a class="dre-icon-btn" href="<?= proj_e($view_url) ?>" target="_blank" rel="noopener" title="<?= $is_live ? 'View on the website' : 'Hidden projects do not open on the website' ?>" aria-label="View <?= proj_e($row['proj_name']) ?> on the website"><i class="bi bi-box-arrow-up-right"></i></a>
														<div class="dropdown">
															<button type="button" class="dre-icon-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More" aria-label="More actions for <?= proj_e($row['proj_name']) ?>"><i class="bi bi-three-dots"></i></button>
															<div class="dropdown-menu dropdown-menu-right">
																<a class="dropdown-item" href="<?= BASE_URL ?>manage-gallery.php?proj_id=<?= $id ?>"><i class="bi bi-images mr-2"></i>Gallery</a>
																<a class="dropdown-item" href="<?= BASE_URL ?>manage-faq.php?proj_id=<?= $id ?>"><i class="bi bi-question-circle mr-2"></i>Questions and answers</a>
																<a class="dropdown-item" href="<?= BASE_URL ?>manage-payplan.php?proj_id=<?= $id ?>"><i class="bi bi-calendar-check mr-2"></i>Payment plan</a>
																<div class="dropdown-divider"></div>
																<form method="post" action="<?= proj_e(proj_url()) ?>" class="dre-proj-delete" data-name="<?= proj_e($row['proj_name']) ?>">
																	<input type="hidden" name="csrf" value="<?= proj_e($_SESSION['admin_csrf']) ?>">
																	<input type="hidden" name="proj_id" value="<?= $id ?>">
																	<button type="submit" name="proj_action" value="delete" class="dropdown-item text-danger"><i class="bi bi-trash3 mr-2"></i>Delete</button>
																</form>
															</div>
														</div>
													</div>
												</div>
											<?php endforeach; ?>
										</div>

										<?php if ($pages > 1): ?>
											<nav class="dre-leads-pages" aria-label="Pages">
												<?php if ($f_page > 1): ?>
													<a href="<?= proj_e(proj_url(['page' => $f_page - 1])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
												<?php endif; ?>
												<?php
												$shown = [];
												foreach ([1, 2, $f_page - 1, $f_page, $f_page + 1, $pages - 1, $pages] as $number) {
													if ($number >= 1 && $number <= $pages) {
														$shown[$number] = true;
													}
												}
												ksort($shown);
												$previous = 0;
												foreach (array_keys($shown) as $number):
													if ($number > $previous + 1): ?><span>&hellip;</span><?php endif; ?>
													<a href="<?= proj_e(proj_url(['page' => $number])) ?>" class="<?= $number === $f_page ? 'is-on' : '' ?>" <?= $number === $f_page ? 'aria-current="page"' : '' ?>><?= $number ?></a>
												<?php $previous = $number;
												endforeach; ?>
												<?php if ($f_page < $pages): ?>
													<a href="<?= proj_e(proj_url(['page' => $f_page + 1])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
												<?php endif; ?>
											</nav>
										<?php endif; ?>
									<?php endif; ?>
								</div>

								<!-- Appears when at least one project is ticked -->
								<div class="dre-proj-bulk" id="projBulkBar" hidden>
									<strong><span id="projBulkCount">0</span> selected</strong>
									<div>
										<button type="submit" form="projBulkForm" name="push_to_app" value="1" formaction="<?= BASE_URL ?>push-app/push-project-app.php" class="dre-btn dre-btn-main"><i class="bi bi-phone"></i> Push to app</button>
										<button type="submit" form="projBulkForm" name="proj_action" value="show" class="dre-btn" data-confirm="Show the selected projects on the website?"><i class="bi bi-eye"></i> Show on website</button>
										<button type="submit" form="projBulkForm" name="proj_action" value="hide" class="dre-btn" data-confirm="Hide the selected projects from the website?"><i class="bi bi-eye-slash"></i> Hide</button>
										<button type="button" class="dre-btn" id="projBulkClear">Clear</button>
									</div>
								</div>
							</div>

							<script>
								// Selection bar, confirmations, and the message that fades out.
								(function () {
									var boxes = Array.prototype.slice.call(document.querySelectorAll('.dre-proj-box'));
									var all = document.getElementById('projSelectAll');
									var bar = document.getElementById('projBulkBar');
									var count = document.getElementById('projBulkCount');

									function refresh() {
										var picked = boxes.filter(function (box) { return box.checked; }).length;
										count.textContent = picked;
										bar.hidden = picked === 0;
										if (all) {
											all.checked = picked > 0 && picked === boxes.length;
											all.indeterminate = picked > 0 && picked < boxes.length;
										}
									}
									boxes.forEach(function (box) { box.addEventListener('change', refresh); });
									if (all) {
										all.addEventListener('change', function () {
											boxes.forEach(function (box) { box.checked = all.checked; });
											refresh();
										});
									}
									document.getElementById('projBulkClear').addEventListener('click', function () {
										boxes.forEach(function (box) { box.checked = false; });
										refresh();
									});
									bar.querySelectorAll('[data-confirm]').forEach(function (button) {
										button.addEventListener('click', function (event) {
											if (!window.confirm(button.getAttribute('data-confirm'))) {
												event.preventDefault();
											}
										});
									});
									document.querySelectorAll('.dre-proj-delete').forEach(function (form) {
										form.addEventListener('submit', function (event) {
											if (!window.confirm('Delete "' + form.getAttribute('data-name') + '"? Its gallery files, brochure and plans are removed too. A copy of the record is kept in the delete log.')) {
												event.preventDefault();
											}
										});
									});
									refresh();

									var notice = document.getElementById('projNotice');
									if (notice) {
										setTimeout(function () { notice.style.display = 'none'; }, 6000);
									}
								})();
							</script>
							<!--end::Projects-->

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