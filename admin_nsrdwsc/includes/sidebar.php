<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
	<!--begin::Brand-->
	<div class="brand flex-column-auto" id="kt_brand">
		<!--begin::Logo-->
		<a href="<?= BASE_URL ?>dashboard.php" class="brand-logo">
			<img alt="Logo" src="<?= BASE_URL ?>assets/media/logos/logo-small.png" />
		</a>
		<!--end::Logo-->
		<!--begin::Toggle-->
		<button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
			<span class="svg-icon svg-icon svg-icon-xl">
				<!--begin::Svg Icon | path:<?= BASE_URL ?>assets/media/svg/icons/Navigation/Angle-double-left.svg-->
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<polygon points="0 0 24 0 24 24 0 24" />
						<path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
						<path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
					</g>
				</svg>
				<!--end::Svg Icon-->
			</span>
		</button>
		<!--end::Toolbar-->
	</div>
	<!--end::Brand-->
	<!--begin::Aside Menu-->
	<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
		<!--begin::Menu Container-->
		<div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
			<!--begin::Menu Nav-->
			<ul class="menu-nav">
				<li class="menu-item p-2">
					<form action="" method="post">
						<div class="input-group ">
							<span class="input-group-text bg-light border-0">
								<i class="bi bi-search"></i>
							</span>
							<input
								type="text"
								class="form-control border-0 bg-light"
								placeholder="Search Menu..."
								value="<?= htmlspecialchars($search ?? '') ?>"
								id="menuSearch"
								name="search_menu">
						</div>
					</form>
				</li>
				<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
					<a href="<?= BASE_URL ?>dashboard.php" class="menu-link">
						<span class="svg-icon menu-icon">
							<i class="bi bi-speedometer2 fs-2"></i>
						</span>
						<span class="menu-text">Dashboard</span>
					</a>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'manage-project.php' || basename($_SERVER['PHP_SELF']) == 'manage-buy-project.php' || basename($_SERVER['PHP_SELF']) == 'add-buy-project.php' || basename($_SERVER['PHP_SELF']) == 'edit-buy-project.php'  || basename($_SERVER['PHP_SELF']) == 'manage-rent-project.php' || basename($_SERVER['PHP_SELF']) == 'add-project.php' || basename($_SERVER['PHP_SELF']) == 'edit-project.php' || basename($_SERVER['PHP_SELF']) == 'manage-gallery.php' || basename($_SERVER['PHP_SELF']) == 'add-gallery.php' || basename($_SERVER['PHP_SELF']) == 'edit-gallery.php' || basename($_SERVER['PHP_SELF']) == 'edit-buyproperty-content.php'  || basename($_SERVER['PHP_SELF']) == 'edit-rentproperty-content.php' || basename($_SERVER['PHP_SELF']) == 'edit-readytomovein-content.php' || basename($_SERVER['PHP_SELF']) == 'edit-offplan-content.php' || basename($_SERVER['PHP_SELF']) == 'manage-faq.php' || basename($_SERVER['PHP_SELF']) == 'add-faq.php' || basename($_SERVER['PHP_SELF']) == 'edit-faq.php' || basename($_SERVER['PHP_SELF']) == 'edit-international-content.php'  || basename($_SERVER['PHP_SELF']) == 'manage-payplan.php' || basename($_SERVER['PHP_SELF']) == 'add-payplan.php' || basename($_SERVER['PHP_SELF']) == 'edit-payplan.php' || basename($_SERVER['PHP_SELF']) == 'manage-pending-work-project.php' || basename($_SERVER['PHP_SELF']) == 'edit-rent-project.php' ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-kanban fs-2"></i>
						</span>
						<span class="menu-text">Projects</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-pending-work-project.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-pending-work-project.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Pending Work</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-buyproperty-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-buyproperty-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Buy Property Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-rentproperty-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-rentproperty-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Rent Property Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-readytomovein-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-readytomovein-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Ready to Move In Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-offplan-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-offplan-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Offplan In Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-international-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-international-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">International Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-project.php' || basename($_SERVER['PHP_SELF']) == 'add-project.php' || basename($_SERVER['PHP_SELF']) == 'edit-project.php' || basename($_SERVER['PHP_SELF']) == 'manage-gallery.php' || basename($_SERVER['PHP_SELF']) == 'add-gallery.php' || basename($_SERVER['PHP_SELF']) == 'edit-gallery.php' || basename($_SERVER['PHP_SELF']) == 'manage-faq.php' || basename($_SERVER['PHP_SELF']) == 'add-faq.php' || basename($_SERVER['PHP_SELF']) == 'edit-faq.php' || basename($_SERVER['PHP_SELF']) == 'manage-payplan.php' || basename($_SERVER['PHP_SELF']) == 'add-payplan.php' || basename($_SERVER['PHP_SELF']) == 'edit-payplan.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-project.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage Offplan Project</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-buy-project.php' || basename($_SERVER['PHP_SELF']) == 'add-buy-project.php' || basename($_SERVER['PHP_SELF']) == 'edit-buy-project.php' || basename($_SERVER['PHP_SELF']) == 'manage-gallery.php' || basename($_SERVER['PHP_SELF']) == 'add-gallery.php' || basename($_SERVER['PHP_SELF']) == 'edit-gallery.php' || basename($_SERVER['PHP_SELF']) == 'manage-faq.php' || basename($_SERVER['PHP_SELF']) == 'add-faq.php' || basename($_SERVER['PHP_SELF']) == 'edit-faq.php' || basename($_SERVER['PHP_SELF']) == 'manage-payplan.php' || basename($_SERVER['PHP_SELF']) == 'add-payplan.php' || basename($_SERVER['PHP_SELF']) == 'edit-payplan.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-buy-project.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage Buy Project</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-rent-project.php' || basename($_SERVER['PHP_SELF']) == 'manage-gallery.php' || basename($_SERVER['PHP_SELF']) == 'add-gallery.php' || basename($_SERVER['PHP_SELF']) == 'edit-gallery.php' || basename($_SERVER['PHP_SELF']) == 'manage-faq.php' || basename($_SERVER['PHP_SELF']) == 'add-faq.php' || basename($_SERVER['PHP_SELF']) == 'edit-faq.php' || basename($_SERVER['PHP_SELF']) == 'manage-payplan.php' || basename($_SERVER['PHP_SELF']) == 'add-payplan.php' || basename($_SERVER['PHP_SELF']) == 'edit-payplan.php' || basename($_SERVER['PHP_SELF']) == 'edit-rent-project.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-rent-project.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage Rent Project</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-team-details.php' || basename($_SERVER['PHP_SELF']) == 'manage-team-category.php' || basename($_SERVER['PHP_SELF']) == 'add-team-category.php' || basename($_SERVER['PHP_SELF']) == 'edit-team-category.php' || basename($_SERVER['PHP_SELF']) == 'manage-team.php' || basename($_SERVER['PHP_SELF']) == 'add-team.php' || basename($_SERVER['PHP_SELF']) == 'edit-team.php' || basename($_SERVER['PHP_SELF']) == 'manage-team-designation.php' || basename($_SERVER['PHP_SELF']) == 'edit-team-designation.php' || basename($_SERVER['PHP_SELF']) == 'add-team-designation.php'   ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-people fs-2"></i>
						</span>
						<span class="menu-text">Team</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-team-details.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-team-details.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Team Details</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-team-category.php' || basename($_SERVER['PHP_SELF']) == 'add-team-category.php' || basename($_SERVER['PHP_SELF']) == 'edit-team-category.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-team-category.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Team Category</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-team-designation.php' || basename($_SERVER['PHP_SELF']) == 'add-team-designation.php' || basename($_SERVER['PHP_SELF']) == 'edit-team-designation.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-team-designation.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Team Designation</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-team.php' || basename($_SERVER['PHP_SELF']) == 'add-team.php' || basename($_SERVER['PHP_SELF']) == 'edit-team.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-team.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Team</span>
								</a>
							</li>
						</ul>
					</div>
				</li>


				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-blog-content.php' || basename($_SERVER['PHP_SELF']) == 'manage-blog.php' || basename($_SERVER['PHP_SELF']) == 'add-blog.php' || basename($_SERVER['PHP_SELF']) == 'edit-blog.php'  ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-journal-bookmark fs-2"></i>
						</span>
						<span class="menu-text">Blog</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-blog-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-blog-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-blog.php' || basename($_SERVER['PHP_SELF']) == 'add-blog.php'  || basename($_SERVER['PHP_SELF']) == 'edit-blog.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-blog.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage</span>
								</a>
							</li>
						</ul>
					</div>
				</li>


				<?php if ($_SESSION['login_type'] == 1): ?>
					<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'add-user.php' || basename($_SERVER['PHP_SELF']) == 'manage-user.php' || basename($_SERVER['PHP_SELF']) == 'edit-user.php' ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
						<a href="javascript:;" class="menu-link menu-toggle">
							<span class="svg-icon menu-icon">
								<i class="bi bi-people fs-2"></i>
							</span>
							<span class="menu-text">User</span>
							<i class="menu-arrow"></i>
						</a>
						<div class="menu-submenu">
							<i class="menu-arrow"></i>
							<ul class="menu-subnav">
								<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'add-user.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
									<a href="<?= BASE_URL ?>add-user.php" class="menu-link">
										<i class="menu-bullet menu-bullet-line">
											<span></span>
										</i>
										<span class="menu-text">Add</span>
									</a>
								</li>
								<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-user.php' || basename($_SERVER['PHP_SELF']) == 'edit-user.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
									<a href="<?= BASE_URL ?>manage-user.php" class="menu-link">
										<i class="menu-bullet menu-bullet-line">
											<span></span>
										</i>
										<span class="menu-text">Manage</span>
									</a>
								</li>
							</ul>
						</div>
					</li>
				<?php endif; ?>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'add-about-slider.php' || basename($_SERVER['PHP_SELF']) == 'edit-about-slider.php' || basename($_SERVER['PHP_SELF']) == 'manage-about-content.php' || basename($_SERVER['PHP_SELF']) == 'add-about-content.php' || basename($_SERVER['PHP_SELF']) == 'edit-about-content.php' || basename($_SERVER['PHP_SELF']) == 'manage-about-service.php' || basename($_SERVER['PHP_SELF']) == 'add-about-service.php' || basename($_SERVER['PHP_SELF']) == 'edit-about-service.php' || basename($_SERVER['PHP_SELF']) == 'manage-about-counter.php' || basename($_SERVER['PHP_SELF']) == 'add-about-counter.php' || basename($_SERVER['PHP_SELF']) == 'edit-about-counter.php' || basename($_SERVER['PHP_SELF']) == 'edit-about-service-image.php' || basename($_SERVER['PHP_SELF']) == 'edit-about-details.php' || basename($_SERVER['PHP_SELF']) == 'edit-mission-vision.php'  ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-building fs-2"></i>
						</span>
						<span class="menu-text">About Us</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-about-content.php' || basename($_SERVER['PHP_SELF']) == 'add-about-content.php' || basename($_SERVER['PHP_SELF']) == 'edit-about-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-about-content.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-about-counter.php' || basename($_SERVER['PHP_SELF']) == 'add-about-counter.php' || basename($_SERVER['PHP_SELF']) == 'edit-about-counter.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-about-counter.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Counter</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-about-service.php' || basename($_SERVER['PHP_SELF']) == 'add-about-service.php' || basename($_SERVER['PHP_SELF']) == 'edit-about-service.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-about-service.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Service</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-media-content.php' || basename($_SERVER['PHP_SELF']) == 'manage-media.php' || basename($_SERVER['PHP_SELF']) == 'add-media.php' || basename($_SERVER['PHP_SELF']) == 'edit-media.php'  ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-images fs-2"></i>
						</span>
						<span class="menu-text">Media</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-media-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-media-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-media.php' || basename($_SERVER['PHP_SELF']) == 'add-media.php'  || basename($_SERVER['PHP_SELF']) == 'edit-media.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-media.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-company-news-content.php' || basename($_SERVER['PHP_SELF']) == 'manage-company-news.php' || basename($_SERVER['PHP_SELF']) == 'add-company-news.php' || basename($_SERVER['PHP_SELF']) == 'edit-company-news.php'  ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-newspaper fs-2"></i>
						</span>
						<span class="menu-text">Company News</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-company-news-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-company-news-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Content Company News</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-company-news.php' || basename($_SERVER['PHP_SELF']) == 'add-company-news.php'  || basename($_SERVER['PHP_SELF']) == 'edit-company-news.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-company-news.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage Company News</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-testimonial-content.php' || basename($_SERVER['PHP_SELF']) == 'manage-testimonial.php' || basename($_SERVER['PHP_SELF']) == 'add-testimonial.php' || basename($_SERVER['PHP_SELF']) == 'edit-testimonial.php'  ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-chat-quote fs-2"></i>
						</span>
						<span class="menu-text">Testimonial</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-testimonial-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-testimonial-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-testimonial.php' || basename($_SERVER['PHP_SELF']) == 'add-testimonial.php'  || basename($_SERVER['PHP_SELF']) == 'edit-testimonial.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-testimonial.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-awards-content.php' || basename($_SERVER['PHP_SELF']) == 'manage-awards.php' || basename($_SERVER['PHP_SELF']) == 'add-awards.php' || basename($_SERVER['PHP_SELF']) == 'edit-awards.php'  ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-trophy fs-2"></i>
						</span>
						<span class="menu-text">Awards</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-awards-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-awards-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-awards.php' || basename($_SERVER['PHP_SELF']) == 'add-awards.php'  || basename($_SERVER['PHP_SELF']) == 'edit-awards.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-awards.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-mortgage-content.php' || basename($_SERVER['PHP_SELF']) == 'manage-advisor.php' || basename($_SERVER['PHP_SELF']) == 'add-advisor.php' || basename($_SERVER['PHP_SELF']) == 'edit-advisor.php' || basename($_SERVER['PHP_SELF']) == 'manage-taxes.php' || basename($_SERVER['PHP_SELF']) == 'add-taxes.php' || basename($_SERVER['PHP_SELF']) == 'edit-taxes.php' || basename($_SERVER['PHP_SELF']) == 'manage-bank.php' || basename($_SERVER['PHP_SELF']) == 'add-bank.php' || basename($_SERVER['PHP_SELF']) == 'edit-bank.php' ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-calculator fs-2"></i>
						</span>
						<span class="menu-text">Mortgage Calculator</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-mortgage-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-mortgage-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-bank.php' || basename($_SERVER['PHP_SELF']) == 'add-bank.php'  || basename($_SERVER['PHP_SELF']) == 'edit-bank.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-bank.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage Bank</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-golden-visa-content.php' || basename($_SERVER['PHP_SELF']) == 'manage-facilities.php' || basename($_SERVER['PHP_SELF']) == 'add-facilities.php' || basename($_SERVER['PHP_SELF']) == 'edit-facilities.php' || basename($_SERVER['PHP_SELF']) == 'manage-services.php' || basename($_SERVER['PHP_SELF']) == 'add-services.php' || basename($_SERVER['PHP_SELF']) == 'edit-services.php'  ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-gem fs-2"></i>
						</span>
						<span class="menu-text">Golden Visa</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-golden-visa-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-golden-visa-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-services.php' || basename($_SERVER['PHP_SELF']) == 'add-services.php'  || basename($_SERVER['PHP_SELF']) == 'edit-services.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-services.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Services</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-career-content.php' || basename($_SERVER['PHP_SELF']) == 'manage-position.php' || basename($_SERVER['PHP_SELF']) == 'add-position.php' || basename($_SERVER['PHP_SELF']) == 'edit-position.php' || basename($_SERVER['PHP_SELF']) == 'manage-career-awards.php' || basename($_SERVER['PHP_SELF']) == 'add-career-awards.php' || basename($_SERVER['PHP_SELF']) == 'edit-career-awards.php' || basename($_SERVER['PHP_SELF']) == 'manage-resume.php' || basename($_SERVER['PHP_SELF']) == 'manage-focus-area.php' || basename($_SERVER['PHP_SELF']) == 'add-focus-area.php' || basename($_SERVER['PHP_SELF']) == 'edit-focus-area.php' || basename($_SERVER['PHP_SELF']) == 'manage-career-gallery.php' || basename($_SERVER['PHP_SELF']) == 'add-career-gallery.php' || basename($_SERVER['PHP_SELF']) == 'edit-career-gallery.php'  ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-briefcase fs-2"></i>
						</span>
						<span class="menu-text">Career</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-career-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-career-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-position.php' || basename($_SERVER['PHP_SELF']) == 'add-position.php'  || basename($_SERVER['PHP_SELF']) == 'edit-position.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-position.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Position</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-career-awards.php' || basename($_SERVER['PHP_SELF']) == 'add-career-awards.php'  || basename($_SERVER['PHP_SELF']) == 'edit-career-awards.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-career-awards.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Awards</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-focus-area.php' || basename($_SERVER['PHP_SELF']) == 'add-focus-area.php'  || basename($_SERVER['PHP_SELF']) == 'edit-focus-area.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-focus-area.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Focus Areas</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-career-gallery.php' || basename($_SERVER['PHP_SELF']) == 'add-career-gallery.php'  || basename($_SERVER['PHP_SELF']) == 'edit-career-gallery.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-career-gallery.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Gallery</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-resume.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-resume.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Resume</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-message-from-clo.php' || basename($_SERVER['PHP_SELF']) == 'edit-message-from-ceo.php' || basename($_SERVER['PHP_SELF']) == 'edit-message-from-md.php'   ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-envelope fs-2"></i>
						</span>
						<span class="menu-text">Messages</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-message-from-ceo.php'   ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-message-from-ceo.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Message from CEO</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-message-from-clo.php'   ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-message-from-clo.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Message from CLO</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-message-from-md.php'   ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-message-from-md.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Message from MD</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-developer-content.php' || basename($_SERVER['PHP_SELF']) == 'manage-developer.php' || basename($_SERVER['PHP_SELF']) == 'add-developer.php' || basename($_SERVER['PHP_SELF']) == 'edit-developer.php' || basename($_SERVER['PHP_SELF']) == 'manage-chooseus.php' || basename($_SERVER['PHP_SELF']) == 'edit-chooseus.php' || basename($_SERVER['PHP_SELF']) == 'edit-chooseus.php'  ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-building fs-2"></i>
						</span>
						<span class="menu-text">Developer</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-developer-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-developer-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-developer.php' || basename($_SERVER['PHP_SELF']) == 'add-developer.php'  || basename($_SERVER['PHP_SELF']) == 'edit-developer.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-developer.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-community-content.php' || basename($_SERVER['PHP_SELF']) == 'manage-community.php' || basename($_SERVER['PHP_SELF']) == 'add-community.php' || basename($_SERVER['PHP_SELF']) == 'edit-community.php' || basename($_SERVER['PHP_SELF']) == 'manage-comm-loc.php' || basename($_SERVER['PHP_SELF']) == 'add-comm-loc.php' || basename($_SERVER['PHP_SELF']) == 'edit-comm-loc.php' || basename($_SERVER['PHP_SELF']) == 'manage-comm-map.php' || basename($_SERVER['PHP_SELF']) == 'add-comm-map.php' || basename($_SERVER['PHP_SELF']) == 'edit-comm-map.php' ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-people fs-2"></i>
						</span>
						<span class="menu-text">Community</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-community-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-community-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-community.php' || basename($_SERVER['PHP_SELF']) == 'add-community.php'  || basename($_SERVER['PHP_SELF']) == 'edit-community.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-community.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'manage-homepage-links.php' || basename($_SERVER['PHP_SELF']) == 'add-homepage-links.php' || basename($_SERVER['PHP_SELF']) == 'edit-homepage-links.php'  ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-link-45deg fs-2"></i>
						</span>
						<span class="menu-text">Home Page Content</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-homepage-links.php' || basename($_SERVER['PHP_SELF']) == 'add-homepage-links.php'  || basename($_SERVER['PHP_SELF']) == 'edit-homepage-links.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-homepage-links.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Homepage Links</span>
								</a>
							</li>

							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-instagram-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-instagram-content.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Home Page Instagram</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'manage-service-section.php' || basename($_SERVER['PHP_SELF']) == 'add-service-section.php' ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-gear fs-2"></i>
						</span>
						<span class="menu-text">Services</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'add-service-section.php'   ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>add-service-section.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Add</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-service-section.php'   ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-service-section.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'manage-guide.php' || basename($_SERVER['PHP_SELF']) == 'add-guide.php' || basename($_SERVER['PHP_SELF']) == 'manage-guide-image.php' || basename($_SERVER['PHP_SELF']) == 'add-guide-image.php' || basename($_SERVER['PHP_SELF']) == 'edit-guide-image.php' ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-journal-bookmark-fill fs-2"></i>
						</span>
						<span class="menu-text">Guide</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'add-guide.php'   ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>add-guide.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Add</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-guide.php'   ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-guide.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-book-valuation-content.php' || basename($_SERVER['PHP_SELF']) == 'manage-book-valuation.php' || basename($_SERVER['PHP_SELF']) == 'add-book-valuation.php' || basename($_SERVER['PHP_SELF']) == 'edit-book-valuation.php'  ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-book fs-2"></i>
						</span>
						<span class="menu-text">Book Valuation</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-book-valuation-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-book-valuation-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Content</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-book-valuation.php' || basename($_SERVER['PHP_SELF']) == 'add-book-valuation.php'  || basename($_SERVER['PHP_SELF']) == 'edit-book-valuation.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-book-valuation.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Manage</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-brand-video-content.php'   ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-camera-reels fs-2"></i>
						</span>
						<span class="menu-text">Brand Video</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-brand-video-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-brand-video-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Content</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-podcast.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
					<a href="<?= BASE_URL ?>manage-podcast.php" class="menu-link">
						<span class="svg-icon menu-icon">
							<i class="bi bi-earbuds"></i>
						</span>
						<span class="menu-text">Podcast</span>
					</a>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'manage-units.php' || basename($_SERVER['PHP_SELF']) == 'add-units.php' || basename($_SERVER['PHP_SELF']) == 'edit-units.php' || basename($_SERVER['PHP_SELF']) == 'manage-price.php' || basename($_SERVER['PHP_SELF']) == 'add-price.php' || basename($_SERVER['PHP_SELF']) == 'edit-price.php' || basename($_SERVER['PHP_SELF']) == 'manage-construction.php' || basename($_SERVER['PHP_SELF']) == 'add-construction.php' || basename($_SERVER['PHP_SELF']) == 'edit-construction.php' || basename($_SERVER['PHP_SELF']) == 'manage-purchase.php' || basename($_SERVER['PHP_SELF']) == 'add-purchase.php' || basename($_SERVER['PHP_SELF']) == 'edit-purchase.php'  ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-question-circle fs-2"></i>
						</span>
						<span class="menu-text">Quiz</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-units.php' || basename($_SERVER['PHP_SELF']) == 'add-units.php' || basename($_SERVER['PHP_SELF']) == 'edit-units.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-units.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Units</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-price.php' || basename($_SERVER['PHP_SELF']) == 'add-price.php' || basename($_SERVER['PHP_SELF']) == 'edit-price.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-price.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Price Range</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-construction.php' || basename($_SERVER['PHP_SELF']) == 'add-construction.php' || basename($_SERVER['PHP_SELF']) == 'edit-construction.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-construction.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Construction Type</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-purchase.php' || basename($_SERVER['PHP_SELF']) == 'add-purchase.php' || basename($_SERVER['PHP_SELF']) == 'edit-purchase.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-purchase.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Purchase</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'edit-header-info.php' || basename($_SERVER['PHP_SELF']) == 'edit-footer-info.php'   ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-info-circle fs-2"></i>
						</span>
						<span class="menu-text">Info</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-header-info.php'   ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-header-info.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Header Info</span>
								</a>
							</li>
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-footer-info.php'   ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>edit-footer-info.php?edit=<?= base64_encode(1) ?>" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Footer Info</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item menu-item-submenu <?= (basename($_SERVER['PHP_SELF']) == 'manage-quiz.php' || basename($_SERVER['PHP_SELF']) == 'manage-quiz.php'   ? 'menu-item-open' : ' ') ?>" aria-haspopup="true" data-menu-toggle="hover">
					<a href="javascript:;" class="menu-link menu-toggle">
						<span class="svg-icon menu-icon">
							<i class="bi bi-envelope-paper fs-2"></i>
						</span>
						<span class="menu-text">Enquiry</span>
						<i class="menu-arrow"></i>
					</a>
					<div class="menu-submenu">
						<i class="menu-arrow"></i>
						<ul class="menu-subnav">
							<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-quiz.php'   ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
								<a href="<?= BASE_URL ?>manage-quiz.php" class="menu-link">
									<i class="menu-bullet menu-bullet-line">
										<span></span>
									</i>
									<span class="menu-text">Quiz</span>
								</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-privacy-policy-content.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
					<a href="<?= BASE_URL ?>edit-privacy-policy-content.php?edit=<?= base64_encode(1) ?>" class="menu-link">
						<span class="svg-icon menu-icon">
							<i class="bi bi-shield-lock fs-2"></i>
						</span>
						<span class="menu-text">Privacy Policy</span>
					</a>
				</li>

				<?php
				if ($_SESSION['login_type'] == 2) {
				?>
					<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'edit-google-rating.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
						<a href="<?= BASE_URL ?>edit-google-rating.php?edit=<?= base64_encode(1) ?>" class="menu-link">
							<span class="svg-icon menu-icon">
								<i class="bi bi-google fs-2"></i>
							</span>
							<span class="menu-text">Google Rating</span>
						</a>
					</li>
				<?php } ?>

				<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-tags.php' || basename($_SERVER['PHP_SELF']) == 'edit-tags.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
					<a href="<?= BASE_URL ?>manage-tags.php" class="menu-link">
						<span class="svg-icon menu-icon">
							<i class="bi bi-tags fs-2"></i>
						</span>
						<span class="menu-text">Meta Tags</span>
					</a>
				</li>

				<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-hero-section.php' || basename($_SERVER['PHP_SELF']) == 'edit-hero-section.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
					<a href="<?= BASE_URL ?>manage-hero-section.php" class="menu-link">
						<span class="svg-icon menu-icon">
							<i class="bi bi-tags fs-2"></i>
						</span>
						<span class="menu-text">Hero Section</span>
					</a>
				</li>

				<?php
				if ($_SESSION['login_type'] == 1) {
				?>
				<li class="menu-item <?= (basename($_SERVER['PHP_SELF']) == 'manage-leads.php' || basename($_SERVER['PHP_SELF']) == 'edit-leads.php'  ? 'menu-item-active' : ' ') ?>" aria-haspopup="true">
					<a href="<?= BASE_URL ?>manage-leads.php" class="menu-link">
						<span class="svg-icon menu-icon">
							<i class="bi bi-award"></i>
						</span>
						<span class="menu-text">Leads</span>
					</a>
				</li>

					<?php
					$current_page = basename($_SERVER['PHP_SELF']);
					$log_pages = ['manage-loggs.php', 'manage-login-logs.php', 'manage-delete-logs.php'];
					$is_log_open = in_array($current_page, $log_pages);
					?>

					<li class="menu-item menu-item-submenu <?= $is_log_open ? 'menu-item-open' : '' ?>" data-menu-toggle="hover">
						<a href="javascript:;" class="menu-link menu-toggle">
							<span class="svg-icon menu-icon">
								<i class="bi bi-file-text fs-2"></i>
							</span>
							<span class="menu-text">Logs</span>
							<i class="menu-arrow"></i>
						</a>
						<div class="menu-submenu">
							<ul class="menu-subnav">
								<li class="menu-item <?= ($current_page == 'manage-loggs.php' ? 'menu-item-active' : '') ?>">
									<a href="<?= BASE_URL ?>manage-loggs.php" class="menu-link">
										<i class="menu-bullet menu-bullet-line"><span></span></i>
										<span class="menu-text">Activity Logs</span>
									</a>
								</li>
								<li class="menu-item <?= ($current_page == 'manage-login-logs.php' ? 'menu-item-active' : '') ?>">
									<a href="<?= BASE_URL ?>manage-login-logs.php" class="menu-link">
										<i class="menu-bullet menu-bullet-line"><span></span></i>
										<span class="menu-text">Login Logs</span>
									</a>
								</li>
								<li class="menu-item <?= ($current_page == 'manage-delete-logs.php' ? 'menu-item-active' : '') ?>">
									<a href="<?= BASE_URL ?>manage-delete-logs.php" class="menu-link">
										<i class="menu-bullet menu-bullet-line"><span></span></i>
										<span class="menu-text">Delete Data Logs</span>
									</a>
								</li>
							</ul>
						</div>
					</li>
				<?php } ?>

			</ul>
			<!--end::Menu Nav-->
		</div>
		<!--end::Menu Container-->
	</div>
	<!--end::Aside Menu-->
</div>