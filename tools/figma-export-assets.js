const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..');
const FIGMA_FILE_KEY = 'xeOew3dFjDVfjXZrJ09emM';
const OUT_DIR = path.join(ROOT, 'assets-source', 'figma', 'export', 'graphics');
const WP_IMPORT_DIR = path.join(ROOT, 'wp-content', 'uploads', 'import', 'figma');
const DOCS_DIR = path.join(ROOT, 'docs');

const ASSETS = [
  { id: '1:1835', name: 'logo-arctic-spas-header', format: 'svg', themeTarget: 'wp-content/themes/arctic/images/logo.svg' },
  { id: '1:15', name: 'hp-hero-arctic-spas-07', format: 'png', scale: 1 },
  { id: '1:33', name: 'hp-category-virivky', format: 'png', scale: 2 },
  { id: '1:34', name: 'hp-category-celorocni-bazeny', format: 'png', scale: 2 },
  { id: '1:254', name: 'hp-fixed-banner-product', format: 'png', scale: 3 },
  { id: '1:123', name: 'showroom-1', format: 'png', scale: 2 },
  { id: '1:124', name: 'showroom-2', format: 'png', scale: 2 },
  { id: '1:125', name: 'showroom-3', format: 'png', scale: 2 },
  { id: '1:179', name: 'realizace-1', format: 'png', scale: 2 },
  { id: '1:187', name: 'realizace-2', format: 'png', scale: 2 },
  { id: '1:195', name: 'realizace-3', format: 'png', scale: 2 },
  { id: '1:210', name: 'footer-background', format: 'png', scale: 1 },
  { id: '1:242', name: 'footer-map', format: 'png', scale: 2 },
  { id: '1:263', name: 'category-hero-virivky', format: 'png', scale: 1 },
  { id: '1:273', name: 'category-vlastnosti', format: 'png', scale: 2 },
  { id: '1:274', name: 'category-zaruka', format: 'png', scale: 2 },
  { id: '1:275', name: 'category-product-card-1', format: 'png', scale: 2 },
  { id: '1:280', name: 'category-product-card-2', format: 'png', scale: 2 },
  { id: '1:285', name: 'category-product-card-3', format: 'png', scale: 2 },
  { id: '1:409', name: 'category-configurator', format: 'png', scale: 2 },
  { id: '1:1462', name: 'detail-timberwolf-hero', format: 'png', scale: 1 },
  { id: '1:1472', name: 'detail-timberwolf-prestige', format: 'png', scale: 3 },
  { id: '1:1474', name: 'detail-timberwolf-signature', format: 'png', scale: 3 },
  { id: '1:1476', name: 'color-dakota', format: 'png', scale: 3 },
  { id: '1:1479', name: 'color-kalahari', format: 'png', scale: 3 },
  { id: '1:1482', name: 'color-odyssey', format: 'png', scale: 3 },
  { id: '1:1485', name: 'color-platinum-swirl', format: 'png', scale: 3 },
  { id: '1:1488', name: 'color-espresso', format: 'png', scale: 3 },
  { id: '1:1492', name: 'cabinet-cedar', format: 'png', scale: 3 },
  { id: '1:1495', name: 'cabinet-maintenance-free', format: 'png', scale: 3 },
  { id: '1:917', name: 'support-download-1', format: 'png', scale: 3 },
  { id: '1:918', name: 'support-download-2', format: 'png', scale: 3 },
  { id: '1:919', name: 'support-download-3', format: 'png', scale: 3 },
  { id: '1:1069', name: 'contact-map-showroom', format: 'png', scale: 1 },
  { id: '1:1977', name: 'mobile-logo-arctic-spas', format: 'svg' },
  { id: '1:1974', name: 'mobile-hp-hero', format: 'png', scale: 1 },
  { id: '1:2000', name: 'mobile-category-virivky', format: 'png', scale: 2 },
  { id: '1:2001', name: 'mobile-category-celorocni-bazeny', format: 'png', scale: 2 },
];

function readToken() {
  const envPath = path.join(ROOT, '.env.local');
  if (!fs.existsSync(envPath)) {
    throw new Error('.env.local was not found');
  }

  const tokenLine = fs.readFileSync(envPath, 'utf8').split(/\r?\n/).find((line) => line.startsWith('FIGMA_TOKEN='));
  const token = tokenLine ? tokenLine.replace(/^FIGMA_TOKEN=/, '').trim() : '';
  if (!token) {
    throw new Error('FIGMA_TOKEN is missing in .env.local');
  }

  return token;
}

async function figmaFetch(token, url) {
  for (let attempt = 1; attempt <= 6; attempt++) {
    const response = await fetch(url, {
      headers: {
        'X-Figma-Token': token,
      },
    });

    if (response.ok) {
      return response.json();
    }

    const body = await response.text();
    if (response.status === 429 && attempt < 6) {
      const waitMs = attempt * 15000;
      console.log(`Figma rate limit hit, retrying in ${waitMs / 1000}s (${attempt}/5).`);
      await new Promise((resolve) => setTimeout(resolve, waitMs));
      continue;
    }

    throw new Error(`Figma request failed ${response.status}: ${body}`);
  }
}

async function download(url, destination) {
  const response = await fetch(url);
  if (!response.ok) {
    throw new Error(`Download failed ${response.status}: ${url}`);
  }

  const buffer = Buffer.from(await response.arrayBuffer());
  fs.mkdirSync(path.dirname(destination), { recursive: true });
  fs.writeFileSync(destination, buffer);
}

function targetPath(asset, baseDir) {
  return path.join(baseDir, `${asset.name}.${asset.format}`);
}

function groupKey(asset) {
  return `${asset.format}:${asset.scale || 1}`;
}

async function exportGroup(token, assets) {
  const sample = assets[0];
  const url = new URL(`https://api.figma.com/v1/images/${FIGMA_FILE_KEY}`);
  url.searchParams.set('ids', assets.map((asset) => asset.id).join(','));
  url.searchParams.set('format', sample.format);
  url.searchParams.set('contents_only', 'true');

  if (sample.format === 'png') {
    url.searchParams.set('scale', String(sample.scale || 1));
  }

  if (sample.format === 'svg') {
    url.searchParams.set('svg_include_id', 'true');
    url.searchParams.set('svg_simplify_stroke', 'true');
  }

  const response = await figmaFetch(token, url.toString());
  const exported = [];

  for (const asset of assets) {
    const imageUrl = response.images && response.images[asset.id];
    if (!imageUrl) {
      exported.push({
        ...asset,
        ok: false,
        reason: 'Figma did not return an image URL for this node',
      });
      continue;
    }

    const archiveDestination = targetPath(asset, OUT_DIR);
    const wpDestination = targetPath(asset, WP_IMPORT_DIR);

    await download(imageUrl, archiveDestination);
    fs.mkdirSync(path.dirname(wpDestination), { recursive: true });
    fs.copyFileSync(archiveDestination, wpDestination);

    if (asset.themeTarget) {
      const themeDestination = path.join(ROOT, asset.themeTarget);
      fs.mkdirSync(path.dirname(themeDestination), { recursive: true });
      fs.copyFileSync(archiveDestination, themeDestination);
    }

    exported.push({
      id: asset.id,
      name: asset.name,
      format: asset.format,
      scale: asset.scale || 1,
      ok: true,
      archivePath: path.relative(ROOT, archiveDestination).replace(/\\/g, '/'),
      wpImportPath: path.relative(ROOT, wpDestination).replace(/\\/g, '/'),
      themeTarget: asset.themeTarget || null,
    });
  }

  return exported;
}

async function main() {
  const token = readToken();
  fs.mkdirSync(OUT_DIR, { recursive: true });
  fs.mkdirSync(WP_IMPORT_DIR, { recursive: true });
  fs.mkdirSync(DOCS_DIR, { recursive: true });

  const groups = new Map();
  for (const asset of ASSETS) {
    const key = groupKey(asset);
    if (!groups.has(key)) {
      groups.set(key, []);
    }
    groups.get(key).push(asset);
  }

  const exported = [];
  for (const assets of groups.values()) {
    exported.push(...await exportGroup(token, assets));
    await new Promise((resolve) => setTimeout(resolve, 2000));
  }

  const report = {
    generatedAt: new Date().toISOString(),
    figmaFileKey: FIGMA_FILE_KEY,
    exported,
  };

  const reportPath = path.join(DOCS_DIR, 'figma-exported-assets.json');
  fs.writeFileSync(reportPath, JSON.stringify(report, null, 2));

  const ok = exported.filter((asset) => asset.ok).length;
  const failed = exported.length - ok;
  console.log(`Exported ${ok} Figma assets to ${path.relative(ROOT, OUT_DIR)} and ${path.relative(ROOT, WP_IMPORT_DIR)}.`);
  if (failed > 0) {
    console.log(`${failed} assets were not exported. See ${path.relative(ROOT, reportPath)}.`);
  }
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
