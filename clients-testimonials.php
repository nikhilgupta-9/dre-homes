<?php
include('db/config.php');

// SEO: Admin > Manage Tags ("Testimonial")
$meta_tags = get_seo_tags(16) ?: select_row('tbl_tags', 'id=16 and is_active=1');

// Admin > Testimonial > Edit Testimonial Content (banner, heading)
$testimonial_content = get_testimonial_content();

// Admin > Testimonial > Manage Testimonial (reviews, praised advisors, topics, stats)
$testimonials_data = get_testimonials_data();

$reviews = $testimonials_data['reviews'];
$latest_reviews = $testimonials_data['latest_reviews'];
$advisors = $testimonials_data['advisors'];
$top_advisors = $testimonials_data['top_advisors'];
$topic_counts = $testimonials_data['topic_counts'];
$year_counts = $testimonials_data['year_counts'];
$review_stats = $testimonials_data['stats'];

$reviews_per_page = 18; // the page's JS shows this many, then "Show more"

$star = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3l2.6 5.6 6.1.7-4.5 4.2 1.2 6L12 16.6 6.6 19.5l1.2-6L3.3 9.3l6.1-.7z"></path></svg>';
$quote_mark = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9.5 6C6 7.3 4 10 4 13.6V18h6v-6H7.3c.2-2 1.3-3.4 3.2-4.3zM19.5 6C16 7.3 14 10 14 13.6V18h6v-6h-2.7c.2-2 1.3-3.4 3.2-4.3z"></path></svg>';

$page = [
  'active' => 'about',
  'css' => ['assets/css/suggest.css'],
  'js' => ['assets/js/suggest.js'],
];

include('common/header.php');
?>
<!-- ===== TESTIMONIALS: HERO ===== -->
<header class="ph-hero mg-hero tq-hero">
  <?php if (!empty($testimonial_content['banner'])): ?>
    <div class="ph-bg" style="background-image: url(<?= e($testimonial_content['banner']) ?>)"></div>
  <?php endif; ?>
  <div class="wrap">
    <div class="crumbs" role="navigation" aria-label="Breadcrumb">
      <a href="<?= BASE_URL ?>">Home</a><span>/</span><span aria-current="page">Client Testimonials</span>
    </div>
    <span class="eyebrow">Client testimonials</span>
    <h1 class="serif"><?= $testimonial_content['heading_html'] ?></h1>
    <p class="sub">
      Every review is a story of trust, satisfaction, and success — explore what our clients have to say.
    </p>
    <div class="tq-rate glass">
      <span class="tq-stars"><?= str_repeat($star, 5) ?></span><span><b>Rated <?= $review_stats['rating'] ?> stars</b>
        by our happy clients</span>
    </div>
    <div class="dp-stats glass reveal">
      <div><small>Client reviews</small><b><?= $review_stats['total'] ?></b></div>
      <div><small>Advisors praised by name</small><b><?= $review_stats['advisors'] ?></b></div>
      <div><small>Reviews since</small><b><?= e($review_stats['since']) ?></b></div>
      <div><small>Latest review</small><b><?= e($review_stats['latest']) ?></b></div>
    </div>
  </div>
</header>

<?php if (!empty($latest_reviews)): ?>
  <!-- ===== FEATURED ===== -->
  <section style="padding-top: 60px">
    <div class="wrap">
      <div class="head-row reveal">
        <div>
          <span class="eyebrow">Latest stories</span>
          <h2 class="serif">In our clients’ <em class="gold-text">own words.</em></h2>
        </div>
      </div>
      <div class="tq-fg reveal">
        <?php foreach ($latest_reviews as $review):
          $with = !empty($review['advisors']) && isset($advisors[$review['advisors'][0]]) ? ' · with ' . $advisors[$review['advisors'][0]]['first'] : ''; ?>
          <figure class="tq-f glass">
            <span class="tq-qm"><?= $quote_mark ?></span>
            <blockquote><?= e($review['text']) ?></blockquote>
            <figcaption>
              <span
                class="tq-av"><?= e($review['initials']) ?></span><span><b><?= e($review['name']) ?></b><small><?= e($review['date'] . $with) ?></small></span>
            </figcaption>
          </figure>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php if (!empty($top_advisors)): ?>
  <!-- ===== ADVISORS ===== -->
  <section style="padding-top: 40px">
    <div class="wrap">
      <div class="head-row reveal">
        <div>
          <span class="eyebrow">Most praised advisors</span>
          <h2 class="serif">The people <em class="gold-text">behind the stars.</em></h2>
        </div>
        <a class="btn btn-glass" href="<?= page_link('our-team') ?>">Meet the team →</a>
      </div>
      <div class="tq-ags reveal">
        <?php foreach ($top_advisors as $advisor): ?>
          <button class="tq-ag glass" type="button" data-ag="<?= e($advisor['slug']) ?>">
            <img src="<?= e($advisor['img']) ?>" alt="<?= e($advisor['name']) ?>"
              loading="lazy" /><span><b><?= e($advisor['name']) ?></b><small><?= e($advisor['role']) ?></small><em><?= $advisor['reviews'] ?>
                review<?= $advisor['reviews'] === 1 ? '' : 's' ?></em></span>
          </button>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
<?php endif; ?>

<!-- ===== WALL ===== -->
<section id="reviews" style="padding-top: 40px">
  <div class="wrap">
    <div class="head-row reveal" style="margin-bottom: 22px">
      <div>
        <span class="eyebrow">Every review</span>
        <h2 class="serif"><?= $review_stats['total'] ?> stories <em class="gold-text">of trust.</em></h2>
      </div>
    </div>
    <div class="cchips glass" id="tqTp" role="tablist" aria-label="Topic">
      <button data-tp="all" class="on">All reviews <b><?= $topic_counts['all'] ?></b></button><button
        data-tp="buy">Buying <b><?= $topic_counts['buy'] ?></b></button><button data-tp="rent">Renting
        <b><?= $topic_counts['rent'] ?></b></button><button data-tp="sell">Selling
        <b><?= $topic_counts['sell'] ?></b></button><button data-tp="ag">Mentions an advisor
        <b><?= $topic_counts['ag'] ?></b></button>
    </div>
    <div class="fbar glass tq-bar">
      <label class="fsearch"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="11" cy="11" r="7"></circle>
          <path d="M20 20l-3.5-3.5"></path>
        </svg><input id="tqQ" type="search" placeholder="Search reviews — an advisor, a community, a word…"
          aria-label="Search reviews"
          data-trending="Palm Jumeirah|Dubai Marina|Town Square|Dubai Hills|Business Bay|off-plan|mortgage|handover|tenancy|Golden Visa" /></label>
      <label class="fsel"><span>Year</span><select id="tqY">
          <option value="">All years</option>
          <?php foreach ($year_counts as $year => $count): ?>
            <option value="<?= $year ?>"><?= $year ?> (<?= $count ?>)</option>
          <?php endforeach; ?>
        </select></label>
      <div class="tq-agf" id="tqAgf" hidden=""></div>
    </div>
    <p class="dv-count" id="tqCount"></p>
    <div class="tq-wall" id="tqWall" aria-live="polite">
      <?php foreach ($reviews as $i => $review): ?>
        <article class="tq-c glass" data-tp="<?= e(implode(' ', array_keys($review['topics']))) ?>"
          data-y="<?= $review['year'] ?: '' ?>" data-ag="<?= e(implode(' ', $review['advisors'])) ?>" <?= $i >= $reviews_per_page ? ' hidden=""' : '' ?>>
          <div class="tq-ct">
            <span class="tq-qm"><?= $quote_mark ?></span><span
              class="tq-tags"><?php foreach ($review['topics'] as $label) { ?><span><?= $label ?></span><?php } ?></span>
          </div>
          <p data-full="0"><?= e($review['text']) ?></p>
          <?php if (!empty($review['advisors'])): ?>
            <div class="tq-with">
              With
              <?php foreach ($review['advisors'] as $slug):
                if (!isset($advisors[$slug]))
                  continue;
                $advisor = $advisors[$slug]; ?>
                <button type="button" data-ag="<?= e($slug) ?>" data-name="<?= e($advisor['name']) ?>"
                  data-role="<?= e($advisor['role']) ?>">
                  <img src="<?= e($advisor['img']) ?>" alt="" /><?= e($advisor['first']) ?>
                </button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <div class="tq-cf">
            <span
              class="tq-av"><?= e($review['initials']) ?></span><span><b><?= e($review['name']) ?></b><small><?= e($review['date']) ?></small></span>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <div class="op-empty glass" id="tqEmpty" hidden="">
      <h3>No reviews match.</h3>
      <p>Try another filter or keyword.</p>
    </div>
    <div class="md-more"><button class="btn btn-glass" type="button" id="tqMore">Show more reviews</button></div>
  </div>
</section>

<!-- ===== GOOGLE ===== -->
<section style="padding-top: 40px">
  <div class="wrap">
    <div class="head-row reveal">
      <div>
        <span class="eyebrow">Live from Google</span>
        <h2 class="serif">What clients say <em class="gold-text">on Google.</em></h2>
      </div>
    </div>
    <div class="tq-ti glass reveal">
      <script defer="" async="" src="https://cdn.trustindex.io/loader.js?e5ad343727cf637b4d36c8ac53d"></script>
    </div>
  </div>
</section>

<section id="contact" style="padding-top: 20px">
  <div class="wrap">
    <div class="cta glass acc-edge reveal">
      <div>
        <span class="eyebrow">Private Consultation</span>
        <h2 class="serif">Write your own<br /><em class="gold-text">success story.</em></h2>
        <p class="lead">Toll free <?= e($footer_info['footer_contact_number'] ?? '800 37373') ?> · Hotline
          <?= e($footer_info['footer_contact_number2'] ?? '+971 52 754 3243') ?></p>
      </div>
      <form class="form" id="ctaForm" novalidate="">
        <input class="form-control" name="name" placeholder="Full name" autocomplete="name" required="" />
        <input class="form-control" id="ctaPhone" name="phone" type="tel" placeholder="Phone / WhatsApp"
          autocomplete="tel" required="" />
        <input type="hidden" name="phone_full" id="ctaPhoneFull" />
        <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
        <input class="form-control" name="message" placeholder="What are you looking for?" />
        <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
      </form>
    </div>
  </div>
</section>

<?php include('common/footer.php'); ?>