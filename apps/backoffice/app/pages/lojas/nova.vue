<script setup lang="ts">
import { useI18n } from '../../composables/useI18n';
import { useAdmin } from '../../composables/useAdmin';

const { t } = useI18n();
const { newStore, showWizardSuccess, loading, handleCreateStore } = useAdmin();

const plans = [
  { value: 'mensal', label: t('wizard.fields.plan') + ' - ' + t('stores.plans.mensal') },
  { value: 'semestral', label: t('wizard.fields.plan') + ' - ' + t('stores.plans.semestral') },
  { value: 'anual', label: t('wizard.fields.plan') + ' - ' + t('stores.plans.anual') },
];

const colors = [
  { value: 'indigo', label: t('wizard.colors.indigo'), class: 'bg-indigo-500 ring-indigo-500/30' },
  { value: 'amber', label: t('wizard.colors.amber'), class: 'bg-amber-500 ring-amber-500/30' },
  { value: 'red', label: t('wizard.colors.red'), class: 'bg-rose-600 ring-rose-600/30' },
  { value: 'blue', label: t('wizard.colors.blue'), class: 'bg-blue-600 ring-blue-600/30' },
];
</script>

<template>
  <div class="max-w-2xl mx-auto animate-fadeIn">
    <UiCard class="relative overflow-hidden">
      <!-- SUCCESS BOX OVERLAY -->
      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-4"
      >
        <div
          v-if="showWizardSuccess"
          class="absolute inset-0 bg-slate-950/90 z-20 flex flex-col items-center justify-center p-6 text-center"
        >
          <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-4 animate-bounce">
            <iconify-icon icon="tabler:circle-check" class="text-3xl"></iconify-icon>
          </div>
          <h4 class="text-lg font-extrabold text-white">
            {{ t('wizard.successMsg') }}
          </h4>
          <p class="text-xs text-slate-500 mt-2">
            O novo storefront e subdomínio estão prontos para uso. Redirecionando...
          </p>
        </div>
      </transition>

      <div class="border-b border-slate-800 pb-4 mb-6">
        <h3 class="text-base font-extrabold text-white">
          {{ t('wizard.title') }}
        </h3>
        <p class="text-xs text-slate-500 mt-1">
          {{ t('wizard.desc') }}
        </p>
      </div>

      <form @submit.prevent="handleCreateStore" class="space-y-6">
        <!-- RETAIL NAME -->
        <UiInput
          v-model="newStore.name"
          :label="t('wizard.fields.name')"
          :placeholder="t('wizard.fields.namePlaceholder')"
          required
        />

        <!-- SUBDOMAIN HOST -->
        <div>
          <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
            {{ t('wizard.fields.subdomain') }}
          </label>
          <div class="flex">
            <input
              v-model="newStore.subdomain"
              type="text"
              :placeholder="t('wizard.fields.subdomainPlaceholder')"
              required
              class="flex-1 bg-slate-950 border border-slate-800 rounded-l-lg px-3 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors"
            />
            <span class="bg-slate-900 border-y border-r border-slate-800 rounded-r-lg px-4 py-2.5 text-xs text-slate-400 font-bold flex items-center select-none">
              .rederevenda.com
            </span>
          </div>
        </div>

        <!-- SELECT PLAN -->
        <UiSelect
          v-model="newStore.plan"
          :label="t('wizard.fields.plan')"
          :options="plans"
        />

        <!-- COLOR SELECTOR -->
        <div>
          <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
            {{ t('wizard.fields.accentColor') }}
          </label>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <button
              v-for="color in colors"
              :key="color.value"
              type="button"
              @click="newStore.accentColor = color.value"
              :class="[
                'flex items-center gap-3 p-3 rounded-lg border text-left transition-all duration-200 focus:outline-none',
                newStore.accentColor === color.value
                  ? 'border-indigo-500 bg-indigo-650/10'
                  : 'border-slate-800 bg-slate-950 hover:bg-slate-900/40',
              ]"
            >
              <span :class="['w-4 h-4 rounded-full shrink-0 ring-4', color.class]"></span>
              <span class="text-xs font-bold text-slate-200 leading-none">{{ color.label }}</span>
            </button>
          </div>
        </div>

        <!-- SUBMIT -->
        <div class="pt-4 border-t border-slate-800 flex justify-end">
          <UiButton
            type="submit"
            variant="primary"
            :disabled="loading"
          >
            <iconify-icon
              :icon="loading ? 'tabler:loader' : 'tabler:rocket'"
              :class="['text-sm', loading ? 'animate-spin' : '']"
            ></iconify-icon>
            <span>{{ t('wizard.fields.submitBtn') }}</span>
          </UiButton>
        </div>
      </form>
    </UiCard>
  </div>
</template>
