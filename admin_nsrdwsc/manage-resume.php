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

// CVs are stored in the "resume" folder of the website.
$cv_folder = dirname(__DIR__) . '/resume/';
$app_sorts = ['newest' => 'Newest first', 'oldest' => 'Oldest first', 'name' => 'Name A-Z'];

// Text that a person applying for a job would not type: pieces of database commands and scripts
// that attack tools put into every field of a form.
const APP_ODD_PATTERN = "sleep\\\\(|waitfor delay|pg_sleep|benchmark\\\\(|union select|select .* from|<script|sysdate\\\\(|\\\\|\\\\||xp_cmdshell|information_schema";
$odd_sql = "(CONCAT_WS(' ', a.first_name, a.email_id, a.phone, a.linkedin_url, a.pos) REGEXP '" . APP_ODD_PATTERN . "')";

// ---------------------------------------------------------------
// Filters (all optional, all from the address bar)
// ---------------------------------------------------------------
$f_pos = isset($_GET['pos']) && is_string($_GET['pos']) ? mb_substr(trim($_GET['pos']), 0, 255) : '';
$f_from = isset($_GET['from']) && is_string($_GET['from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['from']) && strtotime($_GET['from']) ? $_GET['from'] : '';
$f_to = isset($_GET['to']) && is_string($_GET['to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['to']) && strtotime($_GET['to']) ? $_GET['to'] : '';
$f_odd = ($_GET['odd'] ?? '') === '1' ? '1' : '';
$f_sort = isset($_GET['sort']) && is_string($_GET['sort']) && isset($app_sorts[$_GET['sort']]) ? $_GET['sort'] : 'newest';
$f_search = isset($_GET['q']) && is_string($_GET['q']) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$f_per_page = (int) ($_GET['per'] ?? 25);
if (!in_array($f_per_page, [25, 50, 100], true)) {
	$f_per_page = 25;
}
$f_page = max(1, (int) ($_GET['page'] ?? 1));

// Address of this page with the current filters, with some of them changed.
function app_url(array $change = [])
{
	global $f_pos, $f_from, $f_to, $f_odd, $f_sort, $f_search, $f_per_page, $f_page;
	$params = array_merge([
		'pos' => $f_pos, 'from' => $f_from, 'to' => $f_to, 'odd' => $f_odd, 'sort' => $f_sort === 'newest' ? '' : $f_sort, 'q' => $f_search,
		'per' => $f_per_page === 25 ? '' : $f_per_page, 'page' => $f_page === 1 ? '' : $f_page,
	], $change);
	$params = array_filter($params, function ($value) {
		return $value !== '' && $value !== null;
	});
	return BASE_URL . 'manage-resume.php' . ($params ? '?' . http_build_query($params) : '');
}

$where = ['1 = 1'];
$params = [];
if ($f_search !== '') {
	$like = '%' . addcslashes($f_search, '%_\\') . '%';
	$where[] = '(a.first_name LIKE ? OR a.email_id LIKE ? OR a.phone LIKE ?)';
	array_push($params, $like, $like, $like);
}
if ($f_pos !== '') {
	// Two spaces in a row count as one, the way positions are matched everywhere.
	$where[] = "REGEXP_REPLACE(TRIM(a.pos), ' +', ' ') = REGEXP_REPLACE(TRIM(?), ' +', ' ')";
	$params[] = $f_pos;
}
if ($f_from !== '') {
	$where[] = 'a.tstp >= ?';
	$params[] = $f_from . ' 00:00:00';
}
if ($f_to !== '') {
	$where[] = 'a.tstp <= ?';
	$params[] = $f_to . ' 23:59:59';
}
if ($f_odd !== '') {
	$where[] = $odd_sql;
}
$where_sql = 'WHERE ' . implode(' AND ', $where);
$order_sql = ['newest' => 'a.res_id DESC', 'oldest' => 'a.res_id ASC', 'name' => 'a.first_name ASC, a.res_id DESC'][$f_sort];

// ---------------------------------------------------------------
// Download the applications that match the filters, as a spreadsheet file
// ---------------------------------------------------------------
if (isset($_GET['download'])) {
	$all = set_query($conn, "SELECT a.first_name, a.email_id, a.phone, a.linkedin_url, a.pos, a.resume, a.tstp FROM tbl_resume AS a $where_sql ORDER BY $order_sql LIMIT 10000", $params) ?: [];
	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename="job-applications-' . date('Y-m-d') . '.csv"');
	header('X-Content-Type-Options: nosniff');
	$out = fopen('php://output', 'w');
	fwrite($out, "\xEF\xBB\xBF");
	fputcsv($out, ['Name', 'Email', 'Phone', 'LinkedIn', 'Position', 'CV', 'Applied on']);
	foreach ($all as $row) {
		$cells = [$row['first_name'], $row['email_id'], $row['phone'], $row['linkedin_url'], $row['pos'], trim((string) $row['resume']) !== '' ? BASE_URL_HOME . 'resume/' . rawurlencode($row['resume']) : '', $row['tstp']];
		foreach ($cells as $i => $cell) {
			// A cell that starts like a formula is written as plain text, so a spreadsheet does not run it.
			$cell = (string) $cell;
			$cells[$i] = preg_match('/^[=+\-@\t\r]/', $cell) ? "'" . $cell : $cell;
		}
		fputcsv($out, $cells);
	}
	fclose($out);
	$logger->log('EXPORT', 'tbl_resume', 0, null, ['applications' => count($all)], 'Job applications downloaded: ' . count($all));
	exit;
}

// ---------------------------------------------------------------
// Changes (POST only, with a token): delete applications and their CV files
// ---------------------------------------------------------------
$notice = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['app_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$done = '';
	if ($token_ok && $_POST['app_action'] === 'delete') {
		$ids = isset($_POST['app_id']) ? [(int) $_POST['app_id']] : array_map('intval', (array) ($_POST['app_ids'] ?? []));
		$ids = array_slice(array_unique(array_filter($ids)), 0, 200);
		$deleted = 0;
		foreach ($ids as $id) {
			$row = set_query($conn, "SELECT resume FROM tbl_resume WHERE res_id = ?", [$id]);
			if ($row && set_query($conn, "DELETE FROM tbl_resume WHERE res_id = ?", [$id])) {
				// The CV goes with the application, unless another application uses the same file.
				$name = basename(trim((string) $row[0]['resume']));
				if ($name !== '' && is_file($cv_folder . $name) && !set_query($conn, "SELECT res_id FROM tbl_resume WHERE resume = ? LIMIT 1", [$row[0]['resume']])) {
					@unlink($cv_folder . $name);
				}
				$deleted++;
			}
		}
		if ($deleted > 0) {
			// Only the number is written down: a deleted application leaves no personal details behind.
			$logger->log('DELETE', 'tbl_resume', 0, null, ['applications_deleted' => $deleted], 'Job applications deleted: ' . $deleted);
		}
		$done = $deleted > 0 ? 'deleted-' . $deleted : 'none';
	}
	if ($done !== '') {
		header('Location: ' . app_url(['done' => $done]));
		exit;
	}
	$notice = 'Nothing was changed. Please reload the page and try again.';
}

// ---------------------------------------------------------------
// The list
// ---------------------------------------------------------------
$counted = set_query($conn, "SELECT COUNT(*) AS n FROM tbl_resume AS a $where_sql", $params);
$total = $counted ? (int) $counted[0]['n'] : 0;
$pages = max(1, (int) ceil($total / $f_per_page));
$f_page = min($f_page, $pages);
$offset = ($f_page - 1) * $f_per_page;
$applications = set_query($conn, "SELECT a.*, $odd_sql AS looks_odd FROM tbl_resume AS a $where_sql ORDER BY $order_sql LIMIT ?, ?", array_merge($params, [$offset, $f_per_page])) ?: [];

$stat = set_query($conn, "SELECT COUNT(*) AS total, SUM(a.tstp >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS week, SUM(a.tstp >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS month, SUM($odd_sql) AS odd FROM tbl_resume AS a");
$stat = $stat ? $stat[0] : ['total' => 0, 'week' => 0, 'month' => 0, 'odd' => 0];
// Positions people applied for, most applications first (only names that read like a job title).
$position_options = set_query($conn, "SELECT REGEXP_REPLACE(TRIM(a.pos), ' +', ' ') AS pos, COUNT(*) AS n FROM tbl_resume AS a WHERE TRIM(a.pos) <> '' AND NOT $odd_sql GROUP BY 1 HAVING n >= 3 ORDER BY n DESC LIMIT 40") ?: [];

$filters_on = $f_search !== '' || $f_pos !== '' || $f_from !== '' || $f_to !== '' || $f_odd !== '';
$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : '';
if (preg_match('/^deleted-(\d+)$/', $done, $match)) {
	$notice = (int) $match[1] . ((int) $match[1] === 1 ? ' application was' : ' applications were') . ' deleted, with ' . ((int) $match[1] === 1 ? 'its CV file.' : 'their CV files.');
} elseif ($done === 'none') {
	$notice = 'Nothing was deleted.';
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
							<!--begin::Job applications-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-articles.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-simple.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-careers.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-articles dre-simple dre-careers dre-applications">

								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1>Job applications</h1>
										<p>People who applied through the Careers page, with their CVs. These are personal details: keep them only as long as they are needed.</p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= number_format((int) $stat['week']) ?></strong><span>Last 7 days</span></div>
										<div><strong><?= number_format((int) $stat['month']) ?></strong><span>Last 30 days</span></div>
										<div><strong><?= number_format((int) $stat['total']) ?></strong><span>In all</span></div>
										<div><strong><?= number_format((int) $stat['odd']) ?></strong><span>Not real applications</span></div>
									</div>
									<div class="dre-actions">
										<a class="dre-primary" href="<?= set_e(app_url(['download' => 1, 'page' => ''])) ?>"><i class="bi bi-download"></i> Download <?= $filters_on ? 'these' : 'all' ?> (CSV)</a>
										<a href="<?= BASE_URL ?>manage-position.php"><i class="bi bi-person-workspace"></i> Open positions</a>
									</div>
								</div>

								<?php if ($notice !== ''): ?>
									<div class="dre-leads-notice" role="status" id="appNotice"><i class="bi bi-info-circle"></i> <?= set_e($notice) ?></div>
								<?php endif; ?>
								<?php if ((int) $stat['odd'] > 0 && $f_odd === ''): ?>
									<div class="dre-leads-notice dre-app-odd-note" role="status"><i class="bi bi-shield-exclamation"></i> <?= number_format((int) $stat['odd']) ?> <?= (int) $stat['odd'] === 1 ? 'entry looks' : 'entries look' ?> like an automated attack on the form, not a real application. <a href="<?= set_e(app_url(['odd' => 1, 'page' => '', 'pos' => '', 'q' => '', 'from' => '', 'to' => ''])) ?>">Show them</a></div>
								<?php endif; ?>

								<form method="get" action="<?= BASE_URL ?>manage-resume.php" class="dre-glass dre-leads-filter dre-proj-filter">
									<label class="dre-leads-search">
										<span>Search</span>
										<i class="bi bi-search"></i>
										<input type="search" name="q" value="<?= set_e($f_search) ?>" placeholder="Name, email or phone" autocomplete="off">
									</label>
									<label>
										<span>Position</span>
										<select name="pos">
											<option value="">Any</option>
											<?php
											$pos_listed = false;
											foreach ($position_options as $option):
												$is_selected = $f_pos !== '' && preg_replace('/ +/', ' ', $f_pos) === (string) $option['pos'];
												$pos_listed = $pos_listed || $is_selected;
											?>
												<option value="<?= set_e($option['pos']) ?>" <?= $is_selected ? 'selected' : '' ?>><?= set_e($option['pos']) ?> (<?= number_format((int) $option['n']) ?>)</option>
											<?php endforeach; ?>
											<?php if ($f_pos !== '' && !$pos_listed): ?>
												<option value="<?= set_e($f_pos) ?>" selected><?= set_e($f_pos) ?></option>
											<?php endif; ?>
										</select>
									</label>
									<label>
										<span>From</span>
										<input type="date" name="from" value="<?= set_e($f_from) ?>">
									</label>
									<label>
										<span>To</span>
										<input type="date" name="to" value="<?= set_e($f_to) ?>">
									</label>
									<label>
										<span>Sort by</span>
										<select name="sort">
											<?php foreach ($app_sorts as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_sort === $key ? 'selected' : '' ?>><?= set_e($label) ?></option>
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
										<input type="checkbox" name="odd" value="1" <?= $f_odd !== '' ? 'checked' : '' ?>> Not real applications only
									</label>
									<div class="dre-leads-filter-buttons">
										<button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-funnel"></i> Apply</button>
										<?php if ($filters_on || $f_sort !== 'newest'): ?>
											<a class="dre-btn" href="<?= BASE_URL ?>manage-resume.php">Clear</a>
										<?php endif; ?>
									</div>
								</form>

								<form method="post" action="<?= set_e(app_url()) ?>" id="appBulkForm">
									<input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
									<input type="hidden" name="app_action" value="delete">
								</form>

								<div class="dre-glass dre-leads-list">
									<div class="dre-h">
										<h3>
											<?= number_format($total) ?> <?= $total === 1 ? 'application' : 'applications' ?>
											<small>
												<?php if ($total > 0): ?>
													Showing <?= number_format($offset + 1) ?>&ndash;<?= number_format($offset + count($applications)) ?><?= $filters_on ? ' &middot; filtered' : '' ?>
												<?php else: ?>
													Nothing matches<?= $filters_on ? ' these filters' : '' ?>
												<?php endif; ?>
											</small>
										</h3>
										<?php if ($applications): ?>
											<label class="dre-proj-all"><input type="checkbox" id="appSelectAll"> Select all on this page</label>
										<?php endif; ?>
									</div>

									<?php if (!$applications): ?>
										<div class="dre-empty">
											<i class="bi bi-file-earmark-person" style="font-size: 2rem;"></i><br>
											No applications to show.
											<?php if ($filters_on): ?>
												<a href="<?= BASE_URL ?>manage-resume.php">Show all applications</a>
											<?php endif; ?>
										</div>
									<?php else: ?>
										<div class="dre-proj-table" role="table" aria-label="Job applications">
											<div class="dre-proj-row dre-proj-head" role="row">
												<div role="columnheader"><span class="sr-only">Select</span></div>
												<div role="columnheader">Applicant</div>
												<div role="columnheader">Contact</div>
												<div role="columnheader">Position</div>
												<div role="columnheader">Applied</div>
												<div role="columnheader"><span class="sr-only">Actions</span></div>
											</div>
											<?php foreach ($applications as $row):
												$id = (int) $row['res_id'];
												$odd = (int) $row['looks_odd'] === 1;
												$name = trim((string) $row['first_name']);
												$email = trim((string) $row['email_id']);
												$phone = trim((string) $row['phone']);
												$phone_digits = preg_replace('/\D+/', '', $phone);
												$linkedin = trim((string) $row['linkedin_url']);
												$cv = basename(trim((string) $row['resume']));
												$cv_here = $cv !== '' && is_file($cv_folder . $cv);
												$applied = strtotime((string) $row['tstp']);
											?>
												<div class="dre-proj-row" role="row">
													<div class="dre-proj-pick" role="cell">
														<input type="checkbox" name="app_ids[]" value="<?= $id ?>" form="appBulkForm" class="dre-proj-box" aria-label="Select the application of <?= set_e($name) ?>">
													</div>
													<div class="dre-proj-main" role="cell">
														<span class="dre-proj-thumb dre-sim-initial" aria-hidden="true"><span><?= set_e(mb_strtoupper(mb_substr($name, 0, 1)) ?: '?') ?></span></span>
														<div>
															<span class="dre-proj-name"><?= $name !== '' ? set_e(mb_strimwidth($name, 0, 60, '...')) : 'No name' ?></span>
															<?php if ($odd): ?>
																<span class="dre-app-odd"><i class="bi bi-shield-exclamation"></i> Looks like an automated attack, not a person</span>
															<?php endif; ?>
														</div>
													</div>
													<div class="dre-app-contact" role="cell">
														<?php if ($odd): ?>
															<span><?= set_e(mb_strimwidth($email, 0, 50, '...')) ?></span>
															<span><?= set_e(mb_strimwidth($phone, 0, 40, '...')) ?></span>
														<?php else: ?>
															<?php if (filter_var($email, FILTER_VALIDATE_EMAIL)): ?>
																<a href="mailto:<?= set_e($email) ?>"><i class="bi bi-envelope"></i> <?= set_e($email) ?></a>
															<?php elseif ($email !== ''): ?>
																<span><i class="bi bi-envelope"></i> <?= set_e(mb_strimwidth($email, 0, 60, '...')) ?></span>
															<?php endif; ?>
															<?php if (strlen($phone_digits) >= 7): ?>
																<a href="tel:<?= set_e(($phone !== '' && $phone[0] === '+' ? '+' : '') . $phone_digits) ?>"><i class="bi bi-telephone"></i> <?= set_e($phone) ?></a>
															<?php elseif ($phone !== ''): ?>
																<span><i class="bi bi-telephone"></i> <?= set_e(mb_strimwidth($phone, 0, 40, '...')) ?></span>
															<?php endif; ?>
															<?php if (preg_match('~^https?://[^\s<>"\']+$~i', $linkedin)): ?>
																<a href="<?= set_e($linkedin) ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-linkedin"></i> LinkedIn profile</a>
															<?php endif; ?>
														<?php endif; ?>
													</div>
													<div class="dre-proj-facts" role="cell">
														<?php if (trim((string) $row['pos']) !== '' && !$odd): ?>
															<span class="dre-pill"><?= set_e(mb_strimwidth(trim((string) $row['pos']), 0, 50, '...')) ?></span>
														<?php else: ?>
															<span><?= $odd ? set_e(mb_strimwidth((string) $row['pos'], 0, 40, '...')) : 'No position chosen' ?></span>
														<?php endif; ?>
													</div>
													<div class="dre-proj-facts" role="cell">
														<strong><?= $applied ? date('d M Y', $applied) : 'No date' ?></strong>
														<span><?= $applied ? date('H:i', $applied) : '' ?></span>
													</div>
													<div class="dre-proj-actions" role="cell">
														<?php if ($cv_here): ?>
															<a class="dre-icon-btn" href="<?= set_e(BASE_URL_HOME . 'resume/' . rawurlencode($cv)) ?>" target="_blank" rel="noopener" title="Open the CV" aria-label="Open the CV of <?= set_e($name) ?>"><i class="bi bi-file-earmark-text"></i></a>
														<?php else: ?>
															<span class="dre-icon-btn dre-app-nofile" title="<?= $cv !== '' ? 'The CV file is not on this server' : 'No CV was sent' ?>" aria-label="No CV file"><i class="bi bi-file-earmark-x"></i></span>
														<?php endif; ?>
														<form method="post" action="<?= set_e(app_url()) ?>" class="dre-app-delete" data-name="<?= set_e($name) ?>">
															<input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
															<input type="hidden" name="app_action" value="delete">
															<input type="hidden" name="app_id" value="<?= $id ?>">
															<button type="submit" class="dre-icon-btn" title="Delete" aria-label="Delete the application of <?= set_e($name) ?>"><i class="bi bi-trash3"></i></button>
														</form>
													</div>
												</div>
											<?php endforeach; ?>
										</div>

										<?php if ($pages > 1): ?>
											<nav class="dre-leads-pages" aria-label="Pages">
												<?php if ($f_page > 1): ?>
													<a href="<?= set_e(app_url(['page' => $f_page - 1])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
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
													<a href="<?= set_e(app_url(['page' => $number])) ?>" class="<?= $number === $f_page ? 'is-on' : '' ?>" <?= $number === $f_page ? 'aria-current="page"' : '' ?>><?= $number ?></a>
												<?php $previous = $number;
												endforeach; ?>
												<?php if ($f_page < $pages): ?>
													<a href="<?= set_e(app_url(['page' => $f_page + 1])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
												<?php endif; ?>
											</nav>
										<?php endif; ?>
									<?php endif; ?>
								</div>

								<div class="dre-proj-bulk" id="appBulkBar" hidden>
									<strong><span id="appBulkCount">0</span> selected</strong>
									<div>
										<button type="submit" form="appBulkForm" class="dre-btn dre-btn-main"><i class="bi bi-trash3"></i> Delete with their CVs</button>
										<button type="button" class="dre-btn" id="appBulkClear">Clear</button>
									</div>
								</div>
							</div>

							<script>
								// Selection bar and confirmations for job applications.
								(function () {
									var boxes = Array.prototype.slice.call(document.querySelectorAll('.dre-proj-box'));
									var all = document.getElementById('appSelectAll');
									var bar = document.getElementById('appBulkBar');
									var count = document.getElementById('appBulkCount');
									function refresh() {
										var picked = boxes.filter(function (box) { return box.checked; }).length;
										count.textContent = picked;
										bar.hidden = picked === 0;
										if (all) {
											all.checked = picked > 0 && picked === boxes.length;
											all.indeterminate = picked > 0 && picked < boxes.length;
										}
										return picked;
									}
									boxes.forEach(function (box) { box.addEventListener('change', refresh); });
									if (all) {
										all.addEventListener('change', function () {
											boxes.forEach(function (box) { box.checked = all.checked; });
											refresh();
										});
									}
									document.getElementById('appBulkClear').addEventListener('click', function () {
										boxes.forEach(function (box) { box.checked = false; });
										refresh();
									});
									document.getElementById('appBulkForm').addEventListener('submit', function (event) {
										var picked = refresh();
										if (!window.confirm('Delete ' + picked + (picked === 1 ? ' application and its CV file' : ' applications and their CV files') + '? This cannot be undone.')) {
											event.preventDefault();
										}
									});
									document.querySelectorAll('.dre-app-delete').forEach(function (form) {
										form.addEventListener('submit', function (event) {
											if (!window.confirm('Delete the application of "' + form.getAttribute('data-name') + '" and its CV file? This cannot be undone.')) {
												event.preventDefault();
											}
										});
									});
									refresh();
									var notice = document.getElementById('appNotice');
									if (notice) {
										setTimeout(function () { notice.style.display = 'none'; }, 8000);
									}
								})();
							</script>
							<!--end::Job applications-->

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