<?php
header('Content-Type: application/xml; charset=UTF-8');
$current_date = date('Y-m-d\TH:i:s+00:00');

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

$urls = [
    "https://drehomes.com/",
    "https://drehomes.com/offplan-properties",
    "https://drehomes.com/ready-to-move-in",
    "https://drehomes.com/international-properties",
    "https://drehomes.com/developer",
    "https://drehomes.com/community",
    "https://drehomes.com/our-blogs",
    "https://drehomes.com/about-us",
    "https://drehomes.com/our-agents",
    "https://drehomes.com/clients-testimonials",
    "https://drehomes.com/our-awards",
    "https://drehomes.com/careers",
    "https://drehomes.com/buy-property",
    "https://drehomes.com/rent-property",
    "https://drehomes.com/golden-visa",
    "https://drehomes.com/services/book-valuation",
    "https://drehomes.com/company-news",
    "https://drehomes.com/our-media",
    "https://drehomes.com/message-from-co-founder-and-clo",
    "https://drehomes.com/message-from-co-founder-and-ceo",
    "https://drehomes.com/mortgage-calculator",
    "https://drehomes.com/services/property-and-investment-consultancy",
    "https://drehomes.com/services/mortgage-advisory",
    "https://drehomes.com/services/relocation-service",
    "https://drehomes.com/services/bank-account-opening",
    "https://drehomes.com/services/business-licenses",
    "https://drehomes.com/property-sitemap.xml",
    "https://drehomes.com/sitemap.xml",          
    "https://drehomes.com/blog-sitemap.xml",    
    "https://drehomes.com/developer-sitemap.xml",  
    "https://drehomes.com/community-sitemap.xml",
];

foreach ($urls as $url) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars($url) . '</loc>';
    echo '<lastmod>' . $current_date . '</lastmod>';  // Dynamic lastmod
    echo '<changefreq>daily</changefreq>';
    echo '</url>';
}

echo '</urlset>';
?>
