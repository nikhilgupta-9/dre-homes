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

$dev_sorts = ['newest' => 'Newest first', 'name' => 'Name A-Z', 'projects' => 'Most projects first', 'home' => 'Homepage order'];
$dev_missing = ['logo' => 'Logo', 'short' => 'Short description', 'long' => 'Long description', 'meta' => 'Meta description'];

// ---------------------------------------------------------------
// Filters (all optional, all from the address bar)
// ---------------------------------------------------------------
$f_status = in_array($_GET['status'] ?? '', ['active', 'inactive'], true) ? $_GET['status'] : '';
$f_home = (($_GET['home'] ?? '') === '1' || ($_GET['filter_home'] ?? '') === '1') ? '1' : '';
$f_sync = in_array($_GET['sync'] ?? '', ['pushed', 'pending'], true) ? $_GET['sync'] : '';
$f_missing = isset($_GET['missing']) && is_string($_GET['missing']) && isset($dev_missing[$_GET['missing']]) ? $_GET['missing'] : '';
$f_sort = isset($_GET['sort']) && is_string($_GET['sort']) && isset($dev_sorts[$_GET['sort']]) ? $_GET['sort'] : 'newest';
$f_search = isset($_GET['q']) && is_string($_GET['q']) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$f_per_page = (int) ($_GET['per'] ?? 25);
if (!in_array($f_per_page, [25, 50, 100], true)) {
	$f_per_page = 25;
}
$f_page = max(1, (int) ($_GET['page'] ?? 1));

// Address of this page with the current filters, with some of them changed.
function dev_url(array $change = [])
{
	global $f_status, $f_home, $f_sync, $f_missing, $f_sort, $f_search, $f_per_page, $f_page;
	$params = array_merge([
		'status' => $f_status, 'home' => $f_home, 'sync' => $f_sync, 'missing' => $f_missing,
		'sort' => $f_sort === 'newest' ? '' : $f_sort, 'q' => $f_search,
		'per' => $f_per_page === 25 ? '' : $f_per_page, 'page' => $f_page === 1 ? '' : $f_page,
	], $change);
	$params = array_filter($params, function ($value) {
		return $value !== '' && $value !== null;
	});
	return BASE_URL . 'manage-developer.php' . ($params ? '?' . http_build_query($params) : '');
}

function dev_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function dev_query($conn, $sql, array $params = [])
{
	$stmt = mysqli_prepare($conn, $sql);
	if (!$stmt) {
		error_log('Developer query failed: ' . mysqli_error($conn));
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
		error_log('Developer query failed: ' . mysqli_stmt_error($stmt));
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

// Switches one developer on or off the website and records the change.
function dev_set_active($conn, $logger, $id, $active)
{
	$old = dev_query($conn, "SELECT dev_id, dev_heading, is_active FROM tbl_developer WHERE dev_id = ?", [$id]);
	if (!$old) {
		return false;
	}
	$value = $active ? '1' : '2';
	if ($old[0]['is_active'] === $value) {
		return true;
	}
	if (!dev_query($conn, "UPDATE tbl_developer SET is_active = ? WHERE dev_id = ?", [$value, $id])) {
		return false;
	}
	$logger->log('UPDATE', 'tbl_developer', $id, ['is_active' => $old[0]['is_active']], ['is_active' => $value], ($active ? 'Developer shown on website: ' : 'Developer hidden from website: ') . $old[0]['dev_heading']);
	return true;
}

// ---------------------------------------------------------------
// Changes (POST only, with a token)
// ---------------------------------------------------------------
$notice = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['dev_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$action = is_string($_POST['dev_action']) ? $_POST['dev_action'] : '';
	$done = '';

	if ($token_ok && ($action === 'show' || $action === 'hide')) {
		// One developer, or every ticked developer.
		$ids = isset($_POST['dev_id']) ? [(int) $_POST['dev_id']] : array_map('intval', (array) ($_POST['developer_ids'] ?? []));
		$ids = array_slice(array_unique(array_filter($ids)), 0, 200);
		$changed = 0;
		foreach ($ids as $id) {
			$changed += dev_set_active($conn, $logger, $id, $action === 'show') ? 1 : 0;
		}
		$done = $changed > 0 ? ($action === 'show' ? 'shown' : 'hidden') . '-' . $changed : 'none';
	} elseif ($token_ok && $action === 'delete') {
		$id = (int) ($_POST['dev_id'] ?? 0);
		$developer = $id > 0 ? dev_query($conn, "SELECT * FROM tbl_developer WHERE dev_id = ?", [$id]) : [];
		$used = $developer ? dev_query($conn, "SELECT COUNT(*) AS n FROM tbl_proj WHERE prj_dev_id = ?", [$id]) : [];
		if ($developer && (int) ($used[0]['n'] ?? 0) > 0) {
			// Its projects would be left without a developer.
			$done = 'inuse-' . (int) $used[0]['n'];
		} elseif ($developer) {
			$developer = $developer[0];
			// Files are removed only when a name is stored and the file is really there.
			foreach ([['assets/media/developer/', 'dev_img'], ['assets/media/developer/choose-us/', 'choose_us_img'], ['assets/media/developer/homepage-image/', 'dev_homp_img'], ['assets/media/developer/inner-image/', 'dev_innr_img']] as $file) {
				$name = basename(trim((string) $developer[$file[1]]));
				if ($name !== '' && is_file($file[0] . $name)) {
					@unlink($file[0] . $name);
				}
			}
			$old_data = $logger->getOldRecordData('tbl_developer', $id);
			$logger->log('DELETE', 'tbl_developer', $id, $old_data, null, 'Developer Deleted: ' . $developer['dev_heading']);
			$logger->archiveDeletedRecord('tbl_developer', $id, $old_data);
			dev_query($conn, "DELETE FROM tbl_developer WHERE dev_id = ?", [$id]);
			$done = 'deleted';
		} else {
			$done = 'none';
		}
	}

	if ($done !== '') {
		header('Location: ' . dev_url(['done' => $done]));
		exit;
	}
	$notice = 'Nothing was changed. Please reload the page and try again.';
}

// ---------------------------------------------------------------
// The list
// ---------------------------------------------------------------
$missing_sql = [
	'logo' => "TRIM(d.dev_img) = ''",
	'short' => "TRIM(d.dev_short_descp) = ''",
	'long' => "TRIM(d.dev_long_descp) = ''",
	'meta' => "TRIM(d.meta_description) = ''",
];
$where = ['1 = 1'];
$params = [];
if ($f_search !== '') {
	$like = '%' . addcslashes($f_search, '%_\\') . '%';
	$where[] = '(d.dev_heading LIKE ? OR d.dev_slug LIKE ? OR d.dev_address LIKE ? OR d.dev_email LIKE ?)';
	array_push($params, $like, $like, $like, $like);
}
if ($f_status !== '') {
	$where[] = $f_status === 'active' ? "d.is_active = '1'" : "d.is_active <> '1'";
}
if ($f_home !== '') {
	$where[] = "d.dev_showonhomepage = '1'";
}
if ($f_sync !== '') {
	$where[] = $f_sync === 'pushed' ? 'd.app_sync_status = 1' : '(d.app_sync_status IS NULL OR d.app_sync_status <> 1)';
}
if ($f_missing !== '') {
	$where[] = $missing_sql[$f_missing];
}
$where_sql = 'WHERE ' . implode(' AND ', $where);

$counted = dev_query($conn, "SELECT COUNT(*) AS n FROM tbl_developer AS d $where_sql", $params);
$total = $counted ? (int) $counted[0]['n'] : 0;

$projects_sql = "(SELECT COUNT(*) FROM tbl_proj AS p WHERE p.prj_dev_id = d.dev_id)";
$order_sql = [
	'newest' => 'd.dev_id DESC',
	'name' => 'd.dev_heading ASC',
	'projects' => 'project_count DESC, d.dev_heading ASC',
	'home' => "d.dev_showonhomepage = '1' DESC, d.dev_order_no IS NULL, d.dev_order_no ASC, d.dev_heading ASC",
][$f_sort];

$pages = max(1, (int) ceil($total / $f_per_page));
$f_page = min($f_page, $pages);
$offset = ($f_page - 1) * $f_per_page;

$developers = dev_query(
	$conn,
	"SELECT d.dev_id, d.dev_img, d.dev_heading, d.dev_short_descp, d.dev_long_descp, d.meta_description, d.dev_slug, d.dev_showonhomepage,
		d.dev_order_no, d.is_active, d.app_sync_status, d.app_sync_date, $projects_sql AS project_count
	FROM tbl_developer AS d $where_sql ORDER BY $order_sql LIMIT ?, ?",
	array_merge($params, [$offset, $f_per_page])
) ?: [];

$stat = dev_query($conn, "SELECT COUNT(*) AS total, SUM(is_active = '1') AS active, SUM(is_active <> '1') AS hidden, SUM(dev_showonhomepage = '1') AS home, SUM(app_sync_status = 1) AS pushed FROM tbl_developer");
$stat = $stat ? $stat[0] : ['total' => 0, 'active' => 0, 'hidden' => 0, 'home' => 0, 'pushed' => 0];

$filters_on = $f_search !== '' || $f_status !== '' || $f_home !== '' || $f_sync !== '' || $f_missing !== '';

// Messages after an action
$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : '';
if ($done === 'added') {
	$notice = 'The developer was added.';
} elseif ($done === 'saved') {
	$notice = 'The changes were saved.';
} elseif ($done === 'deleted') {
	$notice = 'The developer was deleted. A copy is kept in the delete log.';
} elseif (preg_match('/^inuse-(\d+)$/', $done, $match)) {
	$notice = 'This developer was not deleted: ' . $match[1] . ((int) $match[1] === 1 ? ' project still uses' : ' projects still use') . ' it. Move those projects to another developer first, or hide the developer instead.';
} elseif (preg_match('/^(shown|hidden)-(\d+)$/', $done, $match)) {
	$notice = $match[2] . ((int) $match[2] === 1 ? ' developer is' : ' developers are') . ' now ' . ($match[1] === 'shown' ? 'live on the website.' : 'hidden from the website.');
} elseif ($done === 'none') {
	$notice = 'Nothing was changed.';
}
$push_message = is_string($_GET['msg'] ?? null) ? $_GET['msg'] : '';
if ($push_message === 'push_success') {
	$notice = 'The selected developers were pushed to the app.';
} elseif ($push_message === 'partial_success') {
	$notice = 'Some developers could not be pushed to the app. Please check and try again.';
} elseif ($push_message === 'push_failed') {
	$notice = 'The developers could not be pushed to the app. Please try again.';
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

							<!--begin::Developers-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-developers.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-developers">

								<!-- Title, numbers, main actions -->
								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1>Developers</h1>
										<p>The companies behind the projects, each with its own page on the website.</p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= number_format((int) $stat['active']) ?></strong><span>Live</span></div>
										<div><strong><?= number_format((int) $stat['hidden']) ?></strong><span>Hidden</span></div>
										<div><strong><?= number_format((int) $stat['home']) ?></strong><span>On homepage</span></div>
										<div><strong><?= number_format((int) $stat['pushed']) ?></strong><span>In the app</span></div>
									</div>
									<div class="dre-actions">
										<a class="dre-primary" href="<?= BASE_URL ?>add-developer.php"><i class="bi bi-plus-lg"></i> Add developer</a>
										<a href="<?= BASE_URL ?>export-developer.php"><i class="bi bi-download"></i> Export XLS</a>
									</div>
								</div>

								<?php if ($notice !== ''): ?>
									<div class="dre-leads-notice" role="status" id="devNotice"><i class="bi bi-info-circle"></i> <?= dev_e($notice) ?></div>
								<?php endif; ?>

								<!-- Search and filters -->
								<form method="get" action="<?= BASE_URL ?>manage-developer.php" class="dre-glass dre-leads-filter dre-proj-filter">
									<label class="dre-leads-search">
										<span>Search</span>
										<i class="bi bi-search"></i>
										<input type="search" name="q" value="<?= dev_e($f_search) ?>" placeholder="Developer name, address or email" autocomplete="off">
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
											<?php foreach ($dev_missing as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_missing === $key ? 'selected' : '' ?>><?= dev_e($label) ?></option>
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
											<?php foreach ($dev_sorts as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_sort === $key ? 'selected' : '' ?>><?= dev_e($label) ?></option>
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
											<a class="dre-btn" href="<?= BASE_URL ?>manage-developer.php">Clear</a>
										<?php endif; ?>
									</div>
								</form>

								<!-- The form the ticked developers belong to. Its buttons choose what happens. -->
								<form method="post" action="<?= dev_e(dev_url()) ?>" id="devBulkForm">
									<input type="hidden" name="csrf" value="<?= dev_e($_SESSION['admin_csrf']) ?>">
								</form>

								<!-- The list -->
								<div class="dre-glass dre-leads-list">
									<div class="dre-h">
										<h3>
											<?= number_format($total) ?> <?= $total === 1 ? 'developer' : 'developers' ?>
											<small>
												<?php if ($total > 0): ?>
													Showing <?= number_format($offset + 1) ?>&ndash;<?= number_format($offset + count($developers)) ?><?= $filters_on ? ' &middot; filtered' : '' ?>
												<?php else: ?>
													Nothing matches<?= $filters_on ? ' these filters' : '' ?>
												<?php endif; ?>
											</small>
										</h3>
										<?php if ($developers): ?>
											<label class="dre-proj-all"><input type="checkbox" id="devSelectAll"> Select all on this page</label>
										<?php endif; ?>
									</div>

									<?php if (!$developers): ?>
										<div class="dre-empty">
											<i class="bi bi-building" style="font-size: 2rem;"></i><br>
											No developers to show.
											<?php if ($filters_on): ?>
												<a href="<?= BASE_URL ?>manage-developer.php">Show all developers</a>
											<?php endif; ?>
										</div>
									<?php else: ?>
										<div class="dre-proj-table" role="table" aria-label="Developers">
											<div class="dre-proj-row dre-proj-head" role="row">
												<div role="columnheader"><span class="sr-only">Select</span></div>
												<div role="columnheader">Developer</div>
												<div role="columnheader">About</div>
												<div role="columnheader">Projects</div>
												<div role="columnheader">Status</div>
												<div role="columnheader"><span class="sr-only">Actions</span></div>
											</div>
											<?php foreach ($developers as $row):
												$id = (int) $row['dev_id'];
												$is_live = $row['is_active'] === '1';
												$logo = trim((string) $row['dev_img']);
												$edit_url = BASE_URL . 'edit-developer.php?edit=' . base64_encode($id);
												$about = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $row['dev_short_descp']), ENT_QUOTES, 'UTF-8')));
												$missing = [];
												if ($logo === '') {
													$missing[] = 'logo';
												}
												if ($about === '') {
													$missing[] = 'short description';
												}
												if (trim(strip_tags((string) $row['dev_long_descp'])) === '') {
													$missing[] = 'long description';
												}
												if (trim((string) $row['meta_description']) === '') {
													$missing[] = 'meta description';
												}
												$project_count = (int) $row['project_count'];
											?>
												<div class="dre-proj-row<?= $is_live ? '' : ' is-hidden' ?>" role="row">
													<div class="dre-proj-pick" role="cell">
														<input type="checkbox" name="developer_ids[]" value="<?= $id ?>" form="devBulkForm" class="dre-proj-box" aria-label="Select <?= dev_e($row['dev_heading']) ?>">
													</div>
													<div class="dre-proj-main" role="cell">
														<a class="dre-proj-thumb dre-dev-logo" href="<?= dev_e($edit_url) ?>" tabindex="-1" aria-hidden="true">
															<?php if ($logo !== ''): ?>
																<img src="<?= BASE_URL ?>assets/media/developer/<?= dev_e(rawurlencode($logo)) ?>" alt="" loading="lazy" onerror="this.remove()">
															<?php endif; ?>
															<i class="bi bi-building"></i>
														</a>
														<div>
															<a class="dre-proj-name" href="<?= dev_e($edit_url) ?>"><?= trim((string) $row['dev_heading']) !== '' ? dev_e($row['dev_heading']) : 'Developer without a name' ?></a>
															<span class="dre-proj-sub"><i class="bi bi-link-45deg"></i> /developer/<?= dev_e($row['dev_slug']) ?></span>
															<?php if ($row['dev_showonhomepage'] === '1'): ?>
																<div class="dre-proj-pills">
																	<span class="dre-pill dre-pill-ai"><i class="bi bi-house-heart"></i> Homepage<?= $row['dev_order_no'] !== null && (int) $row['dev_order_no'] > 0 ? ' &middot; position ' . (int) $row['dev_order_no'] : '' ?></span>
																</div>
															<?php endif; ?>
														</div>
													</div>
													<div class="dre-dev-about" role="cell">
														<span><?= $about !== '' ? dev_e(mb_strimwidth($about, 0, 110, '...')) : '<em>No short description</em>' ?></span>
														<?php if ($missing): ?>
															<span class="dre-proj-missing" title="Details that are not filled in"><i class="bi bi-exclamation-triangle"></i> Missing: <?= dev_e(implode(', ', $missing)) ?></span>
														<?php endif; ?>
													</div>
													<div class="dre-proj-facts" role="cell">
														<?php if ($project_count > 0): ?>
															<a class="dre-dev-count" href="<?= BASE_URL ?>manage-project.php?dev=<?= $id ?>"><strong><?= number_format($project_count) ?></strong> <?= $project_count === 1 ? 'project' : 'projects' ?></a>
														<?php else: ?>
															<span>No projects yet</span>
														<?php endif; ?>
													</div>
													<div class="dre-proj-status" role="cell">
														<form method="post" action="<?= dev_e(dev_url()) ?>">
															<input type="hidden" name="csrf" value="<?= dev_e($_SESSION['admin_csrf']) ?>">
															<input type="hidden" name="dev_id" value="<?= $id ?>">
															<button type="submit" name="dev_action" value="<?= $is_live ? 'hide' : 'show' ?>" class="dre-switch<?= $is_live ? ' is-on' : '' ?>" role="switch" aria-checked="<?= $is_live ? 'true' : 'false' ?>" title="<?= $is_live ? 'Live on the website. Click to hide.' : 'Hidden from the website. Click to show.' ?>">
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
														<a class="dre-icon-btn" href="<?= dev_e($edit_url) ?>" title="Edit" aria-label="Edit <?= dev_e($row['dev_heading']) ?>"><i class="bi bi-pencil"></i></a>
														<a class="dre-icon-btn" href="<?= dev_e(BASE_URL_HOME . 'developer/' . $row['dev_slug']) ?>" target="_blank" rel="noopener" title="<?= $is_live ? 'View on the website' : 'Hidden developers do not open on the website' ?>" aria-label="View <?= dev_e($row['dev_heading']) ?> on the website"><i class="bi bi-box-arrow-up-right"></i></a>
														<div class="dropdown">
															<button type="button" class="dre-icon-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More" aria-label="More actions for <?= dev_e($row['dev_heading']) ?>"><i class="bi bi-three-dots"></i></button>
															<div class="dropdown-menu dropdown-menu-right">
																<a class="dropdown-item" href="<?= BASE_URL ?>manage-chooseus.php?dev_id=<?= $id ?>"><i class="bi bi-hand-thumbs-up mr-2"></i>&ldquo;Why choose us&rdquo; points</a>
																<?php if ($project_count > 0): ?>
																	<a class="dropdown-item" href="<?= BASE_URL ?>manage-project.php?dev=<?= $id ?>"><i class="bi bi-buildings mr-2"></i>Its projects</a>
																<?php endif; ?>
																<div class="dropdown-divider"></div>
																<form method="post" action="<?= dev_e(dev_url()) ?>" class="dre-dev-delete" data-name="<?= dev_e($row['dev_heading']) ?>" data-projects="<?= $project_count ?>">
																	<input type="hidden" name="csrf" value="<?= dev_e($_SESSION['admin_csrf']) ?>">
																	<input type="hidden" name="dev_id" value="<?= $id ?>">
																	<button type="submit" name="dev_action" value="delete" class="dropdown-item text-danger"><i class="bi bi-trash3 mr-2"></i>Delete</button>
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
													<a href="<?= dev_e(dev_url(['page' => $f_page - 1])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
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
													<a href="<?= dev_e(dev_url(['page' => $number])) ?>" class="<?= $number === $f_page ? 'is-on' : '' ?>" <?= $number === $f_page ? 'aria-current="page"' : '' ?>><?= $number ?></a>
												<?php $previous = $number;
												endforeach; ?>
												<?php if ($f_page < $pages): ?>
													<a href="<?= dev_e(dev_url(['page' => $f_page + 1])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
												<?php endif; ?>
											</nav>
										<?php endif; ?>
									<?php endif; ?>
								</div>

								<!-- Appears when at least one developer is ticked -->
								<div class="dre-proj-bulk" id="devBulkBar" hidden>
									<strong><span id="devBulkCount">0</span> selected</strong>
									<div>
										<button type="submit" form="devBulkForm" name="push_to_app" value="1" formaction="<?= BASE_URL ?>push-app/push-developer-app.php" class="dre-btn dre-btn-main"><i class="bi bi-phone"></i> Push to app</button>
										<button type="submit" form="devBulkForm" name="dev_action" value="show" class="dre-btn" data-confirm="Show the selected developers on the website?"><i class="bi bi-eye"></i> Show on website</button>
										<button type="submit" form="devBulkForm" name="dev_action" value="hide" class="dre-btn" data-confirm="Hide the selected developers from the website?"><i class="bi bi-eye-slash"></i> Hide</button>
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
												window.alert('"' + form.getAttribute('data-name') + '" cannot be deleted: ' + projects + (projects === 1 ? ' project still uses' : ' projects still use') + ' it. Move those projects to another developer first, or hide this developer instead.');
											} else if (!window.confirm('Delete "' + form.getAttribute('data-name') + '"? Its images are removed too. A copy of the record is kept in the delete log.')) {
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
							<!--end::Developers-->

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