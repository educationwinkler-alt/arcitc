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
      'showroom-main-web',
      'showroom-detail-web',
      'showroom-covana-interior-web',
      'data-content-source="wp-editor"',
      'data-content-source="showroom-meta"',
    ],
    forbidden: [
      'uploads/import/figma/showroom-hero-bazeny.jpg',
      'uploads/import/figma/showroom-detail-bazeny.png',
      'uploads/import/figma/showroom-detail-virivky.png',
    ],
  });

  await assertAssetContract('/', {
    required: [
      'data-content-source="homepage-meta"',
      'data-content-source="offer-cpt"',
      'data-asset-status="admin-offer-promo"',
      'hp-fixed-banner-product',
      'showroom-main-web',
      'showroom-detail-web',
      'showroom-covana-interior-web',
    ],
    forbidden: [
      'uploads/import/figma/showroom-1.png',
      'uploads/import/figma/showroom-2.png',
      'uploads/import/figma/showroom-3.png',
    ],
  });

  await assertAssetContract('/o-nas/', {
    required: [
      'data-content-source="wp-editor"',
      'data-content-source="about-meta"',
      ...(usesFigmaFallbackTeam ? [
      'uploads/import/figma/about-team-vladimir-portrait.png',
      'uploads/import/figma/about-team-lukas-portrait.png',
      'uploads/import/figma/about-team-helena-portrait.png',
      'uploads/import/figma/about-team-alena-portrait.png',
      'Vlastimil Zhoř',
      'Ing. Lukáš Dušek',
      'Alena Janulíková',
      ] : []),
    ],
    forbidden: [
      ...(usesFigmaFallbackTeam ? ['f-about-person__media--waiting'] : []),
      'uploads/import/figma/about-team-vladimir.png',
      'uploads/import/figma/about-team-lukas.png',
      'uploads/import/figma/about-team-helena.png',
      'uploads/import/figma/about-team-alena.png',
      'uploads/import/figma/about-team-tomas.png',
      'Vladimír Zajíč',
      'Servisní tým',
    ],
  });

  await assertAssetContract('/kontakt/', {
    required: adminMembers.length > 0 ? [
      'data-content-source="admin-member"',
      'data-member-id="',
    ] : [],
    forbidden: [
      'data-content-source="figma-contact-frame"',
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
      'color-platinum-swirl',
      'cabinet-cedar',
      'cabinet-maintenance-free',
      'data-content-source="spa_color"',
      'data-asset-status="admin-product-color"',
    ],
    forbidden: [
      'uploads/import/figma/detail-timberwolf-hero.jpg',
    ],
  });

  await assertAssetContract('/sluzby/', {
    required: [
      'data-content-source="service-cpt"',
      'data-asset-status="admin-service"',
      'service-consultation',
      'service-meeting',
      'service-catalog',
      'service-showroom',
      'service-delivery',
      'service-support',
    ],
  });

  await assertAssetContract('/certifikaty/', {
    required: [
      'data-content-source="certificates-meta"',
      'data-content-source="certificates-media"',
      'data-asset-status="admin-certificate"',
      'certificate-tuv-1',
      'certificate-tuv-2',
      'certificate-tuv-3',
    ],
  });

  await assertAssetContract('/kolik-stoji-udrzba/', {
    required: [
      'data-content-source="wp-editor"',
      'RossExhaust',
    ],
  });

  await assertAssetContract('/vlastnosti/', {
    required: [
      'data-content-source="feature-cpt"',
      'data-asset-status="admin-feature"',
      '/vlastnosti/zaruka-na-skorepinu/',
      '/vlastnosti/termokryt/',
      '/vlastnosti/podlaha-virivky/',
      '/vlastnosti/servisni-pristup/',
      '/vlastnosti/variabilita/',
      '/vlastnosti/automaticka-dezinfekce/',
    ],
    forbidden: [
      '/vlastnosti/#termokryt',
      '/vlastnosti/#podlaha',
      '/vlastnosti/#variabilita',
      '/vlastnosti/#automaticka-dezinfekce',
      '/podpora/#servisni-formular',
    ],
  });

  await assertAssetContract('/vlastnosti/izolace-virivky/', {
    required: [
      'data-content-source="wp-editor"',
      'data-content-source="feature-cpt"',
      'data-asset-status="admin-feature-detail"',
      'data-asset-status="admin-feature"',
      'feature-izolace-freeheat',
      'feature-freeheat-diagram',
      'RossExhaust',
      'Mylovac',
    ],
  });

  for (const featureDetailPath of [
    '/vlastnosti/zaruka-na-skorepinu/',
    '/vlastnosti/termokryt/',
    '/vlastnosti/podlaha-virivky/',
    '/vlastnosti/servisni-pristup/',
    '/vlastnosti/variabilita/',
    '/vlastnosti/automaticka-dezinfekce/',
  ]) {
    await assertAssetContract(featureDetailPath, {
      required: [
        'data-content-source="wp-editor"',
        'data-content-source="feature-cpt"',
        'data-asset-status="admin-feature-detail"',
        'data-asset-status="admin-feature"',
      ],
    });
  }

  await assertAssetContract('/zaruka/', {
    required: [
      'data-content-source="warranty-meta"',
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
