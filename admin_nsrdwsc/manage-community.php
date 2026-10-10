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

$comm_sorts = ['newest' => 'Newest first', 'name' => 'Name A-Z', 'projects' => 'Most projects first', 'updated' => 'Last changed first'];
$comm_missing = ['thumb' => 'Card image', 'inner' => 'Inner image', 'long' => 'Description', 'meta' => 'Meta description', 'highlights' => 'Location highlights', 'nearby' => 'Nearby places'];

// ---------------------------------------------------------------
// Filters (all optional, all from the address bar)
// ---------------------------------------------------------------
$f_status = in_array($_GET['status'] ?? '', ['active', 'inactive'], true) ? $_GET['status'] : '';
$f_home = ($_GET['home'] ?? '') === '1' ? '1' : '';
$f_featured = ($_GET['featured'] ?? '') === '1' ? '1' : '';
$f_sync = in_array($_GET['sync'] ?? '', ['pushed', 'pending'], true) ? $_GET['sync'] : '';
$f_missing = isset($_GET['missing']) && is_string($_GET['missing']) && isset($comm_missing[$_GET['missing']]) ? $_GET['missing'] : '';
$f_sort = isset($_GET['sort']) && is_string($_GET['sort']) && isset($comm_sorts[$_GET['sort']]) ? $_GET['sort'] : 'newest';
$f_search = isset($_GET['q']) && is_string($_GET['q']) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$f_per_page = (int) ($_GET['per'] ?? 25);
if (!in_array($f_per_page, [25, 50, 100], true)) {
	$f_per_page = 25;
}
$f_page = max(1, (int) ($_GET['page'] ?? 1));

// Address of this page with the current filters, with some of them changed.
function comm_url(array $change = [])
{
	global $f_status, $f_home, $f_featured, $f_sync, $f_missing, $f_sort, $f_search, $f_per_page, $f_page;
	$params = array_merge([
		'status' => $f_status, 'home' => $f_home, 'featured' => $f_featured, 'sync' => $f_sync, 'missing' => $f_missing,
		'sort' => $f_sort === 'newest' ? '' : $f_sort, 'q' => $f_search,
		'per' => $f_per_page === 25 ? '' : $f_per_page, 'page' => $f_page === 1 ? '' : $f_page,
	], $change);
	$params = array_filter($params, function ($value) {
		return $value !== '' && $value !== null;
	});
	return BASE_URL . 'manage-community.php' . ($params ? '?' . http_build_query($params) : '');
}

function comm_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function comm_query($conn, $sql, array $params = [])
{
	$stmt = mysqli_prepare($conn, $sql);
	if (!$stmt) {
		error_log('Community query failed: ' . mysqli_error($conn));
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
		error_log('Community query failed: ' . mysqli_stmt_error($stmt));
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

// Switches one community on or off the website and records the change.
function comm_set_active($conn, $logger, $id, $active)
{
	$old = comm_query($conn, "SELECT comm_id, comm_heading, is_active FROM tbl_community WHERE comm_id = ?", [$id]);
	if (!$old) {
		return false;
	}
	$value = $active ? '1' : '2';
	if ($old[0]['is_active'] === $value) {
		return true;
	}
	if (!comm_query($conn, "UPDATE tbl_community SET is_active = ? WHERE comm_id = ?", [$value, $id])) {
		return false;
	}
	$logger->log('UPDATE', 'tbl_community', $id, ['is_active' => $old[0]['is_active']], ['is_active' => $value], ($active ? 'Community shown on website: ' : 'Community hidden from website: ') . $old[0]['comm_heading']);
	return true;
}

// ---------------------------------------------------------------
// Changes (POST only, with a token)
// ---------------------------------------------------------------
$notice = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['comm_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$action = is_string($_POST['comm_action']) ? $_POST['comm_action'] : '';
	$done = '';

	if ($token_ok && ($action === 'show' || $action === 'hide')) {
		// One community, or every ticked community.
		$ids = isset($_POST['comm_id']) ? [(int) $_POST['comm_id']] : array_map('intval', (array) ($_POST['community_ids'] ?? []));
		$ids = array_slice(array_unique(array_filter($ids)), 0, 200);
		$changed = 0;
		foreach ($ids as $id) {
			$changed += comm_set_active($conn, $logger, $id, $action === 'show') ? 1 : 0;
		}
		$done = $changed > 0 ? ($action === 'show' ? 'shown' : 'hidden') . '-' . $changed : 'none';
	} elseif ($token_ok && $action === 'delete') {
		$id = (int) ($_POST['comm_id'] ?? 0);
		$community = $id > 0 ? comm_query($conn, "SELECT * FROM tbl_community WHERE comm_id = ?", [$id]) : [];
		$used = $community ? comm_query($conn, "SELECT COUNT(*) AS n FROM tbl_proj WHERE prj_comm_id = ?", [$id]) : [];
		if ($community && (int) ($used[0]['n'] ?? 0) > 0) {
			// Its projects would be left without a community.
			$done = 'inuse-' . (int) $used[0]['n'];
		} elseif ($community) {
			$community = $community[0];
			// Files are removed only when a name is stored and the file is really there.
			foreach ([['assets/media/community/thumb/', 'comm_thumb_img'], ['assets/media/community/inner/', 'comm_inner_img'], ['assets/media/community/inner-thumb/', 'comm_inner_thumb_img'], ['assets/media/community/location/', 'comm_loc_main_img'], ['assets/media/community/home/', 'comm_home_img']] as $file) {
				$name = basename(trim((string) $community[$file[1]]));
				if ($name !== '' && is_file($file[0] . $name)) {
					@unlink($file[0] . $name);
				}
			}
			$old_data = $logger->getOldRecordData('tbl_community', $id);
			$logger->log('DELETE', 'tbl_community', $id, $old_data, null, 'Community Deleted: ' . $community['comm_heading']);
			$logger->archiveDeletedRecord('tbl_community', $id, $old_data);
			comm_query($conn, "DELETE FROM tbl_community WHERE comm_id = ?", [$id]);
			// Its highlights and nearby places belong to this community only.
			comm_query($conn, "DELETE FROM tbl_community_loc WHERE comm_id = ?", [$id]);
			comm_query($conn, "DELETE FROM tbl_community_map WHERE comm_id = ?", [$id]);
			$done = 'deleted';
		} else {
			$done = 'none';
		}
	}

	if ($done !== '') {
		header('Location: ' . comm_url(['done' => $done]));
		exit;
	}
	$notice = 'Nothing was changed. Please reload the page and try again.';
}

// ---------------------------------------------------------------
// The list
// ---------------------------------------------------------------
$highlights_sql = "(SELECT COUNT(*) FROM tbl_community_loc AS l WHERE l.comm_id = d.comm_id)";
$nearby_sql = "(SELECT COUNT(*) FROM tbl_community_map AS m WHERE m.comm_id = d.comm_id)";
$missing_sql = [
	'thumb' => "TRIM(d.comm_thumb_img) = ''",
	'inner' => "TRIM(d.comm_inner_img) = ''",
	'long' => "TRIM(d.comm_long_descp) = ''",
	'meta' => "TRIM(d.meta_description) = ''",
	'highlights' => "$highlights_sql = 0",
	'nearby' => "$nearby_sql = 0",
];
$where = ['1 = 1'];
$params = [];
if ($f_search !== '') {
	$like = '%' . addcslashes($f_search, '%_\\') . '%';
	$where[] = '(d.comm_heading LIKE ? OR d.comm_slug LIKE ? OR d.comm_location LIKE ? OR d.comm_address LIKE ?)';
	array_push($params, $like, $like, $like, $like);
}
if ($f_status !== '') {
	$where[] = $f_status === 'active' ? "d.is_active = '1'" : "d.is_active <> '1'";
}
if ($f_home !== '') {
	$where[] = "d.comm_showonhomepage = '1'";
}
if ($f_featured !== '') {
	$where[] = "d.comm_featured = '1'";
}
if ($f_sync !== '') {
	$where[] = $f_sync === 'pushed' ? 'd.app_sync_status = 1' : '(d.app_sync_status IS NULL OR d.app_sync_status <> 1)';
}
if ($f_missing !== '') {
	$where[] = $missing_sql[$f_missing];
}
$where_sql = 'WHERE ' . implode(' AND ', $where);

$counted = comm_query($conn, "SELECT COUNT(*) AS n FROM tbl_community AS d $where_sql", $params);
$total = $counted ? (int) $counted[0]['n'] : 0;

$projects_sql = "(SELECT COUNT(*) FROM tbl_proj AS p WHERE p.prj_comm_id = d.comm_id)";
$order_sql = [
	'newest' => 'd.comm_id DESC',
	'name' => 'd.comm_heading ASC',
	'projects' => 'project_count DESC, d.comm_heading ASC',
	'updated' => 'd.tstp DESC, d.comm_id DESC',
][$f_sort];

$pages = max(1, (int) ceil($total / $f_per_page));
$f_page = min($f_page, $pages);
$offset = ($f_page - 1) * $f_per_page;

$communities = comm_query(
	$conn,
	"SELECT d.comm_id, d.comm_thumb_img, d.comm_inner_img, d.comm_heading, d.comm_location, d.comm_long_descp, d.meta_description, d.comm_slug,
		d.comm_showonhomepage, d.comm_featured, d.is_active, d.app_sync_status, d.app_sync_date,
		$projects_sql AS project_count, $highlights_sql AS highlight_count, $nearby_sql AS nearby_count
	FROM tbl_community AS d $where_sql ORDER BY $order_sql LIMIT ?, ?",
	array_merge($params, [$offset, $f_per_page])
) ?: [];

$stat = comm_query($conn, "SELECT COUNT(*) AS total, SUM(is_active = '1') AS active, SUM(is_active <> '1') AS hidden, SUM(comm_showonhomepage = '1') AS home, SUM(comm_featured = '1') AS featured, SUM(app_sync_status = 1) AS pushed FROM tbl_community");
$stat = $stat ? $stat[0] : ['total' => 0, 'active' => 0, 'hidden' => 0, 'home' => 0, 'featured' => 0, 'pushed' => 0];

$filters_on = $f_search !== '' || $f_status !== '' || $f_home !== '' || $f_featured !== '' || $f_sync !== '' || $f_missing !== '';

// Messages after an action
$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : '';
if ($done === 'added') {
	$notice = 'The community was added.';
} elseif ($done === 'saved') {
	$notice = 'The changes were saved.';
} elseif ($done === 'deleted') {
	$notice = 'The community was deleted. Its highlights and nearby places were removed too. A copy is kept in the delete log.';
} elseif (preg_match('/^inuse-(\d+)$/', $done, $match)) {
	$notice = 'This community was not deleted: ' . $match[1] . ((int) $match[1] === 1 ? ' project still uses' : ' projects still use') . ' it. Move those projects to another community first, or hide the community instead.';
} elseif (preg_match('/^(shown|hidden)-(\d+)$/', $done, $match)) {
	$notice = $match[2] . ((int) $match[2] === 1 ? ' community is' : ' communities are') . ' now ' . ($match[1] === 'shown' ? 'live on the website.' : 'hidden from the website.');
} elseif ($done === 'none') {
	$notice = 'Nothing was changed.';
}
$push_message = is_string($_GET['msg'] ?? null) ? $_GET['msg'] : '';
if ($push_message === 'push_success') {
	$notice = 'The selected communities were pushed to the app.';
} elseif ($push_message === 'partial_success') {
	$notice = 'Some communities could not be pushed to the app. Failed IDs: ' . preg_replace('/[^0-9,]/', '', (string) ($_GET['failed'] ?? ''));
} elseif ($push_message === 'push_failed') {
	$notice = 'The communities could not be pushed to the app. Please try again.';
}
if (!empty($_SESSION['flash_message']['message'])) {
	$notice = (string) $_SESSION['flash_message']['message'];
}
unset($_SESSION['flash_message']);
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

							<!--begin::Communities-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-developers.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-communities.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-developers dre-communities">

								<!-- Title, numbers, main actions -->
								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1>Communities</h1>
										<p>The neighbourhoods projects belong to, each with its own page on the website.</p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= number_format((int) $stat['active']) ?></strong><span>Live</span></div>
										<div><strong><?= number_format((int) $stat['hidden']) ?></strong><span>Hidden</span></div>
										<div><strong><?= number_format((int) $stat['featured']) ?></strong><span>Featured</span></div>
										<div><strong><?= number_format((int) $stat['home']) ?></strong><span>On homepage</span></div>
										<div><strong><?= number_format((int) $stat['pushed']) ?></strong><span>In the app</span></div>
									</div>
									<div class="dre-actions">
										<a class="dre-primary" href="<?= BASE_URL ?>add-community.php"><i class="bi bi-plus-lg"></i> Add community</a>
										<a href="<?= BASE_URL ?>export-community.php"><i class="bi bi-download"></i> Export XLS</a>
									</div>
								</div>

								<?php if ($notice !== ''): ?>
									<div class="dre-leads-notice" role="status" id="devNotice"><i class="bi bi-info-circle"></i> <?= comm_e($notice) ?></div>
								<?php endif; ?>

								<!-- Search and filters -->
								<form method="get" action="<?= BASE_URL ?>manage-community.php" class="dre-glass dre-leads-filter dre-proj-filter">
									<label class="dre-leads-search">
										<span>Search</span>
										<i class="bi bi-search"></i>
										<input type="search" name="q" value="<?= comm_e($f_search) ?>" placeholder="Community name, location or address" autocomplete="off">
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
										<span>Missing</span>
										<select name="missing">
											<option value="">Nothing selected</option>
											<?php foreach ($comm_missing as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_missing === $key ? 'selected' : '' ?>><?= comm_e($label) ?></option>
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
									<label>
										<span>Sort by</span>
										<select name="sort">
											<?php foreach ($comm_sorts as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_sort === $key ? 'selected' : '' ?>><?= comm_e($label) ?></option>
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
										<input type="checkbox" name="featured" value="1" <?= $f_featured !== '' ? 'checked' : '' ?>> Featured only
									</label>
									<label class="dre-proj-check">
										<input type="checkbox" name="home" value="1" <?= $f_home !== '' ? 'checked' : '' ?>> On the homepage only
									</label>
									<div class="dre-leads-filter-buttons">
										<button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-funnel"></i> Apply</button>
										<?php if ($filters_on || $f_sort !== 'newest'): ?>
											<a class="dre-btn" href="<?= BASE_URL ?>manage-community.php">Clear</a>
										<?php endif; ?>
									</div>
								</form>

								<!-- The form the ticked communities belong to. Its buttons choose what happens. -->
								<form method="post" action="<?= comm_e(comm_url()) ?>" id="devBulkForm">
									<input type="hidden" name="csrf" value="<?= comm_e($_SESSION['admin_csrf']) ?>">
								</form>

								<!-- The list -->
								<div class="dre-glass dre-leads-list">
									<div class="dre-h">
										<h3>
											<?= number_format($total) ?> <?= $total === 1 ? 'community' : 'communities' ?>
											<small>
												<?php if ($total > 0): ?>
													Showing <?= number_format($offset + 1) ?>&ndash;<?= number_format($offset + count($communities)) ?><?= $filters_on ? ' &middot; filtered' : '' ?>
												<?php else: ?>
													Nothing matches<?= $filters_on ? ' these filters' : '' ?>
												<?php endif; ?>
											</small>
										</h3>
										<?php if ($communities): ?>
											<label class="dre-proj-all"><input type="checkbox" id="devSelectAll"> Select all on this page</label>
										<?php endif; ?>
									</div>

									<?php if (!$communities): ?>
										<div class="dre-empty">
											<i class="bi bi-building" style="font-size: 2rem;"></i><br>
											No communities to show.
											<?php if ($filters_on): ?>
												<a href="<?= BASE_URL ?>manage-community.php">Show all communities</a>
											<?php endif; ?>
										</div>
									<?php else: ?>
										<div class="dre-proj-table" role="table" aria-label="Communities">
											<div class="dre-proj-row dre-proj-head" role="row">
												<div role="columnheader"><span class="sr-only">Select</span></div>
												<div role="columnheader">Community</div>
												<div role="columnheader">Page content</div>
												<div role="columnheader">Projects</div>
												<div role="columnheader">Status</div>
												<div role="columnheader"><span class="sr-only">Actions</span></div>
											</div>
											<?php foreach ($communities as $row):
												$id = (int) $row['comm_id'];
												$is_live = $row['is_active'] === '1';
												$thumb = trim((string) $row['comm_thumb_img']);
												$edit_url = BASE_URL . 'edit-community.php?edit=' . base64_encode($id);
												$place = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $row['comm_location']), ENT_QUOTES, 'UTF-8')));
												$highlight_count = (int) $row['highlight_count'];
												$nearby_count = (int) $row['nearby_count'];
												$project_count = (int) $row['project_count'];
												$missing = [];
												if ($thumb === '') {
													$missing[] = 'card image';
												}
												if (trim((string) $row['comm_inner_img']) === '') {
													$missing[] = 'inner image';
												}
												if (trim(strip_tags((string) $row['comm_long_descp'])) === '') {
													$missing[] = 'description';
												}
												if (trim((string) $row['meta_description']) === '') {
													$missing[] = 'meta description';
												}
											?>
												<div class="dre-proj-row<?= $is_live ? '' : ' is-hidden' ?>" role="row">
													<div class="dre-proj-pick" role="cell">
														<input type="checkbox" name="community_ids[]" value="<?= $id ?>" form="devBulkForm" class="dre-proj-box" aria-label="Select <?= comm_e($row['comm_heading']) ?>">
													</div>
													<div class="dre-proj-main" role="cell">
														<a class="dre-proj-thumb" href="<?= comm_e($edit_url) ?>" tabindex="-1" aria-hidden="true">
															<?php if ($thumb !== ''): ?>
																<img src="<?= BASE_URL ?>assets/media/community/thumb/<?= comm_e(rawurlencode($thumb)) ?>" alt="" loading="lazy" onerror="this.remove()">
															<?php endif; ?>
															<i class="bi bi-geo-alt"></i>
														</a>
														<div>
															<a class="dre-proj-name" href="<?= comm_e($edit_url) ?>"><?= trim((string) $row['comm_heading']) !== '' ? comm_e($row['comm_heading']) : 'Community without a name' ?></a>
															<span class="dre-proj-sub"><i class="bi bi-geo-alt"></i> <?= $place !== '' ? comm_e(mb_strimwidth($place, 0, 60, '...')) : 'No location' ?></span>
															<?php if ($row['comm_featured'] === '1' || $row['comm_showonhomepage'] === '1'): ?>
																<div class="dre-proj-pills">
																	<?php if ($row['comm_featured'] === '1'): ?><span class="dre-pill dre-pill-good"><i class="bi bi-star"></i> Featured</span><?php endif; ?>
																	<?php if ($row['comm_showonhomepage'] === '1'): ?><span class="dre-pill dre-pill-ai"><i class="bi bi-house-heart"></i> Homepage</span><?php endif; ?>
																</div>
															<?php endif; ?>
														</div>
													</div>
													<div class="dre-dev-about dre-comm-content" role="cell">
														<a href="<?= BASE_URL ?>manage-comm-loc.php?comm_id=<?= $id ?>" class="<?= $highlight_count === 0 ? 'is-empty' : '' ?>"><i class="bi bi-stars"></i> <?= $highlight_count ?> <?= $highlight_count === 1 ? 'highlight' : 'highlights' ?></a>
														<a href="<?= comm_e($edit_url) ?>#sec-nearby" class="<?= $nearby_count === 0 ? 'is-empty' : '' ?>"><i class="bi bi-signpost-2"></i> <?= $nearby_count ?> nearby <?= $nearby_count === 1 ? 'place' : 'places' ?></a>
														<?php if ($missing): ?>
															<span class="dre-proj-missing" title="Details that are not filled in"><i class="bi bi-exclamation-triangle"></i> Missing: <?= comm_e(implode(', ', $missing)) ?></span>
														<?php endif; ?>
													</div>
													<div class="dre-proj-facts" role="cell">
														<?php if ($project_count > 0): ?>
															<a class="dre-dev-count" href="<?= BASE_URL ?>manage-project.php?comm=<?= $id ?>"><strong><?= number_format($project_count) ?></strong> <?= $project_count === 1 ? 'project' : 'projects' ?></a>
														<?php else: ?>
															<span>No projects yet</span>
														<?php endif; ?>
													</div>
													<div class="dre-proj-status" role="cell">
														<form method="post" action="<?= comm_e(comm_url()) ?>">
															<input type="hidden" name="csrf" value="<?= comm_e($_SESSION['admin_csrf']) ?>">
															<input type="hidden" name="comm_id" value="<?= $id ?>">
															<button type="submit" name="comm_action" value="<?= $is_live ? 'hide' : 'show' ?>" class="dre-switch<?= $is_live ? ' is-on' : '' ?>" role="switch" aria-checked="<?= $is_live ? 'true' : 'false' ?>" title="<?= $is_live ? 'Live on the website. Click to hide.' : 'Hidden from the website. Click to show.' ?>">
																<i></i><span><?= $is_live ? 'Live' : 'Hidden' ?></span>
															</button>
														</form>
														<span class="dre-proj-sync">
															<?php if ((int) $row['app_sync_status'] === 1): ?>
																<i class="bi bi-phone"></i> In the app<?= $row['app_sync_date'] && strtotime($row['app_sync_date']) > 0 ? ' &middot; ' . date('d M Y', strtotime($row['app_sync_date'])) : '' ?>
															<?php else: ?>
																<i class="bi bi-phone"></i> Not in the app
															<?php endif; ?>
														</span>
													</div>
													<div class="dre-proj-actions" role="cell">
														<a class="dre-icon-btn" href="<?= comm_e($edit_url) ?>" title="Edit" aria-label="Edit <?= comm_e($row['comm_heading']) ?>"><i class="bi bi-pencil"></i></a>
														<a class="dre-icon-btn" href="<?= comm_e(BASE_URL_HOME . 'community/' . $row['comm_slug']) ?>" target="_blank" rel="noopener" title="<?= $is_live ? 'View on the website' : 'Hidden communities do not open on the website' ?>" aria-label="View <?= comm_e($row['comm_heading']) ?> on the website"><i class="bi bi-box-arrow-up-right"></i></a>
														<div class="dropdown">
															<button type="button" class="dre-icon-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More" aria-label="More actions for <?= comm_e($row['comm_heading']) ?>"><i class="bi bi-three-dots"></i></button>
															<div class="dropdown-menu dropdown-menu-right">
																<a class="dropdown-item" href="<?= BASE_URL ?>manage-comm-loc.php?comm_id=<?= $id ?>"><i class="bi bi-stars mr-2"></i>Location highlights</a>
																<a class="dropdown-item" href="<?= comm_e($edit_url) ?>#sec-nearby"><i class="bi bi-signpost-2 mr-2"></i>Nearby places</a>
																<?php if ($project_count > 0): ?>
																	<a class="dropdown-item" href="<?= BASE_URL ?>manage-project.php?comm=<?= $id ?>"><i class="bi bi-buildings mr-2"></i>Its projects</a>
																<?php endif; ?>
																<div class="dropdown-divider"></div>
																<form method="post" action="<?= comm_e(comm_url()) ?>" class="dre-dev-delete" data-name="<?= comm_e($row['comm_heading']) ?>" data-projects="<?= $project_count ?>">
																	<input type="hidden" name="csrf" value="<?= comm_e($_SESSION['admin_csrf']) ?>">
																	<input type="hidden" name="comm_id" value="<?= $id ?>">
																	<button type="submit" name="comm_action" value="delete" class="dropdown-item text-danger"><i class="bi bi-trash3 mr-2"></i>Delete</button>
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
													<a href="<?= comm_e(comm_url(['page' => $f_page - 1])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
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
													<a href="<?= comm_e(comm_url(['page' => $number])) ?>" class="<?= $number === $f_page ? 'is-on' : '' ?>" <?= $number === $f_page ? 'aria-current="page"' : '' ?>><?= $number ?></a>
												<?php $previous = $number;
												endforeach; ?>
												<?php if ($f_page < $pages): ?>
													<a href="<?= comm_e(comm_url(['page' => $f_page + 1])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
												<?php endif; ?>
											</nav>
										<?php endif; ?>
									<?php endif; ?>
								</div>

								<!-- Appears when at least one community is ticked -->
								<div class="dre-proj-bulk" id="devBulkBar" hidden>
									<strong><span id="devBulkCount">0</span> selected</strong>
									<div>
										<button type="submit" form="devBulkForm" name="push_to_app" value="1" formaction="<?= BASE_URL ?>push-app/push-community-app.php" class="dre-btn dre-btn-main"><i class="bi bi-phone"></i> Push to app</button>
										<button type="submit" form="devBulkForm" name="comm_action" value="show" class="dre-btn" data-confirm="Show the selected communities on the website?"><i class="bi bi-eye"></i> Show on website</button>
										<button type="submit" form="devBulkForm" name="comm_action" value="hide" class="dre-btn" data-confirm="Hide the selected communities from the website?"><i class="bi bi-eye-slash"></i> Hide</button>
										<button type="button" class="dre-btn" id="devBulkClear">Clear</button>
									</div>
								</div>
							</div>

							<script>
								// Selection bar, confirmations, and the message that fades out.
								(function () {
									var boxes = Array.prototype.slice.call(document.querySelectorAll('.dre-proj-box'));
									var all = document.getElementById('devSelectAll');
									var bar = document.getElementById('devBulkBar');
									var count = document.getElementById('devBulkCount');

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
									document.getElementById('devBulkClear').addEventListener('click', function () {
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
									document.querySelectorAll('.dre-dev-delete').forEach(function (form) {
										form.addEventListener('submit', function (event) {
											var projects = parseInt(form.getAttribute('data-projects'), 10) || 0;
											if (projects > 0) {
												event.preventDefault();
												window.alert('"' + form.getAttribute('data-name') + '" cannot be deleted: ' + projects + (projects === 1 ? ' project still uses' : ' projects still use') + ' it. Move those projects to another community first, or hide this community instead.');
											} else if (!window.confirm('Delete "' + form.getAttribute('data-name') + '"? Its images, highlights and nearby places are removed too. A copy of the record is kept in the delete log.')) {
												event.preventDefault();
											}
										});
									});
									refresh();

									var notice = document.getElementById('devNotice');
									if (notice) {
										setTimeout(function () { notice.style.display = 'none'; }, 8000);
									}
								})();
							</script>
							<!--end::Communities-->

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