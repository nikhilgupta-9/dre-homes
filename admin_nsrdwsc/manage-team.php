<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

$logger = new Logger($conn);

if (isset($_GET['del'])) {
	$id = intval($_GET['del']);
	$img_name = mysqli_fetch_array(mysqli_query($conn, "select * from tbl_team where team_id=$id"));

	$old_data = $logger->getOldRecordData('tbl_team', $id);

	$logger->log(
		'DELETE',
		'tbl_team',
		$id,
		$old_data,
		null,
		'Team Member Deleted: ' . $id
	);

	$delete_record = $logger->archiveDeletedRecord('tbl_team', $id, $old_data);

	unlink('assets/media/team/' . $img_name['team_img']);
	unlink('assets/media/team/' . $img_name['team_inner_img']);


	$query = mysqli_query($conn, "delete from tbl_team where team_id=$id");

	if ($query) {
		header('Location:' . BASE_URL . 'manage-team.php');
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
							<div class="card card-custom gutter-b">
								<div class="card-header flex-wrap py-3">
									<div class="card-title">
										<h3 class="card-label">Manage Team</h3>
									</div>

									<div class="card-toolbar">
										<div class="dropdown dropdown-inline mr-2">
											<button type="button"
												class="btn btn-light-primary font-weight-bolder dropdown-toggle"
												data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<span class="svg-icon svg-icon-md">
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
												</span>Export</button>
											<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
												<ul class="navi flex-column navi-hover py-2">
													<li
														class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
														Choose an option:</li>
													<li class="navi-item">
														<a href="<?= BASE_URL ?>export-team.php" class="navi-link">
															<span class="navi-icon">
																<i class="la la-file-text-o"></i>
															</span>
															<span class="navi-text">XLS</span>
														</a>
													</li>
												</ul>
											</div>
										</div>
										<a href="<?= BASE_URL ?>add-team.php"
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
											</span>Add Team</a>
									</div>
								</div>

								<!-- Filter Controls -->
								<div class="card-body pb-0">
									<div class="row mb-4">
										<div class="col-md-3">
											<label class="form-label">Filter by Status:</label>
											<select id="statusFilter" class="form-control">
												<option value="">All</option>
												<option value="1">Active</option>
												<option value="2">Inactive</option>
											</select>
										</div>
										<div class="col-md-3">
											<label class="form-label">Filter by Designation:</label>
											<select id="designationFilter" class="form-control">
												<option value="">All</option>
												<?php
												$desg_query = mysqli_query($conn, "SELECT team_desg_id, team_desg_name FROM tbl_team_desg WHERE team_desg_name != '' ORDER BY team_desg_name ASC");
												while ($d = mysqli_fetch_assoc($desg_query)) {
													?>
													<option value="<?= $d['team_desg_id'] ?>">
														<?= htmlspecialchars($d['team_desg_name']) ?>
													</option>
													<?php
												}
												?>
											</select>
										</div>
										<div class="col-md-3">
											<label class="form-label">Search:</label>
											<input type="text" id="searchFilter"
												placeholder="Search by name, designation..." class="form-control">
										</div>
										<div class="col-md-3">
											<label class="form-label">&nbsp;</label>
											<button id="clearFilters" class="btn btn-secondary form-control">Clear All
												Filters</button>
										</div>
									</div>
								</div>

								<!-- Stats Cards -->
								<div class="card m-3 p-3">
									<div class="row align-items-end">
										<div class="col-md-3">
											<div class="card text-center bg-primary text-white">
												<div class="card-body p-2">
													<h6>Total</h6>
													<h4><?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tbl_team")) ?>
													</h4>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="card text-center bg-primary text-white">
												<div class="card-body p-2">
													<h6>Active</h6>
													<h4><?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tbl_team WHERE is_active=1")) ?>
													</h4>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="card text-center bg-primary text-white">
												<div class="card-body p-2">
													<h6>Inactive</h6>
													<h4><?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tbl_team WHERE is_active=2")) ?>
													</h4>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="card text-center bg-primary text-white">
												<div class="card-body p-2">
													<h6>Designations</h6>
													<h4><?= mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT team_desg FROM tbl_team")) ?>
													</h4>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="card-body">
									<table id="teamTable" class="table table-bordered dataTable" style="width:100%">
										<thead>
											<tr>
												<th>ID</th>
												<th>Order No.</th>
												<th>Image</th>
												<th>Name</th>
												<th>Designation</th>
												<th>Date Added</th>
												<th>View</th>
												<th>Status</th>
												<th>Edit</th>
												<th>Delete</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$key = 0;
											$query = mysqli_query($conn, "select * from tbl_team order by team_id desc");
											while ($row = mysqli_fetch_array($query)) {
												?>
												<!-- <tr data-status="<?= $row['is_active'] ?>" data-designation="<?= htmlspecialchars($row['team_desg']) ?>" data-name="<?= htmlspecialchars(strtolower($row['team_name'])) ?>" data-dname="<?= htmlspecialchars(strtolower($row['team_desg'])) ?>"> -->
												<tr data-status="<?= $row['is_active'] ?>"
													data-designation="<?= htmlspecialchars($row['team_desg']) ?>"
													data-desig-id="<?= $row['team_desg_id'] ?>"
													data-name="<?= htmlspecialchars(strtolower($row['team_name'])) ?>"
													data-dname="<?= htmlspecialchars(strtolower($row['team_desg'])) ?>">
													<td><?= ++$key ?></td>
													<td>
														<form action="" method="post"
															id="update_team_order_<?= $row['team_id'] ?>"
															class="d-flex align-items-center gap-2">
															<input type="hidden" name="team_id"
																value="<?= $row['team_id'] ?>">
															<input type="hidden" name="team_desg_id"
																value="<?= $row['team_desg_id'] ?>">
															<input type="number" value="<?= $row['order_no'] ?? '' ?>"
																name="order_no"
																class="form-control form-control-sm text-center"
																style="width:80px;" placeholder="Order">
															<button type="submit" name="update_order_numbers"
																class="btn btn-sm btn-primary">✓</button>
														</form>
													</td>
													<td><img src="<?= BASE_URL ?>assets/media/team/<?= $row['team_img'] ?>"
															width="50"></td>
													<td>
														<?= htmlspecialchars($row['team_name']) ?>

														<?php
														if ($row['is_time_square_team'] == 1) {
															echo '<span class="badge badge-pills badge-success">Time square Team</span>';
														}
														?>

													</td>
													<td>
														<?= htmlspecialchars($row['team_desg']) ?>
													</td>
													<td><?= date('d/m/Y H:i:s', strtotime($row['tstp'])) ?></td>
													<td><a href="<?= BASE_URL_HOME ?>about-us/" target="_blank">View</a>
													</td>
													<td><span
															class="label label-lg font-weight-bold <?= ($row['is_active'] == 1 ? 'label-light-success' : 'label-light-danger') ?> label-inline"><?= ($row['is_active'] == 1 ? 'Active' : 'Inactive') ?></span>
													</td>
													<td><a href="<?= BASE_URL ?>edit-team.php?edit=<?= base64_encode($row['team_id']) ?>"
															class="btn btn-warning">Edit</a></td>
													<td><a href="<?= BASE_URL ?>manage-team.php?edit=<?= base64_encode($row['team_id']) ?>"
															data-url="<?= BASE_URL ?>manage-team.php?del=<?= $row['team_id'] ?>"
															class="btn btn-danger del_rec">Delete</a></td>
												</tr>
											<?php } ?>
										</tbody>
										<tfoot>
											<tr>
												<th>ID</th>
												<th>Order No.</th>
												<th>Image</th>
												<th>Name</th>
												<th>Designation</th>
												<th>Date Added</th>
												<th>View</th>
												<th>Status</th>
												<th>Edit</th>
												<th>Delete</th>
											</tr>
										</tfoot>
									</table>
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

				<script>
					$(document).ready(function () {
						// Initialize DataTable with pagination
						var table = $('#teamTable').DataTable({
							"pageLength": 10,
							"order": [
								[0, 'asc']
							],
							"language": {
								"paginate": {
									"previous": "Previous",
									"next": "Next"
								}
							}
						});

						// Custom filter function
						$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
							var status = $('#statusFilter').val();
							var designationId = $('#designationFilter').val();
							var search = $('#searchFilter').val().toLowerCase();

							var row = table.row(dataIndex).node();
							var rowStatus = $(row).data('status').toString();
							var rowDesignationId = $(row).data('desig-id');
							var rowName = $(row).data('name');
							var rowDname = $(row).data('dname');

							// Status filter
							if (status !== '' && rowStatus !== status) {
								return false;
							}

							// Designation filter (compare by ID)
							if (designationId !== '' && rowDesignationId != designationId) {
								return false;
							}

							// Search filter
							if (search !== '') {
								if (rowName.indexOf(search) === -1 && rowDname.indexOf(search) === -1) {
									return false;
								}
							}

							return true;
						});

						// Event listeners
						$('#statusFilter, #designationFilter, #searchFilter').on('keyup change', function () {
							table.draw();
						});

						// Clear filters
						$('#clearFilters').on('click', function () {
							$('#statusFilter').val('');
							$('#designationFilter').val('');
							$('#searchFilter').val('');
							table.draw();
						});
					});
				</script>