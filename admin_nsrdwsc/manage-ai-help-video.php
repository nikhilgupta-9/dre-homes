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

function aihv_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

$done = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['aihv_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$id = (int) ($_POST['id'] ?? 0);
	if ($token_ok && $_POST['aihv_action'] === 'delete' && $id > 0) {
		$row = get_ai_help_video_one($conn, $id);
		if ($row) {
			$old_data = $logger->getOldRecordData('tbl_ai_help_video', $id);
			$stmt = mysqli_prepare($conn, "DELETE FROM tbl_ai_help_video WHERE video_id = ?");
			mysqli_stmt_bind_param($stmt, 'i', $id);
			if (mysqli_stmt_execute($stmt)) {
				$logger->log('DELETE', 'tbl_ai_help_video', $id, $old_data, null, 'Help video deleted: ' . $row['title']);
				$done = 'deleted';
			}
			mysqli_stmt_close($stmt);
		}
	}
	header('Location: ' . BASE_URL . 'manage-ai-help-video.php' . ($done ? '?done=' . $done : ''));
	exit;
}
$done = isset($_GET['done']) && in_array($_GET['done'], ['added', 'saved', 'deleted'], true) ? $_GET['done'] : '';

$videos = get_ai_help_videos_all($conn);
?>
<!--begin::Body-->

<body id="kt_body"
	class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
	<div class="d-flex flex-column flex-root">
		<div class="d-flex flex-row flex-column-fluid page">
			<?php include('includes/sidebar.php'); ?>
			<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
				<div id="kt_header" class="header header-fixed">
					<div class="container-fluid d-flex align-items-stretch justify-content-between">
						<div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
							<div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default"></div>
						</div>
						<div class="topbar">
							<div class="topbar-item">
								<div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
									<span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
									<span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= aihv_e($_SESSION['username']) ?></span>
									<span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
										<span class="symbol-label font-size-h5 font-weight-bold"><?= aihv_e(substr($_SESSION['username'], 0, 1)) ?></span>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="content d-flex flex-column flex-column-fluid dre-dash-content" id="kt_content">
					<div class="d-flex flex-column-fluid">
						<div class="container">
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-ai-help.css?v=3" rel="stylesheet" type="text/css" />

							<div class="dre-dash aih-wrap">
								<div class="aih-hero">
									<div class="aih-hero-main">
										<div class="aih-hero-icon"><i class="bi bi-youtube"></i></div>
										<div>
											<h1>Ask AI help videos</h1>
											<p>YouTube videos the "Ask AI" assistant can suggest alongside its answer. Add your channel's links here and tag them with keywords.</p>
										</div>
									</div>
									<div class="aih-hero-actions">
										<a href="<?= BASE_URL ?>manage-ai-help.php" class="aih-btn"><i class="bi bi-chat-dots"></i> Help notes</a>
										<a href="<?= BASE_URL ?>add-ai-help-video.php" class="aih-btn aih-btn-main"><i class="bi bi-plus-lg"></i> Add video</a>
									</div>
								</div>

								<div class="aih-stats">
									<div class="aih-stat"><strong><?= count($videos) ?></strong><span>Total videos</span></div>
									<div class="aih-stat"><strong><?= count(array_filter($videos, fn($v) => $v['is_active'] === '1')) ?></strong><span>Suggestible</span></div>
									<div class="aih-stat"><strong><?= count($videos) - count(array_filter($videos, fn($v) => $v['is_active'] === '1')) ?></strong><span>Hidden</span></div>
								</div>

								<?php if ($done): ?>
									<div class="aih-alert aih-good" role="status">
										<i class="bi bi-check-circle"></i>
										<div><?= $done === 'deleted' ? 'Video removed.' : 'Video saved.' ?></div>
									</div>
								<?php endif; ?>

								<?php if (!$videos): ?>
									<div class="aih-empty">
										<i class="bi bi-camera-reels"></i>
										<p class="mb-0"><strong>No videos yet.</strong><br>Add a link from your YouTube channel so the assistant can suggest it.</p>
									</div>
								<?php else: ?>
									<div class="aih-grid">
										<?php foreach ($videos as $video):
											$on = $video['is_active'] === '1';
											$thumb = youtube_thumb_url($video['youtube_url']);
											$keywords = array_filter(array_map('trim', explode(',', (string) $video['keywords'])));
										?>
											<div class="aih-card<?= $on ? '' : ' aih-off' ?>">
												<a href="<?= aihv_e($video['youtube_url']) ?>" target="_blank" rel="noopener" class="aih-thumb">
													<?php if ($thumb): ?>
														<img src="<?= aihv_e($thumb) ?>" alt="" loading="lazy">
														<span class="aih-play"><i class="bi bi-play-fill"></i></span>
													<?php else: ?>
														<i class="bi bi-youtube aih-thumb-fallback"></i>
													<?php endif; ?>
												</a>
												<div class="aih-card-top">
													<span></span>
													<span class="aih-badge <?= $on ? 'aih-on' : 'aih-off2' ?>"><?= $on ? 'Suggestible' : 'Hidden' ?></span>
												</div>
												<h3><?= aihv_e($video['title']) ?></h3>
												<?php if ($keywords): ?>
													<div class="aih-chips">
														<?php foreach (array_slice($keywords, 0, 4) as $kw): ?>
															<span class="aih-chip"><?= aihv_e($kw) ?></span>
														<?php endforeach; ?>
													</div>
												<?php endif; ?>
												<div class="aih-card-actions">
													<a href="<?= BASE_URL ?>edit-ai-help-video.php?edit=<?= base64_encode($video['video_id']) ?>" class="aih-icon-btn"><i class="bi bi-pencil"></i> Edit</a>
													<form method="post" style="flex:1" onsubmit="return confirm('Remove this video?');">
														<input type="hidden" name="csrf" value="<?= aihv_e($_SESSION['admin_csrf']) ?>">
														<input type="hidden" name="id" value="<?= (int) $video['video_id'] ?>">
														<button type="submit" name="aihv_action" value="delete" class="aih-icon-btn aih-danger" style="width:100%"><i class="bi bi-trash3"></i> Delete</button>
													</form>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>

						</div>
					</div>
				</div>
				<?php include('includes/footer-inner.php'); ?>
			</div>
		</div>
	</div>
