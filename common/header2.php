<?php
/*
 * Shared <head>, top navigation and mobile menu for dre-homes-new-ui.
 * Expects $meta_tags to be defined by the calling page:
 *   $meta_tags = select_row('tbl_tags', 'id=... and is_active=1');
 */
$header_details = select_row('tbl_header_info', 'is_active=1') ?: [];
$header_info = $header_details; // alias for compatibility
$home_meta = select_row('tbl_tags', 'id=7 and is_active=1') ?: [];
$footer_info = select_row('tbl_footer_info', 'is_active=1') ?: [];
$footer_details = $footer_info; // alias for compatibility

$pageURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$baseURL = parse_url($pageURL, PHP_URL_SCHEME) . '://' . parse_url($pageURL, PHP_URL_HOST) . parse_url($pageURL, PHP_URL_PATH);

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$parts = explode('/', $path);
$slug = implode('/', $parts);

// Initialize images array for schema
$images = [];
if (!empty($proj_detail) && isset($proj_detail['prj_type_id'], $proj_detail['prj_id'])) {
    $prj_type = select_row('tbl_proj_type', 'prj_type_id=' . intval($proj_detail['prj_type_id']));
    $query = select_query('tbl_proj_gallery', 'proj_id = "' . $proj_detail['prj_id'] . '" AND is_active = 1');
    while ($row = mysqli_fetch_array($query)) {
        $images[] = $row;
    }
} else {
    $prj_type = null;
}

if ($prj_type) {
    $category_name = $prj_type['proj_type_name'];
    $category_slug = slugify($prj_type['proj_type_slug']);
    $breadcrumbs = getBreadcrumbs($proj_detail, $category_name, $category_slug);
} else {
    $breadcrumbs = getBreadcrumbs(null, '', '', $slug);
}

if (isset($_SERVER['PHP_SELF']) && $_SERVER['PHP_SELF'] == '/index.php') {
    echo '<meta http-equiv="refresh" content="1200;URL=' . BASE_URL . '">';
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title><?= !empty($meta_tags['meta_title']) ? htmlspecialchars($meta_tags['meta_title']) : 'DRE Real Estate | Dubai Luxury Properties' ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_tags['meta_description'] ?? '') ?>" />
    <?php if (!empty($meta_tags['meta_keyword'])): ?>
    <meta name="keywords" content="<?= htmlspecialchars($meta_tags['meta_keyword']) ?>" />
    <?php endif; ?>
    <meta name="author" content="DRE Real Estate" />
    <meta name="robots" content="follow,index" />
    <meta http-equiv="Cache-control" content="public" />

    <!-- Canonical & OpenGraph -->
    <link rel="canonical" href="<?= htmlspecialchars((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" />
    <meta property="og:url" content="<?= htmlspecialchars((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" />
    <meta property="og:title" content="<?= htmlspecialchars($meta_tags['meta_title'] ?? '') ?>" />
    <meta property="og:description" content="<?= htmlspecialchars($meta_tags['meta_description'] ?? '') ?>" />
<?= !empty($meta_tags['meta_og_img']) ?
        '<meta property="og:image" content="' . htmlspecialchars(BASE_URL_IMG . 'meta/' . $meta_tags['meta_og_img']) . '" />' :
        (!empty($meta_tags['prj_thumb_img']) ?
            '<meta property="og:image" content="' . htmlspecialchars(BASE_URL_IMG . 'project/thumb/' . trim(explode(',', $meta_tags['prj_thumb_img'])[0])) . '" />' :
            (!empty($meta_tags['dev_img']) ?
                '<meta property="og:image" content="' . htmlspecialchars(BASE_URL_IMG . 'developer/' . $meta_tags['dev_img']) . '" />' :
                (!empty($meta_tags['comm_thumb_img']) ?
                    '<meta property="og:image" content="' . htmlspecialchars(BASE_URL_IMG . 'community/thumb/' . trim(explode(',', $meta_tags['comm_thumb_img'])[0])) . '" />' :
                    (!empty($meta_tags['blog_thumb_img']) ?
                        '<meta property="og:image" content="' . htmlspecialchars(BASE_URL_IMG . 'blog/thumb/' . trim(explode(',', $meta_tags['blog_thumb_img'])[0])) . '" />' :
                        (!empty($meta_tags['team_img']) ?
                            '<meta property="og:image" content="' . htmlspecialchars(BASE_URL_IMG . 'team/' . $meta_tags['team_img']) . '" />' :
                                (!empty($meta_tags['news_inner_img']) ?
                                    '<meta property="og:image" content="' . htmlspecialchars(BASE_URL_IMG . 'company-news/inner/' . $meta_tags['news_inner_img']) . '" />' : ''
                        )
                    )   
                )
            )
        )
        )
?>

    <!-- Favicon -->
    <?php if (!empty($header_details['favicon_logo'])): ?>
    <link rel="shortcut icon" href="<?= BASE_URL_IMG ?>header/<?= htmlspecialchars($header_details['favicon_logo']) ?>" />
    <link rel="icon" href="<?= BASE_URL_IMG ?>header/<?= htmlspecialchars($header_details['favicon_logo']) ?>" />
    <?php else: ?>
    <link rel="shortcut icon" href="<?= BASE_URL ?>images/favicon.ico" />
    <link rel="icon" href="<?= BASE_URL ?>images/favicon.ico" />
    <?php endif; ?>

    <!-- Base URL JS variable -->
    <script type="text/javascript">
        var base_url = '<?= BASE_URL ?>';
    </script>

    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-NDFF757K');
    </script>
    <!-- End Google Tag Manager -->

<?= empty($is_404_page) ? generateProjectDetailSchema($home_meta, $header_details, $footer_info, $breadcrumbs, $proj_detail ?? '', $images, $category_slug ?? '', $slug, $meta_tags ?? '') : '' ?>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,200;0,300;0,400;0,500;0,600;1,200;1,300&amp;display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <meta name="theme-color" content="#5f574c" />
    <script>
      /* apply saved night mode before paint (no flash) */ try {
        if (localStorage.getItem("dre-mode") === "night") {
          document.documentElement.dataset.mode = "night";
          document.querySelector("meta[name=theme-color]").content = "#14110e";
        }
      } catch (e) {}
    </script>

    <!-- Bootstrap 5.3 -->
    <style>
      @layer reset, bootstrap;
      @import url("https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css") layer(bootstrap);
    </style>
    <link rel="stylesheet" href="<?= asset_url('assets/css/main.css') ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/21.2.7/css/intlTelInput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/21.2.7/js/intlTelInput.min.js"></script>
  </head>
  <body>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NDFF757K" height="0" width="0" style="display:none;visibility:hidden;border:none;"></iframe>
    </noscript>

    <div class="ambient">
      <div class="orb o1"></div>
      <div class="orb o2"></div>
      <div class="orb o3"></div>
    </div>
    <div class="grain"></div>

    <!-- ===== TOP NAVIGATION ===== -->
    <nav>
      <div class="wrap">
        <div class="nav-in glass">
          <div class="brand">
            <a
              href="<?= BASE_URL ?>our-awards"
              class="gptw"
              aria-label="Great Place To Work® Certified — Best Workplaces in Real Estate, Middle East 2026. View our awards"
            >
              <span class="gptw-rib" aria-hidden="true"></span>
              <span class="gptw-badge">
                <?php if (!empty($header_details['greate_place_logo'])): ?>
                <img
                  src="<?= BASE_URL_IMG ?>header/<?= htmlspecialchars($header_details['greate_place_logo']) ?>"
                  alt="Great Place To Work — Best Workplaces in Real Estate, Middle East 2026" />
                <?php endif; ?>
                <i class="gptw-shine" aria-hidden="true"></i>
              </span>
              <span class="gptw-pop glass" aria-hidden="true">
                <?php if (!empty($header_details['greate_place_logo'])): ?>
                <img src="<?= BASE_URL_IMG ?>header/<?= htmlspecialchars($header_details['greate_place_logo']) ?>" alt="" />
                <?php endif; ?>
                <span>
                  <small>Certified</small><b>Great Place To Work®</b>
                  <span>Best Workplaces™ in Real Estate · Middle East 2026</span>
                  <em>View our awards →</em>
                </span>
              </span>
            </a>
            <span class="sep"></span>
            <a href="<?= BASE_URL ?>" class="logo" aria-label="DRE Homes — home">
              <img
                class="site-logo"
                src="<?= BASE_URL_IMG ?>header/<?= htmlspecialchars($header_details['header_logo'] ?? '') ?>"
                alt="<?= htmlspecialchars($header_details['header_logo_alt'] ?? 'DRE Real Estate logo') ?>"
                width="250"
                height="110"
              />
            </a>
          </div>

          <ul class="menu" role="menubar">
            <li><a class="mi" href="<?= BASE_URL ?>">Home</a></li>
            <li class="has-dd dropdown">
              <button
                type="button"
                class="mi dropdown-toggle"
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-expanded="false"
              >
                Properties<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4.5l3 3 3-3"></path></svg>
              </button>
              <div class="dd dropdown-menu">
                <div class="dd-in">
                  <a class="dropdown-item" href="<?= BASE_URL ?>offplan-properties"><b>Off Plan Property</b><small>New launches &amp; payment plans</small></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>buy-property"><b>Buy Property</b><small>Ready homes for sale</small></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>rent-property"><b>Rent Property</b><small>Apartments, villas &amp; more</small></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>international-properties"><b>International Properties</b><small>Beyond the UAE</small></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>ready-to-move-in"><b>Ready To Move In</b><small>Keys on transfer</small></a>
                </div>
              </div>
            </li>
            <li><a class="mi" href="<?= BASE_URL ?>developers">Developers</a></li>
            <li><a class="mi" href="<?= BASE_URL ?>communities">Communities</a></li>
            <li><a class="mi" href="<?= BASE_URL ?>blogs">Blogs</a></li>
            <li class="has-dd dropdown">
              <button
                type="button"
                class="mi dropdown-toggle"
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-expanded="false"
              >
                Services<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4.5l3 3 3-3"></path></svg>
              </button>
              <div class="dd wide dropdown-menu">
                <div class="dd-in">
                  <a class="dropdown-item" href="<?= BASE_URL ?>property-management"><b>Property Management</b></a>
                  <?php
                  $srv_query = select_query('tbl_service_section', 'is_active=1');
                  while ($srv_row = mysqli_fetch_array($srv_query)):
                    $srv_slug = trim($srv_row['serv_sec_slug']);
                  ?>
                    <a class="dropdown-item" href="<?= BASE_URL ?>services/<?= htmlspecialchars($srv_slug) ?>"><b><?= htmlspecialchars($srv_row['serv_sec_name']) ?></b></a>
                  <?php endwhile; ?>
                  <a class="dropdown-item" href="<?= BASE_URL ?>mortgage-calculator"><b>Mortgage Calculator</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>golden-visa"><b>Golden Visa</b></a>
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
                Guide<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4.5l3 3 3-3"></path></svg>
              </button>
              <div class="dd dropdown-menu">
                <div class="dd-in">
                  <a class="dropdown-item" href="<?= BASE_URL ?>buyer-guide"><b>Buyer Guide</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>seller-guide"><b>Seller Guide</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>tenant-guide"><b>Tenant Guide</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>landlord-guide"><b>Landlord Guide</b></a>
                </div>
              </div>
            </li>
            <li class="has-dd dropdown">
              <button
                type="button"
                class="mi dropdown-toggle active"
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-expanded="false"
              >
                About<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4.5l3 3 3-3"></path></svg>
              </button>
              <div class="dd wide dropdown-menu">
                <div class="dd-in">
                  <a class="dropdown-item" href="<?= BASE_URL ?>about.php"><b>About Us</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>our-media"><b>Media</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>podcast"><b>Podcast</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>company-news"><b>Company News</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>our-team"><b>Our Team</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>clients-testimonials"><b>Client Testimonial</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>our-awards"><b>Our Awards</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>careers"><b>Careers</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>message-from-co-founder-and-clo"><b>Message From CLO</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>message-from-co-founder-and-ceo"><b>Message From CEO</b></a>
                </div>
              </div>
            </li>
          </ul>

          <div class="nav-r">
            <select class="cur" id="global-currency-select" aria-label="Currency">
              <option value="AED">AED</option>
              <option value="USD">USD</option>
              <option value="EUR">EUR</option>
              <option value="INR">INR</option>
              <option value="GBP">GBP</option>
              <option value="AUD">AUD</option>
              <option value="CAD">CAD</option>
              <option value="CHF">CHF</option>
              <option value="CNY">CNY</option>
              <option value="JPY">JPY</option>
              <option value="NZD">NZD</option>
              <option value="ZAR">ZAR</option>
              <option value="SGD">SGD</option>
              <option value="MYR">MYR</option>
              <option value="KRW">KRW</option>
              <option value="BRL">BRL</option>
              <option value="MXN">MXN</option>
              <option value="RUB">RUB</option>
              <option value="TRY">TRY</option>
              <option value="SAR">SAR</option>
              <option value="EGP">EGP</option>
              <option value="THB">THB</option>
              <option value="IDR">IDR</option>
              <option value="PLN">PLN</option>
            </select>
            <a href="#contact" class="btn btn-gold nav-cta" style="padding: 12px 22px">Private Viewing</a>
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

    <!-- ===== MOBILE OFFCANVAS MENU ===== -->
    <div class="mmenu glass offcanvas offcanvas-top" id="mmenu" tabindex="-1" aria-label="Menu">
      <div class="mm-list accordion" id="mmList">
        <a class="mm-link" href="<?= BASE_URL ?>">Home</a>
        <div class="mm-acc">
          <button
            type="button"
            class="mm-sum collapsed"
            data-bs-toggle="collapse"
            data-bs-target="#mmProperties"
            aria-expanded="false"
            aria-controls="mmProperties"
          >
            Properties<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4.5l3 3 3-3"></path></svg>
          </button>
          <div class="collapse" id="mmProperties" data-bs-parent="#mmList">
            <div class="mm-sub">
              <a href="<?= BASE_URL ?>offplan-properties"><b>Off Plan Property</b></a>
              <a href="<?= BASE_URL ?>buy-property"><b>Buy Property</b></a>
              <a href="<?= BASE_URL ?>rent-property"><b>Rent Property</b></a>
              <a href="<?= BASE_URL ?>international-properties"><b>International Properties</b></a>
              <a href="<?= BASE_URL ?>ready-to-move-in"><b>Ready To Move In</b></a>
            </div>
          </div>
        </div>
        <a class="mm-link" href="<?= BASE_URL ?>developers">Developers</a>
        <a class="mm-link" href="<?= BASE_URL ?>communities">Communities</a>
        <a class="mm-link" href="<?= BASE_URL ?>blogs">Blogs</a>
        <div class="mm-acc">
          <button
            type="button"
            class="mm-sum collapsed"
            data-bs-toggle="collapse"
            data-bs-target="#mmServices"
            aria-expanded="false"
            aria-controls="mmServices"
          >
            Services<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4.5l3 3 3-3"></path></svg>
          </button>
          <div class="collapse" id="mmServices" data-bs-parent="#mmList">
            <div class="mm-sub">
              <a href="<?= BASE_URL ?>property-management"><b>Property Management</b></a>
              <?php
              $srv_query2 = select_query('tbl_service_section', 'is_active=1');
              while ($srv_row2 = mysqli_fetch_array($srv_query2)):
                $srv_slug2 = trim($srv_row2['serv_sec_slug']);
              ?>
                <a href="<?= BASE_URL ?>services/<?= htmlspecialchars($srv_slug2) ?>"><b><?= htmlspecialchars($srv_row2['serv_sec_name']) ?></b></a>
              <?php endwhile; ?>
              <a href="<?= BASE_URL ?>mortgage-calculator"><b>Mortgage Calculator</b></a>
              <a href="<?= BASE_URL ?>golden-visa"><b>Golden Visa</b></a>
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
            Guide<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4.5l3 3 3-3"></path></svg>
          </button>
          <div class="collapse" id="mmGuide" data-bs-parent="#mmList">
            <div class="mm-sub">
              <a href="<?= BASE_URL ?>buyer-guide"><b>Buyer Guide</b></a>
              <a href="<?= BASE_URL ?>seller-guide"><b>Seller Guide</b></a>
              <a href="<?= BASE_URL ?>tenant-guide"><b>Tenant Guide</b></a>
              <a href="<?= BASE_URL ?>landlord-guide"><b>Landlord Guide</b></a>
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
            About<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4.5l3 3 3-3"></path></svg>
          </button>
          <div class="collapse" id="mmAbout" data-bs-parent="#mmList">
            <div class="mm-sub">
              <a href="<?= BASE_URL ?>about.php"><b>About Us</b></a>
              <a href="<?= BASE_URL ?>our-media"><b>Media</b></a>
              <a href="<?= BASE_URL ?>podcast"><b>Podcast</b></a>
              <a href="<?= BASE_URL ?>company-news"><b>Company News</b></a>
              <a href="<?= BASE_URL ?>our-team"><b>Our Team</b></a>
              <a href="<?= BASE_URL ?>clients-testimonials"><b>Client Testimonial</b></a>
              <a href="<?= BASE_URL ?>our-awards"><b>Our Awards</b></a>
              <a href="<?= BASE_URL ?>careers"><b>Careers</b></a>
              <a href="<?= BASE_URL ?>message-from-co-founder-and-clo"><b>Message From CLO</b></a>
              <a href="<?= BASE_URL ?>message-from-co-founder-and-ceo"><b>Message From CEO</b></a>
            </div>
          </div>
        </div>
      </div>
      <div class="mm-foot">
        <a class="btn btn-gold" href="#contact" style="justify-content: center">Private Viewing</a>
        <div class="mm-row">
          <select class="cur mm-cur" aria-label="Currency">
            <option value="AED">AED</option>
            <option value="USD">USD</option>
            <option value="EUR">EUR</option>
            <option value="INR">INR</option>
            <option value="GBP">GBP</option>
            <option value="AUD">AUD</option>
            <option value="CAD">CAD</option>
            <option value="CHF">CHF</option>
            <option value="CNY">CNY</option>
            <option value="JPY">JPY</option>
            <option value="NZD">NZD</option>
            <option value="ZAR">ZAR</option>
            <option value="SGD">SGD</option>
            <option value="MYR">MYR</option>
            <option value="KRW">KRW</option>
            <option value="BRL">BRL</option>
            <option value="MXN">MXN</option>
            <option value="RUB">RUB</option>
            <option value="TRY">TRY</option>
            <option value="SAR">SAR</option>
            <option value="EGP">EGP</option>
            <option value="THB">THB</option>
            <option value="IDR">IDR</option>
            <option value="PLN">PLN</option>
          </select>
          <a class="btn btn-glass" href="tel:<?= preg_replace('/[^0-9+]/', '', $footer_info['footer_contact_number'] ?? '80037373') ?>" style="flex: 1; justify-content: center">Call <?= $footer_info['footer_contact_number'] ?? '800 37373' ?></a>
        </div>
      </div>
    </div>
