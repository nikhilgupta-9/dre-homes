<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);

$err[] = '';
$id = base64_decode($_GET['edit']);
if (isset($_POST['update_blog'])) {

	$blog_thumb_img = 'blog_thumb_img_' . rand(0, 10000) . '_' . $_FILES['blog_thumb_img']['name'];
	$blog_thumb_img_alt = prepStr($_POST['blog_thumb_img_alt']);
	$blog_heading = prepStr($_POST['blog_heading']);
	$blog_short_descp = prepStr($_POST['blog_short_descp']);
	$blog_inner_img = 'blog_inner_img_' . rand(0, 10000) . '_' . $_FILES['blog_inner_img']['name'];
	$blog_inner_img_alt = prepStr($_POST['blog_inner_img_alt']);
	$blog_long_descp = prepStr($_POST['blog_long_descp']);
	$blog_slug = prepStr($_POST['blog_slug']);
	$blog_meta_title = prepStr($_POST['blog_meta_title']);
	$blog_meta_keyword = prepStr($_POST['blog_meta_keyword']);
	$blog_meta_descp = prepStr($_POST['blog_meta_descp']);
	$blog_meta_tags = prepStr($_POST['blog_meta_tags']);
	$blog_posted_on = date('Y-m-d', strtotime($_POST['blog_posted_on']));
	$is_active = (prepStr($_POST['is_active'] != '' ? 1 : 2));



	$allowed_image_extension = array(
		"png",
		"jpg",
		"jpeg"
	);

	$thumb_file_extension = pathinfo($_FILES["blog_thumb_img"]["name"], PATHINFO_EXTENSION);

	if (!in_array($thumb_file_extension, $allowed_image_extension) && !empty($_FILES['blog_thumb_img']['name'])) {
		$err[] = 'Invalid Thumb Image. Only PNG,JPG and JPEG are allowed.';
	}

	if (!empty($_FILES['blog_thumb_img']['name'])) {
		$blog_thumb_img = 'blog_thumb_img_' . rand(0, 10000) . '_' . $_FILES['blog_thumb_img']['name'];
	} else {
		$blog_thumb_img = $_POST['blog_thumb_img_hidden'];
	}

	$inner_file_extension = pathinfo($_FILES["blog_inner_img"]["name"], PATHINFO_EXTENSION);

	if (!in_array($inner_file_extension, $allowed_image_extension) && !empty($_FILES['blog_inner_img']['name'])) {
		$err[] = 'Invalid Inner Image. Only PNG,JPG and JPEG are allowed.';
	}

	if (!empty($_FILES['blog_inner_img']['name'])) {
		$blog_inner_img = 'blog_inner_img_' . rand(0, 10000) . '_' . $_FILES['blog_inner_img']['name'];
	} else {
		$blog_inner_img = $_POST['blog_inner_img_hidden'];
	}

	if (count($err) == 1) {

		if (!empty($_FILES['blog_thumb_img']['name'])) {
			move_uploaded_file($_FILES['blog_thumb_img']['tmp_name'], "assets/media/blog/thumb/$blog_thumb_img");
			unlink('assets/media/blog/thumb/' . $_POST['blog_thumb_img_hidden']);
		}


		if (!empty($_FILES['blog_inner_img']['name'])) {
			move_uploaded_file($_FILES['blog_inner_img']['tmp_name'], "assets/media/blog/inner/$blog_inner_img");
			unlink('assets/media/blog/inner/' . $_POST['blog_inner_img_hidden']);
		}

		$old_data = $logger->getOldRecordData('tbl_blog', $id);

		$query = mysqli_query($conn, "update tbl_blog
								SET blog_thumb_img='$blog_thumb_img',
									blog_thumb_img_alt='$blog_thumb_img_alt',
									blog_heading='$blog_heading',
									blog_short_descp='$blog_short_descp',
									blog_inner_img='$blog_inner_img',
									blog_inner_img_alt='$blog_inner_img_alt',
									blog_long_descp='$blog_long_descp',
									blog_slug='$blog_slug',
									meta_title='$blog_meta_title',
									meta_keyword='$blog_meta_keyword',
									meta_description='$blog_meta_descp',
									meta_tags='$blog_meta_tags',
									blog_posted_on='$blog_posted_on',
									is_active='$is_active',
									tstp='" . $date . "' where blog_id=$id");



		if ($query) {
			$new_data = $logger->getOldRecordData('tbl_blog', $id);

			$logger->log(
				'UPDATE',
				'tbl_blog',
				$id,
				$old_data,
				$new_data,
				'Blog updated'
			);
			header('Location:' . BASE_URL . 'manage-blog.php');
		}
	}
}

$details = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_blog where blog_id=$id"));
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
										<h3 class="card-label">Update Blog</h3>
									</div>
									<div class="card-toolbar">
										<!--begin::Button-->
										<a href="<?= BASE_URL ?>manage-blog.php"
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
										<form action="" enctype="multipart/form-data" method="post" id="edit-blog"
											class="needs-validation" novalidate>
											<div class="card-body">

												<!-- Image Section -->
												<div class="form-group row">
													<div class="col-md-12">
														<h5 class="mb-3 pb-2 border-bottom">Blog Images</h5>
													</div>

													<div class="col-md-6">
														<label class="font-weight-bold">Thumb Image <span
																class="text-danger">*</span></label>
														<div class="custom-file">
															<input type="file" name="blog_thumb_img" id="blog_thumb_img"
																class="custom-file-input" accept="image/*">
															<label class="custom-file-label" for="blog_thumb_img">Choose
																file...</label>
														</div>
														<small class="form-text text-muted">Allowed formats: JPG, PNG,
															JPEG, WEBP, GIF (Max: 2MB)</small>
														<?php if (!empty($details['blog_thumb_img'])) { ?>
															<div class="mt-3">
																<label class="text-muted small">Current Image:</label>
																<div class="mt-1">
																	<img src="<?= BASE_URL ?>assets/media/blog/thumb/<?= $details['blog_thumb_img'] ?>"
																		width="60" class="img-thumbnail">
																</div>
															</div>
														<?php } ?>
														<input type="hidden" name="blog_thumb_img_hidden"
															value="<?= $details['blog_thumb_img'] ?>">
													</div>

													<div class="col-md-6">
														<label class="font-weight-bold">Thumb Image Alt Tag <span
																class="text-danger">*</span></label>
														<input type="text" name="blog_thumb_img_alt"
															value="<?= $details['blog_thumb_img_alt'] ?>"
															class="form-control" autocomplete="off"
															placeholder="e.g., Blog thumbnail image">
														<small class="form-text text-muted">SEO friendly alt text for
															the thumbnail image</small>
													</div>
												</div>

												<div class="form-group row">
													<div class="col-md-6">
														<label class="font-weight-bold">Inner Image <span
																class="text-danger">*</span></label>
														<div class="custom-file">
															<input type="file" name="blog_inner_img" id="blog_inner_img"
																class="custom-file-input" accept="image/*">
															<label class="custom-file-label" for="blog_inner_img">Choose
																file...</label>
														</div>
														<small class="form-text text-muted">Allowed formats: JPG, PNG,
															JPEG, WEBP, GIF (Max: 2MB)</small>
														<?php if (!empty($details['blog_inner_img'])) { ?>
															<div class="mt-3">
																<label class="text-muted small">Current Image:</label>
																<div class="mt-1">
																	<img src="<?= BASE_URL ?>assets/media/blog/inner/<?= $details['blog_inner_img'] ?>"
																		width="60" class="img-thumbnail">
																</div>
															</div>
														<?php } ?>
														<input type="hidden" name="blog_inner_img_hidden"
															value="<?= $details['blog_inner_img'] ?>">
													</div>

													<div class="col-md-6">
														<label class="font-weight-bold">Inner Image Alt Tag <span
																class="text-danger">*</span></label>
														<input type="text" name="blog_inner_img_alt"
															value="<?= $details['blog_inner_img_alt'] ?>"
															class="form-control" autocomplete="off"
															placeholder="e.g., Blog inner image">
														<small class="form-text text-muted">SEO friendly alt text for
															the inner image</small>
													</div>
												</div>

												<!-- Blog Content Section -->
												<div class="form-group row">
													<div class="col-md-12">
														<h5 class="mb-3 pb-2 border-bottom mt-3">Blog Content</h5>
													</div>

													<div class="col-md-12">
														<label class="font-weight-bold">Heading <span
																class="text-danger">*</span></label>
														<input type="text" name="blog_heading"
															value="<?= $details['blog_heading'] ?>" class="form-control"
															autocomplete="off" placeholder="Enter blog heading">
														<small class="form-text text-muted">Main title of the blog
															post</small>
													</div>
												</div>

												<div class="form-group row">
													<div class="col-md-12">
														<label class="font-weight-bold">Short Description <span
																class="text-danger">*</span></label>
														<textarea name="blog_short_descp" class="form-control" rows="4"
															placeholder="Enter short description (summary of the blog)"><?= $details['blog_short_descp'] ?></textarea>
														<small class="form-text text-muted">Brief summary that appears
															in blog listings (150-200 characters recommended)</small>
													</div>
												</div>

												<div class="form-group row">
													<div class="col-md-12">
														<label class="font-weight-bold">Long Description <span
																class="text-danger">*</span></label>
														<textarea name="blog_long_descp" class="form-control"
															id="kt-tinymce-4" rows="12"
															placeholder="Enter detailed blog content"><?= $details['blog_long_descp'] ?></textarea>
														<small class="form-text text-muted">Full blog content with
															formatting options</small>
													</div>
												</div>

												<!-- SEO Section -->
												<div class="form-group row">
													<div class="col-md-12">
														<h5 class="mb-3 pb-2 border-bottom mt-3">SEO Information</h5>
													</div>

													<div class="col-md-12">
														<label class="font-weight-bold">Slug <span
																class="text-danger">*</span></label>
														<div class="input-group">
															<div class="input-group-prepend">
																<span
																	class="input-group-text"><?= BASE_URL ?>blog/</span>
															</div>
															<input type="text" name="blog_slug"
																value="<?= $details['blog_slug'] ?>"
																class="form-control" autocomplete="off"
																placeholder="enter-url-friendly-slug">
														</div>
														<small class="form-text text-muted">URL-friendly version of the
															title (use hyphens instead of spaces)</small>
													</div>
												</div>

												<div class="form-group row">
													<div class="col-md-12">
														<label class="font-weight-bold">Meta Title <span
																class="text-danger">*</span></label>
														<input type="text" name="blog_meta_title"
															value="<?= $details['meta_title'] ?>" class="form-control"
															autocomplete="off"
															placeholder="Enter meta title (50-60 characters)"
															maxlength="160">
														<small class="form-text text-muted">Page title that appears in
															search results (50-60 characters recommended)</small>
													</div>
												</div>

												<div class="form-group row">
													<div class="col-md-12">
														<label class="font-weight-bold">Meta Keyword <span
																class="text-danger">*</span></label>
														<input type="text" name="blog_meta_keyword"
															value="<?= $details['meta_keyword'] ?>" class="form-control"
															autocomplete="off"
															placeholder="keyword1, keyword2, keyword3">
														<small class="form-text text-muted">Comma-separated keywords for
															SEO</small>
													</div>
												</div>

												<div class="form-group row">
													<div class="col-md-12">
														<label class="font-weight-bold">Meta Description <span
																class="text-danger">*</span></label>
														<textarea name="blog_meta_descp" class="form-control" rows="3"
															placeholder="Enter meta description (150-160 characters)"
															maxlength="320"><?= $details['meta_description'] ?></textarea>
														<small class="form-text text-muted">Description that appears in
															search results (150-160 characters recommended)</small>
													</div>
												</div>

												<div class="form-group row">
													<div class="col-md-12">
														<label class="font-weight-bold">Meta Tags</label>
														<textarea name="blog_meta_tags" class="form-control" rows="3"
															placeholder="Enter additional meta tags"><?= $details['meta_tags'] ?></textarea>
														<small class="form-text text-muted">Additional meta tags for
															social media sharing etc.</small>
													</div>
												</div>

												<!-- Publishing Section -->
												<div class="form-group row">
													<div class="col-md-12">
														<h5 class="mb-3 pb-2 border-bottom mt-3">Publishing</h5>
													</div>

													<div class="col-md-4">
														<label class="font-weight-bold">Posted On <span
																class="text-danger">*</span></label>
														<div class="input-group date">
															<div class="input-group-prepend">
																<span class="input-group-text"><i
																		class="fa fa-calendar"></i></span>
															</div>
															<input type="text" name="blog_posted_on"
																value="<?= date('d-m-Y', strtotime($details['blog_posted_on'])) ?>"
																class="form-control" id="kt_datepicker_1"
																placeholder="Select date">
														</div>
														<small class="form-text text-muted">Publication date of the blog
															post</small>
													</div>

													<div class="col-md-4">
														<label class="font-weight-bold">Status</label>
														<div class="mt-2">
															<span class="switch switch-md switch-icon">
																<label>
																	<input type="checkbox" name="is_active" value="1"
																		<?= ($details['is_active'] == 1 ? 'checked' : '') ?>>
																	<span></span>
																</label>
															</span>
														</div>
														<small class="form-text text-muted">Toggle to publish/unpublish
															the blog post</small>
													</div>
												</div>

											</div>

											<div class="card-footer">
												<button type="submit" name="update_blog" class="btn btn-primary px-4">
													<i class="fa fa-save"></i> Update Blog
												</button>
												<button type="reset" class="btn btn-secondary px-4 ml-2">
													<i class="fa fa-undo"></i> Reset
												</button>
												<a href="<?= BASE_URL ?>manage-blog.php"
													class="btn btn-light px-4 ml-2">
													<i class="fa fa-arrow-left"></i> Cancel
												</a>
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