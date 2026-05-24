const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const migrationMapPath = path.join(root, 'docs', 'migration-map.csv');
const finalLocationChecks = new Map();

const redirectActions = new Set([
  'redirect_consolidated',
  'redirect_retired',
  'migrate_product',
  'migrate_product_or_landing',
  'import_download',
  'skip_missing_download',
]);

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

function localUrl(target) {
  return new URL(target, baseUrl).toString();
}

function stripHash(url) {
  return url.replace(/#.*/, '');
}

async function fetchWithTimeout(url, options = {}) {
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), 10000);

  try {
    return await fetch(url, {
      ...options,
      signal: controller.signal,
    });
  } finally {
    clearTimeout(timeout);
  }
}

async function assertFinalTarget(location, row) {
  if (!finalLocationChecks.has(location)) {
    finalLocationChecks.set(location, (async () => {
      const finalResponse = await fetchWithTimeout(location, { method: 'HEAD', redirect: 'manual' });

      if (finalResponse.status >= 400) {
        throw new Error(`${row.SourcePath} redirects to ${location}, which returns HTTP ${finalResponse.status}.`);
      }

      if (finalResponse.status >= 300 && finalResponse.status < 400) {
        throw new Error(`${row.SourcePath} redirects to ${location}, which still redirects.`);
      }
    })());
  }

  await finalLocationChecks.get(location);
}

async function checkRow(row) {
  const requestUrl = localUrl(row.SourcePath);
  const response = await fetchWithTimeout(requestUrl, { method: 'HEAD', redirect: 'manual' });
  const location = response.headers.get('location') || '';

  if (response.status !== 301) {
    throw new Error(`${row.SourcePath} returned HTTP ${response.status}, expected 301.`);
  }

  if (!location.startsWith(baseUrl)) {
    throw new Error(`${row.SourcePath} redirects outside local WordPress: ${location || 'missing Location header'}.`);
  }

  if (/baspa\.cz|arctic-spas\.cz/i.test(location)) {
    throw new Error(`${row.SourcePath} redirects to a live/reference domain: ${location}.`);
  }

  if (row.NewAction === 'import_download') {
    if (!location.startsWith(`${baseUrl}/wp-content/uploads/`) || !location.toLowerCase().endsWith('.pdf')) {
      throw new Error(`${row.SourcePath} PDF redirects to unexpected location: ${location}.`);
    }
  } else {
    const expected = localUrl(row.NewTarget || '/ke-stazeni/');
    if (stripHash(location) !== stripHash(expected)) {
      throw new Error(`${row.SourcePath} redirects to ${location}, expected ${expected}.`);
    }
  }

  await assertFinalTarget(location, row);
}

async function runWithConcurrency(items, limit, worker) {
  let index = 0;
  const workers = Array.from({ length: limit }, async () => {
    while (index < items.length) {
      const item = items[index];
      index += 1;
      await worker(item);
    }
  });

  await Promise.all(workers);
}

(async () => {
  const rows = parseCsv(fs.readFileSync(migrationMapPath, 'utf8'));
  const unsupported = rows.filter((row) => row.NewAction === 'review_content');
  if (unsupported.length) {
    throw new Error(`Migration map still contains review_content rows: ${unsupported.map((row) => row.SourcePath).join(', ')}`);
  }

  const redirectRows = rows.filter((row) => redirectActions.has(row.NewAction) && row.SourcePath !== '/');
  await runWithConcurrency(redirectRows, 6, checkRow);

  const homeRows = rows.filter((row) => row.NewAction === 'migrate_page');
  for (const row of homeRows) {
    const response = await fetchWithTimeout(localUrl(row.SourcePath), { method: 'HEAD', redirect: 'manual' });
    if (response.status >= 400) {
      throw new Error(`${row.SourcePath} home/page target returned HTTP ${response.status}.`);
    }
  }

  console.log(`Redirect smoke passed for ${redirectRows.length} legacy URLs.`);
})();
