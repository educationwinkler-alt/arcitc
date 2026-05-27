# Phase 5 Figma Frame-by-Frame Tracker

Date: 2026-05-26
Last updated: 2026-05-27

## Status

Phase 5 is reopened. The previous responsive/zoom pass was useful, but it was not a complete Figma frame-by-frame implementation.

Current rule:
- Do not mark Phase 5 complete until every required Figma frame/state below is either implemented and verified, or explicitly marked not applicable with a reason.
- Automated geometry gates are evidence, not a replacement for manual visual review.
- Figma covers the visual/UX layer, Baspa.cz covers admin/function patterns, and the old Arctic PHP site covers content.
- Figma does not define every in-between responsive state. Laptop widths and 120% browser zoom must be validated as real release conditions, not as optional polish.

## Implemented in This Corrective Pass

| Figma item | Node / source | Current implementation | Verification |
| --- | --- | --- | --- |
| Header default | `Arctic Spas grafika`, component set `1:1831`, variant `1:1832` | Existing desktop header retained and kept above menu overlay | `npm run figma:audit` |
| Header search state | `1:1855` `hledani` | Search opens as a header-shaped Figma panel with logo, 409x44 search field, CTA button, and correct placeholder | `desktopHeaderSearch` audit |
| Header desktop menu state | `1:1868` `menu` | Product mega menu for `Virivky` and `Celorocni bazeny`, with product columns, round thumbnails, separators, and promo card | `desktopHeaderMega` audit |
| Mobile menu search placement | `GM - HP menu` `1:2208` | Compact menu places search at 323x44 around y=527 on 375px viewport | Manual/spot check, needs fuller screenshot sign-off |
| Swimspa category desktop | `KATEGORIE` `1:262` applied to `/swimspa/` | Swimspa category now renders the same Figma intro, series nav, showroom anchor, references, CTA, and footer pattern as hot tubs | `swimspaCatalog` audit |
| Showroom page desktop | `SHOWROOM` `1:442` | Dedicated showroom route and Figma section geometry are audited | `showroom` audit |
| Product benefit popup | `popup` `1:1959` | First product benefit opens a Figma-style shell detail popup using downloaded Figma media | `benefitPopup` audit |
| Info pages desktop | `VLASTNOSTI`, `SLUZBY`, `CERTIFIKATY`, `ZARUKA`, `KOLIK STOJI UDRZBA`, `VLASTNOSTI DETAIL` | Page-specific desktop geometry and CTA/footer positions are audited | `features`, `services`, `certificates`, `warranty`, `maintenance`, `featureDetail` audits |
| Support page desktop | `PODPORA` `1:752` | Tabs, FAQ, help card, downloads, support form, contact CTA, and footer geometry are audited | `support` audit |
| Reference page desktop | `REFERENCE` `1:1127` | Reference heading, 3x3 card grid, CTA, and footer geometry are audited | `reference` audit |
| About page desktop | `O NAS` `1:945` | About heading, stats, team, jobs, CTA, and footer geometry are audited | `about` audit |
| Service page desktop | `SERVIS` `1:1426` | Service heading copy, form card, consent/button row, pricing columns, CTA, and footer geometry are audited | `serviceRequest` audit |

## Frame Inventory

| Frame | Node | Required status | Current status |
| --- | --- | --- | --- |
| HP | `1:14` | Full desktop and mobile visual parity | Partially automated: hero, categories, promo, benefits, showroom, progress, references, CTA, footer. Needs manual final screenshot sign-off and full-page mobile scroll review. |
| KATEGORIE | `1:262` | Product category layout for hot tubs and swimspa | `Virivky` and `Swimspa` desktop geometry are automated. Product-count/content-length variants still need manual spot-checks. |
| DETAIL KONKRETNIHO PRODUKTU | `1:1461` | Product detail layout and sections | Timberwolf desktop geometry is automated. Other product variants need spot-checks for content-length and gallery differences. |
| SHOWROOM | `1:442` | Dedicated showroom page | Desktop frame geometry is automated. Manual screenshot sign-off remains. |
| VLASTNOSTI | `1:585` | Feature listing page | Desktop frame geometry is automated. Manual screenshot sign-off remains. |
| VLASTNOSTI DETAIL | `1:1302` | Feature detail page | Desktop frame geometry is automated for `/vlastnosti/izolace-virivky/`. Other feature detail variants need content-length spot-checks. |
| SLUZBY | `1:658` | Services page | Desktop frame geometry is automated. Manual screenshot sign-off remains. |
| CERTIFIKATY | `1:694` | Certificates page | Desktop frame geometry is automated. Manual screenshot sign-off remains. |
| ZARUKA | `1:719` | Warranty page | Desktop frame geometry is automated. Manual screenshot sign-off remains. |
| PODPORA | `1:752` | Support/FAQ/download page | Desktop frame geometry is automated. Manual screenshot sign-off remains. |
| O NAS | `1:945` | About page | Desktop frame geometry is automated. Manual screenshot sign-off remains. |
| KONTAKT | `1:1037` | Contact page | Desktop geometry and map source are automated. Needs manual final sign-off. |
| REFERENCE | `1:1127` | Reference page | Desktop frame geometry is automated. Manual screenshot sign-off remains. |
| DALSI INFORMACE | `1:1216` | Additional information hub | Route is treated as navigation/dropdown hub, not a standalone page in the current build. Must be confirmed as not applicable or implemented before Phase 5 closes. |
| KOLIK STOJI UDRZBA | `1:1395` | Maintenance cost page | Desktop frame geometry is automated. Manual screenshot sign-off remains. |
| SERVIS | `1:1426` | Service page | Desktop frame geometry is automated. Manual screenshot sign-off remains. |
| popup | `1:1959` | Popup/contact modal state | Product benefit popup state is automated. Contact modal state still needs explicit confirmation if it is a separate required interaction. |
| GM - HP | `1:1973` | Mobile homepage | Partially automated: mobile hero, logo, menu button, promo, categories. Needs full mobile page scroll sign-off. |
| GM - HP menu | `1:2208` | Mobile navigation state | Partially present: search placement is checked. Needs complete visual comparison beyond search placement. |

## Open Work Before Phase 5 Can Close

1. Export final screenshot pack at desktop, mobile, and real laptop/zoom-like widths.
2. Complete manual screenshot review for all desktop frames that are now under automated geometry gates.
3. Finish full mobile homepage and mobile menu visual comparison, not only the first viewport/search placement.
4. Confirm whether `DALSI INFORMACE` is intentionally a navigation hub/redirect or must become a standalone page.
5. Confirm whether `popup` requires only the product benefit popup or also a separate contact modal state.
6. Spot-check non-Timberwolf product details and category variants for content-length/galleries.
7. Get owner/client visual sign-off after all deltas are closed.

## Latest Automated Evidence

Passed after the corrective frame-by-frame implementation:
- `npm run css:build`
- `npm run figma:audit`
- `npm run visual:smoke`
- `npm run qa:local`
