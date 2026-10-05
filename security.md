# DRE Homes — Security Review and Requirements

Date: 2026-10-05
Scope: a read-through of the codebase done while planning the AI features. It is not a full penetration test. Secret values are deliberately not written in this file.

Related documents: [plan.md](plan.md), [rule.md](rule.md).

---

## 1. Repository exposure — act first

- **The GitHub repository `nikhilgupta-9/dre-homes` is public.** Anyone can read the full source, including the admin folder name and every weakness listed below.
- **Recommendation: make the repository private** (GitHub → Settings → General → Change visibility). This is client source code and it uses a licensed admin theme.
- What was kept out of the first push: all config files with credentials, files with hardcoded live keys, candidate CVs, uploaded media, logs, and the unused theme demo pages. See `.gitignore`.

## 2. Credentials found in source — rotate all of them

These were found in plain text in the working folder. They were **not pushed**, but they have lived in files that are copied over FTP and shared between developers, so treat each one as exposed and replace it.

| # | Credential | Where | Action |
|---|---|---|---|
| 1 | Production MySQL user and password | Commented-out line in `db/config.php` and `admin_nsrdwsc/db/config.php`; live in `submit_feedback.php` | Change the password in cPanel. Remove from all files. |
| 2 | Zoho CRM function API key (`zapikey`) | `admin_nsrdwsc/ajax/auto_push_crm.php` | Regenerate in Zoho. Move to config. |
| 3 | Meta / Instagram access token and app **client secret** | `z.php`, inside browser JavaScript | Reset the app secret and revoke the token in Meta. A client secret must never be sent to a browser. Delete `z.php`. |
| 4 | Exchange-rate API key | `api.php` and `js/custom.js`, `js/custom2.js`, `js2/custom.js` | The JavaScript copies are already public on the live site. Regenerate and call the API from PHP with a cache. |

Files 1–3 and `api.php` are git-ignored for now. They return to git only after the secret is read from config.

**Target state:** one git-ignored secrets file outside the web root (or environment variables), loaded by `db/config.php`. This is also where the AI provider keys will live.

## 3. Findings in the existing code

### Critical

| # | Finding | Where | Fix |
|---|---|---|---|
| C1 | **SQL injection**: request values are placed directly into SQL strings | `ajax/get-suggestions.php` (`keyword`, `filter1`, `filter2`), listing pages (`buy-property.php`, `rent-property.php`, `off-plans.php`, `ready-to-move-in.php`, `international-properties.php`), the single-property pages through `select_project_detail()`, several admin `manage-*.php` pages | Prepared statements everywhere. `mysqli_real_escape_string` is not enough for unquoted numeric values such as `prj_type_id = $keyword`. |
| C2 | **Admin endpoints with no login check** | `admin_nsrdwsc/ajax/`: `img_remove.php`, `pdf_remove.php`, `remove_header_image.php`, `proj_thumb_img.php`, `add-inventory-type.php`, `dev-order-update.php`, `auto_push_crm.php`; all of `admin_nsrdwsc/push-app/*.php` | Add the session check to every file. Anyone who knows the URL can currently delete files or trigger pushes. |
| C3 | **Auth check does not stop the page** | `admin_nsrdwsc/lock2.php` sends a redirect header but does not call `exit`, so the rest of the page still runs for a logged-out visitor — including the delete handler at the top of `manage-project.php` | Add `exit;` after the header in `lock.php` and `lock2.php`. |
| C4 | **Passwords hashed with unsalted MD5** | `admin_nsrdwsc/ajax/login.php`, `admin_nsrdwsc/add-user.php` | Use `password_hash()` and `password_verify()`; upgrade each user's hash at their next login. |
| C5 | **`info.php` exposes `phpinfo()`** | project root | Delete it from the server. |

### High

| # | Finding | Where | Fix |
|---|---|---|---|
| H1 | **No CSRF protection anywhere**, and deletes run on GET links | all admin pages, for example `manage-project.php?del=` | CSRF token on every form; deletes by POST only. |
| H2 | **File uploads are not validated** — about 158 `move_uploaded_file` calls, original filename kept, no type check | admin add / edit pages, `uploadFileWithFolder()`, careers resume upload | Extension allowlist, MIME check, server-generated names, and an `.htaccess` in upload folders that disables PHP execution. |
| H3 | **Stored and reflected XSS** — database and request values printed without escaping | `ajax/get-suggestions.php`, breadcrumbs and titles in the single-property pages, `$_SESSION['username']` in admin headers | `htmlspecialchars()` on output. |
| H4 | **No brute-force protection on admin login**; folders created with mode `0777` | `admin_nsrdwsc/ajax/login.php`, `uploadFileWithFolder()` | Lockout after repeated failures using the existing `tbl_login_logs`; use `0755`. |
| H5 | **Candidate CVs are stored inside the web root** with predictable paths | `resume/` | Move outside the web root and serve through an authenticated admin script. |
| H6 | **Client IP is taken from spoofable headers** | `Logger::getClientIP()`, `get_client_ip()` | Use `REMOTE_ADDR` unless behind a known proxy. This matters for login logs and for AI rate limits. |

### Medium

- Session cookies have no `HttpOnly`, `Secure` or `SameSite` flags set; the session id is not regenerated at login.
- Raw errors are shown to visitors: `zoho-callback.php` turns `display_errors` on and prints the token response; `die('…'.mysqli_error())` in config.
- `zoho-callback.php` would write OAuth tokens to `zoho_tokens.json` inside the web root.
- The admin panel relies on a hard-to-guess folder name. That name is now in a public repository; it was never real protection. Add IP restriction or HTTP auth in front of it.
- No security headers (`Content-Security-Policy`, `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`).
- Lead forms email personal data in plain text and have only a simple image captcha against spam.
- Debug log `ajax/debug_blog.log` is written inside the web root.
- A logic bug that touches security: the delete link in `manage-project.php` has a base64 id in `href` and a plain id in `data-url`, while the handler calls `intval()` on whatever arrives. The JavaScript confirm uses `data-url`, so it works in normal use, but if the `href` is followed directly the id becomes a wrong number and the handler still runs its `unlink()` calls with empty filenames. The handler should check that the record exists before touching files.

## 4. Security requirements for the AI features

These are conditions for building what is described in [plan.md](plan.md), not optional extras.

### Keys and cost

- AI provider keys are stored only in the server-side secrets file. Never in JavaScript, HTML, git, logs or error messages.
- Use separate keys for local and production, with a spend limit set in the provider console.
- A daily token and cost cap in our own code (`tbl_ai_usage`). When the cap is reached the chatbot shows a "contact us" fallback and stops calling the API.
- Per-IP and per-session rate limits on `/ai/chat.php`; maximum message length; maximum turns per conversation; maximum output tokens per reply.

### Prompt injection

- Treat every visitor message and every retrieved chunk as untrusted text. Blog and listing content is editable by admins and may contain instructions; the system prompt states that retrieved content is reference material only.
- The model has no tool that can run SQL, read files, send email to arbitrary addresses, or fetch URLs.
- Tool arguments are validated in PHP (types, ranges, allowed values) before use, and all database access in tools uses prepared statements.
- `create_lead` validates name, phone and email formats, is rate limited per session, and cannot be used to write to any other table.

### Data leakage

- The index is built from an allowlist of tables and columns. Internal columns (`proj_email`, `proj_mobileno`, `prop_user_id`, admin notes), inactive rows, and every lead, resume, login and log table are excluded.
- A test in the indexing code fails if a non-allowlisted table is ever read.
- The chatbot cannot see other visitors' conversations or any lead data.
- Tool results are scoped to what the public site already shows. The assistant must not reveal the system prompt, tool definitions or internal ids.

### Output safety

- Model output is rendered as text, or through a small safe Markdown renderer. It is never inserted as raw HTML.
- Links in answers are built by our code from slugs returned by tools, limited to our own domain.
- No guarantees about price, return on investment, visa approval or legal matters; the assistant gives general information and offers a human advisor.

### Privacy

- The chat widget states that the visitor is talking to an AI and links to the privacy policy.
- Contact details are saved only with clear consent in the conversation or through a form.
- Chat transcripts are personal data: keep them for a fixed period (proposed 90 days), then delete or anonymise. Update the privacy policy to mention the AI provider as a processor.
- Do not send personal data to the embeddings provider; only public listing and article content is embedded.
- UAE data-protection law (PDPL) and, for EU visitors, GDPR apply to lead and chat data. Have the wording reviewed by a qualified person before launch.

### Admin AI helper

- The endpoint requires an admin session and a CSRF token.
- Generated text is placed in the form for review and is never saved or published automatically.

## 5. Order of work

1. Make the repository private. Rotate the four credentials in section 2.
2. C3, C5 and C2 — each is a few lines and closes the widest holes.
3. Secrets file and config loader (needed for the AI keys anyway).
4. C1 on the public endpoints, then the admin pages.
5. C4, H1, H2.
6. The remaining high and medium items, alongside the AI phases.

No public AI endpoint goes live before steps 1–4 are done.

## 6. Checklist before each release

- [ ] `git diff --staged` contains no keys, passwords, tokens, dumps, CVs or uploads
- [ ] New queries use prepared statements
- [ ] New admin files have the auth check and `exit`
- [ ] New forms have a CSRF token
- [ ] New output is escaped
- [ ] AI endpoints: rate limit, size limits and spend cap confirmed working
- [ ] No debug files (`info.php`, `z.php`, `*.log`) on the server
