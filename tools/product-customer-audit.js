const fs = require('fs');
const path = require('path');
const { chromium } = require('playwright-core');

const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const outputDir = path.join('docs', 'screenshots', 'product-customer-audit-2026-06-08');

const targets = [
  { id: 'local', baseUrl: process.env.ARCTIC_LOCAL_BASE_URL || 'http://localhost:8090' },
  { id: 'prod', baseUrl: process.env.ARCTIC_PROD_BASE_URL || 'https://illuminatus.cz' },
];

const products = [
  { slug: 'lunar', group: 'hot-tub-core' },
  { slug: 'orion', group: 'hot-tub-core' },
  { slug: 'summit-xl', group: 'hot-tub-custom' },
  { slug: 'summit', group: 'hot-tub-custom' },
  { slug: 'tundra', group: 'hot-tub-custom' },
  { slug: 'kodiak', group: 'hot-tub-custom' },
  { slug: 'klondiker', group: 'hot-tub-custom' },
  { slug: 'yukon', group: 'hot-tub-custom' },
  { slug: 'cub', group: 'hot-tub-custom' },
  { slug: 'fox', group: 'hot-tub-custom' },
  { slug: 'mckinley', group: 'hot-tub-classic' },
  { slug: 'mustang', group: 'hot-tub-classic' },
  { slug: 'timberwolf', group: 'hot-tub-classic' },
  { slug: 'totem', group: 'hot-tub-classic' },
  { slug: 'eagle', group: 'hot-tub-classic' },
  { slug: 'husky', group: 'hot-tub-core' },
  { slug: 'athabascan', group: 'swimspa-classic' },
  { slug: 'hudson', group: 'swimspa-classic' },
  { slug: 'kingfisher', group: 'swimspa-classic' },
  { slug: 'ocean', group: 'swimspa-custom' },
  { slug: 'okanagan', group: 'swimspa-custom' },
  { slug: 'wolverine', group: 'swimspa-classic' },
];

const screenshotSlugs = new Set(['cub', 'timberwolf', 'athabascan', 'ocean']);

function mkdirp(dir) {
  fs.mkdirSync(dir, { recursive: true });
}

function summarize(results) {
  const byTarget = {};

  for (const entry of results) {
    if (!byTarget[entry.target]) {
      byTarget[entry.target] = {
        totalProducts: 0,
        okProducts: 0,
        productsWithOneOrNoConfiguration: [],
        productsWithHeroTrim: [],
        productsWithNoCabinetColors: [],
        productsWithDeadNavLinks: [],
        failedProducts: [],
        hotTubsWithoutBenefits: [],
        hotTubsWithoutOptions: [],
        swimspasWithoutBenefits: [],
        swimspasWithoutOptions: [],
      };
    }

    const summary = byTarget[entry.target];
    summary.totalProducts += 1;

    if (entry.status && entry.status < 400) {
      summary.okProducts += 1;
    }

    if (entry.error || !entry.status || entry.status >= 400) {
      summary.failedProducts.push({
        slug: entry.slug,
        group: entry.group,
        status: entry.status || null,
        error: entry.error || '',
      });
      continue;
    }

    if ((entry.configurations?.count || 0) <= 1) {
      summary.productsWithOneOrNoConfiguration.push({
        slug: entry.slug,
        group: entry.group,
        count: entry.configurations?.count || 0,
        names: entry.configurations?.names || [],
      });
    }

    if (entry.hero?.descriptionEndsWithEllipsis) {
      summary.productsWithHeroTrim.push({
        slug: entry.slug,
        words: entry.hero.descriptionWordCount,
        text: entry.hero.description,
      });
    }

    if ((entry.colors?.cabinetCount || 0) === 0 && !entry.group.startsWith('other')) {
      summary.productsWithNoCabinetColors.push({
        slug: entry.slug,
        group: entry.group,
        shellCount: entry.colors?.shellCount || 0,
      });
    }

    const deadNav = (entry.navigation || []).filter((item) => item.href.startsWith('#') && !item.targetExists);
    if (deadNav.length > 0) {
      summary.productsWithDeadNavLinks.push({
        slug: entry.slug,
        deadNav: deadNav.map((item) => item.href),
      });
    }

    if (entry.group.startsWith('hot-tub')) {
      if (!entry.benefits?.exists || (entry.benefits?.count || 0) === 0) {
        summary.hotTubsWithoutBenefits.push(entry.slug);
      }
      if (!entry.options?.exists || (entry.options?.count || 0) === 0) {
        summary.hotTubsWithoutOptions.push(entry.slug);
      }
    }

    if (entry.group.startsWith('swimspa')) {
      if (!entry.benefits?.exists || (entry.benefits?.count || 0) === 0) {
        summary.swimspasWithoutBenefits.push(entry.slug);
      }
      if (!entry.options?.exists || (entry.options?.count || 0) === 0) {
        summary.swimspasWithoutOptions.push(entry.slug);
      }
    }
  }

  return byTarget;
}

async function auditProduct(page, target, product) {
  const url = new URL(`/product/${product.slug}/`, target.baseUrl).toString();
  const response = await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });
  await page.waitForTimeout(900);

  const screenshots = {};

  if (screenshotSlugs.has(product.slug)) {
    screenshots.top = path.join(outputDir, `${target.id}-${product.slug}-desktop-top.png`);
    await page.screenshot({ path: screenshots.top, fullPage: false });
  }

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
    const styles = (element) => {
      if (!element) {
        return null;
      }
      const computed = window.getComputedStyle(element);
      return {
        display: computed.display,
        visibility: computed.visibility,
        opacity: computed.opacity,
        position: computed.position,
        zIndex: computed.zIndex,
        objectFit: computed.objectFit,
        objectPosition: computed.objectPosition,
        transform: computed.transform,
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

    const navLinks = Array.from(document.querySelectorAll('.f-links--product a[href^="#"]')).map((link) => {
      const href = link.getAttribute('href') || '';
      return {
        text: text(link),
        href,
        targetExists: Boolean(document.querySelector(href)),
        rect: rect(link),
      };
    });

    const configCards = Array.from(document.querySelectorAll('#konfigurace .f-product-configuration')).map((card) => {
      const fields = {};
      for (const row of Array.from(card.querySelectorAll('dl > div'))) {
        const key = text(row.querySelector('dt'));
        const value = text(row.querySelector('dd'));
        if (key) {
          fields[key] = value;
        }
      }

      const image = card.querySelector('img');

      return {
        name: text(card.querySelector('h3')),
        price: text(card.querySelector('.f-product-configuration__price')),
        fields,
        assetStatus: card.getAttribute('data-asset-status') || '',
        thumbStatus: card.querySelector('.f-product-configuration__thumb')?.getAttribute('data-asset-status') || '',
        imageSrc: image ? absolutize(image.currentSrc || image.src || '') : '',
        imageNaturalWidth: image ? image.naturalWidth : 0,
        imageNaturalHeight: image ? image.naturalHeight : 0,
      };
    });

    const colorsSection = document.querySelector('#barvy');
    const colorLists = colorsSection
      ? Array.from(colorsSection.querySelectorAll('.f-product-colors__list')).map((list) => {
        const heading = list.previousElementSibling;
        const items = Array.from(list.querySelectorAll('.f-product-colors__item')).map((item) => {
          const image = item.querySelector('img');
          return {
            name: text(item),
            source: item.getAttribute('data-content-source') || '',
            assetStatus: item.getAttribute('data-asset-status') || '',
            assetSource: item.getAttribute('data-asset-source') || '',
            hasImage: Boolean(image),
            imageSrc: image ? absolutize(image.currentSrc || image.src || '') : '',
          };
        });

        return {
          heading: text(heading),
          count: items.length,
          items,
        };
      })
      : [];

    const shellList = colorLists.find((list) => /sk[oř]epiny|sko/i.test(list.heading));
    const cabinetList = colorLists.find((list) => /kabinet/i.test(list.heading));

    const collectFeatureSection = (selector) => {
      const section = document.querySelector(selector);
      const cards = section ? Array.from(section.querySelectorAll('.f-product-benefit')).map((card) => ({
        title: text(card.querySelector('h3')),
        summary: text(card.querySelector('p')),
        source: card.getAttribute('data-content-source') || '',
        interactive: card.classList.contains('f-product-benefit--interactive'),
        hasMedia: Boolean(card.querySelector('.f-product-benefit__media img')),
        mediaStatus: card.querySelector('.f-product-benefit__media')?.getAttribute('data-asset-status') || '',
        hasLink: Boolean(card.querySelector('a[href]')),
      })) : [];

      return {
        exists: Boolean(section),
        source: section?.getAttribute('data-content-source') || '',
        heading: text(section?.querySelector('.f-section__header h2')),
        text: text(section?.querySelector('.f-section__header p')),
        count: cards.length,
        interactiveCount: cards.filter((card) => card.interactive).length,
        mediaCount: cards.filter((card) => card.hasMedia).length,
        linkCount: cards.filter((card) => card.hasLink).length,
        titles: cards.map((card) => card.title).filter(Boolean),
        cards: cards.slice(0, 8),
      };
    };

    const heroDescription = document.querySelector('.f-heading--product-detail .f-heading__description');
    const heroText = text(heroDescription);

    return {
      finalUrl: window.location.href,
      title: document.title,
      bodyClass: document.body.className,
      h1: text(document.querySelector('h1')),
      hero: {
        rect: rect(document.querySelector('.f-heading--product-detail')),
        description: heroText,
        descriptionWordCount: heroText ? heroText.split(/\s+/).length : 0,
        descriptionEndsWithEllipsis: /\.\.\.$/.test(heroText),
        descriptionVisible: visible(heroDescription),
        descriptionRect: rect(heroDescription),
      },
      navigation: navLinks,
      configurations: {
        exists: Boolean(document.querySelector('#konfigurace')),
        count: configCards.length,
        names: configCards.map((card) => card.name).filter(Boolean),
        cards: configCards,
      },
      colors: {
        exists: Boolean(colorsSection),
        heading: text(colorsSection?.querySelector('h2')),
        shellCount: shellList ? shellList.count : 0,
        cabinetCount: cabinetList ? cabinetList.count : 0,
        lists: colorLists,
      },
      benefits: collectFeatureSection('#vyhody'),
      options: collectFeatureSection('#volitelna-vybava'),
      references: {
        exists: Boolean(document.querySelector('#references')),
        count: document.querySelectorAll('#references .f-reference-card, #references article').length,
      },
      media: {
        heroImages: Array.from(document.querySelectorAll('.f-heading--product-detail img')).slice(0, 4).map((image) => ({
          src: absolutize(image.currentSrc || image.src || ''),
          naturalWidth: image.naturalWidth,
          naturalHeight: image.naturalHeight,
          rect: rect(image),
          style: styles(image),
        })),
      },
    };
  });

  if (screenshotSlugs.has(product.slug)) {
    const firstIssueSection = page.locator('#konfigurace, #barvy, #vyhody, #volitelna-vybava').first();
    if (await firstIssueSection.count()) {
      await firstIssueSection.scrollIntoViewIfNeeded({ timeout: 5000 }).catch(() => {});
      await page.waitForTimeout(250);
      screenshots.sections = path.join(outputDir, `${target.id}-${product.slug}-desktop-sections.png`);
      await page.screenshot({ path: screenshots.sections, fullPage: false });
    }
  }

  return {
    target: target.id,
    url,
    status: response ? response.status() : null,
    slug: product.slug,
    group: product.group,
    screenshots,
    ...data,
  };
}

async function main() {
  mkdirp(outputDir);

  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const results = [];

  try {
    for (const target of targets) {
      const context = await browser.newContext({
        viewport: { width: 1440, height: 1000 },
        deviceScaleFactor: 1,
      });
      const page = await context.newPage();

      for (const product of products) {
        try {
          results.push(await auditProduct(page, target, product));
        } catch (error) {
          results.push({
            target: target.id,
            slug: product.slug,
            group: product.group,
            url: new URL(`/product/${product.slug}/`, target.baseUrl).toString(),
            error: error && error.message ? error.message : String(error),
          });
        }
      }

      await context.close();
    }
  } finally {
    await browser.close();
  }

  const report = {
    generatedAt: new Date().toISOString(),
    products,
    results,
    summary: summarize(results),
  };

  const outputPath = path.join(outputDir, 'audit.json');
  fs.writeFileSync(outputPath, `${JSON.stringify(report, null, 2)}\n`, 'utf8');

  console.log(`Wrote ${outputPath}`);
  console.log(JSON.stringify(report.summary, null, 2));
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
