# Audit Baspa jako zakladu pro Arctic Spas

Datum: 2026-05-23  
Rozsah: staticka kontrola slozky `baspa.cz/`, hlavne theme `wp-content/themes/baspa`. Reference slozky zustaly netknute.

## Shrnutí

Baspa je pro Arctic velmi dobry vychozi bod na uroven HTML/CSS/layoutu a WordPress sablon. Neni to ale ciste pluginove reseni; je to custom WordPress aplikace schovana v theme. To je pro fork rychle, protoze logika produktu, referenci, FAQ, podpory, kontaktu a sablon uz existuje, ale nemeli bychom bez kontroly kopirovat hlavne formularovy pipeline, externi integrace a administracni nastaveni.

Pro Arctic doporucuji: fork Baspa theme ano, ale v nove slozce `arctic-spas-2`; technicky zaklad prevzit selektivne; produktovy model upravit hned na pilotnim produktu; formularovy modul pred ostrym pouzitim opravit; integrace a tajne klice presunout z theme do konfigurace/plugin vrstvy.

## Co je v Baspa dobre pouzitelne

- Modulova struktura theme je citelna: `functions.php` natahuje samostatne moduly pro produkty, reference, FAQ, podporu, kontakty, slides atd. (`baspa.cz/wp-content/themes/baspa/functions.php:72`).
- Produktovy CPT, taxonomie a sablony uz odpovidaji webu typu katalog/sluzby, takze pro Arctic muzeme zacit z existujiciho toku misto stavet WP theme od nuly.
- Meta Box je pouzit pro produktova pole a je vhodny pro opakovatelne technicke parametry. Pro Arctic ho lze vyuzit pro konfigurace Prestige / Signature / Legend.
- Baspa nema tezky pagebuilder. To je plus: layout se bude dat udrzet v sablonach podle Figma wireframu a vizual upravit pres CSS/assets podle grafiky.
- Existuje uz reseni pro casti, ktere Arctic pravdepodobne take potrebuje: produktove kategorie, galerie, reference, FAQ, support/downloads, showroom, kontakty.

## Hlavni nalezy

### Stav oprav v Arctic forku k 2026-05-24

V `arctic-spas-2/wp-content/themes/arctic/` uz jsou opravené nejdulezitejsi frontendove casti z tohoto auditu:

- formularove sablony vraci hodnoty pres `esc_attr( wp_unslash(...) )` / `esc_textarea( wp_unslash(...) )`,
- formularovy processing uklada POST hodnoty az po `wp_unslash()` a sanitizaci,
- AJAX formularovy handler uz nebere `f-form-processing-path` z POSTu a povoluje jen server-side whitelist sablon,
- hardcoded Bcc je odstraneny a `Reply-To` se pridava jen pro validni e-mail,
- Ecomail integrace pouziva `wp_remote_post()`, ma timeout, validaci e-mailu, local bypass a zadny frontend debug vypis,
- AJAX search sanitizuje keyword, whitelistuje registrovane post typy/taxonomie a escapuje vystup odkazu/titulku,
- vlastni admin settings stranky modulu maji capability gate, nonce, `check_admin_referer()`, `wp_unslash()` pred sanitizaci a escapovany vystup hodnot,
- post metabox a term meta save handlery nepouzivaji slashovane POST hodnoty a post excerpt se uklada korektnim `wp_update_post()` volanim bez rekurzivniho save hooku,
- SVG upload uz neni globalne povoleny; defaultne je vypnuty a lze ho zapnout jen vedome konstantou/filtrem pro admina.

Zbyvajici technicky dluh pred produkci: u verejneho AJAX search lze jeste doplnit nonce/rate limit podle finalni UX implementace.

### Vysoke riziko

1. Kontaktní formulare vypisuji raw `$_POST` zpet do HTML

   V sablonach formularu jsou hodnoty z `$_POST` vlozene primo do `value` a `textarea`, bez `esc_attr()` / `esc_textarea()`:  
   `baspa.cz/wp-content/themes/baspa/modules/contacts/templates/form-contact.php:50`, `:64`, `:78`, `:116`  
   Stejny vzor je i ve `form-service.php` a `form-catalog.php`.

   Dopad: reflektovane XSS pri fallback/PHP odeslani nebo pri chybovem stavu formulare.  
   Doporuceni pro Arctic: pred prevzetim opravit vsechny formularove sablony. Vstup do inputu pres `esc_attr( wp_unslash( $_POST[...] ) )`, textarea pres `esc_textarea(...)`.

2. AJAX formularovy handler bere cestu sablony z POSTu

   Handler `forqy_form_processing()` pouziva `$_POST['f-form-processing-path']` jako parametr pro `get_template_part()`:  
   `baspa.cz/wp-content/themes/baspa/vendor/forqys/form/inc/form.php:19`

   Dopad: i kdyz WordPress `get_template_part()` omezuje nacitani na sablony, je to zbytecne sirene utocne rozhrani. Frontend klient muze ovlivnit, jakou cast theme se system pokusi nacist.

   Doporuceni pro Arctic: cestu z POSTu odstranit. Handler ma povolit jen pevny whitelist typu `contact`, `catalog`, `service`, a podle toho vybrat interní sablonu.

3. E-mailove hlavicky se skladaji rucne a obsahuji hardcoded Bcc

   Form processing pridava `Bcc: pavelrich@gmail.com` a `Reply-To` primo z `$email`:  
   `baspa.cz/wp-content/themes/baspa/modules/contacts/templates/form/processing.php:190`

   Dopad: hardcoded prijemce je pro Arctic neprijatelny; `Reply-To` ma byt validovany pres `sanitize_email()` / `is_email()`.  
   Doporuceni pro Arctic: Bcc odstranit nebo udelat explicitni admin nastaveni; email validovat pred ulozenim i pred hlavickou; idealne pouzit jasny mailer wrapper.

4. Admin nastaveni modulu se ukladaji bez nonce

   Vice modulu ma vlastni admin subpage s `manage_options`, ale ulozeni probiha jen na `isset($_POST['submit'])`, bez `wp_nonce_field()` a bez `check_admin_referer()`:  
   `baspa.cz/wp-content/themes/baspa/modules/accessories/inc/admin.php:43`, `baspa.cz/wp-content/themes/baspa/modules/contacts/inc/admin.php:86`

   Dopad: CSRF riziko pro prihlaseneho admina.  
   Doporuceni pro Arctic: do vsech custom settings stran doplnit nonce, capability check a korektni escapovani pri vystupu hodnot.

5. Snapshot Baspa obsahuje produkcni konfiguraci

   `baspa.cz/wp-config.php` obsahuje DB prihlaseni a salts (`DB_NAME`, `DB_USER`, `DB_PASSWORD`, `AUTH_KEY` atd.; hodnoty zde zamerne nevypisuji).

   Dopad: tyto hodnoty nesmi prejit do noveho projektu, repozitare ani predavky.  
   Doporuceni pro Arctic: `wp-config.php` z referencni slozky nepouzivat, vytvorit vlastni local/stage/prod config, secrets mimo git.

### Stredni riziko

6. Theme bundluje pluginy a vlastni vendor knihovny misto standardni plugin spravy

   Theme primo nacita vlastni `vendor/forqys/*` knihovny a Meta Box plugin:  
   `baspa.cz/wp-content/themes/baspa/functions.php:17`, `:23`, `:41`

   Dopad: rychle pro custom web, horsi pro aktualizace, audit a oddeleni zodpovednosti. Kdyz se theme vypne, zmizi i cast funkcionality.

   Doporuceni pro Arctic: fork je v poradku, ale jadro datoveho modelu a kriticke integrace zvazit jako must-use plugin nebo samostatny plugin `arctic-core`. Minimalne dokumentovat, ze theme je aplikační theme, ne obecna vymenitelna sablona.

7. CPT a taxonomie jsou uvnitr theme

   `product`, `support`, `reference`, `faq`, `contact` atd. jsou registrovane v theme modulech. Produkt ma navic `capability_type => 'page'`, `hierarchical => true`, `has_archive => false`:  
   `baspa.cz/wp-content/themes/baspa/modules/products/type.php:14`, `:40`, `:41`, `:42`

   Dopad: pro fork je to rychle, pro dlouhodobou portabilitu horsi. `page` capabilities a hierarchie mohou byt spravne pro Baspa, ale u Arctic produktu s radami/modely/variantami to musime overit.

   Doporuceni pro Arctic: datovy model definovat pred frontendem na pilotnim produktu. Pokud chceme stabilitu, presunout CPT/meta/taxonomie do `arctic-core` pluginu.

8. Produktovy model Baspa je uzitecny, ale neni presne Arctic

   Baspa ma opakovatelne parametry `product_model`, `product_seats`, `product_nozzles`, rozmery, objem atd.:  
   `baspa.cz/wp-content/themes/baspa/modules/products/type/metabox.php:119`

   Soucasne je tam nedotazeny affiliate model: metabox rika affiliate, `single-product.php` s nim pocita, ale `baspa_products_types()` vraci jen `standard`:  
   `baspa.cz/wp-content/themes/baspa/modules/products/type/metabox.php:24`, `baspa.cz/wp-content/themes/baspa/modules/products/inc/product.php:16`, `baspa.cz/wp-content/themes/baspa/single-product.php:9`

   Dopad: pro Arctic potrebujeme robustnejsi model variant a konfiguraci, ne jen clonovane textove hodnoty.  
   Doporuceni pro Arctic: hned vytvorit pilotni Arctic produkt a podle nej upravit pole pro rady/varianty, ceny, rozmery, jets, seating, dokumenty, galerie a CTA.

9. Externi integrace Ecomail je v theme a pouziva raw cURL

   Ecomail API klic a list ID jsou ulozene v Customizeru a request jde pres `curl_*`:  
   `baspa.cz/wp-content/themes/baspa/modules/contacts/inc/ecomail.php:24`, `:56`

   Dopad: chybi standardni WP HTTP API timeouty/error handling; pri chybe integrace formular muze pusobit jako uspesny; tajny klic je navazany na theme options.

   Doporuceni pro Arctic: pouzit `wp_remote_post()`, timeout, logovani chyb bez vypisu do frontendu, validaci emailu a samostatnou konfiguraci pro stage/prod.

10. reCAPTCHA badge je skryty CSS

   `forqys/form` vklada do `wp_head` CSS, ktere skryje `.grecaptcha-badge`:  
   `baspa.cz/wp-content/themes/baspa/vendor/forqys/form/inc/recaptcha/styles.php:18`

   Dopad: u reCAPTCHA v3 to muze byt problem podle pravidel Google, pokud neni adekvatni textove upozorneni.  
   Doporuceni pro Arctic: bud badge zobrazit, nebo doplnit korektni legal text u formularu.

11. Custom vyhledavani nema nonce a bere typy/taxonomie z POSTu

   AJAX search cte `keyword`, `post_type`, `post_taxonomy` z POSTu bez nonce:  
   `baspa.cz/wp-content/themes/baspa/vendor/forqys/search/inc/search.php:19`

   Dopad: neni to prima kriticka chyba, ale verejny AJAX endpoint muze byt snadno spamovany/dotazovany. `posts_per_page => -1` muze byt vykonove drahe.

   Doporuceni pro Arctic: pridat nonce/rate limit, whitelist povolenych post typů/taxonomii a limit vysledku.

12. Upload SVG je povoleny bez zjevne sanitizace

   Theme pridava `svg` do `upload_mimes`:  
   `baspa.cz/wp-content/themes/baspa/inc/functions/images.php:56`

   Dopad: SVG muze obsahovat skodlivy obsah, pokud uploaduje neduveryhodny uzivatel.  
   Doporuceni pro Arctic: povolit SVG jen adminum a pouzit sanitizaci pres plugin/knihovnu, nebo nahravat loga/ikony primo jako theme assets.

13. Page redirect na produktovou kategorii je chytry, ale muze mat editoru

   Bezne WP stranky se muzou pres meta pole presmerovat na produktovou kategorii:  
   `baspa.cz/wp-content/themes/baspa/page.php:7`, `baspa.cz/wp-content/themes/baspa/modules/pages/type/metabox.php:90`

   Dopad: muze to zpusobit zmatek v obsahu, SEO a migraci, protoze stranka realne existuje, ale neni dostupna.  
   Doporuceni pro Arctic: pouzit jen vedome pro navigacni landing URL; jinak preferovat normalni taxonomicke sablony a Redirection plugin pro stare URL.

14. Theme update checker miri na externi endpoint autora

   Update checker bere JSON z `updates.pavelrichter.cz`:  
   `baspa.cz/wp-content/themes/baspa/inc/updates.php:12`

   Dopad: pro Arctic fork to nesmi zustat jako produkcni update kanal bez dohody.  
   Doporuceni pro Arctic: odstranit nebo prepnout na vlastni update strategii.

### Nizsi riziko / udrzba

15. Chybi build manifest

   Theme ma velky `dist/css/style.css` a mnoho LESS zdroju, ale v rootu theme neni `package.json`, `composer.json`, gulp/vite/webpack config. Enqueue jde primo na `dist/css/style.css` a `dist/js/theme.js`:  
   `baspa.cz/wp-content/themes/baspa/inc/styles.php:14`, `baspa.cz/wp-content/themes/baspa/inc/scripts.php:19`

   Dopad: uprava CSS podle Figma bude mozna, ale musime si rychle ujasnit zdrojovy build. Bez nej hrozi editace velkeho kompilovaneho CSS.

   Doporuceni pro Arctic: zvolena je varianta `2-lite` - puvodni Baspa `dist/css/style.css` neprepisovat primo, ale pridat samostatnou Arctic LESS/CSS vrstvu `src/less -> dist/css/arctic.css`, ktera se nacita po Baspa CSS.

16. Cast kodu vypada historicky/stale

   Theme ma CF7 default template integraci, ale Contact Form 7 neni ve vypsanych pluginech Baspa instalace:  
   `baspa.cz/wp-content/themes/baspa/inc/functions/cf7/form.php:9`

   Dopad: neni to velky problem, ale ukazuje to historicke vrstvy.  
   Doporuceni pro Arctic: pri forku odstranit nepouzivane integrace, aby nova sablona nebyla zbytecne tezka.

17. Customizer drzi marketingove a technicke integrace pohromade

   GTM/GA4, reCAPTCHA a Ecomail jdou pres `get_theme_mod()`:  
   `baspa.cz/wp-content/themes/baspa/theme.php:28`, `:32`, `:40`

   Dopad: pro jeden web funkcni, pro stage/prod a spravu pristupu mene prehledne.  
   Doporuceni pro Arctic: business kontaktni udaje mohou zustat v administraci, tajne klice a API konfigurace radeji do env/config nebo samostatne settings strany s jasnou dokumentaci.

## Odpoved na otazku "obchazi to pluginy?"

Ano, v nekolika oblastech:

- Formularovy system nahrazuje plugin typu Contact Form 7 / Gravity Forms / Fluent Forms vlastnim AJAX/PHP resenim.
- Ecomail integrace je vlastni kod misto oficiálního pluginu nebo izolovane integracni vrstvy.
- Meta Box je pribaleny primo v theme misto spravy jako plugin.
- CPT, taxonomie a cast nastaveni jsou v theme, ne v core pluginu.
- Tracking/GTM eventy jsou resene vlastnim JS a theme configem.
- Admin settings stranky jsou vlastni, misto WordPress Settings API s nonce/options group patternem.

To neni automaticky spatne. Pro Baspa to dava smysl jako custom theme. Pro Arctic je ale potreba prevzit to jako kontrolovany fork, ne jako slepe kopirovani.

## Doporuceny postup pro Arctic

1. Forknout Baspa do `arctic-spas-2/wp-content/themes/arctic-spas` a zmenit identitu theme.
2. Nejdrive vytvorit pilotni produkt podle live Arctic obsahu a Figma detailu. Na nem overit datovy model.
3. Upravit produktova pole pro Arctic konfigurace: rada/model, varianta Prestige/Signature/Legend, technicke parametry, cena/text ceny, galerie, downloads, CTA.
4. Opravit formularovy modul pred produkci: escapovani, whitelist handleru, email validace, odstranit hardcoded Bcc, nonce v adminu.
5. Rozhodnout, jestli datovy model zustane v theme, nebo vznikne maly `arctic-core` plugin. Rychlejsi je theme, cistsi je core plugin.
6. Napojit Figma: wireframe urci architekturu sablon, grafika urci CSS tokeny, logo, spacing, barvy a assety. Baspa rozmerove drzet co nejvic, aby prechod mezi weby pusobil skoro neznatelne.
7. Crawlovat live `arctic-spas.cz` jako zdroj pravdy pro obsah a soucasne mapovat stare URL na nove WP routy.
8. Po implementaci udelat QA checklist: formular, produkt, kategorie, mobile menu, support/downloads, SEO title/meta, redirects, performance, consent/tracking.

## Co bych pro Arctic urcite neopakoval beze zmeny

- Raw vypis `$_POST` do formularu.
- Cestu template partu ridit z POSTu.
- Hardcoded Bcc na konkretni email vyvojare.
- Ukladani admin nastaveni bez nonce.
- Produkcni tajemstvi ve snapshotu projektu.
- Ecomail pres raw cURL bez osetreni chyb.
- SVG upload bez sanitizace.
- Produktove varianty resit jen volnymi textovymi clone poli, pokud Arctic potrebuje porovnatelne konfigurace.

## Verdikt

Baspa je vhodny zaklad pro rychle a kvalitni postaveni Arcticu, protoze architektura webu je blizka a Figma potvrzuje, ze Arctic ma byt rozmerove a UX velmi podobny. Neni ale vhodne prevzit Baspa jako "hotovy system". Nejbezpecnejsi cesta je fork + rychly technicky cleanup + pilotni Arctic produkt + postupne prebarveni/prebrandovani podle Figma grafiky.
