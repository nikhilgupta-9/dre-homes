/*
 * DRE Homes — our own JavaScript for the website (rule.md 9.1a).
 * Loaded on every page after the design's main.js and site.js (common/footer.php).
 * Pages never contain inline <script> code: add behaviour here, grouped by page,
 * and attach it to elements by id / class / data-* so it only runs where those elements exist.
 */
(function () {
  "use strict";

  /* Communities autocomplete can register here even if the page script fails to load. */
  var communityGrid = document.getElementById("cmGrid");
  if (communityGrid && !window.__SUGS?.some(function (cfg) { return cfg.input === "cmQ"; })) {
    var communityCards = Array.from(communityGrid.querySelectorAll(".cm"));
    var toCommunitySuggestion = function (card) {
      return {
        t: card.querySelector("b").textContent.trim(),
        s: card.querySelector("small").textContent.trim(),
        g: "Communities",
        img: card.querySelector("img").src,
        href: card.getAttribute("href"),
      };
    };
    window.__SUGS = window.__SUGS || [];
    window.__SUGS.push({
      input: "cmQ",
      popTitle: "Popular communities",
      per: 9,
      max: 9,
      popular: communityCards.filter(function (card) { return card.dataset.pc; })
        .sort(function (a, b) { return Number(b.dataset.pc) - Number(a.dataset.pc); })
        .map(toCommunitySuggestion),
      items: communityCards.map(toCommunitySuggestion),
    });
  }

  /* Keep the off-plan search and filters usable if the shared page script fails to load. */
  (function () {
    var grid = document.getElementById("opGrid");
    var input = document.getElementById("qIn");
    if (!grid || !input || typeof input.oninput === "function") return;

    var cards = Array.from(grid.querySelectorAll(":scope > .oc"));
    var pageSize = 9;
    var state = { page: 1, emirate: "", developer: "", area: "", min: 0, max: 0, beds: new Set(), sort: "feat", view: "grid" };
    var pager = document.getElementById("pager");
    var noResults = document.getElementById("opEmpty");
    var count = document.getElementById("rCount");
    var q = function (id) { return document.getElementById(id); };
    var words = function (value) { return String(value || "").toLowerCase().trim().split(/\s+/).filter(Boolean); };

    function matches(card) {
      var d = card.dataset;
      var price = Number(d.p) || 0;
      var terms = words(input.value);
      var searchable = [d.projectName, d.name, d.d, d.l, d.e].join(" ").toLowerCase();
      var bedList = (d.blist || "").split(/\s+/);
      var bedsMatch = !state.beds.size || Array.from(state.beds).some(function (bed) {
        var value = bed === "STD" ? 0 : Number(bed);
        if (d.blist) return bedList.includes(String(value)) || (value === 0 && Number(d.bmin) === 0);
        return d.bmin !== undefined && (value === 5 ? Number(d.bmax) >= 5 : value >= Number(d.bmin) && value <= Number(d.bmax));
      });
      return terms.every(function (term) { return searchable.includes(term); }) &&
        (!state.emirate || d.e === state.emirate) &&
        (!state.developer || d.d === state.developer) &&
        (!state.area || d.l === state.area) &&
        (!state.min || (price && price >= state.min)) &&
        (!state.max || (price && price <= state.max)) &&
        bedsMatch;
    }

    function draw() {
      var results = cards.filter(matches);
      if (state.sort === "pa") results.sort(function (a, b) { return (Number(a.dataset.p) || Infinity) - (Number(b.dataset.p) || Infinity); });
      if (state.sort === "pd") results.sort(function (a, b) { return (Number(b.dataset.p) || 0) - (Number(a.dataset.p) || 0); });
      if (state.sort === "az") results.sort(function (a, b) { return (a.dataset.name || "").localeCompare(b.dataset.name || ""); });

      var pages = Math.max(1, Math.ceil(results.length / pageSize));
      state.page = Math.min(state.page, pages);
      var visible = new Set(results.slice((state.page - 1) * pageSize, state.page * pageSize));
      results.concat(cards.filter(function (card) { return !visible.has(card) && !results.includes(card); }))
        .forEach(function (card) { grid.appendChild(card); });
      cards.forEach(function (card) { card.hidden = !visible.has(card); });
      grid.classList.toggle("list", state.view === "list");
      noResults.hidden = results.length > 0;
      count.innerHTML = results.length
        ? "Showing <b>" + ((state.page - 1) * pageSize + 1) + "–" + Math.min(state.page * pageSize, results.length) + "</b> of <b>" + results.length + "</b> projects"
        : "";
      q("clearAll").hidden = !(input.value.trim() || state.emirate || state.developer || state.area || state.min || state.max || state.beds.size);
      pager.innerHTML = pages < 2 ? "" :
        '<button data-p="' + (state.page - 1) + '" ' + (state.page === 1 ? "disabled" : "") + ' aria-label="Previous page">←</button>' +
        Array.from({ length: pages }, function (_, i) { return i + 1; }).map(function (n) {
          return '<button data-p="' + n + '" class="' + (n === state.page ? "on" : "") + '"' +
            (n === state.page ? ' aria-current="page"' : "") + ">" + n + "</button>";
        }).join("") +
        '<button data-p="' + (state.page + 1) + '" ' + (state.page === pages ? "disabled" : "") + ' aria-label="Next page">→</button>';
    }

    function setFilter(key, value) {
      state[key] = value;
      state.page = 1;
      draw();
    }

    q("opSearch").addEventListener("submit", function (event) {
      event.preventDefault();
      setFilter("page", 1);
      q("listing").scrollIntoView({ behavior: "smooth" });
    });
    input.addEventListener("input", function () { setFilter("page", 1); });
    q("emSeg").addEventListener("click", function (event) {
      var button = event.target.closest("button[data-e]");
      if (!button) return;
      q("emSeg").querySelectorAll("button").forEach(function (item) { item.classList.toggle("on", item === button); });
      setFilter("emirate", button.dataset.e);
    });
    [["devSel", "developer"], ["locSel", "area"]].forEach(function (pair) {
      q(pair[0]).addEventListener("change", function (event) { setFilter(pair[1], event.target.value); });
    });
    q("pMin").addEventListener("change", function (event) { setFilter("min", Number(event.target.value) || 0); });
    q("pMax").addEventListener("change", function (event) { setFilter("max", Number(event.target.value) || 0); });
    q("sortSel").addEventListener("change", function (event) { setFilter("sort", event.target.value); });
    document.querySelectorAll(".vt button").forEach(function (button) {
      button.addEventListener("click", function () {
        state.view = button.dataset.v;
        document.querySelectorAll(".vt button").forEach(function (item) { item.classList.toggle("on", item === button); });
        draw();
      });
    });
    q("bedChips").addEventListener("click", function (event) {
      var button = event.target.closest("button[data-b]");
      if (!button) return;
      if (state.beds.has(button.dataset.b)) state.beds.delete(button.dataset.b);
      else state.beds.add(button.dataset.b);
      button.classList.toggle("on", state.beds.has(button.dataset.b));
      q("bedCount").textContent = state.beds.size || "";
      setFilter("page", 1);
    });
    q("pager").addEventListener("click", function (event) {
      var button = event.target.closest("button[data-p]");
      if (button && !button.disabled) setFilter("page", Number(button.dataset.p));
    });
    q("clearAll").addEventListener("click", clearFilters);
    q("emptyClear").addEventListener("click", clearFilters);
    function clearFilters() {
      input.value = "";
      state = { page: 1, emirate: "", developer: "", area: "", min: 0, max: 0, beds: new Set(), sort: state.sort, view: state.view };
      ["devSel", "locSel", "pMin", "pMax"].forEach(function (id) { q(id).value = ""; });
      q("emSeg").querySelectorAll("button").forEach(function (button, index) { button.classList.toggle("on", index === 0); });
      q("bedChips").querySelectorAll("button").forEach(function (button) { button.classList.remove("on"); });
      q("bedCount").textContent = "";
      draw();
    }
    q("quickDev").addEventListener("click", function (event) {
      var button = event.target.closest("button[data-d]");
      if (!button) return;
      state.developer = state.developer === button.dataset.d ? "" : button.dataset.d;
      q("devSel").value = state.developer;
      q("quickDev").querySelectorAll("button").forEach(function (item) { item.classList.toggle("on", item.dataset.d === state.developer); });
      state.page = 1;
      draw();
    });
    window.__SUGS = window.__SUGS || [];
    if (!window.__SUGS.some(function (cfg) { return cfg.input === "qIn"; })) {
      var suggestions = cards.map(function (card) {
        var title = card.querySelector("h3 a");
        return {
          t: card.dataset.projectName || card.dataset.name,
          s: [card.dataset.d, card.dataset.l, card.dataset.e].filter(Boolean).join(" · "),
          g: "Projects",
          img: card.querySelector("img").src,
          href: title ? title.href : "",
        };
      });
      window.__SUGS.push({
        input: "qIn",
        popTitle: "Popular projects",
        per: 9,
        max: 9,
        items: suggestions,
      });
    }
    draw();
  })();

  /* ---- Walls with a "Show more" button (Our Media, Our Team, Testimonials, …):
     load the next page automatically on scroll, instead of waiting for a click.
     Markup from site.js: <div class="md-more"><button id="xxMore">Show more …</button></div>;
     the wrapper div is hidden by site.js once every item is shown. The button itself is left
     working and visible, so a keyboard or screen-reader visitor can still use it directly. ---- */
  var wraps = document.querySelectorAll(".md-more");
  if (!wraps.length || !("IntersectionObserver" in window)) return;

  // Only once the visitor has actually scrolled — a tall viewport must not
  // auto-load everything the moment the page opens.
  var scrolled = false;
  window.addEventListener(
    "scroll",
    function () {
      scrolled = true;
    },
    { passive: true, once: true }
  );

  wraps.forEach(function (wrap) {
    var button = wrap.querySelector("button[id]");
    if (!button) return;

    var busy = false;
    var safety = 0; // stops a runaway loop if a page's "no more items" state is ever missed

    var observer = new IntersectionObserver(
      function (entries) {
        var entry = entries[0];
        if (!scrolled || !entry.isIntersecting || wrap.hidden || busy || safety > 500) return;
        busy = true;
        safety++;
        button.click();
        // site.js re-renders synchronously, so the next frame has the final hidden state
        requestAnimationFrame(function () {
          busy = false;
          if (wrap.hidden) observer.disconnect();
        });
      },
      { rootMargin: "0px 0px 600px 0px" } // start loading a little before the button is actually on screen
    );
    observer.observe(wrap);
  });

  /* ---- Brand film: handle local video playback if chosen in admin ---- */
  (function () {
    var f = document.getElementById("filmFrame");
    var b = document.getElementById("filmPlay");
    if (!f || !b) return;
    if (b.dataset.source === "local" && b.dataset.video) {
      b.onclick = function (e) {
        if (e) {
          e.preventDefault();
          e.stopPropagation();
        }
        var v = document.createElement("video");
        v.src = b.dataset.video;
        v.controls = true;
        v.autoplay = true;
        v.playsInline = true;
        v.style.position = "absolute";
        v.style.inset = "0";
        v.style.width = "100%";
        v.style.height = "100%";
        v.style.objectFit = "cover";
        f.appendChild(v);
        f.classList.add("playing");
        v.play();
      };
    }
  })();
})();
