<?php
require_once('db/config.php');

// Slug resolution
$slug = '';
if (!empty($_GET['detail'])) {
  $slug = trim((string) $_GET['detail']);
} elseif (!empty($_GET['slug'])) {
  $slug = trim((string) $_GET['slug']);
} elseif (!empty($_GET['id'])) {
  $slug = trim((string) $_GET['id']);
} else {
  $req_uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
  if (preg_match('#/(?:developer|developers)/([^/?#]+)#', $req_uri, $matches)) {
    $slug = trim($matches[1]);
  }
}

$data = get_single_developer_data($slug);

if (!$data) {
  header('Location: ' . page_link('developers'));
  exit;
}

$dev = $data['dev'];
$meta_tags = $data['meta_tags'];

$page = [
  'active' => 'developers',
  'css' => ['assets/css/suggest.css', 'assets/css/pages/developers-single.css'],
  'js' => ['assets/js/suggest.js'],
];

// Title heading formatting with gold accent
$heading = trim((string) $dev['dev_heading']);
$words = preg_split('/\s+/', $heading);
if (count($words) > 1) {
  $last = array_pop($words);
  $hero_title = htmlspecialchars(implode(' ', $words)) . ' <em class="gold-text">' . htmlspecialchars($last) . '</em>';
} else {
  $hero_title = '<em class="gold-text">' . htmlspecialchars($heading) . '</em>';
}

// Partner status badge text
$partner_status = 'Verified Partner';
if ($dev['dev_showonhomepage'] === '1') {
  $partner_status = in_array((int)$dev['dev_order_no'], [1, 2], true) ? 'Exclusive Partner' : 'Top Partner';
}

include('common/header.php');
?>

    <!-- ===== DEVELOPER PROFILE: HERO ===== -->
    <header class="ph-hero dp-hero">
      <div
        class="ph-bg"
        style="
          background-image: url('<?= htmlspecialchars($data['hero_bg']) ?>');
          background-position: center 35%;
        "
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= page_link('index') ?>">Home</a><span>/</span><a href="<?= page_link('developers') ?>">Developers</a><span>/</span><span aria-current="page"><?= htmlspecialchars($dev['dev_heading']) ?></span>
        </div>
        <div class="dp-head">
          <?php if (!empty($data['logo'])): ?>
          <div class="dp-logo glass acc-edge">
            <img
              src="<?= htmlspecialchars($data['logo']) ?>"
              alt="<?= htmlspecialchars($dev['dev_heading']) ?> logo"
            />
          </div>
          <?php endif; ?>
          <div>
            <span class="eyebrow">Developer profile · <?= $partner_status ?></span>
            <h1 class="serif"><?= $hero_title ?></h1>
            <p class="sub"><?= htmlspecialchars($data['sub']) ?></p>
            <div class="story-cta">
              <?php if ($data['project_count'] > 1): ?>
              <a class="btn btn-gold" href="#projects">Browse <?= $data['project_count'] ?> projects →</a>
              <?php elseif ($data['project_count'] === 1): ?>
              <a class="btn btn-gold" href="#projects">View launch project →</a>
              <?php endif; ?>
              <a class="btn btn-glass" href="#contact">Talk to an advisor</a>
            </div>
          </div>
        </div>
        <div class="dp-stats glass reveal">
          <div>
            <small>Projects with DRE</small>
            <b><?= $data['project_count'] > 0 ? (int)$data['project_count'] : 'New' ?></b>
          </div>
          <div>
            <small>Communities</small>
            <b><?= max(1, $data['community_count']) ?><?= $data['community_count'] > 1 ? '+' : '' ?></b>
          </div>
          <div>
            <small>Partner Status</small>
            <b><?= $partner_status ?></b>
          </div>
          <?php if ($data['min_price'] > 0): ?>
          <div>
            <small>Starting from</small>
            <b>AED <?= number_format($data['min_price']) ?></b>
          </div>
          <?php else: ?>
          <div>
            <small>Pricing</small>
            <b>Flexible Plans</b>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <!-- ===== ABOUT ===== -->
    <section id="about-dev" style="padding-top: 70px">
      <div class="wrap dp-about">
        <div class="reveal">
          <span class="eyebrow">About <?= htmlspecialchars($dev['dev_heading']) ?></span>
          <h2 class="serif">Building the <em class="gold-text">pulse of Dubai.</em></h2>
        </div>
        <div class="reveal">
          <?php if (!empty($dev['dev_long_descp'])): 
            $clean_about = preg_replace('/style="[^"]*"/', '', (string) $dev['dev_long_descp']);
          ?>
            <div class="lead-content">
              <?= $clean_about ?>
            </div>
          <?php elseif (!empty($dev['dev_short_descp'])): ?>
            <p class="lead"><?= htmlspecialchars($dev['dev_short_descp']) ?></p>
          <?php else: ?>
            <p class="lead">
              <?= htmlspecialchars($dev['dev_heading']) ?> is one of the premier property developers shaping the urban landscape of Dubai and the UAE with innovative architecture, world-class amenities, and timeless communities.
            </p>
          <?php endif; ?>
          <div class="dp-pills" style="margin-top: 24px;">
            <span>Master-planned communities</span>
            <span>Exceptional lifestyle</span>
            <span>Prime locations</span>
            <span>Award-winning</span>
          </div>
        </div>
      </div>
    </section>

    <?php if (!empty($data['choose_us_points']) || !empty($data['choose_us_desc'])): ?>
    <!-- ===== WHY CHOOSE ===== -->
    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="dp-why glass acc-edge reveal">
          <?php if (!empty($data['choose_us_img'])): ?>
          <div
            class="dw-img"
            style="background-image: url('<?= htmlspecialchars($data['choose_us_img']) ?>');"
          ></div>
          <?php endif; ?>
          <div class="dw-tx" <?php if (empty($data['choose_us_img'])) echo 'style="grid-column: 1 / -1;"'; ?>>
            <span class="eyebrow">Why choose <?= htmlspecialchars($dev['dev_heading']) ?>?</span>
            <h2 class="serif">Considered luxury, <em class="gold-text">enduring value.</em></h2>
            <?php if (!empty($data['choose_us_desc'])): ?>
            <p class="lead"><?= htmlspecialchars($data['choose_us_desc']) ?></p>
            <?php else: ?>
            <p class="lead">
              Recognised for a considered approach to luxury development and destination creation — every project reflects a commitment to design, quality and enduring value for residents.
            </p>
            <?php endif; ?>
            <?php if (!empty($data['choose_us_points'])): ?>
            <div class="dw-pts">
              <?php foreach ($data['choose_us_points'] as $pt): ?>
              <div>
                <span class="dw-ic">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M12 3l2.6 5.4 5.9.9-4.3 4.1 1 5.9L12 16.6 6.8 19.3l1-5.9L3.5 9.3l5.9-.9z"></path>
                  </svg>
                </span>
                <b><?= htmlspecialchars($pt['choose_us_head']) ?></b>
                <p><?= htmlspecialchars($pt['choose_us_descp']) ?></p>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ===== PROJECTS SECTION ===== -->
    <?php if ($data['project_count'] > 1): ?>
    <!-- Multiple projects: Interactive Catalog with Filters -->
    <section id="projects" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal" style="margin-bottom: 26px">
          <div>
            <span class="eyebrow">Latest projects</span>
            <h2 class="serif"><?= $data['project_count'] ?> projects by <em class="gold-text"><?= htmlspecialchars($dev['dev_heading']) ?></em></h2>
          </div>
          <p class="lead" style="max-width: 420px">
            Start with a community, or search and filter every <?= htmlspecialchars($dev['dev_heading']) ?> launch DRE can help you buy.
          </p>
        </div>

        <div class="ctiles">
          <?php 
          $display_tiles = array_slice($data['ctiles'], 0, 6);
          foreach ($display_tiles as $ct): 
          ?>
          <button class="ct" data-c="<?= htmlspecialchars($ct['name']) ?>">
            <span
              class="ct-bg"
              style="background-image: url('<?= htmlspecialchars($ct['image']) ?>');"
            ></span>
            <span class="ct-in glass on-img">
              <b><?= htmlspecialchars($ct['name']) ?></b>
              <small><?= $ct['count'] ?> project<?= $ct['count'] === 1 ? '' : 's' ?></small>
            </span>
          </button>
          <?php endforeach; ?>
          <button class="ct ct-all" data-c="">
            <span class="ct-in glass">
              <b>All communities</b>
              <small><?= $data['project_count'] ?> projects</small>
            </span>
          </button>
        </div>

        <div class="fbar glass" id="fbar">
          <div class="fb-row">
            <label class="fsearch">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="M20 20l-3.5-3.5"></path>
              </svg>
              <input
                id="pjQ"
                type="search"
                placeholder="Search <?= $data['project_count'] ?> <?= htmlspecialchars($dev['dev_heading']) ?> projects…"
                aria-label="Search <?= htmlspecialchars($dev['dev_heading']) ?> projects"
              />
            </label>
            <label class="fsel">
              <span>Community</span>
              <select id="pjC">
                <option value="">All communities</option>
                <?php foreach ($data['comm_counts'] as $cname => $ccount): ?>
                <option value="<?= htmlspecialchars($cname) ?>"><?= htmlspecialchars($cname) ?> (<?= $ccount ?>)</option>
                <?php endforeach; ?>
              </select>
            </label>
            <label class="fsel">
              <span>Budget</span>
              <select id="pjP">
                <option value="">Any budget</option>
                <option value="0-1000000">Under AED 1M</option>
                <option value="1000000-2000000">AED 1M – 2M</option>
                <option value="2000000-5000000">AED 2M – 5M</option>
                <option value="5000000-1e12">AED 5M+</option>
                <option value="req">Price on request</option>
              </select>
            </label>
            <label class="fsel">
              <span>Sort</span>
              <select id="pjS">
                <option value="feat">Latest</option>
                <option value="pa">Price: low to high</option>
                <option value="pd">Price: high to low</option>
                <option value="az">Name: A–Z</option>
              </select>
            </label>
          </div>
          <div class="fb-row2">
            <div class="beds" id="pjB" aria-label="Bedrooms">
              <button type="button" data-b="0">Studio</button>
              <button type="button" data-b="1">1 bed</button>
              <button type="button" data-b="2">2 bed</button>
              <button type="button" data-b="3">3 bed</button>
              <button type="button" data-b="4">4 bed</button>
              <button type="button" data-b="5">5+ bed</button>
            </div>
            <div class="vt" role="group" aria-label="View">
              <button data-v="grid" class="on" aria-label="Grid view">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <rect x="3" y="3" width="8" height="8" rx="2"></rect>
                  <rect x="13" y="3" width="8" height="8" rx="2"></rect>
                  <rect x="3" y="13" width="8" height="8" rx="2"></rect>
                  <rect x="13" y="13" width="8" height="8" rx="2"></rect>
                </svg>
              </button>
              <button data-v="list" aria-label="List view">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <rect x="3" y="4" width="18" height="4" rx="1.5"></rect>
                  <rect x="3" y="10" width="18" height="4" rx="1.5"></rect>
                  <rect x="3" y="16" width="18" height="4" rx="1.5"></rect>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div class="rbar">
          <p id="pjCount"></p>
          <button class="clear" id="pjClear" hidden="">Clear filters ×</button>
        </div>

        <div class="opg" id="pjGrid" aria-live="polite">
          <?php foreach ($data['projects'] as $prj): 
            $wa_msg = rawurlencode("Hi DRE, I'm interested in {$prj['name']} by {$dev['dev_heading']} ({$prj['community']}).");
            $wa_link = "https://wa.me/{$prj['whatsapp']}?text={$wa_msg}";
          ?>
          <article
            class="oc"
            data-c="<?= htmlspecialchars($prj['community']) ?>"
            data-p="<?= $prj['price_num'] ?>"
            <?php if ($prj['bmin'] !== null): ?>data-bmin="<?= $prj['bmin'] ?>"<?php endif; ?>
            <?php if ($prj['bmax'] !== null): ?>data-bmax="<?= $prj['bmax'] ?>"<?php endif; ?>
            <?php if ($prj['blist'] !== ''): ?>data-blist="<?= htmlspecialchars($prj['blist']) ?>"<?php endif; ?>
            data-name="<?= htmlspecialchars($prj['name']) ?>"
            data-l="<?= htmlspecialchars($prj['location']) ?>"
          >
            <a class="oc-img" href="<?= htmlspecialchars($prj['url']) ?>">
              <img
                src="<?= htmlspecialchars($prj['image']) ?>"
                alt="<?= htmlspecialchars($prj['name']) ?>"
                loading="lazy"
              />
              <span class="badge"><?= htmlspecialchars($prj['status']) ?></span>
              <span class="oc-loc">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path>
                </svg>
                <?= htmlspecialchars($prj['community']) ?>
              </span>
            </a>
            <div class="oc-b">
              <span class="oc-dev">By <?= htmlspecialchars($dev['dev_heading']) ?></span>
              <h3><a href="<?= htmlspecialchars($prj['url']) ?>"><?= htmlspecialchars($prj['name']) ?></a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b><?= htmlspecialchars($prj['bedroom_display']) ?></b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><?= $prj['price_display'] ?></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="<?= htmlspecialchars($prj['url']) ?>">View project →</a>
                <a
                  class="oc-wa"
                  href="<?= htmlspecialchars($wa_link) ?>"
                  target="_blank"
                  rel="noopener"
                  aria-label="WhatsApp about <?= htmlspecialchars($prj['name']) ?>"
                >
                  <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path>
                  </svg>
                </a>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
        <div class="op-empty glass" id="pjEmpty" hidden="">
          <h3>No <?= htmlspecialchars($dev['dev_heading']) ?> projects match.</h3>
          <p>Try another community or budget — or ask us about upcoming launches.</p>
          <div class="story-cta" style="justify-content: center">
            <button class="btn btn-glass" id="pjEmptyClear">Clear filters</button>
            <a class="btn btn-gold" href="#contact">Ask an advisor →</a>
          </div>
        </div>
        <div class="pager" id="pjPager" role="navigation" aria-label="Projects pagination"></div>
      </div>
    </section>

    <?php elseif ($data['project_count'] === 1): ?>
    <!-- Single Launch Showcase Card (matches developer-ora-developers.html) -->
    <?php 
      $single_prj = $data['projects'][0]; 
      $wa_msg = rawurlencode("Hi DRE, I'm interested in {$single_prj['name']} by {$dev['dev_heading']}. " . $single_prj['url']);
      $wa_link = "https://wa.me/{$single_prj['whatsapp']}?text={$wa_msg}";
    ?>
    <section id="projects" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Latest projects</span>
            <h2 class="serif">Latest by <em class="gold-text"><?= htmlspecialchars($dev['dev_heading']) ?></em></h2>
          </div>
          <a class="btn btn-glass" href="<?= page_link('offplan-properties') ?>">All off-plan projects →</a>
        </div>
        <article class="dp-proj glass reveal">
          <a class="dpp-img" href="<?= htmlspecialchars($single_prj['url']) ?>">
            <img
              src="<?= htmlspecialchars($single_prj['image']) ?>"
              alt="<?= htmlspecialchars($single_prj['name']) ?>"
              loading="lazy"
            />
            <span class="badge"><?= htmlspecialchars($single_prj['status']) ?></span>
          </a>
          <div class="dpp-b">
            <span class="eyebrow"><?= htmlspecialchars($single_prj['location']) ?></span>
            <h3>
              <a href="<?= htmlspecialchars($single_prj['url']) ?>"><?= htmlspecialchars($single_prj['name']) ?></a>
            </h3>
            <p class="lead"><?= htmlspecialchars($single_prj['overview']) ?></p>
            <div class="dpp-meta">
              <div><small>Bedrooms</small><b><?= htmlspecialchars($single_prj['bedroom_display']) ?></b></div>
              <div><small>Starting price</small><b class="em"><?= $single_prj['price_display'] ?></b></div>
              <div><small>Developer</small><b><?= htmlspecialchars($dev['dev_heading']) ?></b></div>
            </div>
            <div class="dpp-act">
              <a class="btn btn-gold" href="<?= htmlspecialchars($single_prj['url']) ?>">View project →</a>
              <a
                class="btn btn-glass dpp-wa"
                href="<?= htmlspecialchars($wa_link) ?>"
                target="_blank"
                rel="noopener"
              >
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"></path>
                </svg>
                WhatsApp
              </a>
            </div>
          </div>
        </article>
      </div>
    </section>

    <?php else: ?>
    <!-- Fallback when developer has 0 active projects listed -->
    <section id="projects" style="padding-top: 20px">
      <div class="wrap">
        <div class="op-empty glass" style="display: block; text-align: center; padding: 48px 24px; border-radius: 24px;">
          <span class="eyebrow">Exclusive Access</span>
          <h2 class="serif" style="margin-top: 8px;">Upcoming launches by <em class="gold-text"><?= htmlspecialchars($dev['dev_heading']) ?></em></h2>
          <p class="lead" style="max-width: 600px; margin: 14px auto 26px;">
            Our advisors have direct developer access to off-market inventory, private pre-launches, and upcoming phases. Contact us to get priority notification.
          </p>
          <div class="story-cta" style="justify-content: center">
            <a class="btn btn-gold" href="#contact">Inquire about <?= htmlspecialchars($dev['dev_heading']) ?> launches →</a>
            <a class="btn btn-glass" href="<?= page_link('developers') ?>">Browse all developers</a>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ===== OTHER DEVELOPERS ===== -->
    <?php if (!empty($data['other_developers'])): ?>
    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Explore more</span>
            <h2 class="serif">Other <em class="gold-text">developers</em></h2>
          </div>
          <a class="btn btn-glass" href="<?= page_link('developers') ?>">All <?= $data['total_developers_count'] ?> developers →</a>
        </div>
        <div class="od-grid reveal">
          <?php foreach ($data['other_developers'] as $od): ?>
          <a class="od glass" href="<?= htmlspecialchars($od['url']) ?>" title="<?= htmlspecialchars($od['name']) ?>">
            <span class="od-logo">
              <img
                src="<?= htmlspecialchars($od['logo']) ?>"
                alt="<?= htmlspecialchars($od['name']) ?> logo"
                loading="lazy"
              />
            </span>
            <b><?= htmlspecialchars($od['name']) ?></b>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

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
