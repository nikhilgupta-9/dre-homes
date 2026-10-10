<?php
// Turns the free-text columns of a tbl_proj row into numbers and dates.
// Pure functions: no database access here, so they can be tested on their own.

class ProjectFacts
{
    // Raise this when the parsing rules change, so every row is rebuilt on the next run.
    const PARSER_VERSION = 1;

    const SQM_TO_SQFT = 10.7639;

    // The tbl_proj columns the facts are built from.
    const SOURCE_COLUMNS = ['proj_price', 'proj_start_price', 'proj_price_type', 'proj_bedroom', 'proj_size', 'proj_area', 'proj_handover'];

    public static function sourceHash(array $row)
    {
        $parts = [self::PARSER_VERSION];
        foreach (self::SOURCE_COLUMNS as $column) {
            $parts[] = (string) ($row[$column] ?? '');
        }
        return md5(implode("\x1f", $parts));
    }

    public static function parse(array $row)
    {
        $notes = [];

        $price_text = trim((string) ($row['proj_price'] ?? ''));
        $start_price_text = trim((string) ($row['proj_start_price'] ?? ''));
        $price = self::parsePrice($price_text);
        if ($price === null) {
            $price = self::parsePrice($start_price_text);
        }
        if ($price === null && self::hasDigit($price_text . $start_price_text)) {
            $notes[] = 'price not understood: ' . ($price_text !== '' ? $price_text : $start_price_text);
        }

        $bedrooms = self::parseBedrooms((string) ($row['proj_bedroom'] ?? ''));
        if (!empty($bedrooms['unknown'])) {
            $notes[] = 'bedroom not understood: ' . implode(' | ', $bedrooms['unknown']);
        }

        $size_text = trim((string) ($row['proj_size'] ?? ''));
        $area_text = trim((string) ($row['proj_area'] ?? ''));
        $size = self::parseSizeSqft($size_text);
        if ($size === null) {
            $size = self::parseSizeSqft($area_text);
        }
        if ($size === null && $size_text . $area_text !== '') {
            $notes[] = 'size not understood: ' . ($size_text !== '' ? $size_text : $area_text);
        }

        $handover_text = trim((string) ($row['proj_handover'] ?? ''));
        $handover = self::parseHandover($handover_text);
        if ($handover_text !== '' && $handover['year'] === null && !$handover['completed']) {
            $notes[] = 'handover not understood: ' . $handover_text;
        }

        return [
            'price_aed' => $price,
            'price_period' => self::parsePricePeriod((string) ($row['proj_price_type'] ?? '')),
            'price_on_request' => $price === null ? 1 : 0,
            'bedrooms_min' => $bedrooms['min'],
            'bedrooms_max' => $bedrooms['max'],
            'bedrooms_list' => $bedrooms['list'],
            'has_studio' => $bedrooms['has_studio'] ? 1 : 0,
            'size_sqft' => $size,
            'handover_year' => $handover['year'],
            'handover_quarter' => $handover['quarter'],
            'handover_date' => $handover['date'],
            'handover_completed' => $handover['completed'] ? 1 : 0,
            'parse_notes' => mb_substr(implode('; ', $notes), 0, 255),
        ];
    }

    // "1168000", "AED 1,872,888", "1.69 M", "13.47 Mn", "699K", "36 Million" -> whole dirhams.
    // "Call Us", "0" and empty text -> null (price on request).
    public static function parsePrice($text)
    {
        if (!preg_match('/([0-9][0-9,]*(?:\.[0-9]+)?)\s*(k|thousand|mn|million\w*|m|bn|billion\w*|b)?(?![a-z])/i', (string) $text, $match)) {
            return null;
        }

        $number = (float) str_replace(',', '', $match[1]);
        $unit = strtolower($match[2] ?? '');

        if ($unit === 'k' || $unit === 'thousand') {
            $number *= 1000;
        } elseif ($unit !== '' && $unit[0] === 'm') {
            $number *= 1000000;
        } elseif ($unit !== '' && $unit[0] === 'b') {
            $number *= 1000000000;
        }

        $price = (int) round($number);
        return $price > 0 ? $price : null;
    }

    public static function parsePricePeriod($text)
    {
        $text = strtolower(trim($text));
        if (strpos($text, 'year') !== false || strpos($text, 'annual') !== false) {
            return 'year';
        }
        if (strpos($text, 'month') !== false) {
            return 'month';
        }
        return 'total';
    }

    // "1,2,3,STD" -> min 0, max 3, list "0,1,2,3", studio yes. A studio counts as 0 bedrooms.
    public static function parseBedrooms($text)
    {
        $values = [];
        $unknown = [];
        $has_studio = false;

        foreach (preg_split('/[,&\/]|\band\b|\bto\b/i', $text) as $token) {
            $token = strtoupper(trim($token));
            if ($token === '') {
                continue;
            }
            if ($token === 'STD' || strpos($token, 'STUDIO') === 0) {
                $has_studio = true;
                $values[] = 0.0;
            } elseif (preg_match('/^([0-9]{1,2}(?:\.[0-9])?)\s*(?:BR|BDR|BED\w*)?\+?$/', $token, $match)) {
                $values[] = (float) $match[1];
            } else {
                $unknown[] = $token;
            }
        }

        $values = array_values(array_unique($values));
        sort($values);

        return [
            'min' => $values ? $values[0] : null,
            'max' => $values ? $values[count($values) - 1] : null,
            'list' => implode(',', array_map(function ($value) {
                return rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.');
            }, $values)),
            'has_studio' => $has_studio,
            'unknown' => $unknown,
        ];
    }

    // "1145", "1,125.37 sq ft", "1565.40 SQ.FT." -> square feet. "153.224 Sq. M." is converted.
    public static function parseSizeSqft($text)
    {
        $text = trim((string) $text);
        if (!preg_match('/^([0-9][0-9,]*(?:\.[0-9]+)?)\s*(.*)$/', $text, $match)) {
            return null;
        }

        $number = (float) str_replace(',', '', $match[1]);
        $unit = strtolower(preg_replace('/[\s.]+/', '', $match[2]));

        if ($unit === '' || in_array($unit, ['sqft', 'sf', 'ft', 'ft2', 'squarefeet', 'squarefoot'], true)) {
            $sqft = $number;
        } elseif (in_array($unit, ['sqm', 'm2', 'm²', 'sqmt', 'sqmtr', 'squaremeter', 'squaremeters', 'squaremetre', 'squaremetres'], true)) {
            $sqft = $number * self::SQM_TO_SQFT;
        } else {
            return null;
        }

        return $sqft > 0 ? round($sqft, 2) : null;
    }

    // "Handover: Q4 2027" -> 2027, quarter 4, 2027-12-31. "Handover: 2029" -> 2029, 2029-12-31.
    // "Completed" -> completed. The date is the last day of the quarter or year.
    public static function parseHandover($text)
    {
        $result = ['year' => null, 'quarter' => null, 'date' => null, 'completed' => false];
        $text = trim((string) $text);

        if (preg_match('/\bQ\s*([1-4])\s*[-,\/]?\s*(20[0-9]{2})\b/i', $text, $match)) {
            $result['quarter'] = (int) $match[1];
            $result['year'] = (int) $match[2];
            $last_month = $result['quarter'] * 3;
            $result['date'] = date('Y-m-t', mktime(0, 0, 0, $last_month, 1, $result['year']));
        } elseif (preg_match('/\b(20[0-9]{2})\b/', $text, $match)) {
            $result['year'] = (int) $match[1];
            $result['date'] = $result['year'] . '-12-31';
        } elseif (preg_match('/^(completed|ready|ready to move( in)?)$/i', $text)) {
            $result['completed'] = true;
        }

        return $result;
    }

    private static function hasDigit($text)
    {
        return preg_match('/[1-9]/', $text) === 1;
    }
}
