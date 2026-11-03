-- SEO check of the project forms in the admin (the panel that works like Yoast SEO):
-- the focus keyphrase of a project and the two scores from the last time it was saved.
-- The row goes away by itself when its project is deleted.
CREATE TABLE IF NOT EXISTS tbl_proj_seo (
  prj_id             INT(11)      NOT NULL,
  focus_keyphrase    VARCHAR(190) NOT NULL DEFAULT '',
  seo_score          TINYINT UNSIGNED DEFAULT NULL,
  readability_score  TINYINT UNSIGNED DEFAULT NULL,
  checked_at         DATETIME     NOT NULL,
  PRIMARY KEY (prj_id),
  KEY idx_proj_seo_keyphrase (focus_keyphrase),
  CONSTRAINT fk_proj_seo_proj FOREIGN KEY (prj_id) REFERENCES tbl_proj (prj_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
