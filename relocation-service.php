<?php
// 1. CONFIG
include('db/config.php');

// 2. DATA: Relocation Service (/relocation-service, see .htaccess) — design: relocation-service.html
// Admin > Services > Edit Service Section (tbl_service_section, id = 6; edit-service-section.php?edit=Ng==)
$data = get_relocation_service_data();
$meta_tags = $data['meta_tags'];

// Split name to highlight the last word with <em class="gold-text">
$words = explode(' ', $data['name']);
if (count($words) > 1) {
    $last_word = array_pop($words);
    $first_words = implode(' ', $words);
    $hero_title_html = e($first_words) . ' <em class="gold-text">' . e($last_word) . '</em>';
} else {
    $hero_title_html = '<em class="gold-text">' . e($data['name']) . '</em>';
}

// 3. SEO: tbl_service_section id = 6 (Admin > Services > edit-service-section.php?edit=Ng==)
$page = [
    'active' => 'services',
    'title' => $meta_tags['meta_title'] ?? 'Relocation Service | DRE Homes',
    'description' => $meta_tags['meta_description'] ?? 'End-to-end relocation for individuals and corporates — so your move to your new home is quick and stress-free.',
    'css' => ['assets/css/pages/relocation-service.css'],
];

include('common/header.php');
?>
    <!-- ===== SERVICE: HERO ===== -->
    <header class="ph-hero mg-hero">
      <div class="ph-bg" style="background-image: url(<?= e($data['banner_img']) ?>)"></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>">Home</a><span>/</span><a href="#">Services</a><span>/</span
          ><span aria-current="page"><?= e($data['name']) ?></span>
        </div>
        <span class="eyebrow">Services · Moving to Dubai</span>
        <h1 class="serif"><?= $hero_title_html ?></h1>
        <p class="sub">
          <?= e($meta_tags['meta_description']) ?>
        </p>
        <div class="story-cta">
          <a class="btn btn-gold" href="#contact">Plan my move →</a
          ><a class="btn btn-glass" href="tel:+971527543243">Call +971 52 754 3243</a>
        </div>
        <div class="dp-stats glass reveal">
          <div><small>Service</small><b>End-to-end</b></div>
          <div><small>For</small><b>Individuals · Corporates</b></div>
          <div><small>Packages</small><b>Built to your budget</b></div>
          <div><small>Goal</small><b>Quick &amp; stress-free</b></div>
        </div>
      </div>
    </header>

    <!-- ===== INTRO ===== -->
    <section style="padding-top: 70px">
      <div class="wrap mg-intro">
        <div class="mi-num glass reveal">
          <span class="mi-q serif">“Quick and <em class="gold-text">stress-free.</em>”</span
          ><small>That’s the promise of every DRE relocation.</small>
        </div>
        <div class="reveal">
          <span class="eyebrow">About the service</span>
          <h2 class="serif">Your move, <em class="gold-text">handled end to end.</em></h2>
          <p class="lead">
            DRE specialises in offering end-to-end solutions for relocation programs to individuals and corporates. Our
            comprehensive service ensures your movement to your new location is quick and stress-free.
          </p>
          <p class="lead">
            Our team of experts have in-depth knowledge and expertise in the relocation industry, who understand your
            needs and offer packages that meet your budget.
          </p>
        </div>
      </div>
    </section>

    <!-- ===== WHO WE HELP ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Who we help</span>
            <h2 class="serif">Made for <em class="gold-text">every kind of move.</em></h2>
          </div>
        </div>
        <div class="sv-who-grid reveal">
          <article class="sv-who glass">
            <span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="8" cy="7" r="3"></circle>
                <circle cx="17" cy="9" r="2.5"></circle>
                <path d="M2 21a6 6 0 0112 0M13 21a4.5 4.5 0 019 0"></path></svg></span
            ><small>For individuals &amp; families</small>
            <h3>Moving yourself or your family to Dubai</h3>
            <p>
              From finding the right home and community to getting your finances set up — one team guides you through
              every step of settling in.
            </p>
            <button class="mx-go" type="button" data-svc="Individual / family relocation">
              Plan a personal move →
            </button>
          </article>
          <article class="sv-who glass">
            <span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="4" y="3" width="16" height="18" rx="1"></rect>
                <path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2M10 21v-3h4v3"></path></svg></span
            ><small>For corporates</small>
            <h3>Relocation programs for your people</h3>
            <p>
              Moving employees to the UAE? We put together relocation packages around your budget so your team can focus
              on work from day one.
            </p>
            <button class="mx-go" type="button" data-svc="Corporate relocation program">
              Plan a corporate program →
            </button>
          </article>
        </div>
      </div>
    </section>

    <!-- ===== JOURNEY ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Settle in with DRE</span>
            <h2 class="serif">Everything you need, <em class="gold-text">under one roof.</em></h2>
          </div>
        </div>
        <ol class="sv-jr reveal">
          <li>
            <a class="sv-jr-c glass" href="<?= page_link('buy-property') ?>"
              ><span class="sv-jr-n">01</span
              ><span class="mx-ic"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M3 11l9-7 9 7"></path>
                  <path d="M5 10v10h14V10"></path>
                  <path d="M10 20v-5h4v5"></path></svg></span
              ><b>Find your home</b>
              <p>Ready homes to buy across Dubai’s communities.</p>
              <span class="sv-jr-go">Browse homes →</span></a
            >
          </li>
          <li>
            <a class="sv-jr-c glass" href="<?= page_link('mortgage-advisory') ?>"
              ><span class="sv-jr-n">02</span
              ><span class="mx-ic"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M3 11l9-7 9 7"></path>
                  <path d="M5 10v10h14V10"></path>
                  <path
                    d="M12 17v-5M10 13.5c0-1 .9-1.5 2-1.5s2 .5 2 1.5-1 1.3-2 1.5-2 .6-2 1.5.9 1.5 2 1.5 2-.5 2-1.5"
                  ></path></svg></span
              ><b>Finance it</b>
              <p>Compare mortgage offers from the banks we work with.</p>
              <span class="sv-jr-go">Mortgage advisory →</span></a
            >
          </li>
          <li>
            <a class="sv-jr-c glass" href="<?= page_link('bank-account-opening') ?>"
              ><span class="sv-jr-n">03</span
              ><span class="mx-ic"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M3 10l9-6 9 6"></path>
                  <path d="M5 10v8M9 10v8M15 10v8M19 10v8M3 21h18"></path></svg></span
              ><b>Open a bank account</b>
              <p>What citizens, residents and non-residents need.</p>
              <span class="sv-jr-go">Banking guide →</span></a
            >
          </li>
          <li>
            <a class="sv-jr-c glass" href="<?= page_link('business-licenses') ?>"
              ><span class="sv-jr-n">04</span
              ><span class="mx-ic"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M14 3H6v18h12V7z"></path>
                  <path d="M14 3v4h4M9 13h6M9 17h6"></path></svg></span
              ><b>Start your business</b>
              <p>Licence types, documents and steps in the UAE.</p>
              <span class="sv-jr-go">Business licenses →</span></a
            >
          </li>
          <li>
            <a class="sv-jr-c glass" href="<?= page_link('property-management') ?>"
              ><span class="sv-jr-n">05</span
              ><span class="mx-ic"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <circle cx="8" cy="15" r="4"></circle>
                  <path d="M11 12l9-9M17 6l3 3M15 8l2 2"></path></svg></span
              ><b>Let us look after it</b>
              <p>Leasing, maintenance and tenants — managed for you.</p>
              <span class="sv-jr-go">Property management →</span></a
            >
          </li>
        </ol>
      </div>
    </section>

    <!-- ===== WHY ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Why DRE</span>
            <h2 class="serif">Experts who <em class="gold-text">get it right.</em></h2>
          </div>
        </div>
        <div class="sv-why3 reveal">
          <div class="sv-w3 glass">
            <span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="6" cy="19" r="2"></circle>
                <circle cx="18" cy="5" r="2"></circle>
                <path d="M8 19h7a3.5 3.5 0 000-7H9a3.5 3.5 0 010-7h7"></path></svg></span
            ><b>End-to-end</b>
            <p>One partner for the whole move — no juggling multiple providers.</p>
          </div>
          <div class="sv-w3 glass">
            <span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M12 3l2.6 5.6 6.1.7-4.5 4.2 1.2 6L12 16.6 6.6 19.5l1.2-6L3.3 9.3l6.1-.7z"></path></svg></span
            ><b>In-depth expertise</b>
            <p>A team with deep knowledge of the relocation industry and of Dubai.</p>
          </div>
          <div class="sv-w3 glass">
            <span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="3" y="6" width="18" height="14" rx="3"></rect>
                <path d="M3 10h18M16 15h2"></path></svg></span
            ><b>Packages for your budget</b>
            <p>We understand your needs first, then build a package that fits.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== CALL STRIP ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="mg-call glass acc-edge reveal">
          <div>
            <span class="eyebrow">Talk to us</span>
            <h3 class="serif">Our consultants are <em class="gold-text">ready to assist you.</em></h3>
          </div>
          <div class="mc-acts">
            <a class="mc" href="tel:+971527543243"
              ><span
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path
                    d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2"
                  ></path></svg></span
              ><b>+971 52 754 3243</b><small>Call DRE</small></a
            >
            <a class="mc" href="mailto:sales@drehomes.com"
              ><span
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                  <path d="M3 7l9 6 9-6"></path></svg></span
              ><b>sales@drehomes.com</b><small>Email us</small></a
            >
          </div>
        </div>
      </div>
    </section>

<?php if (!empty($data['other_services'])) { ?>
    <!-- ===== OTHER SERVICES ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">More from DRE</span>
            <h2 class="serif">Other <em class="gold-text">services</em></h2>
          </div>
        </div>
        <div class="os-grid reveal">
<?php foreach ($data['other_services'] as $os) { ?>
          <a class="os glass" href="<?= e($os['url']) ?>"
            ><span class="os-ic"><?= service_icon_svg($os['slug']) ?></span
            ><b><?= e($os['name']) ?></b><span class="os-go">→</span></a
          >
<?php } ?>
        </div>
      </div>
    </section>
<?php } ?>

    <!-- ===== CONTACT FORM ===== -->
    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Plan your move</span>
            <h2 class="serif">Let’s plan<br /><em class="gold-text">your move.</em></h2>
            <p class="lead">
              Call +971 52 754 3243 or email sales@drehomes.com — or leave your details and a relocation consultant will
              call you back.
            </p>
          </div>
          <form class="form" id="ctaForm" novalidate="">
            <input name="name" placeholder="Full name" autocomplete="name" required="" />
            <input
              id="ctaPhone"
              name="phone"
              type="tel"
              placeholder="Phone / WhatsApp"
              autocomplete="tel"
              required=""
            />
            <input type="hidden" name="phone_full" id="ctaPhoneFull" />
            <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
            <select name="service" id="ctaSvc" aria-label="I’m relocating as">
              <option value="">I’m relocating as…</option>
              <option>Individual / family relocation</option>
              <option>Corporate relocation program</option>
              <option>Not sure yet</option>
            </select>
            <input name="message" placeholder="Anything else we should know? (optional)" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Plan my move</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>

