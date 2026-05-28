# Phase 5B: Design System & Component Parity Audit

Datum: 2026-05-27
Status: audit hotovy, PR0-PR2 uzavrene, implementace PR3-PR6 ceka
Navazuje na: Phase 5A compact laptop / hero / promo / footer stabilizace

Update 2026-05-28 (PR2):
- Reference card radius contract byl sjednocen na `40px` pro homepage references, `/reference/` archive cards a `/product/timberwolf/` references.

## Executive Summary

Hlavni problem po Phase 5A neni globalni token layer, ale komponentova vrstva. Live Figma API potvrdilo, ze aktualni zakladni tokeny v implementaci jsou z velke casti spravne. Rozpad Figma parity vzniká hlavne tim, ze opakovane prvky nejsou centralizovane jako jeden design-system modul, ale existuji ve vice lokalnich variantach s ruznymi CSS overrides.

Nejdulezitejsi zaver:

- Neměnit tokeny podle `docs/figma-tokens.md`; dokument je zastaraly.
- Nejdrive sjednotit komponenty, ktere se opakuji napric webem.
- Teprve potom delat page-by-page Figma parity pass a manualni screenshot sign-off.

## Phase Classification

Tento audit patri do nove podfaze:

**Phase 5B - Design System & Component Parity Audit / Refactor Plan**

Doporucene cleneni Phase 5:

- Phase 5A: compact/laptop/hero/promo/footer stabilizace.
- Phase 5B: design-system audit a sjednoceni opakovanych komponent.
- Phase 5C: page-by-page Figma parity pass a manualni screenshot sign-off.

## Sources Used

Audit vychazi z techto vrstev:

- Live Figma API data ze souboru `Arctic-spas.cz grafika`.
- Lokalni computed-style browser audit pres Playwright Core a Chrome.
- Zdrojove LESS/PHP sablony.
- Existujici QA/audit skripty.
- Predchozi screenshoty a uzivatelske vizualni vytky.

Poznamka:

- Prvni plosny browser audit pres vsechny stranky a viewporty timeoutnul.
- Nasledne zkracene browser audity pro klicove komponenty probehly a potvrdily hlavni computed hodnoty.
- Full sweep timeoutnul; targeted browser audits completed for listed component mismatches.

## Live Figma Token Truth

Live Figma API potvrdilo aktualni hodnoty:

| Token / styl | Live Figma hodnota | Stav v implementaci |
|---|---:|---|
| Background | `#EEF1F5` | odpovida |
| Dark / text | `#23282F` | odpovida |
| Red CTA | `#A31F37` | odpovida |
| Font family | `Red Hat Display` | odpovida |
| H1 | `56px / 61px`, weight `700` | globalne odpovida smeru |
| H2 | `36px / 51px`, weight `700` | globalne odpovida smeru |
| Velky panel radius | `40px` | nekde odpovida, nekde ne |
| Pill button radius | `50px` | nekde odpovida, nekde ne |

## Deprecated Source: figma-tokens.md

`docs/figma-tokens.md` je zastaraly a nesmi byt pouzity jako pravda pro implementaci tokenu.

Zastarale nebo zavadejici hodnoty v dokumentaci:

- Stare barvy typu `#071826`, `#0b2437`, `#c82032`.
- Male radiusy typu `5px` nebo `8px`.
- Nesoulad s aktualni live Figmou, ktera pouziva `#23282F`, `#A31F37`, `40px` a `50px`.

Pravidlo pro Phase 5B:

- Tokeny menit pouze proti live Figma API, ne proti stare dokumentaci.

## Confirmed Local Computed Mismatches

Tyto body jsou potvrzene live renderem nebo prime porovnanim Figma API + local:

| Oblast | Local stav | Figma stav | Dopad |
|---|---:|---:|---|
| `/reference/` reference cards | radius `8px` | radius `40px` | viditelne spatne zaobleni karet |
| `/product/timberwolf/` reference cards | radius `16px` desktop | radius `40px` | product detail nema stejny modul realizaci |
| Compact/mobile reference cards | ruzne `8px` / `50px` | sjednoceny modul | nekonzistentni responsivni vzhled |
| `/kontakt/` top buttons | radius `8px` | radius `50px` | form buttony nejsou Figma pill |
| `/kontakt/` map/location card | cca `24px` desktop, cca `21px` mobile | radius `40px` | mapa/kontakt karta nesedi podle Figmy |
| `/showroom/` CTA button | radius `0px` | radius `50px` | ostre tlacitko misto pill |
| `/dalsi-informace/` | redirect na `/#order-progress` | samostatny Figma frame | resolved: vedoma IA odchylka (redirect-only hub) |
| Product/card images | casto upscalovane z malych zdroju | Figma predpoklada ostre obrazky | ani spravne CSS nezachrani vizualni kvalitu |

## Image Quality Findings

Byl potvrzen problem s prirozenym rozlisenim nekterych obrazku.

Priklady:

| Stranka | Priklad | Local display | Natural source | Problem |
|---|---|---:|---:|---|
| `/virivky/` | product card images | cca `281x215` | cca `127x79` | silny upscale |
| `/swimspa/` | hero `swimspa.jpg` | cca `1920x795` | `800x600` | velky upscale |
| `/swimspa/` | product card images | cca `281x215` | cca `127x79` | silny upscale |
| `/product/timberwolf/` | hero image | cca `1920x795` | `800x800` | hero upscale/crop risk |
| `/reference/` | reference image | cca `438x320` | napr. `168x168` | nekvalitni reference karta |

Zaver:

- Image parity neni jen CSS problem.
- Je potreba doplnit/vymenit assety ve spravnem rozliseni.
- Audit by mel hlidat pomer `display size / natural size`.

## Component Architecture Findings

V kodu existuji sdilene PHP sablony, ale jejich vizualni pravidla jsou prebijena mnoha LESS overrides.

Klicove sablony:

- `wp-content/themes/arctic/templates/section/contact.php`
- `wp-content/themes/arctic/templates/section/showroom.php`
- `wp-content/themes/arctic/templates/section/map.php`
- `wp-content/themes/arctic/modules/references/templates/section-recent.php`
- `wp-content/themes/arctic/modules/references/templates/post/listing.php`
- `wp-content/themes/arctic/templates/footer.php`

Klicove CSS riziko:

- `wp-content/themes/arctic/src/less/_components.less` obsahuje mnoho page-specific a breakpoint-specific oprav pro stejne komponenty.

Pocet opakovanych selektoru v `_components.less`:

| Selector | Pocet vyskytu | Interpretace |
|---|---:|---|
| `.f-section--references` | 137 | reference nejsou jeden modul |
| `.f-showroom-panel` | 88 | showroom panel ma mnoho lokalnich overrides |
| `.f-contact-cta` | 68 | contact CTA je prepisovane na vice mistech |
| `.f-footer--arctic` | 112 | footer ma mnoho specialnich pravidel |
| `.f-listing--reference` | 51 | reference listing ma vice variant |
| `.f-hero-promo` | 53 | promo je rizikove napric breakpointy |
| `.f-local-map` | 26 | mapa/location neni plne sjednocena |
| `.a-button` | 52 | button system neni dost centralizovany |

Zaver:

- To neni cisty design system.
- Je to sada opakovanych komponent s lokalnimi zaplatami.
- Proto se jeden prvek opravi na homepage, ale zustane spatne na archive/detail/mobile.

## Module-by-Module Audit

### 1. Reference Cards

Figma:

- Card `438x320`.
- Radius `40px`.
- Gradient overlay pres image.
- Meta pills: dark/white.
- Title: `Red Hat Display`, cca `22px / 34px`, medium, underline.
- Carousel arrow: bile kolecko `42x42`, cerna sipka.
- `Zobrazit dalsi reference` button: outline red, radius `50px`.

Local:

- Homepage/reference recent je nejbliz Figme.
- `/reference/` archive ma radius `8px`.
- Product detail reference card ma desktop radius `16px`.
- Compact/mobile varianty maji dalsi odchylky, vcetne radiusu `50px`.

Verdikt:

- Reference card musi byt jeden modul, ne tri varianty.
- Archive, homepage/category recent a product detail musi pouzivat stejny zaklad.

### 2. Button System

Figma:

- Hlavni pill buttons maji radius `50px`.
- Outline buttons maji red border `#A31F37`.
- Header CTA a form/action buttons jsou pill.

Local:

- Global button token existuje, ale ne vsechny buttony ho zdedi.
- `/kontakt/` form buttons maji radius `8px`.
- `/showroom/` appointment button ma radius `0px`.
- Nektere buttony maji `999px`, coz je opticky podobne pillu, ale neni sjednocene s Figma tokenem `50px`.

Verdikt:

- Potreba centralni button contract.
- Zakaz lokalnich button radiusu mimo explicitni vyjimky.

### 3. Contact CTA

Figma:

- Cerveny panel `#A31F37`.
- Radius `40px`.
- Vnitrni bar `rgba(0,0,0,0.1)`.
- Vnitrni bar radius `40px`.
- Button radius `50px`.

Local:

- Hlavni contact CTA je vetsinou blizko Figme.
- CSS je ale prepisovane na mnoha mistech.
- Riziko: nova stranka muze dostat odlisnou variantu.

Verdikt:

- Contact CTA ma byt jeden sdileny modul bez page-specific oprav.

### 4. Showroom

Figma:

- Tmavy panel `#23282F`.
- Radius `40px`.
- Obrazky radius `40px`.
- Badge `280 m²` red `#A31F37`.
- CTA buttons radius `50px`.

Local:

- Homepage showroom panel je blizko.
- `/showroom/` special CTA button ma radius `0px`.
- Showroom modul ma mnoho overrides.

Verdikt:

- Sjednotit showroom panel a showroom CTA buttony.

### 5. Map / Location

Figma:

- Contact map card radius `40px`.
- Footer map card radius `30px`.
- Pin/location prvky maji konzistentni sedo/bily vizual.

Local:

- `/kontakt/` map card ma cca `24px`, mobile cca `21px`.
- Footer map je blizko `30px`.
- Nektere location prvky vypadaji jako lokalni ad hoc reseni.

Verdikt:

- Map/location musi byt jeden modul se spravnymi radiusy a barvami podle Figmy.

### 6. Footer

Figma:

- Quick contact card radius `40px`.
- Map card radius `30px`.
- Copyright: `Copyright © 2024 BASPA s.r.o. Všechna práva vyhrazena.`
- Figma obsahuje `Vytvořil eboost`.

Local:

- Quick contact a map jsou blizko.
- Fotka Lukase Duska je doplnena.
- Copyright BASPA je spravne.
- `eboost` je odstranen.

Verdikt:

- Bez `eboost` je vedoma produktova odchylka podle zadani, ne chyba.
- Footer je blizko, ale ma byt hlidan jako modul a ne jako screenshot.

### 7. Hero Promo

Figma:

- Promo banner existuje pro full desktop homepage.
- Nema se rozbijet v compact/laptop/mobile stavech.

Local:

- V aktualnich auditovanych compact viewports uz promo nebylo viditelne.
- Historicky problem byl, ze promo karta byla viditelna v realnem Windows 175% scaling stavu.
- Architektonicka otazka zustava: jestli je logika opravdu default off + enable only desktop, nebo jen dalsi sada breakpoint potlaceni.

Verdikt:

- Overit a vynutit pravidlo: default hidden, explicitne enable jen homepage full desktop `min-width: 1280px` nebo podle finalniho Figma breakpoint rozhodnuti.

### 8. Dalsi Informace

Figma:

- Existuje standalone frame `DALSI INFORMACE`.
- Obsahuje H1, breadcrumb, header, cca 10 white cards, contact CTA a footer.

Local:

- `/dalsi-informace/` redirectuje na homepage `/#order-progress`.
- Footer ma sloupec `Další informace`, ale standalone stranka neni realne implementovana jako Figma frame.

Verdikt:

- IA rozhodnuti uzavreno v PR0 (2026-05-27):
- `/dalsi-informace/` zustava redirect-only route na `/#order-progress`.
- Standalone frame `DALSI INFORMACE` je vedoma odchylka mimo aktualni implementacni scope.

## QA Coverage Gaps

Aktualni `tools/figma-visual-audit.js` kontroluje hodne geometrie, ale nechyta vsechny stylove odchylky.

Nechyta nebo nedostatecne hlida:

- `/reference/` card radius `8px`.
- Product detail reference card radius `16px`.
- Contact top buttons radius `8px`.
- Showroom CTA button radius `0px`.
- Contact map card radius cca `24px`.
- Image upscaling pomer.
- Plny mobile visual sign-off vsech stranek.
- Konzistenci opakovanych komponent napric sablonami.

Nutne doplnit do QA:

- Component contract assertions.
- Cross-page module assertions.
- Image natural size vs rendered size checks.
- Manual screenshot checklist.

## Confirmed Priority Order

### P0 - Truth Source

- Pouzivat live Figma API jako source of truth.
- Ignorovat `docs/figma-tokens.md` pro aktualni token values.
- Do master planu/trackeru doplnit, ze token docs jsou zastarale.

### P1 - Component System Refactor

- Reference card jako jeden modul.
- Button system jako jeden contract.
- Contact CTA bez page-specific overrides.
- Showroom panel/CTA sjednoceni.
- Map/location sjednoceni.

### P2 - IA / Missing Page

- Closed in PR0:
- `/dalsi-informace/` je vedomy redirect-only hub route (`301` na `/#order-progress`).
- Neni vedeno jako otevrena implementacni mezera.

### P3 - Asset Quality

- Audit a nahrada nizkorezolucnich produktu/reference/hero obrazku.
- Doporuceni: zadny klicovy image nesmi byt renderovan nad cca `1.25x` natural size.

### P4 - QA Hardening

- Rozsirit `figma-visual-audit.js` o komponentove computed-style checks.
- Pridat image quality checks.
- Pridat realne compact/laptop viewporty vcetne `1097x617`, `904x617`.
- Pridat mobile full-page visual review.

### P5 - Page-by-Page Sign-off

- Homepage.
- Category pages.
- Product detail pages.
- Reference archive/detail.
- Contact.
- Showroom.
- Support/download/service pages.
- Footer/header/mobile menu.

## Recommended Implementation Plan For Phase 5B

1. Freeze live token truth.
2. Create/normalize shared component CSS contracts.
3. Refactor reference card usage across homepage/category/product/archive.
4. Refactor button variants.
5. Refactor map/location radius and colors.
6. Refactor showroom CTA.
7. Keep `/dalsi-informace/` as documented intentional redirect-only hub route.
8. Add QA assertions for every fixed contract.
9. Run local QA plus manual screenshots.
10. Only after that move to Phase 5C page-by-page parity.

## Do Not Do

- Nemenit tokeny podle stareho `figma-tokens.md`.
- Neopravovat dalsi jednotlive sekce page-by-page pred sjednocenim modulu.
- Nepridavat dalsi breakpoint zaplaty misto default component logic.
- Nepovazovat zeleny `npm run qa:local` za manualni Figma sign-off.

## Final Verdict

Phase 5A stabilizovala cast problemu, ale neuzavrela Figma parity. Phase 5B musi byt systemovy refactor design komponent, protoze hlavni pricina je nekonzistentni komponentova vrstva.

Globalni tokeny jsou prevazne spravne. Komponenty nejsou.
