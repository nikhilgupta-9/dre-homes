<?php
// Try the index from the command line.
//
//   php ai/cron/search.php "family community near the beach"
//   php ai/cron/search.php "golden visa rules" --types=page,blog
//   php ai/cron/search.php "Palm Beach Towers" --keyword-only

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require __DIR__ . '/../../db/config.php';
require __DIR__ . '/../Embedder.php';
require __DIR__ . '/../VectorStore.php';
while (ob_get_level() > 0) {
    ob_end_flush();
}

$query = '';
$source_types = [];
$keyword_only = false;
foreach (array_slice($argv, 1) as $argument) {
    if (preg_match('/^--types=(.+)$/', $argument, $match)) {
        $source_types = array_filter(array_map('trim', explode(',', $match[1])));
    } elseif ($argument === '--keyword-only') {
        $keyword_only = true;
    } elseif ($query === '') {
        $query = $argument;
    }
}
if ($query === '') {
    fwrite(STDERR, "Usage: php ai/cron/search.php \"your question\" [--types=project,blog] [--keyword-only]\n");
    exit(1);
}

$config = require __DIR__ . '/../config.php';
$embedder = Embedder::fromConfig($config);
$vector_store = new VectorStore();

$query_vector = null;
if (!$keyword_only && $embedder->isConfigured()) {
    try {
        $query_vector = $embedder->embedQuery($query);
    } catch (Throwable $error) {
        echo "Embeddings unavailable ({$error->getMessage()}), using keywords only.\n";
    }
}
echo 'Mode: ' . ($query_vector !== null ? 'vector + keyword' : 'keyword only') . "\n\n";

$started = microtime(true);
$chunks = $vector_store->search($query, $query_vector, $embedder->model(), 6, $source_types);
$elapsed = round((microtime(true) - $started) * 1000);

foreach ($chunks as $position => $chunk) {
    $preview = preg_replace('/\s+/', ' ', mb_substr($chunk['chunk_text'], 0, 220));
    echo ($position + 1) . ". [{$chunk['source_type']}/{$chunk['chunk_kind']}] {$chunk['title']}  ->  " . BASE_URL . "{$chunk['url_path']}\n   $preview\n\n";
}
echo count($chunks) . " results in {$elapsed} ms\n";
