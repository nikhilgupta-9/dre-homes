# DRE Homes — Working Rules

These rules apply to every change in this repository, by a person or by an AI coding assistant. They exist because of specific problems found in the code; the reasons are in [security.md](security.md) and the roadmap is in [plan.md](plan.md).

---

## 1. Git

1. **Never commit secrets.** No passwords, API keys, tokens or client secrets in any tracked file — not in comments either. Real values go in the git-ignored config files; tracked `*.example.php` files hold placeholders only.
2. **Never commit uploads or personal data.** `admin_nsrdwsc/assets/media/`, `resume/`, database dumps, logs and exports stay out of git.
3. **Check before every commit**: run `git status` and `git diff --staged`, and read them. Do not use `git add -f` to get past `.gitignore`.
4. **No file larger than 50 MB** in git. Videos and archives live on the server.
5. **One topic per commit**, with a message that says what changed and why. Work on a branch, merge to `main` by pull request.
6. **No backup copies in the repo.** Do not create `file.php--`, `file-old.php`, `file2.php` or `file-new.php`. Git is the backup. Delete test files (`z.php`, `info.php`) before committing.

## 2. PHP code

1. **Every query that includes a variable uses a prepared statement** (`$conn->prepare` with `bind_param`). No string-joined SQL with request data, including values that "should" be numbers. Do not add new callers of `select_row`, `select_all`, `select_query`, `query` or `query_detail` with request input in the condition string.
2. **Escape all output.** Anything from the database or the request is printed through `htmlspecialchars()`, unless it is an admin-authored rich-text field printed on purpose.
3. **Every admin file starts with an auth check** — pages, AJAX endpoints and `push-app/` scripts alike — and calls `exit` right after a redirect header.
4. **State-changing actions use POST with a CSRF token.** No deletes or updates through a GET link.
5. **File uploads**: check the extension against an allowlist, check the real MIME type, generate the stored filename on the server, and never accept `.php` or other executable types.
6. **No credentials or environment URLs in code.** Read them from the config layer. Switching between local and production must not require editing tracked files.
7. **Errors**: `display_errors` is off in production; log with `error_log()`. Never `echo` a raw database or API error to the visitor.
8. **Match the existing style** of the file being edited (tabs, naming, layout). Do not reformat whole files in a feature commit.
9. **Third-party libraries come through Composer**, not by copying folders into the project.

## 3. Database

1. **Schema changes are scripts in `db/migrations/`**, numbered and committed. No ad-hoc changes in phpMyAdmin that others cannot reproduce.
2. **New tables** use the `tbl_` prefix, `utf8mb4`, an auto-increment primary key and a `tstp` timestamp, like the existing ones.
3. **Numbers are stored as numbers, dates as dates.** No new `varchar` columns for prices, sizes, counts or dates.
4. **Deletes go through the Logger** (`archiveDeletedRecord`) as the existing admin pages do.
5. **Production data never goes into git or into a chat with an AI tool.** Use the local database for development.

## 4. AI and RAG rules

1. **API keys stay on the server.** The browser talks to our PHP endpoints only. No key, token or provider URL in JavaScript.
2. **Indexing uses an allowlist.** Only the tables listed in [plan.md](plan.md) section 3.4 are indexed. Lead, enquiry, resume, login and log tables are never sent to any AI or embeddings provider.
3. **The model never writes SQL.** It calls tools with typed arguments; our PHP code validates each argument and runs prepared queries.
4. **Facts come from tools.** Prices, availability and handover dates are read from the database at answer time. The model's own memory is not a source.
5. **Retrieved text is data, not instructions.** Content from the database or from the visitor cannot change the system prompt, the tool list, or the rules.
6. **Tools are read-only**, except `create_lead`, which can only insert into the enquiry table with validated fields.
7. **Limits on every public AI endpoint**: rate limit per IP and per session, maximum message length, maximum turns per conversation, maximum output tokens, and a daily spend cap that turns the feature off when reached.
8. **AI-written content is always reviewed by a person** before it is published. The admin helper fills a form field; it never saves on its own.
9. **Log usage, not secrets.** Record tokens, cost, latency and the tools called for each request. Do not log API keys. Chat transcripts are personal data and follow the retention period in [security.md](security.md).
10. **Use the official Anthropic PHP SDK** and the current model IDs from the Anthropic documentation. Keep the model name in one config value.
11. **Prompt changes are tested.** Run the evaluation question set before and after any change to the system prompt, the tools or the model.
12. **Tell visitors they are talking to an AI**, and always offer a way to reach a human agent.

## 5. Deployment

1. Deploy from git, not by editing files on the server over FTP.
2. Take a database backup before any migration.
3. Never upload `.git/`, `.env`, `*.example.php` secrets, `plan.md`, `rule.md` or `security.md` into the public web root unless `.htaccess` blocks them.
4. After deploying, check: home page, one listing page, one property page, one lead form, admin login.
