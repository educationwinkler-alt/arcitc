# Arctic Spas WordPress: end-to-end realizacni plan

Datum: 2026-05-23  
Pracovni prostor: `arctic-spas-2/`  
Lokalni web: `http://localhost:8090`  
Admin: `http://localhost:8090/wp-admin/`  
Technicky vzor: `../baspa.cz/`  
Obsahovy zdroj: `../Arctic-spas/`, crawl `docs/crawl-live/`, live web jen pro overeni obsahu  
Designovy zdroj: Figma wireframe + Figma grafika

## Tvrde pravidlo od 2026-05-23

Tato cast ma prednost pred starsimi formulacemi nize v dokumentu.

Od ted je Figma jediny zdroj UX a grafiky. Vizualni assety, layout, rozmery, logo, hero obrazky, bannery, ikony, karty, spacing a typografie se nesmi brat ze stareho `arctic-spas.cz` a nesmi se skladat odhadem. Stary Arctic web je pouze obsahovy zdroj: texty, produkty, parametry, dokumenty, stare URL a pripadne produktove fotky tam, kde ve Figme neni konkretni asset.

Pravidlo zdroju:

1. `Arctic-spas.cz wireframe` ridi architekturu sablon a poradi sekci.
2. `Arctic-spas.cz grafika` ridi finalni vizual, rozmery, assety a CSS.
3. `../baspa.cz/` je read-only WordPress/funkcni zaklad, ne vizualni fallback.
4. `../Arctic-spas/` je read-only obsahovy archiv.
5. Pokud je asset ve Figme, exportuje se primo z Figma node ID do `assets-source/figma/export/`.
6. Pokud Figma asset chybi, lze pouzit stary Arctic jen jako obsahovy nebo produktovy zdroj a vyjimka se zapise do `docs/figma-asset-manifest.md`.

Tento dokument je souvisly realizacni runbook. Neni to navrh k dalsimu rozpadani na faze; je to poradi prace od aktualniho stavu az po produkcni spusteni.

## Vysledek, ktereho dosahneme

Novy Arctic Spas web bude WordPress web postaveny jako kontrolovany fork Baspa. Programatorsky vychazi z Baspa, ale UX a grafika se nevymysli a neopisuji ze stareho Arctic webu. Architekturu urcuje Figma wireframe, finalni vizual urcuje Figma grafika a obsah urcuje stary Arctic archiv/crawl.

Web bude spravovatelny z administrace. Produkty, downloady, reference, FAQ, kontaktni data a nabidky nebudou napevno ve statickem HTML, ale v editovatelnem modelu WordPressu.

## Pevna rozhodnuti

- Pracuje se jen v `arctic-spas-2/`.
- `../baspa.cz/` se neupravuje. Je to read-only technicka reference.
- `../Arctic-spas/` se neupravuje. Je to read-only archiv stareho webu a assetu.
- Baspa theme fork zustava v `wp-content/themes/arctic/`.
- Lokalni WordPress bezi v Dockeru na `http://localhost:8090`.
- Baspa `dist/css/style.css` zustava jako zaklad.
- Arctic rebrand jde do samostatne vrstvy `dist/css/arctic.css`.
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

- `arctic-spas-2/` existuje.
- Fork theme `wp-content/themes/arctic/` existuje.
- WordPress lokalne bezi.
- Theme `Arctic Spas` je aktivni.
- Crawl live webu je hotovy: 135 URL.
- Prvni redirect-only seznam je hotovy.
- Figma soubory jsou importovane a citelne pres API.
- Figma missing pages jsou doplneny do lokalnich raw dumpu: grafika `docs/grafika-missing-pages.raw.json` a wireframe `docs/wireframe-missing-pages.raw.json`. Tim uz nejsou blokovane stranky `VLASTNOSTI`, `DALSI INFORMACE`, `SLUZBY`, `CERTIFIKATY`, `ZARUKA`, `KOLIK STOJI UDRZBA` ani `VLASTNOSTI DETAIL`.
- Stranky `/vlastnosti/`, `/dalsi-informace/`, `/sluzby/`, `/certifikaty/`, `/zaruka/`, `/kolik-stoji-udrzba/` a `/vlastnosti/izolace-virivky/` maji vlastni Figma sablony. Uz nepouzivaji obecny `page.php`, placeholder obsah ani showroom jako nahradu.
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
- Audit 2026-05-24: Figma grafika pro HP obsahuje jeden skutecny hero obraz `Arctic Spas 07` (`1:15`, mobile `1:1974`). Dalsi uvodni slidy Lunar/showroom nejsou ve Figme jako samostatne hero plochy, proto se na homepage nezobrazuji jako realny slider. Homepage query je zamcena na seed `home-hero-arctic`; tecky a sipky zustavaji jen jako staticky Figma vizual podle navrhu.
- Desktop homepage hero nepouziva WordPress crop z media sizes, ale primo Figma asset `hp-hero-arctic-spas-07.jpg` natazeny na frame `1920 x 795`, aby sedel Figma `scaleMode: STRETCH`.
- Desktop homepage ma podle Figma HP frame srovnanou i dolni cast: produktove karty `x=258/986 y=866 w=674 h=424`, obsahovy blok `x=584 y=1405`, benefity `x=260/752/1244 y=1703`, showroom panel `x=260 y=2102`, prubeh `y=2863`, reference `y=3418`, kontaktni CTA `y=3945` a footer `y=4428`.
- Produktove karty na HP pouzivaji Figma image crop smerem k `imageTransform` z node `1:33` a `1:34`, ne defaultni WordPress center crop.
- Hero promo banner je odblokovany mimo clipping slideru, takze se zobrazuje i spodní Figma button v presne pozici. Showroom kolaz ma poradi obrazku podle Figma node souradnic `1:123`, `1:125`, `1:124`.
- Hero promo banner bere produktovy obrazek z Figma node `1:254` / mobile `1:1992`. CSS kreslena bila nahradni ikona se nesmi zobrazit; fallback je nahrazen figmovym produktem a oranzovym `%` badge.
- Benefit ikony a showroom pin jsou vyrezane z ulozeneho Figma HP screenshotu `docs/screenshots/figma-hp.png`; kontaktni portret `1:50` je exportovany z Figma imageRef. Vse je zapsane v `docs/figma-asset-manifest.md` a neni prevzate ze stareho Arctic webu.
- Mobile homepage top podle `GM - HP` ma Figma hero crop, hero vysku 556 px, vyprodejovy banner na y=562 a dve hlavni kategorie od y=842.
- Mobile homepage dolni cast podle `GM - HP` ma Figma showroom kolaz: panel `x=20/y=2162/w=335/h=695`, fotky `1:2081/1:2082/1:2083`, badge `x=139/y=2254/w=121/h=123`; reference maji heading `y=3728`, kartu `x=20/y=3793/w=318/h=232` a CTA pod carousel.
- Mobile menu podle `GM - HP menu` ma Figma rozmery: logo `x=20/y=7/w=86/h=48`, close button `x=310/y=9/w=45/h=45`, tmavy panel `#23282f`, search `x=26/y=527/w=323/h=44`, placeholder `Zadejte hledany vyraz` a ikonu search `x=311/y=537/w=24/h=24`.
- Homepage pouziva Figma logo a hlavni obrazove assety z node/export manifestu.
- Automaticky Figma audit `npm run figma:audit` kontroluje HP/header/mobile top proti Figma souradnicim a zaroven hlida, ze logo, hero background, hero image, vyprodejovy banner, kategoriove intro, konfigurator, showroom kolaz a kontaktni mapa berou Figma assety z `uploads/import/figma/`, ne WP crop ani stary Arctic vizual.
- Katalog virivek ma desktop Figma pass podle frame `KATEGORIE` az po footer: header, top kontakt, breadcrumb, hero, promo banner, Vlastnosti/Zaruka, series switcher, serie Custom/Classic/Core, produktove karty z Figma assetu, konfigurator, showroom, prubeh, reference, kontaktni CTA a footer sedi na Figma souradnice.
- Katalogy `Celoroční bazény` a `Další sortiment` pouzivaji stejnou Figma kategorii, ale hero CTA text je uz rizeny pres term meta: `Vybrat bazén` a `Prohlédnout sortiment`, aby se nepropsal nesmyslny text `Vybrat vířivku` mimo kategorii virivek.
- `Celoroční bazény` maji hero obraz z Figma assetu `hp-category-celorocni-bazeny.png`, ne obecny HP/virivkovy hero. Stare Arctic fotky zustavaji jen pro produktove karty/detail tam, kde Figma nema konkretni modelovy asset.
- `Další sortiment` je v seedu oznacen jako accessory kategorie, proto frontend zobrazuje vsech 6 polozek sirsiho sortimentu (`Covana`, sauny, koupaci sudy, prislusenstvi, IKONO nabytek, ochlazovaci bazenek) misto pouhe serie Covana.
- Produktove karty v kategorii pouzivaji Figma exporty `category-product-card-1/2/3.png`; stare Arctic produktove fotky zustavaji obsahova vyjimka pro detaily nebo galerie, ne UX karta kategorie.
- Detail Timberwolf je hlavni Figma detail podle frame `DETAIL KONKRETNIHO PRODUKTU`; hero a produktova navigace sedi na souradnice frame, konfigurace sedi na `x=260/y=940`, karty na `y=1041/1283`, Figma konfigurator banner na `x=260/y=1608/w=1400/h=312`, barvy na `x=260/y=2022`, vyhody na `y=2866`, volitelna vybava na `y=4883`, realizace na `y=6027`, kontaktni CTA na `y=6552` a footer na `y=7035`.
- Produktovy detail uz nerozlisuje vsechny produkty jako virivky. Swimspa dostava nadpis `Celoroční bazén ...`, sirsi sortiment zustava bez virivkoveho prefixu a detailova navigace zobrazuje jen realne dostupne sekce. `Covana` proto nema falesne odkazy na barvy, vyhody virivek ani volitelnou vybavu.
- Aktivni swimspa produkty maji v seedu obsah z puvodni Arctic slozky: popis, rozmery `436 x 236 x 129 cm`, objem `5100 litrů`, trysky/protiproud a konfigurace vcetne vice variant pro `Arctic Ocean` a `Okanagan`.
- Globalni kontaktni CTA zachovava Figma komponentu, ale text se meni podle kontextu: virivky, celoročni bazeny a sirsi sortiment nepouzivaji navzajem spatny produktovy pojem.
- Produktovy mini kontakt na Timberwolf detailu je nahrazen custom Figma komponentou: karta `x=1362/y=934/w=298/h=341`, kontaktni data `y=1018`, portret `1:50` na `x=1392/y=1115` a button `x=1392/y=1195`.
- `Podpora` ma desktop Figma pass hornich sekci: heading `x=260/y=206`, tabs `x=260/y=394/w=1400/h=93`, FAQ `x=260/y=568`, 9 FAQ karet podle Figma rytmu a mini kontakt `x=1362/y=556/w=298/h=341` s portretem z node `1:50`. Sekce `Ke stazeni` ma Figma accordion: nadpis `y=1953`, chips `y=2027`, otevrena skupina `x=260/y=2109/w=1045/h=503`, zavrene skupiny `y=2632/2748`. Servisni formular sedi na Figma pozice: nadpis `y=2940`, karta `x=260/y=3114/w=1045/h=674`, inputy `x=346/w=893`, textarea `h=146`, souhlas `y=3680` a submit `x=1053/y=3668/w=186/h=50`.
- Samostatna stranka `/ke-stazeni/` uz nepouziva defaultni obsahovy seznam. Ma sablonu `template-downloads.php`, bere data z CPT `download` a zobrazuje stejny Figma download accordion/chips jako sekce podpory; visual smoke ji uklada i na desktopu a mobilu.
- Baspa fork mel nelogiku, kdy `footer.php` automaticky vkladal kontaktni CTA, ale `Podpora` a `Ke stazeni` si stejne CTA vkladaly jeste rucne. Rucni vlozeni je odstranene, aby kazda stranka mela jen jeden kontaktni blok.
- Audit frontend identity 2026-05-24: viditelny fallback `Career in BASPA` v modulu kariery je prepsany na `Kariera v Arctic Spas` a WP theme metadata uz v administraci neuvadi Baspa jako autora/popis. Interni legacy nazvy trid a text domain se zatim nemigruji, protoze nejsou viditelne ve frontendu a prejmenovani by zbytecne zvysilo riziko.
- `Showroom` uz neni defaultni page s vlozenymi obsahovymi fotkami ani obecna komponenta z HP. Sablona sleduje Figma frame `SHOWROOM`: hero `1:446`, kontaktni radek, duvody navstevy, dve obsahove fotografie `1:443/1:444`, kontaktni CTA a footer v Figma poradi.
- `Kontakt` ma desktop Figma pass: heading `x=260/y=206`, kontakty `x=970`, CTA buttony `x=1424`, mapa/showroom z Figma node `1:1069` na `y=430/h=782`, karta `x=260/y=561/w=565/h=491`, pin `x=1226/y=786/w=42/h=42`, kontaktni karty `x=260/733/1206 y=1399` a `y=1704`, fakturacni udaje `x=260/y=2071` a footer `y=2425`.
- Produktovy model ma konfigurace, barvy akrylu, sirsi typy produktu, taxonomie `product-kind` a `product-series`.
- CPT `download` a shortcode `[arctic-downloads]` jsou implementovane.
- Seed script zaklada homepage, menu, kontaktni data, kategorie a produkty. Figma pilot je Timberwolf; Lunar/Orion jsou obsahove produkty v katalogu, Husky uz ma doplnenou obsahovou konfiguraci podle puvodni Arctic stranky: 5 osob, 20 trysek, 1030 litru a jedno dvourychlostni cerpadlo.
- Redirect MU plugin resi aktivni produkty, sirsi sortiment, Dreammaker, stare Core modely a hlavni review URL z crawlu.
- Redirect audit 2026-05-24: stare obsahove URL jsou zpresnene na nove Figma stranky tam, kde uz existuji. `baspa.php` a `kariera.php` jdou na `/o-nas/`, `sluzby.php` na `/sluzby/`, `servis.php` na `/servis/`, `certifikaty.php` na `/certifikaty/`, `zaruka.php` na `/zaruka/`, provozni naklady na `/kolik-stoji-udrzba/`, izolace na `/vlastnosti/izolace-virivky/` a obecne obsahove technicke odkazy na `/vlastnosti/` nebo `/dalsi-informace/`.
- Migracni mapa je generovana prikazem `npm run migration:map` do `docs/migration-map.csv`. Posledni vystup ma 135 radku: 75 sloucenych redirectu, 26 downloadu k importu, 1 chybejici download presmerovany na `/ke-stazeni/`, 22 produktu, 5 vyrazenych produktu, 4 polozky sirsiho sortimentu a 2 hlavni stranky.
- Stare PDF URL se dynamicky presmeruji na importovany media soubor podle `download_original_url`; chybejici PDF padaji na `/ke-stazeni/`.
- Download audit 2026-05-24: lokalni WP obsahuje 26 publikovanych `download` polozek a vsechny maji `download_original_url` i `download_file_url`. Testovane stare PDF URL vraci 301 na lokalni media soubor; chybejici `as-sluzby-cenik-2022.pdf` vraci 301 na `/ke-stazeni/`.
- Produktovy obsahovy audit 2026-05-24: prikaz `npm run legacy:products` extrahuje z `../Arctic-spas/www` obsah 24 dostupnych produktovych stranek do `wp-content/uploads/import/legacy-content/product-data.json`. Seed z nej doplnuje popisy a zakladni parametry produktu tam, kde Figma nema obsah a kde predtim zustaval pracovni placeholder.
- Kontaktni formular ma local reCAPTCHA bypass, neposila maily v localu, nema hardcoded Bcc a jeden POST se zpracuje jen jednou.
- Kontaktni formularovy pipeline je po Baspa auditu zpevneny: hodnoty z POSTu jdou pres `wp_unslash()` + sanitizaci, AJAX handler uz nebere cestu sablony z POSTu, Ecomail pouziva `wp_remote_post()` misto cURL a debug odpovedi se nevypisuji do frontendu.
- AJAX vyhledavani uz nebere libovolne post typy/taxonomie z POSTu; hodnoty se sanitizuji a validuji proti registrovanym WordPress typum/taxonomiim.
- Vlastni admin settings stranky modulu maji nonce, capability gate, `check_admin_referer()`, unslash/sanitizaci a escapovany vystup hodnot. Post metabox a term meta save handlery jsou stejne zpevnené proti slashovanym POST hodnotam.
- Smartsupp uz nema ve forku hardcoded Baspa klic. Chat/preconnect se vypise jen v `production` prostredi a jen pokud je nastaveny `arctic_smartsupp_key`.
- Smoke test hlavnich cest neukazuje `Baspa`, `baspa.cz`, Smartsupp, tracking preconnecty, Ecomail URL, Google Fonts ani Google map embed.
- `npm run visual:smoke` prochazi hlavni URL vcetne `Dalsi sortiment`, kontroluje zakazane stringy, zakazane externi browser requesty, horizontalni overflow na desktopu/mobilu a uklada desktop/mobile screenshoty Figma stranek, katalogu `Swimspa`/`Další sortiment` i detailu `Husky`, `Athabascan` a `Covana`.
- Defaultni WP obsah `Hello world!` a `Sample Page` seed odstranuje, aby se nepropsal do novinek ani navigace.
- Posledni overeni: PHP lint upravenych sablon prosel, seed se propsal do lokalniho WordPressu, swimspa hero pouziva Figma kategoriovy asset, `Další sortiment` zobrazuje 6 polozek, swimspa/Covana detaily a globalni CTA nemaji falesny virivkovy wording, Smartsupp nema hardcoded Baspa klic a `npm run visual:smoke` prosel bez externich requestu.
- Aktualni kontrolni screenshoty jsou v `docs/screenshots/`, vcetne `home-desktop-playwright.png`, `category-swimspa-desktop-playwright.png`, `category-dalsi-sortiment-desktop-playwright.png`, `product-timberwolf-desktop-playwright.png`, `product-husky-desktop-playwright.png`, `product-husky-mobile-playwright.png` a dalsich hlavnich stran.
- Overeni 2026-05-24: nove Figma informacni stranky maji v Playwright kontrole na desktopu 1920 px presne nastavene hlavni Figma souradnice. `/vlastnosti/`, `/dalsi-informace/`, `/sluzby/`, `/certifikaty/`, `/zaruka/`, `/kolik-stoji-udrzba/` a `/vlastnosti/izolace-virivky/` sedi na heading height, contact CTA y a footer y podle Figma frame; `npm run visual:smoke` prochazi i s temito URL.
- Overeni 2026-05-24: `/o-nas/`, `/reference/` a `/servis/` maji Figma souradnice na desktopu 1920 px: heading, contact CTA a footer sedi podle frame `O NAS`, `REFERENCE` a `SERVIS`. Seed helper je opraveny tak, aby child page `/vlastnosti/izolace-virivky/` pri opakovanem seedu nevytvarel duplikaty.

## Souvisly postup realizace

Nejdrive se z lokalniho WordPressu udela bezpecny a stabilni vyvojovy zaklad. Docker konfigurace zustane hlavni vstup do projektu, `tools/wp-local.ps1` zustane ovladaci skript a README bude drzet aktualni prikazy. Local safety guard zustane aktivni po celou dobu vyvoje. Kazda ziva integrace se bude zapinat az podle prostredi, nikdy implicitne v localu.

Hned potom se doplni Arctic CSS vrstva. V theme vznikne `src/less/arctic.less`, `src/less/_tokens.less`, `src/less/_brand.less`, `src/less/_components.less` a vystup `dist/css/arctic.css`. Tento soubor se nacita po Baspa CSS. Pokud bude na zacatku rychlejsi psat primo do `dist/css/arctic.css`, je to povolene jako prechodny krok, ale zdrojove tokeny se budou udrzovat v `src/less/`, aby se rebrand nerozpadl do puvodniho obriho `style.css`.

Z Figmy se vytahne prakticky handoff a node map: barvy, fonty, velikosti, spacing, logo, ikony, buttony, produktove karty, header, mobile menu, homepage, kategorie, produktovy detail, podpora a kontakt. Vystup bude `docs/figma-asset-manifest.md`, `docs/figma-tokens.md` a exporty v `assets-source/figma/export/`. Wireframe se pouzije jako architektura sablon, graficka Figma jako finalni skin. Baspa se nebere jako vizualni fallback; bere se jen jako funkcni WordPress kostra.

Globalni shell webu se upravi tak, aby uz pri prazdnem obsahu pusobil jako Arctic: logo, header, kontaktni bar, mobile menu, footer, zakladni barvy, buttony, kontakty, copyright a zakladni typografie. Soucasne se vycisti zbytkove Baspa kontakty ve frontendu. Staticke prekladove zminky v `.po/.pot` se mohou docistit pozdeji, ale nesmi se zobrazovat na webu ani spoustet requesty.

Pak se upravi produktovy model kolem realneho Figma detailu `Timberwolf`. To je klicove, protoze Figma detail, konfigurace, barvy a produktove fakty jsou navrzene prave pro Timberwolf: Serie Classic, 3 osoby, 217 x 174 x 98 cm, 884 litru, Prestige 15/1 a Signature 30/2. Do produktu se doplni pole pro typ produktu, radu, prezentacni rezim, cenu/cenovy text, konfigurace, parametry, galerii, barvy, dokumenty, CTA a puvodni URL. `Lunar` zustava produkt v katalogu Core, ale nevede vizualni implementaci detailu.

Na `Timberwolf` se postavi prvni skutecny Figma vertical slice. Administrace, data, single produkt, produktova karta, listing, galerie, parametry, konfigurace, barvy, vyhody a poptavkove CTA musi fungovat dohromady a sedet na Figma frame. Dokud tento pruchod neni cisty, nehromadne se nemigruji dalsi produkty. Jakmile `Timberwolf` sedi, stejne pole a sablony se pouziji pro `Lunar`, `Orion` a `Husky`.

Nasledne se vlozi `Covana` jako kontrolni produkt mimo virivky. Bude v jednom `product` CPT, ale s jinym typem produktu a prezentacnim rezimem. Tim se overi, ze sirsi sortiment jako sauny, koupaci sudy, ochlazovaci bazenek, IKONO nabytek, Covana a prislusenstvi nemusi dostat vlastni CPT. Aktualni implementace je drzi jako `landing_section`, aby local frontend neodskakoval na externi e-shopy. Pokud bude mit polozka plny obsah, zustane jako detail; pokud ma byt jen proklik, pred produkci se prepne na `external_shop`.

Po overeni `Timberwolf` a `Covana` se dokonci katalog. Vzniknou listingy `/catalog/virivky/`, `/catalog/swimspa/` a `/catalog/dalsi-sortiment/`, karta produktu, taxonomicke nebo landing sablony pro rady Core/Classic/Custom/AWP, zakladni filtry a souvisejici CTA. Aktivni produkty se vezmou z local Arctic archivu, crawlu a briefu. Vyradene produkty se nevkladaji jako verejne aktivni produkty, ale jejich stare URL se zapisi do redirect mapy.

Homepage se postavi podle frame `HP`: hero, hlavni produktove smery, vyhradni prodejce, montaz/podpora/servis, showroom, prubeh zakazky, ukazky realizaci, kontaktni CTA a footer. Hero, kategorie a dalsi obrazove plochy se exportuji z Figma node ID. Stare Arctic obrazky se nesmi pouzit jako nahrada Figma vizualu.

Produktova kategorie se postavi podle frame `KATEGORIE`: text kategorie, listing, filtry, produktove karty, sekce vyhod a CTA. Detail produktu se postavi podle frame `DETAIL KONKRETNIHO PRODUKTU`: galerie/hero, konfigurace, parametry, barvy, vlastnosti, dokumenty, souvisejici produkty a poptavkovy blok.

Podpora se postavi jako editovatelny rozcestnik. `Ke stazeni` nebude staticky seznam v obsahu stranky, ale listing nad CPT `download`. Download polozky dostanou kategorii dokumentu: katalog, navod, rozmery, zaruky, stavebni pripravenost, uprava vody, servis. Stare PDF URL se bud importuji jako media a redirectuji na nove media/detail URL, nebo se zachyti redirectem na prislusnou download polozku.

Showroom a kontakt se postavi z noveho obrazoveho archivu. Pouziji se fotky prodejny z klientskych podkladu, ne stare fotky ze stareho webu. Kontakt bude obsahovat hlavni poptavku, servis, adresu, oteviraci dobu, telefon, e-mail a jasne CTA. Mapa se resi jako link nebo embed podle finalni cookie/tracking strategie; do localu se nesmi pridavat nic, co nekontrolovane vola externi sluzby.

Reference se prevedou do existujici Baspa logiky referenci. Pokud stary web obsahuje jen volne citace a galerie, normalizuji se do `reference` polozek s titulkem, textem, fotkami a pripadne produktem. FAQ se prevede do editovatelne struktury, ne jako dlouha staticka HTML stranka.

Migracni tabulka se vyrobi z `docs/crawl-live/arctic-spas-live-crawl.csv`. Kazdy radek dostane starou URL, novou URL, typ obsahu, migracni akci, redirect, zdroj obsahu a poznamku. Z 75 `review_migrate_page` URL se odstrani duplicity a temata se slouci do realnych stránek, aby se nemigrovaly tri varianty stejne landing page. Cilem je zachovat SEO hodnotu, ne kopirovat historickou strukturu 1:1.

Importery budou v `tools/`. Prakticky vzniknou skripty pro produkty, stranky, downloady, media a redirect mapu. Importer bude umet vytvorit nebo aktualizovat WP obsah podle puvodni URL, aby sel pustit opakovane. U produktu nastavi taxonomie, meta pole, galerii, featured image a puvodni URL. U stranek vycisti stare HTML, ponecha obsah a vlozi ho do nove sablonove struktury. U medii vynecha balast a importuje jen whitelist.

Media workflow bude Figma-first. Vsechny vizualni assety, ktere existuji ve Figme, se exportuji z Figma node ID a ulozi do `assets-source/figma/export/` plus do importni slozky WordPressu. Stary Arctic archiv se pouzije pro produktove fotky nebo dokumentacni assety jen tehdy, kdyz Figma konkretni asset nema. Kazda takova vyjimka se zapise do manifestu.

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
- interní odkazy,
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
