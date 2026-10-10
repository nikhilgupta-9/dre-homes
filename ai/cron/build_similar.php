<?php
// Works out the "More Like This" list for every project and stores it in tbl_ai_similar.
// Run after build_proj_facts.php and reindex.php. If nothing it depends on has changed
// since the last run, it stops straight away.
//
//   php ai/cron/build_similar.php            update
//   php ai/cron/build_similar.php --force    rebuild even if nothing changed
//   php ai/cron/build_similar.php --show=455 print the list stored for one project

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require __DIR__ . '/../../db/config.php';
require __DIR__ . '/../VectorStore.php';
require __DIR__ . '/../SimilarProjects.php';
while (ob_get_level() > 0) {
    ob_end_flush();
}

$options = array_slice($argv, 1);
$force = in_array('--force', $options, true);
foreach ($options as $option) {
    if (preg_match('/^--show=(\d+)$/', $option, $match)) {
        $rows = db_all(
            "SELECT s.rank_no, s.score, p.prj_id, p.proj_name, p.proj_location, p.proj_price, p.proj_bedroom
             FROM tbl_ai_similar s JOIN tbl_proj p ON p.prj_id = s.similar_prj_id WHERE s.prj_id = ? ORDER BY s.rank_no",
            [(int) $match[1]]
        );
        $self = db_row("SELECT proj_name, proj_location, proj_price, proj_bedroom FROM tbl_proj WHERE prj_id = ?", [(int) $match[1]]);
        echo "For: " . ($self ? "{$self['proj_name']} | {$self['proj_location']} | {$self['proj_price']} | {$self['proj_bedroom']}" : 'unknown project') . "\n";
        foreach ($rows as $row) {
            echo "{$row['rank_no']}. {$row['score']}  #{$row['prj_id']} {$row['proj_name']} | {$row['proj_location']} | {$row['proj_price']} | {$row['proj_bedroom']}\n";
        }
        exit(0);
    }
}

$config = require __DIR__ . '/../config.php';
$type_ids = array_keys($config['project_url_prefix']);

$started = microtime(true);
$projects = SimilarProjects::loadProjects($type_ids);
$vectors = SimilarProjects::loadVectors($config['embedding_providers'][$config['embedding_provider']]['model'], $config['embedding_dim']);

// Fingerprint of everything the result depends on.
$embedded = db_row("SELECT COUNT(*) AS chunks, COALESCE(MAX(tstp), '') AS latest FROM tbl_ai_chunks WHERE source_type = 'project' AND embedding IS NOT NULL");
$signature = md5(json_encode([$projects, $embedded, SimilarProjects::WEIGHTS, SimilarProjects::UNKNOWN_FACT, SimilarProjects::MEANING_SHARE, SimilarProjects::KEEP]));
$signature_file = __DIR__ . '/../storage/similar.signature';
if (!$force && is_file($signature_file) && trim((string) file_get_contents($signature_file)) === $signature) {
    echo "Nothing changed since the last run. Use --force to rebuild.\n";
    exit(0);
}

$ranked = SimilarProjects::rank($projects, $vectors);

$rows_written = 0;
$projects_with_list = 0;
$failed = 0;

mysqli_begin_transaction($conn);
if (!db_query("DELETE FROM tbl_ai_similar")) {
    $failed++;
}
$batch = [];
$params = [];
$flush = function () use (&$batch, &$params, &$failed, &$rows_written) {
    if (empty($batch)) {
        return;
    }
    if (db_query("INSERT INTO tbl_ai_similar (prj_id, similar_prj_id, rank_no, score) VALUES " . implode(',', $batch), $params)) {
        $rows_written += count($batch);
    } else {
        $failed++;
    }
    $batch = [];
    $params = [];
};
foreach ($ranked as $prj_id => $similar) {
    if ($similar) {
        $projects_with_list++;
    }
    $rank_no = 0;
    foreach ($similar as $similar_prj_id => $score) {
        $rank_no++;
        $batch[] = '(?, ?, ?, ?)';
        array_push($params, (int) $prj_id, (int) $similar_prj_id, $rank_no, number_format($score, 4, '.', ''));
        if (count($batch) >= 500) {
            $flush();
        }
    }
}
$flush();

if ($failed > 0) {
    mysqli_rollback($conn);
    fwrite(STDERR, "Could not save the lists; the previous ones are kept.\n");
    exit(1);
}
mysqli_commit($conn);

if (!is_dir(dirname($signature_file))) {
    mkdir(dirname($signature_file), 0755, true);
}
file_put_contents($signature_file, $signature);

echo "Projects compared:       " . count($projects) . "\n";
echo "With an embedding:       " . count($vectors) . ($vectors ? '' : '  (facts only - embeddings not built yet)') . "\n";
echo "Projects with a list:    $projects_with_list\n";
echo "Rows written:            $rows_written\n";
echo "Time:                    " . round(microtime(true) - $started, 1) . " s\n";
