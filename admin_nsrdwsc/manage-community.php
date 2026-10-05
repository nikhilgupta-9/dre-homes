<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);

if (isset($_GET['del'])) {
	$id = intval($_GET['del']);
	$img_name = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_community where comm_id=$id"));
	unlink('assets/media/community/thumb/' . $img_name['comm_thumb_img']);
	unlink('assets/media/community/inner/' . $img_name['comm_inner_img']);
	unlink('assets/media/community/inner-thumb/' . $img_name['comm_inner_thumb_img']);
	unlink('assets/media/community/location/' . $img_name['comm_loc_main_img']);

	$old_data = $logger->getOldRecordData('tbl_community', $id);

	$logger->log(
		'DELETE',
		'tbl_community',
		$id,
		$old_data,
		null,
		'Community Deleted: ' . $delete_name
	);

	$delete_record = $logger->archiveDeletedRecord('tbl_community', $id, $old_data);

	$query = mysqli_query($conn, "delete from tbl_community where comm_id=$id");

	if ($query) {
		header('Location:' . BASE_URL . 'manage-community.php');
	}

}

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

				<?php
				if (isset($_GET['msg']) && $_GET['msg'] == 'push_success') {
					?>
					<div id="successMsg" class="alert alert-success mx-5">
						Community pushed to app successfully.
					</div>
				<?php } ?>
				<!--begin::Content-->
				<div class="d-flex flex-column flex-column-fluid" id="kt_content">
					<!--begin::Entry-->
					<div class="d-flex flex-column-fluid">
						<!--begin::Container-->
						<div class="container">

							<!--begin::Card-->
							<form method="POST" action="<?= BASE_URL ?>push-app/push-community-app.php">
								<div class="card card-custom gutter-b">
									<div class="card-header flex-wrap py-3">
										<div class="card-title">
											<h3 class="card-label">Manage Community</h3>
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
												<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right"
													style="">
													<!--begin::Navigation-->
													<ul class="navi flex-column navi-hover py-2">
														<li
															class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
															Choose an option:</li>

														<li class="navi-item">
															<a href="<?= BASE_URL ?>export-community.php"
																class="navi-link">
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
											<a href="<?= BASE_URL ?>add-community.php"
												class="btn btn-primary font-weight-bolder">
												<span class="svg-icon svg-icon-md">
													<svg xmlns="http://www.w3.org/2000/svg"
														xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
														height="24px" viewBox="0 0 24 24" version="1.1">
														<g stroke="none" stroke-width="1" fill="none"
															fill-rule="evenodd">
															<rect x="0" y="0" width="24" height="24"></rect>
															<circle fill="#000000" cx="9" cy="15" r="6"></circle>
															<path
																d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z"
																fill="#000000" opacity="0.3"></path>
														</g>
													</svg>
												</span>Add Community</a> &nbsp;

											<button type="submit" class="btn btn-success" name="push_to_app">
												<i class="bi bi-align-end"></i>Push To App
											</button>
										</div>
									</div>
									<div class="card-body">

										<table id="example" class="table table-bordered  dataTable " style="width:100%">
											<thead>
												<tr>
													<th>Select</th>
													<th>App Sync</th>
													<th>ID</th>
													<th>Thumb Image</th>
													<th>Heading</th>
													<th>Location</th>
													<th>Loc</th>
													<th>Map</th>
													<th>View</th>
													<th>Status</th>
													<th>Edit</th>
													<th>Delete</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$key = 0;
												$query = mysqli_query($conn, "select * from tbl_community order by comm_id desc");
												while ($row = mysqli_fetch_array($query)) {
													?>
													<tr>
														<td>
															<input type="checkbox" name="community_ids[]"
																value="<?= $row['comm_id'] ?>">
														</td>
														<td>
															<?php
															if ($row['app_sync_status'] == 1) {

																echo '<span class="badge badge-success">
            Pushed
          </span>';

																echo '<br><small>' .
																	date('d M Y h:i A', strtotime($row['app_sync_date'])) .
																	'</small>';

															} else {

																echo '<span class="badge badge-warning">
            Pending
          </span>';

															}
															?>
														</td>
														<td><?= ++$key ?></td>
														<td><img src="<?= BASE_URL ?>assets/media/community/thumb/<?= $row['comm_thumb_img'] ?>"
																width="50"></td>
														<td>
															<?= $row['comm_heading'] ?><br>
															<?php
															if (!empty($row['comm_showonhomepage'] == 1)) {
																echo '<span class="badge badge-pill badge-success">OnHome</span>';
															}
															?>

														</td>
														<td><?= $row['comm_location'] ?></td>
														<td><a
																href="<?= BASE_URL ?>manage-comm-loc.php?comm_id=<?= $row['comm_id'] ?>">Manage</a>
														</td>
														<td><a
																href="<?= BASE_URL ?>manage-comm-map.php?comm_id=<?= $row['comm_id'] ?>">Manage</a>
														</td>
														<td><a href="<?= BASE_URL_HOME ?>community/<?= $row['comm_slug'] ?>"
																target="_blank">View</a></td>
														<td><span
																class="label label-lg font-weight-bold 
		<?= ($row['is_active'] == 1 ? 'label-light-success' : 'label-light-danger') ?> label-inline"><?= ($row['is_active'] == 1 ? 'Active' : 'Inactive') ?></span>
														</td>
														<td><a href="<?= BASE_URL ?>edit-community.php?edit=<?= base64_encode($row['comm_id']) ?>"
																class="btn btn-warning">Edit</a></td>
														<td><a href="<?= BASE_URL ?>manage-community.php?del=<?= base64_encode($row['comm_id']) ?>"
																data-url="<?= BASE_URL ?>manage-community.php?del=<?= $row['comm_id'] ?>"
																class="btn btn-danger del_rec">Delete</a></td>
													</tr>
												<?php } ?>
											</tbody>
											<tfoot>
												<tr>
													<th>Select</th>
													<th>App Sync</th>
													<th>ID</th>
													<th>Thumb Image</th>
													<th>Heading</th>
													<th>Location</th>
													<th>Loc</th>
													<th>Map</th>
													<th>View</th>
													<th>Status</th>
													<th>Edit</th>
													<th>Delete</th>
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
				<script>
					setTimeout(function () {
						document.getElementById('successMsg').style.display = 'none';
					}, 3000);
				</script>
				<?php
				include('includes/footer-inner.php');
				?>