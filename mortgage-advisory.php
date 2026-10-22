<?php
// 1. CONFIG
include('db/config.php');

// 2. DATA: Mortgage Advisory (/mortgage-advisory, see .htaccess) — design: mortgage-advisory.html
// Admin > Services > Edit Service Section (tbl_service_section, id = 4; edit-service-section.php?edit=NA==)
$data = get_mortgage_advisory_page_data();
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

// 3. SEO: tbl_service_section id = 4 (Admin > Services > edit-service-section.php?edit=NA==)
$page = [
  'active' => 'services',
  'title' => $meta_tags['meta_title'] ?? 'Mortgage Advisory | DRE Homes',
  'description' => $meta_tags['meta_description'] ?? 'Compare, negotiate and secure the right mortgage for your property in Dubai with DRE Homes — working with 40+ banks and financial institutions.',
  'css' => ['assets/css/pages/mortgage-advisory.css'],
];

include('common/header.php');
?>

    <!-- ===== MORTGAGE: HERO ===== -->
    <header class="ph-hero mg-hero">
      <div
        class="ph-bg"
        style="
          background-image: url('<?= e($data['banner_img']) ?>');
        "
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>">Home</a><span>/</span><a href="#">Services</a><span>/</span
          ><span aria-current="page"><?= e($data['name']) ?></span>
        </div>
        <span class="eyebrow">Services · Finance</span>
        <h1 class="serif"><?= $hero_title_html ?></h1>
        <p class="sub">
          <?= e($meta_tags['meta_description']) ?>
        </p>
        <div class="story-cta">
          <a class="btn btn-gold" href="#contact">Apply for a mortgage →</a
          ><a class="btn btn-glass" href="#calc">Calculate repayments</a>
        </div>
        <div class="dp-stats glass reveal">
          <div><small>Banks &amp; lenders in the UAE</small><b>40+</b></div>
          <div><small>Mortgage services</small><b><?= count($data['advisor_steps']) ?: 4 ?></b></div>
          <div><small>Property types</small><b>Off-plan · Ready</b></div>
          <div><small>Consultation</small><b>Free</b></div>
        </div>
      </div>
    </header>

    <!-- ===== INTRO ===== -->
    <section style="padding-top: 70px">
      <div class="wrap mg-intro">
        <div class="mi-num glass reveal">
          <b class="gold-text">40+</b>
          <span>banks and financial institutions offer mortgage products in the UAE.</span>
          <small><?= htmlspecialchars($data['fav_cont']) ?></small>
        </div>
        <div class="reveal">
          <span class="eyebrow">Why DRE</span>
          <h2 class="serif">Too many options. <em class="gold-text">One trusted advisor.</em></h2>
          <p class="lead">
            With more than 40 banks and financial institutions offering a variety of mortgage products in the UAE,
            getting the right mortgage deal isn’t easy!
          </p>
          <p class="lead">
            As we work closely with numerous banks and developers, DRE is well placed to assist you in finding the right
            mortgage for your dream home in Dubai. <?= htmlspecialchars($data['sub_heading']) ?>
          </p>
        </div>
      </div>
    </section>

    <!-- ===== EXPERTISE ===== -->
    <section id="expertise" style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Our expertise includes</span>
            <h2 class="serif">Four ways <em class="gold-text">we can help.</em></h2>
          </div>
        </div>
        <div class="mx-grid reveal">
          <article class="mx glass">
            <span class="mx-n">01</span>
            <span class="mx-ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 21V5l10-2v18"></path>
                <path d="M14 7h6v4"></path>
                <path d="M20 11v3"></path>
                <path d="M8 21v-4h3v4"></path>
                <path d="M2 21h20"></path>
              </svg>
            </span>
            <h3>Mortgage on a new or off-plan property</h3>
            <p>
              Buying off-plan? We explain how a mortgage works alongside your developer’s payment plan — and which banks
              can finance the project you’re looking at.
            </p>
            <button class="mx-go" type="button" data-svc="Mortgage on a new or off-plan property">
              Ask about this →
            </button>
          </article>
          <article class="mx glass">
            <span class="mx-n">02</span>
            <span class="mx-ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="8" cy="15" r="4"></circle>
                <path d="M11 12l9-9M17 6l3 3M15 8l2 2"></path>
              </svg>
            </span>
            <h3>Mortgage on ready properties</h3>
            <p>
              Moving straight in or buying to let? We compare rates and terms across banks and help you get pre-approved
              before you make an offer.
            </p>
            <button class="mx-go" type="button" data-svc="Mortgage on ready properties">Ask about this →</button>
          </article>
          <article class="mx glass">
            <span class="mx-n">03</span>
            <span class="mx-ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 11l9-7 9 7"></path>
                <path d="M5 10v10h14V10"></path>
                <path d="M12 17v-5M10 13.5c0-1 .9-1.5 2-1.5s2 .5 2 1.5-1 1.3-2 1.5-2 .6-2 1.5.9 1.5 2 1.5 2-.5 2-1.5"></path>
              </svg>
            </span>
            <h3>Equity Release</h3>
            <p>
              Unlock cash from the value of a property you already own — to reinvest, renovate or fund your next
              purchase.
            </p>
            <button class="mx-go" type="button" data-svc="Equity Release">Ask about this →</button>
          </article>
          <article class="mx glass">
            <span class="mx-n">04</span>
            <span class="mx-ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 8h13l-3-3M20 16H7l3 3"></path>
              </svg>
            </span>
            <h3>Remortgage / Buyout</h3>
            <p>
              Paying too much on your current mortgage? Move it to a bank with a better rate or terms — we handle the
              comparison and the paperwork.
            </p>
            <button class="mx-go" type="button" data-svc="Remortgage / Buyout">Ask about this →</button>
          </article>
        </div>
      </div>
    </section>

    <!-- ===== CALCULATOR ===== -->
    <section id="calc" style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Mortgage calculator</span>
            <h2 class="serif">What could you <em class="gold-text">pay monthly?</em></h2>
          </div>
        </div>
        <div class="mort glass reveal">
          <div class="mg-in">
            <div class="mg-type" role="group" aria-label="Buyer type">
              <button type="button" class="on" data-min="20">Expat</button
              ><button type="button" data-min="15">UAE national</button>
            </div>
            <label
              ><span>Property price</span>
              <div class="mv">
                <span>AED</span
                ><input id="mPrice" inputmode="numeric" value="2,000,000" aria-label="Property price in AED" /></div
            ></label>
            <label
              ><span>Down payment <b id="mDpV">20%</b></span
              ><input type="range" id="mDp" min="20" max="80" step="5" value="20"
            /></label>
            <label
              ><span>Interest rate <b id="mRtV">4.5%</b></span
              ><input type="range" id="mRt" min="2" max="9" step="0.1" value="4.5"
            /></label>
            <label
              ><span>Loan term <b id="mYrV">25 years</b></span
              ><input type="range" id="mYr" min="5" max="25" step="1" value="25"
            /></label>
          </div>
          <div class="m-out">
            <small>Estimated monthly payment</small>
            <div class="m-big gold-text" id="mOut">—</div>
            <div class="m-split" aria-hidden="true"><i id="mBarP"></i><i id="mBarI"></i></div>
            <div class="m-leg">
              <span><i class="lp"></i>Principal <b id="mPP">—</b></span
              ><span><i class="li"></i>Interest <b id="mIP">—</b></span>
            </div>
            <ul>
              <li><span>Loan amount</span><b id="mLoan">—</b></li>
              <li><span>Down payment</span><b id="mDown">—</b></li>
              <li><span>Total interest</span><b id="mInt">—</b></li>
            </ul>
            <div class="m-up">
              <small>Cash needed upfront (approx.)</small>
              <ul>
                <li><span>Down payment</span><b id="uDown">—</b></li>
                <li><span>DLD transfer fee · 4%</span><b id="uDld">—</b></li>
                <li><span>Mortgage registration · 0.25% of loan</span><b id="uReg">—</b></li>
                <li class="tot"><span>Total</span><b id="uTot">—</b></li>
              </ul>
            </div>
            <p>
              Indicative only — excludes bank processing, valuation and agency fees. Rates depend on the bank and your
              profile.
            </p>
            <a class="btn btn-gold" href="#contact" id="mApply">Get pre-approved →</a>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== PROCESS / ADVISOR STEPS ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">How it works</span>
            <h2 class="serif"><?= gold_last_word($data['adv_heading']) ?></h2>
          </div>
        </div>
        <ol class="ms-grid reveal">
          <?php if (!empty($data['advisor_steps'])) { ?>
            <?php foreach ($data['advisor_steps'] as $s) { ?>
              <li class="ms glass">
                <span class="ms-ic"><?= $s['svg_icon'] ?></span>
                <small><?= htmlspecialchars($s['step_label']) ?></small>
                <b><?= htmlspecialchars($s['heading']) ?></b>
                <p><?= htmlspecialchars($s['text']) ?></p>
              </li>
            <?php } ?>
          <?php } else { ?>
            <li class="ms glass">
              <span class="ms-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M21 12a8 8 0 01-11.6 7.1L4 20l1-4.6A8 8 0 1121 12z"></path></svg></span>
              <small>Step 1</small><b>Free consultation</b>
              <p>Tell us about the property, your budget and your income. We’ll work out what you can borrow.</p>
            </li>
            <li class="ms glass">
              <span class="ms-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 3v18M5 21h14M6 7h12M6 7l-3 7a3 3 0 006 0zM18 7l-3 7a3 3 0 006 0z"></path></svg></span>
              <small>Step 2</small><b>Compare the market</b>
              <p>We compare mortgage products from the banks we work with and shortlist the best fit for you.</p>
            </li>
            <li class="ms glass">
              <span class="ms-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M9 12l2 2 4-4"></path><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path></svg></span>
              <small>Step 3</small><b>Pre-approval &amp; valuation</b>
              <p>We prepare your documents, secure a pre-approval and coordinate the bank’s property valuation.</p>
            </li>
            <li class="ms glass">
              <span class="ms-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 11l9-7 9 7"></path><path d="M5 10v10h14V10"></path><path d="M10 20v-5h4v5"></path></svg></span>
              <small>Step 4</small><b>Final offer &amp; keys</b>
              <p>From the final offer letter to transfer at the Land Department — we stay with you until you get the keys.</p>
            </li>
          <?php } ?>
        </ol>
      </div>
    </section>

    <!-- ===== LENDERS / BANKS ===== -->
    <?php if (!empty($data['banks'])) { ?>
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="mc2-lend glass acc-edge reveal">
          <div>
            <span class="eyebrow">Our lending partners</span>
            <h2 class="serif">
              <?= gold_last_word($data['bank_heading']) ?>
            </h2>
            <div class="story-cta">
              <a class="btn btn-gold" href="#contact">Apply for a mortgage →</a
              ><a class="btn btn-glass" href="#calc">Calculate repayments</a>
            </div>
          </div>
          <div class="mc2-logos">
            <?php foreach ($data['banks'] as $b) { ?>
              <span class="mc2-lg">
                <img src="<?= htmlspecialchars($b['logo']) ?>" alt="<?= htmlspecialchars($b['alt']) ?>" loading="lazy" />
              </span>
            <?php } ?>
          </div>
        </div>
      </div>
    </section>
    <?php } ?>

    <!-- ===== CALL STRIP ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="mg-call glass acc-edge reveal">
          <div>
            <span class="eyebrow">Talk to us</span>
            <h3 class="serif">Our consultants are <em class="gold-text">ready to assist you.</em></h3>
          </div>
          <div class="mc-acts">
            <?php
              $phone_num = !empty($footer_info['footer_contact_number2']) ? $footer_info['footer_contact_number2'] : (!empty($footer_info['footer_contact_number']) ? $footer_info['footer_contact_number'] : '+971 52 754 3243');
              $email_addr = !empty($footer_info['footer_email_id']) ? $footer_info['footer_email_id'] : 'sales@drehomes.com';
            ?>
            <a class="mc" href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', $phone_num)) ?>">
              <span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2"></path>
                </svg>
              </span>
              <b><?= htmlspecialchars($phone_num) ?></b>
              <small>Call DRE</small>
            </a>
            <a class="mc" href="mailto:<?= htmlspecialchars($email_addr) ?>">
              <span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                  <path d="M3 7l9 6 9-6"></path>
                </svg>
              </span>
              <b><?= htmlspecialchars($email_addr) ?></b>
              <small>Email us</small>
            </a>
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
            <span class="eyebrow"><?= htmlspecialchars($data['frm_heading']) ?></span>
            <h2 class="serif">Let’s find your<br /><em class="gold-text">mortgage.</em></h2>
            <p class="lead">
              <?= htmlspecialchars($data['frm_sub_heading']) ?>. Toll free <?= htmlspecialchars($footer_info['footer_contact_number'] ?? '800 37373') ?> · Hotline <?= htmlspecialchars($footer_info['footer_contact_number2'] ?? '+971 52 754 3243') ?>
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
            <select name="service" id="ctaSvc" aria-label="I need help with">
              <option value="">I need help with…</option>
              <option>Mortgage on a new or off-plan property</option>
              <option>Mortgage on ready properties</option>
              <option>Equity Release</option>
              <option>Remortgage / Buyout</option>
              <option>Not sure yet</option>
            </select>
            <input name="message" placeholder="Property value, budget or questions (optional)" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Submit application</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>

