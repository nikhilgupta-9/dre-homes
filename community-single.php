<?php
require_once __DIR__ . '/db/config.php';
require_once __DIR__ . '/db/functions.php';

$slug = trim((string) ($_GET['detail'] ?? ''));
if ($slug === '') {
	$slug = 'palm-jumeirah';
}

$data = get_single_community_data($slug);
if (!$data) {
	http_response_code(404);
	if (is_file(__DIR__ . '/404.php')) {
		include __DIR__ . '/404.php';
	} else {
		echo '<h1>Community Not Found</h1>';
	}
	exit;
}

$meta_tags = $data['meta_tags'];
$comm = $data['comm'];
$projects = $data['projects'];
$dev_counts = $data['dev_counts'];
$highlights = $data['highlights'];
$nearby = $data['nearby'];
$other_communities = $data['other_communities'];
$hero_bg = $data['hero_bg'];
$loc_img = $data['loc_img'];
$map_url = $data['map_url'];
$sub = $data['sub'];

$page = [
	'active' => 'communities',
	'css' => [
		'assets/css/suggest.css',
		'assets/css/pages/community-single.css',
	],
	'js' => [
		'assets/js/suggest.js',
	],
];

include __DIR__ . '/common/header.php';
?>

    <!-- ===== COMMUNITY: HERO ===== -->
    <header class="ph-hero cd-hero">
      <div
        class="ph-bg"
        style="background-image: url(<?= htmlspecialchars($hero_bg) ?>);"
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= page_link('index') ?>">Home</a><span>/</span><a href="<?= page_link('communities') ?>">Communities</a><span>/</span><span aria-current="page"><?= htmlspecialchars($comm['comm_heading']) ?></span>
        </div>
        <span class="eyebrow">Community · <?= htmlspecialchars($comm['comm_location'] ?: 'Dubai') ?></span>
        <h1 class="serif"><?= highlight_last_word($comm['comm_heading']) ?></h1>
        <p class="sub">
          <?= htmlspecialchars($sub) ?>
        </p>
        <div class="story-cta">
          <a class="btn btn-gold" href="#homes">Explore <?= count($projects) ?> project<?= count($projects) === 1 ? '' : 's' ?> →</a>
          <a class="btn btn-glass" href="#contact">Talk to an area expert</a>
        </div>
        <div class="dp-stats glass reveal">
          <div><small>Projects</small><b><?= count($projects) ?></b></div>
          <div><small>Developers</small><b><?= count($dev_counts) ?></b></div>
          <div><small>Starting from</small><b><?= format_community_price_compact($data['min_price']) ?></b></div>
          <div><small>Location</small><b><?= htmlspecialchars($comm['comm_location'] ?: 'Dubai') ?></b></div>
        </div>
      </div>
    </header>

    <!-- ===== ABOUT ===== -->
    <?php if (!empty($comm['comm_long_descp'])): ?>
    <section id="about-c" style="padding-top: 70px">
      <div class="wrap dp-about">
        <div class="reveal">
          <span class="eyebrow">About the community</span>
          <h2 class="serif"><?= highlight_last_word('About ' . $comm['comm_heading']) ?></h2>
          <div class="dp-pills">
            <span>Villas</span><span>Townhouses</span><span>Apartments</span><span>Luxury</span><span>Beachfront</span>
          </div>
        </div>
        <div class="reveal lead-content">
          <?= preg_replace('/<p(\s+class="[^"]*")?>/i', '<p class="lead">', $comm['comm_long_descp']) ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ===== WHY LIVE ===== -->
    <?php if (!empty($comm['comm_loc_main_descp']) || !empty($highlights)): ?>
    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="dp-why glass acc-edge reveal">
          <div
            class="dw-img"
            style="background-image: url(<?= htmlspecialchars($loc_img) ?>);"
          ></div>
          <div class="dw-tx">
            <span class="eyebrow">Living in <?= htmlspecialchars($comm['comm_heading']) ?></span>
            <h2 class="serif"><?= highlight_last_word($comm['comm_loc_main_heading'] ?: ('Why live in ' . $comm['comm_heading'] . '?')) ?></h2>
            <?php if (!empty($comm['comm_loc_main_descp'])): ?>
            <div class="lead">
              <?= $comm['comm_loc_main_descp'] ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($highlights)): ?>
            <div class="dw-pts">
              <?php 
              $hl_icons = [
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 3c-1.5 3-6 5-6 10a6 6 0 0012 0c0-5-4.5-7-6-10z"></path><path d="M12 13v8"></path></svg>',
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-6h6v6"></path></svg>',
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>',
              ];
              foreach ($highlights as $idx => $hl): 
                $icon = $hl_icons[$idx % count($hl_icons)];
              ?>
              <div>
                <span class="dw-ic"><?= $icon ?></span>
                <b><?= htmlspecialchars($hl['comm_loc_heading']) ?></b>
                <p><?= strip_tags($hl['comm_loc_description']) ?></p>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ===== LOCATION ===== -->
    <?php if ($map_url): ?>
    <section id="location" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Location</span>
            <h2 class="serif"><?= highlight_last_word($comm['comm_map_heading'] ?: ("Minutes from " . ($comm['comm_location'] ?: "Dubai") . "'s icons")) ?></h2>
          </div>
        </div>
        <div class="cl-grid reveal"<?= empty($nearby) ? ' style="grid-template-columns: 1fr;"' : '' ?>>
          <div class="map-wrap glass" id="mapWrap">
            <iframe
              src="<?= htmlspecialchars($map_url) ?>"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              allowfullscreen
              title="Map"
              style="width: 100%; height: 100%; border: 0"
            ></iframe>
          </div>
          <?php if (!empty($nearby)): ?>
          <ol class="drive glass">
            <?php foreach ($nearby as $nb):
              $dt = parse_community_drive_time($nb['comm_map_distance']);
              $place = preg_replace('/^\d+\s*(mins?|minutes?)\s*(from\s*)?/i', '', $nb['comm_map_heading']);
            ?>
            <li>
              <b><?= htmlspecialchars($dt['num']) ?><?php if ($dt['unit']): ?><small><?= htmlspecialchars($dt['unit']) ?></small><?php endif; ?></b>
              <span><?= htmlspecialchars($place) ?></span>
            </li>
            <?php endforeach; ?>
          </ol>
          <?php endif; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ===== HOMES ===== -->
    <section id="homes" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal" style="margin-bottom: 22px">
          <div>
            <span class="eyebrow">Exclusive homes</span>
            <h2 class="serif"><?= count($projects) ?> project<?= count($projects) === 1 ? '' : 's' ?> in <em class="gold-text"><?= htmlspecialchars($comm['comm_heading']) ?></em></h2>
          </div>
        </div>

        <?php if (!empty($projects)): ?>
        <?php if (!empty($dev_counts)): ?>
        <div class="cchips glass" id="pmDev" role="tablist" aria-label="Developer">
          <button class="on" data-d="">All developers <b><?= count($projects) ?></b></button>
          <?php 
          $top_devs = array_slice($dev_counts, 0, 6, true);
          foreach ($top_devs as $dname => $dcount): 
          ?>
          <button data-d="<?= htmlspecialchars($dname) ?>"><?= htmlspecialchars($dname) ?> <b><?= $dcount ?></b></button>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="fbar glass" id="fbar">
          <div class="fb-row">
            <label class="fsearch">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="M20 20l-3.5-3.5"></path>
              </svg>
              <input
                id="pmQ"
                type="search"
                placeholder="Search projects or developers in <?= htmlspecialchars($comm['comm_heading']) ?>…"
                aria-label="Search <?= htmlspecialchars($comm['comm_heading']) ?> projects"
              />
            </label>
            <label class="fsel">
              <span>Developer</span>
              <select id="pmD">
                <option value="">All developers</option>
                <?php foreach ($dev_counts as $dname => $dcount): ?>
                <option value="<?= htmlspecialchars($dname) ?>"><?= htmlspecialchars($dname) ?> (<?= $dcount ?>)</option>
                <?php endforeach; ?>
              </select>
            </label>
            <label class="fsel">
              <span>Budget</span>
              <select id="pmP">
                <option value="">Any budget</option>
                <option value="0-5000000">Under AED 5M</option>
                <option value="5000000-15000000">AED 5M – 15M</option>
                <option value="15000000-1e12">AED 15M+</option>
                <option value="req">Price on request</option>
              </select>
            </label>
            <label class="fsel">
              <span>Sort</span>
              <select id="pmS">
                <option value="feat">Featured</option>
                <option value="pa">Price: low to high</option>
                <option value="pd">Price: high to low</option>
                <option value="az">Name: A–Z</option>
              </select>
            </label>
          </div>
          <div class="fb-row2">
            <div class="beds" id="pmB" aria-label="Bedrooms">
              <button type="button" data-b="0">Studio</button>
              <button type="button" data-b="1">1 bed</button>
              <button type="button" data-b="2">2 bed</button>
              <button type="button" data-b="3">3 bed</button>
              <button type="button" data-b="4">4 bed</button>
              <button type="button" data-b="5">5+ bed</button>
            </div>
            <button class="clear" id="pmClear" hidden="">Clear filters ×</button>
          </div>
        </div>
        <p class="dv-count" id="pmCount"></p>
        <div class="opg" id="pmGrid" aria-live="polite">
          <?php foreach ($projects as $prj): 
            $wa_msg = rawurlencode("Hi DRE, I'm interested in {$prj['name']} by {$prj['developer']} ({$comm['comm_heading']}).");
            $wa_link = "https://wa.me/{$prj['whatsapp']}?text={$wa_msg}";
          ?>
          <article
            class="oc"
            data-d="<?= htmlspecialchars($prj['developer']) ?>"
            data-p="<?= $prj['price_num'] ?>"
            <?php if ($prj['bmin'] !== null): ?>data-bmin="<?= $prj['bmin'] ?>"<?php endif; ?>
            <?php if ($prj['bmax'] !== null): ?>data-bmax="<?= $prj['bmax'] ?>"<?php endif; ?>
            <?php if ($prj['blist'] !== ''): ?>data-blist="<?= htmlspecialchars($prj['blist']) ?>"<?php endif; ?>
            data-name="<?= htmlspecialchars($prj['name']) ?>"
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
                <?= htmlspecialchars($comm['comm_heading']) ?>
              </span>
            </a>
            <div class="oc-b">
              <span class="oc-dev">By <?= htmlspecialchars($prj['developer']) ?></span>
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
        <div class="op-empty glass" id="pmEmpty" hidden="">
          <h3>No <?= htmlspecialchars($comm['comm_heading']) ?> projects match.</h3>
          <p>Try another developer or budget — or ask us about off-market homes.</p>
          <div class="story-cta" style="justify-content: center">
            <button class="btn btn-glass" id="pmEmptyClear">Clear filters</button>
            <a class="btn btn-gold" href="#contact">Ask an advisor →</a>
          </div>
        </div>
        <div class="pager" id="pmPager" role="navigation" aria-label="Projects pagination"></div>
        <?php else: ?>
        <div class="op-empty glass" style="margin-top: 10px;">
          <h3>Explore opportunities in <?= htmlspecialchars($comm['comm_heading']) ?></h3>
          <p>Contact our area specialists for off-market homes, upcoming launches, and personalized guidance.</p>
          <div class="story-cta" style="justify-content: center">
            <a class="btn btn-gold" href="#contact">Speak to an advisor →</a>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- ===== OTHER COMMUNITIES ===== -->
    <?php if (!empty($other_communities)): ?>
    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Explore more</span>
            <h2 class="serif">Other <em class="gold-text">communities</em></h2>
          </div>
          <a class="btn btn-glass" href="<?= page_link('communities') ?>">All communities →</a>
        </div>
        <div class="cm-grid oc5 reveal">
          <?php foreach ($other_communities as $oc): ?>
          <a class="cm" href="<?= htmlspecialchars($oc['url']) ?>">
            <img
              src="<?= htmlspecialchars($oc['image']) ?>"
              alt="<?= htmlspecialchars($oc['name']) ?>"
              loading="lazy"
            />
            <span class="cm-in glass on-img">
              <b><?= htmlspecialchars($oc['name']) ?></b>
              <small><?= htmlspecialchars($oc['location']) ?></small>
            </span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ===== PRIVATE CONSULTATION ===== -->
    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Private Consultation</span>
            <h2 class="serif">Your advisor is<br /><em class="gold-text">one call</em> away.</h2>
            <p class="lead">Toll free <?= htmlspecialchars($toll_free) ?> · Hotline +971 52 754 3243</p>
          </div>
          <form class="form" id="ctaForm" method="post" action="<?= page_link('enquiry-submit') ?>" novalidate="">
            <input type="hidden" name="source" value="Community: <?= htmlspecialchars($comm['comm_heading']) ?>" />
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

<?php include __DIR__ . '/common/footer.php'; ?>

