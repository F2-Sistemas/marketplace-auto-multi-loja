<script setup lang="ts">
import { computed } from 'vue';
import { useTenant } from '~/composables/useTenant';
import { useI18n } from '~/composables/useI18n';

const { currentStore, tenants, currentTenantKey } = useTenant();
const { t } = useI18n();

// Dynamic CSS custom variables based on the active store
const storeStyles = computed(() => {
    let primaryFrom = '#f59e0b';
    let primaryTo = '#ea580c';
    let primaryRgb = '245, 158, 11';
    let accent = '#fbbf24';
    let text = '#0f172a';

    if (currentStore.value.theme === 'red') {
        primaryFrom = '#e11d48';
        primaryTo = '#be123c';
        primaryRgb = '225, 29, 72';
        accent = '#fda4af';
        text = '#ffffff';
    } else if (currentStore.value.theme === 'blue') {
        primaryFrom = '#2563eb';
        primaryTo = '#1d4ed8';
        primaryRgb = '37, 99, 235';
        accent = '#93c5fd';
        text = '#ffffff';
    }

    return {
        '--store-primary-from': primaryFrom,
        '--store-primary-to': primaryTo,
        '--store-primary-rgb': primaryRgb,
        '--store-accent-color': accent,
        '--store-btn-text': text,
    };
});

const getBackofficeUrl = (path: string = '') => {
    if (!import.meta.client) return '#';
    const currentHost = window.location.hostname;
    const protocol = window.location.protocol;
    let base = '';
    if (currentHost === 'localhost' || currentHost === '127.0.0.1') {
        base = `http://${currentHost}:7032`;
    } else {
        const domainParts = currentHost.split('.');
        if (domainParts.length >= 2) {
            const baseDomain = domainParts.slice(-2).join('.');
            base = `${protocol}//admin.${baseDomain}`;
        } else {
            base = `${protocol}//admin.rederevenda.com`;
        }
    }
    return `${base}${path}`;
};
</script>

<template>
    <div
        :style="storeStyles"
        class="min-h-screen bg-slate-950 text-slate-100 font-['Outfit'] antialiased flex flex-col justify-between selection:bg-amber-500/30"
    >
        <!-- Main Shell -->
        <div class="flex flex-col min-h-screen">
            <!-- Header -->
            <header class="sticky top-0 z-40 bg-slate-950/80 backdrop-blur-md border-b border-slate-900/80">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                    <!-- Logo -->
                    <NuxtLink to="/" class="flex items-center gap-3 group">
                        <span
                            class="p-2.5 rounded-xl bg-gradient-to-br from-slate-900 to-slate-800 border border-slate-800 text-2xl transition duration-300"
                        >
                            <iconify-icon :icon="currentStore.logoIcon" class="store-icon-color block"></iconify-icon>
                        </span>
                        <div class="text-left">
                            <h1 class="text-xl font-black text-slate-100 leading-none tracking-tight">
                                {{ currentStore.name }}
                            </h1>
                            <span class="text-[10px] text-slate-500 uppercase tracking-widest font-normal mt-1 block">
                                {{ currentStore.tagline }}
                            </span>
                        </div>
                    </NuxtLink>

                    <!-- Nav Menu -->
                    <nav class="hidden md:flex items-center gap-8 text-sm font-normal text-slate-400">
                        <NuxtLink
                            to="/"
                            class="hover:text-slate-100 transition duration-205"
                            active-class="store-active-nav"
                        >
                            {{ t('nav.home') }}
                        </NuxtLink>
                        <NuxtLink
                            to="/about"
                            class="hover:text-slate-100 transition duration-205"
                            active-class="store-active-nav"
                        >
                            {{ t('nav.about') }}
                        </NuxtLink>
                        <!-- Lojista Area Menu -->
                        <div class="relative group">
                            <button
                                class="flex items-center gap-1 hover:text-slate-100 transition duration-205 focus:outline-none"
                            >
                                {{ t('nav.dealer_area') }}
                                <iconify-icon icon="tabler:chevron-down"></iconify-icon>
                            </button>
                            <div
                                class="absolute left-0 mt-2 w-48 bg-slate-900 border border-slate-800 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50"
                            >
                                <div class="py-2">
                                    <a
                                        :href="getBackofficeUrl('/chamados')"
                                        class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-800 hover:text-white"
                                    >
                                        {{ t('nav.my_tickets') }}
                                    </a>
                                    <NuxtLink
                                        to="/configuracoes/tema"
                                        class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-800 hover:text-white"
                                    >
                                        {{ t('nav.customize_theme') }}
                                    </NuxtLink>
                                    <a
                                        :href="getBackofficeUrl('/perfil')"
                                        class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-800 hover:text-white"
                                    >
                                        {{ t('nav.my_profile') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <!-- Contact Widget -->
                    <div class="flex items-center gap-3">
                        <a
                            :href="`https://wa.me/${currentStore.whatsapp}`"
                            target="_blank"
                            class="inline-flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white transition duration-200 cursor-pointer"
                        >
                            <iconify-icon
                                icon="tabler:brand-whatsapp"
                                class="text-base mr-2 text-emerald-400 block"
                            ></iconify-icon>
                            <span class="hidden sm:inline">{{ currentStore.phone }}</span>
                            <span class="sm:hidden">{{ t('nav.contact_btn') }}</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Render Container -->
            <main class="flex-grow">
                <slot></slot>
            </main>
        </div>

        <!-- Footer -->
        <footer class="bg-slate-950 border-t border-slate-900/60 py-8">
            <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center md:flex md:justify-between md:items-center text-slate-500 text-xs gap-4"
            >
                <div class="flex flex-col md:items-start items-center gap-2 mb-4 md:mb-0">
                    <p class="font-semibold text-slate-350">{{ t('footer.powered_by') }}</p>
                    <p>{{ currentStore.address }} — {{ currentStore.city }}/{{ currentStore.state }}</p>
                </div>
                <div>
                    <p>&copy; {{ new Date().getFullYear() }} {{ currentStore.name }}. {{ t('footer.copyright') }}</p>
                </div>
            </div>
        </footer>

        <!-- Floating Tenant Simulator Box -->
        <div class="fixed bottom-6 right-6 z-50">
            <div
                class="bg-slate-900/90 backdrop-blur-md border border-slate-850 rounded-2xl shadow-xl shadow-slate-950/80 p-4 max-w-[280px] text-left"
            >
                <div class="flex items-center gap-2 mb-2 pb-2 border-b border-slate-800">
                    <iconify-icon icon="tabler:settings" class="text-amber-500 text-lg block"></iconify-icon>
                    <span class="text-xs font-semibold text-slate-200">{{ t('simulator.title') }}</span>
                </div>
                <p class="text-[10px] text-slate-400 mb-3 leading-relaxed">
                    {{ t('simulator.select_label') }}
                </p>
                <div class="space-y-1.5">
                    <button
                        v-for="(details, key) in tenants"
                        :key="key"
                        @click="currentTenantKey = key"
                        :class="[
                            'w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs font-semibold transition duration-200 cursor-pointer',
                            currentTenantKey === key
                                ? 'bg-amber-500/10 text-amber-400 border border-amber-500/25'
                                : 'bg-slate-950/40 hover:bg-slate-950/80 text-slate-400 border border-transparent',
                        ]"
                    >
                        <span>{{ details.name }}</span>
                        <span
                            :class="[
                                'w-2 h-2 rounded-full',
                                details.theme === 'amber' ? 'bg-amber-500' : '',
                                details.theme === 'red' ? 'bg-red-500' : '',
                                details.theme === 'blue' ? 'bg-blue-500' : '',
                            ]"
                        ></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.store-icon-color {
    color: var(--store-accent-color, #fbbf24);
}
.store-active-nav {
    color: var(--store-accent-color, #fbbf24);
    border-bottom: 2px solid var(--store-accent-color, #fbbf24);
    padding-bottom: 4px;
}
</style>
