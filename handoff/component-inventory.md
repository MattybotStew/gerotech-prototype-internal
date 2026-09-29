# Component inventory

Section → BEM root → editable fields → JS dependency. One row per repeating component.

| Section | BEM root | Editable fields | JS |
|---|---|---|---|
| Alert banner | `.alert-banner` | `header_banner_items` — label + value, `tel:` derived | `nav.js` (collapse on scroll) |
| Site header | `.site-header` | `header_logo`, `header_logo_alt`, `header_logo_url`, `header_cta_label`, `header_cta_url` | `nav.js` (sticky, mobile toggle, search modal) |
| Desktop nav | `.site-nav` | `nav_items` — label, url, new_tab, style, show_mobile, sub-links | `nav.js` |
| Mega-nav (Machines) | `.mega-nav--machines` | `nav_machines_groups` (title, column, mobile_order, links), help card, footer link (+ mobile label) | `nav.js` |
| Mega-nav (ES) | `.mega-nav--es` | `nav_es_col1_title`, `nav_es_categories`, `nav_es_services` (incl. per-link mobile label), `nav_es_cta_*`, `nav_es_col2_title` | `nav.js` |
| Mobile nav | `.mobile-nav` | **derived from `nav_items` + both panels** — no separate fields | `nav.js` |
| Search modal | `.search-modal` | `header_search_title` / `_hint` / `_placeholder`, `header_search_links` | `nav.js` (focus trap, inert) |
| Hero peek slider | `.hero-slider--peek` | eyebrow, headline, body, CTA, image, badge | `slider.js` |
| Stat counter | `.stat-counter` | value, label | `stat-counter.js` |
| Haas relationship | `.haas-relationship` | eyebrow, headline, lede, brand logo, features (4) | `animations.js` |
| Machine lineup | `.machine-lineup` | tabs + 5 panels (photo, badge, cat, title, desc, tags, CTA) | `machine-tabs.js` |
| Page hero | `.page-hero` | eyebrow, headline, body, CTA, image, breadcrumb | `animations.js` |
| Page hero (trust) | `.page-hero-trust` | eyebrow, headline, body, 4 stats, image | `animations.js` |
| Why section (ES) | `.why-section` | 3 features (icon, title, body) | `animations.js` |
| Credential band (ES) | `.credential-band` | FANUC ASI seal + copy | `animations.js` |
| Tech partners (ES) | `.tech-partners-section` | wordmarks | `animations.js` |
| Capability band (ES) | `.capability-band` | 3 cards (01–03) | `animations.js` |
| Trust / FAQ (ES) | `.trust-section` | FAQ items | `animations.js` |
| Service grid | `.mcs-grid-section` | cards: eyebrow, title, body, image, modal body | `animations.js`, `modal.js` |
| Gallery | `.mcs-gallery-section` | tiles: image, alt, caption | `modal.js` (lightbox) |
| News editorial | `.news-section--editorial` | lead group + items 02–04 | `animations.js` |
| Testimonials | `.testimonial-grid` | quote, name, role (global ACF) | `animations.js` |
| CTA band (lockup) | `.cta-band--cinema-lockup` | eyebrow, headline, body, button, optional call card, image | `animations.js` |
| Email signup | `.email-signup` | title (accent), sub, form | `nav.js` (thanks state) |
| Footer | `.site-footer` | `footer_logo`(+`_alt`), `footer_tagline`, `footer_address`, `footer_phone`, `footer_socials`, `footer_columns` (max 3), `footer_copyright_text`, `footer_legal_links` | — |

## Cards

| Card | BEM root | Fields | JS |
|---|---|---|---|
| Service card | `.service-card` | category, title, body, image, modal body | `modal.js` |
| MCS/machine card | `.mcs-card` | title, image, modal body | `modal.js` |
| News item | `.news-item` | index, tag, date, title, excerpt, thumb | — |
| News feature | `.news-feature` | tag, date, title, excerpt, image, 3 stats | — |
| Testimonial card | `.testimonial-card` | quote, name, role | `animations.js` |
| Model tag | `.model-tag` | label, url | — |

## One-instance-per-page assumptions (do not break in the port)

- `machine-tabs.js` binds a single tablist (`#machine-browse`).
- `modal.js` assumes one modal/lightbox instance set per page.
- `slider.js` binds the single `.hero-slider` (homepage only).
- `stat-counter.js` binds the single `.stat-counter` (homepage only).
- `animations.js` runs site-wide; targets `.testimonial-card`, `.news-item`, etc.
