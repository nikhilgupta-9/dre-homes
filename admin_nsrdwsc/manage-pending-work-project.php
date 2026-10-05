<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');

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
                    <!--begin::Svg Icon | path:<?= BASE_URL ?>assets/media/svg/icons/General/User.svg-->
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24" />
                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                        </g>
                    </svg>
                    <!--end::Svg Icon-->
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
                                        <h3 class="card-label">Project Pending Work</h3>
                                    </div>

                                    <div class="card-toolbar">

                                    </div>
                                </div>
                                <div class="card-body pb-0">
                                    <!-- Filter Controls -->
                                    <div class="row mb-4">
                                        <div class="col-md-3">
                                            <label class="form-label">Filter by Status:</label>
                                            <select id="statusFilter" class="form-control">

                                                <option value="">All</option>
                                                <option value="1">Active</option>
                                                <option value="2">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Filter by Completion:</label>
                                            <select id="completionFilter" class="form-control">
                                                <option value="">All Projects</option>
                                               <?php
                                                $columns = mysqli_query($conn, "SHOW COLUMNS FROM tbl_proj");

                                                while ($col = mysqli_fetch_assoc($columns)) {

                                                    $field = $col['Field'];

                                                    // skip unwanted fields
                                                    if (in_array($field, ['prj_id', 'tstp'])) continue;

                                                    // clean label
                                                    $label = ucwords(str_replace('_', ' ', $field));
                                                ?>
                                                    <option value="<?= $field ?>">
                                                        <?= $label ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Search</label>
                                            <input type="text" name="pending-search" id="pending-search-filter" placeholder="Search" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button id="clearFilters" class="btn btn-secondary form-control">Clear All Filters</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="example" class="table table-bordered  dataTable " style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Project Name</th>
                                                <th>Type</th>
                                                <th>Community</th>
                                                <th>Developer</th>
                                                <th>Pending Fields</th>
                                                <th>Status</th>
                                                <th>Edit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $key = 0;
                                            $query = mysqli_query($conn, "SELECT tp.*, 
                                            tc.comm_heading AS proj_comm_name, 
                                            td.dev_heading AS proj_dev_name, 
                                            ty.proj_type_name AS proj_type 
                                        FROM tbl_proj AS tp 
                                        LEFT JOIN tbl_developer AS td ON tp.prj_dev_id = td.dev_id 
                                        LEFT JOIN tbl_proj_type AS ty ON tp.prj_type_id = ty.prj_type_id 
                                        LEFT JOIN tbl_community AS tc ON tp.prj_comm_id = tc.comm_id 
                                        WHERE tp.prj_type_id IN (1, 2, 3, 4) 
                                        ORDER BY tp.prj_id DESC");

                                            // Define all database fields to check (excluding system fields)
                                            $db_fields = [
                                                'proj_name',
                                                'proj_sub_heading',
                                                'prj_thumb_img',
                                                'proj_price',
                                                'proj_price_type',
                                                'proj_pplan_cont',
                                                'proj_handover',
                                                'proj_days',
                                                'proj_location',
                                                'proj_loc_img',
                                                'proj_bedroom',
                                                'proj_shower',
                                                'proj_area',
                                                'proj_email',
                                                'proj_whatsaapno',
                                                'proj_mobileno',
                                                'proj_inner_img',
                                                'proj_dev_img',
                                                'proj_dev_img_alt',
                                                'proj_size',
                                                'proj_inventory_type',
                                                'proj_start_price',
                                                'proj_overview',
                                                'proj_brochure',
                                                'proj_payplan',
                                                'proj_floorplan',
                                                'proj_fea_fac_cont',
                                                'proj_fea_fac_opt',
                                                'proj_loc_cont',
                                                'proj_loc_img_alt_tag',
                                                'proj_video',
                                                'proj_masterplan',
                                                'proj_mastrpln_img',
                                                'proj_mastrpln_img_alt_tag',
                                                'proj_qr',
                                                'proj_qr_link',
                                                'proj_qr_img',
                                                'proj_slug',
                                                'proj_map',
                                                'meta_title',
                                                'meta_keyword',
                                                'meta_description',
                                                'meta_tags',
                                                'proj_comm_feat',
                                                'proj_showonhomepage',
                                                'proj_markfeat',
                                                'proj_posted_on',
                                                'is_active'
                                            ];

                                            while ($row = mysqli_fetch_assoc($query)) {
                                                $emptyFields = [];

                                                // Check only database fields that are empty
                                                foreach ($db_fields as $field) {
                                                    if (isset($row[$field]) && ($row[$field] === NULL || $row[$field] === '')) {
                                                        $emptyFields[] = $field;
                                                    }
                                                }

                                                $emptyCount = count($emptyFields);
                                                $totalFields = count($db_fields);
                                                $filled = $totalFields - $emptyCount;
                                                $percentage = ($totalFields > 0) ? round(($filled / $totalFields) * 100) : 0;

                                                // Build tooltip text only with empty fields
                                                $tooltipText = '';
                                                if (!empty($emptyFields)) {
                                                    $formatted = array_map(function ($field) {
                                                        return ucwords(str_replace('_', ' ', $field));
                                                    }, $emptyFields);

                                                    $tooltipText = '<ul class="pending-work-list" style="margin:0;padding-left:15px;margin-bottom:0;">';
                                                    foreach ($formatted as $f) {
                                                        $tooltipText .= "<li>$f</li>";
                                                    }
                                                    $tooltipText .= '</ul>';
                                                } else {
                                                    $tooltipText = '✅ All fields completed!';
                                                }

                                                // Determine badge color based on pending count
                                                $badgeColor = 'success';
                                                if ($emptyCount > 5) {
                                                    $badgeColor = 'danger';
                                                } elseif ($emptyCount > 0) {
                                                    $badgeColor = 'warning';
                                                }
                                            ?>
                                                <tr data-empty-fields="<?= implode(',', $emptyFields) ?>">
                                                    <td><?= ++$key ?></td>
                                                    <td><?= htmlspecialchars($row['proj_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['proj_type']) ?></td>
                                                    <td><?= htmlspecialchars($row['proj_comm_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['proj_dev_name']) ?></td>
                                                    <td>
                                                        <span class="badge badge-<?= $badgeColor ?> pending-badge"
                                                            data-toggle="tooltip"
                                                            data-placement="top"
                                                            data-html="true"
                                                            title="<?= htmlspecialchars($tooltipText) ?>">
                                                            <?= $emptyCount ?> Pending
                                                        </span>
                                                        <br>
                                                        <span class="badge badge-success mt-1">
                                                            <?= $filled ?> Filled
                                                        </span>
                                                        <br>
                                                        <small>Total: <?= $totalFields ?></small>
                                                        <br>
                                                        <div class="progress mt-1" style="height: 4px;">
                                                            <div class="progress-bar bg-<?= $badgeColor == 'danger' ? 'danger' : ($badgeColor == 'warning' ? 'warning' : 'success') ?>"
                                                                role="progressbar"
                                                                style="width: <?= $percentage ?>%"
                                                                aria-valuenow="<?= $percentage ?>"
                                                                aria-valuemin="0"
                                                                aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="label label-lg font-weight-bold 
                                                        <?= ($row['is_active'] == 1 ? 'label-light-success' : 'label-light-danger') ?> label-inline">
                                                            <?= ($row['is_active'] == 1 ? 'Active' : 'Inactive') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="<?= BASE_URL ?>edit-project.php?edit=<?= base64_encode($row['prj_id']) ?>" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>ID</th>
                                                <th>Project Name</th>
                                                <th>Type</th>
                                                <th>Community</th>
                                                <th>Developer</th>
                                                <th>Pending Fields</th>
                                                <th>Status</th>
                                                <th>Edit</th>
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