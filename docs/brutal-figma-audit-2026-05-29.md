# Brutal Figma audit - Arctic Spas

Datum: 2026-05-29  
Scope: porovnani aktualniho localu proti Figma frame exportum, stranka po strance.  
Verdikt: implementace neni vizualne hotova. Nektere casti jsou blizko, ale existuje rada P0/P1 rozdilu, ktere jsou videt bez pixel-huntingu.

Errata notice (append-only):
- For source-role corrections (content vs visual vs wireframe state), use `docs/brutal-figma-audit-2026-05-29-errata.md` together with this file.

## Podklady

Figma exporty:

`docs/screenshots/brutal-figma-audit-2026-05-29/figma/`

Aktualni local screenshoty:

`docs/screenshots/brutal-figma-audit-2026-05-29/current/`

Side-by-side porovnani:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/`

Desktop viewport:

`1920px` sirka, full-page screenshot.

Mobile viewport:

`375px` sirka, full-page screenshot.

## Legenda zavaznosti

| Priorita | Vyznam |
|---|---|
| P0 | Viditelna chyba proti Figme nebo rozbita funkcni/obsahova cast. Musi se opravit pred klientskym review. |
| P1 | Vizuální odchylka, ktera kazi presnost nebo opakovatelny component pattern. Opravit pred finalem. |
| P2 | Mensi spacing/crop/token drift. Opravit po P0/P1, pokud se honi pixel parity. |

## Globalni nalezy

| ID | Priorita | Problem | Dopad |
|---|---:|---|---|
| G-01 | P0 | CTA/footer prechod je na mnoha strankach spatne. Local opakovane ukazuje svetle tyrkysovy horizontalni pas pred footerem, Figma ma plynuly frost/mountain/footer prechod. | Kazi konec skoro kazde stranky a pusobi jako layer bug. |
| G-02 | P0 | Showroom collage komponenta je rozbita nebo nekompletni. Ve Figme jsou okolo tmaveho panelu fotky, local casto ukazuje tmavy panel s prazdnymi nebo polopruhlednymi boxy. | Jeden z nejviditelnejsich shared bloku na HP/kategoriich. |
| G-03 | P0 | Konfigurator banner neni podle Figmy. Figma ma cerveny gradient, produktovy/interierovy vizual a slozitejsi kompozici; local je casto jen plain cervena plocha. | Velky CTA blok vypada nedodelane. |
| G-04 | P0 | Chybi nebo jsou prazdne produktove obrazky a assety: produktove karty, barvy/swatch na detailu, team fotky, kontaktni avatary. | Pusobi jako broken media, ne jako final web. |
| G-05 | P1 | Predchozi audit/tracker oznacil vic stranek jako pass, ale realne ignoroval image vrstvy, obsahovou strukturu a shared komponenty. | Plan neni spolehlivy jako zdroj "hotovo". |
| G-06 | P1 | Oproti Figme se opakovane lisi radius/stin/kompaktni rytmus karet. | Neni to jeden bug, ale soustava komponentovych driftu. |

## Homepage

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/hp-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| HP-01 | P0 | Hero crop/scale nesedi. | Ve Figme je virivka v hero dominantnejsi, v localu je mensi a jinak posazena. Prvni viewport tak nepůsobí stejne. |
| HP-02 | P0 | Showroom section je vizualne rozbita. | Figma ukazuje tmavy showroom panel s realnymi fotkami okolo; local ma velke prazdne/faint image vrstvy. |
| HP-03 | P1 | Reference vypadaji jinak nez Figma. | Content muze byt realny misto placeholderu, ale cropy, overlay a pilulky nepusobi jako stejny component. |
| HP-04 | P1 | Promo badge/visual neni pixelove stejne. | Badge existuje, ale pozice a meritko nejsou stejne jako ve Figme. |
| HP-05 | P1 | CTA/footer pas. | Dole se objevuje spatny svetly pruh namisto Figma prechodu. |

## Kategorie - virivky

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/kategorie-virivky-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| CAT-HOT-01 | P0 | Benefit/warranty obrazkovy blok je prazdny. | Figma ma realnou fotku virivky v rounded karte, local ma svetly prazdny placeholder. |
| CAT-HOT-02 | P0 | Produktove karty maji prazdne media area. | Ve Figme jsou jasne rendery/fotky produktu, local ukazuje bile prazdne plochy u casti produktu. |
| CAT-HOT-03 | P0 | Konfigurator banner je plain cerveny. | Chybi Figma image/gradient/laptop/produktova kompozice. |
| CAT-HOT-04 | P0 | Showroom collage je rozbita. | Stejny globalni problem jako na HP. |
| CAT-HOT-05 | P1 | Sale/promo badge ve Figma kategorii neni v localu stejne. | Figma ukazuje pro kategorii pravy promo prvek, local ho nema ve stejne podobě. Je nutne rozhodnout, zda jde o zamer klienta nebo implementacni chybu. |
| CAT-HOT-06 | P1 | Reference sekce neni vizualne stejna. | Realny obsah muze byt spravny, ale proti Figme je jiny rytmus a tmavost/cropy. |
| CAT-HOT-07 | P1 | CTA/footer pas. | Spodni prechod se opakuje spatne. |

## Kategorie - swimspa

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/kategorie-swimspa-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| CAT-SWIM-01 | P0 | Struktura je vyrazne kratsi nez Figma kategorie. | Current screenshot ma cca 6674px vysku, Figma kategorie cca 8583px. Pokud je swimspa umyslne kratsi varianta, nesmi se tvarit jako pixel parity s Figma kategorii. |
| CAT-SWIM-02 | P0 | Benefit/warranty image block je prazdny. | Stejny problem jako u virivek. |
| CAT-SWIM-03 | P0 | Product cards maji prazdne obrazky. | Velke bile media area nejsou final stav. |
| CAT-SWIM-04 | P0 | Konfigurator banner je plain cerveny a textove sporny. | U swimspa kontextu se objevuje formulace "vlastni virivku"; vizualne chybi Figma kompozice. |
| CAT-SWIM-05 | P0 | Showroom collage je rozbita. | Shared component problem. |
| CAT-SWIM-06 | P1 | CTA/footer pas. | Spodni prechod neni podle Figmy. |

## Detail produktu - Timberwolf

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/detail-timberwolf-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| PROD-01 | P0 | Hero/product image nesedi. | Figma ma exterierovy hot tub image; local pouziva top-down/plan-like obrazek, tedy uplne jiny vizualni zamer. |
| PROD-02 | P0 | Shell/cabinet swatche jsou prazdne. | Figma ukazuje materialove/obrazkove vzorky, local ma bile nebo prazdne karty. |
| PROD-03 | P1 | Benefit/optional equipment karty nejsou stejne. | Local pouziva sede kruhove placeholdery/ikony a jiny rytmus nez Figma. |
| PROD-04 | P1 | Reference na detailu nejsou vizualne stejne. | Obsah muze byt realny, ale card treatment a cropy nejsou Figma parity. |
| PROD-05 | P1 | CTA/footer pas. | Opakovany globalni problem. |

## Showroom

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/showroom-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| SHOW-01 | P0 | Hero image je spatny. | Figma pouziva showroom/interier s virivkou, local ukazuje venkovni bazen/deck. |
| SHOW-02 | P0 | "Proc navstivit..." karta neni podle Figmy. | Figma ma rounded white card se stinem a kompaktnim icon rhythm; local je plossi a mene komponovany. |
| SHOW-03 | P0 | Hlavni obsahove fotky chybi. | Ve Figme jsou dve velke showroom fotky, v localu vznikaji prazdna mista a text bez odpovidajiciho media bloku. |
| SHOW-04 | P1 | Kontaktni/hero kompozice nesedi. | Mapa/contact chips a rozlozeni nejsou presne. |
| SHOW-05 | P1 | CTA/footer pas. | Spodni prechod neni podle Figmy. |

## Vlastnosti - listing

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/vlastnosti-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| FEAT-01 | P1 | CTA/footer pas. | Jinak je stranka jedna z blizsich, ale globalni footer handoff porad nesedi. |
| FEAT-02 | P2 | Card scale/spacing drift. | Karty jsou blizko, ale ne uplne stejne v meritku, mezerach a cropu. |
| FEAT-03 | P2 | Sirka/header alignment. | Lokal vypada lehce sirsi/posunuty oproti Figma frame. |

## Vlastnosti - detail izolace

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/vlastnosti-detail-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| FEATD-01 | P0 | Hero/content image nesedi. | Figma ma cisty patio/spa obrazek, local pouziva starsi zakrytou virivku u cihly. |
| FEATD-02 | P0 | Body copy je zkracene. | Figma obsahuje delsi article, local ma mene textu a celkove pusobi jako zkracena verze. |
| FEATD-03 | P0 | Diagram block neni podle Figmy. | Figma diagram sedi v rounded white cardu; local je mensi raw image/white rectangle bez stejneho container treatmentu. |
| FEATD-04 | P1 | Mezery kolem sekci nesedi. | Okoli diagramu a textovych casti ma jiny vertical rhythm. |
| FEATD-05 | P1 | CTA/footer pas. | Opakovany globalni problem. |

## Sluzby

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/sluzby-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| SERV-01 | P1 | Card images nejsou Figma-identicke. | Local pouziva realne/fallback fotky misto opakovaneho Figma placeholder image. To muze byt obsahove spravne, ale neni to vizualni parity. |
| SERV-02 | P1 | Cropy a textovy rhythm nesedi. | Karty maji podobny layout, ale crop a spacing nejsou stejne jako Figma. |
| SERV-03 | P1 | CTA/footer pas. | Spodni prechod neni podle Figmy. |

## Certifikaty

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/certifikaty-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| CERT-01 | P1 | Certifikatove obrazky nemaji Figma card containment. | Figma ma TUV/star visualy v rounded white kartach se stinem, local pusobi jako raw obrazky/square bloky na strance. |
| CERT-02 | P1 | CTA/footer pas. | Spodni prechod neni podle Figmy. |
| CERT-03 | P2 | Text block spacing drift. | Menší odchylky v mezerach a umisteni textu. |

## Zaruka

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/zaruka-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| WAR-01 | P0 | Warranty table komponenta je spatne. | Figma ma tri vertikalni rounded produktove karty s obrazkem nahore; local je plochy horizontalni table s linkami a bez produktovych obrazku. |
| WAR-02 | P0 | Page height/visual hierarchy nesedi. | Current je kratsi a hlavni warranty blok nema stejnou hierarchii jako Figma. |
| WAR-03 | P1 | Poznamky/links placement se lisi. | Local ma pravy textovy blok jinak posazeny nez Figma. |
| WAR-04 | P1 | CTA/footer pas. | Spodni prechod neni podle Figmy. |

## Podpora

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/podpora-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| SUP-01 | P1 | Stranka je delsi nez Figma. | Current ma cca 5509px, Figma cca 5201px. Rozdil vypada zpusobeny hlavne card/form spacingem. |
| SUP-02 | P1 | Download/FAQ rows jsou prilis vzdušne. | Realny obsah je ok, ale row heights a spacing nejsou Figma compact style. |
| SUP-03 | P1 | Servisni formular card nesedi. | Local form card je vetsi/sirsi a field geometrie neni stejna jako Figma. |
| SUP-04 | P1 | CTA/footer pas. | Opakovany globalni problem. |
| SUP-05 | P2 | Kategorie chips text/count mismatch. | Pravdepodobne content-driven, ale neni to Figma parity. |

## O nas

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/o-nas-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| ABOUT-01 | P0 | Team sekce je rozbita. | Figma ukazuje ctyri photo cards, local ma prvni tri jako prazdne/missing image nebo text-only plochy a jen posledni fotku. |
| ABOUT-02 | P0 | Statistika ma spatnou barvu. | Figma pouziva cervena cisla, local ma tmava/cerna cisla. |
| ABOUT-03 | P1 | Jobs accordion/card styling neni podle Figmy. | Figma ma rounded accordion s plus ikonou a sede collapsed rows, local vypada square/flat a jinak rytmizovane. |
| ABOUT-04 | P1 | Team spacing/carousel affordance se lisi. | Sekce je funkcne podobna, ale vizualni rhythm neni podle Figmy. |
| ABOUT-05 | P1 | CTA/footer pas. | Opakovany globalni problem. |

## Kontakt

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/kontakt-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| CONT-01 | P0 | Mapa je spatne stylovana. | Figma ma tmavy/modry overlay s cervenym pinem; local je svetle seda mapa a tmavy/cerny pin. |
| CONT-02 | P0 | "Dalsi dulezite kontakty" neni podle Figmy. | Figma ukazuje sest person contact cards s avatary; local ukazuje department/contact cards s inicialami a jinou hierarchii. |
| CONT-03 | P1 | Top heading line-break/text nesedi. | Lokal ma trochu jine zalomeni a rytmus titulku. |
| CONT-04 | P1 | Map card/button positioning drift. | Panel a button jsou blizko, ale ne stejne. |
| CONT-05 | P1 | Footer/handoff. | Spodni cast neni podle Figmy. |

## Reference

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/reference-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| REF-01 | P1 | Realny obsah se lisi od Figma placeholderu. | To muze byt spravne obsahove, ale vizualne to neni 1:1. |
| REF-02 | P1 | Card crop/overlay consistency drift. | Nektere karty jsou tmavsi nebo cropnute jinak nez Figma pattern. |
| REF-03 | P1 | CTA/footer pas. | Spodni prechod neni podle Figmy. |

## Kolik stoji udrzba

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/maintenance-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| MAINT-01 | P0 | Masivni ztrata obsahu. | Figma frame ma cca 4324px vysku, current cca 2692px. Local vynechava velkou cast clanku, odstavcu a bullet listu. |
| MAINT-02 | P0 | Article struktura neni podle Figmy. | Local redukuje dlouhy clanek na kratke headings + male texty. |
| MAINT-03 | P1 | CTA prichazi prilis brzy. | Protoze chybi obsah, konec stranky nastava mnohem driv nez ve Figme. |
| MAINT-04 | P1 | CTA/footer pas. | Opakovany globalni problem. |

## Servis

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/servis-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| SREQ-01 | P0 | Formular card styling je spatne. | Figma ma rounded card a rounded inputy, local ma square/flat white block a hranate inputy. |
| SREQ-02 | P1 | Formular width/height a button placement nesedi. | Layout je funkcni, ale neodpovida kompaktnimu Figma patternu. |
| SREQ-03 | P1 | Cenove sloupce maji spacing drift. | Text a sloupce jsou podobne, ale ne sedi presne. |
| SREQ-04 | P1 | CTA/footer pas. | Spodni prechod neni podle Figmy. |

## Mobile homepage

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/mobile-hp-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| MOB-HP-01 | P0 | Mobile promo/sale banner chybi. | Figma ma velky cerveny promo blok hned po hero, local jde rovnou na category cards. |
| MOB-HP-02 | P0 | Category cards jsou moc nahore a jinak velke. | Figma mobile karty jsou velke standalone bloky az po promu, local je kompresovanejsi a strukturou jinde. |
| MOB-HP-03 | P0 | Hero crop/height/text placement nesedi. | Local hero ma jiny crop a textove posazeni nez Figma mobile. |
| MOB-HP-04 | P0 | Showroom mobile card nesedi. | Figma ma tmavou kartovou kompozici s collage/pin, local vypada jinak a mensi. |
| MOB-HP-05 | P1 | Reference carousel/cards drift. | Realny obsah muze byt spravne, ale vizualne se lisi. |
| MOB-HP-06 | P1 | Final CTA/footer layout je komprimovany. | Dole nesedi spacing a kompozice. |

## Mobile menu

Compare:

`docs/screenshots/brutal-figma-audit-2026-05-29/compare/mobile-menu-figma-vs-current.png`

| ID | Priorita | Problem | Popis |
|---|---:|---|---|
| MOB-MENU-01 | P0 | Figma export a local menu nejsou srovnatelne bez kontroly layer state. | Figma export vypada skoro jako prazdny dark panel se search dole, local ma plne navigacni menu s CTA a kontakty. Je potreba otevrit Figma layer/state a potvrdit, co je autoritativni. |
| MOB-MENU-02 | P1 | Pokud je Figma frame autorita, local ma extra obsah. | Nav polozky, CTA a kontaktni casti jsou navic proti exportu. Pokud export chybi layer visibility, problem je ve zdroji auditu, ne nutne v kodu. |
| MOB-MENU-03 | P1 | Close/nav/search rhythm se muze lisit. | Bez potvrzeneho Figma stavu nelze spravedlive oznacit jako pass. |

## Nejvetsi opravne bloky

| Poradi | Blok | Proc prvni |
|---:|---|---|
| 1 | Shared missing media/image layer bugy | Resi showroom, kategorie, produktove karty, team, contact cards a swatche. Viditelne napric webem. |
| 2 | CTA/footer prechod | Jeden globalni bug na skoro kazde strance. Oprava jedne komponenty muze odstranit hodne P1 najednou. |
| 3 | Konfigurator banner | Velky prominentni CTA blok, aktualne vypada jako placeholder. |
| 4 | Warranty table | /zaruka/ je proti Figme jina komponenta, ne jen spacing drift. |
| 5 | Maintenance article content | /kolik-stoji-udrzba/ ma masivni obsahovy deficit. |
| 6 | Contact/map/team assets | Viditelne missing/incorrect personal/contact media. |
| 7 | Mobile homepage | Mobile se od Figmy lisi strukturou, ne jen drobnostmi. |

## Poznamky k dalsimu postupu

Neopirat se uz o predchozi "pass" v trackeru bez obrazkove kontroly. Ten tracker zachytil layout/token audit, ale minul realne vizualni chyby.

Pro opravy je lepsi jit komponentove, ne stranku po strance. Nejdriv shared komponenty, pak znovu screenshot audit:

| Krok | Akce |
|---:|---|
| 1 | Opravit shared image/media rendering a asset mapping. |
| 2 | Opravit footer/CTA handoff globalne. |
| 3 | Opravit showroom collage component. |
| 4 | Opravit configurator CTA/banner component. |
| 5 | Opravit specialni stranky: zaruka, maintenance, kontakt, o nas. |
| 6 | Znovu spustit desktop/mobile screenshot diff proti Figme. |
