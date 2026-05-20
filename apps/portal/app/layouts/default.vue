<script setup lang="ts">
import { useRoute } from '#app';
import { onMounted, ref } from 'vue';
import { useI18n } from '~/composables/useI18n';
import { useFavorites } from '~/composables/useFavorites';

const route = useRoute();
const { t } = useI18n();
const { fetchFavorites, favoriteCount } = useFavorites();

const isDropdownOpen = ref(false);
const isMoreInfoExpanded = ref(false);

onMounted(() => {
    fetchFavorites();
});

const navLinks = [
    { label: 'Início', to: '/' },
    { label: 'Veículos', to: '/veiculos' },
    { label: 'Concessionárias', to: '/lojas' },
    { label: 'Notícias', to: '/noticias' },
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
        <!-- Sticky navbar header -->
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
                        class="text-sm font-semibold uppercase tracking-wider transition-colors duration-150 py-1.5 border-b-2"
                        :class="
                            isLinkActive(link.to)
                                ? 'text-brand-600 border-brand-500'
                                : 'text-neutral-500 border-transparent hover:text-neutral-800'
                        "
                    >
                        {{ link.label }}
                    </NuxtLink>
                </nav>

                <!-- Dynamic CTA / Login dropdown -->
                <div class="flex items-center gap-3">
                    <!-- Favorites -->
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

                    <!-- Entrar Dropdown (Webmotors style) -->
                    <div 
                        class="relative" 
                        @mouseenter="isDropdownOpen = true" 
                        @mouseleave="isDropdownOpen = false"
                    >
                        <button 
                            @click="isDropdownOpen = !isDropdownOpen" 
                            class="px-4 h-9 font-semibold text-xs rounded-button bg-neutral-100 text-neutral-700 hover:bg-neutral-200 transition duration-150 cursor-pointer flex items-center gap-1.5 shadow-xs"
                        >
                            <iconify-icon icon="tabler:user" class="text-sm"></iconify-icon>
                            <span>Entrar</span>
                            <iconify-icon 
                                icon="tabler:chevron-down" 
                                :class="['text-[10px] text-neutral-400 transition-transform duration-200', isDropdownOpen ? 'rotate-180' : '']"
                            ></iconify-icon>
                        </button>
                        
                        <div 
                            v-show="isDropdownOpen" 
                            class="absolute right-0 mt-2 w-64 bg-white border border-neutral-200 rounded-xl shadow-lg py-2 z-50 animate-in fade-in slide-in-from-top-1 duration-150"
                        >
                            <div class="px-4 py-2 border-b border-neutral-100">
                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Identifique-se</span>
                            </div>
                            
                            <!-- Sou Cliente -->
                            <NuxtLink to="/favoritos" @click="isDropdownOpen = false" class="block px-4 py-3 hover:bg-neutral-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                                        <iconify-icon icon="tabler:user" class="text-lg"></iconify-icon>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-neutral-800">Sou Cliente</div>
                                        <div class="text-[10px] text-neutral-400 font-normal">Acessar meus favoritos</div>
                                    </div>
                                </div>
                            </NuxtLink>

                            <!-- Sou Lojista -->
                            <NuxtLink to="/admin" @click="isDropdownOpen = false" class="block px-4 py-3 hover:bg-neutral-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                        <iconify-icon icon="tabler:building-store" class="text-lg"></iconify-icon>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-neutral-800">Sou Lojista / Parceiro</div>
                                        <div class="text-[10px] text-neutral-400 font-normal">Acessar meu painel administrativo</div>
                                    </div>
                                </div>
                            </NuxtLink>

                            <div class="border-t border-neutral-100 my-1"></div>

                            <!-- Quero ser um parceiro link -->
                            <NuxtLink to="/quero-anunciar" @click="isDropdownOpen = false" class="block px-4 py-2.5 hover:bg-neutral-50 transition text-center text-xs font-bold text-brand-600">
                                Quero fazer parte da rede &rarr;
                            </NuxtLink>
                        </div>
                    </div>

                    <!-- Search Button -->
                    <NuxtLink to="/veiculos">
                        <button
                            class="px-4 h-9 font-semibold text-xs rounded-button bg-brand-50 text-brand-700 hover:bg-brand-100 transition duration-150 cursor-pointer flex items-center gap-1.5 shadow-sm shadow-brand-500/5"
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

        <!-- Premium Webmotors-style Footer -->
        <footer class="bg-neutral-900 border-t border-neutral-800 pt-16 pb-8 text-neutral-300">
            <div class="container max-w-7xl mx-auto px-4 md:px-6 space-y-12">
                <!-- Top Links Matrix -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <!-- Column 1: Comprar -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Comprar</h4>
                        <ul class="space-y-2 text-xs font-normal text-neutral-400">
                            <li>
                                <NuxtLink to="/veiculos" class="hover:text-white transition">Carros Usados</NuxtLink>
                            </li>
                            <li>
                                <NuxtLink to="/veiculos" class="hover:text-white transition">Carros Novos</NuxtLink>
                            </li>
                            <li>
                                <NuxtLink to="/lojas" class="hover:text-white transition">Concessionárias</NuxtLink>
                            </li>
                            <li>
                                <NuxtLink to="/veiculos" class="hover:text-white transition">Ver Todo o Estoque</NuxtLink>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 2: Vender -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Anunciar</h4>
                        <ul class="space-y-2 text-xs font-normal text-neutral-400">
                            <li>
                                <NuxtLink to="/quero-anunciar" class="hover:text-white transition font-semibold text-brand-400">Quero Anunciar Meu Estoque</NuxtLink>
                            </li>
                            <li>
                                <NuxtLink to="/quero-anunciar#cadastro" class="hover:text-white transition">Criar Minha Loja</NuxtLink>
                            </li>
                            <li>
                                <NuxtLink to="/quero-anunciar" class="hover:text-white transition">Planos e Preços</NuxtLink>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 3: Notícias & Serviços -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Notícias & Serviços</h4>
                        <ul class="space-y-2 text-xs font-normal text-neutral-400">
                            <li>
                                <NuxtLink to="/noticias" class="hover:text-white transition">Portal de Notícias (WM1)</NuxtLink>
                            </li>
                            <li>
                                <NuxtLink to="/noticias" class="hover:text-white transition">Avaliações e Testes</NuxtLink>
                            </li>
                            <li>
                                <NuxtLink to="/noticias" class="hover:text-white transition">Lançamentos de Veículos</NuxtLink>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 4: Institucional -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Institucional</h4>
                        <ul class="space-y-2 text-xs font-normal text-neutral-400">
                            <li>
                                <NuxtLink to="/lojas" class="hover:text-white transition">Nossas Lojas Parceiras</NuxtLink>
                            </li>
                            <li>
                                <NuxtLink to="/admin" class="hover:text-white transition">Acesso ao Backoffice</NuxtLink>
                            </li>
                            <li>
                                <span class="block opacity-75">Suporte ao Lojista</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Expandable Accordion "Mais Informações" -->
                <div class="border-t border-neutral-800 pt-6">
                    <button 
                        @click="isMoreInfoExpanded = !isMoreInfoExpanded"
                        class="w-full flex items-center justify-between text-left text-xs font-bold text-neutral-400 hover:text-white transition py-2"
                    >
                        <span>Mais informações sobre a RedeRevenda</span>
                        <iconify-icon 
                            icon="tabler:chevron-down" 
                            :class="['text-neutral-500 text-sm transition-transform duration-200', isMoreInfoExpanded ? 'rotate-180' : '']"
                        ></iconify-icon>
                    </button>

                    <div 
                        v-show="isMoreInfoExpanded"
                        class="mt-4 text-[11px] text-neutral-500 font-normal leading-relaxed space-y-4 animate-in fade-in duration-200"
                    >
                        <p>
                            A RedeRevenda é a maior e mais completa plataforma de integração de concessionárias de veículos da região. Nosso propósito é conectar lojistas qualificados diretamente a compradores finais com total segurança, transparência e eficiência. Todos os estoques apresentados na plataforma são atualizados em tempo real pelos respectivos proprietários.
                        </p>
                        <p>
                            Para os lojistas, oferecemos uma ferramenta de software como serviço (SaaS) robusta para provisionamento de sites multimarcas instantâneos com domínio personalizado, controle de leads integrados, relatórios e métricas de acessos detalhados e canal de suporte centralizado.
                        </p>
                        <p>
                            Os preços descritos na Tabela FIPE são puramente referenciais e podem sofrer variações conforme estado de conservação, quilometragem e opcionais de cada veículo. A RedeRevenda não realiza transações financeiras diretamente e não retém comissões sobre as vendas de automóveis realizadas entre as partes.
                        </p>
                    </div>
                </div>

                <!-- Bottom Copyright Area -->
                <div
                    class="border-t border-neutral-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-normal text-neutral-500"
                >
                    <div class="flex items-center gap-3 select-none">
                        <div class="w-6 h-6 rounded-md bg-brand-500 flex items-center justify-center text-white">
                            <iconify-icon icon="tabler:steering-wheel" class="text-sm"></iconify-icon>
                        </div>
                        <span class="font-extrabold text-white">AutoMarket <span class="text-neutral-500 font-normal">Portal</span></span>
                    </div>
                    
                    <span>&copy; 2026 AutoMarket Portal. Todos os direitos reservados.</span>
                    
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] bg-neutral-800 text-neutral-400 px-2.5 py-1 rounded-full border border-neutral-700">
                            PT-BR (BRL)
                        </span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
