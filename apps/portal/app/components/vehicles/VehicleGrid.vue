<script setup lang="ts">
import { useI18n } from '~/composables/useI18n';
import VehicleCard from './VehicleCard.vue';
import UiSkeleton from '~/components/ui/UiSkeleton.vue';
import UiCard from '~/components/ui/UiCard.vue';

interface Vehicle {
    id: number;
    title: string;
    brand: string;
    model: string;
    version: string;
    year_manufacture: number;
    year_model: number;
    mileage: number;
    transmission: string;
    price: number;
    slug: string;
    images: string[];
    store?: {
        name: string;
        slug: string;
        logo?: string;
    };
}

defineProps<{
    vehicles: Vehicle[];
    loading?: boolean;
    layout?: 'grid' | 'list';
}>();

const { t } = useI18n();
</script>

<template>
    <div>
        <!-- Loading Skeletal Matrix -->
        <!-- Loading Skeletal Matrix -->
        <div
            v-if="loading"
            :class="layout === 'list' ? 'flex flex-col gap-4' : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6'"
            id="vehicles-loading-skeleton"
        >
            <UiCard
                v-for="n in 6"
                :key="n"
                :class="['flex pointer-events-none', layout === 'list' ? 'flex-row h-48' : 'flex-col h-full']"
            >
                <UiSkeleton
                    type="rect"
                    :height="layout === 'list' ? '100%' : '180px'"
                    :width="layout === 'list' ? '240px' : '100%'"
                    radius="none"
                    :class="layout === 'list' ? '-my-6 -ml-6 mr-5 w-60' : '-mt-6 -mx-6 mb-5'"
                />
                <div class="space-y-4 flex-1 py-4">
                    <UiSkeleton type="text" width="40%" />
                    <UiSkeleton type="text" width="80%" />
                    <UiSkeleton type="text" width="60%" />
                    <div class="flex gap-4 pt-2">
                        <UiSkeleton type="text" width="30%" />
                        <UiSkeleton type="text" width="30%" />
                    </div>
                    <div class="flex justify-between items-center pt-4 border-t border-neutral-100 mt-auto">
                        <UiSkeleton type="text" width="20%" />
                        <UiSkeleton type="text" width="40%" />
                    </div>
                </div>
            </UiCard>
        </div>

        <!-- Empty State Showcase -->
        <div
            v-else-if="vehicles.length === 0"
            class="text-center py-16 px-4 bg-white border border-neutral-200 rounded-card shadow-card flex flex-col items-center justify-center space-y-4 max-w-xl mx-auto"
            id="vehicles-empty-state"
        >
            <div class="w-16 h-16 rounded-full bg-brand-50 flex items-center justify-center text-brand-500">
                <iconify-icon icon="tabler:car-off" class="text-3xl"></iconify-icon>
            </div>
            <div class="space-y-1">
                <h3 class="font-extrabold text-neutral-800 text-lg">
                    {{ t('search.emptyStateTitle') }}
                </h3>
                <p class="text-sm text-neutral-400 font-medium">
                    {{ t('search.emptyStateDesc') }}
                </p>
            </div>
        </div>

        <!-- Inventory list display -->
        <TransitionGroup
            v-else
            tag="div"
            :class="layout === 'list' ? 'flex flex-col gap-4' : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6'"
            id="vehicles-active-grid"
            name="fade-list"
        >
            <div v-for="vehicle in vehicles" :key="vehicle.id" :class="layout === 'list' ? '' : 'h-full'">
                <VehicleCard :vehicle="vehicle" :layout="layout" />
            </div>
        </TransitionGroup>
    </div>
</template>

<style scoped>
.fade-list-enter-active,
.fade-list-leave-active {
    transition: all 0.25s ease-in-out;
}
.fade-list-enter-from,
.fade-list-leave-to {
    opacity: 0;
    transform: translateY(8px);
}
.fade-list-move {
    transition: transform 0.25s ease-in-out;
}
</style>
