-- Fingerprint sign-in for the admin panel (Touch ID, Windows Hello and the like, through WebAuthn).
--
-- 1. tbl_login.biometric_allowed: a super admin switches this on for a user (Users > Edit user).
--    Until then the user cannot set up or use a fingerprint.
-- 2. tbl_login_passkeys: one row per device a user has set up on their profile page.
--    Only the public key is stored: the fingerprint itself never leaves the user's device.
--    The rows go away by themselves when their user is deleted.
ALTER TABLE tbl_login
  ADD COLUMN biometric_allowed TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = a super admin allowed fingerprint sign-in' AFTER is_active;

CREATE TABLE IF NOT EXISTS tbl_login_passkeys (
  passkey_id     INT(11)          NOT NULL AUTO_INCREMENT,
  login_id       INT(11)          NOT NULL,
  credential_id  VARCHAR(255)     NOT NULL COMMENT 'base64url, given by the device',
  public_key     TEXT             NOT NULL COMMENT 'PEM',
  sign_count     INT(10) UNSIGNED NOT NULL DEFAULT 0,
  device_name    VARCHAR(100)     NOT NULL DEFAULT '',
  created_at     DATETIME         NOT NULL,
  last_used_at   DATETIME         DEFAULT NULL,
  PRIMARY KEY (passkey_id),
  UNIQUE KEY uq_passkey_credential (credential_id),
  KEY idx_passkey_login (login_id),
  CONSTRAINT fk_passkey_login FOREIGN KEY (login_id) REFERENCES tbl_login (login_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
