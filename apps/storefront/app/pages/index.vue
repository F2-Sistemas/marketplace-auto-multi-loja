<script setup lang="ts">
import { useTenant } from '~/composables/useTenant';
import { useI18n } from '~/composables/useI18n';
import VehicleFilters from '~/components/vehicle/VehicleFilters.vue';
import VehicleGrid from '~/components/vehicle/VehicleGrid.vue';
import LeadForm from '~/components/lead/LeadForm.vue';
import UiBadge from '~/components/ui/UiBadge.vue';

const {
  currentStore,
  searchQuery,
  filteredVehicles,
  selectedVehicle,
  openVehicleDetails,
  closeDetails,
  formatPrice
} = useTenant();

const { t } = useI18n();
</script>

<template>
  <div>
    <!-- Hero Banner -->
    <section class="relative py-20 md:py-28 overflow-hidden bg-slate-950">
      <!-- Background Gradients -->
      <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-900/60 to-slate-950"></div>
      <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-gradient-to-br from-store-primary-from/20 to-transparent blur-3xl"></div>
      <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-gradient-to-br from-store-accent-color/10 to-transparent blur-3xl"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900/80 border border-slate-800 text-xs font-semibold text-slate-300 mb-6 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          Estoque Atualizado Hoje
        </div>
        
        <h2 class="text-4xl md:text-6xl font-black text-slate-100 tracking-tight leading-none mb-6">
          Encontre seu <span class="bg-gradient-to-r bg-clip-text text-transparent store-text-gradient">Próximo Carro</span>
        </h2>
        
        <p class="text-base md:text-lg text-slate-400 max-w-2xl mx-auto leading-relaxed mb-10">
          {{ currentStore.tagline }}. {{ t('inventory.subtitle') }}.
        </p>

        <!-- Filters Box -->
        <VehicleFilters v-model="searchQuery" />
      </div>
    </section>

    <!-- Main Stock Section -->
    <section class="py-12 bg-slate-950 border-t border-slate-900/40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Stock Header Info -->
        <div class="flex justify-between items-center mb-8 border-b border-slate-900 pb-4">
          <div>
            <h3 class="text-xl font-bold text-slate-200">{{ t('inventory.title') }}</h3>
          </div>
          <span class="text-xs font-bold text-slate-500">
            {{ filteredVehicles.length }} {{ t('inventory.found') }}
          </span>
        </div>

        <!-- Vehicle Grid -->
        <VehicleGrid
          :vehicles="filteredVehicles"
          @select-vehicle="openVehicleDetails"
        />
      </div>
    </section>

    <!-- Lead Proposals Modal (Floating Drawer) -->
    <div v-if="selectedVehicle" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-3xl w-full overflow-hidden shadow-2xl relative text-left">
        <!-- Close Button -->
        <button
          @click="closeDetails"
          class="absolute top-4 right-4 z-10 w-9 h-9 flex items-center justify-center rounded-full bg-slate-950/60 hover:bg-slate-950 text-slate-400 hover:text-white transition duration-200 cursor-pointer border border-slate-850"
        >
          <iconify-icon icon="tabler:x" class="text-xl block"></iconify-icon>
        </button>

        <div class="grid grid-cols-1 md:grid-cols-2">
          <!-- Car Preview Info -->
          <div class="relative bg-slate-950 min-h-[260px] md:min-h-full">
            <img
              :src="selectedVehicle.image"
              :alt="`${selectedVehicle.brand} ${selectedVehicle.model}`"
              class="w-full h-full object-cover min-h-[260px] md:h-full"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent flex flex-col justify-end p-6">
              <span class="text-xs font-semibold text-slate-450 uppercase tracking-widest">{{ selectedVehicle.brand }}</span>
              <h4 class="text-2xl font-black text-slate-100 mt-1 leading-tight">{{ selectedVehicle.model }}</h4>
              <p class="text-xs text-slate-450 mt-1 leading-snug">{{ selectedVehicle.version }}</p>
              
              <div class="flex gap-2 mt-4">
                <UiBadge variant="neutral">{{ selectedVehicle.year }}</UiBadge>
                <UiBadge variant="neutral">{{ selectedVehicle.transmission }}</UiBadge>
              </div>

              <div class="mt-6 border-t border-slate-850 pt-4">
                <span class="text-xs text-slate-500 block">Preço de Venda</span>
                <span class="text-2xl font-extrabold store-text-color">{{ formatPrice(selectedVehicle.price) }}</span>
              </div>
            </div>
          </div>

          <!-- Proposal Form -->
          <div class="p-6 md:p-8 bg-slate-900">
            <div class="mb-6">
              <h5 class="text-lg font-bold text-slate-200">{{ t('detail.title') }}</h5>
              <p class="text-xs text-slate-550 mt-1">Envie seus dados para atendimento imediato</p>
            </div>

            <!-- Form -->
            <LeadForm
              :vehicle-id="selectedVehicle.id"
              :initial-message="`Olá! Gostaria de falar com o consultor de vendas da ${currentStore.name} sobre o ${selectedVehicle.brand} ${selectedVehicle.model} anunciado por R$ ${selectedVehicle.price.toLocaleString('pt-BR')}.`"
              :store-whatsapp="currentStore.whatsapp"
              @success="() => {}"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.store-text-gradient {
  background: linear-gradient(to right, var(--store-accent-color, #fbbf24), var(--store-primary-to, #ea580c));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.store-text-color {
  color: var(--store-accent-color, #fbbf24);
}
</style>
