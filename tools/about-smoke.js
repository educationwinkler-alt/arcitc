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
    await page.goto(`${baseUrl}/o-nas/`, { waitUntil: 'networkidle' });

    const people = await page.locator('.f-about-person').count();
    assert(people === 4, `desktop: expected 4 about team cards, got ${people}`);

    const waitingMedia = await page.locator('.f-about-person__media[data-asset-status="WAITING_ON_OWNER"]').count();
    assert(waitingMedia === 4, `desktop: expected 4 WAITING_ON_OWNER team media placeholders, got ${waitingMedia}`);

    const teamImages = await page.locator('.f-about-person__media img').count();
    assert(teamImages === 0, `desktop: team must not render unapproved portrait assets, got ${teamImages}`);

    const html = await page.content();
    assert(!html.includes('uploads/import/figma/about-team-'), 'desktop: design-only Figma team portraits must not be used');

    const statColor = await page.locator('.f-about-figma__stats strong').first().evaluate((element) => getComputedStyle(element).color);
    assert(statColor === 'rgb(163, 31, 55)', `desktop: stats must use Figma red token, got ${statColor}`);

    const jobs = await page.locator('.f-about-job').count();
    assert(jobs === 2, `desktop: expected 2 career rows, got ${jobs}`);

    const openJobs = await page.locator('.f-about-job--open').count();
    assert(openJobs === 1, `desktop: expected 1 open career card, got ${openJobs}`);

    const footerBackground = await page.locator('.f-footer--arctic').first().evaluate((element) => getComputedStyle(element).backgroundImage);
    assert(footerBackground.includes('footer-background'), `desktop: footer mountain image must stay visible, got ${footerBackground}`);

    await assertBox(page, '.f-main--about-figma', { x: 0, y: 441, width: 1920, height: 2772 }, 4, 'desktop.main');
    await assertBox(page, '.f-about-figma__stats', { x: 260, y: 1122, width: 1040, height: 119 }, 4, 'desktop.stats');
    await assertBox(page, '.f-about-figma__team', { x: 260, y: 1658, width: 1407, height: 461.2 }, 8, 'desktop.team');
    await assertBox(page, '.f-about-person:nth-child(1) .f-about-person__media', { x: 260, y: 1658, width: 336, height: 335 }, 4, 'desktop.teamMediaOne');
    await assertBox(page, '.f-about-figma__jobs', { x: 260, y: 2457, width: 1401, height: 642 }, 4, 'desktop.jobs');
    await assertBox(page, '.page-template-template-about .f-contact-cta', { x: 260, y: 3328, width: 1400, height: 455 }, 4, 'desktop.contactCta');

    await page.setViewportSize({ width: 375, height: 900 });
    await page.goto(`${baseUrl}/o-nas/`, { waitUntil: 'networkidle' });

    const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
    assert(overflow <= 0, `mobile: horizontal overflow ${overflow}`);

    const mobilePeople = await page.locator('.f-about-person').count();
    assert(mobilePeople === 4, `mobile: expected 4 about team cards, got ${mobilePeople}`);

    const mobileWaitingMedia = await page.locator('.f-about-person__media[data-asset-status="WAITING_ON_OWNER"]').count();
    assert(mobileWaitingMedia === 4, `mobile: expected 4 WAITING_ON_OWNER team media placeholders, got ${mobileWaitingMedia}`);

    console.log('About smoke passed.');
  } finally {
    await browser.close();
  }
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
