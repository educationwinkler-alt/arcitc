# Duplicate Baspa/Arctic logic audit - 2026-06-08

## Rule

Baspa remains the functional source where it already has a working module, CPT, form, query, admin screen, or data processing pipeline. Arctic-specific code may be a visual skin over that source, but it must not create a second business workflow for the same thing.

Allowed:

- Arctic template/CSS skin over one Baspa data source.
- Helper that normalizes existing Baspa data for multiple Arctic skins.
- Explicit fallback only as an audited empty state, never as silent production content.

Not allowed:

- A second query/render path for the same public module without a single owner.
- A second admin setting or theme_mod when a CPT/meta/form already owns the content.
- Static Figma/seed fallback content that looks like real production data.
- Local-only or code-only repairs that leave production DB/menu/customizer state behind.

## Findings

| Area | Existing Baspa source | Arctic/custom path found | Verdict | Required action |
| --- | --- | --- | --- | --- |
| Offers promo on homepage | `offer` CPT, `modules/offers/templates/section-small.php`, `modules/offers/templates/post/listing-small.php` | `templates/section/hero-promo.php` rendered inside slider, while homepage also called the old small offers section before commit `5cfd187`. | Confirmed duplicate. This caused two offer promos on production. | Done for homepage: removed `section-small` from `template-homepage.php`, deployed production, added `tools/homepage-offer-promo-contract-smoke.js`. Keep guard permanently. |
| Offers promo data | `offer` CPT meta: `offer_featured`, `offer_title_short`, `offer_promo_image_id`, `offer_button_*` | `baspa_offers_promo_data()` plus `hero-promo.php` and `mega.php` skins. Some fallback text/theme_mods still exist. | Acceptable only as one normalized source feeding multiple skins. Risk remains if theme_mod fallback is treated as real production content. | Make `baspa_offers_promo_data()` the only promo source. Production smoke must fail if promo uses `theme-mod`/`computed-fallback` while an offer CPT is expected. |
| Offers archive/card | Existing offer loop/listing templates: `modules/offers/templates/section.php`, `loop.php`, `post/listing.php` | New `template-offers.php` and untracked `modules/offers/templates/post/card.php`. | Presentation duplication. It can be OK if the archive card is only an Arctic skin over the same `offer` CPT and `baspa_offer_card_data()`. | Do not create a second offer archive logic. Either retire old archive/listing usage for public offers or make both render from the same card-data helper and one query helper. |
| Footer/menu offers label | WP menu + footer fallback | `apply-footer-offers-copyright-2026-06-08.php`, footer fallback, mega fallback. | Data plus code repair, not a pure duplicate, but it exposed deploy/data split risk. | Keep as migration ledger pattern. Any offer/menu label change must include code files, production DB step, and smoke. |
| Catalog/price-list request | Existing contact module: `[formular-katalog]`, `form-catalog.php`, contact processing, Ecomail helper | `modules/contacts/templates/section-catalog.php` hardcodes the banner title/text/image and is inserted on homepage/category/product pages. | Partial duplicate. It correctly reuses the Baspa catalog form, but the banner itself is code-owned instead of admin-owned. | Convert banner title/text/image/placement to admin settings or page/block source. Keep `form-catalog.php` and Ecomail pipeline as the only form workflow. |
| Catalog vs downloads/support pricelist | `download`/`support` modules include download/pricelist concepts; support categories have `display_pricelist` flags. | Catalog request banner says "cenik" but does not connect to actual pricelist/download availability. | Product/business duplication risk. A request banner is not the same as the price-list/download source. | Define one price-list contract: either request-only via catalog form, or downloadable documents via downloads/supports, with explicit Ecomail capture before download if required. |
| Contact forms | Baspa contact module: `form-contact.php`, `form-service.php`, `form-catalog.php`, shared `form/processing.php`, `contact` CPT. | Jucra inquiry reuses `form-contact.php` with context `jucra`; service-request template also renders service form. | Mostly acceptable extension, not a separate form engine. Risk: multiple entry pages/modals for service/contact can diverge in copy and hidden fields. | Keep one processing pipeline. Add smoke that contact/service/catalog/jucra submissions all create `contact` CPT rows with the expected `contact_form` value and Ecomail intent. |
| Quick contact cards | Members module: `member` CPT, `baspa_members_get_selected_contact()`, member settings. | `templates/component/quick-contact-card.php`, footer quick contact, product/sidebar/support/offer variants. | Acceptable Arctic skin if it never owns separate person/contact data. | Keep all cards sourced from members module/options. Smoke should fail on hardcoded names/phones except approved empty fallback. |
| Contact directory | Members module already has member CPT/query helpers. | `templates/section/contact-directory.php` custom layout on contact page. | Acceptable presentation skin over member CPT. | Keep data source as `baspa_members_query_contacts()`. Do not add parallel contact-person settings outside members. |
| Contact/showroom map | Existing Baspa customizer likely used `baspa_map`; Meta Box also has map field support available. | New `inc/functions/location.php` and `templates/section/map.php` use `arctic_get_map_*`, `arctic_map_embed`, and Figma fallback image. | Potential duplicate source. Map URL/address/embed can diverge between contact, showroom, footer/about address, and customizer. | Create one location/map source contract. Contact and showroom must use the same configured address, map URL, and embed URL. Figma map image can only be visual fallback, not source of truth. |
| Product detail renderer | Existing product detail templates: `description.php`, `parameters-and-description.php`, `configurations.php`, `sidebar.php`, content/meta. | `figma-detail-body.php` was added and selected when configurations exist. | High-risk parallel renderer. It can bypass older description/parameter/content assumptions and make missing sections look intentional. | Merge into one product detail composer: configuration, params, content, colors, benefits, options, equipment, price, contact. No separate "Figma body" path that skips legacy product contracts. |
| Product configurations | Existing product meta and templates existed, but were insufficient. | New `modules/products/inc/configurations.php` and changed `configurations.php`. | Necessary data model repair, not inherently duplicate. Risk exists if old meta and new normalized rows both render differently. | One normalization helper must be the only frontend source. Admin save must preserve all rows; production QA must fail when public products have only dummy/incomplete configurations. |
| Product colors/cabinet colors | Existing product color text/image meta fields. | New untracked `spa_color` CPT and `modules/products/inc/colors.php` with legacy fallback. | Controlled migration risk. It is valid only if legacy fields are explicitly fallback and global color CPT becomes the source. | Finish migration plan: shell and cabinet colors use one catalog; product meta only selects IDs. Smoke must cover shell, cabinet, configurator, and mobile thumbnails. |
| Product benefits/options/equipment | Existing product content and section templates plus customer expectation from old Baspa/current site. | New/changed `product-benefits.php`, `product-options.php`, untracked `benefit-sections.php`. | Potential duplicate if benefits/options are hardcoded section logic instead of product/series feature catalog. | Use one reusable feature/equipment catalog with applicability by product/series. Do not create static per-template fallback cards. |
| Category hero/CTA | Existing heading/term template and term meta fields for button text/url. | `templates/section/category-intro.php` and category-specific hero behavior. | Potential duplicate. The invisible `/virivky/` CTA proves hero CTA layout is not globally governed. | One term/category hero component must own CTA position, link, crop, overlay, and smoke for `/virivky/`, `/swimspa/`, and series/category pages. |
| Features content | Existing pages under `/vlastnosti/` and page templates. | Untracked `modules/features/` registers private `feature` CPT and links cards to detail pages/custom URLs. | Needs product decision. This may be useful structured content, but it is a new content model next to pages. | Approve or remove. If approved, feature CPT owns feature cards only; detail pages still own long-form content. No duplicate detail body in CPT and page. |
| Services content | Existing `/sluzby/` page, contact/service form module, blocks. | Untracked `modules/services/` registers private `service` CPT for service cards. | Needs product decision. It may duplicate editable page blocks/services page content. | Approve or remove. If approved, service CPT owns repeatable service cards; `/sluzby/` page owns intro/layout; service form remains in contacts module. |
| Downloads/pricelist | Existing support/download module relation and support category flags. | `download` CPT and heavily custom Figma listing groups. | Potential duplicate with support documents and catalog/pricelist request. | Define one document model. Downloads can own files, supports can reference them, catalog request can gate/lead-capture them. No static Figma cards as fake documents. |
| References | Existing reference CPT/templates. | Modified listing/recent/reference page layout. | Mostly presentation skin, but customer reported detail showed only photo. | Keep one reference CPT. Repair rendering so text/meta/gallery all come from reference data; do not create separate "recent reference" data path. |
| About/team | Existing members CPT/settings. | `template-about.php` custom team presentation and settings. | Acceptable skin over members, but customer says info stale. | Keep members as source. Update member/page data, not template fallbacks. Smoke stale/fallback copy on `/o-nas/`. |
| Figma/static fallbacks | Seed/Figma assets and theme_mod defaults across helpers. | Many Arctic helpers use fallback images/copy (`figma`, `seed`, `theme_mod`). | Systemic risk. Fallbacks hide missing admin data and create fake acceptance. | Production fallback smoke must be required. Public blocks must expose `data-content-source`; release fails on unapproved seed/figma fallback content. |
| Encoding | Existing theme strings should be UTF-8. | Current dirty working files show mojibake when read in PowerShell in several places. | Release blocker if actual file bytes are corrupt; even if display-only, this area is fragile. | Run encoding smoke after every Czech text change. Prefer entities or verified UTF-8 writes. Do not commit mojibake. |

## Direct answer

Yes: the offers issue was a real duplicate architecture mistake. I created/used an Arctic hero promo while the original Baspa small offers section was still active on the homepage. The production symptom exposed it clearly.

The same pattern may exist or may be forming in these areas:

1. product detail body (`figma-detail-body.php` vs older detail templates),
2. catalog/price-list banner (static section vs catalog shortcode/form/download/pricelist workflow),
3. map/location source (`arctic_map_*` vs `baspa_map`/shared address),
4. category hero CTA (`category-intro.php` vs heading/term CTA rules),
5. untracked feature/service CPTs (possible duplicate of pages/blocks),
6. product colors and benefits/options (valid only if completed as one canonical catalog, not parallel fallback sections).

## Required repair rule from here

Before any remaining customer fix is marked done:

1. Identify the original Baspa owner: CPT, taxonomy, page, shortcode, template, customizer option, or form processor.
2. Decide whether Arctic code is a skin or a replacement.
3. If it is a replacement, explicitly retire the old path and add a smoke proving it is gone.
4. If it is a skin, prove it uses the original owner as its only data source.
5. Production deploy must include both code and data/migration steps where content is WP-owned.

## Immediate follow-up candidates

1. Add an offers contract smoke to release QA:
   - homepage: one `f-hero-promo`, zero `f-section--offers-small`;
   - mega menu: promo source is `offer-cpt`;
   - offers archive: cards source is `offer-cpt`;
   - no public `Vyprodej skladovych virivek`/`Skladove virivky`.
2. Audit and merge product detail renderers before filling product content.
3. Convert catalog request from hardcoded section copy/image to admin-managed content using the existing catalog form.
4. Decide whether `feature` and `service` CPTs are approved content models or should be removed before they reach production.
5. Create one map/location helper contract and point contact/showroom/about/footer to it.
