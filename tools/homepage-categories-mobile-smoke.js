const { chromium } = require('playwright-core');
const fs = require('fs');
const path = require('path');

const baseUrl = (process.env.ARCTIC_BASE_URL || 'http://localhost:8090').replace(/\/$/, '');
const label = baseUrl.includes('localhost') ? 'local' : 'production';
const outDir = path.join('docs', 'screenshots', 'homepage-categories-mobile-2026-06-08');

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

  await page.goto(`${baseUrl}/`, { waitUntil: 'networkidle' });
  await page.locator('.f-section--product-categories').scrollIntoViewIfNeeded();
  await page.waitForTimeout(500);

  const audit = await page.evaluate(() => {
    const rect = (element) => {
      if (!element) {
        return null;
      }
      const r = element.getBoundingClientRect();
      return {
        x: r.x,
        y: r.y,
        width: r.width,
        height: r.height,
        top: r.top,
        right: r.right,
        bottom: r.bottom,
        left: r.left,
      };
    };

    const cards = Array.from(document.querySelectorAll('.template--homepage .f-category')).map((card) => {
      const image = card.querySelector('.f-category__image');
      const img = card.querySelector('.f-category__image img');
      const title = card.querySelector('h3');

      return {
        className: card.className,
        slug: card.className.includes('virivky') ? 'virivky' : (card.className.includes('swimspa') ? 'swimspa' : ''),
        card: rect(card),
        media: rect(image),
        img: rect(img),
        title: title && {
          text: title.textContent.trim(),
          rect: rect(title),
        },
        objectFit: img ? getComputedStyle(img).objectFit : '',
        objectPosition: img ? getComputedStyle(img).objectPosition : '',
      };
    });

    return {
      url: window.location.href,
      viewportWidth: window.innerWidth,
      scrollWidth: document.documentElement.scrollWidth,
      cards,
      assets: Array.from(document.querySelectorAll('link[href*="homepage-mobile-slider.css"]')).map((asset) => asset.outerHTML),
    };
  });

  await page.screenshot({
    path: path.join(outDir, `${label}-home-categories-mobile.png`),
    fullPage: false,
  });

  fs.writeFileSync(path.join(outDir, `${label}-audit.json`), JSON.stringify(audit, null, 2));

  if (audit.scrollWidth > audit.viewportWidth + 1) {
    fail('Homepage mobile categories create horizontal overflow.', audit);
  }

  if (audit.cards.length !== 2) {
    fail('Expected exactly two homepage category cards on mobile.', audit);
  }

  const [first, second] = audit.cards;
  for (const card of audit.cards) {
    if (!card.card || !card.media || !card.img || !card.title) {
      fail(`Homepage category ${card.slug || card.className} is missing required DOM parts.`, card);
    }

    if (Math.abs(card.card.width - card.media.width) > 1 || Math.abs(card.card.height - card.media.height) > 1) {
      fail(`Homepage category ${card.slug} media does not match the card box.`, card);
    }

    if (Math.abs(card.media.width - card.img.width) > 1 || Math.abs(card.media.height - card.img.height) > 1) {
      fail(`Homepage category ${card.slug} image does not fill the media box.`, card);
    }

    if (card.objectFit !== 'cover') {
      fail(`Homepage category ${card.slug} image must use object-fit: cover.`, card);
    }

    if (card.title.rect.left < card.card.left || card.title.rect.right > card.card.right || card.title.rect.bottom > card.card.bottom) {
      fail(`Homepage category ${card.slug} title escapes the card.`, card);
    }
  }

  if (Math.abs(first.card.x - second.card.x) > 1 || Math.abs(first.card.width - second.card.width) > 1 || Math.abs(first.card.height - second.card.height) > 1) {
    fail('Homepage mobile category cards are not aligned to the same x/width/height.', audit);
  }

  const gap = second.card.top - first.card.bottom;
  if (gap < 16 || gap > 28) {
    fail('Homepage mobile category cards have an unstable vertical gap.', audit);
  }

  if (audit.assets.length < 1) {
    fail('Homepage mobile category stylesheet was not enqueued.', audit);
  }

  console.log(`Homepage mobile categories smoke passed: ${audit.cards.map((card) => card.slug).join(', ')}`);
  await browser.close();
})().catch((error) => {
  console.error(error.message);
  if (error.details) {
    console.error(JSON.stringify(error.details, null, 2));
  }
  process.exit(1);
});
