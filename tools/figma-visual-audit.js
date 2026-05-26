const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

function round(value) {
  return Math.round(value * 10) / 10;
}

function desktopScale(width) {
  if (width >= 1400 && width <= 1919) {
    return Math.min(1, (width - 40) / 1400);
  }

  return 1;
}

function heroScale(width) {
  if (width >= 1400 && width <= 1919) {
    return width / 1920;
  }

  return 1;
}

function promoBox(width) {
  const scale = heroScale(width);
  const promoWidth = 268 * scale;
  const originalX = ((width - (1920 * scale)) / 2) + (1699 * scale);
  const safeX = width - promoWidth - 20;

  return {
    x: Math.min(originalX, safeX),
    y: 593 * scale,
    width: promoWidth,
    height: 288 * scale,
    scale,
  };
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

async function assertComputedStyle(page, selector, property, expected, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    throw new Error(`${label}: missing selector ${selector}`);
  }

  const actual = await locator.evaluate((element, propertyName) => getComputedStyle(element).getPropertyValue(propertyName), property);

  if (actual.trim() !== expected) {
    throw new Error(`${label}: expected ${property} to be "${expected}", got "${actual}"`);
  }
}

async function assertHtmlContains(page, path, expectedItems, forbiddenItems = []) {
  await page.goto(`${baseUrl}${path}`, { waitUntil: 'load' });
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

async function assertNoHorizontalOverflow(page, label) {
  const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);

  if (overflow > 2) {
    throw new Error(`${label}: horizontal overflow ${overflow}px`);
  }
}

async function assertHiddenBox(page, selector, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    return;
  }

  const rect = await locator.boundingBox();
  if (rect && rect.width > 1 && rect.height > 1) {
    throw new Error(`${label}: expected ${selector} to be hidden, got ${round(rect.width)}x${round(rect.height)}`);
  }
}

async function assertInsideViewport(page, selector, width, label, inset = 0) {
  const rect = await box(page, selector, label);

  if (rect.x < inset || rect.x + rect.width > width - inset) {
    throw new Error(`${label}: expected ${selector} inside viewport ${width}px, got x=${round(rect.x)}, width=${round(rect.width)}`);
  }
}

async function assertCompactHeader(page, width, path) {
  const label = `responsive:${width}:${path}`;
  await assertNoHorizontalOverflow(page, label);
  await assertBox(page, '.f-header', { x: 0, y: 0, width, height: 62 }, 2, `${label}.header`);
  await assertBox(page, '.f-logo__img', { x: 20, y: 7, width: 85.6, height: 48 }, 2, `${label}.logo`);

  const trigger = await box(page, '.f-navigation__trigger', `${label}.navigationTrigger`);
  assertClose(trigger.width, 45, 2, `${label}.navigationTrigger.width`);
  assertClose(trigger.height, 45, 2, `${label}.navigationTrigger.height`);
  assertClose(trigger.y, 8.5, 2, `${label}.navigationTrigger.y`);
  if (trigger.x < width - 95 || trigger.x + trigger.width > width - 12) {
    throw new Error(`${label}.navigationTrigger.x: expected right-aligned trigger, got ${round(trigger.x)}`);
  }

  await assertHiddenBox(page, '.f-header__search-slot .f-search__trigger', `${label}.searchTrigger`);
  await assertHiddenBox(page, '.f-header__cta-slot .a-button', `${label}.headerButton`);

  const breakpoint = await page.locator('.f-off--navigation').first().getAttribute('data-off-breakpoint');
  if (breakpoint !== '1400') {
    throw new Error(`${label}.navigationBreakpoint: expected 1400, got ${breakpoint}`);
  }
}

async function assertDesktopHeader(page, width, path) {
  const label = `responsive:${width}:${path}`;
  const scale = desktopScale(width);
  const headerWidth = Math.min(1400 * scale, width - 40);
  const headerX = (width - headerWidth) / 2;
  const headerY = 18;
  const headerHeight = 105 * scale;

  await assertNoHorizontalOverflow(page, label);
  await assertBox(page, '.f-header__container', { x: headerX, y: headerY, width: headerWidth, height: headerHeight }, 6, `${label}.headerContainer`);

  const header = await box(page, '.f-header__container', `${label}.headerContainerBox`);
  const nav = await box(page, '.f-header .js-off__container', `${label}.navigationBox`);
  const button = await box(page, '.f-header__button .a-button', `${label}.headerButtonBox`);

  if (nav.y < header.y + 40 || nav.y + nav.height > header.y + header.height + 2) {
    throw new Error(`${label}.navigationOverlap: desktop navigation is not vertically contained in header`);
  }

  if (button.x + button.width > width - 20) {
    throw new Error(`${label}.headerButtonOverflow: CTA reaches outside safe viewport`);
  }
}

async function auditResponsiveShell(page) {
  for (const width of [1903, 1600, 1536, 1456, 1440]) {
    await page.setViewportSize({ width, height: 1000 });
    for (const path of ['/', '/swimspa/', '/virivky/', '/product/timberwolf/', '/kontakt/']) {
      await page.goto(`${baseUrl}${path}`, { waitUntil: 'load' });
      await assertDesktopHeader(page, width, path);
    }
  }

  for (const width of [1366, 1280, 1024, 768, 430, 390]) {
    await page.setViewportSize({ width, height: width <= 430 ? 900 : 1000 });
    for (const path of ['/', '/swimspa/', '/virivky/', '/product/timberwolf/', '/kontakt/']) {
      await page.goto(`${baseUrl}${path}`, { waitUntil: 'load' });
      await assertCompactHeader(page, width, path);
    }
  }
}

async function auditCompactNavigation(page) {
  for (const width of [1366, 1280, 1024, 768, 430, 390]) {
    await page.setViewportSize({ width, height: width <= 430 ? 900 : 1000 });
    await page.goto(baseUrl, { waitUntil: 'load' });
    await page.locator('.f-navigation__trigger').first().click();
    await page.waitForTimeout(250);

    const active = await page.locator('.f-off--navigation.active').count();
    if (!active) {
      throw new Error(`compactNavigation:${width}: off-canvas navigation did not open`);
    }

    const visibleSubmenus = await page.locator('.f-off--navigation .f-navigation-sub, .f-off--navigation .sub-menu').evaluateAll((elements) => elements.filter((element) => {
      const style = getComputedStyle(element);
      const rect = element.getBoundingClientRect();
      return style.display !== 'none' && rect.width > 1 && rect.height > 1;
    }).length);
    if (visibleSubmenus > 0) {
      throw new Error(`compactNavigation:${width}: desktop submenu content is visible in compact navigation`);
    }

    await page.keyboard.press('Escape');
  }
}

async function assertFooterLayout(page, label, expectedY = null) {
  const footer = await box(page, '.f-footer--arctic', `${label}.footer`);
  assertClose(footer.x, 0, 2, `${label}.footer.x`);
  if (expectedY !== null) {
    assertClose(footer.y, expectedY, 4, `${label}.footer.y`);
  }
  assertClose(footer.width, 1920, 2, `${label}.footer.width`);
  assertClose(footer.height, 773, 4, `${label}.footer.height`);

  const checks = [
    ['.f-footer--arctic .f-footer__container', { x: 260, y: 0, width: 1400, height: 773 }, 'container'],
    ['.f-footer__group:nth-child(1)', { x: 260, y: 86, width: 163, height: 324 }, 'columnsHotTubs'],
    ['.f-footer__group:nth-child(2)', { x: 541, y: 86, width: 176, height: 250 }, 'columnsFeatures'],
    ['.f-footer__group:nth-child(3)', { x: 822, y: 86, width: 188, height: 340 }, 'columnsInfo'],
    ['.f-footer__quick-contact', { x: 1070, y: 60, width: 592, height: 347 }, 'quickContact'],
    ['.f-footer__quick-contact h2', { x: 1104, y: 86, width: 142, height: 26 }, 'quickTitle'],
    ['.f-footer__quick-avatar', { x: 1101, y: 144, width: 58, height: 58 }, 'quickAvatar'],
    ['.f-footer__quick-contact-body > a[href^="mailto"]', { x: 1101, y: 226, width: 233, height: 24 }, 'quickEmail'],
    ['.f-footer__quick-contact-body > a[href^="tel"]', { x: 1101, y: 253, width: 233, height: 24 }, 'quickPhone'],
    ['.f-footer__quick-hours', { x: 1101, y: 284, width: 147, height: 21 }, 'quickHours'],
    ['.f-footer__quick-contact-body .a-button', { x: 1101, y: 333, width: 208, height: 50 }, 'quickButton'],
    ['.f-footer__quick-map', { x: 1375, y: 84, width: 262, height: 299 }, 'quickMap'],
    ['.f-footer__quick-map a', { x: 1409, y: 309, width: 200, height: 50 }, 'quickMapButton'],
    ['.f-footer__bottom', { x: 260, y: 514, width: 1400, height: 57 }, 'bottom'],
    ['.f-footer__copyright', { x: 262, y: 521, width: 362, height: 34 }, 'copyright'],
    ['.f-footer__bottom .f-logo', { x: 909, y: 514, width: 102, height: 57 }, 'bottomLogo'],
    ['.f-footer__bottom > a', { x: 1343, y: 521, width: 154, height: 34 }, 'privacy'],
  ];

  for (const [selector, expected, name] of checks) {
    const rect = await box(page, selector, `${label}.${name}`);
    assertClose(rect.x, expected.x, 4, `${label}.${name}.x`);
    assertClose(rect.y - footer.y, expected.y, 4, `${label}.${name}.y`);
    assertClose(rect.width, expected.width, name === 'quickHours' ? 6 : 4, `${label}.${name}.width`);
    assertClose(rect.height, expected.height, 4, `${label}.${name}.height`);
  }

  const footerText = await page.locator('.f-footer--arctic').innerText();
  for (const expected of [
    'Vířivky',
    'Série Core',
    'Vlastnosti vířivek',
    'Další informace',
    'Rychlý kontakt',
    'lukas.dusek@arctic-spas.cz',
    '+420 777 099 687',
    'Po - Pá 8:00-17:00 h',
    'Ochrana osobních údajů',
  ]) {
    if (!footerText.includes(expected)) {
      throw new Error(`${label}.footerText: missing "${expected}"`);
    }
  }

  const flattenedFooterImages = await page.locator('.f-footer--arctic img[src*="footer-desktop"], .f-footer--arctic img[src*="footer-mobile"]').count();
  if (flattenedFooterImages > 0) {
    throw new Error(`${label}.footer: footer must not be rendered as a flattened PNG`);
  }

  const avatarImages = await page.locator('.f-footer__quick-avatar img').count();
  if (avatarImages > 0) {
    throw new Error(`${label}.footerAvatar: Figma footer avatar is a placeholder ellipse, not a photo`);
  }

  const eboostSignature = await page.locator('.f-footer--arctic .f-signature--eboost').count();
  if (eboostSignature > 0) {
    throw new Error(`${label}.footerCredit: eboost signature must not be rendered`);
  }
}

async function auditDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(baseUrl, { waitUntil: 'load' });

  const promo = promoBox(1920);

  await assertBox(page, '.f-header__container', { x: 260, y: 18, width: 1400, height: 105 }, 2, 'desktop.headerContainer');
  await assertBox(page, '.f-logo__img', { x: 289, y: 24, width: 148, height: 83 }, 2, 'desktop.logo');
  await assertBox(page, '.f-header .js-off__container', { x: 615, y: 69, width: 639, height: 24 }, 3, 'desktop.navigation');
  await assertBox(page, '.f-header .f-search__trigger', { x: 1229.5, y: 68.5, width: 24, height: 24 }, 2, 'desktop.search');
  await assertBox(page, '.f-header__button .a-button', { x: 1431, y: 56, width: 208, height: 50 }, 2, 'desktop.headerButton');
  await assertBox(page, '.f-section--slides', { x: 0, y: 0, width: 1920, height: 795 }, 2, 'desktop.heroSection');
  await assertBox(page, '.f-caption', { x: 266, y: 280, width: 488, height: 309 }, 3, 'desktop.heroCaption');
  await assertBox(page, '.f-slides__control--prev', { x: 125, y: 382, width: 42, height: 42 }, 2, 'desktop.heroPrevArrow');
  await assertBox(page, '.f-slides__control--next', { x: 1767, y: 382, width: 42, height: 42 }, 2, 'desktop.heroNextArrow');
  await assertBox(page, '.f-hero-promo', { x: promo.x, y: promo.y, width: promo.width, height: promo.height }, 3, 'desktop.heroPromo');
  await assertBox(page, '.f-hero-promo__image', { x: promo.x + (32 * promo.scale), y: promo.y, width: 174, height: 131 }, 3, 'desktop.heroPromoImage');
  await assertInsideViewport(page, '.f-hero-promo', 1920, 'desktop.heroPromoInside', 20);
  await assertBox(page, '.f-category:nth-child(1)', { x: 258, y: 866, width: 674, height: 424 }, 3, 'desktop.categoryHotTubs');
  await assertBox(page, '.f-category:nth-child(2)', { x: 986, y: 866, width: 674, height: 424 }, 3, 'desktop.categorySwimspa');
  await assertBox(page, '.template--homepage .f-page__content', { x: 584, y: 1405, width: 752, height: 232 }, 3, 'desktop.exclusiveDealer');
  await assertBox(page, '.template--homepage .f-arctic-benefits', { x: 260, y: 1703, width: 1400, height: 175 }, 3, 'desktop.benefits');
  await assertBox(page, '.template--homepage .f-arctic-benefit:nth-child(1)', { x: 260, y: 1703, width: 416, height: 175 }, 3, 'desktop.benefitInstallation');
  await assertBox(page, '.template--homepage .f-arctic-benefit:nth-child(2)', { x: 752, y: 1703, width: 416, height: 175 }, 3, 'desktop.benefitSupport');
  await assertBox(page, '.template--homepage .f-arctic-benefit:nth-child(3)', { x: 1244, y: 1703, width: 416, height: 175 }, 3, 'desktop.benefitService');
  await assertBox(page, '.template--homepage .f-section--showroom .f-section__container', { x: 260, y: 2102, width: 1400, height: 649 }, 4, 'desktop.showroomContainer');
  await assertBox(page, '.template--homepage .f-showroom-panel', { x: 260, y: 2102, width: 1400, height: 525 }, 4, 'desktop.showroomPanel');
  await assertBox(page, '.template--homepage .f-progress-layout', { x: 265, y: 2863, width: 1392, height: 444 }, 4, 'desktop.progress');
  await assertBox(page, '.template--homepage .f-section--references', { x: 0, y: 3418, width: 1920, height: 422 }, 4, 'desktop.referencesSection');
  await assertBox(page, '.template--homepage .f-section--references .f-carousel', { x: 258, y: 3520, width: 1400, height: 320 }, 4, 'desktop.referencesCarousel');
  await assertBox(page, '.template--homepage .f-contact-cta', { x: 260, y: 3945, width: 1400, height: 455 }, 4, 'desktop.contactCta');
  await assertFooterLayout(page, 'desktop', 4428);

  await assertSourceContains(page, '.f-logo__img', 'images/logo.svg', 'desktop.logoSource');
  await assertSourceContains(page, '.template--homepage .f-slide--1 .f-slide__background', 'uploads/import/figma/hp-hero-arctic-spas-07.jpg', 'desktop.heroBackgroundSource');
  await assertSourceContains(page, '.f-slide__background img', 'uploads/import/figma/hp-hero-arctic-spas-07.jpg', 'desktop.heroImageSource');
  await assertSourceContains(page, '.f-hero-promo__image', 'uploads/import/figma/hp-fixed-banner-product.png', 'desktop.heroPromoImageSource');
  await assertComputedStyle(page, '.template--homepage .f-slide--1 .f-slide__background', 'background-size', 'cover', 'desktop.heroBackgroundFit');
}

async function auditDesktopScaledMatrix(page) {
  for (const width of [1903, 1600, 1536, 1456, 1440]) {
    const scale = heroScale(width);
    const heroHeight = 795 * scale;
    const captionWidth = 488 * scale;
    const captionHeight = 309 * scale;
    const captionX = ((width - (1400 * scale)) / 2) + (6 * scale);
    const captionY = 280 * scale;
    const arrowSize = 42 * scale;
    const arrowY = 382 * scale;
    const prevArrowX = 125 * scale;
    const nextArrowX = width - (111 * scale) - arrowSize;
    const promo = promoBox(width);

    await page.setViewportSize({ width, height: 1080 });
    await page.goto(baseUrl, { waitUntil: 'load' });

    await assertNoHorizontalOverflow(page, `scaledDesktop:${width}`);
    await assertBox(page, '.f-section--slides', { x: 0, y: 0, width, height: heroHeight }, 4, `scaledDesktop:${width}.heroSection`);
    await assertBox(page, '.f-caption', { x: captionX, y: captionY, width: captionWidth, height: captionHeight }, 6, `scaledDesktop:${width}.heroCaption`);
    await assertBox(page, '.f-slides__control--prev', { x: prevArrowX, y: arrowY, width: arrowSize, height: arrowSize }, 6, `scaledDesktop:${width}.heroPrevArrow`);
    await assertBox(page, '.f-slides__control--next', { x: nextArrowX, y: arrowY, width: arrowSize, height: arrowSize }, 6, `scaledDesktop:${width}.heroNextArrow`);
    await assertBox(page, '.f-hero-promo', { x: promo.x, y: promo.y, width: promo.width, height: promo.height }, 8, `scaledDesktop:${width}.heroPromo`);
    await assertInsideViewport(page, '.f-hero-promo', width, `scaledDesktop:${width}.heroPromoInside`, 20);
    await assertComputedStyle(page, '.template--homepage .f-slide--1 .f-slide__background', 'background-size', 'cover', `scaledDesktop:${width}.heroBackgroundFit`);
  }

  for (const width of [1366, 1280]) {
    await page.setViewportSize({ width, height: 1080 });
    await page.goto(baseUrl, { waitUntil: 'load' });
    await assertHiddenBox(page, '.f-hero-promo', `scaledCompact:${width}.heroPromo`);
  }
}

async function auditMobile(page) {
  await page.setViewportSize({ width: 390, height: 900 });
  await page.goto(baseUrl, { waitUntil: 'load' });

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
  await assertHtmlContains(page, '/virivky/', [
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
  await page.goto(`${baseUrl}/virivky/`, { waitUntil: 'load' });

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
  await assertFooterLayout(page, 'catalog', 7810);

  await assertSourceContains(page, '.f-heading--term .f-background__image img', 'virivky.jpg', 'catalog.heroSource');
  await assertSourceContains(page, '.f-category-intro--split .f-category-intro__image img', 'uploads/import/figma/category-vlastnosti.jpg', 'catalog.featuresSource');
  await assertSourceContains(page, '.f-category-intro--reverse .f-category-intro__image img', 'uploads/import/figma/category-zaruka.jpg', 'catalog.warrantySource');
  await assertSourceContains(page, '.f-products-series--custom .f-listing--product:nth-child(1) .f-listing__image img', 'virivka-summit-xl.jpg', 'catalog.productCardOneSource');
  await assertSourceContains(page, '.f-products-series--custom .f-listing--product:nth-child(2) .f-listing__image img', 'virivka-summit.jpg', 'catalog.productCardTwoSource');
  await assertSourceContains(page, '.f-products-series--custom .f-listing--product:nth-child(3) .f-listing__image img', 'virivka-tundra.jpg', 'catalog.productCardThreeSource');
  await assertSourceContains(page, '.f-configurator-cta__image', 'uploads/import/figma/category-configurator.png', 'catalog.configuratorSource');
  await assertSourceContains(page, '.f-showroom-panel__image--1 img', 'uploads/import/figma/showroom-1.png', 'catalog.showroomOneSource');
  await assertSourceContains(page, '.f-showroom-panel__image--2 img', 'uploads/import/figma/showroom-3.png', 'catalog.showroomTwoSource');
  await assertSourceContains(page, '.f-showroom-panel__image--3 img', 'uploads/import/figma/showroom-2.png', 'catalog.showroomThreeSource');
}

async function auditTimberwolfDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/product/timberwolf/`, { waitUntil: 'load' });

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
  await assertFooterLayout(page, 'timberwolf', 7035);

  await assertSourceContains(page, '.f-heading--product-detail .f-gallery__slide:nth-child(1) img', 'timberwolf-signature.jpg', 'timberwolf.heroSource');
  await assertSourceContains(page, '.f-product-configuration:nth-child(1) .f-product-configuration__thumb img', 'timberwolf-prestige.jpg', 'timberwolf.prestigeSource');
  await assertSourceContains(page, '.f-product-configuration:nth-child(2) .f-product-configuration__thumb img', 'timberwolf-signature.jpg', 'timberwolf.signatureSource');
  await assertSourceContains(page, '.f-product-detail-configurator .f-configurator-cta__image', 'uploads/import/figma/category-configurator.png', 'timberwolf.configuratorSource');
  await assertSourceContains(page, '.f-product-contact-card__avatar img', 'uploads/import/figma/contact-lukas-dusek.png', 'timberwolf.contactAvatarSource');
  await assertComputedStyle(page, '.f-heading--product-detail .f-gallery__slide:nth-child(1) img', 'object-fit', 'cover', 'timberwolf.heroImageFit');
}

async function auditContactDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/kontakt/`, { waitUntil: 'load' });

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
  await assertFooterLayout(page, 'contact', 2425);

  await assertSourceContains(page, '.f-local-map__image', 'uploads/import/figma/contact-map-showroom.png', 'contact.mapSource');
  await assertSourceContains(page, '.f-contact-card:nth-child(1) .f-contact-card__avatar img', 'uploads/import/figma/contact-lukas-dusek.png', 'contact.cardAvatarSource');
}

async function auditSharedFooterDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });

  for (const path of [
    '/swimspa/',
    '/podpora/',
    '/showroom/',
    '/reference/',
    '/o-nas/',
    '/sluzby/',
    '/vlastnosti/',
    '/certifikaty/',
    '/zaruka/',
    '/kolik-stoji-udrzba/',
  ]) {
    await page.goto(`${baseUrl}${path}`, { waitUntil: 'load' });
    await assertFooterLayout(page, `sharedFooter:${path}`);
  }
}

(async () => {
  const browser = await chromium.launch({ executablePath: chromePath });
  const page = await browser.newPage({ deviceScaleFactor: 1 });

  try {
    await auditDesktop(page);
    await auditDesktopScaledMatrix(page);
    await auditMobile(page);
    await auditResponsiveShell(page);
    await auditCompactNavigation(page);
    await auditFigmaSources(page);
    await auditCatalogHotTubsDesktop(page);
    await auditTimberwolfDesktop(page);
    await auditContactDesktop(page);
    await auditSharedFooterDesktop(page);
    console.log('Figma visual audit passed.');
  } finally {
    await browser.close();
  }
})();

