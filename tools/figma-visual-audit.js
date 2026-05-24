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

async function offsetBox(page, selector, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    throw new Error(`${label}: missing selector ${selector}`);
  }

  return locator.evaluate((element) => {
    const parent = element.offsetParent || element.parentElement;
    const parentRect = parent ? parent.getBoundingClientRect() : { x: 0, y: 0 };

    return {
      x: parentRect.x + element.offsetLeft,
      y: parentRect.y + element.offsetTop,
      width: element.offsetWidth,
      height: element.offsetHeight,
    };
  });
}

async function assertOffsetBox(page, selector, expected, tolerance, label) {
  const rect = await offsetBox(page, selector, label);

  assertClose(rect.x, expected.x, tolerance, `${label}.x`);
  assertClose(rect.y, expected.y, tolerance, `${label}.y`);
  assertClose(rect.width, expected.width, tolerance, `${label}.width`);
  assertClose(rect.height, expected.height, tolerance, `${label}.height`);
}

async function assertSourceContains(page, selector, expected, label) {
  const source = await page.locator(selector).first().evaluate((element) => (
    [
      element.currentSrc,
      element.src,
      element.getAttribute('data-src'),
      element.getAttribute('data-srcset'),
      getComputedStyle(element).backgroundImage,
    ].filter(Boolean).join(' ')
  ));

  if (!source.includes(expected)) {
    throw new Error(`${label}: expected source to include "${expected}", got "${source}"`);
  }
}

async function assertHtmlContains(page, path, expectedItems, forbiddenItems = []) {
  await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle' });
  const html = await page.content();

  for (const expected of expectedItems) {
    if (!html.includes(expected)) {
      throw new Error(`${path}: expected HTML to include "${expected}"`);
    }
  }

  for (const forbidden of forbiddenItems) {
    if (html.includes(forbidden)) {
      throw new Error(`${path}: forbidden generated asset source found "${forbidden}"`);
    }
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

async function auditFigmaSources(page) {
  await assertHtmlContains(page, '/catalog/virivky/', [
    'uploads/import/figma/category-vlastnosti.jpg',
    'uploads/import/figma/category-zaruka.jpg',
    'uploads/import/figma/category-configurator.png',
    'uploads/import/figma/showroom-1.png',
    'uploads/import/figma/showroom-2.png',
    'uploads/import/figma/showroom-3.png',
  ], [
    'category-vlastnosti-1024',
    'category-zaruka-1024',
    'category-configurator-1024',
    'showroom-2-1024',
  ]);

  await assertHtmlContains(page, '/kontakt/', [
    'uploads/import/figma/contact-map-showroom.png',
  ], [
    'contact-map-showroom-2048',
  ]);
}

async function auditCatalogHotTubsDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/catalog/virivky/`, { waitUntil: 'networkidle' });

  await assertBox(page, '.f-heading--term', { x: 0, y: 0, width: 1920, height: 795 }, 2, 'catalog.heading');
  await assertBox(page, '.f-heading__container', { x: 260, y: 0, width: 1400, height: 795 }, 2, 'catalog.headingContainer');
  await assertBox(page, '.f-section--category-intro', { x: 0, y: 795, width: 1920, height: 1195 }, 3, 'catalog.categoryIntroSection');
  await assertBox(page, '.f-category-intro--split', { x: 260, y: 910, width: 1400, height: 424 }, 3, 'catalog.categoryIntroFeatures');
  await assertBox(page, '.f-category-intro--reverse', { x: 260, y: 1472, width: 1400, height: 424 }, 3, 'catalog.categoryIntroWarranty');
  await assertBox(page, '.f-section--series-nav', { x: 0, y: 1990, width: 1920, height: 93 }, 3, 'catalog.seriesNavSection');
  await assertBox(page, '.f-series-nav', { x: 313, y: 2012, width: 603, height: 51 }, 4, 'catalog.seriesNav');
  await assertBox(page, '.f-products-series--custom', { x: 260, y: 2177, width: 1400, height: 1039 }, 4, 'catalog.customSeries');
  await assertBox(page, '.f-products-series--classic', { x: 260, y: 3331, width: 1400, height: 686 }, 4, 'catalog.classicSeries');
  await assertBox(page, '.f-products-series--core', { x: 260, y: 4132, width: 1400, height: 686 }, 4, 'catalog.coreSeries');
  await assertBox(page, '.f-products-series--custom .f-listing--product:nth-child(1)', { x: 615, y: 2177, width: 335, height: 333 }, 3, 'catalog.productCardOne');
  await assertBox(page, '.f-products-series--custom .f-listing--product:nth-child(2)', { x: 970, y: 2177, width: 335, height: 333 }, 3, 'catalog.productCardTwo');
  await assertBox(page, '.f-products-series--custom .f-listing--product:nth-child(3)', { x: 1325, y: 2177, width: 335, height: 333 }, 3, 'catalog.productCardThree');
  await assertBox(page, '.f-configurator-cta', { x: 260, y: 4994, width: 1400, height: 312 }, 3, 'catalog.configurator');
  await assertBox(page, '.f-showroom-panel', { x: 260, y: 5484, width: 1400, height: 525 }, 4, 'catalog.showroomPanel');
  await assertBox(page, '.f-progress-layout', { x: 264, y: 6245, width: 1392, height: 444 }, 4, 'catalog.progress');
  await assertBox(page, '.f-section--references', { x: 0, y: 6800, width: 1920, height: 422 }, 4, 'catalog.references');
  await assertBox(page, '.f-contact-cta', { x: 260, y: 7327, width: 1400, height: 455 }, 4, 'catalog.contactCta');
  await assertBox(page, '.f-footer', { x: 0, y: 7810, width: 1920, height: 605.5 }, 4, 'catalog.footer');

  await assertSourceContains(page, '.f-heading--term .f-background__image img', 'category-hero-virivky.jpg', 'catalog.heroSource');
  await assertSourceContains(page, '.f-category-intro--split .f-category-intro__image img', 'uploads/import/figma/category-vlastnosti.jpg', 'catalog.featuresSource');
  await assertSourceContains(page, '.f-category-intro--reverse .f-category-intro__image img', 'uploads/import/figma/category-zaruka.jpg', 'catalog.warrantySource');
  await assertSourceContains(page, '.f-products-series--custom .f-listing--product:nth-child(1) .f-listing__image img', 'uploads/import/figma/category-product-card-1.png', 'catalog.productCardOneSource');
  await assertSourceContains(page, '.f-products-series--custom .f-listing--product:nth-child(2) .f-listing__image img', 'uploads/import/figma/category-product-card-2.png', 'catalog.productCardTwoSource');
  await assertSourceContains(page, '.f-products-series--custom .f-listing--product:nth-child(3) .f-listing__image img', 'uploads/import/figma/category-product-card-3.png', 'catalog.productCardThreeSource');
  await assertSourceContains(page, '.f-configurator-cta__image', 'uploads/import/figma/category-configurator.png', 'catalog.configuratorSource');
  await assertSourceContains(page, '.f-showroom-panel__image--1 img', 'uploads/import/figma/showroom-1.png', 'catalog.showroomOneSource');
  await assertSourceContains(page, '.f-showroom-panel__image--2 img', 'uploads/import/figma/showroom-3.png', 'catalog.showroomTwoSource');
  await assertSourceContains(page, '.f-showroom-panel__image--3 img', 'uploads/import/figma/showroom-2.png', 'catalog.showroomThreeSource');
}

async function auditTimberwolfDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/product/timberwolf/`, { waitUntil: 'networkidle' });

  await assertBox(page, '.f-heading--product-detail', { x: 0, y: 0, width: 1920, height: 795 }, 2, 'timberwolf.heading');
  await assertBox(page, '.f-heading__container', { x: 260, y: 0, width: 1400, height: 795 }, 2, 'timberwolf.headingContainer');
  await assertBox(page, '.f-product-detail-config__layout', { x: 260, y: 940, width: 1400, height: 546 }, 3, 'timberwolf.configLayout');
  await assertBox(page, '.f-product-configurations', { x: 260, y: 940, width: 1132, height: 546 }, 3, 'timberwolf.configurations');
  await assertBox(page, '.f-product-configuration:nth-child(1)', { x: 260, y: 1041, width: 1132, height: 203 }, 3, 'timberwolf.configurationPrestige');
  await assertBox(page, '.f-product-configuration:nth-child(2)', { x: 260, y: 1283, width: 1132, height: 203 }, 3, 'timberwolf.configurationSignature');
  await assertBox(page, '.f-product-contact-card', { x: 1362, y: 934, width: 298, height: 341 }, 3, 'timberwolf.contactCard');
  await assertBox(page, '.f-product-contact-card__details', { x: 1392, y: 1018, width: 233, height: 70.6 }, 3, 'timberwolf.contactDetails');
  await assertBox(page, '.f-product-contact-card__avatar', { x: 1392, y: 1115, width: 58, height: 58 }, 2, 'timberwolf.contactAvatar');
  await assertBox(page, '.f-product-contact-card__button', { x: 1392, y: 1195, width: 149, height: 50 }, 2, 'timberwolf.contactButton');
  await assertBox(page, '.f-product-detail-configurator', { x: 260, y: 1608, width: 1400, height: 312 }, 3, 'timberwolf.configurator');
  await assertBox(page, '.f-product-colors', { x: 260, y: 2022, width: 1400, height: 739 }, 4, 'timberwolf.colors');
  await assertBox(page, '.f-section--product-benefits', { x: 0, y: 2866, width: 1920, height: 2017 }, 4, 'timberwolf.benefits');
  await assertBox(page, '.f-section--product-options', { x: 0, y: 4883, width: 1920, height: 1144 }, 4, 'timberwolf.options');
  await assertBox(page, '.f-section--references', { x: 0, y: 6027, width: 1920, height: 525 }, 4, 'timberwolf.references');
  await assertBox(page, '.f-contact-cta', { x: 260, y: 6552, width: 1400, height: 455 }, 4, 'timberwolf.contactCta');
  await assertBox(page, '.f-footer', { x: 0, y: 7035, width: 1920, height: 605.5 }, 4, 'timberwolf.footer');

  await assertSourceContains(page, '.f-heading--product-detail .f-gallery__slide:nth-child(1) img', 'detail-timberwolf-hero.jpg', 'timberwolf.heroSource');
  await assertSourceContains(page, '.f-product-configuration:nth-child(1) .f-product-configuration__thumb img', 'detail-timberwolf-prestige', 'timberwolf.prestigeSource');
  await assertSourceContains(page, '.f-product-configuration:nth-child(2) .f-product-configuration__thumb img', 'detail-timberwolf-signature', 'timberwolf.signatureSource');
  await assertSourceContains(page, '.f-product-detail-configurator .f-configurator-cta__image', 'uploads/import/figma/category-configurator.png', 'timberwolf.configuratorSource');
  await assertSourceContains(page, '.f-product-contact-card__avatar img', 'uploads/import/figma/contact-lukas-dusek.png', 'timberwolf.contactAvatarSource');
}

async function auditContactDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/kontakt/`, { waitUntil: 'networkidle' });

  await assertBox(page, '.f-heading', { x: 0, y: 0, width: 1920, height: 430 }, 2, 'contact.heading');
  await assertBox(page, '.f-heading__headline', { x: 260, y: 206, width: 672, height: 122 }, 3, 'contact.headingHeadline');
  await assertBox(page, '.f-heading__contacts', { x: 970, y: 220, width: 360, height: 118 }, 3, 'contact.headingContacts');
  await assertBox(page, '.f-heading__buttons', { x: 1424, y: 211, width: 235, height: 126 }, 3, 'contact.headingButtons');
  await assertBox(page, '.f-section--map', { x: 0, y: 430, width: 1920, height: 782 }, 2, 'contact.mapSection');
  await assertBox(page, '.f-local-map__image', { x: -595, y: 430, width: 3110, height: 782 }, 3, 'contact.mapImage');
  await assertBox(page, '.f-local-map__card', { x: 260, y: 561, width: 565, height: 491 }, 3, 'contact.mapCard');
  await assertOffsetBox(page, '.f-local-map__pin', { x: 1226, y: 786, width: 42, height: 42 }, 2, 'contact.mapPin');
  await assertBox(page, '.f-contact-card:nth-child(1)', { x: 260, y: 1399, width: 453, height: 280 }, 3, 'contact.cardOne');
  await assertBox(page, '.f-contact-card:nth-child(2)', { x: 733, y: 1399, width: 453, height: 280 }, 3, 'contact.cardTwo');
  await assertBox(page, '.f-contact-card:nth-child(3)', { x: 1206, y: 1399, width: 453, height: 280 }, 3, 'contact.cardThree');
  await assertBox(page, '.f-contact-card:nth-child(4)', { x: 260, y: 1704, width: 453, height: 280 }, 3, 'contact.cardFour');
  await assertBox(page, '.f-billing-box', { x: 260, y: 2071, width: 507, height: 310 }, 3, 'contact.billing');
  await assertBox(page, '.f-footer', { x: 0, y: 2425, width: 1920, height: 605.5 }, 4, 'contact.footer');

  await assertSourceContains(page, '.f-local-map__image', 'uploads/import/figma/contact-map-showroom.png', 'contact.mapSource');
  await assertSourceContains(page, '.f-contact-card:nth-child(1) .f-contact-card__avatar img', 'uploads/import/figma/contact-lukas-dusek.png', 'contact.cardAvatarSource');
}

(async () => {
  const browser = await chromium.launch({ executablePath: chromePath });
  const page = await browser.newPage({ deviceScaleFactor: 1 });

  try {
    await auditDesktop(page);
    await auditMobile(page);
    await auditFigmaSources(page);
    await auditCatalogHotTubsDesktop(page);
    await auditTimberwolfDesktop(page);
    await auditContactDesktop(page);
    console.log('Figma visual audit passed.');
  } finally {
    await browser.close();
  }
})();
