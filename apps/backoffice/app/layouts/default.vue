<script setup lang="ts">
import { useRoute } from 'vue-router';
import { useI18n } from '../composables/useI18n';

const route = useRoute();
const { t } = useI18n();

const getHeaderTitle = () => {
  if (route.path === '/lojas') return t('headers.stores');
  if (route.path === '/lojas/nova') return t('headers.wizard');
  if (route.path === '/seguranca') return t('headers.security');
  return t('headers.dashboard');
};
</script>

<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 font-['Outfit'] antialiased flex flex-col md:flex-row">
    <!-- SIDEBAR NAVIGATION -->
    <aside
      class="w-full md:w-64 bg-slate-900 border-b md:border-b-0 md:border-r border-slate-800 flex flex-col justify-between shrink-0"
    >
      <div>
        <!-- BRAND LOGO -->
        <div class="p-6 flex items-center gap-3 border-b border-slate-800">
          <div
            class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-amber-400 flex items-center justify-center text-white shadow shadow-indigo-500/20"
          >
            <iconify-icon icon="tabler:steering-wheel" class="text-xl"></iconify-icon>
          </div>
          <div>
            <span
              class="font-extrabold text-xl tracking-tight bg-gradient-to-r from-indigo-400 to-amber-400 bg-clip-text text-transparent"
            >
              {{ t('appName') }}
            </span>
            <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-widest leading-none">
              {{ t('appSub') }}
            </span>
          </div>
        </div>

        <!-- NAVIGATION ITEMS -->
        <nav class="p-4 space-y-1.5">
          <NuxtLink
            to="/"
            :class="[
              'w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/50',
              route.path === '/'
                ? 'bg-indigo-650/15 border border-indigo-500/30 text-indigo-300'
                : 'bg-transparent border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-850',
            ]"
          >
            <iconify-icon icon="tabler:chart-bar" class="text-lg"></iconify-icon>
            <span>{{ t('navigation.dashboard') }}</span>
          </NuxtLink>

          <NuxtLink
            to="/lojas"
            :class="[
              'w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/50',
              route.path === '/lojas'
                ? 'bg-indigo-650/15 border border-indigo-500/30 text-indigo-300'
                : 'bg-transparent border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-850',
            ]"
          >
            <iconify-icon icon="tabler:building-store" class="text-lg"></iconify-icon>
            <span>{{ t('navigation.stores') }}</span>
          </NuxtLink>

          <NuxtLink
            to="/lojas/nova"
            :class="[
              'w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/50',
              route.path === '/lojas/nova'
                ? 'bg-indigo-650/15 border border-indigo-500/30 text-indigo-300'
                : 'bg-transparent border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-850',
            ]"
          >
            <iconify-icon icon="tabler:circle-plus" class="text-lg"></iconify-icon>
            <span>{{ t('navigation.wizard') }}</span>
          </NuxtLink>

          <NuxtLink
            to="/seguranca"
            :class="[
              'w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/50',
              route.path === '/seguranca'
                ? 'bg-indigo-650/15 border border-indigo-500/30 text-indigo-300'
                : 'bg-transparent border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-850',
            ]"
          >
            <iconify-icon icon="tabler:shield-lock" class="text-lg"></iconify-icon>
            <span>{{ t('navigation.security') }}</span>
          </NuxtLink>
        </nav>
      </div>

      <!-- FOOTER USER INFO -->
      <div class="p-4 border-t border-slate-800 bg-slate-950/40">
        <div class="flex items-center gap-3">
          <div
            class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-indigo-400 font-bold text-sm"
          >
            TS
          </div>
          <div>
            <span class="font-bold text-xs text-white block">{{ t('adminName') }}</span>
            <span class="text-[10px] text-slate-500 block">{{ t('adminRole') }}</span>
          </div>
        </div>
      </div>
    </aside>

    <!-- MAIN CONTENT BODY -->
    <main class="flex-1 p-6 md:p-8 space-y-8 max-w-7xl mx-auto w-full overflow-hidden">
      <!-- TOP STATUS BAR -->
      <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 class="text-2xl font-extrabold text-white">
            {{ getHeaderTitle() }}
          </h1>
          <p class="text-xs text-slate-500 mt-1">
            {{ t('headers.subtitle') }}
          </p>
        </div>

        <UiBadge variant="success" pulse>
          {{ t('connectedDb') }}
        </UiBadge>
      </header>
      
      <div class="w-full">
        <slot />
      </div>
    </main>
  </div>
</template>
