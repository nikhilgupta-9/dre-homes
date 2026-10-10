<?php
// Storage and search for tbl_ai_chunks. Vectors are stored as packed 32-bit floats,
// scaled to length 1, so the similarity of two vectors is their dot product.
// Search reads every vector and compares in PHP: fine for some thousands of chunks.

class VectorStore
{
    public static function normalize(array $vector)
    {
        $sum = 0.0;
        foreach ($vector as $value) {
            $sum += $value * $value;
        }
        if ($sum <= 0.0) {
            return $vector;
        }
        $length = sqrt($sum);
        foreach ($vector as $i => $value) {
            $vector[$i] = $value / $length;
        }
        return $vector;
    }

    public static function pack(array $vector)
    {
        return pack('g*', ...self::normalize($vector));
    }

    public static function unpack($blob)
    {
        return array_values(unpack('g*', $blob));
    }

    public static function dot(array $a, array $b)
    {
        $sum = 0.0;
        foreach ($a as $i => $value) {
            $sum += $value * $b[$i];
        }
        return $sum;
    }

    public function saveEmbedding($chunk_id, array $vector, $model)
    {
        return (bool) db_query(
            "UPDATE tbl_ai_chunks SET embedding = ?, embedding_model = ?, embedding_dim = ? WHERE chunk_id = ?",
            [self::pack($vector), $model, count($vector), (int) $chunk_id]
        );
    }

    // Best matches for a query vector: [chunk_id => similarity], highest first.
    // $source_types limits the search, for example ['blog', 'news', 'page'].
    public function searchByVector(array $query_vector, $model, $limit = 8, array $source_types = [])
    {
        $query_vector = self::normalize($query_vector);
        $dimension = count($query_vector);

        $sql = "SELECT chunk_id, embedding FROM tbl_ai_chunks WHERE embedding IS NOT NULL AND embedding_model = ? AND embedding_dim = ?";
        $params = [$model, $dimension];
        $sql .= $this->sourceTypeFilter($source_types, $params);

        $result = db_query($sql, $params);
        if (!$result) {
            return [];
        }

        $scores = [];
        while ($row = mysqli_fetch_row($result)) {
            $scores[(int) $row[0]] = self::dot($query_vector, self::unpack($row[1]));
        }
        arsort($scores);
        return array_slice($scores, 0, $limit, true);
    }

    // Keyword matches through the FULLTEXT index: [chunk_id => score], highest first.
    // Catches exact project names and spellings that embeddings blur.
    public function searchByKeyword($query, $limit = 8, array $source_types = [])
    {
        $query = trim((string) $query);
        if ($query === '') {
            return [];
        }

        $sql = "SELECT chunk_id, MATCH(title, chunk_text) AGAINST (?) AS score FROM tbl_ai_chunks WHERE MATCH(title, chunk_text) AGAINST (?)";
        $params = [$query, $query];
        $sql .= $this->sourceTypeFilter($source_types, $params);
        $sql .= " ORDER BY score DESC LIMIT ?";
        $params[] = (int) $limit;

        $scores = [];
        foreach (db_all($sql, $params) as $row) {
            $scores[(int) $row['chunk_id']] = (float) $row['score'];
        }
        return $scores;
    }

    // Joins the two rankings with reciprocal rank fusion and returns the chunk rows.
    // Pass null as $query_vector to search by keyword only (no embeddings key, or provider down).
    public function search($query, $query_vector, $model, $limit = 8, array $source_types = [])
    {
        $rankings = [];
        if ($query_vector !== null) {
            $rankings[] = array_keys($this->searchByVector($query_vector, $model, $limit * 3, $source_types));
        }
        $rankings[] = array_keys($this->searchByKeyword($query, $limit * 3, $source_types));

        // At most two chunks from one record, so a single long page cannot fill the answer.
        $chunks = [];
        $per_record = [];
        foreach ($this->fetchChunks(array_keys(self::fuse($rankings))) as $chunk) {
            $record = $chunk['source_type'] . '|' . $chunk['source_id'];
            $per_record[$record] = ($per_record[$record] ?? 0) + 1;
            if ($per_record[$record] > 2) {
                continue;
            }
            $chunks[] = $chunk;
            if (count($chunks) >= $limit) {
                break;
            }
        }
        return $chunks;
    }

    // Reciprocal rank fusion: [id => score], highest first.
    public static function fuse(array $rankings, $k = 60)
    {
        $scores = [];
        foreach ($rankings as $ranking) {
            foreach (array_values($ranking) as $position => $id) {
                $scores[$id] = ($scores[$id] ?? 0.0) + 1.0 / ($k + $position + 1);
            }
        }
        arsort($scores);
        return $scores;
    }

    // Chunk rows in the order of $ids, without the embedding.
    public function fetchChunks(array $ids)
    {
        if (empty($ids)) {
            return [];
        }
        $ids = array_map('intval', array_values($ids));
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $rows = db_all("SELECT chunk_id, source_type, source_id, chunk_kind, title, url_path, chunk_text FROM tbl_ai_chunks WHERE chunk_id IN ($marks)", $ids);

        $by_id = [];
        foreach ($rows as $row) {
            $by_id[(int) $row['chunk_id']] = $row;
        }
        $ordered = [];
        foreach ($ids as $id) {
            if (isset($by_id[$id])) {
                $ordered[] = $by_id[$id];
            }
        }
        return $ordered;
    }

    private function sourceTypeFilter(array $source_types, array &$params)
    {
        if (empty($source_types)) {
            return '';
        }
        foreach ($source_types as $source_type) {
            $params[] = (string) $source_type;
        }
        return " AND source_type IN (" . implode(',', array_fill(0, count($source_types), '?')) . ")";
    }
}
