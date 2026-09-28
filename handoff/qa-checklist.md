# QA checklist

Pre-launch verification for the WordPress build. Verify every page against the approved **prototype** and the **Figma** design — see **§1, Sources of truth** below.

---

## 1. Sources of truth — prototype **and** Figma

The final pages, images and content exist in **two places**, and QA checks against **both**.

| | Where | What it holds |
|---|---|---|
| **Prototype (code)** | Repo root `*.html` + `assets/images/` | The build source of truth. Real breakpoints, real responsive behaviour, real motion. Run it: `python3 -m http.server 8080` → http://localhost:8080/ |
| **Figma** | File `YgHwqyyFj57c1ZSbmfkL0c` (Gerotech-Design) — homepage handoff frame **`7306:1063`** | The presentation and sign-off record. The same pages, images and copy, **plus the client's comments** and the approved state. |

Neither is a rough draft of the other. The prototype is what the WordPress build is measured against; Figma is what the client reviewed and approved.

### Why both are needed

- **Figma** carries the client's comments and the record of what was signed off. If a question comes up about *intent*, the answer is in the comment thread pinned to that frame.
- **The prototype** carries what a static artboard cannot show: breakpoints, stacking, hover and focus states, animation, carousel and modal behaviour. These are exactly the things that break in a build and are invisible in Figma.

### How to check against them

1. **Figma ↔ prototype** — confirm the approved design and every client comment are reflected in the code. An open Figma comment is either applied or consciously deferred — note which, and why.
2. **Prototype ↔ WordPress** — page by page, section by section. This is the **primary** QA comparison: same section order, same spacing, same imagery, same copy.
3. **Record divergences.** A deliberate difference (a CMS-driven element, a field left empty because a decision is still owed) should be written down, never silently passed.

### Known differences — these are not bugs

- **Figma artboards are static.** Motion, hover, focus and scroll behaviour have no Figma equivalent — judge those in a browser, on the prototype and the build.
- **Imagery can drift after import.** If an image was changed after the last import into Figma, **the prototype is authoritative** and Figma needs re-importing.
- **WordPress can hold newer copy than both.** Content edited in the admin after handoff wins inside WordPress — check the editor before filing a copy bug.
- **Accent words render as `<em>` in code** and as styled text in Figma. Same result, different mechanism — compare the rendered colour, not the markup.

---

## 2. Breakpoints

Test every page at: **360, 390, 768, 1024, 1440, 1920**.

- [ ] No horizontal overflow at any width (prototype audit found none)
- [ ] Header collapses to hamburger ≥1120px desktop-nav threshold
- [ ] `.grid-4` 4→2→1; `.grid-6` / `.partners-grid` 6→3→1 (tablet step 1024, single col 640)
- [ ] `.haas-relationship__grid` 4-col → 2×2 (≤1024) → 1-col (≤640)
- [ ] Hero copy left-aligned on desktop; mobile fallback centered ≤768 (homepage peek stays left)
- [ ] Machine lineup tabs scroll horizontally on mobile
- [ ] Gallery grids 4→3→2→1
- [ ] Long button labels wrap ≤480

## 3. Browsers

- [ ] Chrome (latest), Safari (latest + iOS), Firefox, Edge
- [ ] Safari: `backdrop-filter` header blur, `aspect-ratio`, `gap` in flex
- [ ] `position: sticky` header + alert banner on iOS Safari
- [ ] `IntersectionObserver` reveals fire correctly (not stuck at opacity 0)

## 4. Accessibility

- [ ] Skip-to-content link works
- [ ] Visible `:focus-visible` (orange 2px) on all interactive elements
- [ ] Search modal: focus trap, Esc closes, background `inert`, focus restored
- [ ] Hero slider: off-screen slides `aria-hidden` + links `tabindex="-1"`; dots `aria-selected`
- [ ] Machine tabs: `role="tablist"` / `role="tab"` / `aria-selected` / `aria-controls`
- [ ] Modals/lightbox: `role="dialog"`, `aria-modal`, focus trap, Esc, focus restore
- [ ] All images have meaningful `alt`; decorative images `aria-hidden`
- [ ] `prefers-reduced-motion`: animations, card zoom, testimonial lift, counter, lightbox fade all disabled
- [ ] Color contrast: body gray on white, white on orange, accent text on light (deep orange) all ≥ 4.5:1
- [ ] Forms: labels (`sr-only` ok), `required`, `autocomplete`

## 5. Content / editor walkthrough

- [ ] Every ACF field edits the correct spot with no layout break
- [ ] Repeaters: add / remove / reorder items (hero slides, stats, cards, gallery, news, testimonials, footer links)
- [ ] News lead Group renders; news items 02–04 index correctly
- [ ] Machine lineup: switching `panel_type` shows the right conditional fields
- [ ] Accent words: italicising a word in a WYSIWYG maps to orange
- [ ] Images: upload, crop, alt text; no missing-image boxes
- [ ] Long copy doesn't overflow (test a 2× paragraph in each field)
- [ ] Empty-field behaviour: section hides or shows a sensible fallback (decide per section)

## 6. Functional

- [ ] Sticky header + alert banner collapse on scroll
- [ ] Mobile nav opens/closes; all links resolve
- [ ] Mega-nav (Machines + ES) opens, keyboard-navigable
- [ ] Search modal quick links work
- [ ] Email signup shows thanks state (Constant Contact / CF7 interim)
- [ ] `tel:` links dial correct numbers (E.164)
- [ ] `mailto:` Get a Quote opens with subject
- [ ] External machine links open new tab with `rel="noopener noreferrer"`
- [ ] MCS/Automation/Applications card modals + gallery lightbox (arrows, keys, swipe)
- [ ] `machine-modification` URL 301s to MCS

## 7. WordPress-specific

- [ ] Parent theme CSS dequeued; load order `tokens → components → layout → elevated`
- [ ] No console errors on any template
- [ ] ACF Pro active; field groups registered in PHP (no admin-UI drift)
- [ ] Options page saves; values render site-wide
- [ ] Adobe Fonts kit `lqh7ybe` domains include production + staging (Navigo loads)
- [ ] WP Engine cache purges on content update; check logged-in vs logged-out
- [ ] Slug collisions resolved (`/training/`, `/support/`, `/about/`, `/careers/`)
- [ ] `wp_get_attachment_image()` outputs `srcset`/`sizes`; images optimized (WebP)

## 8. Launch blockers (must be resolved before go-live)

- [ ] All Unsplash stand-ins replaced with client photography
- [ ] FANUC ASI seal usage rights confirmed
- [ ] Social + legal footer URLs wired (currently `#` / placeholders)
- [x] Large PNGs compressed — `es-hero.png` → `es-hero.jpg` (2.8MB → 500KB) and homepage hero → `hero-slide-1.jpg` + `@2x` with `srcset` (7.2MB → 619KB/1.0MB), 2026-09-21. The last large PNG (`machine-milling-centers.png`, preview-only) was pruned with its preview page 2026-09-28.
- [ ] News "Show More" destination confirmed (or link removed)
