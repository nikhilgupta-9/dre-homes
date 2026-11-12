<?php
// Live search/filter for Manage Projects. Signed-in admins only; read-only.
// Returns the same #projResults markup manage-project.php renders, so the
// page's script can swap it in without a full reload.
include('../db/config.php');
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');

if (empty($_SESSION['email_id'])) {
	http_response_code(401);
	exit('Please sign in again.');
}

include('../includes/project-query.php');
include('../includes/project-list.php');
include('../includes/project-results.php');
