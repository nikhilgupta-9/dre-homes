<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');


if (isset($_GET['del'])) {

    $id = intval($_GET['del']);
    $query = mysqli_query($conn, "delete from tbl_position where pos_id=$id");

    if ($query) {
        header('Location:' . BASE_URL . 'manage-position.php');
    }

}

?>
<!--begin::Body-->

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
            <?php include('includes/sidebar.php'); ?>
            <!--end::Aside-->
            <!--begin::Wrapper-->
            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                <!--begin::Header-->
                <?php include('includes/topbar-desktop.php'); ?>
                <!--end::Header-->


                <!--begin::Content-->
                <div class="d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Entry-->
                    <div class="d-flex flex-column-fluid">
                        <!--begin::Container-->
                        <div class="container mt-2">

                            <!--begin::Card-->
                            <div class="card card-custom gutter-b">
                                <div class="card-header flex-wrap py-3">
                                    <div class="card-title">
                                        <h3 class="card-label">Manage Podcast</h3>
                                    </div>

                                    <div class="card-toolbar">

                                        <a href="<?= BASE_URL ?>add-podcast.php"
                                            class="btn btn-primary font-weight-bolder">
                                            <span class="svg-icon svg-icon-md">
                                                <i class="bi bi-plus-circle-dotted"></i>
                                            </span>Add Podcast
                                        </a>
                                    </div>
                                </div>


                                <div class="card-body">
                                    <table id="example" class="table table-bordered  dataTable " style="width:100%">
                                        <table id="example" class="table table-bordered dataTable" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Thumbnail</th>
                                                    <th>Title</th>
                                                    <th>Order</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Edit</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $key = 0;
                                                $query = mysqli_query($conn, "SELECT * FROM tbl_podcast ORDER BY updated_on DESC");
$first = true;
                                                while ($row = mysqli_fetch_array($query)) {
                                                    ?>
                                                    <tr>
                                                        <td><?= ++$key ?></td>
                                                        <td>
                                                            <?php if (!empty($row['podcast_thumb'])): ?>
                                                                <img src="<?= BASE_URL ?>assets/media/podcast/thumb/<?= $row['podcast_thumb'] ?>"
                                                                    width="60" height="40"
                                                                    style="object-fit:cover; border-radius:4px;" />
                                                            <?php else: ?>
                                                                <span class="label label-inline label-light-dark">No
                                                                    Image</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="font-weight-bolder"><?= htmlspecialchars($row['podcast_title']) ?></span>&nbsp;&nbsp;

                                                            <?php
                                                            if ($row['is_main'] == 1) {
                                                                echo '<span class="badge badge-success">Hero Podcast</span>&nbsp;';
                                                            }

                                                            if ($first && $row['is_main'] == 1) {
                                                                echo '<span class="badge badge-warning">Active</span>';
                                                                $first = false;
                                                            }
                                                            ?>

                                                            <br>
                                                            <small class="text-muted">
                                                                <?= substr(htmlspecialchars($row['podcast_descp']), 0, 60) ?>...
                                                            </small>
                                                        </td>
                                                        <td><?= $row['order_no'] ?></td>
                                                        <td>
                                                            <?php if (!empty($row['youtube_link'])): ?>
                                                                <span
                                                                    class="label label-inline label-light-danger font-weight-bold">YouTube</span>
                                                            <?php else: ?>
                                                                <span
                                                                    class="label label-inline label-light-primary font-weight-bold">Uploaded</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="label label-lg font-weight-bold <?= ($row['is_active'] == 1 ? 'label-light-success' : 'label-light-danger') ?> label-inline">
                                                                <?= ($row['is_active'] == 1 ? 'Active' : 'Inactive') ?>
                                                            </span>
                                                        </td>
                                                        <td><a href="<?= BASE_URL ?>edit-podcast.php?edit=<?= base64_encode($row['podcast_id']) ?>"
                                                                class="btn btn-warning btn-sm">Edit</a></td>
                                                        <td><a href="<?= BASE_URL ?>manage-podcast.php?del=<?= $row['podcast_id'] ?>"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Are you sure you want to delete this podcast?');">Delete</a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Thumbnail</th>
                                                    <th>Title</th>
                                                    <th>Order</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Edit</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                </div>
                            </div>
                            <!--end::Card-->

                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Entry-->
                </div>
                <!--end::Content-->
                <?php
                include('includes/footer-inner.php');
                ?>