<!--begin::Footer-->
<div class="footer bg-white py-4 d-flex flex-lg-column" id="kt_footer">
<!--begin::Container-->
<div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
<!--begin::Copyright-->
<div class="text-dark order-2 order-md-1">
<span class="text-muted font-weight-bold mr-2"><?=date('Y')?>©</span>
<a href="http://keenthemes.com/metronic" target="_blank" class="text-dark-75 text-hover-primary">All rights reserved. &copy DREHOMES</a>
</div>
<!--end::Copyright-->

</div>
<!--end::Container-->
</div>
<!--end::Footer-->
</div>
<!--end::Wrapper-->
</div>
<!--end::Page-->
</div>
<!--end::Main-->
<div id="kt_quick_user" class="offcanvas offcanvas-right p-10">
<!--begin::Header-->
<div class="offcanvas-header d-flex align-items-center justify-content-between pb-5" kt-hidden-height="41" style="">
<h3 class="font-weight-bold m-0">User Profile</h3>
<a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
<i class="ki ki-close icon-xs text-muted"></i>
</a>
</div>
<!--end::Header-->
<!--begin::Content-->
<div class="offcanvas-content pr-5 mr-n5 scroll ps ps--active-y" style="height: 407px; overflow: hidden;">
<!--begin::Header-->
<div class="d-flex align-items-center mt-5">
<div class="d-flex flex-column">
<a href="#" class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary"><?=$_SESSION['username']?></a>
<div class="navi mt-2">
<a href="#" class="navi-item">
<span class="navi-link p-0 pb-2">
<span class="navi-icon mr-1">
<span class="svg-icon svg-icon-lg svg-icon-primary">
<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Mail-notification.svg-->
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
<rect x="0" y="0" width="24" height="24"></rect>
<path d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000"></path>
<circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5" r="2.5"></circle>
</g>
</svg>
<!--end::Svg Icon-->
</span>
</span>
<span class="navi-text text-muted text-hover-primary"><?=$_SESSION['email_id']?></span>
</span>
</a>
<a href="<?=BASE_URL?>my-profile.php" class="btn btn-sm btn-light-primary font-weight-bolder py-2 px-5 mr-2">My Profile</a>
<a href="<?=BASE_URL?>logout.php" class="btn btn-sm btn-light-primary font-weight-bolder py-2 px-5">Sign Out</a>
</div>
</div>
</div>
<!--end::Header-->
<!--begin::Separator-->
<div class="separator separator-dashed mt-8 mb-5"></div>
<!--end::Separator-->
<!--begin::Nav-->



<div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; height: 407px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 186px;"></div></div></div>
<!--end::Content-->
</div>

<script>var HOST_URL = "https://preview.keenthemes.com/metronic/theme/html/tools/preview";</script>
<!--begin::Global Config(global config for global JS scripts)-->
<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>
<!--end::Global Config-->
<!--begin::Global Theme Bundle(used by all pages)-->
<script src="<?=BASE_URL?>assets/plugins/global/plugins.bundle.js"></script>
<script src="<?=BASE_URL?>assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
<script src="<?=BASE_URL?>assets/js/scripts.bundle.js"></script>
<!--end::Global Theme Bundle-->
<!--begin::Page Vendors(used by this page)-->
<script src="<?=BASE_URL?>assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
<!--end::Page Vendors-->
<!--begin::Page Scripts(used by this page)-->

<script type='text/javascript' src='<?=BASE_URL?>assets/js/pages/widgets.js'></script>
<!--end::Page Scripts-->
<script type='text/javascript' src='<?=BASE_URL?>assets/plugins/custom/datatables/datatables.bundle.js'></script>
<script type='text/javascript' src='<?=BASE_URL?>assets/js/pages/crud/datatables/basic/basic.js'></script>
<script type='text/javascript' src='<?=BASE_URL?>assets/js/pages/features/miscellaneous/sweetalert2.js'></script>
<script type='text/javascript' src='<?=BASE_URL?>assets/js/pages/crud/file-upload/image-input.js'></script>
<script type='text/javascript' src='<?=BASE_URL?>assets/js/jquery.validate.js'></script>
<script type='text/javascript' src='<?=BASE_URL?>assets/plugins/custom/tinymce/tinymce.bundle.js'></script>
<script type='text/javascript' src='<?=BASE_URL?>assets/js/pages/crud/forms/editors/tinymce.js'></script>
<script type='text/javascript' src='<?=BASE_URL?>assets/js/pages/crud/forms/widgets/tagify.js'></script>
<script src="<?=BASE_URL?>assets/js/additional-methods.min.js"></script>
<script src=<?=BASE_URL?>assets/js/pages/features/miscellaneous/sweetalert2.js></script>
<script src="<?=BASE_URL?>assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?=BASE_URL?>assets/js/custom.js?v=20261017d"></script>

<!--end::Page Scripts-->
<script type='text/javascript'> 
$(document).ready(function(e){
$('#example').DataTable();
if($(window).width() < 600) {
$('.dataTable').addClass('table-responsive');
}

});

</script>
<script>
// Links to a part of the same page ("#sec-seo").
// The <base> tag in the header makes the browser read such a link as an address on the website's
// home, so a click left the page. Here the click scrolls to the part instead.
document.addEventListener('click', function (event) {
	var link = event.target.closest ? event.target.closest('a[href^="#"]') : null;
	var id = link ? link.getAttribute('href').slice(1) : '';
	if (id === '' || event.defaultPrevented || event.ctrlKey || event.metaKey || event.shiftKey) {
		return;
	}
	var target = document.getElementById(id);
	if (!target) {
		return;
	}
	event.preventDefault();
	target.scrollIntoView({ block: 'start' });
	if (window.history && window.history.replaceState) {
		window.history.replaceState(null, '', window.location.pathname + window.location.search + '#' + id);
	}
});
// Opening a page with "#part" in its address goes to that part once the page has loaded.
window.addEventListener('load', function () {
	var target = window.location.hash.length > 1 ? document.getElementById(window.location.hash.slice(1)) : null;
	if (target) {
		target.scrollIntoView({ block: 'start' });
	}
});
</script>
<?php
// The "Ask AI" widget below needs this on every admin page, not only the ones that already set
// it for their own forms — without it, the chat endpoint rejects every message as "page expired".
if (empty($_SESSION['admin_csrf'])) {
	$_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
}
?>
<!--begin::Ask AI help widget-->
<style>
/* Colors match the admin's own theme (see admin_nsrdwsc/assets/css/dre-dashboard.css):
   --dd-main:#5f574c, --dd-accent:#ab9889, --dd-ink:#2f2a25, --dd-muted:#7c7268,
   --dd-good:#3f8f6b, --dd-bad:#b9524a. Hardcoded here since this widget sits outside .dre-dash. */
#dreAiBtnWrap{position:fixed;right:22px;bottom:22px;z-index:1090;font-family:Poppins,Helvetica,sans-serif}
#dreAiBtnWrap::before{content:'';position:absolute;inset:-6px;border-radius:50%;background:linear-gradient(135deg,#5f574c,#ab9889);opacity:.45;filter:blur(10px);animation:dreAiGlow 2.6s ease-in-out infinite}
#dreAiBtn{position:relative;width:58px;height:58px;border-radius:50%;background:linear-gradient(135deg,#5f574c,#ab9889);color:#fff;border:0;box-shadow:0 8px 20px rgba(95,87,76,.45);font-size:23px;display:flex;align-items:center;justify-content:center;cursor:pointer;animation:dreAiFloat 2.4s ease-in-out infinite;transition:transform .2s}
#dreAiBtn:hover{transform:scale(1.07);box-shadow:0 10px 24px rgba(95,87,76,.55)}
#dreAiBtn i{position:relative;z-index:1}
#dreAiBadge{position:absolute;top:-4px;right:-4px;background:#b9524a;color:#fff;font-size:10px;font-weight:700;line-height:1;padding:3px 5px;border-radius:999px;border:2px solid #fff;z-index:2;animation:dreAiPop 1.8s ease-in-out infinite}
#dreAiLabel{position:absolute;right:68px;bottom:16px;background:#2f2a25;color:#fff;font-size:12px;font-weight:600;padding:7px 12px;border-radius:999px;white-space:nowrap;box-shadow:0 6px 16px rgba(0,0,0,.2);opacity:0;transform:translateX(6px);transition:opacity .25s,transform .25s;pointer-events:none}
#dreAiBtnWrap:hover #dreAiLabel{opacity:1;transform:translateX(0)}
#dreAiBtnWrap.open #dreAiLabel,#dreAiBtnWrap.open #dreAiBadge{display:none}
@keyframes dreAiFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-5px)}}
@keyframes dreAiGlow{0%,100%{opacity:.3;transform:scale(1)}50%{opacity:.6;transform:scale(1.08)}}
@keyframes dreAiPop{0%,100%{transform:scale(1)}50%{transform:scale(1.18)}}
#dreAiPanel{position:fixed;right:22px;bottom:86px;z-index:1090;width:340px;max-height:520px;background:#fdfbf9;border-radius:16px;box-shadow:0 12px 32px rgba(47,42,37,.22);display:none;flex-direction:column;overflow:hidden;font-family:Poppins,Helvetica,sans-serif;border:1px solid rgba(171,152,137,.3)}
#dreAiPanel.open{display:flex}
#dreAiPanel .dre-ai-head{background:linear-gradient(135deg,#5f574c,#ab9889);color:#fff;padding:12px 14px;display:flex;align-items:center;justify-content:space-between}
#dreAiPanel .dre-ai-head b{font-size:14px;font-weight:600}
#dreAiPanel .dre-ai-head i{margin-right:4px}
#dreAiPanel .dre-ai-head button{background:none;border:0;color:#fff;font-size:18px;cursor:pointer;line-height:1}
#dreAiBody{flex:1;overflow-y:auto;padding:12px;background:#f3eee7;font-size:13px;color:#2f2a25}
#dreAiBody .dre-ai-msg{margin-bottom:10px;padding:8px 10px;border-radius:10px;max-width:92%;line-height:1.4}
#dreAiBody .dre-ai-msg.me{background:#5f574c;color:#fff;margin-left:auto}
#dreAiBody .dre-ai-msg.bot{background:#fff;border:1px solid rgba(171,152,137,.3);color:#2f2a25}
#dreAiBody .dre-ai-msg.bot p{margin:0 0 6px}
#dreAiBody .dre-ai-msg.bot p:last-child{margin-bottom:0}
#dreAiBody .dre-ai-msg.bot ul,#dreAiBody .dre-ai-msg.bot ol{margin:4px 0 6px;padding-left:20px}
#dreAiBody .dre-ai-msg.bot li{margin-bottom:3px}
#dreAiBody .dre-ai-videos{margin-top:6px;font-size:12px}
#dreAiBody .dre-ai-videos a{display:block;color:#5f574c;text-decoration:underline;margin-top:2px}
#dreAiBody .dre-ai-links{margin-top:8px;display:flex;flex-wrap:wrap;gap:6px}
#dreAiBody .dre-ai-pill{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:#fff;text-decoration:none;border-radius:999px;padding:6px 13px;background:linear-gradient(135deg,#5f574c,#ab9889);box-shadow:0 3px 8px rgba(95,87,76,.3);transition:filter .15s}
#dreAiBody .dre-ai-pill:hover{filter:brightness(1.08);color:#fff}
/* Confirm/Cancel card for a proposed show/hide change (propose_record_status_change) */
#dreAiBody .dre-ai-action{margin-top:8px;padding:8px 10px;border-radius:10px;background:rgba(171,152,137,.14);border:1px dashed rgba(171,152,137,.5);font-size:12px}
#dreAiBody .dre-ai-action .dre-ai-action-row{display:flex;gap:8px;margin-top:6px}
#dreAiBody .dre-ai-action button{flex:1;border:0;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:600;cursor:pointer}
#dreAiBody .dre-ai-action .dre-ai-confirm{background:#3f8f6b;color:#fff}
#dreAiBody .dre-ai-action .dre-ai-cancel{background:#e4ddd4;color:#2f2a25}
#dreAiBody .dre-ai-action button:disabled{opacity:.55;cursor:default}
#dreAiForm{display:flex;border-top:1px solid rgba(171,152,137,.3);padding:8px;background:#fdfbf9}
#dreAiForm input{flex:1;border:1px solid rgba(171,152,137,.4);border-radius:999px;padding:7px 12px;font-size:13px;font-family:inherit}
#dreAiForm button{margin-left:6px;background:#5f574c;color:#fff;border:0;border-radius:999px;width:34px;cursor:pointer;flex:none}
#dreAiForm button:hover{background:#4a433a}
#dreAiMic.listening{background:#b9524a;animation:dreAiPop 1s ease-in-out infinite}
</style>
<div id="dreAiBtnWrap">
	<span id="dreAiLabel">Ask AI &#10024;</span>
	<button id="dreAiBtn" aria-label="Ask AI" title="Ask AI"><i class="bi bi-stars"></i></button>
	<span id="dreAiBadge">AI</span>
</div>
<div id="dreAiPanel" role="dialog" aria-label="Ask AI">
	<div class="dre-ai-head"><b><i class="bi bi-robot"></i> Ask AI</b><button id="dreAiClose" aria-label="Close">&times;</button></div>
	<div id="dreAiBody"></div>
	<form id="dreAiForm">
		<input type="text" id="dreAiInput" maxlength="500" autocomplete="off" placeholder="Ask, or tap the mic to speak...">
		<button type="button" id="dreAiMic" aria-label="Speak your question" title="Speak"><i class="bi bi-mic"></i></button>
		<button type="submit" aria-label="Send"><i class="bi bi-send"></i></button>
	</form>
</div>
<script>
window.dreAiHistory = <?= json_encode(array_values($_SESSION['ai_help_chat'] ?? []), JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_HEX_TAG | JSON_HEX_APOS) ?>;
</script>
<script>
(function(){
	var csrf = '<?= htmlspecialchars($_SESSION['admin_csrf'] ?? '', ENT_QUOTES, "UTF-8") ?>';
	var btn = document.getElementById('dreAiBtn'), panel = document.getElementById('dreAiPanel'),
		wrap = document.getElementById('dreAiBtnWrap'),
		body = document.getElementById('dreAiBody'), form = document.getElementById('dreAiForm'),
		input = document.getElementById('dreAiInput'), close = document.getElementById('dreAiClose'),
		mic = document.getElementById('dreAiMic');
	function escHtml(s){
		return s.replace(/[&<>"]/g, function(c){ return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; });
	}
	// Plain text from the AI, kept safe (escaped) but with its own line breaks, numbered
	// steps ("1. ...") and bullets ("- ...") rendered as real lines/lists instead of one run-on line.
	function formatAnswer(text){
		var lines = String(text).replace(/\r\n/g, '\n').split('\n');
		var html = '', listOpen = '';
		lines.forEach(function(line){
			var bullet = /^\s*[-*]\s+(.*)$/.exec(line);
			var numbered = /^\s*\d+[.)]\s+(.*)$/.exec(line);
			if (bullet || numbered) {
				var tag = bullet ? 'ul' : 'ol';
				if (listOpen !== tag) {
					if (listOpen) { html += '</' + listOpen + '>'; }
					html += '<' + tag + '>';
					listOpen = tag;
				}
				html += '<li>' + escHtml((bullet || numbered)[1]) + '</li>';
				return;
			}
			if (listOpen) { html += '</' + listOpen + '>'; listOpen = ''; }
			if (line.trim() !== '') { html += '<p>' + escHtml(line) + '</p>'; }
		});
		if (listOpen) { html += '</' + listOpen + '>'; }
		return html;
	}
	function add(text, who){
		var d = document.createElement('div');
		d.className = 'dre-ai-msg ' + who;
		if (who === 'bot') { d.innerHTML = formatAnswer(text); } else { d.textContent = text; }
		body.appendChild(d);
		body.scrollTop = body.scrollHeight;
		return d;
	}
	// One proposed show/hide change (from propose_record_status_change): a Confirm/Cancel pair.
	// Nothing changes until one of these two buttons is clicked — never from voice or typed "yes".
	function addAction(d, action){
		var card = document.createElement('div');
		card.className = 'dre-ai-action';
		var verb = action.make_visible ? 'show' : 'hide';
		card.innerHTML = '<div>Ready to <b>' + verb + '</b> &ldquo;' + escHtml(action.label) + '&rdquo;. Nothing has changed yet.</div>';
		var row = document.createElement('div');
		row.className = 'dre-ai-action-row';
		var yes = document.createElement('button');
		yes.type = 'button'; yes.className = 'dre-ai-confirm'; yes.textContent = 'Confirm';
		var no = document.createElement('button');
		no.type = 'button'; no.className = 'dre-ai-cancel'; no.textContent = 'Cancel';
		row.appendChild(yes); row.appendChild(no);
		card.appendChild(row);
		d.appendChild(card);
		function resolve(confirm){
			yes.disabled = true; no.disabled = true;
			fetch('<?= BASE_URL ?>ajax/ai-help-action.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ nonce: action.nonce, confirm: confirm, csrf: csrf })
			}).then(function(r){ return r.json(); }).then(function(r){
				card.innerHTML = '<div>' + escHtml(r.message || (confirm ? 'Done.' : 'Cancelled.')) + '</div>';
			}).catch(function(){
				card.innerHTML = '<div>Could not reach the admin panel. Please try again.</div>';
			});
		}
		yes.onclick = function(){ resolve(true); };
		no.onclick = function(){ resolve(false); };
	}
	// Appends the "Open this page" buttons, related-video links, and any pending show/hide
	// confirmations under a bot bubble. Used both for a fresh answer and when replaying the
	// saved conversation after a page reload (a still-unconfirmed action stays clickable).
	function addExtras(d, links, videos, actions){
		if (links && links.length) {
			var l = document.createElement('div');
			l.className = 'dre-ai-links';
			links.forEach(function(link){
				var a = document.createElement('a');
				a.href = link.url; a.className = 'dre-ai-pill';
				var icon = document.createElement('i'); icon.className = 'bi bi-arrow-right-circle';
				a.appendChild(icon);
				a.appendChild(document.createTextNode(' ' + link.label));
				l.appendChild(a);
			});
			d.appendChild(l);
		}
		if (videos && videos.length) {
			var v = document.createElement('div');
			v.className = 'dre-ai-videos';
			v.innerHTML = '<div>Related video' + (videos.length > 1 ? 's' : '') + ':</div>';
			videos.forEach(function(video){
				var a = document.createElement('a');
				a.href = video.url; a.target = '_blank'; a.rel = 'noopener'; a.textContent = video.title;
				v.appendChild(a);
			});
			d.appendChild(v);
		}
		(actions || []).forEach(function(action){ addAction(d, action); });
	}
	function greet(){
		if (!body.children.length) {
			add('Hi! Ask me how to use this admin panel, or ask me to show/hide a developer, community, team member or property — I will always wait for you to press Confirm first.', 'bot');
		}
	}
	// The conversation is kept in the admin's own PHP session (ai-help-chat.php), so it survives
	// a page refresh or moving to another admin page and only disappears when the admin signs out.
	(window.dreAiHistory || []).forEach(function(msg){
		var d = add(msg.text, msg.role);
		if (msg.role === 'bot') { addExtras(d, msg.links, msg.videos, msg.actions); }
	});
	btn.onclick = function(){
		panel.classList.toggle('open');
		wrap.classList.toggle('open', panel.classList.contains('open'));
		if (panel.classList.contains('open')) { greet(); input.focus(); }
	};
	close.onclick = function(){ panel.classList.remove('open'); wrap.classList.remove('open'); };
	function ask(q){
		if (!q) { return; }
		add(q, 'me');
		input.value = '';
		var thinking = add('Thinking...', 'bot');
		fetch('<?= BASE_URL ?>ajax/ai-help-chat.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ question: q, page: location.pathname.split('/').pop(), csrf: csrf })
		}).then(function(r){ return r.json(); }).then(function(r){
			thinking.remove();
			if (!r.ok) { add(r.message || 'Something went wrong. Please try again.', 'bot'); return; }
			var d = add(r.answer, 'bot');
			addExtras(d, r.links, r.videos, r.actions);
		}).catch(function(){
			thinking.remove();
			add('Could not reach the AI assistant. Please try again.', 'bot');
		});
	}
	form.onsubmit = function(e){
		e.preventDefault();
		ask(input.value.trim());
	};
	// Voice dictation: the mic fills the box and speaks for you, but a spoken "yes"/"confirm"
	// is never enough on its own to change anything — that still needs an actual click above.
	var SpeechRecognitionApi = window.SpeechRecognition || window.webkitSpeechRecognition;
	if (SpeechRecognitionApi) {
		var recognizer = new SpeechRecognitionApi();
		recognizer.lang = 'en-US';
		recognizer.interimResults = false;
		recognizer.maxAlternatives = 1;
		var listening = false;
		recognizer.onstart = function(){ listening = true; mic.classList.add('listening'); };
		recognizer.onend = function(){ listening = false; mic.classList.remove('listening'); };
		recognizer.onerror = function(){ listening = false; mic.classList.remove('listening'); };
		recognizer.onresult = function(event){
			var said = event.results[0][0].transcript;
			input.value = said;
			ask(said.trim());
		};
		mic.onclick = function(){
			if (listening) { recognizer.stop(); return; }
			panel.classList.add('open'); wrap.classList.add('open'); greet();
			try { recognizer.start(); } catch (err) { /* already starting */ }
		};
	} else {
		mic.disabled = true;
		mic.title = 'Voice input is not supported in this browser';
	}
})();
</script>
<!--end::Ask AI help widget-->

</body>
<!--end::Body-->
</html>