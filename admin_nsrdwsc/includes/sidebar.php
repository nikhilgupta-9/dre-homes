<?php
// ---------------------------------------------------------------
// Admin menu. To add, move or rename an entry, edit the list below:
// the HTML is built from it further down.
//
//   section => list of entries
//   entry   => [label, icon, link, pages]            a single link
//           or [label, icon, [children], extra pages] a group that opens
//   child   => [label, link, pages]
//
// "pages" are the files on which the entry is shown as the current one
// (for example the add and edit screens that belong to a list).
// 'only' => 1 or 2 shows an entry to that login type only.
// ---------------------------------------------------------------
$dre_current_page = basename($_SERVER['PHP_SELF']);
$dre_login_type = (int) ($_SESSION['login_type'] ?? 0);
$dre_edit = '?edit=' . base64_encode(1);
$dre_listing_extras = ['manage-gallery.php', 'add-gallery.php', 'edit-gallery.php', 'manage-faq.php', 'add-faq.php', 'edit-faq.php', 'manage-payplan.php', 'add-payplan.php', 'edit-payplan.php'];

$dre_menu = [
	'Overview' => [
		['Dashboard', 'bi-speedometer2', 'dashboard.php', ['dashboard.php']],
		['Leads', 'bi-person-lines-fill', 'manage-leads.php', ['manage-leads.php', 'edit-leads.php'], 'only' => 1],
		['AI Chats', 'bi-chat-dots', 'manage-ai-chats.php', ['manage-ai-chats.php'], 'only' => 1],
		['Quiz Enquiries', 'bi-patch-question', 'manage-quiz.php', ['manage-quiz.php']],
	],

	'Properties' => [
		['Off-plan Projects', 'bi-buildings', 'manage-project.php', array_merge(['manage-project.php', 'add-project.php', 'edit-project.php'], $dre_listing_extras)],
		['Import from GenieMap', 'bi-geo-alt', 'import-geniemap.php', ['import-geniemap.php']],
		['Properties for Sale', 'bi-house-door', 'manage-buy-project.php', array_merge(['manage-buy-project.php', 'add-buy-project.php', 'edit-buy-project.php'], $dre_listing_extras)],
		['Properties for Rent', 'bi-key', 'manage-rent-project.php', array_merge(['manage-rent-project.php', 'edit-rent-project.php'], $dre_listing_extras)],
		['Pending Work', 'bi-hourglass-split', 'manage-pending-work-project.php', ['manage-pending-work-project.php']],
		['Developers', 'bi-building', [
			['All Developers', 'manage-developer.php', ['manage-developer.php', 'add-developer.php', 'edit-developer.php']],
			['Page Content', 'edit-developer-content.php' . $dre_edit, ['edit-developer-content.php']],
		], ['manage-chooseus.php', 'edit-chooseus.php']],
		['Communities', 'bi-geo-alt', [
			['All Communities', 'manage-community.php', ['manage-community.php', 'add-community.php', 'edit-community.php']],
			['Page Content', 'edit-community-content.php' . $dre_edit, ['edit-community-content.php']],
		], ['manage-comm-loc.php', 'add-comm-loc.php', 'edit-comm-loc.php', 'manage-comm-map.php', 'add-comm-map.php', 'edit-comm-map.php']],
		['Listing Page Content', 'bi-layout-text-window', [
			['Off-plan Page', 'edit-offplan-content.php' . $dre_edit, ['edit-offplan-content.php']],
			['Buy Page', 'edit-buyproperty-content.php' . $dre_edit, ['edit-buyproperty-content.php']],
			['Rent Page', 'edit-rentproperty-content.php' . $dre_edit, ['edit-rentproperty-content.php']],
			['Ready to Move In Page', 'edit-readytomovein-content.php' . $dre_edit, ['edit-readytomovein-content.php']],
			['International Page', 'edit-international-content.php' . $dre_edit, ['edit-international-content.php']],
		]],
	],

	'Content' => [
		['Blog', 'bi-journal-text', [
			['All Posts', 'manage-blog.php', ['manage-blog.php', 'add-blog.php', 'edit-blog.php']],
			['Page Content', 'edit-blog-content.php' . $dre_edit, ['edit-blog-content.php']],
		]],
		['Company News', 'bi-newspaper', [
			['All News', 'manage-company-news.php', ['manage-company-news.php', 'add-company-news.php', 'edit-company-news.php']],
			['Page Content', 'edit-company-news-content.php' . $dre_edit, ['edit-company-news-content.php']],
		]],
		['Guides', 'bi-compass', [
			['All Guides', 'manage-guide.php', ['manage-guide.php']],
			['Add Guide', 'add-guide.php', ['add-guide.php']],
		], ['manage-guide-image.php', 'add-guide-image.php', 'edit-guide-image.php']],
		['Podcast', 'bi-mic', 'manage-podcast.php', ['manage-podcast.php']],
		['Media Gallery', 'bi-images', [
			['All Media', 'manage-media.php', ['manage-media.php', 'add-media.php', 'edit-media.php']],
			['Page Content', 'edit-media-content.php' . $dre_edit, ['edit-media-content.php']],
		]],
		['Testimonials', 'bi-chat-quote', [
			['All Testimonials', 'manage-testimonial.php', ['manage-testimonial.php', 'add-testimonial.php', 'edit-testimonial.php']],
			['Page Content', 'edit-testimonial-content.php' . $dre_edit, ['edit-testimonial-content.php']],
		]],
		['Google Rating', 'bi-google', 'edit-google-rating.php' . $dre_edit, ['edit-google-rating.php'], 'only' => 2],
		['Awards', 'bi-trophy', [
			['All Awards', 'manage-awards.php', ['manage-awards.php', 'add-awards.php', 'edit-awards.php']],
			['Page Content', 'edit-awards-content.php' . $dre_edit, ['edit-awards-content.php']],
		]],
	],

	'Website Pages' => [
		['Home Page', 'bi-house', [
			['Hero Section', 'manage-hero-section.php', ['manage-hero-section.php', 'edit-hero-section.php']],
			['Homepage Links', 'manage-homepage-links.php', ['manage-homepage-links.php', 'add-homepage-links.php', 'edit-homepage-links.php']],
			['Instagram', 'manage-instagram-content.php', ['manage-instagram-content.php']],
		]],
		['About Us', 'bi-info-circle', [
			['Page Content', 'edit-about-content.php' . $dre_edit, ['manage-about-content.php', 'edit-about-content.php']],
			['Counters', 'manage-about-counter.php', ['manage-about-counter.php', 'add-about-counter.php', 'edit-about-counter.php']],
			['Points', 'manage-about-service.php', ['manage-about-service.php', 'add-about-service.php', 'edit-about-service.php']],
		], ['add-about-slider.php', 'edit-about-slider.php', 'edit-about-service-image.php', 'edit-about-details.php', 'edit-mission-vision.php']],
		['Leadership Messages', 'bi-envelope-open', [
			['Message from CEO', 'edit-message-from-ceo.php' . $dre_edit, ['edit-message-from-ceo.php']],
			['Message from CLO', 'edit-message-from-clo.php' . $dre_edit, ['edit-message-from-clo.php']],
			['Message from MD', 'edit-message-from-md.php' . $dre_edit, ['edit-message-from-md.php']],
		]],
		['Team', 'bi-people', [
			['Team Members', 'manage-team.php', ['manage-team.php', 'add-team.php', 'edit-team.php']],
			['Arrange Order', 'arrange-team.php', ['arrange-team.php']],
			['Categories', 'manage-team-category.php', ['manage-team-category.php', 'add-team-category.php', 'edit-team-category.php']],
			['Designations', 'manage-team-designation.php', ['manage-team-designation.php', 'add-team-designation.php', 'edit-team-designation.php']],
			['Page Content', 'edit-team-details.php' . $dre_edit, ['edit-team-details.php']],
		]],
		['Brand Video', 'bi-camera-reels', 'edit-brand-video-content.php' . $dre_edit, ['edit-brand-video-content.php']],
		['Legal Pages', 'bi-shield-lock', 'edit-privacy-policy-content.php' . $dre_edit, ['edit-privacy-policy-content.php']],
	],

	'Services & Tools' => [
		['Services', 'bi-briefcase', [
			['All Services', 'manage-service-section.php', ['manage-service-section.php', 'edit-service-section.php']],
			['Add Service', 'add-service-section.php', ['add-service-section.php']],
		]],
		['Golden Visa', 'bi-gem', [
			['Page Content', 'edit-golden-visa-content.php' . $dre_edit, ['edit-golden-visa-content.php']],
			['Visa Benefits', 'manage-services.php', ['manage-services.php', 'add-services.php', 'edit-services.php']],
			['Facilities', 'manage-facilities.php', ['manage-facilities.php', 'add-facilities.php', 'edit-facilities.php']],
		]],
		['Mortgage', 'bi-calculator', [
			['Page Content', 'edit-mortgage-content.php' . $dre_edit, ['edit-mortgage-content.php']],
			['Banks', 'manage-bank.php', ['manage-bank.php', 'add-bank.php', 'edit-bank.php']],
			['Advisor Steps', 'manage-advisor.php', ['manage-advisor.php', 'add-advisor.php', 'edit-advisor.php']],
			['Calculator Types', 'manage-taxes.php', ['manage-taxes.php', 'add-taxes.php', 'edit-taxes.php']],
		]],
		['Book Valuation', 'bi-clipboard-check', [
			['Valuation Items', 'manage-book-valuation.php', ['manage-book-valuation.php', 'add-book-valuation.php', 'edit-book-valuation.php']],
			['Page Content', 'edit-book-valuation-content.php' . $dre_edit, ['edit-book-valuation-content.php']],
		]],
		['Property Quiz Setup', 'bi-ui-checks', [
			['Units', 'manage-units.php', ['manage-units.php', 'add-units.php', 'edit-units.php']],
			['Price Ranges', 'manage-price.php', ['manage-price.php', 'add-price.php', 'edit-price.php']],
			['Construction Types', 'manage-construction.php', ['manage-construction.php', 'add-construction.php', 'edit-construction.php']],
			['Purchase Options', 'manage-purchase.php', ['manage-purchase.php', 'add-purchase.php', 'edit-purchase.php']],
		]],
	],

	'Careers' => [
		['Job Applications', 'bi-file-earmark-person', 'manage-resume.php', ['manage-resume.php']],
		['Open Positions', 'bi-person-workspace', 'manage-position.php', ['manage-position.php', 'add-position.php', 'edit-position.php']],
		['Careers Page', 'bi-layout-text-sidebar', [
			['Page Content', 'edit-career-content.php' . $dre_edit, ['edit-career-content.php']],
			['Awards', 'manage-career-awards.php', ['manage-career-awards.php', 'add-career-awards.php', 'edit-career-awards.php']],
			['Focus Areas', 'manage-focus-area.php', ['manage-focus-area.php', 'add-focus-area.php', 'edit-focus-area.php']],
			['Gallery', 'manage-career-gallery.php', ['manage-career-gallery.php', 'add-career-gallery.php', 'edit-career-gallery.php']],
		]],
	],

	'Settings' => [
		['My Profile', 'bi-person-circle', 'my-profile.php', ['my-profile.php']],
		['SEO Meta Tags', 'bi-tags', 'manage-tags.php', ['manage-tags.php', 'edit-tags.php']],
		['Header & Footer', 'bi-window-desktop', [
			['Header Info', 'edit-header-info.php' . $dre_edit, ['edit-header-info.php']],
			['Footer Info', 'edit-footer-info.php' . $dre_edit, ['edit-footer-info.php']],
		]],
		['Users', 'bi-person-gear', [
			['All Users', 'manage-user.php', ['manage-user.php', 'edit-user.php']],
			['Add User', 'add-user.php', ['add-user.php']],
		], 'only' => 1],
		['Logs', 'bi-clock-history', [
			['Activity Log', 'manage-loggs.php', ['manage-loggs.php']],
			['Login Log', 'manage-login-logs.php', ['manage-login-logs.php']],
			['Deleted Data Log', 'manage-delete-logs.php', ['manage-delete-logs.php']],
		], 'only' => 1],
	],
];
?>
<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto dre-aside" id="kt_aside">
	<!--begin::Brand-->
	<div class="brand flex-column-auto" id="kt_brand">
		<!--begin::Logo-->
		<a href="<?= BASE_URL ?>dashboard.php" class="brand-logo">
			<img alt="DRE Homes" src="<?= BASE_URL ?>assets/media/logos/logo-small.png" />
			<span class="dre-brand-text">Admin</span>
		</a>
		<!--end::Logo-->
		<!--begin::Toggle-->
		<button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle" aria-label="Collapse or expand the menu">
			<i class="bi bi-chevron-double-left"></i>
		</button>
		<!--end::Toolbar-->
	</div>
	<!--end::Brand-->

	<div class="dre-menu-search">
		<i class="bi bi-search"></i>
		<input type="text" id="dreMenuSearch" placeholder="Search menu..." autocomplete="off" aria-label="Search the menu">
	</div>

	<!--begin::Aside Menu-->
	<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
		<!--begin::Menu Container-->
		<div id="kt_aside_menu" class="aside-menu my-2" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
			<!--begin::Menu Nav-->
			<ul class="menu-nav">
				<?php foreach ($dre_menu as $dre_section => $dre_entries):
					// Leave out entries this login type may not see, and sections that end up empty.
					$dre_visible = [];
					foreach ($dre_entries as $dre_entry) {
						if (!isset($dre_entry['only']) || $dre_entry['only'] === $dre_login_type) {
							$dre_visible[] = $dre_entry;
						}
					}
					if (!$dre_visible) {
						continue;
					}
				?>
					<li class="menu-section">
						<h4 class="menu-text"><?= htmlspecialchars($dre_section) ?></h4>
						<i class="menu-icon bi bi-three-dots"></i>
					</li>
					<?php foreach ($dre_visible as $dre_entry):
						if (is_array($dre_entry[2])):
							// A group: open when the current page belongs to one of its children.
							$dre_open = in_array($dre_current_page, $dre_entry[3] ?? [], true);
							foreach ($dre_entry[2] as $dre_child) {
								if (in_array($dre_current_page, $dre_child[2], true)) {
									$dre_open = true;
								}
							}
					?>
							<li class="menu-item menu-item-submenu <?= $dre_open ? 'menu-item-open menu-item-here' : '' ?>" aria-haspopup="true" data-menu-toggle="hover">
								<a href="javascript:;" class="menu-link menu-toggle">
									<span class="svg-icon menu-icon"><i class="bi <?= $dre_entry[1] ?>"></i></span>
									<span class="menu-text"><?= htmlspecialchars($dre_entry[0]) ?></span>
									<i class="menu-arrow"></i>
								</a>
								<div class="menu-submenu">
									<i class="menu-arrow"></i>
									<ul class="menu-subnav">
										<?php foreach ($dre_entry[2] as $dre_child): ?>
											<li class="menu-item <?= in_array($dre_current_page, $dre_child[2], true) ? 'menu-item-active' : '' ?>" aria-haspopup="true">
												<a href="<?= BASE_URL . $dre_child[1] ?>" class="menu-link">
													<i class="menu-bullet menu-bullet-dot"><span></span></i>
													<span class="menu-text"><?= htmlspecialchars($dre_child[0]) ?></span>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							</li>
						<?php else: ?>
							<li class="menu-item <?= in_array($dre_current_page, $dre_entry[3], true) ? 'menu-item-active' : '' ?>" aria-haspopup="true">
								<a href="<?= BASE_URL . $dre_entry[2] ?>" class="menu-link">
									<span class="svg-icon menu-icon"><i class="bi <?= $dre_entry[1] ?>"></i></span>
									<span class="menu-text"><?= htmlspecialchars($dre_entry[0]) ?></span>
								</a>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endforeach; ?>
				<li class="dre-menu-empty" style="display: none;">Nothing in the menu matches.</li>
			</ul>
			<!--end::Menu Nav-->
		</div>
		<!--end::Menu Container-->
	</div>
	<!--end::Aside Menu-->

	<div class="dre-aside-foot">
		<a href="<?= BASE_URL_HOME ?>" target="_blank" rel="noopener" title="Open the website"><i class="bi bi-box-arrow-up-right"></i><span>View website</span></a>
		<a href="<?= BASE_URL ?>logout.php" title="Sign out"><i class="bi bi-box-arrow-right"></i><span>Sign out</span></a>
	</div>
</div>
<script>
	// Menu search: shows matching entries, opens the groups they are in, hides empty sections.
	(function () {
		var input = document.getElementById('dreMenuSearch');
		var nav = document.querySelector('#kt_aside_menu .menu-nav');
		if (!input || !nav) {
			return;
		}
		var opened = null;

		input.addEventListener('input', function () {
			var query = input.value.trim().toLowerCase();
			var topItems = nav.querySelectorAll(':scope > li.menu-item');

			// Remember which groups were open before the first keystroke, to restore them afterwards.
			if (opened === null) {
				opened = [];
				topItems.forEach(function (item) {
					if (item.classList.contains('menu-item-open')) {
						opened.push(item);
					}
				});
			}

			var found = 0;
			topItems.forEach(function (item) {
				var label = item.querySelector(':scope > a .menu-text').textContent.toLowerCase();
				var children = item.querySelectorAll('.menu-subnav > li.menu-item');
				var ownMatch = query === '' || label.indexOf(query) !== -1;
				var childMatch = false;

				children.forEach(function (child) {
					var match = ownMatch || child.textContent.toLowerCase().indexOf(query) !== -1;
					child.style.display = match ? '' : 'none';
					childMatch = childMatch || match;
				});

				var show = ownMatch || childMatch;
				item.style.display = show ? '' : 'none';
				if (children.length) {
					item.classList.toggle('menu-item-open', query === '' ? opened.indexOf(item) !== -1 : show);
				}
				found += show ? 1 : 0;
			});

			// A section title stays only while something under it is visible.
			nav.querySelectorAll(':scope > li.menu-section').forEach(function (section) {
				var visible = false;
				var next = section.nextElementSibling;
				while (next && !next.classList.contains('menu-section')) {
					if (next.classList.contains('menu-item') && next.style.display !== 'none') {
						visible = true;
					}
					next = next.nextElementSibling;
				}
				section.style.display = visible ? '' : 'none';
			});

			nav.querySelector('.dre-menu-empty').style.display = found === 0 ? '' : 'none';
			if (query === '') {
				opened = null;
			}
		});
	})();
</script>
