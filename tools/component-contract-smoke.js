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

function assertIncludesOneOf(label, text, needles) {
  if (!needles.some((needle) => text.includes(needle))) {
    throw new Error(`${label} is missing one of expected component markers: ${needles.join(' OR ')}`);
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

function assertProductDetailContract(path, html, scope) {
  assertIncludes(path, html, [
    'data-product-detail-contract="figma"',
    `data-product-detail-scope="${scope}"`,
    'f-heading--product-detail',
  ]);
  assertExcludes(path, html, [
    'f-heading--timberwolf',
  ]);

  if (scope === 'hot-tub') {
    assertIncludes(path, html, [
      'f-configurator-cta--shared f-configurator-cta--product',
      '/konfigurator/',
    ]);
  } else {
    assertExcludes(path, html, [
      'f-configurator-cta--product',
      'category-configurator.png',
    ]);
  }
}

async function main() {
  const productDetailPaths = {
    '/product/timberwolf/': 'hot-tub',
    '/product/lunar/': 'hot-tub',
    '/product/orion/': 'hot-tub',
    '/product/husky/': 'hot-tub',
    '/product/athabascan/': 'swimspa',
  };
  const pages = {
    '/': await fetchHtml('/'),
    '/virivky/': await fetchHtml('/virivky/'),
    '/swimspa/': await fetchHtml('/swimspa/'),
    '/reference/': await fetchHtml('/reference/'),
    '/konfigurator/': await fetchHtml('/konfigurator/'),
    '/konfigurator/timberwolf/': await fetchHtml('/konfigurator/timberwolf/'),
    '/poptavka-konfigurace/': await fetchHtml('/poptavka-konfigurace/?model_name=Timberwolf&option_jets=dd-30+Jets+2+Pumps&option_acrylic=dd-Acrylic+Platinum&option_cabinet=dd-Cabinet+Cedar'),
  };
  for (const path of Object.keys(productDetailPaths)) {
    pages[path] = await fetchHtml(path);
  }

  assertIncludes('/', pages['/'], [
    'f-showroom-panel--collage',
    'f-progress-layout--shared',
    'f-contact-cta--shared',
    'f-footer--arctic',
  ]);
  assertExcludes('/', pages['/'], [
    'f-footer--handoff',
  ]);

  assertIncludes('/virivky/', pages['/virivky/'], [
    'data-category-flow="hot-tub"',
    'f-section--product-listing-contract',
    'f-product-card--category',
    'f-configurator-cta--shared f-configurator-cta--hot-tub',
    `href="${baseUrl}/konfigurator/`,
    'category-configurator.png',
    'f-showroom-panel--collage',
    'f-progress-layout--shared',
    'f-contact-cta--shared',
    'data-reference-context="virivky"',
  ]);
  assertExcludes('/virivky/', pages['/virivky/'], [
    'id="visao-viewer-id"',
  ]);

  assertIncludes('/swimspa/', pages['/swimspa/'], [
    'data-category-flow="swimspa"',
    'f-section--product-listing-contract',
    'f-product-card--category',
    'f-showroom-panel--collage',
    'f-progress-layout--shared',
    'f-contact-cta--shared',
    'data-reference-context="swimspa"',
  ]);
  assertExcludes('/swimspa/', pages['/swimspa/'], [
    'f-section--configurator',
    'f-configurator-cta--swimspa',
    'Nakonfigurujte si vlastní swimspa',
    'Nakonfigurujte si vlastni swimspa',
  ]);

  assertIncludes('/reference/', pages['/reference/'], [
    'f-reference-grid',
    'f-reference-card',
    'js-images',
  ]);

  assertIncludes('/konfigurator/', pages['/konfigurator/'], [
    'f-section--jucra-builder',
    'data-jucra-builder',
    '3D konfigurátor Arctic Spas',
    'f-jucra-builder__model-strip',
  ]);
  assertIncludesOneOf('/konfigurator/', pages['/konfigurator/'], [
    'data-jucra-status="WAITING_ON_JUCRA_PLUGIN"',
    'data-jucra-shortcode=',
  ]);
  assertExcludes('/konfigurator/', pages['/konfigurator/'], [
    'f-jucra-builder__panel',
    'data-builder-param=',
    'data-builder-request-url=',
    'Vybraný model',
  ]);
  if (pages['/konfigurator/'].includes('data-jucra-shortcode=')) {
    assertIncludes('/konfigurator/ plugin output', pages['/konfigurator/'], [
      'id="visao-viewer-id"',
      'data-jucra-pricing-handoff="local-inquiry"',
      '/poptavka-konfigurace/?model_name=',
      'Sestavte si svou vířivku',
      'Vyžádat cenovou nabídku',
    ]);
    assertExcludes('/konfigurator/ plugin output', pages['/konfigurator/'], [
      'Request Pricing',
      'Build Your Spa',
      'Developers Tools',
    ]);
  }

  assertIncludes('/konfigurator/timberwolf/', pages['/konfigurator/timberwolf/'], [
    'data-jucra-model="Timberwolf"',
    'Timberwolf',
  ]);
  assertExcludes('/konfigurator/timberwolf/', pages['/konfigurator/timberwolf/'], [
    '[visao_viewer',
    '[visao_builder',
    'f-jucra-builder__panel',
    'data-builder-param=',
  ]);

  assertIncludes('/poptavka-konfigurace/', pages['/poptavka-konfigurace/'], [
    'f-section--jucra-inquiry',
    'data-jucra-inquiry',
    'Timberwolf',
    '2 čerpadla',
    'Platinum Swirl',
    'Cedar',
    'f-form--jucra-inquiry',
    'name="f-form" value="jucra"',
    'name="f-jucra-model" value="Timberwolf"',
    'name="f-jucra-option-jets" value="dd-30 Jets 2 Pumps"',
    'name="f-jucra-option-acrylic" value="dd-Acrylic Platinum"',
    'name="f-jucra-option-cabinet" value="dd-Cabinet Cedar"',
  ]);
  assertExcludes('/poptavka-konfigurace/', pages['/poptavka-konfigurace/'], [
    'Chybí vybraná konfigurace',
    '/kontakt/?model_name=',
  ]);

  assertIncludes('/product/timberwolf/', pages['/product/timberwolf/'], [
    'f-configurator-cta--shared f-configurator-cta--product',
    `href="${baseUrl}/konfigurator/timberwolf/`,
    'category-configurator.png',
    'data-reference-context="virivky"',
    'f-product-benefit--interactive',
    'f-product-benefit--static',
    'f-contact-cta--shared',
  ]);
  for (const [path, scope] of Object.entries(productDetailPaths)) {
    assertProductDetailContract(path, pages[path], scope);
  }

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
    '.f-showroom-panel.f-showroom-panel--collage',
    '.f-configurator-cta.f-configurator-cta--shared',
    '.f-section--jucra-builder',
    '.f-section--jucra-inquiry',
    '.f-section--product-listing-contract',
    '.f-product-card.f-product-card--category',
    '.f-progress-layout.f-progress-layout--shared',
    '.f-jucra-builder__shortcode .clickable-image',
    'border: 2px solid rgba(35, 40, 47, 0.18)',
    '.f-product-benefit--static',
    '.f-off--benefit-popup',
    '.f-reference-section--recent-carousel',
  ]);
  assertExcludes('_component-contracts.less', contractsCss, [
    '.f-footer--arctic.f-footer--handoff',
    '.f-configurator-cta--swimspa',
  ]);

  console.log('Component contract smoke passed.');
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
