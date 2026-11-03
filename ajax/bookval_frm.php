<?php
/**
 * Book Valuation lead submission endpoint (book-valuation.php, #bvForm).
 * Saves to tbl_book_val_enquiry (Admin > Manage Leads) and tbl_bookval_frm,
 * then sends notification email to leads inbox.
 * Returns JSON: {"status": "success" | "error", "msg": "..."}
 */
include_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json; charset=utf-8');

function bookval_reply($status, $msg, $code = 200)
{
	http_response_code($code);
	echo json_encode(['status' => $status, 'msg' => $msg]);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	bookval_reply('error', 'Method not allowed.', 405);
}

// Honeypot: real visitors never see or fill this field
if (!empty($_POST['website'])) {
	bookval_reply('success', 'Thank you, we will contact you shortly!');
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));

// Phone resolution
$phone = trim((string) ($_POST['phone_full'] ?? ''));
if ($phone === '') {
	$prefix = trim((string) ($_POST['book_val_phone_hidden'] ?? ''));
	$raw_phone = trim((string) ($_POST['phone'] ?? ''));
	$phone = $prefix !== '' ? ($prefix . ' ' . $raw_phone) : $raw_phone;
}

$location = trim((string) ($_POST['location'] ?? ''));
$appointment_date = trim((string) ($_POST['date'] ?? ($_POST['appointment_date'] ?? '')));
$appointment_time = trim((string) ($_POST['time'] ?? ($_POST['appointment_time'] ?? '')));
$enq_about = trim((string) ($_POST['topic'] ?? ($_POST['enq_about'] ?? '')));
$enq_type = trim((string) ($_POST['ptype'] ?? ($_POST['enq_type'] ?? '')));
$totl_bedr = trim((string) ($_POST['beds'] ?? ($_POST['totl_bedr'] ?? '')));
$area_sqft = trim((string) ($_POST['area_sqft'] ?? ''));

// Validations
if ($name === '' || mb_strlen($name) > 120) {
	bookval_reply('error', 'Please enter your name.', 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	bookval_reply('error', 'Please enter a valid email address.', 422);
}
if (!preg_match('/^\+?[0-9 ()\-]{6,25}$/', $phone)) {
	bookval_reply('error', 'Please enter a valid phone number.', 422);
}
if ($location === '') {
	bookval_reply('error', 'Please enter your preferred location.', 422);
}
if ($appointment_date === '') {
	bookval_reply('error', 'Please select an appointment date.', 422);
}
if ($appointment_time === '') {
	bookval_reply('error', 'Please select an appointment time.', 422);
}

$ip_address = function_exists('get_client_ip') ? get_client_ip() : ($_SERVER['REMOTE_ADDR'] ?? '');
$ddate = date('d-m-Y H:i:s');

$message = "Preferred date: $appointment_date, time: $appointment_time, location: $location";

// 1. Insert into tbl_book_val_enquiry (Admin > Manage Leads)
db_query(
	"INSERT INTO tbl_book_val_enquiry 
	 (name, email_id, mobile, location, appointment_date, appointment_time, enq_type, totl_bedr, area_sqft, enq_about, message, ip_address, is_active) 
	 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1')",
	[$name, $email, $phone, $location, $appointment_date, $appointment_time, $enq_type, $totl_bedr, $area_sqft, $enq_about, $message, $ip_address]
);

// 2. Insert into tbl_bookval_frm (Legacy table)
db_query(
	"INSERT INTO tbl_bookval_frm 
	 (name, email, mobile, location, appointment_date, appointment_time, enq_about, enq_type, totl_bedr, area_sqft, ip_address, is_active) 
	 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1')",
	[$name, $email, $phone, $location, $appointment_date, $appointment_time, $enq_about, $enq_type, $totl_bedr, $area_sqft, $ip_address]
);

// 3. Email notification
$to = 'leads@drehomes.ae';
$subject = "$ddate : Enquiry from Drehomes: Book your Valuation Form";
$body = "Name:- $name\r\n"
	. "Mobile:- $phone\r\n"
	. "Email ID:- $email\r\n"
	. "Location:- $location\r\n"
	. "Appointment Date:- $appointment_date\r\n"
	. "Appointment Time:- $appointment_time\r\n"
	. "Enquiry About:- $enq_about\r\n"
	. "Enquiry Type:- $enq_type\r\n"
	. "Total Bedroom:- $totl_bedr\r\n"
	. "Area Sqft:- $area_sqft\r\n"
	. "IP Addr:- $ip_address\r\n";

try {
	$mail = new PHPMailer(true);
	if (function_exists('secret') && secret('smtp_host')) {
		$mail->isSMTP();
		$mail->Host = secret('smtp_host');
		$mail->Port = (int) secret('smtp_port', 587);
		$mail->SMTPAuth = (bool) secret('smtp_user');
		$mail->Username = (string) secret('smtp_user', '');
		$mail->Password = (string) secret('smtp_pass', '');
		$mail->SMTPSecure = $mail->Port === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
	}
	$mail->CharSet = 'UTF-8';
	$from_email = (function_exists('secret') && secret('smtp_from')) ?: 'donotreply@drehomes.com';
	$mail->setFrom($from_email, $name);
	$mail->addAddress($to);
	$mail->addReplyTo($email, $name);
	$mail->addCC('internal-leads@drehomes.ae');
	$mail->addCC('leads@drehomes.ae');
	$mail->isHTML(false);
	$mail->Subject = $subject;
	$mail->Body = $body;
	$mail->send();
} catch (Exception $e) {
	// Fallback to PHP mail()
	$headers = "From: donotreply@drehomes.com\r\n"
		. "Reply-To: donotreply@drehomes.com\r\n"
		. "CC: internal-leads@drehomes.ae, leads@drehomes.ae\r\n"
		. "MIME-Version: 1.0\r\n"
		. "Content-Type: text/plain; charset=UTF-8\r\n";
	@mail($to, $subject, $body, $headers);
	error_log('Book valuation email send error: ' . $e->getMessage());
}

bookval_reply('success', 'Thank you, we will contact you shortly!');
