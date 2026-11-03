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

include('includes/developer-query.php');

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

include('includes/developer-list.php');

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

								<?php include('includes/developer-results.php'); ?>


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
								// Delegated on the document so it keeps working after the live search
								// below swaps in a fresh #devResults.
								(function () {
									var bar = document.getElementById('devBulkBar');
									var count = document.getElementById('devBulkCount');

									function refresh() {
										var boxes = Array.prototype.slice.call(document.querySelectorAll('.dre-proj-box'));
										var all = document.getElementById('devSelectAll');
										var picked = boxes.filter(function (box) { return box.checked; }).length;
										count.textContent = picked;
										bar.hidden = picked === 0;
										if (all) {
											all.checked = picked > 0 && picked === boxes.length;
											all.indeterminate = picked > 0 && picked < boxes.length;
										}
									}

									document.addEventListener('change', function (event) {
										if (event.target.classList.contains('dre-proj-box')) {
											refresh();
										} else if (event.target.id === 'devSelectAll') {
											document.querySelectorAll('.dre-proj-box').forEach(function (box) { box.checked = event.target.checked; });
											refresh();
										}
									});
									document.getElementById('devBulkClear').addEventListener('click', function () {
										document.querySelectorAll('.dre-proj-box').forEach(function (box) { box.checked = false; });
										refresh();
									});
									bar.querySelectorAll('[data-confirm]').forEach(function (button) {
										button.addEventListener('click', function (event) {
											if (!window.confirm(button.getAttribute('data-confirm'))) {
												event.preventDefault();
											}
										});
									});
									document.addEventListener('submit', function (event) {
										var form = event.target.closest('.dre-dev-delete');
										if (!form) return;
										var projects = parseInt(form.getAttribute('data-projects'), 10) || 0;
										if (projects > 0) {
											event.preventDefault();
											window.alert('"' + form.getAttribute('data-name') + '" cannot be deleted: ' + projects + (projects === 1 ? ' project still uses' : ' projects still use') + ' it. Move those projects to another developer first, or hide this developer instead.');
										} else if (!window.confirm('Delete "' + form.getAttribute('data-name') + '"? Its images are removed too. A copy of the record is kept in the delete log.')) {
											event.preventDefault();
										}
									});
									refresh();
									window.dreDevRefreshBulk = refresh;

									var notice = document.getElementById('devNotice');
									if (notice) {
										setTimeout(function () { notice.style.display = 'none'; }, 8000);
									}
								})();

								// Real-time search/filter: typing or changing a filter re-queries the
								// table without a full page reload. Pagination links inside the result
								// stay AJAX too, so the whole list never needs a hard refresh.
								(function () {
									var form = document.querySelector('.dre-leads-filter');
									if (!form) return;

									function load(page, pushUrl) {
										var params = new URLSearchParams(new FormData(form));
										params.set('page', page || 1);
										// base_url (from includes/header.php) is required here: the page sets
										// <base href="../../../">, so a bare relative URL would resolve outside
										// the admin folder entirely.
										fetch(base_url + 'ajax/manage-developer-search.php?' + params.toString(), { credentials: 'same-origin' })
											.then(function (response) { return response.text(); })
											.then(function (html) {
												var current = document.getElementById('devResults');
												if (!current) return;
												current.outerHTML = html;
												if (window.dreDevRefreshBulk) window.dreDevRefreshBulk();
												if (pushUrl) {
													var query = params.toString();
													history.replaceState(null, '', base_url + 'manage-developer.php' + (query ? '?' + query : ''));
												}
											})
											.catch(function () { /* leave the table as it is; the Apply button still works */ });
									}

									var timer;
									var search = form.querySelector('input[name="q"]');
									if (search) {
										search.addEventListener('input', function () {
											clearTimeout(timer);
											timer = setTimeout(function () { load(1, true); }, 350);
										});
									}
									form.querySelectorAll('select, input[type="checkbox"]').forEach(function (field) {
										field.addEventListener('change', function () { load(1, true); });
									});
									form.addEventListener('submit', function (event) {
										event.preventDefault();
										clearTimeout(timer);
										load(1, true);
									});

									document.addEventListener('click', function (event) {
										var link = event.target.closest('#devResults .dre-leads-pages a');
										if (!link) return;
										event.preventDefault();
										var page = new URL(link.href, location.href).searchParams.get('page') || 1;
										load(page, true);
									});
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