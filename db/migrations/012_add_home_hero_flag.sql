-- Migration 012: Add homepage hero flag to tbl_proj
-- Lets admins pick a single project for the homepage hero banner,
-- kept separate from the (up to 6) projects shown as cards in the
-- "Hot property launches" grid on the homepage.

ALTER TABLE `tbl_proj`
  ADD COLUMN `is_home_hero` ENUM('1','2') NOT NULL DEFAULT '2' AFTER `proj_showonhomepage`;
