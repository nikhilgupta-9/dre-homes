<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);
$date = date('Y-m-d H:i:s');

$err[] = '';
$id = base64_decode($_GET['edit']);
if (isset($_POST['update_news'])) {

	$news_thumb_img = 'news_thumb_img_' . rand(0, 10000) . '_' . $_FILES['news_thumb_img']['name'];
	$news_thumb_img_alt = prepStr($_POST['news_thumb_img_alt']);
	$news_heading = prepStr($_POST['news_heading']);
	$news_short_descp = prepStr($_POST['news_short_descp']);
	$news_inner_img = 'news_inner_img_' . rand(0, 10000) . '_' . $_FILES['news_inner_img']['name'];
	$news_inner_img_alt = prepStr($_POST['news_inner_img_alt']);
	$news_long_descp = prepStr($_POST['news_long_descp']);
	$news_slug = prepStr($_POST['news_slug']);
	$news_meta_title = prepStr($_POST['news_meta_title']);
	$news_meta_keyword = prepStr($_POST['news_meta_keyword']);
	$news_meta_descp = prepStr($_POST['news_meta_descp']);
	$news_meta_tags = prepStr($_POST['news_meta_tags']);
	$news_posted_on = date('Y-m-d', strtotime($_POST['news_posted_on']));
	$news_markspot = (prepStr($_POST['news_markspot'] != '' ? $_POST['news_markspot'] : 2));
	$is_active = (prepStr($_POST['is_active'] != '' ? 1 : 2));



	$allowed_image_extension = array(
		"png",
		"jpg",
		"jpeg"
	);

	$thumb_file_extension = pathinfo($_FILES["news_thumb_img"]["name"], PATHINFO_EXTENSION);

	if (!in_array($thumb_file_extension, $allowed_image_extension) && !empty($_FILES['news_thumb_img']['name'])) {
		$err[] = 'Invalid Thumb Image. Only PNG,JPG and JPEG are allowed.';
	}

	if (!empty($_FILES['news_thumb_img']['name'])) {
		$news_thumb_img = 'news_thumb_img_' . rand(0, 10000) . '_' . $_FILES['news_thumb_img']['name'];
	} else {
		$news_thumb_img = $_POST['news_thumb_img_hidden'];
	}

	$inner_file_extension = pathinfo($_FILES["news_inner_img"]["name"], PATHINFO_EXTENSION);

	if (!in_array($inner_file_extension, $allowed_image_extension) && !empty($_FILES['news_inner_img']['name'])) {
		$err[] = 'Invalid Inner Image. Only PNG,JPG and JPEG are allowed.';
	}

	if (!empty($_FILES['news_inner_img']['name'])) {
		$news_inner_img = 'news_inner_img_' . rand(0, 10000) . '_' . $_FILES['news_inner_img']['name'];
	} else {
		$news_inner_img = $_POST['news_inner_img_hidden'];
	}

	if (count($err) == 1) {

		if (!empty($_FILES['news_thumb_img']['name'])) {
			move_uploaded_file($_FILES['news_thumb_img']['tmp_name'], "assets/media/company-news/thumb/$news_thumb_img");
			unlink('assets/media/company-news/thumb/' . $_POST['news_thumb_img_hidden']);
		}


		if (!empty($_FILES['news_inner_img']['name'])) {
			move_uploaded_file($_FILES['news_inner_img']['tmp_name'], "assets/media/company-news/inner/$news_inner_img");
			unlink('assets/media/company-news/inner/' . $_POST['news_inner_img_hidden']);
		}


		$old_data = $logger->getOldRecordData('tbl_news', $id);

		$query = mysqli_query($conn, "update tbl_news
								SET news_thumb_img='$news_thumb_img',
									news_thumb_img_alt='$news_thumb_img_alt',
									news_heading='$news_heading',
									news_short_descp='$news_short_descp',
									news_inner_img='$news_inner_img',
									news_inner_img_alt='$news_inner_img_alt',
									news_long_descp='$news_long_descp',
									news_slug='$news_slug',
									meta_title='$news_meta_title',
									meta_keyword='$news_meta_keyword',
									meta_description='$news_meta_descp',
									meta_tags='$news_meta_tags',
									news_posted_on='$news_posted_on',
					                news_markspot='$news_markspot',
									is_active='$is_active',
									tstp='" . $date . "' where news_id=$id");
		if ($query) {
			$new_data = $logger->getOldRecordData('tbl_news', $id);

			$logger->log(
				'UPDATE',
				'tbl_news',
				$id,
				$old_data,
				$new_data,
				'Update Company News Detail'
			);

			header('Location:' . BASE_URL . 'manage-company-news.php');
		}
	}

}

$details = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_news where news_id=$id"));
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
				<!--begin::Entry - Responsive Mobile Ready Section (Bootstrap 4)-->
				<div class="content card-custom gutter-b" id="kt_content">
					<div class="d-flex flex-column-fluid">
						<div class="container">
							<div class="card card-custom gutter-b shadow-sm border-0">
								<div class="card-header flex-wrap py-3 bg-white border-0">
									<div class="card-title">
										<h3 class="card-label font-weight-bold">Edit Company News</h3>
									</div>
									<div class="card-toolbar">
										<a href="<?= BASE_URL ?>manage-company-news.php"
											class="btn btn-primary font-weight-bolder btn-sm px-4">
											<span class="svg-icon svg-icon-md mr-1">
												<svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px"
													viewBox="0 0 24 24" version="1.1">
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
								<hr>
								<div class="card-body">
									<div class="card-custom gutter-b example example-compact">
										<!--begin::Form-->
										<form action="" enctype="multipart/form-data" method="post"
											id="edit-company-news">
											<div class="card-body px-0 pt-4">
												<!-- Responsive grid layout: 1 column on mobile, 2 columns on medium+ screens for compact fields -->
												<div class="form-row">
													<div class="form-group col-md-6">
														<label for="thumb_img" class="font-weight-semibold">Thumb Image
															<span class="text-danger">*</span></label>
														<div class="custom-file">
															<input type="file" name="news_thumb_img"
																class="custom-file-input" id="thumb_img">
															<label class="custom-file-label" for="thumb_img">Choose
																file</label>
														</div>
														<?php if (!empty($details['news_thumb_img'])) { ?>
															<div class="mt-3">
																<img src="<?= BASE_URL ?>assets/media/company-news/thumb/<?= $details['news_thumb_img'] ?>"
																	width="50" class="img-thumbnail p-1"
																	style="max-width: 70px;">
															</div>
														<?php } ?>
														<input type="hidden" name="news_thumb_img_hidden"
															value="<?= $details['news_thumb_img'] ?>">
													</div>
													<div class="form-group col-md-6">
														<label class="font-weight-semibold">Thumb Image Alt Tag <span
																class="text-danger">*</span></label>
														<input type="text" name="news_thumb_img_alt"
															value="<?= $details['news_thumb_img_alt'] ?>"
															class="form-control" autocomplete="off"
															placeholder="Image Alt Tag">
													</div>
												</div>

												<div class="form-row">
													<div class="form-group col-md-12">
														<label class="font-weight-semibold">Heading <span
																class="text-danger">*</span></label>
														<input type="text" name="news_heading"
															value="<?= $details['news_heading'] ?>"
															class="form-control form-control-lg" autocomplete="off"
															placeholder="Heading">
													</div>
												</div>

												<div class="form-row">
													<div class="form-group col-md-12">
														<label class="font-weight-semibold">Short Description <span
																class="text-danger">*</span></label>
														<textarea name="news_short_descp" class="form-control"
															placeholder="Short Description" aria-hidden="true"
															rows="6"><?= $details['news_short_descp'] ?></textarea>
														<small class="form-text text-muted">Brief summary visible on
															listing pages.</small>
													</div>
												</div>

												<div class="form-row">
													<div class="form-group col-md-6">
														<label class="font-weight-semibold">Inner Image <span
																class="text-danger">*</span></label>
														<div class="custom-file">
															<input type="file" name="news_inner_img"
																class="custom-file-input" id="inner_img">
															<label class="custom-file-label" for="inner_img">Choose
																file</label>
														</div>
														<?php if (!empty($details['news_inner_img'])) { ?>
															<div class="mt-3">
																<img src="<?= BASE_URL ?>assets/media/company-news/inner/<?= $details['news_inner_img'] ?>"
																	width="50" class="img-thumbnail p-1"
																	style="max-width: 70px;">
															</div>
														<?php } ?>
														<input type="hidden" name="news_inner_img_hidden"
															value="<?= $details['news_inner_img'] ?>">
													</div>
													<div class="form-group col-md-6">
														<label class="font-weight-semibold">Inner Image Alt Tag <span
																class="text-danger">*</span></label>
														<input type="text" name="news_inner_img_alt"
															value="<?= $details['news_inner_img_alt'] ?>"
															class="form-control" autocomplete="off"
															placeholder="Image Alt Tag">
													</div>
												</div>

												<div class="form-row">
													<div class="form-group col-md-12">
														<label class="font-weight-semibold">Long Description <span
																class="text-danger">*</span></label>
														<textarea name="news_long_descp" class="form-control"
															id="kt-tinymce-4" placeholder="Long Description"
															aria-hidden="true"
															rows="10"><?= $details['news_long_descp'] ?></textarea>
														<small class="form-text text-muted">Full article content —
															editor preserved.</small>
													</div>
												</div>

												<div class="form-row">
													<div class="form-group col-md-6">
														<label class="font-weight-semibold">Slug <span
																class="text-danger">*</span></label>
														<input type="text" name="news_slug"
															value="<?= $details['news_slug'] ?>" class="form-control"
															autocomplete="off" placeholder="URL-friendly slug">
													</div>
													<div class="form-group col-md-6">
														<label class="font-weight-semibold">Posted On <span
																class="text-danger">*</span></label>
														<input type="text" name="news_posted_on"
															value="<?= date('d-m-Y', strtotime($details['news_posted_on'])) ?>"
															class="form-control" autocomplete="off" id="kt_datepicker_1"
															placeholder="dd-mm-yyyy">
													</div>
												</div>

												<!-- Meta details row: 2 columns on tablet/desktop -->
												<div class="form-row">
													<div class="form-group col-md-12">
														<label class="font-weight-semibold">Meta Title <span
																class="text-danger">*</span></label>
														<input type="text" name="news_meta_title"
															value="<?= $details['meta_title'] ?>" class="form-control"
															autocomplete="off" placeholder="Meta Title">
													</div>
												</div>
												<div class="form-row">
													<div class="form-group col-md-12">
														<label class="font-weight-semibold">Meta Keyword <span
																class="text-danger">*</span></label>
														<input type="text" name="news_meta_keyword"
															value="<?= $details['meta_keyword'] ?>" class="form-control"
															autocomplete="off" placeholder="Meta Keyword">
													</div>
												</div>
												<div class="form-row">
													<div class="form-group col-md-12">
														<label class="font-weight-semibold">Meta Description <span
																class="text-danger">*</span></label>
														<textarea name="news_meta_descp" class="form-control"
															placeholder="Meta Description" aria-hidden="true"
															rows="5"><?= $details['meta_description'] ?></textarea>
													</div>
												</div>
												<div class="form-row">
													<div class="form-group col-md-12">
														<label class="font-weight-semibold">Meta Tags</label>
														<textarea name="news_meta_tags" class="form-control"
															placeholder="Meta Tags (comma separated)" aria-hidden="true"
															rows="4"><?= $details['meta_tags'] ?></textarea>
														<small class="form-text text-muted">Optional additional meta
															tags.</small>
													</div>
												</div>

												<!-- Two column layout for toggle & spotlight checkbox on mobile-responsive -->
												<div class="form-row align-items-center">
													<div class="form-group col-sm-6">
														<label class="font-weight-semibold d-block">Mark as
															Spotlight</label>
														<div class="custom-control custom-switch custom-control-md">
															<input type="checkbox" class="custom-control-input"
																name="news_markspot" value='1' id="spotlightSwitch"
																<?= ($details['news_markspot'] == 1 ? 'checked' : '') ?>>
															<label class="custom-control-label"
																for="spotlightSwitch">Spotlight feature</label>
														</div>
														<small class="form-text text-muted">Highlight this news
															item.</small>
													</div>
													<div class="form-group col-sm-6">
														<label class="font-weight-semibold d-block">Status
															(Active/Inactive)</label>
														<div class="custom-control custom-switch custom-control-md">
															<input type="checkbox" class="custom-control-input"
																name="is_active" value="1" id="statusSwitch"
																<?= ($details['is_active'] == 1 ? 'checked' : '') ?>>
															<label class="custom-control-label"
																for="statusSwitch">Active</label>
														</div>
														<small class="form-text text-muted">Toggle visibility on
															frontend.</small>
													</div>
												</div>
											</div>
											<div
												class="card-footer bg-transparent border-0 px-0 pb-0 pt-4 d-flex flex-wrap justify-content-start gap-2">
												<button type="submit" name="update_news"
													class="btn btn-primary font-weight-bold px-5 py-2 mr-3 mb-2">
													<i class="fas fa-save mr-1"></i> Update News
												</button>
												<button type="reset"
													class="btn btn-outline-secondary font-weight-bold px-5 py-2 mb-2">
													<i class="fas fa-undo-alt mr-1"></i> Reset
												</button>
											</div>
										</form>
										<!--end::Form-->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				include('includes/footer-inner.php');
				?>