<script setup lang="ts">
import { computed } from 'vue';
import { useRoute, useFetch } from '#app';
import { useI18n } from '~/composables/useI18n';
import Breadcrumb from '~/components/layout/Breadcrumb.vue';
import VehicleDetailGallery from '~/components/vehicles/VehicleDetailGallery.vue';
import VehicleDetailSummary from '~/components/vehicles/VehicleDetailSummary.vue';
import VehicleSpecs from '~/components/vehicles/VehicleSpecs.vue';
import LeadForm from '~/components/leads/LeadForm.vue';
import UiCard from '~/components/ui/UiCard.vue';
import UiBadge from '~/components/ui/UiBadge.vue';
import UiButton from '~/components/ui/UiButton.vue';

definePageMeta({
  layout: 'default'
});

const route = useRoute();
const { t } = useI18n();

const { getApiUrl } = useApi();

// Dedicated fetch of all vehicles (24 total) to guarantee finding our target by slug
const { data: response, pending, error } = await useFetch<any>(getApiUrl('/api/vehicles?per_page=100'));

const vehicle = computed(() => {
  if (!response.value || !response.value.data) return null;
  const match = response.value.data.find((v: any) => v.slug === route.params.slug);
  if (!match) return null;

  return {
    id: match.id,
    title: match.title || `${match.brand?.name} ${match.model?.name}`,
    store_name: match.store ? match.store.name : 'AutoHub',
    store_slug: match.store ? match.store.slug : '',
    store_phone: '84999999999', // Fallback standard whatsapp number for seeded stores
    brand: match.brand ? match.brand.name : '',
    model: match.model ? match.model.name : '',
    version: match.version || 'Completo',
    price: Number(match.price) || 0,
    year_manufacture: Number(match.year_manufacture) || 2020,
    year_model: Number(match.year_model) || 2021,
    mileage: Number(match.mileage) || 0,
    transmission: match.transmission || 'Automatico',
    fuel: match.fuel || 'Flex',
    color: match.color || 'Prata',
    description: match.description || 'Veículo em perfeito estado de conservação, revisado e com garantia de procedência.',
    images: match.images && match.images.length > 0
      ? match.images.map((i: any) => i.image_url || i.path)
      : ['https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=1200&q=80'],
    features: match.features ? match.features.map((f: any) => f.name) : [
      'Ar Condicionado', 'Direção Hidráulica', 'Vidros Elétricos', 'Travas Elétricas', 'Freio ABS', 'Airbag'
    ]
  };
});
</script>

<template>
  <div class="container max-w-7xl mx-auto px-4 md:px-6 py-6 space-y-6">
    <!-- Pending loader view -->
    <div v-if="pending" class="py-20 flex flex-col items-center justify-center space-y-4">
      <iconify-icon icon="tabler:loader" class="text-4xl text-brand-500 animate-spin"></iconify-icon>
      <p class="text-sm font-semibold text-neutral-500">Carregando detalhes do veículo...</p>
    </div>

    <!-- Error/NotFound handling -->
    <div v-else-if="error || !vehicle" class="py-16 text-center max-w-md mx-auto space-y-4">
      <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto border border-red-100">
        <iconify-icon icon="tabler:alert-triangle" class="text-3xl"></iconify-icon>
      </div>
      <h2 class="text-xl font-extrabold text-neutral-800">Veículo não encontrado</h2>
      <p class="text-sm text-neutral-500 font-medium">
        Não conseguimos localizar o veículo solicitado. Ele pode ter sido vendido ou removido do catálogo.
      </p>
      <UiButton variant="primary" size="md" to="/veiculos" class="font-bold">
        <span>Voltar ao Catálogo</span>
      </UiButton>
    </div>

    <!-- Live Vehicle detail page contents -->
    <div v-else class="space-y-6">
      <!-- Breadcrumbs -->
      <Breadcrumb
        :items="[
          { label: t('search.catalogTitle'), to: '/veiculos' },
          { label: `${vehicle.brand} ${vehicle.model}` }
        ]"
      />

      <!-- Content Split Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Left Side Column (Gallery + Specs + Features) -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Gallery -->
          <VehicleDetailGallery :images="vehicle.images" :title="vehicle.title" />

          <!-- Details & Highlights box -->
          <div class="lg:hidden">
            <VehicleDetailSummary :vehicle="vehicle" />
          </div>

          <!-- Description Box -->
          <UiCard class="p-6 space-y-4">
            <h3 class="font-black text-neutral-800 text-lg border-b border-neutral-100 pb-3 flex items-center gap-2">
              <iconify-icon icon="tabler:file-description" class="text-brand-500"></iconify-icon>
              <span>Descrição do Veículo</span>
            </h3>
            <p class="text-sm font-semibold text-neutral-600 leading-relaxed whitespace-pre-line">
              {{ vehicle.description }}
            </p>
          </UiCard>

          <!-- Specifications Sheet -->
          <UiCard class="p-6 space-y-4">
            <h3 class="font-black text-neutral-800 text-lg border-b border-neutral-100 pb-3 flex items-center gap-2">
              <iconify-icon icon="tabler:info-circle" class="text-brand-500"></iconify-icon>
              <span>Ficha Técnica</span>
            </h3>
            <VehicleSpecs :vehicle="vehicle" />
          </UiCard>

          <!-- Features Tags -->
          <UiCard class="p-6 space-y-4">
            <h3 class="font-black text-neutral-800 text-lg border-b border-neutral-100 pb-3 flex items-center gap-2">
              <iconify-icon icon="tabler:list-details" class="text-brand-500"></iconify-icon>
              <span>Itens de Série e Opcionais</span>
            </h3>
            <div class="flex flex-wrap gap-2.5">
              <div
                v-for="feat in vehicle.features"
                :key="feat"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-neutral-50 border border-neutral-200 text-xs font-bold text-neutral-600 rounded-full"
              >
                <iconify-icon icon="tabler:circle-check" class="text-brand-500 text-sm"></iconify-icon>
                <span>{{ feat }}</span>
              </div>
            </div>
          </UiCard>
        </div>

        <!-- Right Side Column (Sticky Pricing Card + LeadForm) -->
        <div class="space-y-6 lg:sticky lg:top-20">
          <div class="hidden lg:block">
            <VehicleDetailSummary :vehicle="vehicle" />
          </div>

          <!-- Store partner details card -->
          <UiCard class="p-5 border border-brand-100 bg-brand-50/20 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center border border-brand-200">
                <iconify-icon icon="tabler:building-store" class="text-xl"></iconify-icon>
              </div>
              <div>
                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Anunciado por</span>
                <span class="text-sm font-extrabold text-neutral-800">{{ vehicle.store_name }}</span>
              </div>
            </div>
            <UiButton
              variant="outline"
              size="sm"
              :to="`/lojas/${vehicle.store_slug}`"
              class="font-bold border-brand-200 text-brand-700 hover:bg-brand-50"
            >
              <span>Ver Loja</span>
            </UiButton>
          </UiCard>

          <!-- WhatsApp lead form -->
          <LeadForm
            :vehicleId="vehicle.id"
            :vehicleTitle="vehicle.title"
            :storeName="vehicle.store_name"
            :storePhone="vehicle.store_phone"
          />
        </div>
      </div>
    </div>
  </div>
</template>
