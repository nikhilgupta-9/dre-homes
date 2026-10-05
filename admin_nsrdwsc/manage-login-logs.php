<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);

// Only admin can access
if ($_SESSION['login_type'] != 1) {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit;
}

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total_logins,
    SUM(CASE WHEN login_status = 'SUCCESS' THEN 1 ELSE 0 END) as successful_logins,
    SUM(CASE WHEN login_status = 'FAILED' THEN 1 ELSE 0 END) as failed_logins,
    COUNT(DISTINCT user_email) as unique_users,
    MAX(login_time) as last_login
FROM tbl_login_logs";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Get login attempts in last 24 hours
$last_24hrs_query = "SELECT COUNT(*) as attempts FROM tbl_login_logs WHERE login_time > DATE_SUB(NOW(), INTERVAL 24 HOUR) AND login_status = 'FAILED'";
$last_24hrs_result = mysqli_query($conn, $last_24hrs_query);
$failed_24hrs = mysqli_fetch_assoc($last_24hrs_result);

// Get unique users for filter dropdown
$users_query = mysqli_query($conn, "SELECT DISTINCT user_email FROM tbl_login_logs WHERE user_email IS NOT NULL AND user_email != '' ORDER BY user_email ASC");

// Clean up stale sessions
$logger->cleanupStaleSessions();
?>

<!--begin::Body-->

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
    <!--begin::Main-->
    <!--begin::Header Mobile-->
    <div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
        <a href="index.html">
            <img alt="Logo" src="<?= BASE_URL ?>assets/media/logos/logo-light.png" />
        </a>
        <div class="d-flex align-items-center">
            <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
                <span></span>
            </button>
            <button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle">
                <span></span>
            </button>
            <button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle">
                <span class="svg-icon svg-icon-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24" />
                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                        </g>
                    </svg>
                </span>
            </button>
        </div>
    </div>
    <!--end::Header Mobile-->

    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-row flex-column-fluid page">
            <?php include('includes/sidebar.php'); ?>

            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                <div id="kt_header" class="header header-fixed">
                    <div class="container-fluid d-flex align-items-stretch justify-content-between">
                        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                            <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default"></div>
                        </div>
                        <div class="topbar">
                            <div class="topbar-item">
                                <div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
                                    <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                                    <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= htmlspecialchars($_SESSION['username']) ?></span>
                                    <span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
                                        <span class="symbol-label font-size-h5 font-weight-bold"><?= substr($_SESSION['username'], 0, 1) ?></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="d-flex flex-column-fluid">
                        <div class="container">

                            <!-- Statistics Cards -->
                            <div class="row mb-5">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body p-3">
                                            <h5 class="mb-1">Total Logins</h5>
                                            <h3 class="mb-0"><?= number_format($stats['total_logins']) ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body p-3">
                                            <h5 class="mb-1">Successful</h5>
                                            <h3 class="mb-0"><?= number_format($stats['successful_logins']) ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body p-3">
                                            <h5 class="mb-1">Failed</h5>
                                            <h3 class="mb-0"><?= number_format($stats['failed_logins']) ?></h3>
                                            <small class="text-white-50">Last 24h: <?= $failed_24hrs['attempts'] ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body p-3">
                                            <h5 class="mb-1">Unique Users</h5>
                                            <h3 class="mb-0"><?= number_format($stats['unique_users']) ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Active Sessions Card -->
                            <?php
                            $active_sessions = $logger->getActiveSessions();
                            if (count($active_sessions) > 0):
                            ?>
                                <div class="card card-custom gutter-b mb-5">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h3 class="card-label">
                                                <i class="fa fa-circle text-success"></i> Active Sessions
                                                <span class="badge badge-success ml-2"><?= count($active_sessions) ?> Active</span>
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover table-striped">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>User Email</th>
                                                        <th>IP Address</th>
                                                        <th>Login Time</th>
                                                        <th>Current Duration</th>
                                                        <th>Session ID</th>
                                                    </tr>
                                                </thead>

                                                <tbody>

                                                    <?php if (!empty($active_sessions)) : ?>

                                                        <?php foreach ($active_sessions as $session): ?>

                                                            <tr>

                                                                <!-- User Email -->
                                                                <td>
                                                                    <span class="font-weight-bold text-dark">
                                                                        <?= htmlspecialchars($session['user_email']) ?>
                                                                    </span>
                                                                </td>

                                                                <!-- IP -->
                                                                <td>
                                                                    <code>
                                                                        <?= htmlspecialchars($session['user_ip']) ?>
                                                                    </code>
                                                                </td>

                                                                <!-- Login Time -->
                                                                <td>
                                                                    <span class="text-success font-weight-bold">
                                                                        <?= date('d-m-Y', strtotime($session['login_time'])) ?>
                                                                    </span>

                                                                    <br>

                                                                    <small class="text-muted">
                                                                        <?= date('h:i:s A', strtotime($session['login_time'])) ?>
                                                                    </small>
                                                                </td>

                                                                <!-- Current Duration -->
                                                                <td>
                                                                    <span class="badge badge-warning px-3 py-2">
                                                                        <?= $session['current_duration_formatted'] ?>
                                                                    </span>
                                                                </td>

                                                                <!-- Session ID -->
                                                                <td>
                                                                    <small class="text-muted"
                                                                        title="<?= htmlspecialchars($session['session_id']) ?>">

                                                                        <?= substr($session['session_id'], 0, 20) ?>...

                                                                    </small>
                                                                </td>

                                                            </tr>

                                                        <?php endforeach; ?>

                                                    <?php else : ?>

                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">
                                                                No active sessions found
                                                            </td>
                                                        </tr>

                                                    <?php endif; ?>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Login Logs Table Card -->
                            <div class="card card-custom gutter-b">
                                <div class="card-header flex-wrap py-3 d-flex justify-content-between align-items-center">
                                    <div class="card-title">
                                        <h3 class="card-label mb-0">Login Logs</h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <div class="dropdown dropdown-inline mr-2">
                                            <button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown">
                                                <i class="bi bi-download text-light"></i> Export
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                <ul class="navi flex-column navi-hover py-2">
                                                    <li class="navi-item">
                                                        <a href="#" class="navi-link" id="exportCSV">
                                                            <i class="bi bi-filetype-csv text-primary mr-2"></i> CSV File
                                                        </a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-light-primary font-weight-bolder mr-2" onclick="window.location.reload();">
                                            <i class="bi bi-arrow-clockwise text-light"></i> Refresh
                                        </button>
                                        <button type="button" class="btn btn-danger font-weight-bolder" id="clearLogsBtn">
                                            <i class="bi bi-trash"></i> Clear Logs
                                        </button>
                                    </div>
                                </div>

                                <!-- Filter Section -->
                                <div class="card-body pb-0">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Status</label>
                                            <select id="statusFilter" class="form-control">
                                                <option value="">All</option>
                                                <option value="SUCCESS">Success</option>
                                                <option value="FAILED">Failed</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Filter by User:</label>
                                            <select id="userFilter" class="form-control">
                                                <option value="">All Users</option>
                                                <?php
                                                mysqli_data_seek($users_query, 0);
                                                while ($user = mysqli_fetch_assoc($users_query)) {
                                                    $user_email = htmlspecialchars($user['user_email']);
                                                    echo "<option value=\"$user_email\">$user_email</option> - manage-login-logs.php:284";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" id="start_date" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">End Date</label>
                                            <input type="date" id="end_date" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">&nbsp;</label>
                                            <button class="btn btn-secondary w-100" id="resetFilters">
                                                <i class="bi bi-eraser"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="login_logs_table" class="table table-bordered dataTable" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="3%"><input type="checkbox" id="selectAll"></th>
                                                    <th width="5%">S No.</th>
                                                    <th width="15%">User Email</th>
                                                    <th width="8%">Status</th>
                                                    <th width="12%">Failure Reason</th>
                                                    <th width="10%">IP Address & Location</th>
                                                    <th width="12%">User Agent</th>
                                                    <th width="12%">Login Time</th>
                                                    <th width="12%">Logout Time</th>
                                                    <th width="8%">Duration</th>
                                                    <th width="5%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $key = 1;
                                                $query = mysqli_query($conn, "SELECT * FROM tbl_login_logs ORDER BY login_time DESC");
                                                while ($row = mysqli_fetch_array($query)) {
                                                    // Calculate session duration
                                                    $duration = '-';
                                                    if (!empty($row['session_duration'])) {
                                                        $seconds = $row['session_duration'];
                                                        $hours = floor($seconds / 3600);
                                                        $minutes = floor(($seconds % 3600) / 60);
                                                        $secs = $seconds % 60;
                                                        $duration = ($hours ? $hours . 'h ' : '') . ($minutes ? $minutes . 'm ' : '') . $secs . 's';
                                                    } elseif (!empty($row['login_time']) && !empty($row['logout_time'])) {
                                                        $login = new DateTime($row['login_time']);
                                                        $logout = new DateTime($row['logout_time']);
                                                        $interval = $login->diff($logout);
                                                        $duration = $interval->format('%hh %im %ss');
                                                    }
                                                ?>
                                                    <tr data-log-id="<?= $row['login_log_id'] ?>"
                                                        data-status="<?= $row['login_status'] ?>"
                                                        data-email="<?= strtolower($row['user_email']) ?>"
                                                        data-login-time="<?= $row['login_time'] ?>">
                                                        <td class="text-center">
                                                            <input type="checkbox" class="logCheckbox" value="<?= $row['login_log_id'] ?>">
                                                        </td>
                                                        <td><?= $key++ ?></td>
                                                        <td>
                                                            <strong><?= htmlspecialchars($row['user_email']) ?></strong>
                                                            <br>
                                                            <small class="text-muted">ID: <?= $row['login_log_id'] ?></small>
                                                        </td>
                                                        <td>
                                                            <?php if ($row['login_status'] == 'SUCCESS'): ?>
                                                                <span class="badge rounded-pill bg-success  text-light">
                                                                    <i class="fa fa-check-circle text-light"></i> SUCCESS
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge rounded-pill bg-danger text-light">
                                                                    <i class="fa fa-times-circle text-light"></i> FAILED
                                                                </span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($row['failure_reason'])): ?>
                                                                <span class="text-danger">
                                                                    <i class="fa fa-exclamation-triangle text-warning"></i>
                                                                    <?= htmlspecialchars($row['failure_reason']) ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <code><?= htmlspecialchars($row['user_ip']) ?></code>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fa fa-map-marker text-primary"></i>
                                                                <span class="ip-location" data-ip="<?= htmlspecialchars($row['user_ip']) ?>">Loading...</span>
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary btn-sm view-user-agent w-100 mb-1" data-agent="<?= htmlspecialchars($row['user_agent']) ?>">
                                                                View
                                                            </button>
                                                            <small class="text-muted d-block" title="<?= htmlspecialchars($row['user_agent']) ?>">
                                                                <?= htmlspecialchars(substr($row['user_agent'], 0, 40)) ?>...
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <i class="fa fa-sign-in text-success"></i>
                                                            <span class="text-dark"><?= date('d-m-Y H:i:s', strtotime($row['login_time'])) ?></span>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($row['logout_time'])): ?>
                                                                <i class="fa fa-sign-out text-warning"></i>
                                                                <span class="text-dark"><?= date('d-m-Y H:i:s', strtotime($row['logout_time'])) ?></span>
                                                            <?php else: ?>
                                                                <span class="badge badge-warning">Active</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($duration != '-'): ?>
                                                                <span class="badge badge-secondary">
                                                                    <i class="fa fa-hourglass-half text-dark"></i> <?= $duration ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm" role="group">

                                                                <button type="button" class="btn btn-danger delete-single-log" data-id="<?= $row['login_log_id'] ?>" title="Delete">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include('includes/footer-inner.php'); ?>

                <!-- Bulk Actions Bar -->
                <div id="bulkActionsBar" class="fixed-bottom mb-3" style="display: none; z-index: 1000;">
                    <div class="container">
                        <div class="alert alert-primary d-flex justify-content-between align-items-center mb-0 shadow">
                            <div class="text-light">
                                <i class="fa fa-check-circle text-success mr-2"></i>
                                <strong><span id="selectedCount">0</span></strong> log(s) selected
                            </div>
                            <div>
                                <button class="btn btn-sm btn-danger" id="bulkDeleteBtn">
                                    <i class="fa fa-trash"></i> Delete Selected
                                </button>
                                <button class="btn btn-sm btn-secondary ml-2" id="clearSelectionBtn">
                                    <i class="fa fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered" role="document">

                        <div class="modal-content">

                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="fa fa-trash"></i> Confirm Delete
                                </h5>

                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">

                                <p id="confirmDeleteMessage" class="mb-0"></p>

                                <small class="text-danger">
                                    This action cannot be undone.
                                </small>

                            </div>

                            <div class="modal-footer">

                                <button type="button"
                                    class="btn btn-secondary"
                                    data-dismiss="modal">

                                    Cancel

                                </button>

                                <button type="button"
                                    class="btn btn-danger"
                                    id="confirmDeleteBtn">

                                    <i class="fa fa-trash"></i> Delete

                                </button>

                            </div>

                        </div>

                    </div>
                </div>

                <!-- User Agent Modal -->
                <div class="modal fade" id="userAgentModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="fa fa-desktop"></i> User Agent Details</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label><strong>Full User Agent String:</strong></label>
                                    <textarea class="form-control" id="userAgentString" rows="5" readonly style="font-family: monospace;"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <strong><i class="fa fa-windows"></i> Operating System:</strong>
                                                <p id="osInfo" class="mb-0 mt-2"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <strong><i class="fa fa-chrome"></i> Browser:</strong>
                                                <p id="browserInfo" class="mb-0 mt-2"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clear Logs Modal -->
                <div class="modal fade" id="clearLogsModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Clear Login Logs</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete login logs?</p>
                                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone!</p>
                                <div class="form-group">
                                    <label>Delete logs older than (days):</label>
                                    <select id="delete_days" class="form-control">
                                        <option value="all">All logs</option>
                                        <option value="7">Older than 7 days</option>
                                        <option value="30">Older than 30 days</option>
                                        <option value="90">Older than 90 days</option>
                                        <option value="180">Older than 180 days</option>
                                        <option value="365">Older than 365 days</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmClearLogs">Delete Logs</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        var table = $('#login_logs_table').DataTable({
                            "pageLength": 25,
                            "order": [
                                [7, 'desc']
                            ],
                            "language": {
                                "search": "Search:",
                                "lengthMenu": "Show _MENU_ entries",
                                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                                "paginate": {
                                    "first": "First",
                                    "last": "Last",
                                    "next": "Next",
                                    "previous": "Previous"
                                }
                            },
                            "columnDefs": [{
                                    "orderable": false,
                                    "targets": [0, 10]
                                },
                                {
                                    "width": "3%",
                                    "targets": 0
                                },
                                {
                                    "width": "5%",
                                    "targets": 1
                                },
                                {
                                    "width": "15%",
                                    "targets": 2
                                },
                                {
                                    "width": "8%",
                                    "targets": 3
                                },
                                {
                                    "width": "12%",
                                    "targets": 4
                                },
                                {
                                    "width": "10%",
                                    "targets": 5
                                },
                                {
                                    "width": "12%",
                                    "targets": 6
                                },
                                {
                                    "width": "12%",
                                    "targets": 7
                                },
                                {
                                    "width": "12%",
                                    "targets": 8
                                },
                                {
                                    "width": "8%",
                                    "targets": 9
                                },
                                {
                                    "width": "5%",
                                    "targets": 10
                                }
                            ]
                        });

                        // Custom filter function
                        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                            var status = $('#statusFilter').val();
                            var selectedUser = $('#userFilter').val();
                            var startDate = $('#start_date').val();
                            var endDate = $('#end_date').val();

                            var row = table.row(dataIndex).node();
                            var rowStatus = $(row).data('status');
                            var rowEmail = $(row).data('email');
                            var rowLoginTime = $(row).data('login-time');

                            if (status !== '' && rowStatus !== status) return false;
                            if (selectedUser !== '' && rowEmail !== selectedUser.toLowerCase()) return false;

                            if (startDate !== '') {
                                var startDateObj = new Date(startDate);
                                var rowDateObj = new Date(rowLoginTime);
                                startDateObj.setHours(0, 0, 0, 0);
                                if (rowDateObj < startDateObj) return false;
                            }

                            if (endDate !== '') {
                                var endDateObj = new Date(endDate);
                                var rowDateObj = new Date(rowLoginTime);
                                endDateObj.setHours(23, 59, 59, 999);
                                if (rowDateObj > endDateObj) return false;
                            }

                            return true;
                        });

                        // Filter events
                        $('#statusFilter, #userFilter, #start_date, #end_date').on('change', function() {
                            table.draw();
                        });

                        $('#resetFilters').on('click', function() {
                            $('#statusFilter').val('');
                            $('#userFilter').val('');
                            $('#start_date').val('');
                            $('#end_date').val('');
                            table.draw();
                        });

                        // Select All functionality
                        $('#selectAll, #selectAllFooter').on('change', function() {
                            var isChecked = $(this).prop('checked');
                            $('.logCheckbox').prop('checked', isChecked);
                            updateBulkActionsBar();
                        });

                        $(document).on('change', '.logCheckbox', function() {
                            updateBulkActionsBar();
                            var allChecked = $('.logCheckbox:checked').length === $('.logCheckbox').length;
                            $('#selectAll, #selectAllFooter').prop('checked', allChecked);
                        });

                        function updateBulkActionsBar() {
                            var selected = $('.logCheckbox:checked').length;
                            $('#selectedCount').text(selected);
                            if (selected > 0) {
                                $('#bulkActionsBar').fadeIn();
                            } else {
                                $('#bulkActionsBar').fadeOut();
                            }
                        }

                        // Bulk Delete
                        // Bulk Delete Logs
                        $('#bulkDeleteBtn').on('click', function() {

                            var selectedIds = [];

                            $('.logCheckbox:checked').each(function() {
                                selectedIds.push($(this).val());
                            });

                            // No selection
                            if (selectedIds.length === 0) {
                                toastr.warning('Please select at least one log.');
                                return;
                            }

                            // Set confirmation message
                            $('#confirmDeleteMessage').html(
                                'Are you sure you want to delete <strong>' +
                                selectedIds.length +
                                '</strong> selected log(s)?'
                            );

                            // Open Bootstrap 4 Modal
                            $('#deleteConfirmModal').modal('show');

                            // Remove old click event to prevent duplicate AJAX
                            $('#confirmDeleteBtn').off('click');

                            // Confirm delete
                            $('#confirmDeleteBtn').on('click', function() {

                                var $btn = $(this);

                                $btn.prop('disabled', true)
                                    .html('<i class="fa fa-spinner fa-spin"></i> Deleting...');

                                $.ajax({
                                    url: '<?= BASE_URL ?>ajax/bulk-delete-logs.php',
                                    type: 'POST',
                                    data: {
                                        ids: selectedIds,
                                        type: 'login'
                                    },
                                    dataType: 'json',

                                    success: function(response) {

                                        if (response.status === 'success') {

                                            toastr.success(response.message);

                                            setTimeout(function() {
                                                location.reload();
                                            }, 1200);

                                        } else {

                                            toastr.error(response.message || 'Delete failed');

                                        }
                                    },

                                    error: function(xhr, status, error) {

                                        console.log(error);

                                        toastr.error('Something went wrong while deleting logs.');

                                    },

                                    complete: function() {

                                        $btn.prop('disabled', false)
                                            .html('<i class="fa fa-trash"></i> Delete');

                                        $('#deleteConfirmModal').modal('hide');
                                    }
                                });

                            });

                        });
                        $('#clearSelectionBtn').on('click', function() {
                            $('.logCheckbox').prop('checked', false);
                            $('#selectAll, #selectAllFooter').prop('checked', false);
                            updateBulkActionsBar();
                        });

                        // Single Delete
                        $(document).on('click', '.delete-single-log', function() {

                            var logId = $(this).data('id');

                            if (confirm('Are you sure you want to delete this log entry?')) {

                                $.ajax({
                                    url: '<?= BASE_URL ?>ajax/delete-single-log.php',
                                    type: 'POST',
                                    data: {
                                        id: logId,
                                        type: 'login'
                                    },
                                    dataType: 'json',

                                    success: function(response) {

                                        if (response.status === 'success') {

                                            toastr.success(response.message);

                                            setTimeout(function() {
                                                location.reload();
                                            }, 1000);

                                        } else {

                                            toastr.error(response.message);
                                        }
                                    },

                                    error: function() {

                                        toastr.error('Failed to delete log');
                                    }
                                });
                            }
                        });

                        // View Log Details
                        $(document).on('click', '.view-log-details', function() {
                            var log = $(this).data('log');

                            var content = '<div class="row">' +
                                '<div class="col-md-6"><strong>User Email:</strong><br>' + (log.user_email || '-') + '</div>' +
                                '<div class="col-md-6"><strong>Status:</strong><br><span class="badge badge-' + (log.login_status === 'SUCCESS' ? 'success' : 'danger') + '">' + (log.login_status || '-') + '</span></div>' +
                                '<div class="col-md-12"><hr></div>' +
                                '<div class="col-md-6"><strong>IP Address:</strong><br><code>' + (log.user_ip || '-') + '</code></div>' +
                                '<div class="col-md-6"><strong>Session ID:</strong><br><small>' + (log.session_id || '-') + '</small></div>' +
                                '<div class="col-md-6"><strong>Login Time:</strong><br>' + (log.login_time || '-') + '</div>' +
                                '<div class="col-md-6"><strong>Logout Time:</strong><br>' + (log.logout_time || 'Active Session') + '</div>' +
                                (log.session_duration ? '<div class="col-md-6"><strong>Session Duration:</strong><br>' + log.session_duration + ' seconds</div>' : '') +
                                (log.failure_reason ? '<div class="col-md-12"><hr><strong>Failure Reason:</strong><br><span class="text-danger">' + log.failure_reason + '</span></div>' : '') +
                                '<div class="col-md-12"><hr><strong>User Agent:</strong><br><small>' + (log.user_agent || '-') + '</small></div>' +
                                '</div>';

                            bootbox.dialog({
                                title: '<i class="fa fa-info-circle"></i> Login Log Details #' + (log.login_log_id || ''),
                                message: content,
                                buttons: {
                                    close: {
                                        label: 'Close',
                                        className: 'btn-secondary'
                                    }
                                },
                                size: 'large'
                            });
                        });

                        // View User Agent
                        $(document).on('click', '.view-user-agent', function() {
                            var userAgent = $(this).data('agent');
                            $('#userAgentString').val(userAgent);

                            var os = 'Unknown';
                            var browser = 'Unknown';

                            if (userAgent.indexOf('Windows') > -1) os = 'Windows';
                            else if (userAgent.indexOf('Mac') > -1) os = 'Mac OS';
                            else if (userAgent.indexOf('Linux') > -1) os = 'Linux';
                            else if (userAgent.indexOf('Android') > -1) os = 'Android';
                            else if (userAgent.indexOf('iPhone') > -1) os = 'iOS';

                            if (userAgent.indexOf('Chrome') > -1 && userAgent.indexOf('Edg') === -1) browser = 'Chrome';
                            else if (userAgent.indexOf('Firefox') > -1) browser = 'Firefox';
                            else if (userAgent.indexOf('Safari') > -1 && userAgent.indexOf('Chrome') === -1) browser = 'Safari';
                            else if (userAgent.indexOf('Edg') > -1) browser = 'Edge';
                            else if (userAgent.indexOf('MSIE') > -1 || userAgent.indexOf('Trident') > -1) browser = 'Internet Explorer';

                            $('#osInfo').html('<i class="fa fa-"></i> ' + os);
                            $('#browserInfo').html('<i class="fa fa-"></i> ' + browser);

                            $('#userAgentModal').modal('show');
                        });

                        // IP Location
                        const ipCache = {};

                        $('.ip-location').each(function() {

                            var ip = $(this).data('ip');
                            var $this = $(this);

                            // Prevent duplicate API calls
                            if (ipCache[ip]) {
                                $this.text(ipCache[ip]);
                                return;
                            }

                            // Skip localhost/private IPs
                            if (
                                !ip ||
                                ip === '::1' ||
                                ip === '127.0.0.1' ||
                                ip === 'localhost'
                            ) {
                                $this.text('Localhost');
                                return;
                            }

                            // Show loading text
                            $this.text('Loading...');

                            $.ajax({
                                url: 'https://ipapi.co/' + ip + '/json/',
                                method: 'GET',
                                dataType: 'json',
                                timeout: 5000,

                                success: function(data) {

                                    let location = 'Unknown';

                                    if (data && !data.error) {

                                        location =
                                            (data.city ? data.city + ', ' : '') +
                                            (data.country_name || '');

                                        if (location.trim() === '') {
                                            location = 'Unknown';
                                        }
                                    }

                                    ipCache[ip] = location;
                                    $this.text(location);
                                },

                                error: function(xhr) {

                                    // Handle rate limit silently
                                    if (xhr.status === 429) {
                                        $this.text('Rate Limited');
                                    } else {
                                        $this.text('Unavailable');
                                    }

                                    // Prevent huge console spam
                                    console.clear();
                                }
                            });

                        });

                        // Export functions
                        $('#exportCSV').on('click', function(e) {
                            e.preventDefault();
                            exportTableToCSV('login_logs.csv');
                        });

                        $('#exportExcel').on('click', function(e) {
                            e.preventDefault();
                            exportTableToExcel('login_logs.xls');
                        });

                        function exportTableToCSV(filename) {
                            var csv = [];
                            var rows = document.querySelectorAll('#login_logs_table thead tr, #login_logs_table tbody tr');
                            for (var i = 0; i < rows.length; i++) {
                                var row = [],
                                    cols = rows[i].querySelectorAll('td, th');
                                for (var j = 0; j < cols.length; j++) {
                                    var text = cols[j].innerText.replace(/,/g, ';');
                                    row.push('"' + text + '"');
                                }
                                csv.push(row.join(','));
                            }
                            var csvFile = new Blob(["\uFEFF" + csv.join('\n')], {
                                type: 'text/csv;charset=utf-8;'
                            });
                            var downloadLink = document.createElement('a');
                            downloadLink.href = URL.createObjectURL(csvFile);
                            downloadLink.setAttribute('download', filename);
                            document.body.appendChild(downloadLink);
                            downloadLink.click();
                            document.body.removeChild(downloadLink);
                            toastr.success('Export completed!');
                        }

                        function exportTableToExcel(filename) {
                            var table = document.getElementById('login_logs_table');
                            var html = table.cloneNode(true);
                            $(html).find('.view-user-agent, .view-log-details, .delete-single-log').remove();
                            var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html.outerHTML);
                            var downloadLink = document.createElement('a');
                            downloadLink.href = url;
                            downloadLink.setAttribute('download', filename);
                            document.body.appendChild(downloadLink);
                            downloadLink.click();
                            document.body.removeChild(downloadLink);
                            toastr.success('Export completed!');
                        }

                        // Clear logs
                        $('#clearLogsBtn').on('click', function() {
                            $('#clearLogsModal').modal('show');
                        });

                        $('#confirmClearLogs').on('click', function() {
                            var days = $('#delete_days').val();
                            var $btn = $(this);
                            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

                            $.ajax({
                                url: '<?= BASE_URL ?>ajax/clear-login-logs.php',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    days: days
                                },
                                success: function(response) {
                                    if (response.status === 'success') {
                                        toastr.success(response.message);
                                        setTimeout(function() {
                                            location.reload();
                                        }, 1500);
                                    } else {
                                        toastr.error(response.message);
                                    }
                                },
                                error: function() {
                                    toastr.error('Failed to clear logs.');
                                },
                                complete: function() {
                                    $btn.prop('disabled', false).html('Delete Logs');
                                    $('#clearLogsModal').modal('hide');
                                }
                            });
                        });
                    });
                </script>