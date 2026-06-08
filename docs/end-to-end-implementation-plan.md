# Arctic Spas WordPress: end-to-end realizacni plan

Datum: 2026-05-23  
Pracovni prostor: `arctic-spas-2/`  
Lokalni web: `http://localhost:8090`  
Admin: `http://localhost:8090/wp-admin/`  
Technicky vzor: `../baspa.cz/`  
Obsahovy zdroj: `../Arctic-spas/`, crawl `docs/crawl-live/`, live web jen pro overeni obsahu  
Designovy zdroj: Figma wireframe + Figma grafika

## Aktualizace 2026-06-08: zakaznicke P0 pripominky, lokalni opravy a deploy pravidlo

Zakaznicke pripominky od Lukase Duska jsou P0. Do produkce se od tohoto bodu neposila nic automaticky: opravy se delaji a commituji lokalne, do produkce pujdou az po schvaleni lokalniho stavu. Stav local-only oprav je v `docs/local-only-p0-repair-ledger-2026-06-08.md`.

Aktualni Figma grafika pro kontrolu je file key `zWLRkhgU5uOipN7I6cGHHe`; starsi grafika key `xeOew3dFjDVfjXZrJ09emM` je jen legacy reference. Figma neni inspirace podle oka, ale zdroj souradnic, rozmeru, fill vrstev, gradientu/stinu, mobile frame a footer/header kompozic. Baspa je technicky WordPress zaklad a zdroj existujicich funkcnosti, ne vizualni fallback.

Repair plan je rozdeleny do techto bloků:

1. Home Page: rychlost prvniho nacteni, slidy vcetne prokliku, mobilni slider bez temneho filtru, zarovnani karet `Virivky`/`Celorocni bazeny`, admin save integrity pro text `Jsme vyhradni prodejce`, navrat service ikon a progress odrazek, footer menu bez `Skladove virivky`, copyright `BASPA s.r.o.`.
2. Kategorie `Venkovni virivky Arctic Spas` a `Celorocni bazeny`: hero CTA musi byt viditelne ve vsech breakpointach, produktove nahledy nesmi byt spatne cropnute, a chybejici souhrnne stranky modelovych rad se musi doplnit podle realneho Arctic/Baspa obsahoveho modelu.
3. Produktove stranky obecne: doplnit vsechny dostupne konfigurace, spravne nazvy/pocty trysek/cerpadel, popisy bez useknuti, provedeni, barvy kabinetu, standardni vybavu, volitelnou vybavu, funkci odkazu `Vyhody` a `Volitelna vybava`; u swimspa nesmi zmizet produktove sekce jen proto, ze nejsou virivka.
4. Vlastnosti a vybava: popis parametru musi byt obsahove relevantni, ne placeholder/fallback. Struktura se inspiruje korporatnim Arctic features obsahem, ale implementace zustava adminovatelná pres WP.
5. `Kolik stoji provoz a udrzba`: presunout do `Podpora / Caste dotazy` tak, aby zbyl redirect nebo jasny odkaz, ne osirela informacni stranka.
6. `Reference`: musi zobrazovat texty i fotky po kliknuti; ne jen samotnou fotku. Datovy model musi pouzit editovatelne reference, ne staticke karty.
7. `O nas`: aktualizovat obsah, protoze klient hlasi vice nez rok stare informace. Zdroj dat: aktualni klientsky podklad, existujici Arctic/Baspa kontaktni data a WP admin, ne fallback text.
8. `Showroom`: doplnit mapu, opravit odkaz do fotogalerie a zachovat Figma rozvrzeni vcetne mobilu.
9. `Kontakt`: mapa nesmi byt nezretelna a pin nesmi ukazovat do Cernych Poli; lokalita se musi overit proti realne adrese Bohunicka cesta 15, Moravany u Brna a vlozit jako adminovatelná hodnota.
10. `Dalsi informace`: musi existovat jako samostatna stranka/rozcestnik i jako menu dropdown. Menu ma obsahovat poptavkovy formular nad servisem.
11. Cenik/katalog na vyzadani: lokalne ma byt dostupny banner/formular po vzoru Baspa `Kompletni katalog s cenikem produktu`, ale jako Arctic komponenta a local-only do schvaleni. Ecomail se pripoji az na finalni domene; do te doby musi byt videt UX a data flow.
12. `Akcni nabidky`: nahrazuje label `Vyprodej skladovych virivek`, ale nesmi vzniknout paralelni vlastni logika vedle existujici nabidkove logiky z Baspa/Arctic forku. Archiv ma byt jednoduse editovatelny a musi podporovat vice nabidek s publikovanim jen vybranych.
13. Nahledy skorepin a barev: oprava musi byt local i pozdeji produkcne, ne jen mobilni hotfix. Karty nesmi byt rozhazene, text nesmi pretekat a crop musi sedet na vsech produktovych detail layout variantach.
14. Globalni admin stabilita: zadna sablona nesmi po uprave jedne vety v editoru ztratit souvisejici bloky, ikony nebo odrazky. Fallbacky mohou existovat jen jako local/dev pojistka; verejny obsah musi byt realny WP obsah, media nebo meta data.
15. Globalni text flow: useknute vety se nesmi opravovat po jedne strance. CSS a audit maji hlidat `line-clamp`, fixed height, overflow clipping a fixed card heights u editovatelnych textu globalne.
16. Duplicitni logika: kazdy novy blok se pred implementaci porovna s existujicimi Baspa/Arctic moduly. Katalog, nabidky, reference, downloady, FAQ, kontakty a mapy se nemaji implementovat paralelne, pokud uz existuje editovatelny modul.
17. Deploy: kazda oprava se commituje a pushuje s poznamkou, jestli je nebo neni v produkci. Produkce = FTP upload souboru + pripadny produkcni DB/script krok + hash/URL verifikace. Bez schvaleni zustava status `Not deployed to production`.

## Aktualni navazujici plan od 2026-05-25

Tento dokument zustava jako puvodni implementacni runbook a technicky baseline. Neni ale pravda, ze produkcni web je hotovy jen proto, ze cast automatickych smoke testu prosla.

Aktualni plan dokonceni je v `docs/web-completion-plan.md`.

Dulezita aktualizace (2026-05-25):
- Pro finalni layout fidelity a desktop scaling je kanonicky plan `docs/arctic-scaling-rebuild-plan.md`.
- Pokud je konflikt mezi timto runbookem a kanonickym planem, plati `docs/arctic-scaling-rebuild-plan.md`.
- Aktualni pass/fail stav se bere jen z posledniho cisteho CSS buildu a naslednych audit gate checku; historicke overovaci body nize jsou archivni snapshoty.

Klicova korekce: Figma fidelity a responzivita jsou P0 pro cely web, ne jen pro homepage. Homepage hero/header/footer jsou nejviditelnejsi symptom, ale stejne pravidlo plati pro katalogy (`/virivky/`, `/swimspa/`, `Dalsi sortiment`), produktove detaily, podporu, kontakt, showroom, reference a informacni stranky. Realny vizual porad nese Baspa stretching chovani: na screenshotech je videt kolize sipky s textem, spatny crop/rozmer hero obrazku a vnitrni stranky se v Chrome nechovaji jako Figma frame.

CSS korekce: Baspa zustava technicky WordPress zaklad, ale Baspa CSS neni vizualni ani rozmerovy zdroj. Pred dalsim pixel-perfect ladenim se musi verejny layout prevest na Arctic-first CSS podle Figmy. Baspa pravidla pro header, hero/heading, container, CTA, karty, footer a breakpointy se odstrani, zneutralizuji nebo prepisou u zdroje; `arctic.css` nema byt nekonecna sada override hacku nad Baspa `style.css`. Primarni reseni neni `@layer baspa`, protoze jde o fork bez upstream zavazku.

Responzivni pravidlo od 2026-05-25: desktop Figma frame `1920 px` je globalni navrhove pravidlo. Na beznych Windows/Chrome viewports `1920/1903`, `1536`, `1440`, `1366` a tablet sirce kolem `1024` musi header, dropdowny, hero/heading, hlavni container, CTA bloky a footer drzet stejny system napric webem. Homepage-only oprava nebo homepage-only scale se nepovazuje za splneni P0.

Doplneni od 2026-05-25: slozene Figma sekce se neberou jako inspirace k prekresleni, ale nesmi se ani slepe zplostit do jednoho PNG. Pokud existuje cely Figma node/frame pro header, footer, hero, CTA, banner, mapu nebo kolaz, implementace musi rozlisit obrazove vrstvy a semanticky obsah. Dekorativni/obrazove vrstvy se exportuji z Figmy 1:1; navigace, texty, kontakty, tlacitka a odkazy zustavaji realne HTML s CSS hodnotami z Figmy. Soucasny footer skladany z HTML/CSS + `footer-background.jpg` + `footer-map.png` je nevyhovujici proto, ze nesedi na Figma rozmery/styly, ne proto, ze by footer mel byt jedna bitmapa.

Souradnice z Figma API jsou pracovni data. Konkretni hodnoty pro header, hero a ovladaci prvky se pred kodovanim overuji vizualne proti otevrene Figme/Dev Mode nebo proti ulozenemu Figma screenshotu vedle browser renderu.

## Tvrde pravidlo od 2026-05-23

Tato cast ma prednost pred starsimi formulacemi nize v dokumentu.

Od ted je Figma jediny zdroj UX, architektury, rozmeru, loga, komponent, spacingu, typografie a designovych assetu. Obsahove fotky produktu, virivek, swimspa, realizaci a referenci se berou primarne ze stareho Arctic webu nebo `assets-source/owner-info/`, pokud tam existuji. Figma se pro tyto obsahove fotky pouziva jako navrh layoutu, cropu a proporci, ne jako zdroj obsahu.

Pravidlo zdroju:

1. `Arctic-spas.cz wireframe` ridi architekturu sablon a poradi sekci.
2. `Arctic-spas.cz grafika` ridi finalni vizual, rozmery, designove assety a CSS.
3. `../baspa.cz/` je read-only WordPress/funkcni zaklad, ne vizualni fallback.
4. `../Arctic-spas/` je read-only obsahovy archiv.
5. Designove assety z Figmy (logo, HP hero, bannery, ikony, mapove/kolazove kompozice, UI prvky) se exportuji primo z Figma node ID do `assets-source/figma/export/`.
6. Obsahove media (produktove fotky, virivky, swimspa, realizace, reference) maji zdroj ve starem Arctic archivu nebo owner podkladech; vyjimka se zapise do `docs/figma-asset-manifest.md`.
7. Komponovane Figma sekce maji mit Figma mereni celeho node/frame; obrazove vrstvy se exportuji, ale zive texty, odkazy a kontakty zustavaji realne HTML.
8. API souradnice se pred CSS zmenou validuji vizualne ve Figme nebo screenshot diffem.

Tento dokument je souvisly realizacni runbook. Neni to navrh k dalsimu rozpadani na faze; je to poradi prace od aktualniho stavu az po produkcni spusteni.

## Vysledek, ktereho dosahneme

Novy Arctic Spas web bude WordPress web postaveny jako kontrolovany fork Baspa. Programatorsky vychazi z Baspa, ale UX a grafika se nevymysli a neopisuji ze stareho Arctic webu. Architekturu urcuje Figma wireframe, finalni vizual urcuje Figma grafika a obsah vcetne produktovych/reference fotek urcuje stary Arctic archiv, crawl a owner podklady.

Web bude spravovatelny z administrace. Produkty, downloady, reference, FAQ, kontaktni data a nabidky nebudou napevno ve statickem HTML, ale v editovatelnem modelu WordPressu.

## Pevna rozhodnuti

- Pracuje se jen v `arctic-spas-2/`.
- `../baspa.cz/` se neupravuje. Je to read-only technicka reference.
- `../Arctic-spas/` se neupravuje. Je to read-only archiv stareho webu a assetu.
- Baspa theme fork zustava v `wp-content/themes/arctic/`.
- Lokalni WordPress bezi v Dockeru na `http://localhost:8090`.
- Baspa `dist/css/style.css` existuje jako zdedeny technicky soubor, ale nesmi zustat vizualni autoritou Arctic layoutu.
- Arctic rebrand jde do `dist/css/arctic.css`, ale po cleanupu ma jit o finalni Arctic design system, ne o vrstvu specificity hacku proti Baspa layoutu.
- Datovy model se ted drzi v theme, protoze to odpovida Baspa architekture a zrychluje implementaci.
- Hlavni CPT zustava jeden sirsi `product`.
- Downloady budou samostatny CPT `download`, protoze crawl nasel 27 dokumentu a podpora se bude spravovat dlouhodobe.
- Dreammaker, Ellesmere, Aurora, Orca a Grizzly nebudou aktivni produkty. Dostanou redirect.
- Figma pilot pro produktovy detail bude `Timberwolf`, protoze frame `DETAIL KONKRETNIHO PRODUKTU` je navrzen pro Timberwolf.
- `Lunar` zustava aktivni Core produkt v katalogu, ale neni hlavni Figma vertical slice.
- Kontrolni ne-virivkovy produkt bude `Covana`.
- Local prostredi nesmi kontaktovat `baspa.cz`, Baspa staging, Ecomail ani zive e-mailove prijemce.
- Local frontend nesmi nacitat tracking, Smartsupp, Google map embed ani vzdaleny font endpoint.

## Aktualni stav

Hotove veci:

- Poznamka k aktualnosti: body s prefixem `Overeni ...` jsou historicke mezivysledky konkretni iterace. Aktualni gate stav po cistem rebuildu je veden v `docs/arctic-scaling-rebuild-plan.md` (sekce `Current Baseline` a `Verification Gates`).

- `arctic-spas-2/` existuje.
- Fork theme `wp-content/themes/arctic/` existuje.
- WordPress lokalne bezi.
- Theme `Arctic Spas` je aktivni.
- Crawl live webu je hotovy: 135 URL.
- Prvni redirect-only seznam je hotovy.
- Figma soubory jsou importovane a citelne pres API.
- Figma missing pages jsou doplneny do lokalnich raw dumpu: grafika `docs/grafika-missing-pages.raw.json` a wireframe `docs/wireframe-missing-pages.raw.json`. Tim uz nejsou blokovane stranky `VLASTNOSTI`, `DALSI INFORMACE`, `SLUZBY`, `CERTIFIKATY`, `ZARUKA`, `KOLIK STOJI UDRZBA` ani `VLASTNOSTI DETAIL`.
- Stranky `/vlastnosti/`, `/sluzby/`, `/certifikaty/`, `/zaruka/`, `/kolik-stoji-udrzba/` a `/vlastnosti/izolace-virivky/` maji vlastni Figma sablony. Aktualizace 2026-06-08: `/dalsi-informace/` uz nesmi byt redirect na homepage; je to samostatny Figma rozcestnik s kartami `Sluzby`, `Certifikaty`, `Zaruka`, `Kolik stoji provoz a udrzba`, `Caste dotazy`, `Reference`, `O nas`, `Showroom`, `Servis` a `Kontakt`.
- Figma assety `feature-freeheat-diagram.png` a `certificate-tuv-1/2/3.png` jsou exportovane pres lokalni Figma imageRef flow do `assets-source/figma/export/graphics/` a `wp-content/uploads/import/figma/`.
- Grafika pro zbyvajici frame `SHOWROOM`, `O NAS`, `REFERENCE` a `SERVIS` je ulozena v `docs/grafika-remaining-pages.raw.json`. `/showroom/`, `/o-nas/`, `/reference/` a `/servis/` maji vlastni Figma sablony a jsou zahrnute do visual smoke testu.
- Klientsky ZIP je rozbaleny do `assets-source/owner-info/`.
- Baspa audit je hotovy v `docs/baspa-audit.md`.
- Local safety guard existuje v `wp-content/mu-plugins/arctic-local-safety.php`.
- Test requestu na `https://baspa.cz/` se v local WP blokuje s kodem `arctic_local_external_http_blocked`.
- Arctic CSS vrstva je implementovana v `wp-content/themes/arctic/src/less/` a buildi se pres `npm run css:build`.
- `docs/figma-tokens.md` popisuje aktualni Arctic tokeny a jejich mapovani na Baspa promenne.
- `docs/figma-asset-manifest.md` mapuje hlavni Figma node ID na konkretni exportovane assety a vyjimky.
- Figma API export ma skript `npm run figma:export-assets`; kvuli aktualnimu Figma rate limitu `429` je pripraveny i lokalni `.fig` imageRef export `npm run figma:export-local-assets`.
- Primarni Figma assety jsou vyexportovane do `assets-source/figma/export/graphics/` a `wp-content/uploads/import/figma/`.
- Frontend nacita `dist/css/arctic.css` po Baspa CSS.
- Logo, CTA, header preconnecty, Smartsupp a mapa jsou upravene tak, aby local nevysilal externi requesty a nezobrazoval Baspa identitu.
- Homepage header a prvni viewport HP jsou srovnane podle Figma node souradnic: desktop header, mobile header, hero text, slider tecky, Figma vyprodejovy banner a dve hlavni kategorie.
- Historicke overeni 2026-05-25: desktop HP prvni viewport byl v jedne iteraci zmeren v Chrome 1920 px proti Figma frame HP: header x=260/y=18/w=1400/h=105, hero x=0/y=0/w=1920/h=795, nadpis x=266/y=280/w=454/h=122, text x=266/y=435/w=488/h=154, leva sipka x=125/y=382/w=42/h=42, prava sipka x=1767/y=382/w=42/h=42, tecky x=932/y=767/w=45/h=10 vcetne jednotlivych bodu x=932/950/967, vyprodejovy banner x=1699/y=593/w=268/h=288; po naslednem cistem rebuildu uz tento stav neni povazovan za aktualni verdict.
- Audit 2026-05-24: Figma grafika pro HP obsahuje jeden skutecny hero obraz `Arctic Spas 07` (`1:15`, mobile `1:1974`). Dalsi uvodni slidy Lunar/showroom nejsou ve Figme jako samostatne hero plochy, proto se na homepage nezobrazuji jako realny slider. Homepage query je zamcena na seed `home-hero-arctic`; tecky a sipky zustavaji jen jako staticky Figma vizual podle navrhu.
- Desktop homepage hero nepouziva WordPress crop z media sizes, ale primo Figma asset `hp-hero-arctic-spas-07.jpg` natazeny na frame `1920 x 795`, aby sedel Figma `scaleMode: STRETCH`.
- Desktop homepage ma podle Figma HP frame srovnanou i dolni cast: produktove karty `x=258/986 y=866 w=674 h=424`, obsahovy blok `x=584 y=1405`, benefity `x=260/752/1244 y=1703`, showroom panel `x=260 y=2102`, prubeh `y=2863`, reference `y=3418`, kontaktni CTA `y=3945` a footer `y=4428`.
- Historicke overeni 2026-05-25: homepage footer byl zmeren proti Figma footer komponentu: footer x=0/w=1920/h=773, container x=260/w=1400/h=773, sloupce x=260/541/822 s relativnim y=86, rychly kontakt x=1070/y=60/w=592/h=347, logo dole x=909/y=514/w=102/h=57; aktualni pass/fail stav je nutne overit znovu po cistem buildu.
- Produktove karty na HP pouzivaji Figma image crop smerem k `imageTransform` z node `1:33` a `1:34`, ne defaultni WordPress center crop.
- Hero promo banner je odblokovany mimo clipping slideru, takze se zobrazuje i spodní Figma button v presne pozici. Showroom kolaz ma poradi obrazku podle Figma node souradnic `1:123`, `1:125`, `1:124`.
- Hero promo banner bere produktovy obrazek z Figma node `1:254` / mobile `1:1992`. CSS kreslena bila nahradni ikona se nesmi zobrazit; fallback je nahrazen figmovym produktem a oranzovym `%` badge.
- Benefit ikony a showroom pin jsou vyrezane z ulozeneho Figma HP screenshotu `docs/screenshots/figma-hp.png`; kontaktni portret `1:50` je exportovany z Figma imageRef. Vse je zapsane v `docs/figma-asset-manifest.md` a neni prevzate ze stareho Arctic webu.
- Mobile homepage top podle `GM - HP` ma Figma hero crop, hero vysku 556 px, vyprodejovy banner na y=562 a dve hlavni kategorie od y=842.
- Mobile homepage dolni cast podle `GM - HP` ma Figma showroom kolaz: panel `x=20/y=2162/w=335/h=695`, fotky `1:2081/1:2082/1:2083`, badge `x=139/y=2254/w=121/h=123`; reference maji heading `y=3728`, kartu `x=20/y=3793/w=318/h=232` a CTA pod carousel.
- Mobile menu podle `GM - HP menu` ma Figma rozmery: logo `x=20/y=7/w=86/h=48`, close button `x=310/y=9/w=45/h=45`, tmavy panel `#23282f`, search `x=26/y=527/w=323/h=44`, placeholder `Zadejte hledany vyraz` a ikonu search `x=311/y=537/w=24/h=24`.
- Homepage pouziva Figma logo a hlavni obrazove assety z node/export manifestu.
- Automaticky Figma audit `npm run figma:audit` kontroluje HP/header/mobile top i desktopovou dolni cast HP proti Figma souradnicim. Navic hlida realny Chrome viewport `1903 px`, pevny Figma hero render `1920 x 795`, designove Figma assety z `uploads/import/figma/` a obsahove produktove/reference fotky z legacy importu, ne nahodne WP cropy nebo Figma placeholdery.
- Audit je rozsireny i na hlavni desktopove Figma pruchody `/virivky/`, `/product/timberwolf/` a `/kontakt/`. Kontroluje souradnice sekci, produktu, konfiguraci, rychleho kontaktu, mapy, kontaktu a footeru podle Figma passu; u produktu navic overuje, ze produktove karty a Timberwolf detail pouzivaji legacy produktove fotky, ne Figma placeholdery.
- URL audit 2026-05-24: verejne kanonicke katalogove URL jsou `/virivky/` a `/swimspa/`, ne `/catalog/virivky/` a `/catalog/swimspa/`. Stranky s `page_product_category` se uz nepresmerovavaji na taxonomii, ale renderuji Figma katalogovou sablonu primo na kratke URL. `get_term_link()` pro propojene produktove kategorie vraci odpovidajici page permalink, interni odkazy a testy jsou prepnute na kratke URL a `/catalog/...` slouzi jen jako 301 fallback.
- Token audit 2026-05-24: `docs/figma-api-summary.json` je pouze top-level souhrn. Detailni Figma node dumpy existuji v `docs/figma-grafika-nodes.summary.json` a `docs/figma-wireframe-nodes.summary.json`, ale CSS tokeny v `src/less/_tokens.less` zatim nejsou plne generovane ani stoprocentne trasovatelne na jednotlive Figma nody. Pred produkcnim schvalenim se musi projit token pass: barvy, fonty, radiusy, stiny a spacing porovnat proti Figma grafice a rozdily zapsat do `docs/figma-tokens.md`.
- Asset audit 2026-05-24: Figma assety fyzicky existuji ve `wp-content/uploads/import/figma/` a cast je seedovana i jako media attachment. Produkcni deploy musi tuto import slozku zahrnout, nebo se musi vsechny sablonami pouzivane assety seednout do media library a pridat asset smoke, ktery overi HTTP 200 pro kazdy `uploads/import/figma/*` odkaz.
- Katalog virivek ma desktop Figma pass podle frame `KATEGORIE` az po footer: header, top kontakt, breadcrumb, hero, promo banner, Vlastnosti/Zaruka, series switcher, serie Custom/Classic/Core, produktove karty z legacy Arctic produktovych fotek vlozene do Figma rozmeru, konfigurator, showroom, prubeh, reference, kontaktni CTA a footer sedi na Figma souradnice.
- Katalogy `Celoroční bazény` a `Další sortiment` pouzivaji stejnou Figma kategorii, ale hero CTA text je uz rizeny pres term meta: `Vybrat bazén` a `Prohlédnout sortiment`, aby se nepropsal nesmyslny text `Vybrat vířivku` mimo kategorii virivek.
- `Celoroční bazény` maji obsahovy hero/kartovy obraz ze stareho Arctic webu v rozmerech Figma kategorie, ne obecny HP/virivkovy hero. Stare Arctic fotky jsou primarni zdroj pro swimspa produkty, produktove karty a detaily.
- `Další sortiment` je v seedu oznacen jako accessory kategorie, proto frontend zobrazuje vsech 6 polozek sirsiho sortimentu (`Covana`, sauny, koupaci sudy, prislusenstvi, IKONO nabytek, ochlazovaci bazenek) misto pouhe serie Covana.
- Produktove karty v kategorii nepouzivaji Figma placeholder exporty `category-product-card-1/2/3.png`. Karta drzi Figma rozmer a layout, ale obraz je z `legacy-products/*.jpg` podle skutecneho produktu.
- Detail Timberwolf je hlavni Figma detail podle frame `DETAIL KONKRETNIHO PRODUKTU`; hero a produktova navigace sedi na souradnice frame, konfigurace sedi na `x=260/y=940`, karty na `y=1041/1283`, Figma konfigurator banner na `x=260/y=1608/w=1400/h=312`, barvy na `x=260/y=2022`, vyhody na `y=2866`, volitelna vybava na `y=4883`, realizace na `y=6027`, kontaktni CTA na `y=6552` a footer na `y=7035`.
- Produktovy detail uz nerozlisuje vsechny produkty jako virivky. Swimspa dostava nadpis `Celoroční bazén ...`, sirsi sortiment zustava bez virivkoveho prefixu a detailova navigace zobrazuje jen realne dostupne sekce. `Covana` proto nema falesne odkazy na barvy, vyhody virivek ani volitelnou vybavu.
- Aktivni swimspa produkty maji v seedu obsah z puvodni Arctic slozky: popis, rozmery `436 x 236 x 129 cm`, objem `5100 litrů`, trysky/protiproud a konfigurace vcetne vice variant pro `Arctic Ocean` a `Okanagan`.
- Globalni kontaktni CTA zachovava Figma komponentu, ale text se meni podle kontextu: virivky, celoročni bazeny a sirsi sortiment nepouzivaji navzajem spatny produktovy pojem.
- Produktovy mini kontakt na Timberwolf detailu je nahrazen custom Figma komponentou: karta `x=1362/y=934/w=298/h=341`, kontaktni data `y=1018`, portret `1:50` na `x=1392/y=1115` a button `x=1392/y=1195`.
- `Podpora` ma desktop Figma pass hornich sekci: heading `x=260/y=206`, tabs `x=260/y=394/w=1400/h=93`, FAQ `x=260/y=568`, 9 FAQ karet podle Figma rytmu a mini kontakt `x=1362/y=556/w=298/h=341` s portretem z node `1:50`. Sekce `Ke stazeni` ma Figma accordion: nadpis `y=1953`, chips `y=2027`, otevrena skupina `x=260/y=2109/w=1045/h=503`, zavrene skupiny `y=2632/2748`. Servisni formular sedi na Figma pozice: nadpis `y=2940`, karta `x=260/y=3114/w=1045/h=674`, inputy `x=346/w=893`, textarea `h=146`, souhlas `y=3680` a submit `x=1053/y=3668/w=186/h=50`.
- FAQ v `Podpora` uz nejsou jen pole natvrdo v sablone. Seed zaklada 9 polozek v CPT `faq`, prirazuje kategorie `Obchodni`, `Stavebni priprava`, `Montaz`, `Provoz a udrzba` a `Servis`, a `template-support.php` je cte z WordPressu. Hardcoded pole zustava pouze jako fallback, kdyby databaze FAQ byla prazdna.
- Samostatna stranka `/ke-stazeni/` uz nepouziva defaultni obsahovy seznam. Ma sablonu `template-downloads.php`, bere data z CPT `download` a zobrazuje stejny Figma download accordion/chips jako sekce podpory; visual smoke ji uklada i na desktopu a mobilu.
- Baspa fork mel nelogiku, kdy `footer.php` automaticky vkladal kontaktni CTA, ale `Podpora` a `Ke stazeni` si stejne CTA vkladaly jeste rucne. Rucni vlozeni je odstranene, aby kazda stranka mela jen jeden kontaktni blok.
- Audit frontend identity 2026-05-24: viditelny fallback `Career in BASPA` v modulu kariery je prepsany na `Kariera v Arctic Spas` a WP theme metadata uz v administraci neuvadi Baspa jako autora/popis. Interni legacy nazvy trid a text domain se zatim nemigruji, protoze nejsou viditelne ve frontendu a prejmenovani by zbytecne zvysilo riziko.
- `Showroom` uz neni defaultni page s vlozenymi obsahovymi fotkami ani obecna komponenta z HP. Sablona sleduje Figma frame `SHOWROOM`: hero `1:446`, kontaktni radek, duvody navstevy, dve obsahove fotografie `1:443/1:444`, kontaktni CTA a footer v Figma poradi.
- `Reference` uz neopakuji tri testovaci Figma karty dokola. Seed zaklada 9 editovatelnych polozek CPT `reference`; texty vychazeji ze stareho Arctic obsahu `diskuze.php` a obrazky jsou ze stareho Arctic webu nebo owner podkladu, vlozene do Figma kartoveho layoutu.
- `Kontakt` ma desktop Figma pass: heading `x=260/y=206`, kontakty `x=970`, CTA buttony `x=1424`, mapa/showroom z Figma node `1:1069` na `y=430/h=782`, karta `x=260/y=561/w=565/h=491`, pin `x=1226/y=786/w=42/h=42`, kontaktni karty `x=260/733/1206 y=1399` a `y=1704`, fakturacni udaje `x=260/y=2071` s legalnim textem z Figma wireframu a footer `y=2425`.
- `/ochrana-osobnich-udaju/` je seednuta jako WordPress privacy policy stranka. Obsah vychazi ze stareho Arctic GDPR podkladu, ale kontaktni udaje jsou upravene na aktualni Arctic/BASPA provoz; `get_privacy_policy_url()`, formulare i paticka miri na stejnou lokalni URL. Stare URL `cookies.php` a `zasady-zpracovani-osobnich-udaju.php` maji 301 redirect primo sem.
- Produktovy model ma konfigurace, barvy akrylu, sirsi typy produktu, taxonomie `product-kind` a `product-series`.
- CPT `download` a shortcode `[arctic-downloads]` jsou implementovane.
- Seed script zaklada homepage, menu, kontaktni data, kategorie a produkty. Figma pilot je Timberwolf; Lunar/Orion jsou obsahove produkty v katalogu, Husky uz ma doplnenou obsahovou konfiguraci podle puvodni Arctic stranky: 5 osob, 20 trysek, 1030 litru a jedno dvourychlostni cerpadlo.
- Redirect MU plugin resi aktivni produkty, sirsi sortiment, Dreammaker, stare Core modely a hlavni review URL z crawlu.
- Redirect audit 2026-05-24: stare obsahove URL jsou zpresnene na nove Figma stranky tam, kde uz existuji. `baspa.php` a `kariera.php` jdou na `/o-nas/`, `sluzby.php` na `/sluzby/`, `servis.php` na `/servis/`, `certifikaty.php` na `/certifikaty/`, `zaruka.php` na `/zaruka/`, provozni naklady na `/kolik-stoji-udrzba/`, izolace na `/vlastnosti/izolace-virivky/` a obecne obsahove technicke odkazy na `/vlastnosti/` nebo konkretni obsahovou stranku v dropdownu `Dalsi informace`.
- Migracni mapa je generovana prikazem `npm run migration:map` do `docs/migration-map.csv`. Posledni vystup ma 135 radku: 75 sloucenych redirectu, 26 downloadu k importu, 1 chybejici download presmerovany na `/ke-stazeni/`, 22 produktu, 5 vyrazenych produktu, 4 polozky sirsiho sortimentu a 2 hlavni stranky.
- Stare PDF URL se dynamicky presmeruji na importovany media soubor podle `download_original_url`; chybejici PDF padaji na `/ke-stazeni/`.
- Download audit 2026-05-24: lokalni WP obsahuje 26 publikovanych `download` polozek a vsechny maji `download_original_url` i `download_file_url`. Testovane stare PDF URL vraci 301 na lokalni media soubor; chybejici `as-sluzby-cenik-2022.pdf` vraci 301 na `/ke-stazeni/`.
- Produktovy obsahovy audit 2026-05-24: prikaz `npm run legacy:products` extrahuje z `../Arctic-spas/www` obsah 24 dostupnych produktovych stranek do `wp-content/uploads/import/legacy-content/product-data.json`. Seed z nej doplnuje popisy a zakladni parametry produktu tam, kde Figma nema obsah a kde predtim zustaval pracovni placeholder.
- Produktovy obsahovy smoke test `npm run product:smoke` prochazi aktivni virivky, swimspa i sirsi sortiment, hlida konfigurace standardnich produktu, Arctic identitu, placeholdery a mojibake v obsahu. Soucasne overuje editovatelne FAQ na `/podpora/` a zakaznicke reference na `/reference/`.
- Interni link smoke test `npm run link:smoke` prochazi 21 hlavnich vstupnich stran, blokuje odkazy na `baspa.cz`, zive `arctic-spas.cz`, Ecomail a Smartsupp, a overuje 60 realnych internich URL vcetne privacy stranky. Technicke WordPress discovery URL a staticke assety ignoruje, aby test resil skutecnou navigaci.
- Kontaktni formular ma local reCAPTCHA bypass, neposila maily v localu, nema hardcoded Bcc a jeden POST se zpracuje jen jednou.
- Kontaktni formularovy pipeline je po Baspa auditu zpevneny: hodnoty z POSTu jdou pres `wp_unslash()` + sanitizaci, AJAX handler uz nebere cestu sablony z POSTu, Ecomail pouziva `wp_remote_post()` misto cURL a debug odpovedi se nevypisuji do frontendu.
- `npm run form:smoke` overuje realny AJAX pruchod kontaktniho a servisniho formulare, ulozeni do `contact` CPT, local reCAPTCHA metadata a uklid testovacich zaznamu.
- `npm run redirect:smoke` overuje 133 starych Arctic URL z `docs/migration-map.csv`: aktivni produkty, vyrazene produkty, sloucene obsahove stranky, sirsi sortiment, PDF downloady a chybejici PDF fallback.
- `npm run local:safety` overuje, ze WordPress bezi jako `local`, ma zapnute blokovani externich HTTP requestu i mailu a ze pokus o request na `baspa.cz`/Ecomail konci kodem `arctic_local_external_http_blocked`.
- `npm run search:smoke` overuje AJAX vyhledavani na lokalni `admin-ajax.php`, nalezeni Timberwolfu a odmitnuti neplatneho nonce.
- `npm run qa:local` spousti cely lokalni QA pruchod: CSS build, local safety, Figma audit, visual smoke, produktovy obsahovy smoke, link smoke, formulare, vyhledavani a redirecty.
- AJAX vyhledavani uz nebere libovolne post typy/taxonomie z POSTu; hodnoty se sanitizuji a validuji proti registrovanym WordPress typum/taxonomiim. Endpoint ma nonce, lehky IP transient rate limit a limit 10 vysledku pro prispevky i termy.
- Vlastni admin settings stranky modulu maji nonce, capability gate, `check_admin_referer()`, unslash/sanitizaci a escapovany vystup hodnot. Post metabox a term meta save handlery jsou stejne zpevnené proti slashovanym POST hodnotam.
- SVG upload uz neni globalne povoleny jako v Baspa. Default je vypnuty; zapnout jde jen vedome konstantou/filtrem pro admina.
- reCAPTCHA badge uz neni natvrdo schovany CSS jako v Baspa; skryti je mozne jen vedomym filtrem po vyreseni pravniho textu.
- Smartsupp uz nema ve forku hardcoded Baspa klic. Chat/preconnect se vypise jen v `production` prostredi a jen pokud je nastaveny `arctic_smartsupp_key`.
- Theme ma zakladni SEO vrstvu bez externiho pluginu: canonical, meta description, Open Graph, Twitter card, Product JSON-LD pro produktove detaily a `noindex,nofollow` pro non-production prostredi.
- Smoke test hlavnich cest neukazuje `baspa.cz`, Smartsupp, tracking preconnecty, Ecomail URL, Google Fonts ani Google map embed. Jedina povolena viditelna zminka `BASPA s.r.o.` je fakturacni legal entity na `/kontakt/`, protoze je primo ve Figma wireframu.
- Smoke test soucasne blokuje verejne placeholdery `Lorem ipsum`, `Hello world!`, `Sample Page`, `Hello Pattern`, `example.com` a pracovni texty typu `bude dopln`.
- Smoke test soucasne hlida mojibake sekvence napric hlavnimi strankami, aby se rozbita cestina z PowerShell/encoding problemu nepropsala do verejneho HTML.
- Smoke test hlida, ze kazda hlavni URL ma lokalni canonical, pouzitelnou meta description a zakladni Open Graph metadata.
- Smoke test overuje i `robots.txt` a core WordPress sitemap index `/wp-sitemap.xml` na lokalni domene.
- `npm run visual:smoke` prochazi hlavni URL vcetne `Dalsi sortiment`, kontroluje zakazane stringy, zakazane externi browser requesty, horizontalni overflow na desktopu/mobilu, cesky 404 fallback a uklada desktop/mobile screenshoty Figma stranek, katalogu `Swimspa`/`Další sortiment` i detailu `Husky`, `Athabascan` a `Covana`.
- Defaultni WP obsah `Hello world!` a `Sample Page` seed odstranuje, aby se nepropsal do novinek ani navigace.
- Historicke overeni: PHP lint upravenych sablon prosel, seed se propsal do lokalniho WordPressu, swimspa hero pouziva Figma kategoriovy asset, Dalsi sortiment zobrazuje 6 polozek, swimspa/Covana detaily a globalni CTA nemaji falesny virivkovy wording a Smartsupp nema hardcoded Baspa klic. Cast tvrzeni o kompletne zelenem `npm run qa:local` uz neplati po cistem rebuildu a musi se vzdy brat z aktualniho gate behu.
- Aktualni kontrolni screenshoty jsou v `docs/screenshots/`, vcetne `home-desktop-playwright.png`, `category-swimspa-desktop-playwright.png`, `category-dalsi-sortiment-desktop-playwright.png`, `product-timberwolf-desktop-playwright.png`, `product-husky-desktop-playwright.png`, `product-husky-mobile-playwright.png` a dalsich hlavnich stran.
- Overeni 2026-05-24: nove Figma informacni stranky maji v Playwright kontrole na desktopu 1920 px presne nastavene hlavni Figma souradnice. `/vlastnosti/`, `/sluzby/`, `/certifikaty/`, `/zaruka/`, `/kolik-stoji-udrzba/` a `/vlastnosti/izolace-virivky/` sedi na heading height, contact CTA y a footer y podle Figma frame. Header dropdown `Dalsi informace` obsahuje polozky `Sluzby`, `Certifikaty`, `Zaruka`, `Kolik stoji provoz a udrzba`, `Podpora`, `Reference`, `O nas`, `Showroom`, `Servis` a `Kontakt`; screenshot kontroly je `docs/screenshots/header-dalsi-informace-dropdown.png`.
- Overeni 2026-05-24: `/o-nas/`, `/reference/` a `/servis/` maji Figma souradnice na desktopu 1920 px: heading, contact CTA a footer sedi podle frame `O NAS`, `REFERENCE` a `SERVIS`. Seed helper je opraveny tak, aby child page `/vlastnosti/izolace-virivky/` pri opakovanem seedu nevytvarel duplikaty.
- Historicke overeni 2026-05-25: po oprave header/hero/footer a odstraneni redirect chainu v jedne iteraci proslo cele `npm run qa:local` (CSS build, local safety, Figma audit, visual smoke, produktovy obsah, link smoke, formulare, vyhledavani a 133 legacy redirectu). Tento vysledek neni po pozdejsim cistem rebuildu povazovan za aktualni pass stav; rozhodujici je posledni gate run podle kanonickeho scaling recovery planu.

## Souvisly postup realizace

Nejdrive se z lokalniho WordPressu udela bezpecny a stabilni vyvojovy zaklad. Docker konfigurace zustane hlavni vstup do projektu, `tools/wp-local.ps1` zustane ovladaci skript a README bude drzet aktualni prikazy. Local safety guard zustane aktivni po celou dobu vyvoje. Kazda ziva integrace se bude zapinat az podle prostredi, nikdy implicitne v localu.

Hned potom se doplni Arctic CSS vrstva. V theme vznikne `src/less/arctic.less`, `src/less/_tokens.less`, `src/less/_brand.less`, `src/less/_components.less` a vystup `dist/css/arctic.css`. Tento soubor se nacita po Baspa CSS. Pokud bude na zacatku rychlejsi psat primo do `dist/css/arctic.css`, je to povolene jako prechodny krok, ale zdrojove tokeny se budou udrzovat v `src/less/`, aby se rebrand nerozpadl do puvodniho obriho `style.css`.

Z Figmy se vytahne prakticky handoff a node map: barvy, fonty, velikosti, spacing, logo, ikony, buttony, produktove karty, header, mobile menu, homepage, kategorie, produktovy detail, podpora a kontakt. Vystup bude `docs/figma-asset-manifest.md`, `docs/figma-tokens.md` a exporty v `assets-source/figma/export/`. Wireframe se pouzije jako architektura sablon, graficka Figma jako finalni skin. Baspa se nebere jako vizualni fallback; bere se jen jako funkcni WordPress kostra.

Globalni shell webu se upravi tak, aby uz pri prazdnem obsahu pusobil jako Arctic: logo, header, kontaktni bar, mobile menu, footer, zakladni barvy, buttony, kontakty, copyright a zakladni typografie. Soucasne se vycisti zbytkove Baspa kontakty ve frontendu. Staticke prekladove zminky v `.po/.pot` se mohou docistit pozdeji, ale nesmi se zobrazovat na webu ani spoustet requesty.

Pak se upravi produktovy model kolem realneho Figma detailu `Timberwolf`. To je klicove, protoze Figma detail, konfigurace, barvy a produktove fakty jsou navrzene prave pro Timberwolf: Serie Classic, 3 osoby, 217 x 174 x 98 cm, 884 litru, Prestige 15/1 a Signature 30/2. Do produktu se doplni pole pro typ produktu, radu, prezentacni rezim, cenu/cenovy text, konfigurace, parametry, galerii, barvy, dokumenty, CTA a puvodni URL. `Lunar` zustava produkt v katalogu Core, ale nevede vizualni implementaci detailu.

Na `Timberwolf` se postavi prvni skutecny Figma vertical slice. Administrace, data, single produkt, produktova karta, listing, galerie, parametry, konfigurace, barvy, vyhody a poptavkove CTA musi fungovat dohromady a sedet na Figma frame. Dokud tento pruchod neni cisty, nehromadne se nemigruji dalsi produkty. Jakmile `Timberwolf` sedi, stejne pole a sablony se pouziji pro `Lunar`, `Orion` a `Husky`.

Nasledne se vlozi `Covana` jako kontrolni produkt mimo virivky. Bude v jednom `product` CPT, ale s jinym typem produktu a prezentacnim rezimem. Tim se overi, ze sirsi sortiment jako sauny, koupaci sudy, ochlazovaci bazenek, IKONO nabytek, Covana a prislusenstvi nemusi dostat vlastni CPT. Aktualni implementace je drzi jako `landing_section`, aby local frontend neodskakoval na externi e-shopy. Pokud bude mit polozka plny obsah, zustane jako detail; pokud ma byt jen proklik, pred produkci se prepne na `external_shop`.

Po overeni `Timberwolf` a `Covana` se dokonci katalog. Vzniknou listingy `/virivky/`, `/swimspa/` a `/catalog/dalsi-sortiment/`, karta produktu, taxonomicke nebo landing sablony pro rady Core/Classic/Custom/AWP, zakladni filtry a souvisejici CTA. Aktivni produkty se vezmou z local Arctic archivu, crawlu a briefu. Vyradene produkty se nevkladaji jako verejne aktivni produkty, ale jejich stare URL se zapisi do redirect mapy.

Homepage se postavi podle frame `HP`: hero, hlavni produktove smery, vyhradni prodejce, montaz/podpora/servis, showroom, prubeh zakazky, ukazky realizaci, kontaktni CTA a footer. HP hero, bannery, logo, ikony a UI kompozice se exportuji z Figma node ID. Obsahove fotky kategorii, realizaci a referenci se berou ze stareho Arctic webu nebo owner podkladu a orezavaji se do Figma layoutu.

Produktova kategorie se postavi podle frame `KATEGORIE`: text kategorie, listing, filtry, produktove karty, sekce vyhod a CTA. Detail produktu se postavi podle frame `DETAIL KONKRETNIHO PRODUKTU`: galerie/hero, konfigurace, parametry, barvy, vlastnosti, dokumenty, souvisejici produkty a poptavkovy blok.

Podpora se postavi jako editovatelny rozcestnik. `Ke stazeni` nebude staticky seznam v obsahu stranky, ale listing nad CPT `download`. Download polozky dostanou kategorii dokumentu: katalog, navod, rozmery, zaruky, stavebni pripravenost, uprava vody, servis. Stare PDF URL se bud importuji jako media a redirectuji na nove media/detail URL, nebo se zachyti redirectem na prislusnou download polozku.

Showroom a kontakt se postavi z noveho obrazoveho archivu. Pouziji se fotky prodejny z klientskych podkladu, ne stare fotky ze stareho webu. Kontakt bude obsahovat hlavni poptavku, servis, adresu, oteviraci dobu, telefon, e-mail a jasne CTA. Mapa se resi jako link nebo embed podle finalni cookie/tracking strategie; do localu se nesmi pridavat nic, co nekontrolovane vola externi sluzby.

Reference se prevedou do existujici Baspa logiky referenci. Pokud stary web obsahuje jen volne citace a galerie, normalizuji se do `reference` polozek s titulkem, textem, fotkami a pripadne produktem. FAQ se prevede do editovatelne struktury, ne jako dlouha staticka HTML stranka.

Migracni tabulka se vyrobi z `docs/crawl-live/arctic-spas-live-crawl.csv`. Kazdy radek dostane starou URL, novou URL, typ obsahu, migracni akci, redirect, zdroj obsahu a poznamku. Z 75 `review_migrate_page` URL se odstrani duplicity a temata se slouci do realnych stránek, aby se nemigrovaly tri varianty stejne landing page. Cilem je zachovat SEO hodnotu, ne kopirovat historickou strukturu 1:1.

Importery budou v `tools/`. Prakticky vzniknou skripty pro produkty, stranky, downloady, media a redirect mapu. Importer bude umet vytvorit nebo aktualizovat WP obsah podle puvodni URL, aby sel pustit opakovane. U produktu nastavi taxonomie, meta pole, galerii, featured image a puvodni URL. U stranek vycisti stare HTML, ponecha obsah a vlozi ho do nove sablonove struktury. U medii vynecha balast a importuje jen whitelist.

Media workflow je rozdeleny: layout/design je Figma-first, obsahove fotografie jsou old-web/owner-first. Designove assety z Figmy se exportuji z Figma node ID do `assets-source/figma/export/` a `wp-content/uploads/import/figma/`. Produktove fotky, virivky, swimspa, reference a realizace se importuji ze stareho Arctic webu nebo `assets-source/owner-info/`. Kazdy pouzity zdroj se zapisuje do manifestu.

Formulare a vlastni admin nastaveni jsou po Baspa auditu zpevnené. Frontendovy pipeline uz ma hotovy zakladni cleanup: raw `$_POST` nejde do vystupu bez escapovani, processing template z POSTu je nahrazeny server-side whitelistem, e-maily se validuji, hardcoded Bcc je odstraneny, Ecomail jede pres `wp_remote_post()` a local blokovani mailu zustava aktivni. Admin settings stranky maji nonce/capability pattern, term meta save handlery a post metabox ukladaji az po `wp_unslash()` a sanitizaci. Staging nesmi posilat ostre poptavky bez zamerneho nastaveni.

SEO se resi soubezne s migraci. Kazda stara `.php` URL dostane novou URL nebo 301 redirect. Title a meta description se prenesou tam, kde jsou uzitecne; duplicity se prepisou. Vznikne sitemap, robots, Open Graph obrazky a schema pro produkty/FAQ tam, kde to dava smysl. Vyradene produkty jdou redirectem na relevantni kategorii, ne do aktivniho katalogu.

QA probehne pres realne uzivatelske cesty: homepage -> virivky -> Timberwolf -> poptavka, homepage -> swimspa -> detail, podpora -> download, showroom -> kontakt, reference -> CTA. Soucasne se zkontroluje mobile menu, tablet, desktop, galerie, formulare, interni odkazy, 404, redirecty, PHP logy, externi requesty, zbytkove Baspa texty ve frontendu a rozdily proti Figma screenshotum.

Staging se nasadi az po cistem lokalu. Na stagingu se local-only blokace vypnou podle prostredi, ale e-maily a integrace zustanou opatrne nakonfigurovane. Provede se plny import obsahu, vizualni kontrola proti Figme, obsahova kontrola proti live webu a briefu, redirect test a rychlostni kontrola.

Launch probehne az po content freeze. Pred preklopenim se udela posledni crawl stareho webu, finalni import rozdilu, export redirect mapy, zaloha a kontrola hlavních URL. Po preklopeni se kontroluje homepage, kategorie, produktovy detail, formular, sitemap, Search Console, 404 logy a rychlost. Prvni dny po launchi se aktivne opravují nalezene redirecty a obsahove drobnosti.

## Soubory, ktere vzniknou nebo se budou upravovat

Theme:

- `wp-content/themes/arctic/functions.php`
- `wp-content/themes/arctic/inc/styles.php`
- `wp-content/themes/arctic/theme.json`
- `wp-content/themes/arctic/header.php`
- `wp-content/themes/arctic/footer.php`
- `wp-content/themes/arctic/single-product.php`
- `wp-content/themes/arctic/archive.php`
- `wp-content/themes/arctic/modules/products/**`
- `wp-content/themes/arctic/modules/contacts/**`
- `wp-content/themes/arctic/templates/**`
- `wp-content/themes/arctic/parts/**`

Arctic CSS:

- `wp-content/themes/arctic/src/less/arctic.less`
- `wp-content/themes/arctic/src/less/_tokens.less`
- `wp-content/themes/arctic/src/less/_brand.less`
- `wp-content/themes/arctic/src/less/_components.less`
- `wp-content/themes/arctic/dist/css/arctic.css`

Local safety:

- `wp-content/mu-plugins/arctic-local-safety.php`
- `docker-compose.yml`
- `tools/wp-local.ps1`

Import a migrace:

- `tools/crawl-live-site.ps1`
- `wp-content/themes/arctic/tools/seed-pilot-content.php`
- `tools/import-products.ps1`
- `tools/import-pages.ps1`
- `tools/import-downloads.ps1`
- `tools/import-media.ps1`
- `tools/build-redirect-map.ps1`
- `docs/migration-map.csv`
- `docs/redirect-map.csv`
- `wp-content/mu-plugins/arctic-redirects.php`

Design a obsah:

- `docs/figma-tokens.md`
- `docs/figma-structure.md`
- `docs/figma-asset-manifest.md`
- `docs/figma-handoff.md`
- `assets-source/figma/export/`
- `assets-source/owner-info/`

## Minimalni obsah pro prvni kompletni pruchod

Prvni kompletni pruchod musi obsahovat:

- homepage,
- `/virivky/`,
- detail `Timberwolf` podle Figma detailu,
- detail `Lunar` jako obsahovy Core produkt,
- detail `Orion`,
- detail `Husky`,
- jedna ukazkova Classic virivka,
- jedna ukazkova Custom virivka,
- jedna swimspa,
- `Covana` jako sirsi sortiment,
- `Podpora`,
- `Ke stazeni`,
- `Showroom`,
- `Kontakt`,
- zakladni redirecty pro Dreammaker a stare Core modely.

Jakmile tento pruchod sedi, zbytek obsahu je uz hlavne rozsirovani stejneho modelu.

## Kontrola proti Figme, Baspa a obsahu

U kazde sablony se kontroluje:

- zda architektura sedi na Figma wireframe,
- zda vizual, rozmery a assety sedi na Figma grafiku,
- zda je programatorsky co nejbliz Baspa bez kopirovani Baspa vizualu,
- zda obsah odpovida staremu Arctic archivu/crawlu,
- zda brief nepozaduje vyradit nebo aktualizovat puvodni obsah,
- zda ve frontendu nezustal Baspa kontakt, staging URL nebo stary obchodni text.

## Bezpecnostni pravidla

Local:

- blokovat externi HTTP requesty mimo localhost,
- blokovat e-maily,
- nevolat Ecomail,
- neukladat produkcni tajemstvi,
- nepouzivat `../baspa.cz/wp-config.php`,
- nevypisovat tokeny ani hesla do dokumentace.

Staging:

- e-maily jen na testovaci adresu nebo pres log,
- integrace zapinat jen explicitne,
- roboty podle potreby noindex,
- produkcni redirecty testovat pred zapnutim.

Producke:

- API klice mimo git,
- produkcni prijemci ve spravnem nastaveni,
- finalni cookie/tracking konfigurace,
- zalohy pred launch.

## QA checklist

Pred predanim musi projit:

- homepage desktop/mobile,
- header a mobile menu,
- produktova kategorie,
- produktovy detail,
- konfigurace produktu,
- produktove galerie,
- poptavkove CTA,
- kontaktni formular,
- servisni kontakt,
- showroom fotky,
- download listing,
- PDF odkazy,
- FAQ,
- reference,
- privacy policy stranka,
- interní odkazy,
- zakazane externi odkazy na stare/live sluzby,
- 301 redirecty,
- 404 crawl,
- sitemap,
- robots,
- title/meta,
- Open Graph,
- PHP log,
- kontrola externich requestu,
- kontrola zbytkovych Baspa textu ve frontendu.

## Casovy odhad

Rychly MVP s hlavnim obsahem: 10 az 15 pracovnich dni.

Kvalitni produkcni verze: 3 az 4 tydny.

Prakticky rozpad prace:

- local safety, CSS vrstva, Figma tokeny a global shell: 2 az 3 dny,
- Timberwolf + Covana vertical slice: 2 az 3 dny,
- katalog, homepage a hlavni sablony: 5 az 7 dni,
- migrace obsahu, media, downloady a redirecty: 4 az 6 dni,
- formulare, SEO, QA a staging: 4 az 6 dni,
- launch a post-launch kontrola: 1 az 2 dny.

## Definice hotovo

Projekt je hotovy, kdyz:

- lokalni a staging prostredi jsou oddelena od zivych Baspa systemu,
- reference slozky zustaly netknute,
- Figma architektura je prevedena do WordPress sablon,
- grafika z Figmy je prevedena do Arctic CSS vrstvy,
- produkty jsou spravovatelne ve WP,
- Timberwolf, Lunar, Orion a Husky jsou kompletni,
- sirsi sortiment je pokryty pres jeden `product` model,
- downloady jsou spravovane pres CPT `download`,
- Dreammaker a stare Core modely nejsou aktivni,
- stare URL maji nove stranky nebo redirecty,
- formulare jsou bezpecne a neposilaji nic nespravnym prijemcum,
- homepage, kategorie, detail, podpora, showroom, kontakt a reference jsou responzivni,
- nejsou kriticke 404,
- SEO metadata jsou doplnena,
- klient umi upravovat hlavni obsah bez vyvojare.

