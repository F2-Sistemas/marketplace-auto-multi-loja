export const useApi = () => {
    const getApiUrl = (path: string = '') => {
        // SSR context: use direct HTTP local port
        // Client-side browser: use secure HTTPS reverse proxy
        const apiBase = import.meta.client
            ? 'https://api.rederevenda.com'
            : process.env.API_URL || 'http://localhost:7031';

        const cleanPath = path.startsWith('/') ? path : `/${path}`;
        return `${apiBase}${cleanPath}`;
    };

    return {
        getApiUrl,
    };
};
