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

// Users are managed by a super admin only.
$is_super_admin = (int) ($_SESSION['login_type'] ?? 0) === 1;
$my_id = (int) ($_SESSION['user_id'] ?? 0);
$user_roles = ['1' => 'Super admin', '2' => 'Sub admin'];

// ---------------------------------------------------------------
// Changes (POST only, with a token): let one user sign in, or stop them
// ---------------------------------------------------------------
$notice = '';
if ($is_super_admin && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['user_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$action = is_string($_POST['user_action']) ? $_POST['user_action'] : '';
	$id = (int) ($_POST['user_id'] ?? 0);
	$user = $token_ok && $id > 0 && in_array($action, ['show', 'hide'], true) ? set_query($conn, "SELECT login_id, username, login_type, is_active FROM tbl_login WHERE login_id = ?", [$id]) : [];
	if ($user) {
		$done = $action === 'show' ? 'on' : 'off';
		if ($action === 'hide' && $id === $my_id) {
			// Nobody can lock themselves out.
			$done = 'self';
		} elseif ($action === 'hide' && $user[0]['login_type'] === '1' && count(set_query($conn, "SELECT login_id FROM tbl_login WHERE login_type = '1' AND is_active = '1' AND login_id <> ?", [$id]) ?: []) === 0) {
			// There must always be a super admin who can sign in.
			$done = 'last';
		} else {
			$value = $action === 'show' ? '1' : '2';
			if ($user[0]['is_active'] !== $value) {
				set_query($conn, "UPDATE tbl_login SET is_active = ? WHERE login_id = ?", [$value, $id]);
				$logger->log('UPDATE', 'tbl_login', $id, ['is_active' => $user[0]['is_active']], ['is_active' => $value], 'User ' . ($value === '1' ? 'allowed to sign in: ' : 'stopped from signing in: ') . $user[0]['username']);
			}
		}
		header('Location: ' . BASE_URL . 'manage-user.php?done=' . $done);
		exit;
	}
	$notice = 'Nothing was changed. Please reload the page and try again.';
}

$users = $is_super_admin ? (set_query($conn, "SELECT l.login_id, l.username, l.login_email, l.login_type, l.is_active, l.biometric_allowed, l.tstp, (SELECT COUNT(*) FROM tbl_login_passkeys p WHERE p.login_id = l.login_id) AS fingerprint_devices FROM tbl_login l ORDER BY l.login_type ASC, l.username ASC") ?: []) : [];
// Sign-in history is read on its own and matched here by email, so the two tables do not have to be compared in SQL.
$sign_ins = [];
if ($users) {
	foreach (set_query($conn, "SELECT user_email, MAX(CASE WHEN login_status = 'SUCCESS' THEN login_time END) AS last_sign_in, SUM(login_status = 'FAILED' AND login_time > DATE_SUB(NOW(), INTERVAL 30 DAY)) AS failed_recently FROM tbl_login_logs GROUP BY user_email") ?: [] as $row) {
		$sign_ins[strtolower(trim((string) $row['user_email']))] = $row;
	}
}
foreach ($users as $index => $row) {
	$history = $sign_ins[strtolower(trim((string) $row['login_email']))] ?? [];
	$users[$index]['last_sign_in'] = $history['last_sign_in'] ?? null;
	$users[$index]['failed_recently'] = (int) ($history['failed_recently'] ?? 0);
}
$active = count(array_filter($users, function ($row) { return $row['is_active'] === '1'; }));
$supers = count(array_filter($users, function ($row) { return $row['login_type'] === '1'; }));

$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : '';
$notice = [
	'added' => 'The user was added. Give them their email and password so they can sign in.', 'saved' => 'The changes were saved.',
	'on' => 'This user can sign in again.', 'off' => 'This user can no longer sign in.',
	'self' => 'You cannot stop your own account from signing in.', 'last' => 'This is the only super admin who can sign in, so it stays on.',
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
							<!--begin::Users-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-articles.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-simple.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-settings.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-articles dre-simple dre-settings dre-users">

								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1>Users</h1>
										<p>The people who can sign in to this admin panel.</p>
									</div>
									<?php if ($is_super_admin): ?>
										<div class="dre-leads-stats">
											<div><strong><?= $active ?></strong><span>Can sign in</span></div>
											<div><strong><?= count($users) - $active ?></strong><span>Stopped</span></div>
											<div><strong><?= $supers ?></strong><span>Super <?= $supers === 1 ? 'admin' : 'admins' ?></span></div>
										</div>
										<div class="dre-actions">
											<a class="dre-primary" href="<?= BASE_URL ?>add-user.php"><i class="bi bi-person-plus"></i> Add user</a>
											<a href="<?= BASE_URL ?>manage-login-logs.php"><i class="bi bi-clock-history"></i> Login log</a>
										</div>
									<?php endif; ?>
								</div>

								<?php if (!$is_super_admin): ?>
									<div class="dre-leads-notice" role="alert"><i class="bi bi-shield-lock"></i> Only a super admin can see and change users.</div>
								<?php else: ?>
									<?php if ($notice !== ''): ?>
										<div class="dre-leads-notice" role="status" id="userNotice"><i class="bi bi-info-circle"></i> <?= set_e($notice) ?></div>
									<?php endif; ?>

									<div class="dre-glass dre-leads-list">
										<div class="dre-h">
											<h3><?= count($users) ?> <?= count($users) === 1 ? 'user' : 'users' ?></h3>
										</div>
										<div class="dre-proj-table" role="table" aria-label="Users">
											<div class="dre-proj-row dre-proj-head" role="row">
												<div role="columnheader">User</div>
												<div role="columnheader">Role</div>
												<div role="columnheader">Last signed in</div>
												<div role="columnheader">Sign-in</div>
												<div role="columnheader"><span class="sr-only">Actions</span></div>
											</div>
											<?php foreach ($users as $row):
												$id = (int) $row['login_id'];
												$can_sign_in = $row['is_active'] === '1';
												$last = $row['last_sign_in'] ? strtotime($row['last_sign_in']) : false;
												$edit_url = BASE_URL . 'edit-user.php?edit=' . base64_encode($id);
											?>
												<div class="dre-proj-row<?= $can_sign_in ? '' : ' is-hidden' ?>" role="row">
													<div class="dre-proj-main" role="cell">
														<a class="dre-proj-thumb dre-sim-initial" href="<?= set_e($edit_url) ?>" tabindex="-1" aria-hidden="true"><span><?= set_e(mb_strtoupper(mb_substr(trim((string) $row['username']), 0, 1)) ?: '?') ?></span></a>
														<div>
															<a class="dre-proj-name" href="<?= set_e($edit_url) ?>"><?= set_e($row['username']) ?><?= $id === $my_id ? ' <em class="dre-pill dre-pill-ai">You</em>' : '' ?></a>
															<span class="dre-proj-sub"><i class="bi bi-envelope"></i> <?= set_e($row['login_email']) ?></span>
														</div>
													</div>
													<div class="dre-tag-result" role="cell">
														<span class="dre-pill <?= $row['login_type'] === '1' ? 'dre-pill-good' : '' ?>"><i class="bi <?= $row['login_type'] === '1' ? 'bi-shield-check' : 'bi-person' ?>"></i> <?= set_e($user_roles[$row['login_type']] ?? 'Sub admin') ?></span>
														<span><?= $row['login_type'] === '1' ? 'Everything, including users and logs' : 'Content pages only' ?></span>
													</div>
													<div class="dre-tag-checks" role="cell">
														<span><?= $last ? date('d M Y, H:i', $last) : 'Never' ?></span>
														<?php if ((int) $row['biometric_allowed'] === 1): ?>
															<span><i class="bi bi-fingerprint"></i> Fingerprint / face allowed &middot; <?= (int) $row['fingerprint_devices'] === 0 ? 'not set up yet' : (int) $row['fingerprint_devices'] . ((int) $row['fingerprint_devices'] === 1 ? ' device' : ' devices') ?></span>
														<?php endif; ?>
														<?php if ((int) $row['failed_recently'] > 0): ?>
															<span class="dre-proj-missing"><i class="bi bi-exclamation-triangle"></i> <?= (int) $row['failed_recently'] ?> failed <?= (int) $row['failed_recently'] === 1 ? 'attempt' : 'attempts' ?> in 30 days</span>
														<?php endif; ?>
													</div>
													<div class="dre-proj-status" role="cell">
														<form method="post" action="<?= BASE_URL ?>manage-user.php" class="dre-user-toggle" data-name="<?= set_e($row['username']) ?>">
															<input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
															<input type="hidden" name="user_id" value="<?= $id ?>">
															<button type="submit" name="user_action" value="<?= $can_sign_in ? 'hide' : 'show' ?>" class="dre-switch<?= $can_sign_in ? ' is-on' : '' ?>" role="switch" aria-checked="<?= $can_sign_in ? 'true' : 'false' ?>" title="<?= $can_sign_in ? 'Can sign in. Click to stop.' : 'Stopped. Click to allow signing in.' ?>"<?= $id === $my_id ? ' disabled' : '' ?>>
																<i></i><span><?= $can_sign_in ? 'Allowed' : 'Stopped' ?></span>
															</button>
														</form>
													</div>
													<div class="dre-proj-actions" role="cell">
														<a class="dre-icon-btn" href="<?= set_e($edit_url) ?>" title="Edit" aria-label="Edit <?= set_e($row['username']) ?>"><i class="bi bi-pencil"></i></a>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endif; ?>
							</div>

							<script>
								(function () {
									document.querySelectorAll('.dre-user-toggle').forEach(function (form) {
										form.addEventListener('submit', function (event) {
											var button = event.submitter;
											if (button && button.value === 'hide' && !window.confirm('Stop "' + form.getAttribute('data-name') + '" from signing in? If they are signed in now, they stay in until that session ends.')) {
												event.preventDefault();
											}
										});
									});
									var notice = document.getElementById('userNotice');
									if (notice) {
										setTimeout(function () { notice.style.display = 'none'; }, 9000);
									}
								})();
							</script>
							<!--end::Users-->

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