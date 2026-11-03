<?php include('common/header.php'); ?>

    <!-- ===== PROPERTY DETAIL ===== -->
    <header class="pd-hero cd-hero rps-box">
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="index.php">Home</a><span>/</span><a href="buy-property.php">Buy Property</a><span>/</span
          ><span aria-current="page">Shams Townhouses</span>
        </div>
        <div class="pd-gal">
          <button class="pg-main" data-g="0" aria-label="Open gallery">
            <img
              src="https://drehomes.com/admin_nsrdwsc/assets/media/project/gallery/gallery_image_8227_imgi_74_868850650-800x600.webp"
              alt="Shams Townhouses living room"
            />
          </button>
          <div class="pg-side">
            <button data-g="1" aria-label="Open photo 2">
              <img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/gallery/gallery_image_9360_imgi_78_868850654-800x600.webp"
                alt="Shams Townhouses photo 2"
              />
            </button>
            <button data-g="2" aria-label="Open photo 3">
              <img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/gallery/gallery_image_9517_imgi_82_868850658-800x600.webp"
                alt="Shams Townhouses photo 3"
              /><span class="pg-more glass on-img">⤢ View all 5 photos</span>
            </button>
          </div>
          <div class="pd-title glass on-img">
            <!-- <div class="pt-tags"><span>For sale</span><span>Townhouse</span><span>Ready to move in</span></div> -->
            <h1 class="serif">Shams <em class="gold-text">Townhouses</em></h1>
            <p class="pt-sub">Single row · Vacant · Landscaped garden</p>
            <p>
              <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
                <path
                  d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                ></path>
              </svg>
              Shams, Town Square, Dubai
            </p>
          </div>
          <ul class="pd-photos" id="pdPhotos" hidden="">
            <li
              data-src="https://drehomes.com/admin_nsrdwsc/assets/media/project/gallery/gallery_image_8227_imgi_74_868850650-800x600.webp"
              data-title="Shams Townhouses photo 1"
            ></li>
            <li
              data-src="https://drehomes.com/admin_nsrdwsc/assets/media/project/gallery/gallery_image_9360_imgi_78_868850654-800x600.webp"
              data-title="Shams Townhouses photo 2"
            ></li>
            <li
              data-src="https://drehomes.com/admin_nsrdwsc/assets/media/project/gallery/gallery_image_9517_imgi_82_868850658-800x600.webp"
              data-title="Shams Townhouses photo 3"
            ></li>
            <li
              data-src="https://drehomes.com/admin_nsrdwsc/assets/media/project/gallery/gallery_image_1665_imgi_92_868850696-800x600.webp"
              data-title="Shams Townhouses photo 4"
            ></li>
            <li
              data-src="https://drehomes.com/admin_nsrdwsc/assets/media/project/gallery/gallery_image_8404_imgi_93_868850697-800x600.webp"
              data-title="Shams Townhouses photo 5"
            ></li>
          </ul>
        </div>
      </div>
    </header>

    <section class="pd-body">
      <div class="wrap pd-grid">
        <div class="pd-main">
          <div class="pd-tabs glass" id="pdTabs">
            <a href="#overview" class="on">Overview</a><a href="#details">Details</a><a href="#amenities">Amenities</a
            ><a href="#location">Location</a><a href="#mortgage">Mortgage</a><a href="#enquire">Book a viewing</a>
          </div>

          <!-- <div class="facts facts5 reveal">
            <div class="glass"><small>Price</small><b>AED 3,100,000</b></div>
            <div class="glass"><small>Bedrooms</small><b>3</b></div>
            <div class="glass"><small>Bathrooms</small><b>4</b></div>
            <div class="glass"><small>Built-up area</small><b>2,128 sq ft</b></div>
            <div class="glass"><small>Price / sq ft</small><b>AED 1,457</b></div>
          </div> -->

          <article id="overview" class="pd-sec reveal">
            <span class="eyebrow">About this property</span>
            <!-- <h2 class="serif">A single-row family home, <em class="gold-text">ready today.</em></h2> -->
            <p class="lead">
              DRE Homes is delighted to offer this stunning 3-bedroom townhouse in Shams, Town Square, Dubai. This
              beautifully maintained home is vacant and ready to move in, offering a single-row location, a
              professionally landscaped garden, and built-in kitchen appliances — an excellent choice for both end users
              and investors seeking privacy, comfort and a family-friendly lifestyle.
            </p>
            <div class="feat-chips">
              <span
                ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Single row</span
              ><span
                ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Vacant</span
              ><span
                ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Landscaped garden</span
              ><span
                ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Private garden</span
              ><span
                ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Built-in kitchen appliances</span
              ><span
                ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Spacious living &amp; dining area</span
              >
            </div>
          </article>

          <article id="details" class="pd-sec reveal">
            <span class="eyebrow">Property Details</span>
            <!-- <h2 class="serif">At a <em class="gold-text">glance.</em></h2> -->
            <dl class="kv">
              <div>
                <dt>Built-up area</dt>
                <dd>2,128 sq ft</dd>
              </div>
              <div>
                <dt>Plot size</dt>
                <dd>1,872.92 sq ft</dd>
              </div>
              <div>
                <dt>Bedrooms</dt>
                <dd>3</dd>
              </div>
              <div>
                <dt>Bathrooms</dt>
                <dd>4</dd>
              </div>
              <div>
                <dt>Parking</dt>
                <dd>2 covered spaces</dd>
              </div>
              <div>
                <dt>Furnishing</dt>
                <dd>Semi furnished</dd>
              </div>
              <div>
                <dt>View</dt>
                <dd>Community</dd>
              </div>
              <div>
                <dt>Balcony</dt>
                <dd>Yes</dd>
              </div>
              <div>
                <dt>Position</dt>
                <dd>Single row</dd>
              </div>
              <div>
                <dt>Status</dt>
                <dd>Vacant · ready to move in</dd>
              </div>
            </dl>
          </article>

          <article id="amenities" class="pd-sec reveal">
            <span class="eyebrow">Amenities &amp; Facilities</span>
            <!-- <h2 class="serif">Everything on <em class="gold-text">your doorstep.</em></h2> -->
            <ul class="am-list">
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Town Square Park
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Landscaped parks &amp; green open spaces
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Swimming pools
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Fully equipped gym
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Children's play areas
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >13 km cycling trails
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >10 km jogging trails
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Basketball courts
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Football pitch
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Tennis &amp; padel courts
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Skate park
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Splash pad &amp; kids' water play
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Dog park
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Carousel &amp; children's train
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Outdoor cinema
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Boutique hotel
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Restaurants &amp; cafés
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Spinneys supermarket
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Retail shops &amp; daily conveniences
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >Community mosque
              </li>
              <li>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 12l4 4 10-10"></path></svg
                >24/7 security &amp; gated community
              </li>
            </ul>
          </article>

          <article id="location" class="pd-sec reveal">
            <span class="eyebrow">Property Location</span>
            <!-- <h2 class="serif">Shams, <em class="gold-text">Town Square.</em></h2> -->
            <div class="loc-grid">
              <div class="map-wrap glass" id="mapWrap">
                <button
                  class="map-load"
                  id="mapLoad"
                  data-map="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4299.753017823419!2d55.28216975181064!3d25.012111521879863!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f71007b132549%3A0xa564b0f522bc2afd!2sShams%2C%20Townsquare!5e0!3m2!1sen!2sin!4v1790410628960!5m2!1sen!2sin"
                >
                  <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28">
                    <path
                      d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                    ></path></svg
                  ><b>Load interactive map</b><small>Google Maps</small>
                </button>
              </div>
              <ol class="drive glass">
                <li>
                  <b>2<small>min</small></b
                  ><span>Town Square Park</span>
                </li>
                <li>
                  <b>5<small>min</small></b
                  ><span>Ranches Souk</span>
                </li>
                <li>
                  <b>15<small>min</small></b
                  ><span>Global Village</span>
                </li>
                <li>
                  <b>15<small>min</small></b
                  ><span>Dubai Miracle Garden</span>
                </li>
                <li>
                  <b>25<small>min</small></b
                  ><span>Dubai Hills Mall</span>
                </li>
                <li>
                  <b>25<small>min</small></b
                  ><span>Downtown Dubai</span>
                </li>
                <li>
                  <b>25<small>min</small></b
                  ><span>Dubai Marina</span>
                </li>
                <li>
                  <b>35<small>min</small></b
                  ><span>Dubai International Airport (DXB)</span>
                </li>
                <li class="road"><b>⇄</b><span>Easy access to Al Qudra Road &amp; Emirates Road</span></li>
              </ol>
            </div>
            <div class="">
              
              <div>
                <h3>About Town Square</h3>
                <p>
                  A vibrant master community by Nshama known for its green spaces, recreational facilities, retail,
                  cafés and schools — a well-connected lifestyle with easy access to major roads in a peaceful suburban
                  setting.
                </p>
              </div>
            </div>
          </article>

          <article id="mortgage" class="pd-sec reveal">
            <span class="eyebrow">Instant mortgage estimate</span>
            <h2 class="serif">What could you <em class="gold-text">pay monthly?</em></h2>
            <div class="mort glass">
              <div class="mg-in">
                <label
                  ><span>Purchase price</span>
                  <div class="mv"><span>AED</span><input id="mPrice" inputmode="numeric" value="3,100,000" /></div
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
                  ><span>Loan duration <b id="mYrV">25 years</b></span
                  ><input type="range" id="mYr" min="5" max="25" step="1" value="25"
                /></label>
              </div>
              <div class="m-out">
                <small>Estimated monthly payment</small>
                <div class="m-big gold-text" id="mOut">—</div>
                <ul>
                  <li><span>Loan amount</span><b id="mLoan">—</b></li>
                  <li><span>Down payment</span><b id="mDown">—</b></li>
                  <li><span>Total interest</span><b id="mInt">—</b></li>
                </ul>
                <p>Indicative only — excludes DLD fees and bank charges.</p>
                <a class="btn btn-gold" href="mortgage-advisory.php">Apply for a loan →</a>
              </div>
            </div>
          </article>

          <span id="contact" aria-hidden="true"></span>
          <article id="enquire" class="pd-sec reveal">
            <div class="enq glass acc-edge">
              <div>
                <span class="eyebrow">Book a viewing</span>
                <h2 class="serif">See it <em class="gold-text">in person.</em></h2>
                <p class="lead">Fill in the form and a DRE advisor will arrange a viewing at a time that suits you.</p>
              </div>
              <form class="form" id="ctaForm" novalidate="">
                <input name="name" placeholder="Your name" autocomplete="name" required="" />
                <input id="ctaPhone" name="phone" type="tel" placeholder="Your phone" autocomplete="tel" required="" />
                <input type="hidden" name="phone_full" id="ctaPhoneFull" />
                <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
                <input name="email" type="email" placeholder="Your email" autocomplete="email" />
                <input
                  name="message"
                  placeholder="Your message"
                  value="I'd like to view Shams Townhouses (REF DRE-S-16913)."
                />
                <input
                  type="hidden"
                  name="page_url"
                  value="https://drehomes.com/buy-property/shams-townhouses-town-square-dubai"
                />
                <button class="btn btn-gold" style="justify-content: center">Request a viewing</button>
              </form>
            </div>
          </article>

          <a class="repair glass reveal" href="property-management.php">
            <span class="rp-ic"
              ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path
                  d="M14.7 6.3a4 4 0 00-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 005.4-5.4l-2.5 2.5-2.4-.6-.6-2.4z"
                ></path></svg
            ></span>
            <div>
              <b>Home repairs made easy</b><span>We take care of everything — quick, reliable and hassle-free.</span>
            </div>
            <em>Explore →</em>
          </a>
        </div>

        <aside class="pd-side">
          <div class="side-card glass acc-edge">
            <small>Price</small>
            <div class="sc-price gold-text">AED 3,100,000</div>
            <p class="sc-ppf">REF DRE-S-16913</p>
            <ul class="sc-facts">
              <li><span>Type</span><b>Townhouse</b></li>
              <li><span>Community</span><b>Shams, Town Square</b></li>
              <li><span>Beds / baths</span><b>3 / 4</b></li>
              <li><span>Built-up area</span><b>2,128 sq ft</b></li>
              <li><span>Status</span><b>Vacant</b></li>
            </ul>
            <div class="sc-ct">
              <a
                class="btn btn-glass sc-wa"
                href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Shams%20Townhouses%2C%20Town%20Square%20(REF%20DRE-S-16913%2C%20AED%203%2C100%2C000).%20https://drehomes.com/buy-property/shams-townhouses-town-square-dubai"
                target="_blank"
                rel="noopener"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                  ></path></svg
                >WhatsApp</a
              >
              <button class="btn btn-glass" id="pdVideo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="16" height="16">
                  <rect x="2.5" y="6" width="13" height="12" rx="3"></rect>
                  <path d="M15.5 10.5l6-3.5v10l-6-3.5z"></path></svg
                >Video tour
              </button>
            </div>
            <a class="btn btn-gold sc-book" href="#enquire">Book a viewing →</a>
            <a class="sc-call" href="tel:80037373">Or call <b>800 37373</b></a>
          </div>
          <div class="verify glass">
            <a
              href="https://www.bayut.com/api/listing/16639946/permitValidation/MEUCID5c4qWD7pMQ1DQGa1lqMuIWO3TPK4wyYMbu2JOriCAoAiEA6OHGWrpsxXdt-jWDzN7xWgfkBeJB3PWGMDfx3KHDLuw"
              target="_blank"
              rel="noopener"
              class="vq"
              aria-label="Verify listing permit"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/qr/proj_qr_img_37_imgi_172_default.png"
                alt="Listing permit QR code"
            /></a>
            <div>
              <b>Verified listing</b>
              <dl>
                <div>
                  <dt>Zone</dt>
                  <dd>Al Yelayiss 2</dd>
                </div>
                <div>
                  <dt>RERA</dt>
                  <dd>652</dd>
                </div>
                <div>
                  <dt>BRN</dt>
                  <dd>78186</dd>
                </div>
              </dl>
              <a
                href="https://www.bayut.com/api/listing/16639946/permitValidation/MEUCID5c4qWD7pMQ1DQGa1lqMuIWO3TPK4wyYMbu2JOriCAoAiEA6OHGWrpsxXdt-jWDzN7xWgfkBeJB3PWGMDfx3KHDLuw"
                target="_blank"
                rel="noopener"
                >Scan or tap to verify ↗</a
              >
            </div>
          </div>
        </aside>
      </div>
    </section>

    <section id="similar" style="padding-top: 40px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">More like this</span>
            <h2 class="serif">Similar homes <em class="gold-text">for sale</em></h2>
          </div>
          <div class="car-nav">
            <button id="simPrev" aria-label="Previous">←</button><button id="simNext" aria-label="Next">→</button>
          </div>
        </div>
        <div class="sim-track" id="simTrack">
          <article class="oc sim">
            <a class="oc-img" href="https://drehomes.com/buy-property/townhouse-for-sale-in-south-bay-3"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_6281_1312x894 (3).webp"
                alt="Townhouse for sale in South Bay 3, South Bay"
                loading="lazy"
              /><span class="badge">Townhouse</span><span class="oc-loc">South Bay</span></a
            >
            <div class="oc-b">
              <h3>
                <a href="https://drehomes.com/buy-property/townhouse-for-sale-in-south-bay-3"
                  >Townhouse for sale in South Bay 3, South Bay</a
                >
              </h3>
              <div class="specs"><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path
                      d="M3 18V8M21 18v-5a3 3 0 00-3-3H10v8M3 14h18M6.5 11.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"
                    ></path></svg
                  >3 bed</span><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path
                      d="M4 12h16v3a4 4 0 01-4 4H8a4 4 0 01-4-4zM6 12V6a2 2 0 014 0M7 19l-1 2M17 19l1 2"
                    ></path></svg
                  > 4 bath</span><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"></path></svg
                  >2,098 sq ft</span></div>
              <div class="oc-meta">
                <div class="pr"><small>Price</small><b>AED 3,199,998</b></div>
              </div>
            </div>
            
          </article>
          <article class="oc sim">
            <a class="oc-img" href="https://drehomes.com/buy-property/the-mayfair-for-sale-in-town-square-dubai"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_6348_mayfair-img1.webp"
                alt="The Mayfair for sale in Town Square"
                loading="lazy"
              /><span class="badge">Apartment</span><span class="oc-loc">Town Square Dubai</span></a
            >
            <div class="oc-b">
              <h3>
                <a href="https://drehomes.com/buy-property/the-mayfair-for-sale-in-town-square-dubai"
                  >The Mayfair for sale in Town Square</a
                >
              </h3>
              <div class="specs"><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path
                      d="M3 18V8M21 18v-5a3 3 0 00-3-3H10v8M3 14h18M6.5 11.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"
                    ></path></svg
                  >3 bed</span><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path
                      d="M4 12h16v3a4 4 0 01-4 4H8a4 4 0 01-4-4zM6 12V6a2 2 0 014 0M7 19l-1 2M17 19l1 2"
                    ></path></svg
                  > 3 bath</span><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"></path></svg
                  >2,339 sq ft</span></div>
              <div class="oc-meta">
                <div class="pr"><small>Price</small><b>AED 2,600,000</b></div>
              </div>
            </div>
          </article>
          <article class="oc sim">
            <a class="oc-img" href="https://drehomes.com/buy-property/villa-for-sale-primrose-damac-hills-2"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_2986_primrose-img1.jpg"
                alt="Villa for sale in Primrose at Damac Hills 2"
                loading="lazy"
              /><span class="badge">Villa</span><span class="oc-loc">Damac Hills 2</span></a
            >
            <div class="oc-b">
              <h3>
                <a href="https://drehomes.com/buy-property/villa-for-sale-primrose-damac-hills-2"
                  >Villa for sale in Primrose at Damac Hills 2</a
                >
              </h3>
              <div class="specs"><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path
                      d="M3 18V8M21 18v-5a3 3 0 00-3-3H10v8M3 14h18M6.5 11.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"
                    ></path></svg
                  >3 bed</span><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path
                      d="M4 12h16v3a4 4 0 01-4 4H8a4 4 0 01-4-4zM6 12V6a2 2 0 014 0M7 19l-1 2M17 19l1 2"
                    ></path></svg
                  > 4 bath</span><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"></path></svg
                  >1,882 sq ft</span></div>
              <div class="oc-meta">
                <div class="pr"><small>Price</small><b>AED 2,150,000</b></div>
              </div>
            </div>
          </article>
          <article class="oc sim">
            <a
              class="oc-img"
              href="https://drehomes.com/buy-property/villa-for-sale-in-aurum-villas-aster-damac-hills-2-dubai"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_2009_aster-img1.webp"
                alt="Villa for sale in Aurum Villas Aster at Damac Hills 2"
                loading="lazy"
              /><span class="badge">Villa</span><span class="oc-loc">Damac Hills 2</span></a
            >
            <div class="oc-b">
              <h3>
                <a href="https://drehomes.com/buy-property/villa-for-sale-in-aurum-villas-aster-damac-hills-2-dubai"
                  >Villa for sale in Aurum Villas Aster at Damac Hills 2</a
                >
              </h3>
              <div class="specs"><span>3 bed</span><span>5 bath</span><span>2,641 sq ft</span></div>
              <div class="oc-meta">
                <div class="pr"><small>Price</small><b>AED 2,000,000</b></div>
              </div>
            </div>
          </article>
          <article class="oc sim">
            <a class="oc-img" href="https://drehomes.com/buy-property/villa-for-sale-costa-brava-2-damac-lagoons"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9748_costa-brava-2-Img1.jpg"
                alt="Villa For Sale in Costa Brava 2 at Damac Lagoons"
                loading="lazy"
              /><span class="badge">Villa</span><span class="oc-loc">Damac Lagoons</span></a
            >
            <div class="oc-b">
              <h3>
                <a href="https://drehomes.com/buy-property/villa-for-sale-costa-brava-2-damac-lagoons"
                  >Villa For Sale in Costa Brava 2 at Damac Lagoons</a
                >
              </h3>
              <div class="specs"><span>4 bed</span><span>4 bath</span><span>1,550 sq ft</span></div>
              <div class="oc-meta">
                <div class="pr"><small>Price</small><b>AED 2,750,000</b></div>
              </div>
            </div>
          </article>
          <article class="oc sim">
            <a
              class="oc-img"
              href="https://drehomes.com/buy-property/townhouse-for-sale-santorini-phase-2-damac-lagoons"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_5404_Santorini-2-img1.jpg"
                alt="Townhouse for sale in Satorini Phase 2 by Damac, Damac Lagoons"
                loading="lazy"
              /><span class="badge">Townhouse</span><span class="oc-loc">Damac Lagoons</span></a
            >
            <div class="oc-b">
              <h3>
                <a href="https://drehomes.com/buy-property/townhouse-for-sale-santorini-phase-2-damac-lagoons"
                  >Townhouse for sale in Satorini Phase 2 by Damac, Damac Lagoons</a
                >
              </h3>
              <div class="specs"><span>5 bed</span><span>6 bath</span><span>1,550 sq ft</span></div>
              <div class="oc-meta">
                <div class="pr"><small>Price</small><b>AED 3,550,000</b></div>
              </div>
            </div>
          </article>
          <article class="oc sim">
            <a class="oc-img" href="https://drehomes.com/buy-property/hayat-boulevard-1-a-town-square-dubai"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9900_imgi_8_844157173-800x600.jpg"
                alt="Hayat Boulevard 1A, Hayat Boulevard, Town Square"
                loading="lazy"
              /><span class="badge">Apartment</span><span class="oc-loc">Town Square</span></a
            >
            <div class="oc-b">
              <h3>
                <a href="https://drehomes.com/buy-property/hayat-boulevard-1-a-town-square-dubai"
                  >Hayat Boulevard 1A, Hayat Boulevard, Town Square</a
                >
              </h3>
              <div class="specs"><span>2 bed</span><span>2 bath</span><span>933 sq ft</span></div>
              <div class="oc-meta">
                <div class="pr"><small>Price</small><b>AED 1,925,000</b></div>
              </div>
            </div>
          </article>
          <article class="oc sim">
            <a class="oc-img" href="https://drehomes.com/buy-property/jenna-main-square-dubai"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_182_imgi_16_838311759-800x600.jpg"
                alt="Jenna Main Square 2, Jenna Main Square, Town Square"
                loading="lazy"
              /><span class="badge">Apartment</span><span class="oc-loc">Town Square</span></a
            >
            <div class="oc-b">
              <h3>
                <a href="https://drehomes.com/buy-property/jenna-main-square-dubai"
                  >Jenna Main Square 2, Jenna Main Square, Town Square</a
                >
              </h3>
              <div class="specs"><span>2 bed</span><span>2 bath</span><span>1,701 sq ft</span></div>
              <div class="oc-meta">
                <div class="pr"><small>Price</small><b>AED 1,649,999</b></div>
              </div>
            </div>
          </article>
          <article class="oc sim">
            <a class="oc-img" href="https://drehomes.com/buy-property/reportage-village-dubai"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_7907_imgi_13_855465957-800x600.jpg"
                alt="Reportage Village"
                loading="lazy"
              /><span class="badge">Villa</span><span class="oc-loc">Dubai</span></a
            >
            <div class="oc-b">
              <h3><a href="https://drehomes.com/buy-property/reportage-village-dubai">Reportage Village</a></h3>
              <div class="specs"><span>2 bed</span><span>3 bath</span><span>1,402 sq ft</span></div>
              <div class="oc-meta">
                <div class="pr"><small>Price</small><b>AED 1,600,000</b></div>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <dialog class="lb" id="lb" aria-label="Photo gallery">
      <button class="lb-x" id="lbX" aria-label="Close">×</button
      ><button class="lb-n lb-p" id="lbP" aria-label="Previous">←</button><img id="lbImg" alt="" /><button
        class="lb-n lb-nx"
        id="lbN"
        aria-label="Next"
      >
        →
      </button>
      <p id="lbC"></p>
    </dialog>

    <?php include('common/footer.php'); ?>