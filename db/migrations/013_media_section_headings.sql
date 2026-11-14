ALTER TABLE `tbl_media_content`
  ADD COLUMN `media_story_eyebrow` varchar(255) NOT NULL DEFAULT 'Highlight stories' AFTER `media_sub_title`,
  ADD COLUMN `media_story_title` varchar(255) NOT NULL DEFAULT 'Moments worth reliving.' AFTER `media_story_eyebrow`,
  ADD COLUMN `media_gallery_eyebrow` varchar(255) NOT NULL DEFAULT 'The archive' AFTER `media_story_title`,
  ADD COLUMN `media_gallery_title` varchar(255) NOT NULL DEFAULT 'Our visual stories.' AFTER `media_gallery_eyebrow`;
