<?php 
include('db/config.php');
include('includes/header.php');
include ('lock2.php');

/*Delete record from tbl_point_of_interest start*/
$proj_id=intval($_GET['proj_id']);
if(isset($_GET['del']))
{

$id=intval($_GET['del']);
$img_name=mysqli_fetch_array(mysqli_query($conn,"select * from tbl_proj_gallery where proj_gallery_id=$id"));
unlink('assets/media/project/gallery/'.$img_name['gallery_img']);

$query=mysqli_query($conn,"delete from tbl_proj_gallery where proj_gallery_id='".$_GET['del']."'");

if($query)
{
   header('Location:manage-gallery.php?proj_id='.$_GET['id'].'');
}

}

/*Delete record from tbl_point_of_interest end*/

?>
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<!--begin::Main-->
		<!--begin::Header Mobile-->
		<div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
			<!--begin::Logo-->
			<a href="index.html">
				<img alt="Logo" src="<?=BASE_URL?>assets/media/logos/logo-light.png" />
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
						<!--begin::Svg Icon | path:<?=BASE_URL?>assets/media/svg/icons/General/User.svg-->
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
							<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
								<polygon points="0 0 24 0 24 24 0 24" />
								<path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
								<path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
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
				<?php include('includes/sidebar.php');?>
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
									<div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
										<span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
										<span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?=$_SESSION['username']?></span>
										<span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
											<span class="symbol-label font-size-h5 font-weight-bold"><?=substr($_SESSION['username'],0,1)?></span>
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
<h3 class="card-label">Manage Gallery</h3>
</div>
<div class="card-toolbar">
<a href="<?=BASE_URL?>add-gallery.php?proj_id=<?=$_GET['proj_id']?>" class="btn btn-primary font-weight-bolder">
<span class="svg-icon svg-icon-md">
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
<rect x="0" y="0" width="24" height="24"></rect>
<circle fill="#000000" cx="9" cy="15" r="6"></circle>
<path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3"></path>
</g>
</svg>
</span>Add Gallery</a>
</div>
</div>
<div class="card-body">
<table id="example" class="table table-bordered  dataTable " style="width:100%">
        <thead>
            <tr>
				<th>ID</th>
				<th>Image</th>
				<th>Image Alt Tag</th>
				<th>Status</th>
				<th>Date</th>
				<th>Edit</th>
				<th>Delete</th>
            </tr>
        </thead>
        <tbody>
		<?php 
		$key=0;
		$query=mysqli_query($conn,"select * from tbl_proj_gallery where proj_id=$proj_id");
		while($row=mysqli_fetch_array($query))
		{
		?>
		<tr>
		<td><?=++$key?></td>
		<td><img src="<?=BASE_URL?>assets/media/project/gallery/<?=$row['gallery_img']?>" width="100"></td>
        <td><?=$row['gallery_img_alt']?></td>
		<td><span class="label label-lg font-weight-bold 
		<?=($row['is_active']==1 ? 'label-light-success' : 'label-light-danger')?> label-inline"><?=($row['is_active']==1 ? 'Active' : 'Inactive')?></span></td>
		<td><?=date("M d,Y", strtotime($row['tstp']))?></td>
		<td><a href="<?=BASE_URL?>edit-gallery.php?edit=<?=$row['proj_gallery_id']?>&id=<?=$row['proj_id']?>" class="btn btn-warning">Edit</a></td>
		<td><a href="<?=BASE_URL?>manage-gallery.php?del=<?=$row['proj_gallery_id']?>&id=<?=$row['proj_id']?>" class="btn btn-danger del_rec" data-url="<?=BASE_URL?>manage-gallery.php?del=<?=$row['proj_gallery_id']?>&id=<?=$row['proj_id']?>">Delete</a></td>
		</tr>
		<?php } ?>  
        </tbody>
        <tfoot>
            <tr>
				<th>ID</th>
				<th>Image</th>
				<th>Image Alt Tag</th>
				<th>Status</th>
				<th>Date</th>
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