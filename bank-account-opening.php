<?php
// 1. CONFIG
include('db/config.php');

// 2. DATA: Bank Account Opening (/bank-account-opening, see .htaccess) — design: bank-account-opening.html
// Admin > Services > Edit Service Section (tbl_service_section, id = 7; edit-service-section.php?edit=Nw==)
$data = get_bank_account_opening_data();
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

// 3. SEO: tbl_service_section id = 7 (Admin > Services > edit-service-section.php?edit=Nw==)
$page = [
    'active' => 'services',
    'title' => $meta_tags['meta_title'] ?? 'Bank Account Opening | DRE Homes',
    'description' => $meta_tags['meta_description'] ?? 'How to open a bank account in the UAE — a simple guide for citizens, residents and non-residents.',
    'css' => ['assets/css/pages/bank-account-opening.css'],
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
        <span class="eyebrow">Services · Banking</span>
        <h1 class="serif"><?= $hero_title_html ?></h1>
        <p class="sub">
          <?= e($meta_tags['meta_description']) ?>
        </p>
        <div class="story-cta">
          <a class="btn btn-gold" href="#contact">Get help opening an account →</a
          ><a class="btn btn-glass" href="tel:+971527543243">Call +971 52 754 3243</a>
        </div>
        <div class="dp-stats glass reveal">
          <div><small>Guides for</small><b>Citizens · Residents · Non-residents</b></div>
          <div><small>Banking</small><b>Islamic &amp; conventional</b></div>
          <div><small>IBAN</small><b>Mandatory since 2012</b></div>
          <div><small>Joint accounts</small><b>Available</b></div>
        </div>
      </div>
    </header>

    <!-- ===== INTRO ===== -->
    <section style="padding-top: 70px">
      <div class="wrap mg-intro">
        <div class="reveal">
          <span class="eyebrow">How to open a bank account in the UAE</span>
          <h2 class="serif">A local account, <em class="gold-text">made simple.</em></h2>
          <p class="lead">
            Opening a bank account in the UAE is generally a smooth process for citizens and residents, while
            non-residents can also access banking services with additional documentation and compliance checks. Whether
            you’re living, working, or investing in the UAE, having a local bank account is essential for handling your
            finances efficiently.
          </p>
          <p class="lead">
            Here is a guide to help you through the requirements for opening a bank account in the UAE for all three
            categories of individuals — and the importance of the IBAN system.
          </p>
        </div>
        <div class="mi-num glass reveal">
          <span class="mx-ic"
            ><svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.6"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <circle cx="12" cy="12" r="9"></circle>
              <path d="M12 11v6M12 7.5v.01"></path></svg></span
          ><span class="mi-t"
            >Always check with the bank for the most up-to-date requirements, especially if you’re a non-resident or
            looking to open a joint or offshore account.</span
          ><small>Requirements vary by bank</small>
        </div>
      </div>
    </section>

    <!-- ===== WHO ARE YOU ===== -->
    <section id="guide" style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Requirements</span>
            <h2 class="serif">Which one <em class="gold-text">are you?</em></h2>
          </div>
        </div>
        <div class="reveal">
          <div class="sv-tabs glass" role="tablist" aria-label="Applicant type">
            <button role="tab" id="bk-t0" aria-controls="bk-p0" aria-selected="true" class="on">
              <span
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M5 21V4M5 4h11l-2 4 2 4H5"></path></svg></span
              >UAE citizen</button
            ><button role="tab" id="bk-t1" aria-controls="bk-p1" aria-selected="false" class="">
              <span
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                  <circle cx="9" cy="12" r="2.5"></circle>
                  <path d="M14 10h4M14 14h3"></path></svg></span
              >UAE resident</button
            ><button role="tab" id="bk-t2" aria-controls="bk-p2" aria-selected="false" class="">
              <span
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M2 16l20-8-6 13-3-6z"></path>
                  <path d="M13 15l-4 4"></path></svg></span
              >Non-resident
            </button>
          </div>
          <div class="sv-panes">
            <div role="tabpanel" id="bk-p0" aria-labelledby="bk-t0" class="sv-pane">
              <div class="pane-g">
                <div>
                  <h3 class="serif">For UAE <em class="gold-text">citizens</em></h3>
                  <p class="lead">
                    Citizens of the UAE can open accounts with conventional or Islamic banks operating in the country.
                  </p>
                  <p class="pn-note">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.6"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <circle cx="9" cy="8" r="3.5"></circle>
                      <circle cx="17" cy="9" r="2.5"></circle>
                      <path d="M2 21a7 7 0 0114 0M15 15a5 5 0 017 5"></path></svg
                    >Citizens can open individual or joint accounts, subject to the policies of the bank.
                  </p>
                </div>
                <div class="ckl" data-ckl="cit">
                  <div class="ckl-h">
                    <span class="eyebrow">Your document checklist</span><b class="ckl-n">0 of 2 ready</b>
                  </div>
                  <div class="ckl-bar"><i></i></div>
                  <ul>
                    <li>
                      <label
                        ><input type="checkbox" /><span class="sv-cbx"
                          ><svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          >
                            <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                        ><span>A copy of the passport or family book (Khulasat Al Qaid)</span></label
                      >
                    </li>
                    <li>
                      <label
                        ><input type="checkbox" /><span class="sv-cbx"
                          ><svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          >
                            <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                        ><span>A salary certificate or a No Objection Certificate (NOC) from the employer</span></label
                      >
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div role="tabpanel" id="bk-p1" aria-labelledby="bk-t1" class="sv-pane" hidden="">
              <div class="pane-g">
                <div>
                  <h3 class="serif">For UAE <em class="gold-text">residents</em></h3>
                  <p class="lead">
                    UAE residents, including expatriates living and working in the country, are eligible to open bank
                    accounts as long as they hold a valid residence visa.
                  </p>
                  <p class="pn-note">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.6"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <circle cx="9" cy="8" r="3.5"></circle>
                      <circle cx="17" cy="9" r="2.5"></circle>
                      <path d="M2 21a7 7 0 0114 0M15 15a5 5 0 017 5"></path></svg
                    >Residents can also open joint accounts, often with a spouse or family member, but requirements vary
                    between banks.
                  </p>
                </div>
                <div class="ckl" data-ckl="res">
                  <div class="ckl-h">
                    <span class="eyebrow">Your document checklist</span><b class="ckl-n">0 of 3 ready</b>
                  </div>
                  <div class="ckl-bar"><i></i></div>
                  <ul>
                    <li>
                      <label
                        ><input type="checkbox" /><span class="sv-cbx"
                          ><svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          >
                            <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                        ><span>A copy of the passport with a valid residence visa</span></label
                      >
                    </li>
                    <li>
                      <label
                        ><input type="checkbox" /><span class="sv-cbx"
                          ><svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          >
                            <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                        ><span>A copy of the Emirates ID card</span></label
                      >
                    </li>
                    <li>
                      <label
                        ><input type="checkbox" /><span class="sv-cbx"
                          ><svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          >
                            <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                        ><span>A salary certificate or NOC from the employer or sponsor</span></label
                      >
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div role="tabpanel" id="bk-p2" aria-labelledby="bk-t2" class="sv-pane" hidden="">
              <div class="pane-g">
                <div>
                  <h3 class="serif">For <em class="gold-text">non-residents</em></h3>
                  <p class="lead">
                    Non-residents — those who do not hold a UAE residence visa — can also open limited-functionality
                    accounts, such as savings accounts or investment accounts. However, the process may be more
                    selective and restrictive.
                  </p>
                  <div class="sv-warn">
                    <b
                      ><svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      >
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 11v6M12 7.5v.01"></path></svg
                      >Note: non-resident accounts may</b
                    >
                    <ul class="sv-ul">
                      <li>Have minimum balance requirements</li>
                      <li>Not include checkbooks</li>
                      <li>Be subject to limited transaction types</li>
                      <li>Take longer to open due to due diligence and compliance checks</li>
                    </ul>
                  </div>
                  <p class="pn-note">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.6"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path d="M12 3l2.6 5.6 6.1.7-4.5 4.2 1.2 6L12 16.6 6.6 19.5l1.2-6L3.3 9.3l6.1-.7z"></path></svg
                    >Some international and private banks in the UAE specifically cater to high-net-worth individuals,
                    investors, or offshore account holders.
                  </p>
                </div>
                <div class="ckl" data-ckl="non">
                  <div class="ckl-h">
                    <span class="eyebrow">Your document checklist</span><b class="ckl-n">0 of 4 ready</b>
                  </div>
                  <div class="ckl-bar"><i></i></div>
                  <ul>
                    <li>
                      <label
                        ><input type="checkbox" /><span class="sv-cbx"
                          ><svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          >
                            <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                        ><span>Passport copy</span></label
                      >
                    </li>
                    <li>
                      <label
                        ><input type="checkbox" /><span class="sv-cbx"
                          ><svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          >
                            <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                        ><span>Recent utility bill or proof of address (in the home country)</span></label
                      >
                    </li>
                    <li>
                      <label
                        ><input type="checkbox" /><span class="sv-cbx"
                          ><svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          >
                            <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                        ><span
                          >Bank reference letter or bank statements from an existing account (usually 3–6 months)</span
                        ></label
                      >
                    </li>
                    <li>
                      <label
                        ><input type="checkbox" /><span class="sv-cbx"
                          ><svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          >
                            <path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></span
                        ><span>A CV or information on the source of funds/income</span></label
                      >
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== JOINT + CONSIDER ===== -->
    <section style="padding-top: 30px">
      <div class="wrap sv-duo2 reveal">
        <div class="sv-d2 glass">
          <span class="mx-ic"
            ><svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.6"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <circle cx="9" cy="8" r="3.5"></circle>
              <circle cx="17" cy="9" r="2.5"></circle>
              <path d="M2 21a7 7 0 0114 0M15 15a5 5 0 017 5"></path></svg></span
          ><span class="eyebrow">Joint accounts in the UAE</span>
          <h3 class="serif">Banking <em class="gold-text">together.</em></h3>
          <p>
            Both citizens and residents can open joint bank accounts with one another. The process typically requires
            both parties to be present during the application and provide full sets of identification documents.
            Policies and limits for joint accounts differ by bank, so always confirm with your chosen institution.
          </p>
        </div>
        <div class="sv-d2 glass">
          <span class="mx-ic"
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
          ><span class="eyebrow">Before you open an account</span>
          <h3 class="serif">Things to <em class="gold-text">consider.</em></h3>
          <ul class="sv-ul">
            <li>Minimum balance and monthly fees can vary</li>
            <li>Decide between Islamic and conventional banking</li>
            <li>Check if the bank offers online banking or multi-currency accounts</li>
            <li>Ask about account opening timelines, especially for non-residents</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- ===== IBAN ===== -->
    <section id="iban" style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">IBAN in the UAE</span>
            <h2 class="serif">What you <em class="gold-text">need to know.</em></h2>
          </div>
        </div>
        <div class="sv-iban glass reveal">
          <div class="sv-ib-l">
            <b class="gold-text">2012</b>
            <p>
              Since 2012, the use of the International Bank Account Number (IBAN) has been mandatory for all electronic
              transfers within and outside the UAE, excluding credit card payments.
            </p>
            <div class="sv-ib-demo" aria-label="Example IBAN format">
              <small>UAE IBAN format</small>
              <div class="ib-seg">
                <span><code>AE</code><i>Country</i></span
                ><span><code>07</code><i>Check</i></span
                ><span><code>033</code><i>Bank</i></span
                ><span><code>1234567890123456</code><i>Account no.</i></span>
              </div>
            </div>
          </div>
          <div class="sv-ib-c">
            <h3>What is an IBAN?</h3>
            <ul class="sv-ul">
              <li>A globally recognised format for identifying bank accounts</li>
              <li>Ensures accuracy and speed in cross-border and domestic payments</li>
              <li>Complies with the ISO 13616 standard</li>
            </ul>
          </div>
          <div class="sv-ib-c">
            <h3>Key points</h3>
            <ul class="sv-ul">
              <li>Every bank account in the UAE is assigned a unique IBAN</li>
              <li>You will have a different IBAN for each of your accounts, even if they are within the same bank</li>
              <li>Banks are required by law to inform you of your IBAN</li>
            </ul>
          </div>
          <div class="sv-ib-c">
            <h3>You’ll need your IBAN for</h3>
            <ul class="sv-ul">
              <li>Salary transfers</li>
              <li>Receiving international funds</li>
              <li>Paying utility bills</li>
              <li>Submitting bank details for business or freelance income</li>
            </ul>
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
            <span class="eyebrow">Get help opening an account</span>
            <h2 class="serif">Banking in the UAE,<br /><em class="gold-text">sorted.</em></h2>
            <p class="lead">
              Call +971 52 754 3243 or email sales@drehomes.com — or leave your details and a consultant will walk you
              through it.
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
            <select name="service" id="ctaSvc" aria-label="I am a">
              <option value="">I am a…</option>
              <option>UAE citizen</option>
              <option>UAE resident</option>
              <option>Non-resident</option>
              <option>Company / corporate</option>
            </select>
            <input name="message" placeholder="Anything else we should know? (optional)" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>

