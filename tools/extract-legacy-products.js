const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const crawlMapPath = path.join(root, 'docs', 'migration-map.csv');
const legacyRoot = path.resolve(root, '..', 'Arctic-spas', 'www');
const outputPath = path.join(root, 'wp-content', 'uploads', 'import', 'legacy-content', 'product-data.json');

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

function decodeEntities(value) {
  return value
    .replace(/&nbsp;/g, ' ')
    .replace(/&amp;/g, '&')
    .replace(/&quot;/g, '"')
    .replace(/&Prime;|&prime;/g, '"')
    .replace(/&reg;/g, '®')
    .replace(/&trade;/g, '™')
    .replace(/&#43;/g, '+');
}

function cleanHtml(value) {
  return decodeEntities(value || '')
    .replace(/<script[\s\S]*?<\/script>/gi, ' ')
    .replace(/<style[\s\S]*?<\/style>/gi, ' ')
    .replace(/<[^>]+>/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();
}

function firstMatch(input, pattern) {
  const match = input.match(pattern);
  return match ? cleanHtml(match[1]) : '';
}

function extractRows(input) {
  const rows = [];
  const rowPattern = /<tr[^>]*>([\s\S]*?)<\/tr>/gi;
  let rowMatch;

  while ((rowMatch = rowPattern.exec(input)) !== null) {
    const cells = [...rowMatch[1].matchAll(/<(th|td)[^>]*>([\s\S]*?)<\/\1>/gi)].map((match) => cleanHtml(match[2]));
    if (cells.length >= 2 && cells[0]) {
      rows.push({
        label: cells[0].replace(/\s*:$/, ''),
        value: cells.slice(1).filter(Boolean).join(' '),
      });
    }
  }

  return rows;
}

function extractProduct(sourcePath) {
  const relativePath = sourcePath.replace(/^\//, '');
  const filePath = path.join(legacyRoot, relativePath);
  if (!filePath.startsWith(legacyRoot) || !fs.existsSync(filePath)) {
    return null;
  }

  const html = fs.readFileSync(filePath, 'utf8');
  const paragraphs = [...html.matchAll(/<p[^>]*>([\s\S]*?)<\/p>/gi)]
    .map((match) => cleanHtml(match[1]))
    .filter(Boolean)
    .filter((text) => !/^&nbsp;$/.test(text))
    .filter((text) => !/^Menu\s+Domů(?:\s|$)/.test(text))
    .slice(0, 4);

  return {
    sourcePath,
    title: firstMatch(html, /<title[^>]*>([\s\S]*?)<\/title>/i),
    metaDescription: firstMatch(html, /<meta[^>]+name=["']description["'][^>]+content=["']([^"']*)["'][^>]*>/i),
    h1: firstMatch(html, /<h1[^>]*class=["']title["'][^>]*>([\s\S]*?)<\/h1>/i),
    lead: firstMatch(html, /<h5[^>]*>([\s\S]*?)<\/h5>/i),
    paragraphs,
    parameters: extractRows(html).slice(0, 12),
  };
}

function main() {
  const map = parseCsv(fs.readFileSync(crawlMapPath, 'utf8'));
  const productRows = map.filter((row) => ['migrate_product', 'migrate_product_or_landing'].includes(row.NewAction));
  const products = {};

  for (const row of productRows) {
    const slug = row.SourcePath.replace(/^\//, '').replace(/\.php$/, '');
    const product = extractProduct(row.SourcePath);
    if (product) {
      products[slug] = product;
    }
  }

  fs.mkdirSync(path.dirname(outputPath), { recursive: true });
  fs.writeFileSync(outputPath, JSON.stringify(products, null, 2) + '\n', 'utf8');

  console.log(`Wrote ${path.relative(root, outputPath)} with ${Object.keys(products).length} products.`);
}

main();
