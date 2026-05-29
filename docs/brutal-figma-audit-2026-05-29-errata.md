# Errata - Brutal Figma Audit (2026-05-29)

Date: 2026-05-29  
Type: append-only errata for `docs/brutal-figma-audit-2026-05-29.md`.

## Why this errata exists

The original audit is still valid for many visual findings.  
This errata corrects findings that were judged mainly against Figma copy, placeholder text, or uncertain Figma layer state without source-role classification.

Source-role authority for corrections:
- `figma-visual` and `figma-wireframe` for layout/state
- `old-arctic-content` and `owner-content` for business copy/media truth
- `baspa-workflow` for admin behavior

## Corrected findings

| Audit ID | Original classification | Corrected classification | Correction |
|---|---|---|---|
| `FEATD-02` | `P0` framed as "Figma has longer body copy" | `content-source` check | Keep `P0` only if content is missing against old Arctic (`izolace-virivky.php`) or owner input. Figma copy length alone is not a failure. |
| `MAINT-01` | `P0` based on frame height/length delta vs Figma | `content-source` check | Keep `P0` only if old Arctic maintenance article content is missing/truncated. Do not use Figma page height as sole proof. |
| `MAINT-02` | `P0` based on article structure mismatch vs Figma | `content-source` + `wireframe` split | `content-source` if old article sections are missing. `wireframe` only for structural UX break, not for literal Figma copy differences. |
| `REF-01` | `P1` "real content differs from Figma placeholder" | `INFO` unless another rule is broken | Real Arctic references can differ from Figma placeholder text. Raise issue only for visual treatment or wrong content source. |
| `SERV-01` | `P1` because card photos are not Figma-identical | `INFO` unless visual treatment breaks | Real service photos are allowed; fail only when crop/treatment contract is broken or asset source is wrong. |
| `MOB-MENU-01` | `P0` with unresolved Figma layer-state ambiguity | `wireframe` investigation first | Do not keep as implementation `P0` until the authoritative Figma menu state/layer visibility is confirmed. |
| `MOB-MENU-02` | `P1` conditional on uncertain export state | `wireframe` investigation first | Same rule: classify as unresolved wireframe-state check until the Figma state is confirmed. |
| `CAT-SWIM-04` (text part) | `P0` due to wording concern inside visual finding | split into `visual` + `content-source` | Visual banner composition can stay `P0` (`visual`). Wording must be validated against old Arctic/owner swimspa wording (`content-source`). |

## Net effect on repair planning

- Visual and shared-component P0 findings remain actionable.
- Copy/content findings must be validated against old Arctic and owner inputs first.
- Any future finding must include explicit type: `visual`, `wireframe`, `content-source`, `admin-workflow`, or `interaction`.
