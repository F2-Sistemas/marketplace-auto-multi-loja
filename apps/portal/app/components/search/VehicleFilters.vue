<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useVehicles } from '~/composables/useVehicles';
import { useI18n } from '~/composables/useI18n';
import UiInput from '~/components/ui/UiInput.vue';
import UiSelect from '~/components/ui/UiSelect.vue';
import UiTypeahead from '~/components/ui/UiTypeahead.vue';

const {
    searchPayload,
    search,
    selectedBrand,
    selectedStore,
    stores,
    totalResults,
    fetchBrands,
    fetchStates,
    fetchCities,
} = useVehicles();

const { t } = useI18n();

// Local active vehicle type selection (Carros / Motos switcher)
const selectedType = computed({
    get: () => searchPayload.value.filters.type || 'carros',
    set: (val: string) => {
        searchPayload.value.filters.type = val;
        searchPayload.value.pagination.page = 1;
    }
});

// Collapsible sections state (essential open by default, secondary collapsed)
const collapsedSections = ref<Record<string, boolean>>({
    applied: false,
    year: false,
    condition: false,
    brand: false,
    price: false,
    mileage: false,
    store: true,
    transmission: true,
    fuel: true,
});

const toggleSection = (section: string) => {
    collapsedSections.value[section] = !collapsedSections.value[section];
};

const isCollapsed = (section: string) => {
    return !!collapsedSections.value[section];
};

// Dynamic dropdown sources
const brandsList = ref<{ id: number; name: string }[]>([]);
const statesList = ref<{ id: number; name: string; uf: string }[]>([]);
const citiesList = ref<{ id: number; name: string; state_id: number; state?: { uf: string } }[]>([]);

onMounted(async () => {
    try {
        const [b, s, c] = await Promise.all([fetchBrands(), fetchStates(), fetchCities()]);
        brandsList.value = b || [];
        statesList.value = s || [];
        citiesList.value = c || [];
    } catch (err) {
        console.error('Erro ao carregar auxiliares de filtros:', err);
    }
});

// Brand Options
const brandOptions = computed(() => {
    return brandsList.value.map((b) => ({ label: b.name, value: String(b.id) }));
});

// Location options builder (unifying state and cities)
const locationOptions = computed(() => {
    const list: { label: string; value: string }[] = [];
    statesList.value.forEach((s) => {
        list.push({ label: `${s.name} (${s.uf})`, value: `state-${s.id}` });
    });
    citiesList.value.forEach((c) => {
        list.push({ label: `${c.name} - ${c.state?.uf || ''}`, value: `city-${c.id}` });
    });
    return list;
});

// Unified selected location v-model
const selectedLocationValue = computed({
    get: () => {
        const loc = searchPayload.value.location;
        if (loc.mode === 'city' && loc.city_id) {
            return `city-${loc.city_id}`;
        }
        if (loc.mode === 'state' && loc.state_id) {
            return `state-${loc.state_id}`;
        }
        return '';
    },
    set: (val: string) => {
        if (!val) {
            searchPayload.value.location = { mode: 'all', city_id: null, state_id: null, radius_km: null };
        } else if (val.startsWith('city-')) {
            const cityId = Number(val.split('-')[1]);
            searchPayload.value.location = {
                mode: 'city',
                city_id: cityId,
                state_id: null,
                radius_km: null,
            };
        } else if (val.startsWith('state-')) {
            const stateId = Number(val.split('-')[1]);
            searchPayload.value.location = {
                mode: 'state',
                city_id: null,
                state_id: stateId,
                radius_km: null,
            };
        }
        searchPayload.value.pagination.page = 1;
    },
});

const locationModeSelection = computed({
    get: () => {
        const loc = searchPayload.value.location;
        if (loc.mode === 'radius') {
            return `radius-${loc.radius_km || 50}`;
        }
        return loc.mode || 'all';
    },
    set: (val) => {
        if (val === 'city') {
            searchPayload.value.location.mode = 'city';
            searchPayload.value.location.radius_km = null;
        } else if (val.startsWith('radius-')) {
            const radius = Number(val.split('-')[1]);
            searchPayload.value.location.mode = 'radius';
            searchPayload.value.location.radius_km = radius;
        }
    },
});

// Year Range configuration (10 years less than current to 1 year above)
const currentYear = new Date().getFullYear();
const startYear = currentYear - 10;
const endYear = currentYear + 1;

const yearRange = computed(() => {
    const list: number[] = [];
    for (let y = endYear; y >= startYear; y--) {
        list.push(y);
    }
    return list;
});

// Dynamic year options constrained by each other
const minYearOptions = computed(() => {
    const maxVal = searchPayload.value.filters.year.max;
    const filtered = maxVal !== null
        ? yearRange.value.filter(y => y <= maxVal)
        : yearRange.value;
    return filtered.map(y => ({ label: String(y), value: String(y) }));
});

const maxYearOptions = computed(() => {
    const minVal = searchPayload.value.filters.year.min;
    const filtered = minVal !== null
        ? yearRange.value.filter(y => y >= minVal)
        : yearRange.value;
    return filtered.map(y => ({ label: String(y), value: String(y) }));
});

const yearMinModel = computed({
    get: () => searchPayload.value.filters.year.min === null ? '' : String(searchPayload.value.filters.year.min),
    set: (val) => {
        searchPayload.value.filters.year.min = val === '' ? null : Number(val);
        searchPayload.value.pagination.page = 1;
    }
});

const yearMaxModel = computed({
    get: () => searchPayload.value.filters.year.max === null ? '' : String(searchPayload.value.filters.year.max),
    set: (val) => {
        searchPayload.value.filters.year.max = val === '' ? null : Number(val);
        searchPayload.value.pagination.page = 1;
    }
});

// Price filter mask and formatters
const formatBRL = (val: string | number | null): string => {
    if (val === null || val === '') return '';
    const clean = String(val).replace(/\D/g, '');
    if (!clean) return '';
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(Number(clean));
};

const priceMinDisplay = ref(formatBRL(searchPayload.value.filters.price.min));
const priceMaxDisplay = ref(formatBRL(searchPayload.value.filters.price.max));

watch(
    () => [searchPayload.value.filters.price.min, searchPayload.value.filters.price.max],
    ([newMin, newMax]) => {
        priceMinDisplay.value = formatBRL(newMin);
        priceMaxDisplay.value = formatBRL(newMax);
    }
);

const handlePriceMinInput = (val: string) => {
    const clean = val.replace(/\D/g, '');
    const num = clean ? parseInt(clean, 10) : null;
    searchPayload.value.filters.price.min = num;
    priceMinDisplay.value = formatBRL(clean);
    searchPayload.value.pagination.page = 1;
};

const handlePriceMaxInput = (val: string) => {
    const clean = val.replace(/\D/g, '');
    const num = clean ? parseInt(clean, 10) : null;
    searchPayload.value.filters.price.max = num;
    priceMaxDisplay.value = formatBRL(clean);
    searchPayload.value.pagination.page = 1;
};


// Year toggle quick selections
const toggleYearFilter = (y: number) => {
    if (searchPayload.value.filters.year.min === y && searchPayload.value.filters.year.max === y) {
        searchPayload.value.filters.year.min = null;
        searchPayload.value.filters.year.max = null;
    } else {
        searchPayload.value.filters.year.min = y;
        searchPayload.value.filters.year.max = y;
    }
    searchPayload.value.pagination.page = 1;
};

// Price toggle quick selections
const togglePricePill = (p: number) => {
    if (searchPayload.value.filters.price.max === p) {
        searchPayload.value.filters.price.max = null;
    } else {
        searchPayload.value.filters.price.max = p;
        searchPayload.value.filters.price.min = null;
    }
    searchPayload.value.pagination.page = 1;
};

// Condition Novo / Usado
const conditionNovo = computed({
    get: () => searchPayload.value.filters.mileage.max === 0,
    set: (val) => {
        if (val) {
            searchPayload.value.filters.mileage.max = 0;
        } else {
            searchPayload.value.filters.mileage.max = null;
        }
        searchPayload.value.pagination.page = 1;
    }
});

const conditionUsado = computed({
    get: () => searchPayload.value.filters.mileage.max !== null && searchPayload.value.filters.mileage.max > 0,
    set: (val) => {
        if (val) {
            searchPayload.value.filters.mileage.max = 999999;
        } else {
            searchPayload.value.filters.mileage.max = null;
        }
        searchPayload.value.pagination.page = 1;
    }
});

const countNovas = computed(() => 0);
const countUsadas = computed(() => totalResults.value);

// Checklist bindings
const selectedFuels = computed({
    get: () => searchPayload.value.filters.fuel || [],
    set: (val) => {
        searchPayload.value.filters.fuel = val;
        searchPayload.value.pagination.page = 1;
    },
});

const selectedTransmissions = computed({
    get: () => searchPayload.value.filters.transmission || [],
    set: (val) => {
        searchPayload.value.filters.transmission = val;
        searchPayload.value.pagination.page = 1;
    },
});

// Active filters count and clear all
const activeFiltersCount = computed(() => {
    let count = 0;
    if (search.value) count++;
    if (selectedBrand.value) count++;
    if (selectedStore.value) count++;
    if (searchPayload.value.location.city_id || searchPayload.value.location.state_id) count++;
    if (searchPayload.value.filters.year.min || searchPayload.value.filters.year.max) count++;
    if (searchPayload.value.filters.price.min || searchPayload.value.filters.price.max) count++;
    if (searchPayload.value.filters.mileage.max !== null) count++;
    if (selectedTransmissions.value.length > 0) count += selectedTransmissions.value.length;
    if (selectedFuels.value.length > 0) count += selectedFuels.value.length;
    return count;
});

const clearAllFilters = () => {
    search.value = '';
    selectedBrand.value = '';
    selectedStore.value = '';
    searchPayload.value.location = { mode: 'all', city_id: null, state_id: null, radius_km: null };
    searchPayload.value.filters.brand_id = null;
    searchPayload.value.filters.year = { min: null, max: null };
    searchPayload.value.filters.price = { min: null, max: null };
    searchPayload.value.filters.mileage = { max: null };
    selectedTransmissions.value = [];
    selectedFuels.value = [];
    searchPayload.value.pagination.page = 1;
};

// Sidebar representation of active filter chips
const activeSidebarChips = computed(() => {
    const chips: { label: string; action: () => void }[] = [];
    
    if (search.value) {
        chips.push({
            label: `Busca: ${search.value}`,
            action: () => { search.value = ''; }
        });
    }
    if (selectedBrand.value) {
        const brandObj = brandsList.value.find(b => b.id === Number(selectedBrand.value));
        chips.push({
            label: brandObj ? brandObj.name : 'Marca',
            action: () => { selectedBrand.value = ''; }
        });
    }
    if (selectedStore.value) {
        const storeObj = stores.value.find(s => s.slug === selectedStore.value);
        chips.push({
            label: storeObj ? storeObj.name : 'Loja',
            action: () => { selectedStore.value = ''; }
        });
    }
    if (searchPayload.value.location.city_id) {
        const cityObj = citiesList.value.find(c => c.id === searchPayload.value.location.city_id);
        chips.push({
            label: cityObj ? cityObj.name : 'Cidade',
            action: () => {
                searchPayload.value.location.city_id = null;
                if (searchPayload.value.location.mode === 'city' || searchPayload.value.location.mode === 'radius') {
                    searchPayload.value.location.mode = 'all';
                }
            }
        });
    }
    if (searchPayload.value.location.state_id) {
        const stateObj = statesList.value.find(s => s.id === searchPayload.value.location.state_id);
        chips.push({
            label: stateObj ? stateObj.name : 'Estado',
            action: () => {
                searchPayload.value.location.state_id = null;
                if (searchPayload.value.location.mode === 'state') {
                    searchPayload.value.location.mode = 'all';
                }
            }
        });
    }
    if (searchPayload.value.filters.price.min !== null || searchPayload.value.filters.price.max !== null) {
        const min = searchPayload.value.filters.price.min;
        const max = searchPayload.value.filters.price.max;
        let label = 'Preço';
        if (min !== null && max !== null) label = `R$ ${min/1000}k - ${max/1000}k`;
        else if (min !== null) label = `>= R$ ${min/1000}k`;
        else if (max !== null) label = `Até R$ ${max/1000}k`;
        chips.push({
            label,
            action: () => { searchPayload.value.filters.price = { min: null, max: null }; }
        });
    }
    if (searchPayload.value.filters.year.min !== null || searchPayload.value.filters.year.max !== null) {
        const min = searchPayload.value.filters.year.min;
        const max = searchPayload.value.filters.year.max;
        let label = 'Ano';
        if (min !== null && max !== null) label = `Ano: ${min} - ${max}`;
        else if (min !== null) label = `Ano >= ${min}`;
        else if (max !== null) label = `Ano <= ${max}`;
        chips.push({
            label,
            action: () => { searchPayload.value.filters.year = { min: null, max: null }; }
        });
    }
    if (searchPayload.value.filters.mileage.max !== null) {
        const maxVal = searchPayload.value.filters.mileage.max;
        if (maxVal === 0) {
            chips.push({
                label: 'Novo',
                action: () => { searchPayload.value.filters.mileage.max = null; }
            });
        } else {
            chips.push({
                label: `Km até ${maxVal.toLocaleString('pt-BR')}`,
                action: () => { searchPayload.value.filters.mileage.max = null; }
            });
        }
    }
    selectedTransmissions.value.forEach(tOpt => {
        chips.push({
            label: tOpt === 'automatic' ? 'Automático' : 'Manual',
            action: () => {
                selectedTransmissions.value = selectedTransmissions.value.filter(x => x !== tOpt);
            }
        });
    });
    selectedFuels.value.forEach(fOpt => {
        const labelMap: Record<string, string> = {
            flex: 'Flex', gasoline: 'Gasolina', ethanol: 'Etanol', diesel: 'Diesel', electric: 'Elétrico', hybrid: 'Híbrido'
        };
        chips.push({
            label: labelMap[fOpt] || fOpt,
            action: () => {
                selectedFuels.value = selectedFuels.value.filter(x => x !== fOpt);
            }
        });
    });
    
    return chips;
});
</script>

<template>
    <div class="space-y-5">
        
        <!-- Tab selector switcher (Carros / Motos) -->
        <div class="flex p-1 bg-neutral-100 rounded-full border border-neutral-200">
            <button
                type="button"
                @click="selectedType = 'carros'"
                :class="[
                    'flex-1 flex items-center justify-center space-x-2 py-2 px-3 rounded-full text-sm font-semibold transition-all duration-200',
                    selectedType === 'carros' ? 'bg-white text-neutral-900 shadow-sm' : 'text-neutral-500 hover:text-neutral-700'
                ]"
            >
                <iconify-icon icon="fa7-solid:car" class="w-4 h-4"></iconify-icon>
                <span>Carros</span>
            </button>
            <button
                type="button"
                @click="selectedType = 'motos'"
                :class="[
                    'flex-1 flex items-center justify-center space-x-2 py-2 px-3 rounded-full text-sm font-semibold transition-all duration-200',
                    selectedType === 'motos' ? 'bg-white text-neutral-900 shadow-sm' : 'text-neutral-500 hover:text-neutral-700'
                ]"
            >
                <iconify-icon icon="fa7-solid:motorcycle" class="w-4 h-4"></iconify-icon>
                <span>Motos</span>
            </button>
        </div>

        <!-- 1. Search Query Field -->
        <div class="space-y-2 border-b border-neutral-150 pb-5">
            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-500">
                O que você procura?
            </label>
            <div class="relative">
                <UiInput v-model="search" type="text" :placeholder="t('search.placeholder')" id="search-filter-input" />
            </div>
        </div>

        <!-- 2. Location (Digite sua cidade ou estado) -->
        <div class="border-b border-neutral-150 pb-5">
            <div class="space-y-3">
                <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-500">
                    Onde
                </label>
                <UiTypeahead
                    v-model="selectedLocationValue"
                    :initial-options="locationOptions"
                    placeholder="Digite sua cidade ou estado"
                />
                
                <!-- Radius Selector (Visible only when a city is selected) -->
                <div v-if="searchPayload.location.mode === 'city' || searchPayload.location.mode === 'radius'" class="space-y-2">
                    <label class="block text-xs font-semibold text-neutral-500 uppercase tracking-wider">Raio de Busca</label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="rOpt in [
                                { label: 'Somente a cidade', value: 'city' },
                                { label: '+50 km', value: 'radius-50' },
                                { label: '+100 km', value: 'radius-100' },
                                { label: '+150 km', value: 'radius-150' },
                            ]"
                            :key="rOpt.value"
                            type="button"
                            @click="locationModeSelection = rOpt.value"
                            :class="[
                                'px-3 py-1.5 rounded-full text-xs font-medium border transition-all duration-200',
                                locationModeSelection === rOpt.value
                                    ? 'bg-neutral-900 border-neutral-900 text-white shadow-sm'
                                    : 'bg-white border-neutral-200 text-neutral-600 hover:border-neutral-300'
                            ]"
                        >
                            {{ rOpt.label }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Filtros Aplicados Section -->
        <div v-if="activeFiltersCount > 0" class="border-b border-neutral-150 pb-5">
            <div class="flex items-center justify-between">
                <button
                    type="button"
                    @click="toggleSection('applied')"
                    class="flex items-center space-x-2 font-semibold text-neutral-800 focus:outline-none"
                >
                    <span>Filtros aplicados</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-brand-500 rounded-full min-w-5 h-5">
                        {{ activeFiltersCount }}
                    </span>
                    <iconify-icon
                        :icon="isCollapsed('applied') ? 'fa7-solid:chevron-down' : 'fa7-solid:chevron-up'"
                        class="h-3 w-3 text-neutral-400"
                    ></iconify-icon>
                </button>
                <button
                    type="button"
                    @click="clearAllFilters"
                    class="text-xs text-brand-600 font-semibold hover:underline"
                >
                    Limpar todos
                </button>
            </div>
            
            <div v-show="!isCollapsed('applied')" class="mt-3 flex flex-wrap gap-2 transition-all duration-205">
                <button
                    v-for="(chip, idx) in activeSidebarChips"
                    :key="idx"
                    type="button"
                    @click="chip.action"
                    class="inline-flex items-center space-x-1 px-2.5 py-1 text-xs font-medium bg-neutral-50 border border-neutral-200 text-neutral-700 rounded-md hover:bg-neutral-100 transition"
                >
                    <span>{{ chip.label }}</span>
                    <iconify-icon icon="fa7-solid:xmark" class="w-3 h-3 text-neutral-450"></iconify-icon>
                </button>
            </div>
        </div>

        <!-- 4. Ano Section (Collapsible) -->
        <div class="border-b border-neutral-150 pb-5">
            <button
                type="button"
                @click="toggleSection('year')"
                class="flex w-full items-center justify-between font-semibold text-neutral-800 focus:outline-none"
            >
                <span>Ano</span>
                <iconify-icon
                    :icon="isCollapsed('year') ? 'fa7-solid:chevron-down' : 'fa7-solid:chevron-up'"
                    class="h-3 w-3 text-neutral-400"
                ></iconify-icon>
            </button>
            <div v-show="!isCollapsed('year')" class="mt-3 space-y-3 transition-all duration-205">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-2xs text-neutral-500 mb-1">Mínimo</label>
                        <UiSelect
                            v-model="yearMinModel"
                            :options="minYearOptions"
                            class="text-sm"
                        >
                            <option value="">De</option>
                        </UiSelect>
                    </div>
                    <div>
                        <label class="block text-2xs text-neutral-500 mb-1">Máximo</label>
                        <UiSelect
                            v-model="yearMaxModel"
                            :options="maxYearOptions"
                            class="text-sm"
                        >
                            <option value="">Até</option>
                        </UiSelect>
                    </div>
                </div>
                <div class="flex flex-wrap gap-1.5 pt-1">
                    <button
                        v-for="y in yearRange.filter(val => val <= currentYear).slice(0, 10)"
                        :key="y"
                        type="button"
                        @click="toggleYearFilter(y)"
                        :class="[
                            'px-2.5 py-1 rounded-md text-xs border transition-all duration-200',
                            searchPayload.filters.year.min === y && searchPayload.filters.year.max === y
                                ? 'bg-neutral-900 border-neutral-900 text-white font-semibold shadow-sm'
                                : 'bg-neutral-50 border-neutral-200 text-neutral-600 hover:border-neutral-350 hover:bg-neutral-100'
                        ]"
                    >
                        {{ y }}
                    </button>
                </div>
            </div>
        </div>

        <!-- 5. Condition Filter (Novo/Usado Checkbox group) -->
        <div class="border-b border-neutral-150 pb-5">
            <button
                type="button"
                @click="toggleSection('condition')"
                class="flex w-full items-center justify-between font-semibold text-neutral-800 focus:outline-none"
            >
                <span>Condição</span>
                <iconify-icon
                    :icon="isCollapsed('condition') ? 'fa7-solid:chevron-down' : 'fa7-solid:chevron-up'"
                    class="h-3 w-3 text-neutral-400"
                ></iconify-icon>
            </button>
            <div v-show="!isCollapsed('condition')" class="mt-3 space-y-2.5 transition-all duration-205">
                <label class="flex items-center space-x-2 text-sm text-neutral-700 cursor-pointer">
                    <input
                        v-model="conditionNovo"
                        type="checkbox"
                        class="rounded border-neutral-300 text-brand-600 focus:ring-brand-500"
                    />
                    <span class="flex-1">Novas</span>
                    <span class="text-xs text-neutral-400">({{ countNovas }})</span>
                </label>
                <label class="flex items-center space-x-2 text-sm text-neutral-700 cursor-pointer">
                    <input
                        v-model="conditionUsado"
                        type="checkbox"
                        class="rounded border-neutral-300 text-brand-600 focus:ring-brand-500"
                    />
                    <span class="flex-1">Usadas</span>
                    <span class="text-xs text-neutral-400">({{ countUsadas }})</span>
                </label>
            </div>
        </div>

        <!-- 6. Marca & Modelo Section (Collapsible) -->
        <div class="border-b border-neutral-150 pb-5">
            <button
                type="button"
                @click="toggleSection('brand')"
                class="flex w-full items-center justify-between font-semibold text-neutral-800 focus:outline-none"
            >
                <span>Marca e Modelo</span>
                <iconify-icon
                    :icon="isCollapsed('brand') ? 'fa7-solid:chevron-down' : 'fa7-solid:chevron-up'"
                    class="h-3 w-3 text-neutral-400"
                ></iconify-icon>
            </button>
            <div v-show="!isCollapsed('brand')" class="mt-3 space-y-3 transition-all duration-205">
                <div>
                    <label class="block text-2xs text-neutral-500 mb-1">Marca</label>
                    <UiTypeahead
                        v-model="selectedBrand"
                        :initial-options="brandOptions"
                        placeholder="Todos os fabricantes"
                    />
                </div>
                
                <!-- Todos os Modelos visual row -->
                <div class="flex items-center justify-between py-2 border-t border-neutral-100 mt-2 text-sm text-neutral-600 cursor-pointer hover:text-neutral-900 transition">
                    <span>Todos os modelos</span>
                    <iconify-icon icon="fa7-solid:chevron-down" class="h-3 w-3 text-neutral-400"></iconify-icon>
                </div>

                <!-- Add vehicle model mock CTA button -->
                <button type="button" class="text-xs text-brand-600 font-semibold hover:underline flex items-center space-x-1 mt-1">
                    <iconify-icon icon="fa7-solid:plus" class="w-3 h-3"></iconify-icon>
                    <span>Adicionar mais um veículo</span>
                </button>
            </div>
        </div>

        <!-- 7. Preço Section (Collapsible) -->
        <div class="border-b border-neutral-150 pb-5">
            <button
                type="button"
                @click="toggleSection('price')"
                class="flex w-full items-center justify-between font-semibold text-neutral-800 focus:outline-none"
            >
                <span>Preço</span>
                <iconify-icon
                    :icon="isCollapsed('price') ? 'fa7-solid:chevron-down' : 'fa7-solid:chevron-up'"
                    class="h-3 w-3 text-neutral-400"
                ></iconify-icon>
            </button>
            <div v-show="!isCollapsed('price')" class="mt-3 space-y-3 transition-all duration-205">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-2xs text-neutral-500 mb-1">Mínimo</label>
                        <UiInput
                            :model-value="priceMinDisplay"
                            type="text"
                            placeholder="Mínimo (R$)"
                            class="text-sm"
                            @update:model-value="handlePriceMinInput"
                        />
                    </div>
                    <div>
                        <label class="block text-2xs text-neutral-500 mb-1">Máximo</label>
                        <UiInput
                            :model-value="priceMaxDisplay"
                            type="text"
                            placeholder="Máximo (R$)"
                            class="text-sm"
                            @update:model-value="handlePriceMaxInput"
                        />
                    </div>
                </div>
                
                <!-- Quick Selection Price Pills -->
                <div class="flex flex-wrap gap-1.5 pt-1">
                    <button
                        v-for="p in [60000, 70000, 80000, 100000, 150000, 200000]"
                        :key="p"
                        type="button"
                        @click="togglePricePill(p)"
                        :class="[
                            'px-2.5 py-1 rounded-md text-xs border transition-all duration-200',
                            searchPayload.filters.price.max === p
                                ? 'bg-neutral-900 border-neutral-900 text-white font-semibold shadow-sm'
                                : 'bg-neutral-50 border-neutral-200 text-neutral-600 hover:border-neutral-350 hover:bg-neutral-100'
                        ]"
                    >
                        Até {{ p/1000 }} mil
                    </button>
                </div>
            </div>
        </div>

        <!-- 8. Quilometragem Section (Collapsible) -->
        <div class="border-b border-neutral-150 pb-5">
            <button
                type="button"
                @click="toggleSection('mileage')"
                class="flex w-full items-center justify-between font-semibold text-neutral-800 focus:outline-none"
            >
                <span>Quilometragem</span>
                <iconify-icon
                    :icon="isCollapsed('mileage') ? 'fa7-solid:chevron-down' : 'fa7-solid:chevron-up'"
                    class="h-3 w-3 text-neutral-400"
                ></iconify-icon>
            </button>
            <div v-show="!isCollapsed('mileage')" class="mt-3 space-y-3 transition-all duration-205">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-2xs text-neutral-500 mb-1">Km Mínimo</label>
                        <UiInput
                            type="number"
                            placeholder="De"
                            class="text-sm bg-neutral-50 opacity-60 cursor-not-allowed"
                            disabled
                        />
                    </div>
                    <div>
                        <label class="block text-2xs text-neutral-500 mb-1">Km Máximo</label>
                        <UiInput
                            v-model.number="searchPayload.filters.mileage.max"
                            type="number"
                            placeholder="Até"
                            class="text-sm"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- 9. Lojas Section (Collapsible) -->
        <div class="border-b border-neutral-150 pb-5">
            <button
                type="button"
                @click="toggleSection('store')"
                class="flex w-full items-center justify-between font-semibold text-neutral-800 focus:outline-none"
            >
                <span>Lojas</span>
                <iconify-icon
                    :icon="isCollapsed('store') ? 'fa7-solid:chevron-down' : 'fa7-solid:chevron-up'"
                    class="h-3 w-3 text-neutral-400"
                ></iconify-icon>
            </button>
            <div v-show="!isCollapsed('store')" class="mt-3 space-y-3 transition-all duration-205">
                <UiSelect
                    v-model="selectedStore"
                    :options="stores.map((s) => ({ label: s.name, value: s.slug }))"
                    id="store-filter-select"
                >
                    <option value="">{{ t('search.allStores') }}</option>
                </UiSelect>
            </div>
        </div>

        <!-- 10. Câmbio Section (Collapsible) -->
        <div class="border-b border-neutral-150 pb-5">
            <button
                type="button"
                @click="toggleSection('transmission')"
                class="flex w-full items-center justify-between font-semibold text-neutral-800 focus:outline-none"
            >
                <span>Câmbio</span>
                <iconify-icon
                    :icon="isCollapsed('transmission') ? 'fa7-solid:chevron-down' : 'fa7-solid:chevron-up'"
                    class="h-3 w-3 text-neutral-400"
                ></iconify-icon>
            </button>
            <div v-show="!isCollapsed('transmission')" class="mt-3 space-y-2.5 transition-all duration-205">
                <label
                    v-for="tOpt in [
                        { label: 'Automático', value: 'automatic' },
                        { label: 'Manual', value: 'manual' },
                    ]"
                    :key="tOpt.value"
                    class="flex items-center space-x-2 text-sm text-neutral-700 cursor-pointer"
                >
                    <input
                        v-model="selectedTransmissions"
                        type="checkbox"
                        :value="tOpt.value"
                        class="rounded border-neutral-300 text-brand-600 focus:ring-brand-500"
                    />
                    <span>{{ tOpt.label }}</span>
                </label>
            </div>
        </div>

        <!-- 11. Combustível Section (Collapsible) -->
        <div class="pb-2">
            <button
                type="button"
                @click="toggleSection('fuel')"
                class="flex w-full items-center justify-between font-semibold text-neutral-800 focus:outline-none"
            >
                <span>Combustível</span>
                <iconify-icon
                    :icon="isCollapsed('fuel') ? 'fa7-solid:chevron-down' : 'fa7-solid:chevron-up'"
                    class="h-3 w-3 text-neutral-400"
                ></iconify-icon>
            </button>
            <div v-show="!isCollapsed('fuel')" class="mt-3 space-y-2.5 transition-all duration-205">
                <label
                    v-for="f in ['flex', 'gasoline', 'ethanol', 'diesel', 'electric', 'hybrid']"
                    :key="f"
                    class="flex items-center space-x-2 text-sm text-neutral-700 cursor-pointer"
                >
                    <input
                        v-model="selectedFuels"
                        type="checkbox"
                        :value="f"
                        class="rounded border-neutral-300 text-brand-600 focus:ring-brand-500"
                    />
                    <span class="capitalize">
                        {{
                            f === 'flex'
                                ? 'Flex'
                                : f === 'gasoline'
                                  ? 'Gasolina'
                                  : f === 'ethanol'
                                    ? 'Etanol'
                                    : f === 'diesel'
                                      ? 'Diesel'
                                      : f === 'electric'
                                        ? 'Elétrico'
                                        : 'Híbrido'
                        }}
                    </span>
                </label>
            </div>
        </div>

    </div>
</template>
