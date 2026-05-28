# Content / Language / Function Audit

Date: 2026-05-28  
Scope: Local runtime (`localhost:8090`) + template/CSS/JS audit for reported issues (spacing, language/diacritics, non-functional controls).

## Executive Summary

Audit confirms that most current problems are not random browser glitches. They come from three systematic sources:

1. Static Figma-shell geometry in CSS (`fixed height` / `fixed min-height` / `fixed padding-top`) that creates large empty vertical gaps.
2. Figma-like UI controls rendered as non-interactive markup (plus icons, chips, closed groups) with no JS handler.
3. ASCII fallback/default copy in settings/templates (missing Czech diacritics) and placeholder content contracts (reused image assets, anchor mismatches).

## Findings

### P0 - Functionality

1. Support FAQ plus/minus cards are visual-only, not interactive.
- Evidence: `wp-content/themes/arctic/template-support.php:131-143` renders `<article class="f-support-faq-card">` blocks; only first card gets paragraph, no toggle behavior.
- Evidence: no handler for `f-support-faq-card` in `wp-content/themes/arctic/dist/js/theme.js`.
- Impact: Plus icons imply accordion behavior but clicks do nothing.

2. Downloads closed groups with `+` are static shells.
- Evidence: `wp-content/themes/arctic/modules/downloads/templates/listing.php:79-89` renders `f-download-group--closed` with `+` only.
- Evidence: no JS handler for `f-download-group` in `wp-content/themes/arctic/dist/js/theme.js`.
- Impact: User expects expand/collapse; UI does not respond.

3. Internal anchor links point to missing targets.
- Evidence: `/podpora/#servis` is generated in:
  - `wp-content/themes/arctic/template-features.php:36`
  - `wp-content/themes/arctic/template-feature-detail.php:33`
  - `wp-content/themes/arctic/templates/footer.php:38`
  - `wp-content/themes/arctic/tools/seed-pilot-content.php:1371`
- Evidence: Support page target is `#servisni-formular` (`wp-content/themes/arctic/template-support.php:182`), no `id="servis"`.
- Evidence: `wp-content/themes/arctic/template-showroom.php:72` links to `/kontakt/#formular`, but no `id="formular"` exists in contact templates.
- Impact: Dead/partial navigation, perceived as broken functionality.

### P1 - Layout / Geometry

4. Product series nav overlap (`Vlastní konfiguraceShowroom`) is caused by hardcoded item widths.
- Evidence: `wp-content/themes/arctic/src/less/_components.less:8781-8794` sets fixed widths for `a:nth-child(1..4)`.
- Evidence: real labels come from `wp-content/themes/arctic/templates/section/product-series-nav.php:9-12`.
- Impact: text collision/overlap in real viewport combinations.

5. Warranty page vertical spacing is over-constrained by fixed desktop geometry.
- Evidence:
  - `wp-content/themes/arctic/src/less/_components.less:387-389` (`.f-section--warranty-table { height: 525px; }`)
  - `wp-content/themes/arctic/src/less/_components.less:396` (`padding-top: 217px`)
  - `wp-content/themes/arctic/src/less/_components.less:423-424` (additional large paragraph top margin)
- Impact: large empty gap between intro and table in local render.

6. Maintenance page blank/oversized vertical gaps are from extreme fixed min-heights.
- Evidence:
  - `wp-content/themes/arctic/src/less/_components.less:438-440` (`.f-main--maintenance .f-figma-article { min-height: 2384px; }`)
  - `wp-content/themes/arctic/src/less/_components.less:490-492` (`section:nth-of-type(1) { min-height: 1651px; }`)
- Impact: long blank zones and inconsistent reading flow.

### P1 - Content / Language

7. Missing diacritics in defaults on support/downloads/showroom/configurator.
- Evidence:
  - `wp-content/themes/arctic/template-support.php:11-13,15-20,22`
  - `wp-content/themes/arctic/modules/supports/inc/admin.php:43-50`
  - `wp-content/themes/arctic/modules/downloads/inc/admin.php:41-45,61-73`
  - `wp-content/themes/arctic/modules/downloads/templates/listing.php:17-23`
  - `wp-content/themes/arctic/inc/customize/section/sections.php:109-117`
  - `wp-content/themes/arctic/template-showroom.php:13-18`
  - `wp-content/themes/arctic/template-about.php:3,8-25`
- Impact: inconsistent Czech quality, lower trust/brand polish.

8. Services cards reuse one identical image for all items.
- Evidence: `wp-content/themes/arctic/template-services.php:6` uses one `$service_image` and reuses it for every card (`:44`).
- Impact: repetitive visuals, perceived as placeholder content.

9. Reference archive fills missing cards by cloning existing entries.
- Evidence: `wp-content/themes/arctic/template-references.php:41-43`.
- Impact: repeated cards when source count is low; can look like data duplication bug.

## Root-Cause Pattern

The project currently has mixed layers:
- Real editable WP content (good),
- Figma-specific static templates (quick parity),
- Legacy fixed-pixel CSS geometry.

Where these layers overlap, users get:
- controls that look interactive but are static,
- visual parity at one target frame but poor behavior in real viewport ranges,
- copy quality drift due ASCII defaults.

## Recommended Fix Wave (Plan Input)

1. PR7A - Language/default-copy normalization
- Normalize Czech defaults in support/downloads/customizer/showroom/about.
- Add safe migration for existing option values that still equal old ASCII defaults.

2. PR7B - Interaction hardening
- Convert support FAQ and downloads groups to real accordions/details.
- Make chips either functional filters or visibly non-interactive labels (no fake affordance).
- Repair broken anchors (`#servis` -> `#servisni-formular`, `#formular` target strategy).

3. PR7C - Geometry de-hardcoding
- Remove page-critical fixed heights/min-heights/padding-top from warranty/maintenance/support desktop overrides.
- Keep Figma intent via spacing tokens, not hard frame heights.

4. PR7D - Content parity cleanup
- Replace repeated services image with per-card assets.
- Stop mandatory reference cloning for missing items (or make fallback explicit and labeled).

5. PR7E - QA hardening + manual sign-off reopen
- Add automated checks for:
  - dead internal anchors,
  - non-interactive affordances with plus/chip semantics,
  - diacritic regressions in configured defaults,
  - large blank-gap guardrails on target pages.
- Regenerate screenshot evidence for `/zaruka/`, `/kolik-stoji-udrzba/`, `/podpora/`, `/sluzby/`, category series nav.

