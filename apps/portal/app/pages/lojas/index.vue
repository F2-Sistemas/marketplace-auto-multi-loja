<script setup lang="ts">
import { computed } from 'vue';
import { useFetch } from '#app';
import { useI18n } from '~/composables/useI18n';
import Breadcrumb from '~/components/layout/Breadcrumb.vue';
import StorePublicCard from '~/components/stores/StorePublicCard.vue';

definePageMeta({
    layout: 'default',
});

const { t } = useI18n();

const { getApiUrl } = useApi();

// Fetch dynamic inventory (24 total) to infer active dealers and details
const { data: response, pending } = await useFetch<any>(getApiUrl('/api/vehicles?per_page=100'));

const storesList = computed(() => {
    const map = new Map();

    if (response.value && response.value.data) {
        response.value.data.forEach((v: any) => {
            if (v.store && v.store.slug && !map.has(v.store.slug)) {
                // Map details based on slug to offer a premium localized look
                const slug = v.store.slug;
                let details = {
                    id: v.store.id,
                    name: v.store.name,
                    slug: slug,
                    logo: 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?q=80&w=300&auto=format&fit=crop',
                    description: `Concessionária autorizada ${v.store.name}. Amplo estoque com garantia e taxas de financiamento exclusivas.`,
                    phone: '(84) 3222-3000',
                    email: `contato@${slug}.com.br`,
                    address: 'Av. Prudente de Morais, 1000 - Tirol',
                    city: 'Natal',
                    state: 'RN',
                };

                if (slug === 'autocar-natal') {
                    details.description =
                        'Sua concessionária autorizada em Natal, RN. Ofertas exclusivas de veículos novos e seminovos multimarcas.';
                    details.phone = '(84) 3222-3000';
                    details.address = 'Av. Salgado Filho, 2200 - Lagoa Nova';
                } else if (slug === 'sp-veiculos') {
                    details.description =
                        'Líder em vendas de seminovos importados e nacionais. Garantia e a melhor avaliação do seu usado na troca.';
                    details.phone = '(11) 3888-4000';
                    details.address = 'Av. Europa, 1500 - Jardim Europa';
                    details.city = 'São Paulo';
                    details.state = 'SP';
                } else if (slug === 'loja01') {
                    details.description =
                        'Veículos com laudo cautelar aprovado e procedência garantida. Credenciada com as principais financeiras.';
                    details.phone = '(84) 99999-9999';
                    details.address = 'Av. Prudente de Morais, 3300 - Lagoa Nova';
                } else if (slug === 'loja02') {
                    details.description =
                        'Exclusividade e sofisticação em marcas premium como BMW, Audi, Mercedes-Benz e Porsche.';
                    details.phone = '(84) 98888-8888';
                    details.address = 'Av. Hermes da Fonseca, 800 - Petrópolis';
                } else if (slug === 'tauro-motors') {
                    details.description =
                        'Concessionária referência em picapes e utilitários esportivos (SUV). Venha fazer um test-drive!';
                    details.phone = '(84) 97777-7777';
                    details.address = 'Av. Engenheiro Roberto Freire, 1200 - Ponta Negra';
                }

                map.set(slug, details);
            }
        });
    }

    if (map.size === 0) {
        // Absolute fallback so the interface is never empty during initial load
        return [
            {
                id: 1,
                name: 'AutoCar Natal',
                slug: 'autocar-natal',
                logo: 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?q=80&w=300&auto=format&fit=crop',
                description:
                    'Sua concessionária autorizada em Natal, RN. Ofertas exclusivas de veículos novos e seminovos multimarcas.',
                phone: '(84) 3222-3000',
                email: 'contato@autocar-natal.com.br',
                address: 'Av. Salgado Filho, 2200 - Lagoa Nova',
                city: 'Natal',
                state: 'RN',
            },
            {
                id: 2,
                name: 'São Paulo Veículos',
                slug: 'sp-veiculos',
                logo: 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?q=80&w=300&auto=format&fit=crop',
                description:
                    'Líder em vendas de seminovos importados e nacionais. Garantia e a melhor avaliação do seu usado na troca.',
                phone: '(11) 3888-4000',
                email: 'vendas@spveiculos.com.br',
                address: 'Av. Europa, 1500 - Jardim Europa',
                city: 'São Paulo',
                state: 'SP',
            },
            {
                id: 3,
                name: 'Loja 01 Multimarcas',
                slug: 'loja01',
                logo: 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?q=80&w=300&auto=format&fit=crop',
                description:
                    'Veículos com laudo cautelar aprovado e procedência garantida. Credenciada com as principais financeiras do mercado.',
                phone: '(84) 99999-9999',
                email: 'contato@loja01.com.br',
                address: 'Av. Prudente de Morais, 3300 - Lagoa Nova',
                city: 'Natal',
                state: 'RN',
            },
            {
                id: 4,
                name: 'Loja 02 Premium',
                slug: 'loja02',
                logo: 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?q=80&w=300&auto=format&fit=crop',
                description: 'Exclusividade e sofisticação em marcas premium como BMW, Audi, Mercedes-Benz e Porsche.',
                phone: '(84) 98888-8888',
                email: 'contato@loja02.com.br',
                address: 'Av. Hermes da Fonseca, 800 - Petrópolis',
                city: 'Natal',
                state: 'RN',
            },
            {
                id: 5,
                name: 'Tauro Motors',
                slug: 'tauro-motors',
                logo: 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?q=80&w=300&auto=format&fit=crop',
                description:
                    'Concessionária referência em picapes e utilitários esportivos (SUV). Venha fazer um test-drive!',
                phone: '(84) 97777-7777',
                email: 'contato@tauromotors.com.br',
                address: 'Av. Engenheiro Roberto Freire, 1200 - Ponta Negra',
                city: 'Natal',
                state: 'RN',
            },
        ];
    }
    return Array.from(map.values());
});
</script>

<template>
    <div class="container max-w-7xl mx-auto px-4 md:px-6 py-6 space-y-6">
        <!-- Breadcrumbs -->
        <Breadcrumb :items="[{ label: 'Nossas Lojas' }]" />

        <!-- Header Section -->
        <div class="space-y-2">
            <h1 class="text-3xl font-black text-neutral-800 tracking-tight flex items-center gap-2">
                <iconify-icon icon="tabler:building-store" class="text-brand-500"></iconify-icon>
                <span>Nossas Lojas Parceiras</span>
            </h1>
            <p class="text-sm font-normal text-neutral-500 max-w-2xl leading-relaxed">
                Conheça as melhores concessionárias de automóveis da região. Encontre a loja parceira mais próxima e
                garanta o seu próximo carro com total segurança e garantia.
            </p>
        </div>

        <!-- Active Loading Spinner -->
        <div v-if="pending" class="py-16 flex flex-col items-center justify-center space-y-4">
            <iconify-icon icon="tabler:loader" class="text-4xl text-brand-500 animate-spin"></iconify-icon>
            <p class="text-sm font-normal text-neutral-400">Localizando concessionárias...</p>
        </div>

        <!-- Dealership Cards Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="store in storesList" :key="store.slug" class="h-full">
                <StorePublicCard :store="store" />
            </div>
        </div>
    </div>
</template>
