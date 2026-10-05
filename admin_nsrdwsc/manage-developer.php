<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);

// Handle deletion with proper error handling
if (isset($_GET['del'])) {
    try {
        $id = intval($_GET['del']);
        
        // Fetch developer data
        $result = mysqli_query($conn, "SELECT * FROM tbl_developer WHERE dev_id = $id");
        if (!$result) {
            throw new Exception("Failed to fetch developer data");
        }
        
        $img_name = mysqli_fetch_assoc($result);
        $delete_name = $img_name['dev_heading'] ?? 'Unknown Developer';
        
        // Delete images with error suppression
        $images_to_delete = [
            'assets/media/developer/' . ($img_name['dev_img'] ?? ''),
            'assets/media/developer/choose-us/' . ($img_name['choose_us_img'] ?? ''),
            'assets/media/developer/homepage-image/' . ($img_name['dev_homp_img'] ?? ''),
            'assets/media/developer/inner-image/' . ($img_name['dev_innr_img'] ?? '')
        ];
        
        foreach ($images_to_delete as $image_path) {
            if (!empty($image_path) && file_exists($image_path)) {
                @unlink($image_path);
            }
        }
        
        // Log the deletion
        $old_data = $logger->getOldRecordData('tbl_developer', $id);
        $logger->log('DELETE', 'tbl_developer', $id, $old_data, null, 'Developer Deleted: ' . $delete_name);
        $logger->archiveDeletedRecord('tbl_developer', $id, $old_data);
        
        // Delete from database
        if (!mysqli_query($conn, "DELETE FROM tbl_developer WHERE dev_id = $id")) {
            throw new Exception("Database deletion failed: " . mysqli_error($conn));
        }
        
        // Set success message
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => "Developer '{$delete_name}' deleted successfully!"
        ];
        
    } catch (Exception $e) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => $e->getMessage()
        ];
    }
    
    header('Location: ' . BASE_URL . 'manage-developer.php');
    exit();
}

// Handle flash messages
$flash_message = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);

// Handle push success message
$push_success = isset($_GET['msg']) && $_GET['msg'] == 'push_success';
?>

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
    
    <?php include('includes/topbar-mobile.php'); ?>
    
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-row flex-column-fluid page">
            <?php include('includes/sidebar.php'); ?>
            
            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                <?php include('includes/topbar-desktop.php'); ?>
                
                <!-- Flash Messages -->
                <?php if ($flash_message): ?>
                    <div class="alert alert-<?= $flash_message['type'] ?> alert-dismissible fade show mx-5 mt-3" role="alert">
                        <i class="fas fa-<?= $flash_message['type'] == 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i>
                        <?= htmlspecialchars($flash_message['message']) ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php if ($push_success): ?>
                    <div class="alert alert-success alert-dismissible fade show mx-5 mt-3" role="alert">
                        <i class="fas fa-cloud-upload-alt mr-2"></i>
                        Developer(s) pushed to app successfully!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <!-- AJAX Response Container -->
                <div id="ajaxResponse" class="mx-5 mt-3" style="display:none;"></div>
                
                <!-- Content -->
                <div class="d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="d-flex flex-column-fluid">
                        <div class="container">
                            
                            <form method="POST" id="pushForm" action="<?= BASE_URL ?>push-app/push-developer-app.php">
                                <input type="hidden" name="push_to_app" value="1">
                                <div class="card card-custom gutter-b">
                                    <div class="card-header flex-wrap py-3">
                                        <div class="card-title">
                                            <h3 class="card-label">
                                                <i class="fas fa-building text-primary mr-2"></i>
                                                Manage Developers
                                            </h3>
                                        </div>
                                        
                                        <div class="card-toolbar">
                                            <div class="dropdown dropdown-inline mr-2">
                                                <button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fas fa-file-export mr-1"></i> Export
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                    <a href="<?= BASE_URL ?>export-developer.php" class="dropdown-item">
                                                        <i class="fas fa-file-excel mr-2"></i> Export to XLS
                                                    </a>
                                                </div>
                                            </div>
                                            
                                            <a href="<?= BASE_URL ?>add-developer.php" class="btn btn-primary font-weight-bolder">
                                                <i class="fas fa-plus-circle mr-2"></i> Add Developer
                                            </a>
                                            
                                            <button type="submit" class="btn btn-success font-weight-bolder ml-2" id="pushToAppBtn">
                                                <i class="fas fa-cloud-upload-alt mr-2"></i> Push To App
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body">
                                        <!-- Filters -->
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="form-group mb-0">
                                                    <label class="font-weight-bold">Filter by Homepage</label>
                                                    <select class="form-control" id="filterHomeDeveloper" onchange="window.location.href='?filter_home='+this.value">
                                                        <option value="0" <?= (isset($_GET['filter_home']) && $_GET['filter_home'] == '0') ? 'selected' : '' ?>>All Developers</option>
                                                        <option value="1" <?= (isset($_GET['filter_home']) && $_GET['filter_home'] == '1') ? 'selected' : '' ?>>Home Page Only</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-8 text-right">
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAll()">
                                                    <i class="fas fa-check-double mr-1"></i> Select All
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="deselectAll()">
                                                    <i class="fas fa-times mr-1"></i> Deselect All
                                                </button>
                                                <span class="ml-3 text-muted" id="selectedCount">0 selected</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Table -->
                                        <div class="table-responsive">
                                            <table id="example" class="table table-bordered table-hover" style="width:100%">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th width="50">
                                                            <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                                                        </th>
                                                        <th width="120">App Sync</th>
                                                        <th width="50">ID</th>
                                                        <th width="80">Image</th>
                                                        <th>Heading</th>
                                                        <th>Short Description</th>
                                                        <th width="100">Actions</th>
                                                        <th width="80">View</th>
                                                        <th width="80">Status</th>
                                                        <th width="80">Edit</th>
                                                        <th width="80">Delete</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $key = 0;
                                                    $filter_home = isset($_GET['filter_home']) ? intval($_GET['filter_home']) : 0;
                                                    
                                                    $where_clause = $filter_home == 1 ? "WHERE dev_showonhomepage = 1" : "";
                                                    $query = mysqli_query($conn, "
                                                        SELECT * FROM tbl_developer 
                                                        {$where_clause} 
                                                        ORDER BY dev_id DESC, dev_id DESC
                                                    ");
                                                    
                                                    while ($row = mysqli_fetch_assoc($query)):
                                                    ?>
                                                        <tr>
                                                            <td class="text-center">
                                                                <input type="checkbox" name="developer_ids[]" 
                                                                       value="<?= $row['dev_id'] ?>" class="developer-checkbox"
                                                                       onchange="updateSelectedCount()">
                                                            </td>
                                                            <td>
                                                                <?php if (isset($row['app_sync_status']) && $row['app_sync_status'] == 1): ?>
                                                                    <span class="badge badge-success">
                                                                        <i class="fas fa-check-circle"></i> Pushed
                                                                    </span>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        <?= date('d M Y h:i A', strtotime($row['app_sync_date'])) ?>
                                                                    </small>
                                                                <?php else: ?>
                                                                    <span class="badge badge-warning">
                                                                        <i class="fas fa-clock text-light" style="font-size: 12px;"></i> Pending
                                                                    </span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= ++$key ?></td>
                                                            <td>
                                                                <img src="<?= BASE_URL ?>assets/media/developer/<?= $row['dev_img'] ?>" 
                                                                     alt="<?= htmlspecialchars($row['dev_heading']) ?>"
                                                                     width="50" height="50" class="img-thumbnail" style="object-fit:cover;">
                                                            </td>
                                                            <td>
                                                                <strong><?= htmlspecialchars($row['dev_heading']) ?></strong>
                                                                <?php if ($row['dev_showonhomepage'] == 1): ?>
                                                                    <span class="badge badge-pill badge-warning ml-1">
                                                                        <i class="fas fa-home"></i> Home <?= $row['dev_order_no'] ?>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= htmlspecialchars(substr($row['dev_short_descp'], 0, 50)) . (strlen($row['dev_short_descp']) > 50 ? '...' : '') ?></td>
                                                            <td>
                                                                <a href="<?= BASE_URL ?>manage-chooseus.php?dev_id=<?= $row['dev_id'] ?>" 
                                                                   class="btn btn-sm btn-info">
                                                                    <i class="fas fa-handshake"></i> Choose Us
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="<?= BASE_URL_HOME ?>developer/<?= $row['dev_slug'] ?>" 
                                                                   target="_blank" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-pill badge-<?= $row['is_active'] == 1 ? 'success' : 'danger' ?>">
                                                                    <?= $row['is_active'] == 1 ? 'Active' : 'Inactive' ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="<?= BASE_URL ?>edit-developer.php?edit=<?= base64_encode($row['dev_id']) ?>" 
                                                                   class="btn btn-sm btn-warning">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="<?= BASE_URL ?>manage-developer.php?del=<?= base64_encode($row['dev_id']) ?>" 
                                                                   class="btn btn-sm btn-danger delete-btn"
                                                                   data-name="<?= htmlspecialchars($row['dev_heading']) ?>">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                </div>
                
                <?php include('includes/footer-inner.php'); ?>
            </div>
        </div>
    </div>
    
    <script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Delete confirmation
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete "' + $(this).data('name') + '"?')) {
            window.location.href = $(this).attr('href');
        }
    });
    
    // AJAX Form Submission
    $('#pushForm').on('submit', function(e) {
        e.preventDefault();
        
        var checkboxes = $('.developer-checkbox:checked');
        if (checkboxes.length === 0) {
            showAlert('warning', 'Please select at least one developer to push.');
            return false;
        }
        
        if (!confirm('Are you sure you want to push ' + checkboxes.length + ' developer(s) to the app?')) {
            return false;
        }
        
        // Disable button and show loading
        var $btn = $('#pushToAppBtn');
        $btn.prop('disabled', true);
        $btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Pushing...');
        
        // Get form data
        var formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.status === 'success') {
                    showAlert('success', response.message);
                    // Reload page after 2 seconds to update status
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else if (response.status === 'partial') {
                    showAlert('warning', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                } else {
                    showAlert('danger', response.message || 'Failed to push developers.');
                }
            },
            error: function(xhr, status, error) {
                showAlert('danger', 'An error occurred: ' + error);
            },
            complete: function() {
                $btn.prop('disabled', false);
                $btn.html('<i class="fas fa-cloud-upload-alt mr-2"></i> Push To App');
            }
        });
    });
});

// Show alert function
function showAlert(type, message) {
    var alertClass = '';
    var icon = '';
    
    switch(type) {
        case 'success':
            alertClass = 'alert-success';
            icon = 'fa-check-circle';
            break;
        case 'warning':
            alertClass = 'alert-warning';
            icon = 'fa-exclamation-triangle';
            break;
        case 'danger':
            alertClass = 'alert-danger';
            icon = 'fa-exclamation-circle';
            break;
        default:
            alertClass = 'alert-info';
            icon = 'fa-info-circle';
    }
    
    var $response = $('#ajaxResponse');
    $response.html(
        '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
            '<i class="fas ' + icon + ' mr-2"></i> ' + message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
            '</button>' +
        '</div>'
    ).fadeIn();
    
    // Auto hide after 5 seconds
    setTimeout(function() {
        $response.fadeOut('slow');
    }, 5000);
}

// Select/Deselect all functions
function toggleAll(source) {
    $('.developer-checkbox').prop('checked', $(source).prop('checked'));
    updateSelectedCount();
}

function selectAll() {
    $('.developer-checkbox').prop('checked', true);
    $('#selectAll').prop('checked', true);
    updateSelectedCount();
}

function deselectAll() {
    $('.developer-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false);
    updateSelectedCount();
}

function updateSelectedCount() {
    var count = $('.developer-checkbox:checked').length;
    $('#selectedCount').text(count + ' selected');
}
</script>
