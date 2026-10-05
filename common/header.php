<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php
    $header_details = select_row('tbl_header_info', 'is_active=1');
    $home_meta = select_row('tbl_tags', 'id=7 and is_active=1');
    $footer_info = select_row('tbl_footer_info', 'is_active=1');
    $meta_tags = $home_meta ?? [];

    $pageURL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $baseURL = parse_url($pageURL, PHP_URL_SCHEME) . '://' . parse_url($pageURL, PHP_URL_HOST) . parse_url($pageURL, PHP_URL_PATH);

    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $parts = explode('/', $path);
    $slug = implode('/', $parts);

    // Initialize images array
    $images = [];

    // Load project type and images only if $proj_detail is set and valid
    if (!empty($proj_detail) && isset($proj_detail['prj_type_id'], $proj_detail['prj_id'])) {
        $prj_type = select_row('tbl_proj_type', 'prj_type_id=' . intval($proj_detail['prj_type_id']));
        $query = select_query('tbl_proj_gallery', 'proj_id = "' . $proj_detail['prj_id'] . '" AND is_active = 1');
        while ($row = mysqli_fetch_array($query)) {
            $images[] = $row;
        }
    } else {
        $prj_type = null;
    }

    // Set breadcrumbs based on whether project detail is present
    if ($prj_type) {
        $category_name = $prj_type['proj_type_name'];
        $category_slug = slugify($prj_type['proj_type_slug']);
        $breadcrumbs = getBreadcrumbs($proj_detail, $category_name, $category_slug);
    } else {
        // For other pages, build breadcrumbs using slug
        $breadcrumbs = getBreadcrumbs(null, '', '', $slug);
    }

    if ($_SERVER['PHP_SELF'] == '/index.php') {
        echo '<meta http-equiv="refresh" content="1200;URL=' . BASE_URL . '">';
    }

    echo '<meta http-equiv="content-type" content="text/html;charset=UTF-8" />';
    ?>



    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title><?= $meta_tags['meta_title'] ?></title>
    <meta name="description" content="<?= $meta_tags['meta_description'] ?>">
    <meta name="author" content="DRE Real Estate">
    <meta name="robots" content="follow,index">
    <meta http-equiv="Cache-control" content="public">

    <!-- Canonical & OpenGraph -->
    <link rel="canonical"
        href="<?= htmlspecialchars((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" />
    <meta property="og:url"
        content="<?= htmlspecialchars((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" />
    <meta property="og:title" content="<?= htmlspecialchars($meta_tags['meta_title']) ?>" />
    <meta property="og:description" content="<?= htmlspecialchars($meta_tags['meta_description']) ?>" />
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




    <!-- Styles -->
    <link type="text/css" rel="stylesheet" href="<?= BASE_URL ?>css/plugins.css">
    <link type="text/css" rel="stylesheet" href="<?= BASE_URL ?>css/style.css?cache=11112271">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/21.2.7/css/intlTelInput.css">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= BASE_URL ?>images/favicon.ico">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
    <!-- Base URL JS variable -->
    <script type="text/javascript">
        var base_url = '<?= BASE_URL ?>';
    </script>

    <!-- Google Tag Manager -->
    <script>
        (function (w, d, s, l, i) {
            w[l] = w[l] || []; w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            }); var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-NDFF757K');
    </script>
    <!-- End Google Tag Manager -->


    <?= generateProjectDetailSchema($home_meta, $header_details, $footer_info, $breadcrumbs, $proj_detail, $images, $category_slug, $slug, $meta_tags) ?>



</head>

<body>

    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NDFF757K" height="0" width="0"
            style="display:none;visibility:hidden;border:none;"></iframe>
    </noscript>

    <!-- Loader -->
    <div class="loader-wrap"></div>

    <!-- Main Content -->
    <div id="main">

        <!-- Header -->
        <header class="main-header">
            <div class="container">
                <div class="header-inner">
                    <a href="javascript:void(0)" class="logo-holder badge-holder">
                        <?php
                        if($header_details['greate_place_logo']){
                        ?>
                        <img src="<?= BASE_URL_IMG ?>header/<?= $header_details['greate_place_logo'] ?>" alt="Great Place to Work">
                        <?php } ?>
                    </a>
                    <a href="<?= BASE_URL ?>" class="logo-holder">
                        <img src="<?= BASE_URL_IMG ?>header/<?= $header_details['header_logo'] ?>"
                            alt="<?= $header_details['header_logo_alt'] ?>">
                    </a>

                    <!-- Navigation -->
                    <div class="nav-holder main-menu">
                        <nav>
                            <ul class="no-list-style">
                                <li><a href="<?= BASE_URL ?>">Home</a></li>

                                <li>
                                    <a href="javascript:void(0)">Properties <i class="fa-solid fa-caret-down"></i></a>
                                    <ul>
                                        <li><a href="<?= BASE_URL ?>offplan-properties">Off Plan Property</a></li>
                                        <li><a href="<?= BASE_URL ?>buy-property">Buy Property</a></li>
                                        <li><a href="<?= BASE_URL ?>rent-property">Rent Property</a></li>
                                        <li><a href="<?= BASE_URL ?>international-properties">International
                                                Properties</a></li>
                                        <li><a href="<?= BASE_URL ?>ready-to-move-in">Ready To Move In</a></li>
                                    </ul>
                                </li>

                                <li><a href="<?= BASE_URL ?>developer">Developers</a></li>
                                <li><a href="<?= BASE_URL ?>community">Communities</a></li>
                                <li><a href="<?= BASE_URL ?>our-blogs/">Blogs</a></li>

                                <?php
                                $query = select_query('tbl_service_section', 'is_active=1');
                                if (mysqli_num_rows($query) > 0):
                                    ?>
                                    <li class="services-list">
                                        <a href="javascript:void(0)">Services <i class="fa-solid fa-caret-down"></i></a>
                                        <ul>
                                            <?php while ($row = mysqli_fetch_array($query)): ?>
                                                <li><a
                                                        href="<?= BASE_URL ?>services/<?= $row['serv_sec_slug'] ?>"><?= $row['serv_sec_name'] ?></a>
                                                </li>
                                            <?php endwhile; ?>
                                            <li><a href="<?= BASE_URL ?>mortgage-calculator">Mortgage Calculator</a></li>
                                            <li><a href="<?= BASE_URL ?>golden-visa">Golden Visa</a></li>
                                        </ul>
                                    </li>
                                <?php endif; ?>

                                <li>
                                    <a href="javascript:void(0)">Guide <i class="fa-solid fa-caret-down"></i></a>
                                    <ul>
                                        <li><a href="<?= BASE_URL ?>buyer-guide">Buyer Guide</a></li>
                                        <li><a href="<?= BASE_URL ?>seller-guide">Seller Guide</a></li>
                                        <li><a href="<?= BASE_URL ?>tenant-guide">Tenant Guide</a></li>
                                        <li><a href="<?= BASE_URL ?>landlord-guide">Landlord Guide</a></li>
                                    </ul>
                                </li>

                                <li class="about-list">
                                    <a href="javascript:void(0)">About <i class="fa-solid fa-caret-down"></i></a>
                                    <ul>
                                        <li><a href="<?= BASE_URL ?>our-media">Media</a></li>
                                        <li><a href="<?= BASE_URL ?>company-news">Company News</a></li>
                                        <li><a href="<?= BASE_URL ?>our-team">Our Team</a></li>
                                        <li><a href="<?= BASE_URL ?>clients-testimonials">Client Testimonial</a></li>
                                        <li><a href="<?= BASE_URL ?>our-awards">Our Awards</a></li>
                                        <li><a href="<?= BASE_URL ?>careers">Careers</a></li>
                                        <li><a href="<?= BASE_URL ?>message-from-co-founder-and-clo">Message From
                                                CLO</a></li>
                                        <li><a href="<?= BASE_URL ?>message-from-co-founder-and-ceo">Message From
                                                CEO</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <!-- Currency Dropdown -->
                    <div class="currency-change">
                        <ul class="no-list-style d-flex align-items-center gap-2 m-0">
                            <li>
                                <select id="global-currency-select" class="cur-dd currency-select" style="width: 50px;">
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
                            </li>
                            <li><i class="fa-solid fa-earth-americas ms-1"></i></li>
                        </ul>
                    </div>

                    <!-- Mobile Nav Button -->
                    <div class="nav-button-wrap">
                        <div class="nav-button"><span></span><span></span><span></span></div>
                    </div>

                </div>
            </div>
        </header>

        <!-- Search Overlay -->
        <div class="body-overlay fs-wrapper search-form-overlay close-search-form"></div>