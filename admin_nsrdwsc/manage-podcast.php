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

function pod_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function pod_query($conn, $sql, array $params = [])
{
	$stmt = mysqli_prepare($conn, $sql);
	if (!$stmt) {
		error_log('Podcast query failed: ' . mysqli_error($conn));
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
		error_log('Podcast query failed: ' . mysqli_stmt_error($stmt));
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

// The 11-character video id inside a YouTube address, or '' when it is not one.
function pod_youtube_id($url)
{
	$url = trim((string) $url);
	if (preg_match('~^https?://(?:www\.|m\.)?youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)([A-Za-z0-9_-]{11})~', $url, $match) || preg_match('~^https?://youtu\.be/([A-Za-z0-9_-]{11})~', $url, $match)) {
		return $match[1];
	}
	return '';
}

// ---------------------------------------------------------------
// Changes (POST only, with a token)
// ---------------------------------------------------------------
$notice = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['pod_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$action = is_string($_POST['pod_action']) ? $_POST['pod_action'] : '';
	$id = (int) ($_POST['pod_id'] ?? 0);
	$episode = $token_ok && $id > 0 ? pod_query($conn, "SELECT * FROM tbl_podcast WHERE podcast_id = ?", [$id]) : [];
	$done = '';

	if ($episode && ($action === 'show' || $action === 'hide')) {
		$value = $action === 'show' ? 1 : 0;
		if ((int) $episode[0]['is_active'] !== $value) {
			pod_query($conn, "UPDATE tbl_podcast SET is_active = ? WHERE podcast_id = ?", [$value, $id]);
			$logger->log('UPDATE', 'tbl_podcast', $id, ['is_active' => $episode[0]['is_active']], ['is_active' => $value], 'Podcast episode ' . ($value ? 'shown on website: ' : 'hidden from website: ') . $episode[0]['podcast_title']);
		}
		$done = $action === 'show' ? 'shown' : 'hidden';
	} elseif ($episode && $action === 'main') {
		// One main episode at a time: it is the large player at the top of the Podcast page.
		pod_query($conn, "UPDATE tbl_podcast SET is_main = 0 WHERE podcast_id <> ?", [$id]);
		pod_query($conn, "UPDATE tbl_podcast SET is_main = 1 WHERE podcast_id = ?", [$id]);
		$logger->log('UPDATE', 'tbl_podcast', $id, ['is_main' => $episode[0]['is_main']], ['is_main' => 1], 'Podcast main episode set: ' . $episode[0]['podcast_title']);
		$done = 'main';
	} elseif ($episode && $action === 'delete') {
		$old_data = $logger->getOldRecordData('tbl_podcast', $id);
		$logger->log('DELETE', 'tbl_podcast', $id, $old_data, null, 'Podcast episode deleted: ' . $episode[0]['podcast_title']);
		$logger->archiveDeletedRecord('tbl_podcast', $id, $old_data);
		if (pod_query($conn, "DELETE FROM tbl_podcast WHERE podcast_id = ?", [$id])) {
			// Files are removed only when a name is stored and the file is really there.
			foreach ([['assets/media/podcast/thumb/', 'podcast_thumb'], ['assets/media/podcast/video/', 'video_file']] as $file) {
				$name = basename(trim((string) $episode[0][$file[1]]));
				if ($name !== '' && is_file($file[0] . $name)) {
					@unlink($file[0] . $name);
				}
			}
		}
		$done = 'deleted';
	}

	if ($done !== '') {
		header('Location: ' . BASE_URL . 'manage-podcast.php?done=' . $done);
		exit;
	}
	$notice = 'Nothing was changed. Please reload the page and try again.';
}

// ---------------------------------------------------------------
// The list, in the order the website shows it
// ---------------------------------------------------------------
$episodes = pod_query($conn, "SELECT * FROM tbl_podcast ORDER BY is_active = 1 DESC, CASE WHEN is_main = 1 THEN 0 ELSE 1 END, order_no ASC, podcast_id DESC") ?: [];
$live = 0;
$main_title = '';
foreach ($episodes as $row) {
	if ((int) $row['is_active'] === 1) {
		$live++;
		if ((int) $row['is_main'] === 1 && $main_title === '') {
			$main_title = (string) $row['podcast_title'];
		}
	}
}

// Messages after an action
$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : (is_string($_GET['success'] ?? null) ? $_GET['success'] : '');
$notice = [
	'added' => 'The episode was added.', 'saved' => 'The changes were saved.', 'updated' => 'The changes were saved.',
	'shown' => 'The episode is now live on the website.', 'hidden' => 'The episode is now hidden from the website.',
	'main' => 'This is now the main episode at the top of the Podcast page.',
	'deleted' => 'The episode was deleted. A copy of the record is kept in the delete log.',
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

							<!--begin::Podcast-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-articles.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-podcast.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-articles dre-podcast">

								<!-- Title, numbers, main actions -->
								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1>Podcast</h1>
										<p>Episodes on the Podcast page. The main episode is the large player at the top.</p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= $live ?></strong><span>Live</span></div>
										<div><strong><?= count($episodes) - $live ?></strong><span>Hidden</span></div>
										<div class="dre-pod-main-stat"><strong><?= $main_title !== '' ? pod_e(mb_strimwidth($main_title, 0, 34, '...')) : 'None' ?></strong><span>Main episode</span></div>
									</div>
									<div class="dre-actions">
										<a class="dre-primary" href="<?= BASE_URL ?>add-podcast.php"><i class="bi bi-plus-lg"></i> Add episode</a>
										<a href="<?= pod_e(BASE_URL_HOME) ?>podcast" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View the page</a>
									</div>
								</div>

								<?php if ($notice !== ''): ?>
									<div class="dre-leads-notice" role="status" id="podNotice"><i class="bi bi-info-circle"></i> <?= pod_e($notice) ?></div>
								<?php endif; ?>
								<?php if ($live > 0 && $main_title === ''): ?>
									<div class="dre-leads-notice dre-pod-warning" role="status"><i class="bi bi-exclamation-triangle"></i> No live episode is marked as the main one, so the large player at the top of the Podcast page is empty. Use &ldquo;Make main episode&rdquo; on one of them.</div>
								<?php endif; ?>

								<!-- The list -->
								<div class="dre-glass dre-leads-list">
									<div class="dre-h">
										<h3><?= count($episodes) ?> <?= count($episodes) === 1 ? 'episode' : 'episodes' ?> <small>In the order the website shows them</small></h3>
									</div>

									<?php if (!$episodes): ?>
										<div class="dre-empty">
											<i class="bi bi-mic" style="font-size: 2rem;"></i><br>
											No episodes yet. <a href="<?= BASE_URL ?>add-podcast.php">Add the first one</a>
										</div>
									<?php else: ?>
										<div class="dre-proj-table" role="table" aria-label="Podcast episodes">
											<div class="dre-proj-row dre-proj-head" role="row">
												<div role="columnheader">Episode</div>
												<div role="columnheader">About</div>
												<div role="columnheader">Plays from</div>
												<div role="columnheader">Status</div>
												<div role="columnheader"><span class="sr-only">Actions</span></div>
											</div>
											<?php foreach ($episodes as $row):
												$id = (int) $row['podcast_id'];
												$is_live = (int) $row['is_active'] === 1;
												$is_main = (int) $row['is_main'] === 1;
												$thumb = trim((string) $row['podcast_thumb']);
												$video = trim((string) $row['video_file']);
												$youtube = trim((string) $row['youtube_link']);
												$youtube_id = pod_youtube_id($youtube);
												$about = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $row['podcast_descp']), ENT_QUOTES, 'UTF-8')));
												$edit_url = BASE_URL . 'edit-podcast.php?edit=' . base64_encode($id);
											?>
												<div class="dre-proj-row<?= $is_live ? '' : ' is-hidden' ?>" role="row">
													<div class="dre-proj-main" role="cell">
														<a class="dre-proj-thumb" href="<?= pod_e($edit_url) ?>" tabindex="-1" aria-hidden="true">
															<?php if ($thumb !== ''): ?>
																<img src="<?= BASE_URL ?>assets/media/podcast/thumb/<?= pod_e(rawurlencode($thumb)) ?>" alt="" loading="lazy" onerror="this.remove()">
															<?php endif; ?>
															<i class="bi bi-mic"></i>
														</a>
														<div>
															<a class="dre-proj-name" href="<?= pod_e($edit_url) ?>"><?= trim((string) $row['podcast_title']) !== '' ? pod_e($row['podcast_title']) : 'Untitled episode' ?></a>
															<span class="dre-proj-sub"><i class="bi bi-list-ol"></i> Position <?= (int) $row['order_no'] ?></span>
															<?php if ($is_main): ?>
																<div class="dre-proj-pills"><span class="dre-pill <?= $is_live ? 'dre-pill-good' : 'dre-pill-bad' ?>"><i class="bi bi-star-fill"></i> <?= $is_live ? 'Main episode' : 'Main episode, but hidden' ?></span></div>
															<?php endif; ?>
														</div>
													</div>
													<div class="dre-art-summary" role="cell">
														<span><?= $about !== '' ? pod_e(mb_strimwidth($about, 0, 120, '...')) : '<em>No description</em>' ?></span>
														<?php if ($thumb === ''): ?>
															<span class="dre-proj-missing"><i class="bi bi-exclamation-triangle"></i> Missing: thumbnail</span>
														<?php endif; ?>
													</div>
													<div class="dre-proj-facts" role="cell">
														<?php if ($video !== ''): ?>
															<strong><i class="bi bi-film"></i> Uploaded video</strong>
															<span><?= $youtube !== '' ? 'A YouTube link is saved too' : 'No YouTube link' ?></span>
														<?php elseif ($youtube_id !== ''): ?>
															<strong><i class="bi bi-youtube"></i> YouTube</strong>
															<span><a href="<?= pod_e($youtube) ?>" target="_blank" rel="noopener">Open the video</a></span>
														<?php else: ?>
															<strong class="dre-pod-bad"><i class="bi bi-exclamation-triangle"></i> Nothing to play</strong>
															<span><?= $youtube !== '' ? 'The link is not a YouTube video address' : 'No link and no video' ?></span>
														<?php endif; ?>
													</div>
													<div class="dre-proj-status" role="cell">
														<form method="post" action="<?= BASE_URL ?>manage-podcast.php">
															<input type="hidden" name="csrf" value="<?= pod_e($_SESSION['admin_csrf']) ?>">
															<input type="hidden" name="pod_id" value="<?= $id ?>">
															<button type="submit" name="pod_action" value="<?= $is_live ? 'hide' : 'show' ?>" class="dre-switch<?= $is_live ? ' is-on' : '' ?>" role="switch" aria-checked="<?= $is_live ? 'true' : 'false' ?>" title="<?= $is_live ? 'Live on the website. Click to hide.' : 'Hidden from the website. Click to show.' ?>">
																<i></i><span><?= $is_live ? 'Live' : 'Hidden' ?></span>
															</button>
														</form>
														<span class="dre-proj-sync"><i class="bi bi-clock-history"></i> Changed <?= strtotime((string) $row['updated_on']) > 0 ? date('d M Y', strtotime($row['updated_on'])) : 'not known' ?></span>
													</div>
													<div class="dre-proj-actions" role="cell">
														<a class="dre-icon-btn" href="<?= pod_e($edit_url) ?>" title="Edit" aria-label="Edit <?= pod_e($row['podcast_title']) ?>"><i class="bi bi-pencil"></i></a>
														<div class="dropdown">
															<button type="button" class="dre-icon-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More" aria-label="More actions for <?= pod_e($row['podcast_title']) ?>"><i class="bi bi-three-dots"></i></button>
															<div class="dropdown-menu dropdown-menu-right">
																<form method="post" action="<?= BASE_URL ?>manage-podcast.php" class="dre-pod-delete" data-name="<?= pod_e($row['podcast_title']) ?>">
																	<input type="hidden" name="csrf" value="<?= pod_e($_SESSION['admin_csrf']) ?>">
																	<input type="hidden" name="pod_id" value="<?= $id ?>">
																	<?php if (!$is_main): ?>
																		<button type="submit" name="pod_action" value="main" class="dropdown-item"><i class="bi bi-star-fill mr-2"></i>Make main episode</button>
																		<div class="dropdown-divider"></div>
																	<?php endif; ?>
																	<button type="submit" name="pod_action" value="delete" class="dropdown-item text-danger"><i class="bi bi-trash3 mr-2"></i>Delete</button>
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
									document.querySelectorAll('.dre-pod-delete').forEach(function (form) {
										form.addEventListener('submit', function (event) {
											var button = event.submitter;
											if (button && button.value === 'delete' && !window.confirm('Delete "' + form.getAttribute('data-name') + '"? Its thumbnail and uploaded video are removed too. A copy of the record is kept in the delete log.')) {
												event.preventDefault();
											}
										});
									});
									var notice = document.getElementById('podNotice');
									if (notice) {
										setTimeout(function () { notice.style.display = 'none'; }, 8000);
									}
								})();
							</script>
							<!--end::Podcast-->

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