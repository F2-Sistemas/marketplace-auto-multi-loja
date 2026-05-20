<script setup lang="ts">
import { ref, computed } from 'vue';
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
        state?: { uf: string };
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

// ── Image carousel ────────────────────────────────────────────────────────────
const currentIndex = ref(0);
const isHovering = ref(false);

const allImages = computed(() => {
    if (props.vehicle.images && props.vehicle.images.length > 0) return props.vehicle.images;
    return ['https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80'];
});

const displayImage = computed(() => allImages.value[currentIndex.value]);
const imageCount = computed(() => allImages.value.length);
const hasMultiple = computed(() => imageCount.value > 1);

const prevImage = (e: Event) => {
    e.stopPropagation();
    currentIndex.value = currentIndex.value === 0 ? imageCount.value - 1 : currentIndex.value - 1;
};

const nextImage = (e: Event) => {
    e.stopPropagation();
    currentIndex.value = currentIndex.value === imageCount.value - 1 ? 0 : currentIndex.value + 1;
};

const goToImage = (idx: number, e: Event) => {
    e.stopPropagation();
    currentIndex.value = idx;
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const formatPrice = (value: number) =>
    value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL', maximumFractionDigits: 0 });

const formatMileage = (value: number) =>
    value === 0 ? '0 km (Novo)' : `${value.toLocaleString('pt-BR')} km`;

const isAutomatic = computed(() => props.vehicle.transmission.toLowerCase().includes('auto'));

const handleNavigate = () => router.push(`/veiculos/${props.vehicle.slug}`);

const handleStoreClick = (e: Event) => {
    e.stopPropagation();
    if (props.vehicle.store) router.push(`/lojas/${props.vehicle.store.slug}`);
};

const handleFavoriteClick = (e: Event) => {
    e.stopPropagation();
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
        <!-- ── Image Wrapper ──────────────────────────────────────────────── -->
        <div
            :class="[
                'relative bg-neutral-100 overflow-hidden shrink-0',
                layout === 'list' ? 'w-full sm:w-72 md:w-80 h-48 sm:h-auto' : 'w-full aspect-video',
            ]"
            @mouseenter="isHovering = true"
            @mouseleave="isHovering = false"
        >
            <!-- Main image with crossfade via key -->
            <transition name="img-fade" mode="out-in">
                <img
                    :key="currentIndex"
                    :src="displayImage"
                    :alt="`${vehicle.title} — foto ${currentIndex + 1}`"
                    @error="handleImageError($event, vehicle.title)"
                    class="w-full h-full object-cover"
                    loading="lazy"
                />
            </transition>

            <!-- Prev / Next arrows — only when hovered and multiple images -->
            <template v-if="hasMultiple && isHovering">
                <button
                    @click="prevImage"
                    class="absolute left-2 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-white/90 shadow-md flex items-center justify-center text-neutral-700 hover:bg-white hover:scale-110 transition-all cursor-pointer"
                    aria-label="Foto anterior"
                >
                    <iconify-icon icon="tabler:chevron-left" class="text-base leading-none"></iconify-icon>
                </button>
                <button
                    @click="nextImage"
                    class="absolute right-2 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-white/90 shadow-md flex items-center justify-center text-neutral-700 hover:bg-white hover:scale-110 transition-all cursor-pointer"
                    aria-label="Próxima foto"
                >
                    <iconify-icon icon="tabler:chevron-right" class="text-base leading-none"></iconify-icon>
                </button>
            </template>

            <!-- Top-left badges -->
            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5 z-10">
                <UiBadge variant="secondary" size="sm">
                    {{ vehicle.year_manufacture }}/{{ vehicle.year_model }}
                </UiBadge>
                <UiBadge v-if="isAutomatic" variant="primary" size="sm">
                    {{ t('search.transmissionAuto') }}
                </UiBadge>
            </div>

            <!-- Favorite button top-right -->
            <button
                @click="handleFavoriteClick"
                class="absolute top-3 right-3 z-20 w-8 h-8 rounded-full bg-white/95 backdrop-blur-xs flex items-center justify-center hover:scale-110 active:scale-95 transition-all shadow-sm cursor-pointer"
                :aria-label="isFavorited(vehicle.id) ? 'Remover dos favoritos' : 'Adicionar aos favoritos'"
            >
                <iconify-icon
                    :icon="isFavorited(vehicle.id) ? 'fa7-solid:heart' : 'fa7-regular:heart'"
                    :class="['text-sm transition-colors', isFavorited(vehicle.id) ? 'text-red-500' : 'text-neutral-400']"
                ></iconify-icon>
            </button>

            <!-- Image counter bottom-left -->
            <div
                v-if="hasMultiple"
                class="absolute bottom-2.5 left-3 z-10 flex items-center gap-1 bg-black/55 backdrop-blur-sm rounded-full px-2 py-0.5"
            >
                <iconify-icon icon="tabler:camera" class="text-white/80 text-[11px]"></iconify-icon>
                <span class="text-[11px] font-semibold text-white/90">{{ currentIndex + 1 }}/{{ imageCount }}</span>
            </div>

            <!-- Dot indicators bottom-center — up to 5 dots max -->
            <div
                v-if="hasMultiple && imageCount <= 8"
                class="absolute bottom-2.5 left-1/2 -translate-x-1/2 z-10 flex items-center gap-1"
            >
                <button
                    v-for="(_, idx) in allImages.slice(0, 5)"
                    :key="idx"
                    @click="goToImage(idx, $event)"
                    :class="[
                        'rounded-full transition-all duration-200',
                        idx === currentIndex
                            ? 'w-4 h-1.5 bg-white'
                            : 'w-1.5 h-1.5 bg-white/50 hover:bg-white/80',
                    ]"
                ></button>
                <span v-if="imageCount > 5" class="text-white/60 text-[10px] ml-0.5">+{{ imageCount - 5 }}</span>
            </div>
        </div>

        <!-- ── Details block ─────────────────────────────────────────────── -->
        <div class="flex-1 flex flex-col justify-between p-5">
            <div class="space-y-2">
                <!-- Brand / Model -->
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-brand-600 block">
                        {{ vehicle.brand }}
                    </span>
                    <h4 class="font-extrabold text-neutral-800 text-base line-clamp-1 group-hover:text-brand-600 transition-colors">
                        {{ vehicle.model }}
                    </h4>
                    <p class="text-xs text-neutral-400 font-medium line-clamp-1">{{ vehicle.version }}</p>
                </div>

                <!-- Specs grid -->
                <div class="grid grid-cols-2 gap-y-2 gap-x-4 py-2 text-xs text-neutral-500 font-normal border-y border-neutral-100">
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
                <!-- Price -->
                <div class="flex items-baseline justify-between">
                    <span class="text-xs font-semibold text-neutral-400 uppercase tracking-wider">
                        {{ t('vehicle.priceLabel') }}
                    </span>
                    <span class="text-lg font-extrabold text-neutral-900">
                        {{ formatPrice(vehicle.price) }}
                    </span>
                </div>

                <!-- Store + CTA -->
                <div v-if="vehicle.store" class="flex items-center justify-between border-t border-neutral-100 pt-3">
                    <button
                        @click="handleStoreClick"
                        class="flex items-center gap-2 hover:text-brand-600 text-left transition-colors cursor-pointer group/store"
                    >
                        <div class="w-6 h-6 rounded-full bg-neutral-100 flex items-center justify-center overflow-hidden border border-neutral-200">
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
                        <span class="text-xs font-semibold text-neutral-600 group-hover/store:text-brand-600 truncate max-w-[150px]">
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

<style scoped>
/* Crossfade between images */
.img-fade-enter-active,
.img-fade-leave-active {
    transition: opacity 0.25s ease;
}
.img-fade-enter-from,
.img-fade-leave-to {
    opacity: 0;
}
</style>
