# Figma tokens and Arctic CSS mapping

Datum: 2026-05-23

Zdroj:

- Wireframe: `Arctic-spas.cz wireframe`, key `puPBNFpuaXpRZR2TINaDvm`
- Grafika: `Arctic-spas.cz grafika`, key `xeOew3dFjDVfjXZrJ09emM`

## Stav

Figma soubory jsou dostupne pres API a jejich hlavni frame jsou popsane v `docs/figma-structure.md`.

Aktualni implementace uz nepouziva jen konzervativni Baspa skin. Homepage, katalog virivek, detail Lunar, Podpora a Kontakt maji prvni Figma visual pass nad Baspa architekturou. Header, hero, promo banner a hlavni kategorie jsou ladene podle frame `HP` a `header`; katalog podle `KATEGORIE`; detail podle `DETAIL KONKRETNIHO PRODUKTU`; support/kontakt podle prislusnych Figma stranek. Cilem zustava drzet Baspa rozmery a doplnovat jen rozdily z finalni grafiky. Tokeny jsou ulozene v:

- `wp-content/themes/arctic/src/less/_tokens.less`
- build vystup `wp-content/themes/arctic/dist/css/arctic.css`

## Soucasne tokeny

| Token | Hodnota | Pouziti |
| --- | --- | --- |
| `--arctic-color-ink` | `#071826` | primarni text, stiny |
| `--arctic-color-navy` | `#0b2437` | horni lista, tmave plochy |
| `--arctic-color-blue` | `#1f78b4` | odkazy, aktivni prvky |
| `--arctic-color-ice` | `#eaf6fb` | jemne sekce a chipy |
| `--arctic-color-snow` | `#ffffff` | svetle plochy |
| `--arctic-color-red` | `#c82032` | CTA, logo akcent |
| `--arctic-color-steel` | `#5e7180` | sekundarni text |
| `--arctic-radius-s` | `0.3125rem` | buttony |
| `--arctic-radius-m` | `0.5rem` | karty, obrazky, downloady |
| `--arctic-shadow-soft` | `0 1rem 2.5rem rgba(7, 24, 38, 0.1)` | konfiguracni karty |

## Mapovani do Baspa promennych

Arctic vrstva prepise jen vybrane Baspa promene:

- `--a--color`
- `--a--color--accent`
- `--a--color--highlight`
- `--a--color--soft`
- button background promene
- link active/accent promene

To drzi technicky zaklad Baspa a soucasne umoznuje skin podle Arctic.

## Implementovane komponenty

- Figma logo z node `1:1835` v `images/logo.svg`; ručně kreslený fallback `images/logo.php` je odstraněný, aby se nikdy nezobrazil nefigmový znak
- header a top bar skin podle Figma HP/header
- CTA button skin
- mobilni navigation trigger layout
- homepage slide/hero pres existujici Baspa `slide` CPT, s Figma hero textem a lokalnim assetem `Arctic Spas 07`
- homepage promo banner `Výprodej skladových vířivek`
- homepage kategorie `Vířivky` a `Celoroční bazény`
- katalogovy hero a intro bloky `Vlastnosti vířivek` / `Záruka`
- produktove serie v katalogu podle `product-series`
- produktovy hero, sticky produktova navigace a konfigurace jako prvni detailova sekce
- produktove konfigurace Prestige/Signature
- barvy akrylu jako chip list
- download listing
- support taby, FAQ karty a servisni formular
- lokalni kontaktni mapa bez externiho embedu
- Arctic footer s navigacnimi sloupci, rychlym kontaktem a krajinnym pozadim

## Dalsi Figma kroky

- Logo už je exportované z Figmy; dál se jen hlídá, že header používá `images/logo.svg` a žádný ruční fallback.
- Vytahnout presne hodnoty fontu a zkontrolovat proti Baspa typografii.
- Dodelat pixelove rozdily ve spacingu z `HP`, `KATEGORIE`, `DETAIL KONKRETNIHO PRODUKTU`, `PODPORA` a `KONTAKT`.
- Porovnat mobilni frame `GM - HP menu` s aktualnim mobile headerem.
- Doplnit tokeny pro finalni produktove karty, tabulky konfiguraci, FAQ a podporu po klientskem vizualnim schvaleni.
