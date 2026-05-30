# Component Contract Usage Map - PR-D - 2026-05-30

## Rule

Repeated visual blocks are implemented through a named component contract. Page-specific CSS may place the whole section in the page flow, but it must not redefine the component internals unless a named variant is added here.

## Contracts

| Component | Canonical marker | Current usage | Variant markers | Notes |
| --- | --- | --- | --- | --- |
| Contact CTA | `.f-section--component-contact`, `.f-contact-cta--shared` | Global footer-injected CTA on all non-contact pages | Context comes from existing WordPress body/page classes only for section placement | The shared contract owns CTA surface, typography, bar, hours pill, and button treatment without repainting the footer. |
| Footer mountain | `.f-footer--arctic` | Global footer | none | The footer must keep the Figma mountain background (`footer-background.jpg`). The old `.f-footer--handoff` navy override is forbidden. |
| Showroom collage | `.f-showroom-panel--collage` | Homepage, product categories, default page/single shared section | none | Uses PR-C owner showroom assets and shared crop/object-position rules. |
| Configurator CTA | `.f-configurator-cta--shared` | Product categories, product detail configurator | `.f-configurator-cta--hot-tub`, `.f-configurator-cta--swimspa`, `.f-configurator-cta--product` | Shared gradient/visual veil lives in `_component-contracts.less`; swimspa gets separate text defaults and color treatment. |
| Progress steps | `.f-progress-layout--shared` | Homepage, product categories | none | Shared typography/number treatment lives in contract; page CSS can still position the whole section. |
| Product listing media | `data-product-media`, `.f-listing__image--product-media`, `.f-listing__image--featured-media`, `.f-listing__image--product-missing` | Product category cards and product loops | missing state is a neutral placeholder safety net | PR-E owns the image-source cascade: product image meta -> featured image -> explicit missing state. |
| Product benefit card | `.f-product-benefit--interactive`, `.f-product-benefit--static` | Product detail benefits and options | interactive opens `.f-off--benefit-popup`; static has no plus/trigger | Static cards must not render a plus or invisible trigger. |
| Product benefit media | `.f-product-benefit__media--...` | Product detail benefits and options | named variants such as `shell`, `heatlock`, `onzen`, `wifi`, `covana` | Shared CSS icon/token treatment replaces the old gray generic media disk without inventing product photos. |
| Benefit popup | `.f-off--benefit-popup`, `.f-benefit-popup` | Product detail shell benefit | none | Contract owns dark overlay, white rounded modal, close button radius and modal shadow. |
| Mega menu product media | `.f-mega-menu__thumb`, `.f-mega-menu__thumb--missing`, `data-product-media` | Desktop hot tub/swimspa mega menu | missing thumbnail is explicit neutral fallback | PR-E guard fails if current seeded public products expose missing thumbnails. |
| Support/download shell | `.f-main--support-contract` | `/podpora/`, `/ke-stazeni/` | support page includes tabs/FAQ/form; downloads page includes downloads only | PR-G owns shared support surface guardrails without duplicating page-specific internals. |
| Support chips/tabs | `.f-support-tabs--contract`, `.f-chip-list--contract` | Support tabs, FAQ filters, download filters | interactive chips expose `role="tab"` and active state | PR-G guard checks tab state and mobile containment. |
| FAQ accordion | `.f-support-faq-card--contract`, `data-support-faq-card` | `/podpora/` FAQ rows | open/closed state controlled by `support-download-interactions.js` | Plus/minus rows must update ARIA and panel visibility. |
| Downloads accordion/cards | `.f-downloads--contract`, `.f-download-group--contract`, `.f-download-card--contract` | `/podpora/` downloads section, `/ke-stazeni/` page | group rows open/close; cards keep thumbnail/body/CTA attached | PR-G guard checks toggle state, filter state, and CTA attachment. |
| Support service form | `.f-support-form--contract`, `.f-support-form__card--contract` | `/podpora/` service form card | contact-page fallback action | Contract bounds inputs/buttons and prevents mobile compression. |
| Mobile shell guard | `.f-off--navigation` plus homepage section boundaries | homepage mobile, mobile menu | compact menu hides desktop submenus | PR-G guard opens the mobile menu and checks no horizontal overflow. |

## PR-D Guard

`npm run component:smoke` validates:

- Shared contract markers exist on homepage/category/product pages.
- Swimspa configurator does not reuse hot-tub wording.
- Static product benefit/options cards do not render fake plus affordances or invisible triggers.
- `_component-contracts.less` contains the canonical selectors for PR-D components.

`npm run product-media:smoke` validates the PR-E product/category media contract:

- Product category cards use real seeded product media and no Figma product-card placeholders.
- Timberwolf detail uses seeded Timberwolf media and owner swatches.
- Benefit/options cards expose named media variants instead of the old gray placeholder disk.
- Mega menu exposes all seeded product thumbnails with no missing media state.

`npm run support-mobile:smoke` validates the PR-G support/download/mobile contract:

- Support/download pages expose shared contract markers and the support/download interaction script.
- FAQ plus/minus rows and download groups update ARIA plus panel state.
- Download filters expose active tab state and CTAs stay attached to card content.
- Mobile homepage/menu/support/download pages do not create horizontal overflow.

## Deferred To Later PRs

- Old page-specific CSS blocks still provide page flow positioning and exact desktop coordinates for existing visual audits. They should be reduced gradually when each page-specific PR replaces absolute positioning with component variants.
- Product/category media polish remains in PR-E.
- Special page rebuilds remain in PR-F.
