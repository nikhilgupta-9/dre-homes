<?php
include('db/config.php');
// SEO: Admin > Manage Tags ("Our Team")
$meta_tags = select_row('tbl_tags', 'id=15 and is_active=1');

// Admin > Team > Team Content (banner, heading, intro)
$team_content = select_row('tbl_team_det', 'is_active=1') ?: [];
// Admin > Team > Designations (the department filter, in admin order)
$departments = select_all('tbl_team_desg', "is_active='1' ORDER BY order_no ASC");
// Admin > Team > Manage Team: everyone, by department order, then member order (same order as the old site)
$team_members = db_all(
  "SELECT t.*, d.team_desg_name
   FROM tbl_team t
   LEFT JOIN tbl_team_desg d ON d.team_desg_id = t.team_desg_id
   WHERE t.is_active = '1'
   ORDER BY d.order_no IS NULL, d.order_no ASC, t.order_no IS NULL, t.order_no ASC, t.team_id ASC"
);

// Admin text (TinyMCE HTML) -> plain text / paragraphs
$plain = fn($html) => trim(preg_replace('/\s+/u', ' ', str_replace("\xC2\xA0", ' ', html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], ' ', (string) $html)), ENT_QUOTES, 'UTF-8'))));
$paragraphs = function ($html) use ($plain) {
  preg_match_all('#<p[^>]*>(.*?)</p>#is', (string) $html, $m);
  return array_values(array_filter(array_map($plain, $m[1] ?: [(string) $html])));
};
$excerpt = function ($text, $words = 40) {
  $all = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
  return count($all) <= $words ? implode(' ', $all) : rtrim(implode(' ', array_slice($all, 0, $words)), ' ,;:.') . '…';
};

// One entry per person with everything the page prints.
// "k" is the position in the directory; the quick view opens by it (also from the leadership cards).
$people = [];
$department_counts = [];
foreach ($team_members as $k => $t) {
  $mobile = preg_replace('/[^0-9+]/', '', (string) $t['team_mobile']);
  $whatsapp = preg_replace('/[^0-9]/', '', (string) ($t['team_whtsapp'] ?: $t['team_mobile']));
  $bio = $paragraphs($t['team_cont']);
  $people[] = [
    'k' => $k,
    'department_id' => (int) $t['team_desg_id'],
    'department' => trim((string) $t['team_desg_name']),
    'name' => trim(preg_replace('/\s+/', ' ', $t['team_name'])),
    'role' => trim($t['team_desg']),
    'brn' => trim((string) $t['team_orn']), // RERA broker number: filled in = licensed
    'tel' => $mobile,
    'wa' => $whatsapp,
    'email' => trim($t['team_emailid']),
    'slug' => $t['team_slug'],
    'img' => BASE_URL_IMG . 'team/' . rawurlencode($t['team_img']),
    'bio' => $bio,
    'intro' => $excerpt(implode(' ', $bio)),
  ];
  $department_counts[(int) $t['team_desg_id']] = ($department_counts[(int) $t['team_desg_id']] ?? 0) + 1;
}

// Leadership section: the first two departments in admin order (Leadership big, Management small)
$leaders = array_values(array_filter($people, fn($p) => $p['department_id'] === (int) ($departments[0]['team_desg_id'] ?? 0)));
$managers = array_values(array_filter($people, fn($p) => $p['department_id'] === (int) ($departments[1]['team_desg_id'] ?? 0)));

$team_stats = [
  'people' => count($people),
  'departments' => count($department_counts),
  'licensed' => count(array_filter($people, fn($p) => $p['brn'] !== '')),
];
$cards_per_page = 24; // the page's JS shows this many, then "Show more"

// Hero: "Faces of DRE" -> "Faces of <em>DRE</em>", intro from the team content
$heading_words = preg_split('/\s+/u', trim($team_content['team_heading'] ?? '') ?: 'Faces of DRE');
$heading_last = array_pop($heading_words);
$team_h1 = e(implode(' ', $heading_words)) . ' <em class="gold-text">' . e($heading_last) . '</em>';
$team_intro = $paragraphs($team_content['team_descp'] ?? '')[0] ?? '';

$icon_licensed = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path><path d="M9 12l2 2 4-4"></path></svg>';
$icon_call = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2"></path></svg>';
$icon_whatsapp = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"></path></svg>';
$icon_view = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>';

include('common/header.php');
?>
    <!-- ===== TEAM: HERO ===== -->
    <header class="ph-hero mg-hero ot-hero">
      <div class="ph-bg" style="background-image: url(<?= BASE_URL_IMG ?>team/<?= rawurlencode($team_content['team_img'] ?? '') ?>)"></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>">Home</a><span>/</span><span aria-current="page">Our Team</span>
        </div>
        <span class="eyebrow">Our team</span>
        <h1 class="serif"><?= $team_h1 ?></h1>
<?php if ($team_intro !== '') { ?>
        <p class="sub"><?= e($team_intro) ?></p>
<?php } ?>
        <div class="story-cta">
          <a class="btn btn-gold" href="#find">Find your advisor →</a
          ><a class="btn btn-glass" href="<?= page_link('careers') ?>">Join the team</a>
        </div>
        <div class="dp-stats glass reveal">
          <div><small>People</small><b><?= $team_stats['people'] ?></b></div>
          <div><small>Departments</small><b><?= $team_stats['departments'] ?></b></div>
          <div><small>RERA-licensed brokers</small><b><?= $team_stats['licensed'] ?></b></div>
          <div><small>Since</small><b>2007</b></div>
        </div>
      </div>
    </header>

<?php if ($leaders || $managers) { ?>
    <!-- ===== LEADERSHIP ===== -->
    <section style="padding-top: 60px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Leadership</span>
            <h2 class="serif">The founders <em class="gold-text">behind DRE.</em></h2>
          </div>
        </div>
<?php if ($leaders) { ?>
        <div class="ot-lead reveal">
<?php foreach ($leaders as $p) { ?>
          <article class="ot-ld glass big">
            <button class="ot-ldi" type="button" data-k="<?= $p['k'] ?>" aria-label="Quick view: <?= e($p['name']) ?>">
              <img src="<?= e($p['img']) ?>" alt="<?= e($p['name']) ?>" loading="lazy" />
            </button>
            <div class="ot-ldb">
              <span class="eyebrow"><?= e($p['department']) ?></span>
              <h3 class="serif"><?= e($p['name']) ?></h3>
              <b class="ot-role"><?= e($p['role']) ?></b>
<?php if ($p['intro'] !== '') { ?>
              <p><?= e($p['intro']) ?></p>
<?php } ?>
              <button class="ot-more" type="button" data-k="<?= $p['k'] ?>">View profile →</button>
            </div>
          </article>
<?php } ?>
        </div>
<?php } ?>
<?php if ($managers) { ?>
        <div class="ot-mg reveal">
<?php foreach ($managers as $p) { ?>
          <article class="ot-ld glass">
            <button class="ot-ldi" type="button" data-k="<?= $p['k'] ?>" aria-label="Quick view: <?= e($p['name']) ?>">
              <img src="<?= e($p['img']) ?>" alt="<?= e($p['name']) ?>" loading="lazy" />
            </button>
            <div class="ot-ldb">
              <span class="eyebrow"><?= e($p['department']) ?></span>
              <h3 class="serif"><?= e($p['name']) ?></h3>
              <b class="ot-role"><?= e($p['role']) ?></b
              ><button class="ot-more" type="button" data-k="<?= $p['k'] ?>">View profile →</button>
            </div>
          </article>
<?php } ?>
        </div>
<?php } ?>
      </div>
    </section>
<?php } ?>

    <!-- ===== DIRECTORY ===== -->
    <section id="find" style="padding-top: 40px">
      <div class="wrap">
        <div class="head-row reveal" style="margin-bottom: 22px">
          <div>
            <span class="eyebrow">Find your advisor</span>
            <h2 class="serif">Meet the <em class="gold-text">whole team.</em></h2>
          </div>
        </div>
        <div class="cchips glass" id="otDep" role="tablist" aria-label="Department">
          <button data-d="all" class="on">All <b><?= $team_stats['people'] ?></b></button
<?php foreach ($departments as $d) {
          $count = $department_counts[(int) $d['team_desg_id']] ?? 0;
          if (!$count) continue; ?>
          ><button data-d="<?= (int) $d['team_desg_id'] ?>"><?= e($d['team_desg_name']) ?> <b><?= $count ?></b></button
<?php } ?>
          >
        </div>
        <div class="fbar glass ot-bar">
          <label class="fsearch"
            ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="11" cy="11" r="7"></circle>
              <path d="M20 20l-3.5-3.5"></path></svg
            ><input id="otQ" type="search" placeholder="Search by name or designation…" aria-label="Search team"
          /></label>
          <label class="ot-tg"><input type="checkbox" id="otLic" /><span></span>RERA-licensed only</label>
          <label class="fsel"
            ><span>Sort</span
            ><select id="otS">
              <option value="team">By team</option>
              <option value="az">Name: A–Z</option>
            </select></label
          >
        </div>
        <p class="dv-count" id="otCount"></p>
        <div class="ot-grid" id="otGrid" aria-live="polite">
<?php foreach ($people as $p) {
          $licensed = $p['brn'] !== ''; ?>
          <article
            class="ot-c glass"
            data-k="<?= $p['k'] ?>"
            data-d="<?= $p['department_id'] ?>"<?= $licensed ? "\n            data-lic=\"1\"" : '' ?>

            data-name="<?= e($p['name']) ?>"
            data-role="<?= e($p['role']) ?>"<?= $licensed ? "\n            data-brn=\"" . e($p['brn']) . '"' : '' ?><?= $p['tel'] !== '' ? "\n            data-tel=\"" . e($p['tel']) . "\"\n            data-wa=\"" . e($p['wa']) . '"' : '' ?>

            data-email="<?= e($p['email']) ?>"
            data-u="<?= e($p['slug']) ?>"<?= $p['k'] >= $cards_per_page ? "\n            hidden=\"\"" : '' ?>

          >
            <button class="ot-ci" type="button" data-k="<?= $p['k'] ?>" aria-label="Quick view: <?= e($p['name']) ?>">
              <img src="<?= e($p['img']) ?>" alt="<?= e($p['name']) ?>" loading="lazy" decoding="async" /><?= $licensed ? '<span class="ot-lic">' . $icon_licensed . 'RERA licensed</span>' : '' ?>

            </button>
            <div class="ot-cb"><b><?= e($p['name']) ?></b><span><?= e($p['role']) ?></span></div>
            <div class="ot-ca">
<?php if ($p['tel'] !== '') { ?>
              <a href="tel:<?= e($p['tel']) ?>" aria-label="Call <?= e($p['name']) ?>"><?= $icon_call ?></a
              ><a href="https://wa.me/<?= e($p['wa']) ?>" target="_blank" rel="noopener" aria-label="WhatsApp <?= e($p['name']) ?>"><?= $icon_whatsapp ?></a
              ><?php } ?><button type="button" data-k="<?= $p['k'] ?>" aria-label="View profile of <?= e($p['name']) ?>"><?= $icon_view ?></button>
            </div>
            <div class="ot-bio" hidden="">
<?php foreach ($p['bio'] as $paragraph) { ?>
              <p><?= e($paragraph) ?></p>
<?php } ?>
            </div>
          </article>
<?php } ?>
        </div>
        <div class="op-empty glass" id="otEmpty" hidden="">
          <h3>No one matches.</h3>
          <p>Try another department or name.</p>
        </div>
        <div class="md-more"><button class="btn btn-glass" type="button" id="otMore">Show more</button></div>
      </div>
    </section>

    <!-- ===== JOIN ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="mg-call glass acc-edge reveal">
          <div>
            <span class="eyebrow">Careers</span>
            <h3 class="serif">Got the drive? <em class="gold-text">Let’s build success together.</em></h3>
          </div>
          <div class="story-cta">
            <a class="btn btn-gold" href="<?= page_link('careers') ?>">Join the team →</a
            ><a class="btn btn-glass" href="<?= page_link('town-square-team') ?>">Town Square branch</a>
          </div>
        </div>
      </div>
    </section>

    <dialog class="ot-qv glass" id="otQv" aria-labelledby="otQvN">
      <button class="ot-x" type="button" id="otX" aria-label="Close">✕</button>
      <div class="ot-qvg">
        <img id="otQvI" alt="" />
        <div class="ot-qvb">
          <span class="eyebrow" id="otQvD"></span>
          <h3 class="serif" id="otQvN"></h3>
          <b class="ot-role" id="otQvR"></b><span class="ot-brn" id="otQvB"></span>
          <div class="ot-acts" id="otQvA"></div>
          <div class="ot-bio" id="otQvBio"></div>
          <a class="ot-full" id="otQvL" href="#">Open full profile ↗</a>
        </div>
      </div>
    </dialog>

    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Private Consultation</span>
            <h2 class="serif">Your advisor is<br /><em class="gold-text">one call</em> away.</h2>
            <p class="lead">Toll free <?= e($footer_info['footer_contact_number'] ?? '800 37373') ?> · Hotline <?= e($footer_info['footer_contact_number2'] ?? '+971 52 754 3243') ?></p>
          </div>
          <form class="form" id="ctaForm" novalidate="">
            <input class="form-control" name="name" placeholder="Full name" autocomplete="name" required="" />
            <input class="form-control" id="ctaPhone" name="phone" type="tel" placeholder="Phone / WhatsApp" autocomplete="tel" required="" />
            <input type="hidden" name="phone_full" id="ctaPhoneFull" />
            <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
            <input class="form-control" name="message" placeholder="What are you looking for?" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>
