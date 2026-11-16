/*
 * DRE Homes — our own JavaScript for the website (rule.md 9.1a).
 * Loaded on every page after the design's main.js and site.js (common/footer.php).
 * Pages never contain inline <script> code: add behaviour here, grouped by page,
 * and attach it to elements by id / class / data-* so it only runs where those elements exist.
 */
(function () {
  "use strict";

  /* Load homepage Trustindex widgets even if the design's site.js fails to load. */
  (function () {
    if (window.__dreTrustindexReviewsInitialized) return;
    var section = document.querySelector(".reviews");
    var scripts = Array.from(document.querySelectorAll(".reviews script[data-src]"));
    if (!section || !scripts.length) return;

    window.__dreTrustindexReviewsInitialized = true;
    var loaded = false;
    function loadReviews() {
      if (loaded) return;
      loaded = true;
      scripts.forEach(function (placeholder) {
        var script = document.createElement("script");
        script.src = placeholder.dataset.src;
        script.defer = true;
        placeholder.replaceWith(script);
      });
    }

    if ("IntersectionObserver" in window) {
      var observer = new IntersectionObserver(function (entries) {
        if (entries.some(function (entry) { return entry.isIntersecting; })) {
          loadReviews();
          observer.disconnect();
        }
      }, { rootMargin: "300px" });
      observer.observe(section);
    } else {
      loadReviews();
    }
  })();

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

  /* Developers autocomplete can register from rendered cards if site.js is unavailable. */
  (function () {
    var grid = document.getElementById("dvGrid");
    if (!grid || window.__SUGS?.some(function (cfg) { return cfg.input === "dvQ"; })) return;

    var cards = Array.from(grid.querySelectorAll(":scope > .dv"));
    var suggestionFor = function (card) {
      var name = card.querySelector("h3");
      var description = card.querySelector("p");
      var image = card.querySelector("img");
      return {
        t: name ? name.textContent.trim() : "",
        s: description ? description.textContent.trim() : "",
        g: "Developers",
        img: image ? image.src : "",
        logo: 1,
        href: card.getAttribute("href") || "",
      };
    };

    window.__SUGS = window.__SUGS || [];
    window.__SUGS.push({
      input: "dvQ",
      popTitle: "Top partners",
      per: 8,
      max: 8,
      popular: cards.filter(function (card) { return card.dataset.top !== undefined; })
        .sort(function (a, b) { return Number(a.dataset.top) - Number(b.dataset.top); })
        .map(suggestionFor),
      items: cards.map(suggestionFor),
    });
  })();

  /* Buy and Rent autocomplete can use rendered listing data if site.js is unavailable. */
  (function () {
    var grid = document.getElementById("opGrid");
    var input = document.getElementById("qIn");
    if (!grid || !input || !document.getElementById("tySeg") ||
      window.__SUGS?.some(function (cfg) { return cfg.input === "qIn"; })) return;

    var cards = Array.from(grid.querySelectorAll(":scope > .oc"));
    var communityCounts = {};
    cards.forEach(function (card) {
      var community = card.dataset.c;
      if (community) communityCounts[community] = (communityCounts[community] || 0) + 1;
    });

    var noun = document.getElementById("rCount")?.dataset.noun || "homes for sale";
    var communities = Object.keys(communityCounts).sort(function (a, b) {
      return communityCounts[b] - communityCounts[a];
    });
    var communitySuggestions = communities.map(function (community) {
      return {
        t: community,
        s: communityCounts[community] + " home" + (communityCounts[community] > 1 ? "s" : "") +
          noun.replace(/^homes/, ""),
        g: "Communities",
        ic: "pin",
        q: "",
      };
    });
    var listingSuggestions = cards.map(function (card) {
      var link = card.querySelector("h3 a");
      var image = card.querySelector("img");
      return {
        t: card.dataset.projectName || card.dataset.name || (link ? link.textContent.trim() : ""),
        s: [card.dataset.name, card.dataset.c, card.dataset.l, card.dataset.ty].filter(Boolean).join(" · "),
        g: "Listings",
        img: image ? image.src : "",
        href: link ? link.href : "",
      };
    });

    window.__SUGS = window.__SUGS || [];
    window.__SUGS.push({
      input: "qIn",
      popTitle: "Popular communities",
      per: 5,
      max: 10,
      popular: communitySuggestions.slice(0, 6),
      items: communitySuggestions.concat(listingSuggestions),
      onPick: function (item) {
        if (item.g !== "Communities") return;
        var locationSelect = document.getElementById("locSel");
        if (!locationSelect) return;
        locationSelect.value = item.t;
        locationSelect.dispatchEvent(new Event("change", { bubbles: true }));
      },
    });
  })();

  /* Keep Buy and Rent search/filter results working when site.js fails to load. */
  (function () {
    var grid = document.getElementById("opGrid");
    var input = document.getElementById("qIn");
    var typeSegment = document.getElementById("tySeg");
    if (!grid || !input || !typeSegment || typeof input.oninput === "function") return;

    var cards = Array.from(grid.querySelectorAll(":scope > .oc"));
    var pageSize = 9;
    var state = { page: 1, type: "", community: "", min: 0, max: 0, beds: new Set(), baths: 0, size: 0, sort: "feat", view: "grid" };
    var count = document.getElementById("rCount");
    var pager = document.getElementById("pager");
    var empty = document.getElementById("opEmpty");
    var noun = count?.dataset.noun || "homes for sale";
    var el = function (id) { return document.getElementById(id); };
    var terms = function (text) { return String(text || "").toLowerCase().trim().split(/\s+/).filter(Boolean); };

    function matches(card) {
      var d = card.dataset;
      var searchText = [d.projectName, d.name, card.querySelector("h3")?.textContent, d.c, d.l, d.ty]
        .join(" ").toLowerCase();
      var price = Number(d.p) || 0;
      var bedroomCount = Number(d.bn);
      return terms(input.value).every(function (term) { return searchText.includes(term); }) &&
        (!state.type || d.ty === state.type) &&
        (!state.community || d.c === state.community) &&
        (!state.min || (price > 0 && price >= state.min)) &&
        (!state.max || (price > 0 && price <= state.max)) &&
        (!state.beds.size || Array.from(state.beds).some(function (bed) {
          return bed === "STD" ? bedroomCount === 0 : bed === "5" ? bedroomCount >= 5 : bedroomCount === Number(bed);
        })) &&
        (!state.baths || Number(d.bt) >= state.baths) &&
        (!state.size || Number(d.a) >= state.size);
    }

    function draw() {
      var results = cards.filter(matches);
      if (state.sort === "pa") results.sort(function (a, b) { return (Number(a.dataset.p) || Infinity) - (Number(b.dataset.p) || Infinity); });
      if (state.sort === "pd") results.sort(function (a, b) { return (Number(b.dataset.p) || 0) - (Number(a.dataset.p) || 0); });
      if (state.sort === "sz") results.sort(function (a, b) { return Number(b.dataset.a) - Number(a.dataset.a); });
      if (state.sort === "ppf") results.sort(function (a, b) { return Number(a.dataset.ppf) - Number(b.dataset.ppf); });

      var pages = Math.max(1, Math.ceil(results.length / pageSize));
      state.page = Math.min(state.page, pages);
      var visible = new Set(results.slice((state.page - 1) * pageSize, state.page * pageSize));
      results.concat(cards.filter(function (card) { return !visible.has(card) && !results.includes(card); }))
        .forEach(function (card) { grid.appendChild(card); });
      cards.forEach(function (card) { card.hidden = !visible.has(card); });
      grid.classList.toggle("list", state.view === "list");
      if (empty) empty.hidden = results.length > 0;
      if (count) count.innerHTML = results.length
        ? "Showing <b>" + ((state.page - 1) * pageSize + 1) + "–" + Math.min(state.page * pageSize, results.length) +
          "</b> of <b>" + results.length + "</b> " + noun
        : "";
      if (pager) pager.innerHTML = pages < 2 ? "" :
        '<button data-p="' + (state.page - 1) + '" ' + (state.page === 1 ? "disabled" : "") + ' aria-label="Previous page">←</button>' +
        Array.from({ length: pages }, function (_, index) {
          var page = index + 1;
          return '<button data-p="' + page + '"' + (page === state.page ? ' class="on" aria-current="page"' : "") + ">" +
            page + "</button>";
        }).join("") +
        '<button data-p="' + (state.page + 1) + '" ' + (state.page === pages ? "disabled" : "") + ' aria-label="Next page">→</button>';

      var active = input.value.trim() || state.type || state.community || state.min || state.max ||
        state.beds.size || state.baths || state.size;
      if (el("clearAll")) el("clearAll").hidden = !active;
      if (el("bedCount")) {
        var activeChips = state.beds.size + (state.baths ? 1 : 0) + (state.size ? 1 : 0);
        el("bedCount").textContent = activeChips || "";
        el("bedCount").classList.toggle("on", activeChips > 0);
      }
      var quick = el("quickC");
      if (quick) quick.querySelectorAll("button[data-c]").forEach(function (button) {
        button.classList.toggle("on", button.dataset.c === state.community);
      });
    }

    function setFilter(key, value) {
      state[key] = value;
      state.page = 1;
      draw();
    }

    input.addEventListener("input", function () { setFilter("page", 1); });
    var searchForm = el("opSearch");
    if (searchForm) searchForm.addEventListener("submit", function (event) {
      event.preventDefault();
      setFilter("page", 1);
      el("listing")?.scrollIntoView({ behavior: "smooth" });
    });
    typeSegment.addEventListener("click", function (event) {
      var button = event.target.closest("button[data-t]");
      if (!button) return;
      typeSegment.querySelectorAll("button").forEach(function (item) { item.classList.toggle("on", item === button); });
      setFilter("type", button.dataset.t);
    });
    var locationSelect = el("locSel");
    if (locationSelect) locationSelect.addEventListener("change", function () {
      setFilter("community", locationSelect.value);
    });
    [["pMin", "min"], ["pMax", "max"]].forEach(function (pair) {
      var select = el(pair[0]);
      if (select) select.addEventListener("change", function () {
        setFilter(pair[1], Number(select.value) || 0);
      });
    });
    var sortSelect = el("sortSel");
    if (sortSelect) sortSelect.addEventListener("change", function () { setFilter("sort", sortSelect.value); });
    var quick = el("quickC");
    if (quick) quick.addEventListener("click", function (event) {
      var button = event.target.closest("button[data-c]");
      if (!button) return;
      state.community = state.community === button.dataset.c ? "" : button.dataset.c;
      if (locationSelect) locationSelect.value = state.community;
      state.page = 1;
      draw();
    });
    [["bedChips", "beds", "b"], ["bathChips", "baths", "b"], ["sizeChips", "size", "s"]]
      .forEach(function (config) {
        var container = el(config[0]);
        if (!container) return;
        container.addEventListener("click", function (event) {
          var button = event.target.closest("button[data-" + config[2] + "]");
          if (!button) return;
          var value = button.dataset[config[2]];
          if (config[1] === "beds") {
            if (state.beds.has(value)) state.beds.delete(value);
            else state.beds.add(value);
            button.classList.toggle("on", state.beds.has(value));
          } else {
            var next = Number(value);
            state[config[1]] = state[config[1]] === next ? 0 : next;
            container.querySelectorAll("button").forEach(function (item) {
              item.classList.toggle("on", Number(item.dataset[config[2]]) === state[config[1]] && !!state[config[1]]);
            });
          }
          setFilter("page", 1);
        });
      });
    var moreButton = el("moreBtn");
    if (moreButton) moreButton.addEventListener("click", function () {
      var panel = el("moreF");
      if (!panel) return;
      panel.hidden = !panel.hidden;
      moreButton.setAttribute("aria-expanded", String(!panel.hidden));
    });
    if (pager) pager.addEventListener("click", function (event) {
      var button = event.target.closest("button[data-p]");
      if (button && !button.disabled) setFilter("page", Number(button.dataset.p));
    });
    function clearFilters() {
      input.value = "";
      Object.assign(state, { page: 1, type: "", community: "", min: 0, max: 0, baths: 0, size: 0 });
      state.beds.clear();
      ["locSel", "pMin", "pMax"].forEach(function (id) { if (el(id)) el(id).value = ""; });
      typeSegment.querySelectorAll("button").forEach(function (button, index) {
        button.classList.toggle("on", index === 0);
      });
      ["bedChips", "bathChips", "sizeChips"].forEach(function (id) {
        if (el(id)) el(id).querySelectorAll("button").forEach(function (button) { button.classList.remove("on"); });
      });
      draw();
    }
    ["clearAll", "emptyClear"].forEach(function (id) {
      if (el(id)) el(id).addEventListener("click", clearFilters);
    });
    var viewToggle = document.querySelector(".vt");
    if (viewToggle) viewToggle.addEventListener("click", function (event) {
      var button = event.target.closest("button[data-v]");
      if (!button) return;
      state.view = button.dataset.v;
      viewToggle.querySelectorAll("button").forEach(function (item) { item.classList.toggle("on", item === button); });
      draw();
    });
    draw();
  })();

  /* Keep the off-plan search and filters usable if the shared page script fails to load. */
  (function () {
    var grid = document.getElementById("opGrid");
    var input = document.getElementById("qIn");
    if (!grid || !input || !document.getElementById("emSeg") || typeof input.oninput === "function") return;

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
