<?php
/*
 * Shared by manage-project.php and ajax/manage-project-search.php.
 * Renders the project table + pagination. Expects includes/project-list.php
 * to already be loaded ($projects, $total, $offset, $filters_on, $pages, $f_page).
 * The outer div (id="projResults") is the fragment the live search swaps in whole.
 */
?>
<div class="dre-glass dre-leads-list" id="projResults">
	<div class="dre-h">
		<h3>
			<?= number_format($total) ?> <?= $total === 1 ? 'project' : 'projects' ?><?= $f_type ? ' &middot; ' . proj_e($proj_types[$f_type]) : '' ?>
			<small>
				<?php if ($total > 0): ?>
					Showing <?= number_format($offset + 1) ?>&ndash;<?= number_format($offset + count($projects)) ?><?= $filters_on ? ' &middot; filtered' : '' ?>
				<?php else: ?>
					Nothing matches<?= $filters_on ? ' these filters' : '' ?>
				<?php endif; ?>
			</small>
		</h3>
		<?php if ($projects): ?>
			<label class="dre-proj-all"><input type="checkbox" id="projSelectAll"> Select all on this page</label>
		<?php endif; ?>
	</div>

	<?php if (!$projects): ?>
		<div class="dre-empty">
			<i class="bi bi-buildings" style="font-size: 2rem;"></i><br>
			No projects to show.
			<?php if ($filters_on || $f_type): ?>
				<a href="<?= BASE_URL ?>manage-project.php">Show all projects</a>
			<?php endif; ?>
		</div>
	<?php else: ?>
		<div class="dre-proj-table" role="table" aria-label="Projects">
			<div class="dre-proj-row dre-proj-head" role="row">
				<div role="columnheader"><span class="sr-only">Select</span></div>
				<div role="columnheader">Project</div>
				<div role="columnheader">Developer and community</div>
				<div role="columnheader">Price and bedrooms</div>
				<div role="columnheader">Status</div>
				<div role="columnheader"><span class="sr-only">Actions</span></div>
			</div>
			<?php foreach ($projects as $row):
				$id = (int) $row['prj_id'];
				$type_id = (int) $row['prj_type_id'];
				$is_live = $row['is_active'] === '1';
				$thumb = trim(explode(',', (string) $row['prj_thumb_img'])[0]);
				$view_url = BASE_URL_HOME . ($proj_url_prefix[$type_id] ?? 'property/') . $row['proj_slug'];
				$missing = [];
				if ($row['price_number'] === null) {
					$missing[] = 'price';
				}
				if (trim((string) $row['proj_bedroom']) === '') {
					$missing[] = 'bedrooms';
				}
				if ($thumb === '') {
					$missing[] = 'image';
				}
				if (trim((string) $row['meta_description']) === '') {
					$missing[] = 'meta description';
				}
				$bedrooms = str_ireplace('STD', 'Studio', implode(', ', array_filter(array_map('trim', explode(',', (string) $row['proj_bedroom'])), 'strlen')));
			?>
				<div class="dre-proj-row<?= $is_live ? '' : ' is-hidden' ?>" role="row">
					<div class="dre-proj-pick" role="cell">
						<input type="checkbox" name="project_ids[]" value="<?= $id ?>" form="projBulkForm" class="dre-proj-box" aria-label="Select <?= proj_e($row['proj_name']) ?>">
					</div>
					<div class="dre-proj-main" role="cell">
						<a class="dre-proj-thumb" href="<?= BASE_URL ?>edit-project.php?edit=<?= base64_encode($id) ?>" tabindex="-1" aria-hidden="true">
							<?php if ($thumb !== ''): ?>
								<img src="<?= BASE_URL ?>assets/media/project/thumb/<?= proj_e(rawurlencode($thumb)) ?>" alt="" loading="lazy" onerror="this.remove()">
							<?php endif; ?>
							<i class="bi bi-building"></i>
						</a>
						<div>
							<a class="dre-proj-name" href="<?= BASE_URL ?>edit-project.php?edit=<?= base64_encode($id) ?>"><?= proj_e($row['proj_name']) ?></a>
							<?php if (trim((string) ($row['proj_name_on_card'] ?? '')) !== '' && strcasecmp(trim((string) $row['proj_name_on_card']), trim((string) $row['proj_name'])) !== 0): ?><span class="dre-proj-sub">Card name: <?= proj_e($row['proj_name_on_card']) ?></span><?php endif; ?>
							<span class="dre-proj-sub"><i class="bi bi-geo-alt"></i> <?= trim((string) $row['proj_location']) !== '' ? proj_e($row['proj_location']) : 'No location' ?></span>
							<div class="dre-proj-pills">
								<span class="dre-pill"><?= proj_e($proj_types[$type_id] ?? 'Other') ?></span>
								<?php if ($row['proj_showonhomepage'] === '1'): ?><span class="dre-pill dre-pill-ai"><i class="bi bi-house-heart"></i> Homepage</span><?php endif; ?>
							<?php if ($row['is_home_hero'] === '1'): ?><span class="dre-pill dre-pill-hero" title="Shown in the homepage hero banner"><i class="bi bi-stars"></i> Hero</span><?php endif; ?>
								<?php if ($has_seo): $seo_level = $row['seo_score'] === null ? 'none' : ((int) $row['seo_score'] > 70 ? 'good' : ((int) $row['seo_score'] > 40 ? 'ok' : 'bad')); ?><span class="dre-seo-pill is-<?= $seo_level ?>" title="SEO check of the project form<?= $row['seo_score'] === null ? ': open the project and save it once to get a score' : ' (score ' . (int) $row['seo_score'] . ' of 100)' ?>"><i></i> SEO: <?= proj_e($seo_levels[$seo_level][0]) ?></span><?php endif; ?>
								<?php if (!empty($row['genie_id'])): ?><span class="dre-pill dre-pill-genie" title="This project was imported from GenieMap"><i class="bi bi-geo-alt-fill"></i> Imported from GenieMap</span><?php endif; ?>
							</div>
						</div>
					</div>
					<div class="dre-proj-where" role="cell">
						<span><i class="bi bi-building"></i> <?= trim((string) $row['proj_dev_name']) !== '' ? proj_e($row['proj_dev_name']) : '<em>No developer</em>' ?></span>
						<span><i class="bi bi-map"></i> <?= trim((string) $row['proj_comm_name']) !== '' ? proj_e($row['proj_comm_name']) : '<em>No community</em>' ?></span>
					</div>
					<div class="dre-proj-facts" role="cell">
						<strong><?= $row['price_number'] !== null ? 'AED ' . number_format((float) $row['price_number']) : 'Price on request' ?></strong>
						<span><?= $bedrooms !== '' ? proj_e($bedrooms) . ' bed' : 'Bedrooms not set' ?></span>
						<?php if ($missing): ?>
							<span class="dre-proj-missing" title="Details that are not filled in"><i class="bi bi-exclamation-triangle"></i> Missing: <?= proj_e(implode(', ', $missing)) ?></span>
						<?php endif; ?>
					</div>
					<div class="dre-proj-status" role="cell">
						<form method="post" action="<?= proj_e(proj_url()) ?>">
							<input type="hidden" name="csrf" value="<?= proj_e($_SESSION['admin_csrf']) ?>">
							<input type="hidden" name="proj_id" value="<?= $id ?>">
							<button type="submit" name="proj_action" value="<?= $is_live ? 'hide' : 'show' ?>" class="dre-switch<?= $is_live ? ' is-on' : '' ?>" role="switch" aria-checked="<?= $is_live ? 'true' : 'false' ?>" title="<?= $is_live ? 'Live on the website. Click to hide.' : 'Hidden from the website. Click to show.' ?>">
								<i></i><span><?= $is_live ? 'Live' : 'Hidden' ?></span>
							</button>
						</form>
						<span class="dre-proj-sync">
							<?php if ((int) $row['app_sync_status'] === 1): ?>
								<i class="bi bi-phone"></i> In the app<?= $row['app_sync_date'] ? ' &middot; ' . date('d M Y', strtotime($row['app_sync_date'])) : '' ?>
							<?php else: ?>
								<i class="bi bi-phone"></i> Not in the app
							<?php endif; ?>
						</span>
					</div>
					<div class="dre-proj-actions" role="cell">
						<a class="dre-icon-btn" href="<?= BASE_URL ?>edit-project.php?edit=<?= base64_encode($id) ?>" title="Edit" aria-label="Edit <?= proj_e($row['proj_name']) ?>"><i class="bi bi-pencil"></i></a>
						<a class="dre-icon-btn" href="<?= proj_e($view_url) ?>" target="_blank" rel="noopener" title="<?= $is_live ? 'View on the website' : 'Hidden projects do not open on the website' ?>" aria-label="View <?= proj_e($row['proj_name']) ?> on the website"><i class="bi bi-box-arrow-up-right"></i></a>
						<div class="dropdown">
							<button type="button" class="dre-icon-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More" aria-label="More actions for <?= proj_e($row['proj_name']) ?>"><i class="bi bi-three-dots"></i></button>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="<?= BASE_URL ?>manage-gallery.php?proj_id=<?= $id ?>"><i class="bi bi-images mr-2"></i>Gallery</a>
								<a class="dropdown-item" href="<?= BASE_URL ?>manage-faq.php?proj_id=<?= $id ?>"><i class="bi bi-question-circle mr-2"></i>Questions and answers</a>
								<a class="dropdown-item" href="<?= BASE_URL ?>manage-payplan.php?proj_id=<?= $id ?>"><i class="bi bi-calendar-check mr-2"></i>Payment plan</a>
								<div class="dropdown-divider"></div>
								<form method="post" action="<?= proj_e(proj_url()) ?>" class="dre-proj-delete" data-name="<?= proj_e($row['proj_name']) ?>">
									<input type="hidden" name="csrf" value="<?= proj_e($_SESSION['admin_csrf']) ?>">
									<input type="hidden" name="proj_id" value="<?= $id ?>">
									<button type="submit" name="proj_action" value="delete" class="dropdown-item text-danger"><i class="bi bi-trash3 mr-2"></i>Delete</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ($pages > 1): ?>
			<nav class="dre-leads-pages" aria-label="Pages">
				<?php if ($f_page > 1): ?>
					<a href="<?= proj_e(proj_url(['page' => $f_page - 1])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
				<?php endif; ?>
				<?php
				$shown = [];
				foreach ([1, 2, $f_page - 1, $f_page, $f_page + 1, $pages - 1, $pages] as $number) {
					if ($number >= 1 && $number <= $pages) {
						$shown[$number] = true;
					}
				}
				ksort($shown);
				$previous = 0;
				foreach (array_keys($shown) as $number):
					if ($number > $previous + 1): ?><span>&hellip;</span><?php endif; ?>
					<a href="<?= proj_e(proj_url(['page' => $number])) ?>" class="<?= $number === $f_page ? 'is-on' : '' ?>" <?= $number === $f_page ? 'aria-current="page"' : '' ?>><?= $number ?></a>
				<?php $previous = $number;
				endforeach; ?>
				<?php if ($f_page < $pages): ?>
					<a href="<?= proj_e(proj_url(['page' => $f_page + 1])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
				<?php endif; ?>
			</nav>
		<?php endif; ?>
	<?php endif; ?>
</div>
