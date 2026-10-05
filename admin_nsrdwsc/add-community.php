<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);

if (isset($_POST['add_community'])) {

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
	$comm_featured = (prepStr($_POST['comm_featured'] != '' ? $_POST['comm_featured'] : 2));
	$comm_loc_heading = $_POST['comm_loc_heading'];
	$comm_loc_description = $_POST['comm_loc_description'];
	$comm_map_fa_icon = $_POST['comm_map_fa_icon'];
	$comm_map_heading = $_POST['comm_map_heading'];
	$comm_map_distance = $_POST['comm_map_distance'];



	$is_active = (prepStr($_POST['is_active'] != '' ? 1 : 2));

	$query = mysqli_query($conn, "insert into tbl_community
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
									comm_featured='$comm_featured',
									is_active='$is_active',
									tstp='" . $date . "'");

	$comm_id = mysqli_insert_id($conn);



	/*insert into tbl_community_loc start*/

	$count_loc_heading = count($comm_loc_heading);

	for ($i = 0; $i < $count_loc_heading; $i++) {

		$comm_loc_headings = mysqli_real_escape_string($conn, $comm_loc_heading[$i]);
		$comm_loc_descriptions = mysqli_real_escape_string($conn, $comm_loc_description[$i]);


		if (!empty($comm_loc_headings)):

			$query = mysqli_query($conn, "insert into tbl_community_loc
                                                        SET comm_id='$comm_id',
                                                        comm_loc_heading='$comm_loc_headings',
                                                        comm_loc_description='$comm_loc_descriptions',
                                                        is_active='1',
                                                        tstp='$date'
                                                        ");


		endif;

	}

	/*insert into tbl_community_loc end*/


	/*insert into tbl_community_map start*/

	$count_map_fa_icon = count($comm_map_fa_icon);

	for ($i = 0; $i < $count_map_fa_icon; $i++) {

		$comm_map_fa_icons = mysqli_real_escape_string($conn, $comm_map_fa_icon[$i]);
		$comm_map_headings = mysqli_real_escape_string($conn, $comm_map_heading[$i]);
		$comm_map_distances = mysqli_real_escape_string($conn, $comm_map_distance[$i]);


		if (!empty($comm_map_fa_icons)):

			$query = mysqli_query($conn, "insert into tbl_community_map
                                                        SET comm_id='$comm_id',
                                                        comm_map_fa_icon='$comm_map_fa_icons',
                                                        comm_map_heading='$comm_map_headings',
                                                        comm_map_distance='$comm_map_distances',
                                                        is_active='1',
                                                        tstp='$date'
                                                        ");


		endif;

	}

	/*insert into tbl_community_map end*/
	if ($query) {
		move_uploaded_file($_FILES['comm_thumb_img']['tmp_name'], "assets/media/community/thumb/$comm_thumb_img");
		move_uploaded_file($_FILES['comm_inner_img']['tmp_name'], "assets/media/community/inner/$comm_inner_img");
		move_uploaded_file($_FILES['comm_inner_thumb_img']['tmp_name'], "assets/media/community/inner-thumb/$comm_inner_thumb_img");
		move_uploaded_file($_FILES['comm_loc_main_img']['tmp_name'], "assets/media/community/location/$comm_loc_main_img");

		$id = mysqli_insert_id($conn);
		$new_data = $logger->getOldRecordData('tbl_community', $id);

		$logger->log(
			'INSERT',
			'tbl_community',
			$id,
			null,
			$new_data,
			'New Community added: ' . $comm_heading
		);

		header('Location:' . BASE_URL . 'manage-community.php');
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
										<h3 class="card-label">Add Community</h3>
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


									<div class=" card-custom gutter-b example example-compact">


										<!--begin::Form-->
										<form action="" enctype="multipart/form-data" method="post" id="add-community">
											<!-- <div class="card-body"> -->

											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Heading
															<span class="text-danger">*</span></label>
														<input type="text" name="comm_heading" value=""
															class="form-control" autocomplete="off"
															placeholder="Heading">
													</div>
												</div>

												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Location
															<span class="text-danger">*</span></label>
														<input type="text" name="comm_location" value=""
															class="form-control" autocomplete="off"
															placeholder="Location">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Thumb Image
															<span class="text-danger">*</span></label>
														<input type="file" name="comm_thumb_img" value=""
															class="form-control">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Thumb Image Alt Tag
															<span class="text-danger">*</span></label>
														<input type="text" name="comm_thumb_img_alt" value=""
															class="form-control" autocomplete="off"
															placeholder="Thumb Image Alt Tag">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="exampleInputPassword1">Inner Image
															<span class="text-danger">*</span></label>
														<input type="file" name="comm_inner_img" value=""
															class="form-control">
													</div>

												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="exampleInputPassword1">Inner Thumb Image
															<span class="text-danger">*</span></label>
														<input type="file" name="comm_inner_thumb_img" value=""
															class="form-control">
													</div>

												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="exampleInputPassword1">Inner Thumb Image Alt Tag
															<span class="text-danger">*</span></label>
														<input type="text" name="comm_inner_thumb_img_alt" value=""
															class="form-control" autocomplete="off"
															placeholder="Inner Thumb Image Alt Tag">
													</div>
												</div>
											</div>

											<div class="form-group">
												<label for="exampleInputPassword1">Long Description
													<span class="text-danger">*</span></label>
												<textarea name="comm_long_descp" class="form-control " id="kt-tinymce-4"
													placeholder="Long Description" aria-hidden="true"
													rows="10"><?= $details['comm_long_descp'] ?></textarea>
											</div>

											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Address</label>
														<input type="text" name="comm_address" value=""
															class="form-control" autocomplete="off"
															placeholder="Address">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Phone Number</label>
														<input type="text" name="comm_phoneno" value=""
															class="form-control" autocomplete="off"
															placeholder="Phone Number">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Email ID</label>
														<input type="text" name="comm_email" value=""
															class="form-control" autocomplete="off"
															placeholder="Email ID">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Location Heading</label>
														<input type="text" name="comm_loc_main_heading" value=""
															class="form-control" autocomplete="off"
															placeholder="Location Heading">
													</div>
												</div>
											</div>









											<div class="form-group">
												<label for="exampleInputPassword1">Location Description
													<span class="text-danger">*</span></label>
												<textarea name="comm_loc_main_descp" class="form-control "
													id="kt-tinymce-5" placeholder="Location Description"
													aria-hidden="true"
													rows="10"><?= $details['comm_loc_main_heading'] ?></textarea>
											</div>


											<div class="form-group">
												<label for="exampleInputPassword1">Location Image
													<span class="text-danger">*</span></label>
												<input type="file" name="comm_loc_main_img" value=""
													class="form-control">
											</div>

											<div class="form-group">
												<h3>Location Content</h3>
												<hr>


												<div class="row comm_loc">
													<div class="col-md-4">
														<input type="text" name="comm_loc_heading[]"
															class="form-control" placeholder="Heading">
													</div>

													<div class="col-md-4 form-group">
														<textarea name="comm_loc_description[]"
															placeholder="Description" class="form-control"></textarea>
													</div>


													<div class="col-md-4">
														<a href="" class="btn btn-primary add_more_comm_loc">Add
															more <i class="mdi  mdi-plus-circle-outline"></i></a>
													</div>

												</div>

											</div>



											<div class="form-group">
												<label for="exampleInputPassword1">Maps Heading</label>
												<input type="text" name="comm_map_header" value="" class="form-control"
													autocomplete="off" placeholder="Maps Heading">
											</div>


											<div class="form-group">
												<label for="exampleInputPassword1">Maps Iframe</label>
												<input type="text" name="comm_map_iframe" value="" class="form-control"
													autocomplete="off" placeholder="Maps Iframe">
											</div>

											<div class="form-group">
												<h3>Maps Content</h3>
												<hr>


												<div class="row comm_maps">
													<div class="col-md-3">
														<input type="text" name="comm_map_fa_icon[]"
															class="form-control" placeholder="Fa Icon">
													</div>

													<div class="col-md-3">
														<input type="text" name="comm_map_heading[]"
															class="form-control" placeholder="Heading">
													</div>

													<div class="col-md-3">
														<input type="text" name="comm_map_distance[]"
															class="form-control" placeholder="Distance">
													</div>


													<div class="col-md-3">
														<a href="" class="btn btn-primary add_more_comm_map">Add
															more <i class="mdi  mdi-plus-circle-outline"></i></a>
													</div>

												</div>

											</div>



											<div class="form-group">
												<label for="exampleInputPassword1">Posted On</label>
												<input type="text" name="comm_posted_on" value="" class="form-control"
													autocomplete="off" id="kt_datepicker_1" placeholder="Posted On">
											</div>

											<div class="form-group">
												<label for="exampleInputPassword1">Slug</label>
												<input type="text" name="comm_slug" value="" class="form-control"
													autocomplete="off" placeholder="Slug">
											</div>

											<h3 class="mb-2">SEO Content</h3>
											<hr>

											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Meta Title</label>
														<input type="text" name="meta_title" value=""
															class="form-control" autocomplete="off"
															placeholder="Meta Title">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="exampleInputPassword1">Meta Keyword</label>
														<input type="text" name="meta_keyword" value=""
															class="form-control" autocomplete="off"
															placeholder="Meta Keyword">
													</div>
												</div>
											</div>

											<div class="form-group">
												<label for="exampleInputPassword1">Meta Description</label>
												<textarea name="meta_description" class="form-control"
													placeholder="Meta Description" aria-hidden="true"
													rows="10"></textarea>
											</div>

											<div class="form-group">
												<label for="exampleInputPassword1">Meta Tags</label>
												<textarea name="meta_tags" class="form-control" placeholder="Meta Tags"
													aria-hidden="true" rows="10"></textarea>
											</div>

											<div class="row">
												<div class="col-md-2">
													<div class="form-group">
														<label for="exampleInputPassword1">Show On Homepage</label><br>
														<input type="checkbox" name="comm_showonhomepage" value='1'>
													</div>
												</div>
												<div class="col-md-2">

													<div class="form-group">
														<label for="exampleInputPassword1">Mark as Featured</label><br>
														<input type="checkbox" name="comm_featured" value='1'>
													</div>
												</div>
												<div class="col-md-2">
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
											</div>


											<!-- </div> -->
											<div class="card-footer">
												<button type="submit" name="add_community"
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
				<?php
				include('includes/footer-inner.php');
				?>