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

// Picture types by what the file really contains: real type => extension it is stored with.
const SET_IMAGE_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
const SET_ICON_TYPES = ['image/vnd.microsoft.icon' => 'ico', 'image/x-icon' => 'ico'];
const SET_IMAGE_MAX = 5 * 1024 * 1024;

// Checks one uploaded picture by extension and by its real content.
// Returns [extension to store it with, error]; the extension is '' when no file was chosen.
function set_check_upload($file, $label, $allow_icon = false)
{
	if (!is_array($file) || is_array($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE || (string) $file['name'] === '') {
	  return ['', ''];
	}
	if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > SET_IMAGE_MAX) {
	  return ['', "$label is too large (maximum 5 MB)."];
	}
	if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
	  return ['', "$label could not be uploaded. Please try again."];
	}
	$types = SET_IMAGE_TYPES + ($allow_icon ? SET_ICON_TYPES : []);
	$extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
	$finfo = new finfo(FILEINFO_MIME_TYPE);
	$real = $finfo->file($file['tmp_name']);
	if (!in_array($extension === 'jpeg' ? 'jpg' : $extension, $types, true) || !isset($types[$real])) {
	  return ['', "$label must be one of: " . strtoupper(implode(', ', array_unique(array_values($types)))) . '.'];
	}
	return [$types[$real], ''];
}

// Videos are checked the same way as pictures: by extension and by what the file really contains.
const WP_VIDEO_TYPES = ['video/mp4' => 'mp4', 'video/webm' => 'webm'];
const WP_VIDEO_MAX = 32 * 1024 * 1024;
function wp_check_video($file)
{
	if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > WP_VIDEO_MAX) {
		return ['', 'The video is too large (maximum 32 MB).'];
	}
	if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
		return ['', 'The video could not be uploaded. Please try again.'];
	}
	$extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
	$finfo = new finfo(FILEINFO_MIME_TYPE);
	$real = $finfo->file($file['tmp_name']);
	if (!in_array($extension, WP_VIDEO_TYPES, true) || !isset(WP_VIDEO_TYPES[$real])) {
		return ['', 'The video must be an MP4 or WEBM file.'];
	}
	return [WP_VIDEO_TYPES[$real], ''];
}

// What this page works on.
$page = [
	'key' => 'hero', 'file' => 'manage-hero-section.php', 'table' => 'tbl_hero_section',
	'title' => 'Home page hero', 'one' => 'hero item', 'many' => 'hero items', 'add_label' => 'Add a hero picture or video',
	'blurb' => 'The large picture or video at the top of the home page, behind the property search.',
	'how' => 'The home page takes one live item per screen size (the one with the largest position number). When the desktop item is a video, that video is used on every screen size.',
	'path_col' => 'media_path', 'type_col' => 'media_type', 'order_col' => 'display_order', 'touch' => ', updated_at = NOW()',
	'image_folder' => 'hero/', 'video_folder' => 'hero/videos/', 'slots' => 0, 'unused_label' => 'Live, but another item is used',
	'accept' => 'image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm', 'heavy' => 8 * 1024 * 1024,
	'file_label' => 'Picture or video', 'file_help' => 'Picture: JPG, PNG, WEBP or GIF, up to 5 MB; about 1920 × 1080 fits best. Video: MP4 or WEBM, up to 32 MB; a short clip under 8 MB keeps the home page fast.',
	'fields' => [
		'screen' => ['col' => 'img_screen_size', 'label' => 'Screen size', 'type' => 'choice', 'choices' => ['desktop' => 'Desktop', 'tablet' => 'Tablet', 'mobile' => 'Mobile'], 'default' => 'desktop', 'help' => 'A video is only played when it is the desktop item.'],
		'order' => ['col' => 'display_order', 'label' => 'Position', 'type' => 'number', 'help' => 'With several live items for one screen size, the largest number is used.'],
		'title' => ['col' => 'title', 'label' => 'Note', 'type' => 'text', 'max' => 200, 'placeholder' => 'Palm Jumeirah drone clip', 'help' => 'For your own reference; the home page does not show it.'],
	],
];
$t = $page['table'];
$media_root = 'assets/media/';
$self = BASE_URL . $page['file'];

// Lighter copies of a hero video that the home page uses when they exist next to it.
function wp_related_files($path)
{
	$base = preg_replace('/\.[a-z0-9]+$/i', '', $path);
	return [$path, $base . '.web.mp4', $base . '.poster.jpg'];
}

// ---------------------------------------------------------------
// Actions. Each one is a POST with the form token, then a redirect.
// ---------------------------------------------------------------
$errors = [];
$old = null;      // what was typed, when a save was refused
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
	$action = is_string($_POST['wp_action'] ?? null) ? $_POST['wp_action'] : '';
	$id = (int) ($_POST['id'] ?? 0);
	$token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
	$row = $id > 0 ? (set_query($conn, "SELECT * FROM $t WHERE id = ?", [$id]) ?: []) : [];
	$row = $row ? $row[0] : null;

	if (!$token_ok) {
		$errors[] = 'The form expired. Please try again.';
		if ($action !== 'save') {
			header('Location: ' . $self . '?done=expired');
			exit;
		}
	}

	if ($action === 'toggle' && $row) {
		$old_data = $logger->getOldRecordData($t, $id);
		set_query($conn, "UPDATE $t SET is_active = ? WHERE id = ?", [(int) $row['is_active'] === 1 ? 0 : 1, $id]);
		$logger->log('UPDATE', $t, $id, $old_data, $logger->getOldRecordData($t, $id), $page['title'] . ': ' . ((int) $row['is_active'] === 1 ? 'hidden' : 'shown'));
		header('Location: ' . $self . '?done=' . ((int) $row['is_active'] === 1 ? 'hidden' : 'shown'));
		exit;
	}

	if ($action === 'delete' && $row) {
		$old_data = $logger->getOldRecordData($t, $id);
		$logger->log('DELETE', $t, $id, $old_data, null, $page['title'] . ': deleted ' . $row[$page['path_col']]);
		$logger->archiveDeletedRecord($t, $id, $old_data);
		if (set_query($conn, "DELETE FROM $t WHERE id = ?", [$id])) {
			$path = trim((string) $row[$page['path_col']]);
			foreach ($path !== '' && strpos($path, '..') === false ? wp_related_files($path) : [] as $file) {
				if (is_file($media_root . $file)) {
					@unlink($media_root . $file);
				}
			}
		}
		header('Location: ' . $self . '?done=deleted');
		exit;
	}

	if ($action === 'save') {
		if ($id > 0 && !$row) {
			header('Location: ' . $self . '?done=gone');
			exit;
		}
		$old = [];
		$data = [];
		foreach ($page['fields'] as $name => $field) {
			$value = isset($_POST[$name]) && is_string($_POST[$name]) ? trim($_POST[$name]) : '';
			$old[$name] = $value;
			if ($field['type'] === 'number') {
				if ($value !== '' && !preg_match('/^\d{1,6}$/', $value)) {
					$errors[] = $field['label'] . ': type a whole number.';
				}
				$value = (int) $value;
			} elseif ($field['type'] === 'choice') {
				if (!isset($field['choices'][$value])) {
					$errors[] = $field['label'] . ': choose one from the list.';
				}
			} elseif ($field['type'] === 'link') {
				if ($value !== '' && !preg_match('#^https://[^\s<>"\']+$#i', $value)) {
					$errors[] = $field['label'] . ': paste the full address, starting with https://';
				}
				$value = mb_substr($value, 0, 500);
			} else {
				$value = mb_substr($value, 0, $field['max']);
			}
			if (!empty($field['required']) && $value === '') {
				$errors[] = $field['label'] . ' is needed.';
			}
			$data[$field['col']] = $value;
		}
		$old['is_active'] = isset($_POST['is_active']) ? '1' : '';
		$data['is_active'] = $old['is_active'] === '1' ? 1 : 0;

		// The file: a picture, or (where the page allows it) a video.
		$file = $_FILES['media_file'] ?? null;
		$has_file = is_array($file) && !is_array($file['name']) && $file['error'] !== UPLOAD_ERR_NO_FILE && (string) $file['name'] !== '';
		$new_path = '';
		$kind = '';
		if ($has_file) {
			$extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
			if ($page['video_folder'] !== '' && in_array($extension, WP_VIDEO_TYPES, true)) {
				list($extension, $error) = wp_check_video($file);
				$kind = 'video';
			} else {
				list($extension, $error) = set_check_upload($file, 'The picture');
				$kind = 'image';
			}
			if ($error !== '') {
				$errors[] = $error;
			} else {
				$new_path = ($kind === 'video' ? $page['video_folder'] : $page['image_folder']) . time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
			}
		} elseif (!$row) {
			$errors[] = $page['video_folder'] !== '' ? 'Choose a picture or a video.' : 'Choose a picture.';
		}

		if (!$errors && $new_path !== '') {
			if (!is_dir(dirname($media_root . $new_path))) {
				mkdir(dirname($media_root . $new_path), 0755, true);
			}
			if (!move_uploaded_file($file['tmp_name'], $media_root . $new_path)) {
				$errors[] = 'The file could not be stored on the server. Nothing was saved.';
			}
		}

		if (!$errors) {
			if ($new_path !== '') {
				$data[$page['path_col']] = $new_path;
				$data[$page['type_col']] = $kind;
			}
			if ($row) {
				$old_data = $logger->getOldRecordData($t, $id);
				$assignments = [];
				foreach (array_keys($data) as $column) {
					$assignments[] = "$column = ?";
				}
				$saved = set_query($conn, "UPDATE $t SET " . implode(', ', $assignments) . $page['touch'] . ' WHERE id = ?', array_merge(array_values($data), [$id]));
				$record_id = $id;
			} else {
				$saved = set_query($conn, "INSERT INTO $t (" . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ')', array_values($data));
				$record_id = $saved ? (int) mysqli_insert_id($conn) : 0;
			}
			if (!$saved) {
				if ($new_path !== '' && is_file($media_root . $new_path)) {
					@unlink($media_root . $new_path);
				}
				$errors[] = 'This could not be saved. Please try again.';
			} else {
				// The file that was replaced is no longer needed.
				$replaced = $row && $new_path !== '' ? trim((string) $row[$page['path_col']]) : '';
				foreach ($replaced !== '' && strpos($replaced, '..') === false ? wp_related_files($replaced) : [] as $file_path) {
					if (is_file($media_root . $file_path)) {
						@unlink($media_root . $file_path);
					}
				}
				$logger->log($row ? 'UPDATE' : 'INSERT', $t, $record_id, $row ? $old_data : null, $logger->getOldRecordData($t, $record_id), $page['title'] . ($row ? ' updated' : ' added'));
				header('Location: ' . $self . '?done=' . ($row ? 'saved' : 'added'));
				exit;
			}
		}
	}
}

// ---------------------------------------------------------------
// What the page shows
// ---------------------------------------------------------------
$rows = set_query($conn, "SELECT * FROM $t ORDER BY {$page['order_col']} ASC, id ASC") ?: [];

// Which rows the home page really uses, worked out the way index.php does it.
$in_use = [];
if ($page['key'] === 'hero') {
	$winner = [];
	foreach ($rows as $row) {
		if ((int) $row['is_active'] === 1) {
			$winner[$row['img_screen_size']] = $row;
		}
	}
	if (isset($winner['desktop']) && $winner['desktop']['media_type'] === 'video') {
		$in_use[(int) $winner['desktop']['id']] = 'On the home page, all screen sizes';
	} else {
		foreach ($winner as $size => $row) {
			$in_use[(int) $row['id']] = 'On the home page: ' . $size;
		}
	}
} else {
	$slot = 0;
	foreach ($rows as $row) {
		if ((int) $row['is_active'] === 1 && $slot < $page['slots']) {
			$slot++;
			$in_use[(int) $row['id']] = 'Slot ' . $slot . ' of ' . $page['slots'];
		}
	}
}
$stat = ['live' => 0, 'hidden' => 0, 'used' => count($in_use)];
foreach ($rows as $row) {
	$stat[(int) $row['is_active'] === 1 ? 'live' : 'hidden']++;
}

$edit_id = (int) ($_POST['id'] ?? ($_GET['edit'] ?? 0));
$editing = null;
foreach ($rows as $row) {
	if ((int) $row['id'] === $edit_id) {
		$editing = $row;
	}
}
$values = ['is_active' => $editing ? ((int) $editing['is_active'] === 1 ? '1' : '') : '1'];
$next_order = 0;
foreach ($rows as $row) {
	$next_order = max($next_order, (int) $row[$page['order_col']] + 1);
}
foreach ($page['fields'] as $name => $field) {
	$values[$name] = $editing ? (string) $editing[$field['col']] : ($field['col'] === $page['order_col'] ? (string) $next_order : (string) ($field['default'] ?? ''));
}
if ($old !== null) {
	$values = $old + $values;
}

$notices = ['added' => 'Added.', 'saved' => 'Changes saved.', 'deleted' => 'Deleted.', 'hidden' => 'Hidden from the website.', 'shown' => 'Shown on the website again.', 'expired' => 'The form expired. Nothing was changed; please try again.', 'gone' => 'That item no longer exists.'];
$done = is_string($_GET['done'] ?? null) ? $_GET['done'] : '';
$notice = $notices[$done] ?? '';
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
							<!--begin::<?= set_e($page['title']) ?>-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-media.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-webpages.css?v=3" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-media dre-webpages">

								<!-- Title, numbers, main actions -->
								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1><?= set_e($page['title']) ?></h1>
										<p><?= set_e($page['blurb']) ?></p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= $stat['live'] ?></strong><span>Live</span></div>
										<div><strong><?= $stat['hidden'] ?></strong><span>Hidden</span></div>
										<div><strong><?= $stat['used'] ?></strong><span>On the home page</span></div>
									</div>
									<div class="dre-actions">
										<a class="dre-primary" href="<?= set_e($self) ?>#wpForm"><i class="bi bi-plus-lg"></i> <?= set_e($page['add_label']) ?></a>
										<a href="<?= set_e(BASE_URL_HOME) ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View the home page</a>
									</div>
								</div>

								<div class="dre-leads-notice dre-site-note" role="note"><i class="bi bi-info-circle"></i> <?= set_e($page['how']) ?></div>

								<?php if ($notice !== ''): ?>
									<div class="dre-leads-notice" role="status" id="wpNotice"><i class="bi bi-check-circle"></i> <?= set_e($notice) ?></div>
								<?php endif; ?>
								<?php if ($errors): ?>
									<div class="dre-leads-notice dre-media-problems" role="alert">
										<i class="bi bi-exclamation-triangle"></i> Nothing was saved yet:
										<ul>
											<?php foreach ($errors as $error): ?>
												<li><?= set_e($error) ?></li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>

								<!-- What is there now -->
								<div class="dre-glass dre-leads-list">
									<div class="dre-h">
										<h3><?= count($rows) ?> <?= set_e(count($rows) === 1 ? $page['one'] : $page['many']) ?> <small>In the order the home page reads them</small></h3>
									</div>
									<?php if (!$rows): ?>
										<div class="dre-empty"><i class="bi bi-images" style="font-size: 2rem;"></i><br>Nothing here yet. Add the first one below.</div>
									<?php else: ?>
										<div class="dre-media-grid dre-wp-grid">
											<?php foreach ($rows as $row):
												$id = (int) $row['id'];
												$is_live = (int) $row['is_active'] === 1;
												$path = trim((string) $row[$page['path_col']]);
												$url = BASE_URL . $media_root . implode('/', array_map('rawurlencode', explode('/', $path)));
												$is_video = $row[$page['type_col']] === 'video';
												$size = $path !== '' && is_file($media_root . $path) ? filesize($media_root . $path) : 0;
											?>
												<div class="dre-media-card<?= $is_live ? '' : ' is-hidden' ?><?= $edit_id === $id ? ' is-editing' : '' ?>">
													<div class="dre-media-frame">
														<?php if ($is_video): ?>
															<video src="<?= set_e($url) ?>#t=0.5" muted playsinline preload="metadata" controls></video>
														<?php else: ?>
															<a href="<?= set_e($url) ?>" target="_blank" rel="noopener" title="Open the picture"><img src="<?= set_e($url) ?>" alt="" loading="lazy" onerror="this.closest('.dre-media-card').classList.add('is-broken')"></a>
														<?php endif; ?>
														<i class="bi bi-image dre-media-broken" aria-hidden="true"></i>
														<?php if (!$is_live): ?><span class="dre-pill dre-pill-bad">Hidden</span><?php endif; ?>
													</div>
													<div class="dre-wp-info">
														<strong><?= set_e($page['key'] === 'hero' ? ucfirst((string) $row['img_screen_size']) . ' · ' . ($is_video ? 'Video' : 'Picture') : ('Position ' . (int) $row[$page['order_col']])) ?></strong>
														<?php if (isset($in_use[$id])): ?>
															<span class="dre-pill dre-pill-good"><?= set_e($in_use[$id]) ?></span>
														<?php elseif ($is_live): ?>
															<span class="dre-pill"><?= set_e($page['unused_label']) ?></span>
														<?php endif; ?>
														<?php if ($page['key'] === 'insta' && trim((string) $row['instagram_url']) !== ''): ?>
															<a href="<?= set_e($row['instagram_url']) ?>" target="_blank" rel="noopener noreferrer" class="dre-wp-link"><i class="bi bi-instagram"></i> Open the post</a>
														<?php elseif ($page['key'] === 'insta'): ?>
															<span class="dre-wp-warn"><i class="bi bi-exclamation-triangle"></i> No post link</span>
														<?php endif; ?>
														<?php if ($size > 0): ?>
															<small class="<?= $size > $page['heavy'] ? 'dre-wp-warn' : '' ?>"><?= $size >= 1048576 ? number_format($size / 1048576, 1) . ' MB' : number_format($size / 1024) . ' KB' ?><?= $size > $page['heavy'] ? ' · a large file' : '' ?></small>
														<?php elseif ($path !== ''): ?>
															<small class="dre-wp-warn">The file is missing on the server</small>
														<?php endif; ?>
													</div>
													<div class="dre-media-actions">
														<a class="dre-icon-btn" href="<?= set_e($self) ?>?edit=<?= $id ?>#wpForm" title="Edit" aria-label="Edit"><i class="bi bi-pencil"></i></a>
														<form method="post" action="<?= set_e($self) ?>">
															<input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
															<input type="hidden" name="id" value="<?= $id ?>">
															<button type="submit" name="wp_action" value="toggle" class="dre-icon-btn" title="<?= $is_live ? 'Hide from the website' : 'Show on the website' ?>" aria-label="<?= $is_live ? 'Hide from the website' : 'Show on the website' ?>"><i class="bi <?= $is_live ? 'bi-eye-slash' : 'bi-eye' ?>"></i></button>
															<button type="submit" name="wp_action" value="delete" class="dre-icon-btn dre-media-delete" title="Delete" aria-label="Delete" data-confirm="Delete this <?= set_e($page['one']) ?> and its file? This cannot be undone."><i class="bi bi-trash"></i></button>
														</form>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
								</div>

								<!-- Add one, or change the one chosen above -->
								<form method="post" action="<?= set_e($self) ?><?= $editing ? '?edit=' . (int) $editing['id'] : '' ?>#wpForm" enctype="multipart/form-data" class="dre-glass dre-wp-form" id="wpForm">
									<input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
									<input type="hidden" name="wp_action" value="save">
									<input type="hidden" name="id" value="<?= $editing ? (int) $editing['id'] : 0 ?>">
									<div class="dre-h">
										<h3><i class="bi <?= $editing ? 'bi-pencil' : 'bi-plus-circle' ?>"></i> <?= $editing ? 'Change this ' . set_e($page['one']) : set_e($page['add_label']) ?></h3>
										<?php if ($editing): ?><a class="dre-btn" href="<?= set_e($self) ?>#wpForm">Add a new one instead</a><?php endif; ?>
									</div>
									<div class="dre-wp-fields">
										<label class="dre-wp-file">
											<span><?= set_e($page['file_label']) ?><?= $editing ? '' : ' *' ?></span>
											<input type="file" name="media_file" id="wpFile" accept="<?= set_e($page['accept']) ?>"<?= $editing ? '' : ' required' ?>>
											<small><?= set_e($page['file_help']) ?><?= $editing ? ' Leave empty to keep the current file.' : '' ?></small>
											<span class="dre-wp-preview" id="wpPreview" hidden></span>
										</label>
										<?php foreach ($page['fields'] as $name => $field): ?>
											<label>
												<span><?= set_e($field['label']) ?><?= !empty($field['required']) ? ' *' : '' ?></span>
												<?php if ($field['type'] === 'choice'): ?>
													<select name="<?= set_e($name) ?>">
														<?php foreach ($field['choices'] as $value => $label): ?>
															<option value="<?= set_e($value) ?>" <?= $values[$name] === (string) $value ? 'selected' : '' ?>><?= set_e($label) ?></option>
														<?php endforeach; ?>
													</select>
												<?php elseif ($field['type'] === 'textarea'): ?>
													<textarea name="<?= set_e($name) ?>" rows="3" maxlength="<?= (int) $field['max'] ?>"><?= set_e($values[$name]) ?></textarea>
												<?php else: ?>
													<input type="<?= $field['type'] === 'number' ? 'number' : ($field['type'] === 'link' ? 'url' : 'text') ?>" name="<?= set_e($name) ?>" value="<?= set_e($values[$name]) ?>"<?= $field['type'] === 'number' ? ' min="0" step="1"' : ' maxlength="' . (int) ($field['max'] ?? 500) . '"' ?> placeholder="<?= set_e($field['placeholder'] ?? '') ?>" autocomplete="off"<?= !empty($field['required']) ? ' required' : '' ?>>
												<?php endif; ?>
												<?php if (!empty($field['help'])): ?><small><?= set_e($field['help']) ?></small><?php endif; ?>
											</label>
										<?php endforeach; ?>
										<label class="dre-proj-check dre-wp-live"><input type="checkbox" name="is_active" value="1" <?= $values['is_active'] === '1' ? 'checked' : '' ?>> Show on the website</label>
									</div>
									<div class="dre-wp-buttons">
										<button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> <?= $editing ? 'Save changes' : 'Add' ?></button>
										<?php if ($editing): ?><a class="dre-btn" href="<?= set_e($self) ?>">Cancel</a><?php endif; ?>
									</div>
								</form>
							</div>

							<script>
								// Small helpers of this page: confirm before deleting, preview the chosen file, tidy the address bar.
								(function () {
									document.querySelectorAll('[data-confirm]').forEach(function (button) {
										button.addEventListener('click', function (event) {
											if (!window.confirm(button.getAttribute('data-confirm'))) {
												event.preventDefault();
											}
										});
									});

									var input = document.getElementById('wpFile');
									var preview = document.getElementById('wpPreview');
									var heavy = <?= (int) $page['heavy'] ?>;
									if (input && preview) {
										input.addEventListener('change', function () {
											preview.textContent = '';
											preview.hidden = true;
											var file = input.files && input.files[0];
											if (!file) {
												return;
											}
											var media = document.createElement(file.type.indexOf('video/') === 0 ? 'video' : 'img');
											media.src = URL.createObjectURL(file);
											if (media.tagName === 'VIDEO') {
												media.muted = true;
												media.controls = true;
											}
											var note = document.createElement('em');
											var megabytes = file.size / 1048576;
											note.textContent = file.name + ' · ' + (megabytes >= 1 ? megabytes.toFixed(1) + ' MB' : Math.round(file.size / 1024) + ' KB') + (file.size > heavy ? ' · a large file: it will slow the home page down' : '');
											if (file.size > heavy) {
												note.className = 'dre-wp-warn';
											}
											preview.appendChild(media);
											preview.appendChild(note);
											preview.hidden = false;
										});
									}

									if (window.history.replaceState && /[?&]done=/.test(window.location.search)) {
										window.history.replaceState(null, '', window.location.pathname);
									}
								})();
							</script>
							<!--end::<?= set_e($page['title']) ?>-->


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