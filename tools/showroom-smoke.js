const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

function round(value) {
  return Math.round(value * 10) / 10;
}

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

function assertClose(actual, expected, tolerance, label) {
  assert(Math.abs(actual - expected) <= tolerance, `${label}: expected ${expected} +/- ${tolerance}, got ${round(actual)}`);
}

async function box(page, selector, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  assert(count > 0, `${label}: missing selector ${selector}`);

  const rect = await locator.boundingBox();

  assert(rect, `${label}: selector ${selector} has no bounding box`);

  return rect;
}

async function assertBox(page, selector, expected, tolerance, label) {
  const rect = await box(page, selector, label);

  assertClose(rect.x, expected.x, tolerance, `${label}.x`);
  assertClose(rect.y, expected.y, tolerance, `${label}.y`);
  assertClose(rect.width, expected.width, tolerance, `${label}.width`);
  assertClose(rect.height, expected.height, tolerance, `${label}.height`);
}

async function source(page, selector) {
  return page.locator(selector).first().evaluate((element) => (
    [
      element.currentSrc,
      element.src,
      element.getAttribute('data-src'),
      element.getAttribute('data-srcset'),
      getComputedStyle(element).backgroundImage,
    ].filter(Boolean).join(' ')
  ));
}

async function assertSource(page, selector, expected, label) {
  const actual = await source(page, selector);
  assert(actual.includes(expected), `${label}: expected source to include ${expected}, got ${actual}`);
}

async function main() {
  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, deviceScaleFactor: 1 });

  try {
    await page.goto(`${baseUrl}/showroom/`, { waitUntil: 'networkidle' });

    await assertSource(page, '.f-showroom-hero', 'uploads/import/owner-showroom/showroom-covana-interior-web.jpg', 'desktop.heroSource');
    await assertSource(page, '.f-showroom-split--first img', 'uploads/import/owner-showroom/showroom-detail-web.jpg', 'desktop.splitFirstSource');
    await assertSource(page, '.f-showroom-split--second img', 'uploads/import/owner-showroom/showroom-main-web.jpg', 'desktop.splitSecondSource');

    const html = await page.content();
    for (const forbidden of [
      'uploads/import/figma/showroom-hero-bazeny.jpg',
      'uploads/import/figma/showroom-detail-bazeny.png',
      'uploads/import/figma/showroom-detail-virivky.png',
    ]) {
      assert(!html.includes(forbidden), `desktop: forbidden design-only showroom asset rendered: ${forbidden}`);
    }

    await assertBox(page, '.f-showroom-hero', { x: 0, y: 0, width: 1920, height: 801 }, 4, 'desktop.hero');
    await assertBox(page, '.f-showroom-mini-cta', { x: 1167, y: 725, width: 498, height: 299 }, 4, 'desktop.miniCta');
    await assertBox(page, '.f-showroom-reasons__grid', { x: 313, y: 1300, width: 1293, height: 172 }, 4, 'desktop.reasonsGrid');
    await assertBox(page, '.f-showroom-split--first img', { x: 986, y: 1662, width: 674, height: 424 }, 4, 'desktop.splitFirstImage');
    await assertBox(page, '.f-showroom-split--second img', { x: 260, y: 2224, width: 674, height: 424 }, 4, 'desktop.splitSecondImage');

    await page.setViewportSize({ width: 375, height: 900 });
    await page.goto(`${baseUrl}/showroom/`, { waitUntil: 'networkidle' });

    const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
    assert(overflow <= 0, `mobile: horizontal overflow ${overflow}`);

    await assertSource(page, '.f-showroom-hero', 'uploads/import/owner-showroom/showroom-covana-interior-web.jpg', 'mobile.heroSource');

    console.log('Showroom smoke passed.');
  } finally {
    await browser.close();
  }
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
