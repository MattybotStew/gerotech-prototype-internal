# LLM Instructions — Gerotech Prototype

This file is read by Claude Code and any other LLM assistants working in this repo.

## Shared Context (read every session)

Read `.clinerules` at the start of every session. It contains current project state, what was last worked on, open decisions, and things not to do. After any change, update `.clinerules` so all agents stay in sync.

## Homepage (current — Figma `7306:1063`)

Desktop home is Gerotech-Design canvas `6573:406` → frame **`7306:1063`**. Keep proto + WP homepage on this frame unless new Figma/client direction says otherwise.

- Peek hero **min-height 500px** at `min-width: 901px`, grows with the active slide's content (set in both `components.css` and `elevated.css`). Peek 02 `In-Stock & Ready`, peek 03 `Automation for Michigan`. Slide 2 **headline** stays “Our Showroom Machines Are Ready To Ship”.
- Haas Relationship: watermark + F1 lockup + intro **only** — no 4-column features band on this handoff. F1 brand image: `assets/images/haas-f1-team.jpg` (not the `.png`).
- Homepage CTA: `assets/images/cta-home-figma.jpg`, no phone lockup.
- Proto Training / Support / About have page hrefs. WP Support remains a service dropdown.

## WP Engine Dev (as of 2026-09-18)

- URL: https://gerotechdev.wpenginepowered.com/ — Gerotech Child **active**. Homepage, 4 ES pages, 7 legacy pages (ACF-wired), and Careers are live.
- Push from Local: **Dev only**, files **Select** → `wp-content/themes/gerotech-child/` only, **Database off**. Never `wp-admin` / `wp-includes` / `uploads`. Never Production.
- **Direct deploy (no GUI):** `rsync -avz --delete -e "ssh -i \"$HOME/Library/Application Support/Local/ssh/wpe-connect\"" wp-content/themes/gerotech-child/ gerotechdev@gerotechdev.ssh.wpengine.net:/nas/content/live/gerotechdev/wp-content/themes/gerotech-child/`, then normalise perms on the server (`ssh -i "$HOME/Library/Application Support/Local/ssh/wpe-connect" gerotechdev@gerotechdev.ssh.wpengine.net "find /nas/content/live/gerotechdev/wp-content/themes/gerotech-child -type d -exec chmod 755 {} +; find /nas/content/live/gerotechdev/wp-content/themes/gerotech-child -type f -exec chmod 644 {} +"`), then `wp page-cache flush` + `wp cdn-cache flush` (path `/nas/content/live/gerotechdev`). **The chmod is not optional:** a restrictive local umask ships files the web server can't read, and WP Engine answers **403**. Note macOS's `openrsync` has no `--chmod`, which is why it's a separate step; `sync-theme-assets.sh` and `sync-theme-to-local.sh` already normalise the local copies. See `handoff/implementation-plan-wordpress-theme-acf.md` §14.
- After a Pull from WPE, re-run `./scripts/sync-theme-to-local.sh`.
- reCAPTCHA site key `6LcOOuoaAAAAADRxx65d0pb_BvgWG9d9e5aqE9k6` does not include the Dev hostname yet (red “Invalid domain for site key” chip) — needs Google reCAPTCHA admin access.

## Project

CloudMellow (Matt's agency) is rebuilding the Gerotech website (Michigan CNC machinery distributor, Haas Factory Outlet, est. 1987). The repo holds **two things**: the **static HTML/CSS/JS prototype** (design source of truth) and the **WordPress child theme build** under `wp-content/themes/gerotech-child/`. The final site is WordPress.

**Branch:** `master` — synced with `origin/master`. Build phase: WordPress child theme + ACF, **no page builder**. Plan: `handoff/implementation-plan-wordpress-theme-acf.md`.

## Prototype → WP build sync

The prototype stays the **source of truth for shared assets** until design lock.

- **CSS / JS / images:** edit in the prototype `assets/`, then run `./scripts/sync-theme-assets.sh` (one-way copy into the theme). `--check` reports drift; `--prune` removes orphaned theme images.
- **Markup:** port prototype HTML changes by hand into the matching PHP template — map in `handoff/theme-map.md`.
- **Not synced:** `include-partials.js` (replaced by PHP includes) and `gallery-module.js` (not promoted).
- **At design lock:** tag the prototype, then the theme becomes the source and syncing stops.

**Repo theme → Local site:** the running LocalWP site has its own copy of the child theme. After editing the theme, run `./scripts/sync-theme-to-local.sh` (or `--check`) so changes appear at `gerotech.local`. A WP Engine "Pull" can overwrite the Local theme dir — re-run the sync afterward.

## ACF fields must be APPLIED, not just defined (standing rule)

Adding or changing an ACF field in `inc/acf-*.php` is **only half the job**. Registering a
field makes the front end render its template default — which means the site looks correct
while the editor shows an **empty control**. That is the failure mode to avoid, and it is
easy to miss because nothing looks broken.

**Whenever you add, rename or change an ACF field:**

1. Add the field definition **and** the template default. Defaults live in the *template*,
   never in `default_value` — ACF injects `default_value` on read, so a plain save persists
   it (see `.clinerules` for the full reasoning).
2. Add the field to `scripts/seed-acf-content.php` if it should hold content, using the
   **field key**, not the field name.
3. Sync, seed, then audit — in that order.
4. Deploy the theme to Dev and run the seeder there too. **A theme push does not apply
   fields** — Dev and Local each store their own values.
5. Confirm with `scripts/audit-acf-applied.php` before calling it done.

```bash
# Local
./scripts/sync-theme-to-local.sh
wp eval-file scripts/seed-acf-content.php     # idempotent; only fills blanks
wp eval-file scripts/audit-acf-applied.php    # what is still unapplied, and why
```

**Some blanks are correct** — do not "fix" these:

- **`*_hero_accent_color` / `*_hero_cta_color` selects** — blank means "keep the design
  colour". Seeding one freezes the design.
- **`es_show_news`** — blank/false = section hidden, which is the intended state.
- **`es_partners_logos`** — the wordmark fallback *is* the design.
- **`haas_brand_logo`, `cta_image`** — theme-bundled image defaults.
- **`mcs_cta_body`, `app_cta_body`** — the template default is deliberately empty.

The audit prints these categories; read its output rather than only the counts.

**Gotchas the audit has already caught:**

- **Never compare bare slugs on a hierarchical site.** `rotary-repair` (#1484, at
  `/rotary-repair/`) and `service/rotary-repair` (#194, at `/service/rotary-repair/`)
  are **two different pages, both live and both 200**. Comparing slugs made #194 look
  like an unreachable duplicate of #1484 and nearly got a live URL trashed. Compare
  full paths with `get_page_uri()`.
- **Locate field groups by `page_template`, not `post_name`, when a slug is not
  unique.** `group_rotary_content` matched `post_name == 'rotary-repair'` and so
  appeared on `/service/rotary-repair/` — a page running `page-service.php` that never
  reads those nine fields. Editors saw controls that did nothing.
- Passing a field **name** where `update_field()` expects a **key** silently writes junk
  meta (e.g. `field_cta_call_label`) that nothing reads, and the audit still reports the
  field as blank. **If a seed reports success but the audit disagrees, check the key.**

**The 2017-era Service sub-pages are LIVE, not leftovers.** `submit-a-request`,
`parts-order`, `rotary-repair`, `preventive-maintenance-plan` and `application-support`
all still exist as children of Service (#20) and resolve under `/service/…`. They run
`page-service.php`. Do not delete them without checking with the client.

## Site Content editor — header / menus / footer (plan as of 2026-09-29)

**Goal:** the client edits the global chrome without a developer and without being able to
break it. Rendering is finished and must not change in this work; only the editing surface.

**Done (commit `7277717`, live on Local + Dev):**

1. **Four screens**, not one page: Site Content → Header / Menus / Footer / Shared Content.
   `gerotech_site_content_screens()` in `inc/helpers.php` is the single list; attach groups
   with `gerotech_options_location( $slug )`. Data is keyed by field name under `'option'`, so
   moving a group between screens moves no data.
2. **Layout-only fields are hidden**, not removed: wrapper class `gerotech-advanced` +
   CSS in `inc/admin.php`. Reveal with **`&advanced=1`** on any Site Content URL.
   *Never delete a repeater sub-field to hide it* — ACF skips a missing sub-field on save and
   its stored value stays at the OLD row index, so a reorder scrambles the layout. Hidden but
   posted travels with its row. Table-layout repeaters also need the `<th>` hidden;
   `gerotech_advanced_field_keys()` emits those rules from the field definitions.
3. **One control per link.** No `new_tab` toggles. Any URL on another host opens in a new
   tab automatically (`gerotech_is_external_url()`; `gerotech.com` counts as this site on
   every environment). The Link field's own target still forces it for internal URLs.
4. **Menu `style` blank = automatic** (sub-links ⇒ drop-down, else plain). Mega styles are
   explicit and advanced; the drop-down table is hidden on mega items by conditional logic.
5. **Socials** use a `network` picker → inline SVG (`gerotech_social_icon_svg()`). Glyph text
   is a fallback for old rows.
6. **2017 "Site Options" menu removed** (`gerotech_hide_legacy_site_options` filter restores).
   Old `page=gerotech-site-content` URL redirects to Header — hooked on `admin_menu`, because
   `menu.php` dies before `admin_init`.

**Rules for anyone touching these screens:**

- Labels and instructions are written for the client: name the thing on the page ("the black
  phone bar"), not the field. Keep it that way.
- New layout/design-only field ⇒ give it `gerotech_acf_advanced_wrapper()`.
- New link list ⇒ use `gerotech_acf_link_pair()`; do not add a new-tab toggle.
- New options-page field name ⇒ diff against the 371 legacy names first (see `footer_copyright`).
- After any change: `sync-theme-to-local.sh` → seed → `migrate-global-links.php` if links
  changed shape → audit → rsync Dev `-avz --delete` + chmod + flush → seed/migrate/audit on
  Dev. Verify a save round-trip: click Update with fields hidden, diff the rendered
  header+footer, expect 0 lines.
- Verify ACF read-backs in scripts after `acf_get_store( 'values' )->reset()`; `wp_cache_flush()`
  alone returns stale rows.

**Not done — needs a client or design decision before anyone starts:**

7. Auto-derive machine-panel columns from group order (drops `column` + `mobile_order`, but the
   phone menu would then follow desktop order: Rotaries before Horizontal).
8. Merge the two-part ES titles (`heading_lead` / `heading_main`) into one field with an accent
   convention like the hero `<em>`.
9. Phone-menu machine group titles render `href="#"` (`site-mobile-nav.php`) — decide where
   they should go.
10. `header_search_hint` on Local and Dev still says "Prototype site search — browse by
    section:" — client-facing copy; stored value, so fix in both DBs, not just the default.
11. Client edits on Dev live only in the Dev DB. Before launch: agree how values move to
    Production (seeder + migrate scripts, or a content export).

Spec: `handoff/acf-spec.md` §1. Journal entry: 2026-09-29 "Site Content editor rebuilt".

## Stack

- Pure HTML5 / CSS3 (custom properties) / vanilla JS (ES6)
- Zero frameworks, no build tools, no package manager
- Fonts: **Barlow Condensed** (Google Fonts, 500/600/700) for headlines (`--font-display`) + **Navigo** (Adobe Fonts kit `lqh7ybe`, 400 + 700) for body/UI (`--font-sans`)
- **CSS load order:** `tokens.css` → `components.css` → `layout.css` → **`elevated.css`**
- **JS:** `include-partials.js`, `nav.js` (sticky, mobile, search modal, signup thanks), `slider.js`, `filter.js`, `animations.js`, `machine-tabs.js`, `modal.js` (ES detail card modals + gallery lightbox)

## Key conventions

- All images are Unsplash stand-ins (HTML comment on each) — verify URLs periodically; some IDs 404 over time
- CSS class naming: BEM (e.g. `.service-card__category`, `.slide__content--left`)
- Phone links: E.164 format (`tel:+17343797788`)
- Never guess at open client decisions — use placeholder + HTML comment
- Buttons: `text-transform: uppercase` + 0.05em letter-spacing
- Card border-radius: **0** (squared edges per Figma)
- **Hero pattern (homepage + all interior pages):** full-bleed photo, left gradient overlay (`105deg`), left-aligned copy; centers on mobile ≤768px
- **Interior hero:** All sub-pages use **`.page-hero`** — same markup as homepage hero slide (`slide__bg`, `slide__overlay--left`, `slide__content--left`). Per-page photo via `<img src>` in HTML.
- **CTA bands:** `.cta-band--photo` — same left-aligned photo treatment as hero
- **CTA lockup (site-wide):** `.cta-band--cinema-lockup` — copy-left + optional call card; Careers / Support / Training omit the call card
- See `design-spec.md` and `cline-project-handoff.md` for full project context

## Design directions (2026-08-17)

Homepage and shared components use an editorial, numbered-row system with inverted light bands and photo-led cards. **Do not revert** these patterns without explicit client direction.

### Accent + section headers
- Orange accent words in headlines: `.accent` on dark surfaces, `.accent--deep` on light
- Short orange rule under titles: `.headline-rule` / `.headline-rule--deep` (48px bar — same motif on testimonial card tops)
- Eyebrows: uppercase Navigo, `--ls-meta` tracking; deep orange on light sections
- **Applied site-wide:** the section-header accent/rule system (accent word + headline-rule under `section-title`) now runs on Homepage + all 8 interior pages (ES, MCS, Automation, Application, Training, Support, About, Careers). Keep new section titles on the pattern. `.headline-rule--deep` on light (white/gray) sections.

### Haas Relationship (Figma `7080:1405` intro · `7080:2240` features — CSS still in repo)
- Section: `.haas-relationship` — eyebrow row, intro copy, optional features band
- **Current homepage (`7306:1063`):** watermark + F1 lockup + intro only. **Do not put the 4-column features band back on home** unless Figma/client restores it.
- **Watermark:** `assets/images/haas-wordmark-watermark.svg` at ~5% opacity inside `.haas-relationship__bg`; visible copy in `.haas-relationship__content` with `isolation: isolate` + z-index so intro/brand sit **above** the watermark. Watermark is **static** (`position: absolute`) behind intro only. Section uses `overflow-x: clip`. No parallax.
- **Features band (CSS, not on current home):** `.haas-relationship__features` — inverted light treatment if a later page/frame needs it. Do not delete the CSS speculatively.

### Latest Projects & News (homepage + ES editorial)
- Class: `.news-section--editorial` → `.news-editorial` grid (`1.08fr / 1fr`)
- **Lead story:** `.news-feature` — full-bleed photo, 105° left gradient + bottom scrim (matches hero/CTA), orange `.news-tag`, Barlow headline, 3-up stat row on hairline rule
- **Secondary rows:** `.news-list` → numbered `.news-item` (index `02`–`04`), `.news-tag--light` chips, 132×96 thumb; hover tints row and turns index/title deep orange
- **Scope (2026-08-17 session):** editorial split live on Homepage **and** Engineered Solutions (ES converted from legacy `.news-card`). Legacy `.news-card` styles remain in CSS for showroom/preview pages — do not remove.
- Story links omitted until dedicated news page (client TBD) — no dead `href="#"`

### Testimonials (shared partial)
- Partial: `partials/testimonials-block.html` — Homepage, Engineered Solutions, **and all interior pages** (MCS, Automation, Application, Training, Support, About, Careers) via `<div data-include>` placed before each CTA band
- Layout: `.testimonial-grid` (3 → 2+1 → 1 col in `layout.css`), **not** the old split-photo carousel
- Card: `.testimonial-card` — orange top rule (widens on hover), oversized serif closing quote in `--clr-orange-tint` bottom-right, hairline divider above attribution, Barlow Condensed name (20px), uppercase micro role label (11px, `--ls-meta`). Hover: 4px lift + orange-tinted border. `prefers-reduced-motion` disables lift.
- Scroll reveal: `.testimonial-card` in `animations.js` targets

### Typography
- **Display:** Barlow Condensed (`--font-display`) — section titles, card titles, stat numerals, testimonial names, news index numerals
- **Body/UI:** Navigo (`--font-sans`) — paragraphs, labels, buttons
- Buttons: uppercase + 0.05em letter-spacing

### Photography + motion
- Photo cards: left gradient overlay at 105°, bottom scrim where needed
- Card image zoom on hover; disabled under `prefers-reduced-motion`
- `elevated.css` photography cohesion includes `.news-feature__bg`, `.news-item__thumb` with hero/CTA images

### Feature icon tiles (site-wide)
- **Pattern:** 40×40 `--clr-gray-card` tile, 4px radius, 18px icon — matches Haas features band (`7080:2240`)
- **Classes:** `.haas-relationship__icon` (img + `filter: brightness(0)`), `.category-card__icon`, `.why-feature__icon` (inline SVG, `stroke: var(--clr-ink)`)
- Do not use orange-tint icon backgrounds or rounded-10/12px tiles on interior pages

### Figma reference nodes (Gerotech-Design `YgHwqyyFj57c1ZSbmfkL0c`)
| Area | Node |
|------|------|
| Haas Relationship intro + watermark | `7080:1405` |
| Haas features band (inverted) | `7080:2240` |
| Homepage wireframe | `6218:10` |
| ES wireframe | `6217:425` |

## Pages (11 HTML)

| Page | File |
|------|------|
| Homepage | `index.html` |
| Engineered Solutions | `engineered-solutions.html` |
| Machine Custom Solutions | `machine-custom-solutions.html` |
| Automation & Controls | `automation-integration.html` |
| Applications | `application.html` |
| Training | `training.html` |
| Support | `support.html` |
| About | `about.html` |
| Careers | `careers.html` |
| Machine Modification | `machine-modification.html` (redirect → MCS) |
| Showroom | `showroom.html` (exploratory) |

Shared partials: `partials/site-header.html`, `partials/site-footer.html`, `partials/testimonials-block.html`

## Session continuity

This project is worked on in **Cursor** and **VS Code (Cline)** — plus other agents as needed.

### Sync workflow (all editors)

| When | Action |
|------|--------|
| **Session start** | Read `.clinerules`, then `JOURNAL.md` (newest first), then `git log -5` |
| **Session end** | Prepend to `JOURNAL.md`; update **Current Session State** in `.clinerules` |
| **Handoff code** | Commit + push so the other editor pulls the same branch |

Shared config (committed in repo):

- `.clinerules` — live state (Cline reads this automatically)
- `AGENTS.md` / `CLAUDE.md` — LLM instructions (Cursor + Claude Code)
- `.cursor/rules/gerotech-agent-sync.mdc` — Cursor always-on sync rule
- `.vscode/tasks.json` — **Serve Gerotech (8080)** dev server
- `.vscode/mcp.json` — Figma MCP (remote) for VS Code Copilot Agent

### Local preview

From repo root: `python3 -m http.server 8080` → http://localhost:8080/

**Note:** Server can hang after long sessions — if `ERR_EMPTY_RESPONSE` or `ERR_CONNECTION_REFUSED`, kill port 8080 and restart.

In VS Code: **Terminal → Run Task → Serve Gerotech (8080)**.

### Figma → agent workflow (global remote MCP)

Figma is used via the **remote** MCP at `https://mcp.figma.com/mcp` — works in **any project**, no Figma desktop app or local `:3845` server required.

| Client | Config |
|--------|--------|
| **Cursor** | Figma plugin (`/add-plugin figma`) + global `~/.cursor/mcp.json` |
| **VS Code Copilot** | `.vscode/mcp.json` (this repo) or user-level MCP |
| **Cline** | Add remote HTTP server `https://mcp.figma.com/mcp` in Cline MCP settings |

**Workflow (any project):**
1. Open the design at [figma.com](https://www.figma.com) (FigmaAgent supplies local fonts if installed)
2. Copy a frame/layer link (`figma.com/design/:fileKey/...?node-id=...`)
3. In **Agent mode**, paste the link and ask to implement using this project's HTML/BEM/tokens
4. Prefer skill **`figma-design-to-code`** before `get_design_context`
5. First use: authenticate Figma MCP (**Settings → MCP → Figma → Connect**) if tools show `needsAuth`

**Gerotech file keys (reference):**
- File: `YgHwqyyFj57c1ZSbmfkL0c` (Gerotech-Design)
- Homepage handoff (desktop home): canvas `6573:406` → frame **`7306:1063`**
- Haas Relationship intro: `7080:1405` · Haas features band: `7080:2240` (not on current home frame)
- ES wireframe node: `6217:425` · Homepage wireframe: `6218:10`

**Note:** Local Dev Mode MCP (`http://127.0.0.1:3845/mcp`) only works with Figma **desktop** + Dev Mode MCP enabled. This machine uses **web Figma + remote MCP** instead — do not depend on `:3845`.

@FIGMA.md
