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
| Opakované bloky se nesmí lepit stránku po stránce. | Reference, showroom, CTA/footer, configurator, progress, product cards a podobné opakované prvky musí mít jeden sdílený template/CSS contract a jasné varianty; stránka smí řešit jen umístění a povolenou variantu. |
| Page-specific CSS override je výjimka, ne architektura. | Selektory typu `.template--homepage .f-section--references ...` nebo `.tax-product-category .f-section--references ...` nesmí být hlavním zdrojem pravdy pro sdílený component. |

## Prioritní pořadí oprav

| Pořadí | Balík | Priorita | Proč první |
|---:|---|---:|---|
| 1 | Source/UX contract hardening | P0 | Zabrání dalšímu míchání Figma copy, Baspa workflow a Arctic obsahu. |
| 2 | Component architecture hardening | P0 | Nejdřív zastavit drift opakovaných bloků. Stejný prvek nesmí mít tři ručně lepené CSS implementace podle stránky. |
| 3 | Reference UX reset | P0 | Teď local zavádí standalone detailní stránky, které Figma neprokazuje; single detail je pouze klientské rozhodnutí / nový mini-scope. Součástí je sjednocení reference componentu globálně. |
| 4 | Media/asset mapping | P0 | Prázdné obrázky a špatné assety ničí většinu stránek; chybějící owner assety se nesmí domýšlet. |
| 5 | Shared visual components | P0 | Showroom, CTA/footer, configurator, popup a cards se opakují napříč webem. |
| 6 | Page-specific P0 stránky | P0 | Záruka, údržba, kontakt, o nás, showroom, produkt detail mají vlastní větší odchylky. |
| 7 | Mobile/responsive parity | P0/P1 | Mobile homepage a menu musí sedět po velkých strukturálních opravách. |
| 8 | QA/sign-off automation | P1 | Aby se znovu nestalo, že gate projde a vizuál je mimo. |

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

## Architektonická brána před Wave 2+

Priorita: P0  
Typ: component architecture / CSS contract cleanup  
Cíl: zastavit opakované opravování stejného prvku na více stránkách.

Tahle brána platí pro PR-B a všechny další visual/component PR. Neopravovat jeden výskyt komponenty izolovaně, pokud stejný HTML/CSS pattern existuje na dalších stránkách.

### Zjištěný anti-pattern

| Problém | Dopad |
|---|---|
| Sdílený PHP template existuje, ale vzhled se ručně přepisuje přes page-specific selektory. | Oprava homepage se automaticky nepropíše do `/virivky/`, `/swimspa/`, product detailu atd. |
| Stejná komponenta má samostatné CSS bloky pro `.template--homepage`, `.tax-product-category`, `.single-product` a podobné kontexty. | Vzniká drift, větší CSS, složitější cascade a vyšší riziko regresí. |
| QA kontroluje hlavně stránky, ne komponentu ve všech místech použití. | Gate může projít pro jednu stránku, zatímco stejný prvek zůstane rozbitý jinde. |

### Povinný postup pro opakované prvky

| Krok | Akce | Výstup |
|---:|---|---|
| 1 | Najít všechny výskyty opakovaného prvku. | usage map: template + CSS selectors + screenshot evidence |
| 2 | Určit jeden canonical template a jeden canonical CSS contract. | např. `reference recent carousel`, `reference archive grid`, `showroom panel`, `contact CTA` |
| 3 | Povolit jen pojmenované varianty. | `recent-carousel`, `archive-grid`, `product-context`, ne náhodný page override |
| 4 | Přesunout společná pravidla z page-specific bloků do component contractu. | méně duplicit v `_components.less`, jasnější `_component-contracts.less` nebo component LESS |
| 5 | Page-specific CSS nechat jen pro layoutové umístění celé sekce. | stránka řeší pořadí/mezery, ne vnitřní card/button/overlay pravidla |
| 6 | Přidat QA guard, že komponenta sedí ve všech použitích. | homepage + category + product + archive podle typu komponenty |

### Acceptance criteria

| Kritérium | Ověření |
|---|---|
| Žádný sdílený P0/P1 prvek není opravovaný jen pro jednu stránku. | diff + usage map |
| Každá opakovaná komponenta má canonical contract a seznam povolených variant. | docs nebo comment v component source |
| Page-specific override má vysvětlený důvod. | krátká poznámka v PR/fix evidence |
| QA se ptá komponentově i stránkově. | screenshot matrix zahrnuje všechny výskyty daného componentu |

## Repair Wave 2 - Reference UX reset

Priorita: P0  
Typ: template + seed + interaction  
Cíl: reference nejsou defaultně standalone detailní stránky a zároveň nesmí být udržované jako několik různých page-specific implementací. Výchozí implementace má odpovídat archive/grid + případný lightbox/popup/fotogalerie záměr. Samostatný detail reference je legitimní jen po explicitním potvrzení klientem/vlastníkem.

Poznámka k architektuře: PR-B musí nejdřív sjednotit reference component contract pro homepage, kategorii, product detail a `/reference/` archive. Až potom se řeší konkrétní UX archive/lightbox reset. Jinak se stejná chyba bude vracet v dalších výskytech.

Scope poznámka: usage map pro reference component je netriviální krok. `.template--homepage`, `.tax-product-category`, `.single-product` a `/reference/` archive dohromady obsahují stovky řádků page-specific CSS pro stejný opakovaný prvek. PR-B proto plánovat jako větší refaktor component contractu, ne jako kosmetickou úpravu.

Scope doplneni 2026-05-30: reference component nema jen vizualni drift, ale i query/filter drift. `modules/references/templates/section-recent.php` aktualne taha globalni seznam referenci (`post_type=reference`, thumbnail exists, `posts_per_page=7`) bez kontextoveho filtru pro `/virivky/`, `/swimspa/` nebo product detail. Homepage muze mit povolenou curated/global variantu pouze jako explicitni Figma/owner rozhodnuti; category/product context nesmi nahodne ukazovat reference mimo dany kontext (napr. swimspa reference na `/virivky/`). Pokud pro kontext nejsou dostupne reference, musi byt fallback zapsany jako owner/content decision, ne tichy globalni mix.

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
- `modules/references/templates/section-recent.php` pouziva globalni WP_Query bez kontextoveho taxonomy/meta filtru; na category/product strankach tak muze zobrazit reference z jine produktove rodiny.

Figma/old Arctic záměr:

- `WF - REFERENCE` je sběrná/archive stránka.
- `REFERENCE` grafika ukazuje grid.
- old Arctic `diskuze.php` je stránka textových referencí.
- old Arctic `fotogalerie-virivky.php` používá fancybox/lightbox.

### Kroky

| Krok | Akce | Soubory |
|---:|---|---|
| 1 | Změnit seed default na `reference_single = 0` pro výchozí Figma UX. | `wp-content/themes/arctic/tools/seed-pilot-content.php` |
| 2 | Udělat usage map všech reference výskytů: homepage, `/virivky/`, `/swimspa/`, product detail, `/reference/`. | templates + LESS selectors + screenshots |
| 3 | Zmapovat query/filter contract pro kazdy vyskyt: homepage curated/global, category context, product context, archive all/grid. | `section-recent.php`, reference taxonomy/meta data, seed |
| 4 | Doplnit kontextovy filtr: `/virivky/` nesmi ukazat swimspa reference, `/swimspa/` nesmi ukazat hot-tub-only reference, product detail ma preferovat product/category relevantni reference. | reference query helper/template |
| 5 | Definovat fallback, kdyz pro kontext nejsou reference: curated owner fallback nebo `WAITING_ON_OWNER`, ne tichy globalni mix. | docs + template fallback |
| 6 | Sjednotit canonical reference component contract a pojmenované varianty (`recent-carousel`, `archive-grid`, `product-context`). | reference template/LESS contract |
| 7 | Přesunout duplicitní page-specific reference CSS do component contractu; page-specific ponechat jen pro umístění celé sekce. | `_components.less`, `_component-contracts.less`, module LESS |
| 8 | Upravit `template-references.php`, aby karta defaultně nevedla na permalink. | `wp-content/themes/arctic/template-references.php` |
| 9 | U referencí s fotkou použít PhotoSwipe/lightbox trigger nebo Figma-like popup, pokud je pro kartu galerie. | `template-references.php`, reference listing templates |
| 10 | Pokud karta nemá galerii, zůstane vizuální karta bez falešného linku. | `template-references.php` |
| 11 | `single-reference.php` ponechat jako technický/admin fallback, ale neodkazovat na něj z veřejného gridu. | `single-reference.php`, templates |
| 12 | Noindex/redirect pro `/project/...` řešit až po rozhodnutí vlastníka; bez potvrzení jen odstranit veřejné odkazy. | `modules/references/type.php`, redirect logic |
| 13 | Opravit metadata/pilulky a overlay tak, aby real content seděl do Figma karty napříč všemi výskyty. | LESS/CSS reference card styles |
| 14 | Přidat QA check, že `/reference/` neobsahuje `href="/project/`, recent reference component sedí ve všech použitích a category/product výskyty neobsahují reference mimo svůj kontext. | `tools/*audit*` |

### Acceptance criteria

| Kritérium | Ověření |
|---|---|
| `/reference/` nevytváří povinné detailní URL pro každou kartu, pokud klient nepotvrdil portfolio detail jako nový scope. | HTML smoke + source decision note. |
| Klik na fotku otevírá lightbox/popup, pokud je galerie. | Playwright interaction smoke. |
| Reference se skládají jako grid/listing podle Figmy. | Screenshot diff. |
| Reference recent carousel je jeden sdílený component contract, ne tři page-specific kopie. | usage map + CSS diff. |
| Category/product reference carousel nepoužívá tichý globální/all-reference výběr mimo svůj kontext. | HTML/content smoke: `/virivky/` bez swimspa-only referencí, `/swimspa/` bez hot-tub-only referencí, product detail preferuje relevantní reference. |
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

PR-C asset source map: `docs/asset-source-map-2026-05-29.md`.

### Asset delivery / repo policy

Poznámka z 2026-05-30: produkční `baspa.cz` podle veřejného HTML používá WebP varianty typu `.jpg.webp` a marker `ewww`, tedy pravděpodobně image optimizer ve stylu EWWW Image Optimizer plus standardní WordPress `srcset` velikosti. Arctic plán proto nesmí řešit produkční optimalizaci tím, že budeme commitovat stále víc velkých binárek do repa.

| Typ assetu | Pravidlo | Důvod |
|---|---|---|
| Malé seed/fallback assety nutné pro lokál a staging | Mohou být v gitu, pokud jsou optimalizované a cíleně použité. | Lokální prostředí musí být reprodukovatelné bez ručního uploadu. |
| Originály owner fotek, celé galerie, velké produktové série | Necommitovat do gitu. | Repo by rychle bobtnalo a git není produkční media library. |
| Produkční obrázky | Upload přes WordPress media library / deploy uploads a optimalizovat pluginem typu EWWW/WebP + responsive sizes. | Produkce má generovat velikosti, WebP a cache mimo source repo. |
| Větší verzovaná asset knihovna | Pouze po rozhodnutí použít Git LFS nebo externí storage. | Musí to být vědomé infrastrukturní rozhodnutí, ne náhodný vedlejší efekt PR. |

PR-C výjimka: aktuálně commitnuté owner swatche a webové showroom deriváty jsou malé, ověřené, produkčně použité seed assety. Další asset expanze musí projít tímto pravidlem.

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

Architektonická poznámka: Wave 4 se nesmí dělat jako sada lokálních patchů pro jednotlivé stránky. Každý opakovaný blok musí nejdřív dostat canonical component contract, usage map a pojmenované varianty. Teprve potom se ladí konkrétní stránkové umístění.

PR-D usage map: `docs/component-contract-usage-map-2026-05-30.md`.

PR-D scope doplneni 2026-05-30: globalni header/top-contact patri do stejne vlny jako footer/contact CTA. Oteviraci status neni staticka zelena tecka. Baspa/Forqys pattern pouziva `forqy_hours` + AJAX `hours_is_open`, ktery podle aktualniho casu prida `.open` nebo `.closed`; vizual pak ukaze zelenou tecku pri otevreno a cervenou pri zavreno. Arctic ma tento mechanismus zachovat a napojit, ne hardcodovat barvu.

### 4-0 - Component contract cleanup

| Akce | Soubory |
|---|---|
| Auditovat opakované bloky: reference, showroom, header/top-contact status, contact CTA/footer, configurator, progress, product cards. | templates + `_components.less` + `_component-contracts.less` |
| Najít duplicitní page-specific CSS implementace pro stejný component. | `.template--homepage`, `.tax-product-category`, `.single-product`, page-template selektory |
| Přesunout společný vzhled do component contractu a ponechat page-specific CSS jen pro layoutové umístění. | LESS/CSS component layer |
| Přidat screenshot matrix podle componentu, ne jen podle stránky. | QA docs/tools |

Acceptance:

| Kritérium | Ověření |
|---|---|
| Oprava sdíleného componentu se propíše do všech jeho výskytů bez dalšího ručního kopírování. | diff + screenshot matrix |
| CSS objem a cascade pro opakované prvky neroste page-by-page. | selector audit |

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

### 4E - Header top contact / opening-hours status

| Akce | Soubory |
|---|---|
| Zachovat Baspa-like automatiku oteviraci doby: `templates/about/hours.php` -> `forqy_hours` -> vendor status template -> `hours_is_open` AJAX -> `.open`/`.closed`. | `templates/about/hours.php`, `vendor/forqys/hours/*`, `inc/functions/hours.php` |
| Odstranit nebo sjednotit staticky header text `.f-bar__hours`, aby vedle nej nevznikal duplicitni/konfliktni status. | `templates/header/bar/contacts.php` |
| Nastavit default/local opening-hours data pro Arctic, aby se status vubec renderoval. Pokud owner doda jine hodiny, menit data, ne komponentu. | Customizer seed/defaults, `inc/functions/hours.php` |
| Stylovat status jako Figma skupinu u kontaktu: text + indikacni tecka, kde tecka je zelena jen pri `.open` a cervena pri `.closed`. | header/topbar LESS/CSS |
| Dodelat dark/light variantu top kontaktu podle pozadi: svetle stranky maji tmavy/citelny text, tmavy hero muze mit svetly text. | header template/body context + LESS |
| Pridat QA guard pro pritomnost `.js-hours__status`, prepnuti `.open/.closed`, computed barvu tecky a kontrast top kontaktu na svetlem pozadi. | `tools/*smoke*`, visual QA |

Acceptance:

| Kritérium | Ověření |
|---|---|
| Header status neni hardcoded green dot; pouziva `hours_is_open` a tridy `.open/.closed`. | DOM + network/AJAX smoke |
| Pri otevreno je tecka zelena, pri zavreno cervena. | mocked/time-aware smoke nebo manual check mimo oteviraci dobu |
| Na svetlych strankach je top contact tmavy/citelny a nezanika v pozadi. | screenshot + computed color |
| Staticky text `Po - Pá 8:00-17:00 h` neni duplicitni vedle dynamickeho statusu, pokud status renderuje stejna data. | HTML smoke |

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

PR-E product/category media contract: `docs/product-category-media-contract-2026-05-30.md`.

PR-E guard: `npm run product-media:smoke`.

## Repair Wave 6 - Page-specific P0 opravy

Priorita: P0  
Typ: jednotlivé templates  
Cíl: opravit stránky, které nejsou jen shared component bug.

### 6A - Záruka

Status 2026-05-30: implemented. `/zaruka/` now uses the Figma warranty card matrix geometry, shared labels, right-side note/link, and `WAITING_ON_OWNER` media placeholders because no verified owner/legacy warranty card images exist. Evidence: `docs/screenshots/warranty-card-fix-2026-05-30/`. Guard: `npm run warranty:smoke`.

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

Status 2026-05-30: implemented. `/kolik-stoji-udrzba/` now follows the Figma maintenance article structure: one long ownership-cost block and three FreeHeat follow-up sections (`Další inovace`, `Nejnižší provozní náklady`, `Skutečná ochrana proti mrazu`). CTA/footer geometry matches the Figma frame height. Evidence: `docs/screenshots/maintenance-content-fix-2026-05-30/`. Guard: `npm run maintenance:smoke`.

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

Status 2026-05-30: implemented/verified. `/kontakt/` uses the Figma dark map treatment with red pin, no floating map labels, six contact cards from the Figma contact frame with avatars marked `WAITING_ON_OWNER`, and footer mountain handoff restored. Evidence: `docs/screenshots/contact-6c-verification-2026-05-30/`. Guard: `npm run contact-map:smoke`.

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

Status 2026-05-30: implemented/verified. `/o-nas/` matches the Figma page geometry for intro, stats, team grid, career cards, contact CTA, and mountain footer. Team/person portraits remain `WAITING_ON_OWNER` because only design-only Figma portraits exist in the repo asset map; the page must not invent or reuse them as owner assets. Stats use the Figma red token. Evidence: `docs/screenshots/about-6d-verification-2026-05-30/`. Guard: `npm run about:smoke`.

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

Status 2026-05-30: implemented/verified. `/showroom/` now uses only owner showroom assets: the hero is the interior hot tub/logo photo instead of the exterior facade, content blocks use the available interior/detail and exterior showroom photos, and the exterior crop is explicitly positioned so it renders as a real image block. Design-only Figma showroom exports remain forbidden. Evidence: `docs/screenshots/showroom-6e-verification-2026-05-30/`. Guard: `npm run showroom:smoke`.

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

Status 2026-05-30: verified. `/servis/` form card already matches the Figma service-request geometry: rounded 40px card, 8px inputs, submit/consent alignment, warranty/pricing blocks, contact CTA, and restored mountain footer. Evidence: `docs/screenshots/service-6f-verification-2026-05-30/`. Guards: `npm run form:smoke` and `npm run figma:audit`.

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

Status 2026-05-30: implemented/verified. `/podpora/` and `/ke-stazeni/` use the shared PR-G support/download contract: real FAQ/download interactions, tab/chip states, bounded download rows, service form card, and mobile homepage/menu overflow checks. Evidence: `docs/screenshots/wave7-support-download-verification-2026-05-30/`. Guard: `npm run support-mobile:smoke`.

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

PR-G support/download/mobile contract: `docs/support-download-mobile-contract-2026-05-30.md`.

PR-G guard: `npm run support-mobile:smoke`.

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

PR-G mobile guard: `npm run support-mobile:smoke`.

Wave 8 status 2026-05-30: implemented/verified.

- Mobile HP promo je znovu viditelne podle Figmy: 390px audit `x=27.5/y=562/w=335/h=288`, 375px manual compare `x=20/y=562/w=335/h=288`.
- Product category cards jsou po promo bloku: 390px audit `y=842/1084`, 375px manual compare `y=842/1084`.
- Promo copy je sjednocena na Figma text `Vyprodej skladovych virivek` / CZ output `Výprodej skladových vířivek`; legacy `Akcni nabidka skladovych virivek` se normalizuje pres theme_mod helper.
- `GM - HP menu` Figma node `1:2208` ma pouze white header + dark panel + search shell. Local drzi funkcni nav linky jako UX extension, ale Figma search shell sedi: `x=26/y=527/w=323/h=44`, search icon `x=311/y=537/w=24/h=24`.
- Evidence: `docs/screenshots/wave8-mobile-verification-2026-05-30/`.
- Guardy: `npm run figma:audit`, `npm run visual:smoke`, `npm run support-mobile:smoke`, `npm run component:smoke`, `npm run header:smoke`.

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
| 7 | Přidat guard pro header/top-contact: dark/light kontrast, `.js-hours__status`, `.open/.closed` a barvu status tečky. | HTML + AJAX/computed-style smoke |
| 8 | Přidat content-source checklist pro old Arctic/owner výjimky. | docs sign-off |
| 9 | Ručně projít 1920, 1366/1280, 1024, 430/390/375. | sign-off table |

### Acceptance criteria

| Kritérium | Ověření |
|---|---|
| `npm run qa:local` projde. | terminal |
| `npm run figma:audit` projde, ale není jediný gate. | terminal + manual |
| Screenshot sign-off má pro každou stránku pass/fail a odkaz na screenshot. | docs |
| Žádný P0 z auditů nezůstává otevřený. | repair checklist |

Wave 9 status 2026-05-30: implemented/verified.

- Added final QA gate: `npm run final:qa`.
- Guard covers global footer mountain background/no `f-footer--handoff`, `/reference/` archive without `/project/` links, visible image load failures, horizontal overflow, static vs interactive product benefit affordances, and `WAITING_ON_OWNER` marker counts for contact/about/warranty media.
- Final screenshot evidence: `docs/screenshots/final-qa-2026-05-30/` with desktop/mobile manifest and index.
- Supporting guards re-run in Wave 8/9 pass: `npm run figma:audit`, `npm run visual:smoke`, `npm run support-mobile:smoke`, `npm run component:smoke`, `npm run header:smoke`, `npm run local:safety`, `npm run asset:smoke`, `npm run link:smoke`, `npm run final:qa`.

## Doporučené PR pořadí

| PR | Název | Obsah | Blokuje |
|---|---|---|---|
| PR-A | Source rules + audit errata | Master plan link, source-role checklist, audit errata. | všechny další |
| PR-B | Reference component contract + archive/lightbox reset | Nejprve sjednotit reference component napříč homepage/kategoriemi/product detailem/archive, odstranit page-specific CSS drift; potom default archive/grid podle Figmy, žádný standalone detail bez klientského potvrzení, případný popup/lightbox, QA guard. | product/home references |
| PR-C | Asset mapping and fallbacks | Asset source map, dostupné product images, dostupné swatche/team/contact/showroom assets, services fallback policy, `WAITING_ON_OWNER` položky. | visual komponenty |
| PR-D | Shared component contracts parity | Globální component contract cleanup pro header/top-contact opening-hours status, footer/contact CTA, showroom collage, configurator banner, progress, popup pattern a cards; page-specific CSS jen pro umístění; finální media pass jen s dostupnými assety. | page-specific polish |
| PR-E | Product/category parity | Product cards, detail hero, swatches, mega menu, swimspa text; asset-dependent části podle PR-C. | final visual QA |
| PR-F | Special pages P0 | Warranty, maintenance, contact, about, showroom, service; owner-asset části mohou zůstat čekací. | final visual QA |
| PR-G | Support/download/mobile polish | support/download compactness, mobile HP/menu. | final sign-off |
| PR-H | Final QA/sign-off | screenshot matrix, automation guardy, docs. | release |

## Page-by-page checklist

Tahle tabulka je validační checklist, ne implementační strategie. Pokud se stejný problém opakuje ve sdílené komponentě, řeší se globálně v component contractu a stránkový checklist jen potvrzuje všechny výskyty.

| Stránka | P0 opravy | P1 opravy | Zdroj obsahu |
|---|---|---|---|
| `/` | Showroom collage, hero/mobile promo, footer mountain. | Reference crop/pills, promo position. | old Arctic + owner |
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
| Component architecture correctness | Opakované prvky mají jeden canonical template/CSS contract a jasné varianty; stránkové selektory nepřepisují vnitřní vzhled komponenty jako hlavní zdroj pravdy. |
| Visual correctness | Figma grafika sedí ve sdílených komponentech a stránkách bez P0 rozdílů. |
| Media correctness | Žádné prázdné image bloky, žádné nevhodné fallback ikony, žádné roztažené thumbnails. |
| Functional correctness | Klikatelné prvky něco dělají; neklikatelné prvky tak nevypadají. |
| Responsive correctness | 1920, 1366/1280, 1024, 430/390/375 bez ořezů, overlay kolizí a gutters. |
| QA correctness | Automatické gate projdou a manual screenshot sign-off má pass pro každou šablonu. |

## Doporučení pro implementaci

Nedělat to stránku po stránce odshora dolů. To by vedlo k dalším lokálním hackům, duplicitnímu CSS a regresím, kde oprava homepage neopraví stejný prvek na kategorii nebo detailu.

Správný postup:

1. Nejprve reference/source logic.
2. Před PR-B udělat component architecture gate: usage map, canonical contract, pojmenované varianty.
3. PR-B implementovat jako reference component contract + Figma-default archive/grid; single reference řešit pouze po klientském potvrzení.
4. Potom asset mapping, ale jen s tím, co existuje nebo je dodané; zbytek označit `WAITING_ON_OWNER`.
5. Potom shared komponenty globálně, ne stránku po stránce.
6. Potom special pages.
7. Nakonec mobile/responsive a final sign-off.

Praktický start:

| Krok | Doporučení |
|---:|---|
| 1 | Začít PR-A, protože narovnává pravidla a brání dalšímu zmatku. |
| 2 | Před PR-B udělat usage map referencí a sjednotit reference component contract napříč homepage, kategoriemi, product detailem a `/reference/`. |
| 3 | Pokračovat PR-B s výchozím UX: reference na jedné stránce, případně popup/lightbox, žádné domyšlené single detaily. |
| 4 | Rozjet PR-C pouze pro assety, které reálně máme; zbytek se dokumentuje jako čekající na owner podklady. |
| 5 | Wave 4-6 plánovat jako větší globální komponentovou práci s otevřenou timeline, ne jako drobné dokončovací fixy. |

Každá vlna má po dokončení vygenerovat screenshoty a krátký `fix evidence` zápis, jinak se nebude dát zpětně poznat, co je skutečně hotové.
