<?php

include('db/config.php');

// Function to generate the sitemap XML
function generateSitemapXML() {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
  
    $query = select_query('tbl_blog', 'is_active=1');
    while ($row = mysqli_fetch_array($query)) {
        // Get current date and time
        $dateTime = new DateTime();
        $formattedDate = $dateTime->format('Y-m-d\TH:i:sP'); // Example: 2025-08-30T01:00:48+00:00

        $xml .= "\t<url>" . PHP_EOL;
        $xml .= "\t\t<loc>https://drehomes.com/our-blogs/" . $row['blog_slug'] . "</loc>" . PHP_EOL;
        $xml .= "\t\t<lastmod>" . $formattedDate . "</lastmod>" . PHP_EOL;
        $xml .= "\t\t<changefreq>weekly</changefreq>" . PHP_EOL;
        
        $xml .= "\t</url>" . PHP_EOL;
    }

    $xml .= '</urlset>';

    return $xml;
}

// Generate sitemap XML
$sitemapXML = generateSitemapXML();

// Set the content type header to XML
header('Content-Type: application/xml');

// Output the sitemap content
echo $sitemapXML;

?>
