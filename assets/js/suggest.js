/* Search suggestions engine: pages register boxes via window.__SUGS.push({...}) */
/* ---- Search suggestions: glass autocomplete for every search box ---- */
(function () {
  const cfgs = window.__SUGS || [];
  if (!cfgs.length) return;
  const ICON = {
    pin: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 21s-7-6.2-7-11.5a7 7 0 0114 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>',
    dev: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 21V8l8-5 8 5v13M9 21v-6h6v6"/></svg>',
    search:
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>',
    user: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>',
  };
  const esc = (s) =>
    String(s).replace(/[&<>"]/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" })[c]);
  const norm = (s) => String(s).toLowerCase().normalize("NFD").replace(/[̀-ͯ]/g, "");
  const hl = (s, q) => {
    if (!q) return esc(s);
    const i = norm(s).indexOf(norm(q));
    return i < 0
      ? esc(s)
      : esc(s.slice(0, i)) + "<mark>" + esc(s.slice(i, i + q.length)) + "</mark>" + esc(s.slice(i + q.length));
  };
  let uid = 0;
  cfgs.forEach((cfg) => {
    const input = document.getElementById(cfg.input);
    if (!input) return;
    const anchor = input.closest(cfg.anchor || ".op-search,.t-search,.field,form") || input;
    const id = "sg" + ++uid;
    const box = document.createElement("div");
    box.className = "sg";
    box.id = id;
    box.setAttribute("role", "listbox");
    document.body.appendChild(box);
    input.setAttribute("role", "combobox");
    input.setAttribute("aria-autocomplete", "list");
    input.setAttribute("aria-expanded", "false");
    input.setAttribute("aria-controls", id);
    input.setAttribute("autocomplete", "off");
    let items = [],
      act = -1,
      open = false,
      suppress = false;
    const place = () => {
      const r = anchor.getBoundingClientRect();
      const w = Math.min(Math.max(r.width, cfg.minW || 0), innerWidth - 16);
      box.style.width = w + "px";
      box.style.left = Math.max(8, Math.min(r.left, innerWidth - w - 8)) + "px";
      const below = innerHeight - r.bottom;
      if (below < 260 && r.top > below) {
        box.style.top = "auto";
        box.style.bottom = innerHeight - r.top + 8 + "px";
      } else {
        box.style.bottom = "auto";
        box.style.top = r.bottom + 8 + "px";
      }
    };
    function search(q) {
      const n = norm(q.trim());
      const per = cfg.per || 4,
        max = cfg.max || 9;
      const groups = {};
      let out = [];
      const src = n
        ? cfg.items
            .map((it) => {
              const t = norm(it.t),
                s = norm(it.s || ""),
                k = norm(it.k || "");
              let sc = t.startsWith(n)
                ? 3
                : t.split(/[\s,·-]+/).some((w) => w.startsWith(n))
                  ? 2
                  : t.includes(n)
                    ? 1.5
                    : s.includes(n) || k.includes(n)
                      ? 1
                      : 0;
              return [sc, it];
            })
            .filter((x) => x[0] > 0)
            .sort((a, b) => b[0] - a[0])
            .map((x) => x[1])
        : cfg.popular || cfg.items.slice(0, max);
      src.forEach((it) => {
        const g = it.g || "";
        groups[g] = groups[g] || [];
        if (groups[g].length < per && out.length < max) {
          groups[g].push(it);
          out.push(it);
        }
      });
      return { groups, out };
    }
    function render() {
      const q = input.value;
      const { groups, out } = search(q);
      items = [].concat(...Object.values(groups));
      act = out.length && q.trim() ? 0 : -1;
      let h = "";
      if (!out.length) h = `<div class="sg-e">No matches for “${esc(q)}”. Press Enter to search anyway.</div>`;
      else {
        if (!q.trim() && cfg.popTitle) h += `<div class="sg-g">${cfg.popTitle}</div>`;
        let k = 0;
        Object.entries(groups).forEach(([g, arr]) => {
          if (!arr.length) return;
          if (q.trim() || !cfg.popTitle) h += g ? `<div class="sg-g">${esc(g)}</div>` : "";
          arr.forEach((it) => {
            const i = k++;
            const th = it.img
              ? `<img class="sg-th${it.logo ? " logo" : ""}" src="${it.img}" alt="" decoding="async">`
              : `<span class="sg-ic">${ICON[it.ic || "search"]}</span>`;
            h += `<div class="sg-i" role="option" id="${id}-${i}" data-i="${i}" aria-selected="${i === act}">${th}<span class="sg-t"><b>${hl(it.t, q.trim())}</b>${it.s ? `<small>${hl(it.s, q.trim())}</small>` : ""}</span><span class="sg-go">${it.href ? "↗" : "↵"}</span></div>`;
          });
        });
      }
      h +=
        '<div class="sg-f"><span><kbd>↑</kbd> <kbd>↓</kbd> to navigate</span><span><kbd>Enter</kbd> to select · <kbd>Esc</kbd> to close</span></div>';
      box.innerHTML = h;
      setAct(act);
    }
    function setAct(i) {
      act = i;
      box.querySelectorAll(".sg-i").forEach((el) => el.setAttribute("aria-selected", +el.dataset.i === i));
      const el = box.querySelector(`[data-i="${i}"]`);
      if (el) {
        input.setAttribute("aria-activedescendant", el.id);
        el.scrollIntoView({ block: "nearest" });
      } else input.removeAttribute("aria-activedescendant");
    }
    function show() {
      render();
      place();
      box.classList.add("open");
      open = true;
      input.setAttribute("aria-expanded", "true");
    }
    function hide() {
      box.classList.remove("open");
      open = false;
      input.setAttribute("aria-expanded", "false");
      input.removeAttribute("aria-activedescendant");
    }
    function pick(it) {
      if (!it) return;
      hide();
      if (it.href) {
        location.href = it.href;
        return;
      }
      input.value = it.q != null ? it.q : it.t;
      suppress = true;
      input.dispatchEvent(new Event("input", { bubbles: true }));
      suppress = false;
      if (cfg.onPick) cfg.onPick(it, input);
    }
    input.addEventListener("focus", show);
    input.addEventListener("input", () => {
      if (suppress) return;
      if (!open) show();
      else {
        render();
        place();
      }
    });
    input.addEventListener("keydown", (e) => {
      if (!open && (e.key === "ArrowDown" || e.key === "ArrowUp")) {
        show();
        e.preventDefault();
        return;
      }
      if (!open) return;
      if (e.key === "ArrowDown") {
        e.preventDefault();
        setAct(items.length ? (act + 1) % items.length : -1);
      } else if (e.key === "ArrowUp") {
        e.preventDefault();
        setAct(items.length ? (act - 1 + items.length) % items.length : -1);
      } else if (e.key === "Enter") {
        if (act >= 0 && items[act]) {
          e.preventDefault();
          pick(items[act]);
        } else hide();
      } else if (e.key === "Escape") {
        e.stopPropagation();
        hide();
      }
    });
    input.addEventListener("blur", () => setTimeout(hide, 120));
    document.addEventListener("pointerdown", (e) => {
      if (open && !box.contains(e.target) && !anchor.contains(e.target)) hide();
    });
    box.addEventListener("mousedown", (e) => e.preventDefault());
    box.addEventListener("click", (e) => {
      const el = e.target.closest(".sg-i");
      if (el) pick(items[+el.dataset.i]);
    });
    addEventListener("scroll", () => open && place(), { passive: true });
    addEventListener("resize", () => open && place());
  });
})();
