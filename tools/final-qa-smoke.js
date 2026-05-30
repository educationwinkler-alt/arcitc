const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

const desktopPaths = [
  '/',
  '/virivky/',
  '/swimspa/',
  '/product/timberwolf/',
  '/reference/',
  '/kontakt/',
  '/o-nas/',
  '/showroom/',
  '/servis/',
  '/podpora/',
  '/ke-stazeni/',
];

const mobilePaths = [
  '/',
  '/virivky/',
  '/reference/',
  '/kontakt/',
  '/podpora/',
  '/ke-stazeni/',
];

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

async function fetchHtml(path) {
  const response = await fetch(`${baseUrl}${path}`);

  if (!response.ok) {
    throw new Error(`${path} returned ${response.status}`);
  }

  return response.text();
}

function assertReferenceArchive(html) {
  const projectLinks = html.match(/href=(["'])[^"']*\/project\//g) || [];
  assert(projectLinks.length === 0, `/reference/ archive contains /project/ links: ${projectLinks.join(', ')}`);

  const lightboxLinks = html.match(/class=(["'])[^"']*\bjs-image\b/g) || [];
  assert(lightboxLinks.length >= 6, `/reference/ archive should expose PhotoSwipe/lightbox cards, got ${lightboxLinks.length}`);
}

function assertHtmlContracts(path, html) {
  assert(!html.includes('f-footer--handoff'), `${path} still contains forbidden f-footer--handoff class`);

  if (path === '/reference/') {
    assertReferenceArchive(html);
  }
}

async function assertNoHorizontalOverflow(page, label) {
  const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
  assert(overflow <= 2, `${label} has horizontal overflow of ${overflow}px`);
}

async function assertFooterContract(page, label) {
  const footer = await page.locator('.f-footer--arctic').first();
  assert(await footer.count(), `${label} is missing .f-footer--arctic`);

  const state = await footer.evaluate((element) => {
    const rect = element.getBoundingClientRect();
    const style = getComputedStyle(element);
    const previous = element.previousElementSibling;
    const previousRect = previous ? previous.getBoundingClientRect() : null;

    return {
      className: element.className,
      backgroundImage: style.backgroundImage,
      backgroundColor: style.backgroundColor,
      y: rect.y,
      previousBottom: previousRect ? previousRect.bottom : null,
    };
  });

  assert(!state.className.includes('f-footer--handoff'), `${label} footer contains forbidden handoff class`);
  assert(state.backgroundImage.includes('footer-background'), `${label} footer mountain background is missing: ${state.backgroundImage}`);
  assert(state.backgroundColor !== 'rgb(35, 40, 47)', `${label} footer fell back to solid navy`);

  if (typeof state.previousBottom === 'number') {
    const gap = state.y - state.previousBottom;
    assert(gap > -120 && gap < 220, `${label} footer handoff gap looks wrong: ${Math.round(gap)}px`);
  }
}

async function assertVisibleImagesLoaded(page, label) {
  const broken = await page.evaluate(() => {
    const viewportHeight = window.innerHeight;
    const viewportWidth = window.innerWidth;

    return [...document.querySelectorAll('main img, footer img')]
      .filter((image) => {
        const rect = image.getBoundingClientRect();
        return rect.width > 8 && rect.height > 8 && rect.bottom > 0 && rect.right > 0 && rect.top < viewportHeight && rect.left < viewportWidth;
      })
      .filter((image) => image.naturalWidth < 1 || image.naturalHeight < 1)
      .map((image) => image.currentSrc || image.src || image.getAttribute('src') || image.getAttribute('data-src') || '(missing source)');
  });

  assert(broken.length === 0, `${label} has broken visible images: ${broken.join(', ')}`);
}

async function assertProductAffordances(page) {
  await page.goto(`${baseUrl}/product/timberwolf/`, { waitUntil: 'networkidle', timeout: 90000 });

  const state = await page.evaluate(() => {
    const staticCards = [...document.querySelectorAll('.f-product-benefit--static')].map((card) => ({
      hasTrigger: !!card.querySelector('.f-product-benefit__trigger'),
      hasMore: !!card.querySelector('.f-product-benefit__more'),
      text: card.textContent.trim().replace(/\s+/g, ' ').slice(0, 80),
    }));
    const interactiveCards = [...document.querySelectorAll('.f-product-benefit--interactive')].map((card) => ({
      hasTrigger: !!card.querySelector('.f-product-benefit__trigger'),
      hasMore: !!card.querySelector('.f-product-benefit__more'),
      text: card.textContent.trim().replace(/\s+/g, ' ').slice(0, 80),
    }));

    return { staticCards, interactiveCards };
  });

  assert(state.staticCards.length > 0, '/product/timberwolf/ has no static benefit cards to verify');
  for (const card of state.staticCards) {
    assert(!card.hasTrigger && !card.hasMore, `/product/timberwolf/ static card looks interactive: ${card.text}`);
  }

  assert(state.interactiveCards.length > 0, '/product/timberwolf/ has no interactive benefit cards to verify');
  for (const card of state.interactiveCards) {
    assert(card.hasTrigger && card.hasMore, `/product/timberwolf/ interactive card is missing trigger/more affordance: ${card.text}`);
  }
}

async function assertWaitingOnOwnerMarkers(page) {
  const expectations = [
    ['/kontakt/', '.f-contact-card__avatar[data-asset-status="WAITING_ON_OWNER"]', 6],
    ['/o-nas/', '.f-about-person__media[data-asset-status="WAITING_ON_OWNER"]', 4],
    ['/zaruka/', '.f-warranty-card[data-asset-status="WAITING_ON_OWNER"]', 3],
  ];

  for (const [path, selector, expected] of expectations) {
    await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle', timeout: 90000 });
    const count = await page.locator(selector).count();
    assert(count === expected, `${path} expected ${expected} ${selector}, got ${count}`);
  }
}

(async () => {
  for (const path of desktopPaths) {
    const html = await fetchHtml(path);
    assertHtmlContracts(path, html);
  }

  const browser = await chromium.launch({ executablePath: chromePath, headless: true });

  try {
    const page = await browser.newPage({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1 });

    for (const path of desktopPaths) {
      await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle', timeout: 90000 });
      await assertNoHorizontalOverflow(page, `${path} desktop`);
      await assertFooterContract(page, `${path} desktop`);
      await assertVisibleImagesLoaded(page, `${path} desktop`);
    }

    await assertProductAffordances(page);
    await assertWaitingOnOwnerMarkers(page);

    const mobile = await browser.newPage({ viewport: { width: 390, height: 900 }, deviceScaleFactor: 1 });

    for (const path of mobilePaths) {
      await mobile.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle', timeout: 90000 });
      await assertNoHorizontalOverflow(mobile, `${path} mobile`);
      await assertFooterContract(mobile, `${path} mobile`);
      await assertVisibleImagesLoaded(mobile, `${path} mobile`);
    }
  } finally {
    await browser.close();
  }

  console.log('Final QA smoke passed.');
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
