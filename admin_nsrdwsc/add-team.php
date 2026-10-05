<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);
$date = date('Y-m-d H:i:s');

// Handle form submission
if (isset($_POST['add_team'])) {
    
    // Validation array
    $errors = [];
    $upload_dir = 'assets/media/team/';
    
    // Validate required fields
    if (empty($_POST['team_name'])) {
        $errors[] = "Team name is required";
    }
    
    // Validate file upload
    if (empty($_FILES['team_img']['name'])) {
        $errors[] = "Team image is required";
    } else {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $file_type = $_FILES['team_img']['type'];
        $file_ext = strtolower(pathinfo($_FILES['team_img']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_ext)) {
            $errors[] = "Invalid image format. Allowed: JPG, PNG, WEBP, GIF";
        }
        
        if ($_FILES['team_img']['size'] > 2097152) { // 2MB
            $errors[] = "Image size should be less than 2MB";
        }
    }
    
    // If no errors, process the form
    if (empty($errors)) {
        
        // Sanitize inputs
        $team_cat_id = isset($_POST['team_cat_id']) ? intval($_POST['team_cat_id']) : 0;
        $team_desg_id = isset($_POST['team_desg_id']) ? intval($_POST['team_desg_id']) : 0;
        
        // Generate unique filename
        $file_extension = pathinfo($_FILES['team_img']['name'], PATHINFO_EXTENSION);
        $team_img = 'team_img_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        
        $team_img_alt = mysqli_real_escape_string($conn, trim($_POST['team_img_alt'] ?? ''));
        $team_inner_heading = mysqli_real_escape_string($conn, trim($_POST['team_inner_heading'] ?? ''));
        $team_name = mysqli_real_escape_string($conn, trim($_POST['team_name']));
        $team_desg = mysqli_real_escape_string($conn, trim($_POST['team_desg'] ?? ''));
        $team_orn = mysqli_real_escape_string($conn, trim($_POST['team_orn'] ?? ''));
        $team_cont = mysqli_real_escape_string($conn, $_POST['team_cont'] ?? '');
        $team_mobile = mysqli_real_escape_string($conn, trim($_POST['team_mobile'] ?? ''));
        $team_whtsapp = mysqli_real_escape_string($conn, trim($_POST['team_whtsapp'] ?? ''));
        $team_emailid = mysqli_real_escape_string($conn, trim($_POST['team_emailid'] ?? ''));
        $team_address = mysqli_real_escape_string($conn, trim($_POST['team_address'] ?? ''));
        $team_fblink = mysqli_real_escape_string($conn, trim($_POST['team_fblink'] ?? ''));
        $team_twitterlink = mysqli_real_escape_string($conn, trim($_POST['team_twitterlink'] ?? ''));
        $team_linkedinlink = mysqli_real_escape_string($conn, trim($_POST['team_linkedinlink'] ?? ''));
        $team_instagramlink = mysqli_real_escape_string($conn, trim($_POST['team_instagramlink'] ?? ''));
        $team_slug = mysqli_real_escape_string($conn, trim($_POST['team_slug'] ?? ''));
        $team_pos = isset($_POST['team_pos']) ? intval($_POST['team_pos']) : 0;
        $meta_title = mysqli_real_escape_string($conn, trim($_POST['meta_title'] ?? ''));
        $meta_keyword = mysqli_real_escape_string($conn, trim($_POST['meta_keyword'] ?? ''));
        $meta_description = mysqli_real_escape_string($conn, trim($_POST['meta_description'] ?? ''));
        $meta_tags = mysqli_real_escape_string($conn, trim($_POST['meta_tags'] ?? ''));
        $team_company_owner = isset($_POST['team_company_owner']) ? 1 : 2;
        $is_active = isset($_POST['is_active']) ? 1 : 2;
        
        // Generate slug if empty
        if (empty($team_slug)) {
            $team_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $team_name)));
        }
        
        // Insert query
        $query = "INSERT INTO tbl_team SET 
                    team_cat_id = '$team_cat_id',
                    team_desg_id = '$team_desg_id',
                    team_img = '$team_img',
                    team_img_alt = '$team_img_alt',
                    team_inner_heading = '$team_inner_heading',
                    team_name = '$team_name',
                    team_desg = '$team_desg',
                    team_orn = '$team_orn',
                    team_cont = '$team_cont',
                    team_mobile = '$team_mobile',
                    team_whtsapp = '$team_whtsapp',
                    team_emailid = '$team_emailid',
                    team_address = '$team_address',
                    team_fblink = '$team_fblink',
                    team_twitterlink = '$team_twitterlink',
                    team_linkedinlink = '$team_linkedinlink',
                    team_instagramlink = '$team_instagramlink',
                    team_slug = '$team_slug',
                    team_pos = '$team_pos',
                    meta_title = '$meta_title',
                    meta_keyword = '$meta_keyword',
                    meta_description = '$meta_description',
                    meta_tags = '$meta_tags',
                    team_company_owner = '$team_company_owner',
                    is_active = '$is_active',
                    tstp = '$date'";
        
        $result = mysqli_query($conn, $query);
        
        if ($result) {
            $team_id = mysqli_insert_id($conn);
            
            // Create directory if not exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Upload file
            $upload_path = $upload_dir . $team_img;
            if (move_uploaded_file($_FILES['team_img']['tmp_name'], $upload_path)) {
                
                // Log the activity
                $new_data = $logger->getOldRecordData('tbl_team', $team_id);
                $logger->log(
                    'INSERT',
                    'tbl_team',
                    $team_id,
                    null,
                    $new_data,
                    'New team member added: ' . $team_name
                );
                
                $_SESSION['success_message'] = "Team member added successfully!";
                header('Location: ' . BASE_URL . 'manage-team.php');
                exit;
            } else {
                // Delete the record if file upload fails
                mysqli_query($conn, "DELETE FROM tbl_team WHERE team_id = $team_id");
                $_SESSION['error_message'] = "Failed to upload image file!";
                header('Location: ' . BASE_URL . 'add-team.php');
                exit;
            }
        } else {
            $_SESSION['error_message'] = "Database error: " . mysqli_error($conn);
            header('Location: ' . BASE_URL . 'add-team.php');
            exit;
        }
    } else {
        $_SESSION['error_message'] = implode("<br>", $errors);
        header('Location: ' . BASE_URL . 'add-team.php');
        exit;
    }
}
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
                            
                            <!-- Display Messages -->
                            <?php if (isset($_SESSION['error_message'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fa fa-exclamation-triangle"></i> <?= $_SESSION['error_message'] ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <?php unset($_SESSION['error_message']); ?>
                            <?php endif; ?>

                            <!--begin::Card-->
                            <div class="card card-custom gutter-b">
                                <div class="card-header flex-wrap py-3">
                                    <div class="card-title">
                                        <h3 class="card-label">
                                            <i class="fa fa-user-plus text-primary"></i> Add Team Member
                                        </h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <a href="<?= BASE_URL ?>manage-team.php" class="btn btn-light-primary font-weight-bolder">
                                            <i class="fa fa-arrow-left"></i> Back to Manage
                                        </a>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <form action="" enctype="multipart/form-data" method="post" id="add-team" class="needs-validation" novalidate>
                                        
                                        <!-- Basic Information Section -->
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <h5 class="mb-3 pb-2 border-bottom">Basic Information</h5>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="font-weight-bold">Category</label>
                                                <select name="team_cat_id" class="form-control select2">
                                                    <option value="">Select Category</option>
                                                    <?php
                                                    $query = mysqli_query($conn, "SELECT * FROM tbl_team_cat WHERE is_active = 1 ORDER BY team_cat_name ASC");
                                                    while ($row = mysqli_fetch_array($query)) {
                                                    ?>
                                                        <option value="<?= $row['team_cat_id'] ?>"><?= ucwords($row['team_cat_name']) ?></option>
                                                    <?php } ?>
                                                </select>
                                                <small class="form-text text-muted">Select team category</small>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="font-weight-bold">Designation</label>
                                                <select name="team_desg_id" class="form-control select2">
                                                    <option value="">Select Designation</option>
                                                    <?php
                                                    $query = mysqli_query($conn, "SELECT * FROM tbl_team_desg WHERE is_active = 1 ORDER BY team_desg_name ASC");
                                                    while ($row = mysqli_fetch_array($query)) {
                                                    ?>
                                                        <option value="<?= $row['team_desg_id'] ?>"><?= ucwords($row['team_desg_name']) ?></option>
                                                    <?php } ?>
                                                </select>
                                                <small class="form-text text-muted">Select team designation</small>
                                            </div>
                                        </div>

                                        <!-- Image Section -->
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <h5 class="mb-3 pb-2 border-bottom">Profile Image</h5>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="font-weight-bold">Team Image <span class="text-danger">*</span></label>
                                                <div class="custom-file">
                                                    <input type="file" name="team_img" id="team_img" class="custom-file-input" accept="image/*" required>
                                                    <label class="custom-file-label" for="team_img">Choose file...</label>
                                                </div>
                                                <small class="form-text text-muted">Allowed: JPG, PNG, WEBP, GIF (Max: 2MB)</small>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="font-weight-bold">Image Alt Tag</label>
                                                <input type="text" name="team_img_alt" class="form-control" placeholder="e.g., Team member profile photo">
                                                <small class="form-text text-muted">SEO friendly alt text for the image</small>
                                            </div>
                                        </div>

                                        <!-- Personal Information Section -->
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <h5 class="mb-3 pb-2 border-bottom">Personal Information</h5>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <label class="font-weight-bold">Name <span class="text-danger">*</span></label>
                                                <input type="text" name="team_name" class="form-control" placeholder="Full name" required>
                                                <small class="form-text text-muted">Full name of team member</small>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <label class="font-weight-bold">Designation</label>
                                                <input type="text" name="team_desg" class="form-control" placeholder="Job title">
                                                <small class="form-text text-muted">Current job position</small>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <label class="font-weight-bold">Inner Heading</label>
                                                <input type="text" name="team_inner_heading" class="form-control" placeholder="Inner page heading">
                                                <small class="form-text text-muted">Custom heading for inner page</small>
                                            </div>
                                        </div>

                                        <!-- Contact Information Section -->
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <h5 class="mb-3 pb-2 border-bottom">Contact Information</h5>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <label class="font-weight-bold">Mobile</label>
                                                <input type="tel" name="team_mobile" class="form-control" placeholder="+971 XXXXXXXXX">
                                                <small class="form-text text-muted">Contact number</small>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <label class="font-weight-bold">WhatsApp</label>
                                                <input type="tel" name="team_whtsapp" class="form-control" placeholder="+971 XXXXXXXXX">
                                                <small class="form-text text-muted">WhatsApp number</small>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <label class="font-weight-bold">Email ID</label>
                                                <input type="email" name="team_emailid" class="form-control" placeholder="email@example.com">
                                                <small class="form-text text-muted">Email address</small>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <label class="font-weight-bold">ORN Number</label>
                                                <input type="text" name="team_orn" class="form-control" placeholder="ORN number">
                                                <small class="form-text text-muted">Official Registration Number</small>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <label class="font-weight-bold">Address</label>
                                                <input type="text" name="team_address" class="form-control" placeholder="Office address">
                                                <small class="form-text text-muted">Physical address</small>
                                            </div>
                                        </div>

                                        <!-- Social Media Section -->
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <h5 class="mb-3 pb-2 border-bottom">Social Media Links</h5>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <label class="font-weight-bold"><i class="fab fa-facebook text-primary"></i> Facebook</label>
                                                <input type="url" name="team_fblink" class="form-control" placeholder="https://facebook.com/...">
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <label class="font-weight-bold"><i class="fab fa-twitter text-info"></i> Twitter</label>
                                                <input type="url" name="team_twitterlink" class="form-control" placeholder="https://twitter.com/...">
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <label class="font-weight-bold"><i class="fab fa-linkedin text-primary"></i> LinkedIn</label>
                                                <input type="url" name="team_linkedinlink" class="form-control" placeholder="https://linkedin.com/...">
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <label class="font-weight-bold"><i class="fab fa-instagram text-danger"></i> Instagram</label>
                                                <input type="url" name="team_instagramlink" class="form-control" placeholder="https://instagram.com/...">
                                            </div>
                                        </div>

                                        <!-- Content Section -->
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <h5 class="mb-3 pb-2 border-bottom">Biography</h5>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <label class="font-weight-bold">Content / Biography</label>
                                                <textarea name="team_cont" id="kt-tinymce-2" class="form-control" rows="10"></textarea>
                                                <small class="form-text text-muted">Detailed biography of team member</small>
                                            </div>
                                        </div>

                                        <!-- SEO Section -->
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <h5 class="mb-3 pb-2 border-bottom">SEO Information</h5>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="font-weight-bold">Slug</label>
                                                <input type="text" name="team_slug" class="form-control" placeholder="auto-generated-from-name">
                                                <small class="form-text text-muted">URL-friendly version (leave empty for auto-generation)</small>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="font-weight-bold">Position / Order</label>
                                                <input type="number" name="team_pos" class="form-control" placeholder="0">
                                                <small class="form-text text-muted">Display order (lower number = higher position)</small>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <label class="font-weight-bold">Meta Title</label>
                                                <input type="text" name="meta_title" class="form-control" placeholder="Meta title for SEO" maxlength="160">
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <label class="font-weight-bold">Meta Keywords</label>
                                                <input type="text" name="meta_keyword" class="form-control" placeholder="keyword1, keyword2, keyword3">
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <label class="font-weight-bold">Meta Description</label>
                                                <textarea name="meta_description" class="form-control" rows="3" placeholder="Meta description for SEO" maxlength="320"></textarea>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <label class="font-weight-bold">Meta Tags</label>
                                                <textarea name="meta_tags" class="form-control" rows="3" placeholder="Additional meta tags"></textarea>
                                            </div>
                                        </div>

                                        <!-- Options Section -->
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <h5 class="mb-3 pb-2 border-bottom">Options</h5>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="team_company_owner" value="1" class="custom-control-input" id="companyOwner">
                                                    <label class="custom-control-label font-weight-bold" for="companyOwner">Company Owner</label>
                                                </div>
                                                <small class="form-text text-muted">Mark if this person is a company owner</small>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="font-weight-bold">Status</label>
                                                <div class="mt-2">
                                                    <span class="switch switch-md switch-icon">
                                                        <label>
                                                            <input type="checkbox" name="is_active" value="1" checked>
                                                            <span></span>
                                                        </label>
                                                    </span>
                                                </div>
                                                <small class="form-text text-muted">Toggle to activate/deactivate</small>
                                            </div>
                                        </div>

                                        <!-- Form Actions -->
                                        <div class="card-footer px-0">
                                            <button type="submit" name="add_team" class="btn btn-primary px-5">
                                                <i class="fa fa-save"></i> Add Team Member
                                            </button>
                                            <button type="reset" class="btn btn-secondary px-5 ml-2">
                                                <i class="fa fa-undo"></i> Reset
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include('includes/footer-inner.php'); ?>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true
        });
        
        // Custom file input
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
        
        // Auto-generate slug from name
        $('input[name="team_name"]').on('keyup', function() {
            var name = $(this).val();
            var slug = name.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/--+/g, '-')
                .trim();
            if ($('input[name="team_slug"]').val() === '') {
                $('input[name="team_slug"]').val(slug);
            }
        });
    });
    </script>

    <style>
        .border-bottom {
            border-bottom: 2px solid #e9ecef !important;
            padding-bottom: 8px;
        }
        .custom-file-label::after {
            content: "Browse";
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label.font-weight-bold {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
    </style>
</body>