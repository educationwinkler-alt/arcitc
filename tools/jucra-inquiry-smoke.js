const { execFileSync } = require('child_process');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';

const query = '/poptavka-konfigurace/?model_name=Timberwolf&option_jets=dd-30+Jets+2+Pumps&option_acrylic=dd-Acrylic+Platinum&option_cabinet=dd-Cabinet+Cedar';
const title = `Arctic Jucra Smoke ${Date.now()}`;

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

function assertIncludes(label, text, needles) {
  for (const needle of needles) {
    if (!text.includes(needle)) {
      throw new Error(`${label} is missing expected text: ${needle}`);
    }
  }
}

function extractInputs(html) {
  const inputs = {};
  const inputRegex = /<input\b[^>]*>/gi;
  const attrRegex = /\s([a-zA-Z0-9_-]+)=["']([^"']*)["']/g;
  let inputMatch;

  while ((inputMatch = inputRegex.exec(html))) {
    const attrs = {};
    let attrMatch;

    while ((attrMatch = attrRegex.exec(inputMatch[0]))) {
      attrs[attrMatch[1]] = attrMatch[2]
        .replace(/&quot;/g, '"')
        .replace(/&#039;/g, "'")
        .replace(/&amp;/g, '&');
    }

    if (attrs.name) {
      inputs[attrs.name] = attrs.value || '';
    }
  }

  return inputs;
}

function fetchContactByTitle(contactTitle) {
  const code = `
global $wpdb;
$post_id = $wpdb->get_var( $wpdb->prepare(
  "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s AND post_title = %s ORDER BY ID DESC LIMIT 1",
  'contact',
  'publish',
  ${JSON.stringify(contactTitle)}
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
    'jucra_model' => get_post_meta( $post_id, 'contact_jucra_model', true ),
    'jucra_builder_url' => get_post_meta( $post_id, 'contact_jucra_builder_url', true ),
    'jucra_options' => get_post_meta( $post_id, 'contact_jucra_options', true ),
    'jucra_jets' => get_post_meta( $post_id, 'contact_jucra_jets', true ),
    'jucra_acrylic' => get_post_meta( $post_id, 'contact_jucra_acrylic', true ),
    'jucra_cabinet' => get_post_meta( $post_id, 'contact_jucra_cabinet', true ),
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

function cleanupContact(contactTitle) {
  try {
    const contact = fetchContactByTitle(contactTitle);
    if (contact && contact.id) {
      wpCli(['post', 'delete', String(contact.id), '--force']);
    }
  } catch (error) {
    console.warn(`Could not clean up Jucra smoke contact "${contactTitle}": ${error.message}`);
  }
}

async function main() {
  try {
    const modelDefinitions = wpCli(['eval', "echo get_theme_mod('arctic_jucra_model_definitions');"]);

    assertIncludes('Jucra Customizer model definitions', modelDefinitions, [
      'timberwolf|Timberwolf|Timberwolf|timberwolf',
      'summit-xl|Summit XL|Summit XL|summit-xl',
    ]);

    const modelDefinitionLines = modelDefinitions
      .split(/\r?\n/)
      .filter((line) => line.includes('|'));

    if (modelDefinitionLines.length < 14) {
      throw new Error(`Jucra Customizer stores ${modelDefinitionLines.length} model definitions, expected at least 14.`);
    }

    const builderResponse = await fetch(`${baseUrl}/konfigurator/timberwolf/`);

    if (!builderResponse.ok) {
      throw new Error(`Jucra builder page returned ${builderResponse.status}.`);
    }

    const builderHtml = await builderResponse.text();

    assertIncludes('Jucra builder page', builderHtml, [
      'f-section--jucra-builder',
      'data-content-source="jucra-settings"',
      'data-jucra-model="Timberwolf"',
      '/konfigurator/timberwolf/',
      '/konfigurator/summit-xl/',
      'Timberwolf',
      'Summit XL',
    ]);

    const modelLinks = builderHtml.match(/class=["'][^"']*f-jucra-builder__model/g) || [];

    if (modelLinks.length < 14) {
      throw new Error(`Jucra builder rendered ${modelLinks.length} model links, expected at least 14.`);
    }

    const response = await fetch(`${baseUrl}${query}`);

    if (!response.ok) {
      throw new Error(`Inquiry page returned ${response.status}.`);
    }

    const html = await response.text();

    assertIncludes('Jucra inquiry page', html, [
      'f-section--jucra-inquiry',
      'f-form--jucra-inquiry',
      'Timberwolf',
      '2 čerpadla',
      'Platinum Swirl',
      'Cedar',
      'name="f-form" value="jucra"',
      'name="f-jucra-model" value="Timberwolf"',
      'name="f-jucra-option-jets" value="dd-30 Jets 2 Pumps"',
    ]);

    const formMatch = html.match(/<form\b[^>]*class=["'][^"']*f-form--jucra-inquiry[\s\S]*?<\/form>/i);

    if (!formMatch) {
      throw new Error('Jucra inquiry page is missing the scoped inquiry form.');
    }

    const inputs = extractInputs(formMatch[0]);
    const requiredInputs = [
      'f-contact-nonce',
      'f-form',
      'f-form-name',
      'f-number',
      'f-title',
      'f-url',
      'f-interest',
      'f-jucra-model',
      'f-jucra-option-jets',
      'f-jucra-option-acrylic',
      'f-jucra-option-cabinet',
    ];

    for (const name of requiredInputs) {
      if (!(name in inputs)) {
        throw new Error(`Inquiry form is missing hidden input ${name}.`);
      }
    }

    const data = new FormData();
    for (const [name, value] of Object.entries(inputs)) {
      data.set(name, value);
    }

    data.set('action', 'form_processing');
    data.set('f-form--submitted', 'true');
    data.set('f-name', title);
    data.set('f-email', 'test@test.com');
    data.set('f-phone', '+420 777 000 222');
    data.set('f-message', 'Lokalni smoke test poptavky z 3D konfiguratoru.');

    const ajaxResponse = await fetch(`${baseUrl}/wp-admin/admin-ajax.php`, {
      method: 'POST',
      body: data,
    });
    const ajaxText = await ajaxResponse.text();

    if (ajaxResponse.status !== 200) {
      throw new Error(`Jucra inquiry AJAX returned HTTP ${ajaxResponse.status}.`);
    }

    if (!ajaxText.includes('a-alert--success')) {
      throw new Error('Jucra inquiry form did not return the success alert.');
    }

    for (const forbidden of ['a-alert--error', 'Nonce check failed', 'Invalid form processing template', 'recaptcha']) {
      if (ajaxText.toLowerCase().includes(forbidden.toLowerCase())) {
        throw new Error(`Jucra inquiry form returned forbidden response text: ${forbidden}.`);
      }
    }

    const contact = fetchContactByTitle(title);

    if (!contact) {
      throw new Error('Jucra inquiry form did not create a contact CPT entry.');
    }

    const options = JSON.parse(contact.jucra_options || '{}');

    if (contact.form !== 'jucra') {
      throw new Error(`Jucra contact has form "${contact.form}", expected "jucra".`);
    }

    if (contact.jucra_model !== 'Timberwolf') {
      throw new Error(`Jucra contact has model "${contact.jucra_model}", expected "Timberwolf".`);
    }

    if (contact.interest !== 'jacuzzi') {
      throw new Error(`Jucra contact has interest "${contact.interest}", expected "jacuzzi".`);
    }

    if (contact.processed !== 'AJAX' || contact.recaptcha !== 'skipped') {
      throw new Error('Jucra contact has unexpected processing metadata.');
    }

    if (contact.jucra_jets !== '2 čerpadla' || contact.jucra_acrylic !== 'Platinum Swirl' || contact.jucra_cabinet !== 'Cedar') {
      throw new Error('Jucra contact did not persist the selected option labels.');
    }

    if (!options.jets || options.jets.id !== 'dd-30 Jets 2 Pumps') {
      throw new Error('Jucra contact did not persist the selected jets option id.');
    }

    console.log('Jucra inquiry smoke passed.');
  } finally {
    cleanupContact(title);
  }
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
