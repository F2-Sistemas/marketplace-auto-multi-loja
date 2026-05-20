import { ref, computed } from 'vue';
import { useApi } from './useApi';
import type { Vehicle } from './useVehicles';

const favoriteVehicles = ref<Vehicle[]>([]);
const isLoaded = ref(false);

export const useFavorites = () => {
    const { getApiUrl } = useApi();

    const loadFromLocalStorage = () => {
        if (process.client) {
            try {
                const stored = localStorage.getItem('automarket_favorites');
                if (stored) {
                    favoriteVehicles.value = JSON.parse(stored);
                } else {
                    favoriteVehicles.value = [];
                }
            } catch (err) {
                console.error('Error reading favorites from localStorage:', err);
                favoriteVehicles.value = [];
            }
            isLoaded.value = true;
        }
    };

    const saveToLocalStorage = () => {
        if (process.client) {
            try {
                localStorage.setItem('automarket_favorites', JSON.stringify(favoriteVehicles.value));
            } catch (err) {
                console.error('Error saving favorites to localStorage:', err);
            }
        }
    };

    const fetchFavorites = async () => {
        // Try to fetch from the API first
        try {
            const response = await $fetch<any[]>(getApiUrl('/api/favorites'), {
                headers: {
                    'X-Test-User-Email': 'comprador@gmail.com',
                    Accept: 'application/json',
                },
            });
            if (response && Array.isArray(response)) {
                favoriteVehicles.value = response.map((v: any) => ({
                    id: v.id,
                    title: v.title || `${v.brand_name || ''} ${v.model_name || ''}`,
                    brand: v.brand_name || '',
                    model: v.model_name || '',
                    version: v.version || '',
                    year_manufacture: Number(v.year_manufacture) || 2020,
                    year_model: Number(v.year_model) || 2021,
                    mileage: Number(v.mileage) || 0,
                    transmission: v.transmission || '',
                    price: Number(v.price) || 0,
                    slug: v.slug || '',
                    images: [
                        v.main_image ||
                            'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80',
                    ],
                    fuel: v.fuel || '',
                    color: v.color || '',
                    city: v.city_name ? { name: v.city_name } : undefined,
                }));
                saveToLocalStorage();
                isLoaded.value = true;
                return;
            }
        } catch (err) {
            console.warn('Backend favorites API failed, falling back to local storage:', err);
        }
        // Fallback to local storage
        loadFromLocalStorage();
    };

    const isFavorited = (vehicleId: number) => {
        if (!isLoaded.value) {
            fetchFavorites();
        }
        return favoriteVehicles.value.some((v) => v.id === vehicleId);
    };

    const toggleFavorite = async (vehicle: Vehicle | number) => {
        if (!isLoaded.value) {
            await fetchFavorites();
        }

        const vehicleId = typeof vehicle === 'number' ? vehicle : vehicle.id;
        const index = favoriteVehicles.value.findIndex((v) => v.id === vehicleId);

        // Call API toggle in background
        try {
            await $fetch(getApiUrl(`/api/favorites/${vehicleId}/toggle`), {
                method: 'POST',
                headers: {
                    'X-Test-User-Email': 'comprador@gmail.com',
                    Accept: 'application/json',
                },
            });
        } catch (err) {
            console.error('Error toggling favorite on backend:', err);
        }

        if (index > -1) {
            favoriteVehicles.value.splice(index, 1);
        } else {
            if (typeof vehicle === 'object') {
                favoriteVehicles.value.push(vehicle);
            } else {
                favoriteVehicles.value.push({
                    id: vehicleId,
                    title: `Veículo #${vehicleId}`,
                    brand: '',
                    model: '',
                    version: '',
                    year_manufacture: 2024,
                    year_model: 2024,
                    mileage: 0,
                    transmission: '',
                    price: 0,
                    slug: '',
                    images: [],
                });
            }
        }
        saveToLocalStorage();
    };

    const favoriteCount = computed(() => {
        if (!isLoaded.value) {
            fetchFavorites();
        }
        return favoriteVehicles.value.length;
    });

    return {
        favoriteVehicles,
        favoriteCount,
        fetchFavorites,
        isFavorited,
        toggleFavorite,
    };
};
