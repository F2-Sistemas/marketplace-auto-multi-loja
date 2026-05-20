<script setup lang="ts">
import { onMounted } from 'vue';
import { useI18n } from '~/composables/useI18n';
import { useFavorites } from '~/composables/useFavorites';
import Breadcrumb from '~/components/layout/Breadcrumb.vue';
import VehicleGrid from '~/components/vehicles/VehicleGrid.vue';
import UiButton from '~/components/ui/UiButton.vue';

definePageMeta({
    layout: 'default',
});

const { t } = useI18n();
const { favoriteVehicles, favoriteCount, fetchFavorites } = useFavorites();

onMounted(async () => {
    await fetchFavorites();
});
</script>

<template>
    <div class="container max-w-7xl mx-auto px-4 md:px-6 py-6 space-y-6">
        <Breadcrumb :items="[{ label: t('home'), to: '/' }, { label: t('favorites.breadcrumbAccount') }]" />

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-neutral-200 pb-6">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-800 tracking-tight flex items-center gap-2">
                    <iconify-icon icon="fa7-solid:heart" class="text-red-500"></iconify-icon>
                    <span>{{ t('favorites.title') }}</span>
                </h1>
                <p class="text-sm font-normal text-neutral-500 max-w-2xl leading-relaxed">
                    {{ t('favorites.description') }}
                </p>
            </div>

            <div v-if="favoriteCount > 0" class="shrink-0">
                <div
                    class="px-4 py-2 bg-neutral-100 rounded-full border border-neutral-200 text-xs font-semibold text-neutral-600 flex items-center gap-2"
                >
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                    <span>
                        {{ favoriteCount }}
                        {{ favoriteCount === 1 ? t('favorites.countSingle') : t('favorites.countPlural') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="space-y-6">
            <div
                v-if="favoriteCount === 0"
                class="py-16 md:py-24 text-center bg-white border border-neutral-200 rounded-card shadow-xs flex flex-col items-center justify-center max-w-md mx-auto px-6"
            >
                <div
                    class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center text-red-500 mb-4 animate-bounce"
                >
                    <iconify-icon icon="fa7-regular:heart" class="text-3xl"></iconify-icon>
                </div>
                <h3 class="text-lg font-extrabold text-neutral-800">{{ t('favorites.emptyTitle') }}</h3>
                <p class="text-xs text-neutral-400 font-normal mt-2 mb-6 leading-relaxed">
                    {{ t('favorites.emptyDesc') }}
                </p>
                <NuxtLink to="/veiculos">
                    <UiButton variant="primary" size="md" class="font-semibold flex items-center gap-2">
                        <iconify-icon icon="tabler:search"></iconify-icon>
                        <span>{{ t('favorites.searchVehicles') }}</span>
                    </UiButton>
                </NuxtLink>
            </div>

            <div v-else>
                <VehicleGrid :vehicles="favoriteVehicles" :loading="false" layout="grid" />
            </div>
        </div>
    </div>
</template>
