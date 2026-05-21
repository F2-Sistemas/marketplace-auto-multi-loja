<script setup lang="ts">
import type { Vehicle } from '~/composables/useTenant';
import { useI18n } from '~/composables/useI18n';
import VehicleCard from '~/components/vehicle/VehicleCard.vue';
import type { StoreLayoutStyle } from '~/composables/useTenant';

interface Props {
    vehicles: Vehicle[];
    loading?: boolean;
    layoutStyle?: StoreLayoutStyle;
}
defineProps<Props>();
// Cards navigate internally to /vehicle/:id — no emit needed

const { t } = useI18n();
</script>

<template>
    <div
        v-if="loading"
        :class="[
            'grid gap-6 animate-fade-in',
            {
                'grid-cols-1 md:grid-cols-2 xl:grid-cols-3': layoutStyle === 'catalog',
                'grid-cols-1 lg:grid-cols-2': layoutStyle !== 'catalog',
            },
        ]"
    >
        <div
            v-for="i in 6"
            :key="i"
            :class="[
                'overflow-hidden rounded-xl border animate-pulse',
                {
                    'border-slate-800 bg-slate-900/40 h-[380px]': layoutStyle !== 'catalog',
                    'border-slate-200 bg-white h-[360px]': layoutStyle === 'catalog',
                },
            ]"
        >
            <div
                :class="[
                    'bg-slate-800/80',
                    {
                        'aspect-video': layoutStyle !== 'catalog',
                        'h-44': layoutStyle === 'catalog',
                    },
                ]"
            ></div>
            <div class="p-4 space-y-4">
                <div class="h-6 bg-slate-800/80 rounded w-2/3"></div>
                <div class="h-4 bg-slate-800/60 rounded w-1/3"></div>
                <div class="flex gap-2 pt-2">
                    <div class="h-5 bg-slate-800/60 rounded w-16"></div>
                    <div class="h-5 bg-slate-800/60 rounded w-20"></div>
                </div>
                <div class="border-t border-slate-800 pt-3 flex justify-between items-center mt-4">
                    <div class="h-6 bg-slate-800/60 rounded w-1/2"></div>
                    <div class="h-8 bg-slate-800/80 rounded w-8"></div>
                </div>
            </div>
        </div>
    </div>

    <div
        v-else-if="vehicles.length === 0"
        :class="[
            'flex flex-col items-center justify-center text-center py-16 px-4 rounded-xl border animate-fade-in',
            {
                'bg-slate-900/20 border-slate-800/40': layoutStyle !== 'catalog',
                'bg-white border-slate-200': layoutStyle === 'catalog',
            },
        ]"
    >
        <div
            :class="[
                'w-16 h-16 rounded-full flex items-center justify-center mb-4 border',
                {
                    'bg-slate-800/50 text-slate-400 border-slate-700/50': layoutStyle !== 'catalog',
                    'bg-slate-100 text-slate-500 border-slate-200': layoutStyle === 'catalog',
                },
            ]"
        >
            <iconify-icon icon="tabler:car-off" class="text-3xl block"></iconify-icon>
        </div>
        <h3 :class="['text-lg font-semibold mb-1', { 'text-slate-200': layoutStyle !== 'catalog', 'text-slate-800': layoutStyle === 'catalog' }]">
            {{ t('empty.title') }}
        </h3>
        <p class="max-w-sm text-sm text-slate-500">
            {{ t('empty.subtitle') }}
        </p>
    </div>

    <div
        v-else
        :class="[
            'grid gap-6 animate-fade-in',
            {
                'grid-cols-1 md:grid-cols-2 xl:grid-cols-3': layoutStyle === 'catalog',
                'grid-cols-1 lg:grid-cols-2': layoutStyle !== 'catalog',
            },
        ]"
    >
        <VehicleCard
            v-for="vehicle in vehicles"
            :key="vehicle.id"
            :vehicle="vehicle"
            :layout-style="layoutStyle"
        />
    </div>
</template>
