<?php

function getYouTubeVideoId($url) {
    parse_str(parse_url($url, PHP_URL_QUERY), $vars);
    return $vars['v'] ?? '';
}

function prepStr($val)
{
   global $conn;
   $data=mysqli_real_escape_string($conn,$val);
   return $data;
}


function query($tbl_name,$cond)
{
	global $conn;
	$query=mysqli_query($conn,"select * from $tbl_name where $cond");
	if(!empty($query))
	{
		return $query;
	}
	return false;
	
}

function query_detail($tbl_name,$cond)
{
	global $conn;
	$query=mysqli_fetch_array(mysqli_query($conn,"select * from $tbl_name where $cond"));

	if(!empty($query))
	{
		return $query;
	}
	return false;
	
}

function total_record($cond)
{
	global $conn;
	$total=mysqli_num_rows($cond);
	if(!empty($total))
	{
		return $total;
	}
	return false;

	
}

function check_email($tbl_name,$email_id)
{
	global $conn;
	$query=mysqli_query($conn,"select * from $tbl_name where login_email='$email_id'");
	$totl=mysqli_num_rows($query);

	if(!empty($total))
	{
       return $total;
	}

	return false;

}
