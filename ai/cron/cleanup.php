<?php
// Deletes chat transcripts older than the retention period (ai/config.php, chat_retention_days).
// Usage rows are kept, but the visitor's IP address is removed from the old ones.

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require __DIR__ . '/../../db/config.php';
while (ob_get_level() > 0) {
    ob_end_flush();
}

$config = require __DIR__ . '/../config.php';
$days = max(1, (int) $config['chat_retention_days']);

db_query("DELETE m FROM tbl_ai_messages m JOIN tbl_ai_conversations c ON c.conversation_id = m.conversation_id WHERE c.last_message_at < NOW() - INTERVAL ? DAY", [$days]);
$messages = max(0, (int) $GLOBALS['db_affected_rows']);
db_query("DELETE FROM tbl_ai_conversations WHERE last_message_at < NOW() - INTERVAL ? DAY", [$days]);
$conversations = max(0, (int) $GLOBALS['db_affected_rows']);
db_query("UPDATE tbl_ai_usage SET ip_address = '' WHERE ip_address <> '' AND created_at < NOW() - INTERVAL ? DAY", [$days]);
$usage_rows = max(0, (int) $GLOBALS['db_affected_rows']);

echo "Older than $days days: $conversations conversations and $messages messages deleted, IP address removed from $usage_rows usage rows.\n";
