/* DRE Homes — page features (one file for every page).

   All text, cards, photos, links and lists are written in the HTML pages — edit the website there.
   This file only adds behaviour on top of the HTML: filters, search, sorting, "show more",
   page numbers, photo galleries, calculators and forms.
   Each block first checks that its elements exist, so it simply does nothing on other pages.

   How the lists work: every card carries its details in data-* attributes
   (e.g. <article class="oc" data-c="Dubai Hills Estate" data-p="1710000" …>).
   To add a card, copy an existing one in the HTML and change its text and data-* values. */
(function () {
  "use strict";

  /* =====================================================================
     Shared helpers
     ===================================================================== */
  const $ = (id) => document.getElementById(id);
  const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];
  const esc = (s) =>
    String(s).replace(/[&<>"]/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" })[c]);
  const aed = (n) => "AED " + Math.round(n).toLocaleString("en-US");
  const digits = (v) => +String(v).replace(/[^\d]/g, "") || 0;
  const fillRange = (r) => r.style.setProperty("--p", ((r.value - r.min) / (r.max - r.min)) * 100 + "%");
  const SUGS = (window.__SUGS = window.__SUGS || []);
  const label = (btn) =>
    (btn.firstChild && btn.firstChild.nodeType === 3 ? btn.firstChild.nodeValue : btn.textContent).trim();
  /* text of an element as written in the HTML (the Dirham symbol counts as "AED") */
  const plain = (el) => {
    if (!el) return "";
    if (!el.querySelector(".aed")) return el.textContent.trim();
    const c = el.cloneNode(true);
    c.querySelectorAll(".aed").forEach((s) => s.replaceWith("AED "));
    return c.textContent.trim();
  };
  const text = (el, sel) => (sel ? $$(sel, el).map(plain).join(" ") : plain(el)).toLowerCase();
  const plural = (n, one, many) => n + " " + (n === 1 ? one : many || one + "s");

  /* Put the Private Consultation form in view and focus the name field */
  function goContact() {
    const c = $("contact");
    if (!c) return;
    c.scrollIntoView({ behavior: "smooth" });
    setTimeout(() => {
      const n = document.querySelector("#ctaForm input[name=name]");
      if (n) n.focus({ preventScroll: true });
    }, 600);
  }

  /* Highlight a search word inside an element (keeps any markup, e.g. the Dirham symbol) */
  function mark(el, q) {
    if (el.__h === undefined) el.__h = el.innerHTML;
    if (el.__hl) {
      el.innerHTML = el.__h;
      el.__hl = false;
    }
    if (!q) return;
    const ql = q.toLowerCase();
    const w = document.createTreeWalker(el, NodeFilter.SHOW_TEXT);
    const nodes = [];
    while (w.nextNode()) nodes.push(w.currentNode);
    nodes.forEach((n) => {
      const s = n.nodeValue,
        low = s.toLowerCase();
      let i = low.indexOf(ql);
      if (i < 0) return;
      const frag = document.createDocumentFragment();
      let last = 0;
      while (i >= 0) {
        frag.append(s.slice(last, i));
        const m = document.createElement("mark");
        m.textContent = s.slice(i, i + q.length);
        frag.append(m);
        last = i + q.length;
        i = low.indexOf(ql, last);
      }
      frag.append(s.slice(last));
      n.replaceWith(frag);
    });
    el.__hl = true;
  }

  /* Recount the little numbers on filter chips from the cards on the page */
  function recount(chips, items, test) {
    chips.forEach((b) => {
      const n = items.filter((el) => test(el, b)).length;
      const c = b.querySelector("b,span");
      if (c) c.textContent = n;
    });
  }

  /* Page numbers (three looks used on the site: "dots" as span, "dots" as button, or every number) */
  function pager(el, page, pages, style) {
    if (pages < 2) {
      el.innerHTML = "";
      return;
    }
    const nums = [];
    for (let i = 1; i <= pages; i++) {
      if (style === "all" || i === 1 || i === pages || Math.abs(i - page) <= 1) nums.push(i);
      else if (nums[nums.length - 1] !== "…") nums.push("…");
    }
    el.innerHTML =
      `<button data-p="${page - 1}" ${page === 1 ? "disabled" : ""} aria-label="Previous page">←</button>` +
      nums
        .map((x) =>
          x === "…"
            ? style === "span"
              ? '<span class="dots">…</span>'
              : "<button disabled>…</button>"
            : `<button data-p="${x}" class="${x === page ? "on" : ""}" ${x === page ? 'aria-current="page"' : ""}>${x}</button>`,
        )
        .join("") +
      `<button data-p="${page + 1}" ${page === pages ? "disabled" : ""} aria-label="Next page">→</button>`;
  }

  /* =====================================================================
     List engine — shows/hides the cards that are already in the HTML
       grid   : the element that holds the cards
       test   : (card, state) => true when the card matches the filters
       mode   : "more" (Show more button), "pages" (page numbers) or "all"
       per    : cards per page / per "Show more"
       sorts  : { key: (a, b) => … } extra sort orders (default = HTML order)
       render : called after every update to refresh counts, buttons, etc.
     ===================================================================== */
  function makeList(o) {
    const grid = o.grid;
    const items = [...grid.children].filter((el) => el.nodeType === 1);
    items.forEach((el, i) => (el.__i = i));
    const st = Object.assign({ page: 1, n: o.per || Infinity, sort: "" }, o.state || {});
    let view = [],
      lastSort = "";
    function draw(scroll) {
      view = items.filter((el) => o.test(el, st));
      const sorter = o.sorts && o.sorts[st.sort];
      if (sorter) view = view.slice().sort(sorter);
      if (st.sort !== lastSort) {
        (sorter ? items.slice().sort(sorter) : items).forEach((el) => grid.appendChild(el));
        lastSort = st.sort;
      }
      let shown = view;
      if (o.mode === "pages") {
        st.pages = Math.max(1, Math.ceil(view.length / o.per));
        st.page = Math.min(st.page, st.pages);
        shown = view.slice((st.page - 1) * o.per, st.page * o.per);
      } else if (o.mode === "more") shown = view.slice(0, st.n);
      const vis = new Set(shown);
      items.forEach((el) => (el.hidden = !vis.has(el)));
      if (o.delay) shown.forEach((el, i) => (el.style.animationDelay = o.delay(i)));
      if (o.render) o.render(view, shown, st, scroll);
    }
    return {
      st,
      items,
      draw,
      get view() {
        return view;
      },
      set(k, v, scroll) {
        st[k] = v;
        st.page = 1;
        st.n = o.per || Infinity;
        draw(scroll);
      },
    };
  }

  /* =====================================================================
     Photo lightbox (one <dialog> per page; photos come from the HTML)
       triggers: buttons/links with data-src (full photo) and data-title
     ===================================================================== */
  function lightbox(dlg, ids, onShow) {
    let set = [],
      cur = 0;
    const img = $(ids.img);
    function show(i) {
      cur = (i + set.length) % set.length;
      const p = set[cur];
      img.src = p.src;
      img.alt = p.title || "";
      if (ids.title && $(ids.title)) $(ids.title).textContent = p.title || "";
      if (ids.count && $(ids.count))
        $(ids.count).textContent = ids.countText ? ids.countText(cur, set, p) : `${cur + 1} / ${set.length}`;
      if (onShow) onShow(cur, set, p);
      [set[cur + 1], set[cur - 1]].forEach((n) => {
        if (n) new Image().src = n.src;
      });
    }
    function open(list, i) {
      set = list;
      dlg.showModal();
      show(i || 0);
    }
    $(ids.prev).onclick = () => show(cur - 1);
    $(ids.next).onclick = () => show(cur + 1);
    $(ids.close).onclick = () => dlg.close();
    dlg.addEventListener("click", (e) => {
      if (e.target === dlg || e.target.tagName === "FIGURE") dlg.close();
    });
    dlg.addEventListener("keydown", (e) => {
      if (e.key === "ArrowRight") show(cur + 1);
      if (e.key === "ArrowLeft") show(cur - 1);
    });
    let tx = null;
    dlg.addEventListener("touchstart", (e) => (tx = e.touches[0].clientX), { passive: true });
    dlg.addEventListener(
      "touchend",
      (e) => {
        if (tx === null) return;
        const dx = e.changedTouches[0].clientX - tx;
        if (Math.abs(dx) > 50) show(cur + (dx < 0 ? 1 : -1));
        tx = null;
      },
      { passive: true },
    );
    return {
      open,
      show,
      get cur() {
        return cur;
      },
    };
  }
  const photo = (el) => ({ src: el.dataset.src, title: el.dataset.title || "", cap: el.dataset.cap || "", el });

  /* Simple galleries: every [data-lb] thumbnail opens the page lightbox (#mdLb) */
  (function () {
    const dlg = $("mdLb"),
      thumbs = $$("[data-lb][data-src]");
    if (!dlg || !thumbs.length) return;
    const lb = lightbox(dlg, {
      img: "mdLbImg",
      title: "mdLbT",
      count: "mdLbN",
      prev: "mdPv",
      next: "mdNx",
      close: "mdX",
    });
    const list = thumbs.map(photo);
    thumbs.forEach((t, i) => (t.onclick = () => lb.open(list, i)));
  })();

  /* =====================================================================
     Home page — hero location suggestions (list = <datalist> in the HTML)
     ===================================================================== */
  /* Any search box with data-suggest="<datalist id>" gets glass suggestions from that list */
  $$("input[data-suggest]").forEach((inp) => {
    const dl = $(inp.dataset.suggest);
    if (!dl || SUGS.some((c) => c.input === inp.id)) return;
    const opts = $$("option", dl).map((o) => ({ t: o.value, s: o.textContent.trim() }));
    const pop = (inp.dataset.popular || "").split("|").filter(Boolean);
    const g = pop.length ? "Communities" : "Areas";
    SUGS.push({
      input: inp.id,
      minW: +inp.dataset.minW || undefined,
      popTitle: inp.dataset.popTitle,
      per: +inp.dataset.per || 4,
      max: +inp.dataset.max || 9,
      popular: pop.length ? pop.map((t) => ({ t, s: "Dubai", g, ic: "pin" })) : undefined,
      items: opts.map((o) => ({ t: o.t, s: o.s || "Community", g, ic: "pin" })),
      onPick: () => inp.dispatchEvent(new Event("change", { bubbles: true })),
    });
  });

  /* =====================================================================
     Guides (buyer / seller / tenant / landlord): step checklist, progress, scrollspy
     ===================================================================== */
  (function () {
    const nav = $("gdNav");
    if (!nav) return;
    const KEY = "dre-guide-" + (location.pathname.match(/([a-z]+)-guide/) || [, "guide"])[1];
    const boxes = $$(".gd-done input"),
      links = $$("a", nav),
      cards = $$(".gd-step"),
      N = cards.length;
    let done = [];
    try {
      done = JSON.parse(localStorage.getItem(KEY) || "[]");
    } catch (e) {}
    boxes.forEach((b) => (b.checked = done.includes(+b.dataset.i)));
    function upd() {
      const d = boxes.filter((b) => b.checked).map((b) => +b.dataset.i);
      try {
        localStorage.setItem(KEY, JSON.stringify(d));
      } catch (e) {}
      links.forEach((a, i) => a.classList.toggle("done", d.includes(i)));
      cards.forEach((c, i) => c.classList.toggle("done", d.includes(i)));
      $("gdCount").textContent = d.length === N ? "All done ✦" : `${d.length} of ${N}`;
      $("gdBar").style.width = (d.length / N) * 100 + "%";
      $("gdReset").hidden = !d.length;
    }
    boxes.forEach((b) => b.addEventListener("change", upd));
    $("gdReset").onclick = () => {
      boxes.forEach((b) => (b.checked = false));
      upd();
    };
    function spy() {
      let k = 0;
      cards.forEach((c, i) => {
        if (c.getBoundingClientRect().top < innerHeight * 0.4) k = i;
      });
      links.forEach((a, i) => a.classList.toggle("on", i === k));
    }
    const tb = document.querySelector(".gd-tabs"),
      on = tb && tb.querySelector(".gd-tab.on");
    if (on && tb.scrollWidth > tb.clientWidth) tb.scrollLeft = on.offsetLeft - (tb.clientWidth - on.offsetWidth) / 2;
    addEventListener("scroll", spy, { passive: true });
    spy();
    upd();
  })();

  /* =====================================================================
     Service pages: tabs, document checklists, "Get started" → contact form
     ===================================================================== */
  /* ---- Service pages: tabs, document checklists, service → form prefill ---- */
  (function () {
    document.querySelectorAll(".sv-tabs").forEach((tl) => {
      const tabs = [...tl.querySelectorAll("[role=tab]")];
      const go = (t, focus) => {
        tabs.forEach((x) => {
          const on = x === t;
          x.classList.toggle("on", on);
          x.setAttribute("aria-selected", on);
          x.tabIndex = on ? 0 : -1;
          document.getElementById(x.getAttribute("aria-controls")).hidden = !on;
        });
        if (focus) t.focus();
      };
      tabs.forEach((t, i) => {
        t.tabIndex = i ? -1 : 0;
        t.onclick = () => go(t);
        t.onkeydown = (e) => {
          const k = { ArrowRight: 1, ArrowLeft: -1 }[e.key];
          if (k) {
            e.preventDefault();
            go(tabs[(i + k + tabs.length) % tabs.length], true);
          }
        };
      });
    });
    document.querySelectorAll(".ckl").forEach((c) => {
      const boxes = [...c.querySelectorAll("input")],
        n = c.querySelector(".ckl-n"),
        bar = c.querySelector(".ckl-bar i");
      const upd = () => {
        const k = boxes.filter((b) => b.checked).length;
        n.textContent = k === boxes.length ? "All set — you’re ready to apply" : `${k} of ${boxes.length} ready`;
        bar.style.setProperty("--w", (k / boxes.length) * 100 + "%");
        c.classList.toggle("done", k === boxes.length);
      };
      boxes.forEach((b) => b.addEventListener("change", upd));
      upd();
    });
    const sel = document.getElementById("ctaSvc");
    document.querySelectorAll(".mx-go[data-svc]").forEach(
      (b) =>
        (b.onclick = () => {
          if (sel) sel.value = b.dataset.svc;
          document.getElementById("contact").scrollIntoView({ behavior: "smooth" });
          setTimeout(() => document.querySelector("#ctaForm input[name=name]").focus({ preventScroll: true }), 600);
        }),
    );
  })();

  /* =====================================================================
     Legal pages: contents scrollspy, reading progress, copy section link, print
     ===================================================================== */
  /* ---- Legal: TOC scrollspy, reading progress, copy section link, print ---- */
  (function () {
    const doc = document.querySelector(".lg-doc");
    if (!doc) return;
    const links = [...document.querySelectorAll(".lg-toc a")],
      secs = links.map((a) => document.getElementById(a.hash.slice(1))),
      bar = document.querySelector(".lg-prog i");
    function upd() {
      let k = 0;
      secs.forEach((s, i) => {
        if (s && s.getBoundingClientRect().top < innerHeight * 0.3) k = i;
      });
      links.forEach((a, i) => a.classList.toggle("on", i === k));
      const r = doc.getBoundingClientRect();
      bar.style.width =
        Math.min(100, Math.max(0, ((innerHeight * 0.3 - r.top) / (r.height - innerHeight * 0.3)) * 100)) + "%";
    }
    addEventListener("scroll", upd, { passive: true });
    upd();
    document.querySelectorAll(".lg-link").forEach(
      (b) =>
        (b.onclick = async () => {
          const u = location.href.split("#")[0] + "#" + b.dataset.id;
          try {
            await navigator.clipboard.writeText(u);
          } catch (e) {}
          history.replaceState(null, "", "#" + b.dataset.id);
          b.classList.add("ok");
          b.textContent = "✓";
          setTimeout(() => {
            b.classList.remove("ok");
            b.textContent = "#";
          }, 1500);
        }),
    );
    const p = document.getElementById("lgPrint");
    if (p) p.onclick = () => window.print();
  })();

  /* =====================================================================
     Articles (blog post, news story): reading progress, contents scrollspy, copy link
       copy-link URL = data-url on the #arCopy / #arCopy2 buttons
     ===================================================================== */
  /* copy-link URL = data-url on the #arCopy / #arCopy2 buttons */
  /* ---- Blog article: reading progress, table-of-contents scrollspy, copy link ---- */
  (function () {
    const body = document.getElementById("arBody");
    if (!body) return;
    const bar = document.querySelector("#arProg i");
    function prog() {
      const r = body.getBoundingClientRect();
      const p = Math.min(1, Math.max(0, (innerHeight * 0.35 - r.top) / (r.height - innerHeight * 0.35)));
      bar.style.transform = `scaleX(${p})`;
    }
    addEventListener("scroll", prog, { passive: true });
    addEventListener("resize", prog);
    prog();
    const links = [...document.querySelectorAll("#arToc a")],
      heads = links.map((a) => document.getElementById(a.hash.slice(1)));
    function spy() {
      let k = -1;
      heads.forEach((h, i) => {
        if (h && h.getBoundingClientRect().top < 160) k = i;
      });
      links.forEach((a, i) => a.classList.toggle("on", i === k));
    }
    addEventListener("scroll", spy, { passive: true });
    spy();
    ["arCopy", "arCopy2"].forEach((id) => {
      const b = document.getElementById(id);
      if (!b) return;
      b.onclick = async () => {
        try {
          await navigator.clipboard.writeText(b.dataset.url || location.href);
        } catch (e) {}
        b.classList.add("ok");
        b.setAttribute("aria-label", "Link copied");
        setTimeout(() => {
          b.classList.remove("ok");
          b.setAttribute("aria-label", "Copy link");
        }, 1600);
      };
    });
  })();

  /* =====================================================================
     International properties: filter by country
     ===================================================================== */
  /* ---- International: filter by country ---- */
  (function () {
    const ch = document.getElementById("cChips");
    if (!ch) return;
    ch.onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      ch.querySelectorAll("button").forEach((x) => {
        x.classList.toggle("on", x === b);
        x.setAttribute("aria-selected", x === b);
      });
      const c = b.dataset.c;
      document.querySelectorAll(".icard").forEach((k) => {
        k.hidden = !!c && k.dataset.c !== c;
        k.classList.add("in");
      });
    };
  })();

  /* =====================================================================
     Brand film: loads the YouTube player only when clicked (video id = data-yt)
     ===================================================================== */
  /* video id = data-yt on the #filmPlay button */
  /* ---- Brand film: click-to-load YouTube ---- */
  (function () {
    const f = document.getElementById("filmFrame"),
      b = document.getElementById("filmPlay");
    if (!f || !b) return;
    b.onclick = () => {
      const i = document.createElement("iframe");
      i.src =
        "https://www.youtube-nocookie.com/embed/" + b.dataset.yt + "?autoplay=1&rel=0&modestbranding=1&playsinline=1";
      i.title = b.dataset.title || "Video";
      i.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture";
      i.allowFullscreen = true;
      f.appendChild(i);
      f.classList.add("playing");
    };
  })();

  /* =====================================================================
     Google map loads only when clicked (map link = data-map on the #mapLoad button)
     Used by the property detail pages (single-property, buy/rent/ready/international
     -*-single.php); community-single.php embeds its map directly instead.
     ===================================================================== */
  (function () {
    const b = $("mapLoad"),
      w = $("mapWrap");
    if (!b || !w || !b.dataset.map) return;
    b.onclick = () => {
      const i = document.createElement("iframe");
      i.src = b.dataset.map;
      i.loading = "lazy";
      i.referrerPolicy = "no-referrer-when-downgrade";
      i.allowFullscreen = true;
      i.title = "Map";
      w.innerHTML = "";
      w.appendChild(i);
    };
  })();

  /* =====================================================================
     Mortgage advisory: calculator, buyer type, service → form
     ===================================================================== */
  /* ---- Mortgage advisory: calculator, buyer type, service → form prefill ---- */
  (function () {
    const P = document.getElementById("mPrice");
    if (!P || !document.getElementById("uDld")) return; // the property pages have their own smaller calculator
    const $ = (id) => document.getElementById(id),
      dp = $("mDp"),
      rt = $("mRt"),
      yr = $("mYr");
    const f = (n) => "AED " + Math.round(n).toLocaleString("en-US");
    const fill = (r) => r.style.setProperty("--p", ((r.value - r.min) / (r.max - r.min)) * 100 + "%");
    function calc() {
      const price = +P.value.replace(/[^\d]/g, "") || 0,
        d = +dp.value / 100,
        r = +rt.value / 1200,
        n = +yr.value * 12;
      const loan = price * (1 - d),
        m = r ? (loan * r) / (1 - Math.pow(1 + r, -n)) : loan / n,
        int = m * n - loan;
      $("mDpV").textContent = dp.value + "%";
      $("mRtV").textContent = (+rt.value).toFixed(1) + "%";
      $("mYrV").textContent = yr.value + " years";
      $("mOut").textContent = price ? f(m) : "—";
      $("mLoan").textContent = f(loan);
      $("mDown").textContent = f(price * d);
      $("mInt").textContent = f(int);
      const tot = loan + int || 1;
      $("mBarP").style.width = (loan / tot) * 100 + "%";
      $("mBarI").style.width = (int / tot) * 100 + "%";
      $("mPP").textContent = f(loan / n);
      $("mIP").textContent = f(int / n);
      const dld = price * 0.04,
        reg = loan * 0.0025;
      $("uDown").textContent = f(price * d);
      $("uDld").textContent = f(dld);
      $("uReg").textContent = f(reg);
      $("uTot").textContent = f(price * d + dld + reg);
      [dp, rt, yr].forEach(fill);
    }
    P.addEventListener("input", () => {
      const v = P.value.replace(/[^\d]/g, "");
      P.value = v ? (+v).toLocaleString("en-US") : "";
      calc();
    });
    [dp, rt, yr].forEach((r) => r.addEventListener("input", calc));
    document.querySelector(".mg-type").addEventListener("click", (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      document.querySelectorAll(".mg-type button").forEach((x) => x.classList.toggle("on", x === b));
      dp.min = b.dataset.min;
      if (+dp.value < +dp.min) dp.value = dp.min;
      calc();
    });
    calc();
    const sel = $("ctaSvc"),
      msg = document.querySelector("#ctaForm input[name=message]");
    document.querySelectorAll(".mx-go").forEach(
      (b) =>
        (b.onclick = () => {
          if (sel) sel.value = b.dataset.svc;
          $("contact").scrollIntoView({ behavior: "smooth" });
          setTimeout(() => document.querySelector("#ctaForm input[name=name]").focus({ preventScroll: true }), 600);
        }),
    );
    $("mApply").addEventListener("click", () => {
      if (msg && !msg.value)
        msg.value =
          "Pre-approval for a property of " +
          P.value.replace(/[^\d,]/g, "") +
          " with " +
          dp.value +
          "% down over " +
          yr.value +
          " years";
    });
  })();

  /* =====================================================================
     Mortgage calculator page: payment, donut, yearly schedule, shareable link
     ===================================================================== */
  /* ---- Mortgage calculator: monthly amortisation, donut, yearly schedule, shareable link ---- */
  (function () {
    const $ = (id) => document.getElementById(id);
    if (!$("cPrice")) return;
    const P = $("cPrice"),
      PR = $("cPriceR"),
      dp = $("cDp"),
      yr = $("cYr"),
      rt = $("cRt"),
      rtN = $("cRtN");
    let freq = "m";
    const f0 = (n) => "AED " + Math.round(n).toLocaleString("en-US");
    const short = (n) =>
      n >= 1e6
        ? "AED " + (n / 1e6).toFixed(n >= 1e7 ? 1 : 2).replace(/\.?0+$/, "") + "M"
        : "AED " + Math.round(n / 1e3) + "K";
    const fill = (r) => r.style.setProperty("--p", ((r.value - r.min) / (r.max - r.min)) * 100 + "%");
    const price = () => +P.value.replace(/[^\d]/g, "") || 0;
    function seg(el, frac, off) {
      const C = 2 * Math.PI * 48;
      el.style.strokeDasharray = `${Math.max(0, frac * C - 1.5)} ${C}`;
      el.style.strokeDashoffset = -off * C;
    }
    function calc() {
      const pr = price(),
        d = +dp.value / 100,
        n = +yr.value * 12,
        r = Math.min(15, Math.max(0, +rtN.value || 0)) / 1200,
        loan = pr * (1 - d);
      const m = n ? (r ? (loan * r) / (1 - Math.pow(1 + r, -n)) : loan / n) : 0,
        int = m * n - loan,
        tot = pr + int;
      $("cDpV").textContent = dp.value + "%";
      $("cDpA").textContent = f0(pr * d);
      $("cYrV").textContent = yr.value + (yr.value == 1 ? " year" : " years");
      $("cLbl").textContent = freq === "m" ? "Estimated monthly payment" : "Estimated annual payment";
      $("cOut").textContent = pr ? f0(freq === "m" ? m : m * 12) : "—";
      $("cDown").textContent = f0(pr * d);
      $("cLoan").textContent = f0(loan);
      $("cInt").textContent = f0(int);
      $("cTot").textContent = short(tot);
      $("cInc").textContent = f0(m / 0.5);
      const T = tot || 1;
      seg($("dDown"), (pr * d) / T, 0);
      seg($("dLoan"), loan / T, (pr * d) / T);
      seg($("dInt"), int / T, (pr * d + loan) / T);
      const dld = pr * 0.04,
        reg = loan * 0.0025;
      $("uDown").textContent = f0(pr * d);
      $("uDld").textContent = f0(dld);
      $("uReg").textContent = f0(reg);
      $("uTot").textContent = f0(pr * d + dld + reg);
      /* yearly schedule */
      let bal = loan,
        rows = [],
        half = null;
      for (let y = 1; y <= +yr.value; y++) {
        let pp = 0,
          ii = 0;
        for (let k = 0; k < 12; k++) {
          const it = bal * r,
            pc = Math.min(bal, m - it);
          ii += it;
          pp += pc;
          bal = Math.max(0, bal - pc);
        }
        rows.push([y, pp, ii, bal]);
        if (half === null && bal <= loan / 2) half = y;
      }
      const mx = Math.max(...rows.map((x) => x[1] + x[2]), 1);
      $("cBars").innerHTML = rows
        .map(
          ([y, pp, ii, b]) =>
            `<div class="mc2-bar" tabindex="0"><i class="p" style="height:${(pp / mx) * 100}%"></i><i class="n" style="height:${(ii / mx) * 100}%"></i><em>${y}</em><span class="tip">Year ${y}<br>Principal ${f0(pp)}<br>Interest ${f0(ii)}<br>Balance ${f0(b)}</span></div>`,
        )
        .join("");
      $("cRows").innerHTML = rows
        .map(([y, pp, ii, b]) => `<tr><td>${y}</td><td>${f0(pp)}</td><td>${f0(ii)}</td><td>${f0(b)}</td></tr>`)
        .join("");
      $("cHalf").textContent = loan > 0 && half ? `Half your loan is paid off by year ${half}` : "";
      [PR, dp, yr, rt].forEach(fill);
      if (calc.ready)
        try {
          history.replaceState(null, "", `#p=${pr}&d=${dp.value}&y=${yr.value}&r=${rtN.value}`);
        } catch (e) {}
    }
    P.addEventListener("input", () => {
      const v = P.value.replace(/[^\d]/g, "");
      P.value = v ? (+v).toLocaleString("en-US") : "";
      PR.value = Math.min(PR.max, Math.max(PR.min, +v || 0));
      calc();
    });
    PR.addEventListener("input", () => {
      P.value = (+PR.value).toLocaleString("en-US");
      calc();
    });
    [dp, yr].forEach((x) => x.addEventListener("input", calc));
    rt.addEventListener("input", () => {
      rtN.value = (+rt.value).toFixed(2).replace(/0$/, "");
      calc();
    });
    rtN.addEventListener("input", () => {
      rt.value = Math.min(+rt.max, Math.max(0, +rtN.value || 0));
      calc();
    });
    document.querySelector(".mc2 .mg-type").addEventListener("click", (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      document.querySelectorAll(".mc2 .mg-type button").forEach((x) => x.classList.toggle("on", x === b));
      dp.min = b.dataset.min;
      if (+dp.value < +dp.min) dp.value = dp.min;
      $("cMinNote").textContent =
        b.dataset.min === "15"
          ? "UAE nationals usually need at least 15% down on a first home under AED 5M."
          : "Expats usually need at least 20% down on a first home under AED 5M.";
      calc();
    });
    document.querySelector(".mc2-seg").addEventListener("click", (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      freq = b.dataset.f;
      document.querySelectorAll(".mc2-seg button").forEach((x) => x.classList.toggle("on", x === b));
      calc();
    });
    /* restore from link */
    const q = new URLSearchParams(location.hash.slice(1));
    if (q.get("p")) {
      const p = +q.get("p") || 0;
      P.value = p.toLocaleString("en-US");
      PR.value = p;
      if (q.get("d")) dp.value = q.get("d");
      if (q.get("y")) yr.value = q.get("y");
      if (q.get("r")) {
        rt.value = q.get("r");
        rtN.value = q.get("r");
      }
    }
    $("cShare").onclick = async () => {
      const u = location.href.split("#")[0].replace(/[?].*$/, "") + location.hash;
      try {
        await navigator.clipboard.writeText(u);
      } catch (e) {}
      const b = $("cShare");
      b.classList.add("ok");
      b.querySelector("span").textContent = "Link copied";
      setTimeout(() => {
        b.classList.remove("ok");
        b.querySelector("span").textContent = "Share estimate";
      }, 1800);
    };
    $("cApply").addEventListener("click", () => {
      const msg = document.querySelector("#ctaForm input[name=message]");
      if (msg)
        msg.value = `Loan of ${f0(price() * (1 - dp.value / 100))} on a ${f0(price())} property · ${dp.value}% down · ${yr.value} yrs · ${rtN.value}%`;
    });
    calc();
    calc.ready = true;
  })();

  /* =====================================================================
     Golden Visa: eligibility checker
     ===================================================================== */
  /* ---- Golden Visa: eligibility checker ---- */
  (function () {
    const $ = (id) => document.getElementById(id);
    if (!$("gvV")) return;
    const V = $("gvV"),
      R = $("gvR"),
      S = $("gvS"),
      T = 2000000,
      MIN = +R.min,
      MAX = +R.max;
    let st = "ready",
      mg = "no";
    const f0 = (n) => "AED " + Math.round(n).toLocaleString("en-US");
    const OK =
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>',
      NO =
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6v18h12V7z"/><path d="M14 3v4h4M9 13h6M9 17h6"/></svg>',
      DOT =
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>';
    const pos = (v) => Math.max(0, Math.min(100, ((v - MIN) / (MAX - MIN)) * 100));
    document.querySelector(".gv-mark").style.setProperty("--m", pos(T) + "%");
    function upd() {
      const val = +V.value.replace(/[^\d]/g, "") || 0,
        sh = +S.value / 100,
        mine = val * sh,
        ok = mine >= T;
      $("gvSV").textContent = S.value + "%";
      $("gvYour").textContent = f0(mine);
      $("gvFill").style.width = pos(mine) + "%";
      $("gvFill").parentElement.classList.toggle("ok", ok);
      [R, S].forEach((r) => r.style.setProperty("--p", ((r.value - r.min) / (r.max - r.min)) * 100 + "%"));
      $("gvRes").className = "gv-res" + (ok ? " ok" : "");
      $("gvRes").innerHTML = ok
        ? OK +
          "<div><b>Likely eligible</b>for the 10-year Golden Visa. We can shortlist qualifying residences for you.</div>"
        : NO +
          `<div><b>${f0(T - mine)} short</b>Below the AED 2M threshold — a 2-year investor visa may still apply. Ask our advisors.</div>`;
      const n = [];
      if (sh < 1) n.push("For jointly owned property, the value of your own share is what counts.");
      if (st === "offplan") n.push("Off-plan properties may qualify — we’ll check your developer and project.");
      if (mg === "yes") n.push("Mortgaged properties may qualify — your bank’s paperwork will be needed.");
      n.push("Your family can be included on the same visa.");
      $("gvNotes").innerHTML = n.map((x) => `<li>${DOT}<span>${x}</span></li>`).join("");
    }
    V.addEventListener("input", () => {
      const v = V.value.replace(/[^\d]/g, "");
      V.value = v ? (+v).toLocaleString("en-US") : "";
      R.value = Math.min(MAX, Math.max(MIN, +v || 0));
      upd();
    });
    R.addEventListener("input", () => {
      V.value = (+R.value).toLocaleString("en-US");
      upd();
    });
    S.addEventListener("input", upd);
    document.querySelectorAll(".gv-tg .mg-type").forEach((g) =>
      g.addEventListener("click", (e) => {
        const b = e.target.closest("button");
        if (!b) return;
        g.querySelectorAll("button").forEach((x) => x.classList.toggle("on", x === b));
        if (b.dataset.st) st = b.dataset.st;
        if (b.dataset.mg) mg = b.dataset.mg;
        upd();
      }),
    );
    $("gvClaim").addEventListener("click", () => {
      const m = document.querySelector("#ctaForm input[name=message]");
      if (m)
        m.value = `Golden Visa: ${st === "offplan" ? "off-plan" : "ready"} property worth ${V.value} (my share ${S.value}%)${mg === "yes" ? ", mortgaged" : ""}`;
    });
    upd();
  })();

  /* =====================================================================
     Property management: fee calculator, package → form, feedback survey
     ===================================================================== */
  /* ---- Property management: fee calculator, package -> form, survey ---- */
  (function () {
    const ri = document.getElementById("rentIn"),
      rr = document.getElementById("rentRange");
    if (!ri) return;
    const fmt = (n) => "AED " + Math.round(n).toLocaleString("en-US");
    function calc(v) {
      document.querySelectorAll(".pk").forEach((p) => {
        const pct = +p.dataset.pct,
          min = +p.dataset.min,
          raw = (v * pct) / 100,
          fee = Math.max(raw, min);
        p.querySelector(".pk-fee b").innerHTML = v
          ? fmt(fee) +
            (raw < min ? "<em>minimum applies</em>" : "") +
            '<span style="display:inline;letter-spacing:0;text-transform:none;font-size:13px;color:var(--faint)"> / year</span>'
          : "—";
      });
    }
    const parse = (t) => +String(t).replace(/[^0-9]/g, "");
    ri.addEventListener("input", () => {
      const v = parse(ri.value);
      ri.value = v ? v.toLocaleString("en-US") : "";
      rr.value = Math.min(Math.max(v, +rr.min), +rr.max);
      rr.style.setProperty("--p", ((rr.value - rr.min) / (rr.max - rr.min)) * 100 + "%");
      calc(v);
    });
    rr.addEventListener("input", () => {
      ri.value = (+rr.value).toLocaleString("en-US");
      rr.style.setProperty("--p", ((rr.value - rr.min) / (rr.max - rr.min)) * 100 + "%");
      calc(+rr.value);
    });
    rr.dispatchEvent(new Event("input"));
    document.querySelectorAll("[data-pkg]").forEach((a) =>
      a.addEventListener("click", () => {
        const s = document.getElementById("pkgSel");
        if (s) s.value = a.dataset.pkg;
      }),
    );
    const dlg = document.getElementById("survey");
    document.getElementById("openSurvey").onclick = () => dlg.showModal();
    document.getElementById("closeSurvey").onclick = () => dlg.close();
    dlg.addEventListener("click", (e) => {
      if (e.target === dlg) dlg.close();
    });
    document.getElementById("surveyForm").addEventListener("submit", (e) => {
      e.preventDefault();
      e.target.innerHTML =
        "<span class='eyebrow'>Thank you</span><h2 class='serif'>Thank you for choosing <em class='gold-text'>DRE Property Management!</em></h2><p class='lead'>Your feedback helps us improve every visit.</p><button class='btn btn-gold' type='button' onclick=\"this.closest('dialog').close()\" style='justify-content:center'>Close</button>";
    });
  })();

  /* =====================================================================
     Property / listing detail pages: photo gallery, read more, section tabs,
     mortgage estimate, brochure download, "similar" carousel
       all photos = the hidden <ul id="pdPhotos"> list in the HTML
     ===================================================================== */
  (function () {
    if (!document.querySelector(".pd-gal")) return;
    const list = $$("#pdPhotos li").map(photo);
    if ($("lb") && list.length) {
      const lb = lightbox($("lb"), {
        img: "lbImg",
        count: "lbC",
        countText: (i, set, p) => `${i + 1} / ${set.length}` + (p.cap ? " · " + p.cap : ""),
        prev: "lbP",
        next: "lbN",
        close: "lbX",
      });
      $$("[data-g]").forEach((b) => b.addEventListener("click", () => lb.open(list, +b.dataset.g)));
    }
    $$(".rm").forEach(
      (b) =>
        (b.onclick = () => {
          const t = $(b.dataset.t);
          const o = t.classList.toggle("open");
          b.textContent = o ? "Read less ↑" : "Read more ↓";
        }),
    );
    const tabs = $$("#pdTabs a"),
      secs = tabs.map((a) => document.querySelector(a.getAttribute("href")));
    const tio = new IntersectionObserver(
      (es) =>
        es.forEach((e) => {
          if (e.isIntersecting) {
            const i = secs.indexOf(e.target);
            tabs.forEach((a, k) => a.classList.toggle("on", k === i));
          }
        }),
      { rootMargin: "-45% 0px -50% 0px" },
    );
    secs.forEach((x) => x && tio.observe(x));
    if ($("mPrice") && !$("uDld")) {
      const mort = () => {
        const P = digits($("mPrice").value),
          dp = +$("mDp").value,
          r = +$("mRt").value / 1200,
          n = +$("mYr").value * 12,
          L = P * (1 - dp / 100);
        const m = r ? (L * r) / (1 - Math.pow(1 + r, -n)) : L / n;
        $("mDpV").textContent = dp + "%";
        $("mRtV").textContent = (+$("mRt").value).toFixed(1) + "%";
        $("mYrV").textContent = $("mYr").value + " years";
        $("mOut").textContent = P ? aed(m) : "—";
        $("mLoan").textContent = aed(L);
        $("mDown").textContent = aed(P - L);
        $("mInt").textContent = aed(m * n - L);
        ["mDp", "mRt", "mYr"].forEach((id) => fillRange($(id)));
      };
      ["mDp", "mRt", "mYr"].forEach((id) => $(id).addEventListener("input", mort));
      $("mPrice").addEventListener("input", (e) => {
        const v = digits(e.target.value);
        e.target.value = v ? v.toLocaleString("en-US") : "";
        mort();
      });
      mort();
    }
    const dl = $("dl");
    if (dl) {
      $$("[data-dl]").forEach(
        (b) =>
          (b.onclick = () => {
            $("dlEy").textContent = "Download " + b.dataset.dl;
            $("dlT").textContent = b.dataset.dl.toLowerCase();
            dl.showModal();
          }),
      );
      $("dlX").onclick = () => dl.close();
      dl.addEventListener("click", (e) => {
        if (e.target === dl) dl.close();
      });
      $("dlForm").addEventListener("submit", (e) => {
        e.preventDefault();
        const t = $("dlT").textContent;
        e.target.innerHTML = `<span class="eyebrow">Thank you</span><h2 class="serif">Your ${esc(t)} is <em class="gold-text">on its way.</em></h2><p class="lead">${esc(e.target.dataset.done || "A DRE advisor will send it to you shortly.")}</p><button class="btn btn-gold" type="button" onclick="this.closest('dialog').close()" style="justify-content:center">Close</button>`;
      });
    }
    if ($("pdVideo"))
      $("pdVideo").onclick = () => (window.__ai && window.__ai.openPopin ? window.__ai.openPopin() : null);
    const tr = $("simTrack");
    if (tr) {
      const step = () => tr.querySelector(".sim").offsetWidth + 22;
      $("simPrev").onclick = () => tr.scrollBy({ left: -step(), behavior: "smooth" });
      $("simNext").onclick = () => tr.scrollBy({ left: step(), behavior: "smooth" });
    }
  })();

  /* =====================================================================
     Podcast: lite YouTube player (loads only on play), episode switching, share
       episode details = data-yt / data-title / data-sub / data-tag / data-date on each .pd-ep
     ===================================================================== */
  (function () {
    const frame = $("pdFrame");
    if (!frame || !$("pdPoster")) return;
    const eps = $$(".pd-ep[data-yt]");
    const ep = (i) => eps.find((e) => +e.dataset.i === i) || eps[0];
    let cur = 0;
    function setInfo(i) {
      const x = ep(i).dataset;
      $("pdTag").textContent = (i === 0 ? "Latest episode · " : "") + x.tag + (x.date ? " · " + x.date : "");
      $("pdTitle").textContent = x.title;
      $("pdSub").textContent = x.sub;
      $("pdYt").href = "https://www.youtube.com/watch?v=" + x.yt;
      eps.forEach((c) => c.classList.toggle("on", +c.dataset.i === i));
    }
    function play(i, scroll) {
      cur = i;
      setInfo(i);
      const x = ep(i).dataset;
      frame.innerHTML = `<iframe src="https://www.youtube-nocookie.com/embed/${x.yt}?autoplay=1&rel=0${x.start ? "&start=" + x.start : ""}" title="${esc(x.title)}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen" allowfullscreen loading="lazy"></iframe>`;
      if (scroll) $("player").scrollIntoView({ behavior: "smooth", block: "start" });
    }
    $("pdPoster").onclick = () => play(0);
    $$(".pd-eps button[data-i]").forEach((b) => (b.onclick = () => play(+b.dataset.i, true)));
    $("pdShare").onclick = async () => {
      const x = ep(cur).dataset,
        u = "https://www.youtube.com/watch?v=" + x.yt;
      try {
        if (navigator.share) {
          await navigator.share({ title: x.title, url: u });
          return;
        }
        await navigator.clipboard.writeText(u);
        $("pdShare").textContent = "Link copied ✓";
        setTimeout(() => ($("pdShare").textContent = "Share episode"), 1800);
      } catch (e) {}
    };
  })();

  /* =====================================================================
     Careers: role → form, CV drop zone, validation, video
     ===================================================================== */
  /* the photo gallery uses the shared lightbox above (photos = data-src on each thumbnail) */
  /* ---- Careers: role → form, CV drop zone, validation, video + gallery ---- */
  (function () {
    const f = document.getElementById("crForm");
    if (!f) return;
    const $ = (id) => document.getElementById(id);
    document.querySelectorAll(".cr-apply").forEach(
      (b) =>
        (b.onclick = () => {
          $("crPos").value = b.dataset.role;
          $("apply").scrollIntoView({ behavior: "smooth", block: "start" });
          setTimeout(() => $("crName").focus({ preventScroll: true }), 600);
        }),
    );
    /* phone */
    let iti = null;
    const ph = $("crPhone");
    if (window.intlTelInput && ph)
      iti = intlTelInput(ph, {
        initialCountry: "auto",
        geoIpLookup: (ok) => (typeof detectCountry === "function" ? detectCountry(ok) : ok("ae")),
        showSelectedDialCode: true,
        dropdownContainer: document.body,
        nationalMode: true,
        autoPlaceholder: "aggressive",
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/21.2.7/js/utils.min.js",
      });
    /* CV drop zone */
    const drop = $("crDrop"),
      file = $("crFile"),
      OKX = /\.(pdf|docx?|DOCX?|PDF)$/,
      MAX = 5 * 1024 * 1024;
    let cv = null;
    function setFile(fl) {
      if (!fl) return;
      if (!OKX.test(fl.name)) {
        err("Please upload a PDF, DOC or DOCX file.", drop);
        return;
      }
      if (fl.size > MAX) {
        err("That file is over 5 MB — please upload a smaller CV.", drop);
        return;
      }
      cv = fl;
      drop.classList.add("has");
      drop.classList.remove("bad");
      $("crDt").innerHTML =
        `<b>${fl.name.replace(/[<>&"]/g, "")}</b><small>${(fl.size / 1024 / 1024).toFixed(2)} MB · click to replace</small>`;
      $("crErr").textContent = "";
    }
    file.addEventListener("change", () => setFile(file.files[0]));
    ["dragenter", "dragover"].forEach((t) =>
      drop.addEventListener(t, (e) => {
        e.preventDefault();
        drop.classList.add("over");
      }),
    );
    ["dragleave", "drop"].forEach((t) =>
      drop.addEventListener(t, (e) => {
        e.preventDefault();
        drop.classList.remove("over");
      }),
    );
    drop.addEventListener("drop", (e) => {
      const fl = e.dataTransfer.files[0];
      if (fl) {
        try {
          const dt = new DataTransfer();
          dt.items.add(fl);
          file.files = dt.files;
        } catch (x) {}
        setFile(fl);
      }
    });
    function err(msg, el) {
      $("crErr").textContent = msg;
      if (el) {
        el.classList.add("bad");
        (el.focus ? el : el.querySelector("input"))?.focus?.();
      }
    }
    f.addEventListener("input", (e) => e.target.classList.remove("bad"));
    f.addEventListener("change", (e) => e.target.classList.remove("bad"));
    f.addEventListener("submit", (e) => {
      e.preventDefault();
      f.querySelectorAll(".bad").forEach((x) => x.classList.remove("bad"));
      if (!$("crName").value.trim()) return err("Please enter your name.", $("crName"));
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test($("crEmail").value.trim()))
        return err("Please enter a valid email address.", $("crEmail"));
      if (!ph.value.trim() || (iti && window.intlTelInputUtils && !iti.isValidNumber()))
        return err("Please enter a valid phone number.", ph);
      if (!$("crPos").value) return err("Please choose a position.", $("crPos"));
      if (!cv) return err("Please attach your CV.", drop);
      const name = $("crName")
          .value.trim()
          .split(" ")[0]
          .replace(/[<>&"]/g, ""),
        pos = $("crPos").value.replace(/[<>&"]/g, "");
      const showDone = () =>
        (f.innerHTML = `<div class="cr-ok"><span class="eyebrow">Application received</span><h3 class="serif">Thank you, ${name}.</h3><p>Your application for <b>${pos}</b> is with our People &amp; Culture team. If your profile is a match, we’ll be in touch.</p><a class="btn btn-glass" href="our-team.html">Meet the team →</a></div>`);
      /* static preview (no form endpoint): just show the thank-you */
      if (!f.getAttribute("action")) return showDone();
      /* send the application (the dropped CV is attached even when the browser could not put it in the input) */
      const data = new FormData(f);
      data.set("resume", cv, cv.name);
      if (iti) data.set("phone_full", iti.getNumber());
      const btn = f.querySelector('button[type="submit"]'),
        label = btn.textContent;
      btn.disabled = true;
      btn.textContent = "Sending…";
      fetch(f.getAttribute("action"), { method: "POST", body: data })
        .then((r) => r.json())
        .then((res) => {
          if (res && res.status === "success") return showDone();
          err((res && res.msg) || "Something went wrong, please try again.");
        })
        .catch(() => err("We could not send your application. Please check your connection and try again."))
        .finally(() => {
          if (document.body.contains(btn)) {
            btn.disabled = false;
            btn.textContent = label;
          }
        });
    });
    /* video (loads only when opened) */
    const vd = $("crVd"),
      v = $("crVideo");
    const openV = () => {
      const s = v.querySelector("source");
      if (!s.src) {
        s.src = s.dataset.src;
        v.load();
      }
      vd.showModal();
      v.play().catch(() => {});
    };
    $("crPlay").onclick = openV;
    $("crPlayHero").onclick = openV;
    const closeV = () => {
      v.pause();
      vd.close();
    };
    $("crVdX").onclick = closeV;
    vd.addEventListener("click", (e) => {
      if (e.target === vd) closeV();
    });
    vd.addEventListener("close", () => v.pause());
  })();

  /* =====================================================================
     Book a valuation: 3-step booking wizard with live summary
       community suggestions = <datalist id="bvLocList"> in the HTML
     ===================================================================== */
  /* community suggestions = <datalist id="bvLocList"> in the HTML (see "data-suggest" above) */
  /* ---- Book valuation: 3-step booking wizard with live summary ---- */
  (function () {
    const f = document.getElementById("bvForm");
    if (!f) return;
    const $ = (id) => document.getElementById(id),
      steps = [...f.querySelectorAll(".bv-step")],
      prog = [...f.querySelectorAll(".bv-prog li")];
    let cur = 0;
    const NEXT = ["Next: Appointment →", "Next: Your details →"];
    /* dates: next 14 days */
    const D = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
      M = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const t0 = new Date();
    $("bvDates").innerHTML = Array.from({ length: 14 }, (_, i) => {
      const d = new Date(t0.getFullYear(), t0.getMonth(), t0.getDate() + i + 1);
      const v =
        d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, "0") + "-" + String(d.getDate()).padStart(2, "0");
      return `<label class="bv-d"><input type="radio" name="date" value="${v}" data-label="${D[d.getDay()]}, ${d.getDate()} ${M[d.getMonth()]}"><span><small>${i === 0 ? "Tomorrow" : D[d.getDay()]}</small><b>${d.getDate()}</b><em>${M[d.getMonth()]}</em></span></label>`;
    }).join("");
    const val = (n) => {
      const x = f.querySelector(`input[name="${n}"]:checked`);
      return x ? x.value : "";
    };
    const NOBED = ["Land", "Shop", "Office Space"];
    function bedsGate() {
      const no = NOBED.includes(val("ptype"));
      f.querySelectorAll("input[name=beds]").forEach((b) => {
        b.disabled = no;
        if (no) b.checked = false;
      });
      $("bvBedsF").style.opacity = no ? 0.5 : 1;
    }
    function sum() {
      const dl = f.querySelector("input[name=date]:checked");
      const rows = [
        ["Enquiry", val("topic")],
        [
          "Property",
          [val("ptype"), val("beds") && (val("beds") === "Studio" ? "Studio" : val("beds") + " bed")]
            .filter(Boolean)
            .join(" · "),
        ],
        ["Location", $("bvLoc").value.trim()],
        ["Area", $("bvArea").value ? $("bvArea").value + " sq ft" : ""],
        ["Date", dl ? dl.dataset.label : ""],
        ["Time", val("time")],
        ["Name", f.name.value.trim()],
      ];
      $("bvSum").innerHTML = rows
        .map(
          ([k, v]) =>
            `<li class="${v ? "" : "empty"}"><span>${k}</span><b>${v ? v.replace(/[&<>"]/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" })[c]) : "—"}</b></li>`,
        )
        .join("");
    }
    f.addEventListener("change", () => {
      bedsGate();
      sum();
      clearErr();
    });
    f.addEventListener("input", sum);
    $("bvArea").addEventListener("input", (e) => {
      const v = e.target.value.replace(/[^\d]/g, "");
      e.target.value = v ? (+v).toLocaleString("en-US") : "";
    });
    function clearErr() {
      $("bvErr").textContent = "";
      f.querySelectorAll(".bad").forEach((x) => x.classList.remove("bad"));
    }
    let iti = null;
    const ph = $("bvPhone");
    if (window.intlTelInput && ph) {
      iti = intlTelInput(ph, {
        initialCountry: "auto",
        geoIpLookup: (ok) => (typeof detectCountry === "function" ? detectCountry(ok) : ok("ae")),
        showSelectedDialCode: true,
        dropdownContainer: document.body,
        nationalMode: true,
        autoPlaceholder: "aggressive",
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/21.2.7/js/utils.min.js",
      });
    }
    function check(i) {
      const bad = [];
      const need = (el, ok) => {
        if (!ok) {
          el.classList.add("bad");
          bad.push(el);
        }
      };
      if (i === 0) {
        need(f.querySelector("[data-req=topic]"), val("topic"));
        need(f.querySelector("[data-req=ptype]"), val("ptype"));
        need($("bvLoc"), $("bvLoc").value.trim());
      }
      if (i === 1) {
        need($("bvDates"), val("date"));
        need(f.querySelector("[data-req=time]"), val("time"));
      }
      if (i === 2) {
        need($("bvName"), $("bvName").value.trim());
        need($("bvEmail"), /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test($("bvEmail").value.trim()));
        need(ph, ph.value.trim() && (!iti || !window.intlTelInputUtils || iti.isValidNumber()));
      }
      if (bad.length) {
        $("bvErr").textContent =
          i === 2 && bad.includes(ph) && bad.length === 1
            ? "Please enter a valid phone number."
            : "Please complete the highlighted fields.";
        const fc = bad[0].querySelector ? bad[0].querySelector("input:not(:disabled)") || bad[0] : bad[0];
        fc.focus && fc.focus({ preventScroll: false });
      }
      return !bad.length;
    }
    function go(i) {
      steps.forEach((s, k) => (s.hidden = k !== i));
      prog.forEach((p, k) => {
        p.classList.toggle("on", k === i);
        p.classList.toggle("done", k < i);
      });
      cur = i;
      $("bvBack").hidden = i === 0;
      $("bvNext").hidden = i === 2;
      $("bvSubmit").hidden = i !== 2;
      if (i < 2) $("bvNext").textContent = NEXT[i];
      clearErr();
      const t = f.getBoundingClientRect().top;
      if (t < 80 || t > innerHeight * 0.6) f.scrollIntoView({ behavior: "smooth", block: "start" });
    }
    $("bvNext").onclick = () => {
      if (check(cur)) go(cur + 1);
    };
    $("bvBack").onclick = () => go(cur - 1);
    prog.forEach((p, k) => {
      p.style.cursor = "pointer";
      p.onclick = () => {
        if (k < cur) go(k);
        else if (k === cur + 1 && check(cur)) go(k);
      };
    });
    f.addEventListener("keydown", (e) => {
      if (
        e.key === "Enter" &&
        e.target.tagName === "INPUT" &&
        cur < 2 &&
        !document.querySelector('.sg-pop.on,.sg[style*="block"]')
      ) {
        e.preventDefault();
        $("bvNext").click();
      }
    });
    f.addEventListener("submit", (e) => {
      e.preventDefault();
      if (!check(2)) return;
      if (iti) $("bvPhoneFull").value = iti.getNumber();
      const dl = f.querySelector("input[name=date]:checked")?.dataset?.label || val("date"),
        tm = val("time"),
        name = $("bvName")
          .value.trim()
          .split(" ")[0]
          .replace(/[&<>"]/g, "");

      const submitBtn = $("bvSubmit");
      const origText = submitBtn ? submitBtn.textContent : "Book my valuation";
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = "Booking...";
      }
      clearErr();

      const endpoint = (typeof base_url !== "undefined" ? base_url : "/") + "ajax/bookval_frm.php";
      fetch(endpoint, {
        method: "POST",
        body: new FormData(f)
      })
        .then((res) => res.json())
        .then((data) => {
          if (data && data.status === "success") {
            f.innerHTML = `<div class="bv-ok"><span class="mx-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg></span><span class="eyebrow">Booking received</span><h3 class="serif">Thank you, ${name}.</h3><p>Your valuation request for <b>${dl} at ${tm}</b> is in. A member of our Customer Care Team will call you within 24 working hours to confirm.</p><a class="btn btn-glass" href="${typeof base_url !== "undefined" ? base_url : "/"}">Back to home</a></div>`;
            const sumEyebrow = document.querySelector(".bv-sum .eyebrow");
            if (sumEyebrow) sumEyebrow.textContent = "Booking summary";
          } else {
            $("bvErr").textContent = (data && data.msg) || "Something went wrong. Please try again.";
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = origText;
            }
          }
        })
        .catch((err) => {
          console.error("Booking error:", err);
          $("bvErr").textContent = "Connection error. Please try again or call +971 52 754 3243.";
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = origText;
          }
        });
    });
    document.querySelectorAll(".bv-c .mx-go[data-topic]").forEach(
      (b) =>
        (b.onclick = () => {
          const r = f.querySelector(`input[name=topic][value="${b.dataset.topic}"]`);
          if (r) {
            r.checked = true;
            f.dispatchEvent(new Event("change"));
          }
          go(0);
          $("contact").scrollIntoView({ behavior: "smooth" });
        }),
    );
    bedsGate();
    sum();
  })();

  /* =====================================================================
     OUR TEAM — department chips, search, licensed filter, sort, show more, quick view
       each card: data-d (department id), data-lic, data-name, data-role, data-brn,
                  data-tel, data-wa, data-email, data-u; the bio is in the hidden .ot-bio
     ===================================================================== */
  (function () {
    const grid = $("otGrid");
    if (!grid) return;
    const chips = $$("#otDep button");
    const DEP = {};
    chips.forEach((b) => (DEP[b.dataset.d] = label(b)));
    const L = makeList({
      grid,
      mode: "more",
      per: 24,
      state: { d: "all", q: "", lic: false, sort: "team" },
      delay: (j) => (j % 24) * 25 + "ms",
      test: (el, st) =>
        (st.d === "all" || el.dataset.d === st.d) &&
        (!st.lic || el.dataset.lic) &&
        (!st.q ||
          (el.dataset.name + " " + el.dataset.role + " " + DEP[el.dataset.d])
            .toLowerCase()
            .includes(st.q.toLowerCase())),
      sorts: { az: (a, b) => a.dataset.name.localeCompare(b.dataset.name) },
      render: (view, shown, st) => {
        $("otEmpty").hidden = view.length > 0;
        $("otMore").parentElement.hidden = view.length <= st.n;
        $("otMore").textContent = `Show more (${view.length - shown.length} left)`;
        $("otCount").innerHTML = view.length
          ? `Showing <b>${shown.length}</b> of <b>${view.length}</b> people${st.d !== "all" ? ` in <b>${esc(DEP[st.d])}</b>` : ""}`
          : "";
        chips.forEach((b) => b.classList.toggle("on", b.dataset.d === L.st.d));
      },
    });
    recount(chips, L.items, (el, b) => b.dataset.d === "all" || el.dataset.d === b.dataset.d);
    $("otDep").onclick = (e) => {
      const b = e.target.closest("button");
      if (b) L.set("d", b.dataset.d);
    };
    $("otQ").oninput = (e) => L.set("q", e.target.value.trim());
    $("otLic").onchange = (e) => L.set("lic", e.target.checked);
    $("otS").onchange = (e) => {
      L.st.sort = e.target.value;
      L.draw();
    };
    $("otMore").onclick = () => {
      L.st.n += 24;
      L.draw();
    };
    /* quick view */
    const qv = $("otQv");
    const icon = (sel) => {
      const s = grid.querySelector(sel + " svg");
      return s ? s.outerHTML : "";
    };
    const IC = {
      call: icon(".ot-ca a[href^='tel:']"),
      wa: icon(".ot-ca a[href*='wa.me']"),
      mail: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>',
    };
    function open(k) {
      const c = L.items.find((x) => x.dataset.k === String(k));
      if (!c) return;
      const m = c.dataset;
      $("otQvI").src = c.querySelector("img").src;
      $("otQvI").alt = m.name;
      $("otQvD").textContent = DEP[m.d] || "";
      $("otQvN").textContent = m.name;
      $("otQvR").textContent = m.role;
      $("otQvB").textContent = m.brn ? "RERA BRN: " + m.brn : "";
      $("otQvA").innerHTML =
        (m.tel ? `<a href="tel:${esc(m.tel)}">${IC.call}Call</a>` : "") +
        (m.wa ? `<a href="https://wa.me/${esc(m.wa)}" target="_blank" rel="noopener">${IC.wa}WhatsApp</a>` : "") +
        (m.email ? `<a href="mailto:${esc(m.email)}">${IC.mail}Email</a>` : "");
      $("otQvBio").innerHTML = c.querySelector(".ot-bio").innerHTML;
      $("otQvBio").scrollTop = 0;
      $("otQvL").href = "our-team/" + m.u;
      $("otQvL").textContent = "Open full profile →";
      qv.showModal();
    }
    document.addEventListener("click", (e) => {
      const b = e.target.closest("[data-k]");
      if (b && (b.closest("#otGrid") || b.closest(".ot-ld")) && !b.classList.contains("ot-c")) open(b.dataset.k);
    });
    $("otX").onclick = () => qv.close();
    qv.addEventListener("click", (e) => {
      if (e.target === qv) qv.close();
    });
    const depItems = chips
      .filter((b) => b.dataset.d !== "all")
      .map((b) => ({
        t: DEP[b.dataset.d],
        s: plural(L.items.filter((x) => x.dataset.d === b.dataset.d).length, "person", "people"),
        g: "Departments",
        ic: "dev",
        q: "",
        dd: b.dataset.d,
      }))
      .filter((x) => !x.s.startsWith("0 "));
    depItems.forEach((x) => (x.s = x.s.replace(/ (person|people)$/, " people")));
    SUGS.push({
      input: "otQ",
      anchor: "#find .fsearch",
      minW: 400,
      popTitle: "Departments",
      per: 8,
      max: 10,
      popular: depItems.slice(0, 8),
      items: [
        ...depItems,
        ...L.items.map((c) => ({
          t: c.dataset.name,
          s: c.dataset.role,
          g: "People",
          img: c.querySelector("img").src,
          q: "",
          kk: c.dataset.k,
        })),
      ],
      onPick: (it) => {
        if (it.dd) {
          $("otQ").value = "";
          L.st.q = "";
          L.set("d", it.dd);
        } else if (it.kk != null) {
          $("otQ").value = "";
          L.set("q", "");
          open(it.kk);
        }
      },
    });
    L.draw();
  })();

  /* =====================================================================
     BLOGS — topic chips, search with highlight, year, sort, page numbers
       each card: data-c (topic), data-y (year), data-ts (date stamp), data-m (minutes)
     ===================================================================== */
  (function () {
    const grid = $("blGrid");
    if (!grid) return;
    const chips = $$("#blCat button");
    const L = makeList({
      grid,
      mode: "pages",
      per: 9,
      state: { q: "", c: "", y: "", sort: "new" },
      delay: (i) => i * 35 + "ms",
      test: (el, st) =>
        (!st.q || text(el, "h3, .bc-b > p, .btag").includes(st.q.toLowerCase())) &&
        (!st.c || el.dataset.c === st.c) &&
        (!st.y || el.dataset.y === st.y),
      sorts: {
        old: (a, b) => a.dataset.ts - b.dataset.ts,
        quick: (a, b) => a.dataset.m - b.dataset.m || b.dataset.ts - a.dataset.ts,
      },
      render: (view, shown, st, scroll) => {
        L.items.forEach((el) => $$("h3 a, .bc-b > p", el).forEach((x) => mark(x, shown.includes(el) ? st.q : "")));
        $("blEmpty").hidden = view.length > 0;
        $("blCount").innerHTML = view.length
          ? `Showing <b>${(st.page - 1) * 9 + 1}–${Math.min(st.page * 9, view.length)}</b> of <b>${view.length}</b> articles${st.c ? ` in <b>${esc(st.c)}</b>` : ""}${st.y ? ` from <b>${st.y}</b>` : ""}`
          : "";
        pager($("blPager"), st.page, st.pages, "span");
        $("blClear").hidden = !(st.q || st.c || st.y || st.sort !== "new");
        chips.forEach((b) => b.classList.toggle("on", b.dataset.c === st.c));
        if (scroll) $("blCat").scrollIntoView({ behavior: "smooth", block: "start" });
      },
    });
    recount(chips, L.items, (el, b) => !b.dataset.c || el.dataset.c === b.dataset.c);
    $("blCat").onclick = (e) => {
      const b = e.target.closest("button");
      if (b) L.set("c", b.dataset.c);
    };
    $("blQ").oninput = (e) => L.set("q", e.target.value.trim());
    $("blY").onchange = (e) => L.set("y", e.target.value);
    $("blS").onchange = (e) => L.set("sort", e.target.value);
    $("blPager").onclick = (e) => {
      const b = e.target.closest("button[data-p]");
      if (!b || b.disabled) return;
      L.st.page = +b.dataset.p;
      L.draw(true);
    };
    function clear() {
      Object.assign(L.st, { q: "", c: "", y: "", sort: "new", page: 1 });
      $("blQ").value = "";
      $("blY").value = "";
      $("blS").value = "new";
      L.draw();
    }
    $("blClear").onclick = clear;
    $("blEmptyClear").onclick = clear;
    $("blAllGuides").onclick = () => L.set("c", "Guides", true);
    const topics = [...new Set(L.items.map((x) => x.dataset.c))].map((c) => ({
      t: c,
      s: plural(L.items.filter((x) => x.dataset.c === c).length, "article"),
      g: "Topics",
      ic: "dev",
      q: "",
    }));
    const trend = ($("blQ").dataset.trending || "").split("|").filter(Boolean);
    SUGS.push({
      input: "blQ",
      anchor: "#articles .fsearch",
      minW: 420,
      popTitle: "Popular topics",
      per: 6,
      max: 10,
      popular: [
        ...topics,
        ...trend.map((t) => ({
          t,
          s: plural(L.items.filter((x) => text(x, "h3, .bc-b > p, .btag").includes(t.toLowerCase())).length, "article"),
          g: "Trending searches",
          ic: "search",
          q: t,
        })),
      ],
      items: [
        ...topics,
        ...L.items.map((c) => ({
          t: plain(c.querySelector("h3")),
          s:
            c.dataset.c +
            " · " +
            [...c.querySelector(".bmeta").childNodes]
              .find((n) => n.nodeType === 3 && n.nodeValue.trim())
              .nodeValue.trim(),
          g: "Articles",
          img: c.querySelector("img").src,
          href: c.querySelector("h3 a").getAttribute("href"),
        })),
      ],
      onPick: (it) => {
        if (it.g === "Topics") {
          $("blQ").value = "";
          L.st.q = "";
          L.set("c", it.t, true);
        } else if (it.q) L.set("q", it.q, true);
      },
    });
    L.draw();
  })();

  /* =====================================================================
     CLIENT TESTIMONIALS — topic, year, advisor, search, read more
       each card: data-tp (topics), data-y (year), data-ag (advisor ids)
       advisor buttons inside a card carry data-name / data-role
     ===================================================================== */
  (function () {
    const wall = $("tqWall");
    if (!wall) return;
    const LIM = 48;
    const chips = $$("#tqTp button");
    const AG = {};
    $$("[data-ag][data-name]", wall).forEach(
      (b) => (AG[b.dataset.ag] = { n: b.dataset.name, r: b.dataset.role || "", i: (b.querySelector("img") || {}).src }),
    );
    $$(".tq-ag").forEach((b) => {
      if (!AG[b.dataset.ag])
        AG[b.dataset.ag] = {
          n: (b.querySelector("b") || b).textContent.trim(),
          r: "",
          i: (b.querySelector("img") || {}).src,
        };
    });
    const bodyOf = (el) => el.querySelector(":scope > p");
    L0: for (const el of wall.children) {
      const p = bodyOf(el);
      if (p && p.__full === undefined) p.__full = p.innerHTML;
    }
    function body(el, q, open) {
      const p = bodyOf(el);
      p.innerHTML = p.__full;
      p.__h = p.__full;
      p.__hl = false;
      const words = p.textContent.trim().split(/\s+/);
      const long = words.length > LIM && !q && !open;
      let rm = el.querySelector(".tq-rm");
      if (long) {
        p.textContent = words.slice(0, LIM).join(" ") + "…";
        if (!rm) {
          rm = document.createElement("button");
          rm.className = "tq-rm";
          rm.type = "button";
          rm.textContent = "Read more";
          p.after(rm);
        }
      } else {
        if (rm) rm.remove();
        mark(p, q);
      }
    }
    const L = makeList({
      grid: wall,
      mode: "more",
      per: 18,
      state: { tp: "all", y: "", q: "", ag: "" },
      delay: (j) => (j % 18) * 25 + "ms",
      test: (el, st) => {
        const tp = (el.dataset.tp || "").split(" "),
          ag = (el.dataset.ag || "").split(" ").filter(Boolean);
        const hay = (
          bodyOf(el).__full.replace(/<[^>]+>/g, "") +
          " " +
          text(el, ".tq-cf b") +
          " " +
          ag.map((a) => (AG[a] || {}).n).join(" ")
        ).toLowerCase();
        return (
          (st.tp === "all" || (st.tp === "ag" ? ag.length : tp.includes(st.tp))) &&
          (!st.y || el.dataset.y == st.y) &&
          (!st.ag || ag.includes(st.ag)) &&
          (!st.q || hay.includes(st.q.toLowerCase()))
        );
      },
      render: (view, shown, st) => {
        shown.forEach((el) => body(el, st.q));
        $("tqEmpty").hidden = view.length > 0;
        $("tqMore").parentElement.hidden = view.length <= st.n;
        $("tqMore").textContent = `Show more reviews (${view.length - shown.length} left)`;
        $("tqCount").innerHTML = view.length ? `Showing <b>${shown.length}</b> of <b>${view.length}</b> reviews` : "";
        chips.forEach((b) => b.classList.toggle("on", b.dataset.tp === st.tp));
        $$(".tq-ag").forEach((b) => b.classList.toggle("on", b.dataset.ag === st.ag));
        const f = $("tqAgf");
        if (st.ag && AG[st.ag]) {
          f.hidden = false;
          f.innerHTML = `<img src="${AG[st.ag].i}" alt="">${esc(AG[st.ag].n)}<button type="button" aria-label="Clear advisor filter">✕</button>`;
        } else f.hidden = true;
      },
    });
    recount(
      chips,
      L.items,
      (el, b) =>
        b.dataset.tp === "all" ||
        (b.dataset.tp === "ag" ? !!el.dataset.ag : (el.dataset.tp || "").split(" ").includes(b.dataset.tp)),
    );
    wall.addEventListener("click", (e) => {
      const rm = e.target.closest(".tq-rm");
      if (rm) {
        body(rm.closest(".tq-c"), L.st.q, true);
        return;
      }
      const a = e.target.closest("[data-ag]");
      if (a) setAg(a.dataset.ag);
    });
    function setAg(a) {
      L.set("ag", L.st.ag === a ? "" : a);
      $("tqTp").scrollIntoView({ behavior: "smooth", block: "start" });
    }
    $$(".tq-ag").forEach((b) => (b.onclick = () => setAg(b.dataset.ag)));
    $("tqAgf").onclick = (e) => {
      if (e.target.closest("button")) L.set("ag", "");
    };
    $("tqTp").onclick = (e) => {
      const b = e.target.closest("button");
      if (b) L.set("tp", b.dataset.tp);
    };
    $("tqY").onchange = (e) => L.set("y", e.target.value);
    $("tqQ").oninput = (e) => L.set("q", e.target.value.trim());
    $("tqMore").onclick = () => {
      L.st.n += 18;
      L.draw();
    };
    const cnt = {};
    L.items.forEach((el) =>
      (el.dataset.ag || "")
        .split(" ")
        .filter(Boolean)
        .forEach((a) => (cnt[a] = (cnt[a] || 0) + 1)),
    );
    const adv = Object.keys(cnt).filter((a) => AG[a]);
    const kw = ($("tqQ").dataset.trending || "").split("|").filter(Boolean);
    SUGS.push({
      input: "tqQ",
      anchor: "#reviews .fsearch",
      minW: 400,
      popTitle: "Most praised advisors",
      per: 8,
      max: 10,
      popular: adv
        .slice()
        .sort((a, b) => cnt[b] - cnt[a])
        .slice(0, 6)
        .map((a) => ({ t: AG[a].n, s: plural(cnt[a], "review"), g: "Advisors", img: AG[a].i, q: "", ag: a })),
      items: [
        ...adv.map((a) => ({
          t: AG[a].n,
          s: plural(cnt[a], "review") + (AG[a].r ? " · " + AG[a].r : ""),
          g: "Advisors",
          img: AG[a].i,
          q: "",
          ag: a,
        })),
        ...kw.map((t) => ({
          t,
          s: L.items.filter((el) => bodyOf(el).__full.toLowerCase().includes(t.toLowerCase())).length + " reviews",
          g: "Topics",
          ic: "search",
          q: t,
        })),
      ],
      onPick: (it) => {
        if (it.ag) {
          L.st.q = "";
          $("tqQ").value = "";
          setAg(it.ag);
        }
      },
    });
    L.draw();
  })();

  /* =====================================================================
     Property grids (Emaar projects, Palm Jumeirah, Buy, Off-plan) — shared bits
     ===================================================================== */
  const bedsOk = (el, beds) => {
    if (!beds.size) return true;
    const lo = el.dataset.bmin,
      hi = el.dataset.bmax,
      list = el.dataset.blist;
    if (lo === undefined) return false;
    return [...beds].some((b) => {
      const v = b === "STD" ? 0 : +b;
      if (list) return list.split(" ").map(Number).includes(v) || (v === 0 && +lo === 0);
      return v === 5 && b !== "STD" ? +hi >= 5 : v >= +lo && v <= +hi;
    });
  };
  const priceRange = (el, p) => {
    if (!p) return true;
    const v = +el.dataset.p;
    if (p === "req") return !v;
    const [a, b] = p.split("-").map(Number);
    return v && v >= a && v < b;
  };
  const PRICE_SORTS = {
    pa: (a, b) => (+a.dataset.p || 9e15) - (+b.dataset.p || 9e15),
    pd: (a, b) => (+b.dataset.p || -1) - (+a.dataset.p || -1),
    az: (a, b) =>
      (a.dataset.name || plain(a.querySelector("h3"))).localeCompare(b.dataset.name || plain(b.querySelector("h3"))),
  };
  const pagerClick = (L, el, scroll) =>
    (el.onclick = (e) => {
      const b = e.target.closest("button[data-p]");
      if (!b || b.disabled) return;
      L.st.page = +b.dataset.p;
      L.draw(scroll);
    });
  const cardSug = (L, g, sub) =>
    L.items.map((c) => ({
      t: plain(c.querySelector("h3")),
      s: sub(c),
      g,
      img: c.querySelector("img").src,
      href: c.querySelector("h3 a").getAttribute("href"),
    }));
  const money = (v) => (+v ? " · AED " + (+v).toLocaleString("en-US") : "");

  /* ---- Developer page (Emaar): community tiles, search, filters, page numbers ---- */
  (function () {
    const grid = $("pjGrid");
    if (!grid) return;
    const devTitle = (document.querySelector(".dp-head h1")?.textContent || "developer").replace(/\s+/g, " ").trim();
    const L = makeList({
      grid,
      mode: "pages",
      per: 12,
      state: { q: "", c: "", p: "", beds: new Set(), sort: "feat", view: "grid" },
      delay: (i) => i * 35 + "ms",
      test: (el, st) =>
        (!st.q || (el.dataset.name + " " + (el.dataset.l || "")).toLowerCase().includes(st.q.toLowerCase())) &&
        (!st.c || el.dataset.c === st.c) &&
        priceRange(el, st.p) &&
        bedsOk(el, st.beds),
      sorts: PRICE_SORTS,
      render: (view, shown, st, scroll) => {
        grid.className = "opg" + (st.view === "list" ? " list" : "");
        $("pjEmpty").hidden = view.length > 0;
        $("pjCount").innerHTML = view.length
          ? `Showing <b>${(st.page - 1) * 12 + 1}–${Math.min(st.page * 12, view.length)}</b> of <b>${view.length}</b> ${esc(devTitle)} projects${st.c ? ` in <b>${esc(st.c)}</b>` : ""}`
          : "";
        pager($("pjPager"), st.page, st.pages, "button");
        $("pjClear").hidden = !(st.q || st.c || st.p || st.beds.size);
        $$(".ct").forEach((t) =>
          t.classList.toggle("on", t.dataset.c === st.c && (!!st.c || t.classList.contains("ct-all"))),
        );
        if (scroll) $("fbar").scrollIntoView({ behavior: "smooth", block: "start" });
      },
    });
    document.querySelector(".ctiles").onclick = (e) => {
      const t = e.target.closest(".ct");
      if (!t) return;
      $("pjC").value = t.dataset.c;
      L.set("c", t.dataset.c, true);
    };
    $("pjQ").oninput = (e) => L.set("q", e.target.value.trim());
    $("pjC").onchange = (e) => L.set("c", e.target.value);
    $("pjP").onchange = (e) => L.set("p", e.target.value);
    $("pjS").onchange = (e) => L.set("sort", e.target.value);
    $("pjB").onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      const v = b.dataset.b;
      L.st.beds.has(v) ? L.st.beds.delete(v) : L.st.beds.add(v);
      b.classList.toggle("on");
      L.set("page", 1);
    };
    document.querySelector("#fbar .vt").onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      $$("#fbar .vt button").forEach((x) => x.classList.toggle("on", x === b));
      L.st.view = b.dataset.v;
      L.draw();
    };
    pagerClick(L, $("pjPager"), true);
    function clear() {
      Object.assign(L.st, { q: "", c: "", p: "", page: 1 });
      L.st.beds.clear();
      ["pjQ", "pjC", "pjP"].forEach((id) => ($(id).value = ""));
      $$("#pjB button").forEach((b) => b.classList.remove("on"));
      L.draw();
    }
    $("pjClear").onclick = clear;
    $("pjEmptyClear").onclick = clear;
    const cc = {};
    L.items.forEach((el) => (cc[el.dataset.c] = (cc[el.dataset.c] || 0) + 1));
    const comms = Object.keys(cc).filter((c) => c !== "Other Dubai");
    SUGS.push({
      input: "pjQ",
      anchor: ".fsearch",
      minW: 380,
      popTitle: "Top communities",
      per: 5,
      max: 10,
      popular: comms
        .slice()
        .sort((a, b) => cc[b] - cc[a])
        .slice(0, 6)
        .map((c) => ({ t: c, s: cc[c] + " projects", g: "Communities", ic: "pin", q: "" })),
      items: [
        ...comms.map((c) => ({ t: c, s: cc[c] + " projects", g: "Communities", ic: "pin", q: "" })),
        ...cardSug(L, "Projects", (c) => (c.dataset.l || "Dubai") + money(c.dataset.p)),
      ],
      onPick: (it) => {
        if (it.g === "Communities") {
          $("pjC").value = it.t;
          L.set("c", it.t, true);
        }
      },
    });
    L.draw();
  })();

  /* ---- Community page (Palm Jumeirah): developer chips, search, filters, page numbers, map ---- */
  (function () {
    const grid = $("pmGrid");
    if (!grid) return;
    const chips = $$("#pmDev button");
    const L = makeList({
      grid,
      mode: "pages",
      per: 9,
      state: { q: "", d: "", p: "", beds: new Set(), sort: "feat" },
      delay: (i) => i * 35 + "ms",
      test: (el, st) =>
        (!st.q || ((el.dataset.name || "") + " " + el.dataset.d).toLowerCase().includes(st.q.toLowerCase())) &&
        (!st.d || el.dataset.d === st.d) &&
        priceRange(el, st.p) &&
        bedsOk(el, st.beds),
      sorts: PRICE_SORTS,
      render: (view, shown, st, scroll) => {
        $("pmEmpty").hidden = view.length > 0;
        $("pmCount").innerHTML = view.length
          ? `Showing <b>${(st.page - 1) * 9 + 1}–${Math.min(st.page * 9, view.length)}</b> of <b>${view.length}</b> projects${st.d ? ` by <b>${esc(st.d)}</b>` : ""}`
          : "";
        pager($("pmPager"), st.page, st.pages, "all");
        $("pmClear").hidden = !(st.q || st.d || st.p || st.beds.size);
        chips.forEach((b) => b.classList.toggle("on", b.dataset.d === st.d));
        if (scroll) $("pmDev").scrollIntoView({ behavior: "smooth", block: "start" });
      },
    });
    recount(chips, L.items, (el, b) => !b.dataset.d || el.dataset.d === b.dataset.d);
    $("pmDev").onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      $("pmD").value = b.dataset.d;
      L.set("d", b.dataset.d);
    };
    $("pmQ").oninput = (e) => L.set("q", e.target.value.trim());
    $("pmD").onchange = (e) => L.set("d", e.target.value);
    $("pmP").onchange = (e) => L.set("p", e.target.value);
    $("pmS").onchange = (e) => L.set("sort", e.target.value);
    $("pmB").onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      const v = b.dataset.b;
      L.st.beds.has(v) ? L.st.beds.delete(v) : L.st.beds.add(v);
      b.classList.toggle("on");
      L.set("page", 1);
    };
    pagerClick(L, $("pmPager"), true);
    function clear() {
      Object.assign(L.st, { q: "", d: "", p: "", page: 1 });
      L.st.beds.clear();
      ["pmQ", "pmD", "pmP"].forEach((id) => ($(id).value = ""));
      $$("#pmB button").forEach((b) => b.classList.remove("on"));
      L.draw();
    }
    $("pmClear").onclick = clear;
    $("pmEmptyClear").onclick = clear;
    const dc = {};
    L.items.forEach((el) => (dc[el.dataset.d] = (dc[el.dataset.d] || 0) + 1));
    const dev = (d) => ({ t: d, s: plural(dc[d], "project") + " on the Palm", g: "Developers", ic: "dev", q: "" });
    SUGS.push({
      input: "pmQ",
      anchor: ".fsearch",
      minW: 380,
      popTitle: "Top developers on the Palm",
      per: 6,
      max: 10,
      popular: Object.keys(dc)
        .sort((a, b) => dc[b] - dc[a])
        .slice(0, 6)
        .map(dev),
      items: [...Object.keys(dc).map(dev), ...cardSug(L, "Projects", (c) => c.dataset.d + money(c.dataset.p))],
      onPick: (it) => {
        if (it.g === "Developers") {
          $("pmD").value = it.t;
          L.set("d", it.t, true);
        }
      },
    });
    L.draw();
  })();

  /* ---- Favourites (♥) for the Buy and Off-plan lists ---- */
  function favourites(key, onChange) {
    let favs = new Set();
    try {
      favs = new Set(JSON.parse(localStorage.getItem(key) || "[]"));
    } catch (e) {}
    const paint = () => {
      $$(".oc-fav").forEach((x) => {
        const on = favs.has(x.dataset.u);
        x.setAttribute("aria-pressed", on);
        x.textContent = on ? "♥" : "♡";
      });
      $("favCount").textContent = favs.size;
    };
    const toggle = (u) => {
      favs.has(u) ? favs.delete(u) : favs.add(u);
      try {
        localStorage.setItem(key, JSON.stringify([...favs]));
      } catch (e) {}
      paint();
      onChange();
    };
    paint();
    return { has: (u) => favs.has(u), toggle };
  }
  /* "More filters" pop-up on the Buy and Off-plan lists */
  function moreFilters() {
    const mb = $("moreBtn"),
      mf = $("moreF");
    const setMore = (o) => {
      mf.hidden = !o;
      mb.setAttribute("aria-expanded", o);
      mb.classList.toggle("on", o);
    };
    mb.onclick = () => setMore(mf.hidden);
    document.addEventListener("pointerdown", (e) => {
      if (!mf.hidden && !mf.contains(e.target) && !mb.contains(e.target)) setMore(false);
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && !mf.hidden) {
        setMore(false);
        mb.focus();
      }
    });
  }

  /* ---- Buy property: filters, sort, favourites, photo sliders, exclusive carousel, page numbers ----
       each card: data-ty (type), data-c (community), data-p (price), data-bn (beds; 0 = studio),
                  data-bt (baths), data-a (sq ft), data-ppf (price per sq ft), data-u, data-l */
  (function () {
    const grid = $("opGrid");
    if (!grid || !$("tySeg")) return;
    let fav;
    const ppf = (el) => (+el.dataset.p && +el.dataset.a ? el.dataset.p / el.dataset.a : 9e15); // price per sq ft
    const noun = $("rCount").dataset.noun || "homes for sale"; // e.g. "homes for rent" on the Rent page
    const L = makeList({
      grid,
      mode: "pages",
      per: 9,
      state: {
        q: "",
        ty: "",
        loc: "",
        min: 0,
        max: 0,
        beds: new Set(),
        bath: 0,
        size: 0,
        sort: "feat",
        fav: false,
        view: "grid",
      },
      delay: (i) => i * 45 + "ms",
      test: (el, st) => {
        const d = el.dataset,
          p = +d.p,
          bn = d.bn === undefined ? null : +d.bn;
        return (
          (!st.q || (text(el, "h3") + " " + (d.l || "")).toLowerCase().includes(st.q.toLowerCase())) &&
          (!st.ty || d.ty === st.ty) &&
          (!st.loc || d.c === st.loc) &&
          (!st.min || (p && p >= st.min)) &&
          (!st.max || (p && p <= st.max)) &&
          (!st.beds.size || [...st.beds].some((b) => (b === "STD" ? bn === 0 : b === "5" ? bn >= 5 : bn === +b))) &&
          (!st.bath || +d.bt >= st.bath) &&
          (!st.size || +d.a >= st.size) &&
          (!st.fav || fav.has(d.u))
        );
      },
      sorts: {
        pa: PRICE_SORTS.pa,
        pd: PRICE_SORTS.pd,
        sz: (a, b) => b.dataset.a - a.dataset.a,
        ppf: (a, b) => ppf(a) - ppf(b),
      },
      render: (view, shown, st, scroll) => {
        grid.className = "opg" + (st.view === "list" ? " list" : "");
        $("opEmpty").hidden = view.length > 0;
        $("rCount").innerHTML = view.length
          ? `Showing <b>${(st.page - 1) * 9 + 1}–${Math.min(st.page * 9, view.length)}</b> of <b>${view.length}</b> ${noun}`
          : "";
        pager($("pager"), st.page, st.pages, "button");
        $("clearAll").hidden = !(
          st.q ||
          st.ty ||
          st.loc ||
          st.min ||
          st.max ||
          st.beds.size ||
          st.bath ||
          st.size ||
          st.fav
        );
        const nb = st.beds.size + (st.bath ? 1 : 0) + (st.size ? 1 : 0),
          bc = $("bedCount");
        bc.textContent = nb || "";
        bc.classList.toggle("on", nb > 0);
        $$("#quickC button").forEach((b) => b.classList.toggle("on", b.dataset.c === st.loc));
        if (scroll) $("listing").scrollIntoView({ behavior: "smooth" });
      },
    });
    fav = favourites(grid.dataset.favKey || "dre-favs-buy", () => {
      if (L.st.fav) L.draw();
    });
    const set = (k, v) => L.set(k, v);
    $("opSearch").onsubmit = (e) => {
      e.preventDefault();
      L.set("q", $("qIn").value.trim(), true);
    };
    $("qIn").oninput = (e) => set("q", e.target.value.trim());
    $("quickC").onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      const c = L.st.loc === b.dataset.c ? "" : b.dataset.c;
      $("locSel").value = c;
      L.set("loc", c, true);
    };
    $("tySeg").onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      $$("#tySeg button").forEach((x) => x.classList.toggle("on", x === b));
      set("ty", b.dataset.t);
    };
    $("locSel").onchange = (e) => set("loc", e.target.value);
    $("pMin").onchange = (e) => set("min", +e.target.value || 0);
    $("pMax").onchange = (e) => set("max", +e.target.value || 0);
    $("sortSel").onchange = (e) => set("sort", e.target.value);
    moreFilters();
    $("bedChips").onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      const v = b.dataset.b;
      L.st.beds.has(v) ? L.st.beds.delete(v) : L.st.beds.add(v);
      b.classList.toggle("on");
      L.set("page", 1);
    };
    const single = (id, key, attr) =>
      ($(id).onclick = (e) => {
        const b = e.target.closest("button");
        if (!b) return;
        const v = +b.dataset[attr];
        const on = L.st[key] !== v;
        $$("#" + id + " button").forEach((x) => x.classList.toggle("on", on && x === b));
        set(key, on ? v : 0);
      });
    single("bathChips", "bath", "b");
    single("sizeChips", "size", "s");
    $("favOnly").onclick = (e) => {
      L.st.fav = !L.st.fav;
      e.currentTarget.setAttribute("aria-pressed", L.st.fav);
      L.st.page = 1;
      L.draw();
    };
    const ex = $("exTrack");
    function onCards(e) {
      const f = e.target.closest(".oc-fav");
      if (f) {
        fav.toggle(f.dataset.u);
        return;
      }
      const nv = e.target.closest(".oc-nav"),
        oc = e.target.closest(".oc");
      if (nv && oc) {
        e.preventDefault();
        const n = oc.querySelectorAll(".oc-slides img").length;
        const i = (+(oc.dataset.i || 0) + (nv.classList.contains("n") ? 1 : -1) + n) % n;
        oc.dataset.i = i;
        oc.querySelector(".oc-slides").style.transform = `translateX(-${i * 100}%)`;
        $$(".oc-dots i", oc).forEach((d, k) => d.classList.toggle("on", k === i));
        return;
      }
      const im = e.target.closest(".oc-img");
      if (im && im.dataset.href) location.href = im.dataset.href;
    }
    grid.addEventListener("click", onCards);
    ex.addEventListener("click", onCards);
    let tx = 0;
    [grid, ex].forEach((c) => {
      c.addEventListener("touchstart", (e) => (tx = e.touches[0].clientX), { passive: true });
      c.addEventListener(
        "touchend",
        (e) => {
          const im = e.target.closest(".oc-img");
          if (!im) return;
          const dx = e.changedTouches[0].clientX - tx;
          if (Math.abs(dx) > 40) {
            const b = im.querySelector(dx < 0 ? ".oc-nav.n" : ".oc-nav.p");
            if (b) b.click();
          }
        },
        { passive: true },
      );
    });
    document.querySelector(".vt").onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      $$(".vt button").forEach((x) => x.classList.toggle("on", x === b));
      L.st.view = b.dataset.v;
      L.draw();
    };
    pagerClick(L, $("pager"), true);
    const stp = () => (ex.querySelector(".oc") || { offsetWidth: 300 }).offsetWidth + 22;
    $("exPrev").onclick = () => ex.scrollBy({ left: -stp(), behavior: "smooth" });
    $("exNext").onclick = () => ex.scrollBy({ left: stp(), behavior: "smooth" });
    function clearAll() {
      Object.assign(L.st, { q: "", ty: "", loc: "", min: 0, max: 0, bath: 0, size: 0, fav: false, page: 1 });
      L.st.beds.clear();
      ["qIn", "locSel", "pMin", "pMax"].forEach((id) => ($(id).value = ""));
      $$("#bedChips button,#bathChips button,#sizeChips button").forEach((b) => b.classList.remove("on"));
      $$("#tySeg button").forEach((b, i) => b.classList.toggle("on", i === 0));
      $("favOnly").setAttribute("aria-pressed", false);
      L.draw();
    }
    $("clearAll").onclick = clearAll;
    $("emptyClear").onclick = clearAll;
    const cc = {};
    L.items.forEach((el) => (cc[el.dataset.c] = (cc[el.dataset.c] || 0) + 1));
    const comms = Object.keys(cc).sort((a, b) => cc[b] - cc[a]);
    const comm = (c) => ({
      t: c,
      s: cc[c] + " home" + (cc[c] > 1 ? "s" : "") + noun.replace(/^homes/, ""),
      g: "Communities",
      ic: "pin",
      q: "",
    });
    SUGS.push({
      input: "qIn",
      popTitle: "Popular communities",
      per: 5,
      max: 10,
      popular: comms.slice(0, 6).map(comm),
      items: [
        ...comms.map(comm),
        ...cardSug(
          L,
          "Listings",
          (c) =>
            c.dataset.ty +
            " · " +
            (c.dataset.bn === "0" ? "Studio" : c.dataset.bn + " bed") +
            " · AED " +
            (+c.dataset.p).toLocaleString("en-US"),
        ),
      ],
      onPick: (it) => {
        if (it.g !== "Communities") return;
        $("locSel").value = it.t;
        $("locSel").dispatchEvent(new Event("change"));
        $("listing").scrollIntoView({ behavior: "smooth" });
      },
    });
    L.draw();
  })();

  /* ---- Off-plan properties: emirate, developer, area, price, beds, favourites, page numbers ----
       each card: data-e (emirate), data-d (developer), data-l (area), data-p (price),
                  data-bmin / data-bmax / data-blist (bedrooms), data-u, data-name */
  (function () {
    const grid = $("opGrid");
    if (!grid || !$("emSeg")) return;
    const segKey = $("emSeg").dataset.key || "e"; // emirate (Off-plan) or type (Ready To Move In)
    let fav;
    const L = makeList({
      grid,
      mode: "pages",
      per: 9,
      state: {
        q: "",
        em: "",
        dev: "",
        loc: "",
        min: 0,
        max: 0,
        beds: new Set(),
        sort: "feat",
        fav: false,
        view: "grid",
      },
      delay: (i) => i * 45 + "ms",
      test: (el, st) => {
        const d = el.dataset,
          p = +d.p;
        return (
          (!st.q || (d.name + " " + d.d + " " + (d.l || "")).toLowerCase().includes(st.q.toLowerCase())) &&
          (!st.em || d[segKey] === st.em) &&
          (!st.dev || d.d === st.dev) &&
          (!st.loc || d.l === st.loc) &&
          (!st.min || (p && p >= st.min)) &&
          (!st.max || (p && p <= st.max)) &&
          bedsOk(el, st.beds) &&
          (!st.fav || fav.has(d.u))
        );
      },
      sorts: PRICE_SORTS,
      render: (view, shown, st, scroll) => {
        grid.className = "opg" + (st.view === "list" ? " list" : "");
        $("opEmpty").hidden = view.length > 0;
        $("rCount").innerHTML = view.length
          ? `Showing <b>${(st.page - 1) * 9 + 1}–${Math.min(st.page * 9, view.length)}</b> of <b>${view.length}</b> projects`
          : "";
        pager($("pager"), st.page, st.pages, "button");
        $("clearAll").hidden = !(st.q || st.em || st.dev || st.loc || st.min || st.max || st.beds.size || st.fav);
        const bc = $("bedCount");
        bc.textContent = st.beds.size || "";
        bc.classList.toggle("on", st.beds.size > 0);
        $$("#quickDev button").forEach((b) => b.classList.toggle("on", b.dataset.d === st.dev));
        if (scroll) $("listing").scrollIntoView({ behavior: "smooth" });
      },
    });
    fav = favourites(grid.dataset.favKey || "dre-favs", () => {
      if (L.st.fav) L.draw();
    });
    const set = (k, v) => L.set(k, v);
    $("opSearch").onsubmit = (e) => {
      e.preventDefault();
      L.set("q", $("qIn").value.trim(), true);
    };
    $("qIn").oninput = (e) => set("q", e.target.value.trim());
    $("quickDev").onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      const d = L.st.dev === b.dataset.d ? "" : b.dataset.d;
      $("devSel").value = d;
      L.set("dev", d, true);
    };
    $("emSeg").onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      $$("#emSeg button").forEach((x) => x.classList.toggle("on", x === b));
      set("em", b.dataset.e);
    };
    $("devSel").onchange = (e) => set("dev", e.target.value);
    $("locSel").onchange = (e) => set("loc", e.target.value);
    $("pMin").onchange = (e) => set("min", +e.target.value || 0);
    $("pMax").onchange = (e) => set("max", +e.target.value || 0);
    $("sortSel").onchange = (e) => set("sort", e.target.value);
    moreFilters();
    $("bedChips").onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      const v = b.dataset.b;
      L.st.beds.has(v) ? L.st.beds.delete(v) : L.st.beds.add(v);
      b.classList.toggle("on");
      L.set("page", 1);
    };
    $("favOnly").onclick = (e) => {
      L.st.fav = !L.st.fav;
      e.currentTarget.setAttribute("aria-pressed", L.st.fav);
      L.st.page = 1;
      L.draw();
    };
    grid.addEventListener("click", (e) => {
      const f = e.target.closest(".oc-fav");
      if (f) fav.toggle(f.dataset.u);
    });
    document.querySelector(".vt").onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      $$(".vt button").forEach((x) => x.classList.toggle("on", x === b));
      L.st.view = b.dataset.v;
      L.draw();
    };
    pagerClick(L, $("pager"), true);
    function clearAll() {
      Object.assign(L.st, { q: "", em: "", dev: "", loc: "", min: 0, max: 0, fav: false, page: 1 });
      L.st.beds.clear();
      ["qIn", "devSel", "locSel", "pMin", "pMax"].forEach((id) => ($(id).value = ""));
      $$("#bedChips button").forEach((b) => b.classList.remove("on"));
      $$("#emSeg button").forEach((b, i) => b.classList.toggle("on", i === 0));
      $("favOnly").setAttribute("aria-pressed", false);
      L.draw();
    }
    $("clearAll").onclick = clearAll;
    $("emptyClear").onclick = clearAll;
    const count = (f) => L.items.reduce((m, el) => ((m[f(el)] = (m[f(el)] || 0) + 1), m), {});
    const dc = count((el) => el.dataset.d),
      lc = count((el) => el.dataset.l || "");
    const devs = Object.keys(dc).sort((a, b) => dc[b] - dc[a]);
    SUGS.push({
      input: "qIn",
      popTitle: "Popular projects",
      per: 4,
      max: 9,
      items: [
        ...cardSug(L, "Projects", (c) => c.dataset.d + " · " + (c.dataset.l || c.dataset.e)),
        ...devs.map((d) => ({
          t: d,
          s: plural(dc[d], "project") + " in this collection",
          g: "Developers",
          ic: "dev",
          q: "",
        })),
        ...Object.keys(lc)
          .filter(Boolean)
          .map((l) => ({ t: l, s: plural(lc[l], "project"), g: "Areas", ic: "pin", q: "" })),
      ],
      onPick: (it) => {
        if (it.g === "Developers") {
          $("devSel").value = it.t;
          $("devSel").dispatchEvent(new Event("change"));
        } else if (it.g === "Areas") {
          $("locSel").value = it.t;
          $("locSel").dispatchEvent(new Event("change"));
        }
        $("listing").scrollIntoView({ behavior: "smooth" });
      },
    });
    L.draw();
  })();

  /* =====================================================================
     OUR MEDIA — category chips, search, show more, lightbox with thumbnail strip, stories
       each photo: data-k (photo number), data-c (category); stories: data-ix="k,k,k"
     ===================================================================== */
  (function () {
    const wall = $("mdWall");
    if (!wall) return;
    const chips = $$("#mdCat button");
    const CN = {};
    chips.forEach((b) => (CN[b.dataset.c] = label(b)));
    const L = makeList({
      grid: wall,
      mode: "more",
      per: 24,
      state: { c: "all", q: "" },
      delay: (j) => (j % 24) * 25 + "ms",
      test: (el, st) =>
        (st.c === "all" || el.dataset.c === st.c) &&
        (!st.q || (el.textContent + " " + CN[el.dataset.c]).toLowerCase().includes(st.q.toLowerCase())),
      render: (view, shown, st) => {
        $("mdEmpty").hidden = view.length > 0;
        $("mdMore").parentElement.hidden = view.length <= st.n;
        $("mdMore").textContent = `Show more photos (${view.length - shown.length} left)`;
        $("mdCount").innerHTML = view.length ? `Showing <b>${shown.length}</b> of <b>${view.length}</b> photos` : "";
        chips.forEach((b) => b.classList.toggle("on", b.dataset.c === st.c));
      },
    });
    recount(chips, L.items, (el, b) => b.dataset.c === "all" || el.dataset.c === b.dataset.c);
    $("mdCat").onclick = (e) => {
      const b = e.target.closest("button");
      if (b) L.set("c", b.dataset.c);
    };
    $("mdQ").oninput = (e) => L.set("q", e.target.value.trim());
    $("mdMore").onclick = () => {
      L.st.n += 24;
      L.draw();
    };
    const ph = (el) => ({ src: el.querySelector("img").src, title: el.textContent.trim(), c: el.dataset.c, el });
    const strip = $("mdStrip");
    const lb = lightbox(
      $("mdLb"),
      { img: "mdLbImg", title: "mdLbT", count: "mdLbN", prev: "mdPv", next: "mdNx", close: "mdX" },
      (cur, set, p) => {
        $("mdLbC").textContent = CN[p.c] || "";
        if (set.length <= 40) {
          strip.hidden = false;
          $$("button", strip).forEach((b, k) => b.classList.toggle("on", k === cur));
          const on = strip.children[cur];
          if (on) on.scrollIntoView({ inline: "center", block: "nearest" });
        } else strip.hidden = true;
      },
    );
    function open(list, i) {
      strip.innerHTML =
        list.length <= 40
          ? list
              .map(
                (p, k) =>
                  `<button type="button" data-k="${k}" aria-label="Photo ${k + 1}"><img src="${p.src}" alt="" loading="lazy"></button>`,
              )
              .join("")
          : "";
      $("mdPv").disabled = $("mdNx").disabled = list.length < 2;
      lb.open(list, i);
    }
    wall.onclick = (e) => {
      const b = e.target.closest(".md-ph");
      if (b) open(L.view.map(ph), L.view.indexOf(b));
    };
    $$(".md-st").forEach(
      (b) =>
        (b.onclick = () =>
          open(
            b.dataset.ix
              .split(",")
              .map((k) => L.items.find((el) => el.dataset.k === k.trim()))
              .filter(Boolean)
              .map(ph),
            0,
          )),
    );
    strip.onclick = (e) => {
      const b = e.target.closest("button");
      if (b) lb.show(+b.dataset.k);
    };
    const tc = {};
    L.items.forEach((el) => {
      const t = el.textContent.trim();
      tc[t] = (tc[t] || 0) + 1;
    });
    const pop = ($("mdQ").dataset.trending || "").split("|").filter(Boolean);
    SUGS.push({
      input: "mdQ",
      anchor: "#wall .fsearch",
      minW: 380,
      popTitle: "Popular searches",
      per: 6,
      max: 10,
      popular: pop.map((t) => ({
        t,
        s: L.items.filter((el) => el.textContent.toLowerCase().includes(t.toLowerCase())).length + " photos",
        g: "Popular",
        ic: "search",
        q: t,
      })),
      items: [
        ...chips
          .filter((b) => b.dataset.c !== "all")
          .map((b) => ({
            t: CN[b.dataset.c],
            s: L.items.filter((el) => el.dataset.c === b.dataset.c).length + " photos",
            g: "Categories",
            ic: "dev",
            q: "",
            c: b.dataset.c,
          })),
        ...Object.keys(tc).map((t) => ({
          t,
          s: tc[t] + (tc[t] > 1 ? " photos" : " photo"),
          g: "Moments",
          ic: "search",
          q: t,
        })),
      ],
      onPick: (it) => {
        if (it.c) {
          $("mdQ").value = "";
          L.st.q = "";
          L.set("c", it.c);
        }
      },
    });
    L.draw();
  })();

  /* =====================================================================
     Year timelines (Our Awards, Company News): chips, search, year rail, scrollspy
     ===================================================================== */
  function timeline(o) {
    const tl = o.tl,
      groups = $$(o.group, tl),
      cards = $$(o.card, tl);
    const st = { f: "", q: "" };
    let view = cards;
    function spy() {
      let k = null;
      groups.forEach((y) => {
        if (!y.hidden && y.getBoundingClientRect().top < innerHeight * 0.35) k = y.id.slice(o.idPrefix.length);
      });
      if (k === null) {
        const f = groups.find((y) => !y.hidden);
        k = f && f.id.slice(o.idPrefix.length);
      }
      $$("#" + o.years + " button").forEach((b) => b.classList.toggle("on", b.dataset.y === k));
    }
    function draw() {
      const q = st.q.toLowerCase();
      view = cards.filter((c) => (!st.f || c.dataset[o.key] === st.f) && (!q || o.search(c).toLowerCase().includes(q)));
      cards.forEach((c) => {
        c.hidden = !view.includes(c);
        o.marks.forEach((sel) => $$(sel, c).forEach((x) => mark(x, st.q)));
      });
      const by = {};
      groups.forEach((g) => {
        const n = $$(o.card, g).filter((c) => !c.hidden).length;
        g.hidden = !n;
        by[g.id.slice(o.idPrefix.length)] = n;
        const s = g.querySelector(o.groupCount);
        if (s) s.textContent = o.groupText(g, n);
      });
      $(o.empty).hidden = view.length > 0;
      $(o.count).innerHTML = view.length ? o.countText(view.length, st.f) : "";
      $$("#" + o.years + " button").forEach((b) => {
        b.disabled = !by[b.dataset.y];
        b.querySelector("b").textContent = by[b.dataset.y] || 0;
      });
      $$("#" + o.chips + " button").forEach((b) =>
        b.classList.toggle("on", (b.dataset[o.chipKey] || "") === (st.f || o.allValue || "")),
      );
      spy();
    }
    recount(
      $$("#" + o.chips + " button"),
      cards,
      (c, b) =>
        !b.dataset[o.chipKey] ||
        b.dataset[o.chipKey] === (o.allValue || "") ||
        c.dataset[o.key] === b.dataset[o.chipKey],
    );
    $(o.chips).onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      const v = b.dataset[o.chipKey] || "";
      st.f = v === (o.allValue || "") ? "" : v;
      draw();
    };
    $(o.input).oninput = (e) => {
      st.q = e.target.value.trim();
      draw();
    };
    $(o.years).onclick = (e) => {
      const b = e.target.closest("button");
      if (!b || b.disabled) return;
      const t = $(o.idPrefix + b.dataset.y);
      if (!t) return;
      const top = t.getBoundingClientRect().top + scrollY - 100;
      window.scrollTo({ top, behavior: Math.abs(top - scrollY) > 2500 ? "auto" : "smooth" });
    };
    addEventListener("scroll", spy, { passive: true });
    draw();
    return {
      st,
      draw,
      cards,
      get view() {
        return view;
      },
      setFilter(v) {
        st.f = v;
        draw();
      },
    };
  }

  /* ---- OUR AWARDS: each card has data-by (partner), data-y (year), data-k, data-img, data-title;
          the full write-up is in the hidden .aw-x paragraph ---- */
  (function () {
    const tl = $("awTl");
    if (!tl) return;
    const T = timeline({
      tl,
      group: ".aw-yr",
      card: ".aw-c",
      idPrefix: "ay",
      key: "by",
      chips: "awBy",
      chipKey: "b",
      years: "awYears",
      input: "awQ",
      empty: "awEmpty",
      count: "awCount",
      groupCount: ".aw-yl small",
      groupText: (g, n) => (g.id === "ay0" ? "Undated · " : "") + plural(n, "award"),
      countText: (n, f) => `<b>${n}</b> award${n > 1 ? "s" : ""}${f ? ` from <b>${esc(f)}</b>` : ""}`,
      search: (c) =>
        c.dataset.title +
        " " +
        (c.querySelector(".aw-x") || { textContent: "" }).textContent +
        " " +
        c.dataset.by +
        " " +
        c.dataset.y,
      marks: [".aw-cb > b"],
    });
    const dlg = $("awDlg");
    let list = T.cards,
      cur = 0;
    function show(i) {
      cur = (i + list.length) % list.length;
      const a = list[cur].dataset;
      $("awDI").src = a.img;
      $("awDI").alt = a.title;
      $("awDM").textContent = a.by + (a.y ? " · " + a.y : "");
      $("awDT").textContent = a.title;
      const x = list[cur].querySelector(".aw-x");
      $("awDX").innerHTML = x ? `<p>${esc(x.textContent)}</p>` : "";
      $("awDX").parentElement.scrollTop = 0;
    }
    const cardOf = (k) => T.cards.find((c) => c.dataset.k === String(k));
    function openK(k, fromTl) {
      list = fromTl ? T.view : T.cards;
      const i = list.indexOf(cardOf(k));
      dlg.showModal();
      show(i < 0 ? 0 : i);
    }
    document.addEventListener("click", (e) => {
      const b = e.target.closest("[data-k]");
      if (!b || b.classList.contains("aw-c") || !(b.closest("#awTl") || b.closest(".aw-fg"))) return;
      openK(b.dataset.k, !!b.closest("#awTl"));
    });
    $("awPv").onclick = () => show(cur - 1);
    $("awNx").onclick = () => show(cur + 1);
    $("awX").onclick = () => dlg.close();
    dlg.addEventListener("click", (e) => {
      if (e.target === dlg) dlg.close();
    });
    dlg.addEventListener("keydown", (e) => {
      if (e.key === "ArrowRight") show(cur + 1);
      if (e.key === "ArrowLeft") show(cur - 1);
    });
    const byK = T.cards.slice().sort((a, b) => a.dataset.k - b.dataset.k);
    const bc = {};
    byK.forEach((c) => (bc[c.dataset.by] = (bc[c.dataset.by] || 0) + 1));
    const partners = Object.keys(bc).filter((b) => b !== "Other");
    const pItem = (b) => ({ t: b, s: plural(bc[b], "award"), g: "Partners", ic: "dev", q: "", bb: b });
    SUGS.push({
      input: "awQ",
      anchor: "#wall .fsearch",
      minW: 400,
      popTitle: "Awarding partners",
      per: 8,
      max: 10,
      popular: partners
        .slice()
        .sort((a, b) => bc[b] - bc[a])
        .slice(0, 6)
        .map(pItem),
      items: [
        ...partners.map(pItem),
        ...byK.map((c) => ({
          t: c.dataset.title,
          s: c.dataset.by + (c.dataset.y ? " · " + c.dataset.y : ""),
          g: "Awards",
          img: c.dataset.img,
          q: "",
          kk: c.dataset.k,
        })),
      ],
      onPick: (it) => {
        if (it.bb) {
          T.st.q = "";
          $("awQ").value = "";
          T.setFilter(it.bb);
        } else if (it.kk != null) {
          $("awQ").value = "";
          T.st.q = "";
          T.draw();
          openK(it.kk, false);
        }
      },
    });
  })();

  /* ---- COMPANY NEWS: each story has data-c (category) and data-y (year) ---- */
  (function () {
    const tl = $("cnTl");
    if (!tl) return;
    const CN = {};
    $$("#cnCat button").forEach((b) => (CN[b.dataset.c] = label(b)));
    const T = timeline({
      tl,
      group: ".cn-yr",
      card: ".cn-c",
      idPrefix: "y",
      key: "c",
      chips: "cnCat",
      chipKey: "c",
      allValue: "all",
      years: "cnYears",
      input: "cnQ",
      empty: "cnEmpty",
      count: "cnCount",
      groupCount: ".cn-yl small",
      groupText: (g, n) => n + " " + (n > 1 ? "stories" : "story"),
      countText: (n, f) => `<b>${n}</b> ${n > 1 ? "stories" : "story"}${f ? ` in <b>${esc(CN[f])}</b>` : ""}`,
      search: (c) => text(c, "h3, .cn-cb > p"),
      marks: ["h3", ".cn-cb > p"],
    });
    const pop = ($("cnQ").dataset.trending || "").split("|").filter(Boolean);
    SUGS.push({
      input: "cnQ",
      anchor: "#news .fsearch",
      minW: 420,
      popTitle: "Popular searches",
      per: 6,
      max: 10,
      popular: pop.map((t) => ({
        t,
        s: T.cards.filter((c) => text(c, "h3, .cn-cb > p").includes(t.toLowerCase())).length + " stories",
        g: "Popular",
        ic: "search",
        q: t,
      })),
      items: [
        ...Object.keys(CN)
          .filter((k) => k !== "all" && T.cards.some((c) => c.dataset.c === k))
          .map((k) => ({
            t: CN[k],
            s: T.cards.filter((c) => c.dataset.c === k).length + " stories",
            g: "Categories",
            ic: "dev",
            q: "",
            c: k,
          })),
        ...T.cards.map((c) => ({
          t: plain(c.querySelector("h3")),
          s: CN[c.dataset.c] + " · " + c.querySelector(".bmeta").textContent.trim(),
          g: "Stories",
          img: c.querySelector("img").src,
          href: c.getAttribute("href"),
        })),
      ],
      onPick: (it) => {
        if (it.c) {
          T.st.q = "";
          $("cnQ").value = "";
          T.setFilter(it.c);
        }
      },
    });
  })();

  /* =====================================================================
     DEVELOPERS directory — search, A–Z, sort, show more
       each card: data-l (first letter), data-top (featured position, if any)
     ===================================================================== */
  (function () {
    const grid = $("dvGrid");
    if (!grid) return;
    const az = $("azBar");
    const top = (el) => (el.dataset.top === undefined ? 99 : +el.dataset.top);
    const L = makeList({
      grid,
      mode: "more",
      per: 24,
      state: { q: "", l: "", sort: "feat" },
      delay: (i) => Math.min(i, 12) * 35 + "ms",
      test: (el, st) => (!st.q || text(el, "h3, p").includes(st.q.toLowerCase())) && (!st.l || el.dataset.l === st.l),
      sorts: {
        feat: (a, b) => top(a) - top(b) || a.__i - b.__i,
        az: (a, b) => plain(a.querySelector("h3")).localeCompare(plain(b.querySelector("h3"))),
        za: (a, b) => plain(b.querySelector("h3")).localeCompare(plain(a.querySelector("h3"))),
      },
      render: (view, shown, st) => {
        L.items.forEach((el) =>
          el.classList.toggle("top", el.dataset.top !== undefined && st.sort === "feat" && !st.q && !st.l),
        );
        $("dvEmpty").hidden = view.length > 0;
        $("dvCount").innerHTML = view.length
          ? `Showing <b>${shown.length}</b> of <b>${view.length}</b> developers${st.l ? ` starting with “${st.l}”` : ""}${st.q ? ` matching “${esc(st.q)}”` : ""}`
          : "";
        const m = $("dvMore");
        m.hidden = view.length <= st.n;
        m.textContent = `Show more developers (${view.length - shown.length} more)`;
      },
    });
    const has = new Set(L.items.map((el) => el.dataset.l));
    $$("button[data-l]", az).forEach((b) => b.dataset.l && (b.disabled = !has.has(b.dataset.l)));
    $("dvSearch").onsubmit = (e) => {
      e.preventDefault();
      L.set("q", $("dvQ").value.trim());
      $("directory").scrollIntoView({ behavior: "smooth" });
    };
    $("dvQ").oninput = (e) => L.set("q", e.target.value.trim());
    az.onclick = (e) => {
      const b = e.target.closest("button");
      if (!b || b.disabled) return;
      $$("button", az).forEach((x) => x.classList.toggle("on", x === b));
      L.set("l", b.dataset.l);
    };
    $("dvSort").onchange = (e) => {
      L.st.sort = e.target.value;
      L.draw();
    };
    $("dvMore").onclick = () => {
      L.st.n += 24;
      L.draw();
    };
    $("dvClear").onclick = () => {
      L.st.l = "";
      $("dvQ").value = "";
      $$("button", az).forEach((x, i) => x.classList.toggle("on", i === 0));
      L.set("q", "");
    };
    const dv = (c) => ({
      t: plain(c.querySelector("h3")),
      s: c.querySelector("p").textContent.trim(),
      g: "Developers",
      img: c.querySelector("img").src,
      logo: 1,
      href: c.getAttribute("href"),
    });
    SUGS.push({
      input: "dvQ",
      popTitle: "Top partners",
      per: 8,
      max: 8,
      popular: L.items
        .filter((c) => c.dataset.top !== undefined)
        .sort((a, b) => top(a) - top(b))
        .map(dv),
      items: L.items.map(dv),
    });
    L.draw();
  })();

  /* =====================================================================
     COMMUNITIES directory — region, A–Z, search, show more
       each card: data-r (region), data-l (first letter), data-pc (listings, for "Popular")
     ===================================================================== */
  (function () {
    const grid = $("cmGrid");
    if (!grid) return;
    const az = $("cmAz");
    const L = makeList({
      grid,
      mode: "more",
      per: 16,
      state: { q: "", r: "", l: "" },
      delay: (i) => Math.min(i, 12) * 35 + "ms",
      test: (el, st) =>
        (!st.q || text(el, "b, small").includes(st.q.toLowerCase())) &&
        (!st.r || el.dataset.r === st.r) &&
        (!st.l || el.dataset.l === st.l),
      render: (view, shown, st) => {
        $("cmEmpty").hidden = view.length > 0;
        $("cmCount").innerHTML = view.length
          ? `Showing <b>${shown.length}</b> of <b>${view.length}</b> communities${st.r ? ` in <b>${st.r}</b>` : ""}${st.l ? ` starting with “${st.l}”` : ""}${st.q ? ` matching “${esc(st.q)}”` : ""}`
          : "";
        const m = $("cmMore");
        m.hidden = view.length <= st.n;
        m.textContent = `Show more (${view.length - shown.length} more)`;
      },
    });
    const chips = $$("#cmReg button");
    recount(chips, L.items, (el, b) => !b.dataset.r || el.dataset.r === b.dataset.r);
    function azDraw() {
      const has = new Set(L.items.filter((c) => !L.st.r || c.dataset.r === L.st.r).map((c) => c.dataset.l));
      $$("button", az).forEach((b) => {
        b.classList.toggle("on", b.dataset.l === L.st.l);
        if (b.dataset.l) b.disabled = !has.has(b.dataset.l);
      });
    }
    $("cmReg").onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      chips.forEach((x) => x.classList.toggle("on", x === b));
      L.st.r = b.dataset.r;
      L.st.l = "";
      azDraw();
      L.set("page", 1);
    };
    az.onclick = (e) => {
      const b = e.target.closest("button");
      if (!b || b.disabled) return;
      L.st.l = b.dataset.l;
      azDraw();
      L.set("page", 1);
    };
    $("cmQ").oninput = (e) => L.set("q", e.target.value.trim());
    $("cmSearch").onsubmit = (e) => {
      e.preventDefault();
      L.set("q", $("cmQ").value.trim());
      $("directory").scrollIntoView({ behavior: "smooth" });
    };
    $("cmMore").onclick = () => {
      L.st.n += 16;
      L.draw();
    };
    $("cmClear").onclick = () => {
      Object.assign(L.st, { q: "", r: "", l: "" });
      $("cmQ").value = "";
      chips.forEach((x, i) => x.classList.toggle("on", i === 0));
      azDraw();
      L.set("page", 1);
    };
    const cm = (c) => ({
      t: c.querySelector("b").textContent.trim(),
      s: c.querySelector("small").textContent.trim(),
      g: "Communities",
      img: c.querySelector("img").src,
      href: c.getAttribute("href"),
    });
    SUGS.push({
      input: "cmQ",
      popTitle: "Popular communities",
      per: 9,
      max: 9,
      popular: L.items
        .filter((c) => c.dataset.pc)
        .sort((a, b) => b.dataset.pc - a.dataset.pc)
        .map(cm),
      items: L.items.map(cm),
    });
    azDraw();
    L.draw();
  })();

  /* =====================================================================
     TOWN SQUARE team — department tabs + search (each card: data-d)
     ===================================================================== */
  (function () {
    const grid = $("tGrid");
    if (!grid) return;
    const fl = $("tFilters"),
      tabs = $$("button", fl);
    const L = makeList({
      grid,
      mode: "all",
      state: { d: "all", q: "" },
      delay: (i) => Math.min(i, 12) * 40 + "ms",
      test: (el, st) => (st.d === "all" || el.dataset.d === st.d) && (!st.q || text(el, "h3, small").includes(st.q)),
      render: (view) => ($("tEmpty").hidden = view.length > 0),
    });
    recount(tabs, L.items, (el, b) => b.dataset.id === "all" || el.dataset.d === b.dataset.id);
    fl.onclick = (e) => {
      const b = e.target.closest("button");
      if (!b) return;
      tabs.forEach((x) => {
        x.classList.toggle("on", x === b);
        x.setAttribute("aria-selected", x === b);
      });
      L.set("d", b.dataset.id);
    };
    $("tSearch").oninput = (e) => L.set("q", e.target.value.trim().toLowerCase());
    SUGS.push({
      input: "tSearch",
      popTitle: "Team members",
      per: 6,
      max: 6,
      items: [
        ...L.items.map((c) => ({
          t: plain(c.querySelector("h3")),
          s: c.querySelector("small").textContent.trim(),
          g: "Team",
          img: c.querySelector("img").src,
          href: c.getAttribute("href"),
        })),
        ...tabs
          .filter((b) => b.dataset.id !== "all")
          .map((b) => ({
            t: label(b),
            s: L.items.filter((el) => el.dataset.d === b.dataset.id).length + " members",
            g: "Departments",
            ic: "user",
            q: "",
            id: b.dataset.id,
          })),
      ],
      onPick: (it) => {
        if (it.g === "Departments") {
          $("tSearch").value = "";
          L.st.q = "";
          fl.querySelector(`button[data-id="${it.id}"]`).click();
        }
      },
    });
    L.draw();
  })();
})();
