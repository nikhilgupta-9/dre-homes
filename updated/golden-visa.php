<?php include('common/header.php'); ?>

    <!-- ===== GOLDEN VISA: HERO ===== -->
    <header class="ph-hero mg-hero gv-hero">
      <div
        class="ph-bg"
        style="
          background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/blog/inner/blog_inner_img_142_dubai-skyline-1.jpg);
        "
      ></div>
      <div class="wrap gv-hg">
        <div>
          <div class="crumbs" role="navigation" aria-label="Breadcrumb">
            <a href="index.php">Home</a><span>/</span><span aria-current="page">Golden Visa</span>
          </div>
          <span class="eyebrow">Residency through property</span>
          <h1 class="serif">Golden <em class="gold-text">Visa</em></h1>
          <p class="sub">
            <b>Secure your Dubai Golden Visa through property investment.</b> Invest in premium properties in Dubai and
            become eligible for the prestigious Golden Visa.
          </p>
          <div class="story-cta">
            <a class="btn btn-gold" href="#elig">Check my eligibility →</a
            ><a class="btn btn-glass" href="#contact">Claim your visa</a>
          </div>
        </div>
        <div class="gv-cards" aria-hidden="true">
          <img src="https://drehomes.com/images/bg/golden-visa-cards.png" alt="" />
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
              <label for="gvS"><span>Your ownership share</span><b id="gvSV">100%</b></label
              ><input type="range" id="gvS" min="10" max="100" step="5" value="100" />
            </div>
            <div class="gv-tg">
              <span class="bv-l">Property status</span>
              <div class="mg-type" role="group" aria-label="Property status">
                <button type="button" class="on" data-st="ready">Ready</button
                ><button type="button" data-st="offplan">Off-plan</button>
              </div>
              <span class="bv-l">Mortgaged?</span>
              <div class="mg-type" role="group" aria-label="Mortgage">
                <button type="button" class="on" data-mg="no">No</button
                ><button type="button" data-mg="yes">Yes</button>
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
    <section id="homes" style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Homes from AED 2M</span>
            <h2 class="serif">Properties that <em class="gold-text">can qualify.</em></h2>
          </div>
          <a class="btn btn-glass" href="off-plans.php">All off-plan projects →</a>
        </div>
        <div class="gv-hg2 reveal">
          <a class="gv-h glass" href="https://drehomes.com/property/seacliff-by-imtiaz"
            ><span class="gv-hi"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1552_Sea-Cliff-Image.jpg"
                alt="Seacliff"
                loading="lazy"
              /><span class="gv-ok"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M9 12l2 2 4-4"></path>
                  <circle cx="12" cy="12" r="9"></circle></svg
                >Golden Visa eligible price</span
              ></span
            ><span class="gv-hb"
              ><small>Imtiaz Developments</small><b>Seacliff</b
              ><span class="gv-hl"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M12 21s-7-6.2-7-11.5a7 7 0 0114 0C19 14.8 12 21 12 21z"></path>
                  <circle cx="12" cy="9.5" r="2.5"></circle></svg
                >Dubai Islands</span
              ><span class="gv-hp"><small>From</small>AED 2,100,000</span></span
            ></a
          ><a class="gv-h glass" href="https://drehomes.com/property/fior-1-at-rashid-yachts-and-marina"
            ><span class="gv-hi"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_3112_Fior-Img1.jpg"
                alt="Emaar Fior 1"
                loading="lazy"
              /><span class="gv-ok"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M9 12l2 2 4-4"></path>
                  <circle cx="12" cy="12" r="9"></circle></svg
                >Golden Visa eligible price</span
              ></span
            ><span class="gv-hb"
              ><small>Emaar Properties</small><b>Emaar Fior 1</b
              ><span class="gv-hl"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M12 21s-7-6.2-7-11.5a7 7 0 0114 0C19 14.8 12 21 12 21z"></path>
                  <circle cx="12" cy="9.5" r="2.5"></circle></svg
                >Rashid Yachts &amp; Marina</span
              ><span class="gv-hp"><small>From</small>AED 2,200,000</span></span
            ></a
          ><a class="gv-h glass" href="https://drehomes.com/property/the-wilds-residences"
            ><span class="gv-hi"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1410_The-Wilds-dubai.jpg"
                alt="The Wilds Residences"
                loading="lazy"
              /><span class="gv-ok"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M9 12l2 2 4-4"></path>
                  <circle cx="12" cy="12" r="9"></circle></svg
                >Golden Visa eligible price</span
              ></span
            ><span class="gv-hb"
              ><small>Aldar</small><b>The Wilds Residences</b
              ><span class="gv-hl"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M12 21s-7-6.2-7-11.5a7 7 0 0114 0C19 14.8 12 21 12 21z"></path>
                  <circle cx="12" cy="9.5" r="2.5"></circle></svg
                >Dubai</span
              ><span class="gv-hp"><small>From</small>AED 2,672,359</span></span
            ></a
          ><a class="gv-h glass" href="https://drehomes.com/property/marea-residences-at-dubai-islands"
            ><span class="gv-hi"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_5936_marea-residences-at-dubai-islands.jpg"
                alt="Marea Residences"
                loading="lazy"
              /><span class="gv-ok"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M9 12l2 2 4-4"></path>
                  <circle cx="12" cy="12" r="9"></circle></svg
                >Golden Visa eligible price</span
              ></span
            ><span class="gv-hb"
              ><small>Sharafi Development</small><b>Marea Residences</b
              ><span class="gv-hl"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M12 21s-7-6.2-7-11.5a7 7 0 0114 0C19 14.8 12 21 12 21z"></path>
                  <circle cx="12" cy="9.5" r="2.5"></circle></svg
                >Dubai Islands</span
              ><span class="gv-hp"><small>From</small>AED 2,740,000</span></span
            ></a
          ><a class="gv-h glass" href="https://drehomes.com/property/cheval-residences-at-dubai-islands"
            ><span class="gv-hi"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_6821_cheval-Residences-dubai-islands.jpg"
                alt="Cheval Residences"
                loading="lazy"
              /><span class="gv-ok"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M9 12l2 2 4-4"></path>
                  <circle cx="12" cy="12" r="9"></circle></svg
                >Golden Visa eligible price</span
              ></span
            ><span class="gv-hb"
              ><small>Avenew Development</small><b>Cheval Residences</b
              ><span class="gv-hl"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M12 21s-7-6.2-7-11.5a7 7 0 0114 0C19 14.8 12 21 12 21z"></path>
                  <circle cx="12" cy="9.5" r="2.5"></circle></svg
                >Dubai Islands</span
              ><span class="gv-hp"><small>From</small>AED 2,930,000</span></span
            ></a
          ><a class="gv-h glass" href="https://drehomes.com/property/antigua-damac-islands-2"
            ><span class="gv-hi"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9457_antigua-damac-islands-2-img1"
                alt="Antigua"
                loading="lazy"
              /><span class="gv-ok"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M9 12l2 2 4-4"></path>
                  <circle cx="12" cy="12" r="9"></circle></svg
                >Golden Visa eligible price</span
              ></span
            ><span class="gv-hb"
              ><small>Damac Properties</small><b>Antigua</b
              ><span class="gv-hl"
                ><svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M12 21s-7-6.2-7-11.5a7 7 0 0114 0C19 14.8 12 21 12 21z"></path>
                  <circle cx="12" cy="9.5" r="2.5"></circle></svg
                >Damac Islands 2</span
              ><span class="gv-hp"><small>From</small>AED 3,300,000</span></span
            ></a
          >
        </div>
      </div>
    </section>

    <!-- ===== REASONS ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Reasons to call Dubai home</span>
            <h2 class="serif">Why the world <em class="gold-text">moves here.</em></h2>
          </div>
        </div>
        <div class="gv-rg reveal">
          <article class="gv-r glass">
            <span class="gv-rn">01</span
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M3 12h4l3-8 4 16 3-8h4"></path></svg
            ></span>
            <h3>Open and free system</h3>
            <p>
              Foreign Direct Investment (FDI) is facilitated by open economic policies, minimal government control, and
              private sector regulation.
            </p>
          </article>
          <article class="gv-r glass">
            <span class="gv-rn">02</span
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"></path></svg
            ></span>
            <h3>Strategic location</h3>
            <p>
              Dubai is located at the intersection of Europe, Asia, and Africa, making it easily accessible by air, sea,
              and land.
            </p>
          </article>
          <article class="gv-r glass">
            <span class="gv-rn">03</span
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M12 3l2.6 5.6 6.1.7-4.5 4.2 1.2 6L12 16.6 6.6 19.5l1.2-6L3.3 9.3l6.1-.7z"></path></svg
            ></span>
            <h3>World-class amenities</h3>
            <p>
              Residences here are synonymous with luxury and built for family living and for those who prefer large and
              comfortable homes.
            </p>
          </article>
          <article class="gv-r glass">
            <span class="gv-rn">04</span
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path>
                <path d="M9 12l2 2 4-4"></path></svg
            ></span>
            <h3>Safety for all</h3>
            <p>
              As one of the world’s safest countries, the UAE has developed a strong law enforcement system that has
              allowed it to achieve low crime rates.
            </p>
          </article>
          <article class="gv-r glass">
            <span class="gv-rn">05</span
            ><span class="mx-ic"
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
                <path d="M2 21a7 7 0 0114 0M15 15a5 5 0 017 5"></path></svg
            ></span>
            <h3>Communal harmony</h3>
            <p>
              Over 200 nationalities live in Dubai, making the cosmopolitan city a safe haven for everyone and offering
              a first-class lifestyle to families.
            </p>
          </article>
          <article class="gv-r glass">
            <span class="gv-rn">06</span
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="3" y="3" width="18" height="18" rx="4"></rect>
                <path d="M12 8v8M8 12h8"></path></svg
            ></span>
            <h3>World-class healthcare</h3>
            <p>
              Internationally accredited hospitals and clinics across the city, with health insurance a requirement for
              every Dubai resident.
            </p>
          </article>
          <article class="gv-r glass">
            <span class="gv-rn">07</span
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M8 16L16 8"></path>
                <circle cx="9" cy="9" r="1.2"></circle>
                <circle cx="15" cy="15" r="1.2"></circle></svg
            ></span>
            <h3>Tax efficient</h3>
            <p>
              There is no income tax in Dubai for residents, and there is no Value Added Tax (VAT) on residential
              properties for investors.
            </p>
          </article>
          <article class="gv-r glass">
            <span class="gv-rn">08</span
            ><span class="mx-ic"
              ><svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <ellipse cx="12" cy="7" rx="7" ry="3"></ellipse>
                <path d="M5 7v5c0 1.7 3.1 3 7 3s7-1.3 7-3V7M5 12v5c0 1.7 3.1 3 7 3s7-1.3 7-3v-5"></path></svg
            ></span>
            <h3>Stable currency</h3>
            <p>Since 1973, the UAE Dirham has been pegged to the US Dollar and set at a constant rate since 1997.</p>
          </article>
        </div>
      </div>
    </section>

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

    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Claim your visa</span>
            <h2 class="serif">Your decade in the UAE<br /><em class="gold-text">starts here.</em></h2>
            <p class="lead">
              Fill out the form to get started and learn more about this exclusive opportunity. Toll free 800 37373 ·
              Hotline +971 52 754 3243
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