<?php
// Run: php ai/tests/index_test.php
// Uses the local database: it adds a few rows with source_type 'selftest' to
// tbl_ai_chunks and removes them again.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require __DIR__ . '/../../db/config.php';
require __DIR__ . '/../Indexer.php';
require __DIR__ . '/../VectorStore.php';
while (ob_get_level() > 0) {
    ob_end_flush();
}

$failed = 0;
$checked = 0;

function check($label, $actual, $expected)
{
    global $failed, $checked;
    $checked++;
    if ($actual !== $expected) {
        $failed++;
        echo "FAIL $label\n  expected: " . var_export($expected, true) . "\n  actual:   " . var_export($actual, true) . "\n";
    }
}

function refuses($label, callable $read)
{
    try {
        $read();
        check($label, 'read was allowed', 'read was refused');
    } catch (RuntimeException $error) {
        check($label, 'read was refused', 'read was refused');
    }
}

$config = require __DIR__ . '/../config.php';
$indexer = new Indexer($config, BASE_URL);

// --- The allowlist: personal and internal data can never be read by the indexer ---
foreach (['tbl_enq', 'tbl_offplan_enq', 'tbl_pdf_enquiry', 'tbl_book_val_enquiry', 'tbl_goldvisa_enq', 'tbl_mortg_frm', 'tbl_resume', 'tbl_login', 'tbl_login_logs', 'tbl_activity_logs', 'tbl_ai_conversations', 'tbl_ai_messages'] as $table) {
    refuses("table $table is refused", function () use ($indexer, $table) {
        $indexer->readAllowed($table, ['name']);
    });
    check("table $table is not in the allowlist", isset($config['index_allowlist'][$table]), false);
}
foreach (['proj_email', 'proj_mobileno', 'proj_whatsaapno', 'prop_user_id'] as $column) {
    refuses("column tbl_proj.$column is refused", function () use ($indexer, $column) {
        $indexer->readAllowed('tbl_proj', ['prj_id', $column]);
    });
}
foreach (['comm_phoneno', 'comm_email'] as $column) {
    refuses("column tbl_community.$column is refused", function () use ($indexer, $column) {
        $indexer->readAllowed('tbl_community', [$column]);
    });
}
foreach (['dev_phoneno', 'dev_email'] as $column) {
    refuses("column tbl_developer.$column is refused", function () use ($indexer, $column) {
        $indexer->readAllowed('tbl_developer', [$column]);
    });
}
foreach (array_keys($config['index_allowlist']) as $table) {
    check("allowlisted table $table is not a lead, resume, login or log table", preg_match('/enq|enquiry|resume|login|_logs?$|frm|user|feedback|conversation|message/i', $table), 0);
}
check('an allowlisted read works', count($indexer->readAllowed('tbl_proj_type', ['prj_type_id', 'proj_type_name'])) > 0, true);

// --- Only active records are indexed ---
$inactive = db_row("SELECT COUNT(*) AS n FROM tbl_ai_chunks c JOIN tbl_proj p ON c.source_type = 'project' AND c.source_id = p.prj_id WHERE p.is_active <> '1'");
check('no chunk belongs to an inactive project', (int) $inactive['n'], 0);

// --- Text tools ---
check('html to text', TextTools::htmlToText('<p>Hello&nbsp;<b>world</b></p><script>alert(1)</script><ul><li>One</li><li>Two</li></ul>'), "Hello world\n\n- One\n\n- Two");
check('short text is one chunk', TextTools::chunk('Short text.', 100, 20), ['Short text.']);
$long = implode("\n\n", array_fill(0, 12, str_repeat('Dubai property market sentence. ', 8)));
$chunks = TextTools::chunk($long, 600, 100);
check('long text is split', count($chunks) > 3, true);
check('no chunk is far over the limit', max(array_map('mb_strlen', $chunks)) <= 750, true);
check('nothing is lost when splitting', preg_replace('/\s+/', '', implode('', $chunks)), preg_replace('/\s+/', '', $long));
check('page text comes from between the markers', Indexer::extractPageText('<header>Menu</header><!--content--><p>Body</p><!--content  end--><footer>Foot</footer>'), 'Body');
check('page without markers gives nothing', Indexer::extractPageText('<p>Body</p>'), '');

// --- Vectors ---
$vector = [3.0, 4.0, 0.0];
$unpacked = VectorStore::unpack(VectorStore::pack($vector));
check('vector is stored at length 1', [round($unpacked[0], 4), round($unpacked[1], 4), round($unpacked[2], 4)], [0.6, 0.8, 0.0]);
check('rank fusion puts a result found by both searches first', array_keys(VectorStore::fuse([[10, 20, 30], [30, 40, 10]]))[0], 10);

$store = new VectorStore();
db_query("DELETE FROM tbl_ai_chunks WHERE source_type = 'selftest'");
$samples = [
    'beach' => ['Zxqbeach villas', 'Zxqbeach villas by the sea with a private shore.', [1.0, 0.1, 0.0]],
    'golf' => ['Zxqgolf townhouses', 'Zxqgolf townhouses beside the fairway.', [0.1, 1.0, 0.0]],
    'city' => ['Zxqcity apartments', 'Zxqcity apartments in the business district.', [0.0, 0.1, 1.0]],
];
$ids = [];
foreach ($samples as $key => $sample) {
    db_query("INSERT INTO tbl_ai_chunks (source_type, source_id, chunk_no, chunk_kind, title, url_path, chunk_text, content_hash) VALUES ('selftest', ?, 0, 'summary', ?, '', ?, ?)", [$key, $sample[0], $sample[1], md5($sample[1])]);
    $ids[$key] = (int) mysqli_insert_id($conn);
    $store->saveEmbedding($ids[$key], $sample[2], 'selftest-model');
}

$scores = $store->searchByVector([0.9, 0.2, 0.0], 'selftest-model', 3, ['selftest']);
check('vector search ranks the closest chunk first', array_keys($scores), [$ids['beach'], $ids['golf'], $ids['city']]);
check('vector search ignores other models', $store->searchByVector([0.9, 0.2, 0.0], 'another-model', 3, ['selftest']), []);
check('keyword search finds an exact word', array_keys($store->searchByKeyword('Zxqgolf', 3, ['selftest'])), [$ids['golf']]);
$found = $store->search('Zxqcity', [0.9, 0.2, 0.0], 'selftest-model', 3, ['selftest']);
check('hybrid search returns chunk rows', count($found) === 3 && isset($found[0]['chunk_text']) && !isset($found[0]['embedding']), true);
$found = $store->search('Zxqcity', null, 'selftest-model', 3, ['selftest']);
check('keyword-only search works without a vector', array_column($found, 'chunk_id') == [$ids['city']], true);

db_query("DELETE FROM tbl_ai_chunks WHERE source_type = 'selftest'");
$left = db_row("SELECT COUNT(*) AS n FROM tbl_ai_chunks WHERE source_type = 'selftest'");
check('test rows are removed', (int) $left['n'], 0);

echo $failed === 0 ? "OK: $checked checks passed\n" : "$failed of $checked checks failed\n";
exit($failed === 0 ? 0 : 1);
