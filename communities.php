<?php
require_once('db/config.php');

$data = get_communities_page_data();

// SEO & Meta tags (tbl_tags ID 12 for Communities)
$meta_tags = get_seo_tags(12) ?: [];
if (empty($meta_tags['meta_title'])) {
  $meta_tags['meta_title'] = 'Communities | DRE Real Estate | Dubai | Buy, Sell or Rent Property';
}

$page = [
  'active' => 'communities',
  'css' => ['assets/css/suggest.css', 'assets/css/pages/communities.css'],
];

// Banner image
$banner_img = !empty($data['content']['comm_cont_img'])
  ? BASE_URL_IMG . 'community/' . rawurlencode($data['content']['comm_cont_img'])
  : 'https://drehomes.com/admin_nsrdwsc/assets/media/community/comm_img_705_dre-communities.jpeg';

// Hero title formatting
$hero_heading = trim((string)($data['content']['comm_cont_heading'] ?? 'Find Community'));
if (strcasecmp($hero_heading, 'Find Community') === 0 || strcasecmp($hero_heading, 'Find your community') === 0) {
  $hero_h1 = 'Find your <em class="gold-text">community.</em>';
} else {
  $words = preg_split('/\s+/', $hero_heading);
  if (count($words) > 1) {
    $last = array_pop($words);
    $hero_h1 = e(implode(' ', $words)) . ' <em class="gold-text">' . e($last) . '.</em>';
  } else {
    $hero_h1 = e($hero_heading);
  }
}

include('common/header.php');
?>

    <!-- ===== COMMUNITIES: HERO ===== -->
    <header class="ph-hero cm-hero">
      <div
        class="ph-bg"
        style="background-image: url('<?= htmlspecialchars($banner_img) ?>');"
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= page_link('index') ?>">Home</a><span>/</span><span aria-current="page">Communities</span>
        </div>
        <span class="eyebrow">Communities</span>
        <h1 class="serif"><?= $hero_h1 ?></h1>
        <p class="sub">
          <?= (int) $data['total_count'] ?> neighbourhoods across Dubai, Abu Dhabi, the Northern Emirates and beyond — explore the lifestyle first,
          then the home.
        </p>
        <form class="op-search glass" id="cmSearch" role="search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="M20 20l-3.5-3.5"></path>
          </svg>
          <input
            id="cmQ"
            type="search"
            placeholder="Search a community — e.g. Dubai Hills, JVC, Saadiyat…"
            aria-label="Search communities"
          />
          <button class="btn btn-gold" type="submit">Search</button>
        </form>
      </div>
    </header>

    <!-- ===== LIFESTYLE COLLECTIONS ===== -->
    <?php if (!empty($data['featured'])): ?>
      <section style="padding: 60px 0 20px">
        <div class="wrap">
          <div class="head-row reveal" style="margin-bottom: 26px">
            <div>
              <span class="eyebrow">Featured communities</span>
              <h2 class="serif">Live your <em class="gold-text">best.</em></h2>
            </div>
          </div>
          <div class="lfs">
            <?php foreach ($data['featured'] as $idx => $f): ?>
              <a class="lf reveal" href="<?= htmlspecialchars($f['url']) ?>">
                <span
                  class="lf-bg"
                  <?php if (!empty($f['image'])): ?>
                  style="background-image: url('<?= htmlspecialchars($f['image']) ?>');"
                  <?php endif; ?>
                ></span>
                <span class="lf-no"><?= sprintf('%02d', $idx + 1) ?></span>
                <span class="lf-in glass on-img">
                  <small>Lifestyle collection</small>
                  <b><?= e($f['heading']) ?></b>
                  <?php if (!empty($f['desc'])): ?>
                    <p><?= e($f['desc']) ?></p>
                  <?php endif; ?>
                  <em>Explore homes →</em>
                </span>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <!-- ===== POPULAR ===== -->
    <?php if (!empty($data['popular'])): ?>
      <section style="padding-top: 40px">
        <div class="wrap">
          <div class="head-row reveal" style="margin-bottom: 26px">
            <div>
              <span class="eyebrow">Most popular</span>
              <h2 class="serif">Where Dubai <em class="gold-text">wants to live.</em></h2>
            </div>
          </div>
          <div class="pcs reveal">
            <?php foreach ($data['popular'] as $idx => $p): ?>
              <a class="pc<?= $idx === 0 ? ' big' : '' ?>" href="<?= htmlspecialchars($p['url']) ?>">
                <span
                  class="pc-bg"
                  <?php if (!empty($p['image'])): ?>
                  style="background-image: url('<?= htmlspecialchars($p['image']) ?>');"
                  <?php endif; ?>
                ></span>
                <span class="pc-in glass on-img">
                  <b><?= e($p['name']) ?></b>
                  <small><?= $p['prop_count'] > 0 ? $p['prop_count'] . ' properties · ' : '' ?><?= e($p['location']) ?></small>
                </span>
                <span class="pc-go">→</span>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <!-- ===== DIRECTORY ===== -->
    <section id="directory" style="padding-top: 40px">
      <div class="wrap">
        <div class="head-row reveal" style="margin-bottom: 22px">
          <div>
            <span class="eyebrow">All communities</span>
            <h2 class="serif">Browse <em class="gold-text">every neighbourhood</em></h2>
          </div>
        </div>
        <div class="cchips glass" id="cmReg" role="tablist" aria-label="Region">
          <button class="on" data-r="">All <b><?= (int) $data['total_count'] ?></b></button>
          <button data-r="Dubai">Dubai <b><?= (int) ($data['region_counts']['Dubai'] ?? 0) ?></b></button>
          <button data-r="Abu Dhabi">Abu Dhabi <b><?= (int) ($data['region_counts']['Abu Dhabi'] ?? 0) ?></b></button>
          <button data-r="Northern Emirates">Northern Emirates <b><?= (int) ($data['region_counts']['Northern Emirates'] ?? 0) ?></b></button>
          <button data-r="International">International <b><?= (int) ($data['region_counts']['International'] ?? 0) ?></b></button>
        </div>
        <div class="az glass" id="cmAz" role="tablist" aria-label="Filter by first letter">
          <button class="all on" data-l="">All</button>
          <?php foreach (range('A', 'Z') as $letter): ?>
            <button data-l="<?= $letter ?>"<?= !in_array($letter, $data['available_letters'], true) ? ' disabled=""' : '' ?>><?= $letter ?></button>
          <?php endforeach; ?>
        </div>
        
        <p class="dv-count" id="cmCount"></p>
        <div class="cm-grid" id="cmGrid" aria-live="polite">
          <?php foreach ($data['communities'] as $idx => $c): ?>
            <a
              class="cm"
              href="<?= htmlspecialchars($c['url']) ?>"
              data-r="<?= e($c['region']) ?>"
              data-l="<?= e($c['first_letter']) ?>"
              data-pc="<?= (int)$c['prop_count'] ?>"
              data-e="<?= e($c['location']) ?>"
              <?= $idx >= 16 ? 'hidden=""' : '' ?>
            >
              <img
                src="<?= htmlspecialchars($c['image']) ?>"
                alt="<?= e($c['name']) ?>"
                loading="lazy"
              />
              <?php if ($c['is_popular']): ?>
                <span class="cm-pop">Popular</span>
              <?php endif; ?>
              <span class="cm-in glass on-img">
                <b><?= e($c['name']) ?></b>
                <small><?= e($c['location']) ?><?= $c['prop_count'] > 0 ? ' · ' . $c['prop_count'] . ' properties' : '' ?></small>
              </span>
            </a>
          <?php endforeach; ?>
        </div>
        <div class="op-empty glass" id="cmEmpty" hidden="">
          <h3>No community found.</h3>
          <p>Try another name — or ask us, we cover many more areas.</p>
          <div class="story-cta" style="justify-content: center">
            <button class="btn btn-glass" id="cmClear">Show all</button>
            <a class="btn btn-gold" href="#contact">Ask an advisor →</a>
          </div>
        </div>
        <div style="text-align: center; margin-top: 34px">
          <button class="btn btn-glass" id="cmMore">Show more</button>
        </div>
      </div>
    </section>

    <!-- ===== AREA ADVICE CTA ===== -->
    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="op-cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Not sure where to start?</span>
            <h2 class="serif">Let us match you to <em class="gold-text">the right area.</em></h2>
            <p class="lead">
              Tell us your budget, commute and lifestyle — our advisors know every community from JVC to Saadiyat.
            </p>
          </div>
          <div class="op-cta-b">
            <a class="btn btn-gold" href="#contact">Get area advice →</a>
            <a class="btn btn-glass" href="<?= page_link('offplan-properties') ?>">Browse off-plan</a>
            <a class="btn btn-glass" href="<?= page_link('buy-property') ?>">Browse ready homes</a>
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
            <p class="lead">Toll free 800 37373 · Hotline +971 52 754 3243</p>
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

