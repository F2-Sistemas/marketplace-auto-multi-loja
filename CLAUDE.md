# CLAUDE.md

## Project context

This is a monorepo for a vehicle marketplace platform.

Applications:

- `/apps/api`: Laravel API-only backend.
- `/apps/portal`: Nuxt 4 public marketplace portal.
- `/apps/storefront`: Nuxt 4 public storefront app used by multiple stores.
- `/apps/backoffice`: Nuxt 4 administrative backoffice.

Project priorities:

1. Functional MVP.
2. Security.
3. Performance.
4. Maintainability.
5. Simplicity.
6. Production readiness.

Avoid over-engineering.

---

## Primary instructions

For complete agent instructions, read:

- `/AGENTS.md`

For app-specific instructions, read the nearest app-level `AGENTS.md` before changing files inside an app.

---

## Portal UI rule

Before changing any UI in `/apps/portal`, read:

- `/apps/portal/AGENTS.md`
- `/docs/design/portal/design.json`
- `/docs/design/portal/design.md`

These files are the source of truth for the public marketplace portal UI, UX, tokens, components, states, and visual acceptance criteria.

If current code diverges from those files, adjust the code to follow the design system unless doing so would break existing functionality.

---

## Front-end conventions

Use:

- Nuxt 4.
- Vue 3 Composition API.
- TypeScript.
- Tailwind CSS v4.
- Small components.
- Domain composables.
- i18n for visible text.
- Mobile-first responsive UI.

Avoid:

- hardcoded visible strings;
- large components with mixed responsibilities;
- critical business logic in the front-end;
- unnecessary API contract changes.

---

## Portal visual direction

The portal UI must be:

- modern;
- clean;
- clear;
- commercial;
- responsive;
- light mode;
- marketplace-oriented;
- not Bootstrap-like;
- not backoffice-like.

Use:

- Inter;
- white cards;
- subtle borders;
- discreet shadows;
- light gray page background;
- small or medium buttons;
- slightly rounded buttons;
- subtle input focus states;
- fast transitions between 150ms and 220ms.

Avoid:

- `rounded-full` as the default;
- thick focus rings;
- strong default blue browser focus;
- polluted cards;
- oversized buttons;
- hardcoded strings.

---

## Validation checklist for portal UI

Before finishing UI work in `/apps/portal`, verify:

- `design.json` was followed;
- `design.md` was followed;
- i18n was used;
- the UI is responsive;
- loading, empty, and error states exist where applicable;
- the UI does not look like Bootstrap;
- the UI does not look like an admin panel;
- focus states are subtle;
- API contracts were not changed unnecessarily;
- lint/typecheck/build were run when available.
