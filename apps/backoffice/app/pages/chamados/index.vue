<script setup lang="ts">
import { ref, computed } from 'vue';
import { useApi } from '~/composables/useApi';

const { getApiUrl } = useApi();

const {
    data: ticketsResponse,
    refresh: refreshTickets,
    pending,
} = useFetch<any>(() => getApiUrl('/api/store/tickets'));

const tickets = computed(() => ticketsResponse.value || []);

// Filtering state
const filterCategory = ref('');
const filterStatus = ref('');

const filteredTickets = computed(() => {
    return tickets.value.filter((ticket: any) => {
        const matchCategory = !filterCategory.value || ticket.category === filterCategory.value;
        const matchStatus = !filterStatus.value || ticket.status === filterStatus.value;
        return matchCategory && matchStatus;
    });
});

const formatStatus = (status: string) => {
    const map: Record<string, string> = {
        aberto: 'Aberto',
        respondido: 'Respondido',
        aguardando_cliente: 'Aguardando sua Resposta',
        encerrado: 'Encerrado',
    };
    return map[status] || status;
};

const getStatusVariant = (status: string): 'success' | 'danger' | 'warning' | 'info' | 'indigo' => {
    const map: Record<string, 'success' | 'danger' | 'warning' | 'info' | 'indigo'> = {
        aberto: 'warning',
        respondido: 'success',
        aguardando_cliente: 'danger',
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
        await $fetch(getApiUrl(`/api/store/tickets/${selectedTicket.value.id}/reply`), {
            method: 'POST',
            body: { message: replyMessage.value },
        });
        replyMessage.value = '';
        await openDetails(selectedTicket.value);
        await refreshTickets();
    } catch (e) {
        console.error(e);
    } finally {
        replying.value = false;
    }
};

// Action operations (reopen, close, rate)
const actionLoading = ref(false);
const ratingHover = ref(0);
const ratingSubmitting = ref(false);

const closeTicket = async () => {
    if (!selectedTicket.value) return;
    actionLoading.value = true;
    try {
        await $fetch(getApiUrl(`/api/store/tickets/${selectedTicket.value.id}/close`), {
            method: 'POST',
        });
        await openDetails(selectedTicket.value);
        await refreshTickets();
    } catch (e) {
        console.error(e);
    } finally {
        actionLoading.value = false;
    }
};

const reopenTicket = async () => {
    if (!selectedTicket.value) return;
    actionLoading.value = true;
    try {
        await $fetch(getApiUrl(`/api/store/tickets/${selectedTicket.value.id}/reopen`), {
            method: 'POST',
        });
        await openDetails(selectedTicket.value);
        await refreshTickets();
    } catch (e) {
        console.error(e);
    } finally {
        actionLoading.value = false;
    }
};

const rateTicket = async (rating: number) => {
    if (!selectedTicket.value) return;
    ratingSubmitting.value = true;
    try {
        await $fetch(getApiUrl(`/api/store/tickets/${selectedTicket.value.id}/rate`), {
            method: 'POST',
            body: { rating },
        });
        await openDetails(selectedTicket.value);
        await refreshTickets();
    } catch (e) {
        console.error(e);
    } finally {
        ratingSubmitting.value = false;
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

// New Ticket Modal State
const showNewTicketModal = ref(false);
const newTicket = ref({
    title: '',
    category: 'suporte',
    message: '',
});
const creatingTicket = ref(false);

const createTicket = async () => {
    if (!newTicket.value.title || !newTicket.value.message) return;
    creatingTicket.value = true;

    try {
        await $fetch(getApiUrl('/api/store/tickets'), {
            method: 'POST',
            body: newTicket.value,
        });
        showNewTicketModal.value = false;
        newTicket.value = { title: '', category: 'suporte', message: '' };
        await refreshTickets();
    } catch (e) {
        console.error(e);
    } finally {
        creatingTicket.value = false;
    }
};
</script>

<template>
    <div class="space-y-6 animate-fadeIn">
        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-white">Meus Chamados</h2>
                <p class="text-sm text-slate-400 mt-1">Acompanhe suas solicitações de suporte e financeiro</p>
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
                <UiButton variant="primary" @click="showNewTicketModal = true">
                    <iconify-icon icon="tabler:plus"></iconify-icon>
                    <span>Novo Chamado</span>
                </UiButton>
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
                        Filtrar por Categoria:
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
                        <option value="aguardando_cliente">Aguardando minha Resposta</option>
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
                                    <span>Ver Detalhes</span>
                                </UiButton>
                            </td>
                        </tr>

                        <tr v-if="filteredTickets.length === 0">
                            <td colspan="6" class="py-12 text-center text-slate-500">
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
                        <div class="p-6 border-b border-slate-800 flex justify-between items-center bg-slate-950/50">
                            <div>
                                <div class="flex items-center gap-3">
                                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                                        <iconify-icon icon="tabler:ticket" class="text-indigo-400"></iconify-icon>
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
                            </div>
                            <button
                                @click="showDrawer = false"
                                class="text-slate-400 hover:text-white transition-colors"
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
                            <!-- TAB 1: MESSAGES/CONVERSATION -->
                            <div v-if="activeTab === 'messages'" class="space-y-6">
                                <div v-for="msg in ticketDetails.messages" :key="msg.id" class="flex flex-col">
                                    <div
                                        :class="[
                                            'max-w-[85%] rounded-xl p-4',
                                            !msg.is_agent
                                                ? 'bg-indigo-500/10 border border-indigo-500/20 self-end'
                                                : 'bg-slate-800 border border-slate-700 self-start',
                                        ]"
                                    >
                                        <div
                                            class="flex items-center gap-2 mb-2 text-xs font-semibold"
                                            :class="!msg.is_agent ? 'text-indigo-400' : 'text-slate-400'"
                                        >
                                            <iconify-icon
                                                :icon="!msg.is_agent ? 'tabler:user' : 'tabler:shield-check'"
                                            ></iconify-icon>
                                            <span>{{ !msg.is_agent ? 'Você' : msg.user_name || 'Suporte' }}</span>
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

                            <!-- TAB 2: EVENT TIMELINE -->
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

                        <!-- Footer / Interactive Controls -->
                        <div
                            class="p-6 border-t border-slate-800 bg-slate-950/50"
                            v-if="!loadingDetails && ticketDetails"
                        >
                            <!-- Rating Area (If Encerrado) -->
                            <div v-if="ticketDetails.ticket.status === 'encerrado'" class="space-y-4">
                                <!-- Already Rated -->
                                <div
                                    v-if="ticketDetails.ticket.rating"
                                    class="flex flex-col items-center py-3 bg-indigo-950/15 border border-indigo-900/30 rounded-xl"
                                >
                                    <span class="text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-1.5">
                                        Sua Avaliação
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

                                <!-- Pending Evaluation -->
                                <div
                                    v-else
                                    class="flex flex-col items-center py-4 bg-slate-900/60 border border-slate-800/80 rounded-xl space-y-2"
                                >
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                        Como avalia o nosso atendimento?
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        <button
                                            v-for="star in 5"
                                            :key="star"
                                            @click="rateTicket(star)"
                                            @mouseenter="ratingHover = star"
                                            @mouseleave="ratingHover = 0"
                                            :disabled="ratingSubmitting"
                                            class="text-slate-500 hover:text-yellow-400 focus:outline-none transition-colors"
                                        >
                                            <iconify-icon
                                                :icon="
                                                    (ratingHover ? star <= ratingHover : false)
                                                        ? 'fa7-solid:star'
                                                        : 'fa7-regular:star'
                                                "
                                                :class="[
                                                    'text-2xl transition-all',
                                                    (ratingHover ? star <= ratingHover : false)
                                                        ? 'text-yellow-400 scale-110'
                                                        : 'text-slate-600',
                                                ]"
                                            ></iconify-icon>
                                        </button>
                                    </div>
                                    <p class="text-[10px] text-slate-500">
                                        Clique em uma estrela para enviar sua avaliação
                                    </p>
                                </div>

                                <!-- Reopen Ticket Option -->
                                <div class="flex justify-center pt-2">
                                    <UiButton
                                        @click="reopenTicket"
                                        variant="secondary"
                                        :disabled="actionLoading"
                                        class="w-full sm:w-auto bg-slate-800 hover:bg-slate-700 border-slate-700 text-slate-300"
                                    >
                                        <iconify-icon
                                            v-if="actionLoading"
                                            icon="tabler:loader"
                                            class="animate-spin"
                                        ></iconify-icon>
                                        <iconify-icon v-else icon="tabler:arrow-back-up"></iconify-icon>
                                        <span>Reabrir Chamado</span>
                                    </UiButton>
                                </div>
                            </div>

                            <!-- Reply Box & Close Action (If Active) -->
                            <div v-else class="flex flex-col gap-3">
                                <textarea
                                    v-model="replyMessage"
                                    placeholder="Digite sua resposta..."
                                    class="w-full bg-slate-900 border border-slate-800 rounded-lg p-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors resize-none"
                                    rows="3"
                                ></textarea>
                                <div class="flex justify-between items-center">
                                    <!-- Close Action -->
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

                                    <!-- Send Reply -->
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
                        </div>
                    </div>
                </div>
            </Teleport>
        </ClientOnly>

        <!-- NEW TICKET MODAL -->
        <ClientOnly>
            <Teleport to="body">
                <div v-if="showNewTicketModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
                    <div
                        class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
                        @click="showNewTicketModal = false"
                    ></div>

                    <div
                        class="relative w-full max-w-lg bg-slate-900 border border-slate-800 rounded-xl shadow-2xl flex flex-col animate-fadeIn"
                    >
                        <div
                            class="p-6 border-b border-slate-800 flex justify-between items-center bg-slate-950/50 rounded-t-xl"
                        >
                            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                                <iconify-icon icon="tabler:ticket" class="text-indigo-400"></iconify-icon>
                                Abrir Novo Chamado
                            </h2>
                            <button
                                @click="showNewTicketModal = false"
                                class="text-slate-400 hover:text-white transition-colors"
                            >
                                <iconify-icon icon="tabler:x" class="text-xl"></iconify-icon>
                            </button>
                        </div>

                        <form @submit.prevent="createTicket" class="p-6 space-y-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-300">Assunto</label>
                                <input
                                    v-model="newTicket.title"
                                    placeholder="Ex: Problema com pagamento"
                                    class="w-full h-10 px-3 bg-slate-950 border border-slate-800 rounded-lg text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-300">Categoria</label>
                                <select
                                    v-model="newTicket.category"
                                    class="w-full h-10 px-3 bg-slate-950 border border-slate-800 rounded-lg text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors"
                                >
                                    <option value="suporte">Dúvida / Suporte</option>
                                    <option value="tecnico">Problema Técnico</option>
                                    <option value="financeiro">Financeiro / Faturamento</option>
                                    <option value="outros">Outros</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-300">Mensagem</label>
                                <textarea
                                    v-model="newTicket.message"
                                    placeholder="Descreva seu problema com o máximo de detalhes possível..."
                                    class="w-full bg-slate-950 border border-slate-800 rounded-lg p-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors resize-none"
                                    rows="5"
                                    required
                                ></textarea>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-slate-800">
                                <UiButton
                                    type="submit"
                                    variant="primary"
                                    :disabled="creatingTicket"
                                    class="w-full sm:w-auto"
                                >
                                    <iconify-icon
                                        v-if="creatingTicket"
                                        icon="tabler:loader"
                                        class="animate-spin"
                                    ></iconify-icon>
                                    <span>Criar Chamado</span>
                                </UiButton>
                            </div>
                        </form>
                    </div>
                </div>
            </Teleport>
        </ClientOnly>
    </div>
</template>
