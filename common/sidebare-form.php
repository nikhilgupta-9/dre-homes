<form method="post" name="contact-property-form" id="offplan-frm">
    <!-- here we add extra fields  -->
    <input type="hidden" name="prop_name" value="<?= $proj_detail['proj_name'] ?>">
    <input type="hidden" name="prop_location" value="<?= $proj_detail['proj_location'] ?>">
    <input type="hidden" name="prop_bedroom" value="<?= $proj_detail['proj_inventory_type'] ?>">
    <input type="hidden" name="developer" value="<?= $dev_detail['dev_heading'] ?>">
    <input type="hidden" name="channel" value="Website">
    <input type="hidden" name="leads_nature" value="Fresh Lead">

    <div class="cs-intputwrap">
        <label for="" class="sr-only">Your Name</label>
        <i class="fa-light fa-user"></i>
        <input name="name" type="text" placeholder="Your Name *" value="">
    </div>
    <div class="cs-intputwrap">
        <label for="" class="sr-only">Your Phone</label>
        <input class="phone-input" name="phone" id="book_val_phone" type="text" placeholder="Your Phone *" value="">
    </div>
    <div class="cs-intputwrap">
        <label for="" class="sr-only">Your Email</label>
        <i class="fa-light fa-envelope"></i>
        <input type="text" name="email" id="email" placeholder="Email Address *" value="">
    </div>
    <div class="side-f-msg">
        <div class="cs-intputwrap">
            <label for="" class="sr-only">Your Message</label>
            <i class="fa-light fa-comment"></i>
            <textarea placeholder="Message" name="message" class="" col="60" row="60"></textarea>
        </div>
    </div>

    <input type="hidden" name="sideform_country_code" id="sideform_country_code">
    <input type="hidden" name="download_type" value="Sidebar Form">
    <input type="hidden" name="page_url" value="<?= current_url() ?>">

    <button class="commentssubmit commentssubmit_fw" name="offplan_submit">Submit</button>
</form>
<!--<div class="log-separator py-3"><span>or</span></div>-->