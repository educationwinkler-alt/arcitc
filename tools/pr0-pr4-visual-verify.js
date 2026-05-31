const fs = require('fs');
const path = require('path');
const { chromium } = require('playwright-core');

const root = process.cwd();
const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const outDir = path.join(root, 'docs', 'screenshots', 'pr0-pr4-visual-verify-2026-05-30');

function round(value) {
  return Math.round(value * 10) / 10;
}

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

function assertClose(actual, expected, tolerance, label) {
  assert(
    Math.abs(actual - expected) <= tolerance,
    `${label}: expected ${expected} +/- ${tolerance}, got ${round(actual)}`
  );
}

function readFigmaNode(fileName, nodeId) {
  const file = JSON.parse(fs.readFileSync(path.join(root, 'docs', fileName), 'utf8'));
  const node = file.nodes?.[nodeId]?.document;

  assert(node, `Missing Figma node ${nodeId} in ${fileName}`);

  return node;
}

function relativeBox(node, frame) {
  return {
    x: round(node.absoluteBoundingBox.x - frame.absoluteBoundingBox.x),
    y: round(node.absoluteBoundingBox.y - frame.absoluteBoundingBox.y),
    width: round(node.absoluteBoundingBox.width),
    height: round(node.absoluteBoundingBox.height),
  };
}

function findChild(frame, nodeId) {
  const stack = [...(frame.children || [])];

  while (stack.length > 0) {
    const node = stack.shift();

    if (node.id === nodeId) {
      return node;
    }

    stack.push(...(node.children || []));
  }

  throw new Error(`Missing Figma child node ${nodeId} inside ${frame.id}`);
}

function categoryFigmaContract() {
  const frame = readFigmaNode('figma-grafika-nodes.raw.json', '1:262');

  return {
    source: 'Figma grafika frame KATEGORIE 1:262',
    frame: relativeBox(frame, frame),
    productFirstCard: relativeBox(findChild(frame, '1:275'), frame),
    productSecondCard: relativeBox(findChild(frame, '1:310'), frame),
    productThirdCard: relativeBox(findChild(frame, '1:359'), frame),
    configurator: relativeBox(findChild(frame, '1:402'), frame),
    showroom: relativeBox(findChild(frame, '1:437'), frame),
    references: relativeBox(findChild(frame, '1:439'), frame),
  };
}

function staticFigmaContract() {
  return {
    configuratorImage: {
      source: 'Figma grafika node 1:409',
      width: 667,
      height: 312,
      borderRadius: '0px 40px 40px 0px',
    },
    referenceArchive: {
      source: 'Figma grafika frame REFERENCE 1:1127',
      frame: { width: 1920, height: 2665 },
      firstCard: { x: 260, y: 310, width: 438, height: 320 },
      secondCard: { x: 743, y: 310, width: 438, height: 320 },
      thirdCard: { x: 1226, y: 310, width: 438, height: 320 },
      cards: 9,
    },
    jucra: {
      source: 'JUCRA KB 4832 and ArcticSpas.com builder pattern',
      shortcode: '[visao_viewer model_name="MODELNAME"]',
      localFallback: 'WAITING_ON_JUCRA_PLUGIN',
    },
  };
}

async function pageMetrics(page, selectors) {
  return page.evaluate((selectorMap) => {
    const box = (selector) => {
      const element = document.querySelector(selector);

      if (!element) {
        return null;
      }

      const rect = element.getBoundingClientRect();

      return {
        x: Math.round(rect.x * 10) / 10,
        y: Math.round(rect.y * 10) / 10,
        width: Math.round(rect.width * 10) / 10,
        height: Math.round(rect.height * 10) / 10,
        bottom: Math.round((rect.y + rect.height) * 10) / 10,
      };
    };

    const style = (selector, property) => {
      const element = document.querySelector(selector);

      if (!element) {
        return null;
      }

      return getComputedStyle(element).getPropertyValue(property).trim();
    };

    const result = {
      url: window.location.pathname,
      bodyHeight: document.documentElement.scrollHeight,
      productCards: document.querySelectorAll('.f-product-card--category').length,
      referenceCards: document.querySelectorAll('.f-reference-card, .f-listing--reference').length,
      configurators: document.querySelectorAll('.f-section--configurator').length,
      jucraBuilders: document.querySelectorAll('.f-section--jucra-builder').length,
      hasJucraShortcode: document.body.innerHTML.includes('data-jucra-shortcode='),
      hasCustomBuilderPanel: document.body.innerHTML.includes('f-jucra-builder__panel'),
      hasSwimspaConfiguratorCopy: document.body.innerText.includes('Nakonfigurujte si vlastní swimspa'),
      hasWaitingOnJucra: document.body.innerHTML.includes('WAITING_ON_JUCRA_PLUGIN'),
      boxes: {},
      styles: {},
      links: Array.from(document.querySelectorAll('a[href*="konfigurator"]')).map((link) => link.href),
    };

    const recentReferenceImage = document.querySelector('.f-reference-section--recent-carousel .f-listing--reference .f-image');

    if (recentReferenceImage) {
      const overlay = getComputedStyle(recentReferenceImage, '::before');

      result.referenceOverlay = {
        display: overlay.display,
        backgroundImage: overlay.backgroundImage,
        content: overlay.content,
      };
    }

    for (const [key, selector] of Object.entries(selectorMap.boxes || {})) {
      result.boxes[key] = box(selector);
    }

    const productCards = Array.from(document.querySelectorAll('.f-product-card--category'))
      .map((element) => {
        const rect = element.getBoundingClientRect();
        return {
          x: Math.round(rect.x * 10) / 10,
          y: Math.round(rect.y * 10) / 10,
          width: Math.round(rect.width * 10) / 10,
          height: Math.round(rect.height * 10) / 10,
          bottom: Math.round((rect.y + rect.height) * 10) / 10,
        };
      });

    if (productCards.length > 0) {
      result.boxes.lastProductCard = productCards.reduce((max, current) => (
        current.bottom > max.bottom ? current : max
      ), productCards[0]);
    }

    for (const [key, spec] of Object.entries(selectorMap.styles || {})) {
      result.styles[key] = style(spec.selector, spec.property);
    }

    return result;
  }, selectors);
}

async function clipScreenshot(page, selector, fileName) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  assert(count > 0, `Cannot screenshot missing selector ${selector}`);

  await locator.scrollIntoViewIfNeeded();
  await locator.evaluate((element) => {
    element.querySelectorAll('img').forEach((image) => {
      image.loading = 'eager';
      if (image.getAttribute('src')) {
        image.src = image.getAttribute('src');
      }
    });
  });
  await page.waitForFunction((targetSelector) => {
    const element = document.querySelector(targetSelector);

    return !element || Array.from(element.querySelectorAll('img')).every((image) => (
      image.currentSrc && image.complete && image.naturalWidth > 0
    ));
  }, selector, { timeout: 10000 }).catch(() => {});
  await locator.evaluate((element) => Promise.all(
    Array.from(element.querySelectorAll('img')).map((image) => (
      image.decode ? image.decode().catch(() => true) : true
    ))
  ));
  await page.evaluate(() => new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve))));
  await page.waitForTimeout(250);

  const box = await locator.boundingBox();

  assert(box, `Selector ${selector} has no screenshot box`);

  await page.screenshot({
    path: path.join(outDir, fileName),
    clip: {
      x: Math.max(0, Math.floor(box.x) - 12),
      y: Math.max(0, Math.floor(box.y) - 12),
      width: Math.min(1920, Math.ceil(box.width) + 24),
      height: Math.min(1200, Math.ceil(box.height) + 24),
    },
  });
}

function isJucraPath(pathName) {
  return pathName === '/konfigurator/' || pathName.startsWith('/konfigurator/');
}

async function gotoVisualPath(page, pathName) {
  const response = await page.goto(`${baseUrl}${pathName}`, {
    waitUntil: isJucraPath(pathName) ? 'domcontentloaded' : 'networkidle',
    timeout: 60000,
  });

  if (isJucraPath(pathName)) {
    await page.waitForSelector('#visao-viewer-id, [data-jucra-status="WAITING_ON_JUCRA_PLUGIN"]', { timeout: 30000 });
    await page.waitForTimeout(1000);
  }

  return response;
}

function compareVirivky(metrics, figma) {
  assert(metrics.productCards >= 12, `/virivky/ should render category product cards, got ${metrics.productCards}`);
  assert(metrics.configurators === 1, `/virivky/ should render exactly one configurator CTA, got ${metrics.configurators}`);

  assertClose(metrics.boxes.firstProductCard.x, figma.productFirstCard.x, 12, '/virivky/ first product card x');
  assertClose(metrics.boxes.firstProductCard.width, figma.productFirstCard.width, 6, '/virivky/ first product card width');
  assertClose(metrics.boxes.firstProductCard.height, figma.productFirstCard.height, 6, '/virivky/ first product card height');
  assertClose(metrics.boxes.configuratorCta.x, figma.configurator.x, 14, '/virivky/ configurator x');
  assertClose(metrics.boxes.configuratorCta.width, figma.configurator.width, 14, '/virivky/ configurator width');
  assertClose(metrics.boxes.configuratorCta.height, figma.configurator.height, 8, '/virivky/ configurator height');
  assertClose(metrics.boxes.configuratorVisual.width, figma.static?.configuratorImage?.width || 667, 8, '/virivky/ configurator visual width');
  assertClose(metrics.boxes.configuratorVisual.height, figma.static?.configuratorImage?.height || 312, 8, '/virivky/ configurator visual height');

  const cardToConfigGap = metrics.boxes.configuratorSection.y - metrics.boxes.lastProductCard.bottom;

  assert(cardToConfigGap >= 160 && cardToConfigGap <= 260, `/virivky/ last product card to configurator gap should follow Figma rhythm, got ${round(cardToConfigGap)}px`);
  assert(metrics.boxes.configuratorButton.height >= 40, '/virivky/ configurator button must be visible');
  assert(metrics.boxes.configuratorButton.bottom <= metrics.boxes.configuratorCta.bottom - 40, '/virivky/ configurator button must not be pushed under the image layer');
  assert(metrics.styles.configuratorButtonColor === 'rgb(255, 255, 255)', `/virivky/ configurator button text must be white on red CTA, got ${metrics.styles.configuratorButtonColor}`);
  assert(metrics.styles.configuratorButtonBorder === 'rgb(255, 255, 255)', `/virivky/ configurator button border must be white on red CTA, got ${metrics.styles.configuratorButtonBorder}`);
  assert(metrics.referenceOverlay?.display === 'none', `/virivky/ reference carousel image overlay must be disabled globally, got display ${metrics.referenceOverlay?.display}`);
  assert(metrics.referenceOverlay?.backgroundImage === 'none', `/virivky/ reference carousel image overlay must not tint photos, got ${metrics.referenceOverlay?.backgroundImage}`);
  assert(metrics.boxes.contactAvatarImage.width >= metrics.boxes.contactAvatar.width * 1.8, '/virivky/ shared contact avatar must use the Figma crop zoom');
  assert(metrics.boxes.contactAvatarImage.height >= metrics.boxes.contactAvatar.height * 1.8, '/virivky/ shared contact avatar must use the Figma crop zoom');
  assert(metrics.boxes.contactAvatarImage.x < metrics.boxes.contactAvatar.x, '/virivky/ shared contact avatar crop must shift image left like Figma');
  assert(metrics.boxes.contactAvatarImage.y < metrics.boxes.contactAvatar.y, '/virivky/ shared contact avatar crop must shift image upward like Figma');
  assert(metrics.links.some((link) => link.includes('/konfigurator/')), '/virivky/ configurator CTA must link to /konfigurator/');
}

function compareSwimspa(metrics, figma) {
  assert(metrics.productCards >= 6, `/swimspa/ should render category product cards, got ${metrics.productCards}`);
  assert(metrics.configurators === 0, '/swimspa/ must not render the hot-tub configurator without approved scope');
  assert(!metrics.hasSwimspaConfiguratorCopy, '/swimspa/ must not contain swimspa configurator copy');
  assertClose(metrics.boxes.firstProductCard.width, figma.productFirstCard.width, 6, '/swimspa/ first product card width');
  assertClose(metrics.boxes.firstProductCard.height, figma.productFirstCard.height, 6, '/swimspa/ first product card height');
}

function compareReferenceArchive(metrics, figma) {
  assert(metrics.referenceCards === figma.referenceArchive.cards, `/reference/ should render ${figma.referenceArchive.cards} cards, got ${metrics.referenceCards}`);
  assertClose(metrics.boxes.firstReferenceCard.x, figma.referenceArchive.firstCard.x, 16, '/reference/ first card x');
  assertClose(metrics.boxes.firstReferenceCard.width, figma.referenceArchive.firstCard.width, 12, '/reference/ first card width');
  assertClose(metrics.boxes.firstReferenceCard.height, figma.referenceArchive.firstCard.height, 12, '/reference/ first card height');
}

function compareJucra(metrics) {
  assert(metrics.jucraBuilders === 1, `${metrics.url} should render one Jucra builder module`);
  assert(metrics.hasWaitingOnJucra || metrics.hasJucraShortcode, `${metrics.url} should expose either real Jucra shortcode output or WAITING_ON_JUCRA_PLUGIN fallback`);
  assert(metrics.boxes.jucraViewer.width > 1100, `${metrics.url} viewer should be the single wide builder surface`);
  assert(!metrics.hasCustomBuilderPanel, `${metrics.url} must not render the old parallel custom option panel`);
}

async function assertBuilderInteraction(page) {
  await page.locator('#variantListAcrylics .clickable-image').nth(1).click();
  await page.locator('#variantListCabinets .clickable-image').nth(1).click();

  const interaction = await page.evaluate(() => {
    const requestLink = document.querySelector('#pricing-link');
    const url = requestLink ? new URL(requestLink.href) : null;
    const acrylic = document.querySelector('#variantListAcrylics .clickable-image.selected-image');
    const cabinet = document.querySelector('#variantListCabinets .clickable-image.selected-image');

    return {
      href: requestLink ? requestLink.getAttribute('href') : '',
      path: url ? url.pathname : '',
      query: url ? url.search : '',
      acrylicSelected: acrylic ? acrylic.id : '',
      cabinetSelected: cabinet ? cabinet.id : '',
    };
  });

  assert(interaction.path === '/poptavka-konfigurace/', `Builder request must go to /poptavka-konfigurace/, got ${interaction.href}`);
  assert(interaction.query.includes('model_name=Timberwolf'), `Builder request href missing model_name=Timberwolf: ${interaction.href}`);
  assert(interaction.query.includes('option_acrylic='), `Builder request href missing option_acrylic: ${interaction.href}`);
  assert(interaction.query.includes('option_cabinet='), `Builder request href missing option_cabinet: ${interaction.href}`);
  assert(interaction.query.includes('option_jets='), `Builder request href missing option_jets: ${interaction.href}`);
  assert(interaction.acrylicSelected !== '', 'Selected acrylic option should come from the Jucra plugin UI');
  assert(interaction.cabinetSelected !== '', 'Selected cabinet option should come from the Jucra plugin UI');

  return interaction;
}

async function main() {
  fs.mkdirSync(outDir, { recursive: true });

  const figma = {
    category: categoryFigmaContract(),
    static: staticFigmaContract(),
  };

  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, deviceScaleFactor: 1 });
  const records = [];

  try {
    await gotoVisualPath(page, '/virivky/');
    const virivky = await pageMetrics(page, {
      boxes: {
        productsSection: '.f-section--products-grouped',
        firstProductCard: '.f-products-series:first-child .f-product-card--category',
        configuratorSection: '.f-section--configurator',
        configuratorCta: '.f-section--configurator .f-configurator-cta',
        configuratorContent: '.f-section--configurator .f-configurator-cta__content',
        configuratorButton: '.f-section--configurator .f-configurator-cta__content .a-button',
        configuratorVisual: '.f-section--configurator .f-configurator-cta__visual',
        contactAvatar: '.f-contact-cta__avatar',
        contactAvatarImage: '.f-contact-cta__avatar img[src*="contact-lukas-dusek.png"]',
        showroom: '.f-section--showroom .f-showroom-panel',
        references: '.f-reference-section--recent-carousel',
      },
      styles: {
        configuratorRadius: { selector: '.f-section--configurator .f-configurator-cta', property: 'border-radius' },
        configuratorButtonColor: { selector: '.f-section--configurator .f-configurator-cta__content .a-button', property: 'color' },
        configuratorButtonBorder: { selector: '.f-section--configurator .f-configurator-cta__content .a-button', property: 'border-color' },
      },
    });
    compareVirivky(virivky, figma.category);
    await clipScreenshot(page, '.f-section--products-grouped', 'virivky-products-local.png');
    await clipScreenshot(page, '.f-section--configurator', 'virivky-configurator-local.png');
    records.push({ key: 'virivky', metrics: virivky });

    await gotoVisualPath(page, '/swimspa/');
    const swimspa = await pageMetrics(page, {
      boxes: {
        productsSection: '.f-section--products-grouped',
        firstProductCard: '.f-products-series:first-child .f-product-card--category',
        showroom: '.f-section--showroom .f-showroom-panel',
        references: '.f-reference-section--recent-carousel',
      },
    });
    compareSwimspa(swimspa, figma.category);
    await clipScreenshot(page, '.f-section--products-grouped', 'swimspa-products-local.png');
    await clipScreenshot(page, '.f-section--showroom', 'swimspa-showroom-local.png');
    records.push({ key: 'swimspa', metrics: swimspa });

    await gotoVisualPath(page, '/reference/');
    const reference = await pageMetrics(page, {
      boxes: {
        referenceGrid: '.f-reference-grid',
        firstReferenceCard: '.f-reference-card',
      },
    });
    compareReferenceArchive(reference, figma.static);
    await clipScreenshot(page, '.f-section--references-figma', 'reference-archive-local.png');
    records.push({ key: 'reference', metrics: reference });

    await gotoVisualPath(page, '/konfigurator/');
    const jucraIndex = await pageMetrics(page, {
      boxes: {
        jucra: '.f-section--jucra-builder',
        jucraViewer: '.f-jucra-builder__viewer',
      },
    });
    compareJucra(jucraIndex);
    await clipScreenshot(page, '.f-section--jucra-builder', 'jucra-builder-local.png');
    records.push({ key: 'jucra-index', metrics: jucraIndex });

    await gotoVisualPath(page, '/konfigurator/timberwolf/');
    const jucraTimberwolf = await pageMetrics(page, {
      boxes: {
        jucra: '.f-section--jucra-builder',
        jucraViewer: '.f-jucra-builder__viewer',
      },
    });
    compareJucra(jucraTimberwolf);
    const jucraTimberwolfInteraction = await assertBuilderInteraction(page);
    await clipScreenshot(page, '.f-section--jucra-builder', 'jucra-builder-timberwolf-local.png');
    records.push({ key: 'jucra-timberwolf', metrics: jucraTimberwolf, interaction: jucraTimberwolfInteraction });

    await gotoVisualPath(page, '/product/timberwolf/');
    await clipScreenshot(page, '.f-product-detail-configurator', 'product-timberwolf-configurator-local.png');
    await clipScreenshot(page, '.f-reference-section--product-context', 'product-timberwolf-references-local.png');
  } finally {
    await browser.close();
  }

  const result = {
    generatedAt: new Date().toISOString(),
    baseUrl,
    figma,
    records,
    screenshots: fs.readdirSync(outDir).filter((file) => file.endsWith('.png')).sort(),
  };

  fs.writeFileSync(path.join(outDir, 'metrics.json'), `${JSON.stringify(result, null, 2)}\n`);
  fs.writeFileSync(
    path.join(outDir, 'index.html'),
    `<!doctype html><html><head><meta charset="utf-8"><title>PR0-PR4 visual verify</title><style>body{font-family:Arial,sans-serif;margin:24px;background:#eef1f5;color:#23282f}section{margin:0 0 28px;padding:18px;background:#fff;border-radius:16px;box-shadow:0 8px 24px rgba(35,40,47,.08)}img{display:block;max-width:100%;height:auto;border:1px solid #d8dee5;border-radius:12px}.meta{color:#5f6f7b}</style></head><body><h1>PR0-PR4 visual verify</h1><p class="meta">Figma sources: KATEGORIE 1:262, konfigurator 1:409, REFERENCE 1:1127, JUCRA KB 4832 shortcode pattern.</p>${result.screenshots.map((file) => `<section><h2>${file}</h2><img src="${file}" alt=""></section>`).join('')}</body></html>`
  );

  console.log(`PR0-PR4 visual verify passed. Evidence written to ${outDir}`);
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
