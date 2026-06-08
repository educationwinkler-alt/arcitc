const { execFileSync } = require('child_process');

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

const php = `<?php
$locations = get_nav_menu_locations();
$target_locations = array('navigation', 'navigation_footer', 'navigation_bar');
$bad = array();
$summary = array();

foreach ($target_locations as $location) {
    $menu_id = (int)($locations[$location] ?? 0);
    $summary[$location] = array('custom' => 0, 'object' => 0, 'missing' => !$menu_id);

    if (!$menu_id) {
        continue;
    }

    $items = wp_get_nav_menu_items($menu_id, array('post_status' => 'publish')) ?: array();

    foreach ($items as $item) {
        if ('custom' !== $item->type) {
            $summary[$location]['object']++;
            continue;
        }

        $summary[$location]['custom']++;
        $url = (string)$item->url;
        $allowed = false;

        if ('' !== $url && '#' !== $url) {
            $parts = wp_parse_url($url);
            $allowed = !empty($parts['query']) || !empty($parts['fragment']);
        }

        if ('#' === $url || '' === $url) {
            $allowed = !get_page_by_path(sanitize_title((string)$item->title));
        }

        if (!$allowed) {
            $bad[] = array(
                'location' => $location,
                'title' => $item->title,
                'url' => $url,
            );
        }
    }
}

echo wp_json_encode(array('bad' => $bad, 'summary' => $summary), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
`;

const output = execFileSync(
  'docker',
  ['compose', 'run', '--rm', '-T', 'wpcli', 'wp', 'eval-file', '-'],
  {
    cwd: process.cwd(),
    input: php,
    encoding: 'utf8',
    stdio: ['pipe', 'pipe', 'pipe'],
  }
);

const jsonStart = output.indexOf('{');
assert(jsonStart >= 0, `WP-CLI did not return JSON:\n${output}`);

const result = JSON.parse(output.slice(jsonStart));

for (const [location, state] of Object.entries(result.summary || {})) {
  assert(!state.missing, `${location} menu location is not assigned`);
}

assert(
  Array.isArray(result.bad) && result.bad.length === 0,
  `Menu contains non-object internal items:\n${JSON.stringify(result.bad, null, 2)}`
);

console.log('Admin menu object smoke passed.');
