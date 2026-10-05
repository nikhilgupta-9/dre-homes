<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$err[] = '';
$id = base64_decode($_GET['edit']);
$date = date('Y-m-d H:i:s');

// Helper: Create directory if not exists
function createDirectoryIfNotExists($path) {
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

if (isset($_POST['update_brand_video_content'])) {

    $brand_video_heading  = mysqli_real_escape_string($conn, prepStr($_POST['brand_video_heading']));
    $brand_video_content  = prepStr($_POST['brand_video_content']);
    $brand_video_youtube  = mysqli_real_escape_string($conn, trim($_POST['brand_video_youtube']));
    $video_source         = mysqli_real_escape_string($conn, trim($_POST['video_source']));
    $is_active            = (!empty($_POST['is_active']) ? 1 : 2);

    $allowed_image_ext = array("png", "jpg", "jpeg", "webp");
    $allowed_video_ext = array("mp4", "webm", "ogg", "mov", "avi", "mkv");
    $max_video_size    = 100 * 1024 * 1024; // 100MB

    $base_path      = $_SERVER['DOCUMENT_ROOT'] . '/dre_homes_new/';
    $img_dir        = $base_path . 'admin_nsrdwsc/assets/media/brand-video/';
    $video_dir      = $base_path . 'admin_nsrdwsc/assets/media/brand-video/videos/';

    createDirectoryIfNotExists($img_dir);
    createDirectoryIfNotExists($video_dir);

    // ---------- Main Image ----------
    if (!empty($_FILES['brand_video_cont_img']['name'])) {
        $ext = strtolower(pathinfo($_FILES['brand_video_cont_img']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_image_ext)) {
            $err[] = "Invalid main image format. Allowed: PNG, JPG, JPEG, WEBP.";
        } else {
            $brand_video_cont_img = 'brand_video_img_' . rand(0,10000) . '_' . time() . '.' . $ext;
        }
    } else {
        $brand_video_cont_img = $_POST['brand_video_cont_img_hidden'];
    }

    // ---------- YouTube Thumbnail ----------
    if (!empty($_FILES['brand_video_youtube_img']['name'])) {
        $ext = strtolower(pathinfo($_FILES['brand_video_youtube_img']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_image_ext)) {
            $err[] = "Invalid thumbnail image format. Allowed: PNG, JPG, JPEG, WEBP.";
        } else {
            $brand_video_youtube_img = 'brand_video_yt_img_' . rand(0,10000) . '_' . time() . '.' . $ext;
        }
    } else {
        $brand_video_youtube_img = $_POST['brand_video_youtube_img_hidden'];
    }

    // ---------- Local Video ----------
    $brand_video_2      = $_POST['brand_video_2_hidden'] ?? '';
    $video_upload_error = false;

    if (!empty($_FILES['brand_video_2']['name'])) {
        $ext        = strtolower(pathinfo($_FILES['brand_video_2']['name'], PATHINFO_EXTENSION));
        $video_size = $_FILES['brand_video_2']['size'];

        if (!in_array($ext, $allowed_video_ext)) {
            $err[] = "Invalid video format: .$ext. Allowed: MP4, WEBM, OGG, MOV, AVI, MKV.";
            $video_upload_error = true;
        } elseif ($video_size > $max_video_size) {
            $err[] = "Video file exceeds 100MB limit. Current size: " . round($video_size / (1024*1024), 2) . "MB.";
            $video_upload_error = true;
        } else {
            $brand_video_2 = 'brand_video_2_' . rand(0,10000) . '_' . time() . '.' . $ext;
        }
    }

    // ---------- Process if no errors ----------
    if (count($err) == 1 && !$video_upload_error) {

        // Upload main image
        if (!empty($_FILES['brand_video_cont_img']['name'])) {
            if (move_uploaded_file($_FILES['brand_video_cont_img']['tmp_name'], $img_dir . $brand_video_cont_img)) {
                $old = $_POST['brand_video_cont_img_hidden'];
                if (!empty($old) && file_exists($img_dir . $old)) unlink($img_dir . $old);
            } else {
                $err[] = "Failed to upload main image.";
            }
        }

        // Upload YouTube thumbnail
        if (!empty($_FILES['brand_video_youtube_img']['name'])) {
            if (move_uploaded_file($_FILES['brand_video_youtube_img']['tmp_name'], $img_dir . $brand_video_youtube_img)) {
                $old = $_POST['brand_video_youtube_img_hidden'];
                if (!empty($old) && file_exists($img_dir . $old)) unlink($img_dir . $old);
            } else {
                $err[] = "Failed to upload YouTube thumbnail.";
            }
        }

        // Upload local video
        if (!empty($_FILES['brand_video_2']['name'])) {
            if (move_uploaded_file($_FILES['brand_video_2']['tmp_name'], $video_dir . $brand_video_2)) {
                $old = $_POST['brand_video_2_hidden'];
                if (!empty($old) && $old !== $brand_video_2 && file_exists($video_dir . $old)) unlink($video_dir . $old);
            } else {
                $err[] = "Failed to upload local video.";
                $brand_video_2 = $_POST['brand_video_2_hidden'];
            }
        }

        if (count($err) == 1) {
            $db_img    = mysqli_real_escape_string($conn, basename($brand_video_cont_img));
            $db_yt_img = mysqli_real_escape_string($conn, basename($brand_video_youtube_img));
            $db_video  = mysqli_real_escape_string($conn, basename($brand_video_2));

            $query = mysqli_query($conn, "UPDATE tbl_brand_video_content SET
                brand_video_cont_img    = '$db_img',
                brand_video_youtube_img = '$db_yt_img',
                brand_video_youtube     = '$brand_video_youtube',
                brand_video_heading     = '$brand_video_heading',
                brand_video_content     = '$brand_video_content',
                brand_video_2           = '$db_video',
                video_source            = '$video_source',
                is_active               = '$is_active',
                tstp                    = '$date'
                WHERE brand_video_cont_id = $id");

            if ($query) {
                header('Location:' . BASE_URL . 'edit-brand-video-content.php?edit=' . $_GET['edit'] . '&success=1');
                exit();
            } else {
                $err[] = "Database update failed: " . mysqli_error($conn);
            }
        }
    }
}

$details = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM tbl_brand_video_content WHERE brand_video_cont_id = $id"));
$current_source = $details['video_source'] ?? 'local';
?>

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">

    <!-- Header Mobile -->
    <div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
        <a href="index.html">
            <img alt="Logo" src="<?= BASE_URL ?>assets/media/logos/logo-light.png" />
        </a>
        <div class="d-flex align-items-center">
            <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle"><span></span></button>
            <button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle"><span></span></button>
            <button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle">
                <span class="svg-icon svg-icon-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24"/>
                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                        </g>
                    </svg>
                </span>
            </button>
        </div>
    </div>

    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-row flex-column-fluid page">

            <?php include('includes/sidebar.php'); ?>

            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">

                <!-- Header -->
                <div id="kt_header" class="header header-fixed">
                    <div class="container-fluid d-flex align-items-stretch justify-content-between">
                        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                            <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default"></div>
                        </div>
                        <div class="topbar">
                            <div class="topbar-item">
                                <div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
                                    <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                                    <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= $_SESSION['username'] ?></span>
                                    <span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
                                        <span class="symbol-label font-size-h5 font-weight-bold"><?= substr($_SESSION['username'], 0, 1) ?></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="d-flex flex-column-fluid">
                        <div class="container">

                            <!-- Alerts -->
                            <?php if (isset($_GET['success'])): ?>
                                <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                                    <strong>Success!</strong> Brand video content updated successfully.
                                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                </div>
                            <?php endif; ?>

                            <?php if (count($err) > 1): ?>
                                <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
                                    <strong>Please fix the following errors:</strong>
                                    <ul class="mb-0 mt-2">
                                        <?php for ($i = 1; $i < count($err); $i++): ?>
                                            <?php if (!empty($err[$i])): ?>
                                                <li><?= htmlspecialchars($err[$i]) ?></li>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                </div>
                            <?php endif; ?>

                            <!-- Main Card -->
                            <div class="card card-custom gutter-b mt-4">
                                <div class="card-header flex-wrap py-3">
                                    <div class="card-title">
                                        <h3 class="card-label">Brand Video Content</h3>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <form action="" enctype="multipart/form-data" method="post" id="edit-brand-video-form">
                                        <div class="card-body">

                                            <!-- ===== VIDEO SOURCE SELECTION ===== -->
                                            <div class="row mb-5">
                                                <div class="col-md-12">
                                                    <label class="font-weight-bold d-block mb-2">Video Source <span class="text-danger">*</span></label>
                                                    <div class="d-flex" style="gap: 16px;">

                                                        <label class="video-source-card <?= $current_source === 'local' ? 'active' : '' ?>" for="src_local" style="cursor:pointer; border:2px solid <?= $current_source === 'local' ? '#3699FF' : '#E4E6EF' ?>; border-radius:8px; padding:16px 24px; display:flex; align-items:center; gap:10px; min-width:160px; transition: all .2s;">
                                                            <input type="radio" name="video_source" id="src_local" value="local"
                                                                <?= $current_source === 'local' ? 'checked' : '' ?>
                                                                onchange="toggleVideoSource(this.value)" style="accent-color:#3699FF;">
                                                            <span>
                                                                <i class="fas fa-film text-primary mr-1"></i>
                                                                <strong>Local Video</strong><br>
                                                                <small class="text-muted">Upload from your computer</small>
                                                            </span>
                                                        </label>

                                                        <label class="video-source-card <?= $current_source === 'youtube' ? 'active' : '' ?>" for="src_youtube" style="cursor:pointer; border:2px solid <?= $current_source === 'youtube' ? '#F64E60' : '#E4E6EF' ?>; border-radius:8px; padding:16px 24px; display:flex; align-items:center; gap:10px; min-width:160px; transition: all .2s;">
                                                            <input type="radio" name="video_source" id="src_youtube" value="youtube"
                                                                <?= $current_source === 'youtube' ? 'checked' : '' ?>
                                                                onchange="toggleVideoSource(this.value)" style="accent-color:#F64E60;">
                                                            <span>
                                                                <i class="fab fa-youtube text-danger mr-1"></i>
                                                                <strong>YouTube Video</strong><br>
                                                                <small class="text-muted">Embed from YouTube</small>
                                                            </span>
                                                        </label>

                                                    </div>
                                                    <small class="form-text text-muted mt-2">This controls which video is shown on the website frontend.</small>
                                                </div>
                                            </div>

                                            <hr class="mb-7">

                                            <!-- ===== IMAGES ROW ===== -->
                                            <div class="row">

                                                <!-- Main Image -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Section Background / Poster Image</label>
                                                        <div class="dropzone-box border rounded p-4 text-center position-relative" id="img-dropzone" style="background:#f8f9fa; border-style:dashed !important; cursor:pointer;" onclick="document.getElementById('brand_video_cont_img').click()">
                                                            <?php if (!empty($details['brand_video_cont_img'])): ?>
                                                                <img id="img-preview" src="<?= BASE_URL ?>assets/media/brand-video/<?= htmlspecialchars($details['brand_video_cont_img']) ?>" style="max-height:120px; max-width:100%; object-fit:cover; border-radius:4px; display:block; margin: 0 auto 8px;">
                                                            <?php else: ?>
                                                                <img id="img-preview" src="#" style="max-height:120px; max-width:100%; object-fit:cover; border-radius:4px; display:none; margin: 0 auto 8px;">
                                                            <?php endif; ?>
                                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2" id="img-icon" <?= !empty($details['brand_video_cont_img']) ? 'style="display:none"' : '' ?>></i>
                                                            <p class="text-muted mb-0 small">Click to upload image</p>
                                                            <input type="file" id="brand_video_cont_img" name="brand_video_cont_img"
                                                                accept="image/png,image/jpg,image/jpeg,image/webp"
                                                                style="display:none" onchange="previewImage(this,'img-preview','img-icon')">
                                                        </div>
                                                        <input type="hidden" name="brand_video_cont_img_hidden" value="<?= htmlspecialchars($details['brand_video_cont_img'] ?? '') ?>">
                                                        <small class="form-text text-muted">Allowed: PNG, JPG, JPEG, WEBP</small>
                                                    </div>
                                                </div>

                                                <!-- YouTube Thumbnail -->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">YouTube Thumbnail Image</label>
                                                        <div class="dropzone-box border rounded p-4 text-center position-relative" id="yt-img-dropzone" style="background:#f8f9fa; border-style:dashed !important; cursor:pointer;" onclick="document.getElementById('brand_video_youtube_img').click()">
                                                            <?php if (!empty($details['brand_video_youtube_img'])): ?>
                                                                <img id="yt-img-preview" src="<?= BASE_URL ?>assets/media/brand-video/<?= htmlspecialchars($details['brand_video_youtube_img']) ?>" style="max-height:120px; max-width:100%; object-fit:cover; border-radius:4px; display:block; margin: 0 auto 8px;">
                                                            <?php else: ?>
                                                                <img id="yt-img-preview" src="#" style="max-height:120px; max-width:100%; object-fit:cover; border-radius:4px; display:none; margin: 0 auto 8px;">
                                                            <?php endif; ?>
                                                            <i class="fab fa-youtube fa-2x text-danger mb-2" id="yt-img-icon" <?= !empty($details['brand_video_youtube_img']) ? 'style="display:none"' : '' ?>></i>
                                                            <p class="text-muted mb-0 small">Click to upload thumbnail</p>
                                                            <input type="file" id="brand_video_youtube_img" name="brand_video_youtube_img"
                                                                accept="image/png,image/jpg,image/jpeg,image/webp"
                                                                style="display:none" onchange="previewImage(this,'yt-img-preview','yt-img-icon')">
                                                        </div>
                                                        <input type="hidden" name="brand_video_youtube_img_hidden" value="<?= htmlspecialchars($details['brand_video_youtube_img'] ?? '') ?>">
                                                        <small class="form-text text-muted">Displayed as play button thumbnail. Allowed: PNG, JPG, JPEG, WEBP</small>
                                                    </div>
                                                </div>

                                            </div><!-- /images row -->

                                            <!-- ===== LOCAL VIDEO SECTION ===== -->
                                            <div id="local_video_section" style="display: <?= $current_source === 'local' ? 'block' : 'none' ?>;">
                                                <div class="card border mb-6" style="border-color:#3699FF !important; border-radius:8px;">
                                                    <div class="card-header" style="background:#EEF6FF; border-radius:8px 8px 0 0;">
                                                        <h6 class="mb-0 text-primary"><i class="fas fa-film mr-2"></i>Local Video Upload</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-0">
                                                                    <label class="font-weight-bold">Upload Video File</label>
                                                                    <div class="border rounded p-4 text-center" style="background:#f8f9fa; border-style:dashed !important; cursor:pointer;" onclick="document.getElementById('brand_video_2').click()">
                                                                        <i class="fas fa-video fa-2x text-primary mb-2"></i>
                                                                        <p class="text-muted mb-1 small">Click to select video</p>
                                                                        <p class="text-muted mb-0" style="font-size:11px;">MP4, WEBM, OGG, MOV, AVI, MKV &bull; Max 100MB</p>
                                                                        <input type="file" id="brand_video_2" name="brand_video_2"
                                                                            accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo,video/x-matroska"
                                                                            style="display:none" onchange="handleVideoSelect(this)">
                                                                    </div>
                                                                    <div id="video_file_name" class="mt-2 small text-success" style="display:none;"></div>
                                                                    <input type="hidden" name="brand_video_2_hidden" value="<?= htmlspecialchars($details['brand_video_2'] ?? '') ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <?php if (!empty($details['brand_video_2'])): ?>
                                                                    <div id="current_video_box">
                                                                        <label class="font-weight-bold d-block">Current Video</label>
                                                                        <video width="100%" controls style="border-radius:6px; max-height:180px;">
                                                                            <source src="<?= BASE_URL ?>assets/media/brand-video/videos/<?= htmlspecialchars($details['brand_video_2']) ?>" type="video/mp4">
                                                                            Your browser does not support the video tag.
                                                                        </video>
                                                                        <small class="text-muted d-block mt-1">
                                                                            <i class="fas fa-file-video mr-1"></i><?= htmlspecialchars($details['brand_video_2']) ?>
                                                                        </small>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="d-flex align-items-center justify-content-center h-100">
                                                                        <p class="text-muted text-center"><i class="fas fa-film fa-3x mb-2 d-block"></i>No video uploaded yet</p>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- ===== YOUTUBE VIDEO SECTION ===== -->
                                            <div id="youtube_video_section" style="display: <?= $current_source === 'youtube' ? 'block' : 'none' ?>;">
                                                <div class="card border mb-6" style="border-color:#F64E60 !important; border-radius:8px;">
                                                    <div class="card-header" style="background:#FFF5F8; border-radius:8px 8px 0 0;">
                                                        <h6 class="mb-0 text-danger"><i class="fab fa-youtube mr-2"></i>YouTube Video</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-0">
                                                                    <label class="font-weight-bold">YouTube Video URL</label>
                                                                    <input type="url" id="brand_video_youtube" name="brand_video_youtube"
                                                                        class="form-control"
                                                                        value="<?= htmlspecialchars($details['brand_video_youtube'] ?? '') ?>"
                                                                        placeholder="https://www.youtube.com/watch?v=..."
                                                                        oninput="updateYoutubePreview(this.value)">
                                                                    <small class="form-text text-muted">Paste the full YouTube video URL</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="font-weight-bold d-block">Preview</label>
                                                                <div id="youtube_preview_box">
                                                                    <?php if (!empty($details['brand_video_youtube'])): ?>
                                                                        <div class="embed-responsive embed-responsive-16by9" style="border-radius:6px; overflow:hidden;">
                                                                            <iframe id="youtube_iframe" class="embed-responsive-item"
                                                                                src="https://www.youtube.com/embed/<?= getYouTubeVideoId($details['brand_video_youtube']) ?>"
                                                                                allowfullscreen></iframe>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div id="yt_placeholder" class="d-flex align-items-center justify-content-center bg-light rounded" style="height:150px;">
                                                                            <p class="text-muted mb-0"><i class="fab fa-youtube fa-3x text-danger d-block text-center mb-2"></i>Enter a URL to preview</p>
                                                                        </div>
                                                                        <div class="embed-responsive embed-responsive-16by9" style="border-radius:6px; overflow:hidden; display:none;">
                                                                            <iframe id="youtube_iframe" class="embed-responsive-item" src="" allowfullscreen></iframe>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- ===== TEXT FIELDS ===== -->
                                            <div class="form-group">
                                                <label class="font-weight-bold">Heading</label>
                                                <input type="text" name="brand_video_heading" class="form-control"
                                                    value="<?= htmlspecialchars($details['brand_video_heading'] ?? '') ?>"
                                                    placeholder="Enter section heading">
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Content</label>
                                                <textarea name="brand_video_content" class="form-control" rows="5"
                                                    id="kt-tinymce-2" placeholder="Enter section content"><?= htmlspecialchars($details['brand_video_content'] ?? '') ?></textarea>
                                            </div>

                                            <!-- Status -->
                                            <div class="form-group">
                                                <label class="font-weight-bold d-block">Status</label>
                                                <span class="switch switch-md switch-icon">
                                                    <label>
                                                        <input type="checkbox" name="is_active" value="1"
                                                            <?= ($details['is_active'] == 1 ? 'checked' : '') ?>>
                                                        <span></span>
                                                    </label>
                                                </span>
                                                <small class="form-text text-muted">Toggle to Active / Inactive</small>
                                            </div>

                                        </div><!-- /card-body -->

                                        <div class="card-footer">
                                            <button type="submit" name="update_brand_video_content" class="btn btn-primary mr-2">
                                                <i class="fas fa-save mr-1"></i> Update
                                            </button>
                                            <button type="reset" class="btn btn-secondary">
                                                <i class="fas fa-undo mr-1"></i> Reset
                                            </button>
                                        </div>

                                    </form>
                                </div>
                            </div><!-- /card -->

                        </div>
                    </div>
                </div>
                <?php include('includes/footer-inner.php'); ?>
            </div>
        </div>
    </div>

    <script>
        // Toggle video source sections
        function toggleVideoSource(value) {
            const local   = document.getElementById('local_video_section');
            const youtube = document.getElementById('youtube_video_section');
            const cards   = document.querySelectorAll('.video-source-card');

            // Reset card borders
            cards.forEach(c => c.style.borderColor = '#E4E6EF');

            if (value === 'local') {
                local.style.display   = 'block';
                youtube.style.display = 'none';
                document.getElementById('src_local').closest('label').style.borderColor = '#3699FF';
            } else {
                local.style.display   = 'none';
                youtube.style.display = 'block';
                document.getElementById('src_youtube').closest('label').style.borderColor = '#F64E60';
            }
        }

        // Preview uploaded image
        function previewImage(input, previewId, iconId) {
            const preview = document.getElementById(previewId);
            const icon    = document.getElementById(iconId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src          = e.target.result;
                    preview.style.display = 'block';
                    if (icon) icon.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Show selected video filename + size
        function handleVideoSelect(input) {
            const label = document.getElementById('video_file_name');
            if (input.files && input.files[0]) {
                const f    = input.files[0];
                const size = (f.size / (1024 * 1024)).toFixed(2);
                if (f.size > 100 * 1024 * 1024) {
                    label.innerHTML      = '<i class="fas fa-times-circle text-danger mr-1"></i>File too large: ' + size + 'MB (max 100MB)';
                    label.style.color    = '#F64E60';
                    label.style.display  = 'block';
                    input.value          = '';
                } else {
                    label.innerHTML     = '<i class="fas fa-check-circle mr-1"></i>' + f.name + ' (' + size + 'MB)';
                    label.style.color   = '#1BC5BD';
                    label.style.display = 'block';
                }
            }
        }

        // Live YouTube preview
        function getYouTubeId(url) {
            const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
            return match ? match[1] : null;
        }

        function updateYoutubePreview(url) {
            const id          = getYouTubeId(url);
            const iframe      = document.getElementById('youtube_iframe');
            const placeholder = document.getElementById('yt_placeholder');

            if (id && iframe) {
                iframe.src                            = 'https://www.youtube.com/embed/' + id;
                if (placeholder) placeholder.style.display = 'none';
                iframe.parentElement.style.display    = 'block';
            } else if (iframe) {
                iframe.src                            = '';
                if (placeholder) placeholder.style.display = 'flex';
                iframe.parentElement.style.display    = 'none';
            }
        }

        // Form validation on submit
        document.getElementById('edit-brand-video-form').addEventListener('submit', function(e) {
            const source       = document.querySelector('input[name="video_source"]:checked').value;
            const ytUrl        = document.getElementById('brand_video_youtube').value.trim();
            const localFile    = document.getElementById('brand_video_2').files.length;
            const existingVid  = document.querySelector('input[name="brand_video_2_hidden"]').value;

            if (source === 'youtube' && !ytUrl) {
                e.preventDefault();
                alert('Please enter a YouTube video URL.');
                document.getElementById('brand_video_youtube').focus();
                return false;
            }

            if (source === 'local' && localFile === 0 && !existingVid) {
                e.preventDefault();
                alert('Please upload a local video file.');
                return false;
            }
        });

        // Init on load
        document.addEventListener('DOMContentLoaded', function() {
            const checked = document.querySelector('input[name="video_source"]:checked');
            if (checked) toggleVideoSource(checked.value);
        });
    </script>

</body>