# Portal Marketplace Design System

Version: 1.0.0  
App: `/apps/portal`  
Default locale: `pt-BR`  
Default currency: `BRL`  
Default theme: light mode  

---

## Goal

This document defines the UI and UX rules for the public central marketplace portal.

The portal must help visitors search vehicles across multiple stores, compare listings, open vehicle details, favorite vehicles, and contact stores.

The UI must look like a modern marketplace, not like an admin panel.

---

## Visual direction

The portal UI must be:

- modern;
- clean;
- clear;
- commercial;
- responsive;
- trustworthy;
- light mode;
- not Bootstrap-like;
- not backoffice-like.

Use:

- Inter as the primary font;
- light gray page background;
- white cards;
- subtle borders;
- discreet shadows;
- small or medium buttons;
- slightly rounded buttons;
- subtle input focus states;
- fast transitions.

Avoid:

- `rounded-full` as the default;
- thick focus rings;
- strong default blue browser focus;
- oversized buttons;
- polluted cards;
- hardcoded visible text.

---

## Tailwind CSS v4

Use theme variables and tokens.

Recommended CSS entry:

```txt
/apps/portal/app/assets/css/main.css
```

Recommended base:

```css
@import "tailwindcss";

@theme {
  --font-sans: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;

  --color-brand-50: oklch(0.985 0.012 252);
  --color-brand-100: oklch(0.965 0.024 252);
  --color-brand-200: oklch(0.925 0.046 252);
  --color-brand-500: oklch(0.62 0.17 252);
  --color-brand-600: oklch(0.52 0.18 252);
  --color-brand-700: oklch(0.43 0.15 252);
  --color-brand-800: oklch(0.34 0.11 252);

  --radius-button: 0.5rem;
  --radius-input: 0.5rem;
  --radius-card: 0.875rem;

  --shadow-card: 0 1px 2px rgb(15 23 42 / 0.04);
  --shadow-card-hover: 0 10px 30px rgb(15 23 42 / 0.08);
}

:root {
  color-scheme: light;
}

html {
  font-family: var(--font-sans);
}

button:not(:disabled),
a[href],
[role="button"]:not([aria-disabled="true"]) {
  cursor: pointer;
}

button:disabled,
[aria-disabled="true"] {
  cursor: default;
}

:focus-visible {
  outline: none;
}
```

---

## Focus states

Inputs should generally use:

```html
<input
  class="border border-neutral-200 bg-white text-sm outline-none transition focus:border-neutral-300 focus:ring-0"
/>
```

When a more visible focus state is needed:

```html
<input
  class="border border-neutral-200 bg-white text-sm outline-none transition focus:border-neutral-300 focus:ring-1 focus:ring-neutral-200"
/>
```

Do not use thick focus rings by default.

---

## Core components

Expected base components:

- `UiButton`
- `UiInput`
- `UiSelect`
- `UiBadge`
- `UiCard`
- `UiDrawer`
- `UiModal`
- `UiSkeleton`
- `UiPagination`

Expected marketplace components:

- `VehicleSearchBox`
- `VehicleFilters`
- `ActiveFilterChips`
- `SortSelect`
- `VehicleCard`
- `VehicleGrid`
- `VehicleDetailGallery`
- `VehicleDetailSummary`
- `VehicleSpecs`
- `StorePublicCard`
- `LeadForm`
- `Breadcrumb`
- `SeoLandingSection`
- `SeoPageHeader`

---

## Vehicle card

A vehicle card must show:

- main image;
- title;
- price or "Price on request";
- year;
- mileage;
- transmission;
- fuel;
- city/state;
- store name;
- favorite button.

Rules:

- Use fixed image aspect ratio.
- Price has the strongest visual weight.
- Title is limited to two lines.
- Metadata must be easy to scan.
- Avoid too many badges.
- Favorite button must not shift layout.

---

## Listing UX

Rules:

- Show result count.
- Show sorting.
- Show active filters as removable chips.
- Use sticky sidebar filters on desktop.
- Use drawer or bottom sheet filters on mobile.
- Use skeleton loading.
- Use useful empty states.
- Prefer pagination over infinite scroll for the MVP.

---

## Vehicle detail UX

Rules:

- Photos, price, and CTAs must appear early.
- Contact/WhatsApp is the primary CTA when available.
- "View on store website" is complementary.
- Mobile should use sticky bottom CTAs.
- Show other vehicles from the same store.
- Show similar vehicles when useful.

---

## Store public page

Rules:

- Reinforce trust.
- Show store identity and contact.
- List only the store inventory.
- Show CTA to the store website.
- Keep the page clearly inside the central portal.

---

## i18n

Do not hardcode visible strings in components.

Use translation files.

Defaults:

- locale: `pt-BR`;
- currency: `BRL`;
- date format: Brazilian format.

---

## Done criteria

A portal UI change is ready when:

- it follows `design.json`;
- it follows this document;
- it is responsive;
- it uses i18n;
- it has loading, empty, and error states where applicable;
- it does not look like Bootstrap;
- it does not look like backoffice;
- focus states are subtle;
- no unnecessary API contract changes were introduced.
