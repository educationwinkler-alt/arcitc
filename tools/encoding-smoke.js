const { readdirSync, readFileSync, statSync } = require('fs');
const { join, extname } = require('path');
const { TextDecoder } = require('util');

const decoder = new TextDecoder('utf-8', { fatal: true });

const roots = [
  'tools',
  'wp-content/themes/arctic',
];

const ignoredSegments = new Set([
  '.git',
  'node_modules',
  'vendor',
  'uploads',
  'tmp',
]);

const ignoredRelativePrefixes = [
  'wp-content/themes/arctic/dist/fonts',
  'wp-content/themes/arctic/dist/images',
  'wp-content/themes/arctic/assets',
];

const textExtensions = new Set([
  '.css',
  '.html',
  '.js',
  '.json',
  '.less',
  '.md',
  '.php',
  '.svg',
  '.txt',
  '.xml',
  '.yml',
]);

const mojibakePattern = /[\u0102\u00c4\u0139\u00c2\u00e2\ufffd]/u;

function shouldSkip(path) {
  const normalized = path.replace(/\\/g, '/');

  if (ignoredRelativePrefixes.some((prefix) => normalized.startsWith(prefix))) {
    return true;
  }

  return normalized.split('/').some((segment) => ignoredSegments.has(segment));
}

function collectTextFiles(root) {
  const files = [];

  for (const entry of readdirSync(root)) {
    const path = join(root, entry);

    if (shouldSkip(path)) {
      continue;
    }

    const stat = statSync(path);

    if (stat.isDirectory()) {
      files.push(...collectTextFiles(path));
      continue;
    }

    if (stat.isFile() && textExtensions.has(extname(path).toLowerCase())) {
      files.push(path);
    }
  }

  return files;
}

const files = Array.from(new Set(roots.flatMap(collectTextFiles))).sort();

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

  if (mojibakePattern.test(text)) {
    throw new Error(`${file} contains mojibake-like characters.`);
  }
}

console.log(`Encoding smoke passed for ${files.length} source files.`);
