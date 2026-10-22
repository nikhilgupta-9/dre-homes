<?php
// 1. CONFIG
require_once('db/config.php');

// 2. SEO: Admin > Manage Tags ("Book Your Valuation")
$meta_tags = get_seo_tags(22);

// 3. DATA: one line per admin section; each call returns rows already prepared for the template
$valuation_content = get_book_valuation_content(); // Admin > Book Valuation > Page Content (tbl_book_valuation_content)
$valuation_topics = get_book_valuation_topics();  // Admin > Book Valuation > Valuation Items (tbl_book_valuation)

$page = [
  'active' => 'services',
  'css' => ['assets/css/pages/book-valuation.css'],
];

include('common/header.php');
?>

<!-- ===== VALUATION: HERO ===== -->
<header class="ph-hero mg-hero">
  <div class="ph-bg"
    style="background-image: url('<?= htmlspecialchars($valuation_content['banner_img'], ENT_QUOTES, 'UTF-8') ?>')">
  </div>
  <div class="wrap">
    <div class="crumbs" role="navigation" aria-label="Breadcrumb">
      <a href="<?= page_link('index') ?>">Home</a><span>/</span><span>Services</span><span>/</span><span
        aria-current="page">Book a Valuation</span>
    </div>
    <span class="eyebrow">Services · Valuation</span>
    <h1 class="serif"><?= $valuation_content['heading_html'] ?></h1>
    <p class="sub">
      <?= e($valuation_content['sub_heading']) ?>
    </p>
    <div class="story-cta">
      <a class="btn btn-gold" href="#contact">Book a valuation →</a><button class="btn btn-glass" type="button"
        onclick="window.Popin &amp;&amp; Popin('open')">
        Talk to a live agent
      </button>
    </div>
    <div class="dp-stats glass reveal">
      <div><small>Call-back</small><b>Within 24 working hours</b></div>
      <div><small>Property</small><b>Residential · Commercial</b></div>
      <div><small>Also</small><b>Management · Holiday homes</b></div>
      <div><small>Booking</small><b>3 quick steps</b></div>
    </div>
  </div>
</header>

<!-- ===== TOPICS ===== -->
<section style="padding-top: 70px">
  <div class="wrap">
    <div class="head-row reveal">
      <div>
        <span class="eyebrow"><?= e($valuation_topics['intro_head']) ?></span>
        <h2 class="serif">We’re there <em class="gold-text">to guide you.</em></h2>
      </div>
      <p class="bv-intro">
        <?= e($valuation_topics['intro_text']) ?>
      </p>
    </div>
    <div class="bv-grid reveal">
      <?php foreach ($valuation_topics['cards'] as $card) { ?>
        <article class="bv-c glass">
          <span class="mx-ic"><?= $card['svg_icon'] ?></span>
          <h3><?= e($card['heading']) ?></h3>
          <?php foreach ($card['paragraphs'] as $para) { ?>
            <p><?= e($para) ?></p>
          <?php } ?>
          <button class="mx-go" type="button"
            data-topic="<?= e($card['topic']) ?>"><?= e($card['button_text']) ?></button>
        </article>
      <?php } ?>
    </div>
  </div>
</section>

<!-- ===== HOW IT WORKS ===== -->
<section style="padding-top: 30px">
  <div class="wrap">
    <div class="head-row reveal">
      <div>
        <span class="eyebrow">How it works</span>
        <h2 class="serif">Three steps <em class="gold-text">to your valuation.</em></h2>
      </div>
    </div>
    <ol class="ms-grid bv-steps reveal">
      <li class="ms glass">
        <span class="ms-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
            stroke-linecap="round" stroke-linejoin="round">
            <rect x="5" y="3" width="14" height="18" rx="2"></rect>
            <path d="M9 8h6M9 12h6M9 16h4"></path>
          </svg></span><small>Step 1</small><b>Book online</b>
        <p>Tell us about the property and pick a date and time that works for you.</p>
      </li>
      <li class="ms glass">
        <span class="ms-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2"></path>
          </svg></span><small>Step 2</small><b>We call you back</b>
        <p>Our customer care team gets back to you within 24 working hours to confirm.</p>
      </li>
      <li class="ms glass">
        <span class="ms-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 12l2 2 4-4"></path>
            <circle cx="12" cy="12" r="9"></circle>
          </svg></span><small>Step 3</small><b>Meet your agent</b>
        <p>An agent guides you through buying, selling or leasing — at the time you chose.</p>
      </li>
    </ol>
  </div>
</section>

<!-- ===== BOOKING ===== -->
<section id="contact" style="padding-top: 30px">
  <div class="wrap">
    <div class="head-row reveal">
      <div>
        <span class="eyebrow">Book your valuation</span>
        <h2 class="serif">Complete the form — <em class="gold-text">we’ll call you back.</em></h2>
      </div>
    </div>
    <div class="bv-wrap reveal">
      <form class="bv-form glass" id="bvForm" novalidate="">
        <!-- Honeypot for spam bot detection -->
        <input type="text" name="website" style="display:none !important;" tabindex="-1" autocomplete="off"
          aria-hidden="true" />
        <ol class="bv-prog" aria-label="Booking steps">
          <li class="on" data-s="0"><span>1</span>Property</li>
          <li data-s="1"><span>2</span>Appointment</li>
          <li data-s="2"><span>3</span>Your details</li>
        </ol>
        <fieldset class="bv-step" data-step="0">
          <legend class="sr">Property</legend>
          <div class="bv-f">
            <span class="bv-l">I’m enquiring about <i>*</i></span>
            <div class="bv-chips" data-req="topic">
              <label class="bv-chip"><input type="radio" name="topic" value="Residential Sale" /><span>Residential
                  Sale</span></label><label class="bv-chip"><input type="radio" name="topic"
                  value="Commercial Sale" /><span>Commercial Sale</span></label><label class="bv-chip"><input
                  type="radio" name="topic" value="Residential Lease" /><span>Residential Lease</span></label><label
                class="bv-chip"><input type="radio" name="topic" value="Commercial Lease" /><span>Commercial
                  Lease</span></label><label class="bv-chip"><input type="radio" name="topic"
                  value="Property Management" /><span>Property Management</span></label><label class="bv-chip"><input
                  type="radio" name="topic" value="Holiday Homes" /><span>Holiday Homes</span></label>
            </div>
          </div>
          <div class="bv-f">
            <span class="bv-l">Property type <i>*</i></span>
            <div class="bv-chips" data-req="ptype">
              <label class="bv-chip"><input type="radio" name="ptype"
                  value="Apartment" /><span>Apartment</span></label><label class="bv-chip"><input type="radio"
                  name="ptype" value="Penthouse" /><span>Penthouse</span></label><label class="bv-chip"><input
                  type="radio" name="ptype" value="Villa" /><span>Villa</span></label><label class="bv-chip"><input
                  type="radio" name="ptype" value="Townhouse" /><span>Townhouse</span></label><label
                class="bv-chip"><input type="radio" name="ptype" value="Mansion" /><span>Mansion</span></label><label
                class="bv-chip"><input type="radio" name="ptype" value="Land" /><span>Land</span></label><label
                class="bv-chip"><input type="radio" name="ptype" value="Shop" /><span>Shop</span></label><label
                class="bv-chip"><input type="radio" name="ptype" value="Office Space" /><span>Office
                  Space</span></label>
            </div>
          </div>
          <div class="bv-f" id="bvBedsF">
            <span class="bv-l">Bedrooms</span>
            <div class="bv-chips sm">
              <label class="bv-chip"><input type="radio" name="beds" value="Studio" /><span>Studio</span></label><label
                class="bv-chip"><input type="radio" name="beds" value="1" /><span>1</span></label><label
                class="bv-chip"><input type="radio" name="beds" value="2" /><span>2</span></label><label
                class="bv-chip"><input type="radio" name="beds" value="3" /><span>3</span></label><label
                class="bv-chip"><input type="radio" name="beds" value="4" /><span>4</span></label><label
                class="bv-chip"><input type="radio" name="beds" value="5" /><span>5</span></label><label
                class="bv-chip"><input type="radio" name="beds" value="6" /><span>6</span></label><label
                class="bv-chip"><input type="radio" name="beds" value="7" /><span>7</span></label>
            </div>
          </div>
          <div class="bv-row">
            <label class="bv-f bv-in fsearch-l"><span class="bv-l">Preferred location <i>*</i></span><input id="bvLoc"
                name="location" placeholder="e.g. Dubai Marina, Town Square…" autocomplete="off" required=""
                data-suggest="bvLocList"
                data-popular="Dubai Marina|Downtown Dubai|Palm Jumeirah|Dubai Hills Estate|Business Bay|Town Square"
                data-pop-title="Popular locations" data-min-w="320" data-per="8" data-max="8" /><datalist
                id="bvLocList">
                <option value="Abu Dhabi"></option>
                <option value="Abu Hail"></option>
                <option value="Al Badaa"></option>
                <option value="Al Barari"></option>
                <option value="Al Barsha"></option>
                <option value="Al Bateen"></option>
                <option value="Al Furjan"></option>
                <option value="Al Ghadeer"></option>
                <option value="Al Habtoor City"></option>
                <option value="Al Hamra Village"></option>
                <option value="Al Jaddaf"></option>
                <option value="Al Jurf Gardens"></option>
                <option value="Al Khawaneej"></option>
                <option value="Al Mamsha"></option>
                <option value="Al Mamzar"></option>
                <option value="Al Maryah Island"></option>
                <option value="Al Qusais"></option>
                <option value="Al Raha Beach"></option>
                <option value="Al Rashidiya"></option>
                <option value="Al Reem Island"></option>
                <option value="Al Satwa"></option>
                <option value="Al Shamkha"></option>
                <option value="Al Sufouh"></option>
                <option value="Al Warsan"></option>
                <option value="Al Zahia"></option>
                <option value="Aljada"></option>
                <option value="Arabian Ranches"></option>
                <option value="Arabian Ranches 2"></option>
                <option value="Arabian Ranches 3"></option>
                <option value="Arjan"></option>
                <option value="Aykon City"></option>
                <option value="Barsha Heights"></option>
                <option value="Bloom Gardens"></option>
                <option value="Bloom Living"></option>
                <option value="Bluewaters Island"></option>
                <option value="Business Bay"></option>
                <option value="City Walk"></option>
                <option value="Creek Beach"></option>
                <option value="DIFC"></option>
                <option value="Damac Hills"></option>
                <option value="Damac Hills 2"></option>
                <option value="Damac Lagoons"></option>
                <option value="Discovery Gardens"></option>
                <option value="District One"></option>
                <option value="Downtown Dubai"></option>
                <option value="Dragon City"></option>
                <option value="Dubai"></option>
                <option value="Dubai Creek Harbour"></option>
                <option value="Dubai Festival City"></option>
                <option value="Dubai Golf City"></option>
                <option value="Dubai Harbour"></option>
                <option value="Dubai Healthcare City"></option>
                <option value="Dubai Hills Estate"></option>
                <option value="Dubai International City"></option>
                <option value="Dubai Investments Park"></option>
                <option value="Dubai Islands"></option>
                <option value="Dubai Marina"></option>
                <option value="Dubai Maritime City"></option>
                <option value="Dubai Motor City"></option>
                <option value="Dubai Production City"></option>
                <option value="Dubai Science Park"></option>
                <option value="Dubai Silicon Oasis"></option>
                <option value="Dubai South"></option>
                <option value="Dubai Sports City"></option>
                <option value="Dubai Studio City"></option>
                <option value="Dubai Water Canal"></option>
                <option value="Dubailand"></option>
                <option value="Emaar Beachfront"></option>
                <option value="Emaar South"></option>
                <option value="Expo City Dubai"></option>
                <option value="Fahid Island"></option>
                <option value="Fujairah"></option>
                <option value="Istanbul"></option>
                <option value="Jebel Ali Village"></option>
                <option value="Jumeirah 1"></option>
                <option value="Jumeirah Beach Residence"></option>
                <option value="Jumeirah Golf Estates"></option>
                <option value="Jumeirah Islands"></option>
                <option value="Jumeirah Lake Towers"></option>
                <option value="Jumeirah Village Circle"></option>
                <option value="Jumeirah Village Triangle"></option>
                <option value="Jumeriah Bay Island"></option>
                <option value="Karachi"></option>
                <option value="Lebanon"></option>
                <option value="MBR City"></option>
                <option value="Madinat Jumeirah Living"></option>
                <option value="Maryam Island"></option>
                <option value="Meydan"></option>
                <option value="Midtown Dubai"></option>
                <option value="Millennium Estates"></option>
                <option value="Mina Rashid Port"></option>
                <option value="Montenegro"></option>
                <option value="Mudon"></option>
                <option value="Muscat"></option>
                <option value="Nad Al Sheba"></option>
                <option value="Oman"></option>
                <option value="One Za'abeel"></option>
                <option value="Palm Jebel Ali"></option>
                <option value="Palm Jumeirah"></option>
                <option value="Pearl Jumeirah"></option>
                <option value="Port De La Mer"></option>
                <option value="Ras Al Khaimah"></option>
                <option value="Rashid Yachts &amp; Marina"></option>
                <option value="Reem"></option>
                <option value="Reem Hills"></option>
                <option value="Saadiyat Grove"></option>
                <option value="Saadiyat Island"></option>
                <option value="Safa Park"></option>
                <option value="Serena"></option>
                <option value="Sharjah"></option>
                <option value="Sobha Hartland"></option>
                <option value="Sobha Hartland 2"></option>
                <option value="Sobha Siniya Island"></option>
                <option value="The Heights Country Club &amp; Wellness"></option>
                <option value="The Oasis"></option>
                <option value="The Opera District"></option>
                <option value="The Valley"></option>
                <option value="The World Islands"></option>
                <option value="Tilal Al Ghaf"></option>
                <option value="Town Square"></option>
                <option value="Umm Al Quwain"></option>
                <option value="Villanova"></option>
                <option value="Warsan First"></option>
                <option value="Wasl Gate, Dubai"></option>
                <option value="Yas Acres"></option>
                <option value="Yas Island"></option>
              </datalist></label>
            <label class="bv-f bv-in"><span class="bv-l">Approx. area</span><span class="bv-unit"><input id="bvArea"
                  name="area_sqft" inputmode="numeric" placeholder="e.g. 1,200" /><em>sq ft</em></span></label>
          </div>
        </fieldset>
        <fieldset class="bv-step" data-step="1" hidden="">
          <legend class="sr">Appointment</legend>
          <div class="bv-f">
            <span class="bv-l">Pick a date <i>*</i></span>
            <div class="bv-dates" id="bvDates" data-req="date"></div>
          </div>
          <div class="bv-f">
            <span class="bv-l">Pick a time <i>*</i></span>
            <div class="bv-chips bv-times" data-req="time">
              <label class="bv-chip"><input type="radio" name="time" value="9 AM" /><span>9 AM</span></label><label
                class="bv-chip"><input type="radio" name="time" value="10 AM" /><span>10 AM</span></label><label
                class="bv-chip"><input type="radio" name="time" value="11 AM" /><span>11 AM</span></label><label
                class="bv-chip"><input type="radio" name="time" value="12 PM" /><span>12 PM</span></label><label
                class="bv-chip"><input type="radio" name="time" value="1 PM" /><span>1 PM</span></label><label
                class="bv-chip"><input type="radio" name="time" value="2 PM" /><span>2 PM</span></label><label
                class="bv-chip"><input type="radio" name="time" value="3 PM" /><span>3 PM</span></label><label
                class="bv-chip"><input type="radio" name="time" value="4 PM" /><span>4 PM</span></label><label
                class="bv-chip"><input type="radio" name="time" value="5 PM" /><span>5 PM</span></label><label
                class="bv-chip"><input type="radio" name="time" value="6 PM" /><span>6 PM</span></label>
            </div>
          </div>
        </fieldset>
        <fieldset class="bv-step" data-step="2" hidden="">
          <legend class="sr">Your details</legend>
          <div class="bv-row">
            <label class="bv-f bv-in"><span class="bv-l">Your name <i>*</i></span><input name="name" id="bvName"
                autocomplete="name" placeholder="Full name" required="" /></label>
            <label class="bv-f bv-in"><span class="bv-l">Email <i>*</i></span><input name="email" id="bvEmail"
                type="email" autocomplete="email" placeholder="you@example.com" required="" /></label>
          </div>
          <label class="bv-f bv-in"><span class="bv-l">Phone <i>*</i></span><input id="bvPhone" name="phone" type="tel"
              autocomplete="tel" placeholder="Phone / WhatsApp" required="" /><input type="hidden" name="phone_full"
              id="bvPhoneFull" /></label>
        </fieldset>
        <p class="bv-err" id="bvErr" role="alert"></p>
        <div class="bv-nav">
          <button class="btn btn-glass" type="button" id="bvBack" hidden="">← Back</button><button class="btn btn-gold"
            type="button" id="bvNext">Next: Appointment →</button><button class="btn btn-gold" type="submit"
            id="bvSubmit" hidden="">Book my valuation</button>
        </div>
      </form>
      <aside class="bv-sum glass" aria-live="polite">
        <span class="eyebrow">Your booking</span>
        <ul id="bvSum"></ul>
        <div class="bv-sum-f">
          <p>
            Prefer to talk? Call <a href="tel:+971527543243">+971 52 754 3243</a> or email
            <a href="mailto:sales@drehomes.com">sales@drehomes.com</a>.
          </p>
          <button class="btn btn-glass" type="button" onclick="window.Popin &amp;&amp; Popin('open')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
              stroke-linejoin="round">
              <rect x="3" y="6" width="13" height="12" rx="2"></rect>
              <path d="M16 10l5-3v10l-5-3"></path>
            </svg>
            Live agent
          </button>
        </div>
      </aside>
    </div>
  </div>
</section>

<!-- ===== OTHER SERVICES ===== -->
<section style="padding-top: 30px">
  <div class="wrap">
    <div class="head-row reveal">
      <div>
        <span class="eyebrow">More from DRE</span>
        <h2 class="serif">Other <em class="gold-text">services</em></h2>
      </div>
    </div>
    <div class="os-grid reveal">
      <a class="os glass" href="<?= page_link('property-investment-consultancy') ?>"><span class="os-ic"><svg
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
            stroke-linejoin="round">
            <path d="M3 3v18h18"></path>
            <path d="M7 15l4-4 3 3 6-6"></path>
          </svg></span><b>Property &amp; Investment Consultancy</b><span class="os-go">→</span></a><a class="os glass"
        href="<?= page_link('mortgage-advisory') ?>"><span class="os-ic"><svg viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 11l9-7 9 7"></path>
            <path d="M5 10v10h14V10"></path>
            <path d="M12 17v-5M10 13.5c0-1 .9-1.5 2-1.5s2 .5 2 1.5-1 1.3-2 1.5-2 .6-2 1.5.9 1.5 2 1.5 2-.5 2-1.5">
            </path>
          </svg></span><b>Mortgage Advisory</b><span class="os-go">→</span></a><a class="os glass"
        href="<?= page_link('property-management') ?>"><span class="os-ic"><svg viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 21V8l8-5 8 5v13"></path>
            <path d="M9 21v-6h6v6M9 11h.01M15 11h.01"></path>
          </svg></span><b>Property Management</b><span class="os-go">→</span></a><a class="os glass"
        href="<?= page_link('relocation-service') ?>"><span class="os-ic"><svg viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"></path>
          </svg></span><b>Relocation Service</b><span class="os-go">→</span></a><a class="os glass"
        href="<?= page_link('bank-account-opening') ?>"><span class="os-ic"><svg viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 10l9-6 9 6"></path>
            <path d="M5 10v8M9 10v8M15 10v8M19 10v8M3 21h18"></path>
          </svg></span><b>Bank Account Opening</b><span class="os-go">→</span></a><a class="os glass"
        href="<?= page_link('business-licenses') ?>"><span class="os-ic"><svg viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 3H6v18h12V7z"></path>
            <path d="M14 3v4h4M9 13h6M9 17h6"></path>
          </svg></span><b>Business Licenses</b><span class="os-go">→</span></a>
    </div>
  </div>
</section>

<?php include('common/footer.php'); ?>