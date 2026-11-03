
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta
      name="description"
      content="An award-winning, Tier One real estate brokerage in Dubai Hills Estate — connecting people with the right properties since 2007."
    />
    <title>Pages</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,200;0,300;0,400;0,500;0,600;1,200;1,300&amp;display=swap"
      rel="stylesheet"
    />
    <meta name="theme-color" content="#14110e" />
    <script>
      /* Dark mode is the default; respect a visitor's saved day/night choice before paint. */
      try {
        var savedMode = localStorage.getItem("dre-mode");
        if (savedMode !== "day") {
          document.documentElement.dataset.mode = "night";
          document.querySelector('meta[name="theme-color"]').content = "#14110e";
        } else {
          document.querySelector('meta[name="theme-color"]').content = "#5f574c";
        }
      } catch (e) {
        document.documentElement.dataset.mode = "night";
      }
    </script>
    <!-- Bootstrap 5.3 — loaded into a cascade layer so the DRE theme below always takes priority -->
    <style>
      @layer reset, bootstrap;
      @import url("https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css") layer(bootstrap);
    </style>  
    <link rel="stylesheet" href="assets/css/main.css?v=4" />
    <link rel="stylesheet" href="assets/css/suggest.css?v=2" />
    <link rel="stylesheet" href="assets/css/pages-style.css?v=2" />
    <link rel="stylesheet" href="assets/css/style.css?v=3" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/21.2.7/css/intlTelInput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/21.2.7/js/intlTelInput.min.js"></script>
  </head>
  <body>
    <div class="ambient">
      <div class="orb o1"></div>
      <div class="orb o2"></div>
      <div class="orb o3"></div>
    </div>
    <div class="grain"></div>

    <nav>
      <div class="wrap">
        <div class="nav-in glass">
          <div class="brand">
            <a
              href="our-awards.php"
              class="gptw"
              aria-label="Great Place To Work® Certified — Best Workplaces in Real Estate, Middle East 2026. View our awards"
            >
              <span class="gptw-rib" aria-hidden="true"></span>
              <span class="gptw-badge"
                ><img
                  src="https://drehomes.com/admin_nsrdwsc/assets/media/header/greate_place_logo_2803_Great-place-to-work-middle-east-website.png"
                  alt="Great Place To Work — Best Workplaces in Real Estate, Middle East 2026" /><i
                  class="gptw-shine"
                  aria-hidden="true"
                ></i
              ></span>
              <span class="gptw-pop glass" aria-hidden="true"
                ><img
                  src="https://drehomes.com/admin_nsrdwsc/assets/media/header/greate_place_logo_2803_Great-place-to-work-middle-east-website.png"
                  alt=""
                /><span
                  ><small>Certified</small><b>Great Place To Work®</b
                  ><span>Best Workplaces™ in Real Estate · Middle East 2026</span><em>View our awards →</em></span
                ></span
              > </a
            ><span class="sep"></span
            ><a href="index.php" class="logo" aria-label="DRE Homes — home"
              ><img
                class="site-logo"
                src="https://drehomes.com/admin_nsrdwsc/assets/media/header/header_logo_7708_dre-logo-b.png"
                alt="DRE Real Estate logo"
                width="250"
                height="110"
            /></a>
          </div>
          <ul class="menu" role="menubar">
            <li><a class="mi" href="index.php">Home</a></li>
            <li class="has-dd dropdown">
              <button
                type="button"
                class="mi dropdown-toggle"
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-expanded="false"
              >
                Properties<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6">
                  <path d="M3 4.5l3 3 3-3"></path>
                </svg>
              </button>
              <div class="dd dropdown-menu">
                <div class="dd-in">
                  <a class="dropdown-item" href="off-plans.php"
                    ><b>Off Plan Property</b><small>New launches &amp; payment plans</small></a
                  ><a class="dropdown-item" href="buy-property.php"
                    ><b>Buy Property</b><small>Ready homes for sale</small></a
                  ><a class="dropdown-item" href="rent-property.php"
                    ><b>Rent Property</b><small>Apartments, villas &amp; more</small></a
                  ><a class="dropdown-item" href="international-properties.php"
                    ><b>International Properties</b><small>Beyond the UAE</small></a
                  ><a class="dropdown-item" href="ready-to-move-in.php"
                    ><b>Ready To Move In</b><small>Keys on transfer</small></a
                  >
                </div>
              </div>
            </li>
            <li><a class="mi" href="developers.php">Developers</a></li>
            <li><a class="mi" href="communities.php">Communities</a></li>
            <li><a class="mi" href="blogs.php">Blogs</a></li>
            <li class="has-dd dropdown">
              <button
                type="button"
                class="mi dropdown-toggle"
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-expanded="false"
              >
                Services<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6">
                  <path d="M3 4.5l3 3 3-3"></path>
                </svg>
              </button>
              <div class="dd wide dropdown-menu">
                <div class="dd-in">
                  <a class="dropdown-item" href="property-management.php"><b>Property Management</b></a
                  ><a class="dropdown-item" href="property-investment-consultancy.php"
                    ><b>Property &amp; Investment Consultancy</b></a
                  ><a class="dropdown-item" href="mortgage-advisory.php"><b>Mortgage Advisory</b></a
                  ><a class="dropdown-item" href="relocation-service.php"><b>Relocation Service</b></a
                  ><a class="dropdown-item" href="bank-account-opening.php"><b>Bank Account Opening</b></a
                  ><a class="dropdown-item" href="business-licenses.php"><b>Business Licenses</b></a
                  ><a class="dropdown-item" href="book-valuation.php"><b>Book Your Valuation</b></a
                  ><a class="dropdown-item" href="mortgage-calculator.php"><b>Mortgage Calculator</b></a
                  ><a class="dropdown-item" href="golden-visa.php"><b>Golden Visa</b></a>
                </div>
              </div>
            </li>
            <li class="has-dd dropdown">
              <button
                type="button"
                class="mi dropdown-toggle"
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-expanded="false"
              >
                Guide<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6">
                  <path d="M3 4.5l3 3 3-3"></path>
                </svg>
              </button>
              <div class="dd dropdown-menu">
                <div class="dd-in">
                  <a class="dropdown-item" href="buyer-guide.php"><b>Buyer Guide</b></a
                  ><a class="dropdown-item" href="seller-guide.php"><b>Seller Guide</b></a
                  ><a class="dropdown-item" href="tenant-guide.php"><b>Tenant Guide</b></a
                  ><a class="dropdown-item" href="landlord-guide.php"><b>Landlord Guide</b></a>
                </div>
              </div>
            </li>
            <li class="has-dd dropdown">
              <button
                type="button"
                class="mi dropdown-toggle "
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-expanded="false"
              >
                About<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6">
                  <path d="M3 4.5l3 3 3-3"></path>
                </svg>
              </button>
              <div class="dd wide dropdown-menu">
                <div class="dd-in">
                  <a class="dropdown-item" href="about-us.php"><b>About Us</b></a
                  ><a class="dropdown-item" href="gallery.php"><b>Media</b></a
                  ><a class="dropdown-item" href="podcast.php"><b>Podcast</b></a
                  ><a class="dropdown-item" href="company-news.php"><b>Company News</b></a
                  ><a class="dropdown-item" href="our-team.php"><b>Our Team</b></a
                  ><a class="dropdown-item" href="clients-testimonials.php"><b>Client Testimonial</b></a
                  ><a class="dropdown-item" href="our-awards.php"><b>Our Awards</b></a
                  ><a class="dropdown-item" href="careers.php"><b>Careers</b></a
                  ><a class="dropdown-item" href="message-from-co-founder-and-clo.php"><b>Message From CLO</b></a
                  ><a class="dropdown-item" href="message-from-co-founder-and-ceo.php"><b>Message From CEO</b></a>
                </div>
              </div>
            </li>
          </ul>
          <div class="nav-r currency-wrap">
            <select class="cur" aria-label="Currency">
              <option>AED</option>
              <option>USD</option>
              <option>EUR</option>
              <option>INR</option>
              <option>GBP</option>
              <option>AUD</option>
              <option>CAD</option>
              <option>CHF</option>
              <option>CNY</option>
              <option>JPY</option>
              <option>NZD</option>
              <option>ZAR</option>
              <option>SGD</option>
              <option>MYR</option>
              <option>KRW</option>
              <option>BRL</option>
              <option>MXN</option>
              <option>RUB</option>
              <option>TRY</option>
              <option>SAR</option>
              <option>EGP</option>
              <option>THB</option>
              <option>IDR</option>
              <option>PLN</option>
            </select>
            <a href="#contact" class="btn btn-gold nav-cta" style="padding: 7px 17px">Contact Us</a>
            <button
              class="burger"
              id="burger"
              type="button"
              data-bs-toggle="offcanvas"
              data-bs-target="#mmenu"
              aria-label="Open menu"
              aria-expanded="false"
              aria-controls="mmenu"
            >
              <span></span><span></span><span></span>
            </button>
          </div>
        </div>
      </div>
    </nav>
    <div class="mmenu glass offcanvas offcanvas-top" id="mmenu" tabindex="-1" aria-label="Menu">
      <div class="mm-list accordion" id="mmList">
        <a class="mm-link" href="index.php">Home</a>
        <div class="mm-acc">
          <button
            type="button"
            class="mm-sum collapsed"
            data-bs-toggle="collapse"
            data-bs-target="#mmProperties"
            aria-expanded="false"
            aria-controls="mmProperties"
          >
            Properties<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M3 4.5l3 3 3-3"></path>
            </svg>
          </button>
          <div class="collapse" id="mmProperties" data-bs-parent="#mmList">
            <div class="mm-sub">
              <a href="off-plans.php"><b>Off Plan Property</b></a
              ><a href="buy-property.php"><b>Buy Property</b></a
              ><a href="rent-property.php"><b>Rent Property</b></a
              ><a href="international-properties.php"><b>International Properties</b></a
              ><a href="ready-to-move-in.php"><b>Ready To Move In</b></a>
            </div>
          </div>
        </div>
        <a class="mm-link" href="developers.php">Developers</a
        ><a class="mm-link" href="communities.php">Communities</a><a class="mm-link" href="blogs.php">Blogs</a>
        <div class="mm-acc">
          <button
            type="button"
            class="mm-sum collapsed"
            data-bs-toggle="collapse"
            data-bs-target="#mmServices"
            aria-expanded="false"
            aria-controls="mmServices"
          >
            Services<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M3 4.5l3 3 3-3"></path>
            </svg>
          </button>
          <div class="collapse" id="mmServices" data-bs-parent="#mmList">
            <div class="mm-sub">
              <a href="property-management.php"><b>Property Management</b></a
              ><a href="property-investment-consultancy.php"
                ><b>Property &amp; Investment Consultancy</b></a
              ><a href="mortgage-advisory.php"><b>Mortgage Advisory</b></a
              ><a href="relocation-service.php"><b>Relocation Service</b></a
              ><a href="bank-account-opening.php"><b>Bank Account Opening</b></a
              ><a href="business-licenses.php"><b>Business Licenses</b></a
              ><a href="book-valuation.php"><b>Book Your Valuation</b></a
              ><a href="mortgage-calculator.php"><b>Mortgage Calculator</b></a
              ><a href="golden-visa.php"><b>Golden Visa</b></a>
            </div>
          </div>
        </div>
        <div class="mm-acc">
          <button
            type="button"
            class="mm-sum collapsed"
            data-bs-toggle="collapse"
            data-bs-target="#mmGuide"
            aria-expanded="false"
            aria-controls="mmGuide"
          >
            Guide<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M3 4.5l3 3 3-3"></path>
            </svg>
          </button>
          <div class="collapse" id="mmGuide" data-bs-parent="#mmList">
            <div class="mm-sub">
              <a href="buyer-guide.php"><b>Buyer Guide</b></a
              ><a href="seller-guide.php"><b>Seller Guide</b></a
              ><a href="tenant-guide.php"><b>Tenant Guide</b></a
              ><a href="landlord-guide.php"><b>Landlord Guide</b></a>
            </div>
          </div>
        </div>
        <div class="mm-acc">
          <button
            type="button"
            class="mm-sum collapsed"
            data-bs-toggle="collapse"
            data-bs-target="#mmAbout"
            aria-expanded="false"
            aria-controls="mmAbout"
          >
            About<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M3 4.5l3 3 3-3"></path>
            </svg>
          </button>
          <div class="collapse" id="mmAbout" data-bs-parent="#mmList">
            <div class="mm-sub">
              <a href="about-us.php"><b>About Us</b></a
              ><a href="gallery.php"><b>Media</b></a
              ><a href="podcast.php"><b>Podcast</b></a
              ><a href="company-news.php"><b>Company News</b></a
              ><a href="our-team.php"><b>Our Team</b></a
              ><a href="clients-testimonials.php"><b>Client Testimonial</b></a
              ><a href="our-awards.php"><b>Our Awards</b></a
              ><a href="careers.php"><b>Careers</b></a
              ><a href="message-from-co-founder-and-clo.php"><b>Message From CLO</b></a
              ><a href="message-from-co-founder-and-ceo.php"><b>Message From CEO</b></a>
            </div>
          </div>
        </div>
      </div>
      <div class="mm-foot">
        <a class="btn btn-gold" href="#contact" style="justify-content: center">Contact Us</a>
        <div class="mm-row ">
          <!-- <div class="currency-wrap">
          <select class="cur mm-cur" aria-label="Currency">
            <option>AED</option>
            <option>USD</option>
            <option>EUR</option>
            <option>INR</option>
            <option>GBP</option>
            <option>AUD</option>
            <option>CAD</option>
            <option>CHF</option>
            <option>CNY</option>
            <option>JPY</option>
            <option>NZD</option>
            <option>ZAR</option>
            <option>SGD</option>
            <option>MYR</option>
            <option>KRW</option>
            <option>BRL</option>
            <option>MXN</option>
            <option>RUB</option>
            <option>TRY</option>
            <option>SAR</option>
            <option>EGP</option>
            <option>THB</option>
            <option>IDR</option>
            <option>PLN</option></select
          >
          </div> -->
          <a class="btn btn-glass" href="tel:80037373" style="flex: 1; justify-content: center">Call 800 37373</a>
        </div>
      </div>
    </div>