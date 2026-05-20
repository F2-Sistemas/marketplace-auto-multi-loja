<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useStoreAuth } from '~/composables/useStoreAuth';
import { useTenant } from '~/composables/useTenant';

// This page uses blank layout (no header/footer)
definePageMeta({ layout: false });

const router = useRouter();
const { login, isAuthenticated } = useStoreAuth();
const { currentStore } = useTenant();

// Redirect if already logged in
if (import.meta.client && isAuthenticated.value) {
    router.replace('/admin');
}

// Quick-fill credential sets (for dev convenience)
const quickCredentials = [
    {
        label: 'Gerente da Loja',
        email: 'gerente.natal@autocar.com',
        password: 'secret123',
        role: 'Gerente',
        icon: 'tabler:building-store',
    },
    {
        label: 'Vendedor',
        email: 'vendedor.natal@autocar.com',
        password: 'secret123',
        role: 'Vendedor',
        icon: 'tabler:user-check',
    },
    {
        label: 'Admin Central',
        email: 'admin@rederevenda.com',
        password: 'secret123',
        role: 'Admin',
        icon: 'tabler:shield-check',
    },
];

const form = reactive({ email: '', password: '' });
const loading = ref(false);
const errorMsg = ref('');

const quickLogin = async (cred: typeof quickCredentials[0]) => {
    form.email = cred.email;
    form.password = cred.password;
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
        router.replace('/admin');
    } else {
        errorMsg.value = result.error || 'Credenciais inválidas.';
    }
    loading.value = false;
};
</script>

<template>
    <div class="min-h-screen bg-slate-950 font-['Outfit'] antialiased flex flex-col items-center justify-center p-4">
        <!-- Ambient glows -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[500px] h-80 rounded-full blur-3xl opacity-20"
                style="background: var(--store-accent-color, #fbbf24);">
            </div>
        </div>

        <div class="w-full max-w-md relative z-10">
            <!-- Store Brand -->
            <div class="text-center mb-8">
                <div class="inline-flex w-14 h-14 rounded-2xl items-center justify-center mb-4 shadow-xl"
                    style="background: linear-gradient(135deg, var(--store-accent-color, #fbbf24), #ea580c);">
                    <iconify-icon icon="tabler:steering-wheel" class="text-2xl text-white"></iconify-icon>
                </div>
                <h1 class="text-2xl font-black text-white">{{ currentStore.name }}</h1>
                <p class="text-sm text-slate-400 mt-1">Área de Gerenciamento da Loja</p>
            </div>

            <!-- Login Card -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-8 shadow-2xl backdrop-blur">
                <h2 class="text-lg font-bold text-white mb-1">Entrar</h2>
                <p class="text-xs text-slate-400 mb-6">Acesse com suas credenciais de lojista.</p>

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
                                id="store-login-email"
                                v-model="form.email"
                                type="email"
                                placeholder="gerente@sujaloja.com"
                                autocomplete="email"
                                class="w-full bg-slate-950/60 border border-slate-700 rounded-xl px-4 py-3 pl-10 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-store-accent focus:ring-1 transition"
                                style="--tw-ring-color: var(--store-accent-color, #fbbf24);"
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
                                id="store-login-password"
                                v-model="form.password"
                                type="password"
                                placeholder="••••••••"
                                autocomplete="current-password"
                                class="w-full bg-slate-950/60 border border-slate-700 rounded-xl px-4 py-3 pl-10 text-sm text-slate-100 placeholder-slate-600 focus:outline-none transition"
                            />
                        </div>
                    </div>

                    <!-- Error -->
                    <div v-if="errorMsg" class="flex items-center gap-2 bg-red-500/10 border border-red-500/30 rounded-lg px-3 py-2.5">
                        <iconify-icon icon="tabler:alert-circle" class="text-red-400 shrink-0"></iconify-icon>
                        <span class="text-xs text-red-400">{{ errorMsg }}</span>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3 rounded-xl font-bold text-sm text-slate-950 transition-all duration-200 shadow-lg disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2 cursor-pointer"
                        style="background: var(--store-accent-color, #fbbf24);"
                    >
                        <iconify-icon
                            :icon="loading ? 'tabler:loader-2' : 'tabler:login'"
                            :class="loading ? 'animate-spin' : ''"
                            class="text-lg"
                        ></iconify-icon>
                        {{ loading ? 'Entrando...' : 'Entrar' }}
                    </button>
                </form>

                <!-- Quick access divider -->
                <div class="flex items-center gap-3 my-5">
                    <hr class="flex-1 border-slate-800" />
                    <span class="text-[10px] text-slate-600 font-semibold uppercase tracking-wider">Acesso Rápido (Dev)</span>
                    <hr class="flex-1 border-slate-800" />
                </div>

                <!-- Quick-fill cards -->
                <div class="space-y-2">
                    <button
                        v-for="cred in quickCredentials"
                        :key="cred.email"
                        type="button"
                        @click="quickLogin(cred)"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 hover:border-slate-600 text-left transition-all duration-200 cursor-pointer group"
                    >
                        <iconify-icon :icon="cred.icon" class="text-slate-400 text-base shrink-0 group-hover:text-slate-200 transition"></iconify-icon>
                        <div class="flex-1 min-w-0">
                            <span class="font-semibold text-xs text-slate-200 block">{{ cred.label }}</span>
                            <span class="text-[10px] text-slate-500 truncate block">{{ cred.email }}</span>
                        </div>
                        <span class="text-[9px] font-bold text-slate-600 bg-slate-800 px-1.5 py-0.5 rounded shrink-0">
                            {{ cred.role }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- Back to store link -->
            <p class="text-center mt-6">
                <NuxtLink to="/" class="text-xs text-slate-500 hover:text-slate-300 transition">
                    ← Voltar ao catálogo
                </NuxtLink>
            </p>
        </div>
    </div>
</template>
