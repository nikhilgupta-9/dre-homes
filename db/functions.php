<?php

function generateProjectDetailSchema($home_meta, $header_details, $footer_info, $breadcrumbs, $proj_detail, $images, $category_slug, $slug, $meta_tags)
{
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
  } elseif (preg_match('#^(property|buy-property|rent-property|international-properties|ready-to-move-in)/(.+)$#', $slug, $matches)) {
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


function slugify($string)
{
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

function getBreadcrumbs($proj_detail = null, $category_name = '', $category_slug = '', $slug = '')
{
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
      'url' => rtrim(BASE_URL, '/') . '/property/' . $proj_detail['proj_slug'] . '/',
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




function select_query($tbl_name, $cond)
{
  global $conn;
  $query = mysqli_query($conn, "SELECT * FROM $tbl_name WHERE $cond");
  if ($query) {
    return $query;
  }
  return false;
}


function truncateHeading($text, $maxLength = 60)
{
  if (strlen($text) > $maxLength) {
    return substr($text, 0, $maxLength) . '...';
  }
  return $text;
}

function truncateParagraph($text, $maxLength = 180)
{
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

  $query = mysqli_fetch_array(mysqli_query($conn, "select tp.*,td.dev_img as developer_image,td.dev_img_alt from tbl_proj as tp LEFT JOIN tbl_developer as td ON tp.prj_dev_id=td.dev_id where tp.proj_slug='$id' and tp.is_active=1 and prj_type_id=$type"));
  if ($query) {
    return $query;
  } else {
    header('Location: ' . BASE_URL . '404.php');
    exit;
  }
  return false;
}

function roundDownToHalf($number)
{
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
    $result .= '<i class="fa ' . $starClass . '" aria-hidden="true"></i>';
  }

  return $result;


}



function star_home_rating($rating)
{
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

function getDeveloper($dev_id)
{
  $dev_name = select_query("tbl_developer", "dev_id=" . (int) $dev_id);
  $dev_row = mysqli_fetch_assoc($dev_name);

  return $dev_row['dev_heading'] ?? ''; // ✅ return string
}


function get_client_ip()
{
  $ipaddress = '';
  if (getenv('HTTP_X_FORWARDED_FOR'))
    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
  else if (getenv('HTTP_X_FORWARDED'))
    $ipaddress = getenv('HTTP_X_FORWARDED');
  else if (getenv('HTTP_FORWARDED_FOR'))
    $ipaddress = getenv('HTTP_FORWARDED_FOR');
  else if (getenv('HTTP_FORWARDED'))
    $ipaddress = getenv('HTTP_FORWARDED');
  else if (getenv('REMOTE_ADDR'))
    $ipaddress = getenv('REMOTE_ADDR');
  return $ipaddress;
}


function ip_details($IPaddress)
{
  $json = file_get_contents("http://ipinfo.io/{$IPaddress}");
  $details = json_decode($json);
  return $details;
}


function current_url()
{
  $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

  return $actual_link;

}



function currency_dropdown_list($key)
{
  $record = '<select id="currencySelect"  name="to" data-id=' . $key . '>
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

