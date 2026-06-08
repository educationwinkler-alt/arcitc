const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

const desktopPaths = [
  '/',
  '/virivky/',
  '/swimspa/',
  '/product/timberwolf/',
  '/product/lunar/',
  '/product/orion/',
  '/product/husky/',
  '/product/athabascan/',
  '/reference/',
  '/kontakt/',
  '/o-nas/',
  '/showroom/',
  '/servis/',
  '/podpora/',
  '/ke-stazeni/',
];

const mobilePaths = [
  '/',
  '/virivky/',
  '/reference/',
  '/kontakt/',
  '/podpora/',
  '/ke-stazeni/',
];

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

async function fetchHtml(path) {
  const response = await fetch(`${baseUrl}${path}`);

  if (!response.ok) {
    throw new Error(`${path} returned ${response.status}`);
  }

  return response.text();
}

async function fetchAdminMembers() {
  try {
    const response = await fetch(`${baseUrl}/wp-json/wp/v2/member?per_page=100`);

    if (!response.ok) {
      return [];
    }

    const members = await response.json();

    return Array.isArray(members) ? members : [];
  } catch (_error) {
    return [];
  }
}

function assertReferenceArchive(html) {
  const projectLinks = html.match(/href=(["'])[^"']*\/project\//g) || [];
  assert(projectLinks.length === 0, `/reference/ archive contains /project/ links: ${projectLinks.join(', ')}`);

  const lightboxLinks = html.match(/class=(["'])[^"']*\bjs-image\b/g) || [];
  assert(lightboxLinks.length >= 6, `/reference/ archive should expose PhotoSwipe/lightbox cards, got ${lightboxLinks.length}`);
}

function assertHtmlContracts(path, html) {
  assert(!html.includes('f-footer--handoff'), `${path} still contains forbidden f-footer--handoff class`);

  if (path.startsWith('/product/')) {
    assert(html.includes('data-product-detail-contract="figma"'), `${path} is missing shared product detail contract`);
    assert(!html.includes('f-heading--timberwolf'), `${path} still contains Timberwolf-only heading class`);

    if (path === '/product/athabascan/') {
      assert(html.includes('data-product-detail-scope="swimspa"'), `${path} is missing swimspa detail scope`);
      assert(!html.includes('f-configurator-cta--product'), `${path} must not render hot tub configurator CTA`);
    } else {
      assert(html.includes('data-product-detail-scope="hot-tub"'), `${path} is missing hot tub detail scope`);
      assert(html.includes('f-configurator-cta--product'), `${path} is missing product configurator CTA`);
    }
  }

  if (path === '/reference/') {
    assertReferenceArchive(html);
  }
}

async function assertNoHorizontalOverflow(page, label) {
  const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
  assert(overflow <= 2, `${label} has horizontal overflow of ${overflow}px`);
}

async function assertFooterContract(page, label) {
  const footer = await page.locator('.f-footer--arctic').first();
  assert(await footer.count(), `${label} is missing .f-footer--arctic`);

  const state = await footer.evaluate((element) => {
    const rect = element.getBoundingClientRect();
    const style = getComputedStyle(element);
    const previous = element.previousElementSibling;
    const previousRect = previous ? previous.getBoundingClientRect() : null;

    return {
      className: element.className,
      backgroundImage: style.backgroundImage,
      backgroundColor: style.backgroundColor,
      y: rect.y,
      previousBottom: previousRect ? previousRect.bottom : null,
    };
  });

  assert(!state.className.includes('f-footer--handoff'), `${label} footer contains forbidden handoff class`);
  assert(state.backgroundImage.includes('footer-background'), `${label} footer mountain background is missing: ${state.backgroundImage}`);
  assert(state.backgroundColor !== 'rgb(35, 40, 47)', `${label} footer fell back to solid navy`);

  if (typeof state.previousBottom === 'number') {
    const gap = state.y - state.previousBottom;
    assert(gap > -120 && gap < 220, `${label} footer handoff gap looks wrong: ${Math.round(gap)}px`);
  }
}

async function assertVisibleImagesLoaded(page, label) {
  const broken = await page.evaluate(() => {
    const viewportHeight = window.innerHeight;
    const viewportWidth = window.innerWidth;

    return [...document.querySelectorAll('main img, footer img')]
      .filter((image) => {
        const rect = image.getBoundingClientRect();
        return rect.width > 8 && rect.height > 8 && rect.bottom > 0 && rect.right > 0 && rect.top < viewportHeight && rect.left < viewportWidth;
      })
      .filter((image) => image.naturalWidth < 1 || image.naturalHeight < 1)
      .map((image) => image.currentSrc || image.src || image.getAttribute('src') || image.getAttribute('data-src') || '(missing source)');
  });

  assert(broken.length === 0, `${label} has broken visible images: ${broken.join(', ')}`);
}

async function assertProductAffordances(page) {
  await page.goto(`${baseUrl}/product/timberwolf/`, { waitUntil: 'networkidle', timeout: 90000 });

  const state = await page.evaluate(() => {
    const staticCards = [...document.querySelectorAll('.f-product-benefit--static')].map((card) => ({
      hasTrigger: !!card.querySelector('.f-product-benefit__trigger'),
      hasMore: !!card.querySelector('.f-product-benefit__more'),
      text: card.textContent.trim().replace(/\s+/g, ' ').slice(0, 80),
    }));
    const interactiveCards = [...document.querySelectorAll('.f-product-benefit--interactive')].map((card) => ({
      hasTrigger: !!card.querySelector('.f-product-benefit__trigger'),
      hasMore: !!card.querySelector('.f-product-benefit__more'),
      text: card.textContent.trim().replace(/\s+/g, ' ').slice(0, 80),
    }));
    const mediaWithoutStatus = [...document.querySelectorAll('.f-product-benefit__media')]
      .filter((media) => !media.getAttribute('data-asset-status')).length;
    const mediaWithoutImages = [...document.querySelectorAll('.f-product-benefit__media')]
      .filter((media) => !media.querySelector('img')).length;
    const generatedPseudoMedia = [...document.querySelectorAll('.f-product-benefit__media')]
      .filter((media) => getComputedStyle(media, '::before').content !== 'none' || getComputedStyle(media, '::after').content !== 'none').length;

    return { staticCards, interactiveCards, mediaWithoutStatus, mediaWithoutImages, generatedPseudoMedia };
  });

  assert(state.mediaWithoutStatus === 0, `/product/timberwolf/ has ${state.mediaWithoutStatus} benefit/options media without data-asset-status`);
  assert(state.mediaWithoutImages === 0, `/product/timberwolf/ has ${state.mediaWithoutImages} benefit/options media slots without exported Figma images`);
  assert(state.generatedPseudoMedia === 0, `/product/timberwolf/ still has ${state.generatedPseudoMedia} generated pseudoicon media slots`);
  assert(state.staticCards.length > 0, '/product/timberwolf/ has no static benefit cards to verify');
  for (const card of state.staticCards) {
    assert(!card.hasTrigger && !card.hasMore, `/product/timberwolf/ static card looks interactive: ${card.text}`);
  }

  assert(state.interactiveCards.length > 0, '/product/timberwolf/ has no interactive benefit cards to verify');
  for (const card of state.interactiveCards) {
    assert(card.hasTrigger && card.hasMore, `/product/timberwolf/ interactive card is missing trigger/more affordance: ${card.text}`);
  }
}

async function assertWaitingOnOwnerMarkers(page) {
  const adminMembers = await fetchAdminMembers();

  await page.goto(`${baseUrl}/kontakt/`, { waitUntil: 'networkidle', timeout: 90000 });

  if (adminMembers.length > 0) {
    const contactCards = await page.evaluate(() => Array.from(document.querySelectorAll('.f-contact-card')).map((card) => {
      const avatar = card.querySelector('.f-contact-card__avatar');
      const image = avatar ? avatar.querySelector('img') : null;

      return {
        name: card.querySelector('h3') ? card.querySelector('h3').textContent.trim() : '',
        source: card.getAttribute('data-content-source') || '',
        memberId: card.getAttribute('data-member-id') || '',
        avatarStatus: avatar ? avatar.getAttribute('data-asset-status') || '' : '',
        avatarSource: image ? image.getAttribute('data-src') || image.currentSrc || image.src : '',
      };
    }));

    assert(contactCards.length === adminMembers.length, `/kontakt/ expected ${adminMembers.length} member-backed contact cards, got ${contactCards.length}`);
    assert(contactCards.every((card) => card.source === 'admin-member'), '/kontakt/ contains non-admin contact card sources');
    assert(contactCards.every((card) => Number(card.memberId) > 0), '/kontakt/ contact cards must expose WP member ids');

    const tomas = contactCards.find((card) => card.name.includes('Tomáš Koutný'));
    assert(tomas, '/kontakt/ is missing Tomáš Koutný');
    assert(tomas.avatarStatus === 'admin-member', `/kontakt/ Tomáš Koutný avatar status is ${tomas ? tomas.avatarStatus : '(missing)'}`);
    assert(tomas.avatarSource.includes('contact-tomas-koutny'), `/kontakt/ Tomáš Koutný avatar source is ${tomas ? tomas.avatarSource : '(missing)'}`);

    const figmaFallbacks = contactCards.filter((card) => card.source.includes('figma') || card.avatarStatus.includes('figma')).length;
    assert(figmaFallbacks === 0, `/kontakt/ still has ${figmaFallbacks} Figma/Baspa fallback contact cards`);
  }

  await page.goto(`${baseUrl}/zaruka/`, { waitUntil: 'networkidle', timeout: 90000 });
  const warrantyWaiting = await page.locator('.f-warranty-card[data-asset-status="WAITING_ON_OWNER"]').count();
  assert(warrantyWaiting === 3, `/zaruka/ expected 3 .f-warranty-card[data-asset-status="WAITING_ON_OWNER"], got ${warrantyWaiting}`);

  const usesFigmaFallbackTeam = adminMembers.length === 0;
  const expectedPeople = adminMembers.length || 4;

  await page.goto(`${baseUrl}/o-nas/`, { waitUntil: 'networkidle', timeout: 90000 });

  const teamCards = await page.locator('.f-about-person').count();
  assert(teamCards === expectedPeople, `/o-nas/ expected ${expectedPeople} team cards, got ${teamCards}`);

  if (usesFigmaFallbackTeam) {
    const figmaPortraits = await page.locator('.f-about-person__media[data-asset-status="figma-export"] img').count();
    const waitingMedia = await page.locator('.f-about-person__media[data-asset-status="WAITING_ON_OWNER"]').count();

    assert(figmaPortraits === 4, `/o-nas/ fallback expected 4 Figma portraits, got ${figmaPortraits}`);
    assert(waitingMedia === 0, `/o-nas/ fallback must not show waiting placeholders, got ${waitingMedia}`);
  } else {
    const adminMedia = await page.locator('.f-about-person__media[data-asset-status="admin-member"]').count();
    const avatarFallbackMedia = await page.locator('.f-about-person__media[data-asset-status="admin-member-avatar-fallback"]').count();
    const waitingMedia = await page.locator('.f-about-person__media[data-asset-status="WAITING_ON_OWNER"]').count();

    assert(adminMedia + avatarFallbackMedia + waitingMedia === teamCards, `/o-nas/ admin team media mismatch: ${adminMedia} admin images + ${avatarFallbackMedia} avatar fallbacks + ${waitingMedia} waiting media for ${teamCards} cards`);
  }
}

(async () => {
  for (const path of desktopPaths) {
    const html = await fetchHtml(path);
    assertHtmlContracts(path, html);
  }

  const browser = await chromium.launch({ executablePath: chromePath, headless: true });

  try {
    const page = await browser.newPage({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1 });

    for (const path of desktopPaths) {
      await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle', timeout: 90000 });
      await assertNoHorizontalOverflow(page, `${path} desktop`);
      await assertFooterContract(page, `${path} desktop`);
      await assertVisibleImagesLoaded(page, `${path} desktop`);
    }

    await assertProductAffordances(page);
    await assertWaitingOnOwnerMarkers(page);

    const mobile = await browser.newPage({ viewport: { width: 390, height: 900 }, deviceScaleFactor: 1 });

    for (const path of mobilePaths) {
      await mobile.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle', timeout: 90000 });
      await assertNoHorizontalOverflow(mobile, `${path} mobile`);
      await assertFooterContract(mobile, `${path} mobile`);
      await assertVisibleImagesLoaded(mobile, `${path} mobile`);
    }
  } finally {
    await browser.close();
  }

  console.log('Final QA smoke passed.');
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
