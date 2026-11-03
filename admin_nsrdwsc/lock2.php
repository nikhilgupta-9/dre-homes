<?php
// Admin pages: anyone who is not signed in goes to the login page, and nothing else of the page is sent
if (empty($_SESSION['email_id'])) {
	header('Location: ' . BASE_URL);
	exit;
}
?>
