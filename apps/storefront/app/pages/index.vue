<script setup lang="ts">
import { computed } from 'vue';
import { useTenant } from '~/composables/useTenant';
import { useI18n } from '~/composables/useI18n';
import VehicleFilters from '~/components/vehicle/VehicleFilters.vue';
import VehicleGrid from '~/components/vehicle/VehicleGrid.vue';

const { currentStore, searchQuery, filteredVehicles } = useTenant();
const { t } = useI18n();

const isCatalogTemplate = computed(() => currentStore.value.layoutStyle === 'catalog');

const featuredVehicles = computed(() => {
    return filteredVehicles.value.slice(0, 3);
});
</script>

<template>
    <div>
        <section
            :class="[
                'relative overflow-hidden',
                {
                    'bg-slate-950 py-20 md:py-28': !isCatalogTemplate,
                    'bg-slate-50 py-12 md:py-16': isCatalogTemplate,
                },
            ]"
        >
            <div
                :class="[
                    'absolute inset-0',
                    {
                        'bg-linear-to-b from-slate-950 via-slate-900/60 to-slate-950': !isCatalogTemplate,
                        'bg-linear-to-b from-white via-slate-50 to-slate-100': isCatalogTemplate,
                    },
                ]"
            ></div>

            <div
                v-if="!isCatalogTemplate"
                class="absolute -left-40 -top-40 h-96 w-96 rounded-full bg-linear-to-br from-store-primary-from/20 to-transparent blur-3xl"
            ></div>
            <div
                v-if="!isCatalogTemplate"
                class="absolute -bottom-40 -right-40 h-96 w-96 rounded-full bg-linear-to-br from-store-accent-color/10 to-transparent blur-3xl"
            ></div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    :class="[
                        'grid gap-8',
                        {
                            'items-center lg:grid-cols-[1.1fr_0.9fr]': !isCatalogTemplate,
                            'lg:grid-cols-[1.2fr_0.8fr]': isCatalogTemplate,
                        },
                    ]"
                >
                    <div :class="['space-y-6 text-left', { 'text-center lg:text-left': !isCatalogTemplate }]">
                        <div
                            :class="[
                                'inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold',
                                {
                                    'border-slate-800 bg-slate-900/80 text-slate-300': !isCatalogTemplate,
                                    'border-slate-200 bg-white text-slate-600 shadow-sm': isCatalogTemplate,
                                },
                            ]"
                        >
                            <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                            {{ t('inventory.badge') }}
                        </div>

                        <div class="space-y-4">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.32em] text-slate-500">
                                {{ currentStore.name }}
                            </p>
                            <h2
                                :class="[
                                    'tracking-tight leading-none',
                                    {
                                        'text-4xl font-black text-slate-100 md:text-6xl': !isCatalogTemplate,
                                        'text-3xl font-extrabold text-slate-900 md:text-5xl': isCatalogTemplate,
                                    },
                                ]"
                            >
                                {{ t('inventory.hero.title_part1') }}
                                <span
                                    :class="[
                                        'bg-gradient-to-r bg-clip-text text-transparent',
                                        {
                                            'store-text-gradient': !isCatalogTemplate,
                                            'store-text-gradient-light': isCatalogTemplate,
                                        },
                                    ]"
                                >
                                    {{ t('inventory.hero.title_highlight') }}
                                </span>
                            </h2>
                        </div>

                        <p
                            :class="[
                                'max-w-2xl leading-relaxed',
                                {
                                    'mx-auto text-base text-slate-400 md:text-lg lg:mx-0': !isCatalogTemplate,
                                    'text-sm text-slate-600 md:text-base': isCatalogTemplate,
                                },
                            ]"
                        >
                            {{ currentStore.tagline }}. {{ t('inventory.subtitle') }}.
                        </p>

                        <div class="max-w-2xl">
                            <VehicleFilters v-model="searchQuery" :layout-style="currentStore.layoutStyle" />
                        </div>

                        <div
                            :class="[
                                'grid gap-3 sm:grid-cols-3',
                                { 'max-w-2xl': !isCatalogTemplate, 'max-w-3xl': isCatalogTemplate },
                            ]"
                        >
                            <div
                                v-for="vehicle in featuredVehicles"
                                :key="vehicle.id"
                                :class="[
                                    'rounded-2xl border p-4 text-left',
                                    {
                                        'border-slate-800 bg-slate-900/60': !isCatalogTemplate,
                                        'border-slate-200 bg-white shadow-sm': isCatalogTemplate,
                                    },
                                ]"
                            >
                                <p
                                    :class="[
                                        'text-[10px] font-semibold uppercase tracking-[0.28em]',
                                        'text-slate-500',
                                    ]"
                                >
                                    {{ vehicle.brand }}
                                </p>
                                <p
                                    :class="[
                                        'mt-1 line-clamp-1 text-sm font-semibold',
                                        { 'text-slate-100': !isCatalogTemplate, 'text-slate-900': isCatalogTemplate },
                                    ]"
                                >
                                    {{ vehicle.model }}
                                </p>
                                <p
                                    :class="[
                                        'mt-2 text-xs',
                                        { 'text-slate-400': !isCatalogTemplate, 'text-slate-500': isCatalogTemplate },
                                    ]"
                                >
                                    {{ vehicle.year }} · {{ vehicle.mileage.toLocaleString('pt-BR') }} km
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        :class="[
                            'rounded-[2rem] border p-4 shadow-2xl',
                            {
                                'border-slate-800 bg-slate-900/50': !isCatalogTemplate,
                                'border-slate-200 bg-white/95 shadow-sm': isCatalogTemplate,
                            },
                        ]"
                    >
                        <div
                            :class="[
                                'overflow-hidden rounded-[1.5rem] p-5',
                                {
                                    'bg-linear-to-br from-slate-900 to-slate-950': !isCatalogTemplate,
                                    'bg-linear-to-br from-slate-100 to-slate-200': isCatalogTemplate,
                                },
                            ]"
                        >
                            <div
                                v-if="featuredVehicles.length > 0"
                                :class="[
                                    'space-y-4',
                                    {
                                        'text-slate-100': !isCatalogTemplate,
                                        'text-slate-900': isCatalogTemplate,
                                    },
                                ]"
                            >
                                <div class="space-y-2">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-slate-500">
                                        {{ t('theme.layout.label') }}
                                    </p>
                                    <h3 class="text-2xl font-black tracking-tight">
                                        {{ featuredVehicles[0].brand }} {{ featuredVehicles[0].model }}
                                    </h3>
                                    <p
                                        :class="[
                                            'text-sm',
                                            {
                                                'text-slate-400': !isCatalogTemplate,
                                                'text-slate-500': isCatalogTemplate,
                                            },
                                        ]"
                                    >
                                        {{ featuredVehicles[0].version }}
                                    </p>
                                </div>

                                <div class="overflow-hidden rounded-2xl border border-slate-800/20">
                                    <img
                                        :src="featuredVehicles[0].image"
                                        :alt="featuredVehicles[0].model"
                                        class="h-52 w-full object-cover object-center"
                                    />
                                </div>

                                <div
                                    :class="[
                                        'grid gap-3 sm:grid-cols-2',
                                        {
                                            'text-slate-300': !isCatalogTemplate,
                                            'text-slate-600': isCatalogTemplate,
                                        },
                                    ]"
                                >
                                    <div
                                        :class="[
                                            'rounded-2xl border p-3',
                                            {
                                                'border-slate-800 bg-slate-950/40': !isCatalogTemplate,
                                                'border-slate-200 bg-white': isCatalogTemplate,
                                            },
                                        ]"
                                    >
                                        <span class="text-[10px] uppercase tracking-[0.2em] text-slate-500">
                                            {{ t('inventory.found') }}
                                        </span>
                                        <p class="mt-1 text-lg font-bold">{{ filteredVehicles.length }}</p>
                                    </div>
                                    <div
                                        :class="[
                                            'rounded-2xl border p-3',
                                            {
                                                'border-slate-800 bg-slate-950/40': !isCatalogTemplate,
                                                'border-slate-200 bg-white': isCatalogTemplate,
                                            },
                                        ]"
                                    >
                                        <span class="text-[10px] uppercase tracking-[0.2em] text-slate-500">
                                            {{ t('theme.layout.selected') }}
                                        </span>
                                        <p class="mt-1 text-lg font-bold">
                                            {{ isCatalogTemplate ? t('theme.layout.catalog') : t('theme.layout.showroom') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section
            :class="[
                'border-t',
                {
                    'border-slate-900/40 bg-slate-950 py-12': !isCatalogTemplate,
                    'border-slate-200 bg-slate-50 py-12': isCatalogTemplate,
                },
            ]"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    :class="[
                        'mb-8 flex items-center justify-between border-b pb-4',
                        {
                            'border-slate-900': !isCatalogTemplate,
                            'border-slate-200': isCatalogTemplate,
                        },
                    ]"
                >
                    <div>
                        <h3
                            :class="[
                                'text-xl font-semibold',
                                { 'text-slate-200': !isCatalogTemplate, 'text-slate-900': isCatalogTemplate },
                            ]"
                        >
                            {{ t('inventory.title') }}
                        </h3>
                                <p
                                    :class="[
                                        'mt-1 text-sm',
                                        'text-slate-500',
                                    ]"
                                >
                                    {{ filteredVehicles.length }} {{ t('inventory.found') }}
                        </p>
                    </div>
                </div>

                <VehicleGrid :vehicles="filteredVehicles" :layout-style="currentStore.layoutStyle" />
            </div>
        </section>
    </div>
</template>

<style scoped>
.store-text-gradient {
    background: linear-gradient(to right, var(--store-accent-color, #fbbf24), var(--store-primary-to, #ea580c));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.store-text-gradient-light {
    background: linear-gradient(to right, var(--store-primary-to, #ea580c), #334155);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
