<?php
// 1. CONFIG
include('db/config.php');

// 2. DATA: International properties listing (/international-properties, see .htaccess) — design: international-properties.html
// Admin > International (tbl_international_content, id 1; tbl_tags, id 10; tbl_proj, type 3)
$meta_tags = get_seo_tags(10);
$intl_content = get_international_content();
$projects = get_international_projects();
$destinations = get_international_destinations($projects);

$banner_img = !empty($intl_content['intl_cont_img'])
  ? BASE_URL_IMG . 'project/' . rawurlencode($intl_content['intl_cont_img'])
  : BASE_URL_IMG . 'project/thumb/thumb_img_140_Mandarin-Oriental-img-02_3_11zon.jpg';
$hero_eyebrow = !empty($intl_content['intl_cont_heading']) ? $intl_content['intl_cont_heading'] : 'International Properties';

// 3. SEO: tbl_tags id 10 (Admin > Manage Tags > edit-tags.php?edit=MTA=)
$page = [
  'active' => 'properties',
  'title' => $meta_tags['meta_title'] ?? 'International Properties | DRE Homes',
  'description' => $meta_tags['meta_description'] ?? 'Handpicked residences from Istanbul to Muscat and the Adriatic coast — with the same trusted DRE guidance you get in Dubai.',
  'css' => ['assets/css/pages/international-properties.css'],
];

include('common/header.php');
?>
<!-- ===== INTERNATIONAL: HERO ===== -->
<header class="ph-hero intl-hero">
  <div class="ph-bg" style="background-image: url(<?= e($banner_img) ?>);"></div>
  <div class="wrap">
    <div class="crumbs" role="navigation" aria-label="Breadcrumb">
      <a href="<?= BASE_URL ?>">Home</a><span>/</span><a href="#">Properties</a><span>/</span><span
        aria-current="page">International Properties</span>
    </div>
    <span class="eyebrow"><?= e($hero_eyebrow) ?></span>
    <h1 class="serif">Own <em class="gold-text">beyond borders.</em></h1>
    <p class="sub">
      Handpicked residences from Istanbul to Muscat and the Adriatic coast — with the same trusted DRE guidance you
      get in Dubai.
    </p>
    <?php if (!empty($destinations)) { ?>
      <ul class="dest glass reveal">
        <?php foreach ($destinations as $dest) { ?>
          <li><i><?= e($dest['flag']) ?></i><b><?= e($dest['country']) ?></b><span><?= e($dest['label']) ?></span></li>
        <?php } ?>
      </ul>
    <?php } ?>
  </div>
</header>

<section id="listing" style="padding-top: 60px">
  <div class="wrap">
    <div class="head-row" style="margin-bottom: 26px">
      <div>
        <span class="eyebrow">Destinations</span>
        <h2 class="serif"><?= count($projects) ?> projects, <em class="gold-text"><?= count($destinations) ?>
            countries.</em></h2>
      </div>
    </div>
    <?php if (!empty($destinations)) { ?>
      <div class="cchips glass" id="cChips" role="tablist" aria-label="Filter by country">
        <button class="on" data-c="">All destinations <b><?= count($projects) ?></b></button>
        <?php foreach ($destinations as $dest) { ?>
          <button data-c="<?= e($dest['country']) ?>"><i><?= e($dest['flag']) ?></i><?= e($dest['country']) ?>
            <b><?= (int) $dest['count'] ?></b></button>
        <?php } ?>
      </div>
    <?php } ?>

    <div class="ilist" id="iList">
      <?php foreach ($projects as $p) { ?>
        <article class="<?= $p['is_rev'] ? 'icard rev reveal' : 'icard reveal' ?>" data-c="<?= e($p['country']) ?>">
          <a class="ic-img" href="<?= e($p['url']) ?>"><img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>"
              loading="lazy" /><span
              class="ic-flag glass on-img"><i><?= e($p['flag']) ?></i><?= e($p['country']) ?></span></a>
          <div class="ic-body glass">
            <span class="ic-no gold-text"><?= e($p['number']) ?></span>
            <span class="eyebrow"><?= e($p['location']) ?></span>
            <h3><a href="<?= e($p['url']) ?>"><?= e($p['name']) ?></a></h3>
            <?php if ($p['developer'] !== '') { ?>
              <span class="ic-dev">by <?= e($p['developer']) ?></span>
            <?php } ?>
            <div class="ic-meta">
              <div>
                <small>Bedrooms</small><span class="ic-b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.6">
                    <path d="M3 18V8M21 18v-5a3 3 0 00-3-3H10v8M3 14h18M6.5 11.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                  </svg><?= e($p['beds']) ?></span>
              </div>
              <div class="pr">
                <small>Starting price</small><?= $p['price_html'] ?>
              </div>
            </div>
            <div class="ic-act">
              <a class="btn btn-gold" href="<?= e($p['url']) ?>">Explore project →</a><a class="ic-ic"
                href="tel:+971527543243" aria-label="Call about <?= e($p['name']) ?>"><svg viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="1.7">
                  <path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2"></path>
                </svg></a><a class="ic-ic wa" href="<?= e($p['whatsapp']) ?>" target="_blank" rel="noopener"
                aria-label="WhatsApp about <?= e($p['name']) ?>"><svg viewBox="0 0 24 24" fill="currentColor">
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

<section style="padding-top: 20px">
  <div class="wrap">
    <div class="op-cta glass acc-edge reveal">
      <div>
        <span class="eyebrow">Investing abroad?</span>
        <h2 class="serif">One advisor, <em class="gold-text">every market.</em></h2>
        <p class="lead">
          Compare international projects side by side with UAE opportunities — pricing, payment plans and handover —
          and get honest advice before you commit.
        </p>
      </div>
      <div class="op-cta-b">
        <a class="btn btn-gold" href="#contact">Talk to an advisor →</a>
        <a class="btn btn-glass" href="<?= BASE_URL ?>offplan-properties">Explore UAE off-plan</a>
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
        <p class="lead">Toll free <?= e($toll_free) ?> · Hotline +971 52 754 3243</p>
      </div>
      <form class="form" id="ctaForm" novalidate="">
        <input class="form-control" name="name" placeholder="Full name" autocomplete="name" required="" />
        <input class="form-control" id="ctaPhone" name="phone" type="tel" placeholder="Phone / WhatsApp"
          autocomplete="tel" required="" />
        <input type="hidden" name="phone_full" id="ctaPhoneFull" />
        <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
        <input class="form-control" name="message" placeholder="What are you looking for?" />
        <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
      </form>
    </div>
  </div>
</section>

<?php include('common/footer.php'); ?>