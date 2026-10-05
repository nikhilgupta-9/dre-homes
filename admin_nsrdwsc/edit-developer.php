<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);

$err[] = '';
$id = base64_decode($_GET['edit']);
if (isset($_POST['update_developer'])) {

	$dev_img = 'dev_img_' . rand(0, 10000) . '_' . $_FILES['dev_img']['name'];
	$choose_us_img = 'choose_us_img_' . rand(0, 10000) . '_' . $_FILES['choose_us_img']['name'];
	$dev_img_alt = prepStr($_POST['dev_img_alt']);
	$dev_homp_img = 'dev_homp_img_' . rand(0, 10000) . '_' . $_FILES['dev_homp_img']['name'];
	$dev_homp_img_alt = prepStr($_POST['dev_homp_img_alt']);
	$dev_innr_img = 'dev_innr_img_' . rand(0, 10000) . '_' . $_FILES['dev_innr_img']['name'];
	$dev_heading = prepStr($_POST['dev_heading']);
	$dev_short_descp = prepStr($_POST['dev_short_descp']);
	$dev_long_descp = prepStr($_POST['dev_long_descp']);
	$dev_address = prepStr($_POST['dev_address']);
	$dev_phoneno = prepStr($_POST['dev_phoneno']);
	$dev_email = prepStr($_POST['dev_email']);
	$choose_us_desc = prepStr($_POST['choose_us_desc']);
	$dev_posted_on = date('Y-m-d', strtotime($_POST['dev_posted_on']));
	$dev_slug = prepStr($_POST['dev_slug']);
	$meta_title = prepStr($_POST['meta_title']);
	$meta_keyword = prepStr($_POST['meta_keyword']);
	$meta_description = prepStr($_POST['meta_description']);
	$meta_tags = prepStr($_POST['meta_tags']);
	$dev_showonhomepage = (prepStr($_POST['dev_showonhomepage'] != '' ? $_POST['dev_showonhomepage'] : 2));
	$is_active = (prepStr($_POST['is_active'] != '' ? 1 : 2));


	if (!empty($_FILES['dev_img']['name'])) {
		$dev_img = 'dev_img_' . rand(0, 10000) . '_' . $_FILES['dev_img']['name'];
	} else {
		$dev_img = $_POST['dev_img_hidden'];
	}



	if (!empty($_FILES['dev_homp_img']['name'])) {
		$dev_homp_img = 'dev_homp_img_' . rand(0, 10000) . '_' . $_FILES['dev_homp_img']['name'];
	} else {
		$dev_homp_img = $_POST['dev_homp_img_hidden'];
	}


	if (!empty($_FILES['dev_innr_img']['name'])) {
		$dev_innr_img = 'dev_innr_img_' . rand(0, 10000) . '_' . $_FILES['dev_innr_img']['name'];
	} else {
		$dev_innr_img = $_POST['dev_innr_img_hidden'];
	}


	if (!empty($_FILES['choose_us_img']['name'])) {
		$choose_us_img = 'choose_us_img_' . rand(0, 10000) . '_' . $_FILES['choose_us_img']['name'];
	} else {
		$choose_us_img = $_POST['choose_us_img_hidden'];
	}

	if (count($err) == 1) {

		if (!empty($_FILES['dev_img']['name'])) {
			move_uploaded_file($_FILES['dev_img']['tmp_name'], "assets/media/developer/$dev_img");
			unlink('assets/media/developer/' . $_POST['dev_img_hidden']);
		}

		if (!empty($_FILES['dev_homp_img']['name'])) {
			move_uploaded_file($_FILES['dev_homp_img']['tmp_name'], "assets/media/developer/homepage-image/$dev_homp_img");
			unlink('assets/media/developer/homepage-image/' . $_POST['dev_homp_img_hidden']);
		}


		if (!empty($_FILES['dev_innr_img']['name'])) {
			move_uploaded_file($_FILES['dev_innr_img']['tmp_name'], "assets/media/developer/inner-image/$dev_innr_img");
			unlink('assets/media/developer/inner-image/' . $_POST['dev_innr_img_hidden']);
		}


		if (!empty($_FILES['choose_us_img']['name'])) {
			move_uploaded_file($_FILES['choose_us_img']['tmp_name'], "assets/media/developer/choose-us/$choose_us_img");
			unlink('assets/media/developer/choose-us/' . $_POST['choose_us_img_hidden']);
		}

		$old_data = $logger->getOldRecordData('tbl_developer', $id);


		$query = mysqli_query($conn, "update tbl_developer
								SET dev_img='$dev_img',
									dev_img_alt='$dev_img_alt',
									dev_homp_img='$dev_homp_img',
									dev_homp_img_alt='$dev_homp_img_alt',
									dev_innr_img='$dev_innr_img',
									dev_heading='$dev_heading',
									dev_short_descp='$dev_short_descp',
									dev_long_descp='$dev_long_descp',
									dev_address='$dev_address',
									dev_phoneno='$dev_phoneno',
									dev_email='$dev_email',
									choose_us_img='$choose_us_img',
									choose_us_desc='$choose_us_desc',
									dev_posted_on='$dev_posted_on',
									dev_slug='$dev_slug',
									meta_title='$meta_title',
									meta_keyword='$meta_keyword',
									meta_description='$meta_description',
									meta_tags='$meta_tags',
									dev_showonhomepage='$dev_showonhomepage',
									is_active='$is_active',
									tstp='" . $date . "' where dev_id=$id");
		if ($query) {
			$new_data = $logger->getOldRecordData('tbl_developer', $id);

			$logger->log(
				'UPDATE',
				'tbl_developer',
				$id,
				$old_data,
				$new_data,
				'Developer Profile Updated : ' . $dev_heading
			);

			header('Location:' . BASE_URL . 'manage-developer.php');
		}
	}

}

$details = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_developer where dev_id=$id"));
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
										<h3 class="card-label">Edit Developer</h3>
									</div>
									<div class="card-toolbar">
										<!--begin::Button-->
										<a href="<?= BASE_URL ?>manage-developer.php"
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


										<!--begin::Form-->
										<form action="" enctype="multipart/form-data" method="post" id="edit-developer">
											<!-- <div class="card-body"> -->

											<div class="row">
												<div class="col-md-2">
													<label for="exampleInputPassword1">Order No.
														<span class="text-danger">*</span></label>
													<input type="hidden" id="devId" value="<?= $details['dev_id'] ?>">
													<div class="input-group mb-3">

														<input type="text" name="order_no"
															value="<?= $details['dev_order_no'] ?>" class="form-control"
															placeholder="Order No." id="devOrderNoInput">
														<div class="input-group-append">
															<button class="btn btn-primary" type="button"
																id="devOrderSubmitBtn">Submit</button>
														</div>
													</div>

													<div class="orderMsgColor text-sm" id="devOrderMsg"></div>
												</div>
												<div class="col-10">
													<div class="form-group">
														<label for="exampleInputPassword1">Heading
															<span class="text-danger">*</span></label>
														<input type="text" name="dev_heading"
															value="<?= $details['dev_heading'] ?>" class="form-control"
															autocomplete="off" placeholder="Heading">
													</div>

												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Image
															<span class="text-danger">*</span></label>
														<input type="file" name="dev_img" value="" class="form-control">
														<?php if (!empty($details['dev_img'])) { ?>
															<img src="<?= BASE_URL ?>assets/media/developer/<?= $details['dev_img'] ?>"
																width='50' class="mt-3">
														<?php } ?>
														<input type="hidden" name="dev_img_hidden"
															value="<?= $details['dev_img'] ?>">
													</div>
												</div>

												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Image Alt Tag
															<span class="text-danger">*</span></label>
														<input type="text" name="dev_img_alt"
															value="<?= $details['dev_img_alt'] ?>" class="form-control"
															autocomplete="off" placeholder="Image Alt Tag">
													</div>
												</div>

												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Homepage Image
															<span class="text-danger">*</span></label>
														<input type="file" name="dev_homp_img" value=""
															class="form-control">
														<?php if (!empty($details['dev_homp_img'])) { ?>
															<img src="<?= BASE_URL ?>assets/media/developer/homepage-image/<?= $details['dev_homp_img'] ?>"
																width='50' class="mt-3">
														<?php } ?>
														<input type="hidden" name="dev_homp_img_hidden"
															value="<?= $details['dev_homp_img'] ?>">
													</div>
												</div>

												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Homepage Image Alt Tag
															<span class="text-danger">*</span></label>
														<input type="text" name="dev_homp_img_alt"
															value="<?= $details['dev_homp_img_alt'] ?>"
															class="form-control" autocomplete="off"
															placeholder="Homepage Image Alt Tag">
													</div>
												</div>
											</div>



											<div class="form-group">
												<label for="exampleInputPassword1">Inner Image
													<span class="text-danger">*</span></label>
												<input type="file" name="dev_innr_img" value="" class="form-control">
												<?php if (!empty($details['dev_innr_img'])) { ?>
													<img src="<?= BASE_URL ?>assets/media/developer/inner-image/<?= $details['dev_innr_img'] ?>"
														width='50' class="mt-3">
												<?php } ?>
												<input type="hidden" name="dev_innr_img_hidden"
													value="<?= $details['dev_innr_img'] ?>">
											</div>





											<div class="form-group">
												<label for="exampleInputPassword1">Short Description
													<span class="text-danger">*</span></label>
												<textarea name="dev_short_descp" class="form-control"
													placeholder="Short Description" aria-hidden="true"
													rows="10"><?= $details['dev_short_descp'] ?></textarea>
											</div>

											<div class="form-group">
												<label for="exampleInputPassword1">Long Description
													<span class="text-danger">*</span></label>
												<textarea name="dev_long_descp" class="form-control " id="kt-tinymce-4"
													placeholder="Long Description" aria-hidden="true"
													rows="10"><?= $details['dev_long_descp'] ?></textarea>
											</div>



											<div class="form-group">
												<label for="exampleInputPassword1">Choose Us Image
													<span class="text-danger">*</span></label>
												<input type="file" name="choose_us_img" value="" class="form-control">
												<?php if (!empty($details['choose_us_img'])) { ?>
													<img src="<?= BASE_URL ?>assets/media/developer/choose-us/<?= $details['choose_us_img'] ?>"
														width='50' class="mt-3">
												<?php } ?>
												<input type="hidden" name="choose_us_img_hidden"
													value="<?= $details['choose_us_img'] ?>">
											</div>



											<div class="form-group">
												<label for="choose_us_desc">Description</label>
												<textarea name="choose_us_desc" class="form-control"
													rows="5"><?= $details['choose_us_desc'] ?></textarea>
											</div>

											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Address</label>
														<input type="text" name="dev_address"
															value="<?= $details['dev_address'] ?>" class="form-control"
															autocomplete="off" placeholder="Address">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Phone Number</label>
														<input type="text" name="dev_phoneno"
															value="<?= $details['dev_phoneno'] ?>" class="form-control"
															autocomplete="off" placeholder="Phone Number">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Email ID</label>
														<input type="text" name="dev_email"
															value="<?= $details['dev_email'] ?>" class="form-control"
															autocomplete="off" placeholder="Email ID">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Posted On</label>
														<input type="text" name="dev_posted_on"
															value="<?= date('d-m-Y', strtotime($details['dev_posted_on'])) ?>"
															class="form-control" autocomplete="off" id="kt_datepicker_1"
															placeholder="Posted On">
													</div>
												</div>
											</div>


											<div class="form-group">
												<label for="exampleInputPassword1">Slug</label>
												<input type="text" name="dev_slug" value="<?= $details['dev_slug'] ?>"
													class="form-control" autocomplete="off" placeholder="Slug">
											</div>



											<div class="row mt-3">
												<div class="col-12">
													<h4 class="text-primary">SEO Content</h4>
													<hr class="text-primary mt-2" style="border-bottom:2px solid">
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Meta Title</label>
														<input type="text" name="meta_title"
															value="<?= $details['meta_title'] ?>" class="form-control"
															autocomplete="off" placeholder="Meta Title">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Meta Keyword</label>
														<input type="text" name="meta_keyword"
															value="<?= $details['meta_keyword'] ?>" class="form-control"
															autocomplete="off" placeholder="Meta Keyword">
													</div>
												</div>

											</div>

											<div class="form-group">
												<label for="exampleInputPassword1">Meta Tags</label>
												<textarea name="meta_tags" class="form-control" placeholder="Meta Tags"
													aria-hidden="true" rows="10"><?= $details['meta_tags'] ?></textarea>
											</div>

											<div class="form-group">
												<label for="exampleInputPassword1">Meta Description</label>
												<textarea name="meta_description" class="form-control"
													placeholder="Meta Description" aria-hidden="true"
													rows="10"><?= $details['meta_description'] ?></textarea>
											</div>

											<div class="row">
												<div class="col-md-2">
													<div class="form-group">
														<label for="exampleInputPassword1">Show On Homepage</label><br>
														<input type="checkbox" name="dev_showonhomepage" value='1'
															<?= ($details['dev_showonhomepage'] == 1 ? 'checked' : '') ?>>
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<label for="exampleInputPassword1">Status</label>
														<span class="switch switch-md switch-icon">
															<label>
																<input type="checkbox" name="is_active" value="1"
																	<?= ($details['is_active'] == 1 ? 'checked' : '') ?>>
																<span></span>
															</label>
														</span>
													</div>
												</div>
											</div>

											<!-- </div> -->
											<div class="card-footer">
												<button type="submit" name="update_developer"
													class="btn btn-primary mr-2">Update</button>
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
				<?php
				include('includes/footer-inner.php');
				?>