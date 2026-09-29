# ACF field spec

**Companion to:** `implementation-plan-wordpress-theme-acf.md`
**Requires:** ACF Pro (repeaters + options page)
**Registration:** all field groups registered in PHP (`inc/acf-fields.php`) via `acf_add_local_field_group()` — version-controlled, **not** via the admin UI.

---

## 1. Global — Options page "Site Content"

Location: `acf_add_options_page()`. Consumed by `header.php` / `footer.php` / shared parts.

**Registration:** three groups in `inc/acf-global-fields.php` (`group_site_header`, `group_site_navigation`, `group_site_footer`) plus the pre-existing `group_site_forms` / `group_site_testimonials` in `inc/acf-fields.php`. All on the same options page.

**Defaults live in `inc/global-content.php`**, never in ACF `default_value` — see the standing rule in `AGENTS.md`. Every field below renders the current design when its value is blank.

### 1a. Site Content — Header (`group_site_header`)

| Field | Type | Notes |
|---|---|---|
| `header_banner_items` | Repeater (max 4) | `label`, `value`, `url` (link) — 3 phone numbers today. The `tel:` link is derived from `value` when `url` is empty. |
| `header_logo` | Image | blank = theme-bundled `gerotech-logo.svg` |
| `header_logo_alt` | Text | |
| `header_logo_url` | Link | blank = home URL |
| `header_cta_label` | Text | "Talk to an Engineer" — clear to hide the button |
| `header_cta_url` | Link | |
| `header_search_title` / `header_search_hint` / `header_search_placeholder` | Text | search modal copy |
| `header_search_links` | Repeater | `label`, `url` — the 7 quick links in the search modal |

### 1b. Site Content — Navigation (`group_site_navigation`)

| Field | Type | Notes |
|---|---|---|
| `nav_items` | Repeater (max 8) | `label`, `url`, `new_tab`, `style` (plain / dropdown / machines-mega / es-mega), `show_mobile`, `links` (repeater: `label`, `url`, `new_tab`) |
| `nav_machines_groups` | Repeater (max 24) | `title`, `column` (1–4), `mobile_order` (number, optional), `links` (repeater: `label`, `url`, `new_tab`) |
| `nav_machines_help_title` / `_label` / `_url` | Text / Text / Link | the dark "not sure which machine" card in column 4 |
| `nav_machines_footer_label` | Text | "Browse the full Haas catalog" — the full-width link under the panel |
| `nav_machines_footer_mobile_label` | Text | "Full Haas Catalog ↗" — shorter wording for the phone menu |
| `nav_machines_footer_url` / `_new_tab` | Link / true_false | |
| `nav_es_col1_title` / `nav_es_col2_title` | Text | "By Category" / "All Services" |
| `nav_es_categories` | Repeater | `heading_lead`, `heading_main`, `url`, `description`, `last` — the three category cards |
| `nav_es_cta_label` / `nav_es_cta_mobile_label` / `nav_es_cta_url` | Text / Text / Link | the bottom-of-column button |
| `nav_es_services` | Repeater | `heading_lead`, `heading_main`, `links` (repeater: `label`, **`mobile_label`**, `url`) |

> **The phone menu is generated from the same tree** (`template-parts/site-mobile-nav.php`), not hand-written a second time — the old `header.php` repeated every link there and the two could drift. Where the design genuinely differs, it is an explicit field, not a hidden hardcoded list: machine group `mobile_order`, `nav_machines_footer_mobile_label`, `nav_es_cta_mobile_label`, and a per-link `mobile_label` on Engineered Solutions service links.

### 1c. Site Content — Footer (`group_site_footer`)

| Field | Type | Notes |
|---|---|---|
| `footer_logo` / `footer_logo_alt` | Image / Text | blank image = theme-bundled white `gerotech-logo-white.svg` |
| `footer_tagline` | Textarea | |
| `footer_address` | Textarea | line breaks preserved |
| `footer_phone` | Text | `tel:` link generated from it |
| `footer_socials` | Repeater (max 6) | `icon` (glyph), `label` (aria-label), `url`, `new_tab` |
| `footer_columns` | Repeater (**max 3**) | `title`, `links` (repeater: `label`, `url`, `new_tab`) — Machines / Solutions & Support / Company |
| `footer_copyright_text` | Text | year is prepended by the template |
| `footer_legal_links` | Repeater (max 6) | `label`, `url`, `new_tab` |

> **`footer_copyright_text`, not `footer_copyright`.** The 2017 legacy group `group_59305cb95c705` ("Site Options", stored as `acf-field-group` posts) already owns a field named `footer_copyright`, and its option row carries an `options_footer_copyright` reference. ACF resolves option values by field *name*, so a name collision silently serves the legacy value. When adding a new options-page field, diff its name against the 371 legacy field names first.

### 1d. Existing groups on the same page

| Group key | Contents |
|---|---|
| `group_site_forms` | `signup_email_label`, `signup_email_placeholder`, `signup_submit_label` |
| `group_site_testimonials` | `testimonials` repeater — `quote`, `name`, `role`; **shared, global**, consumed on every page |

> `testimonials` was spec'd as a flat global repeater and is implemented that way.

---

## 2. Homepage (`front-page.php`)

| Field | Type | Notes |
|---|---|---|
| `hero_slides` | Repeater | `eyebrow`, `headline` (WYSIWYG — accent via `em`), **`accent_color`**, `body`, `cta_label`, `cta_url`, **`cta_color`**, `image`, `image_position`, `title_alt`, `peek_eyebrow`, `peek_accent`, `peek_title` |

> There is **no** separate peek-colour field. The peek card's accent word inherits the slide's `accent_color` — `front-page.php` emits it as `data-peek-accent-color="<choice>"`, and `slider.js` whitelists it (`ACCENT_COLORS`) before applying `hero-slider__peek-accent--<colour>`. So the headline accent and its peek-card counterpart can never drift apart.
| `hero_stats` | Repeater | `value`, `label` — currently 39+ / 14,000+ |
| `haas_eyebrow` | Text | "The Haas Relationship" |
| `haas_eyebrow_color` | Select | `white` / `haas` (default) / `orange` — colours the eyebrow text **and** its short rule together |
| `haas_headline` | WYSIWYG | Accent word via `em` |
| `haas_accent_color` | Select | `white` / `haas` (default) / `orange` — colour of the `em` accent words |
| `haas_lede` | Textarea | |
| `haas_brand_logo` | Image | F1 lockup (theme-bundled default) |
| `haas_features` | Repeater | `index`, `icon` (image), `label`, `title`, `body` — numbered `01 ·`–`04 ·` |
| `lineup_eyebrow` | Text | |
| `lineup_headline` | WYSIWYG | |
| `lineup_panels` | Repeater (flat) | `panel_type` (select: milling/turning/rotaries/automation/tooling), `tab_label`, `photo`, `badge`, `category`, `title`, `description`, `tags_label`, `tags` (repeater of `label`+`url` — **allowed: nested inside a repeater is not; use a flattened tag list or a sub-repeater only if ACF Pro nested repeaters are enabled**), `cta_label`, `cta_url` |
| `news_lead` | **Group** | `tag`, `date`, `title`, `excerpt`, `image`, `stats` (repeater `value`+`label`) — **Group because a repeater can't hold a repeater** |
| `news_items` | Repeater | `index`, `tag`, `date`, `title`, `excerpt`, `image` |
| `cta_eyebrow`, `cta_headline`, `cta_body`, `cta_button_label`, `cta_button_url` | Text/WYSIWYG | |
| `cta_call_label`, `cta_call_number`, `cta_call_note` | Text | Optional call card (omit on Careers/Support/Training) |
| `cta_image` | Image | |

> **Nested-repeater constraint:** if `lineup_panels.tags` cannot nest, flatten to `lineup_tags` repeater keyed by `panel_type`. Decide in Phase 4.

---

## 3. Interior pages (shared pattern)

| Field | Type | Notes |
|---|---|---|
| `hero_eyebrow` | Text | |
| `hero_headline` | WYSIWYG | Accent via `em` |
| `hero_body` | Textarea | |
| `hero_cta_label` / `hero_cta_url` | Text | Optional |
| `hero_image` | Image | |
| `hero_breadcrumb` | Repeater | MCS / Automation / Applications only: `label`, `url` (last item = current, no link) |
| `hero_trust_stats` | Repeater | About / Careers only: `value`, `label` |
| `hero_accent_color` / `hero_cta_color` | Select | Per page, prefixed (`es_`, `app_`, `ai_`, `careers_`). Same White / Haas Red / Brand Orange palette as the homepage hero. Ship **blank** with **no `default_value`** — blank keeps the design colour (Brand Orange). See "Un-migrated databases" below. |
| `sections` | Flexible/Repeater | Section headers + card grids per page (see below) |
| `cta_*` | same as homepage | |
| `signup_title` | WYSIWYG | "Join Our <em>Mailing List</em>" |
| `signup_sub` | Text | |

**Card-grid repeater (MCS, Automation, Applications, Training, Support, About, Careers):** `eyebrow`, `title` (WYSIWYG), `body`, `image`, `modal_body` (MCS/Automation/Applications — drives `.mcs-modal`).

**Gallery repeater (MCS, Automation, Applications):** `image`, `alt`, `caption`, `category`.

**News editorial (ES):** same shape as homepage `news_lead` + `news_items` — **but PARKED** (see below).

**Service cards (`mcs_cards`, MCS / `page-modification-of-standard-machine-tools.php`):** repeater of `title`, `image`, `video` (optional), `detail` (WYSIWYG, rendered into the card's modal).

| Field | Type | Notes |
|---|---|---|
| `video` | File (`mp4,webm,mov`, returns array) | **Optional.** When set, the card renders a `<video>` instead of the `<img>`: `autoplay muted loop playsinline`, with `image` reused as the `poster` so there is no blank frame and reduced-motion users keep the still. The element is `aria-hidden` — the card heading and modal carry the meaning. One card uses it today (Auto Doors). Registers as `field_mcs_card_video`. **Asset weight:** an inline looped clip downloads with the page (the current one is 4.4MB), so keep card clips small. |
| `image` | Image (returns array) | Also the video poster when `video` is set. |

> **Gotcha — this repeater overrides the PHP default array.** `$cards = $pick( 'mcs_cards', <default array> )`. Local and Dev have *stored* rows, so adding `'video' => …` to the template's default array alone changes nothing: the stored rows win and have no `video` key. Seeding the row (`mcs_cards_<i>_video` + `_mcs_cards_<i>_video` = `field_mcs_card_video`) is what makes it appear. A fresh install with no stored rows gets the default automatically.

### Latest Projects & News — parked behind `es_show_news` (ES only)

Client (Sep 2026): *"we just don't know if we can support the Latest Projects & News right now… disable on all pages BUT keep it as a component that can be easily added back by the client."*

| Field | Type | Notes |
|---|---|---|
| `es_show_news` | True/False | **`default_value` 0 (Hidden).** Renders `template-parts/sections/news.php` on Engineered Solutions only. |

The markup was extracted out of `page-engineered-solutions.php` into `template-parts/sections/news.php`, which returns early unless `es_show_news` is on. **Nothing was deleted** — every `es_news_*` field is still registered and still holds its seeded content, so flipping the toggle re-renders the real section with the client's own data. Only ES ever rendered it (homepage news was already removed in the Figma-alignment pass).

- **Client re-enables it:** ES → **News** tab → "Show the Latest Projects & News section" → **Show** → Update. No dev work.
- **Adding it to another page:** add `get_template_part( 'template-parts/sections/news' );` to that template and copy the `field_es_show_news` entry into that page's ACF group.
- **Prototype equivalent:** `engineered-solutions.html` SECTION 12 is now just a pointer comment — the markup itself was **moved** into `partials/news-block.html` (not duplicated), and ES keeps a commented-out one-line `data-include` to bring it back. Re-enable = uncomment that line.
- **Gotcha:** while the toggle is off this template never runs, so a content-seeder's capture hook will not see these fields. Both Local and Dev are already seeded; if a re-seed is ever needed, switch the toggle on for one render first.
- **Verified on Dev:** toggle off → 0 news markers, on → 2, off again → 0; meta then deleted so Dev sits at the default-off state. Local behaves identically.

---

## 4. Accent-word rule

Headlines that mix ink and orange use a WYSIWYG field. The editor italicises the word (`em`); the theme maps `em`/`i` → `.accent` (dark surfaces) or `.accent--deep` (light surfaces). Confirmed approach — see plan §11 open decision 5.

### Hero slide accent colour (`hero_slides` → `accent_color`)

Per-slide select controlling the colour of the `<em>` accent word **and** its matching peek-card word:

| Value | Label | Classes emitted | Colour |
|---|---|---|---|
| `white` | White (no highlight) | `accent accent--white` | `--clr-white` — no colour highlight |
| `haas` | Haas Red | `accent accent--haas` | `--clr-haas-red` (#CF0A2C) |
| `orange` | Brand Orange | `accent` | `--clr-orange` (#F38A2C) |

Mapped by `gerotech_accent_class()` (`inc/helpers.php`), which also tolerates the retired raw-class values (`accent`, `accent--haas`, `accent--deep`) so an un-migrated database (e.g. Dev) still renders correctly. Current: slide 1 `haas`, slides 2–3 `orange`. The select ships **blank** — blank means "no choice made" and resolves to the positional design default (slide 1 Haas red, later slides Brand Orange), **not** to White. This field has **no `default_value`**; see "Un-migrated databases" below for why that matters.

> **Note:** the `.accent--haas` CSS rule previously read `.accent.accent--haas` (compound), so the bare `accent--haas` class the theme emitted never matched and Haas red silently rendered white on WordPress. The modifiers now stand alone and follow `.accent` in source order.

### Hero slide button colour (`hero_slides` → `cta_color`)

Per-slide select controlling that slide's call-to-action button, **independent of the accent colour**:

| Value | Label | Classes emitted |
|---|---|---|
| `orange` | Brand Orange | `btn btn--primary` |
| `haas` | Haas Red | `btn btn--primary btn--haas` |
| `white` | White outline | `btn btn--outline-white` |

Mapped by `gerotech_btn_class()` (`inc/helpers.php`). Current: slide 1 `haas`, slides 2–3 `orange`. Ships **blank** with no `default_value`; blank resolves positionally (slide 1 Haas Red, later slides Brand Orange).

### Haas Relationship colours (`haas_eyebrow_color` / `haas_accent_color`)

Same White / Haas Red / Brand Orange palette as the hero slides, defaulting to **Haas Red** to match the Figma. The eyebrow fields drive a shared custom property so text and rule can never drift:

```css
.haas-relationship .eyebrow-row            { --eyebrow-accent: var(--clr-haas-red); }
.haas-relationship .eyebrow-row--white     { --eyebrow-accent: var(--clr-white); }
.haas-relationship .eyebrow-row--orange    { --eyebrow-accent: var(--clr-orange); }
.haas-relationship .eyebrow-row .eyebrow,
.haas-relationship .eyebrow-row .eyebrow-row__rule { color/background: var(--eyebrow-accent); }
```

The unmodified default is Haas red, so markup without a modifier (older prototypes, un-migrated rows) still renders as designed. `haas_accent_color` reuses `gerotech_accent_class()`. **Interior page eyebrows are untouched** — the modifiers are scoped to `.haas-relationship` for now.

### Interior hero accent colours (2026-09-22)

Every interior hero now supports `<em>` accent words coloured by a per-page select: `es_hero_accent_color`, `app_hero_accent_color`, `ai_hero_accent_color`, `careers_hero_accent_color`, and — added last — **`mcs_hero_accent_color`**. All ship **blank** with no `default_value`; blank means "keep the design colour" (Brand Orange). MCS also accepts `<em>` in both its lead and main fields.

> **Breadcrumbs must strip the tags.** Any template whose breadcrumb echoes an accent-capable headline has to `strip_tags()` it first or the literal `<em>` prints as visible text. `page-automated-system.php` does this; `page-modification-of-standard-machine-tools.php` originally did not and showed `Custom &lt;em&gt;Solutions&lt;/em&gt;`.

### Service page checklist + locations (2026-09-22)

`page-service.php` previously hardcoded 87 strings. Now field-driven:

| Field | Type | Notes |
|---|---|---|
| `service_tab_label_{service,general,parts,rotary,plan,support}` | Text ×6 | Tab strip labels. **Individual fields on purpose** — each is bound to a fixed tab id (`tf_service`, …), so a repeater would let someone reorder them and break the wiring. |
| `service_plan_inspect_groups` | Repeater | `heading`, `items` (one per line), `column` (left/right). 10 groups. |
| `service_plan_optional_groups` | Repeater | Same shape. 6 groups. |
| `service_plan_inspect_title` / `_intro` / `_footnote` | Text | Heading, intro (HTML allowed), and the "* if applicable" note. |
| `service_plan_optional_title`, `service_plan_cta_title`, `service_plan_cta_body` | Text | |
| `service_support_note` | Text | Application Support tab note. |
| `service_locations` | Repeater | `anchor`, `name`, `address`, `phone`, `fax`. |

> **`items` is a textarea, not a nested repeater** because the legacy two-column layout is driven by `.t_left` / `.t_right` floats and the column is a per-group choice. Nested repeaters are not available here anyway.
>
> **`anchor` is load-bearing.** `assets/css/legacy.css` targets `#location_grand_rapids` and `#location_flat_rock`. The anchor is an explicit field precisely so it is *not* derived from the location name — deriving it with `sanitize_title()` produces `location_grand-rapids-mi` and silently stops those rules matching.

### Global forms — Site Content → Forms (2026-09-22)

The mailing-list form was duplicated across six templates with hardcoded strings:

| Field | Default |
|---|---|
| `signup_email_label` | Email address (visually hidden, read by screen readers) |
| `signup_email_placeholder` | your@email.com |
| `signup_submit_label` | Sign Up |

Read with `gerotech_field( 'signup_submit_label', 'Sign Up', 'option' )` — the third argument selects the options page.

### Other additions (2026-09-22)
`careers_col_job` / `_location` / `_department` / `_date` (careers table headers), `contact_page_title` + `contact_form_title`, `training_page_title`, `about_page_title`.

### Un-migrated databases (important)

ACF **injects a field's `default_value` on read** when a repeater row has no stored value. So a database that predates a newly-added field does not return an empty string — it returns the default. That would silently change the design on deploy (slide 1's Haas-red accent and button would both have turned white/orange).

The fix is to **give these fields no `default_value` at all** (`'default_value' => ''` + `'allow_null' => 1` + a `placeholder` naming the design default). A blank/absent value then genuinely means "the client has not chosen", so `front-page.php` can fall back in order:

1. the retired `accent_class` meta — read **raw** via `get_post_meta()`, because that field is no longer registered and so is absent from the ACF row array;
2. the original positional treatment — slide 1 Haas red, all others brand orange.

An earlier revision tried to solve this with `metadata_exists( 'post', $home_id, 'home_hero_slides_<i>_<field>' )` guards while leaving `default_value` in place. **That does not work** — ACF's injected default makes the value non-empty on read, so the guard never fires and the design silently regresses. The guards were removed; the empty default is the actual fix. Every interior `*_hero_accent_color` / `*_hero_cta_color` field follows the same pattern.

Verified across four states on Local: explicit values, un-migrated rows (legacy `accent_class` only), a **naive wp-admin save that leaves the select blank**, and an explicit White choice. All four render as designed. Re-seed with `scripts/seed-home-hero-colors.php` (idempotent, environment-agnostic) — note it lives in the repo's `scripts/` directory, which a theme-only push does **not** deliver, so it must be copied to the server before `wp eval-file`.

---

## 5. Field-group locations

| Group | Location rule |
|---|---|
| Site Content (Header / Navigation / Footer / Forms / Testimonials) | Options page `gerotech-site-content` |
| Homepage | `page_type == front_page` |
| Page content | `page_template` matches the page template |
| CTA / signup | Attached to each page group (not global) — copy differs per page |
