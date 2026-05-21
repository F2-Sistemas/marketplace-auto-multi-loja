import { ref } from 'vue';
import { useApi } from './useApi';
import type { Vehicle } from './useVehicles';

export interface InterestListVehicle {
    id: number;
    title: string;
    slug: string;
    version?: string;
    price?: number;
    year_manufacture?: number;
    year_model?: number;
    mileage?: number;
    transmission?: string;
    fuel?: string;
    brand_name?: string;
    model_name?: string;
    main_image?: string;
}

export interface InterestList {
    id: number;
    name: string;
    slug: string;
    description?: string | null;
    is_public: boolean;
    public_url?: string | null;
    items_count: number;
    vehicles: InterestListVehicle[];
    created_at?: string;
    updated_at?: string;
}

const interestLists = ref<InterestList[]>([]);
const currentInterestList = ref<InterestList | null>(null);

export const useInterestLists = () => {
    const { getApiUrl } = useApi();

    const fetchInterestLists = async () => {
        const response = await $fetch<InterestList[]>(getApiUrl('/api/interest-lists'), {
            headers: {
                'X-Test-User-Email': 'comprador@gmail.com',
                Accept: 'application/json',
            },
        });

        interestLists.value = Array.isArray(response) ? response : [];

        return interestLists.value;
    };

    const createInterestList = async (payload: {
        name: string;
        description?: string;
        is_public?: boolean;
    }) => {
        const response = await $fetch<{ message: string; list_id: number }>(getApiUrl('/api/interest-lists'), {
            method: 'POST',
            headers: {
                'X-Test-User-Email': 'comprador@gmail.com',
                Accept: 'application/json',
            },
            body: payload,
        });

        await fetchInterestLists();

        return response;
    };

    const updateInterestList = async (
        listId: number,
        payload: {
            name: string;
            description?: string;
            is_public?: boolean;
        }
    ) => {
        const response = await $fetch<{ message: string }>(getApiUrl(`/api/interest-lists/${listId}`), {
            method: 'PUT',
            headers: {
                'X-Test-User-Email': 'comprador@gmail.com',
                Accept: 'application/json',
            },
            body: payload,
        });

        await fetchInterestLists();

        return response;
    };

    const addVehicleToList = async (listId: number, vehicleId: number) => {
        const response = await $fetch<{ message: string; added: boolean }>(
            getApiUrl(`/api/interest-lists/${listId}/vehicles`),
            {
                method: 'POST',
                headers: {
                    'X-Test-User-Email': 'comprador@gmail.com',
                    Accept: 'application/json',
                },
                body: {
                    vehicle_id: vehicleId,
                },
            }
        );

        await fetchInterestLists();

        return response;
    };

    const fetchPublicInterestList = async (slug: string) => {
        const response = await $fetch<{ list: InterestList; vehicles: InterestListVehicle[] }>(
            getApiUrl(`/api/interest-lists/${slug}`),
            {
                headers: {
                    Accept: 'application/json',
                },
            }
        );

        currentInterestList.value = response.list;

        return response;
    };

    const clearCurrentInterestList = () => {
        currentInterestList.value = null;
    };

    const findListById = (listId: number) => {
        return interestLists.value.find((list) => list.id === listId) || null;
    };

    const addVehicleToDefaultList = async (vehicle: Vehicle) => {
        if (interestLists.value.length === 0) {
            return null;
        }

        return addVehicleToList(interestLists.value[0].id, vehicle.id);
    };

    return {
        interestLists,
        currentInterestList,
        fetchInterestLists,
        fetchPublicInterestList,
        createInterestList,
        updateInterestList,
        addVehicleToList,
        addVehicleToDefaultList,
        findListById,
        clearCurrentInterestList,
    };
};
