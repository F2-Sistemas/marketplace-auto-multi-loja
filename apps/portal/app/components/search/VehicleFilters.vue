<script setup lang="ts">
import { useVehicles } from '~/composables/useVehicles';
import { useI18n } from '~/composables/useI18n';
import UiInput from '~/components/ui/UiInput.vue';
import UiSelect from '~/components/ui/UiSelect.vue';

const {
  search,
  selectedBrand,
  selectedStore,
  maxPrice,
  selectedTransmission,
  brands,
  stores
} = useVehicles();

const { t } = useI18n();

const formatPrice = (value: number) => {
  return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 });
};
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
        :options="brands"
        id="brand-filter-select"
      >
        <option value="">{{ t('search.allBrands') }}</option>
      </UiSelect>
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

    <!-- Transmission Choices Dropdown -->
    <div class="space-y-2">
      <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500">
        {{ t('vehicle.specs.transmission') }}
      </label>
      <UiSelect
        v-model="selectedTransmission"
        :options="[
          { label: t('search.transmissionAuto'), value: 'automatico' },
          { label: t('search.transmissionManual'), value: 'manual' }
        ]"
        id="transmission-filter-select"
      >
        <option value="">{{ t('search.transmissionLabel') }}</option>
      </UiSelect>
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
