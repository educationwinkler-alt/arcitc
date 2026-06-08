const fs = require('fs');
const path = require('path');
const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const outputDir = path.join(process.cwd(), 'docs', 'screenshots', 'footer-offers-copyright-2026-06-08');

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

function normalizeText(value) {
  return String(value || '').replace(/\s+/g, ' ').trim();
}

async function readPage(page, pathname) {
  const response = await page.goto(`${baseUrl}${pathname}`, { waitUntil: 'networkidle', timeout: 90000 });

  assert(response && response.status() < 400, `${pathname} returned ${response ? response.status() : 'no response'}`);

  return page.evaluate(() => ({
    title: document.title,
    bodyText: document.body.innerText,
    footerCopyright: document.querySelector('.f-footer__copyright')?.textContent || '',
    footerGroups: Array.from(document.querySelectorAll('.f-footer__group')).map((group) => ({
      heading: group.querySelector('h2')?.textContent || '',
      links: Array.from(group.querySelectorAll('a')).map((link) => ({
        text: link.textContent || '',
        href: link.href,
      })),
    })),
    homePromoText: document.querySelector('.f-hero-promo')?.textContent || '',
    homePromoHref: document.querySelector('.f-hero-promo a')?.href || '',
  }));
}

(async () => {
  fs.mkdirSync(outputDir, { recursive: true });

  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1440, height: 1100 }, deviceScaleFactor: 1 });

  try {
    const home = await readPage(page, '/');
    await page.screenshot({ path: path.join(outputDir, 'local-home-footer-offers.png'), fullPage: true });

    const offers = await readPage(page, '/akcni-nabidky/');
    await page.screenshot({ path: path.join(outputDir, 'local-offers-archive.png'), fullPage: true });

    const footerCopyright = normalizeText(home.footerCopyright);
    const homeBody = normalizeText(home.bodyText);
    const offersBody = normalizeText(offers.bodyText);
    const homePromoText = normalizeText(home.homePromoText);
    const footerHotTubGroup = home.footerGroups.find((group) => normalizeText(group.heading) === 'Vířivky');
    const footerInfoGroup = home.footerGroups.find((group) => normalizeText(group.heading) === 'Další informace');

    assert(footerCopyright.includes('BASPA s.r.o.'), `footer copyright should name BASPA s.r.o., got: ${footerCopyright}`);
    assert(!footerCopyright.includes('Arctic Spas CZ'), `footer copyright must not name Arctic Spas CZ: ${footerCopyright}`);
    assert(footerHotTubGroup, 'footer hot tub group is missing');
    assert(!footerHotTubGroup.links.some((link) => normalizeText(link.text) === 'Skladové vířivky'), 'footer Vířivky group still contains Skladové vířivky');
    assert(footerInfoGroup && footerInfoGroup.links.some((link) => normalizeText(link.text) === 'Akční nabídky'), 'footer Další informace group is missing Akční nabídky');
    assert(homePromoText.includes('Akční nabídky'), `homepage promo should say Akční nabídky, got: ${homePromoText}`);
    assert(home.homePromoHref.includes('/akcni-nabidky/'), `homepage promo should link to /akcni-nabidky/, got: ${home.homePromoHref}`);
    assert(!homeBody.includes('Výprodej skladových vířivek'), 'homepage still contains Výprodej skladových vířivek');
    assert(!offersBody.includes('Výprodej skladových vířivek'), 'offers archive still contains Výprodej skladových vířivek');
    assert(offersBody.includes('Akční nabídky'), 'offers archive should contain Akční nabídky');

    const audit = {
      baseUrl,
      footerCopyright,
      homePromoText,
      homePromoHref: home.homePromoHref,
      footerHotTubLinks: footerHotTubGroup.links.map((link) => normalizeText(link.text)),
      footerInfoLinks: footerInfoGroup.links.map((link) => normalizeText(link.text)),
      screenshots: [
        'local-home-footer-offers.png',
        'local-offers-archive.png',
      ],
    };

    fs.writeFileSync(path.join(outputDir, 'audit.json'), `${JSON.stringify(audit, null, 2)}\n`);
    console.log('Footer/offers/copyright smoke passed.');
  } finally {
    await browser.close();
  }
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
