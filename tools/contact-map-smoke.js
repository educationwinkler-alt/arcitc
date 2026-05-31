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

(async () => {
  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, deviceScaleFactor: 1 });

  try {
    const response = await page.goto(`${baseUrl}/kontakt/`, { waitUntil: 'networkidle', timeout: 60000 });
    assert(response && response.status() < 400, `/kontakt/ returned ${response ? response.status() : 'no response'}`);

    const map = page.locator('.f-local-map').first();
    await map.scrollIntoViewIfNeeded();
    await page.waitForFunction(() => {
      const image = document.querySelector('.f-local-map__image');
      return image && image.complete && image.naturalWidth > 0;
    }, { timeout: 10000 });

    const state = await page.evaluate(() => {
      const rect = (element) => element ? element.getBoundingClientRect().toJSON() : null;
      const mapElement = document.querySelector('.f-local-map');
      const wrapper = document.querySelector('.f-section__map--local');
      const image = document.querySelector('.f-local-map__image');
      const pin = document.querySelector('.f-local-map__pin');
      const card = document.querySelector('.f-local-map__card');
      const firstLabel = document.querySelector('.f-local-map__grid h3');
      const button = document.querySelector('.f-local-map__card .a-button');
      const mapBox = rect(mapElement);
      const imageBox = rect(image);
      const pinBox = rect(pin);
      const cardBox = rect(card);

      return {
        source: image ? image.currentSrc || image.src : '',
        labelsCount: document.querySelectorAll('.f-local-map__label').length,
        wrapperFilter: wrapper ? getComputedStyle(wrapper).filter : '',
        imageFilter: image ? getComputedStyle(image).filter : '',
        overlayBackground: mapElement ? getComputedStyle(mapElement, '::before').backgroundImage : '',
        pinBackground: pin ? getComputedStyle(pin).backgroundColor : '',
        pinDotBackground: pin ? getComputedStyle(pin, '::after').backgroundColor : '',
        labelIconBackground: firstLabel ? getComputedStyle(firstLabel, '::before').backgroundColor : '',
        buttonColor: button ? getComputedStyle(button).color : '',
        buttonBorderColor: button ? getComputedStyle(button).borderColor : '',
        text: mapElement ? mapElement.textContent.trim().replace(/\s+/g, ' ') : '',
        map: mapBox,
        image: imageBox,
        pin: pinBox,
        card: cardBox,
        relative: {
          imageX: imageBox && mapBox ? imageBox.x - mapBox.x : null,
          pinX: pinBox && mapBox ? pinBox.x - mapBox.x : null,
          pinY: pinBox && mapBox ? pinBox.y - mapBox.y : null,
          cardX: cardBox && mapBox ? cardBox.x - mapBox.x : null,
          cardY: cardBox && mapBox ? cardBox.y - mapBox.y : null,
        },
        contactCards: Array.from(document.querySelectorAll('.f-contact-card')).map((card) => ({
          name: card.querySelector('h3') ? card.querySelector('h3').textContent.trim() : '',
          source: card.getAttribute('data-content-source') || '',
          assetStatus: card.querySelector('.f-contact-card__avatar') ? card.querySelector('.f-contact-card__avatar').getAttribute('data-asset-status') || '' : '',
          avatarClass: card.querySelector('.f-contact-card__avatar') ? card.querySelector('.f-contact-card__avatar').className : '',
        })),
        contactDirectoryHeading: document.querySelector('.f-section--contact-directory .f-section__container > h2')
          ? document.querySelector('.f-section--contact-directory .f-section__container > h2').textContent.trim()
          : '',
      };
    });

    assert(state.source.includes('uploads/import/figma/contact-map-showroom.png'), `contact map source mismatch: ${state.source}`);
    assert(state.labelsCount === 0, `contact map should not render custom Brno/Moravany overlay labels, got ${state.labelsCount}`);
    assert(state.wrapperFilter === 'none', `contact map wrapper filter is ${state.wrapperFilter}, expected none so UI stays red`);
    assert(state.imageFilter.includes('grayscale'), `contact map image filter is ${state.imageFilter}, expected grayscale image-only filter`);
    assert(state.overlayBackground.includes('rgba(48, 60, 75'), `contact map overlay mismatch: ${state.overlayBackground}`);

    assertClose(state.map.width, 1920, 2, 'contact.map.width');
    assertClose(state.map.height, 782, 2, 'contact.map.height');
    assertClose(state.relative.imageX, -867, 3, 'contact.mapImage.relativeX');
    assertClose(state.image.width, 3110, 3, 'contact.mapImage.width');
    assertClose(state.image.height, 782, 3, 'contact.mapImage.height');
    assertClose(state.relative.cardX, 260, 3, 'contact.mapCard.relativeX');
    assertClose(state.relative.cardY, 131, 3, 'contact.mapCard.relativeY');
    assertClose(state.card.width, 565, 3, 'contact.mapCard.width');
    assertClose(state.card.height, 491, 3, 'contact.mapCard.height');
    assertClose(state.relative.pinX, 1217.3, 4, 'contact.mapPin.relativeX');
    assertClose(state.relative.pinY, 347.3, 4, 'contact.mapPin.relativeY');

    for (const [label, value] of Object.entries({
      pinBackground: state.pinBackground,
      labelIconBackground: state.labelIconBackground,
    })) {
      assert(normalizeRgb(value) === 'rgb(181, 29, 58)', `${label} is ${value}, expected Figma red`);
    }

    assert(normalizeRgb(state.pinDotBackground) === 'rgba(255, 255, 255, 0.86)', `pin dot is ${state.pinDotBackground}, expected white center`);
    assert(normalizeRgb(state.buttonColor) === 'rgb(163, 31, 55)', `map button text color is ${state.buttonColor}, expected Arctic red`);
    assert(normalizeRgb(state.buttonBorderColor) === 'rgb(163, 31, 55)', `map button border is ${state.buttonBorderColor}, expected Arctic red`);
    assert(state.text.includes('Moravany u Brna') && state.text.includes('Bohunická cesta 15'), 'contact map card address does not match Figma copy');
    assert(state.text.includes('Úterý - Pátek') && state.text.includes('9:00 - 11:30') && state.text.includes('12:30 - 16:00'), 'contact map card hours do not match Figma copy');
    assert(!state.text.includes('Po - Pá 8:00-17:00 h'), 'contact map still contains old compressed hours copy');
    assert(state.contactDirectoryHeading === 'Další důležité kontakty', `contact directory heading is "${state.contactDirectoryHeading}"`);
    assert(state.contactCards.length === 6, `contact directory should render 6 Figma contact cards, got ${state.contactCards.length}`);

    for (const expectedName of [
      'Vlastimil Zhoř',
      'Ing. Lukáš Dušek',
      'Helena Antonyová',
      'Alena Janulíková',
      'Bc. Tomáš Koutný',
      'Pavel Nováček',
    ]) {
      assert(state.contactCards.some((card) => card.name === expectedName), `contact directory is missing ${expectedName}`);
    }

    for (const card of state.contactCards) {
      assert(card.source === 'figma-contact-frame', `${card.name} source is ${card.source}, expected figma-contact-frame`);
      assert(card.assetStatus === 'WAITING_ON_OWNER', `${card.name} avatar status is ${card.assetStatus}, expected WAITING_ON_OWNER`);
      assert(card.avatarClass.includes('f-contact-card__avatar--waiting'), `${card.name} avatar is missing waiting placeholder class`);
    }

    await readLink(page, '/', '.f-footer__quick-map a', 'footer quick map');
    await readLink(page, '/kontakt/', '.f-local-map__card .a-button', 'contact local map CTA');
    await readLink(page, '/showroom/', '.f-showroom-info__item a[href^="http"]', 'showroom map CTA');

    console.log('Contact page smoke passed.');
  } finally {
    await browser.close();
  }
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
