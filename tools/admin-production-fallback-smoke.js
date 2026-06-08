const fs = require('fs');
const path = require('path');

const root = process.cwd();

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

function read(relativePath) {
  return fs.readFileSync(path.join(root, relativePath), 'utf8');
}

function assertIncludes(relativePath, needle, label = needle) {
  const content = read(relativePath);
  assert(content.includes(needle), `${relativePath} is missing ${label}`);
}

assertIncludes(
  'wp-content/themes/arctic/inc/functions/environment.php',
  "in_array( $environment, array( 'local', 'development' ), true )",
  'local/development fallback gate'
);

for (const relativePath of [
  'wp-content/themes/arctic/template-about.php',
  'wp-content/themes/arctic/template-support.php',
  'wp-content/themes/arctic/template-services.php',
  'wp-content/themes/arctic/template-features.php',
  'wp-content/themes/arctic/template-feature-detail.php',
  'wp-content/themes/arctic/template-certificates.php',
  'wp-content/themes/arctic/template-references.php',
  'wp-content/themes/arctic/modules/slides/templates/section.php',
  'wp-content/themes/arctic/modules/products/inc/benefit-sections.php',
  'wp-content/themes/arctic/modules/products/inc/colors.php',
  'wp-content/themes/arctic/modules/products/templates/post/single/configurations.php',
  'wp-content/themes/arctic/templates/section/benefits.php',
  'wp-content/themes/arctic/templates/section/showroom.php',
  'wp-content/themes/arctic/templates/section/progress.php',
  'wp-content/themes/arctic/templates/section/category-intro.php',
]) {
  assertIncludes(relativePath, 'arctic_allow_seed_fallbacks', 'seed fallback guard');
}

assertIncludes(
  'wp-content/themes/arctic/template-support.php',
  '$allow_support_fallbacks = function_exists( \'arctic_allow_seed_fallbacks\' ) && arctic_allow_seed_fallbacks();',
  'support FAQ fallback guard'
);
assertIncludes(
  'wp-content/themes/arctic/template-support.php',
  'arctic_downloads_filter_definitions',
  'support shared download filter definitions'
);
assertIncludes(
  'wp-content/themes/arctic/template-downloads.php',
  'arctic_downloads_filter_definitions',
  'downloads shared filter definitions'
);
assertIncludes(
  'wp-content/themes/arctic/modules/downloads/templates/listing.php',
  'arctic_downloads_filter_key_for_document_type',
  'download document type to filter helper'
);
assertIncludes(
  'wp-content/themes/arctic/modules/downloads/templates/listing.php',
  '$allow_seed_fallbacks ? $thumbs',
  'download thumbnail seed fallback guard'
);
assertIncludes(
  'wp-content/themes/arctic/modules/downloads/inc/admin.php',
  'arctic_downloads_filter_definitions',
  'shared download filter helper'
);
assertIncludes(
  'wp-content/themes/arctic/template-references.php',
  'while ( $allow_seed_fallbacks && count( $references ) < $reference_target_count )',
  'placeholder reference gate'
);
assertIncludes(
  'wp-content/themes/arctic/template-references.php',
  'if ( !$image && !$allow_seed_fallbacks )',
  'reference image fallback gate'
);
assertIncludes(
  'wp-content/themes/arctic/modules/slides/templates/section.php',
  "$allow_homepage_fallbacks    = $is_homepage_slides && function_exists( 'arctic_allow_seed_fallbacks' ) && arctic_allow_seed_fallbacks();",
  'homepage fallback slide gate'
);
assertIncludes(
  'wp-content/themes/arctic/modules/products/inc/benefit-sections.php',
  'if ( !$has_rows && !$allow_seed )',
  'product benefit row fallback gate'
);
assertIncludes(
  'wp-content/themes/arctic/modules/products/templates/post/single/configurations.php',
  '$allow_seed_fallbacks && file_exists( $fallback_path )',
  'product configuration media fallback gate'
);
assertIncludes(
  'wp-content/themes/arctic/modules/products/inc/colors.php',
  "if ( !function_exists( 'arctic_allow_seed_fallbacks' ) || !arctic_allow_seed_fallbacks() )",
  'product color legacy fallback gate'
);
assertIncludes(
  'wp-content/themes/arctic/templates/section/benefits.php',
  "if ( !function_exists( 'arctic_allow_seed_fallbacks' ) || !arctic_allow_seed_fallbacks() )",
  'homepage benefits fallback gate'
);
assertIncludes(
  'wp-content/themes/arctic/templates/section/showroom.php',
  "if ( !function_exists( 'arctic_allow_seed_fallbacks' ) || !arctic_allow_seed_fallbacks() )",
  'homepage showroom fallback gate'
);
assertIncludes(
  'wp-content/themes/arctic/templates/section/progress.php',
  "if ( !function_exists( 'arctic_allow_seed_fallbacks' ) || !arctic_allow_seed_fallbacks() )",
  'homepage progress fallback gate'
);
assertIncludes(
  'wp-content/themes/arctic/templates/section/category-intro.php',
  "if ( !function_exists( 'arctic_allow_seed_fallbacks' ) || !arctic_allow_seed_fallbacks() )",
  'category intro fallback gate'
);
assertIncludes(
  'wp-content/themes/arctic/modules/features/inc/features.php',
  "$allow_seed ? arctic_feature_fallback_image_url() : ''",
  'feature card media fallback gate'
);
assertIncludes(
  'wp-content/themes/arctic/template-feature-detail.php',
  "$article_source = 'admin-empty';",
  'feature detail editor empty state'
);

for (const relativePath of [
  'wp-content/themes/arctic/templates/component/quick-contact-card.php',
  'wp-content/themes/arctic/templates/section/contact.php',
  'wp-content/themes/arctic/templates/footer.php',
]) {
  const content = read(relativePath);
  assert(content.includes('baspa_member_avatar_html'), `${relativePath} should render shared member avatars`);
  assert(content.includes("'admin-empty'"), `${relativePath} should expose admin-empty fallback avatars`);
  assert(!content.includes('contact-tomas-koutny.jpg'), `${relativePath} must not hardcode the Tomáš Koutný avatar`);
  assert(!content.includes('data-asset-status="figma-fallback"'), `${relativePath} must not expose contact figma-fallback avatars`);
}

for (const relativePath of [
  'wp-content/themes/arctic/modules/offers/templates/post/card.php',
  'wp-content/themes/arctic/templates/navigation/mega.php',
  'wp-content/themes/arctic/templates/section/hero-promo.php',
]) {
  const content = read(relativePath);
  assert(content.includes('data-asset-status="admin-empty"'), `${relativePath} should expose explicit empty offer media state`);
  assert(!content.includes('hp-fixed-banner-product.png'), `${relativePath} must not hardcode the Figma offer promo image`);
  assert(!content.includes('data-asset-status="figma-fallback"'), `${relativePath} must not expose offer figma-fallback media`);
}

const categoryBackground = read('wp-content/themes/arctic/templates/image/background.php');
assert(!categoryBackground.includes('forced_term_images'), 'category background must not override WP term hero images');

console.log('Admin production fallback smoke passed.');
