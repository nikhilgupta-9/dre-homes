<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

// Leads are personal data: nothing below runs unless an administrator is signed in.
if (empty($_SESSION['email_id'])) {
    exit;
}
if ((int) ($_SESSION['login_type'] ?? 0) !== 1) {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit;
}

// One entry per enquiry table: label in the address bar => table, key column, name shown, icon.
$lead_sources = [
    'Offplan' => ['table' => 'tbl_offplan_enq', 'key' => 'id', 'label' => 'Enquiry form', 'icon' => 'bi-envelope-paper'],
    'PDF Download' => ['table' => 'tbl_pdf_enquiry', 'key' => 'pdf_enq_id', 'label' => 'Downloads', 'icon' => 'bi-file-earmark-arrow-down'],
    'Gold Visa' => ['table' => 'tbl_goldvisa_enq', 'key' => 'id', 'label' => 'Golden visa', 'icon' => 'bi-gem'],
    'General Enquiry' => ['table' => 'tbl_enq', 'key' => 'enq_id', 'label' => 'Contact us', 'icon' => 'bi-chat-left-text'],
    'Book Valuation' => ['table' => 'tbl_book_val_enquiry', 'key' => 'book_val_id', 'label' => 'Valuation', 'icon' => 'bi-clipboard-check'],
    'Commercial Dev' => ['table' => 'tbl_comm_dev_enquiry', 'key' => 'comm_dev_id', 'label' => 'Community / developer', 'icon' => 'bi-building'],
    'Visa Enquiry' => ['table' => 'tbl_visa_enquiry', 'key' => 'visa_enq_id', 'label' => 'Visa', 'icon' => 'bi-passport'],
    'Newsletter' => ['table' => 'tbl_subscribe_frm', 'key' => 'subscribe_frm_id', 'label' => 'Newsletter', 'icon' => 'bi-newspaper'],
];

if (empty($_SESSION['leads_csrf'])) {
    $_SESSION['leads_csrf'] = bin2hex(random_bytes(16));
}

// ---------------------------------------------------------------
// Filters (all optional, all from the address bar)
// ---------------------------------------------------------------
$f_source = isset($_GET['source']) && isset($lead_sources[$_GET['source']]) ? $_GET['source'] : '';
$f_search = isset($_GET['q']) && is_string($_GET['q']) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$f_from = isset($_GET['from']) && is_string($_GET['from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['from']) ? $_GET['from'] : '';
$f_to = isset($_GET['to']) && is_string($_GET['to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['to']) ? $_GET['to'] : '';
$f_per_page = (int) ($_GET['per'] ?? 25);
if (!in_array($f_per_page, [25, 50, 100], true)) {
    $f_per_page = 25;
}
$f_page = max(1, (int) ($_GET['page'] ?? 1));

// Address of this page with the current filters, with some of them changed.
function leads_url(array $change = [])
{
    global $f_source, $f_search, $f_from, $f_to, $f_per_page, $f_page;
    $params = array_merge(['source' => $f_source, 'q' => $f_search, 'from' => $f_from, 'to' => $f_to, 'per' => $f_per_page === 25 ? '' : $f_per_page, 'page' => $f_page === 1 ? '' : $f_page], $change);
    $params = array_filter($params, function ($value) {
        return $value !== '' && $value !== null;
    });
    return BASE_URL . 'manage-leads.php' . ($params ? '?' . http_build_query($params) : '');
}

function leads_e($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function leads_query($conn, $sql, array $params = [])
{
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log('Leads query failed: ' . mysqli_error($conn));
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
        error_log('Leads query failed: ' . mysqli_stmt_error($stmt));
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

// ---------------------------------------------------------------
// Delete one lead (POST only, with a token, kept in the delete log)
// ---------------------------------------------------------------
$notice = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['delete_id'])) {
    $delete_source = is_string($_POST['delete_source'] ?? null) ? $_POST['delete_source'] : '';
    $delete_id = (int) $_POST['delete_id'];
    $token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['leads_csrf'], $_POST['csrf']);

    if ($token_ok && isset($lead_sources[$delete_source]) && $delete_id > 0) {
        $table = $lead_sources[$delete_source]['table'];
        $key = $lead_sources[$delete_source]['key'];
        $old = leads_query($conn, "SELECT * FROM $table WHERE $key = ?", [$delete_id]);
        if ($old) {
            $logger = new Logger($conn);
            $logger->log('DELETE', $table, $delete_id, $old[0], null, 'Lead deleted: ' . $delete_source . ' #' . $delete_id);
            $logger->archiveDeletedRecord($table, $delete_id, $old[0]);
            leads_query($conn, "DELETE FROM $table WHERE $key = ?", [$delete_id]);
        }
        header('Location: ' . leads_url(['deleted' => 1]));
        exit;
    }
    $notice = 'The lead was not deleted. Please reload the page and try again.';
}

// ---------------------------------------------------------------
// All enquiry tables as one list
// ---------------------------------------------------------------
$lead_union = "
    SELECT book_val_id AS id, name, email_id AS email, mobile AS phone, enq_about AS message,
           location AS d1, appointment_date AS d2, appointment_time AS d3, enq_type AS d4, totl_bedr AS d5, area_sqft AS d6,
           'Book Valuation' AS source, tstp AS date FROM tbl_book_val_enquiry
    UNION ALL
    SELECT comm_dev_id, name, email_id, phone, comment, page_name, '', '', '', '', '', 'Commercial Dev', tstp FROM tbl_comm_dev_enquiry
    UNION ALL
    SELECT enq_id, CONCAT(first_name, ' ', last_name), email_id, enquiry_phone, enq_comment, '', '', '', '', '', '', 'General Enquiry', tstp FROM tbl_enq
    UNION ALL
    SELECT id, name, email_id, number, message, '', '', '', '', '', '', 'Gold Visa', tstp FROM tbl_goldvisa_enq
    UNION ALL
    SELECT id, name, email, phone, message, prop_name, prop_location, prop_bedroom, developer, channel, leads_nature, 'Offplan', created_at FROM tbl_offplan_enq
    UNION ALL
    SELECT pdf_enq_id, name, email_id, phone, download_type, prop_name, prop_location, prop_bedroom, developer, channel, leads_nature, 'PDF Download', tstp FROM tbl_pdf_enquiry
    UNION ALL
    SELECT subscribe_frm_id, '', subscribe_frm_email, '', '', '', '', '', '', '', '', 'Newsletter', tstp FROM tbl_subscribe_frm
    UNION ALL
    SELECT visa_enq_id, username, email_id, visa_phone, message, '', '', '', '', '', '', 'Visa Enquiry', tstp FROM tbl_visa_enquiry";

// Search and dates apply to every tab; the source is added on top for the list itself.
$where = [];
$params = [];
if ($f_search !== '') {
    $like = '%' . addcslashes($f_search, '%_\\') . '%';
    $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR message LIKE ? OR d1 LIKE ? OR d4 LIKE ?)';
    array_push($params, $like, $like, $like, $like, $like, $like);
}
if ($f_from !== '') {
    $where[] = 'date >= ?';
    $params[] = $f_from . ' 00:00:00';
}
if ($f_to !== '') {
    $where[] = 'date <= ?';
    $params[] = $f_to . ' 23:59:59';
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$source_counts = [];
$count_rows = leads_query($conn, "SELECT source, COUNT(*) AS n FROM ($lead_union) l $where_sql GROUP BY source", $params);
foreach ($count_rows ?: [] as $row) {
    $source_counts[$row['source']] = (int) $row['n'];
}
$count_all = array_sum($source_counts);

$list_where = $where;
$list_params = $params;
if ($f_source !== '') {
    $list_where[] = 'source = ?';
    $list_params[] = $f_source;
}
$list_where_sql = $list_where ? 'WHERE ' . implode(' AND ', $list_where) : '';
$total = $f_source !== '' ? ($source_counts[$f_source] ?? 0) : $count_all;

// What a lead says about the property, with the right label for each kind of form.
function leads_details(array $row)
{
    if ($row['source'] === 'Book Valuation') {
        $labels = ['d1' => 'Location', 'd2' => 'Appointment date', 'd3' => 'Time', 'd4' => 'Type', 'd5' => 'Bedrooms', 'd6' => 'Area (sq ft)'];
    } elseif ($row['source'] === 'Commercial Dev') {
        $labels = ['d1' => 'Page'];
    } else {
        $labels = ['d1' => 'Property', 'd2' => 'Location', 'd3' => 'Unit type', 'd4' => 'Developer', 'd5' => 'Channel', 'd6' => 'Lead type'];
    }
    $details = [];
    foreach ($labels as $column => $label) {
        if (trim((string) $row[$column]) !== '') {
            $details[$label] = trim($row[$column]);
        }
    }
    return $details;
}

// ---------------------------------------------------------------
// Download the filtered list as a spreadsheet file
// ---------------------------------------------------------------
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $rows = leads_query($conn, "SELECT * FROM ($lead_union) l $list_where_sql ORDER BY date DESC", $list_params) ?: [];
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="dre-leads-' . date('Y-m-d') . '.csv"');
    header('Cache-Control: no-store');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    // A cell that starts like a formula is written as text, so a spreadsheet program does not run it.
    $safe = function ($value) {
        $value = (string) $value;
        // A plain phone number such as "+971 50 000 0000" is left as it is.
        if (preg_match('/^\+[\d\s().-]+$/', $value)) {
            return $value;
        }
        return preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
    };
    fputcsv($out, ['Date', 'Source', 'Name', 'Phone', 'Email', 'Message', 'Details']);
    foreach ($rows as $row) {
        $details = [];
        foreach (leads_details($row) as $label => $value) {
            $details[] = "$label: $value";
        }
        fputcsv($out, array_map($safe, [$row['date'], $lead_sources[$row['source']]['label'], $row['name'], $row['phone'], $row['email'], $row['message'], implode(' | ', $details)]));
    }
    fclose($out);
    exit;
}

$pages = max(1, (int) ceil($total / $f_per_page));
$f_page = min($f_page, $pages);
$offset = ($f_page - 1) * $f_per_page;
$leads = leads_query($conn, "SELECT * FROM ($lead_union) l $list_where_sql ORDER BY date DESC LIMIT ?, ?", array_merge($list_params, [$offset, $f_per_page])) ?: [];

$stat = leads_query($conn, "SELECT SUM(date >= CURDATE()) AS today, SUM(date >= CURDATE() - INTERVAL 6 DAY) AS week, SUM(date >= CURDATE() - INTERVAL 29 DAY) AS month, COUNT(*) AS total FROM ($lead_union) l");
$stat = $stat ? $stat[0] : ['today' => 0, 'week' => 0, 'month' => 0, 'total' => 0];

if (isset($_GET['deleted'])) {
    $notice = 'The lead was deleted. A copy is kept in the delete log.';
}
$filters_on = $f_search !== '' || $f_from !== '' || $f_to !== '';

function leads_phone_digits($phone)
{
    // "United Arab Emirates: +971 58 578 3994" -> 971585783994
    if (preg_match('/\+?\d[\d\s().-]{5,}\d/', (string) $phone, $match)) {
        return preg_replace('/\D+/', '', $match[0]);
    }
    return '';
}
function leads_when($datetime)
{
    $time = strtotime((string) $datetime);
    if (!$time) {
        return ['', ''];
    }
    $days = (int) floor((strtotime('today') - strtotime(date('Y-m-d', $time))) / 86400);
    $day = $days === 0 ? 'Today' : ($days === 1 ? 'Yesterday' : date('d M Y', $time));
    return [$day, date('h:i A', $time)];
}
?>
<!--begin::Body-->

<body id="kt_body"
    class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
    <!--begin::Main-->
    <!--begin::Header Mobile-->
    <div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
        <!--begin::Logo-->
        <a href="index.html">
            <img alt="Logo" src="<?= BASE_URL ?>assets/media/logos/logo-light.png" />
        </a>
        <!--end::Logo-->
        <!--begin::Toolbar-->
        <div class="d-flex align-items-center">
            <!--begin::Aside Mobile Toggle-->
            <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
                <span></span>
            </button>
            <!--end::Aside Mobile Toggle-->
            <!--begin::Header Menu Mobile Toggle-->
            <button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle">
                <span></span>
            </button>
            <!--end::Header Menu Mobile Toggle-->
            <!--begin::Topbar Mobile Toggle-->
            <button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle">
                <span class="svg-icon svg-icon-xl">
                    <!--begin::Svg Icon | path:<?= BASE_URL ?>assets/media/svg/icons/General/User.svg-->
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                        height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24" />
                            <path
                                d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                fill="#000000" fill-rule="nonzero" opacity="0.3" />
                            <path
                                d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                fill="#000000" fill-rule="nonzero" />
                        </g>
                    </svg>
                    <!--end::Svg Icon-->
                </span>
            </button>
            <!--end::Topbar Mobile Toggle-->
        </div>
        <!--end::Toolbar-->
    </div>
    <!--end::Header Mobile-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="d-flex flex-row flex-column-fluid page">
            <!--begin::Aside-->
            <?php include('includes/sidebar.php'); ?>
            <!--end::Aside-->
            <!--begin::Wrapper-->
            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                <!--begin::Header-->
                <div id="kt_header" class="header header-fixed">
                    <!--begin::Container-->
                    <div class="container-fluid d-flex align-items-stretch justify-content-between">
                        <!--begin::Header Menu Wrapper-->
                        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                            <!--begin::Header Menu-->
                            <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">

                            </div>
                            <!--end::Header Menu-->
                        </div>
                        <!--end::Header Menu Wrapper-->
                        <!--begin::Topbar-->
                        <div class="topbar">

                            <!--begin::User-->
                            <div class="topbar-item">
                                <div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2"
                                    id="kt_quick_user_toggle">
                                    <span
                                        class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                                    <span
                                        class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= $_SESSION['username'] ?></span>
                                    <span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
                                        <span
                                            class="symbol-label font-size-h5 font-weight-bold"><?= substr($_SESSION['username'], 0, 1) ?></span>
                                    </span>
                                </div>
                            </div>
                            <!--end::User-->
                        </div>
                        <!--end::Topbar-->
                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Header-->


                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid dre-dash-content" id="kt_content">
                    <!--begin::Entry-->
                    <div class="d-flex flex-column-fluid">
                        <!--begin::Container-->
                        <div class="container">

                            <!--begin::Leads-->
                            <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
                            <link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />

                            <div class="dre-dash dre-leads">

                                <!-- Title, numbers, download -->
                                <div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
                                    <div>
                                        <h1>Leads</h1>
                                        <p>Every enquiry from the website in one list, newest first.</p>
                                    </div>
                                    <div class="dre-leads-stats">
                                        <div><strong><?= number_format((int) $stat['today']) ?></strong><span>Today</span></div>
                                        <div><strong><?= number_format((int) $stat['week']) ?></strong><span>7 days</span></div>
                                        <div><strong><?= number_format((int) $stat['month']) ?></strong><span>30 days</span></div>
                                        <div><strong><?= number_format((int) $stat['total']) ?></strong><span>All time</span></div>
                                    </div>
                                    <div class="dre-actions">
                                        <a class="dre-primary" href="<?= leads_e(leads_url(['export' => 'csv', 'page' => ''])) ?>"><i class="bi bi-download"></i> Download <?= number_format($total) ?> as CSV</a>
                                    </div>
                                </div>

                                <?php if ($notice !== ''): ?>
                                    <div class="dre-leads-notice" role="status"><i class="bi bi-info-circle"></i> <?= leads_e($notice) ?></div>
                                <?php endif; ?>

                                <!-- Kind of enquiry -->
                                <nav class="dre-leads-tabs" aria-label="Kind of enquiry">
                                    <a href="<?= leads_e(leads_url(['source' => '', 'page' => ''])) ?>" class="<?= $f_source === '' ? 'is-on' : '' ?>">
                                        <i class="bi bi-collection"></i> All <b><?= number_format($count_all) ?></b>
                                    </a>
                                    <?php foreach ($lead_sources as $key => $source): ?>
                                        <a href="<?= leads_e(leads_url(['source' => $key, 'page' => ''])) ?>" class="<?= $f_source === $key ? 'is-on' : '' ?>">
                                            <i class="bi <?= $source['icon'] ?>"></i> <?= leads_e($source['label']) ?> <b><?= number_format($source_counts[$key] ?? 0) ?></b>
                                        </a>
                                    <?php endforeach; ?>
                                </nav>

                                <!-- Search and dates -->
                                <form method="get" action="<?= BASE_URL ?>manage-leads.php" class="dre-glass dre-leads-filter">
                                    <?php if ($f_source !== ''): ?><input type="hidden" name="source" value="<?= leads_e($f_source) ?>"><?php endif; ?>
                                    <label class="dre-leads-search">
                                        <span>Search</span>
                                        <i class="bi bi-search"></i>
                                        <input type="search" name="q" value="<?= leads_e($f_search) ?>" placeholder="Name, phone, email, property or developer" autocomplete="off">
                                    </label>
                                    <label>
                                        <span>From</span>
                                        <input type="date" name="from" value="<?= leads_e($f_from) ?>" max="<?= date('Y-m-d') ?>">
                                    </label>
                                    <label>
                                        <span>To</span>
                                        <input type="date" name="to" value="<?= leads_e($f_to) ?>" max="<?= date('Y-m-d') ?>">
                                    </label>
                                    <label>
                                        <span>Per page</span>
                                        <select name="per">
                                            <?php foreach ([25, 50, 100] as $option): ?>
                                                <option value="<?= $option ?>" <?= $f_per_page === $option ? 'selected' : '' ?>><?= $option ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                    <div class="dre-leads-filter-buttons">
                                        <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-funnel"></i> Apply</button>
                                        <?php if ($filters_on): ?>
                                            <a class="dre-btn" href="<?= leads_e(leads_url(['q' => '', 'from' => '', 'to' => '', 'page' => ''])) ?>">Clear</a>
                                        <?php endif; ?>
                                    </div>
                                </form>

                                <!-- The list -->
                                <div class="dre-glass dre-leads-list">
                                    <div class="dre-h">
                                        <h3>
                                            <?= number_format($total) ?> <?= $total === 1 ? 'lead' : 'leads' ?><?= $f_source !== '' ? ' &middot; ' . leads_e($lead_sources[$f_source]['label']) : '' ?>
                                            <small>
                                                <?php if ($total > 0): ?>
                                                    Showing <?= number_format($offset + 1) ?>&ndash;<?= number_format($offset + count($leads)) ?><?= $filters_on ? ' &middot; filtered' : '' ?>
                                                <?php else: ?>
                                                    Nothing matches<?= $filters_on ? ' these filters' : '' ?>
                                                <?php endif; ?>
                                            </small>
                                        </h3>
                                    </div>

                                    <?php if (!$leads): ?>
                                        <div class="dre-empty">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i><br>
                                            No leads to show.
                                            <?php if ($filters_on || $f_source !== ''): ?>
                                                <a href="<?= BASE_URL ?>manage-leads.php">Show all leads</a>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="dre-leads-table" role="table" aria-label="Leads">
                                            <div class="dre-leads-row dre-leads-head" role="row">
                                                <div role="columnheader">Lead</div>
                                                <div role="columnheader">Contact</div>
                                                <div role="columnheader">Interested in</div>
                                                <div role="columnheader">Received</div>
                                                <div role="columnheader"><span class="sr-only">Actions</span></div>
                                            </div>
                                            <?php foreach ($leads as $lead):
                                                $name = trim((string) $lead['name']);
                                                $email = trim((string) $lead['email']);
                                                $phone = trim((string) $lead['phone']);
                                                $message = trim((string) $lead['message']);
                                                $digits = leads_phone_digits($phone);
                                                $details = leads_details($lead);
                                                $is_ai = ($details['Channel'] ?? '') === 'AI Chat';
                                                list($day, $clock) = leads_when($lead['date']);
                                                $display_name = $name !== '' ? $name : ($email !== '' ? $email : 'No name');
                                                $headline = $details['Property'] ?? ($details['Location'] ?? ($details['Page'] ?? ''));
                                                unset($details['Channel']);
                                                $extra = $details;
                                                if (isset($extra['Property']) && $extra['Property'] === $headline) {
                                                    unset($extra['Property']);
                                                } elseif (isset($extra['Location']) && $extra['Location'] === $headline) {
                                                    unset($extra['Location']);
                                                } elseif (isset($extra['Page'])) {
                                                    unset($extra['Page']);
                                                }
                                            ?>
                                                <div class="dre-leads-row" role="row">
                                                    <div class="dre-leads-who" role="cell">
                                                        <span class="dre-avatar"><?= leads_e(mb_substr($display_name, 0, 1)) ?></span>
                                                        <div>
                                                            <strong><?= leads_e($display_name) ?></strong>
                                                            <span class="dre-pill<?= $is_ai ? ' dre-pill-ai' : '' ?>"><i class="bi <?= $is_ai ? 'bi-stars' : $lead_sources[$lead['source']]['icon'] ?>"></i> <?= $is_ai ? 'AI chat' : leads_e($lead_sources[$lead['source']]['label']) ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="dre-leads-contact" role="cell">
                                                        <?php if ($phone !== ''): ?>
                                                            <a href="tel:+<?= leads_e($digits) ?>" class="dre-leads-line"><i class="bi bi-telephone"></i> <?= leads_e($phone) ?></a>
                                                        <?php endif; ?>
                                                        <?php if ($email !== ''): ?>
                                                            <a href="mailto:<?= leads_e($email) ?>" class="dre-leads-line"><i class="bi bi-envelope"></i> <?= leads_e($email) ?></a>
                                                        <?php endif; ?>
                                                        <?php if ($phone === '' && $email === ''): ?>
                                                            <span class="dre-leads-muted">No contact details</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="dre-leads-interest" role="cell">
                                                        <?php if ($headline !== ''): ?>
                                                            <strong><?= leads_e($headline) ?></strong>
                                                        <?php endif; ?>
                                                        <?php if ($extra): ?>
                                                            <div class="dre-leads-tags">
                                                                <?php foreach ($extra as $label => $value): ?>
                                                                    <span title="<?= leads_e($label) ?>"><?= leads_e($label) ?>: <?= leads_e($value) ?></span>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if ($message !== ''): ?>
                                                            <?php if (mb_strlen($message) > 110): ?>
                                                                <details class="dre-leads-message">
                                                                    <summary><?= leads_e(mb_substr($message, 0, 110)) ?>&hellip; <em>more</em></summary>
                                                                    <p><?= nl2br(leads_e($message)) ?></p>
                                                                </details>
                                                            <?php else: ?>
                                                                <p class="dre-leads-message"><?= nl2br(leads_e($message)) ?></p>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                        <?php if ($headline === '' && !$extra && $message === ''): ?>
                                                            <span class="dre-leads-muted">&mdash;</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="dre-leads-date" role="cell">
                                                        <strong><?= leads_e($day) ?></strong>
                                                        <span><?= leads_e($clock) ?></span>
                                                    </div>
                                                    <div class="dre-leads-actions" role="cell">
                                                        <?php if ($digits !== ''): ?>
                                                            <a class="dre-icon-btn" href="tel:+<?= leads_e($digits) ?>" title="Call" aria-label="Call <?= leads_e($display_name) ?>"><i class="bi bi-telephone-fill"></i></a>
                                                            <a class="dre-icon-btn dre-icon-wa" href="https://wa.me/<?= leads_e($digits) ?>" target="_blank" rel="noopener" title="WhatsApp" aria-label="WhatsApp <?= leads_e($display_name) ?>"><i class="bi bi-whatsapp"></i></a>
                                                        <?php endif; ?>
                                                        <?php if ($email !== ''): ?>
                                                            <a class="dre-icon-btn" href="mailto:<?= leads_e($email) ?>" title="Email" aria-label="Email <?= leads_e($display_name) ?>"><i class="bi bi-envelope-fill"></i></a>
                                                        <?php endif; ?>
                                                        <form method="post" action="<?= leads_e(leads_url()) ?>" onsubmit="return confirm('Delete this lead? A copy is kept in the delete log.');">
                                                            <input type="hidden" name="csrf" value="<?= leads_e($_SESSION['leads_csrf']) ?>">
                                                            <input type="hidden" name="delete_id" value="<?= (int) $lead['id'] ?>">
                                                            <input type="hidden" name="delete_source" value="<?= leads_e($lead['source']) ?>">
                                                            <button type="submit" class="dre-icon-btn dre-icon-del" title="Delete" aria-label="Delete the lead from <?= leads_e($display_name) ?>"><i class="bi bi-trash3"></i></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <?php if ($pages > 1): ?>
                                            <nav class="dre-leads-pages" aria-label="Pages">
                                                <?php if ($f_page > 1): ?>
                                                    <a href="<?= leads_e(leads_url(['page' => $f_page - 1])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
                                                <?php endif; ?>
                                                <?php
                                                $shown = [];
                                                foreach ([1, 2, $f_page - 1, $f_page, $f_page + 1, $pages - 1, $pages] as $number) {
                                                    if ($number >= 1 && $number <= $pages) {
                                                        $shown[$number] = true;
                                                    }
                                                }
                                                ksort($shown);
                                                $previous = 0;
                                                foreach (array_keys($shown) as $number):
                                                    if ($number > $previous + 1): ?><span>&hellip;</span><?php endif; ?>
                                                    <a href="<?= leads_e(leads_url(['page' => $number])) ?>" class="<?= $number === $f_page ? 'is-on' : '' ?>" <?= $number === $f_page ? 'aria-current="page"' : '' ?>><?= $number ?></a>
                                                <?php $previous = $number;
                                                endforeach; ?>
                                                <?php if ($f_page < $pages): ?>
                                                    <a href="<?= leads_e(leads_url(['page' => $f_page + 1])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
                                                <?php endif; ?>
                                            </nav>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!--end::Leads-->

                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Entry-->
                </div>
                <!--end::Content-->
                <?php
                include('includes/footer-inner.php');
                ?>