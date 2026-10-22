<?php
// Copy this file to config/secrets.php and fill in the values for your environment.
// config/secrets.php is git-ignored: never commit real credentials.
//
// On production keep the real file outside the web root and point to it with the
// DRE_SECRETS_FILE environment variable, or place it one level above the web root
// as dre-homes-secrets.php. See config/load.php for the lookup order.
return [
	'base_url'     => 'http://localhost/dre_homes_new/',
	'base_url_img' => 'http://localhost/dre_homes_new/admin_nsrdwsc/assets/media/',

	'db' => [
		'host' => 'localhost',
		'user' => 'DB_USER',
		'pass' => 'DB_PASSWORD',
		'name' => 'DB_NAME',
	],

	// IPs of reverse proxies in front of this server (for example a CDN).
	// Leave empty on plain shared hosting: the visitor IP is then REMOTE_ADDR.
	'trusted_proxies' => [],

	// Exchange-rate API, called from PHP only.
	'exchange_rate_api_key' => '',

	// AI providers (used from the AI phases onwards). Server side only.
	'anthropic_api_key' => '',
	// Chatbot on/off. Keep false on production until the security checklist is done.
	'ai_chat_enabled' => false,
	// Where chat leads are emailed. Empty = saved to the enquiry table only.
	'ai_lead_notify_email' => '',

	// SMTP account the AI chat's lead emails are sent through. Empty smtp_host = PHP's mail() is used,
	// which works on the live server but not on a local Mac. For Gmail: smtp.gmail.com, port 587,
	// your Gmail address as user and from, and an App Password (Google account > Security > App passwords).
	'smtp_host' => '',
	'smtp_port' => 587,
	'smtp_user' => '',
	'smtp_pass' => '',
	'smtp_from' => '',

	'gemini_api_key'    => '',
	'voyage_api_key'    => '',

	// GenieMap (geniemap.net) partner API key, for the admin's "Import from GenieMap" page.
	'geniemap_api_token' => '',

	// Mobile app database (admin_nsrdwsc/push-app)
	'app_db' => [
		'host' => 'localhost',
		'user' => 'APP_DB_USER',
		'pass' => 'APP_DB_PASSWORD',
		'name' => 'APP_DB_NAME',
	],
];
