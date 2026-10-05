<?php
include('../db/config.php');

$username=prepStr($_POST['username']);


$query=query("tbl_login","username='$username'");
$tot=total_record($query);

if($tot>0)
{
	echo "false";
}
else
{
	echo "true";
}
