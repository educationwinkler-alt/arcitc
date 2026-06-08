const { chromium } = require('playwright-core');
const fs = require('fs');
const path = require('path');

const baseUrl = (process.env.ARCTIC_BASE_URL || 'http://localhost:8090').replace(/\/$/, '');
const label = baseUrl.includes('localhost') ? 'local' : 'production';
const outDir = path.join('docs', 'screenshots', 'homepage-offer-promo-contract-2026-06-08');

function fail(message, details = undefined) {
  const error = new Error(message);
  if (details) {
    error.details = details;
  }
  throw error;
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

  const response = await page.goto(`${baseUrl}/`, { waitUntil: 'domcontentloaded', timeout: 90000 });
  await page.waitForLoadState('networkidle', { timeout: 12000 }).catch(() => {});
  await page.waitForTimeout(900);

  const state = await page.evaluate(() => {
    const heroPromos = Array.from(document.querySelectorAll('.template--homepage .f-section--slides > .f-hero-promo'));
    const legacySections = Array.from(document.querySelectorAll('.template--homepage .f-section--offers-small'));
    const legacyCards = Array.from(document.querySelectorAll('.template--homepage .f-listing--offer-small'));
    const firstPromo = heroPromos[0] || null;
    const promoImage = firstPromo ? firstPromo.querySelector('.f-hero-promo__image') : null;
    const promoLink = firstPromo ? firstPromo.querySelector('.f-hero-promo__button') : null;

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
      url: window.location.href,
      heroPromoCount: heroPromos.length,
      legacySectionCount: legacySections.length,
      legacyCardCount: legacyCards.length,
      promoSource: firstPromo ? firstPromo.getAttribute('data-content-source') || '' : '',
      promoOfferId: firstPromo ? firstPromo.getAttribute('data-offer-id') || '' : '',
      promoHref: promoLink ? promoLink.href : '',
      promoImageStatus: promoImage ? promoImage.getAttribute('data-asset-status') || '' : '',
      promoRect: rect(firstPromo),
      legacyTexts: legacySections.map((section) => section.textContent.trim().replace(/\s+/g, ' ').slice(0, 240)),
    };
  });

  await page.screenshot({
    path: path.join(outDir, `${label}-homepage-offer-promo.png`),
    fullPage: false,
  });

  fs.writeFileSync(path.join(outDir, `${label}-audit.json`), JSON.stringify({
    baseUrl,
    status: response ? response.status() : 0,
    ...state,
  }, null, 2));

  if (!response || response.status() >= 400) {
    fail(`Homepage returned HTTP ${response ? response.status() : 0}`, state);
  }

  if (state.heroPromoCount !== 1) {
    fail(`Homepage must render exactly one Arctic hero promo, got ${state.heroPromoCount}.`, state);
  }

  if (state.legacySectionCount !== 0 || state.legacyCardCount !== 0) {
    fail('Homepage still renders the legacy small offers listing in addition to the Arctic hero promo.', state);
  }

  if (state.promoSource !== 'offer-cpt') {
    fail(`Homepage promo must use the editable offer CPT source, got ${state.promoSource}.`, state);
  }

  if (!Number.isFinite(Number(state.promoOfferId)) || Number(state.promoOfferId) <= 0) {
    fail(`Homepage promo must expose the source offer id, got ${state.promoOfferId}.`, state);
  }

  if (!state.promoHref.includes('/akcni-nabidky/')) {
    fail(`Homepage promo must link to the editable offers archive, got ${state.promoHref}.`, state);
  }

  if (state.promoImageStatus !== 'admin-offer-promo') {
    fail(`Homepage promo must render the Arctic offer promo image, got ${state.promoImageStatus}.`, state);
  }

  console.log(`Homepage offer promo contract passed: one hero promo, ${state.legacySectionCount} legacy sections.`);
  await browser.close();
})().catch((error) => {
  console.error(error.message);
  if (error.details) {
    console.error(JSON.stringify(error.details, null, 2));
  }
  process.exit(1);
});
