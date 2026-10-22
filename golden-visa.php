<?php
require_once('db/config.php');

$data = get_golden_visa_page_data();
$meta_tags = $data['meta_tags'];

$page = [
  'active' => 'services',
  'css' => ['assets/css/pages/golden-visa.css'],
];

include('common/header.php');
?>

    <!-- ===== GOLDEN VISA: HERO ===== -->
    <header class="ph-hero mg-hero gv-hero">
      <div
        class="ph-bg"
        style="
          background-image: url('<?= htmlspecialchars($data['banner_img']) ?>');
        "
      ></div>
      <div class="wrap gv-hg">
        <div>
          <div class="crumbs" role="navigation" aria-label="Breadcrumb">
            <a href="<?= page_link('index') ?>">Home</a><span>/</span><span aria-current="page">Golden Visa</span>
          </div>
          <span class="eyebrow">Residency through property</span>
          <h1 class="serif"><?= gold_last_word($data['title']) ?></h1>
          <p class="sub">
            <b><?= htmlspecialchars($data['heading']) ?>.</b> <?= htmlspecialchars($data['sub_heading']) ?>
          </p>
          <div class="story-cta">
            <a class="btn btn-gold" href="#elig">Check my eligibility →</a>
            <a class="btn btn-glass" href="#contact">Claim your visa</a>
          </div>
        </div>
        <div class="gv-cards" aria-hidden="true">
          <img src="<?= asset_url('images/bg/golden-visa-cards.png') ?>" alt="UAE Golden Visa Cards" />
        </div>
      </div>
    </header>

    <!-- ===== ELIGIBILITY ===== -->
    <section id="elig" style="padding-top: 60px">
      <div class="wrap">
        <div class="gv-el glass reveal">
          <div class="gv-el-l">
            <span class="eyebrow">Eligibility check</span>
            <h2 class="serif">Do you <em class="gold-text">qualify?</em></h2>
            <p class="lead">
              Property investments of AED 2M and above can qualify you and your family for 10-year residency.
            </p>
            <div class="mc2-f">
              <label for="gvV"><span>Property value</span></label>
              <div class="mv"><span>AED</span><input id="gvV" inputmode="numeric" value="2,500,000" /></div>
              <input
                type="range"
                id="gvR"
                min="500000"
                max="10000000"
                step="50000"
                value="2500000"
                aria-label="Property value slider"
              />
            </div>
            <div class="mc2-f">
              <label for="gvS"><span>Your ownership share</span><b id="gvSV">100%</b></label>
              <input type="range" id="gvS" min="10" max="100" step="5" value="100" />
            </div>
            <div class="gv-tg">
              <span class="bv-l">Property status</span>
              <div class="mg-type" role="group" aria-label="Property status">
                <button type="button" class="on" data-st="ready">Ready</button>
                <button type="button" data-st="offplan">Off-plan</button>
              </div>
              <span class="bv-l">Mortgaged?</span>
              <div class="mg-type" role="group" aria-label="Mortgage">
                <button type="button" class="on" data-mg="no">No</button>
                <button type="button" data-mg="yes">Yes</button>
              </div>
            </div>
          </div>
          <div class="gv-el-r" aria-live="polite">
            <div class="gv-meter">
              <div class="gv-track">
                <i id="gvFill"></i><span class="gv-mark" style="--m: 0%"><b>AED 2M</b></span>
              </div>
              <div class="gv-scale"><span>AED 0.5M</span><span>AED 10M+</span></div>
            </div>
            <small class="gv-yl">Your qualifying investment</small>
            <div class="m-big gold-text" id="gvYour">—</div>
            <div class="gv-res" id="gvRes"></div>
            <ul class="gv-notes" id="gvNotes"></ul>
            <a class="btn btn-gold" href="#contact" id="gvClaim">Claim your visa →</a>
            <p class="mc2-fine">
              Indicative only — final eligibility is decided by the UAE authorities. Our advisors will confirm your
              case.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== BENEFITS ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="gv-ben reveal">
          <div>
            <span class="eyebrow">Own a home</span>
            <h2 class="serif">Earn a <em class="gold-text">decade</em> in the UAE.</h2>
            <p class="lead">
              We manage it end to end — from finding a qualifying home to document preparation and submission.
            </p>
          </div>
          <ul class="gv-bl">
            <li class="gv-b">
              <span class="mx-ic"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <rect x="3" y="5" width="18" height="16" rx="3"></rect>
                  <path d="M3 10h18M8 3v4M16 3v4"></path></svg></span
              ><b>10-year residency</b><span>Renewable long-term residency in the UAE.</span>
            </li>
            <li class="gv-b">
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
              ><b>For your family</b><span>Your family can be included on the same visa.</span>
            </li>
            <li class="gv-b">
              <span class="mx-ic"
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
              ><b>No local sponsor</b><span>Live in the UAE without needing a sponsor.</span>
            </li>
            <li class="gv-b">
              <span class="mx-ic"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M4 21V5l10-2v18"></path>
                  <path d="M14 7h6v4"></path>
                  <path d="M20 11v3"></path>
                  <path d="M8 21v-4h3v4"></path>
                  <path d="M2 21h20"></path></svg></span
              ><b>Off-plan &amp; mortgaged</b><span>Off-plan and mortgaged properties may qualify.</span>
            </li>
          </ul>
        </div>
      </div>
    </section>

    <!-- ===== QUALIFYING HOMES ===== -->
    <?php if (!empty($data['qualifying_homes'])): ?>
    <section id="homes" style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Homes from AED 2M</span>
            <h2 class="serif">Properties that <em class="gold-text">can qualify.</em></h2>
          </div>
          <a class="btn btn-glass" href="<?= page_link('offplan-properties') ?>">All off-plan projects →</a>
        </div>
        <div class="gv-hg2 reveal">
          <?php foreach ($data['qualifying_homes'] as $home): ?>
          <a class="gv-h glass" href="<?= htmlspecialchars($home['url']) ?>">
            <span class="gv-hi">
              <?php if (!empty($home['thumb'])): ?>
              <img
                src="<?= htmlspecialchars($home['thumb']) ?>"
                alt="<?= htmlspecialchars($home['name']) ?>"
                loading="lazy"
              />
              <?php endif; ?>
              <span class="gv-ok">
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M9 12l2 2 4-4"></path>
                  <circle cx="12" cy="12" r="9"></circle>
                </svg>Golden Visa eligible price
              </span>
            </span>
            <span class="gv-hb">
              <small><?= htmlspecialchars($home['developer']) ?></small>
              <b><?= htmlspecialchars($home['name']) ?></b>
              <span class="gv-hl">
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M12 21s-7-6.2-7-11.5a7 7 0 0114 0C19 14.8 12 21 12 21z"></path>
                  <circle cx="12" cy="9.5" r="2.5"></circle>
                </svg><?= htmlspecialchars($home['location']) ?>
              </span>
              <span class="gv-hp"><small>From</small><?= htmlspecialchars($home['price_formatted']) ?></span>
            </span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($data['facilities'])): ?>
    <!-- ===== VIP FACILITIES / PRIVILEGES (manage-facilities.php) ===== -->
    <section id="privileges" style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Investor Privileges</span>
            <h2 class="serif">Complimentary <em class="gold-text">VIP Services.</em></h2>
          </div>
        </div>
        <div class="gv-fg reveal">
          <?php foreach ($data['facilities'] as $fac): ?>
          <div class="gv-fc glass">
            <span class="gv-fc-ic">
              <img src="<?= htmlspecialchars($fac['image']) ?>" alt="<?= htmlspecialchars($fac['alt']) ?>" width="36" height="36" loading="lazy" />
            </span>
            <h3><?= htmlspecialchars($fac['heading']) ?></h3>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ===== REASONS (manage-services.php) ===== -->
    <?php if (!empty($data['services'])): ?>
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Reasons to call Dubai home</span>
            <h2 class="serif">Why the world <em class="gold-text">moves here.</em></h2>
          </div>
        </div>
        <div class="gv-rg reveal">
          <?php foreach ($data['services'] as $svc): ?>
          <article class="gv-r glass">
            <span class="gv-rn"><?= htmlspecialchars($svc['num_str']) ?></span>
            <span class="mx-ic"><?= $svc['svg_icon'] ?></span>
            <h3><?= htmlspecialchars($svc['heading']) ?></h3>
            <p><?= htmlspecialchars($svc['text']) ?></p>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ===== PROCESS ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">How DRE helps</span>
            <h2 class="serif">From home <em class="gold-text">to visa.</em></h2>
          </div>
        </div>
        <ol class="ms-grid reveal">
          <li class="ms glass">
            <span class="ms-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M9 12l2 2 4-4"></path>
                <circle cx="12" cy="12" r="9"></circle></svg></span
            ><small>Step 1</small><b>Check eligibility</b>
            <p>Use the checker above or talk to us — we confirm which of your properties count.</p>
          </li>
          <li class="ms glass">
            <span class="ms-ic"
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
            ><small>Step 2</small><b>Choose your property</b>
            <p>Shortlist ready or off-plan homes of AED 2M+ in Dubai with your DRE advisor.</p>
          </li>
          <li class="ms glass">
            <span class="ms-ic"
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
            ><small>Step 3</small><b>We handle the paperwork</b>
            <p>Document preparation and submission, start to finish.</p>
          </li>
          <li class="ms glass">
            <span class="ms-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="2" y="5" width="20" height="14" rx="3"></rect>
                <path d="M2 10h20M6 15h4"></path></svg></span
            ><small>Step 4</small><b>Receive your visa</b>
            <p>Collect your Golden Visa and settle into your new home in Dubai.</p>
          </li>
        </ol>
      </div>
    </section>

    <!-- ===== CONTACT CTA ===== -->
    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Claim your visa</span>
            <h2 class="serif">Your decade in the UAE<br /><em class="gold-text">starts here.</em></h2>
            <p class="lead">
              Fill out the form to get started and learn more about this exclusive opportunity. Toll free <?= htmlspecialchars($footer_info['toll_free_number'] ?? '800 37373') ?> ·
              Hotline <?= htmlspecialchars($footer_info['hotline_number'] ?? '+971 52 754 3243') ?>
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
            <input name="email" type="email" placeholder="Email address" autocomplete="email" />
            <input name="message" placeholder="Message (optional)" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Claim my Golden Visa</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>

