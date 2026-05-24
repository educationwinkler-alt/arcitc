const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const crawlPath = path.join(root, 'docs', 'crawl-live', 'arctic-spas-live-crawl.csv');
const redirectsPath = path.join(root, 'wp-content', 'mu-plugins', 'arctic-redirects.php');
const outputPath = path.join(root, 'docs', 'migration-map.csv');

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

function toCsv(rows) {
  const headers = [
    'SourceUrl',
    'SourcePath',
    'FinalUrl',
    'Status',
    'ContentType',
    'Title',
    'H1',
    'SourceType',
    'CrawlAction',
    'NewAction',
    'NewTarget',
    'Bucket',
    'Note',
  ];

  const escape = (value) => `"${String(value ?? '').replace(/"/g, '""')}"`;
  return [
    headers.map(escape).join(','),
    ...rows.map((row) => headers.map((header) => escape(row[header])).join(',')),
  ].join('\n') + '\n';
}

function parseRedirects(input) {
  const redirects = new Map();
  const redirectPattern = /'([^']+)'\s*=>\s*'([^']+)'/g;
  let match;

  while ((match = redirectPattern.exec(input)) !== null) {
    redirects.set(match[1], match[2]);
  }

  return redirects;
}

function getPath(url) {
  try {
    return new URL(url).pathname;
  } catch (error) {
    return '';
  }
}

function classify(row, sourcePath, redirectTarget) {
  const type = row.Type;
  const crawlAction = row.MigrationAction;

  if (type === 'home') {
    return ['migrate_page', '/', 'home', 'Homepage is rebuilt in the Figma HP template.'];
  }

  if (type === 'product_hot_tub') {
    return ['migrate_product', redirectTarget, 'product_hot_tub', 'Active hot tub product in the WordPress product CPT.'];
  }

  if (type === 'product_swimspa') {
    return ['migrate_product', redirectTarget, 'product_swimspa', 'Active swimspa product in the WordPress product CPT.'];
  }

  if (type === 'product_other_sortiment') {
    return ['migrate_product_or_landing', redirectTarget, 'product_other_sortiment', 'Wider assortment item in the shared product CPT or landing mode.'];
  }

  if (type === 'retired') {
    return ['redirect_retired', redirectTarget || '/catalog/virivky/', 'retired_product', 'Retired product; do not create an active product page.'];
  }

  if (type === 'download_asset') {
    return ['import_download', redirectTarget || '/ke-stazeni/', 'download', 'Import as download CPT/media; old PDF URL redirects dynamically when media exists.'];
  }

  if (redirectTarget) {
    return ['redirect_consolidated', redirectTarget, 'content_redirect', 'Consolidated into an existing Figma page or product/category page.'];
  }

  if (crawlAction === 'migrate_page') {
    return ['migrate_page', row.SuggestedTarget || '/', 'content_page', 'Primary content page rebuilt in a Figma template.'];
  }

  return ['review_content', '', 'content_review', 'Needs manual content decision before production import.'];
}

function main() {
  const crawl = parseCsv(fs.readFileSync(crawlPath, 'utf8'));
  const redirects = parseRedirects(fs.readFileSync(redirectsPath, 'utf8'));

  const rows = crawl.map((row) => {
    const sourcePath = getPath(row.Url);
    const redirectTarget = redirects.get(sourcePath) || row.SuggestedTarget || '';
    const [newAction, newTarget, bucket, generatedNote] = classify(row, sourcePath, redirectTarget);

    return {
      SourceUrl: row.Url,
      SourcePath: sourcePath,
      FinalUrl: row.FinalUrl,
      Status: row.Status,
      ContentType: row.ContentType,
      Title: row.Title,
      H1: row.H1,
      SourceType: row.Type,
      CrawlAction: row.MigrationAction,
      NewAction: newAction,
      NewTarget: newTarget,
      Bucket: bucket,
      Note: generatedNote,
    };
  });

  fs.writeFileSync(outputPath, toCsv(rows), 'utf8');

  const counts = rows.reduce((acc, row) => {
    acc[row.NewAction] = (acc[row.NewAction] || 0) + 1;
    return acc;
  }, {});

  console.log(`Wrote ${path.relative(root, outputPath)} with ${rows.length} rows.`);
  Object.entries(counts)
    .sort((a, b) => b[1] - a[1])
    .forEach(([action, count]) => console.log(`${action}: ${count}`));
}

main();
