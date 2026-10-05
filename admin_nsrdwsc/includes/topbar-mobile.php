<?php
include_once "HeaderInfo.php";

$header_info = new HeaderInfo($conn);
$header = $header_info->getHeaderInfo()
?>

<div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed bg-primary py-2 px-4">
    <!--begin::Logo-->
    <a href="index.html" class="d-flex align-items-center text-decoration-none">
        <img alt="Logo" src="<?= BASE_URL .'assets/media/header/'. $header['header_logo'] ?>" class="max-h-35px" />
        <span class="text-white font-weight-bold ml-2 d-none d-sm-inline-block">Admin</span>
    </a>
    <!--end::Logo-->

    <!--begin::Toolbar-->
    <div class="d-flex align-items-center">
        
        <!--begin::Aside Mobile Toggle-->
        <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
            <span></span>
        </button>
        <!--end::Aside Mobile Toggle-->

        <!--begin::Divider-->
        <span class="text-white-50 mx-2 d-none d-sm-inline-block">|</span>
        <!--end::Divider-->

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
                            fill="#ffffff" fill-rule="nonzero" opacity="0.7" />
                        <path
                            d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                            fill="#ffffff" fill-rule="nonzero" opacity="0.7" />
                    </g>
                </svg>
                <!--end::Svg Icon-->
            </span>
        </button>
        <!--end::Topbar Mobile Toggle-->

        <!--begin::Notification Badge (Optional)-->
        <span class="position-relative ml-2 d-none d-sm-inline-block">
            <span class="btn btn-sm btn-icon btn-light-primary rounded-circle">
                <i class="fas fa-bell text-primary"></i>
            </span>
            <span class="position-absolute top-0 right-0 badge badge-danger badge-pill" style="font-size: 10px; transform: translate(25%, -25%);">3</span>
        </span>
        <!--end::Notification Badge-->

    </div>
    <!--end::Toolbar-->
</div>

<!-- Font Awesome for optional icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Custom CSS for additional polish -->
<style>
    /* Ensure the mobile header has proper height and shadow */
    #kt_header_mobile {
        min-height: 60px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }
    
    /* Burger icon styling for better visibility on dark background */
    .burger-icon span {
        background-color: #ffffff !important;
    }
    
    .burger-icon span::before,
    .burger-icon span::after {
        background-color: #ffffff !important;
    }
    
    /* Hover effect for the user icon */
    #kt_header_mobile_topbar_toggle:hover .svg-icon svg path {
        fill: #ffffff !important;
        opacity: 1 !important;
    }
    
    /* Smooth logo sizing */
    .max-h-35px {
        max-height: 35px;
        width: auto;
    }
    
    /* Responsive adjustments */
    @media (max-width: 576px) {
        #kt_header_mobile {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
    }
</style>