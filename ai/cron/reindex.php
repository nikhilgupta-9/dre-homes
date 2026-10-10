<?php
// Rebuilds the search index. Safe to run often: unchanged chunks are skipped and are
// not sent to the embeddings provider again. Run build_proj_facts.php first.
//
//   php ai/cron/reindex.php                 sync chunks, then embed what is new or changed
//   php ai/cron/reindex.php --dry-run       show what would change, write nothing, call nobody
//   php ai/cron/reindex.php --no-embed      sync chunks only (keyword search works without embeddings)
//   php ai/cron/reindex.php --max-embed=200 embed at most 200 chunks this run
//   php ai/cron/reindex.php --status        show the state of the index and exit

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require __DIR__ . '/../../db/config.php';
require __DIR__ . '/../Indexer.php';
require __DIR__ . '/../Embedder.php';
require __DIR__ . '/../VectorStore.php';
while (ob_get_level() > 0) {
    ob_end_flush();
}

$options = array_slice($argv, 1);
$dry_run = in_array('--dry-run', $options, true);
$no_embed = in_array('--no-embed', $options, true);
$status_only = in_array('--status', $options, true);
$max_embed = 0;
foreach ($options as $option) {
    if (preg_match('/^--max-embed=(\d+)$/', $option, $match)) {
        $max_embed = (int) $match[1];
    }
}

$config = require __DIR__ . '/../config.php';
$indexer = new Indexer($config, BASE_URL);
$embedder = Embedder::fromConfig($config);
$vector_store = new VectorStore();

function print_index_status($indexer, $embedder)
{
    $rows = db_all("SELECT source_type, COUNT(DISTINCT source_id) AS sources, COUNT(*) AS chunks, SUM(token_estimate) AS tokens, SUM(embedding IS NOT NULL) AS embedded FROM tbl_ai_chunks GROUP BY source_type ORDER BY source_type");
    echo str_pad('Source', 12) . str_pad('Records', 9, ' ', STR_PAD_LEFT) . str_pad('Chunks', 8, ' ', STR_PAD_LEFT) . str_pad('Tokens~', 10, ' ', STR_PAD_LEFT) . str_pad('Embedded', 10, ' ', STR_PAD_LEFT) . "\n";
    $totals = ['sources' => 0, 'chunks' => 0, 'tokens' => 0, 'embedded' => 0];
    foreach ($rows as $row) {
        echo str_pad($row['source_type'], 12) . str_pad($row['sources'], 9, ' ', STR_PAD_LEFT) . str_pad($row['chunks'], 8, ' ', STR_PAD_LEFT) . str_pad($row['tokens'], 10, ' ', STR_PAD_LEFT) . str_pad($row['embedded'], 10, ' ', STR_PAD_LEFT) . "\n";
        foreach ($totals as $key => $value) {
            $totals[$key] += (int) $row[$key];
        }
    }
    echo str_pad('TOTAL', 12) . str_pad($totals['sources'], 9, ' ', STR_PAD_LEFT) . str_pad($totals['chunks'], 8, ' ', STR_PAD_LEFT) . str_pad($totals['tokens'], 10, ' ', STR_PAD_LEFT) . str_pad($totals['embedded'], 10, ' ', STR_PAD_LEFT) . "\n";
    echo "Waiting for an embedding ({$embedder->model()}, {$embedder->dimension()} numbers): " . $indexer->pendingCount($embedder) . "\n";
    echo "Embeddings key: " . ($embedder->isConfigured() ? 'set' : 'NOT SET (' . $embedder->secretName() . ' in the secrets file)') . "\n";
}

if ($status_only) {
    print_index_status($indexer, $embedder);
    exit(0);
}

// One run at a time: a long embedding run must not overlap with the next cron start.
$lock = null;
if (!$dry_run) {
    $lock_file = __DIR__ . '/../storage/reindex.lock';
    if (!is_dir(dirname($lock_file))) {
        mkdir(dirname($lock_file), 0755, true);
    }
    $lock = fopen($lock_file, 'c');
    if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
        echo "Another reindex run is still working. Nothing done.\n";
        exit(0);
    }
}

$log_id = 0;
if (!$dry_run) {
    db_query("INSERT INTO tbl_ai_index_log (started_at, status) VALUES (NOW(), 'running')");
    $log_id = (int) mysqli_insert_id($conn);
}

$counts = ['sources' => 0, 'total' => 0, 'written' => 0, 'unchanged' => 0, 'deleted' => 0, 'failed' => 0];
$embedded = 0;
$status = 'ok';
$message = '';

try {
    $documents = $indexer->buildDocuments();
    $counts = $indexer->sync($documents, $dry_run);
    if ($counts['failed'] > 0) {
        $status = 'error';
        $message = "{$counts['failed']} chunks could not be saved.";
    }

    if (!$dry_run && !$no_embed) {
        if ($embedder->isConfigured()) {
            $embedded = $indexer->embedPending($embedder, $vector_store, $max_embed);
        } else {
            $message = trim($message . ' Embeddings skipped: ' . $embedder->secretName() . ' is not set.');
        }
    }
} catch (Throwable $error) {
    $status = 'error';
    $message = trim($message . ' ' . $error->getMessage());
    $embedded = $indexer->embeddedCount();
}

$warnings = $indexer->warnings();
if ($warnings) {
    $message = trim($message . ' ' . implode(' ', $warnings));
}

if ($log_id > 0) {
    db_query(
        "UPDATE tbl_ai_index_log SET finished_at = NOW(), status = ?, sources_read = ?, chunks_total = ?, chunks_written = ?, chunks_deleted = ?, chunks_embedded = ?, embedding_tokens = ?, message = ? WHERE log_id = ?",
        [$status, $counts['sources'], $counts['total'], $counts['written'], $counts['deleted'], $embedded, $embedder->tokensUsed(), mb_substr($message, 0, 500), $log_id]
    );
}

echo ($dry_run ? "DRY RUN - nothing was written, no provider was called\n" : '');
echo "Records read:        {$counts['sources']}\n";
echo "Chunks in total:     {$counts['total']}\n";
echo ($dry_run ? 'Would write:         ' : 'Written:             ') . "{$counts['written']}\n";
echo "Unchanged, skipped:  {$counts['unchanged']}\n";
echo ($dry_run ? 'Would delete:        ' : 'Deleted:             ') . "{$counts['deleted']}\n";
echo "Embedded this run:   $embedded" . ($embedder->tokensUsed() > 0 ? " ({$embedder->tokensUsed()} tokens)" : '') . "\n";
echo "Status:              $status" . ($message !== '' ? " - $message" : '') . "\n";

if (!$dry_run) {
    echo "\n";
    print_index_status($indexer, $embedder);
}

exit($status === 'ok' ? 0 : 1);
