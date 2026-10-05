<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);

$err[] = '';
$id = base64_decode($_GET['edit']);
if (isset($_POST['update_community'])) {

	$comm_thumb_img = 'comm_thumb_img_' . rand(0, 10000) . '_' . $_FILES['comm_thumb_img']['name'];
	$comm_thumb_img_alt = prepStr($_POST['comm_thumb_img_alt']);
	$comm_heading = prepStr($_POST['comm_heading']);
	$comm_location = prepStr($_POST['comm_location']);
	$comm_inner_img = 'comm_inner_img_' . rand(0, 10000) . '_' . $_FILES['comm_inner_img']['name'];
	$comm_inner_thumb_img = 'comm_inner_thumb_img_' . rand(0, 10000) . '_' . $_FILES['comm_inner_thumb_img']['name'];
	$comm_inner_thumb_img_alt = prepStr($_POST['comm_inner_thumb_img_alt']);
	$comm_long_descp = prepStr($_POST['comm_long_descp']);
	$comm_address = prepStr($_POST['comm_address']);
	$comm_phoneno = prepStr($_POST['comm_phoneno']);
	$comm_email = prepStr($_POST['comm_email']);
	$comm_loc_main_heading = prepStr($_POST['comm_loc_main_heading']);
	$comm_loc_main_descp = prepStr($_POST['comm_loc_main_descp']);
	$comm_loc_main_img = 'comm_loc_main_img_' . rand(0, 10000) . '_' . $_FILES['comm_loc_main_img']['name'];
	$comm_map_header = prepStr($_POST['comm_map_header']);
	$comm_map_iframe = prepStr($_POST['comm_map_iframe']);
	$comm_posted_on = date('Y-m-d', strtotime($_POST['comm_posted_on']));
	$comm_slug = prepStr($_POST['comm_slug']);
	$meta_title = prepStr($_POST['meta_title']);
	$meta_keyword = prepStr($_POST['meta_keyword']);
	$meta_description = prepStr($_POST['meta_description']);
	$meta_tags = prepStr($_POST['meta_tags']);
	$comm_showonhomepage = (prepStr($_POST['comm_showonhomepage'] != '' ? $_POST['comm_showonhomepage'] : 2));
	$comm_home_img = 'comm_home_img_' . rand(0, 10000) . '_' . $_FILES['comm_home_img']['name'];
	$comm_featured = (prepStr($_POST['comm_featured'] != '' ? $_POST['comm_featured'] : 2));
	$comm_home_card_content = prepStr($_POST['comm_home_card_content']);
	$is_active = (prepStr($_POST['is_active'] != '' ? 1 : 2));

	$allowed_image_extension = array(
		"png",
		"jpg",
		"jpeg"
	);

	$comm_thumb_file_extension = pathinfo($_FILES["comm_thumb_img"]["name"], PATHINFO_EXTENSION);

	if (!in_array($comm_thumb_file_extension, $allowed_image_extension) && !empty($_FILES['comm_thumb_img']['name'])) {
		$err[] = 'Invalid Thumb Image. Only PNG,JPG and JPEG are allowed.';
	}

	if (!empty($_FILES['comm_thumb_img']['name'])) {
		$comm_thumb_img = 'comm_thumb_img_' . rand(0, 10000) . '_' . $_FILES['comm_thumb_img']['name'];
	} else {
		$comm_thumb_img = $_POST['comm_thumb_img_hidden'];
	}


	$comm_inner_file_extension = pathinfo($_FILES["comm_inner_img"]["name"], PATHINFO_EXTENSION);

	if (!in_array($comm_inner_file_extension, $allowed_image_extension) && !empty($_FILES['comm_inner_img']['name'])) {
		$err[] = 'Invalid Inner Image. Only PNG,JPG and JPEG are allowed.';
	}

	if (!empty($_FILES['comm_inner_img']['name'])) {
		$comm_inner_img = 'comm_inner_img_' . rand(0, 10000) . '_' . $_FILES['comm_inner_img']['name'];
	} else {
		$comm_inner_img = $_POST['comm_inner_img_hidden'];
	}


	$comm_inner_thumb_file_extension = pathinfo($_FILES["comm_inner_thumb_img"]["name"], PATHINFO_EXTENSION);

	if (!in_array($comm_inner_thumb_file_extension, $allowed_image_extension) && !empty($_FILES['comm_inner_thumb_img']['name'])) {
		$err[] = 'Invalid Inner Thumb Image. Only PNG,JPG and JPEG are allowed.';
	}

	if (!empty($_FILES['comm_inner_thumb_img']['name'])) {
		$comm_inner_thumb_img = 'comm_inner_thumb_img_' . rand(0, 10000) . '_' . $_FILES['comm_inner_thumb_img']['name'];
	} else {
		$comm_inner_thumb_img = $_POST['comm_inner_thumb_img_hidden'];
	}


	$comm_loc_main_file_extension = pathinfo($_FILES["comm_loc_main_img"]["name"], PATHINFO_EXTENSION);

	if (!in_array($comm_loc_main_file_extension, $allowed_image_extension) && !empty($_FILES['comm_loc_main_img']['name'])) {
		$err[] = 'Invalid Thumb Image. Only PNG,JPG and JPEG are allowed.';
	}

	if (!empty($_FILES['comm_loc_main_img']['name'])) {
		$comm_loc_main_img = 'comm_loc_main_img_' . rand(0, 10000) . '_' . $_FILES['comm_loc_main_img']['name'];
	} else {
		$comm_loc_main_img = $_POST['comm_loc_main_img_hidden'];
	}


	$comm_home_file_extension = pathinfo($_FILES["comm_home_img"]["name"], PATHINFO_EXTENSION);

	if (!in_array($comm_home_file_extension, $allowed_image_extension) && !empty($_FILES['comm_home_img']['name'])) {
		$err[] = 'Invalid Thumb Image. Only PNG,JPG and JPEG are allowed.';
	}

	if (!empty($_FILES['comm_home_img']['name'])) {
		$comm_home_img = 'comm_home_img_' . rand(0, 10000) . '_' . $_FILES['comm_home_img']['name'];
	} else {
		$comm_home_img = $_POST['comm_home_img_hidden'];
	}


	if (count($err) == 1) {

		if (!empty($_FILES['comm_thumb_img']['name'])) {
			move_uploaded_file($_FILES['comm_thumb_img']['tmp_name'], "assets/media/community/thumb/$comm_thumb_img");
			unlink('assets/media/community/thumb/' . $_POST['comm_thumb_img_hidden']);
		}

		if (!empty($_FILES['comm_inner_img']['name'])) {
			move_uploaded_file($_FILES['comm_inner_img']['tmp_name'], "assets/media/community/inner/$comm_inner_img");
			unlink('assets/media/community/inner/' . $_POST['comm_inner_img_hidden']);
		}

		if (!empty($_FILES['comm_inner_thumb_img']['name'])) {
			move_uploaded_file($_FILES['comm_inner_thumb_img']['tmp_name'], "assets/media/community/inner-thumb/$comm_inner_thumb_img");
			unlink('assets/media/community/inner-thumb/' . $_POST['comm_inner_thumb_img_hidden']);
		}

		if (!empty($_FILES['comm_loc_main_img']['name'])) {
			move_uploaded_file($_FILES['comm_loc_main_img']['tmp_name'], "assets/media/community/location/$comm_loc_main_img");
			unlink('assets/media/community/location/' . $_POST['comm_loc_main_img_hidden']);
		}

		if (!empty($_FILES['comm_home_img']['name'])) {
			$upload_path = 'assets/media/community/home/';
			if (!file_exists($upload_path)) {
				mkdir($upload_path, 0777, true);
			}
			move_uploaded_file($_FILES['comm_home_img']['tmp_name'], $upload_path . $comm_home_img);
			unlink('assets/media/community/home/' . $_POST['comm_home_img_hidden']);
		}

		$old_data = $logger->getOldRecordData('tbl_community', $id);

		$query = mysqli_query($conn, "update tbl_community
								SET comm_thumb_img='$comm_thumb_img',
									comm_thumb_img_alt='$comm_thumb_img_alt',
									comm_heading='$comm_heading',
									comm_location='$comm_location',
									comm_inner_img='$comm_inner_img',
									comm_inner_thumb_img='$comm_inner_thumb_img',
									comm_inner_thumb_img_alt='$comm_inner_thumb_img_alt',
									comm_long_descp='$comm_long_descp',
									comm_address='$comm_address',
									comm_phoneno='$comm_phoneno',
									comm_email='$comm_email',
									comm_loc_main_heading='$comm_loc_main_heading',
									comm_loc_main_descp='$comm_loc_main_descp',
									comm_loc_main_img='$comm_loc_main_img',
									comm_map_heading='$comm_map_header',
                                    comm_map_iframe='$comm_map_iframe',
									comm_posted_on='$comm_posted_on',
									comm_slug='$comm_slug',
									meta_title='$meta_title',
									meta_keyword='$meta_keyword',
									meta_description='$meta_description',
									meta_tags='$meta_tags',
									comm_showonhomepage='$comm_showonhomepage',
									comm_home_img='$comm_home_img',
									comm_home_card_content='$comm_home_card_content',
									comm_featured='$comm_featured',
									is_active='$is_active',
									tstp='" . $date . "' where comm_id=$id");
		if ($query) {
			$new_data = $logger->getOldRecordData('tbl_community', $id);

			$logger->log(
				'UPDATE',
				'tbl_community',
				$id,
				$old_data,
				$new_data,
				'Community Profile Updated : ' . $comm_heading
			);
			header('Location:' . BASE_URL . 'manage-community.php');
		}
	}

}

$details = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_community where comm_id=$id"));
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
				<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

					<!--begin::Entry-->
					<div class="d-flex flex-column-fluid">
						<!--begin::Container-->
						<div class="container">

							<!--begin::Card-->
							<div class="card card-custom gutter-b">
								<div class="card-header flex-wrap py-3">
									<div class="card-title">
										<h3 class="card-label">Edit Community</h3>
									</div>
									<div class="card-toolbar">
										<!--begin::Button-->
										<a href="<?= BASE_URL ?>manage-community.php"
											class="btn btn-primary font-weight-bolder">
											<span class="svg-icon svg-icon-md">
												<!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
												<svg xmlns="http://www.w3.org/2000/svg"
													xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
													height="24px" viewBox="0 0 24 24" version="1.1">
													<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
														<rect x="0" y="0" width="24" height="24"></rect>
														<circle fill="#000000" cx="9" cy="15" r="6"></circle>
														<path
															d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z"
															fill="#000000" opacity="0.3"></path>
													</g>
												</svg>
												<!--end::Svg Icon-->
											</span>Go Back</a>
										<!--end::Button-->
									</div>
								</div>
								<div class="card-body">
									<?php if (count($err) > 1) { ?>
										<ul class="err_list">
											<?php foreach ($err as $list_err) { ?>
												<li><?= $list_err ?></li>
											<?php } ?>
										</ul>
									<?php } ?>
									<div class=" card-custom gutter-b example example-compact">


										<!-- Edit Community Form - Systematic Rearrangement -->
										<form action="" enctype="multipart/form-data" method="post" id="edit-community"
											class="needs-validation" novalidate>
											<!-- Hidden fields for image persistence -->
											<input type="hidden" name="comm_thumb_img_hidden"
												value="<?= $details['comm_thumb_img'] ?>">
											<input type="hidden" name="comm_inner_img_hidden"
												value="<?= $details['comm_inner_img'] ?>">
											<input type="hidden" name="comm_inner_thumb_img_hidden"
												value="<?= $details['comm_inner_thumb_img'] ?>">
											<input type="hidden" name="comm_loc_main_img_hidden"
												value="<?= $details['comm_loc_main_img'] ?>">
											<input type="hidden" name="comm_home_img_hidden"
												value="<?= $details['comm_home_img'] ?>">
											<!-- <div class="card shadow-sm border-0 rounded-lg"> -->


											<div class="card-body">

												<!-- ===================== SECTION 1: BASIC INFORMATION ===================== -->
												<div class="mb-4 pb-2 border-bottom">
													<h6 class="mb-3 font-weight-bold text-secondary"><i
															class="fas fa-info-circle mr-1"></i> Basic Information</h6>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label for="comm_heading">Heading <span
																		class="text-danger">*</span></label>
																<input type="text" name="comm_heading"
																	value="<?= $details['comm_heading'] ?>"
																	class="form-control" placeholder="Community Heading"
																	autocomplete="off">
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="comm_location">Location <span
																		class="text-danger">*</span></label>
																<input type="text" name="comm_location"
																	value="<?= $details['comm_location'] ?>"
																	class="form-control" placeholder="Location"
																	autocomplete="off">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label for="comm_slug">Slug</label>
																<input type="text" name="comm_slug"
																	value="<?= $details['comm_slug'] ?>"
																	class="form-control"
																	placeholder="URL Slug (auto-friendly)"
																	autocomplete="off">
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="comm_posted_on">Posted On</label>
																<input type="text" name="comm_posted_on"
																	value="<?= date('d-m-Y', strtotime($details['comm_posted_on'])) ?>"
																	class="form-control datepicker-input"
																	placeholder="DD-MM-YYYY" autocomplete="off"
																	id="kt_datepicker_1">
															</div>
														</div>
													</div>
												</div>

												<!-- ===================== SECTION 2: MEDIA ASSETS (THUMB + INNER IMAGES) ===================== -->
												<div class="mb-4 pb-2 border-bottom">
													<h6 class="mb-3 font-weight-bold text-secondary"><i
															class="fas fa-images mr-1"></i> Media Gallery</h6>
													<div class="row">
														<!-- Thumb Image -->
														<div class="col-md-4">
															<div class="form-group">
																<label>Thumb Image <span
																		class="text-danger">*</span></label>
																<div class="custom-file">
																	<input type="file" name="comm_thumb_img"
																		class="custom-file-input" id="thumbImgInput">
																	<label class="custom-file-label"
																		for="thumbImgInput">Choose file</label>
																</div>
																<?php if (!empty($details['comm_thumb_img'])): ?>
																	<div class="mt-2">
																		<img src="<?= BASE_URL ?>assets/media/community/thumb/<?= $details['comm_thumb_img'] ?>"
																			width="60"
																			class="img-thumbnail rounded shadow-sm">
																		<small class="text-muted d-block mt-1">Current:
																			<?= $details['comm_thumb_img'] ?></small>
																	</div>
																<?php endif; ?>
															</div>
														</div>
														<!-- Thumb Alt Tag -->
														<div class="col-md-4">
															<div class="form-group">
																<label>Thumb Image Alt Tag <span
																		class="text-danger">*</span></label>
																<input type="text" name="comm_thumb_img_alt"
																	value="<?= $details['comm_thumb_img_alt'] ?>"
																	class="form-control" placeholder="Alt text for SEO">
															</div>
														</div>
														<!-- Inner Image -->
														<div class="col-md-4">
															<div class="form-group">
																<label>Inner Image <span
																		class="text-danger">*</span></label>
																<div class="custom-file">
																	<input type="file" name="comm_inner_img"
																		class="custom-file-input" id="innerImgInput">
																	<label class="custom-file-label"
																		for="innerImgInput">Choose file</label>
																</div>
																<?php if (!empty($details['comm_inner_img'])): ?>
																	<div class="mt-2">
																		<img src="<?= BASE_URL ?>assets/media/community/inner/<?= $details['comm_inner_img'] ?>"
																			width="60"
																			class="img-thumbnail rounded shadow-sm">
																	</div>
																<?php endif; ?>
															</div>
														</div>
													</div>
													<div class="row mt-2">
														<!-- Inner Thumb Image -->
														<div class="col-md-4">
															<div class="form-group">
																<label>Inner Thumb Image <span
																		class="text-danger">*</span></label>
																<div class="custom-file">
																	<input type="file" name="comm_inner_thumb_img"
																		class="custom-file-input" id="innerThumbInput">
																	<label class="custom-file-label"
																		for="innerThumbInput">Choose file</label>
																</div>
																<?php if (!empty($details['comm_inner_thumb_img'])): ?>
																	<div class="mt-2">
																		<img src="<?= BASE_URL ?>assets/media/community/inner-thumb/<?= $details['comm_inner_thumb_img'] ?>"
																			width="60"
																			class="img-thumbnail rounded shadow-sm">
																	</div>
																<?php endif; ?>
															</div>
														</div>
														<!-- Inner Thumb Alt Tag -->
														<div class="col-md-4">
															<div class="form-group">
																<label>Inner Thumb Image Alt Tag <span
																		class="text-danger">*</span></label>
																<input type="text" name="comm_inner_thumb_img_alt"
																	value="<?= $details['comm_inner_thumb_img_alt'] ?>"
																	class="form-control"
																	placeholder="Alt tag for inner thumbnail">
															</div>
														</div>
														<div class="col-md-4">
															<div class="form-group">
																<label for="cardImg">Img On Home page card</label>
																<!-- <input type="file" name="comm_home_img" class="form-control" value="<?= $details['comm_home_img'] ?>" placeholder="Community Home page img"> -->

																<div class="custom-file">
																	<input type="file" name="comm_home_img"
																		class="custom-file-input" id="innerHomeImgInput"
																		value="<?= $details['comm_home_img'] ?>">
																	<label class="custom-file-label"
																		for="innerHomeImgInput">Choose file</label>
																</div>
																<?php if (!empty($details['comm_home_img'])): ?>
																	<div class="mt-2">
																		<img src="<?= BASE_URL ?>assets/media/community/home/<?= $details['comm_home_img'] ?>"
																			width="60"
																			class="img-thumbnail rounded shadow-sm">
																	</div>
																<?php endif; ?>
															</div>
														</div>

														<div class="col-md-4">
															<div class="form-group">
																<label>Home community card conetent <span
																		class="text-danger">*</span></label>
																<input type="text" name="comm_home_card_content"
																	value="<?= $details['comm_home_card_content'] ?>"
																	class="form-control"
																	placeholder="Home community card conetent" required>
															</div>
														</div>
													</div>
												</div>

												<!-- ===================== SECTION 3: LONG DESCRIPTION (TINYMCE) ===================== -->
												<div class="mb-4 pb-2 border-bottom">
													<h6 class="mb-3 font-weight-bold text-secondary"><i
															class="fas fa-paragraph mr-1"></i> Long Description</h6>
													<div class="form-group">
														<label>Long Description <span
																class="text-danger">*</span></label>
														<textarea name="comm_long_descp"
															class="form-control tinymce-editor" id="kt-tinymce-4"
															rows="8"
															placeholder="Detailed description about community"><?= $details['comm_long_descp'] ?></textarea>
													</div>
												</div>

												<!-- ===================== SECTION 4: CONTACT & ADDRESS ===================== -->
												<div class="mb-4 pb-2 border-bottom">
													<h6 class="mb-3 font-weight-bold text-secondary"><i
															class="fas fa-address-card mr-1"></i> Contact Information
													</h6>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label>Address</label>
																<input type="text" name="comm_address"
																	value="<?= $details['comm_address'] ?>"
																	class="form-control" placeholder="Full address">
															</div>
														</div>
														<div class="col-md-3">
															<div class="form-group">
																<label>Phone Number</label>
																<input type="text" name="comm_phoneno"
																	value="<?= $details['comm_phoneno'] ?>"
																	class="form-control" placeholder="+1 234 567 890">
															</div>
														</div>
														<div class="col-md-3">
															<div class="form-group">
																<label>Email ID</label>
																<input type="email" name="comm_email"
																	value="<?= $details['comm_email'] ?>"
																	class="form-control"
																	placeholder="community@example.com">
															</div>
														</div>
													</div>
												</div>

												<!-- ===================== SECTION 5: LOCATION DETAILS (HEADING, DESC, IMAGE) ===================== -->
												<div class="mb-4 pb-2 border-bottom">
													<h6 class="mb-3 font-weight-bold text-secondary"><i
															class="fas fa-map-marker-alt mr-1"></i> Location Feature
														Block</h6>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label>Location Heading</label>
																<input type="text" name="comm_loc_main_heading"
																	value="<?= $details['comm_loc_main_heading'] ?>"
																	class="form-control"
																	placeholder="e.g., Prime Location">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-7">
															<div class="form-group">
																<label>Location Description <span
																		class="text-danger">*</span></label>
																<textarea name="comm_loc_main_descp"
																	class="form-control tinymce-editor"
																	id="kt-tinymce-5" rows="5"
																	placeholder="Describe the location advantages"><?= $details['comm_loc_main_descp'] ?></textarea>
															</div>
														</div>
														<div class="col-md-5">
															<div class="form-group">
																<label>Location Image <span
																		class="text-danger">*</span></label>
																<div class="custom-file">
																	<input type="file" name="comm_loc_main_img"
																		class="custom-file-input" id="locMainImgInput">
																	<label class="custom-file-label"
																		for="locMainImgInput">Choose image</label>
																</div>
																<?php if (!empty($details['comm_loc_main_img'])): ?>
																	<div class="mt-2">
																		<img src="<?= BASE_URL ?>assets/media/community/location/<?= $details['comm_loc_main_img'] ?>"
																			width="70" class="img-thumbnail rounded">
																		<span
																			class="badge badge-secondary ml-1">Current</span>
																	</div>
																<?php endif; ?>
															</div>
														</div>
													</div>
												</div>

												<!-- ===================== SECTION 6: MAPS & IFRAME ===================== -->
												<div class="mb-4 pb-2 border-bottom">
													<h6 class="mb-3 font-weight-bold text-secondary"><i
															class="fas fa-map mr-1"></i> Maps Integration</h6>
													<div class="row">
														<div class="col-md-4">
															<div class="form-group">
																<label>Maps Heading</label>
																<input type="text" name="comm_map_header"
																	value="<?= $details['comm_map_heading'] ?>"
																	class="form-control"
																	placeholder="Section title for map">
															</div>
														</div>
														<div class="col-md-8">
															<div class="form-group">
																<label>Maps Iframe</label>
																<input type="text" name="comm_map_iframe"
																	value="<?= $details['comm_map_iframe'] ?>"
																	class="form-control"
																	placeholder="Paste Google Maps iframe embed code">
																<small class="form-text text-muted">Copy full iframe src
																	or embed code</small>
															</div>
														</div>
													</div>
												</div>

												<!-- ===================== SECTION 7: SEO META DATA ===================== -->
												<div class="mb-4 pb-2 border-bottom">
													<h6 class="mb-3 font-weight-bold text-secondary"><i
															class="fas fa-chart-line mr-1"></i> SEO & Meta Tags</h6>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label>Meta Title</label>
																<input type="text" name="meta_title"
																	value="<?= $details['meta_title'] ?>"
																	class="form-control"
																	placeholder="Browser title for SEO">
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label>Meta Keyword</label>
																<input type="text" name="meta_keyword"
																	value="<?= $details['meta_keyword'] ?>"
																	class="form-control"
																	placeholder="comma, separated, keywords">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label>Meta Description</label>
																<textarea name="meta_description" class="form-control"
																	rows="3"
																	placeholder="Brief meta description (150-160 chars)"><?= $details['meta_description'] ?></textarea>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label>Meta Tags</label>
																<textarea name="meta_tags" class="form-control" rows="3"
																	placeholder="Additional meta tags, one per line or comma"><?= $details['meta_tags'] ?></textarea>
															</div>
														</div>
													</div>
												</div>

												<!-- ===================== SECTION 8: SETTINGS & STATUS ===================== -->
												<div class="mb-4">
													<h6 class="mb-3 font-weight-bold text-secondary"><i
															class="fas fa-toggle-on mr-1"></i> Visibility & Features
													</h6>
													<div class="row align-items-center">
														<div class="col-md-4">
															<div class="custom-control custom-switch custom-control-lg">
																<input type="checkbox" class="custom-control-input"
																	name="comm_showonhomepage" value="1"
																	id="showOnHomepage"
																	<?= ($details['comm_showonhomepage'] == 1 ? 'checked' : '') ?>>
																<label class="custom-control-label font-weight-normal"
																	for="showOnHomepage">Show On Homepage</label>
															</div>
														</div>
														<div class="col-md-4">
															<div class="custom-control custom-switch">
																<input type="checkbox" class="custom-control-input"
																	name="comm_featured" value="1" id="featuredCheck"
																	<?= ($details['comm_featured'] == 1 ? 'checked' : '') ?>>
																<label class="custom-control-label"
																	for="featuredCheck">Mark as Featured</label>
															</div>
														</div>
														<div class="col-md-4">
															<div class="custom-control custom-switch">
																<input type="checkbox" class="custom-control-input"
																	name="is_active" value="1" id="statusSwitch"
																	<?= ($details['is_active'] == 1 ? 'checked' : '') ?>>
																<label class="custom-control-label"
																	for="statusSwitch">Active Status</label>
															</div>
															<small class="form-text text-muted">Enable/disable community
																visibility</small>
														</div>
													</div>
												</div>

											</div> <!-- end card-body -->

											<div
												class="card-footer bg-light border-0 d-flex justify-content-between flex-wrap">
												<button type="submit" name="update_community"
													class="btn btn-primary px-5 py-2">
													<i class="fas fa-save mr-2"></i> Update Community
												</button>
												<button type="reset" class="btn btn-secondary px-4">
													<i class="fas fa-undo-alt mr-2"></i> Reset Fields
												</button>
											</div>
											<!-- </div> -->
										</form>

										<!-- Initialize TinyMCE editors and custom file input labels (requires jQuery & TinyMCE) -->
										<script>
											// TinyMCE initialization for both textareas (assuming you have TinyMCE cloud or local)
											if (typeof tinymce !== 'undefined') {
												tinymce.init({
													selector: '#kt-tinymce-4',
													height: 300,
													menubar: true,
													plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
													toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
													branding: false
												});
												tinymce.init({
													selector: '#kt-tinymce-5',
													height: 220,
													menubar: false,
													plugins: 'advlist autolink lists link charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
													toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link',
													branding: false
												});
											} else {
												console.warn("TinyMCE not loaded, ensure CDN is included.");
											}

											// Bootstrap 4 custom file input - update label on file selection
											$(document).ready(function () {
												// Update file input labels for all custom-file inputs
												$('.custom-file-input').on('change', function () {
													let fileName = $(this).val().split('\\').pop();
													$(this).next('.custom-file-label').addClass("selected").html(fileName || "Choose file");
												});

												// Flatpickr for datepicker (optional but keeps style consistent)
												if (typeof flatpickr !== 'undefined') {
													flatpickr("#kt_datepicker_1", {
														dateFormat: "d-m-Y",
														allowInput: true,
														placeholder: "DD-MM-YYYY"
													});
												} else {
													// Fallback: native date input style but we keep text input format
													console.log("Flatpickr not loaded, using manual date input.");
												}
											});
										</script>

										<!-- Additional CSS to improve spacing and alignments within admin panel -->

									</div>



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