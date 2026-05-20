<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from '#app';
import { useVehicles } from '~/composables/useVehicles';
import { useI18n } from '~/composables/useI18n';
import Breadcrumb from '~/components/layout/Breadcrumb.vue';
import SeoPageHeader from '~/components/layout/SeoPageHeader.vue';
import VehicleFilters from '~/components/search/VehicleFilters.vue';
import ActiveFilterChips from '~/components/search/ActiveFilterChips.vue';
import SortSelect from '~/components/search/SortSelect.vue';
import VehicleGrid from '~/components/vehicles/VehicleGrid.vue';
import UiDrawer from '~/components/ui/UiDrawer.vue';
import UiButton from '~/components/ui/UiButton.vue';
import UiPagination from '~/components/ui/UiPagination.vue';

definePageMeta({
    layout: 'default',
});

const route = useRoute();
const router = useRouter();
const { t } = useI18n();
const {
    vehicles: paginatedVehicles,
    totalResults,
    pending: loading,
    currentPage,
    totalPages,
    selectedBrand,
    selectedStore,
    searchPayload,
} = useVehicles();

const isMobileDrawerOpen = ref(false);
const layoutView = ref<'grid' | 'list'>('grid');

const parseAndValidateQuery = () => {
    const query = route.query;

    // 1. Search term
    if (query.q !== undefined) {
        searchPayload.value.search.term = String(query.q);
    } else {
        searchPayload.value.search.term = '';
    }

    // 2. Brand
    if (query.brand !== undefined) {
        const brandId = Number(query.brand);
        if (!isNaN(brandId) && brandId > 0) {
            searchPayload.value.filters.brand_id = brandId;
        } else {
            searchPayload.value.filters.brand_id = null;
        }
    } else {
        searchPayload.value.filters.brand_id = null;
    }

    // 3. Store
    if (query.store !== undefined) {
        selectedStore.value = String(query.store);
    } else {
        selectedStore.value = '';
    }

    // 4. Transmission
    if (query.transmission !== undefined) {
        const raw = Array.isArray(query.transmission)
            ? query.transmission.map(String)
            : String(query.transmission).split(',');
        const validTransmissions = ['automatic', 'manual'];
        searchPayload.value.filters.transmission = raw.filter((t) => validTransmissions.includes(t));
    } else {
        searchPayload.value.filters.transmission = [];
    }

    // 5. Fuel
    if (query.fuel !== undefined) {
        const raw = Array.isArray(query.fuel) ? query.fuel.map(String) : String(query.fuel).split(',');
        const validFuels = ['flex', 'gasoline', 'ethanol', 'diesel', 'electric', 'hybrid'];
        searchPayload.value.filters.fuel = raw.filter((f) => validFuels.includes(f));
    } else {
        searchPayload.value.filters.fuel = [];
    }

    // 6. Max Price
    if (query.price_max !== undefined) {
        const maxVal = Number(query.price_max);
        if (!isNaN(maxVal) && maxVal >= 50000 && maxVal <= 250000) {
            searchPayload.value.filters.price.max = maxVal >= 250000 ? null : maxVal;
        } else {
            searchPayload.value.filters.price.max = null;
        }
    } else {
        searchPayload.value.filters.price.max = null;
    }

    // 7. Location Mode and Radius
    if (query.loc_mode !== undefined) {
        const mode = String(query.loc_mode);
        if (mode === 'all') {
            searchPayload.value.location.mode = 'all';
            searchPayload.value.location.radius_km = null;
        } else if (mode === 'city') {
            searchPayload.value.location.mode = 'city';
            searchPayload.value.location.radius_km = null;
        } else if (mode === 'state') {
            searchPayload.value.location.mode = 'state';
            searchPayload.value.location.radius_km = null;
        } else if (mode.startsWith('radius-')) {
            const radius = Number(mode.split('-')[1]);
            if (!isNaN(radius) && [50, 100, 150].includes(radius)) {
                searchPayload.value.location.mode = 'radius';
                searchPayload.value.location.radius_km = radius;
            }
        }
    } else {
        searchPayload.value.location.mode = 'all';
        searchPayload.value.location.radius_km = null;
    }

    // 8. City ID
    if (query.city !== undefined) {
        const cityId = Number(query.city);
        if (!isNaN(cityId) && cityId > 0) {
            searchPayload.value.location.city_id = cityId;
        } else {
            searchPayload.value.location.city_id = null;
        }
    } else {
        searchPayload.value.location.city_id = null;
    }

    // 9. State ID
    if (query.state !== undefined) {
        const stateId = Number(query.state);
        if (!isNaN(stateId) && stateId > 0) {
            searchPayload.value.location.state_id = stateId;
        } else {
            searchPayload.value.location.state_id = null;
        }
    } else {
        searchPayload.value.location.state_id = null;
    }

    // 10. Sort
    if (query.sort !== undefined) {
        const sortVal = String(query.sort);
        const validSorts = ['newest', 'price_asc', 'price_desc', 'year_desc'];
        if (validSorts.includes(sortVal)) {
            if (sortVal === 'price_asc') {
                searchPayload.value.sort = { field: 'price', direction: 'asc' };
            } else if (sortVal === 'price_desc') {
                searchPayload.value.sort = { field: 'price', direction: 'desc' };
            } else if (sortVal === 'year_desc') {
                searchPayload.value.sort = { field: 'year', direction: 'desc' };
            } else {
                searchPayload.value.sort = { field: 'created_at', direction: 'desc' };
            }
        }
    } else {
        searchPayload.value.sort = { field: 'created_at', direction: 'desc' };
    }

    // 11. Page
    if (query.page !== undefined) {
        const pageNum = Number(query.page);
        if (!isNaN(pageNum) && pageNum >= 1) {
            searchPayload.value.pagination.page = pageNum;
        } else {
            searchPayload.value.pagination.page = 1;
        }
    } else {
        searchPayload.value.pagination.page = 1;
    }
};

const buildQueryFromState = () => {
    const q: any = {};

    if (searchPayload.value.search.term) {
        q.q = searchPayload.value.search.term;
    }
    if (searchPayload.value.filters.brand_id) {
        q.brand = searchPayload.value.filters.brand_id;
    }
    if (selectedStore.value) {
        q.store = selectedStore.value;
    }
    if (searchPayload.value.filters.transmission.length > 0) {
        q.transmission = searchPayload.value.filters.transmission.join(',');
    }
    if (searchPayload.value.filters.fuel.length > 0) {
        q.fuel = searchPayload.value.filters.fuel.join(',');
    }
    if (searchPayload.value.filters.price.max !== null) {
        q.price_max = searchPayload.value.filters.price.max;
    }

    const loc = searchPayload.value.location;
    if (loc.mode !== 'all') {
        if (loc.mode === 'radius') {
            q.loc_mode = `radius-${loc.radius_km || 50}`;
        } else {
            q.loc_mode = loc.mode;
        }
    }
    if (loc.city_id) {
        q.city = loc.city_id;
    }
    if (loc.state_id) {
        q.state = loc.state_id;
    }

    const f = searchPayload.value.sort.field;
    const d = searchPayload.value.sort.direction;
    if (f === 'price' && d === 'asc') q.sort = 'price_asc';
    else if (f === 'price' && d === 'desc') q.sort = 'price_desc';
    else if (f === 'year' && d === 'desc') q.sort = 'year_desc';
    else if (f !== 'created_at' || d !== 'desc') q.sort = 'newest';

    if (searchPayload.value.pagination.page > 1) {
        q.page = searchPayload.value.pagination.page;
    }

    return q;
};

const areQueriesEqual = (q1: any, q2: any) => {
    const keys1 = Object.keys(q1).sort();
    const keys2 = Object.keys(q2).sort();
    if (keys1.length !== keys2.length) return false;
    return keys1.every((k) => String(q1[k]) === String(q2[k]));
};

let debounceTimeout: NodeJS.Timeout | null = null;

const updateUrlWithDebounce = () => {
    if (debounceTimeout) {
        clearTimeout(debounceTimeout);
    }
    debounceTimeout = setTimeout(() => {
        const query = buildQueryFromState();
        if (!areQueriesEqual(query, route.query)) {
            router.push({
                path: route.path,
                query,
            });
        }
    }, 400);
};

onMounted(() => {
    parseAndValidateQuery();
    const savedLayout = localStorage.getItem('auto-hub:search-layout');
    if (savedLayout === 'grid' || savedLayout === 'list') {
        layoutView.value = savedLayout;
    }
});

const setLayout = (layout: 'grid' | 'list') => {
    layoutView.value = layout;
    localStorage.setItem('auto-hub:search-layout', layout);
};

watch(
    () => route.query,
    (newQuery) => {
        const currentQueryFromState = buildQueryFromState();
        if (!areQueriesEqual(newQuery, currentQueryFromState)) {
            parseAndValidateQuery();
        }
    }
);

watch(
    [searchPayload, selectedStore],
    () => {
        updateUrlWithDebounce();
    },
    { deep: true }
);
</script>

<template>
    <div class="container max-w-7xl mx-auto px-4 md:px-6 py-6 space-y-6">
        <!-- Breadcrumbs -->
        <Breadcrumb :items="[{ label: t('search.catalogTitle') }]" />

        <!-- Page Header details -->
        <SeoPageHeader :title="t('search.catalogTitle')" :description="t('search.catalogSubtitle')" />

        <!-- Main columns sheet -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
            <!-- Desktop sidebar filters -->
            <aside
                class="hidden lg:block lg:sticky lg:top-20 bg-white border border-neutral-200 rounded-card p-2 shadow-sm space-y-6"
            >
                <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
                    <h3
                        class="font-extrabold text-neutral-800 text-sm uppercase tracking-wider flex items-center gap-2"
                    >
                        <iconify-icon icon="tabler:filter" class="text-neutral-400"></iconify-icon>
                        <span>Filtros de Busca</span>
                    </h3>
                </div>
                <VehicleFilters />
            </aside>

            <!-- Search lists viewport columns -->
            <section class="lg:col-span-3 space-y-6">
                <!-- Toolbar bar specs -->
                <div
                    class="p-4 bg-white border border-neutral-200 rounded-card shadow-sm flex items-center justify-between gap-4"
                >
                    <!-- Counters -->
                    <div class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">
                        <span>{{ totalResults }}</span>
                        <span class="text-neutral-400 font-normal lowercase">
                            {{ totalResults === 1 ? 'veículo encontrado' : 'veículos encontrados' }}
                        </span>
                    </div>

                    <!-- Sorting dropdown + Layout toggle + Mobile filter action button -->
                    <div class="flex items-center gap-3">
                        <SortSelect />

                        <!-- Layout Toggles (Hidden on mobile) -->
                        <div class="hidden lg:flex items-center bg-neutral-100 rounded-lg p-1">
                            <button
                                @click="setLayout('grid')"
                                :class="[
                                    'p-1.5 rounded-md transition-colors',
                                    layoutView === 'grid'
                                        ? 'bg-white shadow-sm text-brand-600'
                                        : 'text-neutral-500 hover:text-neutral-700',
                                ]"
                                title="Visualização em Grade"
                            >
                                <iconify-icon icon="tabler:layout-grid" class="text-xl block"></iconify-icon>
                            </button>
                            <button
                                @click="setLayout('list')"
                                :class="[
                                    'p-1.5 rounded-md transition-colors',
                                    layoutView === 'list'
                                        ? 'bg-white shadow-sm text-brand-600'
                                        : 'text-neutral-500 hover:text-neutral-700',
                                ]"
                                title="Visualização em Lista"
                            >
                                <iconify-icon icon="tabler:list" class="text-xl block"></iconify-icon>
                            </button>
                        </div>

                        <!-- Mobile drawer trigger CTA -->
                        <UiButton
                            variant="outline"
                            size="md"
                            class="lg:hidden shadow-sm"
                            @click="isMobileDrawerOpen = true"
                        >
                            <template #icon>
                                <iconify-icon icon="tabler:filter" class="text-base text-neutral-500"></iconify-icon>
                            </template>
                            <span>Filtrar</span>
                        </UiButton>
                    </div>
                </div>

                <!-- Removable Chip Tags -->
                <ActiveFilterChips />

                <!-- Grid catalog -->
                <VehicleGrid :vehicles="paginatedVehicles" :loading="loading" :layout="layoutView" />

                <!-- Navigation Pagination handle -->
                <div class="pt-4 flex justify-center">
                    <UiPagination v-model="currentPage" :totalPages="totalPages" :disabled="loading" />
                </div>
            </section>
        </div>

        <!-- Mobile sidebar drawer wrapper -->
        <UiDrawer :isOpen="isMobileDrawerOpen" title="Filtros de Busca" @close="isMobileDrawerOpen = false">
            <VehicleFilters />

            <template #footer>
                <UiButton variant="primary" size="md" class="w-full font-semibold" @click="isMobileDrawerOpen = false">
                    <span>Aplicar Filtros</span>
                </UiButton>
            </template>
        </UiDrawer>
    </div>
</template>
