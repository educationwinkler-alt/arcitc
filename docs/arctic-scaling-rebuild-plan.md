# Arctic Scaling Recovery Plan (Figma 1:1)

Date: 2026-05-25  
Scope: desktop scaling and shell geometry across homepage, category, product detail, contact  
Status: in progress (previous "completed" state was invalid after clean rebuild)

## Canonical Rule
- Figma is the only visual and geometry source of truth.
- Baspa is technical foundation only.
- A clean CSS rebuild must produce the same geometry as the audited local state.

## Current Baseline (after `npm run css:build`)
- `npm run figma:audit` currently fails on these points:
- `desktop.heroCaption.y: expected 280 +/- 3, got 268`
- `scaledDesktop:1903.heroSection.height: expected 787.9609375 +/- 4, got 795`
- `responsive:1903:/.headerContainer.x: expected 257.69791666666674 +/- 6, got 251.5`
- `catalog.customSeries.width: expected 1400 +/- 4, got 1360`
- `timberwolf.configurator.width: expected 1400 +/- 3, got 1360`

## Root Causes To Fix
1. Breakpoint mismatch between navigation markup and responsive audit model.
: `data-off-breakpoint` is still `1400` in templates while compact behavior is expected only below `1280`.
2. Desktop shell scaling is not active for `1280..1919`.
: major geometry overrides are locked behind `@media (min-width: 1400px)`.
3. Header container uses fixed desktop width in that same range.
: it stays at `1400px` instead of `1400 * s`.
4. Category/product detail blocks subtract side gutters more than once.
: nested `min(1400px, calc(100% - 40px))` produces `1360px` inner widths.
5. Homepage hero/caption geometry is partly fixed-value desktop, not fully scaled.

## Target Scaling Model
- For desktop widths `1280 <= vw <= 1919`, use `s = vw / 1920`.
- Header shell width: `w = min(1400 * s, vw - 40)`.
- Header shell x-position: `x = (vw - w) / 2`.
- Header shell y-position: `y = 18`.
- Header shell height: `h = 105 * s`.
- Homepage hero height: `h = 795 * s`.
- Compact header/menu applies only for `< 1280`.

## Execution Plan

### Phase A: Breakpoint normalization
- Update `data-off-breakpoint` from `1400` to `1280`:
- `wp-content/themes/arctic/templates/navigation.php`
- `wp-content/themes/arctic/templates/navigation/trigger.php`
- Align compact CSS media queries with the same breakpoint where required.

### Phase B: Header and hero scaling
- Move desktop-shell and homepage scaling rules from `@media (min-width: 1400px)` to a `1280..1919` scaling window where needed.
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
- Keep mobile/compact behavior unchanged at `1024` and `390`.

## Verification Gates (must all pass in one run)
1. `npm run css:build`
2. `npm run figma:audit`
3. `npm run visual:smoke`

## Definition Of Done
- No Figma geometry failures in desktop matrix (`1903, 1536, 1456, 1440, 1366, 1280`) and compact checks (`1024, 390`).
- Header, homepage hero, category layout, and product detail configurator all match expected Figma coordinates within audit tolerances.
- Fresh rebuild is deterministic: same results before and after restart/cache bust.
