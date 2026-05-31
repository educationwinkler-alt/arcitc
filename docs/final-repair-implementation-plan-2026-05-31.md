# Finální plán oprav a implementace - 2026-05-31

Tento dokument je nový kanonický plán pro dokončení webu po posledním auditu detailu vířivek a po přímém ověření ve Figmě přes MCP. Cíl není přidat další volný audit, ale uklidit všechny starší plány do jednoho pořadí oprav, aby se už neimplementovalo odhadem.

## Stav Implementace 2026-06-01

P0 bloky z re-auditu screenshotů jsou implementované a kryté guardy:

| Blok | Stav | Evidence |
| --- | --- | --- |
| P0-10 Header/product sticky nav | Hotovo | `.f-links--product .f-links__container` začíná pod hero, ne přes něj; `npm run product-detail:physical` hlídá header control overlap i sticky-nav/hero overlap. |
| P0-11 Product detail physical parity | Hotovo | McKinley a Timberwolf renderují konfigurace s media kartou; chybějící konfigurace používá Figma fallback `detail-config-*.png`, ne text-only kartu. |
| P0-12 Lukáš Dušek media crop | Hotovo | `contact-lukas-dusek.png` je render Figma node `1:50`; globální CSS zoom hack je odstraněný a avatar je guardovaný ve footer/contact/product reuse. |
| P0-13 Benefit/option pseudoikony | Hotovo | `benefit-media-01.png` až `benefit-media-18.png` jsou exporty z Figma media nodů; CSS `:before/:after` pseudoikony jsou zakázané a hlídané. |
| P0-14 Contact CTA/sidebar hours | Hotovo | Product sidebar používá sdílený `js-hours__status`; product CTA hours pill je uvnitř červeného baru. |

End-to-end ověření: `npm run qa:local` prošlo celé dne 2026-06-01.

## Rozhodnutí

| Oblast | Rozhodnutí |
| --- | --- |
| Zdroj designu | Primární zdroj je Figma final grafika, ne screenshoty, ne paměť a ne odhad. |
| Figma přístup | V repozitáři se smí držet pouze odkazy/file key. Token ani hesla se nesmí zapisovat do dokumentů, commitu ani chatu. |
| Produktový detail | Poslední audit detailu je pravdivý backlog: detail není dotažený a nesmí zůstat Timberwolf-only. |
| Google Maps | Mapový contract není hotový, dokud všechny mapové CTA nevedou na stejný Google Maps link. |
| Swimspa | Swimspa nesmí být prezentovaná jako pixel-parity Figma varianta, pokud nemá potvrzený vlastní frame nebo explicitně schválené zrcadlení hot-tub flow. |
| QA | Guard musí kontrolovat sdílený contract ve všech výskytech, ne jen stránku, která se zrovna opravovala. |

## Zdroj Pravdy

| Priorita | Zdroj | Použití |
| --- | --- | --- |
| 1 | Figma final grafika `xeOew3dFjDVfjXZrJ09emM` | Vizuální layout, rozměry, vrstvy, media treatment, komponenty. |
| 2 | Figma wireframe `puPBNFpuaXpRZR2TINaDvm` | UX flow, sekce, popup/modal chování, pořadí obsahu. |
| 3 | Poslední audit `docs/audit-detail-virivky-figma-2026-05-31.md` | Aktuální dluh produktového detailu. |
| 4 | Repair plány `docs/repair-plan-from-audits-2026-05-29.md` a `docs/unresolved-global-repair-plan-2026-05-30.md` | Starší rozhodnutí, komponentové guardy a uzavřené vlny. |
| 5 | Asset mapy `docs/asset-source-map-2026-05-29.md`, `docs/figma-asset-manifest.md`, `docs/component-contract-usage-map-2026-05-30.md` | Evidence assetů a sdílených contractů. |
| 6 | Aktuální kód a smoke testy | Ověření, co je skutečně implementované. |

## Přímá Figma Evidence

| Frame | Node | Co je potvrzené |
| --- | --- | --- |
| Detail konkrétního produktu | `1:1461` | Hero image `1:1462`, H1 `Venkovní vířivka Timberwolf_`, konfigurační image cards `1:1471` a `1:1473`, acrylic swatches `1:1475`, cabinet swatches `1:1491`, benefit/media card group `1:1498` a další option/benefit bloky. |
| Kategorie | `1:262` | Product card contract, CTA button, configurator component `1:402` s image layer `1:409`, showroom shared component `1:437`, footer. |
| Kontakt | `1:1037` | Figma map image `1:1069`, white map card `1:1070`, pin `1:1086`, šest contact cards, footer component. |
| Wireframe detail produktu | `100:662` | Detail flow je produktový contract, ne jednorázová Timberwolf výjimka. |
| Wireframe kategorie | `100:1504` | Kategorie vířivek má potvrzený plný category flow včetně konfigurátoru. |

## Co Už Neotevírat Bez Nového Nálezu

Footer zde záměrně není uvedený jako hotový. Deep physical audit v2 ho drží jako P0 globální component problém a tento plán ho nově řeší v P0-9.

| Oblast | Stav | Poznámka |
| --- | --- | --- |
| Sdílené card/button radius tokens | Hotovo v předchozích repair vlnách | Reopen pouze při konkrétním regression screenshotu nebo failing guardu. |
| Header status / opening hours | Hotovo po re-auditu | Dynamický status zachován, product sticky-nav overlap opraven a guardovaný přes `npm run product-detail:physical`. |
| Reference archive a recent carousel | Hotovo jako contract | Stále hlídat kontext category/product referencí. |
| Shared showroom panel | Hotovo jako contract | Platí pro HP, category, swimspa a showroom kontext. |
| Contact header a contact directory layout | Vizuálně hotovo | Portréty zůstávají `WAITING_ON_OWNER`, pokud nejsou dodané owner assety. |
| Záruka, servis, údržba, o nás, podpora, ke stažení | Opraveno v page-specific vlnách | Zůstává owner/media debt, ne layout debt. |
| Category product media | Částečně hotovo | `/virivky/` a `/swimspa/` používají sdílený listing contract, ale swimspa scope a detail flow zůstávají otevřené. |

## P0 Bloky Z Plánu

### P0-1 Source Map A Contract Úklid

| Položka | Detail |
| --- | --- |
| Problém | Starší `component-contract-usage-map` tvrdí, že product benefit media jsou CSS tokeny. Poslední Figma audit a MCP evidence ukazují, že detail používá vizuální media vrstvy, ne jen vlastní CSS ikonky. |
| Soubory | `docs/component-contract-usage-map-2026-05-30.md`, `docs/asset-source-map-2026-05-29.md`, `docs/figma-asset-manifest.md`, `docs/product-category-media-contract-2026-05-30.md`. |
| Implementace | Přepsat product benefit/option media status na `WAITING_ON_FIGMA_EXPORT`, `WAITING_ON_OWNER`, nebo přesný export/owner asset. Zrušit tvrzení, že CSS token je finální Figma asset. |
| Akceptace | Žádná produktová media vrstva nesmí být označená jako hotová, pokud není doložený node ID/export path/owner source. |

### P0-2 Google Maps Contract Dokončení

| Položka | Detail |
| --- | --- |
| Problém | `/kontakt/` map card už používá `baspa_map`, ale footer quick map pořád vede na interní `/kontakt/`. Starší plán PR-5 chtěl všechny `Zobrazit na mapě` CTA sjednotit na Google Maps. |
| Vizuální bug | Deep physical audit v2 navíc popisuje separátní problém: Figma contact map image node `1:1069` je na `x=-867`, zatímco local `.f-local-map__image` byl naměřený na `x=-595`. Rozdíl cca `272px` posouvá geografii pod pinem, i když pin je obrazovkově blízko Figmě. |
| Figma evidence | Kontakt `1:1037` potvrzuje mapu jako velký vizuální blok s kartou a pinem. Figma neříká, že mapové CTA má vést na interní kontakt. |
| Kód evidence | `wp-content/themes/arctic/templates/section/map.php:45` používá `baspa_map`; `wp-content/themes/arctic/templates/footer.php:114` pořád používá `home_url('/kontakt/')`. Contact override v `_components.less` nastavuje `.page-template-template-contact .f-local-map__image` přes `left: calc(50% - 1827px)`, takže musí projít přesným 1920px měřením proti Figma x pozici. |
| Soubory | `wp-content/themes/arctic/templates/footer.php`, `wp-content/themes/arctic/templates/section/map.php`, showroom/location template, customizer/seed, `tools/contact-map-smoke.js`, link smoke. |
| Implementace | Nastavit canonical `baspa_map` Google Maps URL. Footer, showroom a contact map CTA musí používat stejný helper/link. Současně srovnat local map background offset tak, aby image/pin geometrie odpovídala Figma nodům `1:1069` a `1:1086`. Pokud link chybí, fallback na interní kontakt musí být explicitně označený jako fallback, ne vydávaný za hotový stav. |
| Akceptace | Všechny texty `Zobrazit na mapě` vedou na Google Maps URL. Smoke failne při interním `/kontakt/` nebo `/showroom/` map CTA bez explicitního fallback statusu. Visual/DOM metric na 1920px ověří map image x pozici proti `-867`, pin proti Figma pozici a nepřítomnost vlastních overlay labelů, pokud nejsou ve Figma layeru. |

### P0-3 Product Detail Contract Pro Vířivky A Swimspa

| Položka | Detail |
| --- | --- |
| Problém | Produktový detail je pořád vázaný na Timberwolf větev. To přesně potvrzuje poslední audit. |
| Figma evidence | Detail frame `1:1461` je product detail contract: hero, konfigurace, swatches, benefits/options, configurator CTA, reference/footer. Wireframe detail `100:662` potvrzuje flow detailu jako obecný UX vzor. |
| Kód evidence | `wp-content/themes/arctic/modules/products/templates/post/single.php:13` volá `figma-detail-body` jen přes speciální větev; `wp-content/themes/arctic/modules/products/templates/post/single/heading.php:32` přidává `f-heading--timberwolf`. |
| Soubory | `single.php`, `figma-detail-body.php`, `heading.php`, `configurations.php`, product meta/seed helpers, category/detail templates, LESS contract vrstvy. |
| Implementace | Zavést sdílený `product-detail-contract` pro všechny produkty s detailním obsahem. Timberwolf může být první ověřený fixture, ale nesmí být jediná cesta. Lunar, Orion, Husky a aspoň jeden swimspa produkt musí projít stejným rendererem nebo mít explicitně zdokumentovaný scope/fallback. |
| Akceptace | Žádná Timberwolf-only layout větev pro Figma detail. Minimálně `timberwolf`, `lunar`, `orion`, `husky` a jeden swimspa detail mají stabilní layout bez prázdných media bloků. |

### P0-4 Figma Asset Export Pro Produktový Detail

| Položka | Detail |
| --- | --- |
| Problém | `docs/figma-asset-manifest.md` nepokrývá detail produktu dostatečně. Benefit/option media a některé konfigurace nejsou dohledané jako asset contract. |
| Figma evidence | Detail frame obsahuje image layers pro konfigurace `1:1472`, `1:1474`, swatches `1:1476` až `1:1496`, benefit circular media uvnitř `1:1498` a navazujících benefit/option groups. |
| Soubory | `docs/figma-asset-manifest.md`, `docs/figma-local-exported-assets.json`, `wp-content/uploads/import/figma`, product templates. |
| Implementace | Vypsat každou detailovou media vrstvu s node ID, účelem, export path a source statusem. Exportovat pouze design-owned assety. Produktové fotky a owner obsah nenahrazovat design-only obrázky, pokud jsou jen mock. |
| Akceptace | Každé detailové media místo má jeden z těchto stavů: `available`, `usable-fallback`, `design-only`, `WAITING_ON_OWNER`, `WAITING_ON_FIGMA_EXPORT`. Žádné tiché prázdné nebo vymyšlené media. |

### P0-5 Konfigurační Karty Bez Prázdných Thumbnail Slotů

| Položka | Detail |
| --- | --- |
| Problém | Konfigurační karty renderují thumbnail wrapper i bez image assetu, což vytváří prázdné/falešné media místo. |
| Figma evidence | Detail frame má dvě velké zaoblené konfigurační image cards s reálnou image vrstvou, ne prázdný placeholder. |
| Kód evidence | `wp-content/themes/arctic/modules/products/templates/post/single/configurations.php:27` renderuje `.f-product-configuration__thumb` i když `$image` chybí. |
| Soubory | `configurations.php`, product seed/meta, `tools/product-media-smoke.js`, LESS pro no-media variantu. |
| Implementace | Doplnit image mapping pro konfigurace, kde asset existuje. Když asset neexistuje, karta musí dostat explicitní `f-product-configuration--no-media` nebo `data-asset-status`, a layout se nesmí tvářit jako hotová Figma image card. |
| Akceptace | Smoke failne na prázdný `.f-product-configuration__thumb` bez image nebo bez explicitního missing statusu. |

### P0-5A Kalahari Swatch Viditelnost

| Položka | Detail |
| --- | --- |
| Problém | Audit detailu sekce 7 popisuje, že swatch Kalahari v `/konfigurator/orion/` působí jako prázdný bílý kroužek. Asset se načítá, ale je tak světlý, že splývá s bílým pozadím. |
| Zdroj evidence | `docs/audit-detail-virivky-figma-2026-05-31.md` sekce `7. Swatch Kalahari v 3D konfigurátoru`; lokální referenční soubor `wp-content/uploads/import/owner-swatches/acrylic-kalahari.jpg`. |
| Soubory | Theme CSS/LESS wrapper pro Visao output, případně enqueue override. Needitovat vendor/plugin soubory ve `wp-content/plugins/visao-3d-viewer/`. |
| Implementace | Přidat jemný kruhový border a případně subtilní shadow pro `.clickable-image` nebo užší Visao swatch selector v theme vrstvě. Pravidlo má pomoct světlým swatchům jako Kalahari a Platinum Swirl bez vizuálního poškození tmavých swatchů. |
| Akceptace | Na `/konfigurator/orion/` je Kalahari viditelný jako světlý granitový swatch s obrysem, ne jako prázdný bílý kruh. Oprava není v pluginu a nezakrývá vybraný/aktivní stav. |

### P0-5B Figma Detail Encoding A Mojibake Guard

| Položka | Detail |
| --- | --- |
| Problém | Audit detailu označuje riziko mojibake ve `figma-detail-body.php` a souvisejících detailových šablonách. Obecná zmínka nestačí, protože soubor jde přímo do produkčního renderu. |
| Soubory | `wp-content/themes/arctic/modules/products/templates/post/single/figma-detail-body.php` a související `single/*.php` partials. |
| Implementace | Normalizovat dotčené soubory na UTF-8 bez BOM. Přidat nebo rozšířit statický guard, který failne na replacement char a typické mojibake sekvence jako `Ã`, `Ä`, `Å`, `U+00C3`, `U+00C4`, `U+00C5`. |
| Akceptace | Český text v detailu se renderuje správně. Guard kontroluje konkrétní detailové PHP soubory a failne před commitem, pokud se mojibake vrátí. PowerShell mojibake ve výstupu se nebere jako důkaz poškození souboru; rozhoduje obsah souboru/UTF-8 scan. |

### P0-6 Benefit A Option Data Model

| Položka | Detail |
| --- | --- |
| Problém | Benefit a option bloky jsou částečně hardcoded v šablonách a media jsou CSS náhrady. To se nedá udržovat pro různé vířivky a swimspa. |
| Figma evidence | Detail product frame má opakované benefit/option cards s image/media vrstvami a popup/plus chováním. |
| Kód evidence | `wp-content/themes/arctic/templates/section/product-benefits.php:68` a `wp-content/themes/arctic/templates/section/product-options.php:42` renderují `.f-product-benefit__media--...` span. `_component-contracts.less:934` definuje CSS media treatment. |
| Soubory | `product-benefits.php`, `product-options.php`, product meta helpers, seed, popup template, LESS, smoke test. |
| Implementace | Doporučený model: `benefit_key`, `title`, `summary`, `media_id`, `source_status`, `popup_template`, `sort_order`, `enabled`, `applies_to_series`, `applies_to_category`. Hardcoded fallback smí zůstat jen jako seed/admin-empty fallback a musí být označený. |
| Akceptace | Static card nemá plus ani neviditelný trigger. Interaktivní card má popup content. Media není CSS-only náhrada, pokud Figma/source říká image asset. |

### P0-7 Swimspa Scope A Detail

| Položka | Detail |
| --- | --- |
| Problém | Swimspa působí jako nedotažená odvozenina vířivek. Samostatný Figma final frame pro swimspa category/detail není potvrzený. |
| Figma evidence | Figma final grafika má `KATEGORIE 1:262` pro hot-tub category. Starší unresolved plan výslovně říká, že swimspa použití stejného frame je implementační rozhodnutí, ne důkaz. |
| Soubory | `taxonomy-product-category.php`, category helpers, product detail renderer, Jucra CTA helper, content/seed, smoke tests. |
| Implementace | Udělat explicitní rozhodnutí: buď swimspa zrcadlí category contract s upraveným obsahem a bez konfigurátoru, nebo dostane samostatný design/scope. Detail swimspa musí používat stejný product-detail contract s vlastními daty, nebo být označený jako čekající scope. |
| Akceptace | `/swimspa/` nepoužívá texty typu `vlastní vířivku`, pokud jde o swimspa. Swimspa nemá nepotvrzený konfigurátor. Aspoň jeden swimspa detail má ověřený layout/fallback stav. |

### P0-8 QA Gate Konsolidace

| Položka | Detail |
| --- | --- |
| Problém | `qa:local` obsahuje mnoho guardů, ale ne všechny finální/release smoke. `final:qa` a `jucra:smoke` existují zvlášť. |
| Kód evidence | `package.json` obsahuje `final:qa`, `jucra:smoke`, `contact-map:smoke`, `product-media:smoke`, `component:smoke`; `qa:local` zatím nezahrnuje `final:qa` a `jucra:smoke`. |
| Soubory | `package.json`, `tools/contact-map-smoke.js`, `tools/product-media-smoke.js`, `tools/component-contract-smoke.js`, nový detail contract smoke podle potřeby. |
| Implementace | Buď rozšířit `qa:local`, nebo přidat explicitní `release:qa`, které spustí i `final:qa` a `jucra:smoke`. Doplnit testy pro map CTA, product detail contract, prázdné konfigurace a swimspa wording. |
| Akceptace | Release gate failne na interní map CTA, map background offset, navy footer bez Figma landscape backgroundu, Timberwolf-only detail, prázdný config thumb, neviditelný světlý swatch, mojibake v detailových PHP, CSS-only benefit media bez source statusu, swimspa copy drift a chybějící Jucra fallback/plugin stav. |

### P0-9 Footer Background A CTA Handoff

| Položka | Detail |
| --- | --- |
| Problém | Deep physical audit v2 body 41-60 označil footer jako P0: Figma footer je světlý landscape/mountain background, zatímco local byl tmavý navy blok kvůli handoff/contract override. Bez uzavření footeru nejde dát final visual pass žádné stránce. |
| Figma evidence | Footer component node `1:208` má `1920 x 773`; footer background node `1:210` používá landscape image. Asset `wp-content/uploads/import/figma/footer-background.jpg` existuje a má být viditelný. |
| Kód evidence | Deep audit popisuje starý override `.f-footer--arctic.f-footer--handoff` a `--arctic-contract-footer-bg` na navy. Aktuální kód je potřeba ověřit, protože `footer.php` už nemusí třídu `f-footer--handoff` obsahovat a `--arctic-contract-footer-bg` může být světlý, ale plán ani guard to zatím explicitně neuzavírá. |
| Soubory | `wp-content/themes/arctic/templates/footer.php`, `wp-content/themes/arctic/src/less/_components.less`, `wp-content/themes/arctic/src/less/_component-contracts.less`, component/final visual smoke. |
| Implementace | Zajistit, že `.f-footer--arctic` na desktopu i mobile zobrazuje Figma landscape background a že žádný handoff override nepřepíná footer na `var(--arctic-color-menu)`/navy. CTA-to-footer seam musí navazovat do světlého landscape footeru, ne do tmavého boxu. |
| Akceptace | Computed style/screenshot na HP, category, kontakt a jedné detail stránce potvrzuje viditelný `footer-background.jpg`, desktop výšku cca `773px`, světlý footer treatment a žádný navy `f-footer--handoff` regression. Guard failne, pokud se handoff třída nebo contract proměnná znovu použije k potlačení landscape backgroundu. |

### P0-10 Header Geometrie A Product Sticky Nav Collision

| Položka | Detail |
| --- | --- |
| Problém | Header byl v plánu omylem uzavřený jen podle dynamického opening-hours statusu. Screenshot `/product/mckinley/` ukazuje fyzický problém: header, hero a product sticky nav se vizuálně perou a jeden prvek leze přes druhý. |
| Figma evidence | Header component set `1:1831`, default variant `1:1832`: komponenta má `1400 x 105`, vnitřní bílý panel `1400 x 85` na `y=20`, nav/CTA/search jsou uvnitř jednoho panelu. Product detail frame `1:1461` má product nav row kolem `x=260 y=749 w=1400`, ale nesmí zakrýt hero fakta ani být schovaný pod reálným browser chrome viewportem. |
| Local evidence | Re-audit 2026-06-01: `/product/mckinley/` má `.f-heading--product-detail` `0,0,1920,795` a `.f-links--product` `0,749,1920,93`, takže sticky bar překrývá posledních cca `46px` hero oblasti. |
| Soubory | `templates/header.php`, `templates/header/*`, product links partial, `_components.less`, `_component-contracts.less`, `tools/header-status-smoke.js`, nový/rozšířený product header physical smoke. |
| Implementace | Oddělit header panel, hero a product sticky nav jako fyzicky hlídané vrstvy. Zachovat Figma header komponentu, ale přestat spoléhat na absolutní/z-index hacky, které fungují jen na jednom screenshotu. Product sticky nav musí mít jasné místo v toku nebo bezpečný overlap pouze pokud Figma i guard potvrzují, že nezakrývá obsah. |
| Akceptace | Na 1920x1080 a compact/laptop viewportu žádný header/nav/hero text/fakta nepřekrývá jiný interaktivní prvek. Guard měří bottom/top kolize header panelu, hero facts a `.f-links--product`. |

### P0-11 Product Detail Physical Parity Pro Non-Timberwolf

| Položka | Detail |
| --- | --- |
| Problém | P0-3 řešil renderer, ale neuzavíral fyzickou shodu vnitřku detailu. `/product/mckinley/` ukazuje, že sdílený renderer sám nestačí: konfigurace je textová karta bez Figma image vrstvy, vzniká velké prázdné místo a sidebar působí jako odtržený blok. |
| Figma evidence | Detail frame `1:1461` obsahuje konfigurační image cards `1:1471` a `1:1473`, swatches `1:1475` a `1:1491`, benefit media group `1:1498`, configurator CTA `1:402`/image `1:409`, reference a contact CTA. |
| Local evidence | Re-audit 2026-06-01: `/product/mckinley/` `.f-product-detail-config__layout` je `1400 x 546`, ale `.f-product-configurations` má jen `278px` výšku a jednu textovou kartu bez `.f-product-configuration__thumb`; sidebar je na `x=1362 y=934 w=298 h=341`, zatímco levá část zůstává vizuálně nedotažená. |
| Soubory | `single.php`, `figma-detail-body.php`, `configurations.php`, `sidebar.php`, product seed/meta, image mapping, LESS, product detail physical smoke. |
| Implementace | Neoznačovat non-Timberwolf detail za hotový, dokud nemá buď Figma-like configuration media cards, nebo explicitní `WAITING_ON_OWNER` layout, který nevypadá jako rozbitá hotová karta. McKinley, Lunar, Orion, Husky a jeden swimspa detail musí projít fyzickým smoke testem, ne jen HTML markerem. |
| Akceptace | Každý testovaný detail má konfigurace, sidebar, CTA, benefits/options a references ve Figma měřítku bez prázdných děr. Smoke failne na text-only configuration block vydávaný za Figma detail. |

### P0-12 Lukáš Dušek Media Source A Crop Contract

| Položka | Detail |
| --- | --- |
| Problém | Duškova fotka je použitá napříč webem, ale plán nehlídal, že její zdroj, crop a velikost odpovídají Figmě v každém kontextu. Současný CSS globálně transformuje jakýkoliv `contact-lukas-dusek.png` avatar, což je křehké a snadno rozbije footer/contact CTA/product sidebar jinak. |
| Figma evidence | Avatar node `1:50` je `58 x 58`, imageRef `9257bc6178b9895b9a6eed6e599b071c4e469db5`, crop transform `0.5190338492 / 0.2768465877 / 0.1313925534`. Stejný imageRef se objevuje i v product contact card a footer quick contact vrstvách. |
| Local evidence | Re-audit 2026-06-01: lokál používá `wp-content/uploads/import/figma/contact-lukas-dusek.png` ve footeru, product sidebaru a contact CTA; viditelný crop je řízen CSS pravidlem `img[src*="contact-lukas-dusek.png"] { width: 192.7%; transform: translate(...) }`, ne explicitním per-context assetem/guardem. |
| Soubory | `templates/section/contact.php`, `templates/footer.php`, `modules/products/templates/post/single/sidebar.php`, `_component-contracts.less`, asset manifest, nový contact media smoke. |
| Implementace | Re-exportovat nebo ověřit přesný Figma avatar asset z node `1:50` a zavést pojmenovaný avatar helper/partial s context variants. Globální selector podle filename nahradit scoped třídou nebo přímo správně oříznutým assetem. |
| Akceptace | Footer quick contact, global contact CTA a product sidebar vykreslují Duškovu fotku ve správném Figma cropu/rozměru. Guard porovná source/crop/box a failne, pokud se avatar škáluje neřízeným globálním CSS hackem. |

### P0-13 Benefit/Option Pseudoikony Pryč

| Položka | Detail |
| --- | --- |
| Problém | Plán sice říká, že CSS media token není finální asset, ale není dost tvrdý: současné pseudoikony jsou viditelný regresní stav a působí jako vymyšlený design. |
| Figma evidence | Benefit group `1:1498` má karty `452px` wide, image rectangles `1:1500`, `1:1510`, `1:1520` atd. o velikosti `87 x 87`, red plus frame `33 x 33`, žádné kreslené CSS pseudoikony. |
| Local evidence | Re-audit 2026-06-01 a screenshoty: `.f-product-benefit__media` používá generované `:before/:after` kruhy/čtverce a opakované pseudoikony pro Heatlock, kabinet, vodu atd. |
| Soubory | `templates/section/product-benefits.php`, `templates/section/product-options.php`, `_component-contracts.less`, `_components.less`, asset manifest, benefit media smoke. |
| Implementace | Okamžitě zakázat viditelné pseudoikony jako final treatment. Každý benefit/option media slot musí být buď Figma image export/owner asset, nebo neutrální explicitní waiting placeholder, který nevypadá jako finální ikonografie. |
| Akceptace | Žádný product benefit/option card nevykresluje dekorativní CSS pseudoikonu jako náhradu Figma image. Smoke failne na `:before/:after` media treatment u `WAITING_ON_FIGMA_EXPORT`, pokud není explicitně neutrální placeholder. |

### P0-14 Contact CTA A Product Sidebar Hours Layout

| Položka | Detail |
| --- | --- |
| Problém | Červený contact CTA banner funguje na homepage lépe, ale v product kontextu hours pill leze z vnitřního panelu ven. Product sidebar navíc používá statickou otevírací dobu, takže není sjednocený s dynamickým header/contact CTA/footer statusem. |
| Figma evidence | Product detail frame `1:1461` obsahuje product sidebar contact card layout `x=1362 y=934 w=298 h=341` a contact CTA block `x=260 y=6552 w=1400 h=455`; hours pill je součástí kontaktního detailu, ne volně plující badge mimo panel. |
| Local evidence | Re-audit 2026-06-01: `/product/mckinley/` `.f-contact-cta__bar` bottom `6052`, `.f-contact-cta__hours` bottom `6062.4`, tedy overflow cca `10px`. `sidebar.php` renderuje literal `Po - Pá 8:00-17:00 h` bez `templates/about/hours.php`. |
| Soubory | `templates/section/contact.php`, `templates/about/hours.php`, `modules/products/templates/post/single/sidebar.php`, `_components.less`, `_component-contracts.less`, `tools/header-status-smoke.js`, nový contact CTA hours smoke. |
| Implementace | Převést product sidebar na sdílený hours partial. Pro contact CTA odstranit absolutní hodnoty, které dovolí hours pillu opustit bar, nebo je nahradit fyzicky hlídaným Figma layoutem pro homepage/category/product/swimspa context. |
| Akceptace | V homepage, `/virivky/`, `/swimspa/` a product detailu je hours pill uvnitř kontaktního panelu. Product sidebar má dynamický `.js-hours__status` se stejným open/closed contractem jako header/footer. |

## Otevřené P1 Bloky

| Blok | Co zbývá |
| --- | --- |
| Jucra / Visao produkce | Lokální flow existuje, ale produkce/staging musí mít plugin nainstalovaný, aktivovaný, nastavené domény/API a otestovaný shortcode output. |
| Jucra lead handoff | Potvrdit cílový formulář a parametry. Do té doby musí request URL a fallback jasně ukazovat, co se odešle. |
| Owner media | Team/contact portréty, warranty card media, chybějící galerie produktů a cabinet swatches zůstávají `WAITING_ON_OWNER`. |
| Windows 175% / compact laptop | Po P0 opravách zopakovat signoff, protože detail/category výška a sticky prvky se změní. |
| Copy/content parity | Po opravě product detailu a swimspa projít texty proti old Arctic/content source, ne jen proti Figma placeholderům. |

## Implementační Pořadí

| Pořadí | Blok | Proč teď |
| --- | --- | --- |
| 1 | Source map + manifest cleanup | Nejdřív zastavit další hádání, co je asset, fallback nebo design-only. |
| 2 | Header geometry + product sticky nav collision | Pokud header/nav překrývá obsah, všechny další vizuální screenshoty jsou nespolehlivé. |
| 3 | Product detail physical parity pro non-Timberwolf | McKinley ukazuje, že samotný sdílený renderer nestačí. Detail je hlavní viditelný dluh. |
| 4 | Benefit/option pseudoikony pryč + Figma media status | Pseudoikony jsou okamžitě viditelné a nesmí být vydávány za Figma implementaci. |
| 5 | Contact CTA/sidebar hours + Dušek media crop | Globální kontaktní komponenty se opakují všude, musí být správně v každém kontextu. |
| 6 | Google Maps link + contact map offset | Link a vizuální podklad/pin jsou dva různé bugy, ale musí se uzavřít společně. |
| 7 | Footer background + CTA handoff | Globální P0 z deep auditu; bez footeru není final visual pass. |
| 8 | Product detail Figma asset/export pass | Naváže na renderer a odstraní CSS/fake media. |
| 9 | Konfigurace, Kalahari swatch, benefits/options data model | Bez datového a vizuálního contractu se chyby budou vracet u dalších produktů. |
| 10 | Figma detail encoding guard | Malý krok, ale produkčně viditelný, proto před release QA. |
| 11 | Swimspa category/detail decision | Swimspa musí být pravdivý scope, ne přetřená vířivka. |
| 12 | QA/release gate | Až po contract opravách, aby guardy hlídaly skutečný cílový stav. |
| 13 | Produkční Jucra a owner assets | Nasazovací a obsahové doplnění po stabilizaci UI. |

## Detailní Akceptační Checklist

| Oblast | Pass kritérium |
| --- | --- |
| Figma | Pro product detail jsou v manifestu uvedené použité node ID, export path a source status. |
| Header | Header component `1:1832`, product hero a `.f-links--product` se fyzicky nepřekrývají na 1920px ani compact/laptop viewportu. |
| Mapy | Každé `Zobrazit na mapě` vede na stejný Google Maps link nebo má explicitní fallback status. |
| Contact map visual | Na 1920px sedí map image offset a pin proti Figma nodům `1:1069` a `1:1086`; pin neleží nad posunutou geografií. |
| Footer | Footer používá Figma landscape background, ne navy handoff override. |
| Detail vířivky | Timberwolf, McKinley, Lunar, Orion a Husky používají stejný detail contract bez layout hacku a bez prázdných layout děr. |
| Detail swimspa | Aspoň jeden swimspa detail má explicitní contract nebo čekající scope stav. |
| Konfigurace | Žádná karta nezobrazuje prázdný thumbnail wrapper jako hotový image card. |
| Kalahari swatch | Světlé swatche v Jucra/Visao UI mají viditelný obrys bez editace pluginu. |
| Encoding | `figma-detail-body.php` a detailové partials jsou UTF-8 bez BOM a bez mojibake sekvencí. |
| Benefits/options | Žádné viditelné CSS pseudoikony jako finální media; Figma image vrstva je reálný export nebo explicitní waiting placeholder. |
| Popupy | Plus/trigger existuje jen u card, která má reálný popup content. |
| Dušek avatar | Node `1:50` crop/rozměr sedí ve footeru, contact CTA a product sidebaru; žádný globální filename hack nesmí změnit crop bez guardu. |
| Contact CTA hours | Hours pill zůstává uvnitř contact CTA bar na homepage, category, swimspa i product detailu. |
| Product sidebar hours | Product contact card používá sdílený dynamický hours partial, ne statický string. |
| Reference | Product detail a category reference zůstávají kontextové. |
| Jucra | Pokud plugin není aktivní, UI ukazuje pravdivý fallback; pokud aktivní je, shortcode output se reálně renderuje. |
| QA | `qa:local` nebo `release:qa` zahrnuje finální Figma smoke, Jucra smoke, map CTA guard a product detail media guard. |

## Příkazy Pro Ověření

| Příkaz | Účel |
| --- | --- |
| `npm run css:build` | Ověří LESS build po contract změnách. |
| `npm run product-media:smoke` | Ověří product/category media a detail media guardy. |
| `npm run component:smoke` | Ověří sdílené komponentové contracty. |
| `npm run product-header:smoke` | Doplnit: ověří header/product sticky nav kolize proti Figma a real viewportům. |
| `npm run product-detail:physical` | Doplnit: ověří fyzickou shodu detailu pro Timberwolf, McKinley, Lunar, Orion, Husky a swimspa fixture. |
| `npm run contact-media:smoke` | Doplnit: ověří Dušek avatar node `1:50` source/crop ve všech reuse kontextech. |
| `npm run contact-cta:hours` | Doplnit: ověří, že hours pill nevyteče z contact CTA a product sidebar používá shared hours partial. |
| `npm run contact-map:smoke` | Ověří contact map a musí se rozšířit o footer/showroom map CTA i map image/pin offset. |
| `npm run jucra:smoke` | Ověří builder, fallback, model selector a request flow. |
| `npm run final:qa` | Ověří finální stránkový smoke. |
| `npm run qa:local` | Kompletní lokální gate, po úpravě by měla volat i finální/Jucra guard nebo existuje samostatné `release:qa`. |
| `rg -n "Ã|Ä|Å|�|U\\+00C" wp-content/themes/arctic/modules/products/templates/post/single` | Rychlý statický check mojibake v detailových PHP partials; má být bez nálezů. |

## Neimplementovat

| Ne | Proč |
| --- | --- |
| Neukládat Figma token/hesla do repa | Bezpečnost a auditovatelnost. |
| Nepoužívat Figma mock produktové fotky jako owner-approved obsah | Figma může být design placeholder, ne zdroj reality pro produktovou galerii. |
| Nevymýšlet CSS ikonky tam, kde má Figma media/image vrstvu | To je přesně dluh z posledního auditu. |
| Neopravovat jen Timberwolf | Rozbije Lunar, Orion, Husky a swimspa. |
| Nezapínat swimspa konfigurátor bez scope rozhodnutí | Není potvrzený design ani Jucra model scope. |
| Nepřidávat další page-specific override bez usage mapy | Vrací se tím stejný drift, který starší plány řešily. |

## Otevřená Rozhodnutí Od Ownera/Klienta

| Otázka | Dopad |
| --- | --- |
| Má swimspa dostat vlastní Figma/design scope, nebo schválené zrcadlení hot-tub category contractu? | Určí `/swimspa/`, swimspa detail a konfigurátor. |
| Které product/detail galerie jsou finální owner-approved assety? | Určí hero, konfigurace, benefit media a listing kvalitu. |
| Dodají se team/contact portréty a warranty card media? | Jinak zůstane `WAITING_ON_OWNER`. |
| Má Jucra lead končit v Gravity Forms, email handoffu, nebo plugin vlastním formuláři? | Určí finální request URL a datové parametry. |
| Jaký přesný Google Maps URL/embed je produkční canonical? | Určí `baspa_map`, `arctic_map_embed` a map CTA guardy. |

## Poslední Známý Stav Ověření

| Oblast | Stav |
| --- | --- |
| Lokální web | `http://localhost:8090` vracel HTTP 200. |
| Smoke testy | `npm run final:qa`, `npm run jucra:smoke`, `npm run product-media:smoke`, `npm run component:smoke`, `npm run contact-map:smoke` prošly v posledním známém lokálním běhu. |
| Pracovní strom | Před vytvořením tohoto plánu už byl modifikovaný `docs/audit-detail-virivky-figma-2026-05-31.md` a existovalo `tmp/`; tento plán je nový soubor a nepřepisuje tyto změny. |

## Definition Of Done

Projekt je připravený k finálnímu předání až když platí všechny body níže.

| Bod | Done |
| --- | --- |
| 1 | Header `1:1832`, product hero a sticky product nav jsou fyzicky bez kolizí na desktopu i compact viewportu. |
| 2 | Product detail contract je sdílený pro hot tubs a má explicitní swimspa scope. |
| 3 | Product detail fyzicky sedí pro Timberwolf i non-Timberwolf fixture včetně McKinley, bez text-only konfigurace vydávané za Figma image card. |
| 4 | Product detail manifest a asset source map neobsahují falešné `available` stavy. |
| 5 | Benefit/options cards nepoužívají pseudoikony jako finální media. |
| 6 | Dušek avatar odpovídá Figma node `1:50` ve všech reuse kontextech. |
| 7 | Contact CTA hours a product sidebar hours jsou layoutově i datově sjednocené, bez overflow. |
| 8 | Google Maps contract je jednotný napříč contact map, footerem a showroom/location CTA. |
| 9 | Contact map má správný image offset/pin geometrii proti Figmě. |
| 10 | Footer používá Figma landscape background a CTA-to-footer handoff není navy override. |
| 11 | Jucra/Visao má pravdivý plugin/fallback stav, viditelné světlé swatche a produkční checklist. |
| 12 | `figma-detail-body.php` a detailové PHP partials jsou UTF-8 bez BOM a bez mojibake. |
| 13 | `qa:local` nebo `release:qa` spouští všechny relevantní guardy včetně finálního QA a Jucra. |
| 14 | Vizuální screenshot pass obsahuje `/virivky/`, `/swimspa/`, Timberwolf, McKinley, Lunar/Orion/Husky, jeden swimspa detail, `/kontakt/`, footer map a builder. |
| 15 | Chybějící owner assety jsou viditelně označené jako čekající, ne maskované náhradou. |
| 16 | Žádné nové heslo, token ani privátní Figma údaj nejsou v commitu. |
