<?php
// The About Us page has one set of content, so its list is no longer needed: go straight to the form.
include('db/config.php');
include('lock2.php');
if (empty($_SESSION['email_id'])) {
	exit;
}
header('Location: ' . BASE_URL . 'edit-about-content.php?edit=' . base64_encode(1));
exit;
