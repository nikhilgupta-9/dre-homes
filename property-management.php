<?php
include('db/config.php');
$meta_tags = select_row("tbl_tags", "id=37 and is_active=1");
include('common/header.php');

if (isset($_POST['contact_submit'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email_id = $_POST['email_id'];
    $phone = $_POST['contact_phone_hidden'] . ' ' . $_POST['phone'];
    $message_input = trim($_POST['message']); // get the message from the form
    $ip_address = get_client_ip();
    $ddate = date('d-m-Y H:i:s');

    $to = 'sales@drehomes.com';
    $subject = "$ddate : Enquiry from Property Management Services : Contact Us";

    $message = "Name: $first_name $last_name\r\n";
    $message .= "Phone Number: $phone\r\n";
    $message .= "Email ID: $email_id\r\n";
    $message .= "Message: $message_input\r\n"; // <-- add message to email
    $message .= "IP Address: $ip_address\r\n";

    $headers = "From: $first_name $last_name <$email_id>\r\n";
    $headers .= "Reply-To: sales@drehomes.com\r\n";
    $headers .= "Cc: leads@drehomes.ae\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo "<script>alert('Your details have been submitted successfully! We will get back to you shortly.')</script>";
    } else {
        echo "<script>alert('Failed to send email. Please try again later.')</script>";
    }
}

?>


<!--warpper-->
<div class="wrapper">
    <!--content-->
    <div class="content">
        <!--section-->
        <div class="section hero-section hero-section_sin">
            <div class="hero-section-wrap">
                <div class="hero-section-wrap-item">
                    <div class="container">
                        <div class="hero-section-container">
                            <div class="hero-section-title">
                                <h1>Property Management</h1>
                            </div>
                        </div>
                    </div>
                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                        <div class="bg" data-bg="images/bg/img_5.jpg"
                            data-scrollax="properties: { translateY: '30%' }">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--section-end-->
        <!--container-->
        <div class="container">
            <!--breadcrumbs-list-->
            <div class="breadcrumbs-list bl_flat">
                <a href="index.php">Home</a><span>Property Management</span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
            <!--breadcrumbs-list end-->
            <!--main-content-->
            <div class="main-content">
                <!--boxed-container-->
                <div class="boxed-container">
                    <div class="scroll-content-wrap">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="post-container">
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>Exceptional Property Management with DRE</h3>
                                            <p>Welcome to DRE, your leading property management partner in
                                                Dubai. Our dedicated team of experts is ready to transform your
                                                property management experience, offering comprehensive services
                                                designed to enhance your property’s value and ensure
                                                unparalleled satisfaction for both property owners and tenants.</p>
                                            <a href="#contact-frm" class="commentssubmit commentssubmit_fw pm-contact">Contact
                                                Us</a>
                                        </div>


                                        <!--boxed-content-item-->
                                        <div class="boxed-content-item">
                                            <div class="row pm-box">
                                                <div class="col-12">
                                                    <h3>Why Choose DRE for Property Management?</h3>

                                                </div>
                                            </div>
                                            <div class="row box-grid-pm mt-5">
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="pm-box-cr">
                                                        <div class=" choose-dre-img"><img
                                                                src="images/why-choose-dre/360°-Service-Excellence.png"
                                                                alt="Service Excellence"></div>
                                                        <h5 class="my-3">360° Service Excellence</h5>
                                                        <p>We offer a complete suite of property management services
                                                            tailored to meet your unique needs. From tenant sourcing and
                                                            lease management to maintenance and financial reporting, we
                                                            handle every aspect of property management with precision
                                                            and care.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="pm-box-cr">
                                                        <div class=" choose-dre-img"><img
                                                                src="images/why-choose-dre/Innovative-Solutions.png"
                                                                alt="Innovative Solutions"></div>
                                                        <h5 class="my-3">Innovative Solutions</h5>
                                                        <p>Our commitment to leveraging advanced technology means you
                                                            benefit from the latest tools and systems. Whether it’s our
                                                            intuitive property management platform or our proactive
                                                            communication channels, we provide innovative solutions that
                                                            simplify property management and keep you informed every
                                                            step of the way.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="pm-box-cr">
                                                        <div class=" choose-dre-img"><img
                                                                src="images/why-choose-dre/Dedicated-Customer-Care.png"
                                                                alt="Dedicated Customer Care"></div>
                                                        <h5 class="my-3">Dedicated Customer Care</h5>
                                                        <p>Our team is dedicated to delivering exceptional service. We
                                                            understand the importance of personalized care and strive to
                                                            provide attentive, responsive support to both property
                                                            owners and tenants. Your satisfaction is our priority, and
                                                            we work tirelessly to exceed your expectations.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="pm-box-cr">
                                                        <div class=" choose-dre-img"><img
                                                                src="images/why-choose-dre/Commitment-to-Sustainability.png"
                                                                alt="Commitment to Sustainability"></div>
                                                        <h5 class="my-3">Commitment to Sustainability</h5>
                                                        <p>We believe in managing properties with a focus on sustainable
                                                            practices. From energy-efficient solutions to
                                                            environmentally friendly maintenance practices, we ensure
                                                            that your property is managed with a commitment to reducing
                                                            environmental impact and promoting long-term sustainability.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-12">
                                                    <div class="pm-box-cr">
                                                        <div class=" choose-dre-img"><img
                                                                src="images/why-choose-dre/Maximizing-Asset-Value.png"
                                                                alt="Maximizing Asset Value"></div>
                                                        <h5 class="my-3">Maximizing Asset Value</h5>
                                                        <p>Our strategic approach is designed to enhance the value of
                                                            your property. Through meticulous market analysis, proactive
                                                            maintenance, and effective tenant management, we ensure that
                                                            your property achieves its highest potential and delivers
                                                            optimal returns.
                                                        </p>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <!--boxed-content-item end-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="limit-box"></div>
                </div>
                <!--boxed-container end-->

                <div class="pm-parallax" style="background-image: url(images/pm-images/scroll-img1.png);"></div>

                <!-- partner section -->
                <div class="container mt-4">
                    <div class="post-container">
                        <div class="boxed-content">
                            <div class="boxed-content-item">
                                <div class="row d-flex align-items-center">
                                    <div class="col-md-6">
                                        <div class="mission-vision-img">
                                            <img src="images/pm-images/property-managment1.jpg" width="100%">
                                        </div>
                                    </div>
                                    <div class="col-md-6 py-4">
                                        <div class="mission-vision">
                                            <div>
                                                <h5>Vision </h5>
                                                <p>To establish ourselves as the leading property management firm in
                                                    Dubai,
                                                    offering bespoke services to property owners and implementing
                                                    innovative
                                                    solutions to elevate client satisfaction and maximize asset value.
                                                </p>
                                            </div>
                                            <div>
                                                <h5>Mission </h5>
                                                <p>To deliver exceptional property management services through dedicated
                                                    customer care, advanced technology, and a commitment to sustainable
                                                    practices, ensuring optimal returns and tenant satisfaction.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- partner section-->
                <!-- <div class="container">
                    <div class="boxed-container">
                        <div class="boxed-content-item">
                            <h4>KEY SERVICES</h4>
                        </div>
                    </div>
                </div> -->

                <!-- Video section-->
                <!-- Employees key section -->
                <div class="container">
                    <div class="post-container">
                        <div class="boxed-content">
                            <div class="about-wrap boxed-content-item ch-dubai-box">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="about-title ab-hero">
                                            <h2>KEY SERVICES</h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="row agent-page-container">
                                    <div class="col-lg-4 col-md-6 py-4 agent-detail-box pm-service-box">
                                        <!-- listing-item -->
                                        <div class="listing-item agent-equal-box">
                                            <div class="geodir-category-listing">
                                                <div class="geodir-category-img">
                                                    <div class="pm-service-img">
                                                        <img src="images/pm-images/services/Tenant-Acquisition.jpg"
                                                            alt="image">
                                                    </div>
                                                    <div class="overlay"></div>
                                                </div>
                                                <div class="geodir-category-content">
                                                    <h3>Tenant Acquisition and Screening
                                                    </h3>
                                                    <p>Match your property with perfect tenants—think of us as your
                                                        property’s dating app!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 py-4 agent-detail-box pm-service-box">
                                        <!-- listing-item -->
                                        <div class="listing-item agent-equal-box">
                                            <div class="geodir-category-listing">
                                                <div class="geodir-category-img">
                                                    <div class="pm-service-img">
                                                        <img src="images/pm-images/services/Lease-Management.jpg"
                                                            alt="image">
                                                    </div>
                                                    <div class="overlay"></div>
                                                </div>
                                                <div class="geodir-category-content">
                                                    <h3>Lease Management
                                                    </h3>
                                                    <p>Say goodbye to lease drama—we handle all the paperwork so you
                                                        don’t have to!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 py-4 agent-detail-box pm-service-box">
                                        <!-- listing-item -->
                                        <div class="listing-item agent-equal-box">
                                            <div class="geodir-category-listing">
                                                <div class="geodir-category-img">
                                                    <div class="pm-service-img">
                                                        <img src="images/pm-images/services/Rent-Collection-and-Financial-Reporting.jpg"
                                                            alt="image">
                                                    </div>
                                                    <div class="overlay"></div>
                                                </div>
                                                <div class="geodir-category-content">
                                                    <h3>Rent Collection and Financial Reporting
                                                    </h3>
                                                    <p>Get your rent on time and your reports on point—because financial
                                                        clarity shouldn’t be a mystery!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 py-4 agent-detail-box pm-service-box">
                                        <!-- listing-item -->
                                        <div class="listing-item agent-equal-box">
                                            <div class="geodir-category-listing">
                                                <div class="geodir-category-img">
                                                    <div class="pm-service-img">
                                                        <img src="images/pm-images/services/Property-Maintenance-and-Repairs.jpg"
                                                            alt="image">
                                                    </div>
                                                    <div class="overlay"></div>
                                                </div>
                                                <div class="geodir-category-content">
                                                    <h3>Property Maintenance and Repairs
                                                    </h3>
                                                    <p>From leaky faucets to squeaky doors, we fix it all—think of us as
                                                        your property’s personal pit crew!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 py-4 agent-detail-box pm-service-box">
                                        <!-- listing-item -->
                                        <div class="listing-item agent-equal-box">
                                            <div class="geodir-category-listing">
                                                <div class="geodir-category-img">
                                                    <div class="pm-service-img">
                                                        <img src="images/pm-images/services/Tenant-Relations-and-Support.jpg"
                                                            alt="image">
                                                    </div>
                                                    <div class="overlay"></div>
                                                </div>
                                                <div class="geodir-category-content">
                                                    <h3>Tenant Relations and Support
                                                    </h3>
                                                    <p>Happy tenants are our specialty—quick fixes and friendly support
                                                        are just a call away!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 py-4 agent-detail-box pm-service-box">
                                        <!-- listing-item -->
                                        <div class="listing-item agent-equal-box">
                                            <div class="geodir-category-listing">
                                                <div class="geodir-category-img">
                                                    <div class="pm-service-img">
                                                        <img src="images/pm-images/services/Property-Inspections.jpg"
                                                            alt="image">
                                                    </div>
                                                    <div class="overlay"></div>
                                                </div>
                                                <div class="geodir-category-content">
                                                    <h3>Property Inspections
                                                    </h3>
                                                    <p>Spot potential problems before they become headaches—our
                                                        inspections are like a wellness check for your property!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 py-4 agent-detail-box pm-service-box">
                                        <!-- listing-item -->
                                        <div class="listing-item agent-equal-box">
                                            <div class="geodir-category-listing">
                                                <div class="geodir-category-img">
                                                    <div class="pm-service-img">
                                                        <img src="images/pm-images/services/Legal-Compliance-and-Documentation.jpg"
                                                            alt="image">
                                                    </div>
                                                    <div class="overlay"></div>
                                                </div>
                                                <div class="geodir-category-content">
                                                    <h3>Legal Compliance and Documentation
                                                    </h3>
                                                    <p>Navigate the legal maze with ease—our paperwork is as smooth as a
                                                        well-tailored suit!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 py-4 agent-detail-box pm-service-box">
                                        <!-- listing-item -->
                                        <div class="listing-item agent-equal-box">
                                            <div class="geodir-category-listing">
                                                <div class="geodir-category-img">
                                                    <div class="pm-service-img">
                                                        <img src="images/pm-images/services/Marketing-and-Vacancy-Management.jpg"
                                                            alt="image">
                                                    </div>
                                                    <div class="overlay"></div>
                                                </div>
                                                <div class="geodir-category-content">
                                                    <h3>Marketing and Vacancy Management
                                                    </h3>
                                                    <p>Get your property noticed and loved—our marketing mojo turns ‘For
                                                        Rent’ into ‘Sold!’</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 py-4 agent-detail-box pm-service-box">
                                        <!-- listing-item -->
                                        <div class="listing-item agent-equal-box">
                                            <div class="geodir-category-listing">
                                                <div class="geodir-category-img">
                                                    <div class="pm-service-img">
                                                        <img src="images/pm-images/services/Budgeting-and-Financial-Planning.jpg"
                                                            alt="image">
                                                    </div>
                                                    <div class="overlay"></div>
                                                </div>
                                                <div class="geodir-category-content">
                                                    <h3>Budgeting and Financial Planning
                                                    </h3>
                                                    <p>Maximize your investment with financial strategies that make your
                                                        wallet and your accountant smile!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Employees key section-->

                <div class="pm-parallax"
                    style="background-image: url(images/pm-images/Property-Management-Services-background.jpg);"></div>


                <section class="card-section">

                    <h2>OUR TAILORED PROPERTY CARE PACKAGES</h2>
                    <div class="container-fluid">
                        <div class="card">
                            <div class="top-content-p">
                                <div class="text">
                                    <h3 style="text-align:center;">Basic Package</h3>
                                    <h5>3% of Annual Rent</h5>
                                    <h5>(Minimum AED 1,500)</h5>
                                </div>
                            </div>
                            <div class="pm-content">
                                <div class="lower-content-p">
                                    <li> Property Listing and Advertising</li>
                                    <li> Tenant screening & Recruitment</li>
                                    <li> Move-in/Move-out Inspections</li>
                                    <li> Tenancy Agreement Preparation</li>
                                    <li> Ejari Registration</li>
                                    <li> Rent Collection and Deposit</li>
                                    <li> Basic Maintenance Coordination</li>
                                    <li> Tenant Onboarding</li>
                                    <li> Tenant Communication & Updates</li>
                                    <li> Legal Compliance</li>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="top-content-p">
                                <div class="text">
                                    <h3 style="text-align:center;">Standard Package</h3>
                                    <h5>5% of Annual Rent</h5>
                                    <h5>(Minimum AED 5,000)</h5>
                                </div>
                            </div>
                            <div class="pm-content">
                                <div class="lower-content-p">
                                    <li> All Features of the Basic Package</li>
                                    <li> Tenant Background Check</li>
                                    <li> Utility Setup Support</li>
                                    <li> Routine Maintenance Coordination</li>
                                    <li> Issue Resolution</li>
                                    <li> Online Management Platform</li>
                                    <li> Personal Account Representative</li>
                                    <li> Emergency Service Support</li>
                                    <li> Financial Reporting and Budgeting</li>
                                    <li> Eviction Management Support</li>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="top-content-p">
                                <div class="text">
                                    <h3 style="text-align:center;">Premium Package</h3>
                                    <h5>10% of Annual Rent</h5>
                                    <h5>(Minimum AED 10,000)</h5>
                                </div>
                            </div>
                            <div class="pm-content">
                                <div class="lower-content-p">
                                    <li> All Features of the Enhanced Package</li>
                                    <li> Legal Assistance & Guidance</li>
                                    <li> Power of Attorney Services</li>
                                    <li> Key Handover from Developers</li>
                                    <li> Dispute Resolution Services</li>
                                    <li> Dispatch of Legal Notices</li>
                                    <li> Custom Lease Management</li>
                                    <li> Comprehensive Financial Planning</li>
                                    <li> Investment Consulting and Strategy</li>
                                    <li> Emergency Preparedness Panning</li>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <!--boxed-content-item-->
                <div class="boxed-content-item mb-5 py-5 pm-gallery-box">
                    <h5>GALLERY</h5>
                    <!-- gallery starts -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="lightgallery pm-custom-gallery">
                                <div class="gallery-it">
                                    <a href="images/pm-images/maintenance-gallery/AC-Service.jpg"
                                        class="popup-image overlay-wrap">
                                        <img src="images/pm-images/maintenance-gallery/AC-Service.jpg" alt="images">
                                        <div class="overlay-b">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </a>
                                    <h4>HVAC Maintenance</h4>
                                </div>
                                <div class="gallery-it">
                                    <a href="images/pm-images/maintenance-gallery/cleaning-work.jpg"
                                        class="popup-image overlay-wrap">
                                        <img src="images/pm-images/maintenance-gallery/cleaning-work.jpg" alt="images">
                                        <div class="overlay-b">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </a>
                                    <h4>Cleaning & Janitorial Services</h4>
                                </div>
                                <div class="gallery-it">
                                    <a href="images/pm-images/maintenance-gallery/electrical.jpg"
                                        class="popup-image overlay-wrap">
                                        <img src="images/pm-images/maintenance-gallery/electrical.jpg" alt="images">
                                        <div class="overlay-b">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </a>
                                    <h4>Electrical Services</h4>
                                </div>
                                <div class="gallery-it">
                                    <a href="images/pm-images/maintenance-gallery/painting-work.jpg"
                                        class="popup-image overlay-wrap">
                                        <img src="images/pm-images/maintenance-gallery/painting-work.jpg" alt="images">
                                        <div class="overlay-b">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </a>
                                    <h4>Painting & Refinishing</h4>
                                </div>
                                <div class="gallery-it">
                                    <a href="images/pm-images/maintenance-gallery/pest-control.jpg"
                                        class="popup-image overlay-wrap">
                                        <img src="images/pm-images/maintenance-gallery/pest-control.jpg" alt="images">
                                        <div class="overlay-b">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </a>
                                    <h4>Pest Control</h4>
                                </div>
                                <div class="gallery-it">
                                    <a href="images/pm-images/maintenance-gallery/plumbing.jpg"
                                        class="popup-image overlay-wrap">
                                        <img src="images/pm-images/maintenance-gallery/plumbing.jpg" alt="images">
                                        <div class="overlay-b">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </a>
                                    <h4>Plumbing Services</h4>
                                </div>
                                <div class="gallery-it">
                                    <a href="images/pm-images/maintenance-gallery/Handyman-Services.jpg"
                                        class="popup-image overlay-wrap">
                                        <img src="images/pm-images/maintenance-gallery/Handyman-Services.jpg"
                                            alt="images">
                                        <div class="overlay-b">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </a>
                                    <h4>Handyman Services</h4>
                                </div>
                                <div class="gallery-it">
                                    <a href="images/pm-images/maintenance-gallery/Landscaping.jpg"
                                        class="popup-image overlay-wrap">
                                        <img src="images/pm-images/maintenance-gallery/Landscaping.jpg" alt="images">
                                        <div class="overlay-b">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </a>
                                    <h4>Landscaping</h4>
                                </div>
                                <div class="gallery-it">
                                    <a href="images/pm-images/maintenance-gallery/Snagging-&-General-Inspections.jpg"
                                        class="popup-image overlay-wrap">
                                        <img src="images/pm-images/maintenance-gallery/Snagging-&-General-Inspections.jpg"
                                            alt="images">
                                        <div class="overlay-b">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </a>
                                    <h4>Snagging & General Inspections</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- gallery ends -->

                </div>
                <!--boxed-content-item end-->


                <div class="container">
                    <div class="post-container">
                        <div class="boxed-content">
                            <div class="boxed-content-item">
                                <div class="row">
                                    <div class="add-service col-md-6">
                                        <h3>Additional Services</h3>
                                        <div class="as-box">
                                            <h6><i class="fa fa-check-square-o"></i> <span>Property
                                                    Snagging
                                                    and Inspection</span></h6>
                                            <p>Comprehensive evaluation to identify and address potential issues before
                                                tenant
                                                move-in.</p>
                                        </div>
                                        <div class="as-box">
                                            <h6><i class="fa fa-check-square-o"></i> <span>In-House
                                                    Ejari
                                                    Registration</span></h6>
                                            <p>Streamlined registration of tenancy contracts with Ejari directly through
                                                our
                                                office.
                                            </p>
                                        </div>
                                        <div class="as-box">
                                            <h6><i class="fa fa-check-square-o"></i> <span>Power of
                                                    Attorney
                                                    (POA) Services</span></h6>
                                            <p>Representation and decision-making authority on behalf of property
                                                owners.
                                            </p>
                                        </div>
                                        <div class="as-box">
                                            <h6><i class="fa fa-check-square-o"></i> <span>Annual
                                                    Maintenance
                                                    Contract</span></h6>
                                            <p>Regular upkeep and servicing of property systems to ensure long-term
                                                functionality.
                                            </p>
                                        </div>
                                        <div class="as-box">
                                            <h6><i class="fa fa-check-square-o"></i> <span>Property
                                                    Staging
                                                    and Enhancement</span></h6>
                                            <p>Professional staging services to enhance property appeal and attract
                                                potential
                                                tenants.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 add-ser-form-div">
                                        <!-- <img src="images/pm-images/img2.jpg" alt="property management images" width="100%"> -->
                                        <div class="prop-management-div-page-2 custom-form">
                                            <form action="" method="post" class="management-form" id="contact-frm">
                                                <h4>Schedule Your Property Management Call</h4>
                                                <div class="cs-intputwrap">
                                                    <label for="" class="sr-only">First Name</label>
                                                    <i class="fa-light fa-user"></i>
                                                    <input name="first_name" type="text" placeholder="First Name *" value="">
                                                </div>
                                                  <div class="cs-intputwrap">
                                                    <label for="" class="sr-only">Last Name</label>
                                                    <i class="fa-light fa-user"></i>
                                                    <input name="last_name" type="text" placeholder="Last Name *" value="">
                                                </div>
                                                <div class="cs-intputwrap">
                                                    <label for="" class="sr-only">Your Phone</label>

                                                    <input class="phone-input" name="phone" id="book_val_phone"
                                                        type="text" placeholder="Your Phone *" value="">
                                                </div>
                                                <div class="cs-intputwrap">
                                                    <label for="" class="sr-only">Your Email</label>
                                                    <i class="fa-light fa-envelope"></i>
                                                    <input type="text" name="email_id" id="email"
                                                        placeholder="Email Address *" value="">
                                                </div>
                                                <div class="side-f-msg">
                                                    <div class="cs-intputwrap">
                                                        <label for="" class="sr-only">Your Message</label>
                                                        <i class="fa-light fa-comment"></i>
                                                        <textarea placeholder="Message" name="message" class="" col="60"
                                                            row="60"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 text-center">
                                                    <input type='hidden' name="contact_phone_hidden" value=''>
                                                    <button class="commentssubmit" name="contact_submit" style="width:100%" name="">Submit
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <section style="background: #ab98893b;padding: 50px 0px;">
                    <div class="container">
                        <div class="boxed-container no-mb">
                            <div class="boxed-content">
                                <div class="boxed-content-item">
                                    <div class="simplify-prop-management">
                                        <h3>Effortless Onboarding with DRE: Simplify Your Property Management</h3>
                                        <p><i class="fa fa-dot-circle"></i> Get Started: Initiate the process by
                                            filling out our
                                            online form or contacting us via phone or WhatsApp.</p>
                                        <p><i class="fa fa-dot-circle"></i> Meet Your Specialist: Have a one-on-one
                                            consultation
                                            with your dedicated Property Manager to align on your property needs.
                                        </p>
                                        <p><i class="fa fa-dot-circle"></i> Contract and Welcome: Finalize our
                                            collaboration with a
                                            formal property management agreement and receive your exclusive welcome
                                            kit.</p>
                                        <p><i class="fa fa-dot-circle"></i> Smooth Transition: Transfer your
                                            property and necessary
                                            documents seamlessly, with everything in place for a smooth start.</p>
                                        <p><i class="fa fa-dot-circle"></i> Maximize Returns: We handle everything
                                            from marketing
                                            your property and finding ideal tenants to managing the rental process
                                            and providing
                                            complete maintenance and care for your unit.</p>
                                        <p><i class="fa fa-dot-circle"></i> Enjoy the Benefits: Relax and track your
                                            income while we
                                            handle all the details.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>

                <section>
                    <div class="customer-feedback-btn">
                        <div class="modal-box-cr animated-button1 " data-modal="modal-feedback">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            Give Us Your Valuable Feedback
                            
                        </div>
                    </div>
                </section>




            </div>
            <!--main content end-->
            <div class="to_top-btn-wrap">
                <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                <div class="svg-corner svg-corner_white" style="top:0;left:  -40px; transform: rotate(-90deg)">
                </div>
                <div class="svg-corner svg-corner_white" style="top:0;right: -40px; transform: rotate(-180deg)">
                </div>
            </div>
        </div>
        <!-- container end-->
    </div>
    <div id="modal-feedback" class="custom-modal-cr pm-survey-modal">
        <div class="modal-inner-wrapper">
            <div class="modal-left-img dt-vw">
                <img src="images/pm-images/form-img-1.jpg" alt="Feedback Image" class="">
            </div>
            <div class="modal-left-img mob-vw">
                <img src="images/pm-images/form-img-2.jpg" alt="Feedback Image" class="">
            </div>
            <div class="modal-right-content">
                <div class="modal-content-cr">
                    <div class="cr-modal-title">
                        <h4>Customer Feedback Survey for Maintenance Services</h4>
                        
                        <span class="close-cr">&times;</span>
                    </div>
                    <div class="cr-modal-body pm-survey-body">
                        <form id="feedback-form" class="survey-form">
                            <div class="question">
                                <p>1. How satisfied were you with the overall maintenance service?</p>
                                <input type="radio" name="overall_satisfaction" id="overall_satisfied1"
                                    value="Very Satisfied">
                                <label for="overall_satisfied1">Very Satisfied</label><br />
                                <input type="radio" name="overall_satisfaction" id="overall_satisfied2"
                                    value="Satisfied">
                                <label for="overall_satisfied2">Satisfied</label><br />
                                <input type="radio" name="overall_satisfaction" id="overall_satisfied3" value="Neutral">
                                <label for="overall_satisfied3">Neutral</label><br />
                                <input type="radio" name="overall_satisfaction" id="overall_satisfied4"
                                    value="Dissatisfied">
                                <label for="overall_satisfied4">Dissatisfied</label><br />
                                <input type="radio" name="overall_satisfaction" id="overall_satisfied5"
                                    value="Very Dissatisfied">
                                <label for="overall_satisfied5">Very Dissatisfied</label><br />
                                <div id="radioError1" style="color: red; display: none;"></div>
                            </div>

                            <div class="question">
                                <p>2. How would you rate the professionalism of our maintenance staff?</p>
                                <input type="radio" name="professionalism" id="professionalism1" value="Very Satisfied">
                                <label for="professionalism1">Very Satisfied</label><br />
                                <input type="radio" name="professionalism" id="professionalism2" value="Satisfied">
                                <label for="professionalism2">Satisfied</label><br />
                                <input type="radio" name="professionalism" id="professionalism3" value="Neutral">
                                <label for="professionalism3">Neutral</label><br />
                                <input type="radio" name="professionalism" id="professionalism4" value="Dissatisfied">
                                <label for="professionalism4">Dissatisfied</label><br />
                                <input type="radio" name="professionalism" id="professionalism5"
                                    value="Very Dissatisfied">
                                <label for="professionalism5">Very Dissatisfied</label><br />
                                <div id="radioError2" style="color: red; display: none;"></div>
                            </div>

                            <div class="question">
                                <p>3. How satisfied were you with the quality of the work performed?</p>
                                <input type="radio" name="quality_of_work" id="quality1" value="Very Satisfied">
                                <label for="quality1">Very Satisfied</label><br />
                                <input type="radio" name="quality_of_work" id="quality2" value="Satisfied">
                                <label for="quality2">Satisfied</label><br />
                                <input type="radio" name="quality_of_work" id="quality3" value="Neutral">
                                <label for="quality3">Neutral</label><br />
                                <input type="radio" name="quality_of_work" id="quality4" value="Dissatisfied">
                                <label for="quality4">Dissatisfied</label><br />
                                <input type="radio" name="quality_of_work" id="quality5" value="Very Dissatisfied">
                                <label for="quality5">Very Dissatisfied</label><br />
                                <div id="radioError3" style="color: red; display: none;"></div>
                            </div>

                            <div class="question">
                                <p>4. Was the work completed in a timely manner?</p>
                                <input type="radio" name="timely_manner" id="timely_yes" value="Yes">
                                <label for="timely_yes">Yes</label><br />
                                <input type="radio" name="timely_manner" id="timely_no" value="No">
                                <label for="timely_no">No</label><br />
                                <textarea name="timely_explanation" id="timely_explanation"
                                    placeholder="If no, please explain:" style="display: none;"></textarea><br />
                                <div id="radioError4" style="color: red; display: none;"></div>
                            </div>


                            <div class="question">
                                <p>5. How would you rate the communication from our team regarding the maintenance
                                    process?</p>
                                <input type="radio" name="communication" id="communication1" value="Very Satisfied">
                                <label for="communication1">Very Satisfied</label><br />
                                <input type="radio" name="communication" id="communication2" value="Satisfied">
                                <label for="communication2">Satisfied</label><br />
                                <input type="radio" name="communication" id="communication3" value="Neutral">
                                <label for="communication3">Neutral</label><br />
                                <input type="radio" name="communication" id="communication4" value="Dissatisfied">
                                <label for="communication4">Dissatisfied</label><br />
                                <input type="radio" name="communication" id="communication5" value="Very Dissatisfied">
                                <label for="communication5">Very Dissatisfied</label><br />
                                <div id="radioError5" style="color: red; display: none;"></div>
                            </div>

                            <div class="question">
                                <p>6. Were the costs for the maintenance services clearly communicated to you?</p>
                                <input type="radio" name="cost_communication" id="cost_yes" value="Yes">
                                <label for="cost_yes">Yes</label><br />
                                <input type="radio" name="cost_communication" id="cost_no" value="No">
                                <label for="cost_no">No</label><br />
                                <textarea name="cost_explanation" id="cost_explanation"
                                    placeholder="If no, please explain:" style="display: none;"></textarea>
                                <div id="radioError6" style="color: red; display: none;"></div>
                            </div>

                            <div class="question">
                                <p>7. Would you recommend our maintenance services to others?</p>
                                <input type="radio" name="recommendation" id="recommendation1" value="Definitely">
                                <label for="recommendation1">Definitely</label><br />
                                <input type="radio" name="recommendation" id="recommendation2" value="Probably">
                                <label for="recommendation2">Probably</label><br />
                                <input type="radio" name="recommendation" id="recommendation3" value="Not Sure">
                                <label for="recommendation3">Not Sure</label><br />
                                <input type="radio" name="recommendation" id="recommendation4" value="Probably Not">
                                <label for="recommendation4">Probably Not</label><br />
                                <input type="radio" name="recommendation" id="recommendation5" value="Definitely Not">
                                <label for="recommendation5">Definitely Not</label><br />
                                <div id="radioError7" style="color: red; display: none;"></div>
                            </div>

                            <div class="question">
                                <p>8. What did you like most about our service?</p>
                                <textarea name="liked_most"></textarea>
                            </div>

                            <div class="question">
                                <p>9. What areas do you think we could improve?</p>
                                <textarea name="areas_of_improvement"></textarea>
                            </div>

                            <div class="question">
                                <p>10. Any additional comments or suggestions?</p>
                                <textarea name="additional_comments"></textarea>
                            </div>

                            <div class="submit-btn">
                                <button class="commentssubmit" type="submit" name="feedback_submit">Submit
                                    Feedback</button>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
 

    <?php include 'common/footer.php'; ?>
    
       
    
    
<script>
$(document).ready(function() {

    $('input[name="overall_satisfaction"]').on('change', function() {
    $("#radioError1").hide();
    });

    $('input[name="professionalism"]').on('change', function() {
    $("#radioError2").hide();
    });

    $('input[name="quality_of_work"]').on('change', function() {
    $("#radioError3").hide();
    });

    $('input[name="timely_manner"]').on('change', function() {
    $("#radioError4").hide();
    });

    $('input[name="communication"]').on('change', function() {
    $("#radioError5").hide();
    });

      $('input[name="cost_communication"]').on('change', function() {
    $("#radioError6").hide();
    });

         $('input[name="recommendation"]').on('change', function() {
    $("#radioError7").hide();
    });


        // When any radio button is clicked
    $('input[name="cost_communication"]').change(function() {
        if ($(this).val() === 'No') {
            // Show the textarea if "No" is selected
            $('#cost_explanation').show();
        } else {
            // Hide the textarea if "Yes" is selected
            $('#cost_explanation').hide();
        }
    });


     // When any radio button is clicked
    $('input[name="timely_manner"]').change(function() {
        if ($(this).val() === 'No') {
            // Show the textarea if "No" is selected
            $('#timely_explanation').show();
        } else {
            // Hide the textarea if "Yes" is selected
            $('#timely_explanation').hide();
        }
    });

$("#feedback-form").on("submit", function(event) {
    event.preventDefault(); // Prevent the form from submitting
    let isValid = true;
    let errorMessage1 = "Please select your overall satisfaction.";
    let errorMessage2 = "Please rate the professionalism of our staff.";
    let errorMessage3 = "Please rate the quality of the work performed.";
    let errorMessage4 = "Please indicate if the work was completed in a timely manner.";
    let errorMessage5 = "Please rate the communication from our team.";
    let errorMessage6 = "Please indicate if the costs were clearly communicated.";
    let errorMessage7 = "Please indicate if you would recommend our services.";

    // Validate each radio button group
    if ($('input[name="overall_satisfaction"]:checked').length === 0) {
        isValid = false;
        $("#radioError1").text(errorMessage1).show();
    } else {
        $("#radioError1").hide(); // Hide error message if valid
    }

    if ($('input[name="professionalism"]:checked').length === 0) {
        isValid = false;
        $("#radioError2").text(errorMessage2).show();
    } else {
        $("#radioError2").hide(); // Hide error message if valid
    }

    if ($('input[name="quality_of_work"]:checked').length === 0) {
        isValid = false;
        $("#radioError3").text(errorMessage3).show();
    } else {
        $("#radioError3").hide(); // Hide error message if valid
    }

    if ($('input[name="timely_manner"]:checked').length === 0) {
        isValid = false;
        $("#radioError4").text(errorMessage4).show();
    } else {
        $("#radioError4").hide(); // Hide error message if valid
    }

    if ($('input[name="communication"]:checked').length === 0) {
        isValid = false;
        $("#radioError5").text(errorMessage5).show();
    } else {
        $("#radioError5").hide(); // Hide error message if valid
    }

    if ($('input[name="cost_communication"]:checked').length === 0) {
        isValid = false;
        $("#radioError6").text(errorMessage6).show();
    } else {
        $("#radioError6").hide(); // Hide error message if valid
    }

    if ($('input[name="recommendation"]:checked').length === 0) {
        isValid = false;
        $("#radioError7").text(errorMessage7).show();
    } else {
        $("#radioError7").hide(); // Hide error message if valid
    }

    // Proceed with AJAX only if all validations are successful
    if (isValid) {
        $.ajax({
            type: "POST",
            url: "submit_feedback.php", // Your server-side script
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                $('button[name="feedback_submit"]').text('Please Wait...');
                $('button[name="feedback_submit"]').prop('disabled', true);
            },
            success: function(r) {
                $('button[name="feedback_submit"]').text('Submit Feedback');
                $('button[name="feedback_submit"]').prop('disabled', false);
                alert(r.msg);

                $('#timely_explanation').hide();
                $('#cost_explanation').hide();

                // Reset the form after submission
                this.reset(); 
            }.bind(this), // Ensure `this` refers to the form
            error: function() {
                // Handle any errors that occur during the AJAX request
                $('button[name="feedback_submit"]').text('Submit Feedback');
                $('button[name="feedback_submit"]').prop('disabled', false);
                alert("An error occurred. Please try again.");
            }
        });
    }
});
});
</script>