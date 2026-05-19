<script setup lang="ts">
import { useI18n } from '../composables/useI18n';
import { useAdmin } from '../composables/useAdmin';

const { t } = useI18n();
const { stores } = useAdmin();
</script>

<template>
  <div class="space-y-8 animate-fadeIn">
    <!-- METRIC CARDS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <UiCard class="hover:scale-[1.01] transition-transform duration-200">
        <div class="flex justify-between items-start">
          <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">
              {{ t('activeStores') }}
            </span>
            <span class="text-3xl font-extrabold text-white mt-2 block">
              {{ stores.filter(s => s.status === 'ativo').length }}
            </span>
          </div>
          <div class="w-10 h-10 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shadow shadow-indigo-500/10">
            <iconify-icon icon="tabler:building-store" class="text-xl"></iconify-icon>
          </div>
        </div>
        <p class="text-[11px] text-emerald-400 mt-4 flex items-center gap-1">
          <iconify-icon icon="tabler:trending-up" class="text-xs"></iconify-icon>
          <span>{{ t('activeStoresSub') }}</span>
        </p>
      </UiCard>

      <UiCard class="hover:scale-[1.01] transition-transform duration-200">
        <div class="flex justify-between items-start">
          <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">
              {{ t('totalVehicles') }}
            </span>
            <span class="text-3xl font-extrabold text-white mt-2 block">
              128
            </span>
          </div>
          <div class="w-10 h-10 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shadow shadow-amber-500/10">
            <iconify-icon icon="tabler:car" class="text-xl"></iconify-icon>
          </div>
        </div>
        <p class="text-[11px] text-emerald-400 mt-4 flex items-center gap-1">
          <iconify-icon icon="tabler:trending-up" class="text-xs"></iconify-icon>
          <span>{{ t('totalVehiclesSub') }}</span>
        </p>
      </UiCard>

      <UiCard class="hover:scale-[1.01] transition-transform duration-200">
        <div class="flex justify-between items-start">
          <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">
              {{ t('totalLeads') }}
            </span>
            <span class="text-3xl font-extrabold text-white mt-2 block">
              342
            </span>
          </div>
          <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shadow shadow-emerald-500/10">
            <iconify-icon icon="tabler:users-group" class="text-xl"></iconify-icon>
          </div>
        </div>
        <p class="text-[11px] text-slate-400 mt-4 flex items-center gap-1">
          <iconify-icon icon="tabler:activity" class="text-xs"></iconify-icon>
          <span>{{ t('totalLeadsSub') }}</span>
        </p>
      </UiCard>

      <UiCard class="hover:scale-[1.01] transition-transform duration-200">
        <div class="flex justify-between items-start">
          <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">
              {{ t('monthlyRevenue') }}
            </span>
            <span class="text-3xl font-extrabold text-white mt-2 block">
              R$ 28.450
            </span>
          </div>
          <div class="w-10 h-10 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shadow shadow-indigo-500/10">
            <iconify-icon icon="tabler:currency-real" class="text-xl"></iconify-icon>
          </div>
        </div>
        <p class="text-[11px] text-indigo-400 mt-4 flex items-center gap-1">
          <iconify-icon icon="tabler:repeat" class="text-xs"></iconify-icon>
          <span>{{ t('monthlyRevenueSub') }}</span>
        </p>
      </UiCard>
    </div>

    <!-- MAIN TWO-COLUMN LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- LEFTSIDE CHART CARD -->
      <div class="lg:col-span-2">
        <UiCard class="h-full flex flex-col justify-between">
          <div>
            <div class="flex justify-between items-center border-b border-slate-800 pb-4 mb-6">
              <div>
                <h3 class="text-base font-extrabold text-white">
                  {{ t('dashboard.revenueChartTitle') }}
                </h3>
                <p class="text-[10px] text-slate-500 mt-0.5">
                  Proporção de distribuição dos planos de faturamento ativos
                </p>
              </div>
              <UiBadge variant="indigo">SaaS Tiers</UiBadge>
            </div>

            <!-- TIER BARS -->
            <div class="space-y-6">
              <div>
                <div class="flex justify-between text-xs font-bold text-slate-300 mb-2">
                  <span>{{ t('dashboard.tiers.annual') }}</span>
                  <span class="text-indigo-400">45%</span>
                </div>
                <div class="w-full h-3 bg-slate-800 rounded-full overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-indigo-650 to-indigo-550 rounded-full w-[45%] transition-all duration-1000"></div>
                </div>
              </div>

              <div>
                <div class="flex justify-between text-xs font-bold text-slate-300 mb-2">
                  <span>{{ t('dashboard.tiers.semiannual') }}</span>
                  <span class="text-amber-400">35%</span>
                </div>
                <div class="w-full h-3 bg-slate-800 rounded-full overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-amber-600 to-amber-500 rounded-full w-[35%] transition-all duration-1000"></div>
                </div>
              </div>

              <div>
                <div class="flex justify-between text-xs font-bold text-slate-300 mb-2">
                  <span>{{ t('dashboard.tiers.monthly') }}</span>
                  <span class="text-rose-450">20%</span>
                </div>
                <div class="w-full h-3 bg-slate-800 rounded-full overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-rose-600 to-rose-500 rounded-full w-[20%] transition-all duration-1000"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-8 pt-4 border-t border-slate-800 flex justify-between items-center text-xs text-slate-400">
            <span>Faturamento consolidado: R$ 341.400 / ano</span>
            <iconify-icon icon="tabler:shield-check" class="text-lg text-emerald-400"></iconify-icon>
          </div>
        </UiCard>
      </div>

      <!-- RIGHTSIDE INTEGRATION CARD -->
      <div>
        <UiCard class="h-full flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-indigo-500 to-amber-400 flex items-center justify-center text-white shadow shadow-indigo-500/20 mb-6">
              <iconify-icon icon="tabler:rocket" class="text-2xl animate-pulse"></iconify-icon>
            </div>
            
            <h3 class="text-base font-extrabold text-white mb-2">
              {{ t('dashboard.supportTitle') }}
            </h3>
            
            <p class="text-xs text-slate-400 leading-relaxed">
              {{ t('dashboard.supportDesc') }}
            </p>
          </div>

          <div class="mt-8 pt-6 border-t border-slate-800">
            <UiButton to="/lojas/nova" variant="primary" class="w-full">
              <iconify-icon icon="tabler:circle-plus" class="text-lg"></iconify-icon>
              <span>{{ t('dashboard.supportBtn') }}</span>
            </UiButton>
          </div>
        </UiCard>
      </div>
    </div>
  </div>
</template>
