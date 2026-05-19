<script setup lang="ts">
import { computed } from 'vue';
import { useVehicles } from '~/composables/useVehicles';
import { useI18n } from '~/composables/useI18n';

const {
  search,
  selectedBrand,
  selectedStore,
  maxPrice,
  selectedTransmission,
  resetFilters,
  stores
} = useVehicles();

const { t } = useI18n();

const activeChips = computed(() => {
  const list: { key: string; label: string; action: () => void }[] = [];
  
  if (search.value) {
    list.push({
      key: 'search',
      label: `"${search.value}"`,
      action: () => { search.value = ''; }
    });
  }
  
  if (selectedBrand.value) {
    list.push({
      key: 'brand',
      label: selectedBrand.value,
      action: () => { selectedBrand.value = ''; }
    });
  }
  
  if (selectedStore.value) {
    const storeObj = stores.value.find(s => s.slug === selectedStore.value);
    list.push({
      key: 'store',
      label: storeObj ? storeObj.name : selectedStore.value,
      action: () => { selectedStore.value = ''; }
    });
  }
  
  if (selectedTransmission.value) {
    list.push({
      key: 'transmission',
      label: selectedTransmission.value === 'automatico' ? t('search.transmissionAuto') : t('search.transmissionManual'),
      action: () => { selectedTransmission.value = ''; }
    });
  }
  
  if (maxPrice.value < 250000) {
    const priceFormatted = maxPrice.value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 });
    list.push({
      key: 'maxPrice',
      label: `Até ${priceFormatted}`,
      action: () => { maxPrice.value = 250000; }
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
      class="inline-flex items-center gap-1.5 px-3 py-1 bg-brand-50 border border-brand-100 text-brand-700 text-xs font-semibold rounded-full select-none"
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
      class="text-xs text-neutral-400 hover:text-brand-600 font-bold transition-colors cursor-pointer px-2.5 py-1"
    >
      {{ t('search.clearFilters') }}
    </button>
  </div>
</template>
