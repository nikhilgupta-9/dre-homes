<?php
// 1. CONFIG
include('db/config.php');

// 2. DATA: Business Licenses (/business-licenses, see .htaccess) — design: business-licenses.html
// Admin > Services > Edit Service Section (tbl_service_section, id = 8; edit-service-section.php?edit=OA==)
$data = get_business_licenses_data();
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

// 3. SEO: tbl_service_section id = 8 (Admin > Services > edit-service-section.php?edit=OA==)
$page = [
    'active' => 'services',
    'title' => $meta_tags['meta_title'] ?? 'Business Licenses | DRE Homes',
    'description' => $meta_tags['meta_description'] ?? 'Everything you need to set up a business in the UAE — license types, legal structures, documents and the key steps to get started.',
    'css' => ['assets/css/pages/business-licenses.css'],
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
        <span class="eyebrow">Services · Business setup</span>
        <h1 class="serif"><?= $hero_title_html ?></h1>
        <p class="sub">
          <?= e($meta_tags['meta_description']) ?>
        </p>
        <div class="story-cta">
          <a class="btn btn-gold" href="#contact">Talk to a setup advisor →</a
          ><a class="btn btn-glass" href="tel:+971527543243">Call +971 52 754 3243</a>
        </div>
        <div class="dp-stats glass reveal">
          <div><small>License types</small><b>3</b></div>
          <div><small>Business activities</small><b>2,000+</b></div>
          <div><small>Instant license</small><b>5–10 min</b></div>
          <div><small>Key steps</small><b>8</b></div>
        </div>
      </div>
    </header>

    <!-- ===== INTRO ===== -->
    <section style="padding-top: 70px">
      <div class="wrap mg-intro">
        <div class="mi-num glass reveal">
          <b class="gold-text">2,000+</b
          ><span>listed business activities to choose from across commercial, industrial and professional sectors.</span
          ><small>Getting the license right is mandatory</small>
        </div>
        <div class="reveal">
          <span class="eyebrow">Setting up in the UAE</span>
          <h2 class="serif">Start right. <em class="gold-text">Start legally.</em></h2>
          <p class="lead">
            Setting up a business in the UAE is an exciting opportunity thanks to its strategic location,
            business-friendly environment, and efficient digital government systems. Whether you’re opening a physical
            store, launching an online brand, or setting up a consultancy, obtaining the correct business license is
            mandatory to operate legally.
          </p>
          <p class="lead">
            Below is a comprehensive overview of the types of licenses, legal structures, and key steps to help you get
            started.
          </p>
        </div>
      </div>
    </section>

    <!-- ===== LICENSE TYPES ===== -->
    <section id="types" style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Types of business licenses in the UAE</span>
            <h2 class="serif">Pick the license <em class="gold-text">that fits.</em></h2>
          </div>
        </div>
        <div class="sv-lc-grid reveal">
          <article class="sv-lc glass">
            <div class="sv-lc-top">
              <span class="mx-ic"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M3 9l2-5h14l2 5"></path>
                  <path d="M3 9a3 3 0 006 0 3 3 0 006 0 3 3 0 006 0"></path>
                  <path d="M5 12v9h14v-9M10 21v-5h4v5"></path></svg></span
              ><span class="sv-lc-time"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <circle cx="12" cy="12" r="9"></circle>
                  <path d="M12 7v5l3 2"></path></svg
                ><b>~1–3 business days</b></span
              >
            </div>
            <h3>Normal License</h3>
            <p>Suitable for traditional businesses with a physical location.</p>
            <ul class="sv-ul">
              <li>Requires a lease agreement and a memorandum of association (MoA)</li>
              <li>Application processed through service centres or online via the DED portal</li>
            </ul>
            <button class="mx-go" type="button" data-svc="Normal License">Ask about the Normal license →</button>
          </article>
          <article class="sv-lc glass">
            <div class="sv-lc-top">
              <span class="mx-ic"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M13 2L4 14h7l-1 8 9-12h-7z"></path></svg></span
              ><span class="sv-lc-time"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <circle cx="12" cy="12" r="9"></circle>
                  <path d="M12 7v5l3 2"></path></svg
                ><b>5–10 minutes</b></span
              >
            </div>
            <h3>Instant License</h3>
            <p>Issued within minutes for businesses that do not need external approvals.</p>
            <ul class="sv-ul">
              <li>No lease contract required for the first year (virtual office is allowed)</li>
              <li>Available for: LLC, LLC-SO, Sole Establishment, and Civil Company</li>
            </ul>
            <div class="sv-lc-inc">
              <small>Includes</small>
              <ul class="sv-ul">
                <li>Dubai Chamber membership</li>
                <li>Establishment card from GDRFA</li>
                <li>MOHRE card (to hire up to 3 employees)</li>
              </ul>
            </div>
            <button class="mx-go" type="button" data-svc="Instant License">Ask about the Instant license →</button>
          </article>
          <article class="sv-lc glass">
            <div class="sv-lc-top">
              <span class="mx-ic"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <rect x="4" y="4" width="16" height="11" rx="1.5"></rect>
                  <path d="M2 19h20"></path></svg></span
              ><span class="sv-lc-time"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <circle cx="12" cy="12" r="9"></circle>
                  <path d="M12 7v5l3 2"></path></svg
                ><b>~3–5 business days</b></span
              >
            </div>
            <h3>eTrader License</h3>
            <p>Designed for home-based online businesses in Dubai.</p>
            <ul class="sv-ul">
              <li>Issued as a sole establishment license</li>
              <li>Apply via the Invest in Dubai portal</li>
            </ul>
            <div class="sv-lc-fee"><small>Fees</small><b>AED 1,070 + AED 300</b><span>Dubai Chamber</span></div>
            <button class="mx-go" type="button" data-svc="eTrader License">Ask about the eTrader license →</button>
          </article>
        </div>
      </div>
    </section>

    <!-- ===== DOCUMENTS ===== -->
    <section id="docs" style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Required documents</span>
            <h2 class="serif">What you’ll <em class="gold-text">need to apply.</em></h2>
          </div>
        </div>
        <div class="reveal">
          <div class="sv-tabs glass" role="tablist" aria-label="License type">
            <button role="tab" id="dc-t0" aria-controls="dc-p0" aria-selected="true" class="on">
              <span
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M3 9l2-5h14l2 5"></path>
                  <path d="M3 9a3 3 0 006 0 3 3 0 006 0 3 3 0 006 0"></path>
                  <path d="M5 12v9h14v-9M10 21v-5h4v5"></path></svg></span
              >Normal License</button
            ><button role="tab" id="dc-t1" aria-controls="dc-p1" aria-selected="false" class="">
              <span
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M13 2L4 14h7l-1 8 9-12h-7z"></path></svg></span
              >Instant License</button
            ><button role="tab" id="dc-t2" aria-controls="dc-p2" aria-selected="false" class="">
              <span
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <rect x="4" y="4" width="16" height="11" rx="1.5"></rect>
                  <path d="M2 19h20"></path></svg></span
              >eTrader License
            </button>
          </div>
          <div class="sv-panes">
            <div role="tabpanel" id="dc-p0" aria-labelledby="dc-t0" class="sv-pane">
              <div class="pane-g">
                <div>
                  <h3 class="serif">Normal <em class="gold-text">license</em></h3>
                  <p class="lead">
                    For traditional businesses with a physical location. Gather these before you apply.
                  </p>
                </div>
                <div class="ckl" data-ckl="nl">
                  <div class="ckl-h">
                    <span class="eyebrow">Your document checklist</span><b class="ckl-n">0 of 6 ready</b>
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
                        ><span>Emirates ID and passport copy</span></label
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
                        ><span>Initial approval certificate</span></label
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
                        ><span>Trade name reservation</span></label
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
                        ><span>MoA (signed electronically or manually)</span></label
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
                        ><span>Lease contract (Ejari for Dubai)</span></label
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
                        ><span>Approvals from other authorities (if required)</span></label
                      >
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div role="tabpanel" id="dc-p1" aria-labelledby="dc-t1" class="sv-pane" hidden="">
              <div class="pane-g">
                <div>
                  <h3 class="serif">Instant <em class="gold-text">license</em></h3>
                  <p class="lead">
                    For businesses that don’t need external approvals — a virtual office is acceptable.
                  </p>
                </div>
                <div class="ckl" data-ckl="il">
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
                        ><span>Emirates ID or Unified Number</span></label
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
                        ><span>No lease agreement required (virtual office acceptable)</span></label
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
                        ><span>Trade name reservation</span></label
                      >
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div role="tabpanel" id="dc-p2" aria-labelledby="dc-t2" class="sv-pane" hidden="">
              <div class="pane-g">
                <div>
                  <h3 class="serif">eTrader <em class="gold-text">license</em></h3>
                  <p class="lead">For home-based online businesses in Dubai.</p>
                </div>
                <div class="ckl" data-ckl="el">
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
                        ><span>Emirates ID</span></label
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
                        ><span>UAE residency</span></label
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
                        ><span>Registered trade name</span></label
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

    <!-- ===== LEGAL FORMS ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="sv-lf2 glass reveal">
          <div>
            <span class="eyebrow">Legal forms to consider</span>
            <h3 class="serif">Match the structure <em class="gold-text">to your activity.</em></h3>
            <p>
              Your business structure must match your activity type. Each form comes with different legal obligations,
              ownership rights, and setup procedures.
            </p>
          </div>
          <ul class="sv-lf-chips">
            <li>Sole Proprietorship</li>
            <li>Limited Liability Company (LLC)</li>
            <li>Civil Company</li>
            <li>Private or Public Joint Stock Company</li>
            <li>Branch of a foreign company</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- ===== STEPS ===== -->
    <section id="steps" style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Key steps to get your license</span>
            <h2 class="serif">Eight steps <em class="gold-text">to open for business.</em></h2>
          </div>
        </div>
        <ol class="sv-st-grid reveal">
          <li class="sv-st glass">
            <span class="sv-st-n">01</span>
            <div>
              <b>Identify your business activity</b>
              <p>
                Choose from over 2,000 listed activities across various sectors (commercial, industrial, professional,
                etc.)
              </p>
            </div>
          </li>
          <li class="sv-st glass">
            <span class="sv-st-n">02</span>
            <div>
              <b>Choose a legal structure</b>
              <p>Based on the number of owners and the nature of the business</p>
            </div>
          </li>
          <li class="sv-st glass">
            <span class="sv-st-n">03</span>
            <div>
              <b>Reserve and register a trade name</b>
              <p>Must be unique, relevant, and approved by the DED</p>
            </div>
          </li>
          <li class="sv-st glass">
            <span class="sv-st-n">04</span>
            <div>
              <b>Get initial approval</b>
              <p>Indicates the government’s no objection to starting your business (not a permit to operate)</p>
            </div>
          </li>
          <li class="sv-st glass">
            <span class="sv-st-n">05</span>
            <div>
              <b>Prepare MoA (if required)</b>
              <p>Required for LLCs, partnerships, and joint stock companies</p>
            </div>
          </li>
          <li class="sv-st glass">
            <span class="sv-st-n">06</span>
            <div>
              <b>Secure office space</b>
              <p>The lease agreement is registered and compliant with the municipality’s zoning rules</p>
            </div>
          </li>
          <li class="sv-st glass">
            <span class="sv-st-n">07</span>
            <div>
              <b>Get external approvals (if needed)</b>
              <p>For regulated sectors (healthcare, legal, education, etc.)</p>
            </div>
          </li>
          <li class="sv-st glass">
            <span class="sv-st-n">08</span>
            <div>
              <b>Submit final documents and pay fees</b>
              <p>Finalise your application and receive your commercial license</p>
            </div>
          </li>
        </ol>
      </div>
    </section>

    <!-- ===== FEES + TIME + WHERE ===== -->
    <section style="padding-top: 30px">
      <div class="wrap sv-fz3 reveal">
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
              <path d="M3 12V4h8l10 10-8 8z"></path>
              <circle cx="7.5" cy="8.5" r="1.5"></circle></svg></span
          ><span class="eyebrow">Business license fees in the UAE</span>
          <h3 class="serif">What <em class="gold-text">affects the cost.</em></h3>
          <p>Fees vary depending on:</p>
          <ul class="sv-ul">
            <li>Type of license</li>
            <li>Business activity</li>
            <li>Emirate of registration</li>
            <li>Required government approvals</li>
          </ul>
          <p class="sv-fz-n">
            For updated fee details, visit the official DED or the relevant emirate’s economic department portal.
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
              <circle cx="12" cy="12" r="9"></circle>
              <path d="M12 7v5l3 2"></path></svg></span
          ><span class="eyebrow">How long does it take?</span>
          <h3 class="serif">From minutes <em class="gold-text">to weeks.</em></h3>
          <ul class="sv-tm">
            <li>
              <div class="sv-tm-h"><span>Instant License</span><b>~5–10 minutes</b></div>
              <div class="sv-tm-bar"><i style="--w: 4%"></i></div>
            </li>
            <li>
              <div class="sv-tm-h"><span>Normal License</span><b>~1–3 business days</b></div>
              <div class="sv-tm-bar"><i style="--w: 22%"></i></div>
            </li>
            <li>
              <div class="sv-tm-h"><span>eTrader License</span><b>~3–5 business days</b></div>
              <div class="sv-tm-bar"><i style="--w: 36%"></i></div>
            </li>
            <li>
              <div class="sv-tm-h">
                <span>Full company setup (with external approvals)</span><b>Up to several weeks</b>
              </div>
              <div class="sv-tm-bar"><i style="--w: 100%"></i></div>
            </li>
          </ul>
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
              <path d="M12 21s-7-6.2-7-11.5a7 7 0 0114 0C19 14.8 12 21 12 21z"></path>
              <circle cx="12" cy="9.5" r="2.5"></circle></svg></span
          ><span class="eyebrow">Where to apply</span>
          <h3 class="serif">Online or <em class="gold-text">in person.</em></h3>
          <p>You can apply via:</p>
          <ul class="sv-ul">
            <li>Official DED portals for other emirates</li>
            <li>Authorised service centres across the UAE</li>
          </ul>
          <p class="sv-fz-n">Or let DRE’s consultants guide you through it.</p>
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
            <span class="eyebrow">Set up your business</span>
            <h2 class="serif">Let’s get you<br /><em class="gold-text">licensed.</em></h2>
            <p class="lead">
              Call +971 52 754 3243 or email sales@drehomes.com — or leave your details and a business setup consultant
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
            <select name="service" id="ctaSvc" aria-label="License I’m interested in">
              <option value="">License I’m interested in…</option>
              <option>Normal License</option>
              <option>Instant License</option>
              <option>eTrader License</option>
              <option>Not sure yet</option>
            </select>
            <input name="message" placeholder="Anything else we should know? (optional)" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>

