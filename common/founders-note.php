<?php
/*
 * Founders' note page body, shared by founders-note-ceo.php and founders-note-clo.php.
 *
 * The page sets, before including:
 *   $note  = this founder:  ['row' => DB row, 'prefix' => 'ceo' | 'foundr', 'role' => 'Chief Executive Officer',
 *                            'url' => clean URL of this note, 'quote' => pull quote, 'news' => ['Mudasir', 'Founders']]
 *   $other = the other founder: ['row' => DB row, 'prefix' => ..., 'role' => ..., 'url' => ...]
 * Columns read (prefix ceo_ / foundr_): heading, name, desg, img, img_alt_tag, bg_img, content.
 */
$col = fn($n, $c) => $n['row'][$n['prefix'] . '_' . $c] ?? '';
$display_name = fn($n) => ucwords(strtolower(trim($col($n, 'name'))));
$first_name = fn($n) => explode(' ', $display_name($n))[0];
$photo = fn($n) => BASE_URL_IMG . 'message/' . rawurlencode($col($n, 'img'));

$name = $display_name($note);
$desg = trim($col($note, 'desg'));

// Letter: the admin content is a list of <p>. The first short line ending in "," is the salutation,
// the next paragraph opens the letter (drop cap), and the closing name / designation lines are left
// out because the signature below shows them.
preg_match_all('#<p[^>]*>(.*?)</p>#is', $col($note, 'content'), $m);
$paras = [];
foreach ($m[1] as $p) {
    $p = trim(strip_tags($p, '<b><strong><em><i><a><br>'));
    $plain = trim(html_entity_decode(strip_tags($p), ENT_QUOTES, 'UTF-8'));
    if ($plain === '' || strcasecmp($plain, trim($col($note, 'name'))) === 0 || strcasecmp($plain, html_entity_decode($desg)) === 0) {
        continue;
    }
    $paras[] = $p;
}
$salutation = '';
if ($paras && mb_strlen(strip_tags($paras[0])) < 80 && substr(rtrim(strip_tags($paras[0])), -1) === ',') {
    $salutation = array_shift($paras);
}
$lead = array_shift($paras) ?? '';
$read_min = max(1, (int) ceil(str_word_count(strip_tags($salutation . ' ' . $lead . ' ' . implode(' ', $paras))) / 200));

// Facts: live numbers
$team_count = count(select_all('tbl_team', 'is_active=1'));
$awards_count = count(select_all('tbl_awards', 'is_active=1'));

// Profile on the team pages
$name_sql = mysqli_real_escape_string($conn, $name);
$team_row = select_row('tbl_team', "is_active=1 AND team_name='$name_sql'") ?: [];

// In the news: latest company news that mention this founder
$news_where = implode(' OR ', array_map(fn($w) => "news_heading LIKE '%" . mysqli_real_escape_string($conn, $w) . "%'", $note['news'] ?? [$first_name($note)]));
$news = select_all('tbl_news', "is_active=1 AND ($news_where) ORDER BY news_posted_on DESC LIMIT 4");
?>
    <!-- ===== FOUNDERS NOTE: HERO ===== -->
    <header class="ph-hero fn-hero">
      <div class="ph-bg" style="background-image: url(<?= BASE_URL_IMG ?>message/<?= rawurlencode($col($note, 'bg_img')) ?>)"></div>
      <div class="wrap">
        <div class="crumbs" role="navigation" aria-label="Breadcrumb">
          <a href="<?= BASE_URL ?>">Home</a><span>/</span><a href="<?= BASE_URL ?>about-us">About</a><span>/</span
          ><span aria-current="page"><?= e($col($note, 'heading')) ?></span>
        </div>
        <span class="eyebrow"><?= e($col($note, 'heading')) ?></span>
        <h1 class="serif">A message from our <em class="gold-text"><?= e($note['role']) ?>.</em></h1>
        <nav class="fn-tabs glass" aria-label="Founders’ notes">
<?php
        // CLO first, then CEO, as in the design
        $tabs = $note['prefix'] === 'foundr' ? [$note, $other] : [$other, $note];
        foreach ($tabs as $tab) {
            $on = $tab === $note; ?>
          <a class="fn-tab<?= $on ? ' on' : '' ?>" href="<?= e($tab['url']) ?>"<?= $on ? ' aria-current="page"' : '' ?>
            ><img src="<?= e($photo($tab)) ?>" alt="" /><span><b><?= e($display_name($tab)) ?></b><small><?= e($col($tab, 'desg')) ?></small></span></a
          >
<?php } ?>
        </nav>
      </div>
    </header>

    <!-- ===== LETTER ===== -->
    <section style="padding-top: 50px">
      <div class="wrap fn-grid">
        <aside class="fn-side">
          <div class="fn-ph glass">
            <img src="<?= e($photo($note)) ?>" alt="<?= e($col($note, 'img_alt_tag') ?: $name) ?>" /><span class="fn-phc glass on-img"
              ><b class="serif"><?= e($name) ?></b><small><?= e($desg) ?></small></span
            >
          </div>
          <dl class="fn-facts glass">
            <div>
              <dt>Founded</dt>
              <dd>2007</dd>
            </div>
            <div>
              <dt>Head office</dt>
              <dd>Dubai Hills Estate</dd>
            </div>
            <div>
              <dt>Team</dt>
              <dd><?= $team_count ?> people</dd>
            </div>
            <div>
              <dt>Awards</dt>
              <dd><?= $awards_count ?></dd>
            </div>
          </dl>
<?php if (!empty($team_row['team_slug'])) { ?>
          <a class="btn btn-glass fn-pl" href="<?= BASE_URL ?>our-team/<?= e($team_row['team_slug']) ?>">View <?= e($first_name($note)) ?>’s profile →</a>
<?php } ?>
        </aside>
        <article class="fn-letter glass reveal">
          <div class="fn-lh">
            <span class="fn-mark"
              ><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M3 17c3-4 5-9 7-9s-1 8 2 8 3-5 5-5 1 4 4 4"></path>
                <path d="M3 21h18"></path></svg></span
            ><span><small>A letter from</small><b><?= e($name) ?></b></span
            ><span class="fn-rt"><?= $read_min ?> min read</span>
          </div>
<?php if ($salutation !== '') { ?>
          <p class="fn-sal serif"><?= $salutation ?></p>
<?php } ?>
          <p class="fn-lead"><?= $lead ?></p>
<?php if (!empty($note['quote'])) { ?>
          <blockquote class="fn-pq">
            <span
              ><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path
                  d="M9.5 6C6 7.3 4 10 4 13.6V18h6v-6H7.3c.2-2 1.3-3.4 3.2-4.3zM19.5 6C16 7.3 14 10 14 13.6V18h6v-6h-2.7c.2-2 1.3-3.4 3.2-4.3z"
                ></path></svg
            ></span>
            <p><?= e($note['quote']) ?></p>
          </blockquote>
<?php } ?>
<?php foreach ($paras as $p) { ?>
          <p><?= $p ?></p>
<?php } ?>

          <div class="fn-sign">
            <b class="serif"><?= e($name) ?></b
            ><small><?= e(str_replace(['CEO', 'CLO'], $note['role'], $desg)) ?>, DRE Homes Real Estate</small>
          </div>
        </article>
      </div>
    </section>

    <!-- ===== OTHER NOTE ===== -->
    <section style="padding-top: 40px">
      <div class="wrap">
        <a class="fn-next glass acc-edge reveal" href="<?= e($other['url']) ?>"
          ><img src="<?= e($photo($other)) ?>" alt="<?= e($display_name($other)) ?>" loading="lazy" /><span
            ><span class="eyebrow">Also read</span><b class="serif">A message from our <?= e($other['role']) ?></b
            ><small><?= e($display_name($other)) ?> · <?= e($col($other, 'desg')) ?></small></span
          ><i aria-hidden="true">→</i></a
        >
      </div>
    </section>

<?php if ($news) { ?>
    <!-- ===== IN THE NEWS ===== -->
    <section style="padding-top: 40px">
      <div class="wrap">
        <div class="head-row reveal">
          <div>
            <span class="eyebrow">In the news</span>
            <h2 class="serif">Recognition <em class="gold-text">&amp; press.</em></h2>
          </div>
          <a class="btn btn-glass" href="<?= page_link('company-news') ?>">All company news →</a>
        </div>
        <ol class="mb-news reveal">
<?php foreach ($news as $n) { ?>
          <li>
            <a class="pf-ev glass" href="<?= BASE_URL ?>company-news/<?= e($n['news_slug']) ?>"
              ><span class="pf-evi"
                ><img src="<?= BASE_URL_IMG ?>company-news/thumb/<?= rawurlencode($n['news_thumb_img']) ?>" alt="<?= e($n['news_thumb_img_alt']) ?>" loading="lazy" /></span
              ><span class="pf-evb"
                ><small><?= date('M d, Y', strtotime($n['news_posted_on'])) ?></small
                ><b><?= e($n['news_heading']) ?></b></span
              ><i aria-hidden="true">→</i></a
            >
          </li>
<?php } ?>
        </ol>
      </div>
    </section>
<?php } ?>
