<?php
session_start();
if ($_POST['captcha_footer_text'] == $_SESSION['captcha_footer']) {
  echo 'true';
} else {
  echo 'false';
}