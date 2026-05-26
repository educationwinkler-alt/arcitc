# Arctic Spas: plan dokonceni webu do produkcni kvality

Datum: 2026-05-25  
Navazuje na: `docs/end-to-end-implementation-plan.md` a `../arctic-spas-wordpress-plan.md`  
Lokalni web: `http://localhost:8090`  
Primarni cil: web musi vypadat podle Figma wireframe + Figma grafiky, ne jako roztazena Baspa varianta. Responzivni chovani desktop/tablet/mobile musi byt jednotne napric celym webem, ne opravene jen na homepage. Arctic Spas ma mit vlastni CSS/layout system podle Figmy; Baspa je jen technicky WordPress zaklad tam, kde neprekazi.

Dulezita aktualizace (2026-05-25):
- Pro layout fidelity a scaling rozhoduje kanonicky dokument `docs/arctic-scaling-rebuild-plan.md`.
- Pokud je konflikt mezi timto dokumentem a kanonickym planem, plati `docs/arctic-scaling-rebuild-plan.md`.

## Verdikt k puvodnimu planu

Puvodni plan je hotovy jako technicky zaklad a implementacni baseline:

- existuje oddeleny workspace `arctic-spas-2/`,
- existuje fork Baspa theme,
- bezi lokalni WordPress,
- existuji CPT pro produkty, downloady, FAQ a reference,
- existuji importy obsahu, redirecty, lokalni safety guard a QA skripty,
- Figma soubory jsou napojene jako zdroj UX/designu.

Puvodni plan ale neni hotovy jako produkcni web. Definice hotovo neni splnena, protoze hlavni vizualni schvalovaci plochy napric webem porad rozmerove nesedi proti Figma grafice. Problem neni jen homepage hero/header/footer; stejny princip se tyka katalogu, detailu produktu, podpory, kontaktu, showroomu, referenci a informacnich stranek.

Tento dokument je aktualni plan dokonceni. Ma prednost pred starsimi formulacemi, ktere tvrdi, ze homepage/header/footer uz sedi.

## Zdrojova pravidla

1. Figma wireframe urcuje architekturu stranek a poradi sekci.
2. Figma grafika urcuje rozmery, cropy, komponenty, logo, typografii, barvy, header, hero, footer, mobil a interakce.
3. Baspa je funkcni WordPress zaklad, ne vizualni fallback a ne rozmerovy vzor.
4. Baspa CSS nesmi byt nad Arctic designem. Pravidla z Baspa `style.css`, ktera ovlivnuji verejny layout, header, hero, heading, container, CTA, karty, footer nebo breakpointy, se maji odstranit nebo prepsat u zdroje. Neni spravne je donekonecna prebijet dalsimi selektory.
5. Arctic CSS ma byt finalni designovy system podle Figmy, ne vrstva nouzovych override hacku. `!important`, specificity zavody a globalni opravy jen pro jednu URL se povazuji za technicky dluh, ktery P0 nesplnuje.
6. Nepouziva se primarne `@layer baspa` jako zpusob reseni. Je to fork bez upstream zavazku; jednodussi a spravnejsi je Baspa layout pravidla primo upravit, odstranit nebo nahradit Arctic pravidly.
7. Stary Arctic web a `assets-source/owner-info/` jsou zdroj obsahu, produktovych fotek, swimspa fotek, referenci, realizaci, PDF a textu.
8. Localhost nesmi kontaktovat `baspa.cz`, zivy Arctic web, Ecomail, tracking, Smartsupp ani mapove/sluzbove endpointy.
9. Desktop a tablet responzivita se nesmi resit jako homepage-only hack. Figma definuje globalni system: header, footer, hlavni container, hero/heading frame, CTA bloky a sekcni rytmus se musi na beznych Windows/Chrome viewportech chovat stejne na vsech sablonach.
10. Pokud ve Figme existuje slozena sekce nebo komponenta, napr. header, footer, hero, CTA, fixovany banner, mapa nebo kolaz, musi se rozlisit vizualni vrstvy a semanticky obsah. Dekorativni/obrazove vrstvy se exportuji z Figma node/frame 1:1; navigace, texty, kontakty, tlacitka a odkazy zustavaji realne HTML s CSS hodnotami z Figmy. Nesmime grafiku kreslit od oka, ale nesmime ani schovat obsah do mrtveho PNG.
11. Souradnice z Figma API jsou pracovni podklad, ne slepa pravda. Pred kodovanim konkretni hodnoty se musi overit primo proti otevrene Figme/Dev Mode nebo proti ulozenemu Figma screenshotu vedle browser renderu.

## P0: opravit globalni Figma fidelity a responzivitu

Tohle je blokujici pro klientsky nahled.

### CSS architektura: Arctic-first, Baspa jen kde dava smysl

Aktualni problem:

- `dist/css/style.css` obsahuje velky Baspa layout a nacita se pred `dist/css/arctic.css`,
- `arctic.css` se tim zmenil na sadu override pravidel misto cisteho Arctic design systemu,
- specificity boj vede k tomu, ze header, hero, container a dalsi komponenty porad obcas prevezmou Baspa rozmery,
- dlouhe smoke testy pak jen overuji vysledek spatne architektury a zpomaluji iteraci,
- web podle Figmy neni konceptualne slozity; komplikace vznika hlavne z ponechaneho Baspa CSS sumu.

Implementacni rozhodnuti:

- Baspa se nemusi vizualne udrzovat. Je to fork, ne upstream knihovna.
- Mapovani CSS zdroju je hotove: pro hlavni Baspa verejny layout v tomto forku neni dohledatelny hlavni LESS entrypoint ani build config.
- `src/less/arctic.less` a jeho importy jsou Arctic vstup, ne zdroj Baspa layoutu. `modules/*/less/` resi modulove styly a neni primarni misto pro header/hero/container/footer cleanup.
- Pro P0 se `dist/css/style.css` bere jako editovatelny zdroj zdedeneho Baspa layoutu. Zasahy do nej jsou povolene, ale musi byt cilene na konkretni layout pravidla a nesmi se menit nesouvisejici funkcni/modulove casti.
- Z Baspa se ponecha PHP/WP logika, CPT infrastruktura, formulare, bezpecnostni guardy, admin helpers a drobne utility, pokud neprekazi Figme.
- Baspa layout pravidla pro header, hero/heading, container, footer, CTA, karty, menu a breakpointy se nemaji vrstvove prebijet; maji byt odstranena, zneutralizovana nebo nahrazena Arctic pravidly.
- Arctic vlastni tyto verejne designove komponenty: page shell, 1400px container system, top bar, header, dropdowny, mobile menu, hero/heading, category/product cards, CTA, footer, typograficky rytmus a responzivni breakpointy.
- `arctic.css` zustava misto pro finalni Arctic design system, ale po cleanupu nesmi obsahovat nouzove specificity valky proti Baspa. Stare `!important` override se maji postupne odstranit.
- `@layer baspa` neni primarni reseni. Pouzit by se mel jen jako docasna nouzova izolace, pokud by prime vycisteni Baspa CSS blokovalo postup, ne jako cilova architektura.

Pozadovany stav:

- po nacteni webu neexistuje viditelny Baspa layout fallback,
- header/hero/footer/container se daji ladit podle Figmy bez prebijeni Baspa pravidel,
- CSS je citelne rozdelene na technicky zaklad a Arctic design, ne na nekonecny seznam oprav,
- dalsi Figma prace probiha rychleji, protoze se neopravuji stejne Baspa regresni vlivy porad dokola.

### Globalni desktop/tablet responzivita

Aktualni problem:

- web se na realnem Chrome viewportu nechova napric sablonami jako Figma,
- layout je stale ovlivnen Baspa CSS sumem a nektere komponenty se proto musi opravovat neciste pres override,
- cast oprav byla smerovana na homepage, ale `/swimspa/` a dalsi sablony ukazuji stejne roztazene Baspa chovani,
- header, hero, container a footer nemaji jeden spolecny Figma sizing system,
- nektere hodnoty v dokumentaci jsou jen API souradnice a musi se vizualne validovat ve Figme pred tim, nez se podle nich upravi CSS,
- audit muze projit na 1920 px, ale realny browser s chrome UI/scrollbarem nebo sirsi/uzsi desktop porad vizual rozbije.

Pozadovany stav:

- pro desktop Figma frame je zaklad `1920 px`; vsechny desktop sablony se meri proti svemu Figma frame,
- pri realnych Chrome viewports `1920/1903`, `1536`, `1440`, `1366` a tablet sirce kolem `1024` se layout musi zmensovat nebo prelamovat stejnym systemem napric webem,
- header komponenta, navigace, dropdowny, hero/heading, hlavni 1400px container, CTA a footer maji sdilenou Arctic implementaci bez Baspa layout fallbacku,
- zadna sablona nesmi mit vlastni nahodny breakpoint, ktery opticky zmeni rozvrzeni oproti Figme,
- homepage-only scaling nebo homepage-only oprava se nepovazuje za splneni P0,
- kazda konkretni souradnice z API se pred pouzitim porovna s Dev Mode / Figma screenshotem,
- mobilni zobrazeni se ridi mobilnimi Figma framy a neni jen zmenseny desktop.

### Homepage hero

Aktualni problem:

- hero se chova moc jako Baspa full-width/full-height sekce,
- fotka je roztazena nebo orezana jinak nez ve Figme,
- na realnem Chrome viewportu se kompozice nerozmeruje jako Figma frame,
- leva sipka zasahuje do nadpisu,
- header ma jinou sirku a optickou pozici nez ve Figme,
- vyprodejovy banner neni spolehlive ukotveny jako ve Figma frame.

Pozadovany stav:

- desktop hero se meri proti Figma frame `HP`,
- zakladni frame je `1920 x 795`,
- header sedi na Figma komponentu `x=260 / y=18 / w=1400 / h=105`,
- hero obraz je z Figmy a jeho crop/scale odpovida navrhu,
- textovy blok nesmi byt v kolizi se sipkou,
- sipky maji byt na Figma pozicich a mimo obsahovy text,
- slider tecky a promo banner sedi relativne k Figma frame,
- pri beznem Chrome viewportu na Windows nesmi vzniknout posun, ktery rozbije kompozici.

### Header a dropdowny

Pozadovany stav:

- hlavni header je Figma komponenta, ne jen Baspa header s Arctic barvami,
- stejny header sizing a opticka pozice plati pro homepage i vsechny vnitrni sablony,
- dropdown `Dalsi informace` je menu podle wireframu, ne samostatna dlazdicova podstranka,
- dropdowny maji sedet vizualne na Figma grafiku,
- zadny header prvek nesmi byt useknuty nebo opticky mimo osu.

### Footer

Aktualni problem:

- footer se porad chova moc jako Baspa roztazene pozadi,
- vyska, crop pozadi, rozestupy a kontaktni karta nesedi dostatecne proti Figme,
- soucasny footer je skladany v `templates/footer.php` z HTML/CSS, `footer-background.jpg`, `footer-map.png`, textu a kontaktni karty, ale hodnoty, cropy a rozlozeni nejsou dostatecne odvozene z Figma komponenty.

Pozadovany stav:

- desktop footer se meri proti cele Figma komponente `footer` (`1:208`, `1920 x 773`) nebo konkretni instanci stejne komponenty v danem frame,
- mobile footer se meri proti mobilni Figma footer sekci `1:2168` (`375 x 1396`), pokud se pouziva mobilni layout,
- krajina, gradient, mapa/showroom, loga a ciste obrazove vrstvy se exportuji z Figmy jako assety,
- navigacni sloupce, kontakt, telefon, e-mail, copyright, privacy link, tlacitka a dalsi texty musi zustat realne HTML texty/odkazy kvuli SEO, pristupnosti a editovatelnosti,
- font sizes, line-height, barvy, stiny, radiusy, rozestupy, sirky a pozice kontaktni karty se berou z Figmy/Dev Mode nebo se overi screenshot diffem,
- `footer-background.jpg` a `footer-map.png` jsou jen dilci vizualni assety; samy o sobe nesplnuji 1:1 Figma footer,
- footer musi sedet na homepage i na vsech vnitrnich sablonach, kde ma podle Figmy stejnou komponentu,
- zivy footer nesmi byt jedna bitmapa cele sekce; bitmapa celeho footeru smi slouzit jen jako QA/reference screenshot.

## P0: rychla Figma QA, ktera nelze obelhat

Aktualni problem: automaticky audit muze projit, i kdyz vizualne neni klientsky prijatelny. Druhy problem je opacny: prilis dlouhe smoke testy zpomaluji vyvoj a svadi k testovani spatne vrstvene CSS architektury misto opravy priciny.

Rozhodnuti pro P0:

- dlouhe `qa:local` se nepousti po kazde male vizualni zmene,
- behem CSS/Figma prace se pouziva kratky P0 loop: CSS build, PHP lint upravenych sablon, jeden az tri cilene Playwright screenshoty a rychle mereni konkretniho frame,
- full smoke/QA se pousti az po dokonceni architektonickeho CSS cleanupu a ucelene Figma sekce,
- screenshot proti Figme je dulezitejsi nez dlouhy pruchod, ktery jen rekne, ze stranka nema overflow,
- testy nesmi zapisovat do dokumentace tvrzeni "sedi", dokud to neni potvrzene vizualne vedle Figmy.

Upravit audit tak, aby:

- explicitne failoval pri prekryvu sipky a hero textu,
- meril realne pozice DOM prvku v Chrome, nejen existenci trid/assetu,
- hlidal skutecnou vysku a sirku hero/heading frame na kazde hlavni sablone,
- meril globalni header, footer a hlavni container napric homepage, kategoriemi, detaily, podporou, kontaktem, showroomem, referencemi a informacnimi strankami,
- poustel desktop mereni minimalne na `1920`, realnem Chrome `1903`, `1536`, `1440`, `1366` a tablet `1024`,
- porovnaval screenshoty proti ulozenym Figma screenshotum aspon pres bounding-box a crop pravidla,
- explicitne overoval, ze obrazove vrstvy footeru/headeru a dalsich slozenych Figma sekci pochazeji z Figmy a semanticky obsah zustava realne HTML,
- failoval, pokud viditelny footer text existuje jen v obrazku nebo pokud se footer sklada z dilcich assetu bez presnych Figma rozmeru, stylu a pozic,
- ukladane screenshoty mely jasne nazvy pro desktop/mobile a datum,
- audit nezapisoval do dokumentace tvrzeni "sedi", pokud nebylo rucne nebo automaticky prokazane.

## P1: product_configurations jako skutecny datovy model

Aktualni problem:

`product_configurations` je moc slabe textove pole. Pro Prestige/Signature/Legend chybi plne editovatelna struktura.

Udelat:

- nahradit/rozsirit `fieldset_text` na strukturovany Meta Box group/repeater,
- pridat `active`,
- pridat `sort_order`,
- pridat `name`,
- pridat `price` nebo `price_text`,
- pridat parametry konfigurace,
- pridat popis,
- pridat obrazek konfigurace,
- upravit seed, aby zachoval stavajici data,
- upravit single product sablonu, aby cetla novou strukturu,
- doplnit fallback/migraci pro stare ulozene hodnoty.

## P1: rucni Figma QA frame po framu

Poradi kontroly:

1. Homepage desktop: header, hero, dve kategorie, prodejce, benefity, showroom, prubeh, reference, CTA, footer.
2. Homepage mobile: header, menu, hero, promo, kategorie, showroom, reference, footer.
3. Header dropdowny: Virivky, Celorocni bazeny, Vlastnosti, Dalsi informace.
4. Kategorie `Virivky`.
5. Kategorie `Celorocni bazeny`.
6. Kategorie `Dalsi sortiment`.
7. Detail produktu Timberwolf.
8. Detail aktivni swimspa.
9. Detail sirsiho sortimentu, napr. Covana.
10. Podpora a Ke stazeni.
11. Kontakt a Showroom.
12. Reference.
13. O nas, Servis, Sluzby, Certifikaty, Zaruka, Kolik stoji udrzba, detail vlastnosti.

Kazda kontrola musi odpovedet:

- sedi struktura podle wireframu,
- sedi vizual podle graficke Figmy,
- sedi responzivni chovani na desktop/tablet/mobile podle stejneho systemu napric webem,
- pouzivaji se spravne designove assety z Figmy,
- obrazove vrstvy slozenych Figma sekci se berou z Figma exportu a textove/klikatelne vrstvy zustavaji realne HTML podle Figma hodnot,
- obsahove fotky jsou ze stareho Arctic webu nebo owner podkladu,
- nejsou videt Baspa texty ani Baspa vizualni fallback,
- neni horizontalni overflow,
- mobil neni jen zmenseny desktop.

## P1: obsahovy audit proti staremu Arctic webu

Udelat:

- znovu porovnat seznam aktivnich produktu ve WP proti staremu Arctic obsahu a klientskym poznamkam,
- potvrdit, ze Dreammaker a stare Core modely jsou jen redirect, ne aktivni produkty,
- overit vsechny swimspa produkty a jejich parametry,
- overit sirsi sortiment: Covana, sauna, koupaci sudy, ochlazovaci bazenek, IKONO, prislusenstvi,
- overit 26 downloadu proti starym PDF a migracni mape,
- projit reference a realizace: texty + obrazky musi byt obsahove zdroje, ne nahodne Figma placeholdery,
- zapsat chybejici obsah do migracniho TODO seznamu.

## P2: prevest hardcoded informacni stranky do editovatelnych dat

Tohle je dulezite pred produkci, ale neni dulezitejsi nez Figma fidelity.

Kandidati:

- `template-services.php`,
- `template-certificates.php`,
- `template-about.php`,
- `template-showroom.php`,
- `template-maintenance.php`,
- `template-warranty.php`,
- `template-feature-detail.php`,
- fallback casti v `template-support.php`.

Moznosti:

- Meta Box page fields pro konkretni sablony,
- vlastni CPT pro opakovatelne sekce,
- Gutenberg patterny jen tam, kde nebude potreba slozita struktura.

## P2: produkcni priprava

Udelat:

- asset smoke pro vsechny `uploads/import/figma/*` odkazy,
- potvrdit, ze produkcni deploy obsahuje Figma assety i importovane media,
- zkontrolovat canonical, title, meta description, OG obrazky,
- zkontrolovat 301 redirecty ze starych `.php` URL,
- zkontrolovat sitemap a robots,
- zkontrolovat formularove prijemce mimo local,
- zapnout externi integrace jen vedome a s finalnimi klici,
- projit privacy/cookies texty,
- udelat prelaunch crawl.

## Definice hotovo

Web lze povazovat za hotovy az kdyz:

- Baspa CSS uz neni vizualni autorita webu; verejny layout podle Figmy vlastni Arctic CSS/sablony,
- Baspa layout pravidla, ktera rozbijela header, hero, container, CTA, karty nebo footer, jsou odstranena/prepsana u zdroje, ne jen prebijena dalsimi override selektory,
- `arctic.css` neobsahuje nouzovy specificity boj jako hlavni zpusob implementace,
- desktop/tablet responzivita sedi proti Figme napric webem, ne jen na homepage,
- homepage hero/header/footer sedi proti Figma grafice vizualne, ne jen podle smoke testu,
- footer je implementovany jako realne HTML texty/odkazy/kontakt nad Figma obrazovymi vrstvami a meri se proti kompozici `1:208`/odpovidajicim instancim a mobilnimu footeru `1:2168`,
- header/hero/footer souradnice jsou potvrzene vizualne v otevrene Figme/Dev Mode nebo ulozenym Figma screenshotem vedle browser renderu,
- zadna sipka, CTA ani promo karta neprekryva text proti navrhu,
- vsechny hlavni Figma frame maji desktop i mobile QA,
- produktove a referencni fotky jsou ze stareho Arctic webu nebo owner podkladu,
- produkty maji spravny editovatelny model vcetne konfiguraci,
- zakladni obsah neni zbytecne natvrdo tam, kde ho ma klient menit,
- local safety porad blokuje externi requesty,
- po ucelenem P0 passu projde zkracena Figma QA a pred predanim take `npm run qa:local`,
- rucni vizualni review projde nad screenshoty vedle Figmy,
- klient muze schvalit prvni dojem bez vysvetlovani, ze "se to jeste doladi".
