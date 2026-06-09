const { chromium } = require('playwright-core');

const baseUrl = (process.env.ARCTIC_BASE_URL || 'http://localhost:8090').replace(/\/$/, '');

function fail(message, details = undefined) {
  const error = new Error(message);
  if (details) {
    error.details = details;
  }
  throw error;
}

function near(actual, expected, tolerance, label) {
  if (Math.abs(actual - expected) > tolerance) {
    fail(`${label}: expected ${expected} +/- ${tolerance}, got ${actual}`);
  }
}

function checkRect(rect, expected, label, tolerance = 3) {
  if (!rect) {
    fail(`${label}: missing element`);
  }

  for (const key of Object.keys(expected)) {
    near(rect[key], expected[key], tolerance, `${label}.${key}`);
  }
}

async function main() {
  const browser = await chromium.launch({
    channel: process.env.PLAYWRIGHT_CHANNEL || 'chrome',
    headless: true,
  });

  const page = await browser.newPage({
    viewport: { width: 375, height: 900 },
    deviceScaleFactor: 1,
    isMobile: true,
  });

  await page.goto(`${baseUrl}/`, { waitUntil: 'networkidle' });
  await page.waitForTimeout(700);

  const homepage = await page.evaluate(() => {
    const rect = (selector) => {
      const element = document.querySelector(selector);
      if (!element) {
        return null;
      }

      const box = element.getBoundingClientRect();
      return {
        x: Math.round(box.x * 10) / 10,
        y: Math.round((box.y + window.scrollY) * 10) / 10,
        w: Math.round(box.width * 10) / 10,
        h: Math.round(box.height * 10) / 10,
        display: window.getComputedStyle(element).display,
      };
    };

    return {
      contractStyles: Array.from(document.querySelectorAll('link[rel="stylesheet"]'))
        .some((link) => link.href.includes('mobile-figma-contract.css')),
      promo: rect('.f-hero-promo'),
      categoryOne: rect('.f-section--product-categories .f-category:nth-child(1)'),
      categoryTwo: rect('.f-section--product-categories .f-category:nth-child(2)'),
      catalog: rect('.f-section--catalog-request'),
      benefits: rect('.f-section--arctic-benefits'),
      benefitCards: Array.from(document.querySelectorAll('.f-arctic-benefit')).map((element) => {
        const box = element.getBoundingClientRect();
        return {
          x: Math.round(box.x * 10) / 10,
          y: Math.round((box.y + window.scrollY) * 10) / 10,
          w: Math.round(box.width * 10) / 10,
          h: Math.round(box.height * 10) / 10,
        };
      }),
      showroomPanel: rect('.f-section--showroom .f-showroom-panel'),
      showroomImages: ['--1', '--2', '--3'].map((suffix) => rect(`.f-showroom-panel__image${suffix}`)),
      footer: rect('.f-footer--arctic'),
    };
  });

  if (!homepage.contractStyles) {
    fail('mobile-figma-contract.css is not enqueued');
  }

  checkRect(homepage.promo, { x: 20, y: 562, w: 335, h: 288 }, 'homepage promo');
  checkRect(homepage.categoryOne, { x: 20, y: 842, w: 335, h: 221 }, 'homepage category 1');
  checkRect(homepage.categoryTwo, { x: 20, y: 1084, w: 335, h: 221 }, 'homepage category 2');

  if (!homepage.catalog || homepage.catalog.display !== 'none') {
    fail('mobile homepage catalog section must be hidden until the catalog UX is approved locally', homepage.catalog);
  }

  checkRect(homepage.benefits, { x: 0, y: 1667, w: 375, h: 495 }, 'homepage benefits', 4);
  checkRect(homepage.benefitCards[0], { x: 20, y: 1692, w: 336, h: 128 }, 'benefit card 1', 4);
  checkRect(homepage.benefitCards[1], { x: 20, y: 1830, w: 336, h: 122 }, 'benefit card 2', 4);
  checkRect(homepage.benefitCards[2], { x: 20, y: 1962, w: 336, h: 122 }, 'benefit card 3', 4);
  checkRect(homepage.showroomPanel, { x: 20, y: 2162, w: 335, h: 695 }, 'showroom panel', 4);
  checkRect(homepage.showroomImages[0], { x: 10, y: 2205, w: 183, h: 100 }, 'showroom image 1', 4);
  checkRect(homepage.showroomImages[1], { x: 91, y: 2316, w: 217, h: 136 }, 'showroom image 2', 4);
  checkRect(homepage.showroomImages[2], { x: 205, y: 2143, w: 159, h: 162 }, 'showroom image 3', 4);
  checkRect(homepage.footer, { x: 0, w: 375, h: 1396 }, 'footer frame', 3);

  await page.click('.f-navigation__trigger');
  await page.waitForTimeout(500);

  const menu = await page.evaluate(() => {
    const rect = (selector) => {
      const element = document.querySelector(selector);
      if (!element) {
        return null;
      }

      const box = element.getBoundingClientRect();
      return {
        x: Math.round(box.x * 10) / 10,
        y: Math.round(box.y * 10) / 10,
        w: Math.round(box.width * 10) / 10,
        h: Math.round(box.height * 10) / 10,
        display: window.getComputedStyle(element).display,
      };
    };

    return {
      offcanvas: rect('.f-off--navigation'),
      nav: rect('.f-off--navigation > .f-navigation'),
      logo: rect('.f-header .f-logo img'),
      close: rect('.f-navigation__trigger'),
      search: rect('.f-off--navigation > .f-search'),
      contacts: rect('.f-off--navigation > .f-bar__contacts'),
      hiddenIntroDisplay: window.getComputedStyle(document.querySelector('.f-off--navigation .is-mobile-hidden-figma')).display,
      items: Array.from(document.querySelectorAll('.f-off--navigation .f-navigation__list > li')).slice(0, 4).map((item) => {
        const link = item.querySelector(':scope > a');
        const panel = item.querySelector(':scope > .f-navigation-sub');
        const box = link.getBoundingClientRect();
        return {
          text: link.textContent.trim(),
          x: Math.round(box.x * 10) / 10,
          y: Math.round(box.y * 10) / 10,
          h: Math.round(box.height * 10) / 10,
          open: item.classList.contains('is-open'),
          expanded: link.getAttribute('aria-expanded'),
          panelHidden: panel ? panel.hidden : null,
        };
      }),
    };
  });

  checkRect(menu.offcanvas, { x: 0, y: 0, w: 375 }, 'mobile menu offcanvas');
  checkRect(menu.logo, { x: 20, y: 7, w: 86, h: 48 }, 'mobile menu logo', 2);
  checkRect(menu.close, { x: 310, y: 8.5, w: 45, h: 45 }, 'mobile menu close', 2);
  checkRect(menu.nav, { x: 80, y: 112, w: 236 }, 'mobile menu nav');
  checkRect(menu.items[0], { x: 80, y: 112, h: 29 }, 'mobile menu first heading');
  checkRect(menu.search, { x: 26, y: 527, w: 323, h: 44 }, 'mobile menu search');
  checkRect(menu.contacts, { x: 26, y: 582, w: 323 }, 'mobile menu contacts', 4);

  if (menu.hiddenIntroDisplay !== 'none') {
    fail('mobile menu helper link must be hidden in Figma accordion');
  }

  if (!menu.items[0].open || menu.items[0].expanded !== 'true' || menu.items[0].panelHidden) {
    fail('first mobile menu accordion must be open', menu.items[0]);
  }

  for (const item of menu.items.slice(1)) {
    if (item.open || item.expanded !== 'false' || item.panelHidden !== true) {
      fail('non-first mobile menu accordions must start collapsed', item);
    }
  }

  const footer = await page.evaluate(() => {
    const root = document.querySelector('.f-footer--arctic');
    const rect = (selector) => {
      const element = document.querySelector(selector);
      if (!element) {
        return null;
      }

      const rootBox = root.getBoundingClientRect();
      const box = element.getBoundingClientRect();
      return {
        x: Math.round((box.x - rootBox.x) * 10) / 10,
        y: Math.round((box.y - rootBox.y) * 10) / 10,
        w: Math.round(box.width * 10) / 10,
        h: Math.round(box.height * 10) / 10,
      };
    };

    return {
      group: rect('.f-footer__group:nth-child(1)'),
      quick: rect('.f-footer__quick-contact'),
      map: rect('.f-footer__quick-map'),
      bottom: rect('.f-footer__bottom'),
      headings: Array.from(document.querySelectorAll('.f-footer__group h2')).map((heading) => {
        const next = heading.nextElementSibling;
        return {
          text: heading.textContent.trim(),
          open: heading.classList.contains('is-open'),
          expanded: heading.getAttribute('aria-expanded'),
          listHidden: next && next.matches('ul') ? next.hidden : null,
        };
      }),
    };
  });

  checkRect(footer.group, { x: 32, y: 34, w: 188 }, 'footer menu group');
  checkRect(footer.quick, { x: 16, y: 392, w: 335, h: 679 }, 'footer quick contact');
  checkRect(footer.map, { x: 53, y: 737, w: 267, h: 299 }, 'footer map');
  checkRect(footer.bottom, { x: 0, y: 1082, w: 375 }, 'footer bottom');

  if (!footer.headings[0].open || footer.headings[0].expanded !== 'true' || footer.headings[0].listHidden) {
    fail('first footer accordion must be open', footer.headings[0]);
  }

  for (const heading of footer.headings.slice(1)) {
    if (heading.open || heading.expanded !== 'false' || heading.listHidden !== true) {
      fail('non-first footer accordions must start collapsed', heading);
    }
  }

  await browser.close();
  console.log('mobile-figma-contract-smoke: ok');
}

main().catch((error) => {
  console.error(error.message);
  if (error.details) {
    console.error(JSON.stringify(error.details, null, 2));
  }
  process.exit(1);
});
