<?php
/*
 * Returns the property cards for the slugs in the visitor's dre_wishlist cookie, for the
 * frontend Wishlist page (wishlist.php, #wishGrid). Looks across every listing type by slug.
 * Returns JSON: {"status": "success", "items": [...]}
 */
include __DIR__ . '/../db/config.php';

header('Content-Type: application/json; charset=utf-8');

$slugs = [];
$raw = trim((string) ($_POST['slugs'] ?? $_GET['slugs'] ?? ''));
if ($raw !== '') {
	$decoded = json_decode($raw, true);
	if (is_array($decoded)) {
		$slugs = array_map('strval', $decoded);
	}
}

$items = $slugs ? get_wishlist_cards($slugs) : [];

echo json_encode(['status' => 'success', 'items' => $items]);
