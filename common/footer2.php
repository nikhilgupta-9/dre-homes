<?php
/*
 * Shared footer, quick-contact dock, AI panel and scripts for the new UI.
 * Uses $header_info, $footer_info and $page from common/header.php.
 */
$footer_info = $footer_info ?? (select_row('tbl_footer_info', 'is_active=1') ?: []);
$header_info = $header_info ?? (select_row('tbl_header_info', 'is_active=1') ?: []);
$page = $page ?? ['js' => []];

// Top communities and developers by number of projects (same lists as the old footer)
$footer_comms = [];
$res = mysqli_query($conn, "SELECT tc.comm_slug, tc.comm_heading, COUNT(*) AS total_proj
	FROM tbl_proj AS tp INNER JOIN tbl_community AS tc ON tp.prj_comm_id = tc.comm_id
	WHERE tc.is_active = 1 GROUP BY tc.comm_id ORDER BY total_proj DESC LIMIT 5 OFFSET 1");
while ($res && $row = mysqli_fetch_assoc($res)) {
	$footer_comms[] = $row;
}
$footer_devs = [];
$res = mysqli_query($conn, "SELECT td.dev_slug, td.dev_heading, COUNT(*) AS total_proj
	FROM tbl_proj AS tp INNER JOIN tbl_developer AS td ON tp.prj_dev_id = td.dev_id
	GROUP BY td.dev_id ORDER BY total_proj DESC LIMIT 5");
while ($res && $row = mysqli_fetch_assoc($res)) {
	$footer_devs[] = $row;
}

// "Park Heights Square 2 | Offices 401-406 | Dubai Hills Estate | Dubai | UAE"
//   -> "Park Heights Square 2, Offices 401-406, Dubai Hills Estate" (city and country are implied)
$addr = function ($a) {
	$parts = array_values(array_filter(array_map('trim', explode('|', (string) $a))));
	while (count($parts) > 1 && preg_match('/^(dubai|uae|united arab emirates)$/i', end($parts))) {
		array_pop($parts);
	}
	return implode(', ', $parts);
};
$tel = fn($n) => preg_replace('/[^0-9+]/', '', (string) $n);

$socials = [
	'header_fb_link' => ['Facebook', '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 8.5V6.8c0-.8.2-1.3 1.4-1.3H17V2.6c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.4-4 4.1v1.9H8v3.2h2.6V22H14v-10.3h2.7l.4-3.2z"></path></svg>'],
	'header_instagram_link' => ['Instagram', '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1" fill="currentColor"></circle></svg>'],
	'header_linkedin_link' => ['LinkedIn', '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5a2.5 2.5 0 110 5 2.5 2.5 0 010-5zM3 9.5h4V21H3zM9.5 9.5h3.8v1.6h.1c.5-1 1.8-2 3.8-2 4 0 4.8 2.6 4.8 6V21h-4v-5.2c0-1.2 0-2.8-1.7-2.8s-2 1.3-2 2.7V21h-4z"></path></svg>'],
	'header_youtube_link' => ['YouTube', '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 7.2a3 3 0 00-2.1-2.1C19 4.6 12 4.6 12 4.6s-7 0-8.9.5A3 3 0 001 7.2 31 31 0 00.5 12c0 1.6.2 3.2.5 4.8a3 3 0 002.1 2.1c1.9.5 8.9.5 8.9.5s7 0 8.9-.5a3 3 0 002.1-2.1c.3-1.6.5-3.2.5-4.8s-.2-3.2-.5-4.8zM9.8 15.1V8.9l5.8 3.1z"></path></svg>'],
	// the admin field is called "spotify" but holds the TikTok link
	'header_spotify_link' => ['TikTok', '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M16.6 2h-3.4v13.4a2.9 2.9 0 11-2.9-2.9c.3 0 .6 0 .9.1V9.2a6.3 6.3 0 105.4 6.2V8.7a8 8 0 004.4 1.3V6.6a4.5 4.5 0 01-4.4-4.6z"></path></svg>'],
	'header_twitter_link' => ['X', '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.8 3h3.1l-6.8 7.7L22 21h-6.2l-4.9-6.3L5.3 21H2.2l7.3-8.3L2 3h6.4l4.4 5.8zm-1.1 16.2h1.7L7.4 4.7H5.6z"></path></svg>'],
];
$insta = trim($header_info['header_instagram_link'] ?? '');
$insta_handle = $insta ? '@' . trim(parse_url($insta, PHP_URL_PATH) ?? '', '/') : '';
$permit = !empty($footer_info['permit_number']) ? $footer_info['permit_number'] : '1007935000';
$gptw_logo = !empty($header_info['greate_place_logo']) ? BASE_URL_IMG . 'header/' . $header_info['greate_place_logo'] : '';

// Bottom bar: the design's one-line legal summary, with the numbers taken from the admin's copyright text
$copyright = strip_tags(html_entity_decode($footer_info['footer_copyright_text'] ?? '', ENT_QUOTES, 'UTF-8'));
$license = preg_match('/licen[cs]e\s*(?:no\.?|number|#)?\s*:?\s*(\d+)/i', $copyright, $m) ? $m[1] : '599208';
$rera_office = preg_match('/office\s*(?:no\.?|number|#)?\s*:?\s*(\d+)/i', $copyright, $m) ? $m[1] : '652';
$legal_line = "Registered in Dubai, UAE · License No. $license · RERA office $rera_office · Permit $permit";
?>
    <footer>
      <div class="wrap">
        <div class="f-box glass">
          <div class="f-grid row">
            <div class="col-12 col-md-6 f-col">
              <a href="<?= BASE_URL ?>" aria-label="DRE Homes — home"
                ><img
                  class="site-logo"
                  src="<?= BASE_URL_IMG ?>footer/<?= e($footer_info['footer_logo'] ?? '') ?>"
                  alt="<?= e($footer_info['footer_logo_alt'] ?? 'DRE logo') ?>"
                  width="250"
                  height="110"
                  style="height: 72px; margin-left: -10px"
              /></a>
              <p style="margin-top: 16px; max-width: 300px">
<?php if (!empty($footer_info['footer_address'])) { ?>
                HQ — <?= e($addr($footer_info['footer_address'])) ?>
<?php } ?>
<?php if (!empty($footer_info['footer_address2'])) { ?>
                <br /><br />Town Square Branch — <?= e($addr($footer_info['footer_address2'])) ?>
<?php } ?>
              </p>
              <div class="socials" aria-label="Follow DRE">
<?php foreach ($socials as $col => [$net, $svg]) {
	if (empty(trim($header_info[$col] ?? ''))) continue; ?>
                <a class="so" href="<?= e($header_info[$col]) ?>" target="_blank" rel="noopener" aria-label="<?= $net ?>" title="<?= $net ?>"><?= $svg ?></a>
<?php } ?>
              </div>
<?php if ($gptw_logo) { ?>
              <div class="f-award">
                <img src="<?= e($gptw_logo) ?>" alt="Great Place To Work — Best Workplaces in Real Estate, Middle East 2026" loading="lazy" />
                <div>
                  <b>Best Workplaces<sup style="font-size: 0.55em">™</sup> in Real Estate</b
                  ><small>Great Place To Work® · Middle East 2026</small>
                </div>
              </div>
<?php } ?>
            </div>
            <div class="col-12 col-md-6 f-col">
              <h5>Communities</h5>
              <ul>
<?php foreach ($footer_comms as $row) { ?>
                <li><a href="<?= BASE_URL ?>community/<?= e($row['comm_slug']) ?>"><?= e($row['comm_heading']) ?></a><em><?= (int) $row['total_proj'] ?></em></li>
<?php } ?>
              </ul>
            </div>
            <div class="col-12 col-md-6 f-col">
              <h5>Developers</h5>
              <ul>
<?php foreach ($footer_devs as $row) { ?>
                <li><a href="<?= BASE_URL ?>developer/<?= e($row['dev_slug']) ?>"><?= e($row['dev_heading']) ?></a><em><?= (int) $row['total_proj'] ?></em></li>
<?php } ?>
              </ul>
            </div>
            <div class="col-12 col-md-6 f-col">
              <h5>Get in touch</h5>
              <ul>
<?php if (!empty($footer_info['footer_contact_number'])) { ?>
                <li><a href="tel:<?= e($tel($footer_info['footer_contact_number'])) ?>">Toll free · <?= e($footer_info['footer_contact_number']) ?></a></li>
<?php } ?>
<?php if (!empty($footer_info['footer_contact_number2'])) { ?>
                <li><a href="tel:<?= e($tel($footer_info['footer_contact_number2'])) ?>">Hotline · <?= e($footer_info['footer_contact_number2']) ?></a></li>
<?php } ?>
<?php if (!empty($footer_info['footer_email_id'])) { ?>
                <li><a href="mailto:<?= e($footer_info['footer_email_id']) ?>"><?= e($footer_info['footer_email_id']) ?></a></li>
<?php } ?>
<?php if ($insta) { ?>
                <li><a href="<?= e($insta) ?>" target="_blank" rel="noopener">Instagram · <?= e($insta_handle) ?></a></li>
<?php } ?>
              </ul>
              <div class="f-qr">
                <a href="<?= BASE_URL ?>images/dre-qr-code-26.jpeg" target="_blank" rel="noopener" title="Open permit QR code"
                  ><img src="<?= BASE_URL ?>images/dre-qr-code-26.jpeg" alt="DRE permit QR code — Permit No. <?= e($permit) ?>" loading="lazy"
                /></a>
                <div><b>Permit Number</b><span><?= e($permit) ?></span><small>Scan to verify with DLD / RERA</small></div>
              </div>
            </div>
          </div>
        </div>
        <div class="f-bot">
          <span title="<?= e($copyright) ?>"><?= e($legal_line) ?></span
          ><span
            ><a href="<?= page_link('privacy-policy') ?>">Privacy Policy</a> ·
            <a href="<?= page_link('terms-conditions') ?>">Terms &amp; Conditions</a></span
          >
        </div>
      </div>
    </footer>

    <!-- SIDE DOCK -->
    <div class="dock glass" role="toolbar" aria-label="Quick contact">
      <span
        class="dk-grip"
        tabindex="0"
        role="button"
        aria-label="Move quick contact bar: drag, or use arrow keys. Double-click or Enter to reset."
        title="Drag to move · double-click to reset"
      ></span>
      <button class="dk ai" id="aiBtn" aria-expanded="false" aria-controls="aiPanel" aria-label="AI Assistant">
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2l1.9 5.6L19.5 9.5l-5.6 1.9L12 17l-1.9-5.6L4.5 9.5l5.6-1.9z"></path>
          <path d="M19 14l.9 2.6 2.6.9-2.6.9L19 21l-.9-2.6-2.6-.9 2.6-.9z" opacity=".7"></path></svg
        ><span class="tip">Ask DRE AI</span>
      </button>
      <button class="dk" id="popinBtn" aria-label="Live video agent">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <rect x="2.5" y="6" width="13" height="12" rx="3"></rect>
          <path d="M15.5 10.5l6-3.5v10l-6-3.5z"></path></svg
        ><span class="dot"></span><span class="tip">Live Agent · Video call</span>
      </button>
      <a
        class="dk wa"
        href="https://wa.me/971585837346?text=Just%20visited%20your%20website!%20Would%20like%20to%20know%20more."
        target="_blank"
        rel="noopener"
        aria-label="WhatsApp"
        ><svg viewBox="0 0 24 24" fill="currentColor">
          <path
            d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm0 18.2c-1.5 0-3-.4-4.3-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1112 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.3-.4.3-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 2s.8 2.3.9 2.5c.1.2 1.6 2.5 4 3.5 1.5.6 2.1.7 2.8.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.1-1.2l-.4-.2z"
          ></path></svg
        ><span class="tip">WhatsApp us</span></a
      >
      <button class="dk mode" id="modeBtn" aria-pressed="false" aria-label="Night mode">
        <svg
          class="i-moon"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.8"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path d="M20.5 14.2A8.5 8.5 0 019.8 3.5a8.5 8.5 0 1010.7 10.7z"></path></svg
        ><svg
          class="i-sun"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.8"
          stroke-linecap="round"
        >
          <circle cx="12" cy="12" r="4.2"></circle>
          <path
            d="M12 2.5v2.2M12 19.3v2.2M4.6 4.6l1.6 1.6M17.8 17.8l1.6 1.6M2.5 12h2.2M19.3 12h2.2M4.6 19.4l1.6-1.6M17.8 6.2l1.6-1.6"
          ></path></svg
        ><span class="tip">Night mode</span>
      </button>
    </div>

    <div class="ai-panel glass" id="aiPanel" role="dialog" aria-label="DRE AI Assistant">
      <div class="ai-h">
        <span class="av"
          ><svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2l1.9 5.6L19.5 9.5l-5.6 1.9L12 17l-1.9-5.6L4.5 9.5l5.6-1.9z"></path>
            <path d="M19 14l.9 2.6 2.6.9-2.6.9L19 21l-.9-2.6-2.6-.9 2.6-.9z" opacity=".7"></path></svg
        ></span>
        <div><b>DRE AI Assistant</b><small>Online · replies instantly</small></div>
        <button class="x" id="aiClose" aria-label="Close">×</button>
      </div>
      <div class="ai-body" id="aiBody"></div>
      <div class="ai-chips" id="aiChips">
        <button>Off-plan under AED 2M</button><button>Golden Visa</button><button>Book a viewing</button
        ><button>Best areas to invest</button><button>Talk to a human</button>
      </div>
      <form class="ai-f" id="aiForm">
        <input id="aiIn" placeholder="Ask about properties, areas, visas…" autocomplete="off" /><button
          aria-label="Send"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 12h15M13 6l6 6-6 6"></path>
          </svg>
        </button>
      </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset_url('assets/js/main.js') ?>"></script>
    <script src="<?= asset_url('assets/js/site.js') ?>"></script>
<?php foreach ($page['js'] ?? [] as $js) { ?>
    <script src="<?= e(asset_url($js)) ?>"></script>
<?php } ?>
  </body>
</html>
