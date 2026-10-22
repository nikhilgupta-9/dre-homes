<?php
// 1. CONFIG
require_once('db/config.php');

// 2. SEO: Admin > Manage Tags ("Mortgage Calculator", id = 28, page_id = 29)
$data = get_mortgage_calculator_page_data();
$meta_tags = $data['meta_tags'];

// 3. DATA: Admin > Mortgage Content (tbl_mortgage_content) & Partner Banks (tbl_mortg_bank)
$page = [
  'active' => 'services',
  'css' => ['assets/css/pages/mortgage-calculator.css'],
];

include('common/header.php');
?>

    <!-- ===== CALCULATOR: HERO ===== -->
    <header class="ph-hero mg-hero mc2-hero">
      <div
        class="ph-bg"
        style="
          background-image: url('<?= htmlspecialchars($data['banner_img'], ENT_QUOTES, 'UTF-8') ?>');
        "
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= page_link('index') ?>">Home</a><span>/</span><span>Guide</span><span>/</span
          ><span aria-current="page">Mortgage Calculator</span>
        </div>
        <span class="eyebrow"><?= e($data['eyebrow']) ?></span>
        <h1 class="serif">Mortgage <em class="gold-text">Calculator</em></h1>
        <p class="sub">
          <?= e($data['sub_heading']) ?>
        </p>
        <div class="story-cta">
          <a class="btn btn-gold" href="#calc">Calculate my payment →</a
          ><a class="btn btn-glass" href="<?= page_link('mortgage-advisory') ?>">Talk to a mortgage advisor</a>
        </div>
      </div>
    </header>

    <!-- ===== CALCULATOR ===== -->
    <section id="calc" style="padding-top: 60px">
      <div class="wrap">
        <div class="mc2 glass reveal">
          <div class="mc2-in">
            <div class="mc2-top">
              <span class="eyebrow">Your numbers</span>
              <div class="mg-type" role="group" aria-label="Buyer type">
                <button type="button" class="on" data-min="20">Expat</button
                ><button type="button" data-min="15">UAE national</button>
              </div>
            </div>
            <div class="mc2-f">
              <label for="cPrice"><span>Purchase price</span></label>
              <div class="mv"><span>AED</span><input id="cPrice" inputmode="numeric" value="2,000,000" /></div>
              <input
                type="range"
                id="cPriceR"
                min="250000"
                max="20000000"
                step="50000"
                value="2000000"
                aria-label="Purchase price slider"
              />
            </div>
            <div class="mc2-f">
              <label for="cDp"
                ><span>Down payment</span><b><em id="cDpV">20%</em> · <i id="cDpA">—</i></b></label
              ><input type="range" id="cDp" min="20" max="80" step="1" value="20" />
            </div>
            <div class="mc2-f">
              <label for="cYr"><span>Loan duration</span><b id="cYrV">25 years</b></label
              ><input type="range" id="cYr" min="1" max="25" step="1" value="25" />
            </div>
            <div class="mc2-f">
              <label for="cRt"
                ><span>Interest rate (annual)</span
                ><b
                  ><span class="mc2-rt"
                    ><input
                      id="cRtN"
                      type="number"
                      min="0"
                      max="15"
                      step="0.05"
                      value="4.5"
                      aria-label="Interest rate"
                    />%</span
                  ></b
                ></label
              ><input type="range" id="cRt" min="0" max="10" step="0.05" value="4.5" />
            </div>
            <p class="mc2-note">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 11v6M12 7.5v.01"></path></svg
              ><span id="cMinNote">Expats usually need at least 20% down on a first home under AED 5M.</span>
            </p>
          </div>
          <div class="mc2-out" aria-live="polite">
            <div class="mc2-seg" role="group" aria-label="Payment frequency">
              <button type="button" class="on" data-f="m">Monthly</button
              ><button type="button" data-f="y">Annual</button>
            </div>
            <small id="cLbl">Estimated monthly payment</small>
            <div class="m-big gold-text" id="cOut">—</div>
            <div class="mc2-chart">
              <svg viewBox="0 0 120 120" class="mc2-donut" aria-hidden="true">
                <circle cx="60" cy="60" r="48" class="trk"></circle>
                <circle cx="60" cy="60" r="48" id="dDown" class="seg s1"></circle>
                <circle cx="60" cy="60" r="48" id="dLoan" class="seg s2"></circle>
                <circle cx="60" cy="60" r="48" id="dInt" class="seg s3"></circle>
              </svg>
              <div class="mc2-dc"><small>Total cost</small><b id="cTot">—</b></div>
            </div>
            <ul class="mc2-leg">
              <li><i class="s1"></i><span>Down payment</span><b id="cDown">—</b></li>
              <li><i class="s2"></i><span>Loan amount</span><b id="cLoan">—</b></li>
              <li><i class="s3"></i><span>Total interest</span><b id="cInt">—</b></li>
            </ul>
            <div class="mc2-inc">
              <span
                >Monthly income needed
                <i title="UAE banks typically cap total debt repayments at 50% of monthly income"
                  ><svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 11v6M12 7.5v.01"></path></svg></i></span
              ><b id="cInc">—</b>
            </div>
            <div class="mc2-acts">
              <a class="btn btn-gold" href="#contact" id="cApply">Apply for a loan →</a
              ><button class="btn btn-glass" type="button" id="cShare">
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M10 14a5 5 0 007 0l3-3a5 5 0 00-7-7l-1 1"></path>
                  <path d="M14 10a5 5 0 00-7 0l-3 3a5 5 0 007 7l1-1"></path></svg
                ><span>Share estimate</span>
              </button>
            </div>
          </div>
        </div>

        <div class="mc2-more reveal">
          <div class="mc2-up glass">
            <span class="eyebrow">Cash needed upfront (approx.)</span>
            <ul class="mc2-ul">
              <li><span>Down payment</span><b id="uDown">—</b></li>
              <li><span>DLD transfer fee · 4%</span><b id="uDld">—</b></li>
              <li><span>Mortgage registration · 0.25% of loan</span><b id="uReg">—</b></li>
              <li class="tot"><span>Total</span><b id="uTot">—</b></li>
            </ul>
            <p class="mc2-fine">
              Indicative only — excludes bank processing, valuation and agency fees. Rates depend on the bank and your
              profile.
            </p>
          </div>
          <div class="mc2-sch glass">
            <div class="mc2-sh">
              <div><span class="eyebrow">Loan balance over time</span><b class="mc2-st" id="cHalf">—</b></div>
              <div class="mc2-k">
                <span><i class="s2"></i>Principal paid</span><span><i class="s3"></i>Interest paid</span>
              </div>
            </div>
            <div class="mc2-bars" id="cBars" role="img" aria-label="Yearly breakdown of principal and interest"></div>
            <details class="mc2-tbl">
              <summary>View yearly schedule</summary>
              <div class="mc2-tw">
                <table>
                  <thead>
                    <tr>
                      <th>Year</th>
                      <th>Principal</th>
                      <th>Interest</th>
                      <th>Balance</th>
                    </tr>
                  </thead>
                  <tbody id="cRows"></tbody>
                </table>
              </div>
            </details>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== LENDERS ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="mc2-lend glass acc-edge reveal">
          <div>
            <span class="eyebrow">Our lending partners</span>
            <h2 class="serif">
              <?= $data['bank_heading_html'] ?>
            </h2>
            <div class="story-cta">
              <a class="btn btn-gold" href="<?= page_link('mortgage-advisory') ?>">Mortgage advisory →</a
              ><a class="btn btn-glass" href="#contact">Get pre-approved</a>
            </div>
          </div>
          <?php if (!empty($data['banks'])) { ?>
          <div class="mc2-logos">
            <?php foreach ($data['banks'] as $bank) { ?>
            <span class="mc2-lg"
              ><img
                src="<?= e($bank['logo']) ?>"
                alt="<?= e($bank['name']) ?>"
                loading="lazy"
            /></span>
            <?php } ?>
          </div>
          <?php } ?>
        </div>
      </div>
    </section>

    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Apply for a loan</span>
            <h2 class="serif">Like the numbers?<br /><em class="gold-text">Let’s make it real.</em></h2>
            <p class="lead">
              Leave your details and a DRE mortgage advisor will compare offers from our lending partners and help you
              get pre-approved.
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
            <input name="message" placeholder="Loan details or questions (optional)" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Apply for a loan</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>

