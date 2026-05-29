const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';

const standardProductSlugs = [
  'lunar',
  'orion',
  'summit-xl',
  'summit',
  'tundra',
  'kodiak',
  'klondiker',
  'yukon',
  'cub',
  'fox',
  'mckinley',
  'mustang',
  'timberwolf',
  'totem',
  'eagle',
  'husky',
  'athabascan',
  'hudson',
  'kingfisher',
  'ocean',
  'okanagan',
  'wolverine',
];

const accessoryProductSlugs = [
  'covana',
  'sauny',
  'koupaci-sudy-kirami',
  'prislusenstvi-a-doplnky',
  'ikono-nabytek',
  'ochlazovaci-bazenek',
];

const productExpectations = {
  timberwolf: {
    minConfigurationCards: 2,
    expectedConfigurations: ['Prestige 15/1', 'Signature 30/2'],
  },
  lunar: {
    minConfigurationCards: 2,
    expectedConfigurations: ['Prestige', 'Signature'],
  },
  orion: {
    minConfigurationCards: 2,
    expectedConfigurations: ['Prestige', 'Signature'],
  },
};

const mojibakeNeedles = [
  '\u00c4',
  '\u0102',
  '\u0139',
  '\u00c5',
  '\u00c2',
  '\u00e2',
  '\ufffd',
];

function textFromHtml(html) {
  return html
    .replace(/<script[\s\S]*?<\/script>/gi, ' ')
    .replace(/<style[\s\S]*?<\/style>/gi, ' ')
    .replace(/<[^>]+>/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();
}

async function fetchText(path) {
  const response = await fetch(`${baseUrl}${path}`);

  if (!response.ok) {
    throw new Error(`${path} returned ${response.status}`);
  }

  return textFromHtml(await response.text());
}

async function fetchHtml(path) {
  const response = await fetch(`${baseUrl}${path}`);

  if (!response.ok) {
    throw new Error(`${path} returned ${response.status}`);
  }

  return response.text();
}

function countOccurrences(text, needle) {
  return text.split(needle).length - 1;
}

function countConfigurationCards(html) {
  return (html.match(/<article\b[^>]*class=["'][^"']*\bf-product-configuration\b/g) || []).length;
}

function referenceProjectLinks(html) {
  return html.match(/href=["'][^"']*\/project\//gi) || [];
}

function assertNoMojibake(path, text) {
  const hit = mojibakeNeedles.find((needle) => text.includes(needle));

  if (hit) {
    const index = text.indexOf(hit);
    throw new Error(`${path} contains mojibake near: ${text.slice(Math.max(0, index - 80), index + 120)}`);
  }
}

async function assertProduct(path, options = {}) {
  const html = await fetchHtml(path);
  const text = textFromHtml(html);
  assertNoMojibake(path, text);

  if (!text.includes('Arctic Spas')) {
    throw new Error(`${path} does not look like an Arctic Spas product page.`);
  }

  if (options.requireConfiguration && !text.includes('Konfigurace')) {
    throw new Error(`${path} is missing the product configuration section.`);
  }

  if (options.requireConfiguration) {
    const cardCount = countConfigurationCards(html);
    const minCards = options.minConfigurationCards || 1;

    if (cardCount < minCards) {
      throw new Error(`${path} has ${cardCount} configuration card(s), expected at least ${minCards}.`);
    }
  }

  if (options.expectedConfigurations) {
    for (const expected of options.expectedConfigurations) {
      if (!text.includes(expected)) {
        throw new Error(`${path} is missing configuration label: ${expected}`);
      }
    }
  }

  if (text.includes('Lorem ipsum') || text.includes('Sample Page') || text.includes('Hello world!')) {
    throw new Error(`${path} contains placeholder content.`);
  }
}

async function assertSupportContent() {
  const html = await fetchHtml('/podpora/');
  const text = textFromHtml(html);
  assertNoMojibake('/podpora/', text);

  if (countOccurrences(html, 'f-support-faq-card') < 9) {
    throw new Error('/podpora/ is missing seeded FAQ cards.');
  }

  for (const expected of [
    'Jak probíhá výběr a objednávka vířivky?',
    'Stavební příprava',
    'Jak rychle dostanu cenovou nabídku?',
  ]) {
    if (!text.includes(expected)) {
      throw new Error(`/podpora/ is missing FAQ content: ${expected}`);
    }
  }
}

async function assertReferenceContent() {
  const html = await fetchHtml('/reference/');
  const text = textFromHtml(html);
  assertNoMojibake('/reference/', text);

  if (!html.includes('f-reference-section--archive-grid')) {
    throw new Error('/reference/ is missing the archive-grid reference component contract.');
  }

  if (!html.includes('f-reference-card--lightbox')) {
    throw new Error('/reference/ is missing lightbox reference cards.');
  }

  const forbiddenLinks = referenceProjectLinks(html);

  if (forbiddenLinks.length > 0) {
    throw new Error(`/reference/ exposes reference detail links: ${forbiddenLinks.slice(0, 3).join(', ')}`);
  }

  if (countOccurrences(html, 'f-reference-card') < 9) {
    throw new Error('/reference/ is missing seeded reference cards.');
  }

  for (const expected of [
    'Arctic Fox po letech provozu',
    'Swimspa Wolverine',
    'Novoroční přání z vířivky',
  ]) {
    if (!text.includes(expected)) {
      throw new Error(`/reference/ is missing reference content: ${expected}`);
    }
  }
}

async function assertReferenceComponentContracts() {
  const expectations = [
    ['/', 'f-reference-section--homepage-context'],
    ['/virivky/', 'f-reference-section--category-context'],
    ['/swimspa/', 'f-reference-section--category-context'],
    ['/product/timberwolf/', 'f-reference-section--product-context'],
  ];

  for (const [path, contextClass] of expectations) {
    const html = await fetchHtml(path);

    if (!html.includes('f-reference-section--recent-carousel')) {
      throw new Error(`${path} is missing the recent-carousel reference component contract.`);
    }

    if (!html.includes(contextClass)) {
      throw new Error(`${path} is missing reference context class ${contextClass}.`);
    }

    const forbiddenLinks = referenceProjectLinks(html);

    if (forbiddenLinks.length > 0) {
      throw new Error(`${path} exposes reference detail links: ${forbiddenLinks.slice(0, 3).join(', ')}`);
    }
  }
}

(async () => {
  for (const slug of standardProductSlugs) {
    await assertProduct(`/product/${slug}/`, {
      requireConfiguration: true,
      ...(productExpectations[slug] || {}),
    });
  }

  for (const slug of accessoryProductSlugs) {
    await assertProduct(`/product/${slug}/`);
  }

  await assertSupportContent();
  await assertReferenceContent();
  await assertReferenceComponentContracts();

  console.log('Product and editable content smoke passed.');
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
