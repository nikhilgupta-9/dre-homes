<?php 
include('db/config.php');
include('includes/header.php');
include ('lock2.php');

$err[]='';
$id=base64_decode($_GET['edit']);
if(isset($_POST['update_mortg_content']))
{
    $mortg_cont_heading=prepStr($_POST['mortg_cont_heading']);
    $mortg_cont_sub_heading=prepStr($_POST['mortg_cont_sub_heading']);
	$mortg_fav_img='mortg_fav_img_'.rand(0,10000).'_'.$_FILES['mortg_fav_img']['name'];
	$mortg_fav_img_alt=prepStr($_POST['mortg_fav_img_alt']);
	$mortg_fav_cont=prepStr($_POST['mortg_fav_cont']);
	$mortg_frm_heading=prepStr($_POST['mortg_frm_heading']);
	$mortg_frm_sub_heading=prepStr($_POST['mortg_frm_sub_heading']);
	$mortg_adv_heading=prepStr($_POST['mortg_adv_heading']);
	$mortg_bank_heading=prepStr($_POST['mortg_bank_heading']);
	$is_active=(prepStr($_POST['is_active']!='' ? 1 : 2));


	$allowed_image_extension = array(
"png",
"jpg",
"jpeg"
);

$mortg_fav_file_extension = pathinfo($_FILES["mortg_fav_img"]["name"], PATHINFO_EXTENSION);
$mortg_bg_file_extension = pathinfo($_FILES["mortg_bg_img"]["name"], PATHINFO_EXTENSION);

if(!in_array($mortg_fav_file_extension,$allowed_image_extension) && !empty($_FILES['mortg_fav_img']['name']))
{
$err[]='Invalid Favicon Image. Only PNG,JPG and JPEG are allowed.';
}


if(!in_array($mortg_bg_file_extension,$allowed_image_extension) && !empty($_FILES['mortg_bg_img']['name']))
{
$err[]='Invalid Background Image. Only PNG,JPG and JPEG are allowed.';
}






if(!empty($_FILES['mortg_fav_img']['name']))
{
$mortg_fav_img='mortg_fav_img_'.rand(0,10000).'_'.$_FILES['mortg_fav_img']['name'];
}
else
{
$mortg_fav_img=$_POST['mortg_fav_img_hidden'];
}


if(!empty($_FILES['mortg_bg_img']['name']))
{
$mortg_bg_img='mortg_bg_img_'.rand(0,10000).'_'.$_FILES['mortg_bg_img']['name'];
}
else
{
$mortg_bg_img=$_POST['mortg_bg_img_hidden'];
}



if(count($err)==1)
{

	if(!empty($_FILES['mortg_fav_img']['name']))
	{
	move_uploaded_file($_FILES['mortg_fav_img']['tmp_name'], "assets/media/mortgage/$mortg_fav_img");
	unlink('assets/media/mortgage/'.$_POST['mortg_fav_img_hidden']);
	}

	if(!empty($_FILES['mortg_bg_img']['name']))
	{
	move_uploaded_file($_FILES['mortg_bg_img']['tmp_name'], "assets/media/mortgage/$mortg_bg_img");
	unlink('assets/media/mortgage/'.$_POST['mortg_bg_img_hidden']);
	}


	$query=mysqli_query($conn,"update tbl_mortgage_content
	                    SET mortg_cont_heading='$mortg_cont_heading',
	                        mortg_cont_sub_heading='$mortg_cont_sub_heading',
	                        mortg_fav_img='$mortg_fav_img',
	                        mortg_fav_img_alt='$mortg_fav_img_alt',
	                        mortg_fav_cont='$mortg_fav_cont',
	                        mortg_frm_heading='$mortg_frm_heading',
	                        mortg_frm_sub_heading='$mortg_frm_sub_heading',
	                        mortg_bg_img='$mortg_bg_img',
	                        mortg_adv_heading='$mortg_adv_heading',
	                        mortg_bank_heading='$mortg_bank_heading',
	                        is_active='$is_active',
	                        tstp='".$date."' where mortg_cont_id=$id");
	if($query)
	{

	header('Location:'.BASE_URL.'edit-mortgage-content.php?edit='.$_GET['edit'].'');
	}

}

}

$details=mysqli_fetch_array(mysqli_query($conn,"select * from tbl_mortgage_content where mortg_cont_id=$id"));
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
<h3 class="card-label">Mortgage Content</h3>
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
<input type="text" id="mortg_cont_heading" name="mortg_cont_heading" value="<?=$details['mortg_cont_heading']?>" class="form-control" placeholder="Heading">
</div>


	<div class="form-group">
<label for="exampleInputPassword1">Sub Heading
<span class="text-danger">*</span></label>
<input type="text" id="mortg_cont_sub_heading" name="mortg_cont_sub_heading" value="<?=$details['mortg_cont_sub_heading']?>" class="form-control" placeholder="Sub Heading">
</div>



<div class="form-group">
<label for="exampleInputPassword1">Favicon Image
<span class="text-danger">*</span></label>
<input type="file" id="mortg_fav_img" name="mortg_fav_img" value="" class="form-control">
<?php if(!empty($details['mortg_fav_img'])){?>
<img src="<?=BASE_URL?>assets/media/mortgage/<?=$details['mortg_fav_img']?>" width='50' class="mt-3">
<?php }?>
 <input type="hidden" name="mortg_fav_img_hidden" value="<?=$details['mortg_fav_img']?>">
</div>

<div class="form-group">
<label for="exampleInputPassword1">Favicon Image Alt Tag
<span class="text-danger">*</span></label>
<input type="text" id="mortg_fav_img_alt" name="mortg_fav_img_alt" value="<?=$details['mortg_fav_img_alt']?>" class="form-control" placeholder="Image Alt Tag">
</div>

<div class="form-group">
<label for="exampleInputPassword1">Favicon Content
<span class="text-danger">*</span></label>
<input type="text" id="mortg_fav_cont" name="mortg_fav_cont" value="<?=$details['mortg_fav_cont']?>" class="form-control" placeholder="Image Alt Tag">
</div>

<div class="form-group">
<label for="exampleInputPassword1">Form Heading
<span class="text-danger">*</span></label>
<input type="text" id="mortg_frm_heading" name="mortg_frm_heading" value="<?=$details['mortg_frm_heading']?>" class="form-control" placeholder="Form Heading">
</div>


<div class="form-group">
<label for="exampleInputPassword1">Form Sub Heading
<span class="text-danger">*</span></label>
<input type="text" id="mortg_frm_sub_heading" name="mortg_frm_sub_heading" value="<?=$details['mortg_frm_sub_heading']?>" class="form-control" placeholder="Form Sub Heading">
</div>


<div class="form-group">
<label for="exampleInputPassword1">Background Image
<span class="text-danger">*</span></label>
<input type="file" id="mortg_bg_img" name="mortg_bg_img" value="" class="form-control">
<?php if(!empty($details['mortg_bg_img'])){?>
<img src="<?=BASE_URL?>assets/media/mortgage/<?=$details['mortg_bg_img']?>" width='50' class="mt-3">
<?php }?>
 <input type="hidden" name="mortg_bg_img_hidden" value="<?=$details['mortg_bg_img']?>">
</div>



<div class="form-group">
<label for="exampleInputPassword1">Advisor Heading
<span class="text-danger">*</span></label>
<input type="text" id="mortg_adv_heading" name="mortg_adv_heading" value="<?=$details['mortg_adv_heading']?>" class="form-control" placeholder="Advisor Heading">
</div>

<div class="form-group">
<label for="exampleInputPassword1">Bank Heading
<span class="text-danger">*</span></label>
<input type="text" id="mortg_bank_heading" name="mortg_bank_heading" value="<?=$details['mortg_bank_heading']?>" class="form-control" placeholder="Bank Heading">
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
<button type="submit" name="update_mortg_content" class="btn btn-primary mr-2">Update</button>
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