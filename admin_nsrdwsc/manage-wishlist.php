<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');

// Nothing runs unless signed in.
if (empty($_SESSION['email_id'])) {
	exit;
}

function set_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

$days = isset($_GET['days']) ? (int) $_GET['days'] : 30;
if (!in_array($days, [7, 30, 90, 365], true)) {
	$days = 30;
}

$stats = wishlist_admin_stats($conn, $days);
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
		</div>
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
					<div class="container-fluid d-flex align-items-stretch justify-content-between">
						<div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
							<div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default"></div>
						</div>
						<div class="topbar">
							<div class="topbar-item">
								<div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
									<span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
									<span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= set_e($_SESSION['username'] ?? '') ?></span>
									<span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
										<span class="symbol-label font-size-h5 font-weight-bold"><?= set_e(substr((string) ($_SESSION['username'] ?? '?'), 0, 1)) ?></span>
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
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-logs.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-wishlist.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-logs dre-wishlist">
								<!-- Title -->
								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1><i class="bi bi-heart-fill mr-2"></i> Wishlist Activity</h1>
										<p>Which properties and pages visitors are hearting (♡) most. The visitor's own wishlist stays on their device (a 1-year cookie) — this is tracking only.</p>
									</div>
								</div>

								<!-- Time range -->
								<nav class="dre-leads-tabs" aria-label="Time range">
									<?php foreach ([7 => '7 days', 30 => '30 days', 90 => '90 days', 365 => '1 year'] as $d => $label): ?>
										<a href="?days=<?= $d ?>" class="<?= $d === $days ? 'is-on' : '' ?>"><i class="bi bi-calendar3"></i> <?= set_e($label) ?></a>
									<?php endforeach; ?>
								</nav>

								<div class="row">
									<div class="col-xl-4 col-6 dre-col">
										<div class="dre-glass dre-tile">
											<div class="dre-kpi">
												<div>
													<div class="dre-kpi-label">Added to wishlist</div>
													<div class="dre-kpi-value"><?= number_format((int) $stats['totals']['total_adds']) ?></div>
													<div class="dre-kpi-note">last <?= (int) $days ?> days</div>
												</div>
												<span class="dre-icon dre-icon-good"><i class="bi bi-heart-fill"></i></span>
											</div>
										</div>
									</div>
									<div class="col-xl-4 col-6 dre-col">
										<div class="dre-glass dre-tile">
											<div class="dre-kpi">
												<div>
													<div class="dre-kpi-label">Removed from wishlist</div>
													<div class="dre-kpi-value"><?= number_format((int) $stats['totals']['total_removes']) ?></div>
													<div class="dre-kpi-note">last <?= (int) $days ?> days</div>
												</div>
												<span class="dre-icon dre-icon-bad"><i class="bi bi-heartbreak"></i></span>
											</div>
										</div>
									</div>
									<div class="col-xl-4 col-6 dre-col">
										<div class="dre-glass dre-tile">
											<div class="dre-kpi">
												<div>
													<div class="dre-kpi-label">Unique visitors</div>
													<div class="dre-kpi-value"><?= number_format((int) $stats['totals']['unique_visitors']) ?></div>
													<div class="dre-kpi-note">by wishlist cookie</div>
												</div>
												<span class="dre-icon"><i class="bi bi-people-fill"></i></span>
											</div>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-7 mb-4">
										<div class="dre-glass">
											<div class="dre-h">
												<h3><i class="bi bi-house-heart mr-2"></i> Most wishlisted properties</h3>
												<small><?= count($stats['top_properties']) ?> in the last <?= (int) $days ?> days</small>
											</div>
											<?php if (!$stats['top_properties']): ?>
												<div class="dre-empty"><i class="bi bi-heart" style="font-size: 2rem;"></i><br>No wishlist activity yet in this period.</div>
											<?php else: ?>
												<ul class="dre-list">
													<?php foreach ($stats['top_properties'] as $row): ?>
														<li>
															<span class="dre-avatar"><i class="bi bi-house-door-fill"></i></span>
															<div class="dre-list-main">
																<strong><a href="<?= set_e($row['url']) ?>" target="_blank" rel="noopener"><?= set_e($row['proj_name'] ?: $row['proj_slug']) ?></a></strong>
																<span><span class="dre-pill"><?= set_e($row['type_label'] ?: '—') ?></span></span>
															</div>
															<div class="dre-list-side">
																<span class="dre-pill dre-pill-good"><i class="bi bi-plus-lg"></i> <?= number_format((int) $row['adds']) ?></span>
																<?php if ((int) $row['removes'] > 0): ?>
																	<span class="dre-pill dre-pill-bad"><i class="bi bi-dash-lg"></i> <?= number_format((int) $row['removes']) ?></span>
																<?php endif; ?>
															</div>
														</li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
										</div>
									</div>

									<div class="col-lg-5 mb-4">
										<div class="dre-glass">
											<div class="dre-h">
												<h3><i class="bi bi-file-earmark-text mr-2"></i> Pages where it happens most</h3>
												<small><?= count($stats['top_pages']) ?> in the last <?= (int) $days ?> days</small>
											</div>
											<?php if (!$stats['top_pages']): ?>
												<div class="dre-empty"><i class="bi bi-file-earmark" style="font-size: 2rem;"></i><br>No wishlist activity yet in this period.</div>
											<?php else: ?>
												<ul class="dre-list">
													<?php foreach ($stats['top_pages'] as $row): ?>
														<li>
															<span class="dre-avatar"><i class="bi bi-link-45deg"></i></span>
															<div class="dre-list-main">
																<?php $page_path = trim((string) (parse_url($row['page_url'], PHP_URL_PATH) ?: $row['page_url']), '/') ?: '/'; ?>
																<strong><a href="<?= set_e($row['page_url']) ?>" target="_blank" rel="noopener"><?= set_e($page_path) ?></a></strong>
																<span><?= set_e($row['page_url']) ?></span>
															</div>
															<div class="dre-list-side">
																<span class="dre-pill"><?= number_format((int) $row['adds']) ?> adds</span>
															</div>
														</li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--end::Content-->

				<?php
				include('includes/footer-inner.php');
				?>
