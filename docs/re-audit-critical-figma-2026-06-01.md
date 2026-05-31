# Kriticky re-audit Figma vs local - 2026-06-01

Tento audit doplnuje finalni plan po manualnich screenshotech z `/product/mckinley/`. Predchozi plan nebyl dost explicitni pro header, product detail physical parity, Lukas Dusek media crop, benefit pseudoikony a contact CTA hours overflow.

Implementation note 2026-06-01: findings RA-01 az RA-07 jsou implementovane a kryte `npm run product-detail:physical`; cele `npm run qa:local` proslo.

## Metoda

| Zdroj | Pouziti |
| --- | --- |
| Figma MCP `xeOew3dFjDVfjXZrJ09emM`, node `1:1461` | Product detail physical source. |
| Figma MCP `xeOew3dFjDVfjXZrJ09emM`, node `1:1831` / `1:1832` | Header component source. |
| Figma MCP `xeOew3dFjDVfjXZrJ09emM`, node `1:50` | Lukas Dusek avatar source/crop. |
| Figma MCP `xeOew3dFjDVfjXZrJ09emM`, node `1:1498` | Benefit card media source. |
| Local Playwright DOM metrics at 1920 x 1080 | `/`, `/product/mckinley/`, `/product/timberwolf/`, `/virivky/`, `/swimspa/`. |

## Findings

| ID | Severity | Finding | Evidence | Plan block |
| --- | --- | --- | --- | --- |
| RA-01 | P0 | Product header/sticky nav collision. The product sticky nav starts at `y=749` and ends at `842` while the product hero ends at `795`, so it overlaps the hero content area. | Local `/product/mckinley/`: `.f-heading--product-detail` `0,0,1920,795`; `.f-links--product` `0,749,1920,93`. Figma detail has nav row at `x=260 y=749 w=1400` but it must not hide hero facts or create visual collision at real browser chrome heights. | P0-10 |
| RA-02 | P0 | Header component is not being guarded against internal overlap across product pages. | Figma header component `1:1832`: 1400 x 105, inner white panel 1400 x 85 at y=20. Local product header is absolute, text/nav/search/CTA can collide with hero/sticky states because guard checks mostly source/status, not physical overlap. | P0-10 |
| RA-03 | P0 | Non-Timberwolf product detail body does not match Figma. McKinley renders a single text configuration card and huge empty area instead of Figma image-card configuration layout. | Figma detail `1:1461` includes configuration image frames `1:1471` and `1:1473`, swatch frames, benefit media and CTA. Local `/product/mckinley/`: `.f-product-configurations` height `278`, one card, no configuration image card; sidebar floats in a mostly empty 546px region. | P0-11 |
| RA-04 | P0 | Product detail sidebar uses static hours and is not part of the shared dynamic hours component. | `modules/products/templates/post/single/sidebar.php` renders literal `Po - Pá 8:00-17:00 h`. Local `.f-product-contact-card__details` has no `js-hours__status` and no shared open/closed behavior. | P0-14 |
| RA-05 | P0 | Shared contact CTA hours pill overflows the inner red bar in product context. | Local `/product/mckinley/`: `.f-contact-cta__bar` bottom `6052`; `.f-contact-cta__hours` bottom `6062.4`, leaking about `10px` below the bar. Homepage currently looks closer, so this must be context-specific, not a global guess. | P0-14 |
| RA-06 | P0 | Benefit/option cards use CSS pseudoicons that are not Figma media. | Figma benefit group `1:1498` uses image rectangles `1:1500`, `1:1510`, etc. at `87 x 87` plus a real red plus frame. Local benefit cards render generated `:before/:after` shapes and repeated pseudoicons. | P0-13 |
| RA-07 | P0 | Lukas Dusek photo/crop is not explicitly validated as a reusable media contract. | Figma avatar node `1:50` is exactly `58 x 58` with imageRef `9257bc...` and crop transform. Local applies a global CSS transform to any `contact-lukas-dusek.png` avatar and uses the same asset in footer, contact CTA and product sidebar without per-context screenshot/crop guard. | P0-12 |

## Immediate Plan Corrections

| Correction | Change |
| --- | --- |
| Header is not "do not reopen" | Move header physical overlap and product sticky nav to explicit P0. Header status may stay dynamic, but geometry is not closed. |
| Product detail contract is not enough | Add physical parity requirements for non-Timberwolf details before calling the renderer done. |
| CSS pseudoicons are forbidden | Treat current generated benefit media as a regression placeholder, not acceptable final state. |
| Contact CTA must be context-tested | Homepage passing is insufficient; product, category, swimspa and footer contexts need separate CTA hours/bar guards. |
| Dusek avatar needs source/crop guard | Re-export or validate node `1:50` and guard computed visible crop in all reused contexts. |

## Required Guards

| Guard | Must fail when |
| --- | --- |
| `product-header-physical-smoke` | Product sticky nav overlaps hero/facts/header at 1920 and compact laptop viewport. |
| `product-detail-physical-smoke` | Non-Timberwolf detail lacks configuration image card or creates empty 500px+ layout holes. |
| `contact-media-smoke` | Dusek avatar crop/source differs from Figma node `1:50` or is scaled by unscoped global CSS. |
| `contact-cta-hours-smoke` | Hours pill overflows contact CTA bar in homepage/category/product/swimspa contexts. |
| `benefit-media-smoke` | `.f-product-benefit__media` has generated pseudoicon treatment without real image export or explicit waiting placeholder state. |
