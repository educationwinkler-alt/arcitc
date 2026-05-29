# Component Contract Usage Map - PR-D - 2026-05-30

## Rule

Repeated visual blocks are implemented through a named component contract. Page-specific CSS may place the whole section in the page flow, but it must not redefine the component internals unless a named variant is added here.

## Contracts

| Component | Canonical marker | Current usage | Variant markers | Notes |
| --- | --- | --- | --- | --- |
| Contact CTA | `.f-section--component-contact`, `.f-contact-cta--shared` | Global footer-injected CTA on all non-contact pages | Context comes from existing WordPress body/page classes only for section placement | The shared contract owns CTA surface, footer handoff, typography, bar, hours pill, and button treatment. |
| Footer handoff | `.f-footer--handoff` | Global footer | none | The dark footer background is extended upward to cover the historical frost/cyan gap without shifting layout. |
| Showroom collage | `.f-showroom-panel--collage` | Homepage, product categories, default page/single shared section | none | Uses PR-C owner showroom assets and shared crop/object-position rules. |
| Configurator CTA | `.f-configurator-cta--shared` | Product categories, product detail configurator | `.f-configurator-cta--hot-tub`, `.f-configurator-cta--swimspa`, `.f-configurator-cta--product` | Shared gradient/visual veil lives in `_component-contracts.less`; swimspa gets separate text defaults and color treatment. |
| Progress steps | `.f-progress-layout--shared` | Homepage, product categories | none | Shared typography/number treatment lives in contract; page CSS can still position the whole section. |
| Product benefit card | `.f-product-benefit--interactive`, `.f-product-benefit--static` | Product detail benefits and options | interactive opens `.f-off--benefit-popup`; static has no plus/trigger | Static cards must not render a plus or invisible trigger. |
| Benefit popup | `.f-off--benefit-popup`, `.f-benefit-popup` | Product detail shell benefit | none | Contract owns dark overlay, white rounded modal, close button radius and modal shadow. |

## PR-D Guard

`npm run component:smoke` validates:

- Shared contract markers exist on homepage/category/product pages.
- Swimspa configurator does not reuse hot-tub wording.
- Static product benefit/options cards do not render fake plus affordances or invisible triggers.
- `_component-contracts.less` contains the canonical selectors for PR-D components.

## Deferred To Later PRs

- Old page-specific CSS blocks still provide page flow positioning and exact desktop coordinates for existing visual audits. They should be reduced gradually when each page-specific PR replaces absolute positioning with component variants.
- Product/category media polish remains in PR-E.
- Special page rebuilds remain in PR-F.
