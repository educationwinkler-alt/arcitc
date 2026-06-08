const fs = require('fs');
const path = require('path');
const { chromium } = require('playwright-core');

const root = process.cwd();
const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const outRoot = path.join(root, 'docs', 'screenshots', 'deep-figma-physical-audit-2026-05-30');
const currentDir = path.join(outRoot, 'current');
const compareDir = path.join(outRoot, 'compare-html');
const figmaDir = path.join(outRoot, 'figma-current');

const pages = [
  { key: 'hp', path: '/', viewport: { width: 1920, height: 1080 }, figma: 'figma-hp.png', label: 'Homepage desktop' },
  { key: 'kategorie-virivky', path: '/virivky/', viewport: { width: 1920, height: 1080 }, figma: 'figma-kategorie.png', label: 'Kategorie vířivky' },
  { key: 'kategorie-swimspa', path: '/swimspa/', viewport: { width: 1920, height: 1080 }, figma: 'figma-kategorie.png', label: 'Kategorie swimspa' },
  { key: 'detail-timberwolf', path: '/product/timberwolf/', viewport: { width: 1920, height: 1080 }, figma: 'figma-detail-produktu.png', label: 'Detail Timberwolf' },
  { key: 'showroom', path: '/showroom/', viewport: { width: 1920, height: 1080 }, figma: 'figma-showroom.png', label: 'Showroom' },
  { key: 'vlastnosti', path: '/vlastnosti/', viewport: { width: 1920, height: 1080 }, figma: 'figma-vlastnosti.png', label: 'Vlastnosti listing' },
  { key: 'vlastnosti-detail', path: '/vlastnosti/izolace-virivky/', viewport: { width: 1920, height: 1080 }, figma: 'figma-vlastnosti-detail.png', label: 'Vlastnosti detail' },
  { key: 'sluzby', path: '/sluzby/', viewport: { width: 1920, height: 1080 }, figma: 'figma-sluzby.png', label: 'Služby' },
  { key: 'certifikaty', path: '/certifikaty/', viewport: { width: 1920, height: 1080 }, figma: 'figma-certifikaty.png', label: 'Certifikáty' },
  { key: 'zaruka', path: '/zaruka/', viewport: { width: 1920, height: 1080 }, figma: 'figma-zaruka.png', label: 'Záruka' },
  { key: 'podpora', path: '/podpora/', viewport: { width: 1920, height: 1080 }, figma: 'figma-podpora.png', label: 'Podpora' },
  { key: 'o-nas', path: '/o-nas/', viewport: { width: 1920, height: 1080 }, figma: 'figma-o-nas.png', label: 'O nás' },
  { key: 'reference', path: '/reference/', viewport: { width: 1920, height: 1080 }, figma: 'figma-reference.png', label: 'Reference' },
  { key: 'maintenance', path: '/kolik-stoji-udrzba/', viewport: { width: 1920, height: 1080 }, figma: 'figma-maintenance.png', label: 'Kolik stojí údržba' },
  { key: 'servis', path: '/servis/', viewport: { width: 1920, height: 1080 }, figma: 'figma-servis.png', label: 'Servis' },
  { key: 'kontakt', path: '/kontakt/', viewport: { width: 1920, height: 1080 }, figma: 'figma-kontakt.png', label: 'Kontakt' },
  { key: 'mobile-hp-375', path: '/', viewport: { width: 375, height: 900 }, figma: 'figma-mobile-hp-full.png', label: 'Mobile HP 375' },
];

function enrichMetrics(item, localSize, figmaSize, overflow) {
  const scale = item.viewport.width / figmaSize.width;
  const figmaScaledHeight = Math.round(figmaSize.height * scale);

  return {
    ...item,
    current: `current/${item.key}.png`,
    figmaPath: path.relative(outRoot, path.join(figmaDir, item.figma)).replace(/\\/g, '/'),
    localSize,
    figmaSize,
    figmaScaleToViewport: scale,
    figmaScaledHeight,
    heightDeltaRaw: localSize.height - figmaSize.height,
    heightDeltaScaled: localSize.height - figmaScaledHeight,
    overflow,
  };
}

async function getPngSize(filePath) {
  const imageSize = require('image-size');
  const result = imageSize.imageSize ? imageSize.imageSize(filePath) : imageSize(filePath);
  return { width: result.width, height: result.height };
}

async function main() {
  fs.mkdirSync(currentDir, { recursive: true });
  fs.mkdirSync(compareDir, { recursive: true });

  const browser = await chromium.launch({ executablePath: chromePath });
  const page = await browser.newPage({ deviceScaleFactor: 1 });
  const records = [];

  try {
    for (const item of pages) {
      await page.setViewportSize(item.viewport);
      await page.goto(`${baseUrl}${item.path}`, { waitUntil: 'networkidle' });
      await page.screenshot({ path: path.join(currentDir, `${item.key}.png`), fullPage: true });
      const localSize = await getPngSize(path.join(currentDir, `${item.key}.png`));
      const figmaSize = await getPngSize(path.join(figmaDir, item.figma));
      const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
      const record = enrichMetrics(item, localSize, figmaSize, overflow);
      records.push(record);
      console.log(`${item.key}: local ${localSize.width}x${localSize.height}, figma ${figmaSize.width}x${figmaSize.height}, scaled delta ${record.heightDeltaScaled}`);
    }

    await page.setViewportSize({ width: 375, height: 900 });
    await page.goto(baseUrl, { waitUntil: 'networkidle' });
    await page.locator('.f-navigation__trigger').first().click();
    await page.waitForTimeout(300);
    await page.screenshot({ path: path.join(currentDir, 'mobile-menu-375.png'), fullPage: false });
    const localSize = await getPngSize(path.join(currentDir, 'mobile-menu-375.png'));
    const figmaSize = await getPngSize(path.join(figmaDir, 'figma-mobile-menu.png'));
    const menuItem = { key: 'mobile-menu-375', path: '/', viewport: { width: 375, height: 900 }, figma: 'figma-mobile-menu.png', label: 'Mobile menu 375' };
    records.push(enrichMetrics(menuItem, localSize, figmaSize, null));

  } finally {
    await browser.close();
  }

  const css = `body{font-family:Arial,sans-serif;margin:24px;background:#eef1f5;color:#111}h1{margin:0 0 18px}.pair{margin:0 0 48px;padding:18px;background:#fff;border-radius:12px}.meta{margin:0 0 12px;color:#444}.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start}.pane{overflow:auto;max-height:1200px;border:1px solid #ccd;background:#f8f8f8}.pane img{display:block;max-width:100%;height:auto}.badge{font-weight:700}.p0{color:#a31f37}@media(max-width:900px){.grid{grid-template-columns:1fr}}`;
  const html = `<!doctype html><html><head><meta charset="utf-8"><title>Deep Figma physical audit 2026-05-30</title><style>${css}</style></head><body><h1>Deep Figma physical audit 2026-05-30</h1>${records.map((r) => `<section class="pair"><h2>${r.label}</h2><p class="meta">Local: ${r.localSize.width}x${r.localSize.height}, Figma raw: ${r.figmaSize.width}x${r.figmaSize.height}, Figma scaled height: ${r.figmaScaledHeight}px, scaled height delta: <span class="badge ${Math.abs(r.heightDeltaScaled) > 300 ? 'p0' : ''}">${r.heightDeltaScaled}px</span>, overflow: ${r.overflow ?? 'n/a'}</p><div class="grid"><div><h3>Figma</h3><div class="pane"><img src="${r.figmaPath}"></div></div><div><h3>Local current</h3><div class="pane"><img src="${r.current}"></div></div></div></section>`).join('\n')}</body></html>`;
  fs.writeFileSync(path.join(outRoot, 'physical-compare.html'), html);
  fs.writeFileSync(path.join(outRoot, 'metrics.json'), JSON.stringify({ generatedAt: new Date().toISOString(), baseUrl, records }, null, 2));
}

main().catch((error) => { console.error(error); process.exit(1); });
