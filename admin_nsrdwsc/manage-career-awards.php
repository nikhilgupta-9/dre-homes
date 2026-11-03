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

const MEDIA_FOLDER = 'assets/media/career/awards/';
const MEDIA_IMAGE = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
const MEDIA_IMAGE_MAX = 5 * 1024 * 1024;
const MEDIA_BATCH = 20;   // pictures per upload; the server accepts at most this many files in one request

$media_sorts = ['newest' => 'Newest first', 'oldest' => 'Oldest first'];

// ---------------------------------------------------------------
// Filters (all optional, all from the address bar)
// ---------------------------------------------------------------
$f_status = in_array($_GET['status'] ?? '', ['active', 'inactive'], true) ? $_GET['status'] : '';
$f_noalt = ($_GET['noalt'] ?? '') === '1' ? '1' : '';
$f_sort = isset($_GET['sort']) && is_string($_GET['sort']) && isset($media_sorts[$_GET['sort']]) ? $_GET['sort'] : 'newest';
$f_search = isset($_GET['q']) && is_string($_GET['q']) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$f_per_page = (int) ($_GET['per'] ?? 48);
if (!in_array($f_per_page, [48, 96], true)) {
	$f_per_page = 48;
}
$f_page = max(1, (int) ($_GET['page'] ?? 1));

// Address of this page with the current filters, with some of them changed.
function media_url(array $change = [])
{
	global $f_status, $f_noalt, $f_sort, $f_search, $f_per_page, $f_page;
	$params = array_merge([
		'status' => $f_status, 'noalt' => $f_noalt, 'sort' => $f_sort === 'newest' ? '' : $f_sort, 'q' => $f_search,
		'per' => $f_per_page === 48 ? '' : $f_per_page, 'page' => $f_page === 1 ? '' : $f_page,
	], $change);
	$params = array_filter($params, function ($value) {
		return $value !== '' && $value !== null;
	});
	return BASE_URL . 'manage-career-awards.php' . ($params ? '?' . http_build_query($params) : '');
}

function media_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function media_query($conn, $sql, array $params = [])
{
	$stmt = mysqli_prepare($conn, $sql);
	if (!$stmt) {
		error_log('Careers awards query failed: ' . mysqli_error($conn));
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
		error_log('Careers awards query failed: ' . mysqli_stmt_error($stmt));
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

// ---------------------------------------------------------------
// Changes (POST only, with a token)
// ---------------------------------------------------------------
$notice = '';
$upload_errors = [];
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['media_action'])) {
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$action = is_string($_POST['media_action']) ? $_POST['media_action'] : '';
	$done = '';
	// One picture, or every ticked picture.
	$ids = isset($_POST['media_id']) ? [(int) $_POST['media_id']] : array_map('intval', (array) ($_POST['media_ids'] ?? []));
	$ids = array_slice(array_unique(array_filter($ids)), 0, 200);

	if ($token_ok && $action === 'upload') {
		// Every picture is checked by extension and by its real content, and stored under a name
		// made here. Pictures that pass are added; the others are listed with the reason.
		$alt = is_string($_POST['alt'] ?? null) ? mb_substr(trim($_POST['alt']), 0, 255) : '';
		$live = isset($_POST['is_active']) ? '1' : '2';
		$files = $_FILES['pictures'] ?? null;
		$added = 0;
		if ($files && is_array($files['name'])) {
			if (!is_dir(MEDIA_FOLDER)) {
				mkdir(MEDIA_FOLDER, 0755, true);
			}
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			foreach (array_slice($files['name'], 0, MEDIA_BATCH, true) as $i => $name) {
				if ($files['error'][$i] === UPLOAD_ERR_NO_FILE || (string) $name === '') {
					continue;
				}
				$label = '"' . mb_strimwidth((string) $name, 0, 60, '...') . '"';
				if ($files['error'][$i] === UPLOAD_ERR_INI_SIZE || $files['error'][$i] === UPLOAD_ERR_FORM_SIZE || $files['size'][$i] > MEDIA_IMAGE_MAX) {
					$upload_errors[] = "$label is larger than 5 MB.";
					continue;
				}
				if ($files['error'][$i] !== UPLOAD_ERR_OK || !is_uploaded_file($files['tmp_name'][$i])) {
					$upload_errors[] = "$label could not be uploaded.";
					continue;
				}
				$extension = strtolower(pathinfo((string) $name, PATHINFO_EXTENSION));
				$real_extension = array_search($finfo->file($files['tmp_name'][$i]), ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'], true);
				if (!isset(MEDIA_IMAGE[$extension]) || $real_extension === false) {
					$upload_errors[] = "$label is not a JPG, PNG, GIF or WEBP picture.";
					continue;
				}
				$stored = 'carr_award_img_' . bin2hex(random_bytes(6)) . '.' . $real_extension;
				if (!move_uploaded_file($files['tmp_name'][$i], MEDIA_FOLDER . $stored)) {
					$upload_errors[] = "$label could not be stored on the server.";
					continue;
				}
				if (media_query($conn, "INSERT INTO tbl_career_awards (carr_award_img, carr_award_img_alt_tag, is_active, tstp) VALUES (?, ?, ?, NOW())", [$stored, $alt, $live])) {
					$added++;
				} else {
					@unlink(MEDIA_FOLDER . $stored);
					$upload_errors[] = "$label could not be saved.";
				}
			}
			if (count($files['name']) > MEDIA_BATCH) {
				$upload_errors[] = 'Only the first ' . MEDIA_BATCH . ' pictures were looked at. Upload the rest in another batch.';
			}
		}
		if ($added > 0) {
			$logger->log('INSERT', 'tbl_career_awards', 0, null, ['pictures_added' => $added], 'Careers awards: ' . $added . ($added === 1 ? ' picture' : ' pictures') . ' uploaded');
		}
		if (!$upload_errors) {
			$done = $added > 0 ? 'uploaded-' . $added : 'none';
		} else {
			// Stay on the page so the reasons can be read.
			$notice = $added > 0 ? $added . ($added === 1 ? ' picture was' : ' pictures were') . ' added. The others were not:' : 'No pictures were added:';
		}
	} elseif ($token_ok && $action === 'alts') {
		// Descriptions typed in the grid: only the ones that changed are written.
		$changed = 0;
		foreach ((array) ($_POST['alt_text'] ?? []) as $id => $text) {
			$id = (int) $id;
			$text = is_string($text) ? mb_substr(trim($text), 0, 255) : '';
			$row = $id > 0 ? media_query($conn, "SELECT carr_award_img_alt_tag FROM tbl_career_awards WHERE carr_award_id = ?", [$id]) : [];
			if ($row && $row[0]['carr_award_img_alt_tag'] !== $text && media_query($conn, "UPDATE tbl_career_awards SET carr_award_img_alt_tag = ? WHERE carr_award_id = ?", [$text, $id])) {
				$changed++;
			}
		}
		if ($changed > 0) {
			$logger->log('UPDATE', 'tbl_career_awards', 0, null, ['descriptions_changed' => $changed], 'Careers awards: ' . $changed . ($changed === 1 ? ' description' : ' descriptions') . ' changed');
		}
		$done = $changed > 0 ? 'alts-' . $changed : 'none';
	} elseif ($token_ok && ($action === 'show' || $action === 'hide')) {
		$changed = 0;
		foreach ($ids as $id) {
			$value = $action === 'show' ? '1' : '2';
			$row = media_query($conn, "SELECT is_active FROM tbl_career_awards WHERE carr_award_id = ?", [$id]);
			if ($row && $row[0]['is_active'] !== $value && media_query($conn, "UPDATE tbl_career_awards SET is_active = ? WHERE carr_award_id = ?", [$value, $id])) {
				$changed++;
			}
		}
		if ($changed > 0) {
			$logger->log('UPDATE', 'tbl_career_awards', 0, null, ['is_active' => $action === 'show' ? '1' : '2', 'pictures' => $changed], 'Careers awards: ' . $changed . ($changed === 1 ? ' picture ' : ' pictures ') . ($action === 'show' ? 'shown' : 'hidden'));
		}
		$done = $changed > 0 ? ($action === 'show' ? 'shown' : 'hidden') . '-' . $changed : 'none';
	} elseif ($token_ok && $action === 'delete') {
		$deleted = 0;
		foreach ($ids as $id) {
			$row = media_query($conn, "SELECT * FROM tbl_career_awards WHERE carr_award_id = ?", [$id]);
			if (!$row) {
				continue;
			}
			$old_data = $logger->getOldRecordData('tbl_career_awards', $id);
			$logger->log('DELETE', 'tbl_career_awards', $id, $old_data, null, 'Careers awards picture deleted');
			$logger->archiveDeletedRecord('tbl_career_awards', $id, $old_data);
			if (media_query($conn, "DELETE FROM tbl_career_awards WHERE carr_award_id = ?", [$id])) {
				// The file is removed only when a name is stored and the file is really there.
				$name = basename(trim((string) $row[0]['carr_award_img']));
				if ($name !== '' && is_file(MEDIA_FOLDER . $name)) {
					@unlink(MEDIA_FOLDER . $name);
				}
				$deleted++;
			}
		}
		$done = $deleted > 0 ? 'deleted-' . $deleted : 'none';
	}

	if ($done !== '') {
		header('Location: ' . media_url(['done' => $done] + ($action === 'upload' ? ['page' => '', 'sort' => ''] : [])));
		exit;
	}
	if ($notice === '') {
		$notice = 'Nothing was changed. Please reload the page and try again.';
	}
}

// ---------------------------------------------------------------
// The grid
// ---------------------------------------------------------------
$where = ['1 = 1'];
$params = [];
if ($f_search !== '') {
	$like = '%' . addcslashes($f_search, '%_\\') . '%';
	$where[] = '(m.carr_award_img_alt_tag LIKE ? OR m.carr_award_img LIKE ?)';
	array_push($params, $like, $like);
}
if ($f_status !== '') {
	$where[] = $f_status === 'active' ? "m.is_active = '1'" : "m.is_active <> '1'";
}
if ($f_noalt !== '') {
	$where[] = "TRIM(m.carr_award_img_alt_tag) = ''";
}
$where_sql = 'WHERE ' . implode(' AND ', $where);

$counted = media_query($conn, "SELECT COUNT(*) AS n FROM tbl_career_awards AS m $where_sql", $params);
$total = $counted ? (int) $counted[0]['n'] : 0;
$pages = max(1, (int) ceil($total / $f_per_page));
$f_page = min($f_page, $pages);
$offset = ($f_page - 1) * $f_per_page;

$pictures = media_query(
	$conn,
	"SELECT m.carr_award_id AS media_id, m.carr_award_img AS media_img, m.carr_award_img_alt_tag AS media_img_alt_tag, m.is_active, m.tstp FROM tbl_career_awards AS m $where_sql ORDER BY m.carr_award_id " . ($f_sort === 'oldest' ? 'ASC' : 'DESC') . " LIMIT ?, ?",
	array_merge($params, [$offset, $f_per_page])
) ?: [];

$stat = media_query($conn, "SELECT COUNT(*) AS total, SUM(is_active = '1') AS active, SUM(is_active <> '1') AS hidden, SUM(TRIM(carr_award_img_alt_tag) = '') AS no_alt, SUM(tstp >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS recent FROM tbl_career_awards");
$stat = $stat ? $stat[0] : ['total' => 0, 'active' => 0, 'hidden' => 0, 'no_alt' => 0, 'recent' => 0];

$filters_on = $f_search !== '' || $f_status !== '' || $f_noalt !== '';

// Messages after an action
$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : '';
if (preg_match('/^(uploaded|alts|shown|hidden|deleted)-(\d+)$/', $done, $match)) {
	$n = (int) $match[2];
	$notice = [
		'uploaded' => $n . ($n === 1 ? ' picture was' : ' pictures were') . ' added.',
		'alts' => $n . ($n === 1 ? ' description was' : ' descriptions were') . ' saved.',
		'shown' => $n . ($n === 1 ? ' picture is' : ' pictures are') . ' now live on the website.',
		'hidden' => $n . ($n === 1 ? ' picture is' : ' pictures are') . ' now hidden from the website.',
		'deleted' => $n . ($n === 1 ? ' picture was' : ' pictures were') . ' deleted. A copy of each record is kept in the delete log.',
	][$match[1]];
} elseif ($done === 'none') {
	$notice = 'Nothing was changed.';
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

							<!--begin::Careers awards-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-media.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-media">

								<!-- Title, numbers, main actions -->
								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1>Careers page awards</h1>
										<p>The award logos shown on the Careers page.</p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= number_format((int) $stat['active']) ?></strong><span>Live</span></div>
										<div><strong><?= number_format((int) $stat['hidden']) ?></strong><span>Hidden</span></div>
										<div><strong><?= number_format((int) $stat['no_alt']) ?></strong><span>No description</span></div>
										<div><strong><?= number_format((int) $stat['recent']) ?></strong><span>Added in 30 days</span></div>
									</div>
									<div class="dre-actions">
										<a class="dre-primary" href="#mediaUpload" id="mediaUploadLink"><i class="bi bi-cloud-arrow-up"></i> Add pictures</a>
										<a href="<?= BASE_URL ?>edit-career-content.php?edit=<?= base64_encode(1) ?>"><i class="bi bi-window"></i> Careers page content</a>
										<a href="<?= media_e(BASE_URL_HOME) ?>careers" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View the page</a>
									</div>
								</div>

								<?php if ($notice !== ''): ?>
									<div class="dre-leads-notice<?= $upload_errors ? ' dre-media-problems' : '' ?>" role="status" id="mediaNotice">
										<i class="bi bi-info-circle"></i> <?= media_e($notice) ?>
										<?php if ($upload_errors): ?>
											<ul>
												<?php foreach ($upload_errors as $error): ?>
													<li><?= media_e($error) ?></li>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<!-- Add pictures: several at once -->
								<form method="post" action="<?= media_e(media_url()) ?>" enctype="multipart/form-data" class="dre-glass dre-media-upload" id="mediaUpload">
									<input type="hidden" name="csrf" value="<?= media_e($_SESSION['admin_csrf']) ?>">
									<input type="hidden" name="media_action" value="upload">
									<label class="dre-media-drop" id="mediaDrop">
										<i class="bi bi-images"></i>
										<strong>Choose pictures, or drop them here</strong>
										<span>Up to <?= MEDIA_BATCH ?> at a time. JPG, PNG, GIF or WEBP, up to 5 MB each.</span>
										<input type="file" name="pictures[]" id="mediaFiles" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
									</label>
									<div class="dre-media-picked" id="mediaPicked" hidden>
										<div class="dre-media-thumbs" id="mediaThumbs"></div>
										<p id="mediaPickedNote"></p>
										<div class="dre-media-upload-fields">
											<label>
												<span>Description for these pictures (alt text)</span>
												<input type="text" name="alt" maxlength="255" autocomplete="off" placeholder="Great Place to Work certified 2026">
											</label>
											<label class="dre-proj-check"><input type="checkbox" name="is_active" value="1" checked> Show on the website straight away</label>
											<div>
												<button type="submit" class="dre-btn dre-btn-main" id="mediaUploadButton"><i class="bi bi-cloud-arrow-up"></i> Upload</button>
												<button type="button" class="dre-btn" id="mediaUploadClear">Cancel</button>
											</div>
										</div>
									</div>
								</form>

								<!-- Search and filters -->
								<form method="get" action="<?= BASE_URL ?>manage-career-awards.php" class="dre-glass dre-leads-filter dre-proj-filter">
									<label class="dre-leads-search">
										<span>Search</span>
										<i class="bi bi-search"></i>
										<input type="search" name="q" value="<?= media_e($f_search) ?>" placeholder="Description or file name" autocomplete="off">
									</label>
									<label>
										<span>Website status</span>
										<select name="status">
											<option value="">Any</option>
											<option value="active" <?= $f_status === 'active' ? 'selected' : '' ?>>Live</option>
											<option value="inactive" <?= $f_status === 'inactive' ? 'selected' : '' ?>>Hidden</option>
										</select>
									</label>
									<label>
										<span>Sort by</span>
										<select name="sort">
											<?php foreach ($media_sorts as $key => $label): ?>
												<option value="<?= $key ?>" <?= $f_sort === $key ? 'selected' : '' ?>><?= media_e($label) ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label>
										<span>Per page</span>
										<select name="per">
											<?php foreach ([48, 96] as $option): ?>
												<option value="<?= $option ?>" <?= $f_per_page === $option ? 'selected' : '' ?>><?= $option ?></option>
											<?php endforeach; ?>
										</select>
									</label>
									<label class="dre-proj-check">
										<input type="checkbox" name="noalt" value="1" <?= $f_noalt !== '' ? 'checked' : '' ?>> Without a description only
									</label>
									<div class="dre-leads-filter-buttons">
										<button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-funnel"></i> Apply</button>
										<?php if ($filters_on || $f_sort !== 'newest'): ?>
											<a class="dre-btn" href="<?= BASE_URL ?>manage-career-awards.php">Clear</a>
										<?php endif; ?>
									</div>
								</form>

								<!-- The form the ticked pictures and the typed descriptions belong to. Its buttons choose what happens. -->
								<form method="post" action="<?= media_e(media_url()) ?>" id="mediaGridForm">
									<input type="hidden" name="csrf" value="<?= media_e($_SESSION['admin_csrf']) ?>">
								</form>

								<!-- The grid -->
								<div class="dre-glass dre-leads-list">
									<div class="dre-h">
										<h3>
											<?= number_format($total) ?> <?= $total === 1 ? 'picture' : 'pictures' ?>
											<small>
												<?php if ($total > 0): ?>
													Showing <?= number_format($offset + 1) ?>&ndash;<?= number_format($offset + count($pictures)) ?><?= $filters_on ? ' &middot; filtered' : '' ?>
												<?php else: ?>
													Nothing matches<?= $filters_on ? ' these filters' : '' ?>
												<?php endif; ?>
											</small>
										</h3>
										<?php if ($pictures): ?>
											<label class="dre-proj-all"><input type="checkbox" id="mediaSelectAll"> Select all on this page</label>
										<?php endif; ?>
									</div>

									<?php if (!$pictures): ?>
										<div class="dre-empty">
											<i class="bi bi-images" style="font-size: 2rem;"></i><br>
											No pictures to show.
											<?php if ($filters_on): ?>
												<a href="<?= BASE_URL ?>manage-career-awards.php">Show all pictures</a>
											<?php endif; ?>
										</div>
									<?php else: ?>
										<div class="dre-media-grid">
											<?php foreach ($pictures as $row):
												$id = (int) $row['media_id'];
												$is_live = $row['is_active'] === '1';
												$url = BASE_URL . MEDIA_FOLDER . rawurlencode((string) $row['media_img']);
											?>
												<div class="dre-media-card<?= $is_live ? '' : ' is-hidden' ?>">
													<div class="dre-media-frame">
														<a href="<?= media_e($url) ?>" target="_blank" rel="noopener" title="Open the picture"><img src="<?= media_e($url) ?>" alt="<?= media_e($row['media_img_alt_tag']) ?>" loading="lazy" onerror="this.closest('.dre-media-card').classList.add('is-missing'); this.remove()"></a>
														<i class="bi bi-image dre-media-broken" aria-hidden="true"></i>
														<input type="checkbox" name="media_ids[]" value="<?= $id ?>" form="mediaGridForm" class="dre-proj-box" aria-label="Select picture <?= $id ?>">
														<?php if (!$is_live): ?><span class="dre-pill dre-pill-bad">Hidden</span><?php endif; ?>
													</div>
													<input type="text" name="alt_text[<?= $id ?>]" value="<?= media_e($row['media_img_alt_tag']) ?>" form="mediaGridForm" maxlength="255" class="dre-media-alt<?= trim((string) $row['media_img_alt_tag']) === '' ? ' is-empty' : '' ?>" placeholder="Describe this picture" aria-label="Description of picture <?= $id ?>">
													<div class="dre-media-actions">
														<span title="Added"><?= strtotime((string) $row['tstp']) > 0 ? date('d M Y', strtotime($row['tstp'])) : '' ?></span>
														<form method="post" action="<?= media_e(media_url()) ?>">
															<input type="hidden" name="csrf" value="<?= media_e($_SESSION['admin_csrf']) ?>">
															<input type="hidden" name="media_id" value="<?= $id ?>">
															<button type="submit" name="media_action" value="<?= $is_live ? 'hide' : 'show' ?>" class="dre-icon-btn" title="<?= $is_live ? 'Live on the website. Click to hide.' : 'Hidden. Click to show on the website.' ?>" aria-label="<?= $is_live ? 'Hide' : 'Show' ?> picture <?= $id ?>"><i class="bi <?= $is_live ? 'bi-eye' : 'bi-eye-slash' ?>"></i></button>
															<button type="submit" name="media_action" value="delete" class="dre-icon-btn dre-media-delete" title="Delete" aria-label="Delete picture <?= $id ?>"><i class="bi bi-trash3"></i></button>
														</form>
													</div>
												</div>
											<?php endforeach; ?>
										</div>

										<?php if ($pages > 1): ?>
											<nav class="dre-leads-pages" aria-label="Pages">
												<?php if ($f_page > 1): ?>
													<a href="<?= media_e(media_url(['page' => $f_page - 1])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
												<?php endif; ?>
												<?php
												$shown = [];
												foreach ([1, 2, $f_page - 1, $f_page, $f_page + 1, $pages - 1, $pages] as $number) {
													if ($number >= 1 && $number <= $pages) {
														$shown[$number] = true;
													}
												}
												ksort($shown);
												$previous = 0;
												foreach (array_keys($shown) as $number):
													if ($number > $previous + 1): ?><span>&hellip;</span><?php endif; ?>
													<a href="<?= media_e(media_url(['page' => $number])) ?>" class="<?= $number === $f_page ? 'is-on' : '' ?>" <?= $number === $f_page ? 'aria-current="page"' : '' ?>><?= $number ?></a>
												<?php $previous = $number;
												endforeach; ?>
												<?php if ($f_page < $pages): ?>
													<a href="<?= media_e(media_url(['page' => $f_page + 1])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
												<?php endif; ?>
											</nav>
										<?php endif; ?>
									<?php endif; ?>
								</div>

								<!-- Appears when a picture is ticked or a description is changed -->
								<div class="dre-proj-bulk" id="mediaBulkBar" hidden>
									<strong id="mediaBulkText"></strong>
									<div>
										<button type="submit" form="mediaGridForm" name="media_action" value="alts" class="dre-btn dre-btn-main" id="mediaSaveAlts" hidden><i class="bi bi-check2"></i> Save descriptions</button>
										<button type="submit" form="mediaGridForm" name="media_action" value="show" class="dre-btn" data-needs-pick><i class="bi bi-eye"></i> Show</button>
										<button type="submit" form="mediaGridForm" name="media_action" value="hide" class="dre-btn" data-needs-pick><i class="bi bi-eye-slash"></i> Hide</button>
										<button type="submit" form="mediaGridForm" name="media_action" value="delete" class="dre-btn" data-needs-pick data-confirm="Delete the selected pictures? The files are removed too. A copy of each record is kept in the delete log."><i class="bi bi-trash3"></i> Delete</button>
										<button type="button" class="dre-btn" id="mediaBulkClear">Clear</button>
									</div>
								</div>
							</div>

							<script>
								// Careers awards: picking pictures to upload, the selection bar, and changed descriptions.
								(function () {
									var maxFiles = <?= MEDIA_BATCH ?>;
									var maxBytes = <?= MEDIA_IMAGE_MAX ?>;

									// --- Upload: show what was picked, and say straight away what cannot be used ---
									var files = document.getElementById('mediaFiles');
									var drop = document.getElementById('mediaDrop');
									var picked = document.getElementById('mediaPicked');
									var thumbs = document.getElementById('mediaThumbs');
									var pickedNote = document.getElementById('mediaPickedNote');
									var uploadButton = document.getElementById('mediaUploadButton');
									function showPicked() {
										thumbs.textContent = '';
										var good = 0;
										var problems = [];
										Array.prototype.forEach.call(files.files, function (file, index) {
											if (index >= maxFiles) {
												return;
											}
											if (['image/jpeg', 'image/png', 'image/gif', 'image/webp'].indexOf(file.type) === -1) {
												problems.push('"' + file.name + '" is not a JPG, PNG, GIF or WEBP picture');
											} else if (file.size > maxBytes) {
												problems.push('"' + file.name + '" is larger than 5 MB');
											} else {
												var img = document.createElement('img');
												img.src = URL.createObjectURL(file);
												img.alt = '';
												thumbs.appendChild(img);
												good++;
											}
										});
										if (files.files.length > maxFiles) {
											problems.push('only the first ' + maxFiles + ' pictures will be uploaded');
										}
										picked.hidden = files.files.length === 0;
										pickedNote.textContent = good + (good === 1 ? ' picture' : ' pictures') + ' ready to upload.' + (problems.length ? ' Note: ' + problems.join('; ') + '.' : '');
										pickedNote.classList.toggle('dre-media-warn', problems.length > 0);
										uploadButton.disabled = good === 0;
									}
									files.addEventListener('change', showPicked);
									['dragenter', 'dragover'].forEach(function (name) {
										drop.addEventListener(name, function (event) { event.preventDefault(); drop.classList.add('is-over'); });
									});
									['dragleave', 'drop'].forEach(function (name) {
										drop.addEventListener(name, function (event) { event.preventDefault(); drop.classList.remove('is-over'); });
									});
									drop.addEventListener('drop', function (event) {
										if (event.dataTransfer && event.dataTransfer.files.length) {
											files.files = event.dataTransfer.files;
											showPicked();
										}
									});
									document.getElementById('mediaUploadClear').addEventListener('click', function () {
										files.value = '';
										showPicked();
									});
									document.getElementById('mediaUpload').addEventListener('submit', function () {
										uploadButton.textContent = 'Uploading...';
										// Disabled a moment later so the form is still sent.
										setTimeout(function () { uploadButton.disabled = true; }, 0);
									});
									document.getElementById('mediaUploadLink').addEventListener('click', function (event) {
										event.preventDefault();
										document.getElementById('mediaUpload').scrollIntoView({ block: 'center' });
										files.click();
									});

									// --- Grid: ticked pictures and changed descriptions share one bar ---
									var boxes = Array.prototype.slice.call(document.querySelectorAll('.dre-media-grid .dre-proj-box'));
									var alts = Array.prototype.slice.call(document.querySelectorAll('.dre-media-alt'));
									var all = document.getElementById('mediaSelectAll');
									var bar = document.getElementById('mediaBulkBar');
									var barText = document.getElementById('mediaBulkText');
									var saveAlts = document.getElementById('mediaSaveAlts');
									function refresh() {
										var pickedCount = boxes.filter(function (box) { return box.checked; }).length;
										var changed = alts.filter(function (input) { return input.value !== input.defaultValue; }).length;
										var parts = [];
										if (pickedCount > 0) { parts.push(pickedCount + ' selected'); }
										if (changed > 0) { parts.push(changed + (changed === 1 ? ' description changed' : ' descriptions changed')); }
										barText.textContent = parts.join(' · ');
										bar.hidden = pickedCount === 0 && changed === 0;
										saveAlts.hidden = changed === 0;
										bar.querySelectorAll('[data-needs-pick]').forEach(function (button) { button.hidden = pickedCount === 0; });
										boxes.forEach(function (box) { box.closest('.dre-media-card').classList.toggle('is-picked', box.checked); });
										if (all) {
											all.checked = pickedCount > 0 && pickedCount === boxes.length;
											all.indeterminate = pickedCount > 0 && pickedCount < boxes.length;
										}
										return changed;
									}
									boxes.forEach(function (box) { box.addEventListener('change', refresh); });
									alts.forEach(function (input) {
										input.addEventListener('input', function () {
											input.classList.toggle('is-changed', input.value !== input.defaultValue);
											input.classList.toggle('is-empty', input.value.trim() === '');
											refresh();
										});
										// Enter saves the descriptions instead of doing nothing.
										input.addEventListener('keydown', function (event) {
											if (event.key === 'Enter') {
												event.preventDefault();
												if (refresh() > 0) { saveAlts.click(); }
											}
										});
									});
									if (all) {
										all.addEventListener('change', function () {
											boxes.forEach(function (box) { box.checked = all.checked; });
											refresh();
										});
									}
									document.getElementById('mediaBulkClear').addEventListener('click', function () {
										boxes.forEach(function (box) { box.checked = false; });
										alts.forEach(function (input) { input.value = input.defaultValue; input.classList.remove('is-changed'); });
										refresh();
									});
									bar.querySelectorAll('[data-confirm]').forEach(function (button) {
										button.addEventListener('click', function (event) {
											if (!window.confirm(button.getAttribute('data-confirm'))) {
												event.preventDefault();
											}
										});
									});
									document.querySelectorAll('.dre-media-delete').forEach(function (button) {
										button.addEventListener('click', function (event) {
											if (!window.confirm('Delete this picture? The file is removed too. A copy of the record is kept in the delete log.')) {
												event.preventDefault();
											}
										});
									});
									var leaving = false;
									document.getElementById('mediaGridForm').addEventListener('submit', function () { leaving = true; });
									window.addEventListener('beforeunload', function (event) {
										if (!leaving && alts.some(function (input) { return input.value !== input.defaultValue; })) {
											event.preventDefault();
											event.returnValue = '';
										}
									});
									refresh();
									showPicked();

									var notice = document.getElementById('mediaNotice');
									if (notice && !notice.classList.contains('dre-media-problems')) {
										setTimeout(function () { notice.style.display = 'none'; }, 8000);
									}
								})();
							</script>
							<!--end::Careers awards-->

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