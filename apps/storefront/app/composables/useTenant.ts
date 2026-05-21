import { computed } from 'vue';
import { useRequestHeaders, useFetch, useState } from '#app';

export type StoreLayoutStyle = 'showroom' | 'catalog';

export interface StoreDetails {
    name: string;
    slug: string;
    theme: string;
    primaryColor: string;
    accentColor: string;
    secondaryColor: string;
    fontFamily: string;
    layoutStyle: StoreLayoutStyle;
    city: string;
    state: string;
    phone: string;
    address: string;
    whatsapp: string;
    tagline: string;
    logoIcon: string;
    logoUrl: string;
    accentGradient: string;
    buttonClass: string;
}

export interface Vehicle {
    id: number;
    store_slug: string;
    brand: string;
    model: string;
    version: string;
    price: number;
    year: string;
    mileage: number;
    transmission: string;
    fuel: string;
    image: string;
}

export const normalizeLayoutStyle = (value?: string | null): StoreLayoutStyle => {
    if (value === 'catalog' || value === 'grid' || value === 'list') {
        return 'catalog';
    }

    return 'showroom';
};

const tenants: Record<string, StoreDetails> = {
    'natal-motors': {
        name: 'Natal Motors',
        slug: 'natal-motors',
        theme: 'amber',
        primaryColor: 'from-amber-500 to-orange-600',
        accentColor: 'text-amber-400',
        city: 'Natal',
        state: 'RN',
        phone: '(84) 3222-1000',
        address: 'Av. Prudente de Morais, 4000 - Lagoa Nova',
        whatsapp: '5584999998888',
        tagline: 'Líder em Seminovos e Premium no Rio Grande do Norte',
        logoIcon: 'tabler:sun',
        accentGradient: 'from-amber-400 to-orange-500',
        buttonClass:
            'border border-amber-500 text-amber-400 bg-amber-500/5 hover:bg-amber-500 hover:text-slate-950 transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-amber-500/50 shadow-sm shadow-amber-500/10',
    },
    'sp-veiculos': {
        name: 'SP Veículos',
        slug: 'sp-veiculos',
        theme: 'red',
        primaryColor: 'from-red-600 to-rose-700',
        accentColor: 'text-red-400',
        city: 'São Paulo',
        state: 'SP',
        phone: '(11) 5500-2020',
        address: 'Av. Europa, 1500 - Jardim Europa',
        whatsapp: '5511988887777',
        tagline: 'Os esportivos e importados mais exclusivos de São Paulo',
        logoIcon: 'tabler:building-skyscraper',
        accentGradient: 'from-red-500 to-rose-600',
        buttonClass:
            'border border-red-500 text-red-400 bg-red-500/5 hover:bg-red-500 hover:text-white transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-red-500/50 shadow-sm shadow-red-500/10',
    },
    'euro-select': {
        name: 'Euro Select',
        slug: 'euro-select',
        theme: 'blue',
        primaryColor: 'from-blue-600 to-indigo-700',
        accentColor: 'text-blue-400',
        city: 'Curitiba',
        state: 'PR',
        phone: '(41) 3340-9000',
        address: 'Rua General Mário Tourinho, 2200 - Seminário',
        whatsapp: '5541977776666',
        tagline: 'Alta costura automotiva: Importados Selecionados',
        logoIcon: 'tabler:crown',
        accentGradient: 'from-blue-400 to-indigo-500',
        buttonClass:
            'border border-blue-500 text-blue-400 bg-blue-500/5 hover:bg-blue-500 hover:text-white transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500/50 shadow-sm shadow-blue-500/10',
    },
};

export function useTenant() {
    const currentTenantKey = useState<string>('tenantKey', () => 'natal-motors');
    const searchQuery = useState<string>('searchQuery', () => '');
    const selectedVehicle = useState<Vehicle | null>('selectedVehicle', () => null);
    const leadForm = useState('leadForm', () => ({ name: '', email: '', phone: '', message: '' }));
    const showLeadSuccess = useState('showLeadSuccess', () => false);

    const mapping: Record<string, string> = {
        'natal-motors': 'autocar-natal.app-loja.rederevenda.com',
        'sp-veiculos': 'sp-veiculos.app-loja.rederevenda.com',
        'euro-select': 'sp-veiculos.app-loja.rederevenda.com', // Map euro-select fallback
    };

    const hostHeader = computed(() => {
        const headers = useRequestHeaders(['host']);
        const reqHost = headers.host || '';
        const clientHost = typeof window !== 'undefined' ? window.location.host : '';
        const host = clientHost || reqHost || '';

        if (host === '') {
            return mapping[currentTenantKey.value] || 'autocar-natal.app-loja.rederevenda.com';
        }

        if (host.includes('localhost') || host.includes('127.0.0.1')) {
            return host;
        }

        if (host.endsWith('.localhost')) {
            return host;
        }

        if (
            host.includes('rederevenda.com') &&
            !['rederevenda.com', 'api.rederevenda.com', 'admin.rederevenda.com'].includes(host)
        ) {
            return host;
        }

        return mapping[currentTenantKey.value] || 'autocar-natal.app-loja.rederevenda.com';
    });

    const { getApiUrl } = useApi();

    const { data: tenantResponse } = useFetch<any>(
        computed(() => getApiUrl('/api/tenant')),
        {
            headers: computed(() => ({
                'X-Store-Host': hostHeader.value,
            })),
            watch: [hostHeader],
        }
    );

    const { data: vehiclesResponse } = useFetch<any>(
        computed(() => getApiUrl('/api/vehicles')),
        {
            query: computed(() => {
                const params: any = {};
                if (searchQuery.value) params.q = searchQuery.value;
                return params;
            }),
            headers: computed(() => ({
                'X-Store-Host': hostHeader.value,
            })),
            watch: [hostHeader, searchQuery],
        }
    );

    const currentStore = computed<StoreDetails>(() => {
        const t = tenantResponse.value;
        const fallback = tenants[currentTenantKey.value] || tenants['natal-motors'];

        if (!t || t.is_portal) return fallback;

        const accent = t.settings?.accent_color || '#e11d48';
        const secondaryColor = t.settings?.secondary_color || '#1e293b';
        const fontFamily = t.settings?.font_family || 'Inter';
        const layoutStyle = normalizeLayoutStyle(t.settings?.layout_style);
        let theme = 'red';
        let primaryColor = 'from-red-600 to-rose-700';
        let accentColor = 'text-rose-400';
        let accentGradient = 'from-rose-400 to-red-500';
        let buttonClass =
            'border border-rose-500 text-rose-400 bg-rose-500/5 hover:bg-rose-500 hover:text-white transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-rose-500/50 shadow-sm shadow-rose-500/10';

        if (
            accent.includes('#1d4ed8') ||
            accent.includes('#3b82f6') ||
            accent === 'blue' ||
            accent.includes('#0000ff')
        ) {
            theme = 'blue';
            primaryColor = 'from-blue-600 to-indigo-700';
            accentColor = 'text-blue-400';
            accentGradient = 'from-blue-400 to-indigo-500';
            buttonClass =
                'border border-blue-500 text-blue-400 bg-blue-500/5 hover:bg-blue-500 hover:text-white transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500/50 shadow-sm shadow-blue-500/10';
        } else if (
            accent.includes('#d97706') ||
            accent.includes('#f59e0b') ||
            accent === 'amber' ||
            accent.includes('#ff0000')
        ) {
            theme = 'amber';
            primaryColor = 'from-amber-500 to-orange-600';
            accentColor = 'text-amber-400';
            accentGradient = 'from-amber-400 to-orange-500';
            buttonClass =
                'border border-amber-500 text-amber-400 bg-amber-500/5 hover:bg-amber-500 hover:text-slate-950 transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-amber-500/50 shadow-sm shadow-amber-500/10';
        }

        return {
            name: t.name,
            slug: t.slug,
            theme: theme,
            primaryColor: primaryColor,
            accentColor: accentColor,
            secondaryColor: secondaryColor,
            fontFamily: fontFamily,
            layoutStyle: layoutStyle,
            city: fallback.city,
            state: fallback.state,
            phone: t.settings?.phone || fallback.phone,
            address: t.settings?.address || fallback.address,
            whatsapp: t.settings?.whatsapp_number || fallback.whatsapp,
            tagline: t.settings?.tagline || fallback.tagline,
            logoIcon: fallback.logoIcon,
            logoUrl: t.settings?.logo_url || '',
            accentGradient: accentGradient,
            buttonClass: buttonClass,
        };
    });

    const filteredVehicles = computed<Vehicle[]>(() => {
        if (!vehiclesResponse.value || !vehiclesResponse.value.data) return [];
        return vehiclesResponse.value.data.map((v: any) => {
            return {
                id: v.id,
                store_slug: v.store ? v.store.slug : '',
                brand: v.brand ? v.brand.name : '',
                model: v.model ? v.model.name : '',
                version: v.version,
                price: Number(v.price),
                year: `${v.year_manufacture}/${v.year_model}`,
                mileage: Number(v.mileage),
                transmission: v.transmission,
                fuel: v.fuel,
                image:
                    v.images && v.images.length > 0
                        ? v.images[0].image_url
                        : 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?q=80&w=800&auto=format&fit=crop',
            };
        });
    });

    const openVehicleDetails = (vehicle: Vehicle) => {
        selectedVehicle.value = vehicle;
        leadForm.value.message = `Olá! Gostaria de falar com o consultor de vendas da ${currentStore.value.name} sobre o ${vehicle.brand} ${vehicle.model} anunciado por R$ ${vehicle.price.toLocaleString('pt-BR')}.`;
        showLeadSuccess.value = false;
    };

    const closeDetails = () => {
        selectedVehicle.value = null;
    };

    const submitLead = async () => {
        if (!selectedVehicle.value) return;

        try {
            await $fetch(getApiUrl('/api/leads'), {
                method: 'POST',
                body: {
                    name: leadForm.value.name,
                    email: leadForm.value.email,
                    phone: leadForm.value.phone,
                    message: leadForm.value.message,
                    vehicle_id: selectedVehicle.value.id,
                },
            });

            showLeadSuccess.value = true;
            setTimeout(() => {
                showLeadSuccess.value = false;
                closeDetails();
            }, 2500);
        } catch (error) {
            console.error('Erro ao enviar proposta:', error);
            alert('Ocorreu um erro ao enviar a proposta. Por favor, tente novamente.');
        }
    };

    const formatPrice = (value: number) => {
        return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    };

    return {
        tenants,
        currentTenantKey,
        searchQuery,
        selectedVehicle,
        leadForm,
        showLeadSuccess,
        currentStore,
        filteredVehicles,
        openVehicleDetails,
        closeDetails,
        submitLead,
        formatPrice,
    };
}
