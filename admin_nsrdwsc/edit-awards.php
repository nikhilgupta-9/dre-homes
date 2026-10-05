<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);
$err = []; // Initialize empty array instead of array with empty string
$date = date('Y-m-d H:i:s'); // Define the date variable

$id = base64_decode($_GET['edit']);

// Fetch current details
$details = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_awards where award_id=$id"));

if (isset($_POST['update_awards'])) {

	// Handle image upload
	$award_img = '';
	$award_img_alt = prepStr($_POST['award_img_alt']);
	$award_heading = prepStr($_POST['award_heading']);
	$award_short_descp = prepStr($_POST['award_short_descp']);
	$award_descp = prepStr($_POST['award_descp']);
	$award_postedby = prepStr($_POST['award_postedby']);
	$award_showonhomepage = isset($_POST['award_showonhomepage']) ? 1 : 2;
	$is_active = isset($_POST['is_active']) ? 1 : 2;
	$new_date = $_POST['tstp'];

	// Allowed image extensions
	$allowed_image_extension = array("png", "jpg", "jpeg");

	// Validate new image if uploaded
	if (!empty($_FILES['award_img']['name'])) {
		$test_file_extension = strtolower(pathinfo($_FILES["award_img"]["name"], PATHINFO_EXTENSION));

		if (!in_array($test_file_extension, $allowed_image_extension)) {
			$err[] = 'Invalid Image. Only PNG, JPG and JPEG are allowed.';
		} else {
			// Generate new filename
			$award_img = 'award_img_' . rand(0, 10000) . '_' . time() . '_' . $_FILES['award_img']['name'];
		}
	} else {
		$award_img = $_POST['award_img_hidden'];
	}

	// If no errors, proceed with update
	if (count($err) == 0) {

		// Upload new image if provided
		if (!empty($_FILES['award_img']['name']) && empty($err)) {
			if (move_uploaded_file($_FILES['award_img']['tmp_name'], "assets/media/awards/$award_img")) {
				// Delete old image if exists and not the default
				if (!empty($_POST['award_img_hidden']) && file_exists('assets/media/awards/' . $_POST['award_img_hidden'])) {
					unlink('assets/media/awards/' . $_POST['award_img_hidden']);
				}
			} else {
				$err[] = 'Failed to upload image.';
			}
		}

		// Get old data for logging
		$old_data = $logger->getOldRecordData('tbl_awards', $id);

		// Update query - fixed table name to tbl_awards
		$query = mysqli_query($conn, "UPDATE tbl_awards 
                                      SET award_img='$award_img',
                                          award_img_alt='$award_img_alt',
                                          award_heading='$award_heading',
                                          award_short_descp='$award_short_descp',
                                          award_descp='$award_descp',
                                          award_postedby='$award_postedby',
                                          award_showonhomepage='$award_showonhomepage',
                                          is_active='$is_active',
                                          tstp='$new_date ' 
                                      WHERE award_id=$id");

		if ($query) {
			// Get new data for logging
			$new_data = $logger->getOldRecordData('tbl_awards', $id);

			$logger->log(
				'UPDATE',
				'tbl_awards',
				$id,
				$old_data,
				$new_data,
				'Update Award Detail'
			);

			$_SESSION['success_msg'] = "Award updated successfully!";
			header('Location:' . BASE_URL . 'manage-awards.php');
			exit();
		} else {
			$err[] = 'Database error: ' . mysqli_error($conn);
		}
	}
}

// Refresh details after update
$details = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_awards where award_id=$id"));
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
										<h3 class="card-label">Edit Awards</h3>
									</div>
									<div class="card-toolbar">
										<a href="<?= BASE_URL ?>manage-awards.php"
											class="btn btn-primary font-weight-bolder">
											<span class="svg-icon svg-icon-md">
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
											</span>Go Back
										</a>
									</div>
								</div>
								<div class="card-body">
									<?php if (count($err) > 0) { ?>
										<div class="alert alert-danger">
											<ul class="err_list mb-0">
												<?php foreach ($err as $list_err) {
													if (!empty($list_err)) { ?>
														<li><?= htmlspecialchars($list_err) ?></li>
													<?php }
												} ?>
											</ul>
										</div>
									<?php } ?>

									<!--begin::Form-->
									<form action="" enctype="multipart/form-data" method="post" id="edit-awards">
										<div class="card-body">
											<!-- Image Upload Section -->
											<div class="form-group row">
												<label class="col-form-label col-lg-2 col-sm-12">Image <span
														class="text-danger">*</span></label>
												<div class="col-lg-10 col-md-9 col-sm-12">
													<div class="custom-file">
														<input type="file" name="award_img" class="custom-file-input"
															id="awardImage">
														<label class="custom-file-label" for="awardImage">Choose
															file</label>
													</div>
													<?php if (!empty($details['award_img'])) { ?>
														<div class="mt-3">
															<img src="<?= BASE_URL ?>assets/media/awards/<?= $details['award_img'] ?>"
																width="80" class="img-thumbnail">
															<small class="text-muted d-block mt-1">Current image</small>
														</div>
													<?php } ?>
													<input type="hidden" name="award_img_hidden"
														value="<?= $details['award_img'] ?>">
													<small class="form-text text-muted">Allowed formats: JPG, PNG, JPEG
														(Max size: 2MB)</small>
												</div>
											</div>

											<!-- Image Alt Tag -->
											<div class="form-group row">
												<label class="col-form-label col-lg-2 col-sm-12">Image Alt Tag <span
														class="text-danger">*</span></label>
												<div class="col-lg-10 col-md-9 col-sm-12">
													<input type="text" name="award_img_alt"
														value="<?= htmlspecialchars($details['award_img_alt']) ?>"
														class="form-control" autocomplete="off"
														placeholder="Enter image alt tag">
													<small class="form-text text-muted">SEO friendly alt text for the
														image</small>
												</div>
											</div>

											<!-- Heading -->
											<div class="form-group row">
												<label class="col-form-label col-lg-2 col-sm-12">Heading <span
														class="text-danger">*</span></label>
												<div class="col-lg-10 col-md-9 col-sm-12">
													<input type="text" name="award_heading"
														value="<?= htmlspecialchars($details['award_heading']) ?>"
														class="form-control" autocomplete="off"
														placeholder="Enter heading">
													<small class="form-text text-muted">Main title for the award</small>
												</div>
											</div>

											<!-- Short Description -->
											<div class="form-group row">
												<label class="col-form-label col-lg-2 col-sm-12">Short Description <span
														class="text-danger">*</span></label>
												<div class="col-lg-10 col-md-9 col-sm-12">
													<textarea name="award_short_descp" class="form-control"
														id="kt-tinymce-3" placeholder="Enter short description"
														rows="6"><?= htmlspecialchars($details['award_short_descp']) ?></textarea>
													<small class="form-text text-muted">Brief summary of the award (max
														500 characters)</small>
												</div>
											</div>

											<!-- Description -->
											<div class="form-group row">
												<label class="col-form-label col-lg-2 col-sm-12">Description <span
														class="text-danger">*</span></label>
												<div class="col-lg-10 col-md-9 col-sm-12">
													<textarea name="award_descp" class="form-control" id="kt-tinymce-4"
														placeholder="Enter full description"
														rows="10"><?= htmlspecialchars($details['award_descp']) ?></textarea>
													<small class="form-text text-muted">Detailed information about the
														award</small>
												</div>
											</div>

											<!-- Posted By -->
											<div class="form-group row">
												<label class="col-form-label col-lg-2 col-sm-12">Posted By</label>
												<div class="col-lg-10 col-md-9 col-sm-12">
													<input type="text" name="award_postedby"
														value="<?= htmlspecialchars($details['award_postedby']) ?>"
														class="form-control" autocomplete="off"
														placeholder="Enter name of person posting">
													<small class="form-text text-muted">Name of the person/team posting
														this award</small>
												</div>
											</div>

											<!-- Remove the Posted By/Date field entirely and just display the tstp -->
<!-- Posted Date (Read-only from tstp) -->
<div class="form-group row">
    <label class="col-form-label col-lg-2 col-sm-12">Posted Date</label>
    <div class="col-lg-10 col-md-9 col-sm-12">
        <input type="date" class="form-control" name="tstp"
               value="<?= date('Y-m-d', strtotime($details['tstp'])) ?>">
        <small class="form-text text-muted">Date when this record was created (auto-generated)</small>
    </div>
</div>

											<!-- Show On Homepage -->
											<div class="form-group row">
												<label class="col-form-label col-lg-2 col-sm-12">Show On
													Homepage</label>
												<div class="col-lg-10 col-md-9 col-sm-12">
													<div class="checkbox-inline">
														<label class="checkbox">
															<input type="checkbox" name="award_showonhomepage" value="1"
																<?= ($details['award_showonhomepage'] == 1 ? 'checked' : '') ?>>
															<span></span>
															Display this award on the homepage
														</label>
													</div>
													<small class="form-text text-muted">Check to show this award on the
														homepage carousel/slider</small>
												</div>
											</div>

											<!-- Status Toggle Switch -->
											<div class="form-group row">
												<label class="col-form-label col-lg-2 col-sm-12">Status</label>
												<div class="col-lg-10 col-md-9 col-sm-12">
													<span class="switch switch-md switch-icon">
														<label>
															<input type="checkbox" name="is_active" value="1"
																<?= ($details['is_active'] == 1 ? 'checked' : '') ?>>
															<span></span>
														</label>
													</span>
													<span class="ml-2 font-weight-bold" id="statusLabel">
														<?= ($details['is_active'] == 1 ? 'Active' : 'Inactive') ?>
													</span>
													<small class="form-text text-muted">Toggle to enable/disable this
														award</small>
												</div>
											</div>
										</div>

										<!-- Form Footer -->
										<div class="card-footer d-flex justify-content-between">
											<div>
												<a href="<?= BASE_URL ?>manage-awards.php" class="btn btn-secondary">
													<i class="fas fa-arrow-left"></i> Back to List
												</a>
											</div>
											<div>
												<button type="reset" class="btn btn-secondary mr-2">
													<i class="fas fa-undo"></i> Reset
												</button>
												<button type="submit" name="update_awards" class="btn btn-primary">
													<i class="fas fa-save"></i> Update Award
												</button>
											</div>
										</div>
									</form>
									<!--end::Form-->
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