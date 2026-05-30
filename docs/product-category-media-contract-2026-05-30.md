# Product/category media contract - PR-E - 2026-05-30

## Goal

Product/category media must be wired once as a reusable contract, not patched page by page. The same product card, hero, swatch, benefit, and mega-menu rules apply on `/virivky/`, `/swimspa/`, and product detail pages.

## Contracts

| Area | Contract | Source rule | Guard |
| --- | --- | --- | --- |
| Product listing cards | `data-product-media="product-image"` or `featured-image`; missing state is explicit `f-listing__image--product-missing` | real product attachment first, featured image second, neutral labeled placeholder last | `npm run product-media:smoke` fails if `/virivky/` or `/swimspa/` render missing product media |
| Category intro images | fixed source mapping in `templates/section/category-intro.php` | hot tubs use approved Figma category images; swimspa uses Figma category image plus legacy swimspa lifestyle image | `product-media:smoke` checks required image URLs |
| Product detail hero | validated `product_images` gallery; invalid attachment IDs are ignored | Timberwolf uses seeded real Timberwolf media, not Figma detail placeholder | `product-media:smoke` checks Timberwolf gallery markers and forbids `detail-timberwolf-hero.jpg` |
| Swatches | valid image attachments only | owner acrylic swatches only; missing cabinet swatches stay hidden/WAITING_ON_OWNER | `asset:smoke` and `product-media:smoke` forbid Figma color/cabinet assets |
| Product benefit/options media | named CSS media variants on `.f-product-benefit__media--...` | CSS tokens/icons only; no invented photos and no gray generic placeholder disk | `component:smoke` checks behavior, `product-media:smoke` checks media variants |
| Mega menu thumbnails | `data-product-media="featured-image"` or explicit missing state | product thumbnail first, neutral fallback only if a future product lacks a thumbnail | `product-media:smoke` fails if public mega menu renders missing thumbnails |

## Non-goals

- Do not add new owner/product photo binaries in PR-E.
- Do not use Figma product-card placeholders (`category-product-card-*.png`) as content media.
- Do not invent cabinet/material swatches. Missing final swatches remain `WAITING_ON_OWNER`.
- Do not create per-page copies of product card CSS when the issue belongs to the shared product media contract.

## Implementation Notes

- `modules/products/templates/post/listing/image.php` now normalizes product image meta to valid image attachments and renders one canonical media slot per card.
- `templates/image/gallery-slideshow.php` and product detail heading ignore broken gallery IDs before deciding that a hero gallery exists.
- `templates/section/product-benefits.php` and `templates/section/product-options.php` assign stable media variant classes so the shared CSS contract can style all cards consistently.
- `templates/navigation/mega.php` marks thumbnails with a media status and exposes a neutral missing state only as a safety net.
