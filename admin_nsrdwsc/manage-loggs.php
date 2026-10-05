<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');

// Only super admin can view activity logs
if ($_SESSION['login_type'] != 1) {
    header("Location: dashboard.php");
    exit();
}

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total_logs,
    SUM(CASE WHEN action_type = 'INSERT' THEN 1 ELSE 0 END) as inserts,
    SUM(CASE WHEN action_type = 'UPDATE' THEN 1 ELSE 0 END) as updates,
    SUM(CASE WHEN action_type = 'DELETE' THEN 1 ELSE 0 END) as deletes,
    SUM(CASE WHEN action_type = 'LOGIN' THEN 1 ELSE 0 END) as logins,
    SUM(CASE WHEN action_type = 'LOGOUT' THEN 1 ELSE 0 END) as logouts,
    COUNT(DISTINCT username) as unique_users
FROM tbl_activity_logs";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Get unique users for filter dropdown
$users_query = mysqli_query($conn, "SELECT DISTINCT username, user_type FROM tbl_activity_logs WHERE username IS NOT NULL ORDER BY username ASC");

// Get unique tables for filter dropdown
$tables_query = mysqli_query($conn, "SELECT DISTINCT table_name FROM tbl_activity_logs WHERE table_name IS NOT NULL AND table_name != '' ORDER BY table_name ASC");

// Handle bulk deletion
if (isset($_POST['bulk_delete']) && isset($_POST['delete_ids'])) {
    $delete_ids = array_map('intval', $_POST['delete_ids']);
    if (!empty($delete_ids)) {
        $ids_list = implode(',', $delete_ids);
        $delete_query = "DELETE FROM tbl_activity_logs WHERE log_id IN ($ids_list)";
        if (mysqli_query($conn, $delete_query)) {
            $count = mysqli_affected_rows($conn);
            $_SESSION['success_message'] = "$count log(s) deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to delete logs!";
        }
    }
    header('Location: ' . BASE_URL . 'manage-loggs.php');
    exit;
}

// Handle single deletion
if (isset($_GET['delete_log'])) {
    $log_id = intval($_GET['delete_log']);
    $delete_query = "DELETE FROM tbl_activity_logs WHERE log_id = $log_id";
    if (mysqli_query($conn, $delete_query)) {
        $_SESSION['success_message'] = "Log deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to delete log!";
    }
    header('Location: ' . BASE_URL . 'manage-loggs.php');
    exit;
}
?>

<!--begin::Body-->

<body id="kt_body"
    class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
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
                            <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            </div>
                        </div>
                        <div class="topbar">
                            <div class="topbar-item">
                                <div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2"
                                    id="kt_quick_user_toggle">
                                    <span
                                        class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                                    <span
                                        class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= htmlspecialchars($_SESSION['username']) ?></span>
                                    <span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
                                        <span
                                            class="symbol-label font-size-h5 font-weight-bold"><?= substr($_SESSION['username'], 0, 1) ?></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="d-flex flex-column-fluid">
                        <div class="container">

                            <!-- Display Messages -->
                            <?php if (isset($_SESSION['success_message'])): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fa fa-check-circle"></i> <?= $_SESSION['success_message'] ?>
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                </div>
                                <?php unset($_SESSION['success_message']); ?>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['error_message'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fa fa-exclamation-triangle"></i> <?= $_SESSION['error_message'] ?>
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                </div>
                                <?php unset($_SESSION['error_message']); ?>
                            <?php endif; ?>

                            <!-- Statistics Cards -->
                            <div class="row mb-5">
                                <div class="col-md-2 col-sm-6 mb-3">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body p-3">
                                            <h6 class="mb-1">Total Logs</h6>
                                            <h3 class="mb-0"><?= number_format($stats['total_logs']) ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-3">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body p-3">
                                            <h6 class="mb-1">Inserts</h6>
                                            <h3 class="mb-0"><?= number_format($stats['inserts']) ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-3">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body p-3">
                                            <h6 class="mb-1">Updates</h6>
                                            <h3 class="mb-0"><?= number_format($stats['updates']) ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-3">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body p-3">
                                            <h6 class="mb-1">Deletes</h6>
                                            <h3 class="mb-0"><?= number_format($stats['deletes']) ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-3">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body p-3">
                                            <h6 class="mb-1">Logins/Logouts</h6>
                                            <h3 class="mb-0"><?= number_format($stats['logins'] + $stats['logouts']) ?>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-3">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body p-3">
                                            <h6 class="mb-1">Unique Users</h6>
                                            <h3 class="mb-0"><?= number_format($stats['unique_users']) ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Logs Table Card -->
                            <div class="card card-custom gutter-b">
                                <div class="card-header flex-wrap py-3">
                                    <div class="card-title">
                                        <h3 class="card-label">
                                            <i class="fa fa-list-alt text-primary"></i> Activity Logs
                                        </h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <div class="dropdown dropdown-inline mr-2">
                                            <button type="button"
                                                class="btn btn-light-primary font-weight-bolder dropdown-toggle"
                                                data-toggle="dropdown">
                                                <i class="bi bi-download text-light"></i> Export
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                <ul class="navi flex-column navi-hover py-2">
                                                    <li class="navi-item">
                                                        <a href="#" class="navi-link" id="exportCSV">
                                                            <i class="bi bi-filetype-csv mr-2"></i> CSV File
                                                        </a>
                                                    </li>
                                                    <li class="navi-item">
                                                        <a href="#" class="navi-link" id="exportExcel">
                                                            <i class="bi bi-filetype-xlsx mr-2"></i> Excel File
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-light-primary font-weight-bolder mr-2"
                                            onclick="window.location.reload();">
                                            <i class="bi bi-arrow-clockwise text-light"></i> Refresh
                                        </button>
                                        <button type="button" class="btn btn-danger font-weight-bolder"
                                            id="clearLogsBtn">
                                            <i class="bi bi-trash"></i> Clear Old Logs
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body pb-0">
                                    <!-- Filter Controls -->
                                    <div class="row mb-4">
                                        <div class="col-md-2">
                                            <label class="form-label">Filter by Action:</label>
                                            <select id="actionFilter" class="form-control">
                                                <option value="">All Actions</option>
                                                <option value="INSERT">INSERT</option>
                                                <option value="UPDATE">UPDATE</option>
                                                <option value="DELETE">DELETE</option>
                                                <option value="LOGIN">LOGIN</option>
                                                <option value="LOGOUT">LOGOUT</option>
                                                <option value="EXPORT">EXPORT</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Filter by User:</label>
                                            <select id="userFilter" class="form-control">
                                                <option value="">All Users</option>
                                                <?php
                                                mysqli_data_seek($users_query, 0);
                                                while ($user = mysqli_fetch_assoc($users_query)) {
                                                    $user_name = htmlspecialchars($user['username']);
                                                    ?>
                                                    <option value="<?= $user_name ?>"><?= $user_name ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Filter by Table:</label>
                                            <select id="tableFilter" class="form-control">
                                                <option value="">All Tables</option>
                                                <?php
                                                mysqli_data_seek($tables_query, 0);
                                                while ($table = mysqli_fetch_assoc($tables_query)) {
                                                    $table_name = htmlspecialchars($table['table_name']);
                                                    ?>
                                                    <option value="<?= $table_name ?>"><?= $table_name ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Start Date:</label>
                                            <input type="date" id="startDate" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">End Date:</label>
                                            <input type="date" id="endDate" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">&nbsp;</label>
                                            <button id="resetFilters" class="btn btn-secondary w-100">Reset
                                                Filters</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="activity_logs_table" class="table table-bordered dataTable"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="3%"><input type="checkbox" id="selectAll"></th>
                                                    <th width="5%">ID</th>
                                                    <th width="12%">User</th>
                                                    <th width="8%">Action</th>
                                                    <th width="12%">Table</th>
                                                    <th width="8%">Record ID</th>
                                                    <th width="10%">IP Address</th>
                                                    <th width="12%">Time</th>
                                                    <th width="10%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $key = 1;
                                                $query = mysqli_query($conn, "SELECT * FROM tbl_activity_logs ORDER BY log_id DESC LIMIT 1000");
                                                while ($row = mysqli_fetch_array($query)):
                                                    ?>
                                                    <tr data-action="<?= htmlspecialchars($row['action_type']) ?>"
                                                        data-user="<?= htmlspecialchars($row['username'] ?? '') ?>"
                                                        data-table="<?= htmlspecialchars($row['table_name'] ?? '') ?>"
                                                        data-time="<?= $row['log_time'] ?>">
                                                        <td class="text-center">
                                                            <input type="checkbox" class="logCheckbox"
                                                                value="<?= $row['log_id'] ?>">
                                                        </td>
                                                        <td><?= $key++ ?></td>
                                                        <td>
                                                            <strong><?= htmlspecialchars($row['username'] ?? 'Unknown') ?></strong>
                                                            <br>
                                                            <small
                                                                class="text-muted"><?= htmlspecialchars($row['user_type'] ?? '-') ?></small>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $badge_class = '';
                                                            switch ($row['action_type']) {
                                                                case 'INSERT':
                                                                    $badge_class = 'success';
                                                                    break;
                                                                case 'UPDATE':
                                                                    $badge_class = 'warning';
                                                                    break;
                                                                case 'DELETE':
                                                                    $badge_class = 'danger';
                                                                    break;
                                                                case 'LOGIN':
                                                                    $badge_class = 'primary';
                                                                    break;
                                                                case 'LOGOUT':
                                                                    $badge_class = 'secondary';
                                                                    break;
                                                                default:
                                                                    $badge_class = 'info';
                                                            }
                                                            ?>
                                                            <span class="badge badge-<?= $badge_class ?> px-3 py-2">
                                                                <?= $row['action_type'] ?>
                                                            </span>
                                                        </td>
                                                        <td><code><?= htmlspecialchars($row['table_name'] ?? '-') ?></code>
                                                        </td>
                                                        <td><?= $row['record_id'] ?? '-' ?></td>
                                                        <td><code><?= htmlspecialchars($row['user_ip'] ?? '-') ?></code>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="text-info"><?= date('d-m-Y H:i:s', strtotime($row['log_time'])) ?></span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="button" class="btn btn-primary view-log"
                                                                    data-log-id="<?= $row['log_id'] ?>">
                                                                    <i class="fa fa-eye"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger delete-log"
                                                                    data-log-id="<?= $row['log_id'] ?>">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
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
                        <div class="alert alert-danger d-flex justify-content-between align-items-center mb-0 shadow">
                            <div>
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong><span id="selectedCount">0</span></strong> log(s) selected
                            </div>
                            <div>
                                <button class="btn btn-sm btn-danger" id="confirmBulkDelete">
                                    <i class="fa fa-trash"></i> Delete Selected
                                </button>
                                <button class="btn btn-sm btn-secondary ml-2" id="clearSelection">
                                    <i class="fa fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Log Modal -->
                <div class="modal fade" id="viewLogModal" tabindex="-1" role="dialog"
                    aria-labelledby="viewLogModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content" style="background-color: #f8f8f8f1;">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title text-light" id="viewLogModalLabel">
                                    <i class="fa fa-history"></i> Log Details
                                </h5>
                                <button type="button" class="btn btn-pill btn-secondary pl-3 p-1" data-dismiss="modal">
                                    <i class="fa fa-times"></i> 
                                </button>
                            </div>
                            <div class="modal-body" id="logModalBody">
                                <div class="text-center p-5">
                                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                                    <p class="mt-2">Loading log details...</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fa fa-times"></i> Close
                                </button>
                                <button type="button" class="btn btn-primary" onclick="window.print();">
                                    <i class="fa fa-print"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clear Logs Modal -->
                <div class="modal fade" id="clearLogsModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Clear Activity Logs</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete activity logs?</p>
                                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone!</p>
                                <div class="form-group">
                                    <label>Delete logs older than (days):</label>
                                    <select id="deleteDays" class="form-control">
                                        <option value="7">Older than 7 days</option>
                                        <option value="30">Older than 30 days</option>
                                        <option value="90">Older than 90 days</option>
                                        <option value="180">Older than 180 days</option>
                                        <option value="365">Older than 365 days</option>
                                        <option value="all">All logs</option>
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
                    $(document).ready(function () {
                        // Initialize DataTable
                        var table = $('#activity_logs_table').DataTable({
                            "pageLength": 25,
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
                            "columnDefs": [
                                { "orderable": false, "targets": [0, 8] },
                                { "width": "3%", "targets": 0 },
                                { "width": "5%", "targets": 1 },
                                { "width": "12%", "targets": 2 },
                                { "width": "8%", "targets": 3 },
                                { "width": "12%", "targets": 4 },
                                { "width": "8%", "targets": 5 },
                                { "width": "10%", "targets": 6 },
                                { "width": "12%", "targets": 7 },
                                { "width": "10%", "targets": 8 }
                            ]
                        });

                        // Custom filter function
                        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                            var action = $('#actionFilter').val();
                            var user = $('#userFilter').val();
                            var tableName = $('#tableFilter').val();
                            var startDate = $('#startDate').val();
                            var endDate = $('#endDate').val();

                            var row = table.row(dataIndex).node();
                            var rowAction = $(row).data('action');
                            var rowUser = $(row).data('user');
                            var rowTable = $(row).data('table');
                            var rowTime = $(row).data('time');

                            if (action !== '' && rowAction !== action) return false;
                            if (user !== '' && rowUser !== user) return false;
                            if (tableName !== '' && rowTable !== tableName) return false;
                            if (startDate !== '' && rowTime < startDate) return false;
                            if (endDate !== '' && rowTime > endDate + ' 23:59:59') return false;

                            return true;
                        });

                        // Filter events
                        $('#actionFilter, #userFilter, #tableFilter, #startDate, #endDate').on('change', function () {
                            table.draw();
                        });

                        $('#resetFilters').on('click', function () {
                            $('#actionFilter').val('');
                            $('#userFilter').val('');
                            $('#tableFilter').val('');
                            $('#startDate').val('');
                            $('#endDate').val('');
                            table.draw();
                        });

                        // Select All functionality
                        $('#selectAll').on('change', function () {
                            var isChecked = $(this).prop('checked');
                            $('.logCheckbox').prop('checked', isChecked);
                            updateBulkActionsBar();
                        });

                        $(document).on('change', '.logCheckbox', function () {
                            updateBulkActionsBar();
                            var allChecked = $('.logCheckbox:checked').length === $('.logCheckbox').length;
                            $('#selectAll').prop('checked', allChecked);
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

                        // Helper Functions
                        function escapeHtml(text) {
                            if (!text) return '';
                            return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        }

                        function formatDate(dateString) {
                            if (!dateString) return '-';
                            var date = new Date(dateString);
                            return date.toLocaleString('en-GB', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });
                        }

                        function formatJSON(data) {
                            if (!data || data === 'null' || data === '') return 'No data';
                            try {
                                var parsed = typeof data === 'string' ? JSON.parse(data) : data;
                                return JSON.stringify(parsed, null, 2);
                            } catch (e) {
                                return data;
                            }
                        }

                        function generateDataTable(data) {
                            if (!data || typeof data !== 'object' || Object.keys(data).length === 0) {
                                return '<div class="text-muted text-center">No data available</div>';
                            }

                            var html = '<table class="table table-sm table-bordered mb-0">' +
                                '<thead class="thead-light"><tr><th width="35%">Field</th><th width="65%">Value</th></tr></thead>' +
                                '<tbody>';

                            for (var key in data) {
                                if (data.hasOwnProperty(key)) {
                                    var value = data[key];
                                    if (typeof value === 'object') {
                                        value = JSON.stringify(value);
                                    }
                                    html += '<tr>' +
                                        '<td><strong>' + key.replace(/_/g, ' ').toUpperCase() + '</strong></td>' +
                                        '<td>' + (value || '<em class="text-muted">empty</em>') + '</td>' +
                                        '</tr>';
                                }
                            }
                            html += '</tbody></table>';
                            return html;
                        }

                        // Generate Log Details HTML
                        function generateLogDetailsHTML(log) {
                            var badgeClass = '';
                            switch (log.action_type) {
                                case 'INSERT': badgeClass = 'success'; break;
                                case 'UPDATE': badgeClass = 'warning'; break;
                                case 'DELETE': badgeClass = 'danger'; break;
                                case 'LOGIN': badgeClass = 'primary'; break;
                                case 'LOGOUT': badgeClass = 'secondary'; break;
                                default: badgeClass = 'info';
                            }

                            var actionIcon = '';
                            switch (log.action_type) {
                                case 'INSERT': actionIcon = 'plus'; break;
                                case 'UPDATE': actionIcon = 'edit'; break;
                                case 'DELETE': actionIcon = 'trash'; break;
                                case 'LOGIN': actionIcon = 'sign-in'; break;
                                case 'LOGOUT': actionIcon = 'sign-out'; break;
                                default: actionIcon = 'info-circle';
                            }

                            // Parse old and new data
                            var oldData = null;
                            var newData = null;

                            try {
                                if (log.old_data && log.old_data !== 'null') {
                                    oldData = typeof log.old_data === 'string' ? JSON.parse(log.old_data) : log.old_data;
                                }
                            } catch (e) { oldData = null; }

                            try {
                                if (log.new_data && log.new_data !== 'null') {
                                    newData = typeof log.new_data === 'string' ? JSON.parse(log.new_data) : log.new_data;
                                }
                            } catch (e) { newData = null; }

                            var oldDataHtml = oldData ? generateDataTable(oldData) : '<div class="text-muted text-center">No old data available</div>';
                            var newDataHtml = newData ? generateDataTable(newData) : '<div class="text-muted text-center">No new data available</div>';

                            // Parse changed fields
                            var changedFieldsHtml = '';
                            if (log.changed_fields && log.changed_fields !== 'null') {
                                try {
                                    var changedFields = typeof log.changed_fields === 'string' ? JSON.parse(log.changed_fields) : log.changed_fields;
                                    if (Object.keys(changedFields).length > 0) {
                                        changedFieldsHtml = '<div class="card mt-3 border-0 shadow-sm">' +
                                            '<div class="card-header bg-info text-white">' +
                                            '<strong><i class="fa fa-exchange"></i> Changed Fields Summary</strong>' +
                                            '</div>' +
                                            '<div class="card-body">' +
                                            '<div class="table-responsive">' +
                                            '<table class="table table-bordered">' +
                                            '<thead class="thead-light"><tr><th>Field</th><th>Old Value</th><th>New Value</th></tr></thead>' +
                                            '<tbody>';
                                        for (var field in changedFields) {
                                            changedFieldsHtml += '<tr class="table-warning">' +
                                                '<td><strong>' + field.replace(/_/g, ' ').toUpperCase() + '</strong></td>' +
                                                '<td class="text-danger">' + (changedFields[field].old || '<em class="text-muted">empty</em>') + '</td>' +
                                                '<td class="text-success">' + (changedFields[field].new || '<em class="text-muted">empty</em>') + '</td>' +
                                                '</tr>';
                                        }
                                        changedFieldsHtml += '</tbody></table></div></div></div>';
                                    }
                                } catch (e) {
                                    changedFieldsHtml = '<div class="card mt-3"><div class="card-body"><pre>' + escapeHtml(log.changed_fields) + '</pre></div></div>';
                                }
                            }

                            return `
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body py-2">
                            <small class="text-muted text-uppercase">Log ID</small>
                            <h6 class="mb-0">#${log.log_id}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-2">
                            <small class="text-muted text-uppercase">User</small>
                            <h6 class="mb-0">${escapeHtml(log.username) || '-'}</h6>
                            <small>${escapeHtml(log.user_type) || '-'}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card  border-0 shadow-sm">
                        <div class="card-body py-2">
                            <small class="text-muted text-uppercase">Action Type</small>
                            <h6 class="mb-0">
                                <span class="badge badge-${badgeClass} px-3 py-2">
                                    <i class="fa fa-${actionIcon}"></i> ${log.action_type}
                                </span>
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card  border-0 shadow-sm">
                        <div class="card-body py-2">
                            <small class="text-muted text-uppercase">Log Time</small>
                            <h6 class="mb-0">${formatDate(log.log_time)}</h6>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card  border-0 shadow-sm">
                        <div class="card-body py-2">
                            <small class="text-muted text-uppercase">Table Name</small>
                            <h6 class="mb-0"><code>${escapeHtml(log.table_name) || '-'}</code></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card  border-0 shadow-sm">
                        <div class="card-body py-2">
                            <small class="text-muted text-uppercase">Record ID</small>
                            <h6 class="mb-0"><span class="badge badge-secondary">#${log.record_id || '-'}</span></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card  border-0 shadow-sm">
                        <div class="card-body py-2">
                            <small class="text-muted text-uppercase">Record Identifier</small>
                            <h6 class="mb-0"><code>${escapeHtml(log.record_identifier) || '-'}</code></h6>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card  border-0 shadow-sm">
                        <div class="card-body py-2">
                            <small class="text-muted text-uppercase">IP Address</small>
                            <h6 class="mb-0"><code>${escapeHtml(log.user_ip) || '-'}</code></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card  border-0 shadow-sm">
                        <div class="card-body py-2">
                            <small class="text-muted text-uppercase">HTTP Method</small>
                            <h6 class="mb-0"><code>${log.http_method || '-'}</code></h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card  border-0 shadow-sm">
                        <div class="card-body py-2">
                            <small class="text-muted text-uppercase">User Agent</small>
                            <h6 class="mb-0"><small>${escapeHtml(String(log.user_agent).substring(0, 50))}...</small></h6>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-header ">
                    <strong><i class="fa fa-info-circle"></i> Action Description</strong>
                </div>
                <div class="card-body">
                    <p class="mb-0">${escapeHtml(log.action_description) || '-'}</p>
                </div>
            </div>
            
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-header ">
                    <strong><i class="fa fa-link"></i> Request URL</strong>
                </div>
                <div class="card-body">
                    <small class="text-break">${escapeHtml(log.request_url) || '-'}</small>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-warning">
                            <strong><i class="fa fa-database"></i> Old Data</strong>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow: auto;">
                            ${oldDataHtml}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-success text-white">
                            <strong><i class="fa fa-database"></i> New Data</strong>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow: auto;">
                            ${newDataHtml}
                        </div>
                    </div>
                </div>
            </div>
            
            ${changedFieldsHtml}
        `;
                        }

                        // View log button click handler
                        $(document).on('click', '.view-log', function (e) {
                            e.preventDefault();
                            var logId = $(this).data('log-id');

                            if (!logId) {
                                Swal.fire('Error', 'Invalid log ID', 'error');
                                return;
                            }

                            $('#logModalBody').html('<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-2x"></i><br><p class="mt-2">Loading log details...</p></div>');
                            $('#viewLogModal').modal('show');

                            $.ajax({
                                url: '<?= BASE_URL ?>ajax/get-log-details.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { log_id: logId },
                                success: function (response) {
                                    if (response.error) {
                                        $('#logModalBody').html('<div class="text-center p-5 text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><br><p class="mt-2">' + response.error + '</p></div>');
                                        return;
                                    }
                                    var html = generateLogDetailsHTML(response);
                                    $('#logModalBody').html(html);
                                },
                                error: function (xhr, status, error) {
                                    console.error('AJAX Error:', error);
                                    $('#logModalBody').html('<div class="text-center p-5 text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><br><p class="mt-2">Failed to load log details. Please try again.</p><br><small>' + error + '</small></div>');
                                }
                            });
                        });

                        // Single delete
                        $(document).on('click', '.delete-log', function () {
                            var logId = $(this).data('log-id');
                            var $btn = $(this);

                            Swal.fire({
                                title: 'Delete Log',
                                text: 'Are you sure you want to delete this log?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Yes, delete!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
                                    window.location.href = '<?= BASE_URL ?>manage-loggs.php?delete_log=' + logId;
                                }
                            });
                        });

                        // Bulk delete button
                        $('#bulkDeleteBtn').on('click', function () {
                            var selected = $('.logCheckbox:checked').length;
                            if (selected === 0) {
                                Swal.fire('No Selection', 'Please select logs to delete', 'warning');
                                return;
                            }
                            $('#confirmBulkDelete').click();
                        });

                        // Confirm bulk delete
                        $('#confirmBulkDelete').on('click', function () {
                            var selectedIds = [];
                            $('.logCheckbox:checked').each(function () {
                                selectedIds.push($(this).val());
                            });

                            if (selectedIds.length === 0) return;

                            Swal.fire({
                                title: 'Bulk Delete',
                                html: `Are you sure you want to delete <strong>${selectedIds.length}</strong> logs? This action cannot be undone!`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Yes, delete all!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    var form = $('<form method="post"></form>');
                                    form.append('<input type="hidden" name="bulk_delete" value="1">');
                                    $.each(selectedIds, function (i, id) {
                                        form.append('<input type="hidden" name="delete_ids[]" value="' + id + '">');
                                    });
                                    $('body').append(form);
                                    form.submit();
                                }
                            });
                        });

                        // Clear selection
                        $('#clearSelection').on('click', function () {
                            $('.logCheckbox').prop('checked', false);
                            $('#selectAll').prop('checked', false);
                            updateBulkActionsBar();
                        });

                        // Clear logs modal
                        $('#clearLogsBtn').on('click', function () {
                            $('#clearLogsModal').modal('show');
                        });

                        $('#confirmClearLogs').on('click', function () {
                            var days = $('#deleteDays').val();
                            var $btn = $(this);
                            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

                            $.ajax({
                                url: '<?= BASE_URL ?>ajax/clear-activity-logs.php',
                                type: 'POST',
                                dataType: 'json',
                                data: { days: days },
                                success: function (response) {
                                    if (response.status === 'success') {
                                        Swal.fire('Success', response.message, 'success');
                                        setTimeout(function () { location.reload(); }, 1500);
                                    } else {
                                        Swal.fire('Error', response.message, 'error');
                                    }
                                },
                                error: function () {
                                    Swal.fire('Error', 'Failed to clear logs', 'error');
                                },
                                complete: function () {
                                    $btn.prop('disabled', false).html('Delete Logs');
                                    $('#clearLogsModal').modal('hide');
                                }
                            });
                        });

                        // Export functions
                        $('#exportCSV').on('click', function (e) {
                            e.preventDefault();
                            exportTableToCSV('activity_logs.csv');
                        });

                        $('#exportExcel').on('click', function (e) {
                            e.preventDefault();
                            exportTableToExcel('activity_logs.xls');
                        });

                        function exportTableToCSV(filename) {
                            var csv = [];
                            var rows = document.querySelectorAll('#activity_logs_table thead tr, #activity_logs_table tbody tr');
                            for (var i = 0; i < rows.length; i++) {
                                var row = [], cols = rows[i].querySelectorAll('td, th');
                                for (var j = 0; j < cols.length; j++) {
                                    var text = cols[j].innerText.replace(/,/g, ';');
                                    row.push('"' + text + '"');
                                }
                                csv.push(row.join(','));
                            }
                            var csvFile = new Blob(["\uFEFF" + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
                            var downloadLink = document.createElement('a');
                            downloadLink.href = URL.createObjectURL(csvFile);
                            downloadLink.download = filename;
                            document.body.appendChild(downloadLink);
                            downloadLink.click();
                            document.body.removeChild(downloadLink);
                            Swal.fire('Success', 'Export completed!', 'success');
                        }

                        function exportTableToExcel(filename) {
                            var table = document.getElementById('activity_logs_table');
                            var html = table.cloneNode(true);
                            $(html).find('.view-log, .delete-log').remove();
                            var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html.outerHTML);
                            var downloadLink = document.createElement('a');
                            downloadLink.href = url;
                            downloadLink.download = filename;
                            document.body.appendChild(downloadLink);
                            downloadLink.click();
                            document.body.removeChild(downloadLink);
                            Swal.fire('Success', 'Export completed!', 'success');
                        }
                    });
                </script>

                <style>
                    .dataTables_wrapper .dataTables_length,
                    .dataTables_wrapper .dataTables_filter {
                        margin-bottom: 15px;
                    }

                    .table td {
                        vertical-align: middle;
                    }

                    code {
                        background: #f4f4f4;
                        padding: 2px 5px;
                        border-radius: 3px;
                        font-size: 11px;
                    }

                    .badge {
                        font-size: 11px;
                        font-weight: 500;
                        padding: 6px 12px;
                    }

                    .table-responsive {
                        overflow-x: auto;
                    }

                    #bulkActionsBar {
                        position: fixed;
                        bottom: 20px;
                        left: 0;
                        right: 0;
                        z-index: 1000;
                    }

                    pre {
                        white-space: pre-wrap;
                        word-wrap: break-word;
                        font-size: 12px;
                    }

                    .modal-xl {
                        max-width: 1200px;
                    }
                </style>
</body>