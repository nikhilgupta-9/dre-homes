<?php 
include('db/config.php');
include('includes/header.php');
include ('lock2.php');

$err[]='';
$id=base64_decode($_GET['edit']);
if(isset($_POST['update_test_content']))
{
    $test_heading=prepStr($_POST['test_heading']);
	$test_img='test_img_'.rand(0,10000).'_'.$_FILES['test_img']['name'];
	$is_active=(prepStr($_POST['is_active']!='' ? 1 : 2));



$allowed_image_extension = array(
"png",
"jpg",
"jpeg"
);

$test_file_extension = pathinfo($_FILES["test_img"]["name"], PATHINFO_EXTENSION);

if(!in_array($test_file_extension,$allowed_image_extension) && !empty($_FILES['test_img']['name']))
{
$err[]='Invalid Image. Only PNG,JPG and JPEG are allowed.';
}



	if(!empty($_FILES['test_img']['name']))
{
$test_img='test_img_'.rand(0,10000).'_'.$_FILES['test_img']['name'];
}
else
{
$test_img=$_POST['test_img_hidden'];
}

if(count($err)==1)
{

	if(!empty($_FILES['test_img']['name']))
	{
	move_uploaded_file($_FILES['test_img']['tmp_name'], "assets/media/testimonial/$test_img");
	unlink('assets/media/testimonial/'.$_POST['test_img_hidden']);
	}

	$query=mysqli_query($conn,"update tbl_testimonial_content
	                    SET test_heading='$test_heading',
	                        test_img='$test_img',
	                        is_active='$is_active',
	                         tstp='".$date."' where test_cont_id=$id");
	if($query)
	{

	header('Location:'.BASE_URL.'edit-testimonial-content.php?edit='.$_GET['edit'].'');
	}

}

}

$details=mysqli_fetch_array(mysqli_query($conn,"select * from tbl_testimonial_content where test_cont_id=$id"));
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
					<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
				
						<!--begin::Entry-->
					<div class="d-flex flex-column-fluid">
<!--begin::Container-->
<div class="container">

<!--begin::Card-->
<div class="card card-custom gutter-b">
<div class="card-header flex-wrap py-3">
<div class="card-title">
<h3 class="card-label">Testimonial Content</h3>
</div>
</div>
<div class="card-body">
<?php if(count($err)>1){?>
<ul class="err_list">
<?php foreach($err as $list_err){ ?>
<li><?=$list_err?></li>
<?php } ?>
</ul>
<?php }?>

<div class=" card-custom gutter-b example example-compact">


<!--begin::Form-->
<form action="" enctype="multipart/form-data" method="post"  id
="edit-testimonial-content">
<div class="card-body">


	<div class="form-group">
<label for="exampleInputPassword1">Heading
<span class="text-danger">*</span></label>
<input type="text" id="test_heading" name="test_heading" value="<?=$details['test_heading']?>" class="form-control" placeholder="Heading">
</div>

<div class="form-group">
<label for="exampleInputPassword1">Image
<span class="text-danger">*</span></label>
<input type="file" id="test_img" name="test_img" value="" class="form-control">
<?php if(!empty($details['test_img'])){?>
<img src="<?=BASE_URL?>assets/media/testimonial/<?=$details['test_img']?>" width='50' class="mt-3">
<?php }?>
 <input type="hidden" name="test_img_hidden" value="<?=$details['test_img']?>">
</div>


<div class="form-group">
<label for="exampleInputPassword1">Status</label>
<span class="switch switch-md switch-icon">
<label>
<input type="checkbox" name="is_active" value="1" <?=($details['is_active']==1 ? 'checked' : '')?>>
<span></span>
</label>
</span>
</div>

</div>
<div class="card-footer">
<button type="submit" name="update_test_content" class="btn btn-primary mr-2">Update</button>
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