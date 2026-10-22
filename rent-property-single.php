<?php
// 1. CONFIG
include('db/config.php');

// 2. DATA: Rent property project detail (/rent-property/<slug>, see .htaccess) — design: rent-zahra-apartments-town-square.html
// Admin > Manage Rent Projects (tbl_proj, type 8): project, community, gallery, qr, amenities, similar rental homes
$slug = trim(str_replace('/', '', (string) ($_GET['detail'] ?? '')));
if ($slug === '') {
    $slug = 'zahra-apartments-town-square-dubai';
}

$property = get_rent_project($slug);
if (!$property) {
    // Cross-link fallback: check if it's an Off-plan or Buy property before redirecting
    if ($slug !== '' && get_offplan_project($slug)) {
        header('Location: ' . BASE_URL . 'property/' . rawurlencode($slug), true, 301);
        exit;
    }
    if ($slug !== '' && get_buy_project($slug)) {
        header('Location: ' . BASE_URL . 'buy-property/' . rawurlencode($slug), true, 301);
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
    // Unknown or unpublished project: back to rent properties listing
    header('Location: ' . BASE_URL . 'rent-property', true, 302);
    exit;
}

// 3. SEO: the project's own meta fields (Admin > Manage Rent Projects > SEO); header falls back to photos[0] as og:image
$meta_tags = $property['meta'];
$page = [
    'active' => 'properties',
    'title' => $property['name'] . ' | Rent | DRE Homes',
    'description' => $property['clean_intro'],
    'css' => ['assets/css/pages/rent-property-single.css'],
];

include('common/header.php');
?>
<!-- ===== PROPERTY DETAIL ===== -->
<header class="pd-hero">
  <div class="wrap">
    <div class="crumbs" role="navigation" aria-label="Breadcrumb">
      <a href="<?= BASE_URL ?>">Home</a><span>/</span><a href="<?= BASE_URL ?>rent-property">Rent Property</a><span>/</span><span
        aria-current="page"><?= e($property['name']) ?></span>
    </div>
    <div class="pd-gal">
      <button class="pg-main" data-g="0" aria-label="Open gallery">
        <img src="<?= e($property['photos'][0]['src']) ?>" alt="<?= e($property['photos'][0]['title']) ?>" />
      </button>
      <?php if (!empty($property['side_photos'])) { ?>
        <div class="pg-side">
          <?php foreach ($property['side_photos'] as $photo) { ?>
            <button data-g="<?= $photo['index'] ?>" aria-label="Open image <?= $photo['index'] + 1 ?>">
              <img src="<?= e($photo['src']) ?>" alt="<?= e($photo['title']) ?>" /><?= $photo['show_all'] ? '<span class="pg-more glass on-img">⤢ View all ' . count($property['photos']) . ' photos</span>' : '' ?>
            </button>
          <?php } ?>
        </div>
      <?php } ?>
      <div class="pd-title glass on-img">
        <div class="pt-tags">
          <span>For rent</span>
          <?php if ($property['type'] !== '') { ?><span><?= e($property['type']) ?></span><?php } ?>
          <span><?= $property['beds_label'] === 'Studio' ? 'Studio' : e($property['beds_label']) . ' bedroom' ?></span>
        </div>
        <h1 class="serif"><?= $property['title_html'] ?></h1>
        <?php if ($property['sub_heading'] !== '') { ?>
          <p class="pt-sub"><?= e($property['sub_heading']) ?></p>
        <?php } ?>
        <p>
          <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
            <path
              d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z">
            </path>
          </svg>
          <?= e($property['place']) ?>
        </p>
      </div>
      <ul class="pd-photos" id="pdPhotos" hidden="">
        <?php foreach ($property['photos'] as $photo) { ?>
          <li data-src="<?= e($photo['src']) ?>" data-title="<?= e($photo['title']) ?>"
            data-cap="<?= e($photo['title']) ?>"></li>
        <?php } ?>
      </ul>
    </div>
  </div>
</header>

<section class="pd-body">
  <div class="wrap pd-grid">
    <div class="pd-main">
      <div class="pd-tabs glass" id="pdTabs">
        <a href="#overview" class="on">Overview</a>
        <a href="#details">Details</a>
        <a href="#amenities">Amenities</a>
        <a href="#location">Location</a>
        <a href="#enquire">Book a viewing</a>
      </div>

      <div class="facts facts5 reveal">
        <div class="glass"><small>Annual rent</small><b><?= e($property['price_label']) ?></b></div>
        <div class="glass"><small>Bedrooms</small><b><?= e($property['beds_label']) ?></b></div>
        <div class="glass"><small>Bathrooms</small><b><?= (int) $property['baths_num'] ?></b></div>
        <div class="glass"><small>Area</small><b><?= e($property['area_label']) ?></b></div>
        <div class="glass"><small>Rent / sq ft</small><b><?= e($property['ppf_label']) ?></b></div>
      </div>

      <article id="overview" class="pd-sec reveal">
        <span class="eyebrow">About this property · REF <?= e($property['reference']) ?></span>
        <h2 class="serif">Property <em class="gold-text">Overview.</em></h2>
        <p class="lead">
          <?= e($property['clean_intro']) ?>
        </p>
        <?php if (!empty($property['features'])) { ?>
          <div class="feat-chips">
            <?php foreach ($property['features'] as $f) { ?>
              <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path>
                </svg><?= e($f) ?></span>
            <?php } ?>
          </div>
        <?php } ?>
        <?php if ($property['about_community_text'] !== '') { ?>
          <h3 style="margin-top: 28px; margin-bottom: 12px; font-size: 1.15rem; font-weight: 600;">About <?= e($property['community']) ?></h3>
          <p class="lead"><?= e($property['about_community_text']) ?></p>
        <?php } ?>
      </article>

      <article id="details" class="pd-sec reveal">
        <span class="eyebrow">Property Details</span>
        <h2 class="serif">At a <em class="gold-text">glance.</em></h2>
        <dl class="kv">
          <div>
            <dt>Annual rent</dt>
            <dd><?= e($property['price_label']) ?></dd>
          </div>
          <div>
            <dt>Bedrooms</dt>
            <dd><?= e($property['beds_label']) ?></dd>
          </div>
          <div>
            <dt>Bathrooms</dt>
            <dd><?= (int) $property['baths_num'] ?></dd>
          </div>
          <div>
            <dt>Area</dt>
            <dd><?= e($property['area_label']) ?></dd>
          </div>
          <div>
            <dt>Type</dt>
            <dd><?= e($property['type']) ?></dd>
          </div>
          <div>
            <dt>Community</dt>
            <dd><?= e($property['community']) ?></dd>
          </div>
          <div>
            <dt>Location</dt>
            <dd><?= e($property['location']) ?></dd>
          </div>
          <div>
            <dt>Furnishing</dt>
            <dd><?= e($property['furnishing']) ?></dd>
          </div>
          <div>
            <dt>Status</dt>
            <dd><?= e($property['status']) ?></dd>
          </div>
          <div>
            <dt>Reference</dt>
            <dd><?= e($property['reference']) ?></dd>
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
              </svg><?= e($am) ?>
            </li>
          <?php } ?>
        </ul>
      </article>

      <article id="location" class="pd-sec reveal">
        <span class="eyebrow">Property Location</span>
        <h2 class="serif"><?= e($property['community']) ?>, <em class="gold-text">Dubai.</em></h2>
        <p class="lead">
          <?= e($property['place']) ?> offers convenient connectivity to major Dubai landmarks, parks, and retail destinations.
        </p>
        <?php if (!empty($property['drive_times'])) { ?>
          <div class="drive-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 24px;">
            <?php foreach ($property['drive_times'] as $dt) { ?>
              <div class="glass" style="padding: 14px 18px; border-radius: 12px;">
                <b style="font-size: 1.2rem; color: var(--gold, #c5a880);"><?= e($dt['time']) ?> <?= e($dt['unit']) ?></b>
                <div style="font-size: 0.85rem; opacity: 0.85; margin-top: 4px;"><?= e($dt['place']) ?></div>
              </div>
            <?php } ?>
          </div>
        <?php } ?>
        <?php if ($property['map_url'] !== '') { ?>
          <div class="loc-grid" style="grid-template-columns: 1fr">
            <div class="map-wrap glass" id="mapWrap">
              <button class="map-load" id="mapLoad" data-map="<?= e($property['map_url']) ?>">
                <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z">
                  </path>
                </svg><b>Load interactive map</b><small>Google Maps</small>
              </button>
            </div>
          </div>
        <?php } ?>
      </article>

      <span id="contact" aria-hidden="true"></span>
      <article id="enquire" class="pd-sec reveal">
        <div class="enq glass acc-edge">
          <div>
            <span class="eyebrow">Book a viewing</span>
            <h2 class="serif">See it <em class="gold-text">in person.</em></h2>
            <p class="lead">Fill in the form and a DRE advisor will arrange a private viewing at a time that suits you.</p>
          </div>
          <form class="form" id="ctaForm" novalidate="">
            <input class="form-control" name="name" placeholder="Your name" autocomplete="name" required="" />
            <input class="form-control" id="ctaPhone" name="phone" type="tel" placeholder="Your phone" autocomplete="tel" required="" />
            <input type="hidden" name="phone_full" id="ctaPhoneFull" />
            <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
            <input class="form-control" name="email" type="email" placeholder="Your email" autocomplete="email" />
            <input class="form-control" name="message" placeholder="Your message"
              value="I'd like to view <?= e($property['name']) ?> (REF <?= e($property['reference']) ?>)." />
            <input type="hidden" name="page_url" value="<?= e($property['url']) ?>" />
            <button class="btn btn-gold" style="justify-content: center">Request a viewing</button>
          </form>
        </div>
      </article>

      <a class="repair glass reveal" href="<?= BASE_URL ?>property-management">
        <span class="rp-ic">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
            <path
              d="M14.7 6.3a4 4 0 00-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 005.4-5.4l-2.5 2.5-2.4-.6-.6-2.4z">
            </path>
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
        <small>Annual rent</small>
        <div class="sc-price gold-text"><?= e($property['price_label']) ?></div>
        <p class="sc-ppf">per year · REF <?= e($property['reference']) ?></p>
        <ul class="sc-facts">
          <li><span>Type</span><b><?= e($property['type']) ?></b></li>
          <li><span>Community</span><b><?= e($property['community']) ?></b></li>
          <li><span>Beds / baths</span><b><?= e($property['beds_label']) ?> / <?= (int) $property['baths_num'] ?></b></li>
          <li><span>Area</span><b><?= e($property['area_label']) ?></b></li>
          <?php if ($property['sub_heading'] !== '') { ?>
            <li><span>View</span><b><?= e($property['sub_heading']) ?></b></li>
          <?php } ?>
        </ul>
        <div class="sc-ct">
          <a class="btn btn-glass sc-wa" href="<?= e($property['whatsapp']) ?>" target="_blank" rel="noopener"
            aria-label="Enquire on WhatsApp">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z">
              </path>
            </svg>WhatsApp
          </a>
          <a class="btn btn-gold sc-book" href="#enquire">Book viewing</a>
        </div>
        <a class="sc-call" href="tel:<?= preg_replace('/\s+/', '', $footer_info['footer_contact_number'] ?? '80037373') ?>">Or call <b><?= e($footer_info['footer_contact_number'] ?? '800 37373') ?></b></a>
      </div>

      <?php if ($property['qr_img'] !== '' || $property['qr_link'] !== '') { ?>
        <div class="verify glass">
          <?php if ($property['qr_img'] !== '') { ?>
            <a href="<?= e($property['qr_link'] ?: '#') ?>" target="_blank" rel="noopener" class="vq"
              aria-label="Verify listing permit">
              <img src="<?= e($property['qr_img']) ?>" alt="Listing permit QR code" />
            </a>
          <?php } ?>
          <div>
            <b>Verified listing</b>
            <dl>
              <?php if ($property['zone'] !== '') { ?>
                <div>
                  <dt>Zone</dt>
                  <dd><?= e($property['zone']) ?></dd>
                </div>
              <?php } ?>
              <div>
                <dt>RERA</dt>
                <dd><?= e($property['rera']) ?></dd>
              </div>
              <?php if ($property['brn'] !== '') { ?>
                <div>
                  <dt>BRN</dt>
                  <dd><?= e($property['brn']) ?></dd>
                </div>
              <?php } ?>
            </dl>
            <?php if ($property['qr_link'] !== '') { ?>
              <a href="<?= e($property['qr_link']) ?>" target="_blank" rel="noopener">Scan or tap to verify ↗</a>
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
          <h2 class="serif">Similar homes <em class="gold-text">for rent</em></h2>
        </div>
        <div class="car-nav">
          <button id="simPrev" aria-label="Previous">←</button><button id="simNext" aria-label="Next">→</button>
        </div>
      </div>
      <div class="sim-track" id="simTrack">
        <?php foreach ($property['similar'] as $s) { ?>
          <article class="oc sim">
            <a class="oc-img" href="<?= e($s['url']) ?>">
              <img src="<?= e($s['img']) ?>" alt="<?= e($s['name']) ?>" loading="lazy"
                onerror="this.onerror=null; this.src='<?= BASE_URL ?>admin_nsrdwsc/assets/media/logos/logo-light.png'; this.style.objectFit='contain'; this.style.padding='20px';" />
              <span class="badge"><?= e($s['type']) ?></span>
              <span class="oc-loc"><?= e($s['community']) ?></span>
            </a>
            <div class="oc-b">
              <h3>
                <a href="<?= e($s['url']) ?>"><?= e($s['name']) ?></a>
              </h3>
              <div class="specs">
                <span><?= e($s['beds_label']) ?></span>
                <span><?= e($s['baths_label']) ?></span>
                <?php if ($s['area_label'] !== '') { ?><span><?= e($s['area_label']) ?></span><?php } ?>
              </div>
              <div class="oc-meta">
                <div class="pr"><small>Annual rent</small><b><?= e($s['price_label']) ?></b></div>
              </div>
            </div>
          </article>
        <?php } ?>
      </div>
    </div>
  </section>
<?php } ?>

<?php include('common/footer.php'); ?>

