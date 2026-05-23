const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..');
const OUT_DIR = path.join(ROOT, 'assets-source', 'figma', 'export');
const DOCS_DIR = path.join(ROOT, 'docs');

const FILES = [
  {
    key: 'xeOew3dFjDVfjXZrJ09emM',
    label: 'grafika',
    ids: ['1:1831', '1:14', '1:262', '1:1461', '1:752', '1:1037', '1:1973', '1:2208'],
  },
  {
    key: 'puPBNFpuaXpRZR2TINaDvm',
    label: 'wireframe',
    ids: ['58:87', '100:1504', '100:662', '124:1926', '124:3882', '11:286'],
  },
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
  const response = await fetch(url, {
    headers: {
      'X-Figma-Token': token,
    },
  });

  if (!response.ok) {
    const body = await response.text();
    throw new Error(`Figma request failed ${response.status}: ${body}`);
  }

  return response.json();
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

function safeName(value) {
  return value
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '')
    .slice(0, 80) || 'node';
}

function shortNode(node) {
  return {
    id: node.id,
    name: node.name,
    type: node.type,
    visible: node.visible !== false,
    absoluteBoundingBox: node.absoluteBoundingBox || null,
    characters: node.type === 'TEXT' ? node.characters : undefined,
    fills: Array.isArray(node.fills)
      ? node.fills.map((fill) => ({
          type: fill.type,
          visible: fill.visible !== false,
          color: fill.color,
          imageRef: fill.imageRef,
          scaleMode: fill.scaleMode,
        }))
      : undefined,
    fontSize: node.style && node.style.fontSize,
    fontFamily: node.style && node.style.fontFamily,
    fontWeight: node.style && node.style.fontWeight,
    lineHeightPx: node.style && node.style.lineHeightPx,
    children: Array.isArray(node.children) ? node.children.length : 0,
  };
}

function walk(node, cb, depth = 0, ancestors = []) {
  cb(node, depth, ancestors);
  if (!Array.isArray(node.children)) {
    return;
  }
  for (const child of node.children) {
    walk(child, cb, depth + 1, ancestors.concat(node.name));
  }
}

function collectSummary(label, data) {
  const nodeSummaries = [];
  const logoCandidates = [];
  const textNodes = [];
  const images = [];
  const pageSectionMap = [];

  for (const [requestedId, wrapper] of Object.entries(data.nodes || {})) {
    const rootNode = wrapper.document;
    if (!rootNode) {
      continue;
    }

    pageSectionMap.push({
      requestedId,
      root: shortNode(rootNode),
      children: (rootNode.children || []).map(shortNode),
    });

    walk(rootNode, (node, depth, ancestors) => {
      const summary = shortNode(node);
      summary.depth = depth;
      summary.ancestors = ancestors.slice(-4);
      nodeSummaries.push(summary);

      const nameAndText = `${node.name || ''} ${node.characters || ''}`;
      const box = node.absoluteBoundingBox || {};
      const hasImageFill = Array.isArray(node.fills) && node.fills.some((fill) => fill.type === 'IMAGE');
      const looksLogo = /logo|arcticspas|arctic spas|arctic\s*spa/i.test(nameAndText);
      const reasonableLogoSize =
        Number(box.width || 0) >= 40 &&
        Number(box.width || 0) <= 700 &&
        Number(box.height || 0) >= 12 &&
        Number(box.height || 0) <= 240;

      if (node.type === 'TEXT' && typeof node.characters === 'string') {
        textNodes.push(summary);
      }

      if (hasImageFill) {
        images.push(summary);
      }

      if ((looksLogo || (hasImageFill && /logo/i.test(nameAndText))) && reasonableLogoSize) {
        logoCandidates.push(summary);
      }
    });
  }

  return {
    label,
    generatedAt: new Date().toISOString(),
    pageSectionMap,
    logoCandidates,
    imageNodes: images,
    textNodes: textNodes.slice(0, 400),
    nodes: nodeSummaries,
  };
}

async function exportCandidates(token, file, candidates) {
  const exportable = candidates
    .filter((candidate) => candidate.visible !== false)
    .filter((candidate) => candidate.type !== 'TEXT')
    .slice(0, 24);

  if (!exportable.length) {
    return [];
  }

  const ids = exportable.map((candidate) => candidate.id);
  const exported = [];

  for (const format of ['svg', 'png']) {
    const url = new URL(`https://api.figma.com/v1/images/${file.key}`);
    url.searchParams.set('ids', ids.join(','));
    url.searchParams.set('format', format);
    if (format === 'png') {
      url.searchParams.set('scale', '4');
    }
    if (format === 'svg') {
      url.searchParams.set('svg_include_id', 'true');
      url.searchParams.set('svg_simplify_stroke', 'true');
    }

    const response = await figmaFetch(token, url.toString());
    for (const candidate of exportable) {
      const imageUrl = response.images && response.images[candidate.id];
      if (!imageUrl) {
        continue;
      }

      const filename = `${file.label}-${safeName(candidate.name)}-${candidate.id.replace(':', '-')}.${format}`;
      const destination = path.join(OUT_DIR, 'candidates', filename);
      await download(imageUrl, destination);
      exported.push({
        id: candidate.id,
        name: candidate.name,
        type: candidate.type,
        format,
        path: path.relative(ROOT, destination).replace(/\\/g, '/'),
      });
    }
  }

  return exported;
}

async function main() {
  const token = readToken();
  fs.mkdirSync(OUT_DIR, { recursive: true });
  fs.mkdirSync(DOCS_DIR, { recursive: true });

  const combined = [];

  for (const file of FILES) {
    const url = new URL(`https://api.figma.com/v1/files/${file.key}/nodes`);
    url.searchParams.set('ids', file.ids.join(','));
    const data = await figmaFetch(token, url.toString());
    const rawPath = path.join(DOCS_DIR, `figma-${file.label}-nodes.raw.json`);
    fs.writeFileSync(rawPath, JSON.stringify(data, null, 2));

    const summary = collectSummary(file.label, data);
    summary.exportedLogoCandidates = await exportCandidates(token, file, summary.logoCandidates);

    const summaryPath = path.join(DOCS_DIR, `figma-${file.label}-nodes.summary.json`);
    fs.writeFileSync(summaryPath, JSON.stringify(summary, null, 2));

    combined.push({
      label: file.label,
      key: file.key,
      requestedIds: file.ids,
      rawPath: path.relative(ROOT, rawPath).replace(/\\/g, '/'),
      summaryPath: path.relative(ROOT, summaryPath).replace(/\\/g, '/'),
      logoCandidates: summary.logoCandidates.map(({ id, name, type, absoluteBoundingBox }) => ({
        id,
        name,
        type,
        absoluteBoundingBox,
      })),
      exportedLogoCandidates: summary.exportedLogoCandidates,
      topSections: summary.pageSectionMap,
    });
  }

  const combinedPath = path.join(DOCS_DIR, 'figma-extraction-report.json');
  fs.writeFileSync(combinedPath, JSON.stringify(combined, null, 2));

  for (const item of combined) {
    console.log(`${item.label}: ${item.logoCandidates.length} logo candidates, ${item.exportedLogoCandidates.length} exported files`);
  }
  console.log(`Wrote ${path.relative(ROOT, combinedPath)}`);
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
