<?php
include('db/config.php');
$meta_tags = select_row('tbl_tags', 'id=14 and is_active=1');
include('common/header.php');
$about_details = select_row('tbl_abt_content', 'is_active=1');

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
                                    <h1><?=$about_details['abt_content_title']?></h1>
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="<?=BASE_URL_IMG?>about/<?=$about_details['abt_content_bannr_img']?>"
                                data-scrollax="properties: { translateY: '30%' }">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--section-end-->
            <!--container-->
            <div class="container">
                <!--breadcrumbs-list-->
                <div class="breadcrumbs-list bl_flat">
                    <a href="<?=BASE_URL?>">Home</a><span>About Us</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
                </div>
                <!--breadcrumbs-list end-->
            </div>
            <!--container end-->
            <!--main-content-->
            <div class="main-content ms_vir_height">
                <!--container-->
                <div class="container">
                    <!--boxed-container-->
                    <div class="boxed-container">
                        <div class="boxed-content ">
                            <div class="about-wrap boxed-content-item">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="about-title ab-hero ab-hero2">
                                            <h2><?=$about_details['abt_content_heading']?></h2>
                                            <h4><?=$about_details['abt_content_sub_heading']?></h4>
                                        </div>
                                        <?=$about_details['abt_content_descp']?>
                                        <a href=" <?=$about_details['abt_content_btn_url']?>" class="commentssubmit" style="margin-top: 30px"> <?=$about_details['abt_content_btn_text']?></a>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="about-img ab_i2">
                                            <img src="<?=BASE_URL_IMG?>about/<?=$about_details['abt_content_img']?>" class="respimg" alt="">
                                            <!--<div class="ab_img_wideo_wrap">-->
                                            <!--    <div class="video-link" id="html5-videos" data-html="#video1"><i-->
                                            <!--            class="fas fa-play"></i><span> <?=$about_details['abt_content_vdo_text']?></span></div>-->
                                            <!--</div>-->
                                            <div style="display:none;" id="video1" class="popup_video"
                                                data-videolink="<?=BASE_URL_IMG?>about/<?=$about_details['abt_content_video']?>">
                                                <video class="lg-video-object lg-html5" controls preload="none">
                                                    <source src="" type="video/mp4">
                                                </video>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
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
                    <!--boxed-container end-->
                </div>
                <!--container end-->
                <!-- why choose dre starts -->
                <div class="container">
                    <div class="boxed-container">
                        <div class="boxed-content ">
                            <div class="about-wrap boxed-content-item">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="about-title ab-hero">
                                            <h2>What Sets DRE Apart? </h2>
                                            <h4>It’s not just what we do—it’s how we do it that makes all the difference.</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="row services-wrapper">
                                    <div class="col-lg-12">
                                        <div class="services-opions services-opions-grid">
                                            <ul> 
                                                <?php
                            $service=select_query('tbl_abt_service','is_active=1 order by abt_serv_id desc');
                            while($row=mysqli_fetch_array($service))
                            {
                            ?>  

                                                <li>
                                                    <i class="fal <?=$row['abt_serv_faicon']?>"></i>
                                                    <h4><?=$row['abt_serv_heading']?></h4>
                                                    <p><?=$row['abt_serv_descp']?></p>
                                                </li>
                            <?php }?>

                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- why choose dre ends -->
                <div class="container">
                    <div class="agent-bg-section">
                    <div class="fw_car_title-wrap">
                        <div class="fw_car_title">
                            <h3>Meet Our Management</h3>
                           
                        </div>
                    </div>
                    
                    <div class="agnet-carousel-wrap">
                        <div class="agnet-carousel">
                            <div class="row">
                                  <?php
  $team_query = select_query('tbl_team', 'team_company_owner=1 and is_active=1');
  while($team_row=mysqli_fetch_array($team_query))
  {
  ?> 
                                <div class="col-md-6">
                                    <div class="swiper-slide">
                                        <div class="agent-card-item">
                                            <div class="agent-card-item_media">
                                                <div class="agent-card-item_media-wrap">
                                                    <a href="<?=BASE_URL?>management/<?=$team_row['team_slug']?>"><img src="<?=BASE_URL_IMG?>team/<?=$team_row['team_img']?>" alt="<?=$team_row['team_img_alt']?>" class="respimg">
                                                    <div class="overlay"></div></a>
                                                    <div class="acim-wrap">
                                                        <ul>
                                                             <?php if(!empty($team_row['team_fblink'])){?>
                                <li><a href="<?=$team_row['team_fblink']?>"><i class="fa-brands fa-facebook"></i></a></li>
                                <?php } if(!empty($team_row['team_twitterlink'])){?>
                                <li><a href="<?=$team_row['team_twitterlink']?>"><i class="fa-brands fa-twitter"></i></a></li>
                                <?php } if(!empty($team_row['team_linkedinlink'])){?>
                                <li><a href="<?=$team_row['team_linkedinlink']?>"><i class="fa-brands fa-linkedin"></i></a></li>
                                <?php } if(!empty($team_row['team_instagramlink'])){?>
                                <li><a href="<?=$team_row['team_instagramlink']?>"><i class="fa-brands fa-instagram"></i></a></li>
                                <?php }?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="agent-card-item_text">
                                                <a href="<?=BASE_URL?>management/<?=$team_row['team_slug']?>">
                                                <div class="agent-card-item_text-item">
                                                    <h4><?=$team_row['team_name']?></h4>
                                                    <p><?=$team_row['team_desg']?></p>
                                                    
                                                </div>
                                                </a>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                           <?php }?>

                            </div>
                        </div>
                    </div>
                </div>
                </div>
                
                <!-- section end  -->

            </div>
            <!--main-content end-->
        </div>
        <!--content  end-->
        <?php include 'common/footer.php'; ?>