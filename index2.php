<?php
include('db/config.php');
$meta_tags = select_row("tbl_tags", "id=7 and is_active=1");
include('common/header.php');
?>
<!--warpper-->
<div class="wrapper">
    <div class="content">
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
                                                <select name="type" id="propertyType" class="chosen-select">
                                                    <option value="Offplan" data-id="1">Off Plan</option>
                                                    <option value="Buy" data-id="7">Buy</option>
                                                    <option value="Rent" data-id="8">Rent</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Property Type -->
                                        <!-- `<div class="dre-search-item">
                                                    <div class="dre-search-icon">
                                                        <i class="fa-regular fa-building"></i>
                                                    </div>

                                                    <div class="dre-search-content" style="z-index: 10;">
                                                        <select name="unitType" id="propertyTypeSuggestions" class="chosen-select">
                                                            
                                                        </select>
                                                    </div>
                                                </div>` -->

                                        <!-- Location -->
                                        <div class="dre-search-item">
                                            <div class="dre-search-icon">
                                                <i class="fa-solid fa-location-dot"></i>
                                            </div>

                                            <div class="dre-search-content">
                                                <input type="text" name="location" id="locationInput1"
                                                    placeholder="Enter location" autocomplete="off">

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
                        <div class="bg bg-desktop" data-bg="<?= BASE_URL_IMG . ($hero['desktop']['media_path'] ?? '') ?>">
                        </div>

                        <!-- TABLET -->
                        <div class="bg bg-tablet" data-bg="<?= BASE_URL_IMG . ($hero['tablet']['media_path'] ?? '') ?>">
                        </div>

                        <!-- MOBILE -->
                        <div class="bg bg-mobile" data-bg="<?= BASE_URL_IMG . ($hero['mobile']['media_path'] ?? '') ?>">
                        </div>

                    <?php } ?>
                </div>
            </div>
        </div>
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
                        <div class="testimonilas-carousel  exc-listing-carousel-dre ">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">

                                    <?php
                                    $query = select_query('tbl_proj', 'proj_showonhomepage=1 and is_active=1 order by home_card_order_no IS NULL, home_card_order_no ASC');
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
                                                            <a href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>"
                                                                class="geodir-category-img_item">
                                                                <div class="off-plan-thumb">
                                                                    <img src="<?= $proj_thumb_mg ?>" alt="">
                                                                </div>
                                                                <div class="overlay"></div>
                                                            </a>
                                                            <div class="geodir-category-location">
                                                                <div class="geodir-category-location-item">
                                                                    <?php if (!empty($row['proj_name_on_card'])) { ?>
                                                                        <h3>
                                                                            <a
                                                                                href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>">
                                                                                <?= $row['proj_name_on_card'] ?>
                                                                            </a>
                                                                        </h3>
                                                                    <?php } else { ?>
                                                                        <h3>
                                                                            <a
                                                                                href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>">
                                                                                <?= $row['proj_name'] ?>
                                                                            </a>
                                                                        </h3>
                                                                    <?php } ?>
                                                                    <!-- <h5>
                                                                        <span class="off-n-sp1">By</span>
                                                                        <span class="off-n-sp2"><?= getDeveloper($row["prj_dev_id"]) ?></span>
                                                                    </h5> -->
                                                                    <a href="<?= BASE_URL ?>property/<?= $row["proj_slug"] ?>"
                                                                        class="map-item"><i
                                                                            class="fas fa-map-marker-alt"></i>
                                                                        <?= $row["proj_location"] ?>
                                                                    </a>
                                                                    <div class="dre-op-f">
                                                                        <div class="dre-op-f-item">
                                                                            <div class="text-muted off-f-h">Starting Price: </div>
                                                                            <div>
                                                                                <?php if (is_numeric($row["proj_price"]) && $row["proj_price"] != 0): ?>
                                                                                    <div class="price-row"
                                                                                        style="display: flex; align-items: flex-end; flex-direction: column; gap: 0; font-size:1em;">

                                                                                        <!-- Price Output -->
                                                                                        <div class="geodir-category-content_price text-muted      currency-price off-f-b" "
                                                                                                                                                                                                                data-price="
                                                                                        <?= $row["proj_price"] ?>"
                                                                                            data-original-currency="AED"
                                                                                            data-original-price="<?= $row["proj_price"] ?>">
                                                                                            <img src="<?= BASE_URL ?>images/symbol/currency-symbol-w.png"
                                                                                                alt="Currency"
                                                                                                style="height: 16px; vertical-align: middle;">
                                                                                            AED
                                                                                            <?= number_format($row["proj_price"]) ?>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php else: ?>
                                                                                    <div class="geodir-category-content_price text-muted "
                                                                                        style="font-size:1em; flex-direction: column; align-items: flex-end;">

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
                                                                            <a href="javascript:void(0)"  class="cat-opt">
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
                                              
                </div>
                 <!--boxed-container end-->
            </div>
            <!--main-content end-->
        </div>
                                   
                                    <div class="container">
                                        <div class="boxed-content-item city-box-c">

                                            <div class=" half-carousel-container">
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
                                                                                            width="100%">

                                                                                    </div>

                                                                                    <div class="overlay"></div>

                                                                                </div>

                                                                                <div class="city-carousel-content">
                                                                                    <div class="hc-counter">
                                                                                        <?= count(select_all('tbl_proj', 'prj_comm_id=' . $values['comm_id'] . '')) ?>
                                                                                        Properties
                                                                                    </div>
                                                                                    <h3>
                                                                                        <a
                                                                                            href="<?= BASE_URL . "community/" . $values['comm_slug'] ?>">
                                                                                            <?= $values['comm_heading'] ?>
                                                                                        </a>
                                                                                    </h3>

                                                                                    <p>
                                                                                        <?= $values['comm_home_card_content'] ?>
                                                                                    </p>

                                                                                    <a href="#" class="arrow-btn">
                                                                                        <span class="arrow-wrap">
                                                                                            <i
                                                                                                class="fa-solid fa-arrow-right-long"></i>
                                                                                        </span>
                                                                                    </a>

                                                                                </div>

                                                                            </div>
                                                                        </div>

                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                                <!--city-carousel-item end-->
                                                                <!--city-carousel-item-->
                                                                <div class="swiper-slide">
                                                                    <div class="city-carousel-item">
                                                                        <div class="bg-wrap fs-wrapper">
                                                                            <div class="img-box" data-bg="">
                                                                                <img src="images/fc-image/palm-jumeirah.jpg"
                                                                                    alt="">
                                                                            </div>
                                                                            <div class="overlay"></div>
                                                                        </div>
                                                                        <div class="city-carousel-content">
                                                                            <div class="hc-counter">15
                                                                                Properties</div>
                                                                            <h3><a href="listing.html">Palm
                                                                                    Jumeirah</a></h3>
                                                                            <p>An iconic waterfront destination
                                                                                known for luxury living and
                                                                                beaches.</p>
                                                                            <a href="#" class="arrow-btn">
                                                                                <span class="arrow-wrap">
                                                                                    <i
                                                                                        class="fa-solid fa-arrow-right-long"></i>
                                                                                </span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--city-carousel-item end-->

                                                                <!--city-carousel-item-->
                                                                <div class="swiper-slide">
                                                                    <div class="city-carousel-item">
                                                                        <div class="bg-wrap fs-wrapper">
                                                                            <div class="img-box" data-bg="">
                                                                                <img src="images/fc-image/dubai-hills-estate.jpg"
                                                                                    alt="">
                                                                            </div>
                                                                            <div class="overlay"></div>
                                                                        </div>
                                                                        <div class="city-carousel-content">
                                                                            <div class="hc-counter">78
                                                                                Properties</div>
                                                                            <h3><a href="listing.html">Dubai
                                                                                    Hills Estate</a></h3>
                                                                            <p>A premium golf community offering
                                                                                green landscapes and modern
                                                                                living.</p>
                                                                            <a href="#" class="arrow-btn">
                                                                                <span class="arrow-wrap">
                                                                                    <i
                                                                                        class="fa-solid fa-arrow-right-long"></i>
                                                                                </span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--city-carousel-item end-->
                                                                <!--city-carousel-item-->
                                                                <div class="swiper-slide">
                                                                    <div class="city-carousel-item">
                                                                        <div class="bg-wrap fs-wrapper">
                                                                            <div class="img-box" data-bg="">
                                                                                <img src="images/fc-image/jvc.jpg"
                                                                                    alt="">
                                                                            </div>
                                                                            <div class="overlay"></div>
                                                                        </div>
                                                                        <div class="city-carousel-content">
                                                                            <div class="hc-counter">32
                                                                                Properties</div>
                                                                            <h3><a href="listing.html">Jumeirah
                                                                                    Village Circle</a></h3>
                                                                            <p>A well-connected residential
                                                                                community offering modern urban
                                                                                living.</p>
                                                                            <a href="#" class="arrow-btn">
                                                                                <span class="arrow-wrap">
                                                                                    <i
                                                                                        class="fa-solid fa-arrow-right-long"></i>
                                                                                </span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--city-carousel-item end-->

                                                                <!--city-carousel-item-->
                                                                <div class="swiper-slide">
                                                                    <div class="city-carousel-item">
                                                                        <div class="bg-wrap fs-wrapper">
                                                                            <div class="img-box" data-bg="">
                                                                                <img src="images/fc-image/damac-lagoons.jpg"
                                                                                    alt="">
                                                                            </div>
                                                                            <div class="overlay"></div>
                                                                        </div>
                                                                        <div class="city-carousel-content">
                                                                            <div class="hc-counter">32
                                                                                Properties</div>
                                                                            <h3><a href="listing.html">Damac
                                                                                    Lagoons</a></h3>
                                                                            <p>A waterfront-inspired community
                                                                                offering resort-style living in
                                                                                Dubai.</p>
                                                                            <a href="#" class="arrow-btn">
                                                                                <span class="arrow-wrap">
                                                                                    <i
                                                                                        class="fa-solid fa-arrow-right-long"></i>
                                                                                </span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--city-carousel-item end-->
                                                                <!--city-carousel-item-->
                                                                <div class="swiper-slide">
                                                                    <div class="city-carousel-item">
                                                                        <div class="bg-wrap fs-wrapper">
                                                                            <div class="img-box" data-bg="">
                                                                                <img src="images/fc-image/townsquare.jpg"
                                                                                    alt="">
                                                                            </div>
                                                                            <div class="overlay"></div>
                                                                        </div>
                                                                        <div class="city-carousel-content">
                                                                            <div class="hc-counter">32
                                                                                Properties</div>
                                                                            <h3><a href="listing.html">Town
                                                                                    Square</a></h3>
                                                                            <p>A well-connected destination
                                                                                offering active and
                                                                                family-friendly living.</p>
                                                                            <a href="#" class="arrow-btn">
                                                                                <span class="arrow-wrap">
                                                                                    <i
                                                                                        class="fa-solid fa-arrow-right-long"></i>
                                                                                </span>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--city-carousel-item end-->
                                                            </div>
                                                        </div>
                                                        <div class="sc-controls city-pag-init"></div>
                                                    </div>
                                                </div>
                                                <div class="city-carousel_controls">
                                                    <div class="csc-button csc-button-prev"><i
                                                            class="fas fa-caret-left"></i></div>
                                                    <div class="csc-button csc-button-next"><i
                                                            class="fas fa-caret-right"></i></div>
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
                                                                            <a href="<?= BASE_URL ?>offplan-properties"
                                                                                class="geodir-category-img_item">
                                                                                <div class="home-bg" data-bg="">
                                                                                    <img src="images/home-sec/offplan-property.jpg"
                                                                                        alt="" width="100%">
                                                                                    <div class="home-bg-h">
                                                                                        <h4>OFF-PLAN PROPERTY
                                                                                        </h4>
                                                                                        <i
                                                                                            class="fa-solid fa-arrow-right-long"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="overlay"></div>
                                                                            </a>
                                                                        </div>
                                                                        <div class="geodir-category-content">
                                                                            <h3><a
                                                                                    href="<?= BASE_URL ?>offplan-properties">Hot
                                                                                    Property Launches</a></h3>
                                                                            <p>Discover the newest and most
                                                                                sought-after projects fresh on
                                                                                the Dubai market.</p>
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
                                                                            <a href="<?= BASE_URL ?>buy-property"
                                                                                class="geodir-category-img_item">
                                                                                <div class="home-bg" data-bg="">
                                                                                    <img src="images/home-sec/buy-property.jpg"
                                                                                        alt="" width="100%">
                                                                                    <div class="home-bg-h">
                                                                                        <h4>BUY PROPERTY</h4>
                                                                                        <i
                                                                                            class="fa-solid fa-arrow-right-long"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="overlay"></div>
                                                                            </a>
                                                                        </div>
                                                                        <div class="geodir-category-content">
                                                                            <h3><a href="<?= BASE_URL ?>buy-property">Top
                                                                                    Resale Deals</a></h3>
                                                                            <p>Unlock unbeatable value with
                                                                                handpicked pre-owned homes
                                                                                across prime locations.</p>
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
                                                                            <a href="<?= BASE_URL ?>rent-property"
                                                                                class="geodir-category-img_item">
                                                                                <div class="home-bg" data-bg="">
                                                                                    <img src="images/home-sec/rent-property-1.jpg"
                                                                                        alt="" width="100%">
                                                                                    <div class="home-bg-h">
                                                                                        <h4>RENT PROPERTY</h4>
                                                                                        <i
                                                                                            class="fa-solid fa-arrow-right-long"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="overlay"></div>
                                                                            </a>
                                                                        </div>
                                                                        <div class="geodir-category-content">
                                                                            <h3><a href="<?= BASE_URL ?>rent-property">Trending
                                                                                    Rental Homes</a></h3>
                                                                            <p>Browse the most in-demand rental
                                                                                listings tailored to your
                                                                                lifestyle and budget.</p>
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
                                                    <p>Invest in Dubai real estate and unlock exclusive Golden
                                                        Visa benefits for a secure future, premium lifestyle,
                                                        and long-term UAE residency.</p>
                                                    <a href="<?= BASE_URL ?>golden-visa#golden-visa-box"
                                                        class="gvd-btn">Get Golden Visa Consultation</a>
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
                                                <div
                                                    class="row mb-3 justify-content-center align-items-center about-boxn">

                                                    <div class="col-lg-6 about-img1">
                                                        <div class="about-img">
                                                            <img src="images/dre-tree.jpg" class="respimg" alt="">
                                                            <div class="about-img-hotifer">
                                                                <p>Your Gateway to Premium Dubai Real Estate
                                                                    Opportunities.</p>
                                                                <!-- <h4>DRE Real Estate</h4> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="about-title ab-hero">
                                                            <p>About DRE</p>
                                                            <h2>Elevating Real Estate with Trust and Excellence.
                                                            </h2>
                                                            <p class="mb-4">Connecting clients to Dubai’s
                                                                premium properties through trusted real estate
                                                                expertise, strategic market insight, and
                                                                personalized property solutions.</p>
                                                            <a href="<?= BASE_URL ?>about-us">About Us</a>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="row about-box-counter">
                                                    <?php
                                                    $counter = select_query('tbl_abt_counter', 'is_active=1 order by abt_countr_id desc');
                                                    while ($row = mysqli_fetch_array($counter)) {
                                                        ?>
                                                        <div class="col-lg-3">
                                                            <div class="inline-facts">
                                                                <i class="fa-light <?= $row['abt_countr_faicon'] ?>"></i>
                                                                <div class="milestone-counter">
                                                                    <div class="stats animaper">
                                                                        <div class="num plus-sign" data-content="0"
                                                                            data-num="<?= $row['abt_countr_counter'] ?>">
                                                                            0</div>
                                                                    </div>
                                                                </div>
                                                                <h6><?= $row['abt_countr_heading'] ?></h6>
                                                            </div>
                                                        </div>
                                                    <?php } ?>

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
                                                            <p class="mb-4">Founded on trust and expertise,
                                                                DRE’s founders are committed to helping clients
                                                                make confident real estate decisions through
                                                                honest guidance and deep market knowledge.</p>

                                                            <a href="<?= BASE_URL ?>dre-brand-video">Watch Our
                                                                Story</a>
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
                                                            <img src="images/town-square-office.jpg" class="respimg"
                                                                alt="">
                                                            <!-- <div class="about-img-hotifer">
                                                                    <p>Your trusted partner for buying, renting, and investing in Dubai real estate.</p>
                                                                    <h4>DRE Real Estate</h4>
                                                                </div> -->
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 tsb-item-2">
                                                        <div class="about-title ab-hero">
                                                            <p>Town Square Branch</p>
                                                            <h2>Making Property Decisions Easier Across Dubai.
                                                            </h2>
                                                            <p class="mb-4">With a strong understanding of the
                                                                Town Square community, our team is here to
                                                                connect you with opportunities that truly match
                                                                your needs.</p>

                                                            <a href="https://www.instagram.com/drehomes_townsquare/">Connect
                                                                With Our Team</a>
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
                                                            <div class="clients-carousel-title">Leading
                                                                Developer Partners</div>
                                                            <p>Backed by trusted developers shaping Dubai’s most
                                                                iconic real estate destinations.</p>
                                                        </div>
                                                        <div class="dev-sec-b"><a href="<?= BASE_URL ?>developer">View
                                                                All <i class="fa-solid fa-arrow-right-long"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="clients-carousel">
                                                        <div class="swiper-container">
                                                            <div class="swiper-wrapper">

                                                                <?php
                                                                $query = select_query('tbl_developer', 'dev_showonhomepage=1 AND is_active=1 ORDER BY dev_order_no ASC');

                                                                while ($row = mysqli_fetch_array($query)) {
                                                                    ?>
                                                                    <div class="swiper-slide">
                                                                        <a href="<?= BASE_URL ?>developer/<?= $row['dev_slug'] ?>"
                                                                            class="client-item"><img
                                                                                src="<?= BASE_URL_IMG ?>developer/homepage-image/<?= $row['dev_homp_img'] ?>"
                                                                                alt="<?= $row['dev_homp_img_alt'] ?>"></a>
                                                                    </div>
                                                                <?php } ?>

                                                            </div>
                                                        </div>
                                                        <!-- <div class="cc-button cc-button-next"><i class="fal fa-angle-right"></i></div>
                                                        <div class="cc-button cc-button-prev"><i class="fal fa-angle-left"></i></div> -->
                                                    </div>
                                                </div>
                                                <!-- clients-carousel-wrap end-->
                                            </div>
                                        </div>
                                        <!--container end-->
                                    </div>
                                    <!--main-content end-->
                                    <div class="container-full">
                                        <div class="pm-h-parallax">
                                            <div class="pm-h-container">
                                                <div class="pm-hc-item">
                                                    <div class="pm-h-cont">
                                                        <h4>Smart Property Care</h4>
                                                        <p>DRE handles everything from tenant management and
                                                            maintenance to rent collection and reporting,
                                                            ensuring your property stays profitable, protected,
                                                            and stress-free.</p>
                                                    </div>
                                                    <div class="pm-h-btn">
                                                        <a href="<?= BASE_URL ?>property-management">Discover
                                                            More Services</a>
                                                        <a href="<?= BASE_URL ?>property-management#contact-frm">Get
                                                            Free Consultation</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="pm-icon-bg"><img src="images/pm-sec-bg.png" alt=""
                                                    width="450px"></div>
                                        </div>
                                    </div>
                                    <!-- parallex section  -->

                                    <div class="container">
                                        <div class="customer-testi">

                                            <div class="row d-flex align-items-center justify-content-center">
                                                <div class="col-lg-4 col-12  ">
                                                    <div class="crb-item-1">
                                                        <img src="images/why-c.jpg" alt="" width="100%">
                                                        <div class="overlay"></div>
                                                        <div class="review-ot">
                                                            <div class="customer-review-box">
                                                                <div>
                                                                    <script defer async
                                                                        src='https://cdn.trustindex.io/loader.js?8439f8f7278b1772df3652bb68c'></script>
                                                                </div>
                                                                <h4>Words That Build Trust</h4>
                                                                <p>We take pride in creating smooth and
                                                                    rewarding real estate experiences across
                                                                    Dubai.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-7 col-12 ">
                                                    <script defer async
                                                        src='https://cdn.trustindex.io/loader.js?278f7d97247a176def963ab3118'></script>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="container mt-5">
                                        <div class="insta-post-box">
                                            <div class="insta-post-h">
                                                <a href="https://www.instagram.com/drehomes_realestate/" target="_blank"
                                                    class="me-2"><i class="fa-brands fa-instagram"></i></a>
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
                                                                target="_blank" class="insta-item insta-square-card">
                                                                <img src="<?= BASE_URL_IMG . $item['media_url'] ?>" alt="">
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if (isset($insta_items[1])): ?>
                                                            <?php $item = $insta_items[1]; ?>
                                                            <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                target="_blank" class="insta-item insta-reel-card">
                                                                <img src="<?= BASE_URL_IMG . $item['media_url'] ?>" alt="">
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- COLUMN 2 -->
                                                    <div class="insta-column">
                                                        <?php if (isset($insta_items[2])): ?>
                                                            <?php $item = $insta_items[2]; ?>
                                                            <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                target="_blank" class="insta-item insta-reel-card">
                                                                <img src="<?= BASE_URL_IMG . $item['media_url'] ?>" alt="">
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if (isset($insta_items[3])): ?>
                                                            <?php $item = $insta_items[3]; ?>
                                                            <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                target="_blank" class="insta-item insta-square-card">
                                                                <img src="<?= BASE_URL_IMG . $item['media_url'] ?>" alt="">
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- COLUMN 3 -->
                                                    <div class="insta-column">
                                                        <?php if (isset($insta_items[4])): ?>
                                                            <?php $item = $insta_items[4]; ?>
                                                            <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                target="_blank" class="insta-item insta-square-card">
                                                                <img src="<?= BASE_URL_IMG . $item['media_url'] ?>" alt="">
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if (isset($insta_items[5])): ?>
                                                            <?php $item = $insta_items[5]; ?>
                                                            <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                target="_blank" class="insta-item insta-reel-card">
                                                                <img src="<?= BASE_URL_IMG . $item['media_url'] ?>" alt="">
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- COLUMN 4 -->
                                                    <div class="insta-column">
                                                        <?php if (isset($insta_items[6])): ?>
                                                            <?php $item = $insta_items[6]; ?>
                                                            <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                target="_blank" class="insta-item insta-reel-card">
                                                                <img src="<?= BASE_URL_IMG . $item['media_url'] ?>" alt="">
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if (isset($insta_items[7])): ?>
                                                            <?php $item = $insta_items[7]; ?>
                                                            <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                target="_blank" class="insta-item insta-square-card">
                                                                <img src="<?= BASE_URL_IMG . $item['media_url'] ?>" alt="">
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- COLUMN 5 -->
                                                    <div class="insta-column">


                                                        <?php if (isset($insta_items[9])): ?>
                                                            <?php $item = $insta_items[9]; ?>
                                                            <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                target="_blank" class="insta-item insta-square-card">
                                                                <img src="<?= BASE_URL_IMG . $item['media_url'] ?>" alt="">
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if (isset($insta_items[8])): ?>
                                                            <?php $item = $insta_items[8]; ?>
                                                            <a href="<?= !empty($item['instagram_url']) ? htmlspecialchars($item['instagram_url']) : '#' ?>"
                                                                target="_blank" class="insta-item insta-reel-card">
                                                                <img src="<?= BASE_URL_IMG . $item['media_url'] ?>" alt="">
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


                                <?php
                                $footer_details = select_row('tbl_footer_info', 'is_active=1');
                                ?>
                                <!--<div class="height-emulator"></div>-->
                                <button onclick="Popin('open')" class="whatsapp-icon-fixed-2 ls-btn live-agent-btn">
                                    <span class="agent-status"></span>
                                    <i class="fa-solid fa-video me-1"> </i> Live Agent
                                </button>
                                <footer class="main-footer">
                                    <div class="container">
                                        <div class="footer-inner">
                                            <div class="row">
                                                <!-- footer-widget -->
                                                <div class="col-lg-3">
                                                    <div class="footer-widget">
                                                        <div>
                                                            <figure style="width: 180px"><img
                                                                    src="<?= BASE_URL ?>images/logo-w.png"
                                                                    alt="Dre Logo" width="100%">
                                                            </figure>
                                                        </div>
                                                        <div class="footer-widget-title"></div>
                                                        <div class="footer-social">
                                                            <span class="footer-social-title">Follow Us</span>
                                                            <div class="clearfix"></div>
                                                            <div class="footer-social-wrap">
                                                                <a href="<?= $header_details['header_fb_link'] ?>"
                                                                    target="_blank" class="mt-1"><i
                                                                        class="fa-brands fa-facebook-f"></i></a>
                                                                <a href="<?= $header_details['header_instagram_link'] ?>"
                                                                    target="_blank" class="mt-1"><i
                                                                        class="fa-brands fa-instagram"></i></a>
                                                                <a href="<?= $header_details['header_youtube_link'] ?>"
                                                                    target="_blank" class="mt-1"><i
                                                                        class="fa-brands fa-youtube"></i></a>
                                                                <a href="<?= $header_details['header_linkedin_link'] ?>"
                                                                    target="_blank" class="mt-1"><i
                                                                        class="fa-brands fa-linkedin"></i></a>
                                                                <a href="<?= $header_details['header_spotify_link'] ?>"
                                                                    target="_blank" class="mt-1"><i
                                                                        class="fa-brands fa-tiktok"></i></a>
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <div class="footer-qr">
                                                            <p class="footer-social-title">Permit Number: 102639
                                                            </p>
                                                            <!-- <div class="clearfix"></div>-->
                                                            <!--<img src="<?= BASE_URL ?>images/dre-qr-code-footer.png" alt="Dre QR Code" style="width:100px;">-->
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- footer-widget  end-->
                                                <!-- footer-widget -->
                                                <div class="col-lg-3">
                                                    <div class="footer-widget">
                                                        <div class="footer-widget-title">Community</div>
                                                        <div class="footer-widget-content">
                                                            <div class="footer-list footer-box  footer-numbers">
                                                                <ul>
                                                                    <?php
                                                                    $query = mysqli_query($conn, "
                                                                                SELECT 
                                                                                    tc.comm_slug,
                                                                                    tc.comm_heading,
                                                                                    COUNT(*) AS total_proj 
                                                                                FROM tbl_proj AS tp 
                                                                                INNER JOIN tbl_community AS tc 
                                                                                    ON tp.prj_comm_id = tc.comm_id 
                                                                                WHERE tc.is_active = 1
                                                                                GROUP BY tc.comm_id 
                                                                                ORDER BY total_proj DESC 
                                                                                LIMIT 5 OFFSET 1 
                                                                            ");

                                                                    while ($row = mysqli_fetch_array($query)) {
                                                                        ?>
                                                                        <li>
                                                                            <a
                                                                                href="<?= BASE_URL ?>community/<?= $row['comm_slug'] ?>">
                                                                                <?= $row['comm_heading'] ?>
                                                                            </a>
                                                                            <span
                                                                                class="f-number"><?= $row['total_proj'] ?></span>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- footer-widget  end-->
                                                <!-- footer-widget -->
                                                <div class="col-lg-3">
                                                    <div class="footer-widget">
                                                        <div class="footer-widget-title">Developer</div>
                                                        <div class="footer-widget-content">
                                                            <div class="footer-list footer-box  footer-numbers">
                                                                <ul>
                                                                    <?php
                                                                    $query = mysqli_query($conn, 'select td.dev_slug,td.dev_heading,count(*) as total_proj from tbl_proj as tp INNER JOIN tbl_developer as
                                            td ON tp.prj_dev_id=td.dev_id GROUP BY td.dev_id order by total_proj desc limit 5');
                                                                    while ($row = mysqli_fetch_array($query)) {

                                                                        ?>
                                                                        <li>
                                                                            <a
                                                                                href="<?= BASE_URL ?>developer/<?= $row['dev_slug'] ?>">
                                                                                <?= $row['dev_heading'] ?></a>
                                                                            <span
                                                                                class="f-number"><?= $row['total_proj'] ?></span>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- footer-widget  end-->
                                                <!-- footer-widget -->
                                                <div class="col-lg-3">
                                                    <div class="footer-widget">
                                                        <div class="footer-widget-title">Get in Touch</div>
                                                        <div class="footer-widget-content">
                                                            <div class="footer-list footer-box  ">
                                                                <ul class="footer-contacts  ">
                                                                    <li> <span>Headquarters:</span><a
                                                                            href="https://maps.app.goo.gl/zHNjkBSkwTs6xGsp8"
                                                                            target="_blank"><?= $footer_details['footer_address'] ?></a>
                                                                    </li>
                                                                    <li> <span>Town Square Branch:</span><a
                                                                            href="https://maps.app.goo.gl/42FBpCmPsNUR667V6"
                                                                            target="_blank"><?= $footer_details['footer_address2'] ?></a>
                                                                    </li>
                                                                    <li><span>Toll Free:</span><a
                                                                            href="tel:<?= $footer_details['footer_contact_number'] ?>"><?= $footer_details['footer_contact_number'] ?></a>
                                                                    </li>
                                                                    <li><span>Hotline :</span><a
                                                                            href="tel:<?= $footer_details['footer_contact_number2'] ?>"><?= $footer_details['footer_contact_number2'] ?></a>
                                                                    </li>
                                                                    <li><span>Mail :</span><a
                                                                            href="mailto:<?= $footer_details['footer_email_id'] ?>"
                                                                            target="_blank"><?= $footer_details['footer_email_id'] ?></a>
                                                                    </li>
                                                                </ul>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- footer-widget  end-->
                                            </div>
                                            <!-- footer-widget-wrap end-->
                                        </div>
                                        <div class="footer-bottom d-flex justify-content-center align-items-center">
                                            <div class="f-text-box" style="width:50%; margin-bottom:10px;">
                                                <?= $footer_details['footer_copyright_text'] ?>
                                                <div class="f-text-box mt-3"><a
                                                        href="<?= BASE_URL ?>privacy-policy">Privacy
                                                        Policy</a><span style="color:#5f574c" class="mx-3">|</span><a
                                                        href="<?= BASE_URL ?>terms-conditions">T&amp;C</a></div>
                                            </div>

                                        </div>

                                    </div>

                                </footer>


                                <script>
                                    (function (w, d, s, u) {
                                        w.gbwawc = {
                                            url: u,
                                            options: {
                                                waId: "971585837346",
                                                siteName: "DRE",
                                                siteTag: "Online",
                                                siteLogo: "https://drehomes.com/images/dre-logo-wa.svg",
                                                widgetPosition: "RIGHT",
                                                welcomeMessage: "Welcome to DRE!!!",
                                                brandColor: "#25D366",
                                                customQuestion: "Just visited your website! Would like to know more about it!",
                                                startChatBtnTxt: "Chat Now",
                                                version: "v2",
                                                widgetPositionMarginX: 12,
                                                widgetPositionMarginY: 12,
                                            },
                                        };
                                        var h = d.getElementsByTagName(s)[0],
                                            j = d.createElement(s);
                                        j.async = true;
                                        j.src = u + "/whatsapp-widget.min.js?_=" + "2026-04-30 16";
                                        h.parentNode.insertBefore(j, h);
                                    })(window, document, "script", "https://waw.gallabox.com");
                                </script>
                                <!--<script>
        (function (w, d, s, u) {
            w.gbwawc = {
                url: u,
                options: {
                    waId: "971528413920",
                    siteName: "DRE",
                    siteTag: "Online",
                    siteLogo: "https://drehomes.com/images/dre-logo-wa.svg",
                    widgetPosition: "RIGHT",
                    welcomeMessage: "Welcome to DRE!!!",
                    brandColor: "#25D366",
                    customQuestion: "Just visited your website! Would like to know more about it!",
                    startChatBtnTxt: "Chat Now",
                    version: "v2",
                    widgetPositionMarginX: 12,
                    widgetPositionMarginY: 12,
                },
            };
            var h = d.getElementsByTagName(s)[0],
                j = d.createElement(s);
            j.async = true;
            j.src = u + "/whatsapp-widget.min.js?_=" + "2026-04-22 13";
            h.parentNode.insertBefore(j, h);
        })(window, document, "script", "https://waw.gallabox.com");
    </script>-->
                                <!--<div class="whatsapp-icon-fixed">
            <a href="https://wa.me/971527543243?text=Hi%2C%20I%20came%20across%20your%20website%20and%20would%20like%20to%20know%20more%20about%20your%20real%20estate%20listings%20in%20Dubai." target="_blank">
            <img src="<?= BASE_URL ?>/images/wa-chat-logo.png" alt="Whatsapp Logo" width="80px">
            </a>
        </div>-->
                                <!--main-footer end-->
                            </div>
                            <!--warpper end-->

                            <div class="mob-nav-overlay fs-wrapper"></div>
                            <div class="body-overlay fs-wrapper wishlist-wrap-overlay clwl_btn"></div>


                            <!-- Loan Modal -->
                            <div class="loan-register-container">
                                <div class="loan-register_box">
                                    <div class="loan-register-holder">
                                        <div class="loan-register-wrap ">
                                            <div class="loan-register_bg">
                                                <div class="lr_title">
                                                    <h4>Apply for a Loan</h4>
                                                    <h5>Get Started Today</h5>
                                                </div>
                                                <div class="loan-register_contacts-wrap">
                                                    <!--<h4>Need Assistance?</h4>-->
                                                    <a href="<?= BASE_URL ?>about-us.php"> Get to know Us</a>
                                                    <div class="svg-corner svg-corner_white"
                                                        style="bottom:0;left:  -39px;"></div>
                                                    <div class="svg-corner svg-corner_white"
                                                        style="bottom:0;right:  -39px;transform: rotate(90deg)">
                                                    </div>
                                                </div>
                                                <div class="loan-register_bg-dec"></div>
                                            </div>
                                            <div class="loan-register tabs-act fl-wrap">
                                                <ul class="tabs-menu">
                                                    <li class="current"><a href="#loan-tab-1">Loan
                                                            Application</a></li>
                                                </ul>
                                                <div id="loan-tabs-container">
                                                    <div class="tab">
                                                        <div id="loan-tab-1" class="tab-content first-tab">
                                                            <div class="custom-form">
                                                                <form action="" method="post" id="mortgage-frm">
                                                                    <div class="cs-intputwrap">
                                                                        <i class="fa-light fa-user"></i>
                                                                        <input type="text" name="name"
                                                                            placeholder="Enter Full Name">
                                                                    </div>
                                                                    <div class="cs-intputwrap">
                                                                        <i class="fa-light fa-envelope"></i>
                                                                        <input type="text" name="email"
                                                                            placeholder="Enter Your Email">
                                                                    </div>
                                                                    <div class="cs-intputwrap">
                                                                        <input type="text" name="mobile"
                                                                            placeholder="Enter Your Number"
                                                                            id="comm_dev_phone">
                                                                    </div>

                                                                    <div class="cs-intputwrap">
                                                                        <i class="fa-light fa-comment"></i>

                                                                        <textarea class="ms-ht" name="message" rows=""
                                                                            cols="" placeholder='Message'></textarea>
                                                                    </div>
                                                                    <div class="cs-intputwrap">

                                                                        <img id="captcha_mortgcal"
                                                                            src="<?= BASE_URL ?>captcha/captcha-mortgcal.php"
                                                                            alt="CAPTCHA" style="width:190px">
                                                                    </div>
                                                                    <div class="cs-intputwrap">

                                                                        <input type="text" id="captcha_mortgcal_text"
                                                                            name="captcha_mortgcal_text"
                                                                            class="form-control"
                                                                            placeholder="Enter Captcha">
                                                                    </div>
                                                                    <div class="cs-intputwrap">

                                                                        <button type="button" class="commentssubmit"
                                                                            onclick="captcha_mortgcal_refresh()">Regenerate
                                                                            captcha</button>
                                                                    </div>
                                                                    <input type="hidden"
                                                                        name="mortg_phone_number_hidden">

                                                                    <button type="submit" class="commentssubmit"
                                                                        name="mortg_submit">Submit
                                                                        Application</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="submit"
                                                        class="commentssubmit close-modal close-loan-form">Close</button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="body-overlay fs-wrapper loan-overlay close-loan-form"></div>
                            </div>
                            <!-- apply loan end -->

                            <!-- Filter Modal Index 1 -->
                            <!-- Hidden Filter Modal -->
                            <div class="filterh1-container">
                                <div class="filterh1_box">
                                    <div class="filterh1-holder">
                                        <div class="filterh1-wrap">
                                            <div class="index-hd-filter">

                                                <!-- Bedrooms -->
                                                <div class="">
                                                    <div class="cs-intputwrap">
                                                        <i class="fa-light fa-bed"></i>
                                                        <select name="proj_bedroom[]" multiple data-placeholder="Bed"
                                                            class="chosen-select on-radius no-search-select">
                                                            <option>Show All</option>
                                                            <option>Studio</option>
                                                            <option>1</option>
                                                            <option>2</option>
                                                            <option>3</option>
                                                            <option>4</option>
                                                            <option>5</option>
                                                            <option>6+</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Price Min -->
                                                <div class="">
                                                    <div class="cs-intputwrap">
                                                        <i class="fa-light fa-tag"></i>
                                                        <select name="price_min" data-placeholder="Price Min"
                                                            class="chosen-select on-radius no-search-select">
                                                            <option>Min</option>
                                                            <option>AED 500,000</option>
                                                            <option>AED 1,000,000</option>
                                                            <option>AED 2,000,000</option>
                                                            <option>AED 5,000,000</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Price Max -->
                                                <div class="">
                                                    <div class="cs-intputwrap">
                                                        <i class="fa-light fa-tag"></i>
                                                        <select name="price_max" data-placeholder="Price Max"
                                                            class="chosen-select on-radius no-search-select">
                                                            <option>Max</option>
                                                            <option>AED 1,000,000</option>
                                                            <option>AED 2,000,000</option>
                                                            <option>AED 5,000,000</option>
                                                            <option>AED 10,000,000</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Amenities -->
                                                <div class="">
                                                    <div class="filter-tags-title">Amenities</div>
                                                    <div class="filter-tags">
                                                        <?php
                                                        $amenities_query = mysqli_query($conn, "SELECT featfac_id, featfac_name FROM tbl_proj_featfac WHERE is_active = 1 ORDER BY featfac_name");
                                                        $selected_amenities = isset($_GET["amenities"]) ? (array) $_GET["amenities"] : [];
                                                        ?>
                                                        <ul class="no-list-style">
                                                            <?php while ($amenity = mysqli_fetch_assoc($amenities_query)):
                                                                $id = (int) $amenity["featfac_id"];
                                                                $name = htmlspecialchars($amenity["featfac_name"]);
                                                                $isChecked = in_array($id, $selected_amenities) ? "checked" : "";
                                                                ?>
                                                                <li>
                                                                    <input type="checkbox" name="amenities[]"
                                                                        value="<?= $id ?>" class="amenity-checkbox"
                                                                        id="amenity-<?= $id ?>" <?= $isChecked ?>>
                                                                    <label for="amenity-<?= $id ?>"><?= $name ?></label>
                                                                </li>
                                                            <?php endwhile; ?>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <!-- Submit buttons -->
                                                <div class="cr-modal-footer">
                                                    <a class="commentssubmit me-1" href="javascript:void(0)"
                                                        onclick="redirectResidentialSearch();">Apply</a>
                                                    <div class="commentssubmit close-filterh1-form ms-1">Close
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="body-overlay fs-wrapper filterh1-overlay close-filterh1-form"></div>
                            </div>

                            <!-- Redirect JavaScript -->

                            <!-- Filter Modal Index 1 End -->

                            <!-- Filter Modal Index 2 -->
                            <div class="filterh2-container">
                                <div class="filterh2_box">
                                    <div class="filterh2-holder">
                                        <div class="filterh2-wrap">
                                            <div class="index-hd-filter">
                                                <!-- listsearch-input-item -->
                                                <div class="4">
                                                    <div class="cs-intputwrap">
                                                        <i class="fa-light fa-home"></i>
                                                        <select data-placeholder="Property Type"
                                                            class="chosen-select on-radius no-search-select">
                                                            <option value="" disabled selected>
                                                                Property Type
                                                            </option>
                                                            <option>Show All</option>
                                                            <option>Office Space</option>
                                                            <option>Retail</option>
                                                            <option>Whole Building</option>
                                                            <option>Full Floor</option>
                                                            <option>Factory</option>
                                                            <option>Warehouse</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- listsearch-input-item -->

                                                <!-- listsearch-input-item -->
                                                <div class="3">
                                                    <div class="cs-intputwrap">
                                                        <i class="fa-light fa-tag"></i>
                                                        <select data-placeholder="Price Min"
                                                            class="chosen-select on-radius no-search-select">
                                                            <option value="" disabled selected>Price
                                                                Min
                                                            </option>
                                                            <option>Show All</option>
                                                            <option>AED 500,000</option>
                                                            <option>AED 750,000</option>
                                                            <option>AED 1,000,000</option>
                                                            <option>AED 2,000,000</option>
                                                            <option>AED 3,000,000</option>
                                                            <option>AED 4,000,000</option>
                                                            <option>AED 5,000,000</option>
                                                            <option>AED 6,000,000</option>
                                                            <option>AED 7,000,000</option>
                                                            <option>AED 8,000,000</option>
                                                            <option>AED 9,000,000</option>
                                                            <option>AED 10,000,000</option>
                                                            <option>AED 15,000,000</option>
                                                            <option>AED 25,000,000</option>
                                                            <option>AED 35,000,000</option>
                                                            <option>AED 45,000,000</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- listsearch-input-item -->
                                                <div class="3">
                                                    <div class="cs-intputwrap">
                                                        <i class="fa-light fa-tag"></i>
                                                        <select data-placeholder="Price Max"
                                                            class="chosen-select on-radius no-search-select">
                                                            <option value="" disabled selected>Price
                                                                Max
                                                            </option>
                                                            <option>Show All</option>
                                                            <option>AED 500,000</option>
                                                            <option>AED 750,000</option>
                                                            <option>AED 1,000,000</option>
                                                            <option>AED 2,000,000</option>
                                                            <option>AED 3,000,000</option>
                                                            <option>AED 4,000,000</option>
                                                            <option>AED 5,000,000</option>
                                                            <option>AED 6,000,000</option>
                                                            <option>AED 7,000,000</option>
                                                            <option>AED 8,000,000</option>
                                                            <option>AED 9,000,000</option>
                                                            <option>AED 10,000,000</option>
                                                            <option>AED 15,000,000</option>
                                                            <option>AED 25,000,000</option>
                                                            <option>AED 35,000,000</option>
                                                            <option>AED 45,000,000</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- listsearch-input-item -->
                                                <!-- listsearch-input-item -->
                                                <div class="filter-tags-title">Property Area</div>
                                                <div class="cs-intputwrap mt-3">
                                                    <div class="price-range-wrap ">
                                                        <label>Area (sq.ft)</label>
                                                        <div class="price-rage-item">
                                                            <input type="text" class="price-range-double" data-min="100"
                                                                data-max="100000" name="price-range1" data-step="1"
                                                                value="1">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- listsearch-input-item -->

                                                <div class="cr-modal-footer">

                                                    <div class="commentssubmit close-filterh2-form ms-1">Close
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="body-overlay fs-wrapper filterh2-overlay close-filterh2-form"></div>
                            </div>
                            <!-- Filter Modal Index 2 End -->



                            <!-- progress-bar  -->
                            <div class="progress-bar-wrap">
                                <div class="progress-bar color-bg"></div>
                            </div>
                            <!-- progress-bar end -->
                            <!--map-modal -->
                            <div class="map-modal-wrap">
                                <div class="map-modal-wrap-overlay"></div>
                                <div class="map-modal-item">
                                    <div class="map-modal-container fl-wrap">
                                        <h3> <span>Listing Title </span></h3>
                                        <div class="map-modal-close"><i class="fa-regular fa-xmark"></i></div>
                                        <div class="map-modal fl-wrap">
                                            <div id="singleMap" data-latitude="40.7" data-longitude="-73.1">
                                            </div>
                                            <div class="scrollContorl"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--map-modal end -->
                        </div>
                        <!-- Main end -->

                        <!-- testimonial slider modal starts -->

                        <!-- Modal HTML -->
                        <div id="testimonialModal" class="testimonial-modal">
                            <div class="testimonial-modal-content">
                                <span class="testimonial-modal-close">&times;</span>
                                <p id="testimonialModalText"></p>
                            </div>
                        </div>

                        <!-- Modal Script -->
                        <script>
                            // Open modal using event delegation
                            document.addEventListener('click', function (e) {
                                if (e.target && e.target.classList.contains('pop-read-more')) {
                                    e.preventDefault();
                                    var fullText = e.target.getAttribute('data-fulltext');
                                    document.getElementById('testimonialModalText').textContent = fullText;
                                    document.getElementById('testimonialModal').style.display = 'block';
                                }
                            });

                            // Close modal
                            document.querySelector('.testimonial-modal-close').addEventListener('click', function () {
                                document.getElementById('testimonialModal').style.display = 'none';
                            });

                            // Close when clicking outside modal
                            window.addEventListener('click', function (e) {
                                var modal = document.getElementById('testimonialModal');
                                if (e.target === modal) {
                                    modal.style.display = 'none';
                                }
                            });
                        </script>



                        <?php
                        $query = mysqli_query($conn, "select * from tbl_position where is_active=1");
                        $i = 1;
                        while ($row = mysqli_fetch_array($query)) {
                            ?>
                            <div id="modal<?= $i ?>" class="custom-modal-cr">
                                <div class="modal-content-cr">
                                    <div class="cr-modal-title">
                                        <h4><?= $row['pos_heading'] ?></h4>
                                        <span class="close-cr">&times;</span>
                                    </div>
                                    <div class="cr-modal-body">
                                        <?= $row['pos_descp'] ?>

                                    </div>
                                    <div class="cr-modal-footer">
                                        <a class="commentssubmit" href="#resume-form" id="apply-btn">Apply Now</a>
                                    </div>
                                </div>
                            </div>
                            <?php $i++;
                        } ?>

                        <script>
                            document.querySelectorAll('.modal-box-cr').forEach(box => {
                                box.addEventListener('click', () => {
                                    const target = box.getAttribute('data-modal');
                                    document.getElementById(target).style.display = 'flex';
                                });
                            });

                            document.querySelectorAll('.close-cr').forEach(btn => {
                                btn.addEventListener('click', () => {
                                    btn.closest('.custom-modal-cr').style.display = 'none';
                                });
                            });
                            document.querySelectorAll('#apply-btn').forEach(btn => {
                                btn.addEventListener('click', () => {
                                    btn.closest('.custom-modal-cr').style.display = 'none';
                                });
                            });

                            window.addEventListener('click', function (e) {
                                if (e.target.classList.contains('custom-modal-cr')) {
                                    e.target.style.display = 'none';
                                }
                            });
                        </script>
                        <!--Careers Modals ends -->

                        <!-- autocomplete form script starts -->
                        <script>
                            function autocomplete(inp, arr) {

                                var currentFocus;

                                inp.addEventListener("input", function (e) {
                                    var a, b, i, val = this.value;
                                    closeAllLists();
                                    if (!val) { return false; }
                                    currentFocus = -1;
                                    a = document.createElement("DIV");
                                    a.setAttribute("id", this.id + "autocomplete-list");
                                    a.setAttribute("class", "autocomplete-items");
                                    this.parentNode.appendChild(a);
                                    for (i = 0; i < arr.length; i++) {
                                        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                                            b = document.createElement("DIV");
                                            b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                                            b.innerHTML += arr[i].substr(val.length);
                                            b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                                            b.addEventListener("click", function (e) {
                                                inp.value = this.getElementsByTagName("input")[0].value;
                                                closeAllLists();
                                            });
                                            a.appendChild(b);
                                        }
                                    }
                                });

                                inp.addEventListener("keydown", function (e) {
                                    var x = document.getElementById(this.id + "autocomplete-list");
                                    if (x) x = x.getElementsByTagName("div");
                                    if (e.keyCode == 40) {
                                        currentFocus++;
                                        addActive(x);
                                    } else if (e.keyCode == 38) {
                                        currentFocus--;
                                        addActive(x);
                                    } else if (e.keyCode == 13) {
                                        e.preventDefault();
                                        if (currentFocus > -1) {
                                            if (x) x[currentFocus].click();
                                        }
                                    }
                                });
                                function addActive(x) {

                                    if (!x) return false;

                                    removeActive(x);
                                    if (currentFocus >= x.length) currentFocus = 0;
                                    if (currentFocus < 0) currentFocus = (x.length - 1);

                                    x[currentFocus].classList.add("autocomplete-active");
                                }
                                function removeActive(x) {
                                    for (var i = 0; i < x.length; i++) {
                                        x[i].classList.remove("autocomplete-active");
                                    }
                                }
                                function closeAllLists(elmnt) {
                                    var x = document.getElementsByClassName("autocomplete-items");
                                    for (var i = 0; i < x.length; i++) {
                                        if (elmnt != x[i] && elmnt != inp) {
                                            x[i].parentNode.removeChild(x[i]);
                                        }
                                    }
                                }

                                document.addEventListener("click", function (e) {
                                    closeAllLists(e.target);
                                });
                            }


                            var developers = ["Emaar", "Damac", "Danube", "Majid Al Futtaim", "Arista Properties", "Amaal Properties"];


                            autocomplete(document.getElementById("myInput"), developers);
                        </script>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                function setEqualSlideHeights() {
                                    const slides = document.querySelectorAll('.equal-elc .swiper-slide');
                                    let maxHeight = 0;

                                    // Reset height
                                    slides.forEach(slide => {
                                        slide.style.height = 'auto';
                                    });

                                    // Get tallest height
                                    slides.forEach(slide => {
                                        const height = slide.offsetHeight;
                                        if (height > maxHeight) maxHeight = height;
                                    });

                                    // Apply max height
                                    slides.forEach(slide => {
                                        slide.style.height = maxHeight + 'px';
                                    });
                                }

                                // Run once fully loaded
                                window.addEventListener('load', setEqualSlideHeights);

                                // Optional: Rerun on window resize
                                window.addEventListener('resize', setEqualSlideHeights);
                            });
                        </script>
                        <script>
                            function playVideoSection(videoUrl, button) {

                                const ratioContainer = button.closest('.ratio');

                                const videoContainer = ratioContainer.querySelector('.video-container');
                                const iframe = videoContainer.querySelector('iframe');

                                iframe.src = videoUrl + (videoUrl.includes('?') ? '&' : '?') + 'autoplay=1';

                                videoContainer.style.display = 'block';

                                const thumbnail = ratioContainer.querySelector('img');
                                if (thumbnail) {
                                    thumbnail.style.visibility = 'hidden';
                                }

                                button.style.display = 'none';
                            }
                        </script>



                        <!-- autocomplete form script ends -->

                        <!--=============== scripts  ===============-->
                        <script src="<?= BASE_URL ?>js/jquery.min.js"></script>
                        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
                        <script
                            src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/21.2.7/js/intlTelInput.min.js"></script>
                        <script src="<?= BASE_URL ?>js/plugins.js"></script>
                        <script src="<?= BASE_URL ?>js/scripts2.js?cache=2121223"></script>
                        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
                        <script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js"></script>
                        <!-- <script src="<?= BASE_URL ?>js/custom.js?cache=212121"></script> -->
                        <script src="<?= BASE_URL ?>js/custom.js"></script>
                        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
                        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
                        <script>
                            AOS.init();
                        </script>

                        <script>
                            Fancybox.bind("[data-fancybox='dre-gallery']", {
                                Thumbs: {
                                    autoStart: true
                                }
                            });
                        </script>

                        </body>


                        </html>