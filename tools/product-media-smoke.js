const { readFileSync } = require('fs');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';

async function fetchHtml(path) {
  const response = await fetch(`${baseUrl}${path}`);

  if (!response.ok) {
    throw new Error(`${path} returned ${response.status}`);
  }

  return response.text();
}

function countMatches(text, regex) {
  return (text.match(regex) || []).length;
}

function assertIncludes(label, text, needles) {
  for (const needle of needles) {
    if (!text.includes(needle)) {
      throw new Error(`${label} is missing expected marker: ${needle}`);
    }
  }
}

function assertExcludes(label, text, needles) {
  for (const needle of needles) {
    if (text.includes(needle)) {
      throw new Error(`${label} contains forbidden marker: ${needle}`);
    }
  }
}

function blockBetween(html, startNeedle, endNeedle, label) {
  const start = html.indexOf(startNeedle);

  if (start === -1) {
    throw new Error(`${label} is missing start marker: ${startNeedle}`);
  }

  const end = html.indexOf(endNeedle, start);

  if (end === -1) {
    throw new Error(`${label} is missing end marker after ${startNeedle}: ${endNeedle}`);
  }

  return html.slice(start, end);
}

function assertCatalogMedia(path, html, expectedSources) {
  assertExcludes(path, html, [
    'f-listing__image--product-missing',
    'data-product-media="missing"',
    'uploads/import/figma/category-product-card-',
  ]);

  const productMediaCount = countMatches(html, /data-product-media="product-image"/g);
  if (productMediaCount < expectedSources.length) {
    throw new Error(`${path} has only ${productMediaCount} product media cards.`);
  }

  assertIncludes(path, html, expectedSources);
}

function assertBenefitMedia(html) {
  const benefitSection = blockBetween(
    html,
    'id="vyhody"',
    'id="volitelna-vybava"',
    '/product/timberwolf/ benefits'
  );
  const optionsSection = blockBetween(
    html,
    'id="volitelna-vybava"',
    'f-reference-section--product-context',
    '/product/timberwolf/ options'
  );

  assertIncludes('/product/timberwolf/ benefits', benefitSection, [
    'f-product-benefit__media--shell',
    'f-product-benefit__media--heatlock',
    'f-product-benefit__media--cabinet',
    'f-product-benefit__media--water',
  ]);

  assertIncludes('/product/timberwolf/ options', optionsSection, [
    'f-product-benefit__media--onzen',
    'f-product-benefit__media--spa-boy',
    'f-product-benefit__media--wifi',
    'f-product-benefit__media--covana',
  ]);
}

function assertMegaMenu(html) {
  assertExcludes('desktop mega menu', html, [
    'f-mega-menu__thumb--missing',
    'data-product-media="missing"',
    'V\u0161echny v\u00ed\u0159ivky',
    'Vsechny virivky',
  ]);

  const menuCount = (key) => {
    const match = html.match(new RegExp(`f-mega-menu f-mega-menu--${key}[^>]*data-product-count="([0-9]+)"`));

    if (!match) {
      throw new Error(`desktop mega ${key} is missing data-product-count.`);
    }

    return Number.parseInt(match[1], 10);
  };

  if (menuCount('hot-tubs') < 14) {
    throw new Error('desktop mega hot tubs does not expose the full hot tub product set.');
  }

  if (menuCount('swimspa') < 6) {
    throw new Error('desktop mega swimspa does not expose the full swimspa product set.');
  }
}

async function main() {
  const pages = {
    '/': await fetchHtml('/'),
    '/virivky/': await fetchHtml('/virivky/'),
    '/swimspa/': await fetchHtml('/swimspa/'),
    '/product/timberwolf/': await fetchHtml('/product/timberwolf/'),
  };

  assertCatalogMedia('/virivky/', pages['/virivky/'], [
    'virivka-summit-xl',
    'virivka-summit',
    'virivka-tundra',
  ]);

  assertIncludes('/virivky/ category intro', pages['/virivky/'], [
    'uploads/import/figma/category-vlastnosti.jpg',
    'uploads/import/figma/category-zaruka.jpg',
  ]);

  assertCatalogMedia('/swimspa/', pages['/swimspa/'], [
    'bazen-athabascan',
    'bazen-hudson',
    'bazen-kingfisher',
  ]);

  assertIncludes('/swimspa/ category intro', pages['/swimspa/'], [
    'uploads/import/figma-category-celorocni-bazeny.jpg',
    'uploads/import/legacy-categories/swimspa.jpg',
  ]);

  assertIncludes('/product/timberwolf/', pages['/product/timberwolf/'], [
    'timberwolf-signature',
    'timberwolf-side',
    'timberwolf-prestige',
    'acrylic-dakota',
    'acrylic-kalahari',
    'acrylic-odyssey',
    'acrylic-espresso',
  ]);
  assertExcludes('/product/timberwolf/', pages['/product/timberwolf/'], [
    'uploads/import/figma/detail-timberwolf-hero.jpg',
    'uploads/import/figma/color-',
    'uploads/import/figma/cabinet-',
  ]);
  assertBenefitMedia(pages['/product/timberwolf/']);

  assertMegaMenu(pages['/']);

  const contractsCss = readFileSync('wp-content/themes/arctic/src/less/_component-contracts.less', 'utf8');
  assertIncludes('_component-contracts.less', contractsCss, [
    '.f-listing__image--product-missing',
    '.f-product-benefit__media--shell',
    '.f-product-benefit__media--onzen',
    '.f-mega-menu__thumb--missing',
  ]);

  console.log('Product/category media smoke passed.');
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
