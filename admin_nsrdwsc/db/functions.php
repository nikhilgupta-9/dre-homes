<?php

// Upload checks keep working on a server without PHP's fileinfo extension.
require_once __DIR__ . '/../includes/finfo-fallback.php';

function getYouTubeVideoId($url) {
    parse_str(parse_url($url, PHP_URL_QUERY), $vars);
    return $vars['v'] ?? '';
}

function prepStr($val)
{
   global $conn;
   $data=mysqli_real_escape_string($conn,$val);
   return $data;
}


function query($tbl_name,$cond)
{
	global $conn;
	$query=mysqli_query($conn,"select * from $tbl_name where $cond");
	if(!empty($query))
	{
		return $query;
	}
	return false;
	
}

function query_detail($tbl_name,$cond)
{
	global $conn;
	$query=mysqli_fetch_array(mysqli_query($conn,"select * from $tbl_name where $cond"));

	if(!empty($query))
	{
		return $query;
	}
	return false;
	
}

function total_record($cond)
{
	global $conn;
	$total=mysqli_num_rows($cond);
	if(!empty($total))
	{
		return $total;
	}
	return false;

	
}

function check_email($tbl_name,$email_id)
{
	global $conn;
	$query=mysqli_query($conn,"select * from $tbl_name where login_email='$email_id'");
	$totl=mysqli_num_rows($query);

	if(!empty($total))
	{
       return $total;
	}

	return false;

}

/* =====================================================================
   Projects: live search (Manage Projects search box, ajax/project-search.php)
   ===================================================================== */

// Projects whose name, location, slug, developer or community contain $term (same fields as the page's own search).
// $type: 0 = every listing type, else one prj_type_id. Names starting with the term come first.
function get_project_search_results($conn, $term, $type = 0, $limit = 8)
{
	$term = trim(mb_substr((string) $term, 0, 100));
	if (mb_strlen($term) < 2) {
		return [];
	}
	$like = '%' . addcslashes($term, '%_\\') . '%';
	$starts = addcslashes($term, '%_\\') . '%';
	$types = (int) $type > 0 ? [(int) $type] : [1, 2, 3, 4];
	$sql = "SELECT tp.prj_id, tp.prj_type_id, tp.proj_name, tp.proj_location, tp.prj_thumb_img, tp.is_active,
	               td.dev_heading, tc.comm_heading
	        FROM tbl_proj tp
	        LEFT JOIN tbl_developer td ON td.dev_id = tp.prj_dev_id
	        LEFT JOIN tbl_community tc ON tc.comm_id = tp.prj_comm_id
	        WHERE tp.prj_type_id IN (" . implode(',', array_fill(0, count($types), '?')) . ")
	          AND (tp.proj_name LIKE ? OR tp.proj_location LIKE ? OR tp.proj_slug LIKE ? OR td.dev_heading LIKE ? OR tc.comm_heading LIKE ?)
	        ORDER BY (tp.proj_name LIKE ?) DESC, tp.is_active = '1' DESC, tp.proj_name ASC
	        LIMIT " . max(1, min(20, (int) $limit));
	$params = array_merge($types, [$like, $like, $like, $like, $like, $starts]);

	$stmt = mysqli_prepare($conn, $sql);
	if (!$stmt) {
		error_log('Project search failed: ' . mysqli_error($conn));
		return [];
	}
	mysqli_stmt_bind_param($stmt, str_repeat('i', count($types)) . 'ssssss', ...$params);
	mysqli_stmt_execute($stmt);
	$rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);

	$type_names = [1 => 'Off Plan', 2 => 'Ready to Move In', 3 => 'International', 4 => 'Commercial'];
	$results = [];
	foreach ($rows as $row) {
		$thumb = trim(explode(',', (string) $row['prj_thumb_img'])[0]);
		$results[] = [
			'id' => (int) $row['prj_id'],
			'name' => trim($row['proj_name']),
			'location' => trim((string) $row['proj_location']),
			'developer' => trim((string) $row['dev_heading']),
			'community' => trim((string) $row['comm_heading']),
			'type' => $type_names[(int) $row['prj_type_id']] ?? '',
			'live' => $row['is_active'] === '1',
			'thumb' => $thumb !== '' ? BASE_URL . 'assets/media/project/thumb/' . rawurlencode($thumb) : '',
			'edit_url' => BASE_URL . 'edit-project.php?edit=' . base64_encode((string) $row['prj_id']),
		];
	}
	return $results;
}
/* =====================================================================
   Admin "Ask AI" help assistant: how-to notes (tbl_ai_help_content) and
   suggested YouTube videos (tbl_ai_help_video), both edited in the admin.
   ===================================================================== */

// Every help note, newest-edited module list first — for manage-ai-help.php.
function get_ai_help_content_all($conn)
{
	$rows = mysqli_query($conn, "SELECT * FROM tbl_ai_help_content ORDER BY display_order ASC, help_id DESC");
	return $rows ? mysqli_fetch_all($rows, MYSQLI_ASSOC) : [];
}

// One help note by id, or null.
function get_ai_help_content_one($conn, $id)
{
	$stmt = mysqli_prepare($conn, "SELECT * FROM tbl_ai_help_content WHERE help_id = ?");
	mysqli_stmt_bind_param($stmt, 'i', $id);
	mysqli_stmt_execute($stmt);
	$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
	mysqli_stmt_close($stmt);
	return $row ?: null;
}

// Every video in the library, in display order — for manage-ai-help-video.php.
function get_ai_help_videos_all($conn)
{
	$rows = mysqli_query($conn, "SELECT * FROM tbl_ai_help_video ORDER BY display_order ASC, video_id DESC");
	return $rows ? mysqli_fetch_all($rows, MYSQLI_ASSOC) : [];
}

// One video by id, or null.
function get_ai_help_video_one($conn, $id)
{
	$stmt = mysqli_prepare($conn, "SELECT * FROM tbl_ai_help_video WHERE video_id = ?");
	mysqli_stmt_bind_param($stmt, 'i', $id);
	mysqli_stmt_execute($stmt);
	$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
	mysqli_stmt_close($stmt);
	return $row ?: null;
}

// Words from a question worth matching on: 3+ letters, deduplicated.
function ai_help_question_words($question)
{
	return array_values(array_unique(array_filter(preg_split('/[^a-z0-9]+/', mb_strtolower((string) $question)), fn($w) => mb_strlen($w) >= 3)));
}

// Words too generic across many admin notes ("add a listing", "post a job", "new entry", "header
// info", "page content" all share these) to count as a match by themselves — they only count
// once something else more specific also matched.
const AI_HELP_STOPWORDS = ['add', 'new', 'make', 'made', 'create', 'open', 'show', 'edit', 'update',
	'delete', 'remove', 'page', 'panel', 'admin', 'entry', 'post', 'item', 'list', 'manage', 'section',
	'form', 'upload', 'save', 'change', 'set', 'the', 'and', 'for', 'how', 'can', 'get', 'put',
	'info', 'information', 'content', 'detail', 'details', 'data', 'about'];

// How well one row matches the question's words. A word found in the title counts for more than
// one found only in the keywords, which counts for more than one buried in the long help text —
// so "developer" sitting right in the title "All Developers" outranks "developer" merely
// mentioned in passing somewhere inside a different note's paragraph of help text.
// A match built from nothing but generic words ("post", "add", "info"...) is not enough on its
// own — that is how "post a job" used to pull up the blog note just because both talk about a
// "post" — it takes at least one more specific word hit as well.
function ai_help_match_score($words, $title, $keywords, $body)
{
	$title = ' ' . mb_strtolower($title) . ' ';
	$keywords = ' ' . mb_strtolower($keywords) . ' ';
	$body = ' ' . mb_strtolower((string) $body) . ' ';
	$score = 0;
	$specific_hits = 0;
	$any_hits = 0;
	foreach ($words as $word) {
		// Matches the word whole, allowing it to be singular where the text is plural or the
		// reverse (one "s" either missing or extra) — "developer" has to find "developers" in
		// a note, not just an exact character-for-character copy. Only ever trims one trailing
		// "s", so a word that genuinely ends in a double "s" ("class", "address") is untouched.
		$stem = mb_strlen($word) > 3 && mb_substr($word, -1) === 's' ? mb_substr($word, 0, -1) : $word;
		$pattern = '/(?<![a-z0-9])' . preg_quote($stem, '/') . 's?(?![a-z0-9])/u';
		$hit = preg_match($pattern, $title) ? 4 : (preg_match($pattern, $keywords) ? 2 : (preg_match($pattern, $body) ? 1 : 0));
		if ($hit > 0) {
			$score += $hit;
			$any_hits++;
			if (!in_array($word, AI_HELP_STOPWORDS, true)) {
				$specific_hits++;
			}
		}
	}
	return ($specific_hits >= 1 && $any_hits >= 1) ? $score : 0;
}

function get_ai_help_context($conn, $question, $limit = 6)
{
	$words = ai_help_question_words($question);
	$rows = mysqli_query($conn, "SELECT help_id, module_key, title, help_text, keywords, page_url, page_label FROM tbl_ai_help_content WHERE is_active = '1'");
	$rows = $rows ? mysqli_fetch_all($rows, MYSQLI_ASSOC) : [];
	if (!$words || !$rows) {
		return [];
	}
	$scored = [];
	foreach ($rows as $row) {
		// module_key is excluded here on purpose: for the auto-generated notes it is literally
		// the page's file name (e.g. "edit-header-info.php"), and matching on it let a generic
		// word like "edit" in the question outrank every "edit-*.php" page at once.
		$score = ai_help_match_score($words, $row['title'], $row['keywords'], $row['help_text']);
		if ($score > 0) {
			$scored[] = ['score' => $score, 'row' => $row];
		}
	}
	usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
	// The row's match score travels with it (as '_score') so get_ai_help_links() can tell a
	// clearly-best match from the rest of the material sent to the model for context.
	return array_map(fn($s) => $s['row'] + ['_score' => $s['score']], array_slice($scored, 0, $limit));
}

// Admin page buttons for the matched help notes ($help_rows from get_ai_help_context): only the
// best-scoring page(s) — ties with the top score — not every note that was merely good enough to
// be sent to the model as background material. This is what keeps "open this page" down to the
// one or two pages that actually answer the question, instead of three unrelated buttons every time.
// Only plain admin file names are accepted (no host, no "../"), so this can only ever point
// inside this admin panel.
function get_ai_help_links($help_rows, $limit = 3)
{
	if (!$help_rows) {
		return [];
	}
	$top_score = max(array_column($help_rows, '_score'));
	$links = [];
	$seen = [];
	foreach ($help_rows as $row) {
		if ((int) ($row['_score'] ?? 0) < $top_score) {
			continue; // not one of the best matches — do not add a button for a weaker guess.
		}
		$page = trim((string) ($row['page_url'] ?? ''));
		if ($page === '' || isset($seen[$page]) || !preg_match('/^[a-z0-9_-]+\.php(\?[a-z0-9_=&-]*)?$/i', $page)) {
			continue;
		}
		$seen[$page] = true;
		$links[] = ['label' => trim((string) $row['page_label']) !== '' ? $row['page_label'] : $row['title'], 'url' => BASE_URL . $page];
		if (count($links) >= $limit) {
			break;
		}
	}
	return $links;
}

// Thumbnail image for a youtube.com/youtu.be link, or '' when no video id can be read from it.
function youtube_thumb_url($url)
{
	if (preg_match('#(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([a-zA-Z0-9_-]{6,})#', (string) $url, $m)) {
		return 'https://img.youtube.com/vi/' . $m[1] . '/mqdefault.jpg';
	}
	return '';
}

// Active videos whose title/keywords share a word with the admin's question (or the answer text).
function get_ai_help_videos($conn, $question, $limit = 3)
{
	$words = ai_help_question_words($question);
	$rows = mysqli_query($conn, "SELECT video_id, title, youtube_url, keywords FROM tbl_ai_help_video WHERE is_active = '1'");
	$rows = $rows ? mysqli_fetch_all($rows, MYSQLI_ASSOC) : [];
	if (!$words || !$rows) {
		return [];
	}
	$scored = [];
	foreach ($rows as $row) {
		$score = ai_help_match_score($words, $row['title'], $row['keywords'], '');
		if ($score > 0) {
			$scored[] = ['score' => $score, 'row' => $row];
		}
	}
	usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
	return array_map(fn($s) => ['title' => $s['row']['title'], 'url' => $s['row']['youtube_url']], array_slice($scored, 0, $limit));
}

/* =====================================================================
   Admin "Ask AI" live data reports — a fixed set of safe, read-only counts
   and lists the assistant may call as tools (admin_reports_declarations() /
   run_admin_report()), used by ajax/ai-help-chat.php. The model never writes
   SQL: it picks one of these named reports and typed arguments, same pattern
   as the website chatbot's tools in ai/ChatTools.php.
   ===================================================================== */

// Tool descriptions sent to the model. Keep the list short: every one of these must be a
// query an admin is already allowed to see on some admin page, nothing new is exposed.
function admin_report_declarations()
{
	return [
		[
			'name' => 'count_leads',
			'description' => 'How many leads/enquiries came in from the website (all forms together: book valuation, off-plan enquiry, general enquiry, gold visa, PDF download, newsletter, visa enquiry).',
			'parameters' => [
				'type' => 'object',
				'properties' => [
					'range' => ['type' => 'string', 'enum' => ['today', 'week', 'month', 'all'], 'description' => 'Default "today".'],
				],
			],
		],
		[
			'name' => 'recent_leads',
			'description' => 'The most recent leads/enquiries, newest first, with their name, phone/email, source form and date. Use when asked to show or list recent leads, not just a count.',
			'parameters' => [
				'type' => 'object',
				'properties' => [
					'limit' => ['type' => 'integer', 'description' => '1 to 10, default 5.'],
				],
			],
		],
		[
			'name' => 'count_properties',
			'description' => 'How many active property listings exist, by listing type.',
			'parameters' => [
				'type' => 'object',
				'properties' => [
					'type' => ['type' => 'string', 'enum' => ['off_plan', 'ready_to_move_in', 'international', 'buy', 'rent', 'all'], 'description' => 'Default "all" (every type, broken down).'],
				],
			],
		],
		[
			'name' => 'count_job_applications',
			'description' => 'How many job applications (resumes) have been received, optionally for one open position.',
			'parameters' => [
				'type' => 'object',
				'properties' => [
					'position' => ['type' => 'string', 'description' => 'Part of a job title, to count applications for that role only.'],
				],
			],
		],
		[
			'name' => 'count_content',
			'description' => 'How many published blog posts, company news articles, communities, developers or team members currently exist.',
			'parameters' => [
				'type' => 'object',
				'properties' => [
					'what' => ['type' => 'string', 'enum' => ['blog_posts', 'company_news', 'communities', 'developers', 'team_members']],
				],
				'required' => ['what'],
			],
		],
		[
			'name' => 'find_record',
			'description' => 'Finds one named developer, community, property/project, blog post, company news article or team member by its name, and returns a direct "Edit" link straight to that one record — not just the list page. Use this whenever the admin names a specific thing they want to edit, view or open (a developer name such as "Emaar", a community such as "Palm Jumeirah", a project name, a person\'s name, an article title), instead of only pointing at the general list page for that kind of record.',
			'parameters' => [
				'type' => 'object',
				'properties' => [
					'name' => ['type' => 'string', 'description' => 'The name to search for, exactly as the admin said it (or the closest plain version of it).'],
					'kind' => ['type' => 'string', 'enum' => ['developer', 'community', 'property', 'blog', 'company_news', 'team_member', 'any'], 'description' => 'Narrow the search when the admin said what kind of thing it is. Default "any" searches every kind at once.'],
				],
				'required' => ['name'],
			],
		],
		admin_page_open_declaration(),
	];
}

// Runs one named report. Always returns an array (an ['error' => ...] array on a bad input),
// the same shape ai/ChatTools.php uses, so it is always safe to hand back to the model.
function run_admin_report($conn, $name, array $args)
{
	try {
		switch ($name) {
			case 'count_leads':
				return admin_report_count_leads($conn, $args);
			case 'recent_leads':
				return admin_report_recent_leads($conn, $args);
			case 'count_properties':
				return admin_report_count_properties($conn, $args);
			case 'count_job_applications':
				return admin_report_count_job_applications($conn, $args);
			case 'count_content':
				return admin_report_count_content($conn, $args);
			case 'find_record':
				return admin_report_find_record($conn, $args);
			case 'open_admin_page':
				return admin_report_open_page($args);
		}
		return ['error' => 'Unknown report.'];
	} catch (Throwable $error) {
		error_log('Admin AI report "' . $name . '" failed: ' . $error->getMessage());
		return ['error' => 'That report could not be run right now.'];
	}
}

// Every enquiry/lead table as one list, same union manage-leads.php itself is built on.
function admin_report_lead_union()
{
	return "
		SELECT book_val_id AS id, name, email_id AS email, mobile AS phone, 'Book Valuation' AS source, tstp AS date FROM tbl_book_val_enquiry
		UNION ALL
		SELECT comm_dev_id, name, email_id, phone, 'Commercial Dev', tstp FROM tbl_comm_dev_enquiry
		UNION ALL
		SELECT enq_id, CONCAT(first_name, ' ', last_name), email_id, enquiry_phone, 'General Enquiry', tstp FROM tbl_enq
		UNION ALL
		SELECT id, name, email_id, number, 'Gold Visa', tstp FROM tbl_goldvisa_enq
		UNION ALL
		SELECT id, name, email, phone, 'Offplan', created_at FROM tbl_offplan_enq
		UNION ALL
		SELECT pdf_enq_id, name, email_id, phone, 'PDF Download', tstp FROM tbl_pdf_enquiry
		UNION ALL
		SELECT subscribe_frm_id, '', subscribe_frm_email, '', 'Newsletter', tstp FROM tbl_subscribe_frm
		UNION ALL
		SELECT visa_enq_id, username, email_id, visa_phone, 'Visa Enquiry', tstp FROM tbl_visa_enquiry";
}

function admin_report_count_leads($conn, array $args)
{
	$range = in_array($args['range'] ?? '', ['today', 'week', 'month', 'all'], true) ? $args['range'] : 'today';
	$since = ['today' => 'CURDATE()', 'week' => 'NOW() - INTERVAL 7 DAY', 'month' => 'NOW() - INTERVAL 30 DAY', 'all' => null][$range];
	$sql = "SELECT COUNT(*) AS n FROM (" . admin_report_lead_union() . ") u" . ($since ? " WHERE u.date >= $since" : '');
	$row = mysqli_fetch_assoc(mysqli_query($conn, $sql));
	return ['range' => $range, 'count' => (int) ($row['n'] ?? 0)];
}

function admin_report_recent_leads($conn, array $args)
{
	$limit = isset($args['limit']) && is_numeric($args['limit']) ? max(1, min(10, (int) $args['limit'])) : 5;
	$sql = "SELECT name, email, phone, source, date FROM (" . admin_report_lead_union() . ") u ORDER BY u.date DESC LIMIT $limit";
	$rows = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
	foreach ($rows as &$row) {
		$row['name'] = trim((string) $row['name']) !== '' ? trim($row['name']) : '(no name given)';
		$row['date'] = (string) $row['date'];
	}
	return ['leads' => $rows];
}

function admin_report_count_properties($conn, array $args)
{
	$types = ['off_plan' => 1, 'ready_to_move_in' => 2, 'international' => 3, 'buy' => 7, 'rent' => 8];
	$type = in_array($args['type'] ?? '', array_keys($types), true) ? $args['type'] : 'all';
	if ($type !== 'all') {
		$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM tbl_proj WHERE is_active = '1' AND prj_type_id = " . (int) $types[$type]));
		return [$type => (int) ($row['n'] ?? 0)];
	}
	$counts = [];
	foreach ($types as $key => $id) {
		$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM tbl_proj WHERE is_active = '1' AND prj_type_id = " . (int) $id));
		$counts[$key] = (int) ($row['n'] ?? 0);
	}
	return $counts;
}

function admin_report_count_job_applications($conn, array $args)
{
	$position = isset($args['position']) && is_string($args['position']) ? trim(mb_substr($args['position'], 0, 100)) : '';
	if ($position !== '') {
		$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS n FROM tbl_resume WHERE pos LIKE ?");
		$like = '%' . addcslashes($position, '%_\\') . '%';
		mysqli_stmt_bind_param($stmt, 's', $like);
		mysqli_stmt_execute($stmt);
		$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
		mysqli_stmt_close($stmt);
		return ['position' => $position, 'count' => (int) ($row['n'] ?? 0)];
	}
	$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM tbl_resume"));
	return ['count' => (int) ($row['n'] ?? 0)];
}

function admin_report_count_content($conn, array $args)
{
	$map = [
		'blog_posts' => "SELECT COUNT(*) AS n FROM tbl_blog WHERE is_active = '1'",
		'company_news' => "SELECT COUNT(*) AS n FROM tbl_news WHERE is_active = '1'",
		'communities' => "SELECT COUNT(*) AS n FROM tbl_community WHERE is_active = '1'",
		'developers' => "SELECT COUNT(*) AS n FROM tbl_developer WHERE is_active = '1'",
		'team_members' => "SELECT COUNT(*) AS n FROM tbl_team WHERE is_active = '1'",
	];
	$what = $args['what'] ?? '';
	if (!isset($map[$what])) {
		return ['error' => 'what must be one of: ' . implode(', ', array_keys($map))];
	}
	$row = mysqli_fetch_assoc(mysqli_query($conn, $map[$what]));
	return [$what => (int) ($row['n'] ?? 0)];
}

// Finds one named record (developer, community, property, blog post, news article or team
// member) and returns a direct edit link to it, not just its list page — the "edit the Emaar
// developer" case, where naming the general Developers page is not a specific enough answer.
function admin_report_find_record($conn, array $args)
{
	$name = isset($args['name']) && is_string($args['name']) ? trim(mb_substr($args['name'], 0, 100)) : '';
	if ($name === '') {
		return ['error' => 'A name to search for is needed.'];
	}
	$kind = in_array($args['kind'] ?? '', ['developer', 'community', 'property', 'blog', 'company_news', 'team_member'], true) ? $args['kind'] : 'any';
	$like = '%' . addcslashes($name, '%_\\') . '%';
	$project_edit_file = ['1' => 'edit-project.php', '2' => 'edit-project.php', '3' => 'edit-project.php', '7' => 'edit-buy-project.php', '8' => 'edit-rent-project.php'];
	$project_type_label = ['1' => 'Off Plan', '2' => 'Ready to Move In', '3' => 'International', '7' => 'Buy', '8' => 'Rent'];

	// [kind, label, table, id column, name column, extra WHERE, edit file (fixed) or a column name to look up per-row].
	$sources = [
		'developer' => ['Developer', 'tbl_developer', 'dev_id', 'dev_heading', '', 'edit-developer.php'],
		'community' => ['Community', 'tbl_community', 'comm_id', 'comm_heading', '', 'edit-community.php'],
		'blog' => ['Blog post', 'tbl_blog', 'blog_id', 'blog_heading', '', 'edit-blog.php'],
		'company_news' => ['Company news article', 'tbl_news', 'news_id', 'news_heading', '', 'edit-company-news.php'],
		'team_member' => ['Team member', 'tbl_team', 'team_id', 'team_name', '', 'edit-team.php'],
	];

	$matches = [];
	foreach ($sources as $key => [$label, $table, $id_col, $name_col, , $edit_file]) {
		if ($kind !== 'any' && $kind !== $key) {
			continue;
		}
		$stmt = mysqli_prepare($conn, "SELECT $id_col AS id, $name_col AS name FROM $table WHERE $name_col LIKE ? ORDER BY (LOWER($name_col) = LOWER(?)) DESC LIMIT 3");
		mysqli_stmt_bind_param($stmt, 'ss', $like, $name);
		mysqli_stmt_execute($stmt);
		$rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		foreach ($rows as $row) {
			$matches[] = ['kind' => $label, 'name' => trim((string) $row['name']), 'edit_page' => $edit_file . '?edit=' . base64_encode((string) $row['id'])];
		}
	}

	if ($kind === 'any' || $kind === 'property') {
		$stmt = mysqli_prepare($conn, "SELECT prj_id AS id, proj_name AS name, prj_type_id FROM tbl_proj WHERE proj_name LIKE ? ORDER BY (LOWER(proj_name) = LOWER(?)) DESC LIMIT 5");
		mysqli_stmt_bind_param($stmt, 'ss', $like, $name);
		mysqli_stmt_execute($stmt);
		$rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		foreach ($rows as $row) {
			$type = (string) $row['prj_type_id'];
			if (!isset($project_edit_file[$type])) {
				continue;
			}
			$matches[] = [
				'kind' => 'Property (' . ($project_type_label[$type] ?? 'listing') . ')',
				'name' => trim((string) $row['name']),
				'edit_page' => $project_edit_file[$type] . '?edit=' . base64_encode((string) $row['id']),
			];
		}
	}

	if (!$matches) {
		return ['found' => false, 'message' => 'No developer, community, property, blog post, news article or team member was found with that name.'];
	}
	return ['found' => true, 'matches' => array_slice($matches, 0, 6)];
}

/* =====================================================================
   Admin "Ask AI" write actions — the ONLY thing the assistant may ever change
   is whether one record is shown or hidden on the website (the same toggle every
   manage-*.php page already exposes as a switch). It can never edit text, change
   prices, or delete anything. Nothing is written here: propose_record_status_change
   only resolves the name and records the intent in tbl_ai_pending_action; the actual
   UPDATE happens in ajax/ai-help-action.php, and only after the admin clicks
   "Confirm" in the chat widget — voice or typed text alone can never flip a switch.
   ===================================================================== */

function admin_action_declarations()
{
	return [
		[
			'name' => 'propose_record_status_change',
			'description' => 'Prepares showing or hiding one developer, community, team member or property on the public website. This does NOT make the change yet — it only finds the record and gets it ready. The admin must press "Confirm" in the chat before anything actually changes. Use this whenever the admin asks to hide, unhide, show, publish, unpublish, enable, disable, activate or deactivate something by name.',
			'parameters' => [
				'type' => 'object',
				'properties' => [
					'name' => ['type' => 'string', 'description' => 'The record\'s name, exactly as the admin said it.'],
					'kind' => ['type' => 'string', 'enum' => ['developer', 'community', 'team_member', 'property'], 'description' => 'What kind of record it is.'],
					'make_visible' => ['type' => 'boolean', 'description' => 'true to show/publish it, false to hide/unpublish it.'],
				],
				'required' => ['name', 'kind', 'make_visible'],
			],
		],
	];
}

// Runs one named write-action tool. Same ['error' => ...]-on-failure shape as run_admin_report().
function run_admin_action($conn, $name, array $args, $admin_user_id)
{
	try {
		if ($name === 'propose_record_status_change') {
			return admin_action_propose_status_change($conn, $args, $admin_user_id);
		}
		return ['error' => 'Unknown action.'];
	} catch (Throwable $error) {
		error_log('Admin AI action "' . $name . '" failed: ' . $error->getMessage());
		return ['error' => 'That could not be prepared right now.'];
	}
}

// [kind => [table, id column, name column]] — the only tables a status-change may ever touch.
function admin_action_targets()
{
	return [
		'developer' => ['tbl_developer', 'dev_id', 'dev_heading'],
		'community' => ['tbl_community', 'comm_id', 'comm_heading'],
		'team_member' => ['tbl_team', 'team_id', 'team_name'],
		'property' => ['tbl_proj', 'prj_id', 'proj_name'],
	];
}

function admin_action_propose_status_change($conn, array $args, $admin_user_id)
{
	$name = isset($args['name']) && is_string($args['name']) ? trim(mb_substr($args['name'], 0, 100)) : '';
	$kind = $args['kind'] ?? '';
	$targets = admin_action_targets();
	if ($name === '' || !isset($targets[$kind]) || !isset($args['make_visible'])) {
		return ['error' => 'A name, a kind (developer, community, team_member or property) and whether to show or hide it are all needed.'];
	}
	[$table, $id_col, $name_col] = $targets[$kind];
	$make_visible = (bool) $args['make_visible'];

	$like = '%' . addcslashes($name, '%_\\') . '%';
	$stmt = mysqli_prepare($conn, "SELECT $id_col AS id, $name_col AS name, is_active FROM $table WHERE $name_col LIKE ? ORDER BY (LOWER($name_col) = LOWER(?)) DESC LIMIT 3");
	mysqli_stmt_bind_param($stmt, 'ss', $like, $name);
	mysqli_stmt_execute($stmt);
	$rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);

	if (!$rows) {
		return ['found' => false, 'message' => "No $kind named \"$name\" was found."];
	}
	// More than one close match, and no exact (case-insensitive) one to break the tie —
	// ask instead of guessing which record the admin meant.
	$exact = array_values(array_filter($rows, fn($r) => mb_strtolower(trim((string) $r['name'])) === mb_strtolower($name)));
	if (count($rows) > 1 && !$exact) {
		return ['found' => false, 'ambiguous' => true, 'message' => 'More than one ' . $kind . ' matches "' . $name . '": ' . implode(', ', array_column($rows, 'name')) . '. Please name the exact one.'];
	}
	$row = $exact ? $exact[0] : $rows[0];
	$already = ($row['is_active'] === '1') === $make_visible;
	if ($already) {
		return ['found' => true, 'already_set' => true, 'message' => trim((string) $row['name']) . ' is already ' . ($make_visible ? 'shown' : 'hidden') . '.'];
	}

	$nonce = bin2hex(random_bytes(16));
	$new_active = $make_visible ? '1' : '2';
	$label = trim((string) $row['name']);
	$stmt = mysqli_prepare($conn, "INSERT INTO tbl_ai_pending_action (nonce, admin_user_id, kind, record_id, record_label, new_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
	mysqli_stmt_bind_param($stmt, 'sisiss', $nonce, $admin_user_id, $kind, $row['id'], $label, $new_active);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);

	return [
		'proposed' => true,
		'nonce' => $nonce,
		'kind' => $kind,
		'label' => $label,
		'make_visible' => $make_visible,
		'message' => 'Ready to ' . ($make_visible ? 'show' : 'hide') . ' "' . $label . '" — waiting for the admin to press Confirm.',
	];
}

// Executes one confirmed (or cancelled) pending action. Called only from
// ajax/ai-help-action.php, only after the admin clicked Confirm/Cancel themselves.
function admin_action_resolve_pending($conn, $logger, $nonce, $admin_user_id, $confirm)
{
	$stmt = mysqli_prepare($conn, "SELECT * FROM tbl_ai_pending_action WHERE nonce = ? AND admin_user_id = ? AND created_at >= NOW() - INTERVAL 5 MINUTE");
	mysqli_stmt_bind_param($stmt, 'si', $nonce, $admin_user_id);
	mysqli_stmt_execute($stmt);
	$pending = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
	mysqli_stmt_close($stmt);

	$delete = mysqli_prepare($conn, "DELETE FROM tbl_ai_pending_action WHERE nonce = ?");
	mysqli_stmt_bind_param($delete, 's', $nonce);
	mysqli_stmt_execute($delete);
	mysqli_stmt_close($delete);

	if (!$pending) {
		return ['ok' => false, 'message' => 'That request has expired. Please ask again.'];
	}
	if (!$confirm) {
		return ['ok' => true, 'message' => 'Cancelled — nothing was changed.'];
	}

	$targets = admin_action_targets();
	if (!isset($targets[$pending['kind']])) {
		return ['ok' => false, 'message' => 'That could not be completed.'];
	}
	[$table, $id_col] = $targets[$pending['kind']];
	$id = (int) $pending['record_id'];
	$new_active = $pending['new_active'];

	$old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT is_active FROM $table WHERE $id_col = $id"));
	if (!$old) {
		return ['ok' => false, 'message' => 'That record no longer exists.'];
	}
	$stmt = mysqli_prepare($conn, "UPDATE $table SET is_active = ? WHERE $id_col = ?");
	mysqli_stmt_bind_param($stmt, 'si', $new_active, $id);
	$success = mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
	if (!$success) {
		return ['ok' => false, 'message' => 'That could not be saved. Please try again.'];
	}

	$verb = $new_active === '1' ? 'shown' : 'hidden';
	$logger->log('UPDATE', $table, $id, ['is_active' => $old['is_active']], ['is_active' => $new_active], 'Ask AI: ' . $pending['record_label'] . ' ' . $verb . ' (voice/chat command)');
	return ['ok' => true, 'message' => $pending['record_label'] . ' is now ' . $verb . ' on the website.'];
}

/* =====================================================================
   Admin "Ask AI" page navigation — "open the blog page", "go to developers",
   "show me leads": a fixed directory of this admin's own list pages, read-only,
   no confirm needed (opening a page changes nothing). Kept separate from
   find_record, which looks up one named record, not a general section.
   ===================================================================== */
function admin_page_directory()
{
	return [
		'dashboard' => ['Dashboard', 'dashboard.php'],
		'leads' => ['Leads / enquiries', 'manage-leads.php'],
		'blog' => ['Blog posts', 'manage-blog.php'],
		'company_news' => ['Company news', 'manage-company-news.php'],
		'developers' => ['Developers', 'manage-developer.php'],
		'communities' => ['Communities', 'manage-community.php'],
		'off_plan_projects' => ['Off-plan projects', 'manage-project.php'],
		'buy_projects' => ['Properties for sale', 'manage-buy-project.php'],
		'rent_projects' => ['Properties for rent', 'manage-rent-project.php'],
		'pending_work_projects' => ['Pending work projects', 'manage-pending-work-project.php'],
		'team' => ['Team members', 'manage-team.php'],
		'testimonials' => ['Client testimonials', 'manage-testimonial.php'],
		'awards' => ['Awards', 'manage-awards.php'],
		'media' => ['Media gallery', 'manage-media.php'],
		'podcast' => ['Podcast episodes', 'manage-podcast.php'],
		'guides' => ['Buyer/seller guides', 'manage-guide.php'],
		'services' => ['Services', 'manage-service-section.php'],
		'job_applications' => ['Job applications', 'manage-resume.php'],
		'open_positions' => ['Open positions', 'manage-position.php'],
		'users' => ['Admin users', 'manage-user.php'],
		'tags' => ['SEO meta tags', 'manage-tags.php'],
		'help_notes' => ['Ask AI help notes', 'manage-ai-help.php'],
		'help_videos' => ['Ask AI help videos', 'manage-ai-help-video.php'],
		'activity_log' => ['Activity log', 'manage-loggs.php'],
	];
}

function admin_page_open_declaration()
{
	return [
		'name' => 'open_admin_page',
		'description' => 'Opens one admin list/section page by name — "open the blog page", "go to developers", "show me leads", "take me to the team page". Use this for a general section, not a single named record (use find_record instead for "open the Emaar developer").',
		'parameters' => [
			'type' => 'object',
			'properties' => [
				'page' => ['type' => 'string', 'enum' => array_keys(admin_page_directory()), 'description' => 'The closest matching page key.'],
			],
			'required' => ['page'],
		],
	];
}

function admin_report_open_page(array $args)
{
	$directory = admin_page_directory();
	$page = $args['page'] ?? '';
	if (!isset($directory[$page])) {
		return ['opened' => false, 'message' => 'I do not have a page matching that in this admin panel.'];
	}
	[$label, $file] = $directory[$page];
	return ['opened' => true, 'label' => $label, 'edit_page' => $file];
}
