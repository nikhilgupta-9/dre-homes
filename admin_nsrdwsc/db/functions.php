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


// this functon use for create folder during file uploads (if folder is not exist ) 
function uploadFileWithFolder($file, $hidden_value, $upload_path, $prefix) {
    if (!empty($file['name'])) {
        // Create folder if not exists
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        
        $new_filename = $prefix . '_' . time() . '_' . rand(1000, 9999) . '_' . basename($file['name']);
        $full_path = $upload_path . $new_filename;
        
        if (move_uploaded_file($file['tmp_name'], $full_path)) {
            // Delete old file if exists
            if (!empty($hidden_value) && file_exists($upload_path . $hidden_value)) {
                unlink($upload_path . $hidden_value);
            }
            return $new_filename;
        }
    }
    return $hidden_value;
}
