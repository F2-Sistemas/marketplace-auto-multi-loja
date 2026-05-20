<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from '~/composables/useI18n';
import UiBadge from '~/components/ui/UiBadge.vue';

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
    color?: string;
    fuel?: string;
}

const props = defineProps<{
    vehicle: Vehicle;
}>();

const { t } = useI18n();

const formatPrice = (value: number) => {
    return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 });
};

const formatMileage = (value: number) => {
    if (value === 0) return '0 km (Novo)';
    return `${value.toLocaleString('pt-BR')} km`;
};

const resolvedFuel = computed(() => {
    return props.vehicle.fuel || 'Flex';
});
</script>

<template>
    <div class="space-y-4">
        <!-- Brand / Version Header -->
        <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2">
                <UiBadge variant="primary" size="md">{{ vehicle.year_manufacture }}/{{ vehicle.year_model }}</UiBadge>
                <UiBadge variant="neutral" size="md" class="capitalize">
                    {{ vehicle.transmission }}
                </UiBadge>
            </div>

            <div class="space-y-1">
                <span class="text-sm font-semibold uppercase tracking-wider text-brand-600">
                    {{ vehicle.brand }}
                </span>
                <h1 class="text-2xl md:text-3xl font-black text-neutral-800 tracking-tight leading-tight">
                    {{ vehicle.model }}
                </h1>
                <p class="text-sm font-normal text-neutral-500">
                    {{ vehicle.version }}
                </p>
            </div>
        </div>

        <!-- Price Section -->
        <div
            class="p-6 bg-brand-50 border border-brand-100 rounded-card flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
            <div>
                <span class="text-xs font-semibold text-brand-700 uppercase tracking-wider block mb-1">
                    {{ t('vehicle.priceLabel') }}
                </span>
                <span class="text-3xl font-black text-brand-800">
                    {{ formatPrice(vehicle.price) }}
                </span>
            </div>
            <div class="flex items-center gap-2 text-xs font-extrabold text-brand-700">
                <iconify-icon icon="tabler:discount-check" class="text-lg"></iconify-icon>
                <span>Garantia de Procedência Garantida</span>
            </div>
        </div>

        <!-- Core highlight specs grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 gap-4">
            <div class="p-4 border border-neutral-200 rounded-lg bg-white flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-neutral-50 flex items-center justify-center text-neutral-400 shrink-0"
                >
                    <iconify-icon icon="tabler:road" class="text-xl"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider block truncate">
                        {{ t('vehicle.specs.mileage') }}
                    </span>
                    <span class="text-sm font-extrabold text-neutral-700 block truncate">
                        {{ formatMileage(vehicle.mileage) }}
                    </span>
                </div>
            </div>

            <div class="p-4 border border-neutral-200 rounded-lg bg-white flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-neutral-50 flex items-center justify-center text-neutral-400 shrink-0"
                >
                    <iconify-icon icon="tabler:calendar" class="text-xl"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider block truncate">
                        Ano Modelo
                    </span>
                    <span class="text-sm font-extrabold text-neutral-700 block truncate">
                        {{ vehicle.year_model }}
                    </span>
                </div>
            </div>

            <div class="p-4 border border-neutral-200 rounded-lg bg-white flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-neutral-50 flex items-center justify-center text-neutral-400 shrink-0"
                >
                    <iconify-icon icon="tabler:settings" class="text-xl"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider block truncate">
                        Câmbio
                    </span>
                    <span class="text-sm font-extrabold text-neutral-700 capitalize block truncate">
                        {{ vehicle.transmission }}
                    </span>
                </div>
            </div>

            <div class="p-4 border border-neutral-200 rounded-lg bg-white flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-neutral-50 flex items-center justify-center text-neutral-400 shrink-0"
                >
                    <iconify-icon icon="tabler:gas-station" class="text-xl"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-semibold text-neutral-400 uppercase tracking-wider block truncate">
                        {{ t('vehicle.specs.fuel') }}
                    </span>
                    <span class="text-sm font-extrabold text-neutral-700 block truncate">
                        {{ resolvedFuel }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
