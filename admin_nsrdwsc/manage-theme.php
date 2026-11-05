<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

// Nothing runs unless signed in as admin
if (empty($_SESSION['email_id'])) {
	exit;
}

// Website theme is for super admins only.
$is_super_admin = (int) ($_SESSION['login_type'] ?? 0) === 1;

$logger = new Logger($conn);

if (empty($_SESSION['admin_csrf'])) {
	$_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
}

function theme_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function theme_get_row($conn, $sql, array $params = [])
{
	try {
		$stmt = mysqli_prepare($conn, $sql);
		if (!$stmt) {
			return null;
		}
		if ($params) {
			$types = '';
			foreach ($params as $p) {
				$types .= is_int($p) ? 'i' : 's';
			}
			mysqli_stmt_bind_param($stmt, $types, ...$params);
		}
		mysqli_stmt_execute($stmt);
		$res = mysqli_stmt_get_result($stmt);
		$row = $res ? mysqli_fetch_assoc($res) : null;
		mysqli_stmt_close($stmt);
		return $row;
	} catch (Throwable $e) {
		error_log('theme_get_row error: ' . $e->getMessage());
		return null;
	}
}

// Fetch active or latest theme row
$theme = theme_get_row($conn, "SELECT * FROM tbl_theme_settings WHERE is_active = '1' ORDER BY theme_id DESC");
if (!$theme) {
	$theme = theme_get_row($conn, "SELECT * FROM tbl_theme_settings ORDER BY theme_id DESC");
}
if (!$theme) {
	mysqli_query($conn, "INSERT INTO tbl_theme_settings (theme_name, is_active) VALUES ('DRE Luxury Taupe', '1')");
	$theme = theme_get_row($conn, "SELECT * FROM tbl_theme_settings ORDER BY theme_id DESC") ?: [];
}
$theme_id = (int) ($theme['theme_id'] ?? 1);

$errors = [];
$success_msg = '';

// Handle Reset to Factory Brand Defaults
if ($is_super_admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_defaults'])) {
	if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
		$errors[] = 'Security token expired. Please try again.';
	} else {
		$old_data = $logger->getOldRecordData('tbl_theme_settings', $theme_id);
		$reset_sql = "UPDATE tbl_theme_settings SET
			theme_name = 'DRE Luxury Taupe',
			body_font_family = 'Urbanist',
			heading_font_family = 'Urbanist',
			custom_font_url = '',
			base_font_size = '16px',
			body_line_height = '1.6',
			h1_font_size = '56px',
			h2_font_size = '40px',
			h3_font_size = '24px',
			primary_color = '#5f574c',
			accent_color = '#E8C9A8',
			accent_color_2 = '#F5E9DC',
			accent_color_3 = '#DCC5A9',
			button_bg_color = '#ab7752',
			button_text_color = '#ffffff',
			button_border_radius = '999px',
			night_bg_color = '#14110e',
			night_text_color = '#f4efe9',
			night_accent_color = '#bfab95',
			card_border_radius = '26px',
			default_mode = 'night',
			custom_css = '',
			is_active = '1',
			tstp = NOW()
			WHERE theme_id = ?";
		$stmt = mysqli_prepare($conn, $reset_sql);
		if ($stmt) {
			mysqli_stmt_bind_param($stmt, 'i', $theme_id);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			$new_data = $logger->getOldRecordData('tbl_theme_settings', $theme_id);
			$logger->log('UPDATE', 'tbl_theme_settings', $theme_id, $old_data, $new_data, 'Theme reset to brand defaults');
			header('Location: ' . BASE_URL . 'manage-theme.php?reset=1');
			exit;
		} else {
			$errors[] = 'Failed to reset settings. Database error: ' . mysqli_error($conn);
		}
	}
}

// Handle Save Theme Settings
if ($is_super_admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_theme'])) {
	if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
		$errors[] = 'The form expired. Please check your changes and save again.';
	}

	$theme_name = trim($_POST['theme_name'] ?? 'DRE Luxury Taupe');
	$body_font_family = trim($_POST['body_font_family'] ?? 'Urbanist');
	if (!empty($_POST['custom_body_font'])) {
		$body_font_family = trim($_POST['custom_body_font']);
	}
	$heading_font_family = trim($_POST['heading_font_family'] ?? 'Urbanist');
	if (!empty($_POST['custom_heading_font'])) {
		$heading_font_family = trim($_POST['custom_heading_font']);
	}
	$custom_font_url = trim($_POST['custom_font_url'] ?? '');
	$base_font_size = trim($_POST['base_font_size'] ?? '16px');
	$body_line_height = trim($_POST['body_line_height'] ?? '1.6');
	$h1_font_size = trim($_POST['h1_font_size'] ?? '56px');
	$h2_font_size = trim($_POST['h2_font_size'] ?? '40px');
	$h3_font_size = trim($_POST['h3_font_size'] ?? '24px');

	$primary_color = trim($_POST['primary_color'] ?? '#5f574c');
	$accent_color = trim($_POST['accent_color'] ?? '#E8C9A8');
	$accent_color_2 = trim($_POST['accent_color_2'] ?? '#F5E9DC');
	$accent_color_3 = trim($_POST['accent_color_3'] ?? '#DCC5A9');
	$button_bg_color = trim($_POST['button_bg_color'] ?? '#ab7752');
	$button_text_color = trim($_POST['button_text_color'] ?? '#ffffff');
	$button_border_radius = trim($_POST['button_border_radius'] ?? '999px');

	$night_bg_color = trim($_POST['night_bg_color'] ?? '#14110e');
	$night_text_color = trim($_POST['night_text_color'] ?? '#f4efe9');
	$night_accent_color = trim($_POST['night_accent_color'] ?? '#bfab95');
	$card_border_radius = trim($_POST['card_border_radius'] ?? '26px');

	$default_mode = in_array($_POST['default_mode'] ?? 'night', ['night', 'day'], true) ? $_POST['default_mode'] : 'night';
	$custom_css = trim($_POST['custom_css'] ?? '');
	$is_active = isset($_POST['is_active']) && $_POST['is_active'] === '1' ? '1' : '1';

	// Validation
	if ($theme_name === '') {
		$errors[] = 'Theme name cannot be empty.';
	}
	if ($custom_font_url !== '' && (!filter_var($custom_font_url, FILTER_VALIDATE_URL) || !preg_match('~^https?://~i', $custom_font_url))) {
		$errors[] = 'Custom font URL must be a valid web link starting with https://';
	}

	if (!$errors) {
		$old_data = $logger->getOldRecordData('tbl_theme_settings', $theme_id);
		$update_sql = "UPDATE tbl_theme_settings SET
			theme_name = ?,
			body_font_family = ?,
			heading_font_family = ?,
			custom_font_url = ?,
			base_font_size = ?,
			body_line_height = ?,
			h1_font_size = ?,
			h2_font_size = ?,
			h3_font_size = ?,
			primary_color = ?,
			accent_color = ?,
			accent_color_2 = ?,
			accent_color_3 = ?,
			button_bg_color = ?,
			button_text_color = ?,
			button_border_radius = ?,
			night_bg_color = ?,
			night_text_color = ?,
			night_accent_color = ?,
			card_border_radius = ?,
			default_mode = ?,
			custom_css = ?,
			is_active = ?,
			tstp = NOW()
			WHERE theme_id = ?";
		
		$stmt = mysqli_prepare($conn, $update_sql);
		if ($stmt) {
			mysqli_stmt_bind_param(
				$stmt,
				'sssssssssssssssssssssssi',
				$theme_name,
				$body_font_family,
				$heading_font_family,
				$custom_font_url,
				$base_font_size,
				$body_line_height,
				$h1_font_size,
				$h2_font_size,
				$h3_font_size,
				$primary_color,
				$accent_color,
				$accent_color_2,
				$accent_color_3,
				$button_bg_color,
				$button_text_color,
				$button_border_radius,
				$night_bg_color,
				$night_text_color,
				$night_accent_color,
				$card_border_radius,
				$default_mode,
				$custom_css,
				$is_active,
				$theme_id
			);

			if (mysqli_stmt_execute($stmt)) {
				mysqli_stmt_close($stmt);
				$new_data = $logger->getOldRecordData('tbl_theme_settings', $theme_id);
				$logger->log('UPDATE', 'tbl_theme_settings', $theme_id, $old_data, $new_data, 'Theme settings updated');
				header('Location: ' . BASE_URL . 'manage-theme.php?saved=1');
				exit;
			} else {
				$errors[] = 'Failed to update theme settings: ' . mysqli_stmt_error($stmt);
				mysqli_stmt_close($stmt);
			}
		} else {
			$errors[] = 'Database query preparation failed: ' . mysqli_error($conn);
		}
	}
}

// Reload theme data after potential update
$theme = theme_get_row($conn, "SELECT * FROM tbl_theme_settings WHERE theme_id = ?", [$theme_id]) ?: $theme;

$saved = isset($_GET['saved']) && !$errors;
$reset = isset($_GET['reset']) && !$errors;
?>
<!--begin::Body-->
<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
	<!--begin::Main-->
	<!--begin::Header Mobile-->
	<div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
		<a href="dashboard.php">
			<img alt="Logo" src="<?= BASE_URL ?>assets/media/logos/logo-light.png" />
		</a>
		<div class="d-flex align-items-center">
			<button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
				<span></span>
			</button>
			<button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle">
				<span></span>
			</button>
			<button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle">
				<span class="svg-icon svg-icon-xl">
					<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
						<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							<polygon points="0 0 24 0 24 24 0 24" />
							<path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
							<path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
						</g>
					</svg>
				</span>
			</button>
		</div>
	</div>
	<!--end::Header Mobile-->

	<div class="d-flex flex-column flex-root">
		<div class="d-flex flex-row flex-column-fluid page">
			<!--begin::Aside-->
			<?php include('includes/sidebar.php'); ?>
			<!--end::Aside-->

			<!--begin::Wrapper-->
			<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
				<!--begin::Header-->
				<div id="kt_header" class="header header-fixed">
					<div class="container-fluid d-flex align-items-stretch justify-content-between">
						<div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
							<div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default"></div>
						</div>
						<div class="topbar">
							<div class="topbar-item">
								<div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
									<span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
									<span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= theme_e($_SESSION['username'] ?? 'Admin') ?></span>
									<span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
										<span class="symbol-label font-size-h5 font-weight-bold"><?= theme_e(substr($_SESSION['username'] ?? 'A', 0, 1)) ?></span>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--end::Header-->

				<!--begin::Content-->
				<div class="content d-flex flex-column flex-column-fluid dre-dash-content" id="kt_content">
					<div class="d-flex flex-column-fluid">
						<div class="container-fluid px-lg-8">
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-form.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-settings.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-theme-mgmt.css?v=2" rel="stylesheet" type="text/css" />

							<!-- Google Fonts for Live Preview -->
							<link rel="preconnect" href="https://fonts.googleapis.com" />
							<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
							<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Cormorant+Garamond:ital,wght@0,500;0,600;1,400&family=Inter:wght@400;500;600&family=Montserrat:wght@400;500;600;700&family=Outfit:wght@400;500;600&family=Playfair+Display:ital,wght@0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&family=Syne:wght@500;700&family=Urbanist:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&display=swap" rel="stylesheet" />

							<div class="dre-dash dre-form-page dre-theme-mgmt">
								<!-- Hero Banner -->
								<div class="dre-theme-hero d-flex flex-wrap justify-content-between align-items-center gap-3">
									<div>
										<h1 class="h3 font-weight-bolder mb-1 text-white"><i class="bi bi-palette-fill mr-2"></i> Website Theme &amp; Typography Control</h1>
										<p class="mb-0 text-white-50">Manage entire website font family, typography sizes, primary colors, button styles, dark/light mode and layout.</p>
									</div>
									<div class="d-flex align-items-center gap-2">
										<a href="<?= theme_e(BASE_URL_HOME) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light font-weight-bold px-4 py-2">
											<i class="bi bi-box-arrow-up-right mr-1"></i> View Live Site
										</a>
									</div>
								</div>

								<!-- Success Alert -->
								<?php if ($saved): ?>
									<div class="alert alert-success d-flex align-items-center p-4 mb-4 rounded-xl" role="alert">
										<i class="bi bi-check-circle-fill font-size-h4 mr-3 text-success"></i>
										<div>
											<strong class="d-block font-size-lg">Theme Settings Saved Successfully!</strong>
											<span>Your typography, colors, and layout customizations are now applied dynamically across the entire website.</span>
										</div>
									</div>
								<?php elseif ($reset): ?>
									<div class="alert alert-info d-flex align-items-center p-4 mb-4 rounded-xl" role="alert">
										<i class="bi bi-arrow-counterclockwise font-size-h4 mr-3 text-info"></i>
										<div>
											<strong class="d-block font-size-lg">Factory Brand Defaults Restored!</strong>
											<span>All typography, colors, and button stylings have been reset to the default DRE Luxury Taupe brand settings.</span>
										</div>
									</div>
								<?php endif; ?>

								<!-- Error Alert -->
								<?php if ($errors): ?>
									<div class="alert alert-danger p-4 mb-4 rounded-xl" role="alert">
										<strong class="d-block font-size-lg mb-2"><i class="bi bi-exclamation-triangle-fill mr-2"></i> Please check the following issues:</strong>
										<ul class="mb-0 pl-4">
											<?php foreach ($errors as $err): ?>
												<li><?= theme_e($err) ?></li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>

								<?php if (!$is_super_admin): ?>
									<div class="dre-leads-notice" role="alert"><i class="bi bi-shield-lock"></i> Only a super admin can see and change the website theme.</div>
								<?php else: ?>

								<!-- Presets Bar -->
								<div class="dre-theme-card mb-4">
									<div class="dre-preset-bar">
										<div class="dre-preset-label">
											<span class="font-weight-bolder text-dark font-size-sm text-uppercase mr-2"><i class="bi bi-stars text-warning mr-1"></i> Quick Luxury Presets:</span>
											<span class="text-muted font-size-sm">Click any preset to apply a curated design palette instantly into the preview:</span>
										</div>
										<div class="dre-preset-scroll">
											<button type="button" class="dre-preset-chip" onclick="applyPreset('classic')">
												<span class="dot" style="background: #5f574c;"></span> DRE Classic Taupe
											</button>
											<button type="button" class="dre-preset-chip" onclick="applyPreset('midnight_gold')">
												<span class="dot" style="background: #d4b67a;"></span> Midnight Royal Gold
											</button>
											<button type="button" class="dre-preset-chip" onclick="applyPreset('emerald')">
												<span class="dot" style="background: #2d5447;"></span> Emerald Prestige
											</button>
											<button type="button" class="dre-preset-chip" onclick="applyPreset('minimalist')">
												<span class="dot" style="background: #374151;"></span> Modern Minimalist
											</button>
											<button type="button" class="dre-preset-chip" onclick="applyPreset('sunset')">
												<span class="dot" style="background: #9c583f;"></span> Sunset Bronze
											</button>
											<button type="button" class="dre-preset-chip" onclick="applyPreset('royal_sapphire')">
												<span class="dot" style="background: #1b4965;"></span> Royal Sapphire
											</button>
											<button type="button" class="dre-preset-chip" onclick="applyPreset('rose_quartz')">
												<span class="dot" style="background: #a85d63;"></span> Rose Quartz
											</button>
											<button type="button" class="dre-preset-chip" onclick="applyPreset('monochrome')">
												<span class="dot" style="background: #111111;"></span> Monochrome Noir
											</button>
										</div>
									</div>
								</div>

								<!-- Main Form & Live Preview Row -->
								<form action="<?= BASE_URL ?>manage-theme.php" method="post" id="themeForm">
									<input type="hidden" name="csrf" value="<?= theme_e($_SESSION['admin_csrf']) ?>">
									<input type="hidden" name="save_theme" value="1">

									<div class="row">
										<!-- Left Column: Controls (7 cols on lg) -->
										<div class="col-xl-7 col-lg-7">
											
											<!-- General Info -->
											<div class="dre-theme-card">
												<h3><i class="bi bi-card-heading"></i> Theme Profile &amp; General</h3>
												<div class="form-group mb-3">
													<label class="font-weight-bold text-dark font-size-sm">Theme Name</label>
													<input type="text" name="theme_name" id="theme_name" class="form-control form-control-solid" value="<?= theme_e($theme['theme_name'] ?? 'DRE Luxury Taupe') ?>" required>
													<small class="form-text text-muted">A descriptive label for this visual theme configuration.</small>
												</div>
												<div class="form-group mb-0">
													<label class="font-weight-bold text-dark font-size-sm">Default Visitor Color Scheme</label>
													<div class="d-flex gap-4 mt-1">
														<div class="custom-control custom-radio">
															<input type="radio" id="mode_night" name="default_mode" value="night" class="custom-control-input" <?= ($theme['default_mode'] ?? 'night') === 'night' ? 'checked' : '' ?> onchange="updateLivePreview()">
															<label class="custom-control-label font-weight-bold" for="mode_night"><i class="bi bi-moon-stars mr-1"></i> Night Mode (Warm Charcoal / Gold - Recommended)</label>
														</div>
														<div class="custom-control custom-radio ml-4">
															<input type="radio" id="mode_day" name="default_mode" value="day" class="custom-control-input" <?= ($theme['default_mode'] ?? 'night') === 'day' ? 'checked' : '' ?> onchange="updateLivePreview()">
															<label class="custom-control-label font-weight-bold" for="mode_day"><i class="bi bi-sun mr-1"></i> Day Mode (Primary Taupe)</label>
														</div>
													</div>
												</div>
											</div>

											<!-- Typography & Font Family -->
											<div class="dre-theme-card">
												<h3><i class="bi bi-type"></i> Typography &amp; Font Family</h3>
												
												<div class="row">
													<div class="col-md-6 form-group">
														<label class="font-weight-bold text-dark font-size-sm">Body Font Family</label>
														<select name="body_font_family" id="body_font_family" class="form-control form-control-solid" onchange="updateLivePreview()">
															<?php
															$body_fonts = [
																'Urbanist' => 'Urbanist (DRE Luxury Modern Sans)',
																'Poppins' => 'Poppins (Clean Geometric Sans)',
																'Inter' => 'Inter (Modern Crisp Sans)',
																'Plus Jakarta Sans' => 'Plus Jakarta Sans (Premium Sans)',
																'Outfit' => 'Outfit (Contemporary Sans)',
																'Montserrat' => 'Montserrat (Architectural Sans)',
																'Roboto' => 'Roboto (Clean Neutral Sans)',
																'Open Sans' => 'Open Sans (Versatile Humanist Sans)',
																'Lato' => 'Lato (Warm Sans)',
																'custom' => 'Custom Font (Specify Below)'
															];
															$curr_body = $theme['body_font_family'] ?? 'Urbanist';
															$is_body_custom = !array_key_exists($curr_body, $body_fonts) && $curr_body !== 'custom';
															foreach ($body_fonts as $val => $lbl):
																$sel = ($curr_body === $val || ($val === 'custom' && $is_body_custom)) ? 'selected' : '';
															?>
																<option value="<?= theme_e($val) ?>" <?= $sel ?>><?= theme_e($lbl) ?></option>
															<?php endforeach; ?>
														</select>
														<div id="custom_body_box" class="mt-2 <?= $is_body_custom ? '' : 'd-none' ?>">
															<input type="text" name="custom_body_font" id="custom_body_font" class="form-control form-control-sm" placeholder="e.g. Raleway" value="<?= $is_body_custom ? theme_e($curr_body) : '' ?>" oninput="updateLivePreview()">
														</div>
													</div>

													<div class="col-md-6 form-group">
														<label class="font-weight-bold text-dark font-size-sm">Headings Font Family</label>
														<select name="heading_font_family" id="heading_font_family" class="form-control form-control-solid" onchange="updateLivePreview()">
															<?php
															$heading_fonts = [
																'Urbanist' => 'Urbanist (Same as Brand Sans)',
																'Playfair Display' => 'Playfair Display (Luxury Editorial Serif)',
																'Cormorant Garamond' => 'Cormorant Garamond (Royal Luxury Serif)',
																'Cinzel' => 'Cinzel (Classical Inscription Serif)',
																'Syne' => 'Syne (Avant-Garde Architectural)',
																'Poppins' => 'Poppins (Bold Geometric)',
																'Montserrat' => 'Montserrat (Strong Modern)',
																'custom' => 'Custom Font (Specify Below)'
															];
															$curr_head = $theme['heading_font_family'] ?? 'Urbanist';
															$is_head_custom = !array_key_exists($curr_head, $heading_fonts) && $curr_head !== 'custom';
															foreach ($heading_fonts as $val => $lbl):
																$sel = ($curr_head === $val || ($val === 'custom' && $is_head_custom)) ? 'selected' : '';
															?>
																<option value="<?= theme_e($val) ?>" <?= $sel ?>><?= theme_e($lbl) ?></option>
															<?php endforeach; ?>
														</select>
														<div id="custom_head_box" class="mt-2 <?= $is_head_custom ? '' : 'd-none' ?>">
															<input type="text" name="custom_heading_font" id="custom_heading_font" class="form-control form-control-sm" placeholder="e.g. Bodoni Moda" value="<?= $is_head_custom ? theme_e($curr_head) : '' ?>" oninput="updateLivePreview()">
														</div>
													</div>
												</div>

												<div class="form-group mb-3">
													<label class="font-weight-bold text-dark font-size-sm">Custom Google Font URL (Optional)</label>
													<input type="url" name="custom_font_url" id="custom_font_url" class="form-control form-control-solid" placeholder="https://fonts.googleapis.com/css2?family=..." value="<?= theme_e($theme['custom_font_url'] ?? '') ?>">
													<small class="form-text text-muted">Paste external Google Font stylesheet link if you are using a font outside the preset catalog.</small>
												</div>

												<div class="row">
													<div class="col-md-4 form-group">
														<label class="font-weight-bold text-dark font-size-sm">Base Font Size</label>
														<select name="base_font_size" id="base_font_size" class="form-control form-control-solid" onchange="updateLivePreview()">
															<?php
															$sizes = ['14px' => '14px (Compact)', '15px' => '15px (Medium)', '16px' => '16px (Default Standard)', '17px' => '17px (Comfortable)', '18px' => '18px (Large Accessible)'];
															foreach ($sizes as $val => $lbl):
															?>
																<option value="<?= $val ?>" <?= ($theme['base_font_size'] ?? '16px') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
															<?php endforeach; ?>
														</select>
													</div>
													<div class="col-md-4 form-group">
														<label class="font-weight-bold text-dark font-size-sm">Body Line Height</label>
														<select name="body_line_height" id="body_line_height" class="form-control form-control-solid" onchange="updateLivePreview()">
															<?php
															$heights = ['1.4' => '1.4 (Compact)', '1.5' => '1.5 (Standard)', '1.6' => '1.6 (Default Elegant)', '1.7' => '1.7 (Spacious)', '1.8' => '1.8 (Editorial)'];
															foreach ($heights as $val => $lbl):
															?>
																<option value="<?= $val ?>" <?= ($theme['body_line_height'] ?? '1.6') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
															<?php endforeach; ?>
														</select>
													</div>
													<div class="col-md-4 form-group">
														<label class="font-weight-bold text-dark font-size-sm">H1 Heading Size</label>
														<select name="h1_font_size" id="h1_font_size" class="form-control form-control-solid" onchange="updateLivePreview()">
															<?php
															$h1_sizes = ['42px' => '42px', '48px' => '48px', '52px' => '52px', '56px' => '56px (Default)', '64px' => '64px (Hero Impact)', '72px' => '72px'];
															foreach ($h1_sizes as $val => $lbl):
															?>
																<option value="<?= $val ?>" <?= ($theme['h1_font_size'] ?? '56px') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
															<?php endforeach; ?>
														</select>
													</div>
												</div>

												<div class="row">
													<div class="col-md-6 form-group mb-0">
														<label class="font-weight-bold text-dark font-size-sm">H2 Subtitle Size</label>
														<select name="h2_font_size" id="h2_font_size" class="form-control form-control-solid" onchange="updateLivePreview()">
															<?php
															$h2_sizes = ['32px' => '32px', '36px' => '36px', '40px' => '40px (Default)', '44px' => '44px', '48px' => '48px'];
															foreach ($h2_sizes as $val => $lbl):
															?>
																<option value="<?= $val ?>" <?= ($theme['h2_font_size'] ?? '40px') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
															<?php endforeach; ?>
														</select>
													</div>
													<div class="col-md-6 form-group mb-0">
														<label class="font-weight-bold text-dark font-size-sm">H3 Section Title Size</label>
														<select name="h3_font_size" id="h3_font_size" class="form-control form-control-solid" onchange="updateLivePreview()">
															<?php
															$h3_sizes = ['20px' => '20px', '22px' => '22px', '24px' => '24px (Default)', '26px' => '26px', '28px' => '28px'];
															foreach ($h3_sizes as $val => $lbl):
															?>
																<option value="<?= $val ?>" <?= ($theme['h3_font_size'] ?? '24px') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
															<?php endforeach; ?>
														</select>
													</div>
												</div>
											</div>

											<!-- Colors & Palettes -->
											<div class="dre-theme-card">
												<h3><i class="bi bi-droplet-half"></i> Brand &amp; Daytime Colors</h3>
												<div class="row">
													<div class="col-md-6 form-group">
														<label class="font-weight-bold text-dark font-size-sm">Day Brand Background (--bg)</label>
														<div class="color-input-group">
															<input type="color" class="color-picker-swatch" id="cp_primary_color" value="<?= theme_e($theme['primary_color'] ?? '#5f574c') ?>" oninput="syncColor('primary_color', this.value)">
															<input type="text" name="primary_color" id="primary_color" class="form-control form-control-solid" value="<?= theme_e($theme['primary_color'] ?? '#5f574c') ?>" oninput="syncColorSwatch('cp_primary_color', this.value)">
														</div>
														<small class="text-muted">Main brand taupe background in day mode.</small>
													</div>

													<div class="col-md-6 form-group">
														<label class="font-weight-bold text-dark font-size-sm">Primary Accent / Gold (--acc)</label>
														<div class="color-input-group">
															<input type="color" class="color-picker-swatch" id="cp_accent_color" value="<?= theme_e($theme['accent_color'] ?? '#E8C9A8') ?>" oninput="syncColor('accent_color', this.value)">
															<input type="text" name="accent_color" id="accent_color" class="form-control form-control-solid" value="<?= theme_e($theme['accent_color'] ?? '#E8C9A8') ?>" oninput="syncColorSwatch('cp_accent_color', this.value)">
														</div>
														<small class="text-muted">Primary gold accent for highlights and active states.</small>
													</div>
												</div>

												<div class="row">
													<div class="col-md-6 form-group">
														<label class="font-weight-bold text-dark font-size-sm">Secondary Accent (--acc-2)</label>
														<div class="color-input-group">
															<input type="color" class="color-picker-swatch" id="cp_accent_color_2" value="<?= theme_e($theme['accent_color_2'] ?? '#F5E9DC') ?>" oninput="syncColor('accent_color_2', this.value)">
															<input type="text" name="accent_color_2" id="accent_color_2" class="form-control form-control-solid" value="<?= theme_e($theme['accent_color_2'] ?? '#F5E9DC') ?>" oninput="syncColorSwatch('cp_accent_color_2', this.value)">
														</div>
														<small class="text-muted">Champagne soft highlight color.</small>
													</div>

													<div class="col-md-6 form-group">
														<label class="font-weight-bold text-dark font-size-sm">Tertiary Accent (--acc-3)</label>
														<div class="color-input-group">
															<input type="color" class="color-picker-swatch" id="cp_accent_color_3" value="<?= theme_e($theme['accent_color_3'] ?? '#DCC5A9') ?>" oninput="syncColor('accent_color_3', this.value)">
															<input type="text" name="accent_color_3" id="accent_color_3" class="form-control form-control-solid" value="<?= theme_e($theme['accent_color_3'] ?? '#DCC5A9') ?>" oninput="syncColorSwatch('cp_accent_color_3', this.value)">
														</div>
														<small class="text-muted">Bronze gradient boundary color.</small>
													</div>
												</div>

												<div class="row">
													<div class="col-md-6 form-group mb-0">
														<label class="font-weight-bold text-dark font-size-sm">Primary Button Color (--btn)</label>
														<div class="color-input-group">
															<input type="color" class="color-picker-swatch" id="cp_button_bg_color" value="<?= theme_e($theme['button_bg_color'] ?? '#ab7752') ?>" oninput="syncColor('button_bg_color', this.value)">
															<input type="text" name="button_bg_color" id="button_bg_color" class="form-control form-control-solid" value="<?= theme_e($theme['button_bg_color'] ?? '#ab7752') ?>" oninput="syncColorSwatch('cp_button_bg_color', this.value)">
														</div>
														<small class="text-muted">Call to action and primary buttons background.</small>
													</div>

													<div class="col-md-6 form-group mb-0">
														<label class="font-weight-bold text-dark font-size-sm">Button Text Color (--btn-fg)</label>
														<div class="color-input-group">
															<input type="color" class="color-picker-swatch" id="cp_button_text_color" value="<?= theme_e($theme['button_text_color'] ?? '#ffffff') ?>" oninput="syncColor('button_text_color', this.value)">
															<input type="text" name="button_text_color" id="button_text_color" class="form-control form-control-solid" value="<?= theme_e($theme['button_text_color'] ?? '#ffffff') ?>" oninput="syncColorSwatch('cp_button_text_color', this.value)">
														</div>
														<small class="text-muted">Label text color on primary buttons.</small>
													</div>
												</div>
											</div>

											<!-- Night Mode Palette -->
											<div class="dre-theme-card">
												<h3><i class="bi bi-moon-stars"></i> Night Mode Palette</h3>
												<div class="row">
													<div class="col-md-4 form-group mb-0">
														<label class="font-weight-bold text-dark font-size-sm">Night Background</label>
														<div class="color-input-group">
															<input type="color" class="color-picker-swatch" id="cp_night_bg_color" value="<?= theme_e($theme['night_bg_color'] ?? '#14110e') ?>" oninput="syncColor('night_bg_color', this.value)">
															<input type="text" name="night_bg_color" id="night_bg_color" class="form-control form-control-solid" value="<?= theme_e($theme['night_bg_color'] ?? '#14110e') ?>" oninput="syncColorSwatch('cp_night_bg_color', this.value)">
														</div>
													</div>

													<div class="col-md-4 form-group mb-0">
														<label class="font-weight-bold text-dark font-size-sm">Night Text Color</label>
														<div class="color-input-group">
															<input type="color" class="color-picker-swatch" id="cp_night_text_color" value="<?= theme_e($theme['night_text_color'] ?? '#f4efe9') ?>" oninput="syncColor('night_text_color', this.value)">
															<input type="text" name="night_text_color" id="night_text_color" class="form-control form-control-solid" value="<?= theme_e($theme['night_text_color'] ?? '#f4efe9') ?>" oninput="syncColorSwatch('cp_night_text_color', this.value)">
														</div>
													</div>

													<div class="col-md-4 form-group mb-0">
														<label class="font-weight-bold text-dark font-size-sm">Night Accent</label>
														<div class="color-input-group">
															<input type="color" class="color-picker-swatch" id="cp_night_accent_color" value="<?= theme_e($theme['night_accent_color'] ?? '#bfab95') ?>" oninput="syncColor('night_accent_color', this.value)">
															<input type="text" name="night_accent_color" id="night_accent_color" class="form-control form-control-solid" value="<?= theme_e($theme['night_accent_color'] ?? '#bfab95') ?>" oninput="syncColorSwatch('cp_night_accent_color', this.value)">
														</div>
													</div>
												</div>
											</div>

											<!-- UI Shapes & Border Radius -->
											<div class="dre-theme-card">
												<h3><i class="bi bi-bounding-box-circles"></i> Shapes &amp; Border Radius</h3>
												<div class="row">
													<div class="col-md-6 form-group mb-0">
														<label class="font-weight-bold text-dark font-size-sm">Button Border Radius</label>
														<select name="button_border_radius" id="button_border_radius" class="form-control form-control-solid" onchange="updateLivePreview()">
															<?php
															$btn_radii = [
																'999px' => 'Full Pill Round (999px - Default Brand)',
																'16px' => 'Soft Curved (16px)',
																'10px' => 'Modern Rounded (10px)',
																'6px' => 'Slightly Rounded (6px)',
																'0px' => 'Sharp Square (0px)'
															];
															foreach ($btn_radii as $val => $lbl):
															?>
																<option value="<?= $val ?>" <?= ($theme['button_border_radius'] ?? '999px') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
															<?php endforeach; ?>
														</select>
													</div>

													<div class="col-md-6 form-group mb-0">
														<label class="font-weight-bold text-dark font-size-sm">Card &amp; Glass Radius (--r)</label>
														<select name="card_border_radius" id="card_border_radius" class="form-control form-control-solid" onchange="updateLivePreview()">
															<?php
															$card_radii = [
																'26px' => '26px (Default Luxury Glassmorphism)',
																'20px' => '20px (Medium Glass)',
																'16px' => '16px (Subtle Rounded)',
																'12px' => '12px (Compact Rounded)',
																'8px' => '8px (Minimal)'
															];
															foreach ($card_radii as $val => $lbl):
															?>
																<option value="<?= $val ?>" <?= ($theme['card_border_radius'] ?? '26px') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
															<?php endforeach; ?>
														</select>
													</div>
												</div>
											</div>

											<!-- Advanced Custom CSS -->
											<div class="dre-theme-card">
												<h3><i class="bi bi-code-slash"></i> Advanced Custom CSS</h3>
												<div class="form-group mb-0">
													<label class="font-weight-bold text-dark font-size-sm">Custom CSS Overrides</label>
													<textarea name="custom_css" id="custom_css" class="form-control form-control-solid font-monospace" rows="4" placeholder="/* Any custom CSS rules to inject on all website pages */"><?= theme_e($theme['custom_css'] ?? '') ?></textarea>
													<small class="form-text text-muted">Directly injected into website &lt;head&gt; after all main stylesheets.</small>
												</div>
											</div>

											<!-- Submit & Reset Actions -->
											<div class="dre-theme-actions mb-8">
												<button type="submit" class="btn btn-primary font-weight-bolder px-8 py-3 font-size-h6">
													<i class="bi bi-check-lg mr-2"></i> Save Theme Settings
												</button>

												<button type="button" class="btn btn-outline-danger font-weight-bold px-5 py-3" onclick="confirmReset()">
													<i class="bi bi-arrow-counterclockwise mr-1"></i> Reset to Defaults
												</button>
											</div>

										</div>

										<!-- Right Column: Interactive Real-time Preview (5 cols on lg) -->
										<div class="col-xl-5 col-lg-5 dre-theme-preview-col">
											<div class="dre-preview-pane" id="previewPane">
												<!-- Preview Top Toolbar -->
												<div class="preview-toolbar">
													<div class="d-flex align-items-center text-white">
														<span class="badge badge-success font-weight-bold mr-2"><i class="bi bi-broadcast mr-1"></i> Live</span>
														<span class="font-weight-bold font-size-sm">Interactive Website Preview</span>
													</div>
													<div class="btn-group btn-group-sm" role="group">
														<button type="button" class="btn btn-sm btn-dark" id="pvToggleNight" onclick="setPreviewMode('night')"><i class="bi bi-moon-stars"></i> Night</button>
														<button type="button" class="btn btn-sm btn-light" id="pvToggleDay" onclick="setPreviewMode('day')"><i class="bi bi-sun"></i> Day</button>
													</div>
												</div>

												<!-- Preview Content Stage -->
												<div class="dre-preview-box" id="pvStage">
													<span class="preview-badge" id="pvEyebrow">Featured Collection</span>
													<h1 id="pvHeading" class="mb-2" style="line-height: 1.1;">Dubai Luxury Living</h1>
													<h2 id="pvSubtitle" class="mb-3 preview-gold-text">Palm Jumeirah &amp; Downtown</h2>
													<p id="pvBody" class="mb-4" style="opacity: 0.85;">
														Explore signature off-plan developments and world-class luxury estates crafted for discerning investors seeking elevated architectural elegance.
													</p>

													<!-- Sample Property Card -->
													<div class="preview-card-item" id="pvCard">
														<div class="d-flex justify-content-between align-items-start mb-2">
															<span class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="border-radius: 999px;">OFF-PLAN</span>
															<span class="font-weight-bold font-size-lg preview-gold-text">AED 4.5M</span>
														</div>
														<h4 id="pvCardTitle" class="font-weight-bold mb-1">Palace Residences Creek</h4>
														<p class="font-size-sm mb-3" style="opacity: 0.7;">Dubai Creek Harbour &bull; 1 to 3 BR</p>
														
														<div class="d-flex align-items-center gap-2">
															<button type="button" class="btn btn-sm px-4 py-2 font-weight-bold" id="pvBtnPrimary">Enquire Now</button>
															<button type="button" class="btn btn-sm btn-outline-light px-3 py-2 font-weight-bold" id="pvBtnSecondary">Brochure</button>
														</div>
													</div>

													<div class="mt-4 pt-3 border-top" style="border-color: rgba(255,255,255,0.15) !important;">
														<small style="opacity: 0.65; display: block;" class="mb-1">Active Font Pairing:</small>
														<div class="d-flex justify-content-between font-size-sm">
															<span>Heading: <strong id="pvHeadFontLabel">Urbanist</strong></span>
															<span>Body: <strong id="pvBodyFontLabel">Urbanist</strong></span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</form>

								<!-- Hidden form for Reset to Defaults -->
								<form action="<?= BASE_URL ?>manage-theme.php" method="post" id="resetForm" style="display:none;">
									<input type="hidden" name="csrf" value="<?= theme_e($_SESSION['admin_csrf']) ?>">
									<input type="hidden" name="reset_defaults" value="1">
								</form>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<?php include('includes/footer.php'); ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Interactive Client-side Script -->
	<?php if ($is_super_admin): ?>
	<script>
		// Preset definitions
		var presets = {
			classic: {
				body_font: 'Urbanist',
				heading_font: 'Urbanist',
				base_size: '16px',
				line_height: '1.6',
				h1_size: '56px',
				h2_size: '40px',
				h3_size: '24px',
				primary_color: '#5f574c',
				accent: '#E8C9A8',
				accent_2: '#F5E9DC',
				accent_3: '#DCC5A9',
				btn_bg: '#ab7752',
				btn_fg: '#ffffff',
				btn_radius: '999px',
				night_bg: '#14110e',
				night_text: '#f4efe9',
				night_accent: '#bfab95',
				card_radius: '26px',
				mode: 'night'
			},
			midnight_gold: {
				body_font: 'Urbanist',
				heading_font: 'Cormorant Garamond',
				base_size: '16px',
				line_height: '1.6',
				h1_size: '64px',
				h2_size: '44px',
				h3_size: '26px',
				primary_color: '#070a10',
				accent: '#d4b67a',
				accent_2: '#f1dcae',
				accent_3: '#a7864e',
				btn_bg: '#c5a059',
				btn_fg: '#1e180e',
				btn_radius: '999px',
				night_bg: '#070a10',
				night_text: '#f4efe6',
				night_accent: '#d4b67a',
				card_radius: '20px',
				mode: 'night'
			},
			emerald: {
				body_font: 'Inter',
				heading_font: 'Playfair Display',
				base_size: '16px',
				line_height: '1.6',
				h1_size: '56px',
				h2_size: '40px',
				h3_size: '24px',
				primary_color: '#1b332b',
				accent: '#e0cfb3',
				accent_2: '#f4efe8',
				accent_3: '#cbb694',
				btn_bg: '#2d5447',
				btn_fg: '#ffffff',
				btn_radius: '10px',
				night_bg: '#0f1f1a',
				night_text: '#f2ece4',
				night_accent: '#d8c29d',
				card_radius: '16px',
				mode: 'night'
			},
			minimalist: {
				body_font: 'Plus Jakarta Sans',
				heading_font: 'Plus Jakarta Sans',
				base_size: '15px',
				line_height: '1.5',
				h1_size: '48px',
				h2_size: '36px',
				h3_size: '22px',
				primary_color: '#22252a',
				accent: '#c5cad1',
				accent_2: '#ffffff',
				accent_3: '#9ca3af',
				btn_bg: '#374151',
				btn_fg: '#ffffff',
				btn_radius: '6px',
				night_bg: '#111315',
				night_text: '#f3f4f6',
				night_accent: '#9ca3af',
				card_radius: '12px',
				mode: 'night'
			},
			sunset: {
				body_font: 'Outfit',
				heading_font: 'Outfit',
				base_size: '16px',
				line_height: '1.6',
				h1_size: '56px',
				h2_size: '40px',
				h3_size: '24px',
				primary_color: '#543d37',
				accent: '#e5b292',
				accent_2: '#faf3eb',
				accent_3: '#c88c69',
				btn_bg: '#9c583f',
				btn_fg: '#ffffff',
				btn_radius: '16px',
				night_bg: '#1a1311',
				night_text: '#faefe9',
				night_accent: '#e5b292',
				card_radius: '20px',
				mode: 'night'
			},
			royal_sapphire: {
				body_font: 'Inter',
				heading_font: 'Montserrat',
				base_size: '16px',
				line_height: '1.6',
				h1_size: '56px',
				h2_size: '40px',
				h3_size: '24px',
				primary_color: '#0d1b2a',
				accent: '#9db4c0',
				accent_2: '#e8eef2',
				accent_3: '#5c7a92',
				btn_bg: '#1b4965',
				btn_fg: '#ffffff',
				btn_radius: '10px',
				night_bg: '#060d14',
				night_text: '#eef3f7',
				night_accent: '#9db4c0',
				card_radius: '18px',
				mode: 'night'
			},
			rose_quartz: {
				body_font: 'Poppins',
				heading_font: 'Cinzel',
				base_size: '16px',
				line_height: '1.6',
				h1_size: '56px',
				h2_size: '40px',
				h3_size: '24px',
				primary_color: '#3a2630',
				accent: '#e8b4b8',
				accent_2: '#f8e8e8',
				accent_3: '#c98a90',
				btn_bg: '#a85d63',
				btn_fg: '#ffffff',
				btn_radius: '999px',
				night_bg: '#1c1215',
				night_text: '#f6e9ea',
				night_accent: '#e8b4b8',
				card_radius: '24px',
				mode: 'night'
			},
			monochrome: {
				body_font: 'Syne',
				heading_font: 'Syne',
				base_size: '16px',
				line_height: '1.6',
				h1_size: '56px',
				h2_size: '40px',
				h3_size: '24px',
				primary_color: '#111111',
				accent: '#ffffff',
				accent_2: '#d4d4d4',
				accent_3: '#8a8a8a',
				btn_bg: '#000000',
				btn_fg: '#ffffff',
				btn_radius: '0px',
				night_bg: '#000000',
				night_text: '#ffffff',
				night_accent: '#cfcfcf',
				card_radius: '4px',
				mode: 'night'
			}
		};

		var previewCurrentMode = '<?= ($theme['default_mode'] ?? 'night') === 'day' ? 'day' : 'night' ?>';

		function syncColor(textInputId, hex) {
			document.getElementById(textInputId).value = hex;
			updateLivePreview();
		}

		function syncColorSwatch(swatchId, hex) {
			if (/^#[0-9a-f]{6}$/i.test(hex)) {
				document.getElementById(swatchId).value = hex;
			}
			updateLivePreview();
		}

		function setPreviewMode(mode) {
			previewCurrentMode = mode;
			var btnNight = document.getElementById('pvToggleNight');
			var btnDay = document.getElementById('pvToggleDay');
			if (mode === 'night') {
				btnNight.className = 'btn btn-sm btn-dark active';
				btnDay.className = 'btn btn-sm btn-light';
			} else {
				btnDay.className = 'btn btn-sm btn-primary active';
				btnNight.className = 'btn btn-sm btn-dark';
			}
			updateLivePreview();
		}

		function applyPreset(name) {
			var p = presets[name];
			if (!p) return;

			// Set form values
			setSelectOrCustom('body_font_family', 'custom_body_font', 'custom_body_box', p.body_font);
			setSelectOrCustom('heading_font_family', 'custom_heading_font', 'custom_head_box', p.heading_font);

			document.getElementById('base_font_size').value = p.base_size;
			document.getElementById('body_line_height').value = p.line_height;
			document.getElementById('h1_font_size').value = p.h1_size;
			document.getElementById('h2_font_size').value = p.h2_size;
			document.getElementById('h3_font_size').value = p.h3_size;

			setColorField('primary_color', 'cp_primary_color', p.primary_color);
			setColorField('accent_color', 'cp_accent_color', p.accent);
			setColorField('accent_color_2', 'cp_accent_color_2', p.accent_2);
			setColorField('accent_color_3', 'cp_accent_color_3', p.accent_3);
			setColorField('button_bg_color', 'cp_button_bg_color', p.btn_bg);
			setColorField('button_text_color', 'cp_button_text_color', p.btn_fg);
			setColorField('night_bg_color', 'cp_night_bg_color', p.night_bg);
			setColorField('night_text_color', 'cp_night_text_color', p.night_text);
			setColorField('night_accent_color', 'cp_night_accent_color', p.night_accent);

			document.getElementById('button_border_radius').value = p.btn_radius;
			document.getElementById('card_border_radius').value = p.card_radius;

			if (p.mode === 'day') {
				document.getElementById('mode_day').checked = true;
			} else {
				document.getElementById('mode_night').checked = true;
			}

			previewCurrentMode = p.mode;
			setPreviewMode(p.mode);
			updateLivePreview();
		}

		function setSelectOrCustom(selectId, customInputId, customBoxId, fontVal) {
			var sel = document.getElementById(selectId);
			var customInput = document.getElementById(customInputId);
			var customBox = document.getElementById(customBoxId);
			var found = false;
			for (var i = 0; i < sel.options.length; i++) {
				if (sel.options[i].value === fontVal) {
					sel.selectedIndex = i;
					found = true;
					break;
				}
			}
			if (!found) {
				sel.value = 'custom';
				customInput.value = fontVal;
				customBox.classList.remove('d-none');
			} else {
				customInput.value = '';
				customBox.classList.add('d-none');
			}
		}

		function setColorField(textId, swatchId, val) {
			document.getElementById(textId).value = val;
			if (/^#[0-9a-f]{6}$/i.test(val)) {
				document.getElementById(swatchId).value = val;
			}
		}

		function getActiveFont(selectId, customInputId) {
			var sel = document.getElementById(selectId).value;
			if (sel === 'custom') {
				return document.getElementById(customInputId).value.trim() || 'Urbanist';
			}
			return sel;
		}

		function updateLivePreview() {
			var bodyFont = getActiveFont('body_font_family', 'custom_body_font');
			var headingFont = getActiveFont('heading_font_family', 'custom_heading_font');

			var baseSize = document.getElementById('base_font_size').value;
			var lineHeight = document.getElementById('body_line_height').value;
			var h1Size = document.getElementById('h1_font_size').value;
			var h2Size = document.getElementById('h2_font_size').value;

			var primaryColor = document.getElementById('primary_color').value;
			var accent = document.getElementById('accent_color').value;
			var accent2 = document.getElementById('accent_color_2').value;
			var accent3 = document.getElementById('accent_color_3').value;
			var btnBg = document.getElementById('button_bg_color').value;
			var btnFg = document.getElementById('button_text_color').value;
			var btnRadius = document.getElementById('button_border_radius').value;
			var cardRadius = document.getElementById('card_border_radius').value;

			var nightBg = document.getElementById('night_bg_color').value;
			var nightText = document.getElementById('night_text_color').value;
			var nightAccent = document.getElementById('night_accent_color').value;

			var stage = document.getElementById('pvStage');
			var heading = document.getElementById('pvHeading');
			var subtitle = document.getElementById('pvSubtitle');
			var body = document.getElementById('pvBody');
			var card = document.getElementById('pvCard');
			var btnPrimary = document.getElementById('pvBtnPrimary');
			var btnSecondary = document.getElementById('pvBtnSecondary');
			var eyebrow = document.getElementById('pvEyebrow');

			// Check mode
			if (previewCurrentMode === 'night') {
				stage.style.backgroundColor = nightBg;
				stage.style.color = nightText;
				eyebrow.style.color = nightAccent;
			} else {
				stage.style.backgroundColor = primaryColor;
				stage.style.color = '#ffffff';
				eyebrow.style.color = accent;
			}

			// Fonts
			stage.style.fontFamily = '"' + bodyFont + '", Urbanist, sans-serif';
			stage.style.fontSize = baseSize;
			stage.style.lineHeight = lineHeight;

			heading.style.fontFamily = '"' + headingFont + '", "' + bodyFont + '", sans-serif';
			heading.style.fontSize = h1Size;

			subtitle.style.fontFamily = '"' + headingFont + '", "' + bodyFont + '", sans-serif';
			subtitle.style.fontSize = h2Size;

			document.getElementById('pvCardTitle').style.fontFamily = '"' + headingFont + '", sans-serif';

			// Gradient for subtitle
			subtitle.style.background = 'linear-gradient(100deg, ' + accent3 + ', ' + accent2 + ' 50%, ' + accent + ' 75%, ' + accent3 + ')';
			subtitle.style.webkitBackgroundClip = 'text';
			subtitle.style.backgroundClip = 'text';
			subtitle.style.color = 'transparent';

			// Card
			card.style.borderRadius = cardRadius;

			// Buttons
			btnPrimary.style.backgroundColor = btnBg;
			btnPrimary.style.color = btnFg;
			btnPrimary.style.borderRadius = btnRadius;
			btnPrimary.style.border = 'none';

			btnSecondary.style.borderRadius = btnRadius;

			// Labels
			document.getElementById('pvHeadFontLabel').textContent = headingFont;
			document.getElementById('pvBodyFontLabel').textContent = bodyFont;

			// Handle custom font box toggles
			var bSel = document.getElementById('body_font_family').value;
			document.getElementById('custom_body_box').classList.toggle('d-none', bSel !== 'custom');

			var hSel = document.getElementById('heading_font_family').value;
			document.getElementById('custom_head_box').classList.toggle('d-none', hSel !== 'custom');
		}

		function confirmReset() {
			if (window.confirm('Are you sure you want to restore the default DRE Luxury brand theme? Any custom colors and font changes will be reset.')) {
				document.getElementById('resetForm').submit();
			}
		}

		// Initialize on load
		document.addEventListener('DOMContentLoaded', function () {
			setPreviewMode(previewCurrentMode);
			updateLivePreview();
		});
	</script>
	<?php endif; ?>
</body>
</html>
