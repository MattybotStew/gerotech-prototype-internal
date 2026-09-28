# Theme map — HTML → PHP

**Companion to:** `implementation-plan-wordpress-theme-acf.md`
**Source of truth:** tagged prototype `v1.0-prototype`
**Target:** `wp-content/themes/gerotech-child/` (child vs. new pending Phase 1)

---

## 1. Pages

Each prototype page becomes a `page-{slug}.php` template (auto-applies by slug, no assignment). Fall back to a named `Template Name` where the slug collides with an existing live CPT (`/training/`, `/support/` already exist live).

| Prototype file | WP template | Notes |
|---|---|---|
| `index.html` | `front-page.php` | Homepage |
| `engineered-solutions.html` | `page-engineered-solutions.php` | |
| `machine-custom-solutions.html` | `page-machine-custom-solutions.php` | |
| `automation-integration.html` | `page-automation-integration.php` | |
| `application.html` | `page-application.php` | |
| `training.html` | `page-training.php` | Slug collides live → named template if needed |
| `support.html` | `page-support.php` | Slug collides live → named template if needed |
| `about.html` | `page-about.php` | |
| `careers.html` | `page-careers.php` | |
| `machine-modification.html` | — | Canonical redirect to MCS; recreate as 301 in WP, not a template |

Not in scope: `showroom.html`, `gallery-module-preview.html` (exploratory/preview, stay out of the theme). `hero-variations.html` and `index-cta-lockup-preview.html` were pruned 2026-09-28.

---

## 2. Partials → theme files

| Prototype | WP |
|---|---|
| `<div data-include="partials/site-header.html">` | `<?php get_header(); ?>` → `header.php` |
| `<div data-include="partials/site-footer.html">` | `<?php get_footer(); ?>` → `footer.php` |
| `<div data-include="partials/testimonials-block.html?v=…">` | `get_template_part('template-parts/sections/testimonials')` |
| `assets/js/include-partials.js` | **Deleted** — PHP includes replace it |

`header.php` contains: alert banner + sticky site header + desktop mega-nav + mobile nav + search modal (all currently inside `partials/site-header.html`).

---

## 3. Section markup → template parts

Sections are copied **verbatim**; only strings/images become ACF calls.

| Section root (BEM) | Template part | Used on |
|---|---|---|
| `.hero-slider.hero-slider--peek` | `sections/hero-peek` | Homepage |
| `.stat-counter` | `sections/stat-counter` | Homepage |
| `.haas-relationship` | `sections/haas-relationship` | Homepage |
| `.machine-lineup` | `sections/machine-lineup` | Homepage |
| `.page-hero` | `sections/page-hero` | ES, MCS, Automation, Applications, Training, Support |
| `.page-hero-trust` | `sections/page-hero-trust` | About, Careers |
| `.why-section`, `.credential-band`, `.tech-partners-section`, `.capability-band`, `.trust-section` | `sections/*` | ES |
| `.mcs-grid-section` (service cards) | `sections/service-grid` | MCS, Automation, Applications, Training, Support, About, Careers |
| `.mcs-gallery-section` | `sections/gallery` | MCS, Automation, Applications |
| `.news-section--editorial` | `sections/news-editorial` | ES |
| `.testimonial-grid` | `sections/testimonials` | All pages (shared partial) |
| `.cta-band--cinema-lockup` | `sections/cta-band` | All pages |
| `.email-signup` | `sections/email-signup` | All pages |
| `.site-footer` | `footer.php` | All pages |

Card partials: `cards/service-card`, `cards/mcs-card`, `cards/news-item`, `cards/testimonial-card`.

---

## 4. CSS

Load order is fixed and must be preserved:

1. `assets/css/tokens.css` — design tokens (colors, type, spacing). **Only place brand colors live.**
2. `assets/css/components.css`
3. `assets/css/layout.css`
4. `assets/css/elevated.css` — elevation/photography layer

Enqueue in that order via `inc/enqueue.php`; dequeue the parent theme stylesheet if child.

---

## 5. Scripts

| Script | Templates |
|---|---|
| `include-partials.js` | **Deleted** in port |
| `nav.js` | All pages (header) |
| `animations.js` | All pages |
| `slider.js` | Homepage |
| `stat-counter.js` | Homepage |
| `machine-tabs.js` | Homepage |
| `modal.js` | MCS, Automation, Applications |
| `filter.js` | Not currently linked on live pages — confirm before enqueue |

Conditional enqueue per template (see `js-spec.md`).

---

## 6. Conversion rules

- `<img src="...">` → `get_field()` + `wp_get_attachment_image()`; keep `width`/`height`/`alt`/`loading`.
- Repeated lists → ACF repeater; headings/accent words → WYSIWYG with `em` → `.accent`.
- Nav + footer column links → hardcoded in `header.php` / `footer.php` for v1 (not client-editable).
- `tel:` links stay E.164 (`tel:+17343797788`).
- External machine links (`haascnc.com`, `gerotech.com/machines`) open in a new tab with `rel="noopener noreferrer"`.
