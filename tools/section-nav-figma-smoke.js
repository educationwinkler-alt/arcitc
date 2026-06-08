const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

function assertClose(actual, expected, tolerance, label) {
  assert(Math.abs(actual - expected) <= tolerance, `${label}: expected ${expected} +/- ${tolerance}, got ${Math.round(actual * 10) / 10}`);
}

function isVisibleLineTransform(transform) {
  return transform === 'none' || !transform.startsWith('matrix(0,');
}

function isHiddenPseudoLine(style) {
  return style.content === 'none' || style.display === 'none';
}

async function readState(page, containerSelector, linkSelector, index = 0) {
  return page.evaluate(({ containerSelector, linkSelector, index }) => {
    const rect = (element) => {
      const box = element.getBoundingClientRect();

      return {
        x: box.x,
        y: box.y,
        width: box.width,
        height: box.height,
      };
    };
    const container = document.querySelector(containerSelector);
    const link = document.querySelectorAll(linkSelector)[index];
    const containerStyle = getComputedStyle(container);
    const linkStyle = getComputedStyle(link);
    const beforeLineStyle = getComputedStyle(link, '::before');
    const lineStyle = getComputedStyle(link, '::after');

    return {
      container: rect(container),
      containerRadius: containerStyle.borderRadius,
      link: rect(link),
      linkColor: linkStyle.color,
      linkFontSize: linkStyle.fontSize,
      linkLineHeight: linkStyle.lineHeight,
      linkMargin: linkStyle.margin,
      linkPadding: linkStyle.padding,
      linkBorderBottomWidth: linkStyle.borderBottomWidth,
      linkTextDecorationLine: linkStyle.textDecorationLine,
      beforeLineContent: beforeLineStyle.content,
      beforeLineDisplay: beforeLineStyle.display,
      lineBottom: lineStyle.bottom,
      lineHeight: lineStyle.height,
      lineTransform: lineStyle.transform,
      lineWidth: parseFloat(lineStyle.width),
    };
  }, { containerSelector, linkSelector, index });
}

async function assertSecondaryNav(page, config) {
  await page.goto(`${baseUrl}${config.path}`, { waitUntil: 'networkidle', timeout: 90000 });

  const state = await readState(page, config.containerSelector, config.linkSelector);

  assertClose(state.container.x, config.container.x, 2, `${config.label}.container.x`);
  assertClose(state.container.y, config.container.y, 2, `${config.label}.container.y`);
  assertClose(state.container.width, 1400, 2, `${config.label}.container.width`);
  assertClose(state.container.height, 93, 1, `${config.label}.container.height`);
  assert(state.containerRadius === '40px', `${config.label}.container.radius expected 40px, got ${state.containerRadius}`);
  assertClose(state.link.x, 313, 2, `${config.label}.firstLink.x`);
  assertClose(state.link.y, config.firstLinkY, 2, `${config.label}.firstLink.y`);
  if (config.firstLinkWidth) {
    assertClose(state.link.width, config.firstLinkWidth, 1, `${config.label}.firstLink.width`);
  }
  assertClose(state.link.height, 51, 1, `${config.label}.firstLink.height`);
  assert(state.linkFontSize === '18px', `${config.label}.firstLink.fontSize expected 18px, got ${state.linkFontSize}`);
  assert(state.linkLineHeight === '51px', `${config.label}.firstLink.lineHeight expected 51px, got ${state.linkLineHeight}`);
  assert(state.linkMargin === '0px', `${config.label}.firstLink.margin expected 0px, got ${state.linkMargin}`);
  assert(state.linkPadding === '0px', `${config.label}.firstLink.padding expected 0px, got ${state.linkPadding}`);
  assert(state.linkBorderBottomWidth === '0px', `${config.label}.firstLink.borderBottomWidth expected 0px, got ${state.linkBorderBottomWidth}`);
  assert(state.linkTextDecorationLine === 'none', `${config.label}.firstLink.textDecoration expected none, got ${state.linkTextDecorationLine}`);
  if (config.expectNoBeforeLine) {
    assert(isHiddenPseudoLine({ content: state.beforeLineContent, display: state.beforeLineDisplay }), `${config.label}.firstLink.beforeLine expected hidden, got content ${state.beforeLineContent}, display ${state.beforeLineDisplay}`);
  }
  assert(state.lineBottom === '-20px', `${config.label}.activeLine.bottom expected -20px, got ${state.lineBottom}`);
  assert(state.lineHeight === '1px', `${config.label}.activeLine.height expected 1px, got ${state.lineHeight}`);
  assertClose(state.lineWidth, state.link.width, 0.5, `${config.label}.activeLine.width`);
  assert(isVisibleLineTransform(state.lineTransform), `${config.label}.activeLine should be visible for active link, got ${state.lineTransform}`);

  const secondLink = page.locator(config.linkSelector).nth(1);
  await secondLink.hover();
  await page.waitForTimeout(220);

  const hoverState = await readState(page, config.containerSelector, config.linkSelector, 1);
  assert(hoverState.linkColor === 'rgb(163, 31, 55)', `${config.label}.hover.color expected Figma red, got ${hoverState.linkColor}`);
  assert(isVisibleLineTransform(hoverState.lineTransform), `${config.label}.hover.line should be visible, got ${hoverState.lineTransform}`);
  assertClose(hoverState.lineWidth, hoverState.link.width, 0.5, `${config.label}.hover.lineWidth`);
}

async function main() {
  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, deviceScaleFactor: 1 });

  try {
    await assertSecondaryNav(page, {
      label: 'product',
      path: '/product/timberwolf/',
      containerSelector: '.f-links--product .f-links__container',
      linkSelector: '.f-links--product .f-links__navigation a',
      container: { x: 260, y: 749 },
      firstLinkY: 771,
      expectNoBeforeLine: true,
    });

    await assertSecondaryNav(page, {
      label: 'about',
      path: '/o-nas/',
      containerSelector: '.f-links--about .f-links__container',
      linkSelector: '.f-links--about .f-links__navigation a',
      container: { x: 260, y: 441 },
      firstLinkY: 463,
      firstLinkWidth: 141,
      expectNoBeforeLine: true,
    });

    await assertSecondaryNav(page, {
      label: 'support',
      path: '/podpora/',
      containerSelector: '.f-section--support-tabs .f-support-tabs',
      linkSelector: '.f-support-tabs a',
      container: { x: 260, y: 394 },
      firstLinkY: 416,
    });

    console.log('Section nav Figma smoke passed.');
  } finally {
    await browser.close();
  }
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
