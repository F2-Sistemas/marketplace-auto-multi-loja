<script setup lang="ts">
import { useRouter } from 'vue-router';
import type { StoreLayoutStyle, Vehicle } from '~/composables/useTenant';
import { useI18n } from '~/composables/useI18n';
import UiCard from '~/components/ui/UiCard.vue';
import UiBadge from '~/components/ui/UiBadge.vue';

interface Props {
    vehicle: Vehicle;
    layoutStyle?: StoreLayoutStyle;
}
defineProps<Props>();

const router = useRouter();
const { t } = useI18n();
const { handleImageError } = useImageFallback();

const formatPrice = (value: number) => {
    return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

const goToDetail = (vehicle: Vehicle) => {
    router.push(`/vehicle/${vehicle.id}`);
};
</script>

<template>
    <UiCard
        hover
        @click="goToDetail(vehicle)"
        :class="[
            'group cursor-pointer flex flex-col h-full overflow-hidden',
            {
                'border border-slate-800 bg-slate-900/40 shadow-lg': layoutStyle !== 'catalog',
                'border border-slate-200 bg-white shadow-sm': layoutStyle === 'catalog',
            },
        ]"
    >
        <!-- Image Wrapper -->
        <div
            :class="[
                'relative overflow-hidden',
                {
                    'aspect-video bg-slate-950': layoutStyle !== 'catalog',
                    'h-44 bg-slate-100': layoutStyle === 'catalog',
                },
            ]"
        >
            <img
                :src="vehicle.image"
                :alt="`${vehicle.brand} ${vehicle.model}`"
                @error="handleImageError($event, `${vehicle.brand} ${vehicle.model}`)"
                :class="[
                    'h-full w-full object-cover object-center transition-transform duration-500 group-hover:scale-105',
                    {
                        'opacity-95': layoutStyle === 'catalog',
                    },
                ]"
                loading="lazy"
            />
            <div
                :class="[
                    'absolute inset-0',
                    {
                        'bg-gradient-to-t from-slate-950/40 to-transparent': layoutStyle !== 'catalog',
                        'bg-linear-to-t from-white/20 to-transparent': layoutStyle === 'catalog',
                    },
                ]"
            ></div>
        </div>

        <!-- Details Container -->
        <div :class="['flex flex-grow flex-col justify-between p-4', { 'p-5': layoutStyle !== 'catalog' }]">
            <div>
                <div class="flex justify-between items-start gap-2">
                    <h3
                        :class="[
                            'leading-tight transition-colors duration-200',
                            {
                                'text-lg font-semibold text-slate-100 group-hover:text-brand-300':
                                    layoutStyle !== 'catalog',
                                'text-base font-bold text-slate-900 group-hover:text-brand-600':
                                    layoutStyle === 'catalog',
                            },
                        ]"
                    >
                        {{ vehicle.brand }}
                        <span :class="['font-medium', { 'text-slate-300': layoutStyle !== 'catalog', 'text-slate-600': layoutStyle === 'catalog' }]">
                            {{ vehicle.model }}
                        </span>
                    </h3>
                </div>
                <p class="mt-1 line-clamp-1 text-xs text-slate-500">
                    {{ vehicle.version }}
                </p>
            </div>

            <div class="mt-4">
                <!-- Spec Badges -->
                <div :class="['mb-4 flex flex-wrap gap-1.5', { 'mb-4': layoutStyle !== 'catalog', 'mb-3': layoutStyle === 'catalog' }]">
                    <UiBadge :variant="layoutStyle === 'catalog' ? 'neutral' : 'neutral'">
                        <iconify-icon icon="tabler:calendar" class="text-xs"></iconify-icon>
                        {{ vehicle.year }}
                    </UiBadge>
                    <UiBadge :variant="layoutStyle === 'catalog' ? 'neutral' : 'neutral'">
                        <iconify-icon icon="tabler:road" class="text-xs"></iconify-icon>
                        {{ vehicle.mileage.toLocaleString('pt-BR') }} {{ t('specs.mileage') }}
                    </UiBadge>
                    <UiBadge :variant="layoutStyle === 'catalog' ? 'neutral' : 'neutral'">
                        <iconify-icon icon="tabler:settings" class="text-xs"></iconify-icon>
                        {{ vehicle.transmission }}
                    </UiBadge>
                </div>

                <!-- Price and CTA -->
                <div
                    :class="[
                        'mt-3 flex items-center justify-between border-t pt-3',
                        {
                            'border-slate-800/80': layoutStyle !== 'catalog',
                            'border-slate-200': layoutStyle === 'catalog',
                        },
                    ]"
                >
                    <div>
                        <span class="block text-xs text-slate-500">
                            {{ t('detail.special_price') }}
                        </span>
                        <span
                            :class="[
                                'font-extrabold',
                                {
                                    'text-xl text-slate-100': layoutStyle !== 'catalog',
                                    'text-lg text-slate-900': layoutStyle === 'catalog',
                                },
                            ]"
                        >
                            {{ formatPrice(vehicle.price) }}
                        </span>
                    </div>

                    <span
                        :class="[
                            'rounded-lg p-2 transition-all duration-300',
                            {
                                'bg-slate-800/80 text-slate-100 group-hover:bg-brand-500 group-hover:text-slate-950':
                                    layoutStyle !== 'catalog',
                                'bg-slate-100 text-slate-700 group-hover:bg-brand-500 group-hover:text-white':
                                    layoutStyle === 'catalog',
                            },
                        ]"
                    >
                        <iconify-icon icon="tabler:chevron-right" class="w-5 h-5 block text-lg"></iconify-icon>
                    </span>
                </div>
            </div>
        </div>
    </UiCard>
</template>

<style scoped>
</style>
