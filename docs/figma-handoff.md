# Figma handoff pro Arctic Spas

Chrome je prihlaseny do Figmy, ale realizace bude nejrychlejsi, pokud z Figmy vznikne konkretni export pro vyvoj.

## Aktualni stav

Figma soubory jsou importovane, odkazy jsou ulozene a oba soubory jsou citelne pres Figma API.

Z API uz mame:

- nazvy souboru,
- file keys,
- top-level frame,
- rozmery hlavnich frame,
- zakladni mapu sablon.

Vygenerovane soubory:

- `arctic-spas-2/docs/figma-api-summary.json`
- `arctic-spas-2/docs/figma-top-level-nodes.json`
- `arctic-spas-2/docs/figma-structure.md`

## Hlavni interpretace

Baspa je hlavni programatorsky a rozmerovy vzor. Figma neni navrh uplne jineho webu od nuly, ale presny handoff toho, jak ma Arctic Spas vypadat jako rebrand / specializovana produktova cast nad Baspa architekturou.

Z toho plyne:

- architektura, sablony a WP moduly se maji drzet Baspa tematu,
- rozmerovy system ma zustat co nejblize Baspa,
- wireframe urcuje presnou strukturu Arctic stranek,
- finalni grafika urcuje vizualni skin: logo, barvy, media, typografie, stavy a CSS,
- puvodni `arctic-spas.cz` slouzi pro obsah, ne jako technicky frontendovy vzor.

Navstevnik by mel mit pocit, ze Arctic Spas patri k Baspa ekosystemu a prechod mezi weby je temer neviditelny, jen s vlastnim Arctic brandingem.

## Potrebne vstupy

Lokalni `.fig` soubory jsou pripraveny zde:

- `arctic-spas-2/assets-source/figma/Arctic-spas.cz wireframe.fig`
- `arctic-spas-2/assets-source/figma/Arctic-spas.cz grafika.fig`

Stav importu:

- `Arctic-spas.cz wireframe` - importovano do Figmy 2026-05-23
- `Arctic-spas.cz grafika` - importovano do Figmy 2026-05-23

Figma odkazy:

- Wireframe: https://www.figma.com/design/puPBNFpuaXpRZR2TINaDvm/Arctic-spas.cz-wireframe?t=BdCp3f8qo4vMl5Ft-1
- Finalni grafika: https://www.figma.com/design/xeOew3dFjDVfjXZrJ09emM/Arctic-spas.cz-grafika?t=BdCp3f8qo4vMl5Ft-1

Figma file keys:

- Wireframe: `puPBNFpuaXpRZR2TINaDvm`
- Finalni grafika: `xeOew3dFjDVfjXZrJ09emM`

## Import do Figmy

Import `.fig` souboru se dela ve Figma file browseru, ne uvnitr otevreneho design editoru.

Postup:

1. Otevrit Figma file browser v prihlasenem Chrome.
2. Kliknout na `Create new` / `Import`.
3. Zvolit `Import from computer`.
4. Vybrat oba `.fig` soubory z `arctic-spas-2/assets-source/figma/`.
5. Po dokonceni importu prejmenovat soubory napriklad:
   - `Arctic Spas - Wireframe`
   - `Arctic Spas - Final Design`
6. Zkopirovat share odkazy do tohoto dokumentu.

## Ziskani odkazu

Pro kazdy importovany soubor:

1. Otevrit kartu souboru ve Figme.
2. Kliknout na `Share`.
3. Pokud to jde, nastavit `Anyone with the link can view`.
4. Kliknout `Copy link`.
5. Vlozit odkaz sem nebo do samostatneho souboru `arctic-spas-2/docs/figma-links.md`.

Pokud sdileni "Anyone with link" neni mozne, dalsi varianta je Figma API token ulozeny lokalne mimo chat, napriklad do `.env.local`. Token se nema vkladat do konverzace.

### 1. Odkazy na Figma soubory

- wireframe soubor,
- finalni graficky soubor.

U kazdeho idealne dodat:

- URL souboru,
- informaci, ktera page/frame je aktualni,
- jestli existuji komponenty/design system.

## 2. Seznam sablon / frame

Hlavni frame uz jsou pres API viditelne. Pracovni mapa sablon:

- `HP` -> homepage,
- `KATEGORIE` -> produktova kategorie / listing,
- `DETAIL KONKRETNIHO PRODUKTU` -> pilotni produktovy detail,
- `PODPORA` -> support/download,
- `VLASTNOSTI` -> vlastnosti / proc Arctic,
- `VLASTNOSTI DETAIL` -> detail vlastnosti,
- `SHOWROOM` -> showroom,
- `KONTAKT` -> kontakt,
- `REFERENCE` -> reference/fotogalerie,
- `SLUZBY` -> sluzby,
- `SERVIS` -> servis,
- `O NAS` -> o nas,
- `popup` -> modal / formular,
- `header` -> desktop/mobile navigace a hlavicka,
- `GM - HP` -> mobilni homepage,
- `GM - HP menu` -> mobilni menu.

Tyto sablony se maji mapovat na existujici Baspa template logiku, ne stavet izolovane od nuly.

## 3. Design tokens

Z Figmy / Dev Mode vytahnout:

- font family,
- font weights,
- velikosti H1-H6,
- body text,
- line-height,
- hlavni barvy,
- sekundarni barvy,
- barvy buttonu,
- radiusy,
- stiny,
- spacing scale,
- max sirky containeru,
- breakpointy.

## 4. Assety

Vyexportovat do `arctic-spas-2/assets-source/figma/`:

- logo,
- ikony,
- dekorativni SVG,
- fotky pouzite v navrhu,
- produktove obrazky, pokud nejsou lepsi ve starem webu,
- Open Graph / social preview obrazky, pokud jsou navrzene.

Doporuceny format:

- SVG pro logo a ikony,
- WEBP nebo JPG pro fotografie,
- PNG pouze pokud je potreba pruhlednost.

## 5. Kriticke rozmery

U kazde hlavni sablony zaznamenat:

- desktop sirku frame,
- mobilni sirku frame,
- max sirku obsahu,
- vysku headeru,
- vysku hero sekce,
- rozmery produktove karty,
- rozmery hlavni produktove galerie.

## 6. Kontrola pred kodovanim

Nez zacne plny frontend:

- potvrdit, ze grafika odpovida wireframu,
- potvrdit, ze wireframe odpovida Baspa rozmerum a programatorske architekture,
- potvrdit, ktere Baspa sablony se jen preskinuji a ktere vyzaduji skutecnou upravu markup/logic,
- potvrdit finalni navigaci,
- potvrdit vzhled produktoveho detailu na pilotnim produktu,
- potvrdit mobilni verzi headeru a produktu,
- potvrdit CTA a formularove stavy.

## API vystupy

Token byl otestovan pres Figma API a oba soubory jsou citelne.

Vygenerovane soubory:

- `arctic-spas-2/docs/figma-api-summary.json`
- `arctic-spas-2/docs/figma-top-level-nodes.json`
- `arctic-spas-2/docs/figma-structure.md`

## Doporuceny postup z Figmy

1. Vzit Baspa sablonu jako vychozi programatorsky stav.
2. Porovnat ji s Figma frame `HP`, `KATEGORIE`, `DETAIL KONKRETNIHO PRODUKTU` a `header`.
3. Rozdelit rozdily na:
   - pouze CSS / skin,
   - vymena assetu,
   - textovy/obsahovy rozdil,
   - skutecna zmena struktury sablony.
4. Nechat Baspa markup a logiku vsude, kde Figma ukazuje stejnou architekturu.
5. Udelat pilotni produktovy detail podle frame `DETAIL KONKRETNIHO PRODUKTU`.
6. Teprve po potvrzeni pilotu preskinovat a rozvest zbytek sablon.

## Poznamka k pristupu

Pokud neni k dispozici primy Figma API token nebo browser automation napojena na prihlaseny Chrome profil, nelze spolehlive nacitat vrstvy primo z prihlaseneho Chromu. Bezpecna cesta je exportovat Dev Mode hodnoty, assety a frame odkazy z Figmy ruce nebo pres Figma API token urceny jen pro tento projekt.
