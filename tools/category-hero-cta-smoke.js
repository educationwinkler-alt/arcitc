const { chromium } = require('playwright-core');

const baseUrl = (process.env.ARCTIC_BASE_URL || 'http://localhost:8090').replace(/\/$/, '');
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

const pages = [
  { id: 'hot-tubs', path: '/virivky/' },
  { id: 'swimspas', path: '/swimspa/' },
];

const viewports = [
  { width: 1097, height: 617 },
  { width: 1280, height: 720 },
  { width: 1399, height: 800 },
  { width: 1400, height: 800 },
  { width: 1440, height: 900 },
  { width: 1536, height: 864 },
  { width: 1600, height: 900 },
  { width: 1920, height: 1080 },
];

function assert(condition, message, detail = {}) {
  if (!condition) {
    const suffix = Object.keys(detail).length ? `\n${JSON.stringify(detail, null, 2)}` : '';
    throw new Error(`${message}${suffix}`);
  }
}

async function measure(page) {
  return page.evaluate(() => {
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
        top: Math.round(box.top),
        right: Math.round(box.right),
        bottom: Math.round(box.bottom),
        left: Math.round(box.left),
      };
    };

    const hero = document.querySelector('.tax-product-category .f-heading--term');
    const cta = document.querySelector('.tax-product-category .f-heading--term .f-heading__headline .a-button');
    const ctaStyle = cta ? getComputedStyle(cta) : null;

    return {
      hero: rect(hero),
      cta: rect(cta),
      ctaText: cta ? cta.textContent.trim().replace(/\s+/g, ' ') : '',
      ctaDisplay: ctaStyle ? ctaStyle.display : '',
      ctaVisibility: ctaStyle ? ctaStyle.visibility : '',
      viewport: {
        width: window.innerWidth,
        height: window.innerHeight,
      },
    };
  });
}

(async () => {
  const browser = await chromium.launch({ headless: true, executablePath: chromePath });

  try {
    for (const viewport of viewports) {
      const page = await browser.newPage({ viewport });

      for (const pageDef of pages) {
        const url = `${baseUrl}${pageDef.path}`;
        await page.goto(url, { waitUntil: 'networkidle' });

        const state = await measure(page);
        const detail = { url, viewport, state };

        assert(state.hero, `${pageDef.id}: category hero is missing`, detail);
        assert(state.cta, `${pageDef.id}: category hero CTA is missing`, detail);
        assert(state.ctaDisplay !== 'none', `${pageDef.id}: category hero CTA is display:none`, detail);
        assert(state.ctaVisibility !== 'hidden', `${pageDef.id}: category hero CTA is hidden`, detail);
        assert(state.cta.width >= 120 && state.cta.height >= 40, `${pageDef.id}: category hero CTA is too small`, detail);
        assert(state.cta.bottom <= state.hero.bottom - 16, `${pageDef.id}: category hero CTA is clipped by hero`, detail);
        assert(state.cta.top >= state.hero.top, `${pageDef.id}: category hero CTA starts above hero`, detail);
        assert(state.cta.bottom <= state.viewport.height - 8, `${pageDef.id}: category hero CTA falls below first viewport`, detail);
      }

      await page.close();
    }
  } finally {
    await browser.close();
  }

  console.log(`Category hero CTA smoke passed for ${pages.length} pages and ${viewports.length} viewports.`);
})().catch((error) => {
  console.error(error.message || error);
  process.exit(1);
});
