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

function aih_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

$done = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['aih_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$id = (int) ($_POST['id'] ?? 0);
	if ($token_ok && $_POST['aih_action'] === 'delete' && $id > 0) {
		$row = get_ai_help_content_one($conn, $id);
		if ($row) {
			$old_data = $logger->getOldRecordData('tbl_ai_help_content', $id);
			$stmt = mysqli_prepare($conn, "DELETE FROM tbl_ai_help_content WHERE help_id = ?");
			mysqli_stmt_bind_param($stmt, 'i', $id);
			if (mysqli_stmt_execute($stmt)) {
				$logger->log('DELETE', 'tbl_ai_help_content', $id, $old_data, null, 'Help note deleted: ' . $row['title']);
				$done = 'deleted';
			}
			mysqli_stmt_close($stmt);
		}
	}
	header('Location: ' . BASE_URL . 'manage-ai-help.php' . ($done ? '?done=' . $done : ''));
	exit;
}
$done = isset($_GET['done']) && in_array($_GET['done'], ['added', 'saved', 'deleted'], true) ? $_GET['done'] : '';

$notes = get_ai_help_content_all($conn);
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
									<span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= aih_e($_SESSION['username']) ?></span>
									<span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
										<span class="symbol-label font-size-h5 font-weight-bold"><?= aih_e(substr($_SESSION['username'], 0, 1)) ?></span>
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
										<div class="aih-hero-icon"><i class="bi bi-stars"></i></div>
										<div>
											<h1>Ask AI help notes</h1>
											<p>Short how-to notes the "Ask AI" assistant uses to answer questions about this admin panel. Write one note per screen or task.</p>
										</div>
									</div>
									<div class="aih-hero-actions">
										<a href="<?= BASE_URL ?>manage-ai-help-video.php" class="aih-btn"><i class="bi bi-youtube"></i> Help videos</a>
										<a href="<?= BASE_URL ?>add-ai-help.php" class="aih-btn aih-btn-main"><i class="bi bi-plus-lg"></i> Add help note</a>
									</div>
								</div>

								<div class="aih-stats">
									<div class="aih-stat"><strong><?= count($notes) ?></strong><span>Total notes</span></div>
									<div class="aih-stat"><strong><?= count(array_filter($notes, fn($n) => $n['is_active'] === '1')) ?></strong><span>Used by AI</span></div>
									<div class="aih-stat"><strong><?= count($notes) - count(array_filter($notes, fn($n) => $n['is_active'] === '1')) ?></strong><span>Hidden</span></div>
								</div>

								<?php if ($done): ?>
									<div class="aih-alert aih-good" role="status">
										<i class="bi bi-check-circle"></i>
										<div><?= $done === 'deleted' ? 'Help note deleted.' : 'Help note saved.' ?></div>
									</div>
								<?php endif; ?>

								<?php if (!$notes): ?>
									<div class="aih-empty">
										<i class="bi bi-chat-square-text"></i>
										<p class="mb-0"><strong>No help notes yet.</strong><br>Add one so the assistant has something to answer from.</p>
									</div>
								<?php else: ?>
									<div class="aih-grid">
										<?php foreach ($notes as $note):
											$on = $note['is_active'] === '1';
											$keywords = array_filter(array_map('trim', explode(',', (string) $note['keywords'])));
										?>
											<div class="aih-card<?= $on ? '' : ' aih-off' ?>">
												<div class="aih-card-top">
													<?php if ($note['module_key'] !== ''): ?>
														<span class="aih-tag"><?= aih_e($note['module_key']) ?></span>
													<?php else: ?>
														<span></span>
													<?php endif; ?>
													<span class="aih-badge <?= $on ? 'aih-on' : 'aih-off2' ?>"><?= $on ? 'Used by AI' : 'Hidden' ?></span>
												</div>
												<h3><?= aih_e($note['title']) ?></h3>
												<p><?= aih_e(mb_substr(strip_tags((string) $note['help_text']), 0, 140)) ?></p>
												<?php if ($keywords): ?>
													<div class="aih-chips">
														<?php foreach (array_slice($keywords, 0, 4) as $kw): ?>
															<span class="aih-chip"><?= aih_e($kw) ?></span>
														<?php endforeach; ?>
													</div>
												<?php endif; ?>
												<?php if ($note['page_url'] !== ''): ?>
													<div class="aih-chips">
														<span class="aih-chip" style="color:#5f574c;background:rgba(171,152,137,.22)"><i class="bi bi-link-45deg"></i> Opens <?= aih_e($note['page_url']) ?></span>
													</div>
												<?php endif; ?>
												<div class="aih-card-actions">
													<a href="<?= BASE_URL ?>edit-ai-help.php?edit=<?= base64_encode($note['help_id']) ?>" class="aih-icon-btn"><i class="bi bi-pencil"></i> Edit</a>
													<form method="post" style="flex:1" onsubmit="return confirm('Delete this help note?');">
														<input type="hidden" name="csrf" value="<?= aih_e($_SESSION['admin_csrf']) ?>">
														<input type="hidden" name="id" value="<?= (int) $note['help_id'] ?>">
														<button type="submit" name="aih_action" value="delete" class="aih-icon-btn aih-danger" style="width:100%"><i class="bi bi-trash3"></i> Delete</button>
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
