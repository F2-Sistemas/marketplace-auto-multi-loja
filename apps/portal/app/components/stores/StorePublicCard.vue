<script setup lang="ts">
import { useRouter } from '#app';
import { useI18n } from '~/composables/useI18n';
import UiCard from '~/components/ui/UiCard.vue';
import UiButton from '~/components/ui/UiButton.vue';

interface Store {
  id: number;
  name: string;
  slug: string;
  logo?: string;
  description?: string;
  phone?: string;
  email?: string;
  address?: string;
  city?: string;
  state?: string;
}

const props = defineProps<{
  store: Store;
}>();

const router = useRouter();
const { t } = useI18n();

const handleNavigate = () => {
  router.push(`/lojas/${props.store.slug}`);
};
</script>

<template>
  <UiCard variant="interactive" @click="handleNavigate" class="h-full flex flex-col justify-between cursor-pointer group">
    <div class="space-y-4">
      <!-- Logo Block -->
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl bg-neutral-50 border border-neutral-150 flex items-center justify-center overflow-hidden flex-shrink-0 group-hover:border-brand-300 transition-colors shadow-sm">
          <img
            v-if="store.logo"
            :src="store.logo"
            :alt="store.name"
            class="w-full h-full object-cover"
          />
          <iconify-icon v-else icon="tabler:building-store" class="text-neutral-400 text-2xl"></iconify-icon>
        </div>
        
        <div>
          <h4 class="font-extrabold text-neutral-800 text-base group-hover:text-brand-600 transition-colors line-clamp-1">
            {{ store.name }}
          </h4>
          <span class="text-xs text-neutral-400 font-semibold block">
            {{ store.city || 'São Paulo' }} - {{ store.state || 'SP' }}
          </span>
        </div>
      </div>

      <!-- Slogan Description -->
      <p class="text-xs text-neutral-500 font-semibold line-clamp-2 min-h-[2rem]">
        {{ store.description || 'Concessionária parceira especializada em veículos novos e seminovos de alta qualidade.' }}
      </p>

      <!-- Physical contacts specs -->
      <div class="space-y-2 border-t border-neutral-100 pt-4 text-xs text-neutral-500 font-semibold">
        <div v-if="store.phone" class="flex items-center gap-2">
          <iconify-icon icon="tabler:phone" class="text-neutral-400"></iconify-icon>
          <span>{{ store.phone }}</span>
        </div>
        <div v-if="store.address" class="flex items-center gap-2">
          <iconify-icon icon="tabler:map-pin" class="text-neutral-400"></iconify-icon>
          <span class="truncate">{{ store.address }}</span>
        </div>
      </div>
    </div>

    <!-- Navigate Trigger -->
    <div class="pt-4 border-t border-neutral-100 mt-4">
      <UiButton
        variant="soft"
        size="sm"
        class="w-full"
      >
        <span>{{ t('vehicle.viewDetails') }}</span>
        <template #icon>
          <iconify-icon icon="tabler:chevron-right"></iconify-icon>
        </template>
      </UiButton>
    </div>
  </UiCard>
</template>
