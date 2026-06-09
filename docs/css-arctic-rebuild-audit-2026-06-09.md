# Audit a plán: odříznutí Baspa CSS z vizuální vrstvy Arctic

Status: lokální audit, bez produkčního deploye. Záloha `arctic-spas-2` je uložená mimo repo v `arctic nova/záloha`.

## Závěr

Současný stav není samostatný Arctic frontend. Je to původní Baspa vizuální základ (`dist/css/style.css`) a nad ním velká sada Arctic override pravidel (`dist/css/arctic.css`, `mobile-figma-contract.css`, další lokální CSS hotfixy). To vysvětluje, proč se opakovaně objevují chyby typu tmavé filtry, uříznutý text, špatné mobile menu, rozbitý footer/showroom a nepředvídatelné chování po změně obsahu ve WP adminu.

Správný směr: WordPress, data, admin, CPT, importy a vybrané JS hooky můžou dál vycházet z Baspy. Vizuální frontend CSS ale nesmí být řízen Baspa CSS. Musí vzniknout nová Arctic CSS vrstva podle Figma metadat, s dočasnou kompatibilitou pouze tam, kde ji šablony ještě nutně potřebují.

## Auditní důkazy

### Enqueue vrstvy

`wp-content/themes/arctic/inc/styles.php` dnes vždy načítá:

1. `dist/css/style.css` jako hlavní stylesheet šablony.
2. `dist/css/arctic.css` jako skin závislý na `style.css`.
3. `content-overflow-guard.css`, `mobile-figma-contract.css`, `homepage-mobile-slider.css`, `product-colors-mobile.css`, `catalog-request.css` jako další přebíjecí vrstvy.

To znamená, že Baspa CSS je pořád první a určující vizuální vrstva.

### Velikost a rozsah CSS

| Soubor | Velikost | Reference tříd | Unikátní třídy |
| --- | ---: | ---: | ---: |
| `dist/css/style.css` | 330 951 B | 3 119 | 904 |
| `dist/css/arctic.css` | 395 260 B | 6 342 | 623 |
| `dist/css/mobile-figma-contract.css` | 14 976 B | 197 | 43 |
| `dist/css/homepage-mobile-slider.css` | 5 625 B | 79 | 25 |
| `dist/css/product-colors-mobile.css` | 3 638 B | 59 | 6 |
| `dist/css/catalog-request.css` | 2 427 B | 38 | 12 |

`arctic.css` není čistý nový základ. Je větší než původní `style.css`, ale funguje převážně jako override nad ním.

### Vazba šablon na staré utility

V PHP šablonách a modulech je stále masivní vazba na původní utility:

| Skupina | Počet souborů |
| --- | ---: |
| Baspa `a-*` layout utility (`a-container`, `a-flex`, `a-stack`, `a-button`, `a-image`, `a-off`, `a-gap`, `a-grid`) | 150 |
| Feature/component třídy (`f-header`, `f-footer`, `f-navigation`, `f-section`, `f-product`, `f-listing`...) | 133 |
| JS behavior hooky (`js-off`, `js-carousel`, `js-links`, `js-search`...) | 43 |

To znamená, že `style.css` nejde jen vypnout bez náhrady. Nejdřív musí vzniknout Arctic-owned kompatibilní layout vrstva, nebo se rozsype markup.

### Hotfix vrstva a ořezávání obsahu

Počty rizikových přebití:

| Soubor | `!important` |
| --- | ---: |
| `src/less/_component-contracts.less` | 73 |
| `dist/css/mobile-figma-contract.css` | 316 |
| `dist/css/homepage-mobile-slider.css` | 22 |

V `src/less/_components.less` jsou desítky `overflow: hidden`, pevné `height: ...px`, `max-height` a line-clamp pravidel. To je přesně vzorec, který způsobuje uříznuté věty a schované bloky po běžné změně textu v administraci.

### Figma metadata jako kontrakt

Zdroj grafiky: Figma file `zWLRkhgU5uOipN7I6cGHHe`.

Ověřené uzly:

| Figma node | Význam | Klíčová metadata |
| --- | --- | --- |
| `1:1973` | `GM - HP` mobile homepage | frame 375 × 6046, background `#EEF1F5`, header 375 × 62, hero image 1343 × 556, promo 335 × 288, kategorie 335 × 221, showroom 335 × 695, footer 375 × 1396 |
| `1:2208` | `GM - HP menu` | frame 375 × 774, pozadí `#23282F`, header 375 × 62, close button 45 × 45, search 323 px v pozici x 26 / y 527 |
| `1:2168` | mobile footer | menu group x 32 / y 34, quick-contact card x 16 / y 392 / 335 × 679, bottom landscape y 1016, landscape image 1032 × 650 s gradientem |

Z toho plyne: mobile homepage, menu, footer, showroom ani CTA se nemají “ladit od oka”. Mají být implementované jako Arctic CSS komponenty podle Figma metadat. Textový obsah může být z WP a nemusí se slepě kopírovat z Figmy, protože Figma má místy staré texty/kontakty.

## Co smí z Baspy zůstat

Smí zůstat:

- WordPress struktura, CPT, taxonomie, admin editace, importní skripty a existující data model.
- JS behavior hooky typu `js-off`, `js-carousel`, `js-search`, pokud se jejich vizuální stav přepíše v Arctic CSS.
- Pluginové třídy typu `wp-*`, `swiper-*`, `cky-*`, `pswp-*`, ale jen s minimální kompatibilitou.
- PHP fallbacky pouze tam, kde chrání chybějící data, ne tam, kde nahrazují reálný obsah.

Nesmí zůstat jako vizuální autorita:

- `dist/css/style.css` na frontendu.
- Baspa utility vizuální pravidla pro layout, buttony, offcanvas, header, footer, karty, listingy.
- Další hotfix soubory, které přebíjejí Baspu stovkami `!important`.

## Navržená nová CSS architektura

V nové pracovní kopii `arctic-spas-3` vytvořit samostatnou Arctic CSS vrstvu:

```text
wp-content/themes/arctic/src/arctic-css/
  00-reset.less
  01-tokens.less
  02-base.less
  03-layout.less
  04-components/
    buttons.less
    cards.less
    forms.less
    header.less
    navigation.less
    footer.less
    hero.less
    showroom.less
    references.less
    product-listing.less
    product-detail.less
  05-pages/
    home.less
    category.less
    product.less
    more-info.less
    support.less
    showroom.less
    contact.less
  90-wp-plugin-compat.less
  99-transitional-a-compat.less
  arctic-app.less
```

Výstup:

```text
wp-content/themes/arctic/dist/css/arctic-app.css
```

Dočasná kompatibilita:

- `99-transitional-a-compat.less` definuje jen nutné layout utility pro starý markup (`a-container`, `a-flex`, `a-stack`, `a-grid`, `a-gap`, `a-button`, `a-image`, `a-off`).
- Tato vrstva musí být explicitně označená jako dočasná a nesmí obsahovat Baspa vizuální identitu.
- Cílově se PHP markup postupně přepíše na Arctic komponenty.

## Migrační plán

### P0: zmrazení a bezpečný sandbox

1. Pracovat v `arctic-spas-3`, ne v produkci.
2. Nepouštět katalog/banner ani nové vizuální experimenty do produkce bez schválení.
3. Vygenerovat CSS inventory pro aktuální stav: entrypointy, třídy, `!important`, pevné výšky, `overflow:hidden`.
4. Zapsat Figma kontrakt pro hlavní dostupné frame: homepage mobile/desktop, menu, footer, category hero, product detail, showroom, references.

### P1: nový Arctic entrypoint

1. Přidat build script pro `arctic-app.less -> arctic-app.css`.
2. V `inc/styles.php` přidat lokální přepínač, který v `arctic-spas-3` umí načíst `arctic-app.css` bez `dist/css/style.css`.
3. Zachovat admin/editor CSS odděleně, aby se nerozbila správa WP.
4. Přidat základní reset, tokeny z Figmy a typografii `Red Hat Display`.

### P2: layout kompatibilita bez Baspa vzhledu

1. Vytvořit minimální Arctic implementaci `a-container`, `a-flex`, `a-stack`, `a-grid`, `a-gap`.
2. Vytvořit Arctic implementaci `a-button`, `a-image`, `a-off`, ale bez Baspa barev, radiusů a pozic.
3. Ověřit, že po vypnutí `style.css` stránka nespadne strukturálně.

### P3: globální komponenty podle Figmy

1. Header desktop i mobile podle Figmy.
2. Mobile menu/offcanvas podle node `1:2208`: tmavý panel, červené šipky, přesné řádkování, search, kontakt.
3. Footer podle node `1:2168`: menu accordion, quick contact card, showroom/map card, krajina, logo, copyright.
4. Button systém: červený primární, outline, bílé CTA, ikonové kruhy.
5. Žádné globální `overflow:hidden` na textových kontejnerech bez explicitního důvodu.

### P4: homepage podle Figma kontraktu

1. Mobile hero/slider podle node `1:1973`, včetně gradientů a proporcí.
2. Promo/akční nabídka podle Arctic designu, ne převzatá Baspa karta.
3. Kategorie `Vířivky` a `Celoroční bazény` 335 × 221, radius 40, gradient 175°, správné šipky.
4. Intro text a služby bez ořezávání při delším admin textu.
5. Showroom mobile přeskládat podle Figmy: obrazový cluster, tmavá karta, `280 m²`, CTA.
6. Reference carousel podle Figmy, ne podle Baspa listingu.
7. CTA kontakt a footer navázat na globální komponenty.

### P5: produktové a kategoriové stránky

1. Category hero: tlačítko musí být vždy viditelné a layout nesmí odtékat pod fold.
2. Produktové náhledy: sjednotit crop/object-fit podle Arctic karet.
3. Produkt detail: konfigurace, standardní/volitelná výbava, výhody, skořepiny, kabinet, ceny/katalog CTA.
4. Vše navázat na WP admin data, ne na statické fallbacky.

### P6: obsahové stránky

1. `Další informace` musí existovat jako samostatná stránka/rozcestník podle Figmy.
2. `Kolik stojí provoz a údržba` přesunout do Podpora / Časté dotazy.
3. Reference opravit jako plnohodnotnou stránku, ne jen fotku.
4. `O nás` naplnit aktuálními daty a ověřit admin editaci.
5. Showroom: mapa, galerie a Figma showroom layout.
6. Kontakt: správná mapa/lokace, žádné Černé Pole.

### P7: odstranění Baspa CSS z frontendu

1. Přepnout frontend enqueue na `arctic-app.css`.
2. Přestat načítat `dist/css/style.css` na frontendu.
3. Sloučit nebo odstranit přechodné hotfix CSS: `mobile-figma-contract.css`, `homepage-mobile-slider.css`, `product-colors-mobile.css`, `catalog-request.css`, pokud jejich pravidla už žijí v Arctic komponentách.
4. Nechat plugin/admin CSS jen tam, kde opravdu patří.

### P8: testy a akceptace

1. Playwright smoke pro desktop i mobile: home, menu, category vířivky, category bazény, product detail, další informace, reference, showroom, kontakt.
2. Test dlouhých admin textů: žádný useknutý odstavec, žádné zmizelé ikonky, žádný zkolabovaný blok.
3. Screenshoty proti Figma kontraktu pro dostupné frame.
4. Kontrola HTML: frontend nesmí obsahovat link na `dist/css/style.css`.
5. Kontrola CSS: žádná nová plošná `!important` vrstva; výjimky jen v dokumentovaném plugin compat souboru.

## Akceptační kritéria

- Frontend v lokálu běží bez `dist/css/style.css`.
- Mobile homepage, mobile menu a footer odpovídají Figma metadatům, ne vizuálnímu odhadu.
- Desktop není řízený Baspa CSS.
- WP admin editace textů nerozbije sousední bloky.
- Produktové/listingové karty používají jednotný Arctic crop a layout.
- Všechny nové vizuální úpravy jsou lokální, dokud je ručně neschválíme pro produkci.

## Bezprostřední další krok

Po schválení tohoto směru:

1. Vytvořit pracovní kopii `arctic-spas-3` z aktuálního `arctic-spas-2`.
2. Přidat nový `arctic-app.css` entrypoint a lokální přepínač bez produkčního dopadu.
3. Implementovat P1-P2 a ověřit, že web v lokálu běží bez Baspa `style.css`.
4. Teprve potom skládat komponenty podle Figmy, nejdřív header/menu/footer/homepage.
