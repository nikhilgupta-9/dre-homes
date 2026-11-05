<?php
/*
 * Wishlist (♡) add/remove endpoint, called from assets/js/site.js whenever a visitor
 * taps a property card's heart. The visitor's own wishlist lives in their dre_wishlist
 * cookie (set client-side); this endpoint only logs the action to tbl_wishlist_log for
 * admin tracking (Admin > Wishlist Activity), and makes sure a 1-year anonymous visitor
 * id cookie (dre_wish_uid) exists so repeat actions can be told apart.
 * Returns JSON: {"status": "success" | "error", "msg": "..."}
 */
include __DIR__ . '/../db/config.php';

header('Content-Type: application/json; charset=utf-8');

function wishlist_reply($status, $msg, $code = 200)
{
	http_response_code($code);
	echo json_encode(['status' => $status, 'msg' => $msg]);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	wishlist_reply('error', 'Method not allowed.', 405);
}

$slug = trim((string) ($_POST['slug'] ?? ''));
$action = trim((string) ($_POST['action'] ?? ''));

if ($slug === '' || !in_array($action, ['add', 'remove'], true)) {
	wishlist_reply('error', 'Invalid request.', 422);
}

$one_year = 60 * 60 * 24 * 365;

$wish_uid = $_COOKIE['dre_wish_uid'] ?? '';
if ($wish_uid === '' || !preg_match('/^[a-f0-9]{32}$/', $wish_uid)) {
	$wish_uid = bin2hex(random_bytes(16));
	setcookie('dre_wish_uid', $wish_uid, [
		'expires' => time() + $one_year,
		'path' => '/',
		'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
		'httponly' => true,
		'samesite' => 'Lax',
	]);
}

$page_url = trim((string) ($_POST['page'] ?? ($_SERVER['HTTP_REFERER'] ?? '')));

save_wishlist_log($wish_uid, $slug, $action, $page_url);

wishlist_reply('success', 'Logged.');
