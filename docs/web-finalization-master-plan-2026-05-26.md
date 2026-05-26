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
- Breakpoint normalization is in place (`data-off-breakpoint="1280"`).
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
1. Run manual Figma QA checklist page-by-page:
   - homepage desktop/mobile
   - header dropdowns
   - category pages
   - product detail variants
   - support/downloads
   - contact/showroom
   - references
   - information pages
2. For each page record:
   - pass/fail
   - screenshot pair
   - exact delta note
   - fix commit reference
3. Close only when all high-priority deltas are resolved.

Exit criteria:
- Signed pass sheet for all required pages and breakpoints.

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
