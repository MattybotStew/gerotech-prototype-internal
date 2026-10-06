# Content map — Gerotech Website Content Document → pages

> **STATUS 2026-10-06:** Client confirmed the doc as **final** (same content as the Google Sheet link; no cell changes). Applied to the **prototype** (ES/MCS/Applications/Automation only — Home is out of scope), the **WP child theme templates + ACF definitions** (`field_{app,mcs,ai}_cta_subhead` added), linted, and synced to Local.
> **Local DB:** applied via `scripts/update-es-content-2026-10.php` (idempotent; card rows updated in place by title so media attachments survive). Verified rendered on Local: hero/FAQ/CTA copy, card modals, gallery titles/collections, and the added Automation card all correct.
> **Dev:** deployed 2026-10-06 — theme rsync `-avz --delete` + remote `chmod` + `wp eval-file -` < the migration + page/CDN flush. All 4 pages 200 and render the final copy on Dev; Automation order = HMI Design → Electrical → Layered → Cell → EOAT → Pre-Engineered.

**Source:** `~/Downloads/Gerotech Website Content Document.xlsx` (final client content doc)
**Scope:** the **4 visible tabs only** (the client's active pages). Hidden tabs are retired working drafts and are out of scope.
**Companion to:** `theme-map.md`, `acf-spec.md`, `implementation-plan-wordpress-theme-acf.md`

---

## 1. Source tabs

The workbook has 12 sheets; only 4 are **visible** in the client's tab bar:

| Tab | State | Page |
|---|---|---|
| Engineered Soultions Page 9.1 *(sic)* | visible | Engineered Solutions |
| Machine Custom Solutions 9.1 | visible | Machine Custom Solutions |
| Applications | visible | Applications |
| Automation and Controls Solutio *(stored; renders "…Solutions")* | visible | Automation & Controls |

**Hidden (out of scope):** Home Page 9.1, Home Page Landing Page, Engineering Solutions Landing P, Applications Page, Sheet6, Planned Maintenence, Admin, Image Requests.

Row references below are the sheet row numbers in the doc. "As-is" rows are omitted unless the prototype currently disagrees.

---

## 2. Page map

| Doc tab | Prototype | WP template | ACF group | Repeaters |
|---|---|---|---|---|
| Engineered Soultions Page 9.1 | `engineered-solutions.html` | `page-engineered-solutions.php` | `group_es_content` | `es_why_features`, `es_faq_items`, `es_partners_logos` |
| Machine Custom Solutions 9.1 | `machine-custom-solutions.html` | `page-modification-of-standard-machine-tools.php` | `group_mcs_content` | `mcs_cards`, `mcs_collections` |
| Applications | `application.html` | `page-unique-applications-for-standard-machines.php` | `group_application_content` | `app_cards`, `app_collections` |
| Automation and Controls Solutio | `automation-integration.html` | `page-automated-system.php` | `group_automation_content` | `ai_cards`, `ai_collections` |

> Page slug note: MCS renders at `/modification-of-standard-machine-tools/`, Applications at `/unique-applications-for-standard-machines/`, Automation at `/automated-system/`. Display name and URL are allowed to differ (see `.clinerules`).

Shared components already match the doc — **no work**: testimonials (`partials/testimonials-block.html`, 3 quotes + attributions) and footer (address, phone, copyright, nav columns).

---

## 3. Engineered Solutions

| R | Type | Doc content | Prototype state | Target → field |
|---|---|---|---|---|
| 4 | Hero body | "…Gerotech engineers **are solution driven to provide creative, robust, most efficient process for its customers** - backed by decades of **engineering experience**." | old wording | Replace → `es_hero_body` |
| 6 | Hero CTA2 | **Being Removed** ("Seems senseless to barely move — remove this CTA") — "Explore Capabilities" | present (`#why-headline`) | Remove button; clear `es_hero_cta2_label`/`es_hero_cta2_url` |
| 8 | Why body | "…our engineers **start by understanding your process.**" (drops "don't start with a standard solution—") | old wording | Replace → `es_why_body` |
| 10–18 | Value-prop cards | MCS / Applications / Automation cards + paragraphs + links | ✅ already matches (`es_why_features`) | no change |
| 27 | Tech-partners eyebrow | "Our Technology **Partners**" | "Our Technology **Ecosystem**" | Replace → `es_partners_eyebrow` |
| 30 | Vendor CTA | **Being Removed** — "Explore Capabilities" | present | Remove link; clear `es_partners_cta_label`/`url` |
| 36 | Solutions-grid CTA2 | **Being Removed** — "Explore Capabilities" | present | Remove button; clear `es_cap_cta2_label`/`url` |
| 41 | FAQ 1 question | "Do you **automate machines other than Haas**?" | "Do you work on machines from brands other than Haas?" | Replace → `es_faq_items[0].question` |
| 45–46 | FAQ 3 | **Being Removed** ("How quickly can your service team respond to downtime?" + answer) | present | Delete row → `es_faq_items[2]` |
| 56 | Bottom-CTA header | "**Enigineering** Solutions Built Around Your Operation" (typo, approved "as is") | "Engineering …" | See §7 typos |

**Currently matching, no change:** hero eyebrow/headline, hero CTA1, why headline, FANUC block, tech-partner body, solutions grid, FAQ 2/4/5, testimonials, bottom CTA body/CTA/phone, newsletter.
**CTA band:** ES keeps the phone call card and "Talk to a person, not a form." (rows 59–60 approved as-is) — this is the **exception** to the interior CTA change in §6.

---

## 4. Machine Custom Solutions

| R | Type | Doc content | Prototype state | Target → field |
|---|---|---|---|---|
| 8 | Card 1 detail | Column Risers — updated ending "…only require **additional Z axis clearance**." | old wording | Replace → `mcs_cards[0].detail` |
| 20 | Card 4 detail | Auto Doors — updated ("self-monitoring **features**", "**Automatically** reacts…", "…two-hand buttons are not required **with this solution**", "**outputs** provided") | old wording | Replace → `mcs_cards[3].detail` |
| 24 | Card 5 detail | Hydraulic-Pneumatics — **full replace** ("Hydraulic solutions can be custom designed… PSI, flow rate… clamping force… Pneumatic circuit integration…") | old wording | Replace → `mcs_cards[4].detail` |
| 28 | Card 6 detail | Custom Workholding — **full replace** ("When you need more than off the shelf vises and chucks… hydraulic/trunnion/tombstone fixture…") | old wording | Replace → `mcs_cards[5].detail` |
| 30 | Card 7 | Process Engineering — title as-is, status "**Replace Image**" | current image | Swap image → `mcs_cards[6].image` |
| 36 | Card 8 detail | Specialty Machine — **full replace** ("**5 Axis Grinding**… **Spin Forming**…") | old wording | Replace → `mcs_cards[7].detail` |
| 38 | Gallery eyebrow | "On Our Floor" — **Being Removed** | present | Remove → `mcs_gallery_eyebrow` |
| 39 | Gallery header | "**Machine Custom Solutions Gallery**" | "Installed Gallery" | Replace → `mcs_gallery_title` |
| 40 | Gallery body | "Recent customization and retrofit work from Gerotech engineers." | present | no change (as-is) |
| 42–50 | Gallery set | Remove **Fire Suppression**; add **Safety & Environmental Modifications**, **Process Engineering**, **Specialty Machine**; rename titles | present incl. Fire Suppression | Edit rows → `mcs_collections` |
| 58 | CTA subhead | "**Let's Talk Through It. Prefer Email?**" | "Prefer to talk it through?" | Replace (see §6) |
| 59 | CTA contact | **(734) 379-7788 — Being Removed** | present | Remove call card |
| 60 | CTA body | "Tell us about your machine, part, process, and project goals, and include any drawings, photos, or specifications…" | "Talk to a person, not a form." | Replace → `mcs_cta_body` |

---

## 5. Applications

| R | Type | Doc content | Prototype state | Target → field |
|---|---|---|---|---|
| 8 | Card 1 detail | Part Programming — **full replace** ("We write programs that get the most out of your machine… G-code, conversational, CAM…") | old wording | Replace → `app_cards[0].detail` |
| 10, 12 | Card 2 | Process Troubleshooting — status "In Progress — **Need Image**"; detail **full replace** (simulators/showroom machines, applications@gerotech.com) | old wording + image | Replace detail + swap image → `app_cards[1].detail` / `.image` |
| 16 | Card 3 detail | Process Optimization — **full replace** ("Every program is optimized… custom probing and macro programming…") | old wording | Replace → `app_cards[2].detail` |
| 20 | Card 4 detail | Tooling Recommendation — **full replace** ("Our Application Engineers utilize Mfg Engineering backgrounds…") | old wording | Replace → `app_cards[3].detail` |
| 24 | Card 5 detail | Demo — **full replace** ("We can run machine demos for any machine in the showroom…") | old wording | Replace → `app_cards[4].detail` |
| 28 | Card 6 detail | Training — **full replace** ("…at no cost to our customers… Grand Rapids and Macomb Community College partner…") | old wording | Replace → `app_cards[5].detail` |
| 30 | Gallery eyebrow | "On Our Floor" — **Being Removed** | (may be present) | Remove |
| 31 | Gallery header | "**Applications Product Gallery**" | "Applications — Product Gallery" | Replace → `app_gallery_title` |
| 32 | Gallery paragraph | **Being Removed** | check | Remove |
| 33 | Gallery card 1 | Part Programming — "(remove the gray subtext)" | has meta | Remove `.gallery-collection__meta` → `app_collections` |
| 34 | Gallery card 2 | Process Troubleshooting — "**Replace Image**" | current image | Swap cover |
| 46 | CTA subhead | "Let's Talk Through It. Prefer Email?" | "Prefer to talk it through?" | Replace (see §6) |
| 47 | CTA contact | (734) 379-7788 — **Being Removed** | present | Remove call card |
| 48 | CTA body | "Tell us about your machine, part, process…" | "Talk to a person, not a form." | Replace → `app_cta_body` |

---

## 6. Automation & Controls

| R | Type | Doc content | Prototype state | Target → field |
|---|---|---|---|---|
| 4 | Offerings paragraph | **Being Removed** ("no paragraph on machine custom solutions and application page — remove this paragraph completely") | hero body present | Decide hero body (see §7) |
| 7–14 | Card 1 | **ADD "Electrical – Controls Solutions"** + 6 sub-blocks (Concept & System Design, Electrical Engineering, Panel Build, PLC & HMI, Commissioning, Control Platform Options); "WILL NEED THE + SIGN LIKE THE OTHER CARDS" | **absent** — prototype comment says it was **removed per content doc** | ❌ conflict — see §7 |
| 15–27 | Card 2 HMI Design | Large detail **ADD** set (Configurable Operator Screens, Ethernet Diagnostics, I/O Diagnostics, Device-Specific Diagnostics, Device-Centric Control, Cell Automation Overview/Station/Part Program/Part Data/Safety Devices) | one short merged paragraph | Replace/expand → `ai_cards[1].detail` |
| 34 | Card 3 Layered Controls | Add "**Standardized Software Design Methodology**" as a `+` **under Layer 3** | not present | Add to → `ai_cards[2].detail` |
| 39 | Card 4 detail | Automation Cell Design — **updated** ("…we design every automation cell in SolidWorks and validate it in RoboGuide…") | old wording | Replace → `ai_cards[3].detail` |
| 43 | Card 5 detail | Robot EOAT — **updated** ("…multiple jaws on a Schunk gripper, Servo Onrobot gripper, vacuum, magnetic…") | old wording | Replace → `ai_cards[4].detail` |
| 47–48 | Card 6 detail | Pre-Engineered Solutions + Standardized Software Methodology | present (partly) | Verify/replace → `ai_cards[5].detail` |
| 50 | Gallery eyebrow | "On Our Floor" — **Being Removed** | present | Remove → `ai_gallery_eyebrow` |
| 51 | Gallery header | "**Automation & Controls Gallery**" | "Installed Automation Gallery" | Replace → `ai_gallery_title` |
| 52 | Gallery paragraph | **Being Removed** | check | Remove |
| 53–57 | Gallery cards | Keep card names, "just remove gray subtext" | has meta | Remove `.gallery-collection__meta` → `ai_collections` |
| 64 | CTA paragraph | "Robot cells, workholding, and controls…" — **Being Removed** | present (`cta-band__body`) | Remove |
| 66 | CTA subhead | "Let's Talk Through It. Prefer Email?" | "Prefer to talk it through?" | Replace |
| 67 | CTA contact | (734) 379-7788 — **Being Removed** | present | Remove call card |
| 68 | CTA body | "Tell us about your machine, part, process…" | "Talk to a person, not a form." | Replace → `ai_cta_body` |

---

## 7. Cross-cutting patterns

1. **Interior CTA band change (MCS, Applications, Automation only).** Doc removes the phone call card and "Talk to a person, not a form.", and adds a subhead **"Let's Talk Through It. Prefer Email?"** + a long body ("Tell us about your machine, part, process, and project goals, and include any drawings, photos, or specifications…"). **ES keeps the phone card** (rows 59–60 approved as-is).
   - Gap: there is **no ACF field for the CTA subhead** today, and `*_cta_body` is deliberately empty. Needs a field decision + a `*_cta_call_*` blank convention to render the "Prefer Email?" state.
2. **Gallery "remove gray subtext"** = drop `.gallery-collection__meta` (the small label) for the named cards on Applications + Automation. MCS/others keep theirs.
3. **Card detail length.** The Automation/HMI + Electrical cards note "WILL NEED THE + SIGN LIKE THE OTHER CARDS" — the modal already renders WYSIWYG `detail`; may need collapsible sub-headings inside the modal.
4. **Doc typos approved "as is":** "Enigineering Solutions" (ES R56), "Need applcation support" (Applications R3/R44), "Process Troublshooting" (Applications R10). Decision required — see §8.

---

## 8. Conflicts & open decisions (need Matt/client)

1. **Automation Card 1 — Electrical/Controls.** Tab says **ADD** (row 7, status `ADD`); the prototype carries a comment that it was **removed per the content doc**. Which is current? This changes the card count and every downstream `ai_cards` index.
2. **Automation Offerings paragraph (row 4).** Marked "Being Removed" with the note "no paragraph on machine custom solutions and application page". The AI page hero currently has a paragraph — confirm whether the *hero* body goes or just a separate offerings paragraph.
3. **Typos.** Fix ("Engineering", "application", "Troubleshooting") or preserve verbatim as approved? Recommend fix.
4. **CTA subhead field.** Introduce `*_cta_subhead` + `*_cta_prefer_email` fields, or fold "Let's Talk Through It. Prefer Email?" into the call-card label? Needs an ACF shape decision before seeding.

---

## 9. Implementation notes (per standing rules)

- **Prototype is source of truth for shared assets then the theme is the copy.** Edit prototype HTML/CSS first → hand-port markup into the matching PHP template (map above) → ACF field changes get **definition + template default** → `scripts/seed-acf-content.php` (by **field key**) → `sync-theme-to-local.sh` → seed → `audit-acf-applied.php`.
- **Stored rows win.** MCS/Applications/Automation cards and galleries are stored ACF repeaters, so a template-default edit changes nothing on Local/Dev until the row is reseeded. Card images are media-library attachments → use `scripts/set-card-image.php` (positional args, idempotent).
- **Never compare bare slugs** — use `get_page_uri()`. Never pass a field **name** to `update_field()` where a **key** is expected.
- Deploy to Dev via the `rsync -avz --delete` + remote `chmod` + `wp page-cache flush` / `wp cdn-cache flush` recipe, then run the seeder/audit on Dev too.

---

*Created 2026-10-06. Update this file (and `.clinerules`) as decisions in §8 are made and rows land.*
