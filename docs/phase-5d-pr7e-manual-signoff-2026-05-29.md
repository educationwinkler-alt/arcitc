# Phase 5D / PR7E Manual Sign-Off Reopen Closure

Date: 2026-05-29
Scope: PR7E QA hardening for content, language, anchors, interaction affordances, and large blank-gap regressions.

## Result

Technical reopen for PR7E is closed.

PR7E does not close the separate PR7F layout-composition scope. Product-series nav parity, support desktop composition, and long download-row alignment remain tracked by PR7F.

## Automated Guards Added

- `tools/link-smoke.js` now validates internal fragment anchors, not only page reachability.
- `tools/visual-smoke.js` now checks visible page text for legacy ASCII Czech fallback copy.
- `tools/figma-visual-audit.js` now validates support/download filters and accordion affordances as real interactive controls with ARIA state changes.
- `tools/figma-visual-audit.js` now checks major section-to-section gaps on affected PR7 pages at desktop and compact-laptop widths.
- Runtime menu normalization now repairs stale DB menu anchors for retired sale and swimspa series targets.

## Affected Pages

- `/`
- `/virivky/`
- `/swimspa/`
- `/vlastnosti/`
- `/podpora/`
- `/ke-stazeni/`
- `/zaruka/`
- `/kolik-stoji-udrzba/`
- `/sluzby/`
- `/reference/`

## Screenshot Evidence

- `docs/screenshots/phase-7e-support-interactions-desktop-1920-2026-05-29.png`
- `docs/screenshots/phase-7e-downloads-interactions-desktop-1920-2026-05-29.png`
- `docs/screenshots/phase-7e-warranty-gap-desktop-1920-2026-05-29.png`
- `docs/screenshots/phase-7e-maintenance-gap-desktop-1920-2026-05-29.png`
- `docs/screenshots/phase-7e-services-fallback-desktop-1920-2026-05-29.png`
- `docs/screenshots/phase-7e-references-fallback-desktop-1920-2026-05-29.png`

## Verification

- `npm run link:smoke` -> pass, including 449 anchor targets.
- `npm run visual:smoke` -> pass.
- `npm run figma:audit` -> pass.
- `npm run qa:local` -> pass.

