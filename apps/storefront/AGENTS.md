# /apps/storefront/AGENTS.md

## Scope

This app is the public storefront used by multiple stores.

It resolves the current store by domain or subdomain and renders the store website using a single Nuxt application.

Responsible areas:

- store home;
- store inventory;
- vehicle detail;
- about page;
- contact page;
- related units when applicable;
- store SEO;
- configurable visual theme.

---

## Design system

Before changing storefront UI, read:

- `/docs/design/storefront/design.json`
- `/docs/design/storefront/design.md`

If these files are still placeholders, keep the UI aligned with the general project direction and do not copy portal-specific marketplace patterns blindly.

---

## Rules

- Keep the storefront focused on a single store.
- Do not show vehicles from unrelated stores unless explicitly allowed by the feature.
- Respect store settings such as logo, colors, contact data, banners, featured vehicles, and SEO fields.
- Keep the UI commercial, clean, responsive, and trustworthy.
- Avoid hardcoded visible text.
- Use i18n.
- Avoid critical business logic in the front-end.
- Do not create one deployment per store.
