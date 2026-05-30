# Support/download/mobile contract - PR-G - 2026-05-30

## Goal

Support, downloads, and mobile shell behavior must be guarded as reusable contracts, not patched as isolated page fixes. PR-G closes the remaining Wave 7/8 scope by making the existing support/download interactions explicit, adding shared contract markers, and validating mobile homepage/menu stability.

## Contracts

| Area | Contract | Source rule | Guard |
| --- | --- | --- | --- |
| Support shell | `.f-main--support-contract` | support/download pages share one support surface contract | `npm run support-mobile:smoke` checks `/podpora/` and `/ke-stazeni/` |
| Support tabs/chips | `.f-support-tabs--contract`, `.f-chip-list--contract` | chips are real tab controls when they filter content | smoke checks `role="tab"`, active state, and no mobile overflow |
| FAQ accordion | `.f-support-faq-card--contract`, `data-support-faq-card` | plus/minus affordance must toggle ARIA and panel state | smoke clicks FAQ rows on `/podpora/` |
| Downloads accordion/cards | `.f-downloads--contract`, `.f-download-group--contract`, `.f-download-card--contract` | group toggle, thumbnail, metadata, and CTA layout stay together | smoke clicks groups, filters chips, and checks CTA attachment |
| Support service form | `.f-support-form--contract`, `.f-support-form__card--contract` | support form is a rounded card with bounded inputs and a deterministic contact fallback action | smoke checks the form contract marker |
| Mobile homepage/menu shell | `.f-off--navigation` plus homepage section boundaries | mobile homepage/menu must not create horizontal overflow or expose desktop submenu content | smoke opens the mobile menu and checks hero/category/showroom/CTA/footer containment |

## Implementation Notes

- `support-download-interactions.js` remains globally available, and PR-G smoke validates that it is present on both support/download pages.
- `_component-contracts.less` owns no-overflow and containment guardrails for support/download/mobile. Existing pixel-specific desktop composition stays in `_components.less` until the broader page-specific cleanup removes it safely.
- The contract classes are intentionally additive. They do not replace existing Figma audit selectors, so the strict desktop visual audit can continue to validate current coordinates.

## Non-goals

- Do not rework old Arctic FAQ/PDF content in PR-G unless source data is missing or broken.
- Do not add invented PDF thumbnails or support photos.
- Do not create separate support/download component copies for `/podpora/` and `/ke-stazeni/`.
- Do not change mobile homepage Figma geometry without screenshot sign-off.

## Guard

Run:

```bash
npm run support-mobile:smoke
```

This guard is also included in:

```bash
npm run qa:local
```
