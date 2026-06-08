const { chromium } = require('playwright-core');
const fs = require('fs');
const path = require('path');

const baseUrl = (process.env.ARCTIC_BASE_URL || 'http://localhost:8090').replace(/\/$/, '');
const label = baseUrl.includes('localhost') ? 'local' : 'production';
const outDir = path.join('docs', 'screenshots', 'editable-text-overflow-2026-06-08');

const pages = [
  {
    id: 'home',
    path: '/',
    selectors: [
      '.template--homepage .f-page__content p',
      '.template--homepage .f-showroom-panel__content p',
      '.template--homepage .f-progress-layout__intro p',
    ],
  },
  {
    id: 'hot-tubs',
    path: '/virivky/',
    selectors: [
      '.tax-product-category .f-products-series__description',
      '.tax-product-category .f-configurator-cta__content p',
      '.tax-product-category .f-showroom-panel__content p',
      '.tax-product-category .f-progress-layout__intro p',
    ],
  },
  {
    id: 'swimspas',
    path: '/swimspa/',
    selectors: [
      '.tax-product-category .f-products-series__description',
      '.tax-product-category .f-configurator-cta__content p',
      '.tax-product-category .f-showroom-panel__content p',
      '.tax-product-category .f-progress-layout__intro p',
    ],
  },
  {
    id: 'product-cub',
    path: '/product/cub/',
    selectors: [
      '.single-product .f-heading--product-detail .f-heading__description',
    ],
  },
  {
    id: 'product-timberwolf',
    path: '/product/timberwolf/',
    selectors: [
      '.single-product .f-heading--product-detail .f-heading__description',
    ],
  },
];

function fail(message, details = undefined) {
  const error = new Error(message);
  if (details) {
    error.details = details;
  }
  throw error;
}

function safePath(fileName) {
  return fileName.replace(/[^a-z0-9-]+/gi, '-').replace(/^-|-$/g, '').toLowerCase();
}

(async () => {
  fs.mkdirSync(outDir, { recursive: true });

  const browser = await chromium.launch({
    channel: process.env.PLAYWRIGHT_CHANNEL || 'chrome',
    headless: true,
  });

  const page = await browser.newPage({
    viewport: { width: 1920, height: 1080 },
    deviceScaleFactor: 1,
  });

  const results = [];
  const failures = [];

  for (const pageDef of pages) {
    const url = `${baseUrl}${pageDef.path}`;
    const response = await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 90000 });
    await page.waitForLoadState('networkidle', { timeout: 12000 }).catch(() => {});
    await page.waitForTimeout(700);

    const state = await page.evaluate((selectors) => {
      const assets = Array.from(document.querySelectorAll('link[href*="content-overflow-guard.css"]')).map((asset) => asset.href);

      const checks = selectors.flatMap((selector) => Array.from(document.querySelectorAll(selector)).map((element, index) => {
        const style = getComputedStyle(element);
        const rect = element.getBoundingClientRect();
        const overflowValues = [style.overflow, style.overflowY, style.overflowX];
        const hidden = overflowValues.some((value) => value === 'hidden' || value === 'clip');
        const clipped = hidden && element.scrollHeight > element.clientHeight + 1;

        return {
          selector,
          index,
          text: element.textContent.trim().replace(/\s+/g, ' ').slice(0, 220),
          rect: {
            x: rect.x,
            y: rect.y,
            width: rect.width,
            height: rect.height,
            top: rect.top,
            right: rect.right,
            bottom: rect.bottom,
            left: rect.left,
          },
          clientHeight: element.clientHeight,
          scrollHeight: element.scrollHeight,
          computedHeight: style.height,
          overflow: style.overflow,
          overflowY: style.overflowY,
          maxHeight: style.maxHeight,
          clipped,
        };
      }));

      return { assets, checks };
    }, pageDef.selectors);

    const entry = {
      id: pageDef.id,
      url,
      status: response ? response.status() : 0,
      assets: state.assets,
      checks: state.checks,
      missingSelectors: pageDef.selectors.filter((selector) => !state.checks.some((check) => check.selector === selector)),
    };

    await page.screenshot({
      path: path.join(outDir, `${label}-${safePath(pageDef.id)}.png`),
      fullPage: false,
    });

    results.push(entry);

    if (!response || response.status() >= 400) {
      failures.push({ id: pageDef.id, reason: `HTTP ${entry.status}`, url });
      continue;
    }

    if (entry.assets.length === 0) {
      failures.push({ id: pageDef.id, reason: 'content-overflow-guard.css is not enqueued', url });
    }

    for (const check of entry.checks) {
      if (check.clipped) {
        failures.push({ id: pageDef.id, reason: 'editable text is clipped', check, url });
      }
    }
  }

  fs.writeFileSync(path.join(outDir, `${label}-audit.json`), JSON.stringify({ baseUrl, results, failures }, null, 2));

  await browser.close();

  if (failures.length > 0) {
    fail('Editable text overflow smoke failed.', failures);
  }

  console.log(`Editable text overflow smoke passed for ${results.length} pages and ${results.reduce((sum, result) => sum + result.checks.length, 0)} text nodes.`);
})().catch((error) => {
  console.error(error.message);
  if (error.details) {
    console.error(JSON.stringify(error.details, null, 2));
  }
  process.exit(1);
});
