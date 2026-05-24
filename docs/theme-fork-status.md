# Stav forku Baspa theme

Datum: 2026-05-23

## Co vzniklo

Baspa theme bylo zkopirovano z:

- `../../baspa.cz/wp-content/themes/baspa`

Do nove pracovni sablony:

- `../wp-content/themes/arctic`

Referencni slozka `baspa.cz/` zustala netknuta.

## Provedene upravy

- `style.css`
  - Theme Name zmenen na `Arctic Spas`.
  - Theme URI zmenen na `https://www.arctic-spas.cz/`.
  - Text Domain nastaven na `arctic`.
  - Version nastavena na `0.1.0`.
  - Tested up to nastavene na `6.8`.
- `README.md`
  - Popsany ucel forku a pravidlo, ze se pracuje v `arctic-spas-2`, ne v referencni Baspa slozce.
- `inc/updates.php`
  - Externi Baspa update checker vypnut.
  - Puvodni endpoint `updates.pavelrichter.cz` se ve forku nevola.
- `inc/setup.php`
  - Docasne se nacita i legacy textdomain `baspa`, protoze vetsina stavajicich stringu ve forku ho jeste pouziva.

## Co zustava zamerne zachovane

- Puvodni struktura sablon, modulu, vendor knihoven, `dist/`, `parts/`, `templates/`.
- Funkcni prefixy `baspa_*`.
- Vetsina textdomain volani `baspa`.
- Baspa layoutovy a rozmerovy system.

Tyto veci se nemaji prepisovat hromadne pred prvnim spustenim. Nejdri je potreba zprovoznit fork, overit produktovy vertical slice a teprve potom delat cisteni, ktere ma jasny prinos.

## CSS / JS build rozhodnuti

Zvolena varianta: `2-lite`.

Puvodni Baspa `dist/css/style.css` se nebude primo prepisovat kvuli Arctic rebrandu. Zustane jako stabilni zaklad. Arctic upravy budou v samostatne vrstve:

- `src/less/arctic.less`
- `src/less/_tokens.less`
- `src/less/_brand.less`
- `src/less/_components.less`
- build vystup `dist/css/arctic.css`

`dist/css/arctic.css` se ma nacitat po puvodnim `dist/css/style.css`. JS build se zatim nezavadi, dokud nevznikne konkretni potreba noveho chovani.

Toto rozhodnuti chrani Baspa zaklad, zrychluje pilot a soucasne drzi Figma tokeny a Arctic skin na jednom dohledatelnem miste.

## Aktualni technicky stav

- Lokalni Docker WordPress bezi na `http://localhost:8090`.
- Theme `arctic` je aktivni a nacita Arctic CSS vrstvu po puvodnim Baspa CSS.
- Local safety MU plugin blokuje odchozi WP HTTP requesty mimo localhost a vypina maily.
- Google/Fontshare fonty, tracking preconnecty, Smartsupp a mapa se v localu nenacitaji.
- Produktovy model je rozsiren o konfigurace, barvy, `product-kind` a `product-series`.
- `download` CPT a `[arctic-downloads]` jsou hotove.
- Seed zaklada homepage, menu, katalogy, produkty, sirsi sortiment, showroom, podporu, kontakt a 26 PDF.
- Homepage ma prvni Figma visual pass: bily plovouci header panel nad hero fotkou, Figma hero text, promo banner a dve hlavni kategorie `Vířivky` / `Celoroční bazény`.
- Katalog virivek ma prvni Figma visual pass podle frame `KATEGORIE`: hero, intro `Vlastnosti vířivek` / `Záruka`, produktove serie, konfigurator CTA, showroom a proces.
- Detail Timberwolf ma Figma visual pass podle frame `DETAIL KONKRETNIHO PRODUKTU`: hero, produktova navigace, konfigurace, Figma konfigurator banner, barvy, vyhody, volitelna vybava, realizace a CTA.
- `Podpora` a `Kontakt` maji prvni Figma visual pass: svetly non-hero heading, taby/FAQ/download/form u podpory a lokalni mapovy fallback u kontaktu.
- Footer je nahrazen vlastni Arctic strukturou s navigacnimi sloupci, rychlym kontaktem, lokanim obrazkem showroomu a krajinnym pozadim bez block-template vystupu Baspa.
- Redirect MU plugin pokryva aktivni produkty, sirsi sortiment, vyradene modely, hlavni review URL a PDF.
- `npm run css:build`, PHP lint upravenych souboru, `npm run local:safety`, `npm run form:smoke`, `npm run redirect:smoke` a `npm run visual:smoke` prosly.
- Visual smoke kontroluje hlavni URL, zakazane externi requesty prohlizece, horizontalni overflow na desktopu/mobilu a uklada Figma kontrolni screenshoty.

## Dalsi technicke kroky

1. Dodelat presne texty a parametry pro vsechny produktove detaily mimo piloty.
2. Vytvorit editovatelne FAQ/reference z archivniho obsahu.
3. Doresit finalni pravni stranky cookies/GDPR s klientem.
4. Doladit Figma rozdily, ktere jsou skutecne odlisne od Baspa rozmeru.
5. Pred stagingem znovu projet crawl a exportovat finalni redirect mapu.
