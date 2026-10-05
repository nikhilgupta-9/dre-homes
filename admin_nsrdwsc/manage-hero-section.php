<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ===============================
   BASE PATH (IMPORTANT FIX)
================================= */
$media_base_path = __DIR__ . '/assets/media/'; // absolute path
$media_base_url = BASE_URL . 'assets/media/'; // for browser

/* ===============================
   SAVE / UPDATE HERO
================================= */
if (isset($_POST['save_hero'])) {

    $media_type = $_POST['media_type'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $subtitle = mysqli_real_escape_string($conn, $_POST['subtitle']);
    $video_url = mysqli_real_escape_string($conn, $_POST['video_url']);
    $img_screen_size = mysqli_real_escape_string($conn, $_POST['img_screenSize']);
    $display_order = intval($_POST['display_order']);

    $media_path = '';

    /* ===============================
       FILE UPLOAD
    ================================= */
    if (!empty($_FILES['media_file']['name'])) {

        $allowed_image = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $allowed_video = ['mp4', 'webm', 'ogg', 'mov'];

        $file_name = $_FILES['media_file']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_filename = time() . '_' . uniqid() . '.' . $file_ext;

        if ($media_type === 'image' && in_array($file_ext, $allowed_image)) {

            $upload_dir = $media_base_path . 'hero/';

        } elseif ($media_type === 'video' && in_array($file_ext, $allowed_video)) {

            $upload_dir = $media_base_path . 'hero/videos/';

        } else {
            $_SESSION['error_msg'] = "Invalid file type!";
            header("Location: manage-hero-section.php");
            exit;
        }

        /* create folder if not exists */
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        /* move file */
        if (move_uploaded_file($_FILES['media_file']['tmp_name'], $upload_dir . $new_filename)) {

            // store relative path in DB
            if ($media_type === 'image') {
                $media_path = 'hero/' . $new_filename;
            } else {
                $media_path = 'hero/videos/' . $new_filename;
            }

        } else {
            $_SESSION['error_msg'] = "File upload failed!";
            header("Location: manage-hero-section.php");
            exit;
        }
    }

    /* ===============================
       UPDATE RECORD
    ================================= */
    if (!empty($_POST['hero_id'])) {

        $hero_id = intval($_POST['hero_id']);

        if (!empty($media_path)) {

            // delete old file
            $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT media_path FROM tbl_hero_section WHERE id='$hero_id'"));

            if (!empty($old['media_path'])) {
                $old_file = $media_base_path . $old['media_path'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            $query = "UPDATE tbl_hero_section SET 
                media_type='$media_type',
                media_path='$media_path',
                video_url='$video_url',
                img_screen_size='$img_screen_size',
                title='$title',
                subtitle='$subtitle',
                display_order='$display_order'
                WHERE id='$hero_id'";

        } else {

            // KEEP OLD FILE
            $query = "UPDATE tbl_hero_section SET 
                media_type='$media_type',
                video_url='$video_url',
                img_screen_size='$img_screen_size',
                title='$title',
                subtitle='$subtitle',
                display_order='$display_order'
                WHERE id='$hero_id'";
        }

        mysqli_query($conn, $query);
        $_SESSION['success_msg'] = "Hero updated successfully!";
    }

    /* ===============================
       INSERT RECORD
    ================================= */ else {

        $query = "INSERT INTO tbl_hero_section 
        (media_type, media_path, video_url, img_screen_size, title, subtitle, display_order, is_active)
        VALUES 
        ('$media_type', '$media_path', '$video_url', '$img_screen_size', '$title', '$subtitle', '$display_order', 1)";

        mysqli_query($conn, $query);
        $_SESSION['success_msg'] = "Hero added successfully!";
    }

    header("Location: manage-hero-section.php");
    exit();
}

/* ===============================
   DELETE HERO
================================= */
if (isset($_GET['del_hero'])) {

    $id = intval($_GET['del_hero']);

    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT media_path FROM tbl_hero_section WHERE id='$id'"));

    if (!empty($row['media_path'])) {
        $file = $media_base_path . $row['media_path'];
        if (file_exists($file)) {
            unlink($file);
        }
    }

    mysqli_query($conn, "DELETE FROM tbl_hero_section WHERE id='$id'");

    $_SESSION['success_msg'] = "Deleted successfully!";
    header("Location: manage-hero-section.php");
    exit();
}

// Handle Hero Status Toggle
if (isset($_GET['toggle_hero_status'])) {
    $id = intval($_GET['toggle_hero_status']);
    $query = "UPDATE tbl_hero_section SET is_active = NOT is_active WHERE id='$id'";
    mysqli_query($conn, $query);
    $_SESSION['success_msg'] = "Hero section status updated!";
    header("Location: manage-hero-section.php");
    exit();
}

// Get hero data for editing
$edit_hero = null;
if (isset($_GET['edit_hero'])) {
    $id = intval($_GET['edit_hero']);
    $query = "SELECT * FROM tbl_hero_section WHERE id='$id'";
    $result = mysqli_query($conn, $query);
    $edit_hero = mysqli_fetch_assoc($result);
}


/* ===============================
   FETCH DATA
================================= */
$hero_result = mysqli_query($conn, "SELECT * FROM tbl_hero_section ORDER BY display_order ASC");
?>

<style>
    .hero-preview {
        max-width: 100px;
        max-height: 80px;
        object-fit: cover;
        border-radius: 5px;
    }

    .media-preview {
        margin-top: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
    }

    .hero-table-img {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
    }

    .modal-body .form-group:last-child {
        margin-bottom: 0;
    }
</style>

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
                                        class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= $_SESSION['username'] ?></span>
                                    <span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
                                        <span
                                            class="symbol-label font-size-h5 font-weight-bold"><?= substr($_SESSION['username'], 0, 1) ?></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Header-->

                <!--begin::Content-->
                <div class="d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="d-flex flex-column-fluid">
                        <div class="container">

                            <!-- Page Title -->
                            <div class="card card-custom gutter-b">
                                <div class="card-header flex-wrap py-3">
                                    <div class="card-title">
                                        <h3 class="card-label">Hero Section Management</h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <button type="button" class="btn btn-primary font-weight-bolder"
                                            data-toggle="modal" data-target="#addHeroModal">
                                            <i class="flaticon2-plus"></i> Add New Hero Section
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Success/Error Messages -->
                            <?php if (isset($_SESSION['success_msg'])): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= $_SESSION['success_msg'];
                                    unset($_SESSION['success_msg']); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['error_msg'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= $_SESSION['error_msg'];
                                    unset($_SESSION['error_msg']); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <!-- Hero Sections List -->
                            <div class="card card-custom gutter-b">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="card-label">Hero Sections List</h3>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="15%">Media</th>
                                                    <th width="25%">Title & Subtitle</th>
                                                    <th width="10%">Type</th>
                                                    <th width="10%">Order</th>
                                                    <th width="10%">Status</th>
                                                    <th width="25%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (mysqli_num_rows($hero_result) > 0): ?>
                                                    <?php while ($row = mysqli_fetch_assoc($hero_result)): ?>
                                                        <tr>
                                                            <td>
                                                                <?= $row['id'] ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $full_path = $media_base_path . $row['media_path'];
                                                                ?>

                                                                <?php if ($row['media_type'] === 'image' && !empty($row['media_path']) && file_exists($full_path)): ?>

                                                                    <img src="<?= $media_base_url . $row['media_path'] ?>"
                                                                        class="hero-table-img">

                                                                <?php elseif ($row['media_type'] === 'video' && !empty($row['media_path']) && file_exists($full_path)): ?>

                                                                    <video width="80" muted autoplay loop>
                                                                        <source src="<?= $media_base_url . $row['media_path'] ?>"
                                                                            type="video/mp4">
                                                                    </video>

                                                                <?php elseif (!empty($row['video_url'])): ?>

                                                                    <span class="badge badge-info">YouTube/Vimeo</span>

                                                                <?php else: ?>

                                                                    <span class="badge badge-secondary">No Media</span>

                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <strong><?= htmlspecialchars($row['title'] ?? 'Untitled') ?></strong><br>
                                                                <small
                                                                    class="text-muted"><?= htmlspecialchars(substr($row['subtitle'] ?? '', 0, 60)) ?></small>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge badge-<?= $row['media_type'] == 'image' ? 'primary' : 'info' ?>">
                                                                    <?= ucfirst($row['media_type']) ?>
                                                                </span>
                                                            </td>
                                                            <td><?= $row['display_order'] ?></td>
                                                            <td>
                                                                <a href="<?=BASE_URL?>manage-hero-section.php?toggle_hero_status=<?= $row['id'] ?>"
                                                                    class="btn btn-sm btn-<?= $row['is_active'] ? 'success' : 'secondary' ?>"
                                                                    onclick="return confirm('Change status?')">
                                                                    <?= $row['is_active'] ? 'Active' : 'Inactive' ?>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-primary"
                                                                    onclick="editHero(<?= htmlspecialchars(json_encode($row)) ?>)">
                                                                    <i class="la la-edit"></i> Edit
                                                                </button>
                                                                <a href="<?= BASE_URL ?>manage-hero-section.php?del_hero=<?= $row['id'] ?>"
                                                                    class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Are you sure you want to delete this hero section?')">
                                                                    <i class="la la-trash"></i> Delete
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center">
                                                            <div class="text-center py-5">
                                                                <i class="flaticon2-image display-1 text-muted"></i>
                                                                <p class="mt-3">No hero sections found. Click "Add New Hero
                                                                    Section" to create one.</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Add/Edit Hero Modal -->
                            <div class="modal fade" id="addHeroModal" tabindex="-1" role="dialog"
                                aria-labelledby="addHeroModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addHeroModalLabel">Add New Hero Section</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="" method="post" enctype="multipart/form-data" id="heroForm">
                                            <div class="modal-body">
                                                <input type="hidden" name="hero_id" id="hero_id">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Media Type <span class="text-danger">*</span></label>
                                                            <select name="media_type" id="media_type"
                                                                class="form-control" required>
                                                                <option value="image">Image</option>
                                                                <option value="video">Video</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Display Order</label>
                                                            <input type="number" name="display_order" id="display_order"
                                                                class="form-control" value="0">
                                                            <small class="form-text text-muted">Lower number displays
                                                                first</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group" id="file_upload_div">
                                                    <label id="file_label">Upload Media File</label>
                                                    <input type="file" name="media_file" id="media_file"
                                                        class="form-control-file" accept="image/*,video/*">
                                                    <small class="form-text text-muted" id="file_help">Supported: JPG,
                                                        PNG, GIF, WEBP (images) or MP4, WebM, OGG (videos)</small>
                                                    <div id="current_media_preview" class="media-preview mt-2"
                                                        style="display: none;"></div>
                                                </div>

                                                <div class="form-group" id="video_url_div" style="display: none;">
                                                    <label>Video URL (YouTube)</label>
                                                    <input type="url" name="video_url" id="video_url"
                                                        class="form-control"
                                                        placeholder="https://www.youtube.com/watch?v=...">
                                                    <small class="form-text text-muted">For embedding external videos
                                                        like YouTube</small>
                                                </div>

                                                <div class="form-group" id="img_screenSize_div" style="display: block;">
                                                    <label>Image For Screen</label>
                                                    <select name="img_screenSize" id="img_screenSize"
                                                        class="form-control" required>
                                                        <option value="desktop">Desktop</option>
                                                        <option value="tablet">Tablet</option>
                                                        <option value="mobile">Mobile</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Title</label>
                                                    <input type="text" name="title" id="title" class="form-control"
                                                        placeholder="Hero Title">
                                                </div>

                                                <div class="form-group">
                                                    <label>Subtitle / Description</label>
                                                    <textarea name="subtitle" id="subtitle" class="form-control"
                                                        rows="3" placeholder="Hero Subtitle"></textarea>
                                                </div>

                                                <div class="row d-none">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Button 1 Text</label>
                                                            <input type="text" name="button_text" id="button_text"
                                                                class="form-control" placeholder="Get Started">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Button 1 Link</label>
                                                            <input type="text" name="button_link" id="button_link"
                                                                class="form-control" placeholder="https://example.com">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row d-none">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Button 2 Text (Optional)</label>
                                                            <input type="text" name="button_text_2" id="button_text_2"
                                                                class="form-control" placeholder="Learn More">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Button 2 Link</label>
                                                            <input type="text" name="button_link_2" id="button_link_2"
                                                                class="form-control" placeholder="https://example.com">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row d-none">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Overlay Color (RGBA)</label>
                                                            <input type="text" name="overlay_color" id="overlay_color"
                                                                class="form-control" placeholder="rgba(0,0,0,0.5)"
                                                                value="rgba(0,0,0,0.5)">
                                                            <input type="color" id="overlay_color_picker" class="mt-2"
                                                                value="#000000">
                                                            <small class="form-text text-muted">Pick color, opacity will
                                                                be 0.5</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Text Color</label>
                                                            <input type="color" name="text_color" id="text_color"
                                                                class="form-control" style="height: 50px;"
                                                                value="#ffffff">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                <button type="submit" name="save_hero" class="btn btn-primary">Save Hero
                                                    Section</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!--end::Content-->

                <?php include('includes/footer-inner.php'); ?>

            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Main-->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mediaType = document.getElementById('media_type');
            const fileUploadDiv = document.getElementById('file_upload_div');
            const videoUrlDiv = document.getElementById('video_url_div');
            const imgScreenSizeDiv = document.getElementById('img_screenSize_div');
            const fileLabel = document.getElementById('file_label');
            const fileHelp = document.getElementById('file_help');

            function toggleMediaFields() {
                if (mediaType.value === 'image') {
                    fileUploadDiv.style.display = 'block';
                    videoUrlDiv.style.display = 'none';
                    imgScreenSizeDiv.style.display = 'block';
                    fileLabel.innerHTML = 'Upload Image File';
                    fileHelp.innerHTML = 'Supported: JPG, PNG, GIF, WEBP';
                    document.querySelector('input[name="media_file"]').accept = 'image/*';
                } else {
                    fileUploadDiv.style.display = 'block';
                    videoUrlDiv.style.display = 'block';
                    imgScreenSizeDiv.style.display = 'none';
                    fileLabel.innerHTML = 'Upload Video File (Optional)';
                    fileHelp.innerHTML = 'Supported: MP4, WebM, OGG or use YouTube URL above';
                    document.querySelector('input[name="media_file"]').accept = 'video/*';
                }
            }

            if (mediaType) {
                mediaType.addEventListener('change', toggleMediaFields);
                toggleMediaFields();
            }

            const colorPicker = document.getElementById('overlay_color_picker');
            if (colorPicker) {
                colorPicker.addEventListener('input', function () {
                    const rgb = hexToRgb(this.value);
                    if (rgb) {
                        document.getElementById('overlay_color').value = `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.5)`;
                    }
                });
            }
        });

        function hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }

        function editHero(data) {
            document.getElementById('hero_id').value = data.id;
            document.getElementById('media_type').value = data.media_type;
            document.getElementById('display_order').value = data.display_order;
            document.getElementById('video_url').value = data.video_url || '';
            document.getElementById('title').value = data.title || '';
            document.getElementById('subtitle').value = data.subtitle || '';
            document.getElementById('button_text').value = data.button_text || '';
            document.getElementById('button_link').value = data.button_link || '';
            document.getElementById('button_text_2').value = data.button_text_2 || '';
            document.getElementById('button_link_2').value = data.button_link_2 || '';
            document.getElementById('overlay_color').value = data.overlay_color || 'rgba(0,0,0,0.5)';
            document.getElementById('text_color').value = data.text_color || '#ffffff';

            if (document.getElementById('img_screenSize') && data.img_screen_size) {
                document.getElementById('img_screenSize').value = data.img_screen_size;
            }

            const previewDiv = document.getElementById('current_media_preview');
            if (data.media_path) {
                let previewHtml = '<strong>Current Media:</strong><br>';
                if (data.media_type === 'image') {
                    previewHtml += `<img src="<?= BASE_URL ?>assets/media/${data.media_path}" class="hero-preview mt-2">`;
                } else {
                    previewHtml += `<video width="200" controls><source src="<?= BASE_URL ?>assets/media/${data.media_path}" type="video/mp4"></video>`;
                }
                previewDiv.innerHTML = previewHtml;
                previewDiv.style.display = 'block';
            } else {
                previewDiv.style.display = 'none';
            }

            const event = new Event('change');
            document.getElementById('media_type').dispatchEvent(event);
            document.getElementById('addHeroModalLabel').innerHTML = 'Edit Hero Section';
            $('#addHeroModal').modal('show');
        }

        $('#addHeroModal').on('hidden.bs.modal', function () {
            document.getElementById('heroForm').reset();
            document.getElementById('hero_id').value = '';
            document.getElementById('addHeroModalLabel').innerHTML = 'Add New Hero Section';
            document.getElementById('current_media_preview').style.display = 'none';
            document.getElementById('current_media_preview').innerHTML = '';
            document.getElementById('overlay_color').value = 'rgba(0,0,0,0.5)';
            document.getElementById('text_color').value = '#ffffff';
            document.getElementById('display_order').value = '0';
        });
    </script>