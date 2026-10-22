<?php
// 1. CONFIG
include('db/config.php');

// 2. DATA: International project detail (/international-properties/<slug>, see .htaccess) — design: intl-the-address-residence-istanbul.html
// Admin > Manage Projects (tbl_proj, type 3): project, developer, gallery, amenities, downloads
$slug = trim(str_replace('/', '', (string) ($_GET['detail'] ?? '')));
if ($slug === '') {
    $slug = 'the-address-residence-istanbul';
}

$property = get_international_project($slug);
if (!$property) {
    // Cross-link fallback: check if it belongs to another section before redirecting
    if ($slug !== '' && get_offplan_project($slug)) {
        header('Location: ' . BASE_URL . 'property/' . rawurlencode($slug), true, 301);
        exit;
    }
    if ($slug !== '' && get_buy_project($slug)) {
        header('Location: ' . BASE_URL . 'buy-property/' . rawurlencode($slug), true, 301);
        exit;
    }
    if ($slug !== '' && get_rent_project($slug)) {
        header('Location: ' . BASE_URL . 'rent-property/' . rawurlencode($slug), true, 301);
        exit;
    }
    if ($slug !== '' && get_ready_project($slug)) {
        header('Location: ' . BASE_URL . 'ready-to-move-in/' . rawurlencode($slug), true, 301);
        exit;
    }
    // Unknown or unpublished project: back to international properties listing
    header('Location: ' . BASE_URL . 'international-properties', true, 302);
    exit;
}

// 3. SEO: the project's own meta fields (Admin > Manage Projects > SEO); header falls back to photos[0] as og:image
$meta_tags = $property['meta'];
$page = [
    'active' => 'properties',
    'title' => $property['name'] . ' | DRE Homes',
    'description' => $property['meta']['meta_description'],
    'css' => ['assets/css/pages/international-properties-single.css'],
];

include('common/header.php');
?>
    <!-- ===== PROPERTY DETAIL ===== -->
    <header class="pd-hero">
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>">Home</a><span>/</span><a href="<?= BASE_URL ?>international-properties">International</a><span>/</span
          ><span aria-current="page"><?= e($property['name']) ?></span>
        </div>
        <div class="pd-gal">
<?php if (!empty($property['photos'])) { ?>
          <button class="pg-main" data-g="0" aria-label="Open gallery">
            <img src="<?= e($property['photos'][0]['src']) ?>" alt="<?= e($property['photos'][0]['title']) ?>" />
          </button>
<?php } ?>
<?php if (!empty($property['side_photos'])) { ?>
          <div class="pg-side">
            <button data-g="1" aria-label="Open photo 2">
              <img src="<?= e($property['side_photos'][0]['src']) ?>" alt="<?= e($property['side_photos'][0]['title']) ?>" />
            </button>
<?php if (isset($property['side_photos'][1])) { ?>
            <button data-g="2" aria-label="Open photo 3">
              <img src="<?= e($property['side_photos'][1]['src']) ?>" alt="<?= e($property['side_photos'][1]['title']) ?>" />
              <span class="pg-more glass on-img">⤢ View all <?= count($property['photos']) ?> photos</span>
            </button>
<?php } ?>
          </div>
<?php } ?>
          <div class="pd-title glass on-img">
            <div class="pt-tags">
              <span><?= e($property['flag']) ?> <?= e($property['country']) ?></span>
              <span>Residences &amp; Penthouses</span>
<?php if ($property['developer'] !== '') { ?>
              <span><?= e($property['developer']) ?></span>
<?php } ?>
            </div>
            <h1 class="serif"><?= $property['title_html'] ?></h1>
            <p>
              <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
                <path d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"></path>
              </svg>
              <?= e($property['location']) ?><?php if ($property['developer'] !== '') { ?> · by <?= e($property['developer']) ?><?php } ?>
            </p>
          </div>
<?php if (!empty($property['photos'])) { ?>
          <ul class="pd-photos" id="pdPhotos" hidden="">
<?php foreach ($property['photos'] as $photo) { ?>
            <li data-src="<?= e($photo['src']) ?>" data-title="<?= e($photo['title']) ?>"></li>
<?php } ?>
          </ul>
<?php } ?>
        </div>
      </div>
    </header>

    <section class="pd-body">
      <div class="wrap pd-grid">
        <div class="pd-main">
          <div class="pd-tabs glass" id="pdTabs">
            <a href="#overview" class="on">Overview</a><a href="#amenities">Amenities</a><a href="#location">Location</a
            ><a href="#downloads">Downloads</a><a href="#enquire">Enquire</a>
          </div>

          <div class="facts reveal">
            <div class="glass"><small>Starting from</small><b><?= e($property['price_label']) ?></b></div>
            <div class="glass"><small>Bedrooms</small><b><?= e($property['beds']) ?></b></div>
            <div class="glass"><small>Payment plan</small><b><?= e($property['payment_plan']) ?></b></div>
<?php if ($property['developer'] !== '') { ?>
            <div class="glass"><small>Developer</small><b><?= e($property['developer']) ?></b></div>
<?php } ?>
          </div>

<?php if (!empty($property['overview'])) { ?>
          <article id="overview" class="pd-sec reveal">
            <span class="eyebrow">About this property</span>
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

<?php if (!empty($property['amenities'])) { ?>
          <article id="amenities" class="pd-sec reveal">
            <span class="eyebrow">Property Amenities</span>
            <h2 class="serif">Hotel-grade, <em class="gold-text">at home.</em></h2>
            <div class="am-grid">
<?php foreach ($property['amenities'] as $a) { ?>
              <div class="am glass">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><?= $a['icon'] ?></svg>
                <span><?= e($a['name']) ?></span>
              </div>
<?php } ?>
            </div>
          </article>
<?php } ?>

          <article id="location" class="pd-sec reveal">
            <span class="eyebrow">Property Location</span>
            <h2 class="serif">Destination: <em class="gold-text"><?= e($property['location']) ?>.</em></h2>
            <div class="loc-grid">
<?php if ($property['map_url'] !== '') { ?>
              <div class="map-wrap glass" id="mapWrap">
                <button
                  class="map-load"
                  id="mapLoad"
                  data-map="<?= e($property['map_url']) ?>"
                >
                  <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28">
                    <path d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"></path>
                  </svg>
                  ><b>Load interactive map</b><small>Google Maps</small>
                </button>
              </div>
<?php } ?>
              <ol class="drive glass">
                <li><b>10<small>min</small></b><span>City Center &amp; Shopping</span></li>
                <li><b>15<small>min</small></b><span>Business District</span></li>
                <li><b>20<small>min</small></b><span>Cultural &amp; Historical Sites</span></li>
                <li><b>30<small>min</small></b><span>International Airport</span></li>
              </ol>
            </div>
          </article>

          <article id="downloads" class="pd-sec reveal">
            <span class="eyebrow">Downloads</span>
            <h2 class="serif">Get the <em class="gold-text">full details.</em></h2>
            <div class="dl-cards">
              <button class="dl-card glass" data-dl="Brochure">
                <span class="dl-ic"
                  ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M6 3h9l4 4v14H6z"></path>
                    <path d="M14 3v5h5M9 13h7M9 17h5"></path></svg></span
                ><b>Brochure</b><small>Project overview, amenities &amp; visuals</small><em>Download ↓</em>
              </button>
              <button class="dl-card glass" data-dl="Payment Plan">
                <span class="dl-ic"
                  ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                    <path d="M3 10h18M7 15h4"></path></svg></span
                ><b>Payment Plan</b><small>Instalments &amp; milestones</small><em>Download ↓</em>
              </button>
              <button class="dl-card glass" data-dl="Floor Plan">
                <span class="dl-ic"
                  ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M3 3h18v18H3zM3 12h8M11 3v13M15 12h6M15 12v9"></path></svg></span
                ><b>Floor Plans</b><small>Residences &amp; layouts</small><em>Download ↓</em>
              </button>
            </div>
          </article>

          <span id="contact" aria-hidden="true"></span>
          <article id="enquire" class="pd-sec reveal">
            <div class="enq glass acc-edge">
              <div>
                <span class="eyebrow">Interested?</span>
                <h2 class="serif">Let’s <em class="gold-text">talk.</em></h2>
                <p class="lead">Availability, prices and the latest payment plan — sent to you by a DRE advisor.</p>
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
                  value="I'm interested in <?= e($property['name']) ?>."
                />
                <input
                  type="hidden"
                  name="page_url"
                  value="<?= e($property['url']) ?>"
                />
                <button class="btn btn-gold" style="justify-content: center">Send enquiry</button>
              </form>
            </div>
          </article>
        </div>

        <aside class="pd-side">
          <div class="side-card glass acc-edge">
            <small>Starting from</small>
            <div class="sc-price gold-text"><?= e($property['price_label']) ?></div>
            <ul class="sc-facts">
<?php if ($property['developer'] !== '') { ?>
              <li><span>Developer</span><b><?= e($property['developer']) ?></b></li>
<?php } ?>
              <li><span>Location</span><b><?= e($property['location']) ?></b></li>
              <li><span>Type</span><b>Residences</b></li>
              <li><span>Bedrooms</span><b><?= e($property['beds']) ?></b></li>
              <li><span>Payment plan</span><b><?= e($property['payment_plan']) ?></b></li>
            </ul>
            <div class="sc-dl">
              <button class="btn btn-gold" data-dl="Brochure">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                  <path d="M12 3v12M7 10l5 5 5-5M4 21h16"></path></svg
                >Brochure
              </button>
              <button class="btn btn-glass" data-dl="Payment Plan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                  <path d="M12 3v12M7 10l5 5 5-5M4 21h16"></path></svg
                >Payment plan
              </button>
            </div>
            <div class="sc-ct">
              <a
                class="btn btn-glass sc-wa"
                href="<?= e($property['whatsapp']) ?>"
                target="_blank"
                rel="noopener"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                  ></path></svg
                >WhatsApp</a
              >
              <button class="btn btn-glass" id="popinBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16">
                  <rect x="2.5" y="6" width="13" height="12" rx="3"></rect>
                  <path d="M15.5 10.5l6-3.5v10l-6-3.5z"></path></svg
                >Video call
              </button>
            </div>
            <a class="sc-call" href="tel:<?= e($toll_free) ?>">Or call <b><?= e($toll_free) ?></b></a>
          </div>
        </aside>
      </div>
    </section>

<?php if (!empty($property['similar'])) { ?>
    <section id="similar" style="padding-top: 40px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">More like this</span>
            <h2 class="serif">More <em class="gold-text">international</em> projects</h2>
          </div>
          <div class="car-nav">
            <button id="simPrev" aria-label="Previous">←</button><button id="simNext" aria-label="Next">→</button>
          </div>
        </div>
        <div class="sim-track" id="simTrack">
<?php foreach ($property['similar'] as $sim) { ?>
          <article class="oc sim">
            <a class="oc-img" href="<?= e($sim['url']) ?>"
              ><img
                src="<?= e($sim['image']) ?>"
                alt="<?= e($sim['name']) ?>"
                loading="lazy"
              /><span class="badge"><?= e($sim['flag']) ?> International</span><span class="oc-loc"><?= e($sim['location']) ?></span></a
            >
            <div class="oc-b">
              <h3><a href="<?= e($sim['url']) ?>"><?= e($sim['name']) ?></a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b><?= e($sim['beds']) ?></b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><?= $sim['price_html'] ?>
                </div>
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

    <dialog class="dl glass" id="dl" aria-label="Download">
      <form id="dlForm" data-done="A DRE advisor will send it to you shortly.">
        <button type="button" class="sv-x" id="dlX" aria-label="Close">×</button>
        <span class="eyebrow" id="dlEy">Download</span>
        <h2 class="serif">Get the <em class="gold-text" id="dlT">brochure</em></h2>
        <p class="lead"><?= e($property['name']) ?><?php if ($property['developer'] !== '') { ?> · <?= e($property['developer']) ?><?php } ?></p>
        <input name="name" placeholder="Your name" required="" />
        <input id="dlPhone" name="phone" type="tel" placeholder="Your phone" required="" />
        <input type="hidden" name="phone_full" id="dlPhoneFull" />
        <div class="err" id="dlPhoneErr" role="alert">Please enter a valid phone number.</div>
        <input name="email" type="email" placeholder="Your email (we will send the file here)" required="" />
        <input type="hidden" name="item" id="dlItem" value="Brochure" />
        <input type="hidden" name="project" value="<?= e($property['name']) ?>" />
        <input type="hidden" name="page_url" value="<?= e($property['url']) ?>" />
        <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Send me the file</button>
      </form>
    </dialog>

<?php include('common/footer.php'); ?>

