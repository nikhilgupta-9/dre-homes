<?php 
sleep(1);
include "../db/config.php";
require 'vendor/autoload.php';


  
    // $name=$_POST['name'];
    // $email=$_POST['email'];

    $name = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile=$_POST['book_val_phone_hidden'].' '.$_POST['phone']; 
    $location=$_POST['location'];
    $appointment_date=$_POST['appointment_date'];
    $appointment_time=$_POST['appointment_time'];
    $enq_about=$_POST['enq_about'];
    $enq_type=$_POST['enq_type'];
    $totl_bedr=$_POST['totl_bedr'];
    $area_sqft=$_POST['area_sqft'];
    $ip_address=get_client_ip();
    $details=ip_details("$ip_address");
    $country_code= $details->country; 
    $ddate = date(' d-m-Y H:i:s');

    $query=mysqli_query($conn,"insert into tbl_bookval_frm
	                                              SET name='$name',
	                                                  email='$email',
	                                                  mobile='$mobile',
	                                                  location='$location',
	                                                  appointment_date='$appointment_date',
	                                                  appointment_time='$appointment_time',
	                                                  enq_about='$enq_about',
	                                                  enq_type='$enq_type',
	                                                  totl_bedr='$totl_bedr',
	                                                  area_sqft='$area_sqft',
	                                                  ip_address='$ip_address',
	                                                  is_active='1'");


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
$headers .= "Reply-To: donotreply@drehomes.com\r\n";
$headers .= "CC: internal-leads@drehomes.ae\r\n";
$headers .= "CC: leads@drehomes.ae\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if($query){

    $mail_sent = mail($to, $subject, $message, $headers);

    if($mail_sent){
        $attr = ['status'=>'success','msg'=>'Thank you, we will contact you shortly!'];
    } else {
        $attr = ['status'=>'error','msg'=>'Saved but email failed'];
    }

} else {
    $attr = ['status'=>'error','msg'=>'Database error'];
}

echo json_encode($attr);





 ?>
