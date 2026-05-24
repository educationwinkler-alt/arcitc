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

echo wp_json_encode( array(
  'environment' => wp_get_environment_type(),
  'http_filter' => has_filter( 'pre_http_request', 'arctic_local_block_external_http' ) ? 'yes' : 'no',
  'mail_filter' => has_filter( 'pre_wp_mail', 'arctic_local_block_mail' ) ? 'yes' : 'no',
  'baspa_error' => is_wp_error( $baspa_response ) ? $baspa_response->get_error_code() : 'not_blocked',
  'ecomail_error' => is_wp_error( $ecomail_response ) ? $ecomail_response->get_error_code() : 'not_blocked',
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

console.log('Local safety smoke passed.');
