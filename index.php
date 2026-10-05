<?php
include('db/config.php');
$meta_tags = select_row("tbl_tags", "id=7 and is_active=1");
include('common/header.php');
?>
<!--warpper-->
<div class="wrapper">
    <div class="content">
        <!--section-->
        <!--section-->
                <?php
                $hero_data = select_all('tbl_hero_section', 'is_active=1 ORDER BY display_order');
                $hero = [
                    'desktop' => null,
                    'tablet' => null,
                    'mobile' => null
                ];

                foreach ($hero_data as $row) {
                    $hero[$row['img_screen_size']] = $row;
                }
                    ?>
                <div class="index-sec section hero-section hero-section_sin">
                    <div class="hero-section-wrap">
                        <div class="hero-section-wrap-item hero-sec-vh">
                            <div class="container dre-search-f">
                                <div class="hero-section-container hsc2">
                                    <div class="row align-items-center">
                                        <div class="col-lg-4">
                                            <h1>Find Your <span class="dre-cl">DRE</span>am Property</h1>
                                            <p class="pb-0 mb-0">Search the best properties across the prime location
                                            </p>
                                        </div>
                                        <div class="col-lg-8">
                                            <form class="dre-search-form" id="residentialSearchForm1">

                                                <!-- Purpose -->
                                                <div class="dre-search-item">
                                                    <div class="dre-search-icon">
                                                        <i class="fa-solid fa-house"></i>
                                                    </div>

                                                    <div class="dre-search-content">
                                                        <select name="type" class="chosen-select">
                                                            <option value="Offplan">Off Plan</option>
                                                            <option value="Buy">Buy</option>
                                                            <option value="Rent">Rent</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Property Type -->
                                                <div class="dre-search-item">
                                                    <div class="dre-search-icon">
                                                        <i class="fa-regular fa-building"></i>
                                                    </div>

                                                    <div class="dre-search-content" style="z-index: 10;">
                                                        <select name="unitType" class="chosen-select">
                                                            <option value="all">All Types</option>
                                                            <option value="apartments">Apartment</option>
                                                            <option value="duplex">Duplex</option>
                                                            <option value="triplex">Triplex</option>
                                                            <option value="penthouse">Penthouse</option>
                                                            <option value="villa">Villa</option>
                                                            <option value="townhouse">Townhouse</option>
                                                            <option value="plot">Plot</option>
                                                        </select>
                                                    </div>
                                                </div>

                                               <!-- Location -->
                                                <div class="dre-search-item">
                                                    <div class="dre-search-icon">
                                                        <i class="fa-solid fa-location-dot"></i>
                                                    </div>

                                                    <div class="dre-search-content">
                                                        <input type="text" name="location" id="locationInput1" placeholder="Enter location" autocomplete="off">

                                                        <span id="clearLocationInput" class="clear-input-btn">
                                                            &times;
                                                        </span>
                                                        
                                                        <div id="locationSuggestions"></div>
                                                    </div>
                                                </div>

                                                <!-- Property -->
                                                <div class="dre-search-item">
                                                    <div class="dre-search-icon">
                                                        <i class="fa-regular fa-building"></i>
                                                    </div>

                                                    <div class="dre-search-content">
                                                        <input type="text" name="property" id="propertyInput1"
                                                            placeholder="Enter project name" autocomplete="off">

                                                            <span id="clearPropertyInput" class="clear-input-btn">
                                                            &times;
                                                        </span>

                                                        <div id="propertySuggestions"></div>    
                                                    </div>
                                                </div>

                                                <!-- Button -->
                                                <button type="submit" class="dre-search-btn">
                                                    <i class="fa-solid fa-magnifying-glass"></i>
                                                </button>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($hero['desktop']) && $hero['desktop']['media_type'] == 'video') { ?>

                                <!-- VIDEO -->
                                <div class="bg-wrap bg-hero fs-wrapper">
                                    <video class="hero-video" autoplay muted loop playsinline preload="auto">
                                        <source src="<?= BASE_URL_IMG . $hero['desktop']['media_path'] ?>" type="video/mp4">
                                    </video>
                                    <div class="hero-overlay"></div>
                                </div>

                            <?php } else { ?>

                                <!-- DESKTOP -->
                                <div class="bg bg-desktop"
                                    data-bg="<?= BASE_URL_IMG . ($hero['desktop']['media_path'] ?? '') ?>">
                                </div>

                                <!-- TABLET -->
                                <div class="bg bg-tablet"
                                    data-bg="<?= BASE_URL_IMG . ($hero['tablet']['media_path'] ?? '') ?>">
                                </div>

                                <!-- MOBILE -->
                                <div class="bg bg-mobile"
                                    data-bg="<?= BASE_URL_IMG . ($hero['mobile']['media_path'] ?? '') ?>">
                                </div>

                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!--section-end-->
        <!--section-end-->
        <!--container-->
        <div class="container">

            <!--main-content-->
            <div class="main-content ms_vir_height" id="sec1">
                <!--boxed-container-->
                <div class="new-dre">
                    <div class="listing-grid_heroheader mt-5">
                        <h3>Browse Hot Properties</h3>
                        <div class="home-btn-slider">
                                <div class="tc-button tc-button-prev"><i class="fas fa-caret-left"></i></div>
                                <div class="tc-button tc-button-next"><i class="fas fa-caret-right"></i></div>

                        </div>
                    </div>
                    <!-- exclusive listing carousel starts -->
                    <div class="testimonilas-carousel-wrap">
                        <div class="  exc-listing-carousel-dre ">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">

                                    <?php
                                    $query = select_query('tbl_proj', 'proj_showonhomepage=1 and is_active=1 order by prj_id');
                                    while ($row = mysqli_fetch_array($query)) {
                                        $proj_dev = select_row("tbl_developer", "dev_id=" . $row["prj_dev_id"]);
                                        if (!empty($row['home_card_img'])) {

                                                    $proj_thumb_mg =
                                                        BASE_URL_IMG . "project/home-card-img/" . $row['home_card_img'];

                                                } else {

                                                    $prj_thumb_arr = explode(",", $row['prj_thumb_img']);

                                                    $proj_thumb_img1 =
                                                        !empty($prj_thumb_arr[0])
                                                        ? $prj_thumb_arr[0]
                                                        : '';

                                                    $proj_thumb_mg =
                                                        BASE_URL_IMG . "project/thumb/" . $proj_thumb_img1;

                                                }
                                    ?>
                                        <div class="swiper-slide">
                                            <div class="testi-item">
                                                <div class="listing-item">
                                                    <div class="geodir-category-listing">
                                                        <div class="geodir-category-img">
                                                            <a href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>" class="geodir-category-img_item">
                                                                <div class="off-plan-thumb">
                                                                    <img src="<?= $proj_thumb_mg ?>" alt="<?= $row['proj_name'] ?>" loading="lazy"
    decoding="async">
                                                                </div>
                                                                <div class="overlay"></div>
                                                            </a>
                                                            <div class="geodir-category-location">
                                                                <div class="geodir-category-location-item">
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
                                                                    <!-- <h5>
                                                                        <span class="off-n-sp1">By</span>
                                                                        <span class="off-n-sp2"><?= getDeveloper($row["prj_dev_id"]) ?></span>
                                                                    </h5> -->
                                                                    <a href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>" class="map-item"><i
                                                                            class="fas fa-map-marker-alt"></i>
                                                                            <?= $row["proj_location"] ?></a>
                                                                    <div class="dre-op-f">
                                                                        <div class="dre-op-f-item">
                                                                            <div class="text-muted off-f-h">Starting Price: </div>
                                                                            <div>
                                                                                <?php if (is_numeric($row["proj_price"]) && $row["proj_price"] != 0): ?>
                                                                                                <div class="price-row" style="display: flex; align-items: flex-end; flex-direction: column; gap: 0; font-size:1em;">
                                                                                                    
                                                                                                    <!-- Price Output -->
                                                                                                    <div class="geodir-category-content_price text-muted      currency-price off-f-b" "
                                                                                        data-price=" <?= $row["proj_price"] ?>"
                                                                                                        data-original-currency="AED"
                                                                                                        data-original-price="<?= $row["proj_price"] ?>">
                                                                                                        <img src="<?= BASE_URL ?>images/symbol/currency-symbol-w.png" alt="Currency"
                                                                                                            style="height: 16px; vertical-align: middle;">
                                                                                                        AED <?= number_format($row["proj_price"]) ?>
                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php else: ?>
                                                                                                <div class="geodir-category-content_price text-muted " style="font-size:1em; flex-direction: column; align-items: flex-end;">
                                                                                                    
                                                                                                    <div class="off-f-b " ">Call Us</div>
                                                                                                </div>
                                                                                                <?php endif; ?>
                                                                             </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                
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
                                                        <!-- <div class="geodir-category-content prop-gcc off-n">

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

                                                        </div> -->

                                                        
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            </div>
                                        </div>
                                        
                                </div>
                            </div>
                            <!-- exclusive listing carousel ends -->
                            <!-- <a href="buy-property.php" class="commentssubmit csb-no-align">View All Properties <i
                                class="fa-solid fa-caret-right"></i></a> -->
                        </div>
                        <!--boxed-container end-->
                    </div>
                    <!--main-content end-->
                </div>
                <!--container end-->

                <!-- featured communities -->
                <!-- <div class="modal-box-cr animated-button1 " data-modal="modal-feedback">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            Give Us Your Valuable Feedback
                            
                        </div> -->
                <div class="container">
                    <div class="boxed-content-item city-box-c">
                        
                    <div class=" half-carousel-container" >
                        <div class="city-carousel-wrap ">
                            <span class="span-1"></span>
                            <span class="span-2"></span>
                            <span class="span-3"></span>
                            <span class="span-4"></span>
                            <?php
                                                            $home_comm = select_row('tbl_community_content', 'is_active=1');
                                                            $commun_header = $home_comm['comm_header_home'] ?? 'Explore Signature
                                                                        Neighborhoods';
                                                            $commun_content = $home_comm['comm_content_home'] ?? 'Discover Dubai’s most
                                                                        desirable communities, each offering a unique
                                                                        lifestyle, prime location, and modern living
                                                                        experience.';
                                                            $commun_img = BASE_URL_IMG . "community/home-bg/" . $home_comm['comm_img_home'];
                                                            ?>
                                                            <div class="half-carousel-title-wrap">
                                                                <div class="half-carousel-title">
                                                                    <h2 style="color:#5f574c;"><?= $commun_header ?>
                                                                    </h2>
                                                                    <p style="color:#5f574c;"><?= $commun_content ?></p>
                                                                    <a href="listing.html" class="commentssubmit"
                                                                        style="margin-top: 20px; background:#5f574c;">Explore
                                                                        All<i class="fa-solid fa-caret-right"></i></a>
                                                                </div>
                                                                <div class="abs_bg"
                                                                    style="background-image: url(<?= $commun_img ?>);">
                                                                </div>
                                                            </div>
                            <div class="city-carousel">
                                <div class="swiper-container">
                                    <div class="swiper-wrapper">
                                        <!--city-carousel-item-->
                                        <?php
                                                                        $home_comm_card = select_all('tbl_community', 'comm_showonhomepage=1 AND is_active=1');

                                                                        if (!empty($home_comm_card)) {

                                                                            foreach ($home_comm_card as $key => $values) {

                                                                                $comm_img = !empty($values['comm_home_img'])
                                                                                    ? BASE_URL_IMG . 'community/home/' . $values['comm_home_img']
                                                                                    : BASE_URL_IMG . 'community/thumb/' . $values['comm_thumb_img'];
                                                                                ?>

                                                                                <div class="swiper-slide">
                                                                                    <div class="city-carousel-item">

                                                                                        <div class="bg-wrap fs-wrapper">

                                                                                            <div class="img-box">

                                                                                                <img src="<?= $comm_img ?>"
                                                                                                    alt="<?= $values['comm_heading'] ?>"
                                                                                                    width="100%" loading="lazy">

                                                                                            </div>

                                                                                            <div class="overlay"></div>

                                                                                        </div>

                                                                                        <div class="city-carousel-content">
                                                                                            <div class="hc-counter"><?=count(select_all('tbl_proj', 'prj_comm_id='.$values['comm_id'].''))?> Properties</div>
                                                                                            <h3>
                                                                                                <a href="<?=BASE_URL ."community/". $values['comm_slug']?>">
                                                                                                    <?= $values['comm_heading'] ?>
                                                                                                </a>
                                                                                            </h3>

                                                                                            <p>
                                                                                                <?= $values['comm_home_card_content'] ?>
                                                                                            </p>

                                                                                            <!--<a href="<?=BASE_URL ."community/". $values['comm_slug']?>" class="arrow-btn">-->
                                                                                            <!--    <span class="arrow-wrap">-->
                                                                                            <!--        <i-->
                                                                                            <!--            class="fa-solid fa-arrow-right-long"></i>-->
                                                                                            <!--    </span>-->
                                                                                            <!--</a>-->

                                                                                        </div>

                                                                                    </div>
                                                                                </div>

                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                        <!--city-carousel-item end-->
                                    </div>
                                </div>
                                <div class="sc-controls city-pag-init"></div>
                            </div>
                        </div>
                        <div class="city-carousel_controls">
                            <div class="csc-button csc-button-prev"><i class="fas fa-caret-left"></i></div>
                            <div class="csc-button csc-button-next"><i class="fas fa-caret-right"></i></div>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- featured communities -->


                <!-- linking section starts -->
                <div class="container">
                    <div class="row">
                        <!-- boxed-content -->
                        <div class="col-12">
                            <div class="">
                                <div class="boxed-content-item ">
                                    <div class="row link-sec-container">
                                        <div class="col-lg-4 col-sm-6 py-4 link-detail-box ldb-1">
                                            <!-- listing-item -->
                                            <div class="listing-item">
                                                <div class="geodir-category-listing">
                                                    <div class="geodir-category-img">
                                                        <a href="<?= BASE_URL ?>offplan-properties" class="geodir-category-img_item">
                                                            <div class="home-bg" data-bg="">
                                                                <img src="images/home-sec/offplan-property.jpg" alt="" width="100%">
                                                                <div class="home-bg-h">
                                                                    <h4>OFF-PLAN PROPERTY</h4>
                                                                    <!--<i class="fa-solid fa-arrow-right-long"></i>-->
                                                                </div>
                                                            </div>
                                                            <div class="overlay"></div>
                                                        </a>
                                                    </div>
                                                    <div class="geodir-category-content">
                                                        <h3><a href="<?= BASE_URL ?>offplan-properties">Hot Property Launches</a></h3>
                                                        <p>Discover the newest and most sought-after projects fresh on the Dubai market.</p>
                                                        <!-- <p><a href="<?= BASE_URL ?>offplan-properties" class="home-s-btn">Explore <i class="fa-solid fa-arrow-right-long"></i></a></p> -->
                                                    </div>
                                                    <!-- <div class="geodir-category-footer">
                                                <a href="<?= BASE_URL ?>offplan-properties" class="commentssubmit ">Learn More</a>
                                            </div> -->
                                                </div>
                                            </div>
                                            <!-- listing-item end-->
                                        </div>
                                        <div class="col-lg-4 col-sm-6 py-4 link-detail-box ldb-2">
                                            <!-- listing-item -->
                                            <div class="listing-item">
                                                <div class="geodir-category-listing">
                                                    <div class="geodir-category-img">
                                                        <a href="<?= BASE_URL ?>buy-property" class="geodir-category-img_item">
                                                            <div class="home-bg" data-bg="">
                                                                <img src="images/home-sec/buy-property.jpg" alt="" width="100%">
                                                                <div class="home-bg-h">
                                                                    <h4>BUY PROPERTY</h4>
                                                                    <!--<i class="fa-solid fa-arrow-right-long"></i>-->
                                                                </div>
                                                            </div>
                                                            <div class="overlay"></div>
                                                        </a>
                                                    </div>
                                                    <div class="geodir-category-content">
                                                        <h3><a href="<?= BASE_URL ?>buy-property">Top Resale Deals</a></h3>
                                                        <p>Unlock unbeatable value with handpicked pre-owned homes across prime locations.</p>
                                                        <!-- <p><a href="<?= BASE_URL ?>buy-property" class="home-s-btn">Explore <i class="fa-solid fa-arrow-right-long"></i></a></p> -->
                                                    </div>
                                                    <!-- <div class="geodir-category-footer">
                                                <a href="javascript:void(0)" class="commentssubmit ">Learn More</a>
                                            </div> -->
                                                </div>
                                            </div>
                                            <!-- listing-item end-->
                                        </div>
                                        <div class="col-lg-4 col-sm-6 py-4 link-detail-box ldb-3">
                                            <!-- listing-item -->
                                            <div class="listing-item">
                                                <div class="geodir-category-listing">
                                                    <div class="geodir-category-img">
                                                        <a href="<?= BASE_URL ?>rent-property" class="geodir-category-img_item">
                                                            <div class="home-bg" data-bg="">
                                                                <img src="images/home-sec/rent-property-1.jpg" alt="" width="100%">
                                                                <div class="home-bg-h">
                                                                    <h4>RENT PROPERTY</h4>
                                                                    <!--<i class="fa-solid fa-arrow-right-long"></i>-->
                                                                </div>
                                                            </div>
                                                            <div class="overlay"></div>
                                                        </a>
                                                    </div>
                                                    <div class="geodir-category-content">
                                                        <h3><a href="<?= BASE_URL ?>rent-property">Trending Rental Homes</a></h3>
                                                        <p>Browse the most in-demand rental listings tailored to your lifestyle and budget.</p>
                                                        <!-- <p><a href="<?= BASE_URL ?>rent-property" class="home-s-btn">Explore <i class="fa-solid fa-arrow-right-long"></i></a></p> -->
                                                    </div>
                                                    <!-- <div class="geodir-category-footer">
                                                <a href="<?= BASE_URL ?>rent-property" class="commentssubmit ">Learn More</a>
                                            </div> -->
                                                </div>
                                            </div>
                                            <!-- listing-item end-->
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- boxed-content end-->

                    </div>
                    <div class="limit-box"></div>
                </div>
                <!-- linking section ends -->

                

                

                <!-- golden visa section -->
                <div class="container-full"> 
                    <div class="golden-visa-dre">
                        <div class="row gvd-box">
                            <div class="col-lg-6 gvd-item-1">
                                <h4>Unlock Your Golden Visa With DRE</h4>
                                <p>Invest in Dubai real estate and unlock exclusive Golden Visa benefits for a secure future, premium lifestyle, and long-term UAE residency.</p>
                                <a href="<?= BASE_URL ?>golden-visa#golden-visa-box" class="gvd-btn">Get Golden Visa Consultation</a>
                            </div>
                            <div class="col-lg-6 gvd-item-2">
                                <img src="images/bg/Golden-Visa-14.png" alt="" width="100%">
                            </div>
                        </div>

                        <!-- <img src="images/bg/golden-visa-updated.jpg" alt="" width="100%"> -->
                    </div>
                </div>
                 
                
                <!-- golden visa section ends-->
                 
                

                

                

                <!-- parallex section  -->
                <!-- <div class="container">
                    <div class="parallax-section-wrap">
                        <div class="bg-wrap    fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="" data-scrollax="properties: { translateY: '20%' }"></div>
                            <div class="overlay"></div>
                        </div>
                    </div>
                </div> -->

                <!-- about section 2-->
                <div class="container dre-ab">
                    <div class="boxed-content ">
                        <div class="about-wrap boxed-content-item">
                            <div class="row mb-3 justify-content-center align-items-center about-boxn">
                                
                                <div class="col-lg-6 about-img1">
                                    <div class="about-img">
                                        <img src="images/dre-tree.jpg" class="respimg" alt="">
                                        <div class="about-img-hotifer">
                                            <p>Your Gateway to Premium Dubai Real Estate Opportunities.</p>
                                            <!-- <h4>DRE Real Estate</h4> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="about-title ab-hero">
                                        <p>About DRE</p>
                                        <h2>Elevating Real Estate with Trust and Excellence. </h2>
                                        <p class="mb-4">Connecting clients to Dubai’s premium properties through trusted real estate expertise, strategic market insight, and personalized property solutions.</p>
                                        <a href="<?= BASE_URL ?>about-us">About Us</a>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="row about-box-counter">
                             <?php
                            $counter=select_query('tbl_abt_counter','is_active=1 order by abt_countr_id desc');
                            while($row=mysqli_fetch_array($counter))
                            {
                            ?>  
                            <div class="col-lg-3">
                                <div class="inline-facts">
                                    <i class="fa-light <?=$row['abt_countr_faicon']?>"></i>
                                    <div class="milestone-counter">
                                        <div class="stats animaper">
                                            <div class="num plus-sign" data-content="0" data-num="<?=$row['abt_countr_counter']?>">0</div>
                                        </div>
                                    </div>
                                    <h6><?=$row['abt_countr_heading']?></h6>
                                </div>
                            </div>
                        <?php }?>
                           
                        </div>
                        </div>
                    </div>
                </div>    
                <!-- about section  2-->

                <!-- about section -->
                <div class="container dre-fd">
                    <div class="boxed-content ">
                        <div class="about-wrap boxed-content-item">
                            <div class="row justify-content-center align-items-center about-boxn">
                                <div class="col-lg-6">
                                    <div class="about-title ab-hero">
                                        <p>Meet the vision behind DRE</p>
                                        <h2>Built on relationships, driven by results.</h2>
                                        <p class="mb-4">Founded on trust and expertise, DRE’s founders are committed to helping clients make confident real estate decisions through honest guidance and deep market knowledge.</p>

                                        <a href="<?= BASE_URL ?>dre-brand-video">Watch Our Story</a>
                                    </div>
                                    
                                </div>
                                <div class="col-lg-6">
                                    <div class="about-img">
                                        <img src="images/qurat-and-mudi.jpg" class="respimg" alt="">
                                        <!-- <div class="about-img-hotifer">
                                            <p>Your trusted partner for buying, renting, and investing in Dubai real estate.</p>
                                            <h4>DRE Real Estate</h4>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>    
                <!-- about section  -->

                <!-- Town Square Branch -->
                <div class="container dre-fd">
                    <div class="boxed-content ">
                        <div class="about-wrap boxed-content-item">
                            <div class="row justify-content-center align-items-center about-boxn">
                                <div class="col-lg-6 tsb-item-1">
                                    <div class="about-img">
                                        <img src="images/town-square-office.jpg" class="respimg" alt="">
                                        <!-- <div class="about-img-hotifer">
                                            <p>Your trusted partner for buying, renting, and investing in Dubai real estate.</p>
                                            <h4>DRE Real Estate</h4>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="col-lg-6 tsb-item-2">
                                    <div class="about-title ab-hero">
                                        <p>Town Square Branch</p>
                                        <h2>Making Property Decisions Easier Across Dubai.</h2>
                                        <p class="mb-4">With a strong understanding of the Town Square community, our team is here to connect you with opportunities that truly match your needs.</p>

                                        <a href="<?= BASE_URL ?>town-square-team">Connect With Our Team</a>
                                    </div>
                                    
                                </div>
                                
                            </div>
                            
                        </div>
                    </div>
                </div>    
                <!-- Town Square Branch  -->

                <!-- award section -->
                <div class="container-full">
                    <div class="dre-new-awrd">
                        
                         <div><img src="images/award-image.png" alt="" width="100%"></div>
                    </div>
                </div>
                <!-- award section -->

                <!--main-content-->
                <div class="main-content ms_vir_height">
                    <!--container -->
                    <div class="container">
                        <div class="dre-newc">

                            <!-- clients-carousel-wrap-->
                            <div class="clients-carousel-wrap">
                                <div class="dev-carousel">
                                    <div class="dev-sec-h">
                                    <div class="clients-carousel-title">Leading Developer Partners</div>
                                    <p>Backed by trusted developers shaping Dubai’s most iconic real estate destinations.</p>
                                    </div>
                                    <div class="dev-sec-b"><a href="<?= BASE_URL ?>developer">View All <i class="fa-solid fa-arrow-right-long"></i></a></div>
                                </div>
                                <div class="clients-carousel">
                                    <div class="swiper-container">
                                        <div class="swiper-wrapper">

                                            <?php
                                            $query = select_query('tbl_developer', 'dev_showonhomepage=1 and is_active=1 and dev_id != 0 order by dev_id desc');

                                            while ($row = mysqli_fetch_array($query)) {
                                            ?>
                                                <div class="swiper-slide">
                                                    <a href="<?= BASE_URL ?>developer/<?= $row['dev_slug'] ?>" class="client-item"><img src="<?= BASE_URL_IMG ?>developer/homepage-image/<?= $row['dev_homp_img'] ?>"
                                                            alt="<?= $row['dev_homp_img_alt'] ?>"></a>
                                                </div>
                                            <?php } ?>

                                        </div>
                                    </div>
                                    <div class="cc-button cc-button-next"><i class="fal fa-angle-right"></i></div>
                                    <div class="cc-button cc-button-prev"><i class="fal fa-angle-left"></i></div>
                                </div>
                            </div>
                            <!-- clients-carousel-wrap end-->
                        </div>
                    </div>
                    <!--container end-->
                </div>
                <!--main-content end-->
                <!-- DRE Developer Partnership Section -->

                <section class="dre-partner-sec">
                    <div class="container">

                        <!-- Heading -->
                        <div class="dre-partner-head text-center">
                            <p><img src="images/home-partner/DRE-X-BLACK-GOLD-cc.png" alt="" width="150px" style="display: block; margin: auto; padding-bottom:15px;"></p>
                            <p class="dre-partner-subtitle">
                                STRONG PARTNERSHIPS. ICONIC DEVELOPMENTS.
                            </p>

                            <h2>
                                <span>DRE Exclusive Sales Partners</span>
                            </h2>
                        </div>

                        <!-- Cards -->
                        <div class="row g-4">

                            <!-- EMAAR -->
                            <div class="col-lg-6">
                                <div class="dre-dev-card">

                                    <!-- Image -->
                                    <div class="dre-dev-img">
                                        <a href="https://drehomes.com/company-news/sharafi-development-unveils-marea-residences-with-dre-x-as-exclusive-sales-partner"><img src="images/home-partner/Sharafi-deal1.jpeg" alt=""></a>
                                    </div>

                                    <!-- Content -->
                                    <div class="dre-dev-content">

                                        <!-- Logo -->
                                        <div class="dre-dev-logo">
                                            <a href="https://drehomes.com/developer/sharafi-development"><img src="images/home-partner/sharafi-logo1.png" alt=""></a>
                                        </div>

                                        <!-- Text -->
                                        <div class="dre-dev-text">
                                            <a href="https://drehomes.com/company-news/sharafi-development-unveils-marea-residences-with-dre-x-as-exclusive-sales-partner"><h3>
                                                 Shaping Coastal Living with
                                                Sharafi Developments
                                            </h3></a>
                                            <!-- <a href="https://drehomes.com/company-news/sharafi-development-unveils-marea-residences-with-dre-x-as-exclusive-sales-partner" class="dre-dev-btn">
                                                Learn More
                                            </a> -->

                                        </div>

                                    </div>

                                </div>
                            </div>

                            <!-- NAKHEEL -->
                            <div class="col-lg-6">
                                <div class="dre-dev-card">

                                    <!-- Image -->
                                    <div class="dre-dev-img">
                                        <a href="https://drehomes.com/company-news/dubai-tonino-lamborghini-residences-unveils-final-phase"><img src="images/home-partner/gulf-deal1.jpeg" alt=""></a>
                                    </div>

                                    <!-- Content -->
                                    <div class="dre-dev-content">

                                        <!-- Logo -->
                                        <div class="dre-dev-logo">
                                            <a href="https://drehomes.com/developer/gulf-land-property"><img src="images/home-partner/gulf-logo1.png" alt=""></a>
                                        </div>

                                        <!-- Text -->
                                        <div class="dre-dev-text">
                                            <a href="https://drehomes.com/company-news/dubai-tonino-lamborghini-residences-unveils-final-phase"><h3>
                                               Elevating Luxury Living
                                                with Gulf Land Property Developers
                                            </h3></a>
                                            <!-- <a href="https://drehomes.com/company-news/dubai-tonino-lamborghini-residences-unveils-final-phase" class="dre-dev-btn">
                                                Learn More
                                            </a> -->
                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                </section>
                <!-- end -->
                
                <div class="container-full">
                    <div class="pm-h-parallax">
                        <div class="pm-h-container">
                            <div class="pm-hc-item">
                                <div class="pm-h-cont">
                                    <h4>Smart Property Care</h4>
                                    <p>DRE handles everything from tenant management and maintenance to rent collection and reporting, ensuring your property stays profitable, protected, and stress-free.</p>
                                </div>
                                <div class="pm-h-btn">
                                    <a href="<?= BASE_URL ?>property-management">Discover More Services</a>
                                    <a href="<?= BASE_URL ?>property-management#contact-frm">Get Free Consultation</a>
                                </div>
                            </div>
                        </div>
                        <div class="pm-icon-bg"><img src="images/pm-sec-bg.png" alt="" width="450px"></div>
                    </div>
                </div>
                <!-- parallex section  -->

                <div class="container">
                    <div class="customer-testi">
                        
                        <div class="row d-flex align-items-center justify-content-center">
                            <div  class="col-lg-4 col-12  ">
                                <div class="crb-item-1">
                                    <img src="images/why-c.jpg" alt="" width="100%">
                                    <div class="overlay"></div>
                                    <div class="review-ot">
                                        <div class="customer-review-box">
                                            <div><script defer async src='https://cdn.trustindex.io/loader.js?5e862a8729ae637df016bbd0ae2'></script></div>
                                            <h4>Words That Build Trust</h4>
                                            <p>We take pride in creating smooth and rewarding real estate experiences across Dubai.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7 col-12 "><script defer async src='https://cdn.trustindex.io/loader.js?e5ad343727cf637b4d36c8ac53d'></script></div>
                        </div>
                    </div>
                </div>


                <div class="container">
                                                <div class="insta-post-box">
                                                    <div class="insta-post-h">
                                                        <a href="https://www.instagram.com/drehomes_realestate/"
                                                            target="_blank" class="me-2"><i
                                                                class="fa-brands fa-instagram"></i></a>
                                                        <a href="https://www.instagram.com/drehomes_realestate/"
                                                            target="_blank">
                                                            <h4>Follow us on Instagram</h4>
                                                        </a>

                                                    </div>
                                                    <div class="insta-post-f">
                                                        <div class="insta-layout">
                                                            <?php
                                                            // Fetch Instagram content from database
                                                            $insta_query = "SELECT * FROM tbl_instagram_content WHERE is_active = 1 ORDER BY display_order ASC";
                                                            $insta_result = mysqli_query($conn, $insta_query);

                                                            // Store all items in array
                                                            $insta_items = [];
                                                            while ($item = mysqli_fetch_assoc($insta_result)) {
                                                                $insta_items[] = $item;
                                                            }

                                                            // Define the exact structure pattern from your original code
                                                            // Original had specific items in specific positions
                                                            $structure = [
                                                                'col1' => [0, 1],     // Column 1: positions 0 and 1
                                                                'col2' => [2, 3],     // Column 2: positions 2 and 3  
                                                                'col3' => [4, 5],     // Column 3: positions 4 and 5
                                                                'col4' => [6, 7],     // Column 4: positions 6 and 7
                                                                'col5' => [8, 9]      // Column 5: positions 8 and 9
                                                            ];
                                                            ?>

                                                            <!-- COLUMN 1 -->
                                                            <div class="insta-column">
                                                                <?php if (isset($insta_items[0])): ?>
                                                                    <?php $item = $insta_items[0]; ?>
                                                                    <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                        target="_blank"
                                                                        class="insta-item insta-square-card">
                                                                        <img src="<?= BASE_URL_IMG . $item['media_url'] ?>"
                                                                            alt="" loading="lazy">
                                                                            <i
                                                                class="fa-brands fa-instagram"></i>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if (isset($insta_items[1])): ?>
                                                                    <?php $item = $insta_items[1]; ?>
                                                                    <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                        target="_blank" class="insta-item insta-reel-card">
                                                                        <img src="<?= BASE_URL_IMG . $item['media_url'] ?>"
                                                                            alt="" loading="lazy">
                                                                            <i
                                                                class="fa-brands fa-instagram"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>

                                                            <!-- COLUMN 2 -->
                                                            <div class="insta-column">
                                                                <?php if (isset($insta_items[2])): ?>
                                                                    <?php $item = $insta_items[2]; ?>
                                                                    <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                        target="_blank" class="insta-item insta-reel-card">
                                                                        <img src="<?= BASE_URL_IMG . $item['media_url'] ?>"
                                                                            alt="" loading="lazy">
                                                                            <i
                                                                class="fa-brands fa-instagram"></i>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if (isset($insta_items[3])): ?>
                                                                    <?php $item = $insta_items[3]; ?>
                                                                    <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                        target="_blank"
                                                                        class="insta-item insta-square-card">
                                                                        <img src="<?= BASE_URL_IMG . $item['media_url'] ?>"
                                                                            alt="" loading="lazy">
                                                                            <i
                                                                class="fa-brands fa-instagram"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>

                                                            <!-- COLUMN 3 -->
                                                            <div class="insta-column">
                                                                <?php if (isset($insta_items[4])): ?>
                                                                    <?php $item = $insta_items[4]; ?>
                                                                    <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                        target="_blank"
                                                                        class="insta-item insta-square-card">
                                                                        <img src="<?= BASE_URL_IMG . $item['media_url'] ?>"
                                                                            alt="" loading="lazy">
                                                                            <i
                                                                class="fa-brands fa-instagram"></i>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if (isset($insta_items[5])): ?>
                                                                    <?php $item = $insta_items[5]; ?>
                                                                    <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                        target="_blank" class="insta-item insta-reel-card">
                                                                        <img src="<?= BASE_URL_IMG . $item['media_url'] ?>"
                                                                            alt="" loading="lazy">
                                                                            <i
                                                                class="fa-brands fa-instagram"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>

                                                            <!-- COLUMN 4 -->
                                                            <div class="insta-column">
                                                                <?php if (isset($insta_items[6])): ?>
                                                                    <?php $item = $insta_items[6]; ?>
                                                                    <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                        target="_blank" class="insta-item insta-reel-card">
                                                                        <img src="<?= BASE_URL_IMG . $item['media_url'] ?>"
                                                                            alt="" loading="lazy">
                                                                            <i
                                                                class="fa-brands fa-instagram"></i>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if (isset($insta_items[7])): ?>
                                                                    <?php $item = $insta_items[7]; ?>
                                                                    <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                        target="_blank"
                                                                        class="insta-item insta-square-card">
                                                                        <img src="<?= BASE_URL_IMG . $item['media_url'] ?>"
                                                                            alt="" loading="lazy">
                                                                            <i
                                                                class="fa-brands fa-instagram"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>

                                                            <!-- COLUMN 5 -->
                                                            <div class="insta-column">
                                                                

                                                                <?php if (isset($insta_items[9])): ?>
                                                                    <?php $item = $insta_items[9]; ?>
                                                                    <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                        target="_blank"
                                                                        class="insta-item insta-square-card">
                                                                        <img src="<?= BASE_URL_IMG . $item['media_url'] ?>"
                                                                            alt="" loading="lazy">
                                                                            <i
                                                                class="fa-brands fa-instagram"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                                <?php if (isset($insta_items[8])): ?>
                                                                    <?php $item = $insta_items[8]; ?>
                                                                    <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                        target="_blank" class="insta-item insta-reel-card">
                                                                        <img src="<?= BASE_URL_IMG . $item['media_url'] ?>"
                                                                            alt="" loading="lazy">
                                                                            <i
                                                                class="fa-brands fa-instagram"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                
            </div>
            <!--content  end-->
            <!-- <div class="whatsapp-icon-fixed">
            <a href="https://wa.me/+971527543243" target="_blank">
            <img src="images/wa-chat-logo.png" alt="Whatsapp Logo" width="80px">
            </a>
        </div> -->
        
        <?php include 'common/footer.php'; ?>
