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
    const adminJobs = (await fetchRestCollection('job')).sort((left, right) => {
      const leftOrder = Number(left.menu_order || 0);
      const rightOrder = Number(right.menu_order || 0);

      if (leftOrder !== rightOrder) {
        return leftOrder - rightOrder;
      }

      return String(left.date || '').localeCompare(String(right.date || ''));
    });
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

    if (adminMembers.some((member) => String(member.title?.rendered || '').includes('Koutn'))) {
      const koutnyTeamCard = await page.evaluate(() => {
        const card = Array.from(document.querySelectorAll('.f-about-person')).find((item) => item.textContent.includes('Koutn'));
        const media = card ? card.querySelector('.f-about-person__media') : null;
        const image = media ? media.querySelector('img') : null;
        const mediaBox = media ? media.getBoundingClientRect() : null;
        const imageBox = image ? image.getBoundingClientRect() : null;

        return {
          found: !!card,
          status: media ? media.getAttribute('data-asset-status') || '' : '',
          fallbackClass: media ? media.classList.contains('f-about-person__media--avatar-fallback') : false,
          src: image ? image.currentSrc || image.src : '',
          media: mediaBox ? { width: mediaBox.width, height: mediaBox.height } : null,
          image: imageBox ? { width: imageBox.width, height: imageBox.height } : null,
        };
      });

      assert(koutnyTeamCard.found, 'desktop: Koutny member card is missing from About team');
      assert(koutnyTeamCard.status === 'admin-member', `desktop: Koutny team media status is ${koutnyTeamCard.status}`);
      assert(!koutnyTeamCard.fallbackClass, 'desktop: Koutny team media must use a real Featured image, not the avatar fallback class');
      assert(koutnyTeamCard.src.includes('about-team-tomas-portrait'), `desktop: Koutny team card should use his clean Featured image source, got ${koutnyTeamCard.src}`);
      assert(koutnyTeamCard.media && koutnyTeamCard.image, 'desktop: Koutny team card needs measurable media and image boxes');
      assert(koutnyTeamCard.image.width >= koutnyTeamCard.media.width - 3, `desktop: Koutny team image should fill the framed media interior, got image width ${koutnyTeamCard.image.width} inside media width ${koutnyTeamCard.media.width}`);
      assert(koutnyTeamCard.image.height >= koutnyTeamCard.media.height - 3, `desktop: Koutny team image should fill the framed media interior, got image height ${koutnyTeamCard.image.height} inside media height ${koutnyTeamCard.media.height}`);
    }

    const html = await page.content();

    if (usesFigmaFallback) {
      assert(waitingMedia === 0, `desktop: Figma fallback portraits should not use WAITING_ON_OWNER placeholders, got ${waitingMedia}`);
      assert(teamImages === 4, `desktop: expected 4 clean fallback team portrait images, got ${teamImages}`);

      for (const asset of [
        'uploads/import/figma/about-team-vladimir-portrait.png',
        'uploads/import/figma/about-team-lukas-portrait.png',
        'uploads/import/figma/about-team-helena-portrait.png',
        'uploads/import/figma/about-team-alena-portrait.png',
      ]) {
        assert(html.includes(asset), `desktop: missing clean fallback team portrait ${asset}`);
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

    const teamCarouselPrevButtons = await page.locator('.f-about-team__prev').count();
    const teamCarouselNextButtons = await page.locator('.f-about-team__next').count();
    assert(teamCarouselPrevButtons === 1, `desktop: expected Figma team carousel previous button, got ${teamCarouselPrevButtons}`);
    assert(teamCarouselNextButtons === 1, `desktop: expected Figma team carousel next button, got ${teamCarouselNextButtons}`);

    const initialTeamControls = await page.evaluate(() => {
      const prev = document.querySelector('.js-about-team-carousel__prev');
      const next = document.querySelector('.js-about-team-carousel__next');

      return {
        prevHidden: !!prev?.hidden,
        nextHidden: !!next?.hidden,
      };
    });
    assert(initialTeamControls.prevHidden, 'desktop: team carousel previous arrow should be hidden at the left edge');
    assert(!initialTeamControls.nextHidden, 'desktop: team carousel next arrow should be visible when more team cards exist');

    await page.locator('.js-about-team-carousel__next').click();
    await page.waitForFunction(() => {
      const prev = document.querySelector('.js-about-team-carousel__prev');
      const track = document.querySelector('.js-about-team-carousel__track');

      return prev && track && !prev.hidden && track.scrollLeft > 4;
    });

    const scrolledTeamControls = await page.evaluate(() => {
      const prev = document.querySelector('.js-about-team-carousel__prev');
      const next = document.querySelector('.js-about-team-carousel__next');

      return {
        prevHidden: !!prev?.hidden,
        nextHidden: !!next?.hidden,
      };
    });
    assert(!scrolledTeamControls.prevHidden, 'desktop: team carousel previous arrow should appear after scrolling right');
    assert(!scrolledTeamControls.nextHidden, 'desktop: team carousel next arrow should stay visible before the end');

    await page.evaluate(() => {
      const track = document.querySelector('.js-about-team-carousel__track');

      if (track) {
        track.scrollLeft = track.scrollWidth;
      }
    });
    await page.waitForFunction(() => {
      const prev = document.querySelector('.js-about-team-carousel__prev');
      const next = document.querySelector('.js-about-team-carousel__next');

      return prev && next && !prev.hidden && next.hidden;
    });

    const endTeamControls = await page.evaluate(() => {
      const prev = document.querySelector('.js-about-team-carousel__prev');
      const next = document.querySelector('.js-about-team-carousel__next');

      return {
        prevHidden: !!prev?.hidden,
        nextHidden: !!next?.hidden,
      };
    });
    assert(!endTeamControls.prevHidden, 'desktop: team carousel previous arrow should stay visible at the right edge');
    assert(endTeamControls.nextHidden, 'desktop: team carousel next arrow should hide at the right edge');

    await page.evaluate(() => {
      const track = document.querySelector('.js-about-team-carousel__track');

      if (track) {
        track.scrollLeft = 0;
      }
    });
    await page.waitForFunction(() => {
      const prev = document.querySelector('.js-about-team-carousel__prev');
      const next = document.querySelector('.js-about-team-carousel__next');

      return prev && next && prev.hidden && !next.hidden;
    });
    await page.evaluate(() => window.scrollTo(0, 0));
    await page.waitForFunction(() => window.scrollY === 0);

    const aboutNavPosition = await page.locator('.f-links--about').evaluate((element) => getComputedStyle(element).position);
    assert(aboutNavPosition === 'relative', `desktop: about tab navigation must stay in document flow and not cover sections, got ${aboutNavPosition}`);

    const mediaRadius = await page.locator('.f-about-person__media').first().evaluate((element) => getComputedStyle(element).borderRadius);
    assert(mediaRadius === '40px', `desktop: team image shape must use Figma 40px radius, got ${mediaRadius}`);

    const mediaFrame = await page.locator('.f-about-person__media').first().evaluate((element) => {
      const image = element.querySelector('img');
      const mediaBox = element.getBoundingClientRect();
      const imageBox = image ? image.getBoundingClientRect() : null;
      const mediaStyle = getComputedStyle(element);

      return {
        borderTopWidth: mediaStyle.borderTopWidth,
        boxShadow: mediaStyle.boxShadow,
        media: { x: mediaBox.x, y: mediaBox.y, width: mediaBox.width, height: mediaBox.height },
        image: imageBox ? { x: imageBox.x, y: imageBox.y, width: imageBox.width, height: imageBox.height } : null,
      };
    });
    assert(mediaFrame.borderTopWidth === '1px', `desktop: team media frame must render one CSS border, got ${mediaFrame.borderTopWidth}`);
    assert(mediaFrame.boxShadow !== 'none', 'desktop: team media frame must render the CSS shadow instead of relying on baked image shadow');
    assert(mediaFrame.image, 'desktop: Figma team image is missing an img element');
    assert(mediaFrame.image.x >= mediaFrame.media.x && mediaFrame.image.x <= mediaFrame.media.x + 2, `desktop: team image x must sit inside CSS frame, got image ${mediaFrame.image.x} and media ${mediaFrame.media.x}`);
    assert(mediaFrame.image.y >= mediaFrame.media.y && mediaFrame.image.y <= mediaFrame.media.y + 2, `desktop: team image y must sit inside CSS frame, got image ${mediaFrame.image.y} and media ${mediaFrame.media.y}`);
    assert(mediaFrame.image.width >= mediaFrame.media.width - 3 && mediaFrame.image.width <= mediaFrame.media.width, `desktop: team image width must fill CSS frame interior, got image ${mediaFrame.image.width} and media ${mediaFrame.media.width}`);
    assert(mediaFrame.image.height >= mediaFrame.media.height - 3 && mediaFrame.image.height <= mediaFrame.media.height, `desktop: team image height must fill CSS frame interior, got image ${mediaFrame.image.height} and media ${mediaFrame.media.height}`);

    const teamTextFrame = await page.locator('.f-about-person').first().evaluate((element) => {
      const heading = element.querySelector('h3');
      const role = element.querySelector('.f-about-person__role');
      const description = element.querySelector('.f-about-person__description');
      const read = (node) => {
        const box = node.getBoundingClientRect();
        return { width: box.width, maxWidth: getComputedStyle(node).maxWidth };
      };

      return {
        heading: read(heading),
        role: read(role),
        description: read(description),
      };
    });
    assertClose(teamTextFrame.heading.width, 250, 1, 'desktop.teamHeading.width');
    assertClose(teamTextFrame.role.width, 250, 1, 'desktop.teamRole.width');
    assertClose(teamTextFrame.description.width, 250, 1, 'desktop.teamDescription.width');

    const statColor = await page.locator('.f-about-figma__stats strong').first().evaluate((element) => getComputedStyle(element).color);
    assert(statColor === 'rgb(163, 31, 55)', `desktop: stats must use Figma red token, got ${statColor}`);

    const jobs = await page.locator('.f-about-job').count();
    assert(jobs === expectedJobs, `desktop: expected ${expectedJobs} career rows, got ${jobs}`);

    const renderedJobCount = await page.locator('.f-about-figma__jobs').evaluate((element) => element.getAttribute('data-job-count'));
    assert(renderedJobCount === String(expectedJobs), `desktop: expected data-job-count ${expectedJobs}, got ${renderedJobCount}`);

    const renderedJobsSource = await page.locator('.f-about-figma__jobs').evaluate((element) => element.getAttribute('data-content-source') || '');
    assert(
      renderedJobsSource === (usesJobsFallback ? 'static-fallback' : 'job-cpt'),
      `desktop: expected career source ${usesJobsFallback ? 'static-fallback' : 'job-cpt'}, got ${renderedJobsSource}`,
    );

    const initialOpenDetails = await page.locator('.f-about-job[open]').count();
    assert(initialOpenDetails === 0, `desktop: expected career rows to start closed, got ${initialOpenDetails} open rows`);

    const closedJobIconColor = await page.locator('.f-about-job:not([open]) .f-about-job__icon').first().evaluate((element) => getComputedStyle(element).backgroundColor);
    assert(closedJobIconColor === 'rgb(163, 31, 55)', `desktop: closed career icons should use Figma red, got ${closedJobIconColor}`);

    await page.evaluate(() => document.querySelector('.f-about-job:first-child .f-about-job__summary')?.click());
    await page.waitForTimeout(120);

    const openAfterFirstJobClick = await page.locator('.f-about-job[open]').count();
    assert(openAfterFirstJobClick === 1, `desktop: clicking a career plus should open exactly one row, got ${openAfterFirstJobClick}`);

    const firstJobIconColor = await page.locator('.f-about-job[open] .f-about-job__icon').first().evaluate((element) => getComputedStyle(element).backgroundColor);
    assert(firstJobIconColor === 'rgb(35, 40, 47)', `desktop: open career icon should be graphite minus, got ${firstJobIconColor}`);

    const jobPrimaryButtonColor = await page.locator('.f-about-job[open] .wp-block-button:not(.is-style-outline) .wp-block-button__link').first().evaluate((element) => getComputedStyle(element).backgroundColor);
    assert(jobPrimaryButtonColor === 'rgb(163, 31, 55)', `desktop: open career primary CTA should be red, got ${jobPrimaryButtonColor}`);

    await page.evaluate(() => document.querySelector('.f-about-job[open] .f-about-job__summary')?.click());
    await page.waitForTimeout(120);

    const openAfterMinusClick = await page.locator('.f-about-job[open]').count();
    assert(openAfterMinusClick === 0, `desktop: clicking the career minus should close the row, got ${openAfterMinusClick} open rows`);

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

      const adminJobIds = await page.locator('.f-about-job').evaluateAll((elements) => elements.map((element) => element.getAttribute('data-job-id') || ''));
      assert(adminJobIds.every((id) => Number(id) > 0), `desktop: admin career rows must expose WP job ids, got ${adminJobIds.join(', ')}`);
    }

    if (usesJobsFallback) {
      await page.evaluate(() => document.querySelector('.f-about-job:nth-child(2) .f-about-job__summary')?.click());
      await page.waitForTimeout(120);

      const fallbackOpenAfterEmptyClick = await page.locator('.f-about-job[open]').count();
      const secondFallbackTag = await page.locator('.f-about-job').nth(1).evaluate((element) => element.tagName.toLowerCase());
      const secondFallbackBox = await page.locator('.f-about-job').nth(1).boundingBox();

      assert(secondFallbackTag === 'article', `desktop: empty fallback job rows must not be expandable details, got ${secondFallbackTag}`);
      assert(fallbackOpenAfterEmptyClick === 0, `desktop: clicking an empty fallback job must not open a blank panel, got ${fallbackOpenAfterEmptyClick} open rows`);
      assertClose(secondFallbackBox.height, 96, 1, 'desktop.emptyFallbackJob.height');
    } else if (expectedJobs > 1) {
      await page.evaluate(() => document.querySelector('.f-about-job:nth-child(2) .f-about-job__summary')?.click());
      await page.waitForTimeout(120);

      const openAfterSecondJobClick = await page.locator('.f-about-job[open]').count();
      const secondAdminTag = await page.locator('.f-about-job').nth(1).evaluate((element) => element.tagName.toLowerCase());
      const secondAdminOpen = await page.locator('.f-about-job').nth(1).evaluate((element) => element.hasAttribute('open'));

      assert(secondAdminTag === 'details', `desktop: admin job rows must be expandable details, got ${secondAdminTag}`);
      assert(openAfterSecondJobClick === 1, `desktop: clicking another admin job should keep exactly one open row, got ${openAfterSecondJobClick}`);
      assert(secondAdminOpen, 'desktop: clicking the second admin job should open that row');

      await page.evaluate(() => document.querySelector('.f-about-job:nth-child(2) .f-about-job__summary')?.click());
      await page.waitForTimeout(120);

      const openAfterSecondMinusClick = await page.locator('.f-about-job[open]').count();
      assert(openAfterSecondMinusClick === 0, `desktop: clicking the second admin job minus should close all rows, got ${openAfterSecondMinusClick}`);
    }

    const footerBackground = await page.locator('.f-footer--arctic').first().evaluate((element) => getComputedStyle(element).backgroundImage);
    assert(footerBackground.includes('footer-background'), `desktop: footer mountain image must stay visible, got ${footerBackground}`);

    await assertBox(page, '.f-main--about-figma', { x: 0, y: 441, width: 1920, height: 2772 + jobsExtra }, 4, 'desktop.main');
    await assertBox(page, '.f-about-figma__stats', { x: 260, y: 1122, width: 1040, height: 119 }, 4, 'desktop.stats');
    await assertBox(page, '.f-about-team-carousel', { x: 260, y: 1658, width: 1400, height: 490 }, 4, 'desktop.teamCarousel');
    await assertBox(page, '.f-about-person:nth-child(1) .f-about-person__media', { x: 260, y: 1658, width: 336, height: 335 }, 4, 'desktop.teamMediaOne');

    if (usesJobsFallback) {
      await assertBox(page, '.f-about-figma__jobs', { x: 260, y: 2457, width: 1401, height: 328 }, 4, 'desktop.jobs');
      await assertBox(page, '.page-template-template-about .f-contact-cta', { x: 260, y: 3328, width: 1400, height: 455 }, 4, 'desktop.contactCta');
    } else {
      const jobsBox = await box(page, '.f-about-figma__jobs', 'desktop.jobs');
      assertClose(jobsBox.x, 260, 4, 'desktop.jobs.x');
      assertClose(jobsBox.y, 2457, 4, 'desktop.jobs.y');
      assertClose(jobsBox.width, 1401, 4, 'desktop.jobs.width');
      assertClose(jobsBox.height, 328, 4, 'desktop.jobs.height');
    }

    await page.evaluate(() => window.scrollTo(0, 1370));
    await page.waitForFunction(() => {
      const header = document.querySelector('.f-header');

      if (!header) {
        return false;
      }

      return header.classList.contains('is-section-nav-handoff') && header.getBoundingClientRect().bottom <= 1;
    }, null, { timeout: 2000 });

    const headerDuringAboutContent = await page.locator('.f-header').evaluate((element) => {
      const box = element.getBoundingClientRect();

      return {
        bottom: box.bottom,
        hasHandoffClass: element.classList.contains('is-section-nav-handoff'),
      };
    });

    assert(headerDuringAboutContent.hasHandoffClass, 'desktop: main header should hand off while scrolling through about content');
    assert(headerDuringAboutContent.bottom <= 1, `desktop: main header must not cover about team/career sections, bottom=${round(headerDuringAboutContent.bottom)}`);

    await page.setViewportSize({ width: 375, height: 900 });
    await page.goto(`${baseUrl}/o-nas/`, { waitUntil: 'networkidle' });

    const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
    assert(overflow <= 0, `mobile: horizontal overflow ${overflow}`);

    const mobilePeople = await page.locator('.f-about-person').count();
    assert(mobilePeople === expectedPeople, `mobile: expected ${expectedPeople} about team cards, got ${mobilePeople}`);

    const mobileJobs = await page.locator('.f-about-job').count();
    assert(mobileJobs === expectedJobs, `mobile: expected ${expectedJobs} career rows, got ${mobileJobs}`);

    const mobileOpenDetails = await page.locator('.f-about-job[open]').count();
    assert(mobileOpenDetails === 0, `mobile: expected career rows to start closed, got ${mobileOpenDetails}`);

    const mobileWaitingMedia = await page.locator('.f-about-person__media[data-asset-status="WAITING_ON_OWNER"]').count();
    const mobileTeamImages = await page.locator('.f-about-person__media img').count();
    assert(mobileTeamImages + mobileWaitingMedia === mobilePeople, `mobile: every team card needs image or explicit waiting media, got ${mobileTeamImages} images and ${mobileWaitingMedia} placeholders for ${mobilePeople} cards`);

    if (usesFigmaFallback) {
      assert(mobileWaitingMedia === 0, `mobile: Figma fallback portraits should not use WAITING_ON_OWNER placeholders, got ${mobileWaitingMedia}`);
      assert(mobileTeamImages === 4, `mobile: expected 4 clean fallback team portrait images, got ${mobileTeamImages}`);
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
