Sim. Eu usaria um prompt em **duas fases**: primeiro o agente inspeciona e planeja; depois executa a refatoração. Isso reduz risco de ele sair reescrevendo tudo sem entender a estrutura atual.

A ideia está alinhada com o uso de `AGENTS.md` como um "README for agents", ou seja, um local previsível para orientar agentes de código sobre contexto, comandos, convenções e restrições do projeto. Também faz sentido manter instruções específicas por app em `apps/*/AGENTS.md`, porque arquivos aninhados ajudam a aplicar regras mais específicas por parte do monorepo. ([Agents.md][1])

## Prompt recomendado - fase 1: inspeção e plano

Use este primeiro:

```md
I want to refactor the front-end applications to follow the design system of each app.

Before changing any file, you must inspect the repository and create a phased refactor plan.

Read these files first:

- `/AGENTS.md`
- `/CLAUDE.md`
- `/apps/portal/AGENTS.md`
- `/apps/storefront/AGENTS.md`
- `/apps/backoffice/AGENTS.md`
- `/docs/design/portal/design.json`
- `/docs/design/portal/design.md`
- `/docs/design/storefront/design.json`
- `/docs/design/storefront/design.md`
- `/docs/design/backoffice/design.json`
- `/docs/design/backoffice/design.md`

Scope:

- `/apps/portal`
- `/apps/storefront`
- `/apps/backoffice`

Main objective:

Refactor the UI/UX of each front-end app according to its own design system, without mixing visual rules between apps.

Important distinction:

- `/apps/portal` is the public central marketplace. It must look modern, clean, commercial, responsive, light-mode, and marketplace-oriented.
- `/apps/storefront` is the public website for each store. It must focus on one store, its identity, inventory, trust, contact, and conversion.
- `/apps/backoffice` is the administrative interface. It must be denser, functional, clear, permission-aware, and optimized for productivity.

Do not change files yet.

First, inspect:

- pages;
- layouts;
- components;
- composables;
- stores/state management if present;
- i18n files;
- Tailwind/CSS files;
- UI-related utilities;
- API integration points used by the front-end.

Then produce a refactor plan with:

1. Current structure summary.
2. Main UI/UX inconsistencies found.
3. Components that should be created.
4. Components that should be refactored.
5. Pages that should be refactored.
6. Design-system gaps.
7. i18n gaps.
8. Accessibility issues.
9. Responsive/mobile issues.
10. Risk areas.
11. Suggested execution phases.
12. Validation commands to run.

Rules:

- Do not rewrite unrelated code.
- Do not change API contracts unless strictly necessary.
- Do not move business rules into the front-end.
- Do not hardcode visible strings.
- Do not mix portal design rules into storefront or backoffice.
- Do not mix backoffice density into the public portal.
- Keep the MVP simple and production-ready.

At the end, wait for approval before implementing the plan.
```

## Prompt recomendado - fase 2: execução controlada

Depois que o agente gerar o plano, use:

```md
Execute the approved refactor plan.

Follow these files as mandatory instructions:

- `/AGENTS.md`
- `/CLAUDE.md`
- `/apps/portal/AGENTS.md`
- `/apps/storefront/AGENTS.md`
- `/apps/backoffice/AGENTS.md`
- `/docs/design/portal/design.json`
- `/docs/design/portal/design.md`
- `/docs/design/storefront/design.json`
- `/docs/design/storefront/design.md`
- `/docs/design/backoffice/design.json`
- `/docs/design/backoffice/design.md`

Execution rules:

1. Refactor each app according to its own design system.
2. Do not apply the same visual density to all apps.
3. Do not make the public portal look like an admin panel.
4. Do not make the backoffice look like a marketing marketplace.
5. Do not blindly copy portal components into storefront or backoffice.
6. Preserve existing API contracts.
7. Preserve existing routes unless the plan explicitly requires changes.
8. Preserve existing data flow unless there is a clear bug.
9. Use i18n for all visible text.
10. Keep components small and focused.
11. Use Nuxt 4, Vue 3 Composition API, TypeScript, and Tailwind CSS v4.
12. Keep the UI mobile-first and responsive.
13. Add loading, empty, error, disabled, and active states where applicable.
14. Avoid hardcoded strings in components.
15. Avoid thick focus rings.
16. Avoid Bootstrap-like visual patterns.
17. Avoid unnecessary abstractions.

Portal-specific rules:

- `/apps/portal` must follow `/docs/design/portal/design.json` and `/docs/design/portal/design.md`.
- It must look like a modern public vehicle marketplace.
- Use light mode.
- Use Inter.
- Use white cards, subtle borders, discreet shadows, and light gray backgrounds.
- Use small or medium buttons.
- Avoid `rounded-full` as the default.
- Use subtle input focus states.
- Vehicle listing must support fast comparison.
- Vehicle detail must prioritize photos, price, and contact CTAs.
- Mobile listing filters should use drawer or bottom sheet behavior.
- Active filters should appear as removable chips.
- Prefer pagination over infinite scroll for the MVP.

Storefront-specific rules:

- `/apps/storefront` must follow `/docs/design/storefront/design.json` and `/docs/design/storefront/design.md`.
- It must focus on a single store.
- It must respect store identity: logo, colors, banners, contact data, featured vehicles, SEO fields, and theme settings.
- It must reinforce trust and conversion.
- It must not show unrelated stores unless the feature explicitly allows related units.
- It must not look like the central marketplace unless the design system says so.
- It must not look like backoffice.

Backoffice-specific rules:

- `/apps/backoffice` must follow `/docs/design/backoffice/design.json` and `/docs/design/backoffice/design.md`.
- It must be denser and more functional than the public apps.
- It must prioritize productivity, clarity, permissions, tables, forms, filters, and bulk actions.
- It must not look like a public marketing website.
- It must respect roles and permissions.
- It must not expose actions the user is not authorized to perform.
- It must keep administrative flows clear and efficient.

Implementation order:

1. Update global CSS/theme tokens for each app if needed.
2. Create or normalize base UI components.
3. Refactor shared layout components.
4. Refactor `/apps/portal` public marketplace UI.
5. Refactor `/apps/storefront` public store UI.
6. Refactor `/apps/backoffice` administrative UI.
7. Add or fix loading, empty, error, disabled, hover, active, and focus states.
8. Fix i18n issues.
9. Run validation commands.

Validation:

Run the relevant commands if available:

- install/check dependencies if needed;
- lint;
- typecheck;
- tests;
- build.

If a command does not exist, report it.
If a command fails because of an existing issue, report it clearly.
If a command fails because of your changes, fix it.

Final response must include:

- summary of changed files;
- what was refactored in `/apps/portal`;
- what was refactored in `/apps/storefront`;
- what was refactored in `/apps/backoffice`;
- validation commands executed;
- remaining risks or follow-up tasks.
```

## Prompt mais curto para executar direto

Se você quiser mandar o agente executar sem fase de planejamento, use este:

```md
Refactor the front-end apps to follow their respective design systems.

Read first:

- `/AGENTS.md`
- `/CLAUDE.md`
- `/apps/portal/AGENTS.md`
- `/apps/storefront/AGENTS.md`
- `/apps/backoffice/AGENTS.md`
- `/docs/design/portal/design.json`
- `/docs/design/portal/design.md`
- `/docs/design/storefront/design.json`
- `/docs/design/storefront/design.md`
- `/docs/design/backoffice/design.json`
- `/docs/design/backoffice/design.md`

Scope:

- `/apps/portal`
- `/apps/storefront`
- `/apps/backoffice`

Rules:

- Each app must follow its own design system.
- Do not mix visual rules between apps.
- `/apps/portal` must look like a modern public vehicle marketplace.
- `/apps/storefront` must look like a public website for one vehicle store.
- `/apps/backoffice` must look like a dense, functional administrative interface.
- Use Nuxt 4, Vue 3 Composition API, TypeScript, Tailwind CSS v4, and i18n.
- Do not hardcode visible strings.
- Do not change API contracts unless strictly necessary.
- Do not move critical business logic to the front-end.
- Do not rewrite unrelated code.
- Keep the MVP simple, clean, responsive, accessible, and production-ready.

Portal:

- Light mode.
- Inter.
- White cards.
- Subtle borders.
- Discreet shadows.
- Light gray background.
- Small/medium buttons.
- Slightly rounded buttons.
- Subtle focus states.
- No Bootstrap-like UI.
- No admin-panel look.
- No `rounded-full` as default.
- No thick focus rings.
- Listing must support quick comparison.
- Detail must prioritize photos, price, and contact.

Storefront:

- Single-store experience.
- Respect store identity, logo, colors, banners, contacts, SEO, featured vehicles, and theme settings.
- Reinforce trust and conversion.
- Do not show unrelated stores unless explicitly supported.
- Do not make it look like backoffice.

Backoffice:

- Dense and functional.
- Optimized for productivity.
- Clear tables, forms, filters, actions, and states.
- Permission-aware.
- Do not expose unauthorized actions.
- Do not make it look like a public marketplace.

Before coding:

1. Inspect the current structure.
2. Identify existing components and pages.
3. Reuse existing components when possible.
4. Create missing base components only when useful.

After coding:

1. Run lint if available.
2. Run typecheck if available.
3. Run tests if available.
4. Run build if available.
5. Report what changed and what still needs attention.
```

## Prompt por app - Portal

```md
Refactor `/apps/portal` to follow the portal marketplace design system.

Read first:

- `/AGENTS.md`
- `/apps/portal/AGENTS.md`
- `/docs/design/portal/design.json`
- `/docs/design/portal/design.md`

Goal:

Make the portal look like a modern public vehicle marketplace.

Scope:

- home;
- search;
- listing;
- filters;
- vehicle card;
- vehicle detail;
- public store page inside the portal;
- favorites UI if present;
- lead/contact UI if present;
- loading, empty, error, disabled, hover, active, and focus states.

Rules:

- Use Nuxt 4, Vue 3 Composition API, TypeScript, Tailwind CSS v4, and i18n.
- Light mode only for now.
- Use Inter.
- Use white cards, subtle borders, discreet shadows, and light gray backgrounds.
- Use small or medium buttons.
- Use slightly rounded buttons.
- Avoid `rounded-full` as the default.
- Avoid thick focus rings.
- Avoid Bootstrap-like UI.
- Avoid admin-panel look.
- Use subtle input focus states.
- Use active filter chips.
- Use drawer or bottom sheet filters on mobile.
- Use sticky filter sidebar on desktop when appropriate.
- Prefer pagination over infinite scroll for the MVP.
- Vehicle cards must show image, title, price, metadata, store, city/state, and favorite action.
- Vehicle detail must prioritize photos, price, contact CTA, and "View on store website".
- Mobile detail must keep main CTAs accessible.
- Do not hardcode visible strings.
- Do not change API contracts unless strictly necessary.

After implementation, run lint/typecheck/build if available and report the result.
```

## Prompt por app - Storefront

```md
Refactor `/apps/storefront` to follow the storefront design system.

Read first:

- `/AGENTS.md`
- `/apps/storefront/AGENTS.md`
- `/docs/design/storefront/design.json`
- `/docs/design/storefront/design.md`

Goal:

Make the storefront work as a public website for one vehicle store, using a single Nuxt app resolved by domain/subdomain.

Scope:

- store home;
- inventory;
- vehicle detail;
- about page;
- contact page;
- store hero;
- store identity;
- featured vehicles;
- lead/contact UI;
- loading, empty, error, disabled, hover, active, and focus states.

Rules:

- Use Nuxt 4, Vue 3 Composition API, TypeScript, Tailwind CSS v4, and i18n.
- Respect store settings: logo, colors, banners, contacts, address, SEO fields, featured vehicles, and theme/layout settings.
- Keep the experience focused on one store.
- Do not show unrelated stores unless related units are explicitly supported.
- Reinforce trust and conversion.
- Keep contact CTAs visible and easy to use.
- Do not make the storefront look like the central marketplace unless the design system explicitly says so.
- Do not make it look like backoffice.
- Do not hardcode visible strings.
- Do not change API contracts unless strictly necessary.
- Do not move critical business logic to the front-end.

After implementation, run lint/typecheck/build if available and report the result.
```

## Prompt por app - Backoffice

```md
Refactor `/apps/backoffice` to follow the backoffice design system.

Read first:

- `/AGENTS.md`
- `/apps/backoffice/AGENTS.md`
- `/docs/design/backoffice/design.json`
- `/docs/design/backoffice/design.md`

Goal:

Make the backoffice a dense, clear, functional administrative interface for central admins and store users.

Scope:

- main layout;
- navigation;
- dashboard;
- tables;
- forms;
- filters;
- modals/drawers;
- vehicles management;
- leads management;
- store settings;
- users/team management;
- plans/subscriptions if present;
- domains if present;
- loading, empty, error, disabled, hover, active, and focus states.

Rules:

- Use Nuxt 4, Vue 3 Composition API, TypeScript, Tailwind CSS v4, and i18n.
- Prioritize productivity and clarity.
- Use dense but readable layouts.
- Use clear tables, filters, forms, and actions.
- Respect roles and permissions.
- Do not expose unauthorized actions in the UI.
- Keep destructive actions explicit and confirmable.
- Do not make it look like a public marketplace.
- Do not make it look like a marketing website.
- Do not hardcode visible strings.
- Do not change API contracts unless strictly necessary.
- Do not move critical business logic to the front-end.

After implementation, run lint/typecheck/build if available and report the result.
```

## Minha recomendação prática

Eu não pediria para refatorar os três de uma vez logo de cara. Eu faria nesta ordem:

1. `/apps/portal`, porque é a visão pública principal do marketplace.
2. `/apps/storefront`, reaproveitando só o que fizer sentido, sem copiar a cara do portal.
3. `/apps/backoffice`, com densidade e fluxo administrativo próprios.

O prompt mais seguro para começar é este:

```md
Start with `/apps/portal` only.

Read:

- `/AGENTS.md`
- `/apps/portal/AGENTS.md`
- `/docs/design/portal/design.json`
- `/docs/design/portal/design.md`

Inspect the current portal structure and produce a phased refactor plan.

Do not change files yet.
```

Depois que o plano ficar bom, mande executar a primeira fase.

[1]: https://agents.md/?utm_source=chatgpt.com "AGENTS.md"
