// Storefront route middleware — protects /admin/* and /configuracoes/* routes
export default defineNuxtRouteMiddleware((to) => {
    // Skip server-side (localStorage unavailable)
    if (!import.meta.client) return;

    // Only protect admin/config areas
    const protectedPrefixes = ['/admin', '/configuracoes'];
    const isProtected = protectedPrefixes.some((prefix) => to.path.startsWith(prefix));

    if (!isProtected) return;

    // Allow the login page itself
    if (to.path === '/admin/login') return;

    const token = localStorage.getItem('storefront_auth_token');
    if (!token) {
        return navigateTo('/admin/login');
    }
});
