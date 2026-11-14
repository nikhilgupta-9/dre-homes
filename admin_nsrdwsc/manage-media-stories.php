<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

if (empty($_SESSION['email_id'])) {
	exit;
}

$logger = new Logger($conn);
if (empty($_SESSION['admin_csrf'])) {
	$_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
}

// Auto-create table if not exists
@mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `tbl_media_story_titles` (
  `story_anchor_id` int(11) NOT NULL,
  `story_title` varchar(255) NOT NULL,
  `tstp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`story_anchor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// Helper for story caption
function get_story_caption($alt, $file) {
	$text = trim(html_entity_decode((string) $alt, ENT_QUOTES, 'UTF-8'));
	if ($text === '') {
		$text = preg_replace('/^media_img_\d+_/i', '', (string) $file);
		$text = preg_replace('/(\.(jpe?g|png|webp|gif))+$/i', '', $text);
		$text = preg_replace('/[-_ ]?\d+$/', '', $text);
	}
	if (!preg_match('/\s/', $text)) {
		$text = str_replace(['-', '_'], ' ', $text);
	}
	$text = trim(preg_replace('/\s+/', ' ', preg_replace('/\b(img|image)\b/i', '', $text)));
	return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}

// Fetch photos and build stories
$media_folder = __DIR__ . '/assets/media/media-image/';
$base_media_url = BASE_URL . 'assets/media/media-image/';
$photos = [];
$res = mysqli_query($conn, "SELECT media_id, media_img, media_img_alt_tag FROM tbl_media WHERE is_active = '1' ORDER BY media_id DESC");
while ($r = mysqli_fetch_assoc($res)) {
	$f = basename(trim((string) $r['media_img']));
	if ($f === '' || !is_file($media_folder . $f)) continue;
	$photos[] = [
		'id' => (int) $r['media_id'],
		'file' => $f,
		'url' => $base_media_url . rawurlencode($f),
		'caption' => get_story_caption($r['media_img_alt_tag'], $f),
	];
}

// Custom titles from DB
$custom_titles = [];
$cres = @mysqli_query($conn, "SELECT story_anchor_id, story_title FROM tbl_media_story_titles");
if ($cres) {
	while ($cr = mysqli_fetch_assoc($cres)) {
		$custom_titles[(int) $cr['story_anchor_id']] = trim((string) $cr['story_title']);
	}
}

// Group into stories (runs of >= 7 photos with identical caption)
$stories = [];
$total_photos = count($photos);
for ($i = 0; $i < $total_photos; $i = $end + 1) {
	$end = $i;
	while ($end + 1 < $total_photos && $photos[$end + 1]['caption'] === $photos[$i]['caption']) {
		$end++;
	}
	if ($end - $i + 1 < 7) continue;
	$anchor_id = (int) $photos[$end]['id'];
	$stories[] = [
		'id' => $anchor_id,
		'custom_title' => $custom_titles[$anchor_id] ?? '',
		'default_title' => $photos[$i]['caption'],
		'cover' => $photos[$i]['url'],
		'count' => $end - $i + 1,
	];
}

// Save POST
$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_story_titles'])) {
	if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
		$error = 'The form expired. Please reload and try again.';
	} else {
		$allowed = array_fill_keys(array_column($stories, 'id'), true);
		$changed = 0;
		foreach ((array) ($_POST['story_title'] ?? []) as $s_id => $val) {
			$s_id = (int) $s_id;
			if ($s_id < 1 || !isset($allowed[$s_id])) continue;
			$title = mb_substr(trim((string) $val), 0, 255);
			$old_val = $custom_titles[$s_id] ?? '';
			if ($title === $old_val) continue;

			if ($title === '') {
				mysqli_query($conn, "DELETE FROM tbl_media_story_titles WHERE story_anchor_id = $s_id");
			} else {
				$stmt = mysqli_prepare($conn, "INSERT INTO tbl_media_story_titles (story_anchor_id, story_title, tstp) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE story_title = VALUES(story_title), tstp = NOW()");
				if ($stmt) {
					mysqli_stmt_bind_param($stmt, 'is', $s_id, $title);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_close($stmt);
				}
			}
			$changed++;
		}
		if ($changed > 0) {
			$logger->log('UPDATE', 'tbl_media_story_titles', 0, null, ['titles_changed' => $changed], "Highlight stories: $changed title(s) changed");
		}
		header('Location: ' . BASE_URL . 'manage-media-stories.php?done=' . $changed);
		exit;
	}
}

$done = isset($_GET['done']) && ctype_digit((string) $_GET['done']) ? (int) $_GET['done'] : null;
$e = static fn($text) => htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
?>
<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
	<div class="d-flex flex-column flex-root">
		<div class="d-flex flex-row flex-column-fluid page">
			<?php include('includes/sidebar.php'); ?>
			<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
				<div class="header header-fixed">
					<div class="container-fluid d-flex align-items-center justify-content-between">
						<div></div>
						<div class="topbar-item">
							<span class="text-dark-50 font-weight-bolder font-size-base">Hi, <?= $e($_SESSION['username'] ?? '') ?></span>
						</div>
					</div>
				</div>
				<div class="content d-flex flex-column flex-column-fluid dre-dash-content" id="kt_content">
					<div class="d-flex flex-column-fluid">
						<div class="container">
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-media.css?v=1" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-media">
								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1>Highlight stories</h1>
										<p>Set the display title for each photo group shown in “Moments worth reliving.”</p>
									</div>
									<div class="dre-actions">
										<a class="dre-primary" href="<?= BASE_URL ?>manage-media.php"><i class="bi bi-images"></i> All media</a>
										<a href="<?= BASE_URL ?>edit-media-content.php?edit=<?= base64_encode(1) ?>"><i class="bi bi-window"></i> Page content</a>
										<a href="<?= rtrim(BASE_URL_HOME, '/') ?>/our-media" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View page</a>
									</div>
								</div>

								<?php if ($error !== ''): ?>
									<div class="dre-leads-notice" role="alert"><i class="bi bi-exclamation-triangle"></i> <?= $e($error) ?></div>
								<?php elseif ($done !== null): ?>
									<div class="dre-leads-notice" role="status"><i class="bi bi-check-circle"></i> <?= $done > 0 ? $done . ' story ' . ($done === 1 ? 'title was' : 'titles were') . ' saved.' : 'No story titles changed.' ?></div>
								<?php endif; ?>

								<section class="dre-glass dre-media-stories" aria-labelledby="mediaStoriesTitle">
									<div class="dre-media-stories-head">
										<div>
											<h2 id="mediaStoriesTitle">Photo groups</h2>
											<p>Change a group title without changing the individual photo descriptions. Leave blank to use the original caption.</p>
										</div>
										<span><?= count($stories) ?> <?= count($stories) === 1 ? 'group' : 'groups' ?></span>
									</div>
									<?php if (!$stories): ?>
										<p class="dre-media-stories-empty">No groups currently qualify. A Highlight story is made from at least 7 live photos in a row with the same description.</p>
									<?php else: ?>
										<form method="post" action="<?= BASE_URL ?>manage-media-stories.php" class="dre-media-story-form">
											<input type="hidden" name="csrf" value="<?= $e($_SESSION['admin_csrf']) ?>">
											<div class="dre-media-story-list">
												<?php foreach ($stories as $story): ?>
													<article class="dre-media-story-row">
														<img src="<?= $e($story['cover']) ?>" alt="" loading="lazy">
														<span class="dre-media-story-meta"><strong><?= number_format((int) $story['count']) ?> photos</strong><small>Group ID <?= (int) $story['id'] ?></small></span>
														<label class="dre-media-story-input"><span>Display title</span><input type="text" name="story_title[<?= (int) $story['id'] ?>]" value="<?= $e($story['custom_title']) ?>" placeholder="<?= $e($story['default_title']) ?>" maxlength="255"></label>
													</article>
												<?php endforeach; ?>
											</div>
											<div class="dre-media-story-save"><button type="submit" name="save_story_titles" value="1" class="dre-btn dre-btn-main"><i class="bi bi-check2"></i> Save story titles</button></div>
										</form>
									<?php endif; ?>
								</section>
							</div>
						</div>
					</div>
				</div>
				<?php include('includes/footer-inner.php'); ?>
			</div>
		</div>
	</div>
</body>
