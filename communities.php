<?php
include('db/config.php');
$meta_tags = select_row('tbl_tags', 'id=12 and is_active=1');
include('common/header.php');
$page = 1;
$sql = select_query('tbl_community', 'comm_featured=2 and is_active=1 ');
$count = mysqli_num_rows($sql);
$per_page = 16;
$no_of_page = ceil($count / $per_page);
$start = ($page - 1) * $per_page;
$comm_content = select_row('tbl_community_content', 'is_active=1');
?>
<!--warpper-->
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
                                <h1><?= $comm_content['comm_cont_heading'] ?></h1>
                                <!-- <h5>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut nec tincidunt arcu,
                                        sit amet fermentum sem.</h5> -->
                            </div>
                        </div>
                    </div>

                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                        <div class="bg" data-bg="<?= BASE_URL_IMG ?>community/<?= $comm_content['comm_cont_img'] ?>"
                            data-scrollax="properties: { translateY: '30%' }"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--section-end-->

        <!--container-->
        <div class="container">
            <!--breadcrumbs-list-->
            <div class="breadcrumbs-list bl_flat">
                <a href="<?= BASE_URL ?>">Home</a><span>Community</span>
                <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
            </div>
            <?php
            $comm_feat_query = select_query('tbl_community', "comm_featured=1 and is_active=1 ORDER BY comm_posted_on desc");
            $count_feat_query = mysqli_num_rows($comm_feat_query);

            ?>

<!-- featured community new section starts -->
            <div class="boxed-container">
            <div class="exc-carousel">
                            <h4>Featured Communities -  Live Your Best</h4>
                        </div>
                <div class="listing-item-container three-columns-grid fw-listing-item fw-listing-item2 mt-1">
                <?php
                $query=select_query('tbl_community','comm_featured=1 and is_active=1');
                while($row=mysqli_fetch_array($query))
                {

                    $comm_id = (int)$row['comm_id'];
                    $prop_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM tbl_proj WHERE prj_type_id=1 and FIND_IN_SET($comm_id, proj_comm_feat) AND is_active = 1");
                    $prop_data = mysqli_fetch_assoc($prop_query);
                    $prop_count = $prop_data['total'];                ?>
                    <div class="listing-item dev-list comm-list-n">
                        <div class="geodir-category-listing">
                            <div class="row">
                                <div class="geodir-category-img">
                                    <a href="<?=BASE_URL?>offplan-properties/?comm_id=<?=$row['comm_id']?>" class="geodir-category-img_item">
                                        <div class="">
                                            <img src="<?=BASE_URL_IMG?>community/thumb/<?=$row['comm_thumb_img']?>" alt="/<?=$row['comm_thumb_img_alt']?>">
                                        </div>
                                    </a>
                                </div>
                                <div class="geodir-category-content">
                                    <h3><a href="<?=BASE_URL?>offplan-properties/?comm_id=<?=$row['comm_id']?>"><?=$row['comm_heading']?></a></h3>
                                    <?=$row['comm_long_descp']?>
                                </div>
                            </div>
                        </div>
                        <div class="geodir-category-footer">
                            <div class="geodir-category-content-details">
                                <ul>
                                    <li><i class="fa-light fa-home"></i><span><?=$prop_count?> Properties</span></li>
                                </ul>
                            </div>
                            
                        </div>
                    </div>
                <?php }?>
                </div>
            </div>
            <!-- featured community new section ends -->


            <!--breadcrumbs-list end-->

            <div class="boxed-container comm-search-box">
                <div class="show-mob-filter"><i class="far fa-location"></i> Search Your Desired Location</div>
                <!-- list-searh-input-wrap-->
                <div class="list-searh-input-wrap box_list-searh-input-wrap lws_mobile lsw_mb-btn">
                    <div class="close_mob-filter cmf"><i class="fa-regular fa-xmark"></i></div>
                    <div class="list-searh-input-wrap-title_wrap">
                        <div class="list-searh-input-wrap-title"><span><i class="far fa-location"></i>Search Your
                                Desired Location</span></div>
                        <!-- <div class="list-searh-input-radio_wrap">
                                <div class="header-search-radio">
                                    <input class="hidden radio-label" type="radio" name="accept-offers2"
                                        id="sale-button2" checked="checked">
                                    <label class="button-label" for="sale-button2">Sale</label>
                                    <input class="hidden radio-label" type="radio" name="accept-offers2"
                                        id="rent-button2">
                                    <label class="button-label" for="rent-button2">Rent</label>
                                    <input class="hidden radio-label" type="radio" name="accept-offers2"
                                        id="comm-button2">
                                    <label class="button-label" for="comm-button2">Commercial</label>
                                </div>
                            </div> -->
                    </div>
                    <div class="custom-form">
                        <div class="row">
                            <!-- listsearch-input-item -->
                            <div class="col-lg-12">
                                <div class="cs-intputwrap">
                                    <i class="fa-light fa-search"></i>
                                    <input type="text" name="search_comm" placeholder="Search" value="">
                                </div>
                            </div>
                            <!-- listsearch-input-item -->

                        </div>
                    </div>
                </div>
            </div>
            <!--main-content-->
            <div class="main-content">
                <!--boxed-container-->
                <div class="boxed-container">
                    <!---add-filter----->
                    <div class="row community_pos">
                        <div class="col-lg-12">
                            <!-- list-main-wrap-header-->
                            <div class="list-main-wrap-header box-list-header" style="margin-top: 0">
                                <!-- list-main-wrap-title-->
                                <div class="list-main-wrap-title">
                                    <h2>Results For : <span class="search_name"></span><strong
                                            class="search_count"><?= $count ?></strong></h2>
                                </div>
                                <!-- list-main-wrap-title end-->

                            </div>
                            <!-- list-main-wrap-header end-->
                            <!--listing-item-container-->
                            <div class="listing-item-container comm-listing-box community_data">
                                <?php
                                $comm = select_query('tbl_community', "comm_featured=2 and is_active=1 order by comm_posted_on  desc LIMIT $start,$per_page");
                                if ($count > 0) {
                                    while ($row = mysqli_fetch_array($comm)) {

                                        ?>
                                        <div class="comm-list-item">
                                            <!-- listing-item -->
                                            <div class="listing-item">
                                                <div class="geodir-category-listing">
                                                    <div class="geodir-category-img">
                                                        <a href="<?= BASE_URL ?>community/<?= $row['comm_slug'] ?>"
                                                            class="geodir-category-img_item">
                                                            <div class="comm-img-box">
                                                                <img src="<?= BASE_URL_IMG ?>community/thumb/<?= $row['comm_thumb_img'] ?>"
                                                                    alt="<?= $row['comm_thumb_img_alt'] ?>">
                                                            </div>
                                                            <div class="overlay"></div>
                                                        </a>
                                                    </div>
                                                    <div class="geodir-category-content">
                                                        <h3><a
                                                                href="<?= BASE_URL ?>community/<?= $row['comm_slug'] ?>"><?= $row['comm_heading'] ?></a>
                                                        </h3>
                                                        <div class="geodir-category-content_price text-muted">
                                                            <?= $row['comm_location'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- listing-item end-->
                                        </div>
                                    <?php }
                                } else { ?>
                                    <h2>No record found</h2>
                                <?php } ?>

                            </div>
                            <!--listing-item-container end-->
                            <div class="pagination-wrap">
                                <div class="pagination">
                                    <?php include('pagination/paging_community.php'); ?>
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