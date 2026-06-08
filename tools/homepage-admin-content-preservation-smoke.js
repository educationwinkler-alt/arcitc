const { execFileSync } = require('child_process');
const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

function wp(args) {
  return execFileSync('docker', [
    'compose',
    'run',
    '--rm',
    '-T',
    'wpcli',
    'wp',
    ...args,
    '--allow-root',
  ], {
    cwd: process.cwd(),
    encoding: 'utf8',
    stdio: ['ignore', 'pipe', 'pipe'],
  }).trim();
}

function readMetaState(homeId) {
  const json = wp([
    'eval',
    [
      `$home_id = ${Number(homeId)};`,
      '$benefits = get_post_meta($home_id, "homepage_benefits");',
      '$benefit_images = array_filter(array_map("absint", get_post_meta($home_id, "homepage_benefit_images")));',
      '$steps = get_post_meta($home_id, "homepage_progress_steps");',
      'echo wp_json_encode(array(',
      '"benefits" => count($benefits),',
      '"benefit_images" => count($benefit_images),',
      '"steps" => count($steps),',
      '));',
    ].join(' '),
  ]);

  return JSON.parse(json);
}

async function readFrontendState(page) {
  await page.goto(`${baseUrl.replace(/\/$/, '')}/`, { waitUntil: 'domcontentloaded', timeout: 90000 });
  await page.waitForTimeout(1200);

  return page.evaluate(() => {
    const content = document.querySelector('.template--homepage .f-page__content');
    const benefitSection = document.querySelector('.f-section--arctic-benefits');
    const progressSection = document.querySelector('.f-section--progress');

    return {
      content: content ? content.textContent.trim().replace(/\s+/g, ' ') : '',
      benefitSource: benefitSection ? benefitSection.getAttribute('data-content-source') || '' : 'missing',
      benefitCount: document.querySelectorAll('.f-arctic-benefit').length,
      benefitImages: document.querySelectorAll('.f-arctic-benefit img').length,
      progressSource: progressSection ? progressSection.getAttribute('data-content-source') || '' : 'missing',
      progressStepCount: document.querySelectorAll('.f-progress-steps li').length,
    };
  });
}

(async () => {
  assert(baseUrl.includes('localhost'), 'homepage admin content preservation smoke is local-only');

  const showOnFront = wp(['option', 'get', 'show_on_front']);
  const homeId = Number(wp(['option', 'get', 'page_on_front']));

  assert(showOnFront === 'page', `show_on_front is ${showOnFront}`);
  assert(homeId > 0, 'page_on_front is not configured');

  const originalContent = wp(['post', 'get', String(homeId), '--field=post_content']);
  const editedContent = originalContent.includes('Jsme výhradní prodejce')
    ? originalContent.replace('Jsme výhradní prodejce', 'Jsme výhradní prodejce Arctic Spas')
    : `${originalContent}\n<!-- admin preservation smoke ${Date.now()} -->`;

  const beforeMeta = readMetaState(homeId);

  assert(beforeMeta.benefits >= 3, `expected at least 3 benefit rows before edit, got ${beforeMeta.benefits}`);
  assert(beforeMeta.benefit_images >= 3, `expected at least 3 benefit images before edit, got ${beforeMeta.benefit_images}`);
  assert(beforeMeta.steps >= 6, `expected at least 6 progress rows before edit, got ${beforeMeta.steps}`);

  const browser = await chromium.launch({
    channel: process.env.PLAYWRIGHT_CHANNEL || 'chrome',
    headless: true,
  });

  const page = await browser.newPage({ viewport: { width: 1440, height: 1200 } });

  try {
    wp(['post', 'update', String(homeId), `--post_content=${editedContent}`]);

    const afterMeta = readMetaState(homeId);
    const afterFrontend = await readFrontendState(page);

    assert(afterMeta.benefits >= 3, `benefit rows were lost after content edit, got ${afterMeta.benefits}`);
    assert(afterMeta.benefit_images >= 3, `benefit images were lost after content edit, got ${afterMeta.benefit_images}`);
    assert(afterMeta.steps >= 6, `progress rows were lost after content edit, got ${afterMeta.steps}`);
    assert(afterFrontend.content.includes('Jsme výhradní prodejce'), 'edited homepage content did not render');
    assert(afterFrontend.benefitSource === 'homepage-meta', `benefit source is ${afterFrontend.benefitSource}`);
    assert(afterFrontend.benefitCount === 3, `homepage should render 3 benefits after edit, got ${afterFrontend.benefitCount}`);
    assert(afterFrontend.benefitImages === 3, `homepage should render 3 benefit icons after edit, got ${afterFrontend.benefitImages}`);
    assert(afterFrontend.progressSource === 'homepage-meta', `progress source is ${afterFrontend.progressSource}`);
    assert(afterFrontend.progressStepCount === 6, `homepage should render 6 progress steps after edit, got ${afterFrontend.progressStepCount}`);

    console.log('Homepage admin content preservation smoke passed.');
  } finally {
    wp(['post', 'update', String(homeId), `--post_content=${originalContent}`]);
    await browser.close();
  }
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
