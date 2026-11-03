<?php

header('Content-Type: application/json');

$crmData = [
    "First_Name"      => $_POST['first_name'] ?? '',
    "Last_Name"       => '',
    "Email"           => $_POST['email'] ?? '',
    "Phone"           => $_POST['phone'] ?? '',
    "Lead_Source"     => $_POST['lead_source'] ?? '',
    "Developers"      => !empty($_POST['developer']) ? [$_POST['developer']] : [],
    "Unit_Type"       => $_POST['unit_type'] ?? '',
    "Campaign_Name"   => $_POST['campaign_name'] ?? '',
    "IP_Address"      => $_POST['ip_address'] ?? '',
    "Sub_Location_SL" => $_POST['sub_location'] ?? ''
];

$payload = json_encode($crmData);

$ch = curl_init('https://www.zohoapis.com/crm/v7/functions/getdatafromwebsitepage/actions/execute?auth_type=apikey&zapikey=YOUR_API_KEY');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => $payload
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode([
        'status' => false,
        'message' => curl_error($ch)
    ]);
    exit;
}

curl_close($ch);

echo json_encode([
    'status' => true,
    'message' => 'Lead pushed successfully',
    'crm_payload' => $crmData,
    'response' => json_decode($response, true)
]);