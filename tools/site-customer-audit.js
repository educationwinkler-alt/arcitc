const fs = require('fs');
const path = require('path');
const { chromium } = require('playwright-core');

const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const outputDir = path.join('docs', 'screenshots', 'site-customer-audit-2026-06-08');

const targets = [
  { id: 'local', baseUrl: process.env.ARCTIC_LOCAL_BASE_URL || 'http://localhost:8090' },
  { id: 'prod', baseUrl: process.env.ARCTIC_PROD_BASE_URL || 'https://illuminatus.cz' },
];

const pages = [
  { id: 'maintenance', path: '/kolik-stoji-udrzba/' },
  { id: 'support', path: '/podpora/' },
  { id: 'references', path: '/reference/' },
  { id: 'about', path: '/o-nas/' },
  { id: 'showroom', path: '/showroom/' },
  { id: 'contact', path: '/kontakt/' },
];

function mkdirp(dir) {
  fs.mkdirSync(dir, { recursive: true });
}

function summarize(results) {
  const summary = {};

  for (const target of targets) {
    const byPage = Object.fromEntries(results.filter((item) => item.target === target.id).map((item) => [item.pageId, item]));
    const refs = byPage.references || {};
    const showroom = byPage.showroom || {};
    const contact = byPage.contact || {};
    const support = byPage.support || {};
    const maintenance = byPage.maintenance || {};
    const about = byPage.about || {};

    summary[target.id] = {
      okPages: Object.values(byPage).filter((item) => item.status && item.status < 400).length,
      menuHasInquiryForm: Boolean(byPage.support?.headerMenu?.infoLinks?.some((link) => /popt|konfigur|formular/i.test(link.text + ' ' + link.href))),
      menuHasMaintenancePage: Boolean(byPage.support?.headerMenu?.infoLinks?.some((link) => /kolik stoj/i.test(link.text))),
      supportFaqCount: support.support?.faqCount || 0,
      supportHasMaintenanceCostFaq: Boolean(support.support?.faqTitles?.some((title) => /kolik|provoz|údrž|udrz|náklad|naklad/i.test(title))),
      maintenanceIsStandalonePage: maintenance.status && maintenance.status < 400,
      referenceCards: refs.references?.cardCount || 0,
      referenceImageOnlyLinks: refs.references?.imageOnlyLinkCount || 0,
      referenceCardsWithDescription: refs.references?.cardsWithDescriptionCount || 0,
      aboutContainsOldYearSignals: about.about?.contains2005 || about.about?.contains15Years || false,
      showroomHasEmbeddedMapSection: Boolean(showroom.showroom?.mapSectionExists),
      showroomGalleryButtonHref: showroom.showroom?.galleryButtonHref || '',
      showroomGalleryTargetExists: Boolean(showroom.showroom?.galleryButtonTargetExists),
      showroomGalleryCards: showroom.showroom?.galleryCardCount || 0,
      contactMapSource: contact.contact?.mapContentSource || '',
      contactMapIframe: contact.contact?.mapIframeSrc || '',
      contactMapHasDarkOverlay: Boolean(contact.contact?.mapOverlayBackground && contact.contact.mapOverlayBackground !== 'none'),
    };
  }

  return summary;
}

async function auditPage(page, target, pageDef) {
  const url = new URL(pageDef.path, target.baseUrl).toString();
  const response = await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 35000 });
  await page.waitForTimeout(900);

  const screenshot = path.join(outputDir, `${target.id}-${pageDef.id}-desktop.png`);
  await page.screenshot({ path: screenshot, fullPage: false });

  const data = await page.evaluate((pageId) => {
    const text = (element) => element ? element.textContent.replace(/\s+/g, ' ').trim() : '';
    const absolutize = (value) => {
      if (!value) {
        return '';
      }
      try {
        return new URL(value, window.location.href).toString();
      } catch (error) {
        return value;
      }
    };
    const rect = (element) => {
      if (!element) {
        return null;
      }
      const box = element.getBoundingClientRect();
      return {
        x: Math.round(box.x * 100) / 100,
        y: Math.round(box.y * 100) / 100,
        width: Math.round(box.width * 100) / 100,
        height: Math.round(box.height * 100) / 100,
        top: Math.round(box.top * 100) / 100,
        left: Math.round(box.left * 100) / 100,
        bottom: Math.round(box.bottom * 100) / 100,
        right: Math.round(box.right * 100) / 100,
      };
    };
    const styles = (element, pseudo = null) => {
      if (!element) {
        return null;
      }
      const computed = window.getComputedStyle(element, pseudo);
      return {
        display: computed.display,
        visibility: computed.visibility,
        opacity: computed.opacity,
        filter: computed.filter,
        background: computed.background,
        backgroundColor: computed.backgroundColor,
        pointerEvents: computed.pointerEvents,
      };
    };

    const headerLinks = Array.from(document.querySelectorAll('.f-header a, header a')).map((link) => ({
      text: text(link),
      href: absolutize(link.getAttribute('href') || ''),
      classes: typeof link.className === 'string' ? link.className : '',
    })).filter((link) => link.text || link.href);

    const infoStart = headerLinks.findIndex((link) => /další informace|dalsi informace/i.test(link.text));
    const infoLinks = infoStart >= 0
      ? headerLinks.slice(infoStart, Math.min(headerLinks.length, infoStart + 18))
      : headerLinks.filter((link) => /služby|sluzby|podpora|reference|o nás|o nas|showroom|servis|kontakt|kolik stoj/i.test(link.text));

    const common = {
      finalUrl: window.location.href,
      title: document.title,
      bodyClass: document.body.className,
      h1: text(document.querySelector('h1')),
      textSample: text(document.body).slice(0, 1500),
      headerMenu: {
        linkCount: headerLinks.length,
        infoLinks,
      },
    };

    if (pageId === 'maintenance') {
      return {
        ...common,
        maintenance: {
          articleText: text(document.querySelector('.f-figma-article')),
          headingCount: document.querySelectorAll('.f-figma-article h2, .f-figma-article h3').length,
          isSupportPage: document.body.className.includes('template-support'),
        },
      };
    }

    if (pageId === 'support') {
      const faqCards = Array.from(document.querySelectorAll('.f-support-faq-card'));
      return {
        ...common,
        support: {
          faqCount: faqCards.length,
          faqTitles: faqCards.map((card) => text(card.querySelector('h3'))),
          faqTags: faqCards.map((card) => text(card.querySelector('.f-support-faq-card__tag'))),
          tabLinks: Array.from(document.querySelectorAll('.f-support-tabs a')).map((link) => ({
            text: text(link),
            href: link.getAttribute('href') || '',
            targetExists: Boolean(document.querySelector(link.getAttribute('href') || '')),
          })),
        },
      };
    }

    if (pageId === 'references') {
      const cards = Array.from(document.querySelectorAll('.f-reference-card'));
      const cardData = cards.map((card) => {
        const href = card.tagName === 'A' ? card.getAttribute('href') || '' : '';
        const absoluteHref = absolutize(href);
        const image = card.querySelector('img');
        const strong = text(card.querySelector('strong'));
        const bodyText = text(card);
        const description = bodyText
          .replace(strong, '')
          .replace(/\b(20[0-9]{2}|Připravujeme|Brzy)\b/g, '')
          .trim();

        return {
          tag: card.tagName,
          href: absoluteHref,
          isImageHref: /\.(jpe?g|png|webp|gif|avif)(\?|$)/i.test(absoluteHref),
          isLightbox: card.classList.contains('f-reference-card--lightbox'),
          title: strong,
          text: bodyText,
          description,
          hasUsefulDescription: description.length > 40,
          imageSrc: image ? absolutize(image.currentSrc || image.src || '') : '',
        };
      });

      return {
        ...common,
        references: {
          cardCount: cardData.length,
          imageOnlyLinkCount: cardData.filter((card) => card.isImageHref || card.isLightbox).length,
          cardsWithDescriptionCount: cardData.filter((card) => card.hasUsefulDescription).length,
          cards: cardData.slice(0, 12),
        },
      };
    }

    if (pageId === 'about') {
      const bodyText = text(document.body);
      return {
        ...common,
        about: {
          introText: text(document.querySelector('.f-about-intro, .f-figma-article, main')),
          contains2005: bodyText.includes('2005'),
          contains15Years: /15\s+let/i.test(bodyText),
          stats: Array.from(document.querySelectorAll('.f-about-stat, .f-stat, [class*="stat"]')).map((item) => text(item)).filter(Boolean),
          teamNames: Array.from(document.querySelectorAll('.f-about-team-card h3, .f-member-card h3, [class*="team"] h3')).map((item) => text(item)).filter(Boolean),
        },
      };
    }

    if (pageId === 'showroom') {
      const galleryButton = document.querySelector('.f-showroom-gallery-button');
      const galleryHref = galleryButton ? galleryButton.getAttribute('href') || '' : '';
      const galleryTarget = galleryHref.startsWith('#') ? document.querySelector(galleryHref) : null;
      const gallerySection = document.querySelector('#fotogalerie');

      return {
        ...common,
        showroom: {
          mapSectionExists: Boolean(document.querySelector('.f-section--map, .f-local-map, iframe[src*="maps"]')),
          mapLinks: Array.from(document.querySelectorAll('.f-showroom-info__item a[href*="maps"], .f-showroom-info__item a[target="_blank"]')).map((link) => ({
            text: text(link),
            href: absolutize(link.getAttribute('href') || ''),
          })),
          galleryButtonHref: galleryHref,
          galleryButtonText: text(galleryButton),
          galleryButtonTargetExists: Boolean(galleryTarget),
          gallerySectionText: text(gallerySection),
          galleryImageCount: gallerySection ? gallerySection.querySelectorAll('img').length : 0,
          galleryCardCount: document.querySelectorAll('#fotogalerie .f-gallery-card, #fotogalerie .js-image, #fotogalerie a[href$=".jpg"], #fotogalerie a[href$=".png"]').length,
          infoItems: Array.from(document.querySelectorAll('.f-showroom-info__item')).map((item) => text(item)),
        },
      };
    }

    if (pageId === 'contact') {
      const map = document.querySelector('.f-local-map');
      const iframe = document.querySelector('.f-local-map__iframe');
      const fallbackImage = document.querySelector('.f-local-map__image');
      const overlay = document.querySelector('.f-local-map::before') ? null : map;

      return {
        ...common,
        contact: {
          mapExists: Boolean(map),
          mapContentSource: map ? map.getAttribute('data-content-source') || '' : '',
          mapRect: rect(map),
          mapStyle: styles(map),
          mapBeforeStyle: styles(map, '::before'),
          mapOverlayBackground: styles(map, '::before')?.background || '',
          mapIframeSrc: iframe ? absolutize(iframe.getAttribute('src') || '') : '',
          mapIframeStyle: styles(iframe),
          fallbackImageSrc: fallbackImage ? absolutize(fallbackImage.currentSrc || fallbackImage.src || '') : '',
          cardText: text(document.querySelector('.f-local-map__card')),
          mapButtonHref: absolutize(document.querySelector('.f-local-map__card a')?.getAttribute('href') || ''),
          contactCardCount: document.querySelectorAll('.f-contact-card').length,
          overlayStyle: styles(overlay),
        },
      };
    }

    return common;
  }, pageDef.id);

  return {
    target: target.id,
    pageId: pageDef.id,
    url,
    status: response ? response.status() : null,
    screenshot,
    ...data,
  };
}

async function main() {
  mkdirp(outputDir);

  const browser = await chromium.launch({ executablePath: chromePath, headless: true });
  const results = [];

  try {
    for (const target of targets) {
      const context = await browser.newContext({
        viewport: { width: 1440, height: 1000 },
        deviceScaleFactor: 1,
      });
      const page = await context.newPage();

      for (const pageDef of pages) {
        try {
          results.push(await auditPage(page, target, pageDef));
        } catch (error) {
          results.push({
            target: target.id,
            pageId: pageDef.id,
            url: new URL(pageDef.path, target.baseUrl).toString(),
            error: error && error.message ? error.message : String(error),
          });
        }
      }

      await context.close();
    }
  } finally {
    await browser.close();
  }

  const report = {
    generatedAt: new Date().toISOString(),
    pages,
    results,
    summary: summarize(results),
  };

  const outputPath = path.join(outputDir, 'audit.json');
  fs.writeFileSync(outputPath, `${JSON.stringify(report, null, 2)}\n`, 'utf8');

  console.log(`Wrote ${outputPath}`);
  console.log(JSON.stringify(report.summary, null, 2));
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
