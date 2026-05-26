# Content Parity Audit - Old Arctic Spas to WordPress Redesign

Date: 2026-05-26

## Source Rules

- Old Arctic source: `../Arctic-spas/www`
- Visual/layout source: Figma
- Admin/function source: Baspa.cz
- This audit checks that Arctic business content is sourced from the old Arctic site or explicitly documented as an owner/new-model exception.

## Automated Gate

- Command: `npm run content:parity`
- Included in: `npm run qa:local`
- Current result: passing

The audit validates:

- `docs/migration-map.csv` action counts and key targets.
- Old product PHP source files against `wp-content/uploads/import/legacy-content/product-data.json`.
- Imported legacy product images for hot tubs and swimspa.
- Imported PDF count against download migration rows.
- Required old non-product source files.
- Visible public pages for placeholder text and forbidden live/reference hosts.

## Migration Map Summary

| Action | Count | Phase 4 status |
| --- | ---: | --- |
| `migrate_page` | 2 | Covered by WordPress/Figma templates |
| `redirect_consolidated` | 75 | Covered by redirect smoke and content targets |
| `migrate_product` | 22 | Covered, with Lunar/Orion exceptions below |
| `migrate_product_or_landing` | 4 | Covered as shared product/landing pages |
| `redirect_retired` | 5 | Covered as retired products |
| `import_download` | 26 | Covered by imported PDFs and dynamic old PDF redirects |
| `skip_missing_download` | 1 | Documented missing old PDF |

## Product Parity

| Area | Evidence | Status | Notes |
| --- | --- | --- | --- |
| Active products | 26 product targets in `migration-map.csv` | complete | 22 direct products plus 4 wider assortment product/landing rows |
| Extracted old product content | 24 entries in `product-data.json` | complete | All old archive product files that exist locally are extracted |
| Product parameters | Extracted table rows in `product-data.json` | complete | Standard hot tub/swimspa products have parameter rows |
| Product images | `uploads/import/legacy-products/*.jpg` | complete | Legacy hot tub/swimspa images are used for active old products |
| Lunar | Owner/new model content | exception | Old local PHP archive does not contain `virivka-lunar.php`; product is treated as new 2025 Core content |
| Orion | Owner/new model content | exception | Old local PHP archive does not contain `virivka-orion.php`; product is treated as new 2025 Core content |
| Retired products | `redirect_retired` rows | complete | Dreammaker, Frontier, Ellesmere, Aurora, Orca/Grizzly style retired paths redirect away from active product detail scope |

## Non-Product Content

| Old source | New target | Status | Notes |
| --- | --- | --- | --- |
| `/faq.php` | `/podpora/` | complete | FAQ/support content is seeded into editable FAQ/support structures |
| `/diskuze.php` | `/reference/` | fixed in Phase 4 | Redirect now points to references because the old page contains customer reference/discussion content |
| `/download.php` | `/ke-stazeni/` | complete | Download page uses the `download` CPT |
| `/kontakt.php` | `/kontakt/` | complete | Contact data and legal identity are preserved through contact/showroom templates |
| `/prodejna-bazeny-virivky.php` | `/showroom/` | complete | Showroom destination is preserved |
| `/cookies.php` | `/ochrana-osobnich-udaju/` | complete | Privacy/legal content is consolidated |
| `/zasady-zpracovani-osobnich-udaju.php` | `/ochrana-osobnich-udaju/` | complete | Privacy/legal content is consolidated |

## Downloads

| Check | Result | Notes |
| --- | --- | --- |
| Importable old PDF rows | 26 | All have corresponding files in `uploads/import/downloads/` |
| Missing PDF rows | 1 | `/content/download/as-sluzby-cenik-2022.pdf` resolves to non-PDF/404 source and redirects to `/ke-stazeni/` |
| Old PDF redirect behavior | covered | `redirect:smoke` checks old PDF paths |

## Visible Page Checks

The automated gate checks these public routes:

- `/reference/`
- `/podpora/`
- `/ke-stazeni/`
- `/kontakt/`
- `/showroom/`
- `/ochrana-osobnich-udaju/`

It blocks visible placeholder text such as `Lorem ipsum`, `Sample Page`, `Hello world`, `example.com`, `bude dopln`, `TBD`, and live/reference hosts such as `baspa.cz`, live `arctic-spas.cz`, and Ecomail API hosts.

## Exceptions Requiring Owner Awareness

- Lunar and Orion are not present in the old local Arctic PHP archive. They are documented as new 2025 Core model content using owner/import assets.
- `as-sluzby-cenik-2022.pdf` is not importable from the old source because the old URL resolves to non-PDF content.
- Legal entity text can mention `BASPA s.r.o.` where it is the real operating/legal entity; this is not considered Baspa marketing copy.

## Phase 4 Exit Criteria

- Content parity checklist is complete: complete.
- No visible page relies on lorem ipsum, Figma placeholder copy, or Baspa-specific business copy: automated gate passing.
- Owner/client has a clear list of content exceptions requiring approval: complete.
