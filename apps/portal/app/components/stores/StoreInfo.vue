<script setup lang="ts">
import { useI18n } from '~/composables/useI18n';

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

defineProps<{
  store: Store;
}>();

const { t } = useI18n();
</script>

<template>
  <div class="border border-neutral-150 bg-white rounded-card overflow-hidden shadow-sm space-y-6 p-6">
    <!-- Description section -->
    <div class="space-y-2">
      <h3 class="font-extrabold text-neutral-800 text-sm uppercase tracking-wider">
        Sobre a Loja
      </h3>
      <p class="text-sm text-neutral-500 font-semibold leading-relaxed">
        {{ store.description || 'Esta concessionária é parceira oficial do marketplace e conta com uma equipe especializada para lhe oferecer o melhor atendimento e veículos revisados com garantia de procedência.' }}
      </p>
    </div>

    <!-- Contact lines -->
    <div class="space-y-4 pt-4 border-t border-neutral-100">
      <h3 class="font-extrabold text-neutral-800 text-sm uppercase tracking-wider">
        Contatos e Endereço
      </h3>

      <div class="space-y-3 text-sm text-neutral-600 font-semibold">
        <div v-if="store.phone" class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-neutral-50 flex items-center justify-center text-neutral-400">
            <iconify-icon icon="tabler:phone" class="text-base"></iconify-icon>
          </div>
          <div>
            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Telefone</span>
            <span>{{ store.phone }}</span>
          </div>
        </div>

        <div v-if="store.email" class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-neutral-50 flex items-center justify-center text-neutral-400">
            <iconify-icon icon="tabler:mail" class="text-base"></iconify-icon>
          </div>
          <div>
            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">E-mail</span>
            <span class="break-all">{{ store.email }}</span>
          </div>
        </div>

        <div v-if="store.address" class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-neutral-50 flex items-center justify-center text-neutral-400">
            <iconify-icon icon="tabler:map-pin" class="text-base"></iconify-icon>
          </div>
          <div>
            <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Endereço</span>
            <span>{{ store.address || 'Av. das Nações Unidas, 1520 - São Paulo, SP' }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Hours of operation -->
    <div class="space-y-3 pt-4 border-t border-neutral-100">
      <h3 class="font-extrabold text-neutral-800 text-sm uppercase tracking-wider">
        Horário de Funcionamento
      </h3>
      <div class="space-y-1.5 text-xs text-neutral-500 font-semibold">
        <div class="flex justify-between">
          <span>Segunda a Sexta</span>
          <span class="font-bold text-neutral-700">08:00 às 18:00</span>
        </div>
        <div class="flex justify-between">
          <span>Sábado</span>
          <span class="font-bold text-neutral-700">09:00 às 13:00</span>
        </div>
        <div class="flex justify-between text-neutral-400">
          <span>Domingo</span>
          <span>Fechado</span>
        </div>
      </div>
    </div>
  </div>
</template>
