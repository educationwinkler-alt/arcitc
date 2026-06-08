const { chromium } = require('playwright-core');
const fs = require('fs');
const path = require('path');

const baseUrl = (process.env.ARCTIC_BASE_URL || 'http://localhost:8090').replace(/\/$/, '');
const label = baseUrl.includes('localhost') ? 'local' : 'production';
const outDir = path.join('docs', 'screenshots', 'homepage-content-clipping-2026-06-08');

function fail(message, details = undefined) {
  const error = new Error(message);
  if (details) {
    error.details = details;
  }
  throw error;
}

(async () => {
  fs.mkdirSync(outDir, { recursive: true });

  const browser = await chromium.launch({
    channel: process.env.PLAYWRIGHT_CHANNEL || 'chrome',
    headless: true,
  });

  const page = await browser.newPage({
    viewport: { width: 1920, height: 1080 },
    deviceScaleFactor: 1,
  });

  await page.goto(`${baseUrl}/`, { waitUntil: 'networkidle', timeout: 90000 });
  await page.waitForTimeout(1200);

  const state = await page.evaluate(() => {
    const paragraph = document.querySelector('.template--homepage .f-page__content p');
    const heading = document.querySelector('.template--homepage .f-page__content h2');
    const button = document.querySelector('.template--homepage .f-page__content .wp-block-button__link, .template--homepage .f-page__content .a-button');

    const rect = (element) => element ? {
      x: element.getBoundingClientRect().x,
      y: element.getBoundingClientRect().y,
      width: element.getBoundingClientRect().width,
      height: element.getBoundingClientRect().height,
      top: element.getBoundingClientRect().top,
      right: element.getBoundingClientRect().right,
      bottom: element.getBoundingClientRect().bottom,
      left: element.getBoundingClientRect().left,
    } : null;

    const paragraphStyle = paragraph ? getComputedStyle(paragraph) : null;

    return {
      url: window.location.href,
      headingText: heading ? heading.textContent.trim().replace(/\s+/g, ' ') : '',
      paragraphText: paragraph ? paragraph.textContent.trim().replace(/\s+/g, ' ') : '',
      paragraph: paragraph && {
        rect: rect(paragraph),
        clientHeight: paragraph.clientHeight,
        scrollHeight: paragraph.scrollHeight,
        computedHeight: paragraphStyle.height,
        overflow: paragraphStyle.overflow,
        maxHeight: paragraphStyle.maxHeight,
      },
      button: button && {
        text: button.textContent.trim().replace(/\s+/g, ' '),
        rect: rect(button),
      },
    };
  });

  await page.screenshot({
    path: path.join(outDir, `${label}-homepage-content.png`),
    fullPage: false,
  });

  fs.writeFileSync(path.join(outDir, `${label}-audit.json`), JSON.stringify(state, null, 2));

  if (!state.paragraph) {
    fail('Homepage intro paragraph is missing.', state);
  }

  if (!state.paragraphText.includes('nakonec projekt zrealizujeme')) {
    fail('Homepage intro paragraph is missing its expected ending.', state);
  }

  if (state.paragraph.overflow === 'hidden' && state.paragraph.scrollHeight > state.paragraph.clientHeight + 1) {
    fail('Homepage intro paragraph is visually clipped by fixed height/overflow.', state);
  }

  if (state.paragraph.rect.height < state.paragraph.scrollHeight - 1) {
    fail('Homepage intro paragraph box is shorter than its rendered text.', state);
  }

  if (!state.button || state.button.rect.top < state.paragraph.rect.bottom + 12) {
    fail('Homepage intro CTA is missing or overlaps the intro paragraph.', state);
  }

  console.log(`Homepage content clipping smoke passed: ${state.paragraph.rect.height}px paragraph, ${state.paragraph.scrollHeight}px content.`);
  await browser.close();
})().catch((error) => {
  console.error(error.message);
  if (error.details) {
    console.error(JSON.stringify(error.details, null, 2));
  }
  process.exit(1);
});
