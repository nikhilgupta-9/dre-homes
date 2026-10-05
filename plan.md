# DRE Homes — Project Analysis and AI / RAG Plan

Status: **analysis and planning only**. No AI code has been written yet.
Date: 2026-10-05

Related documents: [rule.md](rule.md) (how we work), [security.md](security.md) (what must be fixed and protected).

---

## 1. What this project is

DRE Homes (drehomes.com) is a Dubai real-estate website with a custom admin panel.

| Part | Location | What it does |
|---|---|---|
| Public website | root `*.php` | Property listings (off-plan, buy, rent, ready-to-move-in, international), communities, developers, blogs, news, guides, mortgage calculator, careers, lead forms |
| Shared layout | `common/` | Header, footer, sidebar enquiry form |
| Public AJAX | `ajax/` | Listing pagination, search suggestions, lead and enquiry form handlers |
| Admin panel | `admin_nsrdwsc/` | About 200 CRUD pages (add / edit / manage / export) on a Metronic theme |
| Admin AJAX | `admin_nsrdwsc/ajax/` | Login, logs, image and PDF removal, push to Zoho CRM |
| App sync | `admin_nsrdwsc/push-app/` | Copies projects, communities and developers to the mobile-app database |
| DB layer | `db/config.php`, `db/functions.php` | `mysqli` connection and helper functions (`select_row`, `select_all`, `select_query`) |

### Tech stack

- Plain PHP (no framework, no Composer at the root, no build step). Local PHP is 8.2; production `php.ini` points to cPanel PHP 8.0.
- MariaDB 10.4 locally (`dre_website_db_v1`), about 95 tables with the `tbl_` prefix.
- jQuery on the frontend, Metronic (Bootstrap 4) in the admin.
- Clean URLs through `.htaccess` rewrites (`/property/{slug}`, `/buy-property/{slug}`, `/community/{slug}` and so on).
- Integrations: Zoho CRM (lead push), PHPMailer and `mail()`, Instagram Graph API, exchange-rate API, Google reviews.

### Size

| Item | Size | In git? |
|---|---|---|
| Whole folder | 11 GB | — |
| `admin_nsrdwsc/assets/media/` (uploaded images, brochures, floor plans) | 10.8 GB (`project/` alone is 9.7 GB, including a 1.3 GB `gallery.zip`) | No |
| `resume/` (candidate CVs, about 1,800 files) | 252 MB | No — personal data |
| `Archive/`, `Archive.zip`, `video/` | about 285 MB | No |
| Code plus static design assets | about 250 MB | **Yes** |

The uploaded media stays on the server and in backups. Git holds code only. The exact rules are in `.gitignore`.

### Data that matters for AI

| Table | Rows (local) | Use for AI |
|---|---|---|
| `tbl_proj` | 777 | Main knowledge source: name, price, bedrooms, area, location, handover, overview, features, payment plan, coordinates |
| `tbl_proj_faq`, `tbl_proj_payplan`, `tbl_proj_featfac` | 1,200+ | Per-property details |
| `tbl_community`, `tbl_community_loc` | 112 / 162 | Area knowledge |
| `tbl_developer` | 148 | Developer profiles |
| `tbl_blog`, `tbl_news`, `tbl_guide` | 138 / 59 / few | Long-form content: buyer, seller, tenant guides, golden visa, mortgage |
| `tbl_enq`, `tbl_offplan_enq`, `tbl_pdf_enquiry`, other `*_enq` | 4,500+ | Leads — personal data, **never indexed** |
| `tbl_resume` | 4,770 | CVs — personal data, **never indexed** |
| `tbl_login`, `tbl_activity_logs`, `tbl_login_logs` | — | Internal, **never indexed** |

### Problems found that affect the AI work

1. **Numbers are stored as text.** `proj_price`, `proj_bedroom`, `proj_area` and `proj_size` are `varchar(255)` with free text such as "AED 1.2M" or "1, 2 & 3 BR". Filters like "under 2 million" cannot work reliably until these are normalised.
2. **SQL is built by joining strings**, including with raw request input (see [security.md](security.md)). New AI code must not copy this pattern.
3. **No config or secrets system.** Credentials are written directly in PHP files. The AI keys need a proper home first.
4. **Duplicate and dead files** (`index2.php`, `*-new.php`, `*.php--`, `z.php`, `code.php`) make it unclear which file is live.
5. **Shared cPanel hosting**: no long-running processes, no Python, no vector database. The design below works inside these limits.

---

## 2. AI features — proposed and prioritised

| # | Feature | Who uses it | Value | Effort |
|---|---|---|---|---|
| 1 | **Property assistant chatbot (RAG)** — answers questions about properties, areas, developers, golden visa, mortgages, and captures the lead | Website visitors | High — more and better leads | Medium |
| 2 | **Natural-language property search** — "2 bed in Dubai Marina under 2M, ready next year" becomes filters | Website visitors | High | Medium (needs clean data) |
| 3 | **Similar properties** on the property page, by embedding similarity | Website visitors | Medium | Low once embeddings exist |
| 4 | **Admin content helper** — draft overview, meta title, meta description, FAQs and image alt text in add / edit project | Content team | High — saves hours per listing | Low |
| 5 | **Lead summary and intent tag** in manage leads, before the Zoho push | Sales team | Medium | Low |
| 6 | **Multilingual answers** (English, Arabic, Hindi, Russian) in the chatbot | Website visitors | Medium | Free with feature 1 |

Deliberately **not** planned: automatic CV screening or ranking. It carries bias and legal risk and should be a separate decision.

Recommended order: 4 → 1 → 2 → 3 → 5. Feature 4 is the smallest, proves the API integration end to end, and carries no public-facing risk.

---

## 3. RAG architecture

### 3.1 Design in one picture

```
                         ┌──────────────── Indexing (admin save / cron) ───────────────┐
 tbl_proj, tbl_community │  build text  →  chunk  →  embeddings API  →  tbl_ai_chunks  │
 tbl_developer, tbl_blog │  (one row per chunk: source, text, vector, content hash)    │
                         └─────────────────────────────────────────────────────────────┘

 Visitor ─► chat widget ─► /ai/chat.php ─► Claude (tool use)
                                │              │
                                │              ├─ search_properties(filters)   → SQL on tbl_proj (prepared)
                                │              ├─ search_knowledge(query)      → vector search on tbl_ai_chunks
                                │              ├─ get_property(slug)           → full details of one listing
                                │              └─ create_lead(name, phone…)    → existing enquiry table + Zoho push
                                ▼
                         streamed answer with links to the real property pages
```

### 3.2 Why hybrid (SQL tools plus vectors) and not vectors only

Most real-estate questions are **structured**: budget, bedrooms, area, handover date. Vector search is poor at "under 2 million" and good at "family-friendly community near a beach". So:

- **Structured questions** → Claude calls `search_properties` with typed filters, and PHP runs a prepared SQL query. Results are exact and always current.
- **Open questions** (guides, area descriptions, developer reputation, golden visa rules) → Claude calls `search_knowledge`, which is the vector search.
- Claude decides which tool to use, and may use both in one answer.

### 3.3 Components and choices

| Piece | Choice | Reason |
|---|---|---|
| Answer model | Claude through the official PHP SDK (`composer require anthropic-ai/sdk`). Default `claude-opus-5-5`; `claude-sonnet-5-5` or `claude-haiku-4-5` are cheaper options — a cost decision for the project owner, to be made with real traffic numbers | Official SDK, tool use, streaming, prompt caching |
| Embeddings | A separate embeddings provider (Anthropic does not offer an embeddings model; Voyage AI is the one Anthropic's documentation points to). Use a multilingual model | Needed for vector search; multilingual covers Arabic and Hindi queries |
| Vector store, phase 1 | A MySQL table `tbl_ai_chunks` with the vector stored as a packed binary blob; cosine similarity computed in PHP | Estimated 5–10 thousand chunks. Brute-force search at this size is fast enough and needs nothing the cPanel host lacks |
| Vector store, later | MariaDB 11.7+ native `VECTOR` type, or a hosted vector database (for example Qdrant Cloud) called over HTTPS | Only if chunk count or latency grows beyond what phase 1 handles |
| Keyword fallback | MySQL `FULLTEXT` index on chunk text, merged with vector results | Catches exact project names and spellings that embeddings blur |
| Chat transport | Server-sent events from `/ai/chat.php` | Streaming works on shared hosting if output buffering is disabled for that script |
| Conversation memory | `tbl_ai_conversations` and `tbl_ai_messages`, keyed by an anonymous session id | No login needed; history is capped per session |
| Prompt caching | System prompt and tool definitions are cached; they stay byte-identical between requests | Large cost and latency saving on every turn |

### 3.4 Indexing rules

- **Indexed**: active rows only (`is_active = 1`) from projects, FAQs, payment plans, communities, developers, blogs, news, guides, and static pages such as golden visa and mortgage advisory.
- **Never indexed**: any lead, enquiry, resume, login, log, or admin table. This is an explicit allowlist in code, not a blocklist.
- **Chunking**: one "summary card" chunk per property (name, type, location, price, bedrooms, handover, developer, URL), plus chunks of 300–500 tokens for long text (overview, blog body), with HTML stripped.
- **Freshness**: re-index one record when an admin saves it (hook in the add / edit / delete pages). A nightly cron re-checks everything using a content hash, so unchanged rows cost nothing.
- **Deletion**: when a record is deleted or deactivated, its chunks are deleted in the same request.

### 3.5 Answer rules for the model

- Answer only from tool results. If the tools return nothing, say so and offer to connect the visitor to an agent.
- Prices, handover dates and availability must come from `search_properties` or `get_property`, never from memory or from a blog chunk.
- Every property mentioned carries a link to its real page.
- No legal, tax, visa-eligibility or investment-return promises. Give general information and hand over to a human advisor.
- Ask for contact details only after giving value, and only save a lead with the visitor's clear consent.

### 3.6 New files and tables (planned)

```
ai/
  config.php            reads keys from the secrets file, model names, limits
  ClaudeClient.php      thin wrapper over the SDK
  Embedder.php          embeddings API calls
  VectorStore.php       insert / delete / search on tbl_ai_chunks
  Indexer.php           builds chunks from each allowed table
  tools/                one file per tool: search_properties, search_knowledge, get_property, create_lead
  chat.php              public SSE endpoint (rate limited)
  admin/generate.php    admin-only content helper endpoint
  cron/reindex.php      nightly re-index (CLI only)
js/ai-chat.js           chat widget
css/ai-chat.css
```

Tables: `tbl_ai_chunks`, `tbl_ai_conversations`, `tbl_ai_messages`, `tbl_ai_usage` (tokens and cost per request), `tbl_ai_index_log`.

---

## 4. Phases

### Phase 0 — Foundation (must be finished before any AI code)

- [ ] Rotate every exposed credential (list in [security.md](security.md) section 2).
- [ ] Move all credentials to one git-ignored secrets file; code reads from it.
- [ ] Fix the critical items in [security.md](security.md): SQL injection in listing and suggestion endpoints, unprotected admin AJAX and push-app scripts, `info.php`, MD5 passwords.
- [ ] Add Composer at the project root.
- [ ] Decide the live file for each duplicate page and delete the rest.
- [ ] Confirm production PHP version (the SDK needs a supported PHP 8 release) and whether cron and outbound HTTPS are available on the host.

### Phase 1 — Data preparation

- [ ] Add numeric columns to `tbl_proj`: `price_min_aed`, `price_max_aed`, `bedrooms_min`, `bedrooms_max`, `area_sqft_min`, `area_sqft_max`, `handover_date`. Backfill with a script, review failures by hand, and keep the original text columns for display.
- [ ] Update the add / edit project forms to fill the new columns.
- [ ] Add indexes on the columns used for filtering.

### Phase 2 — Admin content helper (feature 4)

- [ ] `ai/` skeleton, `ClaudeClient`, usage logging.
- [ ] "Generate with AI" buttons in add / edit project for overview, meta title, meta description and FAQs. Output goes into the form field for a human to review; nothing is saved automatically.

### Phase 3 — Indexing

- [ ] `tbl_ai_chunks`, `Embedder`, `Indexer`, `VectorStore`.
- [ ] First full index, then save hooks and the nightly cron.
- [ ] A small admin page showing index status and a manual "re-index" button.

### Phase 4 — Chatbot (features 1 and 6)

- [ ] Tools, `chat.php` with streaming, chat widget in `common/footer.php`.
- [ ] Rate limiting, input limits, lead capture through the existing enquiry flow.
- [ ] Evaluation set of 50–100 real questions with expected behaviour; run it before every prompt change.

### Phase 5 — Search and recommendations (features 2, 3, 5)

- [ ] Natural-language box on the listing pages that fills the existing filters.
- [ ] "Similar properties" block on the single-property pages.
- [ ] Lead summary in manage leads.

### Phase 6 — Measure and tune

- [ ] Dashboard from `tbl_ai_usage`: cost per day, conversations, leads from chat.
- [ ] Review unanswered questions weekly and fill content gaps.
- [ ] Revisit the model tier and the vector store using real numbers.

---

## 5. Open decisions for the project owner

1. **Repository visibility.** The GitHub repository is currently public. Recommended: make it private (see [security.md](security.md) section 1).
2. **Model tier and monthly budget cap** for the public chatbot.
3. **Embeddings provider** account.
4. **Languages** at launch: English only, or English and Arabic.
5. **Lead consent wording** in the chat widget, and whether chat transcripts are attached to the Zoho lead.
6. **Hosting**: stay on shared cPanel (the plan works there) or move to a VPS (allows a real vector database and background workers).
