const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

function parseRgb(value) {
  const match = String(value || '').match(/rgba?\(([^)]+)\)/i);
  if (!match) {
    return null;
  }

  const parts = match[1].split(',').map((part) => Number.parseFloat(part.trim()));
  if (parts.length < 3 || parts.slice(0, 3).some((part) => Number.isNaN(part))) {
    return null;
  }

  return {
    r: parts[0],
    g: parts[1],
    b: parts[2],
    a: parts.length > 3 && !Number.isNaN(parts[3]) ? parts[3] : 1,
  };
}

function assertLightArcticOverlay(value, label) {
  const color = parseRgb(value);
  assert(color, `${label} is not an rgb/rgba color: ${value}`);
  assert(color.r >= 225 && color.g >= 225 && color.b >= 225, `${label} is too dark/BASPA-like: ${value}`);
}

function assertFrostSurface(value, label) {
  if (String(value || '').includes('rgb(246, 248, 250)') && String(value || '').includes('rgb(238, 241, 245)')) {
    return;
  }

  const color = parseRgb(value);
  assert(color, `${label} is not an rgb/rgba color: ${value}`);
  assert(color.r >= 230 && color.g >= 232 && color.b >= 235, `${label} is not the Arctic frost surface: ${value}`);
}

async function readStyle(page, selector, property, label) {
  await page.waitForSelector(selector, { timeout: 15000 });

  const value = await page.locator(selector).first().evaluate((element, propertyName) => (
    getComputedStyle(element).getPropertyValue(propertyName).trim()
  ), property);

  assert(value, `${label} is empty`);
  return value;
}

async function openContactDialog(page) {
  await page.goto(`${baseUrl}/`, { waitUntil: 'networkidle', timeout: 60000 });
  await page.locator('.js-off__trigger[data-off="contact"]:visible').first().click();
  await page.waitForSelector('.f-off--contact.active', { timeout: 15000 });

  const overlay = await readStyle(page, '.f-off--contact.active', 'background-color', 'contact dialog overlay');
  const formSurface = await readStyle(page, '.f-off--contact.active .f-off__form .f-form', 'background-image', 'contact dialog form surface');

  assertLightArcticOverlay(overlay, 'contact dialog overlay');
  assertFrostSurface(formSurface, 'contact dialog form surface');

  await page.locator('.f-off--contact.active .js-off__close').first().click();
}

async function openBenefitPopup(page) {
  await page.goto(`${baseUrl}/product/timberwolf/`, { waitUntil: 'networkidle', timeout: 60000 });
  await page.locator('.f-product-benefit--has-popup .f-product-benefit__trigger').first().click();
  await page.waitForSelector('.f-off--benefit-popup.active', { timeout: 15000 });

  const overlay = await readStyle(page, '.f-off--benefit-popup.active', 'background-color', 'benefit popup overlay');
  const panel = await readStyle(page, '.f-off--benefit-popup.active .f-benefit-popup', 'background-color', 'benefit popup panel');

  assertLightArcticOverlay(overlay, 'benefit popup overlay');
  assert(panel === 'rgb(255, 255, 255)' || panel === 'rgba(255, 255, 255, 1)', `benefit popup panel should be white, got ${panel}`);

  await page.locator('.f-off--benefit-popup.active .js-off__close').first().click();
}

async function openReferenceLightbox(page) {
  await page.goto(`${baseUrl}/reference/`, { waitUntil: 'networkidle', timeout: 60000 });
  await page.locator('.js-image').first().click();
  await page.waitForSelector('.pswp__bg', { timeout: 15000 });
  await page.waitForTimeout(200);

  const background = await readStyle(page, '.pswp__bg', 'background-color', 'PhotoSwipe background');
  assertLightArcticOverlay(background, 'PhotoSwipe background');

  const closeBackground = await readStyle(page, '.pswp__button--close', 'background-color', 'PhotoSwipe close button');
  const close = parseRgb(closeBackground);
  assert(close && close.r > 140 && close.g < 60 && close.b < 80, `PhotoSwipe close button is not Arctic red: ${closeBackground}`);
}

(async () => {
  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, deviceScaleFactor: 1 });

  try {
    await openContactDialog(page);
    await openBenefitPopup(page);
    await openReferenceLightbox(page);
    console.log('Modal design smoke passed.');
  } finally {
    await browser.close();
  }
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
