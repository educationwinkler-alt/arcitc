# Phase 5 Visual Sign-Off

Date: 2026-05-26
Scope: technical visual sign-off for the Arctic Spas redesign.

## Superseded Scope Note

This document is not the final Phase 5 sign-off. It records the responsive/zoom resilience pass only.

Phase 5 was reopened after manual review found that full Figma frame/state coverage was still missing, especially header search and product menu states. Use `docs/phase-5-figma-frame-by-frame-tracker-2026-05-26.md` as the active Phase 5 checklist.

## Source Rules

- Figma is the visual and UX source of truth.
- Baspa.cz is the functional and WordPress admin source of truth.
- The old `../Arctic-spas/` PHP site is the content source of truth.
- Figma does not define every real responsive state, so in-between laptop widths and browser zoom must preserve usability and visual intent.

## Phase 5 Responsive Decision

The previous desktop navigation threshold was too optimistic for real laptop and browser zoom conditions.

Final technical rule:
- Desktop header geometry is used from `1400px` upward.
- Compact navigation is used below `1400px`.
- Browser zoom at 120% is treated as an effective narrower CSS viewport.
- Homepage promo card keeps the Figma x-position until it would clip, then clamps inside the viewport with a 20px safe inset.
- The promo card is hidden in the compact/laptop band where it would compete with the hero composition.

## Fixed Deltas

| Area | Before | After | Status |
| --- | --- | --- | --- |
| Header at 1366/1280 | Desktop nav collapsed into the top of the header panel | Compact header/navigation is used | Pass |
| Header at 120% zoom equivalent | Menu could overlap and duplicate perceived contact/header rhythm | Effective narrower widths use compact shell | Pass |
| Homepage promo card | Desktop card could be partially off-screen | Card is clamped inside the viewport | Pass |
| Promo CTA text | Button could approach/crop at narrower desktop widths | Button scales with card children and stays visible | Pass |
| Automated guard | Existing audit allowed the broken 1366/1280 desktop state | Audit now checks laptop/zoom matrix and promo in-bounds | Pass |

## Viewport Matrix

| Viewport | Expected shell | Result |
| --- | --- | --- |
| 1920 | desktop | Pass |
| 1903 | desktop | Pass |
| 1600 | desktop | Pass |
| 1536 | desktop | Pass |
| 1456 | desktop | Pass |
| 1440 | desktop | Pass |
| 1366 | compact | Pass |
| 1280 | compact | Pass |
| 1024 | compact | Pass |
| 768 | compact/mobile | Pass |
| 430 | mobile | Pass |
| 390 | mobile | Pass |

## Page Coverage

| Page / template | Coverage type | Result |
| --- | --- | --- |
| Homepage | desktop, scaled desktop, compact, mobile, promo, hero, categories | Pass |
| Header navigation | desktop containment, compact opening, hidden desktop submenus in compact nav | Pass |
| `/virivky/` | responsive shell, catalog desktop geometry, configurator source checks | Pass |
| `/swimspa/` | responsive shell and shared footer checks | Pass |
| `/product/timberwolf/` | responsive shell, product detail desktop geometry, configurator source checks | Pass |
| `/kontakt/` | responsive shell, contact desktop geometry, map source checks | Pass |
| Shared info/support/reference pages | footer and layout smoke coverage | Pass |

## Automated Evidence

Passed:
- `npm run css:build`
- `npm run figma:audit`
- `npm run local:safety`
- `npm run visual:smoke`
- `npm run content:parity`
- `npm run product:smoke`
- `npm run link:smoke`
- `npm run form:smoke`
- `npm run search:smoke`
- `npm run redirect:smoke`

Note:
- The aggregate `npm run qa:local` exceeded the 10 minute command timeout in this terminal run, so each gate was executed individually. No individual gate failed.

## Remaining Human Sign-Off

- Owner/client visual approval is still required before production release.
- If new real-device screenshots show another unrepresented Figma breakpoint, Phase 5 rules say to preserve usability and visual intent rather than forcing an unsafe Figma-only desktop layout.
