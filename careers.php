<?php
include('db/config.php');
// SEO: Admin > Manage Tags ("Careers")
$meta_tags = select_row('tbl_tags', 'id=18 and is_active=1');

// Admin > Career: Edit Career Content, Manage Position, Manage Focus Area, Career Awards, Career Gallery
$career = select_row('tbl_career_content', 'is_active=1') ?: [];
$positions = select_all('tbl_position', 'is_active=1 ORDER BY pos_id DESC');
$focus_areas = select_all('tbl_focus_area', 'is_active=1 ORDER BY focus_area_id ASC');
$career_awards = select_all('tbl_career_awards', 'is_active=1 ORDER BY carr_award_id ASC');
$gallery = select_all('tbl_career_gallery', 'is_active=1 ORDER BY career_gallry_id ASC');
$team_count = count(select_all('tbl_team', 'is_active=1'));
$awards_count = count(select_all('tbl_awards', 'is_active=1'));

// "Build Your Future With Us" -> "Build your future <em>with us.</em>"
$intro_heading = trim($career['career_intro_heading'] ?? '') ?: 'Build Your Future With Us';
$words = preg_split('/\s+/', rtrim($intro_heading, '.'));
$h1_tail = count($words) > 3 ? array_splice($words, -2) : [];
$h1 = e(ucfirst(strtolower(implode(' ', $words)))) . ($h1_tail ? ' <em class="gold-text">' . e(strtolower(implode(' ', $h1_tail))) . '.</em>' : '.');

// Description: first sentence of the first paragraph is the eyebrow, the last paragraph
// ("If you have experience …; if you are new …") fills the two boxes, the rest are the lead text
preg_match_all('#<p[^>]*>(.*?)</p>#is', $career['career_cont_descp'] ?? '', $m);
$paras = array_values(array_filter(array_map(fn($p) => trim(html_entity_decode(strip_tags($p), ENT_QUOTES, 'UTF-8')), $m[1])));
$why_eyebrow = 'Join a diverse and vibrant community';
if ($paras && preg_match('/^([^.]{5,60})\.\s+(.+)$/s', $paras[0], $mm)) {
  $why_eyebrow = $mm[1];
  $paras[0] = $mm[2];
}
$why_boxes = [];
if (count($paras) > 1 && substr_count(end($paras), ';') === 1) {
  foreach (explode(';', array_pop($paras)) as $part) {
    $part = trim(preg_replace('/^if [^,]+,\s*/i', '', trim($part)));
    $why_boxes[] = ucfirst(rtrim($part, '.')) . '.';
  }
}

// Role card details from the position description
$role_department = function ($title) {
  if (preg_match('/\b(IT|technology|developer|software)\b/i', $title))
    return 'Technology';
  if (preg_match('/talent|recruit|\bHR\b|human/i', $title))
    return 'People &amp; Culture';
  if (preg_match('/marketing/i', $title))
    return 'Marketing';
  if (preg_match('/leasing/i', $title))
    return 'Sales &amp; Leasing';
  return 'Sales';
};
$role_location = function ($descp) {
  $first = preg_match('#<p[^>]*>(.*?)</p>#is', (string) $descp, $mm) ? trim(html_entity_decode(strip_tags($mm[1]), ENT_QUOTES, 'UTF-8')) : '';
  if (preg_match('/dubai hills/i', $first))
    return 'Dubai Hills Estate';
  if (preg_match('/town square/i', $first))
    return 'Town Square';
  return $first !== '' && strlen($first) < 40 ? $first : 'Dubai';
};

// Font Awesome names from the admin -> the line icons of the new design
$focus_icons = [
  'fa-wallet' => '<rect x="3" y="6" width="18" height="14" rx="3"></rect><path d="M3 10h18M16 15h2"></path>',
  'fa-file-invoice-dollar' => '<circle cx="9" cy="8" r="3.5"></circle><circle cx="17" cy="9" r="2.5"></circle><path d="M2 21a7 7 0 0114 0M15 15a5 5 0 017 5"></path>',
  'fa-hand-holding-medical' => '<path d="M2 9l10-5 10 5-10 5z"></path><path d="M6 11v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"></path>',
  'fa-people-group' => '<path d="M3 17l6-6 4 4 8-8"></path><path d="M15 7h6v6"></path>',
  'fa-user-shield' => '<path d="M12 21c-5-3-8-7-8-11a8 8 0 0116 0c0 4-3 8-8 11z"></path><path d="M12 8v8M9 12h6"></path>',
  'fa-star' => '<path d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 00-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 000-7.8z"></path>',
];
$focus_icon = function ($fa) use ($focus_icons) {
  foreach ($focus_icons as $name => $svg) {
    if (strpos((string) $fa, $name) !== false)
      return $svg;
  }
  return $focus_icons['fa-star'];
};

$video = !empty($career['career_cont_video']) ? BASE_URL_IMG . 'career/video/' . rawurlencode($career['career_cont_video']) : '';
$video_thumb = BASE_URL . 'images/dre-careers-video-thumb.jpg';

include('common/header.php');
?>
<!-- ===== CAREERS: HERO ===== -->
<header class="ph-hero mg-hero cr-hero">
  <div class="ph-bg"
    style="background-image: url(<?= BASE_URL_IMG ?>career/<?= rawurlencode($career['career_cont_img'] ?? '') ?>)">
  </div>
  <div class="wrap">
    <div class="crumbs" role="navigation" aria-label="Breadcrumb">
      <a href="<?= BASE_URL ?>">Home</a><span>/</span><span aria-current="page">Careers</span>
    </div>
    <span class="eyebrow">Careers at DRE</span>
    <h1 class="serif"><?= $h1 ?></h1>
    <p class="sub"><?= e($career['career_intro_text'] ?? '') ?></p>
    <div class="story-cta">
      <a class="btn btn-gold" href="#roles">See open roles →</a><button class="btn btn-glass" type="button"
        id="crPlayHero">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
          <path d="M8 5v14l11-7z"></path>
        </svg>
        Watch our story
      </button>
    </div>
    <div class="dp-stats glass reveal">
      <div><small>People at DRE</small><b><?= $team_count ?></b></div>
      <div><small>Open roles</small><b><?= count($positions) ?></b></div>
      <div><small>Awards &amp; honours</small><b><?= $awards_count ?></b></div>
      <div><small>Great Place to Work</small><b>Certified</b></div>
    </div>
  </div>
</header>

<!-- ===== WHY ===== -->
<section style="padding-top: 60px">
  <div class="wrap cr-why">
    <div class="reveal">
      <span class="eyebrow"><?= e($why_eyebrow) ?></span>
      <h2 class="serif">An award-winning firm in the heart of <em class="gold-text">Dubai Hills Estate.</em></h2>
      <?php foreach ($paras as $p) { ?>
        <p class="lead"><?= e($p) ?></p>
      <?php } ?>
      <?php if (count($why_boxes) === 2) { ?>
        <div class="cr-two">
          <div class="glass">
            <b>Experienced in real estate?</b>
            <p><?= e($why_boxes[0]) ?></p>
          </div>
          <div class="glass">
            <b>New to this market?</b>
            <p><?= e($why_boxes[1]) ?></p>
          </div>
        </div>
      <?php } ?>
    </div>
    <button class="cr-vid reveal" type="button" id="crPlay" aria-label="Play the DRE brand video">
      <img src="<?= e($video_thumb) ?>" alt="DRE brand video" loading="lazy" /><span class="cr-pb"><svg
          viewBox="0 0 24 24" fill="currentColor">
          <path d="M8 5v14l11-7z"></path>
        </svg></span><span class="cr-vc glass on-img"><b>Inside DRE</b><small>Watch the brand film</small></span>
    </button>
  </div>
</section>

<!-- ===== RECOGNITION ===== -->
<section style="padding-top: 40px">
  <div class="wrap">
    <div class="cr-gp glass acc-edge reveal">
      <div>
        <span class="eyebrow">A great place to work</span>
        <h3 class="serif">Recognised for the culture <em class="gold-text">we’ve built together.</em></h3>
      </div>
      <ul class="cr-gpl">
        <li><b>2024</b><span>Great Place to Work® Certification</span></li>
        <li><b>2025</b><span>Recognised again as a Great Place to Work</span></li>
        <li><b>2026</b><span>Arabian Business — Top Workplaces in the Middle East</span></li>
      </ul>
      <a class="btn btn-glass" href="<?= page_link('our-awards') ?>">All <?= $awards_count ?> awards →</a>
    </div>
  </div>
</section>

<?php if ($focus_areas) { ?>
  <!-- ===== FOCUS ===== -->
  <section style="padding-top: 40px">
    <div class="wrap">
      <div class="head-row reveal">
        <div>
          <span
            class="eyebrow"><?= e(ucfirst(strtolower($career['career_keyfocus_heading'] ?? 'Our employee key focus areas'))) ?></span>
          <h2 class="serif">Built around <em class="gold-text">our people.</em></h2>
        </div>
      </div>
      <div class="cr-fg reveal">
        <?php foreach ($focus_areas as $fa) { ?>
          <div class="cr-fa glass">
            <span class="mx-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"><?= $focus_icon($fa['focus_fa_icon']) ?></svg></span><b><?= e($fa['focus_area_heading']) ?></b>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>
<?php } ?>

<?php if ($career_awards) { ?>
  <!-- ===== PARTNERS ===== -->
  <section style="padding-top: 40px">
    <div class="wrap">
      <div class="head-row reveal">
        <div>
          <span class="eyebrow"><?= e(ucfirst(strtolower($career['career_award_heading'] ?? 'Recognised by'))) ?></span>
          <h2 class="serif">The developers <em class="gold-text">who award us.</em></h2>
        </div>
      </div>
      <div class="cr-lgs reveal">
        <div class="cr-track">
          <?php
          // printed twice so the logo strip scrolls without a gap
          foreach ([1, 2] as $pass) {
            foreach ($career_awards as $aw) { ?>
              <span class="cr-lg"><img src="<?= BASE_URL_IMG ?>career/awards/<?= rawurlencode($aw['carr_award_img']) ?>"
                  alt="<?= $pass === 1 ? e($aw['carr_award_img_alt_tag']) : '' ?>" loading="lazy" /></span>
            <?php }
          } ?>
        </div>
      </div>
    </div>
  </section>
<?php } ?>

<!-- ===== ROLES ===== -->
<section id="roles" style="padding-top: 40px">
  <div class="wrap">
    <div class="head-row reveal">
      <div>
        <span class="eyebrow">Open positions</span>
        <?php if ($positions) { ?>
          <h2 class="serif"><?= count($positions) ?>   <?= count($positions) === 1 ? 'role' : 'roles' ?>, <em
              class="gold-text">one team.</em></h2>
        <?php } else { ?>
          <h2 class="serif">No open roles <em class="gold-text">right now.</em></h2>
        <?php } ?>
      </div>
      <a class="btn btn-glass" href="<?= page_link('our-team') ?>">Meet the team →</a>
    </div>
    <?php if ($positions) { ?>
      <div class="cr-rg reveal">
        <?php foreach ($positions as $pos) {
          $title = trim(preg_replace('/\s+/', ' ', $pos['pos_heading'])); ?>
          <article class="cr-r glass">
            <span class="cr-rd"><?= $role_department($title) ?></span>
            <h3><?= e($title) ?></h3>
            <div class="cr-rm">
              <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                  stroke-linejoin="round">
                  <path d="M12 21s-7-6.2-7-11.5a7 7 0 0114 0C19 14.8 12 21 12 21z"></path>
                  <circle cx="12" cy="9.5" r="2.5"></circle>
                </svg><?= e($role_location($pos['pos_descp'])) ?></span><span><svg viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="7" width="18" height="13" rx="2"></rect>
                  <path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 13h18"></path>
                </svg>Full-time</span>
            </div>
            <button class="btn btn-glass cr-apply" type="button" data-role="<?= e($title) ?>">Apply now →</button>
          </article>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
</section>

<!-- ===== APPLY ===== -->
<section id="contact" style="padding-top: 40px">
  <div class="wrap">
    <div class="cr-ap glass reveal" id="apply">
      <div class="cr-aph">
        <span class="eyebrow"><?= e($career['career_resume_heading'] ?? 'Send us your resume') ?></span>
        <h2 class="serif">Ready to make <em class="gold-text">your move?</em></h2>
        <p class="lead">
          Tell us a little about yourself and attach your CV — our People &amp; Culture team reviews every
          application.
        </p>
      </div>
      <form class="cr-form" id="crForm" novalidate="" action="<?= BASE_URL ?>ajax/career_frm.php" method="post"
        enctype="multipart/form-data">
        <label class="bv-in"><span class="bv-l">Full name <i>*</i></span><input name="first_name" id="crName"
            autocomplete="name" placeholder="Your name" required="" /></label>
        <label class="bv-in"><span class="bv-l">Email <i>*</i></span><input name="email_id" id="crEmail" type="email"
            autocomplete="email" placeholder="you@example.com" required="" /></label>
        <label class="bv-in"><span class="bv-l">Phone <i>*</i></span><input name="phone" id="crPhone" type="tel"
            autocomplete="tel" placeholder="Phone / WhatsApp" required="" /></label>
        <label class="bv-in"><span class="bv-l">LinkedIn profile</span><input name="linkedin_url" id="crLi" type="url"
            placeholder="linkedin.com/in/…" /></label>
        <label class="bv-in cr-full"><span class="bv-l">Position <i>*</i></span><select name="pos" id="crPos"
            required="">
            <option value="">Select a position…</option>
            <?php foreach ($positions as $pos) {
              $title = trim(preg_replace('/\s+/', ' ', $pos['pos_heading'])); ?>
              <option><?= e($title) ?></option>
            <?php } ?>
            <option>Open application</option>
          </select></label>
        <div class="cr-full">
          <span class="bv-l">Resume <i>*</i></span><label class="cr-drop" id="crDrop"><input type="file" name="resume"
              id="crFile" accept=".pdf,.doc,.docx" /><span class="cr-up"><svg viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 16V4M7 9l5-5 5 5"></path>
                <path d="M4 16v3a2 2 0 002 2h12a2 2 0 002-2v-3"></path>
              </svg></span><span class="cr-dt" id="crDt"><b>Drop your CV here or browse</b><small>PDF, DOC or DOCX · up
                to 5 MB</small></span></label>
        </div>
        <label class="bv-in cr-full"><span class="bv-l">Message</span><textarea name="message" id="crMsg" rows="4"
            placeholder="Anything you’d like us to know (optional)"></textarea>
        </label>
        <!-- left empty by people, filled in by spam bots -->
        <input type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true"
          style="position: absolute; left: -9999px" />
        <p class="bv-err cr-full" id="crErr" role="alert"></p>
        <div class="cr-full cr-sub">
          <button class="btn btn-gold" type="submit">Send application →</button><small>By applying you agree to DRE
            processing your details for recruitment.</small>
        </div>
      </form>
    </div>
  </div>
</section>

<?php if ($gallery) { ?>
  <!-- ===== LIFE ===== -->
  <section style="padding-top: 40px">
    <div class="wrap">
      <div class="head-row reveal">
        <div>
          <span class="eyebrow">Life at DRE</span>
          <h2 class="serif">A glimpse of <em class="gold-text">our world.</em></h2>
        </div>
        <a class="btn btn-glass" href="<?= page_link('our-media') ?>">Photo gallery →</a>
      </div>
      <div class="pf-gal reveal">
        <?php foreach ($gallery as $i => $g) {
          $src = BASE_URL_IMG . 'career/gallery/' . rawurlencode($g['career_gallry_img']);
          $label = 'Life at DRE — photo ' . ($i + 1); ?>
          <button class="pf-gi" type="button" aria-label="Open photo <?= $i + 1 ?>" data-src="<?= e($src) ?>"
            data-title="<?= e($label) ?>" data-lb="gallery">
            <img src="<?= e($src) ?>" alt="<?= e($g['career_gallry_img_alt_tag'] ?: $label) ?>" loading="lazy" />
          </button>
        <?php } ?>
      </div>
    </div>
  </section>
<?php } ?>

<dialog class="md-lb" id="mdLb" aria-label="Photo viewer">
  <div class="md-lb-top">
    <span id="mdLbN"></span><button type="button" class="md-x" id="mdX" aria-label="Close">✕</button>
  </div>
  <button type="button" class="md-nv md-pv" id="mdPv" aria-label="Previous photo">‹</button>
  <figure>
    <img id="mdLbImg" alt="" />
    <figcaption><b>Life at DRE</b><span>DRE Homes · Dubai Hills Estate</span></figcaption>
  </figure>
  <button type="button" class="md-nv md-nx" id="mdNx" aria-label="Next photo">›</button>
</dialog>
<dialog class="md-lb cr-vd" id="crVd" aria-label="DRE brand video">
  <div class="md-lb-top">
    <span>Inside DRE</span><button type="button" class="md-x" id="crVdX" aria-label="Close video">✕</button>
  </div>
  <figure>
    <video id="crVideo" controls="" playsinline="" preload="none" poster="<?= e($video_thumb) ?>">
      <source data-src="<?= e($video) ?>" type="video/mp4" />
    </video>
  </figure>
</dialog>

<?php include('common/footer.php'); ?>