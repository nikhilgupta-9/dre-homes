<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');

// Only admin can access
if ($_SESSION['login_type'] != 1) {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit;
}

// Handle permanent deletion
if (isset($_GET['permanent_delete'])) {
    $delete_id = intval($_GET['permanent_delete']);

    if ($delete_id > 0) {
        $delete_query = "DELETE FROM tbl_deleted_records WHERE delete_id = $delete_id";
        if (mysqli_query($conn, $delete_query)) {
            $_SESSION['success_message'] = "Deleted record removed permanently!";
        } else {
            $_SESSION['error_message'] = "Failed to remove record!";
        }
    }
    header('Location: ' . BASE_URL . 'manage-delete-logs.php');
    exit;
}

// Handle bulk permanent deletion
if (isset($_POST['bulk_delete']) && isset($_POST['delete_ids'])) {
    $delete_ids = $_POST['delete_ids'];
    if (!empty($delete_ids)) {
        $ids_list = implode(',', array_map('intval', $delete_ids));
        $delete_query = "DELETE FROM tbl_deleted_records WHERE delete_id IN ($ids_list)";
        if (mysqli_query($conn, $delete_query)) {
            $count = mysqli_affected_rows($conn);
            $_SESSION['success_message'] = "$count deleted record(s) removed permanently!";
        } else {
            $_SESSION['error_message'] = "Failed to remove records!";
        }
    }
    header('Location: ' . BASE_URL . 'manage-delete-logs.php');
    exit;
}

// Handle restore (coming soon feature)
if (isset($_GET['restore'])) {
    $delete_id = intval($_GET['restore']);
    // Restore functionality can be implemented later
    $_SESSION['info_message'] = "Restore feature coming soon!";
    header('Location: ' . BASE_URL . 'manage-delete-logs.php');
    exit;
}

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total_deleted,
    COUNT(DISTINCT original_table) as unique_tables,
    COUNT(DISTINCT deleted_by_name) as unique_deleters,
    MAX(deleted_at) as last_deleted
FROM tbl_deleted_records";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Get unique tables for filter
$tables_query = mysqli_query($conn, "SELECT DISTINCT original_table FROM tbl_deleted_records ORDER BY original_table ASC");

// Get unique deleters for filter
$deleters_query = mysqli_query($conn, "SELECT DISTINCT deleted_by_name FROM tbl_deleted_records ORDER BY deleted_by_name ASC");
?>

<!--begin::Body-->

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
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
                                            <h5 class="mb-1">Total Deleted</h5>
                                            <h3 class="mb-0"><?= number_format($stats['total_deleted']) ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body p-3">
                                            <h5 class="mb-1">Tables Affected</h5>
                                            <h3 class="mb-0"><?= number_format($stats['unique_tables']) ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body p-3">
                                            <h5 class="mb-1">Users Deleted</h5>
                                            <h3 class="mb-0"><?= number_format($stats['unique_deleters']) ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card text-center bg-primary text-white">
                                        <div class="card-body p-3">
                                            <h5 class="mb-1">Last Deleted</h5>
                                            <h3 class="mb-0 small"><?= date('d-m-Y', strtotime($stats['last_deleted'])) ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Deleted Records Table Card -->
                            <div class="card card-custom gutter-b">
                                <div class="card-header flex-wrap py-3">
                                    <div class="card-title">
                                        <h3 class="card-label">
                                            <i class="fa fa-trash text-danger"></i> Deleted Records
                                        </h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <button type="button" class="btn btn-light-primary font-weight-bolder mr-2" onclick="window.location.reload();">
                                            <i class="bi bi-arrow-clockwise text-light"></i> Refresh
                                        </button>
                                        <button type="button" class="btn btn-danger font-weight-bolder" id="bulkDeleteBtn">
                                            <i class="bi bi-trash"></i> Delete Selected
                                        </button>
                                    </div>
                                </div>

                                <!-- Filter Section -->
                                <div class="card-body pb-0">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Filter by Table:</label>
                                            <select id="tableFilter" class="form-control">
                                                <option value="">All Tables</option>
                                                <?php while ($table = mysqli_fetch_assoc($tables_query)): ?>
                                                    <option value="<?= htmlspecialchars($table['original_table']) ?>"><?= htmlspecialchars($table['original_table']) ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Filter by User:</label>
                                            <select id="userFilter" class="form-control">
                                                <option value="">All Users</option>
                                                <?php while ($deleter = mysqli_fetch_assoc($deleters_query)): ?>
                                                    <option value="<?= htmlspecialchars($deleter['deleted_by_name']) ?>"><?= htmlspecialchars($deleter['deleted_by_name']) ?></option>
                                                <?php endwhile; ?>
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
                                            <button class="btn btn-secondary w-100" id="resetFilters">
                                                <i class="bi bi-eraser"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="deletedRecordsTable" class="table table-bordered dataTable" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="3%"><input type="checkbox" id="selectAll"></th>
                                                    <th width="5%">ID</th>
                                                    <th width="15%">Table Name</th>
                                                    <th width="8%">Record ID</th>
                                                    <th width="15%">Deleted By</th>
                                                    <th width="12%">IP Address</th>
                                                    <th width="12%">Deleted At</th>
                                                    <th width="15%">Deleted Data</th>
                                                    <th width="5%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $query = mysqli_query($conn, "SELECT * FROM tbl_deleted_records ORDER BY delete_id DESC");
                                                while ($row = mysqli_fetch_assoc($query)):
                                                    $deleted_data = json_decode($row['deleted_data'], true);
                                                ?>
                                                    <tr data-table="<?= htmlspecialchars($row['original_table']) ?>"
                                                        data-user="<?= htmlspecialchars($row['deleted_by_name']) ?>"
                                                        data-date="<?= $row['deleted_at'] ?>">
                                                        <td class="text-center">
                                                            <input type="checkbox" class="recordCheckbox" value="<?= $row['delete_id'] ?>">
                                                        </td>
                                                        <td><?= $row['delete_id'] ?></td>
                                                        <td>
                                                            <span class="badge badge-secondary"><?= htmlspecialchars($row['original_table']) ?></span>
                                                        </td>
                                                        <td><?= $row['record_id'] ?></td>
                                                        <td>
                                                            <strong><?= htmlspecialchars($row['deleted_by_name']) ?></strong>
                                                            <br>
                                                            <small class="text-muted">ID: <?= $row['deleted_by'] ?></small>
                                                        </td>
                                                        <td><code><?= htmlspecialchars($row['ip_address']) ?></code></td>
                                                        <td>
                                                            <span class="text-info"><?= date('d-m-Y H:i:s', strtotime($row['deleted_at'])) ?></span>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-primary view-data"
                                                                data-data='<?= htmlspecialchars($row['deleted_data'], ENT_QUOTES, 'UTF-8') ?>'
                                                                data-table="<?= htmlspecialchars($row['original_table']) ?>"
                                                                data-id="<?= $row['record_id'] ?>">
                                                                <i class="fa fa-eye"></i> View
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="button" class="btn btn-danger delete-single" data-id="<?= $row['delete_id'] ?>" title="Permanent Delete">
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
                                <strong><span id="selectedCount">0</span></strong> record(s) selected
                            </div>
                            <div>
                                <button class="btn btn-sm btn-danger" id="confirmBulkDelete">
                                    <i class="fa fa-trash"></i> Delete Permanently
                                </button>
                                <button class="btn btn-sm btn-secondary ml-2" id="clearSelection">
                                    <i class="fa fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Data Modal -->
                <div class="modal fade" id="viewDataModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">
                                    <i class="fa fa-database"></i> Deleted Record Details
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Original Table:</strong>
                                        <p id="modalTableName" class="mt-1"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Record ID:</strong>
                                        <p id="modalRecordId" class="mt-1"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <strong>Deleted Data (JSON):</strong>
                                    <pre id="modalDeletedData" style="max-height: 400px; overflow: auto; background: #f4f4f4; padding: 10px; border-radius: 4px; font-size: 12px;"></pre>
                                </div>
                                <div class="form-group">
                                    <strong>Formatted Data:</strong>
                                    <div id="modalFormattedData" style="max-height: 400px; overflow: auto; background: #f8f9fa; padding: 10px; border-radius: 4px;"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        // Initialize DataTable
                        var table = $('#deletedRecordsTable').DataTable({
                            "pageLength": 25,
                            "order": [
                                [0, 'desc']
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
                                    "targets": [0, 7, 8]
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
                                    "width": "15%",
                                    "targets": 4
                                },
                                {
                                    "width": "12%",
                                    "targets": 5
                                },
                                {
                                    "width": "12%",
                                    "targets": 6
                                },
                                {
                                    "width": "15%",
                                    "targets": 7
                                },
                                {
                                    "width": "5%",
                                    "targets": 8
                                }
                            ]
                        });

                        // Custom filter function
                        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                            var tableFilter = $('#tableFilter').val();
                            var userFilter = $('#userFilter').val();
                            var startDate = $('#startDate').val();
                            var endDate = $('#endDate').val();

                            var row = table.row(dataIndex).node();
                            var rowTable = $(row).data('table');
                            var rowUser = $(row).data('user');
                            var rowDate = $(row).data('date');

                            if (tableFilter !== '' && rowTable !== tableFilter) return false;
                            if (userFilter !== '' && rowUser !== userFilter) return false;

                            if (startDate !== '' && rowDate < startDate) return false;
                            if (endDate !== '' && rowDate > endDate + ' 23:59:59') return false;

                            return true;
                        });

                        // Filter events
                        $('#tableFilter, #userFilter, #startDate, #endDate').on('change', function() {
                            table.draw();
                        });

                        $('#resetFilters').on('click', function() {
                            $('#tableFilter').val('');
                            $('#userFilter').val('');
                            $('#startDate').val('');
                            $('#endDate').val('');
                            table.draw();
                        });

                        // Select All functionality
                        $('#selectAll').on('change', function() {
                            var isChecked = $(this).prop('checked');
                            $('.recordCheckbox').prop('checked', isChecked);
                            updateBulkActionsBar();
                        });

                        $(document).on('change', '.recordCheckbox', function() {
                            updateBulkActionsBar();
                            var allChecked = $('.recordCheckbox:checked').length === $('.recordCheckbox').length;
                            $('#selectAll').prop('checked', allChecked);
                        });

                        function updateBulkActionsBar() {
                            var selected = $('.recordCheckbox:checked').length;
                            $('#selectedCount').text(selected);
                            if (selected > 0) {
                                $('#bulkActionsBar').fadeIn();
                            } else {
                                $('#bulkActionsBar').fadeOut();
                            }
                        }

                        // View data
                        // View data
                        $(document).on('click', '.view-data', function() {
                            var deletedData = $(this).data('data');
                            var tableName = $(this).data('table');
                            var recordId = $(this).data('id');

                            $('#modalTableName').html('<code>' + tableName + '</code>');
                            $('#modalRecordId').html('<strong>#' + recordId + '</strong>');

                            // Safely parse JSON
                            var parsedData = null;
                            var displayData = '';
                            var formattedHtml = '';

                            try {
                                // Check if data is already an object or needs parsing
                                if (typeof deletedData === 'object') {
                                    parsedData = deletedData;
                                    displayData = JSON.stringify(parsedData, null, 2);
                                } else if (typeof deletedData === 'string') {
                                    // Try to parse as JSON
                                    parsedData = JSON.parse(deletedData);
                                    displayData = JSON.stringify(parsedData, null, 2);
                                } else {
                                    displayData = String(deletedData);
                                    parsedData = {
                                        'raw_data': displayData
                                    };
                                }

                                $('#modalDeletedData').text(displayData);

                                // Format data as HTML table
                                if (parsedData && typeof parsedData === 'object') {
                                    var html = '<table class="table table-sm table-bordered"><thead class="bg-light"><tr><th>Field</th><th>Value</th></tr></thead><tbody>';
                                    for (var key in parsedData) {
                                        if (parsedData.hasOwnProperty(key)) {
                                            var value = parsedData[key];
                                            if (typeof value === 'object') {
                                                value = JSON.stringify(value);
                                            }
                                            html += '<tr>';
                                            html += '<td><strong>' + key.replace(/_/g, ' ').toUpperCase() + '</strong></td>';
                                            html += '<td>' + (value || '<em class="text-muted">empty</em>') + '</td>';
                                            html += '</tr>';
                                        }
                                    }
                                    html += '</tbody></table>';
                                    $('#modalFormattedData').html(html);
                                } else {
                                    $('#modalFormattedData').html('<pre>' + displayData + '</pre>');
                                }

                            } catch (e) {
                                console.error('JSON Parse Error:', e);
                                console.log('Raw data:', deletedData);

                                // Display raw data as is
                                $('#modalDeletedData').text(String(deletedData));
                                $('#modalFormattedData').html('<div class="alert alert-warning">Unable to parse deleted data. Raw data shown above.</div>');
                            }

                            $('#viewDataModal').modal('show');
                        });
                        // Single delete with SweetAlert
                        $(document).on('click', '.delete-single', function() {
                            var deleteId = $(this).data('id');

                            Swal.fire({
                                title: 'Permanent Delete',
                                text: 'Are you sure you want to permanently delete this record? This action cannot be undone!',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Yes, delete it!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '<?= BASE_URL ?>manage-delete-logs.php?permanent_delete=' + deleteId;
                                }
                            });
                        });

                        // Bulk delete button
                        $('#bulkDeleteBtn').on('click', function() {
                            var selected = $('.recordCheckbox:checked').length;
                            if (selected === 0) {
                                Swal.fire({
                                    title: 'No Selection',
                                    text: 'Please select records to delete',
                                    icon: 'warning',
                                    confirmButtonColor: '#3085d6'
                                });
                                return;
                            }
                            $('#confirmBulkDelete').click();
                        });

                        // Confirm bulk delete with SweetAlert
                        $('#confirmBulkDelete').on('click', function() {
                            var selectedIds = [];
                            $('.recordCheckbox:checked').each(function() {
                                selectedIds.push($(this).val());
                            });

                            if (selectedIds.length === 0) {
                                Swal.fire({
                                    title: 'No Selection',
                                    text: 'No records selected',
                                    icon: 'warning',
                                    confirmButtonColor: '#3085d6'
                                });
                                return;
                            }

                            Swal.fire({
                                title: 'Bulk Delete',
                                html: 'Are you sure you want to permanently delete <strong>' + selectedIds.length + '</strong> records? This action cannot be undone!',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Yes, delete all!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    var form = $('<form method="post" action="<?= BASE_URL ?>manage-delete-logs.php"></form>');
                                    form.append('<input type="hidden" name="bulk_delete" value="1">');
                                    $.each(selectedIds, function(i, id) {
                                        form.append('<input type="hidden" name="delete_ids[]" value="' + id + '">');
                                    });
                                    $('body').append(form);
                                    form.submit();
                                }
                            });
                        });

                        // Clear selection
                        $('#clearSelection').on('click', function() {
                            $('.recordCheckbox').prop('checked', false);
                            $('#selectAll').prop('checked', false);
                            updateBulkActionsBar();
                        });

                        // Clear bulk selection from bar
                        $('#clearSelectionBtn, #clearSelection').on('click', function() {
                            $('.recordCheckbox').prop('checked', false);
                            $('#selectAll').prop('checked', false);
                            updateBulkActionsBar();
                        });
                    });
                </script>

              