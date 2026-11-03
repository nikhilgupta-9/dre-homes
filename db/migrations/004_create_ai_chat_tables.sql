-- Chatbot: conversations, their messages, and one usage row per AI request.
-- Chat transcripts are personal data: ai/cron/cleanup.php deletes them after the
-- retention period set in ai/config.php (see security.md, Privacy).

CREATE TABLE IF NOT EXISTS `tbl_ai_conversations` (
  `conversation_id` int(11) NOT NULL AUTO_INCREMENT,
  `token_hash` char(64) NOT NULL,
  `ip_address` varchar(45) NOT NULL DEFAULT '',
  `page_url` varchar(500) NOT NULL DEFAULT '',
  `turns` int(11) NOT NULL DEFAULT 0,
  `leads_created` tinyint(4) NOT NULL DEFAULT 0,
  `started_at` datetime NOT NULL,
  `last_message_at` datetime NOT NULL,
  `tstp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`conversation_id`),
  UNIQUE KEY `uniq_token_hash` (`token_hash`),
  KEY `idx_last_message_at` (`last_message_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tbl_ai_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `role` enum('user','model','tool') NOT NULL,
  `content_text` mediumtext NOT NULL,
  `content_json` mediumtext DEFAULT NULL,
  `tstp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`message_id`),
  KEY `idx_conversation` (`conversation_id`,`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tbl_ai_usage` (
  `usage_id` int(11) NOT NULL AUTO_INCREMENT,
  `feature` varchar(32) NOT NULL,
  `conversation_id` int(11) NOT NULL DEFAULT 0,
  `model` varchar(64) NOT NULL DEFAULT '',
  `input_tokens` int(11) NOT NULL DEFAULT 0,
  `output_tokens` int(11) NOT NULL DEFAULT 0,
  `cost_usd` decimal(10,6) NOT NULL DEFAULT 0.000000,
  `latency_ms` int(11) NOT NULL DEFAULT 0,
  `tools_called` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(24) NOT NULL DEFAULT 'ok',
  `ip_address` varchar(45) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `tstp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`usage_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_ip_created` (`ip_address`,`created_at`),
  KEY `idx_conversation` (`conversation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
