# Hloubkovy audit: detail virivky/bazeny vs. Figma

Datum: 2026-05-31
Scope: product detail stranky, hlavne Lunar, Orion, Timberwolf, sekce Vyhody, Volitelna vybava a konfiguracni karty.
Lokalni web: `http://localhost:8090`
Figma grafika: frame `DETAIL KONKRETNIHO PRODUKTU`, node `1:1461`

## TL;DR

Tento audit neni implementacni commit. Je to evidence toho, co jeste neni vyresene na produktovem detailu.

Hlavni problem: produktovy detail porad pouziva nahrazky tam, kde Figma pocita se skutecnymi vizualnimi assety. Benefit/option ikony nejsou vyexportovane z Figmy a cast obsahu je hardcoded v sablonach misto dat z WordPressu.

## 1. Benefit a option ikony

### Co rika Figma

Audit `docs/brutal-figma-audit-2026-05-29.md` oznacuje produktove benefity jako rozdil:

- Figma benefit cards maji realne thumbnail/photo-like media.
- Local pouziva sede kruhove placeholdery/ikony.
- Rytmus a media obsah nesedi.

Audit `docs/deep-figma-physical-audit-2026-05-30-v2.md` stejne uvadi, ze benefit cards a product options pouzivaji CSS/generovane ikonove tvary misto Figma assetu.

### Co je v kodu

`templates/section/product-benefits.php` renderuje prazdny span, ne image asset:

```php
<span class="f-product-benefit__media f-product-benefit__media--shell" aria-hidden="true"></span>
```

`src/less/_components.less` z toho dela vizual pres CSS radial gradient. Vysledek je jedna genericka seda kulicka pro vice ruznych benefitu/options.

### Proc je to problem

- Neni to Figma asset.
- Neni to odlisene podle konkretniho benefitu.
- Nelze to povazovat za finalni stav.
- Pokud assety chybi, ma to byt oznacene jako `WAITING_ON_OWNER` nebo `WAITING_ON_FIGMA_EXPORT`, ne nahrazene vlastni grafikou.

## 2. Chybejici asset manifest pro produktovy detail

`docs/figma-asset-manifest.md` pokryva hlavne homepage/kategorie, ale neobsahuje detailni seznam benefit/option ikon pro produktovy detail.

Chybi minimalne:

- Figma node pro kazdou benefit ikonu.
- Export cesta do repozitare.
- Vazba na PHP sablonu nebo datovy model.
- Pravidlo, co se stane, kdyz asset neexistuje.

Doporuceni: doplnit produktovy detail do manifestu, jinak se bude stejna chyba opakovat.

## 3. Hardcoded obsah benefitu

Benefit sekce je z velke casti napevno v PHP array. To znamena:

- klient to neupravi ve WordPress administraci,
- ruzne rady/modely mohou dostavat stejny seznam,
- text typu Serie Classic muze prosvitit i na produktu, ktery patri do jine rady,
- nejde jednoduse resit rozdily mezi virivkami a swimspa.

Doporuceni: rozhodnout, jestli budou benefity:

1. per produkt pres Meta Box/repeater,
2. per serie pres taxonomy/meta,
3. nebo staticky seedovane, ale s jasnym mapovanim na product category/series.

## 4. Konfiguracni karty bez obrazku

`modules/products/templates/post/single/configurations.php` podporuje image ID, ale pokud seed data nemaji `image_id`, karta vykresli prazdny bily thumb box.

Doporuceni:

- doplnit seed data pro konfigurace, hlavne Lunar/Orion/Husky,
- pokud obrazek chybi, renderovat explicitni missing stav nebo kartu bez thumb slotu,
- neukazovat prazdny bily ramecek, ktery vypada jako rozbity image.

## 5. Figma detail layout jen pro Timberwolf

`single-product.php` historicky pouzival specialni vetev pro Timberwolf a jine produkty sly jinou sablonou.

Doporuceni:

- rozsireni Figma detail layoutu nesmi byt jen Timberwolf hack,
- vsechny produkty, ktere maji konfigurace/detailni obsah, maji pouzit stejny product detail contract,
- pokud nektery produkt nema data, ma dostat missing/pending stav, ne rozbitou alternativni vetev.

## 6. Kodovani PHP souboru

V prubehu prace se objevilo riziko mojibake ve `figma-detail-body.php` a souvisejicich sablonach. Aktualni pracovni soubory se musi pred commitem kontrolovat na:

- UTF-8 bez BOM,
- zadne mojibake sekvence typu `U+00C3`, `U+00C4`, `U+00C5`, ani replacement char,
- spravne ceske texty v PHP stringach.

Doporucene guardy:

```bash
rg -n "U+00C3|U+00C4|U+00C5|replacement-char" wp-content/themes/arctic/**/*.php
php -l path/to/file.php
```

## 7. Swatch Kalahari v 3D konfigurátoru

V konfigurátoru na `/konfigurator/orion/` se swatch Kalahari zobrazuje jako prázdný bílý kroužek.

### Příčina

Image existuje (`https://api.arcticspascore.com//apps//visao//images//icons//68d3a664c6a2d.png`) a načítá se správně. Problém není v datech ani v kódu — **Kalahari je světle šedý/bílý granit** a na bílém pozadí karty splyne.

Lokální referenční soubor (`wp-content/uploads/import/owner-swatches/acrylic-kalahari.jpg`) barvu potvrzuje: světle šedá s jemnými tmavými skvrnami, prakticky bílá v malém formátu.

Visao plugin renderuje swatch takto ([functions.php:433](../wp-content/plugins/visao-3d-viewer/functions.php)):
```php
$html .= "<li><img id='$id' class='clickable-image' src='$icon_url'>";
```

`.clickable-image` v `visao-styles.css` má všechny vizuální vlastnosti zakomentované — žádný border, žádný shadow. Světlé swatche proto nemají viditelný obrys.

### Oprava

Vendor plugin neupravovat (přepisuje se při updatech). Přidat do `arctic.css`:

```css
.f-jucra-builder__layout .clickable-image {
    border-radius: 50%;
    box-shadow: inset 0 0 0 1px rgba(7, 24, 38, 0.15);
}
```

Dá všem světlým swatchům (Kalahari, Platinum Swirl) jemný kruhový border bez zásahu do pluginu.

---

## 8. Prehled rozdilu

| Oblast | Figma / cil | Aktualni problem |
| --- | --- | --- |
| Benefit ikony | realne thumbnail/photo-like media | CSS radial-gradient placeholder |
| Option ikony | rozlisene vizualy | genericke kruhove nahrady |
| Benefit obsah | relevantni pro produkt/serii | cast obsahu hardcoded |
| Konfigurace | karta ma obrazek nebo jasny stav | prazdny thumb pri chybejicim image ID |
| Detail layout | sdileny product detail contract | historicky Timberwolf-only pattern |
| Asset manifest | presne nody/exporty | produktovy detail chybi |
| Kalahari swatch | viditelny kruhovy swatch | svetla barva splyne s bilym pozadim |

## 9. Prioritni opravy

### P0

- Udrzet vsechny dotcene PHP soubory jako UTF-8 bez BOM.
- Nepoustet do gitu mojibake.
- Sjednotit product detail layout tak, aby nebyl Timberwolf-only hack.

### P1

- Vyexportovat benefit/option assety z Figmy.
- Dopsat je do `docs/figma-asset-manifest.md`.
- Nahradit CSS placeholdery skutecnymi image/SVG assety nebo oznacit chybejici asset jako waiting stav.
- Doplnit seed obrazky pro konfiguracni karty.

### P2

- Rozhodnout finalni datovy model benefitu/options.
- Umoznit editaci pres WordPress data tam, kde se obsah lisi per model nebo per serie.
- Pridat smoke/visual guard, ktery detekuje prazdne thumb boxy a genericke placeholder ikony.

## 10. Stav po tomto auditu

Tento soubor pouze dokumentuje dluh. Neopravuje produktovy detail. Slouzi jako backlog pro dalsi repair blok, aby se uz nevytvarely dalsi vlastni CSS nahrady misto Figma assetu.