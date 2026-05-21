<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRoute } from '#app';
import { useI18n } from '~/composables/useI18n';
import { useInterestLists } from '~/composables/useInterestLists';
import Breadcrumb from '~/components/layout/Breadcrumb.vue';
import SeoPageHeader from '~/components/layout/SeoPageHeader.vue';
import VehicleGrid from '~/components/vehicles/VehicleGrid.vue';
import UiButton from '~/components/ui/UiButton.vue';

definePageMeta({
    layout: 'default',
});

const route = useRoute();
const { t } = useI18n();
const { fetchPublicInterestList } = useInterestLists();

const loading = ref(true);
const notFound = ref(false);
const list = ref<null | {
    id: number;
    name: string;
    slug: string;
    description?: string | null;
    is_public: boolean;
    items_count: number;
    public_url?: string | null;
}>(null);
const vehicles = ref<any[]>([]);

const mappedVehicles = computed(() => {
    return vehicles.value.map((vehicle: any) => {
        return {
            id: vehicle.id,
            title: vehicle.title || `${vehicle.brand_name || ''} ${vehicle.model_name || ''}`,
            brand: vehicle.brand_name || '',
            model: vehicle.model_name || '',
            version: vehicle.version || '',
            year_manufacture: Number(vehicle.year_manufacture) || 2020,
            year_model: Number(vehicle.year_model) || 2021,
            mileage: Number(vehicle.mileage) || 0,
            transmission: vehicle.transmission || '',
            price: Number(vehicle.price) || 0,
            slug: vehicle.slug || '',
            images: [vehicle.main_image || 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80'],
            fuel: vehicle.fuel || '',
            color: vehicle.color || '',
            city: vehicle.city_name ? { name: vehicle.city_name } : undefined,
        };
    });
});

const loadList = async () => {
    loading.value = true;
    notFound.value = false;

    try {
        const response = await fetchPublicInterestList(String(route.params.slug));
        list.value = response.list;
        vehicles.value = response.vehicles;
    } catch {
        list.value = null;
        vehicles.value = [];
        notFound.value = true;
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    await loadList();
});
</script>

<template>
    <div class="container mx-auto max-w-7xl space-y-6 px-4 py-6 md:px-6">
        <Breadcrumb
            :items="[{ label: t('home'), to: '/' }, { label: t('interestLists.publicPageTitle') }]"
        />

        <div v-if="loading" class="rounded-card border border-neutral-200 bg-white p-10 text-center text-sm text-neutral-500">
            {{ t('interestLists.loading') }}
        </div>

        <div
            v-else-if="notFound || !list"
            class="mx-auto max-w-xl rounded-card border border-neutral-200 bg-white p-10 text-center shadow-sm"
        >
            <h1 class="text-2xl font-black tracking-tight text-neutral-800">
                {{ t('interestLists.privatePageTitle') }}
            </h1>
            <p class="mt-3 text-sm text-neutral-500">
                {{ t('interestLists.privatePageBody') }}
            </p>
            <div class="mt-6">
                <UiButton variant="primary" size="md" to="/veiculos">
                    {{ t('interestLists.viewVehicles') }}
                </UiButton>
            </div>
        </div>

        <div v-else class="space-y-6">
            <SeoPageHeader
                :title="list.name"
                :description="list.description || t('interestLists.publicPageDescription')"
            />

            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-emerald-700">
                    {{ t('interestLists.publicBadge') }}
                </span>
                <span class="text-xs text-neutral-400">
                    {{ list.items_count }} veículos
                </span>
            </div>

            <VehicleGrid :vehicles="mappedVehicles" :loading="false" layout="grid" />
        </div>
    </div>
</template>
