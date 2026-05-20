<script setup lang="ts">
import type { Vehicle } from '~/composables/useTenant';
import { useI18n } from '~/composables/useI18n';
import VehicleCard from '~/components/vehicle/VehicleCard.vue';

interface Props {
    vehicles: Vehicle[];
    loading?: boolean;
}
defineProps<Props>();
// Cards navigate internally to /vehicle/:id — no emit needed

const { t } = useI18n();
</script>

<template>
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in">
        <div
            v-for="i in 6"
            :key="i"
            class="bg-slate-900/40 border border-slate-800 rounded-xl overflow-hidden animate-pulse h-[380px]"
        >
            <div class="aspect-video bg-slate-800/80"></div>
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
        class="flex flex-col items-center justify-center text-center py-16 px-4 bg-slate-900/20 rounded-xl border border-slate-800/40 animate-fade-in"
    >
        <div
            class="w-16 h-16 bg-slate-800/50 rounded-full flex items-center justify-center text-slate-400 mb-4 border border-slate-700/50"
        >
            <iconify-icon icon="tabler:car-off" class="text-3xl block"></iconify-icon>
        </div>
        <h3 class="text-lg font-semibold text-slate-200 mb-1">{{ t('empty.title') }}</h3>
        <p class="text-sm text-slate-500 max-w-sm">{{ t('empty.subtitle') }}</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in">
        <VehicleCard
            v-for="vehicle in vehicles"
            :key="vehicle.id"
            :vehicle="vehicle"
        />
    </div>
</template>
