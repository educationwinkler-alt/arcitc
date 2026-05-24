const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

const allowedHosts = new Set(['localhost', '127.0.0.1', '::1']);
const externalRequests = new Set();

function trackExternalRequests(page) {
  page.on('request', (request) => {
    let url;
    try {
      url = new URL(request.url());
    } catch (error) {
      return;
    }

    if ((url.protocol === 'http:' || url.protocol === 'https:') && !allowedHosts.has(url.hostname)) {
      externalRequests.add(request.url());
    }
  });
}

async function postSearch(page, overrides = {}) {
  return page.evaluate(async (overrides) => {
    const form = document.querySelector('.js-search');
    if (!form) {
      return { status: 0, text: '', error: 'Missing .js-search form.' };
    }

    const data = new FormData(form);
    data.set('action', 'search_processing');
    data.set('keyword', overrides.keyword || 'Timberwolf');
    data.set('post_type', overrides.postType || data.get('post_type') || 'post,page,product');
    data.set('post_taxonomy', overrides.postTaxonomy || data.get('post_taxonomy') || 'category,product-category');

    if (Object.prototype.hasOwnProperty.call(overrides, 'nonce')) {
      data.set('search_nonce', overrides.nonce);
    }

    const response = await fetch(form.dataset.ajax, { method: 'POST', body: data });
    return {
      status: response.status,
      text: await response.text(),
      ajaxUrl: form.dataset.ajax,
    };
  }, overrides);
}

(async () => {
  const browser = await chromium.launch({ executablePath: chromePath });

  try {
    const page = await browser.newPage();
    trackExternalRequests(page);

    const response = await page.goto(baseUrl, { waitUntil: 'networkidle' });
    if (!response || response.status() >= 400) {
      throw new Error(`Homepage returned ${response ? response.status() : 'no response'}.`);
    }

    const valid = await postSearch(page, {
      keyword: 'Timberwolf',
      postType: 'post,page,product,not-real',
    });

    if (valid.error) {
      throw new Error(valid.error);
    }

    if (!valid.ajaxUrl || !valid.ajaxUrl.startsWith(baseUrl)) {
      throw new Error(`Search AJAX posts outside local WordPress: ${valid.ajaxUrl || 'missing ajax URL'}.`);
    }

    if (valid.status !== 200) {
      throw new Error(`Search AJAX returned HTTP ${valid.status}, expected 200.`);
    }

    if (!valid.text.includes('Timberwolf') || !valid.text.includes('/product/timberwolf/')) {
      throw new Error('Search AJAX did not return the Timberwolf product result.');
    }

    for (const forbidden of ['baspa.cz', 'api2.ecomailapp.cz', 'not-real']) {
      if (valid.text.includes(forbidden)) {
        throw new Error(`Search response contains forbidden text: ${forbidden}.`);
      }
    }

    const invalid = await postSearch(page, {
      keyword: 'Timberwolf',
      nonce: 'invalid',
    });

    if (invalid.status !== 403) {
      throw new Error(`Search AJAX accepted an invalid nonce with HTTP ${invalid.status}.`);
    }

    if (externalRequests.size) {
      throw new Error(`External browser requests detected: ${Array.from(externalRequests).join(', ')}`);
    }

    console.log('Search smoke passed.');
  } finally {
    await browser.close();
  }
})();
