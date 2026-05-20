<script setup lang="ts">
import { onMounted } from 'vue';
import { useI18n } from '~/composables/useI18n';
import { useFavorites } from '~/composables/useFavorites';
import Breadcrumb from '~/components/layout/Breadcrumb.vue';
import SeoPageHeader from '~/components/layout/SeoPageHeader.vue';
import VehicleGrid from '~/components/vehicles/VehicleGrid.vue';
import UiButton from '~/components/ui/UiButton.vue';

definePageMeta({
    layout: 'default',
});

const { t } = useI18n();
const { favoriteVehicles, fetchFavorites } = useFavorites();

onMounted(async () => {
    await fetchFavorites();
});

const refresh = async () => {
    await fetchFavorites();
};
</script>

<template>
    <div class="container max-w-7xl mx-auto px-4 md:px-6 py-6 space-y-6">
        <Breadcrumb
            :items="[{ label: t('favorites.breadcrumbAccount'), to: '/conta' }, { label: t('favorites.titleAccount') }]"
        />

        <SeoPageHeader :title="t('favorites.titleAccount')" :description="t('favorites.description')" />

        <div class="space-y-6">
            <div
                class="flex items-center justify-between p-4 bg-white border border-neutral-200 rounded-card shadow-sm"
            >
                <div class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">
                    <span>{{ favoriteVehicles.length }}</span>
                    <span class="text-neutral-400 font-normal lowercase">
                        {{ favoriteVehicles.length === 1 ? t('favorites.countSingle') : t('favorites.countPlural') }}
                    </span>
                </div>

                <UiButton variant="outline" size="sm" @click="refresh">
                    <iconify-icon icon="tabler:refresh"></iconify-icon>
                    <span>{{ t('favorites.refresh') }}</span>
                </UiButton>
            </div>

            <VehicleGrid :vehicles="favoriteVehicles" :loading="false" layout="grid" />
        </div>
    </div>
</template>
