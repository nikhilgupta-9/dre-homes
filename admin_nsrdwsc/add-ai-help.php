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

// This file is used for both adding and editing: this copy adds a note (edit-ai-help.php changes one).
$editing = false;

function aihf_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

$id = 0;
$details = null;
if ($editing) {
	$id = (int) base64_decode(is_string($_GET['edit'] ?? null) ? $_GET['edit'] : '', true);
	$details = $id > 0 ? get_ai_help_content_one($conn, $id) : null;
}
$can_show_form = !$editing || $details;

$errors = [];
$old = ['module_key' => '', 'title' => '', 'help_text' => '', 'keywords' => '', 'page_url' => '', 'page_label' => '', 'display_order' => '0', 'is_active' => '1'];
if ($details) {
	$old = [
		'module_key' => (string) $details['module_key'], 'title' => (string) $details['title'],
		'help_text' => (string) $details['help_text'], 'keywords' => (string) $details['keywords'],
		'page_url' => (string) $details['page_url'], 'page_label' => (string) $details['page_label'],
		'display_order' => (string) (int) $details['display_order'], 'is_active' => $details['is_active'] === '1' ? '1' : '',
	];
}

if ($can_show_form && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_help'])) {
	$old['module_key'] = isset($_POST['module_key']) && is_string($_POST['module_key']) ? mb_substr(trim($_POST['module_key']), 0, 100) : '';
	$old['title'] = isset($_POST['title']) && is_string($_POST['title']) ? mb_substr(trim($_POST['title']), 0, 255) : '';
	$old['help_text'] = isset($_POST['help_text']) && is_string($_POST['help_text']) ? mb_substr(trim($_POST['help_text']), 0, 6000) : '';
	$old['keywords'] = isset($_POST['keywords']) && is_string($_POST['keywords']) ? mb_substr(trim($_POST['keywords']), 0, 255) : '';
	$old['page_url'] = isset($_POST['page_url']) && is_string($_POST['page_url']) ? mb_substr(trim($_POST['page_url']), 0, 255) : '';
	$old['page_label'] = isset($_POST['page_label']) && is_string($_POST['page_label']) ? mb_substr(trim($_POST['page_label']), 0, 100) : '';
	$old['display_order'] = isset($_POST['display_order']) ? (string) (int) $_POST['display_order'] : '0';
	$old['is_active'] = isset($_POST['is_active']) ? '1' : '';

	if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
		$errors[] = 'The form expired. Please check your entries and save again.';
	}
	if ($old['title'] === '') {
		$errors[] = 'Title is needed.';
	}
	if ($old['help_text'] === '') {
		$errors[] = 'The help text is needed: this is what the AI will answer from.';
	}
	// Only a plain admin file name (with an optional query string), never a full URL or "../".
	if ($old['page_url'] !== '' && !preg_match('/^[a-z0-9_-]+\.php(\?[a-z0-9_=&-]*)?$/i', $old['page_url'])) {
		$errors[] = 'The admin page should be a file name such as "add-project.php" or "manage-project.php?status=active", not a full web address.';
	}

	if (!$errors) {
		$values = [$old['module_key'], $old['title'], $old['help_text'], $old['keywords'], $old['page_url'], $old['page_label'], (int) $old['display_order'], $old['is_active'] === '1' ? '1' : '2'];
		if ($editing) {
			$old_data = $logger->getOldRecordData('tbl_ai_help_content', $id);
			$stmt = mysqli_prepare($conn, "UPDATE tbl_ai_help_content SET module_key = ?, title = ?, help_text = ?, keywords = ?, page_url = ?, page_label = ?, display_order = ?, is_active = ? WHERE help_id = ?");
			mysqli_stmt_bind_param($stmt, 'ssssssisi', ...array_merge($values, [$id]));
			$saved = mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			$record_id = $id;
		} else {
			$stmt = mysqli_prepare($conn, "INSERT INTO tbl_ai_help_content (module_key, title, help_text, keywords, page_url, page_label, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
			mysqli_stmt_bind_param($stmt, 'ssssssis', ...$values);
			$saved = mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			$record_id = $saved ? (int) mysqli_insert_id($conn) : 0;
		}
		if (!$saved) {
			$errors[] = 'The help note could not be saved. Please try again.';
		} else {
			$logger->log($editing ? 'UPDATE' : 'INSERT', 'tbl_ai_help_content', $record_id, $editing ? $old_data : null, $logger->getOldRecordData('tbl_ai_help_content', $record_id), ($editing ? 'Help note updated: ' : 'Help note added: ') . $old['title']);
			header('Location: ' . BASE_URL . 'manage-ai-help.php?done=' . ($editing ? 'saved' : 'added'));
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
									<span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= aihf_e($_SESSION['username']) ?></span>
									<span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
										<span class="symbol-label font-size-h5 font-weight-bold"><?= aihf_e(substr($_SESSION['username'], 0, 1)) ?></span>
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
										<div class="aih-hero-icon"><i class="bi bi-chat-dots"></i></div>
										<div>
											<h1><?= $editing ? 'Edit help note' : 'Add help note' ?></h1>
											<p><?= $editing ? ($details ? aihf_e($details['title']) : 'This help note could not be found.') : 'A short how-to note the "Ask AI" assistant will answer from.' ?></p>
										</div>
									</div>
									<div class="aih-hero-actions">
										<a href="<?= BASE_URL ?>manage-ai-help.php" class="aih-btn"><i class="bi bi-arrow-left"></i> Back to help notes</a>
									</div>
								</div>

								<?php if (!$can_show_form): ?>
									<div class="dre-form-alert dre-form-bad" role="alert">
										<i class="bi bi-exclamation-triangle"></i>
										<div><strong>This help note could not be found.</strong> <a href="<?= BASE_URL ?>manage-ai-help.php">Back to help notes</a></div>
									</div>
								<?php endif; ?>

								<?php if ($errors): ?>
									<div class="dre-form-alert dre-form-bad" role="alert" id="aihErrors" tabindex="-1">
										<i class="bi bi-exclamation-triangle"></i>
										<div>
											<strong>Not saved. Please fix <?= count($errors) === 1 ? 'this' : 'these ' . count($errors) . ' things' ?>:</strong>
											<ul>
												<?php foreach ($errors as $error): ?>
													<li><?= aihf_e($error) ?></li>
												<?php endforeach; ?>
											</ul>
										</div>
									</div>
								<?php endif; ?>

								<?php if ($can_show_form): ?>
								<form action="<?= BASE_URL ?><?= $editing ? 'edit-ai-help.php?edit=' . base64_encode($id) : 'add-ai-help.php' ?>" method="post" id="aih-form" class="dre-form">
									<input type="hidden" name="csrf" value="<?= aihf_e($_SESSION['admin_csrf']) ?>">
									<input type="hidden" name="save_help" value="1">

									<section class="dre-glass dre-form-section">
										<h2><i class="bi bi-chat-dots"></i> The note</h2>
										<div class="dre-form-grid dre-cols-2">
											<label class="dre-field">
												<span>Module / screen (for your own reference)</span>
												<input type="text" name="module_key" value="<?= aihf_e($old['module_key']) ?>" maxlength="100" autocomplete="off" placeholder="manage-project">
											</label>
											<label class="dre-field">
												<span>Title <b>*</b></span>
												<input type="text" name="title" value="<?= aihf_e($old['title']) ?>" maxlength="255" autocomplete="off" placeholder="How to add a new off-plan project" required>
											</label>
										</div>
										<label class="dre-field">
											<span>Help text <b>*</b></span>
											<textarea name="help_text" rows="8" placeholder="Write the steps in plain language, as you would explain them to a new team member."><?= aihf_e($old['help_text']) ?></textarea>
											<small>This is exactly what the AI reads to answer. Be specific about menu names, field names and steps.</small>
										</label>
										<label class="dre-field">
											<span>Keywords (comma separated, helps matching)</span>
											<input type="text" name="keywords" value="<?= aihf_e($old['keywords']) ?>" maxlength="255" autocomplete="off" placeholder="project, off-plan, add project, listing">
										</label>
									</section>

									<section class="dre-glass dre-form-section">
										<h2><i class="bi bi-link-45deg"></i> Open this page button</h2>
										<p class="text-muted" style="font-size:12.5px;margin-top:-6px">Optional. When this note answers the question, the assistant shows a button that opens this admin page directly.</p>
										<div class="dre-form-grid dre-cols-2">
											<label class="dre-field">
												<span>Admin page</span>
												<input type="text" name="page_url" value="<?= aihf_e($old['page_url']) ?>" maxlength="255" autocomplete="off" spellcheck="false" placeholder="add-project.php">
												<small>Just the file name, e.g. "add-project.php" or "manage-project.php". Leave empty for no button.</small>
											</label>
											<label class="dre-field">
												<span>Button label</span>
												<input type="text" name="page_label" value="<?= aihf_e($old['page_label']) ?>" maxlength="100" autocomplete="off" placeholder="Open Add Project">
												<small>Leave empty to use the note's title as the button text.</small>
											</label>
										</div>
									</section>

									<section class="dre-glass dre-form-section">
										<h2><i class="bi bi-send"></i> Publishing</h2>
										<div class="dre-form-grid dre-cols-2">
											<label class="dre-field">
												<span>Order (lower shows first)</span>
												<input type="number" name="display_order" value="<?= aihf_e($old['display_order']) ?>" min="0" step="1">
											</label>
											<label class="dre-toggle">
												<input type="checkbox" name="is_active" value="1" <?= $old['is_active'] === '1' ? 'checked' : '' ?>>
												<i></i>
												<span><strong>Used by the AI assistant</strong><small>Off = saved but not used to answer.</small></span>
											</label>
										</div>
									</section>

									<div class="dre-form-bar">
										<div class="dre-form-bar-buttons">
											<button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Save help note' ?></button>
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
