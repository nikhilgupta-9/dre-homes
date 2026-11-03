<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
// This page shows sign-in records: stop here for anyone who is not signed in.
if (empty($_SESSION['email_id'])) {
	exit;
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
										class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= htmlspecialchars($_SESSION['username']) ?></span>
									<span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
										<span
											class="symbol-label font-size-h5 font-weight-bold"><?= htmlspecialchars(substr($_SESSION['username'], 0, 1)) ?></span>
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
							<!--begin::Dashboard-->
							<?php
							// ---------------------------------------------------------------
							// Dashboard data. Every query here is fixed text (no request input).
							// ---------------------------------------------------------------
							function dash_value($conn, $sql)
							{
								$result = @mysqli_query($conn, $sql);
								if (!$result) {
									return 0;
								}
								$row = mysqli_fetch_row($result);
								return $row ? $row[0] : 0;
							}
							function dash_rows($conn, $sql)
							{
								$result = @mysqli_query($conn, $sql);
								$rows = [];
								if ($result) {
									while ($row = mysqli_fetch_assoc($result)) {
										$rows[] = $row;
									}
								}
								return $rows;
							}
							function dash_table_exists($conn, $table)
							{
								$result = @mysqli_query($conn, "SHOW TABLES LIKE '" . mysqli_real_escape_string($conn, $table) . "'");
								return $result && mysqli_num_rows($result) > 0;
							}
							function dash_e($text)
							{
								return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
							}
							function dash_ago($datetime)
							{
								$time = strtotime((string) $datetime);
								if (!$time) {
									return '';
								}
								$seconds = time() - $time;
								if ($seconds < 60) {
									return 'just now';
								}
								if ($seconds < 3600) {
									return floor($seconds / 60) . ' min ago';
								}
								if ($seconds < 86400) {
									return floor($seconds / 3600) . ' h ago';
								}
								if ($seconds < 86400 * 30) {
									return floor($seconds / 86400) . ' d ago';
								}
								return date('d M Y', $time);
							}
							function dash_percent($part, $whole)
							{
								return $whole > 0 ? (int) round($part * 100 / $whole) : 0;
							}

							$resumes_30 = (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_resume WHERE tstp >= CURDATE() - INTERVAL 29 DAY");
							$resumes_total = (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_resume");
							$blogs_active = (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_blog WHERE is_active = '1'");
							$blogs_30 = (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_blog WHERE is_active = '1' AND blog_posted_on >= CURDATE() - INTERVAL 29 DAY");

							// Listings
							$listings_active = (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_proj WHERE is_active = '1'");
							$listings_inactive = (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_proj WHERE is_active <> '1'");
							$listings_30 = (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_proj WHERE is_active = '1' AND proj_posted_on >= CURDATE() - INTERVAL 29 DAY");
							$listings_prev_30 = (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_proj WHERE is_active = '1' AND proj_posted_on >= CURDATE() - INTERVAL 59 DAY AND proj_posted_on < CURDATE() - INTERVAL 29 DAY");
							$listings_change = $listings_prev_30 > 0 ? (int) round(($listings_30 - $listings_prev_30) * 100 / $listings_prev_30) : null;

							// Live listings added per month, last 12 months including this one.
							$month_counts = [];
							foreach (dash_rows($conn, "SELECT DATE_FORMAT(proj_posted_on, '%Y-%m') AS ym, COUNT(*) AS n FROM tbl_proj WHERE is_active = '1' AND proj_posted_on >= DATE_FORMAT(CURDATE() - INTERVAL 11 MONTH, '%Y-%m-01') GROUP BY ym") as $row) {
								$month_counts[$row['ym']] = (int) $row['n'];
							}
							$months = [];
							for ($i = 11; $i >= 0; $i--) {
								$stamp = strtotime(date('Y-m-01') . " -$i month");
								$months[] = ['label' => date('M', $stamp), 'title' => date('F Y', $stamp), 'count' => $month_counts[date('Y-m', $stamp)] ?? 0];
							}
							$month_max = max(1, max(array_column($months, 'count')));
							$listing_types = dash_rows($conn, "SELECT COALESCE(t.proj_type_name, 'Other') AS type_name, COUNT(*) AS n FROM tbl_proj p LEFT JOIN tbl_proj_type t ON t.prj_type_id = p.prj_type_id WHERE p.is_active = '1' GROUP BY type_name ORDER BY n DESC");
							$listing_type_max = $listing_types ? max(1, (int) $listing_types[0]['n']) : 1;
							$recent_projects = dash_rows($conn, "SELECT p.prj_id, p.proj_name, p.proj_location, p.proj_posted_on, COALESCE(t.proj_type_name, '') AS type_name FROM tbl_proj p LEFT JOIN tbl_proj_type t ON t.prj_type_id = p.prj_type_id WHERE p.is_active = '1' ORDER BY p.prj_id DESC LIMIT 5");

							// How complete the listing data is (numbers come from the AI facts table when it exists).
							$quality = [];
							if (dash_table_exists($conn, 'tbl_ai_proj_facts')) {
								$q = dash_rows($conn, "SELECT COUNT(*) AS total, SUM(f.price_aed IS NOT NULL) AS price, SUM(f.bedrooms_list <> '') AS bedrooms, SUM(f.size_sqft IS NOT NULL) AS size, SUM(f.handover_date IS NOT NULL OR f.handover_completed = 1) AS handover FROM tbl_proj p LEFT JOIN tbl_ai_proj_facts f ON f.prj_id = p.prj_id WHERE p.is_active = '1'");
								if ($q && (int) $q[0]['total'] > 0) {
									$total = (int) $q[0]['total'];
									$quality = [
										['Price', (int) $q[0]['price'], $total],
										['Bedrooms', (int) $q[0]['bedrooms'], $total],
										['Size', (int) $q[0]['size'], $total],
										['Handover date', (int) $q[0]['handover'], $total],
									];
								}
							}
							$missing_image = (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_proj WHERE is_active = '1' AND (prj_thumb_img IS NULL OR prj_thumb_img = '')");
							$missing_meta = (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_proj WHERE is_active = '1' AND (meta_description IS NULL OR TRIM(meta_description) = '')");

							// Site content
							$content_tiles = [
								['Developers', dash_value($conn, "SELECT COUNT(*) FROM tbl_developer WHERE is_active = '1'"), 'fa-solid fa-building', 'manage-developer.php'],
								['Communities', dash_value($conn, "SELECT COUNT(*) FROM tbl_community WHERE is_active = '1'"), 'fa-solid fa-map-location-dot', 'manage-community.php'],
								['Blogs', dash_value($conn, "SELECT COUNT(*) FROM tbl_blog WHERE is_active = '1'"), 'fa-solid fa-pen-nib', 'manage-blog.php'],
								['Company news', dash_value($conn, "SELECT COUNT(*) FROM tbl_news WHERE is_active = '1'"), 'fa-solid fa-newspaper', 'manage-company-news.php'],
								['Team', dash_value($conn, "SELECT COUNT(*) FROM tbl_team WHERE is_active = '1'"), 'fa-solid fa-users', 'manage-team.php'],
								['Testimonials', dash_value($conn, "SELECT COUNT(*) FROM tbl_testimonial WHERE is_active = '1'"), 'fa-solid fa-comment-dots', 'manage-testimonial.php'],
								['Awards', dash_value($conn, "SELECT COUNT(*) FROM tbl_awards WHERE is_active = '1'"), 'fa-solid fa-trophy', 'manage-awards.php'],
								['Media', dash_value($conn, "SELECT COUNT(*) FROM tbl_media WHERE is_active = '1'"), 'fa-solid fa-photo-film', 'manage-media.php'],
							];

							// AI assistant (only when its tables exist)
							$ai = null;
							if (dash_table_exists($conn, 'tbl_ai_usage') && dash_table_exists($conn, 'tbl_ai_chunks')) {
								$ai = [
									'chats_7' => (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_ai_conversations WHERE started_at >= CURDATE() - INTERVAL 6 DAY"),
									'messages_7' => (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_ai_usage WHERE feature = 'chat' AND created_at >= CURDATE() - INTERVAL 6 DAY"),
									'failed_7' => (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_ai_usage WHERE feature = 'chat' AND status NOT IN ('ok', 'started') AND created_at >= CURDATE() - INTERVAL 6 DAY"),
									'cost_30' => (float) dash_value($conn, "SELECT COALESCE(SUM(cost_usd), 0) FROM tbl_ai_usage WHERE created_at >= CURDATE() - INTERVAL 29 DAY"),
									'avg_seconds' => (float) dash_value($conn, "SELECT COALESCE(AVG(latency_ms), 0) / 1000 FROM tbl_ai_usage WHERE feature = 'chat' AND status = 'ok' AND created_at >= CURDATE() - INTERVAL 6 DAY"),
									'chunks' => (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_ai_chunks"),
									'embedded' => (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_ai_chunks WHERE embedding IS NOT NULL"),
									'last_run' => dash_rows($conn, "SELECT started_at, status FROM tbl_ai_index_log ORDER BY log_id DESC LIMIT 1"),
								];
							}

							// Sign-ins and changes
							$failed_logins_7 = (int) dash_value($conn, "SELECT COUNT(*) FROM tbl_login_logs WHERE login_status = 'FAILED' AND login_time >= CURDATE() - INTERVAL 6 DAY");
							$recent_logins = dash_rows($conn, "SELECT user_email, login_status, user_ip, login_time FROM tbl_login_logs ORDER BY login_log_id DESC LIMIT 5");
							$recent_changes = dash_rows($conn, "SELECT username, action_type, table_name, record_identifier, log_time FROM tbl_activity_logs ORDER BY log_id DESC LIMIT 6");

							$hour = (int) date('G');
							$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
							$table_labels = ['tbl_proj' => 'Project', 'tbl_blog' => 'Blog', 'tbl_news' => 'News', 'tbl_community' => 'Community', 'tbl_developer' => 'Developer', 'tbl_team' => 'Team member', 'tbl_login' => 'User'];
							?>
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=2" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-home">

								<!-- Greeting and shortcuts -->
								<div class="row">
									<div class="col-12 dre-col">
										<div class="dre-glass dre-glass-dark dre-hero">
											<div>
												<h1><?= $greeting ?>, <?= dash_e($_SESSION['username'] ?? '') ?></h1>
												<p><?= date('l, j F Y') ?> &middot; <?= number_format($listings_active) ?> live listings</p>
											</div>
											<div class="dre-actions">
												<a class="dre-primary" href="<?= BASE_URL ?>add-project.php"><i class="fa-solid fa-plus"></i> Add project</a>
												<a href="<?= BASE_URL ?>add-blog.php"><i class="fa-solid fa-pen"></i> Write blog</a>
												<a href="<?= BASE_URL_HOME ?>" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i> Open website</a>
											</div>
										</div>
									</div>
								</div>

								<!-- Main numbers -->
								<div class="row">
									<div class="col-xl-3 col-6 dre-col">
										<a class="dre-glass dre-glass-hover dre-tile" href="<?= BASE_URL ?>manage-project.php">
											<div class="dre-kpi">
												<div>
													<div class="dre-kpi-label">Live listings</div>
													<div class="dre-kpi-value"><?= number_format($listings_active) ?></div>
													<div class="dre-kpi-note"><?= number_format($listings_inactive) ?> hidden</div>
												</div>
												<span class="dre-icon"><i class="fa-solid fa-city"></i></span>
											</div>
										</a>
									</div>
									<div class="col-xl-3 col-6 dre-col">
										<a class="dre-glass dre-glass-hover dre-tile" href="<?= BASE_URL ?>manage-project.php">
											<div class="dre-kpi">
												<div>
													<div class="dre-kpi-label">Listings added, 30 days</div>
													<div class="dre-kpi-value"><?= number_format($listings_30) ?></div>
													<div class="dre-kpi-note">
														<?php if ($listings_change === null): ?>
															<?= number_format($listings_prev_30) ?> in the 30 days before
														<?php else: ?>
															<span class="<?= $listings_change >= 0 ? 'dre-up' : 'dre-down' ?>"><i class="fa-solid fa-arrow-<?= $listings_change >= 0 ? 'up' : 'down' ?>"></i> <?= abs($listings_change) ?>%</span> vs the 30 days before
														<?php endif; ?>
													</div>
												</div>
												<span class="dre-icon"><i class="fa-solid fa-chart-line"></i></span>
											</div>
										</a>
									</div>
									<div class="col-xl-3 col-6 dre-col">
										<a class="dre-glass dre-glass-hover dre-tile" href="<?= BASE_URL ?>manage-blog.php">
											<div class="dre-kpi">
												<div>
													<div class="dre-kpi-label">Blogs published</div>
													<div class="dre-kpi-value"><?= number_format($blogs_active) ?></div>
													<div class="dre-kpi-note"><?= number_format($blogs_30) ?> in the last 30 days</div>
												</div>
												<span class="dre-icon"><i class="fa-solid fa-pen-nib"></i></span>
											</div>
										</a>
									</div>
									<div class="col-xl-3 col-6 dre-col">
										<a class="dre-glass dre-glass-hover dre-tile" href="<?= BASE_URL ?>manage-resume.php">
											<div class="dre-kpi">
												<div>
													<div class="dre-kpi-label">Job applications, 30 days</div>
													<div class="dre-kpi-value"><?= number_format($resumes_30) ?></div>
													<div class="dre-kpi-note"><?= number_format($resumes_total) ?> in total</div>
												</div>
												<span class="dre-icon"><i class="fa-solid fa-file-lines"></i></span>
											</div>
										</a>
									</div>
								</div>

								<!-- Listings added over time, and by type -->
								<div class="row">
									<div class="col-xl-8 dre-col">
										<div class="dre-glass">
											<div class="dre-h">
												<h3>Listings added per month<small>Last 12 months, live listings only</small></h3>
												<a href="<?= BASE_URL ?>manage-project.php">All listings <i class="fa-solid fa-arrow-right"></i></a>
											</div>
											<div class="dre-bars" role="img" aria-label="Listings added per month for the last 12 months">
												<?php foreach ($months as $index => $month): ?>
													<div class="dre-bar<?= $index === 11 ? ' dre-bar-now' : '' ?>" title="<?= dash_e($month['title']) ?>: <?= number_format($month['count']) ?> listings">
														<div class="dre-bar-num"><?= $month['count'] > 0 ? number_format($month['count']) : '' ?></div>
														<div class="dre-bar-fill" style="height: <?= round($month['count'] * 100 / $month_max, 1) ?>%"></div>
														<div class="dre-bar-label"><?= dash_e($month['label']) ?></div>
													</div>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
									<div class="col-xl-4 dre-col">
										<div class="dre-glass">
											<div class="dre-h">
												<h3>Listings by type<small>Live on the website</small></h3>
											</div>
											<?php if (!$listing_types): ?>
												<div class="dre-empty">No live listings yet.</div>
											<?php endif; ?>
											<?php foreach ($listing_types as $type): ?>
												<div class="dre-meter">
													<div class="dre-meter-top"><span><?= dash_e($type['type_name']) ?></span><span><?= number_format($type['n']) ?></span></div>
													<div class="dre-track"><i style="width: <?= round($type['n'] * 100 / $listing_type_max, 1) ?>%"></i></div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</div>

								<!-- Newest listings, data to fix -->
								<div class="row">
									<div class="col-xl-7 dre-col">
										<div class="dre-glass">
											<div class="dre-h">
												<h3>Newest listings<small>Most recently added</small></h3>
												<a href="<?= BASE_URL ?>manage-project.php">All <i class="fa-solid fa-arrow-right"></i></a>
											</div>
											<?php if (!$recent_projects): ?>
												<div class="dre-empty">No live listings yet.</div>
											<?php else: ?>
												<ul class="dre-list">
													<?php foreach ($recent_projects as $project): ?>
														<li>
															<span class="dre-avatar"><i class="fa-solid fa-building"></i></span>
															<div class="dre-list-main">
																<strong><?= dash_e($project['proj_name']) ?></strong>
																<span><?= dash_e($project['proj_location']) ?></span>
															</div>
															<div class="dre-list-side">
																<?php if ($project['type_name'] !== ''): ?><span class="dre-pill"><?= dash_e($project['type_name']) ?></span><br><?php endif; ?>
																<?= dash_e(dash_ago($project['proj_posted_on'])) ?>
															</div>
														</li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-xl-5 dre-col">
										<div class="dre-glass">
											<div class="dre-h">
												<h3>Listing data to complete<small>Share of live listings that have each detail</small></h3>
												<a href="<?= BASE_URL ?>manage-project.php">Fix <i class="fa-solid fa-arrow-right"></i></a>
											</div>
											<?php foreach ($quality as $item):
												$percent = dash_percent($item[1], $item[2]);
												$tone = $percent >= 70 ? 'dre-good' : ($percent >= 40 ? 'dre-warn' : 'dre-bad'); ?>
												<div class="dre-meter">
													<div class="dre-meter-top"><span><?= dash_e($item[0]) ?></span><span><?= $percent ?>% &middot; <?= number_format($item[2] - $item[1]) ?> missing</span></div>
													<div class="dre-track"><i class="<?= $tone ?>" style="width: <?= $percent ?>%"></i></div>
												</div>
											<?php endforeach; ?>
											<?php if (!$quality): ?>
												<div class="dre-empty">Run ai/cron/build_proj_facts.php to see this.</div>
											<?php endif; ?>
											<div class="dre-kpi-note" style="margin-top: 14px;">
												<?= number_format($missing_image) ?> without a thumbnail image &middot; <?= number_format($missing_meta) ?> without a meta description
											</div>
										</div>
									</div>
								</div>

								<!-- Site content -->
								<div class="row">
									<div class="col-12 dre-col">
										<div class="dre-glass">
											<div class="dre-h">
												<h3>Website content<small>Live items &middot; click to manage</small></h3>
											</div>
											<div class="dre-mini">
												<?php foreach ($content_tiles as $tile): ?>
													<a href="<?= BASE_URL . $tile[3] ?>">
														<i class="<?= $tile[2] ?>"></i>
														<strong><?= number_format((int) $tile[1]) ?></strong>
														<span><?= dash_e($tile[0]) ?></span>
													</a>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
								</div>

								<!-- AI assistant, sign-ins, changes -->
								<div class="row">
									<div class="col-xl-4 col-md-6 dre-col">
										<div class="dre-glass">
											<div class="dre-h">
												<h3>AI assistant<small>Website chat, last 7 days</small></h3>
												<?php if ((int) ($_SESSION['login_type'] ?? 0) === 1): ?>
													<a href="<?= BASE_URL ?>manage-ai-chats.php">Read the chats <i class="fa-solid fa-arrow-right"></i></a>
												<?php else: ?>
													<span class="dre-pill dre-pill-ai"><i class="fa-solid fa-wand-magic-sparkles"></i> AI</span>
												<?php endif; ?>
											</div>
											<?php if ($ai === null): ?>
												<div class="dre-empty">The AI tables are not installed on this database yet.</div>
											<?php else:
												$embedded_percent = dash_percent($ai['embedded'], $ai['chunks']);
												$failed_percent = dash_percent($ai['failed_7'], $ai['messages_7']); ?>
												<div class="dre-facts">
													<div><strong><?= number_format($ai['chats_7']) ?></strong><span>Chats started</span></div>
													<div><strong><?= number_format($ai['messages_7']) ?></strong><span>Questions asked</span></div>
													<div><strong>$<?= number_format($ai['cost_30'], 2) ?></strong><span>Estimated cost, 30 days</span></div>
													<div><strong><?= $ai['avg_seconds'] > 0 ? number_format($ai['avg_seconds'], 1) . ' s' : '-' ?></strong><span>Average answer time</span></div>
												</div>
												<div class="dre-meter">
													<div class="dre-meter-top"><span>Answers that failed</span><span><?= $failed_percent ?>% &middot; <?= number_format($ai['failed_7']) ?> of <?= number_format($ai['messages_7']) ?></span></div>
													<div class="dre-track"><i class="<?= $failed_percent <= 5 ? 'dre-good' : ($failed_percent <= 20 ? 'dre-warn' : 'dre-bad') ?>" style="width: <?= max($failed_percent, $ai['messages_7'] > 0 ? 2 : 0) ?>%"></i></div>
												</div>
												<div class="dre-meter">
													<div class="dre-meter-top"><span>Search index ready</span><span><?= $embedded_percent ?>% &middot; <?= number_format($ai['embedded']) ?> of <?= number_format($ai['chunks']) ?></span></div>
													<div class="dre-track"><i class="<?= $embedded_percent >= 95 ? 'dre-good' : 'dre-warn' ?>" style="width: <?= $embedded_percent ?>%"></i></div>
												</div>
												<?php if ($ai['last_run']): ?>
													<div class="dre-kpi-note" style="margin-top: 14px;">Search index last updated <?= dash_e(dash_ago($ai['last_run'][0]['started_at'])) ?></div>
												<?php endif; ?>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-xl-4 col-md-6 dre-col">
										<div class="dre-glass">
											<div class="dre-h">
												<h3>Admin sign-ins<small><?= number_format($failed_logins_7) ?> failed in the last 7 days</small></h3>
												<a href="<?= BASE_URL ?>manage-login-logs.php">Log <i class="fa-solid fa-arrow-right"></i></a>
											</div>
											<?php if (!$recent_logins): ?>
												<div class="dre-empty">No sign-ins recorded.</div>
											<?php else: ?>
												<ul class="dre-list">
													<?php foreach ($recent_logins as $login): ?>
														<li>
															<span class="dre-avatar"><i class="fa-solid fa-<?= $login['login_status'] === 'SUCCESS' ? 'right-to-bracket' : 'triangle-exclamation' ?>"></i></span>
															<div class="dre-list-main">
																<strong><?= dash_e($login['user_email']) ?></strong>
																<span><?= dash_e($login['user_ip']) ?></span>
															</div>
															<div class="dre-list-side">
																<span class="dre-pill <?= $login['login_status'] === 'SUCCESS' ? 'dre-pill-good' : 'dre-pill-bad' ?>"><?= $login['login_status'] === 'SUCCESS' ? 'Signed in' : 'Failed' ?></span><br>
																<?= dash_e(dash_ago($login['login_time'])) ?>
															</div>
														</li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-xl-4 dre-col">
										<div class="dre-glass">
											<div class="dre-h">
												<h3>Latest changes<small>Who changed what in the admin</small></h3>
												<a href="<?= BASE_URL ?>manage-loggs.php">Log <i class="fa-solid fa-arrow-right"></i></a>
											</div>
											<?php if (!$recent_changes): ?>
												<div class="dre-empty">No changes recorded.</div>
											<?php else: ?>
												<ul class="dre-list">
													<?php foreach ($recent_changes as $change):
														$action = strtoupper((string) $change['action_type']);
														$verb = $action === 'INSERT' ? 'Added' : ($action === 'DELETE' ? 'Deleted' : 'Updated');
														$thing = $table_labels[$change['table_name']] ?? ucfirst(str_replace('_', ' ', preg_replace('/^tbl_/', '', (string) $change['table_name']))); ?>
														<li>
															<span class="dre-avatar"><i class="fa-solid fa-<?= $action === 'INSERT' ? 'plus' : ($action === 'DELETE' ? 'trash' : 'pen') ?>"></i></span>
															<div class="dre-list-main">
																<strong><?= dash_e($verb . ' ' . strtolower($thing)) ?></strong>
																<span><?= dash_e(trim((string) $change['username']) !== '' ? $change['username'] : 'Unknown user') ?><?= trim((string) $change['record_identifier']) !== '' ? ' &middot; ' . dash_e($change['record_identifier']) : '' ?></span>
															</div>
															<div class="dre-list-side"><?= dash_e(dash_ago($change['log_time'])) ?></div>
														</li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
										</div>
									</div>
								</div>

							</div>
							<!--end::Dashboard-->
						</div>
						<!--end::Container-->
					</div>
					<!--end::Entry-->
				</div>
				<!--end::Content-->
				<?php
				include('includes/footer-inner.php');
				?>