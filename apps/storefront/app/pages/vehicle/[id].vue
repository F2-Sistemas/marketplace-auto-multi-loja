<script setup lang="ts">
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTenant } from '~/composables/useTenant';
import { useI18n } from '~/composables/useI18n';
import UiCard from '~/components/ui/UiCard.vue';
import UiBadge from '~/components/ui/UiBadge.vue';
import LeadForm from '~/components/lead/LeadForm.vue';
import UiButton from '~/components/ui/UiButton.vue';

const route = useRoute();
const router = useRouter();
const { t } = useI18n();
const { currentStore, vehicles, formatPrice } = useTenant();

const vehicleId = computed(() => Number(route.params.id));
const vehicle = computed(() => vehicles.value.find(v => v.id === vehicleId.value));

const goBack = () => {
  router.push('/');
};
</script>

<template>
  <div class="py-12 bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Back button -->
      <button
        @click="goBack"
        class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-white transition duration-200 mb-8 cursor-pointer group"
      >
        <iconify-icon icon="tabler:arrow-left" class="text-base group-hover:-translate-x-0.5 transition-transform block"></iconify-icon>
        Voltar ao Estoque
      </button>

      <div v-if="vehicle" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Gallery & Technical Details -->
        <div class="lg:col-span-2 space-y-6 text-left">
          <!-- Main Image -->
          <div class="relative rounded-2xl overflow-hidden border border-slate-900 bg-slate-900 aspect-video shadow-2xl">
            <img
              :src="vehicle.image"
              :alt="`${vehicle.brand} ${vehicle.model}`"
              class="w-full h-full object-cover"
            />
          </div>

          <!-- Description & Specs -->
          <UiCard class="p-6 md:p-8 border-slate-800 bg-slate-900/40">
            <div class="flex items-center gap-2 mb-4">
              <span class="text-xs font-bold uppercase tracking-widest text-slate-500">{{ vehicle.brand }}</span>
              <span class="w-1 h-1 rounded-full bg-slate-700"></span>
              <span class="text-xs font-bold text-slate-450">{{ vehicle.year }}</span>
            </div>
            
            <h3 class="text-2xl md:text-3xl font-black text-slate-100 mb-2 leading-tight">
              {{ vehicle.model }}
            </h3>
            
            <p class="text-xs text-slate-550 leading-snug mb-6">{{ vehicle.version }}</p>

            <!-- Specs Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 border-t border-b border-slate-850 py-6">
              <div>
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Quilometragem</span>
                <span class="text-sm font-bold text-slate-200 mt-1 block">{{ vehicle.km.toLocaleString('pt-BR') }} km</span>
              </div>
              <div>
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Câmbio</span>
                <span class="text-sm font-bold text-slate-200 mt-1 block">{{ vehicle.transmission }}</span>
              </div>
              <div>
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Combustível</span>
                <span class="text-sm font-bold text-slate-200 mt-1 block">{{ vehicle.fuel }}</span>
              </div>
              <div>
                <span class="text-[10px] text-slate-500 block uppercase font-semibold">Cor</span>
                <span class="text-sm font-bold text-slate-200 mt-1 block">{{ vehicle.color }}</span>
              </div>
            </div>

            <!-- Features details -->
            <h4 class="font-bold text-slate-350 text-sm mb-4">Destaques do Veículo</h4>
            <div class="flex flex-wrap gap-2">
              <UiBadge variant="neutral">Direção Hidráulica</UiBadge>
              <UiBadge variant="neutral">Ar Condicionado</UiBadge>
              <UiBadge variant="neutral">Vidros Elétricos</UiBadge>
              <UiBadge variant="neutral">Central Multimídia</UiBadge>
              <UiBadge variant="neutral">Freios ABS</UiBadge>
              <UiBadge variant="neutral">Airbags Frontais</UiBadge>
            </div>
          </UiCard>
        </div>

        <!-- Sidebar lead / price details -->
        <div class="lg:col-span-1 space-y-6 text-left">
          <!-- Price Card -->
          <UiCard class="p-6 md:p-8 border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-xs font-semibold text-slate-500 block">Preço Especial</span>
            <div class="flex items-baseline gap-2 mt-2">
              <span class="text-3xl font-black store-text-color tracking-tight">{{ formatPrice(vehicle.price) }}</span>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-850">
              <h4 class="font-bold text-slate-200 text-sm mb-1">{{ t('detail.title') }}</h4>
              <p class="text-xs text-slate-500 mb-6">Envie sua proposta e receba atendimento imediato</p>

              <!-- Form -->
              <LeadForm
                :vehicle-id="vehicle.id"
                :initial-message="`Olá! Gostaria de falar com o consultor de vendas da ${currentStore.name} sobre o ${vehicle.brand} ${vehicle.model} anunciado por R$ ${vehicle.price.toLocaleString('pt-BR')}.`"
                :store-whatsapp="currentStore.whatsapp"
                @success="() => {}"
              />
            </div>
          </UiCard>
        </div>
      </div>

      <!-- Not Found Box -->
      <div v-else class="text-center py-20">
        <iconify-icon icon="tabler:mood-sad" class="text-5xl text-slate-600 mb-4 block mx-auto animate-bounce"></iconify-icon>
        <h3 class="text-xl font-bold text-slate-350">Veículo Não Encontrado</h3>
        <p class="text-slate-550 text-xs mt-2">O veículo que você está procurando pode ter sido vendido ou removido.</p>
        <NuxtLink to="/">
          <UiButton class="mt-6 px-6 py-2.5 font-bold cursor-pointer">
            Voltar ao Estoque
          </UiButton>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<style scoped>
.store-text-color {
  color: var(--store-accent-color, #fbbf24);
}
</style>
