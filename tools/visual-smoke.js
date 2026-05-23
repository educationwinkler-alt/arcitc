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
  '/showroom/',
  '/podpora/',
  '/ke-stazeni/',
  '/kontakt/',
];

const screenshotPaths = [
  ['/', 'home-desktop-playwright.png'],
  ['/catalog/virivky/', 'category-virivky-desktop-playwright.png'],
  ['/product/timberwolf/', 'product-timberwolf-desktop-playwright.png'],
  ['/podpora/', 'support-desktop-playwright.png'],
  ['/kontakt/', 'contact-desktop-playwright.png'],
];

const mobileScreenshotPaths = [
  ['/', 'home-mobile-playwright.png'],
  ['/catalog/virivky/', 'category-virivky-mobile-playwright.png'],
  ['/product/timberwolf/', 'product-timberwolf-mobile-playwright.png'],
  ['/podpora/', 'support-mobile-playwright.png'],
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
  'BASPA',
  'Baspa',
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
