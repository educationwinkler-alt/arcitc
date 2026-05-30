const baseUrl = process.env.ARCTIC_BASE_URL || 'http://localhost:8090';

async function fetchHtml(path) {
  const response = await fetch(`${baseUrl}${path}`);

  if (!response.ok) {
    throw new Error(`${path} returned ${response.status}`);
  }

  return response.text();
}

function assertIncludes(path, html, needles) {
  for (const needle of needles) {
    if (!html.includes(needle)) {
      throw new Error(`${path} is missing expected asset marker: ${needle}`);
    }
  }
}

function assertExcludes(path, html, needles) {
  for (const needle of needles) {
    if (html.includes(needle)) {
      throw new Error(`${path} contains forbidden design-only asset marker: ${needle}`);
    }
  }
}

async function assertAssetContract(path, { required = [], forbidden = [] }) {
  const html = await fetchHtml(path);

  assertIncludes(path, html, required);
  assertExcludes(path, html, forbidden);
}

async function main() {
  await assertAssetContract('/showroom/', {
    required: [
      'uploads/import/owner-showroom/showroom-main-web.jpg',
      'uploads/import/owner-showroom/showroom-detail-web.jpg',
      'uploads/import/owner-showroom/showroom-covana-interior-web.jpg',
    ],
    forbidden: [
      'uploads/import/figma/showroom-hero-bazeny.jpg',
      'uploads/import/figma/showroom-detail-bazeny.png',
      'uploads/import/figma/showroom-detail-virivky.png',
    ],
  });

  await assertAssetContract('/', {
    required: [
      'uploads/import/owner-showroom/showroom-main-web.jpg',
      'uploads/import/owner-showroom/showroom-detail-web.jpg',
      'uploads/import/owner-showroom/showroom-covana-interior-web.jpg',
    ],
    forbidden: [
      'uploads/import/figma/showroom-1.png',
      'uploads/import/figma/showroom-2.png',
      'uploads/import/figma/showroom-3.png',
    ],
  });

  await assertAssetContract('/o-nas/', {
    required: [
      'data-asset-status="WAITING_ON_OWNER"',
      'f-about-person__media--waiting',
    ],
    forbidden: [
      'uploads/import/figma/about-team-vladimir.png',
      'uploads/import/figma/about-team-lukas.png',
      'uploads/import/figma/about-team-helena.png',
      'uploads/import/figma/about-team-service.png',
    ],
  });

  await assertAssetContract('/kontakt/', {
    required: [
      'data-content-source="figma-contact-frame"',
      'data-asset-status="WAITING_ON_OWNER"',
      'f-contact-card__avatar--waiting',
    ],
    forbidden: [
      'uploads/import/figma/about-team-vladimir.png',
      'uploads/import/figma/about-team-lukas.png',
      'uploads/import/figma/about-team-helena.png',
      'uploads/import/figma/about-team-service.png',
    ],
  });

  await assertAssetContract('/product/timberwolf/', {
    required: [
      'acrylic-dakota',
      'acrylic-kalahari',
      'acrylic-odyssey',
      'acrylic-espresso',
    ],
    forbidden: [
      'uploads/import/figma/color-',
      'uploads/import/figma/cabinet-',
    ],
  });

  await assertAssetContract('/sluzby/', {
    required: [
      'uploads/import/legacy-services/',
    ],
  });

  await assertAssetContract('/zaruka/', {
    required: [
      'data-asset-status="WAITING_ON_OWNER"',
      'f-warranty-card__media--waiting',
    ],
    forbidden: [
      'uploads/import/figma/category-zaruka.jpg',
    ],
  });

  console.log('Asset source smoke passed.');
}

main().catch((error) => {
  console.error(error.message);
  process.exit(1);
});
