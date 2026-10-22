<?php
include('db/config.php');
// SEO: Admin > Manage Tags ("Awards")
$meta_tags = select_row('tbl_tags', 'id=17 and is_active=1');

// Admin > Awards > Edit Awards Content (banner, heading)
$award_content = select_row('tbl_awards_content', 'is_active=1') ?: [];
// Admin > Awards > Manage Awards (newest first)
$award_rows = select_all('tbl_awards', 'is_active=1 ORDER BY award_id DESC');

// Admin text (TinyMCE HTML) -> plain text
$plain = fn($html) => trim(preg_replace('/\s+/u', ' ', str_replace("\xC2\xA0", ' ', html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], ' ', (string) $html)), ENT_QUOTES, 'UTF-8'))));
$excerpt = function ($text, $words = 30) {
    $all = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    return count($all) <= $words ? implode(' ', $all) : rtrim(implode(' ', array_slice($all, 0, $words)), ' ,;:.') . '…';
};

// The admin has no year or partner field, so both are read from the text:
//   year    -> a year in the title ("… Award 2024"), else the "posted by" date, else the description, else 0 (undated)
//   partner -> the first organisation below named in the title, else the description, else "Other"
// If the admin gets `award_year` / `award_partner` fields, their values are used instead.
$award_partners = [
    'Great Place to Work' => 'great place to work|best workplaces?',
    'Arabian Business' => 'arabian business',
    'Arabian Best of Best' => 'best of best',
    'Dubai Land Department' => 'dubai land department|\bDLD\b',
    'Dubai Holding' => 'dubai holding|nakheel|meraas',
    'Dubai Properties' => 'dubai properties',
    'Dubai South' => 'dubai south',
    'Nshama' => 'nshama|town square',
    'Damac' => 'damac',
    'Emaar' => 'emaar',
    'Deyaar' => 'deyaar',
    'Danube' => 'danube',
    'Binghatti' => 'binghatti',
    'Sobha' => 'sobha',
    'Imtiaz' => 'imtiaz',
    'Aldar' => 'aldar',
    'Azizi' => 'azizi',
    'BNW' => '\bBNW\b',
    'Ellington' => 'ellington',
    'SLS' => '\bSLS\b',
    'Meydan' => 'meydan',
];
$award_partner = function ($title, $text) use ($award_partners) {
    foreach ([$title, $text] as $haystack) {
        foreach ($award_partners as $label => $pattern) {
            if (preg_match('/' . $pattern . '/i', $haystack))
                return $label;
        }
    }
    return 'Other';
};
$award_year = function ($title, $posted, $text) {
    foreach ([$title, $posted, $text] as $haystack) {
        if (preg_match('/(?<!\d)(20[0-3]\d)(?!\d)/', (string) $haystack, $m))
            return (int) $m[1];
    }
    return 0;
};

// One entry per award with everything the page prints
$awards = [];
foreach ($award_rows as $added => $row) {
    $title = $plain($row['award_heading']);
    $text = $plain($row['award_descp'] ?: $row['award_short_descp']);
    $awards[] = [
        'added' => $added, // 0 = most recently added in the admin
        'title' => $title,
        'img' => BASE_URL_IMG . 'awards/' . rawurlencode($row['award_img']),
        'alt' => $plain($row['award_img_alt']) ?: $title,
        'text' => $text,
        'excerpt' => $excerpt($text),
        'partner' => trim($row['award_partner'] ?? '') ?: $award_partner($title, $text),
        'year' => (int) ($row['award_year'] ?? 0) ?: $award_year($title, $row['award_postedby'], $text),
    ];
}
// Wall order: newest year first, undated last; newest award first inside a year.
// "k" is the position on the wall; the page's JS opens the popup by it.
usort($awards, fn($a, $b) => [$b['year'] ?: -1, $a['added']] <=> [$a['year'] ?: -1, $b['added']]);
foreach ($awards as $i => &$award) {
    $award['k'] = $i;
}
unset($award);

// Latest honours: the 3 most recently added
$latest_awards = $awards;
usort($latest_awards, fn($a, $b) => $a['added'] <=> $b['added']);
$latest_awards = array_slice($latest_awards, 0, 3);

// Wall groups by year, partner filter buttons, hero numbers
$award_groups = [];
foreach ($awards as $award) {
    $award_groups[$award['year']][] = $award;
}
$partner_counts = array_count_values(array_column($awards, 'partner'));
unset($partner_counts['Other']);
arsort($partner_counts);
$partner_chips = array_slice($partner_counts, 0, 10, true);
$award_years = array_filter(array_keys($award_groups));
$top_partner = array_key_first($partner_counts);
$award_stats = [
    'total' => count($awards),
    'year_range' => $award_years ? min($award_years) . '–' . max($award_years) : '',
    'year_count' => count($award_years),
    'partner_count' => count($partner_counts),
    'top_partner' => $top_partner !== null ? $top_partner . ' · ' . $partner_counts[$top_partner] : '',
];

// "Milestones of Excellence" -> "Milestones of <em>excellence</em>"
$heading_words = preg_split('/\s+/u', trim($award_content['award_cont_heading'] ?? '') ?: 'Milestones of Excellence');
$heading_last = array_pop($heading_words);
$award_h1 = e(implode(' ', $heading_words)) . ' <em class="gold-text">' . e(mb_strtolower($heading_last)) . '</em>';

$award_meta = fn($award) => $award['partner'] . ($award['year'] ? ' · ' . $award['year'] : '');

include('common/header.php');
?>
<!-- ===== AWARDS: HERO ===== -->
<header class="ph-hero mg-hero aw-hero">
    <div class="ph-bg"
        style="background-image: url(<?= BASE_URL_IMG ?>awards/<?= rawurlencode($award_content['award_cont_img'] ?? '') ?>)">
    </div>
    <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
            <a href="<?= BASE_URL ?>">Home</a><span>/</span><span aria-current="page">Our Awards</span>
        </div>
        <span class="eyebrow">Our awards</span>
        <h1 class="serif"><?= $award_h1 ?></h1>
        <p class="sub">
            Every award is a thank-you from the developers and organisations we work alongside — and a reflection of the
            trust our clients place in us.
        </p>
        <div class="dp-stats glass reveal">
            <div><small>Awards &amp; honours</small><b><?= $award_stats['total'] ?></b></div>
            <div><small>Years of recognition</small><b><?= e($award_stats['year_range']) ?></b></div>
            <div><small>Awarding partners</small><b><?= $award_stats['partner_count'] ?>+</b></div>
            <div><small>Most from</small><b><?= e($award_stats['top_partner']) ?></b></div>
        </div>
    </div>
</header>

<?php if ($latest_awards) { ?>
    <!-- ===== LATEST ===== -->
    <section style="padding-top: 60px">
        <div class="wrap">
            <div class="head-row reveal">
                <div>
                    <span class="eyebrow">Latest honours</span>
                    <h2 class="serif">Fresh on <em class="gold-text">the shelf.</em></h2>
                </div>
            </div>
            <div class="aw-fg reveal">
                <?php foreach ($latest_awards as $award) { ?>
                    <article class="aw-f glass">
                        <button class="aw-fi" type="button" data-k="<?= $award['k'] ?>"
                            aria-label="View award: <?= e($award['title']) ?>">
                            <img src="<?= e($award['img']) ?>" alt="<?= e($award['alt']) ?>" />
                        </button>
                        <div class="aw-fb">
                            <span class="aw-meta"><?= e($award_meta($award)) ?></span>
                            <h3 class="serif"><?= e($award['title']) ?></h3>
                            <p><?= e($award['excerpt']) ?></p>
                            <button class="aw-more" type="button" data-k="<?= $award['k'] ?>">Read more →</button>
                        </div>
                    </article>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<!-- ===== WALL ===== -->
<section id="wall" style="padding-top: 40px">
    <div class="wrap">
        <div class="head-row reveal" style="margin-bottom: 22px">
            <div>
                <span class="eyebrow">The trophy wall</span>
                <h2 class="serif"><?= $award_stats['total'] ?> awards, <em
                        class="gold-text"><?= $award_stats['year_count'] ?> years.</em></h2>
            </div>
        </div>
        <div class="cchips glass" id="awBy" role="tablist" aria-label="Awarded by">
            <button data-b="" class="on">All awards <b><?= $award_stats['total'] ?></b></button <?php foreach ($partner_chips as $partner => $count) { ?>><button data-b="<?= e($partner) ?>"><?= e($partner) ?>
                    <b><?= $count ?></b></button <?php } ?>>
        </div>
        <div class="fbar glass aw-bar">
            <label class="fsearch"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="M20 20l-3.5-3.5"></path>
                </svg><input id="awQ" type="search" placeholder="Search awards — e.g. Emaar, top broker, 2024…"
                    aria-label="Search awards" /></label>
            <p class="dv-count" id="awCount"></p>
        </div>
        <div class="aw-grid">
            <aside class="aw-years glass" aria-label="Jump to year">
                <span class="eyebrow">Years</span>
                <div id="awYears">
                    <?php foreach ($award_groups as $year => $items) { ?>
                        <button
                            data-y="<?= (int) $year ?>"><?= $year ? (int) $year : 'Undated' ?><b><?= count($items) ?></b></button>
                    <?php } ?>
                </div>
            </aside>
            <div class="aw-tl" id="awTl" aria-live="polite">
                <?php foreach ($award_groups as $year => $items) {
                    $count_label = count($items) . ' award' . (count($items) === 1 ? '' : 's'); ?>
                    <div class="aw-yr" id="ay<?= (int) $year ?>">
                        <div class="aw-yl">
                            <b><?= $year ? (int) $year : '—' ?></b><small><?= $year ? $count_label : 'Undated · ' . $count_label ?></small>
                        </div>
                        <div class="aw-cards">
                            <?php foreach ($items as $award) { ?>
                                <article class="aw-c glass" data-by="<?= e($award['partner']) ?>" <?= $award['year'] ? ' data-y="' . $award['year'] . '"' : '' ?> data-k="<?= $award['k'] ?>"
                                    data-img="<?= e($award['img']) ?>" data-title="<?= e($award['title']) ?>">
                                    <button class="aw-ci" type="button" data-k="<?= $award['k'] ?>"
                                        aria-label="View award: <?= e($award['title']) ?>">
                                        <img src="<?= e($award['img']) ?>" alt="<?= e($award['alt']) ?>" loading="lazy" />
                                    </button>
                                    <div class="aw-cb">
                                        <span
                                            class="aw-meta"><?= e($award['partner']) ?></span><b><?= e($award['title']) ?></b><button
                                            class="aw-more" type="button" data-k="<?= $award['k'] ?>">Read more →</button>
                                    </div>
                                    <p class="aw-x" hidden=""><?= e($award['text']) ?></p>
                                </article>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="op-empty glass" id="awEmpty" hidden="">
            <h3>No awards match.</h3>
            <p>Try another partner or keyword.</p>
        </div>
    </div>
</section>

<dialog class="aw-dlg glass" id="awDlg" aria-labelledby="awDT">
    <button class="ot-x" type="button" id="awX" aria-label="Close">✕</button>
    <div class="aw-dg">
        <div class="aw-di"><img id="awDI" alt="" /></div>
        <div class="aw-db">
            <span class="aw-meta" id="awDM"></span>
            <h3 class="serif" id="awDT"></h3>
            <div id="awDX"></div>
            <div class="aw-dn">
                <button class="btn btn-glass" type="button" id="awPv">← Previous</button><button class="btn btn-glass"
                    type="button" id="awNx">Next →</button>
            </div>
        </div>
    </div>
</dialog>

<section id="contact" style="padding-top: 20px">
    <div class="wrap">
        <div class="cta glass acc-edge reveal">
            <div>
                <span class="eyebrow">Private Consultation</span>
                <h2 class="serif">Work with an<br /><em class="gold-text">award-winning team.</em></h2>
                <p class="lead">Toll free <?= e($footer_info['footer_contact_number'] ?? '800 37373') ?> · Hotline
                    <?= e($footer_info['footer_contact_number2'] ?? '+971 52 754 3243') ?></p>
            </div>
            <form class="form" id="ctaForm" novalidate="">
                <input name="name" placeholder="Full name" autocomplete="name" required="" />
                <input id="ctaPhone" name="phone" type="tel" placeholder="Phone / WhatsApp" autocomplete="tel"
                    required="" />
                <input type="hidden" name="phone_full" id="ctaPhoneFull" />
                <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
                <input name="message" placeholder="What are you looking for?" />
                <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call
                    back</button>
            </form>
        </div>
    </div>
</section>

<?php include('common/footer.php'); ?>