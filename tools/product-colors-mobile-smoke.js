const { chromium } = require('playwright-core');
const fs = require('fs');
const path = require('path');

const baseUrl = (process.env.ARCTIC_BASE_URL || 'http://localhost:8090').replace(/\/$/, '');
const productPath = process.env.ARCTIC_PRODUCT_PATH || '/produkt/cub/';
const label = baseUrl.includes('localhost') ? 'local' : 'production';
const outDir = path.join('docs', 'screenshots', 'product-colors-mobile-2026-06-08');

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
    viewport: { width: 393, height: 852 },
    deviceScaleFactor: 1,
    isMobile: true,
  });

  const targetUrl = `${baseUrl}${productPath}#barvy`;
  await page.goto(targetUrl, { waitUntil: 'networkidle' });
  await page.locator('#barvy').scrollIntoViewIfNeeded();
  await page.waitForTimeout(500);

  const screenshotPath = path.join(outDir, `${label}-cub-colors-mobile.png`);
  await page.screenshot({ path: screenshotPath, fullPage: false });

  const audit = await page.evaluate(() => {
    const items = Array.from(document.querySelectorAll('.f-product-colors__item'));
    const viewportWidth = window.innerWidth;
    const scrollWidth = document.documentElement.scrollWidth;

    return {
      url: window.location.href,
      viewportWidth,
      scrollWidth,
      items: items.map((item) => {
        const media = item.querySelector('img, .f-product-colors__placeholder');
        const label = Array.from(item.children).find((child) =>
          child.matches('span:not(.f-product-colors__placeholder)')
        );
        const cardRect = item.getBoundingClientRect();
        const mediaRect = media ? media.getBoundingClientRect() : null;
        const labelRect = label ? label.getBoundingClientRect() : null;
        const styles = window.getComputedStyle(item);

        return {
          slug: item.getAttribute('data-color-slug') || '',
          text: label ? label.textContent.trim() : '',
          card: {
            x: cardRect.x,
            y: cardRect.y,
            width: cardRect.width,
            height: cardRect.height,
          },
          media: mediaRect && {
            x: mediaRect.x,
            y: mediaRect.y,
            width: mediaRect.width,
            height: mediaRect.height,
          },
          label: labelRect && {
            x: labelRect.x,
            y: labelRect.y,
            width: labelRect.width,
            height: labelRect.height,
          },
          display: styles.display,
          gridTemplateColumns: styles.gridTemplateColumns,
          gridTemplateRows: styles.gridTemplateRows,
          overflow: styles.overflow,
        };
      }),
    };
  });

  fs.writeFileSync(path.join(outDir, `${label}-audit.json`), JSON.stringify(audit, null, 2));

  if (audit.scrollWidth > audit.viewportWidth + 1) {
    fail('Product color section creates horizontal overflow.', audit);
  }

  if (audit.items.length < 5) {
    fail('Expected at least five product color swatches.', audit);
  }

  for (const item of audit.items) {
    if (!item.media || !item.label) {
      fail(`Missing media or label for color swatch ${item.slug || item.text}.`, item);
    }

    const cardCenter = item.card.x + item.card.width / 2;
    const mediaCenter = item.media.x + item.media.width / 2;
    if (Math.abs(cardCenter - mediaCenter) > 2) {
      fail(`Color swatch media is not centered for ${item.slug || item.text}.`, item);
    }

    if (item.media.width < 60 || item.media.height < 60 || item.media.width > 80 || item.media.height > 80) {
      fail(`Color swatch media has unstable mobile dimensions for ${item.slug || item.text}.`, item);
    }

    if (item.label.y < item.media.y + item.media.height) {
      fail(`Color swatch label overlaps media for ${item.slug || item.text}.`, item);
    }

    if (item.label.y + item.label.height > item.card.y + item.card.height + 1) {
      fail(`Color swatch label escapes card for ${item.slug || item.text}.`, item);
    }
  }

  const firstRowY = audit.items[0].card.y;
  const firstRow = audit.items.filter((item) => Math.abs(item.card.y - firstRowY) < 4);
  if (firstRow.length !== 2) {
    fail('Expected two product color cards in the first mobile row.', audit);
  }

  const widths = audit.items.slice(0, 5).map((item) => item.card.width);
  const minWidth = Math.min(...widths);
  const maxWidth = Math.max(...widths);
  if (maxWidth - minWidth > 2) {
    fail('Product color cards do not keep consistent mobile widths.', audit);
  }

  console.log(`Product color mobile smoke passed: ${audit.items.length} swatches, screenshot ${screenshotPath}`);
  await browser.close();
})().catch(async (error) => {
  console.error(error.message);
  if (error.details) {
    console.error(JSON.stringify(error.details, null, 2));
  }
  process.exit(1);
});
