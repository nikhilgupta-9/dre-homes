<?php
session_start();
if ($_POST['captcha_dev_text'] == $_SESSION['captcha_dev']) {
  echo 'true';
} else {
  echo 'false';
}