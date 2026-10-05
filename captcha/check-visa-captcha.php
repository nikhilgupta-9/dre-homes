<?php
session_start();
if ($_POST['captcha_visa_text'] == $_SESSION['captcha_visa']) {
  echo 'true';
} else {
  echo 'false';
}