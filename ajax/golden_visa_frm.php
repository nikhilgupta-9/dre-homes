<?php 
sleep(1);
include "../db/config.php";
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; 



  
    $name=$_POST['name'];
    $email_id=$_POST['email_id'];
    $number=$_POST['golden_visa_phone_hidden'].' '.$_POST['number']; 
    $message=$_POST['message'];
    $ip_address=get_client_ip();
    $details=ip_details("$ip_address");
    $country_code= $details->country; 
    $ddate = date(' d-m-Y H:i:s');

    $query=mysqli_query($conn,"insert into tbl_goldvisa_enq
	                                              SET name='$name',
	                                                  email_id='$email_id',
	                                                  number='$number',
	                                                  message='$message',
	                                                  ip_address='$ip_address',
	                                                  is_active='1'");


        if($query)
    {
		$mail = new PHPMailer(true);
  $mail->setFrom('donotreply@drehomes.com', "$name");
  $mail->addAddress('leads@drehomes.ae', 'leads@drehomes.ae');
  $mail->addReplyTo('donotreply@drehomes.com', 'Drehomes');
  $mail->addCC('internal-leads@drehomes.ae');
  $mail->addCC('leads@drehomes.ae');
  

  $mail->isHTML(false);                                
  $mail->Subject = "$ddate : Enquiry from Drehomes:Golden Visa Form";
  $mail->Body    = "Name:- " . $name. "\r\n" . "Mobile:- " . $number . "\r\n" . "Email ID:- " . $email_id . "\r\n" . "Comment:- " . $message . "\r\n" .  "IP Addr:- " . get_client_ip();
  $mail->send();
		
		$attr=array('status'=>'success','msg'=>'Thank you for your interest, we will contact you shortly!');


    }
else
{
	$attr=array('status'=>'error','msg'=>'Error ! Try after sometime');
}

echo json_encode($attr);





 ?>
