<?php
session_start();
header("Content-type: image/png");

// Create the image
$image = imagecreate(120, 40);

// Set the background color
$bg = imagecolorallocate($image, 255, 255, 255);
imagecolortransparent($image, $bg); // Make the background color transparent

// Generate a random string for the CAPTCHA
$captcha_string = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);

// Store the captcha string in a session variable
$_SESSION['captcha_mortgcal'] = $captcha_string;

// Set the text color
$text_color = imagecolorallocate($image, 0, 0, 0);
$line_color = imagecolorallocate($image, 140, 140, 140);

// Draw random lines for added security
for ($i = 0; $i < 40; $i++) {
    imageline($image, 0, rand() % 40, 120, rand() % 40, $line_color);
}

// Write the captcha string on the image
imagestring($image, 5, 5, 2, $captcha_string, $text_color); // Changed font size to 5 for visibility

// Output the image
imagepng($image);

// Destroy the image
imagedestroy($image);
?>
