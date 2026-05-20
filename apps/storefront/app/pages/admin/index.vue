<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useStoreAuth } from '~/composables/useStoreAuth';
import { useTenant } from '~/composables/useTenant';

// Admin area uses blank layout (the store's own admin shell)
definePageMeta({ layout: false });

const router = useRouter();
const { user, logout } = useStoreAuth();
const { currentStore } = useTenant();

const userInitials = computed(() => {
    if (!user.value?.name) return '??';
    return user.value.name
        .split(' ')
        .slice(0, 2)
        .map((n: string) => n[0])
        .join('')
        .toUpperCase();
});

const adminLinks = [
    { label: 'Estoque de Veículos', icon: 'tabler:car', to: '/admin/estoque', desc: 'Gerenciar anúncios e inventário' },
    { label: 'Leads & Contatos',    icon: 'tabler:messages', to: '/admin/leads', desc: 'Propostas e formulários de interesse' },
    { label: 'Configurações',       icon: 'tabler:settings', to: '/configuracoes/perfil', desc: 'Tema, dados da loja e domínio' },
    { label: 'Meus Chamados',       icon: 'tabler:ticket', to: '/admin/chamados', desc: 'Suporte e atendimento da plataforma' },
];

const goBack = () => router.push('/');
</script>

<template>
    <div class="min-h-screen bg-slate-950 font-['Outfit'] antialiased" style="color: #f1f5f9;">
        <!-- Top bar -->
        <header class="border-b border-slate-800 bg-slate-900/70 backdrop-blur-sm sticky top-0 z-30">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
                <!-- Brand + store -->
                <div class="flex items-center gap-3">
                    <button @click="goBack" class="flex items-center gap-2 group cursor-pointer">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-950 text-sm font-bold"
                            style="background: var(--store-accent-color, #fbbf24);">
                            <iconify-icon icon="tabler:steering-wheel" class="text-base"></iconify-icon>
                        </div>
                        <span class="font-extrabold text-sm text-white group-hover:opacity-80 transition">
                            {{ currentStore.name }}
                        </span>
                    </button>
                    <span class="text-slate-700">|</span>
                    <span class="text-xs font-semibold text-slate-400">Painel da Loja</span>
                </div>

                <!-- User area -->
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-300">
                            {{ userInitials }}
                        </div>
                        <span class="text-xs text-slate-300 font-medium">{{ user?.name }}</span>
                    </div>

                    <button
                        @click="logout"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 hover:text-red-400 hover:bg-red-500/10 border border-transparent hover:border-red-500/30 transition-all duration-200 cursor-pointer"
                    >
                        <iconify-icon icon="tabler:logout" class="text-base"></iconify-icon>
                        <span class="hidden sm:inline">Sair</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Body -->
        <main class="max-w-6xl mx-auto px-4 sm:px-6 py-10 space-y-8">
            <!-- Greeting -->
            <div>
                <h1 class="text-2xl font-black text-white">
                    Olá, {{ user?.name?.split(' ')[0] || 'Gerente' }} 👋
                </h1>
                <p class="text-sm text-slate-400 mt-1">
                    Bem-vindo ao painel de gestão — <span class="font-semibold text-slate-300">{{ currentStore.name }}</span>
                </p>
            </div>

            <!-- Quick access grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <NuxtLink
                    v-for="link in adminLinks"
                    :key="link.to"
                    :to="link.to"
                    class="group flex items-center gap-4 p-5 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-slate-600 transition-all duration-200"
                >
                    <div class="w-12 h-12 rounded-xl bg-slate-800 group-hover:bg-slate-700 border border-slate-700 flex items-center justify-center transition">
                        <iconify-icon :icon="link.icon" class="text-xl text-slate-300"></iconify-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="font-semibold text-sm text-slate-200 block group-hover:text-white transition">
                            {{ link.label }}
                        </span>
                        <span class="text-xs text-slate-500 line-clamp-1">{{ link.desc }}</span>
                    </div>
                    <iconify-icon icon="tabler:chevron-right" class="text-slate-600 group-hover:text-slate-400 text-base transition"></iconify-icon>
                </NuxtLink>
            </div>

            <!-- Back to store -->
            <div class="pt-4 border-t border-slate-900">
                <button
                    @click="goBack"
                    class="flex items-center gap-2 text-xs text-slate-500 hover:text-slate-300 transition cursor-pointer"
                >
                    <iconify-icon icon="tabler:arrow-left" class="text-sm"></iconify-icon>
                    Ver site público da loja
                </button>
            </div>
        </main>
    </div>
</template>
