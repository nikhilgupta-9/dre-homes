<?php
/*
 * Shared by manage-project.php and ajax/manage-project-search.php.
 * Builds the filtered, sorted, paginated project list.
 * Expects includes/project-query.php (filters + helpers) to already be loaded.
 */
$has_facts = (bool) proj_query($conn, "SHOW TABLES LIKE 'tbl_ai_proj_facts'");
$legacy_price_sql = "IF(tp.proj_price REGEXP '^ *[0-9]+ *$' AND tp.proj_price + 0 > 0, tp.proj_price + 0, NULL)";
$price_sql = $has_facts ? "COALESCE(f.price_aed, $legacy_price_sql)" : $legacy_price_sql;
$from_sql = "FROM tbl_proj AS tp
	LEFT JOIN tbl_developer AS td ON tp.prj_dev_id = td.dev_id
	LEFT JOIN tbl_community AS tc ON tp.prj_comm_id = tc.comm_id"
	. ($has_facts ? " LEFT JOIN tbl_ai_proj_facts AS f ON f.prj_id = tp.prj_id" : '');
// Projects imported from GenieMap carry a badge (the table exists once its migration has been run).
$has_genie = (bool) proj_query($conn, "SHOW TABLES LIKE 'tbl_geniemap_import'");
$from_sql .= $has_genie ? ' LEFT JOIN tbl_geniemap_import AS gm ON gm.prj_id = tp.prj_id' : '';
$genie_sql = $has_genie ? 'gm.genie_id' : 'NULL';
// The SEO check of the project form keeps a score per project (once its migration has been run).
$has_seo = (bool) proj_query($conn, "SHOW TABLES LIKE 'tbl_proj_seo'");
$from_sql .= $has_seo ? ' LEFT JOIN tbl_proj_seo AS seo ON seo.prj_id = tp.prj_id' : '';
$seo_sql = $has_seo ? 'seo.seo_score' : 'NULL';
$seo_levels = ['good' => ['Good', "$seo_sql > 70"], 'ok' => ['OK', "$seo_sql BETWEEN 41 AND 70"], 'bad' => ['Needs improvement', "$seo_sql <= 40"], 'none' => ['Not checked yet', "$seo_sql IS NULL"]];

$missing_sql = [
	'price' => "$price_sql IS NULL",
	'bedrooms' => "(tp.proj_bedroom IS NULL OR TRIM(tp.proj_bedroom) = '')",
	'image' => "(tp.prj_thumb_img IS NULL OR TRIM(tp.prj_thumb_img) = '')",
	'meta' => "(tp.meta_description IS NULL OR TRIM(tp.meta_description) = '')",
];

// Every filter except the type, so the type tabs can show their own counts.
$where = ['tp.prj_type_id IN (1, 2, 3, 4)'];
$params = [];
if ($f_search !== '') {
	$like = '%' . addcslashes($f_search, '%_\\') . '%';
	$where[] = '(tp.proj_name LIKE ? OR tp.proj_name_on_card LIKE ? OR tp.proj_location LIKE ? OR tp.proj_slug LIKE ? OR td.dev_heading LIKE ? OR tc.comm_heading LIKE ?)';
	array_push($params, $like, $like, $like, $like, $like, $like);
}
if ($f_status !== '') {
	$where[] = $f_status === 'active' ? "tp.is_active = '1'" : "tp.is_active <> '1'";
}
if ($f_home !== '') {
	$where[] = "tp.proj_showonhomepage = '1'";
}
if ($f_source !== '' && $has_genie) {
	$where[] = 'gm.genie_id IS NOT NULL';
}
if ($f_seo !== '' && $has_seo) {
	$where[] = $seo_levels[$f_seo][1];
}
if ($f_sync !== '') {
	$where[] = $f_sync === 'pushed' ? 'tp.app_sync_status = 1' : '(tp.app_sync_status IS NULL OR tp.app_sync_status <> 1)';
}
if ($f_dev > 0) {
	$where[] = 'tp.prj_dev_id = ?';
	$params[] = $f_dev;
}
if ($f_comm > 0) {
	$where[] = 'tp.prj_comm_id = ?';
	$params[] = $f_comm;
}
if ($f_missing !== '') {
	$where[] = $missing_sql[$f_missing];
}
$where_sql = 'WHERE ' . implode(' AND ', $where);

$type_counts = [];
foreach (proj_query($conn, "SELECT tp.prj_type_id, COUNT(*) AS n $from_sql $where_sql GROUP BY tp.prj_type_id", $params) ?: [] as $row) {
	$type_counts[(int) $row['prj_type_id']] = (int) $row['n'];
}
$count_all = array_sum($type_counts);
// How many GenieMap projects there are in all, for the quick filter beside the tabs.
$genie_total = $has_genie ? (int) ((proj_query($conn, 'SELECT COUNT(*) AS n FROM tbl_geniemap_import gm JOIN tbl_proj tp ON tp.prj_id = gm.prj_id WHERE tp.prj_type_id IN (1, 2, 3, 4)') ?: [['n' => 0]])[0]['n']) : 0;
$total = $f_type ? ($type_counts[$f_type] ?? 0) : $count_all;

$list_where_sql = $where_sql . ($f_type ? ' AND tp.prj_type_id = ' . $f_type : '');
$order_sql = [
	'newest' => 'tp.tstp DESC, tp.prj_id DESC',
	'added' => 'tp.prj_id DESC',
	'name' => 'tp.proj_name ASC',
	'price_low' => "$price_sql IS NULL, $price_sql ASC",
	'price_high' => "$price_sql IS NULL, $price_sql DESC",
][$f_sort];

$pages = max(1, (int) ceil($total / $f_per_page));
$f_page = min($f_page, $pages);
$offset = ($f_page - 1) * $f_per_page;

$projects = proj_query(
	$conn,
	"SELECT tp.prj_id, tp.prj_type_id, tp.proj_name, tp.proj_name_on_card, tp.proj_location, tp.proj_slug, tp.proj_bedroom, tp.proj_price, tp.prj_thumb_img,
		tp.meta_description, tp.is_active, tp.proj_showonhomepage, tp.is_home_hero, tp.app_sync_status, tp.app_sync_date, tp.tstp,
		td.dev_heading AS proj_dev_name, tc.comm_heading AS proj_comm_name, $price_sql AS price_number, $genie_sql AS genie_id, $seo_sql AS seo_score
	$from_sql $list_where_sql ORDER BY $order_sql LIMIT ?, ?",
	array_merge($params, [$offset, $f_per_page])
) ?: [];

$stat = proj_query($conn, "SELECT COUNT(*) AS total, SUM(is_active = '1') AS active, SUM(is_active <> '1') AS hidden, SUM(proj_showonhomepage = '1') AS home, SUM(app_sync_status = 1) AS pushed FROM tbl_proj WHERE prj_type_id IN (1, 2, 3, 4)");
$stat = $stat ? $stat[0] : ['total' => 0, 'active' => 0, 'hidden' => 0, 'home' => 0, 'pushed' => 0];

$developer_options = proj_query($conn, "SELECT d.dev_id, d.dev_heading FROM tbl_developer d WHERE d.dev_id IN (SELECT prj_dev_id FROM tbl_proj WHERE prj_type_id IN (1, 2, 3, 4)) ORDER BY d.dev_heading") ?: [];
$community_options = proj_query($conn, "SELECT c.comm_id, c.comm_heading FROM tbl_community c WHERE c.comm_id IN (SELECT prj_comm_id FROM tbl_proj WHERE prj_type_id IN (1, 2, 3, 4)) ORDER BY c.comm_heading") ?: [];

$filters_on = $f_search !== '' || $f_status !== '' || $f_home !== '' || $f_sync !== '' || $f_source !== '' || $f_seo !== '' || $f_dev > 0 || $f_comm > 0 || $f_missing !== '';
