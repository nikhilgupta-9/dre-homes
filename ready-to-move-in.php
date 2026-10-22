<?php
// 1. CONFIG
include('db/config.php');

// 2. DATA: Ready to move in properties listing (/ready-to-move-in, see .htaccess) — design: ready-to-move-in.html
// Admin > Ready To Move In (tbl_readytomovein_content, id 1; tbl_tags, id 9; tbl_proj, type 2)
$meta_tags = get_seo_tags(9);
$ready_content = get_ready_content();
$projects = get_ready_projects();
$filters = get_ready_filters($projects);

$banner_img = !empty($ready_content['readytomovein_cont_img'])
    ? BASE_URL_IMG . 'project/' . rawurlencode($ready_content['readytomovein_cont_img'])
    : BASE_URL_IMG . 'project/readytomovein_cont_img_843_ready-to-move-in-properties-dubai-DRE.jpg';
$hero_eyebrow = !empty($ready_content['readytomovein_cont_heading']) ? $ready_content['readytomovein_cont_heading'] : 'Ready To Move In · Dubai';

// 3. SEO: tbl_tags id 9 (Admin > Manage Tags > edit-tags.php?edit=OQ==)
$page = [
    'active' => 'properties',
    'title' => $meta_tags['meta_title'] ?? 'Ready to move in Properties | DRE Real Estate | Dubai | Buy, Sell or Rent Property',
    'description' => $meta_tags['meta_description'] ?? 'Ready-to-move-in apartments, villas and townhouses from Dubai’s leading developers — no waiting for handover.',
    'css' => ['assets/css/pages/ready-to-move-in.css'],
];

include('common/header.php');
?>
    <!-- ===== READY TO MOVE IN: HERO ===== -->
    <header class="ph-hero op-hero">
      <div
        class="ph-bg"
        style="background-image: url(<?= e($banner_img) ?>);"
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>">Home</a><span>/</span><a href="#">Properties</a><span>/</span
          ><span aria-current="page">Ready To Move In</span>
        </div>
        <span class="eyebrow"><?= e($hero_eyebrow) ?></span>
        <h1 class="serif">Completed homes. <em class="gold-text">Keys on transfer.</em></h1>
        <p class="sub">
          Ready-to-move-in apartments, villas and townhouses from Dubai’s leading developers — no waiting for handover.
        </p>
        <form class="op-search glass" id="opSearch" role="search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="M20 20l-3.5-3.5"></path>
          </svg>
          <input
            id="qIn"
            type="search"
            placeholder="Search a project, developer or area — e.g. Emaar, JVC…"
            aria-label="Search projects"
          />
          <button class="btn btn-gold" type="submit">Search</button>
        </form>
<?php if (!empty($filters['quick_developers'])) { ?>
        <div class="quick" id="quickDev">
<?php foreach ($filters['quick_developers'] as $qd) { ?>
          <button type="button" data-d="<?= e($qd['name']) ?>"><?= e($qd['name']) ?> <span style="opacity: 0.6"><?= (int) $qd['count'] ?></span></button>
<?php } ?>
        </div>
<?php } ?>
      </div>
    </header>

    <!-- ===== LISTING ===== -->
    <section id="listing" class="op-list">
      <div class="wrap">
        <div class="fbar glass" id="fbar">
          <div class="fb-row">
            <div class="seg" id="emSeg" role="tablist" aria-label="Property type" data-key="ty">
              <button role="tab" data-e="" class="on">All types</button
              ><button role="tab" data-e="Apartment">Apartments</button><button role="tab" data-e="Villa">Villas</button
              ><button role="tab" data-e="Townhouse">Townhouses</button>
            </div>
            <label class="fsel"
              ><span>Developer</span
              ><select id="devSel">
                <option value="">All developers</option>
<?php foreach ($filters['developers'] as $d) { ?>
                <option value="<?= e($d) ?>"><?= e($d) ?></option>
<?php } ?>
              </select></label
            >
            <label class="fsel"
              ><span>Area</span
              ><select id="locSel">
                <option value="">All areas</option>
<?php foreach ($filters['areas'] as $a) { ?>
                <option value="<?= e($a) ?>"><?= e($a) ?></option>
<?php } ?>
              </select></label
            >
            <label class="fsel"
              ><span>Min price</span
              ><select id="pMin">
                <option value="">Any</option>
<?php foreach ($filters['prices'] as $pr) { ?>
                <option value="<?= (int) $pr ?>">AED <?= number_format($pr) ?></option>
<?php } ?>
              </select></label
            >
            <label class="fsel"
              ><span>Max price</span
              ><select id="pMax">
                <option value="">Any</option>
<?php foreach ($filters['prices'] as $pr) { ?>
                <option value="<?= (int) $pr ?>">AED <?= number_format($pr) ?></option>
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
              <button type="button" data-b="STD">Studio</button><button type="button" data-b="1">1</button
              ><button type="button" data-b="2">2</button><button type="button" data-b="3">3</button
              ><button type="button" data-b="4">4</button><button type="button" data-b="5">5</button
              ><button type="button" data-b="6">6</button><button type="button" data-b="7">7</button>
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

        <div class="opg" id="opGrid" aria-live="polite" data-fav-key="dre-favs-ready">
<?php foreach ($projects as $p) { ?>
          <article
            class="oc"
            data-d="<?= e($p['developer']) ?>"
            data-l="<?= e($p['area']) ?>"
            data-p="<?= (int) $p['price'] ?>"
            data-u="<?= e($p['slug']) ?>"
            data-name="<?= e($p['name']) ?>"
            data-ty="<?= e($p['type']) ?>"
            data-bmin="<?= (int) $p['bmin'] ?>"
            data-bmax="<?= (int) $p['bmax'] ?>"
            data-blist="<?= e($p['blist']) ?>"
          >
            <a class="oc-img" href="<?= e($p['url']) ?>">
              <img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>" loading="lazy" />
              <span class="badge">Ready</span>
              <span class="oc-loc"><?= e($p['location']) ?></span>
            </a>
            <div class="oc-b">
              <span class="oc-dev"><?= e($p['developer']) ?></span>
              <h3><a href="<?= e($p['url']) ?>"><?= e($p['name']) ?></a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b><?= e($p['beds']) ?></b></div>
                <div><small>Handover</small><b>Ready</b></div>
                <div class="pr"><small>Starting price</small><b><?= e($p['price_label']) ?></b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-gold" href="<?= e($p['url']) ?>">Explore home →</a>
                <a class="ic-ic" href="tel:+971527543243" aria-label="Call about <?= e($p['name']) ?>">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2"></path>
                  </svg>
                </a>
                <a class="ic-ic wa" href="<?= e($p['whatsapp']) ?>" target="_blank" rel="noopener" aria-label="WhatsApp about <?= e($p['name']) ?>">
                  <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"></path>
                  </svg>
                </a>
              </div>
            </div>
          </article>
<?php } ?>
        </div>

        <div class="empty glass" id="opEmpty" hidden="">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5"></path></svg>
          <h3>No ready properties match those filters</h3>
          <p>Try widening your price range, choosing another developer, or clearing filters.</p>
          <button class="btn btn-gold" id="emptyReset" type="button">Reset all filters</button>
        </div>

        <nav class="pager" id="pager" aria-label="Ready properties pagination"></nav>
      </div>
    </section>

    <!-- ===== CTA STRIP ===== -->
    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="op-cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Move in today</span>
            <h2 class="serif">Find your keys <em class="gold-text">without the wait.</em></h2>
            <p class="lead">
              Our specialists walk you through inspection, title deed transfer and handover paperwork — from first viewing to move-in day.
            </p>
          </div>
          <div class="op-cta-b">
            <a class="btn btn-gold" href="#contact">Book a viewing →</a>
            <a class="btn btn-glass" href="<?= BASE_URL ?>buy-property">Browse buy properties</a>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== CONTACT ===== -->
    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Private Consultation</span>
            <h2 class="serif">Speak to a ready<br /><em class="gold-text">property specialist.</em></h2>
            <p class="lead">Toll free <?= e($toll_free) ?> · Hotline +971 52 754 3243</p>
          </div>
          <form class="form" id="ctaForm" novalidate="">
            <input class="form-control" name="name" placeholder="Full name" autocomplete="name" required="" />
            <input
              class="form-control"
              id="ctaPhone"
              name="phone"
              type="tel"
              placeholder="Phone / WhatsApp"
              autocomplete="tel"
              required=""
            />
            <input type="hidden" name="phone_full" id="ctaPhoneFull" />
            <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
            <input class="form-control" name="message" placeholder="Which property or community are you interested in?" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request call back</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>
