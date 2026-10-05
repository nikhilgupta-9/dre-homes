<?php 
sleep(1);
include "../db/config.php";

// Collect POST data
$name = $_POST['payplan_name'];
$phone = $_POST['payplan_number_hidden'] . ' ' . $_POST['payplan_phone']; 
$email_id = $_POST['payplan_email'];
$page_url = $_POST['page_url'];
$download_type = $_POST['download_type']; // Should be "Payment Plan"
$ip_address = get_client_ip();
$details = ip_details($ip_address);
$country_code = $details->country ?? ''; 
$ddate = date('d-m-Y H:i:s');

$prop_name = mysqli_real_escape_string($conn, $_POST['prop_name_br'] ?? '');
$prop_location = mysqli_real_escape_string($conn, $_POST['prop_location_br'] ?? '');
$prop_bedroom = mysqli_real_escape_string($conn, $_POST['prop_bedroom_br'] ?? '');
$developer = mysqli_real_escape_string($conn, $_POST['developer_br'] ?? '');
$channel = mysqli_real_escape_string($conn, $_POST['channel_br'] ?? '');
$leads_nature = mysqli_real_escape_string($conn, $_POST['leads_nature_br'] ?? '');

// Insert into DB
$query = mysqli_query($conn, "INSERT INTO tbl_pdf_enquiry
    SET name = '$name',
        phone = '$phone',
        email_id = '$email_id',
        comment = '',
        page_url = '$page_url',
        download_type = '$download_type',
        prop_name = '$prop_name',
        prop_location = '$prop_location',
        prop_bedroom = '$prop_bedroom',
        developer = '$developer',
        channel = '$channel',
        leads_nature = '$leads_nature',
        ip_address = '$ip_address',
        is_active = '1'");

if ($query) {
    // Compose email content
    $subject = "$ddate : Enquiry from Drehomes: $page_url";
    $message = "Name: $name\r\n";
    $message .= "Mobile: $phone\r\n";
    $message .= "Email ID: $email_id\r\n";
    $message .= "URL: $page_url\r\n";
    $message .= "Property Name: $prop_name\r\n";
    $message .= "Property Location: $prop_location\r\n";
    $message .= "Property Inventry Type: $prop_bedroom\r\n";
    $message .= "Property Develooper: $developer\r\n";
    $message .= "Property Channel: $channel\r\n";
    $message .= "Property Leads Nature: $leads_nature\r\n";
    $message .= "Download Type: $download_type\r\n";
    $message .= "IP Addr: $ip_address";

    $to = "leads@drehomes.ae";
    $headers = "From: donotreply@drehomes.com\r\n";
    $headers .= "Reply-To: donotreply@drehomes.com\r\n";
    $headers .= "Cc: internal-leads@drehomes.ae, leads@drehomes.ae\r\n";

    if (mail($to, $subject, $message, $headers)) {
        $attr = array('status' => 'success', 'msg' => 'Thank you for your interest, we will contact you shortly!');
    } else {
        $attr = array('status' => 'error', 'msg' => 'Mail sending failed.');
    }
} else {
    $attr = array('status' => 'error', 'msg' => 'Error! Try after sometime.');
}

echo json_encode($attr);
?>
