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

async function main() {
  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, deviceScaleFactor: 1 });

  try {
    await page.goto(`${baseUrl}/kolik-stoji-udrzba/`, { waitUntil: 'networkidle' });

    const sections = await page.locator('.f-main--maintenance .f-figma-article section').count();
    assert(sections === 4, `desktop: expected 4 maintenance sections, got ${sections}`);

    const text = await page.locator('.f-main--maintenance').innerText();
    for (const expected of [
      'Náklady na vlastnictví a provozování vířivky',
      'Další inovace',
      'Nejnižší provozní náklady',
      'Skutečná ochrana proti mrazu',
      'RossExhaust',
      '149 wattů během 128 hodin',
      '503 wattů za 130 hodin',
      'FreeHeat',
    ]) {
      assert(text.includes(expected), `desktop: missing maintenance content "${expected}"`);
    }

    assert(!text.includes('Spa Boy: Údržba vířivky'), 'desktop: old shortened Spa Boy section should not render on the Figma maintenance page');
    assert(!text.includes('Proč jsou záruky tak důležité'), 'desktop: old warranty section should not render on the Figma maintenance page');

    await assertBox(page, '.f-heading', { x: 0, y: 0, width: 1920, height: 581 }, 4, 'desktop.heading');
    await assertBox(page, '.f-main--maintenance .f-figma-article', { x: 497, y: 581, width: 927, height: 2382 }, 10, 'desktop.article');
    await assertBox(page, '.f-main--maintenance .f-figma-article section:nth-of-type(1)', { x: 497, y: 581, width: 927, height: 1645 }, 10, 'desktop.blockOne');
    await assertBox(page, '.f-main--maintenance .f-figma-article section:nth-of-type(2)', { x: 497, y: 2266, width: 927, height: 247 }, 10, 'desktop.blockTwo');
    await assertBox(page, '.f-main--maintenance .f-figma-article section:nth-of-type(3)', { x: 497, y: 2554, width: 927, height: 172 }, 10, 'desktop.blockThree');
    await assertBox(page, '.f-main--maintenance .f-figma-article section:nth-of-type(4)', { x: 497, y: 2766, width: 927, height: 197 }, 10, 'desktop.blockFour');

    const contactCta = await box(page, '.page-template-template-maintenance .f-contact-cta', 'desktop.contactCta');
    assertClose(contactCta.y, 3068, 10, 'desktop.contactCta.y');

    const footer = await box(page, '.f-footer--arctic', 'desktop.footer');
    assertClose(footer.y, 3551, 10, 'desktop.footer.y');

    await page.setViewportSize({ width: 390, height: 900 });
    await page.goto(`${baseUrl}/kolik-stoji-udrzba/`, { waitUntil: 'networkidle' });

    const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
    assert(overflow <= 0, `mobile: horizontal overflow ${overflow}`);

    const mobileSections = await page.locator('.f-main--maintenance .f-figma-article section').count();
    assert(mobileSections === 4, `mobile: expected 4 maintenance sections, got ${mobileSections}`);

    console.log('Maintenance smoke passed.');
  } finally {
    await browser.close();
  }
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
