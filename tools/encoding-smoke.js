const { readdirSync, readFileSync, statSync } = require('fs');
const { join } = require('path');
const { TextDecoder } = require('util');

const decoder = new TextDecoder('utf-8', { fatal: true });

const roots = [
  'wp-content/themes/arctic/modules/products/templates/post/single',
];

const singleFiles = [
  'wp-content/themes/arctic/inc/functions/location.php',
  'wp-content/themes/arctic/inc/functions.php',
  'wp-content/themes/arctic/template-showroom.php',
  'wp-content/themes/arctic/templates/footer.php',
  'wp-content/themes/arctic/templates/about/address.php',
  'wp-content/themes/arctic/templates/section/map.php',
  'wp-content/themes/arctic/templates/section/product-benefits.php',
  'wp-content/themes/arctic/templates/section/product-options.php',
  'wp-content/themes/arctic/tools/seed-pilot-content.php',
];

const mojibakeMarkers = [
  '\u00c3',
  '\u00c4',
  '\u00c5',
  '\ufffd',
];

function collectPhpFiles(root) {
  return readdirSync(root)
    .map((entry) => join(root, entry))
    .filter((path) => statSync(path).isFile() && path.endsWith('.php'));
}

const files = Array.from(new Set([
  ...roots.flatMap(collectPhpFiles),
  ...singleFiles,
])).sort();

for (const file of files) {
  const bytes = readFileSync(file);

  if (bytes.length >= 3 && bytes[0] === 0xef && bytes[1] === 0xbb && bytes[2] === 0xbf) {
    throw new Error(`${file} has a UTF-8 BOM.`);
  }

  let text = '';
  try {
    text = decoder.decode(bytes);
  } catch (error) {
    throw new Error(`${file} is not valid UTF-8: ${error.message}`);
  }

  for (const marker of mojibakeMarkers) {
    if (text.includes(marker)) {
      throw new Error(`${file} contains mojibake marker U+${marker.codePointAt(0).toString(16).toUpperCase()}.`);
    }
  }
}

console.log(`Encoding smoke passed for ${files.length} PHP files.`);
