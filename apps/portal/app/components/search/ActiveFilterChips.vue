<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useVehicles } from '~/composables/useVehicles';
import { useI18n } from '~/composables/useI18n';

const {
    searchPayload,
    search,
    selectedBrand,
    selectedStore,
    maxPrice,
    resetFilters,
    stores,
    fetchBrands,
    fetchStates,
    fetchCities,
} = useVehicles();

const { t } = useI18n();

const brandsList = ref<{ id: number; name: string }[]>([]);
const statesList = ref<{ id: number; name: string; uf: string }[]>([]);
const citiesList = ref<{ id: number; name: string }[]>([]);

onMounted(async () => {
    try {
        const [b, s, c] = await Promise.all([fetchBrands(), fetchStates(), fetchCities()]);
        brandsList.value = b || [];
        statesList.value = s || [];
        citiesList.value = c || [];
    } catch (err) {}
});

const activeChips = computed(() => {
    const list: { key: string; label: string; action: () => void }[] = [];

    if (search.value) {
        list.push({
            key: 'search',
            label: `"${search.value}"`,
            action: () => {
                search.value = '';
            },
        });
    }

    if (selectedBrand.value) {
        const brandObj = brandsList.value.find((b) => String(b.id) === selectedBrand.value);
        list.push({
            key: 'brand',
            label: brandObj ? brandObj.name : selectedBrand.value,
            action: () => {
                selectedBrand.value = '';
            },
        });
    }

    if (selectedStore.value) {
        const storeObj = stores.value.find((s) => s.slug === selectedStore.value);
        list.push({
            key: 'store',
            label: storeObj ? storeObj.name : selectedStore.value,
            action: () => {
                selectedStore.value = '';
            },
        });
    }

    // Fuel Array Chips
    if (searchPayload.value.filters.fuel && searchPayload.value.filters.fuel.length > 0) {
        const fuelLabels: Record<string, string> = {
            flex: 'Flex',
            gasoline: 'Gasolina',
            ethanol: 'Etanol',
            diesel: 'Diesel',
            electric: 'Elétrico',
            hybrid: 'Híbrido',
        };
        searchPayload.value.filters.fuel.forEach((f) => {
            list.push({
                key: `fuel-${f}`,
                label: fuelLabels[f] || f,
                action: () => {
                    searchPayload.value.filters.fuel = searchPayload.value.filters.fuel.filter((item) => item !== f);
                },
            });
        });
    }

    // Transmission Array Chips
    if (searchPayload.value.filters.transmission && searchPayload.value.filters.transmission.length > 0) {
        searchPayload.value.filters.transmission.forEach((tOpt) => {
            const isAuto = tOpt === 'automatic' || tOpt === 'automatico';
            list.push({
                key: `trans-${tOpt}`,
                label: isAuto ? t('search.transmissionAuto') : t('search.transmissionManual'),
                action: () => {
                    searchPayload.value.filters.transmission = searchPayload.value.filters.transmission.filter(
                        (item) => item !== tOpt
                    );
                },
            });
        });
    }

    if (maxPrice.value < 250000) {
        const priceFormatted = maxPrice.value.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
            maximumFractionDigits: 0,
        });
        list.push({
            key: 'maxPrice',
            label: `Até ${priceFormatted}`,
            action: () => {
                maxPrice.value = 250000;
            },
        });
    }

    // Location chips
    const loc = searchPayload.value.location;
    if (loc.mode === 'city' && loc.city_id) {
        const cityObj = citiesList.value.find((c) => c.id === loc.city_id);
        list.push({
            key: 'location-city',
            label: cityObj ? `Cidade: ${cityObj.name}` : `Cidade #${loc.city_id}`,
            action: () => {
                searchPayload.value.location = { mode: 'all', city_id: null, state_id: null, radius_km: null };
            },
        });
    } else if (loc.mode === 'radius' && loc.city_id) {
        const cityObj = citiesList.value.find((c) => c.id === loc.city_id);
        list.push({
            key: 'location-radius',
            label: cityObj ? `Até ${loc.radius_km} km de ${cityObj.name}` : `Até ${loc.radius_km} km`,
            action: () => {
                searchPayload.value.location = { mode: 'all', city_id: null, state_id: null, radius_km: null };
            },
        });
    } else if (loc.mode === 'state' && loc.state_id) {
        const stateObj = statesList.value.find((s) => s.id === loc.state_id);
        list.push({
            key: 'location-state',
            label: stateObj ? `Estado: ${stateObj.name || stateObj.uf}` : `Estado #${loc.state_id}`,
            action: () => {
                searchPayload.value.location = { mode: 'all', city_id: null, state_id: null, radius_km: null };
            },
        });
    }

    return list;
});
</script>

<template>
    <div v-if="activeChips.length > 0" class="flex flex-wrap items-center gap-2">
        <div
            v-for="chip in activeChips"
            :key="chip.key"
            class="inline-flex items-center gap-1.5 px-3 py-1 bg-brand-50 border border-brand-100 text-brand-700 text-xs font-normal rounded-full select-none"
        >
            <span>{{ chip.label }}</span>
            <button
                @click="chip.action"
                class="w-4 h-4 rounded-full flex items-center justify-center hover:bg-brand-100 text-brand-500 hover:text-brand-800 transition-colors cursor-pointer"
                :aria-label="'Remover filtro ' + chip.label"
            >
                <iconify-icon icon="tabler:x" class="text-xs"></iconify-icon>
            </button>
        </div>

        <!-- Limpar Tudo -->
        <button
            @click="resetFilters"
            class="text-xs text-neutral-400 hover:text-brand-600 font-semibold transition-colors cursor-pointer px-2.5 py-1"
        >
            {{ t('search.clearFilters') }}
        </button>
    </div>
</template>
