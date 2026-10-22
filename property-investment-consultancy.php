<?php
// 1. CONFIG
include('db/config.php');

// 2. DATA: Property and Investment Consultancy (/property-investment-consultancy, see .htaccess) — design: property-investment-consultancy.html
// Admin > Services > Edit Service Section (tbl_service_section, id = 3; edit-service-section.php?edit=Mw==)
$data = get_property_investment_consultancy_data();
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

// 3. SEO: tbl_service_section id = 3 (Admin > Services > edit-service-section.php?edit=Mw==)
$page = [
    'active' => 'services',
    'title' => $meta_tags['meta_title'] ?? 'Property & Investment Consultancy | DRE Homes',
    'description' => $meta_tags['meta_description'] ?? 'Strategic and honest property and investment advice in Dubai — sourcing, due diligence, negotiation and asset management with DRE Homes.',
    'css' => ['assets/css/pages/property-investment-consultancy.css'],
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
        <span class="eyebrow">Services · Investment</span>
        <h1 class="serif"><?= $hero_title_html ?></h1>
        <p class="sub">
          <?= e($meta_tags['meta_description']) ?>
        </p>
        <div class="story-cta">
          <a class="btn btn-gold" href="#contact">Talk to a consultant →</a
          ><a class="btn btn-glass" href="tel:+971527543243">Call +971 52 754 3243</a>
        </div>
        <div class="dp-stats glass reveal">
          <div><small>Advice</small><b>Strategic &amp; honest</b></div>
          <div><small>Covers</small><b>Sourcing → asset management</b></div>
          <div><small>For</small><b>Buyers · Sellers · Investors</b></div>
          <div><small>Goal</small><b>Maximise your ROI</b></div>
        </div>
      </div>
    </header>

    <!-- ===== INTRO ===== -->
    <section style="padding-top: 70px">
      <div class="wrap mg-intro">
        <div class="mi-num glass reveal">
          <span class="mi-q serif">“Strategic and <em class="gold-text">honest</em> advice.”</span
          ><small>At every stage of the acquisition process.</small>
        </div>
        <div class="reveal">
          <span class="eyebrow">About the service</span>
          <h2 class="serif">Expert advice, <em class="gold-text">start to finish.</em></h2>
          <p class="lead">
            DRE investment expertise covers a comprehensive range of real estate sectors whereby we provide strategic
            and honest advice to our clients at all stages of the acquisition process from sourcing, due diligence,
            negotiation and asset management. Our team of highly-qualified experts are extremely well equipped to
            provide advice on real estate strategies, asset management and asset intelligence services.
          </p>
          <p class="lead">
            Our local market knowledge along with our deep understanding of business goals, objectives, drivers and
            constraints allows us to provide sound strategic advice within a wider context.
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
            <h2 class="serif">Buying, selling <em class="gold-text">or investing.</em></h2>
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
            ><small>If you are planning to buy or sell</small>
            <h3>Buying or selling a property in Dubai</h3>
            <p>
              If you are planning to buy or sell a property in Dubai, do call us. We offer our clients seamless, simple
              and efficient service.
            </p>
            <button class="mx-go" type="button" data-svc="Buying or selling a property">
              Talk about buying or selling →
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
            ><small>For investors &amp; portfolio owners</small>
            <h3>Unlocking the full value of your portfolio</h3>
            <p>
              We at DRE guide our clients through the most complex of deals, helping them to unlock the full potential
              value within their portfolios and maximise return on investment.
            </p>
            <button class="mx-go" type="button" data-svc="Investment / portfolio advice">
              Talk about my portfolio →
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
            <span class="eyebrow">At every stage</span>
            <h2 class="serif">From sourcing to <em class="gold-text">asset management.</em></h2>
          </div>
        </div>
        <ol class="sv-jr reveal">
          <li>
            <a class="sv-jr-c glass" href="#contact"
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
              ><b>Sourcing</b>
              <p>Finding the right opportunities, backed by our local market knowledge.</p>
              <span class="sv-jr-go">Talk to us →</span></a
            >
          </li>
          <li>
            <a class="sv-jr-c glass" href="#contact"
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
              ><b>Due diligence</b>
              <p>Checking the details before you commit.</p>
              <span class="sv-jr-go">Talk to us →</span></a
            >
          </li>
          <li>
            <a class="sv-jr-c glass" href="#contact"
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
              ><b>Negotiation</b>
              <p>Working to get you the right terms.</p>
              <span class="sv-jr-go">Talk to us →</span></a
            >
          </li>
          <li>
            <a class="sv-jr-c glass" href="#contact"
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
              ><b>Asset management</b>
              <p>Helping your portfolio deliver its full potential value.</p>
              <span class="sv-jr-go">Talk to us →</span></a
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
            <h2 class="serif">Advice you can <em class="gold-text">count on.</em></h2>
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
            ><b>Local market knowledge</b>
            <p>
              Sound strategic advice within a wider context — built on your business goals, objectives, drivers and
              constraints.
            </p>
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
            ><b>Complex deals, guided</b>
            <p>
              We guide clients through the most complex of deals to unlock the full potential value within their
              portfolios.
            </p>
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
            ><b>A loyal client base</b>
            <p>Repeat customers are the best form of business — and DRE is fully committed to meet your needs.</p>
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
            <span class="eyebrow">Contact us</span>
            <h2 class="serif">We are always<br /><em class="gold-text">at your service.</em></h2>
            <p class="lead">
              Call DRE on +971 52 754 3243 or email us at sales@drehomes.com — or leave your details and a consultant
              will call you back.
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
            <select name="service" id="ctaSvc" aria-label="I need advice on">
              <option value="">I need advice on…</option>
              <option>Buying or selling a property</option>
              <option>Investment / portfolio advice</option>
              <option>Not sure yet</option>
            </select>
            <input name="message" placeholder="Anything else we should know? (optional)" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>

