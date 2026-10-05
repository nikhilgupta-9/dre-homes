<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);

$err[] = '';
$id = base64_decode($_GET['edit']);
if (isset($_POST['update_header_info'])) {

	$header_logo = 'header_logo_' . rand(0, 10000) . '_' . $_FILES['header_logo']['name'];
	$favicon_logo = 'favicon_logo_' . rand(0, 10000) . '_' . $_FILES['favicon_logo']['name'];
	$greate_place_logo = 'greate_place_logo_' . rand(0, 10000) . '_' . $_FILES['greate_place_logo']['name'];
	$header_logo_alt = prepStr($_POST['header_logo_alt']);
	$header_contact_number = prepStr($_POST['header_contact_number']);
	$header_email_id = prepStr($_POST['header_email_id']);
	$header_fb_link = prepStr($_POST['header_fb_link']);
	$header_twitter_link = prepStr($_POST['header_twitter_link']);
	$header_linkedin_link = prepStr($_POST['header_linkedin_link']);
	$header_youtube_link = prepStr($_POST['header_youtube_link']);
	$header_spotify_link = prepStr($_POST['header_spotify_link']);
	$header_instagram_link = prepStr($_POST['header_instagram_link']);
	$is_active = (prepStr($_POST['is_active'] != '' ? 1 : 2));



	$allowed_image_extension = array(
		"png",
		"jpg",
		"jpeg",
		"ico"
	);

	$comm_file_extension = pathinfo($_FILES["header_logo"]["name"], PATHINFO_EXTENSION);
	$comm_file_extension2 = pathinfo($_FILES["favicon_logo"]["name"], PATHINFO_EXTENSION);
	$comm_file_extension3 = pathinfo($_FILES["greate_place_logo"]["name"], PATHINFO_EXTENSION);


	if (!in_array($comm_file_extension, $allowed_image_extension) && !empty($_FILES['header_logo']['name'])) {
		$err[] = 'Invalid Logo Image. Only PNG,JPG and JPEG are allowed.';
	}

	if (!in_array($comm_file_extension2, $allowed_image_extension) && !empty($_FILES['favicon_logo']['name'])) {
		$err[] = 'Invalid FavIcon Image. Only PNG,JPG,JPEG and ICO are allowed.';
	}

	if (!in_array($comm_file_extension3, $allowed_image_extension) && !empty($_FILES['greate_place_logo']['name'])) {
		$err[] = 'Invalid Greate Place Work Image. Only PNG,JPG,JPEG and ICO are allowed.';
	}



	if (!empty($_FILES['header_logo']['name'])) {
		$header_logo = 'header_logo_' . rand(0, 10000) . '_' . $_FILES['header_logo']['name'];
	} else {
		$header_logo = $_POST['header_logo_hidden'];
	}

	if (!empty($_FILES['favicon_logo']['name'])) {
		$favicon_logo = 'favicon_logo_' . rand(0, 10000) . '_' . $_FILES['favicon_logo']['name'];
	} else {
		$favicon_logo = $_POST['favicon_logo_hidden'];
	}

	if (!empty($_FILES['greate_place_logo']['name'])) {
		$greate_place_logo = 'greate_place_logo_' . rand(0, 10000) . '_' . $_FILES['greate_place_logo']['name'];
	} else {
		$greate_place_logo = $_POST['greate_place_logo_hidden'];
	}

	if (count($err) == 1) {

		if (!empty($_FILES['header_logo']['name'])) {
			move_uploaded_file($_FILES['header_logo']['tmp_name'], "assets/media/header/$header_logo");
			unlink('assets/media/header/' . $_POST['header_logo_hidden']);
		}


		if (!empty($_FILES['favicon_logo']['name'])) {
			move_uploaded_file($_FILES['favicon_logo']['tmp_name'], "assets/media/header/$favicon_logo");
			unlink('assets/media/header/' . $_POST['favicon_logo_hidden']);
		}

		if (!empty($_FILES['greate_place_logo']['name'])) {
			move_uploaded_file($_FILES['greate_place_logo']['tmp_name'], "assets/media/header/$greate_place_logo");
			unlink('assets/media/header/' . $_POST['fgreate_place_logo_hidden']);
		}

		$old_data = $logger->getOldRecordData('tbl_header_info', $id);

		$query = mysqli_query($conn, "update tbl_header_info
	                    SET header_logo='$header_logo',
	                        header_logo_alt='$header_logo_alt',
	                        favicon_logo='$favicon_logo',
	                        greate_place_logo='$greate_place_logo',
							header_contact_number='$header_contact_number',
							header_email_id='$header_email_id',
							header_fb_link='$header_fb_link',
							header_twitter_link='$header_twitter_link',
							header_linkedin_link='$header_linkedin_link',
							header_youtube_link='$header_youtube_link',
							header_spotify_link='$header_spotify_link',
							header_instagram_link='$header_instagram_link',
	                        is_active='$is_active',
	                        tstp='" . $date . "' where header_info_id=$id");
		if ($query) {
			// header('Location:' . BASE_URL . 'edit-header-info.php?edit=' . $_GET['edit'] . '');

			$new_data = $logger->getOldRecordData('tbl_header_info', $id);

			$logger->log(
				'UPDATE',
				'tbl_header_info',
				$id,
				$old_data,
				$new_data,
				'Header info updated!'
			);

			$_SESSION['header_info_success_msg'] = "Header Info updated successfully!";
			header('Location: edit-header-info.php?edit=' . base64_encode($id));
			exit;
		} else {
			// Pro Tip: Live platform par direct mysqli_error() user ko mat dikhao (Security Risk)
			$_SESSION['header_info_error_msg'] = "<strong>Error!</strong> Something went wrong while updating the Header Info.<br><b>" . mysqli_error($conn) . "</b>";
			header('Location: edit-header-info.php?edit=' . base64_encode($id));
			exit;
		}
		if (!$query) {
			die("MySQL Error: " . mysqli_error($conn));
		}

	}

}

$details = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_header_info where header_info_id=$id"));
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

					<?php if (isset($_SESSION['header_info_success_msg'])): ?>
						<div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert"
							style="border-left: 4px solid #28a745 !important;">
							<div class="d-flex align-items-center">
								<span class="alert-icon mr-3" style="font-size: 1.2 rem;"> <b>Header Info ✔</b> </span>
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
							<div class="card card-custom gutter-b shadow-sm">
								<div class="card-header flex-wrap py-3 bg-light">
									<div class="card-title">
										<h3 class="card-label font-weight-bold text-dark">
											<i class="fas fa-header mr-2 text-primary"></i>Header Information
										</h3>
									</div>
									<div class="card-toolbar">
										<span class="badge badge-pill badge-primary py-2 px-3">required *</span>
									</div>
								</div>

								<div class="card-body">

									<!-- Error display (if any) -->
									<?php if (isset($err) && count($err) > 1) { ?>
										<div class="alert alert-danger alert-dismissible fade show" role="alert">
											<strong><i class="fas fa-exclamation-circle mr-2"></i>Please fix the following
												errors:</strong>
											<ul class="mb-0 mt-2 pl-3">
												<?php foreach ($err as $list_err) { ?>
													<li><?= $list_err ?></li>
												<?php } ?>
											</ul>
											<button type="button" class="close" data-dismiss="alert" aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
									<?php } ?>

									<!--begin::Form-->
									<form action="" enctype="multipart/form-data" method="post"
										id="edit-community-content">

										<!-- ===== SECTION: LOGO & FAVICON ===== -->
										<div class="bg-light p-4 rounded mb-4">
											<h6 class="font-weight-bold text-primary mb-3"><i
													class="fas fa-image mr-2"></i>Logo & Favicon</h6>
											<div class="form-row">
												<div class="form-group col-md-4">
													<label for="header_logo" class="font-weight-bold">Logo <span
															class="text-danger">*</span></label>
													<div class="custom-file">
														<input type="file" class="custom-file-input" id="header_logo"
															name="header_logo">
														<label class="custom-file-label" for="header_logo">Choose
															file</label>
													</div>
													<?php if (!empty($details['header_logo'])) { ?>
														<div class=" mt-3 d-flex align-items-center">
															<img src="<?= BASE_URL ?>assets/media/header/<?= $details['header_logo'] ?>"
																width="100" class="img-thumbnail border"
																style="background:#D6BDAB;" alt="Logo">
															<span class="ml-2 text-muted small">current</span>
														</div>
													<?php } ?>
													<input type="hidden" name="header_logo_hidden"
														value="<?= $details['header_logo'] ?>">
												</div>

												<div class="form-group col-md-4">
													<label for="favicon_logo" class="font-weight-bold">Favicon <span
															class="text-danger">*</span></label>
													<div class="custom-file">
														<input type="file" class="custom-file-input" id="favicon_logo"
															name="favicon_logo">
														<label class="custom-file-label" for="favicon_logo">Choose
															file</label>
													</div>
													<?php if (!empty($details['favicon_logo'])) { ?>
														<div class="mt-3 d-flex align-items-center">
															<img src="<?= BASE_URL ?>assets/media/header/<?= $details['favicon_logo'] ?>"
																width="70" class="img-thumbnail border p-1" alt="Favicon">
															<span class="ml-2 text-muted small">current</span>
														</div>
													<?php } ?>
													<input type="hidden" name="favicon_logo_hidden"
														value="<?= $details['favicon_logo'] ?>">
												</div>

												<div class="form-group col-md-4">
													<label for="greate_place_logo" class="font-weight-bold">Great Place
														For Work Logo</label>
													<div class="custom-file">
														<input type="file" class="custom-file-input"
															id="greate_place_logo" name="greate_place_logo">
														<label class="custom-file-label" for="greate_place_logo">Choose
															file</label>
													</div>

													<?php if (!empty($details['greate_place_logo'])) { ?>
														<div class="mt-3 d-flex align-items-center justify-content-between"
															id="greate_place_logo_container">
															<div class="d-flex align-items-center">
																<img src="<?= BASE_URL ?>assets/media/header/<?= $details['greate_place_logo'] ?>"
																	width="70" class="img-thumbnail border p-1"
																	alt="Great Place Work Logo">
																<span class="ml-2 text-muted small">current</span>
															</div>
															<button type="button" class="btn btn-danger btn-sm ml-2"
																onclick="removeImageAjax('greate_place_logo', <?= $details['header_info_id'] ?>, <?= $id ?>)">
																<i class="fas fa-trash-alt"></i> Remove
															</button>
														</div>
													<?php } ?>

													<input type="hidden" name="greate_place_logo_hidden"
														value="<?= $details['greate_place_logo'] ?>">
													<input type="hidden" name="remove_greate_place_logo"
														id="remove_greate_place_logo" value="0">

													
												</div>
											</div>
										</div>

										<!-- ===== SECTION: ALT TAG & CONTACT ===== -->
										<div class="p-4 rounded mb-4">
											<h6 class="font-weight-bold text-primary mb-3"><i
													class="fas fa-address-card mr-2"></i>Brand & Contact</h6>
											<div class="form-row">
												<div class="form-group col-md-6">
													<label for="header_logo_alt" class="font-weight-bold">Logo Alt
														Tag</label>
													<input type="text" id="header_logo_alt" name="header_logo_alt"
														value="<?= $details['header_logo_alt'] ?>" class="form-control"
														placeholder="e.g. Company Brand Name">
												</div>

												<div class="form-group col-md-6">
													<label for="header_contact_number" class="font-weight-bold">Contact
														Number</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text"><i
																	class="fas fa-phone-alt"></i></span>
														</div>
														<input type="text" id="header_contact_number"
															name="header_contact_number"
															value="<?= $details['header_contact_number'] ?>"
															class="form-control" placeholder="+1 234 567 890">
													</div>
												</div>
											</div>

											<div class="form-row">
												<div class="form-group col-md-6">
													<label for="header_email_id" class="font-weight-bold">Email
														ID</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text"><i
																	class="fas fa-envelope"></i></span>
														</div>
														<input type="text" id="header_email_id" name="header_email_id"
															value="<?= $details['header_email_id'] ?>"
															class="form-control" placeholder="info@domain.com">
													</div>
												</div>

												<div class="form-group col-md-6">
													<label for="header_fb_link" class="font-weight-bold">Facebook
														Link</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text text-primary"><i
																	class="fab fa-facebook-f"></i></span>
														</div>
														<input type="text" id="header_fb_link" name="header_fb_link"
															value="<?= $details['header_fb_link'] ?>"
															class="form-control" placeholder="https://facebook.com/...">
													</div>
												</div>
											</div>
										</div>

										<!-- ===== SECTION: SOCIAL MEDIA ===== -->
										<div class="p-4 rounded mb-4">
											<h6 class="font-weight-bold text-primary mb-3"><i
													class="fas fa-share-alt mr-2"></i>Social Media Links</h6>
											<div class="form-row">
												<div class="form-group col-md-6">
													<label for="header_twitter_link" class="font-weight-bold">Twitter
														Link</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text text-info"><i
																	class="fab fa-twitter"></i></span>
														</div>
														<input type="text" id="header_twitter_link"
															name="header_twitter_link"
															value="<?= $details['header_twitter_link'] ?>"
															class="form-control" placeholder="https://twitter.com/...">
													</div>
												</div>

												<div class="form-group col-md-6">
													<label for="header_linkedin_link" class="font-weight-bold">LinkedIn
														Link</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text text-primary"><i
																	class="fab fa-linkedin-in"></i></span>
														</div>
														<input type="text" id="header_linkedin_link"
															name="header_linkedin_link"
															value="<?= $details['header_linkedin_link'] ?>"
															class="form-control" placeholder="https://linkedin.com/...">
													</div>
												</div>
											</div>

											<div class="form-row">
												<div class="form-group col-md-6">
													<label for="header_youtube_link" class="font-weight-bold">YouTube
														Link</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text text-danger"><i
																	class="fab fa-youtube"></i></span>
														</div>
														<input type="text" id="header_youtube_link"
															name="header_youtube_link"
															value="<?= $details['header_youtube_link'] ?>"
															class="form-control" placeholder="https://youtube.com/...">
													</div>
												</div>

												<div class="form-group col-md-6">
													<label for="header_spotify_link" class="font-weight-bold">Spotify
														Link</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text text-success"><i
																	class="fab fa-spotify"></i></span>
														</div>
														<input type="text" id="header_spotify_link"
															name="header_spotify_link"
															value="<?= $details['header_spotify_link'] ?>"
															class="form-control" placeholder="https://spotify.com/...">
													</div>
												</div>
											</div>

											<div class="form-row">
												<div class="form-group col-md-6">
													<label for="header_instagram_link"
														class="font-weight-bold">Instagram Link</label>
													<div class="input-group">
														<div class="input-group-prepend">
															<span class="input-group-text text-danger"><i
																	class="fab fa-instagram"></i></span>
														</div>
														<input type="text" id="header_instagram_link"
															name="header_instagram_link"
															value="<?= $details['header_instagram_link'] ?>"
															class="form-control"
															placeholder="https://instagram.com/...">
													</div>
												</div>

												<!-- ===== STATUS ===== -->
												<div class="form-group col-md-6">
													<label class="font-weight-bold d-block">Status</label>
													<div class="d-flex align-items-center mt-2">
														<span class="switch switch-md switch-icon mr-3">
															<label>
																<input type="checkbox" name="is_active" value="1"
																	<?= ($details['is_active'] == 1 ? 'checked' : '') ?>>
																<span></span>
															</label>
														</span>
														<span class="text-muted small">Active / Inactive</span>
													</div>
												</div>
											</div>
										</div>

										<!-- ===== FORM ACTIONS ===== -->
										<div
											class="card-footer  border-top px-0 d-flex justify-content-between align-items-center flex-wrap">
											<button type="submit" name="update_header_info"
												class="btn btn-primary px-5">
												<i class="fas fa-save mr-2"></i>Update
											</button>
											<button type="reset" class="btn btn-outline-secondary px-4">
												<i class="fas fa-undo-alt mr-2"></i>Reset
											</button>
										</div>

									</form>
									<!--end::Form-->

								</div>
								<!--end::Card-body-->
							</div>
							<!--end::Card-->

						</div>
						<!--end::Container-->


						<!-- Bootstrap 4 custom-file JS for better file input display -->
						<script>
							// Auto-update file input label with selected filename
							document.querySelectorAll('.custom-file-input').forEach(function (input) {
								input.addEventListener('change', function (e) {
									var fileName = e.target.files[0]?.name || 'Choose file';
									var label = e.target.nextElementSibling;
									if (label && label.classList.contains('custom-file-label')) {
										label.innerText = fileName;
									}
								});
							});
						</script>
					</div>
					<!--end::Entry-->
				</div>
				<!--end::Content-->
				<?php
				include('includes/footer-inner.php');
				?>