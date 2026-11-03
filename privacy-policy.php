<?php
require_once('db/config.php');

$data = get_legal_page_data('privacy');
$meta_tags = $data['meta_tags'];

$page = [
  'active' => '',
  'css' => ['assets/css/pages/privacy-policy.css'],
];

include('common/header.php');
?>

    <!-- ===== LEGAL: HERO ===== -->
    <header class="ph-hero lg-hero">
      <div
        class="ph-bg"
        style="
          background-image: url('<?= htmlspecialchars($data['banner_img']) ?>');
        "
      ></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= page_link('index') ?>">Home</a><span>/</span><span>Legal</span><span>/</span><span aria-current="page">Privacy Policy</span>
        </div>
        <span class="eyebrow">Legal</span>
        <h1 class="serif"><?= $data['heading_html'] ?></h1>
        <div class="lg-meta">
          <span><?= (int) $data['section_count'] ?> sections</span><span><?= (int) $data['reading_time'] ?> min read</span><span>DRE Homes Real Estate Broker LLC</span>
        </div>
        <nav class="lg-tabs glass" aria-label="Legal documents">
          <a class="lg-tab on" href="<?= page_link('privacy-policy') ?>" aria-current="page">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"></path>
              <path d="M9 12l2 2 4-4"></path>
            </svg>Privacy Policy
          </a>
          <a class="lg-tab" href="<?= page_link('terms-conditions') ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M14 3H6v18h12V7z"></path>
              <path d="M14 3v4h4M9 13h6M9 17h6"></path>
            </svg>Terms &amp; Conditions
          </a>
        </nav>
      </div>
    </header>

    <!-- ===== DOCUMENT ===== -->
    <section style="padding-top: 44px">
      <div class="wrap lg-grid">
        <aside class="lg-side">
          <nav class="lg-toc glass" aria-label="On this page">
            <span class="eyebrow">On this page</span>
            <div class="lg-prog"><i></i></div>
            <ol>
              <?php foreach ($data['toc'] as $item): ?>
              <li>
                <a href="#<?= htmlspecialchars($item['slug']) ?>"><span><?= htmlspecialchars($item['num_str']) ?></span><?= htmlspecialchars($item['title']) ?></a>
              </li>
              <?php endforeach; ?>
            </ol>
          </nav>
          <div class="lg-act">
            <button type="button" id="lgPrint">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                <path d="M6 9V3h12v6M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="7" rx="1"></rect>
              </svg>Print / PDF
            </button>
            <a href="<?= page_link('terms-conditions') ?>">Terms &amp; Conditions →</a>
          </div>
        </aside>
        <article class="lg-doc glass reveal">
          <?= $data['content_html'] ?>
        </article>
      </div>
    </section>

    <!-- ===== HELP ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="lg-help glass acc-edge reveal">
          <span class="mx-ic"
            ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
              <rect x="3" y="5" width="18" height="14" rx="2"></rect>
              <path d="M3 7l9 6 9-6"></path></svg
          ></span>
          <div>
            <span class="eyebrow">Questions about this policy?</span>
            <h3 class="serif">We’re happy to <em class="gold-text">help.</em></h3>
            <p>For any queries or concerns, email us at <?= htmlspecialchars($header_info['header_email'] ?? 'sales@drehomes.com') ?>.</p>
          </div>
          <a class="btn btn-gold" href="mailto:<?= htmlspecialchars($header_info['header_email'] ?? 'sales@drehomes.com') ?>">Email us →</a>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>

