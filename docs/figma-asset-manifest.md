# Figma asset manifest

Datum: 2026-05-23  
Grafika: `Arctic-spas.cz grafika` (`xeOew3dFjDVfjXZrJ09emM`)  
Wireframe: `Arctic-spas.cz wireframe` (`puPBNFpuaXpRZR2TINaDvm`)

## Pravidlo

Figma je jediný zdroj pro UX, layout, rozměry, logo, vizuální styl a obrazové plochy navržené ve stránkách. Starý web `arctic-spas.cz` a složka `../Arctic-spas/` se používají jen pro obsah, produktová data, dokumenty, staré URL a produktové fotky v případech, kde Figma konkrétní produktový snímek nedodává.

Každý Figma asset se exportuje podle node ID do:

- `assets-source/figma/export/graphics/`
- `wp-content/uploads/import/figma/`

WordPress seed pak používá soubory z `wp-content/uploads/import/figma/`, ne ručně pojmenované nebo dohledané obrázky bez node ID.

Poznámka k 2026-05-23: Figma API v průběhu práce vrátilo rate limit `429`, proto jsou primární obrazové assety dočasně exportované přímo z lokálně importovaného `.fig` souboru přes Figma `imageRef`. Po uvolnění API limitu se může spustit `npm run figma:export-assets` pro přesný render složených frame výřezů; zdroj ale zůstává Figma, ne starý Arctic web.

Poznámka k homepage passu: malé vektorové prvky, které nebyly dostupné jako samostatný `imageRef`, jsou dočasně vyříznuté z uloženého Figma frame screenshotu `docs/screenshots/figma-hp.png`. Jde pořád o Figma zdroj, ne o grafiku ze starého webu.

Poznámka k auditu 2026-05-24: HP hero má ve Figma grafice jeden skutečný obrazový node `1:15` (`Arctic Spas 07`) a mobilní referenci `1:1974`. Tři tečky a šipky jsou vizuální ovladače návrhu; další hero fotky Lunar/showroom se nesmí doplňovat ze starého obsahu, pokud pro ně ve Figmě není samostatný hero node.

## Struktura šablon podle wireframe

| Šablona | Wireframe node | Použití |
|---|---:|---|
| Homepage | `58:87` | Pořadí sekcí: hero, produktové směry, výhradní prodejce, montáž/podpora/servis, showroom, průběh zakázky, realizace, CTA, footer |
| Kategorie vířivky | `100:1504` | Hero, vlastnosti, záruka, série Custom/Classic/Core, konfigurátor, showroom, průběh, realizace, CTA |
| Detail produktu | `100:662` | Hero/fakta, navigace, konfigurace, barvy, výhody, volitelná výbava, realizace, CTA |
| Podpora | `124:1926` | Rozcestník podpory, FAQ, downloady, servisní formulář, CTA |
| Kontakt | `124:3882` | Kontaktní hero, mapa/showroom, důležité kontakty, fakturační údaje, CTA |
| Vlastnosti | `123:85` | Rozcestník vlastností, 8 karet, CTA, footer |
| Další informace | `123:619` | Rozcestník dalších informací, 10 karet, CTA, footer |
| Služby | `123:468` | Služby, 6 servisních bloků, CTA, footer |
| Certifikáty | `124:823` | Certifikáty, textové sekce a certifikační obrázky, CTA, footer |
| Záruka | `124:1041` | Záruční přehled, tabulka záruk, CTA, footer |
| Kolik stojí údržba | `124:1161` | Obsahová stránka provozních nákladů, CTA, footer |
| Detail vlastnosti FreeHeat | `124:1262` | Hero, diagram, články, související vlastnosti, CTA, footer |
| O nás | `1:945` | Figma profil firmy, tým, kariéra, CTA, footer |
| Reference | `1:1127` | Figma 3x3 reference grid, CTA, footer |
| Servis | `1:1426` | Figma servisní formulář, ceník servisních služeb, CTA, footer |

## Povinné grafické assety z Figmy

| Oblast | Figma node | Rozměr ve Figmě | Export | Použití ve WP |
|---|---:|---:|---|---|
| Header logo | `1:1835` | 148 x 83 | `graphics/logo-arctic-spas-header.svg` | `wp-content/themes/arctic/images/logo.svg` |
| Homepage hero | `1:15` | 1920 x 795 | `graphics/hp-hero-arctic-spas-07.jpg` | hero slide + fallback produktového detailu Timberwolf podle Figma detailu |
| Homepage karta vířivky | `1:33` | 674 x 424 | `graphics/hp-category-virivky.jpg` | karta směru Vířivky |
| Homepage karta swimspa | `1:34` | 674 x 424 | `graphics/hp-category-celorocni-bazeny.png` | karta směru Celoroční bazény |
| Hero promo produkt | `1:254` | 174 x 131 | `graphics/hp-fixed-banner-product.png` | pevný banner z grafiky |
| Kontakt Lukáš Dušek | `1:50` | 58 x 58 | `graphics/contact-lukas-dusek.png` | portrét v HP CTA a rychlém kontaktu |
| Homepage benefit Montáž | `1:72` | 88 x 88 | `graphics/hp-benefit-montaz.png` | ikona benefitu Montáž podle HP frame |
| Homepage benefit Podpora | `1:114` | 83 x 82 | `graphics/hp-benefit-podpora.png` | ikona benefitu Podpora podle HP frame |
| Homepage benefit Servis | `1:82` | 83 x 82 | `graphics/hp-benefit-servis.png` | ikona benefitu Servis podle HP frame |
| Showroom foto 1 | `1:123` | 384 x 210 | `graphics/showroom-1.png` | showroom koláž |
| Showroom foto 2 | `1:124` | 454 x 285 | `graphics/showroom-2.png` | showroom koláž |
| Showroom foto 3 | `1:125` | 334 x 341 | `graphics/showroom-3.png` | showroom koláž |
| Homepage showroom pin | `1:135` | 24 x 24 | `graphics/hp-pin-showroom.png` | ikona adresy v showroom CTA podle HP frame |
| Realizace 1 | `1:179` | 335 x 422 | `graphics/realizace-1.jpg` | realizace / reference |
| Realizace 2 | `1:187` | 335 x 422 | `graphics/realizace-2.jpg` | realizace / reference |
| Realizace 3 | `1:195` | 335 x 422 | `graphics/realizace-3.jpg` | realizace / reference |
| Footer pozadí | `1:210` | 1920 x 1209 | `graphics/footer-background.jpg` | footer background |
| Footer mapa/showroom | `1:242` | 262 x 299 | `graphics/footer-map.png` | footer kontaktní panel |
| Kategorie hero | `1:263` | 1920 x 795 | `graphics/category-hero-virivky.jpg` | hero kategorie |
| Kategorie vlastnosti | `1:273` | 674 x 424 | `graphics/category-vlastnosti.jpg` | sekce Vlastnosti |
| Kategorie záruka | `1:274` | 674 x 424 | `graphics/category-zaruka.jpg` | sekce Záruka |
| Kategorie produktová karta 1 | `1:275` | 335 x 333 | `graphics/category-product-card-1.png` | Figma referenční karta produktu |
| Kategorie produktová karta 2 | `1:280` | 335 x 333 | `graphics/category-product-card-2.png` | Figma referenční karta produktu |
| Kategorie produktová karta 3 | `1:285` | 335 x 333 | `graphics/category-product-card-3.png` | Figma referenční karta produktu |
| Konfigurátor obrázek | `1:409` | 667 x 312 | `graphics/category-configurator.png` | CTA konfigurátoru |
| Detail Timberwolf hero | `1:1462` | 1920 x 795 | `graphics/detail-timberwolf-hero.jpg` | hero detailu Timberwolf |
| Timberwolf Prestige | `1:1472` | 333 x 279 | `graphics/detail-timberwolf-prestige.png` | konfigurace Prestige |
| Timberwolf Signature | `1:1474` | 333 x 279 | `graphics/detail-timberwolf-signature.png` | konfigurace Signature |
| Barva Dakota | `1:1476` | 106 x 106 | `graphics/color-dakota.png` | vzorek akrylu |
| Barva Kalahari | `1:1479` | 106 x 106 | `graphics/color-kalahari.png` | vzorek akrylu |
| Barva Odyssey | `1:1482` | 106 x 106 | `graphics/color-odyssey.png` | vzorek akrylu |
| Barva Platinum Swirl | `1:1485` | 106 x 106 | `graphics/color-platinum-swirl.png` | vzorek akrylu |
| Barva Espresso | `1:1488` | 106 x 106 | `graphics/color-espresso.png` | vzorek akrylu |
| Kabinet cedr | `1:1492` | 106 x 106 | `graphics/cabinet-cedar.png` | vzorek kabinetu |
| Kabinet bezúdržbový | `1:1495` | 106 x 106 | `graphics/cabinet-maintenance-free.png` | vzorek kabinetu |
| Podpora PDF 1 | `1:917` | 56 x 78 | `graphics/support-download-1.png` | ikona/thumbnail downloadu |
| Podpora PDF 2 | `1:918` | 56 x 78 | `graphics/support-download-2.png` | ikona/thumbnail downloadu |
| Podpora PDF 3 | `1:919` | 56 x 78 | `graphics/support-download-3.png` | ikona/thumbnail downloadu |
| Kontakt mapa/showroom | `1:1069` | 3110 x 782 | `graphics/contact-map-showroom.png` | kontaktní mapa/hero |
| Diagram FreeHeat | `1:1327` | Figma imageRef | `graphics/feature-freeheat-diagram.png` | detail vlastnosti FreeHeat |
| Certifikát TUV 1 | `1:716` | Figma imageRef | `graphics/certificate-tuv-1.png` | stránka Certifikáty |
| Certifikát TUV 2 | `1:717` | Figma imageRef | `graphics/certificate-tuv-2.png` | stránka Certifikáty |
| Certifikát TUV 3 | `1:718` | Figma imageRef | `graphics/certificate-tuv-3.png` | stránka Certifikáty |
| Showroom hero | `1:446` | 1920 x 801 | `graphics/showroom-hero-bazeny.jpg` | Figma showroom/O nás obsahový obraz |
| Tým Vladimír | `1:987` | 336 x 335 | `graphics/about-team-vladimir.png` | stránka O nás |
| Tým Lukáš | `1:1003` | 336 x 335 | `graphics/about-team-lukas.png` | stránka O nás |
| Tým Helena | `1:1004` | 336 x 335 | `graphics/about-team-helena.png` | stránka O nás |
| Tým servis | `1:985` | 335 x 335 | `graphics/about-team-service.png` | stránka O nás |
| Mobile logo | `1:1977` | 86 x 48 | `graphics/mobile-logo-arctic-spas.svg` | kontrola mobilního headeru |
| Mobile hero | `1:1974` | 1343 x 556 | `graphics/mobile-hp-hero.jpg` | mobilní hero reference |
| Mobile karta vířivky | `1:2000` | 335 x 221 | `graphics/mobile-category-virivky.jpg` | mobilní karta směru |
| Mobile karta swimspa | `1:2001` | 335 x 221 | `graphics/mobile-category-celorocni-bazeny.png` | mobilní karta směru |

## Dočasné výjimky ze starého Arctic obsahu

Tyto assety nejsou zdroj UX ani grafiky. Smí sloužit pouze jako produktová nebo obsahová data, dokud Figma nemá konkrétní náhradu:

| Asset | Původ | Důvod |
|---|---|---|
| `lunar-main.jpg`, `lunar-corner.png`, `lunar-cover-black.png` | `../Arctic-spas/` | produktové fotky pro obsahový produkt Lunar |
| `orion-main.jpg`, `orion-lifestyle.jpg` | `../Arctic-spas/` | produktové fotky pro obsahový produkt Orion |
| `legacy-products/*.jpg` | `../Arctic-spas/` | produktové fotky pro aktivní modely v katalogu |
| `downloads/*.pdf` | `../Arctic-spas/` | dokumenty ke stažení |
| `other-sortiment/*.jpg` | klientský/obsahový archiv | obsahové fotky širšího sortimentu |

`Timberwolf` je Figma pilot. Jeho hlavní hero a konfigurace se mají brát z Figma node exportů `1:1462`, `1:1472`, `1:1474`. Staré fotky Timberwolf se mohou ponechat jen jako doplňková produktová galerie, ne jako UX náhrada.

## Kontrola před každým commitem

- Nový vizuální asset má node ID v tomto manifestu.
- Exportovaný soubor existuje v `assets-source/figma/export/graphics/`.
- Stejný soubor existuje pro seed ve `wp-content/uploads/import/figma/`.
- Starý Arctic asset není použitý pro hero, layoutovou kartu, banner, footer, showroom, kontaktní mapu ani navigační UX.
- Pokud se použije starý Arctic obrázek, je to produktová nebo obsahová výjimka uvedená výše.
