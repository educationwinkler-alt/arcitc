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

function assertBetween(actual, min, max, label) {
  assert(actual >= min && actual <= max, `${label}: expected ${min}..${max}, got ${round(actual)}`);
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
    await page.goto(`${baseUrl}/zaruka/`, { waitUntil: 'networkidle' });

    const cards = await page.locator('.f-warranty-card').count();
    assert(cards === 3, `desktop: expected 3 warranty cards, got ${cards}`);

    const waitingCards = await page.locator('.f-warranty-card[data-asset-status="WAITING_ON_OWNER"]').count();
    assert(waitingCards === 3, `desktop: expected 3 WAITING_ON_OWNER cards, got ${waitingCards}`);

    const waitingMedia = await page.locator('.f-warranty-card__media--waiting').count();
    assert(waitingMedia === 3, `desktop: expected 3 waiting media placeholders, got ${waitingMedia}`);

    const cardImages = await page.locator('.f-warranty-card__media img').count();
    assert(cardImages === 0, `desktop: warranty cards must not render unapproved image assets, got ${cardImages}`);

    await assertBox(page, '.f-warranty-labels', { x: 261, y: 674, width: 271, height: 225 }, 4, 'desktop.labels');
    await assertBox(page, '.f-warranty-card:nth-of-type(1)', { x: 450, y: 435, width: 217, height: 499 }, 4, 'desktop.cardOne');
    await assertBox(page, '.f-warranty-card:nth-of-type(2)', { x: 691, y: 435, width: 217, height: 499 }, 4, 'desktop.cardTwo');
    await assertBox(page, '.f-warranty-card:nth-of-type(3)', { x: 932, y: 435, width: 217, height: 499 }, 4, 'desktop.cardThree');
    await assertBox(page, '.f-warranty-card:nth-of-type(1) .f-warranty-card__media', { x: 479, y: 445, width: 159, height: 127 }, 4, 'desktop.media');

    const note = await box(page, '.f-warranty-note', 'desktop.note');
    assertClose(note.x, 1224, 4, 'desktop.note.x');
    assertClose(note.y, 679, 4, 'desktop.note.y');
    assertClose(note.width, 368, 4, 'desktop.note.width');
    assertBetween(note.height, 195, 220, 'desktop.note.height');

    const linkHref = await page.locator('.f-warranty-note a').first().getAttribute('href');
    assert(linkHref && linkHref.includes('/ke-stazeni/'), `desktop.noteLink: unexpected href ${linkHref}`);

    const cta = await box(page, '.page-template-template-warranty .f-contact-cta', 'desktop.contactCta');
    assertClose(cta.y, 1074, 6, 'desktop.contactCta.y');

    const footer = await box(page, '.f-footer--arctic', 'desktop.footer');
    assertClose(footer.y, 1557, 6, 'desktop.footer.y');

    const html = await page.content();
    assert(!html.includes('f-section--warranty-table'), 'desktop: legacy warranty table section must not be rendered');
    assert(!html.includes('uploads/import/figma/category-zaruka.jpg'), 'desktop: category-zaruka design image must not be used as warranty card media');

    await page.setViewportSize({ width: 375, height: 900 });
    await page.goto(`${baseUrl}/zaruka/`, { waitUntil: 'networkidle' });

    const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
    assert(overflow <= 0, `mobile: horizontal overflow ${overflow}`);

    const labelsDisplay = await page.locator('.f-warranty-labels').evaluate((element) => getComputedStyle(element).display);
    assert(labelsDisplay === 'none', `mobile: shared label column should be hidden, got ${labelsDisplay}`);

    const mobileItemLabelDisplay = await page.locator('.f-warranty-card__items dt').first().evaluate((element) => getComputedStyle(element).position);
    assert(mobileItemLabelDisplay !== 'absolute', 'mobile: card item labels must be visible inside stacked cards');

    console.log('Warranty smoke passed.');
  } finally {
    await browser.close();
  }
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
