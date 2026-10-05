<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include('db/config.php');
include('includes/header.php');
include('lock2.php');


$thumb_img_nam = '';
if (isset($_POST['add_project'])) {

	$prj_type_id = mysqli_real_escape_string($conn, $_POST['prj_type_id']);
	$prj_dev_id = !empty($_POST['prj_dev_id'])
		? intval($_POST['prj_dev_id'])
		: 'null';
	// $prj_comm_id = (prepStr($_POST['prj_comm_id'] != '' ? $_POST['prj_comm_id'] : null));
	$proj_name = mysqli_real_escape_string($conn, $_POST['proj_name']);
	$proj_name_on_card = mysqli_real_escape_string($conn, $_POST['proj_name_on_card']);
	$proj_sub_heading = mysqli_real_escape_string($conn, $_POST['proj_sub_heading']);

	$proj_price = isset($_POST['proj_price']) && $_POST['proj_price'] !== '' ? mysqli_real_escape_string($conn, $_POST['proj_price']) : 0;
	$proj_price_type_arr = isset($_POST['proj_price_type']) ? $_POST['proj_price_type'] : [];
	$proj_price_type_cleaned = [];
	foreach ($proj_price_type_arr as $type) {
		$proj_price_type_cleaned[] = mysqli_real_escape_string($conn, $type);
	}
	$proj_price_type = implode(',', $proj_price_type_cleaned);

	$proj_pplan_cont = mysqli_real_escape_string($conn, $_POST['proj_pplan_cont']);
	$proj_days = mysqli_real_escape_string($conn, $_POST['proj_days']);
	$proj_location = mysqli_real_escape_string($conn, $_POST['proj_location']);
	$proj_bedroom_array = isset($_POST['proj_bedroom'])
		? $_POST['proj_bedroom']
		: [];

	$proj_bedroom_cleaned = [];

	foreach ($proj_bedroom_array as $bedroom) {
		$proj_bedroom_cleaned[] = mysqli_real_escape_string($conn, $bedroom);
	}

	// Convert to comma-separated string
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
	// $proj_showonhomepage = (prepStr($_POST['proj_showonhomepage'] != '' ? $_POST['proj_showonhomepage'] : 2));
	$proj_showonhomepage = isset($_POST['proj_showonhomepage']) ? $_POST['proj_showonhomepage'] : 2;
	// $proj_markfeat = (prepStr($_POST['proj_markfeat'] != '' ? $_POST['proj_markfeat'] : 2));
	$proj_markfeat = isset($_POST['proj_markfeat']) ? $_POST['proj_markfeat'] : 2;
	$proj_posted_on = date('Y-m-d', strtotime($_POST['proj_posted_on']));
	// $is_active = (prepStr($_POST['is_active'] != '' ? 1 : 2));
	$is_active = isset($_POST['is_active']) ? 1 : 2;
	$prj_thumb_img = $_FILES['prj_thumb_img']['name'];
	$gallery_image_alt = $_POST['gallery_image_alt'];
	$gallery_image = $_FILES['gallery_image']['name'];
	$feat_fac_img = $_FILES['feat_fac_img']['name'] ?? '';
	$feat_fac_heading = $_POST['feat_fac_heading'] ?? '';
	$proj_fea_fac_opt = !empty($_POST['proj_fea_fac_opt']) ? implode(',', $_POST['proj_fea_fac_opt']) : '';
	$payplan_heading = $_POST['payplan_heading'];
	$payplan_percentage = $_POST['payplan_percentage'];
	$payplan_status = $_POST['payplan_status'];
	$payplan_status = $_POST['payplan_status'];
	$faq_heading = $_POST['faq_heading'];
	$faq_descp = $_POST['faq_descp'];
	$proj_shower = $_POST['proj_shower'];
	$proj_area = $_POST['proj_area'];
	$proj_comm_feat = isset($_POST['proj_comm_feat']) ? implode(',', $_POST['proj_comm_feat']) : '';







	$count_thumb_img = count($prj_thumb_img);


	/*multiple thumb image starts*/


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

	$thumb_img_names = trim($thumb_img_nam, ',');


	/*multiple thumb image ends*/

	$proj_inner_img = (!empty($_FILES['proj_inner_img']['name']) ? 'proj_inner_img_' . rand(0, 10000) . '_' . $_FILES['proj_inner_img']['name'] : '');

	$proj_dev_img = (!empty($_FILES['proj_dev_img']['name']) ? 'proj_dev_img_' . rand(0, 10000) . '_' . $_FILES['proj_dev_img']['name'] : '');

	$proj_brochure = (!empty($_FILES['proj_brochure']['name']) ? 'proj_brochure_' . rand(0, 10000) . '_' . $_FILES['proj_brochure']['name'] : '');

	$proj_payplan = (!empty($_FILES['proj_payplan']['name']) ? 'proj_payplan_' . rand(0, 10000) . '_' . $_FILES['proj_payplan']['name'] : '');

	$proj_floorplan = (!empty($_FILES['proj_floorplan']['name']) ? 'proj_floorplan_' . rand(0, 10000) . '_' . $_FILES['proj_floorplan']['name'] : '');

	$proj_loc_img = (!empty($_FILES['proj_loc_img']['name']) ? 'proj_loc_img_' . rand(0, 10000) . '_' . $_FILES['proj_loc_img']['name'] : '');

	$proj_mastrpln_img = (!empty($_FILES['proj_mastrpln_img']['name']) ? 'proj_mastrpln_img_' . rand(0, 10000) . '_' . $_FILES['proj_mastrpln_img']['name'] : '');

	$proj_qr_img = (!empty($_FILES['proj_qr_img']['name']) ? 'proj_qr_img_' . rand(0, 10000) . '_' . $_FILES['proj_qr_img']['name'] : '');

	$query = mysqli_query($conn, "insert into tbl_proj
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
						               proj_shower='$proj_shower',
						               proj_area='$proj_area',
						               proj_bedroom='$proj_bedroom',
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
						               is_active='$is_active'
	                                   ");



	$proj_id = mysqli_insert_id($conn);


	/*insert into tbl_proj_gallery start*/

	$count_galleryimg = count($gallery_image);

	for ($i = 0; $i < $count_galleryimg; $i++) {

		$gallery_imgs_alt = mysqli_real_escape_string($conn, $gallery_image_alt[$i]);

		$gallery_name_arr = $_FILES['gallery_image']['name'];
		$gallery_loc_arr = $_FILES['gallery_image']['tmp_name'];


		$gallery_image_name = 'gallery_image_' . rand(0, 10000) . '_' . $gallery_name_arr[$i];
		$gallery_image_loc = $gallery_loc_arr[$i];


		if ($gallery_name_arr[$i] != '') {
			move_uploaded_file($gallery_image_loc, "assets/media/project/gallery/$gallery_image_name");
			$query = mysqli_query($conn, "insert into tbl_proj_gallery
                                                        SET proj_id='$proj_id',
                                                        gallery_img='$gallery_image_name',
                                                        gallery_img_alt='$gallery_imgs_alt',
                                                        is_active='1',
                                                        tstp='$date'
                                                        ");
		}

	}

	/*insert into tbl_proj_gallery end*/


	/*insert into tbl_proj_payplan start*/

	$count_payplan_heading = count($payplan_heading);

	for ($i = 0; $i < $count_payplan_heading; $i++) {

		$payplan_headings = mysqli_real_escape_string($conn, $payplan_heading[$i]);
		$payplan_percentages = mysqli_real_escape_string($conn, $payplan_percentage[$i]);
		$payplan_statuss = mysqli_real_escape_string($conn, $payplan_status[$i]);


		if (!empty($payplan_headings)):

			$query = mysqli_query($conn, "insert into tbl_proj_payplan
                                                        SET proj_id='$proj_id',
                                                        payplan_heading='$payplan_headings',
                                                        payplan_percentage='$payplan_percentages',
                                                        payplan_status='$payplan_statuss',
                                                        is_active='1',
                                                        tstp='$date'
                                                        ");

		endif;

	}

	/*insert into tbl_proj_payplan end*/


	/*insert into tbl_proj_faq start*/

	$count_faq_heading = count($faq_heading);

	for ($i = 0; $i < $count_faq_heading; $i++) {

		$faq_headings = mysqli_real_escape_string($conn, $faq_heading[$i]);
		$faq_descps = mysqli_real_escape_string($conn, $faq_descp[$i]);


		if (!empty($faq_headings)):

			$query = mysqli_query($conn, "insert into tbl_proj_faq
                                                        SET proj_id='$proj_id',
                                                        faq_heading='$faq_headings',
                                                        faq_descp='$faq_descps',
                                                        is_active='1',
                                                        tstp='$date'
                                                        ");


		endif;

	}

	/*insert into tbl_proj_faq end*/


	if ($query) {
		move_uploaded_file($_FILES['proj_inner_img']['tmp_name'], "assets/media/project/inner-image/$proj_inner_img");
		move_uploaded_file($_FILES['proj_dev_img']['tmp_name'], "assets/media/project/dev/$proj_dev_img");
		move_uploaded_file($_FILES['proj_brochure']['tmp_name'], "assets/media/project/pdf/$proj_brochure");
		move_uploaded_file($_FILES['proj_payplan']['tmp_name'], "assets/media/project/pdf/$proj_payplan");
		move_uploaded_file($_FILES['proj_floorplan']['tmp_name'], "assets/media/project/pdf/$proj_floorplan");
		move_uploaded_file($_FILES['proj_loc_img']['tmp_name'], "assets/media/project/location/$proj_loc_img");
		move_uploaded_file($_FILES['proj_mastrpln_img']['tmp_name'], "assets/media/project/masterplan/$proj_mastrpln_img");
		move_uploaded_file($_FILES['proj_qr_img']['tmp_name'], "assets/media/project/qr/$proj_qr_img");
		header('Location:manage-buy-project.php');
	}




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
			<?php
			include('includes/sidebar.php');
			?>
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
										<h3 class="card-label">Add Buy Project</h3>
									</div>
									<div class="card-toolbar">
										<!--begin::Button-->
										<a href="<?= BASE_URL ?>manage-buy-project.php"
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


										<!--begin::Form-->
										<form action="" enctype="multipart/form-data" method="post" id="add-project">
											<div class="card-body">

												<div class="row">
													<div class="col-md-4">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Type
																<span class="text-danger">*</span></label>
															<select name="prj_type_id" class="form-control">
																<option value="">Select Project Type</option>
																<?php
																$query = mysqli_query($conn, "SELECT * 
																	FROM tbl_proj_type 
																	WHERE is_active = '1' 
																	AND prj_type_id NOT IN (1, 2, 3, 4, 8)
																	ORDER BY proj_type_name;
																	");
																while ($row = mysqli_fetch_array($query)) {
																	?>
																	<option value="<?= $row['prj_type_id'] ?>" selected>
																		<?= $row['proj_type_name'] ?>
																	</option>
																<?php } ?>
															</select>
														</div>
													</div>

													<div class="col-md-4">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Name
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_name" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Name">
														</div>
													</div>

													<div class="col-md-4">
														<div class="form-group">
															<label>Project Name (On Card) <span
																	class="text-danger">*</span></label>
															<input type="text" name="proj_name_on_card"
																class="form-control" autocomplete="off"
																placeholder="Project Name on Card">
														</div>
													</div>

													<div class="col-md-4">

														<div class="form-group">
															<label for="exampleInputPassword1">Project Sub Heading
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_sub_heading" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Sub Heading">
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Thumb Image
																<span class="text-danger">*</span></label>
															<input type="file" name="prj_thumb_img[]" value=""
																class="form-control" multiple>
														</div>
													</div>

													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Inner Image
																<span class="text-danger">*</span></label>
															<input type="file" name="proj_inner_img" value=""
																class="form-control">
														</div>
													</div>

													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Dev Image
																<span class="text-danger">*</span></label>
															<input type="file" name="proj_dev_img" value=""
																class="form-control">
														</div>
													</div>

													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Dev Image Alt
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_dev_img_alt" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Dev Image Alt">
														</div>
													</div>


													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Price <span
																	class="text-danger">*</span></label>
															<input type="text" name="proj_price" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Price">
															<div class="mt-2">
																<label><input type="checkbox" name="proj_price_type[]"
																		value="year"> Yearly</label>
																<label class="ml-3"><input type="checkbox"
																		name="proj_price_type[]" value="month">
																	Monthly</label>
															</div>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Start Price
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_start_price" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Start Price">
														</div>
													</div>

													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Payment Plan
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_pplan_cont" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Payment Plan">
														</div>
													</div>

													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Days
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_days" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Days">
														</div>
													</div>

													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Location
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_location" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Location">
														</div>
													</div>

													<?php
													$options = ['1', '2', '3', '4', '5', '6', '7', '8', '9', 'STD'];
													?>

													<div class="col-md-6">
														<div class="form-group">
															<label>Project Bedroom (Project Detail Page)</label>

															<select class="form-control" name="proj_bedroom[]"
																id="proj_bedroom" multiple>
																<?php foreach ($options as $opt): ?>
																	<option value="<?= $opt ?>"><?= $opt ?></option>
																<?php endforeach; ?>
															</select>

														</div>
													</div>

													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Shower
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_shower" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Shower">
														</div>
													</div>

													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Area
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_area" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Area">
														</div>
													</div>

													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Email ID
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_email" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Email ID">
														</div>
													</div>

													<div class="col-md-3">

														<div class="form-group">
															<label for="exampleInputPassword1">Project Whatsapp No
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_whatsaapno" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Whatsapp No">
														</div>
													</div>

													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Mobile No
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_mobileno" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Mobile No">
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Size
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_size" value=""
																class="form-control" autocomplete="off"
																placeholder="Project Size">
														</div>
													</div>

													<!-- <div class="col-md-6">
														<div class="form-group">
															<label for="exampleInputPassword1">Inventory Type
																<span class="text-danger">*</span></label>
															<input type="text" name="proj_inventory_type" value=""
																class="form-control" autocomplete="off"
																placeholder="Inventory Type">
														</div>
													</div> -->

													<div class="col-md-5">
														<label for="exampleInputPassword1">Inventory Type
															<span class="text-danger">*</span></label>

														<!-- Hidden input to store selected values -->
														<input type="hidden" name="proj_inventory_type"
															id="proj_inventory_type_hidden">

														<!-- Custom Dropdown -->
														<div class="dropdown w-100" id="inventoryDropdown">
															<button
																class="btn btn-light border w-100 text-left dropdown-toggle"
																type="button" id="inventoryDropdownBtn"
																data-toggle="dropdown" data-bs-toggle="dropdown"
																aria-haspopup="true" aria-expanded="false">
																Select Inventory Type
															</button>

															<div class="dropdown-menu w-100 p-2"
																aria-labelledby="inventoryDropdownBtn"
																id="inventoryCheckboxList"
																style="max-height: 250px; overflow-y: auto;">

																<?php
																$query = mysqli_query($conn, "SELECT * FROM tbl_inventry_type ORDER BY inventry_name");
																while ($row = mysqli_fetch_assoc($query)) { ?>
																	<div class="dropdown-item">
																		<div class="form-check">
																			<input
																				class="form-check-input inventory-checkbox"
																				type="checkbox" value="<?= $row['id'] ?>"
																				id="inv_<?= $row['id'] ?>">
																			<label class="form-check-label w-100"
																				for="inv_<?= $row['id'] ?>">
																				<?= $row['inventry_name'] ?>
																			</label>
																		</div>
																	</div>
																<?php } ?>

															</div>
														</div>

														<!-- Show selected items as badges -->
														<div id="selectedInventoryTags"
															class="mt-2 d-flex flex-wrap gap-1"></div>
													</div>

													<div class="col-md-1 d-none">
														<label for="exampleInputPassword1">
															<span class="text-danger">*</span></label>
														<button type="button" class="btn btn-primary"
															data-toggle="modal" data-target="#inventoryModal">
															Add
														</button>
													</div>

												</div>


												<div class="row">
													<div class="col-12">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Overview
																<span class="text-danger">*</span></label>
															<textarea name="proj_overview" class="form-control"
																placeholder="Project Overview" aria-hidden="true"
																rows="10" id="kt-tinymce-2"></textarea>
														</div>
													</div>

													<div class="col-12">
														<div class="form-group mt-3">
															<h3>Gallery Image</h3>
															<hr>


															<div class="row gallery_img">
																<div class="col-md-4">
																	<input type="file" name="gallery_image[]"
																		class="form-control">
																</div>

																<div class="col-md-4 form-group">
																	<input type="text" name="gallery_image_alt[]"
																		class="form-control"
																		placeholder="Gallery Image Alt">
																</div>


																<div class="col-md-4">
																	<a href=""
																		class="btn btn-primary add_more_gallery_image">Add
																		more
																		<i class="mdi  mdi-plus-circle-outline"></i></a>
																</div>

															</div>

														</div>
													</div>


													<div class="col-md-4">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Brochure
																<span class="text-danger">*</span></label>
															<input type="file" name="proj_brochure" value=""
																class="form-control">
														</div>
													</div>

													<div class="col-md-4">
														<div class="form-group">
															<label for="exampleInputPassword1">Project PayPlan
																<span class="text-danger">*</span></label>
															<input type="file" name="proj_payplan" value=""
																class="form-control">
														</div>
													</div>

													<div class="col-md-4">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Floor Plan
																<span class="text-danger">*</span></label>
															<input type="file" name="proj_floorplan" value=""
																class="form-control">
														</div>
													</div>

													<div class="col-12">
														<div class="form-group">
															<label for="exampleInputPassword1">Project Feature Facility
																Content
																<span class="text-danger">*</span></label>
															<textarea name="proj_fea_fac_cont" class="form-control"
																placeholder="Project Feature Facility Content"
																id="kt-tinymce-5" aria-hidden="true"
																rows="6"></textarea>
														</div>
													</div>

													<div class="col-12">
														<div class="form-group mt-3">
															<h3>Featured and Facility Icons</h3>
															<hr>
														</div>
													</div>

													<div class="col-md-12">
														<div class="form-group">
															<label class="control-label">
																Project Features & Amenities
															</label>

															<div class="row">

																<?php
																$query = mysqli_query($conn, "SELECT * FROM tbl_proj_featfac WHERE is_active = 1 ORDER BY featfac_name");

																while ($row = mysqli_fetch_assoc($query)) {
																	?>
																	<div class="col-md-2 col-sm-4 col-xs-6 mb-2">
																		<label class="feature-checkbox-item">
																			<input type="checkbox" name="proj_fea_fac_opt[]"
																				value="<?= $row['featfac_id']; ?>">

																			<span><?= htmlspecialchars($row['featfac_name']); ?></span>
																		</label>
																	</div>
																<?php } ?>

															</div>
														</div>
													</div>

												</div>
















												<div class="form-group">
													<h3>Payment Plan</h3>
													<hr>


													<div class="row payplan">
														<div class="col-md-3">
															<input type="text" name="payplan_heading[]"
																class="form-control" placeholder="Heading">
														</div>

														<div class="col-md-3">
															<input type="text" name="payplan_percentage[]"
																class="form-control" placeholder="Percentage">
														</div>

														<div class="col-md-3">
															<input type="text" name="payplan_status[]"
																class="form-control" placeholder="Status">
														</div>


														<div class="col-md-3">
															<a href="" class="btn btn-primary add_more_payplan">Add more
																<i class="mdi  mdi-plus-circle-outline"></i></a>
														</div>

													</div>

												</div>

												<div class="form-group">
													<h3>Faq</h3>
													<hr>


													<div class="row faq">
														<div class="col-md-4">
															<input type="text" name="faq_heading[]" class="form-control"
																placeholder="Heading">
														</div>

														<div class="col-md-4 form-group">
															<textarea name="faq_descp[]" placeholder="Description"
																class="form-control"></textarea>
														</div>


														<div class="col-md-4">
															<a href="" class="btn btn-primary add_more_faq">Add more <i
																	class="mdi  mdi-plus-circle-outline"></i></a>
														</div>

													</div>

												</div>


												<div class="form-group">
													<label for="exampleInputPassword1">Project Location Content
														<span class="text-danger">*</span></label>
													<textarea name="proj_loc_cont" class="form-control"
														placeholder="Project Location Content" id="kt-tinymce-6"
														aria-hidden="true" rows="10"></textarea>
												</div>

												<div class="form-group">
													<label for="exampleInputPassword1">Location Image
														<span class="text-danger">*</span></label>
													<input type="file" name="proj_loc_img" value=""
														class="form-control">
												</div>

												<div class="form-group">
													<label for="exampleInputPassword1">Project Video
														<span class="text-danger">*</span></label>
													<input type="text" name="proj_video" value="" class="form-control"
														autocomplete="off" placeholder="Project Video">
												</div>


												<div class="form-group">
													<label for="exampleInputPassword1">Project MasterPlan
														<span class="text-danger">*</span></label>
													<textarea name="proj_masterplan" class="form-control"
														placeholder="Project MasterPlan" id="kt-tinymce-7"
														aria-hidden="true" rows="10"></textarea>
												</div>

												<div class="form-group">
													<label for="exampleInputPassword1">Masterplan Image
														<span class="text-danger">*</span></label>
													<input type="file" name="proj_mastrpln_img" value=""
														class="form-control">
												</div>



												<div class="form-group">
													<label for="exampleInputPassword1">Project QR
														<span class="text-danger">*</span></label>
													<textarea name="proj_qr" class="form-control"
														placeholder="Project QR" id="kt-tinymce-4" aria-hidden="true"
														rows="10"></textarea>
												</div>



												<div class="form-group">
													<label for="exampleInputPassword1">Project QR Link
														<span class="text-danger">*</span></label>
													<input type="text" name="proj_qr_link" value="" class="form-control"
														autocomplete="off" placeholder="Project QR Link">
												</div>


												<div class="form-group">
													<label for="exampleInputPassword1">QR Image
														<span class="text-danger">*</span></label>
													<input type="file" name="proj_qr_img" value="" class="form-control">
												</div>


												<div class="form-group">
													<label for="exampleInputPassword1">Project Slug</label>
													<input type="text" name="proj_slug" value="" class="form-control"
														autocomplete="off" placeholder="Project Slug">
												</div>

												<div class="form-group">
													<label for="exampleInputPassword1">Project Map</label>
													<input type="text" name="proj_map" value="" class="form-control"
														autocomplete="off" placeholder="Project Map">
												</div>

												<div class="form-group">
													<label for="exampleInputPassword1">Meta Title</label>
													<input type="text" name="meta_title" value="" class="form-control"
														autocomplete="off" placeholder="Meta Title">
												</div>

												<div class="form-group">
													<label for="exampleInputPassword1">Meta Keyword</label>
													<input type="text" name="meta_keyword" value="" class="form-control"
														autocomplete="off" placeholder="Meta Keyword">
												</div>

												<div class="form-group">
													<label for="exampleInputPassword1">Meta Description</label>
													<textarea name="meta_description" class="form-control"
														placeholder="Meta Description" aria-hidden="true"
														rows="10"></textarea>
												</div>

												<div class="form-group">
													<label for="exampleInputPassword1">Meta Tags</label>
													<textarea name="meta_tags" class="form-control"
														placeholder="Meta Tags" aria-hidden="true" rows="10"></textarea>
												</div>




												<div class="form-group">
													<label for="exampleInputPassword1">Project Featured Community <span
															class="text-danger">*</span></label>

													<div class="mt-2">
														<?php
														$comm_feat_query = mysqli_query($conn, "SELECT * FROM tbl_community WHERE comm_featured=1 AND is_active=1");
														?>

														<ul style="list-style: none; padding-left: 0;">
															<?php while ($comm_feat_row = mysqli_fetch_array($comm_feat_query)) {
																$comm_id = $comm_feat_row['comm_id'];
																?>
																<li>
																	<label>
																		<input type="checkbox" name="proj_comm_feat[]"
																			value="<?= $comm_id ?>">
																		<?= htmlspecialchars($comm_feat_row['comm_heading']) ?>
																	</label>
																</li>
															<?php } ?>
														</ul>


													</div>

												</div>

												<div class="form-group">
													<label for="exampleInputPassword1">Show On Homepage</label><br>
													<input type="checkbox" name="proj_showonhomepage" value='1'>
												</div>



												<div class="form-group">
													<label for="exampleInputPassword1">Mark as Featured</label><br>
													<input type="checkbox" name="proj_markfeat" value='1'>
												</div>

												<div class="form-group">
													<label for="exampleInputPassword1">Posted On
														<span class="text-danger">*</span></label>
													<input type="text" name="proj_posted_on" value=""
														class="form-control" autocomplete="off" id="kt_datepicker_1"
														placeholder="Posted On">
												</div>

												<div class="form-group">
													<label for="exampleInputPassword1">Status</label>
													<span class="switch switch-md switch-icon">
														<label>
															<input type="checkbox" name="is_active" value="1">
															<span></span>
														</label>
													</span>
												</div>
											</div>
											<div class="card-footer">
												<button type="submit" name="add_project"
													class="btn btn-primary mr-2">Add</button>
												<button type="reset" class="btn btn-secondary">Reset</button>
											</div>
										</form><!--end::Form-->
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
					// Prevent dropdown from closing on checkbox click
					$(document).on('click', '#inventoryCheckboxList', function (e) {
						e.stopPropagation();
					});

					// Update hidden input + button label + badges on checkbox change
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

					// Remove tag on × click
					$(document).on('click', '.remove-tag', function () {
						var id = $(this).data('id');
						$('#inv_' + id).prop('checked', false);
						updateInventorySelection();
					});

					// AJAX - Add new inventory type
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

									// Add new checkbox option to dropdown
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

									updateInventorySelection(); // Refresh selection
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