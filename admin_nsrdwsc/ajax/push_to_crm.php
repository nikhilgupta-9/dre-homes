<?php
// ajax/push_to_crm.php
// ─── Zoho CRM v8 Standard API (OAuth) ───────────────────────

header('Content-Type: application/json');
include('../db/config.php');
include('../lock2.php');

// ════════════════════════════════════════════════════════════
// CONFIG — set these in db/config.php or a secrets file
// ════════════════════════════════════════════════════════════
// define('ZOHO_ACCESS_TOKEN', 'your_oauth_access_token');
// define('ZOHO_API_DOMAIN',   'https://www.zohoapis.com');   // or .eu / .in / .com.au
// ════════════════════════════════════════════════════════════

$lead_id  = intval($_POST['lead_id'] ?? 0);
$source   = $_POST['source'] ?? '';
$pushedBy = $_POST['pushed_by'] ?? 'admin';

// ── Map source → table + pk ──────────────────────────────────
$tableMap = [
    'Book Valuation'  => ['table' => 'tbl_book_val_enquiry', 'col' => 'book_val_id'],
    'Commercial Dev'  => ['table' => 'tbl_comm_dev_enquiry', 'col' => 'comm_dev_id'],
    'General Enquiry' => ['table' => 'tbl_enq',              'col' => 'enq_id'],
    'Gold Visa'       => ['table' => 'tbl_goldvisa_enq',     'col' => 'id'],
    'Offplan'         => ['table' => 'tbl_offplan_enq',      'col' => 'id'],
    'PDF Download'    => ['table' => 'tbl_pdf_enquiry',      'col' => 'pdf_enq_id'],
    'Newsletter'      => ['table' => 'tbl_subscribe_frm',    'col' => 'subscribe_frm_id'],
    'Visa Enquiry'    => ['table' => 'tbl_visa_enquiry',     'col' => 'visa_enq_id'],
];

if (!$lead_id || !isset($tableMap[$source])) {
    echo json_encode(['status' => false, 'message' => 'Invalid lead or source.']);
    exit;
}

$tbl = $tableMap[$source]['table'];
$col = $tableMap[$source]['col'];

// ── Parse name into First / Last ─────────────────────────────
$fullName  = trim($_POST['first_name'] ?? '');
$nameParts = explode(' ', $fullName, 2);
$firstName = $nameParts[0] ?? '';
$lastName  = $nameParts[1] ?? $firstName;   // Last_Name is MANDATORY in Zoho Leads
if (empty($lastName)) $lastName = $firstName; // fallback: repeat first name

// ── Build Zoho CRM v8 payload ────────────────────────────────
// Field names must match your CRM field API names exactly.
// Custom fields like Developers, Unit_Type etc. — verify their
// API names in Zoho: Setup > APIs & SDKs > API Names > Leads
$record = [
    "First_Name"      => $firstName,
    "Last_Name"       => $lastName,          // MANDATORY
    "Email"           => $_POST['email']          ?? '',
    "Phone"           => $_POST['phone']          ?? '',
    "Lead_Source"     => $_POST['lead_source']    ?? 'Web Site',
    "Description"     => $source,                // store source as description
];

// Add optional custom fields only when they have a value
// (prevents INVALID_DATA errors on empty strings for picklists)
$optionalFields = [
    'Developers'      => $_POST['developer']     ?? '',
    'Unit_Type'       => $_POST['unit_type']      ?? '',
    'Campaign_Name'   => $_POST['campaign_name']  ?? '',
    'IP_Address'      => $_POST['ip_address']     ?? '',
    'Sub_Location_SL' => $_POST['sub_location']   ?? '',
];
foreach ($optionalFields as $field => $value) {
    if (!empty(trim($value))) {
        $record[$field] = $value;
    }
}

// Zoho v8 requires data wrapped in {"data": [...]}
$payload = json_encode(['data' => [$record]]);

// ── OAuth access token ───────────────────────────────────────
$accessToken = defined('ZOHO_ACCESS_TOKEN') ? ZOHO_ACCESS_TOKEN : '';
$apiDomain   = defined('ZOHO_API_DOMAIN')   ? ZOHO_API_DOMAIN   : 'https://www.zohoapis.com';

if (empty($accessToken)) {
    echo json_encode(['status' => false, 'message' => 'Zoho OAuth access token not configured.']);
    exit;
}

// ── POST to Zoho CRM v8 Leads module ────────────────────────
$ch = curl_init($apiDomain . '/crm/v8/Leads');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Zoho-oauthtoken ' . $accessToken,
        'Content-Type: application/json',
    ],
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_TIMEOUT        => 15,
]);

$crmResponse = curl_exec($ch);
$curlError   = curl_errno($ch) ? curl_error($ch) : null;
$httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// ── Determine success ────────────────────────────────────────
// Zoho returns HTTP 201 on success; also check body for "SUCCESS"
$responseData = json_decode($crmResponse, true);
$zohoStatus   = $responseData['data'][0]['code']   ?? '';
$zohoMessage  = $responseData['data'][0]['message'] ?? '';
$zohoRecordId = $responseData['data'][0]['details']['id'] ?? null;

$success = !$curlError
    && ($httpCode === 201 || $httpCode === 200)
    && $zohoStatus === 'SUCCESS';

$status = $success ? 'success' : 'failed';
$now    = date('Y-m-d H:i:s');

// Build a meaningful error message
if (!$success) {
    if ($curlError) {
        $errorMsg = 'cURL error: ' . $curlError;
    } elseif ($zohoStatus) {
        $errorMsg = 'Zoho error ' . $zohoStatus . ': ' . $zohoMessage;
    } else {
        $errorMsg = 'HTTP ' . $httpCode . ' — unexpected response';
    }
}

// ── Update source table ──────────────────────────────────────
$escapedResp    = mysqli_real_escape_string($conn, $crmResponse ?: $curlError);
$escapedRecId   = mysqli_real_escape_string($conn, $zohoRecordId ?? '');

// crm_record_id stores the Zoho record ID for reference (add this column if needed)
mysqli_query($conn, "
    UPDATE `$tbl`
    SET crm_pushed = " . ($success ? 1 : 2) . ",
        crm_pushed_at = '$now',
        crm_push_response = '$escapedResp'
    WHERE `$col` = $lead_id
");

// ── Write to audit log ───────────────────────────────────────
$escapedPayload  = mysqli_real_escape_string($conn, $payload);
$escapedSource   = mysqli_real_escape_string($conn, $source);
$escapedPushedBy = mysqli_real_escape_string($conn, $pushedBy);
mysqli_query($conn, "
    INSERT INTO tbl_crm_push_log
        (lead_id, source, pushed_by, pushed_at, status, crm_response, payload)
    VALUES
        ($lead_id, '$escapedSource', '$escapedPushedBy', '$now',
         '$status', '$escapedResp', '$escapedPayload')
");

// ── Response ─────────────────────────────────────────────────
echo json_encode([
    'status'        => $success,
    'message'       => $success
        ? 'Lead pushed to Zoho CRM! Record ID: ' . $zohoRecordId
        : ($errorMsg ?? 'CRM push failed'),
    'lead_id'       => $lead_id,
    'source'        => $source,
    'zoho_record_id'=> $zohoRecordId,
    'http_code'     => $httpCode,
    'crm_payload'   => $record,       // what was sent
    'crm_response'  => $responseData, // what Zoho returned
]);