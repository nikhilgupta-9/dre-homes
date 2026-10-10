<?php
// A small copy of a GenieMap project's first picture, for the cards of the import page.
// The full pictures are 0.5 to 1.5 MB each; 24 of them made every page of the list slow.
// Only a project number is accepted, never an address, and only for someone signed in.
include('../db/config.php');
if (empty($_SESSION['email_id'])) {
	http_response_code(403);
	exit;
}
// The session is not needed any more; closing it lets the pictures of a page load side by side.
session_write_close();

require_once __DIR__ . '/../../config/load.php';
require_once __DIR__ . '/../includes/GenieMap.php';

$genie = new GenieMap(secret('geniemap_api_token', ''));
$file = $genie->isConfigured() ? $genie->thumb((int) ($_GET['id'] ?? 0)) : '';
while (ob_get_level() > 0) {
	ob_end_clean();
}
if ($file === '') {
	http_response_code(404);
	exit;
}
header('Content-Type: image/jpeg');
header('Content-Length: ' . filesize($file));
header('Cache-Control: private, max-age=604800');
readfile($file);
