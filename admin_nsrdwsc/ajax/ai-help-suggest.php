<?php
// Live "as you type" suggestions for the Ask AI widget's input (admin_nsrdwsc/includes/footer-inner.php).
// Plain substring match against the help library (tbl_ai_help_content) — fast and good enough for
// autocomplete; the full question still goes through the Gemini-backed ai-help-chat.php as before.
// Signed-in admins only, read-only, GET.

include('../db/config.php');
while (ob_get_level() > 0) {
	ob_end_clean();
}
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

if (empty($_SESSION['email_id'])) {
	http_response_code(401);
	echo json_encode(['ok' => false, 'items' => []]);
	exit;
}

$q = trim((string) ($_GET['q'] ?? ''));
if (mb_strlen($q) < 2) {
	echo json_encode(['ok' => true, 'items' => []]);
	exit;
}

$like = '%' . mysqli_real_escape_string($conn, $q) . '%';
$rows = mysqli_query($conn, "SELECT title, page_label, page_url FROM tbl_ai_help_content
	WHERE is_active = '1' AND (title LIKE '$like' OR keywords LIKE '$like' OR page_label LIKE '$like')
	ORDER BY display_order ASC, help_id DESC LIMIT 6");
$items = [];
while ($rows && $row = mysqli_fetch_assoc($rows)) {
	$items[] = [
		'title' => $row['title'],
		'label' => trim((string) $row['page_label']) !== '' ? $row['page_label'] : $row['title'],
	];
}

echo json_encode(['ok' => true, 'items' => $items], JSON_UNESCAPED_UNICODE);
