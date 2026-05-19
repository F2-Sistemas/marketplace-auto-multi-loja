<script setup lang="ts">
import type { Vehicle } from '~/composables/useTenant';
import { useI18n } from '~/composables/useI18n';
import UiCard from '~/components/ui/UiCard.vue';
import UiBadge from '~/components/ui/UiBadge.vue';

interface Props {
  vehicle: Vehicle;
}
defineProps<Props>();
defineEmits<{
  (e: 'click'): void;
}>();

const { t } = useI18n();

const formatPrice = (value: number) => {
  return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};
</script>

<template>
  <UiCard hover @click="$emit('click')" class="group cursor-pointer flex flex-col h-full">
    <!-- Image Wrapper -->
    <div class="relative aspect-video overflow-hidden bg-slate-950">
      <img
        :src="vehicle.image"
        :alt="`${vehicle.brand} ${vehicle.model}`"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
        loading="lazy"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 to-transparent"></div>
    </div>

    <!-- Details Container -->
    <div class="p-4 flex-grow flex flex-col justify-between">
      <div>
        <div class="flex justify-between items-start gap-2">
          <h3 class="font-bold text-slate-100 text-lg leading-tight group-hover:text-store transition-colors duration-200">
            {{ vehicle.brand }} <span class="font-medium text-slate-300">{{ vehicle.model }}</span>
          </h3>
        </div>
        <p class="text-xs text-slate-500 mt-1 line-clamp-1">
          {{ vehicle.version }}
        </p>
      </div>

      <div class="mt-4">
        <!-- Spec Badges -->
        <div class="flex flex-wrap gap-1.5 mb-4">
          <UiBadge variant="neutral">
            <iconify-icon icon="tabler:calendar" class="text-xs"></iconify-icon>
            {{ vehicle.year }}
          </UiBadge>
          <UiBadge variant="neutral">
            <iconify-icon icon="tabler:road" class="text-xs"></iconify-icon>
            {{ vehicle.mileage.toLocaleString('pt-BR') }} {{ t('specs.mileage') }}
          </UiBadge>
          <UiBadge variant="neutral">
            <iconify-icon icon="tabler:settings" class="text-xs"></iconify-icon>
            {{ vehicle.transmission }}
          </UiBadge>
        </div>

        <!-- Price and CTA -->
        <div class="flex justify-between items-center border-t border-slate-800/80 pt-3 mt-3">
          <div>
            <span class="text-xs text-slate-500 block">Preço Especial</span>
            <span class="text-xl font-extrabold text-slate-100">{{ formatPrice(vehicle.price) }}</span>
          </div>
          
          <span class="p-2 rounded-lg bg-slate-800/80 group-hover:bg-store-primary group-hover:text-slate-950 transition-all duration-300">
            <iconify-icon icon="tabler:chevron-right" class="w-5 h-5 block text-lg"></iconify-icon>
          </span>
        </div>
      </div>
    </div>
  </UiCard>
</template>

<style scoped>
.group-hover\:text-store:hover {
  color: var(--store-accent-color, #fbbf24);
}
.group-hover\:bg-store-primary:hover {
  background-color: var(--store-accent-color, #fbbf24);
}
</style>
