<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');

if (isset($_POST['submit_podcast'])) {
    $title        = mysqli_real_escape_string($conn, trim($_POST['podcast_title']));
    $descp        = mysqli_real_escape_string($conn, trim($_POST['podcast_descp']));
    $youtube_link = mysqli_real_escape_string($conn, trim($_POST['youtube_link'] ?? ''));
    $order_no     = intval($_POST['order_no11']);
    $main_podcast = isset($_POST['main_podcast']) ? 1 : 0;
    $is_active    = intval($_POST['is_active']);

    $thumb_name = "";
    $video_name = "";

    // Directory paths setup
    if (!is_dir('assets/media/podcast/thumb/')) {
        mkdir('assets/media/podcast/thumb/', 0777, true);
    }
    if (!is_dir('assets/media/podcast/video/')) {
        mkdir('assets/media/podcast/video/', 0777, true);
    }

    // 1. Thumbnail Upload
    if (!empty($_FILES['podcast_thumb']['name']) && $_FILES['podcast_thumb']['error'] === UPLOAD_ERR_OK) {
        $thumb_ext  = pathinfo($_FILES['podcast_thumb']['name'], PATHINFO_EXTENSION);
        $thumb_name = "thumb_" . time() . "_" . rand(1000, 9999) . "." . $thumb_ext;
        move_uploaded_file($_FILES['podcast_thumb']['tmp_name'], 'assets/media/podcast/thumb/' . $thumb_name);
    }

    // 2. Local Video Upload
    if (!empty($_FILES['video_file']['name']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
        $video_ext  = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
        $video_name = "vid_" . time() . "_" . rand(1000, 9999) . "." . $video_ext;
        move_uploaded_file($_FILES['video_file']['tmp_name'], 'assets/media/podcast/video/' . $video_name);
    }

    // Check if editing or adding
    if (isset($_POST['podcast_id']) && !empty($_POST['podcast_id'])) {
        $podcast_id   = intval($_POST['podcast_id']);
        $update_query = "UPDATE `tbl_podcast` SET 
                            `podcast_title` = '$title',
                            `podcast_descp` = '$descp',
                            `youtube_link`  = '$youtube_link',
                            `order_no`      = $order_no,
                            `is_main`       = $main_podcast,
                            `is_active`     = $is_active";

        if (!empty($thumb_name)) {
            $update_query .= ", `podcast_thumb` = '$thumb_name'";
        }
        if (!empty($video_name)) {
            $update_query .= ", `video_file` = '$video_name'";
        }

        $update_query .= " WHERE `podcast_id` = $podcast_id";

        $result = mysqli_query($conn, $update_query);
        if ($result) {
            header('Location:' . BASE_URL . 'manage-podcast.php?success=updated');
            exit;
        } else {
            $form_error = "Update failed: " . mysqli_error($conn);
        }
    } else {
        $ins = mysqli_query($conn, "INSERT INTO `tbl_podcast` 
                (`podcast_title`, `podcast_descp`, `podcast_thumb`, `youtube_link`, `video_file`, `order_no`, `is_main`, `is_active`) 
                VALUES ('$title', '$descp', '$thumb_name', '$youtube_link', '$video_name', $order_no, $main_podcast, $is_active)");
        if ($ins) {
            header('Location:' . BASE_URL . 'manage-podcast.php?success=added');
            exit;
        } else {
            $form_error = "Insert failed: " . mysqli_error($conn);
        }
    }
}

// Fetch data for editing
$edit_data = null;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $edit_id    = intval($_GET['id']);
    $edit_query = mysqli_query($conn, "SELECT * FROM `tbl_podcast` WHERE `podcast_id` = $edit_id");
    $edit_data  = mysqli_fetch_assoc($edit_query);
}
?>

<body id="kt_body"
    class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">

    <div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
        <a href="index.html">
            <img alt="Logo" src="<?= BASE_URL ?>assets/media/logos/logo-light.png" />
        </a>
        <div class="d-flex align-items-center">
            <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle"><span></span></button>
            <button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle"><span></span></button>
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
                                    <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= $_SESSION['username'] ?></span>
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
                        <div class="container p-5">
                            <div class="card card-custom">
                                <div class="card-header flex-wrap py-3">
                                    <div class="card-title">
                                        <h3 class="card-label"><?= $edit_data ? 'Edit' : 'Add New' ?> Podcast</h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <a href="<?= BASE_URL ?>manage-podcast.php" class="btn btn-primary font-weight-bolder">
                                            <span class="svg-icon svg-icon-md"><i class="bi bi-back"></i></span>Back
                                        </a>
                                        &nbsp;
                                        <a href="<?= BASE_URL_HOME ?>podcast/" class="btn btn-primary font-weight-bolder" target="_blank">
                                            <span class="svg-icon svg-icon-md"><i class="bi bi-eye"></i></span>View
                                        </a>
                                    </div>
                                </div>

                                <?php if (!empty($form_error)): ?>
                                    <div class="alert alert-danger mx-5 mt-3"><?= $form_error ?></div>
                                <?php endif; ?>

                                <form method="POST" enctype="multipart/form-data" class="form" id="podcastForm"
                                      onkeydown="return event.key !== 'Enter' || event.target.tagName === 'TEXTAREA';">

                                    <?php if ($edit_data): ?>
                                        <input type="hidden" name="podcast_id" value="<?= $edit_data['podcast_id'] ?>">
                                    <?php endif; ?>

                                    <div class="card-body">

                                        <!-- Title + Hero -->
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    <label>Podcast Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="podcast_title" id="podcast_title"
                                                        class="form-control" required
                                                        value="<?= $edit_data ? htmlspecialchars($edit_data['podcast_title']) : '' ?>"
                                                        placeholder="Enter podcast title" />
                                                    <span class="text-danger font-size-sm" id="err_title"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="heroPodcast">Hero Podcast</label><br>
                                                    <input type="checkbox" id="heroPodcast" name="main_podcast" value="1"
                                                        <?= ($edit_data && !empty($edit_data['is_main'])) ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="podcast_descp" class="form-control" rows="4"
                                                placeholder="Enter description (optional)"><?= $edit_data ? htmlspecialchars($edit_data['podcast_descp']) : '' ?></textarea>
                                        </div>

                                        <!-- Thumbnail + Order -->
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Thumbnail Image <?= !$edit_data ? '<span class="text-danger">*</span>' : '' ?></label>
                                                <input type="file" name="podcast_thumb" id="podcast_thumb"
                                                    class="form-control" accept="image/*" />
                                                <?php if ($edit_data && $edit_data['podcast_thumb']): ?>
                                                    <div class="mt-2 d-flex align-items-center">
                                                        <img src="<?= BASE_URL ?>assets/media/podcast/thumb/<?= $edit_data['podcast_thumb'] ?>"
                                                            width="60" class="rounded mr-2">
                                                        <small class="text-muted"><?= $edit_data['podcast_thumb'] ?></small>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="text-danger font-size-sm" id="err_thumb"></span>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Order Number</label>
                                                <input type="number" name="order_no11" id="order_no11"
                                                    class="form-control" min="0"
                                                    value="<?= $edit_data ? $edit_data['order_no'] : '0' ?>" />
                                                <span class="text-danger font-size-sm" id="err_order"></span>
                                            </div>
                                        </div>

                                        <!-- Media Source Toggle -->
                                        <div class="row">
                                            <div class="col-12 mb-4">
                                                <label class="d-block font-weight-bold">Select Media Source:</label>
                                                <div class="d-flex align-items-center">
                                                    <span class="mr-2 font-weight-bold text-danger">YouTube Link</span>
                                                    <label class="switch">
                                                        <input type="checkbox" id="mediaTypeToggle"
                                                            <?= ($edit_data && $edit_data['video_file']) ? 'checked' : '' ?>>
                                                        <span class="slider round"></span>
                                                    </label>
                                                    <span class="ml-2 font-weight-bold text-primary">Upload Video File</span>
                                                </div>
                                            </div>

                                            <!-- YouTube -->
                                            <div class="col-md-12 form-group" id="youtube_wrapper"
                                                style="<?= ($edit_data && $edit_data['video_file']) ? 'display:none;' : '' ?>">
                                                <label class="font-weight-bold text-danger">
                                                    <i class="fab fa-youtube mr-1"></i> YouTube Video URL
                                                </label>
                                                <input type="url" name="youtube_link" id="youtube_link"
                                                    class="form-control"
                                                    placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/..."
                                                    value="<?= $edit_data ? htmlspecialchars($edit_data['youtube_link']) : '' ?>" />
                                                <span class="text-danger font-size-sm" id="err_youtube"></span>
                                            </div>

                                            <!-- Video File -->
                                            <div class="col-md-12 form-group" id="video_wrapper"
                                                style="<?= ($edit_data && $edit_data['video_file']) ? '' : 'display:none;' ?>">
                                                <label class="font-weight-bold text-primary">
                                                    <i class="fas fa-file-video mr-1"></i> Upload Video File (MP4/MKV)
                                                </label>
                                                <input type="file" name="video_file" id="video_file"
                                                    class="form-control" accept="video/mp4,video/x-matroska" />
                                                <?php if ($edit_data && $edit_data['video_file']): ?>
                                                    <small class="text-muted">Current: <?= $edit_data['video_file'] ?></small>
                                                <?php endif; ?>
                                                <span class="text-danger font-size-sm" id="err_video"></span>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="is_active" class="form-control">
                                                <option value="1" <?= ($edit_data && $edit_data['is_active'] == 1) ? 'selected' : '' ?>>Active</option>
                                                <option value="0" <?= ($edit_data && $edit_data['is_active'] == 0) ? 'selected' : '' ?>>Inactive</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" name="submit_podcast" class="btn btn-primary mr-2">
                                            <?= $edit_data ? 'Update' : 'Save' ?> Podcast
                                        </button>
                                        <a href="manage-podcast.php" class="btn btn-secondary">Cancel</a>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include('includes/footer-inner.php'); ?>

                <script>
               
                /*
                |--------------------------------------------------
                | Form Validation
                |--------------------------------------------------
                */
                document.getElementById('podcastForm').addEventListener('submit', function (e) {
                    let valid = true;

                    // Clear all errors
                    ['err_title', 'err_thumb', 'err_order', 'err_youtube', 'err_video'].forEach(function (id) {
                        document.getElementById(id).innerText = '';
                    });

                    // Title
                    const title = document.getElementById('podcast_title').value.trim();
                    if (title === '') {
                        document.getElementById('err_title').innerText = 'Podcast title is required.';
                        valid = false;
                    } else if (title.length < 3) {
                        document.getElementById('err_title').innerText = 'Title must be at least 3 characters.';
                        valid = false;
                    }

                    // Thumbnail — only required on add
                    <?php if (!$edit_data): ?>
                    const thumb = document.getElementById('podcast_thumb').files.length;
                    if (thumb === 0) {
                        document.getElementById('err_thumb').innerText = 'Thumbnail image is required.';
                        valid = false;
                    }
                    <?php endif; ?>

                    // Order number
                    const order = document.getElementById('order_no11').value;
                    if (order === '' || isNaN(order) || parseInt(order) < 0) {
                        document.getElementById('err_order').innerText = 'Enter a valid order number (0 or above).';
                        valid = false;
                    }

                    // Media validation
                    const isVideoMode = document.getElementById('mediaTypeToggle').checked;

                    if (!isVideoMode) {
                        // YouTube mode
                        const ytUrl = document.getElementById('youtube_link').value.trim();
                        if (ytUrl === '') {
                            document.getElementById('err_youtube').innerText = 'YouTube URL is required.';
                            valid = false;
                        } else if (!ytUrl.match(/youtube\.com|youtu\.be/)) {
                            document.getElementById('err_youtube').innerText = 'Enter a valid YouTube URL.';
                            valid = false;
                        }
                    } else {
                        // Video file mode — only required on add
                        <?php if (!$edit_data): ?>
                        const videoFile = document.getElementById('video_file').files.length;
                        if (videoFile === 0) {
                            document.getElementById('err_video').innerText = 'Please upload a video file.';
                            valid = false;
                        }
                        <?php endif; ?>
                    }

                    if (!valid) {
                        e.preventDefault();
                        // Scroll to first error
                        const firstErr = document.querySelector('.text-danger:not(:empty)');
                        if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
                </script>

           