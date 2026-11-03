<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');

// Nothing below runs unless someone is signed in.
if (empty($_SESSION['email_id'])) {
	exit;
}

// This page shows, for each project, which details are still empty.
$pend_types = [1 => 'Off Plan', 2 => 'Ready to Move In', 3 => 'International', 4 => 'Commercial'];

// The details that are checked, grouped the way the add / edit form is laid out: column => label.
$pend_sections = [
	'Basics' => ['proj_name' => 'Project name', 'proj_sub_heading' => 'Sub heading', 'proj_location' => 'Location', 'proj_slug' => 'Page address (slug)'],
	'Price and units' => [
		'proj_price' => 'Price', 'proj_price_type' => 'Price period', 'proj_start_price' => 'Start price text', 'proj_bedroom' => 'Bedrooms',
		'proj_shower' => 'Bathrooms', 'proj_size' => 'Size', 'proj_area' => 'Area', 'proj_handover' => 'Handover', 'proj_days' => 'Days',
		'proj_inventory_type' => 'Property type text', 'proj_pplan_cont' => 'Payment plan summary',
	],
	'Images and files' => [
		'prj_thumb_img' => 'Thumbnail image', 'proj_inner_img' => 'Inner image', 'proj_dev_img' => 'Developer image', 'proj_dev_img_alt' => 'Developer image alt text',
		'proj_brochure' => 'Brochure', 'proj_payplan' => 'Payment plan file', 'proj_floorplan' => 'Floor plan',
	],
	'Description' => ['proj_overview' => 'Overview', 'proj_fea_fac_cont' => 'Features text', 'proj_fea_fac_opt' => 'Amenities'],
	'Location' => ['proj_loc_cont' => 'Location text', 'proj_loc_img' => 'Location image', 'proj_loc_img_alt_tag' => 'Location image alt text', 'proj_map' => 'Map'],
	'Master plan, video and QR' => [
		'proj_masterplan' => 'Master plan text', 'proj_mastrpln_img' => 'Master plan image', 'proj_mastrpln_img_alt_tag' => 'Master plan image alt text',
		'proj_video' => 'Video', 'proj_qr' => 'QR text', 'proj_qr_link' => 'QR link', 'proj_qr_img' => 'QR image',
	],
	'Contact' => ['proj_email' => 'Email', 'proj_whatsaapno' => 'WhatsApp number', 'proj_mobileno' => 'Mobile number'],
	'SEO' => ['meta_title' => 'Meta title', 'meta_keyword' => 'Meta keywords', 'meta_description' => 'Meta description', 'meta_tags' => 'Meta tags'],
	'Publishing' => [
		'proj_comm_feat' => 'Featured communities', 'proj_showonhomepage' => 'Homepage setting', 'proj_markfeat' => 'Featured setting',
		'proj_posted_on' => 'Posted on date', 'is_active' => 'Website status',
	],
];
$pend_fields = [];
foreach ($pend_sections as $section_fields) {
	$pend_fields += $section_fields;
}
$pend_total = count($pend_fields);
$pend_section_keys = array_keys($pend_sections);
$pend_levels = ['complete' => 'Complete', 'some' => '1 to 5 missing', 'many' => '6 or more missing'];
$pend_sorts = ['most' => 'Most missing first', 'least' => 'Least missing first', 'name' => 'Name A-Z', 'newest' => 'Latest first'];

// ---------------------------------------------------------------
// Filters (all optional, all from the address bar)
// ---------------------------------------------------------------
$f_type = isset($_GET['type']) && isset($pend_types[(int) $_GET['type']]) ? (int) $_GET['type'] : 0;
$f_status = in_array($_GET['status'] ?? '', ['active', 'inactive'], true) ? $_GET['status'] : '';
$f_source = ($_GET['source'] ?? '') === 'geniemap' ? 'geniemap' : '';
$f_level = isset($_GET['level']) && is_string($_GET['level']) && isset($pend_levels[$_GET['level']]) ? $_GET['level'] : '';
// "Missing" is either one detail (its column name) or a whole section ("s0", "s1" ...).
$f_missing = '';
if (isset($_GET['missing']) && is_string($_GET['missing'])) {
	if (isset($pend_fields[$_GET['missing']])) {
		$f_missing = $_GET['missing'];
	} elseif (preg_match('/^s(\d+)$/', $_GET['missing'], $match) && isset($pend_section_keys[(int) $match[1]])) {
		$f_missing = 's' . (int) $match[1];
	}
}
$f_sort = isset($_GET['sort']) && is_string($_GET['sort']) && isset($pend_sorts[$_GET['sort']]) ? $_GET['sort'] : 'most';
$f_search = isset($_GET['q']) && is_string($_GET['q']) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$f_per_page = (int) ($_GET['per'] ?? 25);
if (!in_array($f_per_page, [25, 50, 100], true)) {
	$f_per_page = 25;
}
$f_page = max(1, (int) ($_GET['page'] ?? 1));

// Address of this page with the current filters, with some of them changed.
function pend_url(array $change = [])
{
	global $f_source, $f_type, $f_status, $f_level, $f_missing, $f_sort, $f_search, $f_per_page, $f_page;
	$params = array_merge([
		'type' => $f_type ?: '', 'status' => $f_status, 'source' => $f_source, 'level' => $f_level, 'missing' => $f_missing,
		'sort' => $f_sort === 'most' ? '' : $f_sort, 'q' => $f_search,
		'per' => $f_per_page === 25 ? '' : $f_per_page, 'page' => $f_page === 1 ? '' : $f_page,
	], $change);
	$params = array_filter($params, function ($value) {
		return $value !== '' && $value !== null;
	});
	return BASE_URL . 'manage-pending-work-project.php' . ($params ? '?' . http_build_query($params) : '');
}

function pend_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared read query and returns its rows.
function pend_query($conn, $sql, array $params = [])
{
	$stmt = mysqli_prepare($conn, $sql);
	if (!$stmt) {
		error_log('Pending work query failed: ' . mysqli_error($conn));
		return [];
	}
	if ($params) {
		$types = '';
		foreach ($params as $param) {
			$types .= is_int($param) ? 'i' : 's';
		}
		mysqli_stmt_bind_param($stmt, $types, ...$params);
	}
	if (!mysqli_stmt_execute($stmt)) {
		error_log('Pending work query failed: ' . mysqli_stmt_error($stmt));
		mysqli_stmt_close($stmt);
		return [];
	}
	$result = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	$rows = [];
	while ($result && $row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}

// ---------------------------------------------------------------
// The list
// ---------------------------------------------------------------
// A detail is missing when its column is empty. The column names come from the list above, never from the request.
$empty_sql = [];
foreach ($pend_fields as $column => $label) {
	$empty_sql[$column] = "(tp.`$column` IS NULL OR TRIM(CAST(tp.`$column` AS CHAR)) = '')";
}
$count_sql = '(' . implode(' + ', $empty_sql) . ')';

$from_sql = "FROM tbl_proj AS tp
	LEFT JOIN tbl_developer AS td ON tp.prj_dev_id = td.dev_id
	LEFT JOIN tbl_community AS tc ON tp.prj_comm_id = tc.comm_id";
// Projects imported from GenieMap carry a badge (the table exists once its migration has been run).
$has_genie = (bool) pend_query($conn, "SHOW TABLES LIKE 'tbl_geniemap_import'");
$from_sql .= $has_genie ? ' LEFT JOIN tbl_geniemap_import AS gm ON gm.prj_id = tp.prj_id' : '';
$genie_sql = $has_genie ? 'gm.genie_id' : 'NULL';
$genie_total = $has_genie ? (int) ((pend_query($conn, 'SELECT COUNT(*) AS n FROM tbl_geniemap_import gm JOIN tbl_proj tp ON tp.prj_id = gm.prj_id WHERE tp.prj_type_id IN (1, 2, 3, 4)') ?: [['n' => 0]])[0]['n']) : 0;

// Every filter except the type, so the type tabs can show their own counts.
$where = ['tp.prj_type_id IN (1, 2, 3, 4)'];
$params = [];
if ($f_search !== '') {
	$like = '%' . addcslashes($f_search, '%_\\') . '%';
	$where[] = '(tp.proj_name LIKE ? OR tp.proj_location LIKE ? OR td.dev_heading LIKE ? OR tc.comm_heading LIKE ?)';
	array_push($params, $like, $like, $like, $like);
}
if ($f_status !== '') {
	$where[] = $f_status === 'active' ? "tp.is_active = '1'" : "tp.is_active <> '1'";
}
if ($f_source !== '' && $has_genie) {
	$where[] = 'gm.genie_id IS NOT NULL';
}
if ($f_level !== '') {
	$where[] = ['complete' => "$count_sql = 0", 'some' => "$count_sql BETWEEN 1 AND 5", 'many' => "$count_sql > 5"][$f_level];
}
if ($f_missing !== '' && $f_missing[0] === 's' && isset($pend_section_keys[(int) substr($f_missing, 1)])) {
	$section_columns = array_keys($pend_sections[$pend_section_keys[(int) substr($f_missing, 1)]]);
	$where[] = '(' . implode(' OR ', array_intersect_key($empty_sql, array_flip($section_columns))) . ')';
} elseif ($f_missing !== '') {
	$where[] = $empty_sql[$f_missing];
}
$where_sql = 'WHERE ' . implode(' AND ', $where);

$type_counts = [];
foreach (pend_query($conn, "SELECT tp.prj_type_id, COUNT(*) AS n $from_sql $where_sql GROUP BY tp.prj_type_id", $params) as $row) {
	$type_counts[(int) $row['prj_type_id']] = (int) $row['n'];
}
$count_all = array_sum($type_counts);
$total = $f_type ? ($type_counts[$f_type] ?? 0) : $count_all;

$list_where_sql = $where_sql . ($f_type ? ' AND tp.prj_type_id = ' . $f_type : '');
$order_sql = [
	'most' => 'missing_count DESC, tp.prj_id DESC',
	'least' => 'missing_count ASC, tp.prj_id DESC',
	'name' => 'tp.proj_name ASC',
	'newest' => 'tp.tstp DESC, tp.prj_id DESC',
][$f_sort];

$pages = max(1, (int) ceil($total / $f_per_page));
$f_page = min($f_page, $pages);
$offset = ($f_page - 1) * $f_per_page;

$flag_sql = [];
foreach ($empty_sql as $column => $sql) {
	$flag_sql[] = "$sql AS `e_$column`";
}
$projects = pend_query(
	$conn,
	"SELECT $genie_sql AS genie_id, tp.prj_id, tp.prj_type_id, tp.proj_name, tp.proj_location, tp.prj_thumb_img, tp.is_active,
		td.dev_heading AS proj_dev_name, tc.comm_heading AS proj_comm_name, $count_sql AS missing_count, " . implode(', ', $flag_sql) . "
	$from_sql $list_where_sql ORDER BY $order_sql LIMIT ?, ?",
	array_merge($params, [$offset, $f_per_page])
);

$stat = pend_query($conn, "SELECT COUNT(*) AS total, SUM(n = 0) AS complete, SUM(n BETWEEN 1 AND 5) AS some, SUM(n > 5) AS many, AVG(n) AS average
	FROM (SELECT $count_sql AS n FROM tbl_proj AS tp WHERE tp.prj_type_id IN (1, 2, 3, 4)) AS counted");
$stat = $stat ? $stat[0] : ['total' => 0, 'complete' => 0, 'some' => 0, 'many' => 0, 'average' => 0];
$average_filled = (int) $stat['total'] > 0 ? (int) round(($pend_total - (float) $stat['average']) * 100 / $pend_total) : 0;

$filters_on = $f_search !== '' || $f_status !== '' || $f_level !== '' || $f_missing !== '' || $f_source !== '';
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

							<!--begin::Pending work-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-geniemap.css?v=3" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-pending.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-pending">

								<!-- Title and numbers -->
								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1>Pending work</h1>
										<p>Which details are still empty on each project, out of the <?= $pend_total ?> that are checked.</p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= number_format((int) $stat['complete']) ?></strong><span>Complete</span></div>
										<div><strong><?= number_format((int) $stat['some']) ?></strong><span>1 to 5 missing</span></div>
										<div><strong><?= number_format((int) $stat['many']) ?></strong><span>6 or more missing</span></div>
										<div><strong><?= $average_filled ?>%</strong><span>Filled on average</span></div>
									</div>
									<div class="dre-actions">
										<a href="<?= BASE_URL ?>manage-project.php"><i class="bi bi-buildings"></i> All projects</a>
									</div>
								</div>

								<!-- Kind of listing -->
								<nav class="dre-leads-tabs" aria-label="Kind of listing">
									<a href="<?= pend_e(pend_url(['type' => '', 'page' => ''])) ?>" class="<?= $f_type === 0 ? 'is-on' : '' ?>"><i class="bi bi-collection"></i> All <b><?= number_format($count_all) ?></b></a>
									<?php foreach ($pend_types as $type_id => $type_name): ?>
										<a href="<?= pend_e(pend_url(['type' => $type_id, 'page' => ''])) ?>" class="<?= $f_type === $type_id ? 'is-on' : '' ?>"><?= pend_e($type_name) ?> <b><?= number_format($type_counts[$type_id] ?? 0) ?></b></a>
									<?php endforeach; ?>
									<?php if ($has_genie): ?>
										<a href="<?= pend_e(pend_url(['source' => $f_source !== '' ? '' : 'geniemap', 'page' => ''])) ?>" class="dre-genie-tab<?= $f_source !== '' ? ' is-on' : '' ?>" title="<?= $f_source !== '' ? 'Show projects from every source again' : 'Show only the projects imported from GenieMap' ?>"><i class="bi bi-geo-alt-fill"></i> GenieMap <b><?= number_format($genie_total) ?></b><?php if ($f_source !== ''): ?> <i class="bi bi-x-lg"></i><?php endif; ?></a>
									<?php endif; ?>
								</nav>

								<!-- Search and filters -->
								<form method="get" action="<?= BASE_URL ?>manage-pending-work-project.php" class="dre-glass dre-leads-filter dre-proj-filter">
									<?php if ($f_type): ?><input type="hidden" name="type" value="<?= $f_type ?>"><?php endif; ?>
									<label class="dre-leads-search">
										<span>Search</span>
										<i class="bi bi-search"></i>
										<input type="search" name="q" value="<?= pend_e($f_search) ?>" placeholder="Project, location, developer or community" autocomplete="off">
									</label>
									<label>
										<span>Missing</span>
										<select name="missing">
											<option value="">Anything</option>
											<?php foreach ($pend_section_keys as $index => $section_name): ?>
												<optgroup label="<?= pend_e($section_name) ?>">
													<option value="s<?= $index ?>" <?= $f_missing === 's' . $index ? 'selected' : '' ?>>Anything in <?= pend_e($section_name) ?></option>
													<?php foreach ($pend_sections[$section_name] as $column => $label): ?>
														<option value="<?= pend_e($column) ?>" <?= $f_missing === $column ? 'selected' : '' ?>><?= pend_e($label) ?></option>
													<?php endforeach; ?>
												</optgroup>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span>How much is missing</span>
										<select name="level">
											<option value="">Any</option>
											<?php foreach ($pend_levels as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_level === $key ? 'selected' : '' ?>><?= pend_e($label) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span>Website status</span>
										<select name="status">
											<option value="">Any</option>
											<option value="active" <?= $f_status === 'active' ? 'selected' : '' ?>>Live</option>
											<option value="inactive" <?= $f_status === 'inactive' ? 'selected' : '' ?>>Hidden</option>
										</select>
									</label>
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
											<?php foreach ($pend_sorts as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_sort === $key ? 'selected' : '' ?>><?= pend_e($label) ?></option>
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
									<div class="dre-leads-filter-buttons">
										<button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-funnel"></i> Apply</button>
										<?php if ($filters_on || $f_sort !== 'most'): ?>
											<a class="dre-btn" href="<?= pend_e(pend_url(['q' => '', 'status' => '', 'level' => '', 'missing' => '', 'sort' => '', 'page' => ''])) ?>">Clear</a>
										<?php endif; ?>
									</div>
								</form>

								<!-- The list -->
								<div class="dre-glass dre-leads-list">
									<div class="dre-h">
										<h3>
											<?= number_format($total) ?> <?= $total === 1 ? 'project' : 'projects' ?><?= $f_type ? ' &middot; ' . pend_e($pend_types[$f_type]) : '' ?>
											<small>
												<?php if ($total > 0): ?>
													Showing <?= number_format($offset + 1) ?>&ndash;<?= number_format($offset + count($projects)) ?><?= $filters_on ? ' &middot; filtered' : '' ?>
												<?php else: ?>
													Nothing matches<?= $filters_on ? ' these filters' : '' ?>
												<?php endif; ?>
											</small>
										</h3>
									</div>

									<?php if (!$projects): ?>
										<div class="dre-empty">
											<i class="bi bi-check2-circle" style="font-size: 2rem;"></i><br>
											No projects to show.
											<?php if ($filters_on || $f_type): ?>
												<a href="<?= BASE_URL ?>manage-pending-work-project.php">Show all projects</a>
											<?php endif; ?>
										</div>
									<?php else: ?>
										<div class="dre-proj-table" role="table" aria-label="Projects and their missing details">
											<div class="dre-proj-row dre-proj-head" role="row">
												<div role="columnheader">Project</div>
												<div role="columnheader">Developer and community</div>
												<div role="columnheader">Filled in</div>
												<div role="columnheader">What is missing</div>
												<div role="columnheader"><span class="sr-only">Actions</span></div>
											</div>
											<?php foreach ($projects as $row):
												$id = (int) $row['prj_id'];
												$is_live = $row['is_active'] === '1';
												$thumb = trim(explode(',', (string) $row['prj_thumb_img'])[0]);
												$missing_count = (int) $row['missing_count'];
												$filled = $pend_total - $missing_count;
												$percent = (int) round($filled * 100 / $pend_total);
												$level = $missing_count === 0 ? 'good' : ($missing_count > 5 ? 'bad' : 'warn');
												// Missing details, kept under the section of the form they belong to.
												$missing_by_section = [];
												foreach ($pend_sections as $section_name => $section_fields) {
													foreach ($section_fields as $column => $label) {
														if ((int) $row['e_' . $column] === 1) {
															$missing_by_section[$section_name][] = $label;
														}
													}
												}
												$edit_url = BASE_URL . 'edit-project.php?edit=' . base64_encode($id);
											?>
												<div class="dre-proj-row<?= $is_live ? '' : ' is-hidden' ?>" role="row">
													<div class="dre-proj-main" role="cell">
														<a class="dre-proj-thumb" href="<?= pend_e($edit_url) ?>" tabindex="-1" aria-hidden="true">
															<?php if ($thumb !== ''): ?>
																<img src="<?= BASE_URL ?>assets/media/project/thumb/<?= pend_e(rawurlencode($thumb)) ?>" alt="" loading="lazy" onerror="this.remove()">
															<?php endif; ?>
															<i class="bi bi-building"></i>
														</a>
														<div>
															<a class="dre-proj-name" href="<?= pend_e($edit_url) ?>"><?= trim((string) $row['proj_name']) !== '' ? pend_e($row['proj_name']) : 'Project without a name' ?></a>
															<span class="dre-proj-sub"><i class="bi bi-geo-alt"></i> <?= trim((string) $row['proj_location']) !== '' ? pend_e($row['proj_location']) : 'No location' ?></span>
															<div class="dre-proj-pills">
																<span class="dre-pill"><?= pend_e($pend_types[(int) $row['prj_type_id']] ?? 'Other') ?></span>
																<span class="dre-pill <?= $is_live ? 'dre-pill-good' : 'dre-pill-bad' ?>"><?= $is_live ? 'Live' : 'Hidden' ?></span>
																<?php if (!empty($row['genie_id'])): ?><span class="dre-pill dre-pill-genie" title="This project was imported from GenieMap"><i class="bi bi-geo-alt-fill"></i> Imported from GenieMap</span><?php endif; ?>
															</div>
														</div>
													</div>
													<div class="dre-proj-where" role="cell">
														<span><i class="bi bi-building"></i> <?= trim((string) $row['proj_dev_name']) !== '' ? pend_e($row['proj_dev_name']) : '<em>No developer</em>' ?></span>
														<span><i class="bi bi-map"></i> <?= trim((string) $row['proj_comm_name']) !== '' ? pend_e($row['proj_comm_name']) : '<em>No community</em>' ?></span>
													</div>
													<div class="dre-pend-meter dre-pend-<?= $level ?>" role="cell">
														<strong><?= $percent ?>%</strong>
														<div class="dre-pend-bar" role="progressbar" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?= $percent ?> percent filled in"><i style="width: <?= $percent ?>%"></i></div>
														<span><?= $filled ?> of <?= $pend_total ?> filled</span>
													</div>
													<div class="dre-pend-missing" role="cell">
														<?php if ($missing_count === 0): ?>
															<span class="dre-pend-done"><i class="bi bi-check2-circle"></i> Everything is filled in</span>
														<?php else: ?>
															<details>
																<summary>
																	<b><?= $missing_count ?> missing</b> in <?= count($missing_by_section) ?> <?= count($missing_by_section) === 1 ? 'section' : 'sections' ?>
																	<span class="dre-pend-sections">
																		<?php foreach ($missing_by_section as $section_name => $labels): ?>
																			<span class="dre-pill"><?= pend_e($section_name) ?> <b><?= count($labels) ?></b></span>
																		<?php endforeach; ?>
																	</span>
																</summary>
																<dl>
																	<?php foreach ($missing_by_section as $section_name => $labels): ?>
																		<dt><?= pend_e($section_name) ?></dt>
																		<dd><?= pend_e(implode(', ', $labels)) ?></dd>
																	<?php endforeach; ?>
																</dl>
															</details>
														<?php endif; ?>
													</div>
													<div class="dre-proj-actions" role="cell">
														<a class="dre-btn dre-btn-main" href="<?= pend_e($edit_url) ?>" aria-label="Edit <?= pend_e($row['proj_name']) ?>"><i class="bi bi-pencil"></i> Fill in</a>
													</div>
												</div>
											<?php endforeach; ?>
										</div>

										<?php if ($pages > 1): ?>
											<nav class="dre-leads-pages" aria-label="Pages">
												<?php if ($f_page > 1): ?>
													<a href="<?= pend_e(pend_url(['page' => $f_page - 1])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
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
													<a href="<?= pend_e(pend_url(['page' => $number])) ?>" class="<?= $number === $f_page ? 'is-on' : '' ?>" <?= $number === $f_page ? 'aria-current="page"' : '' ?>><?= $number ?></a>
												<?php $previous = $number;
												endforeach; ?>
												<?php if ($f_page < $pages): ?>
													<a href="<?= pend_e(pend_url(['page' => $f_page + 1])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
												<?php endif; ?>
											</nav>
										<?php endif; ?>
									<?php endif; ?>
								</div>
							</div>
							<!--end::Pending work-->

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