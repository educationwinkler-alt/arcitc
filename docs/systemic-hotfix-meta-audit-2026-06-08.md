# Systemic hotfix meta-audit - 2026-06-08

## Purpose

This audit re-reads the customer feedback audit and repair plan after the homepage text clipping incident. The incident showed that a defect first seen on one page can be a symptom of a broader component/data contract problem.

New rule: any customer-visible defect must be classified before repair:

1. Is this one corrupted data row, or a repeatable data model problem?
2. Is this one CSS selector, or a component/layout contract problem?
3. Is this one missing asset, or a media source/crop pipeline problem?
4. Is this one production omission, or a deploy/data-migration process problem?

A page-only hotfix is acceptable only when the audit proves the problem cannot repeat elsewhere.

## Findings

| Area | Initial symptom | Systemic risk | Current state | Required plan change |
| --- | --- | --- | --- | --- |
| Editable text clipping | Homepage intro paragraph cut after client added `Arctic Spas`. | Many Figma-derived blocks use fixed `height`, `max-height`, `overflow:hidden`, or line clamps on admin text. | Converted from homepage-only fix to `content-overflow-guard.css`; production smoke covers homepage, categories, and products. | Add text-overflow smoke to release QA and inventory any new fixed-height text before page fixes. |
| Mobile homepage slider | First mobile slide was dark/static and controls were hidden. | Mobile behavior was changed through homepage-only CSS/JS overrides, not a unified slider/responsive contract. | Production P0 fix deployed and smoke-covered. | Treat remaining slide/category/mobile issues as a global slider/media contract: all slides need links, images, crop policy, overlay policy, and performance checks. |
| Mobile shell/color thumbnails | Shell preview grid was broken on product detail mobile. | Product colors, cabinet colors, and product media cards share the same image/crop/admin-source problem. | Mobile shell fix deployed for product color swatches. | Expand to global product media/color contract: shell colors, cabinet colors, product thumbnails, configuration thumbnails, and category cards must share one smoke matrix. |
| Footer/copyright/Akcni nabidky | Local fix existed but production still had stale footer/menu/copyright data. | Code changes and WP data changes can diverge; FTP upload alone is insufficient. | Footer files and production data repair were later deployed and smoke-covered. | Every data-backed repair needs a migration script or WP-CLI step, production run evidence, and rollback/backup path. |
| Homepage admin edit deleted blocks | Editing one sentence made benefits/progress disappear. | Repeatable meta save handlers can drop unrelated rows on partial saves, autosave, bad fieldset rows, or missing POST keys. | Homepage malformed meta repair was run; local preservation smoke exists. | Admin preservation must become a cross-model gate: homepage, product configurations, benefits/options, members, contacts, maps, prices, offers, catalog form settings. |
| Catalog/price-list banner | Missing Baspa-like catalog/cenik block. | A code-only banner fixes visibility but not admin control, Ecomail tagging, price discovery, or autoresponder contract. | Reusable section using existing `form-catalog.php` is deployed; smoke proves no direct PDF link and catalog form nonce/type exist. | Convert from code-default section to admin-managed block/settings; add Ecomail source tags per page/product/series and final-domain autoresponder smoke. |
| Product hero descriptions | Product copy is cut mid-sentence. | Same fixed-height text policy as homepage, but on product template. | Included in `content-overflow-guard.css`; Cub production smoke no longer clips. | Do not handle product copy by trimming text until content model is complete; use full admin copy and responsive layout. |
| Product thumbnails/crops | Product listing previews are badly cropped. | This is not per-image; it is an aspect-ratio/object-fit/source-selection contract across all listings and cards. | Audit identifies the problem; some mobile/category fixes exist separately. | Build global product media rules plus smoke for listing cards, category series pages, product details, shell/cabinet thumbnails. |
| Missing product configurations | Most products have one inaccurate configuration. | Data completeness and schema issue, not a template display bug. | Already marked P0 in customer audit. | Implement canonical configuration catalog/import first, then render; avoid per-product manual dummy rows. |
| Benefits/options/equipment | `Vyhody` and `Volitelna vybava` are missing or fallback-like. | Feature/equipment content belongs in a reusable catalog by series/product, not repeated static fallback cards. | Already audited in `product-benefits-options-audit-2026-06-05.md`. | Promote to P0 before visual polishing: feature/equipment catalog, admin applicability, smoke for no `static-fallback` production state. |
| Pricing | Product/category pricing and catalog discovery are incomplete. | Price visibility is a product data contract; a catalog request banner alone does not answer price discovery. | Catalog request banner deployed; price discovery remains incomplete. | Add product price/price_text data contract, import/verify prices, fail production QA on silent empty prices unless explicitly approved. |
| Fallbacks | Local looks okay because seed/Figma fallbacks mask missing production data. | Any fallback can become a fake "done" state. | `admin-production-fallback-smoke.js` exists; audit notes some broader fallback hardening was historically uncommitted. | Add fallback inventory to release QA and require each public block to expose `data-content-source` or `admin-empty`, never silent seed content in production. |
| About/reference/showroom/contact | Customer issues look page-specific. | These share content-source and component-source problems: stale fallback text, reference CPT rendering, map component/address, gallery link model. | Covered in audit but lower visibility than homepage/product issues. | Treat as shared content-component pass: member/contact/map/reference/gallery data sources must be audited across all templates. |
| Contact/showroom maps | Contact pin points to wrong area and showroom lacks map. | Map/address should not be duplicated per template; one address/map source should drive all map renders. | Contact map issue is in audit; not yet fully repaired in this session. | Build shared map/address helper and smoke both contact and showroom pages against the same configured location. |
| Encoding/mojibake | New catalog text initially wrote garbled Czech before correction. | PowerShell/UTF-8 mistakes can enter PHP strings and public HTML. | Encoding smoke exists in package scripts; the new catalog section uses HTML entities decoded in PHP. | Keep encoding smoke in final release gate and run it after any Czech copy edit or data import. |
| Performance | Slow first load was blamed on slides. | Hiding mobile slides or reducing overlays is not a performance strategy. | Mobile slider behavior fixed; performance stack still pending. | Add transfer-size/image-optimizer/cache audit. First-slide media must be compressed/responsive, not hidden. |
| Deploy workflow | Several repairs needed code plus production data. | Manual FTP deploys can miss related files/scripts. | Recent repairs used backups, hash verification, production smoke, and evidence commits. | Create a deploy manifest/checklist per repair class before upload; no "local passed" state counts as done. |

## Audit of the existing plan

The existing customer audit covers all top-level complaints, but some sections still read like independent repair tickets. That is dangerous when the root cause is shared CSS, shared admin data, or shared production workflow.

Required plan correction:

- Before any page-specific P0 fix, run a "repeatability scan" for the same selector/helper/data pattern.
- If the pattern appears in more than one template, the fix must be component-level or data-model-level.
- Any new guard stylesheet or focused smoke must be added to the release QA list, not only run manually once.
- Any code repair that depends on WP data must include the production data step in the same checklist.
- Any customer-facing copy/media block must declare one of: real admin source, explicit approved empty state, or blocked release.

## Current page-only fixes that must not stay page-only

1. `homepage-mobile-slider.css` and `homepage-mobile-slider.js`
   - Keep the deployed P0 behavior.
   - Next slide/media work must move toward a shared slider/media contract.

2. `product-colors-mobile.css`
   - Keep the deployed mobile fix.
   - Next color/cabinet/media work must use a global product color/media smoke, not another single-section CSS override.

3. `content-overflow-guard.css`
   - This has already been promoted from page-only to shared editable-text guard.
   - Still must be added to the formal release QA and later folded into the source LESS/design system.

4. `catalog-request.css` and `section-catalog.php`
   - Keep the deployed conversion block.
   - Still incomplete as a business feature until admin settings, price visibility, Ecomail source tags, and autoresponder proof exist.

5. Production repair scripts under `wp-content/themes/arctic/tools/`
   - Useful for guarded one-time repair.
   - Must be tracked in a migration/deploy ledger so production cannot miss DB changes again.

6. Baspa/Arctic duplicate logic
   - Follow-up audit: `docs/duplicate-baspa-arctic-logic-audit-2026-06-08.md`.
   - Arctic-specific code must be classified as either a visual skin over an existing Baspa owner or a full replacement that retires the old path.
   - The offers homepage incident is the concrete proof: the Arctic hero promo and Baspa small offers section both rendered until the legacy homepage call was removed.

## New release gates

Add these gates to the repair workflow:

1. Text overflow gate:
   - `node tools/editable-text-overflow-smoke.js`
   - Run local and production after any copy/layout/CSS change.

2. Catalog request gate:
   - `node tools/catalog-request-smoke.js`
   - Run local and production after any pricing/catalog/form/menu change.

3. Admin preservation gate:
   - expand `tools/homepage-admin-content-preservation-smoke.js` into cross-model smoke.
   - must cover homepage meta, product configurations, product benefits/options, prices, offers, members, maps, and catalog/Ecomail settings.

4. Product media/crop gate:
   - one matrix for product listing cards, category cards, product detail gallery/configuration thumbnails, shell colors, and cabinet colors.

5. Production migration gate:
   - each production deploy must list code files, assets, DB scripts/WP-CLI commands, backups, hashes, and production smoke results.

## Practical priority update

Before continuing the remaining customer fixes, use this order:

1. Run fallback/admin/text/media smoke on local and production baseline.
2. Convert any page-only symptom into a component/data contract if the pattern repeats.
3. Repair admin save preservation globally enough that client text edits cannot delete unrelated blocks.
4. Complete product data contracts: configurations, colors/cabinets, price/price_text, benefits/options/equipment.
5. Repair content pages from shared sources: references, about, showroom, contact/maps, FAQ/support.
6. Then do final visual/mobile/performance polishing.

The key correction is not "avoid hotfixes forever"; it is "do not let a hotfix close a systemic defect." If a fix is shipped under deadline, the systemic follow-up must be explicit, smoke-covered, and scheduled before client review where it affects acceptance.
