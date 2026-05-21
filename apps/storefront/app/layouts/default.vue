<script setup lang="ts">
import { computed, ref } from 'vue';
import { useRoute } from '#app';
import { useTenant } from '~/composables/useTenant';
import { useI18n } from '~/composables/useI18n';
import UiButton from '~/components/ui/UiButton.vue';

const route = useRoute();
const { t } = useI18n();
const { currentStore, tenants, currentTenantKey } = useTenant();

const isDropdownOpen = ref(false);
const isServicesOpen = ref(false);
const dropdownCloseTimer = ref<ReturnType<typeof setTimeout> | null>(null);
const servicesCloseTimer = ref<ReturnType<typeof setTimeout> | null>(null);

const isCatalogTemplate = computed(() => currentStore.value.layoutStyle === 'catalog');

const shellStyle = computed(() => {
    return {
        '--store-font-family': `'${currentStore.value.fontFamily || 'Inter'}', sans-serif`,
        fontFamily: 'var(--store-font-family, Inter, sans-serif)',
    };
});

const themeBadgeLabel = computed(() => {
    if (isCatalogTemplate.value) {
        return t('theme.layout.catalog');
    }

    return t('theme.layout.showroom');
});

const navLinks = computed(() => {
    return [
        { label: t('home'), to: '/' },
        { label: t('catalog'), to: '/veiculos' },
        { label: t('stores'), to: '/lojas' },
        { label: t('news'), to: '/noticias' },
    ];
});

const servicesMenuItems = computed(() => {
    return [
        {
            label: t('navigation.servicesAutomotive'),
            to: '/veiculos',
            icon: 'tabler:wrench',
        },
        {
            label: t('navigation.servicesFipe'),
            to: '/noticias',
            icon: 'tabler:chart-bar',
        },
        {
            label: t('navigation.servicesEvaluation'),
            to: '/veiculos',
            icon: 'tabler:badge-3d',
        },
        {
            label: t('navigation.servicesAnnouncement'),
            to: '/quero-anunciar',
            icon: 'tabler:rocket',
        },
    ];
});

const isLinkActive = (to: string) => {
    if (to === '/') {
        return route.path === '/';
    }

    return route.path.startsWith(to);
};

const clearTimer = (timer: typeof dropdownCloseTimer) => {
    if (timer.value !== null) {
        clearTimeout(timer.value);
        timer.value = null;
    }
};

const openDropdown = () => {
    clearTimer(dropdownCloseTimer);
    isDropdownOpen.value = true;
};

const closeDropdown = () => {
    clearTimer(dropdownCloseTimer);
    dropdownCloseTimer.value = setTimeout(() => {
        isDropdownOpen.value = false;
        dropdownCloseTimer.value = null;
    }, 160);
};

const openServices = () => {
    clearTimer(servicesCloseTimer);
    isServicesOpen.value = true;
};

const closeServices = () => {
    clearTimer(servicesCloseTimer);
    servicesCloseTimer.value = setTimeout(() => {
        isServicesOpen.value = false;
        servicesCloseTimer.value = null;
    }, 160);
};
</script>

<template>
    <div
        :style="shellStyle"
        :class="[
            'min-h-screen flex flex-col justify-between antialiased selection:text-white',
            {
                'bg-slate-950 text-slate-100 selection:bg-amber-500/30': !isCatalogTemplate,
                'bg-slate-50 text-slate-900 selection:bg-brand-500/30': isCatalogTemplate,
            },
        ]"
    >
        <header
            :class="[
                'sticky top-0 z-40 h-16 flex items-center transition-colors duration-200',
                {
                    'bg-slate-950/80 backdrop-blur-md border-b border-slate-900/80': !isCatalogTemplate,
                    'bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm': isCatalogTemplate,
                },
            ]"
        >
            <div class="container mx-auto flex max-w-7xl items-center justify-between px-4 md:px-6">
                <NuxtLink to="/" class="group flex select-none items-center gap-3">
                    <div
                        :class="[
                            'flex h-9 w-9 items-center justify-center rounded-lg text-white shadow-md transition-transform duration-180 group-hover:scale-105',
                            {
                                'bg-brand-500 shadow-brand-500/20': isCatalogTemplate,
                                'bg-slate-900 shadow-slate-950/20 border border-slate-800': !isCatalogTemplate,
                            },
                        ]"
                    >
                        <img
                            v-if="currentStore.logoUrl"
                            :src="currentStore.logoUrl"
                            :alt="currentStore.name"
                            class="h-full w-full rounded-lg object-contain p-1"
                        />
                        <iconify-icon v-else icon="tabler:steering-wheel" class="text-xl" />
                    </div>

                    <div class="text-left">
                        <div class="flex items-center gap-2">
                            <h1
                                :class="[
                                    'text-lg font-black tracking-tight leading-none',
                                    {
                                        'text-slate-100': !isCatalogTemplate,
                                        'text-slate-900': isCatalogTemplate,
                                    },
                                ]"
                            >
                                {{ currentStore.name }}
                            </h1>
                            <span
                                :class="[
                                    'rounded-full border px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.18em]',
                                    {
                                        'border-amber-500/30 bg-amber-500/10 text-amber-300': !isCatalogTemplate,
                                        'border-brand-500/25 bg-brand-500/10 text-brand-600': isCatalogTemplate,
                                    },
                                ]"
                            >
                                {{ themeBadgeLabel }}
                            </span>
                        </div>
                        <span
                            :class="[
                                'mt-1 block text-[10px] uppercase tracking-[0.22em] font-normal',
                                'text-slate-500',
                            ]"
                        >
                            {{ currentStore.tagline }}
                        </span>
                    </div>
                </NuxtLink>

                <nav class="hidden items-center gap-6 md:flex">
                    <NuxtLink
                        v-for="link in navLinks"
                        :key="link.to"
                        :to="link.to"
                        :class="[
                            'border-b-2 py-1.5 text-sm font-semibold uppercase tracking-wider transition-colors duration-150',
                            {
                                'text-brand-600 border-brand-500': isCatalogTemplate && isLinkActive(link.to),
                                'text-neutral-800 border-brand-500': !isCatalogTemplate && isLinkActive(link.to),
                                'text-neutral-500 border-transparent hover:text-neutral-800': isCatalogTemplate && !isLinkActive(link.to),
                                'text-slate-400 border-transparent hover:text-slate-100': !isCatalogTemplate && !isLinkActive(link.to),
                            },
                        ]"
                    >
                        {{ link.label }}
                    </NuxtLink>

                    <div class="relative pb-2" @mouseenter="openServices" @mouseleave="closeServices">
                        <button
                            type="button"
                            :class="[
                                'flex items-center gap-1 border-b-2 py-1.5 text-sm font-semibold uppercase tracking-wider transition-colors duration-150 focus:outline-none',
                                {
                                    'text-brand-600 border-brand-500': isServicesOpen && isCatalogTemplate,
                                    'text-slate-100 border-brand-500': isServicesOpen && !isCatalogTemplate,
                                    'text-neutral-500 border-transparent hover:text-neutral-800':
                                        isCatalogTemplate && !isServicesOpen,
                                    'text-slate-400 border-transparent hover:text-slate-100':
                                        !isCatalogTemplate && !isServicesOpen,
                                },
                            ]"
                            @click="isServicesOpen = !isServicesOpen"
                        >
                            <span>{{ t('navigation.services') }}</span>
                            <iconify-icon
                                icon="tabler:chevron-down"
                                :class="[
                                    'text-[10px] transition-transform duration-200',
                                    {
                                        'rotate-180 text-brand-500': isServicesOpen && isCatalogTemplate,
                                        'rotate-180 text-amber-400': isServicesOpen && !isCatalogTemplate,
                                        'text-neutral-400': !isServicesOpen && isCatalogTemplate,
                                        'text-slate-500': !isServicesOpen && !isCatalogTemplate,
                                    },
                                ]"
                            />
                        </button>

                        <div
                            v-show="isServicesOpen"
                            :class="[
                                'absolute left-1/2 top-full z-50 w-72 -translate-x-1/2 overflow-hidden rounded-2xl border shadow-xl animate-in fade-in slide-in-from-top-1 duration-150',
                                {
                                    'border-neutral-200 bg-white': isCatalogTemplate,
                                    'border-slate-800 bg-slate-900': !isCatalogTemplate,
                                },
                            ]"
                            @mouseenter="openServices"
                            @mouseleave="closeServices"
                        >
                            <div
                                :class="[
                                    'border-b px-4 py-3',
                                    {
                                        'border-neutral-100': isCatalogTemplate,
                                        'border-slate-800': !isCatalogTemplate,
                                    },
                                ]"
                            >
                                <span
                                    :class="[
                                        'text-[10px] font-bold uppercase tracking-wider',
                                        {
                                            'text-neutral-400': isCatalogTemplate,
                                            'text-slate-500': !isCatalogTemplate,
                                        },
                                    ]"
                                >
                                    {{ t('navigation.services') }}
                                </span>
                            </div>

                            <NuxtLink
                                v-for="item in servicesMenuItems"
                                :key="item.to"
                                :to="item.to"
                                :class="[
                                    'flex items-center gap-3 px-4 py-3 transition',
                                    {
                                        'hover:bg-neutral-50': isCatalogTemplate,
                                        'hover:bg-slate-800': !isCatalogTemplate,
                                    },
                                ]"
                                @click="isServicesOpen = false"
                            >
                                <span
                                    :class="[
                                        'flex h-9 w-9 items-center justify-center rounded-xl',
                                        {
                                            'bg-neutral-100 text-neutral-700': isCatalogTemplate,
                                            'bg-slate-800 text-slate-300': !isCatalogTemplate,
                                        },
                                    ]"
                                >
                                    <iconify-icon :icon="item.icon" class="text-lg" />
                                </span>
                                <span class="min-w-0">
                                    <span
                                        :class="[
                                            'block text-sm font-semibold',
                                            {
                                                'text-neutral-800': isCatalogTemplate,
                                                'text-slate-100': !isCatalogTemplate,
                                            },
                                        ]"
                                    >
                                        {{ item.label }}
                                    </span>
                                </span>
                            </NuxtLink>
                        </div>
                    </div>
                </nav>

                <div class="flex items-center gap-3">
                    <NuxtLink
                        to="/admin/login"
                        :class="[
                            'hidden h-9 items-center rounded-button px-4 text-xs font-semibold shadow-xs transition duration-150 sm:inline-flex',
                            {
                                'bg-neutral-100 text-neutral-700 hover:bg-neutral-200': isCatalogTemplate,
                                'border border-slate-800 bg-slate-900 text-slate-300 hover:bg-slate-800': !isCatalogTemplate,
                            },
                        ]"
                    >
                        {{ t('auth.loginButton') }}
                    </NuxtLink>

                    <div class="relative pb-2" @mouseenter="openDropdown" @mouseleave="closeDropdown">
                        <button
                            type="button"
                            :class="[
                                'flex h-9 items-center gap-1.5 rounded-button px-4 text-xs font-semibold shadow-xs transition duration-150 cursor-pointer',
                                {
                                    'bg-neutral-100 text-neutral-700 hover:bg-neutral-200': isCatalogTemplate,
                                    'border border-slate-800 bg-slate-900 text-slate-300 hover:bg-slate-800':
                                        !isCatalogTemplate,
                                },
                            ]"
                            @click="isDropdownOpen = !isDropdownOpen"
                        >
                            <iconify-icon icon="tabler:user" class="text-sm" />
                            <span>Entrar</span>
                            <iconify-icon
                                icon="tabler:chevron-down"
                                :class="[
                                    'text-[10px] transition-transform duration-200',
                                    {
                                        'rotate-180 text-brand-500': isDropdownOpen && isCatalogTemplate,
                                        'rotate-180 text-amber-400': isDropdownOpen && !isCatalogTemplate,
                                        'text-neutral-400': !isCatalogTemplate,
                                        'text-slate-500': !isCatalogTemplate,
                                    },
                                ]"
                            />
                        </button>

                        <div
                            v-show="isDropdownOpen"
                            :class="[
                                'absolute right-0 top-full z-50 w-80 overflow-hidden rounded-2xl border py-2 shadow-xl animate-in fade-in slide-in-from-top-1 duration-150',
                                {
                                    'border-neutral-200 bg-white': isCatalogTemplate,
                                    'border-slate-800 bg-slate-900': !isCatalogTemplate,
                                },
                            ]"
                            @mouseenter="openDropdown"
                            @mouseleave="closeDropdown"
                        >
                            <div
                                :class="[
                                    'space-y-3 border-b px-4 py-4',
                                    {
                                        'border-neutral-100': isCatalogTemplate,
                                        'border-slate-800': !isCatalogTemplate,
                                    },
                                ]"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <span
                                            :class="[
                                                'text-[10px] font-bold uppercase tracking-wider',
                                                {
                                                    'text-neutral-400': isCatalogTemplate,
                                                    'text-slate-500': !isCatalogTemplate,
                                                },
                                            ]"
                                        >
                                            {{ t('auth.identify') }}
                                        </span>
                                        <p
                                            :class="[
                                                'text-sm font-semibold leading-snug',
                                                {
                                                    'text-neutral-800': isCatalogTemplate,
                                                    'text-slate-100': !isCatalogTemplate,
                                                },
                                            ]"
                                        >
                                            {{ t('auth.loginPrompt') }}
                                        </p>
                                    </div>

                                    <button
                                        type="button"
                                        :class="[
                                            'transition',
                                            {
                                                'text-neutral-400 hover:text-neutral-700': isCatalogTemplate,
                                                'text-slate-400 hover:text-slate-200': !isCatalogTemplate,
                                            },
                                        ]"
                                        aria-label="Fechar"
                                        @click="isDropdownOpen = false"
                                    >
                                        <iconify-icon icon="tabler:x" class="text-lg" />
                                    </button>
                                </div>

                                <UiButton
                                    to="/admin"
                                    variant="primary"
                                    size="md"
                                    class="w-full font-semibold"
                                    @click="isDropdownOpen = false"
                                >
                                    {{ t('auth.loginButton') }}
                                </UiButton>
                            </div>

                            <NuxtLink
                                to="/favoritos"
                                class="block px-4 py-3 transition"
                                :class="{
                                    'hover:bg-neutral-50': isCatalogTemplate,
                                    'hover:bg-slate-800': !isCatalogTemplate,
                                }"
                                @click="isDropdownOpen = false"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        :class="[
                                            'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg',
                                            {
                                                'bg-brand-50 text-brand-600': isCatalogTemplate,
                                                'bg-slate-800 text-amber-300': !isCatalogTemplate,
                                            },
                                        ]"
                                    >
                                        <iconify-icon icon="tabler:user" class="text-lg" />
                                    </div>
                                    <div>
                                        <div
                                            :class="[
                                                'text-xs font-bold',
                                                {
                                                    'text-neutral-800': isCatalogTemplate,
                                                    'text-slate-100': !isCatalogTemplate,
                                                },
                                            ]"
                                        >
                                            {{ t('auth.clientArea') }}
                                        </div>
                                        <div
                                            :class="[
                                                'text-[10px] font-normal',
                                                {
                                                    'text-neutral-400': isCatalogTemplate,
                                                    'text-slate-400': !isCatalogTemplate,
                                                },
                                            ]"
                                        >
                                            {{ t('auth.clientAreaDesc') }}
                                        </div>
                                    </div>
                                </div>
                            </NuxtLink>

                            <NuxtLink
                                to="/admin"
                                class="block px-4 py-3 transition"
                                :class="{
                                    'hover:bg-neutral-50': isCatalogTemplate,
                                    'hover:bg-slate-800': !isCatalogTemplate,
                                }"
                                @click="isDropdownOpen = false"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        :class="[
                                            'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg',
                                            {
                                                'bg-indigo-50 text-indigo-600': isCatalogTemplate,
                                                'bg-slate-800 text-blue-300': !isCatalogTemplate,
                                            },
                                        ]"
                                    >
                                        <iconify-icon icon="tabler:building-store" class="text-lg" />
                                    </div>
                                    <div>
                                        <div
                                            :class="[
                                                'text-xs font-bold',
                                                {
                                                    'text-neutral-800': isCatalogTemplate,
                                                    'text-slate-100': !isCatalogTemplate,
                                                },
                                            ]"
                                        >
                                            {{ t('auth.partnerArea') }}
                                        </div>
                                        <div
                                            :class="[
                                                'text-[10px] font-normal',
                                                {
                                                    'text-neutral-400': isCatalogTemplate,
                                                    'text-slate-400': !isCatalogTemplate,
                                                },
                                            ]"
                                        >
                                            {{ t('auth.partnerAreaDesc') }}
                                        </div>
                                    </div>
                                </div>
                            </NuxtLink>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            <slot />
        </main>

        <footer
            :class="[
                'py-8 transition-colors duration-200',
                {
                    'border-t border-slate-900/60 bg-slate-950 text-slate-500': !isCatalogTemplate,
                    'border-t border-slate-200 bg-white text-slate-500': isCatalogTemplate,
                },
            ]"
        >
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 text-center text-xs md:flex-row md:items-center md:justify-between md:px-6 md:text-left">
                <div class="flex flex-col items-center gap-2 md:items-start">
                    <p class="font-semibold" :class="{ 'text-slate-350': !isCatalogTemplate, 'text-slate-700': isCatalogTemplate }">
                        {{ t('footer.powered_by') }}
                    </p>
                    <p>
                        {{ currentStore.address }} — {{ currentStore.city }}/{{ currentStore.state }}
                    </p>
                </div>
                <div>
                    <p>
                        &copy; {{ new Date().getFullYear() }} {{ currentStore.name }}. {{ t('footer.copyright') }}
                    </p>
                </div>
            </div>
        </footer>

        <div class="fixed bottom-6 right-6 z-50">
            <div
                :class="[
                    'max-w-[280px] rounded-2xl border p-4 text-left shadow-xl shadow-slate-950/80 backdrop-blur-md',
                    {
                        'border-slate-800 bg-slate-900/90': !isCatalogTemplate,
                        'border-slate-200 bg-white/95': isCatalogTemplate,
                    },
                ]"
            >
                <div
                    :class="[
                        'mb-2 flex items-center gap-2 border-b pb-2',
                        {
                            'border-slate-800': !isCatalogTemplate,
                            'border-slate-200': isCatalogTemplate,
                        },
                    ]"
                >
                    <iconify-icon icon="tabler:settings" class="text-lg text-amber-500" />
                    <span
                        :class="[
                            'text-xs font-semibold',
                            {
                                'text-slate-200': !isCatalogTemplate,
                                'text-slate-800': isCatalogTemplate,
                            },
                        ]"
                    >
                        {{ t('simulator.title') }}
                    </span>
                </div>

                <p class="mb-3 text-[10px] leading-relaxed" :class="{ 'text-slate-400': !isCatalogTemplate, 'text-slate-500': isCatalogTemplate }">
                    {{ t('simulator.select_label') }}
                </p>

                <div class="space-y-1.5">
                    <button
                        v-for="(details, key) in tenants"
                        :key="key"
                        :class="[
                            'flex w-full items-center justify-between rounded-lg border px-2.5 py-1.5 text-xs font-semibold transition duration-200 cursor-pointer',
                            {
                                'border-amber-500/25 bg-amber-500/10 text-amber-400':
                                    currentTenantKey === key && !isCatalogTemplate,
                                'border-brand-500/25 bg-brand-500/10 text-brand-600':
                                    currentTenantKey === key && isCatalogTemplate,
                                'border-transparent bg-slate-950/40 text-slate-400 hover:bg-slate-950/80':
                                    currentTenantKey !== key && !isCatalogTemplate,
                                'border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100':
                                    currentTenantKey !== key && isCatalogTemplate,
                            },
                        ]"
                        @click="currentTenantKey = key"
                    >
                        <span>{{ details.name }}</span>
                        <span
                            :class="[
                                'h-2 w-2 rounded-full',
                                {
                                    'bg-amber-500': details.theme === 'amber',
                                    'bg-red-500': details.theme === 'red',
                                    'bg-blue-500': details.theme === 'blue',
                                },
                            ]"
                        />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
