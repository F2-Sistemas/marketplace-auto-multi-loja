<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useApi } from '../../composables/useApi';
import { useI18n } from '../../composables/useI18n';
import { MdEditor } from 'md-editor-v3';
import 'md-editor-v3/lib/style.css';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const { t } = useI18n();
const { getApiUrl } = useApi();

// State
const posts = ref<any[]>([]);
const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 20,
    total: 0,
});
const searchQuery = ref('');
const filterCategory = ref('Tudo');
const loading = ref(false);

// Filter categories
const categories = ['Tudo', 'Notícias', 'Comparativos', 'Avaliações', 'Guias de Compra'];

// Search debounce
let debounceTimeout: NodeJS.Timeout;
const onSearchInput = () => {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        pagination.value.current_page = 1;
        fetchPosts();
    }, 400);
};

// Fetch Posts
const fetchPosts = async () => {
    loading.value = true;
    try {
        const queryParams = new URLSearchParams({
            page: pagination.value.current_page.toString(),
            search: searchQuery.value,
        });
        
        // Match Backend expectations for category filtering (the admin route can support category as well)
        if (filterCategory.value !== 'Tudo') {
            queryParams.append('category', filterCategory.value);
        }

        const data: any = await $fetch(getApiUrl(`/api/admin/posts?${queryParams.toString()}`));
        posts.value = data.data || [];
        pagination.value = {
            current_page: data.current_page || 1,
            last_page: data.last_page || 1,
            per_page: data.per_page || 20,
            total: data.total || 0,
        };
    } catch (err: any) {
        toast.error('Erro ao carregar notícias. Tente novamente mais tarde.');
    } finally {
        loading.value = false;
    }
};

watch(filterCategory, () => {
    pagination.value.current_page = 1;
    fetchPosts();
});

// Initial fetch
onMounted(() => {
    fetchPosts();
});

// Modal state
const showModal = ref(false);
const isEditing = ref(false);
const editingPostId = ref<number | null>(null);

// Form state
const form = ref({
    title: '',
    slug: '',
    summary: '',
    content: '',
    image_url: '',
    category: 'Notícias',
    status: 'draft',
    published_at: '',
});

// Related links builder state
const relatedLinks = ref<{ title: string; url: string }[]>([]);
const newLinkTitle = ref('');
const newLinkUrl = ref('');

const addRelatedLink = () => {
    if (!newLinkTitle.value.trim() || !newLinkUrl.value.trim()) {
        toast.warn('Preencha o título e a URL do link relacionado.');
        return;
    }
    // Simple URL regex validation
    if (!newLinkUrl.value.startsWith('http://') && !newLinkUrl.value.startsWith('https://') && !newLinkUrl.value.startsWith('/')) {
        toast.warn('A URL deve iniciar com http://, https:// ou /');
        return;
    }
    relatedLinks.value.push({
        title: newLinkTitle.value.trim(),
        url: newLinkUrl.value.trim(),
    });
    newLinkTitle.value = '';
    newLinkUrl.value = '';
};

const removeRelatedLink = (index: number) => {
    relatedLinks.value.splice(index, 1);
};

// Generate slug on blur
const generateSlug = () => {
    if (!form.value.slug.trim() && form.value.title.trim()) {
        form.value.slug = form.value.title
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // remove accents
            .replace(/[^a-z0-9\s-]/g, '') // remove invalid chars
            .replace(/\s+/g, '-') // collapse whitespace and replace by -
            .replace(/-+/g, '-'); // collapse dashes
    }
};

// Open Modals
const openCreateModal = () => {
    isEditing.value = false;
    editingPostId.value = null;
    form.value = {
        title: '',
        slug: '',
        summary: '',
        content: '',
        image_url: '',
        category: 'Notícias',
        status: 'draft',
        published_at: '',
    };
    relatedLinks.value = [];
    showModal.value = true;
};

const openEditModal = (post: any) => {
    isEditing.value = true;
    editingPostId.value = post.id;
    form.value = {
        title: post.title || '',
        slug: post.slug || '',
        summary: post.summary || '',
        content: post.content || '',
        image_url: post.image_url || '',
        category: post.category || 'Notícias',
        status: post.status || 'draft',
        published_at: post.published_at ? post.published_at.substring(0, 16) : '', // format to datetime-local
    };
    // related_links can be string json or already array depending on model cast
    if (typeof post.related_links === 'string') {
        try {
            relatedLinks.value = JSON.parse(post.related_links);
        } catch {
            relatedLinks.value = [];
        }
    } else {
        relatedLinks.value = Array.isArray(post.related_links) ? [...post.related_links] : [];
    }
    showModal.value = true;
};

// Save post
const savePost = async () => {
    if (!form.value.title.trim() || !form.value.summary.trim() || !form.value.content.trim()) {
        toast.error('Preencha os campos obrigatórios (Título, Resumo e Conteúdo).');
        return;
    }

    const payload = {
        ...form.value,
        related_links: relatedLinks.value,
        published_at: form.value.published_at ? form.value.published_at.replace('T', ' ') : null,
    };

    try {
        if (isEditing.value && editingPostId.value) {
            await $fetch(getApiUrl(`/api/admin/posts/${editingPostId.value}`), {
                method: 'PUT',
                body: payload,
            });
            toast.success('Notícia atualizada com sucesso!');
        } else {
            await $fetch(getApiUrl('/api/admin/posts'), {
                method: 'POST',
                body: payload,
            });
            toast.success('Notícia criada com sucesso!');
        }
        showModal.value = false;
        fetchPosts();
    } catch (err: any) {
        const errorMsg = err.data?.message || 'Erro ao salvar notícia.';
        toast.error(errorMsg);
    }
};

// Delete post state
const showDeleteConfirm = ref(false);
const postToDelete = ref<any>(null);

const confirmDelete = (post: any) => {
    postToDelete.value = post;
    showDeleteConfirm.value = true;
};

const executeDelete = async () => {
    if (!postToDelete.value) return;
    try {
        await $fetch(getApiUrl(`/api/admin/posts/${postToDelete.value.id}`), {
            method: 'DELETE',
        });
        toast.success('Notícia excluída com sucesso!');
        showDeleteConfirm.value = false;
        postToDelete.value = null;
        fetchPosts();
    } catch (err: any) {
        toast.error('Erro ao excluir notícia.');
    }
};
</script>

<template>
    <div class="space-y-6 animate-fadeIn">
        <!-- ACTION BAR -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-900/50 p-4 rounded-xl border border-slate-800"
        >
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                        <iconify-icon icon="tabler:search" class="text-base"></iconify-icon>
                    </span>
                    <input
                        v-model="searchQuery"
                        @input="onSearchInput"
                        type="text"
                        placeholder="Buscar por título..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg pl-9 pr-4 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors"
                    />
                </div>

                <div class="flex gap-2">
                    <button
                        v-for="cat in categories"
                        :key="cat"
                        @click="filterCategory = cat"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all duration-200 cursor-pointer',
                            filterCategory === cat
                                ? 'bg-indigo-500/10 border-indigo-500 text-indigo-400'
                                : 'bg-slate-950 border-slate-800 text-slate-400 hover:text-slate-200'
                        ]"
                    >
                        {{ cat }}
                    </button>
                </div>
            </div>

            <UiButton @click="openCreateModal" variant="primary">
                <iconify-icon icon="tabler:circle-plus" class="text-sm"></iconify-icon>
                <span>Nova Notícia</span>
            </UiButton>
        </div>

        <!-- NEWS TABLE -->
        <UiCard class="p-0 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800 bg-slate-900/20 flex justify-between items-center">
                <h3 class="text-sm font-extrabold text-white">
                    Notícias do Portal Central
                </h3>
                <span class="text-xs text-slate-400 font-medium">Total: {{ pagination.total }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="border-b border-slate-800 text-[10px] uppercase font-semibold text-slate-400 bg-slate-950/40"
                        >
                            <th class="py-3 px-6 w-16">ID</th>
                            <th class="py-3 px-6">Título</th>
                            <th class="py-3 px-6">Categoria</th>
                            <th class="py-3 px-6 text-center">Visualizações</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6">Publicação</th>
                            <th class="py-3 px-6 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850 text-xs">
                        <tr
                            v-for="post in posts"
                            :key="post.id"
                            class="hover:bg-slate-900/35 transition-colors"
                        >
                            <td class="py-4 px-6 text-slate-500 font-mono">#{{ post.id }}</td>
                            <td class="py-4 px-6 font-semibold text-white max-w-xs truncate">
                                {{ post.title }}
                            </td>
                            <td class="py-4 px-6 text-slate-400">
                                <span class="bg-slate-800 text-slate-300 px-2 py-0.5 rounded text-[10px] uppercase font-semibold">
                                    {{ post.category }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center font-mono text-slate-400">
                                {{ post.views_count || 0 }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <UiBadge
                                    :variant="post.status === 'published' ? 'success' : 'danger'"
                                    :pulse="post.status === 'published'"
                                >
                                    {{ post.status === 'published' ? 'Publicado' : 'Rascunho' }}
                                </UiBadge>
                            </td>
                            <td class="py-4 px-6 text-slate-500 font-medium">
                                {{ post.published_at ? post.published_at.split(' ')[0] : 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <UiButton
                                    @click="openEditModal(post)"
                                    variant="outline"
                                    size="sm"
                                    class="bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white"
                                >
                                    <iconify-icon icon="tabler:edit" class="text-sm"></iconify-icon>
                                    <span>Editar</span>
                                </UiButton>
                                <UiButton
                                    @click="confirmDelete(post)"
                                    variant="danger"
                                    size="sm"
                                >
                                    <iconify-icon icon="tabler:trash" class="text-sm"></iconify-icon>
                                    <span>Excluir</span>
                                </UiButton>
                            </td>
                        </tr>

                        <tr v-if="posts.length === 0 && !loading">
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                Nenhuma notícia encontrada.
                            </td>
                        </tr>

                        <tr v-if="loading">
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="flex items-center justify-center gap-2">
                                    <iconify-icon icon="tabler:spinner" class="animate-spin text-lg text-indigo-500"></iconify-icon>
                                    <span>Carregando...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div v-if="pagination.last_page > 1" class="px-6 py-4 border-t border-slate-800 bg-slate-950/20 flex justify-between items-center">
                <span class="text-xs text-slate-400">
                    Página {{ pagination.current_page }} de {{ pagination.last_page }}
                </span>
                <div class="flex gap-2">
                    <UiButton
                        :disabled="pagination.current_page === 1"
                        @click="pagination.current_page--; fetchPosts()"
                        variant="outline"
                        size="sm"
                    >
                        Anterior
                    </UiButton>
                    <UiButton
                        :disabled="pagination.current_page === pagination.last_page"
                        @click="pagination.current_page++; fetchPosts()"
                        variant="outline"
                        size="sm"
                    >
                        Próximo
                    </UiButton>
                </div>
            </div>
        </UiCard>

        <!-- CREATE / EDIT MODAL DRAWER -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-end">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="showModal = false"></div>

            <!-- Modal Content (Wide Slide-over) -->
            <div
                class="relative w-full max-w-5xl bg-slate-900 h-full shadow-2xl border-l border-slate-800 flex flex-col animate-slideLeft"
            >
                <div class="p-6 border-b border-slate-800 flex justify-between items-center bg-slate-950/50">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <iconify-icon icon="tabler:news" class="text-indigo-400"></iconify-icon>
                        {{ isEditing ? 'Editar Notícia' : 'Nova Notícia' }}
                    </h2>
                    <button @click="showModal = false" class="text-slate-400 hover:text-white transition-colors">
                        <iconify-icon icon="tabler:x" class="text-xl"></iconify-icon>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left inputs -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-normal text-slate-400 uppercase tracking-wide mb-1.5">
                                    Título da Notícia <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    required
                                    placeholder="Ex: Novo GWM Poer chega para revolucionar..."
                                    @blur="generateSlug"
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-normal text-slate-400 uppercase tracking-wide mb-1.5">
                                    Slug amigável
                                </label>
                                <input
                                    v-model="form.slug"
                                    type="text"
                                    placeholder="Ex: novo-gwm-poer-chega-para-revolucionar"
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 font-mono"
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-normal text-slate-400 uppercase tracking-wide mb-1.5">
                                        Categoria <span class="text-rose-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.category"
                                        class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 cursor-pointer"
                                    >
                                        <option v-for="cat in categories.filter(c => c !== 'Tudo')" :key="cat" :value="cat">
                                            {{ cat }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-normal text-slate-400 uppercase tracking-wide mb-1.5">
                                        Status <span class="text-rose-500">*</span>
                                    </label>
                                    <select
                                        v-model="form.status"
                                        class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 cursor-pointer"
                                    >
                                        <option value="draft">Rascunho</option>
                                        <option value="published">Publicado</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-normal text-slate-400 uppercase tracking-wide mb-1.5">
                                    Data de Publicação
                                </label>
                                <input
                                    v-model="form.published_at"
                                    type="datetime-local"
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200"
                                />
                                <span class="text-[10px] text-slate-500 mt-1 block">Deixe em branco para publicar imediatamente.</span>
                            </div>
                        </div>

                        <!-- Right inputs -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-normal text-slate-400 uppercase tracking-wide mb-1.5">
                                    URL da Imagem de Destaque
                                </label>
                                <input
                                    v-model="form.image_url"
                                    type="url"
                                    placeholder="Ex: https://imagens.rederevenda.com/posts/gwm.jpg"
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-normal text-slate-400 uppercase tracking-wide mb-1.5">
                                    Resumo / Subtítulo <span class="text-rose-500">*</span>
                                </label>
                                <textarea
                                    v-model="form.summary"
                                    rows="4"
                                    placeholder="Insira um resumo curto para a listagem e redes sociais..."
                                    class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 resize-none"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- RELATED LINKS LIST BUILDER -->
                    <div class="bg-slate-950/40 p-4 rounded-xl border border-slate-800 space-y-3">
                        <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <iconify-icon icon="tabler:link" class="text-indigo-400"></iconify-icon>
                            Links Relacionados (Sugestões de leitura)
                        </h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                            <div class="sm:col-span-5">
                                <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Título do Link</label>
                                <input
                                    v-model="newLinkTitle"
                                    type="text"
                                    placeholder="Ex: Ficha técnica completa do GWM Poer"
                                    class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2 px-3 text-xs text-white focus:outline-none focus:border-indigo-500"
                                />
                            </div>
                            <div class="sm:col-span-5">
                                <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">URL do Link</label>
                                <input
                                    v-model="newLinkUrl"
                                    type="text"
                                    placeholder="Ex: /noticias/ficha-tecnica-gwm-poer"
                                    class="w-full bg-slate-950 border border-slate-850 rounded-lg py-2 px-3 text-xs text-white focus:outline-none focus:border-indigo-500"
                                />
                            </div>
                            <div class="sm:col-span-2">
                                <UiButton @click="addRelatedLink" variant="primary" class="w-full py-2">
                                    Adicionar
                                </UiButton>
                            </div>
                        </div>

                        <!-- Current Links List -->
                        <div v-if="relatedLinks.length > 0" class="pt-2 space-y-1.5">
                            <div
                                v-for="(link, idx) in relatedLinks"
                                :key="idx"
                                class="flex justify-between items-center bg-slate-900/60 border border-slate-850 px-3 py-2 rounded-lg text-xs"
                            >
                                <div class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:link" class="text-slate-500"></iconify-icon>
                                    <span class="font-semibold text-slate-200">{{ link.title }}</span>
                                    <span class="text-slate-500 text-[10px] font-mono">({{ link.url }})</span>
                                </div>
                                <button
                                    @click="removeRelatedLink(idx)"
                                    class="text-rose-400 hover:text-rose-300 p-1 rounded hover:bg-rose-500/10 transition-colors"
                                >
                                    <iconify-icon icon="tabler:trash" class="text-sm"></iconify-icon>
                                </button>
                            </div>
                        </div>
                        <p v-else class="text-[11px] text-slate-500 italic">Nenhum link relacionado configurado para este post.</p>
                    </div>

                    <!-- WYSIWYG EDITOR -->
                    <div class="space-y-2">
                        <label class="block text-xs font-normal text-slate-400 uppercase tracking-wide">
                            Conteúdo em Markdown <span class="text-rose-500">*</span>
                        </label>
                        <div class="border border-slate-800 rounded-xl overflow-hidden min-h-[400px]">
                            <ClientOnly>
                                <MdEditor
                                    v-model="form.content"
                                    theme="dark"
                                    language="en-US"
                                    preview-theme="github"
                                    class="h-[450px]"
                                />
                            </ClientOnly>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="p-6 border-t border-slate-800 bg-slate-950/50 flex justify-end gap-3">
                    <UiButton @click="showModal = false" variant="outline" size="sm">
                        Cancelar
                    </UiButton>
                    <UiButton @click="savePost" variant="success" size="sm">
                        <iconify-icon icon="tabler:device-floppy" class="text-sm"></iconify-icon>
                        <span>Salvar Notícia</span>
                    </UiButton>
                </div>
            </div>
        </div>

        <!-- DELETE CONFIRMATION MODAL -->
        <div v-if="showDeleteConfirm && postToDelete" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showDeleteConfirm = false"></div>

            <!-- Modal Content -->
            <div
                class="relative w-full max-w-sm bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 overflow-hidden animate-zoomIn"
            >
                <div class="p-6 text-center">
                    <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center mb-4 bg-rose-500/10 text-rose-500">
                        <iconify-icon icon="tabler:alert-triangle" class="text-3xl"></iconify-icon>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Excluir Notícia?</h3>
                    <p class="text-sm text-slate-400 mb-6">
                        Você está prestes a excluir definitivamente a notícia:
                        <br />
                        <strong class="text-white">{{ postToDelete.title }}</strong>
                        <br /><br />
                        Esta ação não pode ser desfeita. Deseja continuar?
                    </p>
                    <div class="flex gap-3">
                        <UiButton @click="showDeleteConfirm = false" variant="outline" class="flex-1 py-2">
                            Cancelar
                        </UiButton>
                        <UiButton @click="executeDelete" variant="danger" class="flex-1 py-2">
                            Sim, Excluir
                        </UiButton>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Markdown styles within edit preview mode */
.md-editor-dark {
    --md-bk-color: #0b0f19 !important;
    --md-color: #cbd5e1 !important;
    border-color: #1e293b !important;
}
.md-editor {
    border-radius: 0.75rem !important;
}
</style>
