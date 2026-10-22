<?php
// 1. CONFIG
require_once('db/config.php');

// 2. SEO: Admin > Manage Tags ("Our Team", id = 15) with page-specific overrides
$data = get_town_square_team_page_data();
$meta_tags = $data['meta_tags'];

// 3. DATA: Admin > Manage Team (tbl_team where is_time_square_team = 1)
$page = [
  'active' => 'about',
  'css' => ['assets/css/suggest.css', 'assets/css/pages/town-square-team.css'],
  'js' => ['assets/js/suggest.js'],
];

include('common/header.php');
?>

    <!-- ===== TOWN SQUARE TEAM: HERO ===== -->
    <header class="ph-hero ts-hero">
      <div class="ph-bg" style="background-image: url('<?= htmlspecialchars($data['hero_banner'], ENT_QUOTES, 'UTF-8') ?>')"></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= page_link('index') ?>">Home</a><span>/</span><a href="<?= page_link('our-team') ?>">Our Team</a><span>/</span
          ><span aria-current="page">Town Square</span>
        </div>
        <span class="eyebrow">Faces of DRE · Town Square Branch</span>
        <h1 class="serif">Meet the <em class="gold-text">Town Square</em> team.</h1>
        <p class="sub">
          Every member of our team brings a unique blend of skill, passion, and dedication — working together to deliver
          excellence in real estate.
        </p>
        <div class="ph-stats glass reveal ts-stats">
          <div><strong class="gold-text" data-n="<?= (int) $data['total_members'] ?>" data-plus="no">0</strong><span>Team members</span></div>
          <div><strong class="gold-text" data-n="<?= (int) $data['total_departments'] ?>" data-plus="no">0</strong><span>Departments</span></div>
          <div><strong class="gold-text">1</strong><span>Community-focused branch</span></div>
        </div>
      </div>
    </header>

    <!-- ===== BRANCH INFO ===== -->
    <section id="branch" style="padding-top: 70px">
      <div class="wrap">
        <div class="branch glass acc-edge reveal">
          <div class="br-tx">
            <span class="eyebrow">Town Square Branch</span>
            <h2 class="serif">Making property decisions <em class="gold-text">easier across Dubai.</em></h2>
            <p class="lead">
              With a strong understanding of the Town Square community, our team is here to connect you with
              opportunities that truly match your needs.
            </p>
            <ul class="br-info">
              <li>
                <span class="bi"
                  ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M12 21s-7-6.2-7-11.5a7 7 0 0114 0C19 14.8 12 21 12 21z"></path>
                    <circle cx="12" cy="9.5" r="2.5"></circle></svg
                ></span>
                <div>
                  <small>Visit us</small><?= e($data['branch_address']) ?>
                </div>
              </li>
              <li>
                <span class="bi"
                  ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path
                      d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2"
                    ></path></svg
                ></span>
                <div>
                  <small>Call</small><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $data['branch_tel1'])) ?>"><?= e($data['branch_tel1']) ?></a><?php if ($data['branch_tel2']) { ?> ·
                  <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $data['branch_tel2'])) ?>"><?= e($data['branch_tel2']) ?></a><?php } ?>
                </div>
              </li>
              <li>
                <span class="bi"
                  ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                    <path d="M3 7l9 6 9-6"></path></svg
                ></span>
                <div><small>Email</small><a href="mailto:<?= e($data['branch_email']) ?>"><?= e($data['branch_email']) ?></a></div>
              </li>
            </ul>
            <div class="story-cta">
              <a
                class="btn btn-gold"
                href="https://www.google.com/maps/search/?api=1&amp;query=DRE+Homes+Town+Square+Community+Center+Dubai"
                target="_blank"
                rel="noopener"
                >Get directions ↗</a
              >
              <a class="btn btn-glass" href="#contact">Book a meeting</a>
            </div>
          </div>
          <div class="br-img" style="background-image: url('<?= htmlspecialchars($data['branch_office_img'], ENT_QUOTES, 'UTF-8') ?>')"></div>
        </div>
      </div>
    </section>

    <!-- ===== TEAM GRID ===== -->
    <section id="team" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal" style="margin-bottom: 30px">
          <div>
            <span class="eyebrow">Our Team</span>
            <h2 class="serif">The people <em class="gold-text">behind every move</em></h2>
          </div>
          <label class="t-search glass"
            ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="11" cy="11" r="7"></circle>
              <path d="M20 20l-3.5-3.5"></path></svg
            ><input id="tSearch" type="search" placeholder="Search by name or role" aria-label="Search team members"
          /></label>
        </div>
        <div class="t-filters glass reveal" role="tablist" id="tFilters">
          <button role="tab" data-id="all" aria-selected="true" class="on">All<span><?= (int) $data['total_members'] ?></span></button>
          <?php foreach ($data['departments'] as $dept) { ?>
          <button role="tab" data-id="<?= (int) $dept['id'] ?>" aria-selected="false"><?= e($dept['name']) ?><span><?= (int) $dept['count'] ?></span></button>
          <?php } ?>
        </div>
        <div class="t-grid" id="tGrid" aria-live="polite">
          <?php foreach ($data['members'] as $m) { ?>
          <a class="tm" href="<?= e($m['link']) ?>" data-d="<?= (int) $m['department_id'] ?>">
            <img
              src="<?= e($m['img']) ?>"
              alt="<?= e($m['name'] . ', ' . $m['role']) ?>"
              loading="lazy"
            />
            <div class="cap glass on-img">
              <h3><?= e($m['name']) ?></h3>
              <small><?= e($m['role']) ?></small><span class="tgo">→</span>
            </div>
          </a>
          <?php } ?>
        </div>
        <p class="t-empty" id="tEmpty" hidden="">No team members found.</p>
      </div>
    </section>

    <!-- ===== AWARDS + CAREERS ===== -->
    <section style="padding-top: 20px">
      <div class="wrap duo">
        <article class="dcard reveal">
          <div
            class="bg"
            style="
              background-image: url('<?= htmlspecialchars($data['awards_img'], ENT_QUOTES, 'UTF-8') ?>');
              background-position: center 75%;
            "
          ></div>
          <div class="dc-in glass on-img">
            <span class="eyebrow">An award-winning team</span>
            <h3>85+ honors, <em class="gold-text">earned together.</em></h3>
            <p>
              From developer partner awards to Great Place To Work® recognition — our people are the reason behind every
              one.
            </p>
            <a class="btn btn-gold" href="<?= page_link('our-awards') ?>">View our awards →</a>
          </div>
        </article>
        <article class="dcard reveal">
          <div class="bg" style="background-image: url('<?= BASE_URL ?>images/why-c.jpg')"></div>
          <div class="dc-in glass on-img">
            <span class="eyebrow">Careers at DRE</span>
            <h3>Want to be one of the <em class="gold-text">faces of DRE?</em></h3>
            <p>Join a Best Workplace™ in Real Estate and grow with one of Dubai’s most recognised brokerages.</p>
            <a class="btn btn-gold" href="<?= page_link('careers') ?>">See open roles →</a>
          </div>
        </article>
      </div>
    </section>

    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Private Consultation</span>
            <h2 class="serif">Your advisor is<br /><em class="gold-text">one call</em> away.</h2>
            <p class="lead">Toll free <?= e($data['branch_tel1']) ?><?php if ($data['branch_tel2']) { ?> · Hotline <?= e($data['branch_tel2']) ?><?php } ?></p>
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

