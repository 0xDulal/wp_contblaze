# ContBlaze — Development Instructions

Instructions for Claude and human developers working on ContBlaze. Read this file before writing any code.

## 1. Product Identity

- **Name:** ContBlaze
- **Tagline:** The AI Engine for Content Growth
- **Platform:** WordPress plugin
- **Users:** Bloggers, businesses, SEO agencies
- **Vision:** Automate the full content lifecycle — ideation, creation, optimization, repurposing, publishing, analytics — inside WordPress, with the user in control of brand, quality, and workflow.

## 2. Technology Stack

- PHP 8.3+ (strict types where practical)
- WordPress APIs: hooks/filters, options API, post meta, Settings API, REST API, admin screens
- JavaScript: ES2022+, vanilla-first; build tooling only when a module genuinely requires it
- HTML5 / CSS3
- Local environment: WordPress Studio on Windows (Git Bash terminal, VS Code editor)

## 3. Modular Architecture Principles

- Each of the ten modules (AI Writer, SEO Assistant, Content Planner, AI Repurposing, Auto Publisher, Bulk Generator, Content Refresh, Brand Voice, Content Workflow, Content Analytics) is a self-contained module.
- Modules live under their own namespace/directory and must not reach into another module's internals. Communication between modules goes through defined hooks/filters or a small shared service container.
- A module must degrade gracefully when disabled: no fatal errors, no orphaned data corruption, no broken admin screens.
- Shared code (DB access, settings, HTTP client for AI providers, logging) lives in a common core layer; modules depend on core, never sideways.
- Every module registers its assets, REST routes, admin pages, and activation/deactivation logic itself. No module may modify core plugin behavior directly.
- Follow YAGNI: do not build configuration points, abstractions, or extension hooks until a real second use case exists.

## 4. WordPress Coding Standards

- Follow WordPress PHP Coding Standards (WPCS): `snake_case` functions and variables, `StudlyCaps` classes, hook naming `contblaze_{module}_{thing}`.
- Prefix everything user-facing with `contblaze` (text domain: `contblaze`), option names `contblaze_*`, transients `contblaze_*`, CSS classes `contblaze-`, JS globals under `contblaze`.
- File headers for PHP files follow the WordPress plugin file header format.
- Escape late, sanitize early. Use the documented WordPress functions listed below — never custom "clever" sanitizers.

## 5. Security Requirements (non-negotiable)

### Input Validation and Sanitization
- Sanitize ALL input on arrival: `sanitize_text_field()`, `sanitize_textarea_field()`, `sanitize_key()`, `absint()`, `wp_unslash()` on `$_POST`/`$_GET`/`$_REQUEST`.
- Validate structure and type before use; reject unknown values, never silently coerce.
- For REST routes, use `sanitize_callback` on every argument registered with `register_rest_route()`.

### Output Escaping
- Escape ALL output at the last moment: `esc_html()`, `esc_attr()`, `esc_url()`, `esc_textarea()`, `wp_kses_post()` for rich content.
- Never echo unescaped variables, even "trusted" ones. Dynamic attributes get `esc_attr()`, URLs get `esc_url()`.

### Capability Checks and Nonce Verification
- Every state-changing action (admin POST, AJAX, REST write) requires:
  1. `current_user_can( '<appropriate_cap>' )` — use specific capabilities (`manage_options` for settings, `edit_posts`/`edit_others_posts` for content), never a blanket admin check where a finer one exists.
  2. Nonce verification: `wp_nonce_field()` / `wp_create_nonce()` on output, `check_ajax_referer()` or `wp_verify_nonce()` on receipt. For REST, rely on `X-WP-Nonce` + `permission_callback` (required on every route — never omitted, never `__return_true` for write routes).
- Fail closed: on failed check, `wp_send_json_error()` / `wp_die()` — never fall through with partial action.

### API Key Protection
- AI provider API keys are stored server-side only (options table, encrypted at rest where feasible; never in JS-localStorage, never printed in HTML, never exposed to the browser, never logged).
- Keys are sent only server-to-server to the configured provider. All AI calls originate in PHP; the browser never talks to a provider directly.
- Settings screens mask keys (show last 4 chars only); keys are excluded from any REST response, export, or log output.
- `.env` / secret files are git-ignored (see `.gitignore`); never commit keys.

### Database Security
- ALL queries use `$wpdb->prepare()` — no string interpolation into SQL, ever.
- Tables created at activation use `dbDelta()` with a `db_version` option and an upgrade routine; custom tables are prefixed `{$wpdb->prefix}contblaze_`.
- Respect the user's data: no storing raw prompt/response logs longer than needed; document retention; provide uninstall cleanup via `uninstall.php` conventions.

### Error Handling
- Fail visibly in development (`WP_DEBUG` logging), gracefully in production: catch exceptions, log with `error_log()` or a plugin logger, show user-friendly admin notices — never raw stack traces to end users.
- External AI calls: always wrapped in try/catch with timeouts, retry-with-backoff on transient failures, and clear user-facing error states. Never let a provider outage fatal the admin.

## 6. AI Provider Integration Principles

- Provider-agnostic core: one shared HTTP client layer in core; modules request "generate," "summarize," "embed," etc. through an internal interface, not by calling OpenAI/Anthropic/Google SDKs directly from module code.
- Model/provider selection is configuration, not hard-coded. Adding a provider = adding an adapter in core, not editing modules.
- Streaming, token counting, and rate limits are handled once in the shared layer.
- Every AI request is attributable: log provider, model, prompt template ID, token usage (not raw keys).
- Respect user content ownership: never train on or retain user content beyond what the provider requires.

## 7. Testing Requirements

- Before any merge, the feature must be manually tested against its acceptance criteria in the local WordPress Studio environment.
- PHP syntax check (`php -l`) on every touched file before commit.
- Non-trivial logic (calculations, parsing, rate limiting, prompt assembly) gets an automated test (PHPUnit as the plugin grows; at minimum an assert-based self-check script).
- No merging with known broken functionality or failing checks.

## 8. Git Workflow

### Branching Strategy
- `main` — production-ready code only. Never develop directly on it.
- `develop` — integration branch; feature branches merge here after testing.
- `feature/<module-or-topic>` — one branch per feature/module slice (e.g., `feature/ai-writer-editor-integration`).
- Branches are short-lived; merge to `develop` promptly after verification.

### Commit Message Conventions
- Format: `type: short imperative summary` (e.g., `feat: add settings page skeleton`, `fix: escape API key in admin notice`).
- Types: `feat`, `fix`, `refactor`, `docs`, `test`, `chore`, `perf`, `security`.
- Small, meaningful commits — one logical change each.
- NEVER include Claude attribution or Co-authored-by trailers (enforced by `.githooks/commit-msg`).
- Use the user's configured Git identity. Never modify `user.name`/`user.email`.

### Hard Rules
- Never force-push or rewrite shared history.
- Never create commits or merge branches without explicit user approval.
- Test before merging.

### Feature-by-Feature Implementation Workflow
1. Confirm scope of the module/feature slice with the user.
2. Create `feature/<topic>` branch off `develop`.
3. Implement in small commits (`feat:`/`fix:`/`chore:` as appropriate).
4. `php -l` on touched files; manual test in WordPress Studio.
5. User verifies; with explicit approval, merge to `develop`.
6. Repeat per module. `develop` → `main` only at release points, with user approval.
