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
    const contacts = document.querySelector('.f-bar__contacts');
    const status = document.querySelector('.f-bar__contacts .js-hours__status');
    const staticHours = document.querySelectorAll('.f-bar__contacts > .f-bar__hours:not(.js-hours__status)');

    return {
      headerClass: header ? header.className : '',
      text: contacts ? contacts.textContent.trim().replace(/\s+/g, ' ') : '',
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

(async () => {
  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, deviceScaleFactor: 1 });

  await withMockedHours(page, true);
  const categoryOpen = await readHeaderState(page, '/virivky/');

  assert(categoryOpen.statusClass.includes('js-hours__status'), '/virivky/ header is missing dynamic hours status');
  assert(categoryOpen.statusClass.includes('open'), '/virivky/ mocked open status did not set .open');
  assert(normalizeRgb(categoryOpen.dotColor) === 'rgb(0, 255, 128)', `/virivky/ open dot is ${categoryOpen.dotColor}, expected Figma green`);
  assert(normalizeRgb(categoryOpen.contactColor) === 'rgb(255, 255, 255)', `/virivky/ top contact color is ${categoryOpen.contactColor}, expected white over hero`);
  assert(categoryOpen.text.includes('Po - Pá 8:00-17:00 h'), '/virivky/ header hours label does not match Czech Figma copy');
  assert(categoryOpen.staticHoursCount === 0, '/virivky/ has duplicate static hours markup');
  await assertDropdownPanelHasNoSquareParent(page, '/product/athabascan/');

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
  assert(normalizeRgb(showroomOpen.contactColor) === 'rgb(255, 255, 255)', `/showroom/ top contact color is ${showroomOpen.contactColor}, expected white over hero`);

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

  assert(normalizeRgb(showroomVisibleColor) === 'rgb(255, 255, 255)', `/showroom/ visible header color is ${showroomVisibleColor}, expected white over showroom photo`);

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
