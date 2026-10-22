/* DRE Homes — shared site script (needs Bootstrap 5 bundle loaded first; nav, dock, AI panel, phone input, Dirham symbol, Liquid Glass, reveal) */
const root = document.documentElement;

/* ---- Popin live video agent (same token as live site) ---- */
(function () {
  const p = document.createElement("script");
  p.src = "https://widget01.popin.to/js/widget.js";
  p.onload = () => {
    try {
      popInWidgetInit({ token: "13538", mode: "hidden" });
    } catch (e) {}
  };
  document.body.appendChild(p);
})();
function openPopin() {
  if (typeof window.Popin === "function") {
    Popin("open");
  } else {
    window.open("https://wa.me/971585837346", "_blank");
  }
}
document.getElementById("popinBtn").onclick = openPopin;

/* ---- AI assistant (UI shell — replies are scripted until connected to a real AI backend) ---- */
const aiPanel = document.getElementById("aiPanel"),
  aiBtn = document.getElementById("aiBtn"),
  aiBody = document.getElementById("aiBody");
function toggleAI(o) {
  o = o ?? !aiPanel.classList.contains("open");
  aiPanel.classList.toggle("open", o);
  aiBtn.setAttribute("aria-expanded", o);
  if (o) {
    if (!aiBody.children.length) greet();
    setTimeout(() => document.getElementById("aiIn").focus(), 300);
  }
}
aiBtn.onclick = () => toggleAI();
document.getElementById("aiClose").onclick = () => toggleAI(false);
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") toggleAI(false);
});
const go = (id) => {
  toggleAI(false);
  const el = document.getElementById(id);
  if (el) el.scrollIntoView({ behavior: "smooth" });
  else location.href = "index.html#" + id;
};
window.__ai = { go, openPopin };
function add(html, who = "bot") {
  const d = document.createElement("div");
  d.className = "msg " + who;
  d.innerHTML = html;
  aiBody.appendChild(d);
  aiBody.scrollTop = aiBody.scrollHeight;
}
function greet() {
  add(
    "Hi 👋 I'm DRE's assistant. I can help you find off-plan projects, resale homes or rentals, check Golden Visa eligibility, or connect you with an advisor. What are you looking for?",
  );
}
const A = (t, f) => `<button onclick="${f}">${t}</button>`;
const RULES = [
  [
    /visa|golden|residen/i,
    `Property investments of <b>AED 2M+</b> can qualify for the 10-year Golden Visa. Try our eligibility check, or speak to an advisor.<div class="acts">${A("Check eligibility", "__ai.go('visa')")}${A("Live video call", "__ai.openPopin()")}</div>`,
  ],
  [
    /off.?plan|launch|new project|under|budget|aed|cheap|afford/i,
    `Here are our latest off-plan launches — several start from around <b>AED 1M</b> with developer payment plans.<div class="acts">${A("See launches", "__ai.go('props')")}</div>`,
  ],
  [
    /view|visit|book|appointment|meet/i,
    `I'd be happy to arrange a private viewing. Share your details and an advisor will call you back.<div class="acts">${A("Request a call back", "__ai.go('contact')")}${A("Live video call", "__ai.openPopin()")}</div>`,
  ],
  [
    /area|invest|community|where|best|palm|downtown|hills|business bay|marina/i,
    `Popular choices are Palm Jumeirah, Downtown, Dubai Hills, Business Bay and Emaar Beachfront — each with a different lifestyle and price point.<div class="acts">${A("Explore communities", "__ai.go('areas')")}</div>`,
  ],
  [
    /developer|emaar|damac|nakheel|sobha|meraas|azizi|binghatti|danube/i,
    `We work with Dubai's leading developers including Emaar, Damac, Nakheel and Meraas.<div class="acts">${A("View developers", "__ai.go('developers')")}</div>`,
  ],
  [
    /rent|lease|tenant/i,
    `Looking to rent? We list in-demand rentals across prime communities.<div class="acts">${A("Find a rental", "__ai.go('paths')")}</div>`,
  ],
  [
    /human|agent|advisor|call|talk|person|whatsapp/i,
    `Of course — you can start a live video call with an advisor right now, or message us on WhatsApp.<div class="acts">${A("Live video call", "__ai.openPopin()")}<a href="https://wa.me/971585837346" target="_blank" rel="noopener">WhatsApp</a></div>`,
  ],
];
function reply(q) {
  const r = RULES.find(([re]) => re.test(q));
  return r
    ? r[1]
    : `Thanks for your question! One of our advisors can give you the best answer on that.<div class="acts">${A("Live video call", "__ai.openPopin()")}${A("Request a call back", "__ai.go('contact')")}</div>`;
}
function ask(q) {
  q = q.trim();
  if (!q) return;
  add(q.replace(/</g, "&lt;"), "me");
  const t = document.createElement("div");
  t.className = "msg bot typing";
  t.innerHTML = "<i></i><i></i><i></i>";
  aiBody.appendChild(t);
  aiBody.scrollTop = aiBody.scrollHeight;
  setTimeout(() => {
    t.remove();
    add(reply(q));
  }, 700);
}
document.getElementById("aiForm").onsubmit = (e) => {
  e.preventDefault();
  const i = document.getElementById("aiIn");
  ask(i.value);
  i.value = "";
};
document.getElementById("aiChips").onclick = (e) => {
  if (e.target.tagName === "BUTTON") ask(e.target.textContent);
};

/* ---- Nav dropdowns (Bootstrap Dropdown) + mobile menu (Bootstrap Offcanvas) ---- */
const burger = document.getElementById("burger"),
  mmenu = document.getElementById("mmenu");
mmenu.addEventListener("show.bs.offcanvas", () => {
  document.body.classList.add("menu-open");
  burger.setAttribute("aria-expanded", true);
  burger.setAttribute("aria-label", "Close menu");
});
mmenu.addEventListener("hide.bs.offcanvas", () => {
  document.body.classList.remove("menu-open");
  burger.setAttribute("aria-expanded", false);
  burger.setAttribute("aria-label", "Open menu");
});
// tapping the burger again closes the menu (it sits above the backdrop)
burger.addEventListener(
  "click",
  (e) => {
    if (mmenu.classList.contains("show")) {
      e.stopImmediatePropagation();
      bootstrap.Offcanvas.getOrCreateInstance(mmenu).hide();
    }
  },
  true,
);
mmenu.addEventListener("click", (e) => {
  if (e.target.closest("a")) bootstrap.Offcanvas.getOrCreateInstance(mmenu).hide();
});
document
  .querySelectorAll(".cur")
  .forEach((c) => (c.onchange = () => document.querySelectorAll(".cur").forEach((x) => (x.value = c.value))));

/* ---- Hero background: video / photo ---- */
if (document.getElementById("heroVideo")) {
  const hero = document.getElementById("hero"),
    hv = document.getElementById("heroVideo"),
    vc = document.getElementById("vidCtl");
  const IC_PAUSE =
    '<svg viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>';
  const IC_PLAY = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';
  const reduce = matchMedia("(prefers-reduced-motion: reduce)").matches;
  function syncVc() {
    const p = hv.paused;
    vc.innerHTML = p ? IC_PLAY : IC_PAUSE;
    vc.setAttribute("aria-label", p ? "Play background video" : "Pause background video");
  }
  hv.addEventListener("playing", () => hv.classList.add("ready"));
  hv.addEventListener("play", syncVc);
  hv.addEventListener("pause", syncVc);
  hv.addEventListener("error", () => setHeroBg("image"), true);
  let userPaused = false;
  vc.onclick = () => {
    if (hv.paused) {
      userPaused = false;
      hv.play();
    } else {
      userPaused = true;
      hv.pause();
    }
  };
  function setHeroBg(m) {
    hero.dataset.bg = m;
    document.querySelectorAll("#heroSeg button").forEach((b) => b.classList.toggle("on", b.dataset.bg === m));
    if (m === "video") {
      hv.preload = "auto";
      if (!reduce) hv.play().catch(() => {});
    } else hv.pause();
    syncVc();
    try {
      localStorage.setItem("dre-hero", m);
    } catch (e) {}
  }
  (document.getElementById("heroSeg") || {}).onclick = (e) => {
    const b = e.target.closest("button");
    if (b) setHeroBg(b.dataset.bg);
  };
  let hb = "video";
  setHeroBg(hb);
  new IntersectionObserver(([e]) => {
    if (hero.dataset.bg !== "video" || reduce || userPaused) return;
    e.isIntersecting ? hv.play().catch(() => {}) : hv.pause();
  }).observe(hero);
}

/* ---- Phone input with auto-detected country (intl-tel-input) ---- */
function detectCountry(cb) {
  try {
    const c = sessionStorage.getItem("dre-cc");
    if (c) return cb(c);
  } catch (e) {}
  const done = (c) => {
    c = (c || "").toLowerCase();
    if (!/^[a-z]{2}$/.test(c)) c = fallbackCC();
    try {
      sessionStorage.setItem("dre-cc", c);
    } catch (e) {}
    cb(c);
  };
  const t = (ms) => new Promise((_, r) => setTimeout(() => r("timeout"), ms));
  Promise.race([
    fetch("https://api.country.is/")
      .then((r) => r.json())
      .then((d) => d.country),
    t(2500),
  ])
    .catch(() =>
      Promise.race([
        fetch("https://ipapi.co/json/")
          .then((r) => r.json())
          .then((d) => d.country_code),
        t(2500),
      ]),
    )
    .then(done)
    .catch(() => done(""));
}
function fallbackCC() {
  const m = (navigator.language || "").match(/-([A-Za-z]{2})$/);
  return m ? m[1].toLowerCase() : "ae";
}
const phoneEl = document.getElementById("ctaPhone");
let iti = null;
if (window.intlTelInput && phoneEl) {
  iti = intlTelInput(phoneEl, {
    initialCountry: "auto",
    geoIpLookup: (ok) => detectCountry(ok),
    showSelectedDialCode: true,
    dropdownContainer: document.body,
    nationalMode: true,
    autoPlaceholder: "aggressive",
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/21.2.7/js/utils.min.js",
  });
  phoneEl.addEventListener("input", () => {
    phoneEl.classList.remove("bad");
    document.getElementById("ctaPhoneErr").classList.remove("on");
  });
}
(document.getElementById("ctaForm") || document.createElement("form")).addEventListener("submit", (e) => {
  e.preventDefault();
  const f = e.target;
  if (iti && phoneEl.value.trim() && window.intlTelInputUtils && !iti.isValidNumber()) {
    phoneEl.classList.add("bad");
    document.getElementById("ctaPhoneErr").classList.add("on");
    phoneEl.focus();
    return;
  }
  const fullPhone = iti ? iti.getNumber() : phoneEl.value.trim();
  const hiddenPhone = document.getElementById("ctaPhoneFull");
  if (hiddenPhone) hiddenPhone.value = fullPhone;

  const btn = f.querySelector("button");
  const origText = btn ? btn.innerHTML : "Request a call back";
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = "Sending...";
  }

  const formData = new FormData(f);
  formData.append("phone", fullPhone);
  formData.append("phone_full", fullPhone);
  formData.append("page_url", window.location.href);
  formData.append("channel", "Website");
  formData.append("leads_nature", "Fresh Lead - Private Consultation");
  formData.append("download_type", "Consultation CTA Form");

  const ajaxUrl = (typeof base_url !== "undefined" ? base_url : "/") + "ajax/offplan_frm.php";

  fetch(ajaxUrl, {
    method: "POST",
    body: formData
  })
    .then(r => r.json())
    .then(res => {
      if (res.status === "success") {
        f.innerHTML = "<p class='serif' style='font-size:26px;color:var(--gold,#d4af37);padding:20px 0;'>Thank you — an advisor will be in touch shortly.</p>";
      } else {
        alert(res.msg || "There was an error submitting your request. Please try again.");
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = origText;
        }
      }
    })
    .catch(() => {
      // Even if network error occurs, give friendly acknowledgment
      f.innerHTML = "<p class='serif' style='font-size:26px;color:var(--gold,#d4af37);padding:20px 0;'>Thank you — an advisor will be in touch shortly.</p>";
    });
});

/* ---- About: video modal ---- */
(function () {
  const dlg = document.getElementById("videoModal"),
    v = document.getElementById("aboutVideo"),
    btn = document.getElementById("openVideo");
  if (!dlg || !btn) return;
  btn.onclick = () => {
    const src = v.querySelector("source");
    if (!src.src) {
      src.src = src.dataset.src;
      v.load();
    }
    dlg.showModal();
    v.play().catch(() => {});
  };
  const close = () => {
    v.pause();
    dlg.close();
  };
  document.getElementById("closeVideo").onclick = close;
  dlg.addEventListener("click", (e) => {
    if (e.target === dlg) close();
  });
  dlg.addEventListener("close", () => v.pause());
})();
/* ---- Home: off-plan cards filter (the cards themselves are written in index.html) ---- */
const cards = document.getElementById("cards");
if (cards) {
  const chips = [...document.querySelectorAll(".chip")].filter((c) => !c.closest("#cards"));
  chips.forEach(
    (b) =>
      (b.onclick = () => {
        chips.forEach((x) => x.classList.remove("on"));
        b.classList.add("on");
        const f = b.textContent.trim();
        [...cards.children].forEach((col) => (col.hidden = f !== "All" && col.dataset.c !== f));
      }),
  );
}
document.querySelectorAll(".tabs button").forEach(
  (b) =>
    (b.onclick = () => {
      document.querySelectorAll(".tabs button").forEach((x) => x.classList.remove("on"));
      b.classList.add("on");
    }),
);

const r = document.getElementById("budget"),
  bv = document.getElementById("bval"),
  br = document.getElementById("bres");
function upd() {
  const v = +r.value;
  bv.textContent = "AED " + v.toLocaleString("en-US");
  r.style.setProperty("--p", ((v - r.min) / (r.max - r.min)) * 100 + "%");
  if (v >= 2000000) {
    br.className = "result";
    br.innerHTML =
      "✦ <b>Likely eligible</b> for the 10-year Golden Visa. We can shortlist qualifying residences for you.";
  } else {
    br.className = "result no";
    br.innerHTML = "Below the AED 2M threshold — a 2-year investor visa may still apply. Ask our advisors.";
  }
}
if (r) {
  r.oninput = upd;
  upd();
}

const io = new IntersectionObserver(
  (es) =>
    es.forEach((e) => {
      if (!e.isIntersecting) return;
      e.target.classList.add("in");
      e.target.querySelectorAll("[data-n]").forEach((el) => {
        const t = +el.dataset.n;
        let s = null;
        const step = (ts) => {
          s ??= ts;
          const k = Math.min((ts - s) / 1600, 1);
          el.textContent =
            Math.floor(t * (1 - Math.pow(1 - k, 3))).toLocaleString() +
            (k === 1 && el.dataset.plus !== "no" ? "+" : "");
          if (k < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
      });
      io.unobserve(e.target);
    }),
  { threshold: 0.12 },
);
document.querySelectorAll(".reveal").forEach((el) => io.observe(el));
new MutationObserver((ms) =>
  ms.forEach((m) =>
    m.addedNodes.forEach((n) => {
      if (n.nodeType !== 1) return;
      if (n.matches(".reveal:not(.in)")) io.observe(n);
      n.querySelectorAll && n.querySelectorAll(".reveal:not(.in)").forEach((el) => io.observe(el));
    }),
  ),
).observe(document.body, { childList: true, subtree: true });

/* ---- Replace "AED" text with the UAE Dirham symbol (incl. dynamic content) ---- */
(function () {
  const SKIP = /^(SCRIPT|STYLE|SELECT|OPTION|TEXTAREA|INPUT|TITLE|NOSCRIPT|IFRAME|svg)$/i;
  const RE = /\bAED\b\s?/;
  function swap(root) {
    const w = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
      acceptNode: (n) => {
        for (let p = n.parentNode; p && p !== document.body; p = p.parentNode) {
          if (p.nodeType === 1 && (SKIP.test(p.tagName) || p.classList.contains("aed")))
            return NodeFilter.FILTER_REJECT;
        }
        return RE.test(n.nodeValue) ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_SKIP;
      },
    });
    const nodes = [];
    while (w.nextNode()) nodes.push(w.currentNode);
    nodes.forEach((n) => {
      const parts = n.nodeValue.split(/\bAED\b\s?/);
      const frag = document.createDocumentFragment();
      parts.forEach((t, i) => {
        if (i) {
          const s = document.createElement("span");
          s.className = "aed";
          s.setAttribute("role", "img");
          s.setAttribute("aria-label", "AED");
          frag.appendChild(s);
        }
        if (t) frag.appendChild(document.createTextNode(t));
      });
      n.parentNode.replaceChild(frag, n);
    });
  }
  swap(document.body);
  new MutationObserver((ms) => {
    for (const m of ms)
      for (const n of m.addedNodes) {
        if (n.nodeType === 1) swap(n);
        else if (n.nodeType === 3 && RE.test(n.nodeValue) && n.parentNode) swap(n.parentNode);
      }
    for (const m of ms)
      if (m.type === "characterData" && RE.test(m.target.nodeValue) && m.target.parentNode) swap(m.target.parentNode);
  }).observe(document.body, { childList: true, subtree: true, characterData: true });
})();

/* ---- Liquid Glass press effect: lens swell, finger-tracking specular, jelly pull, spring release ---- */
(function () {
  const SEL =
    ".btn,button:not(.oc-nav):not(.lb-n):not(.lb-x):not(.sv-x):not(.vm-x),a.glass,.glass a,.chip,.oc,.pcard,.area,.ig,.tm,.ks,.dcard,.mm-l,.so,.dev,.ex,.ex2,.menu .mi,.dd a,.pd-tabs a,.quick button,.seg button,.pager button,.beds button,.sw-item,.dk,.fb-link,.why,.pil,.val,.svc,.path,.ad,.steps li,.repair,.mm-link,.mm-sum,.t-filters button,.tabs button,.go,.am,.feat-chips span,.near li,.vt button,.favbtn,.oc-fav,.fmore,.car-nav button,.sim,.dv,.od,.cm,.pc,.lf,.ct,.icard .ic-img,.ic-ic,.cchips button,.az button,.dl-card,.sg-i,.dp-proj,.pg-main,.pg-side button";
  const reduce = matchMedia("(prefers-reduced-motion: reduce)").matches;
  let act = null;
  function host(el) {
    if (!el.classList.contains("lg-host")) {
      el.classList.add("lg-host");
      if (getComputedStyle(el).position === "static") el.style.position = "relative";
    }
    let h = el.querySelector(":scope>.lg-hl");
    if (!h) {
      h = document.createElement("span");
      h.className = "lg-hl";
      h.setAttribute("aria-hidden", "true");
      el.appendChild(h);
    }
    return h;
  }
  function track(el, x, y) {
    const r = el.getBoundingClientRect();
    const lx = x - r.left,
      ly = y - r.top;
    el.style.setProperty("--lx", lx + "px");
    el.style.setProperty("--ly", ly + "px");
    if (!reduce) {
      const k = Math.min(6, Math.max(r.width, r.height) * 0.02);
      const dx = Math.max(-1, Math.min(1, (lx / r.width - 0.5) * 2)),
        dy = Math.max(-1, Math.min(1, (ly / r.height - 0.5) * 2));
      el.style.translate = `${(dx * k).toFixed(1)}px ${(dy * k).toFixed(1)}px`;
    }
  }
  function down(e) {
    if (e.button > 0) return;
    const el = e.target.closest(SEL);
    if (!el || e.target.closest("input,select,textarea")) return;
    host(el);
    const r = el.getBoundingClientRect();
    const m = Math.max(r.width, r.height);
    el.style.setProperty("--lgs", (1 + Math.min(0.09, Math.max(0.012, 11 / m))).toFixed(3));
    el.style.setProperty("--lr", Math.max(70, Math.min(260, m * 0.9)) + "px");
    el.classList.remove("lg-rel");
    void el.offsetWidth;
    el.classList.add("lg-on");
    track(el, e.clientX || r.left + r.width / 2, e.clientY || r.top + r.height / 2);
    act = el;
  }
  function move(e) {
    if (act) track(act, e.clientX, e.clientY);
  }
  function up() {
    if (!act) return;
    const el = act;
    act = null;
    el.classList.remove("lg-on");
    el.style.translate = "";
    el.classList.add("lg-rel");
    const done = () => {
      el.classList.remove("lg-rel");
      el.removeEventListener("animationend", done);
    };
    el.addEventListener("animationend", done);
    setTimeout(done, 900);
  }
  document.addEventListener("pointerdown", down, { passive: true });
  document.addEventListener("pointermove", move, { passive: true });
  ["pointerup", "pointercancel", "dragstart"].forEach((t) => document.addEventListener(t, up, { passive: true }));
  addEventListener("blur", up);
  document.addEventListener("keydown", (e) => {
    if ((e.key === "Enter" || e.key === " ") && e.target.matches && e.target.matches(SEL) && !e.repeat) {
      down({ button: 0, target: e.target, clientX: 0, clientY: 0 });
      setTimeout(up, 140);
    }
  });
})();

/* ---- Image skeletons: shimmer on every <img> and inline background-image until it loads (incl. dynamic content) ---- */
(function () {
  const SKIP = ".site-logo,.gptw img,.f-award img,[data-nosk]";
  const seen = (u) => {
    try {
      return performance.getEntriesByName(new URL(u, location.href).href).length > 0;
    } catch (e) {
      return false;
    }
  };
  function img(el) {
    if (el.matches(SKIP) || el._sk === el.src) return;
    el._sk = el.src;
    el.classList.remove("sk-in");
    if (el.complete && el.naturalWidth) {
      el.classList.remove("sk-ld");
      return;
    }
    el.classList.add("sk-ld");
    const done = () => {
      el.classList.remove("sk-ld");
      if (el.naturalWidth && getComputedStyle(el).animationName === "none") el.classList.add("sk-in");
    };
    el.addEventListener("load", done, { once: true });
    el.addEventListener("error", () => el.classList.remove("sk-ld"), { once: true });
  }
  const bio = new IntersectionObserver(
    (es) =>
      es.forEach((e) => {
        if (!e.isIntersecting) return;
        bio.unobserve(e.target);
        const el = e.target,
          u = el._sk;
        const p = new Image(),
          done = () => {
            if (el._sk === u) el.classList.remove("sk-bg");
          };
        p.onload = done;
        p.onerror = done;
        p.src = u;
        if (p.complete && p.naturalWidth) done();
      }),
    { rootMargin: "300px" },
  );
  function bg(el) {
    if (el.matches(SKIP)) return;
    const m = /url\(["']?([^"')]+)["']?\)/.exec(el.style.backgroundImage || "");
    if (!m || el._sk === m[1]) return;
    el._sk = m[1];
    if (seen(m[1])) {
      el.classList.remove("sk-bg");
      return;
    }
    el.classList.add("sk-bg");
    bio.observe(el);
  }
  function scan(root) {
    if (root.nodeType !== 1) return;
    if (root.tagName === "IMG") img(root);
    else if (root.style && root.style.backgroundImage) bg(root);
    if (root.firstElementChild) {
      root.querySelectorAll("img").forEach(img);
      root.querySelectorAll('[style*="background-image"]').forEach(bg);
    }
  }
  scan(document.body);
  new MutationObserver((ms) => {
    for (const m of ms) {
      if (m.type === "attributes") {
        if (m.target.tagName === "IMG") img(m.target);
        else bg(m.target);
      } else m.addedNodes.forEach(scan);
    }
  }).observe(document.body, {
    childList: true,
    subtree: true,
    attributes: true,
    attributeFilter: ["src", "srcset", "style"],
  });
})();

/* ---- Movable quick-contact dock: drag (mouse/touch) or arrow keys on the grip; position remembered per device size ---- */
(function () {
  const d = document.querySelector(".dock");
  if (!d) return;
  const grip = d.querySelector(".dk-grip"),
    M = 8,
    mq = matchMedia("(max-width:640px)");
  const key = () => "dre-dock-pos" + (mq.matches ? "-m" : "");
  function place(x, y) {
    const w = d.offsetWidth,
      h = d.offsetHeight;
    x = Math.min(Math.max(M, x), innerWidth - w - M);
    y = Math.min(Math.max(M, y), innerHeight - h - M);
    d.style.left = x + "px";
    d.style.top = y + "px";
    d.classList.add("moved");
    d.classList.toggle("on-left", x + w / 2 < innerWidth / 2);
    return [x, y];
  }
  function save() {
    const r = d.getBoundingClientRect();
    try {
      localStorage.setItem(key(), JSON.stringify({ x: r.left / innerWidth, y: r.top / innerHeight }));
    } catch (e) {}
  }
  function reset() {
    d.classList.remove("moved", "on-left");
    d.style.left = d.style.top = "";
    try {
      localStorage.removeItem(key());
    } catch (e) {}
  }
  function restore() {
    let p = null;
    try {
      p = JSON.parse(localStorage.getItem(key()) || "null");
    } catch (e) {}
    if (p && isFinite(p.x) && isFinite(p.y)) place(p.x * innerWidth, p.y * innerHeight);
    else {
      d.classList.remove("moved", "on-left");
      d.style.left = d.style.top = "";
    }
  }
  restore();
  let s = null,
    dragged = false;
  d.addEventListener("pointerdown", (e) => {
    if (e.button) return;
    const r = d.getBoundingClientRect();
    s = { px: e.clientX, py: e.clientY, x: r.left, y: r.top, id: e.pointerId, grip: !!e.target.closest(".dk-grip") };
    dragged = false;
  });
  addEventListener("pointermove", (e) => {
    if (!s || e.pointerId !== s.id) return;
    const dx = e.clientX - s.px,
      dy = e.clientY - s.py;
    if (!dragged) {
      if (Math.hypot(dx, dy) < (s.grip ? 3 : 10)) return;
      dragged = true;
      d.classList.remove("snap");
      d.classList.add("dragging");
      try {
        d.setPointerCapture(s.id);
      } catch (_) {}
    }
    place(s.x + dx, s.y + dy);
  });
  function end(e) {
    if (!s || (e && e.pointerId !== s.id)) return;
    s = null;
    if (dragged) {
      d.classList.remove("dragging");
      save();
    }
  }
  addEventListener("pointerup", end);
  addEventListener("pointercancel", end);
  d.addEventListener(
    "click",
    (e) => {
      if (dragged) {
        e.preventDefault();
        e.stopImmediatePropagation();
        dragged = false;
      }
    },
    true,
  );
  grip.addEventListener("dblclick", () => {
    d.classList.add("snap");
    reset();
  });
  grip.addEventListener("keydown", (e) => {
    const k = { ArrowLeft: [-1, 0], ArrowRight: [1, 0], ArrowUp: [0, -1], ArrowDown: [0, 1] }[e.key];
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      d.classList.add("snap");
      reset();
      return;
    }
    if (!k) return;
    e.preventDefault();
    const r = d.getBoundingClientRect(),
      st = e.shiftKey ? 60 : 20;
    d.classList.add("snap");
    place(r.left + k[0] * st, r.top + k[1] * st);
    save();
  });
  let lastM = mq.matches;
  addEventListener("resize", () => {
    if (mq.matches !== lastM) {
      lastM = mq.matches;
      restore();
    } else if (d.classList.contains("moved")) {
      const r = d.getBoundingClientRect();
      place(r.left, r.top);
    }
  });
})();

/* ---- Night mode toggle (dock button); choice saved in localStorage, applied before paint by the small inline script in each page <head> ---- */
(function () {
  const b = document.getElementById("modeBtn");
  if (!b) return;
  const h = document.documentElement,
    meta = document.querySelector("meta[name=theme-color]"),
    tip = b.querySelector(".tip");
  function sync() {
    const n = h.dataset.mode === "night";
    b.setAttribute("aria-pressed", n);
    b.setAttribute("aria-label", n ? "Switch to day mode" : "Switch to night mode");
    tip.textContent = n ? "Day mode" : "Night mode";
    if (meta) meta.content = n ? "#14110e" : "#5f574c";
  }
  sync();
  b.addEventListener("click", () => {
    const n = h.dataset.mode !== "night";
    h.classList.add("mode-anim");
    if (n) h.dataset.mode = "night";
    else delete h.dataset.mode;
    try {
      localStorage.setItem("dre-mode", n ? "night" : "day");
    } catch (e) {}
    sync();
    clearTimeout(b._t);
    b._t = setTimeout(() => h.classList.remove("mode-anim"), 600);
  });
})();
