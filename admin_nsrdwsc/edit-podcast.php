<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);

// Check if ID is provided
if (!isset($_GET['edit']) || empty($_GET['edit'])) {
    header('Location: manage-podcast.php?error=no_id');
    exit;
}

$id = base64_decode($_GET['edit']);
$edit_data = null;

// Fetch podcast data
$query = mysqli_query($conn, "SELECT * FROM `tbl_podcast` WHERE `podcast_id` = $id");
$edit_data = mysqli_fetch_assoc($query);

if (!$edit_data) {
    header('Location: manage-podcast.php?error=not_found');
    exit;
}

if (isset($_POST['submit_podcast'])) {
    $podcast_id = mysqli_real_escape_string($conn, $_POST['podcast_id']);
    $title = mysqli_real_escape_string($conn, $_POST['podcast_title']);
    $descp = mysqli_real_escape_string($conn, $_POST['podcast_descp']);
    $youtube_link = mysqli_real_escape_string($conn, $_POST['youtube_link']);
    $order_no = intval($_POST['order_no']);
    $is_active = intval($_POST['is_active']);
    $main_podcast = intval($_POST['main_podcast']);

    $thumb_name = $edit_data['podcast_thumb'];
    $video_name = $edit_data['video_file'];

    // Directory paths setup
    if (!is_dir('assets/media/podcast/thumb/')) {
        mkdir('assets/media/podcast/thumb/', 0777, true);
    }
    if (!is_dir('assets/media/podcast/video/')) {
        mkdir('assets/media/podcast/video/', 0777, true);
    }

    // 1. Thumbnail Upload
    if (!empty($_FILES['podcast_thumb']['name'])) {
        // Delete old thumbnail
        if ($edit_data['podcast_thumb'] && file_exists('assets/media/podcast/thumb/' . $edit_data['podcast_thumb'])) {
            unlink('assets/media/podcast/thumb/' . $edit_data['podcast_thumb']);
        }

        $thumb_ext = pathinfo($_FILES['podcast_thumb']['name'], PATHINFO_EXTENSION);
        $thumb_name = "thumb_" . time() . "_" . rand(1000, 9999) . "." . $thumb_ext;
        move_uploaded_file($_FILES['podcast_thumb']['tmp_name'], 'assets/media/podcast/thumb/' . $thumb_name);
    }

    // 2. Local Video Upload
    if (!empty($_FILES['video_file']['name'])) {
        // Delete old video
        if ($edit_data['video_file'] && file_exists('assets/media/podcast/video/' . $edit_data['video_file'])) {
            unlink('assets/media/podcast/video/' . $edit_data['video_file']);
        }

        $video_ext = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
        $video_name = "vid_" . time() . "_" . rand(1000, 9999) . "." . $video_ext;
        move_uploaded_file($_FILES['video_file']['tmp_name'], 'assets/media/podcast/video/' . $video_name);
    }

    $old_data = $logger->getOldRecordData('tbl_podcast', $id);

    $update_query = "UPDATE `tbl_podcast` SET 
                    `podcast_title` = '$title',
                    `podcast_descp` = '$descp',
                    `podcast_thumb` = '$thumb_name',
                    `youtube_link` = '$youtube_link',
                    `video_file` = '$video_name',
                    `is_main` = '$main_podcast',
                    `order_no` = $order_no,
                    `is_active` = $is_active,
                    `updated_on` = NOW()
                    WHERE `podcast_id` = $podcast_id";

    if (mysqli_query($conn, $update_query)) {

        $new_data = $logger->getOldRecordData('tbl_podcast', $id);

        $logger->log(
            'UPDATE',
            'tbl_podcast',
            $id,
            $old_data,
            $new_data,
            'Podcast info updated!'
        );

        $_SESSION['header_info_success_msg'] = "Podcast updated successfully!";

        header('Location:' . BASE_URL . 'edit-podcast.php?edit=' . base64_encode($id));
        exit;
    } else {
        // Pro Tip: Live platform par direct mysqli_error() user ko mat dikhao (Security Risk)
        $_SESSION['header_info_error_msg'] = "<strong>Error!</strong> Something went wrong while updating the Podcast Info.<br><b>" . mysqli_error($conn) . "</b>";
        header('Location:' . BASE_URL . 'edit-podcast.php?edit=' . base64_encode($id));
        exit;
    }
    if (!$query) {
        die("MySQL Error: " . mysqli_error($conn));
    }
}
?>

<body id="kt_body"
    class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
    <!--begin::Main-->
    <!--begin::Header Mobile-->
   <?php include('includes/topbar-mobile.php'); ?>
    <!--end::Header Mobile-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="d-flex flex-row flex-column-fluid page">
            <!--begin::Aside-->
            <?php
            include('includes/sidebar.php');
            ?>
            <!--end::Aside-->
            <!--begin::Wrapper-->
            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                <!--begin::Header-->
                <?php include('includes/topbar-desktop.php'); ?>
                <!--end::Header-->

                <div class="container-fluid mt-4">

					<?php if (isset($_SESSION['header_info_success_msg'])): ?>
						<div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert"
							style="border-left: 4px solid #28a745 !important;">
							<div class="d-flex align-items-center">
								<span class="alert-icon mr-3" style="font-size: 1.2 rem;"> <b>Podcast Info ✔</b> </span>
								<div>
									<?php
									echo $_SESSION['header_info_success_msg'];
									unset($_SESSION['header_info_success_msg']);
									?>
								</div>
							</div>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					<?php endif; ?>

					<?php if (isset($_SESSION['header_info_error_msg'])): ?>
						<div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert"
							style="border-left: 4px solid #dc3545 !important;">
							<div class="d-flex align-items-center">
								<span class="alert-icon mr-3" style="font-size: 1.5rem;"> ⚠ </span>
								<div>
									<?php
									echo $_SESSION['header_info_error_msg'];
									unset($_SESSION['header_info_error_msg']);
									?>
								</div>
							</div>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					<?php endif; ?>
				</div>

                <!--begin::Content-->
                <div class="d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Entry-->
                    <div class="d-flex flex-column-fluid">

                        <div class="container p-5">
                            <div class="card card-custom">
                                <div class="card-header flex-wrap py-3">
                                    <div class="card-title">
                                        <h3 class="card-label">Edit Podcast</h3>
                                    </div>

                                    <div class="card-toolbar">

                                        <a href="<?= BASE_URL ?>manage-podcast.php"
                                            class="btn btn-primary font-weight-bolder">
                                            <span class="svg-icon svg-icon-md">
                                                <i class="bi bi-back"></i>
                                            </span>Back
                                        </a>
                                        &nbsp;
                                        <a href="<?= BASE_URL_HOME ?>podcast/"
                                            class="btn btn-primary font-weight-bolder" target="_blank">
                                            <span class="svg-icon svg-icon-md">
                                                <i class="bi bi-eye"></i>
                                            </span>View
                                        </a>
                                    </div>
                                </div>

                                <form method="POST" enctype="multipart/form-data" class="form">
                                    <input type="hidden" name="podcast_id" value="<?= $edit_data['podcast_id'] ?>">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    <label>Podcast Title *</label>
                                                    <input type="text" name="podcast_title" class="form-control"
                                                        required
                                                        value="<?= htmlspecialchars($edit_data['podcast_title']) ?>" />
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="heroPodcast">Hero Podcast</label><br>
                                                    <input type="checkbox" id="heroPodcast" name="main_podcast"
                                                        value="1" <?= ($edit_data['is_main'] == 1) ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="podcast_descp" class="form-control"
                                                rows="4"><?= htmlspecialchars($edit_data['podcast_descp']) ?></textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Thumbnail Image</label>
                                                <input type="file" name="podcast_thumb" class="form-control"
                                                    accept="image/*" />
                                                <?php if ($edit_data['podcast_thumb']): ?>
                                                    <small class="text-muted">Current: <?= $edit_data['podcast_thumb'] ?>
                                                    </small><br>
                                                    <img src="<?= BASE_URL . 'assets/media/podcast/thumb/' . $edit_data['podcast_thumb'] ?>"
                                                        alt="" style="height: 100px;" class="border p-1">
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Order Number</label>
                                                <input type="number" name="order_no" class="form-control"
                                                    value="<?= $edit_data['order_no'] ?>" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 mb-4">
                                                <label for="ContentType" class="d-block font-weight-bold">Select Media
                                                    Source:</label>
                                                <div class="d-flex align-items-center">
                                                    <span class="mr-2 font-weight-bold text-danger">YouTube Link</span>

                                                    <label class="switch">
                                                        <input type="checkbox" id="mediaTypeToggle"
                                                            <?= ($edit_data['video_file']) ? 'checked' : '' ?>>
                                                        <span class="slider round"></span>
                                                    </label>

                                                    <span class="ml-2 font-weight-bold text-primary">Upload Video
                                                        File</span>
                                                </div>
                                            </div>

                                            <div class="col-md-12 form-group" id="youtube_wrapper"
                                                style="<?= ($edit_data['video_file']) ? 'display: none;' : '' ?>">
                                                <label class="font-weight-bold text-danger"><i
                                                        class="fab fa-youtube mr-1"></i> YouTube Video URL</label>
                                                <input type="url" name="youtube_link" id="youtube_link"
                                                    class="form-control" placeholder="https://www.youtube.com/watch?..."
                                                    value="<?= htmlspecialchars($edit_data['youtube_link']) ?>" />
                                            </div>

                                            <div class="col-md-12 form-group" id="video_wrapper"
                                                style="<?= ($edit_data['video_file']) ? '' : 'display: none;' ?>">
                                                <label class="font-weight-bold text-primary"><i
                                                        class="fas fa-file-video mr-1"></i> Upload Video File
                                                    (MP4/MKV)</label>
                                                <input type="file" name="video_file" id="video_file"
                                                    class="form-control" accept="video/mp4,video/mkv" />
                                                <?php if ($edit_data['video_file']): ?>
                                                    <small class="text-muted">Current:
                                                        <?= $edit_data['video_file'] ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="is_active" class="form-control">
                                                <option value="1" <?= ($edit_data['is_active'] == 1) ? 'selected' : '' ?>>
                                                    Active</option>
                                                <option value="0" <?= ($edit_data['is_active'] == 0) ? 'selected' : '' ?>>
                                                    Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="submit_podcast" class="btn btn-primary mr-2">Update
                                            Podcast</button>
                                        <a href="manage-podcast.php" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

                <?php include('includes/footer-inner.php'); ?>

                <script>
                    document.getElementById('mediaTypeToggle').addEventListener('change', function () {
                        const youtubeWrapper = document.getElementById('youtube_wrapper');
                        const videoWrapper = document.getElementById('video_wrapper');

                        if (this.checked) {
                            youtubeWrapper.style.display = 'none';
                            videoWrapper.style.display = 'block';
                            document.getElementById('youtube_link').removeAttribute('required');
                        } else {
                            youtubeWrapper.style.display = 'block';
                            videoWrapper.style.display = 'none';
                            document.getElementById('youtube_link').setAttribute('required', 'required');
                        }
                    });

                    <?php if ($edit_data['video_file']): ?>
                        document.getElementById('mediaTypeToggle').checked = true;
                        document.getElementById('youtube_wrapper').style.display = 'none';
                        document.getElementById('video_wrapper').style.display = 'block';
                    <?php endif; ?>
                </script>