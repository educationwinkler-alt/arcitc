const { execFileSync } = require('child_process');

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

function readSafetyState() {
  const code = `
$baspa_response = wp_remote_get( 'https://baspa.cz/', array( 'timeout' => 1 ) );
$ecomail_response = wp_remote_get( 'https://api2.ecomailapp.cz/', array( 'timeout' => 1 ) );
$site_icon_id = (int) get_option( 'site_icon', 0 );
$site_icon_url = get_site_icon_url( 512 );

echo wp_json_encode( array(
  'environment' => wp_get_environment_type(),
  'http_filter' => has_filter( 'pre_http_request', 'arctic_local_block_external_http' ) ? 'yes' : 'no',
  'mail_filter' => has_filter( 'pre_wp_mail', 'arctic_local_block_mail' ) ? 'yes' : 'no',
  'baspa_error' => is_wp_error( $baspa_response ) ? $baspa_response->get_error_code() : 'not_blocked',
  'ecomail_error' => is_wp_error( $ecomail_response ) ? $ecomail_response->get_error_code() : 'not_blocked',
  'site_icon_id' => $site_icon_id,
  'site_icon_url' => (string) $site_icon_url,
  'site_icon_seed_key' => $site_icon_id > 0 ? (string) get_post_meta( $site_icon_id, '_arctic_seed_key', true ) : '',
  'site_icon_marker' => $site_icon_id > 0 ? (string) get_post_meta( $site_icon_id, '_arctic_site_icon_asset', true ) : '',
) );
`;

  const output = wpCli(['eval', code]);
  const jsonStart = output.indexOf('{');
  if (jsonStart === -1) {
    throw new Error(`Could not parse WP-CLI JSON output: ${output}`);
  }

  return JSON.parse(output.slice(jsonStart));
}

const state = readSafetyState();

if (state.environment !== 'local') {
  throw new Error(`WP environment is "${state.environment}", expected "local".`);
}

if (state.http_filter !== 'yes') {
  throw new Error('Local HTTP blocking filter is not registered.');
}

if (state.mail_filter !== 'yes') {
  throw new Error('Local mail blocking filter is not registered.');
}

for (const [label, value] of Object.entries({
  baspa: state.baspa_error,
  ecomail: state.ecomail_error,
})) {
  if (value !== 'arctic_local_external_http_blocked') {
    throw new Error(`${label} request returned "${value}", expected arctic_local_external_http_blocked.`);
  }
}

if (!Number.isInteger(state.site_icon_id) || state.site_icon_id <= 0) {
  throw new Error(`site_icon option is not set to a valid attachment ID (got "${state.site_icon_id}").`);
}

if (state.site_icon_marker !== '1' && state.site_icon_seed_key !== 'arctic-site-icon') {
  throw new Error(
    `site_icon attachment ${state.site_icon_id} is not marked as Arctic asset (marker="${state.site_icon_marker}", seed="${state.site_icon_seed_key}").`
  );
}

const siteIconUrl = String(state.site_icon_url || '');
if (!siteIconUrl) {
  throw new Error('site_icon URL is empty.');
}

if (!siteIconUrl.includes('arctic-site-icon') && !siteIconUrl.includes('/themes/arctic/images/icon.png')) {
  throw new Error(`site_icon URL does not resolve to known Arctic icon asset: ${siteIconUrl}`);
}

if (siteIconUrl.toLowerCase().includes('baspa')) {
  throw new Error(`site_icon URL still contains Baspa brand string: ${siteIconUrl}`);
}

console.log('Local safety smoke passed.');
