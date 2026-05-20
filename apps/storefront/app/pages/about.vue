<script setup lang="ts">
import { useTenant } from '~/composables/useTenant';
import { useI18n } from '~/composables/useI18n';
import UiCard from '~/components/ui/UiCard.vue';
import UiButton from '~/components/ui/UiButton.vue';

const { currentStore } = useTenant();
const { t } = useI18n();
</script>

<template>
    <div class="py-12 md:py-20 bg-slate-950">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Title -->
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-black text-slate-100 mb-4 tracking-tight">
                    Sobre a
                    <span class="store-text-color">{{ currentStore.name }}</span>
                </h2>
                <p class="text-slate-500 text-sm max-w-lg mx-auto font-medium leading-relaxed">
                    {{ t('about.subtitle') }}
                </p>
            </div>

            <!-- Content Cards -->
            <div class="space-y-12 text-left">
                <UiCard class="p-6 md:p-10 border-slate-800 bg-slate-900/40">
                    <h3 class="text-xl font-semibold text-slate-200 mb-4 flex items-center gap-2">
                        <iconify-icon icon="tabler:history" class="store-text-color text-2xl block"></iconify-icon>
                        {{ t('about.story.title') }}
                    </h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        {{ t('about.story.content') }}
                    </p>
                </UiCard>

                <!-- Pillars Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <UiCard class="p-6 border-slate-800 bg-slate-900/20 flex gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0"
                        >
                            <iconify-icon icon="tabler:shield-check" class="text-2xl block"></iconify-icon>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-200 text-sm mb-1">
                                {{ t('about.pillars.quality.title') }}
                            </h4>
                            <p class="text-xs text-slate-500 leading-normal">{{ t('about.features.laudo') }}</p>
                        </div>
                    </UiCard>

                    <UiCard class="p-6 border-slate-800 bg-slate-900/20 flex gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0"
                        >
                            <iconify-icon icon="tabler:currency-real" class="text-2xl block"></iconify-icon>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-200 text-sm mb-1">
                                {{ t('about.pillars.rates.title') }}
                            </h4>
                            <p class="text-xs text-slate-500 leading-normal">{{ t('about.features.financing') }}</p>
                        </div>
                    </UiCard>

                    <UiCard class="p-6 border-slate-800 bg-slate-900/20 flex gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shrink-0"
                        >
                            <iconify-icon icon="tabler:circle-check" class="text-2xl block"></iconify-icon>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-200 text-sm mb-1">
                                {{ t('about.pillars.provenance.title') }}
                            </h4>
                            <p class="text-xs text-slate-500 leading-normal">{{ t('about.features.warranty') }}</p>
                        </div>
                    </UiCard>

                    <UiCard class="p-6 border-slate-800 bg-slate-900/20 flex gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0"
                        >
                            <iconify-icon icon="tabler:refresh" class="text-2xl block"></iconify-icon>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-200 text-sm mb-1">
                                {{ t('about.pillars.exchange.title') }}
                            </h4>
                            <p class="text-xs text-slate-500 leading-normal">{{ t('about.features.exchange') }}</p>
                        </div>
                    </UiCard>
                </div>

                <!-- Call to Action -->
                <div class="text-center pt-6">
                    <NuxtLink to="/">
                        <UiButton class="px-8 py-3 font-semibold select-none cursor-pointer">
                            {{ t('about.button.inventory') }}
                        </UiButton>
                    </NuxtLink>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.store-text-color {
    color: var(--store-accent-color, #fbbf24);
}
</style>
