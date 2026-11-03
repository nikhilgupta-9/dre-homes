<?php
include('db/config.php');
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

// What a title and a description should roughly be, in characters. Search engines cut longer ones.
const TAG_TITLE_MAX = 70;
const TAG_DESC_MIN = 70;
const TAG_DESC_MAX = 160;

$tag_checks = ['short_desc' => 'Description too short', 'long_desc' => 'Description too long', 'long_title' => 'Title too long', 'no_image' => 'No sharing picture'];

// ---------------------------------------------------------------
// Changes (POST only, with a token): switch one page's tags on or off
// ---------------------------------------------------------------
$notice = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['tag_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$action = is_string($_POST['tag_action']) ? $_POST['tag_action'] : '';
	$id = (int) ($_POST['tag_id'] ?? 0);
	$tag = $token_ok && $id > 0 && in_array($action, ['show', 'hide'], true) ? set_query($conn, "SELECT t.is_active, p.page_name FROM tbl_tags t LEFT JOIN tbl_pages p ON p.id = t.page_id WHERE t.id = ?", [$id]) : [];
	if ($tag) {
		$value = $action === 'show' ? '1' : '2';
		if ($tag[0]['is_active'] !== $value) {
			set_query($conn, "UPDATE tbl_tags SET is_active = ? WHERE id = ?", [$value, $id]);
			$logger->log('UPDATE', 'tbl_tags', $id, ['is_active' => $tag[0]['is_active']], ['is_active' => $value], 'SEO tags ' . ($value === '1' ? 'switched on: ' : 'switched off: ') . trim((string) $tag[0]['page_name']));
		}
		if (isset($_POST['ajax'])) {
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(['status' => 'success', 'is_on' => ($value === '1'), 'id' => $id]);
			exit;
		}
		header('Location: ' . BASE_URL . 'manage-tags.php?done=' . ($action === 'show' ? 'on' : 'off'));
		exit;
	}
	if (isset($_POST['ajax'])) {
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['status' => 'error', 'message' => 'Nothing was changed.']);
		exit;
	}
	$notice = 'Nothing was changed. Please reload the page and try again.';
}

// ---------------------------------------------------------------
// The list: one row per page of the website
// ---------------------------------------------------------------
$f_check = isset($_GET['check']) && is_string($_GET['check']) && isset($tag_checks[$_GET['check']]) ? $_GET['check'] : '';
$f_search = isset($_GET['q']) && is_string($_GET['q']) ? trim(mb_substr($_GET['q'], 0, 100)) : '';

$all = set_query($conn, "SELECT t.id, t.meta_title, t.meta_description, t.meta_og_img, t.is_active, t.tstp, p.page_name, p.page_slug FROM tbl_tags t LEFT JOIN tbl_pages p ON p.id = t.page_id ORDER BY p.page_name") ?: [];
$tags = [];
$counts = ['total' => count($all), 'good' => 0, 'attention' => 0, 'off' => 0];
foreach ($all as $row) {
	$row['page_name'] = trim((string) $row['page_name']);
	$title_length = mb_strlen(trim((string) $row['meta_title']));
	$desc_length = mb_strlen(trim((string) $row['meta_description']));
	$row['title_length'] = $title_length;
	$row['desc_length'] = $desc_length;
	$row['problems'] = [];
	if ($desc_length < TAG_DESC_MIN) {
		$row['problems']['short_desc'] = $desc_length === 0 ? 'No description' : 'Description is only ' . $desc_length . ' characters';
	} elseif ($desc_length > TAG_DESC_MAX) {
		$row['problems']['long_desc'] = 'Description is ' . $desc_length . ' characters, it will be cut';
	}
	if ($title_length > TAG_TITLE_MAX) {
		$row['problems']['long_title'] = 'Title is ' . $title_length . ' characters, it may be cut';
	} elseif ($title_length === 0) {
		$row['problems']['long_title'] = 'No title';
	}
	if (trim((string) $row['meta_og_img']) === '') {
		$row['problems']['no_image'] = 'No sharing picture';
	}
	if ($row['is_active'] !== '1') {
		$counts['off']++;
	}
	$counts[$row['problems'] ? 'attention' : 'good']++;
	if ($f_check !== '' && !isset($row['problems'][$f_check])) {
		continue;
	}
	if ($f_search !== '' && mb_stripos($row['page_name'] . ' ' . $row['page_slug'] . ' ' . $row['meta_title'] . ' ' . $row['meta_description'], $f_search) === false) {
		continue;
	}
	$tags[] = $row;
}

function render_tag_list($tags, $f_search = '', $f_check = '')
{
	ob_start();
	?>
	<div class="dre-h">
		<h3><?= count($tags) ?> <?= count($tags) === 1 ? 'page' : 'pages' ?><?= $f_search !== '' || $f_check !== '' ? ' <small>filtered</small>' : '' ?></h3>
	</div>

	<?php if (!$tags): ?>
		<div class="dre-empty">
			<i class="bi bi-tags" style="font-size: 2rem;"></i><br>
			Nothing to show. <a href="<?= BASE_URL ?>manage-tags.php" class="dre-reset-filter">Show all pages</a>
		</div>
	<?php else: ?>
		<div class="dre-proj-table" role="table" aria-label="SEO meta tags">
			<div class="dre-proj-row dre-proj-head" role="row">
				<div role="columnheader">Page</div>
				<div role="columnheader">In a search result</div>
				<div role="columnheader">Checks</div>
				<div role="columnheader">Status</div>
				<div role="columnheader"><span class="sr-only">Actions</span></div>
			</div>
			<?php foreach ($tags as $row):
				$id = (int) $row['id'];
				$is_on = $row['is_active'] === '1';
				$image = trim((string) $row['meta_og_img']);
				$edit_url = BASE_URL . 'edit-tags.php?edit=' . base64_encode($id);
			?>
				<div class="dre-proj-row<?= $is_on ? '' : ' is-hidden' ?>" role="row" data-id="<?= $id ?>">
					<div class="dre-proj-main" role="cell">
						<a class="dre-proj-thumb" href="<?= set_e($edit_url) ?>" tabindex="-1" aria-hidden="true">
							<?php if ($image !== ''): ?>
								<img src="<?= BASE_URL ?>assets/media/meta/<?= set_e(rawurlencode($image)) ?>" alt="" loading="lazy" onerror="this.remove()">
							<?php endif; ?>
							<i class="bi bi-image"></i>
						</a>
						<div>
							<a class="dre-proj-name" href="<?= set_e($edit_url) ?>"><?= $row['page_name'] !== '' ? set_e($row['page_name']) : 'Page without a name' ?></a>
							<span class="dre-proj-sub"><i class="bi bi-link-45deg"></i> /<?= set_e($row['page_slug']) ?></span>
						</div>
					</div>
					<div class="dre-tag-result" role="cell">
						<strong><?= $row['title_length'] > 0 ? set_e(mb_strimwidth((string) $row['meta_title'], 0, 64, '...')) : '<em>No title</em>' ?></strong>
						<span><?= $row['desc_length'] > 0 ? set_e(mb_strimwidth((string) $row['meta_description'], 0, 150, '...')) : '<em>No description</em>' ?></span>
					</div>
					<div class="dre-tag-checks" role="cell">
						<?php if (!$row['problems']): ?>
							<span class="dre-tag-ok"><i class="bi bi-check2-circle"></i> Looks good</span>
						<?php else: ?>
							<?php foreach ($row['problems'] as $problem): ?>
								<span class="dre-proj-missing"><i class="bi bi-exclamation-triangle"></i> <?= set_e($problem) ?></span>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<div class="dre-proj-status" role="cell">
						<form method="post" action="<?= BASE_URL ?>manage-tags.php" class="dre-tag-status-form">
							<input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf'] ?? '') ?>">
							<input type="hidden" name="tag_id" value="<?= $id ?>">
							<button type="submit" name="tag_action" value="<?= $is_on ? 'hide' : 'show' ?>" class="dre-switch<?= $is_on ? ' is-on' : '' ?>" role="switch" aria-checked="<?= $is_on ? 'true' : 'false' ?>" title="<?= $is_on ? 'Used on the website. Click to switch off.' : 'Switched off. Click to use on the website.' ?>">
								<i></i><span><?= $is_on ? 'In use' : 'Off' ?></span>
							</button>
						</form>
						<span class="dre-proj-sync"><i class="bi bi-clock-history"></i> Changed <?= strtotime((string) $row['tstp']) > 0 ? date('d M Y', strtotime($row['tstp'])) : 'not known' ?></span>
					</div>
					<div class="dre-proj-actions" role="cell">
						<a class="dre-icon-btn" href="<?= set_e($edit_url) ?>" title="Edit" aria-label="Edit the tags of <?= set_e($row['page_name']) ?>"><i class="bi bi-pencil"></i></a>
						<a class="dre-icon-btn" href="<?= set_e(BASE_URL_HOME . $row['page_slug']) ?>" target="_blank" rel="noopener" title="View the page" aria-label="View <?= set_e($row['page_name']) ?> on the website"><i class="bi bi-box-arrow-up-right"></i></a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php
	return ob_get_clean();
}

// ---------------------------------------------------------------
// AJAX Search / Filter Endpoint
// ---------------------------------------------------------------
if (isset($_GET['ajax'])) {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode([
		'status' => 'success',
		'count' => count($tags),
		'html' => render_tag_list($tags, $f_search, $f_check),
		'has_filters' => ($f_search !== '' || $f_check !== '')
	]);
	exit;
}

$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : '';
$notice = ['saved' => 'The changes were saved.', 'on' => 'These tags are now used on the website.', 'off' => 'These tags are switched off: the page has no title or description of its own now.'][$done] ?? $notice;

include('includes/header.php');
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
							<!--begin::SEO tags-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-articles.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-settings.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-articles dre-settings">

								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1>SEO meta tags</h1>
										<p>The title, description and sharing picture search engines and social networks use for each main page.</p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= $counts['total'] ?></strong><span>Pages</span></div>
										<div><strong><?= $counts['good'] ?></strong><span>Look good</span></div>
										<div><strong><?= $counts['attention'] ?></strong><span>Need attention</span></div>
										<div><strong><?= $counts['off'] ?></strong><span>Switched off</span></div>
									</div>
								</div>

								<?php if ($notice !== ''): ?>
									<div class="dre-leads-notice" role="status" id="tagNotice"><i class="bi bi-info-circle"></i> <?= set_e($notice) ?></div>
								<?php endif; ?>

								<form method="get" action="<?= BASE_URL ?>manage-tags.php" class="dre-glass dre-leads-filter dre-proj-filter" id="tagFilter">
									<label class="dre-leads-search">
										<span>Search</span>
										<i class="bi bi-search"></i>
										<input type="search" name="q" id="tagSearchInput" value="<?= set_e($f_search) ?>" placeholder="Page, title or description" autocomplete="off">
									</label>
									<label>
										<span>Show pages where</span>
										<select name="check" id="tagCheckSelect">
											<option value="">Anything</option>
											<?php foreach ($tag_checks as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_check === $key ? 'selected' : '' ?>><?= set_e($label) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<div class="dre-leads-filter-buttons">
										<button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-funnel"></i> Apply</button>
										<a class="dre-btn" href="<?= BASE_URL ?>manage-tags.php" id="tagClearBtn" style="<?= ($f_search !== '' || $f_check !== '') ? '' : 'display:none;' ?>">Clear</a>
									</div>
								</form>

								<div class="dre-glass dre-leads-list" id="tagListContainer" style="position:relative; transition: opacity 0.2s ease;">
									<?= render_tag_list($tags, $f_search, $f_check) ?>
								</div>
							</div>

							<script>
								(function () {
									var notice = document.getElementById('tagNotice');
									if (notice) {
										setTimeout(function () { notice.style.display = 'none'; }, 8000);
									}

									var form = document.getElementById('tagFilter');
									if (!form) return;

									var input = document.getElementById('tagSearchInput');
									var select = document.getElementById('tagCheckSelect');
									var clearBtn = document.getElementById('tagClearBtn');
									var container = document.getElementById('tagListContainer');
									var timer = null;
									var abortCtrl = null;

									function applyFilter(pushHistory) {
										if (abortCtrl) {
											abortCtrl.abort();
										}
										abortCtrl = new AbortController();

										var q = input ? input.value.trim() : '';
										var check = select ? select.value : '';

										if (clearBtn) {
											clearBtn.style.display = (q !== '' || check !== '') ? '' : 'none';
										}

										if (container) {
											container.style.opacity = '0.5';
										}

										var params = new URLSearchParams();
										if (q !== '') params.set('q', q);
										if (check !== '') params.set('check', check);

										var queryStr = params.toString();
										var newUrl = window.location.pathname + (queryStr ? '?' + queryStr : '');
										if (pushHistory) {
											window.history.pushState({}, '', newUrl);
										} else {
											window.history.replaceState({}, '', newUrl);
										}

										params.set('ajax', '1');
										var fetchUrl = window.location.pathname + '?' + params.toString();

										fetch(fetchUrl, {
											signal: abortCtrl.signal,
											headers: {
												'X-Requested-With': 'XMLHttpRequest'
											}
										})
										.then(function (res) {
											if (!res.ok) throw new Error('Network error');
											return res.json();
										})
										.then(function (data) {
											if (data && data.html && container) {
												container.innerHTML = data.html;
											}
										})
										.catch(function (err) {
											if (err.name !== 'AbortError') {
												console.error('Tag filter error:', err);
											}
										})
										.finally(function () {
											if (container) {
												container.style.opacity = '1';
											}
										});
									}

									if (input) {
										input.addEventListener('input', function () {
											clearTimeout(timer);
											timer = setTimeout(function () {
												applyFilter(false);
											}, 250);
										});
										input.addEventListener('keydown', function (e) {
											if (e.key === 'Enter') {
												e.preventDefault();
												clearTimeout(timer);
												applyFilter(true);
											}
										});
									}

									if (select) {
										select.addEventListener('change', function () {
											clearTimeout(timer);
											applyFilter(true);
										});
									}

									form.addEventListener('submit', function (e) {
										e.preventDefault();
										clearTimeout(timer);
										applyFilter(true);
									});

									if (clearBtn) {
										clearBtn.addEventListener('click', function (e) {
											e.preventDefault();
											if (input) input.value = '';
											if (select) select.value = '';
											clearTimeout(timer);
											applyFilter(true);
										});
									}

									document.addEventListener('click', function (e) {
										var resetLink = e.target.closest('.dre-reset-filter');
										if (resetLink) {
											e.preventDefault();
											if (input) input.value = '';
											if (select) select.value = '';
											clearTimeout(timer);
											applyFilter(true);
										}
									});

									window.addEventListener('popstate', function () {
										var urlParams = new URLSearchParams(window.location.search);
										if (input) input.value = urlParams.get('q') || '';
										if (select) select.value = urlParams.get('check') || '';
										applyFilter(false);
									});

									// Instant AJAX toggle for switches
									document.addEventListener('click', function (e) {
										var switchBtn = e.target.closest('.dre-tag-status-form button.dre-switch');
										if (!switchBtn) return;

										e.preventDefault();
										var switchForm = switchBtn.closest('form');
										if (!switchForm) return;

										var formData = new FormData(switchForm);
										formData.append('ajax', '1');
										formData.append('tag_action', switchBtn.value);

										switchBtn.disabled = true;
										switchBtn.style.opacity = '0.6';

										fetch(switchForm.action || window.location.pathname, {
											method: 'POST',
											body: formData,
											headers: {
												'X-Requested-With': 'XMLHttpRequest'
											}
										})
										.then(function (res) { return res.json(); })
										.then(function (res) {
											if (res.status === 'success') {
												var row = switchBtn.closest('.dre-proj-row');
												var span = switchBtn.querySelector('span');
												if (res.is_on) {
													switchBtn.classList.add('is-on');
													switchBtn.value = 'hide';
													switchBtn.setAttribute('aria-checked', 'true');
													switchBtn.setAttribute('title', 'Used on the website. Click to switch off.');
													if (span) span.textContent = 'In use';
													if (row) row.classList.remove('is-hidden');
												} else {
													switchBtn.classList.remove('is-on');
													switchBtn.value = 'show';
													switchBtn.setAttribute('aria-checked', 'false');
													switchBtn.setAttribute('title', 'Switched off. Click to use on the website.');
													if (span) span.textContent = 'Off';
													if (row) row.classList.add('is-hidden');
												}
											} else {
												alert(res.message || 'Error updating status');
											}
										})
										.catch(function (err) {
											console.error('Status toggle error:', err);
											switchForm.submit();
										})
										.finally(function () {
											switchBtn.disabled = false;
											switchBtn.style.opacity = '';
										});
									});
								})();
							</script>
							<!--end::SEO tags-->

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