<?php
include('db/config.php');
$meta_tags=select_row('tbl_tags','id=18 and is_active=1');
include('common/header.php');
$career_content=select_row('tbl_career_content','is_active=1');
$nonencoded_url="https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$career_details=mysqli_fetch_array(mysqli_query($conn,"select * from tbl_career_content where is_active=1"));

require 'ajax/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; 


if(isset($_POST['submit_resume']))
{

   $first_name=$_POST['first_name'];
   $email_id=$_POST['email_id'];
   $phone=$_POST['career_phone_hidden'].' '.$_POST['phone']; 
   $linkedin_url=$_POST['linkedin_url'];
   $pos=$_POST['pos'];
   $resume='resume_'.rand(0,1000).'_'.$_FILES['resume']['name'];
   $ip_address=get_client_ip();
   $ddate = date(' d-m-Y H:i:s');
   

   $query=mysqli_query($conn,"insert into tbl_resume
                                                SET first_name='$first_name',
                                                    email_id='$email_id',
                                                    phone='$phone',
                                                    linkedin_url='$linkedin_url',
                                                    resume='$resume',
                                                    pos='$pos',
                                                    ip_address='$ip_address',
                                                    is_active='1'");


     if($query)
    {

      move_uploaded_file($_FILES['resume']['tmp_name'],"resume/$resume");


  $mail = new PHPMailer(true);
  $mail->setFrom('donotreply@drehomes.com', "$first_name");
  $mail->addAddress('newcareer@drehomes.com', 'newcareer@drehomes.com');
  $mail->addReplyTo('donotreply@drehomes.com', 'Dre');
  $mail->addCC('hr@drehomes.com');
  
   //$mail->SMTPDebug = 2; // 2 will show connection and server message errors
   $mail->Debugoutput = 'html'; // Set the output format to HTML for better readability

  
   // Add an attachment
    $file_path = realpath('resume/'.$resume.'');
    $mail->addAttachment($file_path);


  $mail->isHTML(false);                                
  $mail->Subject = "$ddate : Enquiry from Drehomes:Resume";
   $mail->Body    = "Name:- " . $first_name."\r\n"."Email ID:- " . $email_id."\r\n" . "Mobile:- " . $phone . "\r\n" . "Linkedin URL:- " . $linkedin_url . "\r\n" . "Position:- " . $pos . "\r\n"  ."IP Addr:- " . get_client_ip();
  $mail->send();

    echo "<script>alert('Your details has been submitted successfully ! We will get back to your shortly.')</script>";
    
    


    }

}
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
                                    <h1>Join DRE</h1>
                                </div>
                            </div>
                        </div>
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="<?=BASE_URL_IMG?>career/<?=$career_details['career_cont_img']?>"
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
                    <a href="<?= BASE_URL ?>index.php">Home</a><span>Careers</span>
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
                                            <?php
                                            // "Build Your Future With Us": edited in the admin under Careers > Careers Page.
                                            // The text below is shown only while nothing is saved there.
                                            $intro_heading = trim((string) ($career_details['career_intro_heading'] ?? '')) ?: 'Build Your Future With Us';
                                            $intro_text = trim((string) ($career_details['career_intro_text'] ?? '')) ?: 'At DRE, we empower ambitious minds with opportunities to grow, lead, and make an impact in Dubai’s evolving real estate landscape.';
                                            // Colours and fonts pasted along with the text are dropped, so the section looks like the rest of the page.
                                            $intro_body = preg_replace('/\s(style|class)="[^"]*"/i', '', trim((string) ($career_details['career_cont_descp'] ?? '')));
                                            if (trim(strip_tags($intro_body)) === '') {
                                                $intro_body = '<p>Join a diverse and vibrant community. We are an award-winning real estate firm nestled in the heart of the Dubai Hills estate. Our new flagship Head Office is a sustainable hub with a unique human-centric approach where innovation and creativity set a new benchmark within our industry.</p>'
                                                    . '<p>At present, we are actively seeking like-minded professionals to join us on our journey, where together we can build and grow while enhancing your career and financial aspirations.</p>'
                                                    . '<p>If you have experience in real estate, we will take you further than you have ever been; if you are new to this market, our dedicated team of coaches will give you the tools and motivation to help you achieve the outcome you desire.</p>';
                                            }
                                            ?>
                                            <div class="boxed-content-title">
                                                <h3><?= htmlspecialchars($intro_heading, ENT_QUOTES, 'UTF-8') ?></h3>
                                                <p><?= nl2br(htmlspecialchars($intro_text, ENT_QUOTES, 'UTF-8')) ?></p>
                                            </div>
                                            <!--boxed-content-item-->
                                            <div class="boxed-content-item">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <?= $intro_body ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--boxed-content-item end-->

                                            <!--boxed-content-item-->
                                            <div class="boxed-content-item">
                                                <div class="row box-grid-cr">
                                            <?php
                                            $query=mysqli_query($conn,"select * from tbl_position where is_active=1");
                                            $i=1;
                                            while($row=mysqli_fetch_array($query))
                                            {
                                            ?>
                                            <div class="col-lg-3 col-md-6">
                                            <div class="modal-box-cr" data-modal="modal<?=$i?>">
                                            <div><?=$row['pos_heading']?></div>
                                            <div><i class="fa-solid fa-angles-right"></i></div>
                                            </div>
                                            </div>
                                            <?php $i++;}?>

                                                  
                                                </div>
                                            </div>
                                            <!--boxed-content-item end-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="limit-box"></div>
                    </div>
                    <!--boxed-container end-->

                    <!-- partner section -->
                    <div class="container">
                        <div class="">
                            <div class="boxed-content ">
                                <!-- clients-carousel-wrap-->
                                <div class="clients-carousel-wrap">
                                    <div class="clients-carousel-title"><?=$career_details['career_award_heading']?></div>
                                    <div class="clients-carousel">
                                        <div class="swiper-container">
                                            <div class="swiper-wrapper">
                                                <?php
       $query=mysqli_query($conn,"select * from tbl_career_awards where is_active=1");
       while($row=mysqli_fetch_array($query))
       {
       ?>
                                                <div class="swiper-slide">
                                                    <a href="javascript:void(0)" class="client-item"><img
                                                            src="<?=BASE_URL_IMG?>career/awards/<?=$row['carr_award_img']?>" alt="<?=$row['carr_award_img_alt_tag']?>"></a>
                                                </div>
                                                        <?php }?>

                                                
                                            </div>
                                        </div>
                                        <div class="cc-button cc-button-next"><i class="fal fa-angle-right"></i></div>
                                        <div class="cc-button cc-button-prev"><i class="fal fa-angle-left"></i></div>
                                    </div>
                                </div>
                                <!-- clients-carousel-wrap end-->
                            </div>
                        </div>
                    </div>
                    <!-- partner section-->
                    <div class="container">
                        <div class="boxed-container">
                            <div class="boxed-content ">
                                <div class="banner-widget-wrap">
                                    <div class="bg-wrap bg-parallax-wrap-gradien fs-wrapper">
                                        <div class="bg" data-bg="<?=BASE_URL?>images/dre-careers-video-thumb.jpg"></div>
                                    </div>
                                    <div class="banner-widget_content">
                                        <div class="video-box-btn" id="html5-videos" data-html="#video1"><i
                                                class="fas fa-play"></i>
                                        </div>

                                    </div>
                                    <div style="display:none;" id="video1" class="popup_video"
                                        data-videolink="<?=BASE_URL_IMG?>career/video/<?=$career_details['career_cont_video']?>">
                                        <video class="lg-video-object lg-html5" controls preload="none">
                                            <source src="/" type="video/mp4">
                                        </video>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Video section-->
                    <!-- Employees key section -->
                    <div class="container">
                        <div class="boxed-container">
                            <div class="boxed-content ">
                                <div class="about-wrap boxed-content-item ch-dubai-box">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="about-title ab-hero">
                                                <h2><?=$career_details['career_keyfocus_heading']?></h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row services-wrapper">
                                        <div class="col-lg-12 career-focus-box">
                                            <div class="services-opions services-opions-grid">
                                                <ul>
                                                     <?php
                 $query=mysqli_query($conn,"select * from tbl_focus_area where is_active=1");
                 while($row=mysqli_fetch_array($query))
                 {
                 ?>

                                                    <li>
                         <i class="<?=$row['focus_fa_icon']?>"></i>
                                                        <h4><?=$row['focus_area_heading']?></h4>
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
                    <!-- Employees key section-->

                    <!--boxed-content-item-->
                    <div class="boxed-content-item mb-5 py-5" id="resume-form">
                        <div class="row ">
                            <div class="col-md-3 text-left mb-3">
                                <div class="fw_car_title">
                                    <h3>Send Us Your Resume</h3>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="custom-form career-f-box">
  <form action="" method="post" id="career-frm" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="cs-intputwrap">
                                                    <i class="fa-light fa-user"></i>
                                                        <input type="text" id="fname" name="first_name" placeholder="Enter Your Name.." autocomplete='off'>

                                                </div>
                                                <div class="cs-intputwrap">
                                                    
                                                         <input type="text" id="enq_phone" name="phone" placeholder="Enter Your Number.." autocomplete='off'>

                                                </div>
                                                <div class="cs-intputwrap">
                                                    <i class="fa-light fa-envelope"></i>
                                                      <input type="text" id="email_id" name="email_id" placeholder="Your Email Mail.." autocomplete='off'>

                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="cs-intputwrap">
                                                    <i class="fa-light fa-circle-user"></i>
                                                      <input type="text" id="country" name="linkedin_url" placeholder="Enter Your LinkedIn Profile.." autocomplete='off'>

                                                </div>
                                                <div class="cs-intputwrap">
                                                    <i class="fa-light fa-user-tie"></i>
                                                     <select name="pos" class='chosen-select on-radius no-search-select'>
  <option value="Commercial Sales Manager">Commercial Sales Manager</option>
  <option value="IT Assistant">IT Assistant</option>
  <option value="Luxury Property Sales Manager">Luxury Property Sales Manager</option>
  <option value="Off Plan Sales Manager">Off Plan Sales Manager</option>
  <option value="Property Associate">Property Associate</option>
  <option value="Sales & Leasing Manager">Sales & Leasing Manager</option>
  <option value="Senior Property Associate">Senior Property Associate</option>
  <option value="Talent Acquisition Coordinator">Talent Acquisition Coordinator</option>
</select>
                                                </div>
                                                <div class="cs-intputwrap">
                                                    <i class="fa-light fa-file"></i>
                                                       <input type="file" id="myFile" name="resume">

                                                </div>
                                            </div>
                                            <div class="col-12 gv-msg">
                                                <div class="cs-intputwrap">
                                                    <i class="fa-light fa-comment"></i>
                                                    <textarea placeholder="Message" class="" ></textarea>
                                                </div>
                                            </div>
                                             <div class="col-md-3">
    <img id="captcha" src="<?=BASE_URL?>captcha/captcha.php" alt="CAPTCHA" style="width:190px">
    </div>
    <div class="col-md-3">
     <input type="text" name="captcha_text" id="captcha_text" autocomplete="off">

    </div>

     <div class="col-md-3">
    <button type="button" class="commentssubmit commentssubmit_fw" onclick="captcha_refresh()">Regenerate captcha</button>

    </div>
                                        </div>
                                        
                                        <div class="cf-btn">
                                                        <input type='hidden' name='career_phone_hidden'>
             <button type="submit" class="commentssubmit commentssubmit_fw mt-5" value="Submit" name="submit_resume">Send</button>
</div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--boxed-content-item end-->

                    <!-- gallery starts -->
                    <div class="row">
                        <div class="col-12">
                            <div class="lightgallery custom-gallery">
                                <?php
          $query=mysqli_query($conn,"select * from tbl_career_gallery where is_active=1");
          while($row=mysqli_fetch_array($query))
          {
          ?>
                                <div class="gallery-it">
                                    <a href="<?=BASE_URL_IMG?>career/gallery/<?=$row['career_gallry_img']?>" class="popup-image overlay-wrap">
                                        <img src="<?=BASE_URL_IMG?>career/gallery/<?=$row['career_gallry_img']?>" alt="<?=$row['career_gallry_img_alt_tag']?>">
                                        <div class="overlay-b">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </a>
                                </div>
                   <?php }?>

                            </div>
                        </div>
                    </div>
                    <!-- gallery ends -->

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