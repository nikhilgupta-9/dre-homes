<?php
// Talk to the chatbot from the command line (uses the real provider and the local database).
//
//   php ai/cron/chat_cli.php "2 bed in Dubai Marina under 2 million"
//   php ai/cron/chat_cli.php "and something cheaper?" --token=<token printed by the previous call>

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require __DIR__ . '/../../db/config.php';
require __DIR__ . '/../ChatService.php';
while (ob_get_level() > 0) {
    ob_end_flush();
}

$message = '';
$token = '';
foreach (array_slice($argv, 1) as $argument) {
    if (preg_match('/^--token=([a-f0-9]+)$/', $argument, $match)) {
        $token = $match[1];
    } elseif ($message === '') {
        $message = $argument;
    }
}
if ($message === '') {
    fwrite(STDERR, "Usage: php ai/cron/chat_cli.php \"your question\" [--token=...]\n");
    exit(1);
}

$service = new ChatService(require __DIR__ . '/../config.php', BASE_URL);
$token = $service->handle($token, $message, BASE_URL, 'cli', function ($event, array $data) {
    if ($event === 'delta') {
        echo $data['text'];
    } elseif ($event === 'status' && ($data['text'] ?? '') !== '') {
        echo "[{$data['text']}]\n";
    } elseif ($event === 'error') {
        echo "ERROR: {$data['message']}\n";
    }
});

$usage = db_row("SELECT model, input_tokens, output_tokens, cost_usd, latency_ms, tools_called, status FROM tbl_ai_usage ORDER BY usage_id DESC LIMIT 1");
echo "\n\n--- token: $token\n--- " . json_encode($usage) . "\n";
