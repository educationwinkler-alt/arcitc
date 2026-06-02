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

async function fetchRestCollection(type) {
  try {
    const response = await fetch(`${baseUrl}/wp-json/wp/v2/${type}?per_page=100`);

    if (!response.ok) {
      return [];
    }

    const items = await response.json();

    return Array.isArray(items) ? items : [];
  } catch (_error) {
    return [];
  }
}

async function main() {
  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, deviceScaleFactor: 1 });

  try {
    const adminMembers = await fetchRestCollection('member');
    const adminJobs = await fetchRestCollection('job');
    const expectedPeople = adminMembers.length || 4;
    const expectedJobs = adminJobs.length || 3;
    const usesFigmaFallback = adminMembers.length === 0;
    const usesJobsFallback = adminJobs.length === 0;
    const jobsExtra = Math.max(0, expectedJobs - 3) * 116;

    await page.goto(`${baseUrl}/o-nas/`, { waitUntil: 'networkidle' });

    const people = await page.locator('.f-about-person').count();
    assert(people === expectedPeople, `desktop: expected ${expectedPeople} about team cards, got ${people}`);

    const waitingMedia = await page.locator('.f-about-person__media[data-asset-status="WAITING_ON_OWNER"]').count();
    const teamImages = await page.locator('.f-about-person__media img').count();
    assert(teamImages + waitingMedia === people, `desktop: every team card needs image or explicit waiting media, got ${teamImages} images and ${waitingMedia} placeholders for ${people} cards`);

    const html = await page.content();

    if (usesFigmaFallback) {
      assert(waitingMedia === 0, `desktop: Figma fallback portraits should not use WAITING_ON_OWNER placeholders, got ${waitingMedia}`);
      assert(teamImages === 4, `desktop: expected 4 Figma team portrait images, got ${teamImages}`);

      for (const asset of [
        'uploads/import/figma/about-team-vladimir.png',
        'uploads/import/figma/about-team-lukas.png',
        'uploads/import/figma/about-team-helena.png',
        'uploads/import/figma/about-team-alena.png',
      ]) {
        assert(html.includes(asset), `desktop: missing Figma team portrait ${asset}`);
      }

      for (const text of [
        'Vlastimil Zhoř',
        'Ing. Lukáš Dušek',
        'Helena Antonyová',
        'Alena Janulíková',
        'Komunikace s dodavateli a prodej bazénů',
        'Organizace dopravy a fakturace.',
        'tým BASPA je tu pro vás',
      ]) {
        assert(html.includes(text), `desktop: missing Figma about team text "${text}"`);
      }
    }

    assert(!html.includes('Vladimír Zajíč'), 'desktop: stale local team name Vladimír Zajíč must not render');
    assert(!html.includes('Servisní tým'), 'desktop: stale local team placeholder Servisní tým must not render');
    assert(!html.includes('>Kontaktovat</a>'), 'desktop: Figma team cards must not render extra contact links');

    const teamCarouselButtons = await page.locator('.f-about-team__next').count();
    assert(teamCarouselButtons === 1, `desktop: expected Figma team carousel next button, got ${teamCarouselButtons}`);

    const mediaRadius = await page.locator('.f-about-person__media').first().evaluate((element) => getComputedStyle(element).borderRadius);
    assert(mediaRadius === '40px', `desktop: team image shape must use Figma 40px radius, got ${mediaRadius}`);

    const statColor = await page.locator('.f-about-figma__stats strong').first().evaluate((element) => getComputedStyle(element).color);
    assert(statColor === 'rgb(163, 31, 55)', `desktop: stats must use Figma red token, got ${statColor}`);

    const jobs = await page.locator('.f-about-job').count();
    assert(jobs === expectedJobs, `desktop: expected ${expectedJobs} career rows, got ${jobs}`);

    const renderedJobCount = await page.locator('.f-about-figma__jobs').evaluate((element) => element.getAttribute('data-job-count'));
    assert(renderedJobCount === String(expectedJobs), `desktop: expected data-job-count ${expectedJobs}, got ${renderedJobCount}`);

    const openDetails = await page.locator('.f-about-job[open]').count();
    assert(openDetails === 1, `desktop: expected exactly 1 open career details row, got ${openDetails}`);

    const firstJobIconColor = await page.locator('.f-about-job[open] .f-about-job__icon').first().evaluate((element) => getComputedStyle(element).backgroundColor);
    assert(firstJobIconColor === 'rgb(35, 40, 47)', `desktop: open career icon should be graphite minus, got ${firstJobIconColor}`);

    if (usesJobsFallback) {
      assert(html.includes('Montážní technik'), 'desktop: fallback career is missing Montážní technik');
      assert(html.includes('Obchodník na prodejně v Moravanech'), 'desktop: fallback career is missing collapsed Figma job row');
      assert(html.includes('Kontaktujte nás'), 'desktop: fallback career is missing primary CTA');
      assert(html.includes('Více na pracovním portále'), 'desktop: fallback career is missing secondary CTA');
    } else {
      const firstAdminTitle = adminJobs[0]?.title?.rendered?.replace(/<[^>]*>/g, '').trim();
      const firstRenderedTitle = await page.locator('.f-about-job').first().locator('h3').innerText();

      if (firstAdminTitle) {
        assert(firstRenderedTitle.trim() === firstAdminTitle, `desktop: first admin job should render first, expected ${firstAdminTitle}, got ${firstRenderedTitle}`);
      }
    }

    const footerBackground = await page.locator('.f-footer--arctic').first().evaluate((element) => getComputedStyle(element).backgroundImage);
    assert(footerBackground.includes('footer-background'), `desktop: footer mountain image must stay visible, got ${footerBackground}`);

    await assertBox(page, '.f-main--about-figma', { x: 0, y: 441, width: 1920, height: 2772 + jobsExtra }, 4, 'desktop.main');
    await assertBox(page, '.f-about-figma__stats', { x: 260, y: 1122, width: 1040, height: 119 }, 4, 'desktop.stats');
    await assertBox(page, '.f-about-team-carousel', { x: 260, y: 1658, width: 1400, height: 493 }, 12, 'desktop.teamCarousel');
    await assertBox(page, '.f-about-person:nth-child(1) .f-about-person__media', { x: 260, y: 1658, width: 336, height: 335 }, 4, 'desktop.teamMediaOne');

    if (usesJobsFallback) {
      await assertBox(page, '.f-about-figma__jobs', { x: 260, y: 2457, width: 1401, height: 758 }, 4, 'desktop.jobs');
      await assertBox(page, '.f-about-job[open]', { x: 260, y: 2457, width: 1401, height: 526 }, 4, 'desktop.openJob');
      await assertBox(page, '.page-template-template-about .f-contact-cta', { x: 260, y: 3328, width: 1400, height: 455 }, 4, 'desktop.contactCta');
    } else {
      const jobsBox = await box(page, '.f-about-figma__jobs', 'desktop.jobs');
      assertClose(jobsBox.x, 260, 4, 'desktop.jobs.x');
      assertClose(jobsBox.y, 2457, 4, 'desktop.jobs.y');
      assertClose(jobsBox.width, 1401, 4, 'desktop.jobs.width');
      assert(jobsBox.height >= 526, `desktop.jobs.height should fit at least the open job card, got ${round(jobsBox.height)}`);
    }

    await page.setViewportSize({ width: 375, height: 900 });
    await page.goto(`${baseUrl}/o-nas/`, { waitUntil: 'networkidle' });

    const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
    assert(overflow <= 0, `mobile: horizontal overflow ${overflow}`);

    const mobilePeople = await page.locator('.f-about-person').count();
    assert(mobilePeople === expectedPeople, `mobile: expected ${expectedPeople} about team cards, got ${mobilePeople}`);

    const mobileJobs = await page.locator('.f-about-job').count();
    assert(mobileJobs === expectedJobs, `mobile: expected ${expectedJobs} career rows, got ${mobileJobs}`);

    const mobileOpenDetails = await page.locator('.f-about-job[open]').count();
    assert(mobileOpenDetails === 1, `mobile: expected exactly 1 open career details row, got ${mobileOpenDetails}`);

    const mobileWaitingMedia = await page.locator('.f-about-person__media[data-asset-status="WAITING_ON_OWNER"]').count();
    const mobileTeamImages = await page.locator('.f-about-person__media img').count();
    assert(mobileTeamImages + mobileWaitingMedia === mobilePeople, `mobile: every team card needs image or explicit waiting media, got ${mobileTeamImages} images and ${mobileWaitingMedia} placeholders for ${mobilePeople} cards`);

    if (usesFigmaFallback) {
      assert(mobileWaitingMedia === 0, `mobile: Figma fallback portraits should not use WAITING_ON_OWNER placeholders, got ${mobileWaitingMedia}`);
      assert(mobileTeamImages === 4, `mobile: expected 4 Figma team portrait images, got ${mobileTeamImages}`);
    }

    console.log('About smoke passed.');
  } finally {
    await browser.close();
  }
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});