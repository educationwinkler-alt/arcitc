# Homepage customer feedback audit - 2026-06-08

Scope: homepage comments from the customer, local `http://localhost:8090/`, production `https://illuminatus.cz/`, and existing Figma/export documentation in this repository.

Sensitive production access exists outside this report. Credentials are intentionally not copied here.

## Evidence

- Browser audit output: `docs/screenshots/homepage-customer-audit-2026-06-08/audit.json`
- Screenshots:
  - `docs/screenshots/homepage-customer-audit-2026-06-08/local-mobile390.png`
  - `docs/screenshots/homepage-customer-audit-2026-06-08/local-desktop1440.png`
  - `docs/screenshots/homepage-customer-audit-2026-06-08/prod-mobile390.png`
  - `docs/screenshots/homepage-customer-audit-2026-06-08/prod-desktop1440.png`
- Figma source available locally as `.fig` files in the parent directory:
  - `Arctic-spas.cz grafika.fig`
  - `Arctic-spas.cz wireframe.fig`
- Live Figma MCP comparison was not possible from a local `.fig` file alone. Current comparison uses repo Figma exports and docs such as `docs/arctic-scaling-figma-map.md`.

## Summary

The customer comments are valid. The homepage is failing in three different layers:

1. Production content data is incomplete after admin editing. Local has seeded homepage meta, production does not.
2. Mobile slider behavior is actively disabled by CSS, even though Swiper initializes.
3. Homepage assets are too large for first load, especially the footer background and desktop hero imagery.

This is how an admin edit can make major sections disappear: the templates are intentionally production-strict and only render homepage benefits/progress from page meta. If those repeatable meta rows are missing or saved empty, production does not fall back to Figma/seed data. Local still looks fine because local seed fallbacks and local seeded meta hide the issue.

## Customer feedback coverage check - 2026-06-08

Result after re-reading the full customer list: every top-level customer complaint is covered in the audit/repair plan. The "O nas" item was present, but it was buried inside the broader support/reference/showroom/contact addendum; this checklist makes that explicit.

Homepage:

- Slow first load: covered by `P0 - First load is too heavy`, `Baspa environment parity`, and performance repair order.
- Missing slide click-through: covered by `P0 - Slide links are not usable` and CTA/proklik repair.
- Mobile only shows the first dark slide: covered by `P0 - Mobile slider is disabled by CSS`, Figma notes, and mobile slider CSS repair.
- Misaligned mobile `Virivky` / `Celorocni bazeny` images: covered by `P1 - Mobile category card images are same-size but visually misaligned`.
- Admin edit removed `Nase sluzby` pictograms/progress bullets: covered by `P0 - Admin edit caused homepage sections to disappear`, `Admin save hardening`, and June 15 acceptance gates.
- Footer menu should remove `Skladove virivky`: covered by `P1 - Footer menu and copyright are data plus test changes` and offers rename plan.
- Copyright should be `BASPA s.r.o.`: covered by the same footer/copyright section.

Category pages:

- Hot tub and swimspa hero CTAs are clipped/missing: covered by `P0 - Desktop category hero CTA is clipped below the hero`.
- Product thumbnails are badly cropped: covered by `P0 - Product thumbnails are visually bad because product renders are forced into a scaled wide frame`.
- Missing Baspa-style model-series summary pages: covered by `P0 - Series summary pages are only generic archives, not Baspa-style model-series pages`.

Product pages:

- Too few/inaccurate configurations: covered by `P0 - Configuration data is incomplete and the current schema is too shallow`.
- Every model needs all available configurations: covered by the configuration catalog and product smoke plan.
- Customer may edit labels later, but we must deliver correct structure/data: covered by configuration schema extension and admin save hardening.
- Hero description is cut mid-sentence: covered by `P0 - Product hero descriptions are deliberately cut mid-sentence`.
- Cabinet finishes/colors are missing: covered by `P0 - Cabinet colors are missing almost everywhere`.
- `Vyhody` links are dead: covered by `P0 - "Vyhody" and "Volitelna vybava" links are dead on production hot tub pages`.
- `Volitelna vybava` is missing: covered by the same link section plus `P0 - Standard and optional equipment need a real catalog, not static fallback cards`.
- Standard and optional equipment need detail content/links: covered by the feature/equipment catalog repair plan.
- Swimspa benefit/equipment links/sections are missing: covered by `P0 - Swimspa pages are missing benefits and optional equipment by template design`.
- Product copy/photos are weak or fallback-like: covered by `P1 - Product photos and content quality need a source pass` and the June 15 no-fallback acceptance gate.
- Mobile shell-color thumbnails must also be fixed in production: covered by `P0 - Mobile shell-color thumbnails must be fixed on production, not only locally`.

Support and information pages:

- `Kolik stoji provoz a udrzba` should move into support FAQ: covered by `P0 - "Kolik stoji provoz a udrzba" is still a standalone page and menu item`.
- References should behave like Baspa references, not image-only: covered by `P0 - References archive is image-only instead of a content archive`.
- `O nas` is stale: covered by `P1 - "O nas" content is stale` and the support/reference/showroom/contact repair order.
- Showroom map is missing: covered by `P0 - Showroom page has no embedded map and the gallery CTA is not a real gallery`.
- Showroom gallery CTA leads nowhere useful: covered by the same showroom section.
- Contact map is unclear/wrongly centered: covered by `P0 - Contact map is too dark and uses a shifted map viewport`.
- `Dalsi informace` needs `Poptavkovy formular` above `Servis`: covered by `P1 - "Dalsi informace" menu is missing inquiry form link`.

Commercial/admin requirements:

- Missing prices / price-list discovery: covered by `P0 - Price discovery is not complete enough for launch`.
- Reusable catalog/price-list email CTA visible before final Ecomail activation: covered by `P0 - Catalog/price-list CTA must be reusable and present at buying points`.
- Ecomail handoff on final domain: covered by `P0 - Ecomail handoff needs a production contract, not just code`.
- `Vyprodej skladovych virivek` must become `Akcni nabidky`: covered by `P0 - Offers must be renamed and made publication-safe`.
- Four editable offer types with only published offers visible: covered by the same offers section.
- Client must be able to edit texts, contacts, phones, and global facts in wp-admin without breaking sections: covered by admin save hardening, no-fallback gates, and the June 15 acceptance gate.

## Content source strategy - 2026-06-08

This project is for Arctic Spas products. Baspa is the template/reference environment, not a license to fill the Arctic site with generic BASPA product content. Use Baspa pages for proven structure, admin patterns, conversion flows, current company facts, and Arctic-specific content only.

Source priority:

1. Existing WP/admin data and production exports:
   - preserve IDs, menus, media, CPT relationships, and editable fields,
   - never replace real admin content with seed/Figma fallback content.
2. Current Baspa public pages and admin data for company/contact/showroom facts:
   - current `O nas` facts, team, stats, partner list, and footer/contact facts,
   - current showroom address/map/contact facts,
   - current contact people, phones, emails, and opening hours.
3. Current Baspa Arctic product/series pages:
   - series structure,
   - indicative prices,
   - parameter matrices,
   - shell/cabinet color applicability,
   - catalog/price-list CTA behavior,
   - references block structure.
4. Existing/old Arctic Spas CZ pages and official Arctic Spas materials:
   - model-level configurations,
   - feature/equipment descriptions,
   - technical names,
   - compatibility per hot tub/swimspa series.
5. Figma:
   - layout, visual hierarchy, component intent, responsive behavior,
   - not a source of product truth unless the content is also verified elsewhere.
6. Client/BASPA confirmation:
   - final arbiter for facts that are time-sensitive or commercial: prices, staff, phone numbers, stats, current copy, published offers, Ecomail settings, and legal/privacy wording.

Do not use as final public content:

- local seed fallback copy,
- Figma placeholder copy,
- machine-translated feature text,
- generic BASPA pool/reference content that is not relevant to Arctic Spas,
- stale `O nas` facts from the current Arctic build.

### Data source by repair area

Homepage:

- Slides, CTAs, services, progress, and intro text must come from WP/admin data.
- Images should use approved Arctic media from current imports or client-approved replacements, then optimized through the image pipeline.
- Baspa homepage can be used for behavior and conversion patterns, not as product copy.

Categories and series:

- Hot tub and swimspa category/series content should use current Baspa Arctic pages as the closest functional reference:
  - `https://baspa.cz/produkt/virivky-arctic-spas/serie-arctic-spas-custom/`
  - `https://baspa.cz/produkt/bazeny-arctic-classic/`
- Build real Arctic series pages; do not create generic BASPA pool series pages in this project.

Product details:

- Configurations come from old/current Arctic Spas CZ product material, Baspa Arctic pages, and official Arctic Spas documents/pages.
- Feature/equipment copy can use official Arctic Spas feature pages as source material, but must be rewritten/verified in Czech and assigned per product/series.
- Product photos must be marked as approved official, approved BASPA/client, temporary legacy, missing, or wrong crop.

`O nas`:

- Primary source for current company facts is the current Baspa `O nas` page and admin/member data:
  - company founded in 2013,
  - activities in pools/spas since 2003,
  - current public stats are `23+` years, `1200+` clients, `13` team members,
  - current team list and partner list come from Baspa/current admin data.
- Arctic-specific partner copy must stay focused on the Arctic Spas relationship; remove or de-emphasize unrelated suppliers unless the client wants the Arctic site to present the full BASPA company profile.
- Because the client explicitly says the current Arctic `O nas` is more than a year old, all public facts must be marked `client-verify` before final delivery if they are not copied from current Baspa admin/public data.

References:

- Baspa references are the structure model: cards with image, product/category label, title/location/year, description, and link/detail.
- Actual Arctic site references must be filtered to Arctic Spas hot tubs/swimspa or explicitly approved cross-sell/company references. Do not bulk-import unrelated BASPAWOOD/PLAYA references as Arctic product proof.

Support/FAQ:

- `Kolik stoji provoz a udrzba` should be split into editable FAQ entries.
- Sources are existing Arctic/Baspa maintenance content plus client-approved service wording, not static template fallback text.

Pricing/catalog/Ecomail:

- Price and catalog CTA behavior should follow Baspa Arctic product/series pages.
- Actual price values must come from current Baspa Arctic pages, client price list, or admin price fields; if missing, QA should fail instead of silently hiding price.
- Ecomail credentials/settings are production-only and must not be copied into docs.

## Findings

### P0 - Admin edit caused homepage sections to disappear

Production state:

- `.f-section--arctic-benefits`: not rendered, `0` benefit cards.
- `.f-section--progress`: rendered with `data-content-source="homepage-meta"`, but `0` progress steps.

Local state:

- benefits: `3` cards, `data-content-source="homepage-meta"`.
- progress: `6` steps, `data-content-source="homepage-meta"`.

Relevant code:

- `wp-content/themes/arctic/templates/section/benefits.php:31` reads `homepage_benefits`.
- `wp-content/themes/arctic/templates/section/benefits.php:63` returns early on production if benefits are empty and seed fallbacks are disabled.
- `wp-content/themes/arctic/templates/section/progress.php:18` reads title/text meta.
- `wp-content/themes/arctic/templates/section/progress.php:22` reads `homepage_progress_steps`.
- `wp-content/themes/arctic/templates/section/progress.php:41` returns early only if title/text and steps are all empty; therefore production can show an empty progress shell with no list items.
- `wp-content/themes/arctic/inc/functions/environment.php:13` allows seed fallbacks only in `local` and `development`.
- `wp-content/themes/arctic/modules/pages/type/metabox.php` defines the homepage sections as repeatable `fieldset_text` fields.

Likely direct cause:

- The production homepage has missing/empty repeatable meta rows after an admin save.
- The edit to "Jsme vyhradni prodejce" probably saved the homepage post while repeatable fields were absent, empty, or not preserved by the admin UI/request.
- Current QA checked the local seeded state, not production customer content after real admin saves.

Repair plan:

1. Back up production DB before any write.
2. Restore production homepage meta for:
   - `homepage_benefits`
   - `homepage_benefit_images`
   - `homepage_progress_title`
   - `homepage_progress_text`
   - `homepage_progress_steps`
3. Add a production data audit command that fails when critical homepage repeaters are empty.
4. Harden admin saving:
   - do not delete existing homepage repeater rows when the fields are missing from the save request,
   - prefer a structured Meta Box `group`/clone setup over fragile `fieldset_text`,
   - add an admin warning or validation if a critical section is being saved empty.
5. Add production-mode smoke:
   - `ARCTIC_BASE_URL=https://illuminatus.cz`
   - homepage benefits count must be `3`,
   - progress count must be `6`,
   - content source must be admin/meta, not local fallback.

### P0 - Mobile slider is disabled by CSS

Observed production mobile:

- `3` slides exist in DOM.
- Swiper initializes and autoplay is enabled.
- After waiting, active slide remains the first slide.
- Only slide `0` is visible.
- navigation and pagination are hidden.

Relevant CSS:

- `wp-content/themes/arctic/src/less/_component-contracts.less:1155`
  hides pagination/navigation/static controls on mobile.
- `wp-content/themes/arctic/src/less/_component-contracts.less:1162`
  forces `.f-slides__wrapper { transform: none !important; }`.
- `wp-content/themes/arctic/src/less/_component-contracts.less:1166`
  hides all slides except `.f-slide--1`.
- `wp-content/themes/arctic/src/less/_components.less:3447`
  sets a fixed mobile slideshow height.
- `wp-content/themes/arctic/src/less/_components.less:3463`
  hardcodes a mobile CSS background.
- `wp-content/themes/arctic/src/less/_components.less:3473`
  hides the actual slide image on mobile in that older block.
- `wp-content/themes/arctic/src/less/_component-contracts.less:3191`
  adds a dark mobile overlay over slide media.

Repair plan:

1. Remove the mobile rule that hides non-first slides.
2. Remove the forced `transform: none` wrapper override.
3. Keep Swiper active on mobile.
4. Restore mobile pagination, at least bullets; arrows can remain hidden if the touch/pagination UX is approved.
5. Stop replacing real slide images with one CSS background.
6. Remove or drastically soften the dark overlay on mobile.
7. Add a Playwright test:
   - mobile `390px`,
   - visible slide changes after autoplay delay,
   - bullet active state changes,
   - at least two unique slide titles become active during the test.

### P0 - Slide links are not usable

Observed:

- First slide has no CTA/link at all.
- Second and third slides have links in DOM:
  - `/virivky/`
  - `/swimspa/`
- But the CTA footer is hidden by CSS, so the buttons have a `0x0` layout box and are not usable.

Relevant code:

- `wp-content/themes/arctic/modules/slides/templates/slide/button.php:8` reads slide link meta.
- `wp-content/themes/arctic/modules/slides/templates/slide/button.php:12` renders a link only when button text and URL data exist.
- `wp-content/themes/arctic/src/less/_components.less:3141` hides `.template--homepage .f-caption__footer`.

Repair plan:

1. Decide UX:
   - visible CTA button on every slide, or
   - whole slide clickable with a visible/focusable link affordance.
2. Restore `.f-caption__footer` for homepage slides if CTA buttons are the intended Figma/UX behavior.
3. Add URL/button data for the first slide or add safe seed-key defaults for known seeded slides.
4. Test keyboard focus and mobile tap behavior.

### P0 - First load is too heavy

Measured top image/resource load:

- Mobile top 20 resources: about `18.2 MB`, almost all images.
- Desktop top 20 resources: about `27.9 MB`, almost all images.

Largest local files:

- `footer-background.jpg`: `4096x2731`, about `11.9 MB`.
- `hp-hero-arctic-spas-07.jpg`: `4096x1474`, about `7.5 MB`.
- `hp-category-celorocni-bazeny.png`: `1920x1280`, about `3.5 MB`.
- `mobile-category-celorocni-bazeny.png`: `1920x1280`, about `3.5 MB`.
- `footer-map.png`: about `1.1 MB`.

Repair plan:

1. Create optimized responsive derivatives for hero/category/footer assets.
2. Use WebP/AVIF where production supports it.
3. Do not load a 4096px footer background during the homepage first view.
   - Prefer an actual `<picture><img loading="lazy">` background layer in the footer over a CSS background that browsers can fetch early.
4. Keep `fetchpriority="high"` only for the first real hero image.
5. Lazy load non-first slide images.
6. Add an image budget test:
   - mobile initial image transfer target under `3 MB`,
   - desktop initial image transfer target under `5-6 MB` unless explicitly waived.

### P1 - Mobile category card images are same-size but visually misaligned

Measured mobile layout:

- `Vírivky`: `335x221`.
- `Celorocni bazeny`: `335x221`.
- Both image boxes match the card boxes.

But visual crop differs:

- `Vírivky` object position: `50% 100%`.
- `Celorocni bazeny` object position: `100% 100%`.
- The swimspa mobile asset is also a heavy `1920x1280` PNG.

Relevant code:

- `wp-content/themes/arctic/modules/products/templates/section-categories.php:23` defines hardcoded homepage category images.
- `wp-content/themes/arctic/modules/products/templates/section-categories.php:78` swaps mobile sources at `max-width: 767px`.
- `wp-content/themes/arctic/src/less/_components.less:3399` sets hot tub image position.
- `wp-content/themes/arctic/src/less/_components.less:3403` sets swimspa image position.

Repair plan:

1. Export/crop both mobile category images as a matched pair from the approved Figma mobile composition.
2. Normalize object-position after asset selection.
3. Replace the `1920x1280` mobile PNG with a compressed mobile-size asset.
4. Add screenshot guard at `390px` showing both cards in one vertical flow.

### P1 - Footer menu and copyright are data plus test changes

Observed local and production footer menu:

- Footer column `Vírivky` contains `Skladové vířivky`.
- The item comes from the WP footer menu, not only from PHP fallback.

Relevant code/data:

- `wp-content/themes/arctic/templates/footer.php:21` reads `navigation_footer`.
- `wp-content/themes/arctic/templates/footer.php:121` also has `Skladové vířivky` in the PHP fallback.
- Local menu item exists in WP menu id `11`.

Copyright:

- Local and production currently render `Copyright © 2026 Arctic Spas CZ. Všechna práva vyhrazena.`
- Source is Customizer setting `baspa_copyright`.
- Customer wants `BASPA s.r.o.`

Repair plan:

1. Remove the WP footer menu item `Skladové vířivky` from production and local seed/menu data.
2. Remove the same item from PHP fallback so it does not return if the menu is missing.
3. Update Customizer value `baspa_copyright` to the approved BASPA text.
4. Update smoke tests that currently expect the old Arctic Spas CZ copyright and presence of the stock-hot-tub footer item.

## Figma notes

- Desktop Figma map expects homepage hero at `1920 x 795` and desktop arrows visible.
- Current mobile implementation is not a faithful live slider state; it hardcodes a mobile hero/crop and hides the slider state.
- A previous mobile audit recommended a single mobile hero. The current customer request supersedes that: mobile slider should run. The repair should keep the mobile slider active while using Figma-approved mobile crops and removing the dark filter.
- For full frame-by-frame Figma verification, use the live Figma URL/file key. Local `.fig` files are not directly readable through the current MCP tool.

## Category and series addendum - 2026-06-08

Scope: new customer comments for `Venkovni virivky Arctic Spas` and `Celorocni bazeny`, local `http://localhost:8090/`, production `https://illuminatus.cz/`, current code, Figma category frame, and Baspa reference pages:

- `https://baspa.cz/produkt/virivky-arctic-spas/serie-arctic-spas-custom/`
- `https://baspa.cz/produkt/bazeny-arctic-classic/`

Additional evidence:

- Browser audit output: `docs/screenshots/category-customer-audit-2026-06-08/audit.json`
- Screenshots:
  - `docs/screenshots/category-customer-audit-2026-06-08/local-virivky-desktop1440-top.png`
  - `docs/screenshots/category-customer-audit-2026-06-08/local-virivky-desktop1440-products.png`
  - `docs/screenshots/category-customer-audit-2026-06-08/local-virivky-mobile390-top.png`
  - `docs/screenshots/category-customer-audit-2026-06-08/local-virivky-mobile390-products.png`
  - `docs/screenshots/category-customer-audit-2026-06-08/local-swimspa-desktop1440-top.png`
  - `docs/screenshots/category-customer-audit-2026-06-08/local-swimspa-desktop1440-products.png`
  - `docs/screenshots/category-customer-audit-2026-06-08/local-swimspa-mobile390-top.png`
  - `docs/screenshots/category-customer-audit-2026-06-08/local-swimspa-mobile390-products.png`
  - production equivalents in the same folder with the `prod-` prefix.
- Audit helper: `tools/category-customer-audit.js`.

Figma source found:

- `docs/figma-handoff.md` contains the URLs and file keys:
  - Wireframe file key: `puPBNFpuaXpRZR2TINaDvm`
  - Final graphics file key: `xeOew3dFjDVfjXZrJ09emM`
- `docs/figma-structure.md` maps the final category frame:
  - `KATEGORIE` -> node `1:262`, size `1920 x 8583`
  - wireframe `WF - KATEGORIE` -> node `100:1504`, size `1920 x 8902`
- Live Figma MCP check of final graphics node `1:262` confirms the category hero contains a CTA labeled `Vybrat virivku`, product cards, and series headings `Serie Custom`, `Serie Classic`, `Serie Core`.

### P0 - Desktop category hero CTA is clipped below the hero

Observed on local and production at `1440px`:

- `/virivky/` and `/swimspa/` both render the hero CTA element.
- The CTA box starts at `y=623px` and ends at `y=673px`.
- The hero box ends at about `596px`.
- The hero has `overflow: hidden`, so the CTA is clipped outside the title image.
- The button center is not hit-testable as the button; the audit sees the following intro section at that point.

Observed on mobile `390px`:

- The same CTA is visible inside the hero at about `y=428px`.

Relevant code:

- `wp-content/themes/arctic/src/less/_components.less:9834` sets the category hero height and `overflow: hidden`.
- `wp-content/themes/arctic/src/less/_components.less:9972` positions the desktop button at fixed `top: 623px`.
- `wp-content/themes/arctic/templates/heading/term.php:58` renders the CTA as a contact modal button, not as a link/anchor into product selection.

Repair plan:

1. Move category hero CTA positioning inside the actual scaled hero height.
   - Prefer relative positioning to the headline block or `top: calc(var(--arctic-hero-height) - <safe offset>)`.
   - Verify at `1280`, `1366`, `1440`, `1536`, `1920`.
2. Give `.f-heading__headline`/CTA an explicit stacking context above the media overlay.
3. Decide CTA behavior per page:
   - for `/virivky/`, `Vybrat virivku` should probably scroll to `#serie-custom` or `#products`;
   - for `/swimspa/`, `Vybrat bazen` should probably scroll to `#serie-swimspa-classic` or `#products`;
   - if contact modal remains intended, rename the CTA so it does not imply product selection.
4. Add a browser smoke:
   - desktop category CTA center must hit the button itself;
   - CTA bottom must be within hero bottom;
   - mobile CTA remains visible.

### P0 - Product thumbnails are visually bad because product renders are forced into a scaled wide frame

Observed on local and production:

- Category card images all use `data-product-media="product-image"`.
- Hot tub card images are mostly square/tall source renders, for example `1265 x 1600` and `1600 x 1600`.
- Swimspa card images are tall renders, for example `390 x 629` on mobile and `634 x 1024` on desktop.
- Desktop frame is fixed at `281 x 215` inside a `335 x 333` card.
- Mobile frame is about `323.5 x 181.97`.
- The image is then visually enlarged to `1.08`, so the rendered image box is larger than the clipped frame.

Relevant code:

- `wp-content/themes/arctic/modules/products/templates/post/listing/image.php:7` chooses `large` image size on product category archives.
- `wp-content/themes/arctic/modules/products/templates/post/listing/image.php:12` uses the first `product_image` attachment and falls back to featured image.
- `wp-content/themes/arctic/src/less/_component-contracts.less:864` defines the product card media frame.
- `wp-content/themes/arctic/src/less/_component-contracts.less:871` sets `object-fit: contain` but also `transform: scale(1.08)`.
- `wp-content/themes/arctic/src/less/_components.less:10238` hardcodes the desktop media frame at `281 x 215`.
- `wp-content/themes/arctic/src/less/_components.less:11799` later re-centers images but does not remove the scale/crop pressure.

Repair plan:

1. Remove the `transform: scale(1.08)` from category product card images.
2. Keep `object-fit: contain` and `object-position: center center`, but make the media frame and image sizing consistent across the contract and desktop override blocks.
3. Introduce a dedicated product-card media field or documented attachment convention:
   - isolated product render,
   - transparent or clean light background,
   - safe padding around the full product,
   - exported to the actual card ratio used by the design.
4. Regenerate/replace current card thumbnails from the approved source renders, especially swimspa renders that are tall but displayed in a wide frame.
5. Add a product-card QA check:
   - no card image transform scale;
   - image stays inside media frame;
   - consistent frame ratio on desktop/mobile;
   - screenshot review for first 8 hot tub and all 6 swimspa cards.

### P0 - Series summary pages are only generic archives, not Baspa-style model-series pages

Observed:

- Existing canonical term URLs return `200`:
  - `/rada/custom/`
  - `/rada/classic/`
  - `/rada/core/`
  - `/rada/swimspa-classic/`
  - `/rada/swimspa-custom/`
- But the pages are generic taxonomy archives with only the term H1 and an empty term description. They do not match the Baspa model-series page structure.
- Baspa-compatible/customer examples return `404` on local and production:
  - `/produkt/virivky-arctic-spas/serie-arctic-spas-custom/`
  - `/produkt/bazeny-arctic-classic/`
- Product category pages currently link to in-page anchors only:
  - `/virivky/#serie-custom`
  - `/swimspa/#serie-swimspa-classic`

Relevant code/data:

- `wp-content/themes/arctic/modules/products/type/taxonomy.php:113` registers `product-series` as public with rewrite slug `rada`.
- `wp-content/themes/arctic/templates/section/product-series-nav.php:8` hardcodes category navigation as anchors.
- `wp-content/themes/arctic/modules/products/templates/section-products.php:53` groups category products by `product-series`.
- `wp-content/themes/arctic/modules/products/templates/section-products.php:92` hardcodes short series copy inside the category listing template.
- `wp-content/themes/arctic/archive.php:7` is the generic fallback currently used for `product-series` archives.
- Local `product-series` term meta for `custom` and `swimspa-classic` is empty, despite admin fields existing for basic series hero media/title/text.

Baspa reference page structure to reproduce:

- Hero with title, summary, CTA, price and product/series image.
- Section navigation: parameters/description, variants, progress, FAQ, references.
- Parameter matrix by models in the series.
- Rich description and feature bullets.
- Variant/product cards for all models in the series.
- For hot tubs: shell/cabinet color sections.
- References, catalog CTA, accessories, contact person, and progress section.

Repair plan:

1. Create a dedicated `taxonomy-product-series.php` template.
2. Use existing `product-series` term URLs as canonical pages:
   - `/rada/custom/`, `/rada/classic/`, `/rada/core/`
   - `/rada/swimspa-classic/`, `/rada/swimspa-custom/`
3. Add compatibility rewrites/redirects for customer/Baspa-style URLs:
   - `/produkt/virivky-arctic-spas/serie-arctic-spas-custom/` -> `/rada/custom/`
   - `/produkt/virivky-arctic-spas/serie-arctic-spas-classic/` -> `/rada/classic/`
   - `/produkt/virivky-arctic-spas/serie-arctic-spas-core/` -> `/rada/core/`
   - `/produkt/bazeny-arctic-classic/` -> `/rada/swimspa-classic/`
   - `/produkt/bazeny-arctic-custom/` -> `/rada/swimspa-custom/`
4. Extend `product-series` admin/meta beyond current hero fields:
   - series price text,
   - CTA label/target,
   - parameter matrix rows,
   - rich description,
   - feature bullets,
   - shell/cabinet color groups where applicable,
   - related variant/product ordering,
   - FAQ category/filter,
   - reference category/filter,
   - accessories section toggle/filter,
   - contact owner.
5. Reuse existing shared sections where possible:
   - product cards from `modules/products/templates/post/listing`,
   - progress section from `templates/section/progress.php`,
   - recent references from `modules/references/templates/section-recent.php`,
   - contact CTA/member card patterns.
6. Update category pages to expose the new summary pages:
   - keep anchors for quick on-page browsing if useful,
   - add visible `Zobrazit serii` links in each series header/card area,
   - update footer/mega menu links from `#serie-*` anchors where a real series page is expected.
7. Add smoke tests:
   - all five `/rada/*/` pages return `200` and contain hero, nav, parameter section, variant cards and progress;
   - Baspa-style URLs redirect with `301/302` to the canonical series page;
   - category series links are no longer dead-end anchors only.

### Category repair order

1. Fix clipped desktop category hero CTA and decide product-selection vs contact-modal behavior.
2. Remove card image scaling and normalize product-card media frames.
3. Replace bad card thumbnails with approved card-safe renders.
4. Build real `product-series` pages and compatibility redirects.
5. Wire category/footer/mega links to the new series pages.
6. Re-run `node tools/category-customer-audit.js` against local and production, plus the existing visual/product-media smokes.

## Product pages addendum - 2026-06-08

Scope: new customer comments for product detail pages generally, local `http://localhost:8090/`, production `https://illuminatus.cz/`, current product detail templates, final Figma frame `DETAIL KONKRETNIHO PRODUKTU`, the old/current Cub reference, and the corporate Arctic Spas features page:

- `https://www.arctic-spas.cz/virivka-cub.php`
- `https://www.arcticspas.com/features/`

Additional evidence:

- Browser audit output: `docs/screenshots/product-customer-audit-2026-06-08/audit.json`
- Audit helper: `tools/product-customer-audit.js`
- Key screenshots:
  - `docs/screenshots/product-customer-audit-2026-06-08/local-cub-desktop-top.png`
  - `docs/screenshots/product-customer-audit-2026-06-08/local-cub-desktop-sections.png`
  - `docs/screenshots/product-customer-audit-2026-06-08/prod-cub-desktop-top.png`
  - `docs/screenshots/product-customer-audit-2026-06-08/prod-cub-desktop-sections.png`
  - `docs/screenshots/product-customer-audit-2026-06-08/local-athabascan-desktop-sections.png`
  - `docs/screenshots/product-customer-audit-2026-06-08/prod-athabascan-desktop-sections.png`
  - `docs/screenshots/product-customer-audit-2026-06-08/local-ocean-desktop-sections.png`
  - `docs/screenshots/product-customer-audit-2026-06-08/prod-ocean-desktop-sections.png`
- Figma final graphics:
  - file key `xeOew3dFjDVfjXZrJ09emM`
  - node `1:1461` / `DETAIL KONKRETNIHO PRODUKTU`
- Figma wireframe:
  - file key `puPBNFpuaXpRZR2TINaDvm`
  - node `100:662` / `WF - DETAIL KONKRETNIHO PRODUKTU`

Figma MCP check of node `1:1461` confirms the product detail frame expects:

- hero title and full useful hero summary,
- product facts,
- sticky section navigation,
- configuration section,
- shell and cabinet color sections,
- product benefit/equipment card grids,
- references,
- contact CTA and footer.

### P0 - Configuration data is incomplete and the current schema is too shallow

Observed local:

- `17 / 22` audited standard products have `0-1` configuration card.
- Cub has exactly one configuration: `Custom Cub`.
- Classic/Custom hot tubs are mostly placeholders such as `Custom Summit`, `Classic McKinley`, `Classic Eagle`.
- Swimspa Classic products `Athabascan`, `Hudson`, `Kingfisher`, `Wolverine` each have one generic configuration.
- Ocean and Okanagan are better seeded with multiple named configurations, but still use the same shallow card schema.

Observed production:

- `22 / 22` audited product pages returned `200`.
- `17 / 22` audited standard products have `0-1` configuration card.

Old Cub reference:

- The old Cub page has four configurations:
  - `Prestige 20/1`
  - `Signature 40/2`
  - `Legend 40/3`
  - `SDS 40/3`
- Each configuration includes detailed jet rows and pump rows, not only one aggregate `jets` and `pumps` value.

Relevant code:

- `wp-content/themes/arctic/modules/products/templates/post/single/configurations.php` renders only normalized fields: name, price, seats, jets, pumps, dimensions, notes, image.
- `wp-content/themes/arctic/modules/products/inc/configurations.php:302` stores structured configurations.
- `wp-content/themes/arctic/modules/products/inc/configurations.php:307` deletes `product_configuration_items` when normalized configurations are empty.
- `wp-content/themes/arctic/modules/products/inc/configurations.php:618` deletes the same meta when the posted payload is not an array.

Repair plan:

1. Build a canonical product configuration catalog for every model, separated by series and product type.
   - Hot tub Custom examples must include the correct Prestige/Signature/Legend/SDS sets per model.
   - Classic/Core need their own correct sets, not copied placeholders.
   - Swimspa Classic/Custom need configuration sets from the old Arctic/Baspa content and official materials.
2. Extend the configuration schema beyond flat `jets` and `pumps`:
   - configuration name,
   - marketing label,
   - pump count,
   - jet count total,
   - detailed jet rows,
   - detailed pump rows,
   - electrical notes,
   - configuration image,
   - availability per model/series.
3. Render the detailed rows in the product detail, similar in content depth to the old Cub page and Baspa product detail pages.
4. Seed/import the completed catalog into all products before client review.
5. Harden admin saving:
   - never delete existing configurations when the metabox payload is missing,
   - treat malformed payload as validation failure, not as "delete product configuration data",
   - preserve existing rows during quick edits, autosaves, revisions, and partial admin saves,
   - add a warning before saving a product with zero active configurations.
6. Add a product content smoke:
   - every standard product has at least the expected configuration count,
   - Cub specifically contains `Prestige 20/1`, `Signature 40/2`, `Legend 40/3`, `SDS 40/3`,
   - every configuration has at least one jet/pump detail row or an explicit "not applicable" reason.

### P0 - Product hero descriptions are deliberately cut mid-sentence

Observed:

- Local and production both cut many hero descriptions to exactly `26` words and append `...`.
- Cub is rendered as: `Dve kresla ... operkami rukou...` instead of a complete sentence.
- The audit found `12` products with trimmed hero summaries.

Relevant code:

- `wp-content/themes/arctic/modules/products/templates/post/single/heading.php:71` uses `wp_trim_words( strip_tags( $description ), 26, '...' )`.

Repair plan:

1. Stop trimming the product description blindly in the hero.
2. Add a dedicated `product_hero_summary` field if the design needs a shorter hero text.
3. Backfill complete, human-written summaries for all products.
4. Add a smoke check that hero copy does not end in `...` unless explicitly approved.

### P0 - Cabinet colors are missing almost everywhere

Observed:

- Local: `21 / 22` audited products have zero cabinet color options.
- Production: `21 / 22` audited products have zero cabinet color options.
- Timberwolf is the only audited page that currently shows two cabinet colors.
- Shell colors appear for most products because local/production data has at least that side filled or because local fallback masks gaps.

Relevant code:

- `wp-content/themes/arctic/modules/products/type/metabox.php:211` defines `product_shell_color_ids`.
- `wp-content/themes/arctic/modules/products/type/metabox.php:235` defines `product_cabinet_color_ids`.
- `wp-content/themes/arctic/modules/products/inc/colors.php:342` reads the explicit shell/cabinet color IDs.
- `wp-content/themes/arctic/modules/products/inc/colors.php:357` returns no legacy fallback when seed fallbacks are disabled.
- `wp-content/themes/arctic/modules/products/templates/post/single/acrylic-colors.php` changes the section title based on whether cabinet options exist.

Repair plan:

1. Create/verify the global cabinet color catalog:
   - cedar / standard cabinet options,
   - no-maintenance cabinet options,
   - AllClimate cabinet options where applicable.
2. Assign the correct cabinet color set to every product and product series.
3. Decide applicability for swimspa cabinet/finish options and render them consistently.
4. Add a production-mode smoke:
   - all hot tubs show shell and cabinet headings,
   - expected color counts match the catalog,
   - no product relies on seed fallback for colors.

### P0 - "Vyhody" and "Volitelna vybava" links are dead on production hot tub pages

Observed production:

- `15` hot tub product pages have navigation links to `#vyhody` and `#volitelna-vybava`.
- Those anchors do not exist because the sections do not render without admin cards in production mode.
- Cub on production has both nav links, but both targets are missing.

Observed local:

- Hot tub benefit/option sections exist only because local seed/static fallback is allowed.
- Cub local benefits source is `static-fallback`, not real product admin data.

Relevant code:

- `wp-content/themes/arctic/modules/products/templates/post/single/navigation.php:23` adds the two nav links for every hot tub.
- `wp-content/themes/arctic/templates/section/product-benefits.php:19` renders `#vyhody` only when cards exist.
- `wp-content/themes/arctic/templates/section/product-options.php:19` renders `#volitelna-vybava` only when cards exist.
- `wp-content/themes/arctic/modules/products/inc/benefit-sections.php:237` returns an empty card list when no admin rows exist and seed fallback is disabled.
- `wp-content/themes/arctic/modules/products/inc/benefit-sections.php:319` and `:350` use fragile `product_benefit_items` / `product_option_items` repeaters as the content source.

Repair plan:

1. Resolve product section data before building the product nav.
2. Add nav items only when their target section will really be rendered.
3. Do not rely on local static fallback as a release state.
4. Seed/import real benefit and optional-equipment data into production content.
5. Add a browser smoke that every product nav hash resolves to an existing visible section.

### P0 - Swimspa pages are missing benefits and optional equipment by template design

Observed:

- All six audited swimspa pages lack benefit and optional-equipment sections locally and on production.
- Their nav correctly does not show `Vyhody` or `Volitelna vybava`, but this only hides the problem; the content is still missing.

Relevant code:

- `wp-content/themes/arctic/single-product.php:38` renders product benefits/options only when `$is_hot_tub` is true.

Repair plan:

1. Render the feature/equipment system for swimspa products too.
2. Use swimspa-specific headings, copy, and applicability rules.
3. Include relevant All-Weather Pool features, not only hot tub-specific cards.
4. Add swimspa content smoke:
   - every swimspa has feature/equipment sections,
   - section headings do not say "virivky" where the product is a pool,
   - no swimspa page relies on empty fallback.

### P0 - Standard and optional equipment need a real catalog, not static fallback cards

Customer expectation:

- The old pages have a substantial list of standard and optional equipment.
- Items have short descriptions and often separate detail pages or "Zjistit vice" links.
- The corporate Arctic Spas features page presents a modern features surface with named feature blocks and links to deeper detail pages.

Current implementation:

- Benefits are generic cards, sometimes interactive.
- Optional equipment is static only; no per-item link/detail behavior is rendered.
- The fallback text includes translation/encoding issues and is not a trustworthy production content source.
- There is no central feature/equipment catalog with applicability per product, series, or configuration.

Repair plan:

1. Create a feature/equipment content model:
   - title,
   - short summary,
   - long description,
   - media,
   - standard/optional flag,
   - detail URL/page,
   - product/series/configuration applicability,
   - ordering and visibility state.
2. Backfill the catalog from:
   - old Arctic/Baspa product pages,
   - official Arctic Spas feature pages,
   - approved client/source material.
3. Render standard equipment and optional equipment as separate sections.
4. Support either detail modals or canonical feature detail pages.
5. Add links where a detail exists; do not show fake/dead "more" affordances.
6. Reuse the existing `template-features.php` / feature detail system if it is fit; otherwise extend it instead of duplicating data.
7. Add QA:
   - no equipment item is title-only,
   - no feature link returns `404`,
   - every product has the expected standard and optional equipment groups,
   - copy has no mojibake or machine-translation tone.

### P1 - Product photos and content quality need a source pass

Observed:

- Current product detail sections are structurally present on some local pages, but many are powered by fallback/static content.
- Customer complaint about unsuitable photos and machine-like text is consistent with fallback content and incomplete migration.
- Category product-card media issues are covered in the category addendum, but product details also need an image-source audit per hero/configuration/feature/equipment image.

Repair plan:

1. Build a product media matrix:
   - hero image,
   - gallery images,
   - configuration images,
   - shell colors,
   - cabinet colors,
   - feature/equipment media.
2. Mark every asset as one of:
   - approved official,
   - approved client/Baspa,
   - legacy temporary,
   - missing,
   - wrong crop.
3. Replace temporary/fallback imagery before client review.
4. Run screenshots for all product details at desktop and mobile.

### P0 - Mobile shell-color thumbnails must be fixed on production, not only locally

Observed:

- Customer screenshot shows the mobile `Barvy skorepiny` card grid visually broken/unstable.
- Local screenshot after the first CSS pass exists at `docs/screenshots/mobile-shell-colors-after-2026-06-08.png` and shows the intended stable two-column layout.
- The fix is therefore not complete until the same result is verified on production preview and final production.
- The relevant frontend component is `f-product-colors`:
  - markup: `wp-content/themes/arctic/modules/products/templates/post/single/acrylic-colors.php`,
  - desktop/source styling: `wp-content/themes/arctic/src/less/_components.less:5288`,
  - mobile guard: `wp-content/themes/arctic/src/less/_components.less:12894`,
  - compiled CSS must also be deployed, not just source LESS.

Risk:

- This is a classic local/prod drift issue. If production receives PHP/data but not the rebuilt CSS, mobile product pages still show broken shell previews.
- If product color media are changed in admin without visual guardrails, odd aspect ratios can break the circular preview again.

Repair plan:

1. Keep the local mobile guard for `f-product-colors` as the source of truth:
   - two stable columns on `390/393px`,
   - no horizontal overflow,
   - equal card width,
   - circular image/placeholder centered inside the grey circular backdrop,
   - labels remain readable, including `Platinum Swirl`.
2. Rebuild CSS and deploy the compiled asset to production.
3. Verify on both:
   - local `http://localhost:8090/product/timberwolf/#barvy`,
   - production preview/final production equivalent product detail.
4. Add or extend a smoke/screenshot check:
   - mobile viewport `390 x 844`,
   - `#barvy` section present,
   - every visible color card has image/placeholder within card bounds,
   - no card overlaps another card,
   - no horizontal document overflow.
5. Include cabinet colors in the same test once cabinet data is complete.
6. Do not mark the product-detail repair done until the screenshot exists for production too.

### Product repair order

1. Freeze and back up production product data before any admin/import changes.
2. Build the product content matrix:
   - expected configurations,
   - shell colors,
   - cabinet colors,
   - standard equipment,
   - optional equipment,
   - feature detail links,
   - image sources.
3. Fix the admin save safety issues first, especially configuration deletion and missing repeater preservation.
4. Extend the configuration schema and import correct model configurations.
5. Fix hero summary handling and backfill complete summaries.
6. Create/assign cabinet colors for all relevant products.
7. Verify and deploy the mobile shell/cabinet color thumbnail layout to production.
8. Build the feature/equipment catalog and render it for hot tubs and swimspa.
9. Make product navigation data-driven so links cannot point to missing sections.
10. Re-run:
   - `node tools/product-customer-audit.js`
   - `npm run product:smoke`
   - `npm run product-media:smoke`
   - `npm run product-detail:physical`
   - link smoke for all feature/equipment details.
11. Production recheck:
   - all 22 product details return `200`,
   - no product detail page exceeds the browser timeout,
   - every nav hash resolves,
   - Cub matches the four expected configurations from the old reference,
   - mobile `#barvy` shell previews match local fixed screenshot and do not overflow.

## Support, references, about, showroom, contact, and menu addendum - 2026-06-08

Scope: new customer comments for support/FAQ placement, references, about page, showroom, contact map, and "Dalsi informace" menu, local `http://localhost:8090/`, production `https://illuminatus.cz/`, current templates, WP menu data, and Baspa reference archive:

- `https://baspa.cz/reference/?`

Additional evidence:

- Browser audit output: `docs/screenshots/site-customer-audit-2026-06-08/audit.json`
- Audit helper: `tools/site-customer-audit.js`
- Screenshots:
  - `docs/screenshots/site-customer-audit-2026-06-08/local-maintenance-desktop.png`
  - `docs/screenshots/site-customer-audit-2026-06-08/prod-maintenance-desktop.png`
  - `docs/screenshots/site-customer-audit-2026-06-08/local-support-desktop.png`
  - `docs/screenshots/site-customer-audit-2026-06-08/prod-support-desktop.png`
  - `docs/screenshots/site-customer-audit-2026-06-08/local-references-desktop.png`
  - `docs/screenshots/site-customer-audit-2026-06-08/prod-references-desktop.png`
  - `docs/screenshots/site-customer-audit-2026-06-08/local-showroom-desktop.png`
  - `docs/screenshots/site-customer-audit-2026-06-08/prod-showroom-desktop.png`
  - `docs/screenshots/site-customer-audit-2026-06-08/local-contact-desktop.png`
  - `docs/screenshots/site-customer-audit-2026-06-08/prod-contact-desktop.png`

Audit summary:

- Local and production: all six audited pages return `200`.
- Local and production: main "Dalsi informace" menu has no inquiry/form link.
- Local and production: main "Dalsi informace" menu still contains standalone `Kolik stoji provoz a udrzba`.
- Local and production: `/reference/` has `9` reference cards; all `9` link directly to image/lightbox; `0` cards expose a useful description.
- Local and production: `/showroom/` has no embedded map section.
- Local and production: showroom "Fotogalerie" button points to `#fotogalerie`; the target exists, but it is a text/image split with one image and `0` gallery/lightbox cards.
- Local and production: `/kontakt/` map uses `customizer-map-embed` with `https://www.google.com/maps?q=49.149%2C16.589&ll=49.149%2C16.407&z=11&output=embed` and a dark overlay.
- Local and production: `/o-nas/` still contains year/age signals `2005`, `15 let`, and stats such as `21+`, `1000+`, `11`.

### P0 - "Kolik stoji provoz a udrzba" is still a standalone page and menu item

Observed:

- `/kolik-stoji-udrzba/` is a standalone published page with template `template-maintenance.php`.
- Header menu contains `Kolik stoji provoz a udrzba` under `Dalsi informace`.
- `/podpora/` has a FAQ card `Jak narocna je bezna udrzba vody?`, but the full standalone maintenance/provoz content is not migrated into FAQ structure.

Relevant code/data:

- `wp-content/themes/arctic/template-maintenance.php` renders the page as a full article from WP editor content.
- `wp-content/themes/arctic/template-support.php` renders FAQ from `faq` CPT.
- Local WP menu `Arctic hlavni navigace` contains `Kolik stoji provoz a udrzba` at position `24`.

Repair plan:

1. Move the maintenance/provoz content into `/podpora/#caste-dotazy`.
2. Split the long article into proper FAQ entries under a category such as `Provoz a udrzba`.
3. Remove `Kolik stoji provoz a udrzba` from the main "Dalsi informace" menu.
4. Decide SEO behavior:
   - either redirect `/kolik-stoji-udrzba/` to `/podpora/#caste-dotazy`,
   - or keep the URL as a canonical support FAQ landing only if SEO requires it.
5. Update sitemap/link smoke so the old standalone page is not promoted in main navigation.

### P0 - References archive is image-only instead of a content archive

Observed:

- `/reference/` renders `9` reference cards on local and production.
- All cards are `<a class="f-reference-card--lightbox js-image">`.
- Card `href` points directly to the image file, for example `/wp-content/uploads/2026/05/arctic-fox-lidi.jpg`.
- The template reads reference title, location, year and image, but not excerpt/content.
- Clicking a card opens the image/lightbox only, so admin text has no frontend destination.

Baspa reference page expectation:

- The Baspa archive shows cards with an image, category/product label, descriptive paragraph, and linked title/location-year entries.
- Examples on the Baspa page include recent reference cards with product type text, a paragraph description, and linked location/year heading.

Relevant code:

- `wp-content/themes/arctic/template-references.php:55` reads `get_the_title()`.
- `wp-content/themes/arctic/template-references.php:59` reads location/year meta.
- `wp-content/themes/arctic/template-references.php:97` renders a lightbox card.
- `wp-content/themes/arctic/template-references.php:98` sets `href` to `$reference['image']`.
- `wp-content/themes/arctic/template-references.php:99` adds PhotoSwipe dimensions.

Repair plan:

1. Redesign `/reference/` as a content archive, not a pure image gallery.
2. Use `reference` CPT content/excerpt in the card.
3. Add reference category/product labels.
4. Make the card title link to a real reference detail page or to an expanded detail modal with text.
5. Keep image lightbox only inside the detail/gallery context, not as the primary card click.
6. Add `single-reference.php` if the expected UX is detail pages.
7. Add browser tests:
   - every reference card has description text,
   - primary card link is not an image file,
   - opening a reference exposes title, year/location, text, and gallery.

### P1 - "O nas" content is stale

Observed:

- `/o-nas/` contains old time signals:
  - `Prodejem virivek Arctic Spas se zabyvame jiz od roku 2005`
  - `vice nez 15 let osobnich zkusenosti`
  - stats `21+`, `1000+`, `11`
- Customer says the page is more than a year out of date. The audit cannot validate the correct replacement facts, but it confirms the current page uses old fixed copy/stats.

Relevant code/data:

- `wp-content/themes/arctic/template-about.php` uses WP editor content plus `about_stats` meta.
- Fallback copy in `wp-content/themes/arctic/template-about.php:252` also contains `2005` and `15 let`, so local fallback can preserve stale text even when admin content is incomplete.
- Local page meta has `about_stats` values `21+`, `1000+`, `11`.
- Current Baspa `O nas` public source is newer and currently says:
  - BASPA s.r.o. founded in `2013`,
  - owner activities in the field started in `2003`,
  - stats `23+` years, `1200+` clients, `13` team members,
  - current team/member list and partner profile are available there.

Repair plan:

1. Use current Baspa `O nas` and admin/member data as the first draft source for company facts, stats, and team structure.
2. Mark all time-sensitive facts as `client-verify` before delivery:
   - years of experience,
   - number of clients/installations,
   - team count,
   - current staff and roles,
   - supplier/partner list.
3. Keep the Arctic site profile focused on BASPA as Arctic Spas dealer; do not bulk-copy unrelated generic pool supplier copy unless approved.
4. Update WP editor content and `about_stats`.
5. Verify `member` CPT/team cards match current staff.
6. Remove stale fallback copy or make it generic enough that it cannot reintroduce obsolete facts.
7. Add a content freshness checklist for owner-provided facts:
   - company intro,
   - team count,
   - number of installations/clients,
   - phone/email/personnel,
   - year references.

### P0 - Showroom page has no embedded map and the gallery CTA is not a real gallery

Observed:

- `/showroom/` has a showroom info item with an external "Zobrazit na mape" link.
- It does not render `.f-section--map`, `.f-local-map`, or an embedded map iframe.
- The hero button `Fotogalerie` links to `#fotogalerie`.
- The `#fotogalerie` target exists, but it is the first text/image split section, not a gallery.
- The audit found one image in that target and `0` gallery/lightbox cards.

Relevant code:

- `wp-content/themes/arctic/template-showroom.php:57` reads `showroom_gallery_images`.
- `wp-content/themes/arctic/template-showroom.php:181` renders the hero gallery button as `href="#fotogalerie"`.
- `wp-content/themes/arctic/template-showroom.php:220` renders only an external map link.
- `wp-content/themes/arctic/template-showroom.php` does not include `templates/section/map.php`.

Repair plan:

1. Add an actual showroom map section or reusable map component to `/showroom/`.
2. Use the same corrected map source as the contact page.
3. Replace the `#fotogalerie` target with a real gallery section:
   - gallery grid,
   - lightbox,
   - all `showroom_gallery_images`,
   - meaningful alt/labels.
4. Keep the hero button, but point it to the real gallery component.
5. Add showroom smoke:
   - map component exists,
   - gallery button target exists,
   - gallery contains at least the expected image count,
   - gallery cards open images/details.

### P0 - Contact map is too dark and uses a shifted map viewport

Observed:

- `/kontakt/` uses `customizer-map-embed`.
- Embed URL is `q=49.149,16.589&ll=49.149,16.407&z=11&output=embed`.
- The `q` marker and `ll` viewport center are different.
- CSS applies a dark overlay and grayscale/brightness filters.
- Customer reports the map is unclear and the location points toward `Cerna Pole`.

Relevant code:

- `wp-content/themes/arctic/inc/functions/location.php:28` defines the default embed with `q=49.149,16.589` and shifted `ll=49.149,16.407`.
- `wp-content/themes/arctic/templates/section/map.php:7` uses `arctic_get_map_embed_url()`.
- `wp-content/themes/arctic/src/less/_components.less:6460` defines `.f-local-map:before`.
- `wp-content/themes/arctic/src/less/_components.less:6482` filters the iframe.
- `wp-content/themes/arctic/src/less/_components.less:11497` applies a stronger contact-page overlay.
- `wp-content/themes/arctic/src/less/_components.less:11510` applies contact-page iframe filter.
- Existing `tools/contact-map-smoke.js` currently expects the shifted viewport and dark map treatment, so the smoke contract itself must be updated.

Repair plan:

1. Replace the embed with an exact Google Place/address embed for `Bohunicka cesta 727/15, 664 48 Moravany` or an approved BASPA Google Maps place URL.
2. Keep marker and map center aligned unless there is a deliberate layout reason approved by the client.
3. Remove or substantially lighten the dark overlay.
4. Reduce grayscale/brightness filters so the map remains legible.
5. Verify the external map CTA opens the same approved location.
6. Update `tools/contact-map-smoke.js` to assert clarity and correct location instead of the old shifted Figma viewport.
7. Capture desktop/mobile screenshots after the fix.

### P1 - "Dalsi informace" menu is missing inquiry form link

Observed:

- Local and production main menu do not contain a poptavka/form link under `Dalsi informace`.
- Menu currently has `Servis` and `Kontakt`, but no `Poptavkovy formular`.
- Existing possible targets:
  - general contact form: `/kontakt/#formular`,
  - configurator inquiry handoff: `/poptavka-konfigurace/`.

Repair plan:

1. Confirm intended target with BASPA:
   - if this is a general inquiry form, use `/kontakt/#formular`;
   - if this is configuration-specific, use `/poptavka-konfigurace/`.
2. Add `Poptavkovy formular` to the WP main menu under `Dalsi informace`, positioned directly above `Servis`.
3. Add the same item to footer/fallback menu if the footer IA should mirror the header.
4. Add menu smoke:
   - `Dalsi informace` contains `Poptavkovy formular`,
   - it appears above `Servis`,
   - target returns `200` and scrolls/renders the expected form.

### Support/reference/showroom/contact repair order

1. Menu IA:
   - remove standalone `Kolik stoji provoz a udrzba` from `Dalsi informace`,
   - add `Poptavkovy formular` above `Servis`.
2. Support FAQ migration:
   - split the long maintenance article into FAQ CPT entries,
   - route/redirect old URL as approved.
3. References:
   - change archive cards from image lightbox to content cards,
   - add detail pages or detail modal,
   - render admin text and gallery.
4. Showroom:
   - add embedded map,
   - build a real gallery section for the hero gallery CTA.
5. Contact map:
   - replace shifted embed,
   - lighten/remove overlay and filters,
   - update map smoke expectations.
6. About:
   - replace stale company/team/stats content with current owner-approved data.
7. Re-run:
   - `node tools/site-customer-audit.js`,
   - `npm run contact-map:smoke`,
   - `npm run about:smoke`,
   - `npm run showroom:smoke`,
   - `npm run link:smoke`.

## Pricing, catalog, Ecomail, and offers addendum - 2026-06-08

### Customer feedback covered

- There is no clear link to a price list or a way to learn prices.
- Every series/model should show an indicative price.
- The user should be able to request/download a catalog/price list after entering an email address.
- The captured email must be transferred to Ecomail on the final domain.
- The UI for the catalog/price-list request must already be visible before final-domain Ecomail activation.
- `Vyprodej skladovych virivek` must become `Akcni nabidky`.
- The offer area must be an independently editable page/workflow where admins can prepare four offer types, with only the published one visible on the website.
- Client-side edits to texts, contacts, and phones are acceptable only after admin saves stop breaking unrelated page sections.

### Baspa reference model

Baspa does not treat price discovery as one hidden page. It uses three layers:

1. Product/category hero and variant cards show indicative prices, for example `Virivky Arctic Spas` shows `od 209.000 Kc vc. instalace` in the hero and prices on the Core/Classic/Custom series cards.
   Source: `https://baspa.cz/produkt/virivky-arctic-spas/`.
2. Product and series pages include a repeated sales block `Kompletni katalog s cenikem produktu` with an email-only form, consent note, and catalog image.
   Sources: `https://baspa.cz/produkt/virivky-arctic-spas/`, `https://baspa.cz/produkt/virivky-arctic-spas/serie-arctic-spas-custom/`.
3. Support explains where prices are available and the Downloads section has a dedicated `Ceniky` category. For Arctic Spas, Baspa intentionally says the hot-tub/swimspa price list is sent electronically or by post after contact/form request.
   Source: `https://baspa.cz/podpora/`.

This is the right functional reference. The new Arctic site should not copy Baspa visuals blindly, but it needs the same conversion mechanics: visible price clues, a repeated catalog/price-list lead capture, and a clear FAQ/download fallback.

### Figma status

- Figma file keys are documented in `docs/web-finalization-master-plan-2026-05-26.md`:
  - wireframe: `puPBNFpuaXpRZR2TINaDvm`,
  - grafika: `xeOew3dFjDVfjXZrJ09emM`.
- The Figma raw dumps contain `Katalog a cenova nabidka` and explanatory copy about a printed catalog/price list at the showroom.
  Evidence: `docs/wireframe-missing-pages.raw.json:29950` and `docs/wireframe-missing-pages.raw.json:30362`.
- I did not find a Figma-defined Baspa-style email capture block with headline `Kompletni katalog s cenikem produktu`.
- Conclusion: this is a required design extension. Use the Arctic visual language/components, but add a new reusable catalog/price-list CTA section because Figma currently covers the topic only as informational copy.

### Local/production audit evidence

New audit script:

- `tools/pricing-catalog-audit.js`
- Output: `docs/screenshots/pricing-catalog-audit-2026-06-08/audit.json`

Audited on local `http://localhost:8090` and production preview `https://illuminatus.cz`.

Findings are the same on local and production preview:

- 9 audited pages returned OK; direct old offer detail `/offer/vyprodej-skladovych-virivek/` returns `404`.
- Baspa-style catalog/price-list sales block is missing from home, category, support, and tested product pages.
- Catalog form exists only on `/ke-stazeni/`; audit found two catalog-like forms there, likely normal page/off-canvas or repeated rendered form state.
- No menu-level catalog/price-list CTA was found.
- No inquiry CTA was found in the audited menu link sample.
- Header/menu contains `Akcni nabidky`, but old stock-sale wording still appears in promo/footer:
  - `Vyprodej skladovych virivek Zobrazit nabidku`,
  - `Skladove virivky`.
- `/product/cub/` and `/product/ocean/` have no visible price in the audited product view.
- `/product/timberwolf/` has a visible indicative price.
- `/virivky/` has visible prices; `/swimspa/` did not expose a visible price in the audited first pass.

### Existing implementation pieces

The good news: some technical building blocks already exist.

- Catalog form exists in `wp-content/themes/arctic/modules/contacts/templates/form-catalog.php`.
  - It captures email only.
  - It posts as hidden form type `catalog`.
  - It includes a privacy note.
- Contact processing already tags catalog submissions for Ecomail in `wp-content/themes/arctic/modules/contacts/templates/form/processing.php:273`.
- Ecomail integration already exists in `wp-content/themes/arctic/modules/contacts/inc/ecomail.php`.
  - It is disabled in local environment.
  - It requires `baspa_ecomail_api_key` and `baspa_ecomail_list_id`.
  - Local theme mods are empty, which is correct for localhost but must be completed before production.
- Product price fields already exist in `wp-content/themes/arctic/modules/products/type/metabox.php:71`.
  - `product_price`,
  - `product_price_text`,
  - `product_price_suffix`.
- Listing price rendering exists in `wp-content/themes/arctic/modules/products/templates/post/common/price.php`.
- Sticky product navigation currently reads only `product_price_text` in `wp-content/themes/arctic/modules/products/templates/post/single/navigation.php:8`; this can hide a numeric `product_price` if no text field is filled.
- Configuration cards can show price text in `wp-content/themes/arctic/modules/products/templates/post/single/configurations.php:69`.
- Offer CPT exists and already has type fields in `wp-content/themes/arctic/modules/offers/type/metabox.php`.
  - Types are `spring`, `summer`, `autumn`, `winter`.
  - Fields exist for status, discount, price, original price, validity, button, contact, featured flag, and promo image.
- Offer archive helper correctly prefers `/akcni-nabidky/`, but fallback promo text still says `Vyprodej skladovych virivek` in `wp-content/themes/arctic/modules/offers/inc/offer.php:197`.
- WP data currently has only one offer post:
  - slug `vyprodej-skladovych-virivek`,
  - title `Vyprodej skladovych virivek`,
  - status `publish`.
- Footer menu still contains `Skladove virivky` pointing to `/akcni-nabidky/`.

### P0 - Price discovery is not complete enough for launch

Impact:

- A buyer cannot reliably answer "how much does this start at?" from product pages.
- A lead cannot reliably request the price list from the product decision point.
- The current state loses one of the highest-intent conversion moments.

Repair plan:

1. Define a price policy with the client:
   - model price exact vs `od ... Kc`,
   - series price range,
   - whether price includes assembly/installation,
   - what text to show when pricing is intentionally withheld.
2. Fill `product_price` or `product_price_text` for every product and every series card.
3. Ensure sticky product navigation can render both numeric `product_price` and text `product_price_text`.
4. Add product/category smoke:
   - every published product has visible price or an explicit approved fallback,
   - every series section has visible starting price/range,
   - swimspa category is included.
5. Do not use silent empty price output. If price is missing, production QA should fail.

### P0 - Catalog/price-list CTA must be reusable and present at buying points

Repair plan:

1. Build a reusable Arctic catalog/price-list CTA section from existing `form-catalog.php`.
2. Add admin controls for:
   - heading,
   - short text,
   - button label,
   - catalog/price-list image,
   - optional product/series context hidden field,
   - enable/disable per template.
3. Place it at least on:
   - homepage after product entry/category decision area or before contact CTA,
   - `/virivky/`,
   - `/swimspa/`,
   - every product detail after intro/configuration and before contact CTA,
   - `/ke-stazeni/`,
   - `/podpora/` FAQ/download area.
4. Use the same form type `catalog`, but tag Ecomail source with page/product/series context.
5. On localhost/staging:
   - render the same UI,
   - do not call Ecomail,
   - send/store/log through the existing local-safe form path.
6. On production:
   - configure Ecomail API key/list ID,
   - verify subscriber tag/source,
   - verify autoresponder sends the catalog/price-list link.
7. Add form smoke:
   - page renders form,
   - email field required,
   - privacy link present,
   - local submission does not call Ecomail,
   - production config presence can be checked without exposing secrets.

### P0 - Ecomail handoff needs a production contract, not just code

Current code is acceptable as a base, but the contract is unfinished.

Repair plan:

1. Add a documented production checklist:
   - Ecomail list ID,
   - API key configured only in production/staging secrets,
   - tag names: `Catalog`, product slug, series/category,
   - autoresponder/template name,
   - double opt-in/consent decision,
   - privacy text approved.
2. Add a non-secret admin status indicator:
   - "Ecomail configured: yes/no",
   - no API key value displayed.
3. Add a manual production smoke after domain switch:
   - submit test email,
   - confirm Ecomail subscriber,
   - confirm autoresponder,
   - confirm WP email recipient path.

### P0 - Offers must be renamed and made publication-safe

Current state:

- Main nav has `Akcni nabidky`.
- Footer still says `Skladove virivky`.
- Promo fallback still says `Vyprodej skladovych virivek`.
- Only one offer post exists, and it is the old stock-sale title/slug.
- Old detail URL returns `404` in browser audit, so permalink/rewrite handling also needs verification.

Repair plan:

1. Rename public labels:
   - footer `Skladove virivky` -> `Akcni nabidky`,
   - offer post title/short title -> approved campaign name or generic `Akcni nabidky`,
   - fallback promo text -> `Akcni nabidky`.
2. Decide URL model:
   - primary archive `/akcni-nabidky/`,
   - either no public detail page, or stable detail URLs under `/akcni-nabidky/{slug}/`,
   - redirect old `/offer/vyprodej-skladovych-virivek/` if it was ever public.
3. Keep four editable offer types:
   - spring,
   - summer,
   - autumn,
   - winter.
4. Visibility rule:
   - admins can prepare all four as draft/private,
   - only `publish` offers render publicly,
   - optionally only one `featured` offer may power homepage/mega-menu promo.
5. Add admin help text that explains "only published offers are visible".
6. Add smoke:
   - archive renders only published offers,
   - private/draft offers do not appear in REST/front,
   - homepage/mega menu uses published featured offer or generic `Akcni nabidky`,
   - no visible `Vyprodej skladovych virivek` or `Skladove virivky` remains.

### P1 - Figma/design extension needed

Because Figma does not currently define the exact email-gated price-list block, the design repair should be explicit:

1. Create one reusable CTA component matching Arctic spacing, radius, typography, buttons, and responsive behavior.
2. Use a real/generated catalog mockup image only if client approves it; otherwise use a neutral document/catalog visual from provided assets.
3. Avoid a fake "download now" promise unless the autoresponder actually sends a link.
4. Verify mobile layout: email input, button, privacy text, and catalog visual must not overlap or shrink text.

### Pricing/catalog/offers repair order

1. Data policy:
   - approve price display rules,
   - approve catalog/price-list email flow,
   - approve offer URL/naming rules.
2. Admin/data:
   - complete product and series price fields,
   - seed four offer types as draft/private templates,
   - keep only intended offer published.
3. Frontend:
   - add reusable catalog/price-list CTA,
   - place it on product/category/support/download pages,
   - repair price rendering fallback,
   - rename offers across footer/promo/archive.
4. Integrations:
   - keep local Ecomail disabled,
   - configure final-domain Ecomail,
   - add non-secret status checks.
5. QA:
   - `node tools/pricing-catalog-audit.js`,
   - `npm run form:smoke`,
   - `npm run link:smoke`,
   - product content smoke for prices/configurations,
   - manual test email on final domain.

## Escalation, admin data, fallbacks, and performance addendum - 2026-06-08

The cover email does not add a new product URL, Figma frame, database field, or media asset. It does add a hard acceptance constraint: the client will re-check the provisional site on Monday, June 15, 2026 and decide whether to accept or reject the work.

This changes the delivery standard. A page that only looks acceptable locally is not enough. The site must be content-complete enough for client review, editable through wp-admin, and free of production-visible seed/Figma fallback content that hides missing data.

### Useful data extracted from the email

- Final client review date: Monday, June 15, 2026.
- Client risk is no longer only visual quality; it is trust in delivery and admin stability.
- Missing content and fallback content are both release blockers.
- A simple admin text edit already caused important homepage blocks to disappear, so admin save handlers are a P0 delivery risk.
- Photo replacement and text review are still expected, but they must happen on top of a stable admin data model.
- The site is based on the existing Baspa environment. Preserve working Baspa patterns and avoid replacing proven form/contact/download/member/menu behavior unless the current implementation is demonstrably broken.

### Acceptance gates before the June 15 review

1. No production-visible seed/Figma fallback content:
   - homepage slides,
   - homepage services/benefits/progress,
   - category intros/heroes,
   - product configurations,
   - shell/cabinet colors,
   - benefits/optional equipment,
   - references,
   - showroom/contact/member media,
   - offer promos.
2. Every public block must have a real admin source or an explicit approved empty state:
   - slide CPT,
   - homepage page meta,
   - term meta,
   - product meta/repeaters,
   - product-series term/archive data,
   - offer CPT,
   - FAQ/download/reference/member CPTs,
   - theme mods/options for global contacts/forms/maps.
3. Admin save hardening:
   - saving one text field must not delete unrelated repeater rows,
   - missing POST keys from collapsed/hidden metaboxes must preserve existing values,
   - media IDs must not be replaced by empty strings unless the admin explicitly removes them,
   - repeaters need stable row keys or a safe merge strategy,
   - production smoke must simulate at least one simple text edit and assert that homepage/product blocks still render.
4. Product data cannot be "fallback-first":
   - all models need the correct count of configurations,
   - all available configurations need editable names/specs/images,
   - shell and cabinet colors must be assigned through admin data,
   - standard and optional equipment need reusable editable catalog items,
   - feature/equipment links must point to real detail pages or approved anchors.
5. Content migration must be real enough for review:
   - use current Baspa content as a functional baseline where appropriate,
   - use Arctic corporate content only as a reference for structure/quality, not as unreviewed machine-like copy,
   - mark every remaining placeholder visibly in the internal audit, not on the public site.

### Baspa environment parity

Baspa local has two relevant production-support plugins that Arctic local does not currently use:

- `ewww-image-optimizer` - EWWW Image Optimizer 8.2.1.
- `powered-cache` - Powered Cache 3.6.3.

Arctic theme currently registers responsive image sizes in `wp-content/themes/arctic/inc/images.php` and uses the Forqy image helpers for lazy/srcset/preload behavior. That is useful, but it is not the same as compression, WebP/AVIF generation, cache headers, or page cache.

Repair plan:

1. Decide whether Arctic production should use the same Baspa plugin stack or an equivalent host-level optimization stack.
2. If yes, install/configure the image optimizer and cache plugin on staging/production before the final performance pass.
3. Regenerate thumbnails after product/category/hero media is corrected.
4. Compress first-slide and below-fold slide media; do not solve slow first load only by hiding slides on mobile.
5. Add a transfer-size/performance audit for:
   - homepage first load,
   - mobile slider,
   - category heroes,
   - product hero/configuration media.

### Verified/fixed during this addendum

- `npm run admin-fallback:smoke` initially caught that homepage fallback slides were not gated strongly enough.
- `wp-content/themes/arctic/modules/slides/templates/section.php` now only builds static homepage fallback slides when `arctic_allow_seed_fallbacks()` allows local/development seed content.
- The local admin fallback smoke checks this homepage fallback gate; the broader smoke file still depends on other uncommitted admin/fallback hardening work and should be committed with that work, not alone.
- `npm run admin-fallback:smoke` passed after the fix.
- `npm run asset:smoke` passed after the fix.

## Recommended repair order

1. June 15 acceptance gate:
   - freeze the provisional review scope,
   - list every production page that will be checked,
   - define "accepted empty state" vs "missing content",
   - run fallback/admin/performance smokes before publishing changes,
   - verify production does not rely on local-only seed content.
2. Production backup and data repair:
   - homepage benefits/progress meta,
   - first slide link/CTA,
   - footer menu item,
   - copyright,
   - current product configuration/color/benefit/option/price data export,
   - current offer/menu/Ecomail theme-mod export without exposing secrets.
3. Admin hardening before content import:
   - protect homepage repeaters during saves,
   - protect product configurations during saves,
   - protect product benefit/option repeaters during saves,
   - protect product price fields and offer fields during saves,
   - protect contact/member/theme-mod phone, email, and global text fields during saves,
   - protect catalog form/Ecomail settings from accidental empty saves,
   - add production-content smoke gates so a simple text edit cannot silently remove whole sections.
4. Product content model repair:
   - complete configuration catalog for all models,
   - complete product and series indicative prices,
   - shell and cabinet color assignments,
   - mobile shell/cabinet thumbnail layout deployed and verified on production,
   - standard equipment catalog,
   - optional equipment catalog,
   - feature/equipment detail links,
   - swimspa feature/equipment sections.
5. Pricing/catalog/Ecomail/offers repair:
   - add reusable catalog/price-list CTA,
   - place it on home/category/product/support/download decision points,
   - configure production Ecomail handoff,
   - rename `Vyprodej skladovych virivek`/`Skladove virivky` to `Akcni nabidky`,
   - seed four offer types and render only published offers.
6. Baspa parity and performance repair:
   - preserve working Baspa admin/module patterns,
   - decide/install equivalent image optimizer and cache stack,
   - regenerate thumbnails,
   - optimize hero/category/product media,
   - add transfer budget gates for first load.
7. Support/reference/showroom/contact IA repair:
   - move maintenance/provoz content into support FAQ,
   - rebuild references as content cards/details,
   - update stale about copy/stats/team data,
   - add showroom map and real gallery,
   - correct/lighten the contact map,
   - add `Poptavkovy formular` above `Servis` in `Dalsi informace`.
8. Mobile slider CSS repair:
   - remove first-slide-only lock,
   - restore Swiper transform,
   - restore pagination,
   - remove/soften overlay,
   - stop hiding real slide images.
9. CTA/proklik repair:
   - unhide homepage caption footer,
   - add link defaults or production slide meta,
   - verify tap/keyboard behavior.
10. Category and series repair:
   - fix clipped category hero CTAs,
   - normalize product-card media,
   - build real `product-series` summary pages,
   - add Baspa-compatible redirects.
11. Final visual QA:
   - local and production,
   - mobile `390/393`,
   - mobile product `#barvy` screenshot on local and production,
   - desktop `1440/1920`,
   - compare against Figma exports/live Figma if file key is available.

## Immediate risk

Do not deploy only CSS fixes and call this done. The production homepage content data is already damaged or incomplete, product pages currently depend on missing or fallback-only product data, price discovery is incomplete, catalog/price-list capture is not present at buying points, mobile shell-color thumbnails still need production verification, and key information pages expose wrong or incomplete content flows. The escalation email makes the June 15 review a hard business deadline, so fallback masking and fragile admin saves are now acceptance blockers. Visual fixes alone will not bring back missing services, progress bullets, configurations, prices, cabinet colors, benefits, optional equipment, reference descriptions, showroom map/gallery, a correct contact map, or a working Ecomail-backed catalog flow.
