<?php
include('db/config.php');
$meta_tags = select_row('tbl_tags', 'id=14 and is_active=1');
$about = select_row('tbl_abt_content', 'is_active=1') ?: [];
$counters = select_all('tbl_abt_counter', 'is_active=1 ORDER BY abt_countr_id DESC');
$services = select_all('tbl_abt_service', 'is_active=1 ORDER BY abt_serv_id DESC');
$management = select_all('tbl_team', 'team_company_owner=1 AND is_active=1 ORDER BY order_no IS NULL, order_no ASC, team_id ASC');

if (!function_exists('e')) {
  function e($s)
  {
    return htmlspecialchars(trim(html_entity_decode((string) $s, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8');
  }
}
// Admin links are stored either as full URLs or as site paths ("our-awards")
function about_link($url)
{
  $url = trim((string) $url);
  return preg_match('#^(https?:)?//#', $url) || $url === '' || $url[0] === '#' ? $url : BASE_URL . ltrim($url, '/');
}

// Description paragraphs: the first one is the hero intro, the last one is the ethos quote
preg_match_all('#<p[^>]*>(.*?)</p>#is', $about['abt_content_descp'] ?? '', $m);
$paras = array_values(array_filter(array_map(fn($p) => trim(strip_tags($p, '<b><strong><em><i><a>')), $m[1])));
$hero_intro = $paras ? strip_tags(array_shift($paras)) : '';
$ethos = count($paras) > 1 ? array_pop($paras) : 'At DRE, we live by our ethos of <b>Service Beyond the Sale.</b>';

// "Experience. Excellence. DRE." -> last word in gold
$heading = trim($about['abt_content_heading'] ?? 'Experience. Excellence. DRE.');
$h1 = preg_match('/^(.*\s)(\S+)$/u', $heading, $hm) ? e($hm[1]) . ' <em class="gold-text">' . e($hm[2]) . '</em>' : e($heading);
// "Service Beyond the Sale: The DRE Way Since 2007" -> part after the colon in gold
$sub = trim($about['abt_content_sub_heading'] ?? '');
$h2 = strpos($sub, ':') !== false
  ? e(strstr($sub, ':', true)) . ': <em class="gold-text">' . e(substr(strstr($sub, ':'), 1)) . '</em>'
  : e($sub);

$honors = 85;
foreach ($counters as $c) {
  if (stripos($c['abt_countr_heading'], 'honor') !== false)
    $honors = (int) $c['abt_countr_counter'];
}

// Font Awesome names from the admin -> the line icons used by the new design
$val_icons = [
  'fa-rocket' => '<circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="5"></circle><circle cx="12" cy="12" r="1.5" fill="currentColor"></circle>',
  'fa-chart-line' => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle>',
  'fa-award' => '<path d="M12 21s-7-4.4-7-10a4 4 0 017-2.6A4 4 0 0119 11c0 5.6-7 10-7 10z"></path>',
  'fa-thumbs-up' => '<path d="M12 3l2.6 5.4 5.9.9-4.3 4.1 1 5.9L12 16.6 6.8 19.3l1-5.9L3.5 9.3l5.9-.9z"></path>',
  'fa-users' => '<circle cx="9" cy="8" r="3.2"></circle><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"></path><circle cx="17" cy="9" r="2.4"></circle><path d="M15.5 14.2c3 .2 5.5 2.6 5.5 5.8"></path>',
  'fa-headset' => '<path d="M4 5h16v11H8l-4 4z"></path><path d="M8 9h8M8 12h5"></path>',
];
// Mission and Vision get the two large cards
$vals_big = $vals_small = [];
foreach ($services as $s) {
  if (preg_match('/^(mission|vision)$/i', trim($s['abt_serv_heading'])))
    $vals_big[] = $s;
  else
    $vals_small[] = $s;
}
usort($vals_big, fn($a, $b) => strcasecmp(trim($a['abt_serv_heading']), trim($b['abt_serv_heading'])));

$about_img = BASE_URL_IMG . 'about/' . ($about['abt_content_img'] ?? '');
include('common/header.php');
?>

  <!-- ===== ABOUT: PAGE HERO ===== -->
  <header class="ph-hero">
    <div class="ph-bg"
      style="background-image: url(<?= BASE_URL_IMG ?>about/<?= e($about['abt_content_bannr_img'] ?? '') ?>)"></div>
    <div class="wrap">
      <div class="crumbs" role="navigation" aria-label="Breadcrumb">
        <a href="index.php">Home</a><span>/</span><span aria-current="page">About Us</span>
      </div>
      <span class="eyebrow"><?= e($about['abt_content_title'] ?? 'About DRE') ?></span>
      <h1 class="serif"><?= $h1 ?></h1>
      <p class="sub"><?= e($hero_intro) ?></p>
      <div class="ph-stats glass reveal">
        <?php foreach ($counters as $c) { ?>
          <div><strong class="gold-text"
              data-n="<?= (int) $c['abt_countr_counter'] ?>">0</strong><span><?= e($c['abt_countr_heading']) ?></span>
          </div>
        <?php } ?>
      </div>
    </div>
  </header>

  <!-- ===== STORY ===== -->
  <section id="story">
    <div class="wrap story">
      <div class="reveal">
        <span class="eyebrow">Our Story</span>
        <h2 class="serif"><?= $h2 ?></h2>
        <?php foreach ($paras as $p) { ?>
          <p class="lead"><?= $p ?></p>
        <?php } ?>
        <div class="story-cta">
          <?php if (!empty($about['abt_content_btn_text'])) { ?>
            <a class="btn btn-gold"
              href="<?= e(about_link($about['abt_content_btn_url'])) ?>"><?= e($about['abt_content_btn_text']) ?> →</a>
          <?php } ?>
          <a class="btn btn-glass" href="<?= page_link('brand-video') ?>">▶ Watch our film</a>
        </div>
      </div>
      <div class="story-media reveal">
        <div class="sm-img" style="background-image: url(<?= e($about_img) ?>)"></div>
        <!-- <?php if (!empty($about['abt_content_video'])) { ?>
          <button class="play glass on-img sm-play" id="openVideo"
            aria-haspopup="dialog"><i>▶</i><?= e(ucwords($about['abt_content_vdo_text'] ?: 'Discover DRE')) ?></button>
        <?php } ?> -->
        <div class="ethos glass acc-edge">
          <span class="qmark gold-text">“</span>
          <p><?= $ethos ?></p>
        </div>
      </div>
    </div>
  </section>

  

  <!-- ===== WHAT SETS DRE APART ===== -->
  <section id="values" style="padding-top: 20px">
    <div class="wrap">
      <div class="head-row reveal">
        <div>
          <span class="eyebrow">What Sets DRE Apart?</span>
          <h2 class="serif">It’s not just what we do — <em class="gold-text">it’s how we do it</em></h2>
        </div>
      </div>
      <div class="vals reveal">
        <?php foreach (array_merge($vals_big, $vals_small) as $i => $s) {
          $big = $i < count($vals_big);
          $icon = $val_icons[trim($s['abt_serv_faicon'])] ?? $val_icons['fa-award'];
          $title = ucwords(strtolower(trim($s['abt_serv_heading']))); ?>
          <article class="val<?= $big ? ' big' : '' ?> glass<?= $big ? ' acc-edge' : '' ?>">
            <div class="ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><?= $icon ?></svg>
            </div>
            <h3><?= e($title) ?></h3>
            <p><?= e(strip_tags($s['abt_serv_descp'])) ?></p>
            <?php if (stripos($title, 'consul') !== false) { ?>
              <a href="#contact">Book a free consultation →</a>
            <?php } ?>
          </article>
        <?php } ?>
      </div>
    </div>
  </section>

  <!-- ===== MANAGEMENT ===== -->
  <section id="management" style="padding-top: 20px">
    <div class="wrap">
      <div class="head-row reveal">
        <div>
          <span class="eyebrow">Leadership</span>
          <h2 class="serif">Meet our <em class="gold-text">management</em></h2>
        </div>
        <a class="btn btn-glass" href="<?= BASE_URL ?>our-team">Our full team →</a>
      </div>
      <div class="mgmt reveal">
        <?php
        $so_icons = [
          'team_fblink' => ['Facebook', '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 8.5V6.8c0-.8.2-1.3 1.4-1.3H17V2.6c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.4-4 4.1v1.9H8v3.2h2.6V22H14v-10.3h2.7l.4-3.2z"></path></svg>'],
          'team_twitterlink' => ['X', '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.8 3h3.1l-6.8 7.7L22 21h-6.2l-4.9-6.3L5.3 21H2.2l7.3-8.3L2 3h6.4l4.4 5.8zm-1.1 16.2h1.7L7.4 4.7H5.6z"></path></svg>'],
          'team_linkedinlink' => ['LinkedIn', '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5a2.5 2.5 0 110 5 2.5 2.5 0 010-5zM3 9.5h4V21H3zM9.5 9.5h3.8v1.6h.1c.5-1 1.8-2 3.8-2 4 0 4.8 2.6 4.8 6V21h-4v-5.2c0-1.2 0-2.8-1.7-2.8s-2 1.3-2 2.7V21h-4z"></path></svg>'],
          'team_instagramlink' => ['Instagram', '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg>'],
        ];
        foreach ($management as $t) {
          $profile = BASE_URL . 'management/' . $t['team_slug']; ?>
          <article class="mcard">
            <a class="m-ph" href="<?= e($profile) ?>"
              style="background-image: url(<?= BASE_URL_IMG ?>team/<?= e($t['team_img']) ?>)"
              aria-label="<?= e($t['team_name']) ?> profile"></a>
            <div class="m-in glass on-img">
              <div>
                <h3><?= e($t['team_name']) ?></h3>
                <small><?= e($t['team_desg']) ?></small>
              </div>
              <div class="m-so">
                <?php foreach ($so_icons as $col => [$net, $svg]) {
                  if (empty(trim((string) $t[$col])))
                    continue; ?>
                  <a class="so" href="<?= e($t[$col]) ?>" target="_blank" rel="noopener"
                    aria-label="<?= e($t['team_name']) ?> on <?= $net ?>"><?= $svg ?></a>
                <?php } ?>
              </div>
              <a class="m-link" href="<?= e($profile) ?>">View profile →</a>
            </div>
          </article>
        <?php } ?>
        <aside class="m-msg glass acc-edge">
          <span class="eyebrow">Words from our founders</span>
          <h3>A vision backed by <em class="gold-text">determination.</em></h3>
          <p>Read what our co-founders have to say about DRE’s journey, values and what comes next.</p>
          <a class="mm-l" href="founders-note-ceo.html"><b>Message from the CEO</b><span>Mudasir Wani →</span></a>
          <a class="mm-l" href="founders-note-clo.html"><b>Message from the CLO</b><span>Qurat Ul Ain →</span></a>
        </aside>
      </div>
    </div>
  </section>

  <!-- ===== RECOGNITION ===== -->
  <section style="padding-top: 20px">
    <div class="wrap">
      <div class="award-band glass acc-edge reveal">
        <img
          src="https://drehomes.com/admin_nsrdwsc/assets/media/header/greate_place_logo_2803_Great-place-to-work-middle-east-website.png"
          alt="Great Place To Work — Best Workplaces in Real Estate, Middle East 2026" loading="lazy" />
        <div>
          <span class="eyebrow">Recognition</span>
          <h2 class="serif"><?= $honors ?>+ honors, and <em class="gold-text">counting.</em></h2>
          <p class="lead">
            Award-winning, Tier One brokerage — and a Great Place To Work® Best Workplace in Real Estate, Middle East
            2026.
          </p>
        </div>
        <a class="btn btn-gold" href="<?= BASE_URL ?>our-awards">View our awards →</a>
      </div>
    </div>
  </section>

  <!-- VIDEO MODAL -->
  <dialog class="vmodal" id="videoModal" aria-label="Discover DRE video">
    <button class="vm-x" id="closeVideo" aria-label="Close video">×</button>
    <video id="aboutVideo" controls="" playsinline="" preload="none" poster="<?= e($about_img) ?>">
      <source data-src="<?= BASE_URL_IMG ?>about/<?= e($about['abt_content_video'] ?? '') ?>" type="video/mp4" />
    </video>
  </dialog>

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
