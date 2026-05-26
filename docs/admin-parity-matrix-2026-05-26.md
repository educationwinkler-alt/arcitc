# Admin Parity Matrix - Baspa.cz to Arctic Spas

Date: 2026-05-26

## Source Rules

- Figma is the visual and UX authority.
- Baspa.cz is the functional and WordPress admin workflow authority.
- The old `../Arctic-spas/` PHP site is the Arctic content authority.
- Arctic must adapt Baspa admin patterns to Arctic-specific sections instead of copying Baspa content or visual structure.

## Status Legend

- `ported`: Baspa workflow exists in Arctic with the same intent.
- `adapted`: Baspa workflow exists, but fields/copy were adjusted for Arctic.
- `arctic-specific`: Arctic needs an editable control that Baspa did not have.
- `not applicable`: Baspa feature is not relevant to the Arctic release scope.
- `deferred`: intentionally not implemented in this phase and requires a future decision.

## Matrix

| Area | Baspa feature / workflow | Arctic equivalent | Status | Implementation note | Sign-off owner |
| --- | --- | --- | --- | --- | --- |
| Theme contact identity | Customizer About settings for company name, phone, email, address, map | Same Customizer About settings used by Arctic templates | ported | Existing `inc/customize/section/about.php`; showroom template now also consumes address/city from these settings | Client / admin |
| Opening hours | Customizer Hours settings | Same day-by-day hours plus Arctic showroom card overrides | adapted | Existing Baspa hours remain; new Arctic Section controls cover Figma showroom card label/lines | Client / admin |
| Social links | Customizer Socials settings | Same Customizer Socials settings | ported | Instagram/Facebook/YouTube controls preserved | Client / admin |
| Tracking | Customizer GTM setting | Same Customizer GTM setting | ported | Existing `baspa_gtm` control preserved | Technical owner |
| Newsletter / Ecomail | Customizer Ecomail settings and form integration | Same settings with local-safe request handling | adapted | Arctic keeps API/list controls and avoids external leakage in local environments | Technical owner |
| Contact forms | Form sender/recipient/recaptcha controls | Same Customizer contact controls and contact form handling | adapted | Arctic keeps Baspa workflow, including pool/jacuzzi/service recipients where still useful for routing | Client / admin |
| Contact page copy | Contacts Settings page for section titles/form copy | Contacts Settings page in Arctic | adapted | Existing `modules/contacts/inc/admin.php` retained and hardened with capability/nonce/sanitization | Client / admin |
| Products | Product CPT, product metaboxes, categories, frontend display | Product CPT, product categories, Arctic product template | adapted | Arctic adds Arctic-specific product fields such as `jucra_model_name` and structured configurations | Client / admin |
| Product configurations | Baspa flat configuration fields | Structured Arctic configuration admin metabox | adapted | Phase 2 replaced formatting-sensitive text with structured rows while preserving legacy fallback | Client / admin |
| Jucra configurator | Not present in Baspa | Jucra Customizer settings plus product-level `jucra_model_name` | arctic-specific | Phase 1 code is complete; operational plugin activation remains plugin-blocked until Visao 3D Viewer is installed | Technical owner |
| Homepage promo | Not present in Baspa as Arctic Figma badge | Arctic Sections Customizer controls | arctic-specific | Admin can enable/disable promo, edit title/button/URL; component remains homepage-only | Client / admin |
| Category configurator CTA | Not present in Baspa in this form | Arctic Sections Customizer controls plus Jucra fallback | arctic-specific | Admin can edit title, text, button, fallback URL; Jucra pricing URL remains in Jucra settings/plugin flow | Client / admin |
| Showroom contact card | Baspa contact/hours settings | About settings plus Arctic Sections Customizer controls | arctic-specific | Admin can edit key Figma showroom contact name and hours card without code | Client / admin |
| FAQ | FAQ CPT, categories, FAQ settings | FAQ CPT, categories, support FAQ rendering, FAQ settings | adapted | Arctic support page reads FAQ CPT first, seeded fallback second | Client / admin |
| Support page copy | Baspa has Support CPT but no complete Figma support page settings | New Support Settings page | arctic-specific | Admin can edit FAQ title, service form title/text, and help card person/CTA | Client / admin |
| Downloads / PDFs | Baspa had no Arctic download page workflow | Download CPT, taxonomy, metabox, shortcode, and Settings page | arctic-specific | Admin can manage PDF files and edit page/filter/group/card/button copy | Client / admin |
| References / realizations | Reference CPT and Settings page | Reference CPT and Settings page in Arctic | ported | Existing workflow preserved for old Arctic reference content | Client / admin |
| Accessories | Accessory CPT/settings | Accessory workflow retained | adapted | Kept because Arctic products/accessories can reuse the same admin pattern | Client / admin |
| Editorial articles | WordPress Posts and page/post metaboxes | Same WordPress Posts and page/post metaboxes | ported | Client can add/edit articles and pages through standard WP admin | Client / admin |
| Header/menu/footer | WordPress menus plus Customizer contact/social/footer data | Same menu and Customizer-driven data | adapted | Figma layout controls presentation; admin workflow remains WP-native | Client / admin |
| Jobs | Jobs CPT and Settings page in Baspa | Jobs CPT and Settings page retained | not applicable | Retained for parity, but not a primary Arctic release section unless client asks for careers content | Client decision |
| Members | Members CPT and Settings page in Baspa | Members CPT and Settings page retained | not applicable | Retained for parity, but not a primary Arctic release section unless client asks for team content | Client decision |
| Partners | Partners CPT and Settings page in Baspa | Partners CPT and Settings page retained | not applicable | Retained for parity, but not a primary Arctic release section unless client asks for partner content | Client decision |
| Local-safe integrations | Baspa form/ecomail integrations | Arctic hardened form/ecomail integrations | adapted | Local environment avoids external Ecomail calls; production behavior remains configurable | Technical owner |

## Implemented In Phase 3

- Added `Arctic Sections` Customizer controls for homepage promo, configurator CTA, and showroom card copy.
- Added `Support Settings` admin page for Arctic support page titles, form copy, and help card copy.
- Added `Downloads Settings` admin page for downloads page/filter/group/card/button copy.
- Updated homepage promo, configurator, showroom, support, downloads page, and downloads listing templates to consume admin settings.
- Recorded not-applicable Baspa modules explicitly instead of leaving them ambiguous.

## Deferred / Follow-Up Notes

- Visao 3D Viewer plugin activation is not part of admin parity code and remains blocked until the plugin ZIP/access is available.
- Full client sign-off is still needed after Phase 4 content parity and Phase 5 visual QA.
- If the client wants careers/team/partners as live Arctic sections, the retained Baspa-compatible admin modules can be moved from `not applicable` to active scope.

## Exit Criteria Status

- Client can edit the relevant Arctic release content without touching code: complete for Phase 3 scope.
- Baspa-like admin workflows are retained or adapted for Arctic-relevant sections: complete.
- Missing Baspa features are marked not applicable or deferred with reason: complete.
- Matrix is committed and linked from the master finalization plan: complete.
