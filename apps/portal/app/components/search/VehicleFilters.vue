<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useVehicles } from '~/composables/useVehicles';
import { useI18n } from '~/composables/useI18n';
import UiInput from '~/components/ui/UiInput.vue';
import UiSelect from '~/components/ui/UiSelect.vue';

const {
  searchPayload,
  search,
  selectedBrand,
  selectedStore,
  maxPrice,
  stores,
  fetchBrands,
  fetchStates,
  fetchCities
} = useVehicles();

const { t } = useI18n();

// Dynamic dropdown sources
const brandsList = ref<{ id: number; name: string }[]>([]);
const statesList = ref<{ id: number; name: string; uf: string }[]>([]);
const citiesList = ref<{ id: number; name: string; state_id: number; state?: { uf: string } }[]>([]);

// Location helper selections
const locationModeSelection = ref('all'); // all, city, radius-50, radius-100, radius-150, state
const selectedCityId = ref<string | number>('');
const selectedStateId = ref<string | number>('');

// Checklist filters
const selectedFuels = ref<string[]>([]);
const selectedTransmissions = ref<string[]>([]);

onMounted(async () => {
  try {
    const [b, s, c] = await Promise.all([
      fetchBrands(),
      fetchStates(),
      fetchCities()
    ]);
    brandsList.value = b || [];
    statesList.value = s || [];
    citiesList.value = c || [];
  } catch (err) {
    console.error('Erro ao carregar auxiliares de filtros:', err);
  }
});

// Watch location mode and choices to update searchPayload.location dynamically
watch([locationModeSelection, selectedCityId, selectedStateId], () => {
  const mode = locationModeSelection.value;
  
  if (mode === 'all') {
    searchPayload.value.location = {
      mode: 'all',
      city_id: null,
      state_id: null,
      radius_km: null
    };
  } else if (mode === 'city') {
    searchPayload.value.location = {
      mode: 'city',
      city_id: selectedCityId.value ? Number(selectedCityId.value) : null,
      state_id: null,
      radius_km: null
    };
  } else if (mode.startsWith('radius-')) {
    const radius = Number(mode.split('-')[1]);
    searchPayload.value.location = {
      mode: 'radius',
      city_id: selectedCityId.value ? Number(selectedCityId.value) : null,
      state_id: null,
      radius_km: radius
    };
  } else if (mode === 'state') {
    searchPayload.value.location = {
      mode: 'state',
      city_id: null,
      state_id: selectedStateId.value ? Number(selectedStateId.value) : null,
      radius_km: null
    };
  }
  searchPayload.value.pagination.page = 1;
});

// Sync selected arrays to searchPayload.filters
watch([selectedFuels, selectedTransmissions], () => {
  searchPayload.value.filters.fuel = selectedFuels.value;
  searchPayload.value.filters.transmission = selectedTransmissions.value;
  searchPayload.value.pagination.page = 1;
});

const formatPrice = (value: number) => {
  return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 });
};

// UI dropdown mappings
const brandOptions = computed(() => [
  { label: t('search.allBrands'), value: '' },
  ...brandsList.value.map(b => ({ label: b.name, value: b.id }))
]);

const stateOptions = computed(() => [
  { label: 'Todos os Estados', value: '' },
  ...statesList.value.map(s => ({ label: `${s.name} (${s.uf})`, value: s.id }))
]);

const cityOptions = computed(() => [
  { label: 'Selecione uma Cidade', value: '' },
  ...citiesList.value.map(c => ({ label: `${c.name} - ${c.state?.uf || ''}`, value: c.id }))
]);

const locationModeOptions = computed(() => [
  { label: 'Todo o Brasil', value: 'all' },
  { label: 'Somente a cidade', value: 'city' },
  { label: 'Até 50 km', value: 'radius-50' },
  { label: 'Até 100 km', value: 'radius-100' },
  { label: 'Até 150 km', value: 'radius-150' },
  { label: 'Todo o estado', value: 'state' }
]);
</script>

<template>
  <div class="space-y-6">
    <!-- Search Query Field -->
    <div class="space-y-2">
      <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500">
        {{ t('search.placeholder').split('...')[0] }}
      </label>
      <div class="relative">
        <UiInput
          v-model="search"
          type="text"
          :placeholder="t('search.placeholder')"
          id="search-filter-input"
        />
      </div>
    </div>

    <!-- Brands Select Dropdown -->
    <div class="space-y-2">
      <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500">
        {{ t('search.allBrands').split(' ')[2] || 'Marca' }}
      </label>
      <UiSelect
        v-model="selectedBrand"
        :options="brandOptions"
        id="brand-filter-select"
      />
    </div>

    <!-- Stores Select Dropdown -->
    <div class="space-y-2">
      <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500">
        {{ t('search.allStores').split(' ')[2] || 'Loja' }}
      </label>
      <UiSelect
        v-model="selectedStore"
        :options="stores.map(s => ({ label: s.name, value: s.slug }))"
        id="store-filter-select"
      >
        <option value="">{{ t('search.allStores') }}</option>
      </UiSelect>
    </div>

    <!-- Location Mode Selector -->
    <div class="space-y-2">
      <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500">
        Região de Busca
      </label>
      <UiSelect
        v-model="locationModeSelection"
        :options="locationModeOptions"
        id="location-mode-select"
      />
    </div>

    <!-- State Selector (Visible when mode is 'state') -->
    <div v-if="locationModeSelection === 'state'" class="space-y-2 transition-all">
      <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500">
        Estado
      </label>
      <UiSelect
        v-model="selectedStateId"
        :options="stateOptions"
        id="state-filter-select"
      />
    </div>

    <!-- City Selector (Visible when mode is city or radius) -->
    <div v-slot:default v-if="locationModeSelection === 'city' || locationModeSelection.startsWith('radius-')" class="space-y-2 transition-all">
      <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500">
        Cidade de Referência
      </label>
      <UiSelect
        v-model="selectedCityId"
        :options="cityOptions"
        id="city-filter-select"
      />
    </div>

    <!-- Fuels Checklist -->
    <div class="space-y-2">
      <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500">
        Combustível
      </label>
      <div class="space-y-2 bg-white p-3 rounded-lg border border-neutral-200">
        <label v-for="f in ['flex', 'gasoline', 'ethanol', 'diesel', 'electric', 'hybrid']" :key="f" class="flex items-center space-x-2 text-sm text-neutral-700 cursor-pointer">
          <input
            v-model="selectedFuels"
            type="checkbox"
            :value="f"
            class="rounded border-neutral-300 text-brand-600 focus:ring-brand-500"
          />
          <span class="capitalize">{{ f === 'flex' ? 'Flex' : f === 'gasoline' ? 'Gasolina' : f === 'ethanol' ? 'Etanol' : f === 'diesel' ? 'Diesel' : f === 'electric' ? 'Elétrico' : 'Híbrido' }}</span>
        </label>
      </div>
    </div>

    <!-- Transmission Checkboxes (Checklist) -->
    <div class="space-y-2">
      <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500">
        Câmbio
      </label>
      <div class="space-y-2 bg-white p-3 rounded-lg border border-neutral-200">
        <label v-for="tOpt in [{ label: 'Automático', value: 'automatic' }, { label: 'Manual', value: 'manual' }]" :key="tOpt.value" class="flex items-center space-x-2 text-sm text-neutral-700 cursor-pointer">
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

    <!-- Price Range Filter -->
    <div class="space-y-3">
      <div class="flex justify-between items-center text-xs font-bold uppercase tracking-wider text-neutral-500">
        <span>{{ t('search.priceMaxLabel') }}</span>
        <span class="text-brand-600 font-extrabold text-sm normal-case">{{ formatPrice(maxPrice) }}</span>
      </div>
      <input
        v-model.number="maxPrice"
        type="range"
        min="50000"
        max="250000"
        step="5000"
        class="w-full h-1 bg-neutral-200 rounded-lg appearance-none cursor-pointer accent-brand-500 focus:outline-none"
      />
    </div>
  </div>
</template>
