import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';

export interface AuthUser {
    id: number;
    name: string;
    email: string;
    role: string;
}

const TOKEN_KEY = 'backoffice_auth_token';
const USER_KEY = 'backoffice_auth_user';

const user = ref<AuthUser | null>(null);
const token = ref<string | null>(null);

// Initialize from localStorage on client
if (import.meta.client) {
    const storedToken = localStorage.getItem(TOKEN_KEY);
    const storedUser = localStorage.getItem(USER_KEY);
    if (storedToken) token.value = storedToken;
    if (storedUser) {
        try { user.value = JSON.parse(storedUser); } catch {}
    }
}

export const useAuth = () => {
    const router = useRouter();
    const { getApiUrl } = useApi();

    const isAuthenticated = computed(() => !!token.value);

    const login = async (email: string, password: string): Promise<{ success: boolean; error?: string }> => {
        try {
            const res = await $fetch<any>(getApiUrl('/api/auth/login'), {
                method: 'POST',
                body: { email, password },
            });

            const receivedToken = res.token || res.access_token || res.data?.token;
            const receivedUser: AuthUser = {
                id: res.user?.id || res.data?.user?.id || 0,
                name: res.user?.name || res.data?.user?.name || email.split('@')[0],
                email: res.user?.email || res.data?.user?.email || email,
                role: res.user?.role || res.data?.user?.role || 'admin',
            };

            token.value = receivedToken;
            user.value = receivedUser;

            if (import.meta.client) {
                localStorage.setItem(TOKEN_KEY, receivedToken);
                localStorage.setItem(USER_KEY, JSON.stringify(receivedUser));
            }

            return { success: true };
        } catch (err: any) {
            const message = err?.data?.message || 'Credenciais inválidas. Verifique e-mail e senha.';
            return { success: false, error: message };
        }
    };

    const logout = () => {
        token.value = null;
        user.value = null;
        if (import.meta.client) {
            localStorage.removeItem(TOKEN_KEY);
            localStorage.removeItem(USER_KEY);
        }
        router.push('/login');
    };

    const getAuthHeaders = () => {
        if (!token.value) return {};
        return { Authorization: `Bearer ${token.value}` };
    };

    return {
        user,
        token,
        isAuthenticated,
        login,
        logout,
        getAuthHeaders,
    };
};
