<?php
require_once('db/config.php');

$data = get_developers_page_data();

// SEO & Meta tags (tbl_tags id 11 for Developers)
$meta_tags = get_seo_tags(11) ?: ($data['meta_tags'] ?? []);
if (empty($meta_tags['meta_title'])) {
  $meta_tags['meta_title'] = 'Developers | DRE Homes';
}
if (empty($meta_tags['meta_description'])) {
  $meta_tags['meta_description'] = 'From Emaar and Damac to boutique builders — explore the developers shaping the UAE and beyond, and every project DRE can help you buy.';
}

$page = [
  'active' => 'developers',
  'css' => ['assets/css/suggest.css', 'assets/css/pages/developers.css'],
];

include('common/header.php');
?>

    <!-- ===== DEVELOPERS: HERO ===== -->
    <header class="ph-hero dv-hero">
      <div
        class="ph-bg"
        style="
          background-image: url('<?= htmlspecialchars($data['hero_bg']) ?>');
          background-position: center 30%;
        "
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= page_link('index') ?>">Home</a><span>/</span><span aria-current="page">Developers</span>
        </div>
        <span class="eyebrow">Developer Partners</span>
        <h1 class="serif">
          <span class="gold-text" id="devTotal"><?= (int) $data['total_count'] ?></span> developers. <em class="gold-text">One trusted partner.</em>
        </h1>
        <p class="sub">
          From Emaar and Damac to boutique builders — explore the developers shaping the UAE and beyond, and every
          project DRE can help you buy.
        </p>
        <form class="op-search glass" id="dvSearch" role="search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="M20 20l-3.5-3.5"></path>
          </svg>
          <input
            id="dvQ"
            type="search"
            placeholder="Search a developer — e.g. Emaar, Sobha, Binghatti…"
            aria-label="Search developers"
          />
          <button class="btn btn-gold" type="submit">Search</button>
        </form>
      </div>
    </header>

    <?php if (!empty($data['exclusive_partners'])): ?>
    <!-- ===== EXCLUSIVE + TOP ===== -->
    <section style="padding: 60px 0 20px">
      <div class="wrap">
        <div class="head-row reveal" style="margin-bottom: 26px">
          <div>
            <span class="eyebrow">Strong partnerships. Iconic developments.</span>
            <h2 class="serif">DRE Exclusive <em class="gold-text">Sales Partners</em></h2>
          </div>
        </div>
        <div class="excl reveal">
          <?php foreach ($data['exclusive_partners'] as $ep): ?>
          <a class="ex2 glass" href="<?= htmlspecialchars($ep['url']) ?>">
            <div class="phw">
              <div
                class="ph"
                style="background-image: url('<?= htmlspecialchars($ep['image']) ?>')"
              ></div>
            </div>
            <div class="tx">
              <small>Exclusive Sales Partner</small>
              <?php if (!empty($ep['logo'])): ?>
              <img src="<?= htmlspecialchars($ep['logo']) ?>" alt="<?= htmlspecialchars($ep['name']) ?>" />
              <?php endif; ?>
              <h3><?= htmlspecialchars($ep['name']) ?></h3>
              <p><?= htmlspecialchars($ep['desc']) ?></p>
              <em>View developer →</em>
            </div>
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
            <span class="eyebrow">Directory</span>
            <h2 class="serif">Browse <em class="gold-text">all developers</em></h2>
          </div>
          <label class="fsel sort">
            <span>Sort</span>
            <select id="dvSort">
              <option value="feat">Featured</option>
              <option value="az">Name: A–Z</option>
              <option value="za">Name: Z–A</option>
            </select>
          </label>
        </div>

        <div class="az glass" id="azBar">
          <button class="all on" data-l="">All</button>
          <?php foreach (range('A', 'Z') as $char): ?>
          <button data-l="<?= $char ?>" <?= in_array($char, $data['letters'], true) ? '' : 'disabled' ?>><?= $char ?></button>
          <?php endforeach; ?>
        </div>

        <p class="dv-count" id="dvCount"></p>
        <div class="dv-grid" id="dvGrid" aria-live="polite">
          <?php foreach ($data['developers'] as $idx => $d): 
            $isHidden = ($idx >= 24) ? 'hidden=""' : '';
            $isTop = $d['is_top'];
            $topRankAttr = $d['top'] !== null ? 'data-top="' . (int) $d['top'] . '"' : '';
          ?>
          <a class="dv <?= $isTop ? 'top' : '' ?>" href="<?= htmlspecialchars($d['url']) ?>" <?= $topRankAttr ?> data-l="<?= htmlspecialchars($d['letter']) ?>" <?= $isHidden ?>>
            <div class="dv-logo">
              <img
                src="<?= htmlspecialchars($d['logo']) ?>"
                alt="<?= htmlspecialchars($d['name']) ?> logo"
                loading="lazy"
              />
            </div>
            <div class="dv-b">
              <h3><?= htmlspecialchars($d['name']) ?></h3>
              <?php if ($d['desc'] !== ''): ?>
              <p><?= htmlspecialchars($d['desc']) ?></p>
              <?php endif; ?>
              <em>View developer →</em>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
        <div class="op-empty glass" id="dvEmpty" hidden="">
          <h3>No developer found.</h3>
          <p>Try another name — or ask us, we work with many more.</p>
          <div class="story-cta" style="justify-content: center">
            <button class="btn btn-glass" id="dvClear">Show all</button>
            <a class="btn btn-gold" href="#contact">Ask an advisor →</a>
          </div>
        </div>
        <div style="text-align: center; margin-top: 34px">
          <button class="btn btn-glass" id="dvMore">Show more developers</button>
        </div>
      </div>
    </section>

    <!-- ===== PARTNER CTA ===== -->
    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="op-cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Are you a developer?</span>
            <h2 class="serif">Partner with <em class="gold-text">DRE.</em></h2>
            <p class="lead">
              150 dedicated agents, two Dubai offices and 19 years of relationships with buyers and investors worldwide.
              Let’s launch your next project together.
            </p>
          </div>
          <div class="op-cta-b">
            <a class="btn btn-gold" href="#contact">Start a conversation →</a>
            <a class="btn btn-glass" href="<?= page_link('offplan-properties') ?>">See off-plan launches</a>
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

