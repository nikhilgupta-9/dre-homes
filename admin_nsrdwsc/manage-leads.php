<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('db/config.php');
include('includes/header.php');
include('lock2.php');


if (isset($_GET['del']) && isset($_GET['source'])) {

    $id = intval($_GET['del']);
    $source = $_GET['source'];

    switch ($source) {

        case 'Book Valuation':
            $table = 'tbl_book_val_enquiry';
            $column = 'book_val_id';
            break;

        case 'Commercial Dev':
            $table = 'tbl_comm_dev_enquiry';
            $column = 'comm_dev_id';
            break;

        case 'General Enquiry':
            $table = 'tbl_enq';
            $column = 'enq_id';
            break;

        case 'Gold Visa':
            $table = 'tbl_goldvisa_enq';
            $column = 'id';
            break;

        case 'Offplan':
            $table = 'tbl_offplan_enq';
            $column = 'id';
            break;

        case 'PDF Download':
            $table = 'tbl_pdf_enquiry';
            $column = 'pdf_enq_id';
            break;

        case 'Newsletter':
            $table = 'tbl_subscribe_frm';
            $column = 'subscribe_frm_id';
            break;

        case 'Visa Enquiry':
            $table = 'tbl_visa_enquiry';
            $column = 'visa_enq_id';
            break;

        default:
            die("Invalid source");
    }

    $query = mysqli_query($conn, "DELETE FROM $table WHERE $column = $id");

    if ($query) {
        header("Location: " . BASE_URL . "manage-leads.php?source=" . $source . "");
        exit;
    } else {
        echo "Delete failed: " . mysqli_error($conn);
    }
}


$source_filter = "";
if (!empty($_GET['source'])) {
    $source = mysqli_real_escape_string($conn, $_GET['source']);
    $source_filter = "WHERE source = '$source'";
}

$sql = "
SELECT * FROM (

    SELECT 
        book_val_id AS id,
        name,
        email_id AS email,
        mobile AS phone,
        enq_about AS message,
        location AS prop_name,
        appointment_date AS prop_location,
        appointment_time AS prop_bedroom,
        enq_type AS developer,
        totl_bedr AS channel,
        area_sqft AS leads_nature,
        'Book Valuation' AS source,
        tstp AS date
    FROM tbl_book_val_enquiry

    UNION ALL

    SELECT 
        comm_dev_id AS id,
        name,
        email_id AS email,
        phone,
        comment AS message,
        '' AS prop_name,
        '' AS prop_location,
        '' AS prop_bedroom,
        '' AS developer,
        '' AS channel,
        '' AS leads_nature,
        'Commercial Dev' AS source,
        tstp AS date
    FROM tbl_comm_dev_enquiry

    UNION ALL

    SELECT 
        enq_id AS id,
        CONCAT(first_name,' ',last_name) AS name,
        email_id AS email,
        enquiry_phone AS phone,
        enq_comment AS message,
        '' AS prop_name,
        '' AS prop_location,
        '' AS prop_bedroom,
        '' AS developer,
        '' AS channel,
        '' AS leads_nature,
        'General Enquiry' AS source,
        tstp AS date
    FROM tbl_enq

    UNION ALL

    SELECT 
        id,
        name,
        email_id AS email,
        number AS phone,
        message,
        '' AS prop_name,
        '' AS prop_location,
        '' AS prop_bedroom,
        '' AS developer,
        '' AS channel,
        '' AS leads_nature,
        'Gold Visa' AS source,
        tstp AS date
    FROM tbl_goldvisa_enq

    UNION ALL

    SELECT 
        id,
        name,
        email,
        phone,
        message,
        prop_name,
        prop_location,
        prop_bedroom,
        developer,
        channel,
        leads_nature,
        'Offplan' AS source,
        created_at AS date
    FROM tbl_offplan_enq

    UNION ALL

    SELECT 
        pdf_enq_id AS id,
        name,
        email_id AS email,
        phone,
        download_type AS message,
         prop_name,
        prop_location,
        prop_bedroom,
        developer,
        channel,
        leads_nature,
        'PDF Download' AS source,
        tstp AS date
    FROM tbl_pdf_enquiry

    UNION ALL

    SELECT 
        subscribe_frm_id AS id,
        subscribe_frm_email AS name,
        subscribe_frm_email AS email,
        '' AS phone,
        'Subscribed' AS message,
        '' AS prop_name,
        '' AS prop_location,
        '' AS prop_bedroom,
        '' AS developer,
        '' AS channel,
        '' AS leads_nature,
        'Newsletter' AS source,
        tstp AS date
    FROM tbl_subscribe_frm

    UNION ALL

    SELECT 
        visa_enq_id AS id,
        username AS name,
        email_id AS email,
        visa_phone AS phone,
        message,
        '' AS prop_name,
        '' AS prop_location,
        '' AS prop_bedroom,
        '' AS developer,
        '' AS channel,
        '' AS leads_nature,
        'Visa Enquiry' AS source,
        tstp AS date
    FROM tbl_visa_enquiry

) AS combined

$source_filter

ORDER BY date DESC
";
$query = mysqli_query($conn, $sql);

if (!$query) {
    die("SQL Error: " . mysqli_error($conn));
}
?>
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
                    <!--begin::Svg Icon | path:<?= BASE_URL ?>assets/media/svg/icons/General/User.svg-->
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
                                        <h3 class="card-label">Manage Leads</h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <div class="dropdown dropdown-inline mr-2">
                                            <button type="button"
                                                class="btn btn-light-primary font-weight-bolder dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="svg-icon svg-icon-md">
                                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24"></rect>
                                                            <path
                                                                d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                                fill="#000000" opacity="0.3"></path>
                                                            <path
                                                                d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                                fill="#000000"></path>
                                                        </g>
                                                    </svg>
                                                    <!--end::Svg Icon-->
                                                </span>Export
                                            </button>

                                            <!--begin::Dropdown Menu-->
                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right" style="">
                                                <!--begin::Navigation-->
                                                <ul class="navi flex-column navi-hover py-2">
                                                    <li
                                                        class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
                                                        Choose an option:</li>

                                                    <li class="navi-item">
                                                        <a href="<?= BASE_URL ?>export-tags.php" class="navi-link">
                                                            <span class="navi-icon">
                                                                <i class="la la-file-text-o"></i>
                                                            </span>
                                                            <span class="navi-text">XLS</span>
                                                        </a>
                                                    </li>

                                                </ul>
                                                <!--end::Navigation-->
                                            </div>
                                            <!--end::Dropdown Menu-->
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pb-0">
                                    <div class="row">
                                        <?php $selected_source = $_GET['source'] ?? ''; ?>
                                        <div class="col-md-4">
                                            <form method="GET" class="mb-4">
                                                <select name="source" class="form-control"
                                                    onchange="this.form.submit()">
                                                    <option value="">All Leads</option>
                                                    <option value="Book Valuation" <?= ($selected_source == "Book Valuation") ? 'selected' : '' ?>>Book Valuation</option>
                                                    <option value="Commercial Dev" <?= ($selected_source == "Commercial Dev") ? 'selected' : '' ?>>Commercial Dev</option>
                                                    <option value="General Enquiry" <?= ($selected_source == "General Enquiry") ? 'selected' : '' ?>>General Enquiry</option>
                                                    <option value="Gold Visa" <?= ($selected_source == "Gold Visa") ? 'selected' : '' ?>>Gold Visa</option>
                                                    <option value="Offplan" <?= ($selected_source == "Offplan") ? 'selected' : '' ?>>Offplan</option>
                                                    <option value="PDF Download" <?= ($selected_source == "PDF Download") ? 'selected' : '' ?>>PDF Download</option>
                                                    <option value="Newsletter" <?= ($selected_source == "Newsletter") ? 'selected' : '' ?>>Newsletter</option>
                                                    <option value="Visa Enquiry" <?= ($selected_source == "Visa Enquiry") ? 'selected' : '' ?>>Visa Enquiry</option>
                                                </select>
                                            </form>
                                        </div>
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4"></div>
                                    </div>

                                </div>
                                <div class="card-body">

                                    <table id="example" class="table table-bordered  dataTable " style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th style="width: 200px;">Name</th>
                                                <th style="width: 200px;">Email</th>
                                                <th style="width: 200px;">Phone</th>
                                                <th>Message</th>
                                                <th>Source</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $key = 0;
                                            while ($row = mysqli_fetch_assoc($query)) {
                                                ?>
                                                <tr>
                                                    <td><?= ++$key ?></td>
                                                    <td id="firstName"><?= $row['name'] ?></td>
                                                    <td id="leadEmail"><?= $row['email'] ?></td>
                                                    <td id="phoneNo"><?= $row['phone'] ?></td>
                                                    <td>
                                                        <div>
                                                            <strong>MESSAGE - </strong>
                                                            <span class="text-warning">
                                                                <?= htmlspecialchars(!empty($row['message']) ? $row['message'] : ($row['download_type'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?>
                                                            </span>
                                                        </div>

                                                        <?php if ($row['source'] === "Offplan" || $row['source'] === "PDF Download"): ?>
                                                            <ul class="mt-2" style="padding-left: 10px;">
                                                                <li><b>Property Name:</b>
                                                                    <?= htmlspecialchars($row['prop_name']) ?></li>
                                                                <li><b>Location:</b>
                                                                    <?= htmlspecialchars($row['prop_location']) ?></li>
                                                                <li><b>Bedroom:</b>
                                                                    <?= htmlspecialchars($row['prop_bedroom']) ?></li>
                                                                <li><b>Developer:</b> <?= htmlspecialchars($row['developer']) ?>
                                                                </li>
                                                                <li><b>Channel:</b> <?= htmlspecialchars($row['channel']) ?>
                                                                </li>
                                                                <li><b>Leads Type:</b>
                                                                    <?= htmlspecialchars($row['leads_nature']) ?></li>
                                                            </ul>
                                                        <?php elseif ($row['source'] === "Book Valuation"): ?>
                                                            <ul class="mt-2" style="padding-left: 10px;">

                                                                <?php if (!empty($row['prop_name'])): ?>
                                                                    <li><b>Location :</b>
                                                                        <?= htmlspecialchars($row['prop_name'], ENT_QUOTES, 'UTF-8') ?>
                                                                    </li>
                                                                <?php endif; ?>

                                                                <?php if (!empty($row['prop_location'])): ?>
                                                                    <li><b>App Date :</b>
                                                                        <?= htmlspecialchars($row['prop_location'], ENT_QUOTES, 'UTF-8') ?>
                                                                    </li>
                                                                <?php endif; ?>

                                                                <?php if (!empty($row['prop_bedroom'])): ?>
                                                                    <li><b>App Time :</b>
                                                                        <?= htmlspecialchars($row['prop_bedroom'], ENT_QUOTES, 'UTF-8') ?>
                                                                    </li>
                                                                <?php endif; ?>

                                                                <?php if (!empty($row['developer'])): ?>
                                                                    <li><b>Enq Type :</b>
                                                                        <?= htmlspecialchars($row['developer'], ENT_QUOTES, 'UTF-8') ?>
                                                                    </li>
                                                                <?php endif; ?>

                                                                <?php if (!empty($row['channel'])): ?>
                                                                    <li><b>Bedroom :</b>
                                                                        <?= htmlspecialchars($row['channel'], ENT_QUOTES, 'UTF-8') ?>
                                                                    </li>
                                                                <?php endif; ?>

                                                                <?php if (!empty($row['leads_nature'])): ?>
                                                                    <li><b>Area :</b>
                                                                        <?= htmlspecialchars($row['leads_nature'], ENT_QUOTES, 'UTF-8') ?>
                                                                    </li>
                                                                <?php endif; ?>

                                                            </ul>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><span class="badge badge-info"><?= $row['source'] ?></span></td>
                                                    <td><?= date("M d, Y", strtotime($row['date'])) ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-success pushToCRM"
                                                            data-id="<?= $row['id']; ?>"
                                                            data-source="<?= htmlspecialchars($row['source']); ?>"
                                                            data-firstName="<?= $row['name'] ?>"
                                                            data-email="<?= $row['email'] ?>"
                                                            data-phoneNo="<?= $row['phone'] ?>"
                                                            data-leadSource="Organic"
                                                            data-developer="<?= htmlspecialchars($row['developer']) ?>"
                                                            data-unitType="<?= htmlspecialchars($row['prop_bedroom']) ?>"
                                                            data-campaignName="<?= htmlspecialchars($row['prop_name']) ?>"
                                                            data-ipAddress="<?= htmlspecialchars($row['ip_address'] ?? '') ?>"
                                                            data-subLocation="<?= htmlspecialchars($row['prop_location']) ?>"
                                                            >
                                                            Push CRM
                                                        </button>
                                                        <a href="<?= BASE_URL . "manage-leads.php?source=" . $selected_source . "&del=" . $row['id'] . "&source=" . urlencode($row['source']) ?>"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure to delete this record?')">
                                                            <i class="bi bi-trash3"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Message</th>
                                                <th>Source</th>
                                                <th>Date</th>
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