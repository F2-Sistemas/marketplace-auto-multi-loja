<script setup lang="ts">
import { ref, computed } from 'vue';
import { useApi } from '~/composables/useApi';

const { getApiUrl } = useApi();

const {
    data: ticketsResponse,
    refresh: refreshTickets,
    pending,
} = useFetch<any>(() => getApiUrl('/api/admin/tickets'));

const tickets = computed(() => ticketsResponse.value || []);

// Filtering state
const filterStore = ref('');
const filterCategory = ref('');
const filterStatus = ref('');

const uniqueStores = computed(() => {
    const stores = new Set<string>();
    tickets.value.forEach((t: any) => {
        if (t.store_name) stores.add(t.store_name);
    });
    return Array.from(stores);
});

const filteredTickets = computed(() => {
    return tickets.value.filter((ticket: any) => {
        const matchStore = !filterStore.value || ticket.store_name === filterStore.value;
        const matchCategory = !filterCategory.value || ticket.category === filterCategory.value;
        const matchStatus = !filterStatus.value || ticket.status === filterStatus.value;
        return matchCategory && matchStatus && matchStore;
    });
});

const formatStatus = (status: string) => {
    const map: Record<string, string> = {
        aberto: 'Aberto',
        respondido: 'Respondido',
        aguardando_cliente: 'Aguardando Cliente',
        encerrado: 'Encerrado',
    };
    return map[status] || status;
};

const getStatusVariant = (status: string): 'success' | 'danger' | 'warning' | 'info' | 'indigo' => {
    const map: Record<string, 'success' | 'danger' | 'warning' | 'info' | 'indigo'> = {
        aberto: 'danger',
        respondido: 'success',
        aguardando_cliente: 'warning',
        encerrado: 'info',
    };
    return map[status] || 'info';
};

// Drawer state for ticket details
const selectedTicket = ref<any>(null);
const showDrawer = ref(false);
const ticketDetails = ref<any>(null);
const loadingDetails = ref(false);
const replyMessage = ref('');
const replying = ref(false);
const activeTab = ref<'messages' | 'timeline'>('messages');

const openDetails = async (ticket: any) => {
    selectedTicket.value = ticket;
    showDrawer.value = true;
    loadingDetails.value = true;
    ticketDetails.value = null;

    try {
        // Reusing the general ticket show endpoint
        const response = await $fetch<any>(getApiUrl(`/api/store/tickets/${ticket.id}`));
        ticketDetails.value = response;
    } catch (e) {
        console.error(e);
    } finally {
        loadingDetails.value = false;
    }
};

const sendReply = async () => {
    if (!replyMessage.value.trim() || !selectedTicket.value) return;
    replying.value = true;

    try {
        await $fetch(getApiUrl(`/api/admin/tickets/${selectedTicket.value.id}/reply`), {
            method: 'POST',
            body: { message: replyMessage.value },
        });
        replyMessage.value = '';
        // Refresh details
        await openDetails(selectedTicket.value);
        // Refresh main list
        await refreshTickets();
    } catch (e) {
        console.error(e);
    } finally {
        replying.value = false;
    }
};

const actionLoading = ref(false);
const closeTicket = async () => {
    if (!selectedTicket.value) return;
    actionLoading.value = true;
    try {
        await $fetch(getApiUrl(`/api/store/tickets/${selectedTicket.value.id}/close`), {
            method: 'POST',
        });
        // Refresh details
        await openDetails(selectedTicket.value);
        // Refresh main list
        await refreshTickets();
    } catch (e) {
        console.error(e);
    } finally {
        actionLoading.value = false;
    }
};

const categoryUpdating = ref(false);
const updateCategory = async (newCategory: string) => {
    if (!selectedTicket.value) return;
    categoryUpdating.value = true;
    try {
        await $fetch(getApiUrl(`/api/admin/tickets/${selectedTicket.value.id}/category`), {
            method: 'POST',
            body: { category: newCategory },
        });
        // Refresh details & list
        await openDetails(selectedTicket.value);
        await refreshTickets();
    } catch (e) {
        console.error(e);
    } finally {
        categoryUpdating.value = false;
    }
};

const getEventIcon = (action: string) => {
    const map: Record<string, string> = {
        ticket_created: 'tabler:circle-plus',
        message_added: 'tabler:message-circle',
        status_change: 'tabler:refresh',
        reopen: 'tabler:arrow-back-up',
        rating_added: 'tabler:star',
        category_change: 'tabler:category',
    };
    return map[action] || 'tabler:bell';
};

const getEventColorClass = (action: string) => {
    const map: Record<string, string> = {
        ticket_created: 'text-emerald-400 bg-emerald-950/40 border-emerald-800/65',
        message_added: 'text-blue-400 bg-blue-950/40 border-blue-800/65',
        status_change: 'text-amber-400 bg-amber-950/40 border-amber-800/65',
        reopen: 'text-orange-400 bg-orange-950/40 border-orange-800/65',
        rating_added: 'text-yellow-400 bg-yellow-950/40 border-yellow-800/65',
        category_change: 'text-violet-400 bg-violet-950/40 border-violet-800/65',
    };
    return map[action] || 'text-slate-400 bg-slate-900 border-slate-800';
};
</script>

<template>
    <div class="space-y-6 animate-fadeIn">
        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-white">Central de Suporte</h2>
                <p class="text-sm text-slate-400 mt-1">Gerencie os chamados abertos pelos lojistas</p>
            </div>
            <div class="flex items-center gap-4">
                <button
                    @click="() => refreshTickets()"
                    :disabled="pending"
                    class="flex items-center justify-center w-10 h-10 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 hover:text-white hover:bg-slate-700 transition-colors disabled:opacity-50"
                    title="Recarregar"
                >
                    <iconify-icon
                        icon="tabler:refresh"
                        :class="['text-xl', { 'animate-spin': pending }]"
                    ></iconify-icon>
                </button>
            </div>
        </div>

        <!-- TICKETS TABLE WITH FILTERS -->
        <UiCard class="p-0 overflow-hidden">
            <!-- Search Filters Header -->
            <div
                class="flex flex-col sm:flex-row flex-wrap gap-4 items-center bg-slate-900/60 p-4 border-b border-slate-800"
            >
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">
                        Loja:
                    </label>
                    <select
                        v-model="filterStore"
                        class="h-9 w-full sm:w-44 px-3 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:border-indigo-500 transition-colors"
                    >
                        <option value="">Todas</option>
                        <option value="Portal Global">Portal Global (Geral)</option>
                        <option v-for="store in uniqueStores" :key="store" :value="store">{{ store }}</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">
                        Categoria:
                    </label>
                    <select
                        v-model="filterCategory"
                        class="h-9 w-full sm:w-44 px-3 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:border-indigo-500 transition-colors"
                    >
                        <option value="">Todas</option>
                        <option value="suporte">Dúvida / Suporte</option>
                        <option value="tecnico">Problema Técnico</option>
                        <option value="financeiro">Financeiro / Faturamento</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">
                        Status:
                    </label>
                    <select
                        v-model="filterStatus"
                        class="h-9 w-full sm:w-44 px-3 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none focus:border-indigo-500 transition-colors"
                    >
                        <option value="">Todos</option>
                        <option value="aberto">Aberto</option>
                        <option value="respondido">Respondido</option>
                        <option value="aguardando_cliente">Aguardando Cliente</option>
                        <option value="encerrado">Encerrado</option>
                    </select>
                </div>
                <span class="text-xs text-slate-500 font-medium ml-auto hidden sm:inline">
                    {{ filteredTickets.length }} chamados listados
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="border-b border-slate-800 text-[10px] uppercase font-semibold text-slate-400 bg-slate-950/40"
                        >
                            <th class="py-3 px-6">ID</th>
                            <th class="py-3 px-6">Loja</th>
                            <th class="py-3 px-6">Assunto</th>
                            <th class="py-3 px-6">Categoria</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6 text-right">Data</th>
                            <th class="py-3 px-6"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850 text-xs">
                        <tr
                            v-for="ticket in filteredTickets"
                            :key="ticket.id"
                            class="hover:bg-slate-900/35 transition-colors"
                        >
                            <td class="py-4 px-6 text-slate-500 font-mono">#{{ ticket.id }}</td>
                            <td class="py-4 px-6 font-medium text-white">{{ ticket.store_name || 'Portal Global' }}</td>
                            <td class="py-4 px-6 font-medium text-white">{{ ticket.title }}</td>
                            <td class="py-4 px-6 text-slate-400 capitalize">{{ ticket.category }}</td>
                            <td class="py-4 px-6 text-center">
                                <UiBadge :variant="getStatusVariant(ticket.status)">
                                    {{ formatStatus(ticket.status) }}
                                </UiBadge>
                            </td>
                            <td class="py-4 px-6 text-right text-slate-400">
                                {{ new Date(ticket.created_at).toLocaleDateString() }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <UiButton
                                    @click="openDetails(ticket)"
                                    variant="secondary"
                                    size="sm"
                                    class="bg-slate-800 text-slate-300 hover:text-white"
                                >
                                    <iconify-icon icon="tabler:message-circle" class="text-sm"></iconify-icon>
                                    <span>Atender</span>
                                </UiButton>
                            </td>
                        </tr>

                        <tr v-if="filteredTickets.length === 0">
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                Nenhum chamado encontrado para os filtros selecionados.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </UiCard>

        <!-- TICKET DETAILS DRAWER -->
        <ClientOnly>
            <Teleport to="body">
                <div v-if="showDrawer && selectedTicket" class="fixed inset-0 z-50 flex justify-end">
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="showDrawer = false"></div>

                    <!-- Drawer Content -->
                    <div
                        class="relative w-full max-w-2xl bg-slate-900 h-full shadow-2xl border-l border-slate-800 flex flex-col animate-slideLeft"
                    >
                        <div class="p-6 border-b border-slate-800 flex justify-between items-start bg-slate-950/50">
                            <div>
                                <div class="flex items-center gap-3">
                                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                                        <iconify-icon icon="tabler:headset" class="text-indigo-400"></iconify-icon>
                                        Chamado #{{ selectedTicket.id }}
                                    </h2>
                                    <UiBadge
                                        :variant="
                                            getStatusVariant(ticketDetails?.ticket?.status || selectedTicket.status)
                                        "
                                    >
                                        {{ formatStatus(ticketDetails?.ticket?.status || selectedTicket.status) }}
                                    </UiBadge>
                                </div>
                                <p class="text-sm text-slate-400 mt-1 font-medium">{{ selectedTicket.title }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Origem:
                                    <span class="text-slate-300 font-normal">
                                        {{ selectedTicket.store_name || 'Portal Global' }}
                                    </span>
                                </p>

                                <!-- Category Picker -->
                                <div class="flex items-center gap-2 mt-3">
                                    <span class="text-xs text-slate-500 font-semibold uppercase tracking-wide">
                                        Categoria:
                                    </span>
                                    <select
                                        :value="ticketDetails?.ticket?.category || selectedTicket.category"
                                        @change="(e) => updateCategory((e.target as HTMLSelectElement).value)"
                                        :disabled="categoryUpdating"
                                        class="h-7 px-2 bg-slate-950 border border-slate-800 rounded-md text-xs text-white focus:outline-none focus:border-indigo-500 transition-colors"
                                    >
                                        <option value="suporte">Dúvida / Suporte</option>
                                        <option value="tecnico">Problema Técnico</option>
                                        <option value="financeiro">Financeiro / Faturamento</option>
                                        <option value="outros">Outros</option>
                                    </select>
                                    <iconify-icon
                                        v-if="categoryUpdating"
                                        icon="tabler:loader"
                                        class="animate-spin text-xs text-indigo-400"
                                    ></iconify-icon>
                                </div>
                            </div>
                            <button
                                @click="showDrawer = false"
                                class="text-slate-400 hover:text-white transition-colors mt-1"
                            >
                                <iconify-icon icon="tabler:x" class="text-xl"></iconify-icon>
                            </button>
                        </div>

                        <!-- Tab Controls -->
                        <div
                            class="px-6 border-b border-slate-800 bg-slate-950/30 flex gap-4 text-xs font-semibold text-slate-400"
                        >
                            <button
                                @click="activeTab = 'messages'"
                                :class="[
                                    'py-3 border-b-2 transition-colors',
                                    activeTab === 'messages'
                                        ? 'border-indigo-500 text-white'
                                        : 'border-transparent hover:text-slate-200',
                                ]"
                            >
                                Conversa
                            </button>
                            <button
                                @click="activeTab = 'timeline'"
                                :class="[
                                    'py-3 border-b-2 transition-colors',
                                    activeTab === 'timeline'
                                        ? 'border-indigo-500 text-white'
                                        : 'border-transparent hover:text-slate-200',
                                ]"
                            >
                                Linha do Tempo
                            </button>
                        </div>

                        <div class="p-6 overflow-y-auto flex-1 bg-slate-950/20" v-if="!loadingDetails && ticketDetails">
                            <!-- TAB 1: CONVERSATION -->
                            <div v-if="activeTab === 'messages'" class="space-y-6">
                                <div v-for="msg in ticketDetails.messages" :key="msg.id" class="flex flex-col">
                                    <div
                                        :class="[
                                            'max-w-[85%] rounded-xl p-4',
                                            msg.is_agent
                                                ? 'bg-indigo-500/10 border border-indigo-500/20 self-end'
                                                : 'bg-slate-800 border border-slate-700 self-start',
                                        ]"
                                    >
                                        <div
                                            class="flex items-center gap-2 mb-2 text-xs font-semibold"
                                            :class="msg.is_agent ? 'text-indigo-400' : 'text-slate-400'"
                                        >
                                            <iconify-icon
                                                :icon="msg.is_agent ? 'tabler:shield-check' : 'tabler:user'"
                                            ></iconify-icon>
                                            <span>{{ msg.user_name || (msg.is_agent ? 'Suporte' : 'Cliente') }}</span>
                                            <span class="text-slate-600 font-normal ml-auto">
                                                {{ new Date(msg.created_at).toLocaleString() }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-slate-300 whitespace-pre-wrap">{{ msg.message }}</div>
                                    </div>
                                </div>

                                <div v-if="ticketDetails.messages.length === 0" class="text-center py-8 text-slate-500">
                                    Nenhuma mensagem neste chamado.
                                </div>
                            </div>

                            <!-- TAB 2: TIMELINE -->
                            <div
                                v-if="activeTab === 'timeline'"
                                class="relative pl-8 border-l-2 border-slate-800 space-y-6 ml-4 my-2"
                            >
                                <div v-for="event in ticketDetails.timeline" :key="event.id" class="relative">
                                    <!-- Event Icon Pin -->
                                    <span
                                        :class="[
                                            'absolute -left-[45px] top-0.5 flex items-center justify-center w-8 h-8 rounded-full border',
                                            getEventColorClass(event.action),
                                        ]"
                                    >
                                        <iconify-icon :icon="getEventIcon(event.action)" class="text-sm"></iconify-icon>
                                    </span>
                                    <div>
                                        <span class="text-[10px] text-slate-500 font-mono font-semibold">
                                            {{ new Date(event.created_at).toLocaleString() }}
                                        </span>
                                        <p class="text-sm text-slate-200 font-medium mt-0.5">{{ event.description }}</p>
                                        <div
                                            v-if="event.user_name"
                                            class="flex items-center gap-1.5 mt-1 text-xs text-slate-400 bg-slate-900/40 border border-slate-800/80 rounded-md py-1 px-2.5 w-fit"
                                        >
                                            <iconify-icon icon="tabler:user-cog" class="text-slate-500"></iconify-icon>
                                            <span>
                                                Responsável:
                                                <strong>{{ event.user_name }}</strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="!ticketDetails.timeline || ticketDetails.timeline.length === 0"
                                    class="text-center py-8 text-slate-500"
                                >
                                    Nenhum evento registrado no histórico.
                                </div>
                            </div>
                        </div>

                        <div v-else class="p-6 flex-1 flex items-center justify-center">
                            <iconify-icon
                                icon="tabler:loader"
                                class="text-3xl text-indigo-500 animate-spin"
                            ></iconify-icon>
                        </div>

                        <!-- Reply & Close Box -->
                        <div
                            class="p-6 border-t border-slate-800 bg-slate-950/50"
                            v-if="!loadingDetails && ticketDetails"
                        >
                            <div class="flex flex-col gap-3" v-if="ticketDetails.ticket.status !== 'encerrado'">
                                <textarea
                                    v-model="replyMessage"
                                    placeholder="Digite sua resposta..."
                                    class="w-full bg-slate-900 border border-slate-800 rounded-lg p-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors resize-none"
                                    rows="3"
                                ></textarea>
                                <div class="flex gap-3 justify-between items-center">
                                    <UiButton
                                        @click="closeTicket"
                                        variant="secondary"
                                        :disabled="actionLoading"
                                        class="bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 border-rose-500/20"
                                    >
                                        <iconify-icon
                                            v-if="actionLoading"
                                            icon="tabler:loader"
                                            class="animate-spin"
                                        ></iconify-icon>
                                        <iconify-icon v-else icon="tabler:circle-x"></iconify-icon>
                                        <span>Encerrar Chamado</span>
                                    </UiButton>
                                    <UiButton
                                        @click="sendReply"
                                        variant="primary"
                                        :disabled="replying || !replyMessage.trim()"
                                    >
                                        <iconify-icon
                                            v-if="replying"
                                            icon="tabler:loader"
                                            class="animate-spin"
                                        ></iconify-icon>
                                        <iconify-icon v-else icon="tabler:send"></iconify-icon>
                                        <span>Enviar Resposta</span>
                                    </UiButton>
                                </div>
                            </div>
                            <div
                                v-else
                                class="flex flex-col items-center py-4 bg-slate-900/60 border border-slate-800/80 rounded-xl space-y-2"
                            >
                                <span class="text-slate-500 text-sm flex items-center gap-2">
                                    <iconify-icon icon="tabler:lock"></iconify-icon>
                                    Este chamado foi encerrado.
                                </span>
                                <div v-if="ticketDetails.ticket.rating" class="flex flex-col items-center pt-2">
                                    <span class="text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-1.5">
                                        Avaliação do Lojista
                                    </span>
                                    <div class="flex items-center gap-1 text-yellow-400">
                                        <iconify-icon
                                            v-for="star in 5"
                                            :key="star"
                                            :icon="
                                                star <= ticketDetails.ticket.rating
                                                    ? 'fa7-solid:star'
                                                    : 'fa7-regular:star'
                                            "
                                            class="text-xl"
                                        ></iconify-icon>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1 font-normal">
                                        Avaliado com {{ ticketDetails.ticket.rating }} de 5 estrelas
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>
        </ClientOnly>
    </div>
</template>
