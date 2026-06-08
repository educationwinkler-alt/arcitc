const fs = require('fs');
const path = require('path');
const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const outputDir = path.join(process.cwd(), 'docs', 'screenshots', 'contact-interest-options-2026-06-08');

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

(async () => {
  fs.mkdirSync(outputDir, { recursive: true });

  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1 });

  try {
    const response = await page.goto(`${baseUrl}/kontakt/`, { waitUntil: 'networkidle', timeout: 90000 });
    assert(response && response.status() < 400, `/kontakt/ returned ${response ? response.status() : 'no response'}`);

    const state = await page.evaluate(() => {
      const form = document.querySelector('.f-form--contact');
      const select = form ? form.querySelector('select[name="f-interest"]') : null;

      return {
        formSource: form ? form.getAttribute('data-content-source') || '' : '',
        selectId: select ? select.id : '',
        optionValues: select ? Array.from(select.options).map((option) => option.value).filter(Boolean) : [],
        optionLabels: select ? Array.from(select.options).map((option) => option.textContent.trim().replace(/\s+/g, ' ')).filter(Boolean) : [],
      };
    });

    assert(state.formSource === 'contact-settings', `contact form source is ${state.formSource}`);
    assert(state.selectId, 'contact interest select is missing');
    assert(state.optionValues.includes('offer'), `contact interest values are missing offer: ${state.optionValues.join(', ')}`);
    assert(state.optionLabels.includes('Akční nabídka'), `contact interest labels are missing Akční nabídka: ${state.optionLabels.join(', ')}`);

    await page.screenshot({ path: path.join(outputDir, 'local-contact-interest-options.png'), fullPage: true });
    fs.writeFileSync(path.join(outputDir, 'audit.json'), `${JSON.stringify({ baseUrl, ...state }, null, 2)}\n`);

    console.log('Contact interest options smoke passed.');
  } finally {
    await browser.close();
  }
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
