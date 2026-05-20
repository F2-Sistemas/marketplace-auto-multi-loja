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
    return stores.value.filter((s) => s.name.toLowerCase().includes(q) || s.host.toLowerCase().includes(q));
});

// Modal state for toggle
const storeToToggle = ref<any>(null);
const showConfirmModal = ref(false);

const confirmToggle = (store: any) => {
    storeToToggle.value = store;
    showConfirmModal.value = true;
};

const executeToggle = async () => {
    if (!storeToToggle.value) return;
    await toggleStoreStatus(storeToToggle.value);
    showConfirmModal.value = false;
    storeToToggle.value = null;
};

// Drawer state for store details
const selectedStore = ref<any>(null);
const showDrawer = ref(false);

const openDetails = (store: any) => {
    selectedStore.value = store;
    showDrawer.value = true;
};
</script>

<template>
    <div class="space-y-6 animate-fadeIn">
        <!-- ACTION BAR -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-900/50 p-4 rounded-xl border border-slate-800"
        >
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
                        <tr
                            class="border-b border-slate-800 text-[10px] uppercase font-semibold text-slate-400 bg-slate-950/40"
                        >
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
                            <td class="py-4 px-6 text-slate-500 font-mono">#{{ store.id }}</td>
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
                                <UiBadge
                                    :variant="store.status === 'ativo' ? 'success' : 'danger'"
                                    :pulse="store.status === 'ativo'"
                                >
                                    {{ store.status }}
                                </UiBadge>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <UiButton
                                    @click="openDetails(store)"
                                    variant="secondary"
                                    size="sm"
                                    class="bg-slate-800 text-slate-300 hover:text-white"
                                >
                                    <iconify-icon icon="tabler:info-circle" class="text-sm"></iconify-icon>
                                    <span>Detalhes</span>
                                </UiButton>
                                <UiButton
                                    @click="confirmToggle(store)"
                                    :variant="store.status === 'ativo' ? 'danger' : 'success'"
                                    size="sm"
                                >
                                    <iconify-icon
                                        :icon="store.status === 'ativo' ? 'tabler:circle-x' : 'tabler:circle-check'"
                                        class="text-sm"
                                    ></iconify-icon>
                                    <span>
                                        {{
                                            store.status === 'ativo'
                                                ? t('stores.actions.deactivate')
                                                : t('stores.actions.reactivate')
                                        }}
                                    </span>
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

        <!-- STORE DETAILS DRAWER -->
        <div v-if="showDrawer && selectedStore" class="fixed inset-0 z-50 flex justify-end">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="showDrawer = false"></div>

            <!-- Drawer Content -->
            <div
                class="relative w-full max-w-md bg-slate-900 h-full shadow-2xl border-l border-slate-800 flex flex-col animate-slideLeft"
            >
                <div class="p-6 border-b border-slate-800 flex justify-between items-center bg-slate-950/50">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <iconify-icon icon="tabler:building-store" class="text-indigo-400"></iconify-icon>
                        Detalhes da Loja
                    </h2>
                    <button @click="showDrawer = false" class="text-slate-400 hover:text-white transition-colors">
                        <iconify-icon icon="tabler:x" class="text-xl"></iconify-icon>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1 space-y-6">
                    <!-- Main Info -->
                    <div>
                        <h3 class="text-2xl font-extrabold text-white mb-1">{{ selectedStore.name }}</h3>
                        <a
                            :href="'https://' + selectedStore.host"
                            target="_blank"
                            class="text-sm text-indigo-400 hover:underline flex items-center gap-1"
                        >
                            {{ selectedStore.host }}
                            <iconify-icon icon="tabler:external-link" class="text-xs"></iconify-icon>
                        </a>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-950/50 rounded-lg border border-slate-800 p-4">
                            <span class="text-[10px] uppercase font-semibold text-slate-500 block">Status</span>
                            <UiBadge :variant="selectedStore.status === 'ativo' ? 'success' : 'danger'" class="mt-1">
                                {{ selectedStore.status }}
                            </UiBadge>
                        </div>
                        <div class="bg-slate-950/50 rounded-lg border border-slate-800 p-4">
                            <span class="text-[10px] uppercase font-semibold text-slate-500 block">Plano Atual</span>
                            <span class="text-sm font-semibold text-white mt-1 block">
                                {{ formatPlan(selectedStore.plan) }}
                            </span>
                        </div>
                    </div>

                    <!-- Billing Info -->
                    <div>
                        <h4 class="text-sm font-semibold text-white border-b border-slate-800 pb-2 mb-4">
                            Assinatura & Faturamento
                        </h4>
                        <div class="bg-slate-950/50 rounded-lg border border-slate-800 p-4 space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-400">Próximo Vencimento</span>
                                <span class="text-white font-medium">10/06/2026</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-400">Método de Pagamento</span>
                                <span class="text-white font-medium flex items-center gap-1">
                                    <iconify-icon icon="tabler:credit-card" class="text-slate-500"></iconify-icon>
                                    Cartão final 4022
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-sm pt-2 border-t border-slate-800/50">
                                <span class="text-slate-400">Última Fatura</span>
                                <UiBadge variant="success">Paga</UiBadge>
                            </div>
                        </div>
                    </div>

                    <!-- Theme Settings -->
                    <div>
                        <h4 class="text-sm font-semibold text-white border-b border-slate-800 pb-2 mb-4">
                            Identidade Visual
                        </h4>
                        <div class="flex items-center gap-3">
                            <div
                                :class="[
                                    'w-8 h-8 rounded-full shadow-inner',
                                    selectedStore.accentColor === 'amber' ? 'bg-amber-400' : '',
                                    selectedStore.accentColor === 'red' ? 'bg-rose-500' : '',
                                    selectedStore.accentColor === 'blue' ? 'bg-blue-500' : '',
                                    selectedStore.accentColor === 'indigo' ? 'bg-indigo-500' : '',
                                ]"
                            ></div>
                            <span class="text-sm text-slate-300 capitalize">
                                Cor Destaque: {{ selectedStore.accentColor }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-800 bg-slate-950/50 flex gap-3">
                    <UiButton @click="showDrawer = false" variant="secondary" class="flex-1">Fechar</UiButton>
                </div>
            </div>
        </div>

        <!-- DEACTIVATION CONFIRM MODAL -->
        <div v-if="showConfirmModal && storeToToggle" class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showConfirmModal = false"></div>

            <!-- Modal Content -->
            <div
                class="relative w-full max-w-sm bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 overflow-hidden animate-zoomIn"
            >
                <div class="p-6 text-center">
                    <div
                        class="w-16 h-16 rounded-full mx-auto flex items-center justify-center mb-4"
                        :class="
                            storeToToggle.status === 'ativo'
                                ? 'bg-rose-500/10 text-rose-500'
                                : 'bg-emerald-500/10 text-emerald-500'
                        "
                    >
                        <iconify-icon
                            :icon="storeToToggle.status === 'ativo' ? 'tabler:alert-triangle' : 'tabler:check'"
                            class="text-3xl"
                        ></iconify-icon>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">
                        {{ storeToToggle.status === 'ativo' ? 'Desativar Loja?' : 'Reativar Loja?' }}
                    </h3>
                    <p class="text-sm text-slate-400 mb-6">
                        Você está prestes a {{ storeToToggle.status === 'ativo' ? 'desativar' : 'reativar' }} a loja
                        <strong class="text-white">{{ storeToToggle.name }}</strong>
                        .
                        {{
                            storeToToggle.status === 'ativo'
                                ? 'Os anúncios não estarão mais visíveis e o acesso será suspenso.'
                                : 'A loja e seus anúncios voltarão a ficar online.'
                        }}
                        Deseja continuar?
                    </p>
                    <div class="flex gap-3">
                        <UiButton @click="showConfirmModal = false" variant="secondary" class="flex-1">
                            Cancelar
                        </UiButton>
                        <UiButton
                            @click="executeToggle"
                            :variant="storeToToggle.status === 'ativo' ? 'danger' : 'primary'"
                            class="flex-1"
                        >
                            Sim, {{ storeToToggle.status === 'ativo' ? 'Desativar' : 'Reativar' }}
                        </UiButton>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
