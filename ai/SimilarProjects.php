<?php
// "More Like This": which projects are most like a given project.
//
// Two signals are mixed:
//   - meaning: how close the project texts are (embeddings from tbl_ai_chunks), when they exist
//   - facts:   same community or location, close price, shared bedroom counts,
//              same kind of home, same developer
// Without embeddings the facts alone are used, so the block works before the
// embeddings key is set and gets better once it is.

class SimilarProjects
{
    const KEEP = 8;

    // Weight of each fact; they add up to 1.
    const WEIGHTS = ['place' => 0.35, 'price' => 0.25, 'bedrooms' => 0.15, 'kind' => 0.15, 'developer' => 0.10];

    // Value used for a fact that cannot be compared because it is missing on either side.
    const UNKNOWN_FACT = 0.3;

    // Share of the final score that comes from meaning when both projects have an embedding.
    const MEANING_SHARE = 0.5;

    const KINDS = ['apartment', 'villa', 'townhouse', 'penthouse', 'duplex', 'studio', 'mansion', 'plot', 'office', 'hotel', 'loft', 'residence'];

    // ---------------------------------------------------------------
    // For the property pages
    // ---------------------------------------------------------------

    // Rows of tbl_proj for the "More Like This" block, best match first, or false when
    // nothing has been worked out for this project yet (the page then uses its old query).
    public static function forPage($prj_id, $prj_type_id, $limit = self::KEEP)
    {
        $result = db_query(
            "SELECT p.* FROM tbl_ai_similar s
             JOIN tbl_proj p ON p.prj_id = s.similar_prj_id
             WHERE s.prj_id = ? AND p.is_active = '1' AND p.prj_type_id = ?
             ORDER BY s.rank_no LIMIT ?",
            [(int) $prj_id, (int) $prj_type_id, (int) $limit]
        );
        if (!$result || $result === true || mysqli_num_rows($result) === 0) {
            return false;
        }
        return $result;
    }

    // Page address of a project row, by its type: "property/slug", "rent-property/slug" ...
    public static function urlPath(array $row)
    {
        static $prefixes = null;
        if ($prefixes === null) {
            $config = require __DIR__ . '/config.php';
            $prefixes = $config['project_url_prefix'];
        }
        return ($prefixes[(int) $row['prj_type_id']] ?? 'property/') . $row['proj_slug'];
    }

    // ---------------------------------------------------------------
    // Scoring (pure functions)
    // ---------------------------------------------------------------

    // "Apartments, Duplexes & Penthouses" -> ['apartment', 'duplex', 'penthouse']
    public static function kinds($text)
    {
        $text = strtolower((string) $text);
        $found = [];
        foreach (self::KINDS as $kind) {
            if (strpos($text, $kind) !== false) {
                $found[] = $kind;
            }
        }
        return $found;
    }

    public static function normalizePlace($text)
    {
        return trim(preg_replace('/[^a-z0-9]+/', ' ', strtolower((string) $text)));
    }

    // Share of items two lists have in common (0 to 1).
    public static function overlap(array $a, array $b)
    {
        $union = count(array_unique(array_merge($a, $b)));
        return $union === 0 ? 0.0 : (float) (count(array_intersect($a, $b)) / $union);
    }

    // Each project: prj_id, comm_id, dev_id, place, price, period, bedrooms[], kinds[].
    // Returns a score from 0 to 1, or null when no fact can be compared.
    public static function factScore(array $a, array $b)
    {
        $parts = [];

        if ($a['comm_id'] > 0 && $a['comm_id'] === $b['comm_id']) {
            $parts['place'] = 1.0;
        } elseif ($a['place'] !== '' && $b['place'] !== '') {
            if ($a['place'] === $b['place']) {
                $parts['place'] = 1.0;
            } else {
                // "dubai marina" and "marina gate dubai marina" share most words.
                $parts['place'] = 0.6 * self::overlap(explode(' ', $a['place']), explode(' ', $b['place']));
            }
        }

        if ($a['price'] > 0 && $b['price'] > 0 && $a['period'] === $b['period']) {
            // Same price = 1; double or half the price = 0.
            $parts['price'] = max(0.0, 1.0 - abs(log($a['price'] / $b['price'])) / log(2));
        }

        if ($a['bedrooms'] && $b['bedrooms']) {
            $parts['bedrooms'] = self::overlap($a['bedrooms'], $b['bedrooms']);
        }

        if ($a['kinds'] && $b['kinds']) {
            $parts['kind'] = self::overlap($a['kinds'], $b['kinds']);
        }

        if ($a['dev_id'] > 0 && $b['dev_id'] > 0) {
            $parts['developer'] = $a['dev_id'] === $b['dev_id'] ? 1.0 : 0.0;
        }

        if (empty($parts)) {
            return null;
        }

        // A fact that is unknown for either project counts as a weak match, so a project
        // with missing data never ranks above one that is known to match.
        $score = 0.0;
        foreach (self::WEIGHTS as $name => $weight) {
            $score += $weight * ($parts[$name] ?? self::UNKNOWN_FACT);
        }
        return $score;
    }

    public static function combine($fact_score, $meaning_score)
    {
        if ($meaning_score === null) {
            return $fact_score ?? 0.0;
        }
        $meaning_score = max(0.0, min(1.0, $meaning_score));
        if ($fact_score === null) {
            return $meaning_score;
        }
        return self::MEANING_SHARE * $meaning_score + (1 - self::MEANING_SHARE) * $fact_score;
    }

    // $projects: list as described at factScore(). $vectors: [prj_id => unit vector], may be empty.
    // Projects are only compared with others of the same $group (the project type).
    // Returns [prj_id => [similar_prj_id => score, ...]] with the best KEEP for each.
    public static function rank(array $projects, array $vectors = [])
    {
        $groups = [];
        foreach ($projects as $project) {
            $groups[$project['group']][] = $project;
        }

        $ranked = [];
        foreach ($groups as $members) {
            $count = count($members);
            $scores = array_fill(0, $count, []);

            for ($i = 0; $i < $count; $i++) {
                $a = $members[$i];
                $vector_a = $vectors[$a['prj_id']] ?? null;
                for ($j = $i + 1; $j < $count; $j++) {
                    $b = $members[$j];
                    $meaning = null;
                    if ($vector_a !== null && isset($vectors[$b['prj_id']])) {
                        $meaning = 0.0;
                        $vector_b = $vectors[$b['prj_id']];
                        foreach ($vector_a as $k => $value) {
                            $meaning += $value * $vector_b[$k];
                        }
                    }
                    $score = self::combine(self::factScore($a, $b), $meaning);
                    if ($score > 0.0) {
                        $scores[$i][$j] = $score;
                        $scores[$j][$i] = $score;
                    }
                }
            }

            foreach ($members as $i => $project) {
                arsort($scores[$i]);
                $best = [];
                foreach (array_slice($scores[$i], 0, self::KEEP, true) as $j => $score) {
                    $best[$members[$j]['prj_id']] = $score;
                }
                $ranked[$project['prj_id']] = $best;
            }
        }
        return $ranked;
    }

    // ---------------------------------------------------------------
    // Reading the inputs (public project data only)
    // ---------------------------------------------------------------
    public static function loadProjects(array $type_ids)
    {
        $marks = implode(',', array_fill(0, count($type_ids), '?'));
        $rows = db_all(
            "SELECT p.prj_id, p.prj_type_id, p.prj_comm_id, p.prj_dev_id, p.proj_location, p.proj_inventory_type,
                    f.price_aed, f.price_period, f.bedrooms_list
             FROM tbl_proj p LEFT JOIN tbl_ai_proj_facts f ON f.prj_id = p.prj_id
             WHERE p.is_active = '1' AND p.prj_type_id IN ($marks) ORDER BY p.prj_id",
            array_map('intval', $type_ids)
        );

        $projects = [];
        foreach ($rows as $row) {
            $projects[] = [
                'prj_id' => (int) $row['prj_id'],
                'group' => (int) $row['prj_type_id'],
                'comm_id' => (int) $row['prj_comm_id'],
                'dev_id' => (int) $row['prj_dev_id'],
                'place' => self::normalizePlace($row['proj_location']),
                'price' => (float) $row['price_aed'],
                'period' => (string) $row['price_period'],
                'bedrooms' => ($row['bedrooms_list'] ?? '') === '' ? [] : explode(',', $row['bedrooms_list']),
                'kinds' => self::kinds($row['proj_inventory_type']),
            ];
        }
        return $projects;
    }

    // One vector per project: the average of its summary and overview chunk embeddings.
    public static function loadVectors($model, $dimension)
    {
        $result = db_query(
            "SELECT source_id, embedding FROM tbl_ai_chunks
             WHERE source_type = 'project' AND chunk_kind IN ('summary', 'overview')
               AND embedding IS NOT NULL AND embedding_model = ? AND embedding_dim = ?",
            [$model, (int) $dimension]
        );
        if (!$result || $result === true) {
            return [];
        }

        $sums = [];
        while ($row = mysqli_fetch_row($result)) {
            $prj_id = (int) $row[0];
            $vector = VectorStore::unpack($row[1]);
            if (!isset($sums[$prj_id])) {
                $sums[$prj_id] = $vector;
                continue;
            }
            foreach ($vector as $k => $value) {
                $sums[$prj_id][$k] += $value;
            }
        }
        foreach ($sums as $prj_id => $vector) {
            $sums[$prj_id] = VectorStore::normalize($vector);
        }
        return $sums;
    }
}
