const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

function round(value) {
  return Math.round(value * 10) / 10;
}

function desktopScale(width) {
  if (width >= 1400 && width <= 1919) {
    return Math.min(1, (width - 40) / 1400);
  }

  return 1;
}

function heroScale(width) {
  if (width >= 1400 && width <= 1919) {
    return width / 1920;
  }

  return 1;
}

function promoBox(width) {
  const scale = heroScale(width);
  const promoWidth = 268 * scale;
  const originalX = ((width - (1920 * scale)) / 2) + (1699 * scale);
  const safeX = width - promoWidth - 20;

  return {
    x: Math.min(originalX, safeX),
    y: 593 * scale,
    width: promoWidth,
    height: 288 * scale,
    scale,
  };
}

function assertClose(actual, expected, tolerance, label) {
  if (Math.abs(actual - expected) > tolerance) {
    throw new Error(`${label}: expected ${expected} +/- ${tolerance}, got ${round(actual)}`);
  }
}

function assertBetween(actual, min, max, label) {
  if (actual < min || actual > max) {
    throw new Error(`${label}: expected ${min}..${max}, got ${round(actual)}`);
  }
}

async function verticalGap(page, upperSelector, lowerSelector, label) {
  const upper = await box(page, upperSelector, `${label}.upper`);
  const lower = await box(page, lowerSelector, `${label}.lower`);
  return lower.y - (upper.y + upper.height);
}

async function box(page, selector, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    throw new Error(`${label}: missing selector ${selector}`);
  }

  const rect = await locator.boundingBox();

  if (!rect) {
    throw new Error(`${label}: selector ${selector} has no bounding box`);
  }

  return rect;
}

async function assertBox(page, selector, expected, tolerance, label) {
  const rect = await box(page, selector, label);

  assertClose(rect.x, expected.x, tolerance, `${label}.x`);
  assertClose(rect.y, expected.y, tolerance, `${label}.y`);
  assertClose(rect.width, expected.width, tolerance, `${label}.width`);
  assertClose(rect.height, expected.height, tolerance, `${label}.height`);
}

async function offsetBox(page, selector, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    throw new Error(`${label}: missing selector ${selector}`);
  }

  return locator.evaluate((element) => {
    const parent = element.offsetParent || element.parentElement;
    const parentRect = parent ? parent.getBoundingClientRect() : { x: 0, y: 0 };

    return {
      x: parentRect.x + element.offsetLeft,
      y: parentRect.y + element.offsetTop,
      width: element.offsetWidth,
      height: element.offsetHeight,
    };
  });
}

async function assertOffsetBox(page, selector, expected, tolerance, label) {
  const rect = await offsetBox(page, selector, label);

  assertClose(rect.x, expected.x, tolerance, `${label}.x`);
  assertClose(rect.y, expected.y, tolerance, `${label}.y`);
  assertClose(rect.width, expected.width, tolerance, `${label}.width`);
  assertClose(rect.height, expected.height, tolerance, `${label}.height`);
}

async function assertSourceContains(page, selector, expected, label) {
  const source = await page.locator(selector).first().evaluate((element) => (
    [
      element.currentSrc,
      element.src,
      element.getAttribute('data-src'),
      element.getAttribute('data-srcset'),
      getComputedStyle(element).backgroundImage,
    ].filter(Boolean).join(' ')
  ));

  if (!source.includes(expected)) {
    throw new Error(`${label}: expected source to include "${expected}", got "${source}"`);
  }
}

async function assertComputedStyle(page, selector, property, expected, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    throw new Error(`${label}: missing selector ${selector}`);
  }

  const actual = await locator.evaluate((element, propertyName) => getComputedStyle(element).getPropertyValue(propertyName), property);

  if (actual.trim() !== expected) {
    throw new Error(`${label}: expected ${property} to be "${expected}", got "${actual}"`);
  }
}

async function assertComputedStyleIncludes(page, selector, property, expected, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    throw new Error(`${label}: missing selector ${selector}`);
  }

  const actual = await locator.evaluate((element, propertyName) => getComputedStyle(element).getPropertyValue(propertyName), property);

  if (!actual.toLowerCase().includes(expected.toLowerCase())) {
    throw new Error(`${label}: expected ${property} to include "${expected}", got "${actual}"`);
  }
}

async function assertOptionalComputedStyleIncludes(page, selector, property, expected, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    return;
  }

  await assertComputedStyleIncludes(page, selector, property, expected, label);
}

async function assertPseudoStyleIncludes(page, selector, pseudo, property, expected, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    throw new Error(`${label}: missing selector ${selector}`);
  }

  const actual = await locator.evaluate((element, args) => (
    getComputedStyle(element, args.pseudo).getPropertyValue(args.property)
  ), { pseudo, property });

  if (!actual.toLowerCase().includes(expected.toLowerCase())) {
    throw new Error(`${label}: expected ${pseudo} ${property} to include "${expected}", got "${actual}"`);
  }
}

async function assertPseudoStyleNotIncludes(page, selector, pseudo, property, forbidden, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    throw new Error(`${label}: missing selector ${selector}`);
  }

  const actual = await locator.evaluate((element, args) => (
    getComputedStyle(element, args.pseudo).getPropertyValue(args.property)
  ), { pseudo, property });

  if (actual.toLowerCase().includes(forbidden.toLowerCase())) {
    throw new Error(`${label}: expected ${pseudo} ${property} to not include "${forbidden}", got "${actual}"`);
  }
}

async function assertSingleLineText(page, selector, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    throw new Error(`${label}: missing selector ${selector}`);
  }

  const metrics = await locator.evaluate((element) => {
    const range = document.createRange();
    range.selectNodeContents(element);
    const lineRects = range.getClientRects().length;
    const height = element.getBoundingClientRect().height;
    return { lineRects, height };
  });

  if (metrics.lineRects > 1) {
    throw new Error(`${label}: expected single-line text, got ${metrics.lineRects} lines`);
  }

  if (metrics.height > 24) {
    throw new Error(`${label}: expected compact one-line chip, got height ${round(metrics.height)}px`);
  }
}

async function assertNoVerticalOverlap(page, upperSelector, lowerSelector, label, minGap = 0) {
  const upper = await box(page, upperSelector, `${label}.upper`);
  const lower = await box(page, lowerSelector, `${label}.lower`);
  const gap = lower.y - (upper.y + upper.height);

  if (gap < minGap) {
    throw new Error(`${label}: expected at least ${minGap}px vertical gap, got ${round(gap)}px`);
  }
}

async function assertImageMaxUpscale(page, selector, maxScale, label, options = {}) {
  const { limit = 1, skipDataSvg = true } = options;
  const locator = page.locator(selector);
  const count = await locator.count();

  if (!count) {
    throw new Error(`${label}: missing selector ${selector}`);
  }

  const metrics = await locator.evaluateAll((elements, config) => {
    const limit = config.limit > 0 ? config.limit : elements.length;
    return elements.slice(0, limit).map((element) => {
      const rect = element.getBoundingClientRect();
      const source = element.currentSrc || element.src || '';
      return {
        source,
        naturalWidth: Number(element.naturalWidth || 0),
        naturalHeight: Number(element.naturalHeight || 0),
        renderedWidth: Number(rect.width || 0),
        renderedHeight: Number(rect.height || 0),
      };
    });
  }, { limit });

  let checked = 0;

  for (const metric of metrics) {
    if (metric.renderedWidth <= 1 || metric.renderedHeight <= 1) {
      continue;
    }

    if (skipDataSvg && metric.source.startsWith('data:image/svg+xml')) {
      continue;
    }

    if (metric.naturalWidth < 1 || metric.naturalHeight < 1) {
      throw new Error(`${label}: image has invalid natural size (${metric.naturalWidth}x${metric.naturalHeight}) for source ${metric.source}`);
    }

    checked += 1;

    const scaleWidth = metric.renderedWidth / metric.naturalWidth;
    const scaleHeight = metric.renderedHeight / metric.naturalHeight;
    const maxObservedScale = Math.max(scaleWidth, scaleHeight);

    if (maxObservedScale > maxScale) {
      throw new Error(
        `${label}: image exceeds upscale limit ${maxScale}x with ${round(maxObservedScale)}x ` +
        `(render ${round(metric.renderedWidth)}x${round(metric.renderedHeight)} from ${metric.naturalWidth}x${metric.naturalHeight}, source ${metric.source})`,
      );
    }
  }

  if (!checked) {
    throw new Error(`${label}: no visible raster image matched selector ${selector}`);
  }
}

async function assertRootVariable(page, variable, expected, label) {
  const actual = await page.evaluate((name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim(), variable);

  if (actual !== expected) {
    throw new Error(`${label}: expected ${variable} to be "${expected}", got "${actual}"`);
  }
}

async function assertHtmlContains(page, path, expectedItems, forbiddenItems = []) {
  await page.goto(`${baseUrl}${path}`, { waitUntil: 'load' });
  const html = await page.content();

  for (const expected of expectedItems) {
    if (!html.includes(expected)) {
      throw new Error(`${path}: expected HTML to include "${expected}"`);
    }
  }

  for (const forbidden of forbiddenItems) {
    if (html.includes(forbidden)) {
      throw new Error(`${path}: forbidden generated asset source found "${forbidden}"`);
    }
  }
}

async function assertNoHorizontalOverflow(page, label) {
  const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);

  if (overflow > 2) {
    throw new Error(`${label}: horizontal overflow ${overflow}px`);
  }
}

async function assertInteractiveToggleContract(page, selector, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    throw new Error(`${label}: missing interactive toggle ${selector}`);
  }

  const tagName = await locator.evaluate((element) => element.tagName.toLowerCase());
  const role = await locator.getAttribute('role');
  const tabIndex = await locator.getAttribute('tabindex');
  const controls = await locator.getAttribute('aria-controls');
  const initialExpanded = await locator.getAttribute('aria-expanded');

  if (!['button', 'summary'].includes(tagName) && role !== 'button') {
    throw new Error(`${label}: expected real button/summary or role="button", got <${tagName}> role="${role}"`);
  }

  if (!['button', 'summary'].includes(tagName) && tabIndex !== '0') {
    throw new Error(`${label}: custom toggle must be keyboard focusable with tabindex="0"`);
  }

  if (!controls) {
    throw new Error(`${label}: missing aria-controls`);
  }

  if (!['true', 'false'].includes(initialExpanded || '')) {
    throw new Error(`${label}: missing boolean aria-expanded`);
  }

  const panel = page.locator(`[id="${controls}"]`).first();
  if (!await panel.count()) {
    throw new Error(`${label}: aria-controls target #${controls} is missing`);
  }

  await locator.click();
  await page.waitForTimeout(80);

  const expandedAfterClick = await locator.getAttribute('aria-expanded');
  if (expandedAfterClick === initialExpanded) {
    throw new Error(`${label}: click did not toggle aria-expanded`);
  }

  const panelHiddenAfterClick = await panel.evaluate((element) => element.hidden);
  if (panelHiddenAfterClick !== (expandedAfterClick !== 'true')) {
    throw new Error(`${label}: controlled panel hidden state does not match aria-expanded`);
  }

  await locator.press('Enter');
  await page.waitForTimeout(80);

  const expandedAfterKeyboard = await locator.getAttribute('aria-expanded');
  if (expandedAfterKeyboard === expandedAfterClick) {
    throw new Error(`${label}: Enter key did not toggle aria-expanded`);
  }
}

async function assertInteractiveTabs(page, selector, label) {
  const tabs = page.locator(selector);
  const count = await tabs.count();

  if (count < 2) {
    throw new Error(`${label}: expected at least two filter tabs, got ${count}`);
  }

  for (let index = 0; index < count; index += 1) {
    const tab = tabs.nth(index);
    const tagName = await tab.evaluate((element) => element.tagName.toLowerCase());
    const role = await tab.getAttribute('role');
    const selected = await tab.getAttribute('aria-selected');

    if (tagName !== 'button') {
      throw new Error(`${label}.${index}: expected a real button, got <${tagName}>`);
    }

    if (role !== 'tab') {
      throw new Error(`${label}.${index}: expected role="tab"`);
    }

    if (!['true', 'false'].includes(selected || '')) {
      throw new Error(`${label}.${index}: expected boolean aria-selected`);
    }
  }

  const target = tabs.nth(1);
  await target.click();
  await page.waitForTimeout(80);

  const selectedAfterClick = await target.getAttribute('aria-selected');
  if (selectedAfterClick !== 'true') {
    throw new Error(`${label}: clicking a filter tab did not set aria-selected="true"`);
  }
}

async function assertMajorSectionGapLimit(page, path, width, maxGap, label) {
  await page.setViewportSize({ width, height: 1100 });
  await page.goto(`${baseUrl}${path}`, { waitUntil: 'load' });
  await assertNoHorizontalOverflow(page, `${label}.overflow`);

  const gaps = await page.evaluate(() => {
    const elements = Array.from(document.querySelectorAll('main > .f-section, main > .f-main, .f-main > .f-section'));
    const visible = elements
      .map((element) => {
        const rect = element.getBoundingClientRect();
        return {
          label: [element.tagName.toLowerCase(), element.className].filter(Boolean).join('.'),
          y: rect.y + window.scrollY,
          bottom: rect.bottom + window.scrollY,
          height: rect.height,
        };
      })
      .filter((item) => item.height > 2)
      .sort((a, b) => a.y - b.y);

    const result = [];
    for (let index = 1; index < visible.length; index += 1) {
      result.push({
        from: visible[index - 1].label,
        to: visible[index].label,
        gap: visible[index].y - visible[index - 1].bottom,
      });
    }

    return result.filter((gap) => gap.gap > 1);
  });

  const oversized = gaps.filter((gap) => gap.gap > maxGap);
  if (oversized.length) {
    const details = oversized.map((gap) => `${round(gap.gap)}px between ${gap.from} and ${gap.to}`).join('; ');
    throw new Error(`${label}: major vertical section gap exceeds ${maxGap}px (${details})`);
  }
}

async function assertSeriesNavNoOverlap(page, selector, label) {
  const links = await page.locator(`${selector} a`).evaluateAll((elements) => elements.map((element) => {
    const rect = element.getBoundingClientRect();
    const range = document.createRange();
    range.selectNodeContents(element);

    return {
      text: element.textContent.trim(),
      x: rect.x,
      right: rect.right,
      width: rect.width,
      height: rect.height,
      lineRects: range.getClientRects().length,
    };
  }));

  if (links.length < 2) {
    throw new Error(`${label}: expected at least two nav links, got ${links.length}`);
  }

  for (let index = 0; index < links.length; index += 1) {
    if (links[index].width < 20 || links[index].height < 20) {
      throw new Error(`${label}.${index}: link "${links[index].text}" is too small (${round(links[index].width)}x${round(links[index].height)})`);
    }

    if (links[index].lineRects > 1) {
      throw new Error(`${label}.${index}: link "${links[index].text}" wraps to ${links[index].lineRects} lines`);
    }

    if (index > 0 && links[index].x < links[index - 1].right + 8) {
      throw new Error(`${label}: links overlap or touch too closely (${links[index - 1].text} -> ${links[index].text})`);
    }
  }
}

async function assertDownloadRowsAttached(page, label) {
  const metrics = await page.locator('.f-download-card').evaluateAll((cards) => cards.slice(0, 3).map((card) => {
    const row = card.getBoundingClientRect();
    const body = card.querySelector('.f-download-card__body')?.getBoundingClientRect();
    const button = card.querySelector('.f-download-card__button')?.getBoundingClientRect();

    return {
      row: { x: row.x, right: row.right, width: row.width },
      body: body ? { x: body.x, right: body.right, width: body.width } : null,
      button: button ? { x: button.x, right: button.right, width: button.width } : null,
    };
  }));

  if (!metrics.length) {
    throw new Error(`${label}: missing download cards`);
  }

  for (const [index, metric] of metrics.entries()) {
    if (!metric.body || !metric.button) {
      throw new Error(`${label}.${index}: download row is missing body or CTA`);
    }

    if (metric.button.x < metric.body.right + 12) {
      throw new Error(`${label}.${index}: CTA is visually detached/overlapping body (gap ${round(metric.button.x - metric.body.right)}px)`);
    }

    if (metric.button.right > metric.row.right - 16) {
      throw new Error(`${label}.${index}: CTA exceeds row padding (${round(metric.button.right)} > ${round(metric.row.right - 16)})`);
    }

    if (metric.row.width < 700) {
      throw new Error(`${label}.${index}: row width collapsed to ${round(metric.row.width)}px`);
    }
  }
}

async function assertDownloadGroupHeadersReadable(page, label) {
  const metrics = await page.locator('.f-downloads--support-figma [data-download-group]').evaluateAll((groups) => groups.slice(0, 3).map((group) => {
    const groupRect = group.getBoundingClientRect();
    const header = group.querySelector('.f-download-group__header');
    const title = group.querySelector('h3');
    const tag = group.querySelector('.f-download-group__tag');
    const headerRect = header?.getBoundingClientRect();
    const titleRect = title?.getBoundingClientRect();
    const tagRect = tag?.getBoundingClientRect();
    const titleRange = document.createRange();

    if (title) {
      titleRange.selectNodeContents(title);
    }

    return {
      group: { width: groupRect.width },
      header: headerRect ? { width: headerRect.width, height: headerRect.height } : null,
      title: titleRect ? { y: titleRect.y, width: titleRect.width, height: titleRect.height, lineRects: titleRange.getClientRects().length } : null,
      tag: tagRect ? { x: tagRect.x, y: tagRect.y, width: tagRect.width, height: tagRect.height } : null,
    };
  }));

  if (!metrics.length) {
    throw new Error(`${label}: missing download groups`);
  }

  for (const [index, metric] of metrics.entries()) {
    if (!metric.header || !metric.title || !metric.tag) {
      throw new Error(`${label}.${index}: download group header is missing title or tag`);
    }

    if (metric.header.width < metric.group.width - 100) {
      throw new Error(`${label}.${index}: header collapsed to ${round(metric.header.width)}px inside ${round(metric.group.width)}px group`);
    }

    if (metric.title.width < 120) {
      throw new Error(`${label}.${index}: title column collapsed to ${round(metric.title.width)}px`);
    }

    if (metric.title.lineRects > 1 || metric.title.height > 40) {
      throw new Error(`${label}.${index}: title wraps/flows vertically (${metric.title.lineRects} lines, ${round(metric.title.height)}px high)`);
    }

    if (metric.tag.y > metric.title.y + metric.title.height + 8) {
      throw new Error(`${label}.${index}: tag dropped below title`);
    }
  }
}

async function assertHiddenBox(page, selector, label) {
  const locator = page.locator(selector).first();
  const count = await locator.count();

  if (!count) {
    return;
  }

  const rect = await locator.boundingBox();
  if (rect && rect.width > 1 && rect.height > 1) {
    throw new Error(`${label}: expected ${selector} to be hidden, got ${round(rect.width)}x${round(rect.height)}`);
  }
}

async function assertInsideViewport(page, selector, width, label, inset = 0) {
  const rect = await box(page, selector, label);

  if (rect.x < inset || rect.x + rect.width > width - inset) {
    throw new Error(`${label}: expected ${selector} inside viewport ${width}px, got x=${round(rect.x)}, width=${round(rect.width)}`);
  }
}

async function assertHeroBoundaryIsClear(page, label) {
  const boundary = await page.evaluate(() => {
    const read = (selector) => {
      const element = document.querySelector(selector);

      if (!element) {
        return null;
      }

      const rect = element.getBoundingClientRect();

      return {
        x: rect.x,
        y: rect.y,
        width: rect.width,
        height: rect.height,
        bottom: rect.bottom,
      };
    };

    const hero = read('.template--homepage .f-section--slides');
    const slides = read('.template--homepage .f-section--slides .f-slides');
    const background = read('.template--homepage .f-section--slides .f-slide__background');
    const categories = read('.template--homepage .f-section--product-categories');

    if (!hero || !slides || !background || !categories) {
      return null;
    }

    const sampleX = Math.min(window.innerWidth - 4, Math.max(4, window.innerWidth / 2));
    const sampleY = Math.min(window.innerHeight - 4, hero.bottom + 2);
    const elementsAtBoundary = document.elementsFromPoint(sampleX, sampleY).map((element) => ({
      tag: element.tagName,
      className: String(element.className || ''),
      id: element.id || '',
    }));

    return {
      hero,
      slides,
      background,
      categories,
      elementsAtBoundary,
    };
  });

  if (!boundary) {
    throw new Error(`${label}.heroBoundary: missing homepage hero/category elements`);
  }

  if (boundary.slides.bottom > boundary.hero.bottom + 2) {
    throw new Error(`${label}.heroBoundary: inner slider extends past hero section (${round(boundary.slides.bottom)} > ${round(boundary.hero.bottom)})`);
  }

  if (boundary.background.bottom > boundary.hero.bottom + 2) {
    throw new Error(`${label}.heroBoundary: hero background extends past hero section (${round(boundary.background.bottom)} > ${round(boundary.hero.bottom)})`);
  }

  if (boundary.categories.y < boundary.hero.bottom - 1) {
    throw new Error(`${label}.heroBoundary: categories start under hero section (${round(boundary.categories.y)} < ${round(boundary.hero.bottom)})`);
  }

  const topClass = boundary.elementsAtBoundary[0]?.className || '';
  if (/f-slide|f-slides|f-slide__background/.test(topClass)) {
    throw new Error(`${label}.heroBoundary: slider is still painted over category boundary (${topClass})`);
  }
}

async function auditFigmaTokenAndComponentStyles(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(baseUrl, { waitUntil: 'load' });
  await page.evaluate(async () => document.fonts && document.fonts.ready ? document.fonts.ready : true);

  await assertRootVariable(page, '--arctic-color-frost', '#eef1f5', 'figmaTokens.background');
  await assertRootVariable(page, '--arctic-color-red', '#a31f37', 'figmaTokens.red');
  await assertRootVariable(page, '--arctic-color-menu', '#23282f', 'figmaTokens.dark');
  await assertRootVariable(page, '--a--button--border-radius', '50px', 'figmaTokens.buttonRadius');

  await assertComputedStyleIncludes(page, 'body', 'font-family', 'Red Hat Display', 'figmaTokens.bodyFont');
  await assertComputedStyle(page, 'body', 'background-color', 'rgb(238, 241, 245)', 'figmaTokens.bodyBackground');
  await assertComputedStyleIncludes(page, '.template--homepage .f-section--references h2', 'font-family', 'Red Hat Display', 'figmaTokens.headingFont');

  await assertComputedStyle(page, '.f-section--contact .f-contact-cta', 'background-color', 'rgb(163, 31, 55)', 'figmaComponents.contactCtaBackground');
  await assertComputedStyle(page, '.f-section--contact .f-contact-cta', 'border-radius', '40px', 'figmaComponents.contactCtaRadius');
  await assertComputedStyle(page, '.f-section--contact .f-contact-cta__bar .a-button', 'border-radius', '50px', 'figmaComponents.contactButtonRadius');
  await assertComputedStyleIncludes(page, '.f-section--contact .f-contact-cta__bar .a-button', 'font-family', 'Red Hat Display', 'figmaComponents.contactButtonFont');

  await assertComputedStyle(page, '.template--homepage .f-section--references .f-section__actions .a-button', 'border-radius', '50px', 'figmaComponents.referencesButtonRadius');
  await assertComputedStyle(page, '.template--homepage .f-section--references .f-section__actions .a-button', 'border-color', 'rgb(163, 31, 55)', 'figmaComponents.referencesButtonBorder');
  await assertComputedStyle(page, '.template--homepage .f-section--references .f-carousel__control--next', 'background-color', 'rgb(255, 255, 255)', 'figmaComponents.referencesArrowBackground');
  await assertComputedStyle(page, '.template--homepage .f-section--references .f-carousel__control--next', 'color', 'rgb(0, 0, 0)', 'figmaComponents.referencesArrowColor');
  await assertBox(page, '.template--homepage .f-section--references .f-carousel__control--next', { x: 1639, y: 3638, width: 42, height: 42 }, 4, 'figmaComponents.referencesArrowPosition');
  await assertComputedStyle(page, '.template--homepage .f-section--references .f-listing__metas .f-meta:first-child', 'background-color', 'rgb(35, 40, 47)', 'figmaComponents.referencesMetaDarkPill');
  await assertComputedStyle(page, '.template--homepage .f-section--references .f-listing__metas .f-meta:nth-child(2)', 'background-color', 'rgb(255, 255, 255)', 'figmaComponents.referencesMetaLightPill');

  await assertComputedStyle(page, '.template--homepage .f-showroom-panel', 'background-color', 'rgb(35, 40, 47)', 'figmaComponents.showroomBackground');
  await assertComputedStyle(page, '.template--homepage .f-showroom-panel', 'border-radius', '40px', 'figmaComponents.showroomRadius');
  await assertPseudoStyleNotIncludes(page, '.template--homepage .f-showroom-panel__actions span', '::before', 'background-image', 'hp-pin-showroom', 'figmaComponents.showroomPinNoBitmap');
  await assertPseudoStyleIncludes(page, '.template--homepage .f-showroom-panel__actions span', '::after', 'clip-path', 'path(', 'figmaComponents.showroomPinGlyph');

  await assertComputedStyle(page, '.f-footer--arctic.f-footer--arctic .f-footer__quick-contact', 'border-radius', '40px', 'figmaComponents.footerQuickContactRadius');
  await assertComputedStyle(page, '.f-footer--arctic.f-footer--arctic .f-footer__quick-map', 'border-radius', '30px', 'figmaComponents.footerMapRadius');
  await assertComputedStyle(page, '.f-footer--arctic.f-footer--arctic .f-footer__quick-hours', 'white-space', 'nowrap', 'figmaComponents.footerHoursNoWrap');
  await assertSingleLineText(page, '.f-footer--arctic.f-footer--arctic .f-footer__quick-hours', 'figmaComponents.footerHoursSingleLine');
  await assertOptionalComputedStyleIncludes(page, '.template--homepage .f-section--references .f-listing--reference .f-listing__header a', 'color', '255, 255, 255', 'figmaComponents.referencesHeaderLinkColor');
  await assertPseudoStyleIncludes(page, '.template--homepage .f-section--references .f-listing--reference .f-image', '::before', 'background-image', 'none', 'figmaComponents.referencesOverlayDisabled');

  for (const path of ['/', '/virivky/', '/swimspa/', '/product/timberwolf/', '/showroom/', '/reference/', '/kontakt/', '/podpora/', '/o-nas/', '/servis/', '/sluzby/', '/vlastnosti/', '/certifikaty/', '/zaruka/', '/kolik-stoji-udrzba/', '/vlastnosti/izolace-virivky/']) {
    await page.goto(`${baseUrl}${path}`, { waitUntil: 'load' });
    await assertComputedStyleIncludes(page, 'body', 'font-family', 'Red Hat Display', `figmaTypography:${path}.body`);
    await assertComputedStyle(page, 'body', 'background-color', 'rgb(238, 241, 245)', `figmaTypography:${path}.background`);
    await assertOptionalComputedStyleIncludes(page, 'h1', 'font-family', 'Red Hat Display', `figmaTypography:${path}.h1`);
    await assertOptionalComputedStyleIncludes(page, 'main h2, .f-main h2, h2', 'font-family', 'Red Hat Display', `figmaTypography:${path}.h2`);
  }
}

async function assertViewportSpan(page, selector, viewportWidth, label, tolerance = 2) {
  const rect = await box(page, selector, label);
  assertClose(rect.x, 0, tolerance, `${label}.x`);
  assertClose(rect.width, viewportWidth, tolerance, `${label}.width`);
}

async function assertCompactHeader(page, width, path) {
  const label = `responsive:${width}:${path}`;
  await assertNoHorizontalOverflow(page, label);
  await assertBox(page, '.f-header', { x: 0, y: 0, width, height: 62 }, 2, `${label}.header`);
  await assertBox(page, '.f-logo__img', { x: 20, y: 7, width: 85.6, height: 48 }, 2, `${label}.logo`);

  const trigger = await box(page, '.f-navigation__trigger', `${label}.navigationTrigger`);
  assertClose(trigger.width, 45, 2, `${label}.navigationTrigger.width`);
  assertClose(trigger.height, 45, 2, `${label}.navigationTrigger.height`);
  assertClose(trigger.y, 8.5, 2, `${label}.navigationTrigger.y`);
  if (trigger.x < width - 95 || trigger.x + trigger.width > width - 12) {
    throw new Error(`${label}.navigationTrigger.x: expected right-aligned trigger, got ${round(trigger.x)}`);
  }

  await assertHiddenBox(page, '.f-header__search-slot .f-search__trigger', `${label}.searchTrigger`);
  await assertHiddenBox(page, '.f-header__cta-slot .a-button', `${label}.headerButton`);

  const breakpoint = await page.locator('.f-off--navigation').first().getAttribute('data-off-breakpoint');
  if (breakpoint !== '1400') {
    throw new Error(`${label}.navigationBreakpoint: expected 1400, got ${breakpoint}`);
  }
}

async function assertDesktopHeader(page, width, path) {
  const label = `responsive:${width}:${path}`;
  const scale = desktopScale(width);
  const headerWidth = Math.min(1400 * scale, width - 40);
  const headerX = (width - headerWidth) / 2;
  const headerY = 18;
  const headerHeight = 105 * scale;

  await assertNoHorizontalOverflow(page, label);
  await assertBox(page, '.f-header__container', { x: headerX, y: headerY, width: headerWidth, height: headerHeight }, 6, `${label}.headerContainer`);

  const header = await box(page, '.f-header__container', `${label}.headerContainerBox`);
  const nav = await box(page, '.f-header .js-off__container', `${label}.navigationBox`);
  const button = await box(page, '.f-header__button .a-button', `${label}.headerButtonBox`);

  if (nav.y < header.y + 40 || nav.y + nav.height > header.y + header.height + 2) {
    throw new Error(`${label}.navigationOverlap: desktop navigation is not vertically contained in header`);
  }

  if (button.x + button.width > width - 20) {
    throw new Error(`${label}.headerButtonOverflow: CTA reaches outside safe viewport`);
  }
}

async function auditResponsiveShell(page) {
  for (const width of [1903, 1600, 1536, 1456, 1440]) {
    await page.setViewportSize({ width, height: 1000 });
    for (const path of ['/', '/swimspa/', '/virivky/', '/product/timberwolf/', '/kontakt/']) {
      await page.goto(`${baseUrl}${path}`, { waitUntil: 'load' });
      await assertDesktopHeader(page, width, path);
    }
  }

  for (const width of [1366, 1280, 1024, 768, 430, 390]) {
    await page.setViewportSize({ width, height: width <= 430 ? 900 : 1000 });
    for (const path of ['/', '/swimspa/', '/virivky/', '/product/timberwolf/', '/kontakt/']) {
      await page.goto(`${baseUrl}${path}`, { waitUntil: 'load' });
      await assertCompactHeader(page, width, path);
    }
  }
}

async function auditCompactNavigation(page) {
  for (const width of [1366, 1280, 1024, 768, 430, 390]) {
    await page.setViewportSize({ width, height: width <= 430 ? 900 : 1000 });
    await page.goto(baseUrl, { waitUntil: 'load' });
    await page.locator('.f-navigation__trigger').first().click();
    await page.waitForTimeout(250);

    const active = await page.locator('.f-off--navigation.active').count();
    if (!active) {
      throw new Error(`compactNavigation:${width}: off-canvas navigation did not open`);
    }

    const visibleSubmenus = await page.locator('.f-off--navigation .f-navigation-sub, .f-off--navigation .sub-menu').evaluateAll((elements) => elements.filter((element) => {
      const style = getComputedStyle(element);
      const rect = element.getBoundingClientRect();
      return style.display !== 'none' && rect.width > 1 && rect.height > 1;
    }).length);
    if (visibleSubmenus > 0) {
      throw new Error(`compactNavigation:${width}: desktop submenu content is visible in compact navigation`);
    }

    await page.keyboard.press('Escape');
  }
}

async function auditDesktopHeaderStates(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(baseUrl, { waitUntil: 'load' });
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.waitForTimeout(300);

  await page.locator('.f-search__trigger').first().click();
  await page.waitForTimeout(250);

  const activeSearch = await page.locator('.f-off--search.active').count();
  if (!activeSearch) {
    throw new Error('desktopHeaderSearch: Figma search header state did not open');
  }

  await assertBox(page, '.f-off--search .f-search-panel', { x: 260, y: 18, width: 1400, height: 105 }, 2, 'desktopHeaderSearch.panel');
  await assertBox(page, '.f-off--search .f-search-panel__logo .f-logo__img', { x: 289, y: 24, width: 148, height: 83 }, 2, 'desktopHeaderSearch.logo');
  await assertBox(page, '.f-off--search .f-search__field', { x: 730, y: 59, width: 409, height: 44 }, 2, 'desktopHeaderSearch.field');
  await assertBox(page, '.f-off--search .f-search-panel__button .a-button', { x: 1431, y: 56, width: 208, height: 50 }, 2, 'desktopHeaderSearch.button');

  const placeholder = await page.locator('.f-off--search .f-search__input').first().getAttribute('placeholder');
  if (!placeholder || !placeholder.includes('Zadejte hledan')) {
    throw new Error(`desktopHeaderSearch.placeholder: unexpected placeholder "${placeholder || ''}"`);
  }

  await page.locator('.f-off--search .js-off__close').first().click();
  await page.waitForTimeout(250);
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.waitForTimeout(300);

  const hotTubTrigger = await box(page, '.f-navigation__list > .arctic-menu-products:nth-child(1) > a', 'desktopHeaderMega.hotTubsTrigger');
  await page.mouse.move(hotTubTrigger.x + (hotTubTrigger.width / 2), hotTubTrigger.y + (hotTubTrigger.height / 2));
  await page.waitForTimeout(250);
  await assertBox(page, '.f-mega-menu--hot-tubs', { x: 285, y: 38, width: 1350, height: 500 }, 3, 'desktopHeaderMega.hotTubs');

  const hotTubHeadings = await page.locator('.f-mega-menu--hot-tubs h2').evaluateAll((headings) => headings.map((heading) => heading.textContent.trim()));
  if (hotTubHeadings.length !== 3 || hotTubHeadings.some((heading) => heading.length < 2)) {
    throw new Error(`desktopHeaderMega.hotTubs: expected 3 non-empty headings, got [${hotTubHeadings.join(', ')}]`);
  }

  const hotTubProducts = await page.locator('.f-mega-menu--hot-tubs .f-mega-menu__product').count();
  if (hotTubProducts < 3) {
    throw new Error(`desktopHeaderMega.hotTubs: expected at least 3 product links, got ${hotTubProducts}`);
  }
  await assertMegaMenuProductsWrappedNoScroll(page, '.f-mega-menu--hot-tubs', 'desktopHeaderMega.hotTubsWrap');

  const swimspaTrigger = await box(page, '.f-navigation__list > .arctic-menu-products:nth-child(2) > a', 'desktopHeaderMega.swimspaTrigger');
  await page.mouse.move(swimspaTrigger.x + (swimspaTrigger.width / 2), swimspaTrigger.y + (swimspaTrigger.height / 2));
  await page.waitForTimeout(250);
  await assertBox(page, '.f-mega-menu--swimspa', { x: 285, y: 38, width: 1350, height: 500 }, 3, 'desktopHeaderMega.swimspa');

  const swimspaProducts = await page.locator('.f-mega-menu--swimspa .f-mega-menu__product').count();
  if (swimspaProducts < 3) {
    throw new Error(`desktopHeaderMega.swimspa: expected at least 3 product links, got ${swimspaProducts}`);
  }
  await assertMegaMenuProductsWrappedNoScroll(page, '.f-mega-menu--swimspa', 'desktopHeaderMega.swimspaWrap');
}

async function auditDesktopHeaderRealViewport(page) {
  await page.setViewportSize({ width: 1586, height: 756 });
  await page.goto(baseUrl, { waitUntil: 'load' });
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.waitForTimeout(300);

  const hotTubTrigger = await box(page, '.f-navigation__list > .arctic-menu-products:nth-child(1) > a', 'desktopHeaderRealViewport.hotTubsTrigger');
  await page.mouse.move(hotTubTrigger.x + (hotTubTrigger.width / 2), hotTubTrigger.y + (hotTubTrigger.height / 2));
  await page.waitForTimeout(250);

  await assertBox(page, '.f-mega-menu--hot-tubs', { x: 118, y: 38, width: 1350, height: 500 }, 4, 'desktopHeaderRealViewport.hotTubs');
  const realViewportGrid = await box(page, '.f-mega-menu--hot-tubs .f-mega-menu__grid', 'desktopHeaderRealViewport.grid');
  assertClose(realViewportGrid.x, 178, 6, 'desktopHeaderRealViewport.grid.x');
  assertClose(realViewportGrid.y, 156, 6, 'desktopHeaderRealViewport.grid.y');
  assertClose(realViewportGrid.width, 1230, 6, 'desktopHeaderRealViewport.grid.width');
  if (realViewportGrid.height < 340 || realViewportGrid.height > 520) {
    throw new Error(`desktopHeaderRealViewport.grid.height: expected dynamic range 340..520, got ${round(realViewportGrid.height)}`);
  }

  const columnProductCounts = await page.locator('.f-mega-menu--hot-tubs .f-mega-menu__column .f-mega-menu__products').evaluateAll(
    (columns) => columns.map((column) => column.querySelectorAll('.f-mega-menu__product').length)
  );
  if (columnProductCounts.length !== 3) {
    throw new Error(`desktopHeaderRealViewport.columns: expected 3 product columns, got ${columnProductCounts.length}`);
  }
  if (columnProductCounts.some((count) => count < 1)) {
    throw new Error(`desktopHeaderRealViewport.columns: empty mega menu column detected (${columnProductCounts.join(', ')})`);
  }

  await assertComputedStyle(page, '.f-mega-menu--hot-tubs', 'background-color', 'rgb(35, 40, 47)', 'desktopHeaderRealViewport.panelBackground');
  await assertComputedStyle(page, '.f-mega-menu--hot-tubs .f-mega-menu__promo span', 'background-color', 'rgb(248, 137, 68)', 'desktopHeaderRealViewport.promoButtonBackground');
  await assertComputedStyle(page, '.template--homepage .f-section--slides > .f-hero-promo', 'visibility', 'hidden', 'desktopHeaderRealViewport.homePromoHidden');
  await assertComputedStyle(page, '.template--homepage .f-section--slides > .f-hero-promo', 'opacity', '0', 'desktopHeaderRealViewport.homePromoTransparent');
  await assertNoHorizontalOverflow(page, 'desktopHeaderRealViewport');
  await assertMegaMenuProductsWrappedNoScroll(page, '.f-mega-menu--hot-tubs', 'desktopHeaderRealViewport.hotTubsWrap');

  await assertMegaHoverCursorTravel(page, 'desktopHeaderRealViewport');
}
async function auditZoomOutFullBleed(page) {
  for (const width of [2240, 2560]) {
    await page.setViewportSize({ width, height: 1200 });
    await page.goto(baseUrl, { waitUntil: 'load' });

    await assertNoHorizontalOverflow(page, `zoomOut:${width}`);
    await assertViewportSpan(page, '.template--homepage .f-section--slides', width, `zoomOut:${width}.heroSection`);
    await assertViewportSpan(page, '.template--homepage .f-section--product-categories', width, `zoomOut:${width}.categoriesSection`);
    await assertViewportSpan(page, '.template--homepage .f-section--references', width, `zoomOut:${width}.referencesSection`);
    await assertViewportSpan(page, '.template--homepage .f-section--contact', width, `zoomOut:${width}.contactSection`);
    await assertViewportSpan(page, '.f-footer--arctic', width, `zoomOut:${width}.footer`);

    const footerContainer = await box(page, '.f-footer--arctic .f-footer__container', `zoomOut:${width}.footerContainer`);
    assertClose(footerContainer.width, 1400, 2, `zoomOut:${width}.footerContainer.width`);
    assertClose(footerContainer.x, (width - 1400) / 2, 4, `zoomOut:${width}.footerContainer.x`);

    const footerBackgroundSize = await page.locator('.f-footer--arctic').first().evaluate(
      (element) => getComputedStyle(element).getPropertyValue('background-size').trim()
    );
    if (footerBackgroundSize.includes('1920px')) {
      throw new Error(`zoomOut:${width}.footerBackground: fixed 1920px background size causes side gutters (${footerBackgroundSize})`);
    }
  }
}

async function auditCompactLaptopLayout(page) {
  for (const { width, height } of [
    { width: 1024, height: 617 },
    { width: 1097, height: 617 },
    { width: 1279, height: 720 },
  ]) {
    const label = `compactLaptop:${width}x${height}`;

    await page.setViewportSize({ width, height });
    await page.goto(baseUrl, { waitUntil: 'load' });

    await assertNoHorizontalOverflow(page, label);
    await assertViewportSpan(page, '.template--homepage .f-section--slides', width, `${label}.heroSection`);
    await assertViewportSpan(page, '.template--homepage .f-section--product-categories', width, `${label}.categoriesSection`);

    const layout = await page.evaluate(() => {
      const root = getComputedStyle(document.documentElement);
      const box = (selector) => {
        const element = document.querySelector(selector);
        if (!element) {
          return null;
        }

        const rect = element.getBoundingClientRect();
        const style = getComputedStyle(element);

        return {
          x: rect.x,
          y: rect.y,
          width: rect.width,
          height: rect.height,
          bottom: rect.bottom,
          display: style.display,
          gridTemplateColumns: style.gridTemplateColumns,
        };
      };

      return {
        compactScale: root.getPropertyValue('--arctic-compact-scale').trim(),
        heroVariable: root.getPropertyValue('--arctic-hero-height').trim(),
        containerVariable: root.getPropertyValue('--arctic-container-width').trim(),
        slides: box('.template--homepage .f-section--slides'),
        caption: box('.template--homepage .f-caption'),
        promo: box('.template--homepage .f-hero-promo'),
        categories: box('.template--homepage .f-section--product-categories'),
        grid: box('.template--homepage .f-categories--product'),
        firstCard: box('.template--homepage .f-category:nth-child(1)'),
        secondCard: box('.template--homepage .f-category:nth-child(2)'),
      };
    });

    if (!layout.slides || !layout.caption || !layout.promo || !layout.categories || !layout.grid || !layout.firstCard || !layout.secondCard) {
      throw new Error(`${label}.missingElements`);
    }

    if (!layout.compactScale || layout.heroVariable === '49.6875rem') {
      throw new Error(`${label}.variables: compact laptop variables are not active`);
    }

    if (layout.slides.height < 490 || layout.slides.height > 562) {
      throw new Error(`${label}.heroHeight: expected bounded compact hero, got ${round(layout.slides.height)}px`);
    }

    if (layout.caption.bottom > layout.slides.bottom - 24) {
      throw new Error(`${label}.caption: caption reaches too close to hero boundary (${round(layout.caption.bottom)} / ${round(layout.slides.bottom)})`);
    }

    if (layout.promo.display !== 'none') {
      throw new Error(`${label}.promo: promo card must be hidden in compact laptop layout, got display=${layout.promo.display}`);
    }

    if (layout.categories.y < layout.slides.bottom - 1) {
      throw new Error(`${label}.overlap: category section starts before hero ends (${round(layout.categories.y)} < ${round(layout.slides.bottom)})`);
    }

    const cardGap = layout.firstCard.y - layout.slides.bottom;
    if (cardGap < 24 || cardGap > 80) {
      throw new Error(`${label}.categoryBoundary: expected intentional hero/category gap, got ${round(cardGap)}px`);
    }

    const columns = layout.grid.gridTemplateColumns.split(' ').filter(Boolean);
    if (columns.length !== 2) {
      throw new Error(`${label}.categoryGrid: expected 2 compact columns, got "${layout.grid.gridTemplateColumns}"`);
    }

    for (const [name, card] of [['firstCard', layout.firstCard], ['secondCard', layout.secondCard]]) {
      if (card.width < 430 || card.width > 520) {
        throw new Error(`${label}.${name}.width: expected compact card width, got ${round(card.width)}px`);
      }

      if (card.height < 278 || card.height > 330) {
        throw new Error(`${label}.${name}.height: expected compact card height, got ${round(card.height)}px`);
      }
    }

    if (layout.firstCard.y > height - 40) {
      throw new Error(`${label}.firstViewport: category cards are not visible enough in the first viewport`);
    }
  }
}

async function auditScaledLaptopBoundary(page) {
  await page.setViewportSize({ width: 1097, height: 617 });
  await page.goto(baseUrl, { waitUntil: 'load' });

  await assertNoHorizontalOverflow(page, 'scaledLaptopBoundary:1097');
  await assertViewportSpan(page, '.template--homepage .f-section--slides', 1097, 'scaledLaptopBoundary:1097.heroSection');
  await assertViewportSpan(page, '.template--homepage .f-section--product-categories', 1097, 'scaledLaptopBoundary:1097.categoriesSection');

  const boundary = await page.evaluate(() => {
    const slides = document.querySelector('.template--homepage .f-section--slides');
    const categories = document.querySelector('.template--homepage .f-section--product-categories');
    const firstCard = document.querySelector('.template--homepage .f-category:nth-child(1)');

    if (!slides || !categories || !firstCard) {
      return null;
    }

    const slidesRect = slides.getBoundingClientRect();
    const categoriesRect = categories.getBoundingClientRect();
    const firstCardRect = firstCard.getBoundingClientRect();

    return {
      slidesBottom: slidesRect.bottom,
      categoriesTop: categoriesRect.top,
      firstCardTop: firstCardRect.top,
    };
  });

  if (!boundary) {
    throw new Error('scaledLaptopBoundary:1097.missingElements');
  }

  if (boundary.categoriesTop < boundary.slidesBottom - 1) {
    throw new Error(`scaledLaptopBoundary:1097.overlap: category section starts before hero ends (${round(boundary.categoriesTop)} < ${round(boundary.slidesBottom)})`);
  }

  const visualGap = boundary.firstCardTop - boundary.slidesBottom;
  if (visualGap < 20) {
    throw new Error(`scaledLaptopBoundary:1097.glued: first category card gap is only ${round(visualGap)}px`);
  }
}

async function assertHomepageCategoryCardsClickable(page, label) {
  const cards = await page.locator('.template--homepage .f-categories--product > .f-category').evaluateAll((elements) => elements.map((card) => {
    const link = card.querySelector(':scope > a.f-category__container');
    const number = (value) => {
      const parsed = Number.parseFloat(value || '0');
      return Number.isFinite(parsed) ? parsed : 0;
    };

    if (!link) {
      return {
        title: card.querySelector('h3')?.textContent.trim() || '',
        tagName: '',
        href: '',
        nestedLinks: -1,
        textDecorationLine: '',
        arrow: null,
        circle: null,
      };
    }

    const style = getComputedStyle(link);
    const arrow = getComputedStyle(link, '::before');
    const circle = getComputedStyle(link, '::after');

    return {
      title: card.querySelector('h3')?.textContent.trim() || '',
      tagName: link.tagName,
      href: link.getAttribute('href') || '',
      nestedLinks: link.querySelectorAll('a').length,
      textDecorationLine: style.textDecorationLine,
      arrow: {
        x: number(arrow.right) + (number(arrow.width) / 2),
        y: number(arrow.bottom) + (number(arrow.height) / 2),
      },
      circle: {
        x: number(circle.right) + (number(circle.width) / 2),
        y: number(circle.bottom) + (number(circle.height) / 2),
        width: number(circle.width),
        height: number(circle.height),
      },
    };
  }));

  if (cards.length !== 2) {
    throw new Error(`${label}: expected 2 homepage category cards, got ${cards.length}`);
  }

  for (const [index, card] of cards.entries()) {
    if (card.tagName !== 'A') {
      throw new Error(`${label}.${index}: expected whole category container to be the link for "${card.title}"`);
    }

    if (!card.href || card.href === '#') {
      throw new Error(`${label}.${index}: missing real category href for "${card.title}"`);
    }

    if (card.nestedLinks !== 0) {
      throw new Error(`${label}.${index}: category link for "${card.title}" must not contain nested links`);
    }

    if (card.textDecorationLine !== 'none') {
      throw new Error(`${label}.${index}: category link for "${card.title}" must not be underlined`);
    }

    if (!card.circle || card.circle.width < 30 || card.circle.height < 30) {
      throw new Error(`${label}.${index}: missing circular arrow affordance for "${card.title}"`);
    }

    if (Math.abs(card.arrow.x - card.circle.x) > 2 || Math.abs(card.arrow.y - card.circle.y) > 2) {
      throw new Error(`${label}.${index}: arrow is not centered in the red circle for "${card.title}"`);
    }
  }
}

async function assertMegaMenuProductsWrappedNoScroll(page, menuSelector, label) {
  const columns = await page.locator(`${menuSelector} .f-mega-menu__column`).evaluateAll((elements) => elements.map((column) => {
    const menuRect = column.closest('.f-mega-menu').getBoundingClientRect();
    const products = [...column.querySelectorAll('.f-mega-menu__product')];
    const overflowY = getComputedStyle(column).overflowY;
    const productRects = products.map((product) => {
      const rect = product.getBoundingClientRect();
      return {
        text: product.textContent.trim(),
        x: rect.x,
        top: rect.top,
        bottom: rect.bottom,
      };
    });
    const productColumns = [...new Set(productRects.map((rect) => Math.round(rect.x / 20) * 20))].length;

    const result = {
      heading: column.querySelector('h2')?.textContent.trim() || '',
      productCount: products.length,
      overflowY,
      productColumns,
      products: productRects,
      menu: { top: menuRect.top, bottom: menuRect.bottom },
    };

    return result;
  }));

  if (!columns.length) {
    throw new Error(`${label}: missing mega menu columns`);
  }

  for (const [index, column] of columns.entries()) {
    if (column.productCount < 1) {
      throw new Error(`${label}.${index}: column "${column.heading}" has no products`);
    }

    if (['auto', 'scroll', 'overlay'].includes(column.overflowY)) {
      throw new Error(`${label}.${index}: column "${column.heading}" must wrap products, not scroll (overflow-y: ${column.overflowY})`);
    }

    if (column.productCount > 5 && column.productColumns < 2) {
      throw new Error(`${label}.${index}: long column "${column.heading}" did not wrap into another product column`);
    }

    for (const product of column.products) {
      if (product.bottom > column.menu.bottom + 1 || product.top < column.menu.top - 1) {
        throw new Error(`${label}.${index}: product "${product.text}" is clipped outside the mega menu`);
      }
    }
  }
}

async function assertMegaHoverCursorTravel(page, labelPrefix) {
  const triggerSelector = '.f-navigation__list > .arctic-menu-products:nth-child(1) > a';
  const menuSelector = '.f-mega-menu--hot-tubs';
  const targetSelector = '.f-mega-menu--hot-tubs .f-mega-menu__column:nth-child(1) .f-mega-menu__product:nth-child(1)';

  const trigger = await box(page, triggerSelector, `${labelPrefix}.hoverTravel.trigger`);
  await page.mouse.move(trigger.x + (trigger.width / 2), trigger.y + (trigger.height / 2));
  await page.waitForTimeout(120);
  await assertComputedStyle(page, menuSelector, 'visibility', 'visible', `${labelPrefix}.hoverTravel.visibleOnTrigger`);

  const target = await box(page, targetSelector, `${labelPrefix}.hoverTravel.target`);
  const bridgeX = trigger.x + (trigger.width / 2);
  const bridgeY = Math.max(trigger.y + trigger.height + 8, Math.min(trigger.y + 96, target.y - 24));

  await page.mouse.move(bridgeX, bridgeY);
  await page.waitForTimeout(180);
  await assertComputedStyle(page, menuSelector, 'visibility', 'visible', `${labelPrefix}.hoverTravel.visibleOnBridge`);

  await page.mouse.move(target.x + Math.min(24, target.width / 2), target.y + (target.height / 2));
  await page.waitForTimeout(180);
  await assertComputedStyle(page, menuSelector, 'visibility', 'visible', `${labelPrefix}.hoverTravel.visibleOnTarget`);
}

async function assertFooterLayout(page, label, expectedY = null) {
  const footer = await box(page, '.f-footer--arctic', `${label}.footer`);
  assertClose(footer.x, 0, 2, `${label}.footer.x`);
  if (expectedY !== null) {
    assertClose(footer.y, expectedY, 4, `${label}.footer.y`);
  }
  assertClose(footer.width, 1920, 2, `${label}.footer.width`);
  assertClose(footer.height, 773, 4, `${label}.footer.height`);

  const checks = [
    ['.f-footer--arctic .f-footer__container', { x: 260, y: 0, width: 1400, height: 773 }, 'container'],
    ['.f-footer__group:nth-child(1)', { x: 260, y: 86, width: 163, height: 324 }, 'columnsHotTubs'],
    ['.f-footer__group:nth-child(2)', { x: 541, y: 86, width: 176, height: 250 }, 'columnsFeatures'],
    ['.f-footer__group:nth-child(3)', { x: 822, y: 86, width: 188, height: 340 }, 'columnsInfo'],
    ['.f-footer__quick-contact', { x: 1070, y: 60, width: 592, height: 347 }, 'quickContact'],
    ['.f-footer__quick-contact h2', { x: 1104, y: 86, width: 142, height: 26 }, 'quickTitle'],
    ['.f-footer__quick-avatar', { x: 1101, y: 144, width: 58, height: 58 }, 'quickAvatar'],
    ['.f-footer__quick-contact-body > a[href^="mailto"]', { x: 1101, y: 226, width: 233, height: 24 }, 'quickEmail'],
    ['.f-footer__quick-contact-body > a[href^="tel"]', { x: 1101, y: 253, width: 233, height: 24 }, 'quickPhone'],
    ['.f-footer__quick-hours', { x: 1101, y: 284, width: 147, height: 21 }, 'quickHours'],
    ['.f-footer__quick-contact-body .a-button', { x: 1101, y: 333, width: 208, height: 50 }, 'quickButton'],
    ['.f-footer__quick-map', { x: 1375, y: 84, width: 262, height: 299 }, 'quickMap'],
    ['.f-footer__quick-map a', { x: 1409, y: 309, width: 200, height: 50 }, 'quickMapButton'],
    ['.f-footer__bottom', { x: 260, y: 514, width: 1400, height: 57 }, 'bottom'],
    ['.f-footer__copyright', { x: 262, y: 521, width: 520, height: 34 }, 'copyright'],
    ['.f-footer__bottom .f-logo', { x: 909, y: 514, width: 102, height: 57 }, 'bottomLogo'],
    ['.f-footer__bottom > a', { x: 1343, y: 521, width: 220, height: 34 }, 'privacy'],
  ];

  for (const [selector, expected, name] of checks) {
    const rect = await box(page, selector, `${label}.${name}`);
    assertClose(rect.x, expected.x, 4, `${label}.${name}.x`);
    assertClose(rect.y - footer.y, expected.y, 4, `${label}.${name}.y`);
    assertClose(rect.width, expected.width, name === 'quickHours' ? 20 : 4, `${label}.${name}.width`);
    assertClose(rect.height, expected.height, 4, `${label}.${name}.height`);
  }

  await assertSingleLineText(page, '.f-footer__quick-map-address span', `${label}.quickMapStreetSingleLine`);
  await assertSingleLineText(page, '.f-footer__quick-map-address strong', `${label}.quickMapCitySingleLine`);
  await assertNoVerticalOverlap(page, '.f-footer__quick-map-address span', '.f-footer__quick-map-address strong', `${label}.quickMapStreetToCity`);
  await assertNoVerticalOverlap(page, '.f-footer__quick-map-address', '.f-footer__quick-map a', `${label}.quickMapAddressToButton`, 12);

  const footerText = await page.locator('.f-footer--arctic').innerText();
  for (const expected of [
    'Vířivky',
    'Série Core',
    'Vlastnosti vířivek',
    'Další informace',
    'Rychlý kontakt',
    'lukas.dusek@arctic-spas.cz',
    '+420 777 099 687',
    'Po - Pá 8:00-17:00 h',
    'Ochrana osobních údajů',
  ]) {
    if (!footerText.includes(expected)) {
      throw new Error(`${label}.footerText: missing "${expected}"`);
    }
  }

  if (!footerText.includes('BASPA s.r.o.')) {
    throw new Error(`${label}.footerText: footer copyright must name BASPA s.r.o.`);
  }

  const flattenedFooterImages = await page.locator('.f-footer--arctic img[src*="footer-desktop"], .f-footer--arctic img[src*="footer-mobile"]').count();
  if (flattenedFooterImages > 0) {
    throw new Error(`${label}.footer: footer must not be rendered as a flattened PNG`);
  }

  const avatarImages = await page.locator('.f-footer__quick-avatar img').count();
  if (avatarImages !== 1) {
    throw new Error(`${label}.footerAvatar: expected Lukáš Dušek photo in footer avatar, got ${avatarImages}`);
  }
  await assertSourceContains(page, '.f-footer__quick-avatar img', 'uploads/import/figma/contact-lukas-dusek.png', `${label}.footerAvatarSource`);

  const eboostSignature = await page.locator('.f-footer--arctic .f-signature--eboost').count();
  if (eboostSignature > 0) {
    throw new Error(`${label}.footerCredit: eboost signature must not be rendered`);
  }
}

async function auditDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(baseUrl, { waitUntil: 'load' });

  const promo = promoBox(1920);

  await assertBox(page, '.f-header__container', { x: 260, y: 18, width: 1400, height: 105 }, 2, 'desktop.headerContainer');
  await assertBox(page, '.f-logo__img', { x: 289, y: 24, width: 148, height: 83 }, 2, 'desktop.logo');
  await assertBox(page, '.f-header .js-off__container', { x: 615, y: 69, width: 639, height: 24 }, 3, 'desktop.navigation');
  await assertBox(page, '.f-header .f-search__trigger', { x: 1229.5, y: 68.5, width: 24, height: 24 }, 2, 'desktop.search');
  await assertBox(page, '.f-header__button .a-button', { x: 1431, y: 56, width: 208, height: 50 }, 2, 'desktop.headerButton');
  await assertBox(page, '.f-section--slides', { x: 0, y: 0, width: 1920, height: 795 }, 2, 'desktop.heroSection');
  await assertHeroBoundaryIsClear(page, 'desktop');
  await assertBox(page, '.f-caption', { x: 266, y: 280, width: 488, height: 309 }, 3, 'desktop.heroCaption');
  await assertBox(page, '.f-slides__control--prev', { x: 125, y: 382, width: 42, height: 42 }, 2, 'desktop.heroPrevArrow');
  await assertBox(page, '.f-slides__control--next', { x: 1767, y: 382, width: 42, height: 42 }, 2, 'desktop.heroNextArrow');
  await assertBox(page, '.f-hero-promo', { x: promo.x, y: promo.y, width: promo.width, height: promo.height }, 3, 'desktop.heroPromo');
  await assertBox(page, '.f-hero-promo__image', { x: promo.x + (32 * promo.scale), y: promo.y, width: 174, height: 131 }, 3, 'desktop.heroPromoImage');
  await assertInsideViewport(page, '.f-hero-promo', 1920, 'desktop.heroPromoInside', 20);
  await assertBox(page, '.f-category:nth-child(1)', { x: 258, y: 866, width: 674, height: 424 }, 3, 'desktop.categoryHotTubs');
  await assertBox(page, '.f-category:nth-child(2)', { x: 986, y: 866, width: 674, height: 424 }, 3, 'desktop.categorySwimspa');
  await assertHomepageCategoryCardsClickable(page, 'desktop.categoryCards');
  await assertBox(page, '.template--homepage .f-page__content', { x: 584, y: 1405, width: 752, height: 232 }, 3, 'desktop.exclusiveDealer');
  await assertBox(page, '.template--homepage .f-arctic-benefits', { x: 260, y: 1703, width: 1400, height: 175 }, 3, 'desktop.benefits');
  await assertBox(page, '.template--homepage .f-arctic-benefit:nth-child(1)', { x: 260, y: 1703, width: 416, height: 175 }, 3, 'desktop.benefitInstallation');
  await assertBox(page, '.template--homepage .f-arctic-benefit:nth-child(2)', { x: 752, y: 1703, width: 416, height: 175 }, 3, 'desktop.benefitSupport');
  await assertBox(page, '.template--homepage .f-arctic-benefit:nth-child(3)', { x: 1244, y: 1703, width: 416, height: 175 }, 3, 'desktop.benefitService');
  await assertBox(page, '.template--homepage .f-section--showroom .f-section__container', { x: 260, y: 2102, width: 1400, height: 649 }, 4, 'desktop.showroomContainer');
  await assertBox(page, '.template--homepage .f-showroom-panel', { x: 260, y: 2102, width: 1400, height: 525 }, 4, 'desktop.showroomPanel');
  await assertBox(page, '.template--homepage .f-progress-layout', { x: 265, y: 2863, width: 1392, height: 444 }, 4, 'desktop.progress');
  await assertBox(page, '.template--homepage .f-section--references', { x: 0, y: 3418, width: 1920, height: 422 }, 4, 'desktop.referencesSection');
  await assertBox(page, '.template--homepage .f-section--references .f-carousel', { x: 258, y: 3520, width: 1400, height: 320 }, 4, 'desktop.referencesCarousel');
  await assertBox(page, '.template--homepage .f-contact-cta', { x: 260, y: 3945, width: 1400, height: 455 }, 4, 'desktop.contactCta');
  await assertFooterLayout(page, 'desktop', 4428);

  await assertSourceContains(page, '.f-logo__img', 'images/logo.svg', 'desktop.logoSource');
  await assertSourceContains(page, '.template--homepage .f-slide--1 .f-slide__background', 'uploads/import/figma/hp-hero-arctic-spas-07.jpg', 'desktop.heroBackgroundSource');
  await assertSourceContains(page, '.f-slide__background img', 'uploads/import/figma/hp-hero-arctic-spas-07.jpg', 'desktop.heroImageSource');
  await assertSourceContains(page, '.f-hero-promo__image', 'uploads/import/figma/hp-fixed-banner-product.png', 'desktop.heroPromoImageSource');
  await assertSourceContains(page, '.template--homepage .f-category:nth-child(1) .f-category__image img', 'uploads/import/figma/hp-category-virivky.jpg', 'desktop.categoryHotTubsImageSource');
  await assertSourceContains(page, '.template--homepage .f-category:nth-child(2) .f-category__image img', 'uploads/import/figma/hp-category-celorocni-bazeny.png', 'desktop.categorySwimspaImageSource');
  await assertComputedStyle(page, '.template--homepage .f-slide--1 .f-slide__background', 'background-size', 'cover', 'desktop.heroBackgroundFit');
}

async function auditDesktopScaledMatrix(page) {
  for (const width of [1903, 1600, 1536, 1456, 1440, 1400]) {
    const scale = heroScale(width);
    const heroHeight = 795 * scale;
    const captionWidth = 488 * scale;
    const captionHeight = 309 * scale;
    const captionX = ((width - (1400 * scale)) / 2) + (6 * scale);
    const captionY = 280 * scale;
    const arrowSize = 42 * scale;
    const arrowY = 382 * scale;
    const prevArrowX = 125 * scale;
    const nextArrowX = width - (111 * scale) - arrowSize;
    const promo = promoBox(width);

    await page.setViewportSize({ width, height: 1080 });
    await page.goto(baseUrl, { waitUntil: 'load' });

    await assertNoHorizontalOverflow(page, `scaledDesktop:${width}`);
    await assertBox(page, '.f-section--slides', { x: 0, y: 0, width, height: heroHeight }, 4, `scaledDesktop:${width}.heroSection`);
    await assertBox(page, '.f-caption', { x: captionX, y: captionY, width: captionWidth, height: captionHeight }, 6, `scaledDesktop:${width}.heroCaption`);
    await assertBox(page, '.f-slides__control--prev', { x: prevArrowX, y: arrowY, width: arrowSize, height: arrowSize }, 6, `scaledDesktop:${width}.heroPrevArrow`);
    await assertBox(page, '.f-slides__control--next', { x: nextArrowX, y: arrowY, width: arrowSize, height: arrowSize }, 6, `scaledDesktop:${width}.heroNextArrow`);
    await assertBox(page, '.f-hero-promo', { x: promo.x, y: promo.y, width: promo.width, height: promo.height }, 8, `scaledDesktop:${width}.heroPromo`);
    await assertInsideViewport(page, '.f-hero-promo', width, `scaledDesktop:${width}.heroPromoInside`, 20);
    await assertComputedStyle(page, '.template--homepage .f-slide--1 .f-slide__background', 'background-size', 'cover', `scaledDesktop:${width}.heroBackgroundFit`);
    await assertHeroBoundaryIsClear(page, `scaledDesktop:${width}`);
  }

  for (const width of [1366, 1280]) {
    await page.setViewportSize({ width, height: 1080 });
    await page.goto(baseUrl, { waitUntil: 'load' });
    const promo = await box(page, '.f-hero-promo', `scaledDesktopBoundary:${width}.heroPromo`);

    if (promo.width < 170 || promo.height < 185) {
      throw new Error(`scaledDesktopBoundary:${width}.heroPromoSize: expected visible desktop promo, got ${round(promo.width)}x${round(promo.height)}`);
    }

    await assertInsideViewport(page, '.f-hero-promo', width, `scaledDesktopBoundary:${width}.heroPromoInside`, 18);
    await assertHeroBoundaryIsClear(page, `scaledDesktopBoundary:${width}`);
  }
}

async function auditMobile(page) {
  await page.setViewportSize({ width: 390, height: 900 });
  await page.goto(baseUrl, { waitUntil: 'load' });

  await assertBox(page, '.f-logo__img', { x: 20, y: 7, width: 85.6, height: 48 }, 2, 'mobile.logo');
  await assertBox(page, '.f-navigation__trigger', { x: 325, y: 8.5, width: 45, height: 45 }, 2, 'mobile.menuButton');
  await assertBox(page, '.f-section--slides', { x: 0, y: 0, width: 390, height: 556 }, 2, 'mobile.heroSection');
  await assertHiddenBox(page, '.f-hero-promo', 'mobile.heroPromo');
  await assertBox(page, '.f-category:nth-child(1)', { x: 27.5, y: 556, width: 335, height: 221 }, 3, 'mobile.categoryHotTubs');
  await assertBox(page, '.f-category:nth-child(2)', { x: 27.5, y: 798, width: 335, height: 221 }, 4, 'mobile.categorySwimspa');
  await assertHomepageCategoryCardsClickable(page, 'mobile.categoryCards');

  await assertSourceContains(page, '.f-logo__img', 'images/logo.svg', 'mobile.logoSource');
  await assertSourceContains(page, '.template--homepage .f-category:nth-child(1) .f-category__image img', 'uploads/import/figma/mobile-category-virivky.jpg', 'mobile.categoryHotTubsImageSource');
  await assertSourceContains(page, '.template--homepage .f-category:nth-child(2) .f-category__image img', 'uploads/import/figma/mobile-category-celorocni-bazeny.png', 'mobile.categorySwimspaImageSource');
}

async function auditNarrowHomepageLayout(page) {
  for (const { width, height } of [
    { width: 904, height: 617 },
    { width: 1023, height: 617 },
  ]) {
    const label = `narrowHomepage:${width}x${height}`;

    await page.setViewportSize({ width, height });
    await page.goto(baseUrl, { waitUntil: 'load' });

    await assertNoHorizontalOverflow(page, label);
    await assertViewportSpan(page, '.template--homepage .f-section--slides', width, `${label}.heroSection`);
    await assertViewportSpan(page, '.template--homepage .f-section--product-categories', width, `${label}.categoriesSection`);
    await assertHeroBoundaryIsClear(page, label);
    await assertHiddenBox(page, '.template--homepage .f-hero-promo', `${label}.heroPromo`);
    await assertComputedStyle(page, '.template--homepage .f-slide--1 .f-slide__background', 'background-size', 'cover', `${label}.heroBackgroundFit`);
    await assertHeroBoundaryIsClear(page, label);

    const layout = await page.evaluate(() => {
      const box = (selector) => {
        const element = document.querySelector(selector);
        if (!element) {
          return null;
        }

        const rect = element.getBoundingClientRect();
        const style = getComputedStyle(element);

        return {
          x: rect.x,
          y: rect.y,
          width: rect.width,
          height: rect.height,
          bottom: rect.bottom,
          display: style.display,
          gridTemplateColumns: style.gridTemplateColumns,
        };
      };

      return {
        narrowHeroVariable: getComputedStyle(document.documentElement).getPropertyValue('--arctic-narrow-hero-height').trim(),
        slides: box('.template--homepage .f-section--slides'),
        background: box('.template--homepage .f-slide__background'),
        categories: box('.template--homepage .f-section--product-categories'),
        caption: box('.template--homepage .f-caption'),
        headline: box('.template--homepage .f-caption__header h2'),
        grid: box('.template--homepage .f-categories--product'),
        firstCard: box('.template--homepage .f-category:nth-child(1)'),
        secondCard: box('.template--homepage .f-category:nth-child(2)'),
      };
    });

    if (!layout.slides || !layout.background || !layout.categories || !layout.caption || !layout.headline || !layout.grid || !layout.firstCard || !layout.secondCard) {
      throw new Error(`${label}.missingElements`);
    }

    if (!layout.narrowHeroVariable) {
      throw new Error(`${label}.variables: narrow homepage variables are not active`);
    }

    if (layout.slides.height < 500 || layout.slides.height > 558) {
      throw new Error(`${label}.heroHeight: expected narrow hero height, got ${round(layout.slides.height)}px`);
    }

    assertClose(layout.background.width, width, 2, `${label}.background.width`);
    assertClose(layout.background.height, layout.slides.height, 2, `${label}.background.height`);

    if (layout.caption.x < 24 || layout.caption.x > 96) {
      throw new Error(`${label}.caption.x: expected narrow caption to stay in the content gutter, got ${round(layout.caption.x)}px`);
    }

    if (layout.headline.x + layout.headline.width > width - 24) {
      throw new Error(`${label}.headline.overflow: headline reaches outside viewport (${round(layout.headline.x + layout.headline.width)} > ${width - 24})`);
    }

    if (layout.categories.y < layout.slides.bottom - 1) {
      throw new Error(`${label}.overlap: category section starts before hero ends (${round(layout.categories.y)} < ${round(layout.slides.bottom)})`);
    }

    const cardGap = layout.firstCard.y - layout.slides.bottom;
    if (cardGap < 24 || cardGap > 40) {
      throw new Error(`${label}.categoryBoundary: expected compact hero/category gap, got ${round(cardGap)}px`);
    }

    const columns = layout.grid.gridTemplateColumns.split(' ').filter(Boolean);
    if (columns.length !== 2) {
      throw new Error(`${label}.categoryGrid: expected 2 narrow columns, got "${layout.grid.gridTemplateColumns}"`);
    }

    for (const [name, card] of [['firstCard', layout.firstCard], ['secondCard', layout.secondCard]]) {
      if (card.width < 360 || card.width > 430) {
        throw new Error(`${label}.${name}.width: expected narrow card width, got ${round(card.width)}px`);
      }

      if (card.height < 238 || card.height > 282) {
        throw new Error(`${label}.${name}.height: expected narrow card height, got ${round(card.height)}px`);
      }
    }

    if (layout.firstCard.y > height - 40) {
      throw new Error(`${label}.firstViewport: category cards are not visible enough in the first viewport`);
    }
  }
}

async function auditFigmaSources(page) {
  await assertHtmlContains(page, '/virivky/', [
    'uploads/import/figma/category-vlastnosti.jpg',
    'uploads/import/figma/category-zaruka.jpg',
    'uploads/import/figma/category-configurator.png',
  ], [
    'category-vlastnosti-1024',
    'category-zaruka-1024',
    'category-configurator-1024',
  ]);

  await assertHtmlContains(page, '/kontakt/', [
    'uploads/import/figma/contact-map-showroom.png',
  ], [
    'contact-map-showroom-2048',
  ]);
}

async function auditPhase5BHardening(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });

  await page.goto(`${baseUrl}/reference/`, { waitUntil: 'load' });
  await assertComputedStyle(page, '.f-reference-card', 'border-radius', '40px', 'phase5b.referenceArchiveRadius');
  await assertImageMaxUpscale(page, '.f-reference-card img', 1.25, 'phase5b.referenceArchiveImageUpscale', { limit: 9 });

  await page.goto(`${baseUrl}/product/timberwolf/`, { waitUntil: 'load' });
  await assertComputedStyle(page, '.f-section--references .f-listing--reference', 'border-radius', '40px', 'phase5b.productReferenceRadius');
  await assertImageMaxUpscale(page, '.f-section--references .f-listing--reference img', 1.25, 'phase5b.productReferenceImageUpscale', { limit: 6 });
  await assertImageMaxUpscale(page, '.f-heading--product-detail .f-gallery__slide:nth-child(1) img', 1.25, 'phase5b.timberwolfHeroUpscale');

  await page.goto(`${baseUrl}/kontakt/`, { waitUntil: 'load' });
  await assertComputedStyle(page, '.f-heading__buttons .a-button', 'border-radius', '50px', 'phase5b.contactTopButtonsRadius');

  await page.goto(`${baseUrl}/showroom/`, { waitUntil: 'load' });
  await assertComputedStyle(page, '.f-showroom-gallery-button', 'border-radius', '50px', 'phase5b.showroomGalleryButtonRadius');
  await assertComputedStyle(page, '.f-showroom-mini-cta .a-button', 'border-radius', '50px', 'phase5b.showroomAppointmentButtonRadius');

  await page.goto(`${baseUrl}/virivky/`, { waitUntil: 'load' });
  await assertImageMaxUpscale(page, '.f-products-series--custom .f-listing--product:nth-child(1) .f-listing__image img', 1.25, 'phase5b.hotTubsCardOneUpscale');
  await assertImageMaxUpscale(page, '.f-products-series--custom .f-listing--product:nth-child(2) .f-listing__image img', 1.25, 'phase5b.hotTubsCardTwoUpscale');
  await assertImageMaxUpscale(page, '.f-products-series--custom .f-listing--product:nth-child(3) .f-listing__image img', 1.25, 'phase5b.hotTubsCardThreeUpscale');

  await page.goto(`${baseUrl}/swimspa/`, { waitUntil: 'load' });
  await assertImageMaxUpscale(page, '.f-products-series--swimspa .f-listing--product:nth-child(1) .f-listing__image img', 1.25, 'phase5b.swimspaCardOneUpscale');
  await assertImageMaxUpscale(page, '.f-products-series--swimspa .f-listing--product:nth-child(2) .f-listing__image img', 1.25, 'phase5b.swimspaCardTwoUpscale');
  await assertImageMaxUpscale(page, '.f-products-series--swimspa .f-listing--product:nth-child(3) .f-listing__image img', 1.25, 'phase5b.swimspaCardThreeUpscale');
}

async function auditCatalogHotTubsDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/virivky/`, { waitUntil: 'load' });

  await assertBox(page, '.f-heading--term', { x: 0, y: 0, width: 1920, height: 795 }, 2, 'catalog.heading');
  await assertBox(page, '.f-heading__container', { x: 260, y: 0, width: 1400, height: 795 }, 2, 'catalog.headingContainer');
  await assertBox(page, '.f-section--category-intro', { x: 0, y: 795, width: 1920, height: 1195 }, 3, 'catalog.categoryIntroSection');
  await assertBox(page, '.f-category-intro--split', { x: 260, y: 910, width: 1400, height: 424 }, 3, 'catalog.categoryIntroFeatures');
  await assertBox(page, '.f-category-intro--reverse', { x: 260, y: 1472, width: 1400, height: 424 }, 3, 'catalog.categoryIntroWarranty');
  await assertBox(page, '.f-section--series-nav', { x: 0, y: 1990, width: 1920, height: 93 }, 3, 'catalog.seriesNavSection');
  const catalogSeriesNav = await box(page, '.f-series-nav', 'catalog.seriesNav');
  assertClose(catalogSeriesNav.x, 313, 4, 'catalog.seriesNav.x');
  assertClose(catalogSeriesNav.y, 2012, 4, 'catalog.seriesNav.y');
  assertBetween(catalogSeriesNav.width, 560, 620, 'catalog.seriesNav.width');
  assertClose(catalogSeriesNav.height, 51, 4, 'catalog.seriesNav.height');
  await assertSeriesNavNoOverlap(page, '.f-series-nav', 'catalog.seriesNavNoOverlap');
  await assertBox(page, '.f-products-series--custom', { x: 260, y: 2177, width: 1400, height: 1039 }, 4, 'catalog.customSeries');
  await assertBox(page, '.f-products-series--classic', { x: 260, y: 3331, width: 1400, height: 686 }, 4, 'catalog.classicSeries');
  await assertBox(page, '.f-products-series--core', { x: 260, y: 4132, width: 1400, height: 686 }, 4, 'catalog.coreSeries');
  await assertBox(page, '.f-products-series--custom .f-listing--product:nth-child(1)', { x: 615, y: 2177, width: 335, height: 333 }, 3, 'catalog.productCardOne');
  await assertBox(page, '.f-products-series--custom .f-listing--product:nth-child(2)', { x: 970, y: 2177, width: 335, height: 333 }, 3, 'catalog.productCardTwo');
  await assertBox(page, '.f-products-series--custom .f-listing--product:nth-child(3)', { x: 1325, y: 2177, width: 335, height: 333 }, 3, 'catalog.productCardThree');
  await assertBox(page, '.f-configurator-cta', { x: 260, y: 4994, width: 1400, height: 312 }, 3, 'catalog.configurator');
  await assertBox(page, '.f-showroom-panel', { x: 260, y: 5484, width: 1400, height: 525 }, 4, 'catalog.showroomPanel');
  await assertBox(page, '.f-progress-layout', { x: 264, y: 6245, width: 1392, height: 444 }, 4, 'catalog.progress');
  await assertBox(page, '.f-section--references', { x: 0, y: 6800, width: 1920, height: 422 }, 4, 'catalog.references');
  await assertBox(page, '.f-contact-cta', { x: 260, y: 7327, width: 1400, height: 455 }, 4, 'catalog.contactCta');
  await assertFooterLayout(page, 'catalog', 7810);

  await assertSourceContains(page, '.f-heading--term .f-background__image img', 'virivky.jpg', 'catalog.heroSource');
  await assertSourceContains(page, '.f-category-intro--split .f-category-intro__image img', 'uploads/import/figma/category-vlastnosti.jpg', 'catalog.featuresSource');
  await assertSourceContains(page, '.f-category-intro--reverse .f-category-intro__image img', 'uploads/import/figma/category-zaruka.jpg', 'catalog.warrantySource');
  await assertSourceContains(page, '.f-products-series--custom .f-listing--product:nth-child(1) .f-listing__image img', 'virivka-summit-xl.jpg', 'catalog.productCardOneSource');
  await assertSourceContains(page, '.f-products-series--custom .f-listing--product:nth-child(2) .f-listing__image img', 'virivka-summit.jpg', 'catalog.productCardTwoSource');
  await assertSourceContains(page, '.f-products-series--custom .f-listing--product:nth-child(3) .f-listing__image img', 'virivka-tundra.jpg', 'catalog.productCardThreeSource');
  await assertSourceContains(page, '.f-configurator-cta__image', 'uploads/import/figma/category-configurator.png', 'catalog.configuratorSource');
  await assertSourceContains(page, '.f-showroom-panel__image--1 img', 'uploads/import/owner-showroom/showroom-main-web.jpg', 'catalog.showroomOneSource');
  await assertSourceContains(page, '.f-showroom-panel__image--2 img', 'uploads/import/owner-showroom/showroom-covana-interior-web.jpg', 'catalog.showroomTwoSource');
  await assertSourceContains(page, '.f-showroom-panel__image--3 img', 'uploads/import/owner-showroom/showroom-detail-web.jpg', 'catalog.showroomThreeSource');
}

async function auditCatalogSwimspaDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/swimspa/`, { waitUntil: 'load' });

  await assertBox(page, '.f-heading--term', { x: 0, y: 0, width: 1920, height: 795 }, 2, 'swimspaCatalog.heading');
  await assertBox(page, '.f-heading__container', { x: 260, y: 0, width: 1400, height: 795 }, 2, 'swimspaCatalog.headingContainer');
  await assertBox(page, '.f-section--category-intro', { x: 0, y: 795, width: 1920, height: 1195 }, 3, 'swimspaCatalog.categoryIntroSection');
  await assertBox(page, '.f-category-intro--split', { x: 260, y: 910, width: 1400, height: 424 }, 3, 'swimspaCatalog.categoryIntroBenefits');
  await assertBox(page, '.f-category-intro--reverse', { x: 260, y: 1472, width: 1400, height: 424 }, 3, 'swimspaCatalog.categoryIntroOperation');
  await assertBox(page, '.f-section--series-nav', { x: 0, y: 1990, width: 1920, height: 93 }, 3, 'swimspaCatalog.seriesNavSection');
  const swimspaSeriesNav = await box(page, '.f-series-nav', 'swimspaCatalog.seriesNav');
  assertClose(swimspaSeriesNav.x, 313, 4, 'swimspaCatalog.seriesNav.x');
  assertClose(swimspaSeriesNav.y, 2012, 4, 'swimspaCatalog.seriesNav.y');
  assertBetween(swimspaSeriesNav.width, 560, 620, 'swimspaCatalog.seriesNav.width');
  assertClose(swimspaSeriesNav.height, 51, 4, 'swimspaCatalog.seriesNav.height');
  await assertSeriesNavNoOverlap(page, '.f-series-nav', 'swimspaCatalog.seriesNavNoOverlap');
  await assertBox(page, '.f-section--products-grouped', { x: 0, y: 2083, width: 1920, height: 780 }, 4, 'swimspaCatalog.productsSection');
  await assertBox(page, '.f-products-series--swimspa', { x: 260, y: 2177, width: 1400, height: 686 }, 4, 'swimspaCatalog.swimspaSeries');
  await assertBox(page, '.f-products-series--swimspa .f-listing--product:nth-child(1)', { x: 615, y: 2177, width: 335, height: 333 }, 3, 'swimspaCatalog.productCardOne');
  await assertBox(page, '.f-products-series--swimspa .f-listing--product:nth-child(2)', { x: 970, y: 2177, width: 335, height: 333 }, 3, 'swimspaCatalog.productCardTwo');
  await assertBox(page, '.f-products-series--swimspa .f-listing--product:nth-child(3)', { x: 1325, y: 2177, width: 335, height: 333 }, 3, 'swimspaCatalog.productCardThree');
  await assertBox(page, '.f-configurator-cta', { x: 260, y: 2978, width: 1400, height: 312 }, 3, 'swimspaCatalog.configurator');
  await assertBox(page, '.f-showroom-panel', { x: 260, y: 3575, width: 1400, height: 525 }, 4, 'swimspaCatalog.showroomPanel');
  await assertBox(page, '.f-progress-layout', { x: 264, y: 4336, width: 1392, height: 444 }, 4, 'swimspaCatalog.progress');
  await assertBox(page, '.f-section--references', { x: 0, y: 4891, width: 1920, height: 422 }, 4, 'swimspaCatalog.references');
  await assertBox(page, '.f-contact-cta', { x: 260, y: 5418, width: 1400, height: 455 }, 4, 'swimspaCatalog.contactCta');
  await assertFooterLayout(page, 'swimspaCatalog', 5901);

  await assertSourceContains(page, '.f-category-intro--split .f-category-intro__image img', 'uploads/import/figma-category-celorocni-bazeny.jpg', 'swimspaCatalog.benefitsSource');
  await assertSourceContains(page, '.f-category-intro--reverse .f-category-intro__image img', 'uploads/import/legacy-categories/swimspa.jpg', 'swimspaCatalog.operationSource');
  await assertSourceContains(page, '.f-products-series--swimspa .f-listing--product:nth-child(1) .f-listing__image img', 'bazen-athabascan.jpg', 'swimspaCatalog.productCardOneSource');
  await assertSourceContains(page, '.f-products-series--swimspa .f-listing--product:nth-child(2) .f-listing__image img', 'bazen-hudson.jpg', 'swimspaCatalog.productCardTwoSource');
  await assertSourceContains(page, '.f-products-series--swimspa .f-listing--product:nth-child(3) .f-listing__image img', 'bazen-kingfisher.jpg', 'swimspaCatalog.productCardThreeSource');
}

async function auditTimberwolfDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/product/timberwolf/`, { waitUntil: 'load' });

  await assertBox(page, '.f-heading--product-detail', { x: 0, y: 0, width: 1920, height: 795 }, 2, 'timberwolf.heading');
  await assertBox(page, '.f-heading__container', { x: 260, y: 0, width: 1400, height: 795 }, 2, 'timberwolf.headingContainer');
  await assertBox(page, '.f-product-detail-config__layout', { x: 260, y: 940, width: 1400, height: 546 }, 3, 'timberwolf.configLayout');
  await assertBox(page, '.f-product-configurations', { x: 260, y: 940, width: 1132, height: 546 }, 3, 'timberwolf.configurations');
  await assertBox(page, '.f-product-configuration:nth-child(1)', { x: 260, y: 1041, width: 1132, height: 203 }, 3, 'timberwolf.configurationPrestige');
  await assertBox(page, '.f-product-configuration:nth-child(2)', { x: 260, y: 1283, width: 1132, height: 203 }, 3, 'timberwolf.configurationSignature');
  await assertBox(page, '.f-product-contact-card', { x: 1362, y: 934, width: 298, height: 341 }, 3, 'timberwolf.contactCard');
  await assertBox(page, '.f-product-contact-card__details', { x: 1392, y: 1018, width: 233, height: 70.6 }, 3, 'timberwolf.contactDetails');
  await assertBox(page, '.f-product-contact-card__avatar', { x: 1392, y: 1115, width: 58, height: 58 }, 2, 'timberwolf.contactAvatar');
  await assertBox(page, '.f-product-contact-card__button', { x: 1392, y: 1195, width: 149, height: 50 }, 2, 'timberwolf.contactButton');
  await assertBox(page, '.f-product-detail-configurator', { x: 260, y: 1608, width: 1400, height: 312 }, 3, 'timberwolf.configurator');
  await assertBox(page, '.f-product-colors', { x: 260, y: 2022, width: 1400, height: 391 }, 4, 'timberwolf.colors');
  await assertBox(page, '.f-section--product-benefits', { x: 0, y: 2518, width: 1920, height: 2017 }, 4, 'timberwolf.benefits');
  await assertBox(page, '.f-section--product-options', { x: 0, y: 4535, width: 1920, height: 1144 }, 4, 'timberwolf.options');
  await assertBox(page, '.f-section--references', { x: 0, y: 5679, width: 1920, height: 525 }, 4, 'timberwolf.references');
  await assertBox(page, '.f-contact-cta', { x: 260, y: 6204, width: 1400, height: 455 }, 4, 'timberwolf.contactCta');
  await assertFooterLayout(page, 'timberwolf', 6687);

  await assertSourceContains(page, '.f-heading--product-detail .f-gallery__slide:nth-child(1) img', 'timberwolf-signature.jpg', 'timberwolf.heroSource');
  await assertSourceContains(page, '.f-product-configuration:nth-child(1) .f-product-configuration__thumb img', 'timberwolf-prestige.jpg', 'timberwolf.prestigeSource');
  await assertSourceContains(page, '.f-product-configuration:nth-child(2) .f-product-configuration__thumb img', 'timberwolf-signature.jpg', 'timberwolf.signatureSource');
  await assertSourceContains(page, '.f-product-detail-configurator .f-configurator-cta__image', 'uploads/import/figma/category-configurator.png', 'timberwolf.configuratorSource');
  await assertSourceContains(page, '.f-product-contact-card__avatar img', 'uploads/import/figma/contact-lukas-dusek.png', 'timberwolf.contactAvatarSource');
  await assertComputedStyle(page, '.f-heading--product-detail .f-gallery__slide:nth-child(1) img', 'object-fit', 'cover', 'timberwolf.heroImageFit');
}

async function auditBenefitPopupDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/product/timberwolf/`, { waitUntil: 'load' });

  await page.locator('.f-product-benefit--has-popup .f-product-benefit__trigger').first().click();
  await page.waitForTimeout(250);

  const activePopup = await page.locator('.f-off--benefit-popup.active').count();
  if (!activePopup) {
    throw new Error('benefitPopup: shell benefit popup did not open');
  }

  await assertBox(page, '.f-benefit-popup', { x: 546, y: 91, width: 828, height: 1098 }, 3, 'benefitPopup.panel');
  await assertBox(page, '.f-benefit-popup__close', { x: 1311, y: 109, width: 47, height: 47 }, 3, 'benefitPopup.close');
  await assertBox(page, '.f-benefit-popup h2', { x: 601, y: 137, width: 594, height: 102 }, 4, 'benefitPopup.title');
  await assertBox(page, '.f-benefit-popup__media', { x: 602, y: 265, width: 697, height: 364 }, 4, 'benefitPopup.media');
  await assertBox(page, '.f-benefit-popup__content', { x: 601, y: 670, width: 671, height: 480 }, 4, 'benefitPopup.content');
  await assertBox(page, '.f-benefit-popup__button', { x: 601, y: 1101, width: 129, height: 55 }, 6, 'benefitPopup.button');
  await assertSourceContains(page, '.f-benefit-popup__media img', 'uploads/import/figma/popup-shell-detail.png', 'benefitPopup.mediaSource');

  const popupText = await page.locator('.f-benefit-popup').innerText();
  for (const expected of ['Samonosná kompozitní skořepina', 'Aristech', 'Bio-Lok']) {
    if (!popupText.includes(expected)) {
      throw new Error(`benefitPopup.text: missing "${expected}"`);
    }
  }

  await page.locator('.f-benefit-popup__close').first().click();
  await page.waitForTimeout(250);
}

async function auditShowroomDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/showroom/`, { waitUntil: 'load' });

  await assertBox(page, '.f-showroom-page', { x: 0, y: 0, width: 1920, height: 2879 }, 4, 'showroom.page');
  await assertBox(page, '.f-showroom-hero', { x: 0, y: 0, width: 1920, height: 801 }, 3, 'showroom.hero');
  await assertBox(page, '.f-showroom-hero__container', { x: 260, y: 0, width: 1400, height: 801 }, 3, 'showroom.heroContainer');
  await assertBox(page, '.f-showroom-breadcrumb', { x: 262, y: 144, width: 224, height: 19 }, 3, 'showroom.breadcrumb');
  await assertBox(page, '.f-showroom-hero__content', { x: 262, y: 329, width: 488, height: 236 }, 4, 'showroom.heroContent');
  await assertBox(page, '.f-showroom-hero__content h1', { x: 262, y: 329, width: 454, height: 61 }, 3, 'showroom.heroTitle');
  await assertBox(page, '.f-showroom-hero__content p', { x: 262, y: 403, width: 488, height: 93 }, 3, 'showroom.heroLead');
  await assertBox(page, '.f-showroom-gallery-button', { x: 262, y: 515, width: 161, height: 50 }, 3, 'showroom.galleryButton');
  await assertBox(page, '.f-showroom-area-badge', { x: 648, y: 256, width: 121, height: 123 }, 3, 'showroom.areaBadge');
  await assertBox(page, '.f-showroom-mini-cta', { x: 1167, y: 725, width: 498, height: 299 }, 4, 'showroom.miniCta');
  await assertBox(page, '.f-showroom-info', { x: 0, y: 801, width: 1920, height: 335 }, 4, 'showroom.info');
  await assertBox(page, '.f-showroom-info__container', { x: 260, y: 801, width: 1400, height: 215 }, 4, 'showroom.infoContainer');
  await assertBox(page, '.f-showroom-info__item:nth-child(1)', { x: 260, y: 886, width: 264, height: 130 }, 4, 'showroom.infoContact');
  await assertBox(page, '.f-showroom-info__item:nth-child(2)', { x: 557, y: 886, width: 264, height: 130 }, 4, 'showroom.infoMap');
  await assertBox(page, '.f-showroom-info__item:nth-child(3)', { x: 854, y: 886, width: 264, height: 130 }, 4, 'showroom.infoHours');
  await assertBox(page, '.f-showroom-reasons', { x: 0, y: 1136, width: 1920, height: 526 }, 4, 'showroom.reasons');
  await assertBox(page, '.f-showroom-reasons__container', { x: 260, y: 1136, width: 1400, height: 404 }, 4, 'showroom.reasonsContainer');
  await assertBox(page, '.f-showroom-reasons h2', { x: 673, y: 1199, width: 575, height: 51 }, 4, 'showroom.reasonsTitle');
  await assertBox(page, '.f-showroom-reasons__grid', { x: 313, y: 1300, width: 1293, height: 172 }, 4, 'showroom.reasonsGrid');
  await assertBox(page, '.f-showroom-split--first', { x: 0, y: 1662, width: 1920, height: 562 }, 4, 'showroom.splitFirst');
  await assertBox(page, '.f-showroom-split--first .f-showroom-split__copy', { x: 260, y: 1743, width: 575, height: 237 }, 4, 'showroom.splitFirstCopy');
  await assertBox(page, '.f-showroom-split--first img', { x: 986, y: 1662, width: 674, height: 424 }, 4, 'showroom.splitFirstImage');
  await assertBox(page, '.f-showroom-split--second', { x: 0, y: 2224, width: 1920, height: 655 }, 4, 'showroom.splitSecond');
  await assertBox(page, '.f-showroom-split--second img', { x: 260, y: 2224, width: 674, height: 424 }, 4, 'showroom.splitSecondImage');
  await assertBox(page, '.f-showroom-split--second .f-showroom-split__copy', { x: 1024, y: 2338, width: 575, height: 135 }, 4, 'showroom.splitSecondCopy');
  await assertBox(page, '.page-template-template-showroom .f-section--contact', { x: 0, y: 2879, width: 1920, height: 455 }, 4, 'showroom.contactSection');
  await assertBox(page, '.page-template-template-showroom .f-contact-cta', { x: 260, y: 2899, width: 1400, height: 382.4 }, 4, 'showroom.contactCta');
  await assertFooterLayout(page, 'showroom', 3362);

  await assertSourceContains(page, '.f-showroom-hero', 'uploads/import/owner-showroom/showroom-covana-interior-web.jpg', 'showroom.heroSource');
  await assertSourceContains(page, '.f-showroom-split--first img', 'uploads/import/owner-showroom/showroom-detail-web.jpg', 'showroom.splitFirstSource');
  await assertSourceContains(page, '.f-showroom-split--second img', 'uploads/import/owner-showroom/showroom-main-web.jpg', 'showroom.splitSecondSource');
}

async function auditFigmaInfoPagesDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });

  await page.goto(`${baseUrl}/vlastnosti/`, { waitUntil: 'load' });
  await assertBox(page, '.f-heading', { x: 0, y: 0, width: 1920, height: 551 }, 3, 'features.heading');
  await assertBox(page, '.f-heading__headline h1', { x: 260, y: 206, width: 896, height: 122 }, 4, 'features.title');
  await assertBox(page, '.f-heading__description', { x: 260, y: 361, width: 856, height: 124 }, 4, 'features.description');
  await assertBox(page, '.f-section--feature-cards', { x: 0, y: 551, width: 1920, height: 748 }, 3, 'features.cardsSection');
  await assertBox(page, '.f-figma-card-grid--features', { x: 260, y: 551, width: 1400, height: 748 }, 3, 'features.cardsGrid');
  await assertBox(page, '.f-figma-card--feature:nth-child(1)', { x: 260, y: 551, width: 334, height: 364 }, 3, 'features.cardOne');
  await assertBox(page, '.f-figma-card--feature:nth-child(8)', { x: 1322, y: 935, width: 334, height: 364 }, 3, 'features.cardEight');
  await assertBox(page, '.page-template-template-features .f-section--contact', { x: 0, y: 1414, width: 1920, height: 483 }, 4, 'features.contactSection');
  await assertBox(page, '.page-template-template-features .f-contact-cta', { x: 260, y: 1414, width: 1400, height: 455 }, 4, 'features.contactCta');
  await assertFooterLayout(page, 'features', 1897);
  await assertSourceContains(page, '.f-figma-card--feature:nth-child(1)', 'uploads/import/figma/category-hero-virivky.jpg', 'features.cardSource');

  await page.goto(`${baseUrl}/sluzby/`, { waitUntil: 'load' });
  await assertBox(page, '.f-heading', { x: 0, y: 0, width: 1920, height: 447 }, 3, 'services.heading');
  await assertBox(page, '.f-heading__headline h1', { x: 260, y: 206, width: 896, height: 61 }, 4, 'services.title');
  await assertBox(page, '.f-heading__description', { x: 260, y: 289, width: 856, height: 93 }, 4, 'services.description');
  await assertBox(page, '.f-section--figma-services', { x: 0, y: 447, width: 1920, height: 918 }, 3, 'services.section');
  await assertBox(page, '.f-service-grid', { x: 260, y: 447, width: 1400, height: 862 }, 4, 'services.grid');
  await assertBox(page, '.f-service-card:nth-child(1) img', { x: 260, y: 447, width: 453, height: 224 }, 3, 'services.cardOneImage');
  await assertBox(page, '.f-service-card:nth-child(1) h2', { x: 260, y: 693, width: 453, height: 32 }, 4, 'services.cardOneTitle');
  await assertBox(page, '.f-service-card:nth-child(1) p', { x: 260, y: 734, width: 427, height: 125 }, 4, 'services.cardOneText');
  await assertBox(page, '.f-service-card:nth-child(6) img', { x: 1210, y: 922, width: 453, height: 224 }, 4, 'services.cardSixImage');
  await assertBox(page, '.page-template-template-services .f-section--contact', { x: 0, y: 1480, width: 1920, height: 483 }, 4, 'services.contactSection');
  await assertBox(page, '.page-template-template-services .f-contact-cta', { x: 260, y: 1480, width: 1400, height: 455 }, 4, 'services.contactCta');
  await assertFooterLayout(page, 'services', 1963);

  await page.goto(`${baseUrl}/certifikaty/`, { waitUntil: 'load' });
  await assertBox(page, '.f-heading', { x: 0, y: 0, width: 1920, height: 529 }, 3, 'certificates.heading');
  await assertBox(page, '.f-heading__headline h1', { x: 260, y: 206, width: 896, height: 61 }, 4, 'certificates.title');
  await assertBox(page, '.f-heading__description', { x: 260, y: 289, width: 910, height: 155 }, 4, 'certificates.description');
  await assertBox(page, '.f-section--certificates', { x: 0, y: 529, width: 1920, height: 665 }, 4, 'certificates.section');
  await assertBox(page, '.f-certificate-copy section:nth-child(1) h2', { x: 260, y: 568, width: 734, height: 51 }, 4, 'certificates.firstTitle');
  await assertBox(page, '.f-certificate-copy section:nth-child(1) p', { x: 260, y: 635, width: 657, height: 116 }, 8, 'certificates.firstText');
  await assertBox(page, '.f-certificate-copy section:nth-child(2) h2', { x: 260, y: 933, width: 734, height: 51 }, 4, 'certificates.secondTitle');
  await assertBox(page, '.f-certificate-images img:nth-child(1)', { x: 1037, y: 529, width: 300, height: 300 }, 4, 'certificates.imageOne');
  await assertBox(page, '.f-certificate-images img:nth-child(2)', { x: 1359, y: 529, width: 300, height: 300 }, 4, 'certificates.imageTwo');
  await assertBox(page, '.f-certificate-images img:nth-child(3)', { x: 1037, y: 894, width: 300, height: 300 }, 4, 'certificates.imageThree');
  await assertBox(page, '.page-template-template-certificates .f-contact-cta', { x: 260, y: 1320, width: 1400, height: 455 }, 4, 'certificates.contactCta');
  await assertFooterLayout(page, 'certificates', 1803);

  await page.goto(`${baseUrl}/zaruka/`, { waitUntil: 'load' });
  await assertBox(page, '.f-heading', { x: 0, y: 0, width: 1920, height: 435 }, 3, 'warranty.heading');
  await assertBox(page, '.f-heading__headline h1', { x: 260, y: 206, width: 896, height: 61 }, 4, 'warranty.title');
  await assertBox(page, '.f-heading__description', { x: 260, y: 289, width: 910, height: 62 }, 4, 'warranty.description');

  const warrantySection = await box(page, '.f-section--warranty-cards', 'warranty.section');
  assertClose(warrantySection.x, 0, 4, 'warranty.section.x');
  assertClose(warrantySection.y, 435, 4, 'warranty.section.y');
  assertClose(warrantySection.width, 1920, 4, 'warranty.section.width');
  assertClose(warrantySection.height, 524, 4, 'warranty.section.height');

  await assertBox(page, '.f-warranty-cards', { x: 260, y: 435, width: 1400, height: 524 }, 4, 'warranty.cardsGrid');
  await assertBox(page, '.f-warranty-labels', { x: 261, y: 674, width: 271, height: 225 }, 4, 'warranty.labels');
  await assertBox(page, '.f-warranty-card:nth-of-type(1)', { x: 450, y: 435, width: 217, height: 499 }, 4, 'warranty.cardOne');
  await assertBox(page, '.f-warranty-card:nth-of-type(2)', { x: 691, y: 435, width: 217, height: 499 }, 4, 'warranty.cardTwo');
  await assertBox(page, '.f-warranty-card:nth-of-type(3)', { x: 932, y: 435, width: 217, height: 499 }, 4, 'warranty.cardThree');
  await assertBox(page, '.f-warranty-card:nth-of-type(1) .f-warranty-card__media', { x: 479, y: 445, width: 159, height: 127 }, 4, 'warranty.cardMedia');
  await assertBox(page, '.f-warranty-card:nth-of-type(1) .f-warranty-card__items', { x: 423, y: 674, width: 271, height: 225 }, 8, 'warranty.cardValues');

  const warrantyNote = await box(page, '.f-warranty-note', 'warranty.note');
  assertClose(warrantyNote.x, 1224, 4, 'warranty.note.x');
  assertClose(warrantyNote.y, 679, 4, 'warranty.note.y');
  assertClose(warrantyNote.width, 368, 4, 'warranty.note.width');
  assertBetween(warrantyNote.height, 195, 220, 'warranty.note.height');

  const warrantyDescriptionToCardsGap = await verticalGap(page, '.f-heading__description', '.f-section--warranty-cards', 'warranty.descriptionToCardsGap');
  assertBetween(warrantyDescriptionToCardsGap, 60, 120, 'warranty.descriptionToCardsGap');

  const warrantySectionToContactGap = await verticalGap(page, '.f-section--warranty-cards', '.page-template-template-warranty .f-contact-cta', 'warranty.sectionToContactGap');
  assertBetween(warrantySectionToContactGap, 80, 180, 'warranty.sectionToContactGap');

  const warrantyContactCta = await box(page, '.page-template-template-warranty .f-contact-cta', 'warranty.contactCta');
  assertClose(warrantyContactCta.x, 260, 4, 'warranty.contactCta.x');
  assertBetween(warrantyContactCta.y, 1060, 1090, 'warranty.contactCta.y');
  assertClose(warrantyContactCta.width, 1400, 4, 'warranty.contactCta.width');
  assertClose(warrantyContactCta.height, 455, 4, 'warranty.contactCta.height');
  await assertFooterLayout(page, 'warranty', 1556);

  await page.goto(`${baseUrl}/kolik-stoji-udrzba/`, { waitUntil: 'load' });
  await assertBox(page, '.f-heading', { x: 0, y: 0, width: 1920, height: 581 }, 3, 'maintenance.heading');
  await assertBox(page, '.f-heading__headline h1', { x: 497, y: 206, width: 922, height: 61 }, 4, 'maintenance.title');
  await assertBox(page, '.f-heading__description', { x: 497, y: 289, width: 856, height: 217 }, 12, 'maintenance.description');

  const maintenanceSection = await box(page, '.f-section--figma-article', 'maintenance.articleSection');
  assertClose(maintenanceSection.x, 0, 4, 'maintenance.articleSection.x');
  assertClose(maintenanceSection.y, 581, 4, 'maintenance.articleSection.y');
  assertClose(maintenanceSection.width, 1920, 4, 'maintenance.articleSection.width');
  assertBetween(maintenanceSection.height, 2370, 2395, 'maintenance.articleSection.height');

  const maintenanceArticle = await box(page, '.f-main--maintenance .f-figma-article', 'maintenance.article');
  assertClose(maintenanceArticle.x, 497, 8, 'maintenance.article.x');
  assertClose(maintenanceArticle.y, 581, 4, 'maintenance.article.y');
  assertClose(maintenanceArticle.width, 927, 8, 'maintenance.article.width');
  assertBetween(maintenanceArticle.height, 2370, 2395, 'maintenance.article.height');

  await assertBox(page, '.f-main--maintenance .f-figma-article section:nth-of-type(1)', { x: 497, y: 581, width: 927, height: 1645 }, 8, 'maintenance.blockOne');
  await assertBox(page, '.f-main--maintenance .f-figma-article section:nth-of-type(1) h2', { x: 497, y: 581, width: 927, height: 51 }, 8, 'maintenance.blockOneTitle');
  await assertBox(page, '.f-main--maintenance .f-figma-article section:nth-of-type(1) p:first-of-type', { x: 497, y: 652, width: 927, height: 76 }, 8, 'maintenance.blockOneTextStart');
  await assertBox(page, '.f-main--maintenance .f-figma-article section:nth-of-type(2)', { x: 497, y: 2266, width: 927, height: 247 }, 8, 'maintenance.blockTwo');
  await assertBox(page, '.f-main--maintenance .f-figma-article section:nth-of-type(3)', { x: 497, y: 2554, width: 927, height: 172 }, 8, 'maintenance.blockThree');
  await assertBox(page, '.f-main--maintenance .f-figma-article section:nth-of-type(4)', { x: 497, y: 2766, width: 927, height: 197 }, 8, 'maintenance.blockFour');

  const maintenanceGapOne = await verticalGap(page, '.f-main--maintenance .f-figma-article section:nth-of-type(1)', '.f-main--maintenance .f-figma-article section:nth-of-type(2)', 'maintenance.blockOneToTwoGap');
  const maintenanceGapTwo = await verticalGap(page, '.f-main--maintenance .f-figma-article section:nth-of-type(2)', '.f-main--maintenance .f-figma-article section:nth-of-type(3)', 'maintenance.blockTwoToThreeGap');
  const maintenanceGapThree = await verticalGap(page, '.f-main--maintenance .f-figma-article section:nth-of-type(3)', '.f-main--maintenance .f-figma-article section:nth-of-type(4)', 'maintenance.blockThreeToFourGap');
  assertBetween(maintenanceGapOne, 24, 80, 'maintenance.blockOneToTwoGap');
  assertBetween(maintenanceGapTwo, 24, 80, 'maintenance.blockTwoToThreeGap');
  assertBetween(maintenanceGapThree, 24, 80, 'maintenance.blockThreeToFourGap');

  const maintenanceArticleToContactGap = await verticalGap(page, '.f-main--maintenance .f-figma-article', '.page-template-template-maintenance .f-contact-cta', 'maintenance.articleToContactGap');
  assertBetween(maintenanceArticleToContactGap, 95, 115, 'maintenance.articleToContactGap');

  const maintenanceContactCta = await box(page, '.page-template-template-maintenance .f-contact-cta', 'maintenance.contactCta');
  assertClose(maintenanceContactCta.x, 260, 4, 'maintenance.contactCta.x');
  assertClose(maintenanceContactCta.y, 3068, 8, 'maintenance.contactCta.y');
  assertClose(maintenanceContactCta.width, 1400, 4, 'maintenance.contactCta.width');
  assertClose(maintenanceContactCta.height, 455, 4, 'maintenance.contactCta.height');
  await assertFooterLayout(page, 'maintenance', 3551);

  await page.goto(`${baseUrl}/vlastnosti/izolace-virivky/`, { waitUntil: 'load' });
  await assertBox(page, '.f-heading', { x: 0, y: 0, width: 1920, height: 435 }, 3, 'featureDetail.heading');
  await assertBox(page, '.f-heading__headline h1', { x: 497, y: 206, width: 896, height: 61 }, 4, 'featureDetail.title');
  await assertBox(page, '.f-heading__description', { x: 497, y: 289, width: 856, height: 93 }, 4, 'featureDetail.description');
  await assertBox(page, '.f-figma-article--feature-detail', { x: 497, y: 435, width: 927, height: 2295 }, 4, 'featureDetail.article');
  await assertBox(page, '.f-figma-article__hero', { x: 497, y: 435, width: 927, height: 384 }, 4, 'featureDetail.heroImage');
  await assertBox(page, '.f-figma-article--feature-detail section:nth-of-type(1)', { x: 497, y: 859, width: 927, height: 477 }, 4, 'featureDetail.blockOne');
  await assertBox(page, '.f-figma-article__diagram', { x: 679.5, y: 1376, width: 562, height: 562 }, 4, 'featureDetail.diagram');
  await assertBox(page, '.f-figma-article--feature-detail section:nth-of-type(2)', { x: 497, y: 2037, width: 927, height: 246 }, 4, 'featureDetail.blockTwo');
  await assertBox(page, '.f-figma-article--feature-detail section:nth-of-type(3)', { x: 497, y: 2323, width: 927, height: 171 }, 4, 'featureDetail.blockThree');
  await assertBox(page, '.f-figma-article--feature-detail section:nth-of-type(4)', { x: 497, y: 2534, width: 927, height: 196 }, 4, 'featureDetail.blockFour');
  await assertBox(page, '.f-section--feature-related', { x: 0, y: 2840, width: 1920, height: 829 }, 4, 'featureDetail.relatedSection');
  await assertBox(page, '.f-section--feature-related h2', { x: 497, y: 2840, width: 927, height: 51 }, 4, 'featureDetail.relatedTitle');
  await assertBox(page, '.f-section--feature-related .f-figma-card-grid--features', { x: 260, y: 2921, width: 1400, height: 748 }, 4, 'featureDetail.relatedGrid');
  await assertBox(page, '.page-template-template-feature-detail .f-contact-cta', { x: 260, y: 3750, width: 1400, height: 455 }, 4, 'featureDetail.contactCta');
  await assertFooterLayout(page, 'featureDetail', 4233);
  await assertSourceContains(page, '.f-figma-article__hero', 'uploads/import/legacy-categories/virivky.jpg', 'featureDetail.heroSource');
  await assertSourceContains(page, '.f-figma-article__diagram', 'uploads/import/figma/feature-freeheat-diagram.png', 'featureDetail.diagramSource');
}

async function auditSupportDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/podpora/`, { waitUntil: 'load' });

  await assertBox(page, '.f-heading', { x: 0, y: 0, width: 1920, height: 394 }, 3, 'support.heading');
  await assertBox(page, '.f-heading__headline h1', { x: 260, y: 206, width: 896, height: 61 }, 4, 'support.title');
  await assertBox(page, '.f-heading__description', { x: 260, y: 289, width: 910, height: 62 }, 4, 'support.description');
  await assertBox(page, '.f-section--support-tabs', { x: 0, y: 394, width: 1920, height: 93 }, 3, 'support.tabsSection');
  await assertBox(page, '.f-support-tabs', { x: 260, y: 394, width: 1400, height: 93 }, 3, 'support.tabs');
  await assertBox(page, '.f-section--support-faq', { x: 0, y: 487, width: 1920, height: 1466 }, 4, 'support.faqSection');
  await assertBox(page, '.f-section--support-faq h2', { x: 260, y: 568, width: 1045, height: 51 }, 4, 'support.faqTitle');
  await assertBox(page, '.f-section--support-faq .f-chip-list', { x: 260, y: 642, width: 1045, height: 37 }, 4, 'support.faqChips');
  await assertBox(page, '.f-support-accordion', { x: 260, y: 724, width: 1045, height: 1145 }, 8, 'support.faqAccordion');
  await assertBox(page, '.f-support-faq-card:nth-child(1)', { x: 260, y: 724, width: 1045, height: 217 }, 8, 'support.faqCardOpen');
  await assertBox(page, '.f-support-faq-card:nth-child(2)', { x: 260, y: 961, width: 1045, height: 96 }, 4, 'support.faqCardClosed');
  await assertBox(page, '.f-support-help-card', { x: 1362, y: 556, width: 298, height: 341 }, 4, 'support.helpCard');
  await assertBox(page, '.f-section--support-downloads', { x: 0, y: 1953, width: 1920, height: 987 }, 4, 'support.downloadsSection');
  await assertBox(page, '.f-section--support-downloads h2', { x: 260, y: 1953, width: 1400, height: 51 }, 4, 'support.downloadsTitle');
  await assertBox(page, '.f-section--support-downloads .f-chip-list', { x: 260, y: 2027, width: 1400, height: 37 }, 4, 'support.downloadsChips');
  await assertBox(page, '.f-downloads--support-figma', { x: 260, y: 2109, width: 1045, height: 735 }, 8, 'support.downloadsList');
  await assertBox(page, '.f-download-group:nth-child(1)', { x: 260, y: 2109, width: 1045, height: 503 }, 4, 'support.downloadGroupOpen');
  await assertBox(page, '.f-download-card:nth-child(1)', { x: 344, y: 2202, width: 934, height: 118 }, 4, 'support.downloadCardOne');
  await assertComputedStyle(page, '.page-template-template-support .f-download-card:nth-child(1)', 'background-color', 'rgb(223, 231, 240)', 'support.downloadCardBackground');
  await assertDownloadGroupHeadersReadable(page, 'support.downloadGroupHeaders');

  const supportFormSection = await box(page, '.f-section--support-form', 'support.formSection');
  assertClose(supportFormSection.x, 0, 4, 'support.formSection.x');
  assertClose(supportFormSection.y, 2940, 4, 'support.formSection.y');
  assertClose(supportFormSection.width, 1920, 4, 'support.formSection.width');
  assertBetween(supportFormSection.height, 1080, 1500, 'support.formSection.height');

  const supportForm = await box(page, '.f-support-form', 'support.form');
  assertClose(supportForm.x, 260, 4, 'support.form.x');
  assertClose(supportForm.y, 2940, 4, 'support.form.y');
  assertClose(supportForm.width, 1045, 4, 'support.form.width');
  assertBetween(supportForm.height, 980, 1300, 'support.form.height');

  const supportFormIntro = await box(page, '.f-support-form header p', 'support.formIntro');
  assertClose(supportFormIntro.x, 260, 4, 'support.formIntro.x');
  assertBetween(supportFormIntro.y, 3000, 3070, 'support.formIntro.y');
  assertClose(supportFormIntro.width, 819, 8, 'support.formIntro.width');
  assertClose(supportFormIntro.height, 75, 4, 'support.formIntro.height');

  const supportFormCard = await box(page, '.f-support-form__card', 'support.formCard');
  assertClose(supportFormCard.x, 260, 4, 'support.formCard.x');
  assertBetween(supportFormCard.y, 3110, 3160, 'support.formCard.y');
  assertClose(supportFormCard.width, 1045, 4, 'support.formCard.width');
  assertBetween(supportFormCard.height, 760, 980, 'support.formCard.height');

  const supportNameField = await box(page, '.f-support-form__card label:nth-of-type(1)', 'support.formName');
  assertBetween(supportNameField.x, 340, 352, 'support.formName.x');
  assertBetween(supportNameField.y, 3170, 3230, 'support.formName.y');
  assertBetween(supportNameField.width, 840, 900, 'support.formName.width');
  assertClose(supportNameField.height, 113, 6, 'support.formName.height');

  const supportMessageField = await box(page, '.f-support-form__card label:nth-of-type(4)', 'support.formMessage');
  assertBetween(supportMessageField.x, 340, 352, 'support.formMessage.x');
  assertBetween(supportMessageField.y, 3560, 3640, 'support.formMessage.y');
  assertBetween(supportMessageField.width, 840, 900, 'support.formMessage.width');
  assertClose(supportMessageField.height, 211, 8, 'support.formMessage.height');

  await assertBox(page, '.f-support-form__card button', { x: 1033, y: 3880, width: 186, height: 50 }, 6, 'support.formButton');

  const supportIntroToCardGap = await verticalGap(page, '.f-support-form header p', '.f-support-form__card', 'support.formIntroToCardGap');
  assertBetween(supportIntroToCardGap, 24, 80, 'support.formIntroToCardGap');

  const supportFormToContactGap = await verticalGap(page, '.f-section--support-form', '.page-template-template-support .f-section--contact', 'support.formToContactGap');
  assertBetween(supportFormToContactGap, 120, 220, 'support.formToContactGap');

  const supportContactSection = await box(page, '.page-template-template-support .f-section--contact', 'support.contactSection');
  assertClose(supportContactSection.x, 0, 4, 'support.contactSection.x');
  assertBetween(supportContactSection.y, 4200, 4500, 'support.contactSection.y');
  assertClose(supportContactSection.width, 1920, 4, 'support.contactSection.width');
  assertClose(supportContactSection.height, 483, 4, 'support.contactSection.height');

  const supportContactCta = await box(page, '.page-template-template-support .f-contact-cta', 'support.contactCta');
  assertClose(supportContactCta.x, 260, 4, 'support.contactCta.x');
  assertBetween(supportContactCta.y, 4200, 4500, 'support.contactCta.y');
  assertClose(supportContactCta.width, 1400, 4, 'support.contactCta.width');
  assertClose(supportContactCta.height, 455, 4, 'support.contactCta.height');
  await assertFooterLayout(page, 'support');
}

async function auditInfoSupportCompact1097(page) {
  await page.setViewportSize({ width: 1097, height: 1000 });

  await page.goto(`${baseUrl}/zaruka/`, { waitUntil: 'load' });
  await assertNoHorizontalOverflow(page, 'pr7c:1097:warranty');
  const warrantyDescriptionToCardsGap = await verticalGap(page, '.f-heading__description', '.f-section--warranty-cards', 'pr7c:1097:warranty.descriptionToCardsGap');
  const warrantyCardToNoteGap = await verticalGap(page, '.f-warranty-card:nth-of-type(1)', '.f-warranty-note', 'pr7c:1097:warranty.cardToNoteGap');
  const warrantyNote = await box(page, '.f-warranty-note', 'pr7c:1097:warranty.note');
  assertBetween(warrantyDescriptionToCardsGap, 40, 90, 'pr7c:1097:warranty.descriptionToCardsGap');
  assertBetween(warrantyCardToNoteGap, 20, 36, 'pr7c:1097:warranty.cardToNoteGap');
  assertBetween(warrantyNote.width, 720, 790, 'pr7c:1097:warranty.note.width');

  await page.goto(`${baseUrl}/kolik-stoji-udrzba/`, { waitUntil: 'load' });
  await assertNoHorizontalOverflow(page, 'pr7c:1097:maintenance');
  const maintenanceArticle = await box(page, '.f-main--maintenance .f-figma-article', 'pr7c:1097:maintenance.article');
  assertBetween(maintenanceArticle.x, 20, 120, 'pr7c:1097:maintenance.article.x');
  assertBetween(maintenanceArticle.width, 860, 940, 'pr7c:1097:maintenance.article.width');
  const maintenanceDescriptionToSectionGap = await verticalGap(page, '.f-heading__description', '.f-section--figma-article', 'pr7c:1097:maintenance.descriptionToSectionGap');
  const maintenanceBlockOneToTwoGap = await verticalGap(page, '.f-main--maintenance .f-figma-article section:nth-of-type(1)', '.f-main--maintenance .f-figma-article section:nth-of-type(2)', 'pr7c:1097:maintenance.blockOneToTwoGap');
  assertBetween(maintenanceDescriptionToSectionGap, 60, 120, 'pr7c:1097:maintenance.descriptionToSectionGap');
  assertBetween(maintenanceBlockOneToTwoGap, 24, 80, 'pr7c:1097:maintenance.blockOneToTwoGap');

  await page.goto(`${baseUrl}/podpora/`, { waitUntil: 'load' });
  await assertNoHorizontalOverflow(page, 'pr7c:1097:support');
  const supportFaqToDownloadsGap = await verticalGap(page, '.f-section--support-faq', '.f-section--support-downloads', 'pr7c:1097:support.faqToDownloadsGap');
  const supportDownloadsToFormGap = await verticalGap(page, '.f-section--support-downloads', '.f-section--support-form', 'pr7c:1097:support.downloadsToFormGap');
  const supportFormToContactGap = await verticalGap(page, '.f-section--support-form', '.page-template-template-support .f-section--contact', 'pr7c:1097:support.formToContactGap');
  assertBetween(supportFaqToDownloadsGap, -2, 2, 'pr7c:1097:support.faqToDownloadsGap');
  assertBetween(supportDownloadsToFormGap, -2, 2, 'pr7c:1097:support.downloadsToFormGap');
  assertBetween(supportFormToContactGap, 120, 220, 'pr7c:1097:support.formToContactGap');
}

async function auditPr7EQualityGuards(page) {
  await page.setViewportSize({ width: 1440, height: 1100 });
  await page.goto(`${baseUrl}/podpora/`, { waitUntil: 'networkidle' });
  await assertInteractiveToggleContract(page, '[data-support-faq-card]:nth-child(2)', 'pr7e.support.faqToggle');
  await page.goto(`${baseUrl}/podpora/`, { waitUntil: 'networkidle' });
  await assertInteractiveTabs(page, '[data-support-filter]', 'pr7e.support.filters');

  await page.goto(`${baseUrl}/ke-stazeni/`, { waitUntil: 'networkidle' });
  await assertInteractiveToggleContract(page, '[data-download-group]:nth-child(2) [data-download-group-toggle]', 'pr7e.downloads.groupToggle');
  await page.goto(`${baseUrl}/ke-stazeni/`, { waitUntil: 'networkidle' });
  await assertInteractiveTabs(page, '[data-download-filter]', 'pr7e.downloads.filters');

  for (const width of [1920, 1097]) {
    for (const path of ['/zaruka/', '/kolik-stoji-udrzba/', '/podpora/', '/ke-stazeni/', '/sluzby/', '/reference/']) {
      await assertMajorSectionGapLimit(page, path, width, width >= 1400 ? 260 : 300, `pr7e.blankGap:${width}:${path}`);
    }
  }
}

async function auditPr7FComposition(page) {
  for (const width of [1920, 1600, 1478, 1366, 1097]) {
    for (const path of ['/virivky/', '/swimspa/']) {
      await page.setViewportSize({ width, height: 1100 });
      await page.goto(`${baseUrl}${path}`, { waitUntil: 'load' });
      await assertNoHorizontalOverflow(page, `pr7f.seriesNav:${width}:${path}`);
      await assertSeriesNavNoOverlap(page, '.f-series-nav', `pr7f.seriesNav:${width}:${path}`);
    }

    await page.setViewportSize({ width, height: 1100 });
    await page.goto(`${baseUrl}/podpora/`, { waitUntil: 'load' });
    await assertNoHorizontalOverflow(page, `pr7f.support:${width}`);

    const supportContainer = await box(page, '.page-template-template-support .f-section--support-faq .f-section__container', `pr7f.support:${width}.container`);
    const supportMain = await box(page, '.page-template-template-support .f-support-layout__main', `pr7f.support:${width}.main`);
    const supportHelp = await box(page, '.page-template-template-support .f-support-help-card', `pr7f.support:${width}.help`);
    const supportFaqCard = await box(page, '.page-template-template-support .f-support-faq-card:nth-child(1)', `pr7f.support:${width}.faqCard`);

    if (supportHelp.right > supportContainer.right + 2) {
      throw new Error(`pr7f.support:${width}: help card exceeds support container`);
    }

    if (supportMain.right > supportHelp.x - 16) {
      throw new Error(`pr7f.support:${width}: support main column overlaps help card`);
    }

    if (supportFaqCard.right > supportMain.right + 2) {
      throw new Error(`pr7f.support:${width}: FAQ card exceeds main column`);
    }

    assertBetween(supportMain.width, width >= 1400 ? 1000 : 620, 1050, `pr7f.support:${width}.main.width`);
    assertBetween(supportHelp.width, 260, 300, `pr7f.support:${width}.help.width`);

    await assertDownloadGroupHeadersReadable(page, `pr7f.supportDownloadHeaders:${width}`);
    await assertDownloadRowsAttached(page, `pr7f.supportDownloads:${width}`);

    await page.goto(`${baseUrl}/ke-stazeni/`, { waitUntil: 'load' });
    await assertNoHorizontalOverflow(page, `pr7f.downloads:${width}`);
    await assertDownloadGroupHeadersReadable(page, `pr7f.downloadHeaders:${width}`);
    await assertDownloadRowsAttached(page, `pr7f.downloads:${width}`);
  }
}

async function auditReferenceDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/reference/`, { waitUntil: 'load' });

  await assertBox(page, '.f-heading', { x: 0, y: 0, width: 1920, height: 312 }, 3, 'reference.heading');
  await assertBox(page, '.f-heading__headline h1', { x: 260, y: 206, width: 896, height: 61 }, 4, 'reference.title');
  await assertBox(page, '.f-section--references-figma', { x: 0, y: 312, width: 1920, height: 1016 }, 4, 'reference.section');
  await assertBox(page, '.f-reference-grid', { x: 260, y: 312, width: 1400, height: 1016 }, 4, 'reference.grid');
  await assertBox(page, '.f-reference-card:nth-child(1)', { x: 260, y: 312, width: 438, height: 320 }, 4, 'reference.cardOne');
  await assertBox(page, '.f-reference-card:nth-child(4)', { x: 260, y: 660, width: 438, height: 320 }, 4, 'reference.cardFour');
  await assertBox(page, '.f-reference-card:nth-child(9)', { x: 1226, y: 1008, width: 438, height: 320 }, 4, 'reference.cardNine');
  await assertBox(page, '.page-template-template-references .f-section--contact', { x: 0, y: 1411, width: 1920, height: 483 }, 4, 'reference.contactSection');
  await assertBox(page, '.page-template-template-references .f-contact-cta', { x: 260, y: 1411, width: 1400, height: 455 }, 4, 'reference.contactCta');
  await assertFooterLayout(page, 'reference', 1894);
}

async function auditAboutDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/o-nas/`, { waitUntil: 'load' });

  await assertBox(page, '.f-heading', { x: 0, y: 0, width: 1920, height: 441 }, 3, 'about.heading');
  await assertBox(page, '.f-heading__headline h1', { x: 260, y: 206, width: 896, height: 61 }, 4, 'about.title');
  await assertBox(page, '.f-heading__description', { x: 260, y: 289, width: 910, height: 62 }, 4, 'about.description');
  await assertBox(page, '.f-main--about-figma', { x: 0, y: 441, width: 1920, height: 2772 }, 4, 'about.main');
  await assertBox(page, '.f-about-figma__intro h2', { x: 260, y: 591, width: 815, height: 51 }, 4, 'about.introTitle');
  await assertBox(page, '.f-about-figma__stats', { x: 260, y: 1122, width: 1040, height: 119 }, 4, 'about.stats');
  await assertBox(page, '.f-about-figma__stats > div:nth-child(1) strong', { x: 260, y: 1122, width: 171, height: 51 }, 4, 'about.statOneValue');
  await assertBox(page, '.f-about-figma__stats > div:nth-child(2) strong', { x: 614, y: 1122, width: 337, height: 51 }, 4, 'about.statTwoValue');
  await assertBox(page, '.f-about-figma__stats > div:nth-child(3) strong', { x: 1207, y: 1122, width: 79, height: 51 }, 4, 'about.statThreeValue');
  await assertBox(page, '.f-about-figma__team-copy h2', { x: 260, y: 1375, width: 815, height: 51 }, 4, 'about.teamTitle');
  await assertBox(page, '.f-about-figma__team', { x: 260, y: 1658, width: 1407, height: 461.2 }, 8, 'about.teamGrid');
  await assertBox(page, '.f-about-person:nth-child(1) .f-about-person__media', { x: 260, y: 1658, width: 336, height: 335 }, 4, 'about.teamImageOne');
  await assertBox(page, '.f-about-person:nth-child(4) .f-about-person__media', { x: 1331, y: 1658, width: 336, height: 335 }, 8, 'about.teamImageFour');
  await assertBox(page, '.f-about-figma__career h2', { x: 260, y: 2278, width: 815, height: 51 }, 4, 'about.careerTitle');
  await assertBox(page, '.f-about-figma__jobs', { x: 260, y: 2457, width: 1401, height: 642 }, 4, 'about.jobs');
  await assertBox(page, '.f-about-job:nth-child(1)', { x: 260, y: 2457, width: 1401, height: 526 }, 4, 'about.jobOpen');
  await assertBox(page, '.f-about-job:nth-child(2)', { x: 260, y: 3003, width: 1401, height: 96 }, 4, 'about.jobClosed');
  await assertBox(page, '.page-template-template-about .f-section--contact', { x: 0, y: 3328, width: 1920, height: 483 }, 4, 'about.contactSection');
  await assertBox(page, '.page-template-template-about .f-contact-cta', { x: 260, y: 3328, width: 1400, height: 455 }, 4, 'about.contactCta');
  await assertFooterLayout(page, 'about', 3811);
}

async function auditServiceRequestDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/servis/`, { waitUntil: 'load' });

  await assertBox(page, '.f-heading', { x: 0, y: 0, width: 1920, height: 519 }, 3, 'serviceRequest.heading');
  await assertBox(page, '.f-heading__headline h1', { x: 497, y: 206, width: 922, height: 122 }, 4, 'serviceRequest.title');
  await assertBox(page, '.f-heading__description', { x: 497, y: 288, width: 856, height: 186 }, 4, 'serviceRequest.description');
  await assertBox(page, '.f-section--service-request', { x: 0, y: 519, width: 1920, height: 928 }, 4, 'serviceRequest.section');
  await assertBox(page, '.f-service-request__form', { x: 497, y: 519, width: 926, height: 674 }, 4, 'serviceRequest.formCard');
  await assertBox(page, '.f-service-request__form form', { x: 583, y: 578, width: 766, height: 615 }, 8, 'serviceRequest.form');
  await assertBox(page, '.f-service-request__form :is(input, textarea)', { x: 583, y: 617.3, width: 766, height: 50 }, 8, 'serviceRequest.firstInput');
  await assertBox(page, '.f-service-request__form textarea', { x: 583, y: 927, width: 766, height: 146 }, 8, 'serviceRequest.message');
  await assertBox(page, '.f-service-request__form .f-form__note', { x: 583, y: 1083, width: 437, height: 25 }, 4, 'serviceRequest.consent');
  await assertBox(page, '.f-service-request__form .f-form--submit', { x: 1167, y: 1071, width: 186, height: 50 }, 4, 'serviceRequest.submit');
  await assertBox(page, '.f-service-request__pricing--warranty h2', { x: 497, y: 1262, width: 402, height: 51 }, 4, 'serviceRequest.warrantyTitle');
  await assertBox(page, '.f-service-request__pricing--warranty p, .f-service-request__pricing--warranty ul', { x: 497, y: 1335, width: 402, height: 87 }, 8, 'serviceRequest.warrantyText');
  await assertBox(page, '.f-service-request__pricing--paid h2', { x: 969, y: 1262, width: 676, height: 51 }, 4, 'serviceRequest.paidTitle');
  await assertBox(page, '.f-service-request__pricing--paid p, .f-service-request__pricing--paid ul', { x: 969, y: 1335, width: 676, height: 116 }, 8, 'serviceRequest.paidText');
  await assertBox(page, '.page-template-template-service-request .f-section--contact', { x: 0, y: 1552, width: 1920, height: 483 }, 4, 'serviceRequest.contactSection');
  await assertBox(page, '.page-template-template-service-request .f-contact-cta', { x: 260, y: 1552, width: 1400, height: 455 }, 4, 'serviceRequest.contactCta');
  await assertFooterLayout(page, 'serviceRequest', 2035);
}

async function auditContactDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });
  await page.goto(`${baseUrl}/kontakt/`, { waitUntil: 'load' });

  await assertBox(page, '.f-heading', { x: 0, y: 0, width: 1920, height: 430 }, 2, 'contact.heading');
  await assertBox(page, '.f-heading__headline', { x: 260, y: 206, width: 672, height: 122 }, 3, 'contact.headingHeadline');
  await assertBox(page, '.f-heading__contacts', { x: 970, y: 220, width: 360, height: 118 }, 3, 'contact.headingContacts');
  await assertBox(page, '.f-heading__buttons', { x: 1424, y: 211, width: 235, height: 126 }, 3, 'contact.headingButtons');
  await assertBox(page, '.f-section--map', { x: 0, y: 430, width: 1920, height: 782 }, 2, 'contact.mapSection');
  await assertBox(page, '.f-local-map__image', { x: -867, y: 430, width: 3110, height: 782 }, 3, 'contact.mapImage');
  await assertBox(page, '.f-local-map__card', { x: 260, y: 561, width: 565, height: 491 }, 3, 'contact.mapCard');
  await assertOffsetBox(page, '.f-local-map__pin', { x: 1226, y: 786, width: 42, height: 42 }, 2, 'contact.mapPin');
  await assertBox(page, '.f-contact-card:nth-child(1)', { x: 260, y: 1399, width: 453, height: 280 }, 3, 'contact.cardOne');
  await assertBox(page, '.f-contact-card:nth-child(2)', { x: 733, y: 1399, width: 453, height: 280 }, 3, 'contact.cardTwo');
  await assertBox(page, '.f-contact-card:nth-child(3)', { x: 1206, y: 1399, width: 453, height: 280 }, 3, 'contact.cardThree');
  await assertBox(page, '.f-contact-card:nth-child(4)', { x: 260, y: 1704, width: 453, height: 280 }, 3, 'contact.cardFour');
  await assertBox(page, '.f-contact-card:nth-child(5)', { x: 733, y: 1704, width: 453, height: 280 }, 3, 'contact.cardFive');
  await assertBox(page, '.f-contact-card:nth-child(6)', { x: 1206, y: 1704, width: 453, height: 280 }, 3, 'contact.cardSix');
  const contactCardCount = await page.locator('.f-contact-card').count();
  if (contactCardCount !== 6) {
    throw new Error(`contact.cards: expected 6 contact cards, got ${contactCardCount}`);
  }
  await assertBox(page, '.f-billing-box', { x: 260, y: 2071, width: 507, height: 310 }, 3, 'contact.billing');
  await assertFooterLayout(page, 'contact', 2425);

  await assertSourceContains(page, '.f-local-map__image', 'uploads/import/figma/contact-map-showroom.png', 'contact.mapSource');
  const contactAvatarText = (await page.locator('.f-contact-card:nth-child(1) .f-contact-card__avatar').innerText()).trim();
  if (contactAvatarText !== 'VZ') {
    throw new Error(`contact.cardAvatarInitials: expected VZ, got ${contactAvatarText}`);
  }
}

async function auditSharedFooterDesktop(page) {
  await page.setViewportSize({ width: 1920, height: 1080 });

  for (const path of [
    '/swimspa/',
    '/podpora/',
    '/showroom/',
    '/reference/',
    '/o-nas/',
    '/servis/',
    '/sluzby/',
    '/vlastnosti/',
    '/certifikaty/',
    '/zaruka/',
    '/kolik-stoji-udrzba/',
  ]) {
    await page.goto(`${baseUrl}${path}`, { waitUntil: 'load' });
    await assertFooterLayout(page, `sharedFooter:${path}`);
  }
}

(async () => {
  const browser = await chromium.launch({ executablePath: chromePath });
  const page = await browser.newPage({ deviceScaleFactor: 1 });

  try {
    await auditDesktop(page);
    await auditFigmaTokenAndComponentStyles(page);
    await auditDesktopScaledMatrix(page);
    await auditMobile(page);
    await auditNarrowHomepageLayout(page);
    await auditResponsiveShell(page);
    await auditCompactNavigation(page);
    await auditDesktopHeaderStates(page);
    await auditDesktopHeaderRealViewport(page);
    await auditZoomOutFullBleed(page);
    await auditCompactLaptopLayout(page);
    await auditScaledLaptopBoundary(page);
    await auditFigmaSources(page);
    await auditPhase5BHardening(page);
    await auditCatalogHotTubsDesktop(page);
    await auditCatalogSwimspaDesktop(page);
    await auditTimberwolfDesktop(page);
    await auditBenefitPopupDesktop(page);
    await auditShowroomDesktop(page);
    await auditFigmaInfoPagesDesktop(page);
    await auditSupportDesktop(page);
    await auditInfoSupportCompact1097(page);
    await auditPr7EQualityGuards(page);
    await auditPr7FComposition(page);
    await auditReferenceDesktop(page);
    await auditAboutDesktop(page);
    await auditServiceRequestDesktop(page);
    await auditContactDesktop(page);
    await auditSharedFooterDesktop(page);
    console.log('Figma visual audit passed.');
  } finally {
    await browser.close();
  }
})();


