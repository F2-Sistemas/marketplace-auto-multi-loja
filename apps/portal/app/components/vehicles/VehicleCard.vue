<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from '#app';
import { useI18n } from '~/composables/useI18n';
import UiCard from '~/components/ui/UiCard.vue';
import UiBadge from '~/components/ui/UiBadge.vue';
import UiButton from '~/components/ui/UiButton.vue';

interface Vehicle {
  id: number;
  title: string;
  brand: string;
  model: string;
  version: string;
  year_manufacture: number;
  year_model: number;
  mileage: number;
  transmission: string;
  price: number;
  slug: string;
  images: string[];
  store?: {
    name: string;
    slug: string;
    logo?: string;
  };
}

const props = defineProps<{
  vehicle: Vehicle;
}>();

const router = useRouter();
const { t } = useI18n();

const displayImage = computed(() => {
  if (props.vehicle.images && props.vehicle.images.length > 0) {
    return props.vehicle.images[0];
  }
  return 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80';
});

const formatPrice = (value: number) => {
  return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 });
};

const formatMileage = (value: number) => {
  if (value === 0) return '0 km (Novo)';
  return `${value.toLocaleString('pt-BR')} km`;
};

const handleNavigate = () => {
  router.push(`/veiculos/${props.vehicle.slug}`);
};

const handleStoreClick = (event: Event) => {
  event.stopPropagation();
  if (props.vehicle.store) {
    router.push(`/lojas/${props.vehicle.store.slug}`);
  }
};
</script>

<template>
  <UiCard variant="interactive" @click="handleNavigate" class="h-full flex flex-col cursor-pointer group">
    <!-- Image Cover Wrapper -->
    <div class="relative aspect-video w-full bg-neutral-100 overflow-hidden">
      <img
        :src="displayImage"
        :alt="vehicle.title"
        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
        loading="lazy"
      />
      <!-- Transmission badge absolute -->
      <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
        <UiBadge variant="secondary" size="sm">
          {{ vehicle.year_manufacture }}/{{ vehicle.year_model }}
        </UiBadge>
        <UiBadge v-if="vehicle.transmission.toLowerCase().includes('auto')" variant="primary" size="sm">
          {{ t('search.transmissionAuto') }}
        </UiBadge>
      </div>
    </div>

    <!-- Details block -->
    <div class="flex-1 flex flex-col justify-between pt-5">
      <div class="space-y-2">
        <!-- Brand / Model Header -->
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-brand-600 block">
            {{ vehicle.brand }}
          </span>
          <h4 class="font-extrabold text-neutral-800 text-base line-clamp-1 group-hover:text-brand-600 transition-colors">
            {{ vehicle.model }}
          </h4>
          <p class="text-xs text-neutral-400 font-medium line-clamp-1">
            {{ vehicle.version }}
          </p>
        </div>

        <!-- Spec badging -->
        <div class="flex items-center gap-4 py-2 text-xs text-neutral-500 font-semibold border-y border-neutral-100">
          <div class="flex items-center gap-1">
            <iconify-icon icon="tabler:road" class="text-neutral-400 text-sm"></iconify-icon>
            <span>{{ formatMileage(vehicle.mileage) }}</span>
          </div>
          <div class="flex items-center gap-1">
            <iconify-icon icon="tabler:settings" class="text-neutral-400 text-sm"></iconify-icon>
            <span class="capitalize">{{ vehicle.transmission }}</span>
          </div>
        </div>
      </div>

      <div class="pt-4 space-y-4">
        <!-- Price Block -->
        <div class="flex items-baseline justify-between">
          <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">
            {{ t('vehicle.priceLabel') }}
          </span>
          <span class="text-lg font-extrabold text-neutral-900">
            {{ formatPrice(vehicle.price) }}
          </span>
        </div>

        <!-- Store detail links -->
        <div v-if="vehicle.store" class="flex items-center justify-between border-t border-neutral-100 pt-3">
          <button
            @click="handleStoreClick"
            class="flex items-center gap-2 hover:text-brand-600 text-left transition-colors cursor-pointer group/store"
          >
            <div class="w-6 h-6 rounded-full bg-neutral-100 flex items-center justify-center overflow-hidden border border-neutral-150">
              <img
                v-if="vehicle.store.logo"
                :src="vehicle.store.logo"
                :alt="vehicle.store.name"
                class="w-full h-full object-cover"
              />
              <iconify-icon v-else icon="tabler:building-store" class="text-neutral-400 text-xs"></iconify-icon>
            </div>
            <span class="text-xs font-bold text-neutral-600 group-hover/store:text-brand-600 truncate max-w-[150px]">
              {{ vehicle.store.name }}
            </span>
          </button>

          <UiButton
            variant="ghost"
            size="sm"
            class="!p-0 text-brand-600 hover:text-brand-700 !h-auto font-bold text-xs"
          >
            <span>{{ t('vehicle.actions.viewDetails') }}</span>
            <template #icon>
              <iconify-icon icon="tabler:chevron-right" class="text-xs"></iconify-icon>
            </template>
          </UiButton>
        </div>
      </div>
    </div>
  </UiCard>
</template>
