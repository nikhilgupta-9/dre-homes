CREATE TABLE IF NOT EXISTS `tbl_media_story_titles` (
  `story_anchor_id` int(11) NOT NULL,
  `story_title` varchar(255) NOT NULL,
  `tstp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`story_anchor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
