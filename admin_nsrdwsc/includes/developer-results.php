<?php
/*
 * Shared by manage-developer.php and ajax/manage-developer-search.php.
 * Renders the developer table + pagination. Expects includes/developer-list.php
 * to already be loaded ($developers, $total, $offset, $filters_on, $pages, $f_page).
 * The outer div (id="devResults") is the fragment the live search swaps in whole.
 */
?>
<div class="dre-glass dre-leads-list" id="devResults">
	<div class="dre-h">
		<h3>
			<?= number_format($total) ?> <?= $total === 1 ? 'developer' : 'developers' ?>
			<small>
				<?php if ($total > 0): ?>
					Showing <?= number_format($offset + 1) ?>&ndash;<?= number_format($offset + count($developers)) ?><?= $filters_on ? ' &middot; filtered' : '' ?>
				<?php else: ?>
					Nothing matches<?= $filters_on ? ' these filters' : '' ?>
				<?php endif; ?>
			</small>
		</h3>
		<?php if ($developers): ?>
			<label class="dre-proj-all"><input type="checkbox" id="devSelectAll"> Select all on this page</label>
		<?php endif; ?>
	</div>

	<?php if (!$developers): ?>
		<div class="dre-empty">
			<i class="bi bi-building" style="font-size: 2rem;"></i><br>
			No developers to show.
			<?php if ($filters_on): ?>
				<a href="<?= BASE_URL ?>manage-developer.php">Show all developers</a>
			<?php endif; ?>
		</div>
	<?php else: ?>
		<div class="dre-proj-table" role="table" aria-label="Developers">
			<div class="dre-proj-row dre-proj-head" role="row">
				<div role="columnheader"><span class="sr-only">Select</span></div>
				<div role="columnheader">Developer</div>
				<div role="columnheader">About</div>
				<div role="columnheader">Projects</div>
				<div role="columnheader">Status</div>
				<div role="columnheader"><span class="sr-only">Actions</span></div>
			</div>
			<?php foreach ($developers as $row):
				$id = (int) $row['dev_id'];
				$is_live = $row['is_active'] === '1';
				$logo = trim((string) $row['dev_img']);
				$edit_url = BASE_URL . 'edit-developer.php?edit=' . base64_encode($id);
				$about = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $row['dev_short_descp']), ENT_QUOTES, 'UTF-8')));
				$missing = [];
				if ($logo === '') {
					$missing[] = 'logo';
				}
				if ($about === '') {
					$missing[] = 'short description';
				}
				if (trim(strip_tags((string) $row['dev_long_descp'])) === '') {
					$missing[] = 'long description';
				}
				if (trim((string) $row['meta_description']) === '') {
					$missing[] = 'meta description';
				}
				$project_count = (int) $row['project_count'];
			?>
				<div class="dre-proj-row<?= $is_live ? '' : ' is-hidden' ?>" role="row">
					<div class="dre-proj-pick" role="cell">
						<input type="checkbox" name="developer_ids[]" value="<?= $id ?>" form="devBulkForm" class="dre-proj-box" aria-label="Select <?= dev_e($row['dev_heading']) ?>">
					</div>
					<div class="dre-proj-main" role="cell">
						<a class="dre-proj-thumb dre-dev-logo" href="<?= dev_e($edit_url) ?>" tabindex="-1" aria-hidden="true">
							<?php if ($logo !== ''): ?>
								<img src="<?= BASE_URL ?>assets/media/developer/<?= dev_e(rawurlencode($logo)) ?>" alt="" loading="lazy" onerror="this.remove()">
							<?php endif; ?>
							<i class="bi bi-building"></i>
						</a>
						<div>
							<a class="dre-proj-name" href="<?= dev_e($edit_url) ?>"><?= trim((string) $row['dev_heading']) !== '' ? dev_e($row['dev_heading']) : 'Developer without a name' ?></a>
							<span class="dre-proj-sub"><i class="bi bi-link-45deg"></i> /developer/<?= dev_e($row['dev_slug']) ?></span>
							<?php if ($row['dev_showonhomepage'] === '1'): ?>
								<div class="dre-proj-pills">
									<span class="dre-pill dre-pill-ai"><i class="bi bi-house-heart"></i> Homepage<?= $row['dev_order_no'] !== null && (int) $row['dev_order_no'] > 0 ? ' &middot; position ' . (int) $row['dev_order_no'] : '' ?></span>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="dre-dev-about" role="cell">
						<span><?= $about !== '' ? dev_e(mb_strimwidth($about, 0, 110, '...')) : '<em>No short description</em>' ?></span>
						<?php if ($missing): ?>
							<span class="dre-proj-missing" title="Details that are not filled in"><i class="bi bi-exclamation-triangle"></i> Missing: <?= dev_e(implode(', ', $missing)) ?></span>
						<?php endif; ?>
					</div>
					<div class="dre-proj-facts" role="cell">
						<?php if ($project_count > 0): ?>
							<a class="dre-dev-count" href="<?= BASE_URL ?>manage-project.php?dev=<?= $id ?>"><strong><?= number_format($project_count) ?></strong> <?= $project_count === 1 ? 'project' : 'projects' ?></a>
						<?php else: ?>
							<span>No projects yet</span>
						<?php endif; ?>
					</div>
					<div class="dre-proj-status" role="cell">
						<form method="post" action="<?= dev_e(dev_url()) ?>">
							<input type="hidden" name="csrf" value="<?= dev_e($_SESSION['admin_csrf']) ?>">
							<input type="hidden" name="dev_id" value="<?= $id ?>">
							<button type="submit" name="dev_action" value="<?= $is_live ? 'hide' : 'show' ?>" class="dre-switch<?= $is_live ? ' is-on' : '' ?>" role="switch" aria-checked="<?= $is_live ? 'true' : 'false' ?>" title="<?= $is_live ? 'Live on the website. Click to hide.' : 'Hidden from the website. Click to show.' ?>">
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
						<a class="dre-icon-btn" href="<?= dev_e($edit_url) ?>" title="Edit" aria-label="Edit <?= dev_e($row['dev_heading']) ?>"><i class="bi bi-pencil"></i></a>
						<a class="dre-icon-btn" href="<?= dev_e(BASE_URL_HOME . 'developer/' . $row['dev_slug']) ?>" target="_blank" rel="noopener" title="<?= $is_live ? 'View on the website' : 'Hidden developers do not open on the website' ?>" aria-label="View <?= dev_e($row['dev_heading']) ?> on the website"><i class="bi bi-box-arrow-up-right"></i></a>
						<div class="dropdown">
							<button type="button" class="dre-icon-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More" aria-label="More actions for <?= dev_e($row['dev_heading']) ?>"><i class="bi bi-three-dots"></i></button>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="<?= BASE_URL ?>manage-chooseus.php?dev_id=<?= $id ?>"><i class="bi bi-hand-thumbs-up mr-2"></i>&ldquo;Why choose us&rdquo; points</a>
								<?php if ($project_count > 0): ?>
									<a class="dropdown-item" href="<?= BASE_URL ?>manage-project.php?dev=<?= $id ?>"><i class="bi bi-buildings mr-2"></i>Its projects</a>
								<?php endif; ?>
								<div class="dropdown-divider"></div>
								<form method="post" action="<?= dev_e(dev_url()) ?>" class="dre-dev-delete" data-name="<?= dev_e($row['dev_heading']) ?>" data-projects="<?= $project_count ?>">
									<input type="hidden" name="csrf" value="<?= dev_e($_SESSION['admin_csrf']) ?>">
									<input type="hidden" name="dev_id" value="<?= $id ?>">
									<button type="submit" name="dev_action" value="delete" class="dropdown-item text-danger"><i class="bi bi-trash3 mr-2"></i>Delete</button>
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
					<a href="<?= dev_e(dev_url(['page' => $f_page - 1])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
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
					<a href="<?= dev_e(dev_url(['page' => $number])) ?>" class="<?= $number === $f_page ? 'is-on' : '' ?>" <?= $number === $f_page ? 'aria-current="page"' : '' ?>><?= $number ?></a>
				<?php $previous = $number;
				endforeach; ?>
				<?php if ($f_page < $pages): ?>
					<a href="<?= dev_e(dev_url(['page' => $f_page + 1])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
				<?php endif; ?>
			</nav>
		<?php endif; ?>
	<?php endif; ?>
</div>
