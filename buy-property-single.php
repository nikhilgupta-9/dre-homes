<?php
// 1. CONFIG
include('db/config.php');

// 2. DATA: Buy property project detail (/buy-property/<slug>, see .htaccess) — design: listing-shams-townhouses.html
// Admin > Manage Buy Projects (tbl_proj, type 7): project, community, gallery, qr, amenities, similar homes
$slug = trim(str_replace('/', '', (string) ($_GET['detail'] ?? '')));
if ($slug === '') {
    $slug = 'shams-townhouses-town-square-dubai';
}

$property = get_buy_project($slug);
if (!$property) {
    // Check if it's an Off-plan property (tbl_proj type 1)
    if ($slug !== '' && get_offplan_project($slug)) {
        header('Location: ' . BASE_URL . 'property/' . rawurlencode($slug), true, 301);
        exit;
    }
    // Check if it's a Rent property (tbl_proj type 8)
    if ($slug !== '' && get_rent_project($slug)) {
        header('Location: ' . BASE_URL . 'rent-property/' . rawurlencode($slug), true, 301);
        exit;
    }
    // Check if it's an International property (tbl_proj type 3)
    if ($slug !== '' && get_international_project($slug)) {
        header('Location: ' . BASE_URL . 'international-properties/' . rawurlencode($slug), true, 301);
        exit;
    }
    // Check if it's a Ready to move in property (tbl_proj type 2)
    if ($slug !== '' && get_ready_project($slug)) {
        header('Location: ' . BASE_URL . 'ready-to-move-in/' . rawurlencode($slug), true, 301);
        exit;
    }
    // Unknown or unpublished project: back to buy properties listing
    header('Location: ' . BASE_URL . 'buy-property', true, 302);
    exit;
}

// 3. SEO: the project's own meta fields (Admin > Manage Buy Projects > SEO); header falls back to photos[0] as og:image
$meta_tags = $property['meta'];
$page = [
    'active' => 'properties',
    'title' => $property['name'] . ' | DRE Homes',
    'description' => $property['clean_intro'],
    'css' => ['assets/css/pages/buy-property-single.css'],
];

include('common/header.php');
?>
    <!-- ===== PROPERTY DETAIL ===== -->
    <header class="pd-hero">
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>">Home</a><span>/</span><a href="<?= BASE_URL ?>buy-property">Buy Property</a><span>/</span
          ><span aria-current="page"><?= e($property['name']) ?></span>
        </div>
        <div class="pd-gal">
          <button class="pg-main" data-g="0" aria-label="Open gallery">
            <img src="<?= e($property['photos'][0]['src']) ?>" alt="<?= e($property['photos'][0]['title']) ?>" />
          </button>
<?php if (!empty($property['side_photos'])) { ?>
          <div class="pg-side">
<?php foreach ($property['side_photos'] as $photo) { ?>
            <button data-g="<?= $photo['index'] ?>" aria-label="Open photo <?= $photo['index'] + 1 ?>">
              <img src="<?= e($photo['src']) ?>" alt="<?= e($photo['title']) ?>" />
<?php if (!empty($photo['show_all'])) { ?>
              <span class="pg-more glass on-img">⤢ View all <?= count($property['photos']) ?> photos</span>
<?php } ?>
            </button>
<?php } ?>
          </div>
<?php } ?>
          <div class="pd-title glass on-img">
            <div class="pt-tags">
              <span>For sale</span>
              <span><?= e($property['type']) ?></span>
              <span><?= e($property['status']) ?></span>
            </div>
            <h1 class="serif"><?= $property['title_html'] ?></h1>
<?php if (!empty($property['features'])) { ?>
            <p class="pt-sub"><?= e(implode(' · ', array_slice($property['features'], 0, 3))) ?></p>
<?php } ?>
            <p>
              <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
                <path d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"></path>
              </svg>
              <?= e($property['place']) ?>
            </p>
          </div>
          <ul class="pd-photos" id="pdPhotos" hidden="">
<?php foreach ($property['photos'] as $photo) { ?>
            <li data-src="<?= e($photo['src']) ?>" data-title="<?= e($photo['title']) ?>"></li>
<?php } ?>
          </ul>
        </div>
      </div>
    </header>

    <section class="pd-body">
      <div class="wrap pd-grid">
        <div class="pd-main">
          <div class="pd-tabs glass" id="pdTabs">
            <a href="#overview" class="on">Overview</a><a href="#details">Details</a><a href="#amenities">Amenities</a
            ><a href="#location">Location</a><a href="#mortgage">Mortgage</a><a href="#enquire">Book a viewing</a>
          </div>

          <div class="facts facts5 reveal">
            <div class="glass"><small>Price</small><b><?= $property['price'] > 0 ? e($property['price_label']) : 'On request' ?></b></div>
            <div class="glass"><small>Bedrooms</small><b><?= e($property['beds_label']) ?></b></div>
            <div class="glass"><small>Bathrooms</small><b><?= e($property['baths_label']) ?></b></div>
            <div class="glass"><small>Built-up area</small><b><?= $property['area'] > 0 ? e($property['area_label']) : 'On request' ?></b></div>
            <div class="glass"><small>Price / sq ft</small><b><?= $property['ppf'] > 0 ? e($property['ppf_label']) : '—' ?></b></div>
          </div>

          <article id="overview" class="pd-sec reveal">
            <span class="eyebrow">About this property · REF <?= e($property['reference']) ?></span>
            <h2 class="serif">A single-row family home, <em class="gold-text">ready today.</em></h2>
            <p class="lead">
              <?= e($property['clean_intro']) ?>
            </p>
<?php if (!empty($property['features'])) { ?>
            <div class="feat-chips">
<?php foreach ($property['features'] as $feat) { ?>
              <span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path>
                </svg>
                <?= e($feat) ?>
              </span>
<?php } ?>
            </div>
<?php } ?>
          </article>

          <article id="details" class="pd-sec reveal">
            <span class="eyebrow">Property Details</span>
            <h2 class="serif">At a <em class="gold-text">glance.</em></h2>
            <dl class="kv">
              <div>
                <dt>Built-up area</dt>
                <dd><?= $property['area'] > 0 ? e($property['area_label']) : 'On request' ?></dd>
              </div>
<?php if ($property['plot_area'] > 0) { ?>
              <div>
                <dt>Plot size</dt>
                <dd><?= e($property['plot_area_label']) ?></dd>
              </div>
<?php } ?>
              <div>
                <dt>Bedrooms</dt>
                <dd><?= e($property['beds_label']) ?></dd>
              </div>
<?php if ($property['baths_num'] > 0) { ?>
              <div>
                <dt>Bathrooms</dt>
                <dd><?= e($property['baths_label']) ?></dd>
              </div>
<?php } ?>
              <div>
                <dt>Property Type</dt>
                <dd><?= e($property['type']) ?></dd>
              </div>
              <div>
                <dt>Furnishing</dt>
                <dd><?= e($property['furnishing']) ?></dd>
              </div>
              <div>
                <dt>View</dt>
                <dd><?= e($property['view']) ?></dd>
              </div>
              <div>
                <dt>Community</dt>
                <dd><?= e($property['community']) ?></dd>
              </div>
              <div>
                <dt>Status</dt>
                <dd><?= e($property['status']) ?></dd>
              </div>
            </dl>
          </article>

          <article id="amenities" class="pd-sec reveal">
            <span class="eyebrow">Amenities &amp; Facilities</span>
            <h2 class="serif">Everything on <em class="gold-text">your doorstep.</em></h2>
            <ul class="am-list">
<?php foreach ($property['amenities'] as $am) { ?>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path>
                </svg>
                <?= e($am) ?>
              </li>
<?php } ?>
            </ul>
          </article>

          <article id="location" class="pd-sec reveal">
            <span class="eyebrow">Property Location</span>
            <h2 class="serif"><?= e($property['location_title']) ?></h2>
            <div class="loc-grid">
<?php if ($property['map_url'] !== '') { ?>
              <div class="map-wrap glass" id="mapWrap">
                <button class="map-load" id="mapLoad" data-map="<?= e($property['map_url']) ?>">
                  <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28">
                    <path d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"></path>
                  </svg>
                  <b>Load interactive map</b><small>Google Maps</small>
                </button>
              </div>
<?php } ?>
              <ol class="drive glass">
<?php foreach ($property['drive_times'] as $drive) { ?>
                <li>
                  <b><?= e($drive['time']) ?><small><?= e($drive['unit']) ?></small></b><span><?= e($drive['place']) ?></span>
                </li>
<?php } ?>
                <li class="road"><b>⇄</b><span>Easy access to major roads in Dubai</span></li>
              </ol>
            </div>
<?php if ($property['about_community_text'] !== '') { ?>
            <div class="about2">
              <div>
                <h3>About <?= e($property['community']) ?></h3>
                <p><?= e($property['about_community_text']) ?></p>
              </div>
            </div>
<?php } ?>
          </article>

          <article id="mortgage" class="pd-sec reveal">
            <span class="eyebrow">Instant mortgage estimate</span>
            <h2 class="serif">What could you <em class="gold-text">pay monthly?</em></h2>
            <div class="mort glass">
              <div class="mg-in">
                <label
                  ><span>Purchase price</span>
                  <div class="mv"><span>AED</span><input id="mPrice" inputmode="numeric" value="<?= number_format($property['price'] ?: 2000000) ?>" /></div
                ></label>
                <label
                  ><span>Down payment <b id="mDpV">20%</b></span
                  ><input type="range" id="mDp" min="20" max="80" step="5" value="20"
                /></label>
                <label
                  ><span>Interest rate <b id="mRtV">4.5%</b></span
                  ><input type="range" id="mRt" min="2" max="9" step="0.1" value="4.5"
                /></label>
                <label
                  ><span>Loan duration <b id="mYrV">25 years</b></span
                  ><input type="range" id="mYr" min="5" max="25" step="1" value="25"
                /></label>
              </div>
              <div class="m-out">
                <small>Estimated monthly payment</small>
                <div class="m-big gold-text" id="mOut">—</div>
                <ul>
                  <li><span>Loan amount</span><b id="mLoan">—</b></li>
                  <li><span>Down payment</span><b id="mDown">—</b></li>
                  <li><span>Total interest</span><b id="mInt">—</b></li>
                </ul>
                <p>Indicative only — excludes DLD fees and bank charges.</p>
                <a class="btn btn-gold" href="<?= BASE_URL ?>mortgage-advisory">Apply for a loan →</a>
              </div>
            </div>
          </article>

          <span id="contact" aria-hidden="true"></span>
          <article id="enquire" class="pd-sec reveal">
            <div class="enq glass acc-edge">
              <div>
                <span class="eyebrow">Book a viewing</span>
                <h2 class="serif">See it <em class="gold-text">in person.</em></h2>
                <p class="lead">Fill in the form and a DRE advisor will arrange a viewing at a time that suits you.</p>
              </div>
              <form class="form" id="ctaForm" novalidate="">
                <input name="name" placeholder="Your name" autocomplete="name" required="" />
                <input id="ctaPhone" name="phone" type="tel" placeholder="Your phone" autocomplete="tel" required="" />
                <input type="hidden" name="phone_full" id="ctaPhoneFull" />
                <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
                <input name="email" type="email" placeholder="Your email" autocomplete="email" />
                <input
                  name="message"
                  placeholder="Your message"
                  value="I'd like to view <?= e($property['name']) ?> (REF <?= e($property['reference']) ?>)."
                />
                <input
                  type="hidden"
                  name="page_url"
                  value="<?= e($property['url']) ?>"
                />
                <button class="btn btn-gold" style="justify-content: center">Request a viewing</button>
              </form>
            </div>
          </article>

          <a class="repair glass reveal" href="<?= BASE_URL ?>property-management">
            <span class="rp-ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M14.7 6.3a4 4 0 00-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 005.4-5.4l-2.5 2.5-2.4-.6-.6-2.4z"></path>
              </svg>
            </span>
            <div>
              <b>Home repairs made easy</b><span>We take care of everything — quick, reliable and hassle-free.</span>
            </div>
            <em>Explore →</em>
          </a>
        </div>

        <aside class="pd-side">
          <div class="side-card glass acc-edge">
            <small>Price</small>
            <div class="sc-price gold-text"><?= $property['price'] > 0 ? e($property['price_label']) : 'Price on request' ?></div>
            <p class="sc-ppf"><?= $property['ppf'] > 0 ? e($property['ppf_label']) . ' / sq ft · ' : '' ?>REF <?= e($property['reference']) ?></p>
            <ul class="sc-facts">
              <li><span>Type</span><b><?= e($property['type']) ?></b></li>
              <li><span>Community</span><b><?= e($property['community']) ?></b></li>
              <li><span>Beds / baths</span><b><?= e($property['beds_label']) ?> / <?= e($property['baths_label']) ?></b></li>
              <li><span>Built-up area</span><b><?= $property['area'] > 0 ? e($property['area_label']) : 'On request' ?></b></li>
              <li><span>Status</span><b><?= e($property['status']) ?></b></li>
            </ul>
            <div class="sc-ct">
              <a
                class="btn btn-glass sc-wa"
                href="<?= e($property['whatsapp']) ?>"
                target="_blank"
                rel="noopener"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"></path>
                </svg>WhatsApp</a
              >
              <button class="btn btn-glass" id="pdVideo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16">
                  <rect x="2.5" y="6" width="13" height="12" rx="3"></rect>
                  <path d="M15.5 10.5l6-3.5v10l-6-3.5z"></path></svg
                >Video tour
              </button>
            </div>
            <a class="btn btn-gold sc-book" href="#enquire">Book a viewing →</a>
            <a class="sc-call" href="tel:<?= preg_replace('/[^0-9+]/', '', $footer_info['footer_contact_number'] ?? '80037373') ?>">Or call <b><?= e($footer_info['footer_contact_number'] ?? '800 37373') ?></b></a>
          </div>
<?php if (!empty($property['qr_img'])) { ?>
          <div class="verify glass">
            <a
              href="<?= !empty($property['qr_link']) ? e($property['qr_link']) : '#' ?>"
              target="_blank"
              rel="noopener"
              class="vq"
              aria-label="Verify listing permit"
              ><img
                src="<?= e($property['qr_img']) ?>"
                alt="Listing permit QR code"
            /></a>
            <div>
              <b>Verified listing</b>
              <dl>
<?php if (!empty($property['zone'])) { ?>
                <div>
                  <dt>Zone</dt>
                  <dd><?= e($property['zone']) ?></dd>
                </div>
<?php } ?>
                <div>
                  <dt>RERA</dt>
                  <dd><?= e($property['rera']) ?></dd>
                </div>
<?php if (!empty($property['brn'])) { ?>
                <div>
                  <dt>BRN</dt>
                  <dd><?= e($property['brn']) ?></dd>
                </div>
<?php } ?>
              </dl>
<?php if (!empty($property['qr_link'])) { ?>
              <a
                href="<?= e($property['qr_link']) ?>"
                target="_blank"
                rel="noopener"
                >Scan or tap to verify ↗</a
              >
<?php } ?>
            </div>
          </div>
<?php } ?>
        </aside>
      </div>
    </section>

<?php if (!empty($property['similar'])) { ?>
    <section id="similar" style="padding-top: 40px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">More like this</span>
            <h2 class="serif">Similar homes <em class="gold-text">for sale</em></h2>
          </div>
          <div class="car-nav">
            <button id="simPrev" aria-label="Previous">←</button><button id="simNext" aria-label="Next">→</button>
          </div>
        </div>
        <div class="sim-track" id="simTrack">
<?php foreach ($property['similar'] as $sim) { ?>
          <article class="oc sim">
            <a class="oc-img" href="<?= e($sim['url']) ?>">
              <img
                src="<?= e($sim['img']) ?>"
                alt="<?= e($sim['name']) ?>"
                loading="lazy"
                onerror="this.onerror=null; this.src='<?= BASE_URL ?>admin_nsrdwsc/assets/media/logos/logo-light.png';"
              />
              <span class="badge"><?= e($sim['type']) ?></span>
              <span class="oc-loc"><?= e($sim['community']) ?></span>
            </a>
            <div class="oc-b">
              <h3>
                <a href="<?= e($sim['url']) ?>"><?= e($sim['name']) ?></a>
              </h3>
              <div class="specs">
                <span><?= e($sim['beds_label']) ?></span>
                <span><?= e($sim['baths_label']) ?></span>
<?php if ($sim['area_label'] !== '') { ?>
                <span><?= e($sim['area_label']) ?></span>
<?php } ?>
              </div>
              <div class="oc-meta">
                <div class="pr"><small>Price</small><b><?= $sim['price'] > 0 ? e($sim['price_label']) : 'On request' ?></b></div>
              </div>
            </div>
          </article>
<?php } ?>
        </div>
      </div>
    </section>
<?php } ?>

    <dialog class="lb" id="lb" aria-label="Photo gallery">
      <button class="lb-x" id="lbX" aria-label="Close">×</button
      ><button class="lb-n lb-p" id="lbP" aria-label="Previous">←</button><img id="lbImg" alt="" /><button
        class="lb-n lb-nx"
        id="lbN"
        aria-label="Next"
      >
        →
      </button>
      <p id="lbC"></p>
    </dialog>

<?php include('common/footer.php'); ?>
