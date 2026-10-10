<?php
// Loads the secrets array once and gives access through secret('db.host').
// Lookup order:
//   1. the file named in the DRE_SECRETS_FILE environment variable
//   2. dre-homes-secrets.php one level above the web root
//   3. config/secrets.php (local development)

function secrets_file_path()
{
	$candidates = [];

	$env_path = getenv('DRE_SECRETS_FILE');
	if ($env_path) {
		$candidates[] = $env_path;
	}
	$candidates[] = dirname(__DIR__, 2) . '/dre-homes-secrets.php';
	$candidates[] = __DIR__ . '/secrets.php';

	foreach ($candidates as $path) {
		if (is_file($path) && is_readable($path)) {
			return $path;
		}
	}
	return false;
}

function secret($key, $default = null)
{
	static $secrets = null;

	if ($secrets === null) {
		$path = secrets_file_path();
		if ($path === false) {
			error_log('Secrets file not found. Copy config/secrets.example.php to config/secrets.php.');
			http_response_code(500);
			exit('Service temporarily unavailable.');
		}
		$secrets = require $path;
		if (!is_array($secrets)) {
			error_log('Secrets file must return an array: ' . $path);
			http_response_code(500);
			exit('Service temporarily unavailable.');
		}
	}

	$value = $secrets;
	foreach (explode('.', $key) as $part) {
		if (!is_array($value) || !array_key_exists($part, $value)) {
			return $default;
		}
		$value = $value[$part];
	}
	return $value;
}
