# Figma structure summary

Generated from Figma API on 2026-05-23.

Source files:

- Wireframe: `puPBNFpuaXpRZR2TINaDvm`
- Final design: `xeOew3dFjDVfjXZrJ09emM`

Raw API summaries:

- `figma-api-summary.json`
- `figma-top-level-nodes.json`

## Wireframe

File name: `Arctic-spas.cz wireframe`  
Page: `Wireframe`

Primary top-level frames:

| Frame | Node ID | Size |
| --- | --- | --- |
| WF - HP v2 | `58:87` | 1920 x 8902 |
| WF - KATEGORIE | `100:1504` | 1920 x 8902 |
| WF - DETAIL KONKRETNIHO PRODUKTU | `100:662` | 1920 x 10168 |
| WF - VLASTNOSTI | `123:85` | 1920 x 8902 |
| WF - VLASTNOSTI DETAIL | `123:468` | 1920 x 8902 |
| WF - DALSI INFROMACE | `123:619` | 1920 x 8902 |
| WF - SLUZBY | `124:823` | 1920 x 8902 |
| WF - CERTIFIKATY | `124:1041` | 1920 x 8902 |
| WF - ZARUKA | `124:1161` | 1920 x 8902 |
| WF - KOLIK STOJI UDRZBA | `124:1262` | 1920 x 8902 |
| WF - PODPORA | `124:1926` | 1920 x 8902 |
| WF - ONAS | `124:2344` | 1920 x 8902 |
| WF - REFERENCE | `124:3287` | 1920 x 8902 |
| WF - SERVIS | `124:3744` | 1920 x 8902 |
| WF - KONTAKTY | `124:3882` | 1920 x 8902 |
| popup | `127:776` | 828 x 1156 |

Additional top-level objects:

- `varianta menu` (`11:2`)
- `Frame 63` (`100:1479`)
- `Frame 64` (`100:1490`)
- repeated reference components/instances

## Final Design

File name: `Arctic-spas.cz grafika`  
Page: `Grafika`

Primary top-level frames:

| Frame | Node ID | Size |
| --- | --- | --- |
| HP | `1:14` | 1920 x 5201 |
| KATEGORIE | `1:262` | 1920 x 8583 |
| DETAIL KONKRETNIHO PRODUKTU | `1:1461` | 1920 x 7808 |
| SHOWROOM | `1:442` | 1920 x 4135 |
| VLASTNOSTI | `1:585` | 1920 x 2668 |
| VLASTNOSTI DETAIL | `1:1302` | 1920 x 5004 |
| SLUZBY | `1:658` | 1920 x 2734 |
| CERTIFIKATY | `1:694` | 1920 x 2574 |
| ZARUKA | `1:719` | 1920 x 2329 |
| PODPORA | `1:752` | 1920 x 5201 |
| O NAS | `1:945` | 1920 x 4582 |
| KONTAKT | `1:1037` | 1920 x 3198 |
| REFERENCE | `1:1127` | 1920 x 2665 |
| DALSI INFROMACE | `1:1216` | 1920 x 2067 |
| KOLIK STOJI UDRZBA | `1:1395` | 1920 x 4324 |
| SERVIS | `1:1426` | 1920 x 2806 |
| popup | `1:1959` | 1920 x 1323 |
| GM - HP | `1:1973` | 375 x 6046 |
| GM - HP menu | `1:2208` | 375 x 774 |

Components and reusable objects:

- `header` component set (`1:1831`), 1440 x 1557
- `Group 547` (`1:2225`)
- `Group 452` (`1:2235`)

## Implementation implications

- The final design has a direct template map for WordPress pages.
- `DETAIL KONKRETNIHO PRODUKTU` should be used as the pilot vertical slice.
- `header` should be inspected first as a reusable component source for desktop/mobile navigation.
- `GM - HP` and `GM - HP menu` are mobile homepage/menu references.
- Wireframe and final design mostly align by template names, so mismatches can be checked frame-by-frame.

## Next API tasks

1. Fetch deeper JSON for `DETAIL KONKRETNIHO PRODUKTU`.
2. Fetch deeper JSON for `header`.
3. Export PNG previews for the key frames.
4. Extract colors, text styles, and image fills.
5. Build a WordPress template/component checklist from the frame tree.
