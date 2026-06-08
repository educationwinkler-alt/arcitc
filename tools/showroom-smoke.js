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

async function assertComputedStyle(page, selector, property, expected, label) {
  const actual = await page.locator(selector).first().evaluate((element, prop) => getComputedStyle(element).getPropertyValue(prop), property);
  assert(actual.trim() === expected, `${label}: expected ${property} ${expected}, got ${actual}`);
}

async function main() {
  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, deviceScaleFactor: 1 });

  try {
    await page.goto(`${baseUrl}/showroom/`, { waitUntil: 'networkidle' });

    await assertSource(page, '.f-showroom-hero', 'showroom-covana-interior-web', 'desktop.heroSource');
    await assertSource(page, '.f-showroom-split--first img', 'showroom-detail-web', 'desktop.splitFirstSource');
    await assertSource(page, '.f-showroom-split--second img', 'showroom-main-web', 'desktop.splitSecondSource');
    await assertSource(page, '.f-showroom-reason:nth-child(1) .f-showroom-reason__icon', 'uploads/import/figma/showroom-reason-pool.svg', 'desktop.reasonPoolIcon');
    await assertSource(page, '.f-showroom-reason:nth-child(2) .f-showroom-reason__icon', 'uploads/import/figma/showroom-reason-road.svg', 'desktop.reasonRoadIcon');
    await assertSource(page, '.f-showroom-reason:nth-child(3) .f-showroom-reason__icon', 'uploads/import/figma/showroom-reason-parking.svg', 'desktop.reasonParkingIcon');
    await assertSource(page, '.f-showroom-reason:nth-child(4) .f-showroom-reason__icon', 'uploads/import/figma/showroom-reason-coffee.svg', 'desktop.reasonCoffeeIcon');

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
    await assertComputedStyle(page, '.f-showroom-reasons__container', 'border-radius', '40px', 'desktop.reasonsCardRadius');
    await assertComputedStyle(page, '.f-showroom-reasons__container', 'box-shadow', 'rgba(0, 0, 0, 0.1) 0px 4px 4px 0px, rgba(0, 0, 0, 0.05) 0px 14px 24px 0px', 'desktop.reasonsCardShadow');
    await assertComputedStyle(page, '.f-showroom-reason p', 'font-weight', '700', 'desktop.reasonTextWeight');
    await assertBox(page, '.f-showroom-reasons__grid', { x: 313, y: 1300, width: 1293, height: 172 }, 4, 'desktop.reasonsGrid');
    await assertBox(page, '.f-showroom-reason:nth-child(1) .f-showroom-reason__icon', { x: 422.5, y: 1300, width: 63, height: 63 }, 4, 'desktop.reasonPoolIconBox');
    await assertBox(page, '.f-showroom-reason:nth-child(1) p', { x: 329, y: 1379, width: 250, height: 93 }, 4, 'desktop.reasonPoolTextBox');
    await assertBox(page, '.f-showroom-split--first img', { x: 986, y: 1662, width: 674, height: 424 }, 4, 'desktop.splitFirstImage');
    await assertBox(page, '.f-showroom-split--second img', { x: 260, y: 2224, width: 674, height: 424 }, 4, 'desktop.splitSecondImage');

    const adminSources = await page.evaluate(() => ({
      miniCta: document.querySelector('.f-showroom-mini-cta')?.getAttribute('data-content-source') || '',
      reasons: document.querySelector('.f-showroom-reasons__grid')?.getAttribute('data-content-source') || '',
      primary: document.querySelector('.f-showroom-split--first .f-showroom-split__copy')?.getAttribute('data-content-source') || '',
      secondary: document.querySelector('.f-showroom-split--second .f-showroom-split__copy')?.getAttribute('data-content-source') || '',
      contactName: document.querySelector('.f-showroom-info__item--contact strong')?.textContent.trim() || '',
      contactText: document.querySelector('.f-showroom-info__item--contact')?.textContent.trim().replace(/\s+/g, ' ') || '',
      reasonsCount: document.querySelectorAll('.f-showroom-reason').length,
    }));

    assert(adminSources.miniCta === 'showroom-meta', `desktop.miniCta source is ${adminSources.miniCta}`);
    assert(adminSources.reasons === 'showroom-meta', `desktop.reasons source is ${adminSources.reasons}`);
    assert(adminSources.primary === 'wp-editor', `desktop.primary section source is ${adminSources.primary}`);
    assert(adminSources.secondary === 'showroom-meta', `desktop.secondary section source is ${adminSources.secondary}`);
    assert(adminSources.reasonsCount === 4, `desktop: expected 4 showroom reasons, got ${adminSources.reasonsCount}`);
    assert(adminSources.contactName.includes('Tomáš Koutný'), `desktop showroom contact is ${adminSources.contactName}`);
    assert(adminSources.contactText.includes('tomas.koutny@baspa.cz'), 'desktop showroom contact is missing member email');

    await page.setViewportSize({ width: 375, height: 900 });
    await page.goto(`${baseUrl}/showroom/`, { waitUntil: 'networkidle' });

    const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
    assert(overflow <= 0, `mobile: horizontal overflow ${overflow}`);

    await assertSource(page, '.f-showroom-hero', 'showroom-covana-interior-web', 'mobile.heroSource');

    console.log('Showroom smoke passed.');
  } finally {
    await browser.close();
  }
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
