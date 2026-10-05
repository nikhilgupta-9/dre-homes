<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');

$err = array();
$id = base64_decode($_GET['edit']);

if (isset($_POST['update_community_content'])) {

	// Sanitize inputs
	$comm_cont_heading = mysqli_real_escape_string($conn, prepStr($_POST['comm_cont_heading']));
	$comm_cont_map = mysqli_real_escape_string($conn, prepStr($_POST['comm_cont_map']));
	$comm_home_header = mysqli_real_escape_string($conn, $_POST['comm_header_home']);
	$comm_content_header = mysqli_real_escape_string($conn, $_POST['comm_content_home']);
	$is_active = (isset($_POST['is_active']) && $_POST['is_active'] == 1) ? 1 : 2;

	// ========== COMMUNITY CONTENT IMAGE UPLOAD ==========
	$comm_cont_img_path = 'assets/media/community/';

	// Create folder if not exists
	if (!file_exists($comm_cont_img_path)) {
		mkdir($comm_cont_img_path, 0777, true);
	}

	// Initialize with existing image
	$comm_cont_img = isset($_POST['comm_cont_img_hidden']) ? $_POST['comm_cont_img_hidden'] : '';

	// Process new upload
	if (!empty($_FILES['comm_cont_img']['name'])) {
		$allowed = ['jpg', 'jpeg', 'png'];
		$file_ext = strtolower(pathinfo($_FILES['comm_cont_img']['name'], PATHINFO_EXTENSION));

		if (in_array($file_ext, $allowed)) {
			$new_filename = 'comm_img_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
			$full_path = $comm_cont_img_path . $new_filename;

			if (move_uploaded_file($_FILES['comm_cont_img']['tmp_name'], $full_path)) {
				// Delete old file if exists
				if (!empty($comm_cont_img) && file_exists($comm_cont_img_path . $comm_cont_img)) {
					unlink($comm_cont_img_path . $comm_cont_img);
				}
				$comm_cont_img = $new_filename;
			} else {
				$err[] = "Failed to upload community image!";
			}
		} else {
			$err[] = 'Invalid Image. Only PNG, JPG and JPEG are allowed.';
		}
	}

	// ========== HOME PAGE BACKGROUND IMAGE UPLOAD ==========
	$comm_img_header_path = 'assets/media/community/home-bg/';

	// Create folder if not exists
	if (!file_exists($comm_img_header_path)) {
		mkdir($comm_img_header_path, 0777, true);
	}

	// Initialize with existing image
	$comm_img_header = isset($_POST['comm_img_home_hidden']) ? $_POST['comm_img_home_hidden'] : '';

	// Process new upload
	if (!empty($_FILES['comm_img_home']['name'])) {
		$allowed = ['jpg', 'jpeg', 'png', 'webp'];
		$file_ext = strtolower(pathinfo($_FILES['comm_img_home']['name'], PATHINFO_EXTENSION));

		if (in_array($file_ext, $allowed)) {
			$new_filename = 'home_bg_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
			$full_path = $comm_img_header_path . $new_filename;

			if (move_uploaded_file($_FILES['comm_img_home']['tmp_name'], $full_path)) {
				// Delete old file if exists
				if (!empty($comm_img_header) && file_exists($comm_img_header_path . $comm_img_header)) {
					unlink($comm_img_header_path . $comm_img_header);
				}
				$comm_img_header = $new_filename;
			} else {
				$err[] = "Failed to upload home background image!";
			}
		} else {
			$err[] = 'Invalid Image for Home Background. Only PNG, JPG, JPEG, WEBP are allowed.';
		}
	}

	// If no errors, proceed with update
	if (count($err) == 0) {
		$query = mysqli_query($conn, "UPDATE tbl_community_content
                    SET comm_cont_img='$comm_cont_img',
                        comm_cont_heading='$comm_cont_heading',
                        comm_cont_map='$comm_cont_map',
                        comm_header_home='$comm_home_header',
                        comm_content_home='$comm_content_header',
                        comm_img_home='$comm_img_header',
                        is_active='$is_active',
                        tstp='" . date('Y-m-d H:i:s') . "' 
                    WHERE comm_cont_id=$id");

		if ($query) {
			$_SESSION['success_msg'] = "Data Updated Successfully";
			header('Location:' . BASE_URL . 'edit-community-content.php?edit=' . base64_encode($id));
			exit();
		} else {
			$_SESSION['error_msg'] = "Query Failed: " . mysqli_error($conn);
		}
	} else {
		$_SESSION['error_msg'] = implode(", ", $err);
	}
}

$details = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM tbl_community_content WHERE comm_cont_id=$id"));
?>

<!--begin::Body-->

<body id="kt_body"
	class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
	<!--begin::Main-->
	<!--begin::Header Mobile-->
	<div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
		<a href="index.html">
			<img alt="Logo" src="<?= BASE_URL ?>assets/media/logos/logo-light.png" />
		</a>
		<div class="d-flex align-items-center">
			<button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
				<span></span>
			</button>
			<button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle">
				<span></span>
			</button>
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
		</div>
	</div>
	<!--end::Header Mobile-->

	<div class="d-flex flex-column flex-root">
		<div class="d-flex flex-row flex-column-fluid page">
			<?php include('includes/sidebar.php'); ?>

			<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
				<div id="kt_header" class="header header-fixed">
					<div class="container-fluid d-flex align-items-stretch justify-content-between">
						<div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
							<div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
							</div>
						</div>
						<div class="topbar">
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
						</div>
					</div>
				</div>



				<!--begin::Content-->
				<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
					<div class="d-flex flex-column-fluid">
						<div class="container">

							<!--begin::Card-->
							<div class="card card-custom gutter-b">
								<div class="card-header flex-wrap py-3">
									<div class="card-title">
										<h3 class="card-label">Community Content</h3>
									</div>
								</div>
								<div class="card-body">
									<?php if (!empty($err) && count($err) > 0): ?>
										<ul class="err_list alert alert-danger">
											<?php foreach ($err as $list_err): ?>
												<li><?= htmlspecialchars($list_err) ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>

									<div class="row">
										<div class="col-12">
											<!-- Success/Error Messages -->
											<?php if (!empty($_SESSION['success_msg'])): ?>
												<div class="alert alert-success alert-dismissible fade show m-3"
													role="alert">
													<strong>Success!</strong>
													<?= $_SESSION['success_msg'];
													unset($_SESSION['success_msg']); ?>
													<button type="button" class="close" data-dismiss="alert"
														aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
											<?php endif; ?>

											<?php if (!empty($_SESSION['error_msg'])): ?>
												<div class="alert alert-danger alert-dismissible fade show m-3"
													role="alert">
													<strong>Error!</strong>
													<?= $_SESSION['error_msg'];
													unset($_SESSION['error_msg']); ?>
													<button type="button" class="close" data-dismiss="alert"
														aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
											<?php endif; ?>
										</div>
									</div>

									<div class="card-custom gutter-b example example-compact">
										<!--begin::Form-->
										<form action="" enctype="multipart/form-data" method="post"
											id="edit-community-content">
											<div class="card-body">
												<div class="row">
													<div class="col-md-4">
														<div class="form-group">
															<label>Community Image <span
																	class="text-danger">*</span></label>
															<input type="file" name="comm_cont_img" class="form-control"
																accept="image/*">
															<?php if (!empty($details['comm_cont_img'])): ?>
																<div class="mt-2">
																	<img src="<?= BASE_URL ?>assets/media/community/<?= $details['comm_cont_img'] ?>"
																		width="80" class="rounded border">
																	<small class="d-block text-muted">Current Image</small>
																</div>
															<?php endif; ?>
															<input type="hidden" name="comm_cont_img_hidden"
																value="<?= htmlspecialchars($details['comm_cont_img']) ?>">
														</div>
													</div>
													<div class="col-md-8">
														<div class="form-group">
															<label>Heading</label>
															<input type="text" name="comm_cont_heading"
																value="<?= htmlspecialchars($details['comm_cont_heading']) ?>"
																class="form-control" placeholder="Heading">
														</div>
													</div>
												</div>

												<div class="form-group">
													<label>Map</label>
													<input type="text" name="comm_cont_map"
														value="<?= htmlspecialchars($details['comm_cont_map']) ?>"
														class="form-control" placeholder="Map">
												</div>

												<div class="row mt-4">
													<div class="col-md-12">
														<div class="border-bottom border-primary mb-3 pb-2">
															<h4 class="text-primary font-weight-bold mb-0">Home Page
																Content</h4>
														</div>
													</div>

													<div class="col-md-6">
														<div class="form-group">
															<label>Heading for Community Section on Home</label>
															<input type="text" name="comm_header_home"
																value="<?= htmlspecialchars($details['comm_header_home']) ?>"
																placeholder="Heading for Home" class="form-control">
														</div>
													</div>

													<div class="col-md-6">
														<div class="form-group">
															<label>Content for Community Section on Home</label>
															<textarea name="comm_content_home" rows="5"
																class="form-control"
																placeholder="Content for Home"><?= htmlspecialchars($details['comm_content_home']) ?></textarea>
														</div>
													</div>

													<div class="col-md-4">
														<div class="form-group">
															<label>Background Image for Community</label><br>
															<input type="file" name="comm_img_home" class="form-control"
															accept="image/*">
															<span class="text-muted">Image size must be (200*z410 px)</span>
															<?php if (!empty($details['comm_img_home'])): ?>
																<div class="mt-2">
																	<img src="<?= BASE_URL ?>assets/media/community/home-bg/<?= $details['comm_img_home'] ?>"
																		width="80" class="rounded border">
																	<small class="d-block text-muted">Current Background
																		Image</small>
																</div>
															<?php endif; ?>
															<input type="hidden" name="comm_img_home_hidden"
																value="<?= htmlspecialchars($details['comm_img_home']) ?>">
														</div>
													</div>

													<div class="col-md-2">
														<div class="form-group">
															<label>Status</label><br>
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
											</div>
											<div class="card-footer">
												<button type="submit" name="update_community_content"
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
				<?php include('includes/footer-inner.php'); ?>
			</div>
		</div>
	</div>