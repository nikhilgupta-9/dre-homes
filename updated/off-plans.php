<?php include('common/header.php'); ?>

    <!-- ===== OFF-PLAN: HERO ===== -->
    <header class="ph-hero op-hero">
      <div class="ph-bg" style="background-image: url(https://drehomes.com/images/home-sec/offplan-property.jpg)"></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="index.php">Home</a><span>/</span><a href="#">Properties</a><span>/</span
          ><span aria-current="page">Off-Plan</span>
        </div>
        <span class="eyebrow">Off-Plan Properties · UAE</span>
        <h1 class="serif">
          <span class="gold-text">1,052</span> off-plan projects. <em class="gold-text">One trusted guide.</em>
        </h1>
        <p class="sub">
          New launches from the UAE’s leading developers — with payment plans, Golden Visa eligibility and expert advice
          from DRE’s off-plan specialists.
        </p>
        <form class="op-search glass" id="opSearch" role="search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="M20 20l-3.5-3.5"></path>
          </svg>
          <input
            id="qIn"
            type="search"
            placeholder="Search a project, developer or area — e.g. Emaar, Dubai Islands…"
            aria-label="Search projects"
          />
          <button class="btn btn-gold" type="submit">Search</button>
        </form>
        
      </div>
    </header>

    <!-- ===== LISTING ===== -->
    <section id="listing" class="op-list">
      <div class="wrap">
        <div class="fbar glass" id="fbar">
          <div class="fb-row">
            <div class="seg" id="emSeg" role="tablist" aria-label="Emirate">
              <button role="tab" data-e="" class="on">All UAE</button><button role="tab" data-e="Dubai">Dubai</button
              ><button role="tab" data-e="Abu Dhabi">Abu Dhabi</button
              ><button role="tab" data-e="Sharjah">Sharjah</button
              ><button role="tab" data-e="Umm Al Quwain">Umm Al Quwain</button>
            </div>
            <label class="fsel"
              ><span>Developer</span
              ><select id="devSel">
                <option value="">All developers</option>
                <option>Aldar</option>
                <option>Emaar Properties</option>
                <option>Binghatti Developers</option>
                <option>Sobha Realty</option>
                <option>Imtiaz Developments</option>
                <option>MODON</option>
                <option>Eagle Hills</option>
                <option>Avenew Development</option>
                <option>BEYOND</option>
                <option>BNW Developments</option>
                <option>Ohana Development</option>
                <option>ORA Developers</option>
                <option>Wasl Group</option>
                <option>Azizi Developments</option>
                <option>Centurion Developer</option>
                <option>SOL Properties</option>
                <option>Origami Development</option>
                <option>Tarrad Development</option>
                <option>Expo City Dubai</option>
                <option>Taraf Holding</option>
                <option>Ellington Properties</option>
                <option>Alef Group</option>
                <option>AMIS Properties</option>
                <option>Object One Development</option>
                <option>Mira Developments</option>
                <option>Sharafi Development</option>
                <option>Samana Developers</option>
                <option>Amwaj Development</option>
                <option>Danube Properties</option>
                <option>Damac Properties</option>
                <option>Nshama</option>
                <option>Aqua Properties</option>
              </select></label
            >
            <label class="fsel"
              ><span>Area</span
              ><select id="locSel">
                <option value="">All areas</option>
                <option>Abu Dhabi</option>
                <option>Academic City, Dubai</option>
                <option>Al Furjan</option>
                <option>Al Furjan, Dubai</option>
                <option>Al Jaddaf</option>
                <option>Al Jurf</option>
                <option>Al Mamzar, Sharjah</option>
                <option>Al Reem Island</option>
                <option>Aljada, Sharjah</option>
                <option>Arabia, Dubai</option>
                <option>Arjan, Dubai</option>
                <option>Damac Islands 2</option>
                <option>District 11, JVC</option>
                <option>District 11, Meydan</option>
                <option>Dubai</option>
                <option>Dubai Creek Harbour</option>
                <option>Dubai Hills Estate</option>
                <option>Dubai Islands</option>
                <option>Dubai Motor City</option>
                <option>Dubai South</option>
                <option>Dubai Sports City</option>
                <option>Dubailand</option>
                <option>Emaar South</option>
                <option>Expo City Dubai</option>
                <option>Expo Living, Dubai</option>
                <option>Fahid Island</option>
                <option>Fahid Island, Abu Dhabi</option>
                <option>Ghantoot</option>
                <option>Hudayriyat Island, Abu Dhabi</option>
                <option>Jumeirah Village Circle</option>
                <option>MBR City, Dubai</option>
                <option>Majan, Dubai</option>
                <option>Marjan Island</option>
                <option>Masdar City, Abu Dhabi</option>
                <option>Motor City, Dubai</option>
                <option>RAK Central</option>
                <option>Ramhan Island, Abu Dhabi</option>
                <option>Rashid Yachts &amp; Marina</option>
                <option>Saadiyat</option>
                <option>Saadiyat Cultural District</option>
                <option>Saadiyat Island</option>
                <option>Sharjah</option>
                <option>Sheikh Zayed Road</option>
                <option>Sobha City, Abu Dhabi</option>
                <option>Sobha Sanctuary</option>
                <option>Sobha Siniya Island</option>
                <option>The Valley, Dubai</option>
                <option>Town Square Dubai</option>
                <option>Warsan, Dubai</option>
                <option>Yas Acres</option>
                <option>Yas Island</option>
              </select></label
            >
            <label class="fsel"
              ><span>Min price</span
              ><select id="pMin">
                <option value="">Any</option>
                <option value="500000">AED 500,000</option>
                <option value="750000">AED 750,000</option>
                <option value="1000000">AED 1,000,000</option>
                <option value="2000000">AED 2,000,000</option>
                <option value="3000000">AED 3,000,000</option>
                <option value="4000000">AED 4,000,000</option>
                <option value="5000000">AED 5,000,000</option>
                <option value="6000000">AED 6,000,000</option>
                <option value="7000000">AED 7,000,000</option>
                <option value="8000000">AED 8,000,000</option>
                <option value="9000000">AED 9,000,000</option>
                <option value="10000000">AED 10,000,000</option>
                <option value="15000000">AED 15,000,000</option>
                <option value="25000000">AED 25,000,000</option>
              </select></label
            >
            <label class="fsel"
              ><span>Max price</span
              ><select id="pMax">
                <option value="">Any</option>
                <option value="500000">AED 500,000</option>
                <option value="750000">AED 750,000</option>
                <option value="1000000">AED 1,000,000</option>
                <option value="2000000">AED 2,000,000</option>
                <option value="3000000">AED 3,000,000</option>
                <option value="4000000">AED 4,000,000</option>
                <option value="5000000">AED 5,000,000</option>
                <option value="6000000">AED 6,000,000</option>
                <option value="7000000">AED 7,000,000</option>
                <option value="8000000">AED 8,000,000</option>
                <option value="9000000">AED 9,000,000</option>
                <option value="10000000">AED 10,000,000</option>
                <option value="15000000">AED 15,000,000</option>
                <option value="25000000">AED 25,000,000</option>
              </select></label
            >
            <button class="fmore" id="moreBtn" aria-expanded="false" aria-controls="moreF">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M4 6h16M7 12h10M10 18h4"></path></svg
              >Bedrooms<b id="bedCount"></b>
            </button>
          </div>
          <div class="more" id="moreF" hidden="">
            <div class="beds" id="bedChips">
              <button type="button" data-b="STD">Studio</button><button type="button" data-b="1">1</button
              ><button type="button" data-b="1.5">1.5</button><button type="button" data-b="2">2</button
              ><button type="button" data-b="3">3</button><button type="button" data-b="3.5">3.5</button
              ><button type="button" data-b="4">4</button><button type="button" data-b="5">5</button
              ><button type="button" data-b="6">6</button><button type="button" data-b="7">7</button
              ><button type="button" data-b="8">8</button><button type="button" data-b="9">9</button>
            </div>
          </div>
        </div>

        <div class="rbar">
          <p id="rCount"></p>
          <div class="rbar-r">
            <button class="clear" id="clearAll" hidden="">Clear filters ×</button>
            <label class="fsel sort"
              ><span>Sort</span
              ><select id="sortSel">
                <option value="feat">Featured</option>
                <option value="pa">Price: low to high</option>
                <option value="pd">Price: high to low</option>
                <option value="az">Name: A–Z</option>
              </select></label
            >
            <button class="favbtn" id="favOnly" aria-pressed="false" title="Show saved only">
              ♡ <b id="favCount">0</b>
            </button>
            <div class="vt" role="group" aria-label="View">
              <button data-v="grid" class="on" aria-label="Grid view">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <rect x="3" y="3" width="8" height="8" rx="2"></rect>
                  <rect x="13" y="3" width="8" height="8" rx="2"></rect>
                  <rect x="3" y="13" width="8" height="8" rx="2"></rect>
                  <rect x="13" y="13" width="8" height="8" rx="2"></rect>
                </svg></button
              ><button data-v="list" aria-label="List view">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <rect x="3" y="4" width="18" height="4" rx="1.5"></rect>
                  <rect x="3" y="10" width="18" height="4" rx="1.5"></rect>
                  <rect x="3" y="16" width="18" height="4" rx="1.5"></rect>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div class="opg" id="opGrid" aria-live="polite">
          <article
            class="oc"
            data-e="Dubai"
            data-d="Emaar Properties"
            data-l="Dubai Hills Estate"
            data-p="1710000"
            data-bmin="1"
            data-bmax="3"
            data-u="palace-residences-hillside-in-dubai"
            data-name="Palace Residences Hillside"
          >
            <a class="oc-img" href="single-property.php"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1179_palace-residences-hillside.jpg"
                alt="Palace Residences Hillside"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Hills Estate</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="palace-residences-hillside-in-dubai"
              aria-pressed="false"
              aria-label="Save Palace Residences Hillside"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3><a href="single-property.php">Palace Residences Hillside</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,710,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="single-property.php">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Palace%20Residences%20Hillside%20by%20Emaar%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Palace Residences Hillside on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="ORA Developers"
            data-l="Ghantoot"
            data-p="0"
            data-bmin="5"
            data-bmax="6"
            data-blist="5 6"
            data-u="y-views-by-ora"
            data-name="Y Views at BAYN"
          >
            <a class="oc-img" href="https://drehomes.com/property/y-views-by-ora"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1266_Bayn-Img1.jpg"
                alt="Y Views at BAYN"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Ghantoot</span
              ></a
            >
            <button class="oc-fav" data-u="y-views-by-ora" aria-pressed="false" aria-label="Save Y Views at BAYN">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By ORA Developers</span>
              <h3><a href="https://drehomes.com/property/y-views-by-ora">Y Views at BAYN</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>5 &amp; 6</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/y-views-by-ora">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Y%20Views%20at%20BAYN%20by%20ORA%20Developers."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Y Views at BAYN on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Wasl Group"
            data-l="Dubai"
            data-p="0"
            data-bmin="1"
            data-bmax="4"
            data-u="avenue-park-towers-2"
            data-name="Avenue Park Towers 2"
          >
            <a class="oc-img" href="https://drehomes.com/property/avenue-park-towers-2"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_5133_Avenew-Park-Towers-Img3.jpg"
                alt="Avenue Park Towers 2"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="avenue-park-towers-2"
              aria-pressed="false"
              aria-label="Save Avenue Park Towers 2"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Wasl Group</span>
              <h3><a href="https://drehomes.com/property/avenue-park-towers-2">Avenue Park Towers 2</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 4</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/avenue-park-towers-2">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Avenue%20Park%20Towers%202%20by%20Wasl%20Group."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Avenue Park Towers 2 on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Eagle Hills"
            data-l="Ramhan Island, Abu Dhabi"
            data-p="0"
            data-u="the-bristol-hotel-residences"
            data-name="The Bristol Hotel &amp; Residences"
          >
            <a class="oc-img" href="https://drehomes.com/property/the-bristol-hotel-residences"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_3317_the-Bristol-Hotel_img2.jpg"
                alt="The Bristol Hotel &amp; Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Ramhan Island, Abu Dhabi</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="the-bristol-hotel-residences"
              aria-pressed="false"
              aria-label="Save The Bristol Hotel &amp; Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Eagle Hills</span>
              <h3>
                <a href="https://drehomes.com/property/the-bristol-hotel-residences"
                  >The Bristol Hotel &amp; Residences</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>—</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/the-bristol-hotel-residences"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20The%20Bristol%20Hotel%20%26%20Residences%20by%20Eagle%20Hills."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about The Bristol Hotel &amp; Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="MODON"
            data-l="Abu Dhabi"
            data-p="8700000"
            data-bmin="4"
            data-bmax="6"
            data-u="wadeem-gardens"
            data-name="Wadeem Gardens"
          >
            <a class="oc-img" href="https://drehomes.com/property/wadeem-gardens"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_6571_wadeem-gardens-hero.jpg"
                alt="Wadeem Gardens"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Abu Dhabi</span
              ></a
            >
            <button class="oc-fav" data-u="wadeem-gardens" aria-pressed="false" aria-label="Save Wadeem Gardens">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By MODON</span>
              <h3><a href="https://drehomes.com/property/wadeem-gardens">Wadeem Gardens</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>4 - 6</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 8,700,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/wadeem-gardens">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Wadeem%20Gardens%20by%20MODON."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Wadeem Gardens on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Eagle Hills"
            data-l="Abu Dhabi"
            data-p="0"
            data-u="ramhan-marina"
            data-name="Ramhan Marina"
          >
            <a class="oc-img" href="https://drehomes.com/property/ramhan-marina"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_2683_Rahman-Abu-Dhabi-Img1.jpg"
                alt="Ramhan Marina"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Abu Dhabi</span
              ></a
            >
            <button class="oc-fav" data-u="ramhan-marina" aria-pressed="false" aria-label="Save Ramhan Marina">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Eagle Hills</span>
              <h3><a href="https://drehomes.com/property/ramhan-marina">Ramhan Marina</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>—</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/ramhan-marina">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Ramhan%20Marina%20by%20Eagle%20Hills."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Ramhan Marina on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Emaar Properties"
            data-l="The Valley, Dubai"
            data-p="0"
            data-bmin="3"
            data-bmax="4"
            data-u="alva-the-valley-dubai"
            data-name="Alva by Emaar"
          >
            <a class="oc-img" href="https://drehomes.com/property/alva-the-valley-dubai"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9751_Alva-Img2.jpg"
                alt="Alva by Emaar"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >The Valley, Dubai</span
              ></a
            >
            <button class="oc-fav" data-u="alva-the-valley-dubai" aria-pressed="false" aria-label="Save Alva by Emaar">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3><a href="https://drehomes.com/property/alva-the-valley-dubai">Alva by Emaar</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>3 - 4</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/alva-the-valley-dubai">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Alva%20by%20Emaar%20by%20Emaar%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Alva by Emaar on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Aldar"
            data-l="Saadiyat Island"
            data-p="2950000"
            data-bmin="1"
            data-bmax="3"
            data-u="sei-saadiyat-island"
            data-name="Sei Saadiyat"
          >
            <a class="oc-img" href="https://drehomes.com/property/sei-saadiyat-island"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_6719_Sei-Saadiyat-Img2.jpg"
                alt="Sei Saadiyat"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Saadiyat Island</span
              ></a
            >
            <button class="oc-fav" data-u="sei-saadiyat-island" aria-pressed="false" aria-label="Save Sei Saadiyat">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aldar</span>
              <h3><a href="https://drehomes.com/property/sei-saadiyat-island">Sei Saadiyat</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 2,950,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/sei-saadiyat-island">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Sei%20Saadiyat%20by%20Aldar."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Sei Saadiyat on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Sharjah"
            data-d="Azizi Developments"
            data-l="Sharjah"
            data-p="0"
            data-bmin="3"
            data-bmax="6"
            data-u="azizi-florence-sharjah"
            data-name="Azizi Florence"
          >
            <a class="oc-img" href="https://drehomes.com/property/azizi-florence-sharjah"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1882_Azizi-Florence-Image3.jpg"
                alt="Azizi Florence"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Sharjah</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="azizi-florence-sharjah"
              aria-pressed="false"
              aria-label="Save Azizi Florence"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Azizi Developments</span>
              <h3><a href="https://drehomes.com/property/azizi-florence-sharjah">Azizi Florence</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>3 - 6</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/azizi-florence-sharjah">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Azizi%20Florence%20by%20Azizi%20Developments."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Azizi Florence on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Aldar"
            data-l="Abu Dhabi"
            data-p="0"
            data-u="aldar-al-ghadeer-phase-3"
            data-name="Al Ghadeer Phase 3"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/aldar-al-ghadeer-phase-3"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_8970_al_ghadeer_img1"
                alt="Al Ghadeer Phase 3"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Abu Dhabi</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="aldar-al-ghadeer-phase-3"
              aria-pressed="false"
              aria-label="Save Al Ghadeer Phase 3"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aldar</span>
              <h3><a href="https://drehomes.com/property/aldar-al-ghadeer-phase-3">Al Ghadeer Phase 3</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>—</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/aldar-al-ghadeer-phase-3">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Al%20Ghadeer%20Phase%203%20by%20Aldar."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Al Ghadeer Phase 3 on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Imtiaz Developments"
            data-l="Dubailand"
            data-p="0"
            data-bmin="0"
            data-bmax="3"
            data-u="the-archive-by-imtiaz"
            data-name="The Archive"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/the-archive-by-imtiaz"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_5432_The-Archieve-Image1.jpg"
                alt="The Archive"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubailand</span
              ></a
            >
            <button class="oc-fav" data-u="the-archive-by-imtiaz" aria-pressed="false" aria-label="Save The Archive">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Imtiaz Developments</span>
              <h3><a href="https://drehomes.com/property/the-archive-by-imtiaz">The Archive</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 3</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/the-archive-by-imtiaz">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20The%20Archive%20by%20Imtiaz%20Developments."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about The Archive on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Aldar"
            data-l="Abu Dhabi"
            data-p="0"
            data-u="marsa-al-saadiyat"
            data-name="Marsa Al Saadiyat"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/marsa-al-saadiyat"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9531_Mamsa-Img1.jpg"
                alt="Marsa Al Saadiyat"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Abu Dhabi</span
              ></a
            >
            <button class="oc-fav" data-u="marsa-al-saadiyat" aria-pressed="false" aria-label="Save Marsa Al Saadiyat">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aldar</span>
              <h3><a href="https://drehomes.com/property/marsa-al-saadiyat">Marsa Al Saadiyat</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>—</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/marsa-al-saadiyat">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Marsa%20Al%20Saadiyat%20by%20Aldar."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Marsa Al Saadiyat on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Aldar"
            data-l="Yas Island"
            data-p="1650000"
            data-bmin="0"
            data-bmax="3"
            data-u="the-canopies-yas-point"
            data-name="The Canopies"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/the-canopies-yas-point"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_7859_the-canopies-yas-point-island.jpg"
                alt="The Canopies"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Yas Island</span
              ></a
            >
            <button class="oc-fav" data-u="the-canopies-yas-point" aria-pressed="false" aria-label="Save The Canopies">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aldar</span>
              <h3><a href="https://drehomes.com/property/the-canopies-yas-point">The Canopies</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,650,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/the-canopies-yas-point">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20The%20Canopies%20by%20Aldar."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about The Canopies on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Emaar Properties"
            data-l="Emaar South"
            data-p="1250000"
            data-bmin="1"
            data-bmax="3"
            data-u="golf-trails-emaar-south"
            data-name="Golf Trails"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/golf-trails-emaar-south"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_3249_golf-trails-Img4.jpg"
                alt="Golf Trails"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Emaar South</span
              ></a
            >
            <button class="oc-fav" data-u="golf-trails-emaar-south" aria-pressed="false" aria-label="Save Golf Trails">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3><a href="https://drehomes.com/property/golf-trails-emaar-south">Golf Trails</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,250,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/golf-trails-emaar-south">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Golf%20Trails%20by%20Emaar%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Golf Trails on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Centurion Developer"
            data-l="Dubai Islands"
            data-p="0"
            data-bmin="1"
            data-bmax="4"
            data-u="flora-bay-residences"
            data-name="Flora Bay Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/flora-bay-residences"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9014_Flora-Bay-img1.jpg"
                alt="Flora Bay Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Islands</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="flora-bay-residences"
              aria-pressed="false"
              aria-label="Save Flora Bay Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Centurion Developer</span>
              <h3><a href="https://drehomes.com/property/flora-bay-residences">Flora Bay Residences</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 4</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/flora-bay-residences">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Flora%20Bay%20Residences%20by%20Centurion%20Developer."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Flora Bay Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="SOL Properties"
            data-l="District 11, JVC"
            data-p="0"
            data-bmin="4"
            data-bmax="4"
            data-u="sol-terra-casa-jvc"
            data-name="SOL Terra Casa"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/sol-terra-casa-jvc"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_7122_SOL-TERRA-CASA-img2.jpg"
                alt="SOL Terra Casa"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >District 11, JVC</span
              ></a
            >
            <button class="oc-fav" data-u="sol-terra-casa-jvc" aria-pressed="false" aria-label="Save SOL Terra Casa">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By SOL Properties</span>
              <h3><a href="https://drehomes.com/property/sol-terra-casa-jvc">SOL Terra Casa</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>4</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/sol-terra-casa-jvc">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20SOL%20Terra%20Casa%20by%20SOL%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about SOL Terra Casa on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Origami Development"
            data-l="Dubai Islands"
            data-p="1670000"
            data-bmin="1"
            data-bmax="3"
            data-u="soma-residences-at-dubai-Islands"
            data-name="SOMA Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/soma-residences-at-dubai-Islands"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_7504_Soma-Residences_dubai.jpg"
                alt="SOMA Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Islands</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="soma-residences-at-dubai-Islands"
              aria-pressed="false"
              aria-label="Save SOMA Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Origami Development</span>
              <h3><a href="https://drehomes.com/property/soma-residences-at-dubai-Islands">SOMA Residences</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,670,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/soma-residences-at-dubai-Islands"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20SOMA%20Residences%20by%20Origami%20Development."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about SOMA Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Emaar Properties"
            data-l="Dubai Creek Harbour"
            data-p="1960000"
            data-bmin="1"
            data-bmax="3"
            data-u="valia-dubai-creek-harbour"
            data-name="Valia Apartments"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/valia-dubai-creek-harbour"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_2741_VALIA_DUBAI_CREEK_HARBOUR_img1.jpg"
                alt="Valia Apartments"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Creek Harbour</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="valia-dubai-creek-harbour"
              aria-pressed="false"
              aria-label="Save Valia Apartments"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3><a href="https://drehomes.com/property/valia-dubai-creek-harbour">Valia Apartments</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,960,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/valia-dubai-creek-harbour"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Valia%20Apartments%20by%20Emaar%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Valia Apartments on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Avenew Development"
            data-l="Motor City, Dubai"
            data-p="0"
            data-u="o1ne-district-in-dubai"
            data-name="O1NE District"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/o1ne-district-in-dubai"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_7530_O1ne-district-1.jpg.jpeg"
                alt="O1NE District"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Motor City, Dubai</span
              ></a
            >
            <button class="oc-fav" data-u="o1ne-district-in-dubai" aria-pressed="false" aria-label="Save O1NE District">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Avenew Development</span>
              <h3><a href="https://drehomes.com/property/o1ne-district-in-dubai">O1NE District</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>—</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/o1ne-district-in-dubai">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20O1NE%20District%20by%20Avenew%20Development."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about O1NE District on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Tarrad Development"
            data-l="Dubailand"
            data-p="567000"
            data-bmin="0"
            data-bmax="2"
            data-u="calesto-tower-dubailand"
            data-name="Celesto 4 Tower"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/calesto-tower-dubailand"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_6575_Celesto-dubailand.jpg"
                alt="Celesto 4 Tower"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubailand</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="calesto-tower-dubailand"
              aria-pressed="false"
              aria-label="Save Celesto 4 Tower"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Tarrad Development</span>
              <h3><a href="https://drehomes.com/property/calesto-tower-dubailand">Celesto 4 Tower</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 567,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/calesto-tower-dubailand">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Celesto%204%20Tower%20by%20Tarrad%20Development."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Celesto 4 Tower on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Expo City Dubai"
            data-l="Expo City Dubai"
            data-p="1692000"
            data-bmin="1"
            data-bmax="3"
            data-u="roudah-at-expo-valley-views"
            data-name="Roudah Apartments"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/roudah-at-expo-valley-views"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_6778_Roudah-at-Expo-Valley-Views.jpg"
                alt="Roudah Apartments"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Expo City Dubai</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="roudah-at-expo-valley-views"
              aria-pressed="false"
              aria-label="Save Roudah Apartments"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Expo City Dubai</span>
              <h3><a href="https://drehomes.com/property/roudah-at-expo-valley-views">Roudah Apartments</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,692,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/roudah-at-expo-valley-views"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Roudah%20Apartments%20by%20Expo%20City%20Dubai."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Roudah Apartments on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Taraf Holding"
            data-l="Masdar City, Abu Dhabi"
            data-p="3220000"
            data-bmin="2"
            data-bmax="6"
            data-u="fay-valley"
            data-name="Fay Valley"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/fay-valley"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_8443_Fay-Valley-Abu-Dhabi.jpg"
                alt="Fay Valley"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Masdar City, Abu Dhabi</span
              ></a
            >
            <button class="oc-fav" data-u="fay-valley" aria-pressed="false" aria-label="Save Fay Valley">♡</button>
            <div class="oc-b">
              <span class="oc-dev">By Taraf Holding</span>
              <h3><a href="https://drehomes.com/property/fay-valley">Fay Valley</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>2 - 6</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 3,220,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/fay-valley">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Fay%20Valley%20by%20Taraf%20Holding."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Fay Valley on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Ellington Properties"
            data-l="MBR City, Dubai"
            data-p="0"
            data-bmin="1"
            data-bmax="3"
            data-u="everly-place-mbr-city"
            data-name="Everly Place"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/everly-place-mbr-city"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_8804_everly-mbr-city.jpg"
                alt="Everly Place"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >MBR City, Dubai</span
              ></a
            >
            <button class="oc-fav" data-u="everly-place-mbr-city" aria-pressed="false" aria-label="Save Everly Place">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Ellington Properties</span>
              <h3><a href="https://drehomes.com/property/everly-place-mbr-city">Everly Place</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 3</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/everly-place-mbr-city">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Everly%20Place%20by%20Ellington%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Everly Place on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Aldar"
            data-l="Yas Acres"
            data-p="0"
            data-bmin="2"
            data-bmax="5"
            data-u="the-orchids-yas-acres"
            data-name="The Orchids Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/the-orchids-yas-acres"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9375_gallery_img_1206_The-Orchids-5.jpg"
                alt="The Orchids Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Yas Acres</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="the-orchids-yas-acres"
              aria-pressed="false"
              aria-label="Save The Orchids Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aldar</span>
              <h3><a href="https://drehomes.com/property/the-orchids-yas-acres">The Orchids Residences</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>2 - 5</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/the-orchids-yas-acres">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20The%20Orchids%20Residences%20by%20Aldar."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about The Orchids Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Binghatti Developers"
            data-l="Al Jaddaf"
            data-p="799999"
            data-bmin="0"
            data-bmax="3"
            data-u="binghatti-wraith"
            data-name="Binghatti Wraith"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/binghatti-wraith"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_841_binghatti-wraith-dubai.jpg"
                alt="Binghatti Wraith"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Al Jaddaf</span
              ></a
            >
            <button class="oc-fav" data-u="binghatti-wraith" aria-pressed="false" aria-label="Save Binghatti Wraith">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Binghatti Developers</span>
              <h3><a href="https://drehomes.com/property/binghatti-wraith">Binghatti Wraith</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 799,999</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/binghatti-wraith">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Binghatti%20Wraith%20by%20Binghatti%20Developers."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Binghatti Wraith on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Emaar Properties"
            data-l="Emaar South"
            data-p="1260000"
            data-bmin="1"
            data-bmax="3"
            data-u="golf-fields-at-emaar-south"
            data-name="Golf Fields"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/golf-fields-at-emaar-south"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1619_GOLF_FIELDS-1.jpg"
                alt="Golf Fields"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Emaar South</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="golf-fields-at-emaar-south"
              aria-pressed="false"
              aria-label="Save Golf Fields"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3><a href="https://drehomes.com/property/golf-fields-at-emaar-south">Golf Fields</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,260,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/golf-fields-at-emaar-south"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Golf%20Fields%20by%20Emaar%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Golf Fields on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="BEYOND"
            data-l="Arabia, Dubai"
            data-p="1000000"
            data-bmin="1"
            data-bmax="3"
            data-u="arancia-yards-by-beyond"
            data-name="Arancia Yards"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/arancia-yards-by-beyond"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_6661_Arancia-Yards-by-BEYOND-2.jpg"
                alt="Arancia Yards"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Arabia, Dubai</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="arancia-yards-by-beyond"
              aria-pressed="false"
              aria-label="Save Arancia Yards"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By BEYOND</span>
              <h3><a href="https://drehomes.com/property/arancia-yards-by-beyond">Arancia Yards</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,000,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/arancia-yards-by-beyond">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Arancia%20Yards%20by%20BEYOND."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Arancia Yards on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Sharjah"
            data-d="Alef Group"
            data-l="Al Mamzar, Sharjah"
            data-p="849000"
            data-bmin="1"
            data-bmax="4"
            data-u="linar-tower-by-alef"
            data-name="Linar Tower"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/linar-tower-by-alef"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_841_Linar-Tower-img.jpg"
                alt="Linar Tower"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Al Mamzar, Sharjah</span
              ></a
            >
            <button class="oc-fav" data-u="linar-tower-by-alef" aria-pressed="false" aria-label="Save Linar Tower">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Alef Group</span>
              <h3><a href="https://drehomes.com/property/linar-tower-by-alef">Linar Tower</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 4</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 849,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/linar-tower-by-alef">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Linar%20Tower%20by%20Alef%20Group."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Linar Tower on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="AMIS Properties"
            data-l="District 11, Meydan"
            data-p="0"
            data-bmin="5"
            data-bmax="6"
            data-u="fleurs-de-jardin"
            data-name="Fleurs de Jardin"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/fleurs-de-jardin"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_3670_fleurs-de-jardin-meydan.jpg"
                alt="Fleurs de Jardin"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >District 11, Meydan</span
              ></a
            >
            <button class="oc-fav" data-u="fleurs-de-jardin" aria-pressed="false" aria-label="Save Fleurs de Jardin">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By AMIS Properties</span>
              <h3><a href="https://drehomes.com/property/fleurs-de-jardin">Fleurs de Jardin</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>5 - 6</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/fleurs-de-jardin">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Fleurs%20de%20Jardin%20by%20AMIS%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Fleurs de Jardin on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Imtiaz Developments"
            data-l="Sheikh Zayed Road"
            data-p="666000"
            data-bmin="0"
            data-bmax="3"
            data-u="raw-district-by-imtiaz"
            data-name="RAW District Phase 2"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/raw-district-by-imtiaz"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_5315_Raw-District-Img2.jpg"
                alt="RAW District Phase 2"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Sheikh Zayed Road</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="raw-district-by-imtiaz"
              aria-pressed="false"
              aria-label="Save RAW District Phase 2"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Imtiaz Developments</span>
              <h3><a href="https://drehomes.com/property/raw-district-by-imtiaz">RAW District Phase 2</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 666,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/raw-district-by-imtiaz">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20RAW%20District%20Phase%202%20by%20Imtiaz%20Developments."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about RAW District Phase 2 on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Sobha Realty"
            data-l="Sobha Sanctuary"
            data-p="999000"
            data-bmin="1"
            data-bmax="2"
            data-u="the-woods-sobha-sanctuary"
            data-name="The Woods Apartments"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/the-woods-sobha-sanctuary"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9716_the-woods-sobha-sanctuary-img1.jpg"
                alt="The Woods Apartments"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Sobha Sanctuary</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="the-woods-sobha-sanctuary"
              aria-pressed="false"
              aria-label="Save The Woods Apartments"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Sobha Realty</span>
              <h3><a href="https://drehomes.com/property/the-woods-sobha-sanctuary">The Woods Apartments</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 999,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/the-woods-sobha-sanctuary"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20The%20Woods%20Apartments%20by%20Sobha%20Realty."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about The Woods Apartments on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Object One Development"
            data-l="Dubai Sports City"
            data-p="1707334"
            data-bmin="0"
            data-bmax="3"
            data-u="aurel1a-residence-at-dubai-sports-city"
            data-name="AUREL1A Residence"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/aurel1a-residence-at-dubai-sports-city"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_2749_Aurelia-Img5.jpg"
                alt="AUREL1A Residence"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Sports City</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="aurel1a-residence-at-dubai-sports-city"
              aria-pressed="false"
              aria-label="Save AUREL1A Residence"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Object One Development</span>
              <h3>
                <a href="https://drehomes.com/property/aurel1a-residence-at-dubai-sports-city">AUREL1A Residence</a>
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,707,334</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/aurel1a-residence-at-dubai-sports-city"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20AUREL1A%20Residence%20by%20Object%20One%20Development."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about AUREL1A Residence on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="MODON"
            data-l="Hudayriyat Island, Abu Dhabi"
            data-p="4200000"
            data-bmin="3"
            data-bmax="6"
            data-u="golf-estates-by-modon"
            data-name="Hudayriyat Golf Estates"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/golf-estates-by-modon"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_3248_Hudayriyat-Img1.jpg"
                alt="Hudayriyat Golf Estates"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Hudayriyat Island, Abu Dhabi</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="golf-estates-by-modon"
              aria-pressed="false"
              aria-label="Save Hudayriyat Golf Estates"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By MODON</span>
              <h3><a href="https://drehomes.com/property/golf-estates-by-modon">Hudayriyat Golf Estates</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>3 - 6</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 4,200,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/golf-estates-by-modon">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Hudayriyat%20Golf%20Estates%20by%20MODON."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Hudayriyat Golf Estates on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Binghatti Developers"
            data-l="Academic City, Dubai"
            data-p="4200000"
            data-bmin="4"
            data-bmax="7"
            data-u="tilal-binghatti-dubai"
            data-name="Tilal Binghatti"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/tilal-binghatti-dubai"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1631_tilal-binghatti-tilal-oasis.jpg"
                alt="Tilal Binghatti"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Academic City, Dubai</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="tilal-binghatti-dubai"
              aria-pressed="false"
              aria-label="Save Tilal Binghatti"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Binghatti Developers</span>
              <h3><a href="https://drehomes.com/property/tilal-binghatti-dubai">Tilal Binghatti</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>4 - 7</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 4,200,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/tilal-binghatti-dubai">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Tilal%20Binghatti%20by%20Binghatti%20Developers."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Tilal Binghatti on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="MODON"
            data-l="Al Reem Island"
            data-p="1680000"
            data-bmin="1"
            data-bmax="3"
            data-blist="1 2 3"
            data-u="tara-park-al-reem-island"
            data-name="Tara Park"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/tara-park-al-reem-island"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_7313_tara-park-by-modon-8.jpg"
                alt="Tara Park"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Al Reem Island</span
              ></a
            >
            <button class="oc-fav" data-u="tara-park-al-reem-island" aria-pressed="false" aria-label="Save Tara Park">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By MODON</span>
              <h3><a href="https://drehomes.com/property/tara-park-al-reem-island">Tara Park</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1, 2,3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,680,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/tara-park-al-reem-island">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Tara%20Park%20by%20MODON."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Tara Park on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Sobha Realty"
            data-l="Sobha City, Abu Dhabi"
            data-p="0"
            data-bmin="1"
            data-bmax="6"
            data-u="sobha-city-abu-dhabi"
            data-name="Sobha City"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/sobha-city-abu-dhabi"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_5939_Sobha-City-Img1.jpg"
                alt="Sobha City"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Sobha City, Abu Dhabi</span
              ></a
            >
            <button class="oc-fav" data-u="sobha-city-abu-dhabi" aria-pressed="false" aria-label="Save Sobha City">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Sobha Realty</span>
              <h3><a href="https://drehomes.com/property/sobha-city-abu-dhabi">Sobha City</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 6</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/sobha-city-abu-dhabi">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Sobha%20City%20by%20Sobha%20Realty."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Sobha City on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Sobha Realty"
            data-l="Sobha City, Abu Dhabi"
            data-p="9050000"
            data-bmin="4"
            data-bmax="6"
            data-u="the-orchard-at-sobha-city"
            data-name="The Orchard"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/the-orchard-at-sobha-city"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_6195_the-orchid-sobha-city-img.jpg"
                alt="The Orchard"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Sobha City, Abu Dhabi</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="the-orchard-at-sobha-city"
              aria-pressed="false"
              aria-label="Save The Orchard"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Sobha Realty</span>
              <h3><a href="https://drehomes.com/property/the-orchard-at-sobha-city">The Orchard</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>4-6</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 9,050,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/the-orchard-at-sobha-city"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20The%20Orchard%20by%20Sobha%20Realty."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about The Orchard on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Sobha Realty"
            data-l="Sobha City, Abu Dhabi"
            data-p="4960000"
            data-bmin="3"
            data-bmax="4"
            data-u="the-terraces-at-sobha-city"
            data-name="The Terraces"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/the-terraces-at-sobha-city"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_387_the-terraces-sobha-city-img.jpg"
                alt="The Terraces"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Sobha City, Abu Dhabi</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="the-terraces-at-sobha-city"
              aria-pressed="false"
              aria-label="Save The Terraces"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Sobha Realty</span>
              <h3><a href="https://drehomes.com/property/the-terraces-at-sobha-city">The Terraces</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>3-4</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 4,960,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/the-terraces-at-sobha-city"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20The%20Terraces%20by%20Sobha%20Realty."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about The Terraces on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Sobha Realty"
            data-l="Sobha City, Abu Dhabi"
            data-p="2340000"
            data-bmin="1"
            data-bmax="2"
            data-u="river-cove-residences-at-sobha-city"
            data-name="River Cove Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/river-cove-residences-at-sobha-city"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1199_river-cove-residences-at-sobha-city-img.jpg"
                alt="River Cove Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Sobha City, Abu Dhabi</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="river-cove-residences-at-sobha-city"
              aria-pressed="false"
              aria-label="Save River Cove Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Sobha Realty</span>
              <h3>
                <a href="https://drehomes.com/property/river-cove-residences-at-sobha-city">River Cove Residences</a>
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 2,340,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/river-cove-residences-at-sobha-city"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20River%20Cove%20Residences%20by%20Sobha%20Realty."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about River Cove Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Avenew Development"
            data-l="Dubai Islands"
            data-p="2930000"
            data-bmin="1"
            data-bmax="3"
            data-u="cheval-residences-at-dubai-islands"
            data-name="Cheval Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/cheval-residences-at-dubai-islands"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_6821_cheval-Residences-dubai-islands.jpg"
                alt="Cheval Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Islands</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="cheval-residences-at-dubai-islands"
              aria-pressed="false"
              aria-label="Save Cheval Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Avenew Development</span>
              <h3><a href="https://drehomes.com/property/cheval-residences-at-dubai-islands">Cheval Residences</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 2,930,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/cheval-residences-at-dubai-islands"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Cheval%20Residences%20by%20Avenew%20Development."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Cheval Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Mira Developments"
            data-l="Al Furjan"
            data-p="880000"
            data-bmin="0"
            data-bmax="2"
            data-u="richmond-district-al-furjan"
            data-name="Richmond District"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/richmond-district-al-furjan"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_2979_richmond-2.jpg"
                alt="Richmond District"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Al Furjan</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="richmond-district-al-furjan"
              aria-pressed="false"
              aria-label="Save Richmond District"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Mira Developments</span>
              <h3><a href="https://drehomes.com/property/richmond-district-al-furjan">Richmond District</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 880,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/richmond-district-al-furjan"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Richmond%20District%20by%20Mira%20Developments."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Richmond District on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Sharafi Development"
            data-l="Dubai Islands"
            data-p="2740000"
            data-bmin="1"
            data-bmax="3"
            data-u="marea-residences-at-dubai-islands"
            data-name="Marea Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/marea-residences-at-dubai-islands"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_5936_marea-residences-at-dubai-islands.jpg"
                alt="Marea Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Islands</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="marea-residences-at-dubai-islands"
              aria-pressed="false"
              aria-label="Save Marea Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Sharafi Development</span>
              <h3><a href="https://drehomes.com/property/marea-residences-at-dubai-islands">Marea Residences</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 2,740,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/marea-residences-at-dubai-islands"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Marea%20Residences%20by%20Sharafi%20Development."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Marea Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Emaar Properties"
            data-l="Expo Living, Dubai"
            data-p="1596888"
            data-bmin="1"
            data-bmax="3"
            data-u="terra-woods-at-expo-living"
            data-name="Terra Woods"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/terra-woods-at-expo-living"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_5623_terra-woods-at-expo-living-img1.jpg"
                alt="Terra Woods"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Expo Living, Dubai</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="terra-woods-at-expo-living"
              aria-pressed="false"
              aria-label="Save Terra Woods"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3><a href="https://drehomes.com/property/terra-woods-at-expo-living">Terra Woods</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,596,888</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/terra-woods-at-expo-living"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Terra%20Woods%20by%20Emaar%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Terra Woods on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Binghatti Developers"
            data-l="Majan, Dubai"
            data-p="699999"
            data-bmin="0"
            data-bmax="2"
            data-u="binghatti-skyflame-2"
            data-name="Binghatti Skyflame 2"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/binghatti-skyflame-2"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_4558_binghatti-skyflame-img1.jpg"
                alt="Binghatti Skyflame 2"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Majan, Dubai</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="binghatti-skyflame-2"
              aria-pressed="false"
              aria-label="Save Binghatti Skyflame 2"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Binghatti Developers</span>
              <h3><a href="https://drehomes.com/property/binghatti-skyflame-2">Binghatti Skyflame 2</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 699,999</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/binghatti-skyflame-2">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Binghatti%20Skyflame%202%20by%20Binghatti%20Developers."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Binghatti Skyflame 2 on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Samana Developers"
            data-l="Warsan, Dubai"
            data-p="849000"
            data-bmin="1"
            data-bmax="2"
            data-u="samana-greenfield"
            data-name="SAMANA Greenfield"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/samana-greenfield"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_5837_samana-greenfield-img1.jpg"
                alt="SAMANA Greenfield"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Warsan, Dubai</span
              ></a
            >
            <button class="oc-fav" data-u="samana-greenfield" aria-pressed="false" aria-label="Save SAMANA Greenfield">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Samana Developers</span>
              <h3><a href="https://drehomes.com/property/samana-greenfield">SAMANA Greenfield</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 849,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/samana-greenfield">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20SAMANA%20Greenfield%20by%20Samana%20Developers."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about SAMANA Greenfield on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Binghatti Developers"
            data-l="Al Jaddaf"
            data-p="819999"
            data-bmin="0"
            data-bmax="3"
            data-u="binghatti-cullinan"
            data-name="Binghatti Cullinan"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/binghatti-cullinan"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_2430_binghatti-cullinan-img5.jpg"
                alt="Binghatti Cullinan"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Al Jaddaf</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="binghatti-cullinan"
              aria-pressed="false"
              aria-label="Save Binghatti Cullinan"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Binghatti Developers</span>
              <h3><a href="https://drehomes.com/property/binghatti-cullinan">Binghatti Cullinan</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 819,999</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/binghatti-cullinan">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Binghatti%20Cullinan%20by%20Binghatti%20Developers."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Binghatti Cullinan on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Amwaj Development"
            data-l="Dubai Islands"
            data-p="1800000"
            data-bmin="1"
            data-bmax="4"
            data-u="kaia-residences-dubai-islands"
            data-name="Kaia Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/kaia-residences-dubai-islands"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1372_Kaia-dubai-islands-image.jpg"
                alt="Kaia Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Islands</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="kaia-residences-dubai-islands"
              aria-pressed="false"
              aria-label="Save Kaia Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Amwaj Development</span>
              <h3><a href="https://drehomes.com/property/kaia-residences-dubai-islands">Kaia Residences</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-4</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,800,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/kaia-residences-dubai-islands"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Kaia%20Residences%20by%20Amwaj%20Development."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Kaia Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Umm Al Quwain"
            data-d="Sobha Realty"
            data-l="Sobha Siniya Island"
            data-p="1400000"
            data-bmin="1"
            data-bmax="3"
            data-u="tranquil-beach-residences"
            data-name="Tranquil Beach Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/tranquil-beach-residences"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9804_tranquil-beach-residences-image.jpg"
                alt="Tranquil Beach Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Sobha Siniya Island</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="tranquil-beach-residences"
              aria-pressed="false"
              aria-label="Save Tranquil Beach Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Sobha Realty</span>
              <h3><a href="https://drehomes.com/property/tranquil-beach-residences">Tranquil Beach Residences</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,400,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/tranquil-beach-residences"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Tranquil%20Beach%20Residences%20by%20Sobha%20Realty."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Tranquil Beach Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Imtiaz Developments"
            data-l="Dubai South"
            data-p="1700000"
            data-bmin="0"
            data-bmax="2"
            data-u="enre-residence-by-imtiaz"
            data-name="Enre Residence"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/enre-residence-by-imtiaz"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1423_Enre-Residence-by-Imtiaz-img1.jpg"
                alt="Enre Residence"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai South</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="enre-residence-by-imtiaz"
              aria-pressed="false"
              aria-label="Save Enre Residence"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Imtiaz Developments</span>
              <h3><a href="https://drehomes.com/property/enre-residence-by-imtiaz">Enre Residence</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,700,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/enre-residence-by-imtiaz">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Enre%20Residence%20by%20Imtiaz%20Developments."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Enre Residence on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Sharjah"
            data-d="Aldar"
            data-l="Aljada, Sharjah"
            data-p="970000"
            data-bmin="1"
            data-bmax="4"
            data-u="The-gate-6-by-arada"
            data-name="The Gate 6"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/The-gate-6-by-arada"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_2090_the-gate-6-by-arada-img.jpg"
                alt="The Gate 6"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Aljada, Sharjah</span
              ></a
            >
            <button class="oc-fav" data-u="The-gate-6-by-arada" aria-pressed="false" aria-label="Save The Gate 6">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aldar</span>
              <h3><a href="https://drehomes.com/property/The-gate-6-by-arada">The Gate 6</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-4</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 970,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/The-gate-6-by-arada">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20The%20Gate%206%20by%20Aldar."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about The Gate 6 on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Danube Properties"
            data-l="Academic City, Dubai"
            data-p="3500000"
            data-bmin="3"
            data-bmax="5"
            data-u="greenz-by-danube"
            data-name="Greenz by Danube"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/greenz-by-danube"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9088_greenz-by-danube-img1.jpg"
                alt="Greenz by Danube"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Academic City, Dubai</span
              ></a
            >
            <button class="oc-fav" data-u="greenz-by-danube" aria-pressed="false" aria-label="Save Greenz by Danube">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Danube Properties</span>
              <h3><a href="https://drehomes.com/property/greenz-by-danube">Greenz by Danube</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>3 - 5</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 3,500,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/greenz-by-danube">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Greenz%20by%20Danube%20by%20Danube%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Greenz by Danube on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Emaar Properties"
            data-l="Emaar South"
            data-p="1099888"
            data-bmin="1"
            data-bmax="3"
            data-u="golf-vale-at-emaar-south"
            data-name="Golf Vale"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/golf-vale-at-emaar-south"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_6253_Golf_Vale_at_Emaar_South_img2.jpg"
                alt="Golf Vale"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Emaar South</span
              ></a
            >
            <button class="oc-fav" data-u="golf-vale-at-emaar-south" aria-pressed="false" aria-label="Save Golf Vale">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3><a href="https://drehomes.com/property/golf-vale-at-emaar-south">Golf Vale</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,099,888</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/golf-vale-at-emaar-south">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Golf%20Vale%20by%20Emaar%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Golf Vale on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="BNW Developments"
            data-l="Al Furjan, Dubai"
            data-p="1270000"
            data-bmin="1"
            data-bmax="3"
            data-u="orvessa-at-al-furjan"
            data-name="Orvessa Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/orvessa-at-al-furjan"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_465_Orvessa-Img2.jpg"
                alt="Orvessa Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Al Furjan, Dubai</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="orvessa-at-al-furjan"
              aria-pressed="false"
              aria-label="Save Orvessa Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By BNW Developments</span>
              <h3><a href="https://drehomes.com/property/orvessa-at-al-furjan">Orvessa Residences</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,270,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/orvessa-at-al-furjan">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Orvessa%20Residences%20by%20BNW%20Developments."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Orvessa Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Damac Properties"
            data-l="Damac Islands 2"
            data-p="3300000"
            data-bmin="4"
            data-bmax="5"
            data-u="antigua-damac-islands-2"
            data-name="Antigua"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/antigua-damac-islands-2"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9457_antigua-damac-islands-2-img1"
                alt="Antigua"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Damac Islands 2</span
              ></a
            >
            <button class="oc-fav" data-u="antigua-damac-islands-2" aria-pressed="false" aria-label="Save Antigua">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Damac Properties</span>
              <h3><a href="https://drehomes.com/property/antigua-damac-islands-2">Antigua</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>4-5</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 3,300,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/antigua-damac-islands-2">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Antigua%20by%20Damac%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Antigua on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Emaar Properties"
            data-l="Rashid Yachts &amp; Marina"
            data-p="2200000"
            data-bmin="1"
            data-bmax="3"
            data-u="fior-1-at-rashid-yachts-and-marina"
            data-name="Emaar Fior 1"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/fior-1-at-rashid-yachts-and-marina"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_3112_Fior-Img1.jpg"
                alt="Emaar Fior 1"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Rashid Yachts &amp; Marina</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="fior-1-at-rashid-yachts-and-marina"
              aria-pressed="false"
              aria-label="Save Emaar Fior 1"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3><a href="https://drehomes.com/property/fior-1-at-rashid-yachts-and-marina">Emaar Fior 1</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 2,200,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/fior-1-at-rashid-yachts-and-marina"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Emaar%20Fior%201%20by%20Emaar%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Emaar Fior 1 on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="BNW Developments"
            data-l="RAK Central"
            data-p="1120000"
            data-bmin="0"
            data-bmax="3"
            data-u="radisson-blu-residences-at-rak"
            data-name="Radisson Blu Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/radisson-blu-residences-at-rak"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_5672_Radisson-Blu-Residences-4.jpg"
                alt="Radisson Blu Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >RAK Central</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="radisson-blu-residences-at-rak"
              aria-pressed="false"
              aria-label="Save Radisson Blu Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By BNW Developments</span>
              <h3>
                <a href="https://drehomes.com/property/radisson-blu-residences-at-rak">Radisson Blu Residences</a>
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,120,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/radisson-blu-residences-at-rak"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Radisson%20Blu%20Residences%20by%20BNW%20Developments."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Radisson Blu Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Aldar"
            data-l="Fahid Island, Abu Dhabi"
            data-p="6300000"
            data-bmin="1"
            data-bmax="5"
            data-u="fahid-beach-terraces"
            data-name="Fahid Beach Terraces"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/fahid-beach-terraces"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_7422_fahid-beach-terraces"
                alt="Fahid Beach Terraces"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Fahid Island, Abu Dhabi</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="fahid-beach-terraces"
              aria-pressed="false"
              aria-label="Save Fahid Beach Terraces"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aldar</span>
              <h3><a href="https://drehomes.com/property/fahid-beach-terraces">Fahid Beach Terraces</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-5</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 6,300,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/fahid-beach-terraces">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Fahid%20Beach%20Terraces%20by%20Aldar."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Fahid Beach Terraces on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Aldar"
            data-l="Fahid Island, Abu Dhabi"
            data-p="7112057"
            data-bmin="0"
            data-bmax="3"
            data-u="fahid-beach-house"
            data-name="The Beach House Fahid"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/fahid-beach-house"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9820_fahid-beach-house-img1.jpg"
                alt="The Beach House Fahid"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Fahid Island, Abu Dhabi</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="fahid-beach-house"
              aria-pressed="false"
              aria-label="Save The Beach House Fahid"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aldar</span>
              <h3><a href="https://drehomes.com/property/fahid-beach-house">The Beach House Fahid</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 7,112,057</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/fahid-beach-house">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20The%20Beach%20House%20Fahid%20by%20Aldar."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about The Beach House Fahid on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Aldar"
            data-l="Saadiyat Cultural District"
            data-p="11281700"
            data-bmin="1"
            data-bmax="3"
            data-u="the-row-saadiyat"
            data-name="The Row Saadiyat"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/the-row-saadiyat"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_7017_the-row-saadiyat-img2.jpg"
                alt="The Row Saadiyat"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Saadiyat Cultural District</span
              ></a
            >
            <button class="oc-fav" data-u="the-row-saadiyat" aria-pressed="false" aria-label="Save The Row Saadiyat">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aldar</span>
              <h3><a href="https://drehomes.com/property/the-row-saadiyat">The Row Saadiyat</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 11,281,700</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/the-row-saadiyat">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20The%20Row%20Saadiyat%20by%20Aldar."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about The Row Saadiyat on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Aldar"
            data-l="Fahid Island"
            data-p="13272601"
            data-bmin="1"
            data-bmax="5"
            data-u="fahid-beach-residences"
            data-name="Fahid Beach Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/fahid-beach-residences"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_4058_fahid-beach-residences-img1"
                alt="Fahid Beach Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Fahid Island</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="fahid-beach-residences"
              aria-pressed="false"
              aria-label="Save Fahid Beach Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aldar</span>
              <h3><a href="https://drehomes.com/property/fahid-beach-residences">Fahid Beach Residences</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-5</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 13,272,601</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/fahid-beach-residences">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Fahid%20Beach%20Residences%20by%20Aldar."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Fahid Beach Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Ohana Development"
            data-l="Yas Island"
            data-p="3100000"
            data-bmin="0"
            data-bmax="5"
            data-u="manchester-city-yas-residences"
            data-name="Manchester City Yas Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/manchester-city-yas-residences"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1823_Manchester-City-Yas-Residences.jpg"
                alt="Manchester City Yas Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Yas Island</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="manchester-city-yas-residences"
              aria-pressed="false"
              aria-label="Save Manchester City Yas Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Ohana Development</span>
              <h3>
                <a href="https://drehomes.com/property/manchester-city-yas-residences"
                  >Manchester City Yas Residences</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 5</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 3,100,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/manchester-city-yas-residences"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Manchester%20City%20Yas%20Residences%20by%20Ohana%20Development."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Manchester City Yas Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Ras Al Khaimah"
            data-d="BEYOND"
            data-l="Marjan Island"
            data-p="2209000"
            data-bmin="1"
            data-bmax="3"
            data-u="le-chateau-by-beyond"
            data-name="Le Chateau"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/le-chateau-by-beyond"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_7700_Le-Chateau-Beyond-img.jpg"
                alt="Le Chateau"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Marjan Island</span
              ></a
            >
            <button class="oc-fav" data-u="le-chateau-by-beyond" aria-pressed="false" aria-label="Save Le Chateau">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By BEYOND</span>
              <h3><a href="https://drehomes.com/property/le-chateau-by-beyond">Le Chateau</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 2,209,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/le-chateau-by-beyond">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Le%20Chateau%20by%20BEYOND."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Le Chateau on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Aldar"
            data-l="Dubai"
            data-p="2672359"
            data-bmin="1"
            data-bmax="3"
            data-u="the-wilds-residences"
            data-name="The Wilds Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/the-wilds-residences"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1410_The-Wilds-dubai.jpg"
                alt="The Wilds Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="the-wilds-residences"
              aria-pressed="false"
              aria-label="Save The Wilds Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aldar</span>
              <h3><a href="https://drehomes.com/property/the-wilds-residences">The Wilds Residences</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 2,672,359</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/the-wilds-residences">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20The%20Wilds%20Residences%20by%20Aldar."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about The Wilds Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Imtiaz Developments"
            data-l="Dubai Islands"
            data-p="2100000"
            data-bmin="1"
            data-bmax="4"
            data-u="seacliff-by-imtiaz"
            data-name="Seacliff"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/seacliff-by-imtiaz"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1552_Sea-Cliff-Image.jpg"
                alt="Seacliff"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Islands</span
              ></a
            >
            <button class="oc-fav" data-u="seacliff-by-imtiaz" aria-pressed="false" aria-label="Save Seacliff">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Imtiaz Developments</span>
              <h3><a href="https://drehomes.com/property/seacliff-by-imtiaz">Seacliff</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-4</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 2,100,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/seacliff-by-imtiaz">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Seacliff%20by%20Imtiaz%20Developments."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Seacliff on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Nshama"
            data-l="Town Square Dubai"
            data-p="0"
            data-bmin="1"
            data-bmax="3"
            data-u="belmont-townsquare-dubai"
            data-name="Belmont"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/belmont-townsquare-dubai"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9648_BELMONT-townsquare-dubai-img2.jpg"
                alt="Belmont"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Town Square Dubai</span
              ></a
            >
            <button class="oc-fav" data-u="belmont-townsquare-dubai" aria-pressed="false" aria-label="Save Belmont">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Nshama</span>
              <h3><a href="https://drehomes.com/property/belmont-townsquare-dubai">Belmont</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-3</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/belmont-townsquare-dubai">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Belmont%20by%20Nshama."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Belmont on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Aqua Properties"
            data-l="Arjan, Dubai"
            data-p="720888"
            data-bmin="0"
            data-bmax="3"
            data-u="the-central-uptown-at-arjan"
            data-name="The Central Uptown"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/the-central-uptown-at-arjan"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9090_The-Central-Uptown-Img1.jpg"
                alt="The Central Uptown"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Arjan, Dubai</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="the-central-uptown-at-arjan"
              aria-pressed="false"
              aria-label="Save The Central Uptown"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aqua Properties</span>
              <h3><a href="https://drehomes.com/property/the-central-uptown-at-arjan">The Central Uptown</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 720,888</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/the-central-uptown-at-arjan"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20The%20Central%20Uptown%20by%20Aqua%20Properties."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about The Central Uptown on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Abu Dhabi"
            data-d="Aldar"
            data-l="Saadiyat"
            data-p="0"
            data-bmin="2"
            data-bmax="4"
            data-u="baccarat-residences-saadiyat"
            data-name="Baccarat Residences Saadiyat"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/baccarat-residences-saadiyat"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_4071_Baccarat-Residences-Saadiyat-img1.jpg"
                alt="Baccarat Residences Saadiyat"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Saadiyat</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="baccarat-residences-saadiyat"
              aria-pressed="false"
              aria-label="Save Baccarat Residences Saadiyat"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aldar</span>
              <h3>
                <a href="https://drehomes.com/property/baccarat-residences-saadiyat">Baccarat Residences Saadiyat</a>
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>2-4</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Price on request</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/baccarat-residences-saadiyat"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Baccarat%20Residences%20Saadiyat%20by%20Aldar."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Baccarat Residences Saadiyat on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Binghatti Developers"
            data-l="Majan, Dubai"
            data-p="585000"
            data-bmin="0"
            data-bmax="2"
            data-u="binghatti-skyflame"
            data-name="Binghatti Skyflame"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/binghatti-skyflame"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_2423_binghatti-skyflame-image1.jpg"
                alt="Binghatti Skyflame"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Majan, Dubai</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="binghatti-skyflame"
              aria-pressed="false"
              aria-label="Save Binghatti Skyflame"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Binghatti Developers</span>
              <h3><a href="https://drehomes.com/property/binghatti-skyflame">Binghatti Skyflame</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 585,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/binghatti-skyflame">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Binghatti%20Skyflame%20by%20Binghatti%20Developers."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Binghatti Skyflame on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Binghatti Developers"
            data-l="Dubai Motor City"
            data-p="777999"
            data-bmin="0"
            data-bmax="3"
            data-u="binghatti-skyterraces"
            data-name="Binghatti SkyTerraces"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/binghatti-skyterraces"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_3661_binghatti-skyterraces-dubai.jpg"
                alt="Binghatti SkyTerraces"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Motor City</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="binghatti-skyterraces"
              aria-pressed="false"
              aria-label="Save Binghatti SkyTerraces"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Binghatti Developers</span>
              <h3><a href="https://drehomes.com/property/binghatti-skyterraces">Binghatti SkyTerraces</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 777,999</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/binghatti-skyterraces">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Binghatti%20SkyTerraces%20by%20Binghatti%20Developers."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Binghatti SkyTerraces on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Binghatti Developers"
            data-l="Jumeirah Village Circle"
            data-p="764999"
            data-bmin="0"
            data-bmax="2"
            data-u="binghatti-etherea"
            data-name="Binghatti Etherea"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/binghatti-etherea"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_4237_binghatti-etheria-jvc.jpg"
                alt="Binghatti Etherea"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Jumeirah Village Circle</span
              ></a
            >
            <button class="oc-fav" data-u="binghatti-etherea" aria-pressed="false" aria-label="Save Binghatti Etherea">
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Binghatti Developers</span>
              <h3><a href="https://drehomes.com/property/binghatti-etherea">Binghatti Etherea</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 764,999</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/binghatti-etherea">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Binghatti%20Etherea%20by%20Binghatti%20Developers."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Binghatti Etherea on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Umm Al Quwain"
            data-d="Sobha Realty"
            data-l="Sobha Siniya Island"
            data-p="1980000"
            data-bmin="1"
            data-bmax="3"
            data-u="capeside-marina-residences"
            data-name="Capeside Marina Residences"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/capeside-marina-residences"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_4882_capeside-marina-residences-img1.jpg"
                alt="Capeside Marina Residences"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Sobha Siniya Island</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="capeside-marina-residences"
              aria-pressed="false"
              aria-label="Save Capeside Marina Residences"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Sobha Realty</span>
              <h3><a href="https://drehomes.com/property/capeside-marina-residences">Capeside Marina Residences</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1-3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,980,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/capeside-marina-residences"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Capeside%20Marina%20Residences%20by%20Sobha%20Realty."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Capeside Marina Residences on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
          <article
            class="oc"
            data-e="Dubai"
            data-d="Ohana Development"
            data-l="Al Jurf"
            data-p="1500000"
            data-bmin="0"
            data-bmax="7"
            data-u="jacob-co-beachfront-living"
            data-name="Jacob &amp; Co. Beachfront Living"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/property/jacob-co-beachfront-living"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1835_jacob-co-beachfront-living.jpg"
                alt="Jacob &amp; Co. Beachfront Living"
                loading="lazy"
              /><span class="badge">Off plan</span
              ><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Al Jurf</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="jacob-co-beachfront-living"
              aria-pressed="false"
              aria-label="Save Jacob &amp; Co. Beachfront Living"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Ohana Development</span>
              <h3>
                <a href="https://drehomes.com/property/jacob-co-beachfront-living">Jacob &amp; Co. Beachfront Living</a>
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1 - 7</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,500,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/property/jacob-co-beachfront-living"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I'm%20interested%20in%20Jacob%20%26%20Co.%20Beachfront%20Living%20by%20Ohana%20Development."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Jacob &amp; Co. Beachfront Living on WhatsApp"
                  ><svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                      d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                    ></path></svg
                ></a>
              </div>
            </div>
          </article>
        </div>
        <div class="op-empty glass" id="opEmpty" hidden="">
          <h3>No projects match these filters.</h3>
          <p>Try widening your price range — or let our off-plan specialists find it for you.</p>
          <div class="story-cta" style="justify-content: center">
            <button class="btn btn-glass" id="emptyClear">Clear filters</button
            ><a class="btn btn-gold" href="#contact">Ask a specialist →</a>
          </div>
        </div>
        <div class="pager" id="pager" role="navigation" aria-label="Pagination"></div>
        
      </div>
    </section>

    <!-- ===== WHY OFF-PLAN WITH DRE ===== -->
    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="op-cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Can’t decide?</span>
            <h2 class="serif">Get a curated <em class="gold-text">shortlist</em> in 24 hours.</h2>
            <p class="lead">
              Tell us your budget, preferred area and goals — our off-plan specialists will compare payment plans,
              handover dates and Golden Visa eligibility for you.
            </p>
          </div>
          <div class="op-cta-b">
            <a class="btn btn-gold" href="#contact">Get my shortlist →</a>
            <a class="btn btn-glass" href="index.php#visa">Golden Visa check</a>
          </div>
        </div>
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