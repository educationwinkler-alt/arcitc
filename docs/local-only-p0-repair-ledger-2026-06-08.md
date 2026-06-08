# Local-only P0 repair ledger - 2026-06-08

## Rule

From this point, P0 repair work is local-only until explicit owner approval.

- Local target: `http://localhost:8090`
- Production target: no FTP upload, no production DB write, no production data script
- Git requirement: every P0 repair commit must state whether it is deployed to production
- Current production status for new P0 repairs: `not deployed to production`

## Workflow

1. Implement against local WordPress only.
2. Run the focused local smoke/audit for the touched area.
3. Commit and push the repair with a clear `local-only` or `not deployed to production` note.
4. Update this ledger with the commit reference, local evidence, and production status.
5. Deploy to production only after owner approval.

## Repair entries

| Date | Block | Commit | Local evidence | Production status |
| --- | --- | --- | --- | --- |
| 2026-06-08 | P0 local-only workflow gate | this commit | Ledger created before continuing P0 repairs | Not deployed to production |
| 2026-06-08 | P0 category hero CTA visibility for `/virivky/` and `/swimspa/` | this commit | `node tools/category-hero-cta-smoke.js`, `node tools/editable-text-overflow-smoke.js`, `node tools/product-media-smoke.js` passed locally. `npm run figma:audit` currently fails on separate homepage hero caption height baseline: `desktop.heroCaption.height expected 309 got 392`. | Not deployed to production |

## Open local blockers

| Date | Blocker | Evidence | Production status |
| --- | --- | --- | --- |
| 2026-06-08 | Homepage hero caption height drift | `npm run figma:audit` fails locally: `desktop.heroCaption.height expected 309 got 392` | Not deployed to production |
