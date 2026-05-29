# Wireframe / content source audit

Datum: 2026-05-29

Tento audit opravuje interpretaci zdroju. Figma texty nejsou finalni obsahova pravda. Figma ridi UX, wireframe, sekce, stavy a vizualni treatment. Obsahova pravda je stary Arctic web a klientské podklady. Baspa je technicky/admin workflow zaklad.

## Source-of-truth pravidla

| Vrstva | Autorita | Co se z ni bere | Co se z ni nebere |
|---|---|---|---|
| Figma wireframe | `Arctic-spas.cz wireframe` | architektura stranek, poradi sekci, UX patterny, modal/popup stavy, responzivni zamer | finalni texty a business obsah |
| Figma grafika | `Arctic-spas.cz grafika` | finalni vzhled, spacing, typografie, barvy, radiusy, stiny, media treatment | finalni realny obsah, pokud jde jen o placeholder/lorem |
| Stary Arctic | `../Arctic-spas/www` | produkty, popisy, reference, fotogalerie, FAQ, PDF, showroom/kontakt, pravni texty, stare URL | novy vizual ani komponentove chovani |
| Baspa | `../baspa.cz` / fork v `wp-content/themes/arctic` | CPT/admin workflow, formularovy a WP technicky model, lightbox/gallery patterny, customizer/settings | Arctic obsah ani Figma vizual |

## Brutalni zavery

1. Ano, obsah nesouvisi s Figmou tak, ze by se mel doslovne opisovat.
2. Ano, UX/wireframe je ve Figme a ma byt autorita pro strukturu a stavy.
3. Ano, wireframe ma samostatny `popup` frame a poznamku `otevira se do popupu`.
4. Ano, reference jsem mel vyhodnotit primarne jako archive/grid + lightbox/popup/fotogalerii, ne automaticky jako samostatne detailni stranky.
5. Ano, samostatne `single-reference.php` je zdedena Baspa moznost, ne prokazany Figma pozadavek.
6. Ano, stary Arctic obsah u referenci vypada spis jako kombinace `diskuze.php` textovych referenci a `fotogalerie-virivky.php` fotogalerie otevirane ve fancyboxu.

## Reference - nejvetsi logicky rozpor

| Zdroj | Co rika | Dopad |
|---|---|---|
| Figma wireframe `WF - REFERENCE` | Stranka `Reference` je archive/sberna stranka. Wireframe nerika "otevri standalone detail reference". | Lokalni `/reference/` ma byt grid/listing podle Figmy. |
| Figma grafika `REFERENCE` | Ukazuje grid referenci. Neprokazuje potrebu samostatnych detailnich stranek. | Klik do single detailu neni z grafiky zjevny. |
| Stary Arctic `diskuze.php` | Obsahuje textove reference/zkusenosti zakazniku pod jednou strankou. | Obsahove patri na `/reference/`, ne nutne na detail kazde reference. |
| Stary Arctic `fotogalerie-virivky.php` | Fotky jsou galerie a oteviraji se pres `rel="fancybox"`. | Interakce ma byt galerie/lightbox/popup, ne povinne nove URL detailu. |
| Local `template-references.php` | Realne reference jsou `<a class="f-reference-card" href="get_permalink()">`. | Local zavadi standalone detailni stranky. To je pravdepodobne spatne. |
| Local seed `seed-pilot-content.php` | Nastavuje `reference_single = 1`. | Tim aktivne tlaci Baspa single-reference workflow. |
| Local Baspa template | Pri `reference_single = 0` obrazek otevira PhotoSwipe; pri `1` vede na single detail. | Spravna korekce je nejspis prepnout reference na archive + lightbox/popup, ne detail URL. |

Verdikt:

`single-reference.php` muze zustat technicky jako admin/legacy fallback, ale UX default pro klientsky web nema byt standalone reference detail, pokud to klient vyslovne nechce. Frontend karty referenci maji byt:

- bud neklikaci vizualni karty,
- nebo otevirat galerii/lightbox,
- nebo otevirat Figma-like popup,
- ne automaticky navigovat na `/project/...`.

## Popup - wireframe vs local

| Zdroj | Co rika | Local stav | Problem |
|---|---|---|---|
| Wireframe `popup` | Existuje samostatny modal detail s close buttonem, obrazkem a textem. | Local ma `f-off--benefit-popup` v `templates/section/product-benefits.php`. | Pattern existuje, ale jen pro jednu benefit kartu. |
| Wireframe anotace | `otevira se do popupu >` | Local ma jen jednu kartu s `popup => true`; ostatni benefit karty vypadaji klikatelne pluskem, ale nemaji trigger. | UX klame: plus u vsech, popup jen u jedne. |
| Final grafika `popup` | Dark overlay + white rounded modal + cerveny close. | Local popup se musi pixelove porovnat, ale struktura je aspon podobna. | Opravit vizual a rozhodnout rozsah popupu pro benefit karty. |

Verdikt:

Popup neni volitelna dekorace. Je to wireframe stav. Pokud benefit karta vypada jako "vice info", musi bud vsechny relevantni karty otevrit popup/detail, nebo nemaji mit affordanci plus.

## Homepage - wireframe vs local vs stary obsah

| Cast | Wireframe zamer | Local | Hodnoceni |
|---|---|---|---|
| Hero | Slider/hero s CTA a promo prvkem. | Local ma hero, ale crop/scale a promo mobile/desktop nejsou stabilni. | UX struktura sedi, visual ne. |
| Kategorie | Dve hlavni karty `Virivky` a `Celorocni bazeny`. | Local ma karty. | Wireframe sedi, ale final grafika/cropy/arrow treatment musely byt opravovane. |
| Dealer/about intro | Blok `Jsme vyhradni prodejce`. | Local ma content area a benefit bloky. | Obsah ma jit ze stareho Arctic/owner, ne z lorem. |
| Sluzby mini karty | Montaz/podpora/servis. | Local ma sekce/sluzby podle implementace. | Wireframe zamer ano, visual/assety nutno hlidat. |
| Showroom | Collage + showroom CTA. | Local ma opakovane rozbity/missing collage media. | P0 visual/wireframe chyba. |
| Prubeh zakazky | 6 kroku. | Local ma progress sekci. | Struktura sedi, text obsahove muze byt old/owner. |
| Reference | `Priklady realizaci`, slider pokud vice polozek. | Local ma carousel/grid reference. | Wireframe ok, ale interakce/detail stranky spatne riziko. |

## Kategorie - wireframe vs local

| Cast | Wireframe zamer | Local | Hodnoceni |
|---|---|---|---|
| Produktovy listing | Karty produktu s obrazkem a nazvem. | Cast produktu ma prazdne media. | P0, protoze obsahovy zdroj existuje ve starem Arctic/importech. |
| Prubeh zakazky | Ve wireframu kategorie je velky blok procesu. | Local ma `templates/section/progress`, ale az po showroom/configurator. | Zkontrolovat poradi proti wireframu/grafice; nesmi se ridit jen Baspa flow. |
| Konfigurator | CTA blok podle Figma. | Cast localu je plain red placeholder. | P0 visual/UX. |
| Showroom | Shared showroom CTA. | Missing image layers. | P0. |
| Reference | Listing/slider, ne nutne detail. | Local jde pres reference module. | Interakce musi byt lightbox/popup/archive, ne single default. |

## Podpora / Ke stazeni - wireframe vs local vs stary Arctic

| Zdroj | Co rika | Local | Hodnoceni |
|---|---|---|---|
| Wireframe/grafika | FAQ/downloads jako accordion/filter UI. | Local uz ma `support-download-interactions.js`. | Funkcne se to posunulo spravne, ale visual spacing/radius porad musi sedet. |
| Stary Arctic `faq.php` | Velke FAQ s filtrem a accordion collapse. | Local tahá FAQ CPT/fallback. | Obsahove spravne smerovani. |
| Stary Arctic `download.php` | Downloady jako accordions + filtry. | Local ma Download CPT a skupiny. | Technologicky dobry smer, visual musi sedet s Figmou. |

Verdikt:

Tady neni spravne kritizovat, ze texty nejsou lorem z Figmy. Spravne je hlidat:

- accordion/chips musi byt funkcni,
- struktura a vzhled podle Figmy,
- obsah/PDF z old Arctic,
- zadny Baspa business obsah nebo placeholder.

## Produkt detail - wireframe vs local vs stary Arctic

| Cast | Wireframe zamer | Local | Hodnoceni |
|---|---|---|---|
| Produkt detail | Figma definuje strukturu detailu a modal/popup stav. | Local ma detail, nav, product content, colors, benefits, options, references. | Zaklad existuje. |
| Produktova data | Stary Arctic je obsahova autorita. | Import ma produkty a parametry, ale cast image/swatch medii je prazdna. | P0 asset mapping/content-media chyba. |
| Popup benefit | Wireframe ma popup. | Local ma jeden popup, ostatni plusky bez stejneho chovani. | P0/P1 UX affordance problem. |
| Reference u produktu | Wireframe/grafika ukazuje reference jako sekci. | Local pouziva references recent. | OK jen pokud interakce neskace do nechtěnych single detailu. |

## O nas / Kontakt / Showroom

| Stranka | Wireframe/grafika zamer | Old Arctic/owner obsah | Local problem |
|---|---|---|---|
| Showroom | Vizuální showroom bloky a kontaktni kompozice. | `prodejna-bazeny-virivky.php` + owner showroom fotky. | Local pouziva spatne/chybejici fotky v nekterych blocich. |
| Kontakt | Mapa + kontaktni osoby/karty podle Figmy. | `kontakt.php`, legal BASPA s.r.o., Bohunicka cesta, telefony. | Local ma misty departement/inicialy misto osob/fotek; mapa treatment nesedi. |
| O nas | Team/cards/stats podle Figmy. | `baspa.php`, owner info, team/person assets. | Local team media je rozbite; stats barvy nesedi. |

## Co bylo spatne v mem predchozim mentalnim modelu

1. Chyba: flagovat obsah jen proto, ze neni jako Figma lorem/text.
   Spravne: flagovat obsah jen kdyz neni ze stareho Arctic/owner zdroje, je placeholder, chybi, nebo je Baspa-nearctic business copy.

2. Chyba: brat Baspa `single-reference` jako automaticky validni UX.
   Spravne: Baspa je workflow moznost; Figma/stary Arctic ukazuji spis archive + gallery/lightbox/popup.

3. Chyba: u referenci tolerovat detailni permalink bez explicitniho dukazu.
   Spravne: default ma byt grid/stack + lightbox/popup, nebo neklikaci karty.

4. Chyba: hodnotit Figma obrazky jako final obsah ve vsech pripadech.
   Spravne: Figma urcuje rozmer, crop, ostrost, overlay a treatment. Realne fotky se maji brat ze stareho Arctic/owner zdroju, ale musi sedet do Figma componentu.

## Upraveny seznam P0 podle spravne interpretace

| ID | P0 problem | Proc |
|---|---|---|
| SRC-01 | Reference karty defaultne linkuji na standalone detail. | Figma to neprokazuje, stary Arctic ukazuje reference/fotogalerii jako archive/lightbox. |
| SRC-02 | Seeder nastavuje `reference_single = 1`. | Aktivuje single reference workflow, ktery je pravdepodobne proti UX zameru. |
| SRC-03 | Reference archive v `template-references.php` obaluje realne karty permalinkem. | Obchazi Baspa lightbox variantu a dela z gridu detailni navigaci. |
| SRC-04 | Popup affordance na benefit kartach neni konzistentni. | Wireframe ma popup stav, local ma plusky, ale trigger jen u jedne karty. |
| SRC-05 | Missing media nejsou content-vs-Figma problem, ale mapping problem. | Old Arctic/owner media existuji, local je nepouziva spravne nebo je renderuje prazdne. |
| SRC-06 | Showroom/contact/about assety musi jit z owner/old Arctic, ne z placeholderu ani nahodnych fallbacku. | Figma definuje kompozici, old/owner definuje realny obsah. |
| SRC-07 | Kde Figma ukazuje slider/popup stav, local nesmi byt staticky nebo samovolne navigovat jinam. | UX stav je autoritativni. |

## Dalsi oprava auditu

Puvodni `brutal-figma-audit-2026-05-29.md` je pouzitelny pro vizualni a komponentove chyby, ale u content copy je potreba ho cist s touto korekci:

- text/copy mismatch proti Figme neni chyba sam o sobe,
- missing/truncated content proti staremu Arctic je chyba,
- placeholder/lorem/Baspa business content je chyba,
- realne fotky mohou byt jine nez ve Figme, ale musi mit Figma rozmery, crop, sharpness a treatment.

