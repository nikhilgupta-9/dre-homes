<?php
// 1. CONFIG
include('db/config.php');

// 2. SEO: Admin > Manage Tags ("Off-Plan Properties", id=8, edit-tags.php?edit=OA==)
$meta_tags = get_seo_tags(8);

// 3. DATA: Admin > Manage Projects (tbl_proj, type=1, active=1) & Off Plan Content
$offplan_content = get_offplan_content();      // Admin > Off Plan > Off Plan Content
$projects = get_offplan_projects();            // Admin > Manage Projects: published off-plan projects as cards
$filters = get_offplan_filters($projects);     // developers, emirates, areas, bedrooms, prices for the filter bar
$cards_per_page = 9;                           // the page's JS shows this many per page

// Hero Background Image: Prioritize SEO sharing picture from Admin > Manage Tags (edit-tags.php?edit=OA==), fallback to Offplan Content
$hero_bg_img = '';
if (!empty($meta_tags['meta_og_img']) && is_file(__DIR__ . '/admin_nsrdwsc/assets/media/meta/' . $meta_tags['meta_og_img'])) {
    $hero_bg_img = BASE_URL_IMG . 'meta/' . rawurlencode($meta_tags['meta_og_img']);
} elseif (!empty($offplan_content['offplan_cont_img']) && is_file(__DIR__ . '/admin_nsrdwsc/assets/media/project/' . $offplan_content['offplan_cont_img'])) {
    $hero_bg_img = BASE_URL_IMG . 'project/' . rawurlencode($offplan_content['offplan_cont_img']);
}

// Hero Subtitle: from Admin > Manage Tags meta_description (edit-tags.php?edit=OA==)
$hero_subtitle = !empty($meta_tags['meta_description']) 
    ? $meta_tags['meta_description'] 
    : 'New launches from the UAE’s leading developers — with payment plans, Golden Visa eligibility and expert advice from DRE’s off-plan specialists.';

$page = [
    'active' => 'properties',
    'css' => ['assets/css/pages/offplan-properties.css'],
];

include('common/header.php');
?>
    <!-- ===== OFF-PLAN: HERO ===== -->
    <header class="ph-hero op-hero">
      <div class="ph-bg"<?= $hero_bg_img !== '' ? ' style="background-image: url(' . e($hero_bg_img) . ')"' : '' ?>></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>">Home</a><span>/</span><a href="#listing">Properties</a><span>/</span
          ><span aria-current="page">Off-Plan</span>
        </div>
        <span class="eyebrow">Off-Plan Properties · UAE</span>
        <h1 class="serif">
          <span class="gold-text"><?= number_format($filters['total']) ?></span> off-plan projects. <em class="gold-text">One trusted guide.</em>
        </h1>
        <p class="sub">
          <?= e($hero_subtitle) ?>
        </p>
        <form class="op-search glass" id="opSearch" role="search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="M20 20l-3.5-3.5"></path>
          </svg>
          <input id="qIn" type="search" placeholder="Search a project, developer or area — e.g. Emaar, Dubai Islands…" aria-label="Search projects" />
          <button class="btn btn-gold" type="submit">Search</button>
        </form>
        <div class="quick" id="quickDev">
<?php foreach ($filters['quick_developers'] as $developer) { ?>
          <button type="button" data-d="<?= e($developer['name']) ?>"><?= e($developer['short']) ?> <span style="opacity: 0.6"><?= $developer['count'] ?></span></button>
<?php } ?>
        </div>
      </div>
    </header>

    <!-- ===== LISTING ===== -->
    <section id="listing" class="op-list">
      <div class="wrap">
        <div class="fbar glass" id="fbar">
          <div class="fb-row">
            <div class="seg" id="emSeg" role="tablist" aria-label="Emirate">
              <button role="tab" data-e="" class="on">All UAE</button
<?php foreach ($filters['emirates'] as $emirate) { ?>
              ><button role="tab" data-e="<?= e($emirate) ?>"><?= e($emirate) ?></button
<?php } ?>
              >
            </div>
            <label class="fsel"
              ><span>Developer</span
              ><select id="devSel">
                <option value="">All developers</option>
<?php foreach ($filters['developers'] as $developer) { ?>
                <option><?= e($developer) ?></option>
<?php } ?>
              </select></label
            >
            <label class="fsel"
              ><span>Area</span
              ><select id="locSel">
                <option value="">All areas</option>
<?php foreach ($filters['areas'] as $area) { ?>
                <option><?= e($area) ?></option>
<?php } ?>
              </select></label
            >
            <label class="fsel"
              ><span>Min price</span
              ><select id="pMin">
                <option value="">Any</option>
<?php foreach ($filters['prices'] as $step) { ?>
                <option value="<?= $step ?>">AED <?= number_format($step) ?></option>
<?php } ?>
              </select></label
            >
            <label class="fsel"
              ><span>Max price</span
              ><select id="pMax">
                <option value="">Any</option>
<?php foreach ($filters['prices'] as $step) { ?>
                <option value="<?= $step ?>">AED <?= number_format($step) ?></option>
<?php } ?>
              </select></label
            >
            <button class="fmore" id="moreBtn" aria-expanded="false" aria-controls="moreF">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M4 6h16M7 12h10M10 18h4"></path></svg
              >Bedrooms<b id="bedCount"></b>
            </button>
          </div>
          <div class="more" id="moreF" hidden="">
            <div class="beds" id="bedChips">
<?php foreach ($filters['beds'] as $bed) { ?>
              <button type="button" data-b="<?= e($bed['value']) ?>"><?= e($bed['label']) ?></button>
<?php } ?>
            </div>
          </div>
        </div>

        <div class="rbar">
          <p id="rCount"></p>
          <div class="rbar-r">
            <button class="clear" id="clearAll" hidden="">Clear filters ×</button>
            <label class="fsel sort"
              ><span>Sort</span
              ><select id="sortSel">
                <option value="feat">Featured</option>
                <option value="pa">Price: low to high</option>
                <option value="pd">Price: high to low</option>
                <option value="az">Name: A–Z</option>
              </select></label
            >
            <button class="favbtn" id="favOnly" aria-pressed="false" title="Show saved only">♡ <b id="favCount">0</b></button>
            <div class="vt" role="group" aria-label="View">
              <button data-v="grid" class="on" aria-label="Grid view">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <rect x="3" y="3" width="8" height="8" rx="2"></rect>
                  <rect x="13" y="3" width="8" height="8" rx="2"></rect>
                  <rect x="3" y="13" width="8" height="8" rx="2"></rect>
                  <rect x="13" y="13" width="8" height="8" rx="2"></rect>
                </svg></button
              ><button data-v="list" aria-label="List view">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <rect x="3" y="4" width="18" height="4" rx="1.5"></rect>
                  <rect x="3" y="10" width="18" height="4" rx="1.5"></rect>
                  <rect x="3" y="16" width="18" height="4" rx="1.5"></rect>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div class="opg" id="opGrid" aria-live="polite">
<?php foreach ($projects as $i => $p) { ?>
          <article
            class="oc"<?= $p['emirate'] !== '' ? "\n            data-e=\"" . e($p['emirate']) . '"' : '' ?>

            data-d="<?= e($p['developer']) ?>"
            data-l="<?= e($p['area']) ?>"
            data-p="<?= $p['price'] ?>"<?= $p['beds'] ? "\n            data-bmin=\"" . $p['bed_min'] . "\"\n            data-bmax=\"" . $p['bed_max'] . '"' : '' ?><?= $p['bed_list'] !== '' ? "\n            data-blist=\"" . $p['bed_list'] . '"' : '' ?>

            data-u="<?= e($p['slug']) ?>"
            data-name="<?= e($p['name']) ?>"<?= $i >= $cards_per_page ? "\n            hidden=\"\"" : '' ?>

          >
            <a class="oc-img" href="<?= e($p['url']) ?>"
              ><img src="<?= e($p['img']) ?>" alt="<?= e($p['name']) ?>" loading="lazy" onerror="this.onerror=null; this.src='<?= BASE_URL ?>admin_nsrdwsc/assets/media/logos/logo-light.png'; this.style.objectFit='contain'; this.style.padding='20px';" /><span class="badge">Off plan</span
              ><?php if ($p['area'] !== '') { ?><span class="oc-loc"><?= svg_icon('pin') ?><?= e($p['area']) ?></span><?php } ?></a
            >
            <button class="oc-fav" data-u="<?= e($p['slug']) ?>" aria-pressed="false" aria-label="Save <?= e($p['name']) ?>">♡</button>
            <div class="oc-b">
<?php if ($p['developer'] !== '') { ?>
              <span class="oc-dev">By <?= e($p['developer']) ?></span>
<?php } ?>
              <h3><a href="<?= e($p['url']) ?>"><?= e($p['name']) ?></a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b><?= e($p['bed_label']) ?></b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><?= $p['price'] ? e($p['price_label']) : '<em>Price on request</em>' ?></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="<?= e($p['url']) ?>">View details →</a
                ><a class="oc-wa" href="<?= e($p['whatsapp']) ?>" target="_blank" rel="noopener" aria-label="Enquire about <?= e($p['name']) ?> on WhatsApp"><?= svg_icon('whatsapp') ?></a>
              </div>
            </div>
          </article>
<?php } ?>
        </div>
        <div class="op-empty glass" id="opEmpty" hidden="">
          <h3>No projects match these filters.</h3>
          <p>Try widening your price range — or let our off-plan specialists find it for you.</p>
          <div class="story-cta" style="justify-content: center">
            <button class="btn btn-glass" id="emptyClear">Clear filters</button
            ><a class="btn btn-gold" href="#contact">Ask a specialist →</a>
          </div>
        </div>
        <div class="pager" id="pager" role="navigation" aria-label="Pagination"></div>
      </div>
    </section>

    <!-- ===== WHY OFF-PLAN WITH DRE ===== -->
    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="op-cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Can’t decide?</span>
            <h2 class="serif">Get a curated <em class="gold-text">shortlist</em> in 24 hours.</h2>
            <p class="lead">
              Tell us your budget, preferred area and goals — our off-plan specialists will compare payment plans,
              handover dates and Golden Visa eligibility for you.
            </p>
          </div>
          <div class="op-cta-b">
            <a class="btn btn-gold" href="#contact">Get my shortlist →</a>
            <a class="btn btn-glass" href="<?= BASE_URL ?>#visa">Golden Visa check</a>
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
            <p class="lead">Toll free <?= e($footer_info['footer_contact_number'] ?? '800 37373') ?> · Hotline <?= e($footer_info['footer_contact_number2'] ?? '+971 52 754 3243') ?></p>
          </div>
          <form class="form" id="ctaForm" novalidate="">
            <input class="form-control" name="name" placeholder="Full name" autocomplete="name" required="" />
            <input class="form-control" id="ctaPhone" name="phone" type="tel" placeholder="Phone / WhatsApp" autocomplete="tel" required="" />
            <input type="hidden" name="phone_full" id="ctaPhoneFull" />
            <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
            <input class="form-control" name="message" placeholder="What are you looking for?" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>
