<?php
require_once('db/config.php');

$data = get_blogs_page_data();
$meta_tags = $data['meta_tags'];

$page = [
  'active' => 'blogs',
  'css' => ['assets/css/suggest.css', 'assets/css/pages/blogs.css'],
];

include('common/header.php');
?>

    <!-- ===== BLOGS: HERO ===== -->
    <header class="ph-hero bl-hero">
      <div
        class="ph-bg"
        style="
          background-image: url('<?= htmlspecialchars($data['hero_bg']) ?>');
        "
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= page_link('index') ?>">Home</a><span>/</span><span aria-current="page">Blogs</span>
        </div>
        <span class="eyebrow">Insights · News · Guides</span>
        <h1 class="serif"><?= htmlspecialchars($data['hero_heading']) ?></h1>
        <p class="sub">
          <?= htmlspecialchars($data['hero_sub']) ?>
        </p>
        <div class="dp-stats glass reveal">
          <div><small>Articles</small><b><?= (int) $data['total_count'] ?></b></div>
          <div><small>Topics</small><b><?= (int) $data['total_topics'] ?></b></div>
          <div><small>Writing since</small><b><?= (int) $data['writing_since'] ?></b></div>
          <div><small>Latest</small><b><?= htmlspecialchars($data['latest_date']) ?></b></div>
        </div>
      </div>
    </header>

    <!-- ===== FEATURED ===== -->
    <?php if (!empty($data['featured_main'])): ?>
    <section id="featured" style="padding-top: 70px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Latest stories</span>
            <h2 class="serif">Fresh from <em class="gold-text">the desk.</em></h2>
          </div>
        </div>
        <div class="bf-grid reveal">
          <a class="bf-main" href="<?= htmlspecialchars($data['featured_main']['url']) ?>">
            <img
              src="<?= htmlspecialchars($data['featured_main']['inner_img'] ?: $data['featured_main']['thumb_img']) ?>"
              alt="<?= htmlspecialchars($data['featured_main']['inner_alt']) ?>"
              decoding="async"
            />
            <span class="bf-in glass on-img">
              <span class="btag"><?= htmlspecialchars($data['featured_main']['category']) ?></span>
              <b class="serif"><?= htmlspecialchars($data['featured_main']['heading']) ?></b>
              <p><?= htmlspecialchars($data['featured_main']['short_desc']) ?></p>
              <span class="bmeta">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                  <rect x="3" y="5" width="18" height="16" rx="3"></rect>
                  <path d="M3 10h18M8 3v4M16 3v4"></path>
                </svg><?= htmlspecialchars($data['featured_main']['date_formatted']) ?><i></i>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                  <circle cx="12" cy="12" r="9"></circle>
                  <path d="M12 7v5l3 2"></path>
                </svg><?= htmlspecialchars($data['featured_main']['read_time']) ?>
              </span>
              <span class="bf-go">Read article →</span>
            </span>
          </a>
          <?php if (!empty($data['featured_side'])): ?>
          <div class="bf-side">
            <?php foreach ($data['featured_side'] as $fside): ?>
            <a class="bf-s glass" href="<?= htmlspecialchars($fside['url']) ?>">
              <span class="bf-si">
                <img
                  src="<?= htmlspecialchars($fside['thumb_img']) ?>"
                  alt="<?= htmlspecialchars($fside['thumb_alt']) ?>"
                  decoding="async"
                />
              </span>
              <span class="bf-st">
                <span class="btag"><?= htmlspecialchars($fside['category']) ?></span>
                <b><?= htmlspecialchars($fside['heading']) ?></b>
                <span class="bmeta">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <rect x="3" y="5" width="18" height="16" rx="3"></rect>
                    <path d="M3 10h18M8 3v4M16 3v4"></path>
                  </svg><?= htmlspecialchars($fside['date_formatted']) ?><i></i>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 7v5l3 2"></path>
                  </svg><?= htmlspecialchars($fside['read_time']) ?>
                </span>
              </span>
            </a>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ===== ALL ARTICLES ===== -->
    <section id="articles" style="padding-top: 40px">
      <div class="wrap">
        <div class="head-row reveal" style="margin-bottom: 22px">
          <div>
            <span class="eyebrow">The archive</span>
            <h2 class="serif">Every story, <em class="gold-text">one place.</em></h2>
          </div>
        </div>
        <div class="cchips glass" id="blCat" role="tablist" aria-label="Topic">
          <button class="on" data-c="">All articles <b><?= (int) $data['total_count'] ?></b></button>
          <?php foreach ($data['topics'] as $topic_name => $topic_count): ?>
            <?php if ($topic_count > 0): ?>
            <button data-c="<?= htmlspecialchars($topic_name) ?>"><?= htmlspecialchars($topic_name) ?> <b><?= (int) $topic_count ?></b></button>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
        <div class="fbar glass">
          <div class="fb-row bl-row">
            <label class="fsearch">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="M20 20l-3.5-3.5"></path>
              </svg>
              <input
                id="blQ"
                type="search"
                placeholder="Search articles — e.g. Golden Visa, Emaar, off-plan…"
                aria-label="Search articles"
                data-trending="Golden Visa|Emaar|Off-plan|DAMAC|Expo 2020|Mortgage"
              />
            </label>
            <label class="fsel">
              <span>Year</span>
              <select id="blY">
                <option value="">All years</option>
                <?php foreach ($data['years'] as $yr => $ycnt): ?>
                <option value="<?= htmlspecialchars($yr) ?>"><?= htmlspecialchars($yr) ?> (<?= (int) $ycnt ?>)</option>
                <?php endforeach; ?>
              </select>
            </label>
            <label class="fsel">
              <span>Sort</span>
              <select id="blS">
                <option value="new">Newest first</option>
                <option value="old">Oldest first</option>
                <option value="quick">Quick reads first</option>
              </select>
            </label>
          </div>
          <div class="fb-row2">
            <p class="dv-count" id="blCount" style="margin: 0"></p>
            <button class="clear" id="blClear" hidden="">Clear filters ×</button>
          </div>
        </div>
        <div class="bl-grid" id="blGrid" aria-live="polite">
          <?php foreach ($data['blogs'] as $b): ?>
          <article
            class="bc glass"
            data-c="<?= htmlspecialchars($b['category']) ?>"
            data-y="<?= htmlspecialchars($b['year']) ?>"
            data-ts="<?= (int) $b['timestamp'] ?>"
            data-m="<?= (int) $b['read_time_mins'] ?>"
          >
            <a class="bc-img" href="<?= htmlspecialchars($b['url']) ?>" tabindex="-1" aria-hidden="true">
              <img
                src="<?= htmlspecialchars($b['thumb_img']) ?>"
                alt="<?= htmlspecialchars($b['thumb_alt']) ?>"
                loading="lazy"
              />
              <span class="btag"><?= htmlspecialchars($b['category']) ?></span>
            </a>
            <div class="bc-b">
              <span class="bmeta">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                  <rect x="3" y="5" width="18" height="16" rx="3"></rect>
                  <path d="M3 10h18M8 3v4M16 3v4"></path>
                </svg><?= htmlspecialchars($b['date_formatted']) ?><i></i>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                  <circle cx="12" cy="12" r="9"></circle>
                  <path d="M12 7v5l3 2"></path>
                </svg><?= htmlspecialchars($b['read_time']) ?>
              </span>
              <h3>
                <a href="<?= htmlspecialchars($b['url']) ?>"><?= htmlspecialchars($b['heading']) ?></a>
              </h3>
              <p><?= htmlspecialchars($b['short_desc']) ?></p>
              <div class="bc-f"><a href="<?= htmlspecialchars($b['url']) ?>">Read article →</a></div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
        <div class="op-empty glass" id="blEmpty" hidden="">
          <h3>No articles match.</h3>
          <p>Try a different topic, year or keyword.</p>
          <div class="story-cta" style="justify-content: center">
            <button class="btn btn-glass" id="blEmptyClear">Clear filters</button>
          </div>
        </div>
        <div class="pager" id="blPager" role="navigation" aria-label="Articles pagination"></div>
      </div>
    </section>

    <!-- ===== ESSENTIAL GUIDES ===== -->
    <?php if (!empty($data['guides'])): ?>
    <section id="guides" style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Before you buy</span>
            <h2 class="serif">Essential <em class="gold-text">guides.</em></h2>
          </div>
          <button class="btn btn-glass" id="blAllGuides">All guides →</button>
        </div>
        <div class="bg-grid reveal">
          <?php foreach ($data['guides'] as $gindex => $g): ?>
          <a class="bg-c glass" href="<?= htmlspecialchars($g['url']) ?>">
            <span class="bg-n"><?= sprintf('%02d', $gindex + 1) ?></span>
            <span class="bg-i">
              <img
                src="<?= htmlspecialchars($g['thumb_img']) ?>"
                alt="<?= htmlspecialchars($g['thumb_alt']) ?>"
                loading="lazy"
              />
            </span>
            <b><?= htmlspecialchars($g['heading']) ?></b>
            <span class="bmeta">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                <rect x="3" y="5" width="18" height="16" rx="3"></rect>
                <path d="M3 10h18M8 3v4M16 3v4"></path>
              </svg><?= htmlspecialchars($g['date_formatted']) ?><i></i>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 7v5l3 2"></path>
              </svg><?= htmlspecialchars($g['read_time']) ?>
            </span>
            <span class="bg-go">Read guide →</span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- ===== PRIVATE CONSULTATION CTA ===== -->
    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Private Consultation</span>
            <h2 class="serif">Your advisor is<br /><em class="gold-text">one call</em> away.</h2>
            <p class="lead">Toll free 800 37373 · Hotline +971 52 754 3243</p>
          </div>
          <form class="form" id="ctaForm" novalidate="">
            <input class="form-control" name="name" placeholder="Full name" autocomplete="name" required="" />
            <input
              class="form-control"
              id="ctaPhone"
              name="phone"
              type="tel"
              placeholder="Phone / WhatsApp"
              autocomplete="tel"
              required=""
            />
            <input type="hidden" name="phone_full" id="ctaPhoneFull" />
            <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
            <input class="form-control" name="message" placeholder="What are you looking for?" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>

