<?php
// Run: php ai/tests/project_facts_test.php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require __DIR__ . '/../ProjectFacts.php';

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

// Prices
check('plain number', ProjectFacts::parsePrice(' 1168000'), 1168000);
check('with AED and commas', ProjectFacts::parsePrice('AED 1,872,888'), 1872888);
check('typo prefix', ProjectFacts::parsePrice('AEDD 1.91 M'), 1910000);
check('M suffix', ProjectFacts::parsePrice('1.69 M'), 1690000);
check('M no space', ProjectFacts::parsePrice('2.2M'), 2200000);
check('Mn suffix', ProjectFacts::parsePrice('13.47 Mn'), 13470000);
check('Million typo', ProjectFacts::parsePrice('1.81 Millionn'), 1810000);
check('Million word', ProjectFacts::parsePrice('36 Million'), 36000000);
check('K suffix', ProjectFacts::parsePrice('699K'), 699000);
check('comma thousands', ProjectFacts::parsePrice('495,000'), 495000);
check('call us', ProjectFacts::parsePrice('Call Us'), null);
check('zero', ProjectFacts::parsePrice('0'), null);
check('empty', ProjectFacts::parsePrice(''), null);

// Bedrooms
$b = ProjectFacts::parseBedrooms('1,2,3,STD');
check('bedrooms min', $b['min'], 0.0);
check('bedrooms max', $b['max'], 3.0);
check('bedrooms list', $b['list'], '0,1,2,3');
check('bedrooms studio', $b['has_studio'], true);
$b = ProjectFacts::parseBedrooms('3 & 4');
check('ampersand list', $b['list'], '3,4');
$b = ProjectFacts::parseBedrooms('1.5,3.5');
check('half bedrooms', $b['list'], '1.5,3.5');
$b = ProjectFacts::parseBedrooms('');
check('no bedrooms', $b['min'], null);
$b = ProjectFacts::parseBedrooms('2,penthouse');
check('unknown token kept aside', $b['unknown'], ['PENTHOUSE']);
check('known token still used', $b['list'], '2');

// Sizes
check('size plain', ProjectFacts::parseSizeSqft('1145'), 1145.0);
check('size sq ft', ProjectFacts::parseSizeSqft('1,125.37 sq ft'), 1125.37);
check('size dotted', ProjectFacts::parseSizeSqft('1565.40 SQ.FT.'), 1565.4);
check('size sq. ft.', ProjectFacts::parseSizeSqft('3,002 sq. ft.'), 3002.0);
check('size sqft', ProjectFacts::parseSizeSqft('1200 sqft'), 1200.0);
check('size sq m', ProjectFacts::parseSizeSqft('153.224 Sq. M.'), round(153.224 * 10.7639, 2));
check('size text', ProjectFacts::parseSizeSqft('Hotel Edge by Rotana in Damac Hills 2 logo'), null);

// Handover
$h = ProjectFacts::parseHandover(' Completion: Q2 2026');
check('quarter year', [$h['year'], $h['quarter'], $h['date']], [2026, 2, '2026-06-30']);
$h = ProjectFacts::parseHandover('Handover : Q4 2028');
check('quarter 4', $h['date'], '2028-12-31');
$h = ProjectFacts::parseHandover('Handover: Q1 2030');
check('quarter 1', $h['date'], '2030-03-31');
$h = ProjectFacts::parseHandover('Handover: 2029');
check('year only', [$h['year'], $h['quarter'], $h['date']], [2029, null, '2029-12-31']);
$h = ProjectFacts::parseHandover('Completed');
check('completed', [$h['completed'], $h['year']], [true, null]);
$h = ProjectFacts::parseHandover('1');
check('junk handover', [$h['completed'], $h['year']], [false, null]);

// Whole row
$facts = ProjectFacts::parse(['proj_price' => 'Call Us', 'proj_start_price' => '1.01 Million', 'proj_price_type' => '', 'proj_bedroom' => 'STD,1,2', 'proj_size' => '', 'proj_area' => '1145', 'proj_handover' => 'Handover: Q3 2027']);
check('row price falls back to start price', $facts['price_aed'], 1010000);
check('row not on request', $facts['price_on_request'], 0);
check('row size falls back to area', $facts['size_sqft'], 1145.0);
check('row notes empty', $facts['parse_notes'], '');
$facts = ProjectFacts::parse(['proj_price' => 'CALL US ', 'proj_start_price' => '', 'proj_price_type' => 'year', 'proj_bedroom' => '', 'proj_size' => '', 'proj_area' => '', 'proj_handover' => '']);
check('row on request', [$facts['price_aed'], $facts['price_on_request'], $facts['price_period']], [null, 1, 'year']);
check('hash changes with text', ProjectFacts::sourceHash(['proj_price' => '1']) !== ProjectFacts::sourceHash(['proj_price' => '2']), true);

echo $failed === 0 ? "OK: $checked checks passed\n" : "$failed of $checked checks failed\n";
exit($failed === 0 ? 0 : 1);
