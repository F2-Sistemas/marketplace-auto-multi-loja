
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


export const LayoutBreadcrumb: typeof import("../app/components/layout/Breadcrumb.vue")['default']
export const LayoutSeoLandingSection: typeof import("../app/components/layout/SeoLandingSection.vue")['default']
export const LayoutSeoPageHeader: typeof import("../app/components/layout/SeoPageHeader.vue")['default']
export const LeadsLeadForm: typeof import("../app/components/leads/LeadForm.vue")['default']
export const SearchActiveFilterChips: typeof import("../app/components/search/ActiveFilterChips.vue")['default']
export const SearchSortSelect: typeof import("../app/components/search/SortSelect.vue")['default']
export const SearchVehicleFilters: typeof import("../app/components/search/VehicleFilters.vue")['default']
export const SearchVehicleSearchBox: typeof import("../app/components/search/VehicleSearchBox.vue")['default']
export const StoresStoreHero: typeof import("../app/components/stores/StoreHero.vue")['default']
export const StoresStoreInfo: typeof import("../app/components/stores/StoreInfo.vue")['default']
export const StoresStorePublicCard: typeof import("../app/components/stores/StorePublicCard.vue")['default']
export const UiBadge: typeof import("../app/components/ui/UiBadge.vue")['default']
export const UiButton: typeof import("../app/components/ui/UiButton.vue")['default']
export const UiCard: typeof import("../app/components/ui/UiCard.vue")['default']
export const UiDrawer: typeof import("../app/components/ui/UiDrawer.vue")['default']
export const UiImageCarousel: typeof import("../app/components/ui/UiImageCarousel.vue")['default']
export const UiInput: typeof import("../app/components/ui/UiInput.vue")['default']
export const UiModal: typeof import("../app/components/ui/UiModal.vue")['default']
export const UiPagination: typeof import("../app/components/ui/UiPagination.vue")['default']
export const UiSelect: typeof import("../app/components/ui/UiSelect.vue")['default']
export const UiSkeleton: typeof import("../app/components/ui/UiSkeleton.vue")['default']
export const UiTypeahead: typeof import("../app/components/ui/UiTypeahead.vue")['default']
export const VehiclesVehicleCard: typeof import("../app/components/vehicles/VehicleCard.vue")['default']
export const VehiclesVehicleDetailGallery: typeof import("../app/components/vehicles/VehicleDetailGallery.vue")['default']
export const VehiclesVehicleDetailSummary: typeof import("../app/components/vehicles/VehicleDetailSummary.vue")['default']
export const VehiclesVehicleGrid: typeof import("../app/components/vehicles/VehicleGrid.vue")['default']
export const VehiclesVehicleSellerCard: typeof import("../app/components/vehicles/VehicleSellerCard.vue")['default']
export const VehiclesVehicleSpecs: typeof import("../app/components/vehicles/VehicleSpecs.vue")['default']
export const NuxtWelcome: typeof import("../node_modules/nuxt/dist/app/components/welcome.vue")['default']
export const NuxtLayout: typeof import("../node_modules/nuxt/dist/app/components/nuxt-layout")['default']
export const NuxtErrorBoundary: typeof import("../node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']
export const ClientOnly: typeof import("../node_modules/nuxt/dist/app/components/client-only")['default']
export const DevOnly: typeof import("../node_modules/nuxt/dist/app/components/dev-only")['default']
export const ServerPlaceholder: typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']
export const NuxtLink: typeof import("../node_modules/nuxt/dist/app/components/nuxt-link")['default']
export const NuxtLoadingIndicator: typeof import("../node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']
export const NuxtTime: typeof import("../node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']
export const NuxtRouteAnnouncer: typeof import("../node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']
export const NuxtAnnouncer: typeof import("../node_modules/nuxt/dist/app/components/nuxt-announcer")['default']
export const NuxtImg: typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']
export const NuxtPicture: typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']
export const NuxtPage: typeof import("../node_modules/nuxt/dist/pages/runtime/page")['default']
export const NoScript: typeof import("../node_modules/nuxt/dist/head/runtime/components")['NoScript']
export const Link: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Link']
export const Base: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Base']
export const Title: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Title']
export const Meta: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Meta']
export const Style: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Style']
export const Head: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Head']
export const Html: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Html']
export const Body: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Body']
export const NuxtIsland: typeof import("../node_modules/nuxt/dist/app/components/nuxt-island")['default']
export const LazyLayoutBreadcrumb: LazyComponent<typeof import("../app/components/layout/Breadcrumb.vue")['default']>
export const LazyLayoutSeoLandingSection: LazyComponent<typeof import("../app/components/layout/SeoLandingSection.vue")['default']>
export const LazyLayoutSeoPageHeader: LazyComponent<typeof import("../app/components/layout/SeoPageHeader.vue")['default']>
export const LazyLeadsLeadForm: LazyComponent<typeof import("../app/components/leads/LeadForm.vue")['default']>
export const LazySearchActiveFilterChips: LazyComponent<typeof import("../app/components/search/ActiveFilterChips.vue")['default']>
export const LazySearchSortSelect: LazyComponent<typeof import("../app/components/search/SortSelect.vue")['default']>
export const LazySearchVehicleFilters: LazyComponent<typeof import("../app/components/search/VehicleFilters.vue")['default']>
export const LazySearchVehicleSearchBox: LazyComponent<typeof import("../app/components/search/VehicleSearchBox.vue")['default']>
export const LazyStoresStoreHero: LazyComponent<typeof import("../app/components/stores/StoreHero.vue")['default']>
export const LazyStoresStoreInfo: LazyComponent<typeof import("../app/components/stores/StoreInfo.vue")['default']>
export const LazyStoresStorePublicCard: LazyComponent<typeof import("../app/components/stores/StorePublicCard.vue")['default']>
export const LazyUiBadge: LazyComponent<typeof import("../app/components/ui/UiBadge.vue")['default']>
export const LazyUiButton: LazyComponent<typeof import("../app/components/ui/UiButton.vue")['default']>
export const LazyUiCard: LazyComponent<typeof import("../app/components/ui/UiCard.vue")['default']>
export const LazyUiDrawer: LazyComponent<typeof import("../app/components/ui/UiDrawer.vue")['default']>
export const LazyUiImageCarousel: LazyComponent<typeof import("../app/components/ui/UiImageCarousel.vue")['default']>
export const LazyUiInput: LazyComponent<typeof import("../app/components/ui/UiInput.vue")['default']>
export const LazyUiModal: LazyComponent<typeof import("../app/components/ui/UiModal.vue")['default']>
export const LazyUiPagination: LazyComponent<typeof import("../app/components/ui/UiPagination.vue")['default']>
export const LazyUiSelect: LazyComponent<typeof import("../app/components/ui/UiSelect.vue")['default']>
export const LazyUiSkeleton: LazyComponent<typeof import("../app/components/ui/UiSkeleton.vue")['default']>
export const LazyUiTypeahead: LazyComponent<typeof import("../app/components/ui/UiTypeahead.vue")['default']>
export const LazyVehiclesVehicleCard: LazyComponent<typeof import("../app/components/vehicles/VehicleCard.vue")['default']>
export const LazyVehiclesVehicleDetailGallery: LazyComponent<typeof import("../app/components/vehicles/VehicleDetailGallery.vue")['default']>
export const LazyVehiclesVehicleDetailSummary: LazyComponent<typeof import("../app/components/vehicles/VehicleDetailSummary.vue")['default']>
export const LazyVehiclesVehicleGrid: LazyComponent<typeof import("../app/components/vehicles/VehicleGrid.vue")['default']>
export const LazyVehiclesVehicleSellerCard: LazyComponent<typeof import("../app/components/vehicles/VehicleSellerCard.vue")['default']>
export const LazyVehiclesVehicleSpecs: LazyComponent<typeof import("../app/components/vehicles/VehicleSpecs.vue")['default']>
export const LazyNuxtWelcome: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/welcome.vue")['default']>
export const LazyNuxtLayout: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-layout")['default']>
export const LazyNuxtErrorBoundary: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']>
export const LazyClientOnly: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/client-only")['default']>
export const LazyDevOnly: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/dev-only")['default']>
export const LazyServerPlaceholder: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
export const LazyNuxtLink: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-link")['default']>
export const LazyNuxtLoadingIndicator: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']>
export const LazyNuxtTime: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']>
export const LazyNuxtRouteAnnouncer: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']>
export const LazyNuxtAnnouncer: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-announcer")['default']>
export const LazyNuxtImg: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']>
export const LazyNuxtPicture: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']>
export const LazyNuxtPage: LazyComponent<typeof import("../node_modules/nuxt/dist/pages/runtime/page")['default']>
export const LazyNoScript: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['NoScript']>
export const LazyLink: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Link']>
export const LazyBase: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Base']>
export const LazyTitle: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Title']>
export const LazyMeta: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Meta']>
export const LazyStyle: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Style']>
export const LazyHead: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Head']>
export const LazyHtml: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Html']>
export const LazyBody: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Body']>
export const LazyNuxtIsland: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-island")['default']>

export const componentNames: string[]
