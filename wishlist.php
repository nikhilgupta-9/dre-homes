<?php
// 1. CONFIG
include('db/config.php');

// 2. SEO: utility page, no admin tag row — kept simple and noindex-worthy.
$meta_tags = [
	'meta_title' => 'My Wishlist | DRE Real Estate',
	'meta_description' => 'The properties you have saved on DRE Real Estate, kept on this device.',
];

// 3. DATA: none from the server — the grid is filled from the visitor's own wishlist
// cookie by assets/js/site.js (#wsGrid), which calls ajax/get-wishlist.php for the cards.

// Reuses buy-property's card/filter styles (.ph-hero, .opg, .oc, .seg, .fsel) instead of
// duplicating ~700 lines of shared CSS; wishlist.css adds only what's unique to this page.
$page = [
	'active' => 'wishlist', // no nav item matches this on purpose — wishlist isn't a listing page, so nothing should highlight
	'css' => ['assets/css/pages/buy-property.css', 'assets/css/pages/wishlist.css'],
];

include('common/header.php');
?>
    <!-- ===== WISHLIST: HERO ===== -->
    <header class="ph-hero op-hero ws-hero">
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>">Home</a><span>/</span><span aria-current="page">Wishlist</span>
        </div>
        <span class="eyebrow">Your saved homes</span>
        <h1 class="serif">Your <em class="gold-text">wishlist.</em></h1>
        <p class="sub">All the homes you've hearted, in one place. Compare them, then ask us about any of them.</p>
      </div>
    </header>

    <!-- ===== WISHLIST ===== -->
    <section style="padding-top: 44px">
      <div class="wrap">
        <div id="wsMain" hidden="">
          <div class="ws-bar">
            <span class="ws-count" id="wsCount" aria-live="polite"></span>
            <div class="seg" id="wsTabs" role="group" aria-label="Filter by listing type">
              <button type="button" data-k="" class="on">All<small>0</small></button>
              <button type="button" data-k="1" hidden="">Off-plan<small>0</small></button>
              <button type="button" data-k="7" hidden="">Buy<small>0</small></button>
              <button type="button" data-k="8" hidden="">Rent<small>0</small></button>
              <button type="button" data-k="2" hidden="">Ready<small>0</small></button>
              <button type="button" data-k="3" hidden="">International<small>0</small></button>
            </div>
            <label class="fsel ws-sort">
              <span>Sort by</span>
              <select id="wsSort">
                <option value="saved">Default</option>
                <option value="low">Price: low to high</option>
                <option value="high">Price: high to low</option>
                <option value="name">Name: A to Z</option>
              </select>
            </label>
          </div>
          <div class="opg" id="wsGrid" aria-live="polite"></div>
          <div class="ws-actions">
            <a
              class="btn btn-gold ws-enq"
              id="wsEnquire"
              href="https://wa.me/971585837346"
              data-wa="971585837346"
              data-text="Hi DRE, I'd like to know more about these homes from my wishlist:"
              target="_blank"
              rel="noopener"
              ><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm4.5 12.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"></path></svg>Enquire about
              these on WhatsApp</a
            >
            <button class="ws-clear" id="wishClearAll" type="button">Clear wishlist</button>
          </div>
          <p class="ws-note">Your wishlist is saved on this device for a year — come back any time.</p>
        </div>

        <p class="ws-loading" id="wsLoading">Loading your saved homes…</p>
        <p class="ws-error" id="wsError" hidden="">We couldn't load all your saved homes. Please refresh the page.</p>

        <div class="ws-empty glass" id="wsEmpty" hidden="">
          <span class="ic" aria-hidden="true">♡</span>
          <h2 class="serif">Nothing saved <em class="gold-text">yet.</em></h2>
          <p>Tap the heart on any property and it will show up here.</p>
          <div class="ws-links">
            <a class="btn btn-gold" href="<?= BASE_URL ?>buy-property">Buy</a>
            <a class="btn btn-glass" href="<?= BASE_URL ?>offplan-properties">Off-plan</a>
            <a class="btn btn-glass" href="<?= BASE_URL ?>ready-to-move-in">Ready to move in</a>
            <a class="btn btn-glass" href="<?= BASE_URL ?>rent-property">Rent</a>
          </div>
        </div>
      </div>
    </section>

    <div class="ws-toast" id="wsToast" role="status" hidden="">
      <span id="wsToastMsg"></span><button id="wsUndo" type="button">Undo</button>
    </div>

    <section id="contact" style="padding-top: 20px">
      <div class="wrap">
        <div class="cta glass acc-edge reveal">
          <div>
            <span class="eyebrow">Private Consultation</span>
            <h2 class="serif">Your advisor is<br /><em class="gold-text">one call</em> away.</h2>
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
