<?php
// Builds the search index (tbl_ai_chunks) from public content.
// Every database read goes through readAllowed(), which refuses any table or column
// that is not in the allowlist in ai/config.php.

require_once __DIR__ . '/TextTools.php';

class Indexer
{
    // When the provider's daily allowance is smaller than the index, the most useful chunks go first:
    // guides and services, areas, developers, one summary per project, articles, then the long project texts.
    const EMBED_ORDER = "FIELD(source_type, 'page', 'community', 'developer') = 0, chunk_kind <> 'summary', source_type = 'project'";

    private $config;
    private $base_url;
    private $warnings = [];
    // Sources that could not be read this run: their old chunks are kept, not deleted.
    private $protected_sources = [];
    private $embedded_count = 0;

    public function __construct(array $config, $base_url)
    {
        $this->config = $config;
        $this->base_url = rtrim((string) $base_url, '/') . '/';
    }

    public function warnings()
    {
        return $this->warnings;
    }

    // The only way the indexer reads the database. $where is written in this file, never by a visitor.
    public function readAllowed($table, array $columns, $where = '', array $params = [])
    {
        $allowlist = $this->config['index_allowlist'];
        if (!isset($allowlist[$table])) {
            throw new RuntimeException("Indexer refused to read table '$table': it is not in the allowlist.");
        }
        foreach ($columns as $column) {
            if (!in_array($column, $allowlist[$table], true)) {
                throw new RuntimeException("Indexer refused to read column '$table.$column': it is not in the allowlist.");
            }
        }

        $sql = "SELECT " . implode(', ', $columns) . " FROM $table" . ($where !== '' ? " WHERE $where" : '');
        return db_all($sql, $params);
    }

    // Every document that belongs in the index:
    // [source_type, source_id, title, url_path, chunks => [[kind, text], ...]]
    public function buildDocuments()
    {
        return array_merge(
            $this->buildProjects(),
            $this->buildCommunities(),
            $this->buildDevelopers(),
            $this->buildArticles('blog', 'tbl_blog', 'blog', 'our-blogs/', 'Blog'),
            $this->buildArticles('news', 'tbl_news', 'news', 'company-news/', 'Company news'),
            $this->buildStaticPages()
        );
    }

    // Writes new and changed chunks, removes chunks that no longer exist.
    // A changed chunk loses its embedding and is picked up by embedPending().
    public function sync(array $documents, $dry_run = false)
    {
        $counts = ['sources' => count($documents), 'total' => 0, 'written' => 0, 'unchanged' => 0, 'deleted' => 0, 'failed' => 0];

        $existing = [];
        $result = db_query("SELECT chunk_id, source_type, source_id, chunk_no, content_hash FROM tbl_ai_chunks");
        if (!$result) {
            throw new RuntimeException('Could not read tbl_ai_chunks. Has db/migrations/002_create_tbl_ai_chunks.sql been applied?');
        }
        while ($row = mysqli_fetch_assoc($result)) {
            $existing[$row['source_type'] . '|' . $row['source_id'] . '|' . $row['chunk_no']] = $row;
        }

        $seen = [];
        foreach ($documents as $document) {
            foreach ($document['chunks'] as $chunk_no => $chunk) {
                $counts['total']++;
                $key = $document['source_type'] . '|' . $document['source_id'] . '|' . $chunk_no;
                $seen[$key] = true;
                $hash = md5($chunk['kind'] . "\x1f" . $document['title'] . "\x1f" . $document['url_path'] . "\x1f" . $chunk['text']);

                if (isset($existing[$key]) && $existing[$key]['content_hash'] === $hash) {
                    $counts['unchanged']++;
                    continue;
                }
                if ($dry_run) {
                    $counts['written']++;
                    continue;
                }

                $saved = db_query(
                    "INSERT INTO tbl_ai_chunks (source_type, source_id, chunk_no, chunk_kind, title, url_path, chunk_text, token_estimate, content_hash)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE chunk_kind = VALUES(chunk_kind), title = VALUES(title), url_path = VALUES(url_path),
                        chunk_text = VALUES(chunk_text), token_estimate = VALUES(token_estimate), content_hash = VALUES(content_hash),
                        embedding = NULL, embedding_model = '', embedding_dim = 0",
                    [
                        $document['source_type'], (string) $document['source_id'], (int) $chunk_no, $chunk['kind'],
                        mb_substr($document['title'], 0, 255), $document['url_path'], $chunk['text'],
                        TextTools::estimateTokens($chunk['text']), $hash,
                    ]
                );
                $counts[$saved ? 'written' : 'failed']++;
            }
        }

        foreach ($existing as $key => $row) {
            if (isset($seen[$key]) || isset($this->protected_sources[$row['source_type'] . '|' . $row['source_id']])) {
                continue;
            }
            if ($dry_run || db_query("DELETE FROM tbl_ai_chunks WHERE chunk_id = ?", [(int) $row['chunk_id']])) {
                $counts['deleted']++;
            } else {
                $counts['failed']++;
            }
        }

        return $counts;
    }

    // Embeds chunks that have no embedding yet, or one from another model or size.
    // Stops after $max_chunks (0 = no limit). Work already saved is kept if a later batch
    // fails, and embeddedCount() tells how far it got; the next run carries on from there.
    public function embedPending($embedder, $vector_store, $max_chunks = 0)
    {
        $batch_size = (int) $this->config['embedding_batch_size'];
        $per_minute = (int) ($this->config['embedding_texts_per_minute'] ?? 0);
        $this->embedded_count = 0;

        while ($max_chunks === 0 || $this->embedded_count < $max_chunks) {
            $limit = $max_chunks === 0 ? $batch_size : min($batch_size, $max_chunks - $this->embedded_count);
            $rows = db_all(
                "SELECT chunk_id, chunk_text FROM tbl_ai_chunks
                 WHERE embedding IS NULL OR embedding_model <> ? OR embedding_dim <> ?
                 ORDER BY " . self::EMBED_ORDER . ", chunk_id LIMIT ?",
                [$embedder->model(), $embedder->dimension(), $limit]
            );
            if (empty($rows)) {
                break;
            }

            $batch_started = microtime(true);
            $vectors = $embedder->embedDocuments(array_column($rows, 'chunk_text'));
            foreach ($rows as $i => $row) {
                if (!$vector_store->saveEmbedding($row['chunk_id'], $vectors[$i], $embedder->model())) {
                    throw new RuntimeException('Could not save the embedding of chunk ' . $row['chunk_id']);
                }
                $this->embedded_count++;
            }

            // Keep under the provider's per-minute limit.
            if ($per_minute > 0 && count($rows) === $limit) {
                $wait = count($rows) * 60 / $per_minute - (microtime(true) - $batch_started);
                if ($wait > 0) {
                    usleep((int) ($wait * 1000000));
                }
            }
        }
        return $this->embedded_count;
    }

    public function embeddedCount()
    {
        return $this->embedded_count;
    }

    public function pendingCount($embedder)
    {
        $row = db_row(
            "SELECT COUNT(*) AS pending FROM tbl_ai_chunks WHERE embedding IS NULL OR embedding_model <> ? OR embedding_dim <> ?",
            [$embedder->model(), $embedder->dimension()]
        );
        return $row ? (int) $row['pending'] : 0;
    }

    // ---------------------------------------------------------------
    // Projects
    // ---------------------------------------------------------------
    private function buildProjects()
    {
        $prefixes = $this->config['project_url_prefix'];

        $type_names = [];
        foreach ($this->readAllowed('tbl_proj_type', ['prj_type_id', 'proj_type_name'], "is_active = '1'") as $row) {
            $type_names[(int) $row['prj_type_id']] = trim($row['proj_type_name']);
        }
        $developers = [];
        foreach ($this->readAllowed('tbl_developer', ['dev_id', 'dev_heading'], "is_active = '1'") as $row) {
            $developers[(int) $row['dev_id']] = trim($row['dev_heading']);
        }
        $communities = [];
        foreach ($this->readAllowed('tbl_community', ['comm_id', 'comm_heading'], "is_active = '1'") as $row) {
            $communities[(int) $row['comm_id']] = trim($row['comm_heading']);
        }
        $amenities = [];
        foreach ($this->readAllowed('tbl_proj_featfac', ['featfac_id', 'featfac_name'], "is_active = '1'") as $row) {
            $amenities[(int) $row['featfac_id']] = trim($row['featfac_name']);
        }
        $facts = [];
        foreach ($this->readAllowed('tbl_ai_proj_facts', ['prj_id', 'price_aed', 'price_period', 'price_on_request', 'bedrooms_list', 'size_sqft', 'handover_year', 'handover_quarter', 'handover_completed']) as $row) {
            $facts[(int) $row['prj_id']] = $row;
        }
        $payplans = [];
        foreach ($this->readAllowed('tbl_proj_payplan', ['proj_id', 'payplan_heading', 'payplan_percentage'], "is_active = '1' ORDER BY proj_payplan_id") as $row) {
            $payplans[(int) $row['proj_id']][] = trim($row['payplan_heading']) . ' ' . trim($row['payplan_percentage']);
        }
        $faqs = [];
        foreach ($this->readAllowed('tbl_proj_faq', ['proj_id', 'faq_heading', 'faq_descp'], "is_active = '1' ORDER BY proj_faq_id") as $row) {
            $faqs[(int) $row['proj_id']][] = $row;
        }

        $projects = $this->readAllowed('tbl_proj', [
            'prj_id', 'prj_type_id', 'prj_dev_id', 'prj_comm_id', 'proj_name', 'proj_sub_heading', 'proj_location',
            'proj_inventory_type', 'proj_pplan_cont', 'proj_overview', 'proj_fea_fac_cont', 'proj_fea_fac_opt',
            'proj_loc_cont', 'proj_masterplan', 'proj_slug',
            'proj_show_handover', 'proj_show_bedroom', 'proj_show_payment_plan', 'proj_show_size',
        ], "is_active = '1' ORDER BY prj_id");

        $documents = [];
        foreach ($projects as $project) {
            $prj_id = (int) $project['prj_id'];
            $type_id = (int) $project['prj_type_id'];
            $name = trim((string) $project['proj_name']);
            $slug = trim((string) $project['proj_slug']);
            if (!isset($prefixes[$type_id]) || $name === '' || $slug === '') {
                continue;
            }

            $fact = $facts[$prj_id] ?? null;
            $location = trim((string) $project['proj_location']);
            $type_name = $type_names[$type_id] ?? '';

            $lines = [];
            $lines[] = $name . ($type_name !== '' ? " - $type_name property" : '') . ($location !== '' ? " in $location" : '') . '.';
            if (trim((string) $project['proj_sub_heading']) !== '') {
                $lines[] = TextTools::htmlToText($project['proj_sub_heading']);
            }
            if (isset($developers[(int) $project['prj_dev_id']])) {
                $lines[] = 'Developer: ' . $developers[(int) $project['prj_dev_id']] . '.';
            }
            if (isset($communities[(int) $project['prj_comm_id']])) {
                $lines[] = 'Community: ' . $communities[(int) $project['prj_comm_id']] . '.';
            }
            if (trim((string) $project['proj_inventory_type']) !== '') {
                $lines[] = 'Property type: ' . trim($project['proj_inventory_type']) . '.';
            }
            if ($fact) {
                if ($project['proj_show_bedroom'] === '1' && $fact['bedrooms_list'] !== '') {
                    $labels = array_map(function ($bedroom) {
                        return $bedroom === '0' ? 'Studio' : $bedroom;
                    }, explode(',', $fact['bedrooms_list']));
                    $lines[] = 'Bedrooms: ' . implode(', ', $labels) . '.';
                }
                if ($fact['price_aed'] !== null) {
                    $period = $fact['price_period'] === 'year' ? ' per year' : ($fact['price_period'] === 'month' ? ' per month' : '');
                    $lines[] = 'Price: from AED ' . number_format((float) $fact['price_aed']) . $period . '.';
                } else {
                    $lines[] = 'Price: on request.';
                }
                if ($project['proj_show_size'] === '1' && $fact['size_sqft'] !== null) {
                    $lines[] = 'Size: ' . number_format((float) $fact['size_sqft']) . ' sq ft.';
                }
                if ($project['proj_show_handover'] === '1') {
                    if ($fact['handover_completed']) {
                        $lines[] = 'Handover: completed.';
                    } elseif ($fact['handover_year'] !== null) {
                        $lines[] = 'Handover: ' . ($fact['handover_quarter'] !== null ? 'Q' . $fact['handover_quarter'] . ' ' : '') . $fact['handover_year'] . '.';
                    }
                }
            }
            if ($project['proj_show_payment_plan'] === '1') {
                $plan = trim((string) $project['proj_pplan_cont']);
                $steps = isset($payplans[$prj_id]) ? implode(', ', $payplans[$prj_id]) : '';
                if ($plan !== '' || $steps !== '') {
                    $lines[] = 'Payment plan: ' . trim($plan . ($plan !== '' && $steps !== '' ? ' - ' : '') . $steps) . '.';
                }
            }
            $amenity_names = [];
            foreach (explode(',', (string) $project['proj_fea_fac_opt']) as $amenity_id) {
                if (isset($amenities[(int) $amenity_id])) {
                    $amenity_names[] = $amenities[(int) $amenity_id];
                }
            }
            if ($amenity_names) {
                $lines[] = 'Amenities: ' . implode(', ', array_unique($amenity_names)) . '.';
            }

            $chunks = [['kind' => 'summary', 'text' => implode("\n", $lines)]];

            $sections = [
                'overview' => ['Overview', $project['proj_overview']],
                'features' => ['Features and facilities', $project['proj_fea_fac_cont']],
                'location' => ['Location', $project['proj_loc_cont']],
                'masterplan' => ['Master plan', $project['proj_masterplan']],
            ];
            foreach ($sections as $kind => $section) {
                $chunks = array_merge($chunks, $this->textChunks($kind, "$name - {$section[0]}", $section[1]));
            }
            foreach ($faqs[$prj_id] ?? [] as $faq) {
                $question = TextTools::htmlToText($faq['faq_heading']);
                $answer = TextTools::htmlToText($faq['faq_descp']);
                if ($question !== '' && $answer !== '') {
                    $chunks = array_merge($chunks, $this->textChunks('faq', "$name - Question: $question", $answer));
                }
            }

            $documents[] = [
                'source_type' => 'project',
                'source_id' => (string) $prj_id,
                'title' => $name,
                'url_path' => $prefixes[$type_id] . $slug,
                'chunks' => $chunks,
            ];
        }
        return $documents;
    }

    // ---------------------------------------------------------------
    // Communities
    // ---------------------------------------------------------------
    private function buildCommunities()
    {
        $nearby = [];
        foreach ($this->readAllowed('tbl_community_loc', ['comm_id', 'comm_loc_heading', 'comm_loc_description'], "is_active = '1' ORDER BY comm_loc_id") as $row) {
            $text = trim(TextTools::htmlToText($row['comm_loc_heading']) . ': ' . TextTools::htmlToText($row['comm_loc_description']), ': ');
            if ($text !== '') {
                $nearby[(int) $row['comm_id']][] = $text;
            }
        }

        $documents = [];
        $rows = $this->readAllowed('tbl_community', ['comm_id', 'comm_heading', 'comm_location', 'comm_long_descp', 'comm_loc_main_heading', 'comm_loc_main_descp', 'comm_slug'], "is_active = '1' ORDER BY comm_id");
        foreach ($rows as $row) {
            $name = trim((string) $row['comm_heading']);
            $slug = trim((string) $row['comm_slug']);
            if ($name === '' || $slug === '') {
                continue;
            }
            $place = trim(TextTools::htmlToText($row['comm_location']));
            $label = $name . ' (community' . ($place !== '' ? " in $place" : '') . ')';

            $chunks = $this->textChunks('about', $label, $row['comm_long_descp']);
            $chunks = array_merge($chunks, $this->textChunks('location', "$label - " . (trim((string) $row['comm_loc_main_heading']) ?: 'Location'), $row['comm_loc_main_descp']));
            if (isset($nearby[(int) $row['comm_id']])) {
                $chunks = array_merge($chunks, $this->textChunks('nearby', "$label - Nearby", implode("\n\n", $nearby[(int) $row['comm_id']])));
            }
            if (empty($chunks)) {
                $chunks = [['kind' => 'about', 'text' => $label . '.']];
            }

            $documents[] = ['source_type' => 'community', 'source_id' => (string) $row['comm_id'], 'title' => $name, 'url_path' => 'community/' . $slug, 'chunks' => $chunks];
        }
        return $documents;
    }

    // ---------------------------------------------------------------
    // Developers
    // ---------------------------------------------------------------
    private function buildDevelopers()
    {
        $documents = [];
        $rows = $this->readAllowed('tbl_developer', ['dev_id', 'dev_heading', 'dev_short_descp', 'dev_long_descp', 'choose_us_desc', 'dev_slug'], "is_active = '1' ORDER BY dev_id");
        foreach ($rows as $row) {
            $name = trim((string) $row['dev_heading']);
            $slug = trim((string) $row['dev_slug']);
            if ($name === '' || $slug === '') {
                continue;
            }
            $label = "$name (property developer)";
            $short = TextTools::htmlToText($row['dev_short_descp']);
            $long = TextTools::htmlToText($row['dev_long_descp']);
            $about = ($short !== '' && strpos($long, $short) === false) ? trim($short . "\n\n" . $long) : $long;

            $chunks = $this->textChunks('about', $label, $about);
            $chunks = array_merge($chunks, $this->textChunks('why', "$label - Why choose", $row['choose_us_desc']));
            if (empty($chunks)) {
                $chunks = [['kind' => 'about', 'text' => $label . '.']];
            }

            $documents[] = ['source_type' => 'developer', 'source_id' => (string) $row['dev_id'], 'title' => $name, 'url_path' => 'developer/' . $slug, 'chunks' => $chunks];
        }
        return $documents;
    }

    // ---------------------------------------------------------------
    // Blogs and company news (same shape, different column prefix)
    // ---------------------------------------------------------------
    private function buildArticles($source_type, $table, $prefix, $url_prefix, $label)
    {
        $documents = [];
        $columns = ["{$prefix}_id", "{$prefix}_heading", "{$prefix}_short_descp", "{$prefix}_long_descp", "{$prefix}_slug", "{$prefix}_posted_on"];
        foreach ($this->readAllowed($table, $columns, "is_active = '1' ORDER BY {$prefix}_id") as $row) {
            $heading = TextTools::htmlToText($row["{$prefix}_heading"]);
            $slug = trim((string) $row["{$prefix}_slug"]);
            if ($heading === '' || $slug === '') {
                continue;
            }
            $short = TextTools::htmlToText($row["{$prefix}_short_descp"]);
            $long = TextTools::htmlToText($row["{$prefix}_long_descp"]);
            $body = ($short !== '' && strpos($long, $short) === false) ? trim($short . "\n\n" . $long) : $long;
            $posted = (string) $row["{$prefix}_posted_on"];
            $dated = ($posted !== '' && $posted !== '0000-00-00') ? " (published $posted)" : '';

            $chunks = $this->textChunks('article', "$label: $heading$dated", $body);
            if (empty($chunks)) {
                continue;
            }
            $documents[] = ['source_type' => $source_type, 'source_id' => (string) $row["{$prefix}_id"], 'title' => $heading, 'url_path' => $url_prefix . $slug, 'chunks' => $chunks];
        }
        return $documents;
    }

    // ---------------------------------------------------------------
    // Static pages, read from our own site
    // ---------------------------------------------------------------
    private function buildStaticPages()
    {
        $documents = [];
        foreach ($this->config['static_pages'] as $path => $title) {
            $html = $this->fetchPage($path);
            $text = $html === null ? '' : self::extractPageText($html);
            if ($text === '') {
                $this->warnings[] = "Static page '$path' could not be read; its existing chunks are kept.";
                $this->protected_sources['page|' . $path] = true;
                continue;
            }
            $documents[] = ['source_type' => 'page', 'source_id' => $path, 'title' => $title, 'url_path' => $path, 'chunks' => $this->textChunks('page', $title, $text)];
        }
        return $documents;
    }

    private function fetchPage($path)
    {
        $curl = curl_init($this->base_url . $path);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => 'DREHomesIndexer/1.0',
        ]);
        $html = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return ($html !== false && $status === 200) ? $html : null;
    }

    // The page body sits between the theme's "content" markers; header, footer and popups are outside.
    public static function extractPageText($html)
    {
        if (!preg_match('/<!--\s*content\s*-->(.*)<!--\s*content\s+end\s*-->/is', (string) $html, $match)) {
            return '';
        }
        return TextTools::htmlToText($match[1]);
    }

    // Splits one section into chunks. Each chunk starts with its heading so it makes sense alone.
    private function textChunks($kind, $heading, $html)
    {
        $text = TextTools::htmlToText($html);
        if ($text === '') {
            return [];
        }
        $max_chars = max(400, (int) $this->config['chunk_max_chars'] - mb_strlen($heading) - 1);
        $chunks = [];
        foreach (TextTools::chunk($text, $max_chars, (int) $this->config['chunk_min_chars']) as $piece) {
            $chunks[] = ['kind' => $kind, 'text' => $heading . "\n" . $piece];
        }
        return $chunks;
    }
}
