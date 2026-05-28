# Arctic Spas - Master Finalization Plan

Date: 2026-05-26  
Workspace: `arctic-spas-2/`  
Source plans:
- `docs/end-to-end-implementation-plan.md`
- `docs/web-completion-plan.md`
- `docs/arctic-scaling-rebuild-plan.md`

## Working Figma Context (sensitive)

Figma token (provided by owner):
- `FIGMA_TOKEN` (store locally, do not commit raw value)

Figma files:
- Arctic Spas wireframe
  - URL: `https://www.figma.com/design/puPBNFpuaXpRZR2TINaDvm/Arctic-spas.cz-wireframe?t=BdCp3f8qo4vMl5Ft-1`
  - File key: `puPBNFpuaXpRZR2TINaDvm`
- Arctic Spas grafika
  - URL: `https://www.figma.com/design/xeOew3dFjDVfjXZrJ09emM/Arctic-spas.cz-grafika?t=BdCp3f8qo4vMl5Ft-1`
  - File key: `xeOew3dFjDVfjXZrJ09emM`

Operational note:
- If token is rotated, update this section first so work can continue without re-sharing credentials.
- Expected local storage:
  - `.env.local` -> `FIGMA_TOKEN=...`

## Source-of-truth hierarchy

This project is a redesign and functional rebuild with three separate source roles. Do not mix these roles during implementation or sign-off:

1. Visual and UX source: Figma
   - `Arctic Spas wireframe` defines page structure, section order, layout intent, and responsive UX.
   - `Arctic Spas grafika` defines final visual direction, spacing, colors, typography, imagery treatment, and component look.
   - Figma is the visual authority. Baspa.cz must not be used as a visual fallback.
2. Functional and WordPress admin source: `../baspa.cz/`
   - Baspa.cz is the functional/admin reference because the client is used to that editing model.
   - Arctic Spas admin should be similar or better for relevant sections, not reduced unless explicitly rejected.
   - Baspa.cz admin features must be audited, classified, and either ported, adapted, replaced, or documented as not applicable.
3. Content source: `../Arctic-spas/`
   - The old Arctic Spas PHP/Windows web is the content source for the redesign.
   - Product descriptions, parameters, references, downloads, old URLs, legal pages, and other business content should come from the old Arctic source or owner-provided Arctic materials.
   - Content must not be invented from Figma and must not be copied from Baspa.cz unless it is generic functional/admin copy intentionally reused.

Conflict rule:
- If sources disagree, use Figma for visual/layout decisions, old Arctic for content/business meaning, and Baspa.cz for comparable WordPress functionality/admin workflow.

## 1) Status from the 3 existing plans

### A. `end-to-end-implementation-plan.md`
Status: mostly done (technical baseline complete)

Done:
- Local WP runtime and safety guard are active.
- Content model + import tooling + redirect map are present.
- Old Arctic content extraction/import path is present (`../Arctic-spas/` -> seed/import/redirect tooling).
- QA command chain exists and is operational.
- Current full gate check passes:
  - `npm run qa:local` passed on 2026-05-26.

Still open:
- Final production rollout checklist is not closed in this repo (staging/prod release procedure, production smoke after deploy).
- Manual business sign-off on all pages against Figma is still needed.
- Manual content parity sign-off against the old Arctic Spas PHP web is still needed.

### B. `web-completion-plan.md`
Status: partially done (P0 quality gate improved, P1 data model done, visual sign-off still open)

Done:
- Core Figma geometry gate is passing.
- Homepage hero promo x-position mismatch was fixed and validated by `figma:audit`.
- CSS flow is stable enough to run full QA repeatedly.
- Product configuration data model has been moved to structured `product_configuration_items` with legacy fallback.

Still open:
- Full frame-by-frame manual Figma validation across all required templates is not closed as a signed checklist.
- Jucra 3D builder workflow is code-ready, but final runtime activation still waits for the Visao plugin.
- Admin parity with Baspa.cz is not yet audited or closed as a release gate.

### C. `arctic-scaling-rebuild-plan.md`
Status: gate-pass achieved, but visual sign-off still open (reopened 2026-05-26)

Done evidence:
- Previous breakpoint normalization existed, but Phase 5 reopened it because laptop/120% zoom states showed the desktop shell breaking before `1280`.
- `npm run figma:audit` passes after clean build.
- `npm run visual:smoke` and full `npm run qa:local` pass on 2026-05-26.

Open concern:
- Despite gate pass, manual review reports visible mismatch against Figma on real page rendering.
- Scaling and shell geometry must be treated as still in-progress until full frame-by-frame visual sign-off is closed.

## 2) Current blocker list (what is not finished yet)

1. Jucra 3D configurator operational activation is still blocked by the missing Visao plugin.
2. Final functional viewer verification is pending after plugin install/activation.
3. Manual final sign-off sheet for all page/frame combinations is still pending.
4. Admin parity with Baspa.cz is not yet audited, implemented, or explicitly waived per section.
5. Content parity against the old Arctic Spas PHP site is not yet signed off by checklist.
6. Text encoding regression exists in configurator section source strings (mojibake), must be cleaned.
7. Manual review still reports visual mismatch against Figma despite automated gate pass.
8. Desktop product mega menu hover is not closed until the user can move from nav trigger into the submenu without it disappearing.
9. Windows 1920x1080 with 175% system display scaling must be treated as a real customer viewport, not as user error.
10. Product detail layouts for both hot tubs and swimspas/pools need explicit responsive parity checks.
11. Legacy Arctic product/category imagery needs a source-quality audit so cards and hero images do not use low-resolution thumbnails where larger originals exist.
12. Footer and homepage section backgrounds expose white side gutters at scaled/zoomed states; this is a real layout bug, not a cache issue.
13. The site needs a unified full-bleed/background/container width system so sections do not drift apart at zoom-out, zoom-in, or Windows display scaling.

## 3) New complete plan to finish the website

## Phase 0 - Freeze and baseline (P0, 0.5 day)
1. Freeze baseline branch and tag current green gate commit.
2. Keep `qa:local` mandatory before each merge.
3. Export fresh screenshot set with `VISUAL_SMOKE_WRITE_SCREENSHOTS=1` for sign-off package.

Exit criteria:
- Clean git state, reproducible green QA, screenshot package dated.

## Phase 1 - Jucra configurator integration (P0, 1-2 days)
Goal: make section "Nakonfigurujte si vlastni virivku" functional with 3D builder.

Current status (2026-05-26):
- Code integration is done and pushed in commit `06d08ae`.
- Runtime activation is blocked until the `Visao 3D Viewer` plugin is installed and activated in WordPress.
- Final functional viewer verification cannot be closed without that plugin.
- Continue with Phase 2 and Phase 3 first; return to final Jucra activation as soon as the plugin is available.

Implementation:
1. Add theme option (or constants) for:
   - `arctic_jucra_enabled` (bool)
   - `arctic_jucra_default_model`
   - `arctic_jucra_pricing_relative_url`
2. Add product-level meta field `jucra_model_name` (example: `Summit`, `Summit XL`, `Tundra`).
3. Replace configurator CTA target logic:
   - If Jucra enabled and plugin shortcode exists, render:
     - `[visao_viewer model_name="..."]`
   - Else fallback to existing CTA (safe degraded mode).
4. On product detail, pass model name from post meta to shortcode.
5. Keep "Get Pricing Now" URL relative (per Jucra notes), not full external domain.
6. Add smoke validation:
   - detect rendered Jucra container/shortcode output when enabled
   - verify fallback mode when plugin is disabled
   - verify no external call leakage in local mode unless explicitly allowed

Operational steps (outside git code):
1. In Jucra portal install `Visao 3D Viewer` (article references v1.26).
2. Install and activate plugin in WP.
3. Set plugin settings under `Settings > Visao 3D builder Settings`.
4. Configure model name mapping for Arctic products.

Exit criteria:
- Homepage configurator section opens/embeds functional 3D viewer flow.
- Product detail configurator can render model-specific viewer.
- Fallback CTA still works when plugin is unavailable.

Exit criteria status (2026-05-26):
- Fallback CTA works.
- Inline viewer flow is code-ready, but not operationally verified.
- Product detail model-specific viewer is code-ready, but not operationally verified.
- Remaining effort after plugin availability: install/activate plugin, set plugin settings, fill `jucra_model_name` values, run final smoke check.

## Phase 1A - Homepage promo badge scope (P0, 0.5 day)
Goal: keep sale/promo badge compliant with content rules and page scope.

Current status (2026-05-26):
- Done and pushed in commit `06d08ae`.
- Promo badge is renamed and guarded to homepage-only rendering.
- Regression coverage is in `tools/visual-smoke.js`.

Implementation:
1. Change homepage promo copy from "Vyprodej skladovych virivek" to "Akcni nabidka skladovych virivek" (or exact approved CZ final text).
2. Ensure the promo component is rendered only on homepage (`is_front_page()` or equivalent template-level guard).
3. Explicitly prevent rendering on category, product detail, support, contact, showroom, references, and info pages.
4. Add regression check:
   - homepage must contain the approved promo text
   - non-home templates must not contain the promo text/component

Exit criteria:
- Promo badge is visible on homepage only.
- Promo badge text is "Akcni nabidka" (approved final variant).
- Zero occurrences of this component on non-home pages.

## Phase 2 - Data model completion for product configurations (P1, 1-2 days)
Current status (2026-05-26):
- Done in the Phase 2 implementation commit.
- New structured primary meta key: `product_configuration_items`.
- Legacy repeated meta key `product_configurations` remains as fallback for backwards compatibility.
- Admin editing moved from weak `fieldset_text` to a native structured repeater metabox with active/sort order/image support.
- Seed/import writes the new structured meta while keeping legacy meta populated during transition.
- Regression coverage added to product content smoke tests.

1. Replace `product_configurations` flat text fieldset with structured repeater/group:
   - `active`
   - `sort_order`
   - `name`
   - `price`/`price_text`
   - `seats`, `jets`, `pumps`, `dimensions`, optional notes/image
2. Add migration adapter for legacy values.
3. Update templates to read new structure first, legacy fallback second.
4. Update seed/import scripts and regression tests.

Exit criteria:
- Admin can edit configurations safely without formatting hacks.
- Frontend output stable and fully backwards compatible.

Exit criteria status (2026-05-26):
- Completed in code.
- Product frontend smoke passed against local pages.
- Full `qa:local` should remain the release gate before Phase 3 sign-off.

## Phase 3 - Admin parity with Baspa.cz (P0/P1, 1-2 days)
Goal: make the WordPress administration comparable to Baspa.cz for all Arctic-relevant content sections.

Current status (2026-05-26):
- Completed for the Phase 3 code scope.
- Admin parity matrix: `docs/admin-parity-matrix-2026-05-26.md`.
- Arctic-specific editable controls were added for homepage promo, configurator CTA, showroom card, support page, and downloads page/listing copy.
- Baspa-compatible admin modules were audited and either retained/adapted or explicitly marked not applicable/deferred in the matrix.
- Remaining validation is client/admin walkthrough after Phase 4 content parity and Phase 5 visual QA.

Reference rule:
- Baspa.cz is the admin and functional pattern.
- Arctic Spas sections must follow the Arctic content model and Figma structure, but should not lose expected admin editability.

Audit:
1. Build an admin parity matrix:
   - Baspa module/feature
   - Arctic equivalent
   - status: ported / adapted / missing / not applicable / Arctic-specific new need
   - implementation note
   - sign-off owner
2. Compare at least these areas:
   - Products and product categories
   - Product configurations and Jucra model fields
   - Contacts, showroom/contact sections, and form copy
   - FAQ/support content
   - References/realizations
   - Downloads/PDF files
   - Articles/pages if the client expects adding editorial content
   - Header/footer/menu editable elements
   - Homepage promo/configurator blocks
   - Forms and local-safe external integrations
3. For every Baspa admin function:
   - keep if it is relevant for Arctic
   - adapt if Arctic needs the same workflow with different fields/copy
   - reject only with an explicit note explaining why it does not apply
4. Add Arctic-specific admin controls where Figma or content model introduces new editable sections.

Exit criteria:
- Client can edit all relevant Arctic content without touching code.
- Admin workflow is similar enough to Baspa.cz that a Baspa user is not surprised by missing core capabilities.
- Any missing Baspa feature is explicitly marked "not applicable" or "deferred" with reason.
- Admin parity matrix is committed in docs and linked from this master plan.

## Phase 4 - Content parity with old Arctic Spas site (P0/P1, 1 day)
Goal: verify the redesign uses the old Arctic Spas site as the content source, not Figma placeholders or Baspa content.

Current status (2026-05-26):
- Completed for the Phase 4 technical/content audit scope.
- Content parity audit: `docs/content-parity-audit-2026-05-26.md`.
- Automated gate added: `npm run content:parity`, now included in `npm run qa:local`.
- Old `diskuze.php` redirect was corrected from `/podpora/` to `/reference/` because the source page contains customer reference/discussion content.
- Remaining validation is owner/client approval of documented exceptions: Lunar, Orion, and one missing old PDF.

Reference rule:
- `../Arctic-spas/` is the content authority.
- Figma controls presentation, not final business copy.
- Baspa.cz controls admin/function patterns, not Arctic product/reference content.

Checklist:
1. Verify product coverage against old Arctic URLs:
   - title
   - description
   - parameters
   - configurations
   - images where available
   - downloads
   - redirects
2. Verify non-product content:
   - references/realizations
   - FAQ/support
   - contact/showroom information
   - legal/GDPR pages
   - PDF downloads
3. Verify migration tooling evidence:
   - extracted legacy product JSON
   - seed/import script coverage
   - redirect map coverage
   - smoke tests for products and old URLs
4. Record exceptions:
   - content intentionally rewritten
   - content missing from old site
   - content awaiting owner approval

Exit criteria:
- Content parity checklist is complete.
- No visible page relies on lorem ipsum, Figma placeholder copy, or Baspa-specific business copy.
- Owner/client has a clear list of content exceptions requiring approval.

## Phase 5 - Full visual sign-off loop (P0/P1, 2-3 days)
Important responsive/zoom rule:
- Figma does not contain every real responsive state.
- Besides the exact Figma desktop/mobile frames, Phase 5 must validate real in-between laptop widths and browser zoom behavior.
- Required resilience viewports include at least 1920, 1903, 1600, 1536, 1456, 1440, 1366, 1280, 1024, 768, 430, and 390 CSS pixels.
- Browser zoom 120% must be treated as an effective narrower CSS viewport; the layout must not overlap, clip key CTAs, or create horizontal overflow.
- Windows display scaling is also in scope. A common customer setup is 1920x1080 with 175% Windows scaling and Chrome at 100%; this must be simulated as an effective compact CSS viewport and must remain usable.
- Browser zoom-out is also in scope. At zoomed-out states, full-width backgrounds, hero sections, cards, and footer must still cover the viewport consistently and must not reveal white side gutters.
- If Figma is silent for a breakpoint, preserve the visual intent and usability: no collapsed header text, no off-screen promo cards, no cropped CTA text, no broken grids.
- Passing at ideal desktop width is not enough. Phase 5 can close only after the real laptop/scaled states behave correctly.

Hard Phase 5 blockers added from real review (2026-05-27):
- Mega menu hover stability: product dropdowns must stay open while moving the cursor from the top nav item into the submenu panel. There must be no hover gap, flicker, or accidental close.
- Homepage hero/category boundary: at 175% Windows scaling/effective laptop viewport, the section after the hero must not look glued to, hidden behind, or visually collapsed into the slider.
- Full-bleed layout consistency: homepage sections, footer background, and large visual bands must fill the available viewport without white side gutters at browser zoom-out or Windows display scaling.
- Container system consistency: page sections must share a deliberate width model. Fixed `max-width` content, section backgrounds, full-bleed wrappers, and footer artwork must not use conflicting widths that create visible seams.
- Footer integrity: footer columns, quick contact card, copyright row, legal link, logo, and mountain background must remain visually aligned and centered without detached white margins.
- Product detail parity: at least one hot tub detail and one swimspa/pool detail must be checked against Figma intent at desktop, compact laptop/scaled viewport, tablet, and mobile.
- Product detail image quality: hero/background images must use the best available legacy Arctic source, not tiny thumbnails stretched into blurred backgrounds.
- Catalog/card image quality: hot tub and swimspa listing cards must use appropriately sized product images. If the old Arctic source contains larger variants, use those instead of the smallest thumbnail.
- Header/search/menu states: Figma menu/search states are required, but must also be practical in real browser interaction, not only visually close in a static screenshot.
- The homepage promo card must remain homepage-only and must not collide with menu overlays, CTAs, slider arrows, or hero content at scaled laptop widths.

1. Run manual Figma QA checklist page-by-page:
   - homepage desktop/mobile
   - header dropdowns
   - header dropdown hover interaction and cursor travel path
   - category pages
   - product detail variants
   - hot tub detail and swimspa/pool detail variants
   - support/downloads
   - contact/showroom
   - references
   - information pages
   - product/category image sharpness against available legacy Arctic originals
   - footer and full-bleed section behavior at zoom-out, 100%, 120%, and Windows 175% scaling equivalent
2. For each page record:
   - pass/fail
   - screenshot pair
   - exact delta note
   - fix commit reference
3. Close only when all high-priority deltas are resolved.

Exit criteria:
- Signed pass sheet for all required pages and breakpoints.

Current Phase 5 status:
- Phase 5 implementation is complete for the repository code/audit/screenshot scope as of 2026-05-27.
- `docs/visual-signoff-phase-5-2026-05-26.md` remains valid only as evidence for responsive/zoom resilience.
- The active frame-by-frame tracker is `docs/phase-5-figma-frame-by-frame-tracker-2026-05-26.md`.
- Header search and desktop product mega menu states from Figma are now implemented and covered by `npm run figma:audit`.
- Additional desktop frame geometry is now covered for Swimspa category, showroom, product popup, info pages, support, references, about, service, contact, and the shared footer.
- Final visual-token parity is now automated in `tools/figma-visual-audit.js`: local Red Hat Display font usage, Figma background/red/dark color tokens, button and card radii, contact CTA styling, reference cards, showroom panel, footer quick contact card, footer map card, and shared page typography.
- Shared repeated modules have been normalized instead of patched page-by-page: contact CTA, recent references, showroom panel, footer quick contact/map, local-map pin styling, and button radii now inherit one Figma-aligned token/component layer.
- Homepage references now use Figma-style realization metadata pills and white underlined card titles over the image gradient instead of category/excerpt copy.
- Phase 5 screenshot evidence was captured at:
  - `docs/screenshots/phase-5-homepage-figma-parity-1920-2026-05-27.png`
  - `docs/screenshots/phase-5-homepage-showroom-1920-2026-05-27.png`
  - `docs/screenshots/phase-5-homepage-references-1920-2026-05-27.png`
  - `docs/screenshots/phase-5-homepage-contact-cta-1920-2026-05-27.png`
  - `docs/screenshots/phase-5-contact-page-1920-2026-05-27.png`
  - `docs/screenshots/phase-5-reference-page-1920-2026-05-27.png`
- Verification passed on 2026-05-27: `npm run css:build`, `npm run figma:audit`, and full `npm run qa:local`.
- Remaining non-code release requirement: owner/client visual acceptance of the screenshot pack and physical/equivalent Windows `1920x1080`, display scaling `175%`, Chrome `100%` screenshot sign-off before production release.

## Phase 5B/5C execution track (PR0-PR6)
Decision lock (PR0, 2026-05-27):
- `/dalsi-informace/` is an intentional navigation-hub route and remains a `301` redirect to `/#order-progress`.
- This frame is treated as a conscious IA deviation, not an open implementation bug.

Execution sequence:
1. PR0 `IA decision + docs sync`
2. PR1 `component contracts refactor`
3. PR2 `reference cards parity`
4. PR3 `button system parity`
5. PR4 `map/location parity`
6. PR5 `image quality pass`
7. PR6 `QA hardening + Phase 5C manual sign-off`

Task board checklist + DoD:
- [x] PR0 IA decision + docs sync
  - Scope: lock `/dalsi-informace/` behavior and align master plan, tracker, and Phase 5B audit.
  - DoD: no document in repo describes `/dalsi-informace/` as unresolved.
- [x] PR1 component contracts refactor
  - Scope: define one shared contract layer for `.f-section--references`, `.f-contact-cta`, `.f-showroom-panel`, `.f-footer--arctic`, `.f-local-map`; remove conflicting duplicate overrides.
  - DoD: follow-up PRs only change values against shared contracts, not by adding another page-specific override layer.
- [x] PR2 reference cards parity
  - Scope: unify reference card radius and shared card shell across homepage, `/reference/`, and `/product/timberwolf/`.
  - DoD: all target reference cards use the same agreed component radius and shell behavior.
- [x] PR3 button system parity
  - Scope: fix `/kontakt/` top buttons and `/showroom/` appointment CTA to pill `50px` via shared button contract.
  - DoD: no target CTA/button falls back to `0px` or `8px` radius.
  - Verification (2026-05-28): local Playwright computed-style check confirms `/kontakt/` top buttons = `50px`, `/showroom/` gallery + appointment CTA = `50px`.
- [x] PR4 map/location parity
  - Scope: set `/kontakt/` map card to `40px` and keep footer map card parity at `30px` without regressions.
  - DoD: contact and footer map modules match agreed radius tokens across desktop/mobile.
  - Verification (2026-05-28): local Playwright computed-style check confirms `/kontakt/` map card = `40px` and footer quick map = `30px` on desktop, scaled-laptop, and mobile viewports.
- [x] PR5 image quality pass
  - Scope: replace visibly upscaled product/reference/hero assets with higher-quality sources where available.
  - DoD: no critical hero/reference/product card remains obviously upscaled from undersized source art.
  - Verification (2026-05-28): local Playwright natural-size check confirms `/virivky/` and `/swimspa/` product cards now render from high-res sources (no upscale), `/product/timberwolf/` hero max upscale is `1.20x` (1920x795 render from 1600x1600 source), and `/reference/` cards render from >=500x333 sources.
- [ ] PR6 QA hardening + Phase 5C sign-off
  - Scope: add missing automated checks (reference archive radius, product reference radius, contact buttons, showroom button, image natural-size checks) and complete manual page-by-page sign-off.
  - DoD: all known Phase 5B mismatches are covered by automated checks and final manual sign-off records.

## Phase 5A - Compact laptop / Windows 175% scaling (P0, 0.5-1 day)
Goal: make `1920x1080` Windows display scaling at `175%` with Chrome at `100%` behave as a first-class release viewport, not an accidental mix of desktop and mobile rules.

Observed issue (2026-05-27):
- Windows `175%` scaling on a `1920x1080` display creates an effective viewport around `1097x617` CSS pixels.
- The current responsive system does not define a coherent scaling layer for `1024-1279px`.
- `--arctic-desktop-scale` starts at `1280px`, while mobile/card rules start below `1024px`.
- The `1024-1279px` band can therefore inherit a mixed layout: oversized hero, visible promo card competing with hero content, and category cards using rules that were not designed as a complete compact-laptop composition.

Current status (2026-05-27):
- Technical implementation is complete for the code/audit scope.
- A dedicated `1024-1279px` compact-laptop scaling layer now defines shared homepage variables for hero height, container width, section gap, and compact scale.
- Homepage hero, promo behavior, and product category cards are connected to this compact-laptop layer.
- The homepage promo card is hidden in this range to prevent collision with hero content and category cards.
- The promo visibility model is now default-hidden and opt-in only for the explicit homepage desktop hero state.
- The `768-1023px` docked-DevTools / narrow-browser band now has a deliberate full-width hero and two-column category layout instead of inheriting the 375px mobile composition.
- Mobile no longer reserves the old promo-height gap under the hero.
- Desktop hero/category boundary now verifies that the inner slider/background height cannot extend under the category cards.
- Footer parity audit now requires the Lukáš Dušek photo avatar, `BASPA s.r.o.` copyright text, no bottom-line wrapping regression, and no eboost credit.
- Automated coverage now checks `904x617`, `1023x617`, `1024x617`, `1097x617`, `1279x720`, and desktop promo boundary behavior at `1280px` and `1366px`.
- Local screenshot evidence was captured at `docs/screenshots/phase-5a-homepage-904x617-2026-05-27.png`, `docs/screenshots/phase-5a-homepage-1097x617-2026-05-27.png`, and `docs/screenshots/phase-5a-footer-element-1920-2026-05-27.png`.
- Remaining validation before Phase 5 final close: owner/client acceptance of physical or equivalent Windows `1920x1080`, display scaling `175%`, Chrome `100%` screenshot sign-off.

Promo rendering rule update:
- The homepage hero promo must be opt-in, not opt-out.
- `.f-hero-promo` should have `display: none` as its default state.
- It may become visible only through an explicit homepage/full-desktop rule, initially `@media (min-width: 1280px)` scoped to `.template--homepage .f-section--slides > .f-hero-promo`.
- Compact laptop, tablet, mobile, DevTools-narrowed, and Windows scaling states must not need their own repeated suppression rules.
- Automated audit should verify that promo is hidden outside the allowed homepage desktop hero state.

Implementation:
1. Add an explicit `1024-1279px` compact laptop scaling layer.
2. Define shared compact variables first, before component-specific fixes:
   - `--arctic-compact-scale`
   - `--arctic-hero-height`
   - `--arctic-container-width`
   - `--arctic-section-gap`
3. Reconnect homepage hero, promo card, product category cards, header spacing, and hero/category boundary to these shared variables.
4. Apply the promo rendering rule update:
   - default hidden
   - explicit homepage/full-desktop opt-in only
   - no repeated breakpoint-by-breakpoint suppression as the primary control
5. Ensure category cards at `1024-1279px` use a deliberate compact layout, not leftover desktop/mobile rules.
6. Extend automated visual audit for at least `1097x617`:
   - hero height is bounded for the visible viewport
   - promo card is hidden outside the allowed homepage desktop hero state
   - category cards do not overflow, clip, or collapse visually
   - hero/category boundary has intentional spacing
   - no horizontal overflow
   - docked-DevTools / narrow-browser state around `904x617` does not fall back to the mobile promo layout
7. Add manual screenshot sign-off for physical or equivalent test:
   - Windows `1920x1080`
   - display scaling `175%`
   - Chrome zoom `100%`

Exit criteria:
- Homepage at effective `1097x617` CSS viewport is visually usable and intentionally composed.
- The `1024-1279px` band has one coherent compact-laptop scaling model.
- Automated audit covers the compact laptop geometry and interaction risks.
- Manual Windows `175%` scaling screenshot is accepted.

## Phase 6 - Release readiness and deployment (P0, 1 day)
1. Staging deploy + smoke suite + manual business walk-through.
2. Production deploy window.
3. Post-release checks:
   - forms
   - redirects
   - search
   - key conversion flows
   - 404/monitoring
4. Create rollback note and final launch report.

Exit criteria:
- Production web accepted as done.

## Appendix - Configurator implementation status

Code status:
- The original code task is done: the configurator section is no longer just a static CTA to the hot tubs category.
- Theme code now contains Jucra/Visao-aware rendering with safe fallback when the shortcode/plugin is unavailable.
- Product detail pages can pass product-level `jucra_model_name` values to the viewer flow.

Operational status:
- Phase 1 is not fully closed because the external `Visao 3D Viewer` plugin still must be installed and activated in WordPress.
- Final live viewer verification remains blocked until that plugin exists in the runtime environment.
