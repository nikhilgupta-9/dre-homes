<?php
/**
 * SEO check for the project forms: a panel that works like Yoast SEO in WordPress.
 *
 * The checking itself happens in the browser (assets/js/dre-seo-check.js) while the person types.
 * This file draws the panel and keeps the focus keyphrase and the two scores in tbl_proj_seo,
 * so the project list can show a coloured dot for every project.
 */

function seo_check_ready($conn)
{
	static $ready = null;
	if ($ready === null) {
		$result = mysqli_query($conn, "SHOW TABLES LIKE 'tbl_proj_seo'");
		$ready = $result && mysqli_num_rows($result) > 0;
	}
	return $ready;
}

/** The saved keyphrase and scores of one project, or empty values. */
function seo_check_load($conn, $prj_id)
{
	$empty = ['focus_keyphrase' => '', 'seo_score' => null, 'readability_score' => null];
	if (!seo_check_ready($conn) || $prj_id <= 0) {
		return $empty;
	}
	$stmt = mysqli_prepare($conn, 'SELECT focus_keyphrase, seo_score, readability_score FROM tbl_proj_seo WHERE prj_id = ?');
	mysqli_stmt_bind_param($stmt, 'i', $prj_id);
	mysqli_stmt_execute($stmt);
	$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
	mysqli_stmt_close($stmt);
	return $row ?: $empty;
}

/** Keeps what the form sent for one project. Called after the project itself was saved. */
function seo_check_save($conn, $prj_id)
{
	if (!seo_check_ready($conn) || $prj_id <= 0 || !isset($_POST['seo_focus'])) {
		return;
	}
	$focus = mb_substr(trim(preg_replace('/\s+/', ' ', is_string($_POST['seo_focus']) ? $_POST['seo_focus'] : '')), 0, 190);
	$score = function ($name) {
		$value = $_POST[$name] ?? '';
		return is_string($value) && ctype_digit($value) && (int) $value <= 100 ? (int) $value : null;
	};
	$seo = $score('seo_score');
	$readability = $score('seo_readability');
	$stmt = mysqli_prepare($conn, 'INSERT INTO tbl_proj_seo (prj_id, focus_keyphrase, seo_score, readability_score, checked_at) VALUES (?, ?, ?, ?, NOW())
		ON DUPLICATE KEY UPDATE focus_keyphrase = VALUES(focus_keyphrase), seo_score = VALUES(seo_score), readability_score = VALUES(readability_score), checked_at = NOW()');
	mysqli_stmt_bind_param($stmt, 'isii', $prj_id, $focus, $seo, $readability);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
}

/** Other projects that already use this keyphrase: two pages should not compete for the same search. */
function seo_check_same_keyphrase($conn, $prj_id, $focus)
{
	if (!seo_check_ready($conn) || trim($focus) === '') {
		return [];
	}
	$stmt = mysqli_prepare($conn, 'SELECT p.prj_id, p.proj_name FROM tbl_proj_seo s JOIN tbl_proj p ON p.prj_id = s.prj_id WHERE s.focus_keyphrase = ? AND s.prj_id <> ? LIMIT 3');
	mysqli_stmt_bind_param($stmt, 'si', $focus, $prj_id);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$rows = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	mysqli_stmt_close($stmt);
	return $rows;
}

/** "good", "ok", "bad" or "none" for a saved score, the way the dots are coloured. */
function seo_check_level($score)
{
	if ($score === null || $score === '') {
		return 'none';
	}
	return (int) $score > 70 ? 'good' : ((int) $score > 40 ? 'ok' : 'bad');
}

/**
 * Draws the panel. $options:
 *   prj_id   the project (0 on the "add" form)
 *   focus    the keyphrase to show (what was typed, after a refused save)
 *   url      the address the page will have, without the slug
 *   site     the name shown in the search result preview
 */
function seo_check_panel($conn, array $options)
{
	$e = function ($text) { return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8'); };
	$saved = seo_check_load($conn, (int) $options['prj_id']);
	$focus = $options['focus'] !== null ? (string) $options['focus'] : (string) $saved['focus_keyphrase'];
	$same = seo_check_same_keyphrase($conn, (int) $options['prj_id'], (string) $saved['focus_keyphrase']);
	$same_names = array_map(function ($row) { return $row['proj_name']; }, $same);
	// The descriptions of the gallery pictures are kept with the gallery, not in this form.
	$gallery_alts = [];
	if ((int) $options['prj_id'] > 0) {
		$stmt = mysqli_prepare($conn, "SELECT gallery_img_alt FROM tbl_proj_gallery WHERE proj_id = ? AND is_active = '1' AND TRIM(gallery_img_alt) <> '' LIMIT 60");
		$prj_id = (int) $options['prj_id'];
		mysqli_stmt_bind_param($stmt, 'i', $prj_id);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		while ($row = mysqli_fetch_row($result)) {
			$gallery_alts[] = $row[0];
		}
		mysqli_stmt_close($stmt);
	}
	?>
	<div class="dre-seo" id="dreSeo"
		data-url="<?= $e($options['url']) ?>" data-site="<?= $e($options['site']) ?>"
		data-same="<?= $e(implode(' | ', $same_names)) ?>" data-same-for="<?= $e($saved['focus_keyphrase']) ?>"
		data-gallery-alts="<?= $e(json_encode($gallery_alts)) ?>">
		<div class="dre-seo-head">
			<div>
				<h3><i class="bi bi-traffic-light"></i> SEO check</h3>
				<p>Checks this page the way Yoast SEO does in WordPress, while you type. Green is good, orange can be better, red needs work.</p>
			</div>
			<div class="dre-seo-scores">
				<span class="dre-seo-score" data-seo-light="seo"><i></i><b>SEO</b><em>Not checked</em></span>
				<span class="dre-seo-score" data-seo-light="readability"><i></i><b>Readability</b><em>Not checked</em></span>
			</div>
		</div>

		<label class="dre-field dre-seo-focus">
			<span>Focus keyphrase</span>
			<input type="text" name="seo_focus" id="dreSeoFocus" value="<?= $e($focus) ?>" maxlength="190" autocomplete="off" placeholder="palace residences hillside dubai hills">
			<small>The words you want this page to be found for in Google. One keyphrase for one page.</small>
		</label>
		<input type="hidden" name="seo_score" id="dreSeoScore" value="">
		<input type="hidden" name="seo_readability" id="dreSeoReadability" value="">

		<div class="dre-seo-preview">
			<div class="dre-seo-preview-bar">
				<strong>Google preview</strong>
				<span role="group" aria-label="Preview for">
					<button type="button" class="is-on" data-seo-device="mobile"><i class="bi bi-phone"></i> Mobile</button>
					<button type="button" data-seo-device="desktop"><i class="bi bi-display"></i> Desktop</button>
				</span>
			</div>
			<div class="dre-seo-snippet is-mobile" id="dreSeoSnippet">
				<div class="dre-seo-snippet-site"><span class="dre-seo-favicon"><i class="bi bi-buildings"></i></span><span><b data-seo-site></b><small data-seo-url></small></span></div>
				<div class="dre-seo-snippet-title" data-seo-title></div>
				<div class="dre-seo-snippet-text" data-seo-text></div>
			</div>
		</div>

		<div class="dre-seo-tabs" role="tablist">
			<button type="button" role="tab" class="is-on" data-seo-tab="seo"><i data-seo-dot="seo"></i> SEO analysis</button>
			<button type="button" role="tab" data-seo-tab="readability"><i data-seo-dot="readability"></i> Readability</button>
		</div>
		<div class="dre-seo-results" id="dreSeoResults" aria-live="polite"></div>
	</div>
	<?php
}
