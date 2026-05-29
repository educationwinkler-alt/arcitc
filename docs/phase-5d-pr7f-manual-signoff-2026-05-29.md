# Phase 5D / PR7F Composition Sign-Off

Date: 2026-05-29
Scope: support/download desktop composition and product-series navigation parity.

## Result

PR7F repository scope is closed.

The remaining master-plan gates are external release gates: Visao/Jucra plugin activation, owner/client visual/content acceptance, staging deployment, production deployment, and post-release smoke.

## Automated Guards Added

- Product series navigation is now checked for non-overlapping single-line labels at `1920`, `1600`, `1366`, and `1097` CSS px on `/virivky/` and `/swimspa/`.
- Support FAQ composition is checked so the main column, FAQ cards, and right help card remain inside the shared container at the same viewport widths.
- Download rows are checked so the text body and CTA keep a real gap and the CTA stays attached inside the row padding.

## Implementation Notes

- `.f-series-nav` no longer relies on hardcoded `a:nth-child(...)` widths.
- Product series links use intrinsic flex sizing with bounded gaps and reset inherited negative link margins at compact laptop widths.
- `/podpora/` uses a fluid `1024-1399px` two-column composition so the right help card no longer exits the viewport.
- `/podpora/` and `/ke-stazeni/` download cards explicitly restore the CTA to the third grid column, preventing overlap with metadata text.

## Screenshot Evidence

Screenshot pack:
- `docs/screenshots/phase-7f-series-nav-hot-tubs-1920-2026-05-29.png`
- `docs/screenshots/phase-7f-series-nav-hot-tubs-1600-2026-05-29.png`
- `docs/screenshots/phase-7f-series-nav-hot-tubs-1366-2026-05-29.png`
- `docs/screenshots/phase-7f-series-nav-hot-tubs-1097-2026-05-29.png`
- `docs/screenshots/phase-7f-series-nav-swimspa-1920-2026-05-29.png`
- `docs/screenshots/phase-7f-series-nav-swimspa-1600-2026-05-29.png`
- `docs/screenshots/phase-7f-series-nav-swimspa-1366-2026-05-29.png`
- `docs/screenshots/phase-7f-series-nav-swimspa-1097-2026-05-29.png`
- `docs/screenshots/phase-7f-support-layout-1920-2026-05-29.png`
- `docs/screenshots/phase-7f-support-layout-1600-2026-05-29.png`
- `docs/screenshots/phase-7f-support-layout-1366-2026-05-29.png`
- `docs/screenshots/phase-7f-support-layout-1097-2026-05-29.png`
- `docs/screenshots/phase-7f-downloads-layout-1920-2026-05-29.png`
- `docs/screenshots/phase-7f-downloads-layout-1600-2026-05-29.png`
- `docs/screenshots/phase-7f-downloads-layout-1366-2026-05-29.png`
- `docs/screenshots/phase-7f-downloads-layout-1097-2026-05-29.png`

## Verification

- `npm run figma:audit` -> pass.
- `npm run qa:local` -> pass.
