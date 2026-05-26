const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const legacyRoot = path.resolve(root, '..', 'Arctic-spas', 'www');
const migrationMapPath = path.join(root, 'docs', 'migration-map.csv');
const productDataPath = path.join(root, 'wp-content', 'uploads', 'import', 'legacy-content', 'product-data.json');
const downloadsImportRoot = path.join(root, 'wp-content', 'uploads', 'import', 'downloads');
const legacyProductsRoot = path.join(root, 'wp-content', 'uploads', 'import', 'legacy-products');

const expectedActionCounts = {
  migrate_page: 2,
  redirect_consolidated: 75,
  migrate_product: 22,
  import_download: 26,
  skip_missing_download: 1,
  migrate_product_or_landing: 4,
  redirect_retired: 5,
};

const legacyProductExceptions = new Map([
  ['virivka-lunar', 'New 2025 Core model; old local Arctic archive does not contain this PHP page.'],
  ['virivka-orion', 'New 2025 Core model; old local Arctic archive does not contain this PHP page.'],
]);

const expectedRedirectTargets = {
  '/diskuze.php': '/reference/',
  '/download.php': '/ke-stazeni/',
  '/faq.php': '/podpora/',
  '/kontakt.php': '/kontakt/',
  '/prodejna-bazeny-virivky.php': '/showroom/',
  '/cookies.php': '/ochrana-osobnich-udaju/',
  '/zasady-zpracovani-osobnich-udaju.php': '/ochrana-osobnich-udaju/',
};

const contentSourceFiles = [
  'faq.php',
  'diskuze.php',
  'kontakt.php',
  'prodejna-bazeny-virivky.php',
  'cookies.php',
  'zasady-zpracovani-osobnich-udaju.php',
];

const pageExpectations = [
  {
    path: '/reference/',
    contains: ['arctic fox po letech provozu', 'swimspa wolverine', 'novorocni prani'],
  },
  {
    path: '/podpora/',
    contains: ['caste dotazy', 'servisni formular', 'jak probiha vyber'],
  },
  {
    path: '/ke-stazeni/',
    contains: ['ke stazeni', 'dokument arctic spas', 'navod arctic'],
  },
  {
    path: '/kontakt/',
    contains: ['bohunicka cesta', 'moravany', 'baspa s.r.o.'],
  },
  {
    path: '/showroom/',
    contains: ['showroom', 'moravany', 'bohunicka cesta'],
  },
  {
    path: '/ochrana-osobnich-udaju/',
    contains: ['spravcem osobnich udaju', 'gdpr', 'baspa s.r.o.'],
  },
];

const forbiddenVisibleNeedles = [
  'lorem ipsum',
  'sample page',
  'hello world',
  'hello pattern',
  'example.com',
  'bude dopln',
  'bude doplneno',
  'todo',
  'tbd',
];

const forbiddenHtmlNeedles = [
  'https://baspa.cz',
  'https://www.baspa.cz',
  'https://www.arctic-spas.cz',
  'http://www.arctic-spas.cz',
  'api2.ecomailapp.cz',
];

function parseCsv(input) {
  const rows = [];
  let row = [];
  let cell = '';
  let quoted = false;

  for (let i = 0; i < input.length; i += 1) {
    const char = input[i];
    const next = input[i + 1];

    if (quoted && char === '"' && next === '"') {
      cell += '"';
      i += 1;
      continue;
    }

    if (char === '"') {
      quoted = !quoted;
      continue;
    }

    if (!quoted && char === ',') {
      row.push(cell);
      cell = '';
      continue;
    }

    if (!quoted && (char === '\n' || char === '\r')) {
      if (char === '\r' && next === '\n') {
        i += 1;
      }
      row.push(cell);
      if (row.some((value) => value !== '')) {
        rows.push(row);
      }
      row = [];
      cell = '';
      continue;
    }

    cell += char;
  }

  if (cell.length > 0 || row.length > 0) {
    row.push(cell);
    rows.push(row);
  }

  const [rawHeaders, ...records] = rows;
  const headers = rawHeaders.map((header) => header.replace(/^\uFEFF/, ''));
  return records.map((record) => Object.fromEntries(headers.map((header, index) => [header, record[index] || ''])));
}

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

function sourceSlug(sourcePath) {
  return sourcePath.replace(/^\//, '').replace(/\.php$/, '');
}

function countBy(rows, key) {
  return rows.reduce((counts, row) => {
    counts[row[key]] = (counts[row[key]] || 0) + 1;
    return counts;
  }, {});
}

function foldText(value) {
  return value
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/\s+/g, ' ')
    .trim();
}

function textFromHtml(html) {
  return html
    .replace(/<script[\s\S]*?<\/script>/gi, ' ')
    .replace(/<style[\s\S]*?<\/style>/gi, ' ')
    .replace(/<[^>]+>/g, ' ')
    .replace(/&nbsp;/g, ' ')
    .replace(/&amp;/g, '&')
    .replace(/\s+/g, ' ')
    .trim();
}

async function fetchHtml(route) {
  const response = await fetch(`${baseUrl}${route}`);

  if (!response.ok) {
    throw new Error(`${route} returned HTTP ${response.status}`);
  }

  return response.text();
}

function validateMigrationMap(rows) {
  assert(rows.length === 135, `migration-map.csv has ${rows.length} rows, expected 135.`);

  const actionCounts = countBy(rows, 'NewAction');
  for (const [action, expected] of Object.entries(expectedActionCounts)) {
    assert(actionCounts[action] === expected, `${action} count is ${actionCounts[action] || 0}, expected ${expected}.`);
  }

  for (const row of rows) {
    assert(row.SourcePath.startsWith('/'), `Invalid SourcePath: ${row.SourcePath}`);
    assert(!/^https?:\/\//i.test(row.NewTarget), `${row.SourcePath} targets a live absolute URL: ${row.NewTarget}`);
  }

  for (const [source, expectedTarget] of Object.entries(expectedRedirectTargets)) {
    const row = rows.find((entry) => entry.SourcePath === source);
    assert(row, `Missing migration row for ${source}.`);
    assert(row.NewTarget === expectedTarget, `${source} targets ${row.NewTarget}, expected ${expectedTarget}.`);
  }
}

function validateProductEvidence(rows) {
  const productRows = rows.filter((row) => ['migrate_product', 'migrate_product_or_landing'].includes(row.NewAction));
  const productData = JSON.parse(fs.readFileSync(productDataPath, 'utf8'));
  const expectedLegacyDataCount = productRows.length - legacyProductExceptions.size;

  assert(Object.keys(productData).length === expectedLegacyDataCount, `product-data.json has ${Object.keys(productData).length} products, expected ${expectedLegacyDataCount}.`);

  for (const row of productRows) {
    const slug = sourceSlug(row.SourcePath);
    const legacyFile = path.join(legacyRoot, `${slug}.php`);
    const hasException = legacyProductExceptions.has(slug);

    if (hasException) {
      assert(!fs.existsSync(legacyFile), `${slug} is marked as exception but exists in old archive.`);
      continue;
    }

    assert(fs.existsSync(legacyFile), `Missing old Arctic product source file: ${legacyFile}`);
    assert(productData[slug], `Missing extracted legacy product data for ${slug}.`);
    assert(productData[slug].h1, `${slug} is missing h1 in extracted product data.`);
    assert((productData[slug].paragraphs || []).length > 0, `${slug} is missing paragraph content in extracted product data.`);

    if (['product_hot_tub', 'product_swimspa'].includes(row.Bucket)) {
      assert((productData[slug].parameters || []).length > 0, `${slug} is missing parameter rows.`);
      assert(fs.existsSync(path.join(legacyProductsRoot, `${slug}.jpg`)), `${slug} is missing imported legacy product image.`);
    }
  }
}

function validateDownloadEvidence(rows) {
  const importRows = rows.filter((row) => row.NewAction === 'import_download');
  const missingRows = rows.filter((row) => row.NewAction === 'skip_missing_download');
  const importedPdfs = fs.readdirSync(downloadsImportRoot).filter((file) => file.toLowerCase().endsWith('.pdf'));

  assert(importedPdfs.length === importRows.length, `Imported PDF count is ${importedPdfs.length}, expected ${importRows.length}.`);
  assert(missingRows.length === 1, `Missing download exception count is ${missingRows.length}, expected 1.`);
  assert(missingRows[0].SourcePath === '/content/download/as-sluzby-cenik-2022.pdf', `Unexpected missing download: ${missingRows[0].SourcePath}`);
}

function validateLegacySources() {
  for (const file of contentSourceFiles) {
    assert(fs.existsSync(path.join(legacyRoot, file)), `Missing old Arctic content source: ${file}`);
  }
}

async function validateVisiblePages() {
  for (const expectation of pageExpectations) {
    const html = await fetchHtml(expectation.path);
    const foldedHtml = foldText(html);
    const foldedText = foldText(textFromHtml(html));

    for (const needle of forbiddenVisibleNeedles) {
      assert(!foldedText.includes(needle), `${expectation.path} contains placeholder visible text: ${needle}`);
    }

    for (const needle of forbiddenHtmlNeedles) {
      assert(!foldedHtml.includes(needle), `${expectation.path} contains forbidden live/reference host: ${needle}`);
    }

    for (const expected of expectation.contains) {
      assert(foldedText.includes(expected), `${expectation.path} is missing expected Arctic content: ${expected}`);
    }
  }
}

(async () => {
  assert(fs.existsSync(legacyRoot), `Old Arctic archive is missing: ${legacyRoot}`);
  assert(fs.existsSync(migrationMapPath), 'docs/migration-map.csv is missing.');
  assert(fs.existsSync(productDataPath), 'legacy product-data.json is missing.');
  assert(fs.existsSync(downloadsImportRoot), 'Imported downloads directory is missing.');
  assert(fs.existsSync(legacyProductsRoot), 'Imported legacy products directory is missing.');

  const rows = parseCsv(fs.readFileSync(migrationMapPath, 'utf8'));

  validateMigrationMap(rows);
  validateProductEvidence(rows);
  validateDownloadEvidence(rows);
  validateLegacySources();
  await validateVisiblePages();

  console.log('Content parity audit passed.');
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
