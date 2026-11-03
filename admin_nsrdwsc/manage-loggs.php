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

// Logs are for super admins only.
$is_super_admin = (int) ($_SESSION['login_type'] ?? 0) === 1;

// The three log pages share this layout. This copy shows one of them.
$log_kind = 'activity';
$log_kinds = [
	'activity' => [
		'title' => 'Activity log', 'blurb' => 'Who added, changed or deleted what in the admin panel.', 'file' => 'manage-loggs.php', 'icon' => 'bi-activity',
		'table' => 'tbl_activity_logs', 'id' => 'log_id', 'time' => 'log_time', 'who' => 'username', 'who_label' => 'User',
		'type' => 'action_type', 'types' => ['INSERT' => 'Added', 'UPDATE' => 'Changed', 'DELETE' => 'Deleted'], 'type_label' => 'Action',
		'about' => 'table_name', 'search' => ['action_description', 'record_identifier', 'table_name', 'username'],
		'one' => 'entry', 'many' => 'entries', 'can_clear' => true,
	],
	'login' => [
		'title' => 'Login log', 'blurb' => 'Every sign-in to the admin panel, and every attempt that failed.', 'file' => 'manage-login-logs.php', 'icon' => 'bi-door-open',
		'table' => 'tbl_login_logs', 'id' => 'login_log_id', 'time' => 'login_time', 'who' => 'user_email', 'who_label' => 'Email',
		'type' => 'login_status', 'types' => ['SUCCESS' => 'Signed in', 'FAILED' => 'Failed'], 'type_label' => 'Result',
		'about' => '', 'search' => ['user_email', 'user_ip', 'failure_reason'],
		'one' => 'sign-in', 'many' => 'sign-ins', 'can_clear' => true,
	],
	'deleted' => [
		'title' => 'Deleted data log', 'blurb' => 'A copy of every record that was deleted, kept for reference.', 'file' => 'manage-delete-logs.php', 'icon' => 'bi-trash3',
		'table' => 'tbl_deleted_records', 'id' => 'delete_id', 'time' => 'deleted_at', 'who' => 'deleted_by_name', 'who_label' => 'Deleted by',
		'type' => '', 'types' => [], 'type_label' => '',
		'about' => 'original_table', 'search' => ['original_table', 'deleted_by_name', 'deleted_data'],
		'one' => 'record', 'many' => 'records', 'can_clear' => false,
	],
];
$log = $log_kinds[$log_kind];
$t = $log['table'];

// "tbl_proj_gallery" -> "Proj gallery"
function log_table_name($table)
{
	return ucfirst(str_replace('_', ' ', preg_replace('/^tbl_/', '', (string) $table)));
}

// A value from a saved record, as short plain text.
function log_value($value, $length = 140)
{
	if (is_array($value)) {
		$value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}
	$text = trim(preg_replace('/\s+/', ' ', str_replace("\xC2\xA0", ' ', html_entity_decode(strip_tags((string) $value), ENT_QUOTES, 'UTF-8'))));
	return $text === '' ? '(empty)' : mb_strimwidth($text, 0, $length, '...');
}

// "Chrome on Windows" from a browser's own description of itself.
function log_browser($agent)
{
	$agent = (string) $agent;
	if ($agent === '') {
		return 'Unknown browser';
	}
	$browser = 'Browser';
	foreach (['Edg/' => 'Edge', 'OPR/' => 'Opera', 'Chrome/' => 'Chrome', 'Firefox/' => 'Firefox', 'Safari/' => 'Safari'] as $needle => $name) {
		if (strpos($agent, $needle) !== false) {
			$browser = $name;
			break;
		}
	}
	$system = '';
	foreach (['iPhone' => 'iPhone', 'iPad' => 'iPad', 'Android' => 'Android', 'Windows' => 'Windows', 'Mac OS X' => 'Mac', 'Linux' => 'Linux'] as $needle => $name) {
		if (strpos($agent, $needle) !== false) {
			$system = $name;
			break;
		}
	}
	return $browser . ($system !== '' ? ' on ' . $system : '');
}

// ---------------------------------------------------------------
// Filters (all optional, all from the address bar)
// ---------------------------------------------------------------
$f_who = isset($_GET['who']) && is_string($_GET['who']) ? mb_substr(trim($_GET['who']), 0, 255) : '';
$f_type = isset($_GET['type']) && is_string($_GET['type']) && isset($log['types'][$_GET['type']]) ? $_GET['type'] : '';
$f_about = $log['about'] !== '' && isset($_GET['about']) && is_string($_GET['about']) && preg_match('/^[A-Za-z0-9_]{1,64}$/', $_GET['about']) ? $_GET['about'] : '';
$f_from = isset($_GET['from']) && is_string($_GET['from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['from']) && strtotime($_GET['from']) ? $_GET['from'] : '';
$f_to = isset($_GET['to']) && is_string($_GET['to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['to']) && strtotime($_GET['to']) ? $_GET['to'] : '';
$f_search = isset($_GET['q']) && is_string($_GET['q']) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$f_per_page = (int) ($_GET['per'] ?? 50);
if (!in_array($f_per_page, [50, 100, 200], true)) {
	$f_per_page = 50;
}
$f_page = max(1, (int) ($_GET['page'] ?? 1));

// Address of this page with the current filters, with some of them changed.
function log_url(array $change = [])
{
	global $log, $f_who, $f_type, $f_about, $f_from, $f_to, $f_search, $f_per_page, $f_page;
	$params = array_merge([
		'who' => $f_who, 'type' => $f_type, 'about' => $f_about, 'from' => $f_from, 'to' => $f_to, 'q' => $f_search,
		'per' => $f_per_page === 50 ? '' : $f_per_page, 'page' => $f_page === 1 ? '' : $f_page,
	], $change);
	$params = array_filter($params, function ($value) {
		return $value !== '' && $value !== null;
	});
	return BASE_URL . $log['file'] . ($params ? '?' . http_build_query($params) : '');
}

// ---------------------------------------------------------------
// Changes (POST only, with a token): remove entries
// ---------------------------------------------------------------
$notice = '';
if ($is_super_admin && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['log_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$action = is_string($_POST['log_action']) ? $_POST['log_action'] : '';
	$done = '';

	if ($token_ok && $action === 'remove') {
		$ids = array_slice(array_unique(array_filter(array_map('intval', (array) ($_POST['log_ids'] ?? [])))), 0, 500);
		$removed = 0;
		foreach (array_chunk($ids, 100) as $chunk) {
			if (set_query($conn, "DELETE FROM $t WHERE {$log['id']} IN (" . implode(', ', array_fill(0, count($chunk), '?')) . ")", $chunk)) {
				$removed += mysqli_affected_rows($conn);
			}
		}
		$done = $removed > 0 ? 'removed-' . $removed : 'none';
	} elseif ($token_ok && $action === 'clear' && $log['can_clear']) {
		// Only whole months, and never the last 30 days.
		$days = (int) ($_POST['older_than'] ?? 0);
		if (in_array($days, [30, 90, 180, 365], true) && set_query($conn, "DELETE FROM $t WHERE {$log['time']} < DATE_SUB(NOW(), INTERVAL ? DAY)", [$days])) {
			$removed = mysqli_affected_rows($conn);
			$done = $removed > 0 ? 'removed-' . $removed : 'none';
		}
	}

	if ($done !== '') {
		if ($done !== 'none') {
			// Removing entries from a log is itself written down.
			$logger->log('DELETE', $t, 0, null, ['entries_removed' => (int) substr($done, 8), 'how' => $action === 'clear' ? 'older than ' . $days . ' days' : 'selected'], $log['title'] . ': ' . (int) substr($done, 8) . ' ' . ((int) substr($done, 8) === 1 ? $log['one'] : $log['many']) . ' removed');
		}
		header('Location: ' . log_url(['done' => $done, 'page' => '']));
		exit;
	}
	$notice = 'Nothing was changed. Please reload the page and try again.';
}

// ---------------------------------------------------------------
// The list
// ---------------------------------------------------------------
$rows = [];
$total = 0;
$pages = 1;
$offset = 0;
$stats = [];
$who_options = [];
$about_options = [];
if ($is_super_admin) {
	$where = ['1 = 1'];
	$params = [];
	if ($f_search !== '') {
		$like = '%' . addcslashes($f_search, '%_\\') . '%';
		$parts = [];
		foreach ($log['search'] as $column) {
			$parts[] = "a.$column LIKE ?";
			$params[] = $like;
		}
		$where[] = '(' . implode(' OR ', $parts) . ')';
	}
	if ($f_who !== '') {
		$where[] = "a.{$log['who']} = ?";
		$params[] = $f_who;
	}
	if ($f_type !== '') {
		$where[] = "a.{$log['type']} = ?";
		$params[] = $f_type;
	}
	if ($f_about !== '') {
		$where[] = "a.{$log['about']} = ?";
		$params[] = $f_about;
	}
	if ($f_from !== '') {
		$where[] = "a.{$log['time']} >= ?";
		$params[] = $f_from . ' 00:00:00';
	}
	if ($f_to !== '') {
		$where[] = "a.{$log['time']} <= ?";
		$params[] = $f_to . ' 23:59:59';
	}
	$where_sql = 'WHERE ' . implode(' AND ', $where);

	$counted = set_query($conn, "SELECT COUNT(*) AS n FROM $t AS a $where_sql", $params);
	$total = $counted ? (int) $counted[0]['n'] : 0;
	$pages = max(1, (int) ceil($total / $f_per_page));
	$f_page = min($f_page, $pages);
	$offset = ($f_page - 1) * $f_per_page;
	$rows = set_query($conn, "SELECT a.* FROM $t AS a $where_sql ORDER BY a.{$log['time']} DESC, a.{$log['id']} DESC LIMIT ?, ?", array_merge($params, [$offset, $f_per_page])) ?: [];

	$who_options = array_column(set_query($conn, "SELECT DISTINCT {$log['who']} AS w FROM $t WHERE {$log['who']} IS NOT NULL AND {$log['who']} <> '' ORDER BY w LIMIT 200") ?: [], 'w');
	if ($log['about'] !== '') {
		$about_options = array_column(set_query($conn, "SELECT DISTINCT {$log['about']} AS w FROM $t WHERE {$log['about']} IS NOT NULL AND {$log['about']} <> '' ORDER BY w LIMIT 200") ?: [], 'w');
	}

	// Numbers at the top: [number, label]
	if ($log_kind === 'activity') {
		$s = set_query($conn, "SELECT COUNT(*) AS total, SUM(log_time >= CURDATE()) AS today, SUM(log_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS week, SUM(action_type = 'DELETE') AS deletes FROM tbl_activity_logs");
		$s = $s ? $s[0] : [];
		$stats = [[(int) ($s['today'] ?? 0), 'Today'], [(int) ($s['week'] ?? 0), 'Last 7 days'], [(int) ($s['deletes'] ?? 0), 'Deletions'], [(int) ($s['total'] ?? 0), 'In all']];
	} elseif ($log_kind === 'login') {
		$s = set_query($conn, "SELECT COUNT(*) AS total, SUM(login_status = 'SUCCESS' AND login_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)) AS week, SUM(login_status = 'FAILED' AND login_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR)) AS failed_day, SUM(login_status = 'FAILED') AS failed FROM tbl_login_logs");
		$s = $s ? $s[0] : [];
		$stats = [[(int) ($s['week'] ?? 0), 'Sign-ins in 7 days'], [(int) ($s['failed_day'] ?? 0), 'Failed in 24 hours'], [(int) ($s['failed'] ?? 0), 'Failed in all'], [(int) ($s['total'] ?? 0), 'In all']];
	} else {
		$s = set_query($conn, "SELECT COUNT(*) AS total, SUM(deleted_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS month, COUNT(DISTINCT original_table) AS tables FROM tbl_deleted_records");
		$s = $s ? $s[0] : [];
		$stats = [[(int) ($s['total'] ?? 0), 'Kept copies'], [(int) ($s['month'] ?? 0), 'Deleted in 30 days'], [(int) ($s['tables'] ?? 0), 'Kinds of record']];
	}
}

$filters_on = $f_search !== '' || $f_who !== '' || $f_type !== '' || $f_about !== '' || $f_from !== '' || $f_to !== '';

$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : '';
if (preg_match('/^removed-(\d+)$/', $done, $match)) {
	$notice = (int) $match[1] . ' ' . ((int) $match[1] === 1 ? $log['one'] . ' was' : $log['many'] . ' were') . ' removed from the log.';
} elseif ($done === 'none') {
	$notice = 'Nothing was removed.';
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
							<!--begin::Log-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-logs.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-logs">

								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1><?= set_e($log['title']) ?></h1>
										<p><?= set_e($log['blurb']) ?></p>
									</div>
									<?php if ($is_super_admin): ?>
										<div class="dre-leads-stats">
											<?php foreach ($stats as $stat): ?>
												<div><strong><?= number_format($stat[0]) ?></strong><span><?= set_e($stat[1]) ?></span></div>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
								</div>

								<!-- The three logs -->
								<nav class="dre-leads-tabs" aria-label="Logs">
									<?php foreach ($log_kinds as $key => $other): ?>
										<a href="<?= BASE_URL . $other['file'] ?>" class="<?= $key === $log_kind ? 'is-on' : '' ?>" <?= $key === $log_kind ? 'aria-current="page"' : '' ?>><i class="bi <?= set_e($other['icon']) ?>"></i> <?= set_e($other['title']) ?></a>
									<?php endforeach; ?>
								</nav>

								<?php if (!$is_super_admin): ?>
									<div class="dre-leads-notice" role="alert"><i class="bi bi-shield-lock"></i> Only a super admin can see the logs.</div>
								<?php else: ?>

								<?php if ($notice !== ''): ?>
									<div class="dre-leads-notice" role="status" id="logNotice"><i class="bi bi-info-circle"></i> <?= set_e($notice) ?></div>
								<?php endif; ?>

								<!-- Search and filters -->
								<form method="get" action="<?= BASE_URL . $log['file'] ?>" class="dre-glass dre-leads-filter dre-proj-filter">
									<label class="dre-leads-search">
										<span>Search</span>
										<i class="bi bi-search"></i>
										<input type="search" name="q" value="<?= set_e($f_search) ?>" placeholder="<?= $log_kind === 'login' ? 'Email, IP address or reason' : ($log_kind === 'deleted' ? 'Kind of record, name or text in the record' : 'What was done, name of the record or user') ?>" autocomplete="off">
									</label>
									<label>
										<span><?= set_e($log['who_label']) ?></span>
										<select name="who">
											<option value="">Anyone</option>
											<?php foreach ($who_options as $option): ?>
												<option value="<?= set_e($option) ?>" <?= $f_who === (string) $option ? 'selected' : '' ?>><?= set_e($option) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<?php if ($log['types']): ?>
										<label>
											<span><?= set_e($log['type_label']) ?></span>
											<select name="type">
												<option value="">Any</option>
												<?php foreach ($log['types'] as $key => $label): ?>
													<option value="<?= set_e($key) ?>" <?= $f_type === $key ? 'selected' : '' ?>><?= set_e($label) ?></option>
												<?php endforeach; ?>
											</select>
										</label>
									<?php endif; ?>
									<?php if ($log['about'] !== ''): ?>
										<label>
											<span>Kind of record</span>
											<select name="about">
												<option value="">Any</option>
												<?php foreach ($about_options as $option): ?>
													<option value="<?= set_e($option) ?>" <?= $f_about === (string) $option ? 'selected' : '' ?>><?= set_e(log_table_name($option)) ?></option>
												<?php endforeach; ?>
											</select>
										</label>
									<?php endif; ?>
									<label>
										<span>From</span>
										<input type="date" name="from" value="<?= set_e($f_from) ?>">
									</label>
									<label>
										<span>To</span>
										<input type="date" name="to" value="<?= set_e($f_to) ?>">
									</label>
									<label>
										<span>Per page</span>
										<select name="per">
											<?php foreach ([50, 100, 200] as $option): ?>
												<option value="<?= $option ?>" <?= $f_per_page === $option ? 'selected' : '' ?>><?= $option ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<div class="dre-leads-filter-buttons">
										<button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-funnel"></i> Apply</button>
										<?php if ($filters_on): ?>
											<a class="dre-btn" href="<?= BASE_URL . $log['file'] ?>">Clear</a>
										<?php endif; ?>
									</div>
								</form>

								<!-- The form the ticked entries belong to -->
								<form method="post" action="<?= set_e(log_url()) ?>" id="logBulkForm">
									<input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
									<input type="hidden" name="log_action" value="remove">
								</form>

								<div class="dre-glass dre-leads-list">
									<div class="dre-h">
										<h3>
											<?= number_format($total) ?> <?= set_e($total === 1 ? $log['one'] : $log['many']) ?>
											<small>
												<?php if ($total > 0): ?>
													Showing <?= number_format($offset + 1) ?>&ndash;<?= number_format($offset + count($rows)) ?><?= $filters_on ? ' &middot; filtered' : '' ?>
												<?php else: ?>
													Nothing matches<?= $filters_on ? ' these filters' : '' ?>
												<?php endif; ?>
											</small>
										</h3>
										<?php if ($rows): ?>
											<label class="dre-proj-all"><input type="checkbox" id="logSelectAll"> Select all on this page</label>
										<?php endif; ?>
									</div>

									<?php if (!$rows): ?>
										<div class="dre-empty">
											<i class="bi <?= set_e($log['icon']) ?>" style="font-size: 2rem;"></i><br>
											Nothing to show.
											<?php if ($filters_on): ?>
												<a href="<?= BASE_URL . $log['file'] ?>">Show everything</a>
											<?php endif; ?>
										</div>
									<?php else: ?>
										<div class="dre-log-list">
											<?php
											$last_day = '';
											foreach ($rows as $row):
												$id = (int) $row[$log['id']];
												$time = strtotime((string) $row[$log['time']]);
												$day = $time ? date('Y-m-d', $time) : '';
												if ($day !== $last_day):
													$last_day = $day;
											?>
												<div class="dre-log-day"><?= $time ? ($day === date('Y-m-d') ? 'Today' : ($day === date('Y-m-d', strtotime('-1 day')) ? 'Yesterday' : date('l, d F Y', $time))) : 'No date' ?></div>
											<?php endif; ?>

											<?php if ($log_kind === 'activity'):
												$changes = json_decode((string) $row['changed_fields'], true);
												$changes = is_array($changes) ? $changes : [];
												$tone = ['INSERT' => 'good', 'UPDATE' => 'plain', 'DELETE' => 'bad'][$row['action_type']] ?? 'plain';
												$name = trim((string) $row['record_identifier']);
											?>
												<div class="dre-log-row">
													<input type="checkbox" name="log_ids[]" value="<?= $id ?>" form="logBulkForm" class="dre-proj-box" aria-label="Select entry <?= $id ?>">
													<span class="dre-log-time"><?= $time ? date('H:i', $time) : '' ?></span>
													<span class="dre-log-dot dre-log-<?= $tone ?>" aria-hidden="true"><i class="bi <?= ['INSERT' => 'bi-plus-lg', 'UPDATE' => 'bi-pencil', 'DELETE' => 'bi-trash3'][$row['action_type']] ?? 'bi-dot' ?>"></i></span>
													<div class="dre-log-body">
														<strong><?= set_e(trim((string) $row['username']) !== '' ? $row['username'] : 'Someone') ?></strong>
														<?= set_e(strtolower($log['types'][$row['action_type']] ?? 'changed')) ?>
														<?= $name !== '' ? '&ldquo;' . set_e(mb_strimwidth($name, 0, 70, '...')) . '&rdquo;' : 'record ' . (int) $row['record_id'] ?>
														<span class="dre-pill"><?= set_e(log_table_name($row['table_name'])) ?></span>
														<?php if (trim((string) $row['action_description']) !== ''): ?>
															<small><?= set_e(mb_strimwidth((string) $row['action_description'], 0, 140, '...')) ?></small>
														<?php endif; ?>
														<details>
															<summary><?= $changes ? count($changes) . (count($changes) === 1 ? ' detail changed' : ' details changed') : 'More' ?></summary>
															<?php if ($changes): ?>
																<dl class="dre-log-changes">
																	<?php foreach (array_slice($changes, 0, 15, true) as $field => $change): ?>
																		<dt><?= set_e($field) ?></dt>
																		<dd><del><?= set_e(log_value(is_array($change) ? ($change['old'] ?? '') : '')) ?></del> <i class="bi bi-arrow-right"></i> <ins><?= set_e(log_value(is_array($change) ? ($change['new'] ?? '') : $change)) ?></ins></dd>
																	<?php endforeach; ?>
																</dl>
																<?php if (count($changes) > 15): ?><small>and <?= count($changes) - 15 ?> more.</small><?php endif; ?>
															<?php endif; ?>
															<small>Record number <?= (int) $row['record_id'] ?> &middot; from <?= set_e($row['user_ip'] ?: 'an unknown address') ?> &middot; <?= set_e(log_browser($row['user_agent'])) ?></small>
														</details>
													</div>
												</div>

											<?php elseif ($log_kind === 'login'):
												$ok = $row['login_status'] === 'SUCCESS';
												$minutes = $row['session_duration'] !== null ? (int) round((int) $row['session_duration'] / 60) : null;
											?>
												<div class="dre-log-row">
													<input type="checkbox" name="log_ids[]" value="<?= $id ?>" form="logBulkForm" class="dre-proj-box" aria-label="Select sign-in <?= $id ?>">
													<span class="dre-log-time"><?= $time ? date('H:i', $time) : '' ?></span>
													<span class="dre-log-dot dre-log-<?= $ok ? 'good' : 'bad' ?>" aria-hidden="true"><i class="bi <?= $ok ? 'bi-check-lg' : 'bi-x-lg' ?>"></i></span>
													<div class="dre-log-body">
														<strong><?= set_e(trim((string) $row['user_email']) !== '' ? $row['user_email'] : 'No email given') ?></strong>
														<?= $ok ? 'signed in' : 'could not sign in' ?>
														<?php if (!$ok && trim((string) $row['failure_reason']) !== ''): ?>
															<span class="dre-pill dre-pill-bad"><?= set_e($row['failure_reason']) ?></span>
														<?php endif; ?>
														<small>
															From <?= set_e($row['user_ip'] ?: 'an unknown address') ?> &middot; <?= set_e(log_browser($row['user_agent'])) ?>
															<?php if ($ok): ?>
																&middot; <?= $row['logout_time'] ? 'signed out ' . date('H:i', strtotime($row['logout_time'])) . ($minutes !== null ? ' after ' . ($minutes < 1 ? 'less than a minute' : $minutes . ($minutes === 1 ? ' minute' : ' minutes')) : '') : 'no sign-out recorded' ?>
															<?php endif; ?>
														</small>
													</div>
												</div>

											<?php else:
												$data = json_decode((string) $row['deleted_data'], true);
												$data = is_array($data) ? $data : [];
												// The first value that reads like a name: "..._name", "..._heading" or "..._title".
												$name = '';
												foreach ($data as $key => $value) {
													if (is_scalar($value) && preg_match('/(name|heading|title)$/', (string) $key) && trim((string) $value) !== '') {
														$name = log_value($value, 70);
														break;
													}
												}
											?>
												<div class="dre-log-row">
													<input type="checkbox" name="log_ids[]" value="<?= $id ?>" form="logBulkForm" class="dre-proj-box" aria-label="Select record <?= $id ?>">
													<span class="dre-log-time"><?= $time ? date('H:i', $time) : '' ?></span>
													<span class="dre-log-dot dre-log-bad" aria-hidden="true"><i class="bi bi-trash3"></i></span>
													<div class="dre-log-body">
														<strong><?= set_e(trim((string) $row['deleted_by_name']) !== '' ? $row['deleted_by_name'] : 'Someone') ?></strong>
														deleted <?= $name !== '' ? '&ldquo;' . set_e($name) . '&rdquo;' : 'record ' . (int) $row['record_id'] ?>
														<span class="dre-pill"><?= set_e(log_table_name($row['original_table'])) ?></span>
														<details>
															<summary>What was in it (<?= count($data) ?> <?= count($data) === 1 ? 'detail' : 'details' ?>)</summary>
															<dl class="dre-log-changes">
																<?php foreach ($data as $field => $value):
																	if (log_value($value) === '(empty)') {
																		continue;
																	}
																?>
																	<dt><?= set_e($field) ?></dt>
																	<dd><?= set_e(log_value($value, 220)) ?></dd>
																<?php endforeach; ?>
															</dl>
															<small>Record number <?= (int) $row['record_id'] ?> &middot; from <?= set_e($row['ip_address'] ?: 'an unknown address') ?>. Empty details are left out. Pictures and files of a deleted record are not kept.</small>
														</details>
													</div>
												</div>
											<?php endif; ?>
											<?php endforeach; ?>
										</div>

										<?php if ($pages > 1): ?>
											<nav class="dre-leads-pages" aria-label="Pages">
												<?php if ($f_page > 1): ?>
													<a href="<?= set_e(log_url(['page' => $f_page - 1])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
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
													<a href="<?= set_e(log_url(['page' => $number])) ?>" class="<?= $number === $f_page ? 'is-on' : '' ?>" <?= $number === $f_page ? 'aria-current="page"' : '' ?>><?= $number ?></a>
												<?php $previous = $number;
												endforeach; ?>
												<?php if ($f_page < $pages): ?>
													<a href="<?= set_e(log_url(['page' => $f_page + 1])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
												<?php endif; ?>
											</nav>
										<?php endif; ?>
									<?php endif; ?>
								</div>

								<?php if ($log['can_clear']): ?>
									<!-- Tidy up: remove old entries -->
									<form method="post" action="<?= BASE_URL . $log['file'] ?>" class="dre-glass dre-log-clear" id="logClearForm">
										<input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
										<input type="hidden" name="log_action" value="clear">
										<div>
											<strong><i class="bi bi-archive"></i> Tidy up</strong>
											<span>Remove old <?= set_e($log['many']) ?> to keep the log short. The last 30 days always stay.</span>
										</div>
										<label>
											<span class="sr-only">Remove entries older than</span>
											<select name="older_than">
												<option value="365">Older than a year</option>
												<option value="180">Older than 6 months</option>
												<option value="90">Older than 3 months</option>
												<option value="30">Older than 30 days</option>
											</select>
										</label>
										<button type="submit" class="dre-btn"><i class="bi bi-trash3"></i> Remove</button>
									</form>
								<?php endif; ?>

								<!-- Appears when at least one entry is ticked -->
								<div class="dre-proj-bulk" id="logBulkBar" hidden>
									<strong><span id="logBulkCount">0</span> selected</strong>
									<div>
										<button type="submit" form="logBulkForm" class="dre-btn dre-btn-main" id="logBulkRemove"><i class="bi bi-trash3"></i> Remove from the log</button>
										<button type="button" class="dre-btn" id="logBulkClear">Clear</button>
									</div>
								</div>
								<?php endif; ?>
							</div>

							<script>
								// Selection bar and confirmations for the log.
								(function () {
									var bar = document.getElementById('logBulkBar');
									if (!bar) {
										return;
									}
									var many = <?= json_encode($log['many']) ?>;
									var boxes = Array.prototype.slice.call(document.querySelectorAll('.dre-log-list .dre-proj-box'));
									var all = document.getElementById('logSelectAll');
									var count = document.getElementById('logBulkCount');
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
									document.getElementById('logBulkClear').addEventListener('click', function () {
										boxes.forEach(function (box) { box.checked = false; });
										refresh();
									});
									document.getElementById('logBulkForm').addEventListener('submit', function (event) {
										if (!window.confirm('Remove the selected ' + many + ' from the log? This cannot be undone.')) {
											event.preventDefault();
										}
									});
									var clearForm = document.getElementById('logClearForm');
									if (clearForm) {
										clearForm.addEventListener('submit', function (event) {
											var choice = clearForm.querySelector('select');
											if (!window.confirm('Remove all ' + many + ' ' + choice.options[choice.selectedIndex].text.toLowerCase() + '? This cannot be undone.')) {
												event.preventDefault();
											}
										});
									}
									refresh();
									var notice = document.getElementById('logNotice');
									if (notice) {
										setTimeout(function () { notice.style.display = 'none'; }, 8000);
									}
								})();
							</script>
							<!--end::Log-->

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