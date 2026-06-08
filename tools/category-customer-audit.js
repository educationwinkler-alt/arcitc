const fs = require('fs');
const path = require('path');
const { chromium } = require('playwright-core');

const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const outputDir = path.join('docs', 'screenshots', 'category-customer-audit-2026-06-08');

const targets = [
  { id: 'local', baseUrl: process.env.ARCTIC_LOCAL_BASE_URL || 'http://localhost:8090' },
  { id: 'prod', baseUrl: process.env.ARCTIC_PROD_BASE_URL || 'https://illuminatus.cz' },
];

const viewports = [
  { id: 'desktop1440', width: 1440, height: 1000 },
  { id: 'mobile390', width: 390, height: 844, isMobile: true },
];

const categoryPaths = [
  { id: 'virivky', path: '/virivky/' },
  { id: 'swimspa', path: '/swimspa/' },
];

const urlChecks = [
  '/rada/custom/',
  '/rada/classic/',
  '/rada/core/',
  '/rada/swimspa-classic/',
  '/rada/swimspa-custom/',
  '/produkt/virivky-arctic-spas/serie-arctic-spas-custom/',
  '/produkt/bazeny-arctic-classic/',
];

function mkdirp(dir) {
  fs.mkdirSync(dir, { recursive: true });
}

function rectFromElement(element) {
  if (!element) {
    return null;
  }
  const rect = element.getBoundingClientRect();
  return {
    x: Math.round(rect.x * 100) / 100,
    y: Math.round(rect.y * 100) / 100,
    width: Math.round(rect.width * 100) / 100,
    height: Math.round(rect.height * 100) / 100,
    top: Math.round(rect.top * 100) / 100,
    left: Math.round(rect.left * 100) / 100,
    bottom: Math.round(rect.bottom * 100) / 100,
    right: Math.round(rect.right * 100) / 100,
  };
}

async function auditCategory(page, target, viewport, category) {
  const url = new URL(category.path, target.baseUrl).toString();
  const response = await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 45000 });
  await page.waitForLoadState('networkidle', { timeout: 12000 }).catch(() => {});
  await page.waitForTimeout(700);

  const topShot = path.join(outputDir, `${target.id}-${category.id}-${viewport.id}-top.png`);
  await page.screenshot({ path: topShot, fullPage: false });

  const data = await page.evaluate(() => {
    const text = (element) => element ? element.textContent.replace(/\s+/g, ' ').trim() : '';
    const absolutize = (value) => {
      if (!value) {
        return '';
      }
      try {
        return new URL(value, window.location.href).toString();
      } catch (error) {
        return value;
      }
    };
    const rect = (element) => {
      if (!element) {
        return null;
      }
      const box = element.getBoundingClientRect();
      return {
        x: Math.round(box.x * 100) / 100,
        y: Math.round(box.y * 100) / 100,
        width: Math.round(box.width * 100) / 100,
        height: Math.round(box.height * 100) / 100,
        top: Math.round(box.top * 100) / 100,
        left: Math.round(box.left * 100) / 100,
        bottom: Math.round(box.bottom * 100) / 100,
        right: Math.round(box.right * 100) / 100,
      };
    };
    const styles = (element, pseudo = null) => {
      if (!element) {
        return null;
      }
      const computed = window.getComputedStyle(element, pseudo);
      return {
        display: computed.display,
        visibility: computed.visibility,
        opacity: computed.opacity,
        position: computed.position,
        zIndex: computed.zIndex,
        overflow: computed.overflow,
        color: computed.color,
        backgroundColor: computed.backgroundColor,
        backgroundImage: computed.backgroundImage,
        objectFit: computed.objectFit,
        objectPosition: computed.objectPosition,
        transform: computed.transform,
        pointerEvents: computed.pointerEvents,
      };
    };
    const visible = (element) => {
      if (!element) {
        return false;
      }
      const box = element.getBoundingClientRect();
      const computed = window.getComputedStyle(element);
      return box.width > 0 && box.height > 0 && computed.display !== 'none' && computed.visibility !== 'hidden' && computed.opacity !== '0';
    };
    const elementAtCenter = (element) => {
      if (!element) {
        return null;
      }
      const box = element.getBoundingClientRect();
      if (!box.width || !box.height) {
        return null;
      }
      const x = Math.max(0, Math.min(window.innerWidth - 1, box.left + box.width / 2));
      const y = Math.max(0, Math.min(window.innerHeight - 1, box.top + box.height / 2));
      const topElement = document.elementFromPoint(x, y);
      if (!topElement) {
        return null;
      }
      return {
        tag: topElement.tagName,
        id: topElement.id || '',
        className: typeof topElement.className === 'string' ? topElement.className : '',
        text: text(topElement).slice(0, 80),
        isSelfOrChild: element === topElement || element.contains(topElement),
      };
    };

    const hero = document.querySelector('.f-heading--term, .f-heading--background, .f-heading');
    const heroCta = hero ? hero.querySelector('.f-heading__headline .a-button, .f-heading__headline .f-button') : null;
    const heroImg = hero ? hero.querySelector('.f-background img, .f-hero-media__poster, .f-hero-media__video') : null;

    const cards = Array.from(document.querySelectorAll('.tax-product-category .f-listing--product, .f-product-card--category')).slice(0, 12).map((card) => {
      const figure = card.querySelector('.f-listing__image');
      const imageLink = figure ? figure.querySelector('.f-image') : null;
      const image = figure ? figure.querySelector('img') : null;
      const title = card.querySelector('.f-listing__header h2, h2');
      const titleLink = title ? title.querySelector('a') : null;
      const figureBox = rect(figure);
      const cardBox = rect(card);
      return {
        title: text(title),
        href: absolutize(titleLink ? titleLink.getAttribute('href') : ''),
        productMedia: figure ? figure.getAttribute('data-product-media') : '',
        cardRect: cardBox,
        figureRect: figureBox,
        imageLinkRect: rect(imageLink),
        imageRect: rect(image),
        imageSrc: image ? absolutize(image.currentSrc || image.src || '') : '',
        imageNaturalWidth: image ? image.naturalWidth : 0,
        imageNaturalHeight: image ? image.naturalHeight : 0,
        imageStyle: styles(image),
        imageLinkStyle: styles(imageLink),
        figureStyle: styles(figure),
        cardStyle: styles(card),
        frameRatio: figureBox && figureBox.height ? Math.round((figureBox.width / figureBox.height) * 1000) / 1000 : null,
        cardRatio: cardBox && cardBox.height ? Math.round((cardBox.width / cardBox.height) * 1000) / 1000 : null,
      };
    });

    return {
      finalUrl: window.location.href,
      title: document.title,
      bodyClass: document.body.className,
      viewport: {
        width: window.innerWidth,
        height: window.innerHeight,
        scrollY: Math.round(window.scrollY),
      },
      hero: {
        text: text(hero),
        h1: text(hero ? hero.querySelector('h1') : null),
        description: text(hero ? hero.querySelector('.f-heading__description') : null),
        rect: rect(hero),
        style: styles(hero),
        beforeStyle: styles(hero, '::before'),
        media: {
          rect: rect(heroImg),
          src: heroImg ? absolutize(heroImg.currentSrc || heroImg.src || heroImg.getAttribute('poster') || '') : '',
          naturalWidth: heroImg && 'naturalWidth' in heroImg ? heroImg.naturalWidth : 0,
          naturalHeight: heroImg && 'naturalHeight' in heroImg ? heroImg.naturalHeight : 0,
          style: styles(heroImg),
        },
        cta: {
          exists: Boolean(heroCta),
          visible: visible(heroCta),
          text: text(heroCta),
          tag: heroCta ? heroCta.tagName : '',
          href: heroCta && heroCta.tagName === 'A' ? absolutize(heroCta.getAttribute('href')) : '',
          type: heroCta ? heroCta.getAttribute('type') || '' : '',
          dataOff: heroCta ? heroCta.getAttribute('data-off') || '' : '',
          classes: heroCta ? heroCta.className : '',
          rect: rect(heroCta),
          style: styles(heroCta),
          elementAtCenter: elementAtCenter(heroCta),
        },
      },
      seriesNav: Array.from(document.querySelectorAll('.f-series-nav a')).map((link) => ({
        text: text(link),
        href: absolutize(link.getAttribute('href') || ''),
        classes: link.className,
      })),
      seriesSections: Array.from(document.querySelectorAll('.f-products-series')).map((section) => ({
        id: section.id,
        className: section.className,
        title: text(section.querySelector('.f-products-series__title, h2')),
        subtitle: text(section.querySelector('.f-products-series__subtitle')),
        description: text(section.querySelector('.f-products-series__description')),
        productCount: section.querySelectorAll('.f-listing--product, .f-product-card--category').length,
        rect: rect(section),
      })),
      productCards: cards,
    };
  });

  const productsSection = page.locator('.f-section--product-listing-contract, .f-products-series').first();
  const productsVisible = await productsSection.count();
  let productsShot = null;
  if (productsVisible > 0) {
    await productsSection.scrollIntoViewIfNeeded({ timeout: 5000 }).catch(() => {});
    await page.waitForTimeout(250);
    productsShot = path.join(outputDir, `${target.id}-${category.id}-${viewport.id}-products.png`);
    await page.screenshot({ path: productsShot, fullPage: false });
  }

  return {
    status: response ? response.status() : null,
    url,
    screenshots: {
      top: topShot,
      products: productsShot,
    },
    ...data,
  };
}

async function statusCheck(request, baseUrl, checkPath) {
  const url = new URL(checkPath, baseUrl).toString();
  const response = await request.get(url, { maxRedirects: 0, timeout: 20000 }).catch((error) => ({ error }));
  if (response.error) {
    return {
      path: checkPath,
      url,
      error: String(response.error && response.error.message ? response.error.message : response.error),
    };
  }
  return {
    path: checkPath,
    url,
    status: response.status(),
    location: response.headers().location || '',
  };
}

(async () => {
  mkdirp(outputDir);

  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const result = {
    generatedAt: new Date().toISOString(),
    targets: {},
  };

  for (const target of targets) {
    result.targets[target.id] = {
      baseUrl: target.baseUrl,
      urlChecks: [],
      pages: {},
    };

    const requestContext = await browser.newContext({ ignoreHTTPSErrors: true });
    for (const checkPath of urlChecks) {
      result.targets[target.id].urlChecks.push(await statusCheck(requestContext.request, target.baseUrl, checkPath));
    }
    await requestContext.close();

    for (const viewport of viewports) {
      const context = await browser.newContext({
        viewport: { width: viewport.width, height: viewport.height },
        isMobile: Boolean(viewport.isMobile),
        hasTouch: Boolean(viewport.isMobile),
        deviceScaleFactor: viewport.isMobile ? 2 : 1,
        ignoreHTTPSErrors: true,
      });
      const page = await context.newPage();

      for (const category of categoryPaths) {
        const key = `${category.id}-${viewport.id}`;
        result.targets[target.id].pages[key] = await auditCategory(page, target, viewport, category);
      }

      await context.close();
    }
  }

  await browser.close();

  const output = path.join(outputDir, 'audit.json');
  fs.writeFileSync(output, JSON.stringify(result, null, 2));
  console.log(`Wrote ${output}`);
})();
