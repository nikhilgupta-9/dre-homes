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

// Blog posts and company news are the same kind of record. This copy works on one of them:
// its table, the prefix of its columns, and where it shows on the website.
$kind = [
	'one' => 'post', 'many' => 'posts', 'title' => 'Blog',
	'table' => 'tbl_blog', 'p' => 'blog',
	'folder' => 'assets/media/blog/', 'public_path' => 'our-blogs/',
	'list_file' => 'manage-blog.php', 'add_file' => 'add-blog.php', 'edit_file' => 'edit-blog.php',
	'export_file' => 'export-blog.php', 'content_file' => 'edit-blog-content.php',
	'spotlight' => false, 'blurb' => 'Articles on the Blog page of the website.',
];
$p = $kind['p'];
$t = $kind['table'];

$art_sorts = ['newest' => 'Latest date first', 'oldest' => 'Oldest date first', 'title' => 'Title A-Z', 'changed' => 'Last changed first'];
$art_missing = ['thumb' => 'Card image', 'inner' => 'Inner image', 'short' => 'Short description', 'meta' => 'Meta description'];

// ---------------------------------------------------------------
// Filters (all optional, all from the address bar)
// ---------------------------------------------------------------
$f_status = in_array($_GET['status'] ?? '', ['active', 'inactive'], true) ? $_GET['status'] : '';
$f_year = isset($_GET['year']) && preg_match('/^(19|20)\d\d$/', (string) $_GET['year']) ? (int) $_GET['year'] : 0;
$f_spot = $kind['spotlight'] && ($_GET['spot'] ?? '') === '1' ? '1' : '';
$f_missing = isset($_GET['missing']) && is_string($_GET['missing']) && isset($art_missing[$_GET['missing']]) ? $_GET['missing'] : '';
$f_sort = isset($_GET['sort']) && is_string($_GET['sort']) && isset($art_sorts[$_GET['sort']]) ? $_GET['sort'] : 'newest';
$f_search = isset($_GET['q']) && is_string($_GET['q']) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$f_per_page = (int) ($_GET['per'] ?? 25);
if (!in_array($f_per_page, [25, 50, 100], true)) {
	$f_per_page = 25;
}
$f_page = max(1, (int) ($_GET['page'] ?? 1));

// Address of this page with the current filters, with some of them changed.
function art_url(array $change = [])
{
	global $kind, $f_status, $f_year, $f_spot, $f_missing, $f_sort, $f_search, $f_per_page, $f_page;
	$params = array_merge([
		'status' => $f_status, 'year' => $f_year ?: '', 'spot' => $f_spot, 'missing' => $f_missing,
		'sort' => $f_sort === 'newest' ? '' : $f_sort, 'q' => $f_search,
		'per' => $f_per_page === 25 ? '' : $f_per_page, 'page' => $f_page === 1 ? '' : $f_page,
	], $change);
	$params = array_filter($params, function ($value) {
		return $value !== '' && $value !== null;
	});
	return BASE_URL . $kind['list_file'] . ($params ? '?' . http_build_query($params) : '');
}

function art_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function art_query($conn, $sql, array $params = [])
{
	$stmt = mysqli_prepare($conn, $sql);
	if (!$stmt) {
		error_log('Article query failed: ' . mysqli_error($conn));
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
		error_log('Article query failed: ' . mysqli_stmt_error($stmt));
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

// Changes one on/off column of one record and records the change. $column is 'is_active' or the spotlight column.
function art_set_flag($conn, $logger, $id, $column, $on, $what)
{
	global $kind, $p, $t;
	$old = art_query($conn, "SELECT {$p}_id, {$p}_heading AS heading, $column AS flag FROM $t WHERE {$p}_id = ?", [$id]);
	if (!$old) {
		return false;
	}
	$value = $on ? '1' : '2';
	if ($old[0]['flag'] === $value) {
		return true;
	}
	if (!art_query($conn, "UPDATE $t SET $column = ? WHERE {$p}_id = ?", [$value, $id])) {
		return false;
	}
	$logger->log('UPDATE', $t, $id, [$column => $old[0]['flag']], [$column => $value], $kind['title'] . ' ' . $what . ': ' . $old[0]['heading']);
	return true;
}

// ---------------------------------------------------------------
// Changes (POST only, with a token)
// ---------------------------------------------------------------
$notice = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['art_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$action = is_string($_POST['art_action']) ? $_POST['art_action'] : '';
	$done = '';

	if ($token_ok && ($action === 'show' || $action === 'hide')) {
		// One record, or every ticked record.
		$ids = isset($_POST['art_id']) ? [(int) $_POST['art_id']] : array_map('intval', (array) ($_POST['art_ids'] ?? []));
		$ids = array_slice(array_unique(array_filter($ids)), 0, 200);
		$changed = 0;
		foreach ($ids as $id) {
			$changed += art_set_flag($conn, $logger, $id, 'is_active', $action === 'show', $action === 'show' ? 'shown on website' : 'hidden from website') ? 1 : 0;
		}
		$done = $changed > 0 ? ($action === 'show' ? 'shown' : 'hidden') . '-' . $changed : 'none';
	} elseif ($token_ok && $kind['spotlight'] && ($action === 'spot' || $action === 'unspot')) {
		$id = (int) ($_POST['art_id'] ?? 0);
		$done = art_set_flag($conn, $logger, $id, 'news_markspot', $action === 'spot', $action === 'spot' ? 'put in the spotlight' : 'taken out of the spotlight') ? $action : 'none';
	} elseif ($token_ok && $action === 'delete') {
		$id = (int) ($_POST['art_id'] ?? 0);
		$record = $id > 0 ? art_query($conn, "SELECT * FROM $t WHERE {$p}_id = ?", [$id]) : [];
		if ($record) {
			$record = $record[0];
			// Files are removed only when a name is stored and the file is really there.
			foreach ([['thumb/', "{$p}_thumb_img"], ['inner/', "{$p}_inner_img"]] as $file) {
				$name = basename(trim((string) $record[$file[1]]));
				if ($name !== '' && is_file($kind['folder'] . $file[0] . $name)) {
					@unlink($kind['folder'] . $file[0] . $name);
				}
			}
			$old_data = $logger->getOldRecordData($t, $id);
			$logger->log('DELETE', $t, $id, $old_data, null, $kind['title'] . ' deleted: ' . $record["{$p}_heading"]);
			$logger->archiveDeletedRecord($t, $id, $old_data);
			art_query($conn, "DELETE FROM $t WHERE {$p}_id = ?", [$id]);
			$done = 'deleted';
		} else {
			$done = 'none';
		}
	}

	if ($done !== '') {
		header('Location: ' . art_url(['done' => $done]));
		exit;
	}
	$notice = 'Nothing was changed. Please reload the page and try again.';
}

// ---------------------------------------------------------------
// The list
// ---------------------------------------------------------------
$missing_sql = [
	'thumb' => "(a.{$p}_thumb_img IS NULL OR TRIM(a.{$p}_thumb_img) = '')",
	'inner' => "(a.{$p}_inner_img IS NULL OR TRIM(a.{$p}_inner_img) = '')",
	'short' => "(a.{$p}_short_descp IS NULL OR TRIM(a.{$p}_short_descp) = '')",
	'meta' => "(a.meta_description IS NULL OR TRIM(a.meta_description) = '')",
];
$where = ['1 = 1'];
$params = [];
if ($f_search !== '') {
	$like = '%' . addcslashes($f_search, '%_\\') . '%';
	$where[] = "(a.{$p}_heading LIKE ? OR a.{$p}_slug LIKE ? OR a.{$p}_short_descp LIKE ?)";
	array_push($params, $like, $like, $like);
}
if ($f_status !== '') {
	$where[] = $f_status === 'active' ? "a.is_active = '1'" : "a.is_active <> '1'";
}
if ($f_year > 0) {
	$where[] = "YEAR(a.{$p}_posted_on) = ?";
	$params[] = $f_year;
}
if ($f_spot !== '') {
	$where[] = "a.news_markspot = '1'";
}
if ($f_missing !== '') {
	$where[] = $missing_sql[$f_missing];
}
$where_sql = 'WHERE ' . implode(' AND ', $where);

$counted = art_query($conn, "SELECT COUNT(*) AS n FROM $t AS a $where_sql", $params);
$total = $counted ? (int) $counted[0]['n'] : 0;

$order_sql = [
	'newest' => "a.{$p}_posted_on DESC, a.{$p}_id DESC",
	'oldest' => "a.{$p}_posted_on ASC, a.{$p}_id ASC",
	'title' => "a.{$p}_heading ASC",
	'changed' => "a.tstp DESC, a.{$p}_id DESC",
][$f_sort];

$pages = max(1, (int) ceil($total / $f_per_page));
$f_page = min($f_page, $pages);
$offset = ($f_page - 1) * $f_per_page;

$articles = art_query(
	$conn,
	"SELECT a.{$p}_id AS id, a.{$p}_thumb_img AS thumb, a.{$p}_inner_img AS inner_img, a.{$p}_heading AS heading, a.{$p}_short_descp AS short_descp,
		a.{$p}_slug AS slug, a.{$p}_posted_on AS posted_on, a.meta_description, a.is_active, a.tstp,
		CHAR_LENGTH(a.{$p}_long_descp) AS body_length" . ($kind['spotlight'] ? ", a.news_markspot AS spotlight" : ", '2' AS spotlight") . "
	FROM $t AS a $where_sql ORDER BY $order_sql LIMIT ?, ?",
	array_merge($params, [$offset, $f_per_page])
) ?: [];

$stat = art_query($conn, "SELECT COUNT(*) AS total, SUM(is_active = '1') AS active, SUM(is_active <> '1') AS hidden, SUM(YEAR({$p}_posted_on) = YEAR(CURDATE())) AS this_year, MAX({$p}_posted_on) AS latest FROM $t");
$stat = $stat ? $stat[0] : ['total' => 0, 'active' => 0, 'hidden' => 0, 'this_year' => 0, 'latest' => null];
$years = array_column(art_query($conn, "SELECT DISTINCT YEAR({$p}_posted_on) AS y FROM $t WHERE {$p}_posted_on IS NOT NULL ORDER BY y DESC") ?: [], 'y');
// The website shows one spotlight item: the live one with the latest date.
$shown_spotlight = 0;
if ($kind['spotlight']) {
	$spot = art_query($conn, "SELECT news_id FROM tbl_news WHERE is_active = '1' AND news_markspot = '1' ORDER BY news_posted_on DESC LIMIT 1");
	$shown_spotlight = $spot ? (int) $spot[0]['news_id'] : 0;
}

$filters_on = $f_search !== '' || $f_status !== '' || $f_year > 0 || $f_spot !== '' || $f_missing !== '';

// Messages after an action
$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : '';
if ($done === 'added') {
	$notice = 'The ' . $kind['one'] . ' was added.';
} elseif ($done === 'saved') {
	$notice = 'The changes were saved.';
} elseif ($done === 'deleted') {
	$notice = 'The ' . $kind['one'] . ' was deleted. A copy is kept in the delete log.';
} elseif ($done === 'spot') {
	$notice = 'Marked as spotlight. The website shows the live spotlight item with the latest date.';
} elseif ($done === 'unspot') {
	$notice = 'Taken out of the spotlight.';
} elseif (preg_match('/^(shown|hidden)-(\d+)$/', $done, $match)) {
	$notice = $match[2] . ' ' . ((int) $match[2] === 1 ? $kind['one'] . ' is' : $kind['many'] . ' are') . ' now ' . ($match[1] === 'shown' ? 'live on the website.' : 'hidden from the website.');
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

							<!--begin::Articles-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-articles.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-articles">

								<!-- Title, numbers, main actions -->
								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1><?= art_e($kind['title']) ?></h1>
										<p><?= art_e($kind['blurb']) ?></p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= number_format((int) $stat['active']) ?></strong><span>Live</span></div>
										<div><strong><?= number_format((int) $stat['hidden']) ?></strong><span>Hidden</span></div>
										<div><strong><?= number_format((int) $stat['this_year']) ?></strong><span>Dated this year</span></div>
										<div><strong><?= $stat['latest'] ? date('d M', strtotime($stat['latest'])) : '&ndash;' ?></strong><span>Latest date</span></div>
									</div>
									<div class="dre-actions">
										<a class="dre-primary" href="<?= BASE_URL . $kind['add_file'] ?>"><i class="bi bi-plus-lg"></i> Add <?= art_e($kind['one']) ?></a>
										<a href="<?= BASE_URL . $kind['content_file'] ?>?edit=<?= base64_encode(1) ?>"><i class="bi bi-window"></i> Page content</a>
										<a href="<?= BASE_URL . $kind['export_file'] ?>"><i class="bi bi-download"></i> Export XLS</a>
									</div>
								</div>

								<?php if ($notice !== ''): ?>
									<div class="dre-leads-notice" role="status" id="artNotice"><i class="bi bi-info-circle"></i> <?= art_e($notice) ?></div>
								<?php endif; ?>

								<!-- Search and filters -->
								<form method="get" action="<?= BASE_URL . $kind['list_file'] ?>" class="dre-glass dre-leads-filter dre-proj-filter">
									<label class="dre-leads-search">
										<span>Search</span>
										<i class="bi bi-search"></i>
										<input type="search" name="q" value="<?= art_e($f_search) ?>" placeholder="Title, page address or short description" autocomplete="off">
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
											<?php foreach ($art_missing as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_missing === $key ? 'selected' : '' ?>><?= art_e($label) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span>Sort by</span>
										<select name="sort">
											<?php foreach ($art_sorts as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_sort === $key ? 'selected' : '' ?>><?= art_e($label) ?></option>
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
									<?php if ($kind['spotlight']): ?>
										<label class="dre-proj-check">
											<input type="checkbox" name="spot" value="1" <?= $f_spot !== '' ? 'checked' : '' ?>> Spotlight only
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
								<form method="post" action="<?= art_e(art_url()) ?>" id="artBulkForm">
									<input type="hidden" name="csrf" value="<?= art_e($_SESSION['admin_csrf']) ?>">
								</form>

								<!-- The list -->
								<div class="dre-glass dre-leads-list">
									<div class="dre-h">
										<h3>
											<?= number_format($total) ?> <?= art_e($total === 1 ? $kind['one'] : $kind['many']) ?>
											<small>
												<?php if ($total > 0): ?>
													Showing <?= number_format($offset + 1) ?>&ndash;<?= number_format($offset + count($articles)) ?><?= $filters_on ? ' &middot; filtered' : '' ?>
												<?php else: ?>
													Nothing matches<?= $filters_on ? ' these filters' : '' ?>
												<?php endif; ?>
											</small>
										</h3>
										<?php if ($articles): ?>
											<label class="dre-proj-all"><input type="checkbox" id="artSelectAll"> Select all on this page</label>
										<?php endif; ?>
									</div>

									<?php if (!$articles): ?>
										<div class="dre-empty">
											<i class="bi bi-journal-text" style="font-size: 2rem;"></i><br>
											Nothing to show.
											<?php if ($filters_on): ?>
												<a href="<?= BASE_URL . $kind['list_file'] ?>">Show everything</a>
											<?php endif; ?>
										</div>
									<?php else: ?>
										<div class="dre-proj-table" role="table" aria-label="<?= art_e($kind['title']) ?>">
											<div class="dre-proj-row dre-proj-head" role="row">
												<div role="columnheader"><span class="sr-only">Select</span></div>
												<div role="columnheader">Title</div>
												<div role="columnheader">Summary</div>
												<div role="columnheader">Date</div>
												<div role="columnheader">Status</div>
												<div role="columnheader"><span class="sr-only">Actions</span></div>
											</div>
											<?php foreach ($articles as $row):
												$id = (int) $row['id'];
												$is_live = $row['is_active'] === '1';
												$is_spot = $row['spotlight'] === '1';
												$thumb = trim((string) $row['thumb']);
												$edit_url = BASE_URL . $kind['edit_file'] . '?edit=' . base64_encode($id);
												$summary = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $row['short_descp']), ENT_QUOTES, 'UTF-8')));
												$posted = $row['posted_on'] ? strtotime($row['posted_on']) : false;
												// About 6 characters a word and 200 words a minute, counted on the stored text.
												$minutes = max(1, (int) round((int) $row['body_length'] / 6 / 200));
												$missing = [];
												if ($thumb === '') {
													$missing[] = 'card image';
												}
												if (trim((string) $row['inner_img']) === '') {
													$missing[] = 'inner image';
												}
												if ($summary === '') {
													$missing[] = 'short description';
												}
												if (trim((string) $row['meta_description']) === '') {
													$missing[] = 'meta description';
												}
											?>
												<div class="dre-proj-row<?= $is_live ? '' : ' is-hidden' ?>" role="row">
													<div class="dre-proj-pick" role="cell">
														<input type="checkbox" name="art_ids[]" value="<?= $id ?>" form="artBulkForm" class="dre-proj-box" aria-label="Select <?= art_e($row['heading']) ?>">
													</div>
													<div class="dre-proj-main" role="cell">
														<a class="dre-proj-thumb" href="<?= art_e($edit_url) ?>" tabindex="-1" aria-hidden="true">
															<?php if ($thumb !== ''): ?>
																<img src="<?= BASE_URL . $kind['folder'] ?>thumb/<?= art_e(rawurlencode($thumb)) ?>" alt="" loading="lazy" onerror="this.remove()">
															<?php endif; ?>
															<i class="bi bi-journal-text"></i>
														</a>
														<div>
															<a class="dre-proj-name" href="<?= art_e($edit_url) ?>"><?= trim((string) $row['heading']) !== '' ? art_e($row['heading']) : 'Untitled' ?></a>
															<span class="dre-proj-sub"><i class="bi bi-link-45deg"></i> /<?= art_e($kind['public_path'] . $row['slug']) ?></span>
															<?php if ($is_spot): ?>
																<div class="dre-proj-pills">
																	<span class="dre-pill <?= $id === $shown_spotlight ? 'dre-pill-good' : 'dre-pill-ai' ?>"><i class="bi bi-star-fill"></i> <?= $id === $shown_spotlight ? 'Spotlight on the website now' : 'Spotlight (an item with a later date is shown)' ?></span>
																</div>
															<?php endif; ?>
														</div>
													</div>
													<div class="dre-art-summary" role="cell">
														<span><?= $summary !== '' ? art_e(mb_strimwidth($summary, 0, 120, '...')) : '<em>No short description</em>' ?></span>
														<?php if ($missing): ?>
															<span class="dre-proj-missing" title="Details that are not filled in"><i class="bi bi-exclamation-triangle"></i> Missing: <?= art_e(implode(', ', $missing)) ?></span>
														<?php endif; ?>
													</div>
													<div class="dre-proj-facts" role="cell">
														<strong><?= $posted ? date('d M Y', $posted) : 'No date' ?></strong>
														<span><?= (int) $row['body_length'] > 0 ? 'About ' . $minutes . ' min read' : 'No text yet' ?></span>
													</div>
													<div class="dre-proj-status" role="cell">
														<form method="post" action="<?= art_e(art_url()) ?>">
															<input type="hidden" name="csrf" value="<?= art_e($_SESSION['admin_csrf']) ?>">
															<input type="hidden" name="art_id" value="<?= $id ?>">
															<button type="submit" name="art_action" value="<?= $is_live ? 'hide' : 'show' ?>" class="dre-switch<?= $is_live ? ' is-on' : '' ?>" role="switch" aria-checked="<?= $is_live ? 'true' : 'false' ?>" title="<?= $is_live ? 'Live on the website. Click to hide.' : 'Hidden from the website. Click to show.' ?>">
																<i></i><span><?= $is_live ? 'Live' : 'Hidden' ?></span>
															</button>
														</form>
														<span class="dre-proj-sync"><i class="bi bi-clock-history"></i> Changed <?= strtotime((string) $row['tstp']) > 0 ? date('d M Y', strtotime($row['tstp'])) : 'not known' ?></span>
													</div>
													<div class="dre-proj-actions" role="cell">
														<a class="dre-icon-btn" href="<?= art_e($edit_url) ?>" title="Edit" aria-label="Edit <?= art_e($row['heading']) ?>"><i class="bi bi-pencil"></i></a>
														<a class="dre-icon-btn" href="<?= art_e(BASE_URL_HOME . $kind['public_path'] . $row['slug']) ?>" target="_blank" rel="noopener" title="<?= $is_live ? 'View on the website' : 'Hidden items do not open on the website' ?>" aria-label="View <?= art_e($row['heading']) ?> on the website"><i class="bi bi-box-arrow-up-right"></i></a>
														<div class="dropdown">
															<button type="button" class="dre-icon-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More" aria-label="More actions for <?= art_e($row['heading']) ?>"><i class="bi bi-three-dots"></i></button>
															<div class="dropdown-menu dropdown-menu-right">
																<?php if ($kind['spotlight']): ?>
																	<form method="post" action="<?= art_e(art_url()) ?>">
																		<input type="hidden" name="csrf" value="<?= art_e($_SESSION['admin_csrf']) ?>">
																		<input type="hidden" name="art_id" value="<?= $id ?>">
																		<button type="submit" name="art_action" value="<?= $is_spot ? 'unspot' : 'spot' ?>" class="dropdown-item"><i class="bi <?= $is_spot ? 'bi-star' : 'bi-star-fill' ?> mr-2"></i><?= $is_spot ? 'Take out of the spotlight' : 'Put in the spotlight' ?></button>
																	</form>
																	<div class="dropdown-divider"></div>
																<?php endif; ?>
																<form method="post" action="<?= art_e(art_url()) ?>" class="dre-art-delete" data-name="<?= art_e($row['heading']) ?>">
																	<input type="hidden" name="csrf" value="<?= art_e($_SESSION['admin_csrf']) ?>">
																	<input type="hidden" name="art_id" value="<?= $id ?>">
																	<button type="submit" name="art_action" value="delete" class="dropdown-item text-danger"><i class="bi bi-trash3 mr-2"></i>Delete</button>
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
													<a href="<?= art_e(art_url(['page' => $f_page - 1])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
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
													<a href="<?= art_e(art_url(['page' => $number])) ?>" class="<?= $number === $f_page ? 'is-on' : '' ?>" <?= $number === $f_page ? 'aria-current="page"' : '' ?>><?= $number ?></a>
												<?php $previous = $number;
												endforeach; ?>
												<?php if ($f_page < $pages): ?>
													<a href="<?= art_e(art_url(['page' => $f_page + 1])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
												<?php endif; ?>
											</nav>
										<?php endif; ?>
									<?php endif; ?>
								</div>

								<!-- Appears when at least one record is ticked -->
								<div class="dre-proj-bulk" id="artBulkBar" hidden>
									<strong><span id="artBulkCount">0</span> selected</strong>
									<div>
										<button type="submit" form="artBulkForm" name="art_action" value="show" class="dre-btn dre-btn-main" data-confirm="Show the selected items on the website?"><i class="bi bi-eye"></i> Show on website</button>
										<button type="submit" form="artBulkForm" name="art_action" value="hide" class="dre-btn" data-confirm="Hide the selected items from the website?"><i class="bi bi-eye-slash"></i> Hide</button>
										<button type="button" class="dre-btn" id="artBulkClear">Clear</button>
									</div>
								</div>
							</div>

							<script>
								// Selection bar, confirmations, and the message that fades out.
								(function () {
									var boxes = Array.prototype.slice.call(document.querySelectorAll('.dre-proj-box'));
									var all = document.getElementById('artSelectAll');
									var bar = document.getElementById('artBulkBar');
									var count = document.getElementById('artBulkCount');

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
									document.getElementById('artBulkClear').addEventListener('click', function () {
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
									document.querySelectorAll('.dre-art-delete').forEach(function (form) {
										form.addEventListener('submit', function (event) {
											if (!window.confirm('Delete "' + form.getAttribute('data-name') + '"? Its pictures are removed too. A copy of the record is kept in the delete log.')) {
												event.preventDefault();
											}
										});
									});
									refresh();

									var notice = document.getElementById('artNotice');
									if (notice) {
										setTimeout(function () { notice.style.display = 'none'; }, 8000);
									}
								})();
							</script>
							<!--end::Articles-->

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