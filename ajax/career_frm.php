<?php
/*
 * Careers page application form (careers.php, #crForm).
 * Saves the application in tbl_resume and the CV in /resume (both shown in Admin > Manage Resume),
 * then emails it to the careers inbox with the CV attached.
 * Returns JSON: {"status": "success" | "error", "msg": "..."}
 */
include __DIR__ . '/../db/config.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json; charset=utf-8');

function career_reply($status, $msg, $code = 200)
{
	http_response_code($code);
	echo json_encode(['status' => $status, 'msg' => $msg]);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	career_reply('error', 'Method not allowed.', 405);
}
// Honeypot: real visitors never see or fill this field
if (!empty($_POST['website'])) {
	career_reply('success', 'Thank you, your application has been received.');
}

$first_name = trim((string) ($_POST['first_name'] ?? ''));
$email_id = trim((string) ($_POST['email_id'] ?? ''));
$phone = trim((string) ($_POST['phone_full'] ?? '')) ?: trim((string) ($_POST['phone'] ?? ''));
$linkedin_url = trim((string) ($_POST['linkedin_url'] ?? ''));
$pos = trim((string) ($_POST['pos'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($first_name === '' || mb_strlen($first_name) > 120) {
	career_reply('error', 'Please enter your name.', 422);
}
if (!filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
	career_reply('error', 'Please enter a valid email address.', 422);
}
if (!preg_match('/^\+?[0-9 ()\-]{6,25}$/', $phone)) {
	career_reply('error', 'Please enter a valid phone number.', 422);
}
if ($linkedin_url !== '' && !preg_match('#^(https?://)?([a-z]{2,3}\.)?linkedin\.com/#i', $linkedin_url)) {
	career_reply('error', 'Please enter a LinkedIn profile link, or leave it empty.', 422);
}
// Position must be one of the open roles (or an open application)
$open_roles = array_map(fn($r) => trim(preg_replace('/\s+/', ' ', $r['pos_heading'])), db_all("SELECT pos_heading FROM tbl_position WHERE is_active = '1'"));
if ($pos !== 'Open application' && !in_array($pos, $open_roles, true)) {
	career_reply('error', 'Please choose a position.', 422);
}

// CV: PDF / DOC / DOCX, up to 5 MB, checked by extension and by content
$file = $_FILES['resume'] ?? null;
if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
	career_reply('error', 'Please attach your CV.', 422);
}
if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
	career_reply('error', 'The CV could not be uploaded, please try again.', 422);
}
if ($file['size'] > 5 * 1024 * 1024) {
	career_reply('error', 'That file is over 5 MB — please upload a smaller CV.', 422);
}
$allowed = [
	'pdf' => ['application/pdf'],
	'doc' => ['application/msword', 'application/vnd.ms-office', 'application/octet-stream', 'application/CDFV2'],
	'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$mime = function_exists('finfo_open') ? (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) : '';
if (!isset($allowed[$ext]) || ($mime !== '' && !in_array($mime, $allowed[$ext], true))) {
	career_reply('error', 'Please upload a PDF, DOC or DOCX file.', 422);
}

// Stored name: resume_<random>_<cleaned original name>.<ext> (never the visitor's raw file name)
$base = preg_replace('/[^A-Za-z0-9_-]+/', '-', pathinfo($file['name'], PATHINFO_FILENAME));
$base = trim(substr($base, 0, 60), '-') ?: 'cv';
$resume = 'resume_' . bin2hex(random_bytes(4)) . '_' . $base . '.' . $ext;
$folder = dirname(__DIR__) . '/resume/';
if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
	error_log('Careers form: resume folder missing and could not be created: ' . $folder);
	career_reply('error', 'Something went wrong, please try again later.', 500);
}
if (!move_uploaded_file($file['tmp_name'], $folder . $resume)) {
	error_log('Careers form: could not move the uploaded CV to ' . $folder . $resume);
	career_reply('error', 'Something went wrong, please try again later.', 500);
}

$ip_address = get_client_ip();
$saved = db_query(
	"INSERT INTO tbl_resume (first_name, email_id, phone, linkedin_url, resume, pos, ip_address, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, '1')",
	[$first_name, $email_id, $phone, $linkedin_url, $resume, $pos, $ip_address]
);
if (!$saved) {
	@unlink($folder . $resume);
	career_reply('error', 'Something went wrong, please try again later.', 500);
}

// Email to the careers inbox (same recipients as the old site); the application is already saved if this fails
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
	$mail->setFrom(secret('smtp_from') ?: 'donotreply@drehomes.com', $first_name);
	$mail->addAddress('newcareer@drehomes.com');
	$mail->addCC('hr@drehomes.com');
	$mail->addReplyTo($email_id, $first_name);
	$mail->addAttachment($folder . $resume);
	$mail->isHTML(false);
	$mail->Subject = date('d-m-Y H:i:s') . ' : Enquiry from Drehomes:Resume';
	$mail->Body = "Name:- $first_name\r\nEmail ID:- $email_id\r\nMobile:- $phone\r\nLinkedin URL:- $linkedin_url\r\nPosition:- $pos\r\n"
		. ($message !== '' ? "Message:- $message\r\n" : '')
		. "IP Addr:- $ip_address";
	$mail->send();
} catch (Exception $e) {
	error_log('Careers form: email not sent: ' . $e->getMessage());
}

career_reply('success', 'Thank you, your application has been received.');
