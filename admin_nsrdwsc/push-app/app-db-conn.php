<?php
// Mobile app database. Settings come from .env (APP_DB_HOST, APP_DB_USER, APP_DB_PASS, APP_DB_NAME)
// or, when not set there, from 'app_db' in config/secrets.php. secret() is loaded by ../db/config.php.
if (!function_exists('secret')) {
  require_once __DIR__ . '/../../config/load.php';
}

try {
  $app_conn = new mysqli(
    (string) secret('app_db.host', 'localhost'),
    (string) secret('app_db.user', ''),
    (string) secret('app_db.pass', ''),
    (string) secret('app_db.name', '')
  );
} catch (mysqli_sql_exception $error) {
  $app_conn = null;
}
if (!$app_conn || $app_conn->connect_error) {
  error_log('App database connection failed: ' . (isset($error) ? $error->getMessage() : ($app_conn->connect_error ?? 'unknown')));
  http_response_code(503);
  exit('App database unavailable.');
}
