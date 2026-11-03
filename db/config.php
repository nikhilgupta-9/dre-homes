<?php
ob_start();
session_start();
date_default_timezone_set('Asia/Kolkata');

// Settings come from .env (local) or the secrets file (live server) through secret(), see config/load.php:
//   BASE_URL       site address with a trailing slash        (.env: BASE_URL)
//   BASE_URL_IMG   admin media folder                         (.env: BASE_URL_IMG, defaults to BASE_URL + admin media)
//   database       host / user / password / name              (.env: DB_HOST, DB_USER, DB_PASS, DB_NAME)
require_once __DIR__ . '/../config/load.php';

define('BASE_URL', rtrim((string) secret('base_url', 'http://localhost/dre-homes-v3/'), '/') . '/');
define('BASE_URL_IMG', rtrim((string) (secret('base_url_img') ?: BASE_URL . 'admin_nsrdwsc/assets/media/'), '/') . '/');

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_connect(
	(string) secret('db.host', 'localhost'),
	(string) secret('db.user', 'root'),
	(string) secret('db.pass', ''),
	(string) secret('db.name', 'dre_website_db_v3')
);
if (!$conn) {
	// Details go to the PHP error log only; visitors never see database names or users
	error_log('Database connection failed: ' . mysqli_connect_error());
	http_response_code(503);
	exit('Service temporarily unavailable. Please try again shortly.');
}

mysqli_set_charset($conn, 'utf8mb4');
$date = date('Y-m-d H:i:s');
include('functions.php');
