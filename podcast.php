<?php
include('db/config.php');

// SEO Meta Tags for The DRE Podcast
$meta_tags = [
	'meta_title' => 'The DRE Podcast | DRE Homes',
	'meta_description' => 'The DRE Podcast is where real conversations happen. We bring together visionaries and experts to talk about the trends, ideas and opportunities shaping Dubai’s real estate future.',
	'meta_keyword' => 'DRE Podcast, Dubai real estate podcast, real estate conversations, Dubai property market podcast',
];

$episodes = get_podcast_episodes();
$main_ep = get_podcast_main_episode($episodes);
$behind_scenes_photos = get_podcast_behind_scenes_photos();

$page = [
	'active' => 'about',
];

include('common/header.php');
?>
    <!-- ===== PODCAST: HERO ===== -->
    <header class="ph-hero pd-hero">
      <div class="pd-glow" aria-hidden="true"></div>
      <div class="wrap pd-hg">
        <div>
          <div class="crumbs" role="navigation" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>">Home</a><span>/</span><span aria-current="page">Podcast</span>
          </div>
          <span class="eyebrow">The DRE Podcast</span>
          <h1 class="serif">Conversations shaping <em class="gold-text">Dubai’s real estate future.</em></h1>
          <p class="sub">
            The DRE Podcast is where real conversations happen. We bring together visionaries and experts to talk about
            the trends, ideas and opportunities shaping Dubai’s real estate future.
          </p>
          <div class="story-cta">
            <?php if ($main_ep): ?>
            <a class="btn btn-gold" href="#player">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg>
              Watch latest episode
            </a>
            <?php endif; ?>
            <a class="btn btn-glass" href="https://www.youtube.com/@DrehomesRE" target="_blank" rel="noopener">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M23 7.2a3 3 0 00-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 001 7.2 31 31 0 00.5 12a31 31 0 00.5 4.8 3 3 0 002.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 002.1-2.1 31 31 0 00.5-4.8 31 31 0 00-.5-4.8zM9.8 15.1V8.9l5.4 3.1z"></path>
              </svg>
              Subscribe on YouTube
            </a>
          </div>
        </div>
        <div class="pd-mic glass" aria-hidden="true">
          <span class="pd-mi">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round">
              <rect x="9" y="3" width="6" height="11" rx="3"></rect>
              <path d="M5 11a7 7 0 0014 0M12 18v3M8 21h8"></path>
            </svg>
          </span>
          <div class="pd-wave">
            <i style="--d: 0s; --h: 30%"></i><i style="--d: 0.37s; --h: 83%"></i><i style="--d: 0.74s; --h: 66%"></i>
            <i style="--d: 0.11s; --h: 49%"></i><i style="--d: 0.48s; --h: 32%"></i><i style="--d: 0.85s; --h: 85%"></i>
            <i style="--d: 0.22s; --h: 68%"></i><i style="--d: 0.59s; --h: 51%"></i><i style="--d: 0.96s; --h: 34%"></i>
            <i style="--d: 0.33s; --h: 87%"></i><i style="--d: 0.7s; --h: 70%"></i><i style="--d: 0.07s; --h: 53%"></i>
            <i style="--d: 0.44s; --h: 36%"></i><i style="--d: 0.81s; --h: 89%"></i><i style="--d: 0.18s; --h: 72%"></i>
            <i style="--d: 0.55s; --h: 55%"></i><i style="--d: 0.92s; --h: 38%"></i><i style="--d: 0.29s; --h: 91%"></i>
            <i style="--d: 0.66s; --h: 74%"></i><i style="--d: 0.03s; --h: 57%"></i><i style="--d: 0.4s; --h: 40%"></i>
            <i style="--d: 0.77s; --h: 93%"></i><i style="--d: 0.14s; --h: 76%"></i><i style="--d: 0.51s; --h: 59%"></i>
            <i style="--d: 0.88s; --h: 42%"></i><i style="--d: 0.25s; --h: 95%"></i><i style="--d: 0.62s; --h: 78%"></i>
            <i style="--d: 0.99s; --h: 61%"></i><i style="--d: 0.36s; --h: 44%"></i><i style="--d: 0.73s; --h: 97%"></i>
            <i style="--d: 0.1s; --h: 80%"></i><i style="--d: 0.47s; --h: 63%"></i><i style="--d: 0.84s; --h: 46%"></i>
            <i style="--d: 0.21s; --h: 99%"></i><i style="--d: 0.58s; --h: 82%"></i>
            <i style="--d: 0.95s; --h: 65%"></i>
          </div>
          <span class="pd-live"><i></i>On air</span>
        </div>
      </div>
    </header>

<?php if ($main_ep): ?>
    <!-- ===== PLAYER ===== -->
    <section id="player" style="padding-top: 50px">
      <div class="wrap">
        <div class="pd-stage glass reveal">
          <div class="pd-frame" id="pdFrame">
            <button class="pd-poster" type="button" id="pdPoster" aria-label="Play: <?= e($main_ep['title']) ?>">
              <img
                src="<?= e($main_ep['thumb']) ?>"
                alt="<?= e($main_ep['title']) ?>"
                id="pdPosterImg"
              /><span class="pd-big">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg>
              </span>
            </button>
          </div>
          <div class="pd-info">
            <span class="pd-tag" id="pdTag">Latest episode · <?= e($main_ep['tag']) ?></span>
            <h2 class="serif" id="pdTitle"><?= e($main_ep['title']) ?></h2>
            <p id="pdSub"><?= e($main_ep['sub']) ?></p>
            <div class="pd-ea">
              <a
                class="pd-yt"
                id="pdYt"
                href="<?= e($main_ep['youtube_link']) ?>"
                target="_blank"
                rel="noopener"
              >
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <path d="M23 7.2a3 3 0 00-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 001 7.2 31 31 0 00.5 12a31 31 0 00.5 4.8 3 3 0 002.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 002.1-2.1 31 31 0 00.5-4.8 31 31 0 00-.5-4.8zM9.8 15.1V8.9l5.4 3.1z"></path>
                </svg>
                Watch on YouTube
              </a>
              <button class="pd-yt" type="button" id="pdShare">Share episode</button>
            </div>
          </div>
        </div>
      </div>
    </section>
<?php endif; ?>

<?php if (!empty($episodes)): ?>
    <!-- ===== EPISODES ===== -->
    <section style="padding-top: 40px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Episodes</span>
            <h2 class="serif">Every <em class="gold-text">conversation.</em></h2>
          </div>
          <a class="btn btn-glass" href="https://www.youtube.com/@DrehomesRE" target="_blank" rel="noopener">
            All videos on YouTube →
          </a>
        </div>
        <div class="pd-eps reveal">
<?php foreach ($episodes as $ep): ?>
          <article
            class="pd-ep glass<?= $ep['index'] === 0 ? ' on' : '' ?>"
            data-i="<?= $ep['index'] ?>"
            data-yt="<?= e($ep['yt_id']) ?>"
            data-title="<?= e($ep['title']) ?>"
            data-sub="<?= e($ep['sub']) ?>"
            data-tag="<?= e($ep['tag']) ?>"
            <?= $ep['date'] ? 'data-date="' . e($ep['date']) . '"' : '' ?>
            <?= $ep['yt_start'] > 0 ? 'data-start="' . (int)$ep['yt_start'] . '"' : '' ?>
          >
            <button class="pd-et" type="button" data-i="<?= $ep['index'] ?>" aria-label="Play: <?= e($ep['title']) ?>">
              <img
                src="<?= e($ep['thumb']) ?>"
                alt="<?= e($ep['title']) ?>"
                loading="lazy"
              /><span class="pd-pl">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg>
              </span>
              <span class="pd-now">Now playing</span>
            </button>
            <div class="pd-eb">
              <span class="pd-tag"><?= e($ep['tag']) ?><?= $ep['date'] ? ' · ' . e($ep['date']) : '' ?></span>
              <h3><?= e($ep['title']) ?></h3>
              <?php if ($ep['sub'] !== ''): ?><p><?= e($ep['sub']) ?></p><?php endif; ?>
              <div class="pd-ea">
                <button class="btn btn-glass" type="button" data-i="<?= $ep['index'] ?>">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5v14l11-7z"></path>
                  </svg>
                  Play episode
                </button>
                <a class="pd-yt" href="<?= e($ep['youtube_link']) ?>" target="_blank" rel="noopener">
                  <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M23 7.2a3 3 0 00-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 001 7.2 31 31 0 00.5 12a31 31 0 00.5 4.8 3 3 0 002.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 002.1-2.1 31 31 0 00.5-4.8 31 31 0 00-.5-4.8zM9.8 15.1V8.9l5.4 3.1z"></path>
                  </svg>
                  Watch on YouTube
                </a>
              </div>
            </div>
          </article>
<?php endforeach; ?>
        </div>
      </div>
    </section>
<?php endif; ?>

<?php if (!empty($behind_scenes_photos)): ?>
    <!-- ===== BEHIND THE MIC ===== -->
    <section style="padding-top: 40px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Behind the mic</span>
            <h2 class="serif">On set with <em class="gold-text">the DRE Podcast.</em></h2>
          </div>
          <a class="btn btn-glass" href="<?= BASE_URL ?>our-media">Photo gallery →</a>
        </div>
        <div class="pd-bts reveal">
<?php foreach ($behind_scenes_photos as $photo): ?>
          <button
            class="pf-gi"
            type="button"
            aria-label="<?= e($photo['title']) ?>"
            data-src="<?= e($photo['src']) ?>"
            data-title="<?= e($photo['title']) ?>"
            data-lb="gallery"
          >
            <img
              src="<?= e($photo['src']) ?>"
              alt="<?= e($photo['alt']) ?>"
              loading="lazy"
            />
          </button>
<?php endforeach; ?>
        </div>
      </div>
    </section>
<?php endif; ?>

    <!-- ===== SUBSCRIBE ===== -->
    <section style="padding-top: 40px">
      <div class="wrap">
        <div class="pd-sub glass acc-edge reveal">
          <span class="pd-ytb">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M23 7.2a3 3 0 00-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 001 7.2 31 31 0 00.5 12a31 31 0 00.5 4.8 3 3 0 002.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 002.1-2.1 31 31 0 00.5-4.8 31 31 0 00-.5-4.8zM9.8 15.1V8.9l5.4 3.1z"></path>
            </svg>
          </span>
          <div>
            <span class="eyebrow">Never miss an episode</span>
            <h3 class="serif">Subscribe to DRE <em class="gold-text">on YouTube.</em></h3>
            <p>New conversations with the people shaping Dubai’s property market — straight to your feed.</p>
          </div>
          <a class="btn btn-gold" href="https://www.youtube.com/@DrehomesRE" target="_blank" rel="noopener">Subscribe →</a>
        </div>
      </div>
    </section>

    <dialog class="md-lb" id="mdLb" aria-label="Photo viewer">
      <div class="md-lb-top">
        <span id="mdLbN"></span><button type="button" class="md-x" id="mdX" aria-label="Close">✕</button>
      </div>
      <button type="button" class="md-nv md-pv" id="mdPv" aria-label="Previous photo">‹</button>
      <figure>
        <img id="mdLbImg" alt="" />
        <figcaption><b id="mdLbT">Behind the mic</b><span id="mdLbC">The DRE Podcast</span></figcaption>
      </figure>
      <button type="button" class="md-nv md-nx" id="mdNx" aria-label="Next photo">›</button>
    </dialog>

    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Be our next guest</span>
            <h2 class="serif">Have a story about<br /><em class="gold-text">Dubai real estate?</em></h2>
            <p class="lead">Toll free <?= e($footer_info['footer_contact_number'] ?? '800 37373') ?> · Hotline <?= e($footer_info['footer_contact_number2'] ?? '+971 52 754 3243') ?></p>
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
            <input class="form-control" name="message" placeholder="Tell us about yourself or your topic (optional)" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>

