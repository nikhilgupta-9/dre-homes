<?php
include('db/config.php');
$meta_tags=select_row('tbl_tags','id=24 and is_active=1');
include('common/header.php');
$page=1;
$sql=select_query('tbl_media','is_active=1 ');
$count=mysqli_num_rows($sql);
$per_page=6;
$no_of_page=ceil($count/$per_page);
$start=($page-1)*$per_page;
$media_details = select_row('tbl_media_content', 'is_active=1');
?>
    <!--warpper-->
    <div class="wrapper gallery-box">
        <div class="content">
            <!--section-->
            <div class="section hero-section hero-section_sin">
                <div class="hero-section-wrap">
                    <div class="hero-section-wrap-item">
                        <div class="container">
                            <div class="hero-section-container">
                                <div class="hero-section-title">
                                    <h1><?=$media_details['media_heading']?></h1>
                                </div>
                            </div>
                        </div>
                       
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="<?=BASE_URL_IMG?>gallery/<?=$media_details['media_img']?>"
                                data-scrollax="properties: { translateY: '30%' }"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!--section-end-->
            <div class="container">
                <div class="breadcrumbs-list bl_flat">
                    <a href="<?=BASE_URL?>">Home</a><span>Our Media</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
                </div>
                <div class="main-content">
                    <div class="boxed-container gallery-container">
                        <div class="row">
                            <div class="col-lg-3">
                                <!--boxed-container-->
                                <div class="sb-container gallery-head-c">
                                    <!--boxed-content-->
                                    <div class="boxed-content p-5 d-flex justify-content-center align-items-center">
                                        <!--boxed-content-title-->
                                        <div class="gb-title-wrap">
                                            <div class="gb-title">
                                                <h3><?=$media_details['media_title']?></h3>
                                                <p><?=$media_details['media_sub_title']?></p>
                                            </div>
                                        </div>
                                        <!--boxed-content-title end-->
                                    </div>
                                    <!--boxed-content end-->
                                </div>
                            </div>
                            <div class="col-lg-9 media_pos">
                                <div class="scroll-content-wrap">
                                    <div class="boxed-content-item">
                                        <div
                                            class="gallery-items gisp  grid-small-pad  list-single-gallery three-coulms lightgallery media_data" id="media-gallery-container">
                                                <?php
                    $query=select_query('tbl_media',"is_active=1 order by media_id  desc LIMIT $start,$per_page");
                    if($count>0)
                    {
                    while($row=mysqli_fetch_array($query))
                    {
                    ?> 
                                            <div class="gallery-item" >
                                                <div class="grid-item-holder">
                                                    <a href="<?=BASE_URL_IMG?>media-image/<?=$row['media_img']?>" class=" popup-image"><img src="<?=BASE_URL_IMG?>media-image/<?=$row['media_img']?>" alt=""></a>
                                                    
                                                </div>
                                            </div>
                                            <?php }}else{?>
                    <h2>No record found</h2>
                    <?php } ?>
                                            
                                        </div>


                    <div class="pagination-wrap">
                    <div class="pagination">
                    <?php include('pagination/paging_media.php');?>
                    </div>
                    </div>
                                      
                                    </div>
                                    <!--boxed-content-item end-->
                                </div>
                            </div>

                        </div>
                        <div class="limit-box"></div>
                    </div>
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