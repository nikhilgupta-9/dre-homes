<?php 
sleep(1);
include "../db/config.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get POST data
$name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$mobile = ($_POST['book_val_phone_hidden'] ?? '') . ' ' . ($_POST['phone'] ?? '');
$location = mysqli_real_escape_string($conn, $_POST['location'] ?? '');
$appointment_date = mysqli_real_escape_string($conn, $_POST['appointment_date'] ?? '');
$appointment_time = mysqli_real_escape_string($conn, $_POST['appointment_time'] ?? '');
$enq_about = mysqli_real_escape_string($conn, $_POST['enq_about'] ?? '');
$enq_type = mysqli_real_escape_string($conn, $_POST['enq_type'] ?? '');
$totl_bedr = mysqli_real_escape_string($conn, $_POST['totl_bedr'] ?? '');
$area_sqft = mysqli_real_escape_string($conn, $_POST['area_sqft'] ?? '');
$ip_address = $_SERVER['REMOTE_ADDR'];
$ddate = date('d-m-Y H:i:s');

// SIMPLE INSERT - First check if table exists and has basic columns
$query = "INSERT INTO tbl_book_val_enquiry (name, email_id, mobile, location, appointment_date, appointment_time, enq_about, enq_type, totl_bedr, area_sqft, ip_address, is_active) 
          VALUES ('$name', '$email', '$mobile', '$location', '$appointment_date', '$appointment_time', '$enq_about', '$enq_type', '$totl_bedr', '$area_sqft', '$ip_address', '1')";

$query_result = mysqli_query($conn, $query);

if (!$query_result) {
    // If error, try with basic columns only
    $error = mysqli_error($conn);
    $query2 = "INSERT INTO tbl_book_val_enquiry (name, email_id, mobile, ip_address, is_active) 
               VALUES ('$name', '$email', '$mobile', '$ip_address', '1')";
    $query_result = mysqli_query($conn, $query2);
    
    if (!$query_result) {
        $response = ['status' => 'error', 'msg' => 'Database Error: ' . mysqli_error($conn)];
        echo json_encode($response);
        exit;
    }
}

// Send Email
$to = "leads@drehomes.ae";
$subject = "$ddate : Enquiry from Drehomes: Book your Valuation Form";

$message = "Name:- $name\r\n";
$message .= "Mobile:- $mobile\r\n";
$message .= "Email ID:- $email\r\n";
$message .= "Location:- $location\r\n";
$message .= "Appointment Date:- $appointment_date\r\n";
$message .= "Appointment Time:- $appointment_time\r\n";
$message .= "Enquiry About:- $enq_about\r\n";
$message .= "Enquiry Type:- $enq_type\r\n";
$message .= "Total Bedroom:- $totl_bedr\r\n";
$message .= "Area Sqft:- $area_sqft\r\n";
$message .= "IP Addr:- $ip_address\r\n";

$headers = "From: donotreply@drehomes.com\r\n";
// $headers .= "Reply-To: donotreply@drehomes.com\r\n";
$headers .= "Reply-To: iamnikhilgupta9@gmail.com\r\n";
$headers .= "CC: internal-leads@drehomes.ae\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$mail_sent = mail($to, $subject, $message, $headers);

if ($mail_sent) {
    $response = ['status' => 'success', 'msg' => 'Thank you, we will contact you shortly!'];
} else {
    $response = ['status' => 'warning', 'msg' => 'Form submitted but email notification failed.'];
}

echo json_encode($response);
?>