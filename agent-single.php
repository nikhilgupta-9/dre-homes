<?php
include('db/config.php');

$agent_slug = $_GET['detail'] ?? $_GET['u'] ?? '';
$agent = get_agent_single_page_data($agent_slug);

$meta_tags = $agent['meta_tags'] ?? [];
$page = [
  'active' => 'about',
  'css' => ['assets/css/pages/team-member.css'],
];

if (!$agent['found']) {
  http_response_code(404);
}

$icon_shield = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path><path d="M9 12l2 2 4-4"></path></svg>';
$icon_dep = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3.5"></circle><circle cx="17" cy="9" r="2.5"></circle><path d="M2 21a7 7 0 0114 0M15 15a5 5 0 017 5"></path></svg>';
$icon_star = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3l2.6 5.6 6.1.7-4.5 4.2 1.2 6L12 16.6 6.6 19.5l1.2-6L3.3 9.3l6.1-.7z"></path></svg>';
$icon_call = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2"></path></svg>';
$icon_wa = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"></path></svg>';
$icon_mail = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 7l9 6 9-6"></path></svg>';
$icon_qm = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.5 6C6 7.3 4 10 4 13.6V18h6v-6H7.3c.2-2 1.3-3.4 3.2-4.3zM19.5 6C16 7.3 14 10 14 13.6V18h6v-6h-2.7c.2-2 1.3-3.4 3.2-4.3z"></path></svg>';

include('common/header.php');

if (!$agent['found']) { ?>
    <!-- ===== NOT FOUND ===== -->
    <section id="mbNF" style="padding-top: 160px; min-height: 70vh;">
      <div class="wrap">
        <div class="op-empty glass">
          <h3>Profile not found.</h3>
          <p>This team member may have moved on, or the link is incorrect.</p>
          <div class="story-cta" style="justify-content: center">
            <a class="btn btn-gold" href="<?= page_link('our-team') ?>">Browse the team →</a>
          </div>
        </div>
      </div>
    </section>
<?php
  include('common/footer.php');
  exit;
}

$wa_msg = rawurlencode('Hi DRE, I would like to speak with ' . $agent['name'] . '.');
?>
    <!-- ===== MEMBER: HERO ===== -->
    <header class="ph-hero pf-hero tmb-hero">
      <div class="ph-bg" style="background-image: url(<?= e($agent['hero_bg']) ?>);"></div>
      <div class="wrap pf-hg">
        <div class="pf-ph glass">
          <img id="mbImg" src="<?= e($agent['img']) ?>" alt="<?= e($agent['img_alt']) ?>" />
<?php if ($agent['brn'] !== '') { ?>
          <span class="pf-brn" id="mbBrn"><?= $icon_shield ?>RERA BRN <?= e($agent['brn']) ?></span>
<?php } ?>
        </div>
        <div class="pf-ht">
          <div class="crumbs" role="navigation" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>">Home</a><span>/</span><a href="<?= page_link('our-team') ?>">Our Team</a><span>/</span><span aria-current="page" id="mbCrumb"><?= e($agent['inner_heading']) ?></span>
          </div>
          <span class="eyebrow" id="mbDep"><?= e($agent['department']) ?></span>
          <h1 class="serif" id="mbName"><?= $agent['name_html'] ?></h1>
          <p class="pf-role" id="mbRole"><?= e($agent['role']) ?></p>
          <div class="mb-chips" id="mbChips">
            <span><?= $icon_dep ?><?= e($agent['department']) ?></span>
<?php if ($agent['brn'] !== '') { ?>
            <span><?= $icon_shield ?>RERA licensed</span>
<?php } ?>
<?php if (!empty($agent['reviews'])) { ?>
            <span><?= $icon_star ?><a href="#mbRevS"><?= count($agent['reviews']) ?> client review<?= count($agent['reviews']) > 1 ? 's' : '' ?></a></span>
<?php } ?>
          </div>
          <div class="pf-acts" id="mbActs">
<?php if ($agent['mobile'] !== '') { ?>
            <a class="pf-a" href="tel:<?= e($agent['mobile']) ?>"><?= $icon_call ?><span>Call</span></a>
<?php } else { ?>
            <a class="pf-a off" href="tel:<?= e($agent['office']['tel']) ?>"><?= $icon_call ?><span>Call DRE<small>Main office</small></span></a>
<?php } ?>
<?php if ($agent['whatsapp'] !== '') { ?>
            <a class="pf-a" href="https://wa.me/<?= e($agent['whatsapp']) ?>" target="_blank" rel="noopener"><?= $icon_wa ?><span>WhatsApp</span></a>
<?php } else { ?>
            <a class="pf-a off" href="https://wa.me/<?= e($agent['office']['wa']) ?>?text=<?= $wa_msg ?>" target="_blank" rel="noopener"><?= $icon_wa ?><span>WhatsApp<small>Ask for <?= e($agent['first_name']) ?></small></span></a>
<?php } ?>
<?php if ($agent['email'] !== '') { ?>
            <a class="pf-a" href="mailto:<?= e($agent['email']) ?>"><?= $icon_mail ?><span>Email</span></a>
<?php } ?>
            <a class="btn btn-gold" href="#contact">Send an enquiry →</a>
          </div>
        </div>
      </div>
    </header>

    <!-- ===== ABOUT ===== -->
    <section style="padding-top: 50px" id="mbAboutS">
      <div class="wrap">
        <div class="pf-letter glass reveal">
          <div class="pf-lh">
            <span class="eyebrow">About</span>
            <h2 class="serif" id="mbAboutH">Meet <em class="gold-text"><?= e($agent['first_name']) ?>.</em></h2>
            <dl class="mb-facts" id="mbFacts">
              <div><dt>Role</dt><dd><?= e($agent['role']) ?></dd></div>
              <div><dt>Department</dt><dd><?= e($agent['department']) ?></dd></div>
<?php if ($agent['brn'] !== '') { ?>
              <div><dt>RERA BRN</dt><dd><?= e($agent['brn']) ?></dd></div>
<?php } ?>
<?php if (!empty($agent['reviews'])) { ?>
              <div><dt>Client reviews</dt><dd><?= count($agent['reviews']) ?></dd></div>
<?php } ?>
              <div><dt>Company</dt><dd>DRE Homes Real Estate</dd></div>
            </dl>
          </div>
          <div class="pf-lb" id="mbBio">
<?php if (!empty($agent['bio'])) { ?>
<?php foreach ($agent['bio'] as $idx => $p) { ?>
            <p<?= $idx === 0 ? ' class="pf-lead"' : '' ?>><?= e($p) ?></p>
<?php } ?>
<?php } else { ?>
            <p class="mb-lead"><?= e($agent['first_name']) ?> is part of the <?= e($agent['department']) ?> team at DRE Homes Real Estate.</p>
            <div class="mb-empty">
              <p>Have a question about buying, selling or renting in Dubai? Send <?= e($agent['first_name']) ?> a message and our team will get back to you.</p>
              <a class="btn btn-glass" href="#contact">Send an enquiry →</a>
            </div>
<?php } ?>
          </div>
        </div>
      </div>
    </section>

<?php if (!empty($agent['reviews'])) { ?>
    <!-- ===== REVIEWS ===== -->
    <section style="padding-top: 40px" id="mbRevS">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Client reviews</span>
            <h2 class="serif" id="mbRevH">What clients say about <em class="gold-text"><?= e($agent['first_name']) ?>.</em></h2>
          </div>
          <a class="btn btn-glass" id="mbRevAll" href="<?= page_link('clients-testimonials') ?>"><?= count($agent['reviews']) > 6 ? 'All ' . count($agent['reviews']) . ' reviews →' : 'All testimonials →' ?></a>
        </div>
        <div class="tq-wall mb-revs reveal" id="mbRev">
<?php foreach (array_slice($agent['reviews'], 0, 6) as $r) { ?>
          <article class="tq-c glass">
            <div class="tq-ct"><span class="tq-qm"><?= $icon_qm ?></span></div>
            <p><?= e($r['text']) ?></p>
            <div class="tq-cf">
              <span class="tq-av"><?= e($r['initials']) ?></span>
              <span><b><?= e($r['client']) ?></b><?php if ($r['date']) { ?><small><?= e($r['date']) ?></small><?php } ?></span>
            </div>
          </article>
<?php } ?>
        </div>
      </div>
    </section>
<?php } ?>

<?php if (!empty($agent['news'])) { ?>
    <!-- ===== NEWS ===== -->
    <section style="padding-top: 40px" id="mbNewsS">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">In the news</span>
            <h2 class="serif">Recognition <em class="gold-text">&amp; press.</em></h2>
          </div>
          <a class="btn btn-glass" href="<?= page_link('company-news') ?>">All company news →</a>
        </div>
        <ol class="mb-news reveal" id="mbNews">
<?php foreach (array_slice($agent['news'], 0, 8) as $n) { ?>
          <li>
            <a class="pf-ev glass" href="<?= e($n['url']) ?>">
<?php if ($n['thumb'] !== '') { ?>
              <span class="pf-evi"><img src="<?= e($n['thumb']) ?>" alt="<?= e($n['heading']) ?>" loading="lazy" /></span>
<?php } ?>
              <span class="pf-evb">
<?php if ($n['date'] !== '') { ?>
                <small><?= e($n['date']) ?></small>
<?php } ?>
                <b><?= e($n['heading']) ?></b>
              </span>
              <i aria-hidden="true">→</i>
            </a>
          </li>
<?php } ?>
        </ol>
      </div>
    </section>
<?php } ?>

<?php if (!empty($agent['colleagues'])) { ?>
    <!-- ===== COLLEAGUES ===== -->
    <section style="padding-top: 40px" id="mbColS">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow" id="mbColE">More from <?= e($agent['department']) ?></span>
            <h2 class="serif">Meet the <em class="gold-text">team.</em></h2>
          </div>
          <a class="btn btn-glass" href="<?= page_link('our-team') ?>">All <?= (int)$agent['total_people'] ?> people →</a>
        </div>
        <div class="ot-grid mb-col reveal" id="mbCol">
<?php foreach ($agent['colleagues'] as $c) { ?>
          <a class="ot-c glass" href="<?= e($c['url']) ?>">
            <span class="ot-ci">
              <img src="<?= e($c['img']) ?>" alt="<?= e($c['name']) ?>" loading="lazy" />
<?php if ($c['brn'] !== '') { ?>
              <span class="ot-lic"><?= $icon_shield ?>RERA licensed</span>
<?php } ?>
            </span>
            <span class="ot-cb">
              <b><?= e($c['name']) ?></b>
              <span><?= e($c['role']) ?></span>
            </span>
          </a>
<?php } ?>
        </div>
<?php if (!empty($agent['prev_member']) || !empty($agent['next_member'])) { ?>
        <div class="pn-row" id="mbPn">
<?php if (!empty($agent['prev_member'])) { ?>
          <a class="pn pn-l glass" href="<?= e($agent['prev_member']['url']) ?>">
            <small>← Previous</small>
            <b><?= e($agent['prev_member']['name']) ?></b>
          </a>
<?php } ?>
<?php if (!empty($agent['next_member'])) { ?>
          <a class="pn pn-r glass" href="<?= e($agent['next_member']['url']) ?>">
            <small>Next →</small>
            <b><?= e($agent['next_member']['name']) ?></b>
          </a>
<?php } ?>
        </div>
<?php } ?>
      </div>
    </section>
<?php } ?>

    <!-- ===== CONTACT ===== -->
    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Send an enquiry</span>
            <h2 class="serif">Get in touch<br />with <em class="gold-text"><?= e($agent['first_name']) ?>.</em></h2>
            <p class="lead">Toll free <?= e($footer_info['footer_contact_number'] ?? '800 37373') ?> · Hotline <?= e($footer_info['footer_contact_number2'] ?? '+971 52 754 3243') ?></p>
          </div>
          <form class="form" id="ctaForm" novalidate="">
            <input type="hidden" name="agent" value="<?= e($agent['slug']) ?>" />
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
            <input class="form-control" name="message" placeholder="Message for <?= e($agent['first_name']) ?> (optional)" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>

