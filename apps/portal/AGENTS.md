# /apps/portal/AGENTS.md

## Scope

This app is the public central marketplace portal.

It is responsible for:

- public home;
- global vehicle search;
- vehicle listing;
- filters;
- vehicle detail;
- public store page inside the portal;
- visitor favorites;
- visitor login/register;
- contact and lead generation;
- portal SEO.

---

## Mandatory design system

Before changing any file in this app, read:

- `/docs/design/portal/design.json`
- `/docs/design/portal/design.md`

These files are mandatory for any visual or UX change.

`design.json` is the structured source for tokens, components, states, layout, and rules.

`design.md` is the explanatory source with usage rules, examples, and acceptance criteria.

---

## Visual rules

The interface must be:

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
- `neutral-50` page background;
- white cards;
- `neutral-200` borders;
- `neutral-300` hover borders;
- discreet shadows;
- `rounded-button` for buttons;
- `rounded-input` for inputs;
- `rounded-card` for cards;
- transitions between 150ms and 220ms.

Avoid:

- `rounded-full` as the default;
- `focus:ring-2`, `focus:ring-4`, or strong focus rings;
- strong default blue browser focus;
- oversized buttons;
- polluted cards;
- large components;
- hardcoded visible text.

---

## Expected components

When creating or refactoring UI, prefer these components:

- `components/ui/UiButton.vue`
- `components/ui/UiInput.vue`
- `components/ui/UiSelect.vue`
- `components/ui/UiBadge.vue`
- `components/ui/UiCard.vue`
- `components/ui/UiDrawer.vue`
- `components/ui/UiModal.vue`
- `components/ui/UiSkeleton.vue`
- `components/ui/UiPagination.vue`
- `components/search/VehicleSearchBox.vue`
- `components/search/VehicleFilters.vue`
- `components/search/ActiveFilterChips.vue`
- `components/search/SortSelect.vue`
- `components/vehicle/VehicleCard.vue`
- `components/vehicle/VehicleGrid.vue`
- `components/vehicle/VehicleDetailGallery.vue`
- `components/vehicle/VehicleDetailSummary.vue`
- `components/vehicle/VehicleSpecs.vue`
- `components/store/StorePublicCard.vue`
- `components/store/StoreHero.vue`
- `components/store/StoreInfo.vue`
- `components/lead/LeadForm.vue`
- `components/seo/Breadcrumb.vue`
- `components/seo/SeoLandingSection.vue`
- `components/seo/SeoPageHeader.vue`

If a component does not exist yet, create it according to the portal design system.

---

## Mandatory UX

### Home

- Search must appear above the fold.
- The hero must be objective.
- Useful shortcuts are allowed.
- Avoid heavy automatic carousels.

### Listing

- Active filters must appear as removable chips.
- Desktop should use a sticky filter sidebar when there is enough space.
- Mobile should use a drawer or bottom sheet for filters.
- Use skeleton loading.
- Use useful empty states.
- Prefer pagination over infinite scroll for the MVP.

### Vehicle card

- Use fixed image aspect ratio.
- Give price the strongest visual weight.
- Limit title to two lines.
- Keep metadata scannable.
- Show store and city/state.
- Favorite button must not shift layout.

### Vehicle detail

- Photos, price, and CTAs must appear early.
- WhatsApp/contact should be the primary CTA when available.
- "View on store website" must exist as a complementary CTA.
- Mobile must have accessible sticky bottom CTAs.
- Show other vehicles from the same store.

### Store public page

- Reinforce trust.
- List the store inventory.
- Show contact information.
- Show CTA to the store website.

---

## i18n

Do not hardcode visible text in components.

Use translation files.

Defaults:

- locale: `pt-BR`;
- currency: `BRL`;
- dates: Brazilian format.

---

## Tailwind CSS v4

Prefer theme variables and tokens from the design system.

Inputs should follow this direction:

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

Do not use thick focus rings as the default.

---

## Done criteria

A change in this app is ready when:

- it follows `/docs/design/portal/design.json`;
- it follows `/docs/design/portal/design.md`;
- it is responsive;
- it uses i18n;
- it has loading, empty, and error states where applicable;
- it does not look like Bootstrap;
- it does not look like backoffice;
- it does not use heavy focus states;
- it does not add unnecessary complexity.
