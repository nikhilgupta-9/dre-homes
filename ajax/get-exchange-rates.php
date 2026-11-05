<?php
/*
 * Live AED -> other currency conversion rates for the navbar currency switcher
 * (common/header.php #global-currency-select). Calls exchangerate-api.com server
 * side (the API key must never reach the browser) and caches the result in a file
 * for a few hours, since the free plan only refreshes once a day anyway.
 * Returns JSON: {"status": "success", "rates": {"AED": 1, "USD": 0.27, ...}} or
 * {"status": "error", "msg": "..."}
 */
include __DIR__ . '/../db/config.php';

header('Content-Type: application/json; charset=utf-8');

$cache_file = sys_get_temp_dir() . '/dre_exchange_rates.json';
$cache_ttl = 6 * 60 * 60; // 6 hours

if (is_file($cache_file) && (time() - filemtime($cache_file)) < $cache_ttl) {
	$cached = json_decode((string) file_get_contents($cache_file), true);
	if (is_array($cached) && !empty($cached['rates'])) {
		echo json_encode(['status' => 'success', 'rates' => $cached['rates']]);
		exit;
	}
}

$api_key = (string) secret('exchange_rate_api_key', '');
if ($api_key === '') {
	echo json_encode(['status' => 'error', 'msg' => 'Exchange rate API is not configured.']);
	exit;
}

$ctx = stream_context_create(['http' => ['timeout' => 6]]);
$raw = @file_get_contents('https://v6.exchangerate-api.com/v6/' . rawurlencode($api_key) . '/latest/AED', false, $ctx);
$data = $raw !== false ? json_decode($raw, true) : null;

if (!is_array($data) || ($data['result'] ?? '') !== 'success' || empty($data['conversion_rates'])) {
	// Serve a stale cache rather than nothing, if we have one
	if (is_file($cache_file)) {
		$cached = json_decode((string) file_get_contents($cache_file), true);
		if (is_array($cached) && !empty($cached['rates'])) {
			echo json_encode(['status' => 'success', 'rates' => $cached['rates']]);
			exit;
		}
	}
	echo json_encode(['status' => 'error', 'msg' => 'Could not fetch exchange rates.']);
	exit;
}

$rates = $data['conversion_rates'];
@file_put_contents($cache_file, json_encode(['rates' => $rates]));

echo json_encode(['status' => 'success', 'rates' => $rates]);
