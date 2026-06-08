const { execFileSync } = require('child_process');
const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

const allowedHosts = new Set(['localhost', '127.0.0.1', '::1']);
const externalRequests = new Set();
const createdTitles = [];

const forms = [
  {
    label: 'contact',
    path: '/kontakt/',
    selector: '.f-form--contact',
    titlePrefix: 'Arctic Contact Smoke',
    message: 'Lokalni smoke test kontaktniho formulare.',
    expectedForm: 'contact',
    expectedInterest: null,
  },
  {
    label: 'service',
    path: '/servis/',
    selector: '.f-form--service',
    titlePrefix: 'Arctic Service Smoke',
    message: 'Lokalni smoke test servisniho formulare.',
    expectedForm: 'service',
    expectedInterest: 'service',
  },
];

function isAllowedExternalRequest(url) {
  if (url.hostname === 'www.google.com' && url.pathname.startsWith('/maps')) {
    return true;
  }

  if (url.hostname === 'maps.googleapis.com' && (
    url.pathname.startsWith('/maps') ||
    url.pathname.startsWith('/maps-api-v3') ||
    url.pathname.startsWith('/$rpc/google.internal.maps')
  )) {
    return true;
  }

  if (url.hostname === 'maps.gstatic.com') {
    return true;
  }

  if (url.hostname === 'places.googleapis.com' && url.pathname.startsWith('/$rpc/google.maps.places')) {
    return true;
  }

  if (url.hostname === 'fonts.googleapis.com' || url.hostname === 'fonts.gstatic.com') {
    return true;
  }

  return false;
}

function trackExternalRequests(page) {
  page.on('request', (request) => {
    let url;
    try {
      url = new URL(request.url());
    } catch (error) {
      return;
    }

    if ((url.protocol === 'http:' || url.protocol === 'https:') && !allowedHosts.has(url.hostname) && !isAllowedExternalRequest(url)) {
      externalRequests.add(request.url());
    }
  });
}

function wpCli(args) {
  return execFileSync('docker', ['compose', 'run', '--rm', 'wpcli', 'wp', ...args], {
    cwd: process.cwd(),
    encoding: 'utf8',
    env: {
      ...process.env,
      COMPOSE_PROGRESS: 'plain',
    },
    stdio: ['ignore', 'pipe', 'pipe'],
  }).trim();
}

function fetchContactByTitle(title) {
  const code = `
global $wpdb;
$post_id = $wpdb->get_var( $wpdb->prepare(
  "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s AND post_title = %s ORDER BY ID DESC LIMIT 1",
  'contact',
  'publish',
  ${JSON.stringify(title)}
) );

if ( !$post_id ) {
  echo wp_json_encode( null );
} else {
  echo wp_json_encode( array(
    'id' => (int) $post_id,
    'title' => get_the_title( $post_id ),
    'form' => get_post_meta( $post_id, 'contact_form', true ),
    'email' => get_post_meta( $post_id, 'contact_email', true ),
    'phone' => get_post_meta( $post_id, 'contact_phone', true ),
    'interest' => get_post_meta( $post_id, 'contact_interest', true ),
    'processed' => get_post_meta( $post_id, 'contact_processed', true ),
    'recaptcha' => get_post_meta( $post_id, 'contact_recaptcha_success', true ),
  ) );
}
`;

  const output = wpCli(['eval', code]);
  const jsonStart = output.indexOf('{');
  if (jsonStart === -1) {
    return null;
  }

  return JSON.parse(output.slice(jsonStart));
}

function cleanupCreatedContacts() {
  for (const title of createdTitles) {
    try {
      const contact = fetchContactByTitle(title);
      if (contact && contact.id) {
        wpCli(['post', 'delete', String(contact.id), '--force']);
      }
    } catch (error) {
      console.warn(`Could not clean up smoke contact "${title}": ${error.message}`);
    }
  }
}

async function submitForm(page, formConfig) {
  const title = `${formConfig.titlePrefix} ${Date.now()}`;
  createdTitles.push(title);

  const result = await page.evaluate(async ({ selector, title, message, expectedInterest }) => {
    const form = document.querySelector(selector);
    if (!form) {
      return { status: 0, text: '', error: `Missing form ${selector}` };
    }

    const ajaxUrl = form.getAttribute('data-ajax');
    const data = new FormData(form);
    data.set('action', 'form_processing');
    data.set('f-form--submitted', 'true');
    data.set('f-name', title);
    data.set('f-email', 'test@test.com');
    data.set('f-phone', '+420 777 000 111');
    data.set('f-message', message);

    const interestSelect = form.querySelector('select[name="f-interest"]');
    if (interestSelect) {
      const option = interestSelect.querySelector('option:not([disabled])');
      data.set('f-interest', option && option.value ? option.value : 'jacuzzi');
    } else if (expectedInterest) {
      data.set('f-interest', expectedInterest);
    }

    const response = await fetch(ajaxUrl, { method: 'POST', body: data });
    return {
      status: response.status,
      text: await response.text(),
      ajaxUrl,
      title,
      interest: data.get('f-interest'),
    };
  }, { ...formConfig, title });

  if (result.error) {
    throw new Error(result.error);
  }

  if (result.status !== 200) {
    throw new Error(`${formConfig.label} form returned HTTP ${result.status}.`);
  }

  if (!result.ajaxUrl || !result.ajaxUrl.startsWith(baseUrl)) {
    throw new Error(`${formConfig.label} form posts outside local WordPress: ${result.ajaxUrl || 'missing ajax URL'}.`);
  }

  if (!result.text.includes('a-alert--success')) {
    throw new Error(`${formConfig.label} form did not return the success alert.`);
  }

  for (const forbidden of ['a-alert--error', 'Nonce check failed', 'Invalid form processing template', 'recaptcha']) {
    if (result.text.toLowerCase().includes(forbidden.toLowerCase())) {
      throw new Error(`${formConfig.label} form returned forbidden response text: ${forbidden}.`);
    }
  }

  const contact = fetchContactByTitle(result.title);
  if (!contact) {
    throw new Error(`${formConfig.label} form did not create a contact CPT entry.`);
  }

  if (contact.form !== formConfig.expectedForm) {
    throw new Error(`${formConfig.label} contact CPT has form "${contact.form}", expected "${formConfig.expectedForm}".`);
  }

  if (contact.email !== 'test@test.com' || contact.phone !== '+420 777 000 111') {
    throw new Error(`${formConfig.label} contact CPT did not persist contact details.`);
  }

  if (contact.processed !== 'AJAX' || contact.recaptcha !== 'skipped') {
    throw new Error(`${formConfig.label} contact CPT has unexpected processing metadata.`);
  }

  if (formConfig.expectedInterest && contact.interest !== formConfig.expectedInterest) {
    throw new Error(`${formConfig.label} contact CPT interest is "${contact.interest}", expected "${formConfig.expectedInterest}".`);
  }

  return contact.id;
}

(async () => {
  const browser = await chromium.launch({ executablePath: chromePath });

  try {
    const page = await browser.newPage();
    trackExternalRequests(page);

    for (const formConfig of forms) {
      const response = await page.goto(`${baseUrl}${formConfig.path}`, { waitUntil: 'networkidle' });
      if (!response || response.status() >= 400) {
        throw new Error(`${formConfig.path} returned ${response ? response.status() : 'no response'}.`);
      }

      await submitForm(page, formConfig);
    }

    if (externalRequests.size) {
      throw new Error(`External browser requests detected: ${Array.from(externalRequests).join(', ')}`);
    }

    console.log('Form smoke passed.');
  } finally {
    await browser.close();
    cleanupCreatedContacts();
  }
})();
