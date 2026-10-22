<?php
require_once('db/config.php');

// tbl_guide, guide_id = 15
$guide_id = 15;
$guide = get_guide($guide_id);
if (!$guide) {
  header('Location: ' . BASE_URL);
  exit;
}

$steps = get_guide_steps($guide['guide_id']);

// SEO & Meta tags
$seo_fallback = get_seo_tags(31) ?: [];
$meta_tags = [
  'meta_title' => !empty($guide['meta_title']) ? $guide['meta_title'] : ($seo_fallback['meta_title'] ?? 'Buyer Guide | DRE Homes'),
  'meta_description' => !empty($guide['meta_description']) ? $guide['meta_description'] : ($seo_fallback['meta_description'] ?? (!empty($guide['guide_short_description']) ? strip_tags($guide['guide_short_description']) : '')),
  'meta_keyword' => !empty($guide['meta_keyword']) ? $guide['meta_keyword'] : ($seo_fallback['meta_keyword'] ?? ''),
  'meta_tags' => !empty($guide['meta_tags']) ? $guide['meta_tags'] : ($seo_fallback['meta_tags'] ?? ''),
  'meta_og_img' => $seo_fallback['meta_og_img'] ?? '',
];

$page = [
  'active' => 'guide',
  'css' => ['assets/css/pages/buyer-guide.css'],
];

// Title formatting with gold emphasis
$heading_words = preg_split('/\s+/', trim($guide['guide_heading'] ?? 'Buyer Guide'));
if (count($heading_words) > 1) {
  $heading_last = array_pop($heading_words);
  $guide_h1 = e(guide_format_title(implode(' ', $heading_words))) . ' <em class="gold-text">' . e(guide_format_title($heading_last)) . '</em>';
} else {
  $guide_h1 = e(guide_format_title($guide['guide_heading'] ?? 'Buyer Guide'));
}

$contact_phone_raw = !empty($header_details['header_contact_number']) ? $header_details['header_contact_number'] : '+971527543243';
$contact_phone_display = !empty($header_details['header_contact_number']) ? '+971 52 754 3243' : '+971 52 754 3243';
$contact_email = !empty($header_details['header_email_id']) ? $header_details['header_email_id'] : 'sales@drehomes.com';

include('common/header.php');
?>

    <!-- ===== GUIDE: HERO ===== -->
    <header class="ph-hero mg-hero gd-hero">
      <div class="ph-bg" style="background-image: url(https://drehomes.com/images/bg/10.jpg)"></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= page_link('index') ?>">Home</a><span>/</span><span>Guide</span><span>/</span><span aria-current="page"><?= e(guide_format_title($guide['guide_heading'])) ?></span>
        </div>
        <span class="eyebrow eye-fff">Step-by-step guide · <?= count($steps) ?> steps</span>
        <h1 class="serif"><?= $guide_h1 ?></h1>
        <?php if (!empty($guide['guide_short_description'])): ?>
          <p class="sub"><?= e($guide['guide_short_description']) ?></p>
        <?php endif; ?>
        <div class="gd-tabs glass" role="navigation" aria-label="Guides">
          <a class="gd-tab on" href="<?= page_link('buyer-guide') ?>" aria-current="page">
            <span>
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M3 11l9-7 9 7"></path>
                <path d="M5 10v10h14V10"></path>
                <path d="M10 20v-5h4v5"></path>
              </svg>
            </span>Buyer
          </a>
          <a class="gd-tab" href="<?= page_link('seller-guide') ?>">
            <span>
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M3 12V4h8l10 10-8 8z"></path>
                <circle cx="7.5" cy="8.5" r="1.5"></circle>
              </svg>
            </span>Seller
          </a>
          <a class="gd-tab" href="<?= page_link('tenant-guide') ?>">
            <span>
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="8" cy="15" r="4"></circle>
                <path d="M11 12l9-9M17 6l3 3M15 8l2 2"></path>
              </svg>
            </span>Tenant
          </a>
          <a class="gd-tab" href="<?= page_link('landlord-guide') ?>">
            <span>
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="4" y="3" width="16" height="18" rx="1"></rect>
                <path d="M9 7h2M13 7h2M9 11h2M13 11h2M10 21v-3h4v3"></path>
              </svg>
            </span>Landlord
          </a>
        </div>
      </div>
    </header>

    <!-- ===== FACTS ===== -->
    <section style="padding-top: 50px">
      <div class="wrap">
        <div class="dp-stats glass reveal gd-facts">
          <div><small>Who can buy</small><b>Any nationality</b></div>
          <div><small>Average transaction</small><b>~30 days</b></div>
          <div><small>Deposit at MoU</small><b>10% (most cases)</b></div>
          <div><small>Title deed</small><b>Issued by DLD</b></div>
        </div>
      </div>
    </section>

    <!-- ===== STEPS ===== -->
    <section id="steps" style="padding-top: 40px">
      <div class="wrap gd-grid">
        <aside class="gd-nav glass" aria-label="Guide steps">
          <span class="eyebrow">Your progress</span>
          <div class="gd-prog">
            <b id="gdCount">0 of <?= count($steps) ?></b><span>steps done</span>
            <div class="gd-bar"><i id="gdBar"></i></div>
          </div>
          <ol id="gdNav">
            <?php foreach ($steps as $idx => $step): ?>
              <li>
                <a href="#s<?= $idx + 1 ?>" data-i="<?= $idx ?>">
                  <span class="gd-n"><?= sprintf('%02d', $idx + 1) ?></span>
                  <span class="gd-tt"><?= htmlspecialchars(guide_format_title($step['guide_head'])) ?></span>
                  <span class="gd-ck">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.6"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path d="M5 12.5l4.5 4.5L19 7.5"></path>
                    </svg>
                  </span>
                </a>
              </li>
            <?php endforeach; ?>
          </ol>
          <button class="gd-reset" type="button" id="gdReset" hidden="">Reset progress</button>
        </aside>
        <div class="gd-steps">
          <?php foreach ($steps as $idx => $step): ?>
            <article class="gd-step glass" id="s<?= $idx + 1 ?>" data-i="<?= $idx ?>">
              <div class="gd-sh">
                <span
                  class="gd-ic"
                  <?php if (!empty($step['guide_image'])): ?>
                  style="--ic: url('<?= htmlspecialchars(BASE_URL_IMG . 'guide/' . rawurlencode($step['guide_image'])) ?>');"
                  <?php endif; ?>
                ></span>
                <span class="gd-num">Step <?= sprintf('%02d', $idx + 1) ?></span>
                <label class="gd-done">
                  <input type="checkbox" data-i="<?= $idx ?>" />
                  <span>
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.6"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path d="M5 12.5l4.5 4.5L19 7.5"></path>
                    </svg>
                  </span>Done
                </label>
              </div>
              <h3 class="serif"><?= htmlspecialchars(guide_format_title($step['guide_head'])) ?></h3>
              <?= render_guide_step_description($step['guide_short_descp']) ?>
            </article>
          <?php endforeach; ?>

          <div class="gd-help glass">
            <div>
              <span class="eyebrow">Need a hand?</span>
              <h3 class="serif">Our consultants are <em class="gold-text">ready to assist you.</em></h3>
            </div>
            <div class="mc-acts">
              <a class="mc" href="tel:<?= htmlspecialchars($contact_phone_raw) ?>">
                <span>
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path
                      d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2"
                    ></path>
                  </svg>
                </span>
                <b><?= htmlspecialchars($contact_phone_display) ?></b><small>Call DRE</small>
              </a>
              <a class="mc" href="mailto:<?= htmlspecialchars($contact_email) ?>">
                <span>
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                    <path d="M3 7l9 6 9-6"></path>
                  </svg>
                </span>
                <b><?= htmlspecialchars($contact_email) ?></b><small>Email us</small>
              </a>
              <button class="mc" type="button" onclick="window.Popin &amp;&amp; Popin('open')">
                <span>
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <rect x="3" y="6" width="13" height="12" rx="2"></rect>
                    <path d="M16 10l5-3v10l-5-3"></path>
                  </svg>
                </span>
                <b>Live agent</b><small>Video call</small>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== TOOLS ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">Useful next steps</span>
            <h2 class="serif">Tools for <em class="gold-text">your journey.</em></h2>
          </div>
        </div>
        <div class="gd-tools reveal">
          <a class="gd-tool glass" href="<?= page_link('mortgage-calculator') ?>">
            <span class="mx-ic">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                <path d="M8 7h8M8 11h2M12 11h2M16 11h0M8 15h2M12 15h2M8 18h2M12 18h2"></path>
              </svg>
            </span>
            <b>Mortgage calculator</b><span>Work out your monthly payment.</span><i>→</i>
          </a>
          <a class="gd-tool glass" href="<?= page_link('golden-visa') ?>">
            <span class="mx-ic">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="2" y="5" width="20" height="14" rx="3"></rect>
                <path d="M2 10h20M6 15h4"></path>
              </svg>
            </span>
            <b>Golden Visa</b><span>AED 2M+ homes can qualify.</span><i>→</i>
          </a>
          <a class="gd-tool glass" href="<?= page_link('offplan-properties') ?>">
            <span class="mx-ic">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M4 21V5l10-2v18"></path>
                <path d="M14 7h6v4"></path>
                <path d="M20 11v3"></path>
                <path d="M8 21v-4h3v4"></path>
                <path d="M2 21h20"></path>
              </svg>
            </span>
            <b>Off-plan projects</b><span>Browse new launches.</span><i>→</i>
          </a>
          <a class="gd-tool glass" href="<?= page_link('buy-property') ?>">
            <span class="mx-ic">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M3 11l9-7 9 7"></path>
                <path d="M5 10v10h14V10"></path>
                <path d="M10 20v-5h4v5"></path>
              </svg>
            </span>
            <b>Ready homes</b><span>Move-in ready listings.</span><i>→</i>
          </a>
        </div>
      </div>
    </section>

    <!-- ===== OTHER GUIDES ===== -->
    <section style="padding-top: 30px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">More guides</span>
            <h2 class="serif">Other <em class="gold-text">guides</em></h2>
          </div>
        </div>
        <div class="gd-os reveal">
          <a class="gd-o glass" href="<?= page_link('seller-guide') ?>">
            <span class="mx-ic">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M3 12V4h8l10 10-8 8z"></path>
                <circle cx="7.5" cy="8.5" r="1.5"></circle>
              </svg>
            </span>
            <b>Seller guide</b><i>→</i>
          </a>
          <a class="gd-o glass" href="<?= page_link('tenant-guide') ?>">
            <span class="mx-ic">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="8" cy="15" r="4"></circle>
                <path d="M11 12l9-9M17 6l3 3M15 8l2 2"></path>
              </svg>
            </span>
            <b>Tenant guide</b><i>→</i>
          </a>
          <a class="gd-o glass" href="<?= page_link('landlord-guide') ?>">
            <span class="mx-ic">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="4" y="3" width="16" height="18" rx="1"></rect>
                <path d="M9 7h2M13 7h2M9 11h2M13 11h2M10 21v-3h4v3"></path>
              </svg>
            </span>
            <b>Landlord guide</b><i>→</i>
          </a>
        </div>
      </div>
    </section>

    <!-- ===== CONTACT FORM ===== -->
    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Ready to buy?</span>
            <h2 class="serif">Let’s find <em class="gold-text">your home.</em></h2>
            <p class="lead">
              Call DRE on <?= htmlspecialchars($contact_phone_display) ?> or email us at <?= htmlspecialchars($contact_email) ?> — or leave your details and a consultant
              will call you back.
            </p>
          </div>
          <form class="form" id="ctaForm" novalidate="">
            <input name="name" placeholder="Full name" autocomplete="name" required="" />
            <input
              id="ctaPhone"
              name="phone"
              type="tel"
              placeholder="Phone / WhatsApp"
              autocomplete="tel"
              required=""
            />
            <input type="hidden" name="phone_full" id="ctaPhoneFull" />
            <div class="err" id="ctaPhoneErr" role="alert">Please enter a valid phone number.</div>
            <select name="topic" aria-label="I’m interested in">
              <option value="">I’m interested in…</option>
              <option>Buying a ready home</option>
              <option>Buying off-plan</option>
              <option>Mortgage advice</option>
              <option>Golden Visa</option>
            </select>
            <input name="message" placeholder="Anything else? (optional)" />
            <button class="btn btn-gold" style="justify-content: center; margin-top: 6px">Request a call back</button>
          </form>
        </div>
      </div>
    </section>

<?php include('common/footer.php'); ?>

