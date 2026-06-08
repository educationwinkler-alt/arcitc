const fs = require('fs');
const path = require('path');
const { chromium } = require('playwright-core');

const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const outputDir = path.join('docs', 'screenshots', 'pricing-catalog-audit-2026-06-08');

const targets = [
  { id: 'local', baseUrl: process.env.ARCTIC_LOCAL_BASE_URL || 'http://localhost:8090' },
  { id: 'prod', baseUrl: process.env.ARCTIC_PROD_BASE_URL || 'https://illuminatus.cz' },
];

const pages = [
  { id: 'home', path: '/' },
  { id: 'hot-tubs', path: '/virivky/' },
  { id: 'swimspas', path: '/swimspa/' },
  { id: 'downloads', path: '/ke-stazeni/' },
  { id: 'support', path: '/podpora/' },
  { id: 'product-cub', path: '/product/cub/', product: true },
  { id: 'product-timberwolf', path: '/product/timberwolf/', product: true },
  { id: 'product-ocean', path: '/product/ocean/', product: true },
  { id: 'offers-archive', path: '/akcni-nabidky/', offers: true },
  { id: 'offer-stock', path: '/offer/vyprodej-skladovych-virivek/', offers: true },
];

function mkdirp(dir) {
  fs.mkdirSync(dir, { recursive: true });
}

function summarize(results) {
  const summary = {};

  for (const target of targets) {
    const targetResults = results.filter((item) => item.target === target.id);
    const okResults = targetResults.filter((item) => item.status && item.status < 400);
    const productResults = okResults.filter((item) => item.page.product);
    const offersResults = okResults.filter((item) => item.page.offers);
    const menuLinks = okResults.flatMap((item) => item.menuLinks || []);
    const catalogForms = okResults.flatMap((item) => item.forms || []).filter((form) => form.isCatalogLikely);

    summary[target.id] = {
      okPages: okResults.length,
      pagesWithCatalogText: okResults.filter((item) => item.catalogBlocks.length > 0).map((item) => item.pageId),
      pagesWithCatalogForm: okResults.filter((item) => item.forms.some((form) => form.isCatalogLikely)).map((item) => item.pageId),
      catalogFormCount: catalogForms.length,
      menuHasCatalogOrPriceCTA: menuLinks.some((link) => /cen[ií]k|katalog|ke stažení|ke-stazeni/i.test(link.text + ' ' + link.href)),
      menuHasInquiryCTA: menuLinks.some((link) => /popt|konzult|formular|kontakt/i.test(link.text + ' ' + link.href)),
      menuHasAkcniNabidky: menuLinks.some((link) => /akčn|akcni/i.test(link.text + ' ' + link.href)),
      menuHasOldStockSaleLabel: menuLinks.some((link) => /výprodej|vyprodej|skladov/i.test(link.text)),
      allOldStockSaleLinks: menuLinks.filter((link) => /výprodej|vyprodej|skladov/i.test(link.text + ' ' + link.href)).slice(0, 20),
      productPagesWithoutVisiblePrice: productResults.filter((item) => item.product.priceTexts.length === 0).map((item) => item.pageId),
      productPagesWithConfigPrices: productResults.filter((item) => item.product.configurationPrices.length > 0).map((item) => item.pageId),
      productConfigurationCounts: Object.fromEntries(productResults.map((item) => [item.pageId, item.product.configurationCount])),
      offerPageTitles: Object.fromEntries(offersResults.map((item) => [item.pageId, item.h1])),
      offerCardsByPage: Object.fromEntries(offersResults.map((item) => [item.pageId, item.offers.cardCount])),
    };
  }

  return summary;
}

async function auditPage(page, target, pageDef) {
  const url = new URL(pageDef.path, target.baseUrl).toString();
  let response = null;
  let errorMessage = '';

  try {
    response = await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 35000 });
    await page.waitForTimeout(900);
  } catch (error) {
    errorMessage = error.message;
  }

  const screenshot = path.join(outputDir, `${target.id}-${pageDef.id}-desktop.png`);
  try {
    await page.screenshot({ path: screenshot, fullPage: false });
  } catch (error) {
    // Keep the audit useful even when one page fails before first paint.
  }

  const data = await page.evaluate((pageMeta) => {
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
    const visible = (element) => {
      if (!element) {
        return false;
      }
      const rect = element.getBoundingClientRect();
      const styles = window.getComputedStyle(element);
      return rect.width > 0 && rect.height > 0 && styles.display !== 'none' && styles.visibility !== 'hidden' && styles.opacity !== '0';
    };
    const unique = (items, keyFn) => {
      const seen = new Set();
      return items.filter((item) => {
        const key = keyFn(item);
        if (seen.has(key)) {
          return false;
        }
        seen.add(key);
        return true;
      });
    };
    const matchesCommercial = (value) => /cen[ií]k|cena|ceny|katalog|catalog|akčn|akcni|výprodej|vyprodej|skladov/i.test(value || '');

    const links = Array.from(document.querySelectorAll('a')).map((link) => ({
      text: text(link),
      href: absolutize(link.getAttribute('href') || ''),
      visible: visible(link),
      classes: typeof link.className === 'string' ? link.className : '',
    }));
    const menuLinks = links.filter((link) => /f-header|f-footer|menu|navigation|nav/i.test(link.classes) || link.visible).filter((link) => matchesCommercial(link.text + ' ' + link.href));

    const catalogBlocks = unique(Array.from(document.querySelectorAll('main section, main article, main .f-section, .f-contact-cta, .f-form, .f-content, body > section'))
      .map((element) => ({ text: text(element), visible: visible(element) }))
      .filter((block) => block.visible && /katalog/i.test(block.text) && /cen[ií]k|cena|cenovou|email|e-mail/i.test(block.text))
      .map((block) => ({ text: block.text.slice(0, 900) })), (item) => item.text);

    const forms = Array.from(document.querySelectorAll('form')).map((form) => {
      const formText = text(form);
      const hiddenValues = Array.from(form.querySelectorAll('input[type="hidden"]')).map((input) => ({
        name: input.getAttribute('name') || '',
        value: input.getAttribute('value') || '',
      }));
      const emailInputs = Array.from(form.querySelectorAll('input[type="email"], input[name*="email" i]')).map((input) => ({
        name: input.getAttribute('name') || '',
        placeholder: input.getAttribute('placeholder') || '',
        required: input.required,
      }));
      const buttonTexts = Array.from(form.querySelectorAll('button, input[type="submit"]')).map((button) => text(button) || button.getAttribute('value') || '').filter(Boolean);
      const hiddenText = hiddenValues.map((item) => `${item.name}:${item.value}`).join(' ');
      const allText = `${formText} ${hiddenText} ${emailInputs.map((input) => input.placeholder).join(' ')}`;

      return {
        id: form.id || '',
        classes: typeof form.className === 'string' ? form.className : '',
        action: absolutize(form.getAttribute('action') || ''),
        method: form.getAttribute('method') || '',
        text: formText.slice(0, 700),
        hiddenValues,
        emailInputs,
        buttonTexts,
        isCatalogLikely: /catalog|katalog|cen[ií]k|ke stažení|ke-stazeni/i.test(allText),
        isEcomailVisible: /ecomail|api2\.ecomailapp/i.test(allText + ' ' + form.outerHTML),
      };
    });

    const priceTexts = unique(Array.from(document.querySelectorAll('.f-price, .f-links__price, [class*="price"], [class*="Price"]'))
      .map((element) => text(element))
      .filter((value) => value && (/kč|kc|cena|vyžádání|vyzadani|od\s+\d/i.test(value)))
      .map((value) => value.slice(0, 180)), (value) => value);

    const configurationCards = Array.from(document.querySelectorAll('.f-product-configuration, [class*="configuration"]')).filter(visible);
    const configurationPrices = unique(configurationCards
      .map((card) => text(card.querySelector('.f-product-configuration__price, [class*="price"]')))
      .filter(Boolean), (value) => value);

    const offerCards = Array.from(document.querySelectorAll('.f-offer-card, [class*="offer-card"], .f-offer-grid > *')).filter(visible);

    return {
      page: pageMeta,
      finalUrl: window.location.href,
      title: document.title,
      bodyClass: document.body.className,
      h1: text(document.querySelector('h1')),
      commercialLinks: links.filter((link) => matchesCommercial(link.text + ' ' + link.href)).slice(0, 80),
      menuLinks,
      catalogBlocks,
      forms,
      frontendMentionsEcomail: /ecomail|api2\.ecomailapp/i.test(document.documentElement.outerHTML),
      product: {
        priceTexts,
        configurationCount: configurationCards.length,
        configurationPrices,
        navigationLinks: links.filter((link) => /výhody|vyhody|voliteln|config|barv|popt/i.test(link.text + ' ' + link.href)).slice(0, 30),
      },
      offers: {
        cardCount: offerCards.length,
        cardTexts: offerCards.map((card) => text(card).slice(0, 350)).slice(0, 12),
        oldStockSaleTextCount: (text(document.body).match(/Výprodej skladových vířivek|Vyp[^\s]+ skladov/i) || []).length,
      },
    };
  }, pageDef);

  return {
    target: target.id,
    pageId: pageDef.id,
    url,
    status: response ? response.status() : 0,
    errorMessage,
    screenshot,
    ...data,
  };
}

(async () => {
  mkdirp(outputDir);
  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const context = await browser.newContext({
    viewport: { width: 1440, height: 1000 },
    deviceScaleFactor: 1,
  });
  const page = await context.newPage();
  const results = [];

  for (const target of targets) {
    for (const pageDef of pages) {
      results.push(await auditPage(page, target, pageDef));
    }
  }

  await browser.close();

  const payload = {
    generatedAt: new Date().toISOString(),
    pages,
    results,
    summary: summarize(results),
  };

  const outputPath = path.join(outputDir, 'audit.json');
  fs.writeFileSync(outputPath, JSON.stringify(payload, null, 2), 'utf8');
  console.log(`Wrote ${outputPath}`);
  console.log(JSON.stringify(payload.summary, null, 2));
})();
