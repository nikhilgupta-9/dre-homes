<?php
// Live search/filter for Manage Developers. Signed-in admins only; read-only.
// Returns the same #devResults markup manage-developer.php renders, so the
// page's script can swap it in without a full reload.
include('../db/config.php');
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');

if (empty($_SESSION['email_id'])) {
	http_response_code(401);
	exit('Please sign in again.');
}

include('../includes/developer-query.php');
include('../includes/developer-list.php');
include('../includes/developer-results.php');
