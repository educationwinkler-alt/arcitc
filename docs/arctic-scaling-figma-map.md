# Arctic Figma Geometry Map

Date: 2026-05-25  
Status: baseline mapping aligned with canonical recovery model

## Rules
- Figma desktop reference canvas: `1920 x 795` for hero/heading compositions.
- Desktop scaling factor: `s = viewportWidth / 1920` for `1280 <= viewportWidth <= 1919`, otherwise `s = 1`.
- Desktop shell width: `1400 * s` (clamped to viewport minus `40px`).
- Desktop header card: `x = (viewportWidth - shellWidth)/2`, `y = 18`, `w = shellWidth`, `h = 105*s`.

## Mandatory Page Map
| Figma frame/node | URL | DOM selector | Geometry expectation |
| --- | --- | --- | --- |
| `HP` header card | `/` + all templates | `.f-header__container` | centered shell, `h = 105*s` |
| `HP` hero frame | `/` | `.f-section--slides` | `w = viewportWidth`, `h = 795*s` |
| `HP` hero copy | `/` | `.f-caption` | anchored to shell left, `x ~= shellLeft + 6*s`, `y ~= 280*s` |
| `HP` arrows | `/` | `.f-slides__control--prev`, `.f-slides__control--next` | visible, in-bounds, `y ~= 382*s` |
| `HP` promo sticker | `/` | `.f-hero-promo` | anchored from scaled canvas, in-bounds |
| `KATEGORIE` heading | `/virivky/`, `/swimspa/` | `.f-heading--term` | `h = 795*s` desktop shell model |
| `DETAIL KONKRETNIHO PRODUKTU` | `/product/timberwolf/` | `.f-heading--product-detail` | `h = 795*s` desktop shell model |
| `KONTAKT` heading + map card | `/kontakt/` | `.f-heading`, `.f-local-map__card` | shell-aligned, scaled desktop offsets |
| `footer` component | shared pages | `.f-footer--arctic` | full-width footer composition + shell-aligned content |

## Viewport QA Matrix
Desktop: `1903`, `1536`, `1456`, `1440`, `1366`, `1280`  
Tablet/mobile sanity: `1024`, `390`

## Acceptance Gates
- no horizontal overflow,
- header card centered on every audited template,
- hero/heading compositions scale down proportionally on desktop,
- desktop arrows remain visible and inside viewport,
- no Baspa fallback geometry for header/heading/slides.
