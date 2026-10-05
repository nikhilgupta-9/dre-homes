<?php
// zoho-callback.php

ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'db/config.php';

// STEP 1: Check if code received
if (!isset($_GET['code'])) {
    die("Authorization code not received!");
}

$code = $_GET['code'];

// STEP 2: Your Zoho credentials
$client_id = "YOUR_CLIENT_ID";
$client_secret = "YOUR_CLIENT_SECRET";
$redirect_uri = BASE_URL ."zoho-callback.php"; // must match EXACT

// STEP 3: Exchange code for tokens
$url = "https://accounts.zoho.in/oauth/v2/token";

$postData = [
    "grant_type" => "authorization_code",
    "client_id" => $client_id,
    "client_secret" => $client_secret,
    "redirect_uri" => $redirect_uri,
    "code" => $code
];

// STEP 4: CURL request
$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($postData),
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    die("Curl Error: " . curl_error($ch));
}

curl_close($ch);

$data = json_decode($response, true);

// STEP 5: Show response (for testing)
echo "<pre>";
print_r($data);
echo "</pre>";

// STEP 6: Save tokens (IMPORTANT)
if (isset($data['refresh_token'])) {

    // Example: save in file (you can use DB instead)
    file_put_contents("zoho_tokens.json", json_encode($data));

    echo "Tokens saved successfully!";
} else {
    echo "Error getting tokens!";
}
?>