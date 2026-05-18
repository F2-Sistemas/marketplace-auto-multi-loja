# AGENTS.md

## Project

This repository is a monorepo for a vehicle marketplace platform.

Applications:

- `/apps/api`: Laravel API-only backend.
- `/apps/portal`: Nuxt 4 public marketplace portal.
- `/apps/storefront`: Nuxt 4 public storefront app used by multiple stores.
- `/apps/backoffice`: Nuxt 4 administrative backoffice.
- `/packages/ui`: optional shared UI package.
- `/packages/types`: optional shared contracts and TypeScript types.

The project priority is a production-ready MVP with clean architecture, security, performance, and maintainability.

Avoid over-engineering.

---

## Instruction hierarchy

When working in this repository, follow this order:

1. The explicit user task.
2. The nearest app-level `AGENTS.md`.
3. The relevant design system files under `/docs/design`.
4. This root `AGENTS.md`.
5. Existing code conventions.

If existing code conflicts with the relevant design system, prefer the design system unless that would break functionality.

If a technical conflict may break existing behavior, preserve behavior and report the conflict.

---

## General rules

- Do not introduce microservices for the MVP.
- Do not create one deployment per store.
- Do not create one database or schema per store for the MVP.
- Do not spread critical business rules across the front-end.
- Do not change API contracts unless the task explicitly requires it.
- Prefer the simplest professional solution that is secure, performant, and maintainable.
- Preserve the monorepo structure.
- Inspect existing files before making broad changes.
- Do not rewrite unrelated areas.
- Keep changes scoped to the requested task.
- Prefer explicit, readable code over clever abstractions.

---

## Front-end rules

The front-end applications use:

- Nuxt 4.
- Vue 3 Composition API.
- TypeScript.
- Tailwind CSS v4.
- i18n with `pt-BR` as the default locale.

When changing front-end code:

- Use small components.
- Use domain-oriented composables when useful.
- Keep UI mobile-first and responsive.
- Avoid large components with mixed responsibilities.
- Avoid critical business logic in the front-end.
- Use translation files for visible text.
- Do not hardcode user-facing strings in components.
- Keep API integration isolated in composables/services where possible.

---

## Design systems

Each public or administrative app has its own design system context:

- Portal marketplace: `/docs/design/portal/design.json` and `/docs/design/portal/design.md`.
- Storefront app: `/docs/design/storefront/design.json` and `/docs/design/storefront/design.md`.
- Backoffice app: `/docs/design/backoffice/design.json` and `/docs/design/backoffice/design.md`.

When working inside an app, always read the app-level `AGENTS.md` and the relevant design system files before changing UI or UX.

---

## Laravel/API rules

The API uses Laravel API-only.

Rules:

- Keep controllers thin.
- Use Form Requests for validation.
- Use API Resources for responses.
- Use Policies for authorization.
- Use Services or Actions when they improve clarity.
- Use Jobs/Queues for heavy work.
- Use Enums for statuses and fixed types when useful.
- Write structured migrations with proper indexes.
- Use factories with `fake()`, not `$this->faker`.
- Do not suppress PHP errors with `@`.
- Avoid unindexed heavy queries.
- Enforce tenant/store isolation through `store_id` where applicable.
- Never allow cross-store data access without explicit authorization.

---

## Testing and validation

When changing code, run the relevant commands if available:

- lint;
- typecheck;
- tests;
- build.

If a command fails because of an existing issue or missing local dependency, report it clearly.

Do not claim validation was successful unless it was actually run successfully.

---

## Final checklist

Before finishing a task, verify:

- the change is scoped;
- no unrelated files were rewritten;
- relevant design system rules were followed;
- visible front-end text uses i18n;
- loading, empty, error, and disabled states exist when applicable;
- API contracts were preserved unless explicitly changed;
- relevant validation commands were run or clearly reported as unavailable.
