# Arctic Spas 2

Oddeleny pracovni prostor pro novou WordPress realizaci webu Arctic Spas.

Referencni slozky mimo tento adresar se nemaji upravovat:

- `../Arctic-spas/` - puvodni Arctic web, archiv obsahu a assetu
- `../baspa.cz/` - technicka reference WordPressu a zdroj pro fork tematu
- `../drive-download-20260523T082002Z-3-001.zip` - doplnkove podklady od klienta

Veskere nove soubory, upravy sablony, importery, dokumentace a staging konfigurace patri sem.

## Navrzena struktura

- `wp-content/themes/arctic/` - nove WordPress tema
- `tools/` - crawlery, importery a pomocne skripty
- `docs/` - migracni mapy, exporty z Figmy, SEO/redirect podklady
- `assets-source/` - vybrane zdrojove assety pred importem do WordPressu

Aktualni rozbalene zdroje:

- `assets-source/figma/` - kopie `.fig` souboru
- `assets-source/owner-info/` - rozbaleny archiv s marketingovym briefem, PDF a fotkami
- `wp-content/themes/arctic/src/less/` - Arctic CSS build vrstva pro Figma tokeny a rebrand

Aktualni stav:

- `wp-content/themes/arctic/` - zalozeny fork Baspa theme
- `docs/theme-fork-status.md` - stav a pravidla prvniho forku
- `docs/end-to-end-implementation-plan.md` - souvisly implementacni plan od lokalniho prostredi po launch
- `docs/figma-tokens.md` - aktualni mapovani Figma/Arctic tokenu do CSS vrstvy
- `wp-content/mu-plugins/arctic-redirects.php` - prvni redirect vrstva pro stare `.php` URL
- `wp-content/themes/arctic/tools/seed-pilot-content.php` - opakovatelny seed obsahu, produktu, menu a nastaveni

## Lokalni WordPress

Docker Compose stack:

- WordPress: `http://localhost:8090`
- Adminer: `http://localhost:8091`
- DB host v Admineru: `db`
- DB/user/password: `wordpress` / `wordpress` / `wordpress`

Pomocne prikazy:

- `powershell -ExecutionPolicy Bypass -File tools/wp-local.ps1 start`
- `powershell -ExecutionPolicy Bypass -File tools/wp-local.ps1 install`
- `powershell -ExecutionPolicy Bypass -File tools/wp-local.ps1 status`
- `powershell -ExecutionPolicy Bypass -File tools/wp-local.ps1 stop`

Lokalni bezpecnost:

- `wp-content/mu-plugins/arctic-local-safety.php` blokuje odchozi WordPress HTTP requesty mimo localhost.
- V local rezimu se neodesilaji e-maily.
- Ecomail integrace se v local rezimu nevola.
- Ecomail ve theme pouziva WordPress HTTP API a nevypisuje debug odpovedi do frontendu.
- Formularovy AJAX handler nepousti cestu sablony z POSTu; pouziva server-side whitelist.
- AJAX vyhledavani sanitizuje keyword, vyzaduje nonce, ma lehky rate limit a povoluje jen registrovane WordPress post typy/taxonomie.
- SVG upload neni globalne povoleny; zapnout jde jen vedome pro admina.
- reCAPTCHA badge se defaultne neschovava natvrdo.
- V local rezimu se nenacitaji Google/Fontshare fonty, tracking preconnecty, Smartsupp ani Google mapa.
- WP-CLI i webovy kontejner jsou nastavene na `WP_ENVIRONMENT_TYPE=local`.

Kontrola local safety:

- `npm run local:safety`

Admin:

- URL: `http://localhost:8090/wp-admin/`
- Lokalni ucet: `admin` / `admin`

Frontend je seednuty minimalnim, ale pruchodnym obsahem:

- homepage se slide/hero obsahem podle Figma HP passu,
- presny Figma pass pro homepage header, prvni viewport, vyprodejovy banner z Figma assetu a dve hlavni kategorie,
- desktop hero pouziva primo Figma `1920 x 795` background asset bez WordPress orezoveho media cropu,
- mobilni HP top podle Figma `GM - HP`, vcetne specialniho mobile hero cropu a vyprodejoveho banneru,
- mobilni HP showroom a realizace podle Figma `GM - HP`, vcetne Figma kolaze, badge `280 m2`, referencni karty a CTA pod carousel,
- mobilni menu podle Figma `GM - HP menu`, vcetne presne pozice loga, close buttonu, tmaveho panelu, vyhledavani a kontaktu,
- desktop Figma pass pro katalog virivek podle frame `KATEGORIE` az po footer: hero, intro, series nav, produktove karty, konfigurator, showroom, prubeh, realizace, CTA a footer,
- Figma visual pass pro detail produktu Timberwolf podle frame `DETAIL KONKRETNIHO PRODUKTU`: hero, navigace, konfigurace, konfigurator banner, barvy, vyhody, volitelna vybava, realizace a kontaktni CTA sedi na Figma souradnice,
- Figma/Baspa visual pass pro `Podpora` a `Kontakt` vcetne lokalni mapove nahrady bez externiho embedu,
- vlastni Arctic footer podle Figma logiky s rychlym kontaktem a bez puvodniho Baspa block-template vystupu,
- kategorie `Vířivky`, `Celoroční bazény`, `Další sortiment`,
- 28 publikovanych produktu celkem,
- 22 aktivnich produktu z crawlu a `Covana` jako kontrolni sirsi sortiment,
- detailnejsi piloty `Lunar`, `Orion`, `Husky`, `Covana`,
- sirsi sortiment `Luxusni sauny`, `Koupaci sudy Kirami`, `Prislusenstvi a doplnky`, `IKONO nabytek`, `Ochlazovaci bazenek`,
- 26 dostupnych PDF dokumentu z crawlu ve `download` CPT,
- 3 reference z Figma realizaci pro sekci `Ukazky realizaci`,
- `Podpora`, `Ke stazeni`, `Showroom`, `Kontakt`,
- menu v hlavni navigaci podle Figma grafiky, horni liste a paticce.

## CSS build

Instalace zavislosti:

- `npm install`

Build Arctic CSS:

- `npm run css:build`

Watch rezim:

- `npm run css:watch`

Figma audit podle hlavních Figma passů:

- `npm run figma:audit`

Kontroluje homepage/header/mobile top, katalog vířivek, detail Timberwolf a kontakt proti Figma souřadnicím a Figma assetům.

Vizualni smoke test hlavnich cest:

- `npm run visual:smoke`

Smoke test hlídá hlavní URL, externí browser requesty, horizontální overflow, veřejné placeholdery a zakázané živé integrace.

Smoke test zahrnuje homepage, katalog virivek, swimspa, dalsi sortiment, produktove detaily, showroom, podporu, downloady a kontakt. Soucasne kontroluje zakazane externi requesty v prohlizeci, horizontalni overflow na desktopu i mobilu a uklada Playwright screenshoty hlavnich Figma stranek.

Formularovy smoke test:

- `npm run form:smoke`

Test odesle lokalne kontaktni a servisni formular pres WordPress `admin-ajax.php`, overi uspech, ulozeni do `contact` CPT, metadata AJAX/local reCAPTCHA a po sobe testovaci zaznamy smaze.

Redirect smoke test:

- `npm run redirect:smoke`

Test projde `docs/migration-map.csv`, overi 301 ze starych Arctic URL na lokalni nove URL/media soubory a hlida, ze zadny redirect nemiri na `baspa.cz` ani zive `arctic-spas.cz`.

Vyhledavaci smoke test:

- `npm run search:smoke`

Test odesle AJAX vyhledavani na lokalni `admin-ajax.php`, overi nalezeni produktu Timberwolf, ignorovani nepovoleneho post typu a odmitnuti neplatneho nonce.
