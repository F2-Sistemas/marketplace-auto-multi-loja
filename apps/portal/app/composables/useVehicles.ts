import { ref, computed, watch } from 'vue';

export interface Vehicle {
  id: number;
  title: string;
  brand: string;
  model: string;
  version: string;
  year_manufacture: number;
  year_model: number;
  mileage: number;
  transmission: string;
  price: number;
  slug: string;
  images: string[];
  store?: {
    name: string;
    slug: string;
    logo?: string;
  };
  featured?: boolean;
  fuel?: string;
  color?: string;
}

export interface Store {
  name: string;
  slug: string;
}

// Global shared states to preserve choices during routing
const search = ref('');
const selectedBrand = ref('');
const selectedStore = ref('');
const maxPrice = ref(250000);
const selectedTransmission = ref('');
const sortBy = ref('relevance');
const currentPage = ref(1);
const itemsPerPage = ref(6);

const brands = ['Toyota', 'Honda', 'Jeep', 'BMW', 'Volkswagen'];

export const useVehicles = () => {
  const { getApiUrl } = useApi();

  // Core Inventory Fetching with custom key to deduplicate calls and fix hydration mismatch
  const { data: apiResponse, pending, error } = useAsyncData<any>('vehicles-catalog-key', () => {
    const params: any = {};
    
    let queryText = search.value.trim();
    if (selectedBrand.value) {
      queryText = queryText ? `${queryText} ${selectedBrand.value}` : selectedBrand.value;
    }
    
    if (queryText) {
      params.q = queryText;
    }
    
    if (maxPrice.value && maxPrice.value < 250000) {
      params.price_max = maxPrice.value;
    }
    
    if (selectedTransmission.value) {
      params.transmission = selectedTransmission.value;
    }
    
    const headers: any = {};
    if (selectedStore.value) {
      headers['X-Store-Host'] = `${selectedStore.value}.rederevenda.com`;
    }

    return $fetch(getApiUrl('/api/vehicles'), {
      query: params,
      headers
    });
  }, {
    watch: [search, selectedBrand, selectedStore, selectedTransmission, maxPrice],
  });

  // Convert raw API schema into clean consistent client format matching the aligned Vehicle interface
  const allVehicles = computed<Vehicle[]>(() => {
    if (!apiResponse.value || !apiResponse.value.data) return [];
    return apiResponse.value.data.map((v: any) => ({
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

  // Sort and process catalog list
  const processedVehicles = computed<Vehicle[]>(() => {
    let list = [...allVehicles.value];
    
    if (sortBy.value === 'priceAsc') {
      list.sort((a, b) => a.price - b.price);
    } else if (sortBy.value === 'priceDesc') {
      list.sort((a, b) => b.price - a.price);
    } else if (sortBy.value === 'yearDesc') {
      list.sort((a, b) => (b.year_model || 0) - (a.year_model || 0));
    }
    
    return list;
  });

  const totalResults = computed(() => processedVehicles.value.length);
  const totalPages = computed(() => Math.ceil(totalResults.value / itemsPerPage.value) || 1);

  // Paginated active grid window
  const paginatedVehicles = computed<Vehicle[]>(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return processedVehicles.value.slice(start, end);
  });

  // Automatically reset page when query filters alter
  watch([search, selectedBrand, selectedStore, selectedTransmission, maxPrice], () => {
    currentPage.value = 1;
  });

  const resetFilters = () => {
    search.value = '';
    selectedBrand.value = '';
    selectedStore.value = '';
    maxPrice.value = 250000;
    selectedTransmission.value = '';
    sortBy.value = 'relevance';
    currentPage.value = 1;
  };

  const getVehicleById = (id: number | string): Vehicle | null => {
    return allVehicles.value.find(v => v.id === Number(id)) || null;
  };

  const sendLead = async (leadData: {
    name: string;
    email: string;
    phone: string;
    message: string;
    vehicle_id: number;
  }) => {
    return $fetch(getApiUrl('/api/leads'), {
      method: 'POST',
      body: leadData
    });
  };

  const stores = computed(() => {
    const map = new Map();
    allVehicles.value.forEach(v => {
      if (v.store?.slug && !map.has(v.store.slug)) {
        map.set(v.store.slug, {
          name: v.store.name,
          slug: v.store.slug
        });
      }
    });
    if (map.size === 0) {
      return [
        { name: 'AutoCar Natal', slug: 'autocar-natal' },
        { name: 'São Paulo Veículos', slug: 'sp-veiculos' },
        { name: 'Loja 01 Multimarcas', slug: 'loja01' },
        { name: 'Loja 02 Premium', slug: 'loja02' },
        { name: 'Tauro Motors', slug: 'tauro-motors' }
      ];
    }
    return Array.from(map.values());
  });

  return {
    search,
    selectedBrand,
    selectedStore,
    maxPrice,
    selectedTransmission,
    sortBy,
    currentPage,
    itemsPerPage,
    brands,
    stores,
    
    pending,
    error,
    totalResults,
    totalPages,
    vehicles: paginatedVehicles,
    allVehicles,
    
    resetFilters,
    getVehicleById,
    sendLead,
    postLead: sendLead
  };
};
