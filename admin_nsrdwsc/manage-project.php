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

include('includes/project-query.php');

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

include('includes/project-list.php');

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
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=5" rel="stylesheet" type="text/css" />
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

								<!-- Search and filters: one row (search, status, developer, sort); the rest under "More filters" -->
								<?php
								// Filters inside "More filters", and the chips that show every active filter with its own remove link
								$adv_active = count(array_filter([$f_comm > 0, $f_missing !== '', $f_sync !== '', $f_seo !== '', $f_source !== '', $f_per_page !== 25, $f_home !== '']));
								$filter_chips = [];
								if ($f_search !== '') $filter_chips[] = ['Search: “' . $f_search . '”', ['q' => '']];
								if ($f_status !== '') $filter_chips[] = [$f_status === 'active' ? 'Live' : 'Hidden', ['status' => '']];
								foreach ($developer_options as $option) {
									if ($f_dev === (int) $option['dev_id']) $filter_chips[] = ['Developer: ' . $option['dev_heading'], ['dev' => '']];
								}
								foreach ($community_options as $option) {
									if ($f_comm === (int) $option['comm_id']) $filter_chips[] = ['Community: ' . $option['comm_heading'], ['comm' => '']];
								}
								if ($f_missing !== '') $filter_chips[] = ['Missing: ' . $proj_missing[$f_missing], ['missing' => '']];
								if ($f_sync !== '') $filter_chips[] = [$f_sync === 'pushed' ? 'In the app' : 'Not in the app', ['sync' => '']];
								if ($has_seo && $f_seo !== '' && isset($seo_levels[$f_seo])) $filter_chips[] = ['SEO: ' . $seo_levels[$f_seo][0], ['seo' => '']];
								if ($f_source !== '') $filter_chips[] = ['From GenieMap', ['source' => '']];
								if ($f_home !== '') $filter_chips[] = ['On the homepage', ['home' => '']];
								$clear_all = ['q' => '', 'status' => '', 'home' => '', 'sync' => '', 'source' => '', 'seo' => '', 'dev' => '', 'comm' => '', 'missing' => '', 'sort' => '', 'per' => '', 'page' => ''];
								?>
								<form method="get" action="<?= BASE_URL ?>manage-project.php" class="dre-glass dre-leads-filter dre-proj-filter<?= $adv_active ? ' is-open' : '' ?>" id="projFilter">
									<?php if ($f_type): ?><input type="hidden" name="type" value="<?= $f_type ?>"><?php endif; ?>
									<label class="dre-leads-search">
										<span>Search</span>
										<i class="bi bi-search"></i>
										<input type="search" name="q" value="<?= proj_e($f_search) ?>" placeholder="Project, location, developer or community" autocomplete="off">
									</label>
									<label class="dre-pf-main">
										<span>Website status</span>
										<select name="status">
											<option value="">Any</option>
											<option value="active" <?= $f_status === 'active' ? 'selected' : '' ?>>Live</option>
											<option value="inactive" <?= $f_status === 'inactive' ? 'selected' : '' ?>>Hidden</option>
										</select>
									</label>
									<label class="dre-pf-main">
										<span>Developer</span>
										<select name="dev">
											<option value="">Any</option>
											<?php foreach ($developer_options as $option): ?>
												<option value="<?= (int) $option['dev_id'] ?>" <?= $f_dev === (int) $option['dev_id'] ? 'selected' : '' ?>><?= proj_e($option['dev_heading']) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label class="dre-pf-main">
										<span>Sort by</span>
										<select name="sort">
											<?php foreach ($proj_sorts as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_sort === $key ? 'selected' : '' ?>><?= proj_e($label) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<div class="dre-pf-actions">
										<button type="button" class="dre-btn dre-pf-toggle" id="projFilterToggle" aria-expanded="<?= $adv_active ? 'true' : 'false' ?>" aria-controls="projFilter">
											<i class="bi bi-sliders"></i> <span class="dre-pf-more-label">More filters</span><span class="dre-pf-all-label">Filters</span>
											<?php $badge = $adv_active + ($f_status !== '' ? 1 : 0) + ($f_dev > 0 ? 1 : 0); ?>
											<b class="dre-pf-count dre-pf-count-adv"<?= $adv_active ? '' : ' hidden' ?>><?= $adv_active ?></b>
											<b class="dre-pf-count dre-pf-count-all"<?= $badge ? '' : ' hidden' ?>><?= $badge ?></b>
										</button>
										<button type="submit" class="dre-btn dre-btn-main" aria-label="Apply filters"><i class="bi bi-search"></i><span class="dre-pf-apply-label"> Apply</span></button>
									</div>

									<!-- More filters (open when one of them is in use) -->
									<label class="dre-pf-adv">
										<span>Community</span>
										<select name="comm">
											<option value="">Any</option>
											<?php foreach ($community_options as $option): ?>
												<option value="<?= (int) $option['comm_id'] ?>" <?= $f_comm === (int) $option['comm_id'] ? 'selected' : '' ?>><?= proj_e($option['comm_heading']) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label class="dre-pf-adv">
										<span>Missing</span>
										<select name="missing">
											<option value="">Nothing selected</option>
											<?php foreach ($proj_missing as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_missing === $key ? 'selected' : '' ?>><?= proj_e($label) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label class="dre-pf-adv">
										<span>App</span>
										<select name="sync">
											<option value="">Any</option>
											<option value="pending" <?= $f_sync === 'pending' ? 'selected' : '' ?>>Not in the app</option>
											<option value="pushed" <?= $f_sync === 'pushed' ? 'selected' : '' ?>>In the app</option>
										</select>
									</label>
									<?php if ($has_seo): ?>
										<label class="dre-pf-adv">
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
										<label class="dre-pf-adv">
											<span>Source</span>
											<select name="source">
												<option value="">Any</option>
												<option value="geniemap" <?= $f_source !== '' ? 'selected' : '' ?>>Imported from GenieMap</option>
											</select>
										</label>
									<?php endif; ?>
									<label class="dre-pf-adv">
										<span>Per page</span>
										<select name="per">
											<?php foreach ([25, 50, 100] as $option): ?>
												<option value="<?= $option ?>" <?= $f_per_page === $option ? 'selected' : '' ?>><?= $option ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label class="dre-pf-adv dre-proj-check">
										<input type="checkbox" name="home" value="1" <?= $f_home !== '' ? 'checked' : '' ?>> On the homepage only
									</label>
								</form>

								<?php if ($filter_chips): ?>
									<!-- Active filters: each one can be removed on its own -->
									<div class="dre-pf-chips" aria-label="Active filters">
										<?php foreach ($filter_chips as [$label, $remove]): ?>
											<a class="dre-pf-chip" href="<?= proj_e(proj_url($remove + ['page' => ''])) ?>" title="Remove this filter"><?= proj_e($label) ?> <i class="bi bi-x-lg"></i></a>
										<?php endforeach; ?>
										<a class="dre-pf-clear" href="<?= proj_e(proj_url($clear_all)) ?>">Clear all</a>
									</div>
								<?php endif; ?>


								<!-- The form the ticked projects belong to. Its buttons choose what happens. -->
								<form method="post" action="<?= proj_e(proj_url()) ?>" id="projBulkForm">
									<input type="hidden" name="csrf" value="<?= proj_e($_SESSION['admin_csrf']) ?>">
								</form>

								<!-- The list: shared with ajax/manage-project-search.php so live search can swap it in whole. -->
								<?php include('includes/project-results.php'); ?>

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
								// Delegated on the document so it keeps working after the live search
								// below swaps in a fresh #projResults.
								(function () {
									var bar = document.getElementById('projBulkBar');
									var count = document.getElementById('projBulkCount');

									function refresh() {
										var boxes = Array.prototype.slice.call(document.querySelectorAll('.dre-proj-box'));
										var all = document.getElementById('projSelectAll');
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
										} else if (event.target.id === 'projSelectAll') {
											document.querySelectorAll('.dre-proj-box').forEach(function (box) { box.checked = event.target.checked; });
											refresh();
										}
									});
									document.getElementById('projBulkClear').addEventListener('click', function () {
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
										var form = event.target.closest('.dre-proj-delete');
										if (!form) return;
										if (!window.confirm('Delete "' + form.getAttribute('data-name') + '"? Its gallery files, brochure and plans are removed too. A copy of the record is kept in the delete log.')) {
											event.preventDefault();
										}
									});
									refresh();
									window.dreProjRefreshBulk = refresh;

									var notice = document.getElementById('projNotice');
									if (notice) {
										setTimeout(function () { notice.style.display = 'none'; }, 6000);
									}
								})();

								// "More filters" toggle: not inside #projResults, so it survives the live search below.
								(function () {
									var filterForm = document.getElementById('projFilter');
									var filterToggle = document.getElementById('projFilterToggle');
									if (!filterForm || !filterToggle) return;
									filterToggle.addEventListener('click', function () {
										var open = filterForm.classList.toggle('is-open');
										filterToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
									});
								})();

								// Real-time search/filter: typing or changing a filter re-queries the
								// table without a full page reload. Pagination links inside the result
								// stay AJAX too, so the whole list never needs a hard refresh.
								(function () {
									var form = document.getElementById('projFilter');
									if (!form) return;

									function load(page, pushUrl) {
										var params = new URLSearchParams(new FormData(form));
										params.set('page', page || 1);
										// base_url (from includes/header.php) is required here: the page sets
										// <base href="../../../">, so a bare relative URL would resolve outside
										// the admin folder entirely.
										fetch(base_url + 'ajax/manage-project-search.php?' + params.toString(), { credentials: 'same-origin' })
											.then(function (response) { return response.text(); })
											.then(function (html) {
												var current = document.getElementById('projResults');
												if (!current) return;
												current.outerHTML = html;
												if (window.dreProjRefreshBulk) window.dreProjRefreshBulk();
												if (pushUrl) {
													var query = params.toString();
													history.replaceState(null, '', base_url + 'manage-project.php' + (query ? '?' + query : ''));
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
										var link = event.target.closest('#projResults .dre-leads-pages a');
										if (!link) return;
										event.preventDefault();
										var page = new URL(link.href, location.href).searchParams.get('page') || 1;
										load(page, true);
									});
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
