<?php 
include('db/config.php');
include('includes/header.php');
include ('lock2.php');


if(isset($_POST['add_guide']))
{

	$guide_heading=mysqli_real_escape_string($conn,$_POST['guide_heading']);
	$guide_short_description=mysqli_real_escape_string($conn,$_POST['guide_short_description']);
	$meta_title=mysqli_real_escape_string($conn,$_POST['meta_title']);
	$meta_keyword=mysqli_real_escape_string($conn,$_POST['meta_keyword']);
	$meta_description=mysqli_real_escape_string($conn,$_POST['meta_description']);
	$meta_tags=mysqli_real_escape_string($conn,$_POST['meta_tags']);
	$guide_slug=mysqli_real_escape_string($conn,$_POST['guide_slug']);
	$is_active=(prepStr($_POST['is_active']!='' ? 1 : 2));
	$guide_image=$_FILES['guide_image']['name'];
	$guide_head=$_POST['guide_head'];
	$guide_short_descp=$_POST['guide_short_descp'];

  




	$query=mysqli_query($conn,"insert into tbl_guide
						           SET guide_heading='$guide_heading',
						               guide_short_description='$guide_short_description',
						               meta_title='$meta_title',
						               meta_keyword='$meta_keyword',
						               meta_description='$meta_description',
						               meta_tags='$meta_tags',
						               guide_slug='$guide_slug',
						               is_active='$is_active'
	                                   ");



   $guid_id = mysqli_insert_id($conn);




    /*insert into tbl_guide_img start*/

    $count_guideimg=count($guide_image);

    for($i=0;$i<$count_guideimg;$i++)
    {

    $guide_heads=mysqli_real_escape_string($conn,$guide_head[$i]);
    $guide_short_descps=mysqli_real_escape_string($conn,$guide_short_descp[$i]);
    
    $guide_name_arr=$_FILES['guide_image']['name'];
    $guide_loc_arr=$_FILES['guide_image']['tmp_name'];


    $guide_image_name='guide_image_'.rand(0,10000).'_'.$guide_name_arr[$i];
    $guide_image_loc=$guide_loc_arr[$i];

    
    if($guide_name_arr[$i]!='')
    {


      move_uploaded_file($guide_image_loc, "assets/media/guide/$guide_image_name");
      $query=mysqli_query($conn,"insert into tbl_guide_img
                                                        SET guide_id='$guid_id',
                                                        guide_image='$guide_image_name',
                                                        guide_head='$guide_heads',
                                                        guide_short_descp='$guide_short_descps',
                                                        is_active='1',
                                                        tstp='$date'
                                                        ");
    }

    }

    /*insert into tbl_guide_img end*/


 

    if($query)
   {
      header('Location:manage-guide.php');
   }


	

}
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
<h3 class="card-label">Add Guide</h3>
</div>
<div class="card-toolbar">
<!--begin::Button-->
<a href="<?=BASE_URL?>manage-guide.php" class="btn btn-primary font-weight-bolder">
<span class="svg-icon svg-icon-md">
<!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
<rect x="0" y="0" width="24" height="24"></rect>
<circle fill="#000000" cx="9" cy="15" r="6"></circle>
<path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3"></path>
</g>
</svg>
<!--end::Svg Icon-->
</span>Go Back</a>
<!--end::Button-->
</div>
</div>
<div class="card-body">


<div class=" card-custom gutter-b example example-compact">


<!--begin::Form-->
<form action="" enctype="multipart/form-data" method="post"  id
="add-project">
<div class="card-body">



<div class="form-group">
<label for="exampleInputPassword1">Heading
<span class="text-danger">*</span></label>
<input type="text" name="guide_heading" value="" class="form-control" autocomplete="off" placeholder="Heading">
</div>



<div class="form-group">
<label for="exampleInputPassword1">Short Description
<span class="text-danger">*</span></label>
<textarea name="guide_short_description" class="form-control" placeholder="Short Description" aria-hidden="true" rows="10" ></textarea>
</div>

<div class="form-group mt-3">
<h3>Guide Image</h3>
<hr>


<div class="row guide_sec">
<div class="col-md-3">
<input type="file" name="guide_image[]" class="form-control">
</div>

<div class="col-md-3 form-group">
<input type="text" name="guide_head[]" class="form-control" placeholder="Heading">
</div>


<div class="col-md-3 form-group">
<textarea name="guide_short_descp[]" class="form-control" placeholder="Short Description"></textarea>
</div>



<div class="col-md-3">
<a href="" class="btn btn-primary add_more_guide_sec">Add more <i class="mdi  mdi-plus-circle-outline"></i></a>  
</div>

</div>

</div>



<div class="form-group">
<label for="exampleInputPassword1">Meta Title</label>
<input type="text" name="meta_title" value="" class="form-control" autocomplete="off" placeholder="Meta Title">
</div>

<div class="form-group">
<label for="exampleInputPassword1">Meta Keyword</label>
<input type="text" name="meta_keyword" value="" class="form-control" autocomplete="off" placeholder="Meta Keyword">
</div>

<div class="form-group">
<label for="exampleInputPassword1">Meta Description</label>
<textarea name="meta_description" class="form-control" placeholder="Meta Description" aria-hidden="true" rows="10"></textarea>
</div>

<div class="form-group">
<label for="exampleInputPassword1">Meta Tags</label>
<textarea name="meta_tags" class="form-control" placeholder="Meta Tags" aria-hidden="true" rows="10"></textarea>
</div>

<div class="form-group">
<label for="exampleInputPassword1">Slug</label>
<input type="text" name="guide_slug" value="" class="form-control" autocomplete="off" placeholder="Slug">
</div>



<div class="form-group">
<label for="exampleInputPassword1">Status</label>
<span class="switch switch-md switch-icon">
<label>
<input type="checkbox" name="is_active" value="1">
<span></span>
</label>
</span>
</div>
</div>
<div class="card-footer">
<button type="submit" name="add_guide" class="btn btn-primary mr-2">Add</button>
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