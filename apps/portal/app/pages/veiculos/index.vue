<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { useRoute } from '#app';
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
  layout: 'default'
});

const route = useRoute();
const { t } = useI18n();
const {
  vehicles: paginatedVehicles,
  totalResults,
  pending: loading,
  currentPage,
  totalPages,
  selectedBrand,
  selectedStore
} = useVehicles();

const isMobileDrawerOpen = ref(false);

// Bind query parameters on mount or navigation changes
const syncQueryParams = () => {
  if (route.query.brand) {
    selectedBrand.value = String(route.query.brand);
  }
  if (route.query.store) {
    selectedStore.value = String(route.query.store);
  }
};

onMounted(() => {
  syncQueryParams();
});

watch(() => route.query, () => {
  syncQueryParams();
});
</script>

<template>
  <div class="container max-w-7xl mx-auto px-4 md:px-6 py-6 space-y-6">
    <!-- Breadcrumbs -->
    <Breadcrumb :items="[{ label: t('search.catalogTitle') }]" />

    <!-- Page Header details -->
    <SeoPageHeader
      :title="t('search.catalogTitle')"
      :description="t('search.catalogSubtitle')"
    />

    <!-- Main columns sheet -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
      <!-- Desktop sidebar filters -->
      <aside class="hidden lg:block lg:sticky lg:top-20 bg-white border border-neutral-200 rounded-card p-6 shadow-sm space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
          <h3 class="font-extrabold text-neutral-800 text-sm uppercase tracking-wider flex items-center gap-2">
            <iconify-icon icon="tabler:filter" class="text-neutral-400"></iconify-icon>
            <span>Filtros de Busca</span>
          </h3>
        </div>
        <VehicleFilters />
      </aside>

      <!-- Search lists viewport columns -->
      <section class="lg:col-span-3 space-y-6">
        <!-- Toolbar bar specs -->
        <div class="p-4 bg-white border border-neutral-150 rounded-card shadow-sm flex items-center justify-between gap-4">
          <!-- Counters -->
          <div class="text-xs font-bold text-neutral-500 uppercase tracking-wider">
            <span>{{ totalResults }}</span>
            <span class="text-neutral-400 font-semibold lowercase"> {{ totalResults === 1 ? 'veículo encontrado' : 'veículos encontrados' }}</span>
          </div>

          <!-- Sorting dropdown + Mobile filter action button -->
          <div class="flex items-center gap-3">
            <SortSelect />

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
        <VehicleGrid :vehicles="paginatedVehicles" :loading="loading" />

        <!-- Navigation Pagination handle -->
        <div class="pt-4 flex justify-center">
          <UiPagination
            v-model="currentPage"
            :totalPages="totalPages"
            :disabled="loading"
          />
        </div>
      </section>
    </div>

    <!-- Mobile sidebar drawer wrapper -->
    <UiDrawer
      :isOpen="isMobileDrawerOpen"
      title="Filtros de Busca"
      @close="isMobileDrawerOpen = false"
    >
      <VehicleFilters />
      
      <template #footer>
        <UiButton
          variant="primary"
          size="md"
          class="w-full font-bold"
          @click="isMobileDrawerOpen = false"
        >
          <span>Aplicar Filtros</span>
        </UiButton>
      </template>
    </UiDrawer>
  </div>
</template>
