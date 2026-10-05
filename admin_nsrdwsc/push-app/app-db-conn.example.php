<?php
// Copy this file to app-db-conn.php and fill in the mobile-app database credentials.
// app-db-conn.php is git-ignored: never commit real credentials.
$servername = "localhost";
$username = "DB_USER";
$password = "DB_PASSWORD";
$dbname = "APP_DB_NAME";

// Create connection
$app_conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($app_conn->connect_error) {
  die("Connection failed");
}
