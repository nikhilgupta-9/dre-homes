<?php
include('db/config.php');
$meta_tags=select_row('tbl_tags','id=17 and is_active=1');
include('common/header.php');
$page=1;
$sql=select_query('tbl_awards','is_active=1 ');
$count=mysqli_num_rows($sql);
$per_page=6;
$no_of_page=ceil($count/$per_page);
$start=($page-1)*$per_page;
$award_content=select_row('tbl_awards_content','is_active=1');
?>
    <!--warpper-->
    <div class="box-head-bg"></div>
    <div class="wrapper">
        <!--content-->
        <div class="content">
            <!--section-->
            <div class="section hero-section hero-section_sin">
                <div class="hero-section-wrap">
                    <div class="hero-section-wrap-item">
                        <div class="container">
                            <div class="hero-section-container">
                                <div class="hero-section-title">
                                    <h1><?=$award_content['award_cont_heading']?></h1>
                                </div>
                            </div>
                        </div>
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="<?=BASE_URL_IMG?>awards/<?=$award_content['award_cont_img']?>"
                                data-scrollax="properties: { translateY: '90%' }"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!--section-end-->
            <!--container-->
            <div class="container awards_pos">
                <!--breadcrumbs-list-->
                <div class="breadcrumbs-list bl_flat">
                    <a href="<?=BASE_URL?>">Home</a> <span>Our Awards</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
                </div>
                <!--breadcrumbs-list end-->
                <!--main-content-->
                <div class="main-content ">
                    <!--boxed-container-->
                    <div class="boxed-container">
                        <div class="row">

                            <div class="col-12">

                                <!--listing-item-container-->
                                <div class="listing-item-container fw-listing-item award-listing awards_data">
                                 
<?php
$awards=select_query('tbl_awards',"is_active=1 order by award_id desc LIMIT $start,$per_page");
$i=0;
while($row=mysqli_fetch_array($awards))
{
?>  
                                    <div class="listing-item award-list">
                                        <div class="geodir-category-listing">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="geodir-category-img">
                                                            <div class="">
                                                                <img src="<?=BASE_URL_IMG?>awards/<?=$row['award_img']?>" alt="<?=$row['award_img_alt']?>">
                                                            </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="geodir-category-content">
                                                        <h3><?=$row['award_heading']?></h3>
                                                        <?=$row['award_descp']?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php $i++;}?>

                                    
                                </div>
                                <!--listing-item-container end-->
                               <div class="pagination-wrap">
                    <div class="pagination">
                    <?php include('pagination/paging_awards.php');?>
                    </div>
                    </div>
                            </div>
                        </div>
                        <div class="limit-box"></div>
                    </div>
                    <!--boxed-container end-->
                </div>
                <!--main-content end-->
                <div class="to_top-btn-wrap">
                    <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                    <div class="svg-corner svg-corner_white" style="top:0;left:  -40px; transform: rotate(-90deg)">
                    </div>
                    <div class="svg-corner svg-corner_white" style="top:0;right: -40px; transform: rotate(-180deg)">
                    </div>
                </div>
            </div>
            <!-- container end-->
        </div>
        <!--content  end-->
        <?php include 'common/footer.php'; ?>