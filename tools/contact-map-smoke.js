const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

function assertClose(actual, expected, tolerance, label) {
  if (Math.abs(actual - expected) > tolerance) {
    throw new Error(`${label}: expected ${expected} +/- ${tolerance}, got ${actual}`);
  }
}

function normalizeRgb(value) {
  return String(value).replace(/\s+/g, ' ').trim();
}

function assertExternalMapsLink(label, link) {
  assert(link, `${label} map link is missing.`);
  assert(!link.href.startsWith(`${baseUrl}/kontakt`) && !link.href.startsWith(`${baseUrl}/showroom`), `${label} map link is internal: ${link.href}`);
  assert(
    /^https:\/\/(maps\.app\.goo\.gl|www\.google\.com\/maps|google\.com\/maps|maps\.google\.)/.test(link.href),
    `${label} map link is not a Google Maps URL: ${link.href}`
  );
  assert(link.target === '_blank', `${label} map link target is ${link.target}, expected _blank`);
  assert(link.rel.split(/\s+/).includes('noopener'), `${label} map link rel is ${link.rel}, expected noopener`);
}

async function readLink(page, path, selector, label) {
  const response = await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle', timeout: 60000 });
  assert(response && response.status() < 400, `${path} returned ${response ? response.status() : 'no response'}`);

  const link = await page.locator(selector).first().evaluate((anchor) => ({
    href: anchor.href,
    target: anchor.getAttribute('target') || '',
    rel: anchor.getAttribute('rel') || '',
    text: anchor.textContent.trim().replace(/\s+/g, ' '),
  }));

  assertExternalMapsLink(label, link);
  return link;
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

(async () => {
  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, deviceScaleFactor: 1 });

  try {
    const adminMembers = await fetchAdminMembers();
    const response = await page.goto(`${baseUrl}/kontakt/`, { waitUntil: 'networkidle', timeout: 60000 });
    assert(response && response.status() < 400, `/kontakt/ returned ${response ? response.status() : 'no response'}`);

    const map = page.locator('.f-local-map').first();
    await map.scrollIntoViewIfNeeded();
    await page.waitForFunction(() => !!document.querySelector('.f-local-map__iframe'), { timeout: 10000 });

    const state = await page.evaluate(() => {
      const rect = (element) => element ? element.getBoundingClientRect().toJSON() : null;
      const mapElement = document.querySelector('.f-local-map');
      const wrapper = document.querySelector('.f-section__map');
      const embedWrapper = document.querySelector('.f-section__map--embed');
      const iframe = document.querySelector('.f-local-map__iframe');
      const image = document.querySelector('.f-local-map__image');
      const card = document.querySelector('.f-local-map__card');
      const firstLabel = document.querySelector('.f-local-map__grid h3');
      const button = document.querySelector('.f-local-map__card .a-button');
      const mapBox = rect(mapElement);
      const iframeBox = rect(iframe);
      const cardBox = rect(card);

      return {
        source: iframe ? iframe.src : '',
        decodedSource: iframe ? decodeURIComponent(iframe.src) : '',
        contentSource: mapElement ? mapElement.getAttribute('data-content-source') || '' : '',
        hasEmbedWrapper: !!embedWrapper,
        hasFallbackImage: !!image,
        hasOverlayPin: !!document.querySelector('.f-local-map__pin'),
        labelsCount: document.querySelectorAll('.f-local-map__label').length,
        wrapperFilter: wrapper ? getComputedStyle(wrapper).filter : '',
        iframeFilter: iframe ? getComputedStyle(iframe).filter : '',
        iframePointerEvents: iframe ? getComputedStyle(iframe).pointerEvents : '',
        overlayPointerEvents: mapElement ? getComputedStyle(mapElement, '::before').pointerEvents : '',
        overlayBackground: mapElement ? getComputedStyle(mapElement, '::before').backgroundImage : '',
        overlayBackgroundColor: mapElement ? getComputedStyle(mapElement, '::before').backgroundColor : '',
        labelIconBackground: firstLabel ? getComputedStyle(firstLabel, '::before').backgroundColor : '',
        buttonColor: button ? getComputedStyle(button).color : '',
        buttonBorderColor: button ? getComputedStyle(button).borderColor : '',
        text: mapElement ? mapElement.textContent.trim().replace(/\s+/g, ' ') : '',
        map: mapBox,
        iframe: iframeBox,
        card: cardBox,
        relative: {
          iframeX: iframeBox && mapBox ? iframeBox.x - mapBox.x : null,
          iframeY: iframeBox && mapBox ? iframeBox.y - mapBox.y : null,
          cardX: cardBox && mapBox ? cardBox.x - mapBox.x : null,
          cardY: cardBox && mapBox ? cardBox.y - mapBox.y : null,
        },
        contactCards: Array.from(document.querySelectorAll('.f-contact-card')).map((card) => ({
          name: card.querySelector('h3') ? card.querySelector('h3').textContent.trim() : '',
          source: card.getAttribute('data-content-source') || '',
          memberId: card.getAttribute('data-member-id') || '',
          assetStatus: card.querySelector('.f-contact-card__avatar') ? card.querySelector('.f-contact-card__avatar').getAttribute('data-asset-status') || '' : '',
          avatarClass: card.querySelector('.f-contact-card__avatar') ? card.querySelector('.f-contact-card__avatar').className : '',
          hasImage: !!card.querySelector('.f-contact-card__avatar img'),
        })),
      };
    });

    assert(state.contentSource === 'customizer-map-embed', `contact map content source is ${state.contentSource}, expected customizer-map-embed`);
    assert(state.hasEmbedWrapper, 'contact map should render the embed wrapper on local');
    assert(state.source.includes('google.com/maps'), `contact map iframe source mismatch: ${state.source}`);
    assert(state.source.includes('output=embed'), `contact map iframe should use an embeddable URL: ${state.source}`);
    assert(state.decodedSource.includes('q=49.149,16.589'), `contact map iframe should target the showroom coordinates, got ${state.decodedSource}`);
    assert(state.decodedSource.includes('ll=49.149,16.407'), `contact map iframe should use the Figma-wide west-shifted viewport, got ${state.decodedSource}`);
    assert(state.source.includes('z=11'), `contact map iframe should use the Figma-wide map zoom z=11, got ${state.source}`);
    assert(!state.hasFallbackImage, 'contact map should not render the static Figma fallback image when embed is configured');
    assert(!state.hasOverlayPin, 'contact map must not render a fake red overlay pin over the Google embed');
    assert(state.labelsCount === 0, `contact map should not render fake Brno/Moravany overlay labels, got ${state.labelsCount}`);
    assert(state.wrapperFilter === 'none', `contact map wrapper filter is ${state.wrapperFilter}, expected none so UI stays red`);
    assert(state.iframeFilter.includes('grayscale'), `contact map iframe filter is ${state.iframeFilter}, expected grayscale map treatment`);
    assert(state.iframePointerEvents === 'auto', `contact map iframe pointer-events is ${state.iframePointerEvents}, expected auto`);
    assert(state.overlayPointerEvents === 'none', `contact map overlay pointer-events is ${state.overlayPointerEvents}, expected none so the iframe stays interactive`);
    assert(normalizeRgb(state.overlayBackgroundColor) === 'rgba(35, 40, 47, 0.72)', `contact map overlay should use the dark Figma map veil, got: ${state.overlayBackgroundColor}`);
    assert(!state.overlayBackground.includes('radial-gradient'), `contact map overlay must not contain the removed red radial fleck: ${state.overlayBackground}`);
    assert(!state.overlayBackground.includes('rgba(163, 31, 55'), `contact map overlay still contains the red fleck color: ${state.overlayBackground}`);
    assert(!state.overlayBackground.includes('rgba(48, 60, 75'), `contact map overlay still uses the old dark BASPA layer: ${state.overlayBackground}`);

    assertClose(state.map.width, 1920, 2, 'contact.map.width');
    assertClose(state.map.height, 782, 2, 'contact.map.height');
    assertClose(state.relative.iframeX, 0, 2, 'contact.mapIframe.relativeX');
    assertClose(state.relative.iframeY, 0, 2, 'contact.mapIframe.relativeY');
    assertClose(state.iframe.width, 1920, 2, 'contact.mapIframe.width');
    assertClose(state.iframe.height, 782, 2, 'contact.mapIframe.height');
    assertClose(state.relative.cardX, 260, 3, 'contact.mapCard.relativeX');
    assertClose(state.relative.cardY, 131, 3, 'contact.mapCard.relativeY');
    assertClose(state.card.width, 565, 3, 'contact.mapCard.width');
    assertClose(state.card.height, 491, 3, 'contact.mapCard.height');
    assert(normalizeRgb(state.labelIconBackground) === 'rgb(181, 29, 58)', `label icon is ${state.labelIconBackground}, expected Figma red`);
    assert(normalizeRgb(state.buttonColor) === 'rgb(163, 31, 55)', `map button text color is ${state.buttonColor}, expected Arctic red`);
    assert(normalizeRgb(state.buttonBorderColor) === 'rgb(163, 31, 55)', `map button border is ${state.buttonBorderColor}, expected Arctic red`);
    assert(state.text.includes('Moravany u Brna') && state.text.includes('Bohunická cesta 15'), 'contact map card address does not match Figma copy');
    assert(state.text.includes('9:00 - 11:30') && state.text.includes('12:30 - 16:00'), 'contact map card hours do not match Figma copy');
    assert(!state.text.includes('Po - Pá 8:00-17:00 h'), 'contact map still contains old compressed hours copy');

    assert(adminMembers.length >= 6, `WP admin should expose seeded member CPT entries, got ${adminMembers.length}`);
    assert(state.contactCards.length === 6, `contact directory should render 6 admin member contact cards, got ${state.contactCards.length}`);

    const cardsWithAdminImages = state.contactCards.filter((card) => card.assetStatus === 'admin-member' && card.hasImage).length;
    assert(cardsWithAdminImages >= 4, `contact directory should render seeded admin member portraits, got ${cardsWithAdminImages}`);

    for (const card of state.contactCards) {
      assert(card.source === 'admin-member', `${card.name} source is ${card.source}, expected admin-member`);
      assert(card.memberId && Number(card.memberId) > 0, `${card.name} is missing data-member-id from WP admin`);
      assert(['admin-member', 'WAITING_ON_OWNER'].includes(card.assetStatus), `${card.name} avatar status is ${card.assetStatus}`);

      if (card.assetStatus === 'WAITING_ON_OWNER') {
        assert(card.avatarClass.includes('f-contact-card__avatar--waiting'), `${card.name} avatar is missing waiting placeholder class`);
      }
    }

    await readLink(page, '/', '.f-footer__quick-map a', 'footer quick map');
    await readLink(page, '/kontakt/', '.f-local-map__card .a-button', 'contact interactive map CTA');
    await readLink(page, '/showroom/', '.f-showroom-info__item a[href^="http"]', 'showroom map CTA');

    console.log('Contact page smoke passed.');
  } finally {
    await browser.close();
  }
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
