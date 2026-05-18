# /apps/backoffice/AGENTS.md

## Scope

This app is the administrative interface.

It serves:

- central administration;
- store administration;
- role-based access;
- store management;
- vehicles;
- leads;
- users;
- plans;
- subscriptions;
- domains;
- moderation;
- banners;
- reports;
- integrations.

---

## Design system

Before changing backoffice UI, read:

- `/docs/design/backoffice/design.json`
- `/docs/design/backoffice/design.md`

If these files are still placeholders, keep the UI dense, functional, clear, and consistent with the general project direction.

---

## Rules

- Prioritize clarity and efficiency over marketing visuals.
- Use denser layouts than the public portal.
- Respect permissions and roles.
- Never expose actions the user is not authorized to perform.
- Avoid hardcoded visible text.
- Use i18n.
- Keep business rules in the API whenever possible.
- Do not weaken authorization checks for UI convenience.
