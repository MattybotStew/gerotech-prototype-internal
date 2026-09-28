# Gerotech Website Prototype — Project Brief

> Shared reference for AI agents (Cursor, Cline, Claude, etc.). Keep updated as the project evolves.

## Overview

Static HTML/CSS/JS **presentation prototype** for **Gerotech, Inc.** — Michigan-based CNC machinery distributor & engineering solutions provider (serving manufacturers since 1987). Built for the **July 7, 2026** client presentation.

**Current stage: WP child theme live on Dev (2026-09-18).** `gerotechdev.wpenginepowered.com` runs **Gerotech Child**. Git `master` @ `ba259ca`. Homepage Figma **`7306:1063`**. F1 card `haas-f1-team.jpg`. **Always read `.clinerules` → Next session — pickup here.**

**Live site:** https://gerotech.com/ (WordPress — reference only; prototype is ahead of live ES hub)

**Agency:** CloudMellow (Matt)

**Repo:** https://github.com/MattybotStew/gerotech-prototype — branch **`master`** (synced with origin, 2026-07-14)

## Pages (11 HTML)

| Page | File | Status |
|------|------|--------|
| Homepage | `index.html` | ✅ Hero slider, stat counter, Haas Relationship, machine lineup, testimonial grid, editorial news |
| Engineered Solutions | `engineered-solutions.html` | ✅ Full hub: stats, grid, FANUC band, tech partners, capability cards, trust FAQ |
| Machine Custom Solutions | `machine-custom-solutions.html` | ✅ Photo hero, 8 service cards, Installed Projects gallery, modals |
| Automation & Controls | `automation-integration.html` | ✅ Photo hero, services, Installed Projects gallery, modals |
| Applications | `application.html` | ✅ Photo hero, 4 application services, modals |
| Training | `training.html` | ✅ Photo hero, 4 Haas courses |
| Support | `support.html` | ✅ Photo hero, 4 support option cards (SVG icons) |
| About | `about.html` | ✅ Photo hero, story, stats, locations |
| Machine Modification | `machine-modification.html` | ✅ Redirect → MCS |
| Showroom | `showroom.html` | 🔵 Exploratory variant page |

Shared: `partials/site-header.html`, `partials/site-footer.html`, `partials/testimonials-block.html`

## Architecture

```
gerotech-prototype/
├── index.html + 8 interior pages (see table)
├── partials/site-header.html    # Alert, nav, mega-menu, search modal
├── partials/site-footer.html
├── AGENTS.md / CLAUDE.md        # LLM instructions
├── JOURNAL.md / .clinerules     # Session sync (read every session)
├── design-spec.md               # Design tokens + section inventory
├── cline-project-handoff.md     # Historical context + live site audit
├── assets/
│   ├── css/
│   │   ├── tokens.css           # Design tokens
│   │   ├── components.css       # BEM components (~2500 lines)
│   │   ├── layout.css           # Grids, sections, responsive
│   │   └── elevated.css         # Polish: heroes, hovers, trust strip
│   ├── js/
│   │   ├── include-partials.js
│   │   ├── nav.js               # Sticky, mobile, search modal, signup thanks
│   │   ├── slider.js            # Homepage hero carousel
│   │   ├── filter.js            # ES grid filter tabs
│   │   ├── animations.js        # Scroll reveal
│   │   ├── testimonials.js      # Testimonial carousels
│   │   ├── machine-tabs.js      # Homepage machine browse
│   │   └── modal.js             # MCS / Automation card modals
│   └── images/
│       ├── gerotech-logo.svg
│       └── gerotech-logo-white.svg
└── docs/
    ├── PROJECT_BRIEF.md         # ← This file
    └── superpowers/plans/       # Original implementation plan (historical)
```

## Navigation Structure (Desktop)

```
Machines ↗ | Engineered Solutions ▼ | Training | Support ▼ | About ▼ | [Get a Quote] [Search]
```

- **Machines** — mega-menu (Haas catalog links still `#` pending); external catalog link
- **Engineered Solutions** — mega-menu with MCS / Application / Automation + CTA
- **Training** — standalone link → `training.html`
- **Support** — dropdown: Service, Parts, Documentation
- **About** — dropdown: Our Story, Team, Careers, Contact
- **Get a Quote** — `mailto:sales@gerotech.com`
- **Search** — opens prototype modal with quick links

## Design System

**CSS custom properties in `tokens.css`** — all color/spacing changes go there first.

| Token | Value |
|-------|-------|
| Primary orange | `#F38A2C` |
| Light orange | `#F9A94A` |
| Ink/black | `#0D0D0D` |
| Dark bg | `#1C1C21` |
| Dark nav/footer | `#3A3A40` |

**Font:** Navigo via Adobe Fonts kit [`lqh7ybe`](https://use.typekit.net/lqh7ybe.css) — **400 + 700** in kit; **add Medium (500)** for UI hierarchy (tokens ready)

**Max-width:** Homepage 1200px | ES + detail pages 1120px  
**Border radius:** Cards **0** | Buttons 8px | Pills 99px  
**Breakpoints:** 1024px, 1000px (nav collapse), 768px, 480px

### Hero pattern (site-wide)

Full-bleed Unsplash photo + **left gradient overlay** (105°) + **left-aligned** white copy. Mobile ≤768px: vertical gradient, centered copy.

| Page | Classes |
|------|---------|
| Homepage | `.hero-slider` / `.slide__content--left` |
| All interior pages | `.page-hero` + `.slide__bg` / `.slide__overlay--left` / `.slide__content--left` |
| CTA bands (all pages) | `.cta-band--photo` (same treatment) |

Per-page hero photos are set via `<img class="slide__bg">` in each HTML file.

## CSS Loading Order

1. `tokens.css`
2. `components.css`
3. `layout.css`
4. **`elevated.css`** — always last

## JavaScript by Page

| Page | Scripts |
|------|---------|
| All pages | `include-partials.js`, `nav.js` |
| Homepage | + `slider.js`, `animations.js`, `testimonials.js`, `machine-tabs.js` |
| ES hub | + `filter.js`, `animations.js`, `testimonials.js` |
| MCS, Application, Automation | + `animations.js`, `modal.js` |
| Other interior | + `animations.js` |

## Primary CTAs (wired)

| Action | Destination |
|--------|-------------|
| Get a Quote | `mailto:sales@gerotech.com?subject=Gerotech%20Quote%20Request` |
| Talk to an Engineer | `tel:+17343797788` |
| Service | `tel:+12484768787` |

## Open / Blocked (do not guess)

| Item | Status |
|------|--------|
| FANUC official logo | Placeholder badge only — legal TBD |
| Mega-menu machine URLs (~40) | `#` pending Haas catalog structure |
| Nav label "Machine Customization" vs page "Custom Solutions" | **Resolved 2026-09-22: "Machine Custom Solutions"** everywhere; legacy URL `/modification-of-standard-machine-tools/` retained |
| News/blog URL | Footer → about.html temporarily |
| Privacy / Terms URLs | Placeholder → about.html |
| Social icons | Hidden until URLs confirmed |
| Constant Contact signup | Prototype thanks state only |
| Client photography | Unsplash stand-ins — verify URLs before demos; see `design-spec.md` for 2026-07-14 URL swaps |

## Key Source Files

- `.clinerules` — **Read first every session** — live state + blockers
- `JOURNAL.md` — Chronological change log
- `design-spec.md` — Tokens, sections, Typekit guidance
- `cline-project-handoff.md` — Live site audit, timeline, Figma comment history
- `AGENTS.md` / `CLAUDE.md` — Stack + conventions for LLMs

## Key Conventions

- **BEM** naming (`.service-card__category`)
- **ARIA** throughout; skip link → `#main`
- **External links:** `target="_blank" rel="noopener noreferrer"`
- **Phone:** `tel:+1` E.164
- **Address:** 29220 Commerce Drive, Flat Rock, MI 48134
- **Never** use real FANUC logo until legal approval
- **Never** change CSS load order
- **Never** hardcode brand colors outside `tokens.css`

## Local Dev

```bash
cd gerotech-prototype
python3 -m http.server 8080
# → http://localhost:8080/
```

If port hangs: `kill $(lsof -t -iTCP:8080 -sTCP:LISTEN)` and restart.

VS Code: **Terminal → Run Task → Serve Gerotech (8080)**
