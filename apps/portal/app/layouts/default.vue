<script setup lang="ts">
import { useRoute } from '#app';
import { onMounted } from 'vue';
import { useI18n } from '~/composables/useI18n';
import { useFavorites } from '~/composables/useFavorites';

const route = useRoute();
const { t } = useI18n();
const { fetchFavorites, favoriteCount } = useFavorites();

onMounted(() => {
    fetchFavorites();
});

const navLinks = [
    { label: 'Início', to: '/' },
    { label: 'Veículos', to: '/veiculos' },
    { label: 'Concessionárias', to: '/lojas' },
];

const isLinkActive = (to: string) => {
    if (to === '/') {
        return route.path === '/';
    }
    return route.path.startsWith(to);
};
</script>

<template>
    <div class="min-h-screen bg-neutral-50 flex flex-col font-sans selection:bg-brand-500 selection:text-white">
        <!-- Sticky responsive navbar header -->
        <header
            class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-neutral-200 shadow-sm h-16 flex items-center"
        >
            <div class="container max-w-7xl mx-auto px-4 md:px-6 flex items-center justify-between">
                <!-- Logo -->
                <NuxtLink to="/" class="flex items-center gap-2 select-none group">
                    <div
                        class="w-9 h-9 rounded-lg bg-brand-500 flex items-center justify-center text-white shadow-md shadow-brand-500/20 group-hover:scale-105 transition-transform duration-180"
                    >
                        <iconify-icon icon="tabler:steering-wheel" class="text-xl"></iconify-icon>
                    </div>
                    <span class="font-black text-lg text-neutral-800 tracking-tight">
                        Auto
                        <span class="text-brand-500 font-normal">Market</span>
                    </span>
                </NuxtLink>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-6">
                    <NuxtLink
                        v-for="link in navLinks"
                        :key="link.to"
                        :to="link.to"
                        class="text-sm font-normal uppercase tracking-wider transition-colors duration-150 py-1.5 border-b-2"
                        :class="
                            isLinkActive(link.to)
                                ? 'text-brand-600 border-brand-500'
                                : 'text-neutral-500 border-transparent hover:text-neutral-800'
                        "
                    >
                        {{ link.label }}
                    </NuxtLink>
                </nav>

                <!-- Dynamic CTA -->
                <div class="flex items-center gap-3">
                    <NuxtLink to="/favoritos" class="relative">
                        <ClientOnly>
                            <button
                                class="px-3 h-9 font-normal text-xs rounded-button bg-neutral-100 text-neutral-700 hover:bg-neutral-200 transition duration-150 cursor-pointer flex items-center gap-1.5 shadow-xs"
                            >
                                <iconify-icon
                                    :icon="favoriteCount > 0 ? 'fa7-solid:heart' : 'fa7-regular:heart'"
                                    :class="[
                                        'text-xs transition-colors',
                                        favoriteCount > 0 ? 'text-red-500' : 'text-neutral-500',
                                    ]"
                                ></iconify-icon>
                                <span class="hidden sm:inline">Favoritos</span>
                                <span
                                    v-if="favoriteCount > 0"
                                    class="absolute -top-1.5 -right-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-normal text-white shadow-xs animate-in zoom-in duration-200"
                                >
                                    {{ favoriteCount }}
                                </span>
                            </button>
                            <template #placeholder>
                                <button
                                    class="px-3 h-9 font-normal text-xs rounded-button bg-neutral-100 text-neutral-700 hover:bg-neutral-200 transition duration-150 cursor-pointer flex items-center gap-1.5 shadow-xs"
                                >
                                    <iconify-icon
                                        icon="fa7-regular:heart"
                                        class="text-xs transition-colors text-neutral-500"
                                    ></iconify-icon>
                                    <span class="hidden sm:inline">Favoritos</span>
                                </button>
                            </template>
                        </ClientOnly>
                    </NuxtLink>

                    <NuxtLink to="/veiculos">
                        <button
                            class="px-4 h-9 font-normal text-xs rounded-button bg-brand-50 text-brand-700 hover:bg-brand-100 transition duration-150 cursor-pointer flex items-center gap-1.5 shadow-sm shadow-brand-500/5"
                        >
                            <iconify-icon icon="tabler:search" class="text-sm"></iconify-icon>
                            <span>Buscar Carro</span>
                        </button>
                    </NuxtLink>
                </div>
            </div>
        </header>

        <!-- Main Viewport Slot -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer maps -->
        <footer class="bg-white border-t border-neutral-200 pt-16 pb-8">
            <div class="container max-w-7xl mx-auto px-4 md:px-6 space-y-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Description Block -->
                    <div class="space-y-4 md:col-span-2">
                        <NuxtLink to="/" class="flex items-center gap-2 select-none">
                            <div
                                class="w-8 h-8 rounded-lg bg-brand-500 flex items-center justify-center text-white shadow-sm"
                            >
                                <iconify-icon icon="tabler:steering-wheel" class="text-lg"></iconify-icon>
                            </div>
                            <span class="font-black text-base text-neutral-800 tracking-tight">
                                Auto
                                <span class="text-brand-500 font-normal">Market</span>
                            </span>
                        </NuxtLink>
                        <p class="text-xs text-neutral-400 font-normal leading-relaxed max-w-md">
                            A plataforma unificada que conecta compradores a diversas concessionárias locais. Encontre
                            carros com garantia de procedência e realize suas simulações de forma totalmente integrada.
                        </p>
                    </div>

                    <!-- Quick Navigation Links -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-semibold text-neutral-800 uppercase tracking-wider">Navegação</h4>
                        <ul class="space-y-2 text-xs font-normal text-neutral-400">
                            <li v-for="link in navLinks" :key="link.to">
                                <NuxtLink :to="link.to" class="hover:text-brand-600 transition-colors">
                                    {{ link.label }}
                                </NuxtLink>
                            </li>
                        </ul>
                    </div>

                    <!-- Technical constraints info -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-semibold text-neutral-800 uppercase tracking-wider">Segurança</h4>
                        <ul class="space-y-2 text-xs font-normal text-neutral-400">
                            <li class="flex items-center gap-1.5">
                                <iconify-icon
                                    icon="tabler:shield-check"
                                    class="text-emerald-500 text-sm"
                                ></iconify-icon>
                                <span>Concessionárias Vistas</span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <iconify-icon icon="tabler:lock" class="text-emerald-500 text-sm"></iconify-icon>
                                <span>Dados Criptografados</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom bar copyright details -->
                <div
                    class="border-t border-neutral-100 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-normal text-neutral-400"
                >
                    <span>&copy; 2026 AutoMarket Portal. Todos os direitos reservados.</span>
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] bg-neutral-100 px-2 py-0.5 rounded-full border border-neutral-200">
                            PT-BR (BRL)
                        </span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
