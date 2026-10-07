# Gerotech Website Redesign — Project Handoff for Cline

**Purpose of this doc:** Full context so you can pick up this project cold inside VS Code. Read this entirely before touching code.

---

## ⚡ Prototype status — 2026-08-17 (supersedes wireframe stage below)

The HTML prototype on **`master`** is **presentation-ready**, not a wireframe.

- **Typography:** Barlow Condensed (Google Fonts) for display headlines + **Navigo** for body/UI (Adobe kit `lqh7ybe`); add **Medium (500)** when ready
- **CSS:** `tokens.css` → `components.css` → `layout.css` → **`elevated.css`**
- **All heroes + CTA photo bands:** left-aligned full-bleed photo (matches homepage hero)
- **Interior hero classes:** all sub-pages use **`.page-hero`** — see `design-spec.md`
- **Homepage (2026-09-18 Figma `7306:1063`):** peek hero **min-height 500px, grows with content**; peeks 02 In-Stock & Ready / 03 Automation for Michigan; Haas intro + watermark + F1 lockup (**no** 4-col features band); cinema-lockup CTA with `assets/images/cta-home-building.jpg` (Flat Rock building); no phone lockup. Slide 2 headline remains Ready To Ship. **Read `.clinerules` first** — this handoff’s August homepage note is historical.
- **Tristien Bridges round (2026-10):** the current revision record is `.clinerules` → *Current Session State* + the `JOURNAL.md` 2026-10-07 entries (and `design-spec.md` → *Tristien round (2026-10)*). In brief: Floor Inventory, 14,000+, Kingsbury, Flat Rock homepage CTA, engineering mailto `Engineeringproposals@gerotech.com`, Bar Feeders re-link, ES/MCS/Applications/Automation re-orders (Layered Controls Solutions removed), and new card/gallery images. **Seeded into stored ACF on Local + Dev and deployed (theme + DB) on 2026-10-07; not committed.** Preview the prototype on port **8081** (8080 is CFHF).
- **ES hub:** Full section stack including tech partners + capability cards; shared testimonial partial
- **Design directions:** see **`AGENTS.md` → Design directions (2026-08-17)** for Haas/news/testimonial/accent patterns — do not drift
- **Images:** Unsplash stand-ins (not dashed-border divs); verify URLs — some 404 over time (swap table in `design-spec.md`)
- **UX:** Search modal, email signup thanks, sticky header, wired mailto/tel CTAs
- **Read first:** `.clinerules` (section **Next session — pickup here**) + `JOURNAL.md` + `docs/PROJECT_BRIEF.md`

**WP Engine Dev (2026-09-18):** child theme is live on https://gerotechdev.wpenginepowered.com/ . Theme-only pushes. Stage = Dev rollback. F1 JPEG `haas-f1-team.jpg`. reCAPTCHA Dev domain still invalid.

Historical wireframe/timeline context below is still useful for client decisions and live-site audit.

---

## 1. The Project, in One Paragraph

CloudMellow (the design/dev agency) is rebuilding the website for Gerotech, a Michigan CNC machinery distributor and engineering solutions company (Haas Factory Outlet, est. 1987). It's a 16-week engagement: discovery → wireframes → SEO audit → design → development. We are currently in the **wireframe approval / pre-design phase**. I (the user) am building an HTML/CSS/JS prototype to get ahead of the design phase and have something concrete to work from before the July 7 design presentation to the client (including their CEO, Rochelle).

**This is a prototype, not the production build.** The actual site will be WordPress. We're using HTML/CSS/JS because it matches how the wireframes were already built and it's fast to iterate visually.

---

## 2. Where We Are Right Now (Timeline)

| Date | Milestone | Status |
|---|---|---|
| June 26 | Site fix priority list due | Should be done |
| **June 29** | Wireframe written approval due (Rochelle/CEO sign-off) | **Pending — blocks design** |
| June 30 | Hosting migration (was moved from June 30 to July 2 in a later meeting — confirm which date is final with client) | TBD |
| July 2 | Content collection begins | Upcoming |
| **July 7** | CloudMellow presents initial page designs to Gerotech (Rochelle attends) | **Target for this prototype** |
| July 10 | Gerotech feedback on designs due | Upcoming |
| July 21 | Design revision presented | Upcoming |
| Aug 4 | Designs approved | Upcoming |
| Aug 5 | Development begins | Upcoming |

**Bottom line: this prototype needs to be presentable by July 7.**

---

## 3. CRITICAL — Live Site Reality Check

I fetched the actual live site at **https://gerotech.com** and it differs from the wireframes in important ways. The wireframes represent the *redesign target* — they are NOT what's live today. Don't confuse the two.

### What's actually live right now (as of June 30, 2026):

**Current nav structure (different from wireframe plan):**
```
Company ▼ (About, Careers, News and Events)
Machines ▼ (huge Haas mega-menu: VMC, HMC, CNC Turning, Rotaries, Tooling, Financing, HFO)
Engineered Solutions ▼ (7 sub-items — see below)
Support ▼ (Service Request Forms, Training, Rotary Repair, Planned Maintenance)
```

**Current Engineered Solutions sub-pages (live, 7 items — totally different naming from our wireframe's 8 services):**
- Automated System
- Engineering Process Optimization
- Custom Workholding and Tooling
- Custom Training Program
- Modification of Standard Machine Tools
- Integration Partners
- Unique Applications for Standard Machines

⚠️ **This is a big naming/IA mismatch.** Our wireframe's planned categories (Machine Modification, Machine Customization/Custom Solutions, Automation Integration with 8 specific services like Hydraulic Additions, Workholding, Robot Selection, etc.) are a completely different taxonomy than what's live now. This is expected — it's literally what we're redesigning — but it means **don't reference the live IA as ground truth.** Use the wireframe spec doc and Content Drafting Worksheet as ground truth for the new structure.

**Current live Engineered Solutions landing page is extremely sparse** — just a headline, two-line subhead ("Collaborative, cutting-edge solutions"), one paragraph of approach copy, and a CTA. No grid, no cards, no FANUC band, no partner logos. This confirms why the redesign is needed — there's a lot of room for the new wireframe's richer structure to be an improvement.

**Current homepage has content NOT in our wireframes that may need a decision:**
- A "Showroom Machines Are For Sale" section with specific machine listings (ST-30, UMC-750, ST-25Y, VF-2SS + COBOT) with mailto-based "Request Info" CTAs
- A "Rotary Products Available Locally" section (HAAS TRT100, TRT210)
- A "Winner's Circle" membership promo ($10/year Haas program)
- These look like **time-sensitive sales/inventory content**, not core IA. They're probably out of scope for the redesign (likely handled as a dynamic/CMS block later) but flag this for the client — don't silently drop functionality they're actively using to sell inventory.

**Confirmed details that match our spec:**
- Phone numbers: 734-379-7788 (HQ/Sales), 248-476-8787 (Service), 616-735-1100 (Grand Rapids) — ✅ matches
- Address: 29220 Commerce Drive, Flat Rock, MI 48134 — ✅ matches
- Michael Rudisill testimonial quote — ✅ exact match, confirmed real
- "Since 1987" / "Michigan's Premier Distributor..." headline — ✅ matches, this is real existing copy
- 3-card intro (Machines / Solutions / Support) — ✅ matches, exact copy confirmed
- Partner logos — ⚠️ **Takamaz is currently live** on the partner row (confirms Figma comment #26 is correct that it needs removing — it's outdated)
- Live partner list: HAAS, TAKAMAZ, MIDACO, FANUC, ONROBOT, DYNATECT, ROYAL PRODUCTS, MARPOSS, TSUDAKOMA, ALBERTI, RENISHAW, KEYENCE, 5TH AXIS (13 total, will become 12 once Takamaz is removed — matches our spec)
- Hero slider currently shows: Scheduled Training (Macomb Community College) / Showroom Machines / Rotary Products — confirms the oversized-slider bug mentioned in Figma comments is real and currently live
- "Put our engineers to work on your project" CTA copy — ✅ exact match
- Email signup "JOIN OUR MAILING LIST TO STAY UP-TO-DATE" — exists on live site already, confirms Figma comment #41 about manual Constant Contact process

**⚠️ Important nav correction:** Live site phone "Headquarters & Sales" link currently goes to `#` (broken/non-functional), and the tel: links are malformed — missing country code and dashes (`tel:734379788` instead of `tel:+17343797788`). This is a known bug to fix, consistent with the click-to-call requirement already flagged.

**Current footer is minimal** — just logo, address/phone, copyright, Privacy link. No social icons currently live (our wireframe spec adds LinkedIn/YouTube/Instagram — confirm these accounts exist before designing icon links to dead profiles).

---

## 4. Current Tech Stack (live site — confirms several things and adds new constraints)

| Layer | What's running |
|---|---|
| CMS | WordPress |
| Theme | Custom theme called "Gerotech" (bespoke, not a marketplace theme) |
| Plugins | **Megamenu** (explains the deep multi-level Haas dropdown nav), **Contact Form 7** + **Contact Form 7 Honeypot** (forms/spam protection), **Smart Slider 3** (the hero slider — confirms the Figma comment about the slider sizing bug, it's a Smart Slider 3 config issue, not custom code) |
| JS libraries | jQuery, jQuery Migrate 3.4.1, HoverIntent JS (used for the megamenu hover-delay behavior) |
| Fonts | Google Font API |
| Tag management | Google Tag Manager |
| Hosting | **Google Cloud** (not GoDaddy as the kickoff meeting's action items suggested — worth clarifying with Tristien whether GoDaddy is DNS/domain only, or whether the new host migration target has changed) |
| CAPTCHA | reCAPTCHA |
| Security | Kount (typically used for fraud/payment risk — unusual for a B2B distributor site, may be legacy or tied to e-commerce features like the Showroom Machines "Request Info" flow) |
| Markup | Open Graph tags present (confirmed in the meta data I pulled) |

**Why this matters for the prototype:**

- **Megamenu plugin explains the nav complexity.** The live Machines dropdown is a multi-column mega-menu (VMC/HMC/Turning/Rotaries/Tooling sub-categories). If the new design keeps any of that depth under "Machines ↗", the prototype's nav.js needs to support nested multi-column dropdowns, not just simple single-level ones. Right now our wireframe nav only shows single-level dropdowns (Support → Training/Parts). Flag for the client: is the Haas mega-menu depth being kept, simplified, or just linking out to haascnc.com entirely (the ↗ icon suggests external link, which simplifies this a lot)?
- **Smart Slider 3 is the hero slider plugin** — confirms this isn't a custom-built carousel, it's a config issue (oversized) within a known plugin. Dev fix on the current site is likely just a settings adjustment, not a rebuild. Don't over-engineer slider.js for the prototype — match Smart Slider 3's typical behavior (dots, arrows, autoplay) but it doesn't need to be that plugin's exact feature set.
- **Contact Form 7 is the form backbone.** Any "Get a Quote" or "Request Info" forms in the new design should be planned with CF7 field-naming/structure in mind if the dev team wants a clean WordPress migration later, even though the prototype itself won't be live WordPress.
- **Google Cloud hosting contradicts the GoDaddy references from the kickoff meeting.** The kickoff action items mentioned granting GoDaddy delegate access and negotiating a GoDaddy refund — but the live site's hosting fingerprint shows Google Cloud. GoDaddy may just be the domain registrar, with hosting separately on GCP, or there's a mismatch worth clarifying with Tristien/Michael before the hosting migration date is finalized.
- **Kount is unusual** for this type of B2B site — likely a holdover from an older payment/checkout integration, possibly tied to the Showroom Machines or Winner's Circle ($10 membership) flows which do involve a transaction. If those sections carry into the redesign, this is a clue that some kind of payment processing exists today and needs a decision on whether it's in scope.

---

## 5. Source of Truth Documents (all in the project)

These are the documents that matter, in priority order:

1. **`Gerotech-Wireframe-Strategy-Round2.md`** — the strategic rationale doc explaining IA decisions, why Haas was de-emphasized on homepage, category structure reasoning
2. **`design-spec.md`** (the file already in this VS Code project) — the full component library, tokens, page structures, and content I compiled — **this is your primary working reference**
3. **Figma file** `YgHwqyyFj57c1ZSbmfkL0c` — nodes 6217:425 (ES landing) and 6218:10 (Homepage) are the actual wireframes
4. **`Content_Drafting_WorksheetEngineering.xlsx`** — service-by-service content status (ready/in progress/not started) and old vs. new copy comparison
5. **`Logo_Page_of_Brand_Standards_Document.pdf`** — official logo color variants (Pantone 446 + 158, black, white)
6. **Live site** `https://gerotech.com` — reference for existing real copy, but NOT for IA/nav structure (that's being redesigned)

---

## 6. Brand Tokens (confirmed, ready to use)

```css
:root {
  /* Primary */
  --orange:        #F38A2C;   /* Pantone 158 */
  --orange-light:  #F9A94A;

  /* Neutrals */
  --ink:           #0D0D0D;
  --dark-bg:       #1C1C21;
  --dark-nav:      #3A3A40;
  --gray-500:      #808080;
  --gray-muted:    #9A9AA8;
  --gray-200:      #D6D6D6;
  --gray-100:      #E0E0E0;
  --gray-bg:       #F9F9FB;
  --gray-band:     #F2F2F2;

  --text-on-dark:  #E7E7EE;
  --text-on-dark2: #DCDCE0;
  --white:         #FCFCFC;
}
```

**Font:** Plus Jakarta Sans (400/500/600/700/800), Google Fonts.

**Type scale:** xs 11px, sm 13px, base 16px, md 18px, lg 21px, xl 24px, 2xl 30px, 3xl 34px (homepage H2), 4xl 48px (ES H2), hero 72px (ES H1), home-hero 46px.

**Layout:** max-width homepage 1200px, ES page 1120px. Button radius 8px, card radius 18–20px, pill radius 99px.

Full component CSS (header, buttons, cards, badges, testimonial, footer, etc.) is already written out in `design-spec.md` Section 3 — copy directly from there, don't rewrite from scratch.

---

## 7. Page Structures

### Homepage (`index.html`) — section order:
1. Alert banner (dark, 3 click-to-call numbers — **fix the tel: format bug, see Section 3 above**)
2. Sticky header — logo | Machines ↗ | Engineered Solutions | Support ▼ | About ▼ | Get a Quote | 🔍
3. Hero slider (dark, full-bleed, 520px — **fix oversized slider bug from live site**)
4. Intro + 3 category cards (real copy confirmed, see Section 7)
5. Partner logo row (12 logos after removing Takamaz)
6. Testimonial split (Michael Rudisill — real quote, confirmed)
7. News feed (3 items)
8. CTA band ("Put our engineers to work on your project")
9. Email signup
10. Footer

### Engineered Solutions (`engineered-solutions.html`) — section order:
1. Alert banner
2. Sticky header
3. Hero ("Your Manufacturing Solutions Partner")
4. Why Gerotech (2-col)
5. Stat strip (placeholder number — needs client input)
6. ES grid — 3×3, 8 service cards + 1 slot (**naming conflict unresolved, see Section 8**)
7. FANUC ASI credential band (**usage rights unconfirmed**)
8. Technology Partners (3×3 logo grid)
9. Capability band ("Your Machine. Our Solution.")
10. Testimonials (2 cards)
11. News feed
12. CTA band ("Ready to Engineer a Better Way to Manufacture?")
13. Footer

Full section-by-section copy and exact component markup guidance is in `design-spec.md` Sections 4 and 5.

---

## 8. Confirmed Real Copy (verified against live site — use these verbatim)

**Homepage intro headline:**
> "Michigan's Premier Distributor for CNC Machinery, Robotics, and Engineered Turnkey Solutions"

**Homepage intro body:**
> "Since 1987, Gerotech has sold CNC Machinery and Engineered Turnkey Solutions to manufacturers worldwide. We have sold Mills, Lathes, 5-Axis Machines along with Industrial and Collaborative Robotic Solutions. We do it through people who listen, respond, build, deliver, and support as committed partners in order to transform your manufacturing challenges into competitive advantages."

**3 cards (exact live copy):**
- **Machines:** "Our knowledgeable sales staff are able to help you choose the perfect machines for your applications. From Mills to Lathes to robots, our experts have something for any application."
- **Solutions:** "Gerotech offers fully engineered turnkey services for any of your custom manufacturing needs. Our experts have years of experience solving unique problems. See what Gerotech can do for you!"
- **Support:** "Our service department is staffed with factory-trained and certified personnel. Every one of our technicians receives comprehensive training on a variety of topics, including new machine installation, electrical and mechanical repair, ball bar testing, and software upgrades."

**Testimonial (confirmed real, live on site):**
> "I have never had such consistent, quality customer support from a company and an overall great experience. Every contact has been timely, there has been good communication, friendly service, and each time they are happy to educate me along the way and I walk away with more information and peace of mind than I had hoped for."
> — Michael Rudisill

**CTA band:**
> "Put our engineers to work on your project" / "Engage with us today"

**Address/Footer:**
> 29220 Commerce Drive, Flat Rock, MI 48134 · 734-379-7788

---

## 9. Open Decisions — Do Not Guess, Flag for Client Instead

| # | Decision | Why it matters for code |
|---|---|---|
| 1 | Wireframe sign-off (Rochelle/CEO) | Structural changes could still come in |
| 2 | Hybrid vs. pure-tab URL structure for ES pages | Determines if you build 9 HTML files or 1 |
| 3 | ~~"Machine Customization" vs. "Machine Custom Solutions" naming~~ | **RESOLVED 2026-09-22 (CloudMellow): "Machine Custom Solutions".** Already applied consistently across nav, mega-menu, mobile nav, search, headings, breadcrumbs and ACF labels. The legacy live slug `/modification-of-standard-machine-tools/` is deliberately retained and mapped in `inc/helpers.php`. Note the client's own message said "Machine Customizations" — worth a final nod, but no code change is pending. |
| 4 | Training: own nav item vs. under Support dropdown | Current wireframe has it under Support; client said (Figma comment) they want it standalone |
| 5 | FANUC badge usage rights | Build the section with a placeholder badge, don't use a real FANUC logo file until confirmed |
| 6 | Final service count for Machine Customization category | Only Remote Tool Offset confirmed; rest are TBD |
| 7 | Capability band copy ("Your Machine. Our Solution.") | CloudMellow drafted it, client hasn't confirmed — use as placeholder, mark clearly |
| 8 | Haas prominence — homepage vs. Machines section only | Don't add Haas branding back to homepage hero without explicit confirmation |
| 9 | Showroom/Rotary Products sections from live site | Not in our wireframe — confirm with client whether this is in scope before building |
| 10 | Stat strip number ("XX machines modified...") | Literal placeholder, needs real client number |

**When in doubt, build with an obvious placeholder (dashed border box, `[PLACEHOLDER: confirm with client]` HTML comment) rather than guessing.**

---

## 10. Build Requirements / Dev Notes

- **All phone numbers** must be `<a href="tel:+1XXXXXXXXXX">` with full E.164 format — the live site has this broken (`tel:734379788`, no `+1`, no dashes preserved). Fix this in the prototype.
- **External links** (Machines → Haas, partner sites): `target="_blank" rel="noopener noreferrer"`
- **Internal links:** same tab, no target attribute
- Use **CSS custom properties** for all colors/spacing — client will request tweaks
- All images: dashed-border placeholder divs until client provides real assets (see asset list in `design-spec.md` Section 8)
- Comment each HTML section clearly (`<!-- HERO SLIDER -->` etc.) for client walkthroughs
- Mobile: nav collapses, cards stack, buttons go full-width
- No inline `<script>` — keep JS in separate files (`nav.js`, `slider.js`, `filter.js`)

---

## 11. Recommended Folder Structure

```
gerotech-prototype/
├── index.html
├── engineered-solutions.html
├── assets/
│   ├── css/
│   │   ├── tokens.css
│   │   ├── components.css
│   │   └── layout.css
│   ├── js/
│   │   ├── nav.js
│   │   ├── slider.js
│   │   └── filter.js
│   └── images/placeholders/
└── design-spec.md   ← already in project, full component + copy reference
```

---

## 12. What NOT to Do

- Don't pull nav structure, IA, or category names from the live site — that's the old structure being replaced
- Don't add real partner/vendor logo files without confirming licensing/approval status per vendor
- Don't use a real FANUC badge graphic until usage rights are confirmed
- Don't silently drop the Showroom Machines / Rotary Products / Winner's Circle content — flag it, don't just omit it
- ~~Don't finalize "Machine Customization" vs "Machine Custom Solutions"~~ — **settled 2026-09-22: "Machine Custom Solutions".** Write it exactly that way (two words, plural "Solutions") in every label, heading and nav item. Don't reintroduce "Machine Customization" or "Machine Customizations".

---

*Compiled June 30, 2026, cross-referencing: 4 project meetings, Figma wireframe nodes 6217:425 and 6218:10, Figma comments #23–42, Round 2 wireframe strategy doc, Content Drafting Worksheet, brand standards PDF, and the live production site at gerotech.com.*
