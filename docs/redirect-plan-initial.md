# Prvni redirect plan

Datum: 2026-05-23  
Zdroj: crawl live webu `https://www.arctic-spas.cz/`  
Vystupy crawlu: `docs/crawl-live/arctic-spas-live-crawl.csv`

## Jasne redirect-only URL

Tyto URL jsou na live webu dostupne a vraci `200`, ale podle briefu se nemaji migrovat jako aktivni produkty/stranky.

| Stara URL | Doporučeny novy cil | Duvod |
| --- | --- | --- |
| `/virivky-dreammaker.php` | `/virivky/` | Dreammaker se ma vyradit z aktivniho sortimentu. |
| `/virivka-ellesmere.php` | `/virivky/` | Stary Core model, neni v aktualnim Core menu. |
| `/virivka-aurora.php` | `/virivky/` | Stary Core model, neni v aktualnim Core menu. |
| `/virivka-orca.php` | `/virivky/` | Stary Core model, neni v aktualnim Core menu. |
| `/virivka-grizzly.php` | `/virivky/` | Stary Core model, neni v aktualnim Core menu. |

Poznamka: cil `/virivky/` je navrzeny cil v novem WordPress URL modelu. Pokud finalni slug kategorie bude jiny, upravi se cil v redirect mape pred nasazenim.

## Aktivni produktove URL z crawlu

Aktivni produkty z crawlu jsou rozpoznane jako:

- 16 virivek,
- 6 swimspa.

Sirsi sortiment k rucni kontrole:

- `/covana.php`
- `/koupaci-sudy-kirami.php`
- `/prislusenstvi-doplnky.php`
- `/sauny.php`

## Pozor na dalsi stare URL

Crawl nasel take dalsi historicke produktove nebo produktove-podobne URL, ktere nejsou automaticky zarazene jako aktivni produkty. Pred finalnim importem je potreba je projit v CSV a rozhodnout, jestli:

- migrovat jako obsahovou stranku,
- sloucit do vlastnosti/technologie,
- presmerovat na aktivni kategorii,
- ponechat jako download/support asset.

Priklad k rucni kontrole: `/virivka-frontier.php`.

## Zpresneni po dokonceni Figma stranek

Datum: 2026-05-24

Po doplneni samostatnych Figma sablon uz cast starych obsahovych URL nesmi smerovat na obecnou podporu nebo katalog. Aktualni mapovani v `wp-content/mu-plugins/arctic-redirects.php`:

| Stara URL | Novy cil | Duvod |
| --- | --- | --- |
| `/baspa.php` | `/o-nas/` | Obsahove jde o O nas / firmu, showroom ma vlastni samostatnou URL. |
| `/kariera.php` | `/o-nas/` | Kariéra a tym patri do Figma stranky O nas. |
| `/sluzby.php` | `/sluzby/` | Nova Figma sablona Sluzby existuje. |
| `/servis.php` | `/servis/` | Nova Figma sablona Servis existuje. |
| `/certifikaty.php` | `/certifikaty/` | Nova Figma sablona Certifikaty existuje. |
| `/zaruka.php` | `/zaruka/` | Nova Figma sablona Zaruka existuje. |
| `/kolik-stoji-provoz-udrzba-virivky.php` | `/kolik-stoji-udrzba/` | Nova Figma obsahova stranka pro provozni naklady existuje. |
| `/izolace-virivky.php`, `/izolace-heatlock.php` | `/vlastnosti/izolace-virivky/` | Nova Figma detailova stranka vlastnosti existuje. |
| `/uprava-vody.php`, `/stavebni-pripravenost.php` | `/dalsi-informace/` | Obsah patri do rozcestniku dalsich informaci; samostatny detail zatim neni. |
| `/servisni-pristup.php`, `/servisni-pristup-core.php` | `/vlastnosti/` | Obsah patri do vlastnosti; samostatny detail zatim neni. |
