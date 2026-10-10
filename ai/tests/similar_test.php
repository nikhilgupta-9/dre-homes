<?php
// Run: php ai/tests/similar_test.php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require __DIR__ . '/../SimilarProjects.php';

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

function project($prj_id, array $changes = [])
{
    return array_merge([
        'prj_id' => $prj_id, 'group' => 1, 'comm_id' => 10, 'dev_id' => 5, 'place' => 'dubai marina',
        'price' => 2000000.0, 'period' => 'total', 'bedrooms' => ['1', '2'], 'kinds' => ['apartment'],
    ], $changes);
}

check('kinds are read from the type text', SimilarProjects::kinds('Apartments, Duplexes & Penthouses'), ['apartment', 'penthouse', 'duplex']);
check('place text is normalised', SimilarProjects::normalizePlace(' Palm Jumeirah, Dubai '), 'palm jumeirah dubai');
check('overlap of equal lists', SimilarProjects::overlap(['1', '2'], ['2', '1']), 1.0);
check('overlap of different lists', SimilarProjects::overlap(['1'], ['4']), 0.0);

$base = project(1);
check('identical facts score 1', round(SimilarProjects::factScore($base, project(2)), 4), 1.0);
$near = SimilarProjects::factScore($base, project(2, ['price' => 2200000.0]));
$far = SimilarProjects::factScore($base, project(3, ['price' => 3900000.0]));
check('a closer price scores higher', $near > $far, true);
$known = SimilarProjects::factScore($base, project(2, ['price' => 2100000.0]));
$unknown = SimilarProjects::factScore($base, project(3, ['price' => 0.0]));
check('a known close price beats an unknown price', $known > $unknown, true);
$same_place = SimilarProjects::factScore($base, project(2, ['dev_id' => 9]));
$other_place = SimilarProjects::factScore($base, project(3, ['comm_id' => 20, 'place' => 'business bay']));
check('same community beats same developer elsewhere', $same_place > $other_place, true);
check('yearly rent is not compared with a sale price', SimilarProjects::factScore($base, project(2, ['period' => 'year'])) < 1.0, true);
check('nothing to compare gives null', SimilarProjects::factScore(
    project(1, ['comm_id' => 0, 'dev_id' => 0, 'place' => '', 'price' => 0.0, 'bedrooms' => [], 'kinds' => []]),
    project(2, ['comm_id' => 0, 'dev_id' => 0, 'place' => '', 'price' => 0.0, 'bedrooms' => [], 'kinds' => []])
), null);

check('facts only', SimilarProjects::combine(0.8, null), 0.8);
check('facts and meaning are averaged', round(SimilarProjects::combine(0.8, 0.4), 4), 0.6);
check('negative similarity counts as zero', round(SimilarProjects::combine(0.8, -0.3), 4), 0.4);

// Ranking: never itself, never another type, best first, at most KEEP.
$projects = [project(1), project(2, ['price' => 2100000.0]), project(3, ['comm_id' => 20, 'place' => 'business bay', 'price' => 9000000.0]), project(4, ['group' => 7])];
for ($i = 10; $i < 25; $i++) {
    $projects[] = project($i, ['price' => 2000000.0 + $i * 50000]);
}
$ranked = SimilarProjects::rank($projects);
check('a project is not similar to itself', isset($ranked[1][1]), false);
check('other project types are left out', isset($ranked[1][4]), false);
check('the only project of a type has an empty list', $ranked[4], []);
check('list is cut to KEEP', count($ranked[1]), SimilarProjects::KEEP);
check('best match comes first', array_key_first($ranked[1]), 2);
$scores = array_values($ranked[1]);
$sorted = $scores;
rsort($sorted);
check('list is ordered by score', $scores, $sorted);

// With embeddings, meaning can lift a project that is weaker on facts.
$projects = [project(1), project(2, ['price' => 2600000.0]), project(3, ['price' => 2700000.0])];
$vectors = [1 => [1.0, 0.0], 2 => [0.0, 1.0], 3 => [1.0, 0.0]];
$ranked = SimilarProjects::rank($projects, $vectors);
check('meaning changes the order', array_key_first($ranked[1]), 3);

echo $failed === 0 ? "OK: $checked checks passed\n" : "$failed of $checked checks failed\n";
exit($failed === 0 ? 0 : 1);
