const { chromium } = require('playwright-core');
const fs = require('fs');
const path = require('path');

const baseUrl = (process.env.ARCTIC_BASE_URL || 'http://localhost:8090').replace(/\/$/, '');
const label = baseUrl.includes('localhost') ? 'local' : 'production';
const outDir = path.join('docs', 'screenshots', 'homepage-mobile-slider-2026-06-08');

function fail(message, details = undefined) {
  const error = new Error(message);
  if (details) {
    error.details = details;
  }
  throw error;
}

function rectToPlain(rect) {
  if (!rect) {
    return null;
  }

  return {
    x: rect.x,
    y: rect.y,
    width: rect.width,
    height: rect.height,
    top: rect.top,
    right: rect.right,
    bottom: rect.bottom,
    left: rect.left,
  };
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
  await page.waitForTimeout(1200);

  const before = await page.evaluate(() => {
    const active = document.querySelector('.f-slide.swiper-slide-active');
    const button = active ? active.querySelector('.f-caption__button') : null;
    const content = active ? active.querySelector('.f-caption .f-content') : null;
    const bg = active ? active.querySelector('.f-slide__background') : null;
    const pagination = document.querySelector('.f-slides__controls--pagination');
    const wrapper = document.querySelector('.f-slides__wrapper');

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

    return {
      url: window.location.href,
      viewportWidth: window.innerWidth,
      scrollWidth: document.documentElement.scrollWidth,
      activeSlideId: active ? active.getAttribute('data-slide-id') : '',
      wrapperTransform: wrapper ? getComputedStyle(wrapper).transform : '',
      slides: Array.from(document.querySelectorAll('.f-slide')).map((slide) => ({
        id: slide.getAttribute('data-slide-id') || '',
        className: slide.className,
        display: getComputedStyle(slide).display,
        rect: rect(slide),
      })),
      button: button && {
        text: button.textContent.trim(),
        href: button.href,
        display: getComputedStyle(button).display,
        rect: rect(button),
      },
      content: rect(content),
      pagination: pagination && {
        display: getComputedStyle(pagination).display,
        rect: rect(pagination),
      },
      bulletCount: document.querySelectorAll('.f-slides__pagination .swiper-pagination-bullet').length,
      overlayBefore: bg ? getComputedStyle(bg, '::before').backgroundImage : '',
      overlayAfter: bg ? getComputedStyle(bg, '::after').backgroundImage : '',
      assets: Array.from(document.querySelectorAll('link[href*="homepage-mobile-slider.css"], script[src*="homepage-mobile-slider.js"]')).map((asset) => asset.outerHTML),
    };
  });

  await page.screenshot({
    path: path.join(outDir, `${label}-home-mobile-slider-first.png`),
    fullPage: false,
  });

  if (before.scrollWidth > before.viewportWidth + 1) {
    fail('Homepage mobile slider creates horizontal overflow.', before);
  }

  if (before.slides.length < 2) {
    fail('Homepage needs at least two rendered slides for mobile slideshow behavior.', before);
  }

  if (before.slides.some((slide) => slide.display === 'none' || slide.rect.width < before.viewportWidth - 4)) {
    fail('One or more homepage mobile slides are hidden or collapsed.', before);
  }

  if (!before.button || !before.button.href || before.button.rect.width < 120 || before.button.rect.height < 40) {
    fail('Homepage mobile active slide CTA is missing or not visible.', before);
  }

  if (before.content && before.button.rect.top < before.content.bottom + 8) {
    fail('Homepage mobile slide CTA overlaps the caption text.', before);
  }

  if (!before.pagination || before.pagination.display === 'none' || before.bulletCount < 2) {
    fail('Homepage mobile slider pagination is not visible/clickable.', before);
  }

  if (before.overlayBefore.includes('0.74') || before.overlayAfter.includes('0.72')) {
    fail('Homepage mobile slider still uses the old dark overlay values.', before);
  }

  if (before.assets.length < 2) {
    fail('Homepage mobile slider CSS/JS assets were not enqueued.', before);
  }

  const bullets = await page.$$('.f-slides__pagination .swiper-pagination-bullet');
  await bullets[1].click();
  await page.waitForTimeout(900);

  const after = await page.evaluate(() => {
    const active = document.querySelector('.f-slide.swiper-slide-active');
    const wrapper = document.querySelector('.f-slides__wrapper');
    return {
      activeSlideId: active ? active.getAttribute('data-slide-id') : '',
      wrapperTransform: wrapper ? getComputedStyle(wrapper).transform : '',
    };
  });

  await page.screenshot({
    path: path.join(outDir, `${label}-home-mobile-slider-second.png`),
    fullPage: false,
  });

  if (!after.activeSlideId || after.activeSlideId === before.activeSlideId) {
    fail('Homepage mobile slider did not move to the second slide after pagination click.', { before, after });
  }

  if (!after.wrapperTransform || after.wrapperTransform === 'none' || after.wrapperTransform === before.wrapperTransform) {
    fail('Homepage mobile slider wrapper transform did not update.', { before, after });
  }

  const audit = { before, after };
  fs.writeFileSync(path.join(outDir, `${label}-audit.json`), JSON.stringify(audit, null, 2));

  console.log(`Homepage mobile slider smoke passed: ${before.slides.length} slides, active ${before.activeSlideId} -> ${after.activeSlideId}`);
  await browser.close();
})().catch((error) => {
  console.error(error.message);
  if (error.details) {
    console.error(JSON.stringify(error.details, null, 2));
  }
  process.exit(1);
});
