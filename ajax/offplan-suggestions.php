<?php
include '../db/config.php';

header('Content-Type: application/json; charset=utf-8');

$query = trim((string) ($_POST['q'] ?? ''));
if (mb_strlen($query) < 2) {
	echo json_encode(['items' => []]);
	exit;
}

$terms = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY);
$escapeLike = static function ($value) {
	return strtr((string) $value, ['!' => '!!', '%' => '!%', '_' => '!_']);
};
$termConditions = [];
$projectTermChecks = [];
$cardTermChecks = [];
$params = [];
foreach ($terms as $term) {
	$termConditions[] = "(p.proj_name LIKE ? ESCAPE '!' OR p.proj_name_on_card LIKE ? ESCAPE '!' OR d.dev_heading LIKE ? ESCAPE '!' OR p.proj_location LIKE ? ESCAPE '!' OR c.comm_location LIKE ? ESCAPE '!')";
	$projectTermChecks[] = "p.proj_name LIKE ? ESCAPE '!'";
	$cardTermChecks[] = "p.proj_name_on_card LIKE ? ESCAPE '!'";
	$like = '%' . $escapeLike($term) . '%';
	array_push($params, $like, $like, $like, $like, $like);
}

$exact = $query;
$starts = $escapeLike($query) . '%';
$contains = '%' . $escapeLike($query) . '%';
$sql = "SELECT p.proj_name, p.proj_name_on_card, p.proj_slug, p.prj_thumb_img,
		d.dev_heading, p.proj_location, c.comm_location
	FROM tbl_proj p
	LEFT JOIN tbl_developer d ON d.dev_id = p.prj_dev_id
	LEFT JOIN tbl_community c ON c.comm_id = p.prj_comm_id
	WHERE p.prj_type_id = 1 AND p.is_active = '1' AND " . implode(' AND ', $termConditions) . "
	ORDER BY CASE
    WHEN p.proj_name = ? THEN 0
		WHEN p.proj_name LIKE ? ESCAPE '!' THEN 1
		WHEN p.proj_name LIKE ? ESCAPE '!' THEN 2
		WHEN (" . implode(' AND ', $projectTermChecks) . ") THEN 3
		WHEN p.proj_name_on_card = ? THEN 4
		WHEN p.proj_name_on_card LIKE ? ESCAPE '!' THEN 5
		WHEN p.proj_name_on_card LIKE ? ESCAPE '!' THEN 6
		WHEN (" . implode(' AND ', $cardTermChecks) . ") THEN 7
		ELSE 8 END ASC, p.proj_name ASC, p.prj_id DESC
	LIMIT 12";
array_push($params, $exact, $starts, $contains);
foreach ($terms as $term) $params[] = '%' . $escapeLike($term) . '%';
$params[] = $exact;
$params[] = $starts;
$params[] = $contains;
foreach ($terms as $term) $params[] = '%' . $escapeLike($term) . '%';
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
	http_response_code(500);
	echo json_encode(['items' => []]);
	exit;
}

$bind = [$stmt, str_repeat('s', count($params))];
foreach ($params as $index => $value) {
	$bind[] = &$params[$index];
}
call_user_func_array('mysqli_stmt_bind_param', $bind);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$items = [];
while ($row = mysqli_fetch_assoc($result)) {
	$name = trim((string) ($row['proj_name'] ?? ''));
	$cardName = trim((string) ($row['proj_name_on_card'] ?? ''));
	$developer = trim((string) ($row['dev_heading'] ?? ''));
	$area = trim((string) ($row['proj_location'] ?? ''));
	if ($area === '') $area = trim((string) ($row['comm_location'] ?? ''));
	$details = [];
	if ($cardName !== '' && strcasecmp($cardName, $name) !== 0) $details[] = 'Card name: ' . $cardName;
	if ($developer !== '') $details[] = $developer;
	if ($area !== '') $details[] = $area;
	$thumb = trim(explode(',', (string) ($row['prj_thumb_img'] ?? ''))[0]);
	$items[] = [
		't' => $name !== '' ? $name : $cardName,
		's' => implode(' · ', $details),
		'g' => 'Projects',
		'img' => $thumb !== '' ? BASE_URL_IMG . 'project/thumb/' . rawurlencode($thumb) : '',
		'href' => BASE_URL . 'property/' . rawurlencode(trim((string) ($row['proj_slug'] ?? ''))),
	];
}
mysqli_stmt_close($stmt);

echo json_encode(['items' => $items], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
