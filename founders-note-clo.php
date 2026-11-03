<?php
include('db/config.php');
// SEO: Admin > Manage Tags ("Message From Co-Founder & CLO")
$meta_tags = select_row('tbl_tags', 'id=25 and is_active=1');

// Content: Admin > Message From Founder (CLO) (tbl_msg_from_foundr); the other founder for the tabs and "Also read"
$note = [
  'row' => select_row('tbl_msg_from_foundr', 'is_active=1') ?: [],
  'prefix' => 'foundr',
  'role' => 'Chief Leadership Officer',
  'url' => BASE_URL . 'message-from-co-founder-and-clo',
  // pull quote shown inside the letter
  'quote' => 'DRE set out to be different by raising the benchmark for client service and expertise.',
  // company news whose heading contains one of these words
  'news' => ['Qurat'],
];
$other = [
  'row' => select_row('tbl_msg_from_ceo', 'is_active=1') ?: [],
  'prefix' => 'ceo',
  'role' => 'Chief Executive Officer',
  'url' => BASE_URL . 'message-from-co-founder-and-ceo',
];
include('common/header.php');
include('common/founders-note.php');
?>

    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Private Consultation</span>
            <h2 class="serif">We look forward to<br /><em class="gold-text">hearing from you.</em></h2>
            <p class="lead">Toll free <?= e($footer_info['footer_contact_number'] ?? '800 37373') ?> · Hotline <?= e($footer_info['footer_contact_number2'] ?? '+971 52 754 3243') ?></p>
          </div>
          <form class="form" id="ctaForm" novalidate="">
            <input name="name" placeholder="Full name" autocomplete="name" required="" />
            <input
              id="ctaPhone"
              name="phone"
              type="tel"
              placeholder="Phone / WhatsApp"
              autocomplete="tel"
              required=""
            />
            <input type="hidden" name="phone_full" id="ctaPhoneFull" />
            <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
            <input name="message" placeholder="What are you looking for?" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>


<?php include('common/footer.php'); ?>
