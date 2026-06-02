# Section Nav Figma Audit - 2026-06-01

Scope: secondary/podmenu bars used on product detail, `O nas`, and `Podpora`.

## Figma Contract

Sources:

- Product detail: `DETAIL KONKRETNIHO PRODUKTU` node `1:1461`, nav frame `1:1773`.
- O nas: `O NAS` node `1:945`, nav frame `1:967`.
- Podpora: `PODPORA` node `1:752`, nav frame `1:773`.

Shared Figma values:

- Container: `1400 x 93`, x `260`, white fill, radius `40`.
- Container padding: left/right `53`, top `22`.
- Link row: height `71`, gap `42`.
- Link text: `18px`, `700`, line-height `51`.
- Active/hover: red text plus a red bottom line exactly under the word, not a padded pill.

## Local Differences Found

- Product detail nav had anchor margin `-30px -20px`; first text started at `x=293` instead of Figma `x=313`.
- Product detail active underline used `left/right: 16px`, `bottom: -23px`, `height: 2px`, so it did not match the text width or Figma line.
- O nas nav inherited centered alignment and list item padding; link was shifted to `x=333`, `y=473` instead of Figma `x=313`, `y=463`.
- O nas and Podpora containers used `24px`/pill radius instead of Figma `40px`.
- O nas and Podpora were missing the hover underline behavior; only the text color changed.
- Support tabs had the right container position but kept the old no-line hover treatment.

## Fix Implemented

- Unified product, O nas, and support secondary nav CSS in `wp-content/themes/arctic/src/less/_components.less`.
- Reset secondary nav `li` and anchor margin/padding to zero.
- Set desktop container height/radius/padding to the Figma values.
- Added shared active/hover underline using `::after`, bottom `-20px`, height `1px`, width inherited from the link text.
- Kept the existing sticky handoff class `.js-section-nav-handoff`.

## Guard

Added `npm run section-nav:smoke`.

The smoke validates:

- Product, O nas, and Podpora container geometry.
- First link x/y, typography, margin and padding.
- Active underline width equals the rendered word width.
- Hover changes text to Figma red and shows the underline.

It is now included in `npm run qa:local`.
