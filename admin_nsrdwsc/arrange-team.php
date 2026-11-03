<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

// Nothing below runs unless someone is signed in.
if (empty($_SESSION['email_id'])) {
	exit;
}

$logger = new Logger($conn);

if (empty($_SESSION['admin_csrf'])) {
	$_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
}

function set_e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write).
function set_query($conn, $sql, array $params = [])
{
	try {
		$stmt = mysqli_prepare($conn, $sql);
		if (!$stmt) {
			error_log('Settings query failed: ' . mysqli_error($conn));
			return false;
		}
		if ($params) {
			$types = '';
			foreach ($params as $param) {
				$types .= is_int($param) ? 'i' : 's';
			}
			mysqli_stmt_bind_param($stmt, $types, ...$params);
		}
		if (!mysqli_stmt_execute($stmt)) {
			error_log('Settings query failed: ' . mysqli_stmt_error($stmt));
			mysqli_stmt_close($stmt);
			return false;
		}
		$result = mysqli_stmt_get_result($stmt);
		mysqli_stmt_close($stmt);
	} catch (mysqli_sql_exception $error) {
		// This server reports database errors as exceptions.
		error_log('Settings query failed: ' . $error->getMessage());
		return false;
	}
	if ($result === false) {
		return true;
	}
	$rows = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}

$self = BASE_URL . 'arrange-team.php';

// ---------------------------------------------------------------
// Save the order. The browser sends every group with its people in their new order.
// ---------------------------------------------------------------
$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_order'])) {
	$sent = json_decode(is_string($_POST['order'] ?? null) ? $_POST['order'] : '', true);
	if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['admin_csrf'], $_POST['csrf'])) {
		$error = 'The page expired before saving. Nothing was changed; please arrange and save again.';
	} elseif (!is_array($sent)) {
		$error = 'The new order could not be read. Nothing was changed.';
	} else {
		$members = [];      // id => [group, position] as saved now
		foreach (set_query($conn, 'SELECT team_id, team_desg_id, order_no FROM tbl_team') ?: [] as $row) {
			$members[(int) $row['team_id']] = [(int) $row['team_desg_id'], $row['order_no'] === null ? null : (int) $row['order_no']];
		}
		$groups = [];       // id => position as saved now
		foreach (set_query($conn, 'SELECT team_desg_id, order_no FROM tbl_team_desg') ?: [] as $row) {
			$groups[(int) $row['team_desg_id']] = $row['order_no'] === null ? null : (int) $row['order_no'];
		}
		$move_groups = !empty($_POST['groups_moved']);
		$changed = 0;
		$failed = false;
		mysqli_begin_transaction($conn);
		$group_position = 0;
		foreach ($sent as $group) {
			$group_id = (int) ($group['id'] ?? 0);
			if (!isset($groups[$group_id]) || !is_array($group['members'] ?? null)) {
				continue;
			}
			$group_position++;
			if ($move_groups && $groups[$group_id] !== $group_position) {
				$failed = $failed || !set_query($conn, 'UPDATE tbl_team_desg SET order_no = ? WHERE team_desg_id = ?', [$group_position, $group_id]);
				$changed++;
			}
			$position = 0;
			foreach ($group['members'] as $member_id) {
				$member_id = (int) $member_id;
				// Only people who really are in this group are numbered.
				if (!isset($members[$member_id]) || $members[$member_id][0] !== $group_id) {
					continue;
				}
				$position++;
				if ($members[$member_id][1] !== $position) {
					$failed = $failed || !set_query($conn, 'UPDATE tbl_team SET order_no = ? WHERE team_id = ?', [$position, $member_id]);
					$changed++;
				}
				unset($members[$member_id]);
			}
		}
		if ($failed) {
			mysqli_rollback($conn);
			$error = 'The order could not be saved. Nothing was changed; please try again.';
		} else {
			mysqli_commit($conn);
			if ($changed > 0) {
				$logger->log('UPDATE', 'tbl_team', null, null, null, 'Team order arranged: ' . $changed . ' position' . ($changed === 1 ? '' : 's') . ' changed');
			}
			header('Location: ' . $self . '?done=' . $changed);
			exit;
		}
	}
}

// ---------------------------------------------------------------
// The groups and their people, in the order the Our Team page shows them
// ---------------------------------------------------------------
$groups = [];
foreach (set_query($conn, 'SELECT team_desg_id, team_desg_name, order_no, is_active FROM tbl_team_desg ORDER BY order_no IS NULL, order_no, team_desg_name') ?: [] as $row) {
	$groups[(int) $row['team_desg_id']] = $row + ['members' => []];
}
$orphans = [];      // people whose designation group no longer exists
$total = 0;
$hidden = 0;
foreach (set_query($conn, 'SELECT team_id, team_desg_id, team_name, team_desg, team_img, order_no, is_active, is_time_square_team FROM tbl_team ORDER BY order_no IS NULL, order_no, team_name') ?: [] as $row) {
	$total++;
	$hidden += $row['is_active'] === '1' ? 0 : 1;
	if (isset($groups[(int) $row['team_desg_id']])) {
		$groups[(int) $row['team_desg_id']]['members'][] = $row;
	} else {
		$orphans[] = $row;
	}
}
$done = isset($_GET['done']) && ctype_digit((string) $_GET['done']) ? (int) $_GET['done'] : -1;
?>
<!--begin::Body-->

<body id="kt_body"
	class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
	<!--begin::Main-->
	<!--begin::Header Mobile-->
	<div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
		<!--begin::Logo-->
		<a href="index.html">
			<img alt="Logo" src="<?= BASE_URL ?>assets/media/logos/logo-light.png" />
		</a>
		<!--end::Logo-->
		<!--begin::Toolbar-->
		<div class="d-flex align-items-center">
			<!--begin::Aside Mobile Toggle-->
			<button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
				<span></span>
			</button>
			<!--end::Aside Mobile Toggle-->
			<!--begin::Header Menu Mobile Toggle-->
			<button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle">
				<span></span>
			</button>
			<!--end::Header Menu Mobile Toggle-->
			<!--begin::Topbar Mobile Toggle-->
			<button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle">
				<span class="svg-icon svg-icon-xl">
					<!--begin::Svg Icon | path:<?= BASE_URL ?>assets/media/svg/icons/General/User.svg-->
					<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
						height="24px" viewBox="0 0 24 24" version="1.1">
						<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
							<polygon points="0 0 24 0 24 24 0 24" />
							<path
								d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
								fill="#000000" fill-rule="nonzero" opacity="0.3" />
							<path
								d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
								fill="#000000" fill-rule="nonzero" />
						</g>
					</svg>
					<!--end::Svg Icon-->
				</span>
			</button>
			<!--end::Topbar Mobile Toggle-->
		</div>
		<!--end::Toolbar-->
	</div>
	<!--end::Header Mobile-->
	<div class="d-flex flex-column flex-root">
		<!--begin::Page-->
		<div class="d-flex flex-row flex-column-fluid page">
			<!--begin::Aside-->
			<?php include('includes/sidebar.php'); ?>
			<!--end::Aside-->
			<!--begin::Wrapper-->
			<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
				<!--begin::Header-->
				<div id="kt_header" class="header header-fixed">
					<!--begin::Container-->
					<div class="container-fluid d-flex align-items-stretch justify-content-between">
						<!--begin::Header Menu Wrapper-->
						<div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
							<!--begin::Header Menu-->
							<div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">

							</div>
							<!--end::Header Menu-->
						</div>
						<!--end::Header Menu Wrapper-->
						<!--begin::Topbar-->
						<div class="topbar">

							<!--begin::User-->
							<div class="topbar-item">
								<div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2"
									id="kt_quick_user_toggle">
									<span
										class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
									<span
										class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?= $_SESSION['username'] ?></span>
									<span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
										<span
											class="symbol-label font-size-h5 font-weight-bold"><?= substr($_SESSION['username'], 0, 1) ?></span>
									</span>
								</div>
							</div>
							<!--end::User-->
						</div>
						<!--end::Topbar-->
					</div>
					<!--end::Container-->
				</div>
				<!--end::Header-->


				<!--begin::Content-->
				<div class="content d-flex flex-column flex-column-fluid dre-dash-content" id="kt_content">
					<!--begin::Entry-->
					<div class="d-flex flex-column-fluid">
						<!--begin::Container-->
						<div class="container">
							<!--begin::Arrange team-->
							<link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-projects.css?v=1" rel="stylesheet" type="text/css" />
							<link href="<?= BASE_URL ?>assets/css/dre-webpages.css?v=3" rel="stylesheet" type="text/css" />

							<div class="dre-dash dre-leads dre-projects dre-arrange">

								<div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
									<div>
										<h1>Arrange the team</h1>
										<p>Drag people into the order they should appear in on the Our Team page, then save. Each designation group has its own order.</p>
									</div>
									<div class="dre-leads-stats">
										<div><strong><?= number_format($total) ?></strong><span>People</span></div>
										<div><strong><?= count($groups) ?></strong><span>Groups</span></div>
										<div><strong><?= number_format($hidden) ?></strong><span>Hidden</span></div>
									</div>
									<div class="dre-actions">
										<a href="<?= BASE_URL ?>manage-team.php"><i class="bi bi-arrow-left"></i> Back to team members</a>
										<a href="<?= BASE_URL ?>manage-team-designation.php"><i class="bi bi-award"></i> Designations</a>
										<a href="<?= set_e(BASE_URL_HOME) ?>our-team" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View the Our Team page</a>
									</div>
								</div>

								<?php if ($error !== ''): ?>
									<div class="dre-leads-notice dre-arrange-bad" role="alert"><i class="bi bi-exclamation-triangle"></i> <?= set_e($error) ?></div>
								<?php elseif ($done >= 0): ?>
									<div class="dre-leads-notice" role="status"><i class="bi bi-check-circle"></i> <?= $done === 0 ? 'The order was already like that; nothing needed changing.' : 'The order was saved: ' . number_format($done) . ' position' . ($done === 1 ? '' : 's') . ' changed.' ?></div>
								<?php endif; ?>

								<!-- Find someone, and choose what to see -->
								<div class="dre-glass dre-arrange-tools">
									<label class="dre-leads-search">
										<i class="bi bi-search"></i>
										<input type="search" id="arrSearch" placeholder="Find a person by name" autocomplete="off" aria-label="Find a person by name">
									</label>
									<label class="dre-proj-check"><input type="checkbox" id="arrShowHidden" checked> Show hidden people</label>
									<label class="dre-proj-check"><input type="checkbox" id="arrCompact"> Small cards</label>
									<span class="dre-arrange-hint"><i class="bi bi-arrows-move"></i> Drag a card, or use its arrows. Typing a number in the badge moves the card to that place.</span>
								</div>

								<?php $group_index = 0; foreach ($groups as $group_id => $group): $group_index++; ?>
									<section class="dre-glass dre-arrange-group<?= $group['is_active'] === '1' ? '' : ' is-off' ?>" data-group="<?= (int) $group_id ?>">
										<div class="dre-h">
											<h3>
												<?= set_e($group['team_desg_name']) ?>
												<small><?= count($group['members']) ?> <?= count($group['members']) === 1 ? 'person' : 'people' ?><?= $group['is_active'] === '1' ? '' : ' · this group is hidden on the website' ?></small>
											</h3>
											<div class="dre-arrange-group-buttons">
												<button type="button" class="dre-btn" data-sort title="Put this group in A to Z order"><i class="bi bi-sort-alpha-down"></i> A–Z</button>
												<button type="button" class="dre-icon-btn" data-group-move="-1" title="Move this group up" aria-label="Move the group <?= set_e($group['team_desg_name']) ?> up"><i class="bi bi-arrow-up"></i></button>
												<button type="button" class="dre-icon-btn" data-group-move="1" title="Move this group down" aria-label="Move the group <?= set_e($group['team_desg_name']) ?> down"><i class="bi bi-arrow-down"></i></button>
											</div>
										</div>
										<?php if (!$group['members']): ?>
											<p class="dre-arrange-empty">Nobody is in this group.</p>
										<?php endif; ?>
										<div class="dre-arrange-cards">
											<?php foreach ($group['members'] as $member):
												$photo = trim((string) $member['team_img']);
											?>
												<div class="dre-arrange-card<?= $member['is_active'] === '1' ? '' : ' is-hidden' ?>" draggable="true" data-id="<?= (int) $member['team_id'] ?>" data-name="<?= set_e(mb_strtolower((string) $member['team_name'])) ?>">
													<input type="number" class="dre-arrange-no" min="1" value="" aria-label="Place of <?= set_e($member['team_name']) ?> in the group">
													<?php if ($photo !== ''): ?>
														<img src="<?= set_e(BASE_URL . 'assets/media/team/' . rawurlencode($photo)) ?>" alt="" loading="lazy" draggable="false">
													<?php else: ?>
														<span class="dre-arrange-nophoto"><i class="bi bi-person"></i></span>
													<?php endif; ?>
													<div class="dre-arrange-text">
														<strong><?= set_e($member['team_name']) ?></strong>
														<span><?= set_e($member['team_desg']) ?></span>
														<?php if ($member['is_active'] !== '1'): ?><em>Hidden</em><?php endif; ?>
													</div>
													<div class="dre-arrange-buttons">
														<button type="button" data-move="first" title="Move to the first place" aria-label="Move <?= set_e($member['team_name']) ?> to the first place"><i class="bi bi-chevron-double-left"></i></button>
														<button type="button" data-move="-1" title="One place earlier" aria-label="Move <?= set_e($member['team_name']) ?> one place earlier"><i class="bi bi-chevron-left"></i></button>
														<button type="button" data-move="1" title="One place later" aria-label="Move <?= set_e($member['team_name']) ?> one place later"><i class="bi bi-chevron-right"></i></button>
														<a href="<?= BASE_URL ?>edit-team.php?edit=<?= base64_encode((string) $member['team_id']) ?>" title="Edit" aria-label="Edit <?= set_e($member['team_name']) ?>"><i class="bi bi-pencil"></i></a>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									</section>
								<?php endforeach; ?>

								<?php if ($orphans): ?>
									<section class="dre-glass dre-arrange-group is-off">
										<div class="dre-h">
											<h3>No designation group <small><?= count($orphans) ?> <?= count($orphans) === 1 ? 'person' : 'people' ?> · their group was deleted, so they cannot be arranged. Open one and choose a group.</small></h3>
										</div>
										<div class="dre-arrange-orphans">
											<?php foreach ($orphans as $member): ?>
												<a href="<?= BASE_URL ?>edit-team.php?edit=<?= base64_encode((string) $member['team_id']) ?>"><?= set_e($member['team_name']) ?><?= $member['is_active'] === '1' ? '' : ' (hidden)' ?></a>
											<?php endforeach; ?>
										</div>
									</section>
								<?php endif; ?>

								<!-- Saving -->
								<form method="post" action="<?= set_e($self) ?>" class="dre-arrange-bar" id="arrForm">
									<input type="hidden" name="csrf" value="<?= set_e($_SESSION['admin_csrf']) ?>">
									<input type="hidden" name="save_order" value="1">
									<input type="hidden" name="order" id="arrOrder" value="">
									<input type="hidden" name="groups_moved" id="arrGroupsMoved" value="">
									<span id="arrState">Nothing moved yet</span>
									<div>
										<button type="button" class="dre-btn" id="arrReset" hidden>Undo all moves</button>
										<button type="submit" class="dre-btn dre-btn-main" id="arrSave"><i class="bi bi-check2"></i> Save the order</button>
									</div>
								</form>
							</div>

							<script>
								// Arranging the team: drag and drop inside a group, arrow buttons, typed place numbers, then one save.
								(function () {
									var root = document.querySelector('.dre-arrange');
									var state = document.getElementById('arrState');
									var reset = document.getElementById('arrReset');
									var groupsMoved = document.getElementById('arrGroupsMoved');
									var dirty = false;

									function snapshot() {
										return Array.prototype.map.call(root.querySelectorAll('.dre-arrange-group[data-group]'), function (group) {
											return {
												id: Number(group.getAttribute('data-group')),
												members: Array.prototype.map.call(group.querySelectorAll('.dre-arrange-card'), function (card) { return Number(card.getAttribute('data-id')); })
											};
										});
									}
									var saved = JSON.stringify(snapshot());

									// Writes the place numbers on the cards and says whether anything differs from what is saved.
									function renumber() {
										root.querySelectorAll('.dre-arrange-cards').forEach(function (list) {
											Array.prototype.forEach.call(list.children, function (card, index) {
												card.querySelector('.dre-arrange-no').value = index + 1;
											});
										});
										dirty = JSON.stringify(snapshot()) !== saved;
										state.textContent = dirty ? 'The order was changed and is not saved yet' : 'Nothing moved yet';
										state.classList.toggle('is-dirty', dirty);
										reset.hidden = !dirty;
									}

									function place(card, index) {
										var list = card.parentNode;
										var others = Array.prototype.filter.call(list.children, function (item) { return item !== card; });
										index = Math.max(0, Math.min(others.length, index));
										list.insertBefore(card, others[index] || null);
										renumber();
										card.classList.add('is-moved');
										window.setTimeout(function () { card.classList.remove('is-moved'); }, 900);
									}

									// Arrow buttons and the typed number
									root.addEventListener('click', function (event) {
										var button = event.target.closest('[data-move]');
										if (button) {
											var card = button.closest('.dre-arrange-card');
											var now = Array.prototype.indexOf.call(card.parentNode.children, card);
											place(card, button.getAttribute('data-move') === 'first' ? 0 : now + Number(button.getAttribute('data-move')));
											button.focus();
											return;
										}
										var sort = event.target.closest('[data-sort]');
										if (sort) {
											var list = sort.closest('.dre-arrange-group').querySelector('.dre-arrange-cards');
											Array.prototype.slice.call(list.children).sort(function (a, b) {
												return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
											}).forEach(function (card) { list.appendChild(card); });
											renumber();
											return;
										}
										var groupButton = event.target.closest('[data-group-move]');
										if (groupButton) {
											var group = groupButton.closest('.dre-arrange-group');
											var direction = Number(groupButton.getAttribute('data-group-move'));
											var other = direction < 0 ? group.previousElementSibling : group.nextElementSibling;
											if (other && other.matches('.dre-arrange-group[data-group]')) {
												group.parentNode.insertBefore(group, direction < 0 ? other : other.nextSibling);
												groupsMoved.value = '1';
												renumber();
												group.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
												groupButton.focus();
											}
										}
									});
									root.addEventListener('change', function (event) {
										if (event.target.classList.contains('dre-arrange-no')) {
											var number = parseInt(event.target.value, 10);
											if (number >= 1) {
												place(event.target.closest('.dre-arrange-card'), number - 1);
											} else {
												renumber();
											}
										}
									});

									// Drag and drop, inside the card's own group only
									var dragged = null;
									root.addEventListener('dragstart', function (event) {
										dragged = event.target.closest('.dre-arrange-card');
										if (!dragged) { return; }
										event.dataTransfer.effectAllowed = 'move';
										event.dataTransfer.setData('text/plain', dragged.getAttribute('data-id'));
										window.setTimeout(function () { if (dragged) { dragged.classList.add('is-dragging'); } }, 0);
									});
									root.addEventListener('dragover', function (event) {
										if (!dragged) { return; }
										var list = event.target.closest('.dre-arrange-cards');
										if (list !== dragged.parentNode) { return; }
										event.preventDefault();
										var over = event.target.closest('.dre-arrange-card');
										if (!over || over === dragged) { return; }
										var box = over.getBoundingClientRect();
										var sameRow = Math.abs(box.top - dragged.getBoundingClientRect().top) < box.height / 2;
										var after = sameRow ? event.clientX > box.left + box.width / 2 : event.clientY > box.top + box.height / 2;
										list.insertBefore(dragged, after ? over.nextSibling : over);
									});
									root.addEventListener('dragend', function () {
										if (dragged) { dragged.classList.remove('is-dragging'); }
										dragged = null;
										renumber();
									});
									root.addEventListener('drop', function (event) { event.preventDefault(); });

									// Finding and filtering
									var search = document.getElementById('arrSearch');
									search.addEventListener('input', function () {
										var term = search.value.trim().toLowerCase();
										var first = null;
										root.querySelectorAll('.dre-arrange-card').forEach(function (card) {
											var hit = term !== '' && card.getAttribute('data-name').indexOf(term) !== -1;
											card.classList.toggle('is-found', hit);
											if (hit && !first && card.offsetParent) { first = card; }
										});
										root.classList.toggle('is-searching', term !== '');
										if (first) { first.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
									});
									document.getElementById('arrShowHidden').addEventListener('change', function (event) {
										root.classList.toggle('hide-hidden', !event.target.checked);
									});
									var compact = document.getElementById('arrCompact');
									compact.addEventListener('change', function () {
										root.classList.toggle('is-compact', compact.checked);
										try { window.localStorage.setItem('dreArrangeCompact', compact.checked ? '1' : ''); } catch (error) {}
									});
									try {
										if (window.localStorage.getItem('dreArrangeCompact') === '1') { compact.checked = true; root.classList.add('is-compact'); }
									} catch (error) {}

									// Saving and leaving
									reset.addEventListener('click', function () {
										if (window.confirm('Put everyone back where they were when the page opened?')) {
											dirty = false;
											window.location.reload();
										}
									});
									document.getElementById('arrForm').addEventListener('submit', function () {
										document.getElementById('arrOrder').value = JSON.stringify(snapshot());
										dirty = false;
									});
									window.addEventListener('beforeunload', function (event) {
										if (dirty) { event.preventDefault(); event.returnValue = ''; }
									});
									if (window.history.replaceState && /[?&]done=/.test(window.location.search)) {
										window.history.replaceState(null, '', window.location.pathname);
									}
									renumber();
								})();
							</script>
							<!--end::Arrange team-->


						</div>
						<!--end::Container-->
					</div>
					<!--end::Entry-->
				</div>
				<!--end::Content-->

				<?php
				include('includes/footer-inner.php');
				?>


<?php
// Don't add extra closing tags
?>