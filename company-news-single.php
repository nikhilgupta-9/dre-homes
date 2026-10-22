<?php
include('db/config.php');

$slug = trim((string) ($_GET['detail'] ?? ''));
$story = get_single_company_news($slug);

if (!$story) {
    http_response_code(404);
    if (is_file(__DIR__ . '/404.php')) {
        include('404.php');
    } else {
        echo '<h1>404 Not Found</h1>';
    }
    exit;
}

// SEO
$meta_tags = [
    'meta_title' => $story['meta_title'],
    'meta_keyword' => $story['meta_keyword'],
    'meta_description' => $story['meta_description'],
    'meta_tags' => $story['meta_tags'],
    'meta_og_img' => $story['thumb'],
];

$page = [
    'active' => 'about',
    'css' => ['assets/css/pages/company-news-single.css'],
];

include('common/header.php');

$current_url = BASE_URL . 'company-news/' . rawurlencode($story['slug']);
$share_text = rawurlencode($story['heading'] . ' ' . $current_url);
$share_url = rawurlencode($current_url);
?>
    <!-- ===== NEWS ARTICLE: HERO ===== -->
    <div class="ar-prog" id="arProg" aria-hidden="true"><i></i></div>
    <header class="ph-hero ar-hero cn-ah">
      <div
        class="ph-bg"
        style="background-image: url(<?= e($story['inner']) ?>);"
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>">Home</a><span>/</span><a href="<?= BASE_URL ?>company-news">Company News</a><span>/</span><span aria-current="page"><?= e($story['heading']) ?></span>
        </div>
        <a class="btag" href="<?= BASE_URL ?>company-news#news"><?= e($story['category_label']) ?></a>
        <h1 class="serif"><?= e($story['heading']) ?></h1>
        <div class="ar-by">
          <span class="ar-av"><img src="<?= BASE_URL ?>assets/img/mark-white.png" alt="" /></span>
          <span>
            <b>DRE Newsroom</b>
            <span class="bmeta">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                <rect x="3" y="5" width="18" height="16" rx="3"></rect>
                <path d="M3 10h18M8 3v4M16 3v4"></path>
              </svg>
              <?= e($story['date_formatted']) ?><i></i>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 7v5l3 2"></path>
              </svg>
              <?= (int) $story['read_mins'] ?> min read
            </span>
          </span>
        </div>
      </div>
    </header>

    <!-- ===== NEWS ARTICLE: BODY ===== -->
    <section id="article" style="padding-top: 60px">
      <div class="wrap ar-grid">
        <aside class="ar-rail" aria-label="Share">
          <div class="ar-share glass">
            <small>Share</small>
            <a
              class="sh"
              href="https://wa.me/?text=<?= $share_text ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on WhatsApp"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path
                  d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                  fill="currentColor"
                ></path>
              </svg>
            </a>
            <a
              class="sh"
              href="https://twitter.com/intent/tweet?url=<?= $share_url ?>&amp;text=<?= rawurlencode($story['heading']) ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on X"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path
                  d="M17.8 3h3.1l-6.8 7.8 8 10.2h-6.3l-4.9-6.4L5.3 21H2.2l7.3-8.3L1.8 3h6.4l4.4 5.8L17.8 3zm-1.1 16.2h1.7L7.4 4.7H5.6l11.1 14.5z"
                  fill="currentColor"
                ></path>
              </svg>
            </a>
            <a
              class="sh"
              href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $share_url ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on LinkedIn"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path
                  d="M4.98 3.5a2.5 2.5 0 11-.02 5 2.5 2.5 0 01.02-5zM3 9h4v12H3zM9 9h3.8v1.7h.1c.5-1 1.8-2 3.8-2 4 0 4.8 2.6 4.8 6V21h-4v-5.6c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9V21H9z"
                  fill="currentColor"
                  stroke="none"
                ></path>
              </svg>
            </a>
            <a
              class="sh"
              href="https://www.facebook.com/sharer/sharer.php?u=<?= $share_url ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on Facebook"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path
                  d="M14 8V6.3c0-.8.2-1.3 1.4-1.3H17V2h-2.6C11.3 2 10 3.7 10 6.4V8H8v3h2v11h4V11h2.6l.4-3z"
                  fill="currentColor"
                  stroke="none"
                ></path>
              </svg>
            </a>
            <button
              class="sh"
              id="arCopy"
              type="button"
              aria-label="Copy link"
              data-url="<?= e($current_url) ?>"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M10 14a5 5 0 007 0l3-3a5 5 0 00-7-7l-1 1"></path>
                <path d="M14 10a5 5 0 00-7 0l-3 3a5 5 0 007 7l1-1"></path>
              </svg>
            </button>
          </div>
        </aside>

        <article class="ar-body" id="arBody">
<?php if (!empty($story['short_descp'])) { ?>
          <p class="ar-lead"><?= e($story['short_descp']) ?></p>
<?php } ?>
          <?= $story['long_descp'] ?>

          <div class="ar-share ar-share-m glass" style="margin-top: 40px">
            <small>Share this story</small>
            <a
              class="sh"
              href="https://wa.me/?text=<?= $share_text ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on WhatsApp"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path
                  d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                  fill="currentColor"
                ></path>
              </svg>
            </a>
            <a
              class="sh"
              href="https://twitter.com/intent/tweet?url=<?= $share_url ?>&amp;text=<?= rawurlencode($story['heading']) ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on X"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path
                  d="M17.8 3h3.1l-6.8 7.8 8 10.2h-6.3l-4.9-6.4L5.3 21H2.2l7.3-8.3L1.8 3h6.4l4.4 5.8L17.8 3zm-1.1 16.2h1.7L7.4 4.7H5.6l11.1 14.5z"
                  fill="currentColor"
                ></path>
              </svg>
            </a>
            <a
              class="sh"
              href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $share_url ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on LinkedIn"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path
                  d="M4.98 3.5a2.5 2.5 0 11-.02 5 2.5 2.5 0 01.02-5zM3 9h4v12H3zM9 9h3.8v1.7h.1c.5-1 1.8-2 3.8-2 4 0 4.8 2.6 4.8 6V21h-4v-5.6c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9V21H9z"
                  fill="currentColor"
                  stroke="none"
                ></path>
              </svg>
            </a>
            <a
              class="sh"
              href="https://www.facebook.com/sharer/sharer.php?u=<?= $share_url ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on Facebook"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path
                  d="M14 8V6.3c0-.8.2-1.3 1.4-1.3H17V2h-2.6C11.3 2 10 3.7 10 6.4V8H8v3h2v11h4V11h2.6l.4-3z"
                  fill="currentColor"
                  stroke="none"
                ></path>
              </svg>
            </a>
            <button
              class="sh"
              id="arCopy2"
              type="button"
              aria-label="Copy link"
              data-url="<?= e($current_url) ?>"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M10 14a5 5 0 007 0l3-3a5 5 0 00-7-7l-1 1"></path>
                <path d="M14 10a5 5 0 00-7 0l-3 3a5 5 0 007 7l1-1"></path>
              </svg>
            </button>
          </div>
        </article>

        <aside class="ar-side">
          <div class="ar-cta glass">
            <b class="serif">The <em class="gold-text">newsroom</em></b>
            <p>Milestones, awards and partnerships at DRE.</p>
            <a class="btn btn-gold" href="<?= BASE_URL ?>company-news">All company news →</a>
            <a class="btn btn-glass" href="<?= BASE_URL ?>our-media">Photo gallery →</a>
          </div>
        </aside>
      </div>
    </section>

<?php if (!empty($story['related'])) { ?>
    <!-- ===== MORE NEWS ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Keep reading</span>
            <h2 class="serif">More from <em class="gold-text">the newsroom.</em></h2>
          </div>
          <a class="btn btn-glass" href="<?= BASE_URL ?>company-news">All news →</a>
        </div>
        <div class="cn-cards cn-more reveal">
<?php foreach ($story['related'] as $rel) { ?>
          <a class="cn-c glass" href="<?= e($rel['url']) ?>">
            <span class="cn-ci">
              <img src="<?= e($rel['thumb']) ?>" alt="<?= e($rel['thumb_alt']) ?>" loading="lazy" />
              <span class="btag"><?= e($rel['category_label']) ?></span>
            </span>
            <span class="cn-cb">
              <span class="bmeta"><?= e($rel['date_formatted']) ?></span>
              <h3><?= e($rel['heading']) ?></h3>
              <span class="cn-go">Read the story<i aria-hidden="true">→</i></span>
            </span>
          </a>
<?php } ?>
        </div>
      </div>
    </section>
<?php } ?>

    <!-- ===== PRIVATE CONSULTATION CTA ===== -->
    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Private Consultation</span>
            <h2 class="serif">Your advisor is<br /><em class="gold-text">one call</em> away.</h2>
            <p class="lead">Toll free <?= e($footer_info['footer_contact_number'] ?? '800 37373') ?> · Hotline <?= e($footer_info['footer_contact_number2'] ?? '+971 52 754 3243') ?></p>
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

