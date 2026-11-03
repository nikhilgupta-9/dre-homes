<?php
// Confirms or cancels one pending "Ask AI" write action (ajax/ai-help-chat.php's
// propose_record_status_change). Nothing is ever written to tbl_developer / tbl_community /
// tbl_team / tbl_proj until this endpoint runs, and it only runs when the admin themselves
// clicks "Confirm" or "Cancel" in the chat widget — never automatically from a voice command.
include('../db/config.php');
include('../includes/Logger.php');
while (ob_get_level() > 0) {
	ob_end_clean();
}
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function ai_action_reply($ok, $message, $status = 200)
{
	http_response_code($status);
	echo json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE);
	exit;
}

if (empty($_SESSION['email_id'])) {
	ai_action_reply(false, 'Please sign in again.', 401);
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
	header('Allow: POST');
	ai_action_reply(false, 'This address only accepts form requests.', 405);
}
$input = json_decode((string) file_get_contents('php://input', false, null, 0, 4096), true);
if (!is_array($input)) {
	ai_action_reply(false, 'The request could not be read.', 400);
}
if (empty($_SESSION['admin_csrf']) || !is_string($input['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $input['csrf'])) {
	ai_action_reply(false, 'The page expired. Reload it and try again.', 403);
}

$nonce = is_string($input['nonce'] ?? null) ? trim($input['nonce']) : '';
$confirm = !empty($input['confirm']);
if (!preg_match('/^[a-f0-9]{32}$/', $nonce)) {
	ai_action_reply(false, 'That request has expired. Please ask again.', 400);
}

$logger = new Logger($conn);
$user_id = (int) ($_SESSION['user_id'] ?? 0);
$result = admin_action_resolve_pending($conn, $logger, $nonce, $user_id, $confirm);
ai_action_reply((bool) $result['ok'], $result['message']);
