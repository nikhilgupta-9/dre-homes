<?php
session_start();



if ($_POST['captcha_bookval_text'] == $_SESSION['captcha_bookval']) {
  echo 'true';
} else {
  echo 'false';
}