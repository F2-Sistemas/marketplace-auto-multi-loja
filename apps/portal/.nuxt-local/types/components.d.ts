
import type { DefineComponent, SlotsType } from 'vue'
type IslandComponent<T> = DefineComponent<{}, {refresh: () => Promise<void>}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, SlotsType<{ fallback: { error: unknown } }>> & T

type HydrationStrategies = {
  hydrateOnVisible?: IntersectionObserverInit | true
  hydrateOnIdle?: number | true
  hydrateOnInteraction?: keyof HTMLElementEventMap | Array<keyof HTMLElementEventMap> | true
  hydrateOnMediaQuery?: string
  hydrateAfter?: number
  hydrateWhen?: boolean
  hydrateNever?: true
}
type LazyComponent<T> = DefineComponent<HydrationStrategies, {}, {}, {}, {}, {}, {}, { hydrated: () => void }> & T

interface _GlobalComponents {
  LayoutBreadcrumb: typeof import("../../app/components/layout/Breadcrumb.vue")['default']
  LayoutSeoLandingSection: typeof import("../../app/components/layout/SeoLandingSection.vue")['default']
  LayoutSeoPageHeader: typeof import("../../app/components/layout/SeoPageHeader.vue")['default']
  LeadsLeadForm: typeof import("../../app/components/leads/LeadForm.vue")['default']
  SearchActiveFilterChips: typeof import("../../app/components/search/ActiveFilterChips.vue")['default']
  SearchSortSelect: typeof import("../../app/components/search/SortSelect.vue")['default']
  SearchVehicleFilters: typeof import("../../app/components/search/VehicleFilters.vue")['default']
  SearchVehicleSearchBox: typeof import("../../app/components/search/VehicleSearchBox.vue")['default']
  StoresStoreHero: typeof import("../../app/components/stores/StoreHero.vue")['default']
  StoresStoreInfo: typeof import("../../app/components/stores/StoreInfo.vue")['default']
  StoresStorePublicCard: typeof import("../../app/components/stores/StorePublicCard.vue")['default']
  UiBadge: typeof import("../../app/components/ui/UiBadge.vue")['default']
  UiButton: typeof import("../../app/components/ui/UiButton.vue")['default']
  UiCard: typeof import("../../app/components/ui/UiCard.vue")['default']
  UiDrawer: typeof import("../../app/components/ui/UiDrawer.vue")['default']
  UiImageCarousel: typeof import("../../app/components/ui/UiImageCarousel.vue")['default']
  UiInput: typeof import("../../app/components/ui/UiInput.vue")['default']
  UiModal: typeof import("../../app/components/ui/UiModal.vue")['default']
  UiPagination: typeof import("../../app/components/ui/UiPagination.vue")['default']
  UiSelect: typeof import("../../app/components/ui/UiSelect.vue")['default']
  UiSkeleton: typeof import("../../app/components/ui/UiSkeleton.vue")['default']
  UiTypeahead: typeof import("../../app/components/ui/UiTypeahead.vue")['default']
  VehiclesVehicleCard: typeof import("../../app/components/vehicles/VehicleCard.vue")['default']
  VehiclesVehicleDetailGallery: typeof import("../../app/components/vehicles/VehicleDetailGallery.vue")['default']
  VehiclesVehicleDetailSummary: typeof import("../../app/components/vehicles/VehicleDetailSummary.vue")['default']
  VehiclesVehicleGrid: typeof import("../../app/components/vehicles/VehicleGrid.vue")['default']
  VehiclesVehicleSellerCard: typeof import("../../app/components/vehicles/VehicleSellerCard.vue")['default']
  VehiclesVehicleSpecs: typeof import("../../app/components/vehicles/VehicleSpecs.vue")['default']
  NuxtWelcome: typeof import("../../node_modules/nuxt/dist/app/components/welcome.vue")['default']
  NuxtLayout: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-layout")['default']
  NuxtErrorBoundary: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']
  ClientOnly: typeof import("../../node_modules/nuxt/dist/app/components/client-only")['default']
  DevOnly: typeof import("../../node_modules/nuxt/dist/app/components/dev-only")['default']
  ServerPlaceholder: typeof import("../../node_modules/nuxt/dist/app/components/server-placeholder")['default']
  NuxtLink: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-link")['default']
  NuxtLoadingIndicator: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']
  NuxtTime: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']
  NuxtRouteAnnouncer: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']
  NuxtAnnouncer: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-announcer")['default']
  NuxtImg: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']
  NuxtPicture: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']
  NuxtPage: typeof import("../../node_modules/nuxt/dist/pages/runtime/page")['default']
  NoScript: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['NoScript']
  Link: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Link']
  Base: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Base']
  Title: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Title']
  Meta: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Meta']
  Style: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Style']
  Head: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Head']
  Html: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Html']
  Body: typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Body']
  NuxtIsland: typeof import("../../node_modules/nuxt/dist/app/components/nuxt-island")['default']
  LazyLayoutBreadcrumb: LazyComponent<typeof import("../../app/components/layout/Breadcrumb.vue")['default']>
  LazyLayoutSeoLandingSection: LazyComponent<typeof import("../../app/components/layout/SeoLandingSection.vue")['default']>
  LazyLayoutSeoPageHeader: LazyComponent<typeof import("../../app/components/layout/SeoPageHeader.vue")['default']>
  LazyLeadsLeadForm: LazyComponent<typeof import("../../app/components/leads/LeadForm.vue")['default']>
  LazySearchActiveFilterChips: LazyComponent<typeof import("../../app/components/search/ActiveFilterChips.vue")['default']>
  LazySearchSortSelect: LazyComponent<typeof import("../../app/components/search/SortSelect.vue")['default']>
  LazySearchVehicleFilters: LazyComponent<typeof import("../../app/components/search/VehicleFilters.vue")['default']>
  LazySearchVehicleSearchBox: LazyComponent<typeof import("../../app/components/search/VehicleSearchBox.vue")['default']>
  LazyStoresStoreHero: LazyComponent<typeof import("../../app/components/stores/StoreHero.vue")['default']>
  LazyStoresStoreInfo: LazyComponent<typeof import("../../app/components/stores/StoreInfo.vue")['default']>
  LazyStoresStorePublicCard: LazyComponent<typeof import("../../app/components/stores/StorePublicCard.vue")['default']>
  LazyUiBadge: LazyComponent<typeof import("../../app/components/ui/UiBadge.vue")['default']>
  LazyUiButton: LazyComponent<typeof import("../../app/components/ui/UiButton.vue")['default']>
  LazyUiCard: LazyComponent<typeof import("../../app/components/ui/UiCard.vue")['default']>
  LazyUiDrawer: LazyComponent<typeof import("../../app/components/ui/UiDrawer.vue")['default']>
  LazyUiImageCarousel: LazyComponent<typeof import("../../app/components/ui/UiImageCarousel.vue")['default']>
  LazyUiInput: LazyComponent<typeof import("../../app/components/ui/UiInput.vue")['default']>
  LazyUiModal: LazyComponent<typeof import("../../app/components/ui/UiModal.vue")['default']>
  LazyUiPagination: LazyComponent<typeof import("../../app/components/ui/UiPagination.vue")['default']>
  LazyUiSelect: LazyComponent<typeof import("../../app/components/ui/UiSelect.vue")['default']>
  LazyUiSkeleton: LazyComponent<typeof import("../../app/components/ui/UiSkeleton.vue")['default']>
  LazyUiTypeahead: LazyComponent<typeof import("../../app/components/ui/UiTypeahead.vue")['default']>
  LazyVehiclesVehicleCard: LazyComponent<typeof import("../../app/components/vehicles/VehicleCard.vue")['default']>
  LazyVehiclesVehicleDetailGallery: LazyComponent<typeof import("../../app/components/vehicles/VehicleDetailGallery.vue")['default']>
  LazyVehiclesVehicleDetailSummary: LazyComponent<typeof import("../../app/components/vehicles/VehicleDetailSummary.vue")['default']>
  LazyVehiclesVehicleGrid: LazyComponent<typeof import("../../app/components/vehicles/VehicleGrid.vue")['default']>
  LazyVehiclesVehicleSellerCard: LazyComponent<typeof import("../../app/components/vehicles/VehicleSellerCard.vue")['default']>
  LazyVehiclesVehicleSpecs: LazyComponent<typeof import("../../app/components/vehicles/VehicleSpecs.vue")['default']>
  LazyNuxtWelcome: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/welcome.vue")['default']>
  LazyNuxtLayout: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-layout")['default']>
  LazyNuxtErrorBoundary: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']>
  LazyClientOnly: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/client-only")['default']>
  LazyDevOnly: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/dev-only")['default']>
  LazyServerPlaceholder: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
  LazyNuxtLink: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-link")['default']>
  LazyNuxtLoadingIndicator: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']>
  LazyNuxtTime: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']>
  LazyNuxtRouteAnnouncer: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']>
  LazyNuxtAnnouncer: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-announcer")['default']>
  LazyNuxtImg: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']>
  LazyNuxtPicture: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']>
  LazyNuxtPage: LazyComponent<typeof import("../../node_modules/nuxt/dist/pages/runtime/page")['default']>
  LazyNoScript: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['NoScript']>
  LazyLink: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Link']>
  LazyBase: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Base']>
  LazyTitle: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Title']>
  LazyMeta: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Meta']>
  LazyStyle: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Style']>
  LazyHead: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Head']>
  LazyHtml: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Html']>
  LazyBody: LazyComponent<typeof import("../../node_modules/nuxt/dist/head/runtime/components")['Body']>
  LazyNuxtIsland: LazyComponent<typeof import("../../node_modules/nuxt/dist/app/components/nuxt-island")['default']>
}

declare module 'vue' {
  export interface GlobalComponents extends _GlobalComponents { }
}

export {}
