# Gerotech Website Prototype — Design Spec

**Presentation date:** July 7, 2026  
**Last updated:** August 17, 2026 (homepage Haas/news/testimonial refresh)  
**Client:** Gerotech — CNC Machinery Distributor + Engineering Solutions, Michigan  
**Build:** Static HTML/CSS/JS, no framework  
**Agent design directions:** `AGENTS.md` → **Design directions (2026-08-17)**

---

## Brand Tokens

| Token | Value |
|-------|-------|
| Primary orange | `#F38A2C` |
| Light orange | `#F9A94A` |
| Ink/black | `#0D0D0D` |
| Dark bg | `#1C1C21` |
| Dark nav/footer | `#3A3A40` |
| Gray body | `#808080` |
| Gray muted | `#9A9AA8` |
| Gray border | `#D6D6D6` |
| Gray card bg | `#F9F9FB` |
| Gray band | `#F2F2F2` |

**Fonts:**
- **Display (headlines):** Barlow Condensed — Google Fonts (`500` / `600` / `700`) → `--font-display`
- **Body / UI:** Navigo via Adobe Fonts — kit [`lqh7ybe`](https://use.typekit.net/lqh7ybe.css) → `--font-sans`

### Typekit — what to add (kit `lqh7ybe`)

**Currently enabled (4 faces):**
| Style | Weight | Use |
|-------|--------|-----|
| Regular | 400 | Body, descriptions |
| Regular Italic | 400 italic | Quotes (rare) |
| Bold | 700 | Headlines, buttons, emphasis |
| Bold Italic | 700 italic | — |

**Add this next (required for UI polish):**
| Style | Weight | Why |
|-------|--------|-----|
| **Medium** | **500** | Eyebrows, breadcrumbs, nav links, card labels, stat labels, `.mcs-name-split__lead` — stops everything collapsing to 400 vs 700 |

**Optional (only if you want more display range):**
| Style | Weight | Why |
|-------|--------|-----|
| Black | 900 | Hero numerals / stat pills only — use sparingly |

**Do not add:** Thin, ExtraLight, Light — too soft for industrial B2B.

**After adding Medium in [Adobe Fonts → Web Projects → lqh7ybe → Navigo](https://fonts.adobe.com/fonts/navigo):**
1. Republish the kit (Adobe does this automatically on save).
2. Hard-refresh the site — tokens already map `--fw-medium: 500`.
3. No CSS changes needed unless you add Black (then set `--fw-extrabold: 900`).

**Max-width:** Homepage 1200px | ES page 1120px  
**Border radius:** Cards 0 (squared) | Buttons 8px | Pills 99px

---

## Hero systems

### Homepage — peek hero (2026-08)

`.hero-slider.hero-slider--peek` — Figma Make pattern. Full-bleed `<img class="slide__bg">`, left gradient overlay, left-aligned copy. Controls: numeric index with SVG progress ring + dynamically built peek tab cards (not arrows/dots).

### Interior pages — `.page-hero` (2026-07-30)

All interior pages use **`.page-hero`** — the same structure as the homepage hero slide: full-bleed `<img class="slide__bg">`, left gradient overlay (`.slide__overlay--left`), left-aligned copy (`.slide__content--left` with `.slide__eyebrow`, `.slide__headline`, `.slide__body`). Per-page photos are set via the hero `<img src>` in each HTML file (Unsplash stand-ins until client assets). ES detail pages add optional `.page-hero__breadcrumb`.

| Page | Hero image (stand-in) |
|------|------------------------|
| Engineered Solutions | Blueprint / facility |
| About | Facility / training |
| Support | Service technician |
| Training | Training classroom |
| Machine Custom Solutions | Shop floor |
| Applications | CNC machining |
| Automation & Controls | Robotics |

**CTA bands:** Interior pages use `.cta-band--photo` (left-aligned). Homepage + most pages use `.cta-band--cinema-lockup` (copy-left lockup + optional call card on full-bleed photo).

## Primary CTA destinations (prototype)

| Action | Destination |
|--------|-------------|
| Get a Quote | `mailto:sales@gerotech.com?subject=Gerotech%20Quote%20Request` |
| Talk to an Engineer | `tel:+17343797788` |
| Service Request | `tel:+12484768787` |

---

## Phone Numbers (click-to-call)

| Label | Display | href |
|-------|---------|------|
| Headquarters & Sales | 734-379-7788 | `tel:+17343797788` |
| Service | 248-476-8787 | `tel:+12484768787` |
| Grand Rapids | 616-735-1100 | `tel:+16167351100` |

**Address:** 29220 Commerce Drive, Flat Rock, MI 48134

---

## Homepage Sections

**Current Figma:** canvas `6573:406` → home **`7306:1063`**. **Narrative flow:** Hero → stats → Haas intro → machine browse → testimonials → CTA → mailing list. (Editorial news is **not** on this handoff frame.)

| # | Section | Class / partial | Notes |
|---|---------|-----------------|-------|
| 1 | Alert banner | `.alert-banner` | Dark bar, 3 click-to-call numbers; collapses on scroll |
| 2 | Sticky header | `partials/site-header.html` | Logo · Machines + · Engineered Solutions + · Training · Support · About · Get a Quote · Search. WP Support is a service dropdown. |
| 3 | Peek hero | `.hero-slider--peek` | **min-height 500px** desktop (`min-width: 901px`); grows with the active slide's content. 3 slides. Figma `7306:1155` / slide 1 photo `7046:872` |
| 4 | Stat counter | `.stat-counter` | **39+** Years in Michigan, **14,000** Machines Placed |
| 5 | Haas Relationship | `.haas-relationship` | Eyebrow + intro + watermark + F1 lockup. **No** 4-col features band on `7306:1063` |
| 6 | Machine lineup | `.machine-lineup` `#machine-browse` | Dark section — 5 tabs + split panel. Default tab: Vertical Mills |
| 7 | Testimonials | `partials/testimonials-block.html` | `.testimonial-grid` — Rudisill / Ford / Kingbury |
| 8 | CTA band | `.cta-band--cinema-lockup` | Copy-left, **no phone lockup**. Photo `assets/images/cta-home-figma.jpg` |
| 9 | Email signup | `.email-signup` | Prototype thanks state on submit (`nav.js`) |
| 10 | Footer | `partials/site-footer.html` | 4-column, wired internal links |

### Hero slides (homepage)

| Slide | Eyebrow | Headline | Peek title | CTA | Image |
|-------|---------|----------|------------|-----|-------|
| 1 | A Division of Gerotech | Haas Factory Outlet | A Division of Gerotech | Explore the Haas Line → `#machine-browse` | `assets/images/hero-slide-1.jpg` |
| 2 | New Arrivals | Our Showroom Machines Are Ready To Ship | In-Stock & Ready | Browse Inventory → Haas showroom | `assets/images/hero-showroom.jpg` |
| 3 | Engineered Solutions | Automation Built for Your Shop Floor | On Our Floor | Explore Solutions | `assets/images/hero-automation-cell.jpg` |

Peek cards read `data-peek-eyebrow` / `data-peek-title` from each `.slide`. Peek 01 stays `A Division of Gerotech`; peek 03 is `On Our Floor` (Figma PM 2026-09-30); slide 2 **headline** is the later client line.

### Haas Relationship capability grid (Figma `7080:2240` — inverted light band)

Full-bleed **white** `.haas-relationship__features` band: dark ink titles, gray body copy, muted labels. Icon tiles on `--clr-gray-card` with black SVGs. Vertical hairline dividers `rgba(0,0,0,0.08)`.

Intro area (Figma `7080:1405`): static Haas wordmark watermark at ~5% opacity behind copy only — layered via `.haas-relationship__bg` / `.haas-relationship__content`.

| # | Label | Title |
|---|-------|-------|
| 01 | Sales | The full Haas line, one source. |
| 02 | Application Support | Factory-backed engineering. |
| 03 | Warranty | Coverage owned locally. |
| 04 | Service & Parts | Factory-trained technicians. |

Icons: `assets/images/icons/haas-rel-{sales,apps,warranty,service}.svg` · Watermark: `assets/images/haas-wordmark-watermark.svg`

### Machine lineup tabs (Figma 7043:223)

| Tab | Panel title | CTA |
|-----|-------------|-----|
| Machining Centers | Vertical Mills | View All Mills → `gerotech.com/machines` |
| Turning Centers | CNC Lathes | View All Lathes → `gerotech.com/machines` |
| 5-Axis | 5-Axis Machines | View All 5-Axis → `gerotech.com/machines` |
| Automation | Pallet Systems & Robots | View Automation → `gerotech.com/machines` |
| Haas Tooling | Tooling & Workholding | View Haas Tooling → `gerotech.com/machines` |

Default panel photo: `assets/images/haas-umc-750.jpg`. Tab switching: `machine-tabs.js`.

### Machine browse cards (legacy — replaced 2026-08-07)

| Card | Category | Models (sample) |
|------|----------|-----------------|
| Vertical Mills | Machining Centers | Mini Mill, VF Series, VR Series, UMC 5-Axis |
| CNC Lathes | Turning Centers | ST Series, TL Series, DS, Toolroom Lathe |
| 5-Axis Machines | 5-Axis | UMC-500, UMC-750, DM-2, EC-400 HH |
| Pallet & Robots | Automation | FANUC Robots, Pallet Changers, Cobots |

### Removed from homepage (2026-08)

| Former section | Replaced by |
|----------------|-------------|
| Trust strip (Since 1987, 3 locations, Haas FFO, FANUC ASI) | Stat counter + Haas Relationship |
| Intro + 3 category cards (Machines / ES / Support) | Hero slide 3 + Haas Relationship + machine browse |
| Machine browse tabs (5 categories + model tags) | 4-card `.machine-cards` grid |

### Homepage — open cleanup (pre-production)

| Item | Status |
|------|--------|
| Hero slides 2–3 Figma/client photos | ⚠️ Unsplash stand-ins |
| Stat claims (#1 Midwest, 4,000+ machines) | ⚠️ Client sign-off |
| Dedicated news page + Show More link | ⚠️ Button removed until page exists |
| Testimonial slides 2–3 client photos | ⚠️ Unsplash stand-ins |
| Navigo Medium (500) in Adobe kit | ⚠️ Recommended for eyebrows/labels |
| FANUC ASI above fold on homepage | ⚠️ Client decision — was in old trust strip |
| `machine-tabs.js` | Legacy file — not loaded on homepage |

**Figma file:** `YgHwqyyFj57c1ZSbmfkL0c` (Gerotech-Design)

---

## ES Page Sections

1. Alert banner (same)
2. Sticky header (same, "Engineered Solutions" active)
3. Hero — left-aligned photo, full-bleed bg, H1 "Your Manufacturing Solutions Partner", 2 CTAs
4. Why Gerotech — 2-col, copy + CTA left, 3 feature rows right
5. Stat strip — gray, 4 pill cards
6. ES grid — 3×3, 8 service cards + 1 placeholder, filterable
7. FANUC ASI band — dark, badge + copy
8. Technology Partners — 2-col, 9 wordmark grid
9. Capability band — boxed numbered cards (01–03)
10. Testimonials — 2 bordered cards
11. News feed (same pattern)
12. CTA band — left-aligned photo hero
13. Footer — dark (#0D0D0D)

---

## Service Cards

| Card | Category | data-category |
|------|----------|--------------|
| Hydraulic Additions | Machine Modification | `machine-modification` |
| Pneumatic Additions | Machine Modification | `machine-modification` |
| Auto-Doors | Machine Modification | `machine-modification` |
| Workholding | Machine Modification | `machine-modification` |
| Broken Tool Detection | Machine Modification | `machine-modification` |
| Remote Tool Offset | Machine Customization | `machine-customization` |
| Simulation (ROBOGUIDE) | Automation Integration | `automation-integration` |
| Robot Selection | Automation Integration | `automation-integration` |

---

## Image Placeholders

Most images are **Unsplash stand-ins** (`<img>` tags with HTML comment crediting source). Client/Figma assets live in `assets/images/`. When client provides final assets:

- Replace `src` URL with client file in `assets/images/` (or CMS path in WordPress build)
- Keep `alt` text descriptive; preserve `loading="lazy"` / `decoding="async"` where present
- **Verify Unsplash URLs** before demos — photo IDs can 404 over time

### Client / Figma assets (homepage)

| File | Use |
|------|-----|
| `assets/images/hero-slide-1.jpg` | Hero slide 1 background |
| `assets/images/haas-umc-750.jpg` | Machine lineup — Machining Centers panel |
| `assets/images/haas-f1-team.jpg` | Haas Relationship brand card |
| `assets/images/gerotech-logo.svg` / `gerotech-logo-white.svg` | Header / footer |

### Known URL swaps (2026-07-14)

| Replaced (404) | Current stand-in | Typical use |
|----------------|------------------|-------------|
| `photo-1713869791526-ba21b52e5528` | `photo-1647427060118-4911c9821b82` | Facility, training, CTA |
| `photo-1565903020905-e53d59a4040b` | `photo-1716191299980-a6e8827ba10b` | Automation, robotics, news |

Affected files: `index.html`, `application.html`, `machine-custom-solutions.html`, `automation-integration.html`, `assets/css/elevated.css` (hero bg URLs).

## Site-wide UX (prototype)

| Feature | Implementation |
|---------|----------------|
| Search | Header button → modal with quick links (`nav.js`) |
| Email signup | Prototype thanks message on submit (`nav.js`) |
| Sticky header | Blur + shadow on scroll (`elevated.css` + `nav.js`) |
| Alert banner | Collapses on scroll |
| Homepage hero | Peek cards + 10s autoplay + orange progress ring (`slider.js`) |
| Stat counter | Count-up on scroll into view (`stat-counter.js`) |
| Footer socials | Hidden until client confirms URLs |

---

## File Structure

```
gerotech-prototype/
├── index.html
├── engineered-solutions.html
├── machine-custom-solutions.html
├── automation-integration.html
├── application.html
├── training.html
├── support.html
├── about.html
├── machine-modification.html   ← redirect stub → MCS
├── partials/
│   ├── site-header.html        ← alert, nav, search modal
│   └── site-footer.html
├── design-spec.md
├── AGENTS.md / CLAUDE.md
├── JOURNAL.md / .clinerules
└── assets/
    ├── css/
    │   ├── tokens.css          ← Design tokens
    │   ├── components.css      ← UI atoms + BEM components
    │   ├── layout.css          ← Grids, sections, responsive
    │   └── elevated.css        ← Polish layer (heroes, machine browse, hovers)
    ├── js/
    │   ├── include-partials.js
    │   ├── nav.js              ← Sticky, mobile, search, signup
    │   ├── slider.js           ← Homepage peek hero (autoplay + progress ring)
    │   ├── stat-counter.js     ← Homepage stat count-up
    │   ├── filter.js           ← ES grid filter tabs
    │   ├── animations.js       ← Scroll reveal
    │   ├── machine-tabs.js     ← Homepage machine lineup tabs
    │   └── modal.js            ← MCS / Automation card modals
    └── images/
        ├── gerotech-logo.svg
        ├── gerotech-logo-white.svg
        ├── haas-f1-team.jpg
        ├── haas-wordmark-watermark.svg
        ├── hero-slide-1.jpg
        └── …                    ← see handoff/asset-manifest.md for the full list
```
