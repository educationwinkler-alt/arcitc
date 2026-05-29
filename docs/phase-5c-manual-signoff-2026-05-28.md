# Phase 5C Manual Sign-Off Record

Date: 2026-05-28
Scope: repository-level technical manual sign-off after PR6 (`QA hardening + Phase 5C sign-off`) and PR6C reopen fixes.

Reopen note (2026-05-28, later audit pass):
- A new cross-page content/language/function audit (`docs/content-language-function-audit-2026-05-28.md`) identified additional unresolved issues (fake-interactive support/download controls, dead anchors, ASCII fallback copy, and figma-page geometry gaps).
- Phase 5C remains valid as geometry evidence, but end-state acceptance is reopened until PR7A-PR7F are closed.

Update 2026-05-29 (PR7E closure):
- Content/language/function QA reopen is now closed for the PR7E scope.
- Automated coverage was extended for internal fragment anchors, support/download interaction affordances, visible legacy ASCII Czech fallback copy, and large section gap regressions.
- Manual evidence and screenshot pack are recorded in `docs/phase-5d-pr7e-manual-signoff-2026-05-29.md`.
- PR7F remains open as a separate composition/parity scope.

Update 2026-05-29 (PR7F closure):
- Support/download desktop composition and product-series navigation parity are now closed for the repository scope.
- Manual evidence and screenshot pack are recorded in `docs/phase-5d-pr7f-manual-signoff-2026-05-29.md`.

Update 2026-05-28 (PR6C reopen closure):
- Showroom panel location pin no longer uses bitmap background (`hp-pin-showroom.png`); icon is now pure CSS glyph over circular chip.
- Footer quick-hours chip is forced to one line (no orphan trailing `h` line break).
- Homepage/category/product reference cards use explicit reference overlay/link contracts (lighter gradient, fixed white link color, no hover darkening scale drift).
- Manual screenshot pack was regenerated via `VISUAL_SMOKE_WRITE_SCREENSHOTS=1 npm run visual:smoke`.

## Sign-Off Method

- Source of truth: Figma visual/UX layer, legacy Arctic content layer, Baspa admin/function layer.
- Inputs used:
  - `docs/screenshots/*-desktop-playwright.png`
  - `docs/screenshots/*-mobile-playwright.png`
  - targeted Phase 5 screenshots from 2026-05-27 (`phase-5-*`, `phase-5a-*`)
  - local runtime spot checks in Chrome/Playwright
  - `npm run figma:audit` and `npm run qa:local`
- Rule: this record closes technical repository sign-off. Owner/client acceptance is a separate release gate.

## Page-By-Page Checklist

| Area | Templates / routes | Manual result | Evidence |
| --- | --- | --- | --- |
| Homepage + hero/promo/categories | `/` | Pass | `home-desktop-playwright.png`, `home-mobile-playwright.png`, `phase-5-homepage-figma-parity-1920-2026-05-27.png`, `phase-5a-homepage-1097x617-2026-05-27.png` |
| Category pages | `/virivky/`, `/swimspa/`, `/catalog/dalsi-sortiment/` | Pass | `category-virivky-desktop-playwright.png`, `category-swimspa-desktop-playwright.png`, `category-dalsi-sortiment-desktop-playwright.png` |
| Product detail | `/product/timberwolf/` + spot checks `/product/husky/`, `/product/athabascan/`, `/product/covana/` | Pass | `product-timberwolf-desktop-playwright.png`, `product-husky-desktop-playwright.png`, `product-athabascan-desktop-playwright.png`, `product-covana-desktop-playwright.png` |
| Showroom | `/showroom/` | Pass | `showroom-desktop-playwright.png`, `showroom-mobile-playwright.png`, `phase-5-homepage-showroom-1920-2026-05-27.png` |
| References | `/reference/` | Pass | `references-desktop-playwright.png`, `references-mobile-playwright.png`, `phase-5-reference-page-1920-2026-05-27.png` |
| Contact + map/location | `/kontakt/` | Pass | `contact-desktop-playwright.png`, `contact-mobile-playwright.png`, `phase-5-contact-page-1920-2026-05-27.png` |
| Support/downloads | `/podpora/`, `/ke-stazeni/` | Pass | `support-desktop-playwright.png`, `downloads-desktop-playwright.png`, mobile equivalents |
| Service | `/servis/` | Pass | `service-request-desktop-playwright.png`, `service-request-mobile-playwright.png` |
| About | `/o-nas/` | Pass | `about-desktop-playwright.png`, `about-mobile-playwright.png` |
| Info pages | `/vlastnosti/`, `/vlastnosti/izolace-virivky/`, `/sluzby/`, `/certifikaty/`, `/zaruka/`, `/kolik-stoji-udrzba/` | Pass | corresponding desktop/mobile screenshots from `docs/screenshots/` |
| Mobile menu state | `GM - HP menu` behavior on homepage | Pass | `home-mobile-playwright.png` + `visual:smoke` checks for menu/search placement |

## Interaction / Scope Decisions

- `DALSI INFORMACE` frame remains consciously out of standalone scope:
  - `/dalsi-informace/` is an intentional `301` to `/#order-progress` (PR0 decision lock).
- `popup` scope is satisfied by product benefit popup interaction:
  - separate contact modal is not required in current implementation scope.

## QA Hardening Outcome

- Added explicit automated checks for previously uncovered Phase 5B mismatches:
  - reference archive radius
  - product reference radius
  - contact top button radius
  - showroom CTA button radius
  - critical image natural-size upscale limits (`<= 1.25x`)
- Final gate evidence for this sign-off:
  - `npm run figma:audit` -> pass
  - `npm run qa:local` -> pass

## Final Technical Sign-Off Status

- Phase 5B + 5C repository technical sign-off: **Conditionally passed**; PR7E and PR7F follow-up repository scopes are closed as of 2026-05-29.
- Remaining release requirement outside repository code: owner/client visual approval before production rollout.

