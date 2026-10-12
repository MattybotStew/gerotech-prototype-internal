# JS spec

All scripts are plain ES5/ES6 IIFEs, no bundler, no dependencies. Enqueue conditionally per template via `inc/enqueue.php`.

---

## `include-partials.js` — **DELETE IN PORT**

Fetches `[data-include]` partials at runtime and re-runs `window.initNav()` + `initTestimonialCarousels()`, then fires `partials:loaded`.

- Replaced by native PHP `get_header()` / `get_footer()` / `get_template_part()`.
- Delete the file and remove the `data-include` wrapper divs.
- `nav.js` currently self-inits (`window.initNav()` at the bottom) — in the port it can just run on load.

## `nav.js` — all pages

| Selector | Behaviour |
|---|---|
| `.site-header`, `.alert-banner` | sticky state on scroll (passive listener) |
| `.mobile-toggle`, `.mobile-nav` | mobile menu toggle, `aria-expanded` |
| `.header-search`, `.search-modal` | open/close modal; backdrop + close button; Esc; focus trap (Tab/Shift+Tab); makes `#main`/header/footer `inert` while open |
| `.email-signup__form` | intercept submit → show `.email-signup__thanks` state |
| document keydown/click | Esc closes nav; outside-click closes mobile menu |

- Exposes `window.initNav` (called by include-partials; call directly after port).
- **A11y:** focus trap + inert background on search modal. Preserve.

## `animations.js` — all pages

- Scroll reveal via `IntersectionObserver`; targets: `.category-card, .news-card, .service-card, .partner-wordmark, .why-feature, .capability-card, .capability-bullet, .section-header, .machine-lineup__header, .cta-band__card, .testimonial-card, .credential-band__inner, .tech-partners-section__inner, .mcat-card, .mcs-card, .mcs-gallery-card, .trust-strip__item, .machine-panel, .news-feature, .news-item`.
- Bails early if `prefers-reduced-motion: reduce`. Staggered 60ms per batch.
- Sets inline opacity/transform — note this will fight ACF content if reordered; keep markup order.

## `accordions.js` — all pages

- **One open drawer per group.** A `toggle` listener (capture phase — the event does not bubble, but capture still reaches `document`) closes every other open `<details>` that shares the opened element's parent.
- Scoped to containers marked `data-accordion="single"`. Currently: `.trust-accordion` (ES FAQ, `page-engineered-solutions.php`) and `.mcs-modal__detail` (`footer.php` — the modal's accordion markup lives in stored ACF, so the rule is enforced at runtime and needs no re-seed).
- Relies on the element's own parent, so nested `<details>` are untouched.
- **Excludes** the mobile-nav disclosure groups (`.mobile-nav__group`), which are navigation and stay independently openable.
- To add a new accordion: mark its container `data-accordion="single"`. No JS change.

## `slider.js` — homepage only

- Binds every `.hero-slider`; homepage is `.hero-slider--peek`.
- Autoplay 10s (peek) / 6s (classic); progress ring; `renderPeeks()` builds peek tabs from `data-peek-eyebrow` / `data-peek-title` on `.slide`.
- **A11y:** off-screen slides get `aria-hidden="true"` and `tabindex="-1"` on links/buttons; dots use `aria-selected`.
- No `prefers-reduced-motion` guard on autoplay — consider adding in port.

## `stat-counter.js` — homepage only

- Animates `[data-count]` (supports `data-suffix`, `data-prefix`, `data-duration`, `data-decimals`) when 35% visible.
- Honors `prefers-reduced-motion` (prints final value instantly).

## `machine-tabs.js` — homepage only

- Click on `.machine-tab` toggles `.is-active` + `aria-selected`; shows the `data-target` panel, hides others (`hidden`).
- **One instance per page** — assumes a single tablist.

## `modal.js` — MCS, Automation, Applications

Two modules in one file:

1. **Card detail modal** — `.mcs-card[aria-haspopup="dialog"]` → `.mcs-modal`; fills title/image/detail from the card + its `<template>`; scroll lock; focus trap; Esc; backdrop.
2. **Gallery lightbox** — `.mcs-gallery-card` → `.gallery-lightbox`; prev/next (buttons + arrow keys + swipe), thumbnails, count/meta; focus restore; `prefers-reduced-motion` disables the fade.

- **One instance per page.**
- If the gallery module (multi-image + video) is later approved, it introduces a **namespaced** `.gallery-viewer*` module (see `assets/js/gallery-module.js`) rather than editing `modal.js`.

## `filter.js` — **not currently linked on any page**

- Toggles `.service-card[data-category]` visibility by `.filter-tab[data-filter]`.
- ES name-split filter was removed in an earlier pass. **Confirm before enqueue**; if unused, drop from the theme.

---

## Enqueue matrix

| Script | Home | ES | MCS | Automation | Applications | Training | Support | About | Careers |
|---|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|
| `nav.js` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| `animations.js` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| `slider.js` | ✓ | | | | | | | | |
| `stat-counter.js` | ✓ | | | | | | | | |
| `machine-tabs.js` | ✓ | | | | | | | | |
| `modal.js` | | | ✓ | ✓ | ✓ | | | | |
| `filter.js` | — | — | — | — | — | — | — | — | — |

## Port rules

- Load scripts in `footer.php` or with `wp_enqueue_script(..., $in_footer = true)`.
- No AJAX re-init needed (no builder) — scripts run once on load as prototyped.
- Keep the one-instance-per-page assumptions documented above; if a page gains a second instance, refactor to per-element init.
