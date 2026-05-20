<script setup lang="ts">
import { ref, watch } from 'vue';
import { useApi } from '~/composables/useApi';
import { useAsyncData } from '#app';

const { getApiUrl } = useApi();

const activeCategory = ref('Tudo');
const searchQuery = ref('');
const currentPage = ref(1);

const categories = ['Tudo', 'Notícias', 'Segredos', 'Lançamentos', 'Avaliações'];

// Debounce search query changes
const debouncedSearch = ref('');
let searchTimeout: any = null;
watch(searchQuery, (newVal) => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        debouncedSearch.value = newVal;
        currentPage.value = 1;
    }, 450);
});

// Fetch posts
const { data: postsData, pending, refresh } = await useAsyncData('posts-list', async () => {
    let url = `${getApiUrl('/api/posts')}?page=${currentPage.value}`;
    if (activeCategory.value !== 'Tudo') {
        url += `&category=${encodeURIComponent(activeCategory.value)}`;
    }
    if (debouncedSearch.value) {
        url += `&search=${encodeURIComponent(debouncedSearch.value)}`;
    }
    return $fetch(url);
}, {
    watch: [activeCategory, debouncedSearch, currentPage]
});

// Fetch popular posts (most read)
const { data: popularPosts } = await useAsyncData('popular-posts-list', async () => {
    // We fetch without pagination filters to get a pool of posts, then sort by views_count
    const res: any = await $fetch(getApiUrl('/api/posts'));
    const postsList = res?.data || [];
    return [...postsList].sort((a: any, b: any) => (b.views_count || 0) - (a.views_count || 0)).slice(0, 5);
});

const formatDate = (dateStr: string) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('pt-BR', { day: 'numeric', month: 'long', year: 'numeric' });
};

const changeCategory = (category: string) => {
    activeCategory.value = category;
    currentPage.value = 1;
};
</script>

<template>
    <div class="min-h-screen bg-neutral-50 py-8 font-sans">
        <div class="container max-w-7xl mx-auto px-4 md:px-6 space-y-8">
            <!-- Header section & Search -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-neutral-200 pb-6">
                <div class="space-y-2">
                    <span class="text-xs font-bold text-brand-500 uppercase tracking-widest">Canal de Notícias</span>
                    <h1 class="text-3xl font-black text-neutral-800 tracking-tight flex items-center gap-2">
                        <iconify-icon icon="tabler:news" class="text-brand-500"></iconify-icon>
                        <span>Notícias & Testes</span>
                    </h1>
                    <p class="text-xs text-neutral-400 font-normal">
                        Fique por dentro de tudo que acontece no mundo dos motores, lançamentos e segredos.
                    </p>
                </div>

                <!-- Search Input -->
                <div class="relative w-full md:w-80">
                    <iconify-icon icon="tabler:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-sm"></iconify-icon>
                    <input 
                        type="text" 
                        v-model="searchQuery"
                        placeholder="Buscar matérias..." 
                        class="w-full pl-9 pr-4 py-2 text-xs rounded-lg border border-neutral-200 bg-white placeholder-neutral-400 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition duration-150"
                    />
                </div>
            </div>

            <!-- Categories selector -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <button
                    v-for="cat in categories"
                    :key="cat"
                    @click="changeCategory(cat)"
                    class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition cursor-pointer select-none"
                    :class="activeCategory === cat 
                        ? 'bg-neutral-900 text-white shadow-sm' 
                        : 'bg-white border border-neutral-200 text-neutral-600 hover:bg-neutral-100'"
                >
                    {{ cat }}
                </button>
            </div>

            <!-- Loading overlay or list state -->
            <div v-if="pending" class="py-24 flex flex-col items-center justify-center space-y-4">
                <iconify-icon icon="tabler:loader" class="text-4xl text-brand-500 animate-spin"></iconify-icon>
                <span class="text-xs text-neutral-400 font-normal">Buscando as últimas novidades...</span>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <!-- Main news feed (left 2 cols) -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Featured Post (Destaque) - Only show on page 1 when no filters/search active -->
                    <div 
                        v-if="currentPage === 1 && postsData?.data?.length > 0 && activeCategory === 'Tudo' && !debouncedSearch"
                        class="group bg-white border border-neutral-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition duration-220 cursor-pointer flex flex-col"
                    >
                        <NuxtLink :to="`/noticias/${postsData.data[0].slug}`">
                            <div class="relative aspect-video w-full overflow-hidden bg-neutral-100">
                                <img 
                                    :src="postsData.data[0].image_url || 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=600&auto=format&fit=crop'"
                                    :alt="postsData.data[0].title"
                                    class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-300"
                                    loading="lazy"
                                />
                                <span class="absolute top-4 left-4 bg-brand-500 text-white text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md">
                                    {{ postsData.data[0].category }}
                                </span>
                            </div>
                            <div class="p-6 md:p-8 space-y-3">
                                <div class="flex items-center gap-2 text-[10px] text-neutral-400 font-semibold uppercase">
                                    <span>{{ formatDate(postsData.data[0].published_at) }}</span>
                                    <span>&bull;</span>
                                    <span class="flex items-center gap-1">
                                        <iconify-icon icon="tabler:eye" class="text-xs"></iconify-icon>
                                        {{ postsData.data[0].views_count || 0 }} visualizações
                                    </span>
                                </div>
                                <h2 class="text-xl md:text-2xl font-black text-neutral-800 tracking-tight leading-tight group-hover:text-brand-500 transition-colors">
                                    {{ postsData.data[0].title }}
                                </h2>
                                <p class="text-xs text-neutral-500 font-normal leading-relaxed">
                                    {{ postsData.data[0].summary }}
                                </p>
                            </div>
                        </NuxtLink>
                    </div>

                    <!-- News Grid List -->
                    <div class="space-y-6">
                        <h3 class="text-sm font-bold text-neutral-700 border-b border-neutral-200 pb-2">
                            {{ activeCategory === 'Tudo' && !debouncedSearch ? 'Outras Matérias' : 'Matérias Encontradas' }}
                        </h3>

                        <!-- Empty State -->
                        <div 
                            v-if="!postsData?.data || postsData.data.length === 0" 
                            class="bg-white border border-neutral-200 rounded-2xl p-12 text-center space-y-3"
                        >
                            <iconify-icon icon="tabler:news-off" class="text-4xl text-neutral-300"></iconify-icon>
                            <p class="text-xs text-neutral-400 font-normal">Nenhuma matéria encontrada com os filtros selecionados.</p>
                        </div>

                        <!-- Grid -->
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div 
                                v-for="(post, index) in (currentPage === 1 && activeCategory === 'Tudo' && !debouncedSearch ? postsData.data.slice(1) : postsData.data)" 
                                :key="post.id"
                                class="group bg-white border border-neutral-200 rounded-xl overflow-hidden shadow-xs hover:shadow-sm hover:border-neutral-300 transition duration-200 flex flex-col h-full"
                            >
                                <NuxtLink :to="`/noticias/${post.slug}`" class="flex flex-col h-full">
                                    <div class="relative aspect-video w-full overflow-hidden bg-neutral-100">
                                        <img 
                                            :src="post.image_url || 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=600&auto=format&fit=crop'" 
                                            :alt="post.title"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            loading="lazy"
                                        />
                                        <span class="absolute top-3 left-3 bg-neutral-900/80 backdrop-blur-xs text-white text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md">
                                            {{ post.category }}
                                        </span>
                                    </div>
                                    <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2 text-[9px] text-neutral-400 font-semibold uppercase">
                                                <span>{{ formatDate(post.published_at) }}</span>
                                                <span>&bull;</span>
                                                <span class="flex items-center gap-1">
                                                    <iconify-icon icon="tabler:eye" class="text-xs"></iconify-icon>
                                                    {{ post.views_count || 0 }}
                                                </span>
                                            </div>
                                            <h4 class="text-sm font-bold text-neutral-800 leading-snug group-hover:text-brand-500 transition-colors line-clamp-2">
                                                {{ post.title }}
                                            </h4>
                                            <p class="text-xs text-neutral-400 font-normal line-clamp-2 leading-relaxed">
                                                {{ post.summary }}
                                            </p>
                                        </div>
                                        <span class="text-[10px] font-semibold text-brand-500 group-hover:underline pt-2">
                                            Ler matéria &rarr;
                                        </span>
                                    </div>
                                </NuxtLink>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination Controls -->
                    <div 
                        v-if="postsData?.last_page > 1" 
                        class="flex items-center justify-between border-t border-neutral-200 pt-6"
                    >
                        <button 
                            @click="currentPage--" 
                            :disabled="currentPage === 1"
                            class="px-4 py-2 border border-neutral-200 bg-white text-xs font-semibold rounded-lg text-neutral-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-neutral-50 transition"
                        >
                            &larr; Anterior
                        </button>
                        <span class="text-xs text-neutral-400 font-normal">
                            Página {{ currentPage }} de {{ postsData.last_page }}
                        </span>
                        <button 
                            @click="currentPage++" 
                            :disabled="currentPage === postsData.last_page"
                            class="px-4 py-2 border border-neutral-200 bg-white text-xs font-semibold rounded-lg text-neutral-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-neutral-50 transition"
                        >
                            Próxima &rarr;
                        </button>
                    </div>
                </div>

                <!-- Sidebar (right col) -->
                <div class="space-y-6">
                    <!-- Mais Lidas Card (Ranked) -->
                    <div class="bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h3 class="text-sm font-bold text-neutral-800 flex items-center gap-2 border-b border-neutral-100 pb-3">
                            <iconify-icon icon="tabler:trending-up" class="text-brand-500 text-lg"></iconify-icon>
                            <span>Mais Lidas</span>
                        </h3>

                        <div class="space-y-4">
                            <NuxtLink 
                                v-for="(post, index) in popularPosts" 
                                :key="post.id"
                                :to="`/noticias/${post.slug}`"
                                class="flex items-start gap-4 group"
                            >
                                <span class="text-2xl font-black text-neutral-200 leading-none w-6 select-none">
                                    {{ index + 1 }}
                                </span>
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold text-brand-500 uppercase tracking-wider block">
                                        {{ post.category }}
                                    </span>
                                    <h4 class="text-xs font-semibold text-neutral-800 group-hover:text-brand-500 transition-colors leading-snug line-clamp-2">
                                        {{ post.title }}
                                    </h4>
                                    <span class="text-[9px] text-neutral-400 block font-normal">
                                        {{ post.views_count || 0 }} visualizações
                                    </span>
                                </div>
                            </NuxtLink>
                        </div>
                    </div>

                    <!-- Promo Banner -->
                    <div class="bg-gradient-to-br from-brand-500 to-brand-600 rounded-2xl p-6 text-white text-center space-y-4 shadow-md relative overflow-hidden">
                        <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px]"></div>
                        <div class="space-y-2 relative z-10">
                            <h4 class="text-lg font-bold tracking-tight">Tem uma concessionária?</h4>
                            <p class="text-[11px] text-brand-100 font-light leading-relaxed">
                                Anuncie seus veículos na melhor plataforma regional de automóveis. Crie seu site em minutos!
                            </p>
                        </div>
                        <NuxtLink to="/quero-anunciar" class="inline-block relative z-10">
                            <button class="bg-white text-brand-600 hover:bg-neutral-50 px-4 py-2 rounded-lg text-xs font-bold transition shadow-md cursor-pointer">
                                Cadastrar Minha Loja
                            </button>
                        </NuxtLink>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
