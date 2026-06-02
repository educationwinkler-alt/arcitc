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

async function main() {
  const adminMembers = await fetchAdminMembers();
  const usesFigmaFallbackTeam = adminMembers.length === 0;

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
    required: usesFigmaFallbackTeam ? [
      'uploads/import/figma/about-team-vladimir.png',
      'uploads/import/figma/about-team-lukas.png',
      'uploads/import/figma/about-team-helena.png',
      'uploads/import/figma/about-team-alena.png',
      'Vlastimil Zhoř',
      'Ing. Lukáš Dušek',
      'Alena Janulíková',
    ] : [],
    forbidden: [
      ...(usesFigmaFallbackTeam ? ['f-about-person__media--waiting'] : []),
      'Vladimír Zajíč',
      'Servisní tým',
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
      'uploads/import/figma/about-team-alena.png',
      'uploads/import/figma/about-team-service.png',
    ],
  });

  await assertAssetContract('/product/timberwolf/', {
    required: [
      'acrylic-dakota',
      'acrylic-kalahari',
      'acrylic-odyssey',
      'acrylic-espresso',
      'uploads/import/figma/color-platinum-swirl.png',
      'uploads/import/figma/cabinet-cedar.png',
      'uploads/import/figma/cabinet-maintenance-free.png',
    ],
    forbidden: [
      'uploads/import/figma/detail-timberwolf-hero.jpg',
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
