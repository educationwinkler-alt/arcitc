# PR-A Source Rules and Sign-Off Checklist

Date: 2026-05-29  
Scope: PR-A (`Source rules + audit errata`) from `docs/repair-plan-from-audits-2026-05-29.md`.

## Inputs

- `docs/repair-plan-from-audits-2026-05-29.md`
- `docs/wireframe-content-source-audit-2026-05-29.md`
- `docs/brutal-figma-audit-2026-05-29.md`
- `docs/brutal-figma-audit-2026-05-29-errata.md`
- `docs/web-finalization-master-plan-2026-05-26.md`

## Source-Role Contract (Content vs Visual)

| Source role | Authority | Must not be used for |
|---|---|---|
| `figma-visual` | Final visual treatment: spacing, colors, typography, radii, shadows, image treatment | Business copy truth |
| `figma-wireframe` | UX structure and states: archive/grid, popup, accordion, slider, menu states | Final business copy |
| `old-arctic-content` | Product/business content from `../Arctic-spas/www` | Visual styling decisions |
| `owner-content` | New client-provided business content/assets | Visual styling decisions |
| `baspa-workflow` | WP admin/workflow model and feature behavior parity | Arctic business content or Figma visual parity |

Decision rule:
- If there is a conflict, choose by role, not by convenience.
- Figma copy/lorem mismatch alone is not a bug.
- Missing old Arctic/owner content or placeholder content is a bug.

## Required Finding Types

Every P0/P1 finding must include one type:

| Type | Use when | Example |
|---|---|---|
| `visual` | Figma visual treatment is broken | wrong footer handoff, wrong radius/shadow |
| `wireframe` | Figma structure/state is broken | expected popup/accordion state missing |
| `content-source` | content is missing, placeholder, or from wrong source | truncated old article, invented fallback copy |
| `admin-workflow` | WP/Baspa workflow parity is broken | missing admin control expected by editor workflow |
| `interaction` | element looks interactive but does not behave correctly | `+` icon on card without action |

## PR-A Completion Checklist

- [x] Master plan links to source audit + repair plan + PR-A checklist/errata.
- [x] Master plan marks old frame tracker/sign-off as historical evidence for reopened scope.
- [x] Manual QA checklist explicitly requires source-role decision notes and finding type tags.
- [x] Append-only errata exists for `docs/brutal-figma-audit-2026-05-29.md`.
- [x] Source-role contract is documented in one checklist used for follow-up PRs.

## Sign-Off Entry Template (Mandatory Fields)

| ID | Severity | Type | Source role used | Evidence | Decision |
|---|---|---|---|---|---|
| Example: `CAT-HOT-03` | `P0` | `visual` | `figma-visual` | screenshot path | fix now |
| Example: `REF-01` | `P1` or `INFO` | `content-source` | `old-arctic-content` | old page path + screenshot | accepted divergence |

## Gate for Starting PR-B

PR-B can start only when:

- [x] this checklist exists and is linked from the master plan,
- [x] errata doc exists and is linked from the brutal audit/master plan,
- [x] source-role conflict rule is explicit in docs (not only implicit in discussion).
