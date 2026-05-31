# Asset Source Map - PR-C - 2026-05-29

## Rules

- `available`: source asset exists locally and can be wired into templates or seed data.
- `usable-fallback`: asset is not the final owner-supplied target, but it is a real legacy/import asset and may be used until final owner media arrives.
- `design-only`: Figma export is layout/reference material only. It must not be treated as final product, showroom, swatch, or team photography.
- `WAITING_ON_OWNER`: asset is required for final content, but no verified owner/legacy source was found.
- `WAITING_ON_FIGMA_EXPORT`: Figma shows a required media layer, but the exact export is not wired yet. Do not present CSS-only treatment as final media.

## Repository And Delivery Policy

Observed on 2026-05-30: production `baspa.cz` serves WebP variants such as `.jpg.webp` and exposes an `ewww` marker in public HTML, so production image optimization should be handled by the WordPress media pipeline and an optimizer/WebP plugin rather than by committing large binaries into the source repository.

| Asset type | Repo policy | Production policy |
| --- | --- | --- |
| Small seed/fallback assets required for local/staging | Allowed in git when optimized and intentionally used. | Can still be uploaded into WP media during seed/import. |
| Owner originals, bulk galleries, large product photo sets | Do not commit to git. | Store in WP uploads/media library or external asset storage. |
| Production responsive images/WebP | Do not pre-build as source truth in git. | Generate via WordPress image sizes plus optimizer plugin, e.g. EWWW/WebP-style pipeline. |
| Larger versioned asset library | Use only after Git LFS/external storage decision. | Treat as infrastructure scope, not incidental PR work. |

Current PR-C exception: owner swatches and web-sized showroom derivatives are committed because they are a small, verified, local/staging seed set. Future expansion must follow this policy.

## Product Photography

| Area | Status | Source | Implementation |
| --- | --- | --- | --- |
| Product listing cards | `usable-fallback` | `wp-content/uploads/import/legacy-products/*.jpg` | Seed keeps legacy product photos for migrated catalog items. |
| Lunar | `available` | `wp-content/uploads/import/lunar-main.jpg`, `lunar-corner.png`, `lunar-cover-black.png` | Seeded as product image/gallery. |
| Orion | `available` | `wp-content/uploads/import/orion-main.jpg`, `orion-lifestyle.jpg` | Seeded as product image/gallery. |
| Timberwolf | `available` | `wp-content/uploads/import/timberwolf-signature.jpg`, `timberwolf-prestige.jpg`, `timberwolf-side.jpg` | Seeded as product image/gallery. |
| Missing final galleries for the rest of the catalog | `WAITING_ON_OWNER` | Owner must provide final product/detail galleries. | Do not invent replacement product photos. Keep legacy fallback only where already mapped. |

## Product UI Media

| Area | Status | Source | Implementation |
| --- | --- | --- | --- |
| Product configuration fallback media | `available` | `wp-content/uploads/import/figma/detail-config-prestige.png`, `detail-config-signature.png` from Figma nodes `1:1472`, `1:1474` | Used only when a configuration has no owner/product attachment, so product detail keeps the Figma media-card layout instead of a text-only blank card. |
| Product benefit/options card media | `available` | `wp-content/uploads/import/figma/benefit-media-01.png` through `benefit-media-18.png` from Figma detail media nodes `1:1500` through `1:1670` | Every benefit/option media slot renders an exported Figma image with `data-asset-status="available"`; CSS pseudoicons are forbidden. |
| Product benefit shell popup media | `available` | `wp-content/uploads/import/figma/popup-shell-detail.png` | Popup detail keeps the real exported shell detail image. |
| Product mega menu thumbnails | `usable-fallback` | seeded product featured images from real product attachments | PR-E marks thumbnail state with `data-product-media`; missing state is explicit and guarded, but current seeded hot tub/swimspa products must not hit it. |

## Acrylic And Cabinet Swatches

| Asset | Status | Source | Implementation |
| --- | --- | --- | --- |
| Dakota | `available` | `assets-source/.../Fotografie pro web/barva akrylátu/Acrylic Swatches Dakota.jpg` copied to `wp-content/uploads/import/owner-swatches/acrylic-dakota.jpg` | Seeded into `product_acrylic_color_options`. |
| Kalahari | `available` | `assets-source/.../Fotografie pro web/barva akrylátu/Acrylic Swatches Kalahari.jpg` copied to `wp-content/uploads/import/owner-swatches/acrylic-kalahari.jpg` | Seeded into `product_acrylic_color_options`. |
| Odyssey | `available` | `assets-source/.../Fotografie pro web/barva akrylátu/Acrylic Swatches Odyssey.jpg` copied to `wp-content/uploads/import/owner-swatches/acrylic-odyssey.jpg` | Seeded into `product_acrylic_color_options`. |
| Espresso | `available` | `assets-source/.../Fotografie pro web/barva akrylátu/espresso-swatch.jpg` copied to `wp-content/uploads/import/owner-swatches/acrylic-espresso.jpg` | Seeded into `product_acrylic_color_options`. |
| Platinum Swirl swatch | `WAITING_ON_OWNER` | No verified owner swatch found. | Not rendered as an image swatch. |
| Cedar cabinet swatch | `WAITING_ON_OWNER` | No verified owner cabinet swatch found. | `product_cabinet_color_options` is cleared by seed until real image exists. |
| Maintenance-free cabinet swatch | `WAITING_ON_OWNER` | No verified owner cabinet swatch found. | `product_cabinet_color_options` is cleared by seed until real image exists. |

## Showroom

| Area | Status | Source | Implementation |
| --- | --- | --- | --- |
| Showroom exterior | `available` | `wp-content/uploads/import/showroom-main.jpg` resized to `wp-content/uploads/import/owner-showroom/showroom-main-web.jpg` | Used by `/showroom/`, reusable showroom panel, and showroom page thumbnail. |
| Showroom interior hot tub | `available` | `wp-content/uploads/import/showroom-detail.jpg` resized to `wp-content/uploads/import/owner-showroom/showroom-detail-web.jpg` | Used by `/showroom/` and reusable showroom panel. |
| Covana/interior detail | `available` | `assets-source/.../Fotografie pro web/Fotografie prodejny/20260123_131159.jpg` resized to `wp-content/uploads/import/owner-showroom/showroom-covana-interior-web.jpg` | Used by `/showroom/` and reusable showroom panel. |
| Figma showroom exports | `design-only` | `wp-content/uploads/import/figma/showroom-*.png`, `showroom-hero-bazeny.jpg`, `showroom-detail-*.png` | Must not be used as production showroom photography. |

## Team And Contact Media

| Area | Status | Source | Implementation |
| --- | --- | --- | --- |
| Team/person portraits | `WAITING_ON_OWNER` | No verified team/person photos found in owner archive. | `/o-nas/` renders neutral initials placeholders with `data-asset-status="WAITING_ON_OWNER"`. |
| Contact directory portraits | `WAITING_ON_OWNER` | No verified owner portraits found for the six contact cards. | `/kontakt/` renders the six Figma contact-frame people, but avatars stay neutral placeholders with `data-asset-status="WAITING_ON_OWNER"` until owner portraits arrive. |
| Figma team portraits | `design-only` | `wp-content/uploads/import/figma/about-team-*.png` | Removed from `/o-nas/` production rendering. |
| Contact/footer map images | `usable-fallback` | `wp-content/uploads/import/figma/contact-map-showroom.png`, `footer-map.png` | Allowed only as temporary map/layout fallback until real map/embed scope is handled. |

## Services

| Area | Status | Source | Implementation |
| --- | --- | --- | --- |
| Service cards | `usable-fallback` | `wp-content/uploads/import/legacy-services/*.jpg` | `template-services.php` keeps real legacy photos and avoids invented icon/media fallbacks. |

## Warranty

| Area | Status | Source | Implementation |
| --- | --- | --- | --- |
| Warranty card product images | `WAITING_ON_OWNER` | No verified owner/legacy images were found for the Custom, Classic, and Core warranty cards. | `/zaruka/` renders the Figma card/media layer and marks each missing card media as `data-asset-status="WAITING_ON_OWNER"`; no Figma/category image is used as fake production media. |

## Guardrails Added

- `tools/asset-source-smoke.js` checks production pages for forbidden Figma showroom/team/swatch media.
- Product color rendering filters image options to valid image attachments only, preventing empty swatch cards when a configured image is missing.
- Missing cabinet swatches are documented as `WAITING_ON_OWNER` instead of rendered with design-only placeholders.
- Warranty card media is guarded as `WAITING_ON_OWNER` until owner-approved card images exist.
