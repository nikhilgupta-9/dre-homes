<?php
/*
 * Shared by manage-developer.php and ajax/manage-developer-search.php.
 * Reads the filters from the address bar ($_GET) and defines the small helpers
 * both pages use to build the SQL and the "same page, different filter" links.
 * Expects $conn (db/config.php) to already be loaded by the caller.
 */
$dev_sorts = ['newest' => 'Newest first', 'name' => 'Name A-Z', 'projects' => 'Most projects first', 'home' => 'Homepage order'];
$dev_missing = ['logo' => 'Logo', 'short' => 'Short description', 'long' => 'Long description', 'meta' => 'Meta description'];

// ---------------------------------------------------------------
// Filters (all optional, all from the address bar)
// ---------------------------------------------------------------
$f_status = in_array($_GET['status'] ?? '', ['active', 'inactive'], true) ? $_GET['status'] : '';
$f_home = (($_GET['home'] ?? '') === '1' || ($_GET['filter_home'] ?? '') === '1') ? '1' : '';
$f_sync = in_array($_GET['sync'] ?? '', ['pushed', 'pending'], true) ? $_GET['sync'] : '';
$f_missing = isset($_GET['missing']) && is_string($_GET['missing']) && isset($dev_missing[$_GET['missing']]) ? $_GET['missing'] : '';
$f_sort = isset($_GET['sort']) && is_string($_GET['sort']) && isset($dev_sorts[$_GET['sort']]) ? $_GET['sort'] : 'newest';
$f_search = isset($_GET['q']) && is_string($_GET['q']) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$f_per_page = (int) ($_GET['per'] ?? 25);
if (!in_array($f_per_page, [25, 50, 100], true)) {
	$f_per_page = 25;
}
$f_page = max(1, (int) ($_GET['page'] ?? 1));

// Address of this page with the current filters, with some of them changed.
function dev_url(array $change = [])
{
	global $f_status, $f_home, $f_sync, $f_missing, $f_sort, $f_search, $f_per_page, $f_page;
	$params = array_merge([
		'status' => $f_status, 'home' => $f_home, 'sync' => $f_sync, 'missing' => $f_missing,
		'sort' => $f_sort === 'newest' ? '' : $f_sort, 'q' => $f_search,
		'per' => $f_per_page === 25 ? '' : $f_per_page, 'page' => $f_page === 1 ? '' : $f_page,
	], $change);
	$params = array_filter($params, function ($value) {
		return $value !== '' && $value !== null;
	});
	return BASE_URL . 'manage-developer.php' . ($params ? '?' . http_build_query($params) : '');
}

function dev_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function dev_query($conn, $sql, array $params = [])
{
	$stmt = mysqli_prepare($conn, $sql);
	if (!$stmt) {
		error_log('Developer query failed: ' . mysqli_error($conn));
		return false;
	}
	if ($params) {
		$types = '';
		foreach ($params as $param) {
			$types .= is_int($param) ? 'i' : 's';
		}
		mysqli_stmt_bind_param($stmt, $types, ...$params);
	}
	if (!mysqli_stmt_execute($stmt)) {
		error_log('Developer query failed: ' . mysqli_stmt_error($stmt));
		mysqli_stmt_close($stmt);
		return false;
	}
	$result = mysqli_stmt_get_result($stmt);
	mysqli_stmt_close($stmt);
	if ($result === false) {
		return true;
	}
	$rows = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}
