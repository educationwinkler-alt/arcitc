const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

function normalizeRgb(value) {
  return value.replace(/\s+/g, ' ').trim();
}

async function withMockedHours(page, open) {
  await page.route('**/wp-admin/admin-ajax.php', async (route) => {
    await route.fulfill({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({ open }),
    });
  });
}

async function readHeaderState(page, path) {
  await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle', timeout: 60000 });
  await page.waitForFunction(() => {
    const status = document.querySelector('.f-bar__contacts .js-hours__status');
    return status && (status.classList.contains('open') || status.classList.contains('closed'));
  }, { timeout: 10000 });

  return page.evaluate(() => {
    const header = document.querySelector('.f-header');
    const bar = document.querySelector('.f-bar');
    const contacts = document.querySelector('.f-bar__contacts');
    const status = document.querySelector('.f-bar__contacts .js-hours__status');
    const staticHours = document.querySelectorAll('.f-bar__contacts > .f-bar__hours:not(.js-hours__status)');
    const barStyle = bar ? getComputedStyle(bar) : null;

    return {
      headerClass: header ? header.className : '',
      text: contacts ? contacts.textContent.trim().replace(/\s+/g, ' ') : '',
      barBackground: barStyle ? barStyle.backgroundColor : '',
      barOpacity: barStyle ? barStyle.opacity : '',
      barVisibility: barStyle ? barStyle.visibility : '',
      contactColor: contacts ? getComputedStyle(contacts).color : '',
      dotColor: status ? getComputedStyle(status, '::before').backgroundColor : '',
      statusClass: status ? status.className : '',
      staticHoursCount: staticHours.length,
    };
  });
}

async function readStatusState(page, selector) {
  const locator = page.locator(selector).first();
  await locator.waitFor({ state: 'attached', timeout: 10000 });

  return locator.evaluate((element) => ({
    className: element.className,
    text: element.textContent.trim().replace(/\s+/g, ' '),
    dotColor: getComputedStyle(element, '::before').backgroundColor,
  }));
}

async function assertDropdownPanelHasNoSquareParent(page, path) {
  await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle', timeout: 60000 });
  await page.locator('.f-header .arctic-menu-info > a').hover();
  await page.waitForFunction(() => {
    const submenu = document.querySelector('.f-header .arctic-menu-info > .f-navigation-sub');
    return submenu && getComputedStyle(submenu).opacity === '1';
  }, { timeout: 10000 });

  const state = await page.evaluate(() => {
    const submenu = document.querySelector('.f-header .arctic-menu-info > .f-navigation-sub');
    const panel = document.querySelector('.f-header .arctic-menu-info > .f-navigation-sub > .f-navigation-sub__panel');
    const submenuStyle = submenu ? getComputedStyle(submenu) : null;
    const panelStyle = panel ? getComputedStyle(panel) : null;

    return {
      hasSubmenu: !!submenu,
      hasPanel: !!panel,
      submenuBackground: submenuStyle ? submenuStyle.backgroundColor : '',
      submenuBackgroundImage: submenuStyle ? submenuStyle.backgroundImage : '',
      panelBorderRadius: panelStyle ? panelStyle.borderRadius : '',
      panelOverflow: panelStyle ? panelStyle.overflow : '',
    };
  });

  assert(state.hasSubmenu && state.hasPanel, `${path} info dropdown markup is missing`);
  assert(normalizeRgb(state.submenuBackground) === 'rgba(0, 0, 0, 0)', `${path} dropdown parent has a visible square background: ${state.submenuBackground}`);
  assert(state.submenuBackgroundImage === 'none', `${path} dropdown parent still has a background image: ${state.submenuBackgroundImage}`);
  assert(state.panelBorderRadius !== '0px', `${path} dropdown panel is not rounded`);
  assert(state.panelOverflow === 'hidden', `${path} dropdown panel does not clip its rounded corners`);
}

async function assertHeaderMenuLinkDecorations(page, path) {
  await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle', timeout: 60000 });
  await page.locator('.f-header .arctic-menu-features > a').hover();
  await page.waitForFunction(() => {
    const submenu = document.querySelector('.f-header .arctic-menu-features > .f-navigation-sub');
    return submenu && getComputedStyle(submenu).opacity === '1';
  }, { timeout: 10000 });

  const submenuState = await page.evaluate(() => {
    const topLink = document.querySelector('.f-header .arctic-menu-features > a');
    const subLink = document.querySelector('.f-header .arctic-menu-features .f-navigation-sub a');
    const topStyle = topLink ? getComputedStyle(topLink) : null;
    const subStyle = subLink ? getComputedStyle(subLink) : null;

    return {
      topBorder: topStyle ? topStyle.borderBottomWidth : '',
      topDecoration: topStyle ? topStyle.textDecorationLine : '',
      subBorder: subStyle ? subStyle.borderBottomWidth : '',
      subDecoration: subStyle ? subStyle.textDecorationLine : '',
    };
  });

  assert(submenuState.topBorder === '0px', `${path} header top menu link has extra border underline: ${submenuState.topBorder}`);
  assert(submenuState.topDecoration === 'none', `${path} header top menu link has extra text underline: ${submenuState.topDecoration}`);
  assert(submenuState.subBorder === '0px', `${path} header submenu link has extra border underline: ${submenuState.subBorder}`);
  assert(submenuState.subDecoration === 'none', `${path} header submenu link has extra text underline: ${submenuState.subDecoration}`);

  await page.locator('.f-header .f-navigation__list > .arctic-menu-products:nth-child(1) > a').hover();
  await page.waitForFunction(() => {
    const mega = document.querySelector('.f-header .f-mega-menu--hot-tubs');
    return mega && getComputedStyle(mega).opacity === '1';
  }, { timeout: 10000 });

  const megaState = await page.evaluate(() => {
    const product = document.querySelector('.f-header .f-mega-menu--hot-tubs .f-mega-menu__product');
    const promo = document.querySelector('.f-header .f-mega-menu--hot-tubs .f-mega-menu__promo');
    const promoImage = promo ? promo.querySelector('img') : null;
    const productStyle = product ? getComputedStyle(product) : null;
    const promoStyle = promo ? getComputedStyle(promo) : null;

    return {
      productBorder: productStyle ? productStyle.borderBottomWidth : '',
      productDecoration: productStyle ? productStyle.textDecorationLine : '',
      promoBorder: promoStyle ? promoStyle.borderBottomWidth : '',
      promoDecoration: promoStyle ? promoStyle.textDecorationLine : '',
      promoImageStatus: promoImage ? promoImage.getAttribute('data-asset-status') || '' : '',
      promoImageSource: promoImage ? promoImage.getAttribute('data-src') || promoImage.currentSrc || promoImage.src : '',
    };
  });

  assert(megaState.productBorder === '0px', `${path} mega product link has extra border underline: ${megaState.productBorder}`);
  assert(megaState.productDecoration === 'none', `${path} mega product link has extra text underline: ${megaState.productDecoration}`);
  assert(megaState.promoBorder === '0px', `${path} mega promo link has extra border underline: ${megaState.promoBorder}`);
  assert(megaState.promoDecoration === 'none', `${path} mega promo link has extra text underline: ${megaState.promoDecoration}`);
  assert(megaState.promoImageStatus === 'admin-offer-promo', `${path} mega promo image status is ${megaState.promoImageStatus}`);
  assert(megaState.promoImageSource.includes('hp-fixed-banner-product'), `${path} mega promo should use the offer promo image, got ${megaState.promoImageSource}`);
}

async function assertHeaderScrollContract(page) {
  await page.goto(`${baseUrl}/virivky/`, { waitUntil: 'networkidle', timeout: 60000 });
  await page.addStyleTag({ content: 'html, body { scroll-behavior: auto !important; }' });
  await page.evaluate(() => window.scrollTo(0, 900));
  await page.waitForTimeout(250);

  const category = await page.evaluate(() => {
    const header = document.querySelector('.f-header');
    const bar = document.querySelector('.f-bar');
    const panel = document.querySelector('.f-header__container');
    const rect = header ? header.getBoundingClientRect() : null;
    const panelRect = panel ? panel.getBoundingClientRect() : null;
    const style = header ? getComputedStyle(header) : null;
    const barStyle = bar ? getComputedStyle(bar) : null;
    const panelStyle = panel ? getComputedStyle(panel) : null;

    return {
      className: header ? header.className : '',
      y: rect ? rect.y : null,
      position: style ? style.position : '',
      transform: style ? style.transform : '',
      barOpacity: barStyle ? barStyle.opacity : '',
      barVisibility: barStyle ? barStyle.visibility : '',
      panelY: panelRect ? panelRect.y : null,
      panelBottom: panelRect ? panelRect.bottom : null,
      panelTransform: panelStyle ? panelStyle.transform : '',
    };
  });

  assert(category.position === 'fixed', `/virivky/ header shell should stay fixed while scrolling, got ${category.position}`);
  assert(Math.abs(category.y) <= 1, `/virivky/ fixed header shell moved during scroll: y=${category.y}, class=${category.className}, transform=${category.transform}`);
  assert(category.transform === 'none', `/virivky/ header shell should not move; only the menu panel should move: ${category.transform}`);
  assert(category.barOpacity === '0' && category.barVisibility === 'hidden', `/virivky/ top contact bar should hide after scroll: opacity=${category.barOpacity}, visibility=${category.barVisibility}`);
  assert(category.panelBottom > 0, `/virivky/ menu panel should remain visible after the contact bar scrolls away: bottom=${category.panelBottom}, transform=${category.panelTransform}`);
  assert(!category.className.includes('is-autohide--zone-hidden'), `/virivky/ should not hide the main header without a product/detail sticky zone: ${category.className}`);

  await page.goto(`${baseUrl}/product/mckinley/`, { waitUntil: 'networkidle', timeout: 60000 });
  await page.addStyleTag({ content: 'html, body { scroll-behavior: auto !important; }' });
  await page.evaluate(() => window.scrollTo(0, 24));
  await page.waitForTimeout(250);

  const productBeforeHandoff = await page.evaluate(() => {
    const header = document.querySelector('.f-header');
    const panel = document.querySelector('.f-header__container');
    const navigation = document.querySelector('.f-links--product');
    const headerRect = header ? header.getBoundingClientRect() : null;
    const panelRect = panel ? panel.getBoundingClientRect() : null;
    const navRect = navigation ? navigation.getBoundingClientRect() : null;

    return {
      headerClass: header ? header.className : '',
      headerY: headerRect ? headerRect.y : null,
      panelBottom: panelRect ? panelRect.bottom : null,
      navY: navRect ? navRect.y : null,
    };
  });

  assert(!productBeforeHandoff.headerClass.includes('is-section-nav-handoff'), `/product/mckinley/ header handed off before product sticky nav reached the top: header=${productBeforeHandoff.headerClass}, navY=${productBeforeHandoff.navY}`);
  assert(!productBeforeHandoff.headerClass.includes('is-autohide--zone-hidden'), `/product/mckinley/ header entered zone-hidden before product sticky nav reached the top: header=${productBeforeHandoff.headerClass}, navY=${productBeforeHandoff.navY}`);
  assert(Math.abs(productBeforeHandoff.headerY) <= 1, `/product/mckinley/ header shell moved during early hero scroll: y=${productBeforeHandoff.headerY}, navY=${productBeforeHandoff.navY}`);
  assert(productBeforeHandoff.panelBottom > 0, `/product/mckinley/ menu panel disappeared before product sticky nav reached the top: bottom=${productBeforeHandoff.panelBottom}, navY=${productBeforeHandoff.navY}`);

  await page.evaluate(() => {
    const navigation = document.querySelector('.f-links--product');
    const target = navigation ? navigation.getBoundingClientRect().top + window.scrollY + 180 : 900;
    window.scrollTo(0, target);
  });
  await page.waitForFunction(() => document.querySelector('.f-header')?.classList.contains('is-autohide--zone-hidden'), { timeout: 10000 });
  await page.waitForTimeout(250);

  const product = await page.evaluate(() => {
    const header = document.querySelector('.f-header');
    const panel = document.querySelector('.f-header__container');
    const navigation = document.querySelector('.f-links--product');
    const headerRect = header ? header.getBoundingClientRect() : null;
    const panelRect = panel ? panel.getBoundingClientRect() : null;
    const navRect = navigation ? navigation.getBoundingClientRect() : null;
    const headerStyle = header ? getComputedStyle(header) : null;
    const panelStyle = panel ? getComputedStyle(panel) : null;
    const navStyle = navigation ? getComputedStyle(navigation) : null;

    return {
      headerClass: header ? header.className : '',
      headerBottom: headerRect ? headerRect.bottom : null,
      headerPosition: headerStyle ? headerStyle.position : '',
      headerTransform: headerStyle ? headerStyle.transform : '',
      panelBottom: panelRect ? panelRect.bottom : null,
      panelTransform: panelStyle ? panelStyle.transform : '',
      navY: navRect ? navRect.y : null,
      navBottom: navRect ? navRect.bottom : null,
      navPosition: navStyle ? navStyle.position : '',
    };
  });

  assert(product.headerPosition === 'fixed', `/product/mckinley/ header should be fixed before it hands off, got ${product.headerPosition}`);
  assert(product.headerClass.includes('is-autohide--zone-hidden'), `/product/mckinley/ header did not enter Baspa-style zone-hidden handoff: ${product.headerClass}`);
  assert(product.headerTransform === 'none', `/product/mckinley/ header shell should not move during handoff; got ${product.headerTransform}`);
  assert(product.panelBottom <= 2, `/product/mckinley/ menu panel still overlaps the product sticky nav after handoff: bottom=${product.panelBottom}, transform=${product.panelTransform}`);
  assert(product.navPosition === 'sticky', `/product/mckinley/ product detail navigation should remain sticky, got ${product.navPosition}`);
  assert(Math.abs(product.navY) <= 2, `/product/mckinley/ product detail navigation did not take over the top edge: y=${product.navY}, bottom=${product.navBottom}`);
}

(async () => {
  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, deviceScaleFactor: 1 });

  await withMockedHours(page, true);
  const categoryOpen = await readHeaderState(page, '/virivky/');

  assert(categoryOpen.statusClass.includes('js-hours__status'), '/virivky/ header is missing dynamic hours status');
  assert(categoryOpen.statusClass.includes('open'), '/virivky/ mocked open status did not set .open');
  assert(normalizeRgb(categoryOpen.dotColor) === 'rgb(0, 255, 128)', `/virivky/ open dot is ${categoryOpen.dotColor}, expected Figma green`);
  assert(normalizeRgb(categoryOpen.barBackground) === 'rgba(0, 0, 0, 0)', `/virivky/ top contact strip should be transparent at rest, got ${categoryOpen.barBackground}`);
  assert(normalizeRgb(categoryOpen.contactColor) === 'rgb(255, 255, 255)', `/virivky/ top contact color is ${categoryOpen.contactColor}, expected white text over dark hero`);
  assert(categoryOpen.text.includes('Po - Pá 8:00-17:00 h'), '/virivky/ header hours label does not match Czech Figma copy');
  assert(categoryOpen.staticHoursCount === 0, '/virivky/ has duplicate static hours markup');
  await assertDropdownPanelHasNoSquareParent(page, '/product/athabascan/');
  await assertHeaderMenuLinkDecorations(page, '/o-nas/');
  await assertHeaderScrollContract(page);

  await page.unroute('**/wp-admin/admin-ajax.php');

  await withMockedHours(page, false);
  const contactClosed = await readHeaderState(page, '/kontakt/');

  assert(contactClosed.statusClass.includes('closed'), '/kontakt/ mocked closed status did not set .closed');
  assert(normalizeRgb(contactClosed.dotColor) === 'rgb(163, 31, 55)', `/kontakt/ closed dot is ${contactClosed.dotColor}, expected Arctic red`);
  assert(normalizeRgb(contactClosed.contactColor) === 'rgb(35, 40, 47)', `/kontakt/ top contact color is ${contactClosed.contactColor}, expected dark text on light background`);
  assert(contactClosed.staticHoursCount === 0, '/kontakt/ has duplicate static hours markup');

  await page.goto(`${baseUrl}/showroom/`, { waitUntil: 'networkidle', timeout: 60000 });
  await page.waitForFunction(() => document.querySelector('.f-contact-cta__hours.closed') && document.querySelector('.f-footer__quick-hours.closed'), { timeout: 10000 });
  const ctaClosed = await readStatusState(page, '.f-contact-cta__hours');
  const footerClosed = await readStatusState(page, '.f-footer__quick-hours');

  assert(ctaClosed.className.includes('js-hours__status'), 'contact CTA hours are not wired to dynamic hours status');
  assert(footerClosed.className.includes('js-hours__status'), 'footer quick hours are not wired to dynamic hours status');
  assert(ctaClosed.className.includes('closed'), 'contact CTA mocked closed status did not set .closed');
  assert(footerClosed.className.includes('closed'), 'footer quick mocked closed status did not set .closed');
  assert(normalizeRgb(ctaClosed.dotColor) === 'rgb(163, 31, 55)', `contact CTA closed dot is ${ctaClosed.dotColor}, expected Arctic red`);
  assert(normalizeRgb(footerClosed.dotColor) === 'rgb(163, 31, 55)', `footer quick closed dot is ${footerClosed.dotColor}, expected Arctic red`);

  await page.unroute('**/wp-admin/admin-ajax.php');

  await withMockedHours(page, true);
  const showroomOpen = await readHeaderState(page, '/showroom/');

  assert(showroomOpen.statusClass.includes('open'), '/showroom/ mocked open status did not set .open');
  assert(normalizeRgb(showroomOpen.barBackground) === 'rgba(0, 0, 0, 0)', `/showroom/ top contact strip should be transparent at rest, got ${showroomOpen.barBackground}`);
  assert(normalizeRgb(showroomOpen.contactColor) === 'rgb(255, 255, 255)', `/showroom/ top contact color is ${showroomOpen.contactColor}, expected white text over dark hero`);

  const ctaOpen = await readStatusState(page, '.f-contact-cta__hours');
  const footerOpen = await readStatusState(page, '.f-footer__quick-hours');

  assert(ctaOpen.className.includes('open'), 'contact CTA mocked open status did not set .open');
  assert(footerOpen.className.includes('open'), 'footer quick mocked open status did not set .open');
  assert(normalizeRgb(ctaOpen.dotColor) === 'rgb(0, 255, 128)', `contact CTA open dot is ${ctaOpen.dotColor}, expected Figma green`);
  assert(normalizeRgb(footerOpen.dotColor) === 'rgb(0, 255, 128)', `footer quick open dot is ${footerOpen.dotColor}, expected Figma green`);

  await page.goto(`${baseUrl}/showroom/#fotogalerie`, { waitUntil: 'networkidle', timeout: 60000 });
  await page.waitForFunction(() => document.querySelector('.f-bar__contacts .js-hours__status.open'), { timeout: 10000 });
  const showroomVisibleColor = await page.evaluate(() => {
    const header = document.querySelector('.f-header');
    const contacts = document.querySelector('.f-bar__contacts');
    header.classList.remove('is-autohide--hidden');
    header.classList.add('is-autohide--visible');
    return getComputedStyle(contacts).color;
  });

  assert(normalizeRgb(showroomVisibleColor) === 'rgb(35, 40, 47)', `/showroom/ visible header color is ${showroomVisibleColor}, expected dark text on light strip`);

  await page.unroute('**/wp-admin/admin-ajax.php');

  await withMockedHours(page, true);
  await page.goto(`${baseUrl}/virivky/`, { waitUntil: 'networkidle', timeout: 60000 });
  await page.waitForFunction(() => document.querySelector('.f-bar__contacts .js-hours__status.open'), { timeout: 10000 });
  const visibleColor = await page.evaluate(() => {
    const header = document.querySelector('.f-header');
    const contacts = document.querySelector('.f-bar__contacts');
    header.classList.add('is-autohide--visible');
    return getComputedStyle(contacts).color;
  });

  assert(normalizeRgb(visibleColor) === 'rgb(35, 40, 47)', `/virivky/ visible header color is ${visibleColor}, expected dark text on light header state`);

  await browser.close();
  console.log('Header status smoke passed.');
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
