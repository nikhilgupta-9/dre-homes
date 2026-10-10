<?php
$footer_details=select_row('tbl_footer_info','is_active=1');
?>
<!--<div class="height-emulator"></div>-->
<!-- Live Agent button: now the video icon in the floating dock below -->
<!--<button onclick="Popin('open')" class="whatsapp-icon-fixed-2 ls-btn live-agent-btn">
  <span class="agent-status"></span> 
 <i class="fa-solid fa-video me-1">  </i> Live Agent
</button>-->
<!-- Floating dock: AI assistant, live video call, WhatsApp, dark mode (css/floating-dock.css, js/floating-dock.js) -->
<div id="floating-dock" class="floating-dock" role="toolbar" aria-label="Quick actions">
  <div class="drag-handle" title="Drag me">
    <span></span><span></span><span></span>
  </div>
<?php if (secret('ai_chat_enabled', false)): ?>
  <button type="button" class="dock-item dock-item-ai" data-dock="ai" aria-label="AI Assistant" data-label="AI Assistant">
    <i class="fa-solid fa-wand-magic-sparkles"></i>
  </button>
<?php endif; ?>
  <button type="button" class="dock-item dock-item-video" data-dock="video" aria-label="Live Video Call" data-label="Live Video Call">
    <span class="agent-status"></span>
    <i class="fa-solid fa-video"></i>
  </button>
  <a href="https://wa.me/971585837346?text=<?= rawurlencode('Just visited your website! Would like to know more about it!') ?>" target="_blank" rel="noopener" class="dock-item dock-item-whatsapp" aria-label="WhatsApp" data-label="WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
  </a>
  <button type="button" id="theme-toggle" class="dock-item dock-item-theme" aria-label="Dark Mode" aria-pressed="false" data-label="Dark Mode">
    <i class="fa-solid fa-moon"></i>
  </button>
</div>
<footer class="main-footer">
    <div class="container">
        <div class="footer-inner">
            <div class="row">
                <!-- footer-widget -->
                <div class="col-lg-3">
                    <div class="footer-widget">
                        <div>
                            <figure style="width: 180px">
                                <img loading="lazy" decoding="async" src="<?= BASE_URL_IMG ?>footer/<?=$footer_details['footer_logo']?>" alt="Dre Logo" width="100%">
                            </figure>
                            </figure>
                        </div>
                        <div class="footer-widget-title"></div>
                        <div class="footer-social">
                            <span class="footer-social-title">Follow Us</span>
                            <div class="clearfix"></div>
                            <div class="footer-social-wrap">
                                <a href="<?=$header_details['header_fb_link']?>" target="_blank"
                                    class="mt-1"><i class="fa-brands fa-facebook-f"></i></a>
                                <a href="<?=$header_details['header_instagram_link']?>" target="_blank" class="mt-1"><i
                                        class="fa-brands fa-instagram"></i></a>
                                <a href="<?=$header_details['header_youtube_link']?>" target="_blank" class="mt-1"><i
                                        class="fa-brands fa-youtube"></i></a>
                                <a href="<?=$header_details['header_linkedin_link']?>"
                                    target="_blank" class="mt-1"><i class="fa-brands fa-linkedin"></i></a>
                                <a href="<?=$header_details['header_spotify_link']?>" target="_blank" class="mt-1"><i class="fa-brands fa-tiktok"></i></a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="footer-qr">
                            <p class="footer-social-title">Permit Number: <?= $footer_details['permit_number'] ? $footer_details['permit_number'] : "102639" ?></p>
                           <div class="clearfix"></div>
                          <img loading="lazy" decoding="async" src="<?=BASE_URL?>images/dre-qr-code-26.jpeg" alt="Dre QR Code" style="width:100px;">
                        </div>
                    </div>
                </div>
                <!-- footer-widget  end-->
                <!-- footer-widget -->
                <div class="col-lg-3">
                    <div class="footer-widget">
                        <div class="footer-widget-title">Communities</div>
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
        <a href="<?= BASE_URL ?>community/<?= $row['comm_slug'] ?>">
            <?= $row['comm_heading'] ?>
        </a>
        <span class="f-number"><?= $row['total_proj'] ?></span>
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
                        <div class="footer-widget-title">Developers</div>
                        <div class="footer-widget-content">
                            <div class="footer-list footer-box  footer-numbers">
                                <ul>
                                         <?php
                                            $query=mysqli_query($conn,'select td.dev_slug,td.dev_heading,count(*) as total_proj from tbl_proj as tp INNER JOIN tbl_developer as
                                            td ON tp.prj_dev_id=td.dev_id GROUP BY td.dev_id order by total_proj desc limit 5');
                                            while($row=mysqli_fetch_array($query))
                                            {
                                             
                                            ?>
                                            <li>
                                                <a href="<?=BASE_URL?>developer/<?=$row['dev_slug']?>"> <?=$row['dev_heading']?></a>
                                                <span class="f-number"><?=$row['total_proj']?></span>
                                            </li>
                                            <?php }?>
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
                                    <li> <span>Headquarters:</span><a href="https://maps.app.goo.gl/zHNjkBSkwTs6xGsp8"
                                            target="_blank"><?=$footer_details['footer_address']?></a></li>
                                    <li> <span>Town Square Branch:</span><a href="https://maps.app.goo.gl/42FBpCmPsNUR667V6"
                                            target="_blank"><?=$footer_details['footer_address2']?></a></li>
                                    <li><span>Toll Free:</span><a href="tel:<?=$footer_details['footer_contact_number']?>"><?=$footer_details['footer_contact_number']?></a></li>
                                    <li><span>Hotline :</span><a href="tel:<?=$footer_details['footer_contact_number2']?>"><?=$footer_details['footer_contact_number2']?></a></li>
                                    <li><span>Mail :</span><a href="mailto:<?=$footer_details['footer_email_id']?>"
                                            target="_blank"><?=$footer_details['footer_email_id']?></a></li>
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
            <div class="f-text-box" style="width:50%; margin-bottom:10px;"><?=$footer_details['footer_copyright_text']?>
            <div class="f-text-box mt-3"><a href="<?=BASE_URL?>privacy-policy">Privacy Policy</a><span style="color:#fff" class="mx-3">|</span><a href="<?=BASE_URL?>terms-conditions">T&amp;C</a></div>
            </div>

        </div>
         
    </div>
    
</footer>
<!--<a href="javascript:void(0)" class="" style="-->
<!--  position: fixed;-->
<!--  bottom: 90px;-->
<!--  right: 30px;-->
<!--  z-index: 999;-->
<!--  width: 60px;-->
<!--  height: auto;-->
<!--  cursor: pointer;-->
<!--  transition: transform 0.2s ease;-->
<!--">-->
<!--  <img src="images/great-place-to-work.png" alt="<?= $header_details['header_logo_alt'] ?>" style="-->
<!--    width: 100%;-->
<!--    height: auto;-->
<!--    display: block;-->
<!--  ">-->
<!--</a>-->
<!-- Gallabox WhatsApp bubble: replaced by the WhatsApp icon in the floating dock -->
<!--<script>
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
    </script>-->
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
            <img src="<?=BASE_URL?>/images/wa-chat-logo.png" alt="Whatsapp Logo" width="80px">
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
                        <a href="<?=BASE_URL?>about-us.php"> Get to know Us</a>
                        <div class="svg-corner svg-corner_white" style="bottom:0;left:  -39px;"></div>
                        <div class="svg-corner svg-corner_white"
                            style="bottom:0;right:  -39px;transform: rotate(90deg)"></div>
                    </div>
                    <div class="loan-register_bg-dec"></div>
                </div>
                <div class="loan-register tabs-act fl-wrap">
                    <ul class="tabs-menu">
                        <li class="current"><a href="#loan-tab-1">Loan Application</a></li>
                    </ul>
                    <div id="loan-tabs-container">
                        <div class="tab">
                            <div id="loan-tab-1" class="tab-content first-tab">
                                <div class="custom-form">
		             <form action="" method="post" id="mortgage-frm">
                                        <div class="cs-intputwrap">
                                            <i class="fa-light fa-user"></i>
                                            <input type="text" name="name" placeholder="Enter Full Name">
                                        </div>
                                        <div class="cs-intputwrap">
                                            <i class="fa-light fa-envelope"></i>
                                           <input type="text" name="email" placeholder="Enter Your Email">
                                        </div>
                                        <div class="cs-intputwrap">
                                          <input type="text" name="mobile" placeholder="Enter Your Number" id="comm_dev_phone">
                                        </div>

                                        <div class="cs-intputwrap">
                                            <i class="fa-light fa-comment"></i>
                                           
                                            	<textarea class="ms-ht" name="message" rows="" cols="" placeholder='Message'></textarea>
                                        </div>
                                                                                <div class="cs-intputwrap">

                                        <img id="captcha_mortgcal" src="<?=BASE_URL?>captcha/captcha-mortgcal.php" alt="CAPTCHA" style="width:190px">
                                        </div>
                                                                                <div class="cs-intputwrap">

                        <input type="text" id="captcha_mortgcal_text" name="captcha_mortgcal_text" class="form-control" placeholder="Enter Captcha" >
                        </div>
                                                                <div class="cs-intputwrap">

                        <button type="button" class="commentssubmit" onclick="captcha_mortgcal_refresh()">Regenerate captcha</button>
                        </div>
                                                                <input type="hidden" name="mortg_phone_number_hidden" >

                                        <button type="submit" class="commentssubmit" name="mortg_submit">Submit Application</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="commentssubmit close-modal close-loan-form">Close</button>
                        
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
<div class="filterh1-container" >
    <div class="filterh1_box">
        <div class="filterh1-holder">
            <div class="filterh1-wrap">
                <div class="index-hd-filter">

                    <!-- Bedrooms -->
                    <div class="">
                        <div class="cs-intputwrap">
                            <i class="fa-light fa-bed"></i>
                            <select name="proj_bedroom[]" multiple data-placeholder="Bed" class="chosen-select on-radius no-search-select">
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
                            <select name="price_min" data-placeholder="Price Min" class="chosen-select on-radius no-search-select">
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
                            <select name="price_max" data-placeholder="Price Max" class="chosen-select on-radius no-search-select">
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
                            $selected_amenities = isset($_GET["amenities"]) ? (array)$_GET["amenities"] : [];
                            ?>
                            <ul class="no-list-style">
                                <?php while ($amenity = mysqli_fetch_assoc($amenities_query)): 
                                    $id = (int)$amenity["featfac_id"];
                                    $name = htmlspecialchars($amenity["featfac_name"]);
                                    $isChecked = in_array($id, $selected_amenities) ? "checked" : "";
                                ?>
                                    <li>
                                        <input type="checkbox" name="amenities[]" value="<?= $id ?>" class="amenity-checkbox" id="amenity-<?= $id ?>" <?= $isChecked ?>>
                                        <label for="amenity-<?= $id ?>"><?= $name ?></label>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Submit buttons -->
                    <div class="cr-modal-footer">
                        <a class="commentssubmit me-1" href="javascript:void(0)" onclick="redirectResidentialSearch();">Apply</a>
                        <div class="commentssubmit close-filterh1-form ms-1">Close</div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="body-overlay fs-wrapper filterh1-overlay close-filterh1-form"></div>
</div>

<!-- Redirect JavaScript -->
<!--<script>

    document.addEventListener("DOMContentLoaded", function () {

        const form = document.getElementById('residentialSearchForm1');

        if (!form) {
            console.log("Form not found");
            return;
        }

        form.addEventListener('submit', function (e) {

            e.preventDefault();

            // URLs
            const baseBuyURL = "<?= BASE_URL ?>" + "buy-property";
            const baseRentURL = "<?= BASE_URL ?>" + "rent-property";
            const baseOffplanURL = "<?= BASE_URL ?>" + "offplan-properties";

            // Values
            const type = form.querySelector('[name="type"]').value;

            //const propertyType = form.querySelector('[name="unitType"]').value;

            const location = form.querySelector('[name="location"]').value.trim();

            const property = form.querySelector('[name="property"]').value.trim();

            // Params
            const params = new URLSearchParams();

            if (location) {
                params.append('location', location);
            }

            if (property) {
                params.append('search_offplan', property);
            }

            params.append('submit', '');

            // Redirect URL
            let redirectURL = baseOffplanURL;

            if (type === 'Buy') {
                redirectURL = baseBuyURL;
            }
            else if (type === 'Rent') {
                redirectURL = baseRentURL;
            }

            // Final Redirect
            window.location.href = redirectURL + '?' + params.toString();

        });

    });
</script>-->
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
                            <select data-placeholder="Property Type" class="chosen-select on-radius no-search-select">
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
                            <select data-placeholder="Price Min" class="chosen-select on-radius no-search-select">
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
                            <select data-placeholder="Price Max" class="chosen-select on-radius no-search-select">
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
                                <input type="text" class="price-range-double" data-min="100" data-max="100000"
                                    name="price-range1" data-step="1" value="1">
                            </div>
                        </div>
                    </div>
                    <!-- listsearch-input-item -->

                    <div class="cr-modal-footer">
                      
                        <div class="commentssubmit close-filterh2-form ms-1">Close</div>
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
                <div id="singleMap" data-latitude="40.7" data-longitude="-73.1"></div>
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
$query=mysqli_query($conn,"select * from tbl_position where is_active=1");
$i=1;
while($row=mysqli_fetch_array($query))
{
?>
<div id="modal<?=$i?>" class="custom-modal-cr">
    <div class="modal-content-cr">
        <div class="cr-modal-title">
            <h4><?=$row['pos_heading']?></h4>
            <span class="close-cr">&times;</span>
        </div>
        <div class="cr-modal-body">
            <?=$row['pos_descp']?>
           
        </div>
        <div class="cr-modal-footer">
            <a class="commentssubmit" href="#resume-form" id="apply-btn">Apply Now</a>
        </div>
    </div>
</div>
<?php $i++;}?>

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
  // Only the developers page has this input
  if (!inp) { return; }
  
  var currentFocus;
  
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      closeAllLists();
      if (!val) { return false;}
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
          b.addEventListener("click", function(e) {
              inp.value = this.getElementsByTagName("input")[0].value;
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  
  inp.addEventListener("keydown", function(e) {
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


var developers = ["Emaar","Damac","Danube","Majid Al Futtaim", "Arista Properties", "Amaal Properties"];


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
<script src="<?= dre_asset('js/jquery.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/21.2.7/js/intlTelInput.min.js"></script>
<script src="<?= dre_asset('js/plugins.js') ?>"></script>
<script src="<?= dre_asset('js/scripts2.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js"></script>
<script src="<?= dre_asset('js/custom.js') ?>"></script>
<script src="<?= dre_asset('js/floating-dock.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
<script>
  Fancybox.bind("[data-fancybox='dre-gallery']", {
    Thumbs: {
      autoStart: true
    }
  });
</script>
<script>
let popIn = document.createElement('script')
popIn.setAttribute('src', 'https://widget01.popin.to/js/widget.js')
document.body.appendChild(popIn)
popIn.onload = () => {
  popInWidgetInit({
    token: "13538",
    mode: "hidden"
    });
}
</script>

<?php if (secret('ai_chat_enabled', false)): ?>
<!-- AI chat widget: talks to our own endpoint only, no key in the browser -->
<link rel="stylesheet" href="<?= dre_asset('css/ai-chat.css') ?>">
<script>
window.DRE_AI_CHAT = <?= json_encode([
    'endpoint' => BASE_URL . 'ai/chat.php',
    'baseUrl' => BASE_URL,
    'privacyUrl' => BASE_URL . 'privacy-policy',
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
</script>
<script src="<?= dre_asset('js/ai-chat.js') ?>" defer></script>
<?php endif; ?>

</body>


</html>