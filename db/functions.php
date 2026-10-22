<?php

function generateProjectDetailSchema($home_meta, $header_details, $footer_info, $breadcrumbs, $proj_detail, $images, $category_slug, $slug, $meta_tags) {
    // Case 1: If slug is empty, return dynamic Organization schema
    if (empty($slug)) {
        ob_start();
        ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "name": "<?= $home_meta['meta_title'] ?>",
      "url": "<?= BASE_URL ?>",
      "logo": "<?= BASE_URL ?>images/dre-logo.png",
      "description": "<?= $home_meta['meta_description'] ?>",
      "sameAs": [
        "<?= $header_details['header_instagram_link'] ?>",
        "<?= $header_details['header_fb_link'] ?>",
        "<?= $header_details['header_youtube_link'] ?>",
        "https://www.linkedin.com/company/drehomes-real-estate-brokers/"
      ],
      "address": [
        "<?= $footer_info['footer_address'] ?>",
        "<?= $footer_info['footer_address2'] ?>"
      ],
      "contactPoint": {
        "@type": "ContactPoint",
        "contactType": "customer service",
        "telephone": "<?= $footer_info['footer_contact_number2'] ?>",
        "email": "<?= $footer_info['footer_email_id'] ?>",
        "areaServed": "AE",
        "availableLanguage": ["English", "Arabic"]
      },
      "foundingDate": "2007-01-01",
      "foundingLocation": {
        "@type": "Place",
        "name": "Dubai, United Arab Emirates"
      },
      "founder": [
        { "@type": "Person", "name": "Qurat Ul Ain" },
        { "@type": "Person", "name": "Mudasir Wani" }
      ],
      "employee": [
        {
          "@type": "Person",
          "name": "Mudasir Wani",
          "jobTitle": "Chief Executive Officer"
        },
        {
          "@type": "Person",
          "name": "Qurat Ul Ain",
          "jobTitle": "Chief Learning Officer"
        }
      ]
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
        {
          "@type": "ListItem",
          "position": <?= $index + 1 ?>,
          "name": "<?= htmlspecialchars($breadcrumb['name'], ENT_QUOTES) ?>",
          "item": "<?= $breadcrumb['url'] ?>"
        }<?= $index < count($breadcrumbs) - 1 ? ',' : '' ?>
        <?php endforeach; ?>
      ]
    }
  ]
}
</script>
<?php
        return ob_get_clean();
    }

    // Case 2: Static Pages
    $static_slugs = [
        'offplan-properties',
        'developer',
        'community',
        'our-blogs',
        'buy-property',
        'rent-property',
        'international-properties',
        'ready-to-move-in',
        'services/property-and-investment-consultancy',
        'services/mortgage-advisory',
        'services/relocation-service',
        'services/bank-account-opening',
        'services/business-licenses',
        'services/book-valuation',
        'mortgage-calculator',
        'golden-visa',
        'buyer-guide',
        'company-news',
        'seller-guide',
        'tenant-guide',
        'landlord-guide',
        'our-media',
        'our-agents',
        'clients-testimonials',
        'our-awards',
        'careers',
        'message-from-co-founder-and-clo',
        'message-from-co-founder-and-ceo'
    ];

    if (in_array($slug, $static_slugs)) {
        ob_start();
        ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "url": "<?= BASE_URL . $slug ?>",
      "name": "<?= htmlspecialchars($meta_tags['meta_title']) ?>",
      "description": "<?= htmlspecialchars($meta_tags['meta_description']) ?>",
      "publisher": {
        "@type": "Organization",
        "name": "<?= $home_meta['meta_title'] ?>",
        "url": "<?= BASE_URL ?>",
        "logo": {
          "@type": "ImageObject",
          "url": "<?= BASE_URL ?>images/dre-logo.png"
        },
        "sameAs": [
          "<?= $header_details['header_instagram_link'] ?>",
          "<?= $header_details['header_fb_link'] ?>",
          "<?= $header_details['header_youtube_link'] ?>",
          "https://www.linkedin.com/company/drehomes-real-estate-brokers/"
        ],
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "<?= $footer_info['footer_address'] ?>",
          "addressLocality": "Dubai",
          "addressRegion": "Dubai",
          "postalCode": "00000",
          "addressCountry": "AE"
        },
        "contactPoint": [
          {
            "@type": "ContactPoint",
            "contactType": "customer service",
            "telephone": "<?= $footer_info['footer_contact_number2'] ?>",
            "email": "<?= $footer_info['footer_email_id'] ?>",
            "areaServed": "AE",
            "availableLanguage": ["English", "Arabic"]
          }
        ]
      },
      "mainEntityOfPage": {
        "@type": "WebPage"
      },
      "image": "<?= BASE_URL_IMG . 'meta/' . $home_meta['meta_og_img'] ?>"
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
        {
          "@type": "ListItem",
          "position": <?= $index + 1 ?>,
          "name": "<?= htmlspecialchars($breadcrumb['name'], ENT_QUOTES) ?>",
          "item": "<?= $breadcrumb['url'] ?>"
        }<?= $index < count($breadcrumbs) - 1 ? ',' : '' ?>
        <?php endforeach; ?>
      ]
    }
  ]
}
</script>
<?php
        return ob_get_clean();
    }
    
    // ✅ Case: Developer Detail Page (Dynamic)
elseif (preg_match('/^developer\/(.+)$/', $slug, $matches)) {
    $developer_slug = $matches[1];
    ob_start();
    ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "name": "<?= htmlspecialchars($home_meta['meta_title']) ?>",
      "description": "<?= htmlspecialchars($home_meta['meta_description']) ?>",
      "logo": "<?= BASE_URL ?>images/dre-logo.png",
      "image": "<?= BASE_URL_IMG ?>meta/<?= $home_meta['meta_og_img'] ?>",
      "url": "<?= BASE_URL ?>",
      "sameAs": [
        "<?= $header_details['header_instagram_link'] ?>",
        "<?= $header_details['header_fb_link'] ?>",
        "<?= $header_details['header_youtube_link'] ?>",
        "https://www.linkedin.com/company/drehomes-real-estate-brokers/"
      ],
      "telephone": "<?= $footer_info['footer_contact_number2'] ?>",
      "email": "<?= $footer_info['footer_email_id'] ?>",
      "address": [
        "<?= $footer_info['footer_address'] ?>",
        "<?= $footer_info['footer_address2'] ?>"
      ]
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
        {
          "@type": "ListItem",
          "position": <?= $index + 1 ?>,
          "item": {
            "@id": "<?= $breadcrumb['url'] ?>",
            "name": "<?= htmlspecialchars($breadcrumb['name'], ENT_QUOTES) ?>"
          }
        }<?= $index < count($breadcrumbs) - 1 ? ',' : '' ?>
        <?php endforeach; ?>
      ]
    },
    {
      "@type": "Organization",
      "name": "<?= htmlspecialchars($meta_tags['meta_title']) ?>",
      "description": "<?= htmlspecialchars($meta_tags['meta_description']) ?>",
      "url": "<?= BASE_URL . $slug ?>",
      "image": "<?= BASE_URL_IMG . 'meta/' . $home_meta['meta_og_img'] ?>",
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?= BASE_URL . $slug ?>"
      }
    }
  ]
}
</script>
<?php
    return ob_get_clean();
}


    // Case 3: Blog Detail Page
    elseif (preg_match('/^our-blogs\/(.+)$/', $slug, $matches)) {
        $blog_slug = $matches[1];
        ob_start();
        ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "name": "<?= $home_meta['meta_title'] ?>",
      "url": "<?= BASE_URL ?>",
      "logo": "<?= BASE_URL ?>images/dre-logo.png",
      "description": "<?= $home_meta['meta_description'] ?>",
      "sameAs": [
        "<?= $header_details['header_instagram_link'] ?>",
        "<?= $header_details['header_fb_link'] ?>",
        "<?= $header_details['header_youtube_link'] ?>"
      ],
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "<?= $footer_info['footer_contact_number2'] ?>",
        "contactType": "sales",
        "email": "<?= $footer_info['footer_email_id'] ?>",
        "areaServed": "AE"
      },
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "<?= $footer_info['footer_address'] ?>",
        "addressLocality": "Dubai",
        "addressCountry": "AE"
      }
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Home",
          "item": "<?= BASE_URL ?>"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "Our Blogs",
          "item": "<?= BASE_URL ?>our-blogs/"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "<?= htmlspecialchars($meta_tags['meta_title']) ?>",
          "item": "<?= BASE_URL . $slug ?>"
        }
      ]
    },
    {
      "@type": "BlogPosting",
      "headline": "<?= htmlspecialchars($meta_tags['meta_title']) ?>",
      "description": "<?= htmlspecialchars($meta_tags['meta_description']) ?>",
      "image": "<?= BASE_URL_IMG . 'meta/' . $home_meta['meta_og_img'] ?>",
      "author": {
        "@type": "Organization",
        "name": "<?= $home_meta['meta_title'] ?>",
        "logo": {
          "@type": "ImageObject",
          "url": "<?= BASE_URL ?>images/dre-logo.png"
        }
      },
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?= BASE_URL . $slug ?>"
      }
    }
  ]
}
</script>
<?php
        return ob_get_clean();
    }

    // ✅ Case 3.1: Company News Single Page (Updated Dynamic)
    elseif (preg_match('/^company-news\/(.+)$/', $slug, $matches)) {
        $news_slug = $matches[1];
        ob_start();
        ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "name": "<?= $home_meta['meta_title'] ?>",
      "description": "<?= $home_meta['meta_description'] ?>",
      "logo": "<?= BASE_URL ?>images/dre-logo.png",
      "image": "<?= BASE_URL_IMG ?>meta/<?= $home_meta['meta_og_img'] ?>",
      "url": "<?= BASE_URL ?>",
      "sameAs": [
        "<?= $header_details['header_instagram_link'] ?>",
        "<?= $header_details['header_fb_link'] ?>",
        "<?= $header_details['header_youtube_link'] ?>",
        "https://www.linkedin.com/company/drehomes-real-estate-brokers/"
      ],
      "telephone": "<?= $footer_info['footer_contact_number2'] ?>",
      "email": "<?= $footer_info['footer_email_id'] ?>",
      "address": [
        "<?= $footer_info['footer_address'] ?>",
        "<?= $footer_info['footer_address2'] ?>"
      ]
    },
    {
      "@type": "BreadcrumbList",
        "itemListElement": [
          {
            "@type": "ListItem",
            "position": 1,
            "item": {
              "@id": "<?= BASE_URL ?>",
              "name": "Home"
            }
          },
          {
            "@type": "ListItem",
            "position": 2,
            "item": {
              "@id": "<?= BASE_URL ?>company-news/",
              "name": "Company News"
            }
          },
          {
            "@type": "ListItem",
            "position": 3,
            "item": {
              "@id": "<?= BASE_URL . $slug ?>",
              "name": "<?= htmlspecialchars($meta_tags['meta_title']) ?>"
            }
          }
        ]

    },
        {
      "@type": "BlogPosting",
      "headline": "<?= htmlspecialchars($meta_tags['meta_title']) ?>",
      "description": "<?= htmlspecialchars($meta_tags['meta_description']) ?>",
      "image": "<?= BASE_URL_IMG . 'meta/' . $home_meta['meta_og_img'] ?>",
      "author": {
        "@type": "Organization",
        "name": "<?= $home_meta['meta_title'] ?>",
        "logo": {
          "@type": "ImageObject",
          "url": "<?= BASE_URL ?>images/dre-logo.png"
        }
      },
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?= BASE_URL . $slug ?>"
      }
    }

]
}
</script>
<?php
        return ob_get_clean();
    }
    
elseif (preg_match('#^(property|buy-property|rent-property|international-properties|ready-to-move-in)/(.+)$#', $slug, $matches)) {
    $category = $matches[1];
    $project_slug = $matches[2];

    $project_name = !empty($proj_detail['project_name']) ? $proj_detail['project_name'] : $meta_tags['meta_title'];
    $project_description = !empty($proj_detail['project_description']) ? strip_tags($proj_detail['project_description']) : $meta_tags['meta_description'];
    $project_price = !empty($proj_detail['proj_price']) ? preg_replace('/[^0-9]/', '', $proj_detail['proj_price']) : '';
    $currency = 'AED';
    $project_keywords = !empty($meta_tags['meta_keyword']) ? $meta_tags['meta_keyword'] : '';

    // ✅ Gather images correctly
    $image_list = [];
    if (!empty($images) && is_array($images)) {
        foreach ($images as $img) {
            if (!empty($img['gallery_img'])) {
                $image_list[] = BASE_URL_IMG . 'project/gallery/' . $img['gallery_img'];
            }
        }
    }
    if (empty($image_list) && !empty($home_meta['meta_og_img'])) {
        $image_list[] = BASE_URL_IMG . 'meta/' . $home_meta['meta_og_img'];
    }

    ob_start();
    ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "name": "DRE Homes Real Estate | Dubai | Buy, Sell or Rent Property",
      "description": "We help you find a home directly with developers. We work directly with Emaar Properties, Nakheel, Danube, Meraas, Sobha Realty, Damac Properties. Off-plan and ready projects across Downtown, Palm Jumeirah, JVC, Dubai Hills, Damac Hills, and more.",
      "logo": "https://drehomes.com/admin_xcs6iwyiueuu8wd/assets/media/header/header_logo_7708_dre-logo-b.png",
      "image": "https://drehomes.com/images/dre-banner-new.jpg",
      "url": "https://drehomes.com/",
      "sameAs": [
        "https://www.instagram.com/drehomes_realestate/",
        "https://www.facebook.com/drehomesrealestateuae/",
        "https://www.youtube.com/@DrehomesRE",
        "https://www.linkedin.com/company/drehomes-real-estate-brokers/"
      ],
      "telephone": "+971527543243",
      "email": "sales@drehomes.com",
      "address": [
        "Park Heights Square 2 | Offices 401-402-405-406, Dubai Hills Estate, Dubai, AE",
        "Business Bay Tower, Office 301, Business Bay, Dubai, AE"
      ]
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
        {
          "@type": "ListItem",
          "position": <?= $index + 1 ?>,
          "item": {
            "@id": "<?= $breadcrumb['url'] ?>",
            "name": "<?= htmlspecialchars($breadcrumb['name'], ENT_QUOTES) ?>"
          }
        }<?= $index < count($breadcrumbs) - 1 ? ',' : '' ?>
        <?php endforeach; ?>
      ]
    },
    {
      "@type": "WebPage",
      "name": "<?= htmlspecialchars($project_name) ?>",
      "description": "<?= htmlspecialchars($project_description) ?>",
      "keywords": "<?= htmlspecialchars($project_keywords) ?>",
      "url": "<?= BASE_URL . $slug ?>",
      "image": [
<?= implode(",\n        ", array_map(fn($img) => '"' . $img . '"', $image_list)); ?>

      ],
      "mainEntity": {
        "@type": "Offer",
        "name": "<?= htmlspecialchars($project_name) ?>",
<?php if (!empty($project_price)): ?>
        "price": "<?= $project_price ?>",
        "priceCurrency": "<?= $currency ?>",
        "availability": "https://schema.org/InStock",
<?php endif; ?>
        "itemOffered": {
          "@type": "House",
          "name": "<?= htmlspecialchars($project_name) ?>",
          "url": "<?= BASE_URL . $slug ?>",
          "image": "<?= !empty($image_list[0]) ? $image_list[0] : '' ?>"
        },
        "offeredBy": {
          "@type": "Organization",
          "name": "DRE Homes Real Estate",
          "image": "https://drehomes.com/images/dre-banner-new.jpg"
        }
      }
    }
  ]
}
</script>
<?php
    return ob_get_clean();
}


    // Case 4: Regular dynamic project schema
    ob_start();
    ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [...]
}
</script>
<?php
    return ob_get_clean();
}


function slugify($string) {
    // Convert to lowercase
    $string = strtolower($string);

    // Remove special characters
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);

    // Replace multiple spaces or dashes with a single dash
    $string = preg_replace('/[\s-]+/', '-', $string);

    // Trim dashes from the beginning and end
    $string = trim($string, '-');

    return $string;
}

function getBreadcrumbs($proj_detail = null, $category_name = '', $category_slug = '', $slug = '') {
    $breadcrumbs = [
        [
            'name' => 'Home',
            'url' => BASE_URL,
        ],
    ];

    if ($proj_detail && $category_name && $category_slug) {
        // Project detail breadcrumbs
        $breadcrumbs[] = [
            'name' => $category_name,
            'url' => rtrim(BASE_URL, '/') . '/' . trim($category_slug, '/') . '/',
        ];
        $breadcrumbs[] = [
            'name' => $proj_detail['proj_name'],
            'url'  => rtrim(BASE_URL, '/') . '/property/' . $proj_detail['proj_slug'] . '/',
        ];
    } elseif ($slug) {
        // For non-project pages: build breadcrumb from slug parts
        $parts = explode('/', trim($slug, '/'));
        $accumulatedUrl = rtrim(BASE_URL, '/');

        foreach ($parts as $part) {
            $accumulatedUrl .= '/' . $part;
            $breadcrumbs[] = [
                'name' => ucwords(str_replace('-', ' ', $part)),
                'url' => $accumulatedUrl . '/',
            ];
        }
    }

    return $breadcrumbs;
}


// // ---------------------------------------------------------------
// // Prepared-statement helpers. Use these for every query that includes
// // a variable: put ? in the SQL and pass the values in $params.
// // Integers are bound as integers, everything else as strings.
// // ---------------------------------------------------------------
function db_query($sql, $params = [])
{
  global $conn;

  $stmt = mysqli_prepare($conn, $sql);
  if (!$stmt) {
    error_log("MySQL Prepare Error: " . mysqli_error($conn) . " | Query: $sql");
    return false;
  }

  if (!empty($params)) {
    $types = '';
    $values = [];
    foreach ($params as $param) {
      if (is_int($param) || is_bool($param)) {
        $types .= 'i';
        $values[] = (int) $param;
      } elseif (is_float($param)) {
        $types .= 'd';
        $values[] = $param;
      } else {
        $types .= 's';
        $values[] = $param === null ? null : (string) $param;
      }
    }
    mysqli_stmt_bind_param($stmt, $types, ...$values);
  }

  if (!mysqli_stmt_execute($stmt)) {
    error_log("MySQL Execute Error: " . mysqli_stmt_error($stmt) . " | Query: $sql");
    mysqli_stmt_close($stmt);
    return false;
  }

  // Rows changed by the last INSERT, UPDATE or DELETE run through db_query().
  $GLOBALS['db_affected_rows'] = mysqli_stmt_affected_rows($stmt);

  // SELECT gives a mysqli_result, so existing mysqli_fetch_* loops keep working.
  $result = mysqli_stmt_get_result($stmt);
  mysqli_stmt_close($stmt);

  return $result === false ? true : $result;
}

function db_row($sql, $params = [])
{
  $result = db_query($sql, $params);
  if (!$result || $result === true) {
    return false;
  }
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  return $row ?: false;
}

function db_all($sql, $params = [])
{
  $result = db_query($sql, $params);
  if (!$result || $result === true) {
    return [];
  }
  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }
  return $rows;
}



function select_query($tbl_name,$cond)
{
	global $conn;
    $query=mysqli_query($conn,"select * from $tbl_name where $cond");
	if($query)
	{
		return $query;
	}
	return false;
}


function truncateHeading($text, $maxLength = 60) {
    if (strlen($text) > $maxLength) {
        return substr($text, 0, $maxLength) . '...';
    }
    return $text;
}

function truncateParagraph($text, $maxLength = 180) {
    if (strlen($text) > $maxLength) {
        return substr($text, 0, $maxLength) . '...';
    }
    return $text;
}





function select_row($tbl_name, $cond)
{
    global $conn;

    $sql = "SELECT * FROM $tbl_name WHERE $cond";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        // Log the error for debugging
        error_log("MySQL Error: " . mysqli_error($conn) . " | Query: $sql");
        return false;
    }

    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    return $row ?: false;
}
function select_all($tbl_name, $cond)
{
    global $conn;

    $sql = "SELECT * FROM $tbl_name WHERE $cond";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        error_log("MySQL Error: " . mysqli_error($conn) . " | Query: $sql");
        return [];
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function select_project_detail($id, $type)
{
    global $conn;
    
    $query=mysqli_fetch_array(mysqli_query($conn,"select tp.*,td.dev_img as developer_image,td.dev_img_alt from tbl_proj as tp LEFT JOIN tbl_developer as td ON tp.prj_dev_id=td.dev_id where tp.proj_slug='$id' and tp.is_active=1 and prj_type_id=$type"));
    if($query)
    {
        return $query;
    }else{
      header('Location: '.BASE_URL.'404.php');
      exit;
    }
    return false;
}

function roundDownToHalf($number) {
     $remainder = ($number * 10) % 10;
     $half = $remainder >= 5 ? 0.5 : 0;
     $value = floatval(intval($number) + $half);
     return number_format($value, 1, '.', '');
}
function star_rating($rating)
{

    $rating_round = roundDownToHalf($rating);
   

      // Determine the number of filled stars based on the rating value
$filledStars = round($rating);


for ($i = 1; $i <= 5; $i++) {
    $starClass = ($i <= $filledStars) ? 'fa-star' : 'fa-star-o';
    $result.='<i class="fa ' . $starClass . '" aria-hidden="true"></i>';
}

return $result;

    
}



function star_home_rating($rating) {
    $fullStars = floor($rating); // Get the integer part of the rating
    $halfStar = ($rating - $fullStars) >= 0.5; // Check if there's a half star
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0); // Calculate the number of empty stars

    echo '<div class="rating">';
    
    // Display full stars
    for ($i = 0; $i < $fullStars; $i++) {
        echo '<i class="fa fa-star" aria-hidden="true" style="color:#ffd602;"></i>';
    }
    
    // Display half star (if needed)
    if ($halfStar) {
        echo '<i class="fa fa-star-half-o" aria-hidden="true" style="color:#ffd602;"></i>';
    }
    
    // Display empty stars
    for ($i = 0; $i < $emptyStars; $i++) {
        echo '<i class="fa fa-star-o" aria-hidden="true" style="color:#ffd602;"></i>';
    }
    
    echo '</div>';
}

function getDeveloper($dev_id){
  $dev_name = select_query("tbl_developer", "dev_id=" . (int)$dev_id);
  $dev_row = mysqli_fetch_assoc($dev_name);

  return $dev_row['dev_heading'] ?? ''; // ✅ return string
}


function get_client_ip()
{
  // Forwarded headers can be set by any visitor, so they are only read when the
  // request really comes from one of our own proxies (see 'trusted_proxies').
  $remote_addr = $_SERVER['REMOTE_ADDR'] ?? '';
  $trusted_proxies = (array) secret('trusted_proxies', []);

  if ($remote_addr !== '' && in_array($remote_addr, $trusted_proxies, true) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $forwarded = array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
    for ($i = count($forwarded) - 1; $i >= 0; $i--) {
      if (filter_var($forwarded[$i], FILTER_VALIDATE_IP) && !in_array($forwarded[$i], $trusted_proxies, true)) {
        return $forwarded[$i];
      }
    }
  }
  return $remote_addr;
}


function ip_details($IPaddress) 
{
    $json       = file_get_contents("http://ipinfo.io/{$IPaddress}");
    $details    = json_decode($json);
    return $details;
}


function current_url()
{
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    return $actual_link;

}



function currency_dropdown_list($key)
{
    $record='<select id="currencySelect"  name="to" data-id='.$key.'>
    <option value="AED" selected>AED</option>
    <option value="AFN">AFN</option>
    <option value="ALL">ALL</option>
    <option value="AMD">AMD</option>
    <option value="ANG">ANG</option>
    <option value="AOA">AOA</option>
    <option value="ARS">ARS</option>
    <option value="AUD">AUD</option>
    <option value="AWG">AWG</option>
    <option value="AZN">AZN</option>
    <option value="BAM">BAM</option>
    <option value="BBD">BBD</option>
    <option value="BDT">BDT</option>
    <option value="BGN">BGN</option>
    <option value="BHD">BHD</option>
    <option value="BIF">BIF</option>
    <option value="BMD">BMD</option>
    <option value="BND">BND</option>
    <option value="BOB">BOB</option>
    <option value="BRL">BRL</option>
    <option value="BSD">BSD</option>
    <option value="BTN">BTN</option>
    <option value="BWP">BWP</option>
    <option value="BYN">BYN</option>
    <option value="BZD">BZD</option>
    <option value="CAD">CAD</option>
    <option value="CDF">CDF</option>
    <option value="CHF">CHF</option>
    <option value="CLP">CLP</option>
    <option value="CNY">CNY</option>
    <option value="COP">COP</option>
    <option value="CRC">CRC</option>
    <option value="CUP">CUP</option>
    <option value="CVE">CVE</option>
    <option value="CZK">CZK</option>
    <option value="DJF">DJF</option>
    <option value="DKK">DKK</option>
    <option value="DOP">DOP</option>
    <option value="DZD">DZD</option>
    <option value="EGP">EGP</option>
    <option value="ERN">ERN</option>
    <option value="ETB">ETB</option>
    <option value="EUR">EUR</option>
    <option value="FJD">FJD</option>
    <option value="FKP">FKP</option>
    <option value="FOK">FOK</option>
    <option value="GBP">GBP</option>
    <option value="GEL">GEL</option>
    <option value="GGP">GGP</option>
    <option value="GHS">GHS</option>
    <option value="GIP">GIP</option>
    <option value="GMD">GMD</option>
    <option value="GNF">GNF</option>
    <option value="GTQ">GTQ</option>
    <option value="GYD">GYD</option>
    <option value="HKD">HKD</option>
    <option value="HNL">HNL</option>
    <option value="HRK">HRK</option>
    <option value="HTG">HTG</option>
    <option value="HUF">HUF</option>
    <option value="IDR">IDR</option>
    <option value="ILS">ILS</option>
    <option value="IMP">IMP</option>
    <option value="INR">INR</option>
    <option value="IQD">IQD</option>
    <option value="IRR">IRR</option>
    <option value="ISK">ISK</option>
    <option value="JEP">JEP</option>
    <option value="JMD">JMD</option>
    <option value="JOD">JOD</option>
    <option value="JPY">JPY</option>
    <option value="KES">KES</option>
    <option value="KGS">KGS</option>
    <option value="KHR">KHR</option>
    <option value="KID">KID</option>
    <option value="KMF">KMF</option>
    <option value="KRW">KRW</option>
    <option value="KWD">KWD</option>
    <option value="KYD">KYD</option>
    <option value="KZT">KZT</option>
    <option value="LAK">LAK</option>
    <option value="LBP">LBP</option>
    <option value="LKR">LKR</option>
    <option value="LRD">LRD</option>
    <option value="LSL">LSL</option>
    <option value="LYD">LYD</option>
    <option value="MAD">MAD</option>
    <option value="MDL">MDL</option>
    <option value="MGA">MGA</option>
    <option value="MKD">MKD</option>
    <option value="MMK">MMK</option>
    <option value="MNT">MNT</option>
    <option value="MOP">MOP</option>
    <option value="MRU">MRU</option>
    <option value="MUR">MUR</option>
    <option value="MVR">MVR</option>
    <option value="MWK">MWK</option>
    <option value="MXN">MXN</option>
    <option value="MYR">MYR</option>
    <option value="MZN">MZN</option>
    <option value="NAD">NAD</option>
    <option value="NGN">NGN</option>
    <option value="NIO">NIO</option>
    <option value="NOK">NOK</option>
    <option value="NPR">NPR</option>
    <option value="NZD">NZD</option>
    <option value="OMR">OMR</option>
    <option value="PAB">PAB</option>
    <option value="PEN">PEN</option>
    <option value="PGK">PGK</option>
    <option value="PHP">PHP</option>
    <option value="PKR">PKR</option>
    <option value="PLN">PLN</option>
    <option value="PYG">PYG</option>
    <option value="QAR">QAR</option>
    <option value="RON">RON</option>
    <option value="RSD">RSD</option>
    <option value="RUB">RUB</option>
    <option value="RWF">RWF</option>
    <option value="SAR">SAR</option>
    <option value="SBD">SBD</option>
    <option value="SCR">SCR</option>
    <option value="SDG">SDG</option>
    <option value="SEK">SEK</option>
    <option value="SGD">SGD</option>
    <option value="SHP">SHP</option>
    <option value="SLE">SLE</option>
    <option value="SLL">SLL</option>
    <option value="SOS">SOS</option>
    <option value="SRD">SRD</option>
    <option value="SSP">SSP</option>
    <option value="STN">STN</option>
    <option value="SYP">SYP</option>
    <option value="SZL">SZL</option>
    <option value="THB">THB</option>
    <option value="TJS">TJS</option>
    <option value="TMT">TMT</option>
    <option value="TND">TND</option>
    <option value="TOP">TOP</option>
    <option value="TRY">TRY</option>
    <option value="TTD">TTD</option>
    <option value="TVD">TVD</option>
    <option value="TWD">TWD</option>
    <option value="TZS">TZS</option>
    <option value="UAH">UAH</option>
    <option value="UGX">UGX</option>
    <option value="USD">USD</option>
    <option value="UYU">UYU</option>
    <option value="UZS">UZS</option>
    <option value="VES">VES</option>
    <option value="VND">VND</option>
    <option value="VUV">VUV</option>
    <option value="WST">WST</option>
    <option value="XAF">XAF</option>
    <option value="XCD">XCD</option>
    <option value="XDR">XDR</option>
    <option value="XOF">XOF</option>
    <option value="XPF">XPF</option>
    <option value="YER">YER</option>
    <option value="ZAR">ZAR</option>
    <option value="ZMW">ZMW</option>
    <option value="ZWL">ZWL</option>
</select>
';

return $record;

}

/**
 * Page speed helpers.
 *
 * dre_img() returns the URL of a smaller copy of a site image, made once and
 * kept in images/cache/. The copy is WebP when this PHP build can write it,
 * otherwise JPEG (or PNG when the picture has transparency). Anything it cannot handle (remote URL, missing
 * file, cache folder not writable) comes back unchanged, so the page never breaks.
 */
function dre_img($url, $max_width = 800)
{
    static $root = null, $cache_ok = null;
    if ($root === null) {
        $root = dirname(__DIR__) . '/';
        $cache_ok = function_exists('imagecreatefromjpeg') && is_dir($root . 'images/cache') && is_writable($root . 'images/cache');
    }
    if (!$cache_ok || !is_string($url) || $url === '') {
        return $url;
    }
    $rel = (strpos($url, BASE_URL) === 0) ? substr($url, strlen(BASE_URL)) : $url;
    $rel = ltrim(rawurldecode(strtok($rel, '?')), '/');
    if (preg_match('#^[a-z]+://#i', $rel) || strpos($rel, '..') !== false) {
        return $url;
    }
    $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        return $url;
    }
    $src = $root . $rel;
    if (!is_file($src)) {
        return $url;
    }
    $webp = function_exists('imagewebp');
    $key = md5($rel . '|' . filemtime($src) . '|' . (int) $max_width);
    $dir = $root . 'images/cache/';
    $name = '';
    foreach ($webp ? ['webp'] : ['jpg', 'png'] as $try) {
        if (is_file($dir . $key . '.' . $try)) {
            $name = $key . '.' . $try;
            break;
        }
    }
    $out = $dir . $name;
    if ($name === '') {
        $info = @getimagesize($src);
        // Skip very large pictures: decoding them needs more memory than a page request should use
        if (!$info || $info[0] * $info[1] > 30000000) {
            return $url;
        }
        $im = ($info[2] === IMAGETYPE_PNG) ? @imagecreatefrompng($src) : (($info[2] === IMAGETYPE_JPEG) ? @imagecreatefromjpeg($src) : false);
        if (!$im) {
            return $url;
        }
        imagepalettetotruecolor($im);
        if ($info[0] > $max_width) {
            // imagecopyresampled works on every PHP build; imagescale() fails on some of them.
            $height = max(1, (int) round($info[1] * $max_width / $info[0]));
            $scaled = imagecreatetruecolor((int) $max_width, $height);
            imagealphablending($scaled, false);
            imagesavealpha($scaled, true);
            if (imagecopyresampled($scaled, $im, 0, 0, 0, 0, (int) $max_width, $height, $info[0], $info[1])) {
                imagedestroy($im);
                $im = $scaled;
            } else {
                imagedestroy($scaled);
            }
        }
        imagealphablending($im, false);
        imagesavealpha($im, true);
        $type = 'webp';
        if (!$webp) {
            // JPEG has no transparency, so a PNG that uses it stays a PNG
            $type = 'jpg';
            if ($info[2] === IMAGETYPE_PNG) {
                $probe = imagescale($im, 24, 24, IMG_NEAREST_NEIGHBOUR);
                imagealphablending($probe, false);
                for ($y = 0; $y < 24 && $type === 'jpg'; $y++) {
                    for ($x = 0; $x < 24; $x++) {
                        if ((imagecolorat($probe, $x, $y) >> 24) & 0x7F) {
                            $type = 'png';
                            break;
                        }
                    }
                }
                imagedestroy($probe);
            }
        }
        $name = $key . '.' . $type;
        $out = $dir . $name;
        $tmp = $out . '.' . getmypid() . '.tmp';
        if ($type === 'webp') {
            $saved = @imagewebp($im, $tmp, 80);
        } elseif ($type === 'jpg') {
            imageinterlace($im, true);
            $saved = @imagejpeg($im, $tmp, 80);
        } else {
            $saved = @imagepng($im, $tmp, 9);
        }
        imagedestroy($im);
        if (!$saved || !@rename($tmp, $out)) {
            @unlink($tmp);
            return $url;
        }
        // Keep the original when the copy did not come out smaller
        if (filesize($out) >= filesize($src) && $info[0] <= $max_width) {
            @file_put_contents($out . '.skip', '');
        }
    }
    if (is_file($out . '.skip')) {
        return $url;
    }
    return BASE_URL . 'images/cache/' . $name;
}

/** Local css/js URL with the file's change time appended, so browsers can cache it for long. */
function dre_asset($rel)
{
    $file = dirname(__DIR__) . '/' . $rel;
    return BASE_URL . $rel . (is_file($file) ? '?v=' . filemtime($file) : '');
}

/* ---- New UI helpers (used by common/header.php, common/footer.php and the pages) ---- */

// Escape text for HTML; admin text is often stored with entities (&amp;), so decode first
if (!function_exists('e')) {
	function e($s)
	{
		return htmlspecialchars(trim(html_entity_decode((string) $s, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8');
	}
}

// Clean link to a page of the new UI (/page, served by page.php or page.html through .htaccess);
// when the new UI has no such page yet, the old site path is used
// (e.g. page_link('mortgage-advisory', 'services/mortgage-advisory'))
if (!function_exists('page_link')) {
	function page_link($page, $fallback_path = null)
	{
		$page = trim($page);
		$root = dirname(__DIR__) . '/';
		if (is_file($root . $page . '.php') || is_file($root . $page . '.html')) {
			return BASE_URL . $page;
		}
		return BASE_URL . ltrim($fallback_path ?? $page, '/');
	}
}

// Asset path relative to the site root, so it also works on nested URLs
if (!function_exists('asset_url')) {
	function asset_url($path)
	{
		return preg_match('#^(https?:)?//#', $path) ? $path : BASE_URL . ltrim($path, '/');
	}
}

/* =====================================================================
   New UI: shared helpers (rule.md 9.1a — pages call these, they never define their own)
   ===================================================================== */

// SEO row from Admin > Manage Tags (tbl_tags), or [] when it is missing or switched off
function get_seo_tags($id)
{
	return db_row("SELECT * FROM tbl_tags WHERE id = ? AND is_active = '1'", [(int) $id]) ?: [];
}

// Admin rich text (TinyMCE HTML) -> plain text: tags removed, entities decoded, spaces collapsed
function plain_text($html)
{
	$text = str_replace(['<br>', '<br/>', '<br />', '</p>'], ' ', (string) $html);
	$text = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');
	return trim(preg_replace('/\s+/u', ' ', str_replace("\xC2\xA0", ' ', $text)));
}

// Admin rich text -> its <p> paragraphs as plain text (empty ones dropped)
function text_paragraphs($html)
{
	preg_match_all('#<p[^>]*>(.*?)</p>#is', (string) $html, $m);
	return array_values(array_filter(array_map('plain_text', $m[1] ?: [(string) $html])));
}

// "Palace Residences Hillside" -> "Palace Residences <em class="gold-text">Hillside</em>" (escaped)
function gold_last_word($text, $lowercase_last = false)
{
	$words = preg_split('/\s+/u', trim((string) $text), -1, PREG_SPLIT_NO_EMPTY);
	if (count($words) < 2) {
		return e($text);
	}
	$last = array_pop($words);
	return e(implode(' ', $words)) . ' <em class="gold-text">' . e($lowercase_last ? mb_strtolower($last) : $last) . '</em>';
}

// URL of a project image uploaded in the admin (project/<folder>/<file>), or '' when not set or the file is missing
function project_media_url($folder, $file)
{
	$file = trim((string) $file);
	$path = dirname(__DIR__) . '/admin_nsrdwsc/assets/media/project/' . $folder . '/' . $file;
	if ($file === '' || !is_file($path)) {
		return '';
	}
	return BASE_URL_IMG . 'project/' . $folder . '/' . rawurlencode($file);
}

// Price typed in the admin -> number: "1678000", "777,777", "AED 6,795,888", "1.81 Million", "13.47 Mn".
// "Call Us", "Announcing soon", empty -> 0 (shown as "Price on request")
function parse_price($raw)
{
	$text = preg_replace('/^aed+/', '', strtolower(str_replace([',', ' '], '', trim((string) $raw))));
	if (preg_match('/^(\d+(?:\.\d+)?)(m|mn|million+|millionn)$/', $text, $m)) {
		return (int) round($m[1] * 1000000);
	}
	if (preg_match('/^(\d+(?:\.\d+)?)(k|thousand)$/', $text, $m)) {
		return (int) round($m[1] * 1000);
	}
	return ctype_digit($text) ? (int) $text : 0;
}

// "AED 1,710,000", or '' for 0
function price_label($price)
{
	return $price ? 'AED ' . number_format($price) : '';
}

// 1.0 -> "1", 1.5 -> "1.5"
function bed_number($value)
{
	return rtrim(rtrim(number_format((float) $value, 1, '.', ''), '0'), '.');
}

// Bedrooms "1,2,STD" -> [0, 1, 2] (0 = studio), sorted, no duplicates
function bedroom_values($raw)
{
	$values = [];
	foreach (explode(',', (string) $raw) as $part) {
		$part = strtoupper(trim($part));
		if (in_array($part, ['STD', 'STUDIO'], true)) {
			$values[] = 0;
		} elseif ($part !== '' && is_numeric($part)) {
			$values[] = (float) $part;
		}
	}
	$values = array_values(array_unique($values, SORT_REGULAR));
	sort($values);
	return $values;
}

// Card label: the admin's "bedrooms on card" text, else "Studio, 1 - 3", else "—"
function bedroom_range_label($raw, $on_card = '')
{
	if (trim((string) $on_card) !== '') {
		return trim($on_card);
	}
	$beds = bedroom_values($raw);
	if (!$beds) {
		return '—';
	}
	$rooms = array_values(array_filter($beds, function ($b) {
		return $b > 0;
	}));
	$parts = in_array(0, $beds) ? ['Studio'] : [];
	if ($rooms) {
		$parts[] = count($rooms) > 1 ? bed_number(min($rooms)) . ' - ' . bed_number(max($rooms)) : bed_number($rooms[0]);
	}
	return implode(', ', $parts);
}

// [0, 1, 2, 3] -> "Studio, 1, 2 & 3"
function bedroom_list_words($beds)
{
	$words = array_map(function ($b) {
		return $b == 0 ? 'Studio' : bed_number($b);
	}, $beds);
	return count($words) > 1 ? implode(', ', array_slice($words, 0, -1)) . ' & ' . end($words) : implode('', $words);
}

// First UAE emirate named in the given texts ("Dubai", "Siniya Island" -> "Umm Al Quwain"), or ''
function find_emirate(...$texts)
{
	$emirates = ['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Umm Al Quwain', 'Ras Al Khaimah', 'Fujairah'];
	$aliases = ['ras al khimah' => 'Ras Al Khaimah', 'rak' => 'Ras Al Khaimah', 'siniya island' => 'Umm Al Quwain', 'uaq' => 'Umm Al Quwain'];
	foreach ($texts as $text) {
		$text = trim((string) $text);
		if ($text === '') {
			continue;
		}
		foreach ($emirates as $emirate) {
			if (stripos($text, $emirate) !== false) {
				return $emirate;
			}
		}
		foreach ($aliases as $alias => $emirate) {
			if (preg_match('/\b' . preg_quote($alias, '/') . '\b/i', $text)) {
				return $emirate;
			}
		}
	}
	return '';
}

// "Emaar Properties" -> "Emaar" (for short buttons and headings)
function short_developer_name($name)
{
	return trim(preg_replace('/\s+(properties|developers?|developments?|realty|group|holding)$/i', '', (string) $name)) ?: (string) $name;
}

// WhatsApp link to DRE with a ready message about a project
function project_whatsapp_url($name, $developer = '', $location = '')
{
	$text = "Hi DRE, I'm interested in $name" . ($developer !== '' ? " by $developer" : '') . ($location !== '' ? " ($location)" : '') . '.';
	return 'https://wa.me/971585837346?text=' . rawurlencode($text);
}

// Small inline icons used by the new UI cards
function svg_icon($name)
{
	$icons = [
		'pin' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 00-7 7c0 5.3 7 13 7 13s7-7.7 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"></path></svg>',
		'whatsapp' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"></path></svg>',
	];
	return $icons[$name] ?? '';
}

// Project amenity: the admin's Font Awesome name -> the design's line icon (inner SVG markup)
function amenity_icon_svg($fa_icon)
{
	$icons = [
		'fa-dumbbell' => '<path d="M3 10v4M6 7v10M18 7v10M21 10v4M6 12h12"></path>',
		'fa-child-reaching' => '<circle cx="12" cy="5" r="2"></circle><path d="M12 7v6M8 21l4-8 4 8M7 10h10"></path>',
		'fa-tree' => '<path d="M12 22v-6M7 16h10l-5-12z"></path>',
		'fa-baby' => '<path d="M4 20h16M6 20V9l6-5 6 5v11"></path><circle cx="12" cy="13" r="2"></circle>',
		'fa-store' => '<path d="M5 8h14l-1 12H6zM9 8a3 3 0 016 0"></path>',
		'fa-school' => '<path d="M2 9l10-5 10 5-10 5z"></path><path d="M6 11v5c3 2 9 2 12 0v-5"></path>',
		'fa-person-swimming' => '<path d="M2 18c2 0 2-1.5 4-1.5s2 1.5 4 1.5 2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5M8 15V5a2 2 0 014 0M16 15V5a2 2 0 00-4 0M8 9h8"></path>',
		'fa-utensils' => '<path d="M7 3v8a2 2 0 002 2v8M7 3v4M11 3v4M9 3v4M17 3c-2 2-2 6 0 8v10"></path>',
	];
	return $icons[trim((string) $fa_icon)] ?? '<circle cx="12" cy="12" r="8"></circle><path d="M9 12l2 2 4-4"></path>';
}

/* =====================================================================
   New UI: off-plan projects (offplan-properties.php, single-property.php)
   ===================================================================== */

// Admin > Off Plan > Off Plan Content (banner image)
function get_offplan_content()
{
	return db_row("SELECT * FROM tbl_offplan_content WHERE is_active = '1' LIMIT 1") ?: [];
}

// Admin > Projects: every published off-plan project (type 1) as a listing card.
// Featured first, then newest (the page's "Featured" sort keeps this order).
function get_offplan_projects()
{
	$rows = db_all(
		"SELECT p.proj_name, p.proj_name_on_card, p.proj_slug, p.prj_thumb_img,
		        COALESCE(f.price_aed, p.proj_price) AS proj_price,
		        p.proj_bedroom, p.proj_bedroom_on_card, p.proj_location, p.emirate,
		        d.dev_heading, c.comm_location
		 FROM tbl_proj p
		 LEFT JOIN tbl_developer d ON d.dev_id = p.prj_dev_id
		 LEFT JOIN tbl_community c ON c.comm_id = p.prj_comm_id
		 LEFT JOIN tbl_ai_proj_facts f ON f.prj_id = p.prj_id
		 WHERE p.prj_type_id = 1 AND p.is_active = '1'
		 ORDER BY p.proj_markfeat = '1' DESC, p.proj_posted_on DESC, p.prj_id DESC"
	);
	$projects = [];
	foreach ($rows as $row) {
		$name_on_card = trim((string) ($row['proj_name_on_card'] ?? ''));
		$proj_name = trim((string) ($row['proj_name'] ?? ''));
		$name = $name_on_card !== '' ? $name_on_card : $proj_name;
		$developer = trim((string) ($row['dev_heading'] ?? ''));
		$beds = bedroom_values($row['proj_bedroom'] ?? '');
		// The page's JS needs a list only when the bedrooms are not one continuous range ("2, 4")
		$whole = array_filter($beds, function ($b) {
			return $b == floor($b);
		});
		$is_range = $beds && count($whole) === count($beds) && (max($beds) - min($beds) + 1) === count($beds);
		$price = parse_price($row['proj_price'] ?? 0);
		$thumb_raw = trim((string) ($row['prj_thumb_img'] ?? ''));
		$first_thumb = $thumb_raw !== '' ? trim(explode(',', $thumb_raw)[0]) : '';
		$thumb_url = $first_thumb !== '' ? BASE_URL_IMG . 'project/thumb/' . rawurlencode($first_thumb) : '';

		$projects[] = [
			'name' => $name,
			'slug' => trim((string) ($row['proj_slug'] ?? '')),
			'url' => BASE_URL . 'property/' . rawurlencode(trim((string) ($row['proj_slug'] ?? ''))),
			'img' => $thumb_url,
			'developer' => $developer,
			'area' => trim(preg_replace('/\s+/', ' ', (string) ($row['proj_location'] ?? ''))),
			'emirate' => find_emirate($row['emirate'] ?? '', $row['comm_location'] ?? '', $row['proj_location'] ?? ''),
			'price' => $price,
			'price_label' => price_label($price),
			'beds' => $beds,
			'bed_min' => $beds ? bed_number(min($beds)) : '',
			'bed_max' => $beds ? bed_number(max($beds)) : '',
			'bed_list' => $beds && !$is_range ? implode(' ', array_map('bed_number', $beds)) : '',
			'bed_label' => bedroom_range_label($row['proj_bedroom'] ?? '', $row['proj_bedroom_on_card'] ?? ''),
			'whatsapp' => project_whatsapp_url($name, $developer),
		];
	}
	return $projects;
}

// Filter options for the off-plan listing, built from the projects themselves
function get_offplan_filters(array $projects)
{
	$count_by = function ($key) use ($projects) {
		$counts = array_count_values(array_filter(array_column($projects, $key), function ($v) {
			return $v !== '';
		}));
		arsort($counts);
		return $counts;
	};
	$developers = $count_by('developer');
	$areas = array_keys($count_by('area'));
	natcasesort($areas);
	$beds = [];
	foreach ($projects as $project) {
		foreach ($project['beds'] as $b) {
			$beds[(string) $b] = ['value' => $b == 0 ? 'STD' : bed_number($b), 'label' => $b == 0 ? 'Studio' : bed_number($b)];
		}
	}
	ksort($beds, SORT_NUMERIC);
	$quick = [];
	foreach (array_slice($developers, 0, 6, true) as $developer => $count) {
		$quick[] = ['name' => $developer, 'short' => short_developer_name($developer), 'count' => $count];
	}
	return [
		'total' => count($projects),
		'quick_developers' => $quick,
		'developers' => array_keys($developers),
		'emirates' => array_keys($count_by('emirate')),
		'areas' => array_values($areas),
		'beds' => array_values($beds),
		'prices' => [500000, 750000, 1000000, 2000000, 3000000, 4000000, 5000000, 6000000, 7000000, 8000000, 9000000, 10000000, 15000000, 25000000],
	];
}

// One off-plan project for the detail page (/property/<slug>), everything the page prints, or [] when not found
function get_offplan_project($slug)
{
	$slug = trim(str_replace('/', '', (string) $slug));
	if ($slug === '') {
		return [];
	}
	$project = db_row(
		"SELECT p.*, d.dev_heading, d.dev_slug, d.dev_img, c.comm_location
		 FROM tbl_proj p
		 LEFT JOIN tbl_developer d ON d.dev_id = p.prj_dev_id
		 LEFT JOIN tbl_community c ON c.comm_id = p.prj_comm_id
		 WHERE TRIM(p.proj_slug) = ? AND p.prj_type_id = 1 AND p.is_active = '1'
		 ORDER BY p.prj_id DESC LIMIT 1",
		[$slug]
	);
	if (!$project) {
		return [];
	}
	$id = (int) $project['prj_id'];
	$name = trim($project['proj_name_on_card']) ?: trim($project['proj_name']);
	$developer = trim((string) $project['dev_heading']);
	$location = trim((string) $project['proj_location']);
	$emirate = trim((string) $project['comm_location']);
	$price = parse_price($project['proj_price']);
	$beds = bedroom_values($project['proj_bedroom']);
	$show_beds = $project['proj_show_bedroom'] !== '2' && $beds;

	// Payment plan: the admin's text, else the milestones ("15% / 45% / 40%")
	$payment_plan = '';
	if ($project['proj_show_payment_plan'] !== '2') {
		$payment_plan = plain_text($project['proj_pplan_cont']);
		if ($payment_plan === '') {
			$milestones = db_all("SELECT payplan_percentage FROM tbl_proj_payplan WHERE proj_id = ? AND is_active = '1' ORDER BY proj_payplan_id ASC", [$id]);
			$payment_plan = implode(' / ', array_map('trim', array_column($milestones, 'payplan_percentage')));
		}
		$payment_plan = ucfirst(mb_strtolower($payment_plan));
	}

	// Photos: gallery, then location map and masterplan (the lightbox shows them in this order)
	$photos = [];
	foreach (db_all("SELECT gallery_img, gallery_img_alt FROM tbl_proj_gallery WHERE proj_id = ? AND is_active = '1' ORDER BY proj_gallery_id ASC", [$id]) as $g) {
		$src = project_media_url('gallery', $g['gallery_img']);
		if ($src !== '') {
			$photos[] = ['src' => $src, 'title' => plain_text($g['gallery_img_alt']) ?: $name];
		}
	}
	$location_img = project_media_url('location', $project['proj_loc_img']);
	$masterplan_img = project_media_url('masterplan', $project['proj_mastrpln_img']);
	$location_img_index = $location_img ? count($photos) : null;
	if ($location_img) {
		$photos[] = ['src' => $location_img, 'title' => 'Location map'];
	}
	$masterplan_img_index = $masterplan_img ? count($photos) : null;
	if ($masterplan_img) {
		$photos[] = ['src' => $masterplan_img, 'title' => 'Masterplan'];
	}
	if (!$photos) {
		$photos[] = ['src' => project_media_url('thumb', explode(',', $project['prj_thumb_img'])[0]), 'title' => $name];
	}
	// Hero: the first photo big; beside it the second photo and the masterplan (or the last photo)
	$side = [];
	if (count($photos) > 1) {
		$side_indexes = [1];
		if ($masterplan_img_index !== null && $masterplan_img_index > 1) {
			$side_indexes[] = $masterplan_img_index;
		} elseif (count($photos) > 2) {
			$side_indexes[] = count($photos) - 1;
		}
		foreach ($side_indexes as $n => $i) {
			$side[] = $photos[$i] + ['index' => $i, 'show_all' => $n === count($side_indexes) - 1];
		}
	}

	// Amenities chosen for the project
	$amenity_ids = array_filter(array_map('intval', explode(',', (string) $project['proj_fea_fac_opt'])));
	$amenities = [];
	if ($amenity_ids) {
		foreach (db_all('SELECT featfac_name, featfac_faicon FROM tbl_proj_featfac WHERE is_active = 1 AND featfac_id IN (' . implode(',', $amenity_ids) . ') ORDER BY featfac_name') as $a) {
			$amenities[] = ['name' => $a['featfac_name'], 'icon' => amenity_icon_svg($a['featfac_faicon'])];
		}
	}

	// Logo: the project's own logo, else the developer's (only when the file exists)
	$logo = project_media_url('dev', $project['proj_dev_img']);
	$dev_logo = trim((string) $project['dev_img']);
	if ($logo === '' && $dev_logo !== '' && is_file(dirname(__DIR__) . '/admin_nsrdwsc/assets/media/developer/' . $dev_logo)) {
		$logo = BASE_URL_IMG . 'developer/' . rawurlencode($dev_logo);
	}

	$overview = text_paragraphs($project['proj_overview']);
	$amenities_text = text_paragraphs($project['proj_fea_fac_cont']);
	$location_text = text_paragraphs($project['proj_loc_cont']);
	$masterplan_text = text_paragraphs($project['proj_masterplan']);
	$map_url = trim((string) $project['proj_map']);
	if (!preg_match('#^https://www\.google\.com/maps/embed\?#', $map_url)) {
		$map_url = '';
	}

	// More from the same developer
	$similar = [];
	$similar_rows = db_all(
		"SELECT proj_name, proj_name_on_card, proj_slug, prj_thumb_img, proj_price, proj_bedroom, proj_bedroom_on_card, proj_location, proj_handover
		 FROM tbl_proj WHERE prj_dev_id = ? AND prj_id <> ? AND prj_type_id = 1 AND is_active = '1'
		 ORDER BY proj_markfeat = '1' DESC, proj_posted_on DESC LIMIT 8",
		[(int) $project['prj_dev_id'], $id]
	);
	foreach ($similar_rows as $s) {
		$s_handover = trim(preg_replace('/^\s*handover\s*:?\s*/i', '', (string) $s['proj_handover']));
		$similar[] = [
			'name' => trim($s['proj_name_on_card']) ?: trim($s['proj_name']),
			'url' => BASE_URL . 'property/' . rawurlencode(trim($s['proj_slug'])),
			'img' => project_media_url('thumb', explode(',', $s['prj_thumb_img'])[0]),
			'location' => trim((string) $s['proj_location']),
			'badge' => $s_handover !== '' ? 'Handover: ' . $s_handover : 'Off plan',
			'beds' => bedroom_range_label($s['proj_bedroom'], $s['proj_bedroom_on_card']),
			'price' => price_label(parse_price($s['proj_price'])),
		];
	}

	return [
		'meta' => $project, // the project's own SEO fields for the header
		'slug' => trim($project['proj_slug']),
		'url' => BASE_URL . 'property/' . rawurlencode(trim($project['proj_slug'])),
		'name' => $name,
		'title_html' => gold_last_word($name),
		'overview_title_html' => gold_last_word(trim($project['proj_sub_heading']) ?: $name),
		'developer' => $developer,
		'developer_short' => short_developer_name($developer),
		'developer_url' => $project['dev_slug'] ? BASE_URL . 'developer/' . rawurlencode($project['dev_slug']) : '',
		'location' => $location,
		'place' => $location . ($emirate !== '' && $emirate !== 'UAE' && stripos($location, $emirate) === false ? ', ' . $emirate : ''),
		'price' => $price,
		'price_label' => price_label($price),
		'bedrooms' => $show_beds ? bedroom_list_words($beds) : '',
		'bedrooms_list' => $show_beds ? implode(', ', array_map(function ($b) {
			return $b == 0 ? 'Studio' : bed_number($b);
		}, $beds)) : '',
		'type' => trim(html_entity_decode((string) $project['proj_inventory_type'], ENT_QUOTES, 'UTF-8')),
		'handover' => $project['proj_show_handover'] !== '2' ? trim(preg_replace('/^\s*handover\s*:?\s*/i', '', (string) $project['proj_handover'])) : '',
		'payment_plan' => $payment_plan,
		'photos' => $photos,
		'side_photos' => $side,
		'location_img' => $location_img,
		'location_img_index' => $location_img_index,
		'masterplan_img' => $masterplan_img,
		'masterplan_img_index' => $masterplan_img_index,
		'overview' => $overview,
		'amenities' => $amenities,
		'amenities_text' => $amenities_text,
		'location_text' => $location_text,
		'masterplan_text' => $masterplan_text,
		'map_url' => $map_url,
		'logo' => $logo,
		'whatsapp' => project_whatsapp_url($name, $developer, $location),
		// Section tabs: only the sections this project has
		'sections' => array_filter([
			'overview' => $overview ? 'Overview' : '',
			'amenities' => ($amenities || $amenities_text) ? 'Amenities' : '',
			'location' => ($location_text || $location_img || $map_url) ? 'Location' : '',
			'masterplan' => ($masterplan_img || $masterplan_text) ? 'Masterplan' : '',
			'mortgage' => 'Mortgage',
			'enquire' => 'Enquire',
		]),
		'similar' => $similar,
	];
}

/* =====================================================================
   New UI: Buy properties (buy-property.php)
   Admin > Buy Property (manage-buy-project.php, edit-tags.php?edit=MTk=)
   ===================================================================== */

// Admin > Buy Property > Buy Property Content (banner image)
function get_buy_content()
{
	return db_row("SELECT * FROM tbl_buyproperty_content WHERE is_active = '1' LIMIT 1") ?: [];
}

function buy_property_clean_community($comm_heading, $proj_location)
{
	$loc = trim((string)$comm_heading);
	if ($loc === '') {
		$loc = trim((string)$proj_location);
	}
	$loc = preg_replace('/,\s*Dubai.*$/i', '', $loc);
	$loc = preg_replace('/\s+Dubai$/i', '', $loc);
	$loc = preg_replace('/\s*\(IMPZ\)/i', '', $loc);
	$loc = trim(explode(',', $loc)[0]);
	if (strcasecmp($loc, 'Town Square') === 0) return 'Town Square';
	if (strcasecmp($loc, 'JVC') === 0) return 'Jumeirah Village Circle';
	if (strcasecmp($loc, 'Damac Hills 2') === 0) return 'Damac Hills 2';
	if (strcasecmp($loc, 'Damac Hills') === 0) return 'Damac Hills';
	return $loc !== '' ? $loc : 'Dubai';
}

function buy_property_type($proj_name, $proj_slug, $property_type_col = '')
{
	$col = trim((string)$property_type_col);
	if ($col !== '') {
		if (stripos($col, 'townhouse') !== false) return 'Townhouse';
		if (stripos($col, 'villa') !== false) return 'Villa';
		if (stripos($col, 'penthouse') !== false) return 'Penthouse';
		if (stripos($col, 'apartment') !== false) return 'Apartment';
		return ucfirst(strtolower($col));
	}
	$str = strtolower($proj_name . ' ' . $proj_slug);
	if (strpos($str, 'townhouse') !== false) return 'Townhouse';
	if (strpos($str, 'villa') !== false) return 'Villa';
	if (strpos($str, 'penthouse') !== false) return 'Penthouse';
	return 'Apartment';
}

function buy_property_whatsapp_url($name, $price = 0, $url = '')
{
	$price_str = $price > 0 ? ' (AED ' . number_format($price) . ')' : '';
	$link_str = $url !== '' ? ' — ' . $url : '';
	$text = "Hi DRE, I'm interested in: " . $name . $price_str . $link_str;
	return 'https://wa.me/971585837346?text=' . rawurlencode($text);
}

// Admin > Manage Buy Projects (prj_type_id = 7, is_active = '1')
function get_buy_projects()
{
	$rows = db_all(
		"SELECT p.prj_id, p.proj_name, p.proj_name_on_card, p.proj_slug, p.prj_thumb_img,
		        COALESCE(f.price_aed, p.proj_price, p.proj_start_price) AS proj_price,
		        p.proj_bedroom, p.proj_bedroom_on_card, p.proj_shower, p.proj_area, p.proj_size,
		        p.proj_location, p.property_type, p.proj_markfeat,
		        c.comm_heading
		 FROM tbl_proj p
		 LEFT JOIN tbl_community c ON c.comm_id = p.prj_comm_id
		 LEFT JOIN tbl_ai_proj_facts f ON f.prj_id = p.prj_id
		 WHERE p.prj_type_id = 7 AND p.is_active = '1'
		 ORDER BY p.proj_markfeat = '1' DESC, p.proj_posted_on DESC, p.prj_id DESC"
	);

	$projects = [];
	foreach ($rows as $row) {
		$name_on_card = trim((string) ($row['proj_name_on_card'] ?? ''));
		$proj_name = trim((string) ($row['proj_name'] ?? ''));
		$name = $name_on_card !== '' ? $name_on_card : $proj_name;
		$slug = trim((string) ($row['proj_slug'] ?? ''));
		$url = BASE_URL . 'buy-property/' . rawurlencode($slug);

		$price = (int) ($row['proj_price'] ?? 0);
		$area = (int) ($row['proj_area'] ?: ($row['proj_size'] ?? 0));
		$ppf = ($area > 0 && $price > 0) ? round($price / $area) : 0;

		$baths = (int) ($row['proj_shower'] ?? 0);
		$bed_raw = trim((string) ($row['proj_bedroom'] ?? ''));
		$is_studio = (strcasecmp($bed_raw, 'STD') === 0 || strcasecmp($bed_raw, 'Studio') === 0 || $bed_raw === '0');
		$beds_num = $is_studio ? 0 : (int) $bed_raw;
		$beds_label = $is_studio ? 'Studio' : ($beds_num > 0 ? $beds_num . ' bed' : '1 bed');

		$type = buy_property_type($proj_name, $slug, $row['property_type'] ?? '');
		$community = buy_property_clean_community($row['comm_heading'] ?? '', $row['proj_location'] ?? '');
		$location = trim((string) ($row['proj_location'] ?? ''));
		if ($location === '') $location = $community;

		$thumbs_raw = trim((string) ($row['prj_thumb_img'] ?? ''));
		$images = [];
		if ($thumbs_raw !== '') {
			foreach (explode(',', $thumbs_raw) as $thumb) {
				$t = trim($thumb);
				if ($t !== '') {
					$images[] = BASE_URL_IMG . 'project/thumb/' . rawurlencode($t);
				}
			}
		}
		if (empty($images)) {
			$images[] = BASE_URL . 'admin_nsrdwsc/assets/media/logos/logo-light.png';
		}

		$is_featured = ($row['proj_markfeat'] == '1');

		$projects[] = [
			'id' => (int) $row['prj_id'],
			'name' => $name,
			'slug' => $slug,
			'url' => $url,
			'images' => $images,
			'type' => $type,
			'community' => $community,
			'location' => $location,
			'price' => $price,
			'price_label' => 'AED ' . number_format($price),
			'beds_num' => $beds_num,
			'beds_label' => $beds_label,
			'baths_num' => $baths,
			'baths_label' => $baths . ' bath',
			'area' => $area,
			'area_label' => number_format($area) . ' sq ft',
			'ppf' => $ppf,
			'ppf_label' => 'AED ' . number_format($ppf) . ' / sq ft',
			'is_featured' => $is_featured,
			'whatsapp' => buy_property_whatsapp_url($name, $price, $url),
		];
	}
	return $projects;
}

// Filter options for the buy properties listing, built from the projects themselves
function get_buy_filters(array $projects)
{
	$comm_counts = [];
	$types = [];
	foreach ($projects as $p) {
		$c = $p['community'];
		if ($c !== '') {
			$comm_counts[$c] = ($comm_counts[$c] ?? 0) + 1;
		}
		$t = $p['type'];
		if ($t !== '') {
			$types[$t] = ($types[$t] ?? 0) + 1;
		}
	}

	$all_communities = array_keys($comm_counts);
	natcasesort($all_communities);

	arsort($comm_counts);
	$quick_comms = [];
	foreach (array_slice($comm_counts, 0, 6, true) as $c => $count) {
		$quick_comms[] = ['name' => $c, 'count' => $count];
	}

	return [
		'total' => count($projects),
		'communities' => array_values($all_communities),
		'quick_communities' => $quick_comms,
		'types' => array_keys($types),
		'prices' => [500000, 750000, 1000000, 2000000, 3000000, 4000000, 5000000, 6000000, 7000000, 8000000, 9000000, 10000000, 15000000, 25000000, 35000000, 45000000],
	];
}

// One buy property for the detail page (/buy-property/<slug>), everything the page prints, or [] when not found
function get_buy_project($slug)
{
	$slug = trim(str_replace('/', '', (string) $slug));
	if ($slug === '') {
		return [];
	}

	$project = db_row(
		"SELECT p.*, c.comm_heading, c.comm_location, c.comm_long_descp
		 FROM tbl_proj p
		 LEFT JOIN tbl_community c ON c.comm_id = p.prj_comm_id
		 WHERE TRIM(p.proj_slug) = ? AND p.prj_type_id = 7 AND p.is_active = '1'
		 ORDER BY p.prj_id DESC LIMIT 1",
		[$slug]
	);
	if (!$project) {
		return [];
	}

	$id = (int) $project['prj_id'];
	$name_on_card = trim((string) ($project['proj_name_on_card'] ?? ''));
	$proj_name = trim((string) ($project['proj_name'] ?? ''));
	$name = $name_on_card !== '' ? $name_on_card : $proj_name;
	$url = BASE_URL . 'buy-property/' . rawurlencode($slug);

	$price = (int) ($project['proj_price'] ?: ($project['proj_start_price'] ?? 0));
	$area = (int) ($project['proj_area'] ?: ($project['proj_size'] ?? 0));
	$ppf = ($area > 0 && $price > 0) ? round($price / $area) : 0;

	$baths = (int) ($project['proj_shower'] ?? 0);
	$bed_raw = trim((string) ($project['proj_bedroom'] ?? ''));
	$is_studio = (strcasecmp($bed_raw, 'STD') === 0 || strcasecmp($bed_raw, 'Studio') === 0 || $bed_raw === '0');
	$beds_num = $is_studio ? 0 : (int) $bed_raw;
	$beds_label = $is_studio ? 'Studio' : ($beds_num > 0 ? (string)$beds_num : '1');

	$type = buy_property_type($proj_name, $slug, $project['property_type'] ?? '');
	$community = buy_property_clean_community($project['comm_heading'] ?? '', $project['proj_location'] ?? '');
	$location = trim((string) ($project['proj_location'] ?? ''));
	if ($location === '') $location = $community;

	// Gallery photos from tbl_proj_gallery
	$gal_rows = db_all(
		"SELECT gallery_img, gallery_img_alt 
		 FROM tbl_proj_gallery 
		 WHERE proj_id = ? AND is_active = '1' 
		 ORDER BY proj_gallery_id ASC",
		[$id]
	);
	$photos = [];
	foreach ($gal_rows as $g) {
		$img = trim((string) $g['gallery_img']);
		if ($img !== '' && is_file(dirname(__DIR__) . '/admin_nsrdwsc/assets/media/project/gallery/' . $img)) {
			$photos[] = [
				'src' => BASE_URL_IMG . 'project/gallery/' . rawurlencode($img),
				'title' => $name . ' photo ' . (count($photos) + 1),
			];
		}
	}
	// Fallback to prj_thumb_img if no gallery files on disk
	if (empty($photos)) {
		$thumbs = array_filter(array_map('trim', explode(',', (string) ($project['prj_thumb_img'] ?? ''))));
		foreach ($thumbs as $t) {
			if ($t !== '' && is_file(dirname(__DIR__) . '/admin_nsrdwsc/assets/media/project/thumb/' . $t)) {
				$photos[] = [
					'src' => BASE_URL_IMG . 'project/thumb/' . rawurlencode($t),
					'title' => $name . ' photo ' . (count($photos) + 1),
				];
			}
		}
	}
	if (empty($photos)) {
		$photos[] = [
			'src' => BASE_URL . 'admin_nsrdwsc/assets/media/logos/logo-light.png',
			'title' => $name,
		];
	}

	$side_photos = [];
	if (count($photos) > 1) {
		$side_photos[] = ['index' => 1, 'src' => $photos[1]['src'], 'title' => $photos[1]['title'], 'show_all' => false];
	}
	if (count($photos) > 2) {
		$side_photos[] = ['index' => 2, 'src' => $photos[2]['src'], 'title' => $photos[2]['title'], 'show_all' => true];
	}

	// Reference Number
	preg_match('/DRE-[A-Z]-\d+/i', (string)$project['proj_overview'] . ' ' . (string)$project['proj_qr'], $ref_m);
	$reference = $ref_m[0] ?? ('DRE-S-' . $id);

	// Permit & QR details
	preg_match('/Zone(?:\s*Name)?\s*:\s*([^<\n\r]+)/i', (string)$project['proj_qr'], $zm);
	preg_match('/RERA\s*:\s*([^<\n\r]+)/i', (string)$project['proj_qr'], $rm);
	preg_match('/BRN\s*:\s*([^<\n\r]+)/i', (string)$project['proj_qr'], $bm);

	$zone = trim(strip_tags($zm[1] ?? ''));
	$rera = trim(strip_tags($rm[1] ?? '652'));
	$brn = trim(strip_tags($bm[1] ?? ''));

	$qr_img = '';
	$qr_raw = trim((string) ($project['proj_qr_img'] ?? ''));
	if ($qr_raw !== '' && is_file(dirname(__DIR__) . '/admin_nsrdwsc/assets/media/project/qr/' . $qr_raw)) {
		$qr_img = BASE_URL_IMG . 'project/qr/' . rawurlencode($qr_raw);
	}
	$qr_link = trim((string) ($project['proj_qr_link'] ?? ''));

	// Map URL
	$map_url = trim((string) ($project['proj_map'] ?? ''));
	if (!preg_match('#^https://www\.google\.com/maps/embed\?#', $map_url)) {
		$map_url = '';
	}

	// Parse Overview, Features, Amenities, Locations Nearby
	$overview_raw = (string) ($project['proj_overview'] ?? '');

	// Extract features (bullet points)
	$features = [];
	if (preg_match('/(?:Unit Features|Property Details|Property Highlights)[^<]*<\/p>\s*<ul>(.*?)<\/ul>/is', $overview_raw, $fm)) {
		preg_match_all('/<li>(.*?)<\/li>/is', $fm[1], $lim);
		foreach ($lim[1] as $item) {
			$clean = trim(strip_tags(html_entity_decode($item, ENT_QUOTES, 'UTF-8')));
			if ($clean !== '') $features[] = $clean;
		}
	}
	if (empty($features)) {
		$features = [
			'Spacious living & dining area',
			'Modern kitchen layout',
			'Balcony with community view',
			'Covered parking space',
			'Built-in wardrobes',
		];
	}

	// Amenities
	$amenities = [];
	if (preg_match('/(?:Amenities & Facilities|Amenities &amp; Facilities|Facilities)[^<]*<\/p>\s*<ul>(.*?)<\/ul>/is', $overview_raw, $am_m)) {
		preg_match_all('/<li>(.*?)<\/li>/is', $am_m[1], $am_lim);
		foreach ($am_lim[1] as $item) {
			$clean = trim(strip_tags(html_entity_decode($item, ENT_QUOTES, 'UTF-8')));
			if ($clean !== '') $amenities[] = $clean;
		}
	}
	if (empty($amenities)) {
		$opt_ids = array_filter(array_map('intval', explode(',', (string) ($project['proj_fea_fac_opt'] ?? ''))));
		if (!empty($opt_ids)) {
			$in = implode(',', $opt_ids);
			$ff_rows = db_all("SELECT featfac_name FROM tbl_proj_featfac WHERE featfac_id IN ($in) AND is_active = '1'");
			foreach ($ff_rows as $ff) {
				$amenities[] = trim((string) $ff['featfac_name']);
			}
		}
	}
	if (empty($amenities)) {
		$amenities = [
			'Landscaped parks & green open spaces',
			'Swimming pool',
			'Fully equipped gym',
			"Children's play areas",
			'Retail shops & daily conveniences',
			'24/7 security & gated community',
		];
	}

	// Drive times
	$drive_times = [];
	if (preg_match('/(?:Locations Nearby|Connectivity)[^<]*<\/p>\s*<ul>(.*?)<\/ul>/is', $overview_raw, $loc_m)) {
		preg_match_all('/<li>(.*?)<\/li>/is', $loc_m[1], $loc_lim);
		foreach ($loc_lim[1] as $item) {
			$clean = trim(strip_tags(html_entity_decode($item, ENT_QUOTES, 'UTF-8')));
			if (preg_match('/^(\d+)\s*(mins?|minutes?)\s*(?:to\s*)?(.*)$/i', $clean, $dm)) {
				$drive_times[] = [
					'time' => $dm[1],
					'unit' => 'min',
					'place' => trim($dm[3]),
				];
			}
		}
	}
	if (empty($drive_times)) {
		$drive_times = [
			['time' => '5', 'unit' => 'min', 'place' => 'Community Park & Retail'],
			['time' => '15', 'unit' => 'min', 'place' => 'Mall of the Emirates'],
			['time' => '20', 'unit' => 'min', 'place' => 'Dubai Hills Mall'],
			['time' => '25', 'unit' => 'min', 'place' => 'Downtown Dubai & Burj Khalifa'],
			['time' => '30', 'unit' => 'min', 'place' => 'Dubai International Airport (DXB)'],
		];
	}

	// About community text
	$about_community_text = '';
	if (preg_match('/About ' . preg_quote($community, '/') . ':[^<]*([^<]+)/i', $overview_raw, $ab_m)) {
		$about_community_text = trim($ab_m[1]);
	}
	if ($about_community_text === '' && !empty($project['comm_long_descp'])) {
		$about_community_text = trim(strip_tags($project['comm_long_descp']));
	}

	// Clean intro paragraphs
	$clean_intro = preg_replace('/<p>\s*REF\s*:[^<]*<\/p>/i', '', $overview_raw);
	$clean_intro = preg_replace('/<p>\s*<strong>(?:Property Details|Amenities & Facilities|Amenities &amp; Facilities|Locations Nearby):?<\/strong><\/p>\s*<ul>.*?<\/ul>/is', '', $clean_intro);
	$clean_intro = preg_replace('/<ul>.*?<\/ul>/is', '', $clean_intro);
	$clean_intro = preg_replace('/<p>\s*About [^<]+<\/p>/is', '', $clean_intro);
	$clean_intro = trim(strip_tags($clean_intro));
	if ($clean_intro === '') {
		$clean_intro = "DRE Homes is delighted to present this exceptional " . $beds_label . "-bedroom " . strtolower($type) . " in " . $community . ", Dubai. Offering modern finishes, comfortable living spaces, and easy access to premium lifestyle amenities.";
	}

	// Similar buy properties
	$similar_rows = db_all(
		"SELECT p.prj_id, p.proj_name, p.proj_name_on_card, p.proj_slug, p.prj_thumb_img,
		        COALESCE(f.price_aed, p.proj_price, p.proj_start_price) AS proj_price,
		        p.proj_bedroom, p.proj_shower, p.proj_area, p.proj_size, p.proj_location,
		        c.comm_heading
		 FROM tbl_proj p
		 LEFT JOIN tbl_community c ON c.comm_id = p.prj_comm_id
		 LEFT JOIN tbl_ai_proj_facts f ON f.prj_id = p.prj_id
		 WHERE p.prj_type_id = 7 AND p.prj_id <> ? AND p.is_active = '1'
		 ORDER BY (c.comm_heading = ? OR p.proj_location LIKE ?) DESC, p.proj_markfeat = '1' DESC, p.prj_id DESC
		 LIMIT 6",
		[$id, $community, '%' . $community . '%']
	);
	$similar = [];
	foreach ($similar_rows as $s) {
		$s_name = trim($s['proj_name_on_card']) ?: trim($s['proj_name']);
		$s_slug = trim($s['proj_slug']);
		$s_url = BASE_URL . 'buy-property/' . rawurlencode($s_slug);
		$s_price = (int) ($s['proj_price'] ?? 0);
		$s_area = (int) ($s['proj_area'] ?: ($s['proj_size'] ?? 0));
		$s_bed = (int) ($s['proj_bedroom'] ?? 0);
		$s_bath = (int) ($s['proj_shower'] ?? 0);
		$s_comm = buy_property_clean_community($s['comm_heading'] ?? '', $s['proj_location'] ?? '');
		$s_thumb = trim(explode(',', (string)$s['prj_thumb_img'])[0] ?? '');
		$s_img = $s_thumb !== '' ? BASE_URL_IMG . 'project/thumb/' . rawurlencode($s_thumb) : (BASE_URL . 'admin_nsrdwsc/assets/media/logos/logo-light.png');

		$similar[] = [
			'name' => $s_name,
			'slug' => $s_slug,
			'url' => $s_url,
			'img' => $s_img,
			'type' => buy_property_type($s['proj_name'], $s_slug),
			'community' => $s_comm,
			'price' => $s_price,
			'price_label' => 'AED ' . number_format($s_price),
			'beds_label' => $s_bed > 0 ? $s_bed . ' bed' : 'Studio',
			'baths_label' => $s_bath . ' bath',
			'area_label' => $s_area > 0 ? number_format($s_area) . ' sq ft' : '',
		];
	}

	// Title HTML with last word highlighted
	$words = explode(' ', $name);
	$title_html = e($name);
	if (count($words) > 1) {
		$last = array_pop($words);
		$title_html = e(implode(' ', $words)) . ' <em class="gold-text">' . e($last) . '</em>';
	}

	return [
		'id' => $id,
		'name' => $name,
		'title_html' => $title_html,
		'slug' => $slug,
		'url' => $url,
		'type' => $type,
		'community' => $community,
		'location' => $location,
		'place' => $location,
		'reference' => $reference,
		'price' => $price,
		'price_label' => 'AED ' . number_format($price),
		'area' => $area,
		'area_label' => number_format($area) . ' sq ft',
		'plot_area' => $area,
		'plot_area_label' => number_format($area) . ' sq ft',
		'ppf' => $ppf,
		'ppf_label' => 'AED ' . number_format($ppf),
		'beds_num' => $beds_num,
		'beds_label' => $beds_label,
		'baths_num' => $baths,
		'baths_label' => (string) $baths,
		'furnishing' => 'Semi furnished',
		'view' => 'Community',
		'status' => 'Vacant · ready to move in',
		'photos' => $photos,
		'side_photos' => $side_photos,
		'features' => $features,
		'amenities' => $amenities,
		'drive_times' => $drive_times,
		'map_url' => $map_url,
		'location_title' => $location,
		'about_community_text' => $about_community_text,
		'clean_intro' => $clean_intro,
		'qr_img' => $qr_img,
		'qr_link' => $qr_link,
		'zone' => $zone,
		'rera' => $rera,
		'brn' => $brn,
		'similar' => $similar,
		'whatsapp' => buy_property_whatsapp_url($name, $price, $url),
		'meta' => [
			'meta_title' => trim((string) ($project['meta_title'] ?? '')) ?: ($name . ' | DRE Homes'),
			'meta_description' => trim((string) ($project['meta_description'] ?? '')) ?: ($clean_intro),
			'meta_keyword' => trim((string) ($project['meta_keyword'] ?? '')) ?: ($name . ', ' . $location . ', Buy Property Dubai'),
			'meta_og_img' => !empty($photos[0]['src']) ? $photos[0]['src'] : '',
		],
	];
}

/* =====================================================================
   New UI: Rent properties (rent-property.php, rent-property-single.php)
   Admin > Rent Property (manage-rent-project.php, edit-tags.php?edit=MjA=)
   ===================================================================== */

// Admin > Rent Property > Rent Property Content (banner image)
function get_rent_content()
{
	return db_row("SELECT * FROM tbl_rentproperty_content WHERE is_active = '1' LIMIT 1") ?: [];
}

function rent_property_clean_community($comm_heading, $proj_location)
{
	$loc = trim((string)$comm_heading);
	if ($loc === '') {
		$loc = trim((string)$proj_location);
	}
	$loc = preg_replace('/,\s*Dubai.*$/i', '', $loc);
	$loc = preg_replace('/\s+Dubai$/i', '', $loc);
	$loc = preg_replace('/\s*\(IMPZ\)/i', '', $loc);
	$loc = trim(explode(',', $loc)[0]);
	if (stripos($loc, 'Town Square') !== false) return 'Town Square';
	if (stripos($loc, 'JVC') !== false || stripos($loc, 'Jumeirah Village Circle') !== false) return 'Jumeirah Village Circle';
	if (strcasecmp($loc, 'Damac Hills 2') === 0) return 'Damac Hills 2';
	if (strcasecmp($loc, 'Damac Hills') === 0 || strcasecmp($loc, 'DAMAC Hills') === 0) return 'DAMAC Hills';
	if (stripos($loc, 'Downtown') !== false) return 'Downtown Dubai';
	if (stripos($loc, 'Dubai Hills') !== false) return 'Dubai Hills Estate';
	return $loc !== '' ? $loc : 'Dubai';
}

function rent_property_type($proj_name, $proj_slug, $property_type_col = '')
{
	$col = trim((string)$property_type_col);
	if ($col !== '') {
		if (stripos($col, 'townhouse') !== false) return 'Townhouse';
		if (stripos($col, 'villa') !== false) return 'Villa';
		if (stripos($col, 'penthouse') !== false) return 'Penthouse';
		if (stripos($col, 'apartment') !== false) return 'Apartment';
		return ucfirst(strtolower($col));
	}
	$str = strtolower($proj_name . ' ' . $proj_slug);
	if (strpos($str, 'townhouse') !== false) return 'Townhouse';
	if (strpos($str, 'villa') !== false) return 'Villa';
	if (strpos($str, 'penthouse') !== false) return 'Penthouse';
	return 'Apartment';
}

function rent_property_whatsapp_url($name, $price = 0, $url = '')
{
	$price_str = $price > 0 ? ' (AED ' . number_format($price) . ' / year)' : '';
	$link_str = $url !== '' ? ' — ' . $url : '';
	$text = "Hi DRE, I'm interested in renting: " . $name . $price_str . $link_str;
	return 'https://wa.me/971585837346?text=' . rawurlencode($text);
}

// Admin > Manage Rent Projects (prj_type_id = 8, is_active = '1')
function get_rent_projects()
{
	$rows = db_all(
		"SELECT p.prj_id, p.proj_name, p.proj_name_on_card, p.proj_slug, p.prj_thumb_img,
		        COALESCE(f.price_aed, p.proj_price, p.proj_start_price) AS proj_price,
		        p.proj_bedroom, p.proj_bedroom_on_card, p.proj_shower, p.proj_area, p.proj_size,
		        p.proj_location, p.property_type, p.proj_inventory_type, p.proj_markfeat,
		        c.comm_heading
		 FROM tbl_proj p
		 LEFT JOIN tbl_community c ON c.comm_id = p.prj_comm_id
		 LEFT JOIN tbl_ai_proj_facts f ON f.prj_id = p.prj_id
		 WHERE p.prj_type_id = 8 AND p.is_active = '1'
		 ORDER BY p.proj_markfeat = '1' DESC, p.proj_posted_on DESC, p.prj_id DESC"
	);

	$projects = [];
	foreach ($rows as $row) {
		$name_on_card = trim((string) ($row['proj_name_on_card'] ?? ''));
		$proj_name = trim((string) ($row['proj_name'] ?? ''));
		$name = $name_on_card !== '' ? $name_on_card : $proj_name;
		$slug = trim((string) ($row['proj_slug'] ?? ''));
		$url = BASE_URL . 'rent-property/' . rawurlencode($slug);

		$price = (int) ($row['proj_price'] ?? 0);
		$area = (int) ($row['proj_area'] ?: ($row['proj_size'] ?? 0));
		$ppf = ($area > 0 && $price > 0) ? round($price / $area) : 0;

		$baths = (int) ($row['proj_shower'] ?? 0);
		$bed_raw = trim((string) ($row['proj_bedroom'] ?? ''));
		$is_studio = (strcasecmp($bed_raw, 'STD') === 0 || strcasecmp($bed_raw, 'Studio') === 0 || $bed_raw === '0');
		$beds_num = $is_studio ? 0 : (int) $bed_raw;
		$beds_label = $is_studio ? 'Studio' : ($beds_num > 0 ? $beds_num . ' bed' : '1 bed');

		$type = rent_property_type($proj_name, $slug, $row['proj_inventory_type'] ?: ($row['property_type'] ?? ''));
		$community = rent_property_clean_community($row['comm_heading'] ?? '', $row['proj_location'] ?? '');
		$location = trim((string) ($row['proj_location'] ?? ''));
		if ($location === '') $location = $community;

		$thumbs_raw = trim((string) ($row['prj_thumb_img'] ?? ''));
		$images = [];
		if ($thumbs_raw !== '') {
			foreach (explode(',', $thumbs_raw) as $thumb) {
				$t = trim($thumb);
				if ($t !== '') {
					$images[] = BASE_URL_IMG . 'project/thumb/' . rawurlencode($t);
				}
			}
		}
		if (empty($images)) {
			$images[] = BASE_URL . 'admin_nsrdwsc/assets/media/logos/logo-light.png';
		}

		$is_featured = ($row['proj_markfeat'] == '1');

		$projects[] = [
			'id' => (int) $row['prj_id'],
			'name' => $name,
			'slug' => $slug,
			'url' => $url,
			'images' => $images,
			'type' => $type,
			'community' => $community,
			'location' => $location,
			'price' => $price,
			'price_label' => 'AED ' . number_format($price) . ' / year',
			'beds_num' => $beds_num,
			'beds_label' => $beds_label,
			'baths_num' => $baths,
			'baths_label' => $baths . ' bath',
			'area' => $area,
			'area_label' => number_format($area) . ' sq ft',
			'ppf' => $ppf,
			'ppf_label' => 'AED ' . number_format($ppf) . ' / sq ft per year',
			'is_featured' => $is_featured,
			'whatsapp' => rent_property_whatsapp_url($name, $price, $url),
		];
	}
	return $projects;
}

// Filter options for the rent properties listing, built from the projects themselves
function get_rent_filters(array $projects)
{
	$comm_counts = [];
	$types = [];
	foreach ($projects as $p) {
		$c = $p['community'];
		if ($c !== '') {
			$comm_counts[$c] = ($comm_counts[$c] ?? 0) + 1;
		}
		$t = $p['type'];
		if ($t !== '') {
			$types[$t] = ($types[$t] ?? 0) + 1;
		}
	}

	$all_communities = array_keys($comm_counts);
	natcasesort($all_communities);

	arsort($comm_counts);
	$quick_comms = [];
	foreach (array_slice($comm_counts, 0, 6, true) as $c => $count) {
		$quick_comms[] = ['name' => $c, 'count' => $count];
	}

	return [
		'total' => count($projects),
		'communities' => array_values($all_communities),
		'quick_communities' => $quick_comms,
		'types' => array_keys($types),
		'prices' => [25000, 50000, 75000, 100000, 125000, 150000, 200000, 250000, 300000, 400000, 500000],
	];
}

// One rent property for the detail page (/rent-property/<slug>), everything the page prints, or [] when not found
function get_rent_project($slug)
{
	$slug = trim(str_replace('/', '', (string) $slug));
	if ($slug === '') {
		return [];
	}

	$project = db_row(
		"SELECT p.*, c.comm_heading, c.comm_location, c.comm_long_descp
		 FROM tbl_proj p
		 LEFT JOIN tbl_community c ON c.comm_id = p.prj_comm_id
		 WHERE TRIM(p.proj_slug) = ? AND p.prj_type_id = 8 AND p.is_active = '1'
		 ORDER BY p.prj_id DESC LIMIT 1",
		[$slug]
	);
	if (!$project) {
		return [];
	}

	$id = (int) $project['prj_id'];
	$name_on_card = trim((string) ($project['proj_name_on_card'] ?? ''));
	$proj_name = trim((string) ($project['proj_name'] ?? ''));
	$name = $name_on_card !== '' ? $name_on_card : $proj_name;
	$url = BASE_URL . 'rent-property/' . rawurlencode($slug);

	$price = (int) ($project['proj_price'] ?: ($project['proj_start_price'] ?? 0));
	$area = (int) ($project['proj_area'] ?: ($project['proj_size'] ?? 0));
	$ppf = ($area > 0 && $price > 0) ? round($price / $area) : 0;

	$baths = (int) ($project['proj_shower'] ?? 0);
	$bed_raw = trim((string) ($project['proj_bedroom'] ?? ''));
	$is_studio = (strcasecmp($bed_raw, 'STD') === 0 || strcasecmp($bed_raw, 'Studio') === 0 || $bed_raw === '0');
	$beds_num = $is_studio ? 0 : (int) $bed_raw;
	$beds_label = $is_studio ? 'Studio' : ($beds_num > 0 ? (string)$beds_num : '1');

	$type = rent_property_type($proj_name, $slug, $project['proj_inventory_type'] ?: ($project['property_type'] ?? ''));
	$community = rent_property_clean_community($project['comm_heading'] ?? '', $project['proj_location'] ?? '');
	$location = trim((string) ($project['proj_location'] ?? ''));
	if ($location === '') $location = $community;

	// Gallery photos from tbl_proj_gallery
	$gal_rows = db_all(
		"SELECT gallery_img, gallery_img_alt 
		 FROM tbl_proj_gallery 
		 WHERE proj_id = ? AND is_active = '1' 
		 ORDER BY proj_gallery_id ASC",
		[$id]
	);
	$photos = [];
	foreach ($gal_rows as $g) {
		$img = trim((string) $g['gallery_img']);
		if ($img !== '' && is_file(dirname(__DIR__) . '/admin_nsrdwsc/assets/media/project/gallery/' . $img)) {
			$photos[] = [
				'src' => BASE_URL_IMG . 'project/gallery/' . rawurlencode($img),
				'title' => $name . ' photo ' . (count($photos) + 1),
			];
		}
	}
	// Fallback to prj_thumb_img if no gallery files on disk
	if (empty($photos)) {
		$thumbs = array_filter(array_map('trim', explode(',', (string) ($project['prj_thumb_img'] ?? ''))));
		foreach ($thumbs as $t) {
			if ($t !== '' && is_file(dirname(__DIR__) . '/admin_nsrdwsc/assets/media/project/thumb/' . $t)) {
				$photos[] = [
					'src' => BASE_URL_IMG . 'project/thumb/' . rawurlencode($t),
					'title' => $name . ' photo ' . (count($photos) + 1),
				];
			}
		}
	}
	if (empty($photos)) {
		$photos[] = [
			'src' => BASE_URL . 'admin_nsrdwsc/assets/media/logos/logo-light.png',
			'title' => $name,
		];
	}

	$side_photos = [];
	if (count($photos) > 1) {
		$side_photos[] = ['index' => 1, 'src' => $photos[1]['src'], 'title' => $photos[1]['title'], 'show_all' => false];
	}
	if (count($photos) > 2) {
		$side_photos[] = ['index' => 2, 'src' => $photos[2]['src'], 'title' => $photos[2]['title'], 'show_all' => true];
	}

	// Reference Number
	preg_match('/DRE-[A-Z]-\d+/i', (string)$project['proj_overview'] . ' ' . (string)$project['proj_qr'], $ref_m);
	$reference = $ref_m[0] ?? ('DRE-R-' . $id);

	// Permit & QR details
	preg_match('/Zone(?:\s*Name)?\s*:\s*([^<\n\r]+)/i', (string)$project['proj_qr'], $zm);
	preg_match('/RERA\s*:\s*([^<\n\r]+)/i', (string)$project['proj_qr'], $rm);
	preg_match('/BRN\s*:\s*([^<\n\r]+)/i', (string)$project['proj_qr'], $bm);

	$zone = trim(strip_tags($zm[1] ?? ''));
	$rera = trim(strip_tags($rm[1] ?? '652'));
	$brn = trim(strip_tags($bm[1] ?? ''));

	$qr_img = '';
	$qr_raw = trim((string) ($project['proj_qr_img'] ?? ''));
	if ($qr_raw !== '' && is_file(dirname(__DIR__) . '/admin_nsrdwsc/assets/media/project/qr/' . $qr_raw)) {
		$qr_img = BASE_URL_IMG . 'project/qr/' . rawurlencode($qr_raw);
	}
	$qr_link = trim((string) ($project['proj_qr_link'] ?? ''));

	// Map URL
	$map_url = trim((string) ($project['proj_map'] ?? ''));
	if (!preg_match('#^https://www\.google\.com/maps/embed\?#', $map_url)) {
		$map_url = '';
	}

	// Sub heading / view info
	$sub_heading = trim((string) ($project['proj_sub_heading'] ?? ''));
	if ($sub_heading === '') {
		$sub_heading = ($beds_label === 'Studio' ? 'Studio' : $beds_label . ' Bedroom') . ' · ' . $location;
	}

	// Parse Overview, Features, Amenities, Locations Nearby
	$overview_raw = (string) ($project['proj_overview'] ?? '');

	// Extract features (bullet points)
	$features = [];
	if (preg_match('/(?:Unit Features|Property Details|Property Highlights)[^<]*<\/p>\s*<ul>(.*?)<\/ul>/is', $overview_raw, $fm)) {
		preg_match_all('/<li>(.*?)<\/li>/is', $fm[1], $lim);
		foreach ($lim[1] as $item) {
			$clean = trim(strip_tags(html_entity_decode($item, ENT_QUOTES, 'UTF-8')));
			if ($clean !== '') $features[] = $clean;
		}
	}
	if (empty($features)) {
		$features = [
			'Spacious living & dining area',
			'Modern kitchen layout',
			'Balcony with view',
			'Covered parking space',
			'Built-in wardrobes',
		];
	}

	// Amenities
	$amenities = [];
	if (preg_match('/(?:Amenities & Facilities|Amenities &amp; Facilities|Facilities)[^<]*<\/p>\s*<ul>(.*?)<\/ul>/is', $overview_raw, $am_m)) {
		preg_match_all('/<li>(.*?)<\/li>/is', $am_m[1], $am_lim);
		foreach ($am_lim[1] as $item) {
			$clean = trim(strip_tags(html_entity_decode($item, ENT_QUOTES, 'UTF-8')));
			if ($clean !== '') $amenities[] = $clean;
		}
	}
	if (empty($amenities)) {
		$opt_ids = array_filter(array_map('intval', explode(',', (string) ($project['proj_fea_fac_opt'] ?? ''))));
		if (!empty($opt_ids)) {
			$in = implode(',', $opt_ids);
			$ff_rows = db_all("SELECT featfac_name FROM tbl_proj_featfac WHERE featfac_id IN ($in) AND is_active = '1'");
			foreach ($ff_rows as $ff) {
				$amenities[] = trim((string) $ff['featfac_name']);
			}
		}
	}
	if (empty($amenities)) {
		$amenities = [
			'Gym',
			'Swimming pool',
			"Kids playground",
			'Park',
			'Retail outlets',
			'24/7 security & CCTV',
		];
	}

	// Drive times
	$drive_times = [];
	if (preg_match('/(?:Locations Nearby|Connectivity)[^<]*<\/p>\s*<ul>(.*?)<\/ul>/is', $overview_raw, $loc_m)) {
		preg_match_all('/<li>(.*?)<\/li>/is', $loc_m[1], $loc_lim);
		foreach ($loc_lim[1] as $item) {
			$clean = trim(strip_tags(html_entity_decode($item, ENT_QUOTES, 'UTF-8')));
			if (preg_match('/^(\d+)\s*(mins?|minutes?)\s*(?:to\s*)?(.*)$/i', $clean, $dm)) {
				$drive_times[] = [
					'time' => $dm[1],
					'unit' => 'min',
					'place' => trim($dm[3]),
				];
			}
		}
	}
	if (empty($drive_times)) {
		$drive_times = [
			['time' => '5', 'unit' => 'min', 'place' => 'Community Park & Retail'],
			['time' => '15', 'unit' => 'min', 'place' => 'Mall of the Emirates'],
			['time' => '20', 'unit' => 'min', 'place' => 'Dubai Hills Mall'],
			['time' => '25', 'unit' => 'min', 'place' => 'Downtown Dubai & Burj Khalifa'],
			['time' => '30', 'unit' => 'min', 'place' => 'Dubai International Airport (DXB)'],
		];
	}

	// About community text
	$about_community_text = '';
	if (preg_match('/About ' . preg_quote($community, '/') . ':[^<]*([^<]+)/i', $overview_raw, $ab_m)) {
		$about_community_text = trim($ab_m[1]);
	}
	if ($about_community_text === '' && !empty($project['comm_long_descp'])) {
		$about_community_text = trim(strip_tags($project['comm_long_descp']));
	}

	// Clean intro paragraphs
	$clean_intro = preg_replace('/<p>\s*REF\s*:[^<]*<\/p>/i', '', $overview_raw);
	$clean_intro = preg_replace('/<p>\s*<strong>(?:Property Details|Amenities & Facilities|Amenities &amp; Facilities|Locations Nearby|Property Highlights):?<\/strong><\/p>\s*<ul>.*?<\/ul>/is', '', $clean_intro);
	$clean_intro = preg_replace('/<ul>.*?<\/ul>/is', '', $clean_intro);
	$clean_intro = preg_replace('/<p>\s*About [^<]+<\/p>/is', '', $clean_intro);
	$clean_intro = trim(strip_tags($clean_intro));
	if ($clean_intro === '') {
		$clean_intro = "DRE Homes is delighted to present this exceptional " . $beds_label . "-bedroom " . strtolower($type) . " for rent in " . $community . ", Dubai. Offering modern finishes, comfortable living spaces, and easy access to community lifestyle amenities.";
	}

	// Similar rent properties
	$similar_rows = db_all(
		"SELECT p.prj_id, p.proj_name, p.proj_name_on_card, p.proj_slug, p.prj_thumb_img,
		        COALESCE(f.price_aed, p.proj_price, p.proj_start_price) AS proj_price,
		        p.proj_bedroom, p.proj_shower, p.proj_area, p.proj_size, p.proj_location,
		        c.comm_heading
		 FROM tbl_proj p
		 LEFT JOIN tbl_community c ON c.comm_id = p.prj_comm_id
		 LEFT JOIN tbl_ai_proj_facts f ON f.prj_id = p.prj_id
		 WHERE p.prj_type_id = 8 AND p.prj_id <> ? AND p.is_active = '1'
		 ORDER BY (c.comm_heading = ? OR p.proj_location LIKE ?) DESC, p.proj_markfeat = '1' DESC, p.prj_id DESC
		 LIMIT 6",
		[$id, $community, '%' . $community . '%']
	);
	$similar = [];
	foreach ($similar_rows as $s) {
		$s_name = trim($s['proj_name_on_card']) ?: trim($s['proj_name']);
		$s_slug = trim($s['proj_slug']);
		$s_url = BASE_URL . 'rent-property/' . rawurlencode($s_slug);
		$s_price = (int) ($s['proj_price'] ?? 0);
		$s_area = (int) ($s['proj_area'] ?: ($s['proj_size'] ?? 0));
		$s_bed = (int) ($s['proj_bedroom'] ?? 0);
		$s_bath = (int) ($s['proj_shower'] ?? 0);
		$s_comm = rent_property_clean_community($s['comm_heading'] ?? '', $s['proj_location'] ?? '');
		$s_thumb = trim(explode(',', (string)$s['prj_thumb_img'])[0] ?? '');
		$s_img = $s_thumb !== '' ? BASE_URL_IMG . 'project/thumb/' . rawurlencode($s_thumb) : (BASE_URL . 'admin_nsrdwsc/assets/media/logos/logo-light.png');

		$similar[] = [
			'name' => $s_name,
			'slug' => $s_slug,
			'url' => $s_url,
			'img' => $s_img,
			'type' => rent_property_type($s['proj_name'], $s_slug),
			'community' => $s_comm,
			'price' => $s_price,
			'price_label' => 'AED ' . number_format($s_price) . ' / year',
			'beds_label' => $s_bed > 0 ? $s_bed . ' bed' : 'Studio',
			'baths_label' => $s_bath . ' bath',
			'area_label' => $s_area > 0 ? number_format($s_area) . ' sq ft' : '',
		];
	}

	// Title HTML with last word highlighted
	$words = explode(' ', $name);
	$title_html = e($name);
	if (count($words) > 1) {
		$last = array_pop($words);
		$title_html = e(implode(' ', $words)) . ' <em class="gold-text">' . e($last) . '</em>';
	}

	return [
		'id' => $id,
		'name' => $name,
		'title_html' => $title_html,
		'sub_heading' => $sub_heading,
		'slug' => $slug,
		'url' => $url,
		'type' => $type,
		'community' => $community,
		'location' => $location,
		'place' => $location,
		'reference' => $reference,
		'price' => $price,
		'price_label' => 'AED ' . number_format($price),
		'price_formatted' => 'AED ' . number_format($price) . ' / year',
		'area' => $area,
		'area_label' => number_format($area) . ' sq ft',
		'ppf' => $ppf,
		'ppf_label' => 'AED ' . number_format($ppf) . ' / yr',
		'beds_num' => $beds_num,
		'beds_label' => $beds_label,
		'baths_num' => $baths,
		'baths_label' => (string) $baths,
		'furnishing' => 'Semi furnished',
		'view' => 'Community',
		'status' => 'Vacant · ready to move in',
		'photos' => $photos,
		'side_photos' => $side_photos,
		'features' => $features,
		'amenities' => $amenities,
		'drive_times' => $drive_times,
		'map_url' => $map_url,
		'location_title' => $location,
		'about_community_text' => $about_community_text,
		'clean_intro' => $clean_intro,
		'qr_img' => $qr_img,
		'qr_link' => $qr_link,
		'zone' => $zone,
		'rera' => $rera,
		'brn' => $brn,
		'similar' => $similar,
		'whatsapp' => rent_property_whatsapp_url($name, $price, $url),
		'meta' => [
			'meta_title' => trim((string) ($project['meta_title'] ?? '')) ?: ($name . ' | Rent | DRE Homes'),
			'meta_description' => trim((string) ($project['meta_description'] ?? '')) ?: ($clean_intro),
			'meta_keyword' => trim((string) ($project['meta_keyword'] ?? '')) ?: ($name . ', ' . $location . ', Rent Property Dubai'),
			'meta_og_img' => !empty($photos[0]['src']) ? $photos[0]['src'] : '',
		],
	];
}

/* =====================================================================
   New UI: International properties (international-properties.php, international-properties-single.php)
   Admin > International (edit-international-content.php?edit=MQ==, manage-project.php where prj_type_id=3, edit-tags.php?edit=MTA=)
   ===================================================================== */

// Admin > International Property Content (edit-international-content.php?edit=MQ==)
function get_international_content()
{
	return db_row("SELECT * FROM tbl_international_content WHERE is_active = '1' ORDER BY intl_cont_id ASC LIMIT 1") ?: [];
}

function international_detect_country($proj_location, $proj_name = '')
{
	$loc = (string) $proj_location . ' ' . (string) $proj_name;
	$countries = [
		'Turkey' => 'Turkey',
		'Pakistan' => 'Pakistan',
		'Oman' => 'Oman',
		'Lebanon' => 'Lebanon',
		'Montenegro' => 'Montenegro',
		'United Kingdom' => 'United Kingdom',
		'UK' => 'United Kingdom',
		'United States' => 'United States',
		'USA' => 'United States',
		'Cyprus' => 'Cyprus',
		'France' => 'France',
		'Spain' => 'Spain',
		'Italy' => 'Italy',
		'Germany' => 'Germany',
		'India' => 'India',
		'Saudi Arabia' => 'Saudi Arabia',
		'Qatar' => 'Qatar',
		'Bahrain' => 'Bahrain',
		'Kuwait' => 'Kuwait',
		'Egypt' => 'Egypt',
	];
	foreach ($countries as $needle => $canonical) {
		if (stripos($loc, $needle) !== false) {
			return $canonical;
		}
	}
	$parts = array_filter(array_map('trim', explode(',', (string) $proj_location)));
	return !empty($parts) ? end($parts) : 'International';
}

function international_country_flag($country)
{
	$c = strtolower(trim((string) $country));
	$flags = [
		'turkey' => '🇹🇷',
		'pakistan' => '🇵🇰',
		'oman' => '🇴🇲',
		'lebanon' => '🇱🇧',
		'montenegro' => '🇲🇪',
		'united kingdom' => '🇬🇧',
		'uk' => '🇬🇧',
		'united states' => '🇺🇸',
		'usa' => '🇺🇸',
		'cyprus' => '🇨🇾',
		'france' => '🇫🇷',
		'spain' => '🇪🇸',
		'italy' => '🇮🇹',
		'germany' => '🇩🇪',
		'india' => '🇮🇳',
		'saudi arabia' => '🇸🇦',
		'qatar' => '🇶🇦',
		'bahrain' => '🇧🇭',
		'kuwait' => '🇰🇼',
		'egypt' => '🇪🇬',
	];
	return $flags[$c] ?? '🌍';
}

function international_clean_name($proj_name, $proj_name_on_card = '', $dev_heading = '')
{
	$on_card = trim((string) $proj_name_on_card);
	if ($on_card !== '') {
		return $on_card;
	}
	$name = trim((string) $proj_name);
	$name = preg_replace('/(?:\s+(?:at|in)|,\s*)\s*(?:Montenegro.*|Lebanon.*|Karachi.*|Pakistan.*|Muscat.*|Oman.*|Istanbul.*|Turkey.*)$/i', '', $name);
	if ($dev_heading !== '') {
		$name = preg_replace('/\s+by\s+' . preg_quote($dev_heading, '/') . '$/i', '', $name);
	}
	if (stripos($name, 'Emaar ') === 0) {
		$name = substr($name, 6);
	}
	$name = preg_replace('/\s+(?:Istanbul|Karachi|Muscat|Beirut|Tivat)$/i', '', $name);
	return trim($name);
}

function international_clean_location($proj_location, $country)
{
	$loc = trim((string) $proj_location);
	if ($loc === '') {
		return $country;
	}
	$parts = array_filter(array_map('trim', explode(',', $loc)));
	if (count($parts) >= 2) {
		$city = $parts[0];
		if (strcasecmp($city, $country) !== 0) {
			return $city . ' · ' . $country;
		}
	}
	if (strcasecmp($loc, 'Montenegro, Europe') === 0) {
		return 'Tivat · Montenegro';
	}
	return $loc !== '' ? $loc : $country;
}

function international_whatsapp_url($name, $location = '', $url = '')
{
	$loc_str = $location !== '' ? ' (' . $location . ')' : '';
	$link_str = $url !== '' ? ' ' . $url : '';
	$text = "Hi DRE, I'm interested in " . $name . $loc_str . "." . $link_str;
	return 'https://wa.me/971585837346?text=' . rawurlencode(trim($text));
}

// Admin > Manage Projects (prj_type_id = 3, is_active = '1')
function get_international_projects()
{
	$rows = db_all(
		"SELECT p.prj_id, p.proj_name, p.proj_name_on_card, p.proj_slug, p.prj_thumb_img,
		        p.proj_price, p.proj_start_price, p.proj_bedroom, p.proj_bedroom_on_card,
		        p.proj_location, p.proj_overview, p.proj_markfeat,
		        d.dev_heading, d.dev_slug
		 FROM tbl_proj p
		 LEFT JOIN tbl_developer d ON d.dev_id = p.prj_dev_id
		 WHERE p.prj_type_id = 3 AND p.is_active = '1'
		 ORDER BY (p.prj_id = 1064) DESC, p.proj_markfeat = '1' DESC, p.prj_id DESC"
	);

	$projects = [];
	foreach ($rows as $idx => $row) {
		$slug = trim((string) ($row['proj_slug'] ?? ''));
		$url = BASE_URL . 'international-properties/' . rawurlencode($slug);
		$dev_name = trim((string) ($row['dev_heading'] ?? ''));
		$name = international_clean_name($row['proj_name'], $row['proj_name_on_card'] ?? '', $dev_name);
		$country = international_detect_country($row['proj_location'] ?? '', $row['proj_name'] ?? '');
		$flag = international_country_flag($country);
		$location_display = international_clean_location($row['proj_location'] ?? '', $country);

		$bed_raw = trim((string) ($row['proj_bedroom_on_card'] ?: ($row['proj_bedroom'] ?? '')));
		$beds_display = str_ireplace('STD', 'Studio', $bed_raw);
		$beds_display = str_replace(',', ', ', $beds_display);
		$beds_display = preg_replace('/\s+/', ' ', $beds_display);
		if ($beds_display === '') $beds_display = 'Residences';

		$price_num = (int) ($row['proj_price'] ?: ($row['proj_start_price'] ?? 0));
		if ($price_num > 1000) {
			$price_label = 'AED ' . number_format($price_num);
			$price_html = '<b>AED ' . number_format($price_num) . '</b>';
		} else {
			$price_label = 'Price on request';
			$price_html = '<b><em>Price on request</em></b>';
		}

		$thumbs_raw = trim((string) ($row['prj_thumb_img'] ?? ''));
		$thumb = '';
		if ($thumbs_raw !== '') {
			$thumb_list = explode(',', $thumbs_raw);
			$thumb = trim($thumb_list[0] ?? '');
		}
		$image = $thumb !== '' ? BASE_URL_IMG . 'project/thumb/' . rawurlencode($thumb) : BASE_URL . 'admin_nsrdwsc/assets/media/logos/logo-light.png';

		$projects[] = [
			'id' => (int) $row['prj_id'],
			'number' => str_pad((string)($idx + 1), 2, '0', STR_PAD_LEFT),
			'name' => $name,
			'raw_name' => $row['proj_name'],
			'slug' => $slug,
			'url' => $url,
			'country' => $country,
			'flag' => $flag,
			'location' => $location_display,
			'developer' => $dev_name,
			'developer_slug' => $row['dev_slug'] ?? '',
			'beds' => $beds_display,
			'price' => $price_num,
			'price_label' => $price_label,
			'price_html' => $price_html,
			'image' => $image,
			'is_rev' => ($idx % 2 === 1),
			'whatsapp' => international_whatsapp_url($name, $country, $url),
		];
	}
	return $projects;
}

// Destination summary built dynamically from the projects array
function get_international_destinations(array $projects)
{
	$dest_map = [];
	foreach ($projects as $p) {
		$c = $p['country'];
		if (!isset($dest_map[$c])) {
			$dest_map[$c] = [
				'country' => $c,
				'flag' => $p['flag'],
				'count' => 0,
			];
		}
		$dest_map[$c]['count']++;
	}
	$destinations = array_values($dest_map);
	foreach ($destinations as &$d) {
		$d['label'] = $d['count'] === 1 ? '1 project' : $d['count'] . ' projects';
	}
	unset($d);
	return $destinations;
}

// One international project for detail page (/international-properties/<slug>)
function get_international_project($slug)
{
	$slug = trim(str_replace('/', '', (string) $slug));
	if ($slug === '') {
		return [];
	}

	$project = db_row(
		"SELECT p.*, d.dev_heading, d.dev_slug, d.dev_innr_img
		 FROM tbl_proj p
		 LEFT JOIN tbl_developer d ON d.dev_id = p.prj_dev_id
		 WHERE TRIM(p.proj_slug) = ? AND p.prj_type_id = 3 AND p.is_active = '1'
		 ORDER BY p.prj_id DESC LIMIT 1",
		[$slug]
	);
	if (!$project) {
		return [];
	}

	$id = (int) $project['prj_id'];
	$dev_name = trim((string) ($project['dev_heading'] ?? ''));
	$name = international_clean_name($project['proj_name'], $project['proj_name_on_card'] ?? '', $dev_name);
	$url = BASE_URL . 'international-properties/' . rawurlencode($slug);
	$country = international_detect_country($project['proj_location'] ?? '', $project['proj_name'] ?? '');
	$flag = international_country_flag($country);
	$location = international_clean_location($project['proj_location'] ?? '', $country);

	$price_num = (int) ($project['proj_price'] ?: ($project['proj_start_price'] ?? 0));
	$price_label = $price_num > 1000 ? 'AED ' . number_format($price_num) : 'Price on request';

	$bed_raw = trim((string) ($project['proj_bedroom'] ?? ''));
	$beds_display = str_ireplace('STD', 'Studio', $bed_raw);
	$beds_display = str_replace(',', ', ', $beds_display);
	$beds_display = preg_replace('/\s+/', ' ', $beds_display);
	if ($beds_display === '') $beds_display = '1 – 5 & penthouses';

	$gal_rows = db_all(
		"SELECT gallery_img, gallery_img_alt 
		 FROM tbl_proj_gallery 
		 WHERE proj_id = ? AND is_active = '1' 
		 ORDER BY proj_gallery_id ASC",
		[$id]
	);
	$photos = [];
	foreach ($gal_rows as $idx => $g) {
		$file = trim((string) $g['gallery_img']);
		if ($file !== '') {
			$photos[] = [
				'src' => BASE_URL_IMG . 'project/gallery/' . rawurlencode($file),
				'title' => trim((string) ($g['gallery_img_alt'] ?: ($name . ' photo ' . ($idx + 1)))),
			];
		}
	}
	if (empty($photos)) {
		$thumb = trim(explode(',', (string)$project['prj_thumb_img'])[0] ?? '');
		if ($thumb !== '') {
			$photos[] = [
				'src' => BASE_URL_IMG . 'project/thumb/' . rawurlencode($thumb),
				'title' => $name,
			];
		}
	}

	$side_photos = [];
	if (count($photos) > 1) {
		$side_photos = array_slice($photos, 1, 2);
	}

	// Overview paragraphs
	$overview_paras = text_paragraphs($project['proj_overview']);
	if (empty($overview_paras)) {
		$overview_paras = [$name . ' offers prestigious residences with multiple layouts and world-class facilities in ' . $location . ', backed by trusted DRE guidance.'];
	}

	// Amenities from tbl_proj_featfac
	$amenities = [];
	$opt_ids = array_filter(array_map('intval', explode(',', (string) ($project['proj_fea_fac_opt'] ?? ''))));
	if (!empty($opt_ids)) {
		$in = implode(',', $opt_ids);
		$ff_rows = db_all("SELECT featfac_name, featfac_faicon FROM tbl_proj_featfac WHERE featfac_id IN ($in) AND is_active = '1'");
		foreach ($ff_rows as $ff) {
			$amenities[] = [
				'name' => trim((string) $ff['featfac_name']),
				'icon' => amenity_icon_svg($ff['featfac_faicon']),
			];
		}
	}
	if (empty($amenities)) {
		$amenities = [
			['name' => 'Gym', 'icon' => amenity_icon_svg('fa-dumbbell')],
			['name' => 'Kids Playground', 'icon' => amenity_icon_svg('fa-child-reaching')],
			['name' => 'Park', 'icon' => amenity_icon_svg('fa-tree')],
			['name' => 'Swimming Pool', 'icon' => amenity_icon_svg('fa-person-swimming')],
			['name' => 'Fine Dining', 'icon' => amenity_icon_svg('fa-utensils')],
		];
	}

	$brochure = trim((string) ($project['proj_brochure'] ?? ''));
	$floorplan = trim((string) ($project['proj_floorplan'] ?? ''));
	$payplan = trim((string) ($project['proj_payplan'] ?? ''));

	$map_url = trim((string) ($project['proj_map'] ?? ''));
	if (!preg_match('#^https://www\.google\.com/maps/embed\?#', $map_url)) {
		$map_url = '';
	}

	$sim_rows = db_all(
		"SELECT p.prj_id, p.proj_name, p.proj_name_on_card, p.proj_slug, p.prj_thumb_img,
		        p.proj_price, p.proj_start_price, p.proj_bedroom, p.proj_location,
		        d.dev_heading
		 FROM tbl_proj p
		 LEFT JOIN tbl_developer d ON d.dev_id = p.prj_dev_id
		 WHERE p.prj_type_id = 3 AND p.prj_id <> ? AND p.is_active = '1'
		 ORDER BY p.proj_markfeat = '1' DESC, p.prj_id ASC
		 LIMIT 5",
		[$id]
	);
	$similar = [];
	foreach ($sim_rows as $s) {
		$s_dev = trim((string) ($s['dev_heading'] ?? ''));
		$s_name = international_clean_name($s['proj_name'], $s['proj_name_on_card'] ?? '', $s_dev);
		$s_slug = trim($s['proj_slug']);
		$s_country = international_detect_country($s['proj_location'] ?? '', $s['proj_name'] ?? '');
		$s_flag = international_country_flag($s_country);
		$s_thumb = trim(explode(',', (string)$s['prj_thumb_img'])[0] ?? '');
		$s_img = $s_thumb !== '' ? BASE_URL_IMG . 'project/thumb/' . rawurlencode($s_thumb) : (BASE_URL . 'admin_nsrdwsc/assets/media/logos/logo-light.png');
		$s_price_num = (int) ($s['proj_price'] ?: ($s['proj_start_price'] ?? 0));
		$s_price_html = $s_price_num > 1000 ? '<b>AED ' . number_format($s_price_num) . '</b>' : '<b><em>Price on request</em></b>';
		$s_bed_raw = trim((string) ($s['proj_bedroom'] ?? ''));
		$s_bed = str_ireplace('STD', 'Studio', $s_bed_raw);
		$s_bed = str_replace(',', ', ', $s_bed);

		$similar[] = [
			'name' => $s_name,
			'slug' => $s_slug,
			'url' => BASE_URL . 'international-properties/' . rawurlencode($s_slug),
			'image' => $s_img,
			'country' => $s_country,
			'flag' => $s_flag,
			'location' => international_clean_location($s['proj_location'] ?? '', $s_country),
			'beds' => $s_bed ?: 'Residences',
			'price_html' => $s_price_html,
		];
	}

	$words = explode(' ', $name);
	$title_html = e($name);
	if (count($words) > 1) {
		$last = array_pop($words);
		$title_html = e(implode(' ', $words)) . ' <em class="gold-text">' . e($last) . '</em>';
	}

	return [
		'id' => $id,
		'name' => $name,
		'title_html' => $title_html,
		'raw_name' => $project['proj_name'],
		'slug' => $slug,
		'url' => $url,
		'country' => $country,
		'flag' => $flag,
		'location' => $location,
		'developer' => $dev_name,
		'developer_slug' => $project['dev_slug'] ?? '',
		'price' => $price_num,
		'price_label' => $price_label,
		'beds' => $beds_display,
		'payment_plan' => !empty($project['proj_pplan_cont']) ? trim(strip_tags($project['proj_pplan_cont'])) : 'Easy payment plan',
		'photos' => $photos,
		'side_photos' => $side_photos,
		'overview' => $overview_paras,
		'amenities' => $amenities,
		'brochure' => $brochure,
		'floorplan' => $floorplan,
		'payplan' => $payplan,
		'map_url' => $map_url,
		'similar' => $similar,
		'whatsapp' => international_whatsapp_url($name, $location, $url),
		'meta' => [
			'meta_title' => trim((string) ($project['meta_title'] ?? '')) ?: ($name . ' | International Properties | DRE Homes'),
			'meta_description' => trim((string) ($project['meta_description'] ?? '')) ?: (mb_substr($overview_paras[0] ?? '', 0, 160)),
			'meta_keyword' => trim((string) ($project['meta_keyword'] ?? '')) ?: ($name . ', ' . $country . ', International Properties'),
			'meta_og_img' => !empty($photos[0]['src']) ? $photos[0]['src'] : '',
		],
	];
}

/* =====================================================================
   New UI: Ready to move in properties (ready-to-move-in.php, ready-to-move-in-single.php)
   Admin > Ready To Move In (edit-readytomovein-content.php?edit=MQ==, manage-project.php where prj_type_id=2, edit-tags.php?edit=OQ==)
   ===================================================================== */

// Admin > Ready To Move In Content (edit-readytomovein-content.php?edit=MQ==)
function get_ready_content()
{
	return db_row("SELECT * FROM tbl_readytomovein_content WHERE is_active = '1' ORDER BY readytomovein_cont_id ASC LIMIT 1") ?: [];
}

function ready_property_type($proj_name, $proj_slug, $property_type_col = '')
{
	$col = trim((string)$property_type_col);
	if ($col !== '') {
		if (stripos($col, 'townhouse') !== false) return 'Townhouse';
		if (stripos($col, 'villa') !== false) return 'Villa';
		if (stripos($col, 'penthouse') !== false) return 'Penthouse';
		if (stripos($col, 'apartment') !== false) return 'Apartment';
		return ucfirst(strtolower($col));
	}
	$str = strtolower($proj_name . ' ' . $proj_slug);
	if (strpos($str, 'townhouse') !== false) return 'Townhouse';
	if (strpos($str, 'villa') !== false) return 'Villa';
	if (strpos($str, 'penthouse') !== false) return 'Penthouse';
	return 'Apartment';
}

function ready_property_whatsapp_url($name, $price = 0, $url = '')
{
	$price_str = $price > 0 ? ' (AED ' . number_format($price) . ')' : '';
	$link_str = $url !== '' ? ' — ' . $url : '';
	$text = "Hi DRE, I'm interested in ready property: " . $name . $price_str . $link_str;
	return 'https://wa.me/971585837346?text=' . rawurlencode($text);
}

// Admin > Manage Projects (prj_type_id = 2, is_active = '1')
function get_ready_projects()
{
	$rows = db_all(
		"SELECT p.prj_id, p.proj_name, p.proj_name_on_card, p.proj_slug, p.prj_thumb_img,
		        COALESCE(f.price_aed, p.proj_price, p.proj_start_price) AS proj_price,
		        p.proj_bedroom, p.proj_bedroom_on_card, p.proj_location, p.proj_area, p.proj_size,
		        p.property_type, p.proj_inventory_type, p.proj_markfeat, p.proj_posted_on,
		        d.dev_heading, d.dev_slug,
		        c.comm_heading,
		        f.bedrooms_min, f.bedrooms_max, f.bedrooms_list, f.has_studio
		 FROM tbl_proj p
		 LEFT JOIN tbl_developer d ON d.dev_id = p.prj_dev_id
		 LEFT JOIN tbl_community c ON c.comm_id = p.prj_comm_id
		 LEFT JOIN tbl_ai_proj_facts f ON f.prj_id = p.prj_id
		 WHERE p.prj_type_id = 2 AND p.is_active = '1'
		 ORDER BY (p.prj_id = 1348) DESC, p.proj_markfeat = '1' DESC, p.proj_posted_on DESC, p.prj_id DESC"
	);

	$projects = [];
	foreach ($rows as $row) {
		$name_on_card = trim((string) ($row['proj_name_on_card'] ?? ''));
		$proj_name = trim((string) ($row['proj_name'] ?? ''));
		$name = $name_on_card !== '' ? $name_on_card : $proj_name;
		$slug = trim((string) ($row['proj_slug'] ?? ''));
		$url = BASE_URL . 'ready-to-move-in/' . rawurlencode($slug);

		$dev_name = trim((string) ($row['dev_heading'] ?? ''));
		if ($dev_name === '') $dev_name = 'Dubai Real Estate';

		$comm = trim((string) ($row['comm_heading'] ?? ''));
		$loc = trim((string) ($row['proj_location'] ?? ''));
		$area = $comm !== '' ? $comm : $loc;
		$area = preg_replace('/,\s*Dubai.*$/i', '', $area);
		$area = trim(preg_replace('/\s+Dubai$/i', '', $area));

		$type = ready_property_type($proj_name, $slug, $row['proj_inventory_type'] ?: ($row['property_type'] ?? ''));

		$price = (int) ($row['proj_price'] ?? 0);
		$price_label = $price > 1000 ? 'AED ' . number_format($price) : 'Price on request';

		$bmin = (int) ($row['bedrooms_min'] ?? 0);
		$bmax = (int) ($row['bedrooms_max'] ?? 0);
		$blist = trim((string) ($row['bedrooms_list'] ?? ''));
		$has_std = !empty($row['has_studio']);

		$bed_raw = trim((string) ($row['proj_bedroom_on_card'] ?: ($row['proj_bedroom'] ?? '')));
		if ($bed_raw !== '') {
			$beds_label = str_ireplace('STD', 'Studio', $bed_raw);
			$beds_label = str_replace(',', ', ', $beds_label);
			$beds_label = preg_replace('/\s+/', ' ', $beds_label);
		} else {
			$beds_label = $bmax > 0 ? ($has_std ? 'Studio, ' : '') . ($bmin === $bmax ? $bmin : $bmin . ' – ' . $bmax) . ' Bed' : 'Residences';
		}

		$thumbs_raw = trim((string) ($row['prj_thumb_img'] ?? ''));
		$thumb = '';
		if ($thumbs_raw !== '') {
			$thumb = trim(explode(',', $thumbs_raw)[0] ?? '');
		}
		$image = $thumb !== '' ? BASE_URL_IMG . 'project/thumb/' . rawurlencode($thumb) : BASE_URL . 'admin_nsrdwsc/assets/media/logos/logo-light.png';

		$projects[] = [
			'id' => (int) $row['prj_id'],
			'name' => $name,
			'slug' => $slug,
			'url' => $url,
			'image' => $image,
			'developer' => $dev_name,
			'developer_slug' => $row['dev_slug'] ?? '',
			'area' => $area,
			'location' => $loc !== '' ? $loc : $area,
			'type' => $type,
			'price' => $price,
			'price_label' => $price_label,
			'beds' => $beds_label,
			'bmin' => $bmin,
			'bmax' => $bmax,
			'blist' => $blist,
			'has_studio' => $has_std,
			'handover' => 'Ready to move in',
			'whatsapp' => ready_property_whatsapp_url($name, $price, $url),
		];
	}
	return $projects;
}

// Filter options for ready to move in listing
function get_ready_filters(array $projects)
{
	$dev_counts = [];
	$area_counts = [];
	$types = [];

	foreach ($projects as $p) {
		$d = $p['developer'];
		if ($d !== '') {
			$dev_counts[$d] = ($dev_counts[$d] ?? 0) + 1;
		}
		$a = $p['area'];
		if ($a !== '') {
			$area_counts[$a] = ($area_counts[$a] ?? 0) + 1;
		}
		$t = $p['type'];
		if ($t !== '') {
			$types[$t] = ($types[$t] ?? 0) + 1;
		}
	}

	$all_devs = array_keys($dev_counts);
	natcasesort($all_devs);

	arsort($dev_counts);
	$quick_devs = [];
	foreach (array_slice($dev_counts, 0, 6, true) as $d => $count) {
		$quick_devs[] = ['name' => $d, 'count' => $count];
	}

	$all_areas = array_keys($area_counts);
	natcasesort($all_areas);

	return [
		'total' => count($projects),
		'types' => array_keys($types),
		'developers' => array_values($all_devs),
		'quick_developers' => $quick_devs,
		'areas' => array_values($all_areas),
		'prices' => [500000, 750000, 1000000, 2000000, 3000000, 4000000, 5000000, 6000000, 7000000, 8000000, 9000000, 10000000, 15000000, 20000000],
	];
}

// Single ready to move in project (/ready-to-move-in/<slug>)
function get_ready_project($slug)
{
	$slug = trim(str_replace('/', '', (string) $slug));
	if ($slug === '') {
		return [];
	}

	$project = db_row(
		"SELECT p.*, d.dev_heading, d.dev_slug, d.dev_innr_img,
		        c.comm_heading, c.comm_location, c.comm_long_descp
		 FROM tbl_proj p
		 LEFT JOIN tbl_developer d ON d.dev_id = p.prj_dev_id
		 LEFT JOIN tbl_community c ON c.comm_id = p.prj_comm_id
		 WHERE TRIM(p.proj_slug) = ? AND p.prj_type_id = 2 AND p.is_active = '1'
		 ORDER BY p.prj_id DESC LIMIT 1",
		[$slug]
	);
	if (!$project) {
		return [];
	}

	$id = (int) $project['prj_id'];
	$name_on_card = trim((string) ($project['proj_name_on_card'] ?? ''));
	$proj_name = trim((string) ($project['proj_name'] ?? ''));
	$name = $name_on_card !== '' ? $name_on_card : $proj_name;
	$url = BASE_URL . 'ready-to-move-in/' . rawurlencode($slug);

	$dev_name = trim((string) ($project['dev_heading'] ?? ''));
	if ($dev_name === '') $dev_name = 'Dubai Real Estate';

	$comm = trim((string) ($project['comm_heading'] ?? ''));
	$loc = trim((string) ($project['proj_location'] ?? ''));
	$location = $loc !== '' ? $loc : ($comm !== '' ? $comm : 'Dubai');

	$price_num = (int) ($project['proj_price'] ?: ($project['proj_start_price'] ?? 0));
	$price_label = $price_num > 1000 ? 'AED ' . number_format($price_num) : 'Price on request';

	$type = ready_property_type($proj_name, $slug, $project['proj_inventory_type'] ?: ($project['property_type'] ?? ''));

	$bed_raw = trim((string) ($project['proj_bedroom'] ?? ''));
	$beds_display = str_ireplace('STD', 'Studio', $bed_raw);
	$beds_display = str_replace(',', ', ', $beds_display);
	$beds_display = preg_replace('/\s+/', ' ', $beds_display);
	if ($beds_display === '') $beds_display = 'Studio, 1 – 3 Bed';

	// Photos from tbl_proj_gallery
	$gal_rows = db_all(
		"SELECT gallery_img, gallery_img_alt 
		 FROM tbl_proj_gallery 
		 WHERE proj_id = ? AND is_active = '1' 
		 ORDER BY proj_gallery_id ASC",
		[$id]
	);
	$photos = [];
	foreach ($gal_rows as $idx => $g) {
		$file = trim((string) $g['gallery_img']);
		if ($file !== '') {
			$photos[] = [
				'src' => BASE_URL_IMG . 'project/gallery/' . rawurlencode($file),
				'title' => trim((string) ($g['gallery_img_alt'] ?: ($name . ' photo ' . ($idx + 1)))),
			];
		}
	}
	if (empty($photos)) {
		$thumb = trim(explode(',', (string)$project['prj_thumb_img'])[0] ?? '');
		if ($thumb !== '') {
			$photos[] = [
				'src' => BASE_URL_IMG . 'project/thumb/' . rawurlencode($thumb),
				'title' => $name,
			];
		}
	}

	$side_photos = [];
	if (count($photos) > 1) {
		$side_photos = array_slice($photos, 1, 2);
	}

	// Overview
	$overview_paras = text_paragraphs($project['proj_overview']);
	if (empty($overview_paras)) {
		$overview_paras = [$name . ' offers prime ready-to-move-in residences with modern finishes and immediate handover in ' . $location . '.'];
	}

	// Amenities from tbl_proj_featfac
	$amenities = [];
	$opt_ids = array_filter(array_map('intval', explode(',', (string) ($project['proj_fea_fac_opt'] ?? ''))));
	if (!empty($opt_ids)) {
		$in = implode(',', $opt_ids);
		$ff_rows = db_all("SELECT featfac_name, featfac_faicon FROM tbl_proj_featfac WHERE featfac_id IN ($in) AND is_active = '1'");
		foreach ($ff_rows as $ff) {
			$amenities[] = [
				'name' => trim((string) $ff['featfac_name']),
				'icon' => amenity_icon_svg($ff['featfac_faicon']),
			];
		}
	}
	if (empty($amenities)) {
		$amenities = [
			['name' => 'Gym', 'icon' => amenity_icon_svg('fa-dumbbell')],
			['name' => 'Swimming Pool', 'icon' => amenity_icon_svg('fa-person-swimming')],
			['name' => 'Kids Playground', 'icon' => amenity_icon_svg('fa-child-reaching')],
			['name' => 'Parks & Leisure Areas', 'icon' => amenity_icon_svg('fa-tree')],
			['name' => 'Dining Outlets', 'icon' => amenity_icon_svg('fa-utensils')],
			['name' => 'Retail Outlets', 'icon' => amenity_icon_svg('fa-store')],
		];
	}

	$brochure = trim((string) ($project['proj_brochure'] ?? ''));
	$floorplan = trim((string) ($project['proj_floorplan'] ?? ''));
	$payplan = trim((string) ($project['proj_payplan'] ?? ''));

	$map_url = trim((string) ($project['proj_map'] ?? ''));
	if (!preg_match('#^https://www\.google\.com/maps/embed\?#', $map_url)) {
		$map_url = '';
	}

	// Similar ready to move in projects
	$sim_rows = db_all(
		"SELECT p.prj_id, p.proj_name, p.proj_name_on_card, p.proj_slug, p.prj_thumb_img,
		        p.proj_price, p.proj_start_price, p.proj_bedroom, p.proj_location,
		        d.dev_heading
		 FROM tbl_proj p
		 LEFT JOIN tbl_developer d ON d.dev_id = p.prj_dev_id
		 WHERE p.prj_type_id = 2 AND p.prj_id <> ? AND p.is_active = '1'
		 ORDER BY p.proj_markfeat = '1' DESC, p.prj_id DESC
		 LIMIT 6",
		[$id]
	);
	$similar = [];
	foreach ($sim_rows as $s) {
		$s_name = trim((string) ($s['proj_name_on_card'] ?? '')) ?: trim((string) ($s['proj_name'] ?? ''));
		$s_slug = trim((string) ($s['proj_slug'] ?? ''));
		$s_thumb = trim(explode(',', (string)$s['prj_thumb_img'])[0] ?? '');
		$s_img = $s_thumb !== '' ? BASE_URL_IMG . 'project/thumb/' . rawurlencode($s_thumb) : (BASE_URL . 'admin_nsrdwsc/assets/media/logos/logo-light.png');
		$s_price_num = (int) ($s['proj_price'] ?: ($s['proj_start_price'] ?? 0));
		$s_price_html = $s_price_num > 1000 ? '<b>AED ' . number_format($s_price_num) . '</b>' : '<b><em>Price on request</em></b>';
		$s_bed_raw = trim((string) ($s['proj_bedroom'] ?? ''));
		$s_bed = str_ireplace('STD', 'Studio', $s_bed_raw);
		$s_bed = str_replace(',', ', ', $s_bed);

		$similar[] = [
			'name' => $s_name,
			'slug' => $s_slug,
			'url' => BASE_URL . 'ready-to-move-in/' . rawurlencode($s_slug),
			'image' => $s_img,
			'location' => trim((string)$s['proj_location']),
			'developer' => trim((string)($s['dev_heading'] ?? '')),
			'beds' => $s_bed ?: 'Residences',
			'price_html' => $s_price_html,
		];
	}

	$words = explode(' ', $name);
	$title_html = e($name);
	if (count($words) > 1) {
		$last = array_pop($words);
		$title_html = e(implode(' ', $words)) . ' <em class="gold-text">' . e($last) . '</em>';
	}

	return [
		'id' => $id,
		'name' => $name,
		'title_html' => $title_html,
		'slug' => $slug,
		'url' => $url,
		'developer' => $dev_name,
		'developer_slug' => $project['dev_slug'] ?? '',
		'location' => $location,
		'type' => $type,
		'price' => $price_num,
		'price_label' => $price_label,
		'beds' => $beds_display,
		'handover' => 'Ready · Immediate handover',
		'photos' => $photos,
		'side_photos' => $side_photos,
		'overview' => $overview_paras,
		'amenities' => $amenities,
		'brochure' => $brochure,
		'floorplan' => $floorplan,
		'payplan' => $payplan,
		'map_url' => $map_url,
		'similar' => $similar,
		'whatsapp' => ready_property_whatsapp_url($name, $price_num, $url),
		'meta' => [
			'meta_title' => trim((string) ($project['meta_title'] ?? '')) ?: ($name . ' | Ready to Move In | DRE Homes'),
			'meta_description' => trim((string) ($project['meta_description'] ?? '')) ?: (mb_substr($overview_paras[0] ?? '', 0, 160)),
			'meta_keyword' => trim((string) ($project['meta_keyword'] ?? '')) ?: ($name . ', Ready to move in Dubai, ' . $location),
			'meta_og_img' => !empty($photos[0]['src']) ? $photos[0]['src'] : '',
		],
	];
}

/* =====================================================================
   New UI: Our Media (our-media.php) — Admin > Media (manage-media.php)
   ===================================================================== */

// Admin > Media > Media Content: banner image, "Media Buzz", "Our Visual Stories", subtitle
function get_media_content()
{
	return db_row("SELECT * FROM tbl_media_content WHERE is_active = '1' LIMIT 1") ?: [];
}

// Photo categories of the media wall: key => label (order of the filter buttons)
function media_categories()
{
	return [
		'awards' => 'Awards',
		'press' => 'Leadership & press',
		'culture' => 'Celebrations',
		'gifts' => 'Thank-yous & gifts',
		'partners' => 'Developer partners',
		'inside' => 'Inside DRE',
	];
}

// Caption of a photo from its alt text (the admin's "Alt tag"); the file name when the alt is empty.
// "the-Black-ONYX-Award-2024" -> "The Black ONYX Award 2024", "Qurat Ul Ain - Binghatti London" -> "Qurat Ul Ain Binghatti London",
// "DRE AT PALM JEBEL ALI BY NAKHEEL" -> "DRE at Palm Jebel Ali by Nakheel"
function media_caption($alt, $file = '')
{
	$text = trim(html_entity_decode((string) $alt, ENT_QUOTES, 'UTF-8'));
	if ($text === '') {
		// "media_img_7747_GulfLand-1.jpg" -> "GulfLand"
		$text = preg_replace('/^media_img_\d+_/i', '', (string) $file);
		$text = preg_replace('/(\.(jpe?g|png|webp|gif))+$/i', '', $text);
		$text = preg_replace('/[-_ ]?\d+$/', '', $text);
	}
	if (!preg_match('/\s/', $text)) {
		$text = str_replace(['-', '_'], ' ', $text); // slug-like alt text
	}
	$text = preg_replace('/\s+-\s+/', ' ', $text);
	$text = preg_replace('/\b(img|image)\b/i', '', $text);
	$text = trim(preg_replace('/\s+/', ' ', $text));
	// ALL CAPS -> Title Case, keeping short codes (DRE, UAE, Q4, BNW …) in capitals
	if ($text !== '' && mb_strtoupper($text) === $text && preg_match('/[A-Z]{4,}/', $text)) {
		$keep = ['DRE', 'UAE', 'DLD', 'BNW', 'CSR', 'BBQ', 'II', 'III'];
		$small = ['at', 'by', 'of', 'the', 'and', 'in', 'on', 'to', 'for', 'with', 'x'];
		$words = explode(' ', mb_strtolower($text));
		foreach ($words as $i => $word) {
			$bare = trim($word, '()');
			if (in_array(mb_strtoupper($bare), $keep, true) || preg_match('/^q\d$/', $bare)) {
				$words[$i] = str_replace($bare, mb_strtoupper($bare), $word);
			} elseif ($i === 0 || !in_array($bare, $small, true)) {
				$words[$i] = str_replace($bare, mb_convert_case($bare, MB_CASE_TITLE), $word);
			}
		}
		$text = implode(' ', $words);
	}
	return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}

// Category of a photo from its caption (first matching rule wins; "inside" when nothing matches)
function media_category($caption)
{
	$rules = [
		'culture' => 'celebrat|hawai|women|christmas|anniversar|years of|iftar|iftaar|ramadan|ramdan|birthday|fun ?day|summer fun|party|\bmatch\b|bbq|beekeeper|diwali|\bcsr\b|footprint|new year|cricket league|unity',
		'gifts' => 'goodies|\bgifts?\b|thank',
		'awards' => 'award|achievement|agency of the year|top (real estate )?broker|best broker|dubai top broker|trusted|recogni[sz]|certificate|winner|great place|true broker|top 50|top agent',
		'press' => 'qurat|mudasir|podcast|article|magazine|passion|times|news|al burai|maktoum|newsletter|golden visa',
		'partners' => '\bx\b|developer|propert(y|ies)|visit|partnership|meet|deal with|gateway|show|invitation|nakheel|emaar|bnw|sobha|damac|aldar|danube|imtiaz|binghatti|ellington|samana|elysian|evolution|bayut|bayz|eagle hills|sharafi|gulf ?land|al noor|avenew|amis|luxe|mildan|\bdld\b|serbia|shahrukh|palm jebel',
	];
	foreach ($rules as $category => $pattern) {
		if (preg_match('/' . $pattern . '/i', (string) $caption)) {
			return $category;
		}
	}
	return 'inside';
}

// Admin > Media > Manage Media: every published photo, newest first, ready for the wall.
// Photos whose file is missing from the upload folder are left out. "k" = position on the wall (the page's JS uses it).
function get_media_photos()
{
	$folder = dirname(__DIR__) . '/admin_nsrdwsc/assets/media/media-image/';
	$photos = [];
	foreach (db_all("SELECT media_id, media_img, media_img_alt_tag FROM tbl_media WHERE is_active = '1' ORDER BY media_id DESC") as $row) {
		$file = trim((string) $row['media_img']);
		if ($file === '' || !is_file($folder . $file)) {
			continue;
		}
		$caption = media_caption($row['media_img_alt_tag'], $file);
		$photos[] = [
			'k' => count($photos),
			'src' => BASE_URL_IMG . 'media-image/' . rawurlencode($file),
			'caption' => $caption,
			'category' => media_category($caption),
		];
	}
	return $photos;
}

// Highlight stories: runs of $min_photos or more photos in a row with the same caption (one event uploaded together).
// The first photo of the run is the cover.
function get_media_stories(array $photos, $min_photos = 7)
{
	$stories = [];
	$count = count($photos);
	for ($i = 0; $i < $count; $i = $end + 1) {
		$end = $i;
		while ($end + 1 < $count && $photos[$end + 1]['caption'] === $photos[$i]['caption']) {
			$end++;
		}
		if ($end - $i + 1 >= $min_photos) {
			$stories[] = [
				'title' => $photos[$i]['caption'],
				'cover' => $photos[$i]['src'],
				'keys' => range($photos[$i]['k'], $photos[$end]['k']),
			];
		}
	}
	return $stories;
}

// Number of photos per category (all categories present, 0 when empty)
function get_media_category_counts(array $photos)
{
	$counts = array_fill_keys(array_keys(media_categories()), 0);
	foreach ($photos as $photo) {
		$counts[$photo['category']]++;
	}
	return $counts;
}

/* =====================================================================
   New UI: Company News (company-news.php) — Admin > Company News (manage-company-news.php)
   ===================================================================== */

// Admin > Company news > Edit Company News Content: banner image, page heading, inner heading
function get_company_news_content()
{
	$content = db_row("SELECT * FROM tbl_company_news_content WHERE is_active = '1' LIMIT 1") ?: [];
	$img = trim((string) ($content['company_news_cont_img'] ?? ''));
	$folder = dirname(__DIR__) . '/admin_nsrdwsc/assets/media/company-news/';
	if ($img !== '' && is_file($folder . $img)) {
		$content['banner_url'] = BASE_URL_IMG . 'company-news/' . rawurlencode($img);
	} else {
		$content['banner_url'] = BASE_URL . 'images/company-news-banner.jpg';
	}
	return $content;
}

// Categories of company news: key => label
function company_news_categories()
{
	return [
		'milestones' => 'Milestones',
		'leaders' => 'Leadership',
		'awards' => 'Awards',
		'partners' => 'Partnerships',
		'culture' => 'Life at DRE',
		'news' => 'Market insights',
	];
}

// Category of a news item from its heading and short description
function company_news_category($heading, $short_descp = '')
{
	$h = trim(preg_replace('/\s+/u', ' ', (string) $heading));
	$text = $h . ' ' . trim(preg_replace('/\s+/u', ' ', (string) $short_descp));

	// 1. Life at DRE / Culture / Celebrations / Charity
	if (preg_match('/\b(national day|diwali|ramadan|iftar|iftaar|al noor|culture|charity|celebration|cricket)\b/i', $h) && !preg_match('/broker awards/i', $h)) {
		return 'culture';
	}

	// 2. Awards with explicit "award" takes precedence over milestone
	if (preg_match('/(partner excellence award|broker awards|annual awards)/i', $h)) {
		return 'awards';
	}

	// 3. Milestones
	if (preg_match('/(headquarters|18-year legacy|xclusive by design|legacy of trust|new headquarters|introducing the new dre|milestone|a new chapter|unveils marea)/i', $h) && !preg_match('/exclusive sales partner/i', $h)) {
		return 'milestones';
	}

	// 4. Partnerships
	if (preg_match('/(strategic partnership|exclusive sales partner|seal(ed)? the deal|beekeepers|brokers program 2024|fin forbes|partners with|signs deal|mou with|in collaboration with)/i', $h)) {
		return 'partners';
	}

	// 5. Leadership & Founders
	if (preg_match('/(qurat|mudasir|founders?|visionary leadership|entrepreneur middle east|ahlan dubai|female industry pioneer|inspiring journey|journey of strength|woman leader|most influential|insights from dre)/i', $h)) {
		if (!preg_match('/signature award 2026/i', $h)) {
			return 'leaders';
		}
	}

	// 6. Awards & Recognition
	if (preg_match('/(award|recogni[sz]|honour|honor|trophy|winner|win\b|top[- ]performing|ranking|trubroker|best workplaces|top broker|top agent|top performance|great place to work|top companies to work|gold ranking|champion|top 50|veterans|appreciated|most creative campaign|envoy|elite performer|benchmarking success|most trusted brokerage|video contest)/i', $h)) {
		return 'awards';
	}

	// 7. Leadership fallback
	if (preg_match('/(leader|leadership|ceo|clo|interview|podcast)/i', $h)) {
		return 'leaders';
	}

	// 8. Partnerships fallback
	if (preg_match('/(partnership|partner|sponsor|collaboration)/i', $h)) {
		return 'partners';
	}

	return 'news';
}

// Plain excerpt from text
function company_news_excerpt($text, $words = 28)
{
	$plain = plain_text($text);
	$parts = preg_split('/\s+/u', $plain, -1, PREG_SPLIT_NO_EMPTY);
	if (count($parts) <= $words) {
		return $plain;
	}
	return rtrim(implode(' ', array_slice($parts, 0, $words)), ' ,;:.') . '…';
}

// Admin > Company news > Manage Company News: all active news, newest first
function get_company_news_items()
{
	$folder = dirname(__DIR__) . '/admin_nsrdwsc/assets/media/company-news/';
	$categories = company_news_categories();
	$items = [];
	$rows = db_all("SELECT * FROM tbl_news WHERE is_active = '1' ORDER BY news_posted_on DESC, news_id DESC");
	foreach ($rows as $row) {
		$heading = trim((string) $row['news_heading']);
		$slug = trim((string) $row['news_slug']);
		$short = plain_text($row['news_short_descp']);
		$excerpt = $short !== '' ? $short : company_news_excerpt($row['news_long_descp'], 28);
		$cat = company_news_category($heading, $short);
		$posted_time = strtotime((string) $row['news_posted_on']) ?: time();

		$thumb_file = trim((string) $row['news_thumb_img']);
		$thumb_url = ($thumb_file !== '' && is_file($folder . 'thumb/' . $thumb_file))
			? BASE_URL_IMG . 'company-news/thumb/' . rawurlencode($thumb_file)
			: BASE_URL . 'images/company-news-placeholder.jpg';

		$inner_file = trim((string) $row['news_inner_img']);
		$inner_url = ($inner_file !== '' && is_file($folder . 'inner/' . $inner_file))
			? BASE_URL_IMG . 'company-news/inner/' . rawurlencode($inner_file)
			: $thumb_url;

		$items[] = [
			'id' => (int) $row['news_id'],
			'heading' => $heading,
			'slug' => $slug,
			'url' => BASE_URL . 'company-news/' . rawurlencode($slug),
			'thumb' => $thumb_url,
			'thumb_alt' => trim((string) $row['news_thumb_img_alt']) ?: $heading,
			'inner' => $inner_url,
			'inner_alt' => trim((string) $row['news_inner_img_alt']) ?: $heading,
			'short_descp' => $short,
			'excerpt' => $excerpt,
			'long_descp' => (string) $row['news_long_descp'],
			'posted_on' => (string) $row['news_posted_on'],
			'date_formatted' => date('M d, Y', $posted_time),
			'year' => (int) date('Y', $posted_time),
			'category' => $cat,
			'category_label' => $categories[$cat] ?? 'News',
			'is_spotlight' => $row['news_markspot'] === '1',
			'meta_title' => (string) ($row['meta_title'] ?? ''),
			'meta_keyword' => (string) ($row['meta_keyword'] ?? ''),
			'meta_description' => (string) ($row['meta_description'] ?? ''),
			'meta_tags' => (string) ($row['meta_tags'] ?? ''),
		];
	}
	return $items;
}

// Spotlight stories: 1 main featured + up to 3 side stories
function get_company_news_spotlight(array $news_items)
{
	if (!$news_items) {
		return ['main' => null, 'side' => []];
	}
	$spotlights = array_values(array_filter($news_items, fn($n) => $n['is_spotlight']));
	$main = $spotlights[0] ?? $news_items[0];
	$main_id = $main['id'];

	// Side items: remaining spotlights first, then newest news items
	$side = [];
	foreach ($spotlights as $s) {
		if ($s['id'] !== $main_id && count($side) < 3) {
			$side[] = $s;
		}
	}
	if (count($side) < 3) {
		foreach ($news_items as $n) {
			if ($n['id'] !== $main_id && !in_array($n['id'], array_column($side, 'id'), true) && count($side) < 3) {
				$side[] = $n;
			}
		}
	}

	return [
		'main' => $main,
		'side' => $side,
	];
}

// Category counts for all categories
function get_company_news_category_counts(array $news_items)
{
	$counts = array_fill_keys(array_keys(company_news_categories()), 0);
	foreach ($news_items as $n) {
		if (isset($counts[$n['category']])) {
			$counts[$n['category']]++;
		}
	}
	return $counts;
}

// News items grouped by year (newest year first)
function get_company_news_year_groups(array $news_items)
{
	$groups = [];
	foreach ($news_items as $n) {
		$groups[$n['year']][] = $n;
	}
	krsort($groups);
	return $groups;
}

// Stats for hero counter block
function get_company_news_stats(array $news_items, array $category_counts, array $year_groups)
{
	$years = array_keys($year_groups);
	return [
		'total' => count($news_items),
		'awards' => $category_counts['awards'] ?? 0,
		'since_year' => $years ? min($years) : 2018,
		'latest_date' => $news_items[0]['date_formatted'] ?? date('M d, Y'),
	];
}

// Single news item by slug (or empty if not found)
function get_single_company_news($slug)
{
	$slug = trim((string) $slug);
	if ($slug === '') {
		return null;
	}
	$folder = dirname(__DIR__) . '/admin_nsrdwsc/assets/media/company-news/';
	$row = db_row("SELECT * FROM tbl_news WHERE news_slug = ? AND is_active = '1' LIMIT 1", [$slug]);
	if (!$row) {
		return null;
	}

	$categories = company_news_categories();
	$heading = trim((string) $row['news_heading']);
	$short = plain_text($row['news_short_descp']);
	$cat = company_news_category($heading, $short);
	$posted_time = strtotime((string) $row['news_posted_on']) ?: time();

	$thumb_file = trim((string) $row['news_thumb_img']);
	$thumb_url = ($thumb_file !== '' && is_file($folder . 'thumb/' . $thumb_file))
		? BASE_URL_IMG . 'company-news/thumb/' . rawurlencode($thumb_file)
		: BASE_URL . 'images/company-news-placeholder.jpg';

	$inner_file = trim((string) $row['news_inner_img']);
	$inner_url = ($inner_file !== '' && is_file($folder . 'inner/' . $inner_file))
		? BASE_URL_IMG . 'company-news/inner/' . rawurlencode($inner_file)
		: $thumb_url;

	// Reading time estimate (approx 200 words per min)
	$word_count = str_word_count(strip_tags((string) $row['news_long_descp']));
	$read_mins = max(1, (int) round($word_count / 200));

	// Related stories (3 latest items excluding this one)
	$related = [];
	$rel_rows = db_all("SELECT * FROM tbl_news WHERE is_active = '1' AND news_id <> ? ORDER BY news_posted_on DESC LIMIT 3", [(int) $row['news_id']]);
	foreach ($rel_rows as $rel) {
		$r_thumb = trim((string) $rel['news_thumb_img']);
		$r_thumb_url = ($r_thumb !== '' && is_file($folder . 'thumb/' . $r_thumb))
			? BASE_URL_IMG . 'company-news/thumb/' . rawurlencode($r_thumb)
			: BASE_URL . 'images/company-news-placeholder.jpg';
		$r_cat = company_news_category($rel['news_heading'], $rel['news_short_descp']);
		$related[] = [
			'heading' => trim((string) $rel['news_heading']),
			'url' => BASE_URL . 'company-news/' . rawurlencode(trim((string) $rel['news_slug'])),
			'thumb' => $r_thumb_url,
			'thumb_alt' => trim((string) $rel['news_thumb_img_alt']) ?: trim((string) $rel['news_heading']),
			'date_formatted' => date('M d, Y', strtotime((string) $rel['news_posted_on']) ?: time()),
			'category_label' => $categories[$r_cat] ?? 'News',
		];
	}

	return [
		'id' => (int) $row['news_id'],
		'heading' => $heading,
		'slug' => $slug,
		'url' => BASE_URL . 'company-news/' . rawurlencode($slug),
		'thumb' => $thumb_url,
		'thumb_alt' => trim((string) $row['news_thumb_img_alt']) ?: $heading,
		'inner' => $inner_url,
		'inner_alt' => trim((string) $row['news_inner_img_alt']) ?: $heading,
		'short_descp' => $short,
		'long_descp' => (string) $row['news_long_descp'],
		'posted_on' => (string) $row['news_posted_on'],
		'date_formatted' => date('M d, Y', $posted_time),
		'read_mins' => $read_mins,
		'year' => (int) date('Y', $posted_time),
		'category' => $cat,
		'category_label' => $categories[$cat] ?? 'News',
		'meta_title' => trim((string) ($row['meta_title'] ?? '')) ?: $heading . ' | DRE Homes',
		'meta_keyword' => (string) ($row['meta_keyword'] ?? ''),
		'meta_description' => trim((string) ($row['meta_description'] ?? '')) ?: ($short ?: company_news_excerpt($row['news_long_descp'], 28)),
		'meta_tags' => (string) ($row['meta_tags'] ?? ''),
		'related' => $related,
	];
}

// -----------------------------------------------------------------------------
// The DRE Podcast: episodes, YouTube parsing, behind-the-scenes gallery
// Managed in Admin > Manage Podcast (tbl_podcast)
// -----------------------------------------------------------------------------

// Extracts YouTube video ID (11 chars) and optional start time in seconds from various URL formats
function podcast_youtube_info($url)
{
	$url = trim((string) $url);
	$id = '';
	$start = 0;

	if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $url, $m)) {
		$id = $m[1];
	} elseif (preg_match('~^[A-Za-z0-9_-]{11}$~', $url)) {
		$id = $url;
	}

	if (preg_match('~[?&#](?:t|start)=([0-9hms]+)~i', $url, $tm)) {
		$raw = strtolower($tm[1]);
		if (ctype_digit($raw)) {
			$start = (int) $raw;
		} else {
			$s = 0;
			if (preg_match('~(\d+)h~', $raw, $h)) $s += (int) $h[1] * 3600;
			if (preg_match('~(\d+)m~', $raw, $m2)) $s += (int) $m2[1] * 60;
			if (preg_match('~(\d+)s~', $raw, $s2)) $s += (int) $s2[1];
			$start = $s;
		}
	}

	return [
		'id' => $id,
		'start' => $start,
	];
}

// All active podcast episodes for the website, with main episode first, then sorted by position
function get_podcast_episodes()
{
	$rows = db_all(
		"SELECT * FROM tbl_podcast
		 WHERE is_active = '1'
		 ORDER BY CASE WHEN is_main = 1 THEN 0 ELSE 1 END, order_no ASC, podcast_id DESC"
	);

	$episodes = [];
	foreach ($rows as $i => $row) {
		$id = (int) $row['podcast_id'];
		$title = trim((string) $row['podcast_title']);
		$descp = trim(preg_replace('/\s+/', ' ', strip_tags((string) $row['podcast_descp'])));
		$yt_url = trim((string) $row['youtube_link']);
		$yt = podcast_youtube_info($yt_url);
		$thumb = trim((string) $row['podcast_thumb']);

		// Resolve thumbnail URL with fallback to YouTube maxres/hq thumbnail
		if ($thumb !== '' && is_file(__DIR__ . '/../admin_nsrdwsc/assets/media/podcast/thumb/' . $thumb)) {
			$thumb_url = BASE_URL_IMG . 'podcast/thumb/' . rawurlencode($thumb);
		} elseif ($yt['id'] !== '') {
			$thumb_url = 'https://img.youtube.com/vi/' . rawurlencode($yt['id']) . '/maxresdefault.jpg';
		} else {
			$thumb_url = BASE_URL . 'images/podcast-placeholder.jpg';
		}

		$order_no = (int) $row['order_no'];
		$is_main = (int) $row['is_main'] === 1;

		$time = strtotime((string) $row['updated_on']);
		if (!$time || $time <= 0) {
			$time = strtotime((string) $row['tstp']) ?: time();
		}
		$date_formatted = date('M d, Y', $time);

		// Tag: EP #1, EP #2, or Episode
		$tag = $order_no > 0 ? ('EP #' . $order_no) : 'Episode';

		$episodes[] = [
			'id' => $id,
			'title' => $title,
			'descp' => $descp,
			'sub' => $descp,
			'thumb' => $thumb_url,
			'youtube_link' => $yt_url ?: ($yt['id'] !== '' ? 'https://www.youtube.com/watch?v=' . $yt['id'] : ''),
			'yt_id' => $yt['id'],
			'yt_start' => $yt['start'],
			'order_no' => $order_no,
			'is_main' => $is_main,
			'tag' => $tag,
			'date' => $date_formatted,
			'index' => $i,
		];
	}

	return $episodes;
}

// Main episode: either the first episode in sorted order, or null
function get_podcast_main_episode(array $episodes)
{
	return $episodes[0] ?? null;
}

// Behind the scenes / on set photos for the Podcast page
function get_podcast_behind_scenes_photos()
{
	$rows = db_all(
		"SELECT media_id, media_img, media_img_alt_tag
		 FROM tbl_media
		 WHERE is_active = '1' AND (media_img_alt_tag LIKE '%podcast%' OR media_img LIKE '%podcast%')
		 ORDER BY media_id ASC"
	);

	$photos = [];
	$n = 1;
	foreach ($rows as $row) {
		$img = trim((string) $row['media_img']);
		if ($img === '') continue;
		$alt = trim((string) $row['media_img_alt_tag']) ?: ('Behind the mic — photo ' . $n);
		$photos[] = [
			'id' => (int) $row['media_id'],
			'src' => BASE_URL_IMG . 'media-image/' . rawurlencode($img),
			'alt' => $alt,
			'title' => 'Behind the mic — photo ' . $n,
		];
		$n++;
	}

	return $photos;
}

// -----------------------------------------------------------------------------
// Client Testimonials: banner content, published reviews, advisors, topic counts
// Managed in Admin > Manage Testimonial (tbl_testimonial) and
// Admin > Edit Testimonial Content (tbl_testimonial_content)
// -----------------------------------------------------------------------------

// Admin > Testimonial > Edit Testimonial Content (banner image, heading)
function get_testimonial_content()
{
	$row = db_row("SELECT * FROM tbl_testimonial_content WHERE is_active = '1' ORDER BY test_cont_id DESC LIMIT 1");
	if (!$row) {
		return [
			'heading' => 'Voices of Trust',
			'heading_html' => 'Voices of <em class="gold-text">trust</em>',
			'banner' => '',
			'img' => '',
		];
	}

	$heading = trim((string) $row['test_heading']) ?: 'Voices of Trust';
	$heading_words = preg_split('/\s+/u', $heading);
	$heading_last = array_pop($heading_words);
	$heading_html = e(implode(' ', $heading_words)) . ' <em class="gold-text">' . e(mb_strtolower($heading_last)) . '</em>';

	$img = trim((string) $row['test_img']);
	$banner_url = $img !== '' ? BASE_URL_IMG . 'testimonial/' . rawurlencode($img) : '';

	return [
		'heading' => $heading,
		'heading_html' => $heading_html,
		'banner' => $banner_url,
		'img' => $img,
	];
}

// All published testimonials, praised advisors matching tbl_team, topic counts, year counts, and stats
function get_testimonials_data()
{
	$testimonial_rows = db_all("SELECT * FROM tbl_testimonial WHERE is_active = '1' ORDER BY test_posted_on DESC, test_id DESC");
	$team_rows = db_all("SELECT * FROM tbl_team WHERE is_active = '1' ORDER BY order_no IS NULL, order_no ASC, team_id ASC");

	// Admin text -> plain text
	$plain = fn($html) => trim(preg_replace('/\s+/u', ' ', str_replace("\xC2\xA0", ' ', html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], ' ', (string) $html)), ENT_QUOTES, 'UTF-8'))));

	// Topics a review is about
	$review_topics = [
		'buy' => ['Buying', '\b(buy|buying|bought|buyer|purchas\w*|invest\w*|off[- ]plan|handover|mortgage)\b'],
		'rent' => ['Renting', '\b(rent\w*|tenan\w*|lease\w*|leasing|landlord|maintenance|moved in|move[- ]in)\b'],
		'sell' => ['Selling', '\b(sell\w*|sold|resold|resale|resell\w*)\b'],
	];

	// Team advisors that reviews can mention
	$advisors = [];
	$first_names = [];
	foreach ($team_rows as $t) {
		$first_names[] = mb_strtolower(strtok(trim((string) $t['team_name']), ' '));
	}
	$first_name_count = array_count_values($first_names);

	foreach ($team_rows as $t) {
		$slug = trim((string) $t['team_slug']);
		if ($slug === '') continue;
		$name = trim(preg_replace('/\s+/', ' ', (string) $t['team_name']));
		$first = strtok($name, ' ');
		$patterns = ['/\b' . preg_quote($name, '/') . '\b/iu'];
		if (($first_name_count[mb_strtolower($first)] ?? 0) === 1 && mb_strlen($first) > 2) {
			$patterns[] = '/\b' . preg_quote($first, '/') . '\b/iu';
		}
		$advisors[$slug] = [
			'slug' => $slug,
			'name' => $name,
			'first' => $first,
			'role' => trim((string) $t['team_desg']),
			'img' => BASE_URL_IMG . 'team/' . rawurlencode(trim((string) $t['team_img'])),
			'patterns' => $patterns,
			'reviews' => 0,
		];
	}

	$find_advisors = function ($text) use (&$advisors) {
		$found = [];
		foreach ($advisors as $slug => $adv) {
			foreach ($adv['patterns'] as $pattern) {
				if (preg_match($pattern, $text, $m, PREG_OFFSET_CAPTURE)) {
					$found[$slug] = $m[0][1];
					break;
				}
			}
		}
		asort($found);
		return array_keys($found);
	};

	$initials = function ($name) {
		$words = preg_split('/\s+/u', trim(preg_replace('/^(mr|mrs|ms|miss|dr)\.?\s+/i', '', trim((string) $name))), -1, PREG_SPLIT_NO_EMPTY);
		if (!$words) return '';
		$letters = mb_substr($words[0], 0, 1) . (count($words) > 1 ? mb_substr(end($words), 0, 1) : '');
		return mb_strtoupper($letters);
	};

	$reviews = [];
	$topic_counts = ['all' => count($testimonial_rows), 'buy' => 0, 'rent' => 0, 'sell' => 0, 'ag' => 0];
	$year_counts = [];

	foreach ($testimonial_rows as $row) {
		$text = $plain($row['test_descp']);
		$topics = [];
		foreach ($review_topics as $key => [$label, $pattern]) {
			if (preg_match('/' . $pattern . '/i', $text)) {
				$topics[$key] = $label;
			}
		}
		$mentioned = $find_advisors($text);
		foreach ($mentioned as $slug) {
			$advisors[$slug]['reviews']++;
		}
		$time = strtotime((string) $row['test_posted_on']);
		$year = $time ? (int) date('Y', $time) : 0;
		$date_str = $time ? date('M d, Y', $time) : '';

		foreach (array_keys($topics) as $key) {
			$topic_counts[$key]++;
		}
		if ($mentioned) {
			$topic_counts['ag']++;
		}
		if ($year) {
			$year_counts[$year] = ($year_counts[$year] ?? 0) + 1;
		}

		$rating = (float) $row['test_rating'];
		if ($rating <= 0 || $rating > 5) {
			$rating = 5.0;
		}

		$reviews[] = [
			'id' => (int) $row['test_id'],
			'name' => trim((string) $row['test_client_name']),
			'initials' => $initials($row['test_client_name']),
			'text' => $text,
			'date' => $date_str,
			'year' => $year,
			'topics' => $topics,
			'advisors' => $mentioned,
			'rating' => $rating,
		];
	}

	krsort($year_counts);

	$praised = array_filter($advisors, fn($a) => $a['reviews'] > 0);
	uasort($praised, fn($a, $b) => $b['reviews'] <=> $a['reviews']);
	$top_advisors = array_slice($praised, 0, 8, true);

	$ratings = array_filter(array_column($reviews, 'rating'));
	$average_rating = $ratings ? round(array_sum($ratings) / count($ratings), 1) : 5;

	$review_stats = [
		'total' => count($reviews),
		'advisors' => count($praised),
		'since' => $year_counts ? min(array_keys($year_counts)) : date('Y'),
		'latest' => $reviews[0]['date'] ?? '',
		'rating' => $average_rating == (int) $average_rating ? (int) $average_rating : $average_rating,
	];

	return [
		'reviews' => $reviews,
		'latest_reviews' => array_slice($reviews, 0, 3),
		'advisors' => $advisors,
		'top_advisors' => $top_advisors,
		'topic_counts' => $topic_counts,
		'year_counts' => $year_counts,
		'stats' => $review_stats,
	];
}

// Guides: fetches a guide row by ID or slug from tbl_guide
function get_guide($id_or_slug)
{
	if (is_numeric($id_or_slug)) {
		return db_row("SELECT * FROM tbl_guide WHERE guide_id = ? AND is_active = '1' LIMIT 1", [(int) $id_or_slug]) ?: [];
	}
	return db_row("SELECT * FROM tbl_guide WHERE guide_slug = ? AND is_active = '1' LIMIT 1", [trim((string) $id_or_slug)]) ?: [];
}

// Guides: fetches all active steps for a guide from tbl_guide_img
function get_guide_steps($guide_id)
{
	$guide_id = (int) $guide_id;
	if ($guide_id <= 0) {
		return [];
	}
	return db_all("SELECT * FROM tbl_guide_img WHERE guide_id = ? AND is_active = '1' ORDER BY guide_img_id ASC", [$guide_id]) ?: [];
}

// Guides: formats guide heading / titles nicely
function guide_format_title($title)
{
	$t = trim((string) $title);
	if ($t === '') {
		return '';
	}
	$letters_only = preg_replace('/[^a-zA-Z]/', '', $t);
	if ($letters_only !== '' && ctype_upper($letters_only)) {
		return ucwords(strtolower($t));
	}
	return $t;
}

// Guides: formats step descriptions (supports multiple paragraphs or list items into numbered lists, or keeps existing lists/markup)
function render_guide_step_description($html)
{
	$trimmed = trim((string) $html);
	if ($trimmed === '') {
		return '';
	}
	if (stripos($trimmed, 'gd-sub') !== false) {
		return $trimmed;
	}
	if (preg_match_all('/<li\b[^>]*>(.*?)<\/li>/is', $trimmed, $li_matches)) {
		if (count($li_matches[1]) > 1) {
			$out = '<ol class="gd-sub">' . "\n";
			foreach ($li_matches[1] as $i => $li) {
				$li_clean = trim(strip_tags($li, '<p><a><b><strong><em><i><br>'));
				if (stripos($li_clean, '<p>') !== 0) {
					$li_clean = '<p>' . $li_clean . '</p>';
				}
				$out .= '  <li><span>' . ($i + 1) . '</span>' . $li_clean . '</li>' . "\n";
			}
			$out .= '</ol>';
			return $out;
		}
	}
	if (preg_match_all('/<p\b[^>]*>(.*?)<\/p>/is', $trimmed, $matches)) {
		if (count($matches[1]) > 1) {
			$out = '<ol class="gd-sub">' . "\n";
			foreach ($matches[1] as $i => $p) {
				$out .= '  <li><span>' . ($i + 1) . '</span><p>' . trim($p) . '</p></li>' . "\n";
			}
			$out .= '</ol>';
			return $out;
		} elseif (count($matches[1]) === 1) {
			return '<p>' . trim($matches[1][0]) . '</p>';
		}
	}
	if (stripos($trimmed, '<ol') !== false || stripos($trimmed, '<ul') !== false) {
		return $trimmed;
	}
	return '<p>' . $trimmed . '</p>';
}

// Communities directory: fetches content, featured collections, popular and all active communities
function get_communities_page_data()
{
	global $conn;

	// 1. Page content from tbl_community_content
	$content = db_row("SELECT * FROM tbl_community_content WHERE is_active = '1' ORDER BY comm_cont_id ASC LIMIT 1") ?: [];

	// 2. Featured collections from tbl_community where comm_featured = '1'
	$featured_raw = db_all(
		"SELECT comm_id, comm_heading, comm_thumb_img, comm_long_descp, comm_slug 
		 FROM tbl_community 
		 WHERE is_active = '1' AND comm_featured = '1' 
		 ORDER BY comm_id ASC"
	);
	$featured = [];
	foreach ($featured_raw as $f) {
		$img = trim((string) $f['comm_thumb_img']);
		$desc = trim(strip_tags((string) $f['comm_long_descp']));
		$slug = trim((string) $f['comm_slug']);
		$url = $slug !== '' ? page_link($slug, 'community/' . $slug) : page_link('offplan-properties') . '?comm_id=' . (int) $f['comm_id'];
		$featured[] = [
			'id' => (int) $f['comm_id'],
			'heading' => trim((string) $f['comm_heading']),
			'desc' => $desc,
			'image' => $img !== '' ? BASE_URL_IMG . 'community/thumb/' . rawurlencode($img) : '',
			'url' => $url,
		];
	}

	// 3. All active communities with non-empty slug, along with property count
	$sql = "SELECT c.*, COALESCE(p.prop_count, 0) as prop_count 
	        FROM tbl_community c 
	        LEFT JOIN (
	            SELECT prj_comm_id, COUNT(*) as prop_count 
	            FROM tbl_proj 
	            WHERE is_active = '1' 
	            GROUP BY prj_comm_id
	        ) p ON c.comm_id = p.prj_comm_id 
	        WHERE c.is_active = '1' AND c.comm_slug != '' 
	        ORDER BY c.comm_heading ASC";
	$communities_raw = db_all($sql);

	$communities = [];
	$region_counts = [
		'Dubai' => 0,
		'Abu Dhabi' => 0,
		'Northern Emirates' => 0,
		'International' => 0,
	];
	$available_letters = [];

	foreach ($communities_raw as $c) {
		$name = trim((string) $c['comm_heading']);
		$loc = trim((string) $c['comm_location']);
		$slug = trim((string) $c['comm_slug']);
		$img_file = trim((string) $c['comm_thumb_img']);

		// Determine Region
		$region = 'International';
		if ($loc === '' || strcasecmp($loc, 'Dubai') === 0) {
			$region = 'Dubai';
		} elseif (strcasecmp($loc, 'Abu Dhabi') === 0) {
			$region = 'Abu Dhabi';
		} else {
			$northern = ['Sharjah', 'Ras Al Khaimah', 'Ras Al Khimah', 'Fujairah', 'Ajman', 'Umm Al Quwain', 'Siniya Island', 'UAE'];
			foreach ($northern as $n) {
				if (strcasecmp($loc, $n) === 0) {
					$region = 'Northern Emirates';
					break;
				}
			}
		}

		if (isset($region_counts[$region])) {
			$region_counts[$region]++;
		}

		$first_letter = strtoupper(substr($name, 0, 1));
		if ($first_letter >= 'A' && $first_letter <= 'Z') {
			$available_letters[$first_letter] = true;
		}

		$img_url = $img_file !== '' ? BASE_URL_IMG . 'community/thumb/' . rawurlencode($img_file) : BASE_URL . 'assets/img/comm-fallback.jpg';

		$communities[] = [
			'id' => (int) $c['comm_id'],
			'name' => $name,
			'slug' => $slug,
			'url' => page_link($slug, 'community/' . $slug),
			'location' => $loc !== '' ? $loc : 'Dubai',
			'region' => $region,
			'first_letter' => $first_letter,
			'prop_count' => (int) $c['prop_count'],
			'image' => $img_url,
			'is_popular' => (int) $c['prop_count'] >= 30,
		];
	}

	// Top popular communities (excluding generic 'Dubai' ID 89)
	$popular_candidates = array_filter($communities, fn($c) => $c['id'] !== 89);
	usort($popular_candidates, fn($a, $b) => $b['prop_count'] <=> $a['prop_count'] ?: strcmp($a['name'], $b['name']));
	$popular = array_slice($popular_candidates, 0, 5);

	return [
		'content' => $content,
		'featured' => $featured,
		'popular' => $popular,
		'communities' => $communities,
		'region_counts' => $region_counts,
		'total_count' => count($communities),
		'available_letters' => array_keys($available_letters),
	];
}

/**
 * Parses raw bedroom string (e.g. '1,2,3,4', 'STD,1', '5') into min, max, list, and display text.
 */
function parse_community_bedrooms($raw_beds) {
	$raw = strtoupper(trim((string)$raw_beds));
	if ($raw === '') {
		return ['bmin' => null, 'bmax' => null, 'blist' => '', 'text' => '—'];
	}
	$parts = preg_split('/[\s,]+/', $raw);
	$numbers = [];
	$has_studio = false;
	foreach ($parts as $p) {
		$p = trim($p);
		if ($p === 'STD' || $p === 'STUDIO' || $p === '0') {
			$has_studio = true;
			$numbers[] = 0;
		} elseif (is_numeric($p)) {
			$numbers[] = (int)$p;
		}
	}
	if (empty($numbers) && !$has_studio) {
		return ['bmin' => null, 'bmax' => null, 'blist' => '', 'text' => $raw];
	}
	sort($numbers);
	$unique = array_values(array_unique($numbers));
	$min = min($unique);
	$max = max($unique);
	$blist = implode(' ', $unique);

	if ($has_studio && $max == 0) {
		$text = 'Studio';
	} elseif ($has_studio && $max > 0) {
		$text = 'Studio - ' . $max;
	} elseif ($min === $max) {
		$text = (string)$min;
	} else {
		$text = $min . ' - ' . $max;
	}
	return [
		'bmin' => $min,
		'bmax' => $max,
		'blist' => $blist,
		'text' => $text,
	];
}

/**
 * Compact currency formatter: AED 0.6M, AED 1.5M, etc.
 */
function format_community_price_compact($num) {
	if (!$num || $num <= 0) return 'On request';
	if ($num >= 1000000000) return 'AED ' . round($num / 1000000000, 1) . 'B';
	if ($num >= 1000000) return 'AED ' . round($num / 1000000, 1) . 'M';
	if ($num >= 100000) return 'AED ' . round($num / 1000000, 1) . 'M';
	if ($num >= 1000) return 'AED ' . round($num / 1000, 1) . 'K';
	return 'AED ' . number_format($num);
}

/**
 * Parses drive time string like '5 Mins', '10 Minutes', '15 km'.
 */
function parse_community_drive_time($distance_str) {
	$str = trim((string)$distance_str);
	if ($str === '') return ['num' => '', 'unit' => ''];
	if (preg_match('/^(\d+)\s*(mins?|minutes?|km|hours?|hrs?)?/i', $str, $m)) {
		$num = $m[1];
		$raw_unit = strtolower($m[2] ?? 'min');
		if (str_starts_with($raw_unit, 'hr') || str_starts_with($raw_unit, 'hour')) {
			$unit = 'hr';
		} elseif (str_starts_with($raw_unit, 'km')) {
			$unit = 'km';
		} else {
			$unit = 'min';
		}
		return ['num' => $num, 'unit' => $unit];
	}
	return ['num' => $str, 'unit' => ''];
}

/**
 * Wraps the last word of a heading in <em class="gold-text">...</em>.
 */
function highlight_last_word($title) {
	$words = explode(' ', trim((string)$title));
	if (count($words) <= 1) {
		return '<em class="gold-text">' . htmlspecialchars(trim((string)$title)) . '</em>';
	}
	$last = array_pop($words);
	return htmlspecialchars(implode(' ', $words)) . ' <em class="gold-text">' . htmlspecialchars($last) . '</em>';
}

/**
 * Fetches all dynamic data required for a single community page.
 */
function get_single_community_data($slug) {
	global $conn;

	$slug = trim((string) $slug);
	if ($slug === '') {
		$slug = 'palm-jumeirah';
	}

	$safe_slug = mysqli_real_escape_string($conn, $slug);
	$comm = db_row("SELECT * FROM tbl_community WHERE comm_slug = '$safe_slug' AND is_active = '1' LIMIT 1");
	if (!$comm && is_numeric($slug)) {
		$comm_id = (int) $slug;
		$comm = db_row("SELECT * FROM tbl_community WHERE comm_id = $comm_id AND is_active = '1' LIMIT 1");
	}
	if (!$comm) {
		return null;
	}

	// Trim whitespace from string columns
	$comm['comm_heading'] = trim((string) $comm['comm_heading']);
	$comm['comm_location'] = trim((string) $comm['comm_location']);
	$comm['comm_loc_main_heading'] = trim((string) $comm['comm_loc_main_heading']);
	$comm['comm_map_heading'] = trim((string) $comm['comm_map_heading']);

	$comm_id = (int) $comm['comm_id'];

	// 1. Highlights from tbl_community_loc
	$highlights = db_all("SELECT * FROM tbl_community_loc WHERE comm_id = $comm_id AND is_active = '1' ORDER BY comm_loc_id ASC") ?: [];

	// 2. Nearby locations from tbl_community_map
	$nearby = db_all("SELECT * FROM tbl_community_map WHERE comm_id = $comm_id AND is_active = '1' ORDER BY comm_map_id ASC") ?: [];

	// 3. Projects in this community joined with tbl_developer
	$projects_raw = db_all("
		SELECT p.prj_id, p.proj_name, p.proj_slug, p.prj_thumb_img, p.proj_price, p.proj_bedroom, 
		       p.proj_status, p.proj_whatsaapno, p.proj_overview, p.proj_markfeat, 
		       d.dev_heading, d.dev_id, d.dev_slug
		FROM tbl_proj p
		LEFT JOIN tbl_developer d ON p.prj_dev_id = d.dev_id
		WHERE p.prj_comm_id = $comm_id AND p.is_active = '1'
		ORDER BY p.proj_markfeat DESC, p.prj_id DESC
	") ?: [];

	$projects = [];
	$dev_counts = [];
	$min_price = 0;

	foreach ($projects_raw as $p) {
		$dev_name = trim((string) ($p['dev_heading'] ?? ''));
		if ($dev_name === '') {
			$dev_name = 'DRE Real Estate';
		}
		$dev_counts[$dev_name] = ($dev_counts[$dev_name] ?? 0) + 1;

		// Parse price
		$raw_price = trim((string) $p['proj_price']);
		$digits = preg_replace('/[^0-9]/', '', $raw_price);
		$num_price = is_numeric($digits) && (int)$digits > 10000 ? (int)$digits : 0;
		if ($num_price > 0 && ($min_price === 0 || $num_price < $min_price)) {
			$min_price = $num_price;
		}

		// Parse bedrooms
		$bed_info = parse_community_bedrooms($p['proj_bedroom'] ?? '');

		// Image
		$thumb = trim((string) $p['prj_thumb_img']);
		$thumb_url = $thumb !== '' ? BASE_URL_IMG . 'project/thumb/' . rawurlencode($thumb) : BASE_URL . 'assets/img/card-placeholder.jpg';

		$p_slug = trim((string) $p['proj_slug']);
		$p_url = $p_slug !== '' ? page_link($p_slug, 'property/' . $p_slug) : '#';

		$projects[] = [
			'id' => (int) $p['prj_id'],
			'name' => trim((string) $p['proj_name']),
			'slug' => $p_slug,
			'url' => $p_url,
			'developer' => $dev_name,
			'image' => $thumb_url,
			'price_raw' => $raw_price,
			'price_num' => $num_price,
			'price_display' => $num_price > 0 ? 'AED ' . number_format($num_price) : '<em>Price on request</em>',
			'bmin' => $bed_info['bmin'],
			'bmax' => $bed_info['bmax'],
			'blist' => $bed_info['blist'],
			'bedroom_display' => $bed_info['text'],
			'status' => trim((string) ($p['proj_status'] ?: 'Off plan')),
			'whatsapp' => trim((string) ($p['proj_whatsaapno'] ?: '971585837346')),
		];
	}

	// Sort developers by project count descending
	arsort($dev_counts);

	// 4. Other communities (5 active communities excluding this one)
	$other_raw = db_all("
		SELECT c.comm_id, c.comm_heading, c.comm_slug, c.comm_location, c.comm_thumb_img
		FROM tbl_community c
		WHERE c.is_active = '1' AND c.comm_id <> $comm_id
		ORDER BY c.comm_featured DESC, c.comm_id ASC
		LIMIT 5
	") ?: [];

	$other_communities = [];
	foreach ($other_raw as $oc) {
		$oc_slug = trim((string) $oc['comm_slug']);
		$oc_thumb = trim((string) $oc['comm_thumb_img']);
		$oc_img = $oc_thumb !== '' ? BASE_URL_IMG . 'community/thumb/' . rawurlencode($oc_thumb) : BASE_URL . 'assets/img/comm-fallback.jpg';
		$other_communities[] = [
			'id' => (int) $oc['comm_id'],
			'name' => trim((string) $oc['comm_heading']),
			'slug' => $oc_slug,
			'url' => page_link($oc_slug, 'community/' . $oc_slug),
			'location' => trim((string) $oc['comm_location']) ?: 'Dubai',
			'image' => $oc_img,
		];
	}

	// Banner image
	$inner_img = trim((string) $comm['comm_inner_img']);
	$thumb_img = trim((string) $comm['comm_thumb_img']);
	if ($inner_img !== '') {
		$hero_bg = BASE_URL_IMG . 'community/inner/' . rawurlencode($inner_img);
	} elseif ($thumb_img !== '') {
		$hero_bg = BASE_URL_IMG . 'community/thumb/' . rawurlencode($thumb_img);
	} else {
		$hero_bg = BASE_URL . 'assets/img/comm-fallback.jpg';
	}

	// Location main image
	$loc_img = trim((string) $comm['comm_loc_main_img']);
	$loc_img_url = $loc_img !== '' ? BASE_URL_IMG . 'community/location/' . rawurlencode($loc_img) : $hero_bg;

	// Google Maps URL
	$map_raw = trim((string) $comm['comm_map_iframe']);
	if ($map_raw !== '') {
		if (preg_match('/src=["\']([^"\']+)["\']/', $map_raw, $m)) {
			$map_url = $m[1];
		} else {
			$map_url = $map_raw;
		}
	} else {
		$map_url = 'https://maps.google.com/maps?q=' . urlencode($comm['comm_heading'] . ', ' . ($comm['comm_location'] ?: 'Dubai')) . '&t=&z=13&ie=UTF8&iwloc=&output=embed';
	}

	// Subtitle / lead paragraph
	$sub = '';
	if (!empty($comm['comm_long_descp'])) {
		$clean_desc = trim(strip_tags($comm['comm_long_descp']));
		if (preg_match('/^([^\.\?!]+[\.\?!])/', $clean_desc, $m)) {
			$sub = $m[1];
		} else {
			$sub = mb_substr($clean_desc, 0, 160) . '...';
		}
	}
	if ($sub === '') {
		$sub = 'Discover luxury properties, iconic lifestyle, and master-planned living in ' . $comm['comm_heading'] . '.';
	}

	// SEO tags
	$meta_tags = [
		'meta_title' => $comm['meta_title'] ?: ($comm['comm_heading'] . ' Properties | DRE Homes'),
		'meta_keyword' => $comm['meta_keyword'] ?: '',
		'meta_description' => $comm['meta_description'] ?: strip_tags(mb_substr($comm['comm_long_descp'] ?: $comm['comm_heading'], 0, 160)),
		'meta_tags' => $comm['meta_tags'] ?: '',
		'comm_thumb_img' => $comm['comm_thumb_img'],
	];

	return [
		'comm' => $comm,
		'sub' => $sub,
		'hero_bg' => $hero_bg,
		'loc_img' => $loc_img_url,
		'map_url' => $map_url,
		'highlights' => $highlights,
		'nearby' => $nearby,
		'projects' => $projects,
		'dev_counts' => $dev_counts,
		'project_count' => count($projects),
		'developer_count' => count($dev_counts),
		'min_price' => $min_price,
		'other_communities' => $other_communities,
		'meta_tags' => $meta_tags,
	];
}

/**
 * Developer directory page data
 */
function get_developers_page_data()
{
	// 1. Content / banner
	$content = db_row("SELECT * FROM tbl_developer_content WHERE is_active = '1' ORDER BY dev_cont_id ASC LIMIT 1") ?: [];

	$banner_img = trim((string) ($content['dev_cont_img'] ?? ''));
	if ($banner_img !== '') {
		$hero_bg = BASE_URL_IMG . 'developer/' . rawurlencode($banner_img);
	} else {
		$hero_bg = BASE_URL . 'images/home-sec/offplan-property.jpg';
	}

	$heading = trim((string) ($content['dev_cont_heading'] ?? '')) ?: 'Developers';

	// 2. SEO Tags (page_id = 12)
	$meta_tags = db_row("SELECT * FROM tbl_tags WHERE page_id = 12 AND is_active = '1' LIMIT 1") ?: [];

	// 3. Exclusive partners (dev_showonhomepage = '1' and dev_order_no in (1, 2))
	$exclusive_raw = db_all("
		SELECT * FROM tbl_developer 
		WHERE is_active = '1' AND dev_showonhomepage = '1' 
		ORDER BY dev_order_no ASC 
		LIMIT 2
	") ?: [];

	$exclusive_partners = [];
	foreach ($exclusive_raw as $ep) {
		$slug = trim((string) $ep['dev_slug']);
		$logo = trim((string) $ep['dev_img']);
		$logo_url = $logo !== '' ? BASE_URL_IMG . 'developer/' . rawurlencode($logo) : '';

		// Image card
		$deal_img = '';
		if ($slug === 'sharafi-development') {
			$deal_img = BASE_URL . 'images/home-partner/Sharafi-deal1.jpeg';
		} elseif ($slug === 'gulf-land-property') {
			$deal_img = BASE_URL . 'images/home-partner/gulf-deal1.jpeg';
		} elseif (!empty($ep['dev_homp_img'])) {
			$deal_img = BASE_URL_IMG . 'developer/homepage-image/' . rawurlencode($ep['dev_homp_img']);
		} elseif (!empty($ep['dev_innr_img'])) {
			$deal_img = BASE_URL_IMG . 'developer/inner-image/' . rawurlencode($ep['dev_innr_img']);
		} else {
			$deal_img = $hero_bg;
		}

		$desc = trim(strip_tags((string) $ep['dev_short_descp']));
		if ($desc === '') {
			$desc = trim(strip_tags((string) $ep['dev_long_descp']));
			$desc = mb_substr($desc, 0, 140) . '...';
		}

		$exclusive_partners[] = [
			'id' => (int) $ep['dev_id'],
			'name' => trim((string) $ep['dev_heading']),
			'slug' => $slug,
			'url' => page_link($slug, 'developer/' . $slug),
			'logo' => $logo_url,
			'image' => $deal_img,
			'desc' => $desc,
		];
	}

	// 4. Featured partners map (for data-top ordering)
	$featured_raw = db_all("
		SELECT dev_id, dev_slug 
		FROM tbl_developer 
		WHERE is_active = '1' AND dev_showonhomepage = '1'
		ORDER BY dev_order_no ASC
	") ?: [];

	$top_map = [];
	$top_rank = 0;
	foreach ($featured_raw as $fr) {
		$top_map[$fr['dev_slug']] = $top_rank++;
	}

	// 5. All active developers (order by name)
	$devs_raw = db_all("
		SELECT dev_id, dev_heading, dev_slug, dev_img, dev_short_descp, dev_long_descp, dev_showonhomepage, dev_order_no
		FROM tbl_developer 
		WHERE is_active = '1' 
		ORDER BY dev_heading ASC
	") ?: [];

	$developers = [];
	$letters_present = [];

	foreach ($devs_raw as $d) {
		$slug = trim((string) $d['dev_slug']);
		$name = trim((string) $d['dev_heading']);
		$first_letter = mb_strtoupper(mb_substr($name, 0, 1));
		if (!preg_match('/[A-Z]/', $first_letter)) {
			$first_letter = '#';
		}
		$letters_present[$first_letter] = true;

		$logo = trim((string) $d['dev_img']);
		$logo_url = $logo !== '' ? BASE_URL_IMG . 'developer/' . rawurlencode($logo) : BASE_URL . 'assets/img/comm-fallback.jpg';

		$desc = trim(strip_tags((string) $d['dev_short_descp']));
		if ($desc === '') {
			$desc = trim(strip_tags((string) $d['dev_long_descp']));
			if (mb_strlen($desc) > 130) {
				$desc = mb_substr($desc, 0, 127) . '...';
			}
		}

		$top = isset($top_map[$slug]) ? $top_map[$slug] : null;
		$is_top = ($top !== null);

		$developers[] = [
			'id' => (int) $d['dev_id'],
			'name' => $name,
			'slug' => $slug,
			'url' => page_link($slug, 'developer/' . $slug),
			'logo' => $logo_url,
			'desc' => $desc,
			'letter' => $first_letter,
			'top' => $top,
			'is_top' => $is_top,
		];
	}

	// Sort initially by top (featured first, then name) so default page load displays featured cards first
	usort($developers, function ($a, $b) {
		$topA = $a['top'] !== null ? $a['top'] : 999;
		$topB = $b['top'] !== null ? $b['top'] : 999;
		if ($topA !== $topB) {
			return $topA - $topB;
		}
		return strcasecmp($a['name'], $b['name']);
	});

	return [
		'content' => $content,
		'hero_bg' => $hero_bg,
		'heading' => $heading,
		'meta_tags' => $meta_tags,
		'exclusive_partners' => $exclusive_partners,
		'developers' => $developers,
		'total_count' => count($developers),
		'letters' => array_keys($letters_present),
	];
}

/**
 * Developer profile / detail page data
 */
function get_single_developer_data($slug)
{
	global $conn;
	$safe_slug = mysqli_real_escape_string($conn, trim((string) $slug));
	if ($safe_slug === '') {
		return null;
	}

	$dev = db_row("SELECT * FROM tbl_developer WHERE dev_slug = '$safe_slug' AND is_active = '1' LIMIT 1");
	if (!$dev && is_numeric($slug)) {
		$dev_id = (int) $slug;
		$dev = db_row("SELECT * FROM tbl_developer WHERE dev_id = $dev_id AND is_active = '1' LIMIT 1");
	}
	if (!$dev) {
		return null;
	}

	$dev_id = (int) $dev['dev_id'];

	// 1. Projects for this developer
	$projects_raw = db_all("
		SELECT p.*, c.comm_heading, c.comm_slug 
		FROM tbl_proj p 
		LEFT JOIN tbl_community c ON p.prj_comm_id = c.comm_id 
		WHERE p.prj_dev_id = $dev_id AND p.is_active = '1' 
		ORDER BY p.proj_posted_on DESC, p.prj_id DESC
	") ?: [];

	$projects = [];
	$comm_counts = [];
	$comm_images = [];
	$min_price = 0;

	foreach ($projects_raw as $p) {
		$prj_slug = trim((string) $p['proj_slug']);
		$comm_name = trim((string) ($p['comm_heading'] ?: ($p['proj_location'] ?: 'Dubai')));
		$comm_slug = trim((string) ($p['comm_slug'] ?: ''));

		// Thumb image
		$thumb = trim((string) $p['prj_thumb_img']);
		$img_url = $thumb !== '' ? BASE_URL_IMG . 'project/thumb/' . rawurlencode($thumb) : BASE_URL . 'assets/img/comm-fallback.jpg';

		// Track community count and representative image
		$comm_counts[$comm_name] = ($comm_counts[$comm_name] ?? 0) + 1;
		if (!isset($comm_images[$comm_name]) && $thumb !== '') {
			$comm_images[$comm_name] = $img_url;
		}

		// Price handling
		$price_num = 0;
		$clean_price = preg_replace('/[^0-9]/', '', (string) $p['proj_price']);
		if (is_numeric($clean_price) && (int) $clean_price >= 100000) {
			$price_num = (int) $clean_price;
		} else {
			$combined = (string) $p['proj_start_price'] . ' ' . (string) $p['proj_price'];
			if (preg_match('/([\d\.]+)\s*(?:million|m\b)/i', $combined, $m)) {
				$price_num = (int) (floatval($m[1]) * 1000000);
			} elseif (preg_match('/([\d\.]+)\s*(?:k\b|thousand)/i', $combined, $m)) {
				$price_num = (int) (floatval($m[1]) * 1000);
			} else {
				$clean_start = preg_replace('/[^0-9]/', '', (string) $p['proj_start_price']);
				if (is_numeric($clean_start) && (int) $clean_start >= 100000) {
					$price_num = (int) $clean_start;
				}
			}
		}

		if ($price_num >= 100000) {
			$price_display = 'AED ' . number_format($price_num);
			if ($min_price === 0 || $price_num < $min_price) {
				$min_price = $price_num;
			}
		} else {
			$price_display = '<em>Price on request</em>';
		}

		// Bedroom range parsing
		$raw_bed = trim((string) ($p['proj_bedroom_on_card'] ?: $p['proj_bedroom']));
		$bmin = null;
		$bmax = null;
		$blist = '';
		$bedroom_display = $raw_bed;

		if (preg_match_all('/\d+/', $raw_bed, $matches) && !empty($matches[0])) {
			$bed_nums = array_map('intval', $matches[0]);
			$bmin = min($bed_nums);
			$bmax = max($bed_nums);
			$blist = implode(' ', $bed_nums);
			if (stripos($raw_bed, 'studio') !== false) {
				$bmin = 0;
				$blist = '0 ' . $blist;
				$bedroom_display = 'Studio - ' . $bmax;
			} elseif ($bmin === $bmax) {
				$bedroom_display = (string) $bmin;
			} else {
				$bedroom_display = $bmin . ' - ' . $bmax;
			}
		} elseif (stripos($raw_bed, 'studio') !== false) {
			$bmin = 0;
			$bmax = 0;
			$blist = '0';
			$bedroom_display = 'Studio';
		}

		if ($bedroom_display === '') {
			$bedroom_display = 'On request';
		}

		$status = trim((string) ($p['proj_status'] ?: 'Off plan'));
		if ($status === '') {
			$status = 'Off plan';
		}

		$wa_raw = trim((string) ($p['proj_whatsaapno'] ?: $p['proj_mobileno']));
		$wa_clean = preg_replace('/[^0-9]/', '', $wa_raw);
		if ($wa_clean === '') {
			$wa_clean = '971585837346';
		}

		$overview = trim(strip_tags((string) $p['proj_overview']));
		if ($overview === '') {
			$overview = "Discover luxury living and iconic design with " . trim((string) $p['proj_name']) . " by " . $dev['dev_heading'] . ".";
		} elseif (mb_strlen($overview) > 160) {
			$overview = mb_substr($overview, 0, 157) . '...';
		}

		$projects[] = [
			'id' => (int) $p['prj_id'],
			'name' => trim((string) $p['proj_name']),
			'slug' => $prj_slug,
			'url' => page_link($prj_slug, 'property/' . $prj_slug),
			'community' => $comm_name,
			'comm_slug' => $comm_slug,
			'image' => $img_url,
			'price_num' => $price_num,
			'price_display' => $price_display,
			'bmin' => $bmin,
			'bmax' => $bmax,
			'blist' => $blist,
			'bedroom_display' => $bedroom_display,
			'status' => $status,
			'whatsapp' => $wa_clean,
			'location' => trim((string) ($p['proj_location'] ?: $comm_name)),
			'overview' => $overview,
		];
	}

	// 2. Sort communities by count descending for .ctiles
	arsort($comm_counts);
	$ctiles = [];
	foreach ($comm_counts as $cname => $ccount) {
		$ctiles[] = [
			'name' => $cname,
			'count' => $ccount,
			'image' => $comm_images[$cname] ?? BASE_URL . 'assets/img/comm-fallback.jpg',
		];
	}

	// 3. Why choose us points
	$choose_us_points = db_all("
		SELECT * FROM tbl_dev_chooseus 
		WHERE dev_id = $dev_id AND is_active = '1' 
		ORDER BY choose_us_id ASC
	") ?: [];

	$choose_us_img = trim((string) $dev['choose_us_img']);
	$choose_us_img_url = $choose_us_img !== '' 
		? BASE_URL_IMG . 'developer/choose-us/' . rawurlencode($choose_us_img)
		: '';

	// 4. Hero background image
	$hero_bg = '';
	if (!empty($projects[0]['image'])) {
		$hero_bg = $projects[0]['image'];
	} elseif ($choose_us_img_url !== '') {
		$hero_bg = $choose_us_img_url;
	} elseif (!empty($dev['dev_innr_img'])) {
		$hero_bg = BASE_URL_IMG . 'developer/inner-image/' . rawurlencode($dev['dev_innr_img']);
	} elseif (!empty($dev['dev_homp_img'])) {
		$hero_bg = BASE_URL_IMG . 'developer/homepage-image/' . rawurlencode($dev['dev_homp_img']);
	} else {
		$hero_bg = BASE_URL . 'images/home-sec/offplan-property.jpg';
	}

	// Developer logo
	$dev_logo = trim((string) $dev['dev_img']);
	$dev_logo_url = $dev_logo !== '' ? BASE_URL_IMG . 'developer/' . rawurlencode($dev_logo) : '';

	// 5. Other developers (up to 10 for bottom grid)
	$other_raw = db_all("
		SELECT dev_id, dev_heading, dev_slug, dev_img 
		FROM tbl_developer 
		WHERE is_active = '1' AND dev_id <> $dev_id AND dev_showonhomepage = '1'
		ORDER BY dev_order_no ASC 
		LIMIT 10
	") ?: [];

	$other_developers = [];
	foreach ($other_raw as $od) {
		$od_slug = trim((string) $od['dev_slug']);
		$od_logo = trim((string) $od['dev_img']);
		$other_developers[] = [
			'name' => trim((string) $od['dev_heading']),
			'slug' => $od_slug,
			'url' => page_link($od_slug, 'developer/' . $od_slug),
			'logo' => $od_logo !== '' ? BASE_URL_IMG . 'developer/' . rawurlencode($od_logo) : BASE_URL . 'assets/img/comm-fallback.jpg',
		];
	}

	// 6. Subtitle / summary
	$sub = trim(strip_tags((string) $dev['dev_short_descp']));
	if ($sub === '') {
		$long = trim(strip_tags((string) $dev['dev_long_descp']));
		if (preg_match('/^([^\.\?!]+[\.\?!])/', $long, $m)) {
			$sub = $m[1];
		} else {
			$sub = mb_substr($long, 0, 160) . '...';
		}
	}
	if ($sub === '') {
		$sub = 'Explore master-planned communities, luxury residences, and iconic architecture by ' . $dev['dev_heading'] . '.';
	}

	// 7. Total active developers count
	$tot_devs = db_row("SELECT COUNT(*) as c FROM tbl_developer WHERE is_active = '1'");
	$total_developers_count = (int) ($tot_devs['c'] ?? 149);

	// 8. Meta tags
	$meta_tags = [
		'meta_title' => $dev['meta_title'] ?: ($dev['dev_heading'] . ' Properties & Projects | DRE Homes'),
		'meta_keyword' => $dev['meta_keyword'] ?: '',
		'meta_description' => $dev['meta_description'] ?: $sub,
		'meta_tags' => $dev['meta_tags'] ?: '',
	];

	return [
		'dev' => $dev,
		'sub' => $sub,
		'hero_bg' => $hero_bg,
		'logo' => $dev_logo_url,
		'projects' => $projects,
		'project_count' => count($projects),
		'community_count' => count($comm_counts),
		'comm_counts' => $comm_counts,
		'ctiles' => $ctiles,
		'min_price' => $min_price,
		'choose_us_points' => $choose_us_points,
		'choose_us_img' => $choose_us_img_url,
		'choose_us_desc' => trim((string) $dev['choose_us_desc']),
		'other_developers' => $other_developers,
		'total_developers_count' => $total_developers_count,
		'meta_tags' => $meta_tags,
	];
}

// -----------------------------------------------------------------------------
// BLOGS: Dynamic category inference, listing page data, and single article data
// -----------------------------------------------------------------------------

function blog_category($blog)
{
	static $static_map = null;
	if ($static_map === null) {
		$static_map = [
			'freehold-vs-leasehold-property-in-dubai' => 'Guides',
			'damac-introduces-the-lifestyle-collection-making-apartment-ownership-more-accessible-across-dubai' => 'Launches & Projects',
			'the-yards-by-beyond-new-landmark-in-city-of-arabia' => 'Launches & Projects',
			'emaar-south-community-guide-2026-lifestyle-amenities-and-investment-insights' => 'Guides',
			'uae-passport-retains-title-as-the-world-most-powerful-in-2026' => 'Visa & Regulation',
			'off-plan-vs-ready-property-in-dubai-which-is-better-for-roi' => 'Guides',
			'top-budget-friendly-areas-to-buy-property-in-dubai-2026' => 'Guides',
			'mortgage-vs-payment-plan-which-is-more-beneficial' => 'Guides',
			'dubai-metro-blue-line-route-stations-and-property-impact' => 'Market Insights',
			'palm-jebel-ali-relaunch-everything-you-need-to-know' => 'Market Insights',
			'dubai-real-estate-q2-2026-market-report' => 'Market Insights',
			'dubai-real-estate-market-outlook-h2-2026' => 'Market Insights',
			'top-10-schools-in-dubai-and-where-to-live-nearby' => 'Dubai Life',
			'dubai-golden-visa-complete-guide-for-property-investors-2026' => 'Visa & Regulation',
			'how-to-open-a-bank-account-in-dubai-as-a-non-resident' => 'Guides',
			'difc-living-everything-you-need-to-know' => 'Dubai Life',
			'best-waterfront-communities-to-live-in-dubai' => 'Dubai Life',
			'next-stop-for-rixos-hotels-abu-dhabi' => 'Launches & Projects',
			'mercedes-benz-places-at-binghatti-city-a-new-urban-vision-for-dubai' => 'Launches & Projects',
			'mohammed-bin-rashid-launches-dh100-billion-zabeel-district-at-difc' => 'Launches & Projects',
			'richmind-completes-oystra-phase-1-sell-out-unveils-phase-2-amid-surging-global-demand' => 'Launches & Projects',
			'dh100-billion-difc-expansion-drives-strong-demand-for-zabeel-district-homes' => 'Market Insights',
			'yas-developers-launches-boutique-luxury-project-casa-altia' => 'Launches & Projects',
			'affordable-dubai-properties-with-monthly-payment-plan' => 'Market Insights',
			'dre-x-appointed-as-exclusive-sales-and-market-rollout-partner-for-sharafi-marea-residences' => 'DRE News',
		];
	}
	$slug = trim((string) ($blog['blog_slug'] ?? ''));
	if (isset($static_map[$slug])) {
		return $static_map[$slug];
	}
	$heading = strtolower(trim((string) ($blog['blog_heading'] ?? '')));
	$slug_lower = strtolower($slug);
	$text = $heading . ' ' . $slug_lower;

	if (preg_match('/\b(guide|how to|step-by-step|checklist|things to know|what is|freehold vs leasehold|tips for|buying process|faqs)\b/i', $text)) {
		return 'Guides';
	}
	if (preg_match('/\b(visa|golden visa|green visa|passport|law|regulation|tax|rera|dld|ejari|tenancy contract|legal|rules)\b/i', $text)) {
		return 'Visa & Regulation';
	}
	if (preg_match('/\b(dre|dre homes|dre-x|award|awards|brokerage|partnership|milestone|appointed|ranks|recogni[sz]ed)\b/i', $text)) {
		return 'DRE News';
	}
	if (preg_match('/\b(launch|launches|project|residence|residences|tower|heights|villas|mansions|estates|yards|apartments for sale|binghatti|damac|emaar|sobha|danube|nakheel|omniyat|meraas|aldar|samana|azizi|ellison)\b/i', $text)) {
		return 'Launches & Projects';
	}
	if (preg_match('/\b(lifestyle|living|life|school|schools|restaurant|beach|park|metro|dubai life|luxury living|neighborhood|attractions|entertainment|nightlife)\b/i', $text)) {
		return 'Dubai Life';
	}
	return 'Market Insights';
}

function get_blogs_page_data()
{
	// 1. Page hero content from tbl_blog_content
	$content = db_row("SELECT * FROM tbl_blog_content WHERE is_active = '1' ORDER BY blog_cont_id ASC LIMIT 1");
	$hero_heading = trim((string) ($content['blog_cont_heading'] ?? '')) ?: 'The DRE Journal';
	$hero_sub = trim((string) ($content['blog_cont_sub_heading'] ?? '')) ?: 'The latest real estate news, insights and guides from DRE Homes — off-plan launches, market trends, investment tips and community highlights across Dubai.';

	$hero_bg = '';
	if (!empty($content['blog_cont_img'])) {
		$hero_bg = BASE_URL_IMG . 'blog/' . $content['blog_cont_img'];
	} else {
		$hero_bg = BASE_URL_IMG . 'blog/inner/blog_inner_img_6070_dubai-real-estate-img.jpg';
	}

	// 2. SEO & Meta tags
	$meta_tags = get_seo_tags(13);
	if (empty($meta_tags['meta_title'])) {
		$meta_tags['meta_title'] = 'Blogs | DRE Homes';
	}
	if (empty($meta_tags['meta_description'])) {
		$meta_tags['meta_description'] = $hero_sub;
	}

	// 3. Query all active blogs
	$rows = db_all(
		"SELECT blog_id, blog_heading, blog_slug, blog_thumb_img, blog_thumb_img_alt, 
		        blog_short_descp, blog_inner_img, blog_inner_img_alt, blog_long_descp, 
		        blog_posted_on, tstp 
		 FROM tbl_blog 
		 WHERE is_active = '1' 
		 ORDER BY blog_posted_on DESC, blog_id DESC"
	);

	$blogs = [];
	$years = [];
	$topics = [
		'Launches & Projects' => 0,
		'Market Insights' => 0,
		'Guides' => 0,
		'Visa & Regulation' => 0,
		'DRE News' => 0,
		'Dubai Life' => 0,
	];
	$min_year = 2026;

	foreach ($rows as $row) {
		$slug = trim((string) $row['blog_slug']);
		$cat = blog_category($row);
		if (isset($topics[$cat])) {
			$topics[$cat]++;
		} else {
			$topics[$cat] = 1;
		}

		$date_val = !empty($row['blog_posted_on']) && $row['blog_posted_on'] !== '0000-00-00'
			? $row['blog_posted_on']
			: substr((string) $row['tstp'], 0, 10);
		$ts = strtotime($date_val) ?: time();
		$year = date('Y', $ts);
		if ((int) $year > 2000 && (int) $year < $min_year) {
			$min_year = (int) $year;
		}
		$years[$year] = ($years[$year] ?? 0) + 1;

		$thumb_img = !empty($row['blog_thumb_img'])
			? BASE_URL_IMG . 'blog/thumb/' . $row['blog_thumb_img']
			: (!empty($row['blog_inner_img'])
				? BASE_URL_IMG . 'blog/inner/' . $row['blog_inner_img']
				: BASE_URL_IMG . 'blog/inner/blog_inner_img_6070_dubai-real-estate-img.jpg');

		$inner_img = !empty($row['blog_inner_img'])
			? BASE_URL_IMG . 'blog/inner/' . $row['blog_inner_img']
			: $thumb_img;

		$words = str_word_count(strip_tags((string) ($row['blog_long_descp'] ?: $row['blog_short_descp'])));
		$read_mins = max(2, min(10, (int) round($words / 200)));

		$short_desc = trim(strip_tags((string) $row['blog_short_descp']));
		if ($short_desc === '') {
			$plain_long = trim(strip_tags((string) $row['blog_long_descp']));
			if (preg_match('/^([^\.\?!]+[\.\?!])/', $plain_long, $m)) {
				$short_desc = $m[1];
			} else {
				$short_desc = mb_substr($plain_long, 0, 140) . '...';
			}
		}

		$blogs[] = [
			'id' => (int) $row['blog_id'],
			'heading' => trim((string) $row['blog_heading']),
			'slug' => $slug,
			'url' => BASE_URL . 'our-blogs/' . $slug,
			'category' => $cat,
			'thumb_img' => $thumb_img,
			'inner_img' => $inner_img,
			'thumb_alt' => trim((string) $row['blog_thumb_img_alt']) ?: trim((string) $row['blog_heading']),
			'inner_alt' => trim((string) $row['blog_inner_img_alt']) ?: trim((string) $row['blog_heading']),
			'short_desc' => $short_desc,
			'date_raw' => $date_val,
			'date_formatted' => date('M d, Y', $ts),
			'year' => $year,
			'timestamp' => $ts,
			'read_time_mins' => $read_mins,
			'read_time' => $read_mins . ' min read',
		];
	}

	krsort($years);

	// Featured stories (top 3)
	$featured_main = $blogs[0] ?? null;
	$featured_side = array_slice($blogs, 1, 2);

	// Essential guides (up to 4 from Guides category)
	$guides = [];
	foreach ($blogs as $b) {
		if ($b['category'] === 'Guides') {
			$guides[] = $b;
			if (count($guides) >= 4) break;
		}
	}
	if (count($guides) < 4) {
		foreach ($blogs as $b) {
			if ($b['category'] !== 'Guides' && !in_array($b, $guides, true)) {
				$guides[] = $b;
				if (count($guides) >= 4) break;
			}
		}
	}

	$latest_date = !empty($blogs[0]['date_formatted']) ? $blogs[0]['date_formatted'] : date('M d, Y');

	return [
		'hero_heading' => $hero_heading,
		'hero_sub' => $hero_sub,
		'hero_bg' => $hero_bg,
		'meta_tags' => $meta_tags,
		'blogs' => $blogs,
		'total_count' => count($blogs),
		'total_topics' => count(array_filter($topics)),
		'writing_since' => $min_year,
		'latest_date' => $latest_date,
		'featured_main' => $featured_main,
		'featured_side' => $featured_side,
		'years' => $years,
		'topics' => $topics,
		'guides' => $guides,
	];
}

function get_single_blog_data($slug)
{
	$slug = trim(str_replace('/', '', (string) $slug));
	if ($slug === '') {
		return [];
	}

	// Try by slug, or fallback by ID if numeric
	$blog = db_row(
		"SELECT * FROM tbl_blog WHERE TRIM(blog_slug) = ? AND is_active = '1' LIMIT 1",
		[$slug]
	);
	if (!$blog && is_numeric($slug)) {
		$blog = db_row(
			"SELECT * FROM tbl_blog WHERE blog_id = ? AND is_active = '1' LIMIT 1",
			[(int) $slug]
		);
	}
	if (!$blog) {
		return [];
	}

	$id = (int) $blog['blog_id'];
	$heading = trim((string) $blog['blog_heading']);
	$clean_slug = trim((string) $blog['blog_slug']);
	$cat = blog_category($blog);

	$date_val = !empty($blog['blog_posted_on']) && $blog['blog_posted_on'] !== '0000-00-00'
		? $blog['blog_posted_on']
		: substr((string) $blog['tstp'], 0, 10);
	$ts = strtotime($date_val) ?: time();
	$date_formatted = date('M d, Y', $ts);

	$words = str_word_count(strip_tags((string) ($blog['blog_long_descp'] ?: $blog['blog_short_descp'])));
	$read_mins = max(2, min(10, (int) round($words / 200)));

	$inner_img = !empty($blog['blog_inner_img'])
		? BASE_URL_IMG . 'blog/inner/' . $blog['blog_inner_img']
		: (!empty($blog['blog_thumb_img'])
			? BASE_URL_IMG . 'blog/thumb/' . $blog['blog_thumb_img']
			: BASE_URL_IMG . 'blog/inner/blog_inner_img_6070_dubai-real-estate-img.jpg');

	$thumb_img = !empty($blog['blog_thumb_img'])
		? BASE_URL_IMG . 'blog/thumb/' . $blog['blog_thumb_img']
		: $inner_img;

	$current_url = BASE_URL . 'our-blogs/' . $clean_slug;

	// Table of contents parsing & ID injection into headings
	$content_html = (string) $blog['blog_long_descp'];

	// Normalize bold section titles saved from TinyMCE into semantic <h2> tags
	$content_html = preg_replace_callback(
		"/<p[^>]*>(\s*(?:<span[^>]*font-size:\s*1[3456]pt[^>]*>)?\s*<strong>\s*(?:<span[^>]*font-size:\s*1[3456]pt[^>]*>)?\s*([^<]{3,80}?)\s*(?:<\/span>)?\s*<\/strong>\s*(?:<\/span>)?\s*)<\/p>/i",
		function($m) {
			$title = trim(strip_tags($m[2]));
			if (str_ends_with($title, '.') && str_word_count($title) > 6) {
				return $m[0];
			}
			$id = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title));
			$id = trim($id, '-');
			return "<h2 id=\"{$id}\">{$title}</h2>";
		},
		$content_html
	);

	$toc = [];
	if (preg_match_all('/<h([23])([^>]*)>(.*?)<\/h\1>/is', $content_html, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $m) {
			$tag = $m[1];
			$attrs = $m[2];
			$title_html = $m[3];
			$title_plain = trim(strip_tags($title_html));
			if ($title_plain === '') continue;

			// Extract or generate ID
			if (preg_match('/id=[\'"]([^\'"]+)[\'"]/i', $attrs, $id_m)) {
				$h_id = $id_m[1];
			} else {
				$h_id = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title_plain));
				$h_id = trim($h_id, '-');
				$orig_h = $m[0];
				$new_h = "<h{$tag} id=\"{$h_id}\"{$attrs}>{$title_html}</h{$tag}>";
				$content_html = str_replace($orig_h, $new_h, $content_html);
			}

			$toc[] = [
				'id' => $h_id,
				'title' => $title_plain,
				'level' => (int) $tag,
			];
		}
	}

	// Previous & Next articles
	$prev = db_row(
		"SELECT blog_id, blog_heading, blog_slug, blog_posted_on, blog_thumb_img 
		 FROM tbl_blog 
		 WHERE is_active = '1' AND (blog_posted_on < ? OR (blog_posted_on = ? AND blog_id < ?)) 
		 ORDER BY blog_posted_on DESC, blog_id DESC LIMIT 1",
		[$blog['blog_posted_on'], $blog['blog_posted_on'], $id]
	);
	$next = db_row(
		"SELECT blog_id, blog_heading, blog_slug, blog_posted_on, blog_thumb_img 
		 FROM tbl_blog 
		 WHERE is_active = '1' AND (blog_posted_on > ? OR (blog_posted_on = ? AND blog_id > ?)) 
		 ORDER BY blog_posted_on ASC, blog_id ASC LIMIT 1",
		[$blog['blog_posted_on'], $blog['blog_posted_on'], $id]
	);

	// Related articles (3 latest active blogs, excluding current)
	$related_rows = db_all(
		"SELECT blog_id, blog_heading, blog_slug, blog_thumb_img, blog_thumb_img_alt, 
		        blog_short_descp, blog_long_descp, blog_posted_on 
		 FROM tbl_blog 
		 WHERE is_active = '1' AND blog_id != ? 
		 ORDER BY blog_posted_on DESC, blog_id DESC LIMIT 3",
		[$id]
	);
	$related = [];
	foreach ($related_rows as $r) {
		$r_slug = trim((string) $r['blog_slug']);
		$r_cat = blog_category($r);
		$r_words = str_word_count(strip_tags((string) ($r['blog_long_descp'] ?: $r['blog_short_descp'])));
		$r_mins = max(2, min(10, (int) round($r_words / 200)));
		$r_ts = strtotime($r['blog_posted_on']) ?: time();

		$related[] = [
			'id' => (int) $r['blog_id'],
			'heading' => trim((string) $r['blog_heading']),
			'slug' => $r_slug,
			'url' => BASE_URL . 'our-blogs/' . $r_slug,
			'category' => $r_cat,
			'thumb_img' => !empty($r['blog_thumb_img'])
				? BASE_URL_IMG . 'blog/thumb/' . $r['blog_thumb_img']
				: BASE_URL_IMG . 'blog/inner/blog_inner_img_6070_dubai-real-estate-img.jpg',
			'thumb_alt' => trim((string) $r['blog_thumb_img_alt']) ?: trim((string) $r['blog_heading']),
			'date_formatted' => date('M d, Y', $r_ts),
			'read_time' => $r_mins . ' min read',
		];
	}

	$meta_tags = [
		'meta_title' => $blog['meta_title'] ?: ($heading . ' | DRE Homes'),
		'meta_keyword' => $blog['meta_keyword'] ?: '',
		'meta_description' => $blog['meta_description'] ?: trim(strip_tags((string) $blog['blog_short_descp'])),
		'meta_tags' => $blog['meta_tags'] ?: '',
		'meta_og_img' => $inner_img,
	];

	return [
		'blog' => $blog,
		'id' => $id,
		'heading' => $heading,
		'slug' => $clean_slug,
		'url' => $current_url,
		'category' => $cat,
		'date_formatted' => $date_formatted,
		'read_time_mins' => $read_mins,
		'read_time' => $read_mins . ' min read',
		'inner_img' => $inner_img,
		'thumb_img' => $thumb_img,
		'inner_alt' => trim((string) $blog['blog_inner_img_alt']) ?: $heading,
		'short_descp' => trim((string) $blog['blog_short_descp']),
		'content_html' => $content_html,
		'toc' => $toc,
		'prev' => $prev ? [
			'heading' => trim((string) $prev['blog_heading']),
			'slug' => trim((string) $prev['blog_slug']),
			'url' => BASE_URL . 'our-blogs/' . trim((string) $prev['blog_slug']),
		] : null,
		'next' => $next ? [
			'heading' => trim((string) $next['blog_heading']),
			'slug' => trim((string) $next['blog_slug']),
			'url' => BASE_URL . 'our-blogs/' . trim((string) $next['blog_slug']),
		] : null,
		'related' => $related,
		'meta_tags' => $meta_tags,
	];
}

/**
 * Helper to convert uppercase or mixed legal title to clean Title Case.
 */
function title_case_legal($str)
{
	$str = html_entity_decode((string) $str, ENT_QUOTES, 'UTF-8');
	$str = trim(trim($str), ':');
	if (strtoupper($str) === $str) {
		$str = ucwords(strtolower($str));
	}
	$small_words = ['and', 'of', 'to', 'this', 'your', 'in', 'for', 'on', 'a', 'an', 'the'];
	$words = explode(' ', $str);
	foreach ($words as $i => $w) {
		if ($i > 0 && in_array(strtolower($w), $small_words, true)) {
			$words[$i] = strtolower($w);
		} else {
			$words[$i] = ucfirst($w);
		}
	}
	return implode(' ', $words);
}

/**
 * Generates URL slug for legal section anchor.
 */
function slugify_legal($text)
{
	$text = html_entity_decode((string) $text, ENT_QUOTES, 'UTF-8');
	$text = strtolower(trim($text));
	$text = preg_replace('/[^a-z0-9]+/i', '-', $text);
	return trim($text, '-');
}

/**
 * Transforms raw HTML from admin TinyMCE into luxury UI sections,
 * extracting table of contents (TOC), section counts, and reading time.
 */
function process_legal_html($raw_html, $type = 'privacy')
{
	$main_headings_privacy = [
		'INTERPRETATIONS AND DEFINITIONS',
		'WEBSITE CONTENT OVERVIEW',
		'TYPES OF DATA COLLECTED',
		'RETENTION OF YOUR PERSONAL DATA',
		'DISCLOSURE OF YOUR PERSONAL DATA',
		'SECURITY OF YOUR PERSONAL DATA',
		'CHILDREN\'S PRIVACY',
		'CHILDREN’S PRIVACY',
		'CHANGES TO THIS PRIVACY POLICY',
		'CONTACT US',
	];

	$main_headings_terms = [
		'Intellectual Property',
		'Property Information',
		'User Obligations',
		'Privacy and Data Collection',
		'Disclaimer of Liability',
		'Links to Third-Party Websites',
		'Modifications to the Terms',
		'Governing Law & Dispute Resolution',
		'Governing Law &amp; Dispute Resolution',
	];

	$known_mains = ($type === 'privacy') ? $main_headings_privacy : $main_headings_terms;
	$known_normalized = array_map(function ($h) {
		return strtoupper(trim(preg_replace('/[^a-zA-Z0-9]/', '', html_entity_decode($h, ENT_QUOTES, 'UTF-8'))));
	}, $known_mains);

	$clean_html = trim((string) $raw_html);
	$clean_html = preg_replace('/<p[^>]*>(?:\s|&nbsp;|<br\s*\/?>)*<\/p>/i', '', $clean_html);

	preg_match_all('/<(p|ul|ol|h[1-6]|dl|div)[^>]*>.*?<\/\\1>/is', $clean_html, $blocks);

	$sections = [];
	$current_section = null;
	$intro_html = '';
	$sec_num = 0;

	foreach ($blocks[0] as $block) {
		$is_heading = false;
		$heading_text = '';

		if (preg_match('/^<h[1-6][^>]*>(.*?)<\/h[1-6]>$/is', $block, $hm)) {
			$is_heading = true;
			$heading_text = trim(strip_tags($hm[1]));
		} elseif (preg_match('/^<p[^>]*>\s*<(?:strong|b)[^>]*>(.*?)<\/(?:strong|b)>\s*<\/p>$/is', $block, $pm)) {
			$txt = trim(strip_tags($pm[1]));
			if (mb_strlen($txt) < 80 && !preg_match('/\.\s*$/', $txt)) {
				$is_heading = true;
				$heading_text = $txt;
			}
		}

		if ($is_heading) {
			$norm = strtoupper(trim(preg_replace('/[^a-zA-Z0-9]/', '', html_entity_decode($heading_text, ENT_QUOTES, 'UTF-8'))));
			$is_main = in_array($norm, $known_normalized, true);

			if ($is_main) {
				if ($current_section !== null) {
					$sections[] = $current_section;
				}
				$sec_num++;
				$title = title_case_legal($heading_text);
				$slug = slugify_legal($title);
				$current_section = [
					'num' => $sec_num,
					'title' => $title,
					'slug' => $slug,
					'blocks' => [],
				];
				continue;
			} else {
				$sub_title = title_case_legal($heading_text);
				if ($current_section !== null) {
					$current_section['blocks'][] = '<h3>' . $sub_title . '</h3>';
				} else {
					$intro_html .= '<h3>' . $sub_title . '</h3>';
				}
				continue;
			}
		}

		// Style lists
		if (stripos($block, '<ul') === 0) {
			$block = preg_replace('/<ul[^>]*>/i', '<ul class="lg-ul">', $block);
			$block = preg_replace('/<li[^>]*>/i', '<li>', $block);
		}

		if ($current_section !== null) {
			$current_section['blocks'][] = $block;
		} else {
			$intro_html .= $block;
		}
	}

	if ($current_section !== null) {
		$sections[] = $current_section;
	}

	$rendered = '';
	if ($intro_html) {
		$intro_html = preg_replace('/^<p(?:\s+[^>]*)?>/i', '<p class="lg-intro">', trim($intro_html));
		$rendered .= $intro_html . "\n";
	}

	$toc = [];
	foreach ($sections as $s) {
		$num_str = sprintf('%02d', $s['num']);
		$toc[] = [
			'num' => $s['num'],
			'num_str' => $num_str,
			'title' => $s['title'],
			'slug' => $s['slug'],
		];

		$safe_title = htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8');
		$rendered .= "<section class=\"lg-sec\" id=\"{$s['slug']}\">\n";
		$rendered .= "  <h2>\n";
		$rendered .= "    <span class=\"lg-n\">{$num_str}</span>{$safe_title}<button\n";
		$rendered .= "      class=\"lg-link\"\n";
		$rendered .= "      type=\"button\"\n";
		$rendered .= "      data-id=\"{$s['slug']}\"\n";
		$rendered .= "      aria-label=\"Copy link to {$safe_title}\"\n";
		$rendered .= "    >\n";
		$rendered .= "      #\n";
		$rendered .= "    </button>\n";
		$rendered .= "  </h2>\n";

		$in_dl = false;
		$dl_html = '';

		foreach ($s['blocks'] as $b) {
			if (preg_match('/^<p[^>]*>\s*<strong[^>]*>(?:&ldquo;|&quot;|“)(.*?)(?:&rdquo;|&quot;|”)<\/strong>\s*(.*?)<\/p>$/is', $b, $dm)) {
				if (!$in_dl) {
					$in_dl = true;
					$dl_html = "  <dl class=\"lg-defs\">\n";
				}
				$term = htmlspecialchars(trim(strip_tags($dm[1])));
				$def = trim($dm[2]);
				$dl_html .= "    <div class=\"glass\">\n";
				$dl_html .= "      <dt>&ldquo;{$term}&rdquo;</dt>\n";
				$dl_html .= "      <dd>{$def}</dd>\n";
				$dl_html .= "    </div>\n";
			} else {
				if ($in_dl) {
					$in_dl = false;
					$dl_html .= "  </dl>\n";
					$rendered .= $dl_html;
					$dl_html = '';
				}
				$rendered .= "  " . $b . "\n";
			}
		}
		if ($in_dl) {
			$dl_html .= "  </dl>\n";
			$rendered .= $dl_html;
		}

		$rendered .= "</section>\n";
	}

	$word_count = str_word_count(strip_tags($rendered));
	$reading_time = max(1, (int) ceil($word_count / 200));

	return [
		'toc' => $toc,
		'content' => $rendered,
		'section_count' => count($sections),
		'reading_time' => $reading_time,
	];
}

/**
 * Loads dynamic legal page data (Privacy Policy or Terms & Conditions)
 * from tbl_privacy_policy (privacy_policy_id = 1) and tbl_tags.
 *
 * @param string $type 'privacy' or 'terms'
 * @return array
 */
function get_legal_page_data($type = 'privacy')
{
	$type = ($type === 'terms') ? 'terms' : 'privacy';
	$is_privacy = ($type === 'privacy');

	$legal = db_row("SELECT * FROM tbl_privacy_policy WHERE is_active = '1' ORDER BY privacy_policy_id ASC LIMIT 1");
	if (!$legal) {
		$legal = db_row("SELECT * FROM tbl_privacy_policy ORDER BY privacy_policy_id ASC LIMIT 1") ?: [];
	}

	// Hero background image
	$banner_img = '';
	if (!empty($legal['privacy_policy_img'])) {
		$banner_img = BASE_URL_IMG . 'privacy-policy/' . $legal['privacy_policy_img'];
	} else {
		$banner_img = asset_url('images/bg/privacy-policy-bg.jpg');
	}

	// Headings and content based on type
	if ($is_privacy) {
		$heading = trim((string) ($legal['privacy_policy_heading'] ?: 'Privacy Policy'));
		$content_heading = trim((string) ($legal['privacy_policy_content_heading'] ?: 'Privacy Policy'));
		$raw_descp = (string) ($legal['privacy_policy_descp'] ?? '');
		$page_id = 30; // tbl_tags page_id for Privacy Policy
		$tag_id = 29;
		$other_tab_title = 'Terms & Conditions';
		$other_tab_slug = 'terms-conditions';
		$heading_html = 'Privacy <em class="gold-text">Policy</em>';
	} else {
		$heading = trim((string) ($legal['terms_heading'] ?: 'Terms & Conditions'));
		$content_heading = $heading;
		$raw_descp = (string) ($legal['terms_descp'] ?? '');
		$page_id = 39; // tbl_tags page_id for Terms & Conditions
		$tag_id = 38;
		$other_tab_title = 'Privacy Policy';
		$other_tab_slug = 'privacy-policy';
		$heading_html = 'Terms <em class="gold-text">&amp; Conditions</em>';
	}

	// SEO Meta tags from tbl_tags
	$meta = db_row("SELECT * FROM tbl_tags WHERE page_id = ? AND is_active = 1 LIMIT 1", [$page_id]);
	if (!$meta) {
		$meta = db_row("SELECT * FROM tbl_tags WHERE id = ? LIMIT 1", [$tag_id]) ?: [];
	}

	$meta_tags = [
		'meta_title' => $meta['meta_title'] ?? ($heading . ' — DRE Homes'),
		'meta_keyword' => $meta['meta_keyword'] ?? '',
		'meta_description' => $meta['meta_description'] ?? 'DRE Homes Real Estate — an award-winning Dubai property brokerage.',
		'meta_tags' => $meta['meta_tags'] ?? '',
		'meta_og_img' => $meta['meta_og_img'] ?? $banner_img,
	];

	// Process legal HTML into TOC and structured document sections
	$processed = process_legal_html($raw_descp, $type);

	return [
		'type' => $type,
		'is_privacy' => $is_privacy,
		'heading' => $heading,
		'heading_html' => $heading_html,
		'content_heading' => $content_heading,
		'banner_img' => $banner_img,
		'toc' => $processed['toc'],
		'content_html' => $processed['content'],
		'section_count' => $processed['section_count'],
		'reading_time' => $processed['reading_time'],
		'other_tab_title' => $other_tab_title,
		'other_tab_slug' => $other_tab_slug,
		'meta_tags' => $meta_tags,
	];
}

/**
 * Maps service heading to a crisp line-art SVG icon.
 */
function get_service_svg_icon($heading, $fa_icon = '')
{
	$key = strtolower(trim((string) $heading));
	if (strpos($key, 'free') !== false || strpos($key, 'system') !== false) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h4l3-8 4 16 3-8h4"></path></svg>';
	}
	if (strpos($key, 'location') !== false) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"></path></svg>';
	}
	if (strpos($key, 'amenit') !== false) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l2.6 5.6 6.1.7-4.5 4.2 1.2 6L12 16.6 6.6 19.5l1.2-6L3.3 9.3l6.1-.7z"></path></svg>';
	}
	if (strpos($key, 'safe') !== false) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path><path d="M9 12l2 2 4-4"></path></svg>';
	}
	if (strpos($key, 'harmony') !== false || strpos($key, 'commun') !== false) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"></circle><circle cx="17" cy="9" r="2.5"></circle><path d="M2 21a7 7 0 0114 0M15 15a5 5 0 017 5"></path></svg>';
	}
	if (strpos($key, 'health') !== false) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="4"></rect><path d="M12 8v8M8 12h8"></path></svg>';
	}
	if (strpos($key, 'tax') !== false) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M8 16L16 8"></path><circle cx="9" cy="9" r="1.2"></circle><circle cx="15" cy="15" r="1.2"></circle></svg>';
	}
	if (strpos($key, 'currency') !== false || strpos($key, 'stable') !== false) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="7" rx="7" ry="3"></ellipse><path d="M5 7v5c0 1.7 3.1 3 7 3s7-1.3 7-3V7M5 12v5c0 1.7 3.1 3 7 3s7-1.3 7-3v-5"></path></svg>';
	}
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v8M8 12h8"></path></svg>';
}

/**
 * Loads dynamic Golden Visa page data:
 * - Content from tbl_golden_visa_content (edit-golden-visa-content.php)
 * - Services from tbl_services (manage-services.php)
 * - Facilities from tbl_facilities (manage-facilities.php)
 * - Qualifying properties from tbl_proj (Price >= 2M AED)
 * - SEO tags from tbl_tags (edit-tags.php, page_id = 22)
 */
function get_golden_visa_page_data()
{
	// 1. Content from tbl_golden_visa_content
	$content = db_row("SELECT * FROM tbl_golden_visa_content WHERE gold_visa_cont_id = 1 AND is_active = 1 LIMIT 1");
	if (!$content) {
		$content = db_row("SELECT * FROM tbl_golden_visa_content WHERE gold_visa_cont_id = 1 LIMIT 1") ?: [];
	}

	$banner_img = '';
	if (!empty($content['gold_visa_cont_img'])) {
		$banner_img = BASE_URL_IMG . 'golden-visa/' . $content['gold_visa_cont_img'];
	} else {
		$banner_img = asset_url('images/bg/golden-visa-bg.jpg');
	}

	$title = trim((string) ($content['gold_visa_cont_title'] ?: 'Golden Visa'));
	$heading = trim((string) ($content['gold_visa_cont_heading'] ?: 'Secure Your Dubai Golden Visa Through Property Investment'));
	$sub_heading = trim((string) ($content['gold_visa_cont_sub_heading'] ?: 'Invest in premium properties in Dubai and become eligible for the prestigious Golden Visa. Fill out the form below to get started and learn more about this exclusive opportunity.'));
	$body_text = trim((string) ($content['gold_visa_cont_text'] ?? ''));

	// 2. SEO Meta tags from tbl_tags (page_id = 22 or id = 21)
	$meta = db_row("SELECT * FROM tbl_tags WHERE page_id = 22 AND is_active = 1 LIMIT 1");
	if (!$meta) {
		$meta = db_row("SELECT * FROM tbl_tags WHERE id = 21 LIMIT 1") ?: [];
	}

	$meta_tags = [
		'meta_title' => $meta['meta_title'] ?? 'Golden Visa | DRE Real Estate | Dubai | Buy, Sell or Rent Property',
		'meta_keyword' => $meta['meta_keyword'] ?? 'Golden Visa',
		'meta_description' => $meta['meta_description'] ?? 'Secure your Dubai Golden Visa through property investment.',
		'meta_tags' => $meta['meta_tags'] ?? '',
		'meta_og_img' => !empty($meta['meta_og_img']) ? $meta['meta_og_img'] : $banner_img,
	];

	// 3. Reasons / Services from tbl_services (manage-services.php)
	$services_rows = db_all("SELECT serv_id, serv_heading, serv_fa_icon, serv_descp FROM tbl_services WHERE is_active = 1 ORDER BY serv_id DESC");
	$services = [];
	foreach ($services_rows as $i => $s) {
		$num_str = sprintf('%02d', $i + 1);
		$title_raw = trim((string) $s['serv_heading']);
		$title_formatted = ucwords(strtolower($title_raw));
		$descp_raw = trim(strip_tags((string) $s['serv_descp']));

		$services[] = [
			'id' => (int) $s['serv_id'],
			'num_str' => $num_str,
			'heading' => $title_formatted,
			'icon' => $s['serv_fa_icon'],
			'svg_icon' => get_service_svg_icon($title_formatted, $s['serv_fa_icon']),
			'text' => $descp_raw,
		];
	}

	// 4. Facilities from tbl_facilities (manage-facilities.php)
	$fac_rows = db_all("SELECT fac_id, fac_heading, fac_img, fac_img_alt_tag FROM tbl_facilities WHERE is_active = 1 ORDER BY fac_id ASC");
	$facilities = [];
	foreach ($fac_rows as $f) {
		$facilities[] = [
			'id' => (int) $f['fac_id'],
			'heading' => trim((string) $f['fac_heading']),
			'alt' => trim((string) $f['fac_img_alt_tag']) ?: trim((string) $f['fac_heading']),
			'image' => BASE_URL_IMG . 'facilities/' . $f['fac_img'],
		];
	}

	// 5. Qualifying Properties from tbl_proj (Price >= 2M AED)
	$proj_rows = db_all("SELECT tp.prj_id, tp.proj_name, tp.proj_name_on_card, tp.proj_slug, tp.proj_price, tp.proj_start_price, tp.prj_thumb_img,
                               td.dev_heading, tc.comm_heading, tp.proj_location
                        FROM tbl_proj tp
                        LEFT JOIN tbl_developer td ON tp.prj_dev_id = td.dev_id
                        LEFT JOIN tbl_community tc ON tp.prj_comm_id = tc.comm_id
                        WHERE tp.is_active = 1 AND (tp.proj_price >= 2000000 OR tp.proj_start_price >= 2000000)
                        ORDER BY FIELD(tp.proj_slug, 'seacliff-by-imtiaz', 'fior-1-at-rashid-yachts-and-marina', 'the-wilds-residences', 'marea-residences-at-dubai-islands', 'cheval-residences-at-dubai-islands', 'antigua-damac-islands-2') DESC, tp.prj_id DESC
                        LIMIT 6");
	$qualifying_homes = [];
	foreach ($proj_rows as $p) {
		$price = (float) ($p['proj_start_price'] ?: $p['proj_price']);
		$p_slug = trim((string) $p['proj_slug']);
		$name = trim((string) ($p['proj_name_on_card'] ?: explode('|', explode('—', $p['proj_name'])[0])[0]));

		// Thumbnail handling: prj_thumb_img can be comma-separated list
		$thumb_file = trim(explode(',', (string) $p['prj_thumb_img'])[0]);
		$thumb_url = !empty($thumb_file) ? BASE_URL_IMG . 'project/thumb/' . $thumb_file : '';

		$qualifying_homes[] = [
			'id' => (int) $p['prj_id'],
			'name' => $name,
			'slug' => $p_slug,
			'url' => BASE_URL . 'property/' . $p_slug,
			'developer' => trim((string) $p['dev_heading']),
			'location' => trim((string) ($p['comm_heading'] ?: $p['proj_location'] ?: 'Dubai')),
			'price_formatted' => 'AED ' . number_format($price),
			'thumb' => $thumb_url,
		];
	}

	return [
		'title' => $title,
		'heading' => $heading,
		'sub_heading' => $sub_heading,
		'body_text' => $body_text,
		'banner_img' => $banner_img,
		'meta_tags' => $meta_tags,
		'services' => $services,
		'facilities' => $facilities,
		'qualifying_homes' => $qualifying_homes,
	];
}

/**
 * Get dynamic data for Mortgage Advisory page:
 * - Content from tbl_mortgage_content (edit-mortgage-content.php)
 * - Banks from tbl_mortg_bank (manage-bank.php)
 * - Primary service details from tbl_service_section (edit-service-section.php?edit=NA==, id = 4)
 * - Content from tbl_mortgage_content (manage-mortgage.php)
 * - Lenders from tbl_mortg_bank (manage-bank.php)
 * - Advisor steps from tbl_mortgage_advisor (manage-advisor.php)
 * - Calculator types from tbl_mortgage_taxes (manage-taxes.php)
 */
function get_mortgage_advisory_page_data()
{
	// 0. Primary Service Section from tbl_service_section (id = 4, edit-service-section.php?edit=NA==)
	$service = db_row("SELECT * FROM tbl_service_section WHERE serv_sec_id = 4 AND is_active = '1' LIMIT 1");
	if (!$service) {
		$service = db_row("SELECT * FROM tbl_service_section WHERE serv_sec_id = 4 LIMIT 1") ?: [];
	}

	// 1. Content from tbl_mortgage_content
	$content = db_row("SELECT * FROM tbl_mortgage_content WHERE mortg_cont_id = 1 AND is_active = 1 LIMIT 1");
	if (!$content) {
		$content = db_row("SELECT * FROM tbl_mortgage_content WHERE mortg_cont_id = 1 LIMIT 1") ?: [];
	}

	$banner_img = '';
	if (!empty($service['meta_og_img']) && is_file(__DIR__ . '/../admin_nsrdwsc/assets/media/meta/' . $service['meta_og_img'])) {
		$banner_img = BASE_URL_IMG . 'meta/' . rawurlencode($service['meta_og_img']);
	} elseif (!empty($content['mortg_bg_img']) && is_file(__DIR__ . '/../admin_nsrdwsc/assets/media/mortgage/' . $content['mortg_bg_img'])) {
		$banner_img = BASE_URL_IMG . 'mortgage/' . rawurlencode($content['mortg_bg_img']);
	} else {
		$banner_img = BASE_URL . 'images/bg/2.jpg';
	}

	$fav_img = '';
	if (!empty($content['mortg_fav_img'])) {
		$fav_img = BASE_URL_IMG . 'mortgage/' . $content['mortg_fav_img'];
	}

	$name = !empty($service['serv_sec_name']) ? trim($service['serv_sec_name']) : 'Mortgage Advisory';
	$heading = trim((string) ($content['mortg_cont_heading'] ?: 'Get Mortgage in Dubai & The UAE'));
	$sub_heading = trim((string) ($content['mortg_cont_sub_heading'] ?: 'The right mortgage for your dream home in Dubai — compared, negotiated and handled for you by advisors who work with the banks every day.'));
	$fav_cont = trim((string) ($content['mortg_fav_cont'] ?: 'More Than 20 Partner Banks To Get You The Best Mortgage'));
	$fav_alt = trim((string) ($content['mortg_fav_img_alt'] ?: 'Partner Banks'));
	$frm_heading = trim((string) ($content['mortg_frm_heading'] ?: 'Apply for a mortgage'));
	$frm_sub_heading = trim((string) ($content['mortg_frm_sub_heading'] ?: 'Find Out How Much You Can Borrow in Minutes'));
	$adv_heading = trim((string) ($content['mortg_adv_heading'] ?: "From first call to keys"));
	$bank_heading = trim((string) ($content['mortg_bank_heading'] ?: 'We work with more than 20 different lenders to get you the best mortgage'));

	// 2. SEO Meta tags from tbl_service_section (id = 4)
	$meta_title = !empty($service['meta_title']) ? trim($service['meta_title']) : ($name . ' | DRE Homes');
	$meta_description = !empty($service['meta_description']) ? trim($service['meta_description']) : 'Compare, negotiate and secure the right mortgage for your property in Dubai with DRE Homes — working with 40+ banks and financial institutions.';
	$meta_keyword = !empty($service['meta_keyword']) ? trim($service['meta_keyword']) : 'Mortgage Advisory, Mortgage Calculator, Dubai Home Loan, UAE mortgage broker, buy property mortgage Dubai';

	$meta_tags = [
		'meta_title' => $meta_title,
		'meta_keyword' => $meta_keyword,
		'meta_description' => $meta_description,
		'meta_og_img' => !empty($service['meta_og_img']) ? (BASE_URL_IMG . 'meta/' . rawurlencode($service['meta_og_img'])) : $banner_img,
	];

	// Other services for bottom grid
	$other_rows = db_all("SELECT serv_sec_id, serv_sec_name, serv_sec_slug FROM tbl_service_section WHERE is_active = '1' AND serv_sec_id != 4 ORDER BY serv_sec_id ASC");
	$other_services = [];
	foreach ($other_rows as $row) {
		$slug = trim((string) $row['serv_sec_slug']);
		$page_target = $slug === 'property-and-investment-consultancy' ? 'property-investment-consultancy' : $slug;
		$other_services[] = [
			'id' => (int) $row['serv_sec_id'],
			'name' => trim((string) $row['serv_sec_name']),
			'slug' => $slug,
			'url' => page_link($page_target, 'services/' . $slug),
		];
	}

	// 3. Banks from tbl_mortg_bank (manage-bank.php)
	$bank_rows = db_all("SELECT mortg_bank_id, mortg_bank_img, mortg_bank_img_alt FROM tbl_mortg_bank WHERE is_active = 1 ORDER BY mortg_bank_id ASC");
	$banks = [];
	foreach ($bank_rows as $b) {
		$banks[] = [
			'id' => (int) $b['mortg_bank_id'],
			'name' => trim((string) $b['mortg_bank_img_alt']),
			'alt' => trim((string) $b['mortg_bank_img_alt']) ?: 'Partner Bank',
			'logo' => BASE_URL_IMG . 'mortgage/' . $b['mortg_bank_img'],
		];
	}

	// 4. Advisor Steps from tbl_mortgage_advisor (manage-advisor.php)
	$advisor_rows = db_all("SELECT mortg_adv_id, mortg_adv_heading, mortg_adv_content FROM tbl_mortgage_advisor WHERE is_active = 1 ORDER BY mortg_adv_id ASC");
	$advisor_steps = [];
	foreach ($advisor_rows as $i => $a) {
		$step_num = $i + 1;
		$title = trim((string) $a['mortg_adv_heading']);
		$desc = trim(strip_tags((string) $a['mortg_adv_content']));

		$advisor_steps[] = [
			'id' => (int) $a['mortg_adv_id'],
			'step_num' => $step_num,
			'step_label' => 'Step ' . $step_num,
			'heading' => $title,
			'text' => $desc,
			'svg_icon' => get_advisor_step_svg_icon($step_num, $title),
		];
	}

	// 5. Calculator types / taxes from tbl_mortgage_taxes (manage-taxes.php)
	$tax_rows = db_all("SELECT mortg_tax_id, mortg_tax_heading, mortg_tax_sub_heading, mortg_tax_descp FROM tbl_mortgage_taxes WHERE is_active = 1 ORDER BY mortg_tax_id ASC");
	$taxes = [];
	foreach ($tax_rows as $t) {
		$taxes[] = [
			'id' => (int) $t['mortg_tax_id'],
			'heading' => trim((string) $t['mortg_tax_heading']),
			'sub_heading' => trim((string) $t['mortg_tax_sub_heading']),
			'text' => trim(strip_tags((string) $t['mortg_tax_descp'])),
		];
	}

	return [
		'heading' => $heading,
		'sub_heading' => $sub_heading,
		'service' => $service,
		'name' => $name,
		'content' => $service['serv_sec_content'] ?? '',
		'banner_img' => $banner_img,
		'fav_img' => $fav_img,
		'fav_alt' => $fav_alt,
		'fav_cont' => $fav_cont,
		'frm_heading' => $frm_heading,
		'frm_sub_heading' => $frm_sub_heading,
		'adv_heading' => $adv_heading,
		'bank_heading' => $bank_heading,
		'meta_tags' => $meta_tags,
		'banks' => $banks,
		'advisor_steps' => $advisor_steps,
		'taxes' => $taxes,
		'other_services' => $other_services,
	];
}

/**
 * Data for Mortgage Advisory page (/mortgage-advisory)
 * Admin > Services > Edit Service Section (tbl_service_section, id = 4, edit-service-section.php?edit=NA==)
 */
function get_mortgage_advisory_data()
{
	return get_mortgage_advisory_page_data();
}

/**
 * Returns a modern SVG line-art icon matching the advisor step.
 */
function get_advisor_step_svg_icon($step_num, $heading = '')
{
	$h = strtolower($heading);

	if (str_contains($h, 'understand') || str_contains($h, 'consult') || $step_num === 1) {
		// Consultation / chat
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 01-11.6 7.1L4 20l1-4.6A8 8 0 1121 12z"></path></svg>';
	} elseif (str_contains($h, 'coordinate') || str_contains($h, 'compare') || str_contains($h, 'application') || $step_num === 2) {
		// Compare / documents / balance
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18M5 21h14M6 7h12M6 7l-3 7a3 3 0 006 0zM18 7l-3 7a3 3 0 006 0z"></path></svg>';
	} elseif (str_contains($h, 'secure') || str_contains($h, 'pre-approval') || str_contains($h, 'valuation') || $step_num === 3) {
		// Shield check / security
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"></path><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path></svg>';
	} else {
		// Key / home / keys
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-7 9 7"></path><path d="M5 10v10h14V10"></path><path d="M10 20v-5h4v5"></path></svg>';
	}
}

/* -------------------------------------------------------------------------
 * Book Valuation Page (book-valuation.php)
 * Reads:
 *   - tbl_book_valuation_content (Admin > Book Valuation > Page Content)
 *   - tbl_book_valuation (Admin > Book Valuation > Valuation Items)
 *   - tbl_tags (Admin > Manage Tags, id=22 / page_id=23)
 * ------------------------------------------------------------------------- */

/**
 * Returns prepared content for the Book Valuation page (banner, headings, SEO tags).
 */
function get_book_valuation_content()
{
	$content = db_row("SELECT * FROM tbl_book_valuation_content WHERE book_val_cont_id = 1 AND is_active = '1' LIMIT 1");
	if (!$content) {
		$content = db_row("SELECT * FROM tbl_book_valuation_content WHERE book_val_cont_id = 1 LIMIT 1") ?: [];
	}

	$banner_img = '';
	if (!empty($content['book_val_cont_img'])) {
		$banner_img = BASE_URL_IMG . 'book-valuation/' . rawurlencode($content['book_val_cont_img']);
	} else {
		$banner_img = BASE_URL . 'images/bg/12.jpg';
	}

	$raw_heading = trim((string) ($content['book_val_cont_heading'] ?? ''));
	if ($raw_heading === '' || strtoupper($raw_heading) === 'BOOK VALUATION') {
		$heading = 'Book your valuation';
	} else {
		$heading = $raw_heading;
	}

	$sub_heading = trim((string) ($content['meta_description'] ?? ''));
	if ($sub_heading === '' || str_starts_with($sub_heading, 'To find out more')) {
		$sub_heading = 'Planning to buy, sell or lease a property? Pick a time that suits you — our customer care team will call you back to confirm.';
	}

	// SEO tags: Admin > Manage Tags (id = 22, page_id = 23 "Book Your Valuation")
	$meta_tags = get_seo_tags(22);
	if (!$meta_tags) {
		$meta_tags = [
			'meta_title' => ($content['meta_title'] ?? '') ?: 'Book Your Valuation | DRE Real Estate | Dubai | Buy, Sell or Rent Property',
			'meta_description' => ($content['meta_description'] ?? '') ?: $sub_heading,
			'meta_keyword' => ($content['meta_keyword'] ?? '') ?: 'Book Your Valuation',
			'meta_og_img' => 'meta_og_img_2227_drehomes-about-us.jpg',
		];
	}

	return [
		'heading' => $heading,
		'heading_html' => gold_last_word($heading),
		'sub_heading' => $sub_heading,
		'banner_img' => $banner_img,
		'meta_tags' => $meta_tags,
	];
}

/**
 * Returns the valuation topic cards and intro text from tbl_book_valuation.
 */
function get_book_valuation_topics()
{
	$rows = db_all("SELECT * FROM tbl_book_valuation WHERE is_active = '1' ORDER BY book_val_id ASC");

	$intro_head = 'What would you like to know?';
	$intro_sub = 'We’re there to guide you.';
	$intro_text = 'Choose what you need — fill out the form and a member of the Customer Care Team will call you. We look forward to hearing from you.';

	$cards = [];
	foreach ($rows as $r) {
		$head = trim((string) $r['book_val_head']);

		// The intro row: "WHAT WOULD YOU LIKE TO KNOW?"
		if (stripos($head, 'what would you like') !== false) {
			$intro_head = $head;
			$paras = text_paragraphs($r['book_val_descp']);
			if ($paras) {
				$intro_text = implode(' ', $paras);
			}
			continue;
		}

		// Clean up paragraphs for card body: omit redundant "Fill up the attached form or call us..." if other paragraphs exist
		$all_paras = text_paragraphs($r['book_val_descp']);
		$clean_paras = array_values(array_filter($all_paras, function ($p) {
			return stripos($p, 'fill up') === false && stripos($p, 'customer care') === false;
		}));
		$paragraphs = $clean_paras ?: $all_paras;

		// Topic button identifier
		$topic = get_valuation_item_topic($head);
		$display_label = ($topic === 'Property Management') ? 'Property Management' : $head;
		$btn_text = 'Book for ' . strtolower($display_label) . ' →';

		$cards[] = [
			'id' => (int) $r['book_val_id'],
			'heading' => $head,
			'paragraphs' => $paragraphs,
			'topic' => $topic,
			'button_text' => $btn_text,
			'svg_icon' => get_valuation_svg_icon($head),
		];
	}

	return [
		'intro_head' => $intro_head,
		'intro_sub' => $intro_sub,
		'intro_text' => $intro_text,
		'cards' => $cards,
	];
}

/**
 * Maps heading to form topic radio value.
 */
function get_valuation_item_topic($heading)
{
	$h = strtolower($heading);
	if (str_contains($h, 'residential')) {
		return 'Residential Sale';
	} elseif (str_contains($h, 'commercial')) {
		return 'Commercial Sale';
	} elseif (str_contains($h, 'property management') || str_contains($h, 'project management')) {
		return 'Property Management';
	} elseif (str_contains($h, 'holiday')) {
		return 'Holiday Homes';
	}
	return trim($heading);
}

/**
 * Modern SVG line-art icon matching the valuation category.
 */
function get_valuation_svg_icon($heading)
{
	$h = strtolower($heading);

	if (str_contains($h, 'residential') || str_contains($h, 'home') || str_contains($h, 'house')) {
		// Home
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-7 9 7"></path><path d="M5 10v10h14V10"></path><path d="M10 20v-5h4v5"></path></svg>';
	} elseif (str_contains($h, 'commercial') || str_contains($h, 'office') || str_contains($h, 'retail') || str_contains($h, 'building')) {
		// Office / building
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="1"></rect><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2M10 21v-3h4v3"></path></svg>';
	} elseif (str_contains($h, 'management') || str_contains($h, 'project')) {
		// Key / lock
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="15" r="4"></circle><path d="M11 12l9-9M17 6l3 3M15 8l2 2"></path></svg>';
	} elseif (str_contains($h, 'holiday') || str_contains($h, 'vacation')) {
		// Sun
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"></path></svg>';
	} else {
		// General / clipboard check
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"></path><circle cx="12" cy="12" r="9"></circle></svg>';
	}
}

/**
 * Get dynamic data for Mortgage Calculator page:
 * - Content from tbl_mortgage_content (edit-mortgage-content.php)
 * - Partner bank logos from tbl_mortg_bank (manage-bank.php)
 * - SEO tags from tbl_tags (edit-tags.php, id = 28, page_id = 29)
 */
function get_mortgage_calculator_page_data()
{
	// 1. Content from tbl_mortgage_content
	$content = db_row("SELECT * FROM tbl_mortgage_content WHERE mortg_cont_id = 1 AND is_active = 1 LIMIT 1");
	if (!$content) {
		$content = db_row("SELECT * FROM tbl_mortgage_content WHERE mortg_cont_id = 1 LIMIT 1") ?: [];
	}

	$banner_img = '';
	if (!empty($content['mortg_bg_img'])) {
		$banner_img = BASE_URL_IMG . 'mortgage/' . $content['mortg_bg_img'];
	} else {
		$banner_img = BASE_URL . 'images/bg/2.jpg';
	}

	$eyebrow = trim((string) ($content['mortg_cont_heading'] ?: 'Get Mortgage in Dubai & The UAE'));
	$sub_heading = trim((string) ($content['mortg_cont_sub_heading'] ?: 'Buying or refinancing a property in Dubai and the UAE can be a challenging task. We can provide you with the best mortgage options and ongoing protection for your interests.'));
	$bank_heading = trim((string) ($content['mortg_bank_heading'] ?: 'We work with more than 20 different lenders to get you the best mortgage'));

	// Accentuate "20 different lenders" with gold-text class
	$bank_heading_html = htmlspecialchars($bank_heading, ENT_QUOTES, 'UTF-8');
	if (stripos($bank_heading, '20 different lenders') !== false) {
		$bank_heading_html = str_ireplace('20 different lenders', '<em class="gold-text">20 different lenders</em>', $bank_heading_html);
	}

	// 2. SEO Meta tags from tbl_tags (id = 28, page_id = 29)
	$meta = get_seo_tags(28);
	$meta_tags = [
		'meta_title' => $meta['meta_title'] ?? 'Mortgage Calculator | DRE Real Estate | Dubai | Buy, Sell or Rent Property',
		'meta_keyword' => $meta['meta_keyword'] ?? 'Mortgage Calculator, Dubai Home Loan, UAE Mortgage',
		'meta_description' => (!empty($meta['meta_description']) && $meta['meta_description'] !== 'Mortgage Calculator')
			? $meta['meta_description']
			: $sub_heading,
		'meta_tags' => $meta['meta_tags'] ?? '',
		'meta_og_img' => !empty($meta['meta_og_img']) ? (BASE_URL_IMG . 'tags/' . $meta['meta_og_img']) : $banner_img,
	];

	// 3. Banks from tbl_mortg_bank (manage-bank.php)
	$bank_rows = db_all("SELECT mortg_bank_id, mortg_bank_img, mortg_bank_img_alt FROM tbl_mortg_bank WHERE is_active = 1 ORDER BY mortg_bank_id ASC");
	$banks = [];
	foreach ($bank_rows as $b) {
		$banks[] = [
			'id' => (int) $b['mortg_bank_id'],
			'name' => trim((string) $b['mortg_bank_img_alt']),
			'alt' => trim((string) $b['mortg_bank_img_alt']) ?: 'Partner Bank',
			'logo' => BASE_URL_IMG . 'mortgage/' . $b['mortg_bank_img'],
		];
	}

	return [
		'eyebrow' => $eyebrow,
		'sub_heading' => $sub_heading,
		'banner_img' => $banner_img,
		'bank_heading' => $bank_heading,
		'bank_heading_html' => $bank_heading_html,
		'meta_tags' => $meta_tags,
		'banks' => $banks,
	];
}

/**
 * Get dynamic data for Town Square Team page:
 * - Town Square branch team members from tbl_team (where is_time_square_team = '1' and is_active = '1')
 * - Departments from tbl_team_desg
 * - Branch office details from tbl_footer_info
 * - Awards background and intro text from tbl_team_det
 * - SEO tags from tbl_tags (id = 15)
 */
function get_town_square_team_page_data()
{
	// 1. Team Content & Awards from tbl_team_det
	$team_content = db_row("SELECT * FROM tbl_team_det WHERE is_active = 1 LIMIT 1") ?: [];

	$awards_img = '';
	if (!empty($team_content['team_img'])) {
		$awards_img = BASE_URL_IMG . 'team/' . $team_content['team_img'];
	} else {
		$awards_img = BASE_URL . 'images/why-c.jpg';
	}

	// 2. Branch Contact Details from tbl_footer_info
	$footer_info = db_row("SELECT * FROM tbl_footer_info WHERE is_active = 1 LIMIT 1") ?: [];

	$branch_address = trim((string) ($footer_info['footer_address2'] ?? 'Street Shop 2, Community Center, Town Square Nshama Al Qudra, Dubai, UAE'));
	$branch_address = str_replace('|', ',', $branch_address);
	$branch_address = preg_replace('/\s*,\s*/', ', ', $branch_address);

	$branch_tel1 = trim((string) ($footer_info['footer_contact_number'] ?? '800 37373'));
	$branch_tel2 = trim((string) ($footer_info['footer_contact_number2'] ?? '+971 52 754 3243'));
	$branch_email = trim((string) ($footer_info['footer_email_id'] ?? 'sales@drehomes.com'));

	// 3. Team Members from tbl_team (where is_time_square_team = '1' and is_active = '1')
	$team_rows = db_all(
		"SELECT t.team_id, t.team_name, t.team_desg, t.team_desg_id, t.team_img, t.team_slug, d.team_desg_name, d.order_no
		 FROM tbl_team t
		 LEFT JOIN tbl_team_desg d ON d.team_desg_id = t.team_desg_id
		 WHERE t.is_time_square_team = '1' AND t.is_active = '1'
		 ORDER BY d.order_no IS NULL, d.order_no ASC, t.order_no IS NULL, t.order_no ASC, t.team_id ASC"
	);

	$members = [];
	$dept_counts = [];
	foreach ($team_rows as $t) {
		$d_id = (int) $t['team_desg_id'];
		$dept_counts[$d_id] = ($dept_counts[$d_id] ?? 0) + 1;

		$slug = trim((string) $t['team_slug']);
		$link = $slug !== '' ? (BASE_URL . 'our-team/' . rawurlencode($slug)) : '#';

		$members[] = [
			'id' => (int) $t['team_id'],
			'name' => trim((string) $t['team_name']),
			'role' => trim((string) $t['team_desg']),
			'department_id' => $d_id,
			'department_name' => trim((string) $t['team_desg_name']),
			'slug' => $slug,
			'link' => $link,
			'img' => BASE_URL_IMG . 'team/' . rawurlencode($t['team_img']),
		];
	}

	// 4. Distinct Departments for Filter Tabs
	$dept_rows = db_all(
		"SELECT DISTINCT d.team_desg_id, d.team_desg_name, d.order_no
		 FROM tbl_team t
		 INNER JOIN tbl_team_desg d ON d.team_desg_id = t.team_desg_id
		 WHERE t.is_time_square_team = '1' AND t.is_active = '1' AND d.is_active = '1'
		 ORDER BY d.order_no IS NULL, d.order_no ASC, d.team_desg_name ASC"
	);

	$departments = [];
	foreach ($dept_rows as $d) {
		$d_id = (int) $d['team_desg_id'];
		$departments[] = [
			'id' => $d_id,
			'name' => trim((string) $d['team_desg_name']),
			'count' => $dept_counts[$d_id] ?? 0,
		];
	}

	// 5. SEO Meta tags from tbl_tags (id = 15)
	$meta = get_seo_tags(15);
	$meta_tags = [
		'meta_title' => 'Town Square Team | DRE Real Estate | Dubai | Buy, Sell or Rent Property',
		'meta_keyword' => 'Town Square Team, DRE Real Estate, Dubai Real Estate Agents, Town Square Dubai',
		'meta_description' => 'Every member of our team brings a unique blend of skill, passion, and dedication — working together to deliver excellence in real estate.',
		'meta_tags' => $meta['meta_tags'] ?? '',
		'meta_og_img' => BASE_URL . 'images/town-square-office.jpg',
	];

	return [
		'meta_tags' => $meta_tags,
		'hero_banner' => BASE_URL . 'images/town-square-office.jpg',
		'branch_office_img' => BASE_URL . 'images/town-square-office.jpg',
		'branch_address' => $branch_address,
		'branch_tel1' => $branch_tel1,
		'branch_tel2' => $branch_tel2,
		'branch_email' => $branch_email,
		'awards_img' => $awards_img,
		'total_members' => count($members),
		'total_departments' => count($departments),
		'departments' => $departments,
		'members' => $members,
	];
}

/* ---- Bank Account Opening ---- */
/**
 * Data for Bank Account Opening service page (/bank-account-opening)
 * Admin > Services > Edit Service Section (tbl_service_section, id = 7, edit-service-section.php?edit=Nw==)
 */
function get_bank_account_opening_data()
{
	$service = db_row("SELECT * FROM tbl_service_section WHERE serv_sec_id = 7 AND is_active = '1' LIMIT 1");
	if (!$service) {
		$service = db_row("SELECT * FROM tbl_service_section WHERE serv_sec_id = 7 LIMIT 1") ?: [];
	}

	$banner_img = '';
	if (!empty($service['meta_og_img']) && is_file(__DIR__ . '/../admin_nsrdwsc/assets/media/meta/' . $service['meta_og_img'])) {
		$banner_img = BASE_URL_IMG . 'meta/' . rawurlencode($service['meta_og_img']);
	} else {
		$banner_img = BASE_URL . 'images/bg/6.jpg';
	}

	$name = !empty($service['serv_sec_name']) ? trim($service['serv_sec_name']) : 'Bank Account Opening';
	$meta_title = !empty($service['meta_title']) ? trim($service['meta_title']) : ($name . ' | DRE Homes');
	$meta_description = !empty($service['meta_description']) ? trim($service['meta_description']) : 'How to open a bank account in the UAE — a simple guide for citizens, residents and non-residents.';
	$meta_keyword = !empty($service['meta_keyword']) ? trim($service['meta_keyword']) : 'Bank Account Opening, UAE bank account, open bank account Dubai';

	// Other services for bottom grid
	$other_rows = db_all("SELECT serv_sec_id, serv_sec_name, serv_sec_slug FROM tbl_service_section WHERE is_active = '1' AND serv_sec_id != 7 ORDER BY serv_sec_id ASC");
	$other_services = [];
	foreach ($other_rows as $row) {
		$slug = trim((string) $row['serv_sec_slug']);
		$page_target = $slug === 'property-and-investment-consultancy' ? 'property-investment-consultancy' : $slug;
		$other_services[] = [
			'id' => (int) $row['serv_sec_id'],
			'name' => trim((string) $row['serv_sec_name']),
			'slug' => $slug,
			'url' => page_link($page_target, 'services/' . $slug),
		];
	}

	return [
		'service' => $service,
		'name' => $name,
		'banner_img' => $banner_img,
		'meta_tags' => [
			'meta_title' => $meta_title,
			'meta_description' => $meta_description,
			'meta_keyword' => $meta_keyword,
			'meta_og_img' => !empty($service['meta_og_img']) ? (BASE_URL_IMG . 'meta/' . rawurlencode($service['meta_og_img'])) : $banner_img,
		],
		'other_services' => $other_services,
	];
}

/* ---- Business Licenses ---- */
/**
 * Data for Business Licenses service page (/business-licenses)
 * Admin > Services > Edit Service Section (tbl_service_section, id = 8, edit-service-section.php?edit=OA==)
 */
function get_business_licenses_data()
{
	$service = db_row("SELECT * FROM tbl_service_section WHERE serv_sec_id = 8 AND is_active = '1' LIMIT 1");
	if (!$service) {
		$service = db_row("SELECT * FROM tbl_service_section WHERE serv_sec_id = 8 LIMIT 1") ?: [];
	}

	$banner_img = '';
	if (!empty($service['meta_og_img']) && is_file(__DIR__ . '/../admin_nsrdwsc/assets/media/meta/' . $service['meta_og_img'])) {
		$banner_img = BASE_URL_IMG . 'meta/' . rawurlencode($service['meta_og_img']);
	} else {
		$banner_img = BASE_URL . 'admin_nsrdwsc/assets/media/blog/inner/blog_inner_img_6070_dubai-real-estate-img.jpg';
	}

	$name = !empty($service['serv_sec_name']) ? trim($service['serv_sec_name']) : 'Business Licenses';
	$meta_title = !empty($service['meta_title']) ? trim($service['meta_title']) : ($name . ' | DRE Homes');
	$meta_description = !empty($service['meta_description']) ? trim($service['meta_description']) : 'Everything you need to set up a business in the UAE — license types, legal structures, documents and the key steps to get started.';
	$meta_keyword = !empty($service['meta_keyword']) ? trim($service['meta_keyword']) : 'Business Licenses in the UAE: What you need to know';

	// Other services for bottom grid
	$other_rows = db_all("SELECT serv_sec_id, serv_sec_name, serv_sec_slug FROM tbl_service_section WHERE is_active = '1' AND serv_sec_id != 8 ORDER BY serv_sec_id ASC");
	$other_services = [];
	foreach ($other_rows as $row) {
		$slug = trim((string) $row['serv_sec_slug']);
		$page_target = $slug === 'property-and-investment-consultancy' ? 'property-investment-consultancy' : $slug;
		$other_services[] = [
			'id' => (int) $row['serv_sec_id'],
			'name' => trim((string) $row['serv_sec_name']),
			'slug' => $slug,
			'url' => page_link($page_target, 'services/' . $slug),
		];
	}

	return [
		'service' => $service,
		'name' => $name,
		'banner_img' => $banner_img,
		'content' => $service['serv_sec_content'] ?? '',
		'meta_tags' => [
			'meta_title' => $meta_title,
			'meta_description' => $meta_description,
			'meta_keyword' => $meta_keyword,
			'meta_og_img' => !empty($service['meta_og_img']) ? (BASE_URL_IMG . 'meta/' . rawurlencode($service['meta_og_img'])) : $banner_img,
		],
		'other_services' => $other_services,
	];
}

/* ---- Relocation Service ---- */
/**
 * Data for Relocation Service page (/relocation-service)
 * Admin > Services > Edit Service Section (tbl_service_section, id = 6, edit-service-section.php?edit=Ng==)
 */
function get_relocation_service_data()
{
	$service = db_row("SELECT * FROM tbl_service_section WHERE serv_sec_id = 6 AND is_active = '1' LIMIT 1");
	if (!$service) {
		$service = db_row("SELECT * FROM tbl_service_section WHERE serv_sec_id = 6 LIMIT 1") ?: [];
	}

	$banner_img = '';
	if (!empty($service['meta_og_img']) && is_file(__DIR__ . '/../admin_nsrdwsc/assets/media/meta/' . $service['meta_og_img'])) {
		$banner_img = BASE_URL_IMG . 'meta/' . rawurlencode($service['meta_og_img']);
	} else {
		$banner_img = BASE_URL . 'images/bg/10.jpg';
	}

	$name = !empty($service['serv_sec_name']) ? trim($service['serv_sec_name']) : 'Relocation Service';
	$meta_title = !empty($service['meta_title']) ? trim($service['meta_title']) : ($name . ' | DRE Homes');
	$meta_description = !empty($service['meta_description']) ? trim($service['meta_description']) : 'End-to-end relocation for individuals and corporates — so your move to your new home is quick and stress-free.';
	$meta_keyword = !empty($service['meta_keyword']) ? trim($service['meta_keyword']) : 'Relocation Service, UAE relocation, corporate relocation Dubai, move to Dubai';

	// Other services for bottom grid
	$other_rows = db_all("SELECT serv_sec_id, serv_sec_name, serv_sec_slug FROM tbl_service_section WHERE is_active = '1' AND serv_sec_id != 6 ORDER BY serv_sec_id ASC");
	$other_services = [];
	foreach ($other_rows as $row) {
		$slug = trim((string) $row['serv_sec_slug']);
		$page_target = $slug === 'property-and-investment-consultancy' ? 'property-investment-consultancy' : $slug;
		$other_services[] = [
			'id' => (int) $row['serv_sec_id'],
			'name' => trim((string) $row['serv_sec_name']),
			'slug' => $slug,
			'url' => page_link($page_target, 'services/' . $slug),
		];
	}

	return [
		'service' => $service,
		'name' => $name,
		'banner_img' => $banner_img,
		'content' => $service['serv_sec_content'] ?? '',
		'meta_tags' => [
			'meta_title' => $meta_title,
			'meta_description' => $meta_description,
			'meta_keyword' => $meta_keyword,
			'meta_og_img' => !empty($service['meta_og_img']) ? (BASE_URL_IMG . 'meta/' . rawurlencode($service['meta_og_img'])) : $banner_img,
		],
		'other_services' => $other_services,
	];
}

/**
 * Data for Property and Investment Consultancy page (/property-investment-consultancy)
 * Admin > Services > Edit Service Section (tbl_service_section, id = 3, edit-service-section.php?edit=Mw==)
 */
function get_property_investment_consultancy_data()
{
	$service = db_row("SELECT * FROM tbl_service_section WHERE serv_sec_id = 3 AND is_active = '1' LIMIT 1");
	if (!$service) {
		$service = db_row("SELECT * FROM tbl_service_section WHERE serv_sec_id = 3 LIMIT 1") ?: [];
	}

	$banner_img = '';
	if (!empty($service['meta_og_img']) && is_file(__DIR__ . '/../admin_nsrdwsc/assets/media/meta/' . $service['meta_og_img'])) {
		$banner_img = BASE_URL_IMG . 'meta/' . rawurlencode($service['meta_og_img']);
	} else {
		$banner_img = BASE_URL . 'images/bg/2.jpg';
	}

	$name = !empty($service['serv_sec_name']) ? trim($service['serv_sec_name']) : 'Property and Investment Consultancy';
	$meta_title = !empty($service['meta_title']) ? trim($service['meta_title']) : ($name . ' | DRE Homes');
	$meta_description = !empty($service['meta_description']) ? trim($service['meta_description']) : 'Strategic and honest property and investment advice in Dubai — sourcing, due diligence, negotiation and asset management with DRE Homes.';
	$meta_keyword = !empty($service['meta_keyword']) ? trim($service['meta_keyword']) : 'Property and Investment Consultancy, Dubai property investment, real estate consultancy Dubai, asset management Dubai';

	// Other services for bottom grid
	$other_rows = db_all("SELECT serv_sec_id, serv_sec_name, serv_sec_slug FROM tbl_service_section WHERE is_active = '1' AND serv_sec_id != 3 ORDER BY serv_sec_id ASC");
	$other_services = [];
	foreach ($other_rows as $row) {
		$slug = trim((string) $row['serv_sec_slug']);
		$page_target = $slug === 'property-and-investment-consultancy' ? 'property-investment-consultancy' : $slug;
		$other_services[] = [
			'id' => (int) $row['serv_sec_id'],
			'name' => trim((string) $row['serv_sec_name']),
			'slug' => $slug,
			'url' => page_link($page_target, 'services/' . $slug),
		];
	}

	return [
		'service' => $service,
		'name' => $name,
		'banner_img' => $banner_img,
		'content' => $service['serv_sec_content'] ?? '',
		'meta_tags' => [
			'meta_title' => $meta_title,
			'meta_description' => $meta_description,
			'meta_keyword' => $meta_keyword,
			'meta_og_img' => !empty($service['meta_og_img']) ? (BASE_URL_IMG . 'meta/' . rawurlencode($service['meta_og_img'])) : $banner_img,
		],
		'other_services' => $other_services,
	];
}

/**
 * Service icon SVG helper by slug
 */
function service_icon_svg(string $slug): string
{
	$slug = strtolower(trim($slug));
	if (str_contains($slug, 'consultancy') || str_contains($slug, 'investment')) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M7 15l4-4 3 3 6-6"></path></svg>';
	}
	if (str_contains($slug, 'mortgage')) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-7 9 7"></path><path d="M5 10v10h14V10"></path><path d="M12 17v-5M10 13.5c0-1 .9-1.5 2-1.5s2 .5 2 1.5-1 1.3-2 1.5-2 .6-2 1.5.9 1.5 2 1.5 2-.5 2-1.5"></path></svg>';
	}
	if (str_contains($slug, 'property-management') || str_contains($slug, 'management')) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21V8l8-5 8 5v13"></path><path d="M9 21v-6h6v6M9 11h.01M15 11h.01"></path></svg>';
	}
	if (str_contains($slug, 'relocation')) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"></path></svg>';
	}
	if (str_contains($slug, 'license') || str_contains($slug, 'business')) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6v18h12V7z"></path><path d="M14 3v4h4M9 13h6M9 17h6"></path></svg>';
	}
	if (str_contains($slug, 'valuation')) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12V4h8l10 10-8 8z"></path><circle cx="7.5" cy="8.5" r="1.5"></circle></svg>';
	}
	if (str_contains($slug, 'bank')) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"></rect><circle cx="9" cy="12" r="2.5"></circle><path d="M14 10h4M14 14h3"></path></svg>';
	}
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v4l3 3"></path></svg>';
}

/* =====================================================================
   Brand Video Page (rule.md 9.1)
   Admin: Admin > Brand video page (edit-brand-video-content.php / tbl_brand_video_content)
   SEO: Admin > Manage Tags (tbl_tags id = 35)
   ===================================================================== */

/**
 * Returns prepared data for brand-video.php
 *
 * @return array{
 *   meta_tags: array,
 *   hero_bg: string,
 *   hero_eyebrow: string,
 *   heading: string,
 *   hero_heading_html: string,
 *   hero_sub: string,
 *   video_source: string,
 *   youtube_url: string,
 *   youtube_id: string,
 *   local_video_url: string,
 *   video_thumb: string,
 *   story_paras: array<string>,
 *   family_para: string
 * }
 */
function get_brand_video_page_data()
{
	$row = db_row("SELECT * FROM tbl_brand_video_content WHERE brand_video_cont_id = 1 AND is_active = '1'") ?: (db_row("SELECT * FROM tbl_brand_video_content WHERE is_active = '1' LIMIT 1") ?: []);

	// SEO: Admin > Manage Tags (id = 35: Dre Brand Video)
	$meta_tags = get_seo_tags(35);

	$heading = trim((string) ($row['brand_video_heading'] ?? ''));
	if ($heading === '') {
		$heading = 'DRE - 18 Years of Excellence';
	}

	if (empty($meta_tags)) {
		$meta_tags = [
			'meta_title' => $heading . ' | DRE Homes',
			'meta_keyword' => 'DRE Brand Video, DRE Brand Film, Real Estate Dubai',
			'meta_description' => 'Honesty, expert advice and personalised service — the story of how DRE earned its place in Dubai’s real estate market.',
			'meta_og_img' => !empty($row['brand_video_cont_img']) ? $row['brand_video_cont_img'] : '',
		];
	}

	$hero_bg = !empty($row['brand_video_cont_img'])
		? BASE_URL_IMG . 'brand-video/' . rawurlencode($row['brand_video_cont_img'])
		: BASE_URL . 'assets/media/brand-video/brand_video_img_814_1778493665.jpg';

	$hero_eyebrow = 'The DRE Story · Since 2007';
	$hero_heading_html = gold_last_word($heading);
	$hero_sub = 'Honesty, expert advice and personalised service — the story of how DRE earned its place in Dubai’s real estate market.';

	// Video player
	$video_source = (!empty($row['video_source']) && $row['video_source'] === 'local') ? 'local' : 'youtube';
	$youtube_url = trim((string) ($row['brand_video_youtube'] ?? ''));
	$yt_info = podcast_youtube_info($youtube_url);
	$youtube_id = $yt_info['id'] ?: 'rL5jftvCfuc';
	$local_video_url = !empty($row['brand_video_2']) ? BASE_URL_IMG . 'brand-video/videos/' . rawurlencode($row['brand_video_2']) : '';
	$video_thumb = !empty($row['brand_video_youtube_img'])
		? BASE_URL_IMG . 'brand-video/' . rawurlencode($row['brand_video_youtube_img'])
		: ($youtube_id ? 'https://img.youtube.com/vi/' . rawurlencode($youtube_id) . '/maxresdefault.jpg' : '');

	// Story paragraphs
	$raw_content = (string) ($row['brand_video_content'] ?? '');
	$paras = text_paragraphs($raw_content);

	if (count($paras) >= 2) {
		$story_paras = array_slice($paras, 0, count($paras) - 1);
		$family_para = end($paras);
	} elseif (count($paras) === 1) {
		$story_paras = [$paras[0]];
		$family_para = $paras[0];
	} else {
		$story_paras = [
			"DRE began by understanding what people need when it comes to buying, selling, or renting a home. Since 2007, DRE has grown into a respected brand in Dubai's real estate market by focusing on honesty, expert advice, and personalised service.",
			"We earned our place by consistently delivering results and building strong relationships. Our name reflects our deep roots in the city and our passion for connecting people with the perfect property."
		];
		$family_para = "We don't believe in one-size-fits-all solutions. Instead, we listen, understand clients' needs, and guide them clearly from start to finish. Every property listed with us gets our full attention, and our team works hard to make sure the client's journey is smooth and stress-free.";
	}

	return [
		'meta_tags' => $meta_tags,
		'hero_bg' => $hero_bg,
		'hero_eyebrow' => $hero_eyebrow,
		'heading' => $heading,
		'hero_heading_html' => $hero_heading_html,
		'hero_sub' => $hero_sub,
		'video_source' => $video_source,
		'youtube_url' => $youtube_url,
		'youtube_id' => $youtube_id,
		'local_video_url' => $local_video_url,
		'video_thumb' => $video_thumb,
		'story_paras' => $story_paras,
		'family_para' => $family_para,
	];
}

/**
 * Returns complete agent / team member data for agent-single.php.
 * Looks up agent by team_slug or team_id in tbl_team, joins tbl_team_desg,
 * and fetches matching testimonials, news, colleagues and prev/next links.
 */
function get_agent_single_page_data($slug = '') {
	global $conn, $footer_info;

	$slug = trim((string) $slug);
	if ($slug === '') {
		$slug = 'qurat-ul-ain';
	}

	// Lookup agent by slug or team_id
	$sql = "SELECT t.*, d.team_desg_name 
	        FROM tbl_team t 
	        LEFT JOIN tbl_team_desg d ON d.team_desg_id = t.team_desg_id 
	        WHERE t.is_active = '1' AND (t.team_slug = ? OR t.team_id = ?) 
	        LIMIT 1";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('ss', $slug, $slug);
	$stmt->execute();
	$row = $stmt->get_result()->fetch_assoc();

	if (!$row) {
		return [
			'found' => false,
			'meta_tags' => [
				'meta_title' => 'Profile not found — DRE Homes',
				'meta_description' => 'This team member may have moved on, or the link is incorrect.',
				'meta_keyword' => 'DRE Homes, Team, Dubai Real Estate',
			],
			'slug' => $slug,
		];
	}

	// Name formatting
	$full_name = trim(preg_replace('/\s+/u', ' ', (string) $row['team_name']));
	$name_without_prefix = trim(preg_replace('/^(mr|ms|mrs|dr)\.?\s+/i', '', $full_name));
	$name_parts = preg_split('/\s+/u', $name_without_prefix, 2);
	$first_name = $name_parts[0] ?? $full_name;
	$rest_name = $name_parts[1] ?? '';

	// Split name with gold accent
	if ($rest_name !== '') {
		$name_html = e($first_name) . ' <em class="gold-text">' . e($rest_name) . '</em>';
	} else {
		$name_html = '<em class="gold-text">' . e($first_name) . '</em>';
	}

	$role = trim((string) $row['team_desg']);
	$department = trim((string) ($row['team_desg_name'] ?? 'Real Estate'));
	$department_id = (int) ($row['team_desg_id'] ?? 0);
	$brn = trim((string) $row['team_orn']);
	$inner_heading = trim((string) ($row['team_inner_heading'] ?? '')) ?: $full_name;

	// Contact details
	$mobile = preg_replace('/[^0-9+]/', '', (string) $row['team_mobile']);
	$whatsapp = preg_replace('/[^0-9]/', '', (string) ($row['team_whtsapp'] ?: $row['team_mobile']));
	$email = trim((string) $row['team_emailid']);

	$img = !empty($row['team_img']) ? BASE_URL_IMG . 'team/' . rawurlencode($row['team_img']) : '';
	$img_alt = trim((string) ($row['team_img_alt'] ?? '')) ?: $full_name;

	// Bio paragraphs
	$bio_paras = text_paragraphs($row['team_cont'] ?? '');

	// Social links
	$socials = array_filter([
		'fb' => trim((string) $row['team_fblink']),
		'twitter' => trim((string) $row['team_twitterlink']),
		'linkedin' => trim((string) $row['team_linkedinlink']),
		'instagram' => trim((string) $row['team_instagramlink']),
	]);

	// Testimonials / Reviews mentioning the agent
	$reviews = [];
	$like_full = '%' . $full_name . '%';
	$like_first = '%' . $first_name . '%';
	$r_stmt = $conn->prepare("SELECT test_id, test_client_name, test_descp, test_posted_on 
	                          FROM tbl_testimonial 
	                          WHERE is_active = '1' AND (test_descp LIKE ? OR test_descp LIKE ?) 
	                          ORDER BY test_id DESC LIMIT 6");
	if ($r_stmt) {
		$r_stmt->bind_param('ss', $like_full, $like_first);
		$r_stmt->execute();
		$r_res = $r_stmt->get_result();
		while ($r_row = $r_res->fetch_assoc()) {
			$client = trim((string) $r_row['test_client_name']);
			$client_clean = trim(preg_replace('/^(mr|ms|mrs|dr)\.?\s+/i', '', $client));
			$cp = preg_split('/\s+/u', $client_clean, -1, PREG_SPLIT_NO_EMPTY);
			$initials = '';
			if (!empty($cp)) {
				$initials .= mb_strtoupper(mb_substr($cp[0], 0, 1));
				if (count($cp) > 1) {
					$initials .= mb_strtoupper(mb_substr(end($cp), 0, 1));
				}
			} else {
				$initials = '•';
			}
			$date_str = !empty($r_row['test_posted_on']) ? date('M d, Y', strtotime($r_row['test_posted_on'])) : '';
			$reviews[] = [
				'id' => (int) $r_row['test_id'],
				'client' => $client,
				'initials' => $initials,
				'text' => trim(strip_tags((string) $r_row['test_descp'])),
				'date' => $date_str,
			];
		}
	}

	// In The News mentioning the agent
	$news = [];
	$n_stmt = $conn->prepare("SELECT news_id, news_heading, news_slug, news_thumb_img, news_posted_on 
	                         FROM tbl_news 
	                         WHERE is_active = '1' AND (news_heading LIKE ? OR news_short_descp LIKE ? OR news_long_descp LIKE ?) 
	                         ORDER BY news_posted_on DESC LIMIT 8");
	if ($n_stmt) {
		$n_stmt->bind_param('sss', $like_full, $like_full, $like_full);
		$n_stmt->execute();
		$n_res = $n_stmt->get_result();
		while ($n_row = $n_res->fetch_assoc()) {
			$thumb = !empty($n_row['news_thumb_img'])
				? BASE_URL_IMG . 'company-news/thumb/' . rawurlencode($n_row['news_thumb_img'])
				: '';
			$date_str = !empty($n_row['news_posted_on']) ? date('M d, Y', strtotime($n_row['news_posted_on'])) : '';
			$news[] = [
				'id' => (int) $n_row['news_id'],
				'heading' => trim((string) $n_row['news_heading']),
				'slug' => $n_row['news_slug'],
				'url' => BASE_URL . 'company-news/' . rawurlencode($n_row['news_slug']),
				'thumb' => $thumb,
				'date' => $date_str,
			];
		}
	}

	// Colleagues: Query same department first (up to 8), then fill from other active members if < 4
	$colleagues = [];
	$current_id = (int) $row['team_id'];
	$col_stmt = $conn->prepare("SELECT t.*, d.team_desg_name 
	                            FROM tbl_team t 
	                            LEFT JOIN tbl_team_desg d ON d.team_desg_id = t.team_desg_id 
	                            WHERE t.is_active = '1' AND t.team_id != ? AND t.team_desg_id = ? 
	                            ORDER BY t.order_no IS NULL, t.order_no ASC, t.team_id ASC 
	                            LIMIT 8");
	if ($col_stmt) {
		$col_stmt->bind_param('ii', $current_id, $department_id);
		$col_stmt->execute();
		$col_res = $col_stmt->get_result();
		while ($c = $col_res->fetch_assoc()) {
			$colleagues[] = [
				'id' => (int) $c['team_id'],
				'name' => trim((string) $c['team_name']),
				'role' => trim((string) $c['team_desg']),
				'department' => trim((string) ($c['team_desg_name'] ?? '')),
				'department_id' => (int) $c['team_desg_id'],
				'brn' => trim((string) $c['team_orn']),
				'slug' => $c['team_slug'],
				'url' => BASE_URL . 'our-team/' . rawurlencode($c['team_slug']),
				'img' => !empty($c['team_img']) ? BASE_URL_IMG . 'team/' . rawurlencode($c['team_img']) : '',
			];
		}
	}

	// If fewer than 4 colleagues in same department, fetch from other departments
	if (count($colleagues) < 4) {
		$needed = 8 - count($colleagues);
		$existing_ids = array_merge([(int) $row['team_id']], array_column($colleagues, 'id'));
		$in_clause = implode(',', $existing_ids);
		$other_stmt = $conn->query("SELECT t.*, d.team_desg_name 
		                            FROM tbl_team t 
		                            LEFT JOIN tbl_team_desg d ON d.team_desg_id = t.team_desg_id 
		                            WHERE t.is_active = '1' AND t.team_id NOT IN ($in_clause) 
		                            ORDER BY d.order_no IS NULL, d.order_no ASC, t.order_no IS NULL, t.order_no ASC, t.team_id ASC 
		                            LIMIT $needed");
		if ($other_stmt) {
			while ($c = $other_stmt->fetch_assoc()) {
				$colleagues[] = [
					'id' => (int) $c['team_id'],
					'name' => trim((string) $c['team_name']),
					'role' => trim((string) $c['team_desg']),
					'department' => trim((string) ($c['team_desg_name'] ?? '')),
					'department_id' => (int) $c['team_desg_id'],
					'brn' => trim((string) $c['team_orn']),
					'slug' => $c['team_slug'],
					'url' => BASE_URL . 'our-team/' . rawurlencode($c['team_slug']),
					'img' => !empty($c['team_img']) ? BASE_URL_IMG . 'team/' . rawurlencode($c['team_img']) : '',
				];
			}
		}
	}

	// Previous and Next team members (in directory order)
	$all_members_res = $conn->query("SELECT t.team_id, t.team_name, t.team_slug 
	                                 FROM tbl_team t 
	                                 LEFT JOIN tbl_team_desg d ON d.team_desg_id = t.team_desg_id 
	                                 WHERE t.is_active = '1' 
	                                 ORDER BY d.order_no IS NULL, d.order_no ASC, t.order_no IS NULL, t.order_no ASC, t.team_id ASC");
	$all_members = [];
	if ($all_members_res) {
		while ($m = $all_members_res->fetch_assoc()) {
			$all_members[] = $m;
		}
	}
	$total_people = count($all_members);
	$current_index = -1;
	foreach ($all_members as $idx => $m) {
		if ((int) $m['team_id'] === (int) $row['team_id']) {
			$current_index = $idx;
			break;
		}
	}

	$prev_member = null;
	$next_member = null;
	if ($total_people > 1 && $current_index !== -1) {
		$prev_idx = ($current_index - 1 + $total_people) % $total_people;
		$next_idx = ($current_index + 1) % $total_people;
		$prev_member = [
			'name' => trim((string) $all_members[$prev_idx]['team_name']),
			'slug' => $all_members[$prev_idx]['team_slug'],
			'url' => BASE_URL . 'our-team/' . rawurlencode($all_members[$prev_idx]['team_slug']),
		];
		$next_member = [
			'name' => trim((string) $all_members[$next_idx]['team_name']),
			'slug' => $all_members[$next_idx]['team_slug'],
			'url' => BASE_URL . 'our-team/' . rawurlencode($all_members[$next_idx]['team_slug']),
		];
	}

	// Meta tags
	$meta_title = !empty($row['meta_title']) ? $row['meta_title'] : ($full_name . ' — ' . $role . ' | DRE Homes');
	$meta_keyword = !empty($row['meta_keyword']) ? $row['meta_keyword'] : ($full_name . ', ' . $role . ', DRE Homes Real Estate, Dubai');
	$meta_description = !empty($row['meta_description']) ? $row['meta_description'] : ($full_name . ', ' . $role . ' at DRE Homes Real Estate, Dubai.');

	$meta_tags = [
		'meta_title' => $meta_title,
		'meta_keyword' => $meta_keyword,
		'meta_description' => $meta_description,
		'meta_og_img' => $img,
		'meta_tags' => $row['meta_tags'] ?? '',
	];

	// Background hero image from tbl_team_det banner or default awards banner
	$team_det = select_row('tbl_team_det', 'is_active=1') ?: [];
	$hero_bg = !empty($team_det['team_img'])
		? BASE_URL_IMG . 'team/' . rawurlencode($team_det['team_img'])
		: 'https://drehomes.com/admin_nsrdwsc/assets/media/team/team_img_912_DRE-Awards-Image.jpeg';

	return [
		'found' => true,
		'id' => (int) $row['team_id'],
		'name' => $full_name,
		'first_name' => $first_name,
		'rest_name' => $rest_name,
		'name_html' => $name_html,
		'role' => $role,
		'department' => $department,
		'department_id' => $department_id,
		'brn' => $brn,
		'inner_heading' => $inner_heading,
		'mobile' => $mobile,
		'display_mobile' => trim((string) $row['team_mobile']),
		'whatsapp' => $whatsapp,
		'email' => $email,
		'img' => $img,
		'img_alt' => $img_alt,
		'slug' => $row['team_slug'],
		'bio' => $bio_paras,
		'socials' => $socials,
		'reviews' => $reviews,
		'news' => $news,
		'colleagues' => $colleagues,
		'prev_member' => $prev_member,
		'next_member' => $next_member,
		'total_people' => $total_people,
		'meta_tags' => $meta_tags,
		'hero_bg' => $hero_bg,
		'office' => [
			'tel' => '+971527543243',
			'wa' => '971585837346',
			'email' => 'sales@drehomes.com',
		],
	];
}

/**
 * Data for Property Management service page (/property-management)
 * Admin > Services > Edit Service Section (tbl_service_section, id = 11, edit-service-section.php?edit=MTE=)
 * and SEO tags from tbl_tags (id = 37).
 */
function get_property_management_data()
{
	$service = db_row("SELECT * FROM tbl_service_section WHERE serv_sec_id = 11 AND is_active = '1' LIMIT 1");
	if (!$service) {
		$service = db_row("SELECT * FROM tbl_service_section WHERE serv_sec_slug = 'property-management' LIMIT 1") ?: [];
	}

	// Banner image from meta_og_img or default bg
	$banner_img = '';
	if (!empty($service['meta_og_img']) && is_file(__DIR__ . '/../admin_nsrdwsc/assets/media/meta/' . $service['meta_og_img'])) {
		$banner_img = BASE_URL_IMG . 'meta/' . rawurlencode($service['meta_og_img']);
	} elseif (is_file(__DIR__ . '/../images/bg/img_5.jpg')) {
		$banner_img = BASE_URL . 'images/bg/img_5.jpg';
	} else {
		$banner_img = 'https://drehomes.com/images/bg/img_5.jpg';
	}

	$name = !empty($service['serv_sec_name']) ? trim($service['serv_sec_name']) : 'Property Management';

	// Tags fallback (tbl_tags id = 37)
	$tag_meta = select_row('tbl_tags', 'id=37 and is_active=1') ?: [];

	$meta_title = !empty($service['meta_title']) ? trim($service['meta_title']) : (!empty($tag_meta['meta_title']) ? trim($tag_meta['meta_title']) : ($name . ' | DRE Homes'));
	$meta_description = !empty($service['meta_description']) ? trim($service['meta_description']) : (!empty($tag_meta['meta_description']) ? trim($tag_meta['meta_description']) : 'Your leading property management partner in Dubai — comprehensive services designed to enhance your property’s value and ensure unparalleled satisfaction for both owners and tenants.');
	$meta_keyword = !empty($service['meta_keyword']) ? trim($service['meta_keyword']) : (!empty($tag_meta['meta_keyword']) ? trim($tag_meta['meta_keyword']) : 'Property Management, Dubai Property Management');

	// Other services for bottom grid or navigation
	$other_rows = db_all("SELECT serv_sec_id, serv_sec_name, serv_sec_slug FROM tbl_service_section WHERE is_active = '1' AND serv_sec_id != 11 AND serv_sec_slug != 'property-management' ORDER BY serv_sec_id ASC");
	$other_services = [];
	foreach ($other_rows as $row) {
		$slug = trim((string) $row['serv_sec_slug']);
		$page_target = $slug === 'property-and-investment-consultancy' ? 'property-investment-consultancy' : $slug;
		$other_services[] = [
			'id' => (int) $row['serv_sec_id'],
			'name' => trim((string) $row['serv_sec_name']),
			'slug' => $slug,
			'url' => page_link($page_target, 'services/' . $slug),
		];
	}

	return [
		'service' => $service,
		'name' => $name,
		'banner_img' => $banner_img,
		'content' => $service['serv_sec_content'] ?? '',
		'meta_tags' => [
			'meta_title' => $meta_title,
			'meta_description' => $meta_description,
			'meta_keyword' => $meta_keyword,
			'meta_og_img' => !empty($service['meta_og_img']) ? (BASE_URL_IMG . 'meta/' . rawurlencode($service['meta_og_img'])) : $banner_img,
		],
		'other_services' => $other_services,
	];
}




