<?php
require_once('db/config.php');

$data = get_brand_video_page_data();
$meta_tags = $data['meta_tags'];

$page = [
  'active' => 'about',
  'css' => ['assets/css/pages/brand-video.css'],
];

include('common/header.php');
?>

    <!-- ===== BRAND VIDEO: HERO ===== -->
    <header class="ph-hero bv-hero">
      <div
        class="ph-bg"
        style="
          background-image: url('<?= htmlspecialchars($data['hero_bg']) ?>');
        "
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= page_link('index') ?>">Home</a><span>/</span><a href="<?= page_link('about') ?>">About</a><span>/</span
          ><span aria-current="page">DRE Brand Video</span>
        </div>
        <span class="eyebrow"><?= e($data['hero_eyebrow']) ?></span>
        <h1 class="serif"><?= $data['hero_heading_html'] ?></h1>
        <p class="sub">
          <?= e($data['hero_sub']) ?>
        </p>
        <a class="btn btn-gold" href="#film">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg>
          Watch the film
        </a>
      </div>
    </header>

    <!-- ===== FILM ===== -->
    <section id="film" class="film-sec">
      <div class="wrap">
        <div class="film glass acc-edge reveal" id="film-player">
          <div class="film-frame" id="filmFrame">
            <img
              src="<?= htmlspecialchars($data['video_thumb']) ?>"
              alt="<?= e($data['heading']) ?> — DRE brand film"
            />
            <button
              class="film-play"
              id="filmPlay"
              aria-label="Play the DRE brand film"
              data-source="<?= e($data['video_source']) ?>"
              data-yt="<?= e($data['youtube_id']) ?>"
              data-video="<?= htmlspecialchars($data['local_video_url']) ?>"
              data-title="<?= e($data['heading']) ?>"
            >
              <span class="ring"></span>
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg>
            </button>
            <div class="film-cap glass on-img">
              <div><small>Brand film</small><b><?= e($data['heading']) ?></b></div>
              <?php if ($data['video_source'] === 'youtube'): ?>
              <span class="film-yt">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M23 7.2a3 3 0 00-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 001 7.2 31 31 0 00.5 12c0 1.6.2 3.2.5 4.8a3 3 0 002.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 002.1-2.1c.3-1.6.5-3.2.5-4.8s-.2-3.2-.5-4.8zM9.8 15.1V8.9l5.8 3.1z"
                  ></path>
                </svg>
                YouTube
              </span>
              <?php else: ?>
              <span class="film-yt">
                <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                  <path d="M8 5v14l11-7z"></path>
                </svg>
                Direct Video
              </span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="film-meta reveal">
          <?php if (!empty($data['youtube_url'])): ?>
          <a href="<?= htmlspecialchars($data['youtube_url']) ?>" target="_blank" rel="noopener">Watch on YouTube ↗</a>
          <?php endif; ?>
          <a href="https://www.youtube.com/@DrehomesRE" target="_blank" rel="noopener">More from @DrehomesRE ↗</a>
        </div>
      </div>
    </section>

    <!-- ===== STORY ===== -->
    <section id="brand-story" style="padding-top: 20px">
      <div class="wrap">
        <div class="bv-story">
          <div class="reveal">
            <span class="eyebrow">Our Roots</span>
            <h2 class="serif">We didn’t become a brand <em class="gold-text">overnight.</em></h2>
          </div>
          <div class="reveal">
            <?php foreach ($data['story_paras'] as $idx => $para): ?>
            <p class="lead<?= $idx === 0 ? ' drop' : '' ?>"><?= e($para) ?></p>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="pillars reveal">
          <div class="pil glass">
            <span class="n gold-text">01</span>
            <h3>Honesty</h3>
            <p>Clear, transparent guidance at every step — no surprises.</p>
          </div>
          <div class="pil glass">
            <span class="n gold-text">02</span>
            <h3>Expert advice</h3>
            <p>Deep knowledge of Dubai’s communities, developers and market.</p>
          </div>
          <div class="pil glass">
            <span class="n gold-text">03</span>
            <h3>Personalised service</h3>
            <p>No one-size-fits-all solutions — every client’s journey is their own.</p>
          </div>
        </div>

        <div class="family glass acc-edge reveal">
          <div
            class="fam-img"
            style="
              background-image: url('<?= htmlspecialchars($data['hero_bg']) ?>');
            "
          ></div>
          <div class="fam-tx">
            <span class="eyebrow">What makes DRE stand out</span>
            <h2 class="serif">Every client, treated <em class="gold-text">like family.</em></h2>
            <p class="lead">
              <?= e($data['family_para']) ?>
            </p>
            <div class="story-cta">
              <a class="btn btn-gold" href="<?= page_link('about') ?>">Read our story →</a>
              <a class="btn btn-glass" href="<?= page_link('our-team') ?>">Meet the team</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== PRIVATE CONSULTATION ===== -->
    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Private Consultation</span>
            <h2 class="serif">Your advisor is<br /><em class="gold-text">one call</em> away.</h2>
            <p class="lead">
              Toll free <?= e($footer_info['footer_contact_number'] ?? '800 37373') ?> · Hotline <?= e($footer_info['footer_contact_number2'] ?? '+971 52 754 3243') ?>
            </p>
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
            <input class="form-control" name="message" placeholder="What are you looking for?" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>

