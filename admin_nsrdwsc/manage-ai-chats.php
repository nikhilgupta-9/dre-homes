<?php
include('db/config.php');
include('includes/header.php');
include('lock2.php');
include('includes/Logger.php');

// Chat transcripts are personal data: nothing below runs unless a super admin is signed in.
if (empty($_SESSION['email_id'])) {
  exit;
}
if ((int) ($_SESSION['login_type'] ?? 0) !== 1) {
  header('Location: ' . BASE_URL . 'dashboard.php');
  exit;
}

$logger = new Logger($conn);

if (empty($_SESSION['admin_csrf'])) {
  $_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
}

function chat_e($text)
{
  return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Runs a prepared query and returns its rows (or true for a write, false when it failed).
function chat_query($conn, $sql, array $params = [])
{
  try {
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
      error_log('AI chats query failed: ' . mysqli_error($conn));
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
      error_log('AI chats query failed: ' . mysqli_stmt_error($stmt));
      mysqli_stmt_close($stmt);
      return false;
    }
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
  } catch (mysqli_sql_exception $error) {
    // This server reports database errors as exceptions.
    error_log('AI chats query failed: ' . $error->getMessage());
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

// "5 min ago", "3 h ago", "2 d ago" or the date.
function chat_ago($datetime)
{
  $time = strtotime((string) $datetime);
  if (!$time) {
    return '';
  }
  $seconds = time() - $time;
  if ($seconds < 60) {
    return 'just now';
  }
  if ($seconds < 3600) {
    return floor($seconds / 60) . ' min ago';
  }
  if ($seconds < 86400) {
    return floor($seconds / 3600) . ' h ago';
  }
  if ($seconds < 86400 * 7) {
    return floor($seconds / 86400) . ' d ago';
  }
  return date('d M Y', $time);
}

// The assistant writes simple Markdown. This turns it into safe HTML: the text is escaped
// first, then only bold, links to web addresses and bullet lists are given back their meaning.
function chat_text_html($text)
{
  $text = chat_e(trim((string) $text));
  $text = preg_replace_callback('/\[([^\]\n]{1,200})\]\((https?:\/\/[^\s)]{1,500})\)/', function ($match) {
    return '<a href="' . $match[2] . '" target="_blank" rel="noopener noreferrer">' . $match[1] . '</a>';
  }, $text);
  $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
  $html = '';
  $list_open = false;
  $paragraph = [];
  $flush = function () use (&$html, &$paragraph) {
    if ($paragraph) {
      $html .= '<p>' . implode('<br>', $paragraph) . '</p>';
      $paragraph = [];
    }
  };
  foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {
    if (preg_match('/^\s*(?:[*\-]|\d{1,2}\.)\s+(.*)$/', $line, $match)) {
      $flush();
      if (!$list_open) {
        $html .= '<ul>';
        $list_open = true;
      }
      $html .= '<li>' . $match[1] . '</li>';
      continue;
    }
    if ($list_open) {
      $html .= '</ul>';
      $list_open = false;
    }
    if (trim($line) === '') {
      $flush();
    } else {
      $paragraph[] = trim($line);
    }
  }
  $flush();
  return $html . ($list_open ? '</ul>' : '');
}

// What the assistant looked up before answering, in plain words. One entry per step:
// ['icon' => ..., 'text' => ...]. $json is the stored "content_json" of a message.
function chat_steps($json)
{
  $names = [
    'search_properties' => ['bi-search', 'Searched the listings'],
    'get_property' => ['bi-building', 'Opened a listing'],
    'search_knowledge' => ['bi-book', 'Searched the guides and pages'],
    'create_lead' => ['bi-person-plus', 'Saved the visitor as a lead'],
  ];
  $steps = [];
  $parts = json_decode((string) $json, true);
  foreach (is_array($parts) ? $parts : [] as $part) {
    if (isset($part['functionCall']['name'])) {
      $name = (string) $part['functionCall']['name'];
      $details = [];
      foreach ((array) ($part['functionCall']['args'] ?? []) as $key => $value) {
        // A lead's name and number are shown in the lead box, not repeated in the steps.
        if ($name === 'create_lead' || !is_scalar($value) || (string) $value === '') {
          continue;
        }
        $details[] = str_replace('_', ' ', (string) $key) . ': ' . mb_substr((string) $value, 0, 60);
      }
      $steps[] = ['icon' => $names[$name][0] ?? 'bi-gear', 'text' => ($names[$name][1] ?? $name) . ($details ? ' (' . implode(', ', $details) . ')' : '')];
    } elseif (isset($part['functionResponse']['name'])) {
      $response = (array) ($part['functionResponse']['response'] ?? []);
      if (isset($response['total_matches'])) {
        $found = (int) $response['total_matches'];
        $steps[] = ['icon' => 'bi-list-check', 'text' => $found === 0 ? 'Nothing matched' : $found . ($found === 1 ? ' listing' : ' listings') . ' matched'];
      } elseif (isset($response['results']) && is_array($response['results'])) {
        $found = count($response['results']);
        $steps[] = ['icon' => 'bi-list-check', 'text' => $found === 0 ? 'Nothing found' : $found . ($found === 1 ? ' page' : ' pages') . ' found'];
      } elseif (isset($response['error']) && is_scalar($response['error'])) {
        $steps[] = ['icon' => 'bi-exclamation-triangle', 'text' => 'Could not do it: ' . mb_substr((string) $response['error'], 0, 120)];
      }
    }
  }
  return $steps;
}

// The details a visitor gave when the assistant saved them as a lead, from the stored steps.
function chat_lead_details(array $messages)
{
  $leads = [];
  foreach ($messages as $message) {
    $parts = json_decode((string) $message['content_json'], true);
    foreach (is_array($parts) ? $parts : [] as $part) {
      if (($part['functionCall']['name'] ?? '') === 'create_lead') {
        $lead = [];
        foreach ((array) ($part['functionCall']['args'] ?? []) as $key => $value) {
          if (is_scalar($value) && trim((string) $value) !== '') {
            $lead[ucfirst(str_replace('_', ' ', (string) $key))] = mb_substr(trim((string) $value), 0, 300);
          }
        }
        if ($lead) {
          $leads[] = $lead;
        }
      }
    }
  }
  return $leads;
}

// ---------------------------------------------------------------
// Filters (all optional, all from the address bar)
// ---------------------------------------------------------------
$f_search = isset($_GET['q']) && is_string($_GET['q']) ? trim(mb_substr($_GET['q'], 0, 100)) : '';
$f_lead = ($_GET['lead'] ?? '') === '1' ? '1' : '';
$f_from = isset($_GET['from']) && is_string($_GET['from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['from']) ? $_GET['from'] : '';
$f_to = isset($_GET['to']) && is_string($_GET['to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['to']) ? $_GET['to'] : '';
$f_page = max(1, (int) ($_GET['page'] ?? 1));
$f_chat = max(0, (int) ($_GET['chat'] ?? 0));
$per_page = 20;
$filters_on = $f_search !== '' || $f_lead !== '' || $f_from !== '' || $f_to !== '';

// The address of this page with the current filters, changed where asked.
function chat_url(array $change = [])
{
  global $f_search, $f_lead, $f_from, $f_to, $f_page, $f_chat;
  $params = array_merge(['q' => $f_search, 'lead' => $f_lead, 'from' => $f_from, 'to' => $f_to, 'page' => $f_page === 1 ? '' : $f_page, 'chat' => $f_chat === 0 ? '' : $f_chat], $change);
  $params = array_filter($params, function ($value) {
    return $value !== '' && $value !== null;
  });
  return BASE_URL . 'manage-ai-chats.php' . ($params ? '?' . http_build_query($params) : '');
}

// The chat tables come with the AI assistant: without them there is nothing to show.
$ready = (bool) chat_query($conn, "SHOW TABLES LIKE 'tbl_ai_conversations'") && (bool) chat_query($conn, "SHOW TABLES LIKE 'tbl_ai_messages'");
$has_usage = $ready && (bool) chat_query($conn, "SHOW TABLES LIKE 'tbl_ai_usage'");

// ---------------------------------------------------------------
// Deleting one chat (POST only, with a token)
// ---------------------------------------------------------------
$notice = '';
if ($ready && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['chat_action'])) {
  $token_ok = is_string($_POST['csrf'] ?? null) && hash_equals($_SESSION['admin_csrf'], $_POST['csrf']);
  $delete_id = (int) ($_POST['chat_id'] ?? 0);
  $found = $token_ok && $_POST['chat_action'] === 'delete' && $delete_id > 0 ? chat_query($conn, "SELECT conversation_id, turns, started_at FROM tbl_ai_conversations WHERE conversation_id = ?", [$delete_id]) : [];
  if ($found) {
    chat_query($conn, "DELETE FROM tbl_ai_messages WHERE conversation_id = ?", [$delete_id]);
    chat_query($conn, "DELETE FROM tbl_ai_conversations WHERE conversation_id = ?", [$delete_id]);
    // The log says that a chat was deleted, never what was said in it.
    $logger->log('DELETE', 'tbl_ai_conversations', $delete_id, ['turns' => $found[0]['turns'], 'started_at' => $found[0]['started_at']], null, 'AI chat deleted: #' . $delete_id);
    header('Location: ' . chat_url(['chat' => '', 'done' => 'deleted']));
    exit;
  }
  $notice = 'Nothing was deleted. Please reload the page and try again.';
}
if (($_GET['done'] ?? '') === 'deleted') {
  $notice = 'The chat was deleted.';
}

// ---------------------------------------------------------------
// The numbers, the list and the chat that is open
// ---------------------------------------------------------------
$stat = ['today' => 0, 'week' => 0, 'month' => 0, 'total' => 0, 'questions' => 0, 'leads' => 0];
$chats = [];
$total = 0;
$pages = 1;
$offset = 0;
$open = null;
$messages = [];
$usage = null;
$lead_details = [];
$auto_opened = false;

if ($ready) {
  $row = chat_query($conn, "SELECT COUNT(*) AS total, COALESCE(SUM(last_message_at >= CURDATE()), 0) AS today, COALESCE(SUM(last_message_at >= CURDATE() - INTERVAL 6 DAY), 0) AS week, COALESCE(SUM(last_message_at >= CURDATE() - INTERVAL 29 DAY), 0) AS month, COALESCE(SUM(turns), 0) AS questions, COALESCE(SUM(leads_created > 0), 0) AS leads FROM tbl_ai_conversations");
  if ($row) {
    $stat = array_map('intval', $row[0]);
  }

  $where = [];
  $params = [];
  if ($f_search !== '') {
    $like = '%' . addcslashes($f_search, '%_\\') . '%';
    $where[] = "(c.ip_address LIKE ? OR c.page_url LIKE ? OR EXISTS (SELECT 1 FROM tbl_ai_messages s WHERE s.conversation_id = c.conversation_id AND s.role IN ('user', 'model') AND s.content_text LIKE ?))";
    array_push($params, $like, $like, $like);
  }
  if ($f_lead !== '') {
    $where[] = 'c.leads_created > 0';
  }
  if ($f_from !== '') {
    $where[] = 'c.last_message_at >= ?';
    $params[] = $f_from . ' 00:00:00';
  }
  if ($f_to !== '') {
    $where[] = 'c.last_message_at <= ?';
    $params[] = $f_to . ' 23:59:59';
  }
  $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

  $count = chat_query($conn, "SELECT COUNT(*) AS n FROM tbl_ai_conversations c $where_sql", $params);
  $total = $count ? (int) $count[0]['n'] : 0;
  $pages = max(1, (int) ceil($total / $per_page));
  $f_page = min($f_page, $pages);
  $offset = ($f_page - 1) * $per_page;
  $chats = chat_query(
    $conn,
    "SELECT c.conversation_id, c.ip_address, c.page_url, c.turns, c.leads_created, c.started_at, c.last_message_at,
      (SELECT m.content_text FROM tbl_ai_messages m WHERE m.conversation_id = c.conversation_id AND m.role = 'user' ORDER BY m.message_id ASC LIMIT 1) AS first_question,
      (SELECT m.content_text FROM tbl_ai_messages m WHERE m.conversation_id = c.conversation_id AND m.role = 'user' ORDER BY m.message_id DESC LIMIT 1) AS last_question
    FROM tbl_ai_conversations c $where_sql ORDER BY c.last_message_at DESC, c.conversation_id DESC LIMIT ?, ?",
    array_merge($params, [$offset, $per_page])
  ) ?: [];

  // The chat that is open: the one asked for, otherwise the newest in the list.
  if ($f_chat === 0 && $chats) {
    $f_chat = (int) $chats[0]['conversation_id'];
    $auto_opened = true;
  }
  if ($f_chat > 0) {
    $row = chat_query($conn, "SELECT conversation_id, ip_address, page_url, turns, leads_created, started_at, last_message_at FROM tbl_ai_conversations WHERE conversation_id = ?", [$f_chat]);
    $open = $row ? $row[0] : null;
  }
  if ($open) {
    $messages = chat_query($conn, "SELECT message_id, role, content_text, content_json, tstp FROM tbl_ai_messages WHERE conversation_id = ? ORDER BY message_id", [$f_chat]) ?: [];
    $lead_details = chat_lead_details($messages);
    if ($has_usage) {
      $row = chat_query($conn, "SELECT COUNT(*) AS requests, COALESCE(SUM(input_tokens + output_tokens), 0) AS tokens, COALESCE(SUM(cost_usd), 0) AS cost, COALESCE(AVG(NULLIF(latency_ms, 0)), 0) AS latency, COALESCE(SUM(status NOT IN ('ok', 'started')), 0) AS failed FROM tbl_ai_usage WHERE conversation_id = ?", [$f_chat]);
      $usage = $row ? $row[0] : null;
    }
  } elseif ($f_chat > 0 && !$auto_opened) {
    $notice = $notice !== '' ? $notice : 'That chat is no longer there. It may have been deleted, or removed after the keeping period.';
  }
}

// Who the visitor is, as far as the chat tells: the name they gave, or a number.
$visitor_name = function (array $chat) {
  return 'Visitor #' . (int) $chat['conversation_id'];
};
$page_path = function ($url) {
  $path = (string) parse_url((string) $url, PHP_URL_PATH);
  return $path === '' ? '/' : $path;
};
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
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
            viewBox="0 0 24 24" version="1.1">
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
                  <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
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
              <!--begin::AI chats-->
              <link href="<?= BASE_URL ?>assets/css/dre-dashboard.css?v=2" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-leads.css?v=1" rel="stylesheet" type="text/css" />
              <link href="<?= BASE_URL ?>assets/css/dre-ai-chats.css?v=1" rel="stylesheet" type="text/css" />

              <div class="dre-dash dre-leads dre-chats<?= $open && !$auto_opened ? ' has-open' : '' ?>">

                <!-- Title and numbers -->
                <div class="dre-glass dre-glass-dark dre-hero dre-leads-hero">
                  <div>
                    <h1>AI chats</h1>
                    <p>What visitors asked the assistant on the website, and what it answered.</p>
                  </div>
                  <div class="dre-leads-stats">
                    <div><strong><?= number_format($stat['today']) ?></strong><span>Today</span></div>
                    <div><strong><?= number_format($stat['week']) ?></strong><span>7 days</span></div>
                    <div><strong><?= number_format($stat['month']) ?></strong><span>30 days</span></div>
                    <div><strong><?= number_format($stat['questions']) ?></strong><span>Questions</span></div>
                    <div><strong><?= number_format($stat['leads']) ?></strong><span>Gave a lead</span></div>
                  </div>
                  <div class="dre-actions">
                    <a class="dre-primary" href="<?= BASE_URL_HOME ?>" target="_blank" rel="noopener"><i class="bi bi-chat-dots"></i> Try the assistant</a>
                  </div>
                </div>

                <?php if ($notice !== ''): ?>
                  <div class="dre-leads-notice" role="status" id="chatNotice"><i class="bi bi-info-circle"></i> <?= chat_e($notice) ?></div>
                <?php endif; ?>

                <?php if (!$ready): ?>
                  <div class="dre-glass">
                    <div class="dre-empty"><i class="bi bi-robot"></i><br>The AI assistant's tables are not in this database yet, so there are no chats to show.<br>Run <code>db/migrations/004_create_ai_chat_tables.sql</code> first.</div>
                  </div>
                <?php else: ?>

                <!-- Search and dates -->
                <form method="get" action="<?= BASE_URL ?>manage-ai-chats.php" class="dre-glass dre-leads-filter">
                  <label class="dre-leads-search">
                    <span>Search</span>
                    <i class="bi bi-search"></i>
                    <input type="search" name="q" value="<?= chat_e($f_search) ?>" placeholder="A word from the chat, a page or an IP address" autocomplete="off">
                  </label>
                  <label>
                    <span>From</span>
                    <input type="date" name="from" value="<?= chat_e($f_from) ?>" max="<?= date('Y-m-d') ?>">
                  </label>
                  <label>
                    <span>To</span>
                    <input type="date" name="to" value="<?= chat_e($f_to) ?>" max="<?= date('Y-m-d') ?>">
                  </label>
                  <label>
                    <span>Show</span>
                    <select name="lead">
                      <option value="">All chats</option>
                      <option value="1" <?= $f_lead === '1' ? 'selected' : '' ?>>Only chats that gave a lead</option>
                    </select>
                  </label>
                  <div class="dre-leads-filter-buttons">
                    <button type="submit" class="dre-btn dre-btn-main"><i class="bi bi-funnel"></i> Apply</button>
                    <?php if ($filters_on): ?>
                      <a class="dre-btn" href="<?= BASE_URL ?>manage-ai-chats.php">Clear</a>
                    <?php endif; ?>
                  </div>
                </form>

                <div class="dre-chats-layout">

                  <!-- The list of chats -->
                  <div class="dre-glass dre-chats-list">
                    <div class="dre-h">
                      <h3>
                        <?= number_format($total) ?> <?= $total === 1 ? 'chat' : 'chats' ?>
                        <small><?= $total > 0 ? 'Newest first' . ($filters_on ? ' &middot; filtered' : '') : 'Nothing matches' . ($filters_on ? ' these filters' : '') ?></small>
                      </h3>
                    </div>
                    <?php if (!$chats): ?>
                      <div class="dre-empty"><i class="bi bi-chat-square-dots"></i><br><?= $filters_on ? 'No chat matches. Try other words or dates.' : 'No one has used the assistant yet.' ?></div>
                    <?php else: ?>
                      <ul class="dre-chats-items">
                        <?php foreach ($chats as $chat):
                          $chat_id = (int) $chat['conversation_id'];
                          $question = trim((string) ($chat['first_question'] ?? ''));
                          $is_open = $open && (int) $open['conversation_id'] === $chat_id; ?>
                          <li>
                            <a href="<?= chat_e(chat_url(['chat' => $chat_id])) ?>#chatPanel" class="<?= $is_open ? 'is-on' : '' ?>"<?= $is_open ? ' aria-current="true"' : '' ?>>
                              <span class="dre-avatar"><i class="bi bi-person"></i></span>
                              <span class="dre-chats-item-main">
                                <strong><?= chat_e($question !== '' ? mb_substr($question, 0, 90) : 'No question yet') ?></strong>
                                <span><?= chat_e($visitor_name($chat)) ?> &middot; <?= (int) $chat['turns'] ?> <?= (int) $chat['turns'] === 1 ? 'question' : 'questions' ?> &middot; <?= chat_e($page_path($chat['page_url'])) ?></span>
                              </span>
                              <span class="dre-chats-item-side">
                                <?= chat_e(chat_ago($chat['last_message_at'])) ?>
                                <?php if ((int) $chat['leads_created'] > 0): ?><br><em class="dre-pill dre-pill-good"><i class="bi bi-person-check"></i> Lead</em><?php endif; ?>
                              </span>
                            </a>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                      <?php if ($pages > 1): ?>
                        <nav class="dre-leads-pages" aria-label="Pages">
                          <?php if ($f_page > 1): ?>
                            <a href="<?= chat_e(chat_url(['page' => $f_page - 1, 'chat' => ''])) ?>" aria-label="Previous page"><i class="bi bi-chevron-left"></i></a>
                          <?php endif; ?>
                          <span>Page <?= $f_page ?> of <?= $pages ?></span>
                          <?php if ($f_page < $pages): ?>
                            <a href="<?= chat_e(chat_url(['page' => $f_page + 1, 'chat' => ''])) ?>" aria-label="Next page"><i class="bi bi-chevron-right"></i></a>
                          <?php endif; ?>
                        </nav>
                      <?php endif; ?>
                    <?php endif; ?>
                  </div>

                  <!-- The chat that is open -->
                  <div class="dre-glass dre-chats-panel" id="chatPanel">
                    <?php if (!$open): ?>
                      <div class="dre-empty"><i class="bi bi-chat-left-text"></i><br>Choose a chat from the list to read it here.</div>
                    <?php else:
                      $started = strtotime((string) $open['started_at']);
                      $last = strtotime((string) $open['last_message_at']); ?>
                      <a class="dre-chats-back" href="<?= chat_e(chat_url(['chat' => ''])) ?>"><i class="bi bi-arrow-left"></i> All chats</a>
                      <div class="dre-h">
                        <h3>
                          <?= chat_e($visitor_name($open)) ?>
                          <small><?= $started ? date('d M Y, H:i', $started) : '' ?><?= $last && $last - $started > 60 ? ' &ndash; ' . date($last - $started > 86400 ? 'd M Y, H:i' : 'H:i', $last) : '' ?> &middot; <?= (int) $open['turns'] ?> <?= (int) $open['turns'] === 1 ? 'question' : 'questions' ?></small>
                        </h3>
                        <form method="post" action="<?= chat_e(chat_url()) ?>" class="dre-chats-delete">
                          <input type="hidden" name="csrf" value="<?= chat_e($_SESSION['admin_csrf']) ?>">
                          <input type="hidden" name="chat_id" value="<?= (int) $open['conversation_id'] ?>">
                          <button type="submit" name="chat_action" value="delete" class="dre-icon-btn dre-icon-del" title="Delete this chat" aria-label="Delete this chat"><i class="bi bi-trash3"></i></button>
                        </form>
                      </div>

                      <dl class="dre-chats-facts">
                        <div><dt>Started on page</dt><dd><?php if (preg_match('/^https?:\/\//i', (string) $open['page_url'])): ?><a href="<?= chat_e($open['page_url']) ?>" target="_blank" rel="noopener noreferrer"><?= chat_e($page_path($open['page_url'])) ?></a><?php else: ?><?= chat_e($open['page_url'] !== '' ? $open['page_url'] : 'Not known') ?><?php endif; ?></dd></div>
                        <div><dt>IP address</dt><dd><?= chat_e($open['ip_address'] !== '' ? $open['ip_address'] : 'Not known') ?></dd></div>
                        <?php if ($usage && (int) $usage['requests'] > 0): ?>
                          <div><dt>Answer time</dt><dd><?= (float) $usage['latency'] > 0 ? number_format((float) $usage['latency'] / 1000, 1) . ' s on average' : 'Not measured' ?><?= (int) $usage['failed'] > 0 ? ' &middot; ' . (int) $usage['failed'] . ' failed' : '' ?></dd></div>
                          <div><dt>AI cost</dt><dd>$<?= number_format((float) $usage['cost'], 4) ?> &middot; <?= number_format((int) $usage['tokens']) ?> tokens</dd></div>
                        <?php endif; ?>
                      </dl>

                      <?php foreach ($lead_details as $lead): ?>
                        <div class="dre-chats-lead">
                          <strong><i class="bi bi-person-check"></i> The visitor left their details</strong>
                          <dl>
                            <?php foreach ($lead as $label => $value): ?>
                              <div><dt><?= chat_e($label) ?></dt><dd><?= chat_e($value) ?></dd></div>
                            <?php endforeach; ?>
                          </dl>
                          <a href="<?= BASE_URL ?>manage-leads.php?source=Offplan&amp;q=<?= chat_e(rawurlencode((string) ($lead['Phone'] ?? $lead['Name'] ?? ''))) ?>">Open in Leads <i class="bi bi-arrow-right"></i></a>
                        </div>
                      <?php endforeach; ?>
                      <?php if ((int) $open['leads_created'] > 0 && !$lead_details): ?>
                        <div class="dre-chats-lead"><strong><i class="bi bi-person-check"></i> The visitor left their details in this chat.</strong> <a href="<?= BASE_URL ?>manage-leads.php?source=Offplan">Open Leads <i class="bi bi-arrow-right"></i></a></div>
                      <?php endif; ?>

                      <div class="dre-chat-thread" id="chatThread" tabindex="0" aria-label="The chat">
                        <?php if (!$messages): ?>
                          <div class="dre-empty">This chat has no messages.</div>
                        <?php endif; ?>
                        <?php foreach ($messages as $message):
                          $text = trim((string) $message['content_text']);
                          $time = strtotime((string) $message['tstp']);
                          if ($message['role'] === 'user'): ?>
                            <div class="dre-chat-msg dre-chat-user">
                              <div class="dre-chat-bubble"><?= nl2br(chat_e($text)) ?></div>
                              <span>Visitor<?= $time ? ' &middot; ' . date('H:i', $time) : '' ?></span>
                            </div>
                          <?php else:
                            $steps = chat_steps($message['content_json']);
                            if ($steps): ?>
                              <div class="dre-chat-steps">
                                <?php foreach ($steps as $step): ?>
                                  <span><i class="bi <?= chat_e($step['icon']) ?>"></i> <?= chat_e($step['text']) ?></span>
                                <?php endforeach; ?>
                              </div>
                            <?php endif;
                            if ($message['role'] === 'model' && $text !== ''): ?>
                              <div class="dre-chat-msg dre-chat-ai">
                                <div class="dre-chat-bubble"><?= chat_text_html($text) ?></div>
                                <span><i class="bi bi-stars"></i> Assistant<?= $time ? ' &middot; ' . date('H:i', $time) : '' ?></span>
                              </div>
                            <?php endif;
                          endif;
                        endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>

                <p class="dre-chats-note"><i class="bi bi-shield-lock"></i> Chats are personal data. Only super admins see this page, and chats are removed by themselves after the keeping period set for the assistant.</p>

                <?php endif; ?>
              </div>
              <!--end::AI chats-->

            </div>
            <!--end::Container-->
          </div>
          <!--end::Entry-->
        </div>
        <!--end::Content-->

        <?php
        include('includes/footer-inner.php');
        ?>

        <script>
          // AI chats: ask before a chat is deleted.
          (function () {
            document.querySelectorAll('.dre-chats-delete').forEach(function (form) {
              form.addEventListener('submit', function (event) {
                if (!window.confirm('Delete this chat? What was said in it cannot be brought back.')) {
                  event.preventDefault();
                }
              });
            });
            var notice = document.getElementById('chatNotice');
            if (notice) {
              setTimeout(function () { notice.style.display = 'none'; }, 7000);
              if (window.history.replaceState) {
                // Reloading the page should not say "deleted" again.
                window.history.replaceState(null, '', window.location.href.replace(/([?&])done=deleted&?/, '$1').replace(/[?&]$/, ''));
              }
            }
          })();
        </script>
