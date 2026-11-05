-- Migration 011: Create tbl_wishlist_log
-- Logs every "add to wishlist" / "remove from wishlist" the visitor's heart (♡) button on a
-- property card. The visitor's own wishlist lives in their browser cookie (dre_wishlist, 1 year) —
-- this table is only for admin tracking: which properties and which pages get wishlisted most.

CREATE TABLE IF NOT EXISTS `tbl_wishlist_log` (
  `wish_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `wish_uid` varchar(40) NOT NULL COMMENT 'anonymous visitor id, from the dre_wish_uid cookie (1 year)',
  `prj_id` int(11) DEFAULT NULL,
  `prj_type_id` int(11) DEFAULT NULL,
  `proj_slug` varchar(255) NOT NULL,
  `proj_name` varchar(255) DEFAULT NULL,
  `page_url` varchar(500) DEFAULT NULL COMMENT 'page the card was wishlisted from (HTTP referer)',
  `wish_action` enum('add','remove') NOT NULL DEFAULT 'add',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`wish_log_id`),
  KEY `idx_wish_uid` (`wish_uid`),
  KEY `idx_prj_id` (`prj_id`),
  KEY `idx_wish_action` (`wish_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
