<?php
include('db/config.php');

// SEO: Admin > Manage Tags ("Our Media")
$meta_tags = get_seo_tags(24);

$media_content = get_media_content();              // Admin > Media > Media Content (banner, headings)
$photos = get_media_photos();                      // Admin > Media > Manage Media: every published photo, newest first
$stories = get_media_stories($photos);             // runs of 7+ photos with the same caption
$category_counts = get_media_category_counts($photos);
$categories = media_categories();
$photos_per_page = 24;                             // the page's JS shows this many, then "Show more"

include('common/header.php');
?>
    <!-- ===== MEDIA: HERO ===== -->
    <header class="ph-hero mg-hero md-hero">
      <div class="ph-bg" style="background-image: url(<?= BASE_URL_IMG ?>gallery/<?= rawurlencode($media_content['media_img'] ?? '') ?>)"></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>">Home</a><span>/</span><span aria-current="page">Our Media</span>
        </div>
        <span class="eyebrow"><?= e($media_content['media_heading'] ?? 'Media buzz') ?> · <?= e($media_content['media_title'] ?? 'Our visual stories') ?></span>
        <h1 class="serif">Every frame <em class="gold-text">tells a story.</em></h1>
        <p class="sub"><?= e($media_content['media_sub_title'] ?? 'A glimpse into the world of DRE.') ?></p>
        <div class="dp-stats glass reveal">
          <div><small>Photos</small><b><?= count($photos) ?></b></div>
          <div><small>Award moments</small><b><?= $category_counts['awards'] ?></b></div>
          <div><small>Partner moments</small><b><?= $category_counts['partners'] ?></b></div>
          <div><small>Celebrations</small><b><?= $category_counts['culture'] ?></b></div>
        </div>
      </div>
    </header>

<?php if ($stories) { ?>
    <!-- ===== STORIES ===== -->
    <section style="padding-top: 60px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Highlight stories</span>
            <h2 class="serif">Moments <em class="gold-text">worth reliving.</em></h2>
          </div>
        </div>
        <div class="md-sts reveal">
<?php foreach ($stories as $story) { ?>
          <button class="md-st" type="button" data-ix="<?= implode(',', $story['keys']) ?>" aria-label="Open <?= e($story['title']) ?> story, <?= count($story['keys']) ?> photos">
            <img src="<?= e($story['cover']) ?>" alt="" loading="lazy" /><span class="md-stc glass on-img"><b><?= e($story['title']) ?></b><small><?= count($story['keys']) ?> photos</small></span
            ><span class="md-ring"></span>
          </button>
<?php } ?>
        </div>
      </div>
    </section>
<?php } ?>

    <!-- ===== WALL ===== -->
    <section id="wall" style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal" style="margin-bottom: 22px">
          <div>
            <span class="eyebrow">The archive</span>
            <h2 class="serif">Our visual <em class="gold-text">stories.</em></h2>
          </div>
        </div>
        <div class="cchips glass" id="mdCat" role="tablist" aria-label="Category">
          <button data-c="all" class="on">All photos <b><?= count($photos) ?></b></button
<?php foreach ($categories as $key => $label) { ?>
          ><button data-c="<?= $key ?>"><?= e($label) ?> <b><?= $category_counts[$key] ?></b></button
<?php } ?>
          >
        </div>
        <div class="fbar glass md-bar">
          <label class="fsearch"
            ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="11" cy="11" r="7"></circle>
              <path d="M20 20l-3.5-3.5"></path></svg
            ><input id="mdQ" type="search" placeholder="Search — e.g. Emaar, Ramadan, Qurat Ul Ain…" aria-label="Search photos" data-trending="Emaar|Damac|Award|Qurat Ul Ain|Ramadan|Nakheel"
          /></label>
          <p class="dv-count" id="mdCount"></p>
        </div>
        <div class="md-wall" id="mdWall">
<?php foreach ($photos as $photo) { ?>
          <button class="md-ph" type="button" aria-label="Open photo: <?= e($photo['caption']) ?>" data-k="<?= $photo['k'] ?>" data-c="<?= $photo['category'] ?>"<?= $photo['k'] >= $photos_per_page ? ' hidden=""' : '' ?>>
            <img src="<?= e($photo['src']) ?>" alt="<?= e($photo['caption']) ?>" loading="lazy" decoding="async" /><span><?= e($photo['caption']) ?></span>
          </button>
<?php } ?>
        </div>
        <div class="op-empty glass" id="mdEmpty" hidden="">
          <h3>No photos match.</h3>
          <p>Try another category or keyword.</p>
        </div>
        <div class="md-more"><button class="btn btn-glass" type="button" id="mdMore">Show more photos</button></div>
      </div>
    </section>

    <dialog class="md-lb" id="mdLb" aria-label="Photo viewer">
      <div class="md-lb-top">
        <span id="mdLbN"></span><button type="button" class="md-x" id="mdX" aria-label="Close">✕</button>
      </div>
      <button type="button" class="md-nv md-pv" id="mdPv" aria-label="Previous photo">‹</button>
      <figure>
        <img id="mdLbImg" alt="" />
        <figcaption><b id="mdLbT"></b><span id="mdLbC"></span></figcaption>
      </figure>
      <button type="button" class="md-nv md-nx" id="mdNx" aria-label="Next photo">›</button>
      <div class="md-strip" id="mdStrip"></div>
    </dialog>

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
