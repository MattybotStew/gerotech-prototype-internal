# Asset manifest

**Classification:** **Bundle** = shipped with the theme (brand assets, rarely change). **Media Library** = uploaded content, referenced by ACF image fields.

---

## 1. Theme-bundled (brand) — `assets/images/`

| File | Use | Status |
|---|---|---|
| `gerotech-logo.svg` | Header logo | Final |
| `gerotech-logo-white.svg` | Footer logo | Final |
| `haas-f1-team.jpg` | Haas Relationship brand card (WP Engine-safe JPEG). Replaced the legacy `haas-f1-lockup.png`, which 403'd on WP Engine and was pruned 2026-09-28. | Final |
| `haas-wordmark-watermark.svg` | Haas Relationship watermark (~5% opacity) | Final |
| `fanuc-asi-seal.png` | ES credential band (clipped circle) | ⚠️ FANUC usage rights unconfirmed |
| `haas-winners-circle.png` | Haas Tooling lineup panel (white) | Final |

## 2. Media Library — client content photos

| File | Used on |
|---|---|
| `es-hero.jpg` (500KB — was a 2.8MB PNG, converted 2026-09-21) | ES page-hero |
| `cta-rail-robot.jpg` + `@2x` (484KB / 740KB — client FANUC rail-robot photo, 2026-09-22; renamed from `cta-engineered-solutions` when it gained a second consumer) | **Engineered Solutions AND Automation & Controls** bottom CTA bands — deliberately shared |
| `mcs-hero.jpg` + `@2x` (444KB / 660KB — client 5-axis machining-center interior, 2026-09-22) | Machine Custom Solutions page-hero — in WP this hero is rendered by **`page-modification-of-standard-machine-tools.php`** (ACF `mcs_hero_image`), i.e. `/modification-of-standard-machine-tools/` on Dev, which displays the "Machine Custom Solutions" headline |
| `mcs-gallery/specialty-machine.jpg` (1920×670, 245KB — client Haas ST-45 + bar feeder, from Figma node `7196:3348`, 2026-09-22) | MCS "Specialty Machine" service card |
| `mcs-gallery/custom-workholding.jpg` (1012×1800, 420KB — client fixture holding a welded subframe, from Figma node `7196:3332`, 2026-09-22) | MCS "Custom Workholding" service card |
| `app-troubleshooting.jpg`, `app-tooling.jpg`, `app-demo.jpg` (1600px — bundled stand-ins, 2026-09-22) | Applications gallery collections (cover + lightbox) AND the matching service cards |
| `hmi-design.jpg` (1600×746, 398KB — **client photo**, 2026-09-22) | Automation **HMI Design** card — Gerotech operator HMI screen |
| `layered-controls.jpg` (1600×800, 172KB — **client graphic**, 2026-09-22) | Automation **Layered Controls Solutions** card — the 3-layer architecture diagram |
| `app-gallery-milling-coolant.jpg`, `app-gallery-turning-large.jpg`, `app-gallery-turning-drill.jpg` (1600px, 220–272KB — **client photos**, 2026-09-22) | Applications **Part Programming** gallery collection (in-machine milling and turning shots) |
| `app-optimization.jpg` (1600×745, 194KB — **client graphic**, 2026-09-22) | Applications **Process Optimization** card + gallery. Branded cycle diagram: cycle-time reduction / part quality / tooling performance. |
| `app-training.jpg` (1600×745, 271KB — **real client photo**, 2026-09-22) | Applications **Training** card + its gallery collection. Client shot of a Gerotech instructor walking a customer through a Haas control. |
| `app-hero.jpg` (1920×1280, 416KB — bundled stand-in, 2026-09-22) | Applications **page header AND CTA band** — the client asked for the same picture in both |
| `mcs-gallery/custom-fixtures.jpg` (1350×1800 — fixture plates on the floor) | MCS **gallery collection** "Custom Fixture Design" (cover + lightbox) — no longer the service card |
| `automation-hero.jpg` | Automation page-hero (Figma `7196:4264`) |
| `automation-cell-design.jpg` | Automation **Automation Cell Design** card (Figma `7196:4308`) |
| `robot-eoat.jpg` | Automation **Robot EOAT** card (Figma `7196:4316`) |
| `pre-engineered-card.jpg` (906×1024, 254KB — client photo, 2026-09-28) | Automation **Pre-Engineered Solutions** card (close-up) |
| `pre-engineered-gallery.jpg` (900×1024, 259KB — client photo, 2026-09-28) | Automation **Pre-Engineered Solutions** gallery collection (wide shop-floor shot) |
| `app-gallery-umc750.jpg` | Applications card + gallery |
| `haas-umc-750.jpg` | Homepage lineup — machining centers |
| `haas-st-25y.jpg` | Homepage lineup — turning |
| `hero-showroom.jpg`, `hero-automation-cell.jpg` | Homepage hero slides (slides 2–3) |
| `mcs-gallery/*.jpg` (13) | MCS gallery + service cards |
| `automation-gallery/hmi-*.jpg`, `layered-controls-diagram.jpg` | Automation gallery photos. Live collections match the five service cards only: HMI Design, Layered Controls Solutions, Automation Cell Design, Robot EOAT, Pre-Engineered Solutions. The older installed-project JPEGs (`01`–`07`) were pruned 2026-09-28 — they were referenced nowhere. |

> Verify each against client-provided originals; some bundled files are downloaded stand-ins, not final client art.

## 3. Unsplash stand-ins — remote URLs (MUST be replaced before launch)

**The WordPress build is now at ZERO.** Every page on Dev renders entirely from local assets or the media library (verified site-wide 2026-09-22, after the Applications gallery migration). The remaining stand-ins below are **prototype-only** — the prototype is the design source and its placeholders are still swapped by hand, so the two can drift.

| Page | Count |
|---|---|
| `index.html` | 2 |
| `engineered-solutions.html` | **0 ✅** |
| `machine-custom-solutions.html` | 1 |
| `automation-integration.html` | 4 |
| `application.html` | **0 ✅** |
| `training.html` | 6 |
| `support.html` | 2 |
| `about.html` | 4 |
| `careers.html` | 3 |
| `showroom.html` *(exploratory)* | 11 |
| **Total** | **33** |

`engineered-solutions.html` and `application.html` are now fully local. Applications was the last page on the **live** site depending on someone else's servers — its five gallery collections (cover + lightbox) were on Unsplash while its cards were already local media attachments, so the fix was the gallery, not the cards. Bundled as `app-{troubleshooting,optimization,tooling,demo,training}.jpg` plus `app-hero.jpg`; the stored rows were rewritten by `scripts/localize-applications-gallery.php`.

**Why `app-training.jpg` is landscape when the other gallery assets are portrait:** the client supplied a portrait shot whose subjects (faces, pointing hand, control panel) sit in the **upper** third. These cards are landscape (~2.15), and `object-fit: cover` crops the middle 35% — which cut both faces off. The asset is therefore pre-cropped to 2.146, framed on the subjects, rather than left portrait. Check the crop on any portrait photo going into these cards: `sips -c <h> <w>` reproduces it.

**`machine-custom-solutions.html` has one left:** `photo-1666634157070` on the **Process Engineering** service card (also still the fallback in the `mcs_cards` default array in `page-modification-of-standard-machine-tools.php`). Every other MCS card is real client art. Worth asking the client for a Process Engineering photo.

**`index.html` has 2** — worth a look, since the homepage is the most visible page in the build.

Every remote `<img>` carries an HTML comment (`<!-- Stand-in: Unsplash — awaiting client photo -->`). Search for `images.unsplash.com` to find them all. Unsplash URLs can 404 over time — verify before migration.

## 4. Videos

| File | Used on | Status |
|---|---|---|
| `auto-door.mp4` (4.4MB) | **Client footage.** MCS Auto Doors card thumbnail (muted inline loop), MCS gallery "Auto Door Integration" collection, and `gallery-module-preview.html` | Shipping |
| `placeholder-*.mp4` | ffmpeg placeholders used only by the **gallery-module preview** (not a shipping page) | Do not migrate; await client footage |

`auto-door.mp4` was remuxed from the client's `.mov`. It is the first video used as an **inline card thumbnail** — see the `video` sub-field on the `mcs_cards` ACF repeater. At 4.4MB it is the heaviest asset on the MCS page and is worth compressing if the client supplies more card clips.

## 5. Fonts

- **Barlow Condensed** — Google Fonts (500/600/700), display.
- **Navigo** — Adobe Fonts kit `lqh7ybe`, 400 + 700 (body/UI). **Domain-locked:** add WP Engine production + staging domains in the Adobe Fonts kit.

## 6. Post-migration tasks

1. ~~Compress large PNGs (`es-hero.png` 2.8MB)~~ **Done 2026-09-21** — `es-hero.png` → `es-hero.jpg` (500KB), and the homepage hero went from a 7.2MB raw JPEG to `hero-slide-1.jpg` (619KB) + `hero-slide-1@2x.jpg` (1.0MB) behind `srcset`. The last large PNG (`machine-milling-centers.png`) was pruned with its preview page 2026-09-28.
2. Replace all Unsplash URLs with Media Library attachments.
3. Confirm FANUC ASI seal usage rights before go-live.
4. Keep brand assets in the theme (not the Media Library) so updates don't touch content.
