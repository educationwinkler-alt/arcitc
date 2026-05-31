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

  const mediaTags = [
    ...(benefitSection.match(/<span\b[^>]*\bf-product-benefit__media\b[^>]*>/g) || []),
    ...(optionsSection.match(/<span\b[^>]*\bf-product-benefit__media\b[^>]*>/g) || []),
  ];

  if (mediaTags.length < 20) {
    throw new Error(`/product/timberwolf/ exposes only ${mediaTags.length} benefit/options media slots.`);
  }

  for (const [index, tag] of mediaTags.entries()) {
    if (!tag.includes('data-asset-status=')) {
      throw new Error(`/product/timberwolf/ benefit/options media slot ${index + 1} is missing data-asset-status.`);
    }
    if (!tag.includes('data-asset-status="available"')) {
      throw new Error(`/product/timberwolf/ benefit/options media slot ${index + 1} is not backed by a Figma media export.`);
    }
  }

  const mediaImages = countMatches(`${benefitSection}\n${optionsSection}`, /uploads\/import\/figma\/benefit-media-[0-9]{2}\.png/g);
  if (mediaImages < mediaTags.length) {
    throw new Error(`/product/timberwolf/ renders only ${mediaImages} Figma benefit media images for ${mediaTags.length} slots.`);
  }

  assertExcludes('/product/timberwolf/ benefits/options', `${benefitSection}\n${optionsSection}`, [
    'data-asset-status="WAITING_ON_FIGMA_EXPORT"',
    'data-asset-status="WAITING_ON_OWNER"',
  ]);
}

function assertConfigurationMedia(path, html) {
  const cards = html.match(/<article\b[^>]*\bf-product-configuration\b[\s\S]*?<\/article>/g) || [];

  if (cards.length === 0) {
    throw new Error(`${path} has no product configuration cards to validate.`);
  }

  for (const [index, card] of cards.entries()) {
    if (card.includes('f-product-configuration--no-media')) {
      throw new Error(`${path} configuration card ${index + 1} still renders without media.`);
    } else {
      assertIncludes(`${path} configuration card ${index + 1}`, card, [
        'f-product-configuration__thumb',
        'data-asset-status=',
      ]);
    }
  }
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
    '/product/athabascan/': await fetchHtml('/product/athabascan/'),
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
  assertConfigurationMedia('/product/timberwolf/', pages['/product/timberwolf/']);
  assertConfigurationMedia('/product/athabascan/', pages['/product/athabascan/']);

  assertMegaMenu(pages['/']);

  const contractsCss = readFileSync('wp-content/themes/arctic/src/less/_component-contracts.less', 'utf8');
  assertIncludes('_component-contracts.less', contractsCss, [
    '.f-listing__image--product-missing',
    '.f-product-benefit__media img',
    '.f-mega-menu__thumb--missing',
  ]);
  assertExcludes('_component-contracts.less', contractsCss, [
    '.f-product-benefit__media:before',
    '.f-product-benefit__media:after',
  ]);

  console.log('Product/category media smoke passed.');
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
