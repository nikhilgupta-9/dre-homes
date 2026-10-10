<?php
session_start();
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);

$id = base64_decode($_GET['edit']);

if (isset($_POST['update_project'])) {

	$prj_type_id = mysqli_real_escape_string($conn, $_POST['prj_type_id']);

	// FIX 1: Use isset() before accessing prj_dev_id and prj_comm_id
	$prj_dev_id = !empty($_POST['prj_dev_id']) ? intval($_POST['prj_dev_id']) : 'null';
	// $prj_comm_id = !empty($_POST['prj_comm_id']) ? intval($_POST['prj_comm_id']) : 0;

	$proj_name = mysqli_real_escape_string($conn, $_POST['proj_name']);
	$proj_name_on_card = mysqli_real_escape_string($conn, $_POST['proj_name_on_card']);
	$proj_sub_heading = mysqli_real_escape_string($conn, $_POST['proj_sub_heading']);
	$app_dev_id = mysqli_real_escape_string($conn, $_POST['app_dev_id']);
	$app_comm_id = mysqli_real_escape_string($conn, $_POST['app_comm_id']);
	$longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
	$latitude = mysqli_real_escape_string($conn, $_POST['latitude']);

	$proj_price = isset($_POST['proj_price']) && $_POST['proj_price'] !== ''
		? mysqli_real_escape_string($conn, $_POST['proj_price'])
		: 0;

	$proj_price_type_arr = isset($_POST['proj_price_type']) ? $_POST['proj_price_type'] : [];
	$proj_price_type_cleaned = [];
	foreach ($proj_price_type_arr as $type) {
		$proj_price_type_cleaned[] = mysqli_real_escape_string($conn, $type);
	}
	$proj_price_type = implode(',', $proj_price_type_cleaned);

	$proj_pplan_cont = mysqli_real_escape_string($conn, $_POST['proj_pplan_cont']);
	$proj_days = mysqli_real_escape_string($conn, $_POST['proj_days']);
	$proj_location = mysqli_real_escape_string($conn, $_POST['proj_location']);

	$proj_bedroom_array = isset($_POST['proj_bedroom']) ? $_POST['proj_bedroom'] : [];
	$proj_bedroom_cleaned = [];
	foreach ($proj_bedroom_array as $bedroom) {
		$proj_bedroom_cleaned[] = mysqli_real_escape_string($conn, $bedroom);
	}
	$proj_bedroom = implode(",", $proj_bedroom_cleaned);

	$proj_email = mysqli_real_escape_string($conn, $_POST['proj_email']);
	$proj_whatsaapno = mysqli_real_escape_string($conn, $_POST['proj_whatsaapno']);
	$proj_mobileno = mysqli_real_escape_string($conn, $_POST['proj_mobileno']);
	$proj_dev_img_alt = mysqli_real_escape_string($conn, $_POST['proj_dev_img_alt']);
	$proj_size = mysqli_real_escape_string($conn, $_POST['proj_size']);
	$proj_inventory_type = mysqli_real_escape_string($conn, $_POST['proj_inventory_type']);
	$proj_start_price = mysqli_real_escape_string($conn, $_POST['proj_start_price']);
	$proj_overview = mysqli_real_escape_string($conn, $_POST['proj_overview']);
	$proj_fea_fac_cont = mysqli_real_escape_string($conn, $_POST['proj_fea_fac_cont']);
	$proj_loc_cont = mysqli_real_escape_string($conn, $_POST['proj_loc_cont']);
	$proj_video = mysqli_real_escape_string($conn, $_POST['proj_video']);
	$proj_masterplan = mysqli_real_escape_string($conn, $_POST['proj_masterplan']);
	$proj_qr = mysqli_real_escape_string($conn, $_POST['proj_qr']);
	$proj_qr_link = mysqli_real_escape_string($conn, $_POST['proj_qr_link']);
	$proj_slug = mysqli_real_escape_string($conn, $_POST['proj_slug']);
	$proj_map = mysqli_real_escape_string($conn, $_POST['proj_map']);
	$meta_title = mysqli_real_escape_string($conn, $_POST['meta_title']);
	$meta_keyword = mysqli_real_escape_string($conn, $_POST['meta_keyword']);
	$meta_description = mysqli_real_escape_string($conn, $_POST['meta_description']);
	$meta_tags = mysqli_real_escape_string($conn, $_POST['meta_tags']);

	$proj_showonhomepage = isset($_POST['proj_showonhomepage']) ? intval($_POST['proj_showonhomepage']) : 2;
	$proj_markfeat = isset($_POST['proj_markfeat']) ? intval($_POST['proj_markfeat']) : 2;
	$proj_posted_on = date('Y-m-d', strtotime($_POST['proj_posted_on']));
	$is_active = !empty($_POST['is_active']) ? 1 : 2;

	$prj_thumb_img = $_FILES['prj_thumb_img']['name'];
	$gallery_image_alt = isset($_POST['gallery_image_alt']) ? $_POST['gallery_image_alt'] : [];

	// FIX 2: Safely access gallery_image and feat_fac_img from $_FILES
	$gallery_image = isset($_FILES['gallery_image']['name']) ? $_FILES['gallery_image']['name'] : [];
	$feat_fac_img = isset($_FILES['feat_fac_img']['name']) ? $_FILES['feat_fac_img']['name'] : [];

	$feat_fac_heading = isset($_POST['feat_fac_heading']) ? $_POST['feat_fac_heading'] : [];

	$proj_fea_fac_opt = isset($_POST['proj_fea_fac_opt']) && is_array($_POST['proj_fea_fac_opt'])
		? implode(',', $_POST['proj_fea_fac_opt'])
		: '';

	$count_thumb_img = count($prj_thumb_img);

	$payplan_heading = isset($_POST['payplan_heading']) ? $_POST['payplan_heading'] : [];
	$payplan_percentage = isset($_POST['payplan_percentage']) ? $_POST['payplan_percentage'] : [];
	$payplan_status = isset($_POST['payplan_status']) ? $_POST['payplan_status'] : [];

	$faq_heading = isset($_POST['faq_heading']) ? $_POST['faq_heading'] : [];
	$faq_descp = isset($_POST['faq_descp']) ? $_POST['faq_descp'] : [];

	$proj_shower = isset($_POST['proj_shower']) ? $_POST['proj_shower'] : '';
	$proj_area = isset($_POST['proj_area']) ? $_POST['proj_area'] : '';
	$proj_comm_feat = isset($_POST['proj_comm_feat']) ? implode(',', $_POST['proj_comm_feat']) : '';

	// App Feature toggles (on/off visibility flags for the app)
	$proj_show_down_payment = !empty($_POST['proj_show_down_payment']) ? 1 : 2;
	$proj_show_payment_plan = !empty($_POST['proj_show_payment_plan']) ? 1 : 2;
	$proj_show_handover = !empty($_POST['proj_show_handover']) ? 1 : 2;
	$proj_show_area = !empty($_POST['proj_show_area']) ? 1 : 2;
	$proj_show_bedroom = !empty($_POST['proj_show_bedroom']) ? 1 : 2;
	$proj_show_shower = !empty($_POST['proj_show_shower']) ? 1 : 2;
	$proj_show_size = !empty($_POST['proj_show_size']) ? 1 : 2;
	$proj_show_balcony = !empty($_POST['proj_show_balcony']) ? 1 : 2;

	// App Feature icon + details pairs (5) - icon is a pasted icon tag, not an upload
	for ($fi = 1; $fi <= 5; $fi++) {
		$iconField = "proj_feature_icon_$fi";
		${$iconField} = mysqli_real_escape_string($conn, $_POST[$iconField] ?? '');

		$textField = "proj_feature_text_$fi";
		${$textField} = mysqli_real_escape_string($conn, $_POST[$textField] ?? '');
	}

	// Example SQL Insert or Update (update to your actual logic):




	$countfiles = count(array_filter($_FILES['prj_thumb_img']['name']));

	/*multiple thumb image starts*/
	if ($countfiles > 0) {
		for ($i = 0; $i < $count_thumb_img; $i++) {

			$thumb_name_arr = $_FILES['prj_thumb_img']['name'];
			$thumb_loc_arr = $_FILES['prj_thumb_img']['tmp_name'];


			$thumb_image_name = 'thumb_img_' . rand(0, 10000) . '_' . $thumb_name_arr[$i];
			$thumb_image_loc = $thumb_loc_arr[$i];


			if ($thumb_name_arr[$i] != '') {
				move_uploaded_file($thumb_image_loc, "assets/media/project/thumb/$thumb_image_name");
				$thumb_img_nam .= $thumb_image_name . ',';
			}
		}
		$thumb_img_naming = rtrim($thumb_img_nam, ',');
		if (empty($_POST['prj_thumb_img_hidden'])) {
			$thumb_img_names = $thumb_img_naming;
		} else {
			$thumb_img_names = $_POST['prj_thumb_img_hidden'] . ',' . $thumb_img_naming;
		}

	} else {
		$thumb_img_names = $_POST['prj_thumb_img_hidden'];
	}





	/*multiple thumb image ends*/

	if (!empty($_FILES['proj_inner_img']['name'])) {
		$proj_inner_img = 'proj_inner_img_' . rand(0, 10000) . '_' . $_FILES['proj_inner_img']['name'];
	} else {
		$proj_inner_img = $_POST['proj_inner_img_hidden'];
	}


	if (!empty($_FILES['proj_dev_img']['name'])) {
		$proj_dev_img = 'proj_dev_img_' . rand(0, 10000) . '_' . $_FILES['proj_dev_img']['name'];
	} else {
		$proj_dev_img = $_POST['proj_dev_img_hidden'];
	}


	if (!empty($_FILES['proj_brochure']['name'])) {
		$proj_brochure = 'proj_brochure_' . rand(0, 10000) . '_' . $_FILES['proj_brochure']['name'];
	} else {
		$proj_brochure = $_POST['proj_brochure_hidden'];
	}

	if (!empty($_FILES['proj_payplan']['name'])) {
		$proj_payplan = 'proj_payplan_' . rand(0, 10000) . '_' . $_FILES['proj_payplan']['name'];
	} else {
		$proj_payplan = $_POST['proj_payplan_hidden'];
	}


	if (!empty($_FILES['proj_floorplan']['name'])) {
		$proj_floorplan = 'proj_floorplan_' . rand(0, 10000) . '_' . $_FILES['proj_floorplan']['name'];
	} else {
		$proj_floorplan = $_POST['proj_floorplan_hidden'];
	}


	if (!empty($_FILES['proj_loc_img']['name'])) {
		$proj_loc_img = 'proj_loc_img_' . rand(0, 10000) . '_' . $_FILES['proj_loc_img']['name'];
	} else {
		$proj_loc_img = $_POST['proj_loc_img_hidden'];
	}


	if (!empty($_FILES['proj_mastrpln_img']['name'])) {
		$proj_mastrpln_img = 'proj_mastrpln_img_' . rand(0, 10000) . '_' . $_FILES['proj_mastrpln_img']['name'];
	} else {
		$proj_mastrpln_img = $_POST['proj_mastrpln_img_hidden'];
	}


	if (!empty($_FILES['proj_qr_img']['name'])) {
		$proj_qr_img = 'proj_qr_img_' . rand(0, 10000) . '_' . $_FILES['proj_qr_img']['name'];
	} else {
		$proj_qr_img = $_POST['proj_qr_img_hidden'];
	}

	$old_data = $logger->getOldRecordData('tbl_proj', $id);

	$query = mysqli_query($conn, "update tbl_proj
						           SET prj_type_id='$prj_type_id',
						               prj_dev_id=$prj_dev_id,
                                       proj_name='$proj_name',
                                       proj_name_on_card='$proj_name_on_card',
                                       proj_sub_heading='$proj_sub_heading',
						               prj_thumb_img='$thumb_img_names',
						               proj_price='$proj_price',
						               proj_price_type = '$proj_price_type',
						               proj_pplan_cont='$proj_pplan_cont',
						               proj_days='$proj_days',
						               proj_location='$proj_location',
						               proj_loc_img='$proj_loc_img',
						               proj_bedroom='$proj_bedroom',
						               proj_shower='$proj_shower',
						               proj_area='$proj_area',
						               proj_email='$proj_email',
						               proj_whatsaapno='$proj_whatsaapno',
						               proj_mobileno='$proj_mobileno',
						               proj_inner_img='$proj_inner_img',
						               proj_dev_img='$proj_dev_img',
						               proj_dev_img_alt='$proj_dev_img_alt',
						               proj_size='$proj_size',
						               proj_inventory_type='$proj_inventory_type',
						               proj_start_price='$proj_start_price',
						               proj_overview='$proj_overview',
						               proj_brochure='$proj_brochure',
						               proj_payplan='$proj_payplan',
						               proj_floorplan='$proj_floorplan',
						               proj_fea_fac_cont='$proj_fea_fac_cont',
						               proj_fea_fac_opt='$proj_fea_fac_opt',
						               proj_loc_cont='$proj_loc_cont',
						               proj_video='$proj_video',
						               proj_masterplan='$proj_masterplan',
						               proj_mastrpln_img='$proj_mastrpln_img',
						               proj_qr='$proj_qr',
						               proj_qr_link='$proj_qr_link',
						               proj_qr_img='$proj_qr_img',
						               proj_show_down_payment='$proj_show_down_payment',
						               proj_show_payment_plan='$proj_show_payment_plan',
						               proj_show_handover='$proj_show_handover',
						               proj_show_area='$proj_show_area',
						               proj_show_bedroom='$proj_show_bedroom',
						               proj_show_shower='$proj_show_shower',
						               proj_show_size='$proj_show_size',
						               proj_show_balcony='$proj_show_balcony',
						               proj_feature_icon_1='$proj_feature_icon_1',
						               proj_feature_text_1='$proj_feature_text_1',
						               proj_feature_icon_2='$proj_feature_icon_2',
						               proj_feature_text_2='$proj_feature_text_2',
						               proj_feature_icon_3='$proj_feature_icon_3',
						               proj_feature_text_3='$proj_feature_text_3',
						               proj_feature_icon_4='$proj_feature_icon_4',
						               proj_feature_text_4='$proj_feature_text_4',
						               proj_feature_icon_5='$proj_feature_icon_5',
						               proj_feature_text_5='$proj_feature_text_5',
						               proj_slug='$proj_slug',
						               proj_map='$proj_map',
						               meta_title='$meta_title',
						               meta_keyword='$meta_keyword',
						               meta_description='$meta_description',
						               meta_tags='$meta_tags',
						               proj_comm_feat = '$proj_comm_feat',
						               proj_showonhomepage='$proj_showonhomepage',
						               proj_markfeat='$proj_markfeat',
						               proj_posted_on='$proj_posted_on',
									   app_sync_status='0',
									   app_sync_date=NOW(),
						               app_dev_id='$app_dev_id',
						               app_comm_id='$app_comm_id',
						               latitude='$latitude',
						               longitude='$longitude',
						               is_active='$is_active' where prj_id=$id
	                                   ");


	if ($query) {

		if (!empty($_FILES['proj_inner_img']['name'])) {
			move_uploaded_file($_FILES['proj_inner_img']['tmp_name'], "assets/media/project/inner-image/$proj_inner_img");
			unlink('assets/media/project/inner-image/' . $_POST['proj_inner_img_hidden']);
		}

		if (!empty($_FILES['proj_dev_img']['name'])) {
			move_uploaded_file($_FILES['proj_dev_img']['tmp_name'], "assets/media/project/dev/$proj_dev_img");
			unlink('assets/media/project/dev/' . $_POST['proj_dev_img_hidden']);
		}

		if (!empty($_FILES['proj_brochure']['name'])) {
			move_uploaded_file($_FILES['proj_brochure']['tmp_name'], "assets/media/project/pdf/$proj_brochure");
			unlink('assets/media/project/pdf/' . $_POST['proj_brochure_hidden']);
		}

		if (!empty($_FILES['proj_payplan']['name'])) {
			move_uploaded_file($_FILES['proj_payplan']['tmp_name'], "assets/media/project/pdf/$proj_payplan");
			unlink('assets/media/project/pdf/' . $_POST['proj_payplan_hidden']);
		}


		if (!empty($_FILES['proj_floorplan']['name'])) {
			move_uploaded_file($_FILES['proj_floorplan']['tmp_name'], "assets/media/project/pdf/$proj_floorplan");
			unlink('assets/media/project/pdf/' . $_POST['proj_floorplan_hidden']);
		}

		if (!empty($_FILES['proj_loc_img']['name'])) {
			move_uploaded_file($_FILES['proj_loc_img']['tmp_name'], "assets/media/project/location/$proj_loc_img");
			unlink('assets/media/project/location/' . $_POST['proj_loc_img_hidden']);
		}


		if (!empty($_FILES['proj_mastrpln_img']['name'])) {
			move_uploaded_file($_FILES['proj_mastrpln_img']['tmp_name'], "assets/media/project/masterplan/$proj_mastrpln_img");
			unlink('assets/media/project/masterplan/' . $_POST['proj_mastrpln_img_hidden']);
		}

		if (!empty($_FILES['proj_qr_img']['name'])) {
			move_uploaded_file($_FILES['proj_qr_img']['tmp_name'], "assets/media/project/qr/$proj_qr_img");
			unlink('assets/media/project/qr/' . $_POST['proj_qr_img_hidden']);
		}

		$new_data = $logger->getOldRecordData('tbl_proj', $id);

		$logger->log(
			'UPDATE',
			'tbl_proj',
			$id,
			$old_data,
			$new_data,
			'Rent projects page updated!'
		);

		$_SESSION['header_info_success_msg'] = "Rent project updated successfully!";
		header('Location: edit-rent-project.php?edit=' . base64_encode($id));
		exit;
	} else {
		// Pro Tip: Live platform par direct mysqli_error() user ko mat dikhao (Security Risk)
		$_SESSION['header_info_error_msg'] = "<strong>Error!</strong> Something went wrong while updating the Rent property info.<br><b>" . mysqli_error($conn) . "</b>";
		header('Location: edit-rent-project.php?edit=' . base64_encode($id));
		exit;
	}
	if (!$query) {
		die("MySQL Error: " . mysqli_error($conn));
	}

}

$details = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_proj where prj_id=$id"));

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
								<span class="alert-icon mr-3" style="font-size: 1.2 rem;"> <b><?= $details['proj_name'] ?> ✔</b> </span>
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
				<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

					<!--begin::Entry-->
					<div class="d-flex flex-column-fluid">
						<!--begin::Container-->
						<div class="container">

							<!--begin::Card-->
							<div class="card card-custom gutter-b">
								<div class="card-header flex-wrap py-3">
									<div class="card-title">
										<h3 class="card-label">Edit Rent Project</h3>
									</div>
									<div class="card-toolbar">
										<!--begin::Button-->
										<a href="<?= BASE_URL ?>manage-rent-project.php"
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


									<div class=" card-custom gutter-b example example-compact">


										<?php
										// Your existing DB connection and $details, $id variables assumed to be set above this form
										?>

										<!--begin::Form-->
										<form action="" enctype="multipart/form-data" method="post" id="add-project">

											<!-- Tab Navigation -->
											<ul class="nav nav-tabs nav-tabs-line mb-0" id="projectTabs" role="tablist">
												<li class="nav-item">
													<a class="nav-link active" data-toggle="tab" href="#tab-basic"
														role="tab">
														<i class="flaticon2-information mr-1"></i> Basic Info
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-pricing"
														role="tab">
														<i class="flaticon2-money mr-1"></i> Pricing & Inventory
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-media" role="tab">
														<i class="flaticon2-photo-camera mr-1"></i> Media & Files
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-details"
														role="tab">
														<i class="flaticon2-list-1 mr-1"></i> Property Details
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-content"
														role="tab">
														<i class="flaticon2-document mr-1"></i> Content
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-contact"
														role="tab">
														<i class="flaticon2-phone mr-1"></i> Contact
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-seo" role="tab">
														<i class="flaticon2-search-1 mr-1"></i> SEO
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-settings"
														role="tab">
														<i class="flaticon2-settings mr-1"></i> Settings
													</a>
												</li>
											</ul>

											<div class="tab-content mt-4">

												<!-- ===================== TAB 1: BASIC INFO ===================== -->
												<div class="tab-pane fade show active" id="tab-basic" role="tabpanel">
													<div class="card card-custom">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">Basic Information</h3>
															</div>
														</div>
														<div class="card-body">
															<div class="row">

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Type <span
																				class="text-danger">*</span></label>
																		<select name="prj_type_id" class="form-control">
																			<option value="">Select Project Type
																			</option>
																			<?php
																			$query = mysqli_query($conn, "SELECT *
																						FROM tbl_proj_type
																						WHERE is_active = '1'
																						AND prj_type_id NOT IN (1, 2, 3, 4, 7)
																						ORDER BY proj_type_name;
																					");
																			while ($row = mysqli_fetch_array($query)) { ?>
																				<option value="<?= $row['prj_type_id'] ?>"
																					<?= ($details['prj_type_id'] == $row['prj_type_id'] ? 'selected' : '') ?>>
																					<?= $row['proj_type_name'] ?>
																				</option>
																			<?php } ?>
																		</select>
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Name <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_name"
																			value="<?= $details['proj_name'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Name">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Name (On Card) <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_name_on_card"
																			value="<?= htmlspecialchars($details['proj_name_on_card']) ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Name on Card">
																	</div>
																</div>

																<div class="col-md-12">
																	<div class="form-group">
																		<label>Project Sub Heading <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_sub_heading"
																			value="<?= $details['proj_sub_heading'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Sub Heading">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Developer <span
																				class="text-danger">*</span></label>

																		<select name="app_dev_id" class="form-control">
																			<option value="">Select Project Developer
																			</option>

																			<?php
																			$query = mysqli_query($conn, "
																						SELECT *
																						FROM tbl_developer
																						WHERE is_active = '1'
																						ORDER BY dev_heading
																					");

																			while ($row = mysqli_fetch_assoc($query)) {
																				?>
																				<option value="<?= $row['dev_id'] ?>"
																					<?= ($details['app_dev_id'] == $row['dev_id']) ? 'selected' : '' ?>>
																					<?= htmlspecialchars($row['dev_heading']) ?>
																				</option>
																			<?php } ?>
																		</select>
																	</div>
																</div>
																<div class="col-md-6">
																	<div class="form-group">
																		<label>Community Name <span
																				class="text-danger">*</span></label>
																		<select name="app_comm_id" class="form-control">
																			<option value="">Select Community
																			</option>
																			<?php
																			$query = mysqli_query($conn, "SELECT *
																						FROM tbl_community
																						WHERE is_active = '1'
																						ORDER BY comm_heading;
																					");
																			while ($row = mysqli_fetch_array($query)) { ?>
																				<option value="<?= $row['comm_id'] ?>"
																					<?= ($details['app_comm_id'] == $row['comm_id'] ? 'selected' : '') ?>>
																					<?= $row['comm_heading'] ?>
																				</option>
																			<?php } ?>
																		</select>
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Location <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_location"
																			value="<?= $details['proj_location'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Location">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label for="latitude">Project Latitude <span
																				class="text-danger">*</span></label>
																		<input type="text" id="latitude" name="latitude"
																			value="<?= htmlspecialchars($details['latitude'] ?? '') ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Latitude">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label for="longitude">Project Longitude <span
																				class="text-danger">*</span></label>
																		<input type="text" id="longitude"
																			name="longitude"
																			value="<?= htmlspecialchars($details['longitude'] ?? '') ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Longitude">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Slug</label>
																		<input type="text" name="proj_slug"
																			value="<?= $details['proj_slug'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Slug">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Map</label>
																		<input type="text" name="proj_map"
																			value="<?= $details['proj_map'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Google Maps Embed URL">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Posted On <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_posted_on"
																			value="<?= date('d-m-Y', strtotime($details['proj_posted_on'])) ?>"
																			class="form-control" autocomplete="off"
																			id="kt_datepicker_1"
																			placeholder="Posted On">
																	</div>
																</div>

															</div>
														</div>
													</div>
												</div>
												<!-- ===================== END TAB 1 ===================== -->


												<!-- ===================== TAB 2: PRICING ===================== -->
												<div class="tab-pane fade" id="tab-pricing" role="tabpanel">
													<div class="card card-custom">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">Pricing & Inventory</h3>
															</div>
														</div>
														<div class="card-body">
															<div class="row">

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Price <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_price"
																			value="<?= $details['proj_price'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Price">
																		<?php
																		$saved_price_types = isset($details['proj_price_type']) ? explode(',', $details['proj_price_type']) : [];
																		?>
																		<div class="mt-2">
																			<label>
																				<input type="checkbox"
																					name="proj_price_type[]"
																					value="year" <?= in_array('year', $saved_price_types) ? 'checked' : '' ?>> Yearly
																			</label>
																			<label class="ml-3">
																				<input type="checkbox"
																					name="proj_price_type[]"
																					value="month" <?= in_array('month', $saved_price_types) ? 'checked' : '' ?>> Monthly
																			</label>
																		</div>
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Start Price <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_start_price"
																			value="<?= $details['proj_start_price'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Start Price">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Payment Plan <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_pplan_cont"
																			value="<?= $details['proj_pplan_cont'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Payment Plan">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Days <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_days"
																			value="<?= $details['proj_days'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Days">
																	</div>
																</div>

																<div class="col-md-12">
																	<div class="form-group">
																		<label>Inventory Type <span
																				class="text-danger">*</span></label>

																		<input type="hidden" name="proj_inventory_type"
																			id="proj_inventory_type_hidden"
																			value="<?= htmlspecialchars($details['proj_inventory_type']) ?>">

																		<?php
																		$saved_inventory = !empty($details['proj_inventory_type'])
																			? explode(',', $details['proj_inventory_type'])
																			: [];
																		?>

																		<div class="dropdown w-100"
																			id="inventoryDropdown">
																			<button
																				class="btn btn-light border w-100 text-left dropdown-toggle"
																				type="button" id="inventoryDropdownBtn"
																				data-toggle="dropdown"
																				data-bs-toggle="dropdown"
																				aria-haspopup="true"
																				aria-expanded="false">
																				Select Inventory Type
																			</button>
																			<div class="dropdown-menu w-100 p-2"
																				aria-labelledby="inventoryDropdownBtn"
																				id="inventoryCheckboxList"
																				style="max-height: 250px; overflow-y: auto;">
																				<?php
																				$inv_query = mysqli_query($conn, "SELECT * FROM tbl_inventry_type ORDER BY inventry_name");
																				while ($inv_row = mysqli_fetch_assoc($inv_query)) {
																					$is_checked = in_array($inv_row['id'], $saved_inventory) ? 'checked' : '';
																					?>
																					<div class="dropdown-item">
																						<div class="form-check">
																							<input
																								class="form-check-input inventory-checkbox"
																								type="checkbox"
																								value="<?= $inv_row['id'] ?>"
																								id="inv_<?= $inv_row['id'] ?>"
																								<?= $is_checked ?>>
																							<label
																								class="form-check-label w-100"
																								for="inv_<?= $inv_row['id'] ?>">
																								<?= htmlspecialchars($inv_row['inventry_name']) ?>
																							</label>
																						</div>
																					</div>
																				<?php } ?>
																			</div>
																		</div>

																		<div id="selectedInventoryTags"
																			class="mt-2 d-flex flex-wrap gap-1"></div>
																	</div>
																</div>

															</div>
														</div>
													</div>
												</div>
												<!-- ===================== END TAB 2 ===================== -->


												<!-- ===================== TAB 3: MEDIA & FILES ===================== -->
												<div class="tab-pane fade" id="tab-media" role="tabpanel">

													<!-- Images -->
													<div class="card card-custom mb-4">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">Images</h3>
															</div>
														</div>
														<div class="card-body">
															<div class="row">

																<div class="col-md-12">
																	<div class="form-group">
																		<label>Project Thumb Image <span
																				class="text-danger">*</span></label>
																		<input type="file" name="prj_thumb_img[]"
																			class="form-control" multiple>
																		<div class="proj_thumb">
																			<?php
																			$prj_thumb_img_arr = explode(',', $details['prj_thumb_img']);
																			foreach ($prj_thumb_img_arr as $img) {
																				if ($img != '') { ?>
																					<div>
																						<span class="del_thumb_img"
																							del-name="<?= htmlspecialchars($img) ?>"
																							del-id="<?= $details['prj_id'] ?>">X</span>
																						<img src="<?= BASE_URL ?>assets/media/project/thumb/<?= $img ?>"
																							class='proj_thumb_img'>
																					</div>
																				<?php }
																			} ?>
																		</div>
																		<input type="hidden" name="prj_thumb_img_hidden"
																			value="<?= $details['prj_thumb_img'] ?>">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Inner Image <span
																				class="text-danger">*</span></label>
																		<input type="file" name="proj_inner_img"
																			class="form-control">
																		<?php if (!empty($details['proj_inner_img'])) { ?>
																			<img src="<?= BASE_URL ?>assets/media/project/inner-image/<?= $details['proj_inner_img'] ?>"
																				width='50' class="mt-3">
																		<?php } ?>
																		<input type="hidden"
																			name="proj_inner_img_hidden"
																			value="<?= $details['proj_inner_img'] ?>">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Dev Image <span
																				class="text-danger">*</span></label>
																		<input type="file" name="proj_dev_img"
																			class="form-control">
																		<?php if (!empty($details['proj_dev_img'])) { ?>
																			<img src="<?= BASE_URL ?>assets/media/project/dev/<?= $details['proj_dev_img'] ?>"
																				width='50' class="mt-3">
																		<?php } ?>
																		<input type="hidden" name="proj_dev_img_hidden"
																			value="<?= $details['proj_dev_img'] ?>">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Dev Image Alt <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_dev_img_alt"
																			value="<?= $details['proj_dev_img_alt'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Dev Image Alt">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Location Image <span
																				class="text-danger">*</span></label>
																		<input type="file" id="proj_loc_img"
																			name="proj_loc_img" class="form-control">
																		<?php if (!empty($details['proj_loc_img'])) { ?>
																			<img src="<?= BASE_URL ?>assets/media/project/location/<?= $details['proj_loc_img'] ?>"
																				width='50' class="mt-3">
																		<?php } ?>
																		<input type="hidden" name="proj_loc_img_hidden"
																			value="<?= $details['proj_loc_img'] ?>">
																		<br>
																		<?php if ($details['proj_loc_img'] != '') { ?>
																			<a href='javascript:void(0)'
																				class='btn btn-danger mt-2 img_remove'
																				img-id="<?= $id ?>" img-type='location'
																				img-name="<?= $details['proj_loc_img'] ?>">Remove
																				Image</a>
																		<?php } ?>
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Masterplan Image <span
																				class="text-danger">*</span></label>
																		<input type="file" id="proj_mastrpln_img"
																			name="proj_mastrpln_img"
																			class="form-control">
																		<?php if (!empty($details['proj_mastrpln_img'])) { ?>
																			<img src="<?= BASE_URL ?>assets/media/project/masterplan/<?= $details['proj_mastrpln_img'] ?>"
																				width='50' class="mt-3">
																		<?php } ?>
																		<input type="hidden"
																			name="proj_mastrpln_img_hidden"
																			value="<?= $details['proj_mastrpln_img'] ?>">
																		<?php if ($details['proj_mastrpln_img'] != '') { ?>
																			<a href='javascript:void(0)'
																				class='btn btn-danger mt-2 img_remove'
																				img-id="<?= $id ?>" img-type='masterplan'
																				img-name="<?= $details['proj_mastrpln_img'] ?>">Remove
																				Image</a>
																		<?php } ?>
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>QR Image <span
																				class="text-danger">*</span></label>
																		<input type="file" name="proj_qr_img"
																			class="form-control">
																		<?php if (!empty($details['proj_qr_img'])): ?>
																			<img src="<?= BASE_URL ?>assets/media/project/qr/<?= $details['proj_qr_img'] ?>"
																				width="50" class="mt-3">
																		<?php endif; ?>
																		<input type="hidden" name="proj_qr_img_hidden"
																			value="<?= htmlspecialchars($details['proj_qr_img']) ?>">
																		<?php if ($details['proj_qr_img']): ?>
																			<a href="javascript:void(0)"
																				class="btn btn-danger mt-2 img_remove"
																				img-id="<?= $id ?>" img-type="qr"
																				img-name="<?= $details['proj_qr_img'] ?>">Remove
																				QR Image</a>
																		<?php endif; ?>
																	</div>
																</div>

															</div>
														</div>
													</div>

													<!-- PDF Documents -->
													<div class="card card-custom">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">PDF Documents</h3>
															</div>
														</div>
														<div class="card-body">
															<div class="row">

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Brochure <span
																				class="text-danger">*</span></label>
																		<input type="file" name="proj_brochure"
																			class="form-control">
																		<?php if (!empty($details['proj_brochure'])) { ?>
																			<a href="<?= BASE_URL ?>assets/media/project/pdf/<?= $details['proj_brochure'] ?>"
																				target="_blank" class="pdf_margin">PDF</a>
																		<?php } ?>
																		<input type="hidden" name="proj_brochure_hidden"
																			value="<?= $details['proj_brochure'] ?>">
																		<?php if ($details['proj_brochure'] != '') { ?>
																			<a href='javascript:void(0)'
																				class='btn btn-danger mt-2 pdf_remove'
																				pdf-id="<?= $id ?>" pdf-type='brochure'
																				pdf-name="<?= $details['proj_brochure'] ?>">Remove
																				PDF</a>
																		<?php } ?>
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project PayPlan <span
																				class="text-danger">*</span></label>
																		<input type="file" name="proj_payplan"
																			class="form-control">
																		<?php if (!empty($details['proj_payplan'])) { ?>
																			<a href="<?= BASE_URL ?>assets/media/project/pdf/<?= $details['proj_payplan'] ?>"
																				target="_blank" class="pdf_margin">PDF</a>
																		<?php } ?>
																		<input type="hidden" name="proj_payplan_hidden"
																			value="<?= $details['proj_payplan'] ?>">
																		<?php if ($details['proj_payplan'] != '') { ?>
																			<a href='javascript:void(0)'
																				class='btn btn-danger mt-2 pdf_remove'
																				pdf-id="<?= $id ?>" pdf-type='payplan'
																				pdf-name="<?= $details['proj_payplan'] ?>">Remove
																				PDF</a>
																		<?php } ?>
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Floor Plan <span
																				class="text-danger">*</span></label>
																		<input type="file" name="proj_floorplan"
																			class="form-control">
																		<?php if (!empty($details['proj_floorplan'])) { ?>
																			<a href="<?= BASE_URL ?>assets/media/project/pdf/<?= $details['proj_floorplan'] ?>"
																				target="_blank" class="pdf_margin">PDF</a>
																		<?php } ?>
																		<input type="hidden"
																			name="proj_floorplan_hidden"
																			value="<?= $details['proj_floorplan'] ?>">
																		<?php if ($details['proj_floorplan'] != '') { ?>
																			<a href='javascript:void(0)'
																				class='btn btn-danger mt-2 pdf_remove'
																				pdf-id="<?= $id ?>" pdf-type='floorplan'
																				pdf-name="<?= $details['proj_floorplan'] ?>">Remove
																				PDF</a>
																		<?php } ?>
																	</div>
																</div>

															</div>
														</div>
													</div>

													
												</div>
												<!-- ===================== END TAB 3 ===================== -->


												<!-- ===================== TAB 4: PROPERTY DETAILS ===================== -->
												<div class="tab-pane fade" id="tab-details" role="tabpanel">

													<div class="card card-custom mb-4">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">Property Specifications</h3>
															</div>
														</div>
														<div class="card-body">
															<div class="row">

																<div class="col-md-4">
																	<div class="form-group">
																		<?php
																		$proj_bedroom_selected = isset($details['proj_bedroom']) ? explode(',', $details['proj_bedroom']) : [];
																		$proj_bedroom_selected = array_filter(array_map('trim', $proj_bedroom_selected));
																		$options = array_unique($proj_bedroom_selected);
																		sort($options);
																		?>
																		<label for="proj_bedroom">Project Bedroom <span
																				class="text-danger">*</span></label>
																		<select class="form-control" id="proj_bedroom"
																			name="proj_bedroom[]" multiple="multiple">
																			<?php foreach ($options as $option): ?>
																				<?php
																				$option_str = (string) $option;
																				$selected = in_array($option_str, $proj_bedroom_selected, true) ? 'selected' : '';
																				?>
																				<option
																					value="<?= htmlspecialchars($option_str) ?>"
																					<?= $selected ?>>
																					<?= htmlspecialchars($option_str) ?>
																				</option>
																			<?php endforeach; ?>
																		</select>
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Shower <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_shower"
																			value="<?= $details['proj_shower'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Shower">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Area <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_area"
																			value="<?= $details['proj_area'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Area">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Size <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_size"
																			value="<?= $details['proj_size'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Size">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Video <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_video"
																			value="<?= $details['proj_video'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="YouTube / Vimeo URL">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project QR Link <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_qr_link"
																			value="<?= $details['proj_qr_link'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project QR Link">
																	</div>
																</div>

															</div>
														</div>
													</div>

													<!-- Features & Facilities -->
													<div class="card card-custom">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">Features &amp; Facilities</h3>
															</div>
														</div>
														<div class="card-body">

															<div class="form-group">
																<label>Project Feature Facility Content <span
																		class="text-danger">*</span></label>
																<textarea name="proj_fea_fac_cont" class="form-control"
																	placeholder="Project Feature Facility Content"
																	id="kt-tinymce-5" aria-hidden="true"
																	rows="10"><?= $details['proj_fea_fac_cont'] ?></textarea>
															</div>

															<div class="form-group">
																<label>Featured and Facility Icons</label>
																<hr>
																<ul class="feat_fac_icon list-unstyled row">
																	<?php
																	$featfac_name_arr = [];
																	if (!empty($details['proj_fea_fac_opt']) && $details['proj_fea_fac_opt'] !== 'NA') {
																		$featfac_name_arr = explode(',', $details['proj_fea_fac_opt']);
																	}
																	$query = mysqli_query($conn, "SELECT * FROM tbl_proj_featfac WHERE is_active = 1 ORDER BY featfac_name");
																	while ($row = mysqli_fetch_array($query)) {
																		$featfac_id = $row['featfac_id'];
																		$featfac_name = $row['featfac_name'];
																		$is_checked = in_array($featfac_id, $featfac_name_arr) ? 'checked' : '';
																		?>
																		<li class="col-md-3 mb-2">
																			<label>
																				<input type="checkbox"
																					name="proj_fea_fac_opt[]"
																					value="<?= $featfac_id ?>"
																					<?= $is_checked ?>>
																				<span
																					class="ml-1"><?= htmlspecialchars($featfac_name) ?></span>
																			</label>
																		</li>
																	<?php } ?>
																</ul>
															</div>

														</div>
													</div>

												</div>
												<!-- ===================== END TAB 4 ===================== -->


												<!-- ===================== TAB 5: CONTENT ===================== -->
												<div class="tab-pane fade" id="tab-content" role="tabpanel">
													<div class="card card-custom">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">Content Sections</h3>
															</div>
														</div>
														<div class="card-body">

															<div class="form-group">
																<label>Project Overview <span
																		class="text-danger">*</span></label>
																<textarea name="proj_overview" class="form-control"
																	placeholder="Project Overview" aria-hidden="true"
																	rows="10"
																	id="kt-tinymce-2"><?= $details['proj_overview'] ?></textarea>
															</div>

															<div class="form-group">
																<label>Project Location Content <span
																		class="text-danger">*</span></label>
																<textarea name="proj_loc_cont" class="form-control"
																	placeholder="Project Location Content"
																	id="kt-tinymce-6" aria-hidden="true"
																	rows="10"><?= $details['proj_loc_cont'] ?></textarea>
															</div>

															<div class="form-group">
																<label>Project MasterPlan <span
																		class="text-danger">*</span></label>
																<textarea name="proj_masterplan" class="form-control"
																	placeholder="Project MasterPlan" id="kt-tinymce-7"
																	aria-hidden="true"
																	rows="10"><?= $details['proj_masterplan'] ?></textarea>
															</div>

															<div class="form-group">
																<label>Project QR <span
																		class="text-danger">*</span></label>
																<textarea name="proj_qr" class="form-control"
																	placeholder="Project QR" id="kt-tinymce-4"
																	aria-hidden="true"
																	rows="10"><?= $details['proj_qr'] ?></textarea>
															</div>

														</div>
													</div>
												</div>
												<!-- ===================== END TAB 5 ===================== -->


												<!-- ===================== TAB 6: CONTACT ===================== -->
												<div class="tab-pane fade" id="tab-contact" role="tabpanel">
													<div class="card card-custom">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">Contact Information</h3>
															</div>
														</div>
														<div class="card-body">
															<div class="row">

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Email ID <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_email"
																			value="<?= $details['proj_email'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Email ID">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project WhatsApp No <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_whatsaapno"
																			value="<?= $details['proj_whatsaapno'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Whatsapp No">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Mobile No <span
																				class="text-danger">*</span></label>
																		<input type="text" name="proj_mobileno"
																			value="<?= $details['proj_mobileno'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Mobile No">
																	</div>
																</div>

															</div>
														</div>
													</div>
												</div>
												<!-- ===================== END TAB 6 ===================== -->


												<!-- ===================== TAB 7: SEO ===================== -->
												<div class="tab-pane fade" id="tab-seo" role="tabpanel">
													<div class="card card-custom">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">SEO Settings</h3>
															</div>
														</div>
														<div class="card-body">

															<div class="form-group">
																<label>Meta Title</label>
																<input type="text" name="meta_title"
																	value="<?= $details['meta_title'] ?>"
																	class="form-control" autocomplete="off"
																	placeholder="Meta Title">
															</div>

															<div class="form-group">
																<label>Meta Keyword</label>
																<input type="text" name="meta_keyword"
																	value="<?= $details['meta_keyword'] ?>"
																	class="form-control" autocomplete="off"
																	placeholder="Meta Keyword">
															</div>

															<div class="form-group">
																<label>Meta Description</label>
																<textarea name="meta_description" class="form-control"
																	placeholder="Meta Description" aria-hidden="true"
																	rows="6"><?= $details['meta_description'] ?></textarea>
															</div>

															<div class="form-group">
																<label>Meta Tags</label>
																<textarea name="meta_tags" class="form-control"
																	placeholder="Meta Tags" aria-hidden="true"
																	rows="6"><?= $details['meta_tags'] ?></textarea>
															</div>

														</div>
													</div>
												</div>
												<!-- ===================== END TAB 7 ===================== -->


												<!-- ===================== TAB 8: SETTINGS ===================== -->
												<div class="tab-pane fade" id="tab-settings" role="tabpanel">
													<div class="card card-custom">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">Visibility &amp; Settings</h3>
															</div>
														</div>
														<div class="card-body">

															<div class="form-group">
																<label>Project Featured Community <span
																		class="text-danger">*</span></label>
																<div class="mt-2">
																	<?php
																	$saved_comm_feat = !empty($details['proj_comm_feat']) ? explode(',', $details['proj_comm_feat']) : [];
																	$comm_feat_query = mysqli_query($conn, "SELECT * FROM tbl_community WHERE comm_featured=1 AND is_active=1");
																	?>
																	<ul class="list-unstyled row">
																		<?php while ($comm_feat_row = mysqli_fetch_array($comm_feat_query)) {
																			$comm_id = $comm_feat_row['comm_id'];
																			?>
																			<li class="col-md-3 mb-2">
																				<label>
																					<input type="checkbox"
																						name="proj_comm_feat[]"
																						value="<?= $comm_id ?>"
																						<?= in_array($comm_id, $saved_comm_feat) ? 'checked' : '' ?>>
																					<span
																						class="ml-1"><?= htmlspecialchars($comm_feat_row['comm_heading']) ?></span>
																				</label>
																			</li>
																		<?php } ?>
																	</ul>
																</div>
															</div>

															<div class="form-group">
																<label>Show On Homepage</label><br>
																<input type="checkbox" name="proj_showonhomepage"
																	value='1' <?= ($details['proj_showonhomepage'] == 1 ? 'checked' : '') ?>>
															</div>

															<div class="form-group">
																<label>Mark as Featured</label><br>
																<input type="checkbox" name="proj_markfeat" value='1'
																	<?= ($details['proj_markfeat'] == 1 ? 'checked' : '') ?>>
															</div>

															<div class="form-group mt-3">
																<label>App Feature</label>
																<hr>
																<div class="row">
																	<div class="col-md-3">
																		<label>Down Payment</label><br>
																		<input type="checkbox" name="proj_show_down_payment"
																			value='1' <?= ($details['proj_show_down_payment'] == 1 ? 'checked' : '') ?>>
																	</div>
																	<div class="col-md-3">
																		<label>Payment Plan</label><br>
																		<input type="checkbox" name="proj_show_payment_plan"
																			value='1' <?= ($details['proj_show_payment_plan'] == 1 ? 'checked' : '') ?>>
																	</div>
																	<div class="col-md-3">
																		<label>Handover</label><br>
																		<input type="checkbox" name="proj_show_handover"
																			value='1' <?= ($details['proj_show_handover'] == 1 ? 'checked' : '') ?>>
																	</div>
																	<div class="col-md-3">
																		<label>Area, sq ft</label><br>
																		<input type="checkbox" name="proj_show_area"
																			value='1' <?= ($details['proj_show_area'] == 1 ? 'checked' : '') ?>>
																	</div>
																	<div class="col-md-3 mt-3">
																		<label>Bedroom</label><br>
																		<input type="checkbox" name="proj_show_bedroom"
																			value='1' <?= ($details['proj_show_bedroom'] == 1 ? 'checked' : '') ?>>
																	</div>
																	<div class="col-md-3 mt-3">
																		<label>Shower</label><br>
																		<input type="checkbox" name="proj_show_shower"
																			value='1' <?= ($details['proj_show_shower'] == 1 ? 'checked' : '') ?>>
																	</div>
																	<div class="col-md-3 mt-3">
																		<label>Project Size</label><br>
																		<input type="checkbox" name="proj_show_size"
																			value='1' <?= ($details['proj_show_size'] == 1 ? 'checked' : '') ?>>
																	</div>
																	<div class="col-md-3 mt-3">
																		<label>Balcony</label><br>
																		<input type="checkbox" name="proj_show_balcony"
																			value='1' <?= ($details['proj_show_balcony'] == 1 ? 'checked' : '') ?>>
																	</div>
																</div>
															</div>

															

															<div class="form-group">
																<label>Status</label>
																<span class="switch switch-md switch-icon">
																	<label>
																		<input type="checkbox" name="is_active"
																			value="1" <?= ($details['is_active'] == 1 ? 'checked' : '') ?>>
																		<span></span>
																	</label>
																</span>
															</div>

														</div>
													</div>
												</div>

												<!-- Feature Icons -->
													<div class="card card-custom mt-4">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">Add Extra Field in App</h3>
															</div>
														</div>
														<div class="card-body">
															<div class="row">
																<?php for ($fi = 1; $fi <= 5; $fi++): ?>
																	<div class="col-md-4 mb-3">
																		<div class="form-group">
																			<label>Feature Icon <?= $fi ?> Tag</label>
																			<input type="text"
																				name="proj_feature_icon_<?= $fi ?>"
																				value="<?= htmlspecialchars($details["proj_feature_icon_$fi"] ?? '') ?>"
																				class="form-control" autocomplete="off"
																				placeholder='e.g. &lt;i class="fas fa-swimming-pool"&gt;&lt;/i&gt;'>
																			<?php if (!empty($details["proj_feature_icon_$fi"])): ?>
																				<div class="mt-2" style="font-size: 24px;">
																					<?= $details["proj_feature_icon_$fi"] ?>
																				</div>
																			<?php endif; ?>

																			<label class="mt-3">Feature <?= $fi ?> Details</label>
																			<input type="text"
																				name="proj_feature_text_<?= $fi ?>"
																				value="<?= htmlspecialchars($details["proj_feature_text_$fi"] ?? '') ?>"
																				class="form-control" autocomplete="off"
																				placeholder="e.g. 5 Min to Metro">
																		</div>
																	</div>
																<?php endfor; ?>
															</div>
														</div>
													</div>

												<!-- ===================== END TAB 8 ===================== -->

											</div><!-- end tab-content -->

											<div class="card-footer mt-3">
												<button type="submit" name="update_project"
													class="btn btn-primary mr-2">Update</button>
												<button type="reset" class="btn btn-secondary">Reset</button>
											</div>

										</form>
										<!--end::Form-->
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
				<div class="modal fade" id="inventoryModal">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title">Add Inventory Type</h4>
							</div>
							<div class="modal-body">
								<input type="text" id="inventry_name" class="form-control"
									placeholder="Enter Inventory Type">
								<br>
								<button type="button" id="saveInventory" class="btn btn-success">
									Save
								</button>
							</div>
						</div>
					</div>
				</div>
				<?php
				include('includes/footer-inner.php');
				?>


				<script>
					// On page load - pre-render badges for already saved values
					$(document).ready(function () {
						updateInventorySelection();
					});

					// Prevent dropdown from closing on checkbox click
					$(document).on('click', '#inventoryCheckboxList', function (e) {
						e.stopPropagation();
					});

					// Update on checkbox change
					$(document).on('change', '.inventory-checkbox', function () {
						updateInventorySelection();
					});

					function updateInventorySelection() {
						var selected = [];
						var labels = [];

						$('.inventory-checkbox:checked').each(function () {
							selected.push($(this).val());
							labels.push($(this).next('label').text().trim());
						});

						// Update hidden input
						$('#proj_inventory_type_hidden').val(selected.join(','));

						// Update button text
						if (labels.length > 0) {
							$('#inventoryDropdownBtn').text(labels.length + ' item(s) selected');
						} else {
							$('#inventoryDropdownBtn').text('Select Inventory Type');
						}

						// Update badges
						var tagsHtml = '';
						labels.forEach(function (label, i) {
							tagsHtml += '<span class="badge badge-primary mr-1 mb-1" style="font-size:12px;">' +
								label +
								' <span class="remove-tag" data-id="' + selected[i] + '" style="cursor:pointer;">&times;</span>' +
								'</span>';
						});
						$('#selectedInventoryTags').html(tagsHtml);
					}

					// Remove badge on × click
					$(document).on('click', '.remove-tag', function () {
						var id = $(this).data('id');
						$('#inv_' + id).prop('checked', false);
						updateInventorySelection();
					});

					// AJAX - Save new inventory type
					$('#saveInventory').click(function () {
						var inventry_name = $('#inventry_name').val().trim();

						if (inventry_name == '') {
							alert('Please enter inventory type');
							return;
						}

						$.ajax({
							url: base_url + 'ajax/add-inventory-type.php',
							type: 'POST',
							data: { inventry_name: inventry_name },
							dataType: 'json',
							success: function (response) {
								if (response.status == 'success') {
									$('#inventoryCheckboxList').append(
										'<div class="dropdown-item">' +
										'<div class="form-check">' +
										'<input class="form-check-input inventory-checkbox" type="checkbox"' +
										' value="' + response.id + '" id="inv_' + response.id + '" checked>' +
										'<label class="form-check-label w-100" for="inv_' + response.id + '">' +
										response.name +
										'</label>' +
										'</div>' +
										'</div>'
									);

									updateInventorySelection();
									$('#inventoryModal').modal('hide');
									$('#inventry_name').val('');
									alert('Inventory Type Added Successfully');
								} else {
									alert(response.message);
								}
							}
						});
					});
				</script>