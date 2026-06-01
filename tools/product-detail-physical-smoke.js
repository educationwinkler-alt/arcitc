const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

function intersects(a, b) {
  return Math.max(a.x, b.x) < Math.min(a.right, b.right) && Math.max(a.y, b.y) < Math.min(a.bottom, b.bottom);
}

async function goto(page, path) {
  const response = await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle', timeout: 90000 });
  assert(response && response.status() < 400, `${path} returned ${response ? response.status() : 'no response'}`);
}

async function assertHeaderHasNoVisibleOverlap(page, path) {
  await goto(page, path);

  const state = await page.evaluate(() => {
    const rect = (element) => {
      const box = element.getBoundingClientRect();
      return {
        x: box.x,
        y: box.y,
        width: box.width,
        height: box.height,
        right: box.right,
        bottom: box.bottom,
        text: element.textContent.trim().replace(/\s+/g, ' '),
      };
    };

    const targets = [
      ...document.querySelectorAll('.f-header .f-navigation__list > li > a'),
      document.querySelector('.f-header .f-search__trigger'),
      document.querySelector('.f-header__button .f-button'),
      document.querySelector('.f-header .f-logo'),
    ].filter(Boolean).map(rect).filter((box) => box.width > 1 && box.height > 1);

    return { targets };
  });

  for (let i = 0; i < state.targets.length; i += 1) {
    for (let j = i + 1; j < state.targets.length; j += 1) {
      assert(
        !intersects(state.targets[i], state.targets[j]),
        `${path} header visible controls overlap: "${state.targets[i].text}" with "${state.targets[j].text}"`
      );
    }
  }
}

async function assertProductDetail(path, options = {}) {
  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const viewport = options.viewport || { width: 1920, height: 1080 };
  const page = await browser.newPage({ viewport, deviceScaleFactor: 1 });

  try {
    await goto(page, path);

    const state = await page.evaluate(() => {
      const rect = (element) => {
        const box = element.getBoundingClientRect();
        return {
          x: box.x,
          y: box.y,
          width: box.width,
          height: box.height,
          right: box.right,
          bottom: box.bottom,
          text: element.textContent.trim().replace(/\s+/g, ' '),
        };
      };

      const box = (selector) => {
        const element = document.querySelector(selector);
        if (!element) {
          return null;
        }
        return rect(element);
      };

      const style = (selector, pseudo = null) => {
        const element = document.querySelector(selector);
        return element ? getComputedStyle(element, pseudo) : null;
      };

      const avatarStyle = style('.f-product-contact-card__avatar img');
      const ctaHoursStyle = style('.f-contact-cta__hours');
      const configuratorButtonStyle = style('.f-product-detail-configurator .f-configurator-cta__content .a-button');

      return {
        hero: box('.f-heading--product-detail'),
        heroFacts: box('.f-product-hero__facts'),
        heroFactItems: [...document.querySelectorAll('.f-product-hero__fact')].map((fact) => {
          const factBox = rect(fact);
          const icon = fact.querySelector('.f-product-hero__fact-icon');
          const label = fact.querySelector('.f-product-hero__fact-label');
          const value = fact.querySelector('strong');

          return {
            fact: factBox,
            icon: icon ? rect(icon) : null,
            label: label ? rect(label) : null,
            value: value ? rect(value) : null,
          };
        }),
        productNav: box('.f-links--product .f-links__container'),
        configLayout: box('.f-product-detail-config__layout'),
        firstConfig: box('.f-product-configuration'),
        firstConfigThumb: box('.f-product-configuration__thumb'),
        sidebar: box('.f-product-contact-card'),
        configuratorButton: box('.f-product-detail-configurator .f-configurator-cta__content .a-button'),
        configuratorButtonClass: document.querySelector('.f-product-detail-configurator .f-configurator-cta__content .a-button')?.className || '',
        configuratorButtonStyle: configuratorButtonStyle ? {
          backgroundColor: configuratorButtonStyle.backgroundColor,
          borderColor: configuratorButtonStyle.borderColor,
          borderRadius: configuratorButtonStyle.borderRadius,
          borderWidth: configuratorButtonStyle.borderWidth,
        } : null,
        ctaBar: box('.f-contact-cta__bar'),
        ctaHours: box('.f-contact-cta__hours'),
        ctaHoursStyle: ctaHoursStyle ? {
          position: ctaHoursStyle.position,
          top: ctaHoursStyle.top,
          left: ctaHoursStyle.left,
          height: ctaHoursStyle.height,
        } : null,
        noMediaCards: document.querySelectorAll('.f-product-configuration--no-media').length,
        configurationThumbs: [...document.querySelectorAll('.f-product-configuration__thumb img')].map((image) => image.currentSrc || image.src),
        sidebarHoursClass: document.querySelector('.f-product-contact-card__hours')?.className || '',
        avatar: {
          src: document.querySelector('.f-product-contact-card__avatar img')?.currentSrc || '',
          transform: avatarStyle ? avatarStyle.transform : '',
          objectFit: avatarStyle ? avatarStyle.objectFit : '',
          position: avatarStyle ? avatarStyle.position : '',
          width: avatarStyle ? avatarStyle.width : '',
          height: avatarStyle ? avatarStyle.height : '',
        },
        benefitMedia: [...document.querySelectorAll('.f-product-benefit__media')].map((media) => ({
          hasImage: !!media.querySelector('img'),
          status: media.getAttribute('data-asset-status') || '',
          before: getComputedStyle(media, '::before').content,
          after: getComputedStyle(media, '::after').content,
        })),
        colorItems: [...document.querySelectorAll('.f-product-colors__list:first-of-type .f-product-colors__item')].map((item) => {
          const image = item.querySelector('img');
          return {
            text: item.textContent.trim().replace(/\s+/g, ' '),
            status: item.getAttribute('data-asset-status') || '',
            source: item.getAttribute('data-asset-source') || '',
            hasImage: !!image,
            imageMarker: image ? [
              image.currentSrc || '',
              image.src || '',
              image.getAttribute('src') || '',
              image.getAttribute('srcset') || '',
              image.getAttribute('data-src') || '',
              image.getAttribute('data-srcset') || '',
              image.getAttribute('data-lazy-src') || '',
              image.getAttribute('data-lazy-srcset') || '',
            ].join(' ') : '',
          };
        }),
      };
    });

    assert(state.hero, `${path} is missing product hero`);
    assert(state.productNav, `${path} is missing product sticky nav`);
    const navHeroOverlap = Math.round(state.hero.bottom - state.productNav.y);
    assert(navHeroOverlap >= 35 && navHeroOverlap <= 70, `${path} product nav is not on the Figma hero overlap: ${navHeroOverlap}px`);
    assert(!state.heroFacts || state.productNav.y >= state.heroFacts.bottom + 120, `${path} product nav collides with hero facts`);

    if (options.assertHeroFactFigmaLayout) {
      const minHeroFacts = options.minHeroFacts || 3;
      assert(state.heroFactItems.length >= minHeroFacts, `${path} exposes only ${state.heroFactItems.length} hero facts`);

      for (const [index, item] of state.heroFactItems.entries()) {
        assert(item.icon && item.icon.width >= 55 && item.icon.width <= 60, `${path} hero fact ${index + 1} icon is not Figma-sized: ${item.icon ? item.icon.width : 'missing'}`);
        assert(item.icon.height >= 55 && item.icon.height <= 60, `${path} hero fact ${index + 1} icon height is not Figma-sized: ${item.icon.height}`);
      }

      for (let index = 0; index < state.heroFactItems.length - 1; index += 1) {
        const current = state.heroFactItems[index];
        const next = state.heroFactItems[index + 1];
        assert(current.value && next.icon, `${path} hero fact ${index + 1} is missing value or next icon`);
        assert(current.value.right <= next.icon.x - 4, `${path} hero fact ${index + 1} value collides with the next fact`);
      }
    }

    if (options.heroFactsOnly) {
      return;
    }

    assert(state.configLayout && state.firstConfig, `${path} is missing product configuration layout`);
    assert(state.noMediaCards === 0, `${path} has ${state.noMediaCards} configuration cards without media`);
    assert(state.configurationThumbs.length > 0, `${path} has no configuration thumbnails`);
    assert(state.firstConfigThumb && state.firstConfigThumb.width >= 200 && state.firstConfigThumb.height >= 190, `${path} first configuration thumbnail is not in the Figma card shape`);

    if (options.maxConfigLayoutHeight) {
      assert(state.configLayout.height <= options.maxConfigLayoutHeight, `${path} configuration layout is too tall: ${state.configLayout.height}px`);
    }

    if (state.configuratorButton) {
      assert(state.configuratorButtonClass.includes('a-button--outline'), `${path} configurator CTA button is not the Figma outline variant: ${state.configuratorButtonClass}`);
      assert(state.configuratorButton.width >= 139 && state.configuratorButton.width <= 143, `${path} configurator CTA button width is not Figma-sized: ${state.configuratorButton.width}px`);
      assert(state.configuratorButton.height === 50, `${path} configurator CTA button height is not Figma-sized: ${state.configuratorButton.height}px`);
      assert(state.configuratorButtonStyle.backgroundColor === 'rgba(0, 0, 0, 0)', `${path} configurator CTA button is filled instead of transparent: ${state.configuratorButtonStyle.backgroundColor}`);
      assert(state.configuratorButtonStyle.borderColor === 'rgb(255, 255, 255)', `${path} configurator CTA button border is not white: ${state.configuratorButtonStyle.borderColor}`);
      assert(state.configuratorButtonStyle.borderWidth === '1px', `${path} configurator CTA button border width is not from Figma: ${state.configuratorButtonStyle.borderWidth}`);
      assert(state.configuratorButtonStyle.borderRadius === '50px', `${path} configurator CTA button radius is not from Figma: ${state.configuratorButtonStyle.borderRadius}`);
    }

    if (options.minColorImageCards) {
      assert(state.colorItems.length >= options.minColorImageCards, `${path} exposes only ${state.colorItems.length} shell color cards`);
      for (const [index, item] of state.colorItems.entries()) {
        assert(item.hasImage, `${path} shell color card ${index + 1} (${item.text}) has no swatch image`);
        assert(!item.status.includes('WAITING'), `${path} shell color card ${index + 1} (${item.text}) is unresolved: ${item.status}`);
        assert(/(acrylic-|color-|wp-content\/uploads)/.test(`${item.source} ${item.imageMarker}`), `${path} shell color card ${index + 1} (${item.text}) does not point to a swatch asset`);
      }
    }

    assert(state.sidebarHoursClass.includes('js-hours__status'), `${path} product sidebar hours are not using shared dynamic hours status`);
    assert(state.avatar.src.includes('contact-lukas-dusek.png'), `${path} product sidebar avatar source is not the Figma Dusek asset`);
    assert(state.avatar.transform === 'none', `${path} product sidebar avatar is still CSS-transformed: ${state.avatar.transform}`);
    assert(state.avatar.objectFit === 'contain', `${path} product sidebar avatar object-fit is ${state.avatar.objectFit}, expected contain for rendered Figma crop`);

    assert(state.ctaBar && state.ctaHours, `${path} is missing contact CTA bar or hours`);
    assert(state.ctaHours.y >= state.ctaBar.y, `${path} contact CTA hours start above the bar`);
    assert(state.ctaHours.bottom <= state.ctaBar.bottom - 8, `${path} contact CTA hours overflow the bar by ${Math.round(state.ctaHours.bottom - state.ctaBar.bottom)}px`);

    assert(state.benefitMedia.length >= 20, `${path} exposes only ${state.benefitMedia.length} benefit/options media slots`);
    for (const [index, media] of state.benefitMedia.entries()) {
      assert(media.status === 'available', `${path} benefit/options media ${index + 1} is not an exported Figma asset`);
      assert(media.hasImage, `${path} benefit/options media ${index + 1} has no image`);
      assert(media.before === 'none' && media.after === 'none', `${path} benefit/options media ${index + 1} still renders a generated pseudoicon`);
    }
  } finally {
    await browser.close();
  }
}

(async () => {
  const headerBrowser = await chromium.launch({ executablePath: chromePath, headless: true });

  try {
    for (const width of [1920, 1440]) {
      const page = await headerBrowser.newPage({ viewport: { width, height: 900 }, deviceScaleFactor: 1 });
      await assertHeaderHasNoVisibleOverlap(page, '/product/mckinley/');
      await page.close();
    }
  } finally {
    await headerBrowser.close();
  }

  await assertProductDetail('/product/mckinley/', { maxConfigLayoutHeight: 380, minColorImageCards: 5, assertHeroFactFigmaLayout: true });
  await assertProductDetail('/product/mckinley/', { viewport: { width: 1440, height: 900 }, maxConfigLayoutHeight: 380, minColorImageCards: 5, assertHeroFactFigmaLayout: true });
  await assertProductDetail('/product/athabascan/', { viewport: { width: 1440, height: 900 }, assertHeroFactFigmaLayout: true, minHeroFacts: 2, heroFactsOnly: true });
  await assertProductDetail('/product/lunar/', { minColorImageCards: 4 });
  await assertProductDetail('/product/timberwolf/', { minColorImageCards: 5 });

  console.log('Product detail physical smoke passed.');
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
