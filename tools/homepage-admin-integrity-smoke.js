const { chromium } = require('playwright-core');

const baseUrl = (process.env.ARCTIC_BASE_URL || 'http://localhost:8090').replace(/\/$/, '');
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

(async () => {
  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1 });

  try {
    const response = await page.goto(`${baseUrl}/`, { waitUntil: 'domcontentloaded', timeout: 90000 });
    assert(response && response.status() < 400, `Homepage returned ${response ? response.status() : 'no response'}`);
    await page.waitForLoadState('networkidle', { timeout: 12000 }).catch(() => {});

    const state = await page.evaluate(() => {
      const text = (selector) => document.querySelector(selector)?.textContent.trim().replace(/\s+/g, ' ') || '';
      const sectionSource = (selector) => document.querySelector(selector)?.getAttribute('data-content-source') || '';

      return {
        introHeading: text('.template--homepage .f-page__content h2'),
        introParagraph: text('.template--homepage .f-page__content p'),
        benefitSource: sectionSource('.f-section--arctic-benefits'),
        benefitCardCount: document.querySelectorAll('.f-arctic-benefit').length,
        benefitImageCount: document.querySelectorAll('.f-arctic-benefit img').length,
        benefitTexts: Array.from(document.querySelectorAll('.f-arctic-benefit')).map((card) => card.textContent.trim().replace(/\s+/g, ' ')),
        progressSource: sectionSource('.f-section--progress'),
        progressStepCount: document.querySelectorAll('.f-progress-steps li').length,
        progressText: text('.f-section--progress'),
      };
    });

    assert(state.introHeading.includes('Jsme výhradní prodejce'), `Homepage intro heading changed unexpectedly: ${state.introHeading}`);
    assert(state.introParagraph.includes('nakonec projekt zrealizujeme'), `Homepage intro paragraph is truncated: ${state.introParagraph}`);
    assert(state.benefitSource === 'homepage-meta', `Benefits source is ${state.benefitSource}`);
    assert(state.benefitCardCount === 3, `Expected 3 benefit cards, got ${state.benefitCardCount}`);
    assert(state.benefitImageCount === 3, `Expected 3 benefit icons, got ${state.benefitImageCount}`);
    assert(state.benefitTexts.some((text) => text.includes('Mont')), 'Benefits are missing install card');
    assert(state.benefitTexts.some((text) => text.includes('Podpora')), 'Benefits are missing support card');
    assert(state.benefitTexts.some((text) => text.includes('Servis')), 'Benefits are missing service card');
    assert(state.progressSource === 'homepage-meta', `Progress source is ${state.progressSource}`);
    assert(state.progressStepCount === 6, `Expected 6 progress steps, got ${state.progressStepCount}`);
    assert(state.progressText.includes('01') && state.progressText.includes('06'), 'Progress steps are missing first/last numbered items');
  } finally {
    await browser.close();
  }

  console.log('Homepage admin integrity smoke passed.');
})().catch((error) => {
  console.error(error.message || error);
  process.exit(1);
});
