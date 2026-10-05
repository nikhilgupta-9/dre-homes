<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);
$date = date('Y-m-d H:i:s');

$err[] = '';
$id = base64_decode($_GET['edit']);
if (isset($_POST['update_team'])) {

	$team_cat_id = prepStr($_POST['team_cat_id']);
	$team_desg_id = prepStr($_POST['team_desg_id']);
	$team_img = 'team_img_' . rand(0, 10000) . '_' . $_FILES['team_img']['name'];
	$team_img_alt = prepStr($_POST['team_img_alt']);
	$team_inner_heading = prepStr($_POST['team_inner_heading']);
	$team_name = prepStr($_POST['team_name']);
	$team_desg = prepStr($_POST['team_desg']);
	$team_orn = prepStr($_POST['team_orn']);
	$team_cont = prepStr($_POST['team_cont']);
	$team_mobile = prepStr($_POST['team_mobile']);
	$team_whtsapp = prepStr($_POST['team_whtsapp']);
	$team_emailid = prepStr($_POST['team_emailid']);
	$team_address = prepStr($_POST['team_address']);
	$team_fblink = prepStr($_POST['team_fblink']);
	$team_twitterlink = prepStr($_POST['team_twitterlink']);
	$team_linkedinlink = prepStr($_POST['team_linkedinlink']);
	$team_instagramlink = prepStr($_POST['team_instagramlink']);
	$team_slug = prepStr($_POST['team_slug']);
	$team_pos = prepStr($_POST['team_pos']);
	$meta_title = prepStr($_POST['meta_title']);
	$meta_keyword = prepStr($_POST['meta_keyword']);
	$meta_description = prepStr($_POST['meta_description']);
	$meta_tags = prepStr($_POST['meta_tags']);
	$team_company_owner = (prepStr($_POST['team_company_owner'] != '' ? 1 : 2));
	$date = date('Y-m-d', strtotime($_POST['add_date']));
	$is_active = isset($_POST['is_active']) ? 1 : 2;

    $is_time_square_team = isset($_POST['is_time_square_team']) ? 1 : 2;


	$allowed_image_extension = array(
		"png",
		"jpg",
		"jpeg"
	);

	$team_img_file_extension = pathinfo($_FILES["team_img"]["name"], PATHINFO_EXTENSION);

	if (!in_array($team_img_file_extension, $allowed_image_extension) && !empty($_FILES['team_img']['name'])) {
		$err[] = 'Invalid Team Image. Only PNG,JPG and JPEG are allowed.';
	}



	if (!empty($_FILES['team_img']['name'])) {
		$team_img = 'team_img_' . rand(0, 10000) . '_' . $_FILES['team_img']['name'];
	} else {
		$team_img = $_POST['team_img_hidden'];
	}


	$old_data = $logger->getOldRecordData('tbl_team', $id);


	if (count($err) == 1) {

		if (!empty($_FILES['team_img']['name'])) {
			move_uploaded_file($_FILES['team_img']['tmp_name'], "assets/media/team/$team_img");
			unlink('assets/media/team/' . $_POST['team_img_hidden']);
		}





		$query = mysqli_query($conn, "update tbl_team
	                    SET   team_cat_id='$team_cat_id', 
	                          team_desg_id='$team_desg_id', 
	                          team_img='$team_img',
	                          team_img_alt='$team_img_alt',
	                          team_inner_heading='$team_inner_heading',
	                          team_name='$team_name',
	                          team_desg='$team_desg',
	                          team_orn='$team_orn',
	                          team_cont='$team_cont',
	                          team_mobile='$team_mobile',
	                          team_whtsapp='$team_whtsapp',
	                          team_emailid='$team_emailid',
	                          team_address='$team_address',
	                          team_fblink='$team_fblink',
	                          team_twitterlink='$team_twitterlink',
	                          team_linkedinlink='$team_linkedinlink',
	                          team_instagramlink='$team_instagramlink',
	                          team_slug='$team_slug',
	                          team_pos='$team_pos',
							  meta_title='$meta_title',
							  meta_keyword='$meta_keyword',
							  meta_description='$meta_description',
							  meta_tags='$meta_tags',
							  team_company_owner='$team_company_owner',
							  is_time_square_team='$is_time_square_team',
	                          is_active='$is_active',
	                         tstp=' $date ' where team_id=$id");
		if ($query) {

			$new_data = $logger->getOldRecordData('tbl_team', $id);

			$logger->log(
				'UPDATE',
				'tbl_team',
				$id,
				$old_data,
				$new_data,
				'Update Team Detail'
			);

			header('Location:' . BASE_URL . 'manage-team.php');
		}
		if(!$query){
    die(mysqli_error($conn));
}
	}
}

$details = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_team where team_id=$id"));
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
										<h3 class="card-label">Edit Team</h3>
									</div>
									<div class="card-toolbar">
										<!--begin::Button-->
										<a href="<?= BASE_URL ?>manage-team.php"
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

										<form action="" enctype="multipart/form-data" method="post" id="edit-team">
											<div class="row">
												<!-- Category -->
												<div class="col-md-5 mb-3">
													<label for="team_cat_id" class="form-label">Category</label>
													<select name="team_cat_id" id="team_cat_id" class="form-control">
														<option value="">Select Category</option>
														<?php
														$query = mysqli_query($conn, "SELECT * FROM tbl_team_cat WHERE is_active=1");
														while ($row = mysqli_fetch_array($query)) {
															?>
															<option value="<?= htmlspecialchars($row['team_cat_id']) ?>"
																<?= ($details['team_cat_id'] == $row['team_cat_id'] ? 'selected' : '') ?>>
																<?= htmlspecialchars(ucwords($row['team_cat_name'])) ?>
															</option>
														<?php } ?>
													</select>
												</div>

												<!-- Designation -->
												<div class="col-md-5 mb-3">
													<label for="team_desg_id" class="form-label">Designation</label>
													<select name="team_desg_id" id="team_desg_id" class="form-control">
														<option value="">Select Designation</option>
														<?php
														$query = mysqli_query($conn, "SELECT * FROM tbl_team_desg WHERE is_active=1");
														while ($row = mysqli_fetch_array($query)) {
															?>
															<option value="<?= htmlspecialchars($row['team_desg_id']) ?>"
																<?= ($details['team_desg_id'] == $row['team_desg_id'] ? 'selected' : '') ?>>
																<?= htmlspecialchars(ucwords($row['team_desg_name'])) ?>
															</option>
														<?php } ?>
													</select>
												</div>

												<!-- Is Time Square Team - CORRECTED VERSION -->
												<div class="col-md-2">
													<label for="townSquareTeam">Is Time Square Team</label><br>
													<input type="checkbox" name="is_time_square_team" value="1"
														id="townSquareTeam" <?= (isset($details['is_time_square_team']) && $details['is_time_square_team'] == 1 ? 'checked' : '') ?>>
												</div>
												<!-- Image -->
												<div class="col-md-6 mb-3">
													<label for="team_img" class="form-label">Image <span
															class="text-danger">*</span></label>
													<input type="file" id="team_img" name="team_img" value=""
														class="form-control">
													<?php if (!empty($details['team_img'])) { ?>
														<img src="<?= BASE_URL ?>assets/media/team/<?= $details['team_img'] ?>"
															width="50" class="mt-2">
													<?php } ?>
													<input type="hidden" name="team_img_hidden"
														value="<?= $details['team_img'] ?>">
												</div>

												<!-- Image Alt Tag -->
												<div class="col-md-6 mb-3">
													<label for="team_img_alt" class="form-label">Image Alt Tag</label>
													<input type="text" name="team_img_alt" id="team_img_alt"
														value="<?= $details['team_img_alt'] ?>" class="form-control"
														autocomplete="off" placeholder="Image Alt Tag">
												</div>

												<!-- Inner Heading -->
												<div class="col-md-6 mb-3">
													<label for="team_inner_heading" class="form-label">Inner
														Heading</label>
													<input type="text" name="team_inner_heading" id="team_inner_heading"
														value="<?= $details['team_inner_heading'] ?>"
														class="form-control" autocomplete="off"
														placeholder="Inner Heading">
												</div>

												<!-- Name -->
												<div class="col-md-6 mb-3">
													<label for="team_name" class="form-label">Name <span
															class="text-danger">*</span></label>
													<input type="text" name="team_name" id="team_name"
														value="<?= $details['team_name'] ?>" class="form-control"
														autocomplete="off" placeholder="Name">
												</div>

												<!-- Designation -->
												<div class="col-md-6 mb-3">
													<label for="team_desg" class="form-label">Designation</label>
													<input type="text" name="team_desg" id="team_desg"
														value="<?= $details['team_desg'] ?>" class="form-control"
														autocomplete="off" placeholder="Designation">
												</div>

												<!-- ORN Number -->
												<div class="col-md-6 mb-3">
													<label for="team_orn" class="form-label">ORN Number</label>
													<input type="text" name="team_orn" id="team_orn"
														value="<?= $details['team_orn'] ?>" class="form-control"
														autocomplete="off" placeholder="ORN Number">
												</div>

												<!-- Content -->
												<div class="col-12 mb-3">
													<label for="kt-tinymce-2" class="form-label">Content</label>
													<textarea name="team_cont" id="kt-tinymce-2" class="form-control"
														rows="4"><?= $details['team_cont'] ?></textarea>
												</div>

												<!-- Mobile -->
												<div class="col-md-6 mb-3">
													<label for="team_mobile" class="form-label">Mobile</label>
													<input type="text" name="team_mobile" id="team_mobile"
														value="<?= $details['team_mobile'] ?>" class="form-control"
														autocomplete="off" placeholder="Mobile">
												</div>

												<!-- Whatsapp Number -->
												<div class="col-md-6 mb-3">
													<label for="team_whtsapp" class="form-label">Whatsapp Number</label>
													<input type="text" name="team_whtsapp" id="team_whtsapp"
														value="<?= $details['team_whtsapp'] ?>" class="form-control"
														autocomplete="off" placeholder="Whatsapp Number">
												</div>

												<!-- Email ID -->
												<div class="col-md-6 mb-3">
													<label for="team_emailid" class="form-label">Email ID</label>
													<input type="email" name="team_emailid" id="team_emailid"
														value="<?= $details['team_emailid'] ?>" class="form-control"
														autocomplete="off" placeholder="Email ID">
												</div>

												<!-- Address -->
												<div class="col-md-6 mb-3">
													<label for="team_address" class="form-label">Address</label>
													<input type="text" name="team_address" id="team_address"
														value="<?= $details['team_address'] ?>" class="form-control"
														autocomplete="off" placeholder="Address">
												</div>

												<!-- Facebook Link -->
												<div class="col-md-4 mb-3">
													<label for="team_fblink" class="form-label">Facebook Link</label>
													<input type="url" name="team_fblink" id="team_fblink"
														value="<?= $details['team_fblink'] ?>" class="form-control"
														autocomplete="off" placeholder="Facebook Link">
												</div>

												<!-- Twitter Link -->
												<div class="col-md-4 mb-3">
													<label for="team_twitterlink" class="form-label">Twitter
														Link</label>
													<input type="url" name="team_twitterlink" id="team_twitterlink"
														value="<?= $details['team_twitterlink'] ?>" class="form-control"
														autocomplete="off" placeholder="Twitter Link">
												</div>

												<!-- Linkedin Link -->
												<div class="col-md-4 mb-3">
													<label for="team_linkedinlink" class="form-label">Linkedin
														Link</label>
													<input type="url" name="team_linkedinlink" id="team_linkedinlink"
														value="<?= $details['team_linkedinlink'] ?>"
														class="form-control" autocomplete="off"
														placeholder="Linkedin Link">
												</div>

												<!-- Instagram Link -->
												<div class="col-md-6 mb-3">
													<label for="team_instagramlink" class="form-label">Instagram
														Link</label>
													<input type="url" name="team_instagramlink" id="team_instagramlink"
														value="<?= $details['team_instagramlink'] ?>"
														class="form-control" autocomplete="off"
														placeholder="Instagram Link">
												</div>

												<!-- Slug -->
												<div class="col-md-6 mb-3">
													<label for="team_slug" class="form-label">Slug</label>
													<input type="text" name="team_slug" id="team_slug"
														value="<?= $details['team_slug'] ?>" class="form-control"
														autocomplete="off" placeholder="Slug">
												</div>

												<!-- Position -->
												<div class="col-md-6 mb-3">
													<label for="team_pos" class="form-label">Position</label>
													<input type="number" name="team_pos" id="team_pos"
														value="<?= $details['team_pos'] ?>" class="form-control"
														autocomplete="off" placeholder="Position">
												</div>

												<!-- Meta Title -->
												<div class="col-md-12 mb-3">
													<label for="meta_title" class="form-label">Meta Title</label>
													<input type="text" name="meta_title" id="meta_title"
														value="<?= $details['meta_title'] ?>" class="form-control"
														autocomplete="off" placeholder="Meta Title">
												</div>

												<!-- Meta Keyword -->
												<div class="col-md-12 mb-3">
													<label for="meta_keyword" class="form-label">Meta Keyword</label>
													<input type="text" name="meta_keyword" id="meta_keyword"
														value="<?= $details['meta_keyword'] ?>" class="form-control"
														autocomplete="off" placeholder="Meta Keyword">
												</div>

												<!-- Meta Description -->
												<div class="col-md-12 mb-3">
													<label for="meta_description" class="form-label">Meta
														Description</label>
													<textarea name="meta_description" id="meta_description"
														class="form-control" placeholder="Meta Description"
														rows="4"><?= $details['meta_description'] ?></textarea>
												</div>

												<!-- Meta Tags -->
												<div class="col-md-12 mb-3">
													<label for="meta_tags" class="form-label">Meta Tags</label>
													<textarea name="meta_tags" id="meta_tags " class="form-control"
														placeholder="Meta Tags"
														rows="4"><?= $details['meta_tags'] ?></textarea>
												</div>

												<div class="row">
													<div class="col-md-6">
														<!-- Company Owner -->
														<div class="col-md-12 mb-3">
															<div class="form-check">
																<input type="checkbox" name="team_company_owner"
																	value="1" id="team_company_owner"
																	class="form-check-input"
																	<?= ($details['team_company_owner'] == 1 ? 'checked' : '') ?>>
																<label for="team_company_owner"
																	class="form-check-label">Company Owner</label>
															</div>
														</div>

														<!-- Status -->
														<div class="col-md-12 mb-3">
															<label class="font-weight-bold">Status</label>
															<div class="mt-2">
																<span class="switch switch-md switch-icon">
																	<label>
																		<input type="checkbox" name="is_active"
																			value="1" <?= ($details['is_active'] == 1 ? 'checked' : '') ?>>
																		<span></span>
																	</label>
																</span>
															</div>
															<small class="form-text text-muted">Toggle to
																activate/inactivate the team member</small>
														</div>
													</div>
													<div class="col-md-6">
														<label for="add_date" class="form-label">Date Added</label>

														<input type="date" name="add_date" id=""
															value="<?= date('Y-m-d', strtotime($details['tstp'])) ?>"
															class="form-control">
													</div>
												</div>

											</div>

											<!-- Buttons -->
											<div class="d-flex justify-content-start gap-2 mt-3">
												<button type="submit" name="update_team"
													class="btn btn-primary mr-3">Update</button>
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
				<?php
				include('includes/footer-inner.php');
				?>