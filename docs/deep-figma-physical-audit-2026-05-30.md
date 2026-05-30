# Deep Figma physical audit - 2026-05-30

## Verdikt

Tenhle audit potvrzuje, ze po PR-G neni stav vizualne hotovy proti Figme. Nejvetsi problem neni jeden detail na jedne strance, ale to, ze nektere "component contracts" zavedly spatny vizualni zdroj jako globalni pravdu.

Nejdulezitejsi korekce: tmavy `f-footer--handoff` neni validni nahrazeni Figma footeru. Figma grafika ma footer komponentu s bilou/svetlou casti, rychlym kontaktem a krajinou/horskym pozadim. Aktualni local ji globalne prepisuje na tmavy navy blok. To je implementacni chyba, ne redesign scope.

## Jak bylo porovnano

- Local: `http://localhost:8090`, branch po PR-G (`main`).
- Figma: aktualni exporty z finalni grafiky `xeOew3dFjDVfjXZrJ09emM`.
- Desktop Figma exporty: `0.25x` scale, tedy 1920px frame je v PNG jako 480px siroky. Vysky v metrikach jsou proto prepoctene zpet na viewport.
- Mobile Figma exporty: `1x`.
- Local screenshoty jsou nove vygenerovane Playwrightem.
- Audit neni oprava kodu. Je to fyzicky porovnavaci snapshot pro dalsi repair krok.

## Artefakty

| Typ | Cesta |
| --- | --- |
| Audit script | `tools/deep-figma-physical-audit.js` |
| Local screenshoty | `docs/screenshots/deep-figma-physical-audit-2026-05-30/current/` |
| Aktualni Figma exporty | `docs/screenshots/deep-figma-physical-audit-2026-05-30/figma-current/` |
| Side-by-side HTML | `docs/screenshots/deep-figma-physical-audit-2026-05-30/physical-compare.html` |
| Metriky | `docs/screenshots/deep-figma-physical-audit-2026-05-30/metrics.json` |

## Pouzite Figma framy

| Local URL | Figma frame | Node |
| --- | --- | --- |
| `/` | `HP` | `1:14` |
| `/virivky/` | `KATEGORIE` | `1:262` |
| `/swimspa/` | `KATEGORIE` | `1:262` |
| `/product/timberwolf/` | `DETAIL KONKRETNIHO PRODUKTU` | `1:1461` |
| `/showroom/` | `SHOWROOM` | `1:442` |
| `/vlastnosti/` | `VLASTNOSTI` | `1:585` |
| `/vlastnosti/izolace-virivky/` | `VLASTNOSTI DETAIL` | `1:1302` |
| `/sluzby/` | `SLUZBY` | `1:658` |
| `/certifikaty/` | `CERTIFIKATY` | `1:694` |
| `/zaruka/` | `ZARUKA` | `1:719` |
| `/podpora/` | `PODPORA` | `1:752` |
| `/o-nas/` | `O NAS` | `1:945` |
| `/reference/` | `REFERENCE` | `1:1127` |
| `/kolik-stoji-udrzba/` | `KOLIK STOJI UDRZBA` | `1:1395` |
| `/servis/` | `SERVIS` | `1:1426` |
| `/kontakt/` | `KONTAKT` | `1:1037` |
| mobile `/` | `GM - HP` | `1:1973` |
| mobile menu | `GM - HP menu` | `1:2208` |

## Scaled height check

Pozor: vyska sama o sobe neni vizualni pass. Vetsina stranek ma velmi podobnou celkovou vysku, ale uvnitr jsou spatne assety, vrstvy, cropy nebo cele opakovane komponenty.

| Page | Local height | Figma scaled height | Delta | Poznamka |
| --- | ---: | ---: | ---: | --- |
| `/` | 5201 | 5204 | -3 | Vyska sedi, ale showroom, mobile promo/footer a footer jsou spatne. |
| `/virivky/` | 8583 | 8584 | -1 | Vyska sedi, ale produktove karty, showroom, configurator a footer nesedi. |
| `/swimspa/` | 6674 | 8584 | -1910 | Stranka je proti pouzitemu category frame kratsi; potrebuje samostatny scope/source decision. |
| `/product/timberwolf/` | 7460 | 7808 | -348 | Detail je kratsi a vizualne ma spatne hero/swatche/benefit media. |
| `/showroom/` | 4135 | 4136 | -1 | Vyska sedi, ale fotky/cropy/hero jsou jine. |
| `/vlastnosti/` | 2670 | 2668 | +2 | Blizke, ale footer globalne spatne. |
| `/vlastnosti/izolace-virivky/` | 5006 | 5004 | +2 | Blizke, ale hero image a textova hustota se lisi. |
| `/sluzby/` | 2736 | 2736 | 0 | Blizke, ale footer globalne spatne. |
| `/certifikaty/` | 2576 | 2576 | 0 | Layout blizky, loga/card styling nejsou 1:1, footer spatne. |
| `/zaruka/` | 2189 | 2332 | -143 | Chybi produktove obrazky nad kartami a note placement. |
| `/podpora/` | 5509 | 5204 | +305 | Obsah a form rhythm jsou vyssi nez Figma. |
| `/o-nas/` | 4584 | 4584 | 0 | Vyska sedi, tymove karty maji inicialy misto fotek. |
| `/reference/` | 2667 | 2668 | -1 | Archivni grid funguje, ale Figma obsah/cropy jsou jine. |
| `/kolik-stoji-udrzba/` | 3303 | 4324 | -1021 | Chybi dlouhy obsah nebo je vyrazne zkraceny proti Figme. |
| `/servis/` | 2808 | 2808 | 0 | Blizke, ale footer globalne spatne. |
| `/kontakt/` | 3198 | 3200 | -2 | Vyska sedi, mapa a kontaktni osoby jsou spatne. |
| mobile `/` | 6114 | 6046 | +68 | Chybi Figma promo blok, showroom/footer se lisi. |
| mobile menu | 900 | 774 | +126 | Local menu zobrazuje nav/CTA, Figma export ma jen dark overlay + search. |

## P0 nalezy

### P0-01 - Global footer je spatne prepsany na tmavy handoff

Local ma ve vsech auditovanych strankach tmavy navy footer. Figma footer ma svetlou cast, rychly kontakt a landscape/mountain background.

Evidence:

- Figma footer komponenta `1:208` ma `width 1920`, `height 773`.
- Figma footer obsahuje background node `1:210`, image `jacob-vizek-TPGbEjP8QQc-unsplash 1`, rozmery `1920 x 1209`.
- Lokalni asset existuje: `wp-content/uploads/import/figma/footer-background.jpg`.
- Puvidni LESS footer stale umi Figma background: `_components.less` ma `.f-footer--arctic` s `footer-background.jpg`.
- `templates/footer.php` ale pridava `f-footer--handoff`.
- `_component-contracts.less` nastavuje `.f-footer--arctic.f-footer--handoff { background: var(--arctic-contract-footer-bg); box-shadow: ... }`.
- `--arctic-contract-footer-bg` je `var(--arctic-color-menu)`, tedy tmavy navy.

Impact:

- Kazda stranka ztraci Figma footer.
- Mobile footer je take mimo Figmu.
- Tohle vysvetleni "redesign chce tmavou paticku" je chybne. Figma source a asset manifest rikaji opak.

Required repair:

- Predefinovat nebo odstranit `f-footer--handoff`, aby neresil footer nahrazenim backgroundu.
- Footer handoff ma resit prechod CTA/footer bez cyan mezery, ale musi zachovat Figma footer background.
- Guard musi kontrolovat pritomnost/viditelnost mountain backgroundu, ne jen tridu.

### P0-02 - Smoke/visual guardy legitimizuji spatny footer

`tools/component-contract-smoke.js` aktualne vyzaduje `f-footer--handoff` v HTML a `.f-footer--arctic.f-footer--handoff` v CSS. To je presne opacne, nez potrebujeme po fyzickem srovnani.

Impact:

- QA muze projit, i kdyz Figma footer je fyzicky spatne.
- Chybny design decision se zamkl jako "contract".

Required repair:

- Prepsat guard z "existuje handoff class" na "footer odpovida Figma kontraktu".
- Minimalni guard: computed background-image obsahuje `footer-background.jpg` nebo screenshot heuristic kontroluje landscape band.
- Zachovat guard proti cyan seam, ale ne za cenu odstraneni footer grafiky.

### P0-03 - Showroom collage/shared panel je stale fyzicky rozbity

Local na HP, kategoriich a swimspa ukazuje tmavy panel s prazdnymi/polopruhlednymi sedymi plochami. Figma ma realnou fotokolaz okolo panelu.

Evidence:

- `/` current: showroom section obsahuje prazdne/faint image bloky.
- `/virivky/` current: stejny problem.
- `/swimspa/` current: stejny problem.
- Figma `HP` a `KATEGORIE`: kolaz ma realne fotky showroomu a badge `280 m²`.
- `templates/section/showroom.php` pouziva owner showroom assety, ale fyzicky se nevykresluji ve Figma kompozici/cropech.

Impact:

- Opakovany shared blok vypada jako placeholder.
- Presne odpovida puvodni stiznosti: opravena jedna stranka neznamena opraveny shared prvek.

Required repair:

- Jedna canonical showroom component mapa: HP, category, swimspa, mobile.
- Ne page-by-page override.
- Pokud owner fotky zustavaji zdroj, musi byt orezane/vrstvene do Figma kolaze, ne jen vlozene "nekam".

### P0-04 - Product/category card media fyzicky nesedi

Na `/virivky/` a `/swimspa/` jsou nektere produktove karty vizualne skoro prazdne nebo maji media jinak nez Figma. Figma category frame ukazuje vsechny produktove karty s viditelnym plan/top-view obrazkem v kartach.

Evidence:

- `/virivky/` current: rada Custom ma bile karty, media prakticky neni videt.
- `/swimspa/` current: vice karet je blank/bez jasneho produktoveho media.
- Figma `KATEGORIE`: produktove karty maji jasne top-view produkty.

Impact:

- Katalog pusobi nedodelane.
- Pokud jsou media realne v HTML, stale jsou vizualne spatne: velikost, kontrast, crop, asset nebo pozadi.

Required repair:

- Udelat product card media audit podle konkretniho produktu: available image, source, expected crop.
- Kde source chybi, zapsat WAITING_ON_OWNER.
- Kde source existuje, upravit jednotny card media contract.

### P0-05 - Configurator/banner CTA je stale placeholder-like

Figma ma v category/detail kontextu cerveny banner s obrazovou/gradientovou kompozici. Local casto vypada jako jednoducha cervena plocha s kruhovou dekoraci nebo bez spravneho vizualu.

Evidence:

- `/virivky/` current: "Nakonfigurujte si vlastni virivku" je plain red-ish banner bez praveho Figma visualu.
- `/swimspa/` current: teal/blue banner je kratsi a vizualne mimo category Figma.
- Figma `KATEGORIE`: banner obsahuje pravou produkt/interier/laptop kompozici.

Required repair:

- Shared `configurator CTA` component musi mit obrazovou vrstvu nebo jasne WAITING_ON_OWNER rozhodnuti.
- Nesmime nahrazovat slozenou Figma sekci jen CSS gradientem, pokud design ukazuje media layer.

### P0-06 - Product detail Timberwolf ma spatne hero, swatche a benefit media

Local detail neni fyzicky podle Figma detailu.

Evidence:

- Hero: local pouziva jinou/product top image, Figma ma lifestyle/exterior hot tub image.
- Swatches: local ukazuje prazdne/svetle swatch karty s textem, Figma ma material/cabinet swatch obrazky.
- Benefits/options: local pouziva generovane CSS ikonove media tvary, Figma ma konkretni thumbnail/foto/plus card kompozici.
- Detail height delta je `-348px`.

Impact:

- Hlavni produktovy detail pusobi jako skeleton, ne jako Figma page.

Required repair:

- Rozdelit benefit karty na real interactive vs static bez fake affordance, ale vizualne podle Figmy.
- Swatche: available owner swatche napojit, missing do WAITING_ON_OWNER.
- Hero: source decision, ale ne tise nahradit jinym cropem.

### P0-07 - Mobile homepage neni podle Figmy

Mobile HP fyzicky neodpovida `GM - HP`.

Evidence:

- Figma ma po hero cerveny promo/sale block; local jde rovnou na category cards.
- Figma mobile showroom je tmavy card/collage pattern; local ma jinou a prazdnejsi kompozici.
- Figma mobile footer ma svetle/accordion-like bloky + mountain strip; local ma tmavy navy footer.

Poznamka:

- V drivejsim plánu byl promo block v compact/laptop stavech schovany kvuli kolizim. To ale neni automaticky licence odstranit mobilni Figma promo, pokud Figma mobile frame promo ma.

Required repair:

- Znovu rozhodnout mobile promo scope proti Figme.
- Footer opravit globalne, tim se opravi i mobile spodek.

### P0-08 - Kontakt: mapa a kontaktni osoby nejsou podle Figmy

Kontakt height sedi, ale vizualni obsah ne.

Evidence:

- Figma `KONTAKT`: tmava/modra mapa s cervenym pinem.
- Local: svetla seda mapa a tmavy pin.
- Figma: 6 kontakt/person cards s fotkami.
- Local: 3 kontakt cards s inicialami/avatar fallback stylem.

Required repair:

- Map layer musi byt Figma map/card nebo real map scope, ne mezistav.
- Contact directory musi pouzit dostupne fotky; missing osoby/assets do WAITING_ON_OWNER.

### P0-09 - Zaruka cards nejsou Figma cards

Local `/zaruka/` je proti Figme kratsi a chybi dulezita vizualni vrstva.

Evidence:

- Figma ma tri produktove/zarucni karty s hot tub obrazkem nahore.
- Local ma tri bile textove cards bez obrazku.
- Figma note je pozicovana vpravo vedle cards; local note je pod nimi.

Required repair:

- Zaruka component obnovit podle Figma card contractu.
- Obrazky bud Figma/available asset, nebo WAITING_ON_OWNER.

### P0-10 - Maintenance page chybi cca 1021px obsahu proti Figme

`/kolik-stoji-udrzba/` je proti Figma frame `KOLIK STOJI UDRZBA` o `1021px` kratsi.

Evidence:

- Figma obsahuje dlouhy textovy clanek se sekcemi a vice odstavci.
- Local ma vyrazne zkracenou verzi a preskakuje na CTA/footer mnohem driv.

Required repair:

- Source-of-truth check: old Arctic / owner text / Figma copy.
- Pokud Figma copy neni finalni obsah, musi byt explicitni content-source rozhodnuti. Jinak doplnit missing text.

## P1 nalezy

### P1-01 - O nas: tymove karty ztratily fotografie

Figma `O NAS` ma tymove karty s fotkami. Local ma placeholder/inicialy. Vyska sedi, ale vizualni vyznam ne.

Status:

- Pokud fotky nejsou owner-approved, je to WAITING_ON_OWNER.
- Nesmime to ale oznacit za Figma parity pass.

### P1-02 - Showroom page pouziva jine fotky/cropy nez Figma

`/showroom/` ma stejnou vysku jako Figma, ale fyzicky je jina:

- Figma hero: showroom/interier s virivkou a Arctic sign.
- Local hero: exterior/storefront/yellow building.
- Figma ma dve velke showroom fotky v obsahu; local ma jine fotky/cropy a kompozici.

Status:

- PR-C pravidlo rika nevymyslet showroom fotky. Owner fotky jsou spravny source, ale musi byt vedome zapsane jako visual-source override proti Figme.
- Pokud se ma Figma vizual brat 1:1, current neni pass.

### P1-03 - Podpora je o 305px vyssi a ma jiny rhythm

`/podpora/` funkcne vypada pouzitelne, ale fyzicky nesedi:

- FAQ cards a download rows maji v localu jiny vertical rhythm.
- Form block je vyssi/volnejsi.
- Footer problem se opakuje.

### P1-04 - Certifikaty jsou layoutove blizko, ale card styling/loga nejsou 1:1

Figma ma loga v mensich rounded white cards. Local ma velke loga ve velkych bilych plochach a jinou vizualni hustotu.

### P1-05 - Vlastnosti detail ma spatny hero image a textovou hustotu

`/vlastnosti/izolace-virivky/` ma celkovou vysku skoro stejnou, ale:

- Figma hero image je jiny hot tub/crop.
- Local ma zkracene textove bloky v uvodu proti Figme.
- Dalsi vlastnosti cards jsou blizko, ale footer znovu pada na P0 global.

### P1-06 - Reference archive je funkcne lepsi, ale neni 1:1 vizual/source

PR-B spravne vratil `/reference/` jako archive/grid a lightbox pattern. Fyzicky ale:

- Figma `REFERENCE` pouziva opakovany Timberwolf visual/content.
- Local ma realne rozmanite reference.

Status:

- To muze byt spravne jako content-source rozhodnuti, ale musi byt explicitne zapsano.
- Layout/card contract vypada blizko; content/crop parity neni 1:1.

### P1-07 - Mobile menu source mismatch

Figma `GM - HP menu` export ukazuje white top bar, dark overlay a search input na y cca 527. Local menu ma nav linky a CTA viditelne nad search inputem.

Status:

- Bud je Figma mobile menu frame nekompletni/vrstvy jsou skryte, nebo local implementuje jiny UX.
- Neoznacovat jako pass bez potvrzeni.

## Systemovy problem

Aktualni QA vrstva meri hodne geometrii a existenci trid, ale ne vzdy skutecny vizualni zdroj. Tim vznikl presne ten pruser:

- `f-footer--handoff` existuje, test projde.
- Ale fyzicky zmizela Figma mountain paticka.
- `f-showroom-panel--collage` existuje, test projde.
- Ale fyzicky jsou tam prazdne/faint media vrstvy.
- Product media v HTML existuje, ale fyzicky karty vypadaji prazdne.

Tohle musi byt opraveno pred dalsim "page polish". Jinak budeme dal lepit stranku po strance a globalni komponenty zustanou rozbite.

## Doporučeny dalsi repair order

1. Opravit audit/guard contracts: footer background, showroom media visibility, product-card visible media, mobile promo decision.
2. PR-D restartovat jako skutecny shared visual components refactor, ne jako page polish.
3. Global footer restore: zachovat Figma landscape background + vyresit CTA handoff bez tmaveho override.
4. Showroom canonical component: jedna implementace pro HP/category/swimspa/mobile, s owner asset crop contractem.
5. Product/media component pass: product cards, detail hero, swatches, benefit media, WAITING_ON_OWNER list.
6. Page-level follow-up az potom: zaruka, kontakt, maintenance, o nas, support.

