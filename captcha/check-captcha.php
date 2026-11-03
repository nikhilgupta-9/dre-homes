<?php
session_start();
if ($_POST['captcha_text'] == $_SESSION['captcha_dev']) {
  echo 'true';
} else {
  echo 'false';
}