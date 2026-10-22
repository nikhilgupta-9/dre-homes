<?php

// Upload checks keep working on a server without PHP's fileinfo extension.
require_once __DIR__ . '/../includes/finfo-fallback.php';

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

/* =====================================================================
   Projects: live search (Manage Projects search box, ajax/project-search.php)
   ===================================================================== */

// Projects whose name, location, slug, developer or community contain $term (same fields as the page's own search).
// $type: 0 = every listing type, else one prj_type_id. Names starting with the term come first.
function get_project_search_results($conn, $term, $type = 0, $limit = 8)
{
	$term = trim(mb_substr((string) $term, 0, 100));
	if (mb_strlen($term) < 2) {
		return [];
	}
	$like = '%' . addcslashes($term, '%_\\') . '%';
	$starts = addcslashes($term, '%_\\') . '%';
	$types = (int) $type > 0 ? [(int) $type] : [1, 2, 3, 4];
	$sql = "SELECT tp.prj_id, tp.prj_type_id, tp.proj_name, tp.proj_location, tp.prj_thumb_img, tp.is_active,
	               td.dev_heading, tc.comm_heading
	        FROM tbl_proj tp
	        LEFT JOIN tbl_developer td ON td.dev_id = tp.prj_dev_id
	        LEFT JOIN tbl_community tc ON tc.comm_id = tp.prj_comm_id
	        WHERE tp.prj_type_id IN (" . implode(',', array_fill(0, count($types), '?')) . ")
	          AND (tp.proj_name LIKE ? OR tp.proj_location LIKE ? OR tp.proj_slug LIKE ? OR td.dev_heading LIKE ? OR tc.comm_heading LIKE ?)
	        ORDER BY (tp.proj_name LIKE ?) DESC, tp.is_active = '1' DESC, tp.proj_name ASC
	        LIMIT " . max(1, min(20, (int) $limit));
	$params = array_merge($types, [$like, $like, $like, $like, $like, $starts]);

	$stmt = mysqli_prepare($conn, $sql);
	if (!$stmt) {
		error_log('Project search failed: ' . mysqli_error($conn));
		return [];
	}
	mysqli_stmt_bind_param($stmt, str_repeat('i', count($types)) . 'ssssss', ...$params);
	mysqli_stmt_execute($stmt);
	$rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
	mysqli_stmt_close($stmt);

	$type_names = [1 => 'Off Plan', 2 => 'Ready to Move In', 3 => 'International', 4 => 'Commercial'];
	$results = [];
	foreach ($rows as $row) {
		$thumb = trim(explode(',', (string) $row['prj_thumb_img'])[0]);
		$results[] = [
			'id' => (int) $row['prj_id'],
			'name' => trim($row['proj_name']),
			'location' => trim((string) $row['proj_location']),
			'developer' => trim((string) $row['dev_heading']),
			'community' => trim((string) $row['comm_heading']),
			'type' => $type_names[(int) $row['prj_type_id']] ?? '',
			'live' => $row['is_active'] === '1',
			'thumb' => $thumb !== '' ? BASE_URL . 'assets/media/project/thumb/' . rawurlencode($thumb) : '',
			'edit_url' => BASE_URL . 'edit-project.php?edit=' . base64_encode((string) $row['prj_id']),
		];
	}
	return $results;
}
