<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from '#app';
import { useI18n } from '~/composables/useI18n';
import { useFavorites } from '~/composables/useFavorites';
import UiCard from '~/components/ui/UiCard.vue';
import UiBadge from '~/components/ui/UiBadge.vue';
import UiButton from '~/components/ui/UiButton.vue';

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
    fuel?: string;
    city?: {
        name: string;
        state?: {
            uf: string;
        };
    };
    store?: {
        name: string;
        slug: string;
        logo?: string;
    };
}

const props = defineProps<{
    vehicle: Vehicle;
    layout?: 'grid' | 'list';
}>();

const router = useRouter();
const { t } = useI18n();
const { isFavorited, toggleFavorite } = useFavorites();
const { handleImageError } = useImageFallback();

const displayImage = computed(() => {
    if (props.vehicle.images && props.vehicle.images.length > 0) {
        return props.vehicle.images[0];
    }
    return 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80';
});

const formatPrice = (value: number) => {
    return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 });
};

const formatMileage = (value: number) => {
    if (value === 0) return '0 km (Novo)';
    return `${value.toLocaleString('pt-BR')} km`;
};

const handleNavigate = () => {
    router.push(`/veiculos/${props.vehicle.slug}`);
};

const handleStoreClick = (event: Event) => {
    event.stopPropagation();
    if (props.vehicle.store) {
        router.push(`/lojas/${props.vehicle.store.slug}`);
    }
};

const handleFavoriteClick = (event: Event) => {
    event.stopPropagation();
    toggleFavorite(props.vehicle);
};
</script>

<template>
    <UiCard
        variant="interactive"
        @click="handleNavigate"
        :body-class="
            layout === 'list'
                ? 'p-0 flex flex-col sm:flex-row h-full cursor-pointer group'
                : 'p-0 flex flex-col h-full cursor-pointer group'
        "
    >
        <!-- Image Cover Wrapper -->
        <div
            :class="[
                'relative bg-neutral-100 overflow-hidden shrink-0',
                layout === 'list' ? 'w-full sm:w-72 md:w-80 h-48 sm:h-auto' : 'w-full aspect-video',
            ]"
        >
            <img
                :src="displayImage"
                :alt="vehicle.title"
                @error="handleImageError($event, vehicle.title)"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                loading="lazy"
            />
            <!-- Transmission badge absolute -->
            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                <UiBadge variant="secondary" size="sm">{{ vehicle.year_manufacture }}/{{ vehicle.year_model }}</UiBadge>
                <UiBadge v-if="vehicle.transmission.toLowerCase().includes('auto')" variant="primary" size="sm">
                    {{ t('search.transmissionAuto') }}
                </UiBadge>
            </div>

            <!-- Favorite button -->
            <button
                @click="handleFavoriteClick"
                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/95 backdrop-blur-xs flex items-center justify-center text-neutral-600 hover:text-red-500 transition-all shadow-sm z-10 hover:scale-110 active:scale-95 cursor-pointer"
                :aria-label="isFavorited(vehicle.id) ? 'Remover dos favoritos' : 'Adicionar aos favoritos'"
            >
                <iconify-icon
                    :icon="isFavorited(vehicle.id) ? 'fa7-solid:heart' : 'fa7-regular:heart'"
                    :class="[
                        'text-base transition-colors',
                        isFavorited(vehicle.id) ? 'text-red-500' : 'text-neutral-500',
                    ]"
                ></iconify-icon>
            </button>
        </div>

        <!-- Details block -->
        <div class="flex-1 flex flex-col justify-between p-5">
            <div class="space-y-2">
                <!-- Brand / Model Header -->
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-brand-600 block">
                        {{ vehicle.brand }}
                    </span>
                    <h4
                        class="font-extrabold text-neutral-800 text-base line-clamp-1 group-hover:text-brand-600 transition-colors"
                    >
                        {{ vehicle.model }}
                    </h4>
                    <p class="text-xs text-neutral-400 font-medium line-clamp-1">
                        {{ vehicle.version }}
                    </p>
                </div>

                <!-- Spec badging -->
                <div
                    class="grid grid-cols-2 gap-y-2 gap-x-4 py-2 text-xs text-neutral-500 font-normal border-y border-neutral-100"
                >
                    <div class="flex items-center gap-1.5">
                        <iconify-icon icon="tabler:road" class="text-neutral-400 text-sm"></iconify-icon>
                        <span class="truncate">{{ formatMileage(vehicle.mileage) }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <iconify-icon icon="tabler:settings" class="text-neutral-400 text-sm"></iconify-icon>
                        <span class="capitalize truncate">{{ vehicle.transmission }}</span>
                    </div>
                    <div v-if="vehicle.fuel" class="flex items-center gap-1.5">
                        <iconify-icon icon="tabler:gas-station" class="text-neutral-400 text-sm"></iconify-icon>
                        <span class="truncate">{{ vehicle.fuel }}</span>
                    </div>
                    <div v-if="vehicle.city" class="flex items-center gap-1.5 min-w-0">
                        <iconify-icon icon="tabler:map-pin" class="text-neutral-400 text-sm shrink-0"></iconify-icon>
                        <span class="truncate">
                            {{ vehicle.city.name }}{{ vehicle.city.state ? '/' + vehicle.city.state.uf : '' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="pt-4 space-y-4">
                <!-- Price Block -->
                <div class="flex items-baseline justify-between">
                    <span class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">
                        {{ t('vehicle.priceLabel') }}
                    </span>
                    <span class="text-lg font-extrabold text-neutral-900">
                        {{ formatPrice(vehicle.price) }}
                    </span>
                </div>

                <!-- Store detail links -->
                <div v-if="vehicle.store" class="flex items-center justify-between border-t border-neutral-100 pt-3">
                    <button
                        @click="handleStoreClick"
                        class="flex items-center gap-2 hover:text-brand-600 text-left transition-colors cursor-pointer group/store"
                    >
                        <div
                            class="w-6 h-6 rounded-full bg-neutral-100 flex items-center justify-center overflow-hidden border border-neutral-200"
                        >
                            <img
                                v-if="vehicle.store.logo"
                                :src="vehicle.store.logo"
                                :alt="vehicle.store.name"
                                @error="handleImageError($event, vehicle.store.name)"
                                class="w-full h-full object-cover"
                            />
                            <iconify-icon
                                v-else
                                icon="tabler:building-store"
                                class="text-neutral-400 text-xs"
                            ></iconify-icon>
                        </div>
                        <span
                            class="text-xs font-semibold text-neutral-600 group-hover/store:text-brand-600 truncate max-w-[150px]"
                        >
                            {{ vehicle.store.name }}
                        </span>
                    </button>

                    <UiButton
                        variant="ghost"
                        size="sm"
                        class="!p-0 text-brand-600 hover:text-brand-700 !h-auto font-semibold text-xs"
                    >
                        <span>{{ t('vehicle.actions.viewDetails') }}</span>
                        <template #icon>
                            <iconify-icon icon="tabler:chevron-right" class="text-xs"></iconify-icon>
                        </template>
                    </UiButton>
                </div>
            </div>
        </div>
    </UiCard>
</template>
