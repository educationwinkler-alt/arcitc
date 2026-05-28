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
- Decision lock (2026-05-27): `/dalsi-informace/` is intentionally handled as a navigation-hub redirect (`301` to `/#order-progress`) and is not in standalone-frame implementation scope.

## Implemented in This Corrective Pass

| Figma item | Node / source | Current implementation | Verification |
| --- | --- | --- | --- |
| Header default | `Arctic Spas grafika`, component set `1:1831`, variant `1:1832` | Existing desktop header retained and kept above menu overlay | `npm run figma:audit` |
| Header search state | `1:1855` `hledani` | Search opens as a header-shaped Figma panel with logo, 409x44 search field, CTA button, and correct placeholder | `desktopHeaderSearch` audit |
| Header desktop menu state | `1:1868` `menu` | Product mega menu for `Virivky` and `Celorocni bazeny`, with product columns, round thumbnails, separators, and promo card | `desktopHeaderMega` audit |
| Header desktop menu real viewport | `1:1868` `menu` + laptop/120%-like viewport | Mega menu panel now uses the dark Figma styling, keeps the inner promo CTA visible, and hides the homepage promo behind the open menu at 1586x756 effective viewport | `desktopHeaderRealViewport` audit |
| Header mega hover cursor-travel stability | `1:1868` `menu` interaction | Desktop mega menu now stays open while moving the cursor from the top trigger into submenu links (with delayed close guard), preventing accidental close/flicker during trigger-to-panel travel | `desktopHeaderRealViewport.hoverTravel` audit |
| Zoom-out full-bleed/footer gutter guard | Homepage + footer in zoom-out-like wide desktop states | Footer background rendering changed from fixed 1920px image sizing to fluid cover behavior and audited at wide viewports so side gutters are not reintroduced | `zoomOut:2240`, `zoomOut:2560` audits |
| Compact laptop / Windows 175% scaling layer | Homepage at `904-1279px`, including docked-DevTools and effective `1097x617` viewport | Added coherent narrow and compact-laptop layers with shared variables, bounded hero height, default-hidden homepage promo outside explicit desktop opt-in, deliberate two-column category cards, and hero/category spacing | `narrowHomepage:904x617`, `narrowHomepage:1023x617`, `compactLaptop:1024x617`, `compactLaptop:1097x617`, `compactLaptop:1279x720`, `scaledLaptopBoundary:1097`, `scaledDesktopBoundary:1280`, `scaledDesktopBoundary:1366` audits |
| Homepage hero/category boundary | Homepage desktop and compact widths | Inner slider/background height is now tied to the same hero height as the outer section, preventing category cards from being painted under the hero slide | `heroBoundary` checks inside desktop, scaled desktop, narrow, and compact audits |
| Footer quick contact/copyright | Footer frame | Footer now renders Lukáš Dušek photo avatar, `BASPA s.r.o.` copyright text, single-line bottom privacy link/copyright, and no eboost credit | `sharedFooter`, `footerAvatarSource`, `footerText` audits |
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
| DALSI INFORMACE | `1:1216` | Additional information hub | Resolved: intentional navigation/dropdown hub behavior. Route stays as `301` to `/#order-progress`; standalone page is marked not applicable in current IA scope. |
| KOLIK STOJI UDRZBA | `1:1395` | Maintenance cost page | Desktop frame geometry is automated. Manual screenshot sign-off remains. |
| SERVIS | `1:1426` | Service page | Desktop frame geometry is automated. Manual screenshot sign-off remains. |
| popup | `1:1959` | Popup/contact modal state | Product benefit popup state is automated. Contact modal state still needs explicit confirmation if it is a separate required interaction. |
| GM - HP | `1:1973` | Mobile homepage | Partially automated: mobile hero, logo, menu button, hidden promo state, categories. Needs full mobile page scroll sign-off. |
| GM - HP menu | `1:2208` | Mobile navigation state | Partially present: search placement is checked. Needs complete visual comparison beyond search placement. |

## Open Work Before Phase 5 Can Close

1. Export final screenshot pack at desktop, mobile, real laptop/zoom-like widths, and physical/equivalent Windows 175% scaling.
2. Complete manual screenshot review for all desktop frames that are now under automated geometry gates.
3. Finish full mobile homepage and mobile menu visual comparison, not only the first viewport/search placement.
4. Confirm whether `popup` requires only the product benefit popup or also a separate contact modal state.
5. Spot-check non-Timberwolf product details and category variants for content-length/galleries.
6. Get owner/client visual sign-off after all deltas are closed.

## Resolved Decisions

- `DALSI INFORMACE` (`1:1216`) is closed as a conscious IA deviation: redirect-only hub route (`301` to `/#order-progress`), no standalone implementation in current scope.

## Latest Automated Evidence

Passed after the corrective frame-by-frame implementation:
- `npm run css:build`
- `npm run figma:audit`
- `npm run visual:smoke`
- `npm run qa:local`

Passed after the real viewport mega menu correction:
- `npm run figma:audit`
- `npm run visual:smoke`
- `npm run qa:local`

Passed after the Phase 5A promo visibility / narrow viewport correction:
- `npm run css:build`
- `npm run figma:audit`
- Local screenshot evidence: `docs/screenshots/phase-5a-homepage-904x617-2026-05-27.png`, `docs/screenshots/phase-5a-homepage-1097x617-2026-05-27.png`, `docs/screenshots/phase-5a-footer-element-1920-2026-05-27.png`

Passed after the footer parity correction:
- `npm run css:build`
- `npm run figma:audit`
