# Hloubkový audit WP administrace Arctic vs BASPA

Datum: 2026-06-02

Rozsah: Arctic lokál `arctic-spas-2` proti BASPA lokálu `baspa.cz`, se zaměřením na to, jestli klient může obsah spravovat ve WordPress administraci bez zásahu do šablon.

## Verdikt

Arctic má dobrý technický základ: existují CPT moduly pro produkty, reference, FAQ, downloady, členy týmu, kariéru, kontakty, slidery a podpůrný obsah. Problém je, že velká část Figma implementace tyto moduly obchází a ukládá obsah přímo do PHP šablon jako statická pole, fallbacky nebo pevné URL.

To znamená, že web teď vypadá jako adminovatelný jen částečně. Klient by sice mohl upravovat produkty, FAQ, reference a downloady, ale u mnoha stránek by narazil na obsah, který v adminu nenajde: footer skupiny, kontaktní adresář, showroom texty/fotky, služby, certifikáty, záruky, vlastnosti, část produktového detailu, CTA bloky, mapové/quick contact assety a část menu logiky.

Největší riziko není jednotlivá chyba vzhledu. Největší riziko je zdvojený zdroj pravdy:

1. WordPress má CPT/moduly/menu.
2. Arctic šablony mají vlastní pevná PHP pole.
3. Figma fallback assety se tváří jako produkční data.
4. Menu má ruční custom URL místo vazby na WP objekty.

Dokud se to nesjednotí, klient nebude mít jistotu, že změna v adminu se projeví globálně.

## Lokální Datový Stav

Publikovaný obsah v Arctic lokálu:

| Typ | Počet | Stav |
| --- | ---: | --- |
| `page` | 18 | existuje |
| `product` | 28 | existuje, hlavní produktový zdroj |
| `download` | 26 | existuje |
| `reference` | 9 | existuje |
| `faq` | 9 | existuje |
| `slide` | 1 | existuje, homepage slider je použitelný |
| `member` | 0 | modul existuje, ale lokál je prázdný |
| `job` | 0 | modul existuje, ale lokál je prázdný |
| `support` | 0 | modul existuje, ale lokál je prázdný |
| `accessory` | 0 | modul existuje, ale lokál je prázdný |
| `partner` | 0 | modul existuje, ale lokál je prázdný |
| `offer` | 0 | modul existuje, ale lokál je prázdný |

Menu lokace existují:

| Lokace | Stav |
| --- | --- |
| `navigation` | přiřazeno |
| `navigation_bar` | přiřazeno |
| `navigation_footer` | přiřazeno |
| `navigation_pools` | existuje, lokálně nepřiřazeno |
| `navigation_jacuzzis` | existuje, lokálně nepřiřazeno |
| `navigation_information` | existuje, lokálně nepřiřazeno |

Hlavní menu a footer menu jsou v lokálu uložené jako `custom` URL položky, ne jako objektové vazby na stránky, produkty nebo taxonomie. To je P0 problém pro globální správu.

## Srovnání S BASPA

BASPA je administrátorsky čistší v tom, že více spoléhá na WP obsah:

| BASPA | Arctic |
| --- | --- |
| `template-about.php` používá `get_the_content()`, `modules/members/templates/section`, `modules/jobs/templates/section` | `template-about.php` má vlastní Figma layout, fallback tým, statické intro a statické statistiky |
| `template-showroom.php` používá primárně page content a recent references | `template-showroom.php` má vlastní statická pole pro hero, důvody, copy bloky a fotky |
| Footer BASPA používá block footer + copyright/footer nav | Arctic footer má pevné `$groups`, quick contact a map blok přímo v PHP |
| Členové týmu a kariéra v BASPA jsou čistě CPT + admin options | Arctic už používá CPT, ale pokud jsou prázdné, produkčně padá do Figma fallbacků |
| Slider je CPT `slide` s metaboxem a featured image | Arctic homepage slider to používá, ale interní hero/CTA sekce jsou místy pevné |

Závěr: Arctic by měl zachovat Figma vzhled, ale vrátit datový model blíž BASPA: šablony pouze renderují layout, data jdou z adminu.

## P0 Nálezy

### P0-1: Hlavní Menu A Footer Menu Jsou Custom URL

Evidence:

- `Arctic hlavni navigace` má položky jako `Vířivky`, `Série Core`, `O nás`, `Showroom`, `Kontakt` uložené jako `type=custom`, `object=custom`.
- `Arctic paticka` má také všechny položky jako `type=custom`.
- Lokace `navigation_pools`, `navigation_jacuzzis`, `navigation_information` existují, ale lokálně nejsou přiřazené.

Dopad:

- Když klient smaže stránku, menu položka zůstane jako mrtvá URL.
- Když se přejmenuje stránka/slug, menu se nerozbije viditelně hned, ale odkaz zůstane starý.
- Když se smaže produkt, produktové listingy zmizí, ale menu/mega menu nebo anchor odkazy mohou zůstat mimo datový model.

Cílový stav:

- Stránky v menu musí být WP `post_type=page` položky.
- Produktové kategorie/série mají být taxonomy položky nebo dynamicky generované z taxonomií.
- Custom URL používat jen pro externí odkazy a interní anchor odkazy, které nelze udělat jako WP objekt.
- Přidat guard test: žádná hlavní stránková menu položka nesmí být `custom`, pokud existuje odpovídající stránka nebo taxonomie.

### P0-2: Mega Menu Je Zdvojené Vůči WP Menu

Evidence:

- `inc/mega-menu.php` definuje pevné panely `hot-tubs` a `swimspa`.
- Produkty uvnitř panelu se dotahují dynamicky z `product` CPT a kategorií, což je správně.
- Promo karta v `templates/navigation/mega.php` má pevný image asset, texty a URL.

Dopad:

- WP menu je jeden zdroj pravdy, mega menu druhý.
- Klient může upravit menu v adminu, ale mega panel se nemusí chovat podle toho.
- Promo v mega menu není admin editovatelné.

Cílový stav:

- Mega panel se má odvozovat z WP menu položky nebo z page metaboxu `page_product_category`.
- Promo karta má být theme option / menu item meta / admin repeater.
- Produkty v panelu zůstanou dynamické.
- Když produkt přestane být publish, zmizí z panelu automaticky.

### P0-3: Footer Je Téměř Celý Natvrdo

Evidence:

- `templates/footer.php` obsahuje pevné `$groups` s odkazy.
- Quick contact má pevný obrázek `contact-lukas-dusek.png`, pevné jméno/roli a pevnou adresu v map bloku.
- Copyright text je pevně napsaný jako BASPA 2024.
- Přitom existuje `navigation_footer` a BASPA footer používá WP/block/template část.

Dopad:

- Klient neupraví footer strukturu přes admin.
- Footer se může rozjet oproti hlavnímu menu.
- Změna člověka/kontaktu/adresy vyžaduje zásah do šablony, i když theme options už částečně existují.

Cílový stav:

- Footer linky renderovat z `navigation_footer` nebo rozdělených lokací `navigation_jacuzzis`, `navigation_pools`, `navigation_information`.
- Quick contact vybírat z `member` CPT nebo global contact settings.
- Footer map/adresa z global company settings.
- Copyright z `get_bloginfo()` + aktuální rok + volitelný option text.

### P0-4: Statické Obsahové Šablony Obcházejí WP Editor

Nejrizikovější soubory:

| Soubor | Problém |
| --- | --- |
| `template-showroom.php` | hero, texty, důvody návštěvy, galerie a fotky jsou převážně v PHP |
| `template-services.php` | služby jsou celé PHP pole `$services` |
| `template-certificates.php` | certifikáty a texty jsou pevně v PHP |
| `template-warranty.php` | warranty matrix a poznámka jsou pevně v PHP |
| `template-maintenance.php` | dlouhý článek je celé PHP pole `$sections` |
| `template-features.php` | seznam vlastností, texty, URL a image jsou pevně v PHP |
| `template-feature-detail.php` | detail vlastnosti je pevný mapping v PHP |

Dopad:

- Klient neumí upravit text, pořadí, fotku, tlačítko ani počet bloků.
- Každá změna obsahu je vývojářský zásah.
- Obsah se nedá verzovat ani spravovat běžným WP workflow.

Cílový stav:

- Figma layout zůstane jako šablona.
- Obsah se přesune do adminu jednou z těchto cest:
  - Gutenberg/page content s pevnými patterny.
  - Meta Box group fields na stránce.
  - CPT moduly pro opakované entity.
- Fallbacky mohou existovat pouze jako seed/dev data, ne jako produkční zdroj.

### P0-5: Produktový Detail Je Jen Částečně Adminovatelný

Co je dobře:

- `product` CPT existuje.
- Produktové parametry, cena, popis, krátký popis, featured/product images a konfigurace mají metabox.
- Produktový detail čte data z produktu.
- Product nav je částečně dynamická podle dostupných sekcí.

Problémy:

| Soubor | Problém |
| --- | --- |
| `modules/products/templates/post/single/acrylic-colors.php` | fallback barvy a asset map jsou pevně v PHP |
| `modules/products/templates/post/single/configurations.php` | konfigurace mají fallback Figma obrázky |
| `modules/products/templates/post/single/figma-detail-body.php` | CTA konfigurátoru má pevný image/title/text |
| `templates/section/product-benefits.php` | benefity jsou pevné PHP pole |
| `templates/section/product-options.php` | volitelná výbava je pevné PHP pole |
| `modules/products/templates/post/single/sidebar.php` | kontaktní osoba/avatar jsou pevné |

Dopad:

- Barvy skořepiny/kabinetu nejsou globální katalog spravovaný klientem.
- Pokud se změní dostupná barva, klient musí upravit každý produkt ručně nebo narazí na PHP fallback.
- Benefity a volitelná výbava nejsou řízené podle série/modelu/kategorie v adminu.
- Produktový detail není plně „produkt jako zdroj pravdy“.

Cílový stav:

- Vytvořit globální katalog barev jako taxonomy/CPT/options: název, slug, typ, obrázek, fallback color, pořadí, aktivní/neaktivní.
- Produkt vybírá barvy z katalogu, ne volným textem.
- Benefity a volitelná výbava mají být buď globální CPT/taxonomy, nebo product/series repeater.
- Sidebar kontakt vybírat z `member` CPT nebo global sales contact setting.
- Žádný produkční fallback obrázek nesmí předstírat reálnou produktovou fotku.

### P0-6: Kontakty Jsou Rozdělené Do Více Zdrojů

Evidence:

- `templates/section/contact-directory.php` má pevné pole kontaktů.
- `template-about.php` používá `member` CPT nebo fallback team.
- `templates/footer.php`, `template-support.php`, product sidebar a quick contact používají vlastní pevný kontakt na Duška.
- Theme mods drží email/phone/hours, ale ne kompletní osobu.

Dopad:

- Jedna osoba se může změnit na pěti místech.
- Klient neumí přidat/odebrat tým a mít změnu globálně.
- Fotky a role lidí nejsou jednotně spravované.

Cílový stav:

- Jeden zdroj: `member` CPT.
- Člen má pole: pozice, působnost, email, telefon, fotka, viditelnost v týmu, viditelnost v kontaktech, primární sales/support/showroom kontakt.
- Footer/support/sidebar/contact directory vybírají osobu z `member` CPT.
- Když klient přidá 11 členů, carousel/listing se přizpůsobí automaticky.

### P0-7: O Nás Je Částečně Adminovatelná, Ale Intro A Statistiky Jsou Natvrdo

Co je dobře:

- `member` CPT existuje.
- `job` CPT existuje.
- Team title/subtitle a jobs title/subtitle mají options.
- Nové guardy mají rozlišovat admin data vs Figma fallback.

Problémy:

- Úvod „Naše společnost“ je pevný text v `template-about.php`.
- Statistiky `21+`, `1000+`, `11` jsou pevné.
- Fallback team má Figma exporty a pevná data.
- Fallback kariéra má pevné job položky.

Dopad:

- Klient neumí upravit claimy, čísla ani intro z adminu.
- Pokud nepřidá tým, produkce ukazuje Figma fallback, který nemusí být realita.

Cílový stav:

- Intro sekce přes page content nebo about settings.
- Statistiky jako admin repeater: hodnota, popisek, pořadí.
- Fallback zobrazovat pouze v dev/admin režimu nebo s jasným placeholder statusem, ne jako produkční obsah.

### P0-8: Homepage Má Mix Admin A Hardcoded Sekcí

Co je dobře:

- `template-homepage.php` používá `modules/slides/templates/section`.
- `slide` CPT má metabox na button text a URL vazbu na produkt/kategorii/custom URL.
- Homepage používá `templates/content`.

Problémy:

- `templates/section/benefits.php`, `templates/section/showroom.php`, `templates/section/progress.php`, `templates/section/hero-promo.php` mají pevné nebo jen částečně option-driven bloky.
- Promo image je pevný Figma asset.
- Progress kroky jsou pevné PHP pole.

Dopad:

- Klient může změnit hlavní slide, ale ne všechny homepage sekce.
- Homepage není kompletně editovatelná jako BASPA-style modulární stránka.

Cílový stav:

- Každá homepage sekce má admin zdroj: page blocks, Meta Box groups nebo CPT.
- Promo/banner přes options s image uploadem.
- Progress kroky přes repeater nebo CPT.

### P0-9: Podpora A Ke Stažení Jsou Jen Částečně Sjednocené

Co je dobře:

- `faq` CPT existuje a lokálně má 9 položek.
- `download` CPT existuje a lokálně má 26 položek.
- Support/download settings existují.

Problémy:

- `template-support.php` má fallback FAQ otázky přímo v PHP.
- Download filter keys `catalog`, `manual`, `dimensions`, `warranty` jsou pevné v šabloně.
- `support` CPT existuje, ale lokálně je prázdný a část support logiky se řeší přes options/fallback.
- Support help avatar je pevný Figma asset.

Dopad:

- Klient může upravovat FAQ/download položky, ale ne plně logiku filtrů.
- Když přidá novou download kategorii, nemusí se objevit jako filtr bez změny kódu.

Cílový stav:

- Filtry generovat z `download-category` taxonomie.
- FAQ filtry generovat z `faq-category`.
- Support cards/help/contact vybírat z `member` nebo global settings.
- Fallback FAQ odstranit z produkční cesty.

### P0-10: Reference Mají Admin CPT, Ale Produkční Placeholdery

Evidence:

- `template-references.php` queryuje `reference` CPT.
- Pokud není dost položek, doplňuje placeholder karty do pevného počtu 9.
- Fallback image/texty jsou pevné.

Dopad:

- Produkce může ukazovat „ukázkové“ reference, pokud administrace není kompletní.
- Klient nemá plnou kontrolu nad tím, kolik referencí se zobrazí.

Cílový stav:

- Zobrazit pouze publikované reference.
- Počet/sloupcování řešit layoutem, ne placeholdery.
- Empty state zobrazit jen adminům nebo v dev režimu.

## P1 Nálezy

### P1-1: Formuláře Mají Statické Labely A Texty

Kontaktní a servisní formuláře ukládají submissions dobře do `contact` CPT, ale samotné popisky, placeholders, typy poptávky a texty jsou převážně v šablonách.

Cílový stav:

- Form labels/placeholders/default subject přes settings nebo translation-ready admin options.
- Typy poptávky jako adminovatelný seznam.
- Ecomail/GTM/API zůstanou technická konfigurace, ne klientský obsah.

### P1-2: Figma A Owner Assety Jsou Používané Jako Produkční Data

Příklady:

- `content_url('uploads/import/figma/...')`
- `owner-showroom/...`
- `legacy-services/...`
- `WAITING_ON_OWNER` placeholdery

Cílový stav:

- Media Library attachment jako primární zdroj.
- Figma/owner import jen seed nebo fallback pro dev.
- Automatické generování velikostí přes WP image sizes.
- Klient nemá ručně zmenšovat fotky; upload do Media Library má vytvořit potřebné velikosti a šablona má řešit crop/object-position.

### P1-3: Interní Hero Fotky A Slidy Nemají Jednotnou Strategii

Výchozí heading bere `featured image`, `page_description_text`, `page_button_*`, což je správně. Problém je, že některé Figma šablony to obejdou a skládají vlastní hero/CTA.

Cílový stav:

- Stránky: featured image + page meta.
- Homepage: `slide` CPT.
- Pokud klient chce slider i na jiné stránce, přidat page-targeted slide relation.
- Hero texty/fotky se nesmí ztratit v pevné šabloně.

### P1-4: Seed Data A Produkční Fallback Nejsou Oddělené

Soubor `tools/seed-pilot-content.php` umí seedovat obsah, což je správně pro lokál. Některé šablony ale seed/fallback logiku drží přímo v renderu.

Cílový stav:

- Seed patří do seed skriptu.
- Šablona má renderovat data.
- Pokud data chybí, produkce má zobrazit prázdný/bezpečný stav, ne smyšlený obsah.

## Audit Po Stránkách A Sekcích

| Oblast | Aktuální zdroj | Problém | Priorita |
| --- | --- | --- | --- |
| Header top bar | theme mods/hours | OK základ, ale contact person není jednotná entita | P1 |
| Hlavní menu | WP menu, ale položky jsou custom URL | není globální vazba na stránky/taxonomie | P0 |
| Mega menu | PHP definice + dynamické produkty | zdvojené vůči WP menu, pevné promo | P0 |
| Footer | pevné PHP `$groups` + quick contact | není adminovatelný jako menu/footer content | P0 |
| Homepage slider | `slide` CPT | dobrý základ, jen 1 slide v lokálu | P1 |
| Homepage benefity/showroom/progress | PHP sekce | klient neupraví obsah/pořadí/fotky | P0 |
| Kategorie vířivek/swimspa | produkty/taxonomie + hardcoded intro | produkt listing OK, intro/CTA tvrdé | P0 |
| Detail produktu | product CPT + metaboxy | benefity, barvy, CTA a kontakt částečně tvrdé | P0 |
| Konfigurátor | Jucra mapping + product model | model list má tvrdý mapping v `jucra.php`, nutná admin kontrola | P1 |
| Poptávka konfigurace | form + query data | form labels/layout static, submissions OK | P1 |
| O nás | page + member/job CPT + fallback | intro/statistiky/fallbacky tvrdé | P0 |
| Kariéra | `job` CPT + options | dobrý směr, fallbacky musí pryč z produkce | P1 |
| Kontakt | map/options + hardcoded contact directory | kontakty a fakturační údaje nejsou sjednocené | P0 |
| Podpora | FAQ/download CPT + options | fallback FAQ, hardcoded filter keys, hardcoded contact avatar | P0 |
| Ke stažení | `download` CPT + shortcode | filtry nejsou plně z taxonomie | P1 |
| Reference | `reference` CPT | placeholdery místo čistého admin obsahu | P1 |
| Showroom | theme mods + hardcoded sections | většina stránky není adminovatelná | P0 |
| Služby | PHP pole | kompletně mimo admin | P0 |
| Vlastnosti | PHP pole | kompletně mimo admin | P0 |
| Detail vlastnosti | PHP mapping | mimo admin | P0 |
| Certifikáty | PHP pole + Figma assety | mimo admin | P0 |
| Záruka | PHP warranty matrix | mimo admin | P0 |
| Provoz a údržba | PHP article sections | mimo WP editor | P0 |
| Ceník/servis pages | support module/page content | lepší, ale ověřit bez fallbacků | P1 |

## Doporučená Architektura

### Jednotná Priorita Zdroje Dat

Každá šablona by měla dodržet:

1. Admin data z CPT/metabox/options/taxonomy.
2. Page content přes Gutenberg/editor.
3. Dev-only fallback pro lokální seed nebo admin preview.
4. Nikdy produkční Figma placeholder jako reálný obsah.

### Doporučené Moduly

| Obsah | Doporučený zdroj |
| --- | --- |
| Produkty | `product` CPT + taxonomie + metaboxy |
| Produktové barvy | nový globální color catalog jako CPT/taxonomy/options |
| Produktové benefity | nový CPT/repeater, přiřazení podle produktu/série/kategorie |
| Volitelná výbava | CPT/repeater, přiřazení podle produktu/série/kategorie |
| Členové týmu | `member` CPT |
| Kontakty napříč webem | `member` CPT + role flags |
| Kariéra | `job` CPT |
| FAQ | `faq` CPT + `faq-category` |
| Downloady | `download` CPT + `download-category` |
| Reference | `reference` CPT |
| Homepage slidy | `slide` CPT |
| Showroom | page content + page metabox/repeater |
| Služby | CPT `service` nebo page repeater |
| Vlastnosti | CPT `feature` nebo page repeater |
| Certifikáty | CPT/repeater s attachmenty |
| Záruky | repeater/matrix admin nastavení |
| Footer | WP menu locations + global company/contact settings |

## Implementační Plán

### Fáze 1: Admin Source Of Truth Guardy

1. Přidat audit script `admin-editability-smoke`.
2. Vypsat všechny template files, které obsahují velká PHP pole s textem.
3. Zakázat produkční fallbacky pro `member`, `job`, `faq`, `reference`.
4. Přidat test na custom menu položky pro interní stránky.
5. Přidat test na `content_url('uploads/import/figma/...')` v produkčních šablonách.

### Fáze 2: Menu, Header, Footer

1. Převést main/footer menu na WP object items.
2. Mega menu navázat na WP menu/page meta.
3. Footer linky renderovat z WP menu lokací.
4. Quick contact vybírat z `member` CPT nebo global option.
5. Header/CTA/settings sjednotit na page meta + global options.

### Fáze 3: Kontakty A Tým

1. Naplnit nebo migrovat `member` CPT.
2. Contact directory přepsat na query `member`.
3. Footer/support/sidebar napojit na vybraného člena.
4. Přidat admin pole pro viditelnost a roli člena.
5. Zachovat carousel pro libovolný počet lidí.

### Fáze 4: Produktový Detail

1. Přidat globální katalog barev.
2. Produkty propojit na katalog barev místo volného textu/fallback mapy.
3. Produktové benefity a options přes admin.
4. Konfigurátor CTA jako global/product option.
5. Odstranit produkční Figma fallback obrázky z detailu.

### Fáze 5: Statické Stránky

1. Showroom převést na page content + repeatery.
2. Služby převést na CPT/repeater.
3. Vlastnosti/detail vlastnosti převést na CPT/repeater.
4. Certifikáty převést na attachment repeater.
5. Záruku převést na admin matrix.
6. Provoz a údržbu převést do page editoru.

### Fáze 6: Support, Downloads, Reference

1. Download filtry generovat z taxonomie.
2. FAQ filtry generovat z taxonomie.
3. Support help card přes `member`.
4. Reference zobrazovat jen publikované položky.
5. Empty states zobrazovat pouze adminům/dev režimu.

## Akceptační Kritéria

### Globální Produkt

- Když klient odpublikuje nebo smaže produkt, zmizí z archivu, mega menu, produktových výpisů a related sekcí.
- Produkt nesmí zůstat dostupný přes ručně napsanou položku v menu.
- Produktový detail nesmí zobrazit fake barvu/fotku, pokud ji admin nepovolil.

### Menu

- Interní stránky v hlavním menu a footeru jsou objektové WP položky.
- Custom URL jsou povolené jen pro externí odkazy a čisté anchor odkazy.
- Mega menu bere produktové položky dynamicky.
- Footer menu se dá měnit ve WP adminu.

### Tým A Kontakty

- Klient přidá 1 až 11+ členů týmu bez zásahu do kódu.
- O nás, kontaktní adresář, footer quick contact, support card a produkt sidebar používají stejný admin zdroj.
- Fotky se nahrávají přes Media Library a šablona použije automatické WP image sizes/crop.

### Stránky

- Klient upraví text/fotku/pořadí bloků na stránkách `Showroom`, `Služby`, `Vlastnosti`, `Certifikáty`, `Záruka`, `Provoz a údržba` bez editace PHP.
- Page hero title/description/button/photo jsou nastavitelné přes existující page meta/featured image.
- Figma layout zůstává, ale texty/asset data nejsou natvrdo.

### Fallbacky

- Produkční web nezobrazuje `WAITING_ON_OWNER`, placeholder reference ani Figma fallback osobu jako reálný obsah.
- Fallbacky jsou jen seed/dev/admin-preview režim.

## Nejbližší Doporučený Postup

1. Nejdřív opravit menu/footer/kontakty, protože to jsou globální zdroje pravdy.
2. Potom produktový detail: barvy, benefity, CTA, sidebar kontakt.
3. Potom převést statické Figma stránky do admin repeaterů/CPT.
4. Nakonec přidat smoke testy, které zabrání návratu hardcoded obsahu.

Tento audit neříká, že Figma layout má pryč. Naopak: Figma layout má zůstat. Musí se ale oddělit prezentace od obsahu tak, aby klient spravoval data ve WordPressu a šablona jen kreslila správný design.
