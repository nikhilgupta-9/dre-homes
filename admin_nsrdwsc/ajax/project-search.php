<?php
// Live search for the Manage Projects search box (assets/js/custom.js). Signed-in admins only; read-only.
// GET q = text typed (2+ characters), type = listing type (optional). Returns JSON: {"results": [...]}
include('../db/config.php');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (empty($_SESSION['email_id'])) {
	http_response_code(401);
	echo json_encode(['results' => [], 'error' => 'Please sign in again.']);
	exit;
}

$results = get_project_search_results($conn, $_GET['q'] ?? '', (int) ($_GET['type'] ?? 0));
echo json_encode(['results' => $results]);
