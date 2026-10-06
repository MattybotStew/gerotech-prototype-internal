# Project journal — gerotech-prototype

Shared session log for all AI agents. Newest entries at the top.

## 2026-10-06 — Mobile ES menu links the main subpages (opencode)

The phone menu's Engineered Solutions group only listed the col-2 "All Services" anchor links; the desktop "By Category" pages had no mobile link, and the group heading (`<summary>`) isn't clickable, so the ES landing page wasn't reachable from the group either.

- **Overview** link → ES landing page, then each service-group heading is now a link to its category page (Machine Custom Solutions / Applications / Automation Controls Solutions); service anchor links unchanged.
- **Prototype** `partials/site-header.html` + `assets/css/components.css` (new `.mobile-nav__sublabel--link` re-enables `pointer-events` and adds a hover underline — the base `.mobile-nav__sublabel` disables pointer events).
- **Theme** `template-parts/site-mobile-nav.php` `es-mega` branch pairs each group with `gerotech_es_panel()['categories']` by heading (new `gerotech_heading_key()` in `inc/global-content.php`; positional fallback), rendering the label as a link when a URL resolves.
- **No ACF fields** touched. Verified Local + Dev (4 category pages 200; Dev HTML shows Overview + 3 links). Deployed Dev via rsync + chmod + page/CDN flush.

## 2026-10-06 — Phone card on every cinema CTA (Cursor)

Bottom callouts on Machine Custom Solutions, Applications, Automation, and Careers were missing the phone lockup. Defaults are now `(734) 379-7788` (label “Prefer to talk it through?”, note “Talk to a person, not a form.”), matching Home and Engineered Solutions. Prototype HTML updated too. Theme synced to Local and verified on those six pages. Not deployed.

## 2026-10-06 — Process Troubleshooting card uses the in-cut photo (Cursor)

Applications **Process Troubleshooting** thumbnail is now `assets/images/app-troubleshooting.jpg` (coolant and a tool in the cut). Prototype `application.html` and the template default in `page-unique-applications-for-standard-machines.php`. Gallery collection still uses `app-troubleshooting-cabinet.jpg`. Theme image copied. Not deployed. A stored ACF `app_cards` image still wins over the template default.

## 2026-10-06 — Process Engineering card uses the client process diagram (Cursor)

The MCS **Process Engineering** service-card thumbnail was the last Unsplash stand-in on that grid. Replaced with `assets/images/process-engineering.jpg` (circular Gerotech Engineering process wheel) in `machine-custom-solutions.html` and the template default in `page-modification-of-standard-machine-tools.php`. Theme image copied via `sync-theme-assets.sh`. Gallery collection still uses `mcs-gallery/custom-fixtures.jpg`. Not seeded (image is a template default, not stored content) and not deployed. Local WordPress will keep a previously saved ACF card image if one is already stored.

## 2026-10-06 — Why Gerotech stroke sits closer to the headline (Cursor)

On Engineered Solutions, the orange rule under “Why Manufacturers Trust Gerotech” was flush with the paragraph (the headline’s 20px bottom margin collapsed into the space *above* the stroke). Scoped to `.why-section` only: headline bottom margin `0`, rule `margin-top: 12px` / `margin-bottom: 24px`. Measured on the prototype: 12px from headline to stroke, 24px from stroke to body. Theme `layout.css` synced via `sync-theme-assets.sh`. Other pages’ `.headline-rule` unchanged. Not deployed.

## 2026-10-06 — Every external link opens in a new tab (opencode)

The header/nav/footer already auto-targeted external links through `gerotech_link_row()`, but links rendered directly by page templates (hero and CTA buttons, course flyers, job postings) and inside legacy post content did not.

- **New `inc/external-links.php`** — one `template_redirect` (priority 20) output-buffer pass over the finished front-end document. `gerotech_external_links_filter()` walks anchors with `WP_HTML_Tag_Processor`, and for any `gerotech_is_external_url()` match sets `target="_blank"` and merges `noopener noreferrer` into `rel` (existing tokens preserved). `mailto:`/`tel:`/anchors/relative and the Gerotech hosts are left alone; anchors inside `<script>` are skipped. A regex fallback covers pre-WP 6.2. Edits nothing in admin/feeds/REST/embeds. Required in `functions.php`.
- **Prototype:** already compliant — an HTML-parser audit of every root/partial/case-study page found 0 external anchors missing `target`. No HTML edits.
- **Verification:** unit-tested the filter against WP 7.0.5's real `WP_HTML_Tag_Processor` (internal/`nofollow`/`_self`/`script` cases). Live Local: homepage hero CTA (`front-page.php:260`, template has no target) renders `rel="noopener noreferrer" target="_blank"`; 328 external anchors across 9 pages, 0 missing, 0 internal over-applied.
- **Deployed to Dev:** rsync `-avz --delete` + remote chmod + page/CDN flush. Dev: 188 external anchors across 4 pages, 0 missing, 0 self over-applied.

## 2026-09-30 — ES mega-menu anchors Dev deploy recovered (Cursor)

Shell 316338 failed after theme rsync: **scp** hit WPE `subsystem request failed on channel 0`, so `wp eval-file` never ran; inline `wp eval` with parentheses also broke bash quoting.

**Fix:** re-rsync theme `-avz --delete` + chmod; upload `scripts/update-es-service-anchors.php` via **rsync over ssh** to `/nas/content/live/gerotechdev/_gerotech-scripts/`; `wp eval-file` that path (not inline eval). Result: **0 updated, 15 already correct** (ACF already hashed — likely from an earlier Local/Dev seed pass). Page + CDN caches flushed.

**Verified on Dev** (PHP panel + homepage HTML): Auto Doors → `/modification-of-standard-machine-tools/#auto-doors`; Specialty Machine → `#specialty-machine`; Part Programming → `/unique-applications-for-standard-machines/#part-programming`; HMI Design → `/automated-system/#hmi-design`; Electrical – Controls Solutions → `#layered-controls-solutions`.

## 2026-09-30 — ES mega-menu scroll-to-card anchors (Cursor)

Engineered Solutions **All Services** sublinks (mega + mobile in proto) now land on the matching `.mcs-card` via `#id` from `gerotech_card_id()` (WP templates) and hand-set ids in proto HTML. Defaults in `gerotech_es_defaults()`; `scripts/update-es-service-anchors.php` pushes into stored `nav_es_services`. `.mcs-card { scroll-margin-top: 120px; }` for sticky header. Local + Dev theme deployed; anchor script verified 15/15 on Local.

## 2026-09-30 — Peek 03 title → On Our Floor (Cursor)

Homepage peek **03** only: `Automation for Michigan` → **On Our Floor**. Peek **01** stays **A Division of Gerotech**; peek **02** unchanged (**In-Stock & Ready**).

- Proto `index.html` slide 3 `data-peek-title`; theme `front-page.php` row-2 default; docs `AGENTS.md` / `design-spec.md`.
- Local: theme synced; stored ACF `home_hero_slides[2].peek_title` updated (row 0 untouched).
- Dev: rsync `-avz --delete` + chmod; ACF row 2 via `wp eval-file` (inline `wp eval` shell-escaping failed earlier — exit 2); page + CDN caches flushed.

## 2026-09-30 — Haas Automation homepage tab photo (Cursor)

Replaced the Machine Lineup **Haas Automation** panel photo with the new client UMC-750 + yellow robot cell shot (was a different yellow-robot/safety-fence cell).

- Proto + theme asset: `assets/images/lineup-haas-automation.jpg` (1024×682, 156 KB) — same path, new pixels.
- `scripts/set-lineup-panel-photo.php` gained an optional third arg `force` so a same-basename replace re-imports instead of no-opping.
- Synced to Local theme dir. **Local WP was stopped** (no mysqld sock), so ACF on Local was not re-pointed — start Local and run:  
  `wp eval-file scripts/set-lineup-panel-photo.php "Haas Automation" assets/images/lineup-haas-automation.jpg force`
- Dev: theme rsync + chmod; forced import → attachment **#3509** (`lineup-haas-automation-1.jpg`); page + CDN caches flushed. Front panel serves the new shot.

Proto `index.html` already pointed at this filename; alt still fits. Also committing the same-day `gerotech_image_alt()` + `gerotech_tel_link()` template wiring (Media Library alts; CTA call `href` from the phone field) that was already live on Dev from the theme rsync.

## 2026-09-30 — Local admin access, first-login gates, and an audit recount (opencode)

No rendering changes and no ACF field changes. This was local access plumbing plus a correction to two numbers that had been recorded wrong.

**Local-only admin `matt.local`.** Added user ID 28 (`matt@local.test`, display `Matt (Local)`) on the **Local database only** so wp-admin could be driven without touching a client admin account. The 8 client admins are unchanged; Local now has 9. `.test` is RFC 2606 reserved, so a password-reset mail can never deliver anywhere. **A user row is data, not code — it cannot ride a theme rsync. Never create this account on Dev or Production.** The password is not written down in this repo (it was removed from `.clinerules`); reset it with `wp user update` against Local if it is ever needed.

**Three first-login gates, all Local-only, all worked around:**
1. **Better WP Security 10.0.4** shows a one-time "Setup Two-Factor" on-boarding screen (`#itsec-2fa-on-board`) on first login, with Skip / Continue. 2FA itself is *off* (`itsec_enable_2fa` = false, no per-user meta) — this is a prompt, not enforcement.
2. The same plugin enforces a **strong-password policy at that step** and rejects anything not rating "Strong" ("Due to site rules, a strong password is required"). So `wp_check_password()` passing does **not** mean the login flow completes — test the actual flow.
3. WP core's **`confirm_admin_email`** gate then blocks the dashboard, and it cannot be satisfied by email here. Fix: set `admin_email_lifespan` to a **future** timestamp (`time() + 6 * MONTH_IN_SECONDS`, which is what `wp-login.php` itself writes). **`0` does not suppress it** — the redirect fires when `time() > $admin_email_lifespan` (`wp-login.php:1391-1400`), so zero guarantees the redirect. Read the condition before guessing at this one.

**Audit recount.** `.clinerules` said Local 311/326 and Dev 310/326. Those totals could not be reproduced. Re-measured by dumping every target's field-name list on both environments: the lists are **byte-identical**, and the real total is **325** (Local 310/325, Dev 309/325) — the summary line prints "32 targets" because it counts the internal `__dupes__` key, which is cosmetic. `inc/acf-global-fields.php` is unchanged and clean in git, so this is a bad count rather than a regression. **All 15 Local blanks were re-checked against the documented correct-by-design list and all 15 are on it** (filtering for anything outside that list returns empty). Treat 325 as the real total; don't chase the 326 as a lost field.

**Also committed:** `scripts/set-lineup-panel-photo.php`, `scripts/_dev-update-batch.sh`, `scripts/_local-acf-apply-once.sh`; `Untitled/` (a stray `git init` with no commits) added to `.gitignore`.

**Sync verified clean before committing:** origin/master ↔ repo 0/0, `sync-theme-assets.sh --check` OK, `sync-theme-to-local.sh --check` OK, and an rsync dry-run against gerotechdev reported 163 files with zero differences and nothing to delete. Repo, Local and Dev all carry identical theme code. (Dev still carries the known Local↔Dev *content* drift, which lives in the DB, not the theme.)

## 2026-09-29 — Site Content editor rebuilt for the client (Cursor)

Review found the header/menus/footer ACF screens were correct but built for the developer: one page with a dozen tabs, a `Style` select that could kill the whole menu, `Column 1–4` and `Mobile order` on every machine group, three ways to say one link, typed social glyphs, and a second look-alike "Site Options" menu from 2017. Nothing rendered changed in this pass — only how it is edited.

**What changed**
- **Four screens instead of one page.** Site Content → Header / Menus / Footer / Shared Content (`acf_add_options_sub_page`). `gerotech_site_content_screens()` in `inc/helpers.php` is the one list; groups attach with `gerotech_options_location()`. Values are keyed by field name under `'option'`, so no data moved. `page=gerotech-site-content` redirects to Header.
- **Layout fields hidden.** `gerotech-advanced` wrapper class + CSS in new `inc/admin.php`; `&advanced=1` reveals them. Hidden with CSS, not removed: a removed repeater sub-field is skipped on save and its value stays at the old row index, so a reorder would scramble the layout. Table-layout repeaters also need the `<th>` hidden — `gerotech_advanced_field_keys()` walks the groups and emits those rules.
- **One control per link.** All `new_tab` toggles removed. `gerotech_link_row()` now opens any external host in a new tab automatically (`gerotech_is_external_url()`; `gerotech.com` treated as internal everywhere). Old stored flags still read; `scripts/migrate-global-links.php` folded them into link targets on Local + Dev.
- **Menu style automatic** when blank (sub-links ⇒ drop-down). Drop-down repeater hidden by conditional logic on the two mega items.
- **Social network picker** → inline SVG (`gerotech_social_icon_svg()`); glyph is a fallback. Migration set `network` on the three existing rows.
- **Legacy "Site Options" menu removed** (`remove_menu_page('acf-options')`, filterable). Notice at the top of every screen: blank keeps current wording; saves are live everywhere immediately.
- Labels rewritten in page language ("the black phone bar", "Orange button — goes to").

**Verification**
- Local admin checked in a browser with a temp admin (deleted). Clicked **Update** on Menus with all layout fields hidden → **0-line diff** in rendered header+footer; option-row diff was only ACF re-serialising link arrays.
- Front end Local + Dev: 41 machine links all `target=_blank`, Support drop-down, 3 SVG socials, copyright line, 0 PHP notices, interior URLs 200. Audit Local 310/325, Dev 309/325 (only logo blanks; one field fewer than before because `nav_machines_footer_new_tab` is gone).
- Traps hit: `admin_init` is too late to redirect a removed menu slug (menu.php dies first — use `admin_menu`); ACF's `values` store must be `reset()` before a same-request read-back, `wp_cache_flush()` alone is not enough.

**Left for a client/design call:** auto-deriving machine-panel columns (drops two fields but changes the phone order), merging the two-part ES titles, phone-menu group titles still `#`. Also: `header_search_hint` still says “Prototype site search — browse by section:” on both environments.

## 2026-09-29 — Machine sublinks pointed at the client's live catalogue (opencode)

All 41 Machines mega-panel sublinks were seeded as `#` placeholders. They now use the URLs from **https://gerotech.com/machines/**, all `target="_blank" rel="noopener noreferrer"`.

**Where the URLs come from:** gerotech.com/machines/ has no pages of its own — the eight category headings and every model link resolve to `haascnc.com` (that is what the client's own live nav does). Scraped and mapped label → slug so nothing was guessed: `vertical-mills/vf-series.html`, `lathes/st.html`, `rotaries-indexers/rotary-tables.html`, `horizontal-mills/ec-series.html` (the 50-Taper), `automation-systems/haas_apls.html`, `desktop-machines/simulator-std.html`, `shop-equipment/knee-mills.html`, `fab-machines/press-brakes.html`, etc. All 41 labels mapped 1:1. Spot-checked four in a browser — all live and correctly titled. Group titles stay plain text; only the sublinks are links.

The map lives in `gerotech_machines_defaults()` (`inc/global-content.php`), not in the script, so there is still one copy of it. `scripts/update-machine-links.php` just pushes those defaults into the stored ACF rows — it never invents a URL, and reports labels it does not recognise instead of guessing. Idempotent; run on Local and Dev.

**Two ACF traps hit here, both silent:**
1. **Options-page repeaters store under the field NAME, not the key.** The real option is `options_nav_machines_groups_0_links_0_url`. Writing `update_field( 'field_nav_machines_groups_0_links_0_url', …, 'option' )` creates an unread `options_field_nav_machines_groups_…` row — 82 junk rows, all "successful" writes, zero visible change. This is the same class of bug as the `field_cta_call_label` note in the standing rules, just at a nested path. Junk rows deleted; the script now uses the name path.
2. **`url` is a Link field and stores an array** — `array( 'url' => …, 'title' => '', 'target' => '' )`. A bare string is silently discarded and the field reads back empty. And a same-request `get_field()` returns the *pre-update* rows because ACF memoises the repeater, so the first run's self-check reported "41 still blank" after a successful write. The script now flushes before verifying, and the second run confirms 41/41.

**Deploy slip worth remembering:** `rsync -q src/ dst/` does **not** recurse — without `-a` it silently skips `inc/` and `template-parts/`, so `global-content.php` never landed and Dev re-seeded the old `#` values. The AGENTS.md recipe (`-avz --delete`) is the one to use; verify a changed file actually arrived with a remote `grep` before running a seeder against it.

Verified: 41/41 sublinks render real URLs with `target="_blank"` on Local and Dev, 0 blank, 41 new-tab; clicking one opens a new tab on the Haas page. Audits unchanged (Local 311/326, Dev 310/326).

## 2026-09-29 — Global header / nav / footer made ACF-editable (opencode)

The last hardcoded part of the site is now editable. Banner, logo/alt, header CTA, search modal, the full desktop menu tree, both mega panels, the phone menu, and the footer all come from the **Site Content** ACF Pro options page.

**New files**
- `inc/acf-global-fields.php` — `group_site_header`, `group_site_navigation`, `group_site_footer` (PHP-registered).
- `inc/global-content.php` — all defaults + normalisers. `gerotech_link_row()` collapses `title`/`label` and `target`/`new_tab`; `gerotech_link_attr()` normalises external + `mailto:`/`tel:`; `gerotech_image_url()` falls back to the theme-bundled SVG when a logo field is blank.
- `template-parts/site-nav.php`, `site-mobile-nav.php`, `search-modal.php`.

**Rewritten:** `header.php`, `footer.php`; `functions.php` loads the two includes. No CSS or JS changes — the same BEM classes and hooks, so `nav.js` / `styles.css` were untouched.

**Defaults live in code, not ACF.** `default_value` is used nowhere in the new groups (the standing rule). Blank value = current design. A blank `header_cta_label` hides the button.

**Mobile menu is derived, not a second copy.** `site-mobile-nav.php` walks `nav_items` + both panels, so the two menus can't drift. Where the design genuinely differs, the difference is a field: `mobile_order` on machine groups, `nav_machines_footer_mobile_label`, `nav_es_cta_mobile_label`, and a per-link `mobile_label` on ES service links. This is what preserves the base's quirks (desktop shows Rotaries before Horizontal; the phone shows Horizontal first; "Machine Column Risers" vs "Column Risers").

**Collision caught:** the new field would have been `footer_copyright`, which the 2017 legacy group `group_59305cb95c705` ("Site Options") already owns — and ACF resolves option values by *name*, so it would have silently served the legacy value. Renamed to `footer_copyright_text` / `field_footer_copyright_text`. A name diff against the 371 legacy fields found this one collision in 32 new names. **Do that diff before adding any options-page field.**

Also removed two dead `default_value`s on the pre-existing `field_nav_item_style` (select) and `field_nav_machines_group_column` (number) — same root cause as the standing rule. Added `field_footer_logo_alt`, and top-level `new_tab` handling for menu items and the Machines catalog link.

**Verification**
- `php -l` on all changed files.
- **Parity:** captured the old header/footer from Local *before* syncing, then diffed rendered markup. Only three deltas, all intentional: a consistent desktop Machines column wrapper, `aria-haspopup="true"` on the ES trigger, footer `Contact` → `/contact/` (was a placeholder `/about/`).
- **Defaults vs seeded:** zero meaningful differences, so a blank field still renders the design.
- **Editor round-trip:** set values, clear them, confirm the defaults return, restore — no residue.
- **Audit:** Local **311/326**, Dev **310/326** across 32 targets; every blank is a documented correct-by-design one.
- **Browser:** 1440×900 and 390×844 — 4-column machines grid, 8 groups, mobile order/labels, `.mobile-toggle`, `.header-search` and the modal all work; no console errors. Admin options page checked with a temporary user (deleted; Local back to 8 admins).
- **Dev:** rsync + chmod + page/CDN flush, seeder and audit re-run there. Home + 12 interior URLs 200; Dev header/footer markup token-identical to Local once host names are normalised.

**Deploy note:** `scp` and `rsync` to `/tmp` and `$HOME` silently land nowhere on WP Engine. Copy eval scripts to `/nas/content/live/gerotechdev/wp-content/` and `rm` them afterwards, or run the one-shot seeder from the existing dev deploy script.

Docs: `handoff/acf-spec.md` §1 rewritten (real field names, caps, the collision warning); `handoff/component-inventory.md` chrome rows now list the fields instead of "hardcoded v1".

## 2026-09-29 — Session wrap: client photos + Dev/Local ACF (Cursor)

MCS, Applications, Automation galleries/hero/CTA/workholding; Cell Design + EOAT expansions; home lineup + tooling + Pre-Engineered (`5ef0b4c`); **On Our Floor** eyebrows. Dev rsync + ACF; Local matched via `_local-acf-apply-once.sh` + lineup script. Matt confirmed all good. Uncommitted: `set-lineup-panel-photo.php`, deploy helper shell scripts, `.clinerules` touch.

## 2026-09-29 — Local ACF apply (Cursor)

Theme synced to Local; full media pass via `scripts/_local-acf-apply-once.sh` (WP-CLI: `-d mysql.default_socket=…/VjZ_PwL-d/mysql/mysqld.sock`). Workholding #3491, MCS CTA #3492, Demo 3-up, EOAT 3-up, Cell Design 4-up, eyebrows **On Our Floor**. Hero already #3455.

## 2026-09-29 — Client image batch: home lineup, Applications tooling, Automation Pre-Engineered cover (Cursor)

**Homepage machine lineup tabs (proto + theme defaults):**
- `lineup-haas-automation.jpg` — Haas UMC-750 + yellow robot cell (`index.html`, `front-page.php` Rotaries/Automation panel photos).
- `lineup-rotaries-indexers.jpg` — three rotaries/indexers on showroom floor.

**Applications — Tooling Recommendation:** replaced `app-tooling-cart.jpg` (card + gallery); alt text updated in proto + `page-unique-applications-for-standard-machines.php` gallery default.

**Automation — Pre-Engineered Solutions:** gallery cover now `pre-engineered-card.jpg` (matches service card); lightbox adds `pre-engineered-gallery.jpg` as second image (`automation-integration.html`, `page-automated-system.php` `ai_collections` default).

**Loose ends:** stored `lineup_panels` on home if still Unsplash URLs; Dev `app_cards` / `app_collections` / `ai_collections` rows until eval-file helpers after deploy. Automation source UUID in brief was missing on disk — used `image-89306316` (UMC + robot) from Cursor assets.

## 2026-09-29 — Automation Cell Design gallery +3 photos (Cursor)

**Automation Cell Design** collection now **4 images** (cover unchanged: `automation-cell-design.jpg`).

- New assets: `automation-gallery/automation-cell-lab.jpg`, `automation-cell-guarded.jpg`, `automation-cell-vision.jpg` (proto + theme sync).
- Proto `automation-integration.html` + theme `page-automated-system.php` `ai_collections` default.
- **Loose end:** stored `ai_collections` row on `automated-system` until `replace-gallery-media.php` on Local/Dev. Not committed.

## 2026-09-29 — Deploy + ACF apply (Dev) + commit (Cursor)

Theme rsync to gerotechdev; chmod 755/644 on child theme. Dev ACF: Custom Workholding card, MCS CTA, Demo gallery (+2), EOAT gallery (3 lines), hero already current. Caches flushed.

- Local: theme synced; **DB offline** — ACF not applied. Start Local and run the five eval-file helpers when ready.
- `set-card-image.php`: idempotent check compares full basename (not stub prefix).
- Pushed to `origin/master`.

## 2026-09-29 — Automation hero photo (Cursor)

Replaced `automation-hero.jpg` with client yellow-robot cell photo (1024×471). Proto + theme asset; alt text updated. Path still `assets/images/automation-hero.jpg`.

- **Loose end:** stored `ai_hero_image` until `set-page-image.php`. Not committed.

## 2026-09-29 — Automation EOAT gallery +2 photos (Cursor)

**Robot EOAT – Ancillary Material Handling** gallery now 3 images (card cover still `robot-eoat.jpg`).

- `automation-gallery/eoat-vacuum-suction.jpg`, `automation-gallery/eoat-gripper-pair.jpg`.
- Proto `automation-integration.html` + theme `ai_collections` default.
- **Loose end:** `replace-gallery-media.php` on `automated-system` / `ai_collections` for stored rows. Not committed.

## 2026-09-29 — Applications Demo gallery +2 photos (Cursor)

Added client demo photos to the **Demo** gallery collection (Applications). Card thumb still `app-demo-showroom.jpg`.

- `app-gallery-demo-dc1.jpg`, `app-gallery-demo-showroom-group.jpg` in proto + theme.
- Proto `application.html` + theme `app_collections` default now 3 images.
- **Loose end:** run `add-app-gallery-items.php "Demo"` on Local/Dev to append to stored rows. Not committed.

## 2026-09-29 — MCS CTA shop-floor photo (Cursor)

Figma `7425:4357` CTA band background replaced with the attached shop photo (VF-2SS + yellow robot cell).

- `assets/images/cta-mcs-cell.jpg` (1024×682) + theme copy.
- Proto `machine-custom-solutions.html` and theme default `mcs_cta_image` now use it. Sheet-metal gallery photo left in place.
- **Loose end:** a stored `mcs_cta_image` attachment still wins over the template default until `set-page-image.php`. Not deployed.

## 2026-09-29 — Custom Workholding card photo (Cursor)

Figma card `7425:4236` comment: the welded subframe in the machining center is the card photo.

- New file: `assets/images/custom-workholding.jpg` (1012×1800, q80, ~408 KB) from the Figma fill. Theme copy in place.
- Proto `machine-custom-solutions.html` and theme `page-modification-of-standard-machine-tools.php` card default now use that file. Gallery set (`workholding-p2.jpg` and the other three) unchanged. `custom-workholding-plate.jpg` left on disk.
- **Loose end:** stored `mcs_cards` still points at the plate upload until `wp eval-file scripts/set-card-image.php` on Local and Dev. Not deployed. Not committed.

## 2026-09-28 — Applications Process Optimization gallery graphic (Cursor)

Gallery-only swap for **Process Optimization** on Applications. Card thumbnail left on the older cycle diagram.

- New file: `assets/images/app-optimization-gallery.jpg` (copied from Untitled drop; 93675 bytes). Card remains on `app-optimization.jpg` / uploads attachment.
- Proto `application.html` gallery cover + data-src → gallery filename; card markup unchanged.
- Theme default `app_collections` Process Optimization media → gallery filename; `app_cards` image default still `app-optimization.jpg`.
- Local ACF `app_collections` row **replaced** (1 pipe string); card `#3476` untouched.
- Theme-only rsync to Dev; Dev `app_collections` media written as **string** (not array); card `#3475` untouched. Page + CDN caches flushed.
- Dev HTML: card `uploads/…/app-optimization.jpg`; gallery `…/app-optimization-gallery.jpg` (cover + data-src).
- **Not committed.**

## 2026-09-28 — Applications Process Troubleshooting cabinet photo (Cursor)

Replaced the Process Troubleshooting stock stand-in with client electrical-cabinet photo (`app-troubleshooting-cabinet.jpg`). Single-item gallery **replaced** (not appended); card image updated.

- Copied from Untitled asset drop → `assets/images/app-troubleshooting-cabinet.jpg` (254813 bytes); synced to theme + Local.
- Proto/theme markup already pointed at the new file; stored ACF applied on Local (`app_cards` attachment `#3489`, `app_collections` 1 pipe line) and Dev (`#3485`; gallery media string fixed after an initial array write broke cover/data-src).
- Theme-only rsync to Dev; page + CDN caches flushed. Dev HTML shows cabinet on card + gallery (3 refs); old `app-troubleshooting.jpg` gone.
- **Not committed.**

## 2026-09-28 — MCS Auto Door Integration gallery (+3 photos) (Cursor)

Appended three client photos to **Auto Door Integration** (existing real cover `auto-door-haas.jpg` kept). Service card left alone (video + poster).

- New files: `mcs-gallery/auto-door-vf2yt.jpg` (first of new), `auto-door-servax.jpg`, `auto-door-pendant.jpg`
- Order after append: haas → vertical-door-closed → vertical-door-window → vertical-door-drive → vf2yt → servax → pendant → video
- Proto + theme default + stored `mcs_collections` row on Local and Dev (theme-relative paths)
- Theme images + PHP rsynced to Dev; caches flushed. Not committed.

## 2026-09-28 — MCS Custom Workholding gallery (4 photos) (Cursor)

Renamed gallery collection **Custom Fixture Design** → **Custom Workholding** and replaced its media with four client tombstone/fixture photos (cover = `workholding-p2.jpg`). Service card stays on `custom-workholding-plate.jpg`.

- Files: `mcs-gallery/workholding-p2.jpg`, `workholding-tombstone.jpg`, `workholding-gtd-11962.jpg`, `workholding-gtd-11961.jpg`
- Proto + theme default + stored `mcs_collections` row 5 on Local and Dev (theme-relative paths; no media sideload)
- Theme images + PHP rsynced to Dev; caches flushed. Not committed.

## 2026-09-28 — MCS Custom Workholding card photo (Cursor)

Replaced the Custom Workholding service-card photo only (Figma `7497:732`). New file `assets/images/custom-workholding-plate.jpg` (silver tombstone plate + Jergens clamps + hydraulic cylinder). Gallery "Custom Fixture Design" still uses `custom-fixtures.jpg`.

- Proto: `machine-custom-solutions.html` card img + alt updated.
- Theme default: `page-modification-of-standard-machine-tools.php` `mcs_cards` Custom Workholding image path.
- Stored ACF: Local attachment `#3488`, Dev `#3484`. Theme image + PHP rsynced to Dev; caches flushed.
- Not committed.

## 2026-09-28 — ES CTA band client photo (Cursor)

Replaced the Engineered Solutions CTA background only (Figma `7425:3847` overlay section). New file `assets/images/cta-es-cell.jpg` (yellow FANUC + Midaco cell). Automation keeps `cta-rail-robot.jpg`.

- Proto: `engineered-solutions.html` points at `cta-es-cell.jpg` (no srcset; no @2x).
- Theme default + alt updated in `page-engineered-solutions.php`.
- Stored ACF `es_cta_image` updated on Local (#3487) and Dev (#3483). Theme image + PHP rsynced to Dev; caches flushed.
- Not committed.

## 2026-09-28 — Remaining export photos (Cursor)

`geroTech-images/image 12`–`17` already match the Automation HMI screens and Layered Controls diagram. `image 7`–`11` were new and were added:

- Part Programming gallery: `app-gallery-coolant-impeller.jpg`, `app-gallery-boring.jpg`, `app-gallery-drill.jpg` (now 7 photos).
- Training gallery: `app-gallery-training-qr.jpg`, `app-gallery-training-vf3.jpg` (now 5 photos).

Prototype, theme defaults, Local, and Dev. Not committed.

## 2026-09-28 — Applications + Automation client photos (Cursor)

Tristien photo comments applied on prototype, Local, and Dev. Stored ACF rows updated (they win over defaults).

- **Process Optimization** gallery now uses `app-optimization.jpg` (same cycle diagram as the card).
- **Tooling Recommendation** card + gallery use `app-tooling-cart.jpg`. Source is a crop of the Figma comment screenshot — the blue selection handle and comment pin are still in the pixels. A clean export would replace this file.
- **Demo** card + gallery use `app-demo-showroom.jpg` (UMC-750 showroom).
- **Pre-Engineered Solutions** card uses `pre-engineered-card.jpg` (grey cabinet close-up). Gallery uses a different file, `pre-engineered-gallery.jpg` (white dual-door cabinet on the floor).
- Theme PHP gallery defaults that were single-quoted (`{$uri}` literal) now interpolate.
- Local attachments: tooling #3484, demo #3485, pre-engineered card #3486. Dev: #3480, #3481, #3482. Caches flushed on Dev.
- Not committed.

## 2026-09-23 — CORRECTION 2: wireframes are code too, comments map via Cursor (DSH)

Two more corrections from Matt, both folded in:

1. **Wireframes are also built in code and presented in Figma** — not drawn in Figma. Reinforces the real thesis: **everything is made in code; Figma is always the presentation layer.**
2. **Code wireframes make content updates a ~5-minute job instead of hours** — text and section order live in one place rather than repeated across dozens of drawn frames.
3. **Figma comments are mapped to the code using Cursor** — that's the mechanism that closes the loop: client comments in Figma → Cursor maps them to code → change made → build re-imported into Figma as artboards.

**Changes:**
- `wireframes.png` regenerated: **"Wireframed in code, presented in Figma"**, with the five-minute-edit callout.
- `feedback-channel.png` regenerated: **"Comments in Figma, applied to the code"**, with the explicit loop `Figma comment → Cursor maps it to the code → change made → re-imported to Figma`. Real pins/quotes re-fetched from the Figma API (node `7283:480`).
- `figma-room.png` regenerated: panel captions now read "Built in code, reviewed in Figma" / "Built in code, presented in Figma", and the intro says every artefact is code-built.
- Deck: title lede, phase map (03/04/05 descriptions), prototype slide (new "content updates in minutes, not hours" bullet), and the checklist (now **10 items**, including "Wireframe in code; present it in Figma" and "Map every Figma comment to the code with Cursor").
- Speaker notes rewritten around the corrected thesis, with new Q&A for "Isn't building wireframes in code slower?" and "How do Figma comments actually get actioned?".
- Phase map gets an empty filler cell so the 7-item grid doesn't show a grey gap.

## 2026-09-23 — CORRECTION: Figma is the client room, code is the engine (DSH)

Matt corrected the core premise of the playbook. The previous version's headline — **"Design happens in code, not in a static file"** — implied Figma gets sidelined. **The opposite is true.**

**The corrected model (now the spine of the deck):**
- **Figma is the room.** Every client-facing moment happens there — wireframes, moodboards, designs, comments, conversation. *"Figma is where we present to the client for everything."*
- **The code prototype is the engine.** It is **internal until the design phase**; from there it's what lets us explore **responsive behaviour, layouts and animation concepts** fast.
- **Design phase = moodboards/reference sites → agree the homepage → iterate all pages**, moving between Figma and Cursor.
- **The bridge:** **html.to.design** (Chrome extension + Figma plugin) imports the HTML prototype into **editable Figma artboards**, so code-built work is reviewed in Figma. *Not "Figma or code" — code for the hard-to-judge parts, back into Figma for the conversation.*
- **Figma is a design AND presentation tool**, alongside the code prototype.

**Deck changes (`case-study/gerotech-process-playbook.html`, now 22 slides, 18 images):**
- Phase model corrected to **seven phases**: discovery · sitemap · wireframes · design · review · convert · hand off. (Wireframes and design stay separate steps — Matt's call.)
- **New slide 3 — "Figma is the room"**, stating the collaboration model up front.
- **New slide 8 — "What the code prototype adds"** (responsive, animation, layout; internal until design).
- **New slide 10 — "HTML → Figma artboards"** with the html.to.design bridge.
- Phase 4 sheet reframed to "Design & iterate" (moodboards → homepage → all pages).
- Three sheets regenerated so their baked-in phase labels match the seven-phase model; `client-controls` had its stale "Phase 8" eyebrow clipped (the admin screenshot source was never committed, and `/tmp` was cleaned mid-session — capture tooling now lives in the gitignored `.tmp/gtcap/`).

### GitHub Pages — the deck now lives in its own repo

**Incident (2026-09-23):** the repo was accidentally switched to **private**, which disabled GitHub Pages on the current plan — the whole site 404'd (root, deck and `/JOURNAL.md`). The Pages deploy run showed **build succeeded, "Deploy to GitHub Pages" failed**, and `has_pages` flipped to `false`. The repo has been **restored to public**.

**Deck moved to a dedicated public repo** so the internal source stays separate from the published artifact:

- **Repo:** https://github.com/MattybotStew/gerotech-process-playbook (public)
- **Live:** https://mattybotstew.github.io/gerotech-process-playbook/
- Contains only `index.html` (redirect), `gerotech-process-playbook.html`, `speaker-notes.md`, `images/` and a README.
- ⚠️ **Pages is intentionally left OFF on this prototype repo** — the deck's canonical URL is now the dedicated repo above. The old `mattybotstew.github.io/gerotech-prototype/case-study/...` URLs are dead. Re-enable here only if the whole-repo exposure is acceptable.
- ✅ This also resolves the earlier concern that Pages was serving `JOURNAL.md`, `AGENTS.md`, `docs/PROJECT_BRIEF.md` and the theme PHP: none of that is published now.
- **Drift warning:** `case-study/` in this repo and the playbook repo are now two copies. Edit here first, then re-copy `case-study/` → playbook repo.

## 2026-09-23 — Playbook restructured as the 8-phase process (DSH)

Matt redefined the story: the deck now follows the **engagement lifecycle** — kickoff/discovery → sitemap → wireframes → **design built in code, not Figma** → iterate between code and the design file → review → convert to WordPress (native editor + ACF, no builder) → hand off a designed front end to dev for complex functionality.

- **`case-study/gerotech-process-playbook.html`** — now **20 slides**, 16 images. Eight-phase spine plus two cross-cutting slides (style guide, shared brief), a **licence-payoff slide**, checklist, roadblocks, outcomes, ask.
- **New visuals (all real):**
  - **Wireframe** — node `6147:2346` (`gerotech-engineered-solutions-tabbed-wireframe`), genuine grey image slots + placeholder copy. ⚠️ **`6217:425` is named "wireframe" but renders as a full-colour design** — do not use it as a wireframe.
  - **Iteration loop** — working draft beside the Figma file (`7306:1063`).
  - **Discovery** — the live-site audit findings and what each one changed.
  - **Sitemap** — IA diagram from the real nav structure.
- **Repointed the existing one-pagers** from "Step N ·" to phase-neutral eyebrows so they don't clash with phase numbering.
- **Licence claim — deliberate wording (Matt-approved):** *no page-builder licence, no commercial theme licence; one plugin licence (ACF Pro) replaces both.* Explicitly **not** "zero licences" and **not** "no themes" — the build is a child theme of the client's existing bespoke parent, and ACF Pro is licensed. The speaker notes carry the exact wording plus the parent-theme answer.
- **Correction to the build description:** the PM handoff plan *recommended* Gutenberg + ACF Blocks; what actually shipped is **ACF fields rendered by PHP templates** on a child theme (the plan's own §1 heading). The deck says "WordPress's native editor plus structured fields" accordingly.
- **Bug fixed:** the sheet generator wrote doubled CSS braces into a non-f-string BASE, silently dropping `.note`/`.shot` rules. Corrected and both sheets regenerated.
- **Verified:** 20 slides / 20 notes, tag balance clean, 16 image refs all resolve, no orphans; renders confirmed for the phase map, phase 4, phase 8 and the payoff slides.

### GitHub Pages — the deck is public

- Pages was already enabled on this repo (serving `master` root), so the deck publishes automatically:
  **https://mattybotstew.github.io/gerotech-prototype/case-study/gerotech-process-playbook.html**
- ⚠️ **Pages serves the whole repo**, not just the deck — `/JOURNAL.md`, `/AGENTS.md`, `/docs/PROJECT_BRIEF.md`, `/cline-project-handoff.md` and the theme PHP all return 200 (`.clinerules` 404s, dotfiles excluded). The GitHub repo was already public, so this is not new exposure, but the Pages URLs are easier to find. Fix if it matters: private repo + a separate public repo holding only `case-study/`.
- **Fixed a capture artifact that had gone public:** the client-controls screenshot's admin bar read "Howdy, gerotech-shotbot" (the temp capture account). The admin bar is now clipped out of the figure.

## 2026-09-23 — Process playbook rebuilt as a visual how-to (DSH)

Leadership-facing deliverable, entirely outside the prototype/theme build — **no site files touched**.

**Story arc across the session:** 18-slide case study → problem slide dropped (17) → reframed as a **nine-step how-to with real screenshots** (16).

- **`case-study/gerotech-process-playbook.html`** — 16-slide self-contained deck (no build step, no server). Styled on the project's own design system (Barlow Condensed + Navigo, brand tokens) so the deck demonstrates the discipline it documents.
  - **How-to spine:** method map → 9 numbered steps (draft · style guide · feedback channel · shared brief · carry across · cost the path · build · hand over · verify) → checklist → roadblocks → outcomes → the ask.
  - **Two slide types:** guide slides (figure + "How to do it" bullets + a *"how you know it worked"* proof box) and full-bleed **figure slides** (the generated one-pagers). `body.sheet-mode` hides the deck wordmark on figure slides, which carry their own header.
  - Nav: ← / → / Space, Prev/Next, swipe, `#slide-N` deep links, **N** notes, **P** print (one landscape page per slide).
- **`case-study/images/` (12 images, 2.7 MB) — all captured from the real thing, nothing mocked:**
  - Prototype + built-site pages via headless Chrome/CDP against a local server and `gerotech.local`.
  - Generated one-pagers: style guide (parsed from the **real `tokens.css`**), shared brief (excerpts from the **real `AGENTS.md` / `.clinerules` / `JOURNAL.md`**), feedback channel (**real Figma comment pins + verbatim client quotes** via the REST API, node `7283:480`), client controls (real wp-admin), verification (**real `audit-acf-applied.php` output — 279/291**), and the design-to-build comparison.
  - Heavy PNGs converted to JPEG q85 (8.1 MB → 2.7 MB).
- **`case-study/speaker-notes.md`** — rewritten for the how-to: per-step talking points, tone rules, likely Q&A, and terms to avoid out loud.
- **Verified:** 16 slides / 16 note blocks, tag balance clean, all image refs resolve, no orphans; headless renders of slides 1–5, 10 and 13 confirmed layout and trim.
- **Tally:** `L-01` 18 → `L-02` 17 → `L-03` 16 slides.

### Local-env side effects (record, no repo change)

Capturing the admin screens needed `gerotech.local` working again:

1. **`wp-admin` was 502-ing** (php-fpm fork crash; fresh crash reports at 09:41). Local had regenerated `wp-config.php`, dropping the fix from 2026-09-16. **Re-applied** `@ini_set('display_errors','0')` + `WP_HTTP_BLOCK_EXTERNAL` + `DISABLE_WP_CRON` (local-only, not in repo). Verified: 5× consecutive `wp-admin` 200, no new crash reports.
2. **Local admin password is no longer `localpass123`.** Rather than reset anyone's credential, a **temporary admin `gerotech-shotbot`** was created for capture and **deleted afterwards** — administrator list is back to the original 8. If admin access is needed later, reset via Local's bundled PHP + `wp-load` bootstrap.

## 2026-09-22 — Orphaned hero attachments deleted on Local + Dev (Cline)

Removed the two retired hero uploads left over from the 2026-09-21 hero-art swap:

- **Local:** `hero-campus-vans.jpg` (attachment **3466**) and `es-hero.png` (**3429**) — both DB rows and upload files deleted.
- **Dev:** `hero-campus-vans.jpg` was already absent (never imported there); `es-hero.png` (**3429**) deleted. Caches flushed.
- **Verified orphaned before deleting:** a reference scan (postmeta-by-ID, posts content, non-attachment postmeta, options) found the only "references" were the attachments' **own** meta — `_wp_attached_file`, `_wp_attachment_metadata`, and the `_gerotech_src` import marker. Self-meta must be excluded from the scan or the deletion script flags everything as referenced.
- **Verified after:** active heroes still serve (`hero-slide-1.jpg` + `@2x`, `es-hero.jpg` + WP-generated sizes on Dev); 0 page-HTML references to the deleted filenames; home + ES + Automation all 200 on both environments.
- Reusable script kept at `/tmp/delete-orphaned-media.php` (dry-run by default, `GEROTECH_DELETE=1` or `--delete` to act) — not added to the repo's `scripts/` since it's a one-off cleanup, not part of the build flow.


## 2026-09-22 — Commit + Dev deploy of the Automation gallery trim (Cline)

- Committed and pushed the previously-uncommitted Cursor gallery trim **plus** the unpushed `f8ef395` (HMI screens + layered controls) — `master` is now `f7292fa`, level with `origin/master`.
- Theme rsynced to Dev with `--delete` (also removed a stale `gerotech-child/scripts/` dir on Dev); page + CDN caches flushed. All 5 key URLs 200; `/automated-system/` renders the 5 service-card collections (HMI Design, Layered Controls, Automation Cell Design, Robot EOAT, Pre-Engineered) with the 7 old collections gone. No DB change was needed — Dev's stored `ai_collections` rows were already correct from the earlier Cursor session.


## 2026-09-22 — Homepage + ES pages ACF editability verification (Cline)

User asked to confirm the homepage and all ES pages are fully editable/customizable with ACF per the plan (`handoff/acf-spec.md`, `implementation-plan-wordpress-theme-acf.md` §13).

- **Code side: already complete.** `front-page.php` + `page-engineered-solutions.php` + the 3 ES detail templates all read every section (hero, cards, galleries, CTA, signup) through `gerotech_field()` with the current design as template defaults; field groups live in `inc/acf-fields.php` / `inc/acf-legacy-fields.php`. No gaps found vs. the ACF spec — no code changes needed.
- **Applied side: verified on Local.** Ran `scripts/audit-acf-applied.php`: **279/291 top-level fields applied across 32 targets**. The 12 blanks are all correct-by-design (accent/CTA colour selects, `es_partners_logos`, `haas_brand_logo`, `cta_image`, `mcs_cta_body`, `app_cta_body`).
- Theme re-synced to Local (`sync-theme-to-local.sh`); homepage + 4 ES pages all HTTP 200.
- **CLI gotcha recorded:** a script that only `require`s wp-load never fires plugin `init`, so ACF is absent. Wrap: `require wp-load; require <script>;`. The audit's `$args` warning under plain CLI is harmless.
- No commit — verification pass only (.clinerules + JOURNAL updated).


## 2026-09-22 — Automation gallery matches the five service cards (Cursor)

Client: the gallery categories are the service cards only. Removed collections with no card (Haas Mill Robot Cell, Robot Line Integration, Guarded Robot Cell, Vision System, Dual-Gripper EOAT, Vacuum EOAT, Gripper Fixtures).

Gallery is now, in card order: **HMI Design**, **Layered Controls Solutions**, **Automation Cell Design**, **Robot EOAT – Ancillary Material Handling**, **Pre-Engineered Solutions**. The last three use the same photo as their card. Stored `ai_collections` on page 1239 updated on Local and Dev (5 rows). Caches flushed.

## 2026-09-22 — Automation gallery: HMI Design + Layered Controls (Cursor)

Figma `7196:4079` gallery comment (the five HMI screens). Hero and the two service-card pins were already done.

- Added **HMI Design** (5 screens) and **Layered Controls Solutions** (diagram) at the front of the Installed Automation gallery. The seven installed-project photos stay.
- Prototype `automation-integration.html`, theme default in `page-automated-system.php`, images in `assets/images/automation-gallery/`.
- **Local:** stored `ai_collections` on page 1239 was 7 rows and would have hidden the template default. Prepended the two collections (now 9). Theme synced to Local.
- **Dev:** theme rsync + `field_ai_collections` prepended on page 1239 (now 9 rows). Caches flushed. Live HTML includes `hmi-operator-1` and `layered-controls-diagram`. Temp eval script removed from the theme.

## 2026-09-22 — Automation Figma `7196:4079` image pass (Cursor)

Closed the last open stand-in on **Automation & Controls** (`7196:4079`): **Pre-Engineered Solutions** card.

- Pulled client art from the Figma IMAGE fill on node `7196:4324` → `assets/images/pre-engineered-solutions.jpg` (1120×550).
- Prototype [`automation-integration.html`](automation-integration.html) + theme default in [`page-automated-system.php`](wp-content/themes/gerotech-child/page-automated-system.php); Figma node IDs noted in HTML comments on hero + all five service cards.
- Gallery copy unchanged — eyebrow **Gallery**, title **Installed Automation Gallery** (comment `#20` on older frame; not Figma wireframe “Project Gallery / Projects”). Seven installed-project collections kept.
- **Local:** `set-card-image` imported attachment `#3481` for Pre-Engineered; other four cards + hero already on correct uploads.
- **Dev:** theme rsync (template + JPEG); `set-card-image` via SSH heredoc (WP-CLI splits spaced titles unless quoted in a heredoc) → attachment `#3479`; caches flushed.
- Verified: Local + Dev `/automated-system/` serve `pre-engineered-solutions`; no Unsplash on the page.

**Loose end:** helper scripts were rsync’d to `gerotech-child/scripts/` on Dev for the one-off eval — safe to delete from Dev on the next theme-only push if you want the theme dir strictly PHP/assets.

## 2026-09-22 — "Solutions" now orange in the MCS hero (Claude)

Client: the Machine Custom Solutions hero h1 should show "Solutions" in brand orange. Committed `0752f4c`, live on Local + Dev.

**This needed no new field** — it's the accent functionality added earlier the same day. Only the default and the stored value changed:
- Template default: `mcs_hero_main` → `'Custom <em>Solutions</em>'`.
- Prototype: `<span class="accent">Solutions</span>` inside `.mcs-name-split__main`, matching the convention the ES hero already uses.

**The stored value had to be updated on both environments** — a stored `mcs_hero_main` beats the code default, so the template change alone would have shown nothing. Both now hold `Custom <em>Solutions</em>`.

**`mcs_hero_accent_color` was deliberately left BLANK.** Blank resolves to the template default `orange`; seeding it would freeze the design colour, which is the trap the standing rule warns about. Confirmed stored as `''` on both.

**Verified:** h1 emits `<span class="accent">Solutions</span>`; computed colour **rgb(243, 138, 44)** on prototype, Local and Dev; the breadcrumb still reads plain "Machine Custom Solutions"; no literal `<em>` leaks; 0 PHP warnings; Dev in sync with the repo.

That breadcrumb check is not ceremony — it is precisely the bug that surfaced when accent support was first added to this hero (the breadcrumb was escaping the headline and printing `Custom &lt;em&gt;Solutions&lt;/em&gt;`). Worth re-checking on every accent-word change.

**Follow-up — the Automation page had the accent backwards.** Client asked for "the same" on `automation-integration.html`. It turned out the **prototype** was the one missing it: WordPress already had `ai_hero_main = 'and <em>Controls Solutions</em>'` and rendered `<span class="accent">Controls Solutions</span>` orange on both Local and Dev. So this was **prototype-only drift** — no theme change, no deploy.

Worth noting the direction: the rule of thumb is "prototype is source of truth", but for hero accent words the **WP build was ahead** of the prototype. An audit of all five prototype heroes against their WP defaults found Automation as the only mismatch:

| Page | Accent word | Prototype | WP |
|---|---|---|---|
| ES | Solutions | ✅ | ✅ |
| MCS | Solutions | ✅ | ✅ |
| **Automation** | **Controls Solutions** | ❌ → **fixed** | ✅ already |
| Applications | Solutions | ✅ | ✅ |
| Careers | manufacturing | ✅ | ✅ |

Verified after the fix: prototype computed colour `rgb(243, 138, 44)`, breadcrumb still plain "Automation and Controls Solutions", and Dev's WP hero unchanged and correct.

## 2026-09-22 — Remaining hardcoded copy made ACF-editable; MCS hero matches the homepage (Claude)

Client: *"i need everything that should be editable using ACF and the hero h1 needs the same functionality as the homepage."* Committed `e3a3745`, deployed and seeded on Local **and** Dev.

**Audit first.** Every template already read its content through ACF (`$pick()` on the rebuilt pages, `gerotech_field()` on the legacy ones — my first scan only grepped `$pick` and wrongly concluded the 7 legacy pages had **zero** fields; they use `gerotech_field` with definitions in `inc/acf-legacy-fields.php`). So the job was finding genuinely hardcoded strings, via a scanner that tracks PHP state and ignores text inside PHP blocks and `<template>`s.

**MCS hero H1 — the only hero lacking homepage functionality.** ES, Applications, Automation and Careers already had `<em>` accent words + a colour select; MCS did not.
- Added `mcs_hero_accent_color` (White / Haas Red / Brand Orange), **blank by default** like every other interior hero, and `<em>` now works in both the lead and main.
- **Found and fixed a bug this exposed:** the breadcrumb echoed `esc_html( $hero_main )`, so an `<em>` in the headline printed the literal tags `Custom &lt;em&gt;Solutions&lt;/em&gt;` in the breadcrumb. Now `strip_tags()` first — which is what `page-automated-system.php` already did. MCS was simply missing it.

**Service page — by far the biggest gap (87 hardcoded strings).**
- The 6 tab labels are now **individual fields, not a repeater**: each is bound to a fixed tab id (`tf_service`, `tf_general`, …), so a repeater would let someone reorder them and silently break the tab wiring.
- The planned-maintenance checklist (12 groups, ~60 lines) is now **two repeaters** with `heading` / `items` (one line per entry) / `column` (left or right), plus the footnote, the optional-services block and the request heading+body. A textarea per group rather than a nested repeater because the legacy layout is driven by `.t_left`/`.t_right`.
- The hidden `#locations` section and the Application Support note are field-driven too — Contact's equivalent was already ACF, so the two pages disagreed.
- The column-splitting helpers went into `inc/helpers.php`, not the template, to avoid a redeclare fatal if the template is ever included twice.

**Also:** careers table headers, contact page title + form heading, training/about page titles, and the mailing-list form strings (**duplicated across six templates**) which are now global `Site Content — Forms` fields.

**Removed three dead fields.** `mcs_hero_eyebrow`, `app_hero_eyebrow` and `ai_hero_eyebrow` were registered but rendered by nothing — filling them in silently did nothing. All three were empty in the DB, so nothing was lost. Notes left in their place rather than a silent deletion.

**Verification — every refactor was diffed against the original markup from git:**
- Service checklist and locations render **byte-identically**, including the curly apostrophe in "axis’" and the `#location_grand_rapids` / `#location_flat_rock` anchors.
- **A regression I caught mid-way:** my first version derived those anchors with `sanitize_title()`, producing `location_grand-rapids-mi` — the legacy stylesheet targets `#location_grand_rapids`, so the responsive rules would have silently stopped applying. Fixed with an explicit, editable `anchor` field defaulting to the original values.
- 19/19 new fields register; **13 Local + 13 Dev pages 200 with 0 PHP warnings**; Dev's copy of the theme is in sync with the repo.
- `scripts/seed-acf-content.php` seeds the copy so editors don't see empty fields; **idempotent** (28 skips / 0 writes on re-run) and it deliberately never touches the accent-colour selects.

**Method note:** Dev's SSH gateway supports neither `scp` (sftp subsystem disabled) nor stdin forwarding, and **each SSH session appears to get its own `/tmp`** — a file written in one session was gone in the next. The working pattern is to base64 the payload and write+run it **in a single SSH command**.

**Excluded on purpose** (per the agreed scope): breadcrumbs, "View Details →", "LEARN MORE", and the "P:" / "F:" phone-fax prefixes.

## 2026-09-22 — Custom Workholding card gets the client's fixture photo (Claude)

Client pointed at Figma node `7196:3332` ("Button dialog") → the **Custom Workholding** service card. Swapped its photo.

- **Asset:** `assets/images/mcs-gallery/custom-workholding.jpg` — **new file**, not an overwrite. The old `custom-fixtures.jpg` is still used by the MCS **gallery collection** "Custom Fixture Design" (cover + lightbox), so replacing the file in place would have silently changed that too, and its alt text ("Row of custom aluminum fixture plates") would have become wrong. 1012×1800, 420KB, from the client's 2250×4000 PNG via `sips` q82.
- **Prototype + theme default:** card image → the new file, alt corrected to describe the photo.
- **Seeded on both:** Local attachment **3474**, Dev **3473** (`mcs_cards_5_image`), because the stored repeater rows win.

**Crop check that mattered:** the source is portrait (aspect 0.563) and the card box is landscape (~2.15), so `object-fit: cover` shows only a **26% vertical band, centred (37%–63%)**. That looked risky for a portrait shot, so I simulated the exact crop with `sips -c 472 1012` before committing to it, and it frames the fixture and subframe perfectly — matching Figma's own 560×275 crop (also a ~27% centred band). No `object-position` override needed. Worth repeating for any portrait artwork going into these landscape cards.

**Verified on Local + Dev:** card renders the new photo, image serves 200 `image/jpeg` (419,901 bytes), the gallery still references `custom-fixtures.jpg` (2 refs), page has 0 Unsplash refs, grid is 1 video + 7 images.

## 2026-09-22 — Specialty Machine card gets the client's Haas ST-45 photo (Claude)

Client pointed at Figma node `7196:3348` ("Button dialog") and said "replace thumbnail with this image". That node is the **Specialty Machine** service card, so the card's Unsplash stand-in (`photo-1655393001768`) is retired.

**Getting the image:** no Figma MCP is wired into this session, but `~/.figma_token` exists, so I used the REST API — `/v1/files/:key/nodes?ids=7196:3348` to identify the frame, then read the IMAGE fill's `imageRef` and pulled the original from `/v1/files/:key/images`. Useful fallback when the MCP is unavailable.

- **Asset:** `assets/images/mcs-gallery/specialty-machine.jpg` (1920×670, 245KB) converted from the client's 2123×741 PNG via `sips` q82. No alpha, so no black-edge problem converting to JPEG.
- **Prototype:** card image → local file, alt corrected from the bare "Specialty Machine" to describe the photo (Haas ST-45 with bar feeder). The stale `Stand-in: Unsplash/ZHENYU LUO — awaiting client photo` comment is gone — Figma's frame still carries that name, but the artwork is real client photography.
- **Theme:** same swap in the `mcs_cards` default array.
- **Seeded on both**, because the stored repeater rows win: Local attachment **3473**, Dev **3472** (`mcs_cards_7_image`).

**Verified on Local + Dev:** card renders the ST-45, image serves 200 `image/jpeg` (244,811 bytes), and the MCS page now has **0 Unsplash references**.

**Still open — one stand-in left on this page:** the **Process Engineering** card (index 6) still uses `photo-1666634157070` in both the prototype and the theme default. Every other MCS card is real client art, so this is the last remote asset on an otherwise fully local page. Worth asking the client for a photo.

## 2026-09-22 — Auto Doors card thumbnail is now the client's video (Claude)

Client: the Auto Doors card on `/modification-of-standard-machine-tools/` should show the video, not the still photo. `auto-door.mp4` was already in the repo (used by the gallery collection); this makes it the **first inline card video** in the project.

**Done:**
- **Prototype:** `machine-custom-solutions.html` — the card's `<img class="mcs-card__image">` became `<video class="mcs-card__image" data-card-video … autoplay muted loop playsinline preload="metadata" aria-hidden="true">` with `poster` set to the photo it replaced (no blank frame while loading; reduced-motion users keep a sensible still).
- **CSS:** the rules targeted `img.mcs-card__image` only, so the video would not have been sized. Extended both the box rule and the hover-zoom selector list to `video.mcs-card__image`.
- **JS:** `animations.js` now pauses `video[data-card-video]` and strips `autoplay` under `prefers-reduced-motion`. Placed **before** that file's reduced-motion early-return deliberately — anything after it never runs for those users.
- **ACF:** new optional `video` sub-field on the `mcs_cards` repeater (`field_mcs_card_video`, type file, mp4/webm/mov) so the client can set/replace a card clip from wp-admin. The PHP default array carries `'video' => 'assets/videos/auto-door.mp4'` for a fresh install.

**The trap that made the first attempt look like it failed:** I added the video to the template's default array and Local still rendered 8 images and 0 videos. `$cards = $pick( 'mcs_cards', <default> )` — Local and Dev have **stored** repeater rows, which win over the default, and those rows have no `video` key. So the fix needed a DB step, not just code: registered the sub-field, imported the mp4 as an attachment, and set `mcs_cards_3_video` + `_mcs_cards_3_video` on both. Same class of mistake as the hero defaults — **a code default only applies where nothing is stored.**

**Verified on Local + Dev:** 1 `<video>` + 7 `<img>` cards, video serves `200 video/mp4` (4,381,347 bytes), poster serves `200 image/jpeg`, new CSS/JS live, and the card renders the FANUC M-10iD/12 frame in the same 275px box as its neighbours.

**Watch item:** `auto-door.mp4` is 4.4MB and now downloads with the MCS page as an autoplaying loop. Worth compressing/trimming if more card clips arrive — there is no ffmpeg on this machine to do it here.

## 2026-09-22 — Machine Custom Solutions naming settled (Claude)

Open decision #3 in `cline-project-handoff.md` ("Machine Customization" vs "Machine Custom Solutions") is **resolved in favour of "Machine Custom Solutions"** (CloudMellow direction). The legacy URL is deliberately unchanged.

**The headline finding: the naming was already consistent, and the "conflict" was a lie in a comment.** "Machine Customization" appeared in **exactly one place** in the entire repo — the placeholder comment in `machine-custom-solutions.html` — and that comment claimed *"Nav/mega-menu still uses 'Machine Customization' until client confirms"*. That was never true. Every user-facing surface (nav, mega-menu, mobile nav, search modal, hero headings, breadcrumbs, ACF labels) already said "Machine Custom Solutions". So this was a **documentation bug that invented a decision**, not a real inconsistency. Worth remembering: a stale comment confidently describing the codebase is worse than no comment.

**Slug deliberately left alone.** The live path is still `/modification-of-standard-machine-tools/` while the page is titled "Machine Custom Solutions". That is now an explicit, documented choice rather than an oversight: links resolve through the single map in `inc/helpers.php` (`'machine-custom-solutions' => '/modification-of-standard-machine-tools/'`), and `functions.php` 301s `/machine-modification` there. The display name and the URL are allowed to differ; renaming would change a live Dev path for no user-visible gain and needs a redirect plan first.

**Changed:**
- `machine-custom-solutions.html` — replaced the false placeholder comment with the settled name, plus a note on the deliberate slug/name split and an instruction not to "fix" the slug without a redirect plan.
- `cline-project-handoff.md` — open decision #3 marked resolved; the "don't finalize" rule rewritten into a positive "write it exactly this way" rule.
- `docs/PROJECT_BRIEF.md` — the "IA decision open" row now reads resolved.
- `.clinerules` — session state.

**Deliberately NOT changed:** the dated historical plan at `docs/superpowers/plans/2026-06-29-gerotech-website.md` still contains "Machine Customization" (it is a point-in-time record of the old wireframe taxonomy — rewriting it would falsify history), and `cline-project-handoff.md:77` keeps its "Machine Customization/Custom Solutions" wording because it is describing the *old live IA*, not our build.

**One item still worth a client nod:** the client's own message said **"Machine Customizations"** (plural). We ship "Machine Custom Solutions". No code change is pending, but if they feel strongly the rename is mechanical — display text only, the slug is unaffected either way.

## 2026-09-22 — Machine Custom Solutions hero gets the client's machining-center photo (Claude)

Client supplied a photo of a 5-axis machining center interior (trunnion rotary fixture holding a large workpiece) for the **Machine Custom Solutions** hero background — the stand-in `photo-1727292485858` is retired.

- **Assets:** `assets/images/mcs-hero.jpg` (1920×1280, 444KB) + `mcs-hero@2x.jpg` (2560×1706, 660KB), converted from the client's 2508×1672 WebP via `sips` (q82/q78).
- **Prototype:** `machine-custom-solutions.html` page-hero now uses the local file with `srcset` (1920w/2560w) + `sizes="100vw"`; `fetchpriority="high"` added to match the ES hero. Alt corrected from "CNC machine on shop floor" to describe the actual subject.
- **Theme:** assets synced into `gerotech-child/assets/images/` so the image ships with the next push. **Correction below** — I first wrote here that nothing in WordPress consumes it; that was wrong.
- **Verified:** rendered the hero standalone at 1440 / 1920 / 500. Section is a clean 500px (`min-height`) at every width, the `srcset` picks the viewport-appropriate candidate, and `scrollWidth == clientWidth` at all three (no overflow).
- **Naming:** ~~the client and the nav/mega-menu say "Machine Customization(s)"~~ — **that was wrong.** "Machine Customization" appeared in exactly one place in the whole repo: the stale placeholder comment in `machine-custom-solutions.html`, which falsely claimed the nav used it. The nav, mega-menu, mobile nav, search, headings and ACF labels all already said **"Machine Custom Solutions"**. Settled as canonical 2026-09-22; see the naming entry above.

**Follow-up (same day) — the WP side was NOT prototype-only; I had this wrong.** I originally claimed "there is no `page-machine-custom-solutions.php` template, so nothing in WordPress consumes it". That was incorrect, and it mattered: the MCS hero **is** rendered in WordPress by **`page-modification-of-standard-machine-tools.php`**, whose `mcs_hero_image` default was the *same* Unsplash stand-in I had just replaced in the prototype. On Dev that hero appears at `/modification-of-standard-machine-tools/` under the "Machine Custom Solutions" headline — so the client would have seen the old stand-in on the live page while the prototype showed the new photo.

Fixed: template default → `assets/images/mcs-hero.jpg`, `srcset` via `gerotech_image_srcset()`, `fetchpriority="high"`, and the corrected alt. Because both environments store their own `mcs_hero_image` attachment id, the code default alone was not enough — imported and re-pointed the field on both: **Local page 1250 → attachment 3471, Dev page 1250 → 3470**. Verified live on Dev (new photo, full srcset, correct alt, old stand-in `gerotech-ca7ef98563` no longer referenced).

**Lesson:** "no template with that name" ≠ "not in WordPress". The MCS naming is split across the prototype page name, the WP slug and the shared `mcs_*` ACF prefix — grep the **ACF field name and the hero markup**, not the template filename.

## 2026-09-22 — ES bottom CTA gets the client's FANUC rail-robot photo (Claude)

Client supplied a photo of a FANUC robot on an overhead rail system (`RAILSYS001`, "RAILCAPACITY 15" visible) to use as the Engineered Solutions bottom CTA band background — the last Unsplash stand-in on that page.

- **Assets:** `assets/images/cta-engineered-solutions.jpg` (1920×1085, 484KB) + `cta-engineered-solutions@2x.jpg` (2560×1447, 740KB), converted from the client's 2722×1539 WebP via `sips` (q82/q78). Sizes sit alongside `cta-home-figma.jpg` and the hero pair.
  - **Renamed 2026-09-22 to `cta-rail-robot.jpg` / `@2x`** when the Automation & Controls CTA band was given the same photo — the old name described one of its two consumers.
- **Prototype:** `engineered-solutions.html` SECTION 13 now uses the local file with `srcset` (1920w/2560w) + `sizes="100vw"`, mirroring the homepage hero pattern.
- **Theme:** `page-engineered-solutions.php` default changed from the Unsplash URL to `assets/images/cta-engineered-solutions.jpg`, and it now emits `srcset` via `gerotech_image_srcset()` like the ES hero does.
- **Alt text fixed:** the old alt ("Engineering blueprints and design") described the retired stand-in, not this photo. Now "FANUC robot on an overhead rail system in a Michigan manufacturing facility".
- **Not just a code default — Local's DB had a stored `es_cta_image`** (attachment 3435, the OLD stand-in), which would have silently won over the new default. Imported the new file as attachment **3470** and pointed `es_cta_image` at it, so it appears in the media library where the client can swap it.
- **Verified:** Local serves the new attachment with a full WP-generated `srcset`; rendered the band standalone at 1440 / 768 / 500 and confirmed the crop (`object-position: center 35%`) keeps the rail and arm reading behind the copy with the gradient keeping text legible. **No horizontal overflow** — measured `scrollWidth == clientWidth` on every CTA element.

**Harness gotcha for future visual checks:** headless Chrome silently enforces a **500px minimum window width**, so `--window-size=390` renders at 500 and the screenshot is cropped — it *looks* like text overflowing the viewport. Verify narrow layouts by measuring (`scrollWidth` vs `clientWidth` via an injected `<pre>` read with `--dump-dom`) rather than trusting a 390px screenshot.

**Doc corrections found while doing this:** three files claimed the parked news markup was "preserved verbatim inside the comment" in `engineered-solutions.html`. It was not — the markup was moved to `partials/news-block.html` and ES keeps only a commented-out `data-include`. Fixed in `.clinerules`, `JOURNAL.md` and `handoff/acf-spec.md`. Also refreshed the stale Unsplash stand-in counts in `handoff/asset-manifest.md` (ES is now 0; `application.html` was listed as 14, actually 17).

## 2026-09-22 — News park committed, pushed, and deployed to Dev (Claude)

Closing out the Cline park work below (see that entry for the full rationale).

- **Committed** `1cf731f` "Park Latest Projects & News behind a client-toggleable ACF switch" — pushed to `origin/master` (0 ahead / 0 behind). Tree clean.
- **Deployed to Dev** by direct rsync (no GUI): delta was only 4 paths (`page-engineered-solutions.php`, `inc/acf-fields.php`, `template-parts/sections/`, `template-parts/sections/news.php`) because Dev was already current from the earlier GUI push. Then `wp page-cache flush` + `wp cdn-cache flush`.
- **Client switch live-tested on Dev, not just inferred:** `es_show_news` off → 0 news markers, on → 2 markers, back off → 0, then `post meta delete` so Dev sits at the default-off state. Dev also confirms the toggle is registered (`true_false`, `default=0`).
- **Full Dev audit:** 22 live pages all HTTP 200, **0 PHP warnings/notices** in served HTML (checked on cache-busted URLs so the page cache could not mask them), **0 broken images**. Dev `components.css` is **md5-identical** to Local (`e63813af6965fbde86ff3f33d8842bd4`), so the earlier locally-verified computed colours hold on Dev.
- **Non-blocker found:** the parent theme `gerotech/functions.php:13` emits "Undefined variable $arguments" under WP-CLI. It does **not** reach any web page. Parent theme is not in this repo, so it is out of scope to fix here.
- **Local hero colours were accidentally clobbered during testing** and restored: a scenario test wrote literal `white` into `home_hero_slides_{0,1,2}_accent_color`; the seed script cannot distinguish an intentional White from a leftover one, so it re-seeded white on top. Deleted the colour + legacy `accent_class` metas and re-ran `scripts/seed-home-hero-colors.php` → slide 1 `haas`, slides 2–3 `orange`, Haas eyebrow/headline `haas`, confirmed in served HTML.

**Unexplained 404s are expected, not broken:** `/machine-custom-solutions/`, `/automation-integration/`, `/applications/`, `/showroom/`, `/hero-variations/` do not exist on Dev — Dev carries the legacy slug set (`/unique-applications-for-standard-machines/`, `/automated-system/`, etc.). `/news/` 301s to `/news-and-events/`.

## 2026-09-22 — Latest Projects & News PARKED, client-toggleable (Cline)

Client MSG: "Sadly, we just don't know if we can support the Latest Projects & News right now. Can we remove this for now? — Can we disable on all pages BUT keep it as a component that can be easily added back by the client."

**Scope:** the editorial news split (`.news-section--editorial`) lives on exactly **two** pages — Homepage and Engineered Solutions. Homepage news was already removed in an earlier Figma-alignment pass (`7306:1063` has no news band), so **ES was the only page still rendering it**. Nothing else carries it (verified: grep across all 12 HTML + all theme PHP).

**What was done — parked, not deleted (both layers):**
- **Prototype:** `engineered-solutions.html` SECTION 12 reduced to a pointer comment + `PARKED` marker. **Correction (2026-09-22):** the original note here claimed the markup was "preserved verbatim inside the comment" — it was not. The markup was **moved** into `partials/news-block.html` and ES keeps a commented-out one-line `data-include`; uncomment it to bring the section back.
- **Theme:** the news markup moved **out of** `page-engineered-solutions.php` into a new **`template-parts/sections/news.php`** (an extract, not a rewrite — same output), rendered behind a new ACF toggle **`es_show_news`** (`true_false`, **default 0 / off**, `field_es_show_news`, on `group_es_content` → News tab). So the client can flip it back on **from wp-admin with no dev involvement**; flipping it on re-renders today's markup.
- **Prototype partial:** new `partials/news-block.html` holds the same markup as a client-facing reference (mirrors how `testimonials-block.html` works), header comment says PARKED.

**Gotcha honoured (recurring theme):** the ES news content was materialised into ACF by the 2026-09-18 seeding, so stored rows exist even though nothing renders them — they are left **untouched** in the DB (harmless while the toggle is off, and reappear instantly if the client re-enables). **Stored rows beat code defaults** — flipping the toggle is a genuine re-enable of the client's data, not a rebuild.

**Verified on Local:** synced (`sync-theme-to-local.sh`), `/engineered-solutions/` HTTP 200, **0 news markers** in served HTML, no PHP errors/notices, page otherwise unchanged. Toggle logic unit-checked (`es_show_news` on → partial included; off → nothing). ACF field confirmed registered (`name=es_show_news`, `type=true_false`, `default=0`, parent `group_es_content`).

**Files:** `engineered-solutions.html` (parked comment), `partials/news-block.html` (new), `page-engineered-solutions.php` (markup → partial + toggle), `inc/acf-fields.php` (toggle field), `template-parts/sections/news.php` (new). **Committed `1cf731f`** and pushed to `origin/master`.

**To re-enable:** client flips "Show the Latest Projects & News section" in ES → News tab (admin). Prototype: uncomment the `<div data-include="partials/news-block.html?v=20260922"></div>` line left in ES SECTION 12.

---


## 2026-09-22 — Applications: Fire Suppression + RFID removed from the WordPress BUILD (Cline)

Client MSG (Tristien Bridges, Sep 21): "on the prototype (but not figma) there are two sections that should not be showing: fire suppression and RFID … make sure those under APPLICATIONS will not go to development."

**The prototype was already clean; the build was not.** `application.html` had been cleaned earlier the same day (Kimi Work), but the WordPress side still shipped both sections twice over:

- `wp-content/themes/gerotech-child/page-unique-applications-for-standard-machines.php` carried them as **template defaults** (`app_cards` rows 7–8, `app_collections` rows 7–8).
- Worse, the 2026-09-18 seeding had materialised **8 cards + 8 collections into ACF on both Local and Dev**, so the live pages rendered "Fire Suppression" and "RFID" regardless of the template — confirmed by fetching both URLs before the fix: **6 `fire-suppression` + 6 `rfid` occurrences each, HTTP 200**. **Stored ACF rows beat code defaults**, so a template-only fix would have changed nothing on the environment the client is reviewing.

**Fix (two layers):**
1. Removed the two cards + two gallery collections from the template defaults, so a fresh environment (production build, clean install) is correct with no DB work.
2. New idempotent **`scripts/remove-apps-fire-suppression-rfid.php`** deletes any row whose title is Fire Suppression / RFID from `app_cards` and `app_collections` on the Applications page, then prints the remaining rows. Ran it on **Local** (8 → 6 in both repeaters) and on **Dev** (8 → 6); re-running reports "already clean (6 rows)" — safe on any environment.

**Verified (after `page-cache` + `cdn-cache` flush on Dev):** prototype, Local **and** Dev all render **6 cards + 6 gallery collections, 0 fire-suppression, 0 rfid**, HTTP 200, no PHP errors. Dev theme `rsync --checksum` dry-run is **zero drift**.

**MCS left alone on purpose** — the client scoped this to APPLICATIONS. `machine-custom-solutions.html` and `page-modification-of-standard-machine-tools.php` keep their own Fire Suppression collection (still 4 hits on Dev, as intended).

**Tooling notes:**
- This machine has **no `wp` CLI**, so Local DB work used Local's bundled PHP plus a `wp-load.php` bootstrap and the site's generated `php.ini` (already carries `mysqli.default_socket`) — recipe now in `.clinerules`. Dev uses the documented `wp eval-file -` over SSH stdin.
- A theme `rsync` pushes the **whole working tree**, so it also carried a **concurrent agent's uncommitted** `Get a Quote` → `Talk to an Engineer` copy change (`header.php`, ES/Apps/MCS/Automation templates) to Dev. It renders correctly and matches the prototype, but **Dev is currently ahead of `master`** until that change is committed.
- Vendored the design-revision stack into the repo as `scripts/design-stack.sh` (repo-aware: Web Lens/`webLens.defaultUrl` corrected to this project's static **:8080** server via a workspace-scoped `.vscode/settings.json` override, added a `serve` command, dropped upstream's broken `$0 usage` self-exec, and `check` now exits non-zero when something is wrong).


## 2026-09-22 — Removed Fire Suppression + RFID sections from Applications prototype (Kimi Work)

Client (Tristien Bridges, Message Board 9/21): the **Fire Suppression** and **RFID** sections exist on the prototype Applications page only — not in Figma — and must **not** go to development. Removed from `application.html`:
- the two `.mcs-card` articles (cards grid) — lines ~70–81
- the two matching `.gallery-collection` articles (Product Gallery) — lines ~193–223

HTML tag-balance validated clean; no remaining references in `application.html`. Note: the **MCS page** (`machine-custom-solutions.html`) still has its own Fire Suppression gallery collection (different context, client only flagged Applications) — left untouched. `assets/images/mcs-gallery/fire-suppression.jpg` still referenced by MCS, kept.

## 2026-09-21 — Pushed theme + hero art + seeds to WP Engine Dev over SSH (opencode)

Live Dev (`gerotechdev.wpenginepowered.com`) is now current. The gateway responded this time (the earlier 11:07 ET hang did not repeat).

**What landed:**
- **Theme:** `rsync --delete` of `wp-content/themes/gerotech-child/` (7 changed files), then a checksum dry-run confirmed **zero drift**.
- **Colours seeded** via `wp eval-file -` (stdin): slide 1 `haas`/`haas`, slides 2–3 `orange`/`orange`, `haas_eyebrow_color=haas`, `haas_accent_color=haas`. Interior hero fields left blank on Dev = design Brand Orange (correct-by-default, no DB work).
- **Homepage hero art:** sideloaded the pushed `hero-slide-1@2x.jpg` → attachment **3467** (matched Local's ID by luck of sequential insert), repointed slide 1's ACF image field, full 2560w + 5 intermedate sizes srcset rendering.
- **ES hero:** swapped `es-hero.png` (2.8 MB) → `es-hero.jpg` (500 KB) → attachment **3468**, repointed `es_hero_image`; old PNG (3429) now orphaned on Dev like Local.
- **Caches:** `wp page-cache flush` + `wp cdn-cache flush` + object cache flush.

**Verified over HTTP** (after flush): homepage slide 1 renders `accent accent--haas` + `btn btn--primary btn--haas`, new hero + ES art serving; 14 URLs all 200 (ES `engineered-solutions`, MCS `modification-of-standard-machine-tools`, Apps `unique-applications-for-standard-machines`, Automation `automated-system`, Training, Support, About, Careers, home); 0 broken images on the 5 new-design pages (uploads + theme-asset URLs).

**Gotchas learned (now in `.clinerules` recipe):** WPE's `scp`/sftp is disabled ("subsystem request failed"), but **rsync over ssh works**. The `~/` home is **ephemeral per-container** — a file written by one ssh connection is gone on the next (load-balanced containers), so `wp eval-file wp-content/themes/...` and absolute `~/` paths both fail; pipe scripts via `wp eval-file -` stdin instead. `wp` prints harmless parent-theme `$feature` notices at CLI (source: gerotech `functions.php`, not child). New uploads land in `uploads/2017/06/` (attachment post dates) — URLs valid, not a problem.

**Not done (unchanged):** no Production push, no DB push. Local still the seed of truth. Editorial/dev-copy decisions still client-TBD.

## 2026-09-21 — Rolled client-editable hero colours out to interior pages (opencode)

Continuation of the DSH "Review project state and improve" thread: the offer to give interior page heroes the same accent/button colour choices as the homepage carousel, accepted and pushed.

**Scope correction vs. the DSH claim of "eight pages".** Only **5** pages use the new-design hero, and only 4 have an accent word in it:

- **Accent colour** (White / Haas Red / Brand Orange, **blank = design Brand Orange**, no ACF default — same footgun-safe pattern as the homepage): ES (`es_hero_accent_color`), Applications (`app_hero_accent_color`), Automation (`ai_hero_accent_color`), Careers (`careers_hero_accent_color`). Templates now `$pick( '<prefix>_hero_accent_color', 'orange' )` → `gerotech_accent_class()`.
- **Button colour** (Brand Orange / Haas Red / White outline, blank = design): only where the hero has a CTA — ES primary (`es_hero_cta_color`) and Careers (`careers_hero_cta_color`). ES's grey secondary button stays fixed `btn--outline-white`.
- **Intentionally skipped:** **MCS** hero uses `mcs-name-split` with plain lead/main (no accent word) and no hero CTA — nothing to colour. **Training / Support / About** are legacy markup (`id="head_image"` / `.legacy`), no `.page-hero` accent spans at all.
- No DB migration needed: blank field → template renders the design (identical HTML to before). No seed-script change.

**Verified on Local.** Blank defaults render byte-identical to the previous hardcoded output: `.accent` + `btn--primary` on all four/five pages, Automation's `.mcs-name-split__main` retains `.accent`. End-to-end override test: stored `haas` on ES rendered `accent accent--haas` + `btn btn--primary btn--haas` (interior `accent--deep` section titles unaffected), then reverted clean to `.accent` / `btn--primary`. `php -l` clean on all 5 files; 13 URLs 200; both drift checks clean; repo→Local synced.

Also this session (earlier commit `94332cd`, pushed): completed the "blank select" footgun removal the DSH chat had left uncommitted, fixed the accent fallback that had drifted to `white` (would have re-broken slide 1 on a fresh DB), and **restored Local's DB after the footgun actually fired** — all six hero colour metas had been persisted `white`, rendering slide 1 accent + CTA white; re-seeded `haas`/`orange`/`orange` + `haas`/`haas` and re-verified.

**Still hardcoded:** interior section eyebrows and section-header accents; legacy Training/Support/About heroes. DSH's homepage colour fields + seed script unchanged.

## 2026-09-21 — Completed the "blank select" footgun removal; Local restored after the footgun actually fired (opencode)

**Context.** The DSH session ("Review project state and improve") offered — but the saved chat does not show executing — the footgun removal: make the hero **Accent colour / Button colour** ACF selects blank-by-default so a careless editor save can't persist ACF's injected default over the design. Found it **half-implemented in the working tree** (`front-page.php` + `inc/acf-fields.php`, uncommitted).

**Two defects found and fixed.**

1. **The accent fallback ended at `white`**, not the design colour. `front-page.php:210` fell back to `'white'`; the committed code and the seed script fall back to *slide 1 Haas red, rest orange* — and the ACF instructions promised "Leave unset to keep the design colour." On a fresh DB (Dev) that would render slide 1's accent white again. Changed the final fallback to `$is_first ? 'haas' : 'orange'` to match the seed script and the instruction text.
2. **The footgun had ALREADY fired on Local.** All six metas were literally stored `white` (`home_hero_slides_{0..2}_{accent_color,cta_color}`) — someone opened Home in the editor while the fields showed the old defaults and clicked Update, persisting `white` over the design. Verified live: slide 1 rendered `accent--white` and its CTA `btn--outline-white` instead of Haas red. Restored the design values through a wp-load bootstrap (slide 1 `haas`/`haas`, slides 2–3 `orange`/`orange`, `haas_eyebrow_color`/`haas_accent_color` = `haas`) and re-verified the served HTML: slide 1 `accent accent--haas` + `btn btn--primary btn--haas`, slides 2–3 orange, peek colours `haas`/`orange`/`orange`.

**Also corrected** the ACF placeholder labels ("White (default)" / "Brand Orange (default)" would have misled — blank means *design default*, not white/orange). Now: accent choices `White (no highlight) / Haas Red / Brand Orange`, button `Brand Orange / Haas Red / White outline`, placeholder "Design default (slide 1 red, rest orange)" on both, instructions "Leave unset to keep the design colour".

**Verified:** `php -l` clean on both files; repo → Local theme synced (`--check` identical); prototype → theme asset drift clean; all 13 Local URLs 200 (0 broken images, 0 PHP warnings, `preventive-maintenance/` 301→`/service/preventive-maintenance-plan/` is the intended redirect).

**Not committed** (working tree): the two theme edits + this journal/.clinerules update — user to confirm commit + push. Dev still needs the theme push (DSH's four commits are on `origin/master` but the theme has not been deployed to gerotechdev).

## 2026-09-21 — Pushed to origin; Local prepared for the Dev push (DSH)

**Git.** Working tree was already clean (4 commits). Nothing to merge: `origin/design-audit-revision` is fully contained in master, and the only unmerged branch — `origin/claude/file-reading-19koen` — is a single 2026-07-06 commit that master has since overtaken by **153 commits**. Left alone deliberately; merging it would reintroduce superseded design decisions. `master` was a clean fast-forward and is now **pushed**: `f7db760..995a72a`, 0 ahead / 0 behind.

**Local prepared.** Repo theme → Local re-synced and verified identical (94 files, 18 MB). `gerotech.local` healthy: 13 URLs 200, 0 broken images, 0 PHP warnings. Checked for the hung-process problem from earlier today — **no** `wpe-connect`/rsync/ssh processes and no open sockets to WP Engine, so Local Connect is clear to use.

**Added `scripts/seed-home-hero-colors.php`** (committed, previously these lived only in `/tmp`). A theme-only push carries ACF field *definitions* but not their *values*, so after such a deploy the editor shows field defaults while the front end renders via template fallbacks — they disagree. The script is idempotent and environment-agnostic: stored choice → retired `accent_class` meta → original treatment (slide 1 Haas red, rest orange). Verified by simulating a theme-only deploy on Local (deleted all five colour metas, restored the legacy `accent_class` keys, ran it — correct values, and a second run changed nothing).

**Checked live Dev to state the delta precisely** (HTTP only, no SSH). Dev is running the pre-change theme: its CSS lacks `.accent--white`, `--clr-haas-red-dark`, `.eyebrow-row--*` and `.hero-slider__peek-accent--*`, its HTML has no `data-peek-accent-color`, and **Haas red is currently broken there** (white) because the live CSS still uses the compound `.accent.accent--haas` selector. The push fixes that and adds the new colour controls.

**One gap flagged:** a files-only push will **not** update Dev's homepage hero photo — Dev's ACF image field points at `uploads/2026/09/hero-slide-01.jpg` and DB values do not travel with a theme push. Nor will the new colour *choices* be stored there, so Dev's editor will show field defaults until someone saves the slides. Both resolve by opening **wp-admin → Home → Meta Boxes → Hero slides → slide 1** and re-selecting the image, which persists every colour choice at once — or by running the seed script over SSH once the WPE gateway responds. Full commands are in `.clinerules` → "Ready to push to Dev".

## 2026-09-21 — Haas Relationship headline + eyebrow colours are client-editable (DSH)

**Request.** Same colour treatment as the hero slides, for the Haas Relationship section's eyebrow ("The Haas Relationship") and its headline ("Proud to Be Michigan's / Haas Factory Outlet").

**Done.** Two new selects in the **Haas Relationship** ACF tab, both **White / Haas Red / Brand Orange**, both defaulting to **Haas Red** to match the Figma:

- **`haas_eyebrow_color`** — colours the eyebrow text **and** its 24px rule together, via one shared custom property (`.haas-relationship .eyebrow-row { --eyebrow-accent: … }` plus `--white`/`--haas`/`--orange` modifiers) so the text and rule can never drift apart. The **unmodified default is Haas red**, so markup with no modifier still renders as designed.
- **`haas_accent_color`** — colours the `<em>` accent words through the existing `gerotech_accent_class()`.

Both fields replace hardcoded `#CF0A2C` in `.haas-relationship .eyebrow-row .eyebrow` / `.eyebrow-row__rule` with `--clr-haas-red`, and the now-redundant `.haas-relationship__headline .accent--haas` override was dropped (`.accent--haas` stands alone since the earlier fix). The prototype's Haas markup was updated for parity (`eyebrow-row--haas`, `accent accent--haas`).

**Verified end-to-end on Local** — set the fields through every combination and read back the served HTML:

| Values | Eyebrow row class | Headline accent class |
|---|---|---|
| `haas` / `haas` (default) | `eyebrow-row--haas` | `accent accent--haas` |
| **meta deleted** (un-migrated) | `eyebrow-row--haas` | `accent accent--haas` |
| `orange` / `orange` | `eyebrow-row--orange` | `accent` |
| `white` / `white` | `eyebrow-row--white` | `accent accent--white` |
| `orange` / `haas` (mixed) | `eyebrow-row--orange` | `accent accent--haas` |

The deleted-meta row matters: it proves an un-migrated database still renders Haas red, because `gerotech_field()` already falls back to the code default on an empty value. Computed styles in headless Chrome confirm text and rule match in every case (`rgb(255,255,255)` / `rgb(207,10,44)` / `rgb(243,138,44)`), including the no-modifier default of Haas red.

**Scope note.** The eyebrow modifiers are deliberately scoped to `.haas-relationship` — interior page eyebrows (`.eyebrow` elsewhere) are untouched. The Haas section is homepage-only. All 13 Local URLs still 200 with 0 broken images and 0 PHP warnings; `php -l`, CSS brace balance, JS syntax, prototype tag/reference scan and both drift checks clean.

## 2026-09-21 — Hero accent colour is now client-editable in ACF (DSH)

**Request.** Every hero slide should offer the client a colour choice for its accent word: **White (default) / Haas Red / Brand Orange** — Haas is brand red, the other slides brand orange.

**Found a real bug while wiring it.** The CSS rule was `.accent.accent--haas` (a *compound* selector requiring both classes), but the WordPress template emitted only `class="accent--haas"` from the old `accent_class` field. So **Haas red never actually applied on the site** — "Haas" in the homepage hero rendered white. The prototype looked correct only because its hand-written markup carried `class="accent accent--haas"`. The accent modifiers now stand alone and are declared after `.accent`, so one class is enough either way.

**What changed.**
- `inc/acf-fields.php`: replaced the `accent_class` select (Orange / Haas red / Deep orange) with **`accent_color`** → `White (default)` / `Haas Red` / `Brand Orange`, default `white`, with editor instructions.
- `inc/helpers.php`: new **`gerotech_accent_choice()`** (normalises any stored value — including the retired raw classes `accent`, `accent--haas`, `accent--deep` — to `white`/`haas`/`orange`) and **`gerotech_accent_class()`** (choice → CSS classes).
- `front-page.php`: hero defaults are now `accent_color` (slide 1 `haas`, slides 2–3 `orange`), and each slide emits `data-peek-accent-color`.
- `assets/js/slider.js` + `components.css`: the peek-card accent word now follows the same choice (`--white` / `--haas` / `--orange` modifiers, whitelisted in JS) instead of being hardcoded Haas red — otherwise the peek would disagree with the headline the moment the client changed the colour.
- CSS cleanup: removed the hardcoded `#CF0A2C` in favour of the existing `--clr-haas-red` token (per the "no brand colours outside tokens.css" rule).

**Migration.** `/tmp/gerotech-accent-migrate.php` reads the raw `home_hero_slides_<i>_accent_class` meta, maps it, writes `accent_color`, and deletes the stale key. Run on Local: slide 0 `accent--haas` → `haas`, slides 1–2 `accent` → `orange`; legacy keys removed.

**Verified.** Rendered HTML now emits `class="accent accent--haas"` for slide 1 and `accent` for 2–3, with `data-peek-accent-color="haas|orange|orange"`. A headless-Chrome computed-style check confirms the exact values: `.accent` → `rgb(243,138,44)`, `.accent--haas` (with **or without** the `accent` class) → `rgb(207,10,44)`, `.accent--white` → `rgb(255,255,255)`, plus both peek modifiers. A DOM dump after the slider advances shows the peek rendering `01 | [[Haas]] Factory Outlet` with `hero-slider__peek-accent--haas`. `php -l` clean; theme↔Local drift check OK.

**Correction to my earlier reasoning (found by testing, not assumed).** I originally claimed the legacy tolerance alone kept an un-migrated DB (Dev) rendering correctly. That was **wrong**: ACF **injects a field's `default_value` on read** when a repeater row has no stored value, so an un-migrated row returns `white` (accent) / `orange` (button) — never the legacy value, and never empty. Deploying that would have silently turned slide 1's Haas-red accent *and* button white/orange on Dev. Fixed by guarding both fields in `front-page.php` with `metadata_exists( 'post', $home_id, "home_hero_slides_{$i}_<field>" )` and falling back to (1) the retired `accent_class` meta read **raw** via `get_post_meta()` — it is unregistered, so it is absent from the ACF row array, which is exactly why my first attempt at this guard did nothing — then (2) the original positional treatment.

**Proof, not assertion.** I simulated an un-migrated row set on Local (restored the legacy `accent_class` meta, deleted `accent_color`/`cta_color`) and re-fetched the homepage: output was **byte-identical** to the migrated state — slide 1 `accent accent--haas` + `btn btn--primary btn--haas`, slides 2–3 `accent` + `btn btn--primary`. The migrations are idempotent and Local is back in its migrated state.

## 2026-09-21 — Hero CTA button colour is now client-editable too (DSH)

**Request.** Follow-up to the accent field: give the client the ability to change a hero slide's CTA to red.

**Done.** New per-slide **Button colour** select — **Brand Orange (default) / Haas Red / White outline** — deliberately **independent** of the accent colour, so an orange accent with a red button is possible. Field `cta_color` (`field_home_hero_cta_color`) on `home_hero_slides`, mapped by **`gerotech_btn_class()`**: `btn--primary` / `btn--primary btn--haas` / `btn--outline-white`. Seeded to preserve the existing design (slide 1 Haas red, slides 2–3 brand orange). The old logic hardcoded this from slide position (`$is_first ? 'btn--haas' : ''`), so it is now data-driven.

**Cleanup.** `.btn--primary.btn--haas` had a hardcoded `#CF0A2C` / `#b00926`; now `var(--clr-haas-red)` with a new `--clr-haas-red-dark` hover token, per the "no brand colours outside tokens.css" rule.

**Verified.** Computed styles in headless Chrome: `btn--primary` → orange bg `rgb(243,138,44)` on ink; `btn--primary btn--haas` → `rgb(207,10,44)` on white; `btn--outline-white` → transparent on white. Local: 13 URLs 200, 0 broken images, 0 PHP warnings; `php -l`, CSS brace balance, JS syntax and both drift checks clean.

**Still open.** Interior `.page-hero` sections (ES, MCS, Applications, Automation, Training, Support, About, Careers) still hardcode `accent` in their templates and have no button-colour field — these two ACF fields cover the homepage carousel slides only.

## 2026-09-21 — Review + hero/page-weight consolidation; stranded Local work rescued (DSH)

**Review findings.** The working tree, the repo theme, and the running Local site had drifted into three different answers for the same hero photo:

- Prototype `index.html` loaded `newHeroSlide.jpg` — a **7.25 MB / 4096 px** JPEG as the LCP image with `fetchpriority="high"`. `index.html` alone weighed **8.51 MB**.
- Repo `front-page.php` defaulted to `hero-campus-vans.jpg` (**1024 px** — visibly upscaled full-bleed, which is why Ken Burns had been disabled), while the Local ACF DB value overrode it anyway.
- Local held a second, never-pushed variant set: `hero-slide-1.jpg` (3 MB) + `hero-slide-1@2x.jpg` (1.3 MB, **referenced by nothing** — no `srcset` existed anywhere).
- The Local-only peek-accent work (`slider.js` + `.hero-slider__peek-accent` CSS + `front-page.php` `peek_accent`) was **inert**: no `peek_accent` ACF sub-field existed, so Local rendered `data-peek-accent=""` and the value could never be saved from the editor.
- `es-hero.png` was a **2.77 MB PNG** of a photo used as the ES page hero (already flagged in `handoff/asset-manifest.md` §6 and `qa-checklist.md`).
- `gallery-module-preview.html` pointed at two deleted placeholder videos.

**Decisions (Matt).** Adopt `hero-slide-1.jpg` + `@2x` with a real `srcset`; back-port both stranded Local changes and add the missing ACF field.

**Done — imagery.** Re-encoded from the 4096 px original into a responsive pair: `hero-slide-1.jpg` (1920×1279, 619 KB) + `hero-slide-1@2x.jpg` (2560×1706, ~1.0 MB), wired with `srcset` + `sizes="100vw"` in both the prototype and the theme. ES hero PNG → `es-hero.jpg` (500 KB, native 1671 px; no alpha, so the conversion is safe). Deleted `newHeroSlide.jpg`, `hero-campus-vans.jpg`, `es-hero.png`. `sync-theme-assets.sh --prune` removed the theme copies (correctly skipping the theme-only `legacy/*` images).

**Done — new helper.** `gerotech_image_srcset( $value, $fallback_rel )` in `inc/helpers.php`: returns WordPress attachment sizes when the ACF field holds an attachment, otherwise pairs a theme asset with its `@2x` sibling via `getimagesize()`. Returns a **raw** value — the template applies `esc_attr()`. Wired into `front-page.php` (hero slides) and `page-engineered-solutions.php`.

**Done — peek accent back-port.** Ported Local's `slider.js` / `components.css` into the prototype (source of truth) so the repo is no longer behind its own downstream copy; added `data-peek-accent="Haas"` to `index.html`; added the `peek_accent` ACF sub-field to `group_home_content`; emitted `data-peek-accent` in `front-page.php`. Ken Burns stays off (`.slide.is-active .slide__bg { animation: none }`), now recorded in both proto and theme.

**Done — a11y + broken refs.** `title_alt` now uses `! empty()` instead of `isset()`, so slides 2–3 fall back to their eyebrow rather than rendering `alt=""` (ACF repeats every sub-field, so an untouched alt is an empty string — this was silently blanking the alt on Local). Repointed the gallery preview's auto-door video at the real `assets/videos/auto-door.mp4` and dropped its sheet-metal placeholder video, matching the MCS decision.

**Local DB.** Imported the new art and re-pointed ACF: homepage hero row 1 → attachment **3467**, ES hero → **3468**, `peek_accent` = `Haas`, full alt text. Attachments **3466** (`hero-campus-vans.jpg`) and **3429** (`es-hero.png`) are now orphaned (files remain in `uploads/` — safe to delete later). No Local theme drift after `sync-theme-to-local.sh`.

**Verified.** Prototype: all 13 pages 200, **zero broken assets**; `index.html` **8.51 MB → 1.86 MB**, ES **3.05 MB → 0.54 MB** (sum of local `src` assets). Local: all 13 URLs 200, **zero PHP warnings**, **zero broken images**, both heroes serving WP-native `srcset`, and a headless-Chrome DOM dump confirmed the peek renders as `01 | [[Haas]] Factory Outlet` with the accent span once slide 2 is active. `php -l` clean on all theme files; `sync-theme-assets.sh --check` and `sync-theme-to-local.sh --check` both report no drift.

**Not done / notes.** Dev is still stale (WPE SSH gateway was hanging — not retried, per the standing instruction). Nothing committed yet. `@keyframes hero-ken-burns` is now dead CSS (kept deliberately so the animation can be re-enabled). `machine-milling-centers.png` (1.2 MB) is still uncompressed but appears only on the exploratory preview page.

**Sandbox note for future agents.** The DSH file sandbox is workspace-write, so `wp media import` and `sync-theme-to-local.sh` need wider access — they write under `~/Local Sites/…`. WP-CLI lives at `/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/wp-cli.phar`, run with Local's PHP (`.../php-8.2.29+0/bin/darwin-arm64/bin/php`) plus `-c ~/Library/Application Support/Local/run/VjZ_PwL-d/conf/php/php.ini`; `wp db` subcommands additionally need the MySQL client (`.../mysql-8.4.0+2/bin/darwin-arm64/bin`) on `PATH`.

## 2026-09-21 — Local Connect hung again (Cursor)
- After the 11:22 kill, Local Connect respawned two more `--dry-run` rsync pairs (11:22 + 11:23) to `local+rsync+gerotechdev@gerotechdev.ssh.wpengine.net` (`/sites/gerotechdev/`). Parent was Local.app PID 31871 (left running). Four ssh still `ESTABLISHED` to `34.168.124.108:22`.
- **Killed (SIGKILL):** rsync 32566, 32568, 32630, 32632 + ssh 32567, 32569, 32631, 32633. First TERM pass failed because zsh did not split the PID list.
- After kill: **no** gerotechdev/wpe-connect processes, **no** sockets to WPE `:22`. `https://gerotech.local/` still **200**. No extra rsync started. Production not touched.
- **No Connect lock file.** Chromium `~/Library/Application Support/Local/{Session Storage,Local Storage/leveldb}/LOCK` are Electron, not Connect. Connect state: `connect-events-VjZ_PwL-d.json` (old pull event).
- **Matt:** close the Connect dialog; optionally quit/reopen Local; wait several minutes; then Dev-only theme push (DB off) or use the WP Engine portal later. Do not loop retries.

## 2026-09-21 — “Can’t connect to files in Local” (Cursor)
- **Not a missing Local site.** `~/Local Sites/gerotech/app/public` is readable; child theme present; nginx/php-fpm/mysql (`VjZ_PwL-d`) running; `https://gerotech.local/` returns **HTTP/2 200**. `wpe-connect` key is `600`. Cursor can read the theme.
- **Actual issue:** Local Connect file dry-run to **gerotechdev** hung (same WPE SSH exec hang). Two overlapping rsync `--dry-run` pairs (11:19 + 11:21) via `local+rsync+gerotechdev@gerotechdev.ssh.wpengine.net`.
- **Fixed:** killed only those gerotechdev ssh/rsync PIDs. Local.app still running; site still 200.
- **Next for Matt:** close the Connect/push dialog, retry **Dev-only** theme push with **Database off**. If it hangs again, WPE gateway is still dead — do not retry in a loop; do not Production.

## 2026-09-21 — Dev deploy retry (Cursor) — SSH hung, nothing landed
- **Attempted:** theme-only rsync of `wp-content/themes/gerotech-child/` to `gerotechdev` (key `wpe-connect`, IdentitiesOnly + ConnectTimeout 20). Then WP-CLI: `page_on_front`, lineup ACF URLs, hero media `hero-campus-vans.jpg`, CTA lockup, cache flush. Never Production / never Local DB.
- **Result:** rsync produced **no file list** (~3 min hang). Killed zombies. Retry of `echo` + `wp option get siteurl` also hung with TCP ESTABLISHED and no stdout. Stopped after one retry.
- **Live Dev still stale** (`https://gerotechdev.wpenginepowered.com/` 200): hero slide 1 is `uploads/2026/09/hero-slide-01.jpg` (not campus vans); Toolroom Lathe still `toolroom-lathes.html`; View All Lathes still `/lathes`; Winner’s Circle still `haastooling.com/WINNERS_CIRCLE` (no `/p/`); CTA has cinema-lockup class but **no** `.cta-band__call` phone card (phone only in header/footer `tel:+17343797788`).
- Production not touched. Retry when WPE SSH exec works.

## 2026-09-21 — Homepage Figma items applied (Cursor)
- **Slide 1 bg:** `assets/images/hero-campus-vans.jpg` (Gerotech/Haas vans + F1 Team sign) in proto `index.html` + `front-page.php`. Imported to Local media (attachment 3466) and set on homepage ACF `home_hero_slides` row 1. Alt: “Gerotech and Haas F1 Team vans at Gerotech headquarters”.
- **Stats band:** `.stat-counter__grid--2` centered (`max-width: 720px; margin-inline: auto`; items text-align center).
- **Mill UMC-750 crop:** `.machine-panel__photo--umc img { object-position: center 18%; }` so the top of the lineup photo is no longer clipped.
- **CTA phone lockup restored** on homepage (user reversed no-lockup): Prefer to talk it through? / (734) 379-7788 / Talk to a person, not a form. `tel:+17343797788`. ACF `cta_call_*` fields populated on Local page 11. Mailing list kept.
- Synced `./scripts/sync-theme-assets.sh` then `./scripts/sync-theme-to-local.sh`. Verified proto `:8080` + `https://gerotech.local/` (hero vans, centered stats, mill crop, CTA call card + mailing list).
- **Dev blocked:** WPE SSH authenticates and accepts `exec`, then hangs (no rsync, no ACF, no cache flush). Do not push Production. Retry deploy when SSH responds.

## 2026-09-21 — Homepage Figma comments (Cursor)
- Pulled 283 file comments. Current handoff `7306:1063` has **zero** pins. Unresolved homepage threads are on `7196:2420` (22), `7306:520` (6), slide `7329:2900` (3), plus deleted lineup frames `7283:*`.
- **Already in proto/handoff:** Haas-red hero, mill chip links, Haas Automation tab, Ford/Kingbury quotes, proud-to-serve lede, CTA copy, UMC-750 mill photo, no phone lockup.
- **Done this session:** aligned leftover Haas URLs to Tristien's comment links (lathe toolroom/dual-spindle/view-all, automation models/pallets/bar feeders/cobots/view-all, Winner's Circle `.../p/WINNERS_CIRCLE-1Y`) in `index.html` + `front-page.php` defaults.
- **Blocked:** comment attachments aren't in the Figma comments API — still need the client files for mill/turning/rotary/automation/hero swaps. **Don't guess:** mailing-list removal (#203) and CTA phone box (#241) conflict with `7306:1063`.
- WP ACF DB still wins over `front-page.php` defaults until those URL fields are edited or re-seeded. Not deployed.

## 2026-09-21 — Sync lineup URLs to Local (Cursor)
- Ran `./scripts/sync-theme-to-local.sh`.
- Updated homepage ACF `lineup_panels` on Local (page 11) so turning/automation/tooling URLs match proto. Verified on `https://gerotech.local/`. Dev not pushed.

## 2026-09-18 — ES pages: remove remaining stubs (opencode)
- **Goal:** make the 4 Engineered Solutions pages fully ready (leaving only header/mega-nav work).
- **Applications:** replaced the two `Content coming soon.` modal bodies (Fire Suppression, RFID) with real copy in `page-unique-applications-for-standard-machines.php`.
- **MCS:** removed the last placeholder video (`placeholder-sheet-metal.mp4`) from the Sheet Metal collection (proto `machine-custom-solutions.html` + `page-modification-of-standard-machine-tools.php`) and deleted the file from prototype + theme + Dev. The Auto Door collection keeps its real video.
- Re-seeded ACF on Local + Dev so the DB reflects the new copy / removed video.
- **Verified on Dev:** all 4 ES pages 200, no PHP warnings, **zero broken images**, no `Content coming soon` / placeholder-video references. Collections: MCS 7, Applications 8, Automation 7.
- **Still external (cannot be code-completed):** stand-in photography (Unsplash/theme assets, now self-hosted), FANUC ASI seal usage rights, and ES news story links (no News page yet).

## 2026-09-18 — Seed ACF content into the DB (Local + Dev) (opencode)
- **Request (option B):** pre-fill the ACF fields with the current content so editors see/edit real values, on Local **and** Dev.
- **Method:** added a capture hook to `gerotech_field()` (`inc/helpers.php`) — when `$GLOBALS['gerotech_capture_defaults']` is an array, every read records its code default. A one-time seeder renders each page template with the hook on, then `update_field()`s the captured defaults onto the page. Avoids retyping defaults (single source of truth = the templates). `front-page.php` was switched from its bespoke `$acf_get` closure to `gerotech_field( $key, $default, $home_id )` so its defaults are captured too.
- **Images:** ACF image fields only accept attachment IDs, and seeding theme-relative strings zeroed them. The seeder now imports each image source into the **media library** (theme assets copied; Unsplash stand-ins fetched via `curl` — Local blocks `WP_HTTP_BLOCK_EXTERNAL`), de-duped by a `_gerotech_src` meta, and stores the attachment ID.
- **Seeded:** 15/58/21/17/21/32/15/11/17/9/9/12/5 fields across Home, ES, MCS, Applications, Automation, Careers, Training, Support, Service, Rotary, Planned, About, Contact + 3 global testimonials.
- **Verified:** Local + Dev `get_field()` return real values/attachment URLs; front-end image counts unchanged (home 13, ES 9, careers 5, MCS 19) with **zero broken images**.
- **Consequence:** DB values now override code defaults — a template-default change won't show until the field is updated. Re-seed documented in `.clinerules`.
- Committed + pushed. Temp diagnostic admin users removed from Local + Dev.

## 2026-09-18 — ACF groups never rendered (missing `post_name` location rule) — fixed (opencode)
- **Symptom:** editors saw "nothing to edit" on Home / ES / legacy pages; the block editor's Meta Boxes panel showed only the parent theme's old groups (e.g. “Home Options”).
- **Root cause:** our ACF groups for ES/MCS/Applications/Automation, all 7 legacy pages, and Careers are located by `post_name == <slug>`. **ACF has no built-in `post_name` location rule**, so those groups were registered but never matched a location — they never rendered and their values were never saveable. The templates kept rendering because they use code defaults, which hid the bug. (`group_home_content` worked only because it uses `page_type == front_page`.)
- **Fix 1 — register the rule:** added `acf/location/rule_types`, `acf/location/rule_values/post_name`, `acf/location/rule_match/post_name` filters at the top of `inc/acf-fields.php`. Verified on Local + Dev: `acf_get_field_groups(['post_id'=>N])` now returns our group for all 13 pages (11, 28, 1250, 1265, 1239, 18, 39, 1535, 20, 1484, 1538, 14, 16).
- **Fix 2 — clean editor:** `functions.php` `gerotech_hide_legacy_field_groups()` (`acf/load_field_groups`, admin only) removes the parent theme's DB groups (Home Options, Page Options, ES/About/Training/Service Options, Rotary/Planned, Contact) on those 13 pages, so the Meta Boxes panel shows only our “— Content” group. Verified by fetching the real `/wp-admin/post.php?post=…&action=edit` HTML with a temporary admin user (since deleted).
- **Note:** in the block editor the fields are in the collapsible **“Meta Boxes”** panel at the bottom — not the main canvas.
- Deployed to Dev, caches flushed. Temp admin users removed from Local + Dev.

## 2026-09-18 — Full proto ↔ Local ↔ Dev audit + fixes (opencode)
- **File parity:** prototype `assets/` == repo theme == Local theme == Dev theme (checksum, zero drift).
- **DB parity:** Local and Dev page lists + `_wp_page_template` metas are identical (30 published pages, same slugs/IDs).
- **Rendered parity:** headless-Chrome audit (1440px) of 13 URLs on Local + Dev + prototype — **Local == Dev** for every page (section class sequences, image counts, broken images, console errors). Prototype == Local for Homepage, ES, MCS, Applications, Automation, Careers (sections + visible text identical).
- **Fixed — Google Maps:** `DeletedApiProjectMapError` (dead dev API key) on `training` + `contact` → replaced with **keyless iframe embeds** from the ACF locations repeater. 0 console errors now.
- **Fixed — hero eyebrow drift:** prototype MCS / Applications / Automation still had `<p class="slide__eyebrow">` that the WP templates had removed (client request). Removed from the prototype so the source of truth matches.
- **Fixed — `machine-modification` 404:** added a 301 → `/modification-of-standard-machine-tools/` in `functions.php` (`gerotech_legacy_redirects()`), matching the prototype canonical redirect.
- **Known intentional divergences (not changed):**
  1. **Training / Support / About** WP pages use the **legacy dev body** (`training.html` / `support.html` / `about.html` in the prototype are the new design). This was a deliberate earlier client request ("our header/footer + dev body").
  2. **WP Support nav is a dropdown** (Service Request Forms / Rotary Repair / Planned Maintenance); prototype Support is a plain item.
  3. Page `<title>`s come from the SEO plugin ("… - Gerotech, Inc.") vs prototype placeholders ("… — Gerotech").
  4. Unconverted Dev URLs (`machines`, `news-and-events`, leftover ES child slugs) still render parent templates — no prototype exists.
- **Link check:** 67 internal links crawled on Dev — all 200 (no 404s).
- All fixes synced to Local + deployed to Dev, caches flushed.

## 2026-09-18 — Peek hero grows with content (min-height 500px) (opencode)
- **Request:** "hero slider doesn't change vertical height based on content — see prototype."
- **Finding:** prototype and Dev were actually identical — both hard-locked to `height: 500px` on desktop (`components.css` + `elevated.css`), with `.slide.is-active { overflow: hidden }`, so longer copy would clip rather than expand. (The 2026-09-18 Figma-alignment session had changed the earlier `height: auto` to a hard 500px.)
- **Fix (prototype assets, then synced):** desktop `.hero-slider--peek`, `.hero-slider__track`, `.slide.is-active` and `.slide__content--left` now use `height: auto; min-height: 500px` (removed `overflow: hidden` / `max-height: 500px`). Short slides stay 500px; longer content expands the hero. Set in **both** `components.css` and `elevated.css`.
- **Verified via headless CDP** at 1440px: prototype and Dev both 500px normally, and both grow to **767px** when extra body copy is injected. Peek band stays pinned to the bottom.
- Synced `sync-theme-assets.sh` + `sync-theme-to-local.sh`, deployed to Dev, flushed page + CDN cache.
- Docs updated: `.clinerules`, `AGENTS.md` (CLAUDE.md symlink), `design-spec.md`, `cline-project-handoff.md`.

## 2026-09-18 — Dev DB fix: parent page templates were overriding the child (opencode)
- **Symptom (reported via screenshot):** `/engineered-solutions/` on Dev rendered the **parent** theme's hero/body ("Collaborative, cutting-edge solutions." / "It starts with our approach."), not our template.
- **Cause:** Dev's `wp_postmeta` still had `_wp_page_template` pointing at parent templates — the 4 ES pages (IDs 28, 1250, 1265, 1239) at `page-solutions.php` and support (1535) at `page-service-home.php`. A page's assigned template overrides our slug templates. The Local DB had these cleared (2026-09-16) but Dev's was never updated (theme-only pushes).
- **Fix:** `wp eval-file` on Dev → `delete_post_meta` for IDs 11, 14, 16, 28, 39, 1239, 1250, 1265, 1535; `update_post_meta(18, '_wp_page_template', 'default')`. Mirrors Local. Flushed page + CDN cache.
- **Verified on Dev:** `/engineered-solutions/` now shows "Your Manufacturing …" (our template), `why-section` present, parent copy gone; MCS/Applications/Automation render our design + gallery collections; `/support/` renders our `.legacy` body. All 200, no PHP warnings.
- **Note:** `service`, `rotary-repair`, `planned-maintenance` intentionally keep metas whose filenames match our child templates. Re-apply this fix if Dev is re-pulled/reset.

## 2026-09-18 — Phase 2 ACF on legacy pages + Careers page live on Dev (opencode)
- **Request:** "do it all" — finish the WP Engine move: reCAPTCHA domain, Phase 2 ACF, unconverted Dev URLs, git push.
- **Phase 2 ACF (done):** new `inc/acf-legacy-fields.php` (required from `functions.php`) registers 7 groups by `post_name`: `group_training_content`, `group_support_content`, `group_service_content`, `group_rotary_content`, `group_planned_content`, `group_about_content`, `group_contact_content`. Wired the matching templates (`page-training.php`, `page-support.php`, `page-service.php`, `rotary-repair.php`, `preventive-maintenance.php`, `page-about.php`, `page-contact.php`) to read ACF with the current markup as **defaults** (zero visual regression). Editable: heroes, intros, repeaters (service columns, training courses, contact departments/locations, training locations), CTA. **Hardcoded:** CF7 shortcodes, Google Maps JS, the PM inspection checklists, tab chrome.
- **Careers (done):** the prototype `careers.html` had no WP template. Built `page-careers.php` (new design system, `page-hero-trust` + `careers-table` + benefits cards + CTA + mailing list) and added `group_careers_content` to `inc/acf-fields.php`. Live at `/careers/`.
- **Deploy:** direct `rsync` over the Local WPE key (`~/Library/Application Support/Local/ssh/wpe-connect`, `gerotechdev@gerotechdev.ssh.wpengine.net`, `/nas/content/live/gerotechdev/wp-content/themes/gerotech-child/`), then `wp page-cache flush` + `wp cdn-cache flush`. Verified all 8 pages 200 and PHP-warning-free on Dev; `acf_get_field_groups()` confirms all 11 of our groups registered.
- **reCAPTCHA (blocked):** CF7 site key `6LcOOuoaAAAAADRxx65d0pb_BvgWG9d9e5aqE9k6` (in the `wpcf7` option) does not allow `gerotechdev.wpenginepowered.com`. Only the Google reCAPTCHA account owner can add the domain — no credential/API here. Documented for the client.
- **Unconverted Dev URLs (deferred, needs direction):** `machines`, `news-and-events`, and the leftover ES child slugs have no prototype; building them would be guessing. Left as-is (parent templates + our header/footer) and documented in `.clinerules`.
- **Git:** committed + pushed to `origin/master`.

## 2026-09-18 — Dev theme verified in sync; F1 PNG 403 fixed (opencode)
- **Request:** "finish moving to WP Engine" (scope: push latest child theme to Dev).
- **Finding:** the theme was **already fully deployed**. A checksum rsync (`rsync -avnc`) of `wp-content/themes/gerotech-child/` → `gerotechdev` showed **zero content drift** across all 104 files. Dev runs `gerotech-child` active; `/`, `/engineered-solutions/`, `/support/`, `/contact/` all 200.
- **Only drift:** `assets/images/haas-f1-lockup.png` was `chmod 600` on Dev (the known 403) vs `644` in the repo. Fixed to `644` → now serves **200**.
- **Direct deploy path discovered:** SSH key `~/Library/Application Support/Local/ssh/wpe-connect`, user `gerotechdev@gerotechdev.ssh.wpengine.net`, theme at `/nas/content/live/gerotechdev/wp-content/themes/gerotech-child/`. Enables `rsync`/`wp` (WP-CLI at `/usr/local/bin/wp`) without the Local GUI push.
- **Left open (unchanged):** reCAPTCHA Dev domain, Phase 2 ACF on legacy pages, unconverted Dev URLs. Repo is still 1 commit ahead of `origin/master` (`4e5ed7f`).

## 2026-09-18 — Pickup notes for next agent (Cursor)
- **Git:** local `master` @ `4e5ed7f` (pickup docs). Origin still `ba259ca` until `git push origin master`. `.clinerules` session pointer may be 1 line ahead of that commit.
- **Dev:** child theme active at https://gerotechdev.wpenginepowered.com/ . Theme-only push (no DB). Rollback = Stage copy of Dev. Never Production.
- **Homepage:** Figma `7306:1063`. F1 image `haas-f1-team.jpg`. Do not use `haas-f1-lockup.png` on WP Engine.
- **Next work:** (1) reCAPTCHA Dev domain on Google key, (2) Phase 2 ACF on legacy pages, (3) unconverted Dev URLs still parent+child-CSS. Pickup block is at the top of `.clinerules`.

## 2026-09-18 — Haas F1 lockup JPEG for WP Engine Dev (Cursor)
- Dev 403’d `haas-f1-lockup.png` (file was `chmod 600`; Cloudflare HTML 403). Other theme images 200.
- Added `assets/images/haas-f1-team.jpg` (644) and pointed proto `index.html` + `front-page.php` default at it. Pushed that JPEG + `front-page.php` to `gerotechdev`; lockup now renders.
- reCAPTCHA “Invalid domain for site key” remains on Dev — add `gerotechdev.wpenginepowered.com` to the Google key; not a theme bug.

## 2026-09-18 — All agent docs synced to homepage Figma handoff (Cursor)
- Pointed Cline/Cursor/Claude/Copilot at the same homepage facts: Figma home **`7306:1063`**, 500px peek hero, peek titles, `cta-home-figma.jpg`, Haas intro-only (no features band), proto nav hrefs, WP Support dropdown kept.
- Files: `.clinerules`, `AGENTS.md`, `CLAUDE.md`, `.cursor/rules/gerotech-agent-sync.mdc`, `design-spec.md`, `cline-project-handoff.md`, `.github/copilot-instructions.md`. Code still uncommitted.

## 2026-09-18 — Homepage aligned to Figma handoff 7306:1063 (Cursor)
- **Peeks:** 02 `In-Stock & Ready`, 03 `Automation for Michigan` (kept slide 2 headline “Ready To Ship”).
- **Hero:** desktop peek carousel locked to **500px** (`components.css` + `elevated.css` override). Peek tiles **107px** tall.
- **CTA:** Figma photo saved as `assets/images/cta-home-figma.jpg` (homepage only).
- **Proto nav:** Training / Support / About now link. WP Support dropdown left as-is (live IA).
- Synced theme + LocalWP. Hero measures 500px; peeks + CTA src verified on proto and `gerotech.local`.

## 2026-09-18 — Homepage Figma vs proto vs LocalWP review (Cursor)
- **Request:** compare homepage to Figma canvas `6573:406` (`home` frame `7306:1063`, Dev handoff).
- **Match:** section order, alert bar phones, Get a Quote, Haas intro (watermark + F1 lockup, no 4-col features), stats 39+ / 14,000, lineup tabs + mill chips, 3 testimonials, CTA copy (no phone lockup), mailing list, footer.
- **Copy drift vs Figma peeks:** 02 title is `Ready To Ship` (Figma: `In-Stock & Ready`); 03 title is `Your Shop Floor` (Figma: `Automation for Michigan`). Slide 2 full headline is the later client line, not shown as the active slide in this Figma frame.
- **Layout:** Figma hero is 500px; live peek hero grows with padding/copy (~630–690px). CTA still Unsplash stand-in vs Figma robot-arm photo.
- **Proto vs WP:** WP Support is `+` dropdown (Figma + proto Support is a plain item); WP Training/Support/About are linked; proto those three have no `href`. WP-only: reCAPTCHA badge + Localhost site-key overlay covering Haas F1 lockup.
- No code changes.

## 2026-09-18 — Hero slide 2 headline → “Our Showroom Machines Are Ready To Ship” (Bionic)
- **Request:** change the second hero slider headline to “Our Showroom Machines Are Ready To Ship”.
- `index.html` (prototype) + `front-page.php` (ACF default): removed the “Are For Sale!” line; headline is now “Our Showroom Machines / Are `<em>Ready To Ship</em>`” with “Ready To Ship” kept as the orange accent.
- **Verified:** `php -l` clean; synced to Local; homepage renders the new headline (no “Are For Sale”).

## 2026-09-18 — Testimonials wired to a global ACF repeater (Bionic)
- **Request:** make the testimonials section editable (Option A from the text/images audit — only testimonials; leave icons/alt-text/aria-labels/breadcrumbs/email-form internals hardcoded).
- **`inc/acf-fields.php`:** added an ACF **options page** (`acf_add_options_page`, menu slug `gerotech-site-content`, title “Site Content”) + field group `group_site_testimonials` located on that options page (`options_page == gerotech-site-content`). Fields: `testimonials_eyebrow` (text), `testimonials_title` (textarea, `<em>` accent), `testimonials` (repeater of `quote`/`name`/`sub`).
- **`template-parts/sections/testimonials.php`:** now reads the three fields from `'option'` via `gerotech_field()` with the v1 static quotes as defaults, so it renders unchanged until an editor saves the repeater. Section is shared across homepage + all 4 ES pages, so it edits once site-wide.
- **Verified:** `php -l` clean on both files; `sync-theme-to-local.sh`; all 5 URLs 200; fallback quotes/eyebrow/title render on `engineered-solutions`. Options page + field group use the same proven `acf_add_local_field_group()` mechanism (headless wp-admin re-verify still blocked by the Local admin password).

## 2026-09-18 — Orange “Controls Solutions” accent on Automation hero (Bionic)
- **Request:** make “Controls Solutions” orange in the Automation hero headline.
- `page-automated-system.php`: `ai_hero_main` default → `and <em>Controls Solutions</em>`; headline main rendered via `gerotech_accent( $hero_main, 'accent' )`; breadcrumb uses `strip_tags()` so it stays plain text. Verified locally (headline shows `<span class="accent">Controls Solutions</span>`).

## 2026-09-18 — Removed hero eyebrow from 3 ES detail pages (Bionic)
- **Request:** remove the hero eyebrow (`.slide__eyebrow`) from MCS, Application, and Automation pages.
- Removed the `<p class="slide__eyebrow">` line + the now-unused `$hero_eyebrow` variable in `page-modification-of-standard-machine-tools.php`, `page-unique-applications-for-standard-machines.php`, `page-automated-system.php`. ES hub page left as-is (not requested). ACF `*_hero_eyebrow` fields left in place (harmless, reversible).
- **Verified:** `php -l` clean; synced to Local; all 3 URLs 200 with `slide__eyebrow` count 0.

## 2026-09-18 — ACF wired onto the 4 Engineered Solutions pages (Bionic)
- **Request:** get ACF editing working on the four “new” ES pages in Local, following the homepage ACF pattern (fields registered in PHP, templates read with defaults). Legacy pages deferred (user-approved).
- **`inc/helpers.php`:** added `gerotech_field( $key, $default, $post_id )` (null-safe ACF read) and `gerotech_parse_media( $text )` (gallery-collection media list → `type|src|poster|alt|caption` items).
- **`inc/acf-fields.php`:** appended **4 field groups** located by `post_name == <slug>` (not `page_template`, since these pages use slug hierarchy): `group_es_content` (`engineered-solutions`), `group_mcs_content` (`modification-of-standard-machine-tools`), `group_application_content` (`unique-applications-for-standard-machines`), `group_automation_content` (`automated-system`). Prefixes `es_*` / `mcs_*` / `app_*` / `ai_*`.
- **Wired 4 templates** to read ACF with the current static content as defaults: `page-engineered-solutions.php`, `page-modification-of-standard-machine-tools.php`, `page-unique-applications-for-standard-machines.php`, `page-automated-system.php`.
- **Decisions:** MCS/Automation headlines use `lead` + `main` fields for `.mcs-name-split`; ES/Application use a single headline field with `<em>` accent (rendered via `gerotech_accent()`). Card modal detail is a `wysiwyg` field; the **“Talk to an Engineer” CTA is auto-appended** by the template (not stored). Gallery collections = repeater of `title`/`meta`/`media` (flat pipe-delimited textarea — no nested repeaters).
- **Verified:** `php -l` clean on all 6 edited files; `sync-theme-to-local.sh`; all 4 URLs 200 with correct defaults (accent spans, card counts 8/5/8, gallery collections 7/8/7, `<details open>` preserved through `wp_kses_post`).
- **Note:** admin field-group list could not be re-verified headlessly — the Local admin password (`localpass123`) no longer authenticates (“The password you entered…”). Registration uses the same proven `acf_add_local_field_group()` mechanism as the homepage group, so groups should appear once logged in.
- **Deferred (user-approved):** legacy pages (Training, Support, Service, Rotary Repair, Planned Maintenance, About, Contact) — Phase 2.

## 2026-09-18 — Gallery module promoted to ES detail pages (Bionic)
- **Request:** promote the standalone gallery module (collections + video) and convert the three ES detail pages (MCS, Application, Automation) from the flat one-photo-per-card gallery to the new collections style. Keep the video placeholders (client will supply real footage later).
- **Prototype HTML:** `machine-custom-solutions.html` (13 cards → 7 collections, incl. 2 placeholder videos), `application.html` (8 cards → 8 collections, no video), `automation-integration.html` (7 cards → 7 collections, no video). Each page now loads `gallery-module.css` + `gallery-module.js` (kept `modal.js` for the `.mcs-modal` detail cards). Unsplash `data-src` bumped `w=500` → `w=1600` for the viewer. Tag-balance verified (0 errors).
- **Theme:** ported the same markup into `page-modification-of-standard-machine-tools.php`, `page-unique-applications-for-standard-machines.php`, `page-automated-system.php` (PHP URI prefix on `src`/`data-src`/`data-poster`). `inc/enqueue.php` now enqueues `gallery-module.css` site-wide and `gallery-module.js` on the modal pages.
- **Sync script:** `scripts/sync-theme-assets.sh` now syncs `gallery-module.css`, `gallery-module.js`, and `assets/videos/**` (with `--prune` support). Ran it + `sync-theme-to-local.sh`.
- **Bug fixed (pre-existing):** `inc/enqueue.php` `$modal_pages` used prototype slugs (`machine-custom-solutions`, etc.) but the pages have dev slugs since the dev pull — so `modal.js` (and the new gallery JS) silently stopped loading. Updated to the actual WP slugs (`modification-of-standard-machine-tools`, `automated-system`, `unique-applications-for-standard-machines`).
- **Verified:** all 3 local URLs 200; `gallery-module.js` + `modal.js` + `data-gallery` present; video assets 200. `php -l` clean on all edited PHP.
- **Follow-up:** gallery-module-preview.html still exists as the standalone preview (not in nav).
- **Client video added (2026-09-18):** `Vertical Auto Door Video.mov` (Downloads) remuxed → `assets/videos/auto-door.mp4` (H.264/AAC, faststart) and wired into the Auto Door Integration collection (prototype + `page-modification-of-standard-machine-tools.php`). `placeholder-auto-door.mp4` removed. Sheet Metal video still `placeholder-sheet-metal.mp4`. **Also fixed `sync-theme-assets.sh --prune`** to skip theme-only `assets/images/legacy/**` (it had wrongly deleted 8 legacy page images; restored via git).

## 2026-09-16 — Nav: drop Contact, point Get a Quote at /contact/ (opencode)
- **`header.php`:** removed the **Contact** item from the desktop `.site-nav` and the mobile `.mobile-nav`. The **Get a Quote** button (desktop `.btn-get-quote` + mobile link) now links to **`/contact/`** via `gerotech_page_link( 'contact' )` instead of the `mailto:`. Desktop nav is now Machines · Engineered Solutions · Training · Support · About · Get a Quote.
- Search modal quick links never had Contact — unchanged. `gerotech_quote_mailto()` still used by the ES mega-menu + mobile "Talk to an Engineer".
- **Verified:** rendered nav + Get a Quote href on local `/support/`.

## 2026-09-16 — Contact page port from live (opencode)
- **Request:** `https://gerotech.com/contact/` (live) should be the contact page. Live vs dev differ **only** in the email domain — live uses the real `@gerotech.com`, dev uses `@gerotechdev.wpenginepowered.com`. Ported from **live**.
- **`page-contact.php` (replaced):** this **replaced the earlier custom design-system build** (`.contact-layout` / `.contact-form` / `.contact-group`) with the live/dev legacy design, wrapped in `.legacy` — "Contact" title, intro band, the Contact Form (**CF7 60**, swapped to shortcode), department details (Headquarters & Sales / Engineering & Automation / Service / Parts / Tooling / Grand Rapids Office), and Locations with Google Maps.
- **Enqueue:** `legacy.css` + parent `fonts.css` now on **support, service, training, rotary-repair, planned-maintenance, about, contact**.
- **Verified:** local `/contact/` desktop matches live. Maps key overridable via `gerotech_google_maps_key`.
- **Note:** the now-unused `.contact-*` CSS remains in `components.css` (harmless).

## 2026-09-16 — Support nav dropdown (opencode)
- **Request:** keep the nav hardcoded; make Support a dropdown of its subpages **excluding Training**.
- **`header.php`:** Support is now `.site-nav__item.has-dropdown` (desktop, using the existing `.dropdown` / `.dropdown__link` pattern + `+` arrow) and a `.mobile-nav__group` `<details>` (mobile). Items: **Service Request Forms** (`/service/`), **Rotary Repair** (`/rotary-repair/`), **Planned Maintenance** (`/planned-maintenance/`). Training left as its own top-level link.
- **`inc/helpers.php`:** added `service`, `rotary-repair`, `planned-maintenance` to the slug→URL map.
- **Verified:** dropdown renders under Support on `/support/`; links resolve locally; mobile group present.
- Nav stays hardcoded (editable WP menus remain a retainer item, deferred until the IA locks).

## 2026-09-16 — About page port (opencode)
- **Request:** `gerotechdev.wpenginepowered.com/about/` should match dev, with our header/footer.
- **`page-about.php` (overwritten):** local page 14 has no `_wp_page_template` (slug hierarchy → `page-about.php`), so the existing prototype-based child template was replaced with the dev body — hero "ABOUT GEROTECH / Your source for advanced manufacturing solutions.", "Since 1987…" intro (orange accent span), **Our People** + image, **Our Facilities**, Engage CTA.
- **Images bundled** into `assets/images/legacy/`: `h_about.jpg`, `bkg_about_mobile.jpg`, and the people image (`about-people.jpg`, copied from the parent theme's `images/about_people_2.jpg` so it no longer depends on the parent theme path).
- **Enqueue:** `legacy.css` + parent `fonts.css` now on **support, service, training, rotary-repair, planned-maintenance, about**.
- **Verified:** local `/about/` desktop matches dev.

## 2026-09-16 — Planned Maintenance page port (opencode)
- **Request:** `gerotechdev.wpenginepowered.com/planned-maintenance/` (sub-page of Support) should match dev, with our header/footer.
- **`preventive-maintenance.php` (new):** local page 1538 has `_wp_page_template = preventive-maintenance.php` (parent template), so the child override uses that filename. Ported the dev body — hero "PLANNED MAINTENANCE / We are here to help.", intro + **PM Flyer** PDF button, the **Planned Maintenance form** (CF7 **321**, swapped to shortcode — Contact Information, Product Identification, Maintenance Schedule Comments, Purchase Order), Engage CTA.
- **Reused** `assets/images/legacy/h_service.jpg` + `bkg_service_mobile.jpg` (same hero as Support) — no new images.
- **Fixed:** static `mailto:service@gerotechdev.wpenginepowered.com` → **`service@gerotech.com`** (the CF7 form 321 body still carries the dev address — DB, flagged). Stripped `onClick="ga(...)"`; dev links → root-relative. PM Flyer PDF resolves locally (`/wp-content/uploads/2021/07/…`).
- **Enqueue:** `legacy.css` + parent `fonts.css` now on **support, service, training, rotary-repair, planned-maintenance**.

## 2026-09-16 — Rotary Repair page port (opencode)
- **Request:** `gerotechdev.wpenginepowered.com/rotary-repair/` (sub-page of Support) should match dev, with our header/footer.
- **`rotary-repair.php` (new):** ported the dev body — hero "ROTARY REPAIR / Gerotech offers world class repair of your HAAS rotary table.", intro, the **Rotary Repair form** (CF7 **299**, swapped to shortcode — includes Product Identification, serial-number guide, cable-connector radios, alarms, PO upload), Engage CTA.
- **Template naming gotcha:** local page 1484 has `_wp_page_template = rotary-repair.php` (the **parent** theme's page template). Child templates only override a same-named file, so the child file must be **`rotary-repair.php`** (not `page-rotary-repair.php`). Renamed accordingly.
- **Cleanup:** fixed the dev template's malformed `<div class="service_tab" id="tf_rotary" class="clearfix>` (unclosed quote); stripped the `onClick="ga(...)"` analytics handlers (undefined `ga` would throw); dev-domain links → root-relative; hero image optimized 1.1 MB → 300 KB (`assets/images/legacy/rotary-hero.jpg`).
- **Enqueue:** `legacy.css` + parent `fonts.css` now on **support + service + training + rotary-repair**.
- **Note (pre-existing):** `/service/rotary-repair/` (page 194) is assigned `page-service.php`, so it renders the Service page content — inherited from the dev DB, unchanged by this work.

## 2026-09-16 — Training page port (opencode)
- **Request:** `gerotechdev.wpenginepowered.com/training/` should be the training page (our header/footer + dev body).
- **`page-training.php` (rewritten):** ported the dev body — hero "TRAINING / Sharpen your skills." (desktop + mobile), "View all upcoming training sessions" button, **Training Opportunities** (8 `.quad` course cards), **Upcoming Training Sessions**, **Custom Training** rich-text block, **Locations** (Flat Rock + Grand Rapids with **Google Maps**), Engage CTA. No CF7 forms on this page.
- **Localized:** dev-domain links → root-relative (all verified 200: `/training/lathe-operator/`, `/training/mill-operator/`, `/scheduled-training/`, `/training/custom-classes/`, `/contact/`); hero images bundled in `assets/images/legacy/` (`h_training.jpg`, `bkg_training_mobile.jpg`).
- **Maps key:** the dev Google Maps key is kept as the default but is now overridable via the **`gerotech_google_maps_key`** filter — replace before production.
- **Enqueue:** `legacy.css` + parent `fonts.css` now load on **support + service + training**.
- **Verified:** local `/training/` desktop matches dev (hero, 8 cards, custom-training, both maps render).

## 2026-09-16 — Service page port + generalize legacy scope to `.legacy` (opencode)
- **Request:** same treatment as Support for `gerotechdev.wpenginepowered.com/service/` — our header/footer, dev body.
- **`page-service.php` (new):** ported the dev body programmatically — isolated `#meat` → `<footer`, stripped comments/scripts, and **replaced each rendered CF7 form with its shortcode** so submissions work (forms 317 Service Request, 500 General Service Inquiry, 322 Parts Order, 299 Rotary Repair, 321 Planned Maintenance, 300 Application Support). Includes the 5-column "World Class Service" block, tab nav, 6 tabbed panels, hidden Locations section, and the Engage CTA.
- **Generalized scope:** regenerated the scoped parent CSS as **`assets/css/legacy.css`** with prefix **`.legacy`** (was `legacy-support.css` / `.legacy-support`); deleted the old file; updated `page-support.php` to `.legacy`.
- **New `assets/js/legacy-tabs.js`** — vanilla replacement for the parent's dequeued jQuery `.to`/`data-tab` tab switcher; enqueued on the service page only.
- **Enqueue:** `inc/enqueue.php` loads parent `fonts.css` + `legacy.css` on **support + service**; `legacy-tabs.js` on service.
- **Verified:** local `/service/` and `/support/` render our header/footer + dev bodies; service desktop matches dev (5-col block, tabs, form 317 fields, engage). Radios render fine (earlier concern was a screenshot-scaling artifact).
- **Follow-up flagged:** the service CF7 form bodies contain **`@gerotechdev.wpenginepowered.com`** addresses (32 refs) — inherited from the dev DB, matches dev, but must be corrected before production.

## 2026-09-16 — Support page: dev body + scoped legacy CSS; repo→Local sync (opencode)
- **Request:** Support page must use our new header/footer but otherwise match `gerotechdev.wpenginepowered.com/support/`.
- **Method:** ported the dev page's visible body (`#head_image` desktop/mobile hero → `.bkg_grey_shapes` intro → `.bkg_white.service_content` 5× `.fifth` columns + "Need Assistance?" → `#engage` CTA) into `page-support.php`, wrapped in `.legacy-support`. Generated `assets/css/legacy-support.css` by prefixing the parent `gerotech/style.css` (3781 lines) with `.legacy-support` via PostCSS — so the parent CSS styles the body but **cannot leak** into our header/footer. `body`/`html` selectors remapped to `.legacy-support` so base typography applies. Hero images downloaded to `assets/images/legacy/`.
- **Enqueue:** `inc/enqueue.php` loads parent `fonts.css` (Replica) + `legacy-support.css` on the support page only.
- **Note:** the dev support page is **not mobile-responsive** (fixed `.row`/`.row_970` widths overflow <1024) — our port matches it faithfully. Flagged for a decision.
- **Root-cause fix:** discovered **two divergent child-theme copies** — the repo and the running LocalWP site. Repo was ahead (`components.css` +158 lines); Local had a redundant root `helpers.php`. Synced repo → Local and added **`scripts/sync-theme-to-local.sh`** (`--check` for drift). Documented the full chain in `.clinerules` + `AGENTS.md`: prototype assets → repo theme → Local site.
- **Verified:** local `/support/` renders our header/footer + dev body; desktop matches dev; both stylesheets load.

## 2026-09-16 — Prototype → WP asset sync script (opencode)
- **Problem:** prototype keeps changing while the WP child theme is a copy — risk of silent drift. Measured drift: prototype **ahead** of theme by 158 lines in `components.css` (the `.contact-*` styles); all other CSS/JS identical; image sets identical (43 each); theme has **zero** unique asset content.
- **New `scripts/sync-theme-assets.sh`** — one-way prototype `assets/` → `wp-content/themes/gerotech-child/assets/`. Modes: default copy, `--check` (drift report, exit 1), `--prune` (drop orphaned theme images). Syncs 4 CSS + 7 JS + mirrored images; deliberately skips `include-partials.js` and `gallery-module.js`.
- **Ran sync** — fixed the `components.css` drift; `--check` now clean.
- **Documented rule** in `.clinerules` + `AGENTS.md`: prototype = source for CSS/JS/images (sync script); markup ported by hand via `handoff/theme-map.md`; theme becomes source at design lock (tag prototype).
- No prototype pages or PHP templates changed.

## 2026-09-16 — Phase 4 ACF: homepage wired (opencode)
- **New `inc/acf-fields.php`** (required from `functions.php`): Homepage group `group_home_content` (location `page_type == front_page`) with tabs Hero slides, Stats, Haas Relationship, Machine Lineup, CTA Band, Mailing List. Verified visible in the editor.
- **`front-page.php` rewritten** to read ACF with the current design as **defaults** (renders correctly with empty fields — safe for a theme-only push). No visual regression; verified in admin + front end.
- **New helpers** in `inc/helpers.php`: `gerotech_accent()` (em→accent span, line breaks), `gerotech_image_url()`, `gerotech_parse_tags()`.
- **Editor model:** plain textareas; accent via `<em>…</em>`; tags = `Label | URL` per line (repeaters can't nest).
- **Local note:** after the pull the admin users are dev's — set `CloudMellow` / `localpass123` locally to verify.
- Next: ES hub group, then MCS/Applications/Automation, Training/Support/About/Contact.

## 2026-09-16 — Training/Support/About converted + Contact built (opencode)
- **Converted** (scripted, dev slugs): `page-training.php` (`/training/`), `page-support.php` (`/support/`), `page-about.php` (`/about/`). Cleared their parent `_wp_page_template` meta.
- **Built `page-contact.php`** (`/contact/`) — no static prototype exists; used dev's real contact content (heading, departments, locations) in our design system. Added a `.contact-*` component to `components.css` (form + details/locations grid). Form is static (CF7/WPForms later); emails set to `@gerotech.com` (dev showed masked addresses).
- **Verified:** all 9 pages (home, 4 ES, training, support, about, contact) 200 at 360/768/1024/1440/1920 — no overflow, no console errors, no PHP warnings. Contact page visually reviewed.
- **Note:** site-wide reCAPTCHA badge (plugin) shows bottom-right on all pages. Plan §12b updated (scope now 9 pages).

## 2026-09-16 — Nav: Training/Support/About/Contact linked; ACF status (opencode)
- **Nav updated** (`header.php`, desktop + mobile): Training, Support, About now link to `/training/`, `/support/`, `/about/`; **Contact** added → `/contact/`. Added `contact` to `gerotech_page_url()` map. All 4 return 200.
- **ACF status:** ACF Pro is **active** and the parent theme's field groups exist (Site Options, Home Options, Solutions Options, Contact Options, Page Options, Training/Service/Career Options…). **But our 5 child templates contain no ACF calls** (`get_field`/`have_rows`) — they're static, so admin edits do nothing on our pages yet. Client editing = **Phase 4**.
- Note: the pulled nav pages render with the parent's old `page-*.php` markup + our CSS → visually unfinished (accepted trade-off).

## 2026-09-16 — Full dev pull + ES templates mapped to dev slugs (opencode)
- **Full pull** `gerotechdev` → local (`gerotech.local`, https) via Local: all 30 pages, plugins (ACF Pro, Megamenu, Smart Slider 3, CF7, WPForms, iThemes Security…), media. Child theme re-copied + activated.
- **Mapping A** agreed: our design takes over dev's existing URLs. Templates renamed: `page-machine-custom-solutions.php` → `page-modification-of-standard-machine-tools.php`, `page-application.php` → `page-unique-applications-for-standard-machines.php`, `page-automation-integration.php` → `page-automated-system.php`. `gerotech_page_url()` map updated (prototype slug → dev URL).
- **Blocker cleared:** the 5 mapped pages had `_wp_page_template` = `page-solutions.php`/`page-home.php` (parent), which overrode our slug templates. Deleted the meta so `page-{slug}.php` applies.
- **Verified:** all 5 pages 200 at 360/768/1024/1440/1920, no overflow, no console errors, no PHP warnings; internal links resolve to dev URLs.
- **Push to dev: deferred.** Unconverted pages render unfinished locally (accepted). Plan §12b updated.

## 2026-09-16 — ES section pages built in WP (opencode)
- **Scope confirmed:** homepage + Engineered Solutions section only (5 pages). Not the original 11.
- **New templates** (scripted conversion from prototype `<main>`): `page-engineered-solutions.php`, `page-machine-custom-solutions.php`, `page-application.php`, `page-automation-integration.php`. All `php -l` clean.
- **Conversion rules:** verbatim markup; `assets/` → `GEROTECH_CHILD_URI`; internal `.html` links → `gerotech_page_link()`; testimonials `data-include` → `get_template_part`; header/footer via `get_header()`/`get_footer()`.
- **Local DB:** created the 4 WP pages (slugs match templates), flushed rewrites. Verified all 5 URLs 200 at 360/768/1024/1440/1920 — no overflow, no console errors, correct scripts (modal.js only on the 3 detail pages).
- **Known follow-up:** nav/footer still link to out-of-scope pages (Training/Support/About/Careers) → will 404 until built or unlinked. Documented in plan §12b.

## 2026-09-16 — Fix wp-admin 502 (php-fpm fork crash) (opencode)
- **Symptom:** `gerotech.local/wp-admin/` → 502; nginx `upstream prematurely closed connection`; php-fpm crash reports in `~/Library/Logs/DiagnosticReports`.
- **Cause:** on this macOS build, WP admin's outbound update/loopback requests load Apple's `Network` framework, after which php-fpm aborts on its next fork (`crashed on child side of fork pre-exec`, `performForkChildInitialize`).
- **Fix (local-only, `wp-config.php`, not in repo):** `@ini_set('display_errors','0')` + `WP_HTTP_BLOCK_EXTERNAL` + `DISABLE_WP_CRON`. Authenticated wp-admin verified 200, no new crashes.
- Local admin password reset to `matt` / `localpass123` for verification. Documented in `handoff/implementation-plan-wordpress-theme-acf.md` §12.

## 2026-09-16 — LocalWP setup + Phase 1 inspection + homepage live (opencode)
- **LocalWP** installed; site `gerotech` (nginx, PHP 8.2.29, MySQL 8.4, WP 7.1) at `~/Local Sites/gerotech/app/public/`. Parent theme pulled from `gerotechdev` via SFTP (the WP Engine portal "Pull" would have overwritten the dev environment — avoided). Child theme copied in and activated; homepage verified at `http://gerotech.local/` (desktop + mobile, zero console errors, matches prototype).
- **Phase 1 findings (parent `gerotech`, Tim Bomers / phiregroup, v2.o):** legacy ~73MB theme with its own `page-*.php` templates. **No `wp_enqueue_style`** — hardcodes `style.css`/`fonts.css` in its `header.php` (overridden by child). **`js_to_footer()`** moves `wp_enqueue_scripts`/`wp_print_head_scripts` to the footer → child CSS would load in footer (FOUC); child now removes it at priority 1. Parent enqueues jQuery-dependent `site-scripts`/`home_script`/`ts_script` → child dequeues. CPTs already registered (`case-study`, `training-session`, `testimonial`, `people`, `career`) → don't re-register. ACF options page already exists → reuse. Menus: `menu-primary`, `menu-mobile`, `menu-footer`, `menu-service`.
- **Fixed** `gerotech-child/inc/enqueue.php` for the above; parent PHP notice (`functions.php:13`) quieted locally via `display_errors=0` in the Local wp-config (not repo).
- **Docs:** Phase 1 findings + local setup added to `handoff/implementation-plan-wordpress-theme-acf.md` §12; child-vs-new resolved (child works).
- **Not in repo:** the 73MB parent theme (local only). Next: Phase 3 — remaining 9 pages.

## 2026-09-16 — WP theme scaffold: header, footer, homepage (opencode)
- **New theme** at `wp-content/themes/gerotech-child/` (child of `gerotech` — parent confirmed in Phase 1). Phase 2 + homepage of Phase 3 of the plan.
- **Files:** `style.css` (child header only), `functions.php` (bootstrap + supports + menus), `inc/helpers.php` (`gerotech_page_url()` slug→URL seam, quote mailto, asset versioning), `inc/enqueue.php` (fonts + CSS in fixed order, conditional JS, dequeue parent handles), `header.php` (alert banner, mega-nav, mobile nav, search modal, `wp_head`), `footer.php` (footer + `wp_footer`), `front-page.php` (homepage ported from `index.html`), `template-parts/sections/testimonials.php`, `index.php` (fallback).
- **Assets copied in:** `assets/css` (4), `assets/js` (7), `assets/images` (22 + 20 gallery). `include-partials.js` dropped (PHP includes replace it); `filter.js` copied but not enqueued.
- **Assumptions/decisions:** nav + footer links hardcoded (plan §7); internal links routed through `gerotech_page_url()` so the open URL-strategy decision stays in one place; `Template: gerotech` + parent style handles filterable (`gerotech_parent_style_handles`); testimonial quotes still static (ACF later).
- **Not verified:** no PHP runtime locally — structural lint only (balanced tags/braces, no leftover `data-include`/`.html` links). Run `php -l` on staging.
- Prototype pages/partials untouched.

## 2026-09-16 — Breakpoint check + top-level responsive type (opencode)
- **Breakpoint audit:** programmatic overflow scan (Playwright + cached Chromium) across 320/360/390/414/768/834/1024/1280/1440/1920 on all 10 pages — **zero horizontal overflow**. Full-page screenshots (390/768/1024/1440) confirm layouts hold (scroll-reveal sections need reduced-motion to render in captures).
- **Responsive type:** added `--fs-h4` + `--fs-stat` fluid tokens (with `--ls-h4`/`--lh-h4`) and converted the remaining fixed top-level sizes — card/feature titles (18–26px), stat numerals (48px), email signup title, news index/title, modal/lightbox titles, haas relationship title. Dropped the `.haas-relationship__title` 640px override.
- Files: `assets/css/tokens.css`, `assets/css/components.css`. Icons/brand marks left fixed.

## 2026-09-16 — WP handoff file cleanup + handoff artifacts (opencode)
- **Housekeeping:** `.gitignore` covers `.DS_Store` + `.codewhale/`; removed `artifacts/` (QA scratch) and both tracked `.DS_Store` files.
- **Deleted unused legacy assets:** 10 unreferenced images (`gerotech-logo.png`, `haas-logo.png`, `haas-logo.svg`, `haas-service-distributor-award-2025.png`, `haas-umc-1500duo-ss.png`, `haas-umc-400-white-bg.png`, `haas-umc-400.png`, `haas-vm-3.png`, `testimonial-collaboration.png`, `testimonial-shop-floor.png`) + `assets/js/testimonials.js`. Removed the dead script tag from `index-cta-lockup-preview.html` and the JS entry from `AGENTS.md`/`design-spec.md`.
- **New handoff artifacts (plan §2):** `handoff/theme-map.md`, `handoff/acf-spec.md`, `handoff/component-inventory.md`, `handoff/asset-manifest.md`, `handoff/js-spec.md`, `handoff/qa-checklist.md`.
- **Left in place (per scope):** exploratory pages (`hero-variations.html`, `showroom.html`, `index-cta-lockup-preview.html`, `gallery-module-preview.html`, `hero-showcase.css`) and no `v1.0-prototype` tag yet.
- No live page markup/CSS changed except removing stale `artifacts/` comments.

## 2026-09-16 — Agent sync (Cursor)
- `.clinerules` Current Session State rewritten for Cline/Cursor/Claude: last commit `c06a6fb`, pickup notes, homepage/lineup/peek status, leftovers (Unsplash rotaries + automation tab photos, gallery module still preview-only).
- `origin/master` is the source of truth. Ignore `.codewhale/`.

## 2026-09-16 — Haas Relationship eyebrow (Cursor)
- “The Haas Relationship” label + rule use `#CF0A2C`.

## 2026-09-16 — Peek slider CTA spacing (Cursor)
- Desktop: +15px margin-top on homepage peek hero buttons.

## 2026-09-16 — Homepage peek slides 2–3 (Cursor)
- Slide 2: showroom photo, for-sale headline, warranty body, Browse Inventory → Haas HFO showroom.
- Slide 3: automation cell photo, “Automation Built for Your Shop Floor,” body removed.

## 2026-09-16 — Haas Tooling Winner's Circle panel (Cursor)
- Replaced turquoise mill stand-in with white panel + Winner's Circle mark (`haas-winners-circle.png`).

## 2026-09-16 — Lineup turning/rotaries/automation/tooling (Cursor)
- Turning: ST-25Y photo; lathe series URLs (ST, Box Way, Chucker).
- Rotaries + Automation: full panel copy; rotary links; Cobots + Bar Feeders Haas URLs.
- Tooling: Haas Tooling eyebrow, **Tooling & Workholding** title, full one-stop copy.

## 2026-09-16 — Homepage polish (Cursor)
- Peek heroes: min-height 500px, 100px vertical padding, copy vertically centered; CTAs not full-width (min-height 50px). Same padding/min-height on `.page-hero` / trust heroes. Interior `.btn--lg` stays auto-width in heroes.
- Machining Centers: six series + Haas links (new tab); View All Mills → vertical-mills.html; extra panel copy gone. Photo is UMC-750 (`haas-umc-750.jpg`).
- Haas F1 card: grey stroke; watermark vertically centered.
- CTA: Tristien body line; phone lockup removed; body 52ch / copy max `--hero-copy-max`.
- Testimonials: Ford + Kingbury quotes from comments. Equal-size lineup chips. Include-partials `no-store`.

## 2026-09-16 — Homepage Figma comment pass (`7196:2420` / Design [WIP] 2)
- Applied the Figma-agent batch (14,000+, Haas headline, Ford/Kingbury names, CTA heading, remove capability band + news + Full Haas Catalog + CTA subtext/tags) plus remaining homepage comments that do not need missing attachments.
- Hero: **Haas Factory Outlet** + **A Division of Gerotech** in Haas red `#CF0A2C`; hero CTA button Haas red / white type.
- Stats: **39+** and **14,000+** only (dropped 12 engineers and #1 Midwest).
- Lineup: Rotaries & Indexers + Haas Automation + simplified Haas Tooling (haastooling.com + Winner’s Circle); mill series links; Gantry → Drill/Tap/Mill; View All Mills → haascnc.com; lathe boxes/links; panel body copy removed where comments asked.
- CTA: ES description + **Engage with us today**. Kingbury quote from Ron Kingsbury LinkedIn; Ford quote body still pending (comment 198 not pasted).
- Not done: attached photos (149, 165, 166, 179, 191), full “Gerotech is proud to serve…” paragraph (200), MCS video on `7196:3079` (140), truncated summaries left as quoted fragments.

## 2026-09-16 — WP implementation plan: theme + ACF, no builder (opencode)
- **Decision:** v1 build = static HTML → WordPress child theme + ACF fields, **no page builder** (fastest path; builder elements can be added later on retainer).
- New `handoff/implementation-plan-wordpress-theme-acf.md`: theme file tree, HTML→PHP conversion map, ACF field model (constraints: repeaters can't nest → Groups/flattened rows; accent words via WYSIWYG `em`; machine lineup as one flat repeater), JS+asset port, forms, 9-phase sequence (~16–17 dev days), risks, open decisions, cold-pickup instructions.
- `.clinerules` updated with build direction + next action.
- Docs only — no prototype pages or assets changed.

## 2026-09-16 — Design-to-dev handoff plan for PM (opencode)
- New `handoff/` folder: `gerotech-handoff-plan.docx` (PM-facing Word doc, 3 real tables) + `gerotech-handoff-plan.html` (editable source).
- Grounded in a fresh live-site audit: WP Engine hosting (not Google Cloud), custom `gerotech` theme, **no page builder**, plugins Megamenu / Smart Slider 3 / CF7 + Honeypot / WPForms / iThemes Security / Search Regex, existing CPTs (training, service, testimonials, people, careers), Google Font API.
- Recommendations in the doc: **child theme** of `gerotech` + CPTs moved to a companion plugin (parent untouched, instant rollback); page builder **Gutenberg + ACF Blocks** first, **WP Bakery** a defensible second; scope = **11 prototype pages only** (no 40-URL migration); 6-phase plan; header/footer are theme-level, never builder-owned.
- No prototype pages or assets changed. Docs only.

## 2026-09-16 — Gallery module preview: collections + video (opencode)
- Client wants multiple images per category and a video option; current flat `.mcs-gallery-card` (one photo each) is wrong. Built as an **isolated preview module** to share with the PM before touching live pages.
- New (untracked): `gallery-module-preview.html`, `assets/css/gallery-module.css`, `assets/js/gallery-module.js`, `assets/videos/placeholder-*.mp4`.
- Model: one card = one category, with a `<template class="gallery-collection__data">` listing `[data-type="image|video"]` media. Badges + play glyph derived in JS; viewer scoped per collection and renders `<video controls>` inline. Chrome mirrors `.gallery-lightbox` but is namespaced `.gallery-viewer*` so it does not touch `components.css` / `modal.js`.
- Preview mirrors MCS content: Column Riser, Fire Suppression, Sheet Metal Modification (3 photos + 1 video), Auto Door Integration (4 photos + 1 video), Hydraulic/Pneumatic, Custom Fixture Design, Safety & Environmental (2 photos).
- Videos are ffmpeg-generated placeholders (commented) until client supplies footage.
- Verified with headless-chrome screenshots (1440 + 390), image viewer, video viewer, mobile single column. Live pages unchanged.
- Next: PM sign-off → promote and convert MCS, then Automation + Applications.

## 2026-09-15 — Nav links trimmed to Machines + Engineered Solutions
- `partials/site-header.html`: Training / Support / About keep their labels but lose `href` (no longer links). Support + About `has-dropdown` wrappers and their dropdowns removed. Desktop + mobile nav. Machines and Engineered Solutions remain linked; Get a Quote CTA and search modal quick links untouched.

## 2026-09-14 — Interior below-hero stats removed
- Dropped `.stat-counter` + `stat-counter.js` from ES, MCS, Automation, Applications. Homepage (and homepage lockup preview) keep the band. About/Careers still use in-hero `.page-hero-trust` facts. Training/Support never had the band.

## 2026-09-14 — Automation `7102:8937` comments
- **#18** hero already client photo (`automation-hero.jpg`).
- **#19** Installed Automation gallery already has the 7 Downloads JPEGs; cell design + EOAT cards already client photos.
- **#20** heading is Installed Automation Gallery; eyebrow “Project Gallery” → Gallery. Reverted unauthorized CTA photo to Unsplash (comments only asked for header + gallery).
- **#21** skipped (MCS copy on the older Automation frame). HMI / Layered / Pre-Engineered still Unsplash — not open unless Matt says images are done.

## 2026-09-14 — Applications image comments closed
- Matt confirmed Applications (`7102:6827`) photos are done: UMC-750 on Part Programming, Kidde on Fire Suppression. Remaining Unsplash (troubleshooting/optimization/tooling/demo/training/RFID) and Unsplash hero/CTA are not open image comments. Copy-only leftover: RFID/Fire modal bodies (“Content coming soon.”). #23 MCS copy still not applied.

## 2026-09-14 — Applications `7102:6827` comments
- **#22** RFID + Fire Suppression cards (and gallery tiles) already on `application.html`. Fire uses Kidde photo; RFID stays Unsplash (no asset) with “Content coming soon.”
- Gallery Part Programming uses UMC-750 (`7102:8142`).
- Reverted unauthorized UMC-750 hero/CTA back to Unsplash (same rule as MCS — no header swap in comments).
- **#23** skipped: MCS copy (risers/sheet metal/spin forming/grinding/mist/tool offsets) was pinned on the Applications frame.

## 2026-09-14 — MCS image comments closed
- Matt confirmed MCS photos for `7102:5961` are done (#12 workholding, #13 gallery, service-card shots from Downloads). Hero stays Unsplash. Process Engineering / Specialty Machine Unsplash is not an open image comment. Remaining MCS items are copy-only (#17 four card bodies).

## 2026-09-14 — MCS hero back to Unsplash
- Reverted `machine-custom-solutions.html` page-hero from `mcs-gallery/sheet-metal-machine.jpg` to the original Unsplash stand-in. Client comments did not request an MCS header swap. CTA still uses the sheet-metal enclosure photo.

## 2026-09-14 — Remaining Figma comments + Downloads photos
- Homepage #2: hero H1 + peek title → “Your Haas Partner in Michigan's Lower Peninsula.” Body #3 already “The Source for Haas Machines for Michigan.” Skipped #4 news rename (ambiguous).
- MCS: Installed Projects → **Installed Gallery**; Unsplash gallery/service/hero/CTA swapped for Downloads photos (column riser, Kidde fire, mist collectors, sheet metal set, auto/vertical doors, hydraulic rotary, fixtures). Process Engineering / Specialty Machine still Unsplash + short placeholder bodies (#17 — no client copy).
- Automation: gallery heading → **Installed Automation Gallery**; CTA uses Haas robot-cell JPEG.
- Applications: Fire Suppression (Kidde photo) + RFID (Unsplash; no photo in Downloads) cards and gallery tiles; modal bodies “Content coming soon.” Hero/CTA use UMC-750. Did not apply MCS copy to Apps/Automation frames.
- Loose ends: RFID photo + body; Hydraulic/Process/Specialty long copy; HMI/Layered/Pre-Engineered still Unsplash.

## 2026-09-14 — ES mega-nav: remove FANUC ASI card
- Dropped the dark “Talk to an Engineer / Get a Quote” card from the Engineered Solutions dropdown. Two-column layout (By Category + All Services) remains.

## 2026-09-14 — CTA trust tags removed site-wide
- Deleted `.cta-band__trust` from Homepage, ES, MCS, Automation, Applications, About, Careers, and the lockup preview. Training/Support never had the row. Dropped unused lockup CSS for the tags.

## 2026-09-14 — Homepage hero body copy (Cursor)
- Slide 1 `.slide__body`: “The Source for Haas Machines for Michigan” (was authorized HFO sales/demos/service line).

## 2026-09-14 — MCS gallery: Custom Fixture photo (Figma 7102:6773)
- Replaced Custom Fixture Design Unsplash with `mcs-gallery/custom-fixtures.jpg`. Same photo on the Custom Workholding service card.

## 2026-09-14 — MCS gallery: Auto Door + Sheet Metal; Automation cell JPEGs
- Figma `7102:6759`: Auto Door Integration Unsplash → `mcs-gallery/auto-door-haas.jpg` (gallery + Auto Doors service card).
- Figma `7102:6752`: Sheet Metal gallery Unsplash → stainless guards + painted enclosure; same stainless on the service card. Full white enclosure with circular intake from the Figma thread was not attached as a file — skipped.
- Figma `7102:9505`: Installed Automation gallery items 01–04 swapped from PNGs to the four client JPEGs (Haas cell, robot line, guarded cell, Keyence vision).

## 2026-09-14 — Applications gallery: UMC-750 photo (Figma 7102:8142)
- Replaced Part Programming Unsplash with `assets/images/app-gallery-umc750.jpg`. Same photo on the Part Programming service card (same stock stand-in).

## 2026-09-14 — EOAT gallery photos (Figma 7102:9509)
- Comment: add dual-gripper, vacuum EOAT, and Schunk fixture shots to the gallery. Already in Installed Automation Projects; swapped PNGs for the client JPEGs.
- Replaced Unsplash on the Robot EOAT service card with the dual-gripper photo.

## 2026-09-14 — Automation Cell Design card photo (Cursor)
- Figma `7102:9505` comment: replaced Unsplash on the Automation Cell Design service card with `assets/images/automation-cell-design.jpg` (FANUC M-20iD/25 · Haas ST-10).
- FANUC ASI seal: clipped to a circle so PNG square corners don’t show on the dark band.

## 2026-09-14 — FANUC ASI seal on ES credential band (Cursor)
- Figma `7102:4501` comment: replace text square with official circular FANUC Authorized System Integrator mark (`assets/images/fanuc-asi-seal.png`, 196×196).
- Featured badge no longer uses orange-bordered text tile.

## 2026-09-14 — Installed Automation Projects gallery photos (Cursor)
- Copied 7 client photos from `Downloads/Installed Automation Projects` to `assets/images/automation-gallery/`.
- Replaced Unsplash gallery on `automation-integration.html` with all 7 (not forced onto HMI/Layered Controls titles). Labels describe what’s in each shot.
- Loose ends: titles are descriptive stand-ins until client names them; CTA band still Unsplash.

## 2026-09-14 — MCS dropdown: Process Engineering + Specialty Machine (Cursor)
- Added both links under Machine Custom Solutions in desktop mega-nav and mobile sub-nav (`partials/site-header.html`). Same href as other MCS items (`machine-custom-solutions.html`).
- Checked mega-nav height at 1920×1080: extra rows still inside the panel (`vhClip` false). No CSS change.
- Loose ends: Safety & Environmental still not in the dropdown (comment #7 did not ask for it).

## 2026-09-14 — Automation hero swapped (Cursor)
- Client cell photo saved as `assets/images/automation-hero.jpg` and wired on `automation-integration.html` `.page-hero`. Gallery/CTA Unsplash left in place.
- Loose ends: gallery images still stand-ins.

## 2026-09-14 — Haas F1 lockup verified; ES hero swapped (Cursor)
- **#6:** Figma `7155:671` is the Haas F1 lockup on a white 5px tile — already live as `assets/images/haas-f1-lockup.png` in `.haas-relationship__brand` (476×137-ish contain in 558×372). No logo swap needed.
- **#8:** Client photo from Downloads copied to `assets/images/es-hero.png` and wired on `engineered-solutions.html`.
- MCS `machineCustomSolutions` attachments left unused — Custom Workholding + gallery stay Unsplash placeholders.
- Loose ends: Automation hero still Unsplash; copy/nav items from comment plan not executed yet.

## 2026-09-14 — Verified Figma client comments vs prototype (Cursor)
- File `YgHwqyyFj57c1ZSbmfkL0c`, page `6:124` ✅ Design [WIP]. Comments API not readable (401); checked selected frames + live HTML.
- Already in prototype: MCS titles/copy 11–16 + modal 24; FANUC ASI on ES; Haas relationship band; MCS/Automation/Applications gallery layouts; F1 lockup.
- Still to do: homepage hero copy (#2–3); mega-nav Process Engineering + Specialty Machine (#7); Applications RFID + Fire Suppression cards (#22); gallery heading “Installed Projects” → “Gallery” (#10/20).
- Blocked: logo/header/gallery images (#6,8,12,13,18,19); four MCS card bodies (#17a–d). Items #21/#23 look like MCS copy pinned on Applications/old Automation — confirm before applying.
- Loose ends: not implemented this session.

## 2026-09-01 — Published Haas F1 lockup to GitHub Pages (Cursor)
- Fast-forwarded `master` to `be3eede` so https://mattybotstew.github.io/gerotech-prototype/ serves the F1 lockup (`haas-f1-lockup.png`, 200). Pages status: built.

## 2026-09-01 — Haas F1 lockup + white brand tile (Cursor)
- **Logo:** Replaced `assets/images/haas-logo.svg` in the Haas Relationship intro with `assets/images/haas-f1-lockup.png` (from Downloads: `HaasF1TeamLogoLockup_Color (2).png`). Transparent PNG — Automation mark + F1 Team lockup. Applied on `index.html` and `index-cta-lockup-preview.html`.
- **Brand tile:** `.haas-relationship__brand` is a 558×372 card (`aspect-ratio`, padding, 5px radius) with **`background: #fff`**. Old SVG gradient is no longer used.
- **Loose ends:** Old `haas-logo.svg` left in assets. White left-side “HAAS” wordmark in the lockup sits on white — red H-mark still reads; say if a dark plate is preferred.


## 2026-08-18 — Mega-nav CTA button text invisible (Cline)
- **Bug:** Text disappeared on all 3 dropdown CTA buttons ("Talk to an Engineer", "Talk to a Sales Engineer", "Get a Quote →").
- **Root cause:** Duplicate conflicting `.mega-nav__cta-btn` rule in `components.css`. Base rule (line ~2402, `b43c854c`) = filled orange button, white text. FANUC-ASI col block (line ~4699, `d4954f17`) used the **same global class** and set `color: var(--clr-orange)` — equal specificity, later in cascade → text turned orange on the orange background (≈1:1 contrast, invisible) for all 3 buttons.
- **Fix:** Scoped the FANUC override to `.mega-nav__col--cta .mega-nav__cta-btn` and set `color: var(--clr-white)` (visible on orange fill); hover keeps white text while lightening background. The other two buttons return to base white-on-orange.
- **Verified:** Playwright render (1440px) — all 3 buttons: color `rgb(255,255,255)`, bg `rgb(243,138,44)`, `visibility: visible`, weight 700. CSS braces balanced (747/747). Diff minimal (+6/−5) in `components.css`.
- **Loose ends:** Uncommitted.

## 2026-08-17 — Peek hero full-width at ≤900px (Figma 7094:5096) (Cursor)
- **Figma:** `7094:5096` — tablet/mobile breakpoint; hero copy should span full viewport (24px inset only).
- **Fix:** At `max-width: 900px`, removed `--max-home` / `16ch` / `--hero-copy-max` constraints on peek hero copy; peek-card band `width: 100%`.
- **Loose ends:** Uncommitted. Figma MCP could not resolve node (file access limited).

## 2026-08-17 — Homepage peek hero height (Figma 7094:5312) (Cursor)
- **Figma:** `7094:5312` — slide 1 tabpanel; height hugs copy, not fixed viewport.
- **Peek hero:** `.hero-slider--peek` → `height: auto`; active slide `position: relative` so track sizes to content. Copy block `padding-top/bottom: 100px`. Removed `75vh` overrides in `elevated.css` + `layout.css`.
- **Mobile:** Bottom padding adds 96px clearance above peek-card band at ≤900px.
- **Loose ends:** Uncommitted.

## 2026-08-17 — Responsive audit: Phases 4–7 (Cline, visual verification)
- **Phase 4 (grid ladder):** `.grid-4` 4→2→1, `.grid-6`/`.partners-grid` 6→3→1, `.grid-2` collapse at 640 (was 480). `.haas-relationship__grid` 4→2×2 (≤1024, cross dividers)→1 (≤640). Added `min-width: 0` to all utility-grid items — fixes images/text forcing their track wider than the container.
- **Phase 5 (hero alignment):** mobile heroes now stay left-aligned site-wide (`.slide__content--left` + `.page-hero__actions` at ≤768), matching the homepage peek; removed the interior-only center override.
- **Phase 6 (cleanup):** deleted orphaned `.machine-browse` + `.machine-cards` CSS blocks and their layout-list entry.
- **Phase 7 (Playwright, in /tmp/pw):** scanned all 10 pages at 360/768/1024/1440 → **0 horizontal overflows** after these fixes: header collapse breakpoint 1000→1120 (desktop nav overflowed 1001–1119), `.about-photo` overflow fixed by grid `min-width:0`, `.btn` wraps at ≤480 (was overflowing a 312px split-grid column at 360).
- **Verified:** CSS brace-counts balanced; all pages + CSS serve 200; console errors = 0.
- **Files:** `assets/css/layout.css`, `assets/css/components.css` (uncommitted).
- **Loose ends:** visual screenshot eyeball not possible (model lacks image input) — used programmatic geometry instead. trust-strip / testimonial-split / testimonial-carousel legacy CSS still present (cross-file, left in place).

## 2026-08-17 — Responsive audit: universal spacing/padding (Cline, Phases 1–3)
- **Static audit** (no browser automation available in plan mode — Playwright not installed). Root causes of the "weird layouts":
  1. **Double horizontal gutter (48px vs 24px):** `.container` adds `padding-inline: 24px` AND the `layout.css` section class list adds another 24px. Sections that nest a `.container` (news, testimonials, mcs-grid, machine-lineup) got 48px mobile insets while `__inner` sections got 24px.
  2. **Mixed container widths per page:** `--max-es` was 1120px while hero/stat-counter use 1200px — left edges didn't align on interior pages.
  3. **Hardcoded vertical rhythm:** full-bleed bands used `--sp-96`/`--sp-64` block padding that ignored the `--section-y` token + its media scaling.
- **Fixes (CSS-only, no markup):**
  - Removed `padding-inline` from the `layout.css` section list; `.container` is the single gutter source. Added `padding-inline: var(--sp-24)` to `__inner` wrappers (why, es-grid, tech-partners, capability, credential, trust, email-signup).
  - `--max-es` 1120 → 1200 (unified to `--max-home`).
  - Added `--section-y-lg: clamp(72px, 8.5vw, 120px)`; applied to `.machine-lineup`, `.haas-relationship__top`, `.cta-band--cinema-lockup .cta-band__content`; removed the fixed media overrides that fought it.
- **Files:** `assets/css/tokens.css`, `assets/css/layout.css`, `assets/css/components.css`. Brace counts balanced.
- **Loose ends:** Phases 4–7 remain — grid-step/breakpoint consistency, mobile hero alignment consistency, legacy CSS cleanup, and a real Playwright screenshot pass at 360/768/1024/1440. Working tree already had uncommitted Cursor WIP (modal.js, HTML, docs) — not committed here.

## 2026-08-17 — Header responsive layout fix (Cursor)
- **Issue:** Logo appeared misaligned on mobile/tablet — header inner shrink-wrapped and centered instead of spanning full width; alert bar used 16px inset vs header/container 24px.
- **Fix:** `width: 100%` on `.site-header__inner` + `.alert-banner__inner`; alert bar padding moved to inner (`padding-inline: var(--sp-24)`). Mobile breakpoint hides desktop `<nav>` wrapper, uses `margin-left: auto` on hamburger; tighter row padding + slightly smaller logo at ≤480px; alert bar centers at ≤768px.
- **Files:** `assets/css/components.css`, `assets/css/layout.css`.
- **Loose ends:** Uncommitted. Verify at ~1440 / 768 / 390px on localhost:8080 or :8899.

## 2026-08-17 — Header logo left padding (Cursor)
- **Issue:** Logo image flush to viewport left (`left: 0`); right-side header content appeared inset.
- **Fix:** `.site-header__inner` horizontal padding `0` → `var(--sp-24)` to match `.container` and section `padding-inline`.
- **File:** `assets/css/components.css` only.
- **Loose ends:** Uncommitted. Browser MCP unavailable for live verify — refresh localhost:8080 to confirm.

## 2026-08-17 — ES detail modal close + gallery lightbox (Cursor)
- **Figma:** Pulled `7009:58` (Gerotech-Design). Node is the mobile contact bar (black / white / orange) — not a lightbox. No gallery lightbox exists in that frame, so chrome matches that high-contrast treatment rather than inventing a conflicting overlay.
- **Close X:** `.mcs-modal__close` is now a 40px ink disc, white glyph, 2px white ring + shadow so it stays visible on light or dark card photos; orange/ink on hover. Top-of-image scrim on `.mcs-modal__img`. Applies to MCS, Application, Automation detail windows.
- **Gallery lightbox:** Clicking `.mcs-gallery-card` opens a full-screen viewer (arrows, keyboard, swipe, scrollable orange-accent thumbs). Wired in `modal.js`; CSS in `components.css`. Same three ES detail pages.
- **Loose ends:** Uncommitted. Figma `7009:58` is not the MCS/gallery page — confirm if a dedicated lightbox frame exists.

## 2026-08-17 — Testimonials + email signup site-wide sync (Cursor)
- **Testimonials partial:** Added `.accent--deep` on "Michigan" + `.headline-rule--deep` under title in `partials/testimonials-block.html` — all pages pick up via include.
- **Email signup:** Unified title/copy to match homepage (`Join Our Mailing List` with accent). Added missing section to Application + MCS.
- **Cleanup:** Removed dead `testimonials.js` from index + ES; dropped `.testimonial-split` from `animations.js`.
- **Loose ends:** Uncommitted.

## 2026-08-17 — Feature icon tiles unified site-wide (Cursor)
- **Scope:** CSS-only — grouped `.haas-relationship__icon`, `.category-card__icon`, `.why-feature__icon` to match homepage Haas band pattern (40×40 `--clr-gray-card` tile, 4px radius, 18px ink/black icons).
- **Before:** Interior pages used orange-tint backgrounds (10–12px radius) with orange stroke SVGs.
- **Pages affected:** Support, About, Careers (category cards); Engineered Solutions (why-feature rows).
- **Loose ends:** Uncommitted.

## 2026-08-17 — Design system propagated site-wide (Cline)
- **Section-header accent system:** Added `.accent--deep` accent words + `.headline-rule--deep` under every `section-title` on ES, MCS, Automation, Application, Training, Support, About, Careers (was homepage-only). Light (white/gray) sections use `.accent--deep` / `.headline-rule--deep`.
- **ES news → editorial split:** Converted Engineered Solutions "Latest Projects & News" from legacy `.news-section--split`/`.news-card` to the homepage's `.news-section--editorial`/`.news-editorial` (photo lead + numbered rows 02–04, same content). No orphaned legacy classes.
- **Stat counters:** MCS, Automation, Application now include the `.stat-counter` band (37+/4,000+/12/#1) after the hero and load `stat-counter.js`. About/Careers keep `.page-hero-trust` (existing decision); Training/Support unchanged.
- **Testimonials site-wide:** Shared `partials/testimonials-block.html` included before the CTA band on MCS, Automation, Application, Training, Support, About, Careers (was homepage + ES only).
- **Verified:** all 9 pages + partials HTTP 200; edited HTML tag-balanced (err=0); editorial/news CSS present in components.css.
- **Loose ends:** Uncommitted. Story links still pending news page.

## 2026-08-17 — Agent docs: design directions sync (Cursor)
- **Updated:** `AGENTS.md`, `CLAUDE.md`, `.cursor/rules/gerotech-agent-sync.mdc`, `.clinerules`, `design-spec.md`, `cline-project-handoff.md` with 2026-08-17 design directions — Haas Relationship (watermark + inverted features band), editorial news split, testimonial card refresh, accent system, Figma node refs (`7080:1405`, `7080:2240`).
- **Loose ends:** Uncommitted code + doc updates from today's session.

## 2026-08-17 — Testimonial card refresh (Cursor)
- **Scope:** CSS-only refresh of `.testimonial-card` in components.css — shared partial `partials/testimonials-block.html` is unchanged, so Homepage + ES both pick it up.
- **Added:** 48px orange top rule (`.headline-rule` motif) that widens to 88px on hover; oversized serif closing quote in `--clr-orange-tint` parked in the empty bottom-right corner (replaces the small inline `"` on `__quote::before`).
- **Attribution:** hairline divider above the name; name now Barlow Condensed 20px (was Navigo 14px); role/company now an 11px uppercase micro-label with `--ls-meta` tracking. `__sub` gets `min-height: 2.7em` so a 2-line role (e.g. "Automotive Supplier, Southeast Michigan") doesn't push the divider out of line with the other cards.
- **Layout:** card `gap` → 0 with explicit margins; `__quote` gets `flex: 1` so attributions bottom-align. Hover adds a 4px lift + orange-tinted border; `prefers-reduced-motion` disables the lift.
- **Also:** `.testimonial-card` added to `animations.js` scroll-reveal targets (the old `.testimonial-split` target is stale since the carousel became a grid).
- **Loose ends:** Uncommitted. Verified Homepage + ES at 1440px and 390px.

## 2026-08-17 — Latest Projects & News redesign (Cursor)
- **Layout:** Replaced 3 identical `.news-card` rows with editorial split — `.news-editorial` grid (1.08fr / 1fr): dark photo lead story + 3 numbered secondary rows (02–04).
- **Lead story (`.news-feature`):** Full-bleed photo, 105deg left gradient + bottom scrim (matches hero/CTA band treatment), orange `.news-tag` chip, Barlow Condensed headline, and a 3-up outcome stat row (38% / FANUC / Turnkey) on a hairline rule.
- **Secondary rows (`.news-item`):** Barlow Condensed index numerals (02/03/04, matching the Haas band `01 ·` motif), tinted `.news-tag--light` chips, 132×96 thumb; hover tints row, shifts padding, zooms thumb, turns index + title deep orange. `justify-content: space-between` aligns the column to the feature height.
- **Added a 4th item** (Training — Macomb operator sessions) so the right column balances the lead card.
- **Also:** `.news-feature` / `.news-item` added to `animations.js` scroll-reveal targets and to the elevated.css photography-cohesion rule; reduced-motion disables image zoom.
- **Untouched:** `.news-card` styles remain (still used by engineered-solutions, showroom, preview pages).
- **Loose ends:** Uncommitted. Story links still omitted pending the dedicated news page (client TBD) — no new dead `href="#"` added.

## 2026-08-17 — Haas features band color invert (Cursor)
- **7080:2240 band:** Inverted to white bg + dark text — `--clr-ink` titles, `--clr-gray-body` copy, `--clr-gray-muted` labels; icon tiles `--clr-gray-card` with black SVGs; dividers `rgba(0,0,0,0.08)`.
- **Loose ends:** Uncommitted.

## 2026-08-17 — Haas watermark stacking fix (Cursor)
- **Layer split:** `.haas-relationship__bg` (watermark only, absolute inset 0, overflow hidden) + `.haas-relationship__content` (z-index 1) inside `__top`; `isolation: isolate` on top wrapper.
- **Result:** Headline, lede, and Haas logo card render above 5% watermark; dark features band (`01 · Sales`, etc.) sits below with solid `--clr-ink` — no watermark bleed.
- **Loose ends:** Uncommitted; preview localhost:8080.

## 2026-08-17 — Haas Relationship features band (Figma 7080:2240) (Cursor)
- **Section:** Dark 4-column capability grid (Sales / Application Support / Warranty / Service & Parts) — part of existing Haas Relationship block on homepage, not a new page.
- **HTML:** Wrapped grid in `.haas-relationship__features` full-bleed band; removed `<hr class="haas-relationship__divider">`; CTAs moved to separate white `.container` below band.
- **CSS:** `components.css` — 4-col grid, column dividers `rgba(255,255,255,0.1)`, icon wrappers 40px / 10% white bg / 4px radius, 24px white titles, 10px uppercase labels, 14px body; mobile stacks single column with row dividers.
- **Assets:** Reused existing `assets/images/icons/haas-rel-*.svg` with `filter: brightness(0) invert(1)` for white-on-dark (no new SVG commits).
- **Placement:** Homepage `index.html` — inside `.haas-relationship`, between intro split (7080:1405) and action buttons, above Machine Lineup.
- **Loose ends:** Uncommitted; preview localhost:8080 — scroll to Haas Relationship section.

## 2026-08-17 — Haas Relationship watermark behind copy (Cursor)
- **Figma 7080:1405:** Repositioned Haas wordmark watermark — `position: absolute` inside `.haas-relationship__intro`, z-index 0 behind copy/brand; Figma left bleed (~-402px); section `overflow-x: clip` prevents horizontal scroll.
- **Removed:** `haas-scroll-bg.js` parallax (user wanted static absolute, not scroll-driven transform).
- **Headline:** Split line breaks + separate `accent--deep` on "authorized" / "outlet" per Figma.
- **Loose ends:** Uncommitted; preview at localhost:8080 — Haas Relationship section.

## 2026-08-17 — Haas Relationship scroll watermark (Cursor) [superseded]
- **Figma 7080:1405:** Added parallax-scrolling Haas wordmark background to `.haas-relationship` — `haas-wordmark-watermark.svg` at 5% opacity, `haas-scroll-bg.js` ties horizontal drift to page scroll; respects `prefers-reduced-motion`.
- **Headline:** Split line breaks + separate `accent--deep` on "authorized" / "outlet" per Figma.
- **Loose ends:** Uncommitted; preview at localhost:8080 — scroll through Haas Relationship section to see drift.

## 2026-08-14 — Site-wide design pass (Codewhale)
- **Lockup CTA site-wide:** `.cta-band--cinema-lockup` (copy-left editorial + call card) promoted to components.css and applied to all 9 pages, each preserving its own copy. Careers/Support/Training keep their custom actions (no call card; copy spans full width).
- **Accent system:** `.accent`/`.accent--deep`/`.headline-rule` added to components.css; orange accent words in hero/section headlines across pages (bright orange on dark, deep `--clr-orange-deep` on light).
- **Testimonials:** shared partial converted from split-photo carousel to `.testimonial-card` grid (`.testimonial-grid` in layout.css); Homepage + ES now use it.
- **Contrast:** new `--clr-orange-deep` (#B45000, ~5.1:1); buttons `.btn-get-quote`/`.btn--primary` → ink-on-orange; `.btn--outline-orange`, `.eyebrow`, card labels/links → deep orange on light surfaces; dark surfaces keep bright orange.
- **A11y:** search modal focus trap (Tab/Shift+Tab) + background `inert` while open.
- **CTA layout:** homepage cinema CTA → 1200px container, left-aligned.
- **ES stat counter:** old `.stat-strip`/`.trust-strip` → homepage `.stat-counter` (37+/4,000+/12/#1).
- **Kept as-is (decision):** About + Careers hero trust facts; showroom/hero-variations exploratory pages.
- **Preview:** `index-cta-lockup-preview.html` committed as an exploratory lockup variant.
- **Loose ends:** `--clr-gray-muted` (#9A9AA8, ~2.8:1) still low-contrast on light surfaces; 57 dead `href="#"` links; mega-menu hover labels on white still bright orange.

## 2026-08-07 — Merged `cursor/hero-bg-and-progress` → `master` (Cursor)
- **Fast-forward** `a578b27..3ba1225` — hero peek/autoplay, Haas Relationship, audit cleanup, machine lineup.
- **`master`** now synced with `origin/master`.

## 2026-08-07 — Machine lineup from Figma 7043:223 (Cursor)
- **Replaced** 4-card `.machine-cards` grid with dark `.machine-lineup` — 5 tabs, split photo/panel, model tag chips.
- **Asset:** `assets/images/machine-milling-centers.png` (Figma export for Machining Centers tab).
- **JS:** Re-enabled `machine-tabs.js` on homepage; panels toggle `hidden` + `is-active`.
- **Tabs:** Machining Centers, Turning Centers, 5-Axis, Automation, Haas Tooling — other tabs use Unsplash stand-ins.

## 2026-08-07 — Homepage audit cleanup implemented (Cursor)
- **Hero slide 1:** Outlet story aligned with shop-floor photo; peek labels synced; CTA → `#machine-browse`.
- **Machine browse:** Renamed to `.machine-browse`; removed duplicate Haas logo; application-focused copy.
- **Haas Relationship:** Added CTAs; eyebrow → shared `.eyebrow-row` + `.eyebrow`.
- **News:** Removed Show More (pending news page); eyebrow → “From the Floor”.
- **CTA / signup:** Trimmed cinema body; `.eyebrow--white`; signup → “Join Our Mailing List”.
- **Cards:** All four link to `gerotech.com/machines` ↗.
- **CSS:** Removed `.machine-dealer-header` / `.haas-dealer-brand`; white/gray section rhythm.
- **Loose ends:** Hero slides 2–3 photos, stat claims, client assets — see `design-spec.md`.

## 2026-08-07 — Homepage design audit + design-spec sync (Cursor)
- **Audit:** High-level homepage review — narrative flow, Haas repetition, hero/peek drift, duplicate logo, placeholders.
- **Docs:** Rewrote `design-spec.md` homepage sections to match current build (peek hero, stat counter, Haas Relationship, 4-card machine browse, cinema CTA); added Figma node refs, removed sections, open cleanup table, Barlow Condensed + asset inventory.
- **Loose ends:** P0 cleanup items documented in spec — not implemented this session.

## 2026-08-07 — Haas Relationship layout upgrade (Figma 7047:904) (Cursor)
- **Intro:** Split redesign — left column stacks headline + lede (40px gap); right column shows Haas wordmark (`haas-logo.png`).
- **HTML/CSS:** `.haas-relationship__copy-col` + `__brand` / `__brand-logo`; eyebrow sits above the split; feature grid unchanged.
- **Branch:** `cursor/hero-bg-and-progress` — uncommitted.

## 2026-08-07 — Fix peek hero autoplay + orange progress (Cursor)
- **Bug:** Autoplay + orange ring never ran — `canAutoplay()` gated on `prefers-reduced-motion` and full-hero hover/focus pause (first viewport = always “paused” while inspecting).
- **Fix (`slider.js`):** Always auto-advance (10s peek / 6s classic); removed hover/focus pause; JS-driven `stroke-dashoffset` via `setInterval(50)` synced to the same clock (not CSS keyframes / rAF — survives background tabs). Manual peek/dot/arrow still resets via `resetAutoplay()`.
- **CSS:** Dropped keyframe / `is-paused` animation hooks; ring look unchanged.
- **Branch:** `cursor/hero-bg-and-progress` — uncommitted follow-up on top of `e690009`.

## 2026-08-07 — Hero peek index autoplay progress ring (Cursor)
- **Progress ring:** Orange SVG stroke on `.hero-slider__index` fills over peek autoplay (10s) via `stroke-dashoffset` + `--hero-progress-ms`.
- **JS (`slider.js`):** `setTimeout` + remaining-time tracking so hover/focus pauses both timer and CSS animation (`is-paused`), then resumes; slide change (auto/manual) resets ring; `prefers-reduced-motion` skips autoplay + animation.
- **CSS:** Explicit `from` keyframe; reduced-motion hides orange stroke, keeps static index + gray track.
- **HTML:** No change (SVG scaffold already in `index.html`) — avoids conflicting with concurrent hero-slide-01 bg work.
- **Loose ends:** Superseded by autoplay fix entry above.

## 2026-08-07 — Homepage hero slide 1 background from Figma (Cursor)
- **Hero slide 1 bg:** Replaced `hero-training-showroom.jpg` with Figma export `assets/images/hero-slide-01.jpg` (node `7046:872` — Haas Automation shop floor / VF-4 line, JPEG 1328×784).
- **HTML:** `index.html` first `.slide__bg` src → local asset; comment notes Figma node. Kept `slide__bg--right` (65% crop) + existing alt/loading attrs — Figma was plain `object-cover`; left-copy bias still fits this frame.
- **Loose ends:** Uncommitted; slides 2–3 still Unsplash stand-ins.

## 2026-08-07 — Haas Relationship section + stat-counter Figma sync (Cursor)
- **Stat counter (Figma 7045:552):** hairline column dividers + bottom rule; values Barlow Condensed 700 / 48px; labels 11px / 1.5px tracking; left-aligned columns.
- **Homepage intro:** Removed Since 1987 text+image block. Replaced with **Haas Relationship** section (Figma 7045:583) — eyebrow, split headline/lede, hairline divider, 2×2 feature grid (Sales / Application Support / Warranty / Service & Parts).
- **Assets:** Figma-exported icons in `assets/images/icons/haas-rel-*.svg` (cart, gear, shield, wrench).
- **CSS:** `.haas-relationship*` in `components.css`; machine browse kept as separate `.intro-section` (margin-top cleared in `elevated.css`).
- **Loose ends:** Uncommitted; browser MCP flaky this session — hard-refresh localhost:8080 for visual QA; not committed.

## 2026-08-07 — Barlow Condensed headline font + hero updates (Cline)
- **Headline font swap:** `--font-display` → **Barlow Condensed** (Google Fonts, 500/600/700); `--font-sans` stays Navigo. Google Fonts link added to all 12 HTML files. Display weight 600 via override rule in components.css; hero exception 700 (bold). Display scale bumped ~25-28% (Barlow Condensed is narrower): `--fs-hero: clamp(40px, 8vw, 106px)`, `--fs-h1: clamp(34px, 4.5vw, 64px)`, `--fs-h2: clamp(30px, 2.6vw + 12px, 50px)`. Tracking loosened, leading tightened for condensed face.
- **Hero text:** eyebrow → "Gerotech"; headline → "The Haas Outlet for Michigan." (106px bold, `pre-headline-font` tag = rollback point).
- **Header/container:** header L/R padding removed; hero peek content container → 1200px centered (`--max-home`).
- **Commits:** `37db95c` (font swap + scale), `7207966` (hero 106px bold), `d83e5e5` (header/container), `c348225` (hero headline), `a652914` (hero eyebrow) — all on `origin/master`.
- **Rollback:** `git reset --hard pre-headline-font` restores Navigo headline state.

## 2026-08-07 — Homepage Figma redesign + stat counter (Cline)
- **Homepage redesign per Figma (node 7009:17 / home frame):** hero slide 1 → "Gerotech is the Front Door to Haas in Michigan"; intro → "Michigan's Premier Distributor for CNC Machinery, Robotics, and Engineered Turnkey Solutions"; machine tabs → **4-card grid**; removed award banner + trust strip; CTA → "Put Our Engineers to Work on Your Project"; email signup → "Stay in the Loop".
- **Header:** FANUC ASI mega-menu column added; nav links UPPERCASE; top bar (`.alert-banner`) → white labels, space-between, 12px padding, 1200px container; factored Figma node 7009:79.
- **Footer:** Machines col → EDM/Automation; Company col → News.
- **Stat counter (node 7030:228):** new `.stat-counter` section (37+ / 4,000+ / 12 / #1) with count-up animation via `stat-counter.js`.
- **Commits:** `d4954f1` (homepage redesign), `90cfe32` (stat counter) — both on `origin/master`.
- **Rollback point:** tag **`pre-headline-font`** pushed — before trying a new headline font (current = Navigo). Roll back with `git reset --hard pre-headline-font`.

## 2026-08-07 — ES hub gateway layout (Cursor)
- **Client (Tristien/Mike):** ES page redundant — mega menu + Why Explore links + filterable service grid all led to same content. Keep mega menu + Why→detail pages + trust strip; remove grid; emphasize FANUC.
- **Removed:** Entire `#es-grid` What We Do section (filters, cards, guide) + `filter.js` from `engineered-solutions.html`.
- **FANUC:** New `.credential-band--featured` chapter after stats — larger badge, H2 claim, benefits list, Talk to an Engineer + Explore Automation; `id="fanuc"`. Placeholder badge only (logo still pending).
- **CTAs:** Hero/partners/capability → `Explore Capabilities` → `#why-headline`. FAQ H2 → **Common Questions**. Partners copy softens FANUC double-sell.
- **CSS:** Featured band in `components.css` / `layout.css` / `elevated.css`.
- **Loose ends:** Not committed; visual QA at 390px; official FANUC logo; Figma `6:124` still has old grid — align when convenient. Peek-card homepage WIP still uncommitted from Aug 4.

## 2026-08-04 — Figma peek-card homepage hero (Cursor)
- **Live homepage:** Replaced classic arrows/dots carousel with Figma Make-style **peek-card** hero (`.hero-slider--peek`): left circular index, bottom-right preview cards for other slides, 3 slides (Macomb training / New Arrivals / Engineered Solutions), 10s autoplay, 450ms opacity crossfade.
- **Artifact:** Excel 5-slide hero saved at `artifacts/homepage-hero-excel-5-slide.html` — restore by swapping that `<section>` onto `index.html`.
- **JS/CSS:** `slider.js` supports peek + classic paths; peek styles in `components.css`.
- **Verified:** index, hero-variations, artifact HTTP 200.
- **Loose ends:** Not committed; peek mobile layout may need visual QA at 390px.

## 2026-08-04 — Excel content sync (5 pages) (Cursor)
- **Homepage (`index.html`):** 5-slide hero (training, showroom inventory multi-CTA, rotary, training duplicate, Winner's Circle); intro + cinema CTA + newsletter copy from content doc. Slide 2 H1 demoted to H2 (H1 only on slide 1).
- **ES hub:** Why-section body + Get A Quote; removed 7 grid cards (Spin Forming, 5-Axis Grinding, Mist & Dust, Tool Offsets, RFID, Fire Suppression, Electrical); Sheet Metal detailed body; CTA headline + newsletter subtitle.
- **MCS:** Hero eyebrow `MACHINE CUSTOMIZATIONS`; modal templates keep Talk to an Engineer only (Get a Quote removed).
- **Applications:** Eyebrow + H1 `Applications Solutions`; removed RFID/Fire Suppression cards; modal Get a Quote removed (page CTA kept).
- **Automation:** Eyebrow + name-split add “and”; removed Electrical card + gallery tile; modal Get a Quote removed; CTA headline updated.
- **Verified:** All 5 pages HTTP 200; ES service cards = 19; MCS/App/Auto cards = 8/6/5.
- **Loose ends:** Excel dump via openpyxl was approval-blocked this session — copy applied from approved prompt + existing partial homepage edits; Figma `6:124` still lags Excel. Uncommitted CSS name-split why-feature rule removal left as-is (no CSS scope in this pass). Not committed.

## 2026-08-03 — Applications page: hero + CTA copy removal, new cards (Cline)
- **Application page (`application.html`):** Removed hero body sentence ("Part programming, process troubleshooting, optimization, tooling recommendations, demos, and training — our application engineers help you get the most out of your equipment.") and removed CTA band body sentence ("Our application engineers help you get the most from your machines, tooling, and production processes.").
- **Application page:** Added 2 new cards to the Application Services grid — **RFID** and **Fire Suppression** (8 cards total now). Both use placeholder copy ("Content coming soon.") + HTML comments flagging no detail copy provided; Unsplash stand-in images pending client photography.
- **Loose ends:** RFID + Fire Suppression detail copy still TBD (same as ES page placeholders).

## 2026-08-03 — Figma design review: ES cards + MCS modals (Cline)
- **ES page (`engineered-solutions.html`):** Removed "Why Gerotech" eyebrow (both Why-features + trust sections) + "More Coming Soon" placeholder card; added "Everyday at Gerotech…" tagline placeholder (copy TBD); updated Machine Column Risers + Sheet Metal Modifications card copy; added 6 new Machine Customization cards — Spin Forming, 5-Axis Grinding, Mist & Dust Collection, Tool Offsets, RFID (placeholder), Fire Suppression (placeholder). Machine Customization group now totals 14 cards.
- **MCS page (`machine-custom-solutions.html`):** Updated modal copy for Machine Column Risers, Sheet Metal Modifications, Safety & Environmental, Auto Doors (full copy from Figma comments); removed hero eyebrow; flagged 4 cards needing content (Hydraulic/Pneumatic, Custom Workholding, Process Engineering, Specialty Machine) as HTML comments only (no visible "Content TBD."); removed email signup section.
- **Application page (`application.html`):** Removed hero eyebrow + email signup section.
- **Automation page:** Removed stray "(Slide 15)" from "Standardized Software Design Methodology" summary + visible "Client provides final copy." from Robot EOAT modal (kept as HTML comments).
- **Verified:** All 4 ES pages HTTP 200; 14 machine cards; breadcrumb targets resolve; no visible stray authoring notes.
- **Loose ends:** Tagline/RFID/Fire Suppression copy TBD; MCS card content TBD; dialog removal (item 15) ambiguous — needs commenter clarification; ES "Update spacing" near signup flagged; Automation "Stay in the Loop" email signup still present (PM decision — removed on MCS + Applications, kept on ES main + Automation).

## 2026-07-31 — Careers page + About rebuild (Cursor)
- **Careers:** New `careers.html` — trust-integrated hero, culture split, open positions table, benefits cards, cinema CTA; live copy from gerotech.com/careers/.
- **Nav:** Header/footer Careers links → `careers.html`.
- **CSS:** `.careers-table` + `.careers-benefits-note` in `components.css`.
- **About:** Rebuilt with live About copy + `page-hero-trust` (prior session work, same batch).
- **Loose ends:** Job links use mailto placeholders — wire to client ATS when available.

- **Copy width:** `--hero-copy-max: 820px` on left-aligned hero headline/body (was 600px).
- **Showcase:** Removed Photo dock + Minimal options; four finalists remain. Trust hero copy spacing; editorial split headline 10% smaller.
- **Loose ends:** Not committed yet in this entry — see git.

## 2026-07-31 — Cinematic footer CTA site-wide (Cursor)
- **Component:** New `cta-band--cinema` — centered full-bleed photo, radial vignette, body lede, dual CTAs.
- **Pages:** Replaced `cta-band--photo` on all 8 core pages (homepage + interiors).
- **Showcase:** Hero option 03 renamed to footer CTA preview; uses production `cta-band--cinema` markup.

## 2026-07-31 — Hero design options page (Cursor)
- **Page:** `hero-variations.html` — rebuilt as a polished 7-option hero showcase with sticky jump nav, metadata tags, and varied Gerotech copy.
- **CSS:** New `assets/css/hero-showcase.css` (exploratory page only — not in global load order).
- **Options:** 01 live carousel · 02 editorial split · 03 cinematic center · 04 left gradient static · 05 photo dock · 06 trust-integrated · 07 minimal statement.
- **Loose ends:** Not committed; preview at `/hero-variations.html`.

## 2026-07-30 — Global testimonial template (Cursor)
- **Partial:** `partials/testimonials-block.html` — homepage split-photo carousel (3 slides, dot nav).
- **Pages:** Homepage + ES hub include the partial; removed ES card variant (`.es-testimonials-section`, `.testimonial-card` carousel).
- **JS:** `testimonials.js` + `include-partials.js` init after partial load.

## 2026-07-30 — Stat strip → trust-strip style (Cursor)
- **Style:** ES + About stat sections now use homepage `.trust-strip` dark bar (orange left rule, white value, grey label, orange bottom border).
- **Counter:** `data-count` on `.trust-strip__value`; headline sits in `.stat-strip__intro` above the bar.

## 2026-07-30 — Stat strip counter redesign (Cursor)
- **Style:** Replaced card pills with flat `.stat-counter` columns — white bg, vertical dividers, large orange numbers, reference-style headline.
- **Animation:** New `stat-counter.js` — counts up on scroll via `data-count` / `data-suffix`; respects `prefers-reduced-motion`.
- **Pages:** `engineered-solutions.html`, `about.html` (Year Founded + “100s” stay static).

## 2026-07-30 — ES hub design refinement (Cursor)
- **Why section:** Stronger headline (“Why Manufacturers Trust Gerotech”); explore links to MCS, Applications, Automation category pages; MCS/Automation name-split titles.
- **ES grid:** Centered header + lede; filters on own row; footer guidance CTA (“Not sure which solution fits your shop?”); category pills aligned to “Machine Custom Solutions” / “Automation Controls Solutions”.
- **News:** Split layout (headline + lede + More Updates left, feed right) on gray band; category meta labels on cards.
- **Spacing:** Extra margin below FANUC credential band (#59).
- **Loose ends:** Not committed; “More Updates” href `#` pending news/blog page (ORDER #48); service card copy still placeholder.

## 2026-07-30 — Unified interior page hero (Cursor)
- **Hero:** All 7 interior pages now use `.page-hero` — same HTML/CSS as homepage hero slide (`slide__bg`, `slide__overlay--left`, `slide__content--left`, `slide__eyebrow/headline/body`).
- **Removed:** Per-page `es-hero` / `mcs-hero--photo` CSS modifiers from `elevated.css`; backgrounds moved to `<img>` in each page.
- **ES detail pages:** Breadcrumb retained as `.page-hero__breadcrumb`; duplicate category badges removed.

## 2026-07-29 — ES mega-menu 2-col + category CTA (Cursor)
- **Layout:** Pure 2-column ES mega (By Category | All Services); removed horizontal CTA band + robot image.
- **CTA:** “Talk to a Sales Engineer” pinned to bottom of first column via flex + `margin-top: auto`.
- **Loose ends:** Not committed; `es-mega-cta-robot.png` unused in markup.

## 2026-07-29 — ES mega-menu horizontal CTA band (Cursor)
- **Layout (option C):** ES mega → 2 nav columns + full-width dark CTA band below (photo left, headline/body/button right). Width `min(880px, calc(100vw - 48px))`.
- **Replaces:** Narrow Col 3 vertical stack that broke “Talk to a Sales Engineer” button.
- **Loose ends:** Not committed; mobile ES CTA still “Talk to an Engineer →”.

## 2026-07-29 — ES mega-menu CTA robot image — white-bar fix (Cursor)
- **Issue:** White vertical bars beside robot photo — letterboxing baked into original Figma export plus bare `<img>` scaling full file width.
- **Fix:** Replaced asset with cleaner center-crop (`300×1024`); added `.mega-nav__cta-media` wrapper (overflow hidden, horizontal bleed); img `height: 100%` + `object-fit: cover`.
- **Loose ends:** Not committed; button copy/link unchanged.

## 2026-07-29 — ES mega-menu CTA robot image (Cursor)
- **ES mega-menu Col 3:** Restored Figma robot-arm photo above “Talk to a Sales Engineer” button; image exported from Figma node 6805:69 → `assets/images/es-mega-cta-robot.png`.
- **CSS:** CTA panel back to flex column (dark bg, 20px padding, image flex-grow + cover, button unchanged fit-content).

## 2026-07-29 — Mega-menu CTA panel + button polish (Cursor)
- **ES mega-menu CTA panel:** Reduced to button-only — removed FANUC badge, headline, and body copy; panel padding tightened.
- **Mega-menu CTAs:** Removed `→` suffixes from “Talk to an Engineer” and “Talk to a Sales Engineer”; buttons use `width: fit-content` and hug label text.
- **Layout:** ES mega width 748px; third column grid ratio 1.1fr; machine-help column `align-items: flex-start`.
- **Nav:** Desktop/mobile dropdown `+` carets and styles unchanged.

- **Machine browse (#73):** Official Haas logo (`assets/images/haas-logo.png`) in dealer header; “Browse Haas Machines ↗” CTA; “Authorized Factory Outlet” badge.

## 2026-07-29 — Header polish: alert banner + search (Cursor)
- **Alert banner:** Background `#000` (`--clr-black`) per Figma.
- **Search button:** Light gray bg (`--clr-gray-border`), dark icon (`--clr-ink`) per Figma.

## 2026-07-29 — Figma homepage comments (Cursor)
- **Trust strip:** Removed Wixom from locations line (`Flat Rock · Grand Rapids`).
- **Award banner:** Removed eyebrow + credit lines; body reworded per tbridges (#71).
- **Machine browse:** Haas text wordmark + dealer lead copy; headline now references Haas (#73).
- **Header:** Primary CTA → "Talk to a Sales Engineer"; ES mega button matches; "Talk to an Engineer" kept on engineering paths (#74/#75).
- **Homepage CTA:** Headline changed from "Put our engineers…" to "Ready to discuss your project?" (#76/#77).
- **Loose ends:** Official Haas logo asset still pending; Flat Rock in trust strip kept (HQ) — client asked re content sheet (#70).

## 2026-07-14 — Agent docs refresh (Cursor)
- **Docs:** Refreshed `AGENTS.md`, `.clinerules`, `JOURNAL.md`, `design-spec.md`, `docs/PROJECT_BRIEF.md`, `cline-project-handoff.md`, `.cursor/rules/gerotech-agent-sync.mdc`.
- **Fixes:** `.clinerules` last-commit pointer → `45977a9`; documented all interior hero modifier classes; Unsplash 404 swap IDs recorded.
- **Remote:** `master` confirmed synced with `origin/master` (no unpushed commits).

## 2026-07-14 — Agent docs sync + interior hero unification (Cursor)
- **Docs:** Updated `AGENTS.md`, `CLAUDE.md`, `docs/PROJECT_BRIEF.md`, `design-spec.md`, `.cursor/rules/gerotech-agent-sync.mdc`, `.clinerules`.
- **Heroes:** All interior pages now use left-aligned photo hero (commit `79b9726`).
- **CTA:** Photo CTA bands left-aligned like hero (`d7138d0`).
- **Images:** Fixed 404 Unsplash URLs on homepage intro/CTA (`4331930`).

## 2026-07-14 — Full design fixes pass (Cursor)
- **Bugs:** Homepage `#main` skip target; scoped `.es-hero` photo modifiers (blueprint / about / support / training).
- **Homepage:** 3 category cards (Machines, ES, Support); slide 3 FANUC ASI eyebrow; cohesive news thumbs; machine tabs → `machine-tabs.js`.
- **ES hub:** Technology Partners section (9 wordmarks); capability band → boxed `.capability-card` layout.
- **Site-wide:** Search modal with quick links; email signup thanks state; footer links wired; CTA band rhythm; SVG category icons (about/support).
- **Automation:** Gallery renamed "Installed Automation Projects".
- **CSS:** Removed dead `.partners-section` / `.partner-logo`; kept `.partner-wordmark` for ES partners grid.

## 2026-07-14 — Elevation pass: photography, Typekit prep, ES/MCS (Cursor)
- **Photography:** Hero slide 2 + intro image aligned to warmer industrial set; `decoding="async"` on homepage heroes; subtle saturation/contrast on hero/CTA photos; MCS sheet-metal card swapped off mismatched asset.
- **Typekit:** `design-spec.md` documents exactly what to add — **Navigo Medium (500)** required; Black (900) optional; no Thin/Light.
- **Tokens:** `--fw-medium` / `--fw-semibold` → 500 (falls back to 400 until kit updated).
- **ES hub:** Dark hero with blueprint bg overlay; why-feature hover accents; stat strip borders; trust accordion open state; credential band hover.
- **MCS:** Left orange accent bar on hero; gallery card lift + image zoom.
- **Branch:** `cursor/design-elevated` — ready to merge → `master`.

## 2026-07-14 — Design elevation pass (Cursor)
- **New layer:** `assets/css/elevated.css` loaded site-wide after layout.css — sticky header blur, hero depth, trust strip, button/card micro-interactions, section rhythm.
- **Homepage:** Trust metrics strip below hero (Since 1987, 3 locations, Haas FFO, FANUC ASI) replaces removed partner band with sharper proof points.
- **Tokens:** `--shadow-btn`, `--shadow-btn-hover`, `--ease-spring`.
- **Branch:** `cursor/design-elevated` — not merged.

## 2026-07-14 — Design recommendations implementation (Cursor)
- **Homepage:** Hero trimmed to 3 slides (Training, Machines, ES); slide 1 owns H1; primary CTAs wired (training, about, mailto quote).
- **ES hub:** Draft stats (35+ years, 3 locations, 500+ machines, 12 engineers); trust FAQ filled; testimonial placeholders replaced; FANUC badge styled as award block; filter tabs use name-split pattern.
- **Nav:** Mega-menu labels aligned — Machine / Custom Solutions, Automation / Controls Solutions; Get a Quote → mailto.
- **Automation page:** Name-split hero pattern applied.
- **MCS:** Gallery reframed as "Installed Projects" with project captions; modal copy filled; CTAs wired.
- **Orphan page:** `machine-modification.html` redirects to MCS.
- **Site-wide:** Lighter alert banner + collapse on scroll; footer socials hidden until URLs confirmed; support/training `#` CTAs wired.
- **Docs:** `design-spec.md` updated with interior hero system + CTA destinations.
- **Not done (needs client/external):** Navigo 500 in Adobe kit; official FANUC logo; real sales email confirmation; machine catalog `#` URLs.

## 2026-07-14 — Presentation-ready verification (Cursor, cursor/cline-beauty-pass)
- **Branch:** `cursor/cline-beauty-pass` reset from latest `master` (`83ae812`). All beauty-pass goals A–C already implemented on master — this pass verified, did not re-implement.
- **Verified @1440px:** Homepage (partners band, intro frame, model tags, testimonials, Ken Burns), ES (SVG why-feature icons), MCS (name split hero).
- **Verified @390px:** Homepage machine tabs horizontal scroll; no console errors on fetch of all 9 pages.
- **Per-page CTA images:** 9 unique Unsplash stand-ins confirmed.
- **Not changed (per blockers):** Nav "Machine Customization" vs page "Custom Solutions", orphan machine-modification.html, FANUC badge placeholder, `#` CTAs.
- **Not pushed** — awaiting Matt.

## 2026-07-14 — Merge beauty pass + per-page CTA images (Cursor)
- **Merged** `cursor/beauty-pass-polish` → `master` (partners, model tags, hero motion, ES SVG icons, token/CSS polish).
- **Salvaged from Cline WIP:** Each page now has its own `cta-band--photo` background (9 unique Unsplash stand-ins; no longer one shared image). Interior pages: about, application, automation-integration, engineered-solutions, machine-custom-solutions, machine-modification, support, training.
- **Discarded:** Duplicate/incomplete beauty-pass staged edits on `cursor/cline-beauty-pass`; `.claude/settings.json` (local paths, not committed).
- **Next:** Orphan machine-modification.html, FANUC badge, `#` CTAs, real partner logos when client supplies assets.

## 2026-07-14 — Beauty pass (Cursor)
- **Design polish:** Squared card radii (`--radius-card` → 0), orange/ink tints, intro image offset frame, partner wordmark grid (12 brands), model-tag chips on all 5 machine panels, hero Ken Burns, news-card hover lift, testimonial track shadow, machine-tab horizontal scroll on mobile.
- **Homepage:** Partners section after intro (Haas, Midaco, Fanuc, OnRobot, Dynatect, Royal Products, Marpos, Tsudakoma, Alberti, Renishaw, Keyence, 5th Axis). Testimonial slides 2–3 filled with draft quotes (no bracket placeholders).
- **Engineered Solutions:** Replaced emoji why-feature icons with inline SVG stroke icons (orange industrial style).
- **Next:** Orphan machine-modification.html, FANUC badge, `#` CTAs, real partner logos when client supplies assets.

## 2026-07-14 — Afternoon Session (Cursor)
- **MCS page naming split**: Fixed visual separation between "Machine" and "Custom Solutions"
- Updated heading structure to match other interior pages
- **Next**: Orphan machine-modification.html, FANUC badge, `#` CTAs

## 2026-07-14 — Claude Code (Fable 5) — homepage patterns propagated to interior pages
- Matt asked to roll homepage additions out site-wide. Photo CTA band (`cta-band--photo`) replaced the white-card CTA on all 8 interior pages — DECISION REVERSED from 2026-07-13 ("interior pages keep the white-card CTA"); each page keeps its own copy and tel: links, all use the homepage's placeholder image for now.
- Email signup added to about + support (only pages missing it). Navigation untouched — header/footer partials are already shared across all pages.
- showroom.html left alone (exploratory, has its own CTA variant). Verified via Playwright screenshots, no console errors.

## 2026-07-14 — Claude Code (Fable 5) — inline CSS migrated to global stylesheets
- Matt asked to globalize inline/hardcoded CSS. index.html's `<style>` block (machine tabs/panels) moved into components.css and tokenized; all `style=""` attributes stripped from index/about/support/engineered-solutions in favor of classes: `.slide__bg--right`, `.news-more`, `.about-photo`, `.section--white/--gray`, `.es-hero--compact`, `.grid-2--split`, `.grid--offset-top`, `.section-body--spaced`.
- Left alone on purpose: showroom.html and hero-variations.html `<style>` blocks — exploratory variant pages; promote their styles to components.css only if/when the designs are adopted.
- Verified with Playwright at 1440px (tabs default + switched, hero crop, about, support): rendering unchanged, no console errors.

## 2026-07-14 — Claude Code (Fable 5) — machine tab visual fix
- Fixed stray radius-matching arcs on inactive machine tabs (homepage). Cause: index.html's inline `<style>` gave `.machine-tab + .machine-tab` dividers both `border-left` and `border-radius`, curving each divider. Inactive tabs are now square with straight dividers; only the active tab is rounded (pill on mobile, dividers dropped there).
- Note: the machine-tab styles that actually apply live in index.html's inline `<style>` block — the `.machine-tab` rules at the bottom of components.css are overridden by it (left in place, uncommitted from a prior session).
- Repaired pre-existing parse error in components.css: orphaned declaration block after `.slide__content--left` (leftover body from a replaced rule in an earlier uncommitted edit) — removed.

## 2026-07-13 — Claude Code (Fable 5) — whole-site design improvement
- Executed Matt-approved 4-phase plan across all 9 pages. Biggest fix: subpages loaded dead Plus Jakarta Sans and rendered fallback sans-serif — all now load the Typekit Navigo kit.
- Consistency: squared all interior card types, 80→88px rhythm, replaced all 53 interior dashed placeholders with Unsplash stand-ins (POLICY CHANGE, Matt-approved — .clinerules updated; each img carries a credit/awaiting-client comment; only reused the 17 verified homepage URLs).
- Responsive: mobile nav switch at 1000px (was 768; header needs ~975px), hero/section/news/alert-banner mobile rules added, es-hero__headline--md modifier replaced inline h1 sizes (fixed 66px overflow on about @390).
- A11y/perf: :focus-visible system, skip link + #main on all pages, prefers-reduced-motion (CSS + autoplay/reveal guards), carousel aria-selected/aria-hidden/tabindex management, autoplay pause on hover/focus, single banner landmark, homepage h1, eager LCP hero img.
- Polish: unified card hover lift, image zoom in card shells, 60ms staggered reveal.
- Verified: 9 pages × 4 widths, zero console errors/404s, Navigo everywhere, no overflow; keyboard + reduced-motion passes.
- Loose ends unchanged by design: open client decisions (naming split on MCS page, orphan machine-modification.html, FANUC badge, ES stats/accordion copy, `#` CTAs).
- Follow-up (Matt request): homepage CTA band redesigned as hero-style full-bleed photo section (`.cta-band--photo` variant; new Unsplash stand-in Sam Moghadam, darker 0.85/0.75/0.82 overlay). Interior pages keep the white-card CTA.
- Follow-up (Matt request): desktop font-size double-check — swept every rendered size on all 9 pages @1440 via computed styles. Fixed 7 stragglers: slide__body 16→18 (lede), credential-band 28→h3, capability-bullet__num 48→h1, .btn 15→14 / .btn--lg 17→16 (uppercase sits a step smaller), mega-nav micro-type consolidated (col-title+services-label 10→11, cat-desc 11→12, badge 9→10). Deliberate off-scale values documented: 42px stat numerals, 22px mcs-card titles, 24px signup title.
- Follow-up (Matt request): full typography system overhaul (kept Navigo). KEY FINDING: typekit kit serves 400+700 only — 500/600/800 were falling back silently; tokens now map to real faces. Added fluid modular scale (--fs-hero→caption), tiered negative tracking (--ls-*), leading tokens, two caps-tracking standards (0.14/0.08em), balance/pretty wrapping, 62ch intro measure; removed all media-query font-size overrides (clamps handle it); unified card titles at 18px, fixed mcs-hero weight outlier. Verified computed styles on 3 pages × 2 widths. Flag to Matt: consider adding 500/800 to the kit for more weight contrast.
- Follow-up (Matt request): signup band bg → dark-nav grey #3A3A40 (the footer's old color) w/ white title + muted-grey sub; footer bg → black (--clr-ink #0D0D0D). Applies on all 7 pages via shared classes.
- Follow-up (Matt request): all button text uppercase w/ 0.05em tracking (.btn family, .btn-get-quote, .email-signup__submit, .mega-nav__cta-btn). Done in CSS via text-transform — markup copy unchanged.
- Follow-up (Matt request): testimonial panel vertical rhythm fixed — quote-mark line box crushed to 0.6 (was leaving ~40px dead space), blockquote made flex column w/ 24px gap so attribution no longer crams against the quote.
- Follow-up (Matt request): eyebrow→headline gap unified to exactly 8px everywhere (fixed es-hero 12px, mcs-hero badge 16px, hero slide 12px, intro "Since 1987" 32px, CTA band double-gap; measured with Playwright across pages). Email signup restructured on all 7 pages: new `__inner`/`__copy` wrappers — copy left-aligned, form right (flex row, wraps to stacked left-aligned on mobile).

## 2026-07-13 — Claude Code (Fable 5)
- Implemented "Gerotech Homepage.dc.html" (Deep Code standalone export found in ~/Downloads; the attached zip never reached disk — user confirmed the Downloads file as source). Unpacked its gzip/base64 bundle to read the design template.
- Most of the design was already in from Cline's Figma pass; this was a gap-closing alignment: bundled logo SVGs wired into header (dark) + footer (white, replaces text wordmark), hero converted to sliding track with dashed "Awaiting client copy" placeholder boxes on slides 2–6, homepage testimonials converted to sliding variant (ES page untouched), squared edges on testimonial/news imagery, news/CTA/email-signup metrics matched to spec, two new neutral tokens (--clr-gray-dot, --clr-gray-input).
- Decision: kept Navigo (Adobe Fonts) rather than the spec's Archivo/IBM Plex — the Navigo swap postdates the design export and looks like a deliberate brand-standards move. Flag to Matt.
- Verified with Playwright (Chromium, 1440/390px, scrolled all sections): motion, logos, no console errors.
- Loose end: hero slide 1 "View Training Schedule" and CTA/quote buttons still `#` placeholders (unchanged, per open client decisions).

## 2026-07-09 — Claude Code (Fable 5)
- Added Machines mega dropdown to the shared header partial: full Haas catalog (8 categories / 41 models per client screenshot), 4-column viewport-centered panel matching the existing ES mega-nav design system, plus "Talk to an Engineer" CTA and full-catalog footer link.
- Mobile nav: Machines is now a `<details>` accordion with the 8 category links.
- nav.js: Escape key dismisses open menus (keyboard a11y).
- Decision: kept the top-level Machines link pointing at gerotech.com/machines (existing behavior); all 41 model links are `#` placeholders — Matt supplies URLs later (TODO comments in partial).
- Fixed Safari bug found after user report: panel used `position: fixed; top: auto`, which WebKit anchors to the document flow position instead of the stuck header — menu rendered ~800px off-screen when scrolled. Now anchors absolutely to the sticky header (li static). Also stretched nav items to full header height so hover survives the link→panel traversal gap.
- Verified with Playwright (WebKit + Chromium) at 1440px + 390px, scrolled and unscrolled. Uncommitted.

## 2026-07-07 — Claude Code (setup)
- Adopted agent-agnostic setup: AGENTS.md is canonical (CLAUDE.md is a symlink), this journal tracks cross-agent session history.
- Recent git history at time of setup:
  - 51020b3 Add product galleries to Applications and Automation & Controls Solutions pages
  - f3c9206 fix: move mailing list signup from buried footer to standalone section
  - 1950760 feat: replace footer logo placeholder with GEROTECH wordmark text
  - 8502f25 feat: populate automation modals with presentation content + footer mailing list signup
  - 3d388d7 Add Figma comments to engineered-solutions.html
  - 068801f chore: update .clinerules with current session state
  - d564578 feat: replace intro category cards with Haas machine browse on homepage
  - 875a9e6 chore: set GEROTECH wordmark to white
