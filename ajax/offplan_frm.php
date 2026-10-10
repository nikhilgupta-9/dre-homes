<?php 
sleep(1);
include "../db/config.php";

// Function to get IP details (supports both IPv4 and IPv6)
/*function ip_details($IPaddress) 
{
    // Validate IP address
    if (!filter_var($IPaddress, FILTER_VALIDATE_IP)) {
        return json_decode(json_encode(['country' => 'Unknown', 'city' => 'Unknown']));
    }
    
    // ipinfo.io supports both IPv4 and IPv6
    $json = @file_get_contents("http://ipinfo.io/{$IPaddress}/json");
    
    if ($json === false) {
        // Fallback if ipinfo.io fails
        return json_decode(json_encode(['country' => 'Unknown', 'city' => 'Unknown']));
    }
    
    $details = json_decode($json);
    
    // Return default if something is missing
    if (empty($details)) {
        $details = new stdClass();
        $details->country = 'Unknown';
        $details->city = 'Unknown';
    }
    
    return $details;
}*/

// Get and sanitize form inputs
$name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$phone = $_POST['sideform_country_code'] . ' ' . $_POST['phone'];
$message = mysqli_real_escape_string($conn, $_POST['message'] ?? '');
$page_url = mysqli_real_escape_string($conn, $_POST['page_url'] ?? '');
$download_type = mysqli_real_escape_string($conn, $_POST['download_type'] ?? '');

$prop_name = mysqli_real_escape_string($conn, $_POST['prop_name'] ?? '');
$prop_location = mysqli_real_escape_string($conn, $_POST['prop_location'] ?? '');
$prop_bedroom = mysqli_real_escape_string($conn, $_POST['prop_bedroom'] ?? '');
$developer = mysqli_real_escape_string($conn, $_POST['developer'] ?? '');
$channel = mysqli_real_escape_string($conn, $_POST['channel'] ?? '');
$leads_nature = mysqli_real_escape_string($conn, $_POST['leads_nature'] ?? '');

// Validate required fields
if (empty($name) || empty($email) || empty($phone)) {
    echo json_encode(['status' => 'error', 'msg' => 'Please fill all required fields']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid email address']);
    exit;
}

// Get IP address (supports both IPv4 and IPv6)
$ip_address = get_client_ip();
$details = ip_details($ip_address);
$country_code = isset($details->country) ? mysqli_real_escape_string($conn, $details->country) : 'Unknown';
$city = isset($details->city) ? mysqli_real_escape_string($conn, $details->city) : 'Unknown';
$ddate = date('d-m-Y H:i:s');

// Insert into database (added missing fields)
$query = mysqli_query($conn, "
    INSERT INTO tbl_offplan_enq 
        SET name = '$name',
            email = '$email',
            phone = '$phone',
            message = '$message',
            prop_name = '$prop_name',
            prop_location = '$prop_location',
            prop_bedroom = '$prop_bedroom',
            developer = '$developer',
            channel = '$channel',
            leads_nature = '$leads_nature',
            ip_address = '$ip_address',
            is_active = '1'
");

// If saved successfully, send email
if ($query) {
    // Compose email content
    $subject = "$ddate : Enquiry from Drehomes: Sidebar Form - $page_url";
    
    $email_body = "Name: $name\r\n";
    $email_body .= "Mobile: $phone\r\n";
    $email_body .= "Email ID: $email\r\n";
    $email_body .= "Message: $message\r\n";
    $email_body .= "Property Name: $prop_name\r\n";
    $email_body .= "Property Location: $prop_location\r\n";
    $email_body .= "Property Inventry Type: $prop_bedroom\r\n";
    $email_body .= "Property Develooper: $developer\r\n";
    $email_body .= "Property Channel: $channel\r\n";
    $email_body .= "Property Leads Nature: $leads_nature\r\n";
    $email_body .= "Page URL: $page_url\r\n";
    $email_body .= "Download Type: $download_type\r\n";
    $email_body .= "IP Address: $ip_address\r\n";
    $email_body .= "Country: $country_code\r\n";
    $email_body .= "City: $city\r\n";
    $email_body .= "Date: $ddate\r\n";
    
    // Email headers
    $to = "leads@drehomes.ae";
    $headers = "From: donotreply@drehomes.com\r\n";
    $headers .= "Reply-To: donotreply@drehomes.com\r\n";
    $headers .= "Cc: internal-leads@drehomes.ae, leads@drehomes.ae\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Send the email
    if (mail($to, $subject, $email_body, $headers)) {
        $attr = ['status' => 'success', 'msg' => 'Thank you for your interest, we will contact you shortly!'];
    } else {
        $attr = ['status' => 'error', 'msg' => 'Mail sending failed. Please try again.'];
    }
} else {
    $attr = ['status' => 'error', 'msg' => 'Error! Try again later. ' . mysqli_error($conn)];
}
    
echo json_encode($attr);
?>