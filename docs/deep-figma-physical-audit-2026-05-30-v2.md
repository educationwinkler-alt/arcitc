# Deep Figma physical audit v2 - granular element checklist - 2026-05-30

## Verdikt v2

Predchozi audit nebyl dost detailni. Chybely v nem konkretni prvky jako top kontakt nad menu, opening-hours status tecka a fakt, ze mapa nema jen spatny styl, ale pin/labely jsou fyzicky posunute proti podkladu. Tecka neni staticka: podle Baspa/Forqys patternu ma byt zelena pri otevreno a cervena pri zavreno.

Tahle verze proto nepouziva jen page-level verdict. Je to granularni seznam jednotlivych viditelnych rozdilu: globalni komponenty, header/topbar, mapa, footer, mobile, showroom, produktove karty, stranky a QA guardy.

## Evidence

- Aktualni local screenshoty: `docs/screenshots/deep-figma-physical-audit-2026-05-30/current/`
- Aktualni Figma exporty: `docs/screenshots/deep-figma-physical-audit-2026-05-30/figma-current/`
- Side-by-side porovnani: `docs/screenshots/deep-figma-physical-audit-2026-05-30/physical-compare.html`
- Metriky: `docs/screenshots/deep-figma-physical-audit-2026-05-30/metrics.json`
- Figma file: `xeOew3dFjDVfjXZrJ09emM`
- Local base URL: `http://localhost:8090`

## Dulezita nova zmerena fakta

- Figma top contact na HP: node `1:28`, obsahuje zelenou tecku `Ellipse 16` s barvou `#00FF80` a text `Po - Pa 8:00-17:00 h`.
- Figma top contact na kontaktni strance: node `1:1039`, text je `Dark`, ne bily.
- Local top contact: `.f-bar` ma `color: rgb(255, 255, 255)` i na svetlych pozadich, takze je necitelny.
- Local top contact: v DOM/Computed checku nebyl nalezen zadny zeleny prvek v headeru (`greenishCount: 0`).
- Baspa live check 2026-05-30: status je automaticky, `hours_data` definuje Po-Pa `08:00-16:00`, sobota/nedele zavreno, AJAX `hours_is_open` vraci `open:false` a DOM dostane `f-hours__status js-hours__status closed`; pseudo-tecka je cervena. Arctic proto nesmi hardcodovat zelenou tecku, ale musi napojit existujici `forqy_hours` open/closed status.
- Figma kontakt mapa: map image node `1:1069` je na `x=-867`, `y=430`, `width=3110`, `height=782`.
- Local kontakt mapa: `.f-local-map__image` je na `x=-595`, `y=430`, `width=3110`, `height=782`.
- Mapovy podklad je tedy proti Figma posunuty o cca `272px`, zatimco pin je skoro na stejne obrazovkove pozici. Vysledkem je pin na nesmyslnem miste.
- Local navic prida vlastni overlay labely `.f-local-map__label--brno` a `.f-local-map__label--moravany`; ve Figma contact frame tyto labely nejsou samostatne overlay vrstvy.

## Granularni seznam rozdilu

### Globalni architektura a QA

1. Opakovane komponenty nejsou auditovane podle fyzickeho vysledku, ale casto jen podle trid.
2. `component-contract-smoke` vynucuje existenci `f-footer--handoff`, i kdyz fyzicky nici Figma footer.
3. Footer guard nekontroluje viditelnou mountain/landscape vrstvu.
4. Showroom guard nekontroluje, jestli jsou fotky v kolazi skutecne viditelne.
5. Product media guard nekontroluje, jestli jsou produktove obrazky v kartach citelne.
6. Mobile promo guard je v konfliktu s Figma mobile frame, kde promo existuje.
7. Height metrics casto sedi, ale neodhaluji chybejici/posunute vrstvy.
8. Testy nehlidaji kontrast top contact textu proti svetlemu pozadi.
9. Testy nehlidaji dynamicky opening-hours status v headeru: `.open` zelena tecka pri otevreno, `.closed` cervena tecka pri zavreno.
10. Testy nehlidaji map background offset proti pinu.
11. Testy nehlidaji, ze local pridal map labely, ktere Figma nema jako overlay.
12. Testy nehlidaji pocet contact/person cards proti Figme.
13. Testy nehlidaji, zda team/contact cards maji fotky nebo placeholder inicialy.
14. Testy nehlidaji, zda warranty cards maji obrazky.
15. Testy nehlidaji, zda swatche maji realne material image vrstvy.
16. Testy nehlidaji, zda CTA/footer handoff zachova Figma landscape footer.
17. Testy nehlidaji, zda Figma slozene sekce nejsou zjednodusene na gradient.
18. Testy nehlidaji textovou hustotu u dlouhych info stranek.
19. Testy nehlidaji, zda product detail benefit media jsou fotky/thumbnail-like misto CSS ikon.
20. Testy nehlidaji, zda mobile menu obsah odpovida Figma frame.

### Header, top kontakt, menu

21. Na svetlych strankach ma byt top kontakt tmavy/citelny, ale local ho drzi bily.
22. Local `.f-bar` ma `color: rgb(255, 255, 255)` i na `/kontakt/`, `/podpora/`, `/o-nas/`, `/servis/` a dalsich svetlych top pozadich.
23. Figma kontakt frame `1:1039` pouziva pro top contact text `Dark`.
24. Local top contact je na svetlem pozadi skoro neviditelny.
25. Local top contact nema dynamicky opening-hours status pred oteviraci dobou.
26. Figma HP top contact node `1:29/1:30` ma zelenou tecku `#00FF80`, ale to reprezentuje open stav; implementace musi byt open/closed podle casu.
27. Local DOM check nenasel v headeru zadny `f-hours__status js-hours__status` prvek ani green/online dot prvek.
28. Figma top contact oddeluje telefon a oteviraci dobu skupinou s teckou; local to zobrazuje jako plain text.
29. Local top contact ma text `Po - Pa 8:00-17:00 h` bez vizualni open/closed indikace.
30. Local top contact neni kontextove prebarveny podle pozadi za headerem.
31. Na HP muze bily top contact davat smysl nad tmavym hero, ale stejna barva se nesmi pouzit na svetlem page backgroundu.
32. Figma top contact na HP je x/y blizko `x=769`, `y=12`; local je centrovany podobne, ale barva/stav jsou spatne pro svetle stranky.
33. Header top bar v localu pusobi jako pruh textu bez vlastniho kontrastniho backgroundu.
34. Na svetlych strankach local top contact text splývá s hornim svetlym gradientem.
35. Header neobsahuje explicitni stav `light background variant` vs `dark hero variant`.
36. Navigation panel je vizualne blizko, ale top contact nad nim neni soucasti spravneho Figma kontraktu.
37. Search icon/menu spacing se zda blizky, ale topbar chyba znici header parity.
38. Mobile header close/menu je blizky, ale mobile menu content neodpovida Figma frame.
39. Desktop mega menu je samostatny stav; audit v2 ho neoznacuje jako final pass bez dalsiho hover screenshotu.
40. Header QA musi porovnavat i barvu topbar textu a pritomnost dynamicke status tecky, ne jen header box.

### Footer global

41. Local footer je tmavy navy blok.
42. Figma footer je svetly footer s landscape/mountain backgroundem.
43. `footer-background.jpg` existuje v `wp-content/uploads/import/figma/`, ale neni viditelny kvuli `f-footer--handoff`.
44. `templates/footer.php` pridava tridu `f-footer--handoff`.
45. `_component-contracts.less` prepisuje `.f-footer--arctic.f-footer--handoff` na `background: var(--arctic-contract-footer-bg)`.
46. `--arctic-contract-footer-bg` je `var(--arctic-color-menu)`, tedy tmavy navy.
47. Puvidni `.f-footer--arctic` v `_components.less` ma spravne `footer-background.jpg`, ale je prebita.
48. Figma footer node `1:208` ma `width 1920`, `height 773`.
49. Figma footer background node `1:210` ma landscape image `1920 x 1209`.
50. Local footer nerespektuje Figma footer komponentu.
51. Footer sloupce jsou v localu svetle/tlumeně na tmavem pozadi, Figma je ma na svetlem landscape/footer pozadi.
52. Footer quick contact card v localu sedi na tmavem bloku, Figma ji ma v bile/svetle kompozici nad krajinou.
53. Footer map card v localu sedi v tmave variante, Figma je soucasti svetle footer kompozice.
54. Footer logo dole je v localu na tmavem pozadi, ve Figme sedí u landscape stripu.
55. Copyright/legal linky jsou v localu v tmavem footeru, ve Figme jsou na svetle landscape casti.
56. CTA-to-footer handoff v localu maskuje cyan/light seam tmavym boxem.
57. Spravny handoff ma zachovat mountain background, ne ho nahradit.
58. Mobile footer ma stejnou chybu jako desktop.
59. Footer je globalni P0 na vsech strankach.
60. Zadna auditovana stranka nemuze dostat final visual pass, dokud footer zustava takto.

### Kontaktni CTA global

61. Contact CTA cerveny blok je obecne pritomen, ale jeho vztah k footeru je spatny.
62. Figma CTA konci do svetleho/landscape footeru, local konci do tmaveho navy footeru.
63. CTA bottom seam/podklad je misty svetle modry/cyan, ale misto Figma transitionu nasleduje tmavy footer.
64. Contact CTA bar/person row je funkcne blizko, ale nelze ho schvalit izolovane od footer handoffu.
65. CTA avatar/image zdroj je misty spravny, ale globalni contrast/handoff ne.
66. CTA button radii jsou blizko, ale bottom visual context je spatny.
67. Mobile CTA je prilis odlisena od Figma mobile/footer flow.
68. QA musi hlidat CTA + footer dohromady, ne oddelene.
69. CTA handoff neni page-specific problem; je to globalni component contract problem.
70. Page-level opravy CTA bez footeru by byly jen kosmetika.

### Mapa a kontakt page

71. Kontaktni mapa ma spatny podkladovy offset.
72. Figma map image `1:1069` je na `x=-867`; local `.f-local-map__image` je na `x=-595`.
73. Rozdil cca `272px` posouva geografii pod pinem.
74. Pin je skoro na stejne obrazovkove pozici jako ve Figme, ale background pod nim je jinde.
75. Vysledkem je pin mimo Moravany/showroom, tedy funkcne nesmysl.
76. Local mapa zobrazuje pin u oblasti, ktera neodpovida showroomu.
77. Local map image je svetle sedy/washed, Figma map visual je tmavsi/modrejsi/kontrastnejsi.
78. Local pin je v dodanem screenshotu tmavy/cerny; Figma pin ma byt cerveny accent.
79. V DOM computed pro local pin je background `rgb(163, 31, 55)`, ale screenshot ukazuje, ze visual se jevi tmave/cerně kvuli map/overlay/renderingu.
80. Local prida overlay label `Brno` jako `.f-local-map__label--brno`.
81. Local prida overlay label `Moravany` jako `.f-local-map__label--moravany`.
82. Figma contact frame nema tyto labely jako samostatne overlay text nody v map sekci.
83. Tyto overlay labely jsou rozhozene a pusobi jako nesmyslne mapove popisky.
84. `Moravany` label v localu je vizualne mimo spravny vztah k pinu.
85. `Brno` label v localu je velky, sedy/bily a neni soucasti Figma map overlay struktury.
86. Local map card text pouziva `Bohunická cesta 15664 48 Moravany u Brna`, Figma ukazuje `Bohunická cesta 15` + `Moravany u Brna`.
87. Local adresa je obsahove/formátově rozbita (`15664 48`).
88. Figma ma v map card oddelene `Kde nas najdete`, `Moravany u Brna`, `Bohunická cesta 15`, `Zobrazit na mapě`.
89. Local card ma nadpis `Adresa` a jinou strukturu.
90. Figma ma oteviraci dobu `Úterý - Pátek` a casy `9:00 - 11:30`, `12:30 - 16:00`; local ma `Po - Pá 8:00-17:00 h`.
91. To muze byt content-source change, ale neni oznacene jako approved override.
92. Figma map section card je na `x=260`, `y=561`; local card je podobne, ale obsah a labels nesedi.
93. Figma pin node `1:1086` je na `x=1226`, `y=786`; local pin je `x=1217`, `y=777`, ale protoze podklad je posunuty, vysledek nesedi.
94. Figma map card ma icon rows; local struktura je zjednodusena/jina.
95. Kontakt page ma misto 6 Figma person/contact cards jen 3 cards.
96. Figma person cards maji fotky/avatar photos.
97. Local contact cards pouzivaji fallback/inicialy nebo zjednodusene avatar prvky.
98. Figma ma `Další důležité kontakty` a `Fakturační údaje`; local kontakt visual tento rozsah neodpovida.
99. Figma obsahuje osoby jako Vlastimil Zhoř, Lukáš Dušek, Helena Antoňová, Alena Janulíková a dalsi; local ma redukovany seznam.
100. Kontakt page vyska sedi, ale to zakryva chyby mapy a contact cards.

### Homepage desktop

101. Homepage celkova vyska sedi, ale vnitrni prvky nesedi.
102. Top contact na HP ma status tecku ve Figme; local ji nema.
103. HP top contact bily text je na tmavem hero relativne citelny, ale chybi open/closed status dot.
104. Hero hlavni image/crop je potreba znovu pixelove overit, neni oznacen jako final pass v2.
105. Hero slider dots jsou pravdepodobne blizko, ale v2 je neuzavira bez samostatneho crop checku.
106. Hero promo desktop je viditelne resene, ale mobile promo chybi.
107. Figma HP category cards maji presne overlay/crop, local je blizky, ale source/crop neni znovu potvrzeny jako pass.
108. Main category card text position muze byt posunuty proti Figma overlay.
109. Benefit cards na HP jsou blizko, ale nebyly fyzicky detailne zmereny v predchozim auditu.
110. Showroom sekce na HP je stale rozbita.
111. HP showroom ma prazdne/faint sede media boxy.
112. Figma showroom ma tri viditelne showroom fotky v kolazi.
113. Figma showroom kolaz ma fotku vlevo, fotku nahore/stred a fotku dole/stred; local je neukazuje spravne.
114. Badge `280 m²` je v localu v jine vizualni kompozici.
115. HP progress/prubeh je blizky, ale musi byt znovu srovnan po showroom repair.
116. HP references maji jine fotky/cropy nez Figma.
117. Figma HP reference card visual pouziva konkretni crop a overlay; local pouziva real reference s jinymi croppy.
117a. Homepage reference muze byt curated/global vyber, ale musi to byt explicitni varianta, ne stejny tichy fallback pro vsechny kontexty.
118. Reference carousel arrow/controls nejsou potvrzene 1:1.
119. HP CTA-to-footer transition je spatny kvuli footeru.
120. HP footer je tmavy misto mountain.

### Homepage mobile

121. Figma mobile HP ma promo block hned po hero.
122. Local mobile HP promo block chybi.
123. Local mobile flow jde z hero rovnou na category cards.
124. Figma mobile category cards startuji az po promo blocku.
125. Mobile HP top contact/opening-hours status neni podle Figma top contact skupiny.
126. Mobile hero crop musi byt znovu porovnan; nebyl finalne schvalen v2.
127. Mobile category cards maji jiny spacing proti Figma, protoze promo chybi.
128. Mobile showroom card je jine kompozice nez Figma.
129. Figma mobile showroom ma tmavy card/collage/pin pattern.
130. Local mobile showroom ma prazdnejsi/placeholder media feeling.
131. Mobile reference carousel ma jine fotky/cropy.
132. Mobile reference controls/tecky/arrow nejsou 1:1.
133. Mobile CTA je v jinem vztahu k footeru.
134. Mobile footer je tmavy navy.
135. Figma mobile footer ma svetle footer/accordion-like prvky a landscape strip.
136. Mobile quick contact card sedi v localu na spatnem tmavem podkladu.
137. Mobile bottom logo/footer pozice je jina.
138. Mobile page height je jen o `68px` jina, ale visual flow je spatny.
139. Mobile promo scope musi byt explicitne rozhodnut; defaultne Figma rika, ze tam je.
140. Mobile HP neni final pass.

### Mobile menu

141. Figma `GM - HP menu` ma white top bar, dark overlay a search input.
142. Figma mobile menu export neukazuje viditelne nav linky.
143. Local mobile menu ukazuje nav linky `Vířivky`, `Celoroční bazény`, `Vlastnosti`, `Další informace`.
144. Local mobile menu ukazuje CTA `Nezávazná konzultace`.
145. Figma mobile menu search je na y cca `527`; local search je po nav/CTA a jinak kontextovany.
146. Local mobile menu screenshot ma viewport height `900`, Figma frame `774`.
147. Local menu content je tedy vetsi/delsi nez Figma menu frame.
148. Close button je podobny, ale obsah overlay je jiny.
149. Pokud Figma menu frame nema skryte vrstvy, local menu je mimo Figmu.
150. Pokud Figma frame je jen partial state, musi byt potvrzeno; nelze to oznacit jako pass.

### Kategorie virivky

151. Category hero ma top contact bez dynamicke status tecky.
152. Category top contact je bily na dark hero, ale open/closed dot chybi.
153. Figma category ma pravy sale/promo badge u hero; local ho nema stejne.
154. Intro `Vlastnosti virivky` image/crop neni 1:1.
155. Druhy intro `Zaruka` blok ma v localu prazdne/sede media misto.
156. Figma `Zaruka` intro ma fotku venkovni virivky/terasy.
157. Series nav/tabs jsou blizko, ale text/spacing neni potvrzen pixelove.
158. Product card media ve `Serie Custom` pusobi bile/prazdne.
159. Figma product cards maji viditelne top-view produkty.
160. Local product cards nekdy maji obrazek, ale maly/svetly/citelnost spatna.
161. Product card background a media contrast neni podle Figmy.
162. Card radius/shadow je blizko, ale media layer chybi/zanika.
163. `TOP` badge placement/velikost neni 1:1.
164. Product names/dimensions mohou byt source-correct, ale visual card contract neni.
165. `Serie Classic` cards jsou lepsi, ale nektere media nejsou stejne jako Figma.
166. `Serie Core` cards jsou lepsi, ale stale neni 1:1 crop/source.
167. Configurator banner je zjednoduseny gradient/dekor.
168. Figma configurator banner ma produkt/interier/laptop obrazovou vrstvu.
169. Local configurator banner nevypada jako slozena Figma sekce.
170. Showroom panel je prazdny/faint, stejne jako HP.
171. Figma showroom panel ma fotokolaz.
172. Progress section je blizka, ale neni final bez okolnich shared oprav.
173. Reference cards maji jine fotky/cropy.
173a. `/virivky/` reference carousel aktualne muze tahat globalni all-reference vyber bez category/product filtru.
173b. Screenshot ukazuje `Swimspa Wolverine` ve virivky kontextu, coz neodpovida category/Figma zameru.
173c. Reference query musi byt kontextovy: hot tub category nesmi nahodne zobrazovat swimspa-only referenci.
174. CTA/footer handoff je spatny.
175. Footer je spatny.
176. Vyska sedi, ale visual vrstvy ne.

### Kategorie swimspa

177. Swimspa pouziva Figma category frame jako srovnani, ale je o cca `1910px` kratsi.
178. Neni jasne potvrzen samostatny Figma frame/scope pro swimspa.
179. Swimspa hero image/crop se lisi od virivky category frame.
180. Product list je kratsi nez Figma category flow.
181. Nektere swimspa product cards jsou prazdne nebo bez citelneho media.
182. Figma category ma vice produktu/sekci a plnejsi layout.
183. Swimspa configurator banner je teal/blue a vizualne mimo Figma category red/banner system.
184. Pokud je teal banner schvaleny override, chybi source decision.
185. Showroom panel ma stale prazdne/faint media.
186. Progress section neni v uplne stejnem kontextu jako Figma.
187. Reference cards maji jine fotky/cropy.
187a. `/swimspa/` reference carousel musi mit vlastni context filter, ne stejny globalni mix jako homepage/category.
188. CTA/footer handoff spatny.
189. Footer spatny.
190. Swimspa neni final pass bez scope rozhodnuti.

### Product detail Timberwolf

191. Detail page je cca `348px` kratsi nez Figma.
192. Product hero image neni Figma hero image/crop.
193. Figma detail hero vypada jako lifestyle/exterior hot tub image.
194. Local detail hero je jiny produktovy/top/interior visual.
195. Product top nav/sticky tabs je potreba znovu pixelove overit.
196. Konfigurace/cards flow je kratsi/komprimovanejsi.
197. Swatch cards jsou prazdne/svetle textove plochy.
198. Figma swatches maji material/cabinet obrazky.
199. Owner swatch assety existuji pro cast barev, ale visual layer neni plne Figma.
200. Cabinet/shell separation neni jako Figma.
201. Missing swatches nejsou v UI oznacene jako WAITING_ON_OWNER.
202. Benefit cards pouzivaji CSS/generovane ikonove tvary.
203. Figma benefit cards maji realne thumbnail/photo-like media.
204. Benefit plus/interaction visual neni 1:1.
205. Static/interactive cards jsou funkcne rozlisene, ale visual contract nesedi.
206. Product options media maji stejny problem jako benefits.
207. Configurator banner neni plne Figma slozena kompozice.
208. Reference section ma jine fotky/cropy.
208a. Product detail reference section musi preferovat product/category relevantni reference, ne tichy globalni all-reference query.
209. Contact CTA/footer handoff spatny.
210. Footer spatny.

### Showroom shared component

211. Shared showroom component je jeden z nejvetsich opakovanych problemu.
212. Figma showroom component ma tmavy panel + tri realne fotky.
213. Local casto ukazuje tmavy panel + prazdne/faint sede plochy.
214. Figma kolaz node `1:121` ma image rectangles `1:123`, `1:124`, `1:125`.
215. Local owner images existuji, ale nejsou fyzicky vrstvene/cropnute jako Figma.
216. Local left photo block neni viditelny jako Figma.
217. Local upper/middle photo block neni viditelny jako Figma.
218. Local lower photo block neni viditelny jako Figma.
219. Badge `280 m²` neni ve spravne kompozici s fotkami.
220. Showroom component se opakuje na HP, category, swimspa a mobile.
221. Oprava musi byt globalni canonical component, ne page-by-page.
222. Guard musi kontrolovat actual image visibility, ne jen `f-showroom-panel--collage` class.
223. Owner asset source muze byt spravny, ale musi respektovat Figma crop/position.
224. Pokud Figma showroom photos jsou design-only, owner override musi byt explicitne zapsany.
225. Soucasny stav vypada jako placeholder.

### Showroom page

226. `/showroom/` hero image je jiny nez Figma.
227. Figma hero je interior/showroom s virivkou a Arctic signage.
228. Local hero je exterior/storefront/yellow building.
229. Pokud je exterior owner image schvaleny override, neni to v auditu jasne oznaceno.
230. Figma showroom page ma dve velke showroom fotky v content split sekcich.
231. Local content split image crop/source se lisi.
232. `Proc navstivit` cards maji jiny card feel/styling.
233. Showroom mini CTA/card neni 1:1.
234. Info/contact row spacing neni 1:1.
235. Page height sedi, ale visual source/crops ne.
236. CTA/footer handoff spatny.
237. Footer spatny.
238. Showroom page neni final pass.

### Reference

239. `/reference/` archive/grid je funkcne spravnejsi nez driv.
240. Figma `REFERENCE` pouziva opakovany Timberwolf visual/content.
241. Local pouziva realne rozmanite reference.
242. To muze byt spravne content-source override, ale neni to 1:1 Figma.
243. Reference card crops se lisi.
244. Reference overlay darkness se lisi.
245. Reference tag/pill placement se lisi.
246. Reference title line placement se lisi.
247. Reference grid spacing je blizky, ale neni pixel signoff.
248. Reference CTA/footer handoff spatny.
249. Recent reference component mimo archive pouziva globalni query/filter bez jasneho kontextoveho contractu.
250. Homepage muze byt curated/global vyjimka, ale category/product context nesmi automaticky prevzit stejne globalni reference.
251. Footer spatny.
252. Reference neni final visual pass bez explicitniho content-source a context-filter rozhodnuti.

### O nas

251. Figma `O NAS` ma team cards s fotkami.
252. Local `O nas` ma team cards s placeholder/inicialami.
253. Team card image layer chybi.
254. Pokud fotky nejsou owner-approved, musi byt WAITING_ON_OWNER.
255. Local team cards proto nejsou Figma parity.
256. Career accordion/card obsah je jednodussi/jiny nez Figma.
257. Career card visual radius/shadow/hustota neni 1:1.
258. Stats section je blizko, ale neni detailne pixelove uzavrena.
259. CTA/footer handoff spatny.
260. Footer spatny.

### Kontaktni osoby / person cards

261. Kontakt page Figma ma 6 person/contact cards.
262. Local kontakt page ma 3 cards.
263. Figma cards maji fotky, local nema stejnou photo layer strukturu.
264. Figma obsahuje role/jmena/emaily/telefony vice osob.
265. Local redukuje kontaktni directory.
266. Pokud je redukce schvalena ownerem, musi byt approved override.
267. Bez override je to content + visual mismatch.
268. Avatar circle/crop neni podle Figmy.
269. Card spacing/grid neni podle Figmy.
270. Contact page neni pass.

### Zaruka

271. Figma `ZARUKA` ma tri warranty cards s produktovymi obrazky nahore.
272. Local ma tri textove bile cards bez obrazku.
273. Figma note/poznamka je vedle cards, local je pod cards.
274. Local page je o cca `143px` kratsi.
275. Card visual density je plossi.
276. Product image layer chybi.
277. Pokud nejsou obrazky owner-approved, musi byt WAITING_ON_OWNER.
278. CTA/footer handoff spatny.
279. Footer spatny.
280. Zaruka neni pass.

### Podpora

281. `/podpora/` je cca `305px` vyssi nez Figma.
282. FAQ section ma jiny vertical rhythm.
283. Open FAQ card je v localu vyssi/volnejsi.
284. FAQ chips spacing neni 1:1.
285. Download section rows jsou vyssi/jinak odsazene.
286. Download card/accordion treatment neni 1:1.
287. Form block je vyssi/volnejsi nez Figma.
288. Sidebar contact card je podobna, ale spacing/position neni 1:1.
289. CTA/footer handoff spatny.
290. Footer spatny.
291. Podpora neni final pass.

### Maintenance / kolik stoji udrzba

292. `/kolik-stoji-udrzba/` je cca `1021px` kratsi nez Figma.
293. Figma obsahuje delsi textovy clanek.
294. Local obsah je zkraceny.
295. Nektere Figma odstavce/sekce chybi nebo jsou redukovane.
296. Textova hustota uvodu je jina.
297. `Naklady na vlastnictvi` cast je kratsi.
298. `Jaký je skutecny provozni naklad` cast je kratsi.
299. `Naklady na udrzbu` cast je kratsi.
300. Figma ma vice obsahu pred CTA.
301. Local jde na CTA moc brzo.
302. Source-of-truth musi byt overen proti old Arctic/owner.
303. Pokud Figma copy neni final, musi byt explicitni content-source override.
304. Footer spatny.
305. Maintenance neni pass.

### Certifikaty

306. Certifikaty height sedi, ale loga/cards nejsou 1:1.
307. Figma certifikacni loga jsou v mensich rounded white cards.
308. Local ma vetsi bile plochy a jinou hustotu.
309. TUV loga velikost/position nejsou presne Figma.
310. Hot Tub Star card scale/position neni presne Figma.
311. Text-to-logo grid je podobny, ale neni pixel parity.
312. CTA/footer handoff spatny.
313. Footer spatny.
314. Certifikaty nejsou final pass.

### Vlastnosti listing

315. Vlastnosti listing height sedi.
316. Feature cards jsou blizko, ale overlay/arrow placement neni potvrzen jako pixel pass.
317. Card image crop je opakovany a muze byt proti Figma source zjednoduseny.
318. CTA/footer handoff spatny.
319. Footer spatny.
320. Bez footeru a card crop signoffu neni final pass.

### Vlastnosti detail

321. Hero/detail image se lisi od Figmy.
322. Figma detail ma jiny hot tub/crop.
323. Local uvodni text je kratsi/ma jinou hustotu.
324. Figma ma vice textu v jednotlivych blocich.
325. Diagram block je podobny, ale scale/spacing neni potvrzen 1:1.
326. `Dalsi inovace` textova hustota se lisi.
327. `Nejnizsi provozni naklady` textova hustota se lisi.
328. `Skutecna ochrana proti mrazu` textova hustota se lisi.
329. Dalsi vlastnosti cards jsou blizko, ale footer problem zustava.
330. Vlastnosti detail neni pass.

### Sluzby

331. Sluzby height sedi.
332. Header top contact na svetlem pozadi je spatny: bily text, bez dynamicke status tecky.
333. CTA/footer handoff spatny.
334. Footer spatny.
335. Main content je potreba jeste pixelove overit po header/footer opravach.
336. Sluzby neni final pass.

### Servis

337. Servis height sedi.
338. Header top contact je bily na svetlem top backgroundu, spatne.
339. Header top contact nema dynamickou status tecku.
340. Form block je podobny, ale spacing/radius neni finalne overen.
341. Service price columns jsou blizko, ale ne pixel signoff.
342. CTA/footer handoff spatny.
343. Footer spatny.
344. Servis neni final pass.

### Product/category media source

345. Product cards mohou mit image URLs, ale nektere jsou vizualne necitelne.
346. Existence `data-product-media="product-image"` neni dostatecny pass.
347. Product image natural file existuje, ale musi byt viditelny v karte.
348. Nektere product images jsou male/svetle a zanikaji v bile card.
349. Missing image status neni vzdy vizualne zrejmy jako WAITING_ON_OWNER.
350. Product card image crop musi byt canonical contract.
351. Category product card Figma pouziva top-view image scale, local neni konzistentni.
352. Swimspa cards maji stejne riziko.
353. Mega menu thumbnails nejsou soucasti final passu bez samostatneho hover/menu screenshotu.
354. Product detail benefit media nesmi byt jen CSS decorative shape, pokud Figma ukazuje image/thumbnail visual.
355. Product options media maji stejne pravidlo.
356. Swatches musi byt real assety nebo WAITING_ON_OWNER, ne prazdne visual cards.

### Configurator/banner component

357. Figma configurator/banner je slozena sekce s media/visual vrstvou.
358. Local konfigurator banner je zjednoduseny gradient/dekor.
359. Category configurator nema spravny right-side visual/laptop/product layer.
360. Swimspa banner ma jiny color system bez jasneho override.
361. Product detail configurator banner neni plne Figma composition.
362. Guard musi kontrolovat visual layer, ne jen `.f-configurator-cta`.
363. Pokud media chybi, musi jit do WAITING_ON_OWNER nebo Figma asset export.
364. Banner neni final pass.

### Asset/source rozhodnuti

365. Footer background je available, ale potlaceny.
366. Showroom owner photos jsou available, ale kompozice/crop nesedi.
367. Team photos nejsou napojene nebo nejsou available, musi byt WAITING_ON_OWNER.
368. Contact person photos nejsou napojene podle Figmy, musi byt WAITING_ON_OWNER nebo approved source.
369. Warranty card images chybi, musi byt WAITING_ON_OWNER nebo approved source.
370. Swatch images jsou jen castecne vyresene.
371. Product detail hero source neni jasne rozhodnuty.
372. Map source je Figma asset, ale local crop/offset je spatne.
373. Configurator visual source neni jasne napojeny.
374. Reference content muze byt real source override, ale musi byt zapsany.
374a. Reference recent query/filter musi byt context-aware; globalni all-reference vyber je povoleny jen pro explicitne schvalenou homepage/archive variantu.
375. Maintenance content source neni jasne uzavreny.

### Zaverecne blokery pred dalsim PR

376. Opravit audit guardy driv nez dalsi vizualni opravy.
377. Header top contact musi mit dark/light variantu podle pozadi.
378. Header top contact musi pouzit dynamicky `forqy_hours`/`hours_is_open` status: `.open` = zelena tecka (`#00FF80`/success token podle designu), `.closed` = cervena tecka, nikdy hardcoded permanent green.
379. Kontakt mapa musi pouzit spravny Figma crop/offset nebo real map scope.
380. Kontakt mapa nesmi mit vlastni rozhozene overlay labely, pokud nejsou ve Figme.
381. Footer musi vratit Figma landscape background.
382. `f-footer--handoff` nesmi prepisovat footer na navy block.
383. Showroom component musi byt opraven globalne.
384. Product cards media musi byt opraveny globalne.
385. Configurator/banner musi byt opraven jako shared component.
386. Product detail swatches/benefits musi byt media-source checked.
387. Kontakt/O nas/team/person photos musi byt WAITING_ON_OWNER nebo napojene.
388. Warranty cards musi dostat image layer nebo WAITING_ON_OWNER.
389. Maintenance musi dostat content-source rozhodnuti.
390. Reference module musi mit context-aware query/filter guard: `/virivky/` bez swimspa-only referenci, `/swimspa/` bez hot-tub-only referenci, product detail relevantni k produktu/kategorii.
391. Teprve potom dava smysl page-level polish.
