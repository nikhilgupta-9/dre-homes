<?php
// Login page: someone already signed in goes straight to the dashboard
if (!empty($_SESSION['email_id'])) {
	header('Location: ' . BASE_URL . 'dashboard.php');
	exit;
}

?>
