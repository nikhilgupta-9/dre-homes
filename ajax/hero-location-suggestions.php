<?php
// Homepage hero search: location suggestions as the visitor types.
// Looks across live communities first, then plain project locations that
// aren't (yet) set up as a community, so every active listing can be found.
include '../db/config.php';

header('Content-Type: application/json; charset=utf-8');

$q = trim((string) ($_POST['q'] ?? ''));
if (mb_strlen($q) < 2) {
	echo json_encode(['items' => []]);
	exit;
}
$like = '%' . strtr($q, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
$items = [];
$seen = [];

$stmt = mysqli_prepare($conn, "SELECT c.comm_heading, c.comm_location, COUNT(p.prj_id) AS proj_count
	FROM tbl_community c
	LEFT JOIN tbl_proj p ON p.prj_comm_id = c.comm_id AND p.is_active = '1'
	WHERE c.is_active = '1' AND (c.comm_heading LIKE ? ESCAPE '!' OR c.comm_location LIKE ? ESCAPE '!')
	GROUP BY c.comm_id
	ORDER BY proj_count DESC, c.comm_heading ASC
	LIMIT 8");
mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
	$name = trim((string) $row['comm_heading']);
	$key = mb_strtolower($name);
	if ($name === '' || isset($seen[$key])) {
		continue;
	}
	$seen[$key] = true;
	$count = (int) $row['proj_count'];
	$items[] = [
		't' => $name,
		's' => $count > 0 ? ($count === 1 ? '1 listing' : "$count listings") : trim((string) $row['comm_location']),
		'g' => 'Communities',
		'ic' => 'pin',
	];
}
mysqli_stmt_close($stmt);

// Free-text locations on projects that aren't tied to a community record.
if (count($items) < 8) {
	$stmt = mysqli_prepare($conn, "SELECT p.proj_location, COUNT(*) AS proj_count
		FROM tbl_proj p
		WHERE p.is_active = '1' AND TRIM(p.proj_location) <> '' AND p.proj_location LIKE ? ESCAPE '!'
		GROUP BY p.proj_location
		ORDER BY proj_count DESC
		LIMIT 8");
	mysqli_stmt_bind_param($stmt, 's', $like);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	while (count($items) < 10 && ($row = mysqli_fetch_assoc($result))) {
		$name = trim((string) $row['proj_location']);
		$key = mb_strtolower($name);
		if ($name === '' || isset($seen[$key])) {
			continue;
		}
		$seen[$key] = true;
		$count = (int) $row['proj_count'];
		$items[] = [
			't' => $name,
			's' => $count === 1 ? '1 listing' : "$count listings",
			'g' => 'Areas',
			'ic' => 'pin',
		];
	}
	mysqli_stmt_close($stmt);
}

echo json_encode(['items' => array_slice($items, 0, 10)], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
