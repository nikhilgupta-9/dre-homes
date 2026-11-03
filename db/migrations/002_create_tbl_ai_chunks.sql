-- Search index for the AI features: one row per piece of public text, with its embedding.
-- Filled by ai/cron/reindex.php from an allowlist of public tables (see ai/config.php).
-- No lead, enquiry, resume, login or log data is ever stored here.

CREATE TABLE IF NOT EXISTS `tbl_ai_chunks` (
  `chunk_id` int(11) NOT NULL AUTO_INCREMENT,
  `source_type` varchar(32) NOT NULL,
  `source_id` varchar(64) NOT NULL,
  `chunk_no` smallint(6) NOT NULL,
  `chunk_kind` varchar(32) NOT NULL,
  `title` varchar(255) NOT NULL,
  `url_path` varchar(500) NOT NULL DEFAULT '',
  `chunk_text` mediumtext NOT NULL,
  `token_estimate` int(11) NOT NULL DEFAULT 0,
  `content_hash` char(32) NOT NULL,
  `embedding` mediumblob DEFAULT NULL,
  `embedding_model` varchar(64) NOT NULL DEFAULT '',
  `embedding_dim` smallint(6) NOT NULL DEFAULT 0,
  `tstp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`chunk_id`),
  UNIQUE KEY `uniq_source_chunk` (`source_type`,`source_id`,`chunk_no`),
  KEY `idx_embedding_model` (`embedding_model`),
  FULLTEXT KEY `ft_title_text` (`title`,`chunk_text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tbl_ai_index_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `started_at` datetime NOT NULL,
  `finished_at` datetime DEFAULT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'running',
  `sources_read` int(11) NOT NULL DEFAULT 0,
  `chunks_total` int(11) NOT NULL DEFAULT 0,
  `chunks_written` int(11) NOT NULL DEFAULT 0,
  `chunks_deleted` int(11) NOT NULL DEFAULT 0,
  `chunks_embedded` int(11) NOT NULL DEFAULT 0,
  `embedding_tokens` int(11) NOT NULL DEFAULT 0,
  `message` varchar(500) NOT NULL DEFAULT '',
  `tstp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
