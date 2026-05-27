const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const screenshotDir = 'docs/screenshots';
const writeScreenshots = process.env.VISUAL_SMOKE_WRITE_SCREENSHOTS === '1';

const paths = [
  '/',
  '/virivky/',
  '/swimspa/',
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
  ['/virivky/', 'category-virivky-desktop-playwright.png'],
  ['/swimspa/', 'category-swimspa-desktop-playwright.png'],
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
  ['/virivky/', 'category-virivky-mobile-playwright.png'],
  ['/swimspa/', 'category-swimspa-mobile-playwright.png'],
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
  'Lorem ipsum',
  'Hello world!',
  'Sample Page',
  'Hello Pattern',
  'example.com',
  'bude dopln',
];

const mojibakeNeedles = [
  '\u00c4',
  '\u0102',
  '\u0139',
  '\u00c5',
  '\u00c2',
  '\u00e2',
  '\ufffd',
];

const forbiddenBrand = [
  'BASPA',
  'Baspa',
];

const homepagePromoText = 'Akční nabídka skladových vířivek';
const legacyPromoTexts = [
  'Vyprodej skladovych virivek',
  'Výprodej skladových vířivek',
  'Výprodej vířivek',
  'Výprodej bazénů',
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
      const htmlWithoutFooter = html.replace(/<footer[^>]*f-footer--arctic[\s\S]*?<\/footer>/i, '');
      const hasHeroPromo = html.includes('<aside class="f-hero-promo"');
      const hasJucraWrapper = html.includes('data-jucra-model=');
      let resolvedPathname = path;
      try {
        resolvedPathname = new URL(page.url()).pathname;
      } catch (error) {
        resolvedPathname = path;
      }
      const hits = forbidden.filter((needle) => html.includes(needle));
      const mojibakeHits = mojibakeNeedles.filter((needle) => html.includes(needle));
      const brandHits = forbiddenBrand.filter((needle) => htmlWithoutFooter.includes(needle));
      if (brandHits.length && !isAllowedLegalEntity(path, html)) {
        hits.push(...brandHits);
      }
      if (mojibakeHits.length) {
        hits.push(...mojibakeHits.map((needle) => `mojibake U+${needle.charCodeAt(0).toString(16).toUpperCase()}`));
      }
      const legacyPromoHits = legacyPromoTexts.filter((text) => html.includes(text));
      if (legacyPromoHits.length) {
        hits.push(...legacyPromoHits);
      }
      if (hits.length) {
        throw new Error(`${path} contains forbidden strings: ${hits.join(', ')}`);
      }

      if (html.includes('f-footer--arctic') && !html.includes('BASPA s.r.o.')) {
        throw new Error(`${path} footer is missing BASPA s.r.o. copyright.`);
      }

      if (resolvedPathname === '/') {
        if (!hasHeroPromo) {
          throw new Error('Homepage is missing the hero promo component.');
        }

        if (!html.includes(homepagePromoText)) {
          throw new Error(`Homepage promo text mismatch. Expected: ${homepagePromoText}`);
        }

      } else if (hasHeroPromo) {
        throw new Error(`${path} should not render the hero promo component outside homepage (resolved to ${resolvedPathname}).`);
      }

      if (html.includes('[visao_viewer')) {
        throw new Error(`${path} contains unresolved visao_viewer shortcode text.`);
      }

      if (path === '/product/timberwolf/' && !hasJucraWrapper && !html.includes('category-configurator.png')) {
        throw new Error('/product/timberwolf/ configurator section is missing both Jucra viewer wrapper and fallback image.');
      }

      const canonical = await page.locator('link[rel="canonical"]').first().getAttribute('href');
      if (!canonical || !canonical.startsWith(baseUrl)) {
        throw new Error(`${path} is missing a local canonical URL.`);
      }

      const description = await page.locator('meta[name="description"]').first().getAttribute('content');
      if (!description || description.trim().length < 40) {
        throw new Error(`${path} is missing a useful meta description.`);
      }

      for (const property of ['og:title', 'og:description', 'og:url', 'og:image']) {
        const value = await page.locator(`meta[property="${property}"]`).first().getAttribute('content');
        if (!value || value.trim().length < 5) {
          throw new Error(`${path} is missing ${property}.`);
        }
      }

      const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
      if (overflow > 2) {
        throw new Error(`${path} has horizontal overflow of ${overflow}px on desktop.`);
      }
    }

    if (externalRequests.size) {
      throw new Error(`External browser requests detected: ${Array.from(externalRequests).join(', ')}`);
    }

    const robotsResponse = await page.goto(`${baseUrl}/robots.txt`, { waitUntil: 'domcontentloaded' });
    const robotsText = robotsResponse ? await robotsResponse.text() : '';
    if (!robotsResponse || robotsResponse.status() >= 400 || !robotsText.includes(`Sitemap: ${baseUrl}/wp-sitemap.xml`)) {
      throw new Error('robots.txt is missing the local WordPress sitemap reference.');
    }

    const sitemapResponse = await page.goto(`${baseUrl}/wp-sitemap.xml`, { waitUntil: 'domcontentloaded' });
    const sitemapText = sitemapResponse ? await sitemapResponse.text() : '';
    if (!sitemapResponse || sitemapResponse.status() >= 400 || !sitemapText.includes('<sitemapindex') || !sitemapText.includes(`${baseUrl}/wp-sitemap-posts-page-1.xml`)) {
      throw new Error('wp-sitemap.xml is missing the expected sitemap index.');
    }

    const notFoundPath = '/arctic-smoke-missing-page/';
    const notFoundResponse = await page.goto(`${baseUrl}${notFoundPath}`, { waitUntil: 'networkidle' });
    const notFoundHtml = await page.content();
    if (!notFoundResponse || notFoundResponse.status() !== 404) {
      throw new Error('Missing page smoke route did not return HTTP 404.');
    }
    for (const expected of ['Stránka nenalezena', 'Zpět na úvod']) {
      if (!notFoundHtml.includes(expected)) {
        throw new Error(`404 page is missing expected Czech text: ${expected}`);
      }
    }
    for (const stale of ['Page not Found', 'Back to Homepage', 'It looks like nothing was found']) {
      if (notFoundHtml.includes(stale)) {
        throw new Error(`404 page still contains stale English text: ${stale}`);
      }
    }

    if (writeScreenshots) {
      for (const [path, fileName] of screenshotPaths) {
        await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle' });
        await page.screenshot({ path: `${screenshotDir}/${fileName}`, fullPage: false });
      }
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
      if (writeScreenshots) {
        await mobile.screenshot({ path: `${screenshotDir}/${fileName}`, fullPage: false });
      }
    }

    if (externalRequests.size) {
      throw new Error(`External browser requests detected: ${Array.from(externalRequests).join(', ')}`);
    }

    if (writeScreenshots) {
      console.log('Visual smoke passed. Screenshots updated.');
    } else {
      console.log('Visual smoke passed. Screenshot writes skipped (set VISUAL_SMOKE_WRITE_SCREENSHOTS=1 to update).');
    }
  } finally {
    await browser.close();
  }
})();
