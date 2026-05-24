const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const screenshotDir = 'docs/screenshots';

const paths = [
  '/',
  '/catalog/virivky/',
  '/catalog/swimspa/',
  '/catalog/dalsi-sortiment/',
  '/product/timberwolf/',
  '/product/husky/',
  '/product/athabascan/',
  '/product/covana/',
  '/vlastnosti/',
  '/vlastnosti/izolace-virivky/',
  '/dalsi-informace/',
  '/sluzby/',
  '/certifikaty/',
  '/zaruka/',
  '/kolik-stoji-udrzba/',
  '/o-nas/',
  '/reference/',
  '/servis/',
  '/showroom/',
  '/podpora/',
  '/ke-stazeni/',
  '/kontakt/',
];

const screenshotPaths = [
  ['/', 'home-desktop-playwright.png'],
  ['/catalog/virivky/', 'category-virivky-desktop-playwright.png'],
  ['/catalog/swimspa/', 'category-swimspa-desktop-playwright.png'],
  ['/catalog/dalsi-sortiment/', 'category-dalsi-sortiment-desktop-playwright.png'],
  ['/product/timberwolf/', 'product-timberwolf-desktop-playwright.png'],
  ['/product/husky/', 'product-husky-desktop-playwright.png'],
  ['/product/athabascan/', 'product-athabascan-desktop-playwright.png'],
  ['/product/covana/', 'product-covana-desktop-playwright.png'],
  ['/vlastnosti/', 'features-desktop-playwright.png'],
  ['/vlastnosti/izolace-virivky/', 'feature-insulation-desktop-playwright.png'],
  ['/dalsi-informace/', 'more-info-desktop-playwright.png'],
  ['/sluzby/', 'services-desktop-playwright.png'],
  ['/certifikaty/', 'certificates-desktop-playwright.png'],
  ['/zaruka/', 'warranty-desktop-playwright.png'],
  ['/kolik-stoji-udrzba/', 'maintenance-desktop-playwright.png'],
  ['/o-nas/', 'about-desktop-playwright.png'],
  ['/reference/', 'references-desktop-playwright.png'],
  ['/servis/', 'service-request-desktop-playwright.png'],
  ['/podpora/', 'support-desktop-playwright.png'],
  ['/ke-stazeni/', 'downloads-desktop-playwright.png'],
  ['/showroom/', 'showroom-desktop-playwright.png'],
  ['/kontakt/', 'contact-desktop-playwright.png'],
];

const mobileScreenshotPaths = [
  ['/', 'home-mobile-playwright.png'],
  ['/catalog/virivky/', 'category-virivky-mobile-playwright.png'],
  ['/catalog/swimspa/', 'category-swimspa-mobile-playwright.png'],
  ['/catalog/dalsi-sortiment/', 'category-dalsi-sortiment-mobile-playwright.png'],
  ['/product/timberwolf/', 'product-timberwolf-mobile-playwright.png'],
  ['/product/husky/', 'product-husky-mobile-playwright.png'],
  ['/product/athabascan/', 'product-athabascan-mobile-playwright.png'],
  ['/product/covana/', 'product-covana-mobile-playwright.png'],
  ['/vlastnosti/', 'features-mobile-playwright.png'],
  ['/vlastnosti/izolace-virivky/', 'feature-insulation-mobile-playwright.png'],
  ['/dalsi-informace/', 'more-info-mobile-playwright.png'],
  ['/sluzby/', 'services-mobile-playwright.png'],
  ['/certifikaty/', 'certificates-mobile-playwright.png'],
  ['/zaruka/', 'warranty-mobile-playwright.png'],
  ['/kolik-stoji-udrzba/', 'maintenance-mobile-playwright.png'],
  ['/o-nas/', 'about-mobile-playwright.png'],
  ['/reference/', 'references-mobile-playwright.png'],
  ['/servis/', 'service-request-mobile-playwright.png'],
  ['/podpora/', 'support-mobile-playwright.png'],
  ['/ke-stazeni/', 'downloads-mobile-playwright.png'],
  ['/showroom/', 'showroom-mobile-playwright.png'],
  ['/kontakt/', 'contact-mobile-playwright.png'],
];

const externalRequests = new Set();
const allowedHosts = new Set(['localhost', '127.0.0.1', '::1']);

function trackExternalRequests(page) {
  page.on('request', (request) => {
    let url;
    try {
      url = new URL(request.url());
    } catch (error) {
      return;
    }

    if ((url.protocol === 'http:' || url.protocol === 'https:') && !allowedHosts.has(url.hostname)) {
      externalRequests.add(request.url());
    }
  });
}

const forbidden = [
  'baspa.cz',
  'smartsuppchat',
  'connect.facebook.net',
  'static.hotjar.com',
  'clarity.ms',
  'c.seznam.cz',
  'api2.ecomailapp.cz',
  'google.com/maps/embed',
  'fonts.googleapis.com',
  'fonts.gstatic.com',
  'api.fontshare.com',
  'cdn.fontshare.com',
  'eboost.cz',
  'pavelrichter.cz',
  'wdsgn.agency',
  'updates.pavelrichter.cz',
];

const forbiddenBrand = [
  'BASPA',
  'Baspa',
];

function isAllowedLegalEntity(path, html) {
  return path === '/kontakt/' && html.includes('BASPA s.r.o.');
}

(async () => {
  const browser = await chromium.launch({ executablePath: chromePath });

  try {
    const page = await browser.newPage({ viewport: { width: 1440, height: 1100 } });
    trackExternalRequests(page);

    for (const path of paths) {
      const response = await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle' });
      if (!response || response.status() >= 400) {
        throw new Error(`${path} returned ${response ? response.status() : 'no response'}`);
      }

      const html = await page.content();
      const hits = forbidden.filter((needle) => html.includes(needle));
      const brandHits = forbiddenBrand.filter((needle) => html.includes(needle));
      if (brandHits.length && !isAllowedLegalEntity(path, html)) {
        hits.push(...brandHits);
      }
      if (hits.length) {
        throw new Error(`${path} contains forbidden strings: ${hits.join(', ')}`);
      }

      const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
      if (overflow > 2) {
        throw new Error(`${path} has horizontal overflow of ${overflow}px on desktop.`);
      }
    }

    if (externalRequests.size) {
      throw new Error(`External browser requests detected: ${Array.from(externalRequests).join(', ')}`);
    }

    for (const [path, fileName] of screenshotPaths) {
      await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle' });
      await page.screenshot({ path: `${screenshotDir}/${fileName}`, fullPage: false });
    }

    const mobile = await browser.newPage({ viewport: { width: 390, height: 900 }, deviceScaleFactor: 1 });
    trackExternalRequests(mobile);
    await mobile.goto(baseUrl, { waitUntil: 'networkidle' });
    const navBox = await mobile.locator('.f-navigation__trigger').boundingBox();
    if (!navBox || navBox.width < 32 || navBox.height < 32) {
      throw new Error('Mobile navigation trigger is not visible enough.');
    }

    for (const [path, fileName] of mobileScreenshotPaths) {
      await mobile.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle' });
      const overflow = await mobile.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
      if (overflow > 2) {
        throw new Error(`${path} has horizontal overflow of ${overflow}px on mobile.`);
      }
      await mobile.screenshot({ path: `${screenshotDir}/${fileName}`, fullPage: false });
    }

    if (externalRequests.size) {
      throw new Error(`External browser requests detected: ${Array.from(externalRequests).join(', ')}`);
    }

    console.log('Visual smoke passed.');
  } finally {
    await browser.close();
  }
})();
