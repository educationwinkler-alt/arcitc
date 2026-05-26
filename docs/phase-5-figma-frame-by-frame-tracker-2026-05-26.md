# Phase 5 Figma Frame-by-Frame Tracker

Date: 2026-05-26

## Status

Phase 5 is reopened. The previous responsive/zoom pass was useful, but it was not a complete Figma frame-by-frame implementation.

Current rule:
- Do not mark Phase 5 complete until every required Figma frame/state below is either implemented and verified, or explicitly marked not applicable with a reason.
- Automated geometry gates are evidence, not a replacement for manual visual review.
- Figma covers the visual/UX layer, Baspa.cz covers admin/function patterns, and the old Arctic PHP site covers content.

## Implemented in This Corrective Pass

| Figma item | Node / source | Current implementation | Verification |
| --- | --- | --- | --- |
| Header default | `Arctic Spas grafika`, component set `1:1831`, variant `1:1832` | Existing desktop header retained and kept above menu overlay | `npm run figma:audit` |
| Header search state | `1:1855` `hledani` | Search now opens as a header-shaped Figma panel with logo, 409x44 search field, CTA button, and correct placeholder | `desktopHeaderSearch` audit |
| Header desktop menu state | `1:1868` `menu` | New product mega menu for `Vířivky` and `Celoroční bazény`, with product columns, round thumbnails, separators, and promo card | `desktopHeaderMega` audit |
| Mobile menu search placement | `GM - HP menu` `1:2208` | Existing compact menu already places search at 323x44 around y=527 on 375px viewport | Manual/spot check, needs fuller screenshot sign-off |

## Frame Inventory

| Frame | Node | Required status | Current status |
| --- | --- | --- | --- |
| HP | `1:14` | Full desktop and mobile visual parity | Partially automated: hero, categories, promo, benefits, showroom, progress, references, CTA, footer. Needs manual final screenshot sign-off. |
| KATEGORIE | `1:262` | Product category layout for hot tubs and swimspa | `Vířivky` desktop geometry is automated. `Swimspa` needs full frame-specific geometry pass, not only shell/footer. |
| DETAIL KONKRETNIHO PRODUKTU | `1:1461` | Product detail layout and sections | Timberwolf desktop geometry is automated. Other product variants need spot-checks for content-length and gallery differences. |
| SHOWROOM | `1:442` | Dedicated showroom page | Not fully frame-audited yet. Shared shell/footer only. |
| VLASTNOSTI | `1:585` | Feature listing page | Not fully frame-audited yet. Shared shell/footer only. |
| VLASTNOSTI DETAIL | `1:1302` | Feature detail page | Not fully frame-audited yet. Needs template mapping and screenshot comparison. |
| SLUZBY | `1:658` | Services page | Not fully frame-audited yet. Shared shell/footer only. |
| CERTIFIKATY | `1:694` | Certificates page | Not fully frame-audited yet. Shared shell/footer only. |
| ZARUKA | `1:719` | Warranty page | Not fully frame-audited yet. Shared shell/footer only. |
| PODPORA | `1:752` | Support/FAQ/download page | Not fully frame-audited yet. Shared shell/footer only. |
| O NAS | `1:945` | About page | Not fully frame-audited yet. Shared shell/footer only. |
| KONTAKT | `1:1037` | Contact page | Desktop geometry and map source automated. Needs manual final sign-off. |
| REFERENCE | `1:1127` | Reference page | Not fully frame-audited yet. Shared shell/footer only. |
| DALSI INFROMACE | `1:1216` | Additional information hub | Not fully frame-audited yet. Needs route/template confirmation. |
| KOLIK STOJI UDRZBA | `1:1395` | Maintenance cost page | Not fully frame-audited yet. Shared shell/footer only. |
| SERVIS | `1:1426` | Service page | Not fully frame-audited yet. Shared shell/footer only. |
| popup | `1:1959` | Popup/contact modal state | Not fully frame-audited yet. Needs modal screenshot and interaction test. |
| GM - HP | `1:1973` | Mobile homepage | Partially automated: mobile hero, logo, menu button, promo, categories. Needs full mobile page scroll sign-off. |
| GM - HP menu | `1:2208` | Mobile navigation state | Partially present. Needs complete visual comparison beyond search placement. |

## Open Work Before Phase 5 Can Close

1. Build automated or manual screenshot checkpoints for every frame listed as not fully frame-audited.
2. Finish `Swimspa` category parity instead of relying on the `Vířivky` category pass.
3. Add page-specific visual checks for showroom, support, reference, about, services, warranty, certificates, maintenance, service, and additional info frames.
4. Add popup/contact modal parity check for frame `1:1959`.
5. Export final screenshot pack at desktop, mobile, and real laptop/zoom-like widths.
6. Get owner/client visual sign-off after all deltas are closed.

## Latest Automated Evidence

Passed after the corrective header/search/menu implementation:
- `npm run css:build`
- `npm run figma:audit`
