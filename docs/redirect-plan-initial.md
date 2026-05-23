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
