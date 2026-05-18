<script setup lang="ts">
import { ref, computed } from 'vue';

interface Store {
    id: number;
    name: string;
    host: string;
    plan: 'mensal' | 'semestral' | 'anual';
    status: 'ativo' | 'inativo';
    accentColor: string;
    created_at: string;
}

// Fetch central backoffice stores from Core API
const { data: storesResponse, refresh: refreshStores } = await useFetch<any>('http://localhost:8000/api/admin/stores');

const stores = computed<Store[]>(() => {
    if (!storesResponse.value || !storesResponse.value.data) return [];
    return storesResponse.value.data.map((s: any) => {
        // Map theme accent colors to readable names
        let accentName = 'indigo';
        const color = s.settings?.accent_color || '';
        if (color === '#f59e0b' || color === 'amber') accentName = 'amber';
        else if (color === '#e11d48' || color === 'red') accentName = 'red';
        else if (color === '#2563eb' || color === 'blue') accentName = 'blue';

        return {
            id: s.id,
            name: s.name,
            host: s.domain || `${s.slug}.rederevenda.com`,
            plan: s.settings?.plan || 'mensal',
            status: s.is_active ? 'ativo' : 'inativo',
            accentColor: accentName,
            created_at: s.created_at ? s.created_at.split('T')[0] : '2026-05-18',
        };
    });
});

const activeTab = ref<'dashboard' | 'stores' | 'wizard'>('dashboard');

// New store form state
const newStore = ref({
    name: '',
    subdomain: '',
    plan: 'mensal' as 'mensal' | 'semestral' | 'anual',
    accentColor: 'indigo',
});

const showWizardSuccess = ref(false);

const handleCreateStore = async () => {
    try {
        let hexColor = '#4f46e5'; // indigo
        if (newStore.value.accentColor === 'amber') hexColor = '#f59e0b';
        else if (newStore.value.accentColor === 'red') hexColor = '#e11d48';
        else if (newStore.value.accentColor === 'blue') hexColor = '#2563eb';

        await $fetch('http://localhost:8000/api/admin/stores', {
            method: 'POST',
            body: {
                name: newStore.value.name,
                slug: newStore.value.subdomain,
                domain: `${newStore.value.subdomain}.rederevenda.com`,
                settings: {
                    plan: newStore.value.plan,
                    accent_color: hexColor,
                    address: 'Av. das Nações, 1000 - Centro',
                    phone: '(84) 99999-8888',
                    whatsapp_number: '5584999998888',
                    tagline: 'Sua melhor escolha em seminovos de procedência!',
                },
            },
        });

        showWizardSuccess.value = true;
        await refreshStores();

        // Reset form
        setTimeout(() => {
            showWizardSuccess.value = false;
            newStore.value = {
                name: '',
                subdomain: '',
                plan: 'mensal',
                accentColor: 'indigo',
            };
            activeTab.value = 'stores';
        }, 2000);
    } catch (error) {
        console.error('Erro ao provisionar tenant:', error);
        alert('Erro ao provisionar o tenant. Certifique-se de que a API está ativa e o subdomínio não está em uso.');
    }
};

const toggleStoreStatus = async (store: Store) => {
    try {
        await $fetch(`http://localhost:8000/api/admin/stores/${store.id}/toggle`, {
            method: 'POST',
        });
        await refreshStores();
    } catch (error) {
        console.error('Erro ao alternar status da loja:', error);
        alert('Erro ao alterar status da loja. Certifique-se de que a API está ativa.');
    }
};

const formatPlan = (value: string) => {
    const map: Record<string, string> = {
        mensal: 'Mensal - R$ 199/mês',
        semestral: 'Semestral - R$ 999/sem',
        anual: 'Anual - R$ 1.800/ano',
    };
    return map[value] || value;
};
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100 font-['Outfit'] antialiased flex flex-col md:flex-row">
        <!-- SIDEBAR NAVIGATION -->
        <aside
            class="w-full md:w-64 bg-slate-900 border-b md:border-b-0 md:border-r border-slate-800 flex flex-col justify-between"
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
                            AutoHub
                        </span>
                        <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-widest leading-none">
                            Backoffice
                        </span>
                    </div>
                </div>

                <!-- NAVIGATION ITEMS -->
                <nav class="p-4 space-y-1.5">
                    <button
                        @click="activeTab = 'dashboard'"
                        :class="[
                            'w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition',
                            activeTab === 'dashboard'
                                ? 'bg-indigo-600 text-white shadow shadow-indigo-600/20'
                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-850',
                        ]"
                    >
                        <iconify-icon icon="tabler:chart-bar" class="text-lg"></iconify-icon>
                        <span>Painel Principal</span>
                    </button>

                    <button
                        @click="activeTab = 'stores'"
                        :class="[
                            'w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition',
                            activeTab === 'stores'
                                ? 'bg-indigo-600 text-white shadow shadow-indigo-600/20'
                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-850',
                        ]"
                    >
                        <iconify-icon icon="tabler:building-store" class="text-lg"></iconify-icon>
                        <span>Gerenciar Lojas</span>
                    </button>

                    <button
                        @click="activeTab = 'wizard'"
                        :class="[
                            'w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition',
                            activeTab === 'wizard'
                                ? 'bg-indigo-600 text-white shadow shadow-indigo-600/20'
                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-850',
                        ]"
                    >
                        <iconify-icon icon="tabler:circle-plus" class="text-lg"></iconify-icon>
                        <span>Nova Loja (Tenant)</span>
                    </button>
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
                        <span class="font-bold text-xs text-white block">Tiago Silva</span>
                        <span class="text-[10px] text-slate-500 block">Administrador Central</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT BODY -->
        <main class="flex-1 p-6 md:p-8 space-y-8 max-w-7xl mx-auto w-full">
            <!-- TOP STATUS BAR -->
            <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-white">
                        <span v-if="activeTab === 'dashboard'">Painel de Métricas Central</span>
                        <span v-else-if="activeTab === 'stores'">Lojas e Revendas Cadastradas</span>
                        <span v-else-if="activeTab === 'wizard'">Adicionar Novo Tenant (Multi-Loja)</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Estatísticas do ecossistema de marketplace em tempo real</p>
                </div>

                <span
                    class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1.5"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                    Conectado ao PostgreSQL
                </span>
            </header>

            <!-- TAB 1: DASHBOARD METRICS -->
            <div v-if="activeTab === 'dashboard'" class="space-y-8 animate-in fade-in duration-300">
                <!-- KPI METRICS GRID -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 text-slate-800/20 text-8xl font-bold">
                            <iconify-icon icon="tabler:building-store"></iconify-icon>
                        </div>
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider font-extrabold block">
                            Lojas Ativas
                        </span>
                        <div class="text-3xl font-extrabold text-white mt-2">{{ stores.length }}</div>
                        <p class="text-[10px] text-emerald-400 font-semibold mt-2 flex items-center gap-1">
                            <iconify-icon icon="tabler:trending-up"></iconify-icon>
                            <span>+1 criada esta semana</span>
                        </p>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 text-slate-800/20 text-8xl font-bold">
                            <iconify-icon icon="tabler:car"></iconify-icon>
                        </div>
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider font-extrabold block">
                            Veículos Anunciados
                        </span>
                        <div class="text-3xl font-extrabold text-white mt-2">2.482</div>
                        <p class="text-[10px] text-emerald-400 font-semibold mt-2 flex items-center gap-1">
                            <iconify-icon icon="tabler:trending-up"></iconify-icon>
                            <span>+18% de aumento este mês</span>
                        </p>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 text-slate-800/20 text-8xl font-bold">
                            <iconify-icon icon="tabler:users"></iconify-icon>
                        </div>
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider font-extrabold block">
                            Propostas / Leads
                        </span>
                        <div class="text-3xl font-extrabold text-white mt-2">1.890</div>
                        <p class="text-[10px] text-emerald-400 font-semibold mt-2 flex items-center gap-1">
                            <iconify-icon icon="tabler:trending-up"></iconify-icon>
                            <span>Conversão de leads de 24%</span>
                        </p>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 text-slate-800/20 text-8xl font-bold">
                            <iconify-icon icon="tabler:cash"></iconify-icon>
                        </div>
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider font-extrabold block">
                            Faturamento Mensal
                        </span>
                        <div class="text-3xl font-extrabold text-amber-450 mt-2">R$ 15.420</div>
                        <p class="text-[10px] text-amber-400 font-semibold mt-2 flex items-center gap-1">
                            <iconify-icon icon="tabler:shield-lock"></iconify-icon>
                            <span>Recorrência Mensal Assinaturas</span>
                        </p>
                    </div>
                </div>

                <!-- RECENT STORES GRID & DYNAMIC PLAN CHARTS -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 lg:col-span-2">
                        <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                            <iconify-icon icon="tabler:device-analytics" class="text-indigo-400"></iconify-icon>
                            <span>Visão Geral do Faturamento de Assinaturas</span>
                        </h3>

                        <div class="space-y-4 mt-6">
                            <div
                                class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase"
                            >
                                <span>Plano Anual (45% das revendas)</span>
                                <span class="text-white">R$ 6.939,00</span>
                            </div>
                            <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-gradient-to-r from-indigo-500 to-indigo-400 rounded-full"
                                    style="width: 45%"
                                ></div>
                            </div>

                            <div
                                class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase"
                            >
                                <span>Plano Semestral (35% das revendas)</span>
                                <span class="text-white">R$ 5.397,00</span>
                            </div>
                            <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-gradient-to-r from-amber-400 to-amber-500 rounded-full"
                                    style="width: 35%"
                                ></div>
                            </div>

                            <div
                                class="flex items-center justify-between text-xs text-slate-400 font-semibold uppercase"
                            >
                                <span>Plano Mensal (20% das revendas)</span>
                                <span class="text-white">R$ 3.084,00</span>
                            </div>
                            <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-gradient-to-r from-emerald-450 to-emerald-400 rounded-full"
                                    style="width: 20%"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-base font-bold text-white mb-2">Suporte & Integração</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                Cada nova loja cadastrada gera automaticamente um banco de dados isolado logicamente,
                                sitemaps configurados no Redis e um site exclusivo no subdomínio correspondente.
                            </p>
                        </div>

                        <button
                            @click="activeTab = 'wizard'"
                            class="mt-6 w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-bold text-xs text-white flex items-center justify-center gap-2 active:scale-98 transition shadow-lg shadow-indigo-600/20"
                        >
                            <iconify-icon icon="tabler:circle-plus"></iconify-icon>
                            <span>Adicionar Nova Revenda</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- TAB 2: STORES MANAGEMENT CRUD TABLE -->
            <div
                v-if="activeTab === 'stores'"
                class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden animate-in fade-in duration-300"
            >
                <div class="p-6 border-b border-slate-800 flex justify-between items-center flex-wrap gap-4">
                    <h3 class="font-bold text-white text-base">Relação de Tenants de Revendas</h3>
                    <button
                        @click="activeTab = 'wizard'"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition"
                    >
                        <iconify-icon icon="tabler:circle-plus"></iconify-icon>
                        <span>Novo Tenant</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-950/60 text-[10px] font-extrabold uppercase text-slate-500 tracking-wider border-b border-slate-800"
                            >
                                <th class="py-4 px-6">ID</th>
                                <th class="py-4 px-6">Revenda</th>
                                <th class="py-4 px-6">Domínio / Host</th>
                                <th class="py-4 px-6">Plano Ativo</th>
                                <th class="py-4 px-6">Data Cadastro</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-850">
                            <tr v-for="s in stores" :key="s.id" class="hover:bg-slate-850/40 transition-colors">
                                <td class="py-4 px-6 text-xs text-slate-400 font-bold">#{{ s.id }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            :class="[
                                                'w-8 h-8 rounded-lg bg-slate-950 flex items-center justify-center font-bold text-xs',
                                                `text-${s.accentColor}-400`,
                                            ]"
                                        >
                                            <iconify-icon icon="tabler:building-store"></iconify-icon>
                                        </div>
                                        <div>
                                            <span class="font-bold text-sm text-white block">{{ s.name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-300 font-semibold">{{ s.host }}</td>
                                <td class="py-4 px-6 text-xs text-indigo-400 font-semibold">
                                    {{ formatPlan(s.plan) }}
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-400">{{ s.created_at }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span
                                        :class="[
                                            'px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider',
                                            s.status === 'ativo'
                                                ? 'bg-emerald-500/10 text-emerald-400'
                                                : 'bg-rose-500/10 text-rose-400',
                                        ]"
                                    >
                                        {{ s.status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <button
                                        @click="toggleStoreStatus(s)"
                                        :class="[
                                            'px-2.5 py-1 rounded text-[10px] font-bold transition active:scale-95',
                                            s.status === 'ativo'
                                                ? 'bg-slate-800 hover:bg-rose-950/20 text-rose-400'
                                                : 'bg-indigo-600/10 hover:bg-indigo-600/20 text-indigo-400',
                                        ]"
                                    >
                                        {{ s.status === 'ativo' ? 'Desativar' : 'Reativar' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 3: TENANT WIZARD FORM -->
            <div
                v-if="activeTab === 'wizard'"
                class="max-w-xl mx-auto bg-slate-900 border border-slate-800 rounded-3xl p-6 md:p-8 animate-in fade-in duration-300"
            >
                <h3 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
                    <iconify-icon icon="tabler:wizard" class="text-indigo-400 text-xl"></iconify-icon>
                    <span>Assistente de Criação de Tenant</span>
                </h3>
                <p class="text-xs text-slate-500 mb-6">
                    Preencha os campos abaixo para provisionar um novo storefront e domínio para a revenda parceira.
                </p>

                <div
                    v-if="showWizardSuccess"
                    class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl flex items-center gap-2 mb-6 animate-in fade-in duration-200"
                >
                    <iconify-icon icon="tabler:circle-check" class="text-xl"></iconify-icon>
                    <span class="text-sm font-semibold">Tenant criado e ativado com sucesso no banco de dados!</span>
                </div>

                <form @submit.prevent="handleCreateStore" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1.5">
                            Nome da Revenda
                        </label>
                        <input
                            v-model="newStore.name"
                            required
                            type="text"
                            placeholder="Ex: Euro Motors"
                            class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none transition-all"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1.5">
                            Subdomínio (Host)
                        </label>
                        <div class="flex">
                            <input
                                v-model="newStore.subdomain"
                                required
                                type="text"
                                placeholder="Ex: euro-motors"
                                class="flex-1 bg-slate-950 border border-slate-850 focus:border-indigo-500 rounded-l-xl py-3 px-4 text-sm text-slate-200 focus:outline-none transition-all"
                            />
                            <span
                                class="bg-slate-800 border border-l-0 border-slate-850 px-4 flex items-center text-xs text-slate-400 rounded-r-xl"
                            >
                                .rederevenda.com
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase mb-1.5">
                                Plano Assinatura
                            </label>
                            <select
                                v-model="newStore.plan"
                                class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none transition-all"
                            >
                                <option value="mensal">Mensal (R$ 199/mês)</option>
                                <option value="semestral">Semestral (R$ 999/sem)</option>
                                <option value="anual">Anual (R$ 1.800/ano)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase mb-1.5">
                                Cor Temática
                            </label>
                            <select
                                v-model="newStore.accentColor"
                                class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none transition-all"
                            >
                                <option value="indigo">Indigo (Padrão)</option>
                                <option value="amber">Laranja / Amber</option>
                                <option value="red">Vermelho SP</option>
                                <option value="blue">Azul Euro</option>
                            </select>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-bold text-sm text-white flex items-center justify-center gap-2 active:scale-[0.99] transition shadow-lg shadow-indigo-600/20"
                    >
                        <iconify-icon icon="tabler:database-cog"></iconify-icon>
                        <span>Provisionar Tenant Automático</span>
                    </button>
                </form>
            </div>
        </main>
    </div>
</template>
