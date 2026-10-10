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

const GUIDE_FOLDER = 'assets/media/guide/';

function guide_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function guide_query($conn, $sql, array $params = [])
{
	$stmt = mysqli_prepare($conn, $sql);
	if (!$stmt) {
		error_log('Guide query failed: ' . mysqli_error($conn));
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
		error_log('Guide query failed: ' . mysqli_stmt_error($stmt));
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

// Each guide page of the website is its own file that reads one guide by its number.
// This finds them: guide id => address of its page, for example 15 => "buyer-guide".
function guide_pages()
{
	$pages = [];
	foreach (glob(dirname(__DIR__) . '/*guide*.php') ?: [] as $file) {
		if (preg_match('/tbl_guide\W+guide_id\s*=\s*(\d+)/', (string) file_get_contents($file, false, null, 0, 4000), $match)) {
			$pages[(int) $match[1]] = basename($file, '.php');
		}
	}
	return $pages;
}

// ---------------------------------------------------------------
// Changes (POST only, with a token)
// ---------------------------------------------------------------
$guide_pages = guide_pages();
$notice = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['guide_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$action = is_string($_POST['guide_action']) ? $_POST['guide_action'] : '';
	$id = (int) ($_POST['guide_id'] ?? 0);
	$guide = $token_ok && $id > 0 ? guide_query($conn, "SELECT * FROM tbl_guide WHERE guide_id = ?", [$id]) : [];
	$done = '';

	if ($guide && ($action === 'show' || $action === 'hide')) {
		$value = $action === 'show' ? '1' : '2';
		if ($guide[0]['is_active'] !== $value) {
			guide_query($conn, "UPDATE tbl_guide SET is_active = ? WHERE guide_id = ?", [$value, $id]);
			$logger->log('UPDATE', 'tbl_guide', $id, ['is_active' => $guide[0]['is_active']], ['is_active' => $value], 'Guide ' . ($value === '1' ? 'shown on website: ' : 'hidden from website: ') . $guide[0]['guide_heading']);
		}
		$done = $action === 'show' ? 'shown' : 'hidden';
	} elseif ($guide && $action === 'delete') {
		if (isset($guide_pages[$id])) {
			// A page of the website reads this guide by its number, so it would be left empty.
			$done = 'inuse';
		} else {
			$files = array_column(guide_query($conn, "SELECT guide_image FROM tbl_guide_img WHERE guide_id = ?", [$id]) ?: [], 'guide_image');
			$old_data = $logger->getOldRecordData('tbl_guide', $id);
			$logger->log('DELETE', 'tbl_guide', $id, $old_data, null, 'Guide deleted: ' . $guide[0]['guide_heading']);
			$logger->archiveDeletedRecord('tbl_guide', $id, $old_data);
			// Deleting the guide removes its steps too (the database does that); their pictures are removed here.
			if (guide_query($conn, "DELETE FROM tbl_guide WHERE guide_id = ?", [$id])) {
				foreach ($files as $name) {
					$name = basename(trim((string) $name));
					if ($name !== '' && is_file(GUIDE_FOLDER . $name)) {
						@unlink(GUIDE_FOLDER . $name);
					}
				}
			}
			$done = 'deleted';
		}
	}

	if ($done !== '') {
		header('Location: ' . BASE_URL . 'manage-guide.php?done=' . $done);
		exit;
	}
	$notice = 'Nothing was changed. Please reload the page and try again.';
}

$guides = guide_query($conn, "SELECT g.*, (SELECT COUNT(*) FROM tbl_guide_img i WHERE i.guide_id = g.guide_id) AS steps, (SELECT COUNT(*) FROM tbl_guide_img i WHERE i.guide_id = g.guide_id AND i.is_active = '1') AS live_steps FROM tbl_guide g ORDER BY g.guide_id") ?: [];
$live = count(array_filter($guides, function ($row) { return $row['is_active'] === '1'; }));
$total_steps = array_sum(array_column($guides, 'steps'));

$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : '';
$notice = [
	'added' => 'The guide was added.', 'saved' => 'The changes were saved.',
	'shown' => 'The guide is now live on the website.', 'hidden' => 'The guide is now hidden from the website.',
	'deleted' => 'The guide and its steps were deleted. A copy of the record is kept in the delete log.',
	'inuse' => 'This guide was not deleted: a page of the website shows it. Hide it instead.',
][$done] ?? $notice;
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
							<!--begin::Guides-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-articles.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-guides.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-articles dre-guides">

								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1>Guides</h1>
										<p>The step-by-step guides for buyers, sellers, tenants and landlords.</p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= $live ?></strong><span>Live</span></div>
										<div><strong><?= count($guides) - $live ?></strong><span>Hidden</span></div>
										<div><strong><?= (int) $total_steps ?></strong><span>Steps in all</span></div>
									</div>
									<div class="dre-actions">
										<a class="dre-primary" href="<?= BASE_URL ?>add-guide.php"><i class="bi bi-plus-lg"></i> Add guide</a>
										<a href="<?= BASE_URL ?>export-guide.php"><i class="bi bi-download"></i> Export XLS</a>
									</div>
								</div>

								<?php if ($notice !== ''): ?>
									<div class="dre-leads-notice" role="status" id="guideNotice"><i class="bi bi-info-circle"></i> <?= guide_e($notice) ?></div>
								<?php endif; ?>

								<div class="dre-glass dre-leads-list">
									<div class="dre-h">
										<h3><?= count($guides) ?> <?= count($guides) === 1 ? 'guide' : 'guides' ?></h3>
									</div>

									<?php if (!$guides): ?>
										<div class="dre-empty">
											<i class="bi bi-compass" style="font-size: 2rem;"></i><br>
											No guides yet. <a href="<?= BASE_URL ?>add-guide.php">Add the first one</a>
										</div>
									<?php else: ?>
										<div class="dre-proj-table" role="table" aria-label="Guides">
											<div class="dre-proj-row dre-proj-head" role="row">
												<div role="columnheader">Guide</div>
												<div role="columnheader">Introduction</div>
												<div role="columnheader">Steps</div>
												<div role="columnheader">Status</div>
												<div role="columnheader"><span class="sr-only">Actions</span></div>
											</div>
											<?php foreach ($guides as $row):
												$id = (int) $row['guide_id'];
												$is_live = $row['is_active'] === '1';
												$page = $guide_pages[$id] ?? '';
												$intro = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $row['guide_short_description']), ENT_QUOTES, 'UTF-8')));
												$edit_url = BASE_URL . 'edit-guide.php?edit=' . base64_encode($id);
												$missing = [];
												if ($intro === '') {
													$missing[] = 'introduction';
												}
												if (trim((string) $row['meta_description']) === '') {
													$missing[] = 'meta description';
												}
											?>
												<div class="dre-proj-row<?= $is_live ? '' : ' is-hidden' ?>" role="row">
													<div class="dre-proj-main" role="cell">
														<a class="dre-proj-thumb" href="<?= guide_e($edit_url) ?>" tabindex="-1" aria-hidden="true"><i class="bi bi-compass"></i></a>
														<div>
															<a class="dre-proj-name" href="<?= guide_e($edit_url) ?>"><?= trim((string) $row['guide_heading']) !== '' ? guide_e($row['guide_heading']) : 'Untitled guide' ?></a>
															<?php if ($page !== ''): ?>
																<span class="dre-proj-sub"><i class="bi bi-link-45deg"></i> /<?= guide_e($page) ?></span>
															<?php else: ?>
																<span class="dre-proj-sub dre-guide-nopage"><i class="bi bi-exclamation-triangle"></i> No page of the website shows this guide</span>
															<?php endif; ?>
														</div>
													</div>
													<div class="dre-art-summary" role="cell">
														<span><?= $intro !== '' ? guide_e(mb_strimwidth($intro, 0, 130, '...')) : '<em>No introduction</em>' ?></span>
														<?php if ($missing): ?>
															<span class="dre-proj-missing"><i class="bi bi-exclamation-triangle"></i> Missing: <?= guide_e(implode(', ', $missing)) ?></span>
														<?php endif; ?>
													</div>
													<div class="dre-proj-facts" role="cell">
														<strong><a href="<?= guide_e($edit_url) ?>#sec-steps"><?= (int) $row['steps'] ?> <?= (int) $row['steps'] === 1 ? 'step' : 'steps' ?></a></strong>
														<span><?= (int) $row['steps'] === 0 ? 'Nothing to read yet' : ((int) $row['live_steps'] === (int) $row['steps'] ? 'All live' : (int) $row['live_steps'] . ' live, ' . ((int) $row['steps'] - (int) $row['live_steps']) . ' hidden') ?></span>
													</div>
													<div class="dre-proj-status" role="cell">
														<form method="post" action="<?= BASE_URL ?>manage-guide.php">
															<input type="hidden" name="csrf" value="<?= guide_e($_SESSION['admin_csrf']) ?>">
															<input type="hidden" name="guide_id" value="<?= $id ?>">
															<button type="submit" name="guide_action" value="<?= $is_live ? 'hide' : 'show' ?>" class="dre-switch<?= $is_live ? ' is-on' : '' ?>" role="switch" aria-checked="<?= $is_live ? 'true' : 'false' ?>" title="<?= $is_live ? 'Live on the website. Click to hide.' : 'Hidden from the website. Click to show.' ?>">
																<i></i><span><?= $is_live ? 'Live' : 'Hidden' ?></span>
															</button>
														</form>
														<span class="dre-proj-sync"><i class="bi bi-clock-history"></i> Changed <?= strtotime((string) $row['tstp']) > 0 ? date('d M Y', strtotime($row['tstp'])) : 'not known' ?></span>
													</div>
													<div class="dre-proj-actions" role="cell">
														<a class="dre-icon-btn" href="<?= guide_e($edit_url) ?>" title="Edit" aria-label="Edit <?= guide_e($row['guide_heading']) ?>"><i class="bi bi-pencil"></i></a>
														<?php if ($page !== ''): ?>
															<a class="dre-icon-btn" href="<?= guide_e(BASE_URL_HOME . $page) ?>" target="_blank" rel="noopener" title="View on the website" aria-label="View <?= guide_e($row['guide_heading']) ?> on the website"><i class="bi bi-box-arrow-up-right"></i></a>
														<?php endif; ?>
														<div class="dropdown">
															<button type="button" class="dre-icon-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More" aria-label="More actions for <?= guide_e($row['guide_heading']) ?>"><i class="bi bi-three-dots"></i></button>
															<div class="dropdown-menu dropdown-menu-right">
																<a class="dropdown-item" href="<?= BASE_URL ?>add-guide-image.php?guide_id=<?= $id ?>"><i class="bi bi-plus-lg mr-2"></i>Add a step</a>
																<div class="dropdown-divider"></div>
																<form method="post" action="<?= BASE_URL ?>manage-guide.php" class="dre-guide-delete" data-name="<?= guide_e($row['guide_heading']) ?>" data-page="<?= guide_e($page) ?>" data-steps="<?= (int) $row['steps'] ?>">
																	<input type="hidden" name="csrf" value="<?= guide_e($_SESSION['admin_csrf']) ?>">
																	<input type="hidden" name="guide_id" value="<?= $id ?>">
																	<button type="submit" name="guide_action" value="delete" class="dropdown-item text-danger"><i class="bi bi-trash3 mr-2"></i>Delete</button>
																</form>
															</div>
														</div>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>

							<script>
								// Confirm before deleting, and let the message fade out.
								(function () {
									document.querySelectorAll('.dre-guide-delete').forEach(function (form) {
										form.addEventListener('submit', function (event) {
											var page = form.getAttribute('data-page');
											var steps = parseInt(form.getAttribute('data-steps'), 10) || 0;
											if (page !== '') {
												event.preventDefault();
												window.alert('"' + form.getAttribute('data-name') + '" cannot be deleted: the page /' + page + ' on the website shows it. Hide it instead.');
											} else if (!window.confirm('Delete "' + form.getAttribute('data-name') + '"' + (steps > 0 ? ' and its ' + steps + (steps === 1 ? ' step' : ' steps') : '') + '? A copy of the guide record is kept in the delete log.')) {
												event.preventDefault();
											}
										});
									});
									var notice = document.getElementById('guideNotice');
									if (notice) {
										setTimeout(function () { notice.style.display = 'none'; }, 8000);
									}
								})();
							</script>
							<!--end::Guides-->

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