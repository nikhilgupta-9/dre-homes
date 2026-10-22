<?php
include('db/config.php');

// Off-plan project detail (/property/<slug>, see .htaccess) — design: property-palace-residences-hillside.html
// Admin > Projects: project, developer, community, gallery, payment plan, amenities, more from the developer
$slug = trim(str_replace('/', '', (string) ($_GET['detail'] ?? '')));
$property = get_offplan_project($slug);
if (!$property) {
  // Check if it's a Buy/Ready property (tbl_proj type 7)
  if ($slug !== '' && get_buy_project($slug)) {
    header('Location: ' . BASE_URL . 'buy-property/' . rawurlencode($slug), true, 301);
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
  // Unknown or unpublished project: back to the list (same as the old site)
  header('Location: ' . BASE_URL . 'offplan-properties', true, 302);
  exit;
}

// SEO: the project's own meta fields (Admin > Projects > SEO); the header falls back to the thumbnail as og:image
$meta_tags = $property['meta'];
$page = [
  'active' => 'properties',
  'title' => $property['name'] . ' | DRE Homes',
  'description' => $property['overview'][0] ?? '',
  'css' => ['assets/css/pages/property-palace-residences-hillside.css'],
];

include('common/header.php');
?>
    <!-- ===== PROPERTY DETAIL ===== -->
    <header class="pd-hero">
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>">Home</a><span>/</span><a href="<?= BASE_URL ?>offplan-properties">Off Plans</a><span>/</span
          ><span aria-current="page"><?= e($property['name']) ?></span>
        </div>
        <div class="pd-gal">
          <button class="pg-main" data-g="0" aria-label="Open gallery">
            <img src="<?= e($property['photos'][0]['src']) ?>" alt="<?= e($property['photos'][0]['title']) ?>" />
          </button>
<?php if ($property['side_photos']) { ?>
          <div class="pg-side">
<?php foreach ($property['side_photos'] as $photo) { ?>
            <button data-g="<?= $photo['index'] ?>" aria-label="Open image <?= $photo['index'] + 1 ?>">
              <img src="<?= e($photo['src']) ?>" alt="<?= e($photo['title']) ?>" /><?= $photo['show_all'] ? '<span class="pg-more glass on-img">⤢ View all ' . count($property['photos']) . ' photos</span>' : '' ?>

            </button>
<?php } ?>
          </div>
<?php } ?>
          <div class="pd-title glass on-img">
            <div class="pt-tags"><span>Off plan</span><?= $property['type'] !== '' ? '<span>' . e($property['type']) . '</span>' : '' ?><?= $property['handover'] !== '' ? '<span>Handover ' . e($property['handover']) . '</span>' : '' ?></div>
            <h1 class="serif"><?= $property['title_html'] ?></h1>
            <p>
              <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"></path></svg>
              <?= e($property['place']) ?><?= $property['developer'] !== '' ? ' · by ' . e($property['developer']) : '' ?>

            </p>
          </div>
          <ul class="pd-photos" id="pdPhotos" hidden="">
<?php foreach ($property['photos'] as $photo) { ?>
            <li data-src="<?= e($photo['src']) ?>" data-title="<?= e($photo['title']) ?>" data-cap="<?= e($photo['title']) ?>"></li>
<?php } ?>
          </ul>
        </div>
      </div>
    </header>

    <section class="pd-body">
      <div class="wrap pd-grid">
        <div class="pd-main">
          <div class="pd-tabs glass" id="pdTabs">
<?php foreach ($property['sections'] as $id => $label) { ?>
            <a href="#<?= $id ?>"<?= $id === array_key_first($property['sections']) ? ' class="on"' : '' ?>><?= $label ?></a>
<?php } ?>
          </div>

          <div class="facts reveal">
            <div class="glass"><small>Starting from</small><b><?= $property['price'] ? e($property['price_label']) : 'Price on request' ?></b></div>
<?php if ($property['bedrooms'] !== '') { ?>
            <div class="glass"><small>Bedrooms</small><b><?= e($property['bedrooms']) ?></b></div>
<?php } ?>
<?php if ($property['payment_plan'] !== '') { ?>
            <div class="glass"><small>Payment plan</small><b><?= e($property['payment_plan']) ?></b></div>
<?php } elseif ($property['handover'] !== '') { ?>
            <div class="glass"><small>Handover</small><b><?= e($property['handover']) ?></b></div>
<?php } ?>
<?php if ($property['developer'] !== '') { ?>
            <div class="glass"><small>Developer</small><b><?= e($property['developer']) ?></b></div>
<?php } ?>
          </div>

<?php if ($property['overview']) { ?>
          <article id="overview" class="pd-sec reveal">
            <span class="eyebrow">Property Overview</span>
            <h2 class="serif"><?= $property['overview_title_html'] ?></h2>
            <p class="lead"><?= e($property['overview'][0]) ?></p>
<?php if (count($property['overview']) > 1) { ?>
            <div class="more-txt" id="ovMore">
<?php foreach (array_slice($property['overview'], 1) as $p) { ?>
              <p class="lead"><?= e($p) ?></p>
<?php } ?>
            </div>
            <button class="rm" data-t="ovMore">Read more ↓</button>
<?php } ?>
          </article>
<?php } ?>

<?php if (isset($property['sections']['amenities'])) { ?>
          <article id="amenities" class="pd-sec reveal">
            <span class="eyebrow">Property Amenities</span>
            <h2 class="serif">Resort-style, <em class="gold-text">every day.</em></h2>
<?php if ($property['amenities']) { ?>
            <div class="am-grid">
<?php foreach ($property['amenities'] as $a) { ?>
              <div class="am glass">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><?= $a['icon'] ?></svg><span><?= e($a['name']) ?></span>
              </div>
<?php } ?>
            </div>
<?php } ?>
<?php foreach ($property['amenities_text'] as $p) { ?>
            <p class="lead"><?= e($p) ?></p>
<?php } ?>
          </article>
<?php } ?>

<?php if (isset($property['sections']['location'])) { ?>
          <article id="location" class="pd-sec reveal">
            <span class="eyebrow">Property Location</span>
            <h2 class="serif">At the heart of <em class="gold-text"><?= e($property['location'] ?: 'the UAE') ?>.</em></h2>
<?php foreach ($property['location_text'] as $p) { ?>
            <p class="lead"><?= e($p) ?></p>
<?php } ?>
<?php if ($property['map_url'] || $property['location_img']) { ?>
            <div class="loc-grid">
<?php if ($property['map_url']) { ?>
              <div class="map-wrap glass" id="mapWrap">
                <button class="map-load" id="mapLoad" data-map="<?= e($property['map_url']) ?>">
                  <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28"><path d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"></path></svg
                  ><b>Load interactive map</b><small>Google Maps</small>
                </button>
              </div>
<?php } ?>
<?php if ($property['location_img']) { ?>
              <button class="loc-img" data-g="<?= (int) $property['location_img_index'] ?>" aria-label="Open location map">
                <img src="<?= e($property['location_img']) ?>" alt="<?= e($property['name']) ?> location map" loading="lazy" />
              </button>
<?php } ?>
            </div>
<?php } ?>
          </article>
<?php } ?>

<?php if (isset($property['sections']['masterplan'])) { ?>
          <article id="masterplan" class="pd-sec reveal">
            <span class="eyebrow">Property Masterplan</span>
            <h2 class="serif">Designed to <em class="gold-text">blend in.</em></h2>
<?php if ($property['masterplan_img']) { ?>
            <button class="mp-img" data-g="<?= (int) $property['masterplan_img_index'] ?>" aria-label="Open masterplan">
              <img src="<?= e($property['masterplan_img']) ?>" alt="<?= e($property['name']) ?> masterplan" loading="lazy" /><span class="glass on-img">⤢ Zoom</span>
            </button>
<?php } ?>
<?php foreach ($property['masterplan_text'] as $p) { ?>
            <p class="lead"><?= e($p) ?></p>
<?php } ?>
          </article>
<?php } ?>

          <article id="mortgage" class="pd-sec reveal">
            <span class="eyebrow">Mortgage estimate</span>
            <h2 class="serif">What could you <em class="gold-text">pay monthly?</em></h2>
            <div class="mort glass">
              <div class="mg-in">
                <label
                  ><span>Property price</span>
                  <div class="mv"><span>AED</span><input id="mPrice" inputmode="numeric" value="<?= $property['price'] ? number_format($property['price']) : '1,000,000' ?>" /></div
                ></label>
                <label><span>Down payment <b id="mDpV">20%</b></span><input type="range" id="mDp" min="20" max="80" step="5" value="20" /></label>
                <label><span>Interest rate <b id="mRtV">4.5%</b></span><input type="range" id="mRt" min="2" max="9" step="0.1" value="4.5" /></label>
                <label><span>Loan term <b id="mYrV">25 years</b></span><input type="range" id="mYr" min="5" max="25" step="1" value="25" /></label>
              </div>
              <div class="m-out">
                <small>Estimated monthly payment</small>
                <div class="m-big gold-text" id="mOut">—</div>
                <ul>
                  <li><span>Loan amount</span><b id="mLoan">—</b></li>
                  <li><span>Down payment</span><b id="mDown">—</b></li>
                  <li><span>Total interest</span><b id="mInt">—</b></li>
                </ul>
                <p>Indicative only — excludes DLD fees and bank charges. Our mortgage advisors can get you a pre-approval.</p>
                <a class="btn btn-glass" href="<?= page_link('mortgage-advisory') ?>">Talk to a mortgage advisor →</a>
              </div>
            </div>
          </article>

          <span id="contact" aria-hidden="true"></span>
          <article id="enquire" class="pd-sec reveal">
            <div class="enq glass acc-edge">
              <div>
                <span class="eyebrow">Need more details?</span>
                <h2 class="serif">Let’s <em class="gold-text">talk.</em></h2>
                <p class="lead">Floor plans, availability and the latest payment plan — sent to you by a DRE off-plan specialist.</p>
              </div>
              <form class="form" id="ctaForm" novalidate="">
                <input name="name" placeholder="Your name" autocomplete="name" required="" />
                <input id="ctaPhone" name="phone" type="tel" placeholder="Your phone" autocomplete="tel" required="" />
                <input type="hidden" name="phone_full" id="ctaPhoneFull" />
                <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
                <input name="email" type="email" placeholder="Your email" autocomplete="email" />
                <input name="message" placeholder="Your message" value="I'm interested in <?= e($property['name']) ?>." />
                <input type="hidden" name="page_url" value="<?= e($property['url']) ?>" />
                <button class="btn btn-gold" style="justify-content: center">Send enquiry</button>
              </form>
            </div>
          </article>
        </div>

        <aside class="pd-side">
          <div class="side-card glass acc-edge">
<?php if ($property['logo']) { ?>
            <img class="dev-logo" src="<?= e($property['logo']) ?>" alt="<?= e($property['name']) ?> logo" />
<?php } ?>
            <small>Starting from</small>
            <div class="sc-price gold-text"><?= $property['price'] ? e($property['price_label']) : 'Price on request' ?></div>
            <ul class="sc-facts">
<?php if ($property['developer'] !== '') { ?>
              <li><span>Developer</span><?= $property['developer_url'] ? '<a href="' . e($property['developer_url']) . '">' . e($property['developer']) . ' →</a>' : '<b>' . e($property['developer']) . '</b>' ?></li>
<?php } ?>
<?php if ($property['location'] !== '') { ?>
              <li><span>Location</span><b><?= e($property['location']) ?></b></li>
<?php } ?>
<?php if ($property['type'] !== '') { ?>
              <li><span>Type</span><b><?= e($property['type']) ?></b></li>
<?php } ?>
<?php if ($property['bedrooms_list'] !== '') { ?>
              <li><span>Bedrooms</span><b><?= e($property['bedrooms_list']) ?></b></li>
<?php } ?>
<?php if ($property['handover'] !== '') { ?>
              <li><span>Handover</span><b><?= e($property['handover']) ?></b></li>
<?php } ?>
<?php if ($property['payment_plan'] !== '') { ?>
              <li><span>Payment plan</span><b><?= e($property['payment_plan']) ?></b></li>
<?php } ?>
            </ul>
            <div class="sc-dl">
              <button class="btn btn-gold" data-dl="Brochure">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M12 3v12M7 10l5 5 5-5M4 21h16"></path></svg>Brochure
              </button>
              <button class="btn btn-glass" data-dl="Payment Plan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M12 3v12M7 10l5 5 5-5M4 21h16"></path></svg>Payment plan
              </button>
            </div>
            <div class="sc-ct">
              <a class="btn btn-glass sc-wa" href="<?= e($property['whatsapp']) ?>" target="_blank" rel="noopener"
                ><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"></path></svg
                >WhatsApp</a
              >
              <button class="btn btn-glass" id="pdVideo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16"><rect x="2.5" y="6" width="13" height="12" rx="3"></rect><path d="M15.5 10.5l6-3.5v10l-6-3.5z"></path></svg>Video call
              </button>
            </div>
            <a class="sc-call" href="tel:<?= e(preg_replace('/[^0-9+]/', '', $footer_info['footer_contact_number'] ?? '80037373')) ?>">Or call <b><?= e($footer_info['footer_contact_number'] ?? '800 37373') ?></b></a>
          </div>
        </aside>
      </div>
    </section>

<?php if ($property['similar']) { ?>
    <section id="similar" style="padding-top: 40px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">More like this</span>
            <h2 class="serif">More from <em class="gold-text"><?= e($property['developer_short']) ?></em></h2>
          </div>
          <div class="car-nav">
            <button id="simPrev" aria-label="Previous">←</button><button id="simNext" aria-label="Next">→</button>
          </div>
        </div>
        <div class="sim-track" id="simTrack">
<?php foreach ($property['similar'] as $s) { ?>
          <article class="oc sim">
            <a class="oc-img" href="<?= e($s['url']) ?>"
              ><img src="<?= e($s['img']) ?>" alt="<?= e($s['name']) ?>" loading="lazy" /><span class="badge"><?= e($s['badge']) ?></span><?= $s['location'] !== '' ? '<span class="oc-loc">' . e($s['location']) . '</span>' : '' ?></a
            >
            <div class="oc-b">
              <span class="oc-dev">By <?= e($property['developer']) ?></span>
              <h3><a href="<?= e($s['url']) ?>"><?= e($s['name']) ?></a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b><?= e($s['beds']) ?></b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b><?= $s['price'] !== '' ? e($s['price']) : '<em>Price on request</em>' ?></b></div>
              </div>
            </div>
          </article>
<?php } ?>
        </div>
      </div>
    </section>
<?php } ?>

    <!-- Lightbox -->
    <dialog class="lb" id="lb" aria-label="Photo gallery">
      <button class="lb-x" id="lbX" aria-label="Close">×</button
      ><button class="lb-n lb-p" id="lbP" aria-label="Previous">←</button><img id="lbImg" alt="" /><button class="lb-n lb-nx" id="lbN" aria-label="Next">→</button>
      <p id="lbC"></p>
    </dialog>

    <!-- Download modal -->
    <dialog class="dl glass" id="dl" aria-label="Download">
      <form id="dlForm" data-done="A DRE off-plan specialist will send it to you shortly.">
        <button type="button" class="sv-x" id="dlX" aria-label="Close">×</button>
        <span class="eyebrow" id="dlEy">Download</span>
        <h2 class="serif">Get the <em class="gold-text" id="dlT">brochure</em></h2>
        <p class="lead"><?= e($property['name']) ?><?= $property['developer'] !== '' ? ' · ' . e($property['developer']) : '' ?></p>
        <input name="name" placeholder="Your name" required="" />
        <input name="phone" type="tel" placeholder="Your phone" required="" />
        <input name="email" type="email" placeholder="Your email" required="" />
        <button class="btn btn-gold" style="justify-content: center">Send it to me</button>
      </form>
    </dialog>

<?php include('common/footer.php'); ?>
