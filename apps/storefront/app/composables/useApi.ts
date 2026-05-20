export const useApi = () => {
    const getApiUrl = (path: string): string => {
        let apiBase = 'https://api.rederevenda.com';

        if (import.meta.client) {
            const hostname = window.location.hostname;
            if (hostname.includes('localhost') || hostname.includes('127.0.0.1')) {
                apiBase = 'http://localhost:7031';
            }
        } else {
            apiBase = process.env.API_URL || 'http://localhost:7031';
        }

        const cleanPath = path.startsWith('/') ? path : `/${path}`;
        return `${apiBase}${cleanPath}`;
    };

    return {
        getApiUrl,
    };
};
