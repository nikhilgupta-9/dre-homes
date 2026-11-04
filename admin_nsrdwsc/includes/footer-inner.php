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
#dreAiBtnWrap{position:fixed;right:24px;bottom:24px;z-index:1090;font-family:Poppins,Helvetica,sans-serif}
#dreAiBtnWrap::before{content:'';position:absolute;inset:-8px;border-radius:50%;background:linear-gradient(135deg,#d4af37,#ab9889,#5f574c);opacity:.55;filter:blur(12px);animation:dreAiGlow 2.6s ease-in-out infinite}
#dreAiRing1,#dreAiRing2{position:absolute;inset:0;border-radius:50%;border:2px solid #d4af37;pointer-events:none;animation:dreAiPing 2.4s cubic-bezier(.3,.6,.3,1) infinite}
#dreAiRing2{animation-delay:1.2s}
#dreAiBtn{position:relative;width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#6b6153,#ab9889 55%,#d4af37);color:#fff;border:0;box-shadow:0 10px 26px rgba(95,87,76,.55),0 0 0 3px rgba(212,175,55,.35);font-size:26px;display:flex;align-items:center;justify-content:center;cursor:pointer;animation:dreAiFloat 2.4s ease-in-out infinite;transition:transform .2s,box-shadow .2s}
#dreAiBtn:hover{transform:scale(1.1);box-shadow:0 12px 30px rgba(95,87,76,.65),0 0 0 4px rgba(212,175,55,.5)}
#dreAiBtn i{position:relative;z-index:1;animation:dreAiTwinkle 2.4s ease-in-out infinite}
#dreAiBadge{position:absolute;top:-6px;right:-6px;background:linear-gradient(135deg,#d4af37,#b9524a);color:#fff;font-size:10px;font-weight:800;letter-spacing:.3px;line-height:1;padding:4px 7px;border-radius:999px;border:2px solid #fff;z-index:2;box-shadow:0 2px 6px rgba(0,0,0,.25);animation:dreAiPop 1.6s ease-in-out infinite}
#dreAiLabel{position:absolute;right:76px;bottom:18px;background:#2f2a25;color:#fff;font-size:13px;font-weight:700;padding:9px 14px;border-radius:999px;white-space:nowrap;box-shadow:0 8px 20px rgba(0,0,0,.28);opacity:0;transform:translateX(10px) scale(.92);transition:opacity .25s,transform .25s;pointer-events:none}
#dreAiLabel::after{content:'';position:absolute;right:-5px;top:50%;transform:translateY(-50%) rotate(45deg);width:9px;height:9px;background:#2f2a25}
#dreAiBtnWrap:hover #dreAiLabel,#dreAiBtnWrap.show-label #dreAiLabel{opacity:1;transform:translateX(0) scale(1)}
#dreAiBtnWrap.open #dreAiLabel,#dreAiBtnWrap.open #dreAiBadge,#dreAiBtnWrap.open #dreAiRing1,#dreAiBtnWrap.open #dreAiRing2{display:none}
@keyframes dreAiFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
@keyframes dreAiGlow{0%,100%{opacity:.4;transform:scale(1)}50%{opacity:.75;transform:scale(1.12)}}
@keyframes dreAiPop{0%,100%{transform:scale(1)}50%{transform:scale(1.22)}}
@keyframes dreAiPing{0%{transform:scale(1);opacity:.65}100%{transform:scale(1.55);opacity:0}}
@keyframes dreAiTwinkle{0%,100%{transform:rotate(0) scale(1)}50%{transform:rotate(12deg) scale(1.15)}}
#dreAiPanel{position:fixed;right:24px;bottom:98px;z-index:1090;width:400px;max-width:calc(100vw - 32px);height:640px;max-height:calc(100vh - 140px);background:#fdfbf9;border-radius:20px;box-shadow:0 20px 50px rgba(47,42,37,.3),0 0 0 1px rgba(212,175,55,.15);display:none;flex-direction:column;overflow:hidden;font-family:Poppins,Helvetica,sans-serif;border:1px solid rgba(171,152,137,.3);opacity:0;transform:translateY(14px) scale(.97);transition:opacity .22s ease,transform .22s ease}
#dreAiPanel.open{display:flex;opacity:1;transform:translateY(0) scale(1)}
#dreAiPanel .dre-ai-head{position:relative;overflow:hidden;background:linear-gradient(135deg,#5f574c,#ab9889 60%,#d4af37);color:#fff;padding:16px 16px 14px;display:flex;align-items:center;justify-content:space-between}
#dreAiPanel .dre-ai-head::after{content:'';position:absolute;inset:0;background:radial-gradient(120px 60px at 85% 0%,rgba(255,255,255,.35),transparent 70%)}
#dreAiPanel .dre-ai-head-l{position:relative;z-index:1;display:flex;align-items:center;gap:10px}
#dreAiPanel .dre-ai-av{width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,.22);border:1px solid rgba(255,255,255,.4);display:flex;align-items:center;justify-content:center;font-size:16px;flex:none}
#dreAiPanel .dre-ai-head b{font-size:14.5px;font-weight:700;display:block}
#dreAiPanel .dre-ai-head small{font-size:11px;font-weight:500;opacity:.88;display:flex;align-items:center;gap:4px}
#dreAiPanel .dre-ai-head small::before{content:'';width:7px;height:7px;border-radius:50%;background:#58e6a8;box-shadow:0 0 0 2px rgba(255,255,255,.4)}
#dreAiPanel .dre-ai-head button{position:relative;z-index:1;background:rgba(255,255,255,.14);border:0;color:#fff;font-size:17px;width:28px;height:28px;border-radius:50%;cursor:pointer;line-height:1;transition:background .15s}
#dreAiPanel .dre-ai-head button:hover{background:rgba(255,255,255,.28)}
#dreAiBody{flex:1;overflow-y:auto;padding:16px 14px;background:linear-gradient(180deg,#f6f1ea,#f0e9e0);font-size:13.5px;color:#2f2a25;scroll-behavior:smooth}
#dreAiBody .dre-ai-row{display:flex;align-items:flex-end;gap:8px;margin-bottom:14px}
#dreAiBody .dre-ai-row.me{flex-direction:row-reverse}
#dreAiBody .dre-ai-ava{width:26px;height:26px;border-radius:50%;flex:none;display:flex;align-items:center;justify-content:center;font-size:12px;color:#fff;background:linear-gradient(135deg,#5f574c,#ab9889)}
#dreAiBody .dre-ai-row.me .dre-ai-ava{background:linear-gradient(135deg,#d4af37,#ab9889)}
#dreAiBody .dre-ai-msg{padding:10px 13px;border-radius:16px;max-width:78%;line-height:1.48;box-shadow:0 2px 8px rgba(73,62,50,.06)}
#dreAiBody .dre-ai-msg.me{background:linear-gradient(135deg,#6b6153,#5f574c);color:#fff;border-bottom-right-radius:5px}
#dreAiBody .dre-ai-msg.bot{background:#fff;border:1px solid rgba(171,152,137,.28);color:#2f2a25;border-bottom-left-radius:5px}
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
/* Typing dots shown while waiting for a reply */
#dreAiBody .dre-ai-typing{display:inline-flex;gap:3px;padding:4px 2px}
#dreAiBody .dre-ai-typing i{width:6px;height:6px;border-radius:50%;background:#ab9889;animation:dreAiTypingDot 1.1s ease-in-out infinite}
#dreAiBody .dre-ai-typing i:nth-child(2){animation-delay:.15s}
#dreAiBody .dre-ai-typing i:nth-child(3){animation-delay:.3s}
@keyframes dreAiTypingDot{0%,60%,100%{transform:translateY(0);opacity:.5}30%{transform:translateY(-4px);opacity:1}}
/* Suggestions dropdown ("as you type", like a hosting-panel search box) */
#dreAiSugWrap{position:relative;flex:1}
#dreAiSug{position:absolute;left:0;right:0;bottom:calc(100% + 8px);background:#fff;border-radius:14px;box-shadow:0 10px 28px rgba(47,42,37,.22);border:1px solid rgba(171,152,137,.3);overflow:hidden;display:none;max-height:220px;overflow-y:auto}
#dreAiSug.show{display:block}
#dreAiSug button{display:flex;flex-direction:column;align-items:flex-start;gap:1px;width:100%;text-align:left;border:0;background:none;padding:9px 13px;font-size:12.5px;color:#2f2a25;cursor:pointer;border-bottom:1px solid rgba(171,152,137,.18)}
#dreAiSug button:last-child{border-bottom:0}
#dreAiSug button:hover,#dreAiSug button.hi{background:rgba(212,175,55,.14)}
#dreAiSug button b{font-weight:600;font-size:12.5px}
#dreAiSug button span{font-size:11px;color:var(--dd-muted,#7c7268)}
#dreAiForm{position:relative;display:flex;align-items:center;gap:6px;border-top:1px solid rgba(171,152,137,.3);padding:10px;background:#fdfbf9}
#dreAiForm input{flex:1;border:1px solid rgba(171,152,137,.4);border-radius:999px;padding:9px 14px;font-size:13.5px;font-family:inherit;min-width:0}
#dreAiForm input:focus{outline:0;border-color:#ab9889;box-shadow:0 0 0 3px rgba(171,152,137,.18)}
#dreAiLang{border:1px solid rgba(171,152,137,.4);border-radius:999px;background:#fff;font-size:11px;font-weight:600;color:#5f574c;padding:0 4px;height:30px;flex:none;width:52px}
#dreAiForm button{background:#5f574c;color:#fff;border:0;border-radius:50%;width:34px;height:34px;cursor:pointer;flex:none;display:flex;align-items:center;justify-content:center;transition:background .15s,transform .15s}
#dreAiForm button:hover{background:#4a433a;transform:scale(1.05)}
#dreAiMic.listening{background:#b9524a;animation:dreAiPop 1s ease-in-out infinite}
</style>
<div id="dreAiBtnWrap">
	<span id="dreAiLabel">Ask AI &#10024;</span>
	<span id="dreAiRing1" aria-hidden="true"></span>
	<span id="dreAiRing2" aria-hidden="true"></span>
	<button id="dreAiBtn" aria-label="Ask AI" title="Ask AI"><i class="bi bi-stars"></i></button>
	<span id="dreAiBadge">AI</span>
</div>
<div id="dreAiPanel" role="dialog" aria-label="Ask AI">
	<div class="dre-ai-head">
		<div class="dre-ai-head-l">
			<span class="dre-ai-av"><i class="bi bi-robot"></i></span>
			<span><b>Ask AI</b><small>Online &mdash; speaks any language</small></span>
		</div>
		<button id="dreAiClose" aria-label="Close">&times;</button>
	</div>
	<div id="dreAiBody"></div>
	<form id="dreAiForm" autocomplete="off">
		<div id="dreAiSugWrap">
			<div id="dreAiSug" role="listbox"></div>
			<input type="text" id="dreAiInput" maxlength="500" autocomplete="off" placeholder="Ask in any language, or tap the mic to speak...">
		</div>
		<select id="dreAiLang" title="Voice language" aria-label="Voice language">
			<option value="auto">Auto</option>
			<option value="en-US">EN</option>
			<option value="hi-IN">HI</option>
			<option value="ar-AE">AR</option>
			<option value="ur-PK">UR</option>
		</select>
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
		mic = document.getElementById('dreAiMic'), lang = document.getElementById('dreAiLang'),
		sug = document.getElementById('dreAiSug');
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
		var row = document.createElement('div');
		row.className = 'dre-ai-row ' + who;
		var ava = document.createElement('span');
		ava.className = 'dre-ai-ava';
		ava.innerHTML = who === 'bot' ? '<i class="bi bi-robot"></i>' : '<i class="bi bi-person-fill"></i>';
		var d = document.createElement('div');
		d.className = 'dre-ai-msg ' + who;
		if (who === 'bot') { d.innerHTML = formatAnswer(text); } else { d.textContent = text; }
		row.appendChild(ava); row.appendChild(d);
		body.appendChild(row);
		body.scrollTop = body.scrollHeight;
		return d;
	}
	// Three bouncing dots while waiting for the AI's reply, instead of a plain "Thinking..." line.
	function addTyping(){
		var d = add('', 'bot');
		d.innerHTML = '<span class="dre-ai-typing"><i></i><i></i><i></i></span>';
		return d.closest('.dre-ai-row');
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
		wrap.classList.remove('show-label');
		if (panel.classList.contains('open')) { greet(); input.focus(); }
	};
	close.onclick = function(){ panel.classList.remove('open'); wrap.classList.remove('open'); };
	// First time this tab sees the widget, pop the "Ask AI" label open on its own for a
	// few seconds so admins notice it, instead of only showing it on hover.
	try {
		if (!sessionStorage.getItem('dreAiLabelShown')) {
			sessionStorage.setItem('dreAiLabelShown', '1');
			setTimeout(function(){ wrap.classList.add('show-label'); }, 700);
			setTimeout(function(){ wrap.classList.remove('show-label'); }, 6000);
		}
	} catch (e) {}
	function ask(q){
		if (!q) { return; }
		add(q, 'me');
		input.value = '';
		var thinking = addTyping();
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
		hideSug();
		ask(input.value.trim());
	};

	// Suggestions, "as you type" (ai-help-suggest.php) — click one to ask it straight away.
	var sugTimer = null, sugItems = [], sugHi = -1;
	function hideSug(){ sug.classList.remove('show'); sug.innerHTML = ''; sugItems = []; sugHi = -1; }
	function renderSug(){
		sug.innerHTML = '';
		sugItems.forEach(function(it, i){
			var b = document.createElement('button');
			b.type = 'button';
			b.className = i === sugHi ? 'hi' : '';
			b.innerHTML = '<b>' + escHtml(it.title) + '</b><span>' + escHtml(it.label) + '</span>';
			b.onclick = function(){ hideSug(); ask(it.title); };
			sug.appendChild(b);
		});
		sug.classList.toggle('show', sugItems.length > 0);
	}
	input.addEventListener('input', function(){
		var q = input.value.trim();
		clearTimeout(sugTimer);
		if (q.length < 2) { hideSug(); return; }
		sugTimer = setTimeout(function(){
			fetch('<?= BASE_URL ?>ajax/ai-help-suggest.php?q=' + encodeURIComponent(q))
				.then(function(r){ return r.json(); })
				.then(function(r){
					sugItems = (r && r.ok && r.items) || [];
					sugHi = -1;
					renderSug();
				})
				.catch(function(){});
		}, 220);
	});
	input.addEventListener('keydown', function(e){
		if (!sug.classList.contains('show')) { return; }
		if (e.key === 'ArrowDown') { e.preventDefault(); sugHi = Math.min(sugHi + 1, sugItems.length - 1); renderSug(); }
		else if (e.key === 'ArrowUp') { e.preventDefault(); sugHi = Math.max(sugHi - 1, 0); renderSug(); }
		else if (e.key === 'Escape') { hideSug(); }
		else if (e.key === 'Enter' && sugHi > -1) { e.preventDefault(); var it = sugItems[sugHi]; hideSug(); ask(it.title); }
	});
	document.addEventListener('click', function(e){
		if (!sug.contains(e.target) && e.target !== input) { hideSug(); }
	});

	// Voice dictation: the mic fills the box and speaks for you, but a spoken "yes"/"confirm"
	// is never enough on its own to change anything — that still needs an actual click above.
	// Language: "Auto" follows the browser/OS language; EN/HI/AR/UR force dictation in that
	// language specifically (the Web Speech API needs one explicit language per recording, it
	// cannot detect the spoken language on its own) — the AI itself always replies in whatever
	// language the question turns out to be in, regardless of which one was picked here.
	try { lang.value = localStorage.getItem('dreAiLang') || 'auto'; } catch (e) {}
	lang.onchange = function(){ try { localStorage.setItem('dreAiLang', lang.value); } catch (e) {} };
	var SpeechRecognitionApi = window.SpeechRecognition || window.webkitSpeechRecognition;
	if (SpeechRecognitionApi) {
		var recognizer = new SpeechRecognitionApi();
		recognizer.interimResults = true;
		recognizer.maxAlternatives = 1;
		var listening = false;
		recognizer.onstart = function(){ listening = true; mic.classList.add('listening'); };
		recognizer.onend = function(){ listening = false; mic.classList.remove('listening'); };
		recognizer.onerror = function(){ listening = false; mic.classList.remove('listening'); };
		recognizer.onresult = function(event){
			var said = event.results[0][0].transcript;
			input.value = said;
			if (event.results[0].isFinal) { ask(said.trim()); }
		};
		mic.onclick = function(){
			if (listening) { recognizer.stop(); return; }
			recognizer.lang = (lang.value === 'auto' ? (navigator.language || 'en-US') : lang.value);
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