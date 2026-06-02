const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..');
const RAW_PATHS = [
  path.join(ROOT, 'docs', 'figma-grafika-nodes.raw.json'),
  path.join(ROOT, 'docs', 'grafika-missing-pages.raw.json'),
  path.join(ROOT, 'docs', 'grafika-remaining-pages.raw.json'),
];
const LOCAL_IMAGES_DIR = path.join(ROOT, 'assets-source', 'figma', 'local-grafika', 'images');
const OUT_DIR = path.join(ROOT, 'assets-source', 'figma', 'export', 'graphics');
const WP_IMPORT_DIR = path.join(ROOT, 'wp-content', 'uploads', 'import', 'figma');
const REPORT_PATH = path.join(ROOT, 'docs', 'figma-local-exported-assets.json');

const ASSETS = [
  { id: '1:1835', name: 'logo-arctic-spas-header', svgSource: 'assets-source/figma/export/logo-arctic-spas.svg', themeTarget: 'wp-content/themes/arctic/images/logo.svg' },
  { id: '1:15', name: 'hp-hero-arctic-spas-07' },
  { id: '1:33', name: 'hp-category-virivky' },
  { id: '1:34', name: 'hp-category-celorocni-bazeny' },
  { id: '1:254', name: 'hp-fixed-banner-product' },
  { id: '1:50', name: 'contact-lukas-dusek' },
  { id: '1:123', name: 'showroom-1' },
  { id: '1:124', name: 'showroom-2' },
  { id: '1:125', name: 'showroom-3' },
  { id: '1:179', name: 'realizace-1' },
  { id: '1:187', name: 'realizace-2' },
  { id: '1:195', name: 'realizace-3' },
  { id: '1:210', name: 'footer-background' },
  { id: '1:242', name: 'footer-map' },
  { id: '1:263', name: 'category-hero-virivky' },
  { id: '1:273', name: 'category-vlastnosti' },
  { id: '1:274', name: 'category-zaruka' },
  { id: '1:275', name: 'category-product-card-1' },
  { id: '1:280', name: 'category-product-card-2' },
  { id: '1:285', name: 'category-product-card-3' },
  { id: '1:409', name: 'category-configurator' },
  { id: '1:1462', name: 'detail-timberwolf-hero' },
  { id: '1:1472', name: 'detail-timberwolf-prestige' },
  { id: '1:1474', name: 'detail-timberwolf-signature' },
  { id: '1:1476', name: 'color-dakota' },
  { id: '1:1479', name: 'color-kalahari' },
  { id: '1:1482', name: 'color-odyssey' },
  { id: '1:1485', name: 'color-platinum-swirl' },
  { id: '1:1488', name: 'color-espresso' },
  { id: '1:1492', name: 'cabinet-cedar' },
  { id: '1:1495', name: 'cabinet-maintenance-free' },
  { id: '1:917', name: 'support-download-1' },
  { id: '1:918', name: 'support-download-2' },
  { id: '1:919', name: 'support-download-3' },
  { id: '1:1069', name: 'contact-map-showroom' },
  { id: '1:1977', name: 'mobile-logo-arctic-spas', svgSource: 'assets-source/figma/export/logo-arctic-spas.svg' },
  { id: '1:1974', name: 'mobile-hp-hero' },
  { id: '1:2000', name: 'mobile-category-virivky' },
  { id: '1:2001', name: 'mobile-category-celorocni-bazeny' },
  { id: '1:1327', name: 'feature-freeheat-diagram' },
  { id: '1:716', name: 'certificate-tuv-1' },
  { id: '1:717', name: 'certificate-tuv-2' },
  { id: '1:718', name: 'certificate-tuv-3' },
  { id: '1:446', name: 'showroom-hero-bazeny' },
  { id: '1:443', name: 'showroom-detail-bazeny' },
  { id: '1:444', name: 'showroom-detail-virivky' },
  { id: '1:987', name: 'about-team-vladimir' },
  { id: '1:1003', name: 'about-team-lukas' },
  { id: '1:1004', name: 'about-team-helena' },
  { id: '1:985', name: 'about-team-alena' },
];

function walk(node, callback) {
  callback(node);
  for (const child of node.children || []) {
    walk(child, callback);
  }
}

function buildNodeMap(raws) {
  const nodes = new Map();
  for (const raw of raws) {
    for (const wrapper of Object.values(raw.nodes || {})) {
      if (!wrapper.document) {
        continue;
      }
      walk(wrapper.document, (node) => nodes.set(node.id, node));
    }
  }
  return nodes;
}

function findImageRef(node) {
  const direct = (node.fills || []).find((fill) => fill.type === 'IMAGE' && fill.visible !== false && fill.imageRef);
  if (direct) {
    return { imageRef: direct.imageRef, nodeId: node.id, nodeName: node.name, mode: 'direct' };
  }

  const queue = [...(node.children || [])];
  while (queue.length) {
    const current = queue.shift();
    const fill = (current.fills || []).find((item) => item.type === 'IMAGE' && item.visible !== false && item.imageRef);
    if (fill) {
      return { imageRef: fill.imageRef, nodeId: current.id, nodeName: current.name, mode: 'descendant' };
    }
    queue.push(...(current.children || []));
  }

  return null;
}

function extensionFor(buffer) {
  if (buffer[0] === 0xff && buffer[1] === 0xd8) {
    return 'jpg';
  }
  if (buffer[0] === 0x89 && buffer[1] === 0x50 && buffer[2] === 0x4e && buffer[3] === 0x47) {
    return 'png';
  }
  if (buffer.subarray(0, 4).toString('ascii') === 'RIFF' && buffer.subarray(8, 12).toString('ascii') === 'WEBP') {
    return 'webp';
  }
  return 'bin';
}

function copyFile(source, destination) {
  fs.mkdirSync(path.dirname(destination), { recursive: true });
  fs.copyFileSync(source, destination);
}

function relative(value) {
  return path.relative(ROOT, value).replace(/\\/g, '/');
}

function exportSvg(asset) {
  const source = path.join(ROOT, asset.svgSource);
  if (!fs.existsSync(source)) {
    return {
      id: asset.id,
      name: asset.name,
      ok: false,
      reason: `Missing SVG source ${asset.svgSource}`,
    };
  }

  const archiveDestination = path.join(OUT_DIR, `${asset.name}.svg`);
  const wpDestination = path.join(WP_IMPORT_DIR, `${asset.name}.svg`);
  copyFile(source, archiveDestination);
  copyFile(source, wpDestination);

  if (asset.themeTarget) {
    copyFile(source, path.join(ROOT, asset.themeTarget));
  }

  return {
    id: asset.id,
    name: asset.name,
    ok: true,
    format: 'svg',
    source: asset.svgSource,
    archivePath: relative(archiveDestination),
    wpImportPath: relative(wpDestination),
    themeTarget: asset.themeTarget || null,
  };
}

function main() {
  const raws = RAW_PATHS
    .filter((rawPath) => fs.existsSync(rawPath))
    .map((rawPath) => JSON.parse(fs.readFileSync(rawPath, 'utf8')));
  const nodes = buildNodeMap(raws);
  fs.mkdirSync(OUT_DIR, { recursive: true });
  fs.mkdirSync(WP_IMPORT_DIR, { recursive: true });

  const exported = ASSETS.map((asset) => {
    if (asset.svgSource) {
      return exportSvg(asset);
    }

    const node = nodes.get(asset.id);
    if (!node) {
      return {
        id: asset.id,
        name: asset.name,
        ok: false,
        reason: 'Node not found in docs/figma-grafika-nodes.raw.json',
      };
    }

    const ref = findImageRef(node);
    if (!ref) {
      return {
        id: asset.id,
        name: asset.name,
        ok: false,
        reason: 'No visible image fill found on node or descendants',
      };
    }

    const source = path.join(LOCAL_IMAGES_DIR, ref.imageRef);
    if (!fs.existsSync(source)) {
      return {
        id: asset.id,
        name: asset.name,
        ok: false,
        imageRef: ref.imageRef,
        reason: 'Image ref not found in local .fig images directory',
      };
    }

    const buffer = fs.readFileSync(source);
    const extension = extensionFor(buffer);
    const archiveDestination = path.join(OUT_DIR, `${asset.name}.${extension}`);
    const wpDestination = path.join(WP_IMPORT_DIR, `${asset.name}.${extension}`);

    copyFile(source, archiveDestination);
    copyFile(source, wpDestination);

    return {
      id: asset.id,
      name: asset.name,
      ok: true,
      format: extension,
      imageRef: ref.imageRef,
      imageNodeId: ref.nodeId,
      imageNodeName: ref.nodeName,
      imageMode: ref.mode,
      archivePath: relative(archiveDestination),
      wpImportPath: relative(wpDestination),
    };
  });

  const report = {
    generatedAt: new Date().toISOString(),
    source: 'local .fig image refs from assets-source/figma/local-grafika/images',
    exported,
  };

  fs.writeFileSync(REPORT_PATH, JSON.stringify(report, null, 2));

  const ok = exported.filter((asset) => asset.ok).length;
  const failed = exported.length - ok;
  console.log(`Exported ${ok} local Figma assets to ${relative(OUT_DIR)} and ${relative(WP_IMPORT_DIR)}.`);
  if (failed > 0) {
    console.log(`${failed} assets were not exported. See ${relative(REPORT_PATH)}.`);
  }
}

main();
