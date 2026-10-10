<?php
// One cron entry for the whole AI data pipeline, in the order the steps depend on each other:
//   1. build_proj_facts.php  numbers from the project text columns
//   2. reindex.php           search index and embeddings
//   3. build_similar.php     "More Like This" lists
//   4. cleanup.php           deletes chat transcripts past the retention period
//
// Cron line (hourly):  0 * * * *  /path/to/php /path/to/site/ai/cron/hourly.php
// If the host does not allow PHP to start programs, add the scripts to cron one after another instead.

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

if (!function_exists('passthru')) {
    fwrite(STDERR, "passthru() is disabled on this host. Run the scripts in ai/cron/ from cron separately.\n");
    exit(1);
}

$exit_code = 0;
foreach (['build_proj_facts.php', 'reindex.php', 'build_similar.php', 'cleanup.php'] as $script) {
    echo "===== $script =====\n";
    passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/' . $script), $code);
    if ($code !== 0) {
        echo "$script ended with code $code\n";
        $exit_code = 1;
    }
    echo "\n";
}
exit($exit_code);
