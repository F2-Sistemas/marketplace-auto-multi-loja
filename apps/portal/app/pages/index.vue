<script setup lang="ts">
import { onMounted } from 'vue';
import { useVehicles } from '~/composables/useVehicles';
import { useI18n } from '~/composables/useI18n';
import VehicleSearchBox from '~/components/search/VehicleSearchBox.vue';
import VehicleGrid from '~/components/vehicles/VehicleGrid.vue';
import StorePublicCard from '~/components/stores/StorePublicCard.vue';
import SeoLandingSection from '~/components/layout/SeoLandingSection.vue';
import UiButton from '~/components/ui/UiButton.vue';

definePageMeta({
    layout: 'default',
});

const { vehicles, stores, pending } = useVehicles();
const { t } = useI18n();
</script>

<template>
    <div class="space-y-16 pb-16">
        <!-- Premium Above-the-fold Hero Section -->
        <section class="relative bg-neutral-900 overflow-hidden py-24 md:py-32 text-center text-white">
            <!-- Gradient overlay and visual curves -->
            <div
                class="absolute inset-0 bg-gradient-to-tr from-brand-900/80 via-neutral-950/90 to-brand-800/40 z-0"
            ></div>
            <div
                class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white/5 via-transparent to-transparent opacity-60"
            ></div>

            <div class="container max-w-7xl mx-auto px-4 md:px-6 relative z-10 space-y-8">
                <!-- Floating brand badge -->
                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/10 backdrop-blur-xs rounded-full border border-white/10 text-xs font-semibold uppercase tracking-wider text-brand-300"
                >
                    <iconify-icon icon="tabler:sparkles" class="text-sm"></iconify-icon>
                    <span>Estoque Integrado Multiloja</span>
                </div>

                <div class="space-y-4 max-w-3xl mx-auto">
                    <h1 class="text-4xl md:text-6xl font-black tracking-tight leading-tight">
                        {{ t('hero.title') }}
                    </h1>
                    <p class="text-base md:text-lg text-neutral-300 font-normal max-w-xl mx-auto leading-relaxed">
                        {{ t('hero.subtitle') }}
                    </p>
                </div>

                <!-- Search Bar component -->
                <VehicleSearchBox class="pt-4" />
            </div>
        </section>

        <!-- Quick Brand Badges -->
        <section class="container max-w-7xl mx-auto px-4 md:px-6">
            <div
                class="p-6 bg-white border border-neutral-100 rounded-card shadow-sm flex flex-wrap items-center justify-center gap-4"
            >
                <span class="text-xs font-semibold text-neutral-400 uppercase tracking-wider mr-2">
                    Busque por marcas
                </span>
                <NuxtLink
                    v-for="brand in ['Chevrolet', 'Fiat', 'Ford', 'Honda', 'Hyundai', 'Jeep', 'Toyota', 'Volkswagen']"
                    :key="brand"
                    :to="`/veiculos?brand=${brand}`"
                    class="px-4 py-2 border border-neutral-200 hover:border-brand-500 rounded-button bg-white text-xs font-normal text-neutral-600 hover:text-brand-700 transition cursor-pointer select-none"
                >
                    {{ brand }}
                </NuxtLink>
            </div>
        </section>

        <!-- Featured Vehicles Showcase -->
        <section class="container max-w-7xl mx-auto px-4 md:px-6 space-y-6">
            <div class="flex items-end justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-semibold uppercase tracking-wider text-brand-600">Oportunidades</span>
                    <h2 class="text-2xl md:text-3xl font-black text-neutral-800 tracking-tight">Destaques da Semana</h2>
                </div>

                <NuxtLink to="/veiculos">
                    <UiButton variant="outline" size="sm">
                        <span>Ver Todos</span>
                        <template #icon>
                            <iconify-icon icon="tabler:arrow-narrow-right"></iconify-icon>
                        </template>
                    </UiButton>
                </NuxtLink>
            </div>

            <VehicleGrid :vehicles="vehicles.slice(0, 3)" :loading="pending" />
        </section>

        <!-- Popular Stores/Concessionárias -->
        <section class="container max-w-7xl mx-auto px-4 md:px-6 space-y-6">
            <div class="space-y-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-brand-600">Parceiros</span>
                <h2 class="text-2xl md:text-3xl font-black text-neutral-800 tracking-tight">Nossas Concessionárias</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="store in stores.slice(0, 3)" :key="store.id">
                    <StorePublicCard :store="store" />
                </div>
            </div>
        </section>

        <!-- SEO text block -->
        <section class="container max-w-7xl mx-auto px-4 md:px-6">
            <SeoLandingSection />
        </section>
    </div>
</template>
