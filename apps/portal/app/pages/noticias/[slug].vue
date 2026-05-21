<script setup lang="ts">
import { computed } from 'vue';
import { useRoute, useAsyncData, createError } from '#app';
import { useApi } from '~/composables/useApi';

const route = useRoute();
const { getApiUrl } = useApi();

// Fetch Single Post by Slug
const { data: post, pending, error } = await useAsyncData(`post-${route.params.slug}`, async () => {
    try {
        const res: any = await $fetch(getApiUrl(`/api/posts/${route.params.slug}`));
        return res;
    } catch (err) {
        throw createError({ statusCode: 404, statusMessage: 'Post não encontrado' });
    }
});

// Fetch popular posts for sidebar
const { data: popularPosts } = await useAsyncData('popular-posts-detail-sidebar', async () => {
    const res: any = await $fetch(getApiUrl('/api/posts'));
    const postsList = res?.data || [];
    return [...postsList]
        .filter((p: any) => p.slug !== route.params.slug)
        .sort((a: any, b: any) => (b.views_count || 0) - (a.views_count || 0))
        .slice(0, 5);
});

const formatDate = (dateStr: string) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('pt-BR', { day: 'numeric', month: 'long', year: 'numeric' });
};

const escapeHtml = (value: string): string => {
    return value
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
};

const renderMarkdown = (content: string): string => {
    const safeContent = escapeHtml(content);
    const withHeadings = safeContent
        .replace(/^### (.*$)/gim, '<h3>$1</h3>')
        .replace(/^## (.*$)/gim, '<h2>$1</h2>')
        .replace(/^# (.*$)/gim, '<h1>$1</h1>');

    const withInlineFormatting = withHeadings
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');

    const blocks = withInlineFormatting
        .split(/\n{2,}/)
        .map((block) => {
            const trimmed = block.trim();

            if (trimmed.startsWith('<h1>') || trimmed.startsWith('<h2>') || trimmed.startsWith('<h3>')) {
                return trimmed;
            }

            if (trimmed.startsWith('<ul>') || trimmed.startsWith('<ol>')) {
                return trimmed;
            }

            const lines = trimmed.split(/\n+/).map((line) => line.trim());
            const paragraph = lines.join('<br>');

            return `<p>${paragraph}</p>`;
        })
        .join('');

    return blocks;
};

const renderedHtml = computed(() => {
    if (!post.value?.content) return '';
    let html = renderMarkdown(post.value.content);

    let links: any[] = [];
    if (post.value.related_links) {
        if (typeof post.value.related_links === 'string') {
            try {
                links = JSON.parse(post.value.related_links);
            } catch {
                links = [];
            }
        } else {
            links = Array.isArray(post.value.related_links) ? post.value.related_links : [];
        }
    }

    if (links.length > 0) {
        let linksHtml = `<div class="related-links-box">
            <span class="related-links-box-title">Leia também</span>
            <ul class="related-links-box-list">`;
        
        links.forEach((link: any) => {
            linksHtml += `<li class="related-links-box-item">
                <a href="${link.url}" class="related-links-box-link">
                    <svg class="w-3.5 h-3.5 text-neutral-400 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 4px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    <span>${link.title}</span>
                </a>
            </li>`;
        });
        
        linksHtml += `</ul></div>`;

        const paragraphs = html.split('</p>');
        if (paragraphs.length > 2) {
            paragraphs[1] += '</p>' + linksHtml;
            html = paragraphs.join('</p>');
        } else {
            html += linksHtml;
        }
    }

    return html;
});

// Share Actions
const shareOnWhatsApp = () => {
    if (import.meta.client) {
        const text = encodeURIComponent(`${post.value?.title} - Leia no AutoMarket: ${window.location.href}`);
        window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
    }
};

const shareOnFacebook = () => {
    if (import.meta.client) {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
    }
};

const copyLink = () => {
    if (import.meta.client) {
        navigator.clipboard.writeText(window.location.href);
        alert('Link copiado para a área de transferência!');
    }
};
</script>

<template>
    <div class="min-h-screen bg-neutral-50 py-8 font-sans">
        <div class="container max-w-7xl mx-auto px-4 md:px-6 space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-[10px] font-bold text-neutral-400 uppercase tracking-wider">
                <NuxtLink to="/" class="hover:text-brand-500 transition">Início</NuxtLink>
                <iconify-icon icon="tabler:chevron-right" class="text-[9px]"></iconify-icon>
                <NuxtLink to="/noticias" class="hover:text-brand-500 transition">Notícias</NuxtLink>
                <iconify-icon icon="tabler:chevron-right" class="text-[9px]"></iconify-icon>
                <span class="text-neutral-500 truncate max-w-xs md:max-w-md">{{ post?.title || 'Carregando...' }}</span>
            </div>

            <!-- Loading State -->
            <div v-if="pending" class="py-24 flex flex-col items-center justify-center space-y-4">
                <iconify-icon icon="tabler:loader" class="text-4xl text-brand-500 animate-spin"></iconify-icon>
                <span class="text-xs text-neutral-400 font-normal">Carregando matéria...</span>
            </div>

            <!-- Article layout -->
            <div v-else-if="post" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <!-- Left column: Main content -->
                <div class="lg:col-span-2 bg-white border border-neutral-200 rounded-2xl p-6 md:p-8 space-y-6 shadow-xs">
                    <!-- Category & Title -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="bg-brand-500 text-white text-[9px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-md">
                                {{ post.category }}
                            </span>
                            <div class="flex items-center gap-2 text-[10px] text-neutral-400 font-semibold uppercase">
                                <span>{{ formatDate(post.published_at) }}</span>
                                <span>&bull;</span>
                                <span class="flex items-center gap-1">
                                    <iconify-icon icon="tabler:eye" class="text-xs"></iconify-icon>
                                    {{ post.views_count || 0 }} visualizações
                                </span>
                            </div>
                        </div>

                        <h1 class="text-2xl md:text-3xl font-black text-neutral-800 tracking-tight leading-tight">
                            {{ post.title }}
                        </h1>

                        <p class="text-xs md:text-sm text-neutral-500 font-normal leading-relaxed border-l-2 border-neutral-300 pl-4 italic">
                            {{ post.summary }}
                        </p>
                    </div>

                    <!-- Share buttons -->
                    <div class="flex items-center gap-2 border-t border-b border-neutral-100 py-3">
                        <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider mr-2">Compartilhar:</span>
                        <button 
                            @click="shareOnWhatsApp" 
                            class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center hover:bg-green-600 transition cursor-pointer"
                        >
                            <iconify-icon icon="tabler:brand-whatsapp" class="text-sm"></iconify-icon>
                        </button>
                        <button 
                            @click="shareOnFacebook" 
                            class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition cursor-pointer"
                        >
                            <iconify-icon icon="tabler:brand-facebook" class="text-sm"></iconify-icon>
                        </button>
                        <button 
                            @click="copyLink" 
                            class="w-7 h-7 rounded-full bg-neutral-200 text-neutral-600 flex items-center justify-center hover:bg-neutral-300 transition cursor-pointer"
                        >
                            <iconify-icon icon="tabler:copy" class="text-sm"></iconify-icon>
                        </button>
                    </div>

                    <!-- Featured Image -->
                    <div class="relative rounded-xl overflow-hidden aspect-video bg-neutral-100">
                        <img 
                            :src="post.image_url || 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=600&auto=format&fit=crop'" 
                            :alt="post.title"
                            class="w-full h-full object-cover"
                        />
                    </div>

                    <!-- Markdown Content Rendered Safe -->
                    <div 
                        class="prose-custom font-normal"
                        v-html="renderedHtml"
                    ></div>

                    <!-- Related links are automatically injected inline in the middle of renderedHtml -->

                    <!-- Back navigation -->
                    <div class="border-t border-neutral-100 pt-6">
                        <NuxtLink 
                            to="/noticias" 
                            class="inline-flex items-center gap-2 text-xs font-semibold text-neutral-500 hover:text-neutral-800 transition"
                        >
                            <iconify-icon icon="tabler:arrow-left"></iconify-icon>
                            <span>Voltar para Notícias</span>
                        </NuxtLink>
                    </div>
                </div>

                <!-- Right column: Sidebar -->
                <div class="space-y-6">
                    <!-- Mais Lidas (Ranked) -->
                    <div class="bg-white border border-neutral-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h3 class="text-sm font-bold text-neutral-800 flex items-center gap-2 border-b border-neutral-100 pb-3">
                            <iconify-icon icon="tabler:trending-up" class="text-brand-500 text-lg"></iconify-icon>
                            <span>Mais Lidas</span>
                        </h3>

                        <div class="space-y-4">
                            <div v-if="!popularPosts || popularPosts.length === 0" class="text-xs text-neutral-400 text-center py-2 font-normal">
                                Nenhuma outra matéria no momento.
                            </div>
                            <NuxtLink 
                                v-else
                                v-for="(pop, index) in popularPosts" 
                                :key="pop.id"
                                :to="`/noticias/${pop.slug}`"
                                class="flex items-start gap-4 group"
                            >
                                <span class="text-2xl font-black text-neutral-200 leading-none w-6 select-none">
                                    {{ index + 1 }}
                                </span>
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold text-brand-500 uppercase tracking-wider block">
                                        {{ pop.category }}
                                    </span>
                                    <h4 class="text-xs font-semibold text-neutral-800 group-hover:text-brand-500 transition-colors leading-snug line-clamp-2">
                                        {{ pop.title }}
                                    </h4>
                                    <span class="text-[9px] text-neutral-400 block font-normal">
                                        {{ pop.views_count || 0 }} visualizações
                                    </span>
                                </div>
                            </NuxtLink>
                        </div>
                    </div>

                    <!-- Back to Catalog Banner -->
                    <div class="bg-neutral-900 rounded-2xl p-6 text-white text-center space-y-4 shadow-md relative overflow-hidden">
                        <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:12px_12px]"></div>
                        <div class="space-y-2 relative z-10">
                            <h4 class="text-base font-bold tracking-tight">Buscando um veículo?</h4>
                            <p class="text-[11px] text-neutral-400 font-light leading-relaxed">
                                Encontre as melhores ofertas integradas de carros novos e seminovos em nossa plataforma.
                            </p>
                        </div>
                        <NuxtLink to="/veiculos" class="inline-block relative z-10">
                            <button class="bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg text-xs font-bold transition shadow-md cursor-pointer border-none">
                                Buscar Carro Agora
                            </button>
                        </NuxtLink>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.prose-custom :deep(h1) {
    font-size: 1.5rem;
    font-weight: 800;
    margin-top: 1.75rem;
    margin-bottom: 0.75rem;
    color: #1f2937;
}
.prose-custom :deep(h2) {
    font-size: 1.25rem;
    font-weight: 700;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    color: #374151;
}
.prose-custom :deep(h3) {
    font-size: 1.125rem;
    font-weight: 700;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
    color: #4b5563;
}
.prose-custom :deep(p) {
    font-size: 0.875rem;
    line-height: 1.65;
    margin-bottom: 1rem;
    color: #4b5563;
}
.prose-custom :deep(ul) {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: #4b5563;
}
.prose-custom :deep(ol) {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: #4b5563;
}
.prose-custom :deep(li) {
    margin-bottom: 0.25rem;
}
.prose-custom :deep(strong) {
    font-weight: 700;
    color: #111827;
}
.prose-custom :deep(blockquote) {
    border-left: 4px solid #d1d5db;
    padding-left: 1rem;
    font-style: italic;
    color: #6b7280;
    margin-bottom: 1rem;
}
.prose-custom :deep(a) {
    color: #c20e1a; /* brand style colour link */
    font-weight: 600;
    text-decoration: underline;
}

.prose-custom :deep(.related-links-box) {
    margin: 2rem 0;
    padding: 1.25rem;
    background-color: #f5f5f5;
    border-left: 4px solid #c20e1a;
    border-top-right-radius: 0.75rem;
    border-bottom-right-radius: 0.75rem;
}
.prose-custom :deep(.related-links-box-title) {
    font-size: 10px;
    font-weight: 700;
    color: #737373;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    display: block;
    margin-bottom: 0.5rem;
}
.prose-custom :deep(.related-links-box-list) {
    list-style-type: none !important;
    padding-left: 0 !important;
    margin: 0 !important;
}
.prose-custom :deep(.related-links-box-item) {
    margin-bottom: 0.5rem !important;
    list-style-type: none !important;
}
.prose-custom :deep(.related-links-box-link) {
    font-size: 0.875rem !important;
    font-weight: 600 !important;
    color: #c20e1a !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.375rem !important;
}
.prose-custom :deep(.related-links-box-link):hover {
    text-decoration: underline !important;
}
</style>
