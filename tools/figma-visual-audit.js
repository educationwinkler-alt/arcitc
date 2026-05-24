const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

function round(value) {
  return Math.round(value * 10) / 10;
}

function assertClose(actual, expected, tolerance, label) {
  if (Math.abs(actual - expected) > tolerance) {
    throw new Error(`${label}: expected ${expected} +/- ${tolerance}, got ${round(actual)}`);
  }
}

async function box(page, selector, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    throw new Error(`${label}: missing selector ${selector}`);
  }

  const rect = await locator.boundingBox();

  if (!rect) {
    throw new Error(`${label}: selector ${selector} has no bounding box`);
  }

  return rect;
}

async function assertBox(page, selector, expected, tolerance, label) {
  const rect = await box(page, selector, label);

  assertClose(rect.x, expected.x, tolerance, `${label}.x`);
  assertClose(rect.y, expected.y, tolerance, `${label}.y`);
  assertClose(rect.width, expected.width, tolerance, `${label}.width`);
  assertClose(rect.height, expected.height, tolerance, `${label}.height`);
}

async function assertSourceContains(page, selector, expected, label) {
  const source = await page.locator(selector).first().evaluate((element) => (
    element.currentSrc || element.src || getComputedStyle(element).backgroundImage || ''
  ));

  if (!source.includes(expected)) {
    throw new Error(`${label}: expected source to include "${expected}", got "${source}"`);
  }
}

async function auditDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(baseUrl, { waitUntil: 'networkidle' });

  await assertBox(page, '.f-header__container', { x: 260, y: 18, width: 1400, height: 105 }, 2, 'desktop.headerContainer');
  await assertBox(page, '.f-logo__img', { x: 289, y: 24, width: 148, height: 83 }, 2, 'desktop.logo');
  await assertBox(page, '.f-header .js-off__container', { x: 615, y: 69, width: 639, height: 24 }, 3, 'desktop.navigation');
  await assertBox(page, '.f-header .f-search__trigger', { x: 1229.5, y: 68.5, width: 24, height: 24 }, 2, 'desktop.search');
  await assertBox(page, '.f-header__button .a-button', { x: 1431, y: 56, width: 208, height: 50 }, 2, 'desktop.headerButton');
  await assertBox(page, '.f-section--slides', { x: 0, y: 0, width: 1920, height: 795 }, 2, 'desktop.heroSection');
  await assertBox(page, '.f-caption', { x: 266, y: 280, width: 488, height: 309 }, 3, 'desktop.heroCaption');
  await assertBox(page, '.f-hero-promo', { x: 1699, y: 593, width: 268, height: 288 }, 3, 'desktop.heroPromo');
  await assertBox(page, '.f-hero-promo__image', { x: 1731, y: 593, width: 174, height: 131 }, 3, 'desktop.heroPromoImage');
  await assertBox(page, '.f-category:nth-child(1)', { x: 258, y: 866, width: 674, height: 424 }, 3, 'desktop.categoryHotTubs');
  await assertBox(page, '.f-category:nth-child(2)', { x: 986, y: 866, width: 674, height: 424 }, 3, 'desktop.categorySwimspa');

  await assertSourceContains(page, '.f-logo__img', 'images/logo.svg', 'desktop.logoSource');
  await assertSourceContains(page, '.template--homepage .f-slide--1 .f-slide__background', 'uploads/import/figma/hp-hero-arctic-spas-07.jpg', 'desktop.heroBackgroundSource');
  await assertSourceContains(page, '.f-slide__background img', 'uploads/import/figma/hp-hero-arctic-spas-07.jpg', 'desktop.heroImageSource');
  await assertSourceContains(page, '.f-hero-promo__image', 'uploads/import/figma/hp-fixed-banner-product.png', 'desktop.heroPromoImageSource');
}

async function auditMobile(page) {
  await page.setViewportSize({ width: 390, height: 900 });
  await page.goto(baseUrl, { waitUntil: 'networkidle' });

  await assertBox(page, '.f-logo__img', { x: 20, y: 7, width: 85.6, height: 48 }, 2, 'mobile.logo');
  await assertBox(page, '.f-navigation__trigger', { x: 325, y: 8.5, width: 45, height: 45 }, 2, 'mobile.menuButton');
  await assertBox(page, '.f-section--slides', { x: 0, y: 0, width: 390, height: 842 }, 2, 'mobile.heroSection');
  await assertBox(page, '.f-hero-promo', { x: 20, y: 562, width: 335, height: 288 }, 3, 'mobile.heroPromo');
  await assertBox(page, '.f-category:nth-child(1)', { x: 27.5, y: 842, width: 335, height: 221 }, 3, 'mobile.categoryHotTubs');
  await assertBox(page, '.f-category:nth-child(2)', { x: 27.5, y: 1081, width: 335, height: 221 }, 3, 'mobile.categorySwimspa');

  await assertSourceContains(page, '.f-logo__img', 'images/logo.svg', 'mobile.logoSource');
  await assertSourceContains(page, '.f-hero-promo__image', 'uploads/import/figma/hp-fixed-banner-product.png', 'mobile.heroPromoImageSource');
}

(async () => {
  const browser = await chromium.launch({ executablePath: chromePath });
  const page = await browser.newPage({ deviceScaleFactor: 1 });

  try {
    await auditDesktop(page);
    await auditMobile(page);
    console.log('Figma visual audit passed.');
  } finally {
    await browser.close();
  }
})();
