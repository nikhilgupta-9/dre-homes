<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');

// Only super admin can view activity logs
if ($_SESSION['login_type'] != 1) {
    header("Location: dashboard.php");
    exit();
}

// Get unique users for filter dropdown
$users_query = mysqli_query($conn, "SELECT DISTINCT username, user_id FROM tbl_activity_logs WHERE username IS NOT NULL ORDER BY username ASC");
?>

<!--begin::Body-->

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
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
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24" />
                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                        </g>
                    </svg>
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
                                <div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
                                    <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                                    <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= $_SESSION['username'] ?></span>
                                    <span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
                                        <span class="symbol-label font-size-h5 font-weight-bold"><?= substr($_SESSION['username'], 0, 1) ?></span>
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
                <div class="d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Entry-->
                    <div class="d-flex flex-column-fluid">
                        <!--begin::Container-->
                        <div class="container">

                            <!--begin::Card-->
                            <div class="card card-custom gutter-b">
                                <div class="card-header flex-wrap py-3">
                                    <div class="card-title">
                                        <h3 class="card-label">Activity Logs</h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <button type="button" class="btn btn-light-primary font-weight-bolder" onclick="window.location.reload();">
                                            <span class="svg-icon svg-icon-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path d="M12,21 C7.02943725,21 3,16.9705627 3,12 C3,7.02943725 7.02943725,3 12,3 C14.627417,3 16.9021249,4.19369558 18.443355,6.06161815 L16.5,6.06161815 C15.6715729,6.06161815 15,6.73319103 15,7.56161815 C15,8.39004527 15.6715729,9.06161815 16.5,9.06161815 L20.5,9.06161815 C21.3284271,9.06161815 22,8.39004527 22,7.56161815 L22,3.56161815 C22,2.73319103 21.3284271,2.06161815 20.5,2.06161815 C19.6715729,2.06161815 19,2.73319103 19,3.56161815 L19,5.27827685 C17.1921949,3.2473172 14.6897479,2 12,2 C6.4771525,2 2,6.4771525 2,12 C2,17.5228475 6.4771525,22 12,22 C17.5228475,22 22,17.5228475 22,12 C22,11.4477153 21.5522847,11 21,11 C20.4477153,11 20,11.4477153 20,12 C20,16.418278 16.418278,20 12,20 Z" fill="#000000" fill-rule="nonzero" />
                                                    </g>
                                                </svg>
                                            </span>
                                            Refresh
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body pb-0">
                                    <!-- Filter Controls -->
                                    <div class="row mb-4">
                                        <div class="col-md-3">
                                            <label class="form-label">Filter by Action:</label>
                                            <select id="actionFilter" class="form-control">
                                                <option value="">All Actions</option>
                                                <option value="INSERT">INSERT</option>
                                                <option value="UPDATE">UPDATE</option>
                                                <option value="DELETE">DELETE</option>
                                                <option value="LOGIN">LOGIN</option>
                                                <option value="LOGOUT">LOGOUT</option>
                                                <option value="EXPORT">EXPORT</option>
                                                <option value="STATUS_CHANGE">STATUS_CHANGE</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Filter by User:</label>
                                            <select id="userFilter" class="form-control">
                                                <option value="">All Users</option>
                                                <?php
                                                mysqli_data_seek($users_query, 0);
                                                while ($user = mysqli_fetch_assoc($users_query)) {
                                                    $user_name = htmlspecialchars($user['username']);
                                                ?>
                                                    <option value="<?= $user_name ?>"><?= $user_name ?></option>;
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Filter by Table:</label>
                                            <input type="text" id="tableFilter" class="form-control" placeholder="Enter table name...">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button id="clearFilters" class="btn btn-secondary form-control">Clear All Filters</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <table id="activity_logs_table" class="table table-bordered dataTable" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>User</th>
                                                <th>Action</th>
                                                <th>Table</th>
                                                <th>Record ID</th>
                                                <th>IP Address</th>
                                                <th>Time</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $key1 = 1;
                                            $query = mysqli_query($conn, "SELECT * FROM tbl_activity_logs ORDER BY log_id DESC LIMIT 500");
                                            while ($row = mysqli_fetch_array($query)) {
                                            ?>
                                                <tr>
                                                    <td><?= $key1++ ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($row['username'] ?? 'Unknown') ?></strong><br>
                                                        <small class="text-muted"><?= $row['user_type'] ?></small>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $badge_class = '';
                                                        switch ($row['action_type']) {
                                                            case 'INSERT':
                                                                $badge_class = 'label-light-success';
                                                                break;
                                                            case 'UPDATE':
                                                                $badge_class = 'label-light-warning';
                                                                break;
                                                            case 'DELETE':
                                                                $badge_class = 'label-light-danger';
                                                                break;
                                                            case 'LOGIN':
                                                                $badge_class = 'label-light-primary';
                                                                break;
                                                            case 'LOGOUT':
                                                                $badge_class = 'label-light-secondary';
                                                                break;
                                                            default:
                                                                $badge_class = 'label-light-info';
                                                        }
                                                        ?>
                                                        <span class="label label-lg font-weight-bold <?= $badge_class ?> label-inline">
                                                            <?= $row['action_type'] ?>
                                                        </span>
                                                    </td>
                                                    <td><code><?= htmlspecialchars($row['table_name']) ?></code>

                                                    <td><?= $row['record_id'] ?? '-' ?>

                                                    <td><code><?= $row['user_ip'] ?? '-' ?></code> </td>
                                                    <td><span class="text-info"><?= date('d-m-Y H:i:s', strtotime($row['log_time'])) ?></span> </td>

                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#logModal<?= $row['log_id'] ?>">
                                                            <i class="fa fa-eye"></i> View
                                                        </button>

                                                        <!-- Modal -->
                                                        <div class="modal fade" id="logModal<?= $row['log_id'] ?>" tabindex="-1">
                                                            <div class="modal-dialog modal-xl">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-primary text-white">
                                                                        <h5 class="modal-title">
                                                                            <i class="fa fa-history"></i> Log Details #<?= $row['log_id'] ?>
                                                                        </h5>
                                                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                                    </div>

                                                                    <div class="modal-body">
                                                                        <!-- Basic Information Cards -->
                                                                        <div class="row mb-4">
                                                                            <div class="col-md-4">
                                                                                <div class="card bg-light border-0 shadow-sm">
                                                                                    <div class="card-body py-2">
                                                                                        <small class="text-muted text-uppercase">Action Type</small>
                                                                                        <h6 class="mb-0">
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
                                                                                                default:
                                                                                                    $badge_class = 'info';
                                                                                            }
                                                                                            ?>
                                                                                            <span class="badge badge-<?= $badge_class ?> badge-lg px-3 py-2">
                                                                                                <i class="fa fa-<?= $row['action_type'] == 'INSERT' ? 'plus' : ($row['action_type'] == 'UPDATE' ? 'edit' : ($row['action_type'] == 'DELETE' ? 'trash' : 'info-circle')) ?>"></i>
                                                                                                <?= $row['action_type'] ?>
                                                                                            </span>
                                                                                        </h6>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-4">
                                                                                <div class="card bg-light border-0 shadow-sm">
                                                                                    <div class="card-body py-2">
                                                                                        <small class="text-muted text-uppercase">Table Name</small>
                                                                                        <h6 class="mb-0">
                                                                                            <code class="bg-dark text-white px-2 py-1 rounded"><?= htmlspecialchars($row['table_name'] ?? '-') ?></code>
                                                                                        </h6>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-4">
                                                                                <div class="card bg-light border-0 shadow-sm">
                                                                                    <div class="card-body py-2">
                                                                                        <small class="text-muted text-uppercase">Record ID</small>
                                                                                        <h6 class="mb-0">
                                                                                            <span class="badge badge-secondary badge-lg px-3 py-2">#<?= $row['record_id'] ?? '-' ?></span>
                                                                                        </h6>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Action Details -->
                                                                        <div class="card mb-4 border-0 shadow-sm">
                                                                            <div class="card-header bg-light">
                                                                                <strong><i class="fa fa-info-circle"></i> Action Details</strong>
                                                                            </div>
                                                                            <div class="card-body">
                                                                                <div class="row">
                                                                                    <div class="col-md-6 mb-3">
                                                                                        <label class="text-muted small text-uppercase mb-1">Action Description</label>
                                                                                        <div class="p-2 bg-light rounded">
                                                                                            <?= nl2br(htmlspecialchars($row['action_description'] ?? '-')) ?>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-3 mb-3">
                                                                                        <label class="text-muted small text-uppercase mb-1">HTTP Method</label>
                                                                                        <div class="p-2 bg-light rounded">
                                                                                            <code><?= $row['http_method'] ?? '-' ?></code>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-3 mb-3">
                                                                                        <label class="text-muted small text-uppercase mb-1">IP Address</label>
                                                                                        <div class="p-2 bg-light rounded">
                                                                                            <code><?= htmlspecialchars($row['user_ip'] ?? '-') ?></code>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <label class="text-muted small text-uppercase mb-1">Request URL</label>
                                                                                        <div class="p-2 bg-light rounded">
                                                                                            <small class="text-break"><?= htmlspecialchars($row['request_url'] ?? '-') ?></small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Timestamp -->
                                                                        <div class="card mb-4 border-0 shadow-sm">
                                                                            <div class="card-header bg-light">
                                                                                <strong><i class="fa fa-clock-o"></i> Timestamp</strong>
                                                                            </div>
                                                                            <div class="card-body">
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <div class="p-2 bg-light rounded text-center">
                                                                                            <i class="fa fa-calendar text-primary"></i>
                                                                                            <strong><?= date('F d, Y - h:i:s A', strtotime($row['log_time'])) ?></strong>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Data Changes Comparison Table -->
                                                                        <div class="card mb-4 border-0 shadow-sm">
                                                                            <div class="card-header bg-light">
                                                                                <strong><i class="fa fa-exchange"></i> Data Changes</strong>
                                                                            </div>
                                                                            <div class="card-body">
                                                                                <?php
                                                                                $old_data = json_decode($row['old_data'] ?? '', true);
                                                                                $new_data = json_decode($row['new_data'] ?? '', true);
                                                                                $changed_fields = $row['changed_fields'] ?? '';

                                                                                if (!empty($old_data) || !empty($new_data)):
                                                                                ?>
                                                                                    <div class="table-responsive">
                                                                                        <table class="table table-bordered table-hover">
                                                                                            <thead class="thead-light">
                                                                                                <tr>
                                                                                                    <th width="30%">Field Name</th>
                                                                                                    <th width="35%">Old Value</th>
                                                                                                    <th width="35%">New Value</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                <?php
                                                                                                // Merge all keys from both arrays
                                                                                                $all_keys = array_unique(array_merge(array_keys($old_data ?? []), array_keys($new_data ?? [])));

                                                                                                foreach ($all_keys as $key):
                                                                                                    $old_val = $old_data[$key] ?? '';
                                                                                                    $new_val = $new_data[$key] ?? '';
                                                                                                    $has_changed = ($old_val != $new_val);
                                                                                                    $row_class = $has_changed ? 'table-warning' : '';
                                                                                                ?>
                                                                                                    <tr class="<?= $row_class ?>">
                                                                                                        <td>
                                                                                                            <strong><?= ucwords(str_replace('_', ' ', $key)) ?></strong>
                                                                                                            <?php if ($has_changed): ?>
                                                                                                                <span class="badge badge-warning ml-2">Changed</span>
                                                                                                            <?php endif; ?>
                                                                                                        </td>
                                                                                                        <td class="<?= $has_changed ? 'text-danger' : '' ?>">
                                                                                                            <?php if (is_array($old_val) || is_object($old_val)): ?>
                                                                                                                <pre class="mb-0 small"><code><?= htmlspecialchars(json_encode($old_val, JSON_PRETTY_PRINT)) ?></code></pre>
                                                                                                            <?php else: ?>
                                                                                                                <?= !empty($old_val) ? nl2br(htmlspecialchars($old_val)) : '<span class="text-muted"><em>empty</em></span>' ?>
                                                                                                            <?php endif; ?>
                                                                                                        </td>
                                                                                                        <td class="<?= $has_changed ? 'text-success' : '' ?>">
                                                                                                            <?php if (is_array($new_val) || is_object($new_val)): ?>
                                                                                                                <pre class="mb-0 small"><code><?= htmlspecialchars(json_encode($new_val, JSON_PRETTY_PRINT)) ?></code></pre>
                                                                                                            <?php else: ?>
                                                                                                                <?= !empty($new_val) ? nl2br(htmlspecialchars($new_val)) : '<span class="text-muted"><em>empty</em></span>' ?>
                                                                                                            <?php endif; ?>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                <?php endforeach; ?>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                <?php else: ?>
                                                                                    <div class="alert alert-info mb-0">
                                                                                        <i class="fa fa-info-circle"></i> No data changes recorded for this action.
                                                                                    </div>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Raw Data Section (Collapsible) -->
                                                                        <div class="card mb-0 border-0 shadow-sm">
                                                                            <div class="card-header bg-light">
                                                                                <strong><i class="fa fa-code"></i> Raw Data</strong>
                                                                                <button class="btn btn-sm btn-link float-right" type="button" data-toggle="collapse" data-target="#rawData<?= $row['log_id'] ?>">
                                                                                    <i class="fa fa-chevron-down"></i> Show/Hide
                                                                                </button>
                                                                            </div>
                                                                            <div class="collapse" id="rawData<?= $row['log_id'] ?>">
                                                                                <div class="card-body">
                                                                                    <div class="row">
                                                                                        <div class="col-md-6">
                                                                                            <label class="text-muted small text-uppercase">Old Data (JSON)</label>
                                                                                            <pre class="text-dark p-3 rounded" style="max-height: 300px; overflow: auto;"><code><?= htmlspecialchars(json_encode($old_data, JSON_PRETTY_PRINT)) ?: 'No old data' ?></code></pre>
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <label class="text-muted small text-uppercase">New Data (JSON)</label>
                                                                                            <pre class=" text-dark p-3 rounded" style="max-height: 300px; overflow: auto;"><code class="bd-dark"><?= htmlspecialchars(json_encode($new_data, JSON_PRETTY_PRINT)) ?: 'No new data' ?></code></pre>
                                                                                        </div>
                                                                                    </div>
                                                                                    <?php if (!empty($changed_fields)): ?>
                                                                                        <div class="row mt-3">
                                                                                            <div class="col-12">
                                                                                                <label class="text-muted small text-uppercase">Changed Fields Summary</label>
                                                                                                <pre class="bg-primary text-light p-3 rounded"><?= htmlspecialchars($changed_fields) ?></pre>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </div>
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
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>ID</th>
                                                <th>User</th>
                                                <th>Action</th>
                                                <th>Table</th>
                                                <th>Record ID</th>
                                                <th>IP Address</th>
                                                <th>Time</th>
                                                <th>Details</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>


                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Entry-->
                </div>
                <!--end::Content-->
                <?php
                include('includes/footer-inner.php');
                ?>



                <style>
                    .modal-xl {
                        max-width: 1200px;
                    }

                    .table-responsive {
                        overflow-x: auto;
                    }

                    .table td {
                        vertical-align: top;
                        word-break: break-word;
                    }

                    .badge-lg {
                        font-size: 14px;
                        font-weight: 500;
                    }

                    .bg-light {
                        background-color: #f8f9fa !important;
                    }

                    .text-break {
                        word-break: break-all;
                    }

                    pre {
                        white-space: pre-wrap;
                        word-wrap: break-word;
                        font-size: 12px;
                    }

                    .table-warning {
                        background-color: #fff3cd !important;
                    }

                    .card-header .btn-link {
                        text-decoration: none;
                        padding: 0;
                    }

                    .card-header .btn-link:hover {
                        text-decoration: underline;
                    }
                </style>