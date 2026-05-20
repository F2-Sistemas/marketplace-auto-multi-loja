<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from '~/composables/useI18n';
import UiButton from '~/components/ui/UiButton.vue';
import UiInput from '~/components/ui/UiInput.vue';
import UiSelect from '~/components/ui/UiSelect.vue';
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
        submitError.value = 'Por favor, preencha todos os campos obrigatórios.';
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
        submitError.value = err.data?.message || 'Ocorreu um erro ao processar o seu cadastro. Tente outro subdomínio.';
    } finally {
        isSubmitting.value = false;
    }
};

// FAQ State
const faqs = ref([
    {
        question: 'Como funciona o período de teste gratuito?',
        answer: 'Você pode testar a nossa plataforma por 14 dias sem custos. Cadastre até 5 veículos para experimentar todas as ferramentas e o painel administrativo. Não é necessário cartão de crédito.',
        open: false,
    },
    {
        question: 'Posso usar meu próprio domínio customizado?',
        answer: 'Sim! Nos planos Semestral e Anual, você pode configurar um domínio próprio (ex: www.sualoja.com.br) para apontar diretamente para a sua loja de forma totalmente gratuita.',
        open: false,
    },
    {
        question: 'Como os leads chegam até a minha concessionária?',
        answer: 'Todos os leads e propostas enviados pelos clientes no portal principal ou no seu site exclusivo são recebidos em tempo real no seu painel administrativo do Lojista e também enviados diretamente para o seu WhatsApp cadastrado.',
        open: false,
    },
    {
        question: 'Posso cancelar ou alterar meu plano quando quiser?',
        answer: 'Sim. Nossos planos não possuem fidelidade ou multas de cancelamento. Você pode fazer o upgrade, downgrade ou cancelamento do seu plano a qualquer momento pelo painel.',
        open: false,
    },
]);

const toggleFaq = (index: number) => {
    faqs.value[index].open = !faqs.value[index].open;
};
</script>

<template>
    <div class="min-h-screen bg-neutral-50 pb-16">
        <!-- Banner Hero Section -->
        <div class="relative bg-gradient-to-br from-neutral-900 via-neutral-800 to-brand-950 text-white overflow-hidden py-16 md:py-24">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
            
            <div class="container max-w-7xl mx-auto px-4 md:px-6 relative z-10">
                <Breadcrumb 
                    :items="[{ label: 'Home', to: '/' }, { label: 'Quero Anunciar' }]" 
                    class="mb-8 text-neutral-300 [&_a]:text-neutral-300 [&_span]:text-neutral-400"
                />

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    <div class="lg:col-span-7 space-y-6">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-500/20 text-brand-400 border border-brand-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-400 animate-pulse"></span>
                            Portal da Concessionária Parceira
                        </span>
                        
                        <h1 class="text-4xl md:text-5xl font-black tracking-tight leading-tight">
                            Digitalize seu estoque e venda muito mais na <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-brand-200">RedeRevenda</span>
                        </h1>
                        
                        <p class="text-base md:text-lg text-neutral-300 leading-relaxed max-w-2xl font-light">
                            Crie seu site exclusivo em segundos, integre seu estoque com o portal central e receba propostas qualificadas de compradores da sua região direto no seu WhatsApp.
                        </p>

                        <!-- Key Stats -->
                        <div class="grid grid-cols-3 gap-6 pt-4 border-t border-neutral-700/60 max-w-lg">
                            <div>
                                <div class="text-2xl md:text-3xl font-black text-brand-400">100k+</div>
                                <div class="text-xs text-neutral-400 font-medium">Acessos Mensais</div>
                            </div>
                            <div>
                                <div class="text-2xl md:text-3xl font-black text-brand-400">24h</div>
                                <div class="text-xs text-neutral-400 font-medium">Setup Ativo</div>
                            </div>
                            <div>
                                <div class="text-2xl md:text-3xl font-black text-brand-400">0%</div>
                                <div class="text-xs text-neutral-400 font-medium">Taxa de Venda</div>
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
                                    <h3 class="font-extrabold text-lg text-white">Comece Grátis Hoje</h3>
                                    <p class="text-xs text-neutral-300">14 dias de teste sem compromisso</p>
                                </div>
                            </div>
                            <p class="text-xs text-neutral-300 font-normal leading-relaxed">
                                Preencha o formulário abaixo para reservar o subdomínio da sua loja e iniciar a publicação do seu estoque.
                            </p>
                            <a href="#cadastro" class="block">
                                <UiButton variant="primary" size="lg" class="w-full font-semibold shadow-lg shadow-brand-500/20 flex items-center justify-center gap-2">
                                    <span>Criar Minha Loja Agora</span>
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
                <h2 class="text-2xl md:text-3xl font-black text-neutral-800 tracking-tight">
                    Tudo o que sua loja precisa para vender na internet
                </h2>
                <p class="text-sm font-normal text-neutral-500 max-w-2xl mx-auto">
                    Nossa plataforma oferece uma solução ponta a ponta para que você foque no que faz melhor: fechar negócios.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Benefit 1 -->
                <div class="bg-white border border-neutral-200 rounded-card p-6 shadow-xs space-y-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <iconify-icon icon="tabler:layout-dashboard" class="text-2xl"></iconify-icon>
                    </div>
                    <h3 class="font-bold text-neutral-800 text-base">Site Próprio Exclusivo</h3>
                    <p class="text-xs font-normal text-neutral-500 leading-relaxed">
                        Tenha um site moderno com a identidade e cores da sua marca para divulgar seu estoque de forma profissional.
                    </p>
                </div>

                <!-- Benefit 2 -->
                <div class="bg-white border border-neutral-200 rounded-card p-6 shadow-xs space-y-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                        <iconify-icon icon="tabler:brand-whatsapp" class="text-2xl"></iconify-icon>
                    </div>
                    <h3 class="font-bold text-neutral-800 text-base">Leads no WhatsApp</h3>
                    <p class="text-xs font-normal text-neutral-500 leading-relaxed">
                        Os interessados entram em contato diretamente no seu celular, acelerando as negociações sem intermediários.
                    </p>
                </div>

                <!-- Benefit 3 -->
                <div class="bg-white border border-neutral-200 rounded-card p-6 shadow-xs space-y-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                        <iconify-icon icon="tabler:chart-bar" class="text-2xl"></iconify-icon>
                    </div>
                    <h3 class="font-bold text-neutral-800 text-base">Portal de Destaques</h3>
                    <p class="text-xs font-normal text-neutral-500 leading-relaxed">
                        Além do seu site, seus veículos são exibidos no portal RedeRevenda, ampliando a visibilidade para todo o público.
                    </p>
                </div>

                <!-- Benefit 4 -->
                <div class="bg-white border border-neutral-200 rounded-card p-6 shadow-xs space-y-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <iconify-icon icon="tabler:adjustments" class="text-2xl"></iconify-icon>
                    </div>
                    <h3 class="font-bold text-neutral-800 text-base">Painel Simplificado</h3>
                    <p class="text-xs font-normal text-neutral-500 leading-relaxed">
                        Controle o estoque, visualize estatísticas de acessos e configure os dados de contato em um painel responsivo.
                    </p>
                </div>
            </div>
        </div>

        <!-- Plans & Pricing Section -->
        <div class="bg-neutral-100 border-y border-neutral-200 py-16">
            <div class="container max-w-7xl mx-auto px-4 md:px-6 space-y-12">
                <div class="text-center space-y-3">
                    <h2 class="text-2xl md:text-3xl font-black text-neutral-800 tracking-tight">
                        Planos flexíveis para qualquer tamanho de loja
                    </h2>
                    <p class="text-sm font-normal text-neutral-500 max-w-xl mx-auto">
                        Sem taxas ocultas, sem comissões sobre vendas. Escolha o plano ideal para alavancar seu estoque.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <!-- Plan 1 -->
                    <div class="bg-white border border-neutral-200 rounded-2xl p-6 md:p-8 space-y-6 flex flex-col justify-between relative shadow-sm">
                        <div class="space-y-4">
                            <h3 class="font-bold text-lg text-neutral-800">Plano Mensal</h3>
                            <p class="text-xs text-neutral-400">Perfeito para quem quer começar a experimentar.</p>
                            <div class="pt-2">
                                <span class="text-3xl font-black text-neutral-800">R$ 99</span>
                                <span class="text-xs text-neutral-400">/mês</span>
                            </div>
                            <ul class="space-y-3 pt-4 border-t border-neutral-100 text-xs text-neutral-600">
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>Até 15 veículos ativos</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>Integração WhatsApp</span>
                                </li>
                                <li class="flex items-center gap-2 text-neutral-400">
                                    <iconify-icon icon="tabler:circle-x" class="text-neutral-300 text-base"></iconify-icon>
                                    <span>Sem Domínio Próprio</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Plan 2 (Highlighted) -->
                    <div class="bg-white border-2 border-brand-500 rounded-2xl p-6 md:p-8 space-y-6 flex flex-col justify-between relative shadow-md scale-105 z-10">
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-brand-500 text-white text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow-sm">
                            Mais Popular
                        </div>
                        <div class="space-y-4">
                            <h3 class="font-bold text-lg text-neutral-800">Plano Semestral</h3>
                            <p class="text-xs text-neutral-400">Ideal para negócios consolidados em expansão.</p>
                            <div class="pt-2">
                                <span class="text-3xl font-black text-neutral-800">R$ 79</span>
                                <span class="text-xs text-neutral-400">/mês (Total R$ 474)</span>
                            </div>
                            <ul class="space-y-3 pt-4 border-t border-neutral-100 text-xs text-neutral-600">
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>Até 50 veículos ativos</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>Integração WhatsApp</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>Domínio Customizado (.com.br)</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Plan 3 -->
                    <div class="bg-white border border-neutral-200 rounded-2xl p-6 md:p-8 space-y-6 flex flex-col justify-between relative shadow-sm">
                        <div class="space-y-4">
                            <h3 class="font-bold text-lg text-neutral-800">Plano Anual</h3>
                            <p class="text-xs text-neutral-400">A melhor economia e recursos premium ilimitados.</p>
                            <div class="pt-2">
                                <span class="text-3xl font-black text-neutral-800">R$ 59</span>
                                <span class="text-xs text-neutral-400">/mês (Total R$ 708)</span>
                            </div>
                            <ul class="space-y-3 pt-4 border-t border-neutral-100 text-xs text-neutral-600">
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>Estoque Ilimitado</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>Integração WhatsApp</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-green-500 text-base"></iconify-icon>
                                    <span>Domínio Customizado (.com.br)</span>
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
                            <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">Nome da Concessionária *</label>
                            <UiInput 
                                v-model="name"
                                placeholder="Ex: Euro Select Motors" 
                                required
                                @input="onNameInput"
                            />
                        </div>

                        <!-- Subdomain (Normalized Preview) -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">Subdomínio Desejado *</label>
                            <div class="relative">
                                <UiInput 
                                    v-model="subdomain"
                                    placeholder="Ex: euro-select" 
                                    required
                                    @input="onSubdomainInput"
                                />
                            </div>
                            <p class="text-xs text-neutral-400 font-normal">
                                Seu site será acessível em: <span class="font-semibold text-brand-600">http://{{ generatedSubdomain || '...' }}.rederevenda.com</span>
                            </p>
                        </div>

                        <!-- Whatsapp Contact -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">WhatsApp de Contato (Formatado com DDD) *</label>
                            <UiInput 
                                v-model="whatsapp"
                                placeholder="Ex: 5511988888888" 
                                required
                            />
                            <p class="text-xs text-neutral-400 font-normal">Insira apenas números incluindo o código do país (55) e o DDD.</p>
                        </div>

                        <!-- Color selection & Plan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">Cor de Destaque da Loja</label>
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
                                <label class="text-xs font-bold text-neutral-700 uppercase tracking-wider">Escolha seu Plano</label>
                                <UiSelect 
                                    v-model="selectedPlan"
                                    :options="[
                                        { label: 'Mensal - R$ 99/mês', value: 'mensal' },
                                        { label: 'Semestral - R$ 79/mês', value: 'semestral' },
                                        { label: 'Anual - R$ 59/mês', value: 'anual' }
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
                                <span>{{ isSubmitting ? 'Processando Cadastro...' : 'Enviar Cadastro e Provisionar Loja' }}</span>
                            </UiButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="container max-w-3xl mx-auto px-4 md:px-6 py-8 space-y-6">
            <div class="text-center space-y-2 mb-8">
                <h2 class="text-xl md:text-2xl font-black text-neutral-800 tracking-tight">Perguntas Frequentes</h2>
                <p class="text-xs text-neutral-400">Esclareça suas dúvidas principais sobre a parceria.</p>
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
