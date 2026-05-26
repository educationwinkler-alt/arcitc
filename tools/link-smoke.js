const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';

const entryPaths = [
  '/',
  '/virivky/',
  '/swimspa/',
  '/catalog/dalsi-sortiment/',
  '/product/timberwolf/',
  '/product/husky/',
  '/product/athabascan/',
  '/product/covana/',
  '/vlastnosti/',
  '/dalsi-informace/',
  '/sluzby/',
  '/certifikaty/',
  '/zaruka/',
  '/kolik-stoji-udrzba/',
  '/o-nas/',
  '/reference/',
  '/servis/',
  '/showroom/',
  '/podpora/',
  '/ke-stazeni/',
  '/kontakt/',
];

const forbiddenExternalHosts = [
  'baspa.cz',
  'www.baspa.cz',
  'arctic-spas.cz',
  'www.arctic-spas.cz',
  'api2.ecomailapp.cz',
  'www.smartsuppchat.com',
];

const debug = process.env.LINK_SMOKE_DEBUG === '1';

function extractLinks(html) {
  const links = [];
  const pattern = /\s(href|src)=["']([^"']+)["']/gi;
  let match;

  while ((match = pattern.exec(html)) !== null) {
    links.push({ attribute: match[1].toLowerCase(), url: match[2] });
  }

  return links;
}

function shouldIgnore(rawUrl) {
  return (
    rawUrl.startsWith('#') ||
    rawUrl.startsWith('mailto:') ||
    rawUrl.startsWith('tel:') ||
    rawUrl.startsWith('javascript:') ||
    rawUrl.startsWith('data:') ||
    rawUrl.startsWith('blob:') ||
    rawUrl.includes('/wp-admin/admin-ajax.php')
  );
}

function isPageLikeUrl(url) {
  const pathname = url.pathname.toLowerCase();

  if (
    pathname === '/xmlrpc.php' ||
    pathname.startsWith('/wp-json/') ||
    (pathname === '/wp-json' || pathname === '/wp-json/') ||
    (pathname === '/' && url.searchParams.has('p')) ||
    pathname.includes('/oembed/')
  ) {
    return false;
  }

  if (pathname === '/' || pathname.endsWith('/')) {
    return true;
  }

  return !/\.(css|js|json|xml|png|jpe?g|webp|gif|svg|ico|avif|pdf|zip|woff2?|ttf|eot|mp4|webm|mov)$/i.test(pathname);
}

async function fetchWithTimeout(url, options = {}) {
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), 8000);

  try {
    return await fetch(url, {
      ...options,
      signal: controller.signal,
    });
  } finally {
    clearTimeout(timeout);
  }
}

async function fetchPage(path) {
  if (debug) {
    console.error(`Checking entry page ${path}`);
  }

  const response = await fetchWithTimeout(`${baseUrl}${path}`, { redirect: 'follow' });

  if (!response.ok) {
    throw new Error(`${path} returned ${response.status}`);
  }

  return response.text();
}

async function checkInternalUrl(url, sourcePath) {
  if (debug) {
    console.error(`Checking link ${url}`);
  }

  let response;
  try {
    response = await fetchWithTimeout(url, { method: 'HEAD', redirect: 'follow' });
    if (response.status === 405) {
      response = await fetchWithTimeout(url, { redirect: 'follow' });
    }
  } catch (error) {
    throw new Error(`${sourcePath} links to unreachable internal URL ${url} (${error.name})`);
  }

  if (response.status >= 400) {
    throw new Error(`${sourcePath} links to broken internal URL ${url} (${response.status})`);
  }

  if (response.body) {
    await response.body.cancel();
  }
}

(async () => {
  const base = new URL(baseUrl);
  const internalUrls = new Map();
  const forbiddenExternal = new Map();

  for (const path of entryPaths) {
    const html = await fetchPage(path);

    for (const link of extractLinks(html)) {
      if (shouldIgnore(link.url)) {
        continue;
      }

      let url;
      try {
        url = new URL(link.url, `${baseUrl}${path}`);
      } catch (error) {
        throw new Error(`${path} contains invalid URL: ${link.url}`);
      }

      const host = url.hostname.toLowerCase();
      if (forbiddenExternalHosts.includes(host)) {
        forbiddenExternal.set(link.url, path);
        continue;
      }

      if (link.attribute === 'href' && host === base.hostname && url.port === base.port && isPageLikeUrl(url)) {
        url.hash = '';
        internalUrls.set(url.href, path);
      }
    }
  }

  if (forbiddenExternal.size) {
    const details = Array.from(forbiddenExternal.entries()).map(([url, path]) => `${path} -> ${url}`).join('\n');
    throw new Error(`Forbidden external links found:\n${details}`);
  }

  const urls = Array.from(internalUrls.entries());
  for (let index = 0; index < urls.length; index += 8) {
    await Promise.all(urls.slice(index, index + 8).map(([url, sourcePath]) => checkInternalUrl(url, sourcePath)));
  }

  console.log(`Link smoke passed for ${entryPaths.length} entry pages and ${internalUrls.size} internal URLs.`);
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
