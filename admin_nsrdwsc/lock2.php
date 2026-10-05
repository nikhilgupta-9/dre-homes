<?php
if(empty($_SESSION['email_id']))
{
	header('Location:'.BASE_URL.'');
}
?>