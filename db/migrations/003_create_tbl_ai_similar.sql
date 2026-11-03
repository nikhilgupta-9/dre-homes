-- "More Like This" on the property pages: the most similar projects for each project,
-- worked out ahead of time by ai/cron/build_similar.php. Pages only read this table,
-- so showing the block costs no AI call.

CREATE TABLE IF NOT EXISTS `tbl_ai_similar` (
  `similar_id` int(11) NOT NULL AUTO_INCREMENT,
  `prj_id` int(11) NOT NULL,
  `similar_prj_id` int(11) NOT NULL,
  `rank_no` tinyint(4) NOT NULL,
  `score` decimal(5,4) NOT NULL,
  `tstp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`similar_id`),
  UNIQUE KEY `uniq_prj_rank` (`prj_id`,`rank_no`),
  KEY `idx_similar_prj_id` (`similar_prj_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
