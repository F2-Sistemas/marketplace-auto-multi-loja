<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from '~/composables/useI18n';
import UiButton from '~/components/ui/UiButton.vue';
import UiInput from '~/components/ui/UiInput.vue';
import UiSelect from '~/components/ui/UiSelect.vue';
import UiImageCarousel from '~/components/ui/UiImageCarousel.vue'; // Importar o novo componente
import Breadcrumb from '~/components/layout/Breadcrumb.vue';

definePageMeta({
    layout: 'default',
});

const { t } = useI18n();
const { getApiUrl } = useApi();

// Form State
const name = ref('');
const subdomain = ref('');
const whatsapp = ref('');
const accentColor = ref('#4f46e5');
const selectedPlan = ref('mensal');
const isSubmitting = ref(false);
const submitError = ref('');
const submitSuccess = ref(false);
const createdStoreInfo = ref<{ name: string; domain: string } | null>(null);

// Subdomain auto-generation
const generatedSubdomain = computed(() => {
    return subdomain.value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9-]/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
});

// Sync input subdomain with normalized slug
const onSubdomainInput = (e: Event) => {
    const input = e.target as HTMLInputElement;
    subdomain.value = input.value;
};

// Auto fill subdomain based on store name if subdomain is untouched
const onNameInput = () => {
    if (!subdomain.value) {
        subdomain.value = name.value;
    }
};

// Form submission to API
const registerStore = async () => {
    if (!name.value || !subdomain.value || !whatsapp.value) {
        submitError.value = t('portal.queroAnunciar.form.errors.requiredFields');
        return;
    }

    submitError.value = '';
    isSubmitting.value = true;

    try {
        const payload = {
            name: name.value,
            subdomain: generatedSubdomain.value,
            whatsapp_number: whatsapp.value,
            accent_color: accentColor.value,
            plan: selectedPlan.value,
        };

        const res: any = await $fetch(getApiUrl('/api/admin/stores'), {
            method: 'POST',
            body: payload,
        });

        createdStoreInfo.value = {
            name: name.value,
            domain: res.domain || `${generatedSubdomain.value}.rederevenda.com`,
        };
        submitSuccess.value = true;
    } catch (err: any) {
        console.error(err);
        submitError.value = err.data?.message || t('portal.queroAnunciar.form.errors.apiError');
    } finally {
        isSubmitting.value = false;
    }
};

// FAQ State
const faqs = ref([
    { // Usando i18n para as FAQs
        question: t('portal.queroAnunciar.faq.testPeriodQuestion'),
        answer: t('portal.queroAnunciar.faq.testPeriodAnswer'),
        open: false,
    },
    {
        question: t('portal.queroAnunciar.faq.customDomainQuestion'),
        answer: t('portal.queroAnunciar.faq.customDomainAnswer'),
        open: false,
    },
    {
        question: t('portal.queroAnunciar.faq.leadsArrivalQuestion'),
        answer: t('portal.queroAnunciar.faq.leadsArrivalAnswer'),
        open: false,
    },
    {
        question: t('portal.queroAnunciar.faq.cancelPlanQuestion'),
        answer: t('portal.queroAnunciar.faq.cancelPlanAnswer'),
        open: false,
    },
]);

const toggleFaq = (index: number) => {
    faqs.value[index].open = !faqs.value[index].open;
};
</script>
<template>
    <div class="min-h-screen bg-neutral-50 pb-16">
        <!-- DEMONSTRAÇÃO: Carrossel de Imagens (apenas para ilustrar o uso do componente) -->
        <div class="h-96 w-full mb-16">
            <UiImageCarousel
                :images="[
                    { id: 1, url: 'https://via.placeholder.com/1920x1080/4f46e5/ffffff?text=Carro+1', alt: 'Carro Esportivo' },
                    { id: 2, url: 'https://via.placeholder.com/1920x1080/10b981/ffffff?text=Carro+2', alt: 'SUV Familiar' },
                    { id: 3, url: 'https://via.placeholder.com/1920x1080/ef4444/ffffff?text=Carro+3', alt: 'Carro Compacto' },
                ]"
            />
        </div>
        <!-- FIM DA DEMONSTRAÇÃO -->

        <!-- Banner Hero Section -->
        <div class="relative bg-gradient-to-br from-neutral-900 via-neutral-800 to-brand-950 text-white overflow-hidden py-16 md:py-24">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>

            <div class="container max-w-7xl mx-auto px-4 md:px-6 relative z-10">
                <Breadcrumb
                    :items="[
                        { label: t('portal.queroAnunciar.breadcrumb.home'), to: '/' },
                        { label: t('portal.queroAnunciar.breadcrumb.queroAnunciar') }
                    ]"
                    class="mb-8 text-neutral-300 [&_a]:text-neutral-300 [&_span]:text-neutral-400"
                />

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    <div class="lg:col-span-7 space-y-6">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-500/20 text-brand-400 border border-brand-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-400 animate-pulse"></span> {{ t('portal.queroAnunciar.banner.partnerPortal') }}
                        </span>

                        <h1 class="text-4xl md:text-5xl font-black tracking-tight leading-tight">
                            <span v-html="t('portal.queroAnunciar.banner.title')"></span>
                        </h1>

                        <p class="text-base md:text-lg text-neutral-300 leading-relaxed max-w-2xl font-light">
                            {{ t('portal.queroAnunciar.banner.subtitle') }}
                        </p>

                        <!-- Key Stats -->
                        <div class="grid grid-cols-3 gap-6 pt-4 border-t border-neutral-700/60 max-w-lg">
                            <div>
                                <div class="text-2xl md:text-3xl font-black text-brand-400">100k+</div>
                                <div class="text-xs text-neutral-400 font-medium">{{ t('portal.queroAnunciar.banner.stats.accesses') }}</div>
                            </div>
                            <div>
                                <div class="text-2xl md:text-3xl font-black text-brand-400">24h</div>
                                <div class="text-xs text-neutral-400 font-medium">{{ t('portal.queroAnunciar.banner.stats.setup') }}</div>
                            </div>
                            <div>
                                <div class="text-2xl md:text-3xl font-black text-brand-400">0%</div>
                                <div class="text-xs text-neutral-400 font-medium">{{ t('portal.queroAnunciar.banner.stats.salesFee') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Side Promo card / CTA link -->
                    <div class="lg:col-span-5">
                        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 md:p-8 space-y-4 shadow-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-brand-500 flex items-center justify-center text-white">
                                    <iconify-icon icon="tabler:rocket" class="text-2xl"></iconify-icon>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-lg text-white">{{ t('portal.queroAnunciar.banner.promoCard.title') }}</h3>
                                    <p class="text-xs text-neutral-300">{{ t('portal.queroAnunciar.banner.promoCard.subtitle') }}</p>
                                </div>
                            </div>
                            <p class="text-xs text-neutral-300 font-normal leading-relaxed">
                                {{ t('portal.queroAnunciar.banner.promoCard.description') }}
                            </p>
                            <a href="#cadastro" class="block">
                                <UiButton variant="primary" size="lg" class="w-full font-semibold shadow-lg shadow-brand-500/20 flex items-center justify-center gap-2">
                                    <span>{{ t('portal.queroAnunciar.banner.promoCard.cta') }}</span>
                                    <iconify-icon icon="tabler:arrow-down" class="text-lg"></iconify-icon>
                                </UiButton>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Benefits Section -->
        <div class="container max-w-7xl mx-auto px-4 md:px-6 py-16 space-y-12">
            <div class="text-center space-y-3">
                <h2 class="text-2xl md:text-3xl font-black text-neutral-800 tracking-tight">{{ t('portal.queroAnunciar.benefits.title') }}</h2>
                <p class="text-sm font-normal text-neutral-500 max-w-2xl mx-auto">
                    {{ t('portal.queroAnunciar.benefits.subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Benefit 1 -->
                <div class="bg-white border border-neutral-200 rounded-card p-6 shadow-xs space-y-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <iconify-icon icon="tabler:layout-dashboard" class="text-2xl"></iconify-icon>
                    </div>
                    <h3 class="font-bold text-neutral-800 text-base">{{ t('portal.queroAnunciar.benefits.siteTitle') }}</h3>
                    <p class="text-xs font-normal text-neutral-500 leading-relaxed">
                        {{ t('portal.queroAnunciar.benefits.siteDescription') }}
                    </p>
                </div>

                <!-- Benefit 2 -->
                <div class="bg-white border border-neutral-200 rounded-card p-6 shadow-xs space-y-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                        <iconify-icon icon="tabler:brand-whatsapp" class="text-2xl"></iconify-icon>
                    </div>
                    <h3 class="font-bold text-neutral-800 text-base">{{ t('portal.queroAnunciar.benefits.leadsTitle') }}</h3>
                    <p class="text-xs font-normal text-neutral-500 leading-relaxed">
                        {{ t('portal.queroAnunciar.benefits.leadsDescription') }}
                    </p>
                </div>

                <!-- Benefit 3 -->
                <div class="bg-white border border-neutral-200 rounded-card p-6 shadow-xs space-y-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                        <iconify-icon icon="tabler:chart-bar" class="text-2xl"></iconify-icon>
                    </div>
                    <h3 class="font-bold text-neutral-800 text-base">{{ t('portal.queroAnunciar.benefits.portalTitle') }}</h3>
                    <p class="text-xs font-normal text-neutral-500 leading-relaxed">
                        {{ t('portal.queroAnunciar.benefits.portalDescription') }}
                    </p>
                </div>

                <!-- Benefit 4 -->
                <div class="bg-white border border-neutral-200 rounded-card p-6 shadow-xs space-y-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <iconify-icon icon="tabler:adjustments" class="text-2xl"></iconify-icon>
                    </div>
                    <h3 class="font-bold text-neutral-800 text-base">{{ t('portal.queroAnunciar.benefits.panelTitle') }}</h3>
                    <p class="text-xs font-normal text-neutral-500 leading-relaxed">
                        {{ t('portal.queroAnunciar.benefits.panelDescription') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Plans & Pricing Section -->
        <div class="bg-neutral-100 border-y border-neutral-200 py-16">
            <div class="container max-w-7xl mx-auto px-4 md:px-6 space-y-12">
                <div class="text-center space-y-3">
                    <h2 class="text-2xl md:text-3xl font-black text-neutral-800 tracking-tight">{{ t('portal.queroAnunciar.plans.title') }}</h2>
                    <p class="text-sm font-normal text-neutral-500 max-w-xl mx-auto">
                        {{ t('portal.queroAnunciar.plans.subtitle') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <!-- Plan 1 -->
                    <div class="bg-white border border-neutral-200 rounded-2xl p-6 md:p-8 space-y-6 flex flex-col justify-between relative shadow-sm">
                        <div class="space-y-4">
                            <h3 class="font-bold text-lg text-neutral-800">{{ t('portal.queroAnunciar.plans.monthly.title') }}</h3>
                            <p class="text-xs text-neutral-400">{{ t('portal.queroAnunciar.plans.monthly.description') }}</p>
                            <div class="pt-2">
                                <span class="text-3xl font-black text-neutral-800">{{ t('portal.queroAnunciar.plans.monthly.price') }}</span>
                                <span class="text-xs text-neutral-400">{{ t('portal.queroAnunciar.plans.monthly.period') }}</span>
                            </div>
                            <ul class="space-y-3 pt-4 border-t border-neutral-100 text-xs text-neutral-600">
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>{{ t('portal.queroAnunciar.plans.monthly.features.vehicles') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>{{ t('portal.queroAnunciar.plans.monthly.features.whatsapp') }}</span>
                                </li>
                                <li class="flex items-center gap-2 text-neutral-400">
                                    <iconify-icon icon="tabler:circle-x" class="text-neutral-300 text-base"></iconify-icon>
                                    <span>{{ t('portal.queroAnunciar.plans.monthly.features.noCustomDomain') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Plan 2 (Highlighted) -->
                    <div class="bg-white border-2 border-brand-500 rounded-2xl p-6 md:p-8 space-y-6 flex flex-col justify-between relative shadow-md scale-105 z-10">
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-brand-500 text-white text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow-sm">
                            {{ t('portal.queroAnunciar.plans.popular') }}
                        </div>
                        <div class="space-y-4">
                            <h3 class="font-bold text-lg text-neutral-800">{{ t('portal.queroAnunciar.plans.semiannual.title') }}</h3>
                            <p class="text-xs text-neutral-400">{{ t('portal.queroAnunciar.plans.semiannual.description') }}</p>
                            <div class="pt-2">
                                <span class="text-3xl font-black text-neutral-800">{{ t('portal.queroAnunciar.plans.semiannual.price') }}</span>
                                <span class="text-xs text-neutral-400">{{ t('portal.queroAnunciar.plans.semiannual.period') }}</span>
                            </div>
                            <ul class="space-y-3 pt-4 border-t border-neutral-100 text-xs text-neutral-600">
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>{{ t('portal.queroAnunciar.plans.semiannual.features.vehicles') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>{{ t('portal.queroAnunciar.plans.semiannual.features.whatsapp') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>{{ t('portal.queroAnunciar.plans.semiannual.features.customDomain') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Plan 3 -->
                    <div class="bg-white border border-neutral-200 rounded-2xl p-6 md:p-8 space-y-6 flex flex-col justify-between relative shadow-sm">
                        <div class="space-y-4">
                            <h3 class="font-bold text-lg text-neutral-800">{{ t('portal.queroAnunciar.plans.annual.title') }}</h3>
                            <p class="text-xs text-neutral-400">{{ t('portal.queroAnunciar.plans.annual.description') }}</p>
                            <div class="pt-2">
                                <span class="text-3xl font-black text-neutral-800">{{ t('portal.queroAnunciar.plans.annual.price') }}</span>
                                <span class="text-xs text-neutral-400">{{ t('portal.queroAnunciar.plans.annual.period') }}</span>
                            </div>
                            <ul class="space-y-3 pt-4 border-t border-neutral-100 text-xs text-neutral-600">
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>{{ t('portal.queroAnunciar.plans.annual.features.vehicles') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>{{ t('portal.queroAnunciar.plans.annual.features.whatsapp') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>{{ t('portal.queroAnunciar.plans.annual.features.customDomain') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registration Form Section -->
        <div id="cadastro" class="container max-w-3xl mx-auto px-4 md:px-6 py-16 scroll-mt-20">
            <div class="bg-white border border-neutral-200 rounded-2xl shadow-card overflow-hidden">
                <div class="bg-gradient-to-r from-brand-600 to-brand-700 px-6 py-8 text-white text-center">
                    <h2 class="text-2xl font-black tracking-tight">Formulário de Cadastro da Loja</h2>
                    <p class="text-xs text-brand-100 mt-1">
                        Cadastre sua loja e configure seu subdomínio exclusivo imediatamente.
                    </p>
                </div>

                <div class="p-6 md:p-8">
                    <!-- Success View -->
                    <div v-if="submitSuccess" class="text-center py-8 space-y-6">
                        <div class="w-16 h-16 rounded-full bg-green-50 text-green-600 flex items-center justify-center mx-auto animate-bounce">
                            <iconify-icon icon="tabler:checkbox" class="text-4xl"></iconify-icon>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-extrabold text-neutral-800">Parabéns! Sua loja foi criada com sucesso.</h3>
                            <p class="text-sm text-neutral-500">
                                Sua concessionária <strong>{{ createdStoreInfo?.name }}</strong> já está ativa no sistema.
                            </p>
                        </div>

                        <div class="bg-neutral-50 border border-neutral-200 rounded-xl p-4 max-w-md mx-auto text-left space-y-2">
                            <div class="text-xs text-neutral-400 uppercase tracking-wider font-bold">Seu endereço exclusivo:</div>
                            <a :href="'http://' + createdStoreInfo?.domain" target="_blank" class="block text-brand-600 font-extrabold text-sm hover:underline flex items-center gap-1.5">
                                <span>http://{{ createdStoreInfo?.domain }}</span>
                                <iconify-icon icon="tabler:external-link" class="text-base"></iconify-icon>
                            </a>
                            <div class="text-xs text-neutral-500 font-normal leading-relaxed pt-2 border-t border-neutral-200/60">
                                Para gerenciar seu estoque, acesse o painel administrativo através da plataforma com os seus dados de lojista.
                            </div>
                        </div>

                        <div class="pt-4">
                            <UiButton variant="primary" size="md" @click="submitSuccess = false; name=''; subdomain=''; whatsapp='';" class="font-semibold">
                                Cadastrar Outra Loja
                            </UiButton>
                        </div>
                    </div>

                    <!-- Active Form View -->
                    <form v-else @submit.prevent="registerStore" class="space-y-6">
                        <!-- Store Name -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">{{ t('portal.queroAnunciar.form.fields.name.label') }}</label>
                            <UiInput
                                v-model="name"
                                :placeholder="t('portal.queroAnunciar.form.fields.name.placeholder')"
                                required
                                @input="onNameInput"
                            />
                        </div>

                        <!-- Subdomain (Normalized Preview) -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">{{ t('portal.queroAnunciar.form.fields.subdomain.label') }}</label>
                            <div class="relative">
                                <UiInput
                                    v-model="subdomain"
                                    :placeholder="t('portal.queroAnunciar.form.fields.subdomain.placeholder')"
                                    required
                                    @input="onSubdomainInput"
                                />
                            </div>
                            <p class="text-xs text-neutral-400 font-normal">
                                {{ t('portal.queroAnunciar.form.fields.subdomain.previewPrefix') }} <span class="font-semibold text-brand-600">http://{{ generatedSubdomain || '...' }}{{ t('portal.queroAnunciar.form.fields.subdomain.previewSuffix') }}</span>
                            </p>
                        </div>

                        <!-- Whatsapp Contact -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">{{ t('portal.queroAnunciar.form.fields.whatsapp.label') }}</label>
                            <UiInput
                                v-model="whatsapp"
                                :placeholder="t('portal.queroAnunciar.form.fields.whatsapp.placeholder')"
                                required
                            />
                            <p class="text-xs text-neutral-400 font-normal">{{ t('portal.queroAnunciar.form.fields.whatsapp.hint') }}</p>
                        </div>

                        <!-- Color selection & Plan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">{{ t('portal.queroAnunciar.form.fields.accentColor.label') }}</label>
                                <div class="flex items-center gap-3">
                                    <input
                                        type="color"
                                        v-model="accentColor"
                                        class="w-10 h-10 border border-neutral-300 rounded cursor-pointer p-0.5 bg-white"
                                    />
                                    <span class="text-xs text-neutral-500 font-mono">{{ accentColor.toUpperCase() }}</span>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">{{ t('portal.queroAnunciar.form.fields.plan.label') }}</label>
                                <UiSelect
                                    v-model="selectedPlan"
                                    :options="[
                                        { label: t('portal.queroAnunciar.form.fields.plan.monthly'), value: 'mensal' },
                                        { label: t('portal.queroAnunciar.form.fields.plan.semiannual'), value: 'semestral' },
                                        { label: t('portal.queroAnunciar.form.fields.plan.annual'), value: 'anual' }
                                    ]"
                                />
                            </div>
                        </div>

                        <!-- Submit Buttons and Error Message -->
                        <div v-if="submitError" class="p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-xs font-semibold">
                            {{ submitError }}
                        </div>

                        <div class="pt-4 border-t border-neutral-100">
                            <UiButton
                                type="submit"
                                variant="primary"
                                size="lg"
                                :disabled="isSubmitting"
                                class="w-full font-bold shadow-lg shadow-brand-500/20 flex items-center justify-center gap-2"
                            >
                                <iconify-icon v-if="isSubmitting" icon="tabler:loader" class="animate-spin"></iconify-icon>
                                <span>{{ isSubmitting ? t('portal.queroAnunciar.form.submit.processing') : t('portal.queroAnunciar.form.submit.cta') }}</span>
                            </UiButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="container max-w-3xl mx-auto px-4 md:px-6 py-8 space-y-6">
            <div class="text-center space-y-2 mb-8">
                <h2 class="text-xl md:text-2xl font-black text-neutral-800 tracking-tight">{{ t('portal.queroAnunciar.faq.title') }}</h2>
                <p class="text-xs text-neutral-400">{{ t('portal.queroAnunciar.faq.subtitle') }}</p>
            </div>

            <div class="space-y-4">
                <div
                    v-for="(faq, idx) in faqs"
                    :key="idx"
                    class="bg-white border border-neutral-200 rounded-xl overflow-hidden shadow-xs"
                >
                    <button
                        @click="toggleFaq(idx)"
                        class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-neutral-50 transition"
                    >
                        <span class="font-bold text-sm text-neutral-800">{{ faq.question }}</span>
                        <iconify-icon
                            icon="tabler:chevron-down"
                            :class="['text-neutral-400 text-lg transition-transform duration-200', faq.open ? 'rotate-180' : '']"
                        ></iconify-icon>
                    </button>

                    <div
                        v-show="faq.open"
                        class="px-6 pb-4 pt-1 border-t border-neutral-100 text-xs text-neutral-500 leading-relaxed font-normal"
                    >
                        {{ faq.answer }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
