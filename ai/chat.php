<?php
// Public chat endpoint. The browser POSTs JSON {message, token, page_url} and reads
// server-sent events back: delta (answer text), status, done, error.
// The AI key never reaches the browser: all provider calls are made from here.

require __DIR__ . '/../db/config.php';

// The chat does not use the PHP session; releasing it keeps the visitor's other
// requests from waiting while an answer streams.
session_write_close();
while (ob_get_level() > 0) {
    ob_end_clean();
}

if (!secret('ai_chat_enabled', false)) {
    http_response_code(404);
    exit;
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

// Only our own pages may call this endpoint from a browser.
$site_host = parse_url(BASE_URL, PHP_URL_HOST);
$origin = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
if ($origin === '' || parse_url($origin, PHP_URL_HOST) !== $site_host) {
    http_response_code(403);
    exit;
}

$raw = file_get_contents('php://input', false, null, 0, 16384);
$input = json_decode((string) $raw, true);
if (!is_array($input)) {
    http_response_code(400);
    exit;
}

require __DIR__ . '/ChatService.php';

header('Content-Type: text/event-stream; charset=utf-8');
header('Cache-Control: no-cache, no-store');
header('X-Accel-Buffering: no');
header('X-Content-Type-Options: nosniff');
if (function_exists('apache_setenv')) {
    @apache_setenv('no-gzip', '1');
}
@ini_set('zlib.output_compression', '0');
@ini_set('display_errors', '0');
ignore_user_abort(false);
set_time_limit(120);

$emit = function ($event, array $data) {
    echo 'event: ' . $event . "\n" . 'data: ' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) . "\n\n";
    flush();
};

try {
    $service = new ChatService(require __DIR__ . '/config.php', BASE_URL);
    $service->handle(
        is_string($input['token'] ?? null) ? $input['token'] : '',
        is_string($input['message'] ?? null) ? $input['message'] : '',
        is_string($input['page_url'] ?? null) ? $input['page_url'] : '',
        get_client_ip(),
        $emit
    );
} catch (Throwable $error) {
    error_log('Chat endpoint failed: ' . $error->getMessage());
    $emit('error', ['message' => ChatService::FALLBACK]);
}
