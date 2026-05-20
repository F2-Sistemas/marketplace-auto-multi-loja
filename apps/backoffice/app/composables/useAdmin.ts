import { ref, computed } from 'vue';
import { useApi } from '~/composables/useApi';

export interface Store {
    id: number;
    name: string;
    host: string;
    plan: 'mensal' | 'semestral' | 'anual';
    status: 'ativo' | 'inativo';
    accentColor: string;
    created_at: string;
}

export const useAdmin = () => {
    const { getApiUrl } = useApi();

    // Fetch stores from API
    const {
        data: storesResponse,
        refresh: refreshStores,
        error: fetchError,
    } = useFetch<any>(() => getApiUrl('/api/admin/stores'));

    // Fetch admin stats
    const refreshQuery = ref(false);
    const {
        data: statsResponse,
        refresh: doRefreshStats,
        pending: loadingStats,
    } = useFetch<any>(() => getApiUrl('/api/admin/stats'), {
        query: { refresh: refreshQuery },
    });

    const refreshStats = async () => {
        refreshQuery.value = true;
        await doRefreshStats();
        refreshQuery.value = false;
    };

    const stores = computed<Store[]>(() => {
        if (!storesResponse.value) return [];

        const rawList = Array.isArray(storesResponse.value) ? storesResponse.value : storesResponse.value.data || [];

        return rawList.map((s: any) => {
            let accentName = 'indigo';
            const color = s.settings?.accent_color || '';
            if (color === '#f59e0b' || color === 'amber') accentName = 'amber';
            else if (color === '#e11d48' || color === 'red') accentName = 'red';
            else if (color === '#2563eb' || color === 'blue') accentName = 'blue';

            const domainObj = s.domains && s.domains.length > 0 ? s.domains.find((d: any) => d.is_primary) : null;
            const resolvedDomain = domainObj ? domainObj.domain : `${s.slug}.rederevenda.com`;

            return {
                id: s.id,
                name: s.name,
                host: resolvedDomain,
                plan: s.settings?.plan || 'mensal',
                status: s.status === 'active' ? 'ativo' : 'inativo',
                accentColor: accentName,
                created_at: s.created_at ? s.created_at.split(' ')[0].split('T')[0] : '2026-05-18',
            };
        });
    });

    // Wizard new store form state
    const newStore = ref({
        name: '',
        subdomain: '',
        plan: 'mensal' as 'mensal' | 'semestral' | 'anual',
        accentColor: 'indigo',
    });

    const showWizardSuccess = ref(false);
    const loading = ref(false);

    const handleCreateStore = async () => {
        loading.value = true;
        try {
            let hexColor = '#4f46e5'; // indigo
            if (newStore.value.accentColor === 'amber') hexColor = '#f59e0b';
            else if (newStore.value.accentColor === 'red') hexColor = '#e11d48';
            else if (newStore.value.accentColor === 'blue') hexColor = '#2563eb';

            await $fetch(getApiUrl('/api/admin/stores'), {
                method: 'POST',
                body: {
                    name: newStore.value.name,
                    slug: newStore.value.subdomain,
                    domain: `${newStore.value.subdomain}.rederevenda.com`,
                    settings: {
                        plan: newStore.value.plan,
                        accent_color: hexColor,
                        address: 'Av. das Nações, 1000 - Centro',
                        phone: '(84) 99999-8888',
                        whatsapp_number: '5584999998888',
                        tagline: 'Sua melhor escolha em seminovos de procedência!',
                    },
                },
            });

            showWizardSuccess.value = true;
            await refreshStores();

            // Reset form
            setTimeout(() => {
                showWizardSuccess.value = false;
                newStore.value = {
                    name: '',
                    subdomain: '',
                    plan: 'mensal',
                    accentColor: 'indigo',
                };
            }, 2000);
            return true;
        } catch (error) {
            console.error('Erro ao provisionar tenant:', error);
            alert(
                'Erro ao provisionar o tenant. Certifique-se de que a API está ativa e o subdomínio não está em uso.'
            );
            return false;
        } finally {
            loading.value = false;
        }
    };

    const toggleStoreStatus = async (store: Store) => {
        try {
            await $fetch(getApiUrl(`/api/admin/stores/${store.id}/toggle`), {
                method: 'POST',
            });
            await refreshStores();
        } catch (error) {
            console.error('Erro ao alternar status da loja:', error);
            alert('Erro ao alterar status da loja. Certifique-se de que a API está ativa.');
        }
    };

    const formatPlan = (value: string) => {
        const map: Record<string, string> = {
            mensal: 'Mensal - R$ 199/mês',
            semestral: 'Semestral - R$ 999/sem',
            anual: 'Anual - R$ 1.800/ano',
        };
        return map[value] || value;
    };

    return {
        stores,
        newStore,
        showWizardSuccess,
        loading,
        refreshStores,
        loadingStats,
        statsResponse,
        refreshStats,
        handleCreateStore,
        toggleStoreStatus,
        formatPlan,
    };
};
