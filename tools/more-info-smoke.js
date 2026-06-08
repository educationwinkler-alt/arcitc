const { chromium } = require('playwright-core');

const baseUrl = (process.env.ARCTIC_BASE_URL || 'http://localhost:8090').replace(/\/$/, '');
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

const expectedCards = [
  'Služby',
  'Certifikáty',
  'Záruka',
  'Kolik stojí provoz a údržba',
  'Časté otázky',
  'Reference',
  'O nás',
  'Showroom',
  'Servis',
  'Kontakt',
];

function assert(condition, message, detail = {}) {
  if (!condition) {
    const suffix = Object.keys(detail).length ? `\n${JSON.stringify(detail, null, 2)}` : '';
    throw new Error(`${message}${suffix}`);
  }
}

(async () => {
  const response = await fetch(`${baseUrl}/dalsi-informace/`, { redirect: 'manual' });
  assert(response.status === 200, `/dalsi-informace/ must render directly, got HTTP ${response.status}`, {
    status: response.status,
    location: response.headers.get('location'),
  });

  const browser = await chromium.launch({ headless: true, executablePath: chromePath });

  try {
    const page = await browser.newPage({ viewport: { width: 1920, height: 1080 } });
    await page.goto(`${baseUrl}/dalsi-informace/`, { waitUntil: 'networkidle' });

    const desktop = await page.evaluate(() => {
      const cardNodes = Array.from(document.querySelectorAll('.page-template-template-more-info .f-figma-card--info'));
      const rect = (element) => {
        if (!element) {
          return null;
        }

        const box = element.getBoundingClientRect();

        return {
          x: Math.round(box.x),
          y: Math.round(box.y),
          width: Math.round(box.width),
          height: Math.round(box.height),
          bottom: Math.round(box.bottom),
        };
      };

      return {
        url: window.location.href,
        h1: document.querySelector('h1')?.textContent.trim() || '',
        cardCount: cardNodes.length,
        cardTitles: cardNodes.map((card) => card.querySelector('strong')?.textContent.trim() || ''),
        cardHrefs: cardNodes.map((card) => card.getAttribute('href') || ''),
        grid: rect(document.querySelector('.f-figma-card-grid--info')),
        firstCard: rect(cardNodes[0]),
        contact: rect(document.querySelector('.f-contact-cta')),
        footer: rect(document.querySelector('.f-footer--arctic')),
      };
    });

    assert(desktop.url.endsWith('/dalsi-informace/'), 'More info page must not redirect in the browser', desktop);
    assert(desktop.h1 === 'Další informace', 'More info page has wrong H1', desktop);
    assert(desktop.cardCount === expectedCards.length, `More info page must render ${expectedCards.length} cards`, desktop);

    for (const title of expectedCards) {
      assert(desktop.cardTitles.includes(title), `More info page is missing card "${title}"`, desktop);
    }

    assert(desktop.cardHrefs.every((href) => href && !href.includes('#order-progress')), 'More info page must not link cards to the old homepage redirect target', desktop);
    assert(desktop.grid && desktop.grid.width >= 1300, 'More info desktop grid is collapsed', desktop);
    assert(desktop.firstCard && desktop.firstCard.width >= 300 && desktop.firstCard.height >= 120, 'More info first card is collapsed', desktop);
    assert(desktop.contact && desktop.contact.y > desktop.grid.bottom, 'More info contact CTA is missing or overlaps cards', desktop);
    assert(desktop.footer && desktop.footer.y > desktop.contact.bottom, 'More info footer is missing or overlaps contact CTA', desktop);

    await page.setViewportSize({ width: 390, height: 900 });
    await page.goto(`${baseUrl}/dalsi-informace/`, { waitUntil: 'networkidle' });

    const mobileState = await page.evaluate(() => {
      const grid = document.querySelector('.f-figma-card-grid--info');
      const cards = Array.from(document.querySelectorAll('.page-template-template-more-info .f-figma-card--info'));
      const footer = document.querySelector('.f-footer--arctic');
      const rect = (element) => {
        if (!element) {
          return null;
        }

        const box = element.getBoundingClientRect();

        return {
          x: Math.round(box.x),
          y: Math.round(box.y),
          width: Math.round(box.width),
          height: Math.round(box.height),
          bottom: Math.round(box.bottom),
        };
      };

      return {
        scrollWidth: document.documentElement.scrollWidth,
        viewportWidth: window.innerWidth,
        cardCount: cards.length,
        grid: rect(grid),
        firstCard: rect(cards[0]),
        footer: rect(footer),
      };
    });

    assert(mobileState.scrollWidth <= mobileState.viewportWidth + 1, 'More info mobile layout creates horizontal overflow', mobileState);
    assert(mobileState.cardCount === expectedCards.length, 'More info mobile card count changed', mobileState);
    assert(mobileState.grid && mobileState.grid.width <= mobileState.viewportWidth, 'More info mobile grid exceeds viewport', mobileState);
    assert(mobileState.firstCard && mobileState.firstCard.width <= mobileState.viewportWidth - 30, 'More info mobile card exceeds viewport', mobileState);
    assert(mobileState.footer, 'More info mobile footer is missing', mobileState);
  } finally {
    await browser.close();
  }

  console.log('More info smoke passed: direct 200, 10 cards, contact CTA, footer.');
})().catch((error) => {
  console.error(error.message || error);
  process.exit(1);
});
