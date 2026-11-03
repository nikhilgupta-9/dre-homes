<?php
/*
 * Shared by manage-developer.php and ajax/manage-developer-search.php.
 * Builds the filtered, sorted, paginated developer list.
 * Expects includes/developer-query.php (filters + helpers) to already be loaded.
 */
$missing_sql = [
	'logo' => "TRIM(d.dev_img) = ''",
	'short' => "TRIM(d.dev_short_descp) = ''",
	'long' => "TRIM(d.dev_long_descp) = ''",
	'meta' => "TRIM(d.meta_description) = ''",
];
$where = ['1 = 1'];
$params = [];
if ($f_search !== '') {
	$like = '%' . addcslashes($f_search, '%_\\') . '%';
	$where[] = '(d.dev_heading LIKE ? OR d.dev_slug LIKE ? OR d.dev_address LIKE ? OR d.dev_email LIKE ?)';
	array_push($params, $like, $like, $like, $like);
}
if ($f_status !== '') {
	$where[] = $f_status === 'active' ? "d.is_active = '1'" : "d.is_active <> '1'";
}
if ($f_home !== '') {
	$where[] = "d.dev_showonhomepage = '1'";
}
if ($f_sync !== '') {
	$where[] = $f_sync === 'pushed' ? 'd.app_sync_status = 1' : '(d.app_sync_status IS NULL OR d.app_sync_status <> 1)';
}
if ($f_missing !== '') {
	$where[] = $missing_sql[$f_missing];
}
$where_sql = 'WHERE ' . implode(' AND ', $where);

$counted = dev_query($conn, "SELECT COUNT(*) AS n FROM tbl_developer AS d $where_sql", $params);
$total = $counted ? (int) $counted[0]['n'] : 0;

$projects_sql = "(SELECT COUNT(*) FROM tbl_proj AS p WHERE p.prj_dev_id = d.dev_id)";
$order_sql = [
	'newest' => 'd.dev_id DESC',
	'name' => 'd.dev_heading ASC',
	'projects' => 'project_count DESC, d.dev_heading ASC',
	'home' => "d.dev_showonhomepage = '1' DESC, d.dev_order_no IS NULL, d.dev_order_no ASC, d.dev_heading ASC",
][$f_sort];

$pages = max(1, (int) ceil($total / $f_per_page));
$f_page = min($f_page, $pages);
$offset = ($f_page - 1) * $f_per_page;

$developers = dev_query(
	$conn,
	"SELECT d.dev_id, d.dev_img, d.dev_heading, d.dev_short_descp, d.dev_long_descp, d.meta_description, d.dev_slug, d.dev_showonhomepage,
		d.dev_order_no, d.is_active, d.app_sync_status, d.app_sync_date, $projects_sql AS project_count
	FROM tbl_developer AS d $where_sql ORDER BY $order_sql LIMIT ?, ?",
	array_merge($params, [$offset, $f_per_page])
) ?: [];

$stat = dev_query($conn, "SELECT COUNT(*) AS total, SUM(is_active = '1') AS active, SUM(is_active <> '1') AS hidden, SUM(dev_showonhomepage = '1') AS home, SUM(app_sync_status = 1) AS pushed FROM tbl_developer");
$stat = $stat ? $stat[0] : ['total' => 0, 'active' => 0, 'hidden' => 0, 'home' => 0, 'pushed' => 0];

$filters_on = $f_search !== '' || $f_status !== '' || $f_home !== '' || $f_sync !== '' || $f_missing !== '';
