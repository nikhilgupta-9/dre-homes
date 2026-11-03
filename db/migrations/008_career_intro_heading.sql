-- Careers page: the "Build Your Future With Us" part becomes editable in the admin
-- (Careers > Careers Page). Its paragraphs were already stored in career_cont_descp;
-- these two columns hold the heading and the short line under it.
-- The page shows the old text until the columns are filled, so running this changes nothing on the site by itself.
ALTER TABLE tbl_career_content
  ADD COLUMN career_intro_heading VARCHAR(255) NOT NULL DEFAULT '' AFTER career_cont_img,
  ADD COLUMN career_intro_text TEXT NULL AFTER career_intro_heading;

UPDATE tbl_career_content
SET career_intro_heading = 'Build Your Future With Us',
    career_intro_text = 'At DRE, we empower ambitious minds with opportunities to grow, lead, and make an impact in Dubai''s evolving real estate landscape.'
WHERE career_intro_heading = '';
