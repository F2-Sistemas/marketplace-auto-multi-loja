<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '~/composables/useAuth';

// Login page uses blank layout (no sidebar)
definePageMeta({ layout: false });

const router = useRouter();
const { login, isAuthenticated } = useAuth();

// Redirect if already logged in
if (import.meta.client && isAuthenticated.value) {
    router.replace('/');
}

// Quick-fill credential sets for local dev
const quickCredentials = [
    {
        label: 'Admin Central',
        email: 'admin@rederevenda.com',
        password: 'secret123',
        role: 'Administrador',
        icon: 'tabler:shield-check',
        color: 'indigo',
    },
    {
        label: 'Gerente AutoCar Natal',
        email: 'gerente.natal@autocar.com',
        password: 'secret123',
        role: 'Gerente de Loja',
        icon: 'tabler:building-store',
        color: 'amber',
    },
    {
        label: 'Gerente SP Veículos',
        email: 'gerente.sp@spveiculos.com',
        password: 'secret123',
        role: 'Gerente de Loja',
        icon: 'tabler:building-store',
        color: 'red',
    },
    {
        label: 'Vendedor Natal',
        email: 'vendedor.natal@autocar.com',
        password: 'secret123',
        role: 'Vendedor',
        icon: 'tabler:user-check',
        color: 'slate',
    },
];

const colorMap: Record<string, { card: string; badge: string }> = {
    indigo: { card: 'border-indigo-500/30 hover:bg-indigo-500/10', badge: 'bg-indigo-500/15 text-indigo-300' },
    amber:  { card: 'border-amber-500/30 hover:bg-amber-500/10',   badge: 'bg-amber-500/15 text-amber-300' },
    red:    { card: 'border-red-500/30 hover:bg-red-500/10',       badge: 'bg-red-500/15 text-red-300' },
    slate:  { card: 'border-slate-600/50 hover:bg-slate-800/60',   badge: 'bg-slate-700/50 text-slate-300' },
};

const form = reactive({ email: '', password: '' });
const loading = ref(false);
const errorMsg = ref('');

const fillCredential = (cred: typeof quickCredentials[0]) => {
    form.email = cred.email;
    form.password = cred.password;
};

const quickLogin = async (cred: typeof quickCredentials[0]) => {
    fillCredential(cred);
    await handleSubmit();
};

const handleSubmit = async () => {
    if (!form.email || !form.password) {
        errorMsg.value = 'Preencha o e-mail e a senha.';
        return;
    }
    loading.value = true;
    errorMsg.value = '';

    const result = await login(form.email, form.password);

    if (result.success) {
        router.replace('/');
    } else {
        errorMsg.value = result.error || 'Credenciais inválidas.';
    }
    loading.value = false;
};
</script>

<template>
    <div class="min-h-screen bg-slate-950 font-['Outfit'] antialiased flex items-center justify-center p-4">
        <!-- Ambient background glows -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-indigo-600/10 blur-3xl"></div>
            <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-amber-500/8 blur-3xl"></div>
        </div>

        <div class="w-full max-w-4xl relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

                <!-- Left: Login Form -->
                <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-8 shadow-2xl backdrop-blur">
                    <!-- Brand -->
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-amber-400 flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
                            <iconify-icon icon="tabler:steering-wheel" class="text-xl"></iconify-icon>
                        </div>
                        <div>
                            <span class="font-extrabold text-xl tracking-tight bg-gradient-to-r from-indigo-400 to-amber-400 bg-clip-text text-transparent">
                                Rede Revenda
                            </span>
                            <span class="text-[9px] uppercase font-semibold text-slate-500 block tracking-widest">
                                Backoffice Administrativo
                            </span>
                        </div>
                    </div>

                    <h1 class="text-2xl font-black text-white mb-1">Entrar</h1>
                    <p class="text-sm text-slate-400 mb-6">Acesse o painel com suas credenciais.</p>

                    <form @submit.prevent="handleSubmit" class="space-y-4">
                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">
                                E-mail
                            </label>
                            <div class="relative">
                                <iconify-icon
                                    icon="tabler:mail"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg"
                                ></iconify-icon>
                                <input
                                    id="login-email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="admin@rederevenda.com"
                                    autocomplete="email"
                                    class="w-full bg-slate-950/60 border border-slate-700 rounded-xl px-4 py-3 pl-10 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/40 transition"
                                />
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">
                                Senha
                            </label>
                            <div class="relative">
                                <iconify-icon
                                    icon="tabler:lock"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-lg"
                                ></iconify-icon>
                                <input
                                    id="login-password"
                                    v-model="form.password"
                                    type="password"
                                    placeholder="••••••••"
                                    autocomplete="current-password"
                                    class="w-full bg-slate-950/60 border border-slate-700 rounded-xl px-4 py-3 pl-10 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/40 transition"
                                />
                            </div>
                        </div>

                        <!-- Error message -->
                        <div v-if="errorMsg" class="flex items-center gap-2 bg-red-500/10 border border-red-500/30 rounded-lg px-3 py-2.5">
                            <iconify-icon icon="tabler:alert-circle" class="text-red-400 shrink-0"></iconify-icon>
                            <span class="text-xs text-red-400">{{ errorMsg }}</span>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            :disabled="loading"
                            class="w-full py-3 rounded-xl font-bold text-sm bg-gradient-to-r from-indigo-600 to-indigo-500 text-white hover:from-indigo-500 hover:to-indigo-400 transition-all duration-200 shadow-lg shadow-indigo-500/20 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <iconify-icon
                                :icon="loading ? 'tabler:loader-2' : 'tabler:login'"
                                :class="loading ? 'animate-spin' : ''"
                                class="text-lg"
                            ></iconify-icon>
                            {{ loading ? 'Entrando...' : 'Entrar' }}
                        </button>
                    </form>
                </div>

                <!-- Right: Quick-fill credential cards -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 mb-4">
                        <iconify-icon icon="tabler:click" class="text-slate-400 text-lg"></iconify-icon>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Acesso Rápido (Dev)</p>
                    </div>

                    <button
                        v-for="cred in quickCredentials"
                        :key="cred.email"
                        type="button"
                        @click="quickLogin(cred)"
                        :class="[
                            'w-full text-left bg-slate-900/60 border rounded-xl p-4 transition-all duration-200 cursor-pointer group',
                            colorMap[cred.color]?.card || colorMap.slate.card
                        ]"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0 group-hover:border-slate-600 transition">
                                <iconify-icon :icon="cred.icon" class="text-slate-300 text-base"></iconify-icon>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold text-sm text-slate-100">{{ cred.label }}</span>
                                    <span :class="['text-[10px] font-semibold px-2 py-0.5 rounded-full shrink-0', colorMap[cred.color]?.badge || colorMap.slate.badge]">
                                        {{ cred.role }}
                                    </span>
                                </div>
                                <span class="text-xs text-slate-500 truncate block mt-0.5">{{ cred.email }}</span>
                            </div>
                            <iconify-icon icon="tabler:arrow-right" class="text-slate-600 group-hover:text-slate-300 text-sm shrink-0 transition"></iconify-icon>
                        </div>
                    </button>

                    <p class="text-[10px] text-slate-600 text-center pt-2">
                        Clique em um perfil para preencher e autenticar automaticamente.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
