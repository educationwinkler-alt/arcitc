# Jucra / Visao 3D konfigurator - end-to-end repair plan

Datum: 2026-05-31

## Cil

Dostat stranku `/konfigurator/` a `/konfigurator/{model}/` do produkcne pouzitelneho stavu tak, aby:

- konfigurator nebyl fake ani dvojity,
- JUCRA/Visao plugin byl zdroj pravdy pro 3D viewer a realne modelove volby,
- web mel ciste ceske Arctic UI bez anglickych zbytku tam, kde je to technicky mozne,
- category/product CTA pouze navigovaly do realneho konfiguratoru,
- kazda uprava byla vizualne overena v browseru, ne jen smoke testem.

## Stav pred opravou

| Oblast | Stav |
| --- | --- |
| Plugin | `visao-3d-viewer` v1.26 je lokalne nainstalovany a aktivni. |
| Shortcode v KB | JUCRA KB uvadi `[visao_viewer model_name="MODELNAME"]`. |
| Shortcode v dodanem ZIPu | Plugin realne registruje `[visao_builder]`. Theme uz musi podporovat oba nazvy. |
| Data zdroj | Plugin taha JSON z `https://api.arcticspascore.com/live/jsons/visao-3d-viewer.php?model_name=...`. |
| Aktualni chyba | Stranka renderuje realny JUCRA plugin a soucasne vlastni cesky panel. To je dvojity konfigurator. |
| Aktualni UX | Model selector ma nativni horizontalni scrollbar, plugin je spatne vlozeny do karty, CTA jsou duplicitni. |

## Zdroj pravdy

### 1. JUCRA plugin

Plugin je primarni runtime zdroj pro:

- 3D iframe viewer,
- dostupne trysky / varianty,
- dostupne shell/acrylic barvy,
- dostupne cabinet barvy,
- request/pricing data, pokud plugin podporuje jejich predani.

### 2. JSON API

Kazdy model ma samostatny JSON endpoint:

```text
https://api.arcticspascore.com/live/jsons/visao-3d-viewer.php?model_name=Summit%20XL
```

Overeny obsah pro `Summit XL`:

- `general_responses`
- `tub_details`
- `options_acrylics`
- `options_jets`
- `options_cabinets`
- `options_sds`
- `features_optional`
- `phrases`
- `logging`

JSON se smi pouzit pro validaci, model mapping, pripadne server-side cache pro navigaci nebo kontrolu dostupnosti. Nesmime podle nej vyrabet paralelni UI, ktere se pak rozjede od pluginu.

### 3. Model selector

JUCRA plugin podle kodu nacita model pres `model_name` pri renderu shortcode. Neni potvrzene, ze umi uvnitr jednoho shortcode prepnout z jednoho typu virivky na jiny bez reloadu.

Spravny kontrakt:

```text
/konfigurator/summit-xl/  -> [visao_builder model_name="Summit XL"]
/konfigurator/summit/     -> [visao_builder model_name="Summit"]
/konfigurator/tundra/     -> [visao_builder model_name="Tundra"]
```

Model selector je tedy navigace mezi URL, ne vlastni JS prepinani modelu.

## Nevyjednatelna pravidla

1. Na strance nesmi byt dva konfiguratory najednou.
2. Vlastni cesky panel nesmi duplikovat volby pluginu, pokud neni primo napojen na stejny zdroj a otestovan.
3. Plugin je zdroj pravdy pro realne konfiguracni volby.
4. Kategorie `/virivky/` muze mit konfigurator CTA, ale nesmi renderovat fake builder.
5. `/swimspa/` nesmi dostat konfigurator automaticky, dokud neni potvrzeno, ze je scope a data pro swimspa.
6. Pricing/form URL musi byt relativni cesta, napr. `/kontakt/` nebo cilova poptavkova stranka.
7. Vendor plugin se nema upravovat primo, pokud to neni posledni moznost. Preferovane jsou nastaveni pluginu, wrapper CSS a theme integrace.
8. Po kazde CSS/template zmene musi vzniknout browser screenshot a manualni vizualni kontrola.

## Aktualni problemy k oprave

| # | Problem | Dopad |
| --- | --- | --- |
| 1 | Realny JUCRA plugin a vlastni cesky konfigurator bezi soucasne. | Dve pravdy, duplicitni data, rozbite UX. |
| 2 | Stranka micha cestinu a anglictinu. | Nedokonceny produkcni stav. |
| 3 | Existuji dve CTA pro cenu: plugin `Request Pricing` a nase `Vyzadat cenovou nabidku`. | Nejasna konverzni akce. |
| 4 | Model selector ma nativni horizontalni scrollbar. | Vizuálně mimo design, pusobi jako rozbite. |
| 5 | Hero/title cast je prilis vysoka. | Realny builder zacina moc nizko. |
| 6 | Plugin je vtazeny do nasi bile karty a jeho obsah je spatne centrovany / oriznuty. | Builder nevypada jako funkcni produktovy nastroj. |
| 7 | Pravy panel je roztazeny do zbytecne vysky. | Velke prazdne plochy, spatny grid. |
| 8 | Nase lokalni volby neodpovidaji plugin volbam. | Napr. `3 cerpadla` vs realne varianty pluginu. |
| 9 | Plugin zobrazuje anglicke texty a zbytky typu browser disclaimer / version info. | Nedodelane. |
| 10 | Category konfigurator banner na `/virivky/` ma rozbite spacing/layout proti Figme. | Vizuální bug mimo samotny builder. |
| 11 | Product/detail CTA musi vest na konkretni model, ne obecny fake stav. | Konverzni cesta muze byt nepresna. |
| 12 | Testy kontroluji hlavne existenci, ne vizualni shodu a nedvojity builder. | QA propousti spatny stav. |

## Cilova architektura

### Doporucena varianta A: plugin-owned builder

Toto je preferovana cesta.

Stranka obsahuje:

1. Arctic header.
2. Kompaktni uvod konfiguratoru.
3. Arctic model selector jako navigaci mezi `/konfigurator/{model}/`.
4. Jeden realny JUCRA shortcode pro aktualni model.
5. Jedno pricing/request CTA podle pluginu nebo podle dohodnuteho formulare, ne obe naraz.
6. Zadny vlastni paralelni panel s tryskami/barvami.

Vyhoda:

- nejmensi riziko rozchodu s JUCRA daty,
- nejbliz realne podporovanemu plugin flow,
- rychlejsi stabilizace.

Nevyhoda:

- mensi kontrola nad vnitrnim vzhledem pluginu,
- preklad plugin textu muze byt omezeny podle moznosti pluginu/API.

### Varianta B: Arctic wrapper nad JUCRA daty

Pouzit jen pokud klient opravdu chce plne custom UI.

Stranka obsahuje:

1. Arctic UI pro volby.
2. Data tahana z JUCRA JSON API.
3. Napojeni na Visao API uvnitr iframe / vieweru.
4. Synchronizaci vybranych voleb do pricing URL/formulare.

Tato varianta je vetsi mini-projekt a nesmi se delat jako kosmeticky fix. Aktualni stav neni hotova varianta B, je to nebezpecny hybrid.

## Implementacni bloky

### Blok 0 - baseline a ochrana proti dalsimu rozbiti

| Krok | Prace | Overeni |
| --- | --- | --- |
| 0.1 | Ulozit aktualni screenshoty `/konfigurator/` a `/konfigurator/summit-xl/` desktop/mobile. | Screenshoty v `docs/screenshots/`. |
| 0.2 | Zmerit DOM: pocet shortcode wrapperu, pocet iframe, pocet pricing CTA, vyska builder sekce. | Playwright metrics JSON. |
| 0.3 | Zapsat aktualni problem jako failing checklist. | Tento plan + pripadne QA pozn. |

Exit criteria:

- mame baseline, aby bylo jasne, co se opravuje,
- neni zadna dalsi zmena bez screenshotu.

### Blok 1 - odstranit hybridni konfigurator

| Krok | Prace | Soubor |
| --- | --- | --- |
| 1.1 | Zrusit nebo vypnout vlastni pravy panel s lokalnimi volbami. | `templates/section/jucra-builder.php` |
| 1.2 | Nechat renderovat pouze realny JUCRA shortcode pro aktualni model. | `templates/section/jucra-builder.php` |
| 1.3 | Pokud plugin chybi, zobrazit pouze truthful fallback `WAITING_ON_JUCRA_PLUGIN`. | `templates/section/jucra-builder.php` |
| 1.4 | Odebrat JS, ktery se tvari jako vlastni konfiguracni logika, pokud neni realne potreba. | `dist/js/jucra-builder.js`, `inc/scripts.php` |

Overeni:

- Na `/konfigurator/summit-xl/` je prave jeden realny plugin output.
- Na strance neni vlastni panel `Vybrany model / Trysky / Barva skorepiny`.
- Neexistuji duplicitni konfiguracni volby mimo plugin.

### Blok 2 - model selector jako navigace, ne fake prepinac

| Krok | Prace | Soubor |
| --- | --- | --- |
| 2.1 | Potvrdit seznam modelu proti JSON API. | `inc/functions/jucra.php` |
| 2.2 | Model selector renderovat jako odkazy na `/konfigurator/{slug}/`. | `templates/section/jucra-builder.php` |
| 2.3 | Aktivni stav odvozovat z URL/modelu. | `inc/functions/jucra.php` |
| 2.4 | Odstranit nativni scrollbar nebo ho nahradit designovym horizontalnim scrollem/fade/arrow patternem. | `_component-contracts.less` nebo relevantni LESS |

Overeni:

- Klik na `Summit` vede na `/konfigurator/summit/`.
- Klik na `Tundra` vede na `/konfigurator/tundra/`.
- Shortcode na cilove URL obsahuje spravny `model_name`.
- Na desktopu neni videt hnusny browser scrollbar.
- Na mobilu je model selector ovladatelny a nepreteká.

### Blok 3 - plugin settings, CTA a jazyk

| Krok | Prace | Soubor / misto |
| --- | --- | --- |
| 3.1 | Overit skutecny tvar optionu pluginu pro `hide_get_pricing` a `hide_version_info`. | WordPress options + plugin code |
| 3.2 | Pokud plugin setting nefunguje, zjistit proc. Nehackovat naslepo CSS. | `visao-3d-viewer.php`, WP option |
| 3.3 | Rozhodnout jedno CTA: plugin pricing button nebo nase externi CTA/formular. | scope decision |
| 3.4 | Nastavit Form Page URL relativne, napr. `/kontakt/` nebo cilova poptavkova stranka. | plugin settings |
| 3.5 | Schovat nebo prelozit produkcne nevhodne plugin texty. | plugin settings / CSS editor / wrapper CSS |
| 3.6 | Zkontrolovat, zda plugin podporuje jazyk `cs`. Pokud ne, zdokumentovat limit. | Visao language API |

Overeni:

- Na strance je jen jedno cenove CTA.
- Neni videt `Request Pricing`, pokud vybrana cesta je ceske CTA.
- Neni videt version/debug/cache/changelog info pro bezneho uzivatele.
- Neni videt zbytecny anglicky disclaimer, pokud ho jde korektne schovat.

### Blok 4 - layout integrace builderu

| Krok | Prace | Soubor |
| --- | --- | --- |
| 4.1 | Zmenit layout tak, aby plugin nebyl utopeny v male karte. | LESS + template |
| 4.2 | Udelat builder jako hlavni obsahovy blok s dostatecnou sirkou. | LESS |
| 4.3 | Zkratit hero uvod, aby se viewer dostal vyse nad fold. | LESS/template |
| 4.4 | Vyresit responsivni chovani iframe/plugin obsahu. | LESS |
| 4.5 | Vyresit handoff na dalsi sekci pod builderem. | LESS |

Overeni:

- Na 1920px desktopu je pluginovy builder viditelny bez pocitu, ze zacina prilis nizko.
- Plugin obsah neni oriznuty.
- Nejsou obri prazdne bile plochy.
- Na mobilu se viewer a volby skladaji citelne pod sebe.

### Blok 5 - data validace a model mapping

| Krok | Prace | Soubor |
| --- | --- | --- |
| 5.1 | Pro kazdy model v nasem seznamu zavolat JSON API a overit `tub_exists=true`. | novy tool/script |
| 5.2 | Pokud model neexistuje v API, nevykreslovat ho v selectoru. | `inc/functions/jucra.php` |
| 5.3 | Ulozit mapu `slug -> model_name -> product_slug`. | `inc/functions/jucra.php` / docs |
| 5.4 | U product detailu overit `jucra_model_name`. | product meta / seed |
| 5.5 | Nezobrazovat swimspa modely, dokud nejsou potvrzene. | guard |

Overeni:

- Kazdy odkaz v model selectoru vede na validni model.
- Nevalidni model neskonci tichym broken embedem.
- Product CTA `Nakonfigurovat` vede na spravny model.

### Blok 6 - category a product CTA napojeni

| Krok | Prace | Soubor |
| --- | --- | --- |
| 6.1 | Opravit `/virivky/` konfigurator banner podle Figma kompozice. | `templates/section/configurator.php`, LESS |
| 6.2 | Odstranit obri mezeru mezi product gridem a bannerem. | category LESS |
| 6.3 | Zajistit, ze banner vede na `/konfigurator/` nebo defaultni model podle zadani. | template |
| 6.4 | Product detail CTA vede na `/konfigurator/{model}/`. | product detail template |
| 6.5 | `/swimspa/` nema konfigurator, pokud neni scope potvrzen. | taxonomy/template guard |

Overeni:

- `/virivky/` sedi vizualne proti Figma CTA banneru.
- `/virivky/` nema obri prazdnou mezeru pred bannerem.
- `/swimspa/` nema nepovolenou verzi banneru.
- Product detail Timberwolf vede na `/konfigurator/timberwolf/`.

### Blok 7 - formulare a lead handoff

| Krok | Prace | Misto |
| --- | --- | --- |
| 7.1 | Rozhodnout cil poptavky: plugin modal, Gravity Forms, nebo `/kontakt/`. | owner / implementace |
| 7.2 | Pokud Gravity Forms: nastavit `Gravity Forms ID` a `Gravity Forms Field ID`. | plugin settings |
| 7.3 | Pokud kontakt page: pricing URL musi mit parametry `model_name` a vybrane volby, pokud plugin umi. | plugin/API |
| 7.4 | Otestovat submit/handoff na lokalnim nebo staging prostredi. | manual QA |

Overeni:

- Klik na cenove CTA vede na spravny formular.
- Model se prenasi do formulare nebo query parametru.
- Neexistuji dve ruzne poptavkove cesty.

### Blok 8 - QA guardy

| Krok | Prace | Soubor |
| --- | --- | --- |
| 8.1 | Smoke test musi failnout, pokud existuje `WAITING_ON_JUCRA_PLUGIN` pri aktivnim pluginu. | `tools/component-contract-smoke.js` |
| 8.2 | Smoke test musi failnout, pokud jsou na builderu dve pricing CTA. | `tools/component-contract-smoke.js` |
| 8.3 | Smoke test musi failnout, pokud se vykresli vlastni side panel i plugin soucasne. | novy nebo stavajici smoke |
| 8.4 | Link smoke musi overit vsechny `/konfigurator/{model}/` URL. | `tools/link-smoke.js` |
| 8.5 | Visual test musi ukladat screenshoty builderu desktop/mobile. | `tools/visual-smoke.js` |

Overeni:

- `npm run component:smoke`
- `npm run link:smoke`
- visual screenshot sada pro `/konfigurator/`, `/konfigurator/summit-xl/`, `/konfigurator/timberwolf/`, `/virivky/#konfigurator`

### Blok 9 - produkcni checklist

| Krok | Prace |
| --- | --- |
| 9.1 | Nainstalovat stejny JUCRA plugin ZIP na produkci/staging. |
| 9.2 | Aktivovat plugin. |
| 9.3 | Nastavit `Hide Version Section`, pricing/form URL a pripadne Gravity Forms pole. |
| 9.4 | Overit, ze produkce umi volat `api.arcticspascore.com` a `demo.visao.ca` / Visao viewer domenu. |
| 9.5 | Overit cookie/cache pravidla, protoze plugin sam resi cache/no-cache. |
| 9.6 | Udelat final desktop/mobile visual pass. |

## Visual verification pravidlo

Po kazde sebemensi zmene v techto souborech:

- `templates/section/jucra-builder.php`
- `templates/section/configurator.php`
- `inc/functions/jucra.php`
- `src/less/_component-contracts.less`
- `dist/css/arctic.css`
- `dist/js/jucra-builder.js`

se musi udelat:

1. Browser reload dane URL.
2. Screenshot desktop.
3. Screenshot mobile.
4. Manualni kontrola proti zdroji:
   - `/virivky/` banner proti Figma kategorii,
   - `/konfigurator/{model}/` proti JUCRA/ArcticSpas.com builder patternu,
   - product detail CTA proti Figma detailu a URL contractu.

Bez screenshotu se blok nepovazuje za hotovy.

## Definition of done

Stranka konfiguratoru je hotova az kdyz plati vse:

- `/konfigurator/` vraci 200 a neni to 404 fallback.
- `/konfigurator/summit-xl/` vraci 200 a renderuje realny plugin output.
- Na strance je jen jeden konfigurator.
- Na strance je jen jedno pricing/request CTA.
- Model selector je navigace mezi modelovymi URL.
- Neni videt nativni horizontalni scrollbar na desktopu.
- Neni videt `WAITING_ON_JUCRA_PLUGIN`, pokud je plugin aktivni.
- Neni videt debug/version/cache/changelog info pro bezneho uzivatele.
- Category `/virivky/` ma Figma-compatible konfigurator CTA bez obri mezery.
- Category `/swimspa/` nema konfigurator bez scope potvrzeni.
- Product detail CTA vede na spravny model.
- Component smoke, link smoke a visual screenshots jsou aktualni.
- Produkcni instalace pluginu ma zdokumentovane settings.

## Stav implementace 2026-05-31

Hotovo lokalne:

- Blok 1: hybridni konfigurator je odstranen. Builder stranka uz nema vlastni paralelni panel `Vybrany model / Trysky / Barva skorepiny / Barva kabinetu`.
- Blok 2: model selector funguje jako URL navigace na `/konfigurator/{model}/`; shortcode dostava model podle aktualni URL.
- Blok 3: plugin output je lokalizovany theme wrapperem, pricing CTA je sjednocene na ceskou variantu a vede pres relativni `/kontakt/` s parametry z pluginu.
- Blok 4: builder layout je upraveny jako jeden hlavni plugin-owned surface; nativni scrollbar model stripu je na desktopu schovany.
- Blok 5: dostupnost modelu byla overena proti JUCRA API `api.arcticspascore.com`; modely bez potvrzeneho scope se do swimspa nevkladaji.
- Blok 6: category/product CTA uz nevklada shortcode ani fake builder, jen naviguje na realny konfigurator a pouziva Figma/owner vizual.
- Blok 8: smoke/visual guardy hlidaji, ze se nevrati lokalni custom panel, duplicitni builder ani anglicke plugin CTA.

Overeno:

- `npm run css:build`
- `npm run component:smoke`
- `npm run link:smoke`
- `npm run pr0-pr4:visual`
- `npm run visual:smoke`
- PHP lint pro upravene PHP sablony a helpery
- Manualni browser screenshoty: `docs/screenshots/jucra-builder-summit-xl-after.png` a `docs/screenshots/virivky-configurator-after-loaded.png`

Poznamka k visual smoke:

- Full visual smoke blokuje tezke externi JUCRA/Visao runtime requesty, aby test nepadal na vendor network/3D runtime. Realny plugin render byl proto overeny samostatne targeted screenshotem s aktivnim pluginem.

Zbyva pro produkci:

- Nainstalovat a aktivovat stejny ZIP pluginu na staging/produkci.
- Nastavit `Hide Version Section`, `Form Page URL = /kontakt/` a pripadne Gravity Forms ID/Field ID podle finalniho lead flow.
- Overit, ze produkce smi volat JUCRA/Visao API domeny.

## Co neni soucasti tohoto planu

- Vyroba vlastniho 3D configurator engine.
- Manualni kopirovani 3D assetu z Visao.
- Vkladani swimspa konfiguratoru bez potvrzeneho scope.
- Prepis vendor pluginu, pokud to neni explicitne schvalene jako vendor patch.

## Doporučené pořadí práce

1. Blok 0 - baseline.
2. Blok 1 - odstranit hybrid a nechat jeden zdroj pravdy.
3. Blok 2 - model selector jako URL navigace.
4. Blok 3 - CTA, jazyk a plugin settings.
5. Blok 4 - layout builderu.
6. Blok 5 - data/model mapping.
7. Blok 6 - category/product CTA.
8. Blok 8 - guardy.
9. Blok 9 - produkcni checklist.

Blok 7 se dodela podle rozhodnuti, kam ma lead realne odchazet.
