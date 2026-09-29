# Gerotech → WordPress Implementation Plan
## Static HTML theme + ACF fields (no page builder)

**Status:** Child theme **live on gerotechdev** (2026-09-18). Homepage + 4 ES pages ACF-wired. Legacy pages ACF = Phase 2.
**Created:** September 16, 2026
**Owner:** CloudMellow (retainer build)
**Source of truth:** `wp-content/themes/gerotech-child/` in this repo
**Companion docs:** `handoff/gerotech-handoff-plan.docx` (PM-facing), this file (build-facing)

---

## 0. Picking this up cold

**Read first:** `.clinerules` (**Next session — pickup here**) → `JOURNAL.md` (newest first) → this file (§12b, §13, §14).

**Where we are (2026-09-16):**
- Child theme `wp-content/themes/gerotech-child/` built and running in **LocalWP** (`gerotech.local`).
- **Phases 1–3 done** for **9 pages**: homepage, 4 ES pages, Training, Support, About, Contact.
- **ACF:** homepage + 4 ES pages + global testimonials options page. Legacy Training/Support/About/Contact ACF = Phase 2.
- **Push to Dev: done 2026-09-18** (child theme files only). See §14.

**How to resume locally:**
1. Open **LocalWP** → start the **gerotech** site (`~/Local Sites/gerotech/app/public`, `https://gerotech.local`).
2. Theme source of truth is this repo — after editing, copy into the Local site:
   ```bash
   cp -R wp-content/themes/gerotech-child/. "$HOME/Local Sites/gerotech/app/public/wp-content/themes/gerotech-child/"
   ```
3. Lint with Local's PHP:
   ```bash
   "$HOME/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php" -l <file>
   ```
4. Local MySQL socket: `~/Library/Application Support/Local/run/koy2IPsI5/mysql/mysqld.sock` (root/root, db `local`).

**Gotchas (see §12):** the legacy parent needs `js_to_footer` neutralised (already done in `inc/enqueue.php`); local `wp-config.php` carries non-shippable workarounds; the local cert was trusted manually.

**Decisions:** §11 is resolved; §12b holds the page/slug mapping.

---

## 1. The model

- Each prototype page becomes a **PHP page template**; section markup is copied verbatim, not rebuilt.
- Client-editable content becomes **ACF fields**; everything else stays in code.
- Header/footer partials become real theme files.
- **No builder, no shortcodes** — no lock-in, no grid conflict.

**Trade-off:** the client edits content within a fixed layout and can add/remove/reorder items inside lists, but cannot drag whole sections. Set expectations accordingly.

---

## 2. Handoff artifacts

| Artifact | Purpose | Status |
|---|---|---|
| `wp-content/themes/gerotech-child/` | The actual build (source of truth) | ✅ 9 pages |
| `handoff/theme-map.md` | HTML file → PHP template; partial → template part | ✅ (slugs superseded by §12b) |
| `handoff/acf-spec.md` | Field groups: name, type, location, consuming template | ✅ draft — reconcile in Phase 4 |
| `handoff/component-inventory.md` | Section → BEM root → editable fields → JS dependency | ✅ |
| `handoff/asset-manifest.md` | Theme-bundled vs. Media Library; final vs. stand-in | ✅ |
| `handoff/js-spec.md` | Each script, selectors, init conditions, a11y/motion | ✅ |
| `handoff/qa-checklist.md` | Breakpoints, browsers, a11y, editor walkthrough | ✅ |
| Tagged prototype `v1.0-prototype` | Frozen visual + markup source | ⬜ not tagged (repo uses the theme as source now) |

---

## 3. Theme architecture (as built)

Child theme of `gerotech`. Actual tree:

```
wp-content/themes/gerotech-child/
├── style.css                 # child theme header only (no rules)
├── functions.php             # bootstrap, theme supports, menus
├── header.php                # skip link + alert banner + sticky header + mega-nav + mobile nav + search modal
├── footer.php                # footer + wp_footer()
├── index.php                 # fallback loop
├── front-page.php            # homepage
├── page-engineered-solutions.php
├── page-modification-of-standard-machine-tools.php
├── page-unique-applications-for-standard-machines.php
├── page-automated-system.php
├── page-training.php
├── page-support.php
├── page-about.php
├── page-contact.php
├── template-parts/
│   └── sections/testimonials.php
├── inc/
│   ├── helpers.php           # gerotech_page_url()/link(), quote mailto, asset version
│   └── enqueue.php           # fonts, CSS order, conditional JS, dequeue parent
└── assets/                   # css/ (4), js/ (7), images/ (22 + 2 gallery sets)
```

**Not yet created (Phase 4+):** `inc/acf-fields.php`, `inc/options.php`, `inc/cpt.php`, additional `template-parts/` (global/sections/cards). Sections currently live inline in the page templates.

**Template strategy:** `page-{slug}.php` auto-applies by slug (no admin assignment). Slugs intentionally match the dev URLs (see §12b).

---

## 4. HTML → PHP conversion map

| Prototype | Becomes |
|---|---|
| `<div data-include="partials/site-header.html">` | `<?php get_header(); ?>` |
| `<div data-include="partials/site-footer.html">` | `<?php get_footer(); ?>` |
| `<div data-include="partials/testimonials-block.html">` | `<?php get_template_part('template-parts/sections/testimonials'); ?>` |
| `<link rel="stylesheet" href="assets/css/*.css">` | `wp_enqueue_style()`; parent CSS dequeued |
| `<script src="assets/js/*.js">` | `wp_enqueue_script()`, loaded per template |
| `include-partials.js` | **Deleted** — replaced by native PHP includes |
| Every `<img src="...">` | `get_field()` → `wp_get_attachment_image()` |

Section markup is unchanged; only strings/images become field calls.

---

## 5. ACF field model

**Global — options page "Site Content":** contact numbers, alert banner, logo + alt, header CTA, search modal, the full menu tree (items, Machines panel, ES panel), footer brand/socials/columns/legal, and the shared testimonials repeater. Implemented — see `acf-spec.md` §1 for the real field names.

**Per page:** homepage = hero slides, stats, Haas intro + features, machine-lineup panels, news lead + items, CTA, email signup. Interior = page-hero, section headers, card-grid repeater, CTA, email signup.

**Three constraints:**

1. **Repeaters can't nest.** News lead → **Group** (groups can hold repeaters). Footer/nav columns are repeaters of *link* repeaters, which ACF does support — only a repeater directly inside a repeater's value object is the problem.
2. **Accent words.** Map WYSIWYG `em`/`i` → accent style; client italicises the word.
3. **Machine lineup** = one flat repeater with `panel_type` select + conditional fields.

**v1 content model:** testimonials = global ACF; news/training cards = ACF repeaters. CPTs later on retainer.

---

## 6. JS + assets

- Enqueue scripts conditionally (slider on home, filter on ES, modal where modals exist).
- **No builder → no AJAX re-init problem**; scripts run once on load as prototyped.
- Preserve the one-instance-per-page assumption in `machine-tabs.js` / `modal.js`.
- Brand assets (logos, icons, watermark, F1 lockup) theme-bundled; content photos in Media Library.
- Adobe Fonts kit `lqh7ybe` is domain-locked — add WP Engine production + staging domains.

---

## 7. Forms and dynamic bits

- Email signup → Constant Contact (CF7 interim).
- Get a Quote → `mailto:` v1; CF7 later.
- Search modal → prototype quick-links v1; WP search later.
- Navigation **hardcoded in `header.php`** for v1 — not client-editable at launch (retainer item).

---

## 8. Build sequence

| Phase | Work | Days | Status |
|---|---|---|---|
| 1 | Inspect staging theme; confirm child-vs-new; check CPT registration | 0.5 | ✅ done (§12) |
| 2 | Theme skeleton: child theme, header/footer, enqueue, dequeue parent | 2 | ✅ done |
| 3 | Convert pages HTML→PHP (hardcoded markup) | 3 | ✅ done — 9 pages (§12b) |
| 4 | ACF Pro + options page + field groups (PHP registration) | 2 | ⬜ **next** (§13) |
| 5 | Wire fields into templates | 3 | ⬜ |
| 6 | Forms + search wiring (CF7/WPForms, search) | 1 | ⬜ |
| 7 | Asset migration + fonts domain | 1 | ⬜ |
| 8 | Content entry + client editor walkthrough | 2 | ⬜ |
| 9 | QA (responsive, a11y, cross-browser, WP Engine cache) | 2 | ⬜ |

Remaining phases 4–9 ≈ 11 days. Pages beyond the 9 in scope (Careers, Machines, Service, News, remaining ES sub-pages) are additional. Push to dev is a separate, deferred step.

---

## 9. Risks

- **Parent theme is legacy** — handled: child overrides templates and neutralises `js_to_footer` (§12). Don't edit the parent (rollback path).
- **URL/slug strategy** — resolved (Mapping A, §12b): our templates reuse dev slugs. Watch for dev pages with duplicate slugs (`/application-support/`, `/rotary-repair/`).
- **ACF Pro license** — active on dev; confirm it covers production.
- **Unsplash stand-ins** must be replaced before launch (see `asset-manifest.md`).
- **Nav not editable** at launch (documented).
- **Push breaks unconverted pages** on dev (§14).
- **WP Engine caching** — purge on content updates.

---

## 10. Division of labour

Retainer build: we do phases 1–7, then content entry + client walkthrough (8). Split front-end theme (us) vs. ACF/forms/integration (WP dev) only if desired.

---

## 11. Decisions (resolved 2026-09-16)

1. **Child theme vs. new theme** — ✅ **child theme of `gerotech`**. Parent is legacy but the child overrides `header.php`/`footer.php`/`front-page.php` and neutralises the parent's footer-script hack (§12).
2. **Navigation** — ✅ **hardcoded in `header.php`** for v1 (desktop + mobile). Now links Machines, Engineered Solutions, Training, Support, About, Contact. Editable WP menus = retainer item.
3. **URL strategy** — ✅ **Mapping A**: our design takes over dev's existing URLs. Templates named after dev slugs; `gerotech_page_url()` is the single mapping seam (§12b).
4. **CPTs now or ACF-only v1** — ✅ **ACF-only v1** (CPTs already exist on the parent for case-study/training-session/testimonial/people/career — reuse, don't re-register).
5. **Accent-word editing** — ✅ WYSIWYG `em`/`i` → `.accent` / `.accent--deep` (implement in Phase 4/5).

---

## 12. Phase 1 findings — staging inspection (2026-09-16)

Parent theme pulled from `gerotechdev` via SFTP and inspected locally (LocalWP).

**Parent `gerotech` (author Tim Bomers / phiregroup, v2.o):**
- Legacy theme, ~73MB (bundled `fonts/`, `images/`, `videos/`), its own `page-*.php` templates (`page-home`, `page-about`, `page-machines`, `page-training`, `page-support`, `page-service`, `page-solutions`, `page-contact`, `page-industries`, `page-products`, `page-news`, `page-sitemap`, `page-calendar`).
- **Does not use `wp_enqueue_style`** — hardcodes `style.css` + `fonts.css` as `<link>` in its own `header.php`. The child overrides `header.php`, so parent CSS never loads (intended).
- **`js_to_footer()`** removes core `wp_enqueue_scripts`/`wp_print_head_scripts` from `wp_head` and re-adds them on `wp_footer` — would push the child's CSS to the footer (FOUC). **Child removes this action at priority 1.**
- Enqueues jQuery-dependent `site-scripts`, `home_script` (front page), `ts_script` (training-session). **Child dequeues all three.**
- Homepage header injects Smart Slider 3 shortcodes (`[smartslider3 slider=2]`, `slider=1`) — gone with the child `header.php`.
- PHP notice at `functions.php:13` (`add_theme_support( $feature, $arguments )` with undefined vars) — only visible with `display_errors` on; left as-is, quieted locally.

**CPTs already registered by the parent (do not re-register):** `case-study`, `training-session`, `testimonial`, `people`, `career`.

**ACF already in use:** parent calls `acf_add_options_page()` — reuse the existing options page rather than adding a second.

**Menus:** `menu-primary`, `menu-mobile`, `menu-footer`, `menu-service`.

**Resolved:** child-theme approach works (parent is legacy but the child overrides `header.php`/`footer.php`/`front-page.php`, and neutralises the footer-script hack). Slug collisions confirmed for `/training/`, `/support/`, `/about/`, `/careers/`.

**Local setup:** LocalWP site `gerotech` (nginx, PHP 8.2.29, MySQL 8.4, WP 7.1) at `~/Local Sites/gerotech/app/public/`; parent theme pulled via SFTP; child theme copied in and activated; homepage verified at `http://gerotech.local/` (desktop + mobile, no console errors).

**Local-only `wp-config.php` workarounds (do not ship):**
- `@ini_set('display_errors','0')` — hides the parent theme's `functions.php:13` notices.
- `WP_HTTP_BLOCK_EXTERNAL` + `DISABLE_WP_CRON` — **required on this macOS build**: WP admin's outbound/loopback requests load Apple's `Network` framework, after which php-fpm aborts on its next fork (`crashed on child side of fork pre-exec`) → 502 on `/wp-admin/`. Blocking external HTTP prevents the framework from loading. Front end is unaffected.
- Local admin login was reset to `matt` / `localpass123` for verification.

---

## 12b. Build scope — updated 2026-09-16

**Scope is now 9 pages: homepage + ES section (4) + the nav pages Training/Support/About/Contact.**

Dev (`gerotechdev.wpenginepowered.com`) has a different, deeper IA (30 pages) than the prototype. **Mapping A**: our design takes over dev's existing URLs, so templates are named after the dev slugs.

| Our template | Dev slug / URL | Dev page ID |
|---|---|---|
| `front-page.php` | `/` (`/home/`) | 11 |
| `page-engineered-solutions.php` | `/engineered-solutions/` | 28 |
| `page-modification-of-standard-machine-tools.php` | `/modification-of-standard-machine-tools/` | 1250 |
| `page-unique-applications-for-standard-machines.php` | `/unique-applications-for-standard-machines/` | 1265 |
| `page-automated-system.php` | `/automated-system/` | 1239 |
| `page-training.php` | `/training/` | 39 |
| `page-support.php` | `/support/` | 1535 |
| `page-about.php` | `/about/` | 14 |
| `page-contact.php` | `/contact/` | 16 |

`gerotech_page_url()` maps prototype slugs → dev URLs. All internal links + nav flow through it.

**Contact** has no static prototype page — built from dev's real contact content (heading, departments, locations) in the project design system; new `.contact-*` component added to `components.css`. Form is static (CF7/WPForms later). Emails use `@gerotech.com` (dev showed masked `wpenginepowered.com` addresses — verify).

**Local state (2026-09-16):** full pull of `gerotechdev` → local (`gerotech.local`, https) done. Child theme active; the 9 mapped pages had their parent `_wp_page_template` cleared so slug templates apply. All 9 verified 200 at 360–1920, no overflow, no console errors.

**Push to Dev (2026-09-18):** child theme live on `gerotechdev` (files only). Unconverted URLs still unfinished. See §14.

**Notes:** site-wide reCAPTCHA badge (plugin) shows bottom-right on all pages. Unconverted local pages render unfinished (parent templates + child CSS). Out of scope: Careers, Machines, Service, Contact-adjacent pages, News, and the remaining ES sub-pages.

---

## 13. Phase 4 — ACF (next up)

**Goal:** make the 9 built pages client-editable. ACF Pro is already active; the parent registers field groups but they're tied to the **old** markup, so our sections need **new groups** (don't reuse the parent's blindly).

**Progress (2026-09-18):**
- ✅ `inc/acf-fields.php` created + required from `functions.php`; **Homepage group** (`group_home_content`, location `page_type == front_page`) registered and verified in the editor — tabs: Hero slides, Stats, Haas Relationship, Machine Lineup, CTA Band, Mailing List.
- ✅ `front-page.php` wired to ACF with the current design as **defaults** (renders correctly even with empty fields — important for a theme-only push). No regression.
- ✅ ES hub + MCS + Applications + Automation groups wired (2026-09-18).
- ✅ Testimonials global repeater (options page `gerotech-site-content`).
- ✅ **Phase 2 — legacy pages wired:** `inc/acf-legacy-fields.php` (7 groups by `post_name`) + all 7 templates read ACF with the current markup as defaults. Forms/maps/inspection checklists stay hardcoded.
- ✅ **Careers** built from `careers.html` + `group_careers_content`.
- ⬜ Remaining: editable WP menus, per-page email-form internals (deferred), any new pages.

**Editor model:** plain-text textareas; wrap the accent phrase in `<em>…</em>`; line breaks become the design's forced breaks. Machine-lineup tags = one `Label | URL` per line (flat — repeaters can't nest). Panels are one flat repeater.

**Steps for the remaining pages:**
1. Add a field group per template in `inc/acf-fields.php` (location by `page_template` or page slug).
2. Wire each `page-*.php` to read fields with the current markup as defaults (same pattern as `front-page.php`).
3. Reuse the parent's existing options page for globals (alert-banner phones, header CTA, footer columns, shared testimonials).

**First action on pickup:** `.clinerules` **Next session** block. Then either reCAPTCHA Dev domain, or Phase 2 ACF on legacy pages — do not re-register ES ACF (already wired).

---

## 14. Push to Dev (first drop done 2026-09-18)

**Done:** Local → `gerotechdev`, **theme files only** (`gerotech-child`), no database. Child **activated**. Homepage verified. Haas F1 uses `haas-f1-team.jpg` (the `.png` 403s). Stage is the Dev rollback.

**Repeat this way:**
- Environment: Development (`gerotechdev`) — never Production
- Files: Select → `wp-content/themes/gerotech-child/` only
- Uncheck Database, `wp-admin`, `wp-includes`, `uploads`
- If Local hides the theme (chmod-only change): switch filter from “only newer files” to all files
- Purge WP Engine cache; hard-refresh

**Direct deploy (discovered 2026-09-18, no Local GUI needed):**
```bash
KEY="$HOME/Library/Application Support/Local/ssh/wpe-connect"
SSH_CMD="ssh -i '$KEY' -o BatchMode=yes"
rsync -avz -e "$SSH_CMD" \
  wp-content/themes/gerotech-child/ \
  gerotechdev@gerotechdev.ssh.wpengine.net:/nas/content/live/gerotechdev/wp-content/themes/gerotech-child/
# then, over SSH:
wp page-cache flush --path=/nas/content/live/gerotechdev
wp cdn-cache flush  --path=/nas/content/live/gerotechdev
```
Verify with a checksum dry-run (`rsync -avnc --itemize-changes`) — expect zero drift.

**Still true:** unconverted Dev URLs look unfinished. Do not full-push Local DB (would overwrite Dev users/content and ship local `wp-config` workarounds).

---

## 15. If the client requires a page builder

**The build target is the client's choice, agreed at kickoff — and this process is independent of it.** Everything up to conversion is identical: the prototype (root `*.html` + `assets/images/`) and Figma (`YgHwqyyFj57c1ZSbmfkL0c`, home frame `7306:1063`) both hold the final pages, images and content, and remain the QA references (§1 of `qa-checklist.md`).

This plan describes the **native-editor route** because that is our default recommendation. If the client picks a builder instead, everything above still applies — only the conversion target changes.

| Route | Effort | Notes |
|---|---|---|
| **Native editor + ACF** *(our default)* | Lowest | What this plan delivers. Structured editing, no builder licence, no lock-in, no grid conflict. |
| **Elementor** | Moderate | **Accelerated by [UiChemy](https://uichemy.com/)** — a Figma→WordPress converter (Figma plugin + WP plugin) that exports designs to **Elementor, Gutenberg or Bricks**, including global styles. Turns much of the conversion from hand-building into an import plus cleanup. |
| **WP Bakery** | Highest | **Full development.** Every bespoke section must be hand-built as a custom element (24+ for this design system), on top of its grid conflict and AJAX re-init problems. Previously estimated at **46–70 developer days**, plus long-term lock-in. |

### What stays true whichever route is taken

- **The header and footer stay theme-level** — never built in the builder, regardless of choice.
- **The design system remains the source of truth.** A builder's stock components can't reproduce the bespoke sections (Haas watermark band, machine lineup tabs, editorial news split, stat counter, gallery collections), so those become custom elements either way.
- **The prototype remains the QA reference.** Compare the built page to the prototype, not to the builder's preview — see `qa-checklist.md` §1.
- **The two-sources-of-truth rule applies unchanged.** Final pages, images and content live in both the prototype and Figma.

### Recommendation

**The build target is the client's call, and it is settled at kickoff — never mid-project.** Offer the routes above with their costs so the decision is informed; don't turn it into a debate about whether the process works, because it works either way.

If a builder is chosen, push for **Elementor + UiChemy** over WP Bakery — the import path removes most of the hand-building, and the cost gap is large. Keep ACF fields for structured content in every case: they are what gives the client plain-language editing, and they survive a builder change.

