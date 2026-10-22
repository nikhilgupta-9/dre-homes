<?php
include('db/config.php');
$meta_tags = select_row("tbl_tags", "id=7 and is_active=1");

// Hero media (desktop row decides video or image)
$hero = ['desktop' => null, 'tablet' => null, 'mobile' => null];
foreach (select_all('tbl_hero_section', 'is_active=1 ORDER BY display_order') as $row) {
  $hero[$row['img_screen_size']] = $row;
}

// Hot property launches, with their community and emirate
$home_projects = [];
$res = mysqli_query($conn, "SELECT p.*, c.comm_location FROM tbl_proj p
  LEFT JOIN tbl_community c ON c.comm_id = p.prj_comm_id
  WHERE p.proj_showonhomepage=1 AND p.is_active=1
  ORDER BY p.home_card_order_no IS NULL, p.home_card_order_no ASC");
while ($res && $row = mysqli_fetch_assoc($res)) {
  $home_projects[] = $row;
}
foreach ($home_projects as &$p) {
  $p['card_name'] = !empty($p['proj_name_on_card']) ? $p['proj_name_on_card'] : $p['proj_name'];
  $p['card_city'] = !empty($p['emirate']) ? $p['emirate'] : (!empty($p['comm_location']) ? $p['comm_location'] : 'Dubai');
  $loc = trim($p['proj_location']);
  $p['card_loc'] = ($loc === '' ? $p['card_city'] : (stripos($loc, $p['card_city']) === false ? $loc . ', ' . $p['card_city'] : $loc));
  $p['card_price'] = (is_numeric($p['proj_price']) && $p['proj_price'] != 0) ? 'AED ' . number_format($p['proj_price']) : 'On request';
  $p['card_handover'] = trim(preg_replace('/^\s*handover\s*:?\s*/i', '', (string) $p['proj_handover']));
  if ($p['card_handover'] === '') $p['card_handover'] = '—';
  $p['card_img'] = !empty($p['home_card_img'])
    ? BASE_URL_IMG . 'project/home-card-img/' . $p['home_card_img']
    : BASE_URL_IMG . 'project/thumb/' . explode(',', $p['prj_thumb_img'])[0];
}
unset($p);
$hero_project = $home_projects[0] ?? null;

// Signature neighbourhoods: the grid holds 5 tiles, the first one is tall
$home_comms = select_all('tbl_community', 'comm_showonhomepage=1 AND is_active=1');
foreach ($home_comms as &$c) {
  $c['proj_count'] = count(select_all('tbl_proj', 'is_active=1 AND prj_comm_id=' . (int) $c['comm_id']));
}
unset($c);
usort($home_comms, fn($a, $b) => $b['proj_count'] <=> $a['proj_count']);
$home_comms = array_slice($home_comms, 0, 5);

$home_counters = select_all('tbl_abt_counter', 'is_active=1 ORDER BY abt_countr_id DESC');
$home_devs = select_all('tbl_developer', 'dev_showonhomepage=1 AND is_active=1 ORDER BY dev_order_no ASC');
$insta_items = select_all('tbl_instagram_content', 'is_active=1 ORDER BY display_order ASC');

$page = [
  'active' => 'home',
  'title' => 'DRE Homes Real Estate | Dubai Property Brokerage',
  'description' => 'Curated off-plan launches, signature resales and premium residences — guided by RERA-certified advisors.',
  'css' => ['assets/css/suggest.css'],
  'js' => ['assets/js/suggest.js'],
];
include('common/header.php');
?>
    <header class="hero" id="hero" data-bg="video">
      <div class="hero-bg" aria-hidden="true">
        <img class="hm" src="https://drehomes.com/images/home-sec/offplan-property.jpg" alt="" />
        <!-- HERO VIDEO: change the src below to use a different video (MP4, ideally < 8 MB, 1920px wide) -->
<?php if (!empty($hero['desktop']) && $hero['desktop']['media_type'] == 'video') { ?>
        <!-- HERO VIDEO: managed from the admin (Hero Section) -->
        <video
          class="hm"
          id="heroVideo"
          muted=""
          loop=""
          playsinline=""
          webkit-playsinline=""
          preload="metadata"
          poster="https://drehomes.com/images/home-sec/offplan-property.jpg"
        >
          <source src="<?= BASE_URL_IMG . e($hero['desktop']['media_path']) ?>" type="video/mp4" />
        </video>
<?php } ?>
      </div>
      <button class="vid-ctl" id="vidCtl" aria-label="Pause background video"></button>
      <div class="wrap hero-grid">
        <div>
          <span class="eyebrow">Dubai · Abu Dhabi · Sharjah</span>
          <h1 class="serif">Find your <em class="gold-text">DREam</em><br />address in Dubai.</h1>
          <p class="sub">
            Curated off-plan launches, signature resales and premium residences — guided by RERA-certified advisors.
          </p>
          <div class="search glass">
            <div class="tabs"><button class="on">Off Plan</button><button>Buy</button><button>Rent</button></div>
            <div class="fields">
              <div class="field">
                <label>Location</label
                ><input
                  id="heroLoc"
                  placeholder="Palm Jumeirah, Dubai Hills…"
                  aria-label="Location"
                  data-suggest="heroLocList"
                  data-pop-title="Popular areas"
                  data-min-w="360"
                  data-per="8"
                  data-max="8"
                /><datalist id="heroLocList">
                  <option value="Palm Jumeirah">Waterfront icon</option>
                  <option value="Downtown Dubai">Burj Khalifa &amp; Dubai Mall</option>
                  <option value="Dubai Hills Estate">Golf-front green community</option>
                  <option value="Business Bay">Canal-side skyline living</option>
                  <option value="Emaar Beachfront">Sea views &amp; private beach</option>
                  <option value="Jumeirah Village Circle">Family-friendly &amp; great value</option>
                  <option value="Dubai Creek Harbour">Creek-side towers</option>
                  <option value="Town Square">Nshama family community</option>
                  <option value="Damac Hills 2">Resort-style living</option>
                  <option value="Damac Lagoons">Lagoon villas</option>
                  <option value="Dubai Islands">New waterfront destination</option>
                  <option value="Dubai Marina">Marina &amp; JBR living</option>
                  <option value="Arabian Ranches">Villa community</option>
                  <option value="Emaar South">Golf community near Expo</option>
                  <option value="The Valley">Townhouse community</option>
                  <option value="Dubai Science Park">Affordable apartments</option>
                  <option value="Saadiyat Island">Abu Dhabi culture &amp; beaches</option>
                  <option value="Yas Island">Abu Dhabi leisure hub</option>
                </datalist>
              </div>
              <div class="field">
                <label>Residence</label
                ><select>
                  <option>Any type</option>
                  <option>Apartment</option>
                  <option>Villa</option>
                  <option>Penthouse</option>
                  <option>Mansion</option>
                </select>
              </div>
              <div class="field">
                <label>Budget</label
                ><select>
                  <option>Any budget</option>
                  <option>AED 1M – 3M</option>
                  <option>AED 3M – 10M</option>
                  <option>AED 10M – 30M</option>
                  <option>AED 30M+</option>
                </select>
              </div>
              <button class="go">Search <span>→</span></button>
            </div>
          </div>
          <div class="hero-stats glass">
            <div><strong class="gold-text">2,400+</strong>Live listings</div>
            <div><strong class="gold-text">650+</strong>Off-plan projects</div>
            <div><strong class="gold-text">4.9★</strong>Google rating</div>
          </div>
        </div>
        <div style="position: relative">
<?php if ($hero_project) { ?>
          <div class="feature glass acc-edge">
            <div class="ph" style="background-image: url(<?= e($hero_project['card_img']) ?>)"><span class="badge">Featured · Off plan</span></div>
            <div class="info">
              <div>
                <h3 class="serif"><?= e($hero_project['card_name']) ?></h3>
                <small><?= e($hero_project['card_loc']) ?></small>
              </div>
              <div class="price"><span>From</span><b class="gold-text"><?= e($hero_project['card_price']) ?></b></div>
            </div>
          </div>
          <div class="mini-stat glass"><strong class="gold-text"><?= e($hero_project['card_handover']) ?></strong>Handover</div>
<?php } ?>
        </div>
      </div>
    </header>

    <section id="props">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">The Collection</span>
            <h2 class="serif">Hot property <em class="gold-text">launches</em></h2>
            <p class="lead">The newest and most sought-after projects, fresh on the UAE market.</p>
          </div>
          <div class="chips glass">
            <button class="chip on">All</button><button class="chip">Dubai</button
            ><button class="chip">Abu Dhabi</button><button class="chip">Sharjah</button>
          </div>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3" id="cards">
<?php foreach ($home_projects as $p) { ?>
          <div class="col" data-c="<?= e($p['card_city']) ?>">
            <article class="pcard">
              <div class="bg" style="background-image: url(<?= e($p['card_img']) ?>)"></div>
              <span class="badge">Off plan</span><span class="fav">♡</span>
              <div class="pinfo glass on-img">
                <h3><?= e($p['card_name']) ?></h3>
                <p class="loc"><?= e($p['card_loc']) ?></p>
                <div class="prow">
                  <div>Starting from<strong><?= e($p['card_price']) ?></strong></div>
                  <div style="text-align: right">Handover<strong><?= e($p['card_handover']) ?></strong></div>
                </div>
              </div>
            </article>
          </div>
<?php } ?>
        </div>
        <div style="text-align: center; margin-top: 56px">
          <a class="btn btn-glass" href="<?= page_link('offplan-properties') ?>">View the full collection →</a>
        </div>
      </div>
    </section>

    <section id="areas" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Signature Neighborhoods</span>
            <h2 class="serif">Addresses that <em class="gold-text">define</em> you</h2>
          </div>
          <a class="btn btn-glass" href="<?= page_link('communities') ?>">All communities →</a>
        </div>
        <div class="areas reveal">
<?php foreach ($home_comms as $i => $c) {
  $comm_img = !empty($c['comm_home_img'])
    ? BASE_URL_IMG . 'community/home/' . $c['comm_home_img']
    : BASE_URL_IMG . 'community/thumb/' . $c['comm_thumb_img']; ?>
          <a class="area<?= $i === 0 ? ' tall' : '' ?>" href="<?= BASE_URL ?>community/<?= e($c['comm_slug']) ?>"
            ><div class="bg" style="background-image: url(<?= e($comm_img) ?>)"></div>
            <div class="lbl glass on-img">
              <div>
                <h3><?= e($c['comm_heading']) ?></h3>
                <p><?= (int) $c['proj_count'] ?> residences</p>
              </div>
              <span class="arrow">→</span>
            </div></a
          >
<?php } ?>
        </div>
      </div>
    </section>

    <section id="paths" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Your Journey</span>
            <h2 class="serif">Three ways <em class="gold-text">in</em></h2>
          </div>
        </div>
        <div class="paths reveal">
          <div class="path glass">
            <span class="num gold-text">i.</span>
            <h3>Off-plan launches</h3>
            <p>Priority access to new projects, with flexible developer payment plans.</p>
            <a href="<?= page_link('offplan-properties') ?>">Explore launches →</a>
          </div>
          <div class="path glass">
            <span class="num gold-text">ii.</span>
            <h3>Signature resales</h3>
            <p>Handpicked ready homes with verified pricing and full DLD transfer support.</p>
            <a href="<?= page_link('buy-property') ?>">View resales →</a>
          </div>
          <div class="path glass">
            <span class="num gold-text">iii.</span>
            <h3>Premium rentals</h3>
            <p>In-demand residences across prime communities, matched to your lifestyle.</p>
            <a href="#">Find a rental →</a>
          </div>
        </div>
      </div>
    </section>

    <section id="visa" style="padding-top: 20px">
      <div class="wrap">
        <div class="visa-box glass acc-edge reveal">
          <div>
            <span class="eyebrow">Golden Visa</span>
            <h2 class="serif">Own a home.<br />Earn a <em class="gold-text">decade</em> in the UAE.</h2>
            <p class="lead">
              Property investments of AED 2M and above can qualify you and your family for 10-year residency. We manage
              it end to end.
            </p>
            <ul class="checks">
              <li>10-year renewable residency for your family</li>
              <li>Off-plan &amp; mortgaged properties may qualify</li>
              <li>No local sponsor required</li>
            </ul>
            <div style="display: flex; gap: 10px; flex-wrap: wrap">
              <a class="btn btn-gold" href="#contact">Free Golden Visa consultation →</a
              ><a class="btn btn-glass" href="<?= page_link('golden-visa') ?>">Learn more</a>
            </div>
          </div>
          <div class="calc glass">
            <h4>Eligibility check</h4>
            <small>Slide to your planned investment</small>
            <div class="val gold-text" id="bval">AED 2,500,000</div>
            <input type="range" id="budget" min="500000" max="10000000" step="50000" value="2500000" />
            <div class="scale"><span>AED 0.5M</span><span>AED 10M+</span></div>
            <div class="result" id="bres"></div>
          </div>
        </div>
      </div>
    </section>

    <section id="about">
      <div class="wrap about">
        <div class="about-img reveal">
          <a class="play glass on-img" href="<?= page_link('about') ?>"><i>→</i>More about DRE</a>
        </div>
        <div class="reveal">
          <span class="eyebrow">About DRE</span>
          <h2 class="serif">Built on trust.<br />Defined by <em class="gold-text">excellence</em>.</h2>
          <p class="lead">
            From our headquarters in Dubai Hills Estate and our Town Square branch, we connect buyers, investors and
            tenants with the UAE's finest properties.
          </p>
          <div class="stats">
<?php foreach ($home_counters as $row) { ?>
            <div class="stat glass">
              <strong class="gold-text" data-n="<?= (int) $row['abt_countr_counter'] ?>">0</strong><span><?= e($row['abt_countr_heading']) ?></span>
            </div>
<?php } ?>
          </div>
        </div>
      </div>
    </section>

    <section id="vision" style="padding-top: 20px">
      <div class="wrap duo">
        <article class="dcard reveal">
          <div class="bg" style="background-image: url(https://drehomes.com/images/qurat-and-mudi.jpg)"></div>
          <div class="dc-in glass on-img">
            <span class="eyebrow">Meet the vision behind DRE</span>
            <h3>Built on relationships, <em class="gold-text">driven by results.</em></h3>
            <p>
              Founded on trust and expertise, DRE’s founders are committed to helping clients make confident real estate
              decisions through honest guidance and deep market knowledge.
            </p>
            <a class="btn btn-gold" href="<?= page_link('brand-video') ?>"><i>▶</i>&nbsp; Watch our story</a>
          </div>
        </article>
        <article class="dcard reveal">
          <div class="bg" style="background-image: url(https://drehomes.com/images/town-square-office.jpg)"></div>
          <div class="dc-in glass on-img">
            <span class="eyebrow">Town Square Branch</span>
            <h3>Making property decisions <em class="gold-text">easier across Dubai.</em></h3>
            <p>
              With a strong understanding of the Town Square community, our team is here to connect you with
              opportunities that truly match your needs.
            </p>
            <a class="btn btn-gold" href="<?= page_link('town-square-team') ?>">Connect with our team →</a>
          </div>
        </article>
      </div>
    </section>

    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Beyond the Sale</span>
            <h2 class="serif">A complete <em class="gold-text">concierge</em></h2>
          </div>
          <a class="btn btn-glass" href="#">All services →</a>
        </div>
        <div class="svc-grid reveal">
          <div class="svc glass">
            <div class="ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                <path d="M3 11l9-7 9 7v9H3z"></path>
                <path d="M9 20v-6h6v6"></path>
              </svg>
            </div>
            <h3>Property management</h3>
            <p>Tenants, maintenance, rent collection and reporting — handled.</p>
          </div>
          <div class="svc glass">
            <div class="ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                <path d="M3 17l6-6 4 4 8-8"></path>
                <path d="M15 7h6v6"></path>
              </svg>
            </div>
            <h3>Investment advisory</h3>
            <p>Yield analysis and market insight before you commit.</p>
          </div>
          <div class="svc glass">
            <div class="ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                <path d="M3 10l9-6 9 6"></path>
                <path d="M5 10v8M10 10v8M14 10v8M19 10v8M3 20h18"></path>
              </svg>
            </div>
            <h3>Mortgage support</h3>
            <p>Pre-approvals and preferred rates from leading UAE banks.</p>
          </div>
          <div class="svc glass">
            <div class="ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                <circle cx="12" cy="10" r="3"></circle>
                <path d="M8.5 17h7"></path>
              </svg>
            </div>
            <h3>Golden Visa</h3>
            <p>Document preparation and submission, start to finish.</p>
          </div>
        </div>
      </div>
    </section>

    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="reviews reveal">
          <div class="rv-intro glass acc-edge">
            <span class="eyebrow">Words That Build Trust</span>
            <h2 class="serif">Our clients, <em class="gold-text">in their words</em></h2>
            <p class="lead">We take pride in creating smooth and rewarding real estate experiences across Dubai.</p>
            <!-- Trustindex rating badge (same as live site) -->
            <div class="rv-badge">
              <script defer="" async="" src="https://cdn.trustindex.io/loader.js?5e862a8729ae637df016bbd0ae2"></script>
            </div>
          </div>
          <!-- Trustindex Google reviews widget (same as live site) -->
          <div class="rv-widget glass">
            <script defer="" async="" src="https://cdn.trustindex.io/loader.js?e5ad343727cf637b4d36c8ac53d"></script>
          </div>
        </div>
      </div>
    </section>

    <section class="devs" id="developers">
      <div class="wrap">
        <div class="head-row reveal" style="margin-bottom: 40px">
          <div>
            <span class="eyebrow">Leading Developer Partners</span>
            <h2 class="serif">Backed by Dubai's most <em class="gold-text">iconic</em> developers</h2>
          </div>
          <a class="btn btn-glass" href="<?= page_link('developers') ?>">View all developers →</a>
        </div>
      </div>
      <div class="dev-track reveal">
        <div class="dev-row">
<?php
          // The row is printed twice so the marquee loops seamlessly; the copy is hidden from screen readers
          foreach ([false, true] as $dup) {
            foreach ($home_devs as $row) {
              $dev_name = $row['dev_homp_img_alt'] ?: $row['dev_heading']; ?>
          <a
            class="dev glass"
            href="<?= BASE_URL ?>developer/<?= e($row['dev_slug']) ?>"
            <?= $dup ? 'aria-hidden="true" tabindex="-1"' : '' ?>
            title="<?= e($dev_name) ?> projects"
            ><img
              src="<?= BASE_URL_IMG ?>developer/homepage-image/<?= e($row['dev_homp_img']) ?>"
              alt="<?= $dup ? '' : e($dev_name) ?>"
              loading="lazy"
            /><span>View projects →</span></a
          >
<?php }
          } ?>
        </div>
      </div>
      <div class="wrap">
        <div class="excl reveal">
          <a
            class="ex glass"
            href="https://drehomes.com/company-news/sharafi-development-unveils-marea-residences-with-dre-x-as-exclusive-sales-partner"
          >
            <div class="phw">
              <div
                class="ph"
                style="background-image: url(https://drehomes.com/images/home-partner/Sharafi-deal1.jpeg)"
              ></div>
            </div>
            <div class="tx">
              <small>Exclusive Sales Partner</small
              ><img src="https://drehomes.com/images/home-partner/sharafi-logo1.png" alt="Sharafi Developments" />
              <h3>Shaping coastal living with Sharafi Developments</h3>
              <em>Read the story →</em>
            </div>
          </a>
          <a
            class="ex glass"
            href="https://drehomes.com/company-news/dubai-tonino-lamborghini-residences-unveils-final-phase"
          >
            <div class="phw">
              <div
                class="ph"
                style="background-image: url(https://drehomes.com/images/home-partner/gulf-deal1.jpeg)"
              ></div>
            </div>
            <div class="tx">
              <small>Exclusive Sales Partner</small
              ><img src="https://drehomes.com/images/home-partner/gulf-logo1.png" alt="Gulf Land Property Developers" />
              <h3>Elevating luxury living with Gulf Land Property Developers</h3>
              <em>Read the story →</em>
            </div>
          </a>
        </div>
      </div>
    </section>

    <section id="instagram" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Follow Along</span>
            <h2 class="serif">Life at <em class="gold-text">DRE</em>, on Instagram</h2>
            <a class="ig-handle" href="https://www.instagram.com/drehomes_realestate/" target="_blank" rel="noopener">
              <span class="ig-av"
                ><span><span class="logo-mark"></span></span
              ></span>
              <span
                ><b>@drehomes_realestate</b><small>New launches, market updates &amp; behind the scenes</small></span
              >
            </a>
          </div>
          <a class="btn btn-gold" href="https://www.instagram.com/drehomes_realestate/" target="_blank" rel="noopener">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect x="3" y="3" width="18" height="18" rx="5"></rect>
              <circle cx="12" cy="12" r="4"></circle>
              <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle>
            </svg>
            Follow us
          </a>
        </div>
        <div class="ig-grid reveal" id="igGrid">
<?php
          // 5 columns of 2; tall (reel) and short (post) tiles alternate from column to column
          $ig_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg>';
          foreach (array_chunk(array_slice($insta_items, 0, 10), 2) as $col => $pair) { ?>
          <div class="ig-col">
<?php foreach ($pair as $k => $item) {
              $tall = ($col + $k) % 2 === 0;
              $img = !empty($item['thumbnail_url']) ? $item['thumbnail_url'] : $item['media_url'];
              $img = preg_match('#^https?://#', $img) ? $img : BASE_URL_IMG . $img; ?>
            <a
              class="ig <?= $tall ? 'reel' : 'post' ?>"
              href="<?= e($item['instagram_url'] ?: 'https://www.instagram.com/drehomes_realestate/') ?>"
              target="_blank"
              rel="noopener"
              aria-label="<?= $tall ? 'Watch reel' : 'View post' ?> on Instagram"
            >
              <img src="<?= e($img) ?>" alt="<?= e($item['caption']) ?>" loading="lazy" />
<?php if ($tall) { ?>
              <span class="type"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg></span>
              <span class="hov glass on-img">Watch reel<?= $ig_svg ?></span>
<?php } else { ?>
              <span class="type"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle></svg></span>
              <span class="hov glass on-img">View post<?= $ig_svg ?></span>
<?php } ?>
            </a>
<?php } ?>
          </div>
<?php } ?>
        </div>
        <a
          class="btn btn-gold ig-more"
          href="https://www.instagram.com/drehomes_realestate/"
          target="_blank"
          rel="noopener"
          style="display: none; justify-content: center; margin-top: 20px"
          ><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3" y="3" width="18" height="18" rx="5"></rect>
            <circle cx="12" cy="12" r="4"></circle>
            <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg
          >Follow @drehomes_realestate</a
        >
      </div>
    </section>

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
