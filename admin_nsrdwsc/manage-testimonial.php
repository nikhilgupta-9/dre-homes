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

// Testimonials and awards share this list layout. This copy works on one of them: its table,
// its columns, and where it shows on the website.
$kind = [
	'one' => 'testimonial', 'many' => 'testimonials', 'title' => 'Testimonials',
	'blurb' => 'What clients say, shown on the Client Testimonials page with the newest date first.',
	'table' => 'tbl_testimonial', 'id' => 'test_id', 'heading' => 'test_client_name', 'summary' => 'test_descp',
	'date' => 'test_posted_on', 'thumb' => '', 'folder' => '', 'flag' => '', 'flag_label' => '', 'rating' => 'test_rating',
	'search' => ['test_client_name', 'test_descp'],
	'missing' => ['date' => ['Date', 'a.test_posted_on IS NULL'], 'rating' => ['Rating', "TRIM(a.test_rating) = ''"], 'text' => ['Text', "TRIM(a.test_descp) = ''"]],
	'list_file' => 'manage-testimonial.php', 'add_file' => 'add-testimonial.php', 'edit_file' => 'edit-testimonial.php',
	'export_file' => 'export-testimonial.php', 'content_file' => 'edit-testimonial-content.php', 'public_path' => 'clients-testimonials',
	'icon' => 'bi-chat-quote',
];
$t = $kind['table'];
$id_col = $kind['id'];
// The column the website orders by: the date when there is one, otherwise the order of adding.
$when = $kind['date'] !== '' ? 'a.' . $kind['date'] : 'a.tstp';

$sim_sorts = ['newest' => 'Newest first', 'oldest' => 'Oldest first', 'name' => 'Name A-Z', 'changed' => 'Last changed first'];

// ---------------------------------------------------------------
// Filters (all optional, all from the address bar)
// ---------------------------------------------------------------
$f_status = in_array($_GET['status'] ?? '', ['active', 'inactive'], true) ? $_GET['status'] : '';
$f_year = isset($_GET['year']) && preg_match('/^(19|20)\d\d$/', (string) $_GET['year']) ? (int) $_GET['year'] : 0;
$f_flag = $kind['flag'] !== '' && ($_GET['flag'] ?? '') === '1' ? '1' : '';
$f_missing = isset($_GET['missing']) && is_string($_GET['missing']) && isset($kind['missing'][$_GET['missing']]) ? $_GET['missing'] : '';
$f_sort = isset($_GET['sort']) && is_string($_GET['sort']) && isset($sim_sorts[$_GET['sort']]) ? $_GET['sort'] : 'newest';
$f_search = isset($_GET['q']) && is_string($_GET['q']) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$f_per_page = (int) ($_GET['per'] ?? 25);
if (!in_array($f_per_page, [25, 50, 100], true)) {
	$f_per_page = 25;
}
$f_page = max(1, (int) ($_GET['page'] ?? 1));

// Address of this page with the current filters, with some of them changed.
function sim_url(array $change = [])
{
	global $kind, $f_status, $f_year, $f_flag, $f_missing, $f_sort, $f_search, $f_per_page, $f_page;
	$params = array_merge([
		'status' => $f_status, 'year' => $f_year ?: '', 'flag' => $f_flag, 'missing' => $f_missing,
		'sort' => $f_sort === 'newest' ? '' : $f_sort, 'q' => $f_search,
		'per' => $f_per_page === 25 ? '' : $f_per_page, 'page' => $f_page === 1 ? '' : $f_page,
	], $change);
	$params = array_filter($params, function ($value) {
		return $value !== '' && $value !== null;
	});
	return BASE_URL . $kind['list_file'] . ($params ? '?' . http_build_query($params) : '');
}

function sim_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function sim_query($conn, $sql, array $params = [])
{
	$stmt = mysqli_prepare($conn, $sql);
	if (!$stmt) {
		error_log('List query failed: ' . mysqli_error($conn));
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
		error_log('List query failed: ' . mysqli_stmt_error($stmt));
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

// Changes one on/off column of one record and records the change. $column is 'is_active' or the homepage column.
function sim_set_flag($conn, $logger, $id, $column, $on, $what)
{
	global $kind, $t, $id_col;
	$old = sim_query($conn, "SELECT {$kind['heading']} AS heading, $column AS flag FROM $t WHERE $id_col = ?", [$id]);
	if (!$old) {
		return false;
	}
	$value = $on ? '1' : '2';
	if ($old[0]['flag'] === $value) {
		return true;
	}
	if (!sim_query($conn, "UPDATE $t SET $column = ? WHERE $id_col = ?", [$value, $id])) {
		return false;
	}
	$logger->log('UPDATE', $t, $id, [$column => $old[0]['flag']], [$column => $value], ucfirst($kind['one']) . ' ' . $what . ': ' . $old[0]['heading']);
	return true;
}

// ---------------------------------------------------------------
// Changes (POST only, with a token)
// ---------------------------------------------------------------
$notice = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['sim_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$action = is_string($_POST['sim_action']) ? $_POST['sim_action'] : '';
	// One record, or every ticked record.
	$ids = isset($_POST['sim_id']) ? [(int) $_POST['sim_id']] : array_map('intval', (array) ($_POST['sim_ids'] ?? []));
	$ids = array_slice(array_unique(array_filter($ids)), 0, 200);
	$done = '';

	if ($token_ok && ($action === 'show' || $action === 'hide')) {
		$changed = 0;
		foreach ($ids as $id) {
			$changed += sim_set_flag($conn, $logger, $id, 'is_active', $action === 'show', $action === 'show' ? 'shown on website' : 'hidden from website') ? 1 : 0;
		}
		$done = $changed > 0 ? ($action === 'show' ? 'shown' : 'hidden') . '-' . $changed : 'none';
	} elseif ($token_ok && $kind['flag'] !== '' && ($action === 'flag' || $action === 'unflag')) {
		$changed = 0;
		foreach ($ids as $id) {
			$changed += sim_set_flag($conn, $logger, $id, $kind['flag'], $action === 'flag', $action === 'flag' ? 'put on the homepage' : 'taken off the homepage') ? 1 : 0;
		}
		$done = $changed > 0 ? $action . '-' . $changed : 'none';
	} elseif ($token_ok && $action === 'delete') {
		$deleted = 0;
		foreach ($ids as $id) {
			$record = sim_query($conn, "SELECT * FROM $t WHERE $id_col = ?", [$id]);
			if (!$record) {
				continue;
			}
			$old_data = $logger->getOldRecordData($t, $id);
			$logger->log('DELETE', $t, $id, $old_data, null, ucfirst($kind['one']) . ' deleted: ' . $record[0][$kind['heading']]);
			$logger->archiveDeletedRecord($t, $id, $old_data);
			if (sim_query($conn, "DELETE FROM $t WHERE $id_col = ?", [$id])) {
				// The picture is removed only when a name is stored and the file is really there.
				$name = $kind['thumb'] !== '' ? basename(trim((string) $record[0][$kind['thumb']])) : '';
				if ($name !== '' && is_file($kind['folder'] . $name)) {
					@unlink($kind['folder'] . $name);
				}
				$deleted++;
			}
		}
		$done = $deleted > 0 ? 'deleted-' . $deleted : 'none';
	}

	if ($done !== '') {
		header('Location: ' . sim_url(['done' => $done]));
		exit;
	}
	$notice = 'Nothing was changed. Please reload the page and try again.';
}

// ---------------------------------------------------------------
// The list
// ---------------------------------------------------------------
$where = ['1 = 1'];
$params = [];
if ($f_search !== '') {
	$like = '%' . addcslashes($f_search, '%_\\') . '%';
	$parts = [];
	foreach ($kind['search'] as $column) {
		$parts[] = "a.$column LIKE ?";
		$params[] = $like;
	}
	$where[] = '(' . implode(' OR ', $parts) . ')';
}
if ($f_status !== '') {
	$where[] = $f_status === 'active' ? "a.is_active = '1'" : "a.is_active <> '1'";
}
if ($f_year > 0) {
	$where[] = "YEAR($when) = ?";
	$params[] = $f_year;
}
if ($f_flag !== '') {
	$where[] = "a.{$kind['flag']} = '1'";
}
if ($f_missing !== '') {
	$where[] = $kind['missing'][$f_missing][1];
}
$where_sql = 'WHERE ' . implode(' AND ', $where);

$counted = sim_query($conn, "SELECT COUNT(*) AS n FROM $t AS a $where_sql", $params);
$total = $counted ? (int) $counted[0]['n'] : 0;

// "Newest" is the order the website uses.
$site_order = $kind['date'] !== '' ? "a.{$kind['date']} DESC, a.$id_col DESC" : "a.$id_col DESC";
$order_sql = [
	'newest' => $site_order,
	'oldest' => $kind['date'] !== '' ? "a.{$kind['date']} ASC, a.$id_col ASC" : "a.$id_col ASC",
	'name' => "a.{$kind['heading']} ASC",
	'changed' => "a.tstp DESC, a.$id_col DESC",
][$f_sort];

$pages = max(1, (int) ceil($total / $f_per_page));
$f_page = min($f_page, $pages);
$offset = ($f_page - 1) * $f_per_page;

$missing_selects = '';
foreach ($kind['missing'] as $key => $spec) {
	$missing_selects .= ", ({$spec[1]}) AS miss_$key";
}
$records = sim_query(
	$conn,
	"SELECT a.$id_col AS id, a.{$kind['heading']} AS heading, a.{$kind['summary']} AS summary, a.is_active, a.tstp, $when AS shown_on"
		. ($kind['thumb'] !== '' ? ", a.{$kind['thumb']} AS thumb" : ", '' AS thumb")
		. ($kind['flag'] !== '' ? ", a.{$kind['flag']} AS flag" : ", '2' AS flag")
		. ($kind['rating'] !== '' ? ", a.{$kind['rating']} AS rating" : ", '' AS rating")
		. $missing_selects . "
	FROM $t AS a $where_sql ORDER BY $order_sql LIMIT ?, ?",
	array_merge($params, [$offset, $f_per_page])
) ?: [];

$stat = sim_query($conn, "SELECT COUNT(*) AS total, SUM(a.is_active = '1') AS active, SUM(a.is_active <> '1') AS hidden, SUM(YEAR($when) = YEAR(CURDATE())) AS this_year"
	. ($kind['flag'] !== '' ? ", SUM(a.{$kind['flag']} = '1' AND a.is_active = '1') AS flagged" : ", 0 AS flagged")
	. ($kind['rating'] !== '' ? ", AVG(CASE WHEN a.is_active = '1' AND a.{$kind['rating']} REGEXP '^[0-9]+(\\\\.[0-9]+)?$' THEN a.{$kind['rating']} + 0 END) AS average" : ", NULL AS average")
	. " FROM $t AS a");
$stat = $stat ? $stat[0] : ['total' => 0, 'active' => 0, 'hidden' => 0, 'this_year' => 0, 'flagged' => 0, 'average' => null];
$years = array_column(sim_query($conn, "SELECT DISTINCT YEAR($when) AS y FROM $t AS a WHERE $when IS NOT NULL ORDER BY y DESC") ?: [], 'y');

$filters_on = $f_search !== '' || $f_status !== '' || $f_year > 0 || $f_flag !== '' || $f_missing !== '';

// Messages after an action
$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : '';
if ($done === 'added') {
	$notice = 'The ' . $kind['one'] . ' was added.';
} elseif ($done === 'saved') {
	$notice = 'The changes were saved.';
} elseif (preg_match('/^(shown|hidden|flag|unflag|deleted)-(\d+)$/', $done, $match)) {
	$n = (int) $match[2];
	$who = $n . ' ' . ($n === 1 ? $kind['one'] . ' is' : $kind['many'] . ' are');
	$notice = [
		'shown' => "$who now live on the website.", 'hidden' => "$who now hidden from the website.",
		'flag' => "$who now on the homepage.", 'unflag' => "$who no longer on the homepage.",
		'deleted' => $n . ' ' . ($n === 1 ? $kind['one'] . ' was' : $kind['many'] . ' were') . ' deleted. A copy of each record is kept in the delete log.',
	][$match[1]];
} elseif ($done === 'none') {
	$notice = 'Nothing was changed.';
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
							<!--begin::List-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-articles.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-simple.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-articles dre-simple">

								<!-- Title, numbers, main actions -->
								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1><?= sim_e($kind['title']) ?></h1>
										<p><?= sim_e($kind['blurb']) ?></p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= number_format((int) $stat['active']) ?></strong><span>Live</span></div>
										<div><strong><?= number_format((int) $stat['hidden']) ?></strong><span>Hidden</span></div>
										<div><strong><?= number_format((int) $stat['this_year']) ?></strong><span><?= $kind['date'] !== '' ? 'Dated this year' : 'Added this year' ?></span></div>
										<?php if ($kind['flag'] !== ''): ?>
											<div><strong><?= number_format((int) $stat['flagged']) ?></strong><span>On homepage</span></div>
										<?php elseif ($kind['rating'] !== ''): ?>
											<div><strong><?= $stat['average'] !== null ? number_format((float) $stat['average'], 2) : '&ndash;' ?></strong><span>Average rating</span></div>
										<?php endif; ?>
									</div>
									<div class="dre-actions">
										<a class="dre-primary" href="<?= BASE_URL . $kind['add_file'] ?>"><i class="bi bi-plus-lg"></i> Add <?= sim_e($kind['one']) ?></a>
										<a href="<?= BASE_URL . $kind['content_file'] ?>?edit=<?= base64_encode(1) ?>"><i class="bi bi-window"></i> Page content</a>
										<a href="<?= BASE_URL . $kind['export_file'] ?>"><i class="bi bi-download"></i> Export XLS</a>
									</div>
								</div>

								<?php if ($notice !== ''): ?>
									<div class="dre-leads-notice" role="status" id="simNotice"><i class="bi bi-info-circle"></i> <?= sim_e($notice) ?></div>
								<?php endif; ?>

								<!-- Search and filters -->
								<form method="get" action="<?= BASE_URL . $kind['list_file'] ?>" class="dre-glass dre-leads-filter dre-proj-filter">
									<label class="dre-leads-search">
										<span>Search</span>
										<i class="bi bi-search"></i>
										<input type="search" name="q" value="<?= sim_e($f_search) ?>" placeholder="Name or text" autocomplete="off">
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
										<span>Year</span>
										<select name="year">
											<option value="">Any</option>
											<?php foreach ($years as $year): ?>
												<option value="<?= (int) $year ?>" <?= $f_year === (int) $year ? 'selected' : '' ?>><?= (int) $year ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span>Missing</span>
										<select name="missing">
											<option value="">Nothing selected</option>
											<?php foreach ($kind['missing'] as $key => $spec): ?>
												<option value="<?= sim_e($key) ?>" <?= $f_missing === $key ? 'selected' : '' ?>><?= sim_e($spec[0]) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span>Sort by</span>
										<select name="sort">
											<?php foreach ($sim_sorts as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_sort === $key ? 'selected' : '' ?>><?= sim_e($label) ?></option>
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
									<?php if ($kind['flag'] !== ''): ?>
										<label class="dre-proj-check">
											<input type="checkbox" name="flag" value="1" <?= $f_flag !== '' ? 'checked' : '' ?>> On the homepage only
										</label>
									<?php endif; ?>
									<div class="dre-leads-filter-buttons">
										<button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-funnel"></i> Apply</button>
										<?php if ($filters_on || $f_sort !== 'newest'): ?>
											<a class="dre-btn" href="<?= BASE_URL . $kind['list_file'] ?>">Clear</a>
										<?php endif; ?>
									</div>
								</form>

								<!-- The form the ticked records belong to. Its buttons choose what happens. -->
								<form method="post" action="<?= sim_e(sim_url()) ?>" id="simBulkForm">
									<input type="hidden" name="csrf" value="<?= sim_e($_SESSION['admin_csrf']) ?>">
								</form>

								<!-- The list -->
								<div class="dre-glass dre-leads-list">
									<div class="dre-h">
										<h3>
											<?= number_format($total) ?> <?= sim_e($total === 1 ? $kind['one'] : $kind['many']) ?>
											<small>
												<?php if ($total > 0): ?>
													Showing <?= number_format($offset + 1) ?>&ndash;<?= number_format($offset + count($records)) ?><?= $filters_on ? ' &middot; filtered' : '' ?>
												<?php else: ?>
													Nothing matches<?= $filters_on ? ' these filters' : '' ?>
												<?php endif; ?>
											</small>
										</h3>
										<?php if ($records): ?>
											<label class="dre-proj-all"><input type="checkbox" id="simSelectAll"> Select all on this page</label>
										<?php endif; ?>
									</div>

									<?php if (!$records): ?>
										<div class="dre-empty">
											<i class="bi <?= sim_e($kind['icon']) ?>" style="font-size: 2rem;"></i><br>
											Nothing to show.
											<?php if ($filters_on): ?>
												<a href="<?= BASE_URL . $kind['list_file'] ?>">Show everything</a>
											<?php endif; ?>
										</div>
									<?php else: ?>
										<div class="dre-proj-table" role="table" aria-label="<?= sim_e($kind['title']) ?>">
											<div class="dre-proj-row dre-proj-head" role="row">
												<div role="columnheader"><span class="sr-only">Select</span></div>
												<div role="columnheader"><?= $kind['rating'] !== '' ? 'Client' : 'Award' ?></div>
												<div role="columnheader">Text</div>
												<div role="columnheader"><?= $kind['date'] !== '' ? 'Date' : 'Added' ?></div>
												<div role="columnheader">Status</div>
												<div role="columnheader"><span class="sr-only">Actions</span></div>
											</div>
											<?php foreach ($records as $row):
												$id = (int) $row['id'];
												$is_live = $row['is_active'] === '1';
												$is_flagged = $row['flag'] === '1';
												$thumb = trim((string) $row['thumb']);
												$edit_url = BASE_URL . $kind['edit_file'] . '?edit=' . base64_encode($id);
												$summary = trim(preg_replace('/\s+/', ' ', str_replace("\xC2\xA0", ' ', html_entity_decode(strip_tags((string) $row['summary']), ENT_QUOTES, 'UTF-8'))));
												$shown_on = $row['shown_on'] ? strtotime($row['shown_on']) : false;
												$rating = is_numeric($row['rating']) ? max(0, min(5, (float) $row['rating'])) : null;
												$missing = [];
												foreach ($kind['missing'] as $key => $spec) {
													if ((int) $row['miss_' . $key] === 1) {
														$missing[] = strtolower($spec[0]);
													}
												}
											?>
												<div class="dre-proj-row<?= $is_live ? '' : ' is-hidden' ?>" role="row">
													<div class="dre-proj-pick" role="cell">
														<input type="checkbox" name="sim_ids[]" value="<?= $id ?>" form="simBulkForm" class="dre-proj-box" aria-label="Select <?= sim_e($row['heading']) ?>">
													</div>
													<div class="dre-proj-main" role="cell">
														<a class="dre-proj-thumb<?= $kind['thumb'] === '' ? ' dre-sim-initial' : '' ?>" href="<?= sim_e($edit_url) ?>" tabindex="-1" aria-hidden="true">
															<?php if ($thumb !== ''): ?>
																<img src="<?= BASE_URL . $kind['folder'] . sim_e(rawurlencode($thumb)) ?>" alt="" loading="lazy" onerror="this.remove()">
															<?php endif; ?>
															<?php if ($kind['thumb'] === ''): ?>
																<span><?= sim_e(mb_strtoupper(mb_substr(trim((string) $row['heading']), 0, 1)) ?: '?') ?></span>
															<?php else: ?>
																<i class="bi <?= sim_e($kind['icon']) ?>"></i>
															<?php endif; ?>
														</a>
														<div>
															<a class="dre-proj-name" href="<?= sim_e($edit_url) ?>"><?= trim((string) $row['heading']) !== '' ? sim_e($row['heading']) : 'No name' ?></a>
															<?php if ($rating !== null): ?>
																<span class="dre-sim-stars" title="<?= sim_e($row['rating']) ?> out of 5" aria-label="Rated <?= sim_e($row['rating']) ?> out of 5"><i style="width: <?= round($rating * 20) ?>%"></i></span>
																<span class="dre-proj-sub"><?= sim_e($row['rating']) ?> out of 5</span>
															<?php endif; ?>
															<?php if ($is_flagged): ?>
																<div class="dre-proj-pills"><span class="dre-pill dre-pill-ai"><i class="bi bi-house-heart"></i> <?= sim_e($kind['flag_label']) ?></span></div>
															<?php endif; ?>
														</div>
													</div>
													<div class="dre-art-summary" role="cell">
														<span><?= $summary !== '' ? sim_e(mb_strimwidth($summary, 0, 150, '...')) : '<em>No text</em>' ?></span>
														<?php if ($missing): ?>
															<span class="dre-proj-missing" title="Details that are not filled in"><i class="bi bi-exclamation-triangle"></i> Missing: <?= sim_e(implode(', ', $missing)) ?></span>
														<?php endif; ?>
													</div>
													<div class="dre-proj-facts" role="cell">
														<strong><?= $shown_on ? date('d M Y', $shown_on) : 'No date' ?></strong>
														<span><?= $summary !== '' ? number_format(str_word_count($summary)) . ' words' : '' ?></span>
													</div>
													<div class="dre-proj-status" role="cell">
														<form method="post" action="<?= sim_e(sim_url()) ?>">
															<input type="hidden" name="csrf" value="<?= sim_e($_SESSION['admin_csrf']) ?>">
															<input type="hidden" name="sim_id" value="<?= $id ?>">
															<button type="submit" name="sim_action" value="<?= $is_live ? 'hide' : 'show' ?>" class="dre-switch<?= $is_live ? ' is-on' : '' ?>" role="switch" aria-checked="<?= $is_live ? 'true' : 'false' ?>" title="<?= $is_live ? 'Live on the website. Click to hide.' : 'Hidden from the website. Click to show.' ?>">
																<i></i><span><?= $is_live ? 'Live' : 'Hidden' ?></span>
															</button>
														</form>
														<span class="dre-proj-sync"><i class="bi bi-clock-history"></i> Changed <?= strtotime((string) $row['tstp']) > 0 ? date('d M Y', strtotime($row['tstp'])) : 'not known' ?></span>
													</div>
													<div class="dre-proj-actions" role="cell">
														<a class="dre-icon-btn" href="<?= sim_e($edit_url) ?>" title="Edit" aria-label="Edit <?= sim_e($row['heading']) ?>"><i class="bi bi-pencil"></i></a>
														<div class="dropdown">
															<button type="button" class="dre-icon-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More" aria-label="More actions for <?= sim_e($row['heading']) ?>"><i class="bi bi-three-dots"></i></button>
															<div class="dropdown-menu dropdown-menu-right">
																<form method="post" action="<?= sim_e(sim_url()) ?>" class="dre-sim-row-form" data-name="<?= sim_e($row['heading']) ?>">
																	<input type="hidden" name="csrf" value="<?= sim_e($_SESSION['admin_csrf']) ?>">
																	<input type="hidden" name="sim_id" value="<?= $id ?>">
																	<?php if ($kind['flag'] !== ''): ?>
																		<button type="submit" name="sim_action" value="<?= $is_flagged ? 'unflag' : 'flag' ?>" class="dropdown-item"><i class="bi bi-house-heart mr-2"></i><?= $is_flagged ? 'Take off the homepage' : 'Put on the homepage' ?></button>
																		<div class="dropdown-divider"></div>
																	<?php endif; ?>
																	<button type="submit" name="sim_action" value="delete" class="dropdown-item text-danger"><i class="bi bi-trash3 mr-2"></i>Delete</button>
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
													<a href="<?= sim_e(sim_url(['page' => $f_page - 1])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
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
													<a href="<?= sim_e(sim_url(['page' => $number])) ?>" class="<?= $number === $f_page ? 'is-on' : '' ?>" <?= $number === $f_page ? 'aria-current="page"' : '' ?>><?= $number ?></a>
												<?php $previous = $number;
												endforeach; ?>
												<?php if ($f_page < $pages): ?>
													<a href="<?= sim_e(sim_url(['page' => $f_page + 1])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
												<?php endif; ?>
											</nav>
										<?php endif; ?>
									<?php endif; ?>
								</div>

								<!-- Appears when at least one record is ticked -->
								<div class="dre-proj-bulk" id="simBulkBar" hidden>
									<strong><span id="simBulkCount">0</span> selected</strong>
									<div>
										<button type="submit" form="simBulkForm" name="sim_action" value="show" class="dre-btn dre-btn-main"><i class="bi bi-eye"></i> Show on website</button>
										<button type="submit" form="simBulkForm" name="sim_action" value="hide" class="dre-btn"><i class="bi bi-eye-slash"></i> Hide</button>
										<?php if ($kind['flag'] !== ''): ?>
											<button type="submit" form="simBulkForm" name="sim_action" value="flag" class="dre-btn"><i class="bi bi-house-heart"></i> Put on homepage</button>
											<button type="submit" form="simBulkForm" name="sim_action" value="unflag" class="dre-btn">Take off homepage</button>
										<?php endif; ?>
										<button type="submit" form="simBulkForm" name="sim_action" value="delete" class="dre-btn" data-confirm="Delete the selected <?= sim_e($kind['many']) ?>? A copy of each record is kept in the delete log."><i class="bi bi-trash3"></i> Delete</button>
										<button type="button" class="dre-btn" id="simBulkClear">Clear</button>
									</div>
								</div>
							</div>

							<script>
								// Selection bar, confirmations, and the message that fades out.
								(function () {
									var boxes = Array.prototype.slice.call(document.querySelectorAll('.dre-proj-box'));
									var all = document.getElementById('simSelectAll');
									var bar = document.getElementById('simBulkBar');
									var count = document.getElementById('simBulkCount');

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
									document.getElementById('simBulkClear').addEventListener('click', function () {
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
									document.querySelectorAll('.dre-sim-row-form').forEach(function (form) {
										form.addEventListener('submit', function (event) {
											var button = event.submitter;
											if (button && button.value === 'delete' && !window.confirm('Delete "' + form.getAttribute('data-name') + '"? A copy of the record is kept in the delete log.')) {
												event.preventDefault();
											}
										});
									});
									refresh();

									var notice = document.getElementById('simNotice');
									if (notice) {
										setTimeout(function () { notice.style.display = 'none'; }, 8000);
									}
								})();
							</script>
							<!--end::List-->

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