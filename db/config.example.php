<?php
// Copy this file to db/config.php and fill in the values for your environment.
// db/config.php is git-ignored: never commit real credentials.
ob_start();
session_start();
date_default_timezone_set('Asia/Kolkata');

define('BASE_URL','http://localhost/dre_homes_new/');
define('BASE_URL_IMG','http://localhost/dre_homes_new/admin_nsrdwsc/assets/media/');

$conn=mysqli_connect('localhost','DB_USER','DB_PASSWORD','DB_NAME');
if(!$conn)
{
	die('Database Connection Failed');
}
mysqli_set_charset($conn, 'utf8mb4');


$date=date('Y-m-d').' '.date('h:m:s');
include('functions.php');
