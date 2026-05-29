const { readFileSync } = require('fs');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';

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
      throw new Error(`${label} is missing component contract marker: ${needle}`);
    }
  }
}

function assertExcludes(label, text, needles) {
  for (const needle of needles) {
    if (text.includes(needle)) {
      throw new Error(`${label} contains forbidden component marker/content: ${needle}`);
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

function assertStaticBenefitCardsDoNotLookInteractive(html) {
  const staticCardRegex = /<article\b[^>]*\bf-product-benefit--static\b[\s\S]*?<\/article>/g;
  const staticCards = html.match(staticCardRegex) || [];

  if (staticCards.length === 0) {
    throw new Error('/product/timberwolf/ has no static benefit cards to validate.');
  }

  staticCards.forEach((card, index) => {
    assertExcludes(`/product/timberwolf/ static benefit card ${index + 1}`, card, [
      'f-product-benefit__trigger',
      'f-product-benefit__more',
    ]);
  });
}

async function main() {
  const pages = {
    '/': await fetchHtml('/'),
    '/virivky/': await fetchHtml('/virivky/'),
    '/swimspa/': await fetchHtml('/swimspa/'),
    '/product/timberwolf/': await fetchHtml('/product/timberwolf/'),
  };

  assertIncludes('/', pages['/'], [
    'f-showroom-panel--collage',
    'f-progress-layout--shared',
    'f-contact-cta--shared',
    'f-footer--handoff',
  ]);

  assertIncludes('/virivky/', pages['/virivky/'], [
    'f-configurator-cta--shared f-configurator-cta--hot-tub',
    'f-showroom-panel--collage',
    'f-progress-layout--shared',
    'f-contact-cta--shared',
  ]);

  assertIncludes('/swimspa/', pages['/swimspa/'], [
    'f-configurator-cta--shared f-configurator-cta--swimspa',
    'f-showroom-panel--collage',
    'f-progress-layout--shared',
    'f-contact-cta--shared',
  ]);

  const swimspaConfigurator = blockBetween(
    pages['/swimspa/'],
    'f-configurator-cta--swimspa',
    '</section>',
    '/swimspa/ configurator'
  );
  assertExcludes('/swimspa/ configurator', swimspaConfigurator, [
    'vlastní vířivku',
    'vlastni virivku',
  ]);

  assertIncludes('/product/timberwolf/', pages['/product/timberwolf/'], [
    'f-configurator-cta--shared f-configurator-cta--product',
    'f-product-benefit--interactive',
    'f-product-benefit--static',
    'f-contact-cta--shared',
  ]);

  const productBenefits = blockBetween(
    pages['/product/timberwolf/'],
    'id="vyhody"',
    'id="volitelna-vybava"',
    '/product/timberwolf/ benefits'
  );
  assertIncludes('/product/timberwolf/ benefits', productBenefits, [
    'f-product-benefit--interactive',
    'f-product-benefit__trigger',
    'f-product-benefit__more',
  ]);
  assertStaticBenefitCardsDoNotLookInteractive(productBenefits);

  const productOptions = blockBetween(
    pages['/product/timberwolf/'],
    'id="volitelna-vybava"',
    'f-reference-section--product-context',
    '/product/timberwolf/ options'
  );
  assertExcludes('/product/timberwolf/ options', productOptions, [
    'f-product-benefit__trigger',
    'f-product-benefit__more',
  ]);

  const contractsCss = readFileSync('wp-content/themes/arctic/src/less/_component-contracts.less', 'utf8');
  assertIncludes('_component-contracts.less', contractsCss, [
    '.f-section--contact.f-section--component-contact',
    '.f-footer--arctic.f-footer--handoff',
    '.f-showroom-panel.f-showroom-panel--collage',
    '.f-configurator-cta.f-configurator-cta--shared',
    '.f-progress-layout.f-progress-layout--shared',
    '.f-product-benefit--static',
    '.f-off--benefit-popup',
  ]);

  console.log('Component contract smoke passed.');
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
