-- Remembers which projects were imported from GenieMap (geniemap.net), so the admin can
-- mark them with a badge and never import the same one twice.
-- The row goes away by itself when its project is deleted.
CREATE TABLE IF NOT EXISTS tbl_geniemap_import (
  prj_id       INT(11)      NOT NULL,
  genie_id     INT(11)      NOT NULL,
  genie_name   VARCHAR(255) NOT NULL,
  imported_by  VARCHAR(255) NOT NULL,
  imported_at  DATETIME     NOT NULL,
  raw_data     LONGTEXT     NULL,
  PRIMARY KEY (prj_id),
  UNIQUE KEY uq_geniemap_genie_id (genie_id),
  CONSTRAINT fk_geniemap_import_proj FOREIGN KEY (prj_id) REFERENCES tbl_proj (prj_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

