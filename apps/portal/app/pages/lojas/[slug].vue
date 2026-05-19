<script setup lang="ts">
import { computed } from 'vue';
import { useRoute, useFetch } from '#app';
import { useI18n } from '~/composables/useI18n';
import Breadcrumb from '~/components/layout/Breadcrumb.vue';
import StoreHero from '~/components/stores/StoreHero.vue';
import StoreInfo from '~/components/stores/StoreInfo.vue';
import VehicleCard from '~/components/vehicles/VehicleCard.vue';
import UiButton from '~/components/ui/UiButton.vue';

definePageMeta({
  layout: 'default'
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
    state: 'RN'
  };

  if (slug === 'autocar-natal') {
    details.description = 'Sua concessionária autorizada em Natal, RN. Ofertas exclusivas de veículos novos e seminovos multimarcas.';
    details.phone = '(84) 3222-3000';
    details.address = 'Av. Salgado Filho, 2200 - Lagoa Nova';
  } else if (slug === 'sp-veiculos') {
    details.description = 'Líder em vendas de seminovos importados e nacionais. Garantia e a melhor avaliação do seu usado na troca.';
    details.phone = '(11) 3888-4000';
    details.address = 'Av. Europa, 1500 - Jardim Europa';
    details.city = 'São Paulo';
    details.state = 'SP';
  } else if (slug === 'loja01') {
    details.description = 'Veículos com laudo cautelar aprovado e procedência garantida. Credenciada com as principais financeiras.';
    details.phone = '(84) 99999-9999';
    details.address = 'Av. Prudente de Morais, 3300 - Lagoa Nova';
  } else if (slug === 'loja02') {
    details.description = 'Exclusividade e sofisticação em marcas premium como BMW, Audi, Mercedes-Benz e Porsche.';
    details.phone = '(84) 98888-8888';
    details.address = 'Av. Hermes da Fonseca, 800 - Petrópolis';
  } else if (slug === 'tauro-motors') {
    details.description = 'Concessionária referência em picapes e utilitários esportivos (SUV). Venha fazer um test-drive!';
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
      images: v.images && v.images.length > 0 
        ? v.images.map((img: any) => img.image_url || img.path)
        : ['https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?q=80&w=800&auto=format&fit=crop'],
      store: v.store ? {
        name: v.store.name,
        slug: v.store.slug,
        logo: v.store.logo_url
      } : undefined,
      fuel: v.fuel || '',
      color: v.color || '',
      featured: Number(v.price) >= 120000
    }));
});
</script>

<template>
  <div class="container max-w-7xl mx-auto px-4 md:px-6 py-6 space-y-6">
    <!-- Pending indicator -->
    <div v-if="pending" class="py-20 flex flex-col items-center justify-center space-y-4">
      <iconify-icon icon="tabler:loader" class="text-4xl text-brand-500 animate-spin"></iconify-icon>
      <p class="text-sm font-semibold text-neutral-500">Buscando informações da loja...</p>
    </div>

    <!-- Error/NotFound -->
    <div v-else-if="error || !store" class="py-16 text-center max-w-md mx-auto space-y-4">
      <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto border border-red-100">
        <iconify-icon icon="tabler:alert-triangle" class="text-3xl"></iconify-icon>
      </div>
      <h2 class="text-xl font-extrabold text-neutral-800">Concessionária não encontrada</h2>
      <p class="text-sm text-neutral-500 font-medium">
        Não conseguimos localizar a loja solicitada no momento. Por favor, volte ao diretório de lojas parceiras.
      </p>
      <UiButton variant="primary" size="md" to="/lojas" class="font-bold">
        <span>Ver Todas as Lojas</span>
      </UiButton>
    </div>

    <!-- Live Store details and inventory catalog -->
    <div v-else class="space-y-6">
      <!-- Breadcrumbs -->
      <Breadcrumb
        :items="[
          { label: 'Nossas Lojas', to: '/lojas' },
          { label: store.name }
        ]"
      />

      <!-- Hero Banner Component -->
      <StoreHero :store="store" />

      <!-- Layout Split Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Left Side Column (Contacts sidebar) -->
        <div class="lg:sticky lg:top-20">
          <StoreInfo :store="store" />
        </div>

        <!-- Right Side Column (Vehicles grid) -->
        <div class="lg:col-span-2 space-y-6">
          <div class="space-y-2">
            <h2 class="text-xl font-black text-neutral-800 tracking-tight flex items-center gap-2">
              <iconify-icon icon="tabler:car" class="text-brand-500"></iconify-icon>
              <span>Veículos Disponíveis ({{ storeVehicles.length }})</span>
            </h2>
            <p class="text-xs font-bold text-neutral-400 uppercase tracking-wider">
              Explore o catálogo completo de ofertas exclusivas da concessionária
            </p>
          </div>

          <!-- Empty catalog state -->
          <div v-if="storeVehicles.length === 0" class="py-16 text-center border-2 border-dashed border-neutral-200 rounded-card p-6 space-y-3">
            <iconify-icon icon="tabler:car-off" class="text-4xl text-neutral-300"></iconify-icon>
            <h3 class="text-base font-extrabold text-neutral-700">Nenhum veículo disponível</h3>
            <p class="text-xs text-neutral-400 font-semibold max-w-xs mx-auto">
              Esta concessionária não possui carros cadastrados no portal neste momento.
            </p>
          </div>

          <!-- Inventory Grid -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="vehicle in storeVehicles" :key="vehicle.id">
              <VehicleCard :vehicle="vehicle" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
