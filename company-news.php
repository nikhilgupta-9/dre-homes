<?php
include('db/config.php');

// SEO: Admin > Manage Tags ("Company News", id = 23)
$meta_tags = get_seo_tags(23);

$news_content = get_company_news_content();          // Admin > Company news > Edit Company News Content (banner, heading)
$news_items = get_company_news_items();              // Admin > Company news > Manage Company News: all active news, newest first
$spotlight = get_company_news_spotlight($news_items);// Main spotlight + side spotlight stories
$category_counts = get_company_news_category_counts($news_items);
$year_groups = get_company_news_year_groups($news_items);
$categories = company_news_categories();
$stats = get_company_news_stats($news_items, $category_counts, $year_groups);

include('common/header.php');
?>
<!-- ===== NEWS: HERO ===== -->
<header class="ph-hero mg-hero cn-hero">
  <div class="ph-bg" style="background-image: url(<?= e($news_content['banner_url'] ?? '') ?>);"></div>
  <div class="wrap">
    <div class="crumbs" role="navigation" aria-label="Breadcrumb">
      <a href="<?= BASE_URL ?>">Home</a><span>/</span><span aria-current="page">Company News</span>
    </div>
    <span class="eyebrow"><?= e($news_content['company_news_cont_inner_heading'] ?? 'Newsroom') ?></span>
    <h1 class="serif"><?= gold_last_word($news_content['company_news_cont_heading'] ?? 'Latest from DRE') ?></h1>
    <p class="sub">Awards, partnerships, leadership and life at DRE — every milestone, as it happened.</p>
    <div class="dp-stats glass reveal">
      <div><small>Stories</small><b><?= (int) $stats['total'] ?></b></div>
      <div><small>Awards &amp; recognition</small><b><?= (int) $stats['awards'] ?></b></div>
      <div><small>Newsroom since</small><b><?= (int) $stats['since_year'] ?></b></div>
      <div><small>Latest</small><b><?= e($stats['latest_date']) ?></b></div>
    </div>
  </div>
</header>

<?php if (!empty($spotlight['main'])) { ?>
  <!-- ===== SPOTLIGHT ===== -->
  <section style="padding-top: 60px">
    <div class="wrap">
      <div class="head-row reveal">
        <div>
          <span class="eyebrow">In spotlight</span>
          <h2 class="serif">Making <em class="gold-text">headlines.</em></h2>
        </div>
      </div>
      <div class="bf-grid reveal">
        <a class="bf-main" href="<?= e($spotlight['main']['url']) ?>">
          <img src="<?= e($spotlight['main']['thumb']) ?>" alt="<?= e($spotlight['main']['thumb_alt']) ?>"
            decoding="async" />
          <span class="bf-in glass on-img"><span class="btag">In spotlight</span><b
              class="serif"><?= e($spotlight['main']['heading']) ?></b>
            <?php if (!empty($spotlight['main']['excerpt'])) { ?>
              <p><?= e($spotlight['main']['excerpt']) ?></p>
            <?php } ?>
            <span class="bmeta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                <rect x="3" y="5" width="18" height="16" rx="3"></rect>
                <path d="M3 10h18M8 3v4M16 3v4"></path>
              </svg><?= e($spotlight['main']['date_formatted']) ?></span><span class="bf-go">Read the story →</span>
          </span>
        </a>
        <?php if (!empty($spotlight['side'])) { ?>
          <div class="bf-side">
            <?php foreach ($spotlight['side'] as $side_item) { ?>
              <a class="bf-s glass" href="<?= e($side_item['url']) ?>"><span class="bf-si"><img
                    src="<?= e($side_item['thumb']) ?>" alt="<?= e($side_item['thumb_alt']) ?>"
                    decoding="async" /></span><span class="bf-st"><span
                    class="btag"><?= e($side_item['category_label']) ?></span><b><?= e($side_item['heading']) ?></b><span
                    class="bmeta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                      <rect x="3" y="5" width="18" height="16" rx="3"></rect>
                      <path d="M3 10h18M8 3v4M16 3v4"></path>
                    </svg><?= e($side_item['date_formatted']) ?></span></span></a>
            <?php } ?>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>
<?php } ?>

<!-- ===== ARCHIVE ===== -->
<section id="news" style="padding-top: 40px">
  <div class="wrap">
    <div class="head-row reveal" style="margin-bottom: 22px">
      <div>
        <span class="eyebrow">The archive</span>
        <h2 class="serif">Every milestone, <em class="gold-text">year by year.</em></h2>
      </div>
    </div>
    <div class="cchips glass" id="cnCat" role="tablist" aria-label="Category">
      <button data-c="all" class="on">All news <b><?= (int) $stats['total'] ?></b></button <?php foreach ($categories as $cat_key => $cat_label) { ?>><button data-c="<?= $cat_key ?>"><?= e($cat_label) ?>
          <b><?= (int) ($category_counts[$cat_key] ?? 0) ?></b></button <?php } ?>>
    </div>
    <div class="fbar glass md-bar">
      <label class="fsearch"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="11" cy="11" r="7"></circle>
          <path d="M20 20l-3.5-3.5"></path>
        </svg><input id="cnQ" type="search" placeholder="Search particular news — e.g. Emaar, Qurat Ul Ain, award…"
          aria-label="Search news" data-trending="Emaar|Damac|Qurat Ul Ain|Sobha|Bayut|Award" /></label>
      <p class="dv-count" id="cnCount"></p>
    </div>
    <div class="cn-grid">
      <aside class="cn-years glass" aria-label="Jump to year">
        <span class="eyebrow">Years</span>
        <div id="cnYears">
          <?php foreach ($year_groups as $yr => $stories) { ?>
            <button data-y="<?= $yr ?>"><?= $yr ?><b><?= count($stories) ?></b></button>
          <?php } ?>
        </div>
        <div class="cn-join">
          <b class="serif">Got the drive?</b>
          <p>Let’s build success together at DRE!</p>
          <a class="btn btn-gold" href="<?= BASE_URL ?>careers">Join the team →</a>
        </div>
        <div class="cn-follow">
          <span class="eyebrow">Follow us</span>
          <div>
            <?php if (!empty($header_info['header_instagram_link'])) { ?>
              <a class="cn-so" href="<?= e($header_info['header_instagram_link']) ?>" target="_blank" rel="noopener"
                aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle>
                  <circle cx="17.5" cy="6.5" r=".8" fill="currentColor"></circle>
                </svg></a>
            <?php } ?>
            <?php if (!empty($header_info['header_linkedin_link'])) { ?>
              <a class="cn-so" href="<?= e($header_info['header_linkedin_link']) ?>" target="_blank" rel="noopener"
                aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                  <path
                    d="M4.98 3.5a2.5 2.5 0 11-.02 5 2.5 2.5 0 01.02-5zM3 9h4v12H3zM9 9h3.8v1.7h.1c.5-1 1.8-2 3.8-2 4 0 4.8 2.6 4.8 6V21h-4v-5.6c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9V21H9z"
                    fill="currentColor" stroke="none"></path>
                </svg></a>
            <?php } ?>
            <?php if (!empty($header_info['header_youtube_link'])) { ?>
              <a class="cn-so" href="<?= e($header_info['header_youtube_link']) ?>" target="_blank" rel="noopener"
                aria-label="YouTube"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                  <rect x="2" y="5" width="20" height="14" rx="4"></rect>
                  <path d="M10 9l5 3-5 3z" fill="currentColor"></path>
                </svg></a>
            <?php } ?>
            <?php if (!empty($header_info['header_fb_link'])) { ?>
              <a class="cn-so" href="<?= e($header_info['header_fb_link']) ?>" target="_blank" rel="noopener"
                aria-label="Facebook"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                  <path d="M14 8V6.3c0-.8.2-1.3 1.4-1.3H17V2h-2.6C11.3 2 10 3.7 10 6.4V8H8v3h2v11h4V11h2.6l.4-3z"
                    fill="currentColor" stroke="none"></path>
                </svg></a>
            <?php } ?>
            <?php if (!empty($header_info['header_spotify_link'])) { ?>
              <a class="cn-so" href="<?= e($header_info['header_spotify_link']) ?>" target="_blank" rel="noopener"
                aria-label="TikTok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                  <path d="M14 3v11.5a3.5 3.5 0 11-3.5-3.5M14 3c.5 2.6 2.3 4.4 5 4.5"></path>
                </svg></a>
            <?php } ?>
          </div>
        </div>
      </aside>
      <div class="cn-tl" id="cnTl" aria-live="polite">
        <?php foreach ($year_groups as $yr => $stories) { ?>
          <div class="cn-yr" id="y<?= $yr ?>">
            <div class="cn-yl"><b><?= $yr ?></b><small><?= count($stories) ?>
                <?= count($stories) > 1 ? 'stories' : 'story' ?></small></div>
            <div class="cn-cards">
              <?php foreach ($stories as $story) { ?>
                <a class="cn-c glass" href="<?= e($story['url']) ?>" data-c="<?= e($story['category']) ?>"
                  data-y="<?= $yr ?>"><span class="cn-ci"><img src="<?= e($story['thumb']) ?>"
                      alt="<?= e($story['thumb_alt']) ?>" loading="lazy" /><span
                      class="btag"><?= e($story['category_label']) ?></span></span><span class="cn-cb"><span
                      class="bmeta"><?= e($story['date_formatted']) ?></span>
                    <h3><?= e($story['heading']) ?></h3>
                    <p><?= e($story['excerpt']) ?></p>
                    <span class="cn-go">Read the story<i aria-hidden="true">→</i></span>
                  </span></a>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
    <div class="op-empty glass" id="cnEmpty" hidden="">
      <h3>No news matches.</h3>
      <p>Try another category or keyword.</p>
    </div>
  </div>
</section>

<!-- ===== PRIVATE CONSULTATION CTA ===== -->
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
        <input class="form-control" name="message" placeholder="What are you looking for?" />
        <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
      </form>
    </div>
  </div>
</section>

<?php include('common/footer.php'); ?>