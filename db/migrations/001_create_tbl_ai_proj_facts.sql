-- Numeric facts about each project, derived from the free-text columns of tbl_proj
-- (proj_price, proj_bedroom, proj_size, proj_handover ...).
-- tbl_proj itself is not changed: the admin panel keeps writing the text columns and
-- ai/cron/build_proj_facts.php keeps this table in step with them.

CREATE TABLE IF NOT EXISTS `tbl_ai_proj_facts` (
  `fact_id` int(11) NOT NULL AUTO_INCREMENT,
  `prj_id` int(11) NOT NULL,
  `price_aed` bigint(20) unsigned DEFAULT NULL,
  `price_period` enum('total','year','month') NOT NULL DEFAULT 'total',
  `price_on_request` tinyint(1) NOT NULL DEFAULT 0,
  `bedrooms_min` decimal(3,1) DEFAULT NULL,
  `bedrooms_max` decimal(3,1) DEFAULT NULL,
  `bedrooms_list` varchar(64) NOT NULL DEFAULT '',
  `has_studio` tinyint(1) NOT NULL DEFAULT 0,
  `size_sqft` decimal(10,2) DEFAULT NULL,
  `handover_year` smallint(6) DEFAULT NULL,
  `handover_quarter` tinyint(4) DEFAULT NULL,
  `handover_date` date DEFAULT NULL,
  `handover_completed` tinyint(1) NOT NULL DEFAULT 0,
  `parse_notes` varchar(255) NOT NULL DEFAULT '',
  `source_hash` char(32) NOT NULL,
  `tstp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`fact_id`),
  UNIQUE KEY `uniq_prj_id` (`prj_id`),
  KEY `idx_price` (`price_period`,`price_aed`),
  KEY `idx_bedrooms` (`bedrooms_min`,`bedrooms_max`),
  KEY `idx_size` (`size_sqft`),
  KEY `idx_handover` (`handover_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
