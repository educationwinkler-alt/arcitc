# Detailní plán oprav podle Figma/wireframe/content auditů

Datum: 2026-05-29  
Vstupy:

- `docs/brutal-figma-audit-2026-05-29.md`
- `docs/wireframe-content-source-audit-2026-05-29.md`
- `docs/web-finalization-master-plan-2026-05-26.md`
- `docs/content-parity-audit-2026-05-26.md`
- `docs/admin-parity-matrix-2026-05-26.md`

## Cíl

Dostat local do stavu, kdy:

- UX a wireframe odpovídá Figmě.
- Finální vizuál odpovídá Figma grafice.
- Obsah je ze starého Arctic webu nebo z klientských podkladů.
- Admin/technologický workflow zůstává Baspa-like tam, kde je to relevantní.
- Neexistují fake-interakce, prázdné media bloky, náhodné fallback ikony ani samovolně domyšlené detailní stránky.

## Nevyjednatelná pravidla

| Pravidlo | Význam |
|---|---|
| Figma wireframe řídí UX. | Pořadí sekcí, modal/popup stavy, archive vs detail, slider vs static, responsive záměr. |
| Figma grafika řídí vzhled. | Spacing, radius, barvy, typografie, stíny, crop, overlay, image treatment. |
| Starý Arctic řídí obsah. | Texty, produkty, reference, FAQ, PDF, showroom/kontakt, právní texty, fotogalerie. |
| Baspa řídí technologii/admin. | CPT, metabox workflow, settings, formuláře, lightbox/gallery technika. |
| Figma text není final copy. | Text se neflaguje jen proto, že se liší od Figmy. Flaguje se, když chybí vůči old Arctic/owner zdroji nebo je placeholder. |
| Figma obrázek není vždy final foto. | Reálné foto může být jiné, ale musí sedět rozměr, ostrost, crop a component treatment. |
| Žádné nevhodné fallbacky. | Když chybí fotka, použít rozměrově správný neutrální placeholder nebo relevantní fallback foto, ne náhodné ikony. |
| Klientské rozhodnutí se nesmí maskovat jako technický fix. | Pokud Figma neukazuje samostatný UX tok, implementujeme výchozí wireframe chování a alternativu bereme jako nový potvrzený scope. |

## Prioritní pořadí oprav

| Pořadí | Balík | Priorita | Proč první |
|---:|---|---:|---|
| 1 | Source/UX contract hardening | P0 | Zabrání dalšímu míchání Figma copy, Baspa workflow a Arctic obsahu. |
| 2 | Reference UX reset | P0 | Teď local zavádí standalone detailní stránky, které Figma neprokazuje; single detail je pouze klientské rozhodnutí / nový mini-scope. |
| 3 | Media/asset mapping | P0 | Prázdné obrázky a špatné assety ničí většinu stránek; chybějící owner assety se nesmí domýšlet. |
| 4 | Shared visual components | P0 | Showroom, CTA/footer, configurator, popup a cards se opakují napříč webem. |
| 5 | Page-specific P0 stránky | P0 | Záruka, údržba, kontakt, o nás, showroom, produkt detail mají vlastní větší odchylky. |
| 6 | Mobile/responsive parity | P0/P1 | Mobile homepage a menu musí sedět po velkých strukturálních opravách. |
| 7 | QA/sign-off automation | P1 | Aby se znovu nestalo, že gate projde a vizuál je mimo. |

## Repair Wave 0 - Freeze a bezpečnostní baseline

Priorita: P0  
Typ: přípravný krok  
Cíl: neměnit dál bez měřitelného baseline.

### Kroky

| Krok | Akce | Výstup |
|---:|---|---|
| 1 | Uložit aktuální screenshot evidence z auditů jako referenci. | `docs/screenshots/brutal-figma-audit-2026-05-29/` |
| 2 | Označit předchozí frame tracker jako neautoritatívní pro finální pass. | Poznámka v master planu nebo nový sign-off doc. |
| 3 | Zavést nový repair checklist podle tohoto dokumentu. | Tabulka P0/P1 oprav s checkboxy. |
| 4 | Před každou opravnou vlnou ověřit čistý nebo pochopený git stav. | Žádné nechtěné revertování. |

### Acceptance criteria

| Kritérium | Stav |
|---|---|
| Existuje jeden plán oprav, podle kterého se jede. | Tento dokument. |
| Vizuální pass se už neopírá pouze o staré automatické `figma:audit`. | Musí se potvrdit screenshotem. |
| Zdrojová pravidla jsou explicitní. | Viz sekce výše. |

## Repair Wave 1 - Source/UX contract hardening

Priorita: P0  
Typ: dokumentace + guardy + audit cleanup  
Cíl: zabránit opakování špatného výkladu zdrojů.

### Kroky

| Krok | Akce | Dotčené části |
|---:|---|---|
| 1 | Upravit master plan tak, aby odkazoval na nový source audit a repair plan. | `docs/web-finalization-master-plan-2026-05-26.md` |
| 2 | Přidat "content vs visual" pravidlo do QA checklistu. | `docs/phase-5-...`, nový sign-off checklist |
| 3 | Přepsat P0 audit položky, které chybně flagují pouze rozdíl proti Figma copy. | `docs/brutal-figma-audit-2026-05-29.md` nebo nový append-only errata |
| 4 | V QA pravidlech rozlišit typ chyby: `visual`, `wireframe`, `content-source`, `admin-workflow`, `interaction`. | nový checklist |

### Acceptance criteria

| Kritérium | Ověření |
|---|---|
| Nikdo už nebere Figma lorem/copy jako obsahovou pravdu. | Checklist obsahuje source-role pravidlo. |
| Každý nález má typ chyby. | P0/P1 tabulka je kategorizovaná. |
| Figma vs content konflikty mají rozhodovací pravidlo. | Figma = UX/vizuál, old Arctic = obsah, Baspa = workflow. |

## Repair Wave 2 - Reference UX reset

Priorita: P0  
Typ: template + seed + interaction  
Cíl: reference nejsou defaultně standalone detailní stránky. Výchozí implementace má odpovídat archive/grid + případný lightbox/popup/fotogalerie záměr. Samostatný detail reference je legitimní jen po explicitním potvrzení klientem/vlastníkem.

### Rozhodovací rámec

| Varianta | Stav | Důsledek |
|---|---|---|
| Archive/grid na `/reference/` bez single detailů | Výchozí podle současné Figmy. | Implementovat jako PR-B. |
| Archive/grid + popup/lightbox | Přípustné, pokud se potvrzuje interakce s galerií. | Implementovat v PR-B jako interakční pattern, ne jako novou URL. |
| Samostatné portfolio stránky `/project/...` | Klientské rozhodnutí / nový mini-scope. | Neimplementovat jako default; vyžaduje návrh UX, obsah, SEO rozhodnutí a QA. |

### Problém

Aktuální local:

- `template-references.php` dává reálným referencím `<a class="f-reference-card" href="get_permalink()">`.
- `tools/seed-pilot-content.php` nastavuje `reference_single = 1`.
- `single-reference.php` je tím aktivně dostupný frontend pattern.

Figma/old Arctic záměr:

- `WF - REFERENCE` je sběrná/archive stránka.
- `REFERENCE` grafika ukazuje grid.
- old Arctic `diskuze.php` je stránka textových referencí.
- old Arctic `fotogalerie-virivky.php` používá fancybox/lightbox.

### Kroky

| Krok | Akce | Soubory |
|---:|---|---|
| 1 | Změnit seed default na `reference_single = 0` pro výchozí Figma UX. | `wp-content/themes/arctic/tools/seed-pilot-content.php` |
| 2 | Upravit `template-references.php`, aby karta defaultně nevedla na permalink. | `wp-content/themes/arctic/template-references.php` |
| 3 | U referencí s fotkou použít PhotoSwipe/lightbox trigger nebo Figma-like popup, pokud je pro kartu galerie. | `template-references.php`, reference listing templates |
| 4 | Pokud karta nemá galerii, zůstane vizuální karta bez falešného linku. | `template-references.php` |
| 5 | `single-reference.php` ponechat jako technický/admin fallback, ale neodkazovat na něj z veřejného gridu. | `single-reference.php`, templates |
| 6 | Noindex/redirect pro `/project/...` řešit až po rozhodnutí vlastníka; bez potvrzení jen odstranit veřejné odkazy. | `modules/references/type.php`, redirect logic |
| 7 | Opravit metadata/pilulky a overlay tak, aby real content seděl do Figma karty. | LESS/CSS reference card styles |
| 8 | Přidat QA check, že `/reference/` neobsahuje `href="/project/` pro běžné karty. | `tools/*audit*` |

### Acceptance criteria

| Kritérium | Ověření |
|---|---|
| `/reference/` nevytváří povinné detailní URL pro každou kartu, pokud klient nepotvrdil portfolio detail jako nový scope. | HTML smoke + source decision note. |
| Klik na fotku otevírá lightbox/popup, pokud je galerie. | Playwright interaction smoke. |
| Reference se skládají jako grid/listing podle Figmy. | Screenshot diff. |
| Obsah referencí vychází z old Arctic/owner, ne z Figma placeholderu. | Content parity check. |
| Pokud klient chce `/project/...`, existuje samostatný mini-scope s wireframem, obsahem a QA. | Sign-off doc. |

## Repair Wave 3 - Media/asset mapping

Priorita: P0  
Typ: asset inventory + import/seed + template fallbacks  
Cíl: odstranit prázdné fotky, špatné obrázky a nevhodné fallback ikony.

### Asset rozhodnutí

Tahle vlna má dvě části:

| Část | Co se dělá hned | Co se nedělá bez podkladů |
|---|---|---|
| Kód a napojení | Připravit templates, helpery, admin/metabox napojení, fallback policy a QA guardy. | Nevyrábět falešné fotky ani swatche. |
| Dostupné assety | Použít old Arctic, import uploads, existující owner podklady a schválené relevantní fallback fotky. | Nevymýšlet showroom, team, material swatches nebo product photos, pokud nejsou v podkladech. |
| Chybějící assety | Označit jako `WAITING_ON_OWNER` v asset mapě. | Neblokovat kód, ale neprezentovat náhodnou náhradu jako finální obsah. |

### Problémové oblasti

| Oblast | Problém |
|---|---|
| Product cards | Některé karty mají prázdné bílé media area. |
| Product detail | Hero/product image a swatche jsou špatně nebo prázdné. |
| Kategorie | Benefit/warranty image block prázdný. |
| Showroom | Chybí reálné showroom fotky v collage/content blocích. |
| O nás | Team karty mají missing images. |
| Kontakt | Contact cards mají initials/departments místo osob/fotek dle záměru. |
| Certifikáty | Raw obrázky bez Figma card containment. |
| Sluzby | Fallback musí být relevantní foto, ne ikona, a rozměr se nesmí zmenšovat. |

### Kroky

| Krok | Akce | Soubory/zdroje |
|---:|---|---|
| 1 | Udělat asset inventory: old Arctic, owner archive, import uploads, Figma exports; každý asset označit `available`, `usable-fallback`, nebo `WAITING_ON_OWNER`. | `../Arctic-spas/www/content/img`, `assets-source/owner-info`, `wp-content/uploads/import` |
| 2 | Pro každý component určit canonical image source nebo explicitní čekací stav. | nový `docs/asset-source-map-2026-05-29.md` |
| 3 | Produktové karty přemapovat na nejlepší dostupný legacy/owner obrázek, ne thumbnail; chybějící nechat jako čekací položku. | product seed/import/templates |
| 4 | Swatche shell/cabinet napojit jen na existující legacy/owner material images; jinak připravit data hook a označit `WAITING_ON_OWNER`. | product detail color templates |
| 5 | Showroom komponentu napojit na owner showroom photos jen pokud jsou dodané; jinak připravit component sloty a čekací položku. | `templates/section/showroom.php`, `template-showroom.php` |
| 6 | Team/contact osoby napojit na reálné fotky, pokud jsou dodané; jinak použít korektní neutrální fallback a čekací položku. | `template-about.php`, `template-contact.php`, member/contact data |
| 7 | Sluzby fallback ponechat jako relevantní fallback fotky, odstranit hnusné ikonky z primární nouze. | `template-services.php`, fallback assets |
| 8 | Vytvořit helper/policy pro obrázek: real image -> relevant fallback foto -> neutrální placeholder stejného poměru. | theme helper nebo template utility |
| 9 | Přidat smoke check proti prázdným media blokům. | `tools/visual-smoke.js` nebo nový audit |

### Acceptance criteria

| Kritérium | Ověření |
|---|---|
| Žádná P0 stránka nemá prázdný image block, pokud existuje old/owner asset. | Screenshot audit. |
| Žádná service karta nepoužije nevhodnou ikonu jako primární fallback. | HTML/CSS/asset check. |
| Product listing a detail používají ostré zdroje. | Screenshot + rozměrový audit. |
| Fallback placeholder zachová rozměry Figma komponenty. | Visual smoke. |
| Chybějící showroom/team/swatch/product asset není vymyšlený, ale zapsaný jako `WAITING_ON_OWNER`. | Asset source map. |

## Repair Wave 4 - Shared visual components

Priorita: P0  
Typ: CSS/LESS + template komponenty  
Cíl: opravit věci, které se opakují napříč stránkami.

Poznámka ke scope: Wave 4 až Wave 6 nejsou jen drobné bugfixy. Jsou to podstatné vizuální a strukturální opravy sdílených komponent a stránek. Pokud chybí owner assety nebo rozhodnutí, implementuje se pouze kódová připravenost a dostupné části; finální vizuální pass čeká na podklady.

### 4A - CTA/footer handoff

| Akce | Soubory |
|---|---|
| Najít zdroj světle tyrkysového pásu před footerem. | LESS/CSS contact/footer sections |
| Sjednotit background model: page frost -> contact CTA -> mountain/footer. | `_components.less`, footer/contact templates |
| Ověřit na všech page templates. | screenshot set |

Acceptance:

| Kritérium | Ověření |
|---|---|
| Světlý cyan pás zmizí na HP, kategoriích, support, reference, about, contact, service. | side-by-side screenshot |
| Footer nemá bílé/odtržené gutters při zoom/scaled viewports. | responsive smoke |

### 4B - Showroom collage

| Akce | Soubory |
|---|---|
| Přestavět shared showroom blok podle Figmy: dark card + real image collage + CTA. | `templates/section/showroom.php`, LESS |
| Použít owner showroom fotky a správné cropy. | asset map |
| Sjednotit desktop/mobile variantu. | CSS breakpoints |

Acceptance:

| Kritérium | Ověření |
|---|---|
| HP/category/showroom shared blok nemá prázdné image vrstvy. | screenshot |
| Layout odpovídá Figma collage záměru. | Figma compare |

### 4C - Configurator CTA/banner

| Akce | Soubory |
|---|---|
| Přestavět plain red placeholder na Figma banner s vizuálem/gradientem. | `templates/section/configurator.php`, LESS |
| Oddělit text pro hot tub vs swimspa. | settings/helper |
| Zkontrolovat Jucra fallback URL a CTA. | customizer/Jucra settings |

Acceptance:

| Kritérium | Ověření |
|---|---|
| Banner není plain red block. | screenshot |
| Swimspa text neříká chybně "vlastní vířivku", pokud jde o bazén/swimspa. | content smoke |
| Fallback i Jucra path jsou funkční. | interaction smoke |

### 4D - Popup/modal pattern

| Akce | Soubory |
|---|---|
| Doladit `f-off--benefit-popup` podle Figma popup grafiky. | `templates/section/product-benefits.php`, LESS |
| Rozhodnout affordance: plus jen u karet, které něco otevírají. | template data |
| U benefit karet bez popupu odebrat falešný plus nebo doplnit popup obsah. | template/data |

Acceptance:

| Kritérium | Ověření |
|---|---|
| Popup odpovídá Figma dark overlay + white rounded modal + close. | screenshot |
| Žádná karta nevypadá klikací, když nic nedělá. | interaction audit |

## Repair Wave 5 - Product/category visual and content media

Priorita: P0  
Typ: product templates + import data  
Cíl: hot tub a swimspa stránky nesmí mít prázdné karty, špatné hero images, nízké thumbnails ani rozpadlé menu/listingy.

### Kroky

| Krok | Akce | Soubory |
|---:|---|---|
| 1 | Product listing cards: doplnit správné obrázky a ostrost. | product section templates, import data |
| 2 | Category intro/benefit/warranty image block: vyplnit relevantní image. | `templates/section/category-intro.php` nebo související |
| 3 | Product detail hero: napojit správný legacy/owner image, ne top-down placeholder, pokud Figma/old Arctic žádá jiný treatment. | `modules/products/templates/post/single/heading.php` |
| 4 | Acrylic/shell/cabinet swatches: napojit obrázky. | product color templates/metabox data |
| 5 | Product benefit cards: odstranit gray placeholder look, doplnit thumbnails/icons podle Figma treatmentu. | `templates/section/product-benefits.php` |
| 6 | Product mega menu: potvrdit wrap columns, žádný scroll, jemné separátory, žádný odkaz "Všechny vířivky" pokud není ve Figmě. | `templates/navigation/mega.php`, LESS |
| 7 | Test hot tub detail i swimspa/pool detail. | Playwright screenshot |

### Acceptance criteria

| Kritérium | Ověření |
|---|---|
| `/virivky/` produktové karty nemají prázdné media area. | screenshot |
| `/swimspa/` používá správný text a obrázky pro swimspa kontext. | screenshot/content smoke |
| `/product/timberwolf/` má správný hero/media treatment a swatche. | screenshot |
| Mega menu ukáže všechny produkty bez scrollu nebo ořezu. | desktop interaction smoke |

## Repair Wave 6 - Page-specific P0 opravy

Priorita: P0  
Typ: jednotlivé templates  
Cíl: opravit stránky, které nejsou jen shared component bug.

### 6A - Záruka

| Problém | Oprava | Soubory |
|---|---|---|
| Local má plochou table, Figma má tři rounded warranty/product cards. | Přestavět warranty component na card layout s obrázky. | `template-warranty.php`, LESS |
| Chybí product image treatment. | Napojit obrázky z old/owner zdrojů. | asset map |

Acceptance:

| Kritérium | Ověření |
|---|---|
| `/zaruka/` hlavní blok vizuálně odpovídá Figmě. | screenshot |
| Nejsou fixed-height prázdné mezery. | visual smoke |

### 6B - Kolik stojí údržba

| Problém | Oprava | Soubory |
|---|---|---|
| Local má masivně kratší článek. | Doplnit obsah ze starého Arctic `kolik-stoji-provoz-udrzba-virivky.php` a owner poznámek. | template/content seed |
| Article struktura nesedí. | Převést do Figma article layoutu, ne hardcoded min-height. | `template-maintenance.php`, LESS |

Acceptance:

| Kritérium | Ověření |
|---|---|
| Obsah odpovídá old Arctic/owner, ne Figmě. | content parity |
| Výška/struktura odpovídá Figma záměru bez prázdných gapů. | screenshot |

### 6C - Kontakt

| Problém | Oprava | Soubory |
|---|---|---|
| Mapa je světlá/špatně stylovaná. | Figma dark/blue map treatment + red pin. | `template-contact.php`, map CSS |
| Contact cards jsou departments/initials místo osob/fotek. | Napojit reálné kontaktní osoby podle old/owner source. | contact data/templates |
| Rychlý kontakt/address wrapping. | Fixnout line wrapping a button/card alignment. | contact/showroom card CSS |

Acceptance:

| Kritérium | Ověření |
|---|---|
| `/kontakt/` mapa vypadá jako Figma. | screenshot |
| Karty odpovídají UX/vizuálu a obsah je Arctic/BASPA legal correct. | screenshot/content check |

### 6D - O nás

| Problém | Oprava | Soubory |
|---|---|---|
| Team karty mají missing images. | Napojit owner/person assets nebo relevantní fallback. | `template-about.php`, member data |
| Stats barva špatně. | Nastavit Figma red token. | LESS |
| Jobs accordion/card styling drift. | Přestylovat podle Figmy, nebo skrýt pokud není relevantní scope. | jobs/about template |

Acceptance:

| Kritérium | Ověření |
|---|---|
| Team sekce nemá prázdné karty. | screenshot |
| Stats používají správnou barvu a rytmus. | screenshot |

### 6E - Showroom

| Problém | Oprava | Soubory |
|---|---|---|
| Hero image je špatný. | Napojit showroom/interior photo z owner/old source. | `template-showroom.php` |
| Content fotky chybí. | Doplnit dva image blocks podle Figma grafiky. | template/assets |
| "Proč navštívit" karta není podle Figmy. | Card/radius/shadow/icon rhythm. | LESS |

Acceptance:

| Kritérium | Ověření |
|---|---|
| `/showroom/` používá showroom fotky, ne venkovní pool deck. | screenshot |
| Dvě hlavní fotky nejsou prázdné. | screenshot |

### 6F - Servis

| Problém | Oprava | Soubory |
|---|---|---|
| Form card je square/flat. | Přestylovat podle Figma rounded card/inputů. | `template-service.php` nebo příslušný template, LESS |
| Button placement a field geometry drift. | Sjednotit s Figma form componentem. | LESS |

Acceptance:

| Kritérium | Ověření |
|---|---|
| `/servis/` form card sedí na Figmu. | screenshot |
| Form je funkční nebo jasně degraduje do kontakt flow. | interaction smoke |

## Repair Wave 7 - Support/download polish

Priorita: P1  
Typ: CSS + interaction QA  
Cíl: funkce už existuje, ale musí sedět kompaktnost a vizuální rytmus.

### Kroky

| Krok | Akce | Soubory |
|---:|---|---|
| 1 | Ověřit, že `support-download-interactions.js` je enqueue na support/download pages. | `inc/scripts.php` |
| 2 | Doladit FAQ row heights, chip styling, opened/closed states. | support LESS |
| 3 | Doladit downloads card layout: thumbnail, metadata, CTA alignment. | download templates/LESS |
| 4 | Zkontrolovat starý Arctic FAQ/download obsah a kategorie. | seed/import data |
| 5 | Opravit service form card na support page. | `template-support.php`, LESS |

### Acceptance criteria

| Kritérium | Ověření |
|---|---|
| FAQ a downloads plus/minus jsou skutečně funkční. | Playwright interaction |
| Vzhled odpovídá Figma compact accordion rows. | screenshot |
| PDF data a FAQ obsah jsou ze starého Arctic/importu. | content parity |

## Repair Wave 8 - Mobile homepage and mobile menu

Priorita: P0/P1  
Typ: responsive UX  
Cíl: mobil nesmí být jen zmenšený desktop a musí respektovat Figma mobile frame.

### Kroky

| Krok | Akce | Soubory |
|---:|---|---|
| 1 | Potvrdit Figma mobile menu layer state, protože export může být neúplný. | Figma API/manual |
| 2 | Mobile HP: vrátit nebo potvrdit promo block podle Figmy a client scope. | homepage promo template/CSS |
| 3 | Mobile hero crop/height/text placement podle `GM - HP`. | slides/hero CSS |
| 4 | Mobile category cards: velikost, pořadí, spacing podle Figmy. | category card CSS |
| 5 | Mobile showroom card: dark card + collage/pin podle Figmy. | showroom CSS |
| 6 | Mobile final CTA/footer: spacing, background, no compression. | footer/contact CSS |
| 7 | Menu: pokud Figma state potvrzuje plnější menu, dorovnat spacing; pokud ne, re-exportovat správný stav. | mobile nav templates/CSS |

### Acceptance criteria

| Kritérium | Ověření |
|---|---|
| Mobile homepage odpovídá `GM - HP` strukturou, ne jen obsahem. | screenshot 375px |
| Mobile menu má potvrzený Figma state a sedí podle něj. | screenshot + interaction |
| Žádný mobile CTA/card není uříznutý nebo komprimovaný mimo záměr. | visual smoke |

## Repair Wave 9 - Final visual QA hardening

Priorita: P1  
Typ: automatizace + manual sign-off  
Cíl: už nikdy neoznačit rozbitou stránku jako pass jen kvůli token/geometry gate.

### Kroky

| Krok | Akce | Výstup |
|---:|---|---|
| 1 | Vytvořit nový screenshot set po opravách. | `docs/screenshots/final-repair-pass-YYYY-MM-DD/` |
| 2 | Generovat Figma-vs-local side-by-side pro všechny relevantní stránky. | compare images |
| 3 | Přidat guardy pro prázdné media bloky. | visual smoke |
| 4 | Přidat guard pro reference permalinky na `/project/` z archive gridu. | HTML smoke |
| 5 | Přidat guard pro fake affordance: plus bez handleru / aria bez interakce. | interaction smoke |
| 6 | Přidat guard pro footer/CTA cyan band nebo background discontinuity. | screenshot heuristic/manual checklist |
| 7 | Přidat content-source checklist pro old Arctic/owner výjimky. | docs sign-off |
| 8 | Ručně projít 1920, 1366/1280, 1024, 430/390/375. | sign-off table |

### Acceptance criteria

| Kritérium | Ověření |
|---|---|
| `npm run qa:local` projde. | terminal |
| `npm run figma:audit` projde, ale není jediný gate. | terminal + manual |
| Screenshot sign-off má pro každou stránku pass/fail a odkaz na screenshot. | docs |
| Žádný P0 z auditů nezůstává otevřený. | repair checklist |

## Doporučené PR pořadí

| PR | Název | Obsah | Blokuje |
|---|---|---|---|
| PR-A | Source rules + audit errata | Master plan link, source-role checklist, audit errata. | všechny další |
| PR-B | Reference archive/lightbox reset | Default archive/grid podle Figmy, žádný standalone detail bez klientského potvrzení, případný popup/lightbox, QA guard. | product/home references |
| PR-C | Asset mapping and fallbacks | Asset source map, dostupné product images, dostupné swatche/team/contact/showroom assets, services fallback policy, `WAITING_ON_OWNER` položky. | visual komponenty |
| PR-D | Shared components parity | Footer handoff, showroom collage, configurator banner, popup pattern; finální media pass jen s dostupnými assety. | page-specific polish |
| PR-E | Product/category parity | Product cards, detail hero, swatches, mega menu, swimspa text; asset-dependent části podle PR-C. | final visual QA |
| PR-F | Special pages P0 | Warranty, maintenance, contact, about, showroom, service; owner-asset části mohou zůstat čekací. | final visual QA |
| PR-G | Support/download/mobile polish | support/download compactness, mobile HP/menu. | final sign-off |
| PR-H | Final QA/sign-off | screenshot matrix, automation guardy, docs. | release |

## Page-by-page checklist

| Stránka | P0 opravy | P1 opravy | Zdroj obsahu |
|---|---|---|---|
| `/` | Showroom collage, hero/mobile promo, footer handoff. | Reference crop/pills, promo position. | old Arctic + owner |
| `/virivky/` | product images, configurator, showroom, category image, mega menu. | references treatment. | old product data |
| `/swimspa/` | product images, swimspa wording, configurator, showroom. | category structure variance documented. | old swimspa data |
| `/product/timberwolf/` | hero/media, swatches, benefit popup affordance. | benefits/options card style. | old product data |
| `/showroom/` | correct showroom images, content photos, visit card. | contact card alignment. | `prodejna-bazeny-virivky.php` + owner |
| `/vlastnosti/` | none major after shared fixes. | spacing/card drift. | old feature pages |
| `/vlastnosti/izolace-virivky/` | image, diagram card, old article completeness. | vertical rhythm. | `izolace-virivky.php` |
| `/sluzby/` | no bad icon fallback. | crop/spacing. | `sluzby.php` + owner updates |
| `/certifikaty/` | none if assets exist. | certificate card containment. | old cert pages/owner |
| `/zaruka/` | warranty cards instead of flat table. | notes placement. | `zaruka.php`, `pro/zaruka.php` |
| `/podpora/` | none if interactions work. | compact accordion/download/form style. | `faq.php`, `download.php` |
| `/o-nas/` | team images, stats color. | jobs card style. | `baspa.php` + owner |
| `/kontakt/` | map style, person cards/assets. | heading/card alignment. | `kontakt.php` |
| `/reference/` | no standalone detail default, lightbox/popup/archive. | card crop/overlay. | `diskuze.php`, `fotogalerie-virivky.php` |
| `/kolik-stoji-udrzba/` | restore old content, article structure. | CTA timing. | `kolik-stoji-provoz-udrzba-virivky.php` |
| `/servis/` | form card styling. | pricing spacing. | `servis.php` |

## Definition of done

| Gate | Musí platit |
|---|---|
| Source correctness | Žádný viditelný text není Figma lorem, Baspa marketing copy nebo vymyšlený placeholder, pokud má existovat old/owner zdroj. |
| UX correctness | Figma wireframe stavy jsou respektované: popup, accordion, slider, archive/grid. |
| Visual correctness | Figma grafika sedí ve sdílených komponentech a stránkách bez P0 rozdílů. |
| Media correctness | Žádné prázdné image bloky, žádné nevhodné fallback ikony, žádné roztažené thumbnails. |
| Functional correctness | Klikatelné prvky něco dělají; neklikatelné prvky tak nevypadají. |
| Responsive correctness | 1920, 1366/1280, 1024, 430/390/375 bez ořezů, overlay kolizí a gutters. |
| QA correctness | Automatické gate projdou a manual screenshot sign-off má pass pro každou šablonu. |

## Doporučení pro implementaci

Nedělat to stránku po stránce odshora dolů. To by vedlo k dalším lokálním hackům.

Správný postup:

1. Nejprve reference/source logic.
2. PR-B implementovat jako Figma-default archive/grid; single reference řešit pouze po klientském potvrzení.
3. Potom asset mapping, ale jen s tím, co existuje nebo je dodané; zbytek označit `WAITING_ON_OWNER`.
4. Potom shared komponenty.
5. Potom special pages.
6. Nakonec mobile/responsive a final sign-off.

Praktický start:

| Krok | Doporučení |
|---:|---|
| 1 | Začít PR-A, protože narovnává pravidla a brání dalšímu zmatku. |
| 2 | Pokračovat PR-B s výchozím UX: reference na jedné stránce, případně popup/lightbox, žádné domyšlené single detaily. |
| 3 | Rozjet PR-C pouze pro assety, které reálně máme; zbytek se dokumentuje jako čekající na owner podklady. |
| 4 | Wave 4-6 plánovat jako větší práci s otevřenou timeline, ne jako drobné dokončovací fixy. |

Každá vlna má po dokončení vygenerovat screenshoty a krátký `fix evidence` zápis, jinak se nebude dát zpětně poznat, co je skutečně hotové.
