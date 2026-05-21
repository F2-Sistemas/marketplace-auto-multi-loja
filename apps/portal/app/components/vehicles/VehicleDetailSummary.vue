<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from '~/composables/useI18n';
import UiBadge from '~/components/ui/UiBadge.vue';
import UiCard from '~/components/ui/UiCard.vue';
import VehicleSellerCard from '~/components/vehicles/VehicleSellerCard.vue';

interface VehicleStore {
    name: string;
    slug: string;
    logo?: string;
    whatsapp_number?: string;
    status?: string;
    created_at?: string;
}

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
    store?: VehicleStore;
    location_label?: string;
}

const props = defineProps<{
    vehicle: Vehicle;
    sameStoreCount?: number;
}>();

const emit = defineEmits<{
    (event: 'report'): void;
}>();

const { t } = useI18n();

const formatPrice = (value: number) => {
    return value.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        maximumFractionDigits: 0,
    });
};

const formatMileage = (value: number) => {
    if (value === 0) {
        return '0 km (Novo)';
    }

    return `${value.toLocaleString('pt-BR')} km`;
};

const resolvedFuel = computed(() => {
    if (props.vehicle.fuel) {
        return props.vehicle.fuel;
    }

    return 'Flex';
});
</script>

<template>
    <UiCard body-class="space-y-6">
        <VehicleSellerCard
            :vehicle="vehicle"
            :same-store-count="sameStoreCount || 0"
            @report="emit('report')"
        />

        <div class="space-y-4 border-t border-neutral-100 pt-6">
            <div class="flex flex-wrap items-center gap-2">
                <UiBadge variant="primary" size="md">
                    {{ vehicle.year_manufacture }}/{{ vehicle.year_model }}
                </UiBadge>
                <UiBadge variant="neutral" size="md" class="capitalize">
                    {{ vehicle.transmission }}
                </UiBadge>
            </div>

            <div class="space-y-1">
                <span class="text-sm font-semibold uppercase tracking-wider text-brand-600">
                    {{ vehicle.brand }}
                </span>
                <h1 class="text-2xl font-black tracking-tight leading-tight text-neutral-800 md:text-3xl">
                    {{ vehicle.model }}
                </h1>
                <p class="text-sm font-normal text-neutral-500">
                    {{ vehicle.version }}
                </p>
            </div>
        </div>

        <div class="rounded-2xl border border-brand-100 bg-brand-50 px-5 py-5">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <span class="mb-1 block text-xs font-semibold uppercase tracking-wider text-brand-700">
                        {{ t('vehicle.priceLabel') }}
                    </span>
                    <span class="text-3xl font-black text-brand-800">
                        {{ formatPrice(vehicle.price) }}
                    </span>
                </div>

                <div class="flex items-center gap-2 text-xs font-extrabold text-brand-700">
                    <iconify-icon icon="tabler:discount-check" class="text-lg"></iconify-icon>
                    <span>{{ t('vehicle.priceTrust') }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-2">
            <div class="flex items-center gap-3 rounded-xl border border-neutral-200 bg-white p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-neutral-50 text-neutral-400">
                    <iconify-icon icon="tabler:road" class="text-xl"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <span class="block truncate text-[10px] font-semibold uppercase tracking-wider text-neutral-400">
                        {{ t('vehicle.specs.mileage') }}
                    </span>
                    <span class="block truncate text-sm font-extrabold text-neutral-700">
                        {{ formatMileage(vehicle.mileage) }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3 rounded-xl border border-neutral-200 bg-white p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-neutral-50 text-neutral-400">
                    <iconify-icon icon="tabler:calendar" class="text-xl"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <span class="block truncate text-[10px] font-semibold uppercase tracking-wider text-neutral-400">
                        {{ t('vehicle.specs.year') }}
                    </span>
                    <span class="block truncate text-sm font-extrabold text-neutral-700">
                        {{ vehicle.year_model }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3 rounded-xl border border-neutral-200 bg-white p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-neutral-50 text-neutral-400">
                    <iconify-icon icon="tabler:settings" class="text-xl"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <span class="block truncate text-[10px] font-semibold uppercase tracking-wider text-neutral-400">
                        {{ t('vehicle.specs.transmission') }}
                    </span>
                    <span class="block truncate text-sm font-extrabold text-neutral-700 capitalize">
                        {{ vehicle.transmission }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3 rounded-xl border border-neutral-200 bg-white p-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-neutral-50 text-neutral-400">
                    <iconify-icon icon="tabler:gas-station" class="text-xl"></iconify-icon>
                </div>
                <div class="min-w-0">
                    <span class="block truncate text-[10px] font-semibold uppercase tracking-wider text-neutral-400">
                        {{ t('vehicle.specs.fuel') }}
                    </span>
                    <span class="block truncate text-sm font-extrabold text-neutral-700">
                        {{ resolvedFuel }}
                    </span>
                </div>
            </div>
        </div>

    </UiCard>
</template>
