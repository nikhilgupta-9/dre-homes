<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);

$id = base64_decode($_GET['edit']);

if (isset($_POST['update_project'])) {
	$prj_type_id = mysqli_real_escape_string($conn, $_POST['prj_type_id']);
	$prj_dev_id = (prepStr($_POST['prj_dev_id'] != '' ? $_POST['prj_dev_id'] : 0));
	$prj_comm_id = (prepStr($_POST['prj_comm_id'] != '' ? $_POST['prj_comm_id'] : 0));
	$proj_name = mysqli_real_escape_string($conn, $_POST['proj_name']);
	$proj_name_on_card = mysqli_real_escape_string($conn, $_POST['proj_name_on_card']);
	$proj_sub_heading = mysqli_real_escape_string($conn, $_POST['proj_sub_heading']);

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
	$proj_handover = mysqli_real_escape_string($conn, $_POST['proj_handover']);
	$proj_days = mysqli_real_escape_string($conn, $_POST['proj_days']);
	$proj_location = mysqli_real_escape_string($conn, $_POST['proj_location']);

	$proj_bedroom_array = isset($_POST['proj_bedroom']) ? $_POST['proj_bedroom'] : [];
	$proj_bedroom_cleaned = [];
	foreach ($proj_bedroom_array as $bedroom) {
		$bedroom = mysqli_real_escape_string($conn, $bedroom);
		$proj_bedroom_cleaned[] = $bedroom;
	}
	$proj_bedroom = implode(",", $proj_bedroom_cleaned);

	$proj_bedroom_on_card = mysqli_real_escape_string($conn, $_POST['proj_bedroom_on_card']);
	$proj_email = mysqli_real_escape_string($conn, $_POST['proj_email']);
	$proj_whatsaapno = mysqli_real_escape_string($conn, $_POST['proj_whatsaapno']);
	$proj_mobileno = mysqli_real_escape_string($conn, $_POST['proj_mobileno']);
	$proj_dev_img_alt = mysqli_real_escape_string($conn, $_POST['proj_dev_img_alt']);
	$proj_size = mysqli_real_escape_string($conn, $_POST['proj_size']);
	$proj_inventory_type = mysqli_real_escape_string($conn, $_POST['proj_inventory_type']);
	$proj_inventory_type_filter = mysqli_real_escape_string($conn, $_POST['proj_inventory_type_filter']);
	$proj_start_price = mysqli_real_escape_string($conn, $_POST['proj_start_price']);
	$proj_overview = mysqli_real_escape_string($conn, $_POST['proj_overview']);
	$proj_fea_fac_cont = mysqli_real_escape_string($conn, $_POST['proj_fea_fac_cont']);
	$proj_loc_cont = mysqli_real_escape_string($conn, $_POST['proj_loc_cont']);
	$proj_loc_img_alt_tag = mysqli_real_escape_string($conn, $_POST['proj_loc_img_alt_tag']);
	$proj_video = mysqli_real_escape_string($conn, $_POST['proj_video']);
	$proj_masterplan = mysqli_real_escape_string($conn, $_POST['proj_masterplan']);
	$proj_mastrpln_img_alt_tag = mysqli_real_escape_string($conn, $_POST['proj_mastrpln_img_alt_tag']);
	$proj_qr = mysqli_real_escape_string($conn, $_POST['proj_qr']);
	$proj_qr_link = mysqli_real_escape_string($conn, $_POST['proj_qr_link']);
	$proj_slug = mysqli_real_escape_string($conn, $_POST['proj_slug']);
	$proj_map = mysqli_real_escape_string($conn, $_POST['proj_map']);
	$meta_title = mysqli_real_escape_string($conn, $_POST['meta_title']);
	$meta_keyword = mysqli_real_escape_string($conn, $_POST['meta_keyword']);
	$meta_description = mysqli_real_escape_string($conn, $_POST['meta_description']);
	$meta_tags = mysqli_real_escape_string($conn, $_POST['meta_tags']);
	$home_card_order_no = !empty($_POST['home_card_order_no'])
		? (int) $_POST['home_card_order_no']
		: 0;	// $proj_showonhomepage = (prepStr($_POST['proj_showonhomepage'] != '' ? $_POST['proj_showonhomepage'] : 2));
	$proj_showonhomepage = isset($_POST['proj_showonhomepage']) && $_POST['proj_showonhomepage'] != ''
		? prepStr($_POST['proj_showonhomepage'])
		: 2;
	$proj_markfeat = isset($_POST['proj_markfeat'])
		? prepStr($_POST['proj_markfeat'])
		: 2;
	$proj_posted_on = date('Y-m-d', strtotime($_POST['proj_posted_on']));
	$is_active = (prepStr(!empty($_POST['is_active']) ? 1 : 2));

	$prj_thumb_img = $_FILES['prj_thumb_img']['name'];
	$gallery_image_alt = isset($_POST['gallery_image_alt']) ? $_POST['gallery_image_alt'] : [];
	// $gallery_image = $_FILES['gallery_image']['name'];
	// $feat_fac_img = $_FILES['feat_fac_img']['name'];

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

	$longitude = mysqli_real_escape_string($conn, $_POST['longitude'] ?? '');
	$latitude = mysqli_real_escape_string($conn, $_POST['latitude'] ?? '');

	// Additional app-side property fields
	$prop_user_id = !empty($_POST['prop_user_id']) ? (int) $_POST['prop_user_id'] : 'NULL';
	$doc_id = !empty($_POST['doc_id']) ? (int) $_POST['doc_id'] : 'NULL';
	$homepage_status = mysqli_real_escape_string($conn, $_POST['homepage_status'] ?? '');
	$balcony = mysqli_real_escape_string($conn, $_POST['balcony'] ?? '');
	$suite_area = mysqli_real_escape_string($conn, $_POST['suite_area'] ?? '');
	$common_area = mysqli_real_escape_string($conn, $_POST['common_area'] ?? '');
	$balcony_area = mysqli_real_escape_string($conn, $_POST['balcony_area'] ?? '');
	$property_type = mysqli_real_escape_string($conn, $_POST['property_type'] ?? '');
	$floor_number = mysqli_real_escape_string($conn, $_POST['floor_number'] ?? '');
	$property_number = mysqli_real_escape_string($conn, $_POST['property_number'] ?? '');
	$property_status = mysqli_real_escape_string($conn, $_POST['property_status'] ?? '');
	$emirate = mysqli_real_escape_string($conn, $_POST['emirate'] ?? '');
	$loan_interest = mysqli_real_escape_string($conn, $_POST['loan_interest'] ?? '');
	$emi_15_year = mysqli_real_escape_string($conn, $_POST['emi_15_year'] ?? '');
	$emi_20_year = mysqli_real_escape_string($conn, $_POST['emi_20_year'] ?? '');
	$emi_25_year = mysqli_real_escape_string($conn, $_POST['emi_25_year'] ?? '');
	$property_id = mysqli_real_escape_string($conn, $_POST['property_id'] ?? '');
	$proj_status = mysqli_real_escape_string($conn, $_POST['proj_status'] ?? '');

	// App Feature toggles (on/off visibility flags for the app)
	$proj_show_down_payment = !empty($_POST['proj_show_down_payment']) ? 1 : 2;
	$proj_show_payment_plan = !empty($_POST['proj_show_payment_plan']) ? 1 : 2;
	$proj_show_handover = !empty($_POST['proj_show_handover']) ? 1 : 2;
	$proj_show_area = !empty($_POST['proj_show_area']) ? 1 : 2;
	$proj_show_bedroom = !empty($_POST['proj_show_bedroom']) ? 1 : 2;
	$proj_show_shower = !empty($_POST['proj_show_shower']) ? 1 : 2;
	$proj_show_size = !empty($_POST['proj_show_size']) ? 1 : 2;
	$proj_show_balcony = !empty($_POST['proj_show_balcony']) ? 1 : 2;


	// ========== HOME CARD IMAGE UPLOAD WITH FOLDER CREATION ==========
	$home_card_img_path = 'assets/media/project/home-card-img/';

	// Create folder if not exists
	if (!file_exists($home_card_img_path)) {
		mkdir($home_card_img_path, 0777, true);
	}

	// Initialize variable with existing image
	$home_card_img = isset($_POST['home_card_img_hidden']) ? $_POST['home_card_img_hidden'] : '';

	// Process new upload
	if (!empty($_FILES['home_card_img']['name'])) {
		$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
		$file_ext = strtolower(pathinfo($_FILES['home_card_img']['name'], PATHINFO_EXTENSION));

		if (in_array($file_ext, $allowed)) {
			$new_filename = 'home_card_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
			$full_path = $home_card_img_path . $new_filename;

			if (move_uploaded_file($_FILES['home_card_img']['tmp_name'], $full_path)) {
				// Delete old file if exists
				if (!empty($home_card_img) && file_exists($home_card_img_path . $home_card_img)) {
					unlink($home_card_img_path . $home_card_img);
				}
				$home_card_img = $new_filename;
				$_SESSION['success_msg'] = "Home card image uploaded successfully!";
			} else {
				$_SESSION['error_msg'] = "Failed to upload home card image!";
			}
		} else {
			$_SESSION['error_msg'] = "Invalid file type! Allowed: JPG, JPEG, PNG, GIF, WEBP";
		}
	}
	// ========== END HOME CARD IMAGE UPLOAD ==========


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

	// App Feature icon + details pairs (5) - icon is a pasted icon tag, not an upload
	for ($fi = 1; $fi <= 5; $fi++) {
		$iconField = "proj_feature_icon_$fi";
		${$iconField} = mysqli_real_escape_string($conn, $_POST[$iconField] ?? '');

		$textField = "proj_feature_text_$fi";
		${$textField} = mysqli_real_escape_string($conn, $_POST[$textField] ?? '');
	}

	$old_data = $logger->getOldRecordData('tbl_proj', $id);

	$query = mysqli_query($conn, "update tbl_proj
						           SET prj_type_id='$prj_type_id',
						               prj_dev_id='$prj_dev_id',
						               prj_comm_id='$prj_comm_id',
                                       proj_name='$proj_name',
                                       proj_name_on_card='$proj_name_on_card',
									   home_card_order_no='$home_card_order_no',
                                       proj_sub_heading='$proj_sub_heading',
						               prj_thumb_img='$thumb_img_names',
						               proj_price='$proj_price',
						               proj_price_type = '$proj_price_type',
						               proj_pplan_cont='$proj_pplan_cont',
									   proj_handover='$proj_handover',
						               proj_days='$proj_days',
						               proj_location='$proj_location',
						               proj_loc_img='$proj_loc_img',
						               proj_bedroom='$proj_bedroom',
						               proj_bedroom_on_card='$proj_bedroom_on_card',
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
						               proj_inventory_type_filter='$proj_inventory_type_filter',
						               proj_start_price='$proj_start_price',
						               proj_overview='$proj_overview',
						               proj_brochure='$proj_brochure',
						               proj_payplan='$proj_payplan',
						               proj_floorplan='$proj_floorplan',
						               proj_fea_fac_cont='$proj_fea_fac_cont',
						               proj_fea_fac_opt='$proj_fea_fac_opt',
						               proj_loc_cont='$proj_loc_cont',
						               proj_loc_img_alt_tag='$proj_loc_img_alt_tag',
						               proj_video='$proj_video',
						               proj_masterplan='$proj_masterplan',
						               proj_mastrpln_img='$proj_mastrpln_img',
						               proj_mastrpln_img_alt_tag='$proj_mastrpln_img_alt_tag',
						               proj_qr='$proj_qr',
						               proj_qr_link='$proj_qr_link',
						               proj_qr_img='$proj_qr_img',
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
									   home_card_img='$home_card_img',
						               proj_markfeat='$proj_markfeat',
						               proj_posted_on='$proj_posted_on',
						               proj_show_down_payment='$proj_show_down_payment',
						               proj_show_payment_plan='$proj_show_payment_plan',
						               proj_show_handover='$proj_show_handover',
						               proj_show_area='$proj_show_area',
						               proj_show_bedroom='$proj_show_bedroom',
						               proj_show_shower='$proj_show_shower',
						               proj_show_size='$proj_show_size',
						               proj_show_balcony='$proj_show_balcony',
						               longitude='$longitude',
						               latitude='$latitude',
						               prop_user_id=$prop_user_id,
						               doc_id=$doc_id,
						               homepage_status='$homepage_status',
						               balcony='$balcony',
						               suite_area='$suite_area',
						               common_area='$common_area',
						               balcony_area='$balcony_area',
						               property_type='$property_type',
						               floor_number='$floor_number',
						               property_number='$property_number',
						               property_status='$property_status',
						               emirate='$emirate',
						               loan_interest='$loan_interest',
						               emi_15_year='$emi_15_year',
						               emi_20_year='$emi_20_year',
						               emi_25_year='$emi_25_year',
						               property_id='$property_id',
						               proj_status='$proj_status',
									   app_sync_status='0',
									   app_sync_date=NOW(),
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
			'Project updated'
		);

		$_SESSION['offplan_success_msg'] = "Off Plan Property updated successfully!";
		header('Location: edit-project.php?edit=' . base64_encode($id));
		exit;
	} else {
		// Pro Tip: Live platform par direct mysqli_error() user ko mat dikhao (Security Risk)
		$_SESSION['offplan_error_msg'] = "<strong>Error!</strong> Something went wrong while updating the property.<br><b>" . mysqli_error($conn) . "</b>";
		header('Location: edit-project.php?edit=' . base64_encode($id));
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
			<?php include('includes/sidebar.php'); ?>
			<!--end::Aside-->
			<!--begin::Wrapper-->
			<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
				<!--begin::Header-->
				<?php include('includes/topbar-desktop.php'); ?>
				<!--end::Header-->

				<div class="container-fluid mt-4">

					<?php if (isset($_SESSION['offplan_success_msg'])): ?>
						<div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert"
							style="border-left: 4px solid #28a745 !important;">
							<div class="d-flex align-items-center">
								<span class="alert-icon mr-3" style="font-size: 1.2 rem;"> <b><?= $details['proj_name'] ?>
										✔</b> </span>
								<div>
									<?php
									echo $_SESSION['offplan_success_msg'];
									unset($_SESSION['offplan_success_msg']);
									?>
								</div>
							</div>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					<?php endif; ?>

					<?php if (isset($_SESSION['offplan_error_msg'])): ?>
						<div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert"
							style="border-left: 4px solid #dc3545 !important;">
							<div class="d-flex align-items-center">
								<span class="alert-icon mr-3" style="font-size: 1.5rem;"> ⚠ </span>
								<div>
									<?php
									echo $_SESSION['offplan_error_msg'];
									unset($_SESSION['offplan_error_msg']);
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
										<h3 class="card-label">Edit Project</h3>
									</div>
									<div class="card-toolbar">
										<a href="<?= BASE_URL_HOME ?>property/<?= $details['proj_slug'] ?>"
											class="btn btn-primary font-weight-bolder" target="_blank">
											<i class="bi bi-eye"></i>
											View
										</a>&nbsp;&nbsp;
										<!--begin::Button-->
										<a href="<?= BASE_URL ?>manage-project.php"
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

											<!-- Tab Navigation -->
											<ul class="nav nav-tabs nav-tabs-line mb-0" id="projectTabs" role="tablist">
												<li class="nav-item">
													<a class="nav-link active" data-toggle="tab" href="#tab-basic" role="tab">
														<i class="flaticon2-information mr-1"></i> Basic Info
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-pricing" role="tab">
														<i class="flaticon2-money mr-1"></i> Pricing & Inventory
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-media" role="tab">
														<i class="flaticon2-photo-camera mr-1"></i> Media & Files
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-details" role="tab">
														<i class="flaticon2-list-1 mr-1"></i> Property Details
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-content" role="tab">
														<i class="flaticon2-document mr-1"></i> Content
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-contact" role="tab">
														<i class="flaticon2-phone mr-1"></i> Contact
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-seo" role="tab">
														<i class="flaticon2-search-1 mr-1"></i> SEO
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-settings" role="tab">
														<i class="flaticon2-settings mr-1"></i> Settings
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-toggle="tab" href="#tab-additional" role="tab">
														<i class="flaticon2-layers-1 mr-1"></i> Additional Info
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
																		<label>Project Type <span class="text-danger">*</span></label>
																		<select name="prj_type_id" class="form-control">
																			<option value="">Select Project Type</option>
																			<?php
																			$query = mysqli_query($conn, "SELECT * FROM tbl_proj_type WHERE is_active = '1' AND prj_type_id NOT IN (4, 7, 8) ORDER BY proj_type_name");
																			while ($row = mysqli_fetch_array($query)) {
																				?>
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
																		<label>Project Developer <span class="text-danger">*</span></label>
																		<select name="prj_dev_id" class="form-control">
																			<option value="">Select Project Developer</option>
																			<?php
																			$query = mysqli_query($conn, "select * from tbl_developer where is_active='1' order by dev_heading");
																			while ($row = mysqli_fetch_array($query)) {
																				?>
																				<option value="<?= $row['dev_id'] ?>"
																					<?= ($details['prj_dev_id'] == $row['dev_id'] ? 'selected' : '') ?>><?= $row['dev_heading'] ?>
																				</option>
																			<?php } ?>
																		</select>
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Community <span class="text-danger">*</span></label>
																		<select name="prj_comm_id" class="form-control">
																			<option value="">Select Project Community</option>
																			<?php
																			$query = mysqli_query($conn, "select * from tbl_community where is_active='1' order by comm_heading");
																			while ($row = mysqli_fetch_array($query)) {
																				?>
																				<option value="<?= $row['comm_id'] ?>"
																					<?= ($details['prj_comm_id'] == $row['comm_id'] ? 'selected' : '') ?>><?= $row['comm_heading'] ?>
																				</option>
																			<?php } ?>
																		</select>
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Name (On Project Details Page)<span class="text-danger">*</span></label>
																		<input type="text" name="proj_name"
																			value="<?= htmlspecialchars($details['proj_name']) ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Name">
																		<small class="form-text text-muted">This will appear on project detail page</small>
																	</div>
																</div>
																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Name (On Card's) <span class="text-danger">*</span></label>
																		<input type="text" name="proj_name_on_card"
																			value="<?= htmlspecialchars($details['proj_name_on_card']) ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Name">
																	</div>
																</div>
																<div class="col-md-12">
																	<div class="form-group">
																		<label>Project Sub Heading <span class="text-danger">*</span></label>
																		<input type="text" name="proj_sub_heading"
																			value="<?= htmlspecialchars($details['proj_sub_heading']) ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Sub Heading">
																		<small class="form-text text-muted">This will appear on project cards</small>
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Location <span class="text-danger">*</span></label>
																		<input type="text" name="proj_location"
																			value="<?= htmlspecialchars($details['proj_location']) ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Location">
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label for="longitude">Project Longitude</label>
																		<input type="text" id="longitude" name="longitude"
																			value="<?= htmlspecialchars($details['longitude'] ?? '') ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Longitude">
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label for="latitude">Project Latitude</label>
																		<input type="text" id="latitude" name="latitude"
																			value="<?= htmlspecialchars($details['latitude'] ?? '') ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Latitude">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Slug</label>
																		<input type="text" name="proj_slug"
																			value="<?= htmlspecialchars($details['proj_slug']) ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Slug">
																		<small class="form-text text-muted">SEO-friendly URL (auto-generated from name if empty)</small>
																	</div>
																</div>
																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Map</label>
																		<input type="text" name="proj_map"
																			value="<?= htmlspecialchars($details['proj_map']) ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Google Map Embed Code">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Posted On <span class="text-danger">*</span></label>
																		<input type="text" name="proj_posted_on"
																			value="<?= date('d-m-Y', strtotime($details['proj_posted_on'])) ?>"
																			class="form-control datepicker" autocomplete="off"
																			id="kt_datepicker_1" placeholder="DD-MM-YYYY">
																	</div>
																</div>

															</div>
														</div>
													</div>
												</div>
												<!-- ===================== END TAB 1 ===================== -->


												<!-- ===================== TAB 2: PRICING & INVENTORY ===================== -->
												<div class="tab-pane fade" id="tab-pricing" role="tabpanel">
													<div class="card card-custom">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">Pricing & Inventory</h3>
															</div>
														</div>
														<div class="card-body">
															<div class="row">

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Price <span class="text-danger">*</span></label>
																		<input type="text" name="proj_price"
																			value="<?= $details['proj_price'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Price">
																		<?php
																		$saved_price_types = isset($details['proj_price_type']) ? explode(',', $details['proj_price_type']) : [];
																		?>
																		<div class="mt-2 d-flex">
																			<div class="custom-control custom-checkbox mr-3">
																				<input type="checkbox" class="custom-control-input"
																					name="proj_price_type[]" value="year"
																					id="priceYearly" <?= in_array('year', $saved_price_types) ? 'checked' : '' ?>>
																				<label class="custom-control-label" for="priceYearly">Yearly</label>
																			</div>
																			<div class="custom-control custom-checkbox">
																				<input type="checkbox" class="custom-control-input"
																					name="proj_price_type[]" value="month"
																					id="priceMonthly" <?= in_array('month', $saved_price_types) ? 'checked' : '' ?>>
																				<label class="custom-control-label" for="priceMonthly">Monthly</label>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Start Price <span class="text-danger">*</span></label>
																		<input type="text" name="proj_start_price"
																			value="<?= $details['proj_start_price'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Start Price">
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Size <span class="text-danger">*</span></label>
																		<input type="text" name="proj_size"
																			value="<?= $details['proj_size'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Size">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Payment Plan <span class="text-danger">*</span></label>
																		<input type="text" name="proj_pplan_cont"
																			value="<?= $details['proj_pplan_cont'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Payment Plan">
																	</div>
																</div>
																<div class="col-md-6">
																	<div class="form-group">
																		<label>Handover <span class="text-danger">*</span></label>
																		<input type="text" name="proj_handover"
																			value="<?= $details['proj_handover'] ?? "" ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Handover">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Inventory Type <span class="text-danger">*</span></label>
																		<input type="text" name="proj_inventory_type"
																			value="<?= $details['proj_inventory_type'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Inventory Type">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label for="exampleInputPassword1">Inventory Type for filter <span class="text-danger">*</span></label>

																		<!-- Hidden input to store selected values -->
																		<input type="hidden" name="proj_inventory_type_filter"
																			id="proj_inventory_type_hidden"
																			value="<?= htmlspecialchars($details['proj_inventory_type_filter']) ?>">

																		<?php
																		// Get already saved inventory IDs as array
																		$saved_inventory = !empty($details['proj_inventory_type_filter'])
																			? explode(',', $details['proj_inventory_type_filter'])
																			: [];
																		?>

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
																							<label class="form-check-label w-100"
																								for="inv_<?= $inv_row['id'] ?>">
																								<?= htmlspecialchars($inv_row['inventry_name']) ?>
																							</label>
																						</div>
																					</div>
																				<?php } ?>

																			</div>
																		</div>

																		<!-- Show selected items as badges -->
																		<div id="selectedInventoryTags" class="mt-2 d-flex flex-wrap gap-1"></div>
																	</div>
																</div>

																<!-- Hidden/legacy fields kept for backend compatibility -->
																<input type="text" name="proj_days" value="<?= $details['proj_days'] ?>" class="d-none">
																<input type="text" name="proj_shower" value="<?= $details['proj_shower'] ?>" class="d-none">
																<input type="text" name="proj_area" value="<?= $details['proj_area'] ?>" class="d-none">

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
																		<label>Project Thumb Images <span class="text-danger">*</span></label>
																		<input type="file" name="prj_thumb_img[]"
																			class="form-control" multiple accept="image/*">
																		<div class="proj_thumb d-flex flex-wrap gap-2 mt-3">
																			<?php
																			$prj_thumb_img_arr = explode(',', $details['prj_thumb_img']);
																			foreach ($prj_thumb_img_arr as $img) {
																				if ($img != '') {
																					?>
																					<div class="position-relative">
																						<span
																							class="del_thumb_img position-absolute bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
																							style="top: -8px; right: -8px; width: 20px; height: 20px; cursor: pointer; font-size: 12px;"
																							del-name="<?= htmlspecialchars($img) ?>"
																							del-id="<?= $details['prj_id'] ?>">×</span>
																						<img src="<?= BASE_URL ?>assets/media/project/thumb/<?= $img ?>"
																							class="rounded"
																							style="width: 80px; height: 80px; object-fit: cover;">
																					</div>
																				<?php }
																			} ?>
																		</div>
																		<input type="hidden" name="prj_thumb_img_hidden"
																			value="<?= $details['prj_thumb_img'] ?>">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Inner Image <span class="text-danger">*</span></label>
																		<input type="file" name="proj_inner_img"
																			class="form-control" accept="image/*">
																		<?php if (!empty($details['proj_inner_img'])) { ?>
																			<div class="mt-2">
																				<img src="<?= BASE_URL ?>assets/media/project/inner-image/<?= $details['proj_inner_img'] ?>"
																					width="60" class="rounded">
																			</div>
																		<?php } ?>
																		<input type="hidden" name="proj_inner_img_hidden"
																			value="<?= $details['proj_inner_img'] ?>">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Dev Image <span class="text-danger">*</span></label>
																		<input type="file" name="proj_dev_img"
																			class="form-control" accept="image/*">
																		<?php if (!empty($details['proj_dev_img'])) { ?>
																			<div class="mt-2">
																				<img src="<?= BASE_URL ?>assets/media/project/dev/<?= $details['proj_dev_img'] ?>"
																					width="60" class="rounded">
																			</div>
																		<?php } ?>
																		<input type="hidden" name="proj_dev_img_hidden"
																			value="<?= $details['proj_dev_img'] ?>">
																	</div>
																</div>

																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Dev Image Alt <span class="text-danger">*</span></label>
																		<input type="text" name="proj_dev_img_alt"
																			value="<?= htmlspecialchars($details['proj_dev_img_alt']) ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Dev Image Alt">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Location Image <span class="text-danger">*</span></label>
																		<input type="file" name="proj_loc_img"
																			class="form-control" accept="image/*">
																		<?php if (!empty($details['proj_loc_img'])) { ?>
																			<div class="mt-2 d-flex align-items-center">
																				<img src="<?= BASE_URL ?>assets/media/project/location/<?= $details['proj_loc_img'] ?>"
																					width="60" class="rounded mr-2">
																				<a href="javascript:void(0)"
																					class="btn btn-danger btn-sm img_remove"
																					img-id="<?= $id ?>" img-type="location"
																					img-name="<?= $details['proj_loc_img'] ?>">Remove Image</a>
																			</div>
																		<?php } ?>
																		<input type="hidden" name="proj_loc_img_hidden"
																			value="<?= $details['proj_loc_img'] ?>">
																	</div>
																</div>
																<div class="col-md-6">
																	<div class="form-group">
																		<label>Location Image Alt Tag <span class="text-danger">*</span></label>
																		<input type="text" name="proj_loc_img_alt_tag"
																			value="<?= htmlspecialchars($details['proj_loc_img_alt_tag']) ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Location Image Alt Tag">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Masterplan Image <span class="text-danger">*</span></label>
																		<input type="file" name="proj_mastrpln_img"
																			class="form-control" accept="image/*">
																		<?php if (!empty($details['proj_mastrpln_img'])) { ?>
																			<div class="mt-2 d-flex align-items-center">
																				<img src="<?= BASE_URL ?>assets/media/project/masterplan/<?= $details['proj_mastrpln_img'] ?>"
																					width="60" class="rounded mr-2">
																				<a href="javascript:void(0)"
																					class="btn btn-danger btn-sm img_remove"
																					img-id="<?= $id ?>" img-type="masterplan"
																					img-name="<?= $details['proj_mastrpln_img'] ?>">Remove Image</a>
																			</div>
																		<?php } ?>
																		<input type="hidden" name="proj_mastrpln_img_hidden"
																			value="<?= $details['proj_mastrpln_img'] ?>">
																	</div>
																</div>
																<div class="col-md-6">
																	<div class="form-group">
																		<label>Masterplan Image Alt Tag <span class="text-danger">*</span></label>
																		<input type="text" name="proj_mastrpln_img_alt_tag"
																			value="<?= htmlspecialchars($details['proj_mastrpln_img_alt_tag']) ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Masterplan Image Alt Tag">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>QR Image <span class="text-danger">*</span></label>
																		<input type="file" name="proj_qr_img"
																			class="form-control" accept="image/*">
																		<?php if (!empty($details['proj_qr_img'])): ?>
																			<div class="mt-2 d-flex align-items-center">
																				<img src="<?= BASE_URL ?>assets/media/project/qr/<?= $details['proj_qr_img'] ?>"
																					width="50" class="rounded mr-2">
																				<a href="javascript:void(0)"
																					class="btn btn-danger btn-sm img_remove"
																					img-id="<?= $id ?>" img-type="qr"
																					img-name="<?= $details['proj_qr_img'] ?>">Remove QR Image</a>
																			</div>
																		<?php endif; ?>
																		<input type="hidden" name="proj_qr_img_hidden"
																			value="<?= htmlspecialchars($details['proj_qr_img']) ?>">
																	</div>
																</div>

																<div class="col-md-6" id="home_img_preview">
																	<div class="form-group">
																		<label class="font-weight-bold">Home Page Card Image</label>

																		<!-- Hidden field to store current image name -->
																		<input type="hidden" name="home_card_img_hidden"
																			value="<?= htmlspecialchars($details['home_card_img'] ?? '') ?>">

																		<!-- File upload input -->
																		<input type="file" class="form-control"
																			name="home_card_img" accept="image/*">

																		<small class="form-text text-muted">Allowed: JPG, JPEG, PNG, GIF, WEBP (Max 2MB)</small>

																		<?php if (!empty($details['home_card_img'])) { ?>
																			<div class="position-relative d-inline-block mt-3">
																				<!-- Delete Button -->
																				<span
																					class="del_thumb_img position-absolute bg-danger text-white rounded-circle d-flex align-items-center justify-content-center"
																					style="top: -8px; right: -8px; width: 22px; height: 22px; cursor: pointer; font-size: 14px; z-index: 2;"
																					del-name="<?= htmlspecialchars($details['home_card_img']) ?>"
																					del-id="<?= $details['prj_id'] ?>"
																					del-type="home_card">
																					×
																				</span>

																				<!-- Preview Image -->
																				<img src="<?= BASE_URL ?>assets/media/project/home-card-img/<?= htmlspecialchars($details['home_card_img']) ?>"
																					class="rounded border"
																					style="width: 100px; height: 100px; object-fit: cover;"
																					onerror="this.src='<?= BASE_URL ?>assets/media/no-image.png'">
																			</div>
																		<?php } ?>
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
																	<div class="card shadow-sm">
																		<div class="card-body">
																			<label class="font-weight-bold">Project Brochure <span class="text-danger">*</span></label>
																			<input type="file" name="proj_brochure"
																				class="form-control mt-2" accept=".pdf">
																			<?php if (!empty($details['proj_brochure'])) { ?>
																				<div class="mt-3 d-flex justify-content-between align-items-center">
																					<a href="javascript:void(0)"
																						class="btn btn-sm btn-danger pdf_remove"
																						pdf-id="<?= $id ?>" pdf-type="brochure"
																						pdf-name="<?= $details['proj_brochure'] ?>">Remove</a>
																					<a href="<?= BASE_URL ?>assets/media/project/pdf/<?= $details['proj_brochure'] ?>"
																						class="btn btn-sm btn-success">Preview</a>
																				</div>
																			<?php } ?>
																			<input type="hidden" name="proj_brochure_hidden"
																				value="<?= $details['proj_brochure'] ?>">
																		</div>
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="card shadow-sm">
																		<div class="card-body">
																			<label class="font-weight-bold">Project PayPlan <span class="text-danger">*</span></label>
																			<input type="file" name="proj_payplan"
																				class="form-control mt-2" accept=".pdf">
																			<?php if (!empty($details['proj_payplan'])) { ?>
																				<div class="mt-3 d-flex justify-content-between align-items-center">
																					<a href="javascript:void(0)"
																						class="btn btn-sm btn-danger pdf_remove"
																						pdf-id="<?= $id ?>" pdf-type="payplan"
																						pdf-name="<?= $details['proj_payplan'] ?>">Remove</a>
																					<a href="<?= BASE_URL ?>assets/media/project/pdf/<?= $details['proj_payplan'] ?>"
																						class="btn btn-sm btn-success">Preview</a>
																				</div>
																			<?php } ?>
																			<input type="hidden" name="proj_payplan_hidden"
																				value="<?= $details['proj_payplan'] ?>">
																		</div>
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="card shadow-sm">
																		<div class="card-body">
																			<label class="font-weight-bold">Project Floor Plan <span class="text-danger">*</span></label>
																			<input type="file" name="proj_floorplan"
																				class="form-control mt-2" accept=".pdf">
																			<?php if (!empty($details['proj_floorplan'])) { ?>
																				<div class="mt-3 d-flex justify-content-between align-items-center">
																					<a href="javascript:void(0)"
																						class="btn btn-sm btn-danger pdf_remove"
																						pdf-id="<?= $id ?>" pdf-type="floorplan"
																						pdf-name="<?= $details['proj_floorplan'] ?>">Remove</a>
																					<a href="<?= BASE_URL ?>assets/media/project/pdf/<?= $details['proj_floorplan'] ?>"
																						class="btn btn-sm btn-success">Preview</a>
																				</div>
																			<?php } ?>
																			<input type="hidden" name="proj_floorplan_hidden"
																				value="<?= $details['proj_floorplan'] ?>">
																		</div>
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
																<h3 class="card-label">Bedrooms & Video</h3>
															</div>
														</div>
														<div class="card-body">
															<div class="row">

																<div class="col-md-6">
																	<?php
																	// Get saved bedrooms as array from DB (comma-separated string)
																	$proj_bedroom_selected = isset($details['proj_bedroom']) ? explode(',', $details['proj_bedroom']) : [];
																	$proj_bedroom_selected = array_filter(array_map('trim', $proj_bedroom_selected)); // Trim and remove empty values

																	// Unique and sort saved values (optional)
																	$options = array_unique($proj_bedroom_selected);
																	sort($options); // optional, sorts alphabetically/numerically
																	?>
																	<div class="form-group">
																		<label for="proj_bedroom">Project Bedroom (Project Detail Page)<span class="text-danger">*</span></label>
																		<select class="form-control" id="proj_bedroom"
																			name="proj_bedroom[]" multiple="multiple">
																			<?php foreach ($options as $option): ?>
																				<?php
																				$option_str = (string) $option;
																				$selected = in_array($option_str, $proj_bedroom_selected, true) ? 'selected' : '';
																				?>
																				<option value="<?= htmlspecialchars($option_str) ?>"
																					<?= $selected ?>>
																					<?= htmlspecialchars($option_str) ?>
																				</option>
																			<?php endforeach; ?>
																		</select>
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Bedroom (On Cards) <span class="text-danger">*</span></label>
																		<input type="text" name="proj_bedroom_on_card"
																			value="<?= $details['proj_bedroom_on_card'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Bedroom on Card">
																	</div>
																</div>

																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project Video <span class="text-danger">*</span></label>
																		<input type="text" name="proj_video"
																			value="<?= $details['proj_video'] ?>" class="form-control"
																			autocomplete="off" placeholder="Project Video URL">
																	</div>
																</div>
																<div class="col-md-6">
																	<div class="form-group">
																		<label>Project QR Link <span class="text-danger">*</span></label>
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
																<h3 class="card-label">Features &amp; Facility Icons</h3>
															</div>
														</div>
														<div class="card-body">
															<div class="row">
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
																	<div class="col-md-3 col-sm-4 col-6 mb-2">
																		<div class="custom-control custom-checkbox">
																			<input type="checkbox"
																				class="custom-control-input"
																				name="proj_fea_fac_opt[]"
																				value="<?= $featfac_id ?>"
																				id="feat_<?= $featfac_id ?>"
																				<?= $is_checked ?>>
																			<label class="custom-control-label"
																				for="feat_<?= $featfac_id ?>"><?= htmlspecialchars($featfac_name) ?></label>
																		</div>
																	</div>
																<?php } ?>
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
																<label>Project Overview <span class="text-danger">*</span></label>
																<textarea name="proj_overview" class="form-control"
																	placeholder="Project Overview" rows="10"
																	id="kt-tinymce-2"><?= $details['proj_overview'] ?></textarea>
															</div>

															<div class="form-group">
																<label>Project Feature Facility Content <span class="text-danger">*</span></label>
																<textarea name="proj_fea_fac_cont" class="form-control"
																	placeholder="Project Feature Facility Content"
																	id="kt-tinymce-5"
																	rows="10"><?= $details['proj_fea_fac_cont'] ?></textarea>
															</div>

															<div class="form-group">
																<label>Project Location Content <span class="text-danger">*</span></label>
																<textarea name="proj_loc_cont" class="form-control"
																	placeholder="Project Location Content" id="kt-tinymce-6"
																	rows="10"><?= $details['proj_loc_cont'] ?></textarea>
															</div>

															<div class="form-group">
																<label>Project MasterPlan <span class="text-danger">*</span></label>
																<textarea name="proj_masterplan" class="form-control"
																	placeholder="Project MasterPlan" id="kt-tinymce-7"
																	rows="10"><?= $details['proj_masterplan'] ?></textarea>
															</div>

															<div class="form-group">
																<label>Project QR <span class="text-danger">*</span></label>
																<textarea name="proj_qr" class="form-control"
																	placeholder="Project QR" id="kt-tinymce-4"
																	rows="4"><?= $details['proj_qr'] ?></textarea>
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
															<div class="row d-none">
																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Email ID <span class="text-danger">*</span></label>
																		<input type="email" name="proj_email"
																			value="<?= $details['proj_email'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Email ID">
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Whatsapp No <span class="text-danger">*</span></label>
																		<input type="text" name="proj_whatsaapno"
																			value="<?= $details['proj_whatsaapno'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Whatsapp No">
																	</div>
																</div>
																<div class="col-md-4">
																	<div class="form-group">
																		<label>Project Mobile No <span class="text-danger">*</span></label>
																		<input type="text" name="proj_mobileno"
																			value="<?= $details['proj_mobileno'] ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Project Mobile No">
																	</div>
																</div>
															</div>
															<p class="text-muted mb-0">These fields are currently not used on the public site.</p>
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
															<div class="row">
																<div class="col-md-6">
																	<div class="form-group">
																		<label>Meta Title</label>
																		<input type="text" name="meta_title"
																			value="<?= htmlspecialchars($details['meta_title']) ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Meta Title">
																	</div>
																</div>
																<div class="col-md-6">
																	<div class="form-group">
																		<label>Meta Keyword</label>
																		<input type="text" name="meta_keyword"
																			value="<?= htmlspecialchars($details['meta_keyword']) ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Meta Keywords">
																	</div>
																</div>
															</div>

															<div class="form-group">
																<label>Meta Description</label>
																<textarea name="meta_description" class="form-control"
																	placeholder="Meta Description"
																	rows="4"><?= htmlspecialchars($details['meta_description']) ?></textarea>
															</div>

															<div class="form-group">
																<label>Meta Tags</label>
																<textarea name="meta_tags" class="form-control"
																	placeholder="Meta Tags"
																	rows="4"><?= htmlspecialchars($details['meta_tags']) ?></textarea>
															</div>
														</div>
													</div>
												</div>
												<!-- ===================== END TAB 7 ===================== -->


												<!-- ===================== TAB 8: SETTINGS ===================== -->
												<div class="tab-pane fade" id="tab-settings" role="tabpanel">

													<div class="card card-custom mb-4">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">Visibility &amp; Featured Community</h3>
															</div>
														</div>
														<div class="card-body">

															<div class="form-group">
																<label>Project Featured Community <span class="text-danger">*</span></label>
																<div class="row mt-2">
																	<?php
																	$saved_comm_feat = !empty($details['proj_comm_feat']) ? explode(',', $details['proj_comm_feat']) : [];
																	$comm_feat_query = mysqli_query($conn, "SELECT * FROM tbl_community WHERE comm_featured=1 AND is_active=1");
																	while ($comm_feat_row = mysqli_fetch_array($comm_feat_query)) {
																		$comm_id = $comm_feat_row['comm_id'];
																		?>
																		<div class="col-md-3 col-sm-4 col-6 mb-2">
																			<div class="custom-control custom-checkbox">
																				<input type="checkbox"
																					class="custom-control-input"
																					name="proj_comm_feat[]"
																					value="<?= $comm_id ?>"
																					id="comm_<?= $comm_id ?>"
																					<?= in_array($comm_id, $saved_comm_feat) ? 'checked' : '' ?>>
																				<label class="custom-control-label"
																					for="comm_<?= $comm_id ?>">
																					<?= htmlspecialchars($comm_feat_row['comm_heading']) ?>
																				</label>
																			</div>
																		</div>
																	<?php } ?>
																</div>
															</div>

															<hr>

															<div class="row">
																<div class="col-md-3">
																	<div class="form-group">
																		<label>Show On Homepage</label>
																		<div class="custom-control custom-switch">
																			<input type="checkbox" class="custom-control-input"
																				name="proj_showonhomepage" value="1"
																				id="showHomepage"
																				<?= ($details['proj_showonhomepage'] == 1 ? 'checked' : '') ?>>
																			<label class="custom-control-label"
																				for="showHomepage">Yes</label>
																		</div>
																	</div>
																</div>
																<div class="col-md-3" id="homeOrderNo">
																	<div class="form-group">
																		<label>Home Page Card Order No</label>
																		<input type="text" name="home_card_order_no"
																			value="<?= htmlspecialchars($details['home_card_order_no'] ?? '') ?>"
																			class="form-control" autocomplete="off"
																			placeholder="Card Order No.">
																	</div>
																</div>
																<div class="col-md-3">
																	<div class="form-group">
																		<label>Mark as Featured</label>
																		<div class="custom-control custom-switch">
																			<input type="checkbox" class="custom-control-input"
																				name="proj_markfeat" value="1" id="markFeatured"
																				<?= ($details['proj_markfeat'] == 1 ? 'checked' : '') ?>>
																			<label class="custom-control-label"
																				for="markFeatured">Yes</label>
																		</div>
																	</div>
																</div>
																<div class="col-md-3">
																	<div class="form-group">
																		<label>Status</label>
																		<div class="custom-control custom-switch">
																			<input type="checkbox" class="custom-control-input"
																				name="is_active" value="1" id="isActive"
																				<?= ($details['is_active'] == 1 ? 'checked' : '') ?>>
																			<label class="custom-control-label"
																				for="isActive">Active</label>
																		</div>
																	</div>
																</div>
															</div>

														</div>
													</div>

													<!-- App Feature -->
													<div class="card card-custom mb-4">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">App Feature Toggles</h3>
															</div>
														</div>
														<div class="card-body">
															<div class="row">
																<div class="col-md-3">
																	<div class="custom-control custom-switch">
																		<input type="checkbox" class="custom-control-input"
																			name="proj_show_down_payment" value="1"
																			id="showDownPayment"
																			<?= ($details['proj_show_down_payment'] == 1 ? 'checked' : '') ?>>
																		<label class="custom-control-label"
																			for="showDownPayment">Down Payment</label>
																			<br>
																		<span class="text-success"><?php if (!empty($details['proj_pplan_cont'])) {
																			echo $details['proj_pplan_cont'];
																		} else {
																			echo "<i class='text-danger'>Empty</i>";
																		} ?>
																		</span>
																	</div>
																</div>
																<div class="col-md-3">
																	<div class="custom-control custom-switch">
																		<input type="checkbox" class="custom-control-input"
																			name="proj_show_payment_plan" value="1"
																			id="showPaymentPlan"
																			<?= ($details['proj_show_payment_plan'] == 1 ? 'checked' : '') ?>>
																		<label class="custom-control-label"
																			for="showPaymentPlan">Payment Plan</label>
																		<br>
																		<span class="text-success"><?php if (!empty($details['proj_pplan_cont'])) {
																			echo $details['proj_pplan_cont'];
																		} else {
																			echo "<i class='text-danger'>Empty</i>";
																		} ?>
																		</span>
																	</div>
																</div>
																<div class="col-md-3">
																	<div class="custom-control custom-switch">
																		<input type="checkbox" class="custom-control-input"
																			name="proj_show_handover" value="1"
																			id="showHandover"
																			<?= ($details['proj_show_handover'] == 1 ? 'checked' : '') ?>>
																		<label class="custom-control-label"
																			for="showHandover">Handover</label>
																		<br>
																		<span class="text-success"><?php if (!empty($details['proj_handover'])) {
																			echo $details['proj_handover'];
																		} else {
																			echo "<i class='text-danger'>Empty</i>";
																		} ?>
																		</span>
																	</div>
																</div>
																<div class="col-md-3">
																	<div class="custom-control custom-switch">
																		<input type="checkbox" class="custom-control-input"
																			name="proj_show_area" value="1" id="showArea"
																			<?= ($details['proj_show_area'] == 1 ? 'checked' : '') ?>>
																		<label class="custom-control-label"
																			for="showArea">Area, sq ft</label>
																			<br>
																		<span class="text-success"><?php if (!empty($details['proj_area'])) {
																			echo $details['proj_area'];
																		} else {
																			echo "<i class='text-danger'>Empty</i>";
																		} ?>
																		</span>
																	</div>
																</div>
																<div class="col-md-3 mt-3">
																	<div class="custom-control custom-switch">
																		<input type="checkbox" class="custom-control-input"
																			name="proj_show_bedroom" value="1"
																			id="showBedroom"
																			<?= ($details['proj_show_bedroom'] == 1 ? 'checked' : '') ?>>
																		<label class="custom-control-label"
																			for="showBedroom">Bedroom</label>
																		<br>
																		<span class="text-success"><?php if (!empty($details['proj_bedroom'])) {
																			echo $details['proj_bedroom'];
																		} else {
																			echo "<i class='text-danger'>Empty</i>";
																		} ?>
																		</span>
																	</div>
																</div>
																<div class="col-md-3 mt-3">
																	<div class="custom-control custom-switch">
																		<input type="checkbox" class="custom-control-input"
																			name="proj_show_shower" value="1"
																			id="showShower"
																			<?= ($details['proj_show_shower'] == 1 ? 'checked' : '') ?>>
																		<label class="custom-control-label"
																			for="showShower">Shower</label>
																			<br>
																		<span class="text-success"><?php if (!empty($details['proj_shower'])) {
																			echo $details['proj_shower'];
																		} else {
																			echo "<i class='text-danger'>Empty</i>";
																		} ?>
																		</span>
																	</div>
																</div>
																<div class="col-md-3 mt-3">
																	<div class="custom-control custom-switch">
																		<input type="checkbox" class="custom-control-input"
																			name="proj_show_size" value="1" id="showSize"
																			<?= ($details['proj_show_size'] == 1 ? 'checked' : '') ?>>
																		<label class="custom-control-label"
																			for="showSize">Project Size</label>
																		<br>
																		<span class="text-success"><?php if (!empty($details['proj_size'])) {
																			echo $details['proj_size'];
																		} else {
																			echo "<i class='text-danger'>Empty</i>";
																		} ?>
																		</span>
																	</div>
																</div>
																<div class="col-md-3 mt-3">
																	<div class="custom-control custom-switch">
																		<input type="checkbox" class="custom-control-input"
																			name="proj_show_balcony" value="1"
																			id="showBalcony"
																			<?= ($details['proj_show_balcony'] == 1 ? 'checked' : '') ?>>
																		<label class="custom-control-label"
																			for="showBalcony">Balcony</label>
																		<br>
																		<span class="text-success"><?php if (!empty($details['balcony'])) {
																			echo $details['balcony'];
																		} else {
																			echo "<i class='text-danger'>Empty</i>";
																		} ?>
																		</span>
																	</div>
																</div>
															</div>
														</div>
													</div>

													<!-- Feature Icons (icon + details pairs) -->
													<div class="card card-custom">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">Add Extra Field in App</h3>
															</div>
														</div>
														<div class="card-body">
															<div class="row">
																<?php
																// Curated Font Awesome 5 icons relevant to real-estate/amenity features.
																// Single source of truth: also drives the live icon preview via data-icon-class.
																$FEATURE_ICON_LIST = [
																	'fa-home' => 'Home', 'fa-building' => 'Building', 'fa-city' => 'City',
																	'fa-warehouse' => 'Warehouse', 'fa-store' => 'Store', 'fa-store-alt' => 'Retail',
																	'fa-hotel' => 'Hotel', 'fa-landmark' => 'Landmark', 'fa-industry' => 'Industry',
																	'fa-door-open' => 'Door Open', 'fa-door-closed' => 'Door Closed', 'fa-window-maximize' => 'Window',
																	'fa-ruler-combined' => 'Area / Size', 'fa-expand-arrows-alt' => 'Expand', 'fa-compress-arrows-alt' => 'Compress',
																	'fa-bed' => 'Bedroom', 'fa-couch' => 'Living Room', 'fa-bath' => 'Bathroom',
																	'fa-swimming-pool' => 'Swimming Pool', 'fa-dumbbell' => 'Gym', 'fa-spa' => 'Spa',
																	'fa-hot-tub' => 'Hot Tub', 'fa-umbrella-beach' => 'Beach', 'fa-tree' => 'Garden',
																	'fa-leaf' => 'Landscaping', 'fa-seedling' => 'Green Area', 'fa-campground' => 'Camping',
																	'fa-hiking' => 'Jogging Track', 'fa-futbol' => 'Football', 'fa-basketball-ball' => 'Basketball',
																	'fa-volleyball-ball' => 'Volleyball', 'fa-table-tennis' => 'Table Tennis', 'fa-child' => 'Kids Play Area',
																	'fa-baby' => 'Nursery', 'fa-dog' => 'Pet Friendly', 'fa-paw' => 'Pet Area',
																	'fa-concierge-bell' => 'Concierge', 'fa-utensils' => 'Restaurant', 'fa-coffee' => 'Cafe',
																	'fa-glass-cheers' => 'Lounge / Bar', 'fa-shopping-cart' => 'Supermarket', 'fa-shopping-bag' => 'Shopping',
																	'fa-wifi' => 'Wi-Fi', 'fa-tv' => 'TV / Cable', 'fa-phone-alt' => 'Phone Line',
																	'fa-mobile-alt' => 'Mobile', 'fa-laptop' => 'Business Center', 'fa-plug' => 'Power Backup',
																	'fa-bolt' => 'Electricity', 'fa-lightbulb' => 'Lighting', 'fa-fan' => 'Ventilation',
																	'fa-thermometer-half' => 'Central AC', 'fa-snowflake' => 'Air Conditioning', 'fa-sun' => 'Sunny',
																	'fa-cloud' => 'Weather', 'fa-wind' => 'Wind', 'fa-tint' => 'Water Supply',
																	'fa-water' => 'Waterfront', 'fa-solar-panel' => 'Solar Power', 'fa-recycle' => 'Waste Disposal',
																	'fa-shield-alt' => 'Security', 'fa-lock' => 'Gated Community', 'fa-key' => 'Access Control',
																	'fa-video' => 'CCTV', 'fa-fire-extinguisher' => 'Fire Safety', 'fa-first-aid' => 'First Aid',
																	'fa-wheelchair' => 'Accessible', 'fa-users' => 'Community Hall', 'fa-user-friends' => 'Family Area',
																	'fa-handshake' => 'Deal / Agreement', 'fa-briefcase' => 'Business',
																	'fa-parking' => 'Parking', 'fa-car' => 'Car', 'fa-car-side' => 'Covered Parking',
																	'fa-bus' => 'Bus Access', 'fa-subway' => 'Metro Access', 'fa-train' => 'Train Access',
																	'fa-plane' => 'Airport Access', 'fa-ship' => 'Marina', 'fa-bicycle' => 'Cycling Track',
																	'fa-road' => 'Road Access', 'fa-traffic-light' => 'Traffic', 'fa-gas-pump' => 'Fuel Station',
																	'fa-map-marker-alt' => 'Location', 'fa-map-marked-alt' => 'Prime Location', 'fa-compass' => 'Direction',
																	'fa-globe' => 'International', 'fa-clock' => '24/7 Service', 'fa-calendar-alt' => 'Booking',
																	'fa-dollar-sign' => 'Price', 'fa-hand-holding-usd' => 'Financing', 'fa-coins' => 'Investment',
																	'fa-credit-card' => 'Payment Plan', 'fa-chart-line' => 'ROI', 'fa-percentage' => 'Discount',
																	'fa-check-circle' => 'Verified', 'fa-star' => 'Featured', 'fa-gem' => 'Luxury',
																	'fa-crown' => 'Premium', 'fa-award' => 'Award Winning', 'fa-trophy' => 'Best in Class',
																	'fa-tools' => 'Maintenance', 'fa-hammer' => 'Under Construction', 'fa-paint-roller' => 'Finishing',
																	'fa-mountain' => 'Mountain View', 'fa-hospital' => 'Hospital Nearby', 'fa-school' => 'School Nearby',
																	'fa-church' => 'Place of Worship', 'fa-university' => 'University Nearby',
																];
																?>
																<?php for ($fi = 1; $fi <= 5; $fi++): ?>
																	<div class="col-md-4 mb-3">
																		<div class="card shadow-sm">
																			<div class="card-body">
																				<?php if ($fi <= 4): ?>
																					<!-- Icon picker: searchable Select2 dropdown (curated Font Awesome 5 icons) -->
																					<label class="font-weight-bold">Feature Icon <?= $fi ?> Tag</label>
																					<?php
																					$icon_current = $details["proj_feature_icon_$fi"] ?? '';
																					preg_match('/fa-[a-z0-9-]+/', $icon_current, $icon_match);
																					$icon_current_class = $icon_match[0] ?? '';
																					?>
																					<select name="proj_feature_icon_<?= $fi ?>"
																						class="form-control feature-icon-select mt-2">
																						<option value="">Select Icon</option>
																						<?php foreach ($FEATURE_ICON_LIST as $ic => $lbl): ?>
																							<option value="<?= htmlspecialchars('<i class="fas ' . $ic . '"></i>') ?>"
																								data-icon-class="<?= $ic ?>"
																								<?= $icon_current_class === $ic ? 'selected' : '' ?>>
																								<?= htmlspecialchars($lbl) ?>
																							</option>
																						<?php endforeach; ?>
																					</select>
																				<?php else: ?>
																					<!-- Field 5: free text (custom/manual icon tag) -->
																					<label class="font-weight-bold">Feature Icon <?= $fi ?> Tag</label>
																					<input type="text"
																						name="proj_feature_icon_<?= $fi ?>"
																						value="<?= htmlspecialchars($details["proj_feature_icon_$fi"] ?? '') ?>"
																						class="form-control mt-2" autocomplete="off"
																						placeholder='e.g. &lt;i class="fas fa-swimming-pool"&gt;&lt;/i&gt;'>
																					<?php if (!empty($details["proj_feature_icon_$fi"])): ?>
																						<div class="mt-2" style="font-size: 24px;">
																							<?= $details["proj_feature_icon_$fi"] ?>
																						</div>
																					<?php endif; ?>
																				<?php endif; ?>

																				<label class="font-weight-bold mt-3">Feature <?= $fi ?> Details</label>
																				<input type="text"
																					name="proj_feature_text_<?= $fi ?>"
																					value="<?= htmlspecialchars($details["proj_feature_text_$fi"] ?? '') ?>"
																					class="form-control mt-2" autocomplete="off"
																					placeholder="e.g. 5 Min to Metro">
																			</div>
																		</div>
																	</div>
																<?php endfor; ?>
															</div>
														</div>
													</div>

												</div>
												<!-- ===================== END TAB 8 ===================== -->


												<!-- ===================== TAB 9: ADDITIONAL INFO ===================== -->
												<div class="tab-pane fade" id="tab-additional" role="tabpanel">
													<div class="card card-custom">
														<div class="card-header">
															<div class="card-title">
																<h3 class="card-label">Additional Property Info</h3>
															</div>
														</div>
														<div class="card-body">
															<div class="row">
																<div class="col-md-3 mb-3">
																	<label>Submitted By User ID</label>
																	<input type="text" name="prop_user_id"
																		value="<?= htmlspecialchars($details['prop_user_id'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Document ID</label>
																	<input type="text" name="doc_id"
																		value="<?= htmlspecialchars($details['doc_id'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Homepage Status</label>
																	<input type="text" name="homepage_status"
																		value="<?= htmlspecialchars($details['homepage_status'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Balcony</label>
																	<input type="text" name="balcony"
																		value="<?= htmlspecialchars($details['balcony'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Suite Area</label>
																	<input type="text" name="suite_area"
																		value="<?= htmlspecialchars($details['suite_area'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Common Area</label>
																	<input type="text" name="common_area"
																		value="<?= htmlspecialchars($details['common_area'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Balcony Area</label>
																	<input type="text" name="balcony_area"
																		value="<?= htmlspecialchars($details['balcony_area'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Property Type</label>
																	<input type="text" name="property_type"
																		value="<?= htmlspecialchars($details['property_type'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Floor Number</label>
																	<input type="text" name="floor_number"
																		value="<?= htmlspecialchars($details['floor_number'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Property Number</label>
																	<input type="text" name="property_number"
																		value="<?= htmlspecialchars($details['property_number'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Property Status</label>
																	<input type="text" name="property_status"
																		value="<?= htmlspecialchars($details['property_status'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Emirate</label>
																	<input type="text" name="emirate"
																		value="<?= htmlspecialchars($details['emirate'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Loan Interest</label>
																	<input type="text" name="loan_interest"
																		value="<?= htmlspecialchars($details['loan_interest'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>EMI (15 Year)</label>
																	<input type="text" name="emi_15_year"
																		value="<?= htmlspecialchars($details['emi_15_year'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>EMI (20 Year)</label>
																	<input type="text" name="emi_20_year"
																		value="<?= htmlspecialchars($details['emi_20_year'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>EMI (25 Year)</label>
																	<input type="text" name="emi_25_year"
																		value="<?= htmlspecialchars($details['emi_25_year'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Property ID</label>
																	<input type="text" name="property_id"
																		value="<?= htmlspecialchars($details['property_id'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
																<div class="col-md-3 mb-3">
																	<label>Project Status</label>
																	<input type="text" name="proj_status"
																		value="<?= htmlspecialchars($details['proj_status'] ?? '') ?>"
																		class="form-control" autocomplete="off">
																</div>
															</div>
														</div>
													</div>
												</div>
												<!-- ===================== END TAB 9 ===================== -->

											</div><!-- end tab-content -->

											<div class="card-footer">
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

				<script>

					const checkbox = document.getElementById('showHomepage');
					const homeOrderNo = document.getElementById('homeOrderNo');

					function toggleHomeOrderField() {

						if (checkbox.checked) {

							homeOrderNo.style.display = "block";

						} else {

							homeOrderNo.style.display = "none";

						}
					}

					toggleHomeOrderField();

					checkbox.addEventListener('change', toggleHomeOrderField);

				</script>
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

					// ===================== Feature Icon Picker (Select2, searchable) =====================
					$('.feature-icon-select').select2({
						width: '100%',
						placeholder: 'Select Icon',
						templateResult: formatFeatureIconOption,
						templateSelection: formatFeatureIconOption
					});

					function formatFeatureIconOption(option) {
						if (!option.id) {
							return option.text;
						}
						var iconClass = $(option.element).data('icon-class');
						if (!iconClass) {
							return option.text;
						}
						return $('<span><i class="fas ' + iconClass + ' mr-2"></i>' + option.text + '</span>');
					}
					// ===================== End Feature Icon Picker =====================
				</script>