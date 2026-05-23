# Inventar doplnkovych podkladu od klienta

Datum zpracovani: 2026-05-23  
Zdrojovy archiv: `../../drive-download-20260523T082002Z-3-001.zip`  
Rozbaleno do: `../assets-source/owner-info/drive-download-20260523T082002Z-3-001/`

## Obsah archivu

- `ARCTIC SPAS CZ 2026 - Marketing.docx` - marketingovy a obsahovy brief pro upravy webu.
- `Marketing Round Table_AS Seminar 2026.pdf` - seminarovy/marketingovy podklad, zatim jen ulozeny jako zdroj.
- `Seminar 2026 AI Keynote_FINAL.pdf` - seminarovy/marketingovy podklad, zatim jen ulozeny jako zdroj.
- `Fotografie pro web/` - nove obrazove podklady pro web.

## Fotografie

Souhrn:

- `Fotografie pro web/barva akrylatu/` - 4 soubory, cca 2.84 MB.
- `Fotografie pro web/Fotografie prodejny/` - 49 souboru, cca 211.45 MB.
- `Fotografie pro web/Lunar/` - 5 souboru, cca 33.74 MB.
- `Fotografie pro web/Orion/` - 6 souboru, cca 48.58 MB.

Dulezite obrazove zdroje:

- Nove modely: `Lunar`, `Orion`.
- Lifestyle/detail assety: `Orion_Lifestyle2.jpg`, `Corner_Lunar.png`, `LUNAR-40-Platinum-Swirl-1500.jpg`, `ORION-40-Platinum-Swirl-1500.jpg`.
- Barvy akrylatu: `Acrylic Swatches Dakota.jpg`, `Acrylic Swatches Kalahari.jpg`, `Acrylic Swatches Odyssey.jpg`, `espresso-swatch.jpg`.
- Prodejna/showroom: sada fotek z ledna a unora 2026, vhodne pro `SHOWROOM`, `O NAS`, `KONTAKT`.

## Vytazek z marketingoveho DOCX

Brief rika, ze informace se budou doplnovat a cast uprav ma byt provedena uz na starem webu. Pro novy WordPress web je ale dulezite hlavne toto:

### Rada CORE

- Pridat / aktualizovat model `Lunar`.
  - Vyroba v Kanade.
  - Novy model pro rok 2025.
  - 1 lehatko, 4 sedadla.
  - Prestige: 249 000 Kc, 1 cerpadlo, 20 trysek.
  - Signature: 279 000 Kc, 2 cerpadla, 40 trysek.
  - Rozmer: 212 x 213 x 99 cm.
  - Barvy skorepiny prevzit od Husky: Platinum Swirl, Espresso, Kalahari, Dakota.
- Pridat / aktualizovat model `Orion`.
  - Vyroba v Kanade.
  - Novy model pro rok 2025.
  - 6 sedadel.
  - Prestige: 249 000 Kc, 1 cerpadlo, 20 trysek.
  - Signature: 279 000 Kc, 2 cerpadla, 40 trysek.
  - Rozmer: 212 x 213 x 99 cm.
  - Barvy skorepiny prevzit od Husky: Platinum Swirl, Espresso, Kalahari, Dakota.
- `Husky` je skladem v evropskem skladu.
  - Cena: 209 000 Kc.
  - 5 sedadel.
  - 1 cerpadlo.
  - 20 trysek.
- U rady Core se maji odstranit / nenabizet mimo USA:
  - `Ellesmere`
  - `Aurora`
  - `Orca`
  - `Grizzly`

### Rady Classic / Custom / AWP

- Produktove beze zmeny.
- Je potreba doplnit konfigurator produktu Arctic Spas.

### Sekce Vlastnosti

- Inovovat informace a obrazky u vyhod virivek Arctic, hlavne tepelna izolace.
- Doplnit fotky z realizaci.
- Zaruky prevzit.
- Bezpecnostni certifikaty prevzit.

### Sekce Podpora

- Stavebni pripravenost prevzit.
- Ke stazeni prevzit a doplnit Smart PH.
- Uprava vody prevest.
- Nahradni filtry aktualizovat: rada Custom ProFilter + odkaz na e-shop.
- Casto kladene otazky prevzit.
- Odkazy prevzit.
- Reference doplnit o aktualni zkusenosti zakazniku.
- Sluzby aktualizovat, vcetne oceneni a roku oceneni Arctic Spas.
- Prislusenstvi e-shop prevzit.
- Prihlaseni `myarcticspa.com` prevzit.

### Dalsi sortiment

- Nabytek IKONO aktualizovat.
- Prislusenstvi a doplnky aktualizovat.
- Automaticky kryt Covana aktualizovat.
- Virivky Dreammaker vyradit.
- Luxusni sauny prevzit.
- Koupaci sudy prevzit.
- Ochlazovaci bazenek pridat.

Rozhodnuti pro datovy model: sortiment je sirsi nez jen virivky/swimspa. Novy web proto nema pocitat s `product` pouze jako s virivkou, ale jako s obecnym katalogovym produktem s taxonomii typu produktu a polem typu prezentace.

Doporucene typy produktu:

- `virivky`
- `swimspa`
- `sauny`
- `koupaci-sudy`
- `ochlazovaci-bazenky`
- `automaticke-kryty`
- `nabytek`
- `prislusenstvi`

Doporucene typy prezentace:

- `full_detail` - vlastni produktovy detail,
- `landing_section` - jen obsahova sekce / landing page,
- `external_shop` - hlavni CTA vede na e-shop nebo externi URL,
- `hidden_or_retired` - nemigrovat jako aktivni produkt, pouzit jen pro redirect/archivni rozhodnuti.

## Kontrola proti live webu

Overeno 2026-05-23 na `https://www.arctic-spas.cz/`:

- Core navigace uz obsahuje `Lunar`, `Orion`, `Husky`.
- Stare Core modely `Ellesmere`, `Aurora`, `Orca`, `Grizzly` nejsou v hlavnim menu.
- Stare URL techto modelu jsou ale porad v sitemapu a vraci HTTP `200`.
- `virivky-dreammaker.php` je porad dostupna stranka a Dreammaker je porad v menu `Dalsi sortiment`.

Zaver: live web je aktualnejsi nez lokalni archiv, ale neni to cisty finalni stav. Pro migraci plati: obsah cerpat z live webu, ale pozadavky na vyrazeni a aktualizace brat z briefu. Dreammaker nemigrovat jako aktivni produkt, jen rozhodnout redirect nebo archivni zachyceni.

### O nas

- O firme BASPA s.r.o. aktualizovat, vcetne novych zamestnancu a novejsich zmen.
- Nase sluzby prevzit a aktualizovat.
- Vzorkova prodejna aktualizovat.
- Arctic jako prvni prevzit a doplnit Smart pH 2025.
- Kolik stoji provoz a udrzba virivky prevzit, upravit kategorii Core: ponechat Husky, pridat Lunar a Orion.
- Kariera prevzit.

### Kontakt

- Akcni nabidku aktualizovat a doplnit akce pri nakupu pro rok 2026.
- Servis virivek prevzit.
- Kontaktni informace prevzit, zvazit doplneni odpovedne osoby.
- Smazat stare fotografie prodejny.

## Dopad na dalsi postup

- Tento archiv je dalsi zdroj pravdy vedle live webu a Figma souboru.
- U produktoveho modelu musi pilot zahrnout minimalne `Lunar`, `Orion` a `Husky`.
- Druhy kontrolni pilot ma overit produkt mimo virivky, napr. `Covana` nebo `IKONO`, aby se nerozbil sirsi katalogovy model.
- Import obsahu ze stareho webu nesmi byt slepy: nektere modely a sekce se maji odstranit nebo aktualizovat podle tohoto briefu.
- Fotografie z archivu jsou kandidat pro novou media knihovnu, ale pred importem do WordPressu je potreba vybrat finalni kusy, prejmenovat je SEO-friendly a zmensit/optimalizovat.
