<?php
session_start();
if ($_POST['captcha_mortgcal_text'] == $_SESSION['captcha_mortgcal']) {
  echo 'true';
} else {
  echo 'false';
}