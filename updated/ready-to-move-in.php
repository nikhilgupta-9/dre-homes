<?php include('common/header.php'); ?>


    <!-- ===== OFF-PLAN: HERO ===== -->
    <header class="ph-hero op-hero">
      <div
        class="ph-bg"
        style="
          background-image: url(https://drehomes.com/admin_nsrdwsc/assets/media/project/readytomovein_cont_img_843_ready-to-move-in-properties-dubai-DRE.jpg);
        "
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="index.php">Home</a><span>/</span><a href="#">Properties</a><span>/</span
          ><span aria-current="page">Ready To Move In</span>
        </div>
        <span class="eyebrow">Ready To Move In · Dubai</span>
        <h1 class="serif">Completed homes. <em class="gold-text">Keys on transfer.</em></h1>
        <p class="sub">
          Ready-to-move-in apartments, villas and townhouses from Dubai’s leading developers — no waiting for handover.
        </p>
        <form class="op-search glass" id="opSearch" role="search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="M20 20l-3.5-3.5"></path>
          </svg>
          <input
            id="qIn"
            type="search"
            placeholder="Search a project, developer or area — e.g. Emaar, JVC…"
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
            <div class="seg" id="emSeg" role="tablist" aria-label="Property type" data-key="ty">
              <button role="tab" data-e="" class="on">All types</button
              ><button role="tab" data-e="Apartment">Apartments</button><button role="tab" data-e="Villa">Villas</button
              ><button role="tab" data-e="Townhouse">Townhouses</button>
            </div>
            <label class="fsel"
              ><span>Developer</span
              ><select id="devSel">
                <option value="">All developers</option>
                <option>Emaar Properties</option>
                <option>Binghatti Developers</option>
                <option>ABA Group</option>
                <option>AMA Holding</option>
                <option>Danube Properties</option>
                <option>Bloom Holding</option>
                <option>Damac Properties</option>
                <option>Liv Developers</option>
                <option>Aqua Properties</option>
                <option>Nshama</option>
                <option>Dubai Properties</option>
              </select></label
            >
            <label class="fsel"
              ><span>Area</span
              ><select id="locSel">
                <option value="">All areas</option>
                <option>Arabian Ranches</option>
                <option>Arabian Ranches II</option>
                <option>Business Bay</option>
                <option>Dubai</option>
                <option>Dubai Healthcare City</option>
                <option>Dubai Hills Estate</option>
                <option>Dubai Marina</option>
                <option>Dubai Opera District</option>
                <option>Emaar South</option>
                <option>Jumeirah Village Circle</option>
                <option>Majan</option>
                <option>Sheikh Zayed Road</option>
                <option>Town Square</option>
                <option>Villanova</option>
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
              ><button type="button" data-b="2">2</button><button type="button" data-b="3">3</button
              ><button type="button" data-b="4">4</button><button type="button" data-b="5">5</button
              ><button type="button" data-b="6">6</button><button type="button" data-b="7">7</button>
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

        <div class="opg" id="opGrid" aria-live="polite" data-fav-key="dre-favs-ready">
          <article
            class="oc"
            data-d="ABA Group"
            data-l="Majan"
            data-p="0"
            data-u="paradise-view-2-majan"
            data-name="Paradise View 2 at Majan, Dubai"
            data-ty="Apartment"
            data-bmin="0"
            data-bmax="1"
            data-blist="0 1"
          >
            <a class="oc-img" href="ready-to-move-in-single.php"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_4678_Paradise-Views-2-Img.jpg"
                alt="Paradise View 2 at Majan, Dubai"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Majan</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="paradise-view-2-majan"
              aria-pressed="false"
              aria-label="Save Paradise View 2 at Majan, Dubai"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By ABA Group</span>
              <h3><a href="ready-to-move-in-single.php">Paradise View 2 at Majan, Dubai</a></h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Call us</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="ready-to-move-in-single.php">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Paradise%20View%202%20at%20Majan%2C%20Dubai%20by%20ABA%20Group%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Paradise View 2 at Majan, Dubai on WhatsApp"
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
            data-d="AMA Holding"
            data-l="Sheikh Zayed Road"
            data-p="2880000"
            data-u="aa-tower-dubai"
            data-name="AA Tower by AMA Holding | Ready To Move in Dubai"
            data-ty="Apartment"
            data-bmin="1"
            data-bmax="2"
            data-blist="1 2"
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/aa-tower-dubai"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_8808_Apartments-Dubai-img2.jpg"
                alt="AA Tower by AMA Holding | Ready To Move in Dubai"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Sheikh Zayed Road</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="aa-tower-dubai"
              aria-pressed="false"
              aria-label="Save AA Tower by AMA Holding | Ready To Move in Dubai"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By AMA Holding</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/aa-tower-dubai"
                  >AA Tower by AMA Holding | Ready To Move in Dubai</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1, 2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 2,880,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/aa-tower-dubai">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20AA%20Tower%20by%20AMA%20Holding%20%7C%20Ready%20To%20Move%20in%20Dubai%20by%20AMA%20Holding%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about AA Tower by AMA Holding | Ready To Move in Dubai on WhatsApp"
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
            data-d="Binghatti Developers"
            data-l="Jumeirah Village Circle"
            data-p="700000"
            data-u="binghatti-phoenix-jvc"
            data-name="Binghatti Phoenix at Jumeirah Village Circle, Dubai"
            data-ty="Apartment"
            data-bmin="0"
            data-bmax="2"
            data-blist="0 1 2"
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/binghatti-phoenix-jvc"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_6692_binghatti-phoenix-jvc-dubai.jpg"
                alt="Binghatti Phoenix at Jumeirah Village Circle, Dubai"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Jumeirah Village Circle</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="binghatti-phoenix-jvc"
              aria-pressed="false"
              aria-label="Save Binghatti Phoenix at Jumeirah Village Circle, Dubai"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Binghatti Developers</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/binghatti-phoenix-jvc"
                  >Binghatti Phoenix at Jumeirah Village Circle, Dubai</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1, 2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 700,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/binghatti-phoenix-jvc"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Binghatti%20Phoenix%20at%20Jumeirah%20Village%20Circle%2C%20Dubai%20by%20Binghatti%20Developers%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Binghatti Phoenix at Jumeirah Village Circle, Dubai on WhatsApp"
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
            data-d="Binghatti Developers"
            data-l="Jumeirah Village Circle"
            data-p="0"
            data-u="binghatti-tulip-jvc"
            data-name="Binghatti Tulip at Jumeirah Village Circle, Dubai"
            data-ty="Apartment"
            data-bmin="0"
            data-bmax="3"
            data-blist="0 1 2 3"
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/binghatti-tulip-jvc"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9409_binghatti-tulip-jvc.jpg"
                alt="Binghatti Tulip at Jumeirah Village Circle, Dubai"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Jumeirah Village Circle</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="binghatti-tulip-jvc"
              aria-pressed="false"
              aria-label="Save Binghatti Tulip at Jumeirah Village Circle, Dubai"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Binghatti Developers</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/binghatti-tulip-jvc"
                  >Binghatti Tulip at Jumeirah Village Circle, Dubai</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1, 2, 3</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Call us</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/binghatti-tulip-jvc"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Binghatti%20Tulip%20at%20Jumeirah%20Village%20Circle%2C%20Dubai%20by%20Binghatti%20Developers%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Binghatti Tulip at Jumeirah Village Circle, Dubai on WhatsApp"
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
            data-d="Danube Properties"
            data-l="Jumeirah Village Circle"
            data-p="1230000"
            data-u="eleganz-by-danube"
            data-name="Eleganz by Danube in Jumeirah Village Circle, Dubai"
            data-ty="Apartment"
            data-bmin="0"
            data-bmax="4"
            data-blist="0 1 2 4"
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/eleganz-by-danube"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9875_Danube-Eleganz-JVC-Dubai.jpg"
                alt="Eleganz by Danube in Jumeirah Village Circle, Dubai"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Jumeirah Village Circle</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="eleganz-by-danube"
              aria-pressed="false"
              aria-label="Save Eleganz by Danube in Jumeirah Village Circle, Dubai"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Danube Properties</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/eleganz-by-danube"
                  >Eleganz by Danube in Jumeirah Village Circle, Dubai</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1, 2, 4</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,230,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/eleganz-by-danube"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Eleganz%20by%20Danube%20in%20Jumeirah%20Village%20Circle%2C%20Dubai%20by%20Danube%20Properties%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Eleganz by Danube in Jumeirah Village Circle, Dubai on WhatsApp"
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
            data-d="Bloom Holding"
            data-l="Jumeirah Village Circle"
            data-p="0"
            data-u="bloom-towers-jvc"
            data-name="Bloom Towers at Jumeirah Village Circle"
            data-ty="Apartment"
            data-bmin="0"
            data-bmax="3"
            data-blist="0 1 2 3"
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/bloom-towers-jvc"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_4325_bloom-towers-facade-1_2_11zon.jpg"
                alt="Bloom Towers at Jumeirah Village Circle"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Jumeirah Village Circle</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="bloom-towers-jvc"
              aria-pressed="false"
              aria-label="Save Bloom Towers at Jumeirah Village Circle"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Bloom Holding</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/bloom-towers-jvc"
                  >Bloom Towers at Jumeirah Village Circle</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1, 2, 3</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Call us</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/bloom-towers-jvc">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Bloom%20Towers%20at%20Jumeirah%20Village%20Circle%20by%20Bloom%20Holding%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Bloom Towers at Jumeirah Village Circle on WhatsApp"
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
            data-d="Binghatti Developers"
            data-l="Business Bay"
            data-p="795000"
            data-u="binghatti-canal-apartments"
            data-name="Binghatti Canal Apartments at Business Bay"
            data-ty="Apartment"
            data-bmin="0"
            data-bmax="2"
            data-blist="0 1 2"
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/binghatti-canal-apartments"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_1558_binghatti-canal-business-bay-facade-min.jpg"
                alt="Binghatti Canal Apartments at Business Bay"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Business Bay</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="binghatti-canal-apartments"
              aria-pressed="false"
              aria-label="Save Binghatti Canal Apartments at Business Bay"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Binghatti Developers</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/binghatti-canal-apartments"
                  >Binghatti Canal Apartments at Business Bay</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1, 2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 795,000</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/binghatti-canal-apartments"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Binghatti%20Canal%20Apartments%20at%20Business%20Bay%20by%20Binghatti%20Developers%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Binghatti Canal Apartments at Business Bay on WhatsApp"
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
            data-d="Emaar Properties"
            data-l="Arabian Ranches"
            data-p="0"
            data-u="emaar-lila-villas"
            data-name="Emaar Lila villas at Arabian Ranches"
            data-ty="Villa"
            data-bmin="3"
            data-bmax="5"
            data-blist="3 4 5"
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/emaar-lila-villas"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_160_img.jpg"
                alt="Emaar Lila villas at Arabian Ranches"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Arabian Ranches</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="emaar-lila-villas"
              aria-pressed="false"
              aria-label="Save Emaar Lila villas at Arabian Ranches"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/emaar-lila-villas"
                  >Emaar Lila villas at Arabian Ranches</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>3, 4, 5</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Call us</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/emaar-lila-villas"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Emaar%20Lila%20villas%20at%20Arabian%20Ranches%20by%20Emaar%20Properties%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Emaar Lila villas at Arabian Ranches on WhatsApp"
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
            data-d="Emaar Properties"
            data-l="Arabian Ranches"
            data-p="0"
            data-u="emaar-aseel-villas-arabian-ranches"
            data-name="Aseel Villas by Emaar – Arabian Ranches, Dubai"
            data-ty="Villa"
            data-bmin="4"
            data-bmax="7"
            data-blist="4 5 6 7"
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/emaar-aseel-villas-arabian-ranches"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_6828_ASEEL_3.jpg"
                alt="Aseel Villas by Emaar – Arabian Ranches, Dubai"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Arabian Ranches</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="emaar-aseel-villas-arabian-ranches"
              aria-pressed="false"
              aria-label="Save Aseel Villas by Emaar – Arabian Ranches, Dubai"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/emaar-aseel-villas-arabian-ranches"
                  >Aseel Villas by Emaar – Arabian Ranches, Dubai</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>4, 5, 6, 7</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Call us</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/emaar-aseel-villas-arabian-ranches"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Aseel%20Villas%20by%20Emaar%20%E2%80%93%20Arabian%20Ranches%2C%20Dubai%20by%20Emaar%20Properties%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Aseel Villas by Emaar – Arabian Ranches, Dubai on WhatsApp"
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
            data-d="Emaar Properties"
            data-l="Dubai Hills Estate"
            data-p="0"
            data-u="mulberry-apartment-by-emaar"
            data-name="Mulberry Apartments by Emaar at Dubai Hills Estate"
            data-ty="Apartment"
            data-bmin="1"
            data-bmax="3"
            data-blist="1 2 3"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/mulberry-apartment-by-emaar"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_9614_Mulberry-Apartments-by-Emaar-at-Dubai-Hills-Estate.jpg"
                alt="Mulberry Apartments by Emaar at Dubai Hills Estate"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Hills Estate</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="mulberry-apartment-by-emaar"
              aria-pressed="false"
              aria-label="Save Mulberry Apartments by Emaar at Dubai Hills Estate"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/mulberry-apartment-by-emaar"
                  >Mulberry Apartments by Emaar at Dubai Hills Estate</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1, 2, 3</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Call us</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/mulberry-apartment-by-emaar"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Mulberry%20Apartments%20by%20Emaar%20at%20Dubai%20Hills%20Estate%20by%20Emaar%20Properties%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Mulberry Apartments by Emaar at Dubai Hills Estate on WhatsApp"
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
            data-d="Emaar Properties"
            data-l="Arabian Ranches II"
            data-p="1454888"
            data-u="emaar-camelia-townhouses"
            data-name="Camelia Townhouses Phase 3 by Emaar in Arabian Ranches II Dubai"
            data-ty="Townhouse"
            data-bmin="3"
            data-bmax="4"
            data-blist="3 4"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/emaar-camelia-townhouses"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_794_g-2.jpg"
                alt="Camelia Townhouses Phase 3 by Emaar in Arabian Ranches II Dubai"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Arabian Ranches II</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="emaar-camelia-townhouses"
              aria-pressed="false"
              aria-label="Save Camelia Townhouses Phase 3 by Emaar in Arabian Ranches II Dubai"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/emaar-camelia-townhouses"
                  >Camelia Townhouses Phase 3 by Emaar in Arabian Ranches II Dubai</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>3, 4</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,454,888</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/emaar-camelia-townhouses"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Camelia%20Townhouses%20Phase%203%20by%20Emaar%20in%20Arabian%20Ranches%20II%20Dubai%20by%20Emaar%20Properties%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Camelia Townhouses Phase 3 by Emaar in Arabian Ranches II Dubai on WhatsApp"
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
            data-d="Damac Properties"
            data-l="Dubai"
            data-p="774000"
            data-u="damac-paramount-tower-hotel-residences-dubai"
            data-name="DAMAC Paramount Tower Hotel &amp; Residences Dubai"
            data-ty="Apartment"
            data-bmin="1"
            data-bmax="2"
            data-blist="1 2"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/damac-paramount-tower-hotel-residences-dubai"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_7402_damac-paramount-tower-hotel-residences-dubai.jpg"
                alt="DAMAC Paramount Tower Hotel &amp; Residences Dubai"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="damac-paramount-tower-hotel-residences-dubai"
              aria-pressed="false"
              aria-label="Save DAMAC Paramount Tower Hotel &amp; Residences Dubai"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Damac Properties</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/damac-paramount-tower-hotel-residences-dubai"
                  >DAMAC Paramount Tower Hotel &amp; Residences Dubai</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1, 2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 774,000</b></div>
              </div>
              <div class="oc-act">
                <a
                  class="btn btn-glass"
                  href="https://drehomes.com/ready-to-move-in/damac-paramount-tower-hotel-residences-dubai"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20DAMAC%20Paramount%20Tower%20Hotel%20%26%20Residences%20Dubai%20by%20Damac%20Properties%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about DAMAC Paramount Tower Hotel &amp; Residences Dubai on WhatsApp"
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
            data-d="Emaar Properties"
            data-l="Dubai Opera District"
            data-p="0"
            data-u="address-residences-by-emaar"
            data-name="The Address Residences by Emaar at Dubai Opera District"
            data-ty="Apartment"
            data-bmin="1"
            data-bmax="3"
            data-blist="1 2 3"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/address-residences-by-emaar"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_8127_img-1.jpg"
                alt="The Address Residences by Emaar at Dubai Opera District"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Opera District</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="address-residences-by-emaar"
              aria-pressed="false"
              aria-label="Save The Address Residences by Emaar at Dubai Opera District"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/address-residences-by-emaar"
                  >The Address Residences by Emaar at Dubai Opera District</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1, 2, 3</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Call us</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/address-residences-by-emaar"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20The%20Address%20Residences%20by%20Emaar%20at%20Dubai%20Opera%20District%20by%20Emaar%20Properties%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about The Address Residences by Emaar at Dubai Opera District on WhatsApp"
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
            data-d="Liv Developers"
            data-l="Dubai Marina"
            data-p="0"
            data-u="liv-dubai-marina"
            data-name="LIV Dubai Marina Residential Tower, Dubai"
            data-ty="Apartment"
            data-bmin="0"
            data-bmax="3"
            data-blist="0 1 2 3"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/liv-dubai-marina"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_8644_liv-marina-dubai-1.jpg"
                alt="LIV Dubai Marina Residential Tower, Dubai"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Marina</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="liv-dubai-marina"
              aria-pressed="false"
              aria-label="Save LIV Dubai Marina Residential Tower, Dubai"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Liv Developers</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/liv-dubai-marina"
                  >LIV Dubai Marina Residential Tower, Dubai</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1, 2, 3</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Call us</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/liv-dubai-marina">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20LIV%20Dubai%20Marina%20Residential%20Tower%2C%20Dubai%20by%20Liv%20Developers%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about LIV Dubai Marina Residential Tower, Dubai on WhatsApp"
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
            data-d="Aqua Properties"
            data-l="Dubai Healthcare City"
            data-p="766888"
            data-u="oten-aqua-properties"
            data-name="O Ten by Aqua Properties at Dubai Healthcare City"
            data-ty="Apartment"
            data-bmin="0"
            data-bmax="2"
            data-blist="0 1 2"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/oten-aqua-properties"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_2862_oten-dubai-healthcare-city-facade-min.jpg"
                alt="O Ten by Aqua Properties at Dubai Healthcare City"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Healthcare City</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="oten-aqua-properties"
              aria-pressed="false"
              aria-label="Save O Ten by Aqua Properties at Dubai Healthcare City"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Aqua Properties</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/oten-aqua-properties"
                  >O Ten by Aqua Properties at Dubai Healthcare City</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>Studio, 1, 2</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 766,888</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/oten-aqua-properties"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20O%20Ten%20by%20Aqua%20Properties%20at%20Dubai%20Healthcare%20City%20by%20Aqua%20Properties%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about O Ten by Aqua Properties at Dubai Healthcare City on WhatsApp"
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
            data-d="Emaar Properties"
            data-l="Dubai Opera District"
            data-p="1585888"
            data-u="emaar-act-one-and-two-apartments"
            data-name="Act One, Act Two by Emaar at Opera District Dubai"
            data-ty="Apartment"
            data-bmin="1"
            data-bmax="3"
            data-blist="1 2 3"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/emaar-act-one-and-two-apartments"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_4860_img-1.jpg"
                alt="Act One, Act Two by Emaar at Opera District Dubai"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Opera District</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="emaar-act-one-and-two-apartments"
              aria-pressed="false"
              aria-label="Save Act One, Act Two by Emaar at Opera District Dubai"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/emaar-act-one-and-two-apartments"
                  >Act One, Act Two by Emaar at Opera District Dubai</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>1, 2, 3</b></div>
                <div class="pr" style="text-align: right"><small>Starting price</small><b>AED 1,585,888</b></div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/emaar-act-one-and-two-apartments"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Act%20One%2C%20Act%20Two%20by%20Emaar%20at%20Opera%20District%20Dubai%20by%20Emaar%20Properties%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Act One, Act Two by Emaar at Opera District Dubai on WhatsApp"
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
            data-d="Emaar Properties"
            data-l="Dubai Hills Estate"
            data-p="0"
            data-u="emaar-maple-townhouses"
            data-name="Emaar Maple Townhouses at Dubai Hills Estate"
            data-ty="Townhouse"
            data-bmin="3"
            data-bmax="5"
            data-blist="3 4 5"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/emaar-maple-townhouses"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_5279_Maple_4.jpg"
                alt="Emaar Maple Townhouses at Dubai Hills Estate"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Dubai Hills Estate</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="emaar-maple-townhouses"
              aria-pressed="false"
              aria-label="Save Emaar Maple Townhouses at Dubai Hills Estate"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/emaar-maple-townhouses"
                  >Emaar Maple Townhouses at Dubai Hills Estate</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>3, 4, 5</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Call us</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/emaar-maple-townhouses"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Emaar%20Maple%20Townhouses%20at%20Dubai%20Hills%20Estate%20by%20Emaar%20Properties%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Emaar Maple Townhouses at Dubai Hills Estate on WhatsApp"
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
            data-d="Emaar Properties"
            data-l="Emaar South"
            data-p="0"
            data-u="emaar-expo-golf-villas"
            data-name="Expo Golf Villas Phase 5 at Emaar South"
            data-ty="Villa"
            data-bmin="3"
            data-bmax="4"
            data-blist="3 4"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/emaar-expo-golf-villas"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_8533_img-1.jpg"
                alt="Expo Golf Villas Phase 5 at Emaar South"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Emaar South</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="emaar-expo-golf-villas"
              aria-pressed="false"
              aria-label="Save Expo Golf Villas Phase 5 at Emaar South"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Emaar Properties</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/emaar-expo-golf-villas"
                  >Expo Golf Villas Phase 5 at Emaar South</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>3, 4</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Call us</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/emaar-expo-golf-villas"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Expo%20Golf%20Villas%20Phase%205%20at%20Emaar%20South%20by%20Emaar%20Properties%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Expo Golf Villas Phase 5 at Emaar South on WhatsApp"
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
            data-d="Nshama"
            data-l="Town Square"
            data-p="0"
            data-u="sama-townhouses"
            data-name="Sama Townhouses by Nshama Town Square Dubai"
            data-ty="Townhouse"
            data-bmin="3"
            data-bmax="4"
            data-blist="3 4"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/sama-townhouses"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_26_img-4.jpg"
                alt="Sama Townhouses by Nshama Town Square Dubai"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Town Square</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="sama-townhouses"
              aria-pressed="false"
              aria-label="Save Sama Townhouses by Nshama Town Square Dubai"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Nshama</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/sama-townhouses"
                  >Sama Townhouses by Nshama Town Square Dubai</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>3, 4</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Call us</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/sama-townhouses">View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Sama%20Townhouses%20by%20Nshama%20Town%20Square%20Dubai%20by%20Nshama%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Sama Townhouses by Nshama Town Square Dubai on WhatsApp"
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
            data-d="Dubai Properties"
            data-l="Villanova"
            data-p="0"
            data-u="amaranta-villanova-townhouses"
            data-name="Amaranta Townhouses at Villanova Dubai"
            data-ty="Townhouse"
            hidden=""
          >
            <a class="oc-img" href="https://drehomes.com/ready-to-move-in/amaranta-villanova-townhouses"
              ><img
                src="https://drehomes.com/admin_nsrdwsc/assets/media/project/thumb/thumb_img_5257_img-4-min.jpg"
                alt="Amaranta Townhouses at Villanova Dubai"
                loading="lazy"
              /><span class="oc-loc"
                ><svg viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"
                  ></path></svg
                >Villanova</span
              ></a
            >
            <button
              class="oc-fav"
              data-u="amaranta-villanova-townhouses"
              aria-pressed="false"
              aria-label="Save Amaranta Townhouses at Villanova Dubai"
            >
              ♡
            </button>
            <div class="oc-b">
              <span class="oc-dev">By Dubai Properties</span>
              <h3>
                <a href="https://drehomes.com/ready-to-move-in/amaranta-villanova-townhouses"
                  >Amaranta Townhouses at Villanova Dubai</a
                >
              </h3>
              <div class="oc-meta">
                <div><small>Bedrooms</small><b>—</b></div>
                <div class="pr" style="text-align: right">
                  <small>Starting price</small><b><em>Call us</em></b>
                </div>
              </div>
              <div class="oc-act">
                <a class="btn btn-glass" href="https://drehomes.com/ready-to-move-in/amaranta-villanova-townhouses"
                  >View details →</a
                ><a
                  class="oc-wa"
                  href="https://wa.me/971585837346?text=Hi%20DRE%2C%20I%27m%20interested%20in%20Amaranta%20Townhouses%20at%20Villanova%20Dubai%20by%20Dubai%20Properties%20%28ready%20to%20move%20in%29."
                  target="_blank"
                  rel="noopener"
                  aria-label="Enquire about Amaranta Townhouses at Villanova Dubai on WhatsApp"
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
          <h3>No ready homes match these filters.</h3>
          <p>Try widening your price range — or let our off-plan specialists find it for you.</p>
          <div class="story-cta" style="justify-content: center">
            <button class="btn btn-glass" id="emptyClear">Clear filters</button
            ><a class="btn btn-gold" href="#contact">Ask a specialist →</a>
          </div>
        </div>
        <div class="pager" id="pager" role="navigation" aria-label="Pagination"></div>
        <p class="op-note">
          Showing the latest <b>72</b> launches in this concept ·
          <a href="https://drehomes.com/offplan-properties" target="_blank" rel="noopener"
            >browse all 1,052 on drehomes.com ↗</a
          >
        </p>
      </div>
    </section>

    <!-- ===== WHY OFF-PLAN WITH DRE ===== -->
    <section style="padding-top: 20px">
      <div class="wrap">
        <div class="op-cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Want to move in sooner?</span>
            <h2 class="serif">Get a <em class="gold-text">shortlist</em> of ready homes in 24 hours.</h2>
            <p class="lead">
              Tell us your budget, preferred area and move-in date — our advisors will match you with completed homes
              that fit.
            </p>
          </div>
          <div class="op-cta-b">
            <a class="btn btn-gold" href="#contact">Get my shortlist →</a
            ><a class="btn btn-glass" href="mortgage-calculator.html">Mortgage calculator</a>
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