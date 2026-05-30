# Aktualni plan nedoresenych oprav

Datum: 2026-05-30

Tento dokument nahrazuje volne interpretace starsich auditu pro dalsi praci. Cilem je dodelat veci, ktere jsou porad nedoresene nebo nejsou dostatecne potvrzene po kodu, Figme, wireframe a vizualni kontrole.

## Stav a pravidla

| Pravidlo | Vyklad |
|---|---|
| Neopravovat stejny modul stranku po strance. | Nejdrive usage map, potom canonical template/CSS contract, az potom vizualni oprava. |
| Page-specific CSS je vyjimka. | Stranka smi resit umisteni cele sekce, ne vnitrni vzhled karet, tlacitek, obrazku a overlayu. |
| Wireframe rozhoduje, jestli modul patri na stranku. | Pokud wireframe neukazuje modul pro konkretni stranku, modul je scope decision, ne default. |
| Figma grafika rozhoduje vizual. | Spacing, crop, radius, stiny, barvy, vrstvy a kompozice se meri podle Figmy. |
| Old Arctic a owner data rozhoduji obsah. | Produktove fotky, reference, texty a materialove assety se nesmi vymyslet. |
| Kazda i mala oprava se vizualne overuje. | Ne jen smoke testy. Minimalne screenshot dotceneho modulu vedle Figma reference nebo fyzicka kontrola na lokalnim webu. |
| QA musi hlidat komponentu ve vsech vysketech. | Pokud se opravi homepage, musi stejny guard zkontrolovat i kategorii, product detail, archive nebo footer podle typu komponenty. |

## Aktualni jistoty

| Oblast | Stav |
|---|---|
| Kategorie `virivky` | Wireframe `WF - KATEGORIE` potvrzuje konfigurator a plny category flow pro virivky. |
| Kategorie `swimspa` | Samostatny Figma/wireframe scope neni potvrzeny. Pouziti stejneho category frame je implementacni rozhodnuti, ne dukaz. |
| Swimspa konfigurator | Neni potvrzeny jako povinny modul. Bez scope rozhodnuti ma byt vypnuty nebo oznaceny jako pending decision. |
| Opakovane moduly | Jsou porad rizikove. Cast uz ma contract, ale cast ma stale page-specific CSS nebo nejasne varianty. |
| Dusek avatar | Aktualne opraveno ve worktree pres spravny Figma crop. Neni predmet tohoto backlogu, jen musi projit final QA. |

## PR-0 - Component audit gate

Priorita: P0

Cil: Zastavit dalsi lepeni stejnych modulu po strankach.

| Krok | Akce | Vystup |
|---:|---|---|
| 1 | Udelat aktualni usage map vsech opakovanych modulu. | `docs/component-contract-usage-map-2026-05-30.md` aktualizovany podle aktualniho kodu. |
| 2 | U kazdeho modulu oznacit canonical template, CSS contract a povolene varianty. | Tabulka `module -> template -> CSS -> variants -> pages`. |
| 3 | Najit page-specific CSS, ktere meni vnitrni vzhled komponent. | Seznam selektoru k odstraneni nebo presunu. |
| 4 | Udelat screenshot matrix pro kazdy sdileny modul. | Homepage, `/virivky/`, `/swimspa/`, product detail, archive podle pouziti. |
| 5 | Upravit smoke testy tak, aby nekontrolovaly jen tridy. | Guardy pro obsah, kontext, viditelnost obrazku, overflow a zakazane hardcoded odkazy. |

### Moduly v auditu

| Modul | Aktualni riziko |
|---|---|
| Product series/listing cards | Karty a mezery se resi hlavne pres `.tax-product-category`; hrozi drift mezi virivky a swimspa. |
| Configurator CTA | Renderuje se pro vsechny product-category, i kdyz swimspa scope neni potvrzeny. |
| Showroom panel | Sdileny blok, ale potrebuje realnou kontrolu viditelnosti fotek a cropu. |
| Reference recent/archive | Uz ma context helpery, ale musi se overit query, seed, fallback a vizual ve vsech vysketech. |
| Contact CTA | Sdileny blok, nutny guard pro Dusek avatar, hodiny a CTA/footer handoff. |
| Footer quick contact/map | Musi mit globalni map link contract a Figma footer visual. |
| Header top contact | Musi mit dark/light variantu a dynamicky open/closed status. |
| Contact map | Musi mit Baspa-like Google Maps contract a spravny fallback, ne rozhozenou lokalni mapu. |
| Product media cards | Existence obrazku nestaci, musi byt citelny a podle Figma card treatmentu. |
| Benefit/options media | CSS ikonky nesmi nahrazovat Figma media layer, pokud neni explicitni WAITING_ON_OWNER. |
| Mobile menu/promo | Musi se overit proti mobile Figma frame, ne jen overflow smoke. |

## PR-1 - Kategorie a product listing contract

Priorita: P0

Cil: Opravit `/virivky/` a `/swimspa/` jako globalni category system, ne jako dva ruzne slepence.

| Krok | Akce | Poznamka |
|---:|---|---|
| 1 | Zmapovat aktualni category render order v `taxonomy-product-category.php`. | Intro, nav, products, configurator, content, showroom, progress, references. |
| 2 | Rozdelit category flow na potvrzene a nepotvrzene moduly. | `virivky` confirmed, `swimspa` needs scope decision. |
| 3 | Zavest category variant contract. | `hot-tub-category`, `swimspa-category`, `accessory-category`. |
| 4 | Opravit mezeru mezi product gridem a konfigurator bannerem na `/virivky/`. | Mezery musi vychazet z Figma y pozic, ne nahodneho paddingu. |
| 5 | Opravit product card visual globalne. | Jedna karta, jeden image contract, jeden badge/name/dimensions layout. |
| 6 | Opravit image contrast a velikost produktu v kartach. | Top-view fotky musi byt citelne jako ve Figme. |
| 7 | Udelat screenshot kontrolu series Custom, Classic, Core. | Figma screenshot vs local crop. |
| 8 | Udelat samostatne rozhodnuti pro swimspa product grid. | Pokud neni Figma frame, nebude se vydavat za Figma parity. |

### Acceptance criteria

| Kontrola | Pass |
|---|---|
| `/virivky/` nema obri prazdnou mezeru pred konfigurator bannerem. | Screenshot. |
| Product card je jedna sdilena komponenta. | Diff nema duplicitni page-specific card internals. |
| Karty jsou citelne ve `virivky` i `swimspa`. | Visual screenshot. |
| `/swimspa/` ma oznacene scope rozhodnuti. | Dokument + guard. |

## PR-2 - Configurator CTA scope a visual

Priorita: P0

Cil: Prestat vkladat konfigurator tam, kde neni potvrzeny, a opravit jeho Figma kompozici tam, kde potvrzeny je.

| Krok | Akce | Soubor |
|---:|---|---|
| 1 | Zmenit renderovani konfiguratoru tak, aby category template neposilal modul automaticky vsem kategoriim. | `taxonomy-product-category.php` |
| 2 | Povolit konfigurator defaultne pro `/virivky/`. | `templates/section/configurator.php` |
| 3 | Pro `/swimspa/` nastavit `WAITING_ON_SCOPE_DECISION` nebo modul do rozhodnuti nevykreslit. | template + plan note |
| 4 | Odstranit teal swimspa variantu jako default. | `_component-contracts.less` |
| 5 | Opravit visual podle Figma: cerveny banner, pravy image/laptop/product layer, spravny crop a spacing. | `category-configurator.png`, CSS contract |
| 6 | Product detail configurator overit zvlast, protoze detail ma vlastni Figma frame. | `figma-detail-body.php` |
| 7 | Guard musi failnout, pokud `/swimspa/` zobrazuje konfigurator bez scope flagu. | `tools/component-contract-smoke.js` nebo novy smoke |

### Acceptance criteria

| Kontrola | Pass |
|---|---|
| `/virivky/` konfigurator sedi proti Figma screenshotu. | Screenshot. |
| `/swimspa/` nema nepotvrzeny konfigurator. | HTML smoke + screenshot. |
| Neexistuje defaultni teal banner vydavany za Figma. | CSS/HTML grep. |
| Banner ma realnou visual image vrstvu. | Visual smoke. |

## PR-3 - Showroom shared component

Priorita: P0

Cil: Jedna showroom komponenta pro HP, category, swimspa a default page/single, s realne viditelnou kolazi.

| Krok | Akce | Poznamka |
|---:|---|---|
| 1 | Potvrdit jeden canonical template pro showroom panel. | `templates/section/showroom.php`. |
| 2 | Presunout internals z page-specific CSS do shared contractu. | Image positions, badge, panel, text, CTA. |
| 3 | Opravit crop a vrstveni tri fotek podle Figmy. | Owner assety jsou povolene, ale crop musi sedet. |
| 4 | Udelat guard na viditelnost fotek, ne jen na existenci tridy. | Screenshot nebo DOM natural size + bounding box. |
| 5 | Zkontrolovat HP, `/virivky/`, `/swimspa/`, mobile. | Screenshot matrix. |

### Acceptance criteria

| Kontrola | Pass |
|---|---|
| Fotky v kolazi jsou realne viditelne. | Screenshot. |
| Badge `280 m²` sedi v kompozici. | Screenshot. |
| Neni jina implementace pro HP a category. | CSS usage map. |

## PR-4 - Reference query, filter a card contract

Priorita: P0

Cil: Reference nesmi byt globalni mix v kazdem kontextu a nesmi mit rozdilny vzhled podle stranky bez canonical variant.

| Krok | Akce | Poznamka |
|---:|---|---|
| 1 | Overit `baspa_references_recent_context_slug()` na HP, `/virivky/`, `/swimspa/`, product detail. | Helper uz existuje, nutna realna kontrola. |
| 2 | Overit seed reference kategorií `virivky` a `swimspa`. | `seed-pilot-content.php`. |
| 3 | Doplnit guard: `/virivky/` bez swimspa-only reference. | Content smoke. |
| 4 | Doplnit guard: `/swimspa/` bez hot-tub-only reference. | Content smoke. |
| 5 | Product detail ma preferovat product nebo category relevantni reference. | Fallback musi byt explicitni. |
| 6 | Homepage muze mit curated/global variantu jen jako explicitni varianta. | `homepage-context`. |
| 7 | Sjednotit card visual archive/recent. | Pills, overlay, title, image crop. |
| 8 | `/reference/` zustava archive/grid + lightbox/popup, ne default portfolio single. | Pokud single existuje, nesmi byt vychozi UX. |

### Acceptance criteria

| Kontrola | Pass |
|---|---|
| Kategorie netahaji reference z jine produktove rodiny. | Smoke + screenshot. |
| Archive grid a recent carousel maji pojmenovane varianty. | Usage map. |
| Figma card treatment sedi ve vsech vysketech. | Screenshot matrix. |

## PR-5 - Mapy a Google Maps contract

Priorita: P0

Cil: Sjednotit mapy podle Baspa patternu a odstranit nesmyslne lokalni mapove odkazy.

| Krok | Akce | Soubor |
|---:|---|---|
| 1 | Nastavit canonical hodnoty `baspa_map` a `arctic_map_embed`. | Customizer/seed. |
| 2 | `/kontakt/` pouziva Google Maps embed v produkci, local fallback jen kdyz embed neni povoleny. | `templates/section/map.php`. |
| 3 | Vsechny buttony `Zobrazit na mapě` vedou na `baspa_map`, ne na `/kontakt/` nebo `/showroom/`. | footer, showroom, map card. |
| 4 | Footer quick map pouziva stejny Google Maps link. | `templates/footer.php`. |
| 5 | Showroom page info link pouziva stejny Google Maps link. | `template-showroom.php`. |
| 6 | Fallback Figma mapa musi mit spravny crop/offset a nesmi pridavat vlastni rozhozene labely. | LESS + map template. |
| 7 | Guard failne pri map CTA na interni URL. | Link smoke. |

### Acceptance criteria

| Kontrola | Pass |
|---|---|
| `/kontakt/` ma embed nebo schvaleny local fallback. | Screenshot. |
| Vsechny `Zobrazit na mapě` CTA vedou na Google Maps. | Link smoke. |
| Fallback mapa nema rozhozene overlay labely. | Screenshot. |

## PR-6 - Header, contact hours a CTA/footer handoff

Priorita: P0

Cil: Status tecky, kontrast topbar textu a CTA/footer flow jsou globalni pravidla, ne page fixes.

| Krok | Akce | Poznamka |
|---:|---|---|
| 1 | Otestovat topbar na tmavem hero a svetlych strankach. | HP, kontakt, showroom, servis, sluzby. |
| 2 | Topbar musi pouzit dynamicky `f-hours__status js-hours__status`. | `.open` zelena, `.closed` cervena. |
| 3 | Topbar ma dark/light variantu podle pozadi. | Ne bily text na svetlem pozadi. |
| 4 | Vsechny hodiny v CTA/footer quick contact pouzivaji stejny status pattern. | Zadna hardcoded permanent green tecka. |
| 5 | Footer musi zustat Figma landscape footer, bez navy override. | Guard na `footer-background.jpg` viditelnost. |
| 6 | CTA/footer handoff se kontroluje jako jeden vizualni blok. | Screenshot. |

### Acceptance criteria

| Kontrola | Pass |
|---|---|
| Na svetlych strankach je top kontakt citelny. | Screenshot. |
| V zavreno stavu jsou tecky cervene. | Forced hours smoke nebo mock. |
| Footer background neni potlaceny. | Screenshot + CSS grep. |

## PR-7 - Product detail media, swatches a benefits

Priorita: P1

Cil: Detail produktu nesmi pouzivat prazdne media plochy nebo CSS ikonky tam, kde Figma ukazuje media layer.

| Krok | Akce | Poznamka |
|---:|---|---|
| 1 | Timberwolf detail znovu porovnat proti Figma detail frame. | Hero, nav, configurator, swatches, benefits. |
| 2 | Acrylic swatches pouzit jen pokud existuje owner/legacy asset. | Jiz castecne hotovo. |
| 3 | Cabinet swatches nechat `WAITING_ON_OWNER`, dokud nejsou podklady. | Nepredstirat hotovy asset. |
| 4 | Benefit/options media nahradit Figma/owner media nebo explicitnim waiting stavem. | Ne genericke CSS ikonky. |
| 5 | Product detail reference musi byt kontextova. | Viz PR-4. |

### Acceptance criteria

| Kontrola | Pass |
|---|---|
| Detail nema fake media. | Screenshot + asset smoke. |
| Missing swatches jsou explicitne oznacene. | DOM smoke. |
| Benefit media odpovida rozhodnuti source. | Asset map. |

## PR-8 - Contact, team, warranty a waiting assety

Priorita: P1

Cil: Stranky, ktere cekaji na owner fotky, musi byt explicitni a nesmi vypadat jako omylem rozbite.

| Krok | Akce | Poznamka |
|---:|---|---|
| 1 | Kontakt directory: potvrdit 6 Figma person cards vs real owner content. | Pokud chybí osoby/fotky, explicitni decision. |
| 2 | O nas team: fotky nebo `WAITING_ON_OWNER`. | Zadna nahodna nahrada. |
| 3 | Warranty cards: obrazky nebo `WAITING_ON_OWNER`. | Card visual musi zustat Figma-like. |
| 4 | Showroom page hero/source decision. | Owner photo vs Figma photo musi byt zapsany override. |
| 5 | Showroom reasons section final QA. | Ikony byly opravene, ale sekce musi projit celou vizualni kontrolou. |

### Acceptance criteria

| Kontrola | Pass |
|---|---|
| Chybejici assety jsou zapsane v asset map. | `docs/asset-source-map-2026-05-29.md`. |
| UI nema nahodne placeholdery. | Screenshot. |
| Owner override je explicitne popsany. | Plan/asset map. |

## PR-9 - Mobile a page-level polish

Priorita: P1

Cil: Po oprave globalnich kontraktu doladit mobile a konkretni stranky.

| Krok | Akce | Poznamka |
|---:|---|---|
| 1 | Mobile homepage promo podle Figmy. | Pokud neni scope zmenen, promo ma existovat. |
| 2 | Mobile menu porovnat proti Figma mobile menu frame. | Nejen overflow. |
| 3 | Support/download rytmus doladit po globalnich opravach. | FAQ, chips, download rows. |
| 4 | Maintenance content source rozhodnuti. | Figma copy vs old Arctic content. |
| 5 | Certifikaty, vlastnosti, servis, sluzby final screenshot pass. | Az po header/footer opravach. |

### Acceptance criteria

| Kontrola | Pass |
|---|---|
| Mobile nema missing promo, pokud Figma scope zustava. | Screenshot. |
| Mobile menu neni jen technicky funkcni, ale odpovida frame. | Screenshot. |
| Page-specific polish nepridava nove globalni duplicity. | Diff review. |

## QA pravidlo po kazdem PR

| Krok | Povinne overeni |
|---:|---|
| 1 | `git status --short` pred praci a po praci. |
| 2 | Screenshot dotceneho modulu v kazdem vyskytu. |
| 3 | Porovnani s Figma screenshotem nebo jasny source override. |
| 4 | Smoke test pro HTML/data kontrakt. |
| 5 | CSS grep, ze nevznikla nova page-specific kopie internals. |
| 6 | Zapis do planu nebo component usage map, pokud se meni contract. |

## Nejblizsi doporucene poradi

| Poradi | PR | Duvod |
|---:|---|---|
| 1 | PR-0 Component audit gate | Bez toho se bude porad opravovat jeden modul vicekrat. |
| 2 | PR-1 Kategorie a product listing contract | Resi tvoji aktualni chybu: mezery, product cards, virivky/swimspa drift. |
| 3 | PR-2 Configurator CTA scope a visual | Potvrdi, ze konfigurator je pro virivky, ne automaticky pro swimspa. |
| 4 | PR-3 Showroom shared component | Opakuje se na vice strankach a nesmi se resit page-by-page. |
| 5 | PR-4 Reference query/filter | Zabrani globalnimu mixu referenci mimo kontext. |
| 6 | PR-5 Mapy a Google Maps contract | Sjednoti Google Maps odkazy a kontaktni mapu podle Baspa patternu. |
| 7 | PR-6 Header/contact/footer status | Doresi globalni viditelnost, status tecky a footer handoff. |
| 8 | PR-7 az PR-9 | Page/detail/mobile polish az po globalnich kontraktech. |

