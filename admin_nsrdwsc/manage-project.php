<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);
$date = date('Y-m-d H:i:s');

if (isset($_GET['del'])) {
	$id = intval($_GET['del']);


	$proj_img = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_proj
    where prj_id=$id"));
	$gallery_img = mysqli_query($conn, "select * from tbl_proj_gallery
    where proj_id=$id");

	while ($row_gallery_img = mysqli_fetch_array($gallery_img)) {
		unlink('assets/media/project/gallery/' . $row_gallery_img['gallery_img']);
	}

	unlink('assets/media/project/inner-image/' . $proj_img['proj_inner_img']);
	unlink('assets/media/project/pdf/' . $proj_img['proj_brochure']);
	unlink('assets/media/project/pdf/' . $proj_img['proj_payplan']);
	unlink('assets/media/project/pdf/' . $proj_img['proj_floorplan']);

	$old_data = $logger->getOldRecordData('tbl_proj', $id);

	$logger->log(
		'DELETE',
		'tbl_peoj',
		$id,
		$old_data,
		null,
		'Delete Properties: ' . $id
	);

	$delete_record = $logger->archiveDeletedRecord('tbl_proj', $id, $old_data);


	$query = mysqli_query($conn, "delete from tbl_proj where prj_id=$id");


	if ($query) {
		header('Location:' . BASE_URL . 'manage-project.php');
	}
}

// Get filter parameter from URL
$homepage_filter = isset($_GET['homepage_filter']) ? $_GET['homepage_filter'] : 'all';

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
				<div class="d-flex flex-column flex-column-fluid" id="kt_content">
					<!--begin::Entry-->
					<div class="d-flex flex-column-fluid">
						<!--begin::Container-->
						<div class="container">

							<!--begin::Card-->
							<?php if (isset($_GET['msg']) && $_GET['msg'] == 'push_success'): ?>
								<div id="successMsg" class="alert alert-success">
									Project(s) pushed to app successfully.
								</div>
							<?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'partial_success'): ?>
								<div id="successMsg" class="alert alert-warning">
									Some project(s) failed to push. Failed IDs: <?= htmlspecialchars($_GET['failed'] ?? '') ?>
								</div>
							<?php endif; ?>
							<form method="POST" action="<?= BASE_URL ?>push-app/push-project-app.php">
							<div class="card card-custom gutter-b">
								<div class="card-header flex-wrap py-3">
									<div class="card-title">
										<h3 class="card-label">Manage Project</h3>
									</div>

									<div class="card-toolbar">
										<div class="dropdown dropdown-inline mr-2">
											<button type="button"
												class="btn btn-light-primary font-weight-bolder dropdown-toggle"
												data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<span class="svg-icon svg-icon-md">
													<!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
													<svg xmlns="http://www.w3.org/2000/svg"
														xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
														height="24px" viewBox="0 0 24 24" version="1.1">
														<g stroke="none" stroke-width="1" fill="none"
															fill-rule="evenodd">
															<rect x="0" y="0" width="24" height="24"></rect>
															<path
																d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
																fill="#000000" opacity="0.3"></path>
															<path
																d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
																fill="#000000"></path>
														</g>
													</svg>
													<!--end::Svg Icon-->
												</span>Export</button>
											<!--begin::Dropdown Menu-->
											<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right" style="">
												<!--begin::Navigation-->
												<ul class="navi flex-column navi-hover py-2">
													<li
														class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
														Choose an option:</li>

													<li class="navi-item">
														<a href="<?= BASE_URL ?>export-project.php" class="navi-link">
															<span class="navi-icon">
																<i class="la la-file-text-o"></i>
															</span>
															<span class="navi-text">XLS</span>
														</a>
													</li>

												</ul>
												<!--end::Navigation-->
											</div>
											<!--end::Dropdown Menu-->
										</div>
										<a href="<?= BASE_URL ?>add-project.php"
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
											</span>Add Project</a> &nbsp;

										<button type="submit" class="btn btn-success" name="push_to_app">
											<i class="bi bi-align-end"></i>Push To App
										</button>
									</div>
								</div>

								<!-- Filter Section -->
								<div class="card-body py-3 border-bottom">
									<div class="row align-items-center">
										<div class="col-md-3">
											<div class="form-group mb-0">
												<label class="font-weight-bold">Filter by Homepage Status:</label>
												<select id="homepageFilter1" class="form-control">
													<option value="all" <?= ($homepage_filter == 'all') ? 'selected' : '' ?>>All Projects</option>
													<option value="1" <?= ($homepage_filter == '1') ? 'selected' : '' ?>>Show on Homepage (Active)</option>
													<!--<option value="2" <?= ($homepage_filter == '2') ? 'selected' : '' ?>>Hide from Homepage</option>-->
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group mb-0">
												<label class="font-weight-bold">&nbsp;</label>
												<button id="clearFilter" class="btn btn-secondary form-control">Clear Filter</button>
											</div>
										</div>
									</div>
								</div>

								<div class="card-body">
									<table id="example" class="table table-bordered dataTable" style="width:100%">
										<thead>
											<tr>
												<th>Select</th>
												<th>App Sync</th>
												<th>ID</th>
												<th>Project Name</th>
												<th>Type</th>
												<th>Community</th>
												<th>Developer</th>
												<th>Bedroom's</th>
												<th>Status</th>
												<th>Actions</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$key = 0;
											
											// Modified query with filter condition
											$query_sql = "SELECT tp.*, 
												tc.comm_heading AS proj_comm_name, 
												td.dev_heading AS proj_dev_name, 
												ty.proj_type_name AS proj_type 
											FROM tbl_proj AS tp 
											LEFT JOIN tbl_developer AS td ON tp.prj_dev_id = td.dev_id 
											LEFT JOIN tbl_proj_type AS ty ON tp.prj_type_id = ty.prj_type_id 
											LEFT JOIN tbl_community AS tc ON tp.prj_comm_id = tc.comm_id 
											WHERE tp.prj_type_id IN (1, 2, 3, 4)";
											
											// Apply filter if not 'all'
											if ($homepage_filter !== 'all') {
												$query_sql .= " AND tp.proj_showonhomepage = " . intval($homepage_filter);
											}
											
											$query_sql .= " ORDER BY tp.tstp DESC";
											
											$query = mysqli_query($conn, $query_sql);

											while ($row = mysqli_fetch_array($query)) {
												?>
												<tr>
													<td>
														<input type="checkbox" name="project_ids[]"
															value="<?= $row['prj_id'] ?>">
													</td>
													<td>
														<?php
														if (isset($row['app_sync_status']) && $row['app_sync_status'] == 1) {
															echo '<span class="badge badge-success">Pushed</span>';
															echo '<br><small>' .
																date('d M Y h:i A', strtotime($row['app_sync_date'])) .
																'</small>';
														} else {
															echo '<span class="badge badge-warning">Pending</span>';
														}
														?>
													</td>
													<td><?= ++$key ?></td>
													<td>
														<?= htmlspecialchars($row['proj_name']) ?>
														<br>
														<?php
														if($row['proj_showonhomepage'] == 1){
																echo '<span class="badge badge-pill badge-success">On Home Active</span>';
															}
														?>
														</td>
														<td><?= htmlspecialchars($row['proj_type']) ?></td>
														<td><?= htmlspecialchars($row['proj_comm_name']) ?></td>
														<td><?= htmlspecialchars($row['proj_dev_name']) ?></td>
														<td><?= htmlspecialchars($row['proj_bedroom']) ?></td>
														<td><span class="label label-lg font-weight-bold <?= ($row['is_active'] == 1 ? 'label-light-success' : 'label-light-danger') ?> label-inline"><?= ($row['is_active'] == 1 ? 'Active' : 'Inactive') ?></span></td>
														<td>
														<?php
														$slug = $row['proj_slug'];
														$type = strtolower($row['proj_type']);
														if ($type === 'off plan') {
															$view_url = BASE_URL_HOME . 'property/' . $slug;
														} elseif ($type === 'international property') {
															$view_url = BASE_URL_HOME . 'international-properties/' . $slug;
														} elseif ($type === 'ready to move in') {
															$view_url = BASE_URL_HOME . 'ready-to-move-in/' . $slug;
														} else {
															$view_url = BASE_URL_HOME . 'property/' . $slug;
														}
														?>
														<div class="dropdown">
															<button type="button" class="btn btn-sm btn-light-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																Actions
															</button>
															<div class="dropdown-menu dropdown-menu-right">
																<a class="dropdown-item" href="<?= BASE_URL ?>edit-project.php?edit=<?= base64_encode($row['prj_id']) ?>">
																	<i class="la la-edit mr-1"></i>Edit
																</a>
																<a class="dropdown-item" href="<?= $view_url ?>" target="_blank">
																	<i class="la la-eye mr-1"></i>View on Site
																</a>
																<div class="dropdown-divider"></div>
																<a class="dropdown-item" href="<?= BASE_URL ?>manage-gallery.php?proj_id=<?= $row['prj_id'] ?>">
																	<i class="la la-image mr-1"></i>Manage Gallery
																</a>
																<a class="dropdown-item" href="<?= BASE_URL ?>manage-faq.php?proj_id=<?= $row['prj_id'] ?>">
																	<i class="la la-question-circle mr-1"></i>Manage Faq
																</a>
																<a class="dropdown-item" href="<?= BASE_URL ?>manage-payplan.php?proj_id=<?= $row['prj_id'] ?>">
																	<i class="la la-calendar mr-1"></i>Manage Payplan
																</a>
																<div class="dropdown-divider"></div>
																<a class="dropdown-item text-danger del_rec" href="<?= BASE_URL ?>manage-project.php?del=<?= base64_encode($row['prj_id']) ?>" data-url="<?= BASE_URL ?>manage-project.php?del=<?= $row['prj_id'] ?>">
																	<i class="la la-trash mr-1"></i>Delete
																</a>
															</div>
														</div>
														</td>
													</tr>
											<?php } ?>
										</tbody>
										<tfoot>
											<tr>
												<th>Select</th>
												<th>App Sync</th>
												<th>ID</th>
												<th>Project Name</th>
												<th>Type</th>
												<th>Community</th>
												<th>Developer</th>
												<th>Bedroom's</th>
												<th>Status</th>
												<th>Actions</th>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
							</form>
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

				<!-- jQuery Script - Moved AFTER footer-inner.php -->
				<script>
				// Use vanilla JavaScript instead of jQuery to avoid dependency
				document.addEventListener('DOMContentLoaded', function() {
				    // Handle filter change
				    var filterSelect = document.getElementById('homepageFilter1');
				    if (filterSelect) {
				        filterSelect.addEventListener('change', function() {
				            var filterValue = this.value;
				            var currentUrl = new URL(window.location.href);
				            
				            if (filterValue === 'all') {
				                currentUrl.searchParams.delete('homepage_filter');
				            } else {
				                currentUrl.searchParams.set('homepage_filter', filterValue);
				            }
				            
				            window.location.href = currentUrl.toString();
				        });
				    }
				    
				    // Clear filter button
				    var clearBtn = document.getElementById('clearFilter');
				    if (clearBtn) {
				        clearBtn.addEventListener('click', function() {
				            var currentUrl = new URL(window.location.href);
				            currentUrl.searchParams.delete('homepage_filter');
				            window.location.href = currentUrl.toString();
				        });
				    }
				});

				setTimeout(function() {
				    var successMsg = document.getElementById('successMsg');
				    if (successMsg) {
				        successMsg.style.display = 'none';
				    }
				}, 3000);
				</script>

<?php
// Don't add extra closing tags
?>