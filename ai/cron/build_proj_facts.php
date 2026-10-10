<?php
// Keeps tbl_ai_proj_facts in step with tbl_proj. Safe to run as often as you like:
// rows whose source text has not changed are skipped.
//
//   php ai/cron/build_proj_facts.php            update changed rows
//   php ai/cron/build_proj_facts.php --dry-run  show what would change, write nothing
//   php ai/cron/build_proj_facts.php --report   also list every row with a parsing problem
//   php ai/cron/build_proj_facts.php --force    rebuild every row

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require __DIR__ . '/../../db/config.php';
require __DIR__ . '/../ProjectFacts.php';
while (ob_get_level() > 0) {
    ob_end_flush();
}

$options = array_slice($argv, 1);
$dry_run = in_array('--dry-run', $options, true);
$report = in_array('--report', $options, true);
$force = in_array('--force', $options, true);

$columns = implode(', ', ProjectFacts::SOURCE_COLUMNS);
$projects = db_query("SELECT prj_id, proj_name, $columns FROM tbl_proj WHERE is_active = '1' ORDER BY prj_id");
if (!$projects) {
    fwrite(STDERR, "Could not read tbl_proj.\n");
    exit(1);
}

$existing = [];
$existing_rows = db_query("SELECT prj_id, source_hash FROM tbl_ai_proj_facts");
if (!$existing_rows) {
    fwrite(STDERR, "Could not read tbl_ai_proj_facts. Has db/migrations/001_create_tbl_ai_proj_facts.sql been applied?\n");
    exit(1);
}
while ($row = mysqli_fetch_assoc($existing_rows)) {
    $existing[(int) $row['prj_id']] = $row['source_hash'];
}

$counts = ['active' => 0, 'written' => 0, 'unchanged' => 0, 'removed' => 0, 'failed' => 0];
$stats = ['price' => 0, 'on_request' => 0, 'bedrooms' => 0, 'size' => 0, 'handover' => 0];
$problems = [];
$seen = [];

while ($project = mysqli_fetch_assoc($projects)) {
    $prj_id = (int) $project['prj_id'];
    $seen[$prj_id] = true;
    $counts['active']++;

    $facts = ProjectFacts::parse($project);
    $hash = ProjectFacts::sourceHash($project);

    $stats['price'] += $facts['price_aed'] !== null ? 1 : 0;
    $stats['on_request'] += $facts['price_on_request'];
    $stats['bedrooms'] += $facts['bedrooms_min'] !== null ? 1 : 0;
    $stats['size'] += $facts['size_sqft'] !== null ? 1 : 0;
    $stats['handover'] += ($facts['handover_date'] !== null || $facts['handover_completed']) ? 1 : 0;
    if ($facts['parse_notes'] !== '') {
        $problems[] = "#$prj_id {$project['proj_name']}: {$facts['parse_notes']}";
    }

    if (!$force && isset($existing[$prj_id]) && $existing[$prj_id] === $hash) {
        $counts['unchanged']++;
        continue;
    }

    if ($dry_run) {
        $counts['written']++;
        continue;
    }

    $saved = db_query(
        "INSERT INTO tbl_ai_proj_facts
            (prj_id, price_aed, price_period, price_on_request, bedrooms_min, bedrooms_max, bedrooms_list, has_studio,
             size_sqft, handover_year, handover_quarter, handover_date, handover_completed, parse_notes, source_hash)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            price_aed = VALUES(price_aed), price_period = VALUES(price_period), price_on_request = VALUES(price_on_request),
            bedrooms_min = VALUES(bedrooms_min), bedrooms_max = VALUES(bedrooms_max), bedrooms_list = VALUES(bedrooms_list),
            has_studio = VALUES(has_studio), size_sqft = VALUES(size_sqft), handover_year = VALUES(handover_year),
            handover_quarter = VALUES(handover_quarter), handover_date = VALUES(handover_date),
            handover_completed = VALUES(handover_completed), parse_notes = VALUES(parse_notes), source_hash = VALUES(source_hash)",
        [
            $prj_id, $facts['price_aed'], $facts['price_period'], $facts['price_on_request'],
            $facts['bedrooms_min'], $facts['bedrooms_max'], $facts['bedrooms_list'], $facts['has_studio'],
            $facts['size_sqft'], $facts['handover_year'], $facts['handover_quarter'], $facts['handover_date'],
            $facts['handover_completed'], $facts['parse_notes'], $hash,
        ]
    );

    if ($saved) {
        $counts['written']++;
    } else {
        $counts['failed']++;
    }
}

// Projects that were deleted or switched off lose their facts row.
foreach (array_keys($existing) as $prj_id) {
    if (isset($seen[$prj_id])) {
        continue;
    }
    if ($dry_run || db_query("DELETE FROM tbl_ai_proj_facts WHERE prj_id = ?", [$prj_id])) {
        $counts['removed']++;
    } else {
        $counts['failed']++;
    }
}

$active = max(1, $counts['active']);
$percent = function ($number) use ($active) {
    return str_pad($number, 5, ' ', STR_PAD_LEFT) . '  (' . round($number * 100 / $active) . '%)';
};

echo ($dry_run ? "DRY RUN - nothing was written\n" : '');
echo "Active projects:      {$counts['active']}\n";
echo ($dry_run ? 'Would write:          ' : 'Written:              ') . "{$counts['written']}\n";
echo "Unchanged, skipped:   {$counts['unchanged']}\n";
echo ($dry_run ? 'Would remove:         ' : 'Removed:              ') . "{$counts['removed']}\n";
echo "Failed to save:       {$counts['failed']}\n\n";
echo "With a price:         " . $percent($stats['price']) . "\n";
echo "Price on request:     " . $percent($stats['on_request']) . "\n";
echo "With bedrooms:        " . $percent($stats['bedrooms']) . "\n";
echo "With a size:          " . $percent($stats['size']) . "\n";
echo "With a handover:      " . $percent($stats['handover']) . "\n";
echo "Parsing problems:     " . count($problems) . ($problems && !$report ? "  (run with --report to list them)" : '') . "\n";

if ($report && $problems) {
    echo "\n" . implode("\n", $problems) . "\n";
}

exit($counts['failed'] > 0 ? 1 : 0);
