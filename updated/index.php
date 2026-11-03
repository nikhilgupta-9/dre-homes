<?php include('common/header.php'); ?>

    <header class="hero" id="hero" data-bg="video">
      <div class="hero-bg" aria-hidden="true">
        <img class="hm" src="https://drehomes.com/images/home-sec/offplan-property.jpg" alt="" />
        <!-- HERO VIDEO: change the src below to use a different video (MP4, ideally < 8 MB, 1920px wide) -->
        <video
          class="hm"
          id="heroVideo"
          muted=""
          loop=""
          playsinline=""
          webkit-playsinline=""
          preload="metadata"
          poster="https://drehomes.com/images/home-sec/offplan-property.jpg"
        >
          <source
            src="https://drehomes.com/admin_nsrdwsc/assets/media/hero/videos/1781329027_6a2cec83412d8.mp4"
            type="video/mp4"
          />
        </video>
      </div>
      <button class="vid-ctl" id="vidCtl" aria-label="Pause background video"></button>
      <div class="wrap hero-grid">
        <div>
          <!-- <span class="eyebrow">Dubai · Abu Dhabi · Sharjah</span> -->
          <h1 class="serif">Find your <br /> <em class="gold-text">DREam</em><br />address in Dubai.</h1>
          <!-- <p class="sub">
            Curated off-plan launches, signature resales and premium residences — guided by RERA-certified advisors.
          </p> -->
          <div class="search glass">
            <div class="tabs"><button class="on">Off Plan</button><button>Buy</button><button>Rent</button></div>
            <div class="fields">
              <div class="field">
                <label>Location</label
                ><input
                  id="heroLoc"
                  placeholder="Palm Jumeirah, Dubai Hills…"
                  aria-label="Location"
                  data-suggest="heroLocList"
                  data-pop-title="Popular areas"
                  data-min-w="360"
                  data-per="8"
                  data-max="8"
                /><datalist id="heroLocList">
                  <option value="Palm Jumeirah">Waterfront icon</option>
                  <option value="Downtown Dubai">Burj Khalifa &amp; Dubai Mall</option>
                  <option value="Dubai Hills Estate">Golf-front green community</option>
                  <option value="Business Bay">Canal-side skyline living</option>
                  <option value="Emaar Beachfront">Sea views &amp; private beach</option>
                  <option value="Jumeirah Village Circle">Family-friendly &amp; great value</option>
                  <option value="Dubai Creek Harbour">Creek-side towers</option>
                  <option value="Town Square">Nshama family community</option>
                  <option value="Damac Hills 2">Resort-style living</option>
                  <option value="Damac Lagoons">Lagoon villas</option>
                  <option value="Dubai Islands">New waterfront destination</option>
                  <option value="Dubai Marina">Marina &amp; JBR living</option>
                  <option value="Arabian Ranches">Villa community</option>
                  <option value="Emaar South">Golf community near Expo</option>
                  <option value="The Valley">Townhouse community</option>
                  <option value="Dubai Science Park">Affordable apartments</option>
                  <option value="Saadiyat Island">Abu Dhabi culture &amp; beaches</option>
                  <option value="Yas Island">Abu Dhabi leisure hub</option>
                </datalist>
              </div>
              <div class="field">
                <label>Residence</label
                ><select>
                  <option>Any type</option>
                  <option>Apartment</option>
                  <option>Villa</option>
                  <option>Penthouse</option>
                  <option>Mansion</option>
                </select>
              </div>
              <div class="field">
                <label>Budget</label
                ><select>
                  <option>Any budget</option>
                  <option>AED 1M – 3M</option>
                  <option>AED 3M – 10M</option>
                  <option>AED 10M – 30M</option>
                  <option>AED 30M+</option>
                </select>
              </div>
              <button class="go">Search <span>→</span></button>
            </div>
          </div>
          <div class="hero-stats glass">
            <div><strong class="gold-text">2,400+</strong>Live listings</div>
            <div><strong class="gold-text">650+</strong>Off-plan projects</div>
            <div><strong class="gold-text">4.9★</strong>Google rating</div>
          </div>
        </div>
        <div style="position: relative">
          <div class="feature glass acc-edge">
            <div class="ph"><span class="badge">Featured · Off plan</span></div>
            <div class="info">
              <div>
                <h3 class="serif">Sei Saadiyat</h3>
                <small>Saadiyat Island, Abu Dhabi</small>
              </div>
              <div class="price banner-price"><span>From</span><b class="gold-text">AED 2.95M</b></div>
            </div>
          </div>
          <div class="mini-stat glass"><strong class="gold-text">Q4 2030</strong>Handover</div>
        </div>
      </div>
    </header>

    <section id="props">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">The Collection</span>
            <h2 class="serif">Hot property <em class="gold-text">launches</em></h2>
            <p class="lead">The newest and most sought-after projects, fresh on the UAE market.</p>
          </div>
          <div>
          <div class="chips glass">
            <button class="chip on">All</button><button class="chip">Dubai</button
            ><button class="chip">Abu Dhabi</button><button class="chip">Sharjah</button>
          </div>
          </div>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3" id="cards">
          <div class="col" data-c="Abu Dhabi">
            <article class="pcard">
              <div
                class="bg"
                style="
                  background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/project/home-card-img/home_card_1789107411_5633.jpg);
                "
              ></div>
              <span class="badge">Off plan</span><span class="fav">♡</span>
              <div class="pinfo glass on-img">
                <h3>Sei Saadiyat</h3>
                <p class="loc">Saadiyat Island, Abu Dhabi</p>
                <div class="prow">
                  <div>Starting from<strong>AED 2,950,000</strong></div>
                  <div style="text-align: right">Handover<strong>Q4 2030</strong></div>
                </div>
              </div>
            </article>
          </div>
          <div class="col" data-c="Dubai">
            <article class="pcard">
              <div
                class="bg"
                style="
                  background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/project/home-card-img/home_card_1784718478_9585.jpg);
                "
              ></div>
              <span class="badge">Off plan</span><span class="fav">♡</span>
              <div class="pinfo glass on-img">
                <h3>Golf Trails</h3>
                <p class="loc">Emaar South, Dubai</p>
                <div class="prow">
                  <div>Starting from<strong>AED 1,250,000</strong></div>
                  <div style="text-align: right">Handover<strong>Q4 2030</strong></div>
                </div>
              </div>
            </article>
          </div>
          <div class="col" data-c="Sharjah">
            <article class="pcard">
              <div
                class="bg"
                style="
                  background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/project/home-card-img/home_card_1789199348_7397.jpg);
                "
              ></div>
              <span class="badge">Off plan</span><span class="fav">♡</span>
              <div class="pinfo glass on-img">
                <h3>Azizi Florence</h3>
                <p class="loc">Sharjah</p>
                <div class="prow">
                  <div>Starting from<strong>On request</strong></div>
                  <div style="text-align: right">Handover<strong>—</strong></div>
                </div>
              </div>
            </article>
          </div>
          <div class="col" data-c="Abu Dhabi">
            <article class="pcard">
              <div
                class="bg"
                style="
                  background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/project/home-card-img/home_card_1789537259_3074.png);
                "
              ></div>
              <span class="badge">Off plan</span><span class="fav">♡</span>
              <div class="pinfo glass on-img">
                <h3>Wadeem Gardens</h3>
                <p class="loc">Abu Dhabi</p>
                <div class="prow">
                  <div>Starting from<strong>AED 8,700,000</strong></div>
                  <div style="text-align: right">Handover<strong>Q2 2031</strong></div>
                </div>
              </div>
            </article>
          </div>
          <div class="col" data-c="Abu Dhabi">
            <article class="pcard">
              <div
                class="bg"
                style="
                  background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/project/home-card-img/home_card_1784718427_2623.jpg);
                "
              ></div>
              <span class="badge">Off plan</span><span class="fav">♡</span>
              <div class="pinfo glass on-img">
                <h3>The Canopies</h3>
                <p class="loc">Yas Island, Abu Dhabi</p>
                <div class="prow">
                  <div>Starting from<strong>AED 1,650,000</strong></div>
                  <div style="text-align: right">Handover<strong>Q3 2030</strong></div>
                </div>
              </div>
            </article>
          </div>
          <div class="col" data-c="Dubai">
            <article class="pcard">
              <div
                class="bg"
                style="
                  background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/project/home-card-img/home_card_1780924631_2212.jpeg);
                "
              ></div>
              <span class="badge">Off plan</span><span class="fav">♡</span>
              <div class="pinfo glass on-img">
                <h3>Arancia Yards</h3>
                <p class="loc">Arabian Ranches, Dubai</p>
                <div class="prow">
                  <div>Starting from<strong>AED 1,000,000</strong></div>
                  <div style="text-align: right">Handover<strong>Q4 2028</strong></div>
                </div>
              </div>
            </article>
          </div>
        </div>
        <div style="text-align: center; margin-top: 56px">
          <a class="btn btn-glass" href="off-plans.php">View the full collection →</a>
        </div>
      </div>
    </section>

    <section id="areas" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Signature Neighborhoods</span>
            <h2 class="serif">Addresses that <em class="gold-text">define</em> you</h2>
          </div>
          <a class="btn btn-glass" href="communities.php">All communities →</a>
        </div>
        <div class="areas reveal">
          <a class="area tall" href="community-single.php"
            ><div
              class="bg"
              style="
                background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/community/home/comm_home_img_1912_palm-jumeirah-dubai.jpg);
              "
            ></div>
            <div class="lbl glass on-img">
              <div>
                <h3>Palm Jumeirah</h3>
                <p>33 residences · Waterfront icon</p>
              </div>
              <span class="arrow">→</span>
            </div></a
          >
          <a class="area" href="https://drehomes.com/community/downtown-dubai"
            ><div
              class="bg"
              style="
                background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/community/home/comm_home_img_7241_downtown.jpg);
              "
            ></div>
            <div class="lbl glass on-img">
              <div>
                <h3>Downtown</h3>
                <p>33 residences</p>
              </div>
              <span class="arrow">→</span>
            </div></a
          >
          <a class="area" href="https://drehomes.com/community/dubai-hills-estate"
            ><div
              class="bg"
              style="
                background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/community/home/comm_home_img_8651_dubai-hills-estate.jpg);
              "
            ></div>
            <div class="lbl glass on-img">
              <div>
                <h3>Dubai Hills</h3>
                <p>51 residences</p>
              </div>
              <span class="arrow">→</span>
            </div></a
          >
          <a class="area" href="https://drehomes.com/community/business-bay"
            ><div
              class="bg"
              style="
                background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/community/home/comm_home_img_4047_business-bay-home.jpg);
              "
            ></div>
            <div class="lbl glass on-img">
              <div>
                <h3>Business Bay</h3>
                <p>48 residences</p>
              </div>
              <span class="arrow">→</span>
            </div></a
          >
          <a class="area" href="https://drehomes.com/community/emaar-beachfront"
            ><div
              class="bg"
              style="
                background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/community/home/comm_home_img_8630_emaar-beachfront.jpg);
              "
            ></div>
            <div class="lbl glass on-img">
              <div>
                <h3>Emaar Beachfront</h3>
                <p>12 residences</p>
              </div>
              <span class="arrow">→</span>
            </div></a
          >
        </div>
      </div>
    </section>

    <section id="paths" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Your Journey</span>
            <h2 class="serif">Three ways <em class="gold-text">in</em></h2>
          </div>
        </div>
        <div class="paths reveal">
          <div class="path glass">
            <span class="num gold-text">i.</span>
            <h3>Off-plan launches</h3>
            <p>Priority access to new projects, with flexible developer payment plans.</p>
            <a href="off-plans.php">Explore launches →</a>
          </div>
          <div class="path glass">
            <span class="num gold-text">ii.</span>
            <h3>Signature resales</h3>
            <p>Handpicked ready homes with verified pricing and full DLD transfer support.</p>
            <a href="buy-property.php">View resales →</a>
          </div>
          <div class="path glass">
            <span class="num gold-text">iii.</span>
            <h3>Premium rentals</h3>
            <p>In-demand residences across prime communities, matched to your lifestyle.</p>
            <a href="#">Find a rental →</a>
          </div>
        </div>
      </div>
    </section>

    <section id="visa" style="padding-top: 20px">
      <div class="wrap">
        <div class="visa-box glass acc-edge reveal">
          <div>
            <span class="eyebrow">Golden Visa</span>
            <h2 class="serif">Own a home.<br />Earn a <em class="gold-text">decade</em> in the UAE.</h2>
            <p class="lead">
              Property investments of AED 2M and above can qualify you and your family for 10-year residency. We manage
              it end to end.
            </p>
            <ul class="checks">
              <li>10-year renewable residency for your family</li>
              <li>Off-plan &amp; mortgaged properties may qualify</li>
              <li>No local sponsor required</li>
            </ul>
            <div style="display: flex; gap: 10px; flex-wrap: wrap">
              <a class="btn btn-gold" href="#contact">Free Golden Visa consultation →</a
              ><a class="btn btn-glass" href="golden-visa.php">Learn more</a>
            </div>
          </div>
          <div class="calc glass correct-aed">
            <h4>Eligibility check</h4>
            <small>Slide to your planned investment</small>
            <div class="val gold-text" id="bval">AED 2,500,000</div>
            <input type="range" id="budget" min="500000" max="10000000" step="50000" value="2500000" />
            <div class="scale"><span>AED 0.5M</span><span>AED 10M+</span></div>
            <div class="result" id="bres"></div>
          </div>
        </div>
      </div>
    </section>

    <section id="about">
      <div class="wrap about">
        <div class="about-img reveal">
          <a class="play glass on-img" href="about-us.php"><i>→</i>More about DRE</a>
        </div>
        <div class="reveal">
          <span class="eyebrow">About DRE</span>
          <h2 class="serif">Built on trust.<br />Defined by <em class="gold-text">excellence</em>.</h2>
          <p class="lead">
            From our headquarters in Dubai Hills Estate and our Town Square branch, we connect buyers, investors and
            tenants with the UAE's finest properties.
          </p>
          <div class="stats">
            <div class="stat glass">
              <strong class="gold-text" data-n="19">0</strong><span>Years of excellence</span>
            </div>
            <div class="stat glass">
              <strong class="gold-text" data-n="5000">0</strong><span>Happy homeowners</span>
            </div>
            <div class="stat glass"><strong class="gold-text" data-n="85">0</strong><span>Honors received</span></div>
            <div class="stat glass"><strong class="gold-text" data-n="150">0</strong><span>Dedicated agents</span></div>
          </div>
        </div>
      </div>
    </section>

    <section id="vision" style="padding-top: 20px">
      <div class="wrap duo">
        <article class="dcard reveal">
          <div class="bg" style="background-image: url(https://drehomes.com/images/qurat-and-mudi.jpg)"></div>
          <div class="dc-in glass on-img">
            <span class="eyebrow">Meet the vision behind DRE</span>
            <h3>Built on relationships, <em class="gold-text">driven by results.</em></h3>
            <!-- <p>
              Founded on trust and expertise, DRE’s founders are committed to helping clients make confident real estate
              decisions through honest guidance and deep market knowledge.
            </p> -->
            <a class="btn btn-gold" href="dre-brand-video.php"><i>▶</i>&nbsp; Watch our story</a>
          </div>
        </article>
        <article class="dcard reveal">
          <div class="bg" style="background-image: url(https://drehomes.com/images/town-square-office.jpg)"></div>
          <div class="dc-in glass on-img">
            <span class="eyebrow">Town Square Branch</span>
            <h3>Making property decisions <em class="gold-text">easier across Dubai.</em></h3>
            <!-- <p>
              With a strong understanding of the Town Square community, our team is here to connect you with
              opportunities that truly match your needs.
            </p> -->
            <a class="btn btn-gold" href="town-square-team.php">Connect with our team →</a>
          </div>
        </article>
      </div>
    </section>

    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Beyond the Sale</span>
            <h2 class="serif">A complete <em class="gold-text">concierge</em></h2>
          </div>
          <!-- <a class="btn btn-glass" href="#">All services →</a> -->
        </div>
        <div class="svc-grid reveal">
          <div class="svc glass">
            <div class="ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                <path d="M3 11l9-7 9 7v9H3z"></path>
                <path d="M9 20v-6h6v6"></path>
              </svg>
            </div>
            <h3>Property management</h3>
            <p>Tenants, maintenance, rent collection and reporting — handled.</p>
          </div>
          <div class="svc glass">
            <div class="ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                <path d="M3 17l6-6 4 4 8-8"></path>
                <path d="M15 7h6v6"></path>
              </svg>
            </div>
            <h3>Investment advisory</h3>
            <p>Yield analysis and market insight before you commit.</p>
          </div>
          <div class="svc glass">
            <div class="ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                <path d="M3 10l9-6 9 6"></path>
                <path d="M5 10v8M10 10v8M14 10v8M19 10v8M3 20h18"></path>
              </svg>
            </div>
            <h3>Mortgage support</h3>
            <p>Pre-approvals and preferred rates from leading UAE banks.</p>
          </div>
          <div class="svc glass">
            <div class="ic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                <circle cx="12" cy="10" r="3"></circle>
                <path d="M8.5 17h7"></path>
              </svg>
            </div>
            <h3>Golden Visa</h3>
            <p>Document preparation and submission, start to finish.</p>
          </div>
        </div>
      </div>
    </section>

    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="reviews reveal">
          <div class="rv-intro glass acc-edge">
            <span class="eyebrow">Words That Build Trust</span>
            <h2 class="serif">Our clients, <em class="gold-text">in their words</em></h2>
            <p class="lead">We take pride in creating smooth and rewarding real estate experiences across Dubai.</p>
            <!-- Trustindex rating badge (same as live site) -->
            <div class="rv-badge">
              <script defer="" async="" src="https://cdn.trustindex.io/loader.js?5e862a8729ae637df016bbd0ae2"></script>
            </div>
          </div>
          <!-- Trustindex Google reviews widget (same as live site) -->
          <div class="rv-widget glass">
            <script defer="" async="" src="https://cdn.trustindex.io/loader.js?e5ad343727cf637b4d36c8ac53d"></script>
          </div>
        </div>
      </div>
    </section>

    <section class="devs" id="developers">
      <div class="wrap">
        <div class="head-row reveal" style="margin-bottom: 40px">
          <div>
            <span class="eyebrow">Leading Developer Partners</span>
            <h2 class="serif">Backed by Dubai's most <em class="gold-text">iconic</em> developers</h2>
          </div>
          <a class="btn btn-glass" href="developers.php">View all developers →</a>
        </div>
      </div>
      <div class="dev-track reveal">
        <div class="dev-row">
          <a class="dev glass" href="https://drehomes.com/developer/emaar" title="Emaar Properties projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_5157_emaar-logo.png"
              alt="Emaar Properties"
              loading="lazy"
            /><span>View projects →</span></a
          ><a class="dev glass" href="https://drehomes.com/developer/damac-properties" title="Damac Properties projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_9298_damac-logo.png"
              alt="Damac Properties"
              loading="lazy"
            /><span>View projects →</span></a
          ><a class="dev glass" href="https://drehomes.com/developer/nakheel" title="Nakheel projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_6577_nakheel-logo.png"
              alt="Nakheel"
              loading="lazy"
            /><span>View projects →</span></a
          ><a class="dev glass" href="https://drehomes.com/developer/meraas" title="Meraas projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_2087_meraas-logo.png"
              alt="Meraas"
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/sharafi-development"
            title="Sharafi Developments projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_5418_sharafidevelopment.png"
              alt="Sharafi Developments"
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/gulf-land-property"
            title="Gulf Land Property projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_6552_gulf-land-property-logo.png"
              alt="Gulf Land Property"
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/danube-properties"
            title="Danube Properties projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_4266_danube-logo.png"
              alt="Danube Properties"
              loading="lazy"
            /><span>View projects →</span></a
          ><a class="dev glass" href="https://drehomes.com/developer/binghatti-developers" title="Binghatti projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_9080_binghatti-logo.png"
              alt="Binghatti"
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/azizi-developments"
            title="Azizi Developments projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_87_azizi-logo.png"
              alt="Azizi Developments"
              loading="lazy"
            /><span>View projects →</span></a
          ><a class="dev glass" href="https://drehomes.com/developer/beyond" title="Beyond projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_5151_beyond-logo.png"
              alt="Beyond"
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/emaar"
            aria-hidden="true"
            tabindex="-1"
            title="Emaar Properties projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_5157_emaar-logo.png"
              alt=""
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/damac-properties"
            aria-hidden="true"
            tabindex="-1"
            title="Damac Properties projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_9298_damac-logo.png"
              alt=""
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/nakheel"
            aria-hidden="true"
            tabindex="-1"
            title="Nakheel projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_6577_nakheel-logo.png"
              alt=""
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/meraas"
            aria-hidden="true"
            tabindex="-1"
            title="Meraas projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_2087_meraas-logo.png"
              alt=""
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/sharafi-development"
            aria-hidden="true"
            tabindex="-1"
            title="Sharafi Developments projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_5418_sharafidevelopment.png"
              alt=""
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/gulf-land-property"
            aria-hidden="true"
            tabindex="-1"
            title="Gulf Land Property projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_6552_gulf-land-property-logo.png"
              alt=""
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/danube-properties"
            aria-hidden="true"
            tabindex="-1"
            title="Danube Properties projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_4266_danube-logo.png"
              alt=""
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/binghatti-developers"
            aria-hidden="true"
            tabindex="-1"
            title="Binghatti projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_9080_binghatti-logo.png"
              alt=""
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/azizi-developments"
            aria-hidden="true"
            tabindex="-1"
            title="Azizi Developments projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_87_azizi-logo.png"
              alt=""
              loading="lazy"
            /><span>View projects →</span></a
          ><a
            class="dev glass"
            href="https://drehomes.com/developer/beyond"
            aria-hidden="true"
            tabindex="-1"
            title="Beyond projects"
            ><img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/developer/homepage-image/dev_homp_img_5151_beyond-logo.png"
              alt=""
              loading="lazy"
            /><span>View projects →</span></a
          >
        </div>
      </div>
      <div class="wrap">
        <div class="excl reveal">
          <a
            class="ex glass"
            href="https://drehomes.com/company-news/sharafi-development-unveils-marea-residences-with-dre-x-as-exclusive-sales-partner"
          >
            <div class="phw">
              <div
                class="ph"
                style="background-image: url(assets/img/sharafi-collab.jpg)"
              ></div>
            </div>
            <div class="tx">
              <small>Exclusive Sales Partner</small
              ><img src="https://drehomes.com/images/home-partner/sharafi-logo1.png" alt="Sharafi Developments" />
              <h3>Shaping coastal living with Sharafi Developments</h3>
              <em>Read the story →</em>
            </div>
          </a>
          <a
            class="ex glass"
            href="https://drehomes.com/company-news/dubai-tonino-lamborghini-residences-unveils-final-phase"
          >
            <div class="phw">
              <div
                class="ph"
                style="background-image: url(assets/img/gulf-collab.jpg)"
              ></div>
            </div>
            <div class="tx">
              <small>Exclusive Sales Partner</small
              ><img src="https://drehomes.com/images/home-partner/gulf-logo1.png" alt="Gulf Land Property Developers" />
              <h3>Elevating luxury living with Gulf Land Property Developers</h3>
              <em>Read the story →</em>
            </div>
          </a>
        </div>
      </div>
    </section>

    <section id="instagram" style="padding-top: 20px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Follow Along</span>
            <h2 class="serif">Life at <em class="gold-text">DRE</em>, on Instagram</h2>
            <a class="ig-handle" href="https://www.instagram.com/drehomes_realestate/" target="_blank" rel="noopener">
              <span class="ig-av"
                ><span><span class="logo-mark"></span></span
              ></span>
              <span
                ><b>@drehomes_realestate</b><small>New launches, market updates &amp; behind the scenes</small></span
              >
            </a>
          </div>
          <a class="btn btn-gold" href="https://www.instagram.com/drehomes_realestate/" target="_blank" rel="noopener">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <rect x="3" y="3" width="18" height="18" rx="5"></rect>
              <circle cx="12" cy="12" r="4"></circle>
              <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle>
            </svg>
            Follow us
          </a>
        </div>
        <div class="ig-grid reveal" id="igGrid">
          <div class="ig-col">
            <a
              class="ig reel"
              href="https://www.instagram.com/reel/DYRPcXkINtf/"
              target="_blank"
              rel="noopener"
              aria-label="Watch reel on Instagram"
            >
              <img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/instagram/1779347751_6a0eb127be4eb_reel4.jpg"
                alt=""
                loading="lazy" /><span class="type"
                ><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg
              ></span>
              <span class="hov glass on-img"
                >Watch reel<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle>
                  <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg></span></a
            ><a
              class="ig post"
              href="https://www.instagram.com/p/DXMagoNDFsx/"
              target="_blank"
              rel="noopener"
              aria-label="View post on Instagram"
            >
              <img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/instagram/1781086234_6a29381a81bdd_kurat_wani_drehomes_1776345831_3876589970660285233_28165876583.jpg"
                alt=""
                loading="lazy" /><span class="type"
                ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle></svg
              ></span>
              <span class="hov glass on-img"
                >View post<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle>
                  <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg></span
            ></a>
          </div>
          <div class="ig-col">
            <a
              class="ig post"
              href="https://www.instagram.com/p/DYOpRHxoVyP/"
              target="_blank"
              rel="noopener"
              aria-label="View post on Instagram"
            >
              <img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/instagram/1785753569_6a706fe14cead_Image-7880.jpg"
                alt=""
                loading="lazy" /><span class="type"
                ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle></svg
              ></span>
              <span class="hov glass on-img"
                >View post<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle>
                  <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg></span></a
            ><a
              class="ig reel"
              href="https://www.instagram.com/p/DbqaXEjiI-J/"
              target="_blank"
              rel="noopener"
              aria-label="Watch reel on Instagram"
            >
              <img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/instagram/1786436310_6a7adad6e2c70_drehomes_realestate_1785942046_3957090600471470116_3310939170.jpg"
                alt=""
                loading="lazy" /><span class="type"
                ><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg
              ></span>
              <span class="hov glass on-img"
                >Watch reel<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle>
                  <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg></span
            ></a>
          </div>
          <div class="ig-col">
            <a
              class="ig reel"
              href="https://www.instagram.com/p/Db5Ql_esvbn/"
              target="_blank"
              rel="noopener"
              aria-label="Watch reel on Instagram"
            >
              <img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/instagram/1786436390_6a7adb26da8f2_Image-6554.jpg"
                alt=""
                loading="lazy" /><span class="type"
                ><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg
              ></span>
              <span class="hov glass on-img"
                >Watch reel<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle>
                  <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg></span></a
            ><a
              class="ig post"
              href="https://www.instagram.com/p/DZ49ytaJIcK/"
              target="_blank"
              rel="noopener"
              aria-label="View post on Instagram"
            >
              <img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/instagram/1782212360_6a3a67089f1ab_drehomes_realestate_1782135500_3925158840821384970_3310939170.jpg"
                alt=""
                loading="lazy" /><span class="type"
                ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle></svg
              ></span>
              <span class="hov glass on-img"
                >View post<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle>
                  <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg></span
            ></a>
          </div>
          <div class="ig-col">
            <a
              class="ig post"
              href="https://www.instagram.com/p/DaXEV48CCId/"
              target="_blank"
              rel="noopener"
              aria-label="View post on Instagram"
            >
              <img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/instagram/1786437288_6a7adea811f4f_drehomes_realestate.jpg"
                alt=""
                loading="lazy" /><span class="type"
                ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle></svg
              ></span>
              <span class="hov glass on-img"
                >View post<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle>
                  <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg></span></a
            ><a
              class="ig reel"
              href="https://www.instagram.com/p/DXeHzjwCFTx/"
              target="_blank"
              rel="noopener"
              aria-label="Watch reel on Instagram"
            >
              <img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/instagram/1786436460_6a7adb6c21e09_Image-21529.jpg"
                alt=""
                loading="lazy" /><span class="type"
                ><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg
              ></span>
              <span class="hov glass on-img"
                >Watch reel<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle>
                  <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg></span
            ></a>
          </div>
          <div class="ig-col">
            <a
              class="ig reel"
              href="https://www.instagram.com/p/DYfMg62spaS/"
              target="_blank"
              rel="noopener"
              aria-label="Watch reel on Instagram"
            >
              <img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/instagram/1779348255_6a0eb31fdc68b_reel3.jpg"
                alt=""
                loading="lazy" /><span class="type"
                ><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"></path></svg
              ></span>
              <span class="hov glass on-img"
                >Watch reel<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle>
                  <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg></span></a
            ><a
              class="ig post"
              href="https://www.instagram.com/p/DbKiXSbIIDW/"
              target="_blank"
              rel="noopener"
              aria-label="View post on Instagram"
            >
              <img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/instagram/1785302134_6a698c76086b9_danube-dre.jpg"
                alt=""
                loading="lazy" /><span class="type"
                ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle></svg
              ></span>
              <span class="hov glass on-img"
                >View post<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                  <circle cx="12" cy="12" r="4"></circle>
                  <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg></span
            ></a>
          </div>
        </div>
        <a
          class="btn btn-gold ig-more"
          href="https://www.instagram.com/drehomes_realestate/"
          target="_blank"
          rel="noopener"
          style="display: none; justify-content: center; margin-top: 20px"
          ><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3" y="3" width="18" height="18" rx="5"></rect>
            <circle cx="12" cy="12" r="4"></circle>
            <circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg
          >Follow @drehomes_realestate</a
        >
      </div>
    </section>

    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Private Consultation</span>
            <h2 class="serif">Your advisor is<br /><em class="gold-text">one call</em> away.</h2>
            <p class="lead">Toll free 800 37373 · Hotline +971 52 754 3243</p>
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