<?php
include('../db/config.php');

$login_email=prepStr($_POST['login_email']);


$query=query("tbl_login","login_email='$login_email'");
$tot=total_record($query);

if($tot>0)
{
	echo "false";
}
else
{
	echo "true";
}
