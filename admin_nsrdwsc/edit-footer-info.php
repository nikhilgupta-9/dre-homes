<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);

$err[] = '';
$id = base64_decode($_GET['edit']);
if (isset($_POST['update_footer_info'])) {

	$footer_logo = 'footer_logo_' . rand(0, 10000) . '_' . $_FILES['footer_logo']['name'];
	$footer_logo_alt = prepStr($_POST['footer_logo_alt']);
	$footer_short_descp = prepStr($_POST['footer_short_descp']);
	$footer_address = prepStr($_POST['footer_address']);
	$footer_address2 = prepStr($_POST['footer_address2']);
	$footer_contact_number = prepStr($_POST['footer_contact_number']);
	$footer_contact_number2 = prepStr($_POST['footer_contact_number2']);
	$footer_email_id = prepStr($_POST['footer_email_id']);
	$footer_copyright_text = prepStr($_POST['footer_copyright_text']);
	$permit_number = prepStr($_POST['permit_number']);
	$is_active = (prepStr($_POST['is_active'] != '' ? 1 : 2));



	$allowed_image_extension = array(
		"png",
		"jpg",
		"jpeg"
	);

	$comm_file_extension = pathinfo($_FILES["footer_logo"]["name"], PATHINFO_EXTENSION);

	if (!in_array($comm_file_extension, $allowed_image_extension) && !empty($_FILES['footer_logo']['name'])) {
		$err[] = 'Invalid Image. Only PNG,JPG and JPEG are allowed.';
	}



	if (!empty($_FILES['footer_logo']['name'])) {
		$footer_logo = 'footer_logo_' . rand(0, 10000) . '_' . $_FILES['footer_logo']['name'];
	} else {
		$footer_logo = $_POST['footer_logo_hidden'];
	}

	if (count($err) == 1) {

		if (!empty($_FILES['footer_logo']['name'])) {
			move_uploaded_file($_FILES['footer_logo']['tmp_name'], "assets/media/footer/$footer_logo");
			unlink('assets/media/footer/' . $_POST['footer_logo_hidden']);
		}

		$old_data = $logger->getOldRecordData('tbl_footer_info', $id);

		$query = mysqli_query($conn, "update tbl_footer_info
	                    SET footer_logo='$footer_logo',
							footer_logo_alt='$footer_logo_alt',
							footer_short_descp='$footer_short_descp',
							footer_address='$footer_address',
							footer_address2='$footer_address2',
							footer_contact_number='$footer_contact_number',
							footer_contact_number2='$footer_contact_number2',
							footer_email_id='$footer_email_id',
							footer_copyright_text='$footer_copyright_text',
							permit_number='$permit_number',
							is_active='$is_active',
	                        tstp='" . $date . "' where footer_info_id=$id");
		if ($query) {
			$new_data = $logger->getOldRecordData('tbl_footer_info', $id);

			$logger->log(
				'UPDATE',
				'tbl_footer_info',
				$id,
				$old_data,
				$new_data,
				'Footer Info updated'
			);

			$_SESSION['offplan_success_msg'] = "Footer info updated successfully!";
			header('Location: edit-footer-info.php?edit=' . base64_encode($id));
			exit;
		} else {
			// Pro Tip: Live platform par direct mysqli_error() user ko mat dikhao (Security Risk)
			$_SESSION['offplan_error_msg'] = "<strong>Error!</strong> Something went wrong while updating the footer info.<br><b>" . mysqli_error($conn) . "</b>";
			header('Location: edit-footer-info.php?edit=' . base64_encode($id));
			exit;
		}
		if (!$query) {
			die("MySQL Error: " . mysqli_error($conn));
		}
	}else{
		die("MySQL Error: " . mysqli_error($conn));

	}

}

$details = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_footer_info where footer_info_id=$id"));
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

				<div class="container-fluid mt-4">

                    <?php if (isset($_SESSION['offplan_success_msg'])): ?>
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert"
                            style="border-left: 4px solid #28a745 !important;">
                            <div class="d-flex align-items-center">
                                <span class="alert-icon mr-3" style="font-size: 1.2 rem;"> <b><?=$details['proj_name']?>  ✔</b> </span>
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
										<h3 class="card-label">Footer Information</h3>
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
										<form action="" enctype="multipart/form-data" method="post"
											id="edit-community-content">
											<div class="card-body">

												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label for="exampleInputPassword1">Logo
																<span class="text-danger">*</span></label>
															<input type="file" id="footer_logo" name="footer_logo"
																value="" class="form-control">
															<?php if (!empty($details['footer_logo'])) { ?>
																<img src="<?= BASE_URL ?>assets/media/footer/<?= $details['footer_logo'] ?>"
																	width='80' class="mt-3 border p-1 bg-secondary">
															<?php } ?>
															<input type="hidden" name="footer_logo_hidden"
																value="<?= $details['footer_logo'] ?>">
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label for="exampleInputPassword1">Logo Alt Tag</label>
															<input type="text" id="footer_logo_alt"
																name="footer_logo_alt"
																value="<?= $details['footer_logo_alt'] ?>"
																class="form-control" placeholder="Logo Alt Tag">
														</div>
													</div>

													<div class="col-12">
														<div class="form-group">
															<label for="exampleInputPassword1">Permit Number</label>
															<input type="text" id="permitNumber" name="permit_number"
																value="<?= $details['permit_number'] ?>"
																class="form-control" placeholder="Company Permit No.">
														</div>
													</div>
												</div>






												<div class="form-group">
													<label for="exampleInputPassword1">Short Description</label>
													<textarea class="form-control" name="footer_short_descp"
														placeholder="Short Description"
														rows="10"><?= $details['footer_short_descp'] ?></textarea>
												</div>


												<div class="form-group">
													<label for="exampleInputPassword1">Address</label>
													<input type="text" id="footer_address" name="footer_address"
														value="<?= $details['footer_address'] ?>" class="form-control"
														placeholder="Address">
												</div>

												<div class="form-group">
													<label for="exampleInputPassword1">Address 2</label>
													<input type="text" id="footer_address2" name="footer_address2"
														value="<?= $details['footer_address2'] ?>" class="form-control"
														placeholder="Address 2">
												</div>


												<div class="row">
													<div class="col-md-4">
														<div class="form-group">
															<label for="exampleInputPassword1">Contact Number</label>
															<input type="text" id="footer_contact_number"
																name="footer_contact_number"
																value="<?= $details['footer_contact_number'] ?>"
																class="form-control" placeholder="Contact Number">
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label for="exampleInputPassword1">Contact Number 2</label>
															<input type="text" id="footer_contact_number2"
																name="footer_contact_number2"
																value="<?= $details['footer_contact_number2'] ?>"
																class="form-control" placeholder="Contact Number 2">
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label for="exampleInputPassword1">Email ID</label>
															<input type="text" id="footer_email_id"
																name="footer_email_id"
																value="<?= $details['footer_email_id'] ?>"
																class="form-control" placeholder="Email ID">
														</div>
													</div>
												</div>






												<div class="form-group">
													<label for="exampleInputPassword1">Copyright Text</label>
													<input type="text" id="footer_copyright_text"
														name="footer_copyright_text"
														value="<?= $details['footer_copyright_text'] ?>"
														class="form-control" placeholder="Copyright Text">
												</div>

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
											<div class="card-footer">
												<button type="submit" name="update_footer_info"
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