<?php
/*
 * Shared <head>, top navigation and mobile menu for dre-homes-new-ui.
 * Expects $meta_tags to be defined by the calling page:
 *   $meta_tags = select_row('tbl_tags', 'id=... and is_active=1');
 * Optional $page: ['active' => 'about', 'css' => [...], 'js' => [...]]
 * (the active menu item is worked out from the file name when not given).
 */
$header_details = select_row('tbl_header_info', 'is_active=1') ?: [];
$header_info = $header_details; // alias for compatibility
$home_meta = select_row('tbl_tags', 'id=7 and is_active=1') ?: [];
$footer_info = select_row('tbl_footer_info', 'is_active=1') ?: [];
$footer_details = $footer_info; // alias for compatibility
$meta_tags = $meta_tags ?? [];
$page = ($page ?? []) + ['active' => '', 'css' => [], 'js' => []];

$path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '', '/');
$slug = $path;
$page_url = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');

// Initialize images array for schema
$images = [];
$prj_type = null;
if (!empty($proj_detail) && isset($proj_detail['prj_type_id'], $proj_detail['prj_id'])) {
    $prj_type = select_row('tbl_proj_type', 'prj_type_id=' . intval($proj_detail['prj_type_id']));
    $images = select_all('tbl_proj_gallery', 'proj_id=' . intval($proj_detail['prj_id']) . ' AND is_active=1');
}

if ($prj_type) {
    $category_name = $prj_type['proj_type_name'];
    $category_slug = slugify($prj_type['proj_type_slug']);
    $breadcrumbs = getBreadcrumbs($proj_detail, $category_name, $category_slug);
} else {
    $breadcrumbs = getBreadcrumbs(null, '', '', $slug);
}

// Active menu item from the page file name
if ($page['active'] === '') {
    $current = basename($_SERVER['SCRIPT_NAME'] ?? '', '.php');
    $sections = [
        'home' => ['index'],
        'properties' => ['offplan-properties', 'off-plans', 'buy-property', 'rent-property', 'international-properties', 'ready-to-move-in'],
        'developers' => ['developers'],
        'communities' => ['communities'],
        'blogs' => ['blogs', 'blog-single', 'our-blogs'],
        'services' => ['property-management', 'property-investment-consultancy', 'mortgage-advisory', 'relocation-service', 'bank-account-opening', 'business-licenses', 'book-valuation', 'mortgage-calculator', 'golden-visa'],
        'guide' => ['buyer-guide', 'seller-guide', 'tenant-guide', 'landlord-guide'],
        'about' => ['about', 'about-us', 'our-media', 'podcast', 'company-news', 'our-team', 'clients-testimonials', 'our-awards', 'careers', 'founders-note-ceo', 'founders-note-clo'],
    ];
    foreach ($sections as $key => $files) {
        if (in_array($current, $files, true)) {
            $page['active'] = $key;
            break;
        }
    }
}
$is_active = fn($key) => $page['active'] === $key ? ' active' : '';

// Page stylesheet: assets/css/pages/<page file name>.css is loaded automatically when it exists
$page_css = 'assets/css/pages/' . basename($_SERVER['SCRIPT_NAME'] ?? '', '.php') . '.css';
if (is_file(dirname(__DIR__) . '/' . $page_css) && !in_array($page_css, $page['css'], true)) {
    $page['css'][] = $page_css;
}

// Services menu: the admin's service pages (the new UI page when it exists)
$services = [];
foreach (select_all('tbl_service_section', 'is_active=1') as $srv_row) {
    $srv_slug = trim($srv_row['serv_sec_slug']);
    $srv_page = $srv_slug === 'property-and-investment-consultancy' ? 'property-investment-consultancy' : $srv_slug;
    $services[] = ['name' => $srv_row['serv_sec_name'], 'url' => page_link($srv_page, 'services/' . $srv_slug)];
}

$currencies = ['AED', 'USD', 'EUR', 'INR', 'GBP', 'AUD', 'CAD', 'CHF', 'CNY', 'JPY', 'NZD', 'ZAR', 'SGD', 'MYR', 'KRW', 'BRL', 'MXN', 'RUB', 'TRY', 'SAR', 'EGP', 'THB', 'IDR', 'PLN'];
$toll_free = $footer_info['footer_contact_number'] ?? '800 37373';
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
    <link rel="canonical" href="<?= htmlspecialchars($page_url) ?>" />
    <meta property="og:url" content="<?= htmlspecialchars($page_url) ?>" />
    <meta property="og:title" content="<?= htmlspecialchars($meta_tags['meta_title'] ?? '') ?>" />
    <meta property="og:description" content="<?= htmlspecialchars($meta_tags['meta_description'] ?? '') ?>" />
<?= !empty($meta_tags['meta_og_img']) ?
        '<meta property="og:image" content="' . htmlspecialchars(preg_match('#^https?://#i', $meta_tags['meta_og_img']) ? $meta_tags['meta_og_img'] : BASE_URL_IMG . 'meta/' . $meta_tags['meta_og_img']) . '" />' :
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

<?= empty($is_404_page) ? generateProjectDetailSchema($home_meta, $header_details, $footer_info, $breadcrumbs, $proj_detail ?? '', $images, $category_slug ?? '', $slug, $meta_tags ?: '') : '' ?>

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

    <!-- Bootstrap 5.3 — loaded into a cascade layer so the DRE theme below always takes priority -->
    <style>
      @layer reset, bootstrap;
      @import url("https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css") layer(bootstrap);
    </style>
    <link rel="stylesheet" href="<?= asset_url('assets/css/main.css') ?>" />
    <?php foreach ($page['css'] as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url($css)) ?>" />
    <?php endforeach; ?>
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
            <?php if (!empty($header_details['greate_place_logo'])): ?>
            <a
              href="<?= page_link('our-awards') ?>"
              class="gptw"
              aria-label="Great Place To Work® Certified — Best Workplaces in Real Estate, Middle East 2026. View our awards"
            >
              <span class="gptw-rib" aria-hidden="true"></span>
              <span class="gptw-badge">
                <img
                  src="<?= BASE_URL_IMG ?>header/<?= htmlspecialchars($header_details['greate_place_logo']) ?>"
                  alt="Great Place To Work — Best Workplaces in Real Estate, Middle East 2026" />
                <i class="gptw-shine" aria-hidden="true"></i>
              </span>
              <span class="gptw-pop glass" aria-hidden="true">
                <img src="<?= BASE_URL_IMG ?>header/<?= htmlspecialchars($header_details['greate_place_logo']) ?>" alt="" />
                <span>
                  <small>Certified</small><b>Great Place To Work®</b>
                  <span>Best Workplaces™ in Real Estate · Middle East 2026</span>
                  <em>View our awards →</em>
                </span>
              </span>
            </a>
            <span class="sep"></span>
            <?php endif; ?>
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
            <li><a class="mi<?= $is_active('home') ?>" href="<?= BASE_URL ?>">Home</a></li>
            <li class="has-dd dropdown">
              <button
                type="button"
                class="mi dropdown-toggle<?= $is_active('properties') ?>"
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
            <li><a class="mi<?= $is_active('developers') ?>" href="<?= BASE_URL ?>developers">Developers</a></li>
            <li><a class="mi<?= $is_active('communities') ?>" href="<?= BASE_URL ?>communities">Communities</a></li>
            <li><a class="mi<?= $is_active('blogs') ?>" href="<?= BASE_URL ?>blogs">Blogs</a></li>
            <li class="has-dd dropdown">
              <button
                type="button"
                class="mi dropdown-toggle<?= $is_active('services') ?>"
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-expanded="false"
              >
                Services<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4.5l3 3 3-3"></path></svg>
              </button>
              <div class="dd wide dropdown-menu">
                <div class="dd-in">
                  <a class="dropdown-item" href="<?= BASE_URL ?>property-management"><b>Property Management</b></a>
                  <?php foreach ($services as $srv): ?>
                  <a class="dropdown-item" href="<?= htmlspecialchars($srv['url']) ?>"><b><?= htmlspecialchars($srv['name']) ?></b></a>
                  <?php endforeach; ?>
                  <a class="dropdown-item" href="<?= BASE_URL ?>mortgage-calculator"><b>Mortgage Calculator</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>golden-visa"><b>Golden Visa</b></a>
                </div>
              </div>
            </li>
            <li class="has-dd dropdown">
              <button
                type="button"
                class="mi dropdown-toggle<?= $is_active('guide') ?>"
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
                class="mi dropdown-toggle<?= $is_active('about') ?>"
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-expanded="false"
              >
                About<svg class="chev" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4.5l3 3 3-3"></path></svg>
              </button>
              <div class="dd wide dropdown-menu">
                <div class="dd-in">
                  <a class="dropdown-item" href="<?= BASE_URL ?>about-us"><b>About Us</b></a>
                  <a class="dropdown-item" href="<?= BASE_URL ?>brand-video"><b>DRE Brand Film</b></a>
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
              <?php foreach ($currencies as $cur): ?>
              <option value="<?= $cur ?>"><?= $cur ?></option>
              <?php endforeach; ?>
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
              <?php foreach ($services as $srv): ?>
              <a href="<?= htmlspecialchars($srv['url']) ?>"><b><?= htmlspecialchars($srv['name']) ?></b></a>
              <?php endforeach; ?>
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
              <a href="<?= BASE_URL ?>about-us"><b>About Us</b></a>
              <a href="<?= BASE_URL ?>brand-video"><b>DRE Brand Film</b></a>
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
            <?php foreach ($currencies as $cur): ?>
            <option value="<?= $cur ?>"><?= $cur ?></option>
            <?php endforeach; ?>
          </select>
          <a class="btn btn-glass" href="tel:<?= preg_replace('/[^0-9+]/', '', $toll_free) ?>" style="flex: 1; justify-content: center">Call <?= htmlspecialchars($toll_free) ?></a>
        </div>
      </div>
    </div>
