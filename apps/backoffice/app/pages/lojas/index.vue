<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from '../../composables/useI18n';
import { useAdmin } from '../../composables/useAdmin';

const { t } = useI18n();
const { stores, toggleStoreStatus, formatPlan } = useAdmin();

const searchQuery = ref('');

const filteredStores = computed(() => {
  if (!searchQuery.value.trim()) return stores.value;
  const q = searchQuery.value.toLowerCase();
  return stores.value.filter(
    (s) =>
      s.name.toLowerCase().includes(q) ||
      s.host.toLowerCase().includes(q)
  );
});
</script>

<template>
  <div class="space-y-6 animate-fadeIn">
    <!-- ACTION BAR -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-900/50 p-4 rounded-xl border border-slate-800">
      <div class="relative w-full sm:max-w-xs">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
          <iconify-icon icon="tabler:search" class="text-base"></iconify-icon>
        </span>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar revenda ou domínio..."
          class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-9 pr-4 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors"
        />
      </div>

      <UiButton to="/lojas/nova" variant="primary">
        <iconify-icon icon="tabler:circle-plus" class="text-sm"></iconify-icon>
        <span>{{ t('stores.btnNew') }}</span>
      </UiButton>
    </div>

    <!-- STORES TABLE -->
    <UiCard class="p-0 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-800 bg-slate-900/20">
        <h3 class="text-sm font-extrabold text-white">
          {{ t('stores.title') }}
        </h3>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-800 text-[10px] uppercase font-bold text-slate-400 bg-slate-950/40">
              <th class="py-3 px-6">{{ t('stores.columns.id') }}</th>
              <th class="py-3 px-6">{{ t('stores.columns.store') }}</th>
              <th class="py-3 px-6">{{ t('stores.columns.domain') }}</th>
              <th class="py-3 px-6">{{ t('stores.columns.plan') }}</th>
              <th class="py-3 px-6">{{ t('stores.columns.createdAt') }}</th>
              <th class="py-3 px-6 text-center">{{ t('stores.columns.status') }}</th>
              <th class="py-3 px-6 text-right">{{ t('stores.columns.actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-850 text-xs">
            <tr
              v-for="store in filteredStores"
              :key="store.id"
              class="hover:bg-slate-900/35 transition-colors"
            >
              <td class="py-4 px-6 text-slate-500 font-mono">
                #{{ store.id }}
              </td>
              <td class="py-4 px-6">
                <div class="flex items-center gap-2.5">
                  <div
                    :class="[
                      'w-2 h-2 rounded-full',
                      store.accentColor === 'amber' ? 'bg-amber-400' : '',
                      store.accentColor === 'red' ? 'bg-rose-500' : '',
                      store.accentColor === 'blue' ? 'bg-blue-500' : '',
                      store.accentColor === 'indigo' ? 'bg-indigo-500' : '',
                    ]"
                  ></div>
                  <span class="font-extrabold text-white">{{ store.name }}</span>
                </div>
              </td>
              <td class="py-4 px-6 text-slate-400">
                {{ store.host }}
              </td>
              <td class="py-4 px-6 text-slate-400">
                {{ formatPlan(store.plan) }}
              </td>
              <td class="py-4 px-6 text-slate-500 font-medium">
                {{ store.created_at }}
              </td>
              <td class="py-4 px-6 text-center">
                <UiBadge :variant="store.status === 'ativo' ? 'success' : 'danger'" :pulse="store.status === 'ativo'">
                  {{ store.status }}
                </UiBadge>
              </td>
              <td class="py-4 px-6 text-right">
                <UiButton
                  @click="toggleStoreStatus(store)"
                  variant="secondary"
                  size="sm"
                >
                  <iconify-icon
                    :icon="store.status === 'ativo' ? 'tabler:circle-x' : 'tabler:circle-check'"
                    class="text-sm"
                  ></iconify-icon>
                  <span>{{ store.status === 'ativo' ? t('stores.actions.deactivate') : t('stores.actions.reactivate') }}</span>
                </UiButton>
              </td>
            </tr>

            <tr v-if="filteredStores.length === 0">
              <td colspan="7" class="py-12 text-center text-slate-500">
                Nenhuma revenda cadastrada ou correspondente aos termos de busca.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </UiCard>
  </div>
</template>
