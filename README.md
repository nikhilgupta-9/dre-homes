# DRE Homes — HTML · CSS · Bootstrap 5 · JavaScript

A plain static site: open any `.html` file in a browser or upload the folder to any web host. No PHP, no Python, no build step.

**All content is in the HTML.** Every card, team member, blog, award, photo, project, gallery link and search suggestion list is written in the page's `.html` file, so you edit the website there. The JavaScript files only add behaviour (filters, search, page numbers, galleries, calculators, forms). You never have to change them to update content.

## Structure

```
index.html, about.html, …        43 pages (each page is a complete HTML file)
assets/css/main.css               shared theme (taupe glass look, night mode, dock, skeletons)
assets/css/suggest.css            search-suggestion dropdown
assets/css/pages/<page>.css       styles used by one page only
assets/js/main.js                 shared behaviour (menu, dock, AI panel, phone input, Dirham symbol…)
assets/js/site.js                 page features for every page (filters, search, galleries, calculators…)
assets/js/suggest.js              search-suggestion dropdown
assets/js/team-member.js          the one data-driven page: team-member.html?u=<name>
assets/img/                       logos
```

## Editing lists (cards)

To add a card, copy an existing card in the HTML, paste it where you want it and change its text, image, link and `data-*` values. To remove a card, delete it.

Filters read the card's `data-*` attributes. The counts on the filter buttons are recalculated from the cards automatically.

| Page | Card | Main `data-*` attributes |
| --- | --- | --- |
| Our Team | `#otGrid .ot-c` | `data-d` department id (see the `#otDep` buttons), `data-lic` (1 = RERA licensed), `data-name`, `data-role`, `data-brn`, `data-tel`, `data-wa`, `data-email`, `data-u` (profile slug). The quick-view bio is the hidden `.ot-bio` block inside the card. |
| Blogs | `#blGrid .bc` | `data-c` topic, `data-y` year, `data-ts` date (for sorting), `data-m` minutes to read |
| Testimonials | `#tqWall .tq-c` | `data-tp` topics (`buy rent sell`), `data-y` year, `data-ag` advisor ids. The advisor buttons carry `data-name` and `data-role`. |
| Emaar projects | `#pjGrid .oc` | `data-c` community, `data-l` location, `data-p` price (0 = on request), `data-bmin` / `data-bmax` bedrooms, `data-name` |
| Palm Jumeirah | `#pmGrid .oc` | `data-d` developer, `data-p`, `data-bmin` / `data-bmax`, `data-name` |
| Buy property | `#opGrid .oc` | `data-ty` type, `data-c` community, `data-p` price, `data-bn` beds (0 = studio), `data-bt` baths, `data-a` sq ft, `data-u`, `data-l` |
| Off-plan | `#opGrid .oc` | `data-e` emirate, `data-d` developer, `data-l` area, `data-p`, `data-bmin` / `data-bmax` / `data-blist`, `data-u`, `data-name` |
| Our Media | `#mdWall .md-ph` | `data-c` category, `data-k` photo number (used by the "stories" `data-ix` lists) |
| Our Awards | `#awTl .aw-c` | `data-by` partner, `data-y` year, `data-k`, `data-img`, `data-title`. The full write-up is the hidden `.aw-x` paragraph. Put a new card inside the right year group (`#ay2026` …). |
| Company News | `#cnTl .cn-c` | `data-c` category, `data-y` year. Put a new card inside the right year group (`#y2026` …). |
| Developers | `#dvGrid .dv` | `data-l` first letter, `data-top` featured position (optional) |
| Communities | `#cmGrid .cm` | `data-r` region, `data-l` first letter, `data-pc` number of properties (makes it "Popular") |
| Town Square team | `#tGrid .tm` | `data-d` department id (see the `#tFilters` tabs) |
| Home | `#cards .col` | `data-c` city (matches the filter chip text) |

Cards with the `hidden` attribute are beyond the first page or "Show more". `site.js` manages them, so you can leave it as it is or remove it.

## Other content that lives in the HTML

- **Photo galleries** (news story, Qurat Ul Ain, podcast, careers): each thumbnail button has `data-src` (full photo) and `data-title`.
- **Property photo galleries**: the hidden `<ul id="pdPhotos">` list on each property page.
- **Maps**: the Google Maps embed link is `data-map` on the `#mapLoad` button.
- **Videos**: the brand film YouTube id is `data-yt` on `#filmPlay`. Each podcast episode card (`.pd-ep`) has `data-yt`, `data-title`, `data-sub`, `data-tag` and `data-date`.
- **Article "copy link"** URL: `data-url` on `#arCopy` / `#arCopy2`.
- **Search suggestions**:
  - Home areas and valuation communities: the `<datalist>` right after the search box. `data-popular` lists the popular ones.
  - "Trending searches": the `data-trending="A|B|C"` attribute on the search box.
  - Everything else is read from the cards.

## How Bootstrap is used

- **Bootstrap 5.3.3** is loaded from cdnjs in every page `<head>` (CSS) and before `main.js` (JS bundle).
- The CSS is imported into a **cascade layer** (`@layer reset, bootstrap;`). Anything in `main.css` therefore overrides Bootstrap, so the DRE look stays the same, while Bootstrap's grid, utilities and components still work.
- Bootstrap components in use:
  - **Dropdown**: desktop menus, with keyboard arrow navigation.
  - **Offcanvas**: the mobile menu.
  - **Collapse**: the mobile sub-menus, as an accordion.
  - **Grid**: footer columns and the home-page property cards.
  - **Buttons**: `.btn` with the DRE variants `.btn-gold` and `.btn-glass`.
  - **Forms**: `.form-control` on the contact form.
- New sections can use any Bootstrap class (`container`, `row`, `col-md-6`, `d-flex`, `mt-3`, `modal`, …).

## Editing the header / footer

The header (nav and mobile menu) and the footer (plus the dock and the AI panel) are the same in every page. When you change a menu link, update it in all `.html` files. A find-and-replace across the folder works well.

The active menu item is marked with `class="mi … active"` on that page's nav link.

## Night mode

The small inline script in each `<head>` applies the saved night mode before the page paints. The toggle lives in the dock (🌙 / ☀️).
