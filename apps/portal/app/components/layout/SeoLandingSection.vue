<script setup lang="ts">
import { useI18n } from '~/composables/useI18n';

const { t } = useI18n();
</script>

<template>
  <section class="border border-neutral-150 bg-white rounded-card shadow-sm p-8 space-y-6">
    <div class="space-y-2">
      <h2 class="text-xl font-black text-neutral-800 tracking-tight">
        Compre Veículos com Segurança e Facilidade
      </h2>
      <p class="text-sm text-neutral-500 font-semibold leading-relaxed">
        Bem-vindo ao maior portal de classificados de automóveis integrando diversas lojas e concessionárias parceiras da sua região. Oferecemos um inventário completo de carros novos, seminovos e usados com garantia de procedência e facilidades de financiamento.
      </p>
    </div>

    <!-- Core Grid of benefits -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-neutral-100">
      <div class="space-y-2">
        <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
          <iconify-icon icon="tabler:shield-check" class="text-xl"></iconify-icon>
        </div>
        <h4 class="font-extrabold text-neutral-800 text-sm">Garantia Integrada</h4>
        <p class="text-xs text-neutral-400 font-semibold leading-relaxed">
          Todos os veículos de concessionárias passam por vistoria cautelar completa para assegurar a melhor procedência para sua compra.
        </p>
      </div>

      <div class="space-y-2">
        <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
          <iconify-icon icon="tabler:building-store" class="text-xl"></iconify-icon>
        </div>
        <h4 class="font-extrabold text-neutral-800 text-sm">Múltiplas Concessionárias</h4>
        <p class="text-xs text-neutral-400 font-semibold leading-relaxed">
          Navegue pelo estoque unificado das melhores lojas de automóveis e encontre o carro ideal comparando condições em um único lugar.
        </p>
      </div>

      <div class="space-y-2">
        <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
          <iconify-icon icon="tabler:message-circle-code" class="text-xl"></iconify-icon>
        </div>
        <h4 class="font-extrabold text-neutral-800 text-sm">Contato Direto</h4>
        <p class="text-xs text-neutral-400 font-semibold leading-relaxed">
          Fale diretamente com os consultores das concessionárias pelo formulário integrado ou de forma imediata via WhatsApp.
        </p>
      </div>
    </div>
  </section>
</template>
