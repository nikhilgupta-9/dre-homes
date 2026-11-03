<?php
// Run: php ai/tests/chat_test.php
// Does not call the AI provider. Uses the local database with the IP address
// 'selftest' and removes its rows again.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require __DIR__ . '/../../db/config.php';
require __DIR__ . '/../ChatService.php';
while (ob_get_level() > 0) {
    ob_end_flush();
}

$failed = 0;
$checked = 0;

function check($label, $actual, $expected)
{
    global $failed, $checked;
    $checked++;
    if ($actual !== $expected) {
        $failed++;
        echo "FAIL $label\n  expected: " . var_export($expected, true) . "\n  actual:   " . var_export($actual, true) . "\n";
    }
}

function cleanup_selftest()
{
    db_query("DELETE m FROM tbl_ai_messages m JOIN tbl_ai_conversations c ON c.conversation_id = m.conversation_id WHERE c.ip_address = 'selftest'");
    db_query("DELETE FROM tbl_ai_conversations WHERE ip_address = 'selftest'");
    db_query("DELETE FROM tbl_ai_usage WHERE ip_address = 'selftest'");
    db_query("DELETE FROM tbl_offplan_enq WHERE ip_address = 'selftest' AND channel = 'AI Chat'");
}

// Sends one message and collects what the browser would receive.
function send($service, $token, $message)
{
    $events = [];
    $token = $service->handle($token, $message, BASE_URL, 'selftest', function ($event, $data) use (&$events) {
        $events[] = [$event, $data];
    });
    $text = '';
    foreach ($events as $event) {
        $text .= $event[1]['text'] ?? ($event[1]['message'] ?? '');
    }
    return ['token' => $token, 'events' => array_column($events, 0), 'text' => $text];
}

$config = require __DIR__ . '/../config.php';
cleanup_selftest();

// --- Tools: arguments are checked before anything touches the database ---
$tools = new ChatTools($config, BASE_URL, ['ip_address' => 'selftest']);
$names = array_column(ChatTools::declarations(), 'name');
sort($names);
check('exactly four tools exist', $names, ['create_lead', 'get_property', 'search_knowledge', 'search_properties']);
check('unknown tool is refused', $tools->call('run_sql', ['query' => 'select 1']), ['error' => 'Unknown tool.']);
check('bad listing type is refused', isset($tools->call('search_properties', ['listing_type' => "x' OR 1=1"])['error']), true);
check('bad price is refused', isset($tools->call('search_properties', ['max_price_aed' => 'cheap'])['error']), true);
check('min above max is refused', isset($tools->call('search_properties', ['min_price_aed' => 5000000, 'max_price_aed' => 1000000])['error']), true);
check('bad property kind is refused', isset($tools->call('search_properties', ['property_kind' => 'castle'])['error']), true);

$all = $tools->call('search_properties', ['limit' => 99]);
check('limit is capped', isset($all['error']) || $all['shown'] <= 8, true);
$injected = $tools->call('search_properties', ['location' => "%' OR '1'='1", 'limit' => 3]);
check('SQL in a text filter matches nothing', $injected['total_matches'], 0);

$priced = $tools->call('search_properties', ['listing_type' => 'off_plan', 'max_price_aed' => 1500000, 'limit' => 8]);
$over_budget = array_filter($priced['listings'], function ($listing) {
    return !isset($listing['price_aed']) || $listing['price_aed'] > 1500000;
});
check('a budget search returns only priced listings within budget', count($priced['listings']) > 0 && count($over_budget) === 0, true);
$site = BASE_URL;
$off_site = array_filter($priced['listings'], function ($listing) use ($site) {
    return strpos($listing['url'], $site) !== 0;
});
check('every link points to our own site', count($off_site), 0);
$leaked = preg_match('/proj_email|proj_mobileno|prop_user_id|prj_id|whatsaap/i', json_encode($priced));
check('no internal column appears in a result', $leaked, 0);

$rent = $tools->call('search_properties', ['listing_type' => 'rent', 'limit' => 1, 'sort' => 'price_low_to_high']);
check('rent prices are per year', strpos($rent['listings'][0]['price_text'] ?? '', 'per year') !== false, true);

check('missing slug is refused', isset($tools->call('get_property', [])['error']), true);
check('unknown slug is refused', isset($tools->call('get_property', ['slug' => 'no-such-property-xyz'])['error']), true);
check('slug with SQL is refused', isset($tools->call('get_property', ['slug' => "x' OR 1=1 -- "])['error']), true);
$detail = $tools->call('get_property', ['slug' => $priced['listings'][0]['url']]);
check('a full link works as a slug', $detail['name'] ?? '', $priced['listings'][0]['name']);

check('empty knowledge query is refused', isset($tools->call('search_knowledge', ['query' => ''])['error']), true);
check('bad knowledge topic is refused', isset($tools->call('search_knowledge', ['query' => 'visa', 'topic' => 'leads'])['error']), true);

// --- create_lead ---
$lead = ['name' => 'Selftest Visitor', 'phone' => '+971 50 000 0000', 'visitor_agreed_to_be_contacted' => true];
check('no consent, no lead', $tools->call('create_lead', ['visitor_agreed_to_be_contacted' => false] + $lead)['saved'], false);
check('consent must be a real yes', $tools->call('create_lead', ['visitor_agreed_to_be_contacted' => 'maybe'] + $lead)['saved'], false);
check('a name is needed', $tools->call('create_lead', ['name' => '1'] + $lead)['saved'], false);
check('a script is not a name', $tools->call('create_lead', ['name' => '<script>alert(1)</script>'] + $lead)['saved'], false);
check('a bad phone is refused', $tools->call('create_lead', ['phone' => 'call me maybe'] + $lead)['saved'], false);
check('a bad email is refused', $tools->call('create_lead', ['email' => 'not-an-email'] + $lead)['saved'], false);
check('phone or email is needed', $tools->call('create_lead', ['phone' => ''] + $lead)['saved'], false);
$before = db_row("SELECT COUNT(*) AS n FROM tbl_offplan_enq WHERE ip_address = 'selftest'");
check('nothing was saved by the refused attempts', (int) $before['n'], 0);
check('a valid lead is saved', $tools->call('create_lead', $lead)['saved'], true);
$saved = db_row("SELECT name, phone, channel FROM tbl_offplan_enq WHERE ip_address = 'selftest' ORDER BY id DESC LIMIT 1");
check('the lead is in the enquiry table', $saved, ['name' => 'Selftest Visitor', 'phone' => '+971 50 000 0000', 'channel' => 'AI Chat']);
check('only one lead per conversation', $tools->call('create_lead', $lead)['saved'], false);

// --- Chat service without a provider key: the fallback is used and nothing is sent anywhere ---
$offline = $config;
$offline['chat_secret'] = 'a_secret_that_does_not_exist';
$service = new ChatService($offline, BASE_URL);

$reply = send($service, '', '   ');
check('empty message is refused', $reply['events'], ['error']);
$reply = send($service, '', str_repeat('a', $config['chat_max_message_chars'] + 1));
check('too long a message is refused', $reply['events'], ['error']);
$count = db_row("SELECT COUNT(*) AS n FROM tbl_ai_conversations WHERE ip_address = 'selftest'");
check('refused messages do not start a conversation', (int) $count['n'], 0);

$reply = send($service, '', 'Hello');
check('without a key the visitor gets the fallback', $reply['text'], ChatService::FALLBACK);
check('a conversation token is issued', preg_match('/^[a-f0-9]{48}$/', $reply['token']), 1);
$stored = db_row("SELECT token_hash FROM tbl_ai_conversations WHERE ip_address = 'selftest' ORDER BY conversation_id DESC LIMIT 1");
check('only a hash of the token is stored', $stored['token_hash'], hash('sha256', $reply['token']));
$again = send($service, $reply['token'], 'Hello again');
check('the same token continues the conversation', $again['token'], $reply['token']);
$forged = send($service, str_repeat('ab', 24), 'Hello');
check('an unknown token starts a new conversation', $forged['token'] !== str_repeat('ab', 24), true);

// --- Rate limit per connection ---
cleanup_selftest();
$limit = (int) $config['chat_per_ip_per_minute'];
$token = '';
for ($i = 0; $i < $limit; $i++) {
    $reply = send($service, '', "Message $i");
}
check('messages up to the limit are answered', $reply['events'], ['delta', 'done']);
$reply = send($service, '', 'One too many');
check('the next message in the same minute is refused', $reply['events'], ['error']);

// --- Turn limit per conversation ---
cleanup_selftest();
$reply = send($service, '', 'Hello');
db_query("UPDATE tbl_ai_conversations SET turns = ? WHERE ip_address = 'selftest'", [(int) $config['chat_max_turns']]);
$reply = send($service, $reply['token'], 'More');
check('a conversation ends at the turn limit', strpos($reply['text'], 'start a new chat') !== false, true);

// --- Daily cap ---
cleanup_selftest();
$capped = $config;
$capped['chat_daily_token_cap'] = 1;
db_query("INSERT INTO tbl_ai_usage (feature, status, input_tokens, ip_address, created_at) VALUES ('chat', 'ok', 5, 'selftest', NOW())");
$reply = send(new ChatService($capped, BASE_URL), '', 'Hello');
$status = db_row("SELECT status FROM tbl_ai_usage WHERE ip_address = 'selftest' ORDER BY usage_id DESC LIMIT 1");
check('over the daily cap the fallback is used', [$reply['text'], $status['status']], [ChatService::FALLBACK, 'capped']);

cleanup_selftest();
$left = db_row("SELECT (SELECT COUNT(*) FROM tbl_ai_conversations WHERE ip_address = 'selftest') + (SELECT COUNT(*) FROM tbl_ai_usage WHERE ip_address = 'selftest') + (SELECT COUNT(*) FROM tbl_offplan_enq WHERE ip_address = 'selftest') AS n");
check('test rows are removed', (int) $left['n'], 0);

echo $failed === 0 ? "OK: $checked checks passed\n" : "$failed of $checked checks failed\n";
exit($failed === 0 ? 0 : 1);
