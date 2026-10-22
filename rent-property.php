<?php
// 1. CONFIG
include('db/config.php');

// 2. SEO: Admin > Manage Tags ("Rent Property", id=20, edit-tags.php?edit=MjA=)
$meta_tags = get_seo_tags(20);

// 3. DATA: Admin > Manage Rent Projects (tbl_proj, type=8, active=1) & Rent Property Content (tbl_rentproperty_content)
$rent_content = get_rent_content();          // Admin > Rent Property > Rent Property Content
$projects = get_rent_projects();             // Admin > Manage Rent Projects: published rental properties as cards
$filters = get_rent_filters($projects);      // types, communities, quick communities, prices for the filter bar
$cards_per_page = 9;                        // the page's JS shows this many per page

// Filter exclusive/featured properties for the #exTrack carousel
$exclusive_projects = array_values(array_filter($projects, function ($p) {
  return !empty($p['is_featured']);
}));

// Hero Background Image: Prioritize SEO sharing picture from Admin > Manage Tags (edit-tags.php?edit=MjA=), fallback to Rent Property Content
$hero_bg_img = '';
if (!empty($meta_tags['meta_og_img']) && is_file(__DIR__ . '/admin_nsrdwsc/assets/media/meta/' . $meta_tags['meta_og_img'])) {
  $hero_bg_img = BASE_URL_IMG . 'meta/' . rawurlencode($meta_tags['meta_og_img']);
} elseif (!empty($rent_content['rentproperty_cont_img']) && is_file(__DIR__ . '/admin_nsrdwsc/assets/media/project/' . $rent_content['rentproperty_cont_img'])) {
  $hero_bg_img = BASE_URL_IMG . 'project/' . rawurlencode($rent_content['rentproperty_cont_img']);
}

// Hero Subtitle: from Admin > Manage Tags meta_description (edit-tags.php?edit=MjA=)
$hero_subtitle = (!empty($meta_tags['meta_description']) && $meta_tags['meta_description'] !== 'Rent Property')
  ? $meta_tags['meta_description']
  : 'Apartments, villas and townhouses for rent across Dubai — verified listings and viewings arranged by DRE.';

$page = [
  'active' => 'properties',
  'css' => ['assets/css/pages/rent-property.css'],
];

include('common/header.php');
?>
<!-- ===== RENT: HERO ===== -->
<header class="ph-hero op-hero">
  <div class="ph-bg" <?= $hero_bg_img !== '' ? 'style="background-image: url(' . e($hero_bg_img) . '); background-position: center 60%;"' : '' ?>></div>
  <div class="wrap">
    <div class="crumbs" role="navigation" aria-label="Breadcrumb">
      <a href="<?= BASE_URL ?>">Home</a><span>/</span><a href="#listing">Properties</a><span>/</span><span
        aria-current="page">Rent Property</span>
    </div>
    <span class="eyebrow">Rent Property · Homes for rent</span>
    <h1 class="serif">Your next home, <em class="gold-text">ready to rent.</em></h1>
    <p class="sub">
      <?= e($hero_subtitle) ?>
    </p>
    <form class="op-search glass" id="opSearch" role="search">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <circle cx="11" cy="11" r="7"></circle>
        <path d="M20 20l-3.5-3.5"></path>
      </svg>
      <input id="qIn" type="search" placeholder="Search a community or building — e.g. Town Square, JVC…"
        aria-label="Search rental homes" />
      <button class="btn btn-gold" type="submit">Search</button>
    </form>
    <div class="quick" id="quickC">
      <?php foreach ($filters['quick_communities'] as $qc) { ?>
        <button type="button" data-c="<?= e($qc['name']) ?>">
          <?= e($qc['name']) ?> <span style="opacity: 0.6"><?= (int) $qc['count'] ?></span>
        </button>
      <?php } ?>
    </div>
  </div>
</header>

<?php if (!empty($exclusive_projects)) { ?>
  <!-- ===== EXCLUSIVE ===== -->
  <section id="exclusive" style="padding: 60px 0 10px">
    <div class="wrap">
      <div class="head-row" style="margin-bottom: 26px">
        <div>
          <span class="eyebrow">Exclusive Listings</span>
          <h2 class="serif">Only with <em class="gold-text">DRE.</em></h2>
        </div>
        <div class="car-nav">
          <button id="exPrev" aria-label="Previous">←</button><button id="exNext" aria-label="Next">→</button>
        </div>
      </div>
      <div class="ex-track" id="exTrack">
        <?php foreach ($exclusive_projects as $p) { ?>
          <article class="oc" data-i="0" data-ty="<?= e($p['type']) ?>" data-c="<?= e($p['community']) ?>"
            data-p="<?= $p['price'] ?>" data-bn="<?= $p['beds_num'] ?>" data-bt="<?= $p['baths_num'] ?>"
            data-a="<?= $p['area'] ?>" data-ppf="<?= $p['ppf'] ?>" data-u="<?= e($p['slug']) ?>"
            data-l="<?= e($p['location']) ?>" data-x="1">
            <div class="oc-img" data-href="<?= e($p['url']) ?>">
              <div class="oc-slides">
                <?php foreach ($p['images'] as $img_idx => $img_url) { ?>
                  <img src="<?= e($img_url) ?>"
                    alt="<?= e($p['name']) . ($p['location'] !== '' ? ', ' . e($p['location']) : '') . ' photo ' . ($img_idx + 1) ?>"
                    loading="lazy"
                    onerror="this.onerror=null; this.src='<?= BASE_URL ?>admin_nsrdwsc/assets/media/logos/logo-light.png'; this.style.objectFit='contain'; this.style.padding='20px';" />
                <?php } ?>
              </div>
              <span class="badge exc">Exclusive</span>
              <?php if ($p['community'] !== '') { ?>
                <span class="oc-loc"><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z">
                    </path>
                  </svg><?= e($p['community']) ?></span>
              <?php } ?>
              <button class="oc-nav p" aria-label="Previous photo">‹</button><button class="oc-nav n"
                aria-label="Next photo">›</button><span class="oc-dots">
                <?php foreach ($p['images'] as $img_idx => $img_url) { ?>
                  <i<?= $img_idx === 0 ? ' class="on"' : '' ?>></i>
                <?php } ?>
              </span>
            </div>
            <button class="oc-fav" data-u="<?= e($p['slug']) ?>" aria-pressed="false"
              aria-label="Save <?= e($p['name']) ?>">
              ♡
            </button>
            <div class="oc-b">
              <span class="tp"><?= e($p['type']) ?></span>
              <h3>
                <a href="<?= e($p['url']) ?>"><?= e($p['name']) ?></a>
              </h3>
              <div class="specs">
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M3 18V8M21 18v-5a3 3 0 00-3-3H10v8M3 14h18M6.5 11.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                  </svg><?= e($p['beds_label']) ?></span><?php if ($p['baths_num'] > 0) { ?><span><svg viewBox="0 0 24 24"
                      fill="none" stroke="currentColor" stroke-width="1.6">
                      <path d="M4 12h16v3a4 4 0 01-4 4H8a4 4 0 01-4-4zM6 12V6a2 2 0 014 0M7 19l-1 2M17 19l1 2"></path>
                    </svg><?= e($p['baths_label']) ?></span><?php } ?><?php if ($p['area'] > 0) { ?><span><svg
                      viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                      <path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"></path>
                    </svg><?= e($p['area_label']) ?></span><?php } ?>
              </div>
              <div class="oc-meta">
                <div class="pr">
                  <small>Annual rent</small><b><?= $p['price'] > 0 ? e($p['price_label']) : 'Price on request' ?></b>
                  <?php if ($p['ppf'] > 0) { ?>
                    <div class="ppf"><?= e($p['ppf_label']) ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="<?= e($p['url']) ?>">View details →</a><a class="oc-wa"
                  href="<?= e($p['whatsapp']) ?>" target="_blank" rel="noopener"
                  aria-label="Enquire about <?= e($p['name']) ?> on WhatsApp"><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z">
                    </path>
                  </svg></a>
              </div>
            </div>
          </article>
        <?php } ?>
      </div>
    </div>
  </section>
<?php } ?>

<!-- ===== LISTING ===== -->
<section id="listing" class="op-list">
  <div class="wrap">
    <div class="fbar glass" id="fbar">
      <div class="fb-row">
        <div class="seg" id="tySeg" role="tablist" aria-label="Property type">
          <button role="tab" data-t="" class="on">All types</button><button role="tab"
            data-t="Apartment">Apartments</button><button role="tab" data-t="Villa">Villas</button><button role="tab"
            data-t="Townhouse">Townhouses</button>
        </div>
        <label class="fsel"><span>Community</span><select id="locSel">
            <option value="">All communities</option>
            <?php foreach ($filters['communities'] as $c) { ?>
              <option><?= e($c) ?></option>
            <?php } ?>
          </select></label>
        <label class="fsel"><span>Min price</span><select id="pMin">
            <option value="">Any</option>
            <?php foreach ($filters['prices'] as $price_step) { ?>
              <option value="<?= $price_step ?>">AED <?= number_format($price_step) ?> / yr</option>
            <?php } ?>
          </select></label>
        <label class="fsel"><span>Max price</span><select id="pMax">
            <option value="">Any</option>
            <?php foreach ($filters['prices'] as $price_step) { ?>
              <option value="<?= $price_step ?>">AED <?= number_format($price_step) ?> / yr</option>
            <?php } ?>
          </select></label>
        <button class="fmore" id="moreBtn" aria-expanded="false" aria-controls="moreF">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M4 6h16M7 12h10M10 18h4"></path>
          </svg>Beds &amp; baths<b id="bedCount"></b>
        </button>
      </div>
      <div class="more" id="moreF" hidden="">
        <div class="more-row">
          <small>Bedrooms</small>
          <div class="beds" id="bedChips">
            <button type="button" data-b="STD">Studio</button><button type="button" data-b="1">1</button><button
              type="button" data-b="2">2</button><button type="button" data-b="3">3</button><button type="button"
              data-b="4">4</button><button type="button" data-b="5">5+</button>
          </div>
        </div>
        <div class="more-row">
          <small>Bathrooms</small>
          <div class="beds" id="bathChips">
            <button type="button" data-b="1">1+</button><button type="button" data-b="2">2+</button><button
              type="button" data-b="3">3+</button><button type="button" data-b="4">4+</button><button type="button"
              data-b="5">5+</button>
          </div>
        </div>
        <div class="more-row">
          <small>Min size</small>
          <div class="beds" id="sizeChips">
            <button type="button" data-s="500">500+ sq ft</button><button type="button" data-s="1000">1,000+ sq
              ft</button><button type="button" data-s="1500">1,500+ sq ft</button><button type="button"
              data-s="2500">2,500+ sq ft</button><button type="button" data-s="4000">4,000+ sq ft</button>
          </div>
        </div>
      </div>
    </div>

    <div class="rbar">
      <p id="rCount" data-noun="homes for rent"></p>
      <div class="rbar-r">
        <button class="clear" id="clearAll" hidden="">Clear filters ×</button>
        <label class="fsel sort"><span>Sort</span><select id="sortSel">
            <option value="feat">Featured</option>
            <option value="pa">Rent: low to high</option>
            <option value="pd">Rent: high to low</option>
            <option value="sz">Size: largest first</option>
            <option value="ppf">Best value (rent / sq ft)</option>
          </select></label>
        <button class="favbtn" id="favOnly" aria-pressed="false" title="Show saved only">
          ♡ <b id="favCount">0</b>
        </button>
        <div class="vt" role="group" aria-label="View">
          <button data-v="grid" class="on" aria-label="Grid view">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <rect x="3" y="3" width="8" height="8" rx="2"></rect>
              <rect x="13" y="3" width="8" height="8" rx="2"></rect>
              <rect x="3" y="13" width="8" height="8" rx="2"></rect>
              <rect x="13" y="13" width="8" height="8" rx="2"></rect>
            </svg></button><button data-v="list" aria-label="List view">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <rect x="3" y="4" width="18" height="4" rx="1.5"></rect>
              <rect x="3" y="10" width="18" height="4" rx="1.5"></rect>
              <rect x="3" y="16" width="18" height="4" rx="1.5"></rect>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <div class="opg" id="opGrid" aria-live="polite" data-fav-key="dre-favs-rent">
      <?php foreach ($projects as $i => $p) { ?>
        <article class="oc" data-i="0" data-ty="<?= e($p['type']) ?>" data-c="<?= e($p['community']) ?>"
          data-p="<?= $p['price'] ?>" data-bn="<?= $p['beds_num'] ?>" data-bt="<?= $p['baths_num'] ?>"
          data-a="<?= $p['area'] ?>" data-ppf="<?= $p['ppf'] ?>" data-u="<?= e($p['slug']) ?>"
          data-l="<?= e($p['location']) ?>" <?= !empty($p['is_featured']) ? 'data-x="1"' : '' ?>   <?= $i >= $cards_per_page ? 'hidden=""' : '' ?>>
          <div class="oc-img" data-href="<?= e($p['url']) ?>">
            <div class="oc-slides">
              <?php foreach ($p['images'] as $img_idx => $img_url) { ?>
                <img src="<?= e($img_url) ?>"
                  alt="<?= e($p['name']) . ($p['location'] !== '' ? ', ' . e($p['location']) : '') . ' photo ' . ($img_idx + 1) ?>"
                  loading="lazy"
                  onerror="this.onerror=null; this.src='<?= BASE_URL ?>admin_nsrdwsc/assets/media/logos/logo-light.png'; this.style.objectFit='contain'; this.style.padding='20px';" />
              <?php } ?>
            </div>
            <span
              class="badge<?= !empty($p['is_featured']) ? ' exc' : '' ?>"><?= !empty($p['is_featured']) ? 'Exclusive' : 'For rent' ?></span>
            <?php if ($p['community'] !== '') { ?>
              <span class="oc-loc"><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z">
                  </path>
                </svg><?= e($p['community']) ?></span>
            <?php } ?>
            <button class="oc-nav p" aria-label="Previous photo">‹</button><button class="oc-nav n"
              aria-label="Next photo">›</button><span class="oc-dots">
              <?php foreach ($p['images'] as $img_idx => $img_url) { ?>
                <i<?= $img_idx === 0 ? ' class="on"' : '' ?>></i>
              <?php } ?>
            </span>
          </div>
          <button class="oc-fav" data-u="<?= e($p['slug']) ?>" aria-pressed="false"
            aria-label="Save <?= e($p['name']) ?>">
            ♡
          </button>
          <div class="oc-b">
            <span class="tp"><?= e($p['type']) ?></span>
            <h3>
              <a href="<?= e($p['url']) ?>"><?= e($p['name']) ?></a>
            </h3>
            <div class="specs">
              <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                  <path d="M3 18V8M21 18v-5a3 3 0 00-3-3H10v8M3 14h18M6.5 11.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                </svg><?= e($p['beds_label']) ?></span><?php if ($p['baths_num'] > 0) { ?><span><svg viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M4 12h16v3a4 4 0 01-4 4H8a4 4 0 01-4-4zM6 12V6a2 2 0 014 0M7 19l-1 2M17 19l1 2"></path>
                  </svg><?= e($p['baths_label']) ?></span><?php } ?><?php if ($p['area'] > 0) { ?><span><svg
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"></path>
                  </svg><?= e($p['area_label']) ?></span><?php } ?>
            </div>
            <div class="oc-meta">
              <div class="pr">
                <small>Annual rent</small><b><?= $p['price'] > 0 ? e($p['price_label']) : 'Price on request' ?></b>
                <?php if ($p['ppf'] > 0) { ?>
                  <div class="ppf"><?= e($p['ppf_label']) ?></div>
                <?php } ?>
              </div>
            </div>
            <div class="oc-act">
              <a class="btn btn-glass" href="<?= e($p['url']) ?>">View details →</a><a class="oc-wa"
                href="<?= e($p['whatsapp']) ?>" target="_blank" rel="noopener"
                aria-label="Enquire about <?= e($p['name']) ?> on WhatsApp"><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z">
                  </path>
                </svg></a>
            </div>
          </div>
        </article>
      <?php } ?>
    </div>
    <div class="op-empty glass" id="opEmpty" hidden="">
      <h3>No rental homes match these filters.</h3>
      <p>Try widening your budget — or tell us what you need and we’ll source it.</p>
      <div class="story-cta" style="justify-content: center">
        <button class="btn btn-glass" id="emptyClear">Clear filters</button><a class="btn btn-gold" href="#contact">Ask
          an advisor →</a>
      </div>
    </div>
    <div class="pager" id="pager" role="navigation" aria-label="Pagination"></div>
  </div>
</section>

<!-- ===== LANDLORD / MANAGEMENT CTA ===== -->
<section style="padding-top: 20px">
  <div class="wrap">
    <div class="op-cta glass acc-edge reveal">
      <div>
        <span class="eyebrow">Own a property?</span>
        <h2 class="serif">Let it with DRE — <em class="gold-text">stress-free.</em></h2>
        <p class="lead">
          From finding the right tenant to rent collection and maintenance — our property management team takes care
          of it.
        </p>
      </div>
      <div class="op-cta-b">
        <a class="btn btn-gold" href="<?= BASE_URL ?>property-management">Property management →</a>
        <a class="btn btn-glass" href="<?= BASE_URL ?>landlord-guide">Landlord guide</a>
        <a class="btn btn-glass" href="<?= BASE_URL ?>tenant-guide">Tenant guide</a>
      </div>
    </div>
  </div>
</section>

<section id="contact" style="padding-top: 20px">
  <div class="wrap">
    <div class="cta glass acc-edge reveal">
      <div>
        <span class="eyebrow">Private Consultation</span>
        <h2 class="serif">Your advisor is<br /><em class="gold-text">one call</em> away.</h2>
        <p class="lead">Toll free <?= e($footer_info['footer_contact_number'] ?? '800 37373') ?> · Hotline
          <?= e($footer_info['footer_contact_number2'] ?? '+971 52 754 3243') ?></p>
      </div>
      <form class="form" id="ctaForm" novalidate="">
        <input class="form-control" name="name" placeholder="Full name" autocomplete="name" required="" />
        <input class="form-control" id="ctaPhone" name="phone" type="tel" placeholder="Phone / WhatsApp"
          autocomplete="tel" required="" />
        <input type="hidden" name="phone_full" id="ctaPhoneFull" />
        <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
        <input class="form-control" name="message" placeholder="What are you looking to rent?" />
        <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
      </form>
    </div>
  </div>
</section>

<?php include('common/footer.php'); ?>

