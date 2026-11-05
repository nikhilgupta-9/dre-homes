-- Migration 010: Create tbl_theme_settings
-- Provides admin control over website typography (fonts, sizes), colors, buttons, shapes, and mode.

CREATE TABLE IF NOT EXISTS `tbl_theme_settings` (
  `theme_id` int(11) NOT NULL AUTO_INCREMENT,
  `theme_name` varchar(100) NOT NULL DEFAULT 'DRE Luxury Taupe',
  `body_font_family` varchar(100) NOT NULL DEFAULT 'Urbanist',
  `heading_font_family` varchar(100) NOT NULL DEFAULT 'Urbanist',
  `custom_font_url` varchar(500) DEFAULT '',
  `base_font_size` varchar(20) NOT NULL DEFAULT '16px',
  `body_line_height` varchar(20) NOT NULL DEFAULT '1.6',
  `h1_font_size` varchar(20) NOT NULL DEFAULT '56px',
  `h2_font_size` varchar(20) NOT NULL DEFAULT '40px',
  `h3_font_size` varchar(20) NOT NULL DEFAULT '24px',
  `primary_color` varchar(50) NOT NULL DEFAULT '#5f574c',
  `accent_color` varchar(50) NOT NULL DEFAULT '#E8C9A8',
  `accent_color_2` varchar(50) NOT NULL DEFAULT '#F5E9DC',
  `accent_color_3` varchar(50) NOT NULL DEFAULT '#DCC5A9',
  `button_bg_color` varchar(50) NOT NULL DEFAULT '#ab7752',
  `button_text_color` varchar(50) NOT NULL DEFAULT '#ffffff',
  `button_border_radius` varchar(20) NOT NULL DEFAULT '999px',
  `night_bg_color` varchar(50) NOT NULL DEFAULT '#14110e',
  `night_text_color` varchar(50) NOT NULL DEFAULT '#f4efe9',
  `night_accent_color` varchar(50) NOT NULL DEFAULT '#bfab95',
  `card_border_radius` varchar(20) NOT NULL DEFAULT '26px',
  `default_mode` enum('night','day') NOT NULL DEFAULT 'night',
  `custom_css` mediumtext DEFAULT NULL,
  `is_active` enum('1','2') NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `tstp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tbl_theme_settings` (
  `theme_id`, `theme_name`, `body_font_family`, `heading_font_family`, `custom_font_url`,
  `base_font_size`, `body_line_height`, `h1_font_size`, `h2_font_size`, `h3_font_size`,
  `primary_color`, `accent_color`, `accent_color_2`, `accent_color_3`,
  `button_bg_color`, `button_text_color`, `button_border_radius`,
  `night_bg_color`, `night_text_color`, `night_accent_color`,
  `card_border_radius`, `default_mode`, `custom_css`, `is_active`
) VALUES (
  1, 'DRE Luxury Taupe', 'Urbanist', 'Urbanist', '',
  '16px', '1.6', '56px', '40px', '24px',
  '#5f574c', '#E8C9A8', '#F5E9DC', '#DCC5A9',
  '#ab7752', '#ffffff', '999px',
  '#14110e', '#f4efe9', '#bfab95',
  '26px', 'night', '', '1'
) ON DUPLICATE KEY UPDATE `is_active` = '1';
