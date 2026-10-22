<?php
// Loads the secrets array once and gives access through secret('db.host').
// A key written in .env (project root) wins, even when it is empty (e.g. DB_PASS= for a
// local MySQL without a password): secret('smtp_host') reads SMTP_HOST, secret('db.host') reads DB_HOST.
// A key left out of .env (or commented out with #) comes from the secrets file:
//   1. the file named in the DRE_SECRETS_FILE environment variable
//   2. dre-homes-secrets.php one level above the web root
//   3. config/secrets.php (local development)

$dre_autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($dre_autoload)) {
	require_once $dre_autoload;
	if (class_exists('Dotenv\\Dotenv')) {
		// Immutable: real server environment variables are never overwritten by .env
		Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
	}
}
unset($dre_autoload);

// Value of the .env / environment variable for a secret key, or null when the variable is not set at all
function env_secret($key)
{
	$name = strtoupper(str_replace('.', '_', $key));
	$value = $_ENV[$name] ?? $_SERVER[$name] ?? getenv($name);
	if ($value === false || $value === null || is_array($value)) {
		return null;
	}
	switch (strtolower($value)) {
		case 'true':
			return true;
		case 'false':
			return false;
	}
	return $value;
}

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

	$env_value = env_secret($key);
	if ($env_value !== null) {
		return $env_value;
	}

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
