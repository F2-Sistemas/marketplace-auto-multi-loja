<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from '~/composables/useI18n';

interface Vehicle {
    id: number;
    brand: string;
    model: string;
    version: string;
    year_manufacture: number;
    year_model: number;
    mileage: number;
    transmission: string;
    color?: string;
    fuel?: string;
}

const props = defineProps<{
    vehicle: Vehicle;
}>();

const { t } = useI18n();

const formatMileage = (value: number) => {
    if (value === 0) return '0 km (Novo)';
    return `${value.toLocaleString('pt-BR')} km`;
};

const specItems = computed(() => {
    return [
        { label: 'Marca', value: props.vehicle.brand },
        { label: 'Modelo', value: props.vehicle.model },
        { label: 'Versão', value: props.vehicle.version },
        { label: 'Ano Fabricação', value: props.vehicle.year_manufacture },
        { label: 'Ano Modelo', value: props.vehicle.year_model },
        { label: t('vehicle.specs.mileage'), value: formatMileage(props.vehicle.mileage) },
        { label: t('vehicle.specs.transmission'), value: props.vehicle.transmission, capitalize: true },
        { label: t('vehicle.specs.color'), value: props.vehicle.color || 'Não especificada' },
        { label: t('vehicle.specs.fuel'), value: props.vehicle.fuel || 'Flex' },
    ];
});
</script>

<template>
    <div class="divide-y divide-neutral-100">
        <div v-for="(spec, index) in specItems" :key="index" class="py-3 flex justify-between items-center text-sm">
            <span class="font-semibold text-neutral-400">
                {{ spec.label }}
            </span>
            <span class="font-extrabold text-neutral-800" :class="{ capitalize: spec.capitalize }">
                {{ spec.value }}
            </span>
        </div>
    </div>
</template>
