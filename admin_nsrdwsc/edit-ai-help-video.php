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

// This file is used for both adding and editing: this copy edits a video (add-ai-help-video.php adds one).
$editing = true;

function aihvf_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

$id = 0;
$details = null;
if ($editing) {
	$id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
	$details = $id > 0 ? get_ai_help_video_one($conn, $id) : null;
}
$can_show_form = !$editing || $details;

$errors = [];
$old = ['title' => '', 'youtube_url' => '', 'keywords' => '', 'display_order' => '0', 'is_active' => '1'];
if ($details) {
	$old = [
		'title' => (string) $details['title'], 'youtube_url' => (string) $details['youtube_url'],
		'keywords' => (string) $details['keywords'], 'display_order' => (string) (int) $details['display_order'],
		'is_active' => $details['is_active'] === '1' ? '1' : '',
	];
}

if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_video'])) {
	$old['title'] = isset($_POST['title']) && is_string($_POST['title']) ? mb_substr(trim($_POST['title']), 0, 255) : '';
	$old['youtube_url'] = isset($_POST['youtube_url']) && is_string($_POST['youtube_url']) ? mb_substr(trim($_POST['youtube_url']), 0, 500) : '';
	$old['keywords'] = isset($_POST['keywords']) && is_string($_POST['keywords']) ? mb_substr(trim($_POST['keywords']), 0, 255) : '';
	$old['display_order'] = isset($_POST['display_order']) ? (string) (int) $_POST['display_order'] : '0';
	$old['is_active'] = isset($_POST['is_active']) ? '1' : '';

	if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
		$errors[] = 'The form expired. Please check your entries and save again.';
	}
	if ($old['title'] === '') {
		$errors[] = 'Title is needed.';
	}
	if (!preg_match('#^https://(www\.)?(youtube\.com|youtu\.be)/#i', $old['youtube_url'])) {
		$errors[] = 'Paste a youtube.com or youtu.be link.';
	}

	if (!$errors) {
		$values = [$old['title'], $old['youtube_url'], $old['keywords'], (int) $old['display_order'], $old['is_active'] === '1' ? '1' : '2'];
		if ($editing) {
			$old_data = $logger->getOldRecordData('tbl_ai_help_video', $id);
			$stmt = mysqli_prepare($conn, "UPDATE tbl_ai_help_video SET title = ?, youtube_url = ?, keywords = ?, display_order = ?, is_active = ? WHERE video_id = ?");
			mysqli_stmt_bind_param($stmt, 'sssisi', ...array_merge($values, [$id]));
			$saved = mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			$record_id = $id;
		} else {
			$stmt = mysqli_prepare($conn, "INSERT INTO tbl_ai_help_video (title, youtube_url, keywords, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
			mysqli_stmt_bind_param($stmt, 'sssis', ...$values);
			$saved = mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			$record_id = $saved ? (int) mysqli_insert_id($conn) : 0;
		}
		if (!$saved) {
			$errors[] = 'The video could not be saved. Please try again.';
		} else {
			$logger->log($editing ? 'UPDATE' : 'INSERT', 'tbl_ai_help_video', $record_id, $editing ? $old_data : null, $logger->getOldRecordData('tbl_ai_help_video', $record_id), ($editing ? 'Help video updated: ' : 'Help video added: ') . $old['title']);
			header('Location: ' . BASE_URL . 'manage-ai-help-video.php?done=' . ($editing ? 'saved' : 'added'));
			exit;
		}
	}
}
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
									<span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= aihvf_e($_SESSION['username']) ?></span>
									<span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
										<span class="symbol-label font-size-h5 font-weight-bold"><?= aihvf_e(substr($_SESSION['username'], 0, 1)) ?></span>
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
							<link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-ai-help.css?v=3" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-form-page">
								<div class="aih-hero" style="margin-bottom:22px">
									<div class="aih-hero-main">
										<div class="aih-hero-icon"><i class="bi bi-youtube"></i></div>
										<div>
											<h1><?= $editing ? 'Edit help video' : 'Add help video' ?></h1>
											<p><?= $editing ? ($details ? aihvf_e($details['title']) : 'This video could not be found.') : 'A video from your YouTube channel the assistant can suggest.' ?></p>
										</div>
									</div>
									<div class="aih-hero-actions">
										<a href="<?= BASE_URL ?>manage-ai-help-video.php" class="aih-btn"><i class="bi bi-arrow-left"></i> Back to help videos</a>
									</div>
								</div>

								<?php if (!$can_show_form): ?>
									<div class="dre-form-alert dre-form-bad" role="alert">
										<i class="bi bi-exclamation-triangle"></i>
										<div><strong>This video could not be found.</strong> <a href="<?= BASE_URL ?>manage-ai-help-video.php">Back to help videos</a></div>
									</div>
								<?php endif; ?>

								<?php if ($errors): ?>
									<div class="dre-form-alert dre-form-bad" role="alert" id="aihvErrors" tabindex="-1">
										<i class="bi bi-exclamation-triangle"></i>
										<div>
											<strong>Not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
											<ul>
												<?php foreach ($errors as $error): ?>
													<li><?= aihvf_e($error) ?></li>
												<?php endforeach; ?>
											</ul>
										</div>
									</div>
								<?php endif; ?>

								<?php if ($can_show_form): ?>
								<form action="<?= BASE_URL ?><?= $editing ? 'edit-ai-help-video.php?edit=' . base64_encode($id) : 'add-ai-help-video.php' ?>" method="post" id="aihv-form" class="dre-form">
									<input type="hidden" name="csrf" value="<?= aihvf_e($_SESSION['admin_csrf']) ?>">
									<input type="hidden" name="save_video" value="1">

									<section class="dre-glass dre-form-section">
										<h2><i class="bi bi-youtube"></i> The video</h2>
										<label class="dre-field">
											<span>Title <b>*</b></span>
											<input type="text" name="title" value="<?= aihvf_e($old['title']) ?>" maxlength="255" autocomplete="off" placeholder="How to add a new off-plan project" required>
										</label>
										<label class="dre-field">
											<span>YouTube link <b>*</b></span>
											<input type="url" name="youtube_url" value="<?= aihvf_e($old['youtube_url']) ?>" maxlength="500" autocomplete="off" placeholder="https://www.youtube.com/watch?v=..." required>
										</label>
										<label class="dre-field">
											<span>Keywords (comma separated, helps matching)</span>
											<input type="text" name="keywords" value="<?= aihvf_e($old['keywords']) ?>" maxlength="255" autocomplete="off" placeholder="project, off-plan, add project, listing">
										</label>
									</section>

									<section class="dre-glass dre-form-section">
										<h2><i class="bi bi-send"></i> Publishing</h2>
										<div class="dre-form-grid dre-cols-2">
											<label class="dre-field">
												<span>Order (lower shows first)</span>
												<input type="number" name="display_order" value="<?= aihvf_e($old['display_order']) ?>" min="0" step="1">
											</label>
											<label class="dre-toggle">
												<input type="checkbox" name="is_active" value="1" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
												<i></i>
												<span><strong>Can be suggested by the AI assistant</strong><small>Off = saved but not suggested.</small></span>
											</label>
										</div>
									</section>

									<div class="dre-form-bar">
										<div class="dre-form-bar-buttons">
											<button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Save video' ?></button>
										</div>
									</div>
								</form>
								<?php endif; ?>
							</div>

						</div>
					</div>
				</div>
				<?php include('includes/footer-inner.php'); ?>
			</div>
		</div>
	</div>
