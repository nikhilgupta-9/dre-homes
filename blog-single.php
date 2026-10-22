<?php
require_once('db/config.php');

$slug = $_GET['detail'] ?? ($_GET['slug'] ?? ($_GET['id'] ?? ''));
$data = get_single_blog_data($slug);

if (empty($data)) {
  header("Location: " . BASE_URL . "blogs", true, 301);
  exit;
}

$meta_tags = $data['meta_tags'];

$page = [
  'active' => 'blogs',
  'css' => ['assets/css/pages/blog-freehold-vs-leasehold.css'],
];

$encoded_title = urlencode($data['heading']);
$encoded_url = urlencode($data['url']);

include('common/header.php');
?>

    <!-- ===== ARTICLE: HERO ===== -->
    <div class="ar-prog" id="arProg" aria-hidden="true"><i></i></div>
    <header class="ph-hero ar-hero">
      <div
        class="ph-bg"
        style="
          background-image: url('<?= htmlspecialchars($data['inner_img']) ?>');
        "
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= page_link('index') ?>">Home</a><span>/</span><a href="<?= BASE_URL ?>blogs">Blogs</a><span>/</span>
          <span aria-current="page"><?= htmlspecialchars(mb_strimwidth($data['heading'], 0, 45, '...')) ?></span>
        </div>
        <a class="btag" href="<?= BASE_URL ?>blogs#articles"><?= htmlspecialchars($data['category']) ?></a>
        <h1 class="serif"><?= htmlspecialchars($data['heading']) ?></h1>
        <div class="ar-by">
          <span class="ar-av"><img src="<?= BASE_URL ?>assets/img/mark-white.png" alt="" /></span>
          <span>
            <b>DRE Real Estate</b>
            <span class="bmeta">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                <rect x="3" y="5" width="18" height="16" rx="3"></rect>
                <path d="M3 10h18M8 3v4M16 3v4"></path>
              </svg><?= htmlspecialchars($data['date_formatted']) ?><i></i>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 7v5l3 2"></path>
              </svg><?= htmlspecialchars($data['read_time']) ?>
            </span>
          </span>
        </div>
      </div>
    </header>

    <!-- ===== ARTICLE: BODY ===== -->
    <section id="article" style="padding-top: 60px">
      <div class="wrap ar-grid">
        <aside class="ar-rail" aria-label="Share">
          <div class="ar-share glass">
            <small>Share</small>
            <a
              class="sh"
              data-net="wa"
              href="https://wa.me/?text=<?= $encoded_title ?>%20<?= $encoded_url ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on WhatsApp"
            >
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                  d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                ></path>
              </svg>
            </a>
            <a
              class="sh"
              data-net="x"
              href="https://twitter.com/intent/tweet?url=<?= $encoded_url ?>&text=<?= $encoded_title ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on X"
            >
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                  d="M17.8 3h3.1l-6.8 7.8 8 10.2h-6.3l-4.9-6.4L5.3 21H2.2l7.3-8.3L1.8 3h6.4l4.4 5.8L17.8 3zm-1.1 16.2h1.7L7.4 4.7H5.6l11.1 14.5z"
                ></path>
              </svg>
            </a>
            <a
              class="sh"
              data-net="in"
              href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $encoded_url ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on LinkedIn"
            >
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                  d="M4.98 3.5a2.5 2.5 0 11-.02 5 2.5 2.5 0 01.02-5zM3 9h4v12H3zM9 9h3.8v1.7h.1c.5-1 1.8-2 3.8-2 4 0 4.8 2.6 4.8 6V21h-4v-5.6c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9V21H9z"
                ></path>
              </svg>
            </a>
            <a
              class="sh"
              data-net="fb"
              href="https://www.facebook.com/sharer/sharer.php?u=<?= $encoded_url ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on Facebook"
            >
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                  d="M14 8V6.3c0-.8.2-1.3 1.4-1.3H17V2h-2.6C11.3 2 10 3.7 10 6.4V8H8v3h2v11h4V11h2.6l.4-3z"
                ></path>
              </svg>
            </a>
            <button
              class="sh"
              id="arCopy"
              type="button"
              aria-label="Copy link"
              data-url="<?= htmlspecialchars($data['url']) ?>"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M10 14a5 5 0 007 0l3-3a5 5 0 00-7-7l-1 1"></path>
                <path d="M14 10a5 5 0 00-7 0l-3 3a5 5 0 007 7l1-1"></path>
              </svg>
            </button>
          </div>
        </aside>

        <article class="ar-body" id="arBody">
          <?php if (!empty($data['short_descp'])): ?>
          <p class="ar-lead"><?= htmlspecialchars($data['short_descp']) ?></p>
          <?php endif; ?>

          <?= $data['content_html'] ?>

          <div class="ar-end glass">
            <div>
              <span class="eyebrow">Talk to an advisor</span>
              <h3 class="serif">Looking for expert advice — <em class="gold-text">we're here to help</em></h3>
              <p>
                Tell us your budget and goals. Our consultants will shortlist the right homes and walk you through
                payment plans, paperwork and handover.
              </p>
            </div>
            <div class="story-cta">
              <a class="btn btn-gold" href="#contact">Book a free consultation →</a>
              <a
                class="btn btn-glass"
                href="https://wa.me/971585837346?text=Hi%20DRE,%20I%20just%20read%20your%20article%20on%20<?= $encoded_title ?>%20and%20would%20like%20some%20advice."
                target="_blank"
                rel="noopener"
              >WhatsApp us</a>
            </div>
          </div>

          <div class="ar-share ar-share-m glass">
            <small>Share this article</small>
            <a
              class="sh"
              data-net="wa"
              href="https://wa.me/?text=<?= $encoded_title ?>%20<?= $encoded_url ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on WhatsApp"
            >
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                  d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
                ></path>
              </svg>
            </a>
            <a
              class="sh"
              data-net="x"
              href="https://twitter.com/intent/tweet?url=<?= $encoded_url ?>&text=<?= $encoded_title ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on X"
            >
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                  d="M17.8 3h3.1l-6.8 7.8 8 10.2h-6.3l-4.9-6.4L5.3 21H2.2l7.3-8.3L1.8 3h6.4l4.4 5.8L17.8 3zm-1.1 16.2h1.7L7.4 4.7H5.6l11.1 14.5z"
                ></path>
              </svg>
            </a>
            <a
              class="sh"
              data-net="in"
              href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $encoded_url ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on LinkedIn"
            >
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                  d="M4.98 3.5a2.5 2.5 0 11-.02 5 2.5 2.5 0 01.02-5zM3 9h4v12H3zM9 9h3.8v1.7h.1c.5-1 1.8-2 3.8-2 4 0 4.8 2.6 4.8 6V21h-4v-5.6c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9V21H9z"
                ></path>
              </svg>
            </a>
            <a
              class="sh"
              data-net="fb"
              href="https://www.facebook.com/sharer/sharer.php?u=<?= $encoded_url ?>"
              target="_blank"
              rel="noopener"
              aria-label="Share on Facebook"
            >
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                  d="M14 8V6.3c0-.8.2-1.3 1.4-1.3H17V2h-2.6C11.3 2 10 3.7 10 6.4V8H8v3h2v11h4V11h2.6l.4-3z"
                ></path>
              </svg>
            </a>
            <button
              class="sh"
              id="arCopy2"
              type="button"
              aria-label="Copy link"
              data-url="<?= htmlspecialchars($data['url']) ?>"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M10 14a5 5 0 007 0l3-3a5 5 0 00-7-7l-1 1"></path>
                <path d="M14 10a5 5 0 00-7 0l-3 3a5 5 0 007 7l1-1"></path>
              </svg>
            </button>
          </div>

          <div class="pn-row">
            <?php if (!empty($data['prev'])): ?>
            <a class="pn pn-l glass" href="<?= htmlspecialchars($data['prev']['url']) ?>">
              <small>← Previous article</small>
              <b><?= htmlspecialchars($data['prev']['heading']) ?></b>
            </a>
            <?php else: ?>
            <span></span>
            <?php endif; ?>

            <?php if (!empty($data['next'])): ?>
            <a class="pn pn-r glass" href="<?= htmlspecialchars($data['next']['url']) ?>" style="text-align: right">
              <small>Next article →</small>
              <b><?= htmlspecialchars($data['next']['heading']) ?></b>
            </a>
            <?php else: ?>
            <span></span>
            <?php endif; ?>
          </div>
        </article>

        <aside class="ar-side">
          <?php if (!empty($data['toc'])): ?>
          <nav class="ar-toc glass" aria-label="In this article">
            <span class="eyebrow">In this article</span>
            <ol id="arToc">
              <?php foreach ($data['toc'] as $titem): ?>
              <li><a href="#<?= htmlspecialchars($titem['id']) ?>"><?= htmlspecialchars($titem['title']) ?></a></li>
              <?php endforeach; ?>
            </ol>
          </nav>
          <?php endif; ?>

          <div class="ar-cta glass">
            <b class="serif">Ready to <em class="gold-text">own in Dubai?</em></b>
            <p>Browse off-plan launches and ready homes across the city’s freehold communities.</p>
            <a class="btn btn-gold" href="<?= page_link('offplan-properties') ?>">Off-plan projects →</a>
            <a class="btn btn-glass" href="<?= page_link('buy-property') ?>">Ready homes →</a>
          </div>
        </aside>
      </div>
    </section>

    <!-- ===== KEEP READING ===== -->
    <?php if (!empty($data['related'])): ?>
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Keep reading</span>
            <h2 class="serif">More <em class="gold-text">stories.</em></h2>
          </div>
          <a class="btn btn-glass" href="<?= BASE_URL ?>blogs">The DRE Journal →</a>
        </div>
        <div class="bl-grid reveal">
          <?php foreach ($data['related'] as $r): ?>
          <a class="bc glass" href="<?= htmlspecialchars($r['url']) ?>">
            <span class="bc-img">
              <img
                src="<?= htmlspecialchars($r['thumb_img']) ?>"
                alt="<?= htmlspecialchars($r['thumb_alt']) ?>"
                loading="lazy"
              />
              <span class="btag"><?= htmlspecialchars($r['category']) ?></span>
            </span>
            <span class="bc-b">
              <span class="bmeta">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                  <rect x="3" y="5" width="18" height="16" rx="3"></rect>
                  <path d="M3 10h18M8 3v4M16 3v4"></path>
                </svg><?= htmlspecialchars($r['date_formatted']) ?><i></i>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                  <circle cx="12" cy="12" r="9"></circle>
                  <path d="M12 7v5l3 2"></path>
                </svg><?= htmlspecialchars($r['read_time']) ?>
              </span>
              <h3><?= htmlspecialchars($r['heading']) ?></h3>
              <span class="bc-f"><span class="bgo">Read article →</span></span>
            </span>
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

