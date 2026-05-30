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

function decodeHtmlAttribute(value) {
  return value
    .replace(/&amp;/g, '&')
    .replace(/&quot;/g, '"')
    .replace(/&#039;/g, "'")
    .replace(/&apos;/g, "'")
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>');
}

function extractAnchorTargets(html) {
  const targets = new Set();
  const pattern = /\s(id|name)=["']([^"']+)["']/gi;
  let match;

  while ((match = pattern.exec(html)) !== null) {
    const value = decodeHtmlAttribute(match[2]).trim();
    if (value) {
      targets.add(value);
    }
  }

  return targets;
}

function shouldIgnore(rawUrl) {
  return (
    rawUrl === '#' ||
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
  const timeout = setTimeout(() => controller.abort(), 15000);

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

async function fetchHtmlForUrl(url, sourcePath, pageHtmlCache) {
  if (pageHtmlCache.has(url)) {
    return pageHtmlCache.get(url);
  }

  let response;
  try {
    response = await fetchWithTimeout(url, { redirect: 'follow' });
  } catch (error) {
    throw new Error(`${sourcePath} links to unreachable internal URL ${url} (${error.name})`);
  }

  if (response.status >= 400) {
    throw new Error(`${sourcePath} links to broken internal URL ${url} (${response.status})`);
  }

  const html = await response.text();
  pageHtmlCache.set(url, html);
  return html;
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

async function checkFragmentTarget(link, pageHtmlCache) {
  const target = decodeURIComponent(link.hash.slice(1)).trim();

  if (!target) {
    return;
  }

  const html = await fetchHtmlForUrl(link.targetUrl, link.sourcePath, pageHtmlCache);
  const anchors = extractAnchorTargets(html);

  if (!anchors.has(target)) {
    throw new Error(`${link.sourcePath} links to missing anchor ${link.rawUrl} on ${link.targetUrl}`);
  }
}

(async () => {
  const base = new URL(baseUrl);
  const internalUrls = new Map();
  const fragmentLinks = [];
  const pageHtmlCache = new Map();
  const forbiddenExternal = new Map();

  for (const path of entryPaths) {
    const html = await fetchPage(path);
    const entryUrl = new URL(path, baseUrl);
    entryUrl.hash = '';
    pageHtmlCache.set(entryUrl.href, html);

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
        const hash = url.hash;
        url.hash = '';
        internalUrls.set(url.href, path);

        if (hash) {
          fragmentLinks.push({
            rawUrl: link.url,
            sourcePath: path,
            targetUrl: url.href,
            hash,
          });
        }
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

  for (let index = 0; index < fragmentLinks.length; index += 8) {
    await Promise.all(fragmentLinks.slice(index, index + 8).map((link) => checkFragmentTarget(link, pageHtmlCache)));
  }

  console.log(`Link smoke passed for ${entryPaths.length} entry pages, ${internalUrls.size} internal URLs, and ${fragmentLinks.length} anchor targets.`);
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
