const { chromium } = require('playwright-core');
const fs = require('fs');
const path = require('path');

const baseUrl = (process.env.ARCTIC_BASE_URL || 'http://localhost:8090').replace(/\/$/, '');
const label = baseUrl.includes('localhost') ? 'local' : 'production';
const shouldRenderCatalog = label === 'local';
const outDir = path.join('docs', 'screenshots', 'catalog-request-2026-06-08');

const pages = [
  { id: 'home', path: '/' },
  { id: 'hot-tubs', path: '/virivky/' },
  { id: 'swimspas', path: '/swimspa/' },
  { id: 'product-cub', path: '/product/cub/' },
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
    viewport: { width: 1440, height: 1200 },
    deviceScaleFactor: 1,
  });

  const results = [];
  const failures = [];

  for (const pageDef of pages) {
    const url = `${baseUrl}${pageDef.path}`;
    const response = await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 90000 });
    await page.waitForLoadState('networkidle', { timeout: 12000 }).catch(() => {});
    await page.waitForTimeout(700);

    const state = await page.evaluate(() => {
      const section = document.querySelector('.f-section--catalog-request');
      const bodyText = document.body ? document.body.textContent.trim().replace(/\s+/g, ' ') : '';
      const rect = (element) => element ? {
        x: element.getBoundingClientRect().x,
        y: element.getBoundingClientRect().y,
        width: element.getBoundingClientRect().width,
        height: element.getBoundingClientRect().height,
        top: element.getBoundingClientRect().top,
        right: element.getBoundingClientRect().right,
        bottom: element.getBoundingClientRect().bottom,
        left: element.getBoundingClientRect().left,
      } : null;

      return {
        title: section ? section.querySelector('h2')?.textContent.trim().replace(/\s+/g, ' ') || '' : '',
        text: section ? section.textContent.trim().replace(/\s+/g, ' ').slice(0, 320) : '',
        rect: rect(section),
        assets: Array.from(document.querySelectorAll('link[href*="catalog-request.css"]')).map((asset) => asset.href),
        formAction: section ? section.querySelector('form.f-form--catalog')?.getAttribute('action') || '' : '',
        hasEmail: !!section?.querySelector('input[type="email"][name="f-email"]'),
        hasCatalogFormType: section ? section.querySelector('input[name="f-form"]')?.value === 'catalog' : false,
        hasCatalogNonce: !!section?.querySelector('input[name="f-catalog-nonce"][value]'),
        submitText: section ? section.querySelector('button[type="submit"]')?.textContent.trim().replace(/\s+/g, ' ') || '' : '',
        directPdfLinks: section ? Array.from(section.querySelectorAll('a[href*=".pdf"]')).map((link) => link.href) : [],
        bodyHasCatalogRequestText: bodyText.includes('Kompletn') && bodyText.includes('katalog') && bodyText.includes('cen'),
      };
    });

    await page.screenshot({
      path: path.join(outDir, `${label}-${safePath(pageDef.id)}.png`),
      fullPage: false,
    });

    const entry = {
      id: pageDef.id,
      url,
      status: response ? response.status() : 0,
      ...state,
    };
    results.push(entry);

    if (!response || response.status() >= 400) {
      failures.push({ id: pageDef.id, reason: `HTTP ${entry.status}`, url });
      continue;
    }

    if (!shouldRenderCatalog) {
      if (entry.rect || entry.bodyHasCatalogRequestText) {
        failures.push({ id: pageDef.id, reason: 'catalog request banner must not render outside local', entry });
      }
      continue;
    }

    if (!entry.assets.length) {
      failures.push({ id: pageDef.id, reason: 'catalog-request.css is not enqueued', url });
    }

    if (!entry.rect || entry.rect.width < 300 || entry.rect.height < 160) {
      failures.push({ id: pageDef.id, reason: 'catalog request banner is missing or collapsed', entry });
    }

    if (!entry.title.includes('Kompletn') || !entry.title.includes('katalog') || !entry.title.includes('cen')) {
      failures.push({ id: pageDef.id, reason: 'catalog request title is missing or garbled', entry });
    }

    if (!entry.hasEmail || !entry.hasCatalogFormType || !entry.hasCatalogNonce || !entry.submitText) {
      failures.push({ id: pageDef.id, reason: 'catalog request form is incomplete', entry });
    }

    if (entry.directPdfLinks.length > 0) {
      failures.push({ id: pageDef.id, reason: 'catalog request exposes a direct PDF link', entry });
    }
  }

  fs.writeFileSync(path.join(outDir, `${label}-audit.json`), JSON.stringify({ baseUrl, results, failures }, null, 2));

  await browser.close();

  if (failures.length > 0) {
    fail('Catalog request smoke failed.', failures);
  }

  console.log(`Catalog request smoke passed for ${results.length} pages.`);
})().catch((error) => {
  console.error(error.message);
  if (error.details) {
    console.error(JSON.stringify(error.details, null, 2));
  }
  process.exit(1);
});
