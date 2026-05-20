<script setup lang="ts">
import { useTenant } from '~/composables/useTenant';
import { useI18n } from '~/composables/useI18n';
import VehicleFilters from '~/components/vehicle/VehicleFilters.vue';
import VehicleGrid from '~/components/vehicle/VehicleGrid.vue';

const { currentStore, searchQuery, filteredVehicles } = useTenant();
const { t } = useI18n();
</script>

<template>
    <div>
        <!-- Hero Banner -->
        <section class="relative py-20 md:py-28 overflow-hidden bg-slate-950">
            <!-- Background Gradients -->
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-900/60 to-slate-950"></div>
            <div
                class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-gradient-to-br from-store-primary-from/20 to-transparent blur-3xl"
            ></div>
            <div
                class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-gradient-to-br from-store-accent-color/10 to-transparent blur-3xl"
            ></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <!-- Badge -->
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900/80 border border-slate-800 text-xs font-normal text-slate-300 mb-6 shadow-sm"
                >
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ t('inventory.badge') }}
                </div>

                <h2 class="text-4xl md:text-6xl font-black text-slate-100 tracking-tight leading-none mb-6">
                    {{ t('inventory.hero.title_part1') }}
                    <span class="bg-gradient-to-r bg-clip-text text-transparent store-text-gradient">
                        {{ t('inventory.hero.title_highlight') }}
                    </span>
                </h2>

                <p class="text-base md:text-lg text-slate-400 max-w-2xl mx-auto leading-relaxed mb-10">
                    {{ currentStore.tagline }}. {{ t('inventory.subtitle') }}.
                </p>

                <!-- Filters Box -->
                <VehicleFilters v-model="searchQuery" />
            </div>
        </section>

        <!-- Main Stock Section -->
        <section class="py-12 bg-slate-950 border-t border-slate-900/40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stock Header Info -->
                <div class="flex justify-between items-center mb-8 border-b border-slate-900 pb-4">
                    <div>
                        <h3 class="text-xl font-semibold text-slate-200">{{ t('inventory.title') }}</h3>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">
                        {{ filteredVehicles.length }} {{ t('inventory.found') }}
                    </span>
                </div>

                <!-- Vehicle Grid — cards navigate to /vehicle/:id -->
                <VehicleGrid :vehicles="filteredVehicles" />
            </div>
        </section>
    </div>
</template>

<style scoped>
.store-text-gradient {
    background: linear-gradient(to right, var(--store-accent-color, #fbbf24), var(--store-primary-to, #ea580c));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
