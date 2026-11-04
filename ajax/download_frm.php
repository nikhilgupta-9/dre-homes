<?php
/*
 * Brochure / Payment plan / Floor plan download form (#dlForm on the property detail pages:
 * single-property, ready-to-move-in-single, international-properties-single).
 * Saves the request in tbl_pdf_enquiry (shown in Admin > Manage Leads under "PDF Download"),
 * then emails it to the leads inbox.
 * Returns JSON: {"status": "success" | "error", "msg": "..."}
 */
include __DIR__ . '/../db/config.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json; charset=utf-8');

function download_reply($status, $msg, $code = 200)
{
	http_response_code($code);
	echo json_encode(['status' => $status, 'msg' => $msg]);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	download_reply('error', 'Method not allowed.', 405);
}
// Honeypot: real visitors never see or fill this field
if (!empty($_POST['website'])) {
	download_reply('success', 'Thank you, the file is on its way.');
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone_full'] ?? '')) ?: trim((string) ($_POST['phone'] ?? ''));
$item = trim((string) ($_POST['item'] ?? 'Brochure'));
$project = trim((string) ($_POST['project'] ?? ''));
$page_url = trim((string) ($_POST['page_url'] ?? ''));

if ($name === '' || mb_strlen($name) > 120) {
	download_reply('error', 'Please enter your name.', 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	download_reply('error', 'Please enter a valid email address.', 422);
}
if (!preg_match('/^\+?[0-9 ()\-]{6,25}$/', $phone)) {
	download_reply('error', 'Please enter a valid phone number.', 422);
}
if (!in_array($item, ['Brochure', 'Payment Plan', 'Floor Plan'], true)) {
	$item = 'Brochure';
}

$ip_address = get_client_ip();
$saved = db_query(
	"INSERT INTO tbl_pdf_enquiry (name, phone, email_id, comment, page_url, download_type, prop_name, channel, leads_nature, ip_address, is_active)
	 VALUES (?, ?, ?, '', ?, ?, ?, 'Website', 'Download Request', ?, '1')",
	[$name, $phone, $email, $page_url, $item, $project, $ip_address]
);
if (!$saved) {
	download_reply('error', 'Something went wrong, please try again later.', 500);
}

// Email to the leads inbox; the request is already saved if this fails
try {
	$mail = new PHPMailer(true);
	if (secret('smtp_host')) {
		$mail->isSMTP();
		$mail->Host = secret('smtp_host');
		$mail->Port = (int) secret('smtp_port', 587);
		$mail->SMTPAuth = (bool) secret('smtp_user');
		$mail->Username = (string) secret('smtp_user', '');
		$mail->Password = (string) secret('smtp_pass', '');
		$mail->SMTPSecure = $mail->Port === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
	}
	$mail->CharSet = 'UTF-8';
	$mail->setFrom(secret('smtp_from') ?: 'donotreply@drehomes.com', $name);
	$mail->addAddress('leads@drehomes.ae');
	$mail->addCC('internal-leads@drehomes.ae');
	$mail->addReplyTo($email, $name);
	$mail->isHTML(false);
	$mail->Subject = date('d-m-Y H:i:s') . " : {$item} request from Drehomes: {$page_url}";
	$mail->Body = "Name:- $name\r\nEmail ID:- $email\r\nMobile:- $phone\r\nProperty:- $project\r\nRequested:- $item\r\nPage URL:- $page_url\r\nIP Addr:- $ip_address";
	$mail->send();
} catch (Exception $e) {
	error_log('Download form: email not sent: ' . $e->getMessage());
}

download_reply('success', 'Thank you, the file is on its way.');
