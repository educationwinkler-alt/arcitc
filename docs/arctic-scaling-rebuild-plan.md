# Arctic Scaling Recovery Plan (Figma 1:1)

Date: 2026-05-25  
Scope: desktop scaling and shell geometry across homepage, category, product detail, contact  
Status: superseded by the Phase 5 responsive/zoom resilience pass on 2026-05-26

## Canonical Rule
- Figma is the only visual and geometry source of truth.
- Baspa is technical foundation only.
- A clean CSS rebuild must produce the same geometry as the audited local state.
- When Figma does not define a real responsive/zoom state, preserve usability and visual intent instead of forcing unsafe desktop geometry.

## Current Baseline (after `npm run css:build`)
- `npm run figma:audit` passes after Phase 5 updates.
- The previous 1280 desktop breakpoint model was rejected for real laptop/120% zoom behavior because it allowed the desktop navigation to collapse into the header panel.

## Root Causes To Fix
1. Breakpoint mismatch between Figma-only desktop assumptions and real browser zoom behavior.
: The safe compact navigation threshold is now `< 1400`, not `< 1280`.
2. Desktop shell scaling is not active for `1280..1919`.
: major geometry overrides are locked behind `@media (min-width: 1400px)`.
3. Header container uses fixed desktop width in that same range.
: it stays at `1400px` instead of `1400 * s`.
4. Category/product detail blocks subtract side gutters more than once.
: nested `min(1400px, calc(100% - 40px))` produces `1360px` inner widths.
5. Homepage hero/caption geometry is partly fixed-value desktop, not fully scaled.

## Target Scaling Model
- For full desktop widths `1400 <= vw <= 1919`, use `s = vw / 1920`.
- Header shell width: `w = min(1400 * s, vw - 40)`.
- Header shell x-position: `x = (vw - w) / 2`.
- Header shell y-position: `y = 18`.
- Header shell height: `h = 105 * s`.
- Homepage hero height: `h = 795 * s`.
- Compact header/menu applies for `< 1400`.
- The homepage promo sticker must stay inside the viewport at all desktop widths; preserve the Figma x-position until it would clip, then clamp it with a 20px safe inset.

## Execution Plan

### Phase A: Breakpoint normalization
- Keep `data-off-breakpoint` at `1400`:
- `wp-content/themes/arctic/templates/navigation.php`
- `wp-content/themes/arctic/templates/navigation/trigger.php`
- Align compact CSS media queries with the same breakpoint where required.

### Phase B: Header and hero scaling
- Keep desktop-shell and homepage scaling rules in the `1400..1919` window for desktop header geometry.
- Ensure header container in that window uses scaled width/height and remains centered.
- Fix hero/caption vertical placement to match Figma matrix checks.

### Phase C: Inner-template width fixes
- Remove double-gutter width loss in category and product detail sections.
- Keep only one authoritative width constraint per section container chain.
- Confirm exact `1400px` width at `1920px` viewport for:
- catalog product series block
- product detail configurator block

### Phase D: Regression cleanup
- Remove stale geometry overrides that conflict with Phase A-C.
- Keep mobile/compact behavior stable at `1366`, `1280`, `1024`, `768`, `430`, and `390`.

## Verification Gates (must all pass in one run)
1. `npm run css:build`
2. `npm run figma:audit`
3. `npm run visual:smoke`

## Definition Of Done
- No Figma geometry failures in desktop matrix (`1903, 1600, 1536, 1456, 1440`) and compact checks (`1366, 1280, 1024, 768, 430, 390`).
- Header, homepage hero, category layout, and product detail configurator all match expected Figma coordinates within audit tolerances.
- Fresh rebuild is deterministic: same results before and after restart/cache bust.
