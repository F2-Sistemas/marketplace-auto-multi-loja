export const useApi = () => {
  const getApiUrl = (path: string): string => {
    const cleanPath = path.startsWith('/') ? path : `/${path}`;
    
    // Client-side requests go to the secure proxy.
    // Server-side (SSR) requests use the high-performance local network endpoint.
    const apiBase = import.meta.client
      ? 'https://api.rederevenda.com'
      : 'http://localhost:7031';
      
    return `${apiBase}${cleanPath}`;
  };

  return {
    getApiUrl
  };
};
