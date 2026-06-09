# Local-only P0 repair ledger - 2026-06-08

## Rule

From this point, P0 repair work is local-only until explicit owner approval.

- Local target: `http://localhost:8090`
- Production target: no FTP upload, no production DB write, no production data script
- Git requirement: every P0 repair commit must state whether it is deployed to production
- Current production status for new P0 repairs: `not deployed to production`

## Workflow

1. Implement against local WordPress only.
2. Run the focused local smoke/audit for the touched area.
3. Commit and push the repair with a clear `local-only` or `not deployed to production` note.
4. Update this ledger with the commit reference, local evidence, and production status.
5. Deploy to production only after owner approval.

## Repair entries

| Date | Block | Commit | Local evidence | Production status |
| --- | --- | --- | --- | --- |
| 2026-06-08 | P0 local-only workflow gate | this commit | Ledger created before continuing P0 repairs | Not deployed to production |
| 2026-06-08 | P0 category hero CTA visibility for `/virivky/` and `/swimspa/` | this commit | `node tools/category-hero-cta-smoke.js`, `node tools/editable-text-overflow-smoke.js`, `node tools/product-media-smoke.js` passed locally. | Not deployed to production |
| 2026-06-08 | P0 homepage admin save integrity for intro text, service icons, and progress steps | this commit | `docker compose run --rm wpcli wp eval-file wp-content/themes/arctic/tools/page-section-meta-helpers-smoke.php --allow-root`, `docker compose run --rm wpcli wp eval-file wp-content/themes/arctic/tools/homepage-admin-metabox-smoke.php --allow-root`, `node tools/admin-production-fallback-smoke.js`, and focused homepage/frontend smokes passed locally. Full `node tools/admin-editability-smoke.js` timed out locally and remains a broad-suite follow-up. | Not deployed to production |
| 2026-06-08 | P0 homepage hero caption desktop drift from mobile CTA CSS leakage | this commit | `npm run figma:audit` no longer fails on `desktop.heroCaption.height`; desktop measured `.f-caption` is `488x309` with footer hidden. `node tools/homepage-mobile-slider-smoke.js` passed, preserving mobile CTA. The later local catalog offset conflict is now resolved by the Figma-first sweep entry below. | Not deployed to production |
| 2026-06-08 | P0 Figma-first local repair sweep: category hero flow, category/product media, editable text flow, support/download controls, local-only catalog request | this commit | `npm run figma:audit`, `node tools/homepage-mobile-slider-smoke.js`, `node tools/more-info-smoke.js`, `node tools/category-hero-cta-smoke.js`, `node tools/support-mobile-smoke.js`, `node tools/editable-text-overflow-smoke.js`, `node tools/homepage-content-clipping-smoke.js`, `node tools/pricing-catalog-audit.js`, and `node tools/link-smoke.js` passed locally. `pricing-catalog-audit` confirms catalog text/form on local home/categories/products and confirms production does not have the local catalog banner outside existing downloads forms. | Not deployed to production |
| 2026-06-09 | P0 CSS architecture audit: Baspa CSS must stop being the visual frontend authority; Arctic CSS rebuild gate added to the main implementation plan | `8c47450` | `docs/css-arctic-rebuild-audit-2026-06-09.md` documents enqueue evidence, class ownership, Figma metadata contract, and the new Arctic CSS migration path. `docs/end-to-end-implementation-plan.md` now includes this as a gate before further visual P0 hotfixes. | Not deployed to production |

## Open local blockers

| Date | Blocker | Evidence | Production status |
| --- | --- | --- | --- |
| 2026-06-08 | Broad admin editability smoke runtime | `node tools/admin-editability-smoke.js` timed out locally after the focused homepage admin integrity smoke passed | Not deployed to production |
| 2026-06-09 | Baspa `dist/css/style.css` still controls frontend visuals | Audit shows `dist/css/style.css` is enqueued before `dist/css/arctic.css`; new visual work should move to the Arctic CSS rebuild gate before more page-level CSS hotfixes | Not deployed to production |

## Resolved local blockers

| Date | Blocker | Resolution | Production status |
| --- | --- | --- | --- |
| 2026-06-08 | Figma desktop flow conflict with local-only catalog banner | `tools/figma-visual-audit.js` now treats the catalog request banner as local-only optional flow and offsets downstream Figma section checks. Full `npm run figma:audit` passes locally. | Not deployed to production |
