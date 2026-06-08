const { chromium } = require('playwright-core');

const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
}

function stripTags(value) {
  return String(value || '').replace(/<[^>]+>/g, '').trim();
}

async function fetchAdminMembers() {
  const response = await fetch(`${baseUrl}/wp-json/wp/v2/member?per_page=100`);

  assert(response.ok, `member REST endpoint returned ${response.status}`);

  const members = await response.json();

  assert(Array.isArray(members), 'member REST endpoint did not return an array');

  return members;
}

async function fetchAdminServices() {
  const response = await fetch(`${baseUrl}/wp-json/wp/v2/service?per_page=100`);

  assert(response.ok, `service REST endpoint returned ${response.status}`);

  const services = await response.json();

  assert(Array.isArray(services), 'service REST endpoint did not return an array');

  return services;
}

async function fetchAdminFeatures() {
  const response = await fetch(`${baseUrl}/wp-json/wp/v2/feature?per_page=100`);

  assert(response.ok, `feature REST endpoint returned ${response.status}`);

  const features = await response.json();

  assert(Array.isArray(features), 'feature REST endpoint did not return an array');

  return features;
}

async function fetchAdminSlides() {
  const response = await fetch(`${baseUrl}/wp-json/wp/v2/slide?per_page=100`);

  assert(response.ok, `slide REST endpoint returned ${response.status}`);

  const slides = await response.json();

  assert(Array.isArray(slides), 'slide REST endpoint did not return an array');

  return slides;
}

async function fetchAdminOffers() {
  const response = await fetch(`${baseUrl}/wp-json/wp/v2/offer?per_page=100`);

  assert(response.ok, `offer REST endpoint returned ${response.status}`);

  const offers = await response.json();

  assert(Array.isArray(offers), 'offer REST endpoint did not return an array');

  return offers;
}

async function fetchAdminJobs() {
  const response = await fetch(`${baseUrl}/wp-json/wp/v2/job?per_page=100`);

  assert(response.ok, `job REST endpoint returned ${response.status}`);

  const jobs = await response.json();

  assert(Array.isArray(jobs), 'job REST endpoint did not return an array');

  return jobs;
}

async function fetchAdminProductColors() {
  const response = await fetch(`${baseUrl}/wp-json/wp/v2/spa_color?per_page=100`);

  assert(response.ok, `spa_color REST endpoint returned ${response.status}`);

  const colors = await response.json();

  assert(Array.isArray(colors), 'spa_color REST endpoint did not return an array');

  return colors;
}

async function goto(page, path) {
  const response = await page.goto(`${baseUrl}${path}`, { waitUntil: 'networkidle', timeout: 90000 });

  assert(response && response.status() < 400, `${path} returned ${response ? response.status() : 'no response'}`);
}

async function readContactSlot(page, path, rootSelector, label) {
  await goto(page, path);

  const state = await page.locator(rootSelector).first().evaluate((root) => {
    const container = root.closest('.f-contact-cta, .f-quick-contact-card, .f-product-contact-card, .f-footer__quick-contact, .f-support-help-card') || root;
    const avatar = root.querySelector('[class*="avatar"]');
    const image = avatar ? avatar.querySelector('img') : null;
    const strong = root.querySelector('strong');
    const role = strong && strong.nextElementSibling ? strong.nextElementSibling.textContent.trim() : '';

    return {
      memberId: container.getAttribute('data-member-id') || root.getAttribute('data-member-id') || '',
      text: container.textContent.trim().replace(/\s+/g, ' '),
      name: strong ? strong.textContent.trim() : '',
      role,
      avatarStatus: avatar ? avatar.getAttribute('data-asset-status') || '' : '',
      avatarSource: image ? image.getAttribute('data-src') || image.getAttribute('data-lazy-src') || image.currentSrc || image.src : '',
      avatarObjectFit: image ? getComputedStyle(image).objectFit : '',
      avatarWidth: avatar ? Math.round(avatar.getBoundingClientRect().width) : 0,
      avatarHeight: avatar ? Math.round(avatar.getBoundingClientRect().height) : 0,
      hasImage: !!image,
    };
  });

  assert(state.name.includes('Tomáš Koutný'), `${label} selected member is "${state.name}"`);
  assert(state.role === 'Prodej vířivek', `${label} selected member role is "${state.role}"`);
  assert(Number(state.memberId) > 0, `${label} does not expose a WP member id`);
  assert(state.text.includes('tomas.koutny@baspa.cz'), `${label} is missing selected member email`);
  assert(state.text.includes('+420 602 149 106'), `${label} is missing selected member phone`);
  assert(state.avatarStatus === 'admin-member', `${label} avatar status is ${state.avatarStatus}, expected admin-member`);
  assert(state.hasImage && state.avatarSource, `${label} admin avatar image is missing`);
  assert(state.avatarSource.includes('contact-tomas-koutny'), `${label} should use the member contact avatar, got ${state.avatarSource}`);
  assert(state.avatarObjectFit === 'cover', `${label} avatar object-fit is ${state.avatarObjectFit}`);
  assert(state.avatarWidth >= 48 && state.avatarHeight >= 48, `${label} avatar box is ${state.avatarWidth}x${state.avatarHeight}`);
}

async function readCategoryTermAdminState(page, path, label) {
  await goto(page, path);

  const state = await page.evaluate(() => {
    const intro = document.querySelector('.f-section--category-intro');
    const headingImage = document.querySelector('.f-heading--term .f-background__image img');
    const imageSource = (image) => image ? image.getAttribute('data-src') || image.currentSrc || image.src : '';

    return {
      source: intro ? intro.getAttribute('data-content-source') || '' : '',
      termId: intro ? intro.getAttribute('data-term-id') || '' : '',
      cardCount: document.querySelectorAll('.f-category-intro').length,
      text: intro ? intro.textContent.trim().replace(/\s+/g, ' ') : '',
      ctaHrefs: Array.from(document.querySelectorAll('.f-section--category-intro .a-button')).map((link) => link.href),
      imageStatuses: Array.from(document.querySelectorAll('.f-section--category-intro img')).map((image) => image.getAttribute('data-asset-status') || ''),
      imageSources: Array.from(document.querySelectorAll('.f-section--category-intro img')).map(imageSource),
      headingMediaType: headingImage ? headingImage.closest('[data-hero-media]')?.getAttribute('data-hero-media') || '' : '',
      headingAssetStatus: headingImage ? headingImage.closest('[data-asset-status]')?.getAttribute('data-asset-status') || '' : '',
      headingImageSource: imageSource(headingImage),
      hasSeedFallbackImage: Array.from(document.querySelectorAll('.f-section--category-intro img')).some((image) => (image.getAttribute('data-asset-status') || '') === 'seed-fallback'),
    };
  });

  assert(state.source === 'term-meta', `${label} category intro source is ${state.source}`);
  assert(Number(state.termId) > 0, `${label} category intro does not expose a WP term id`);
  assert(state.cardCount === 2, `${label} should render 2 editable intro cards, got ${state.cardCount}`);
  assert(state.imageStatuses.every((status) => status === 'admin-category-intro'), `${label} intro images must come from term media, got ${state.imageStatuses.join(', ')}`);
  assert(!state.hasSeedFallbackImage, `${label} still renders a seed fallback image`);
  assert(state.headingMediaType === 'image', `${label} heading media type is ${state.headingMediaType}`);
  assert(state.headingAssetStatus === 'admin-term-hero-image', `${label} heading asset status is ${state.headingAssetStatus}`);
  assert(state.ctaHrefs.some((href) => href.includes('/vlastnosti/')), `${label} intro is missing the editable features CTA`);
  assert(state.ctaHrefs.some((href) => href.includes('/zaruka/')), `${label} intro is missing the editable warranty CTA`);

  return state;
}

(async () => {
  const members = await fetchAdminMembers();
  const services = await fetchAdminServices();
  const features = await fetchAdminFeatures();
  const slides = await fetchAdminSlides();
  const offers = await fetchAdminOffers();
  const jobs = await fetchAdminJobs();
  const productColors = await fetchAdminProductColors();
  const names = members.map((member) => stripTags(member.title && member.title.rendered));
  const jobNames = jobs.map((job) => stripTags(job.title && job.title.rendered));
  const productColorNames = productColors.map((color) => stripTags(color.title && color.title.rendered));

  assert(members.length >= 6, `expected at least 6 admin members, got ${members.length}`);
  for (const expectedName of [
    'Vlastimil Zhoř',
    'Ing. Lukáš Dušek',
    'Helena Antonyová',
    'Alena Janulíková',
    'Bc. Tomáš Koutný',
    'Pavel Nováček',
  ]) {
    assert(names.includes(expectedName), `member REST is missing ${expectedName}`);
  }

  assert(services.length === 6, `expected 6 editable services, got ${services.length}`);
  assert(features.length === 8, `expected 8 editable features, got ${features.length}`);
  assert(slides.length >= 1, `expected at least 1 editable published slide, got ${slides.length}`);
  assert(offers.length >= 1, `expected at least 1 editable offer, got ${offers.length}`);
  assert(jobs.length >= 3, `expected at least 3 editable jobs, got ${jobs.length}`);
  assert(productColors.length >= 7, `expected at least 7 editable product colors, got ${productColors.length}`);
  assert(jobNames.includes('Montážní technik'), 'job REST is missing Montážní technik');
  assert(jobNames.filter((name) => name === 'Obchodník na prodejně v Moravanech').length >= 2, 'job REST is missing the two Figma career rows');
  for (const expectedColor of ['Dakota', 'Kalahari', 'Odyssey', 'Platinum Swirl', 'Espresso']) {
    assert(productColorNames.includes(expectedColor), `spa_color REST is missing ${expectedColor}`);
  }
  assert(slides.some((slide) => stripTags(slide.title && slide.title.rendered).includes('Kanadsk')), 'slide REST is missing the seeded homepage hero slide');
  assert(offers.some((offer) => stripTags(offer.title && offer.title.rendered).includes('Akční nabídky')), 'offer REST is missing the Akční nabídky offer');

  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const page = await browser.newPage({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1 });

  try {
    await goto(page, '/kontakt/');

    const contactForm = await page.evaluate(() => {
      const form = document.querySelector('.f-form--contact');
      const select = form ? form.querySelector('select[name="f-interest"]') : null;

      return {
        source: form ? form.getAttribute('data-content-source') || '' : '',
        optionValues: select ? Array.from(select.querySelectorAll('option')).map((option) => option.value).filter(Boolean) : [],
        optionLabels: select ? Array.from(select.querySelectorAll('option')).map((option) => option.textContent.trim().replace(/\s+/g, ' ')).filter(Boolean) : [],
        namePlaceholder: form ? form.querySelector('[name="f-name"]')?.getAttribute('placeholder') || '' : '',
        submitLabel: form ? form.querySelector('.f-form--submit')?.textContent.trim().replace(/\s+/g, ' ') || '' : '',
      };
    });

    assert(contactForm.source === 'contact-settings', `contact form source is ${contactForm.source}`);
    assert(contactForm.optionValues.includes('offer'), `contact form interest options are missing the admin offer slug: ${contactForm.optionValues.join(', ')}`);
    assert(contactForm.optionLabels.includes('Akční nabídka'), `contact form interest options are missing Akční nabídka: ${contactForm.optionLabels.join(', ')}`);
    assert(contactForm.namePlaceholder.includes('jméno'), `contact form placeholder did not render from contact settings: ${contactForm.namePlaceholder}`);
    assert(contactForm.submitLabel === 'Odeslat', `contact form submit label is ${contactForm.submitLabel}`);

    const directory = await page.evaluate(() => Array.from(document.querySelectorAll('.f-contact-card')).map((card) => {
      const avatar = card.querySelector('.f-contact-card__avatar');
      const image = avatar ? avatar.querySelector('img') : null;

      return {
        name: card.querySelector('h3') ? card.querySelector('h3').textContent.trim() : '',
        source: card.getAttribute('data-content-source') || '',
        memberId: card.getAttribute('data-member-id') || '',
        avatarStatus: avatar ? avatar.getAttribute('data-asset-status') || '' : '',
        hasImage: !!image,
      };
    }));

    assert(directory.length === 6, `contact directory should render 6 member cards, got ${directory.length}`);
    assert(directory.every((card) => card.source === 'admin-member'), 'contact directory still contains non-admin card sources');
    assert(directory.every((card) => Number(card.memberId) > 0), 'contact directory cards must expose WP member ids');
    assert(directory.filter((card) => card.avatarStatus === 'admin-member' && card.hasImage).length >= 4, 'contact directory should render seeded member photos');

    const billingBox = await page.evaluate(() => {
      const box = document.querySelector('.f-billing-box');

      return {
        source: box ? box.getAttribute('data-content-source') || '' : '',
        text: box ? box.textContent.trim().replace(/\s+/g, ' ') : '',
      };
    });

    assert(billingBox.source === 'customizer-about', `billing box source is ${billingBox.source}`);
    assert(billingBox.text.includes('BASPA s.r.o.'), `billing box is missing seeded company: ${billingBox.text}`);
    assert(billingBox.text.includes('02257467'), `billing box is missing seeded company id: ${billingBox.text}`);
    assert(billingBox.text.includes('Krajsk'), `billing box is missing seeded registry note: ${billingBox.text}`);

    await readContactSlot(page, '/virivky/', '.f-contact-cta__person', 'shared contact CTA');
    await readContactSlot(page, '/product/timberwolf/', '.f-quick-contact-card__person', 'product sidebar');
    await readContactSlot(page, '/nabidka/akcni-nabidky-arctic-spas/', '.f-quick-contact-card__person', 'offer sidebar');
    await readContactSlot(page, '/', '.f-footer__quick-person', 'footer quick contact');
    await readContactSlot(page, '/podpora/', '.f-support-help-card__person', 'support help card');

    const hotTubCategory = await readCategoryTermAdminState(page, '/virivky/', 'hot tub term');
    assert(hotTubCategory.headingImageSource.includes('virivky-hero-leto-jezero-01'), `hot tub heading should use the editable curator term hero image, got ${hotTubCategory.headingImageSource}`);
    assert(hotTubCategory.imageSources.some((source) => source.includes('category-vlastnosti')), `hot tub intro is missing the admin category-vlastnosti image: ${hotTubCategory.imageSources.join(', ')}`);
    assert(hotTubCategory.imageSources.some((source) => source.includes('category-zaruka')), `hot tub intro is missing the admin category-zaruka image: ${hotTubCategory.imageSources.join(', ')}`);

    const swimspaCategory = await readCategoryTermAdminState(page, '/swimspa/', 'swimspa term');
    assert(swimspaCategory.headingImageSource.includes('celorocni-bazen-hero-hory-lifestyle-01'), `swimspa heading should use the editable curator term hero image, got ${swimspaCategory.headingImageSource}`);
    assert(swimspaCategory.imageSources.some((source) => source.includes('celorocni-bazen-hero-ocean-side-render-01')), `swimspa intro is missing the admin curator Ocean render: ${swimspaCategory.imageSources.join(', ')}`);
    assert(swimspaCategory.imageSources.some((source) => source.includes('vlastnosti-termokryt-celorocni-bazen-01')), `swimspa intro is missing the admin curator cover image: ${swimspaCategory.imageSources.join(', ')}`);

    await goto(page, '/product/timberwolf/');
    const productBenefitSections = await page.evaluate(() => ({
      benefitSource: document.querySelector('.f-section--product-benefits')?.getAttribute('data-content-source') || '',
      optionSource: document.querySelector('.f-section--product-options')?.getAttribute('data-content-source') || '',
      benefitCount: document.querySelectorAll('.f-section--product-benefits .f-product-benefit').length,
      optionCount: document.querySelectorAll('.f-section--product-options .f-product-benefit').length,
      interactiveCount: document.querySelectorAll('.f-section--product-benefits .f-product-benefit--interactive').length,
      benefitStatuses: Array.from(document.querySelectorAll('.f-section--product-benefits .f-product-benefit__media')).map((media) => media.getAttribute('data-asset-status') || ''),
      optionStatuses: Array.from(document.querySelectorAll('.f-section--product-options .f-product-benefit__media')).map((media) => media.getAttribute('data-asset-status') || ''),
      popupSource: document.querySelector('.f-benefit-popup__media img')?.getAttribute('src') || '',
    }));

    assert(productBenefitSections.benefitSource === 'product_benefit_items', `product benefits source is ${productBenefitSections.benefitSource}`);
    assert(productBenefitSections.optionSource === 'product_option_items', `product options source is ${productBenefitSections.optionSource}`);
    assert(productBenefitSections.benefitCount === 18, `Timberwolf should render 18 editable benefit cards, got ${productBenefitSections.benefitCount}`);
    assert(productBenefitSections.optionCount === 8, `Timberwolf should render 8 editable option cards, got ${productBenefitSections.optionCount}`);
    assert(productBenefitSections.interactiveCount >= 1, 'Timberwolf benefits should keep at least one interactive popup card');
    assert(productBenefitSections.benefitStatuses.every((status) => status === 'admin-product-benefit'), 'Timberwolf benefit media must render only admin-assigned assets');
    assert(productBenefitSections.optionStatuses.every((status) => status === 'admin-product-benefit'), 'Timberwolf option media must render only admin-assigned assets');
    assert(productBenefitSections.popupSource === '' || productBenefitSections.popupSource.includes('/wp-content/uploads/'), 'Timberwolf benefit popup must render only admin-assigned media');

    const colorSwatches = await page.evaluate(() => Array.from(document.querySelectorAll('.f-section--product-colors .f-product-colors__item')).map((item) => ({
      source: item.getAttribute('data-content-source') || '',
      colorId: item.getAttribute('data-color-id') || '',
      status: item.getAttribute('data-asset-status') || '',
      text: item.textContent.trim().replace(/\s+/g, ' '),
    })));

    assert(colorSwatches.length >= 7, `Timberwolf should render global shell/cabinet colors, got ${colorSwatches.length}`);
    assert(colorSwatches.every((swatch) => swatch.source === 'spa_color'), 'Timberwolf color swatches must come from the global spa_color catalog');
    assert(colorSwatches.every((swatch) => Number(swatch.colorId) > 0), 'Timberwolf color swatches must expose WP color ids');
    assert(colorSwatches.every((swatch) => swatch.status === 'admin-product-color'), 'Timberwolf color swatches must render admin catalog media');
    assert(colorSwatches.some((swatch) => swatch.text.includes('Platinum Swirl')), 'Timberwolf color swatches are missing Platinum Swirl');

    await goto(page, '/nabidka/akcni-nabidky-arctic-spas/');
    const offerDetail = await page.evaluate(() => ({
      title: document.querySelector('h1, h2')?.textContent.trim() || '',
      text: document.body.textContent.trim().replace(/\s+/g, ' '),
      image: document.querySelector('meta[property="og:image"]')?.getAttribute('content') || '',
    }));

    assert(offerDetail.text.includes('Akční nabídky'), 'offer detail is missing the Akční nabídky title');
    assert(offerDetail.text.includes('Vybrané skladové modely'), 'seeded offer detail is missing the admin offer description');
    assert(offerDetail.image.includes('timberwolf-signature'), 'seeded offer detail should render the admin featured image');

    await goto(page, '/akcni-nabidky/');
    const offersArchive = await page.evaluate(() => {
      const firstCard = document.querySelector('.f-offer-card');
      const image = firstCard ? firstCard.querySelector('.f-offer-card__media img') : null;
      const button = firstCard ? firstCard.querySelector('.f-offer-card__button') : null;

      return {
        introSource: document.querySelector('.f-offers-archive__intro')?.getAttribute('data-content-source') || '',
        gridSource: document.querySelector('.f-offer-grid')?.getAttribute('data-content-source') || '',
        cardCount: document.querySelectorAll('.f-offer-card').length,
        firstCardSource: firstCard ? firstCard.getAttribute('data-content-source') || '' : '',
        firstCardId: firstCard ? firstCard.getAttribute('data-offer-id') || '' : '',
        firstCardText: firstCard ? firstCard.textContent.trim().replace(/\s+/g, ' ') : '',
        imageStatus: image ? image.getAttribute('data-asset-status') || '' : '',
        imageSource: image ? image.currentSrc || image.src : '',
        buttonHref: button ? button.href : '',
      };
    });

    assert(offersArchive.introSource === 'wp-editor', `offers archive intro source is ${offersArchive.introSource}`);
    assert(offersArchive.gridSource === 'offer-cpt', `offers archive grid source is ${offersArchive.gridSource}`);
    assert(offersArchive.cardCount >= 1, `offers archive should render at least 1 admin offer card, got ${offersArchive.cardCount}`);
    assert(offersArchive.firstCardSource === 'offer-cpt', `offers archive first card source is ${offersArchive.firstCardSource}`);
    assert(Number(offersArchive.firstCardId) > 0, 'offers archive cards must expose WP offer ids');
    assert(offersArchive.firstCardText.includes('Akční nabídky'), 'offers archive is missing the Akční nabídky offer title');
    assert(offersArchive.firstCardText.includes('Na vyžádání'), 'offers archive is missing the seeded offer price/status details');
    assert(offersArchive.imageStatus === 'admin-offer', `offers archive image status is ${offersArchive.imageStatus}`);
    assert(offersArchive.imageSource.includes('timberwolf-signature'), 'offers archive should render the admin featured image');
    assert(offersArchive.buttonHref.includes('/nabidka/akcni-nabidky-arctic-spas/'), `offers archive button href is ${offersArchive.buttonHref}`);

    await goto(page, '/o-nas/');
    const about = await page.evaluate(() => ({
      introSource: document.querySelector('.f-about-figma__intro')?.getAttribute('data-content-source') || '',
      statsSource: document.querySelector('.f-about-figma__stats')?.getAttribute('data-content-source') || '',
      jobsSource: document.querySelector('.f-about-figma__jobs')?.getAttribute('data-content-source') || '',
      statsCount: document.querySelectorAll('.f-about-figma__stats > div').length,
      jobCount: document.querySelectorAll('.f-about-job').length,
      detailsJobCount: document.querySelectorAll('details.f-about-job').length,
      jobIds: Array.from(document.querySelectorAll('.f-about-job')).map((job) => job.getAttribute('data-job-id') || ''),
      openJobCount: document.querySelectorAll('details.f-about-job[open]').length,
      closedJobIconColor: getComputedStyle(document.querySelector('details.f-about-job:not([open]) .f-about-job__icon')).backgroundColor,
      introText: document.querySelector('.f-about-figma__intro')?.textContent.trim().replace(/\s+/g, ' ') || '',
    }));

    assert(about.introSource === 'wp-editor', `about intro source is ${about.introSource}`);
    assert(about.statsSource === 'about-meta', `about stats source is ${about.statsSource}`);
    assert(about.jobsSource === 'job-cpt', `about jobs source is ${about.jobsSource}`);
    assert(about.statsCount === 3, `about stats should render 3 admin rows, got ${about.statsCount}`);
    assert(about.jobCount >= 3, `about jobs should render at least 3 editable rows, got ${about.jobCount}`);
    assert(about.detailsJobCount === about.jobCount, `about jobs must be expandable details, got ${about.detailsJobCount} details for ${about.jobCount} rows`);
    assert(about.jobIds.every((id) => Number(id) > 0), 'about job rows must expose WP job ids');
    assert(about.openJobCount === 0, `about jobs should start closed, got ${about.openJobCount} open rows`);
    assert(about.closedJobIconColor === 'rgb(163, 31, 55)', `about closed job icon is ${about.closedJobIconColor}`);
    assert(about.introText.includes('Arctic Spas'), 'about intro editor content is missing Arctic Spas text');

    await page.evaluate(() => document.querySelector('details.f-about-job:first-child .f-about-job__summary')?.click());
    await page.waitForTimeout(120);

    const openedJob = await page.evaluate(() => ({
      openCount: document.querySelectorAll('details.f-about-job[open]').length,
      openIconColor: getComputedStyle(document.querySelector('details.f-about-job[open] .f-about-job__icon')).backgroundColor,
      primaryButtonColor: getComputedStyle(document.querySelector('details.f-about-job[open] .wp-block-button:not(.is-style-outline) .wp-block-button__link')).backgroundColor,
    }));

    assert(openedJob.openCount === 1, `about clicking a job plus should open one row, got ${openedJob.openCount}`);
    assert(openedJob.openIconColor === 'rgb(35, 40, 47)', `about open job icon is ${openedJob.openIconColor}`);
    assert(openedJob.primaryButtonColor === 'rgb(163, 31, 55)', `about primary job CTA background is ${openedJob.primaryButtonColor}`);

    await page.evaluate(() => document.querySelector('details.f-about-job[open] .f-about-job__summary')?.click());
    await page.waitForTimeout(120);

    const closedAgainCount = await page.locator('details.f-about-job[open]').count();
    assert(closedAgainCount === 0, `about clicking a job minus should close the row, got ${closedAgainCount}`);

    await goto(page, '/sluzby/');
    const serviceCards = await page.evaluate(() => Array.from(document.querySelectorAll('.f-service-card')).map((card) => {
      const image = card.querySelector('img');

      return {
        source: card.getAttribute('data-content-source') || '',
        serviceId: card.getAttribute('data-service-id') || '',
        title: card.querySelector('h2') ? card.querySelector('h2').textContent.trim() : '',
        imageSource: image ? image.currentSrc || image.src : '',
        assetStatus: image ? image.getAttribute('data-asset-status') || '' : '',
      };
    }));

    assert(serviceCards.length === 6, `services page should render 6 service CPT cards, got ${serviceCards.length}`);
    assert(serviceCards.every((card) => card.source === 'service-cpt'), 'services page still contains non-CPT card sources');
    assert(serviceCards.every((card) => Number(card.serviceId) > 0), 'service cards must expose WP service ids');
    assert(serviceCards.every((card) => card.assetStatus === 'admin-service' && card.imageSource), 'service cards must render admin media images');
    assert(serviceCards.some((card) => card.imageSource.includes('service-consultation')), 'services page is missing the consultation media image');

    await goto(page, '/vlastnosti/');
    const featureCards = await page.evaluate(() => Array.from(document.querySelectorAll('.f-figma-card--feature')).map((card) => ({
      source: card.getAttribute('data-content-source') || '',
      featureId: card.getAttribute('data-feature-id') || '',
      assetStatus: card.getAttribute('data-asset-status') || '',
      href: card.getAttribute('href') || '',
      title: card.querySelector('strong') ? card.querySelector('strong').textContent.trim() : '',
    })));

    assert(featureCards.length === 8, `features page should render 8 feature CPT cards, got ${featureCards.length}`);
    assert(featureCards.every((card) => card.source === 'feature-cpt'), 'features page still contains non-CPT card sources');
    assert(featureCards.every((card) => Number(card.featureId) > 0), 'feature cards must expose WP feature ids');
    assert(featureCards.every((card) => card.assetStatus === 'admin-feature'), 'seeded feature cards should render admin featured images from feature CPT');
    const expectedFeatureHrefs = [
      '/vlastnosti/izolace-virivky/',
      '/vlastnosti/zaruka-na-skorepinu/',
      '/vlastnosti/termokryt/',
      '/vlastnosti/podlaha-virivky/',
      '/vlastnosti/servisni-pristup/',
      '/vlastnosti/variabilita/',
      '/vlastnosti/automaticka-dezinfekce/',
      '/sluzby/',
    ];

    for (const href of expectedFeatureHrefs) {
      assert(featureCards.some((card) => card.href.includes(href)), `features page is missing linked feature URL ${href}`);
    }

    const badInternalFeatureLinks = featureCards.filter((card) => card.title !== 'Služby' && (!card.href.includes('/vlastnosti/') || card.href.includes('#')));
    assert(badInternalFeatureLinks.length === 0, `feature cards should link to editable WP detail pages, got ${JSON.stringify(badInternalFeatureLinks)}`);

    await goto(page, '/vlastnosti/izolace-virivky/');
    const featureDetail = await page.evaluate(() => ({
      articleSource: document.querySelector('.f-figma-article--feature-detail')?.getAttribute('data-content-source') || '',
      featureId: document.querySelector('.f-figma-article--feature-detail')?.getAttribute('data-feature-id') || '',
      sectionCount: document.querySelectorAll('.f-figma-article--feature-detail section').length,
      heroStatus: document.querySelector('.f-figma-article__hero')?.getAttribute('data-asset-status') || '',
      diagramStatus: document.querySelector('.f-figma-article__diagram')?.getAttribute('data-asset-status') || '',
      relatedSources: Array.from(document.querySelectorAll('.f-section--feature-related .f-figma-card--feature')).map((card) => card.getAttribute('data-content-source') || ''),
      text: document.querySelector('.f-figma-article--feature-detail')?.textContent.trim().replace(/\s+/g, ' ') || '',
    }));

    assert(featureDetail.articleSource === 'wp-editor', `feature detail article source is ${featureDetail.articleSource}`);
    assert(Number(featureDetail.featureId) > 0, 'feature detail article must expose the linked feature id');
    assert(featureDetail.sectionCount >= 6, `feature detail should render expanded editor sections, got ${featureDetail.sectionCount}`);
    assert(featureDetail.heroStatus === 'admin-feature-detail', `feature detail hero status is ${featureDetail.heroStatus}`);
    assert(featureDetail.diagramStatus === 'admin-feature-detail', `feature detail diagram status is ${featureDetail.diagramStatus}`);
    assert(featureDetail.relatedSources.length === 8, `feature detail should render 8 related feature cards, got ${featureDetail.relatedSources.length}`);
    assert(featureDetail.relatedSources.every((source) => source === 'feature-cpt'), 'feature detail related cards must come from feature CPT');
    assert(featureDetail.text.includes('FreeHeat'), 'feature detail editor content is missing FreeHeat text');
    assert(featureDetail.text.includes('Mylovac'), 'feature detail editor content is missing Mylovac text');
    assert(featureDetail.text.includes('RossExhaust'), 'feature detail editor content is missing RossExhaust text');
    assert(featureDetail.text.includes('Forever Floor'), 'feature detail editor content is missing Forever Floor text');

    const featureDetailExpectations = [
      { path: '/vlastnosti/zaruka-na-skorepinu/', keywords: ['Aristech', 'Bio-Lok', 'záruční matici'] },
      { path: '/vlastnosti/termokryt/', keywords: ['Mylovac', 'Castcore', 'Weathershield'] },
      { path: '/vlastnosti/podlaha-virivky/', keywords: ['Forever Floor', 'rovnou plochu', 'Custom'] },
      { path: '/vlastnosti/servisni-pristup/', keywords: ['odnímatelné', 'upgrade', 'Spa Boy'] },
      { path: '/vlastnosti/variabilita/', keywords: ['2,4 milionu', 'skladových', 'Akční nabídky'] },
      { path: '/vlastnosti/automaticka-dezinfekce/', keywords: ['Spa Boy', 'ORP', 'EcoPack'] },
    ];

    for (const expectation of featureDetailExpectations) {
      await goto(page, expectation.path);
      const detail = await page.evaluate(() => ({
        articleSource: document.querySelector('.f-figma-article--feature-detail')?.getAttribute('data-content-source') || '',
        featureId: document.querySelector('.f-figma-article--feature-detail')?.getAttribute('data-feature-id') || '',
        sectionCount: document.querySelectorAll('.f-figma-article--feature-detail section').length,
        relatedSources: Array.from(document.querySelectorAll('.f-section--feature-related .f-figma-card--feature')).map((card) => card.getAttribute('data-content-source') || ''),
        text: document.querySelector('.f-figma-article--feature-detail')?.textContent.trim().replace(/\s+/g, ' ') || '',
      }));

      assert(detail.articleSource === 'wp-editor', `${expectation.path} article source is ${detail.articleSource}`);
      assert(Number(detail.featureId) > 0, `${expectation.path} must expose the linked feature id`);
      assert(detail.sectionCount >= 4, `${expectation.path} should render at least 4 editor sections, got ${detail.sectionCount}`);
      assert(detail.relatedSources.length === 8, `${expectation.path} should render 8 related feature cards, got ${detail.relatedSources.length}`);
      assert(detail.relatedSources.every((source) => source === 'feature-cpt'), `${expectation.path} related cards must come from feature CPT`);

      for (const keyword of expectation.keywords) {
        assert(detail.text.includes(keyword), `${expectation.path} is missing editor keyword ${keyword}`);
      }
    }

    await goto(page, '/certifikaty/');
    const certificates = await page.evaluate(() => ({
      copySource: document.querySelector('.f-certificate-copy')?.getAttribute('data-content-source') || '',
      mediaSource: document.querySelector('.f-certificate-images')?.getAttribute('data-content-source') || '',
      sectionCount: document.querySelectorAll('.f-certificate-copy section').length,
      images: Array.from(document.querySelectorAll('.f-certificate-images img')).map((image) => ({
        source: image.currentSrc || image.src,
        assetStatus: image.getAttribute('data-asset-status') || '',
      })),
    }));

    assert(certificates.copySource === 'certificates-meta', `certificates copy source is ${certificates.copySource}`);
    assert(certificates.mediaSource === 'certificates-media', `certificates media source is ${certificates.mediaSource}`);
    assert(certificates.sectionCount === 2, `certificates page should render 2 admin text sections, got ${certificates.sectionCount}`);
    assert(certificates.images.length === 3, `certificates page should render 3 admin images, got ${certificates.images.length}`);
    assert(certificates.images.every((image) => image.assetStatus === 'admin-certificate'), 'certificate images must come from admin media');
    assert(certificates.images.some((image) => image.source.includes('certificate-tuv-1')), 'certificates page is missing certificate-tuv-1 media');

    await goto(page, '/zaruka/');
    const warranty = await page.evaluate(() => ({
      source: document.querySelector('.f-warranty-cards')?.getAttribute('data-content-source') || '',
      cardCount: document.querySelectorAll('.f-warranty-card').length,
      names: Array.from(document.querySelectorAll('.f-warranty-card__name')).map((heading) => heading.textContent.trim()),
      waitingCards: document.querySelectorAll('.f-warranty-card[data-asset-status="WAITING_ON_OWNER"]').length,
      labels: Array.from(document.querySelectorAll('.f-warranty-labels span')).map((label) => label.textContent.trim()),
      note: document.querySelector('.f-warranty-note')?.textContent.trim().replace(/\s+/g, ' ') || '',
    }));

    assert(warranty.source === 'warranty-meta', `warranty source is ${warranty.source}`);
    assert(warranty.cardCount === 3, `warranty should render 3 admin tiers, got ${warranty.cardCount}`);
    assert(warranty.names.includes('Custom') && warranty.names.includes('Classic') && warranty.names.includes('Core'), 'warranty tiers are missing seeded series names');
    assert(warranty.waitingCards === 3, `warranty should keep 3 owner-waiting media cards, got ${warranty.waitingCards}`);
    assert(warranty.labels.length === 5, `warranty should render 5 row labels, got ${warranty.labels.length}`);
    assert(warranty.note.includes('Dopravn'), 'warranty meta note is missing transport copy');

    await goto(page, '/kolik-stoji-udrzba/');
    const maintenance = await page.evaluate(() => ({
      source: document.querySelector('.f-main--maintenance .f-figma-article')?.getAttribute('data-content-source') || '',
      sectionCount: document.querySelectorAll('.f-main--maintenance .f-figma-article section').length,
      text: document.querySelector('.f-main--maintenance')?.textContent.trim().replace(/\s+/g, ' ') || '',
    }));

    assert(maintenance.source === 'wp-editor', `maintenance article source is ${maintenance.source}`);
    assert(maintenance.sectionCount === 4, `maintenance page should render 4 editor sections, got ${maintenance.sectionCount}`);
    assert(maintenance.text.includes('RossExhaust'), 'maintenance editor content is missing RossExhaust text');

    await goto(page, '/');
    const homepageHero = await page.evaluate(() => {
      const section = document.querySelector('.f-section--slides');
      const adminSlide = document.querySelector('.f-section--slides .f-slide[data-content-source="slide-cpt"]');
      const heroPromo = document.querySelector('.f-hero-promo');
      const heroPromoImage = heroPromo ? heroPromo.querySelector('.f-hero-promo__image') : null;
      const heroPromoButton = heroPromo ? heroPromo.querySelector('.f-hero-promo__button') : null;

      return {
        sectionSource: section ? section.getAttribute('data-content-source') || '' : '',
        slideSource: adminSlide ? adminSlide.getAttribute('data-content-source') || '' : '',
        slideId: adminSlide ? adminSlide.getAttribute('data-slide-id') || '' : '',
        slideText: adminSlide ? adminSlide.textContent.trim().replace(/\s+/g, ' ') : '',
        promoSource: heroPromo ? heroPromo.getAttribute('data-content-source') || '' : '',
        promoOfferId: heroPromo ? heroPromo.getAttribute('data-offer-id') || '' : '',
        promoText: heroPromo ? heroPromo.textContent.trim().replace(/\s+/g, ' ') : '',
        promoHref: heroPromoButton ? heroPromoButton.href : '',
        promoImageStatus: heroPromoImage ? heroPromoImage.getAttribute('data-asset-status') || '' : '',
        promoImageSource: heroPromoImage ? heroPromoImage.getAttribute('data-src') || heroPromoImage.currentSrc || heroPromoImage.src : '',
      };
    });

    assert(homepageHero.sectionSource === 'slide-cpt', `homepage hero section source is ${homepageHero.sectionSource}`);
    assert(homepageHero.slideSource === 'slide-cpt', `homepage hero slide source is ${homepageHero.slideSource}`);
    assert(Number(homepageHero.slideId) > 0, 'homepage hero slide must expose a WP slide id');
    assert(homepageHero.slideText.includes('Kanadsk'), 'homepage hero slide is missing the seeded admin slide copy');
    assert(homepageHero.promoSource === 'offer-cpt', `homepage promo source is ${homepageHero.promoSource}`);
    assert(Number(homepageHero.promoOfferId) > 0, 'homepage promo must expose a WP offer id');
    assert(homepageHero.promoText.includes('Akční nabídky'), 'homepage promo is missing the Akční nabídky offer short title');
    assert(homepageHero.promoHref.includes('/akcni-nabidky/'), `homepage promo should link to the offers archive, got ${homepageHero.promoHref}`);
    assert(homepageHero.promoImageStatus === 'admin-offer-promo', `homepage promo image status is ${homepageHero.promoImageStatus}`);
    assert(homepageHero.promoImageSource.includes('hp-fixed-banner-product'), `homepage promo should render the offer promo image, got ${homepageHero.promoImageSource}`);

    const homepageSections = await page.evaluate(() => {
      const benefitSection = document.querySelector('.f-section--arctic-benefits');
      const showroomSection = document.querySelector('.f-section--showroom');
      const progressSection = document.querySelector('.f-section--progress');
      const showroomButton = showroomSection ? showroomSection.querySelector('.f-showroom-panel__actions a') : null;

      return {
        benefitSource: benefitSection ? benefitSection.getAttribute('data-content-source') || '' : '',
        benefitCardCount: document.querySelectorAll('.f-arctic-benefit').length,
        benefitTexts: Array.from(document.querySelectorAll('.f-arctic-benefit')).map((card) => card.textContent.trim().replace(/\s+/g, ' ')),
        benefitImageSources: Array.from(document.querySelectorAll('.f-arctic-benefit img')).map((image) => image.getAttribute('data-src') || image.currentSrc || image.src),
        showroomSource: showroomSection ? showroomSection.getAttribute('data-content-source') || '' : '',
        showroomImageCount: document.querySelectorAll('.f-showroom-panel__media img').length,
        showroomText: showroomSection ? showroomSection.textContent.trim().replace(/\s+/g, ' ') : '',
        showroomHref: showroomButton ? showroomButton.href : '',
        progressSource: progressSection ? progressSection.getAttribute('data-content-source') || '' : '',
        progressStepCount: document.querySelectorAll('.f-progress-steps li').length,
        progressText: progressSection ? progressSection.textContent.trim().replace(/\s+/g, ' ') : '',
      };
    });

    assert(homepageSections.benefitSource === 'homepage-meta', `homepage benefit source is ${homepageSections.benefitSource}`);
    assert(homepageSections.benefitCardCount === 3, `homepage should render 3 editable benefit cards, got ${homepageSections.benefitCardCount}`);
    assert(homepageSections.benefitTexts.some((text) => text.includes('Mont')), 'homepage benefits are missing the seeded install card');
    assert(homepageSections.benefitTexts.some((text) => text.includes('Servis')), 'homepage benefits are missing the seeded service card');
    assert(homepageSections.benefitImageSources.length === 3, `homepage benefit images should render 3 admin media icons, got ${homepageSections.benefitImageSources.length}`);
    assert(homepageSections.benefitImageSources.every((source) => source.includes('hp-benefit-')), 'homepage benefit icons must come from seeded admin media');
    assert(homepageSections.showroomSource === 'homepage-meta', `homepage showroom source is ${homepageSections.showroomSource}`);
    assert(homepageSections.showroomImageCount === 3, `homepage showroom should render 3 admin media images, got ${homepageSections.showroomImageCount}`);
    assert(homepageSections.showroomText.includes('showroom'), 'homepage showroom is missing seeded admin copy');
    assert(homepageSections.showroomText.includes('280'), 'homepage showroom is missing seeded admin badge');
    assert(homepageSections.showroomHref.includes('/showroom/'), `homepage showroom CTA should link to showroom, got ${homepageSections.showroomHref}`);
    assert(homepageSections.progressSource === 'homepage-meta', `homepage progress source is ${homepageSections.progressSource}`);
    assert(homepageSections.progressStepCount === 6, `homepage progress should render 6 editable steps, got ${homepageSections.progressStepCount}`);
    assert(homepageSections.progressText.includes('01') && homepageSections.progressText.includes('06'), 'homepage progress is missing numbered admin steps');

    const footerGroups = await page.evaluate(() => Array.from(document.querySelectorAll('.f-footer__group')).map((group) => ({
      heading: group.querySelector('h2') ? group.querySelector('h2').textContent.trim() : '',
      source: group.getAttribute('data-content-source') || '',
      links: Array.from(group.querySelectorAll('a')).map((link) => link.textContent.trim()),
    })));

    assert(footerGroups.length === 3, `footer should render 3 WP menu columns, got ${footerGroups.length}`);
    assert(footerGroups.every((group) => group.source === 'wp-menu'), 'footer columns must come from the WP footer menu');
    assert(footerGroups.some((group) => group.heading === 'Vířivky' && !group.links.includes('Skladové vířivky')), 'footer Vířivky menu still contains Skladové vířivky');
    assert(footerGroups.some((group) => group.heading === 'Další informace' && group.links.includes('Akční nabídky') && group.links.includes('Kontakt')), 'footer menu is missing info links from WP menu');

    const footerCopyright = await page.evaluate(() => {
      const element = document.querySelector('.f-footer__copyright');

      return {
        source: element ? element.getAttribute('data-content-source') || '' : '',
        text: element ? element.textContent.trim().replace(/\s+/g, ' ') : '',
      };
    });

    assert(footerCopyright.source === 'customizer-about', `footer copyright source is ${footerCopyright.source}`);
    assert(footerCopyright.text.includes('BASPA s.r.o.'), `footer copyright is missing BASPA s.r.o.: ${footerCopyright.text}`);
    assert(!footerCopyright.text.includes('Arctic Spas CZ'), `footer copyright still contains Arctic Spas CZ: ${footerCopyright.text}`);

    await goto(page, '/showroom/');
    const showroom = await page.evaluate(() => ({
      miniCta: document.querySelector('.f-showroom-mini-cta')?.getAttribute('data-content-source') || '',
      reasons: document.querySelector('.f-showroom-reasons__grid')?.getAttribute('data-content-source') || '',
      primary: document.querySelector('.f-showroom-split--first .f-showroom-split__copy')?.getAttribute('data-content-source') || '',
      secondary: document.querySelector('.f-showroom-split--second .f-showroom-split__copy')?.getAttribute('data-content-source') || '',
      contactText: document.querySelector('.f-showroom-info__item--contact')?.textContent.trim().replace(/\s+/g, ' ') || '',
      reasonCount: document.querySelectorAll('.f-showroom-reason').length,
      heroSource: getComputedStyle(document.querySelector('.f-showroom-hero')).backgroundImage || '',
    }));

    assert(showroom.miniCta === 'showroom-meta', `showroom mini CTA source is ${showroom.miniCta}`);
    assert(showroom.reasons === 'showroom-meta', `showroom reasons source is ${showroom.reasons}`);
    assert(showroom.primary === 'wp-editor', `showroom primary copy source is ${showroom.primary}`);
    assert(showroom.secondary === 'showroom-meta', `showroom secondary copy source is ${showroom.secondary}`);
    assert(showroom.reasonCount === 4, `showroom should render 4 admin reasons, got ${showroom.reasonCount}`);
    assert(showroom.contactText.includes('tomas.koutny@baspa.cz'), 'showroom contact should come from the selected member');
    assert(showroom.heroSource.includes('showroom-covana-interior-web'), `showroom hero should use media-library owner asset, got ${showroom.heroSource}`);
  } finally {
    await browser.close();
  }

  console.log('Admin editability smoke passed.');
})().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
