<?php 
include('db/config.php');
$detail=str_replace('/', '', $_GET['detail']);
$meta_tags=select_row('tbl_community',"comm_slug='$detail' and is_active=1");
include('common/header.php');
$comm_detail=select_row('tbl_community',"comm_slug='$detail' and is_active='1'");

?>
    <!--warpper-->
    <div class="box-head-bg"></div>
    <div class="wrapper">
        <!--content-->
        <div class="content">
            <div class="container">
<div class="comm-s-hero" style="background-image: url('<?=BASE_URL_IMG?>community/inner/<?=$comm_detail['comm_inner_img']?>');"></div>
            </div>
            <!--container-->
            <div class="container">
                <!--breadcrumbs-list-->
                <div class="breadcrumbs-list bl_flat">
                    <a href="<?=BASE_URL?>">Home</a><a href="<?=BASE_URL?>community">Communities</a><span><?=$comm_detail['comm_heading']?></span>
                    <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
                </div>
                <!--breadcrumbs-list end-->
                <!--main-content-->
                <div class="main-content">
                    <!--boxed-container-->
                    <div class="boxed-container">
                        <div class="scroll-content-wrap">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="post-container">
                                        <div class="boxed-content">
                                            <div class="boxed-content-title seo-chg">
                                                <h1><?=$comm_detail['comm_heading']?></h1>
                                            </div>
                                            <div class="boxed-content-item">
                                                <div class="text-block post-single_tb">
                                                    <div class="text-block-container">
                                                     <?=$comm_detail['comm_long_descp']?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
<div class="dd-boxed-container">
    <div class="scroll-content-wrap">
        <?php
        // Location section check
        $has_loc_heading = !empty($comm_detail['comm_loc_main_heading']);
        $has_loc_desc = !empty($comm_detail['comm_loc_main_descp']);
        $has_loc_image = !empty($comm_detail['comm_loc_main_img']);
        $comm_loc_query = select_query('tbl_community_loc', 'comm_id=' . $comm_detail['comm_id'] . ' and is_active=1');
        $has_loc_items = mysqli_num_rows($comm_loc_query) > 0;

        if ($has_loc_heading || $has_loc_desc || $has_loc_image || $has_loc_items) {
        ?>
        <!-- Community Location Section -->
        <div class="row dev-dd">
            <div class="col-12 mt-3">
                <div class="post-container">
                    <div class="boxed-content">
                        <?php if ($has_loc_heading) { ?>
                        <div class="boxed-content-title">
                            <h3><?= $comm_detail['comm_loc_main_heading'] ?></h3>
                        </div>
                        <?php } ?>
                        <div class="boxed-content-item">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php if ($has_loc_desc) { ?>
                                    <div class="text-block post-single_tb">
                                        <div class="text-block-container">
                                            <?= $comm_detail['comm_loc_main_descp'] ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <?php if ($has_loc_items) { ?>
                                    <div class="text-block post-single_tb">
                                        <div class="text-block-container">
                                            <div class="mt-3" id="drop1">
                                                <div class="accordion">
                                                    <?php
                                                    mysqli_data_seek($comm_loc_query, 0);
                                                    while ($row = mysqli_fetch_array($comm_loc_query)) {
                                                    ?>
                                                    <a class="toggle" href="#">
                                                        <?= $row['comm_loc_heading'] ?>
                                                        <i class="fa-solid fa-caret-down"></i>
                                                    </a>
                                                    <div class="accordion-inner">
                                                        <?= $row['comm_loc_description'] ?>
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <?php if ($has_loc_image) { ?>
                                <div class="col-md-6">
                                    <img src="<?= BASE_URL_IMG ?>community/location/<?= $comm_detail['comm_loc_main_img'] ?>" alt="">
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php
        // Community map section check
        $has_map_heading = !empty($comm_detail['comm_map_heading']);
        $has_iframe = !empty($comm_detail['comm_map_iframe']);
        $comm_map_query = select_query('tbl_community_map', 'comm_id=' . $comm_detail['comm_id'] . ' and is_active=1');
        $has_locations = mysqli_num_rows($comm_map_query) > 0;

        if ($has_map_heading || $has_iframe || $has_locations) {
        ?>
        <!-- Community Map Section -->
        <div class="row dev-dd">
            <div class="col-12 mt-3">
                <div class="post-container">
                    <div class="boxed-content">
                        <?php if ($has_map_heading) { ?>
                        <div class="boxed-content-title">
                            <h3><?= $comm_detail['comm_map_heading'] ?></h3>
                        </div>
                        <?php } ?>
                        <div class="boxed-content-item">
                            <div class="row align-items-center">
                                <?php if ($has_iframe) { ?>
                                <div class="<?= $has_locations ? 'col-md-6' : 'col-12' ?>">
                                    <iframe
                                        src="<?= $comm_detail['comm_map_iframe'] ?>"
                                        width="100%" height="450" style="border:0;"
                                        allowfullscreen="" loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                                <?php } ?>
                                <?php if ($has_locations) { ?>
                                <div class="col-md-6">
                                    <div class="nerby-list-wrap">
                                        <div class="nerby-list-container">
                                            <div class="nerby-list">
                                                <span class="nerby-title">Nearby Locations</span>
                                                <div class="nerby-list-box">
                                                    <ul>
                                                        <?php
                                                        mysqli_data_seek($comm_map_query, 0);
                                                        while ($row = mysqli_fetch_array($comm_map_query)) {
                                                        ?>
                                                        <li>
                                                            <div class="label"><i class="<?= $row['comm_map_fa_icon'] ?>"></i>
                                                            : <?= $row['comm_map_heading'] ?></div>
                                                            <span><?= $row['comm_map_distance'] ?></span>
                                                        </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="limit-box"></div>
</div>
                        </div>
                        <div class="limit-box"></div>
                    </div>
                    <!--boxed-container end-->
                    <!--boxed-container-->

                    <!--boxed-container end-->
                     <?php
                    $query = select_query(
                        'tbl_proj',
                        'prj_comm_id="' . $comm_detail['comm_id'] . '" AND is_active=1 AND prj_type_id=1 ORDER BY proj_posted_on DESC'
                    );
                    $count=mysqli_num_rows($query);
                    $page=1;
                    $per_page=6;
                    $no_of_page=ceil($count/$per_page);
                    $start=($page-1)*$per_page;
                    $prj_query = select_query(
                        'tbl_proj',
                        'prj_comm_id="' . $comm_detail['comm_id'] . '" AND is_active=1 AND prj_type_id=1 ORDER BY proj_posted_on DESC'
                    );
                       $slide_class = ($count > 3) ? 'swiper-slide' : '';

                    if($count>0)
                    {
                    ?>
                    <div class="boxed-container">
                        <div class="boxed-content-title bcst_ca">
                            <h3>Exclusive Homes in <?=$comm_detail['comm_heading']?></h3>
                        </div>
                        <div class="single-carousel-wrap">
                            <div class="single-carousel equal-elc">
                                <div class="swiper-container">
                                    <div class="swiper-wrapper">

  <?php
                     $key=0;
                     while($row=mysqli_fetch_array($prj_query))
                     {
                         $prj_thumb_arr=explode(',',$row['prj_thumb_img']);
                          
                           if( count($prj_thumb_arr)>1)
                            {
                                $prj_thumb_img=$prj_thumb_arr[0];
                            }
                            else
                            {
                                $prj_thumb_img=$row['prj_thumb_img'];
                            }
                     ?> 
                   
    <div class="swiper-slide">
                                            <div class="listing-item">
                                                    <div class="geodir-category-listing">
                                                        <div class="geodir-category-img">
                                                            <a href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>" class="geodir-category-img_item">
                                                                <div class="off-plan-thumb">
                                                                    <img src="<?= BASE_URL_IMG ?>project/thumb/<?= $prj_thumb_img ?>" alt="">
                                                                </div>
                                                                <div class="overlay"></div>
                                                            </a>
                                                            <div class="geodir-category-location">
                                                                <a href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>" class="map-item"><i
                                                                        class="fas fa-map-marker-alt"></i>
                                                                    <?= $row["proj_location"] ?></a>
                                                            </div>
                                                            <ul class="list-single-opt_header_cat off-n">
                                                                <?php if (!empty($row['proj_handover'])): ?>
                                                                    <li>
                                                                        <a href="javascript:void(0)" class="cat-opt">
                                                                            <?= $row['proj_handover'] ?></a>
                                                                    </li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                        <div class="geodir-category-content prop-gcc off-n">

                                                            <?php if (!empty($row['proj_name_on_card'])) { ?>
                                                                <h3>
                                                                    <a href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>">
                                                                        <?= $row['proj_name_on_card'] ?>
                                                                    </a>
                                                                </h3>
                                                            <?php } else { ?>
                                                                <h3>
                                                                    <a href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>">
                                                                        <?= $row['proj_name'] ?>
                                                                    </a>
                                                                </h3>
                                                            <?php } ?>
                                                            <h5>
                                                                <span class="off-n-sp1">By</span>
                                                                <span class="off-n-sp2"><?= getDeveloper($row["prj_dev_id"]) ?></span>
                                                            </h5>

                                                        </div>

                                                        <div class="geodir-category-footer list-footer">
                                                            <div class="d-flex">
                                                            <?php
                                                            $bedroom1 = '';

                                                            if (!empty($row['proj_bedroom_on_card'])) {
                                                                $bedroom1 = htmlspecialchars($row['proj_bedroom_on_card']);
                                                            } else {
                                                                $bedrooms = explode(",", $row["proj_bedroom"]);
                                                                $bedrooms = array_map('trim', $bedrooms);
                                                                $bedrooms = array_filter($bedrooms);

                                                                if (!empty($bedrooms)) {
                                                                    $first = reset($bedrooms);
                                                                    $last  = end($bedrooms);

                                                                    if ($first == $last) {
                                                                        $bedroom1 = htmlspecialchars($first);
                                                                    } else {
                                                                        $bedroom1 = htmlspecialchars($first) . ' - ' . htmlspecialchars($last);
                                                                    }
                                                                }
                                                            }

                                                            // ✅ print only once
                                                            if (!empty($bedroom1)) {
                                                                echo '
                                                                <div class="off-p-f1">
                                                                    <div class="off-f-h">Bedroom:</div>
                                                                    <div class="off-f-b">
                                                                        ' . $bedroom1 . '
                                                                    </div>
                                                                </div>';
                                                            }
                                                            ?>
                                                            <div class="off-p-f2 ms-2">
                                                                <?php if (is_numeric($row["proj_price"]) && $row["proj_price"] != 0): ?>
                                                                    <div class="price-row" style="display: flex; align-items: flex-start; flex-direction: column; gap: 0; font-size:1em;">
                                                                        <div class="text-muted off-f-h">Starting Price: </div>
                                                                        <!-- Price Output -->
                                                                        <div class="geodir-category-content_price text-muted      currency-price off-f-b " "
                                                            data-price=" <?= $row["proj_price"] ?>"
                                                                            data-original-currency="AED"
                                                                            data-original-price="<?= $row["proj_price"] ?>">
                                                                            <img src="<?= BASE_URL ?>images/symbol/currency-symbol.png" alt="Currency"
                                                                                style="height: 16px; vertical-align: middle;">
                                                                            AED <?= number_format($row["proj_price"]) ?>
                                                                        </div>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="geodir-category-content_price text-muted " style="font-size:1em; flex-direction: column; align-items: flex-start;">
                                                                        <div class="text-muted off-f-h">Starting Price:</div>
                                                                        <div class="off-f-b " ">Call Us</div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            </div>
                                            <div class=" clearfix"></div>
                                                                        <?php
                                                                        $phone = '+971527543243' . $row["proj_whatsaapno"];

                                                                        $imageUrl = BASE_URL_IMG . "project/thumb/" . $prj_thumb_img;

                                                                        $message = "Hi, I'm interested in " . $row["proj_name"] . ".\n\n"
                                                                            . "Property Image:\n" . $imageUrl . "\n\n"
                                                                            . "Link:\n" . BASE_URL . "property/" . $row["proj_slug"];

                                                                        $encodedMessage = urlencode($message);
                                                                        ?>

                                                                        <div class="list-contact-icon off-p-f3">
                                                                            <a href="https://wa.me/<?= $phone ?>?text=<?= $encodedMessage ?>" target="_blank"
                                                                                class="pcl_btn">

                                                                                <img src="<?= BASE_URL ?>images/wa-chat-logo-w.png"
                                                                                    alt="Whatsapp Icon" width="100%" title="Whatsapp">
                                                                                <span class="d-none d-md-inline">WHATSAPP</span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                        </div>
                                        </div>

                                    <?php }?>
                                          
                                       
                                    </div>
                                </div>
                                
                                    <div class="ss-carousel-button ss-carousel-button-prev">
                                        <i class="fas fa-caret-left"></i>
                                    </div>
                                    <div class="ss-carousel-button ss-carousel-button-next">
                                        <i class="fas fa-caret-right"></i>
                                    </div>
                                
                            </div>
                        </div>
                        
                    </div>

                      <?php 
                    }
                    ?>
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