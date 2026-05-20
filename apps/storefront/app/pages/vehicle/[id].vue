<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTenant } from '~/composables/useTenant';
import { useI18n } from '~/composables/useI18n';
import UiCard from '~/components/ui/UiCard.vue';
import UiBadge from '~/components/ui/UiBadge.vue';
import LeadForm from '~/components/lead/LeadForm.vue';
import UiButton from '~/components/ui/UiButton.vue';

const route = useRoute();
const router = useRouter();
const { t } = useI18n();
const { currentStore, formatPrice } = useTenant();
const { getApiUrl } = useApi();
const { handleImageError } = useImageFallback();

const vehicleId = computed(() => Number(route.params.id));

// Fetch vehicle data directly from API using vehicle ID
const { data: apiVehicle, pending, error } = await useFetch<any>(
    computed(() => getApiUrl(`/api/vehicles/${vehicleId.value}`)),
    {
        headers: computed(() => ({
            'X-Store-Host': import.meta.client ? window.location.host : '',
        })),
    }
);

// Known special flag names
const POSITIVE_FLAGS = [
    'aceita_troca', 'desconto_troca_usado', 'aceita_ofertas',
    'carro_blindado', 'vidros_blindados', 'gnv_glp',
];
const ALERT_FLAGS = ['carro_sinistro', 'apenas_documentos_ok'];
const FLAG_LABELS: Record<string, { label: string; icon: string; description?: string }> = {
    aceita_troca:         { label: 'Aceita Troca',               icon: 'tabler:arrows-exchange-2' },
    desconto_troca_usado: { label: 'Desconto na Troca do Usado', icon: 'tabler:receipt-discount' },
    aceita_ofertas:       { label: 'Aceita Ofertas',             icon: 'tabler:hand-money' },
    carro_blindado:       { label: 'Carro Blindado',             icon: 'tabler:shield-check' },
    vidros_blindados:     { label: 'Vidros Blindados',           icon: 'tabler:shield' },
    gnv_glp:              { label: 'GNV / GLP',                  icon: 'tabler:gas-station' },
    carro_sinistro:       { label: 'Veículo com Histórico de Sinistro', icon: 'tabler:alert-triangle',
                            description: 'Este veículo possui registro de sinistro. Solicite o histórico detalhado antes de comprar.' },
    apenas_documentos_ok: { label: 'Apenas Documentação Ok',    icon: 'tabler:file-check',
                            description: 'Veículo com documentação regularizada. Verifique demais condições.' },
};

const vehicle = computed(() => {
    if (!apiVehicle.value) return null;
    const v = apiVehicle.value;
    return {
        id: v.id,
        brand: v.brand?.name || '',
        model: v.model?.name || '',
        version: v.version || '',
        price: Number(v.price) || 0,
        year: `${v.year_manufacture}/${v.year_model}`,
        mileage: Number(v.mileage) || 0,
        transmission: v.transmission || '',
        fuel: v.fuel || '',
        color: v.color || '',
        description: v.description || '',
        images: v.images && v.images.length > 0
            ? v.images.map((img: any) => img.image_url || img.path)
            : ['https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?q=80&w=800&auto=format&fit=crop'],
        features: v.features ? v.features.map((f: any) => f.name) : [],
    };
});

const allFeatureNames = computed(() => vehicle.value?.features ?? []);
const positiveFlags  = computed(() => allFeatureNames.value.filter((f: string) => POSITIVE_FLAGS.includes(f)));
const alertFlags     = computed(() => allFeatureNames.value.filter((f: string) => ALERT_FLAGS.includes(f)));
const regularFeatures = computed(() => allFeatureNames.value.filter(
    (f: string) => !POSITIVE_FLAGS.includes(f) && !ALERT_FLAGS.includes(f)
));

// Gallery state
const activeImageIndex = ref(0);
const activeImage = computed(() => vehicle.value?.images[activeImageIndex.value] || '');

// Lead modal state
const showLeadModal = ref(false);
const showAlertAccordion = ref(false);

const goBack = () => router.push('/');
</script>

<template>
    <div class="py-10 bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Back button -->
            <button
                @click="goBack"
                class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition duration-200 mb-8 cursor-pointer group"
            >
                <iconify-icon icon="tabler:arrow-left" class="text-base group-hover:-translate-x-0.5 transition-transform block"></iconify-icon>
                {{ t('button.back') }}
            </button>

            <!-- Loading -->
            <div v-if="pending" class="flex flex-col items-center justify-center py-24 space-y-4">
                <iconify-icon icon="tabler:loader-2" class="text-5xl text-slate-500 animate-spin block"></iconify-icon>
                <p class="text-sm text-slate-500">Carregando detalhes do veículo...</p>
            </div>

            <!-- Error / Not Found -->
            <div v-else-if="error || !vehicle" class="text-center py-20">
                <iconify-icon icon="tabler:mood-sad" class="text-5xl text-slate-600 mb-4 block mx-auto animate-bounce"></iconify-icon>
                <h3 class="text-xl font-semibold text-slate-350">{{ t('detail.not_found.title') }}</h3>
                <p class="text-slate-550 text-xs mt-2">{{ t('detail.not_found.subtitle') }}</p>
                <NuxtLink to="/">
                    <UiButton class="mt-6 px-6 py-2.5 font-semibold cursor-pointer">
                        {{ t('button.back') }}
                    </UiButton>
                </NuxtLink>
            </div>

            <!-- Vehicle detail content -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

                <!-- Left: Gallery + Details -->
                <div class="lg:col-span-2 space-y-6 text-left">

                    <!-- Main Image -->
                    <div class="relative rounded-2xl overflow-hidden border border-slate-800 bg-slate-900 aspect-video shadow-2xl">
                        <img
                            :src="activeImage"
                            :alt="`${vehicle.brand} ${vehicle.model}`"
                            @error="handleImageError($event, `${vehicle.brand} ${vehicle.model}`)"
                            class="w-full h-full object-cover transition-all duration-500"
                        />
                        <!-- Image counter badge -->
                        <div class="absolute bottom-3 right-3 bg-slate-950/70 rounded-full px-3 py-1 text-xs text-slate-300 font-semibold">
                            {{ activeImageIndex + 1 }} / {{ vehicle.images.length }}
                        </div>
                    </div>

                    <!-- Thumbnail strip -->
                    <div v-if="vehicle.images.length > 1" class="flex gap-2 overflow-x-auto pb-1">
                        <button
                            v-for="(img, idx) in vehicle.images"
                            :key="idx"
                            @click="activeImageIndex = idx"
                            :class="[
                                'shrink-0 w-20 h-14 rounded-lg overflow-hidden border-2 transition-all duration-200 cursor-pointer',
                                activeImageIndex === idx
                                    ? 'border-store-accent opacity-100 scale-105'
                                    : 'border-slate-800 opacity-60 hover:opacity-90'
                            ]"
                        >
                            <img :src="img" :alt="`Foto ${idx + 1}`" class="w-full h-full object-cover" />
                        </button>
                    </div>

                    <!-- Specs & Description card -->
                    <UiCard class="p-6 md:p-8 border-slate-800 bg-slate-900/40">
                        <!-- Brand + Year -->
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xs font-semibold uppercase tracking-widest text-slate-500">{{ vehicle.brand }}</span>
                            <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                            <span class="text-xs font-semibold text-slate-400">{{ vehicle.year }}</span>
                        </div>

                        <h2 class="text-2xl md:text-3xl font-black text-slate-100 mb-1 leading-tight">
                            {{ vehicle.model }}
                        </h2>
                        <p class="text-xs text-slate-500 mb-6">{{ vehicle.version }}</p>

                        <!-- Specs grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 border-t border-b border-slate-800 py-6">
                            <div>
                                <span class="text-[10px] text-slate-500 block uppercase font-normal">{{ t('specs.mileage.label') }}</span>
                                <span class="text-sm font-semibold text-slate-200 mt-1 block">
                                    {{ vehicle.mileage.toLocaleString('pt-BR') }} {{ t('specs.mileage') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 block uppercase font-normal">{{ t('specs.transmission') }}</span>
                                <span class="text-sm font-semibold text-slate-200 mt-1 block capitalize">{{ vehicle.transmission }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 block uppercase font-normal">{{ t('specs.fuel') }}</span>
                                <span class="text-sm font-semibold text-slate-200 mt-1 block capitalize">{{ vehicle.fuel }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-500 block uppercase font-normal">{{ t('specs.color') }}</span>
                                <span class="text-sm font-semibold text-slate-200 mt-1 block">{{ vehicle.color }}</span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div v-if="vehicle.description" class="mb-6">
                            <h4 class="font-semibold text-slate-300 text-sm mb-2 flex items-center gap-1.5">
                                <iconify-icon icon="tabler:file-description" class="text-base text-slate-400"></iconify-icon>
                                Descrição
                            </h4>
                            <p class="text-sm text-slate-400 leading-relaxed whitespace-pre-line">{{ vehicle.description }}</p>
                        </div>

                        <!-- Positive Flags -->
                        <div v-if="positiveFlags.length > 0" class="mb-5">
                            <h4 class="font-semibold text-slate-300 text-sm mb-3 flex items-center gap-1.5">
                                <iconify-icon icon="tabler:star-filled" class="text-base text-amber-400"></iconify-icon>
                                Diferenciais
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <div
                                    v-for="flag in positiveFlags"
                                    :key="flag"
                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/30 text-xs font-semibold text-emerald-400 rounded-full"
                                >
                                    <iconify-icon :icon="FLAG_LABELS[flag]?.icon || 'tabler:star'" class="text-sm"></iconify-icon>
                                    <span>{{ FLAG_LABELS[flag]?.label || flag }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Regular features -->
                        <div v-if="regularFeatures.length > 0" class="mb-4">
                            <h4 class="font-semibold text-slate-350 text-sm mb-3">{{ t('specs.highlights') }}</h4>
                            <div class="flex flex-wrap gap-2">
                                <UiBadge v-for="feat in regularFeatures" :key="feat" variant="neutral">
                                    {{ feat }}
                                </UiBadge>
                            </div>
                        </div>

                        <!-- Alert flags accordion -->
                        <div v-if="alertFlags.length > 0" class="border border-amber-500/30 rounded-xl overflow-hidden mt-4">
                            <button
                                @click="showAlertAccordion = !showAlertAccordion"
                                class="w-full flex items-center justify-between px-4 py-3 bg-amber-500/10 text-amber-400 text-sm font-semibold cursor-pointer"
                            >
                                <span class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:alert-triangle" class="text-amber-400"></iconify-icon>
                                    Informações Adicionais ({{ alertFlags.length }})
                                </span>
                                <iconify-icon
                                    :icon="showAlertAccordion ? 'tabler:chevron-up' : 'tabler:chevron-down'"
                                    class="text-amber-400"
                                ></iconify-icon>
                            </button>
                            <div v-if="showAlertAccordion" class="px-4 py-3 bg-amber-500/5 space-y-3">
                                <div v-for="flag in alertFlags" :key="flag" class="flex items-start gap-2">
                                    <iconify-icon :icon="FLAG_LABELS[flag]?.icon || 'tabler:info-circle'" class="text-amber-400 text-base mt-0.5 shrink-0"></iconify-icon>
                                    <div>
                                        <p class="text-xs font-bold text-amber-400">{{ FLAG_LABELS[flag]?.label || flag }}</p>
                                        <p v-if="FLAG_LABELS[flag]?.description" class="text-xs text-amber-500/80 mt-0.5">{{ FLAG_LABELS[flag]?.description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </UiCard>
                </div>

                <!-- Right: Price + CTA -->
                <div class="lg:col-span-1 space-y-6 text-left lg:sticky lg:top-24">
                    <UiCard class="p-6 md:p-8 border-slate-800 bg-slate-900/60 shadow-lg">
                        <span class="text-xs font-normal text-slate-500 block">{{ t('detail.special_price') }}</span>
                        <div class="flex items-baseline gap-2 mt-2 mb-6">
                            <span class="text-3xl font-black store-text-color tracking-tight">
                                {{ formatPrice(vehicle.price) }}
                            </span>
                        </div>

                        <!-- CTA Button -->
                        <button
                            @click="showLeadModal = true"
                            class="w-full py-3 rounded-xl font-bold text-sm transition-all duration-200 cursor-pointer mb-3"
                            :class="currentStore.buttonClass"
                        >
                            <iconify-icon icon="tabler:message-circle" class="mr-1.5"></iconify-icon>
                            Tenho Interesse / Falar com Vendedor
                        </button>

                        <!-- WhatsApp quick link -->
                        <a
                            v-if="currentStore.whatsapp"
                            :href="`https://wa.me/${currentStore.whatsapp}?text=${encodeURIComponent(`Olá! Vi o ${vehicle.brand} ${vehicle.model} ${vehicle.year} por ${formatPrice(vehicle.price)} no site da ${currentStore.name} e tenho interesse!`)}`"
                            target="_blank"
                            class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl border border-emerald-600/40 text-emerald-400 text-sm font-semibold hover:bg-emerald-500/10 transition-all duration-200"
                        >
                            <iconify-icon icon="fa7-brands:whatsapp" class="text-lg"></iconify-icon>
                            Chamar no WhatsApp
                        </a>

                        <!-- Quick specs recap -->
                        <div class="mt-6 pt-5 border-t border-slate-800 space-y-3">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Ano</span>
                                <span class="text-slate-200 font-semibold">{{ vehicle.year }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">KM</span>
                                <span class="text-slate-200 font-semibold">{{ vehicle.mileage.toLocaleString('pt-BR') }} km</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Câmbio</span>
                                <span class="text-slate-200 font-semibold capitalize">{{ vehicle.transmission }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Combustível</span>
                                <span class="text-slate-200 font-semibold capitalize">{{ vehicle.fuel }}</span>
                            </div>
                        </div>
                    </UiCard>
                </div>
            </div>
        </div>

        <!-- Lead / Contact Modal -->
        <div
            v-if="showLeadModal && vehicle"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
        >
            <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl relative text-left">
                <!-- Close -->
                <button
                    @click="showLeadModal = false"
                    class="absolute top-4 right-4 z-10 w-9 h-9 flex items-center justify-center rounded-full bg-slate-950/60 hover:bg-slate-950 text-slate-400 hover:text-white transition duration-200 cursor-pointer border border-slate-800"
                >
                    <iconify-icon icon="tabler:x" class="text-xl block"></iconify-icon>
                </button>

                <div class="p-6 md:p-8">
                    <div class="mb-5">
                        <span class="text-xs text-slate-500 uppercase tracking-widest">{{ vehicle.brand }}</span>
                        <h5 class="text-xl font-black text-slate-100 mt-1">{{ vehicle.model }}</h5>
                        <p class="text-xs text-slate-500 mt-1">{{ vehicle.version }} — {{ vehicle.year }}</p>
                        <div class="mt-2">
                            <span class="text-2xl font-extrabold store-text-color">{{ formatPrice(vehicle.price) }}</span>
                        </div>
                    </div>

                    <h6 class="font-semibold text-slate-200 text-sm mb-1">{{ t('detail.title') }}</h6>
                    <p class="text-xs text-slate-500 mb-4">{{ t('detail.proposal.subtitle') }}</p>

                    <LeadForm
                        :vehicle-id="vehicle.id"
                        :initial-message="`Olá! Gostaria de falar com o consultor de vendas da ${currentStore.name} sobre o ${vehicle.brand} ${vehicle.model} anunciado por ${formatPrice(vehicle.price)}.`"
                        :store-whatsapp="currentStore.whatsapp"
                        @success="showLeadModal = false"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.store-text-color {
    color: var(--store-accent-color, #fbbf24);
}
.store-accent {
    border-color: var(--store-accent-color, #fbbf24);
}
</style>
