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

    console.log('Contact map smoke passed.');
  } finally {
    await browser.close();
  }
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
