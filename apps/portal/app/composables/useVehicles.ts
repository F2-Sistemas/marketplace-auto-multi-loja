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

// Advanced semantic search state structure
export interface SearchPayload {
  search: {
    term: string;
  };
  location: {
    mode: 'city' | 'state' | 'radius' | 'all';
    city_id: number | null;
    state_id: number | null;
    radius_km: number | null;
  };
  filters: {
    brand_id: number | null;
    model_id: number | null;
    price: {
      min: number | null;
      max: number | null;
    };
    year: {
      min: number | null;
      max: number | null;
    };
    mileage: {
      max: number | null;
    };
    transmission: string[];
    fuel: string[];
    body_type: string[];
  };
  sort: {
    field: string;
    direction: 'asc' | 'desc';
  };
  pagination: {
    page: number;
    per_page: number;
  };
  store_id?: number | null;
}

const initialPayload = (): SearchPayload => ({
  search: {
    term: '',
  },
  location: {
    mode: 'all',
    city_id: null,
    state_id: null,
    radius_km: null,
  },
  filters: {
    brand_id: null,
    model_id: null,
    price: {
      min: null,
      max: null,
    },
    year: {
      min: null,
      max: null,
    },
    mileage: {
      max: null,
    },
    transmission: [],
    fuel: [],
    body_type: [],
  },
  sort: {
    field: 'created_at',
    direction: 'desc',
  },
  pagination: {
    page: 1,
    per_page: 6,
  }
});

const searchPayload = ref<SearchPayload>(initialPayload());
const selectedStore = ref('');

export const useVehicles = () => {
  const { getApiUrl } = useApi();

  // Backward compatibility properties using computed getters/setters mapping to searchPayload
  const search = computed({
    get: () => searchPayload.value.search.term,
    set: (val) => {
      searchPayload.value.search.term = val;
      searchPayload.value.pagination.page = 1;
    }
  });

  const selectedBrand = computed({
    get: () => searchPayload.value.filters.brand_id ? String(searchPayload.value.filters.brand_id) : '',
    set: (val) => {
      searchPayload.value.filters.brand_id = val ? Number(val) : null;
      searchPayload.value.filters.model_id = null; // Reset model on brand change
      searchPayload.value.pagination.page = 1;
    }
  });

  const selectedTransmission = computed({
    get: () => searchPayload.value.filters.transmission.length > 0 ? searchPayload.value.filters.transmission[0] : '',
    set: (val) => {
      searchPayload.value.filters.transmission = val ? [val] : [];
      searchPayload.value.pagination.page = 1;
    }
  });

  const maxPrice = computed({
    get: () => searchPayload.value.filters.price.max ?? 250000,
    set: (val) => {
      searchPayload.value.filters.price.max = val >= 250000 ? null : val;
      searchPayload.value.pagination.page = 1;
    }
  });

  const sortBy = computed({
    get: () => {
      const f = searchPayload.value.sort.field;
      const d = searchPayload.value.sort.direction;
      if (f === 'price' && d === 'asc') return 'price_asc';
      if (f === 'price' && d === 'desc') return 'price_desc';
      if (f === 'year' && d === 'desc') return 'year_desc';
      return 'newest';
    },
    set: (val) => {
      if (val === 'price_asc') {
        searchPayload.value.sort = { field: 'price', direction: 'asc' };
      } else if (val === 'price_desc') {
        searchPayload.value.sort = { field: 'price', direction: 'desc' };
      } else if (val === 'year_desc') {
        searchPayload.value.sort = { field: 'year', direction: 'desc' };
      } else {
        searchPayload.value.sort = { field: 'created_at', direction: 'desc' };
      }
      searchPayload.value.pagination.page = 1;
    }
  });

  const currentPage = computed({
    get: () => searchPayload.value.pagination.page,
    set: (val) => {
      searchPayload.value.pagination.page = val;
    }
  });

  const itemsPerPage = computed({
    get: () => searchPayload.value.pagination.per_page,
    set: (val) => {
      searchPayload.value.pagination.per_page = val;
      searchPayload.value.pagination.page = 1;
    }
  });

  // Fetch from the advanced search endpoint using POST and the semantic payload body
  const { data: apiResponse, pending, error, refresh } = useAsyncData<any>('vehicles-catalog-key', () => {
    const headers: any = {};
    if (selectedStore.value) {
      headers['X-Store-Host'] = `${selectedStore.value}.rederevenda.com`;
    }

    return $fetch(getApiUrl('/api/vehicles/search'), {
      method: 'POST',
      body: searchPayload.value,
      headers
    });
  }, {
    watch: [searchPayload, selectedStore],
    deep: true
  });

  // Client conversion mapper
  const vehicles = computed<Vehicle[]>(() => {
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

  // Server-driven pagination metadata
  const totalResults = computed(() => apiResponse.value?.total || 0);
  const totalPages = computed(() => apiResponse.value?.last_page || 1);

  const resetFilters = () => {
    searchPayload.value = initialPayload();
    selectedStore.value = '';
  };

  const getVehicleById = (id: number | string): Vehicle | null => {
    return vehicles.value.find(v => v.id === Number(id)) || null;
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

  // Helper dynamic catalog lookup API calls
  const fetchBrands = async (): Promise<any[]> => {
    return $fetch(getApiUrl('/api/brands'));
  };

  const fetchStates = async (): Promise<any[]> => {
    return $fetch(getApiUrl('/api/states'));
  };

  const fetchCities = async (): Promise<any[]> => {
    return $fetch(getApiUrl('/api/cities'));
  };

  const stores = computed(() => {
    const map = new Map();
    vehicles.value.forEach(v => {
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
    searchPayload,
    search,
    selectedBrand,
    selectedStore,
    maxPrice,
    selectedTransmission,
    sortBy,
    currentPage,
    itemsPerPage,
    stores,
    
    pending,
    error,
    totalResults,
    totalPages,
    vehicles,
    allVehicles: vehicles,
    
    resetFilters,
    getVehicleById,
    sendLead,
    postLead: sendLead,

    // Dropdown fetching
    fetchBrands,
    fetchStates,
    fetchCities,
  };
};
