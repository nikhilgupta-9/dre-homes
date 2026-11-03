<?php
ob_start();
//error_reporting(0);
session_start();
date_default_timezone_set('Asia/Kolkata');

// Settings come from .env (local) or the secrets file (live server) through secret(), the same as the website
// (see config/load.php in the project root):
//   BASE_URL_HOME  website address              (.env: BASE_URL)
//   BASE_URL       admin address                (.env: ADMIN_URL, defaults to BASE_URL_HOME + admin_nsrdwsc/)
//   database       host / user / password / name (.env: DB_HOST, DB_USER, DB_PASS, DB_NAME)
require_once __DIR__ . '/../../config/load.php';

define('BASE_URL_HOME', rtrim((string) secret('base_url', 'http://localhost/dre-homes-new-ui/'), '/') . '/');
define('BASE_URL', rtrim((string) (secret('admin_url') ?: BASE_URL_HOME . 'admin_nsrdwsc/'), '/') . '/');

// Admin pages catch mysqli_sql_exception, so MySQL errors stay exceptions (the PHP 8.1+ default)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
	$conn = mysqli_connect(
		(string) secret('db.host', 'localhost'),
		(string) secret('db.user', ''),
		(string) secret('db.pass', ''),
		(string) secret('db.name', '')
	);
} catch (mysqli_sql_exception $error) {
	// Details go to the PHP error log only
	error_log('Admin database connection failed: ' . $error->getMessage());
	http_response_code(503);
	exit('Service temporarily unavailable. Please try again shortly.');
}
mysqli_set_charset($conn, 'utf8mb4');
$date = date('Y-m-d H:i:s');
include('functions.php');
