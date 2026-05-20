<script setup lang="ts">
import { computed, ref, watch, onMounted } from 'vue';
import { useRoute, useFetch } from '#app';
import { useI18n } from '~/composables/useI18n';
import Breadcrumb from '~/components/layout/Breadcrumb.vue';
import StoreHero from '~/components/stores/StoreHero.vue';
import StoreInfo from '~/components/stores/StoreInfo.vue';
import VehicleCard from '~/components/vehicles/VehicleCard.vue';
import VehicleGrid from '~/components/vehicles/VehicleGrid.vue';
import UiButton from '~/components/ui/UiButton.vue';
import UiPagination from '~/components/ui/UiPagination.vue';

definePageMeta({
    layout: 'default',
});

const route = useRoute();
const { t } = useI18n();

const { getApiUrl } = useApi();

// Fetch inventory to compute this store and filter its vehicles
const { data: response, pending, error } = await useFetch<any>(getApiUrl('/api/vehicles?per_page=100'));

const store = computed(() => {
    if (!response.value || !response.value.data) return null;

    // Find first vehicle matching store slug to extract details
    const match = response.value.data.find((v: any) => v.store?.slug === route.params.slug);
    if (!match || !match.store) return null;

    const slug = match.store.slug;
    let details = {
        id: match.store.id,
        name: match.store.name,
        slug: slug,
        logo: 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?q=80&w=300&auto=format&fit=crop',
        description: `Concessionária autorizada ${match.store.name}. Amplo estoque com garantia e taxas de financiamento exclusivas.`,
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
        details.description = 'Exclusividade e sofisticação em marcas premium como BMW, Audi, Mercedes-Benz e Porsche.';
        details.phone = '(84) 98888-8888';
        details.address = 'Av. Hermes da Fonseca, 800 - Petrópolis';
    } else if (slug === 'tauro-motors') {
        details.description =
            'Concessionária referência em picapes e utilitários esportivos (SUV). Venha fazer um test-drive!';
        details.phone = '(84) 97777-7777';
        details.address = 'Av. Engenheiro Roberto Freire, 1200 - Ponta Negra';
    }

    return details;
});

// Map matching vehicles into clean frontend structures matching the aligned Vehicle interface
const storeVehicles = computed(() => {
    if (!response.value || !response.value.data || !store.value) return [];
    return response.value.data
        .filter((v: any) => v.store?.slug === route.params.slug)
        .map((v: any) => ({
            id: v.id,
            title: v.title || `${v.brand?.name || ''} ${v.model?.name || ''}`,
            brand: v.brand ? v.brand.name : '',
            model: v.model ? v.model.name : '',
            version: v.version || '',
            year_manufacture: Number(v.year_manufacture) || 2020,
            year_model: Number(v.year_model) || 2021,
            mileage: Number(v.mileage) || 0,
            transmission: v.transmission || '',
            price: Number(v.price) || 0,
            slug: v.slug || '',
            images:
                v.images && v.images.length > 0
                    ? v.images.map((img: any) => img.image_url || img.path)
                    : ['https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?q=80&w=800&auto=format&fit=crop'],
            store: v.store
                ? {
                      name: v.store.name,
                      slug: v.store.slug,
                      logo: v.store.logo_url,
                  }
                : undefined,
            fuel: v.fuel || '',
            color: v.color || '',
            featured: Number(v.price) >= 120000,
        }));
});

// Filter & Sort State
const searchQuery = ref('');
const selectedTransmission = ref('');
const selectedFuel = ref('');
const sortBy = ref('price_asc');
const currentPage = ref(1);
const layoutView = ref<'grid' | 'list'>('grid');
const itemsPerPage = 6;

// Reset pagination when filter changes
watch([searchQuery, selectedTransmission, selectedFuel, sortBy], () => {
    currentPage.value = 1;
});

// Compute unique transmission and fuel types for filtering from current dealership inventory
const transmissionOptions = computed(() => {
    const options = new Set<string>();
    storeVehicles.value.forEach((v: any) => {
        if (v.transmission && v.transmission.trim()) {
            options.add(v.transmission.trim());
        }
    });
    return Array.from(options);
});

const fuelOptions = computed(() => {
    const options = new Set<string>();
    storeVehicles.value.forEach((v: any) => {
        if (v.fuel && v.fuel.trim()) {
            options.add(v.fuel.trim());
        }
    });
    return Array.from(options);
});

// Filter & Sort inventory logic
const filteredAndSortedVehicles = computed(() => {
    let result = [...storeVehicles.value];

    // Filter by text search
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (v: any) =>
                v.title.toLowerCase().includes(query) ||
                v.brand.toLowerCase().includes(query) ||
                v.model.toLowerCase().includes(query) ||
                v.version.toLowerCase().includes(query)
        );
    }

    // Filter by transmission
    if (selectedTransmission.value) {
        result = result.filter((v: any) => v.transmission.toLowerCase() === selectedTransmission.value.toLowerCase());
    }

    // Filter by fuel
    if (selectedFuel.value) {
        result = result.filter((v: any) => v.fuel.toLowerCase() === selectedFuel.value.toLowerCase());
    }

    // Sort
    if (sortBy.value === 'price_asc') {
        result.sort((a: any, b: any) => a.price - b.price);
    } else if (sortBy.value === 'price_desc') {
        result.sort((a: any, b: any) => b.price - a.price);
    } else if (sortBy.value === 'year_desc') {
        result.sort((a: any, b: any) => b.year_model - a.year_model);
    } else if (sortBy.value === 'mileage_asc') {
        result.sort((a: any, b: any) => a.mileage - b.mileage);
    }

    return result;
});

// Compute total pages
const totalPages = computed(() => {
    return Math.ceil(filteredAndSortedVehicles.value.length / itemsPerPage);
});

// Compute paginated subset
const paginatedVehicles = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    return filteredAndSortedVehicles.value.slice(start, start + itemsPerPage);
});

const clearFilters = () => {
    searchQuery.value = '';
    selectedTransmission.value = '';
    selectedFuel.value = '';
    sortBy.value = 'price_asc';
    currentPage.value = 1;
};

onMounted(() => {
    const savedLayout = localStorage.getItem('auto-hub:dealership-layout');
    if (savedLayout === 'grid' || savedLayout === 'list') {
        layoutView.value = savedLayout;
    }
});

watch(layoutView, (newVal) => {
    localStorage.setItem('auto-hub:dealership-layout', newVal);
});
</script>

<template>
    <div class="container max-w-7xl mx-auto px-4 md:px-6 py-6 space-y-6">
        <!-- Pending indicator -->
        <div v-if="pending" class="py-20 flex flex-col items-center justify-center space-y-4">
            <iconify-icon icon="tabler:loader" class="text-4xl text-brand-500 animate-spin"></iconify-icon>
            <p class="text-sm font-normal text-neutral-500">Buscando informações da loja...</p>
        </div>

        <!-- Error/NotFound -->
        <div v-else-if="error || !store" class="py-16 text-center max-w-md mx-auto space-y-4">
            <div
                class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto border border-red-100"
            >
                <iconify-icon icon="tabler:alert-triangle" class="text-3xl"></iconify-icon>
            </div>
            <h2 class="text-xl font-extrabold text-neutral-800">Concessionária não encontrada</h2>
            <p class="text-sm text-neutral-500 font-medium">
                Não conseguimos localizar a loja solicitada no momento. Por favor, volte ao diretório de lojas
                parceiras.
            </p>
            <UiButton variant="primary" size="md" to="/lojas" class="font-semibold">
                <span>Ver Todas as Lojas</span>
            </UiButton>
        </div>

        <!-- Live Store details and inventory catalog -->
        <div v-else class="space-y-6">
            <!-- Breadcrumbs -->
            <Breadcrumb :items="[{ label: 'Nossas Lojas', to: '/lojas' }, { label: store.name }]" />

            <!-- Hero Banner Component -->
            <StoreHero :store="store" />

            <!-- Layout Split Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <!-- Left Side Column (Filters & Contacts sidebar) -->
                <div class="space-y-6 lg:sticky lg:top-20">
                    <!-- Inventory Filters Card -->
                    <div
                        class="border border-neutral-200 bg-white rounded-card overflow-hidden shadow-sm p-6 space-y-4"
                    >
                        <h3
                            class="font-semibold text-neutral-800 text-sm uppercase tracking-wider flex items-center gap-2"
                        >
                            <iconify-icon icon="tabler:filter" class="text-brand-500"></iconify-icon>
                            <span>Filtrar Veículos</span>
                        </h3>

                        <!-- Search Input -->
                        <div class="space-y-1.5">
                            <label for="search" class="text-xs font-normal text-neutral-500">Buscar</label>
                            <div class="relative">
                                <input
                                    id="search"
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Ex: Hilux, flex, sedan..."
                                    class="w-full bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2 text-sm text-neutral-800 focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500 placeholder:text-neutral-400"
                                />
                                <iconify-icon
                                    icon="tabler:search"
                                    class="absolute right-3 top-2.5 text-neutral-400"
                                ></iconify-icon>
                            </div>
                        </div>

                        <!-- Transmission Dropdown -->
                        <div class="space-y-1.5" v-if="transmissionOptions.length > 0">
                            <label for="transmission" class="text-xs font-normal text-neutral-500">Câmbio</label>
                            <select
                                id="transmission"
                                v-model="selectedTransmission"
                                class="w-full bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2 text-sm text-neutral-800 focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500"
                            >
                                <option value="">Todos</option>
                                <option v-for="opt in transmissionOptions" :key="opt" :value="opt">
                                    {{ opt }}
                                </option>
                            </select>
                        </div>

                        <!-- Fuel Dropdown -->
                        <div class="space-y-1.5" v-if="fuelOptions.length > 0">
                            <label for="fuel" class="text-xs font-normal text-neutral-500">Combustível</label>
                            <select
                                id="fuel"
                                v-model="selectedFuel"
                                class="w-full bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-2 text-sm text-neutral-800 focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500"
                            >
                                <option value="">Todos</option>
                                <option v-for="opt in fuelOptions" :key="opt" :value="opt">
                                    {{ opt }}
                                </option>
                            </select>
                        </div>

                        <!-- Clear Filters Button -->
                        <button
                            v-if="searchQuery || selectedTransmission || selectedFuel"
                            @click="clearFilters"
                            class="w-full py-2 text-xs font-medium text-brand-600 hover:text-brand-700 bg-brand-50 hover:bg-brand-100 rounded-lg transition-colors flex items-center justify-center gap-1.5"
                        >
                            <iconify-icon icon="tabler:x" class="text-sm"></iconify-icon>
                            <span>Limpar Filtros</span>
                        </button>
                    </div>

                    <!-- Store Contacts Info Card -->
                    <StoreInfo :store="store" />
                </div>

                <!-- Right Side Column (Vehicles grid) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Toolbar -->
                    <div
                        class="p-4 bg-white border border-neutral-200 rounded-card shadow-sm flex items-center justify-between gap-4"
                    >
                        <!-- Counters -->
                        <div class="text-xs font-normal text-neutral-500 uppercase tracking-wider">
                            <span>{{ filteredAndSortedVehicles.length }}</span>
                            <span class="text-neutral-400 font-normal lowercase">
                                {{
                                    filteredAndSortedVehicles.length === 1
                                        ? 'veículo encontrado'
                                        : 'veículos encontrados'
                                }}
                            </span>
                        </div>

                        <!-- Sorting dropdown + Layout toggle -->
                        <div class="flex items-center gap-3">
                            <!-- Local Sort Selector -->
                            <select
                                v-model="sortBy"
                                class="bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-1.5 text-xs text-neutral-700 font-semibold focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500"
                            >
                                <option value="price_asc">Menor Preço</option>
                                <option value="price_desc">Maior Preço</option>
                                <option value="year_desc">Mais Novos</option>
                                <option value="mileage_asc">Menor Km</option>
                            </select>

                            <!-- Layout Toggles (Hidden on mobile) -->
                            <div class="hidden md:flex items-center bg-neutral-100 rounded-lg p-1">
                                <button
                                    @click="layoutView = 'grid'"
                                    :class="[
                                        'p-1.5 rounded-md transition-colors',
                                        layoutView === 'grid'
                                            ? 'bg-white shadow-sm text-brand-600'
                                            : 'text-neutral-500 hover:text-neutral-700',
                                    ]"
                                    title="Visualização em Grade"
                                >
                                    <iconify-icon icon="tabler:layout-grid" class="text-xl block"></iconify-icon>
                                </button>
                                <button
                                    @click="layoutView = 'list'"
                                    :class="[
                                        'p-1.5 rounded-md transition-colors',
                                        layoutView === 'list'
                                            ? 'bg-white shadow-sm text-brand-600'
                                            : 'text-neutral-500 hover:text-neutral-700',
                                    ]"
                                    title="Visualização em Lista"
                                >
                                    <iconify-icon icon="tabler:list" class="text-xl block"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Empty catalog state (No cars registered at all) -->
                    <div
                        v-if="storeVehicles.length === 0"
                        class="py-16 text-center border border-neutral-200 bg-white rounded-card p-6 space-y-3"
                    >
                        <iconify-icon icon="tabler:car-off" class="text-4xl text-neutral-300"></iconify-icon>
                        <h3 class="text-base font-semibold text-neutral-700">Nenhum veículo disponível</h3>
                        <p class="text-xs text-neutral-400 font-normal max-w-xs mx-auto">
                            Esta concessionária não possui carros cadastrados no portal neste momento.
                        </p>
                    </div>

                    <!-- Empty search results state (With active filters) -->
                    <div
                        v-else-if="filteredAndSortedVehicles.length === 0"
                        class="py-16 text-center border border-neutral-200 bg-white rounded-card p-6 space-y-3"
                    >
                        <iconify-icon icon="tabler:car-off" class="text-4xl text-neutral-300"></iconify-icon>
                        <h3 class="text-base font-semibold text-neutral-700">Nenhum veículo encontrado</h3>
                        <p class="text-xs text-neutral-400 font-normal max-w-xs mx-auto">
                            Não encontramos nenhum veículo com os filtros selecionados.
                        </p>
                        <button
                            @click="clearFilters"
                            class="px-4 py-2 text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 rounded-lg transition-colors inline-flex items-center gap-1.5"
                        >
                            <iconify-icon icon="tabler:x" class="text-sm"></iconify-icon>
                            <span>Limpar Filtros</span>
                        </button>
                    </div>

                    <!-- Inventory Grid -->
                    <div v-else class="space-y-6">
                        <VehicleGrid :vehicles="paginatedVehicles" :loading="false" :layout="layoutView" />

                        <!-- Navigation Pagination handle -->
                        <div v-if="totalPages > 1" class="pt-4 flex justify-center">
                            <UiPagination v-model="currentPage" :totalPages="totalPages" :disabled="false" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
