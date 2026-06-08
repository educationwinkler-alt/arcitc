const { readFileSync } = require('fs');
const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

async function fetchHtml(path) {
  const response = await fetch(`${baseUrl}${path}`);

  if (!response.ok) {
    throw new Error(`${path} returned ${response.status}`);
  }

  return response.text();
}

function assertIncludes(label, text, needles) {
  for (const needle of needles) {
    if (!text.includes(needle)) {
      throw new Error(`${label} is missing expected marker: ${needle}`);
    }
  }
}

function countMatches(text, regex) {
  return (text.match(regex) || []).length;
}

function assertCountAtLeast(label, text, regex, expected) {
  const count = countMatches(text, regex);

  if (count < expected) {
    throw new Error(`${label} expected at least ${expected} matches, got ${count}`);
  }
}

function assertNotIncludes(label, text, needles) {
  for (const needle of needles) {
    if (text.includes(needle)) {
      throw new Error(`${label} contains stale marker: ${needle}`);
    }
  }
}

async function assertNoHorizontalOverflow(page, label) {
  const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);

  if (overflow > 2) {
    throw new Error(`${label} has horizontal overflow of ${overflow}px`);
  }
}

async function assertInsideViewport(page, selector, label, gutter = 0) {
  const box = await page.locator(selector).first().boundingBox();
  const viewport = page.viewportSize();

  if (!box || !viewport) {
    throw new Error(`${label} is missing or has no layout box`);
  }

  if (box.x < -gutter || box.x + box.width > viewport.width + gutter) {
    throw new Error(`${label} exceeds viewport: x=${Math.round(box.x)}, width=${Math.round(box.width)}, viewport=${viewport.width}`);
  }
}

async function assertFaqInteraction(page) {
  await page.goto(`${baseUrl}/podpora/`, { waitUntil: 'load' });

  const faq = page.locator('[data-support-faq-card]').nth(1);
  const toggle = faq.locator('[data-support-faq-card-toggle]');
  const panelId = await toggle.getAttribute('aria-controls');

  if (!panelId) {
    throw new Error('support FAQ toggle is missing aria-controls');
  }

  await toggle.click();

  const expanded = await toggle.getAttribute('aria-expanded');
  const panelHidden = await page.locator(`#${panelId}`).getAttribute('hidden');

  if (expanded !== 'true' || panelHidden !== null) {
    throw new Error('support FAQ toggle did not open with synchronized ARIA/panel state');
  }
}

async function assertDownloadInteraction(page) {
  await page.goto(`${baseUrl}/ke-stazeni/`, { waitUntil: 'load' });

  const group = page.locator('[data-download-group]').nth(1);
  const toggle = group.locator('[data-download-group-toggle]');
  const panelId = await toggle.getAttribute('aria-controls');

  if (!panelId) {
    throw new Error('download group toggle is missing aria-controls');
  }

  await toggle.click();

  const expanded = await toggle.getAttribute('aria-expanded');
  const panelHidden = await page.locator(`#${panelId}`).getAttribute('hidden');
  const isOpen = await group.evaluate((element) => element.classList.contains('is-open'));

  if (expanded !== 'true' || panelHidden !== null || !isOpen) {
    throw new Error('download group did not open with synchronized ARIA/panel state');
  }

  const manualFilter = page.locator('[data-download-filter="manual"]').first();
  await manualFilter.click();

  const pressed = await manualFilter.getAttribute('aria-pressed');
  const active = await manualFilter.evaluate((element) => element.classList.contains('is-active'));

  if (pressed !== 'true' || !active) {
    throw new Error('download filter chip did not expose active button state');
  }
}

async function assertDownloadRowsAttached(page) {
  await page.setViewportSize({ width: 1440, height: 1100 });
  await page.goto(`${baseUrl}/ke-stazeni/`, { waitUntil: 'load' });

  const rows = await page.locator('.f-download-card--contract').evaluateAll((cards) => cards.map((card) => {
    const row = card.getBoundingClientRect();
    const body = card.querySelector('.f-download-card__body')?.getBoundingClientRect();
    const button = card.querySelector('.f-download-card__button')?.getBoundingClientRect();

    return {
      row: { x: row.x, right: row.right, width: row.width, height: row.height },
      body: body ? { x: body.x, right: body.right, width: body.width } : null,
      button: button ? { x: button.x, right: button.right, width: button.width } : null,
    };
  }));

  const visibleRows = rows.filter((row) => row.row.width > 1 && row.row.height > 1).slice(0, 3);

  if (!visibleRows.length) {
    throw new Error('download card contract did not render visible rows');
  }

  for (const [index, row] of visibleRows.entries()) {
    if (!row.body || !row.button) {
      throw new Error(`download row ${index} is missing body or CTA`);
    }

    if (row.button.x < row.body.right + 8) {
      throw new Error(`download row ${index} CTA overlaps body`);
    }

    if (row.button.right > row.row.right - 12) {
      throw new Error(`download row ${index} CTA exceeds row padding`);
    }
  }
}

async function assertMobileHomepageAndMenu(page) {
  await page.setViewportSize({ width: 390, height: 900 });
  await page.goto(baseUrl, { waitUntil: 'networkidle' });
  await assertNoHorizontalOverflow(page, 'mobile homepage');

  for (const [selector, label] of [
    ['.template--homepage .f-section--slides', 'mobile homepage hero'],
    ['.template--homepage .f-hero-promo', 'mobile homepage promo'],
    ['.template--homepage .f-category:nth-child(1)', 'mobile hot tub category card'],
    ['.template--homepage .f-category:nth-child(2)', 'mobile swimspa category card'],
    ['.template--homepage .f-showroom-panel', 'mobile showroom panel'],
    ['.template--homepage .f-contact-cta', 'mobile final CTA'],
    ['.f-footer--arctic', 'mobile footer'],
  ]) {
    await assertInsideViewport(page, selector, label, 2);
  }

  await page.locator('.f-navigation__trigger').first().click();
  await page.waitForTimeout(250);
  await assertNoHorizontalOverflow(page, 'mobile menu open');
  await assertInsideViewport(page, '.f-off--navigation.active', 'mobile menu panel', 2);

  const active = await page.locator('.f-off--navigation.active').count();
  if (!active) {
    throw new Error('mobile menu did not open');
  }

  const menuState = await page.locator('.f-off--navigation.active').evaluate((element) => {
    const visibleSubmenus = [...element.querySelectorAll('.f-navigation-sub, .sub-menu')].filter((submenu) => {
      const style = getComputedStyle(submenu);
      const rect = submenu.getBoundingClientRect();
      return style.display !== 'none' && rect.width > 1 && rect.height > 1;
    }).length;

    const visibleLinks = [...element.querySelectorAll('a')]
      .filter((link) => link.offsetWidth > 1 || link.offsetHeight > 1 || link.getClientRects().length > 0)
      .map((link) => link.textContent.replace(/\s+/g, ' ').trim())
      .filter(Boolean);

    return {
      overflowY: getComputedStyle(element).overflowY,
      scrollHeight: element.scrollHeight,
      clientHeight: element.clientHeight,
      visibleSubmenus,
      visibleLinks,
    };
  });

  if (menuState.visibleSubmenus < 4) {
    throw new Error(`mobile menu expected visible submenu groups, got ${menuState.visibleSubmenus}`);
  }

  if (menuState.scrollHeight <= menuState.clientHeight || menuState.overflowY === 'hidden') {
    throw new Error('mobile menu is not scrollable after exposing submenu content');
  }

  for (const label of ['Série Core', 'Série Classic', 'Série Custom', 'Bazény ARCTIC Classic', 'Bazény ARCTIC Custom', 'Termokryt']) {
    if (!menuState.visibleLinks.includes(label)) {
      throw new Error(`mobile menu is missing visible submenu link: ${label}`);
    }
  }
}

async function assertMobileSupportDownloads(page) {
  for (const path of ['/podpora/', '/ke-stazeni/']) {
    await page.setViewportSize({ width: 390, height: 900 });
    await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle' });
    await assertNoHorizontalOverflow(page, `mobile ${path}`);
    await assertInsideViewport(page, '.f-main--support-contract', `mobile ${path} support contract`, 2);
  }
}

async function assertHtmlContracts() {
  const support = await fetchHtml('/podpora/');
  const downloads = await fetchHtml('/ke-stazeni/');

  assertIncludes('/podpora/', support, [
    'support-download-interactions.js',
    'f-main--support-contract',
    'f-support-tabs--contract',
    'f-chip-list--contract',
    'f-support-faq-card--contract',
    'f-downloads--contract',
    'f-download-card--contract',
    'f-support-form--contract',
    'data-support-faq-card',
    'data-download-group-toggle',
  ]);

  assertIncludes('/ke-stazeni/', downloads, [
    'support-download-interactions.js',
    'f-main--support-contract',
    'f-chip-list--contract',
    'f-downloads--contract',
    'f-download-group--contract',
    'f-download-card--contract',
    'data-download-filter-type',
  ]);

  const supportTemplate = readFileSync('wp-content/themes/arctic/template-support.php', 'utf8');
  const downloadsTemplate = readFileSync('wp-content/themes/arctic/template-downloads.php', 'utf8');

  assertNotIncludes('template-support.php', supportTemplate, [
    'contact-tomas-koutny.jpg',
    '$download_filter_keys',
  ]);
  assertNotIncludes('template-downloads.php', downloadsTemplate, [
    '$download_filter_keys',
  ]);

  assertCountAtLeast('/podpora/ FAQ cards', support, /f-support-faq-card--contract/g, 3);
  assertCountAtLeast('/ke-stazeni/ download groups', downloads, /f-download-group--contract/g, 3);
  assertCountAtLeast('/ke-stazeni/ download cards', downloads, /f-download-card--contract/g, 3);

  const contractsCss = readFileSync('wp-content/themes/arctic/src/less/_component-contracts.less', 'utf8');
  assertIncludes('_component-contracts.less', contractsCss, [
    '.f-main--support-contract',
    '.f-downloads--contract',
    '.f-support-faq-card--contract',
    '.f-download-card--contract',
    '.f-off--navigation',
  ]);
}

(async () => {
  await assertHtmlContracts();

  const browser = await chromium.launch({ executablePath: chromePath });

  try {
    const page = await browser.newPage({ deviceScaleFactor: 1 });
    await assertFaqInteraction(page);
    await assertDownloadInteraction(page);
    await assertDownloadRowsAttached(page);
    await assertMobileHomepageAndMenu(page);
    await assertMobileSupportDownloads(page);
  } finally {
    await browser.close();
  }

  console.log('Support/download/mobile smoke passed.');
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
